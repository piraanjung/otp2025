<?php

namespace App\Http\Controllers\FoodWaste; // 🌟 เพิ่ม \FoodWaste ตรงนี้

use App\Http\Controllers\Controller; // 🌟 ต้อง use ตัวนี้เพิ่มเข้ามาด้วย
use Illuminate\Http\Request;
use App\Models\FoodWaste\MealItem;
use App\Models\FoodWaste\MealLog;

class AdminReviewController extends Controller
{
    public function index()
    {
        // ดึงรายการอาหารที่ "รอตรวจสอบ" (pending_review)
        $pendingItems = MealItem::with('mealLog')
            ->where('status', 'pending_review')
            ->orderBy('created_at', 'asc')
            ->get();

        // 🌟 ชี้ไปที่โฟลเดอร์ resources/views/foodwaste/admin/reviews/index.blade.php
        return view('foodwaste.admin.reviews.index', compact('pendingItems'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'calories' => 'required|numeric|min:0' // อนุญาตให้ใส่ 0 ได้ถ้าจำเป็น
        ]);

        $item = MealItem::findOrFail($id);
        $item->calories = $request->calories;
        $item->status = 'verified';
        $item->save();

        $mealLog = $item->mealLog;
        if ($mealLog) {
            $mealLog->total_calories = $mealLog->items()->sum('calories');
            $mealLog->save();
        }

        return back()->with('success', 'บันทึกแคลอรี่สำหรับเมนู "' . $item->menu_name . '" เรียบร้อยแล้ว! ✅');
    }
}
