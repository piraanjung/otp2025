<?php

namespace App\Http\Controllers\KeptKaya;

use App\Http\Controllers\Controller;
use App\Models\KeptKaya\KpPurchaseDetail;
use App\Models\KeptKaya\KpPurchaseTransaction;
use App\Models\KeptKaya\KpTbankItems;
use App\Models\KeptKaya\KpTbankItemsPriceAndPoint;
use App\Models\KeptKaya\KpTbankUnits;
use App\Models\KeptKaya\UserWastePreference;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class KeptKayaPurchaseController extends Controller
{
    /**
     * Show a list of users to select for a new purchase transaction.
     * แสดงหน้ารายการผู้ใช้งานเพื่อเลือกทำธุรกรรม
     * @return \Illuminate\View\View
     */
    public function select_user(Request $request)
    {
        $request->session()->remove('purchase_user_id');
        $request->session()->remove('purchase_cart');

        $query = User::whereHas('wastePreference', function ($query) {
            $query->where('is_waste_bank', true);
        });
        if ($request->filled('name_search')) {
            $nameSearch = $request->input('name_search');
            $query->where(function ($q) use ($nameSearch) {
                $q->where('firstname', 'like', '%' . $nameSearch . '%')
                    ->orWhere('lastname', 'like', '%' . $nameSearch . '%');
            });
        }

        if ($request->filled('username_search')) {
            $usernameSearch = $request->input('username_search');
            $query->with('wastePreference')
                ->whereHas('wastePreference', function($q) use ($usernameSearch){
                     $q->select('*')->where('id' ,$usernameSearch);
                });
        }

        $keptKayaMembers = $query->orderBy('firstname')
            ->orderBy('lastname')
            ->get();

        // Load today's purchase transactions for all members
        $today = Carbon::now()->toDateString();
        $keptKayaMembers->load(['purchaseTransactions' => function ($q) use ($today) {
            $q->whereDate('transaction_date', $today);
        }]);

        return view('keptkaya.purchase.select_user', compact('keptKayaMembers'));
    }
    /**
     * Redirect to the purchase form for the selected user.
     * เปลี่ยนเส้นทางไปยังหน้าฟอร์มรับซื้อสำหรับผู้ใช้ที่เลือก
     * @param \App\Models\User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function startPurchase(UserWastePreference $user)
    {
        // This is just a redirect to the next step.
        // In a real application, you might pass user data or a transaction ID.
        return redirect()->route('keptkayas.purchase.form', $user->id);
    }
    public function showCart()
    {
        $cart = Session::get('purchase_cart', []);
        $userId = Session::get('purchase_user_id');
        $user = null;
        if ($userId) {
            $user = User::find($userId);
        } else {
            return 'ss';
        }

        return view('keptkaya.purchase.cart', compact('cart', 'user'));
    }

    /**
     * Save the purchase cart as a new transaction in the database.
     * บันทึกรายการในรถเข็นเป็นธุรกรรมใหม่
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function saveTransaction(Request $request)
    {
        // The cart items are stored in the session, so we don't need to validate them here.
        // We only validate the user and total amounts if needed.
        $cart = Session::get('purchase_cart', []);
        $userId = Session::get('purchase_user_id');

        if (empty($cart) || !$userId) {
            return redirect()->route('keptkayas.purchase.select_user')->with('error', 'ไม่พบรายการในรถเข็นหรือผู้ใช้งาน กรุณาเริ่มทำธุรกรรมใหม่');
        }

        $user = User::find($userId);
        if (!$user) {
            return redirect()->route('keptkayas.purchase.select_user')->with('error', 'ผู้ใช้งานไม่ถูกต้อง กรุณาเริ่มทำธุรกรรมใหม่');
        }

        $totalWeight = array_sum(array_column($cart, 'amount_in_units')); // Assuming 'weight' is stored in 'amount_in_units'
        $totalAmount = array_sum(array_column($cart, 'amount'));
        $totalPoints = array_sum(array_column($cart, 'points'));
        $recorderId = Auth::id(); // ผู้บันทึกคือเจ้าหน้าที่ที่ล็อกอินอยู่

        DB::beginTransaction();
        try {
            // 1. Create the main purchase transaction
            $userWastePref = UserWastePreference::where('user_id', $userId)->get()->first();
            $transaction = KpPurchaseTransaction::create([
                'kp_u_trans_no' => 'T-' . Carbon::now()->format('YmdHis') . Str::random(4), // Example transaction number
                'kp_user_w_pref_id_fk' => $userWastePref->id,
                'transaction_date' => Carbon::now()->toDateString(),
                'total_weight' => $totalWeight,
                'total_amount' => $totalAmount,
                'total_points' => $totalPoints,
                'recorder_id' => $recorderId,
            ]);

            // 2. Create the purchase details for each item in the cart
            foreach ($cart as $item) {
                KpPurchaseDetail::create([
                    'kp_purchase_trans_id' => $transaction->id,
                    'kp_recycle_item_id' => $item['kp_tbank_item_id'],
                    'kp_tbank_items_pricepoint_id' => $item['kp_tbank_items_pricepoint_id'],
                    'amount_in_units' => $item['amount_in_units'],
                    'price_per_unit' => $item['price_per_unit'],
                    'amount' => $item['amount'],
                    'points' => $item['points'],
                    'unit_type' => $item['unit_name'], // Assuming unit_name is passed
                    'recorder_id' => $recorderId
                ]);
            }

            // 3. Clear the cart session after a successful transaction
            Session::forget('purchase_cart');
            Session::forget('purchase_user_id');

            DB::commit();

            return redirect()->route('keptkayas.purchase.receipt', $transaction->id);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'เกิดข้อผิดพลาดในการบันทึกธุรกรรม: ' . $e->getMessage());
        }
    }

    public function showReceipt(KpPurchaseTransaction $transaction)
    {
        // Load relationships needed for the receipt
        $transaction->load(['user_waste_pref.user', 'details.item', 'details.pricePoint.kp_units_info']);

        return view('keptkaya.purchase.receipt', compact('transaction'));
    }

    public function showPurchaseForm(Request $request, UserWastePreference $user)
    {
        $user = User::find($user->user_id);
        $request->session()->put('purchase_user_id', $user->id);
        // ตรวจสอบว่าผู้ใช้งานที่เลือกเป็นสมาชิกธนาคารขยะหรือไม่
        if (!$user->wastePreference || !$user->wastePreference->is_waste_bank) {
            return redirect()->route('keptkayas.purchase.select_user')->with('error', 'ผู้ใช้งานนี้ไม่ได้เป็นสมาชิกธนาคารขยะ');
        }

        // ดึงรายการขยะทั้งหมด และโหลดราคาที่ Active
        $recycleItems = KpTbankItems::with(['activePrices.kp_units_info'])
            ->whereHas('activePrices.kp_units_info')
            ->get();

        // ดึงข้อมูลหน่วยนับทั้งหมด (ถ้าต้องการใช้ใน dropdown)
        $allUnits = KpTbankUnits::all();

        return view('keptkaya.purchase.purchase_form', compact('user', 'recycleItems', 'allUnits'));
    }



    /**
     * Remove an item from the purchase cart.
     * ลบรายการออกจากรถเข็น
     * @param Request $request
     * @param int $index
     * @return \Illuminate\Http\RedirectResponse
     */
    public function removeFromCart(Request $request, $index)
    {
        $cart = Session::get('purchase_cart', []);
        if (isset($cart[$index])) {
            unset($cart[$index]);
            Session::put('purchase_cart', array_values($cart)); // Re-index the array
            return back()->with('success', 'ลบรายการขยะออกจากรถเข็นแล้ว');
        }

        return back()->with('error', 'ไม่พบรายการที่ต้องการลบ');
    }

    /**
     * Clear the entire cart.
     * ล้างรถเข็นทั้งหมด
     * @return \Illuminate\Http\RedirectResponse
     */
    public function clearCart()
    {
        Session::forget('purchase_cart');
        Session::forget('purchase_user_id');
        return redirect()->route('keptkayas.purchase.select_user');
    }

    public function addToCart(Request $request)
    {
        // Session::forget('purchase_cart');
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'kp_tbank_item_id' => 'required|exists:kp_tbank_items,id',
            'amount_in_units' => 'required|numeric|min:0.01',
            'kp_units_idfk' => 'required|exists:kp_tbank_items_units,id',
        ]);


        // Find the item and price to add to cart
        $item = KpTbankItems::find($validated['kp_tbank_item_id']);
        $unit = KpTbankUnits::find($validated['kp_units_idfk']);

        // Find the active price for this item and unit
        $priceConfig = KpTbankItemsPriceAndPoint::where('kp_items_idfk', $item->id)
            ->where('kp_units_idfk', $unit->id)
            ->where('status', 'active')
            ->where('deleted', '0')
            ->first();

        if (!$priceConfig) {
            return back()->with('error', 'ไม่พบราคารับซื้อที่ใช้งานอยู่สำหรับขยะและหน่วยนับนี้')->withInput();
        }

        // Calculate amount and points
        $amount = $validated['amount_in_units'] * $priceConfig->price_for_member;
        $points = $validated['amount_in_units'] * $priceConfig->point;

        $cartItem = [
            'kp_tbank_item_id' => $item->id,
            'item_name' => $item->kp_itemsname,
            'kp_units_idfk' => $unit->id,
            'unit_name' => $unit->unitname,
            'amount_in_units' => $validated['amount_in_units'],
            'price_per_unit' => $priceConfig->price_for_member,
            'amount' => $amount,
            'points' => $points,
            'kp_tbank_items_pricepoint_id' => $priceConfig->id,
        ];

        // Add to session cart
        Session::push('purchase_cart', $cartItem);

        return back()->with('success', 'เพิ่มรายการขยะลงในรถเข็นแล้ว');
    }

    public function showPurchaseHistory(User $user)
    {
        // Load purchase transactions for the user
        $user->load(['purchaseTransactions.details.item', 'purchaseTransactions.details.pricePoint.kp_units_info']);

        return view('keptkaya.purchase.history', compact('user'));
    }
}
