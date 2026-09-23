<?php

namespace App\Http\Controllers\Tabwater;

use App\Http\Controllers\Controller;
use App\Models\Admin\BudgetYear;
use Illuminate\Http\Request;
use App\Models\Admin\Subzone;
use App\Models\Tabwater\MeterTypeRateConfig;
use App\Models\Tabwater\MeterTypeRateTier;
use App\Models\Tabwater\TwInvoice;
use App\Models\Tabwater\TwInvoicePeriod;
use App\Models\Tabwater\TwMeterInfos;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UserMeterInfosController extends Controller
{
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
        $inv_period = TwInvoicePeriod::where('status', 'active')->get('id')->first();
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
        $invoice->currentmeter = $currentReading;
        $invoice->water_used = $waterUsed;
        $invoice->reserve_meter = $reserveMeter;
        $invoice->vat = $vatAmount;
        $invoice->totalpaid = $totalPaid;
        $invoice->recorder_id = $recorderId;
        $invoice->status = 'invoice'; // เปลี่ยนสถานะเป็น invoice
        $invoice->updated_at = now();
        $invoice->save();

        // 8. อัปเดตเลขมิเตอร์ล่าสุดกลับไปยัง TwMeterInfos
        $meter->last_meter_recording = $currentReading;
        $meter->recorder_id = $recorderId;
        $meter->save();

        DB::commit();

        return response()->json([
            'code' => 200,
            'status' => 'success',
            'message' => 'บันทึกข้อมูลและออกใบแจ้งหนี้เรียบร้อยแล้ว',
            'data' => [
                'invoice_id' => $invoice->id,
                'meter_id' => $meter->meter_id ?? $meter->id,
                'lastmeter' => $lastMeter,
                'currentmeter' => $currentReading,
                'water_used' => $waterUsed,
                'reserve_meter' => $reserveMeter,
                'vat' => $vatAmount,
                'totalpaid' => $totalPaid,
                'status' => $invoice->status
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
}
