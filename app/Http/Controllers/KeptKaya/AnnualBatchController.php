<?php

namespace App\Http\Controllers\KeptKaya;

use App\Http\Controllers\Controller;
use App\Models\KeptKaya\WasteBinSubscription;
use App\Models\KeptKaya\WasteBin;
use App\Models\KeptKaya\WasteBinPayratePerMonth;
use App\Models\Admin\BudgetYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnnualBatchController extends Controller
{
    // หน้า Index: แสดงรายการปีที่ "สร้างหนี้ไว้แล้ว"
    public function index()
    {
        // Group ข้อมูลตามปีงบประมาณ เพื่อสรุปยอด
        $batches = WasteBinSubscription::select(
                'fiscal_year',
                DB::raw('count(*) as total_bins'),
                DB::raw('sum(annual_fee) as total_expected_revenue'), // ยอดหนี้รวม
                DB::raw('sum(total_paid_amt) as total_collected'),    // ยอดเก็บได้จริง
                DB::raw('MAX(created_at) as created_at')
            )
            ->groupBy('fiscal_year')
            ->orderBy('fiscal_year', 'desc')
            ->get();

        // ดึงปีงบประมาณทั้งหมดมาใส่ Dropdown ใน Modal สร้างใหม่
        $budgetYears = BudgetYear::where('status', 'active') // สมมติว่ามี status
                        ->orderBy('budgetyear_name', 'desc')
                        ->get();

        return view('keptkayas.annual_batch.index', compact('batches', 'budgetYears'));
    }

    // ฟังก์ชันสร้างข้อมูล (Store) - เหมือนที่คุยกันรอบที่แล้ว แต่ย้ายมาที่นี่
    public function store(Request $request)
    {
        $request->validate([
            'fiscal_year' => 'required|integer',
        ]);
        
        $targetFiscalYear = $request->fiscal_year;

        // 1. เช็คว่าปีนี้เคยสร้างหรือยัง
        if (WasteBinSubscription::where('fiscal_year', $targetFiscalYear)->exists()) {
            return back()->with('error', "ข้อมูลประจำปี $targetFiscalYear ถูกสร้างไปแล้ว");
        }

        DB::beginTransaction();
        try {
            // 2. ดึงถังขยะที่ต้องเก็บเงิน (Active)
          $activeBins = WasteBin::where('is_active_for_annual_collection', true)
                ->where('status', 'active')
                ->with('user.user_group') 
                ->get();

            if ($activeBins->isEmpty()) {
                return back()->with('error', "ไม่พบถังขยะที่เปิดใช้บริการรายปี");
            }

            $count = 0;
            foreach ($activeBins as $bin) {
                // 3. หาเรทราคา
                $userGroupId = $bin->bin_type; 

    // เช็คความถูกต้อง (เผื่อ bin_type เป็น null)
    if (empty($userGroupId)) {
        // อาจจะ log error หรือข้ามไป
        continue; 
    }
    // 2. วิ่งไปหาราคา (Logic เดิม แต่ตัวแปรต้นทางเปลี่ยน)
    // หาเรทราคาที่ user_group ตรงกับ bin_type และ ตรงกับปีงบประมาณ
    $rateCard = WasteBinPayratePerMonth::where('kp_usergroup_idfk', $userGroupId)
        ->whereHas('budgetyear', function($q) use ($targetFiscalYear) {
            $q->where('budgetyear_name', $targetFiscalYear);
        })
        ->first();

    // ถ้าไม่เจอราคา ให้ข้าม
    if (!$rateCard) continue;

                $annualFee = $rateCard->payrate_permonth * 12;

                // 4. Create
                WasteBinSubscription::create([
                    'waste_bin_id' => $bin->id,
                    'fiscal_year' => $targetFiscalYear,
                    'payrate_permonth_id_fk' => $rateCard->id,
                    'annual_fee' => $annualFee,
                    'month_fee' => $rateCard->payrate_permonth,
                    'total_paid_amt' => 0,
                    'status' => 'pending',
                ]);
                $count++;
            }

            DB::commit();
            return back()->with('success', "สร้างรอบจัดเก็บปี $targetFiscalYear สำเร็จ ($count รายการ)");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }

    // ฟังก์ชันลบ/Rollback ทั้งปี (ใช้กรณีสร้างผิด และยังไม่มีใครจ่ายเงิน)
    public function destroy($fiscal_year)
    {
        // 1. เช็คก่อนว่ามีการจ่ายเงินไปบ้างหรือยัง
        $hasPayment = WasteBinSubscription::where('fiscal_year', $fiscal_year)
            ->where('total_paid_amt', '>', 0)
            ->exists();

        if ($hasPayment) {
            return back()->with('error', "ไม่สามารถลบปี $fiscal_year ได้ เนื่องจากมีการรับชำระเงินไปบางส่วนแล้ว");
        }

        // 2. ถ้ายังไม่มีใครจ่าย ลบได้เลย
        WasteBinSubscription::where('fiscal_year', $fiscal_year)->delete();

        return back()->with('success', "ลบข้อมูลรอบจัดเก็บปี $fiscal_year เรียบร้อยแล้ว");
    }
}