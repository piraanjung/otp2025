<?php

namespace App\Http\Controllers\KeptKaya;

use App\Http\Controllers\Controller;
use App\Models\KeptKaya\KpPurchaseShop;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class KpPurchaseShopController extends Controller
{
    /**
     * Display a listing of purchase shops.
     */
    public function index()
    {
        $shops = KpPurchaseShop::orderBy('shop_name')->paginate(20);
        return view('keptkaya.purchase_shops.index', compact('shops'));
    }

    /**
     * Show the form for creating a new purchase shop.
     */
    public function create()
    {
        $shop = new KpPurchaseShop();
        return view('keptkaya.purchase_shops.create', compact('shop'));
    }

    /**
     * Store a newly created purchase shop in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'shop_name' => 'required|string|unique:kp_purchase_shops,shop_name',
            'contact_person' => 'nullable|string',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'status' => 'required|in:active,inactive',
            'comment' => 'nullable|string',
        ]);

        KpPurchaseShop::create($validated);

        return redirect()->route('keptkaya.purchase-shops.index')
            ->with('success', 'ร้านรับซื้อถูกเพิ่มเรียบร้อยแล้ว');
    }

    /**
     * Show the form for editing the specified purchase shop.
     */
    public function edit(KpPurchaseShop $shop)
    {
        return view('keptkaya.purchase_shops.edit', compact('shop'));
    }

    /**
     * Update the specified purchase shop in storage.
     */
    public function update(Request $request, KpPurchaseShop $shop)
    {
        $validated = $request->validate([
            'shop_name' => ['required', 'string', Rule::unique('kp_purchase_shops', 'shop_name')->ignore($shop->id)],
            'contact_person' => 'nullable|string',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'status' => 'required|in:active,inactive',
            'comment' => 'nullable|string',
        ]);

        $shop->update($validated);

        return redirect()->route('keptkaya.purchase_shops.index')
            ->with('success', 'ร้านรับซื้อถูกอัปเดตเรียบร้อยแล้ว');
    }

    /**
     * Remove the specified purchase shop from storage.
     */
    public function destroy(KpPurchaseShop $shop)
    {
        $shop->delete();
        return redirect()->route('keptkaya.purchase_shops.index')
            ->with('success', 'ร้านรับซื้อถูกลบเรียบร้อยแล้ว');
    }
}
