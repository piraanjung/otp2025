<?php
namespace App\Http\Controllers\FoodWaste;

use App\Http\Controllers\Controller;
use App\Models\Admin\Staff;
use App\Models\FoodWaste\FoodWasteIssueReport;
use Illuminate\Http\Request;

class AdminIssueController extends Controller
{
    // 1. แสดงหน้ารายการปัญหาทั้งหมด
    public function index()
    {
        // ดึงข้อมูลแจ้งปัญหา เรียงสถานะ pending ขึ้นก่อน และตามด้วยเวลา
        $issues = FoodWasteIssueReport::with('user') // ดึงข้อมูล user ที่แจ้งมาด้วย
            ->orderByRaw("FIELD(status, 'pending', 'in_progress', 'resolved')")
            ->orderBy('created_at', 'desc')
            ->get();

            $staffs = Staff::with('user')->get();

        return view('foodwaste.admin.issues.index', compact('issues', 'staffs'));
    }

    // 2. อัปเดตสถานะและบันทึกข้อความจาก Admin
    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,in_progress,resolved',
            'assigned_staff_id' => 'nullable|exists:staffs,user_id', // 🌟 ตรวจสอบว่าเลือก staff ถูกต้อง
            'admin_note' => 'nullable|string|max:1000'
        ]);

        $issue = FoodWasteIssueReport::findOrFail($id);
        $issue->status = $request->status;
        $issue->assigned_staff_id = $request->assigned_staff_id; // 🌟 เซฟชื่อ staff ลงฐานข้อมูล
        $issue->admin_note = $request->admin_note; // บันทึกโน้ตจากแอดมิน
        $issue->staff_comment = $request->staff_comment; // บันทึกโน้ตจากแอดมิน
        $issue->save();

        return back()->with('success', 'อัปเดตสถานะปัญหาหมายเลข #' . $issue->id . ' เรียบร้อยแล้ว!');
    }
}
