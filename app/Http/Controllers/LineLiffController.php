<?php

namespace App\Http\Controllers;

use App\Models\Admin\Organization;
use App\Models\Admin\Province;
use App\Models\AnnualTrash\AnnualTrashPayratePerMonth;
use App\Models\AnnualTrash\AnnualTrashSubscription;
use App\Models\FoodWaste\CompostBatches;
use App\Models\FoodWaste\FoodWasteAccount;
use App\Models\FoodWaste\FoodWasteIssueReport;
use App\Models\FoodWaste\FoodWasteIssueType;
use App\Models\KeptKaya\KPAccounts;
use App\Models\KeptKaya\KpUserWastePreference;
use App\Models\FoodWaste\MealLog;
use App\Models\Tabwater\SequenceNumber;
use App\Models\User;
use App\Models\FoodWaste\FoodWasteLog;
use App\Models\FoodWaste\FoodWasteUserPreference;
use App\Models\KeptKaya\KpPurchaseTransactionDetail;
use App\Models\RecycleBankAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class   LineLiffController extends Controller
{
    public function index()
    {

        $provinces = Province::all();
        $orgs = Organization::with('provinces', 'districts', 'tambons', 'orgType')
            ->get(['id', 'org_type_id', 'org_name', 'org_tambon_id_fk', 'org_district_id_fk', 'org_province_id_fk']);
        return view('lineliff.index', compact('provinces', 'orgs'));
    }

    public function dashboard($pref_id, $org_id)
    {
        // 1. ดึงข้อมูล User และ Login
        $kpPref = KpUserWastePreference::find($pref_id);
        $userId = $kpPref->user_id;
        $user = User::findOrFail($userId);
        Auth::login($user);

        // 2. ดึงข้อมูลบัญชี (New Schema)
        $recycleAcc = RecycleBankAccount::where('user_pref_id', $kpPref->id)->first();
        $foodWasteAcc = FoodWasteAccount::where('user_id', $userId)->first();
        $annualTrash = AnnualTrashSubscription::where('user_id', $userId)->first();

        // --- ส่วนที่ 3 (แก้ไขให้มีตัวแปรครบตามที่ compact ต้องการ) ---
        // 1. น้ำหนักขยะเปียกสะสม (ดึงจากบัญชีโดยตรงตาม Logic เดิมที่คุณอยากได้)
        $totalWasteWeight = $foodWasteAcc ? $foodWasteAcc->total_weight_kg : 0;

        // 2. คาร์บอนขยะเปียก
        $totalFoodWasteCarbon = FoodWasteLog::where('user_id', $userId)->sum('carbon_saved_kg');

        // 3. คาร์บอนขยะรีไซเคิล
        $totalRecycleCarbon = KpPurchaseTransactionDetail::whereHas('transaction.userWastePreference', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        })
            ->whereHas('transaction', function ($q) {
                $q->where('status', 1);
            })
            ->sum('carbon_saved');

        // 4. รวมคาร์บอนทั้งหมด (ตัวแปรหลักที่ใช้โชว์ใน Dashboard)
        $totalCo2Saved = $totalFoodWasteCarbon + $totalRecycleCarbon;

        // 5. ตัวแปรสำรอง (ถ้าใน Blade ยังมีการใช้ชื่อ $totalCarbonSaved อยู่)
        $totalCarbonSaved = $totalCo2Saved;

        // 4. จัดการข้อมูล Batch (ล็อตปุ๋ยปัจจุบัน)
        $activeBatch = CompostBatches::where('user_id', $userId)
            ->where('status', 'filling')
            ->latest()
            ->first();

        if ($activeBatch) {
            $days = (int) now()->diffInDays($activeBatch->start_date);
            $activeBatch->days_passed = ($days == 0) ? 1 : $days;
            $activeBatch->total_weight = FoodWasteLog::where('batch_id', $activeBatch->id)->sum('weight_kg');

            $lastLog = FoodWasteLog::where('batch_id', $activeBatch->id)->latest()->first();
            $activeBatch->temp_status = $lastLog ? $lastLog->temperature_feel : 'ยังไม่มีข้อมูล';
        }

        // 5. ข้อมูลกราฟแคลอรี่ (MealLog)
        $weeklyStats = MealLog::where('user_id', $userId)
            ->where('created_at', '>=', now()->subDays(6))
            ->selectRaw('DATE(created_at) as date, SUM(total_calories) as daily_calories')
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get();

        $chartLabels = $weeklyStats->pluck('date')->map(fn($date) => \Carbon\Carbon::parse($date)->format('d/m'))->toArray();
        $chartData = $weeklyStats->pluck('daily_calories')->toArray();

        // 6. เป้าหมายแคลอรี่ (TDEE) - ใส่ค่า Default เพื่อให้เส้น Red Line ขึ้นเสมอ
        $targetCalories = $user->calculateTDEE() ?: 2000;
        $todayCalories = MealLog::where('user_id', $userId)->whereDate('created_at', now())->sum('total_calories');

        // 7. 🌟 ส่วนสำคัญ: แต้มและเงิน
        // ดึงแต้มจาก FoodWasteAccount (ขยะเปียก)
        $foodWasteTotalPoints = $foodWasteAcc ? $foodWasteAcc->points_balance : 0;

        // ดึงเงินจาก RecycleBankAccount (เงินจากการขายขยะรีไซเคิล)
        $recycleTotalBalance = $recycleAcc ? $recycleAcc->balance : 0.00;
        $recycleTotalPoints = $recycleAcc ? $recycleAcc->points : 0.00;

        // 8. ข้อมูลอื่นๆ
        $qrcode = QrCode::size(300)->generate("USER-" . $userId);
        $myIssues = FoodWasteIssueReport::where('user_id', $userId)->latest()->get();
        $pendingIssuesCount = FoodWasteIssueReport::where('user_id', $userId)->where('status', '!=', 'resolved')->count();
        $issueTypes = FoodWasteIssueType::where('is_active', 1)->get();
        return view('lineliff.dashboard', compact(
            'user',
            'totalWasteWeight',
            'totalCarbonSaved',
            'activeBatch',
            'chartLabels',
            'chartData',
            'targetCalories',
            'todayCalories',
            'foodWasteTotalPoints',    // 🌟 แต้มขยะเปียก
            'recycleTotalBalance',   // 🌟 ยอดเงินคงเหลือ
            'recycleTotalPoints',
            'qrcode',
            'pendingIssuesCount',
            'annualTrash',
            'myIssues',
            'issueTypes',
            'totalCo2Saved'
        ));
    }


    public function handleLineLogin(Request $request)
    {
        $validatedData = $request->validate([
            'userId' => 'required|string',
            'displayName' => 'required|string',
            'pictureUrl' => 'nullable|string',
        ]);

        $lineUserId = $validatedData['userId'];

        // ค้นหา User จาก line_user_id
        $user = User::where('line_user_id', $lineUserId)->first();

        if ($user) {
            // ถ้าพบ: User ได้ลงทะเบียนไว้แล้ว
            Log::info("User with LINE ID {$lineUserId} logged in.");
            return response()->json([
                'status' => 'success',
                'message' => 'User logged in successfully.',
                'action' => 'login'
            ]);
        } else {
            // ถ้าไม่พบ: ลงทะเบียน User ใหม่
            $newUser = User::create([
                'name' => $validatedData['displayName'],
                'line_user_id' => $lineUserId,
                'picture_url' => $validatedData['pictureUrl'],
                // สามารถเพิ่มข้อมูลอื่นๆ ได้ตามต้องการ
            ]);

            Log::info("New user with LINE ID {$lineUserId} registered.");
            return response()->json([
                'status' => 'success',
                'message' => 'New user registered successfully.',
                'action' => 'register'
            ], 201);
        }
    }


    public function update_user_by_phone(Request $request)
    {
        $_user = User::with('wastePreference')->where('phone', $request->phoneNum)->get()->first();
        $res = 0;
        $user_id = 0;
        $waste_pref_id = 0;
        if ($_user) {
            $userUpdate = User::find($_user->id);
            $userUpdate->line_id = $request->line_user_id;
            $userUpdate->image = $request->line_user_image;
            $userUpdate->save();

            if (collect($_user->wastePreference)->isEmpty()) {
                $newUWastePref = KpUserWastePreference::create([
                    'user_id' => $_user->id,
                    'is_annual_collection' => 0,
                    'is_waste_bank' => 1,
                ]);

                $waste_pref_id  = $newUWastePref->id;
            }


            $user_id = $_user->id;
            $res = 1;
        } else {
            //ถ้ายังไม่มีข้อมูลให้ ทำการ create
            $seqNumber = SequenceNumber::where('id', 1)->get('user')->first();

            $user =  new User();
            $user->id = $seqNumber->user;
            $user->firstname = $request->displayName;
            $user->line_id = $request->line_user_id;
            $user->image = $request->line_user_image;
            $user->created_at = date("Y-m-d H:i:s");
            $user->updated_at = date("Y-m-d H:i:s");
            $user->save();

            $newUWastePref = KpUserWastePreference::create([
                'user_id' => $seqNumber->user,
                'is_annual_collection' => 0,
                'is_waste_bank' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);


            (new KPAccounts())->registerAccount($newUWastePref->id);

            SequenceNumber::where('id', 1)->update([
                'user' => $seqNumber->user + 1
            ]);
            $user_id = $user->id;
            $res = 1;
            $waste_pref_id  = $newUWastePref->id;
        }
        return response()->json([
            'res' => $res,
            'user_id' => $user_id,
            'waste_pref_id' => $waste_pref_id
        ]);
    }
    public function user_line_register(Request $request)
    {
        $request = $request->get('payload');
        // 1. เริ่มต้น Transaction (ถ้าพังจุดไหน จะยกเลิกทั้งหมด)
        DB::beginTransaction();

        try {
            // 2. สร้าง User หลัก
            $user = User::create([
                'username'      => $request['org_id'] . $request['phoneNum'],
                'password'      => Hash::make($request['phoneNum']),
                'firstname'     => $request['firstname'],
                'lastname'      => $request['lastname'],
                'line_id'       => $request['line_user_id'],
                'line_user_id'  => $request['line_user_id'],
                'image'         => $request['line_user_image'],
                'phone'         => $request['phoneNum'],
                'org_id_fk'     => $request['org_id'],
                'province_code' => $request['province_id'],
                'district_code' => $request['district_id'],
                'tambon_code'   => $request['tambon_id'],
                'zone_id'       => $request['zone_id'],
                'subzone_id'    => $request['subzone_id'],
                'address'       => $request['address'],
            ]);

            // กำหนด Role พื้นฐาน
            $user->assignRole('User');

            // 3. สร้างบัญชีธนาคารขยะรีไซเคิล (เงินชาวบ้าน)
            $recycleAccount = RecycleBankAccount::create([
                'user_id'    => $user->id,
                'account_no' => 'RC-' . strtoupper(uniqid()), // หรือสร้างตาม Format ที่คุณต้องการ
                'balance'    => 0,
                'points'     => 0,
                'status'     => 'active',
            ]);

            // 4. สร้างบัญชีธนาคารขยะเปียก (เงินชาวบ้าน)
            $kpref = KpUserWastePreference::create([
                'user_id'       => $user->id,
                'is_waste_bank' => 1,
                'status'        => 'active',
                'org_id_fk'     => $request['org_id'],
                'province_code' => $request['province_id'],
                'district_code' => $request['district_id'],
                'tambon_code'   => $request['tambon_id'],
                'zone_id'       => $request['zone_id'],
                'subzone_id'    => $request['subzone_id'],
                'address'       => $request['address'],
            ]);
            // 5. สร้างสิทธิ์ขยะรายปี (เงินเทศบาล)
            // ตั้งค่าเริ่มต้นเป็น 'waived' (ฟรี) ตามที่คุณต้องการ
            $date = now();
            $fiscalYear = ($date->month >= 10) ? $date->year + 1 + 543 : $date->year + 543;
            // +543 กรณีต้องการเก็บเป็น พ.ศ. ตามระบบราชการไทย

            // 2. ดึงอัตราค่าธรรมเนียมล่าสุด
            $payRate = AnnualTrashPayratePerMonth::where('status', 1)->latest()->first();
            $monthFee = $payRate ? $payRate->payrate_permonth : 20.00;

            // 3. บันทึกข้อมูลพร้อมฟิลด์ที่บังคับทั้งหมด
            AnnualTrashSubscription::create([
                'user_id'        => $user->id,
                'fiscal_year'    => $fiscalYear,      // 🌟 ส่งค่าปีงบประมาณ (แก้ Error 1364)
                'month_fee'      => $monthFee,        // 🌟 ส่งค่าธรรมเนียมต่อเดือน
                'annual_fee'     => $monthFee * 12,   // 🌟 ส่งค่าธรรมเนียมรวมปี
                'billing_status' => 'waived',
                'waive_reason'   => 'new_member',
                'current_debt'   => 0,
            ]);


            // หากทุกอย่างสำเร็จ ยืนยันการบันทึก
            DB::commit();

            return response()->json([
                'res' => 1,
                'user_id' => $user->id,
                'recycle_account_id' => $recycleAccount->id,
                'kp_ref' => $kpref->id,
                'msg' => 'ลงทะเบียนและเปิดบัญชีขยะเรียบร้อยแล้ว'
            ]);
        } catch (\Exception $e) {
            // หากเกิด Error ให้ Rollback ข้อมูลทั้งหมดที่สร้างไป
            DB::rollBack();

            Log::error('Register Error: ' . $e->getMessage());

            return response()->json([
                'res' => 0,
                'msg' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ]);
        }
    }

    public function register_bank(Request $request)
    {
        $userId = Auth::id();

        return DB::transaction(function () use ($userId) {
            // 1. สร้าง/อัปเดต Preference (ตัวแม่)
            $pref = FoodWasteUserPreference::updateOrCreate(
                ['user_id' => $userId],
                ['is_foodwaste_bank' => 1]
            );

            // 2. สร้างกระเป๋าเงิน (Account ตัวลูก) สำหรับขยะเปียก
            // ใช้ Model FoodWasteAccount ตามที่คุณส่งมาล่าสุด
            $account = FoodWasteAccount::firstOrCreate(
                ['fw_pref_id_fk' => $pref->id],
                [
                    'points_balance' => 0,
                    'money_balance' => 0,
                    'total_weight_contributed' => 0
                ]
            );

            return back()->with('success', 'ยินดีด้วย! คุณเปิดบัญชีธนาคารขยะเปียกสำเร็จ');
        });
    }

    
    
}
