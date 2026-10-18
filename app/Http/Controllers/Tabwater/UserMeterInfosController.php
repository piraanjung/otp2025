<?php

namespace App\Http\Controllers\Tabwater;

use App\Http\Controllers\Controller;
use App\Models\Admin\BudgetYear;
use App\Models\Admin\Organization;
use Illuminate\Http\Request;
use App\Models\Admin\Subzone;
use App\Models\Admin\Zone;
use App\Models\Tabwater\MeterTypeRateConfig;
use App\Models\Tabwater\MeterTypeRateTier;
use App\Models\Tabwater\TwInvoice;
use App\Models\Tabwater\InvoicePeriod;
use App\Models\Tabwater\TwMeterInfos;
use App\Models\Tabwater\TwMeterType;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UserMeterInfosController extends Controller
{
    public function index(Request $request)
    {
        $orgId = getAuthOrgId();
        $zones = Zone::where('org_id_fk', $orgId)->orWhereNull('org_id_fk')->get();

        // ดึง User ที่มีมิเตอร์ประปาใน อปท. นี้
        $query = User::where('org_id_fk', $orgId)
            ->whereHas('usermeterinfos', function ($q) use ($orgId) {
                $q->where('org_id_fk', $orgId);
            })
            ->with(['usermeterinfos' => function ($q) use ($orgId) {
                $q->where('org_id_fk', $orgId)->with(['meter_type', 'undertake_zone']);
            }, 'user_zone']);

        // ค้นหาด้วยชื่อ, นามสกุล, เบอร์โทร หรือเลขมิเตอร์
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('firstname', 'like', "%{$search}%")
                    ->orWhere('lastname', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhereHas('usermeterinfos', function ($m) use ($search) {
                        $m->where('meternumber', 'like', "%{$search}%")
                            ->orWhere('factory_no', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('zone_id')) {
            $query->where('zone_id', $request->zone_id);
        }

        $members = $query->orderBy('id', 'desc')->paginate(15);

        return view('tabwater.members.index', compact('members', 'zones'));
    }
    public function get_subzone_members(Request $request)
    {
        $subzoneId = $request->input('subzone_id');

        if (!$subzoneId) {
            return response()->json([
                'code' => 400,
                'message' => 'กรุณาระบุ subzone_id'
            ], 400);
        }

        // ดึงมิเตอร์ พร้อมโหลด Relationship ( user, meter_type, และ invoice รอบปัจจุบันถ้ามี )
        $members = TwMeterInfos::with([
            'user',
            'meter_type',
            'invoice_currrent_inv_period',
        ])
            ->whereHas('invoice_currrent_inv_period')
            ->where('undertake_subzone_id', $subzoneId)
            ->get();

        return response()->json([
            'code' => 200,
            'message' => 'ดึงข้อมูลสำเร็จ',
            'data' => $members
        ]);
    }

    /**
     * Summary of find_subzone
     * @param mixed $zone_id
     * @param mixed $subzone_id
     * @return int
     */
    public function find_subzone($zone_id, $subzone_id = 0)
    {
        $resVal = 0;
        if ($zone_id > 0) {
            $subzones = Subzone::where('zone_id', $zone_id)->get('id');
            if (collect($subzones)->isNotEmpty()) {
                foreach ($subzones as $subzone) {
                    $undertake_subzone_id = TwMeterInfos::where('undertake_subzone_id', $subzone->id)
                        ->get()->first();

                    if (collect($undertake_subzone_id)->isNotEmpty()) {
                        $resVal = 1;
                        break;
                    }
                }
            }
        } else {
            $undertake_subzone_id = TwMeterInfos::where('undertake_subzone_id', $subzone_id)->get()->first();
            if (collect($undertake_subzone_id)->isNotEmpty()) {
                $resVal = 1;
            }
        }

        return $resVal;
    }

    /**
     * Summary of edit_invoices
     * @param mixed $meter_id
     * @return \Illuminate\Contracts\View\View
     */
    public function edit_invoices($meter_id)
    {
        $inv_period = InvoicePeriod::where('status', 'active')->get('id')->first();
        $usermeter_infos = TwMeterInfos::with([
            'invoice' => function ($q) use ($inv_period) {
                $budget_year = BudgetYear::with('invoicePeriod:id,budgetyear_id')->where('status', 'active')
                    ->get()->first();
                $inv_period_lists = collect($budget_year->invoicePeriod)->pluck('id');
                return $q->select('*')->whereIn('inv_period_id_fk', $inv_period_lists);
            }
        ])->where('meter_id', $meter_id)->get()->first();
        return view('tabwater.usermeter_infos.index', compact('usermeter_infos'));
    }

    public function meter_records(Request $request)
    {
        // 1. Validate ข้อมูลที่ส่งมาจาก Client
        $validator = Validator::make($request->all(), [
            'invoice_id' => 'required|exists:tw_invoice,id', // 🟢 เช็กชื่อตารางให้ถูกต้อง (tw_invoices หรือ tw_invoice)
            'meter_id' => 'nullable',
            'current_reading' => 'required|numeric|min:0',
            'inv_period_id_fk' => 'nullable',
        ], [
            'invoice_id.required' => 'กรุณาระบุรหัสใบแจ้งหนี้ (Invoice ID)',
            'invoice_id.exists' => 'ไม่พบข้อมูลใบแจ้งหนี้ในระบบ',
            'current_reading.required' => 'กรุณากรอกเลขมิเตอร์ครั้งนี้',
            'current_reading.numeric' => 'เลขมิเตอร์ต้องเป็นตัวเลขเท่านั้น',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 422,
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $invoiceId = $request->input('invoice_id');
            $currentReading = (int) $request->input('current_reading');
            $recorderId = Auth::id();

            // 2. ค้นหาใบแจ้งหนี้ (TwInvoice) ด้วย invoice_id โดยตรง
            $invoice = TwInvoice::find($invoiceId);

            if (!$invoice) {
                return response()->json([
                    'code' => 404,
                    'status' => 'error',
                    'message' => 'ไม่พบรายการใบแจ้งหนี้ที่ระบุ'
                ], 404);
            }

            // ตรวจสอบสถานะว่าต้องเป็น init เท่านั้น (ป้องกันการกดบันทึกซ้ำ)
            if ($invoice->status !== 'init') {
                return response()->json([
                    'code' => 400,
                    'status' => 'error',
                    'message' => 'ใบแจ้งหนี้นี้ได้รับการบันทึกไปแล้ว'
                ], 400);
            }

            // 3. ดึงข้อมูลมิเตอร์จาก Relationship หรือ Query
            $meter = TwMeterInfos::where('meter_id', $invoice->meter_id_fk)
                ->orWhere('id', $invoice->meter_id_fk)
                ->first();

            if (!$meter) {
                return response()->json([
                    'code' => 404,
                    'status' => 'error',
                    'message' => 'ไม่พบข้อมูลมิเตอร์ที่เชื่อมโยงกับใบแจ้งหนี้นี้'
                ], 404);
            }

            // 4. ตรวจสอบและคำนวณหน่วยน้ำที่ใช้ (water_used)
            $lastMeter = (int) $invoice->lastmeter;

            // ป้องกันกรณีป้อนเลขมิเตอร์ใหม่น้อยกว่าเลขมิเตอร์ครั้งก่อน
            if ($currentReading < $lastMeter) {
                return response()->json([
                    'code' => 422,
                    'status' => 'error',
                    'message' => "เลขมิเตอร์ใหม่ ({$currentReading}) ต้องไม่น้อยกว่าเลขมิเตอร์ครั้งก่อน ({$lastMeter})"
                ], 422);
            }

            $waterUsed = $currentReading - $lastMeter;

            // 5. ดึง คอนฟิกอัตราค่าน้ำ (MeterTypeRateConfig) ตามประเภทมิเตอร์
            $rateConfig = MeterTypeRateConfig::where('meter_type_id_fk', $meter->metertype_id)
                ->where('is_active', 1)
                ->orderBy('id', 'desc')
                ->first();

            $waterCharge = 0;
            $reserveMeter = 0;
            $vatPercent = 0;

            if ($rateConfig) {
                $minUsageCharge = (float) ($rateConfig->min_usage_charge ?? 0);
                $vatPercent = (float) ($rateConfig->vat ?? 0);

                // 🟢 เช็กเงื่อนไขการคิดค่ารักษามิเตอร์ / ค่าบริการขั้นต่ำ
                if (!empty($rateConfig->charge_min_only_zero_use) && $rateConfig->charge_min_only_zero_use == 1) {
                    // คิดเฉพาะเมื่อไม่มีการใช้น้ำ (water_used == 0)
                    $reserveMeter = ($waterUsed == 0) ? $minUsageCharge : 0;
                } else {
                    // คิดรวมไปเลยทุกกรณี
                    $reserveMeter = $minUsageCharge;
                }

                // ----------------------------------------------------
                // คำนวณค่าน้ำ ($waterCharge)
                // ----------------------------------------------------
                if ((int)$rateConfig->pricing_type_id === 1) {
                    // รูปแบบที่ A: อัตราคงที่ (Fixed Rate)
                    $fixedRate = (float) ($rateConfig->fixed_rate_per_unit ?? 0);
                    $waterCharge = $waterUsed * $fixedRate;
                } else {
                    // รูปแบบที่ B: อัตราตามช่วง (Tier-based Pricing)
                    $calcType = (int) ($rateConfig->tier_calculation_type ?? 1);

                    if ($calcType === 2) {
                        // [วิธีที่ 2] Flat Bracket Rate (เหมาเรทตามช่วง)
                        $tier = MeterTypeRateTier::where('meter_type_rate_config_id', $rateConfig->id)
                            ->where('min_units', '<=', $waterUsed)
                            ->where(function ($q) use ($waterUsed) {
                                $q->where('max_units', '>=', $waterUsed)
                                    ->orWhereNull('max_units');
                            })
                            ->first();

                        if ($tier) {
                            $waterCharge = $waterUsed * (float) $tier->rate_per_unit;
                        } else {
                            $firstTier = MeterTypeRateTier::where('meter_type_rate_config_id', $rateConfig->id)
                                ->orderBy('tier_order', 'asc')
                                ->first();
                            $ratePerUnit = $firstTier ? (float) $firstTier->rate_per_unit : 0;
                            $waterCharge = $waterUsed * $ratePerUnit;
                        }
                    } else {
                        // [วิธีที่ 1] Progressive Rate (ขั้นบันไดสะสม)
                        $tiers = MeterTypeRateTier::where('meter_type_rate_config_id', $rateConfig->id)
                            ->orderBy('tier_order', 'asc')
                            ->get();

                        $remainingUnits = $waterUsed;

                        foreach ($tiers as $tier) {
                            if ($remainingUnits <= 0) break;

                            $minUnits = (int) $tier->min_units;
                            $maxUnits = $tier->max_units !== null ? (int) $tier->max_units : PHP_INT_MAX;
                            $ratePerUnit = (float) $tier->rate_per_unit;

                            $tierCapacity = ($minUnits === 0) ? ($maxUnits - $minUnits) : ($maxUnits - $minUnits + 1);
                            $unitsInTier = min($remainingUnits, $tierCapacity);

                            $waterCharge += ($unitsInTier * $ratePerUnit);
                            $remainingUnits -= $unitsInTier;
                        }
                    }
                }
            }

            // 6. คำนวณภาษี VAT และยอดเงินสุทธิ
            $subtotal = $waterCharge + $reserveMeter;
            $vatAmount = round(($subtotal * $vatPercent) / 100, 2);
            $totalPaid = $subtotal + $vatAmount;

            // 7. UPDATE ข้อมูลลงใน TwInvoice
            $invoice->currentmeter  = $currentReading;
            $invoice->water_used    = $waterUsed;
            $invoice->reserve_meter = $reserveMeter;
            $invoice->vat           = $vatAmount;
            $invoice->totalpaid     = $totalPaid;
            $invoice->paid          = $waterCharge;
            $invoice->recorder_id   = $recorderId;
            $invoice->status        = 'invoice';
            $invoice->updated_at    = now();
            $invoice->save();

            // 8. อัปเดตเลขมิเตอร์ล่าสุดกลับไปยัง TwMeterInfos
            $meter->last_meter_recording = $currentReading;
            $meter->recorder_id = $recorderId;
            $meter->save();

            DB::commit();

            $oweLists = $invoice->where('status', 'owe')->get();
            $owePaidSum = 0;
            $owes = [];
            foreach ($oweLists as $owe) {
                $owePaidSum += $owe->totalpaid;
                $owes[] = ['invoice_period' => $owe->invoice_period->inv_p_name_int, 'totalpaid' => $owe->totalpaid];
            }
            $owes[] = ['invoice_period' => '09-69', 'totalpaid' => 100];
            $owes[] = ['invoice_period' => '08-69', 'totalpaid' => 200];
            $owePaidSum = 300;

            return response()->json([
                'code' => 200,
                'status' => 'success',
                'message' => 'บันทึกข้อมูลและออกใบแจ้งหนี้เรียบร้อยแล้ว',
                'data' => [
                    'user'              => [
                        'name'    => $meter->user->prefix . "" . $meter->user->firstname . " " . $meter->user->lastname,
                        'address' => $meter->meter_address,
                        'zone'    => $meter->undertake_zone->zone_name,
                        'subname' => $meter->submeter_name,
                    ],
                    'owes'              => $owes,
                    'org'               => [
                        'name'      => $meter->organization->orgType->name . "" . $meter->organization->org_name,
                        'address'   => $meter->organization->org_address,
                        'zone'      => $meter->organization->zones->zone_name,
                        'tambon'    => $meter->organization->tambons->tambon_name,
                        'district'  => $meter->organization->districts->district_name,
                        'province'  => $meter->organization->provinces->province_name,
                        'phone'     => $meter->organization->org_phone,
                        'zipcode'   => $meter->organization->org_zipcode,
                        'logo_img'  => $meter->organization->org_logo_img
                    ],
                    'owe_paid_sum'      => $owePaidSum,
                    'invoice_id'        => $invoice->id,
                    'inv_period_name'   => $invoice->invoice_period->inv_p_name,
                    'meternumber'       => $meter->meternumber,
                    'meter_id'          => $meter->meter_id ?? $meter->id,
                    'lastmeter'         => $lastMeter,
                    'currentmeter'      => $currentReading,
                    'water_used'        => $waterUsed,
                    'paid'              => $waterCharge,
                    'reserve_meter'     => $reserveMeter,
                    'vat'               => $vatAmount,
                    'totalpaid'         => $totalPaid,
                    'status'            => $invoice->status,
                    'net_paid'          => number_format($totalPaid + $owePaidSum, 2)
                ]
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'code' => 500,
                'status' => 'error',
                'message' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $e->getMessage()
            ], 500);
        }
    }



    // 1. หน้าแสดงฟอร์มดึงรายชื่อ Master Users ที่ยังไม่มีมิเตอร์ประปา
    public function create(Request $request)
    {
        $orgId = getAuthOrgId();
        $zones = Zone::where('org_id_fk', $orgId)->orWhereNull('org_id_fk')->get();
        $meterTypes = TwMeterType::all();

        // ดึง Master Users ทั้งหมดใน Org (ผู้ใช้ 1 คนสามารถลงทะเบียนมิเตอร์เพิ่มได้หลายครั้ง)
        $query = User::where('org_id_fk', $orgId);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('firstname', 'like', "%{$search}%")
                    ->orWhere('lastname', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('id_card', 'like', "%{$search}%");
            });
        }

        if ($request->filled('zone_id')) {
            $query->where('zone_id', $request->zone_id);
        }

        $availableUsers = $query->paginate(20);

        return view('tabwater.members.create', compact('availableUsers', 'zones', 'meterTypes'));
    }

    // 2. ฟังก์ชันรับค่าเพิ่มผู้ใช้น้ำแบบ Bulk บันทึกลงตาราง tw_meter_infos
    public function bulkStore(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'factory_no' => 'nullable|array',
            'last_meter_recording' => 'nullable|array',
            'metertype_id' => 'nullable|array',
            'undertake_zone_id' => 'nullable|array',
        ]);

        $orgId = getAuthOrgId();

        DB::beginTransaction();
        try {
            // หาเลข meternumber สูงสุดปัจจุบันของ อปท. นี้
            $maxMeterNo = TwMeterInfos::where('org_id_fk', $orgId)->max('meternumber') ?? 0;

            foreach ($request->user_ids as $userId) {
                $user = User::find($userId);
                if (!$user) continue;

                // บวกเพิ่มทีละ 1 สำหรับสมาชิกแต่ละคนที่ถูกติ๊กเลือก
                $maxMeterNo++;

                TwMeterInfos::create([
                    'org_id_fk'            => $orgId,
                    'user_id'              => $userId,
                    'meternumber'          => $maxMeterNo, // รันเลข 1, 2, 3... ถัดไปให้อัตโนมัติ
                    'factory_no'           => $request->factory_no[$userId] ?? null,
                    'last_meter_recording' => $request->last_meter_recording[$userId] ?? 0,
                    'meter_address'        => $user->address,
                    'undertake_zone_id'    => $request->undertake_zone_id[$userId] ?? $user->zone_id,
                    'undertake_subzone_id' => $user->subzone_id ?? null,
                    'metertype_id'         => $request->metertype_id[$userId] ?? 1,
                    'acceptance_date'      => now(),
                    'status'               => 'active',
                    'recorder_id'          => Auth::id(),
                ]);

                // ผูก Role สมาชิกประปา
                if (!$user->hasRole('Tabwater User')) {
                    $user->assignRole('Tabwater User');
                }
            }

            DB::commit();
            return redirect()->route('tabwater.members.index')
                ->with('success', 'ลงทะเบียนผู้ใช้น้ำประปาเรียบร้อยแล้ว');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $orgId = getAuthOrgId();
        $meter = TwMeterInfos::where('org_id_fk', $orgId)->findOrFail($id);
        $userId = $meter->user_id;

        DB::beginTransaction();
        try {
            // 1. ลบมิเตอร์ประปา
            $meter->delete();

            // 2. เช็กว่า User นี้ยังมีมิเตอร์ประปาอื่นใน อปท. นี้เหลืออยู่อีกไหม
            $remainingMetersCount = TwMeterInfos::where('org_id_fk', $orgId)
                ->where('user_id', $userId)
                ->count();

            // 3. ถ้าไม่เหลือมิเตอร์เลย ให้ถอน Role "Tabwater User" ออกจากบัญชี User
            if ($remainingMetersCount === 0) {
                $user = User::find($userId);
                if ($user && $user->hasRole('Tabwater User')) {
                    $user->removeRole('Tabwater User');
                }
            }

            DB::commit();
            return redirect()->route('tabwater.members.index')
                ->with('success', 'ยกเลิกและลบรายการมิเตอร์ประปาเรียบร้อยแล้ว');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'เกิดข้อผิดพลาดในการลบรายการ: ' . $e->getMessage());
        }
    }

    public function addMeter($userId)
    {
        $orgId = getAuthOrgId();
        $user = User::where('org_id_fk', $orgId)->findOrFail($userId);
        $zones = Zone::where('org_id_fk', $orgId)->orWhereNull('org_id_fk')->get();
        $maxMeterNo = TwMeterInfos::where('org_id_fk', $orgId)->max('meternumber') ?? 0;
        $meterTypes = TwMeterType::all();
        $meterNo = $maxMeterNo + 1;
        return view('tabwater.members.add_meter', compact('user', 'zones', 'meterTypes', 'meterNo'));
    }

    public function storeAdditionalMeter(Request $request, $userId)
    {
        $request->validate([
            'factory_no' => 'nullable|string',
            'last_meter_recording' => 'required|numeric|min:0',
            'metertype_id' => 'required|exists:tw_meter_types,id',
            'undertake_zone_id' => 'required|exists:zones,id',
            'meter_address' => 'nullable|string',
            'submeter_name' => 'nullable|string',

        ]);

        $orgId = getAuthOrgId();
        $user = User::where('org_id_fk', $orgId)->findOrFail($userId);

        DB::beginTransaction();
        try {
            // Auto Run เลข meternumber ถัดไป
            $maxMeterNo = TwMeterInfos::where('org_id_fk', $orgId)->max('meternumber') ?? 0;
            $maxMeterNo++;

            TwMeterInfos::create([
                'org_id_fk'            => $orgId,
                'user_id'              => $user->id,
                'meternumber'          => $maxMeterNo,
                'factory_no'           => $request->factory_no,
                'last_meter_recording' => $request->last_meter_recording ?? 0,
                'meter_address'        => $request->meter_address ?? $user->address,
                'undertake_zone_id'    => $request->undertake_zone_id,
                'undertake_subzone_id' => $user->subzone_id ?? null,
                'submeter_name'         => $request->submeter_name ?? null,
                'metertype_id'         => $request->metertype_id,
                'acceptance_date'      => now(),
                'status'               => 'active',
                'recorder_id'          => Auth::id(),
            ]);

            DB::commit();
            return redirect()->route('tabwater.members.index')
                ->with('success', "เพิ่มมิเตอร์ใหม่ให้กับคุณ {$user->firstname} เรียบร้อยแล้ว (เลขมิเตอร์: TW" . sprintf('%05d', $maxMeterNo) . ")");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $e->getMessage());
        }
    }

    public function edit($id)
{
    $orgId = getAuthOrgId();
    // ดึงข้อมูลมิเตอร์พร้อมข้อมูล User และสัมพันธ์ที่เกี่ยวข้อง
    $meter = TwMeterInfos::with(['user', 'meter_type', 'undertake_zone'])
        ->where('org_id_fk', $orgId)
        ->findOrFail($id);

    $zones = Zone::where('org_id_fk', $orgId)->orWhereNull('org_id_fk')->get();
    $meterTypes = TwMeterType::all();

    return view('tabwater.members.edit', compact('meter', 'zones', 'meterTypes'));
}

public function update(Request $request, $id)
{
    $request->validate([
        'factory_no' => 'nullable|string',
        'last_meter_recording' => 'required|numeric|min:0',
        'metertype_id' => 'required|exists:tw_meter_types,id',
        'undertake_zone_id' => 'required|exists:zones,id',
        'meter_address' => 'nullable|string',
        'status' => 'required|in:active,inactive',
    ]);

    $orgId = getAuthOrgId();
    $meter = TwMeterInfos::where('org_id_fk', $orgId)->findOrFail($id);

    DB::beginTransaction();
    try {
        $meter->update([
            'factory_no'           => $request->factory_no,
            'last_meter_recording' => $request->last_meter_recording,
            'metertype_id'         => $request->metertype_id,
            'undertake_zone_id'    => $request->undertake_zone_id,
            'meter_address'        => $request->meter_address,
            'status'               => $request->status,
        ]);

        DB::commit();
        return redirect()->route('tabwater.members.index')
            ->with('success', 'อัปเดตข้อมูลมิเตอร์เรียบร้อยแล้ว');

    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()
            ->with('error', 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $e->getMessage());
    }
}
}
