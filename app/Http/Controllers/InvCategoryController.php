<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InvCategory;
use Illuminate\Support\Facades\Auth;

class InvCategoryController extends Controller
{
    // แสดงรายการ + ฟอร์มเพิ่ม
    public function index()
    {
        $user = Auth::user();
        $categories = InvCategory::where('org_id_fk', $user->org_id_fk)
                        ->orderBy('name', 'asc')
                        ->get();

        return view('inventory.settings.inv_category_index', compact('categories'));
    }

    // บันทึก
    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:100']);
        $user = Auth::user();

        // เช็คซ้ำใน Org เดียวกัน
        $exists = InvCategory::where('org_id_fk', $user->org_id_fk)
                             ->where('name', $request->name)->exists();

        if ($exists) {
            return back()->with('error', 'หมวดหมู่นี้มีอยู่แล้ว');
        }

        InvCategory::create([
            'org_id_fk' => $user->org_id_fk,
            'name' => $request->name
        ]);

        return back()->with('success', 'เพิ่มหมวดหมู่เรียบร้อย');
    }

    // ลบ
    public function destroy($id)
    {
        $category = InvCategory::findOrFail($id);
        
        // ควรเช็คก่อนลบว่ามี Item ผูกอยู่ไหม? (เพื่อความปลอดภัย)
        if($category->items()->count() > 0) {
             return back()->with('error', 'ไม่สามารถลบได้ เนื่องจากมีพัสดุอยู่ในหมวดหมู่นี้');
        }

        $category->delete();
        return back()->with('success', 'ลบข้อมูลเรียบร้อย');
    }
}