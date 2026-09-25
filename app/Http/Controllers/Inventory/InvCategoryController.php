<?php

namespace App\Http\Controllers\Inventory;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ApprovalWorkflow;
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

        // ➕ ดึงรายการ Workflow ที่เปิดใช้งานอยู่มาส่งให้หน้า View
        $workflows = ApprovalWorkflow::where('is_active', true)->get();

        // ➕ ส่ง $workflows ไปด้วยผ่าน compact
        return view('inventory.settings.category.index', compact('categories', 'workflows'));
    }


    // บันทึก
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'approval_workflow_id' => 'nullable|exists:approval_workflows,id',
        ]);
        $user = Auth::user();

        // เช็คซ้ำใน Org เดียวกัน
        $exists = InvCategory::where('org_id_fk', $user->org_id_fk)
                             ->where('name', $request->name)->exists();

        if ($exists) {
            return back()->with('error', 'หมวดหมู่นี้มีอยู่แล้ว');
        }

        InvCategory::create([
            'org_id_fk' => $user->org_id_fk,
            'name' => $request->name,
            'approval_workflow_id' => $request->approval_workflow_id,
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

    public function edit($id)
    {
        $user = Auth::user();
        $category = InvCategory::where('org_id_fk', $user->org_id_fk)->findOrFail($id);
        
        // ดึง Workflow ที่เปิดใช้งานอยู่มาให้เลือก
        $workflows = ApprovalWorkflow::where('is_active', true)->get();

        return view('inventory.settings.category.edit', compact('category', 'workflows'));
    }

    // 2. บันทึกการอัปเดตหมวดหมู่
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $category = InvCategory::where('org_id_fk', $user->org_id_fk)->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:inv_categories,name,' . $id, // ปรับชื่อตารางให้ตรงกับ DB จริงของคุณ
            'approval_workflow_id' => 'nullable|exists:approval_workflows,id',
        ]);

        $category->update([
            'name' => $request->name,
            'approval_workflow_id' => $request->approval_workflow_id,
        ]);

        return redirect()->route('inventory.categories.index')->with('success', 'อัปเดตหมวดหมู่สำเร็จ');
    }

}
