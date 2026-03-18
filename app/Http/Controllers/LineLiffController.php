<?php

namespace App\Http\Controllers;

use App\Models\Admin\Organization;
use App\Models\Admin\Province;
use App\Models\FoodWaste\CompostBatches;
use App\Models\FoodWaste\FoodWasteIssueReport;
use App\Models\KeptKaya\KPAccounts;
use App\Models\KeptKaya\KpUserWastePreference;
use App\Models\FoodWaste\MealLog;
use App\Models\Tabwater\SequenceNumber;
use App\Models\User;
use App\Models\FoodWaste\FoodWasteLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

    public function dashboard($user_waste_pref_id, $org_id, $regis = 1)
    {
        $uWastePref = KpUserWastePreference::find($user_waste_pref_id);
        $user = User::find($uWastePref->user_id);
        Auth::login($user);

        $userWastePref = KpUserWastePreference::with('user', 'purchaseTransactions', 'kp_account')
            ->where('id', $user_waste_pref_id)->get()->first();

        $userId = $user->id;

        // --- 🌟 1. ดึงสถิติทั่วไป ---
        $totalWasteWeight = FoodWasteLog::where('user_id', $userId)->sum('weight_kg');
        $totalCarbonSaved = FoodWasteLog::where('user_id', $userId)->sum('carbon_saved_kg');

        // --- 🌟 2. ดึงข้อมูลล็อตปัจจุบัน (Active Batch) ---
        // ค้นหาล็อตที่สถานะเป็น 'filling' (กำลังเติม) ของ User คนนี้
        $activeBatch = CompostBatches::where('user_id', $userId)
            ->where('status', 'filling')
            ->latest()
            ->first();

        if ($activeBatch) {
            // คำนวณวันที่ผ่านไป และน้ำหนักรวมเฉพาะในล็อตนี้
            $activeBatch->days_passed = (int) now()->diffInDays($activeBatch->start_date) == 0 ? 1 : (int) now()->diffInDays($activeBatch->start_date);
            $activeBatch->total_weight = FoodWasteLog::where('batch_id', $activeBatch->id)->sum('weight_kg');

            // ดึงสถานะความร้อนล่าสุดจาก Log ล่าสุดในล็อต
            $lastLog = FoodWasteLog::where('batch_id', $activeBatch->id)->latest()->first();
            $activeBatch->temp_status = $lastLog ? $lastLog->temperature_feel : 'ยังไม่มีข้อมูล';
        }

        // --- 🌟 3. ข้อมูลกราฟ ---
        $weeklyStats = MealLog::where('user_id', $userId)
            ->where('created_at', '>=', now()->subDays(6))
            ->selectRaw('DATE(created_at) as date, SUM(total_calories) as daily_calories')
            ->groupBy('date')->orderBy('date', 'ASC')->get();

        $chartLabels = $weeklyStats->pluck('date')->toArray();
        $chartData = $weeklyStats->pluck('daily_calories')->toArray();

        $qrcode = QrCode::size(300)->generate($user_waste_pref_id . "-" . $userWastePref->user_id);

        $pendingIssuesCount = FoodWasteIssueReport::where('user_id', $userId)
            ->where('status', '!=', 'resolved')
            ->count();
        // 2. ดึงรายการแจ้งปัญหาทั้งหมดของ User คนนี้
        $myIssues = FoodWasteIssueReport::where('user_id', $userId)
            ->latest()
            ->get();
        // ส่ง $activeBatch กลับไปที่ View ด้วย

        $targetCalories = 0;

        // คำนวณ TDEE ถ้ามีข้อมูลครบ
        $user = User::find(Auth::id());
        $targetCalories = $user->calculateTDEE();

        $todayCalories = MealLog::where('user_id', Auth::id())
            ->whereDate('created_at', now())
            ->sum('total_calories');

        return view('lineliff.dashboard', compact(
            'userWastePref',
            'qrcode',
            'totalWasteWeight',
            'totalCarbonSaved',
            'chartLabels',
            'chartData',
            'pendingIssuesCount',
            'activeBatch',
            'myIssues',
            'targetCalories',
            'todayCalories'
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
}
