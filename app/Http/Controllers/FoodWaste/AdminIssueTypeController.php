<?php

namespace App\Http\Controllers\FoodWaste;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FoodWaste\FoodWasteIssueType; // อย่าลืมสร้าง Model ตัวนี้นะครับ
use Illuminate\Support\Facades\Auth;

class AdminIssueTypeController extends Controller
{
    /**
     * 1. แสดงหน้าจอจัดการหมวดหมู่ปัญหาทั้งหมด (Read)
     */
    public function index()
    {
        // ดึงข้อมูลหมวดหมู่ทั้งหมด เรียงจาก ID ล่าสุดขึ้นก่อน
        $issueTypes = FoodwasteIssueType::orderBy('id', 'desc')->get();

        // ส่งตัวแปร $issueTypes ไปให้หน้า Blade ที่เราเพิ่งสร้าง
        return view('foodwaste.admin.issue_types.index', compact('issueTypes'));
    }

    /**
     * 2. บันทึกหมวดหมู่ปัญหาใหม่ (Create)
     */
    public function store(Request $request)
    {
        // เปลี่ยนมา Validate ว่าต้องเป็น Array และแต่ละช่องห้ามว่าง
        $request->validate([
            'names' => 'required|array|min:1',
            'names.*' => 'required|string|max:255'
        ]);

        $count = 0; // ตัวแปรไว้นับว่าบันทึกสำเร็จกี่อัน

        // ใช้ลูป foreach เพื่อวนเซฟทีละชื่อที่ส่งมาจากหน้าเว็บ
        foreach ($request->names as $name) {
            // เช็คกันเหนียว เผื่อ User พิมพ์แต่ Spacebar มา
            if (!empty(trim($name))) {
                FoodwasteIssueType::create([
                    'name' => trim($name),
                    'is_active' => true,
                ]);
                $count++;
            }
        }

        return back()->with('success', 'เพิ่มหมวดหมู่ปัญหาใหม่สำเร็จจำนวน ' . $count . ' รายการ!');
    }

    /**
     * 3. อัปเดตแก้ไขชื่อหมวดหมู่ (Update)
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $type = FoodwasteIssueType::findOrFail($id);
        $type->update([
            'name' => $request->name
        ]);

        return back()->with('success', 'แก้ไขชื่อหมวดหมู่เป็น "'.$request->name.'" เรียบร้อยแล้ว!');
    }

    /**
     * 4. สลับสถานะ เปิด/ปิด การใช้งาน (Toggle Active/Inactive)
     */
    public function toggleActive($id)
    {
        $type = FoodwasteIssueType::findOrFail($id);

        // สลับค่า boolean (ถ้าเป็น true จะกลายเป็น false, ถ้า false จะกลายเป็น true)
        $type->is_active = !$type->is_active;
        $type->save();

        // สร้างข้อความแจ้งเตือนให้สอดคล้องกับสถานะใหม่
        $statusMsg = $type->is_active ? 'เปิดการใช้งาน' : 'ปิดการใช้งาน';

        return back()->with('success', "{$statusMsg}หมวดหมู่ '{$type->name}' เรียบร้อยแล้ว!");
    }
}
