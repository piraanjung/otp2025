<?php

namespace App\Http\Controllers\FoodWaste;

use Illuminate\Http\Request;
use App\Models\FoodWaste\MealLog;
use App\Models\FoodWaste\FoodWasteLog;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;
use App\Models\FoodWaste\CompostBatches;
use App\Models\FoodWaste\FoodWasteIssueReport;
use App\Models\FoodWaste\LocalFood;
use App\Models\FoodWaste\MealItem;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AiroBactController extends Controller
{
    // หน้า Dashboard
    public function index()
    {
        // ดึง ID ผู้ใช้ (ถ้ายังไม่ทำระบบ Login ให้ใช้ 1 ไปก่อนครับ)
        $userId = Auth::id() ?? 1;
        $totalWaste = FoodWasteLog::where('user_id', $userId)->sum('weight_kg');
        $totalCarbon = FoodWasteLog::where('user_id', $userId)->sum('carbon_saved_kg');

        // 🌟 ดึงข้อมูลแคลอรี่รวมย้อนหลัง 7 วัน สำหรับทำกราฟ
        $weeklyStats = MealLog::where('user_id', $userId)
            ->where('created_at', '>=', now()->subDays(6))
            ->selectRaw('DATE(created_at) as date, SUM(total_calories) as daily_calories')
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get();

        // เตรียมข้อมูลให้พร้อมสำหรับ JavaScript
        $chartLabels = $weeklyStats->pluck('date')->toArray();
        $chartData = $weeklyStats->pluck('daily_calories')->toArray();

        // 🌟 ดึงมื้อล่าสุด (ที่ยังไม่ได้ลงถัง)
        $latestEntry = MealLog::where('user_id', $userId)
            ->where('created_at', '>=', now()->subHours(3))
            ->where('status','<>', 'binned') // 🌟 เพิ่มบรรทัดนี้: กรองเอาเฉพาะที่ยังไม่ลงถัง
            ->latest()
            ->first();

        $latestMeals = collect();
        if ($latestEntry) {
            // ดึงรายการอาหารทั้งหมดในมื้อนั้นมาโชว์
            $latestMeals = MealItem::where('meal_log_id', $latestEntry->id)->get();
        }

        $targetCalories = 0;

    // คำนวณ TDEE ถ้ามีข้อมูลครบ
    $user = Auth::user();
    if ($user->weight && $user->height && $user->age) {
        if ($user->gender === 'male') {
            $bmr = (10 * $user->weight) + (6.25 * $user->height) - (5 * $user->age) + 5;
        } else {
            $bmr = (10 * $user->weight) + (6.25 * $user->height) - (5 * $user->age) - 161;
        }
        $targetCalories = $bmr * 1.2; // สมมติกิจกรรมน้อย (Sedentary)
    }

    $todayCalories = MealLog::where('user_id', Auth::id())
    ->whereDate('created_at', now())
    ->sum('total_calories');

        $waste_preference = User::where('id', $userId)->with('wastePreference')->get()->first();
        return view('foodwaste.airo.dashboard', compact(
            'totalWaste',
            'totalCarbon',
            'latestMeals',
            'chartLabels',
            'chartData',
            'waste_preference',
            'latestEntry',
            'targetCalories'
        ));
    }

    // ---------------------------------------------------
    // ส่วนที่ 2: รับข้อมูลขยะและคำนวณ Carbon Credit
    // ---------------------------------------------------
    public function storeWaste(Request $request)
    {
        $request->validate([
            'weight_kg' => 'required|numeric',
            'waste_photo' => 'required|image'
        ]);

        $destinationPath = public_path('wastes');

        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }

        $fileName = time() . '_' . $request->file('waste_photo')->getClientOriginalName();

        // 1. ย้ายไฟล์ไปที่ใหม่
        $request->file('waste_photo')->move($destinationPath, $fileName);
        $path = 'wastes/' . $fileName;

        // 2. 🌟 แก้ตรงนี้: อ่านไฟล์จาก path ใหม่ที่เพิ่งย้ายไป (ใช้ $destinationPath . '/' . $fileName)

        // คำนวณคาร์บอน
        $carbonSaved = $this->calculateCarbonCredit($request->weight_kg);

        // 1. หา Batch ที่สถานะเป็น 'filling' (กำลังเติม) ของ User คนนี้
        $activeBatch = CompostBatches::where('user_id', Auth::id())
            ->where('status', 'filling')
            ->first();

        // 2. ถ้าไม่มีล็อตที่เปิดอยู่ ให้สร้างล็อตใหม่ให้อัตโนมัติ
        if (!$activeBatch) {
            $activeBatch = CompostBatches::create([
                'user_id' => Auth::id(),
                'batch_code' => 'LOT-' . date('ym') . '-' . rand(100, 999),
                'start_date' => now(),
                'status' => 'filling'
            ]);
        }
        FoodWasteLog::create([
            'user_id' => Auth::id(),
            'batch_id' => $activeBatch->id,
            'weight_kg' => $request->weight_kg,
            'photo_path' => $path,
            'is_mixed' => $request->has('is_mixed'),
            'moisture' => $request->moisture,
            'carbon_saved_kg' => $carbonSaved,
            'estimated_weight' => $request->weight_kg,
        ]);
        MealLog::where('user_id', Auth::id())
            ->where('status', '!=', 'binned') // สมมติว่าใช้คำว่า binned แปลว่าลงถังแล้ว
            ->update(['status' => 'binned']);
        $user_waste_pref_id = Auth::user()->wastePreference->id;
        $org_id = Auth::user()->org_id_fk;
        return redirect('line/dashboard/' . $user_waste_pref_id . '/' . $org_id);
        // return back()->with('success', 'บันทึกขยะสำเร็จ! คุณลดคาร์บอนได้ ' . number_format($carbonSaved, 2) . ' kgCO2e');
    }

    // สคริปต์คำนวณ Carbon Credit อย่างแม่นยำ (อ้างอิงหลักการ T-VER)
    private function calculateCarbonCredit($weightKg)
    {
        // Emission Factor สำหรับการฝังกลบขยะอินทรีย์ (ทำให้เกิดมีเทน)
        // ขยะเศษอาหาร 1 กก. ถ้าไปฝังกลบจะปล่อยก๊าซเรือนกระจกประมาณ 0.68 kgCO2e
        // การหมักแบบเติมอากาศ (AiroBact) ปล่อยก๊าซน้อยมาก จึงคิดส่วนต่างเป็น Credit
        $emissionFactorLandfill = 0.68;

        return $weightKg * $emissionFactorLandfill;
    }

    // 1. ฟังก์ชันรับรูป -> ส่งให้ AI -> เปิดหน้า confirm_meal
    public function analyzeMeal(Request $request)
    {
        $request->validate(['meal_photo' => 'required|image']);
        $destinationPath = public_path('meals');

        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }

        $fileName = time() . '_' . $request->file('meal_photo')->getClientOriginalName();

        // 1. ย้ายไฟล์ไปที่ใหม่
        $request->file('meal_photo')->move($destinationPath, $fileName);
        $path = 'meals/' . $fileName;

        // 2. 🌟 แก้ตรงนี้: อ่านไฟล์จาก path ใหม่ที่เพิ่งย้ายไป (ใช้ $destinationPath . '/' . $fileName)
        $fullPath = $destinationPath . '/' . $fileName;
        $imageData = base64_encode(file_get_contents($fullPath));

        $apiKey = env('GEMINI_API_KEY');
        $response = Http::post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}", [
            'contents' => [['parts' => [
                // 🌟 ปรับ Prompt ใหม่ให้สั่ง AI คืนค่าเป็น Array
                ['text' => 'Analyze this food image. If there are multiple dishes (like a food set), list each dish separately. Return ONLY a valid JSON ARRAY of objects. Each object must have keys: "menu_name" (string in Thai language), "calories" (number). Example: [{"menu_name": "ไก่ย่าง", "calories": 250}, {"menu_name": "ส้มตำไทย", "calories": 120}]. Do not use markdown format.'],
                ['inline_data' => ['mime_type' => 'image/jpeg', 'data' => $imageData]]
            ]]]
        ]);

        $aiText = $response->json('candidates.0.content.parts.0.text');
        $cleanText = trim(str_replace(['```json', '```'], '', $aiText));

        // 🌟 แปลงผลลัพธ์ที่ได้ให้เป็น Array
        $aiDataArray = json_decode($cleanText, true);

        // เช็คเผื่อ AI ดื้อไม่ยอมส่งเป็น Array
        if (!is_array($aiDataArray) || json_last_error() !== JSON_ERROR_NONE) {
            // ถ้าพัง ให้สร้าง Array ว่างๆ ไว้ 1 ช่องให้ User กรอกเอง
            $aiDataArray = [['menu_name' => '', 'calories' => 0]];
        }

        $localFoods = LocalFood::all()->groupBy('category');

        // เปลี่ยนตัวแปรที่ส่งไปเป็น $aiDataArray
        return view('foodwaste.confirm_meal', compact('aiDataArray', 'path', 'localFoods'));
    }

    // 2. รับข้อมูลจาก Smart Form มาบันทึก
    public function saveMeal(Request $request)
    {
        // 1. สร้าง "หัวมื้อ" ก่อน
        $mealLog = MealLog::create([
            'user_id' => Auth::id() ?? 1,
            'photo_path' => $request->photo_path,
            'total_calories' => array_sum($request->calories) ,
            'status' => 'wait'
        ]);

        // 2. วนลูปเซฟ "ลูกเมนู"
        foreach ($request->selected_foods as $index => $selection) {
            // 🌟 แยก Logic ตรงนี้: ถ้าเป็นเมนูใหม่ (พิมพ์เอง)
            if ($selection === 'NEW_MENU') {
                $menuName = $request->new_menu_names[$index];
                $calories = 0;              // ❌ บังคับเป็น 0
                $status   = 'pending_review'; // 🟠 รอเจ้าหน้าที่
            } else {
                // กรณีเลือกตามที่ AI แนะนำ
                $menuName = $selection;
                $calories = $request->calories[$index];
                $status   = 'verified';      // 🟢 ยืนยันแล้ว
            }
            $mealLog->items()->create([
                'menu_name' => $menuName,
                'category'  => $request->new_categories[$index] ?? 'ทั่วไป',
                'calories'  => $calories,
                'status'    => $status,
            ]);
        }

        return redirect('foodwaste/airo/dashboard')->with('success', 'บันทึกข้อมูลสำเร็จแล้ว!');


    }

    // ดึงรายการที่ User เพิ่มเองและยังไม่ได้รับการตรวจสอบ
    public function mealLogsadminDashboard()
    {
        $pendingItems = MealItem::with('mealLog')
            ->where('status', 'pending_review')
            ->latest()
            ->get();

        return view('foodwaste.admin.meal_logs_confirm_dashboard', compact('pendingItems'));
    }

    // ฟังก์ชันอนุมัติ: ปรับสถานะ และเพิ่มลงใน LocalFood (พจนานุกรม)
    public function approveItem(Request $request, $id)
    {
        $item = MealItem::findOrFail($id);

        // 1. อัปเดตข้อมูลตามที่ Admin แก้ไข
        $item->update([
            'menu_name' => $request->menu_name,
            'calories' => $request->calories,
            'status' => 'verified'
        ]);

        // 2. เพิ่มลงในพจนานุกรมท้องถิ่น (LocalFood) เพื่อให้ระบบจำได้ในครั้งหน้า
        LocalFood::updateOrCreate(
            ['menu_name' => $request->menu_name],
            [
                'category' => $item->category ?? 'ทั่วไป',
                'calories' => $request->calories
            ]
        );

        return back()->with('success', 'อนุมัติและบันทึกเข้าพจนานุกรมเรียบร้อย!');
    }

    public function batchHistory()
    {
        $userId = Auth::id();

        // ดึงข้อมูล Batch ของ User พร้อมกับข้อมูลขยะที่ผูกอยู่
        $batches = CompostBatches::with('wasteLogs')
            ->where('user_id', $userId)
            ->latest()
            ->get();
        $waste_preference = User::where('id', $userId)->with('wastePreference')->get()->first();

        return view('foodwaste.airo.batch_history', compact('batches', 'waste_preference'));
    }

    public function batchDetail($id)
    {
        $userId = Auth::id();

        // ดึงข้อมูลล็อต พร้อมรายการขยะ (FoodWasteLogs) ที่ผูกอยู่
        $batch = CompostBatches::with(['wasteLogs' => function ($query) {
            $query->latest(); // เอาที่เทล่าสุดขึ้นก่อน
        }])
            ->where('user_id', $userId)
            ->findOrFail($id); // ถ้าไม่เจอจะส่ง 404 อัตโนมัติ (แต่เรามี Route รองรับแล้ว)
        $waste_preference = User::where('id', $userId)->with('wastePreference')->get()->first();

        return view('foodwaste.airo.batch_detail', compact('batch', 'waste_preference'));
    }

    public function reportIssue(Request $request)
    {
        $request->validate([
            'issue_type' => 'required',
            'description' => 'nullable|string'
        ]);

        // บันทึกลงฐานข้อมูล (อย่าลืมสร้าง Migration สำหรับตารางนี้)
        FoodWasteIssueReport::create([
            'user_id' => Auth::id(),
            'batch_id' => CompostBatches::where('user_id', Auth::id())->where('status', 'filling')->value('id'),
            'issue_type' => $request->issue_type,
            'description' => $request->description,
            'status' => 'pending' // รอนิ่งจากเจ้าหน้าที่
        ]);

        // ตัวเลือกเสริม: ส่ง Notification หาเจ้าหน้าที่ผ่าน LINE Notify หรือ Email
        // $this->notifyStaff($request->issue_type);

        return back()->with('success', 'ได้รับข้อมูลแจ้งปัญหาแล้ว เจ้าหน้าที่จะติดต่อกลับโดยเร็วที่สุดครับ');
    }

    public function howTo()
    {
        return view('foodwaste.airo.how_to');
    }
}
