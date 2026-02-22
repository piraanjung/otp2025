<?php

namespace App\Http\Controllers;

use App\Models\KeptKaya\KPAccounts;
use App\Models\KeptKaya\KpShopCategory;
use App\Models\KeptKaya\KpShopProduct;
use App\Models\KeptKaya\KpShopOrder;
use App\Models\KeptKaya\KpShopOrderDetail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class KpMemberShopController extends Controller
{
    /**
     * Display a list of products available for purchase.
     */
    public function index(Request $request)
    {

        $user= User::find(Auth::id());
        
        $request->session()->put('user_from_line', 1);
        // ManagesTenantConnection::configConnection(session('db_conn'));
        $member = KPAccounts::with('userWastePreference', 'userWastePreference.user')
            ->where('u_wpref_id_fk', $user->wastePreference->id)->get()->first();
        $products = KpShopProduct::where('status', 'active')->paginate(12);
        $product_categorys = KpShopCategory::all();
        return view('keptkayas.shop.index', compact('products', 'member','product_categorys'));
    }

    /**
     * Add a product to the member's shopping cart.
     */
    public function addToCart(Request $request)
    {


        $request->validate([
            'product_id' => 'required|exists:kp_shop_products,id',
            'payment_method' => 'required',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = KpShopProduct::find($request->product_id);
        $quantity = $request->quantity;

        if ($product->stock < $quantity) {
            return back()->with('error', 'สินค้าไม่เพียงพอในสต็อก');
        }

        $cart = Session::get('shop_cart', []);
        $payment_method = $request->payment_method;
        $point_price = $payment_method == 'points' ? $product->point_price * $quantity : 0;
        $cash_price = $payment_method != 'points' ? $product->cash_price * $quantity : 0;
        $cartItem = [
            'product_id' => $product->id,
            'product_name' => $product->product_name,
            'point_price' => $product->point_price,
            'cash_price' => $product->cash_price,
            'image_path' => $product->image_path,
            'points' => [
                'quantity' => $payment_method == 'points' ? $quantity : 0,
                'total_points' => $point_price,
                'total_cash' => 0,
            ],
            'cash' => [
                'quantity' =>  $payment_method == 'cash' ? $quantity : 0,
                'total_points' => 0,
                'total_cash' => $cash_price,
            ]

        ];

        // Check if item already exists in cart, then update quantity
        if (isset($cart[$product->id])) {
            $cart[$product->id][$payment_method]['quantity'] += $quantity;
            $cart[$product->id][$payment_method]['total_points'] += $payment_method == 'points' ? $product->point_price * $quantity : 0;
            $cart[$product->id][$payment_method]['total_cash'] += $payment_method != 'points' ? $product->cash_price * $quantity : 0;
        } else {
            $cart[$product->id] = $cartItem;
        }

        Session::put('shop_cart', $cart);

        return back()->with('success', 'เพิ่มสินค้าลงในรถเข็นแล้ว');
    }

    /**
     * Display the shopping cart.
     */
    public function showCart()
    {
        $cart = Session::get('shop_cart', []);
        $user = Auth::user();

        $totalPoints = collect($cart)->sum('total_points');
        $totalCash = collect($cart)->sum('total_cash');

        return view('keptkayas.shop.cart', compact('cart', 'user', 'totalPoints', 'totalCash'));
    }

    /**
     * Process the order from the shopping cart.
     */
    public function placeOrder(Request $request)
    {
        $carts = Session::get('shop_cart', []);
        $user = User::where('id', 1940)->with('wastePreference', 'wastePreference.kp_account')->get()->first();
        if($user->zone_id == ''){
        //ยังไม่มีข้อมูลที่อยู้จัดส่งให้กรอกข้อมูลก่อน
            return redirect()->route('admin.register');
        }
        if (empty($carts)) {
            return redirect()->route('keptkayas.shop.index')->with('error', 'รถเข็นว่างเปล่า ไม่สามารถทำรายการได้');
        }

        $totalPointsInCart  = 0;
        $totalCashInCart  = 0;
        foreach ($carts as $cart) {
            $totalPointsInCart += $cart['points']['total_points'];
            $totalCashInCart += $cart['cash']['total_cash'];
        }
        // TODO: Add logic to check user's actual points/cash balance
        // if ($user->points_balance < $totalPointsInCart) {
        //     return back()->with('error', 'คะแนนสะสมไม่เพียงพอ');
        // }

        // DB::beginTransaction();
        // try {
        // 1. Create the main order
        $order = KpShopOrder::create([
            'order_no' => 'ORD-' . Carbon::now()->format('YmdHis') . Str::random(4),
            'user_wpref_id' => $user->wastePreference->id,
            'total_points' => $totalPointsInCart,
            'total_cash' => $totalCashInCart,
            'order_status' => 'pending',
            'recorder_id' => Auth::id(), // Recorder is the user themselves
        ]);

        // 2. Create the order details
        foreach ($carts as $cart) {
            foreach (['cash', 'points'] as $item) {
                if ($cart[$item]['quantity'] > 0) {
                    KpShopOrderDetail::create([
                        'kp_shop_order_id' => $order->id,
                        'kp_shop_product_id' => $cart['product_id'],
                        'order_type' => $item,
                        'quantity' => $cart[$item]['quantity'],
                        'point_per_unit' => $cart['point_price'],
                        'cash_per_unit' => $cart['cash_price'],
                        'total_points' => $cart[$item]['total_points'],
                        'total_cash' => $cart[$item]['total_cash'],
                        'status' => 'pending'
                    ]);


                    // 3. Update product stock
                    $product = KpShopProduct::find($cart['product_id']);
                    $product->stock -= $cart[$item]['quantity'];
                    $product->save();
                }
            }
        }

        // 4. Clear the cart session
        Session::forget('shop_cart');

        // 5. Update user's points/cash balance (placeholder)
        $kpAcc = KPAccounts::find($user->wastePreference->kp_account->id);
        $kpAcc->balance -= $totalPointsInCart;
        $kpAcc->points -= $totalCashInCart;
        $kpAcc->save();

        // DB::commit();

        return redirect()->route('keptkayas.shop.order_history')->with('success', 'คำสั่งซื้อถูกบันทึกเรียบร้อยแล้ว');
        // } catch (\Exception $e) {
        //     DB::rollBack();
        //     return back()->with('error', 'เกิดข้อผิดพลาดในการทำรายการ: ' . $e->getMessage());
        // }
    }

    /**
     * Remove an item from the shopping cart.
     */
    public function removeFromCart($productId)
    {
        $cart = Session::get('shop_cart', []);
        // คืนจำนวนเงินและแต้ม ที่ KPAccounts Table
        if (isset($cart[$productId])) {
            unset($cart[$productId]);
            Session::put('shop_cart', $cart);
            return back()->with('success', 'ลบสินค้าออกจากรถเข็นแล้ว');
        }
        return back()->with('error', 'ไม่พบสินค้าที่ต้องการลบ');
    }

    /**
     * Display order history for the current user.
     */
    public function orderHistory()
    {
        $user = User::where('id', 1940)->with('wastePreference')->get()->first();
        $orders = KpShopOrder::where('user_wpref_id', $user->wastePreference->id)
            ->with('details.product')
            ->orderByDesc('created_at')
            ->get();

        return view('keptkayas.shop.order_history', compact('orders'));
    }
}
