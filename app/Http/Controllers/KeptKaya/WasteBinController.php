<?php

namespace App\Http\Controllers\KeptKaya;

use App\Http\Controllers\Controller;
use App\Http\Controllers\FunctionsController;
use App\Models\Admin\Organization;
use App\Models\Keptkaya\KpUserGroup;
use App\Models\Keptkaya\KpUsergroupPayratePerMonth;
use App\Models\KeptKaya\WasteBinPayratePerMonth;
use App\Models\User;
use App\Models\KeptKaya\WasteBinSubscription; // Import WasteBinSubscription model
use App\Models\KeptKaya\WasteBin;
use App\Services\UserWasteStatusService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class WasteBinController extends Controller
{
    protected $wasteStatusService;

    public function __construct(UserWasteStatusService $wasteStatusService)
    {
        $this->wasteStatusService = $wasteStatusService;
    }

    /**
     * Display a listing of the waste bins for a specific user.
     *
     * @param  \App\Models\User  $w_user
     * @return \Illuminate\Http\Response
     */
    public function index(User $w_user)
    {
        $wasteBins = $w_user->wasteBins()->paginate(10);
        return view('keptkayas.w.waste_bins.index', compact('w_user', 'wasteBins'));
    }

    /**
     * Show the form for creating a new waste bin.
     *
     * @param  \App\Models\User  $w_user
     * @return \Illuminate\Http\Response
     */
    public function create(User $w_user)
    {
        $user_groups = KpUserGroup::all();
        $func = new FunctionsController();
        $bin_code = $func->wastBinCode();
        $orgInfos = Organization::getOrgName($w_user->org_id_fk);

        return view('keptkayas.w.waste_bins.create', compact('w_user', 'user_groups', 'bin_code'));
    }

    public function store(Request $request, User $w_user)
{
    // 1. Validation (เหมือนเดิม)
    $request->validate([
        'bin_code' => 'nullable|string|unique:kp_waste_bins,bin_code|max:255',
        'bin_type' => 'required|string|max:255',
        'user_group' => 'required',
        'location_description' => 'nullable|string|max:255',
        'latitude' => 'nullable|numeric|between:-90,90',
        'longitude' => 'nullable|numeric|between:-180,180',
        'status' => ['required', Rule::in(['active', 'inactive', 'damaged', 'removed'])],
        'is_active_for_annual_collection' => 'boolean',
    ]);

    // 2. Create WasteBin (เหมือนเดิม)
    $wasteBin = $w_user->wasteBins()->create([
        'bin_code' => $request->bin_code,
        'bin_type' => $request->user_group,
        'location_description' => $request->location_description,
        'latitude' => $request->latitude,
        'longitude' => $request->longitude,
        'status' => $request->status,
        'is_active_for_annual_collection' => $request->has('is_active_for_annual_collection'),
        'created_at' => now(), // ใช้ now() ให้ได้เวลาปัจจุบันเป๊ะๆ
        'updated_at' => now(),
    ]);

    // 3. Create Subscription (จุดที่ต้องแก้ logic การคำนวณเงิน)
    if ($wasteBin->is_active_for_annual_collection) {
        
        // 3.1 ดึงปีงบประมาณ (สมมติว่าเป็น พ.ศ. 2569)
        $fiscalYear = WasteBinSubscription::calculateFiscalYear(); 
        
        // แปลงเป็น ค.ศ. เพื่อคำนวณวัน (เช่น 2569 -> 2026)
        $fiscalYearAD = ($fiscalYear > 2500) ? $fiscalYear - 543 : $fiscalYear;

        // 3.2 ดึงเรทราคาต่อเดือน
        $payratePerMonth = WasteBinPayratePerMonth::where('kp_usergroup_idfk', $request->get('user_group'))
            ->where('status', 'active')
            ->first(); // ใช้ first() ก็พอ ไม่ต้อง get()->first()

        if ($payratePerMonth) {
            $monthlyFee = $payratePerMonth->payrate_permonth;

            // --- LOGIC คำนวณยอดเงินตามจริง (Pro-rate) ---
            
            // วันที่สมัคร (วันนี้)
            $startDate = Carbon::now(); 
            
            // วันสิ้นสุดปีงบประมาณ (30 กันยายน ของปีงบนั้น)
            $endDate = Carbon::create($fiscalYearAD, 9, 30)->endOfDay();

            // กรณีพิเศษ: ถ้าสมัครช่วงคาบเกี่ยว (เช่น สมัคร ต.ค. แต่นับเป็นปีงบหน้า)
            // ให้ตรวจสอบว่า วันนี้ เลยวันสิ้นปีงบไปหรือยัง ถ้าเลยแล้ว แสดงว่าเป็นรอบปีถัดไป
            if ($startDate->gt($endDate)) {
                 $endDate->addYear(); // บวกเพิ่มไปอีกปี
            }

            // นับจำนวนเดือนที่เหลือ (รวมเดือนปัจจุบันด้วย)
            // เช่น สมัคร ม.ค. ถึง ก.ย. = 9 เดือน
            // diffInMonths จะนับจำนวนเดือนเต็ม เราใช้ floatDiff หรือคำนวณเองเพื่อให้ครอบคลุม
            // แต่วิธีที่ง่ายที่สุดสำหรับระบบรอบบิลคือดูที่เดือน
            
            // วิธีนับแบบบ้านๆ แต่ชัวร์สุดสำหรับราชการ (นับนิ้ว):
            // Loop จากเดือนปัจจุบัน ไปจนถึงเดือน 9 (กันยายน)
            $remainingMonths = 0;
            $checkDate = $startDate->copy()->startOfMonth();
            $targetDate = $endDate->copy()->startOfMonth();

            while ($checkDate->lte($targetDate)) {
                $remainingMonths++;
                $checkDate->addMonth();
            }

            // คำนวณยอดรายปีตามจริง (เช่น 50 บาท x 9 เดือน = 450)
            $annualFee = $monthlyFee * $remainingMonths;

            // บันทึกข้อมูล
            WasteBinSubscription::firstOrCreate(
                [
                    'waste_bin_id' => $wasteBin->id,
                    'fiscal_year' => $fiscalYear, // ระบุปีด้วย เผื่อมีขยะเดิมแต่ปีใหม่
                ],
                [
                    'payrate_permonth_id_fk' => $payratePerMonth->id,
                    'fiscal_year' => $fiscalYear,
                    'annual_fee' => $annualFee, // ยอดที่คำนวณใหม่
                    'month_fee' => $monthlyFee,
                    'total_paid_amt' => 0,
                    'status' => 'pending',
                    'created_at' => now(), // ใช้ now() ให้ได้เวลาปัจจุบันเป๊ะๆ
                    'updated_at' => now(),
                ]
            );
        }
    }

    // 4. Update Status (เหมือนเดิม)
    $this->wasteStatusService->updateOverallUserWasteStatus($w_user);

    return redirect()->route('keptkayas.waste_bins.index', $w_user->id)
        ->with('success', 'เพิ่มถังขยะเรียบร้อยแล้ว!');
}


    public function show(WasteBin $wasteBin)
    {
        return view('keptkayas.waste_bins.show', compact('wasteBin'));
    }

    public function edit(WasteBin $wasteBin)
    {
        return view('keptkayas.waste_bins.edit', compact('wasteBin'));
    }


    public function update(Request $request, WasteBin $wasteBin)
    {
        $request->validate([
            'bin_code' => ['nullable', 'string', 'max:255', Rule::unique('waste_bins')->ignore($wasteBin->id)],
            'bin_type' => 'required|string|max:255',
            'location_description' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'status' => ['required', Rule::in(['active', 'inactive', 'damaged', 'removed'])],
            'is_active_for_annual_collection' => 'boolean',
        ]);

        // DB::transaction(function () use ($request, $wasteBin) {
        $oldIsActiveForAnnualCollection = $wasteBin->is_active_for_annual_collection;
        $newIsActiveForAnnualCollection = $request->has('is_active_for_annual_collection');

        $data = $request->all();
        $data['is_active_for_annual_collection'] = $newIsActiveForAnnualCollection;

        $wasteBin->update($data); // Update waste bin data

        // If status changed to active for annual collection, create/ensure subscription
        if (!$oldIsActiveForAnnualCollection && $newIsActiveForAnnualCollection) {
            $fiscalYear = WasteBinSubscription::calculateFiscalYear();
            $annualFee = 1200.00; // Default annual fee
            $monthlyFee = $annualFee / 12;

            WasteBinSubscription::firstOrCreate(
                [
                    'waste_bin_id' => $wasteBin->id,
                    'fiscal_year' => $fiscalYear,
                ],
                [
                    'annual_fee' => $annualFee,
                    'monthly_fee' => $monthlyFee,
                    'total_paid_amount' => 0,
                    'status' => 'pending',
                ]
            );
        }
        // If status changed from active to inactive, you might want to update the subscription status to cancelled/inactive
        // Or handle this logic in a separate process. For now, we only create on activation.

        // Call service to update overall user waste status (waste_preference)
        $this->wasteStatusService->updateWasteBinAndUserStatus($wasteBin, $data);
        // });

        return redirect()->route('keptkayas.waste_bins.index', $wasteBin->user->id)
            ->with('success', 'อัปเดตถังขยะเรียบร้อยแล้ว!');
    }


    public function destroy(WasteBin $wasteBin)
    {
        $w_user = $wasteBin->user; // Get user before deleting bin

        DB::transaction(function () use ($wasteBin, $w_user) {
            $wasteBin->delete();
            // Call service to update overall user waste status (waste_preference)
            $this->wasteStatusService->updateOverallUserWasteStatus($w_user);
        });

        return redirect()->route('keptkayas.waste_bins.index', $w_user->id)
            ->with('success', 'ลบถังขยะเรียบร้อยแล้ว!');
    }

    public function viewmap()
    {
        $orgInfos = Organization::getOrgName(Auth::user()->org_id_fk);
        return view('keptkayas.dashboard_map', compact('orgInfos'));
    }

    public function map()
    {
        $bins = WasteBin::with([
            'user' => function ($q) {
                return $q->select('id', 'firstname', 'lastname', 'address', 'zone_id', 'subzone_id');
            },
            'user.user_zone' => function ($q) {
                return $q->select('id', 'zone_name');
            },
            'user.user_subzone' => function ($q) {
                return $q->select('id', 'subzone_name');
            },
        ])->get(['id', 'user_id',  'bin_code', 'latitude', 'longitude', 'status', 'bin_type']);
        return response()->json($bins);
    }
}
