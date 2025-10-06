<?php

namespace App\Http\Controllers\Tabwater;

use App\Http\Controllers\Controller;
use App\Models\Tabwater\MeterTypeRateConfig;
use App\Models\Tabwater\TwMeterType;
use App\Models\Tabwater\TwPricingType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MeterRateConfigController extends Controller
{
    /**
     * แสดงรายการการตั้งค่าอัตราค่าน้ำทั้งหมด
     */
    public function index()
    {
        $rateConfigs = MeterTypeRateConfig::with('meterType', 'pricingType')->get();
        return view('superadmin.meter_rates.index', compact('rateConfigs'));
    }

    /**
     * แสดงฟอร์มสำหรับสร้างการตั้งค่าอัตราค่าน้ำใหม่
     */
    public function create()
    {
        $meterTypes = TwMeterType::all();
        $pricingTypes = TwPricingType::all();
        $meterRateConfig = new MeterTypeRateConfig(); // สร้าง Object ว่างเปล่า

        return view('superadmin.meter_rates.create', compact('meterTypes', 'pricingTypes', 'meterRateConfig'));
    }

    /**
     * จัดเก็บการตั้งค่าอัตราค่าน้ำใหม่ในฐานข้อมูล
     */
    public function store(Request $request)
    {
        $validated = $request->validate($this->rules());

        // DB::transaction(function () use ($validated, $request) {
            $rateConfig = MeterTypeRateConfig::create([
                'meter_type_id' => $validated['meter_type_id'],
                'pricing_type_id' => $validated['pricing_type_id'],
                'min_usage_charge' => $validated['min_usage_charge'],
                'fixed_rate_per_unit' => $validated['fixed_rate_per_unit'] ?? null,
                'effective_date' => $validated['effective_date'],
                'end_date' => $validated['end_date'] ?? null,
                'is_active' => $request->has('is_active'),
                'comment' => $validated['comment'] ?? null,
            ]);

            // ถ้าเป็น Progressive Pricing ให้บันทึก Tiers
            $pricingTypeName = TwPricingType::find($validated['pricing_type_id'])->name;
            if ($pricingTypeName === 'progressive' && isset($validated['tiers'])) {
                foreach ($validated['tiers'] as $order => $tierData) {
                    $rateConfig->Ratetiers()->create([
                        'min_units' => $tierData['min_units'],
                        'max_units' => $tierData['max_units'] ?? null,
                        'rate_per_unit' => $tierData['rate_per_unit'],
                        'tier_order' => $order + 1, // ลำดับ Tier
                        'comment' => $tierData['comment'] ?? null,
                    ]);
                }
            }
        // });

        return redirect()->route('admin.meter_rates.index')
                         ->with('success', 'Rate configuration created successfully.');
    }

    /**
     * แสดงรายละเอียดของการตั้งค่าอัตราค่าน้ำที่ระบุ
     */
    public function show(MeterTypeRateConfig $meterRateConfig)
    {
        $meterRateConfig->load('meterType', 'pricingType', 'Ratetiers');
        return view('superadmin.meter_rates.show', compact('meterRateConfig'));
    }

    /**
     * แสดงฟอร์มสำหรับแก้ไขการตั้งค่าอัตราค่าน้ำที่ระบุ
     */
    public function edit($meterRateConfigId)
    {
        $meterRateConfig = MeterTypeRateConfig::where('id', $meterRateConfigId)
        ->with('Ratetiers')
        ->first();
        
        $meterTypes = TwMeterType::all();
        $pricingTypes = TwPricingType::all();
        $meterRateConfig->load('Ratetiers'); // โหลด Tiers เพื่อแสดงในฟอร์ม
        return view('superadmin.meter_rates.edit', compact('meterRateConfig', 'meterTypes', 'pricingTypes'));
    }

    /**
     * อัปเดตการตั้งค่าอัตราค่าน้ำที่ระบุในฐานข้อมูล
     */
    public function update(Request $request, $meterRateConfigId)
    {
        $validated = $request->validate($this->rules($meterRateConfigId));
        $meterRateConfig = MeterTypeRateConfig::find($meterRateConfigId);
        // DB::transaction(function () use ($validated, $request, $meterRateConfig) {
            $meterRateConfig->update([
                'meter_type_id' => $validated['meter_type_id'],
                'pricing_type_id' => $validated['pricing_type_id'],
                'min_usage_charge' => $validated['min_usage_charge'],
                'fixed_rate_per_unit' => $validated['fixed_rate_per_unit'] ?? null,
                'effective_date' => $validated['effective_date'],
                'end_date' => $validated['end_date'] ?? null,
                'is_active' => $request->has('is_active'),
                'comment' => $validated['comment'] ?? null,
            ]);
            // ลบ Tiers เก่าและบันทึก Tiers ใหม่สำหรับ Progressive Pricing
            $pricingTypeName = TwPricingType::find($validated['pricing_type_id'])->name;
            if ($pricingTypeName === 'progressive') {
                $meterRateConfig->Ratetiers()->delete(); // ลบ Tiers เก่าทั้งหมด
                if (isset($validated['tiers'])) {
                    foreach ($validated['tiers'] as $order => $tierData) {
                        $meterRateConfig->Ratetiers()->create([
                            'meter_type_rate_config_id' => $meterRateConfig->id,
                            'min_units' => $tierData['min_units'],
                            'max_units' => $tierData['max_units'] ?? null,
                            'rate_per_unit' => $tierData['rate_per_unit'],
                            'tier_order' => $order + 1,
                            'comment' => $tierData['comment'] ?? null,
                        ]);
                    }
                }
            } else {
                // ถ้าเปลี่ยนเป็น Fixed Pricing ให้ลบ Tiers ที่เคยมีออก
                $meterRateConfig->Ratetiers()->delete();
            }
        // });

        return redirect()->route('admin.meter_rates.index')
                         ->with('success', 'Rate configuration updated successfully.');
    }

    /**
     * ลบการตั้งค่าอัตราค่าน้ำที่ระบุออกจากฐานข้อมูล
     */
    public function destroy(MeterTypeRateConfig $meterRateConfig)
    {
        $meterRateConfig->delete(); // การลบ Rate Config จะลบ Tiers ที่เกี่ยวข้องด้วย (onDelete('cascade'))

        return redirect()->route('admin.meter_rates.index')
                         ->with('success', 'Rate configuration deleted successfully.');
    }

    /**
     * กำหนดกฎการตรวจสอบข้อมูล
     */
    private function rules($id = null)
    {
        return [
            'meter_type_id' => 'required|exists:tw_meter_types,id',
            'pricing_type_id' => 'required|exists:tw_pricing_types,id',
            'min_usage_charge' => 'required|numeric|min:0',
            'fixed_rate_per_unit' => 'nullable|numeric|min:0', // จะถูก validate เพิ่มเติมด้วย conditional validation
            'effective_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:effective_date',
            'is_active' => 'boolean',
            'comment' => 'nullable|string',
            'tiers' => 'array', // สำหรับ Progressive Pricing
            'tiers.*.min_units' => 'required_with:tiers|integer|min:0',
            'tiers.*.max_units' => 'nullable|integer|gt:tiers.*.min_units',
            'tiers.*.rate_per_unit' => 'required_with:tiers|numeric|min:0',
            'tiers.*.comment' => 'nullable|string',
        ];
    }
}