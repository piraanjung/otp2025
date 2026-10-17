<?php

namespace App\Http\Controllers\FoodWaste;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FoodWaste\LocalFood; // 🌟 เปลี่ยนชื่อ Model ให้ตรงกับของคุณนะครับ
use App\Models\FoodWaste\MealItem;
use Illuminate\Support\Facades\DB;

class AdminLocalFoodController extends Controller
{
    // หน้า Index โชว์รายการทั้งหมด
    public function index()
    {
        // ดึงข้อมูลอาหารพื้นถิ่นทั้งหมด เรียงจากล่าสุด
        $localFoods = LocalFood::orderBy('id', 'desc')->get();

        return view('foodwaste.admin.local_foods.index', compact('localFoods'));
    }

    public function review(){
            {
        // ดึงข้อมูลอาหารพื้นถิ่นทั้งหมด เรียงจากล่าสุด
        $pendingFoods = MealItem::select('menu_name', 'category')
            ->selectRaw('COUNT(*) as request_count') // นับว่ามีคนกรอกเมนูนี้มากี่คน
            ->where('status', 'pending_review')
            ->groupBy('menu_name', 'category')
            ->orderBy('request_count', 'desc') // เอาเมนูที่คนค้นหาเยอะสุดขึ้นก่อน
            ->get();

        return view('foodwaste.admin.local_foods.review', compact('pendingFoods'));
    }

    }

    // ฟังก์ชัน Create (เพิ่มเมนูใหม่)
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'calories' => 'required|numeric|min:0',
            'category' => 'nullable|string|max:255'
        ]);

        LocalFood::create($request->all());
        return back()->with('success', 'เพิ่มเมนูอาหารพื้นถิ่นเรียบร้อยแล้ว!');
    }

    // ฟังก์ชัน Update (แก้ไขเมนูเดิม)
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'calories' => 'required|numeric|min:0',
            'category' => 'nullable|string|max:255'
        ]);

        $food = LocalFood::findOrFail($id);
        $food->update($request->all());

        return back()->with('success', 'อัปเดตข้อมูลเมนู "'.$request->name.'" เรียบร้อยแล้ว!');
    }

    // ฟังก์ชัน Delete (ลบเมนู)
    public function destroy($id)
    {
        $food = LocalFood::findOrFail($id);
        $foodName = $food->name;
        $food->delete();

        return back()->with('success', 'ลบเมนู "'.$foodName.'" ออกจากระบบแล้ว!');
    }

    public function approve(Request $request)
    {
        $request->validate([
            'menu_name' => 'required|string',
            'category' => 'nullable|string',
            'calories' => 'required|numeric|min:0'
        ]);

        $menuName = $request->menu_name;
        $calories = $request->calories;
        $category = $request->category;

        DB::beginTransaction();
        try {
            // STEP 1: บันทึกลงฐานข้อมูลอาหารพื้นถิ่น (LocalFood) เพื่อให้ใช้อ้างอิงได้ในอนาคต
            LocalFood::updateOrCreate(
                ['menu_name' => $menuName], // ถ้ามีชื่อนี้อยู่แล้วให้อัปเดต ถ้าไม่มีให้สร้างใหม่
                [
                    'calories' => $calories,
                    'category' => $category
                ]
            );

            // STEP 2: ค้นหา MealItem ทั้งหมดที่รอกำหนดแคลอรี่สำหรับเมนูนี้
            $itemsToUpdate = MealItem::with('mealLog')
                ->where('menu_name', $menuName)
                ->where('status', 'pending_review')
                ->get();

            // STEP 3: อัปเดตแคลอรี่, สถานะ และคำนวณแคลอรี่รวมของมื้อนั้นใหม่
            foreach ($itemsToUpdate as $item) {
                // อัปเดตลูก (MealItem)
                $item->update([
                    'calories' => $calories,
                    'status' => 'verified'
                ]);

                // อัปเดตแม่ (MealLog) ให้คำนวณแคลอรี่รวมใหม่
                if ($item->mealLog) {
                    $log = $item->mealLog;
                    $log->total_calories = $log->items()->sum('calories');
                    $log->save();
                }
            }

            DB::commit();
            return back()->with('success', "บันทึกและอนุมัติเมนู '{$menuName}' ให้กับผู้ใช้ {$itemsToUpdate->count()} รายการเรียบร้อยแล้ว!");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }
}
