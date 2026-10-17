<?php

namespace App\Http\Controllers;

use App\Models\KeptKaya\KpShopProduct;
use App\Models\KeptKaya\KpShopCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class KpShopProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = KpShopProduct::with('category')->latest()->paginate(10);

        return view('superadmin.keptkaya.shop_products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = KpShopCategory::all();
        return view('superadmin.keptkaya.shop_products.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_name' => 'required|string|max:255',
            'product_description' => 'nullable|string',
            'category_id' => 'required|exists:kp_shop_categories,id',
            'point_price' => 'required|integer|min:0',
            'cash_price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imageFile = $request->file('image');
            $imageName = Str::slug($request->product_name) . '-' . time() . '.' . $imageFile->getClientOriginalExtension();
            $imagePath = $imageFile->storeAs('keptkaya/shop_products', $imageName, 'public');
        }

        KpShopProduct::create([
            'product_name' => $request->product_name,
            'product_code' => 'xxx',
            'product_description' => $request->product_description,
            'kp_shop_category_id' => $request->category_id,
            'point_price' => $request->point_price,
            'cash_price' => $request->cash_price,
            'stock' => $request->stock,
            'image_path' => $imagePath,
        ]);
       
        return redirect()->route('superadmin.keptkaya.shop_products.index')->with('success', 'สินค้าถูกเพิ่มเรียบร้อยแล้ว');
    }

    /**
     * Display the specified resource.
     */
    public function show(KpShopProduct $product)
    {
        $product->load('category');
        return view('superadmin.keptkaya.shop_products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(KpShopProduct $shop_product)
    {
        $categories = KpShopCategory::all();
        return view('superadmin.keptkaya.shop_products.edit', compact('shop_product', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, KpShopProduct $product)
    {
        $request->validate([
            'product_name' => 'required|string|max:255',
            'product_description' => 'nullable|string',
            'category_id' => 'required|exists:kp_shop_categories,id',
            'point_price' => 'required|integer|min:0',
            'cash_price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        $imagePath = $product->image_path;
        if ($request->hasFile('image')) {
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
            $imageFile = $request->file('image');
            $imageName = Str::slug($request->product_name) . '-' . time() . '.' . $imageFile->getClientOriginalExtension();
            $imagePath = $imageFile->storeAs('keptkaya/shop_products', $imageName, 'public');
        }

        $product->update([
            'product_name' => $request->product_name,
            'product_description' => $request->product_description,
            'category_id' => $request->category_id,
            'point_price' => $request->point_price,
            'cash_price' => $request->cash_price,
            'stock' => $request->stock,
            'image_path' => $imagePath,
        ]);

        return redirect()->route('superadmin.keptkaya.shop_products.index')->with('success', 'สินค้าถูกอัปเดตเรียบร้อยแล้ว');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(KpShopProduct $product)
    {
        if ($product->image_path) {
            Storage::disk('public')->delete($product->image_path);
        }
        $product->delete();

        return redirect()->route('superadmin.keptkaya.shop_products.index')->with('success', 'สินค้าถูกลบเรียบร้อยแล้ว');
    }
}
