<?php

namespace App\Http\Controllers\KeptKaya;

use App\Http\Controllers\Controller;
use App\Models\Admin\Organization;
use App\Models\KeptKaya\KPAccounts;
use App\Models\KeptKaya\KpPurchaseRoute;
use App\Models\KeptKaya\KpPurchaseTransactionDetail;
use App\Models\KeptKaya\KpPurchaseTransaction;
use App\Models\KeptKaya\KpTbankItems;
use App\Models\KeptKaya\KpTbankItemsGroups;
use App\Models\KeptKaya\KpTbankItemsPriceAndPoint;
use App\Models\KeptKaya\KpTbankUnits;
use App\Models\KeptKaya\KpUserWastePreference;
use App\Models\KeptKaya\Machine;
use App\Models\KPBankAccount;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class KpPurchaseController extends Controller
{
    public function select_user(Request $request)
{
    // ล้างค่า Session เก่าของ Cart/User
    $request->session()->forget(['purchase_user_id', 'purchase_cart']);
    
    $today = Carbon::now()->toDateString();
    $orgId = Auth::user()->org_id_fk;

    // คำค้นหาหลัก (รับได้ทั้งจาก keyword_input, name_search หรือ username_search)
    $searchKey = trim($request->input('keyword', $request->input('name_search', $request->input('username_search'))));

    $query = User::query()
        ->select(['id', 'org_id_fk', 'username', 'firstname', 'lastname', 'phone', 'id_card', 'zone_id', 'image'])
        ->where('org_id_fk', $orgId)
        // ต้องมีบัญชีธนาคารขยะรีไซเคิลที่สถานะ active เท่านั้น
        ->whereHas('recycleBankAccount', function ($q) {
            $q->where('status', 'active');
        })
        ->with([
            'recycleBankAccount:id,user_id,account_no,balance,points,status',
            'user_zone:id,zonename',
            'kpUserPreference.purchaseTransactions' => function ($q) use ($today) {
                $q->whereDate('transaction_date', $today);
            }
        ]);

    // 1. เงื่อนไขจำกัดตามเขตรับซื้อที่เลือกไว้ใน Session (ถ้าไม่ได้เปิดสวิตช์ค้นหาทุกเขต)
    if (session()->has('purchase_route_id') && !$request->boolean('search_all_zones')) {
        $routeId = session('purchase_route_id');
        $zoneIds = \App\Models\KeptKaya\KpPurchaseRouteZone::where('kp_purchase_route_id', $routeId)
            ->pluck('zone_id');

        $query->whereIn('zone_id', $zoneIds);
    }

    // 2. เงื่อนไขค้นหา Unified (สแกน QR Code / พิมพ์ค้นหา)
    if (!empty($searchKey)) {
        $query->where(function ($q) use ($searchKey) {
            $q->where('id', $searchKey)
              ->orWhere('username', $searchKey)
              ->orWhere('phone', $searchKey)
              ->orWhere('id_card', $searchKey)
              ->orWhere('firstname', 'like', "%{$searchKey}%")
              ->orWhere('lastname', 'like', "%{$searchKey}%");
        });
    }

    $keptKayaMembers = $query->orderBy('firstname')
        ->orderBy('lastname')
        ->get();

    // กรณีสแกน QR Code หรือพิมพ์คำค้นหาแล้วเจอสมาชิกตรงเป๊ะเพียง 1 คน ให้เข้าสู่หน้าฟอร์มรับซื้อทันที
    if (!empty($searchKey) && $keptKayaMembers->count() === 1) {
        $user = $keptKayaMembers->first();
        $pref = $user->kpUserPreference ?? $user->wastePreference;
        
        if ($pref) {
            return redirect()->route('keptkayas.purchase.start_purchase', $pref->id);
        }
    }

    return view('keptkayas.purchase.select_user', compact('keptKayaMembers'));
}

    /**
     * Summary of startPurchase
     * @param mixed $user_waste_pref_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function startPurchase($user_waste_pref_id)
    {
        $userWastePref = KpUserWastePreference::find($user_waste_pref_id);
        // This is just a redirect to the next step.
        // In a real application, you might pass user data or a transaction ID.
        return redirect()->route('keptkayas.purchase.form', $userWastePref->user_id);
    }
    public function showCart()
    {
        $cart = Session::get('purchase_cart', []);
        $userId = Session::get('purchase_user_id');
        // $user = null;
        // if ($userId) {
        //     $user = User::find($userId);
        // } else {
        //     return 'ss';
        // }
        if (!$userId) {
            // ยังไม่ได้เลือกผู้ขาย (เปิดหน้านี้ตรง ๆ) ให้กลับไปเลือกก่อน แทนที่จะเกิด error 500
            return redirect()->route('keptkayas.purchase.select_user')
                ->with('warning', 'กรุณาเลือกผู้ขายก่อนเปิดตะกร้า');
        }
        $seller = User::where('id', $userId)
            ->with('wastePreference')->get()->first();
        $user = User::find(Auth::id());
        // return $user;
        return view('keptkayas.purchase.cart', compact('cart', 'user', 'seller'));
    }




    public function saveTransaction(Request $request)
    {
        $cart   = Session::get('purchase_cart', []);
        $userId = Session::get('purchase_user_id');

        if (empty($cart) || !$userId) {
            return redirect()->route('keptkayas.purchase.select_user')->with('error', 'ไม่พบรายการในรถเข็น');
        }

        return DB::transaction(function () use ($request, $cart, $userId) {
            $userWastePref = KpUserWastePreference::where('user_id', $userId)->first();
            $recorder = Auth::user();
            // 1. คำนวณยอดรวมทั้งหมดเตรียมไว้ก่อน
            $totalWeight = array_sum(array_column($cart, 'amount_in_units'));
            $totalAmount = array_sum(array_column($cart, 'amount'));
            $totalPoints = array_sum(array_column($cart, 'points'));
            $isCashBack  = $request->has('cash_back') ? 1 : 0;

            // 2. เจนเลขที่ธุรกรรม
            $transNo = 'T-' . Carbon::now()->format('ymdHis') . str_pad($userWastePref->id, 4, '0', STR_PAD_LEFT) . strtoupper(Str::random(3));

            // 3. บันทึก Header
            $transaction = KpPurchaseTransaction::create([
                'kp_u_trans_no'         => $transNo,
                'org_id_fk'             => $recorder->org_id_fk, // เพิ่ม org_id ให้เรียบร้อย
                'kp_user_w_pref_id_fk'  => $userWastePref->id,
                'transaction_date'      => Carbon::now()->toDateString(),
                'total_weight'          => $totalWeight,
                'total_amount'          => $totalAmount,
                'total_points'          => $totalPoints,
                'recorder_id'           => $recorder->id,
                'status'                => 1,
                'cash_back'             => $isCashBack
            ]);

            $carbonSavedTotal = 0;

            // 4. บันทึก Detail
            foreach ($cart as $item) {
                $itemModel = KpTbankItems::with('emissionFactor')->find($item['kp_tbank_item_id']);

                // เช็คชื่อคอลัมน์ EF ให้ชัวร์ (ef_value หรือ carbon_value)
                $ef = $itemModel->emissionFactor->ef_value ?? 0;
                $carbonSaved = $item['amount_in_units'] * $ef;
                $carbonSavedTotal += $carbonSaved;

                KpPurchaseTransactionDetail::create([
                    'kp_purchase_trans_id' => $transaction->id,
                    'org_id_fk'            => $recorder->org_id_fk,
                    'kp_u_trans_no'        => $transaction->kp_u_trans_no,
                    'kp_recycle_item_id'   => $item['kp_tbank_item_id'],
                    'kp_units_idfk'        => $item['kp_units_idfk'],
                    'amount_in_units'      => $item['amount_in_units'],
                    'price_per_unit'       => $item['price_per_unit'],
                    'amount'               => $item['amount'],
                    'points'               => $item['points'],
                    'carbon_saved'         => $carbonSaved,
                    'recorder_id'          => $recorder->id
                ]);
            }

            // 5. อัปเดต Carbon รวม
            $transaction->update(['total_carbon_saved' => $carbonSavedTotal]);

            // 6. อัปเดตสมุดบัญชีธนาคารขยะ (RecycleBankAccount)
            $recycleAcc = KpBankAccount::firstOrCreate(
                ['user_id' => $userId],
                [
                    'account_no' => 'ACC-' . str_pad($userId, 6, '0', STR_PAD_LEFT),
                    'balance'    => 0,
                    'points'     => 0,
                    'status'     => 'active'
                ]
            );

            $recycleAcc->increment('points', $totalPoints);
            if ($isCashBack == 0) {
                $recycleAcc->increment('balance', $totalAmount);
            }

            Session::forget(['purchase_cart', 'purchase_user_id']);

            return redirect()->route('keptkayas.purchase.receipt', $transaction->id)
                ->with('success', 'บันทึกสำเร็จ! คุณช่วยลดคาร์บอนได้ ' . number_format($carbonSavedTotal, 4) . ' kgCO2e');
        });
    }
    public function connect_bluethooth()
    {
        $transaction = KpPurchaseTransaction::where('id', 6)
            ->with('userWastePreference.user', 'details.item', 'details.pricePoint.kp_units_info')
            ->get()->first();
        $orgInfos = Organization::getOrgName(Auth::user()->org_id_fk);

        return view('keptkayas.purchase.connect_bluethooth', compact('transaction', 'orgInfos'));
    }

    /**
     * Summary of showReceipt
     * @param mixed $transaction_id
     * @return \Illuminate\Contracts\View\View
     */
    public function showReceipt($transaction_id)
    {
        $transaction = KpPurchaseTransaction::where('id', $transaction_id)
            ->with('userWastePreference.user', 'details.item', 'details.pricePoint.kp_units_info')
            ->get()->first();
        // Load relationships needed for the receipt
        // $transaction->load(['user_waste_pref.user', 'details.item', 'details.pricePoint.kp_units_info']);
        $orgInfos = Organization::getOrgName(Auth::user()->org_id_fk);
        return view('keptkayas.purchase.receipt', compact('transaction', 'orgInfos'));
    }


    public function saveTransactionForMachine(Request $request)
    {
        // 1. Validation (ตรวจสอบความถูกต้องของข้อมูลพื้นฐาน)
        $request->validate([
            'acceptedBottles' => 'required|array|min:1',
            'acceptedBottles.*.user_id' => 'required|numeric|exists:users,id',
            'acceptedBottles.*.kp_tbank_item_id' => 'required|numeric',
            'acceptedBottles.*.kp_tbank_items_pricepoint_id' => 'required|numeric',
            'acceptedBottles.*.amount_in_units' => 'required|numeric|min:0',
            'acceptedBottles.*.price_per_unit' => 'required|numeric|min:0',
            'acceptedBottles.*.amount' => 'required|numeric|min:0',
            'acceptedBottles.*.points' => 'required|numeric|min:0',
            'acceptedBottles.*.unit_name' => 'required|string',
        ]);

        $acceptedBottles = $request->input('acceptedBottles');

        // 2. Initial Checks and Aggregation
        // ดึง user_id จากขวดใบแรก (สมมติว่าการทำธุรกรรมมาจากลูกค้าคนเดียว)
        $userId = $acceptedBottles[0]['user_id'];
        $recorderId = Auth::guard('web_hs1')->id(); // ผู้บันทึกคือ System/Machine user ที่ Authenticate API Call

        $user = User::find($userId);
        if (!$user) {
            // หากไม่พบ User อาจเกิดจาก user_id ที่ส่งมาผิดพลาด
            return response()->json(['error' => 'Customer User ID not found.'], 204);
        }
        if (!session()->has('db_conn')) {
            $conn = Organization::find($user->org_id_fk);
            session(['db_conn' => $conn->org_database]);
        }

        $userWastePref = KpUserWastePreference::where('user_id', $userId)->first();
        if (!$userWastePref) {
            return response()->json(['error' => 'User Waste Preference not configured for this user.'], 204);
        }

        // คำนวณยอดรวมทั้งหมด
        $totalWeight = 0;
        $totalAmount = 0;
        $totalPoints = 0;

        foreach ($acceptedBottles as $bottle) {
            // ใช้ floatval เพื่อแปลงค่าจาก string (toFixed(2) จาก JS) ให้เป็นตัวเลข
            $totalWeight += floatval($bottle['amount_in_units']);
            $totalAmount += floatval($bottle['amount']);
            $totalPoints += floatval($bottle['points']);
        }

        DB::beginTransaction();
        try {

            $machine = Machine::where('machine_id', $acceptedBottles[0]['machine_id'])->get('id')->first();
            // 3. Create the main purchase transaction
            $transaction = KpPurchaseTransaction::create([
                'kp_u_trans_no' => 'M-' . Carbon::now()->format('YmdHis') . Str::random(3), // 'M' for Machine
                'kp_user_w_pref_id_fk' => $userWastePref->id,
                'transaction_date' => date('Y-m-d'),
                'total_weight' => $totalWeight,
                'total_amount' => $totalAmount,
                'total_points' => $totalPoints,
                'recorder_id' => $recorderId, // ID ของ Machine User / System User
                'machine_id_fk' => $machine->id,      // เพิ่ม Field เพื่อระบุว่ามาจากตู้
            ]);

            // อัปเดตยอดเงิน/คะแนนคงเหลือของลูกค้า
            (new KPAccounts())->setConnection('envsogo_hs1')->updateBalanceAndPoint($userWastePref->id, $totalAmount, $totalPoints);

            // 4. Create the purchase details for each item
            foreach ($acceptedBottles as $item) {
                KpPurchaseTransactionDetail::create([
                    'kp_purchase_trans_id' => $transaction->id,
                    'kp_recycle_item_id' => $item['kp_tbank_item_id'],
                    'kp_tbank_items_pricepoint_id' => $item['kp_tbank_items_pricepoint_id'],
                    'amount_in_units' => $item['amount_in_units'],
                    'price_per_unit' => $item['price_per_unit'],
                    'amount' => $item['amount'],
                    'points' => $item['points'],
                    'unit_type' => $item['unit_name'],
                    'recorder_id' => $recorderId
                ]);
            }

            DB::commit();

            // 5. Return success JSON response
            return response()->json([
                'message' => 'Transaction recorded successfully.',
                'transaction_id' => $transaction->id,
                'total_amount' => $totalAmount,
                'redirect_url' => route('keptkayas.purchase.receipt', $transaction->id),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Failed to record transaction due to internal error.',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    public function showPurchaseForm(Request $request, $user_id)
    {
        $user = User::find($user_id);
        $request->session()->put('purchase_user_id', $user_id);
        // ตรวจสอบว่าผู้ใช้งานที่เลือกเป็นสมาชิกธนาคารขยะหรือไม่
        if (!$user->wastePreference || !$user->kpUserPreferenceis_recycle_bank) {
            return redirect()->route('keptkayas.purchase.select_user')->with('error', 'ผู้ใช้งานนี้ไม่ได้เป็นสมาชิกธนาคารขยะ');
        }

        // ดึงรายการขยะทั้งหมด และโหลดราคาที่ Active
        $recycleItems = KpTbankItems::with(['activePrices.kp_units_info', 'emissionFactor'])
            ->where('org_id_fk', Auth::user()->org_id_fk)
            ->whereHas('activePrices.kp_units_info')
            ->get();

        // ดึงข้อมูลหน่วยนับทั้งหมด (ถ้าต้องการใช้ใน dropdown)
        $allUnits = KpTbankUnits::where('org_id_fk', $user->org_id_fk)->get();
        $itemsGroups = KpTbankItemsGroups::where('status', 'active')->get();

        return view('keptkayas.purchase.purchase_form', compact('user', 'recycleItems', 'allUnits', 'itemsGroups'));
    }


    /**
     * Summary of removeFromCart
     * @param Request $request
     * @param mixed $index
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

    public function getUnitsForItem($itemId)
    {
        // 1. ค้นหาราคาที่ใช้งานอยู่สำหรับ item นี้
        // (เลือกรายการที่มี effective_date <= วันนี้ และ end_date >= วันนี้ หรือ end_date เป็น null)
        $today = now()->format('Y-m-d');

        // ตรวจสอบโครงสร้าง Model ของคุณ ซึ่งอาจซับซ้อนกว่านี้
        $unitsAndPrices = KpTbankItemsPriceAndPoint::where('kp_items_idfk', $itemId)
            ->where('status', 'active')
            // ดึงราคาที่ valid ณ วันนี้ (ควรปรับตามตรรกะราคาของคุณ)
            // ->where('effective_date', '<=', $today)
            // ->where(function ($query) use ($today) {
            //     $query->where('end_date', '>=', $today)
            //           ->orWhereNull('end_date');
            // })
            ->with('kp_units_info') // โหลดความสัมพันธ์ไปยังตารางหน่วยนับ (KpUnit)
            ->get();

        $data = [];
        foreach ($unitsAndPrices as $priceEntry) {
            // ดึงข้อมูลที่จำเป็นส่งกลับไป Frontend
            $data[] = [
                'unit_id'           => $priceEntry->kp_units_idfk,
                'unit_name'         => $priceEntry->kp_units_info->unitname,
                'price_for_member'  => $priceEntry->price_for_member,
                'point'             => $priceEntry->point,
            ];
        }

        return response()->json($data);
    }

    /**
     * Summary of showPurchaseHistory
     * @param mixed $kp_waste_pref_id
     * @return \Illuminate\Contracts\View\View
     */
    public function showPurchaseHistory($kp_waste_pref_id)
    {
        // return $kp_waste_pref_id;
        $kp_waste_pref = KpUserWastePreference::find($kp_waste_pref_id);
        // Load userpurchase transactions for the user

        $userHistory = User::where('id', $kp_waste_pref->user_id)
            ->with([
                'wastePreference.purchaseTransactions.details.item',
                'wastePreference.purchaseTransactions.details.pricePoint.kp_units_info'
            ])
            ->get()->first();


        return view('keptkayas.purchase.history', compact('userHistory'));
    }


    public function selectRoute(Request $request)
{
    if ($request->isMethod('post')) {
        $routeId = $request->input('route_id');

        if ($routeId === 'all') {
            session()->forget('purchase_route_id');
        } else {
            session(['purchase_route_id' => $routeId]);
        }

        return redirect()->route('keptkayas.purchase.select_user')
            ->with('success', 'เปลี่ยนเขตการรับซื้อเรียบร้อยแล้ว');
    }

    $orgId = Auth::user()->org_id_fk;
    $routes = KpPurchaseRoute::where('org_id_fk', $orgId)
        ->where('status', 'active')
        ->with('zones')
        ->get();

    $currentRouteId = session('purchase_route_id', 'all');

    return view('keptkayas.purchase.select_route', compact('routes', 'currentRouteId'));
}
// หน้าแสดงรายการและฟอร์มสร้าง/แก้ไขเขตรับซื้อ (สำหรับ Admin)
public function routeIndex()
{
    $orgId = Auth::user()->org_id_fk;
    
    // ดึงรายการเขตรับซื้อทั้งหมดพร้อมโซนที่ผูกไว้
    $routes = KpPurchaseRoute::where('org_id_fk', $orgId)
        ->with('zones')
        ->orderBy('id', 'desc')
        ->get();

    // ดึงโซนทั้งหมดขององค์กรนี้มาให้เลือกใน Checkbox
    $allZones = \App\Models\Admin\Zone::where('org_id_fk', $orgId)->get();

    return view('keptkayas.purchase.routes_manage', compact('routes', 'allZones'));
}

// บันทึก/อัปเดตข้อมูลเขตรับซื้อ
public function routeSave(Request $request)
{
    $request->validate([
        'route_name' => 'required|string|max:255',
        'zone_ids'   => 'nullable|array',
        'zone_ids.*' => 'exists:zones,id',
    ], [
        'route_name.required' => 'กรุณากรอกชื่อเขตรับซื้อ',
    ]);

    $orgId = Auth::user()->org_id_fk;
    $routeId = $request->input('route_id');

    DB::transaction(function () use ($request, $orgId, $routeId) {
        // 1. บันทึก/อัปเดต หัวตาราง kp_purchase_routes
        $route = KpPurchaseRoute::updateOrCreate(
            ['id' => $routeId, 'org_id_fk' => $orgId],
            [
                'route_name' => $request->input('route_name'),
                'status'     => $request->input('status', 'active'),
            ]
        );

        // 2. ซิงก์ข้อมูลโซนที่เลือกในตารางกลาง kp_purchase_route_zones
        $zoneIds = $request->input('zone_ids', []);
        $route->zones()->sync($zoneIds);
    });

    return back()->with('success', 'บันทึกข้อมูลเขตการรับซื้อเรียบร้อยแล้ว');
}

// ลบเขตรับซื้อ
public function routeDelete($id)
{
    $orgId = Auth::user()->org_id_fk;
    $route = KpPurchaseRoute::where('id', $id)
        ->where('org_id_fk', $orgId)
        ->first();

    if ($route) {
        $route->delete(); // จะ cascade ลบใน kp_purchase_route_zones ให้อัตโนมัติ
        return back()->with('success', 'ลบเขตการรับซื้อเรียบร้อยแล้ว');
    }

    return back()->with('error', 'ไม่พบข้อมูลเขตที่ต้องการลบ');
}
}
