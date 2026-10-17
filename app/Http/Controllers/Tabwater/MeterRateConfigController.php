<?php

namespace App\Http\Controllers\Tabwater;

use App\Http\Controllers\Controller;
use App\Models\Tabwater\MeterTypeRateConfig;
use App\Models\Tabwater\TwMeterType;
use App\Models\Tabwater\TwPricingType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MeterRateConfigController extends Controller
{
    // Helper function ตรวจสอบความเป็นเจ้าของ
    private function checkOwnership($orgId)
    {
        if ($orgId != Auth::user()->org_id_fk) {
            abort(403, 'Unauthorized action.');
        }
    }

    public function index()
    {
        // Optimization: Query ตรงๆ ผ่าน org_id_fk ได้เลย ไม่ต้อง whereHas ให้หนัก Server
        $rateConfigs = MeterTypeRateConfig::with(['meterType', 'pricingType'])
            ->where('org_id_fk', Auth::user()->org_id_fk)
            ->latest()
            ->get();

        return view('superadmin.meter_rates.index', compact('rateConfigs'));
    }

    public function create()
    {
        // Security: เลือกได้เฉพาะ MeterType ของ Org ตัวเองเท่านั้น
        $meterTypes = TwMeterType::where('org_id_fk', Auth::user()->org_id_fk)
        ->whereDoesntHave('rateConfigs') // <--- คำสั่งมหัศจรรย์อยู่ตรงนี้
        ->get();
        $pricingTypes = TwPricingType::all();
        $meterRateConfig = new MeterTypeRateConfig();
        // (Optional) เช็ค UX: ถ้าไม่มี MeterType เหลือให้เลือกเลย อาจจะ redirect กลับพร้อมแจ้งเตือน
        if ($meterTypes->isEmpty()) {
            return redirect()->route('admin.meter_rates.index')
                ->with('error', 'คุณได้ตั้งค่าอัตราค่าน้ำครบทุกประเภทมิเตอร์แล้ว (ไม่สามารถสร้างเพิ่มได้)');
        }
        return view('superadmin.meter_rates.create', compact('meterTypes', 'pricingTypes', 'meterRateConfig'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->rules());

        // ใช้ Transaction เพื่อความชัวร์ (ถ้า insert tier พัง ให้ rollback ทั้งหมด)
        DB::transaction(function () use ($validated, $request) {
            
            // 1. Create Main Config
            $rateConfig = MeterTypeRateConfig::create([
                'meter_type_id_fk'    => $validated['meter_type_id_fk'], // แก้ชื่อ field ให้ตรง DB
                'pricing_type_id'     => $validated['pricing_type_id'],
                'min_usage_charge'    => $validated['min_usage_charge'],
                'fixed_rate_per_unit' => $validated['fixed_rate_per_unit'] ?? null,
                'vat'                 => $validated['vat'] ?? 0, // อย่าลืม field vat
                'effective_date'      => $validated['effective_date'],
                'end_date'            => $validated['end_date'] ?? null,
                'org_id_fk'           => Auth::user()->org_id_fk,
                'is_active'           => $request->has('is_active') ? 1 : 0, // แปลงเป็น int
                'comment'             => $validated['comment'] ?? null,
            ]);

            // 2. Create Tiers (ถ้าเป็น Progressive)
          return  $this->saveTiers($rateConfig, $validated);
        });

        return redirect()->route('admin.meter_rates.index')
            ->with('success', 'บันทึกการตั้งค่าเรียบร้อยแล้ว');
    }

    public function edit($id)
    {
        // Security: ค้นหาโดยระบุ org_id_fk ด้วยเสมอ
        $meterRateConfig = MeterTypeRateConfig::where('id', $id)
            ->where('org_id_fk', Auth::user()->org_id_fk)
            ->with('Ratetiers')
            ->firstOrFail();

        $meterTypes = TwMeterType::where('org_id_fk', Auth::user()->org_id_fk)->get();
        $pricingTypes = TwPricingType::all();

        return view('superadmin.meter_rates.edit', compact('meterRateConfig', 'meterTypes', 'pricingTypes'));
    }

    public function update(Request $request, $id)
    {
        // Security Check
        $meterRateConfig = MeterTypeRateConfig::where('id', $id)
            ->where('org_id_fk', Auth::user()->org_id_fk)
            ->firstOrFail();

        $validated = $request->validate($this->rules($id));

        DB::transaction(function () use ($validated, $request, $meterRateConfig) {
            // 1. Update Main Config
            $meterRateConfig->update([
                'meter_type_id_fk'    => $validated['meter_type_id_fk'],
                'pricing_type_id'     => $validated['pricing_type_id'],
                'min_usage_charge'    => $validated['min_usage_charge'],
                'fixed_rate_per_unit' => $validated['fixed_rate_per_unit'] ?? null,
                'vat'                 => $validated['vat'] ?? 0,
                'effective_date'      => $validated['effective_date'],
                'end_date'            => $validated['end_date'] ?? null,
                'is_active'           => $request->has('is_active') ? 1 : 0,
                'comment'             => $validated['comment'] ?? null,
            ]);

            // 2. Reset Tiers: ลบของเก่า สร้างใหม่ (ง่ายกว่าการมานั่งเช็ค update ทีละ row)
            $meterRateConfig->Ratetiers()->delete();
            $this->saveTiers($meterRateConfig, $validated);
        });

        return redirect()->route('admin.meter_rates.index')
            ->with('success', 'แก้ไขข้อมูลเรียบร้อยแล้ว');
    }

    public function show($id)
    {
        // 1. ดึงข้อมูล (อย่าลืมเช็ค org_id_fk เพื่อความปลอดภัย)
        $meterRateConfig = MeterTypeRateConfig::with(['meterType', 'pricingType', 'Ratetiers'])
        ->where('id', $id)
        ->where('org_id_fk', Auth::user()->org_id_fk)
        ->firstOrFail();
        // 2. ส่งไปที่ View (ต้องสร้างไฟล์ show.blade.php ด้วย)
        return view('superadmin.meter_rates.show', compact('meterRateConfig'));
    }

    public function destroy($id)
    {
        $meterRateConfig = MeterTypeRateConfig::where('id', $id)
            ->where('org_id_fk', Auth::user()->org_id_fk)
            ->firstOrFail();

        $meterRateConfig->delete(); // Cascade ลบ Tiers ใน DB (ถ้าตั้งไว้) หรือ Model Event

        return redirect()->route('admin.meter_rates.index')
            ->with('success', 'ลบข้อมูลเรียบร้อยแล้ว');
    }

    // Helper Function สำหรับบันทึก Tier
    private function saveTiers($rateConfig, $validated)
    {
        $pricingTypeName = TwPricingType::find($validated['pricing_type_id'])->name; // หรือ check ID ตรงๆ ก็ได้เพื่อลด Query
        
        // เช็คว่าเป็น Progressive หรือ Step และมีข้อมูล Tiers ส่งมาไหม
        if (in_array(strtolower($pricingTypeName), ['progressive', 'step']) && isset($validated['tiers'])) {
            // ✅ 1. สร้างตัวนับเริ่มต้นที่ 1
            $tierNumber = 1;
            foreach ($validated['tiers'] as $order => $tierData) {
                // ข้ามข้อมูลว่างๆ (เผื่อส่ง array เปล่ามา)
                if(empty($tierData['min_units']) && empty($tierData['rate_per_unit'])) continue;

                $rateConfig->Ratetiers()->create([
                    'min_units'     => $tierData['min_units'],
                    'max_units'     => $tierData['max_units'] ?? null, // 999999 หรือ null แล้วแต่ logic
                    'rate_per_unit' => $tierData['rate_per_unit'],
                    'tier_order'    => $tierNumber++,
                    'comment'       => $tierData['comment'] ?? null,
                ]);
            }
        }
    }

    private function rules($id = null)
    {
        return [
            'meter_type_id_fk'    => 'required|exists:tw_meter_types,id', // แก้ชื่อ field
            'pricing_type_id'     => 'required|exists:tw_pricing_types,id',
            'min_usage_charge'    => 'required|numeric|min:0',
            'fixed_rate_per_unit' => 'nullable|numeric|min:0',
            'vat'                 => 'nullable|numeric|min:0',
            'effective_date'      => 'required|date',
            'end_date'            => 'nullable|date|after_or_equal:effective_date',
            'is_active'           => 'nullable', // checkbox ส่งมาเป็น "on" หรือไม่ส่งมาเลย
            'comment'             => 'nullable|string',
            
            // Validation for Tiers
            'tiers'               => 'array',
            'tiers.*.min_units'   => 'nullable|integer|min:0', // nullable เผื่อ row ว่างที่ User ไม่กรอก
            'tiers.*.max_units'   => 'nullable|integer',
            'tiers.*.rate_per_unit'=> 'required_with:tiers.*.min_units|numeric|min:0',
        ];
    }
}