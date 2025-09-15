<?php

namespace App\Http\Controllers\KeptKaya;

use App\Http\Controllers\Controller;
use App\Models\Keptkaya\KpTbankItems;
use App\Models\KeptKaya\UserWastePreference;
use App\Models\User;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        return view('keptkaya.tbank.cart.index');
    }

    public function create(Request $request)
    {
        $members = UserWastePreference::where('is_waste_bank', "1")->get();
        return view('keptkaya.tbank.cart.create', compact('members'));
    }

    public function addToCart(Request $request, $id, $amount)
    {
        $items = KpTbankItems::where('id', $id)
            ->with('items_price_and_point_infos')
            ->get()->first();
        $cart =   $request->session()->get('cart');
        if (isset($cart[$id])) {
            $cart[$id]['quantity']++;
        } else {
            $cart[$id] = [
                "items_name"    => $items->itemsname,
                "photo"         => $items->image,
                "price"         => $items->items_price_and_point_infos[0]->price_for_member,
                "reward_point"  => $items->items_price_and_point_infos[0]->reward_point,
                "quantity"      => $amount
            ];
        }
        session()->put('cart', $cart);

        return redirect()->route('items.buy_items')->with('success', 'items add to cart successfully!');
    }

    public function cartLists($user_id)
    {
        return  $member =  User::where('id', $user_id)
            // ->with([
            //     'user_kaya_infos.trash_zone' => function($q){
            //         return $q->select('id', 'zone_name');
            //     },
            //     'user_kaya_infos.trash_subzone' => function($q){
            //         return $q->select('id', 'subzone_name');
            //     },
            // ])
            ->get(['id', 'prefix', 'firstname', 'lastname', 'zone_id', 'subzone_id', 'address'])->first();

        return view('keptkaya.tbank.cart.cart_lists', compact('member'));
    }
}
