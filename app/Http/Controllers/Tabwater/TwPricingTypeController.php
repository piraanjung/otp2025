<?php

namespace App\Http\Controllers\Tabwater;

use App\Models\Tabwater\TwPricingType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;

class TwPricingTypeController extends Controller
{
    /**
     * แสดงรายการประเภทราคาทั้งหมด
     */
    public function index()
    {
        $pricingTypes = TwPricingType::all();
        return view('superadmin.pricing_types.index', compact('pricingTypes'));
    }

    /**
     * แสดงฟอร์มสำหรับสร้างประเภทราคาใหม่
     */
    public function create()
    {
        $pricingType = TwPricingType::all();
        return view('superadmin.pricing_types.create',compact('pricingType'));
    }

    /**
     * จัดเก็บประเภทราคาใหม่ในฐานข้อมูล
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:tw_pricing_types,name',
            'description' => 'nullable|string',
        ]);

        TwPricingType::create($request->all());

        return redirect()->route('admin.pricing_types.index')
                         ->with('success', 'Pricing type created successfully.');
    }

    /**
     * แสดงรายละเอียดของประเภทราคาที่ระบุ
     */
    public function show(TwPricingType $pricingType)
    {
        return view('superadmin.pricing_types.show', compact('pricingType'));
    }

    /**
     * แสดงฟอร์มสำหรับแก้ไขประเภทราคาที่ระบุ
     */
    public function edit(TwPricingType $pricingType)
    {
        return view('superadmin.pricing_types.edit', compact('pricingType'));
    }

    /**
     * อัปเดตประเภทราคาที่ระบุในฐานข้อมูล
     */
    public function update(Request $request, TwPricingType $pricingType)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('tw_pricing_types', 'name')->ignore($pricingType->id),
            ],
            'description' => 'nullable|string',
        ]);

        $pricingType->update($request->all());

        return redirect()->route('admin.pricing_types.index')
                         ->with('success', 'Pricing type updated successfully.');
    }

    /**
     * ลบประเภทราคาที่ระบุออกจากฐานข้อมูล
     */
    public function destroy(TwPricingType $pricingType)
    {
        // ควรเพิ่มการตรวจสอบว่ามี Meter Rate Configs ที่ใช้ PricingType นี้อยู่หรือไม่
        // ก่อนที่จะลบ เพื่อป้องกันข้อมูลเสียหาย
        if ($pricingType->rateConfigs()->exists()) {
            return back()->with('error', 'Cannot delete pricing type because it is used by meter rate configurations.');
        }

        $pricingType->delete();

        return redirect()->route('admin.pricing_types.index')
                         ->with('success', 'Pricing type deleted successfully.');
    }
}