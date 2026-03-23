<?php

namespace App\Http\Controllers;

use App\Models\Admin\Organization;
use App\Models\Admin\Province;
use App\Models\AnnualTrashSubscription;
use App\Models\FoodWaste\CompostBatches;
use App\Models\FoodWaste\FoodWasteAccount;
use App\Models\FoodWaste\FoodWasteIssueReport;
use App\Models\KeptKaya\KPAccounts;
use App\Models\KeptKaya\KpUserWastePreference;
use App\Models\FoodWaste\MealLog;
use App\Models\Tabwater\SequenceNumber;
use App\Models\User;
use App\Models\FoodWaste\FoodWasteLog;
use App\Models\FoodWaste\FoodWasteUserPreference;
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
        $orgs = Organization::with('provinces', 'districts', 'tambons')
            ->get(['id', 'org_type_name', 'org_short_type_name', 'org_name', 'org_tambon_id_fk', 'org_district_id_fk', 'org_province_id_fk']);
        return view('lineliff.index', compact('provinces', 'orgs'));
    }

    public function dashboard($userId, $org_id)
    {
        // 1. ดึงข้อมูล User และ Login
        $user = User::findOrFail($userId);
        Auth::login($user);

        // 2. ดึงข้อมูลบัญชี (New Schema)
        $recycleAcc = RecycleBankAccount::where('user_id', $userId)->first();
        $foodWasteAcc = FoodWasteAccount::where('user_id', $userId)->first();
        $annualTrash = AnnualTrashSubscription::where('user_id', $userId)->first();

        // 3. สถิติขยะเปียก & ล็อตปุ๋ย
        $totalWasteWeight = $foodWasteAcc ? $foodWasteAcc->total_weight_kg : 0;
        $totalCarbonSaved = FoodWasteLog::where('user_id', $userId)->sum('carbon_saved_kg'); // เพิ่มจุดนี้

        $activeBatch = CompostBatches::where('user_id', $userId)
            ->where('status', 'filling')
            ->latest()
            ->first();

        if ($activeBatch) {
            $days = (int) now()->diffInDays($activeBatch->start_date);
            $activeBatch->days_passed = ($days == 0) ? 1 : $days;
            $activeBatch->total_weight = FoodWasteLog::where('batch_id', $activeBatch->id)->sum('weight_kg');
            $activeBatch->is_ready = $activeBatch->days_passed >= 7;

            $lastLog = FoodWasteLog::where('batch_id', $activeBatch->id)->latest()->first();
            $activeBatch->temp_status = $lastLog ? $lastLog->temperature_feel : 'ยังไม่มีข้อมูล';
        }

        // 4. ข้อมูลกราฟ 7 วัน (MealLog)
        $weeklyStats = MealLog::where('user_id', $userId)
            ->where('created_at', '>=', now()->subDays(6))
            ->selectRaw('DATE(created_at) as date, SUM(total_calories) as daily_calories')
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get();

        $chartLabels = $weeklyStats->pluck('date')->map(fn($date) => \Carbon\Carbon::parse($date)->format('d/m'))->toArray();
        $chartData = $weeklyStats->pluck('daily_calories')->toArray();

        if (empty($chartLabels)) {
            $chartLabels = [now()->format('d/m')];
            $chartData = [0];
        }

        // 5. พลังงานและเป้าหมาย
        $targetCalories = $user->calculateTDEE();
        $todayCalories = MealLog::where('user_id', $userId)->whereDate('created_at', now())->sum('total_calories');

        // 6. แต้มและเงิน (ใช้จากตารางใหม่)
        $totalPoints = $recycleAcc ? $recycleAcc->points : 0;
        $totalBalance = $recycleAcc ? $recycleAcc->balance : 0.00;

        // 7. ส่วนอื่นๆ
        $qrcode = QrCode::size(300)->generate("USER-" . $userId);
        $myIssues = FoodWasteIssueReport::where('user_id', $userId)->latest()->get();
        $pendingIssuesCount = FoodWasteIssueReport::where('user_id', $userId)->where('status', '!=', 'resolved')->count();

        return view('lineliff.dashboard', compact(
            'user',
            'recycleAcc',
            'foodWasteAcc',
            'annualTrash',
            'activeBatch',
            'totalWasteWeight',
            'totalCarbonSaved',
            'qrcode',
            'pendingIssuesCount',
            'myIssues',
            'targetCalories',
            'todayCalories',
            'totalPoints',
            'totalBalance',
            'chartLabels',
            'chartData'
        ));
    }

    // public function dashboard($userId, $org_id)
    // {

    //     // 1. ดึงข้อมูล User และ Preference (ใช้ user_id เป็นตัวกรองหลัก)
    //     $user = User::findOrFail($userId);
    //     Auth::login($user);
    //     // พยายามดึงข้อมูลการสมัครสมาชิกธนาคารขยะ
    //     $userWastePref = KpUserWastePreference::where('user_id', $userId)->first();

    //     // ดึงกระเป๋าเงิน (Account) ผ่านความสัมพันธ์จาก Preference
    //     $account = $userWastePref ? $userWastePref->kp_account : null;

    //     // --- 🌟 1. ดึงสถิติทั่วไป (ใช้ userId ตรงๆ) ---
    //     $totalWasteWeight = FoodWasteLog::where('user_id', $userId)->sum('weight_kg');
    //     $totalCarbonSaved = FoodWasteLog::where('user_id', $userId)->sum('carbon_saved_kg');

    //     // --- 🌟 2. ดึงข้อมูลล็อตปัจจุบัน (Active Batch) ---
    //     $activeBatch = CompostBatches::where('user_id', $userId)
    //         ->where('status', 'filling')
    //         ->latest()
    //         ->first();


    //     if ($activeBatch) {
    //         $days = (int) now()->diffInDays($activeBatch->start_date);
    //         $activeBatch->days_passed = ($days == 0) ? 1 : $days;
    //         $activeBatch->total_weight = FoodWasteLog::where('batch_id', $activeBatch->id)->sum('weight_kg');
    //         $isReadyToMove = $activeBatch->days_passed >= 7;
    //         $activeBatch->is_ready = $isReadyToMove;

    //         $lastLog = FoodWasteLog::where('batch_id', $activeBatch->id)->latest()->first();
    //         $activeBatch->temp_status = $lastLog ? $lastLog->temperature_feel : 'ยังไม่มีข้อมูล';
    //     }


    //     // --- 🌟 3. ข้อมูลกราฟ (ดึงย้อนหลัง 7 วัน) ---
    //     $weeklyStats = MealLog::where('user_id', $userId)
    //         ->where('created_at', '>=', now()->subDays(6))
    //         ->selectRaw('DATE(created_at) as date, SUM(total_calories) as daily_calories')
    //         ->groupBy('date')
    //         ->orderBy('date', 'ASC')
    //         ->get();

    //     // เตรียมข้อมูลส่งให้ Chart.js หรือ Library กราฟที่คุณใช้
    //     $chartLabels = $weeklyStats->pluck('date')->map(function ($date) {
    //         return \Carbon\Carbon::parse($date)->format('d/m'); // ปรับฟอร์แมตวันที่ให้สั้นลง
    //     })->toArray();

    //     $chartData = $weeklyStats->pluck('daily_calories')->toArray();

    //     // --- 🌟 เช็คค่าว่าง (ป้องกันกราฟพังถ้า User ใหม่ยังไม่มีข้อมูล) ---
    //     if (empty($chartLabels)) {
    //         $chartLabels = [now()->format('d/m')];
    //         $chartData = [0];
    //     }
    //     $targetCalories = $user->calculateTDEE();
    //     $todayCalories = MealLog::where('user_id', $userId)
    //         ->whereDate('created_at', now())
    //         ->sum('total_calories');

    //     // --- 🌟 4. ดึงแต้มและเงินจากตาราง Account จริง ---
    //     $totalPoints = $account ? $account->points_balance : 0;
    //     $totalBalance = $account ? $account->money_balance : 0.00;

    //     // --- 🌟 5. ส่วนอื่นๆ ---
    //     $qrcode = QrCode::size(300)->generate("USER-" . $userId);
    //     $myIssues = FoodWasteIssueReport::where('user_id', $userId)->latest()->get();
    //     $pendingIssuesCount = $myIssues->where('status', '!=', 'resolved')->count();

    //     $userFoodWastePref = FoodWasteUserPreference::with('foodwaste_account')->where('user_id', $userId)
    //         ->where('is_foodwaste_bank', '1')->first();


    //     $foodWastePoints = 0;
    //     if ($userFoodWastePref && $userFoodWastePref->foodwaste_account) {
    //         $foodWastePoints = $userFoodWastePref->foodwaste_account->points_balance;
    //     }
    //     return view('lineliff.dashboard', compact(
    //         'user',
    //         'userWastePref',
    //         'userFoodWastePref',
    //         'foodWastePoints',
    //         'account',
    //         'qrcode',
    //         'totalWasteWeight',
    //         'totalCarbonSaved',
    //         'pendingIssuesCount',
    //         'activeBatch',
    //         'myIssues',
    //         'targetCalories',
    //         'todayCalories',
    //         'totalPoints',
    //         'totalBalance',
    //         'chartLabels',
    //         'chartData' // 👈 เพิ่ม 2 ตัวนี้กลับเข้าไปใน compact
    //     ));
    // }

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
        // 1. เริ่มต้น Transaction (ถ้าพังจุดไหน จะยกเลิกทั้งหมด)
        DB::beginTransaction();

        try {
            // 2. สร้าง User หลัก
            $user = User::create([
                'username'      => $request->org_id . $request->phoneNum,
                'password'      => Hash::make($request->phoneNum),
                'firstname'     => $request->firstname,
                'lastname'      => $request->lastname,
                'line_id'       => $request->line_user_id,
                'image'         => $request->line_user_image,
                'phone'         => $request->phoneNum,
                'org_id_fk'     => $request->org_id,
                'province_code' => $request->province_id,
                'district_code' => $request->district_id,
                'tambon_code'   => $request->tambon_id,
                'zone_id'       => $request->zone_id,
                'subzone_id'    => $request->subzone_id,
                'address'       => $request->address,
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
            FoodWasteAccount::create([
                'user_id' => $user->id,
                'balance' => 0,
                'total_weight_kg' => 0,
            ]);

            // 5. สร้างสิทธิ์ขยะรายปี (เงินเทศบาล)
            // ตั้งค่าเริ่มต้นเป็น 'waived' (ฟรี) ตามที่คุณต้องการ
            AnnualTrashSubscription::create([
                'user_id'        => $user->id,
                'billing_status' => 'waived',
                'waive_reason'   => 'new_recycle_member', // ยกเว้นให้เพราะเพิ่งสมัคร
                'monthly_fee'    => 20.00,
                'current_debt'   => 0,
            ]);

            // หากทุกอย่างสำเร็จ ยืนยันการบันทึก
            DB::commit();

            return response()->json([
                'res' => 1,
                'user_id' => $user->id,
                'recycle_account_id' => $recycleAccount->id,
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

    // public function user_line_register(Request $request)
    // {

    //     // 1. เริ่ม Transaction ป้องกันข้อมูลไม่สมบูรณ์
    //     DB::beginTransaction();

    //     try {
    //         // 2. สร้าง User
    //         $user = User::create([
    //             'username'      => $request->org_id . $request->phoneNum,
    //             'password'      => Hash::make($request->phoneNum),
    //             'firstname'     => $request->firstname,
    //             'lastname'      => $request->lastname,
    //             'line_id'       => $request->line_user_id,
    //             'image'         => $request->line_user_image,
    //             'phone'         => $request->phoneNum,
    //             'age'           => 0,
    //             'height'        => 0,
    //             'weight'        => 0,
    //             'tambon_code'   => $request->tambon_id,
    //             'district_code' => $request->district_id,
    //             'province_code' => $request->province_id,
    //             'org_id_fk'     => $request->org_id,
    //             'zone_id'       => $request->zone_id,
    //             'address'       => $request->address,
    //             'subzone_id'    => $request->subzone_id,
    //             'created_at'    => now(), // ใช้ helper now() แทน date() ได้ครับ สั้นลง
    //             'updated_at'    => now(),
    //         ]);

    //         // กำหนดสิทธิ์
    //         $user->assignRole('User');

    //         // 3. สร้างและเปิดบัญชี: ธนาคารขยะรีไซเคิล
    //         $userWastPref = KpUserWastePreference::create([
    //             'user_id'              => $user->id,
    //             'is_annual_collection' => 0, // ปิดไว้ก่อน รอแอดมินมาอัปเดต
    //             'is_waste_bank'        => 1, // เปิดอัตโนมัติ
    //             'created_at'           => now(),
    //             'updated_at'           => now(),
    //         ]);

    //         // เปิดบัญชีรีไซเคิล
    //         (new KPAccounts())->registerAccount($userWastPref->id);

    //         // 4. สร้างและเปิดบัญชี: ธนาคารขยะเปียก (เพิ่มเข้ามาใหม่)
    //         $foodWastePref = FoodWasteUserPreference::create([
    //             'user_id'           => $user->id,
    //             'is_foodwaste_bank' => 1, // เปิดอัตโนมัติ
    //             'created_at'        => now(),
    //             'updated_at'        => now(),
    //         ]);

    //         // **หมายเหตุ:** ถ้าคุณมีคลาสสำหรับเปิดบัญชีขยะเปียกแยกต่างหาก เช่น FoodWasteAccounts
    //         // ให้เอามาใส่ตรงนี้ได้เลยครับ เช่น:
    //         // (new FoodWasteAccounts())->registerAccount($foodWastePref->id);


    //         // 5. บันทึกข้อมูลทั้งหมดลงฐานข้อมูล
    //         DB::commit();

    //         return response()->json([
    //             'res'           => 1,
    //             'waste_pref_id' => $userWastPref->id
    //         ]);
    //     } catch (\Exception $e) {
    //         // ถ้ามี Error ตรงไหนก็ตาม ระบบจะยกเลิกการสร้างข้อมูลในบล็อก try ทั้งหมด
    //         DB::rollBack();

    //         // เก็บ Log ไว้เผื่อดีบักปัญหา
    //         Log::error('LINE Register Error: ' . $e->getMessage());

    //         return response()->json([
    //             'res' => 0,
    //             'msg' => 'เกิดข้อผิดพลาดในการลงทะเบียน โปรดลองใหม่อีกครั้ง',
    //             'error' => $e->getMessage() // ตอนนำขึ้นใช้จริง (Production) ควรเอาบรรทัดนี้ออกครับ
    //         ]);
    //     }
    // }
}
