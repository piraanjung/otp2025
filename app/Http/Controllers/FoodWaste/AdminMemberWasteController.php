<?php
namespace App\Http\Controllers\FoodWaste;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\FoodWaste\CompostBatches;
use App\Models\FoodWaste\FoodWasteLog;

class AdminMemberWasteController extends Controller
{
    // 1. หน้าแรก: สรุปรายชื่อสมาชิกและผลรวม (ที่คุณทำเสร็จแล้ว)
    public function index()
    {
        $members = User::whereHas('foodwastePreference')
            ->withSum('wasteLogs', 'weight_kg')
            ->withSum('wasteLogs', 'carbon_saved_kg')
            ->withCount('compostBatches')
            ->orderBy('waste_logs_sum_weight_kg', 'desc')
            ->paginate(20);

        return view('foodwaste.admin.members_waste.index', compact('members'));
    }

    // 2. 🌟 ฟังก์ชันที่หายไป: แสดงรายการลอตขยะ (Batches) ของสมาชิกที่เลือก
    public function showBatches($userId)
    {
        // ค้นหาข้อมูล User คนที่เลือกมา
        $member = User::findOrFail($userId);

        // ดึงลอตทั้งหมดของ User คนนี้ พร้อมคำนวณผลรวมขยะและคาร์บอนของ "แต่ละลอต"
        $batches = CompostBatches::where('user_id', $userId)
            ->withCount('wasteLogs') // นับว่าลอตนี้ทิ้งขยะมากี่ครั้ง
            ->withSum('wasteLogs', 'weight_kg') // รวมน้ำหนักขยะในลอตนี้
            ->withSum('wasteLogs', 'carbon_saved_kg') // รวมคาร์บอนในลอตนี้
            ->orderBy('start_date', 'desc')
            ->get();

        return view('foodwaste.admin.members_waste.batches', compact('member', 'batches'));
    }

    // 3. หน้าแสดงรายการขยะย่อยในแต่ละ Batch
    public function showWasteLogs($batchId)
    {
        // ค้นหาข้อมูลลอต และดึง User มาด้วยเพื่อโชว์ชื่อ
        $batch = CompostBatches::with('user')->findOrFail($batchId);

        // ดึงประวัติขยะทั้งหมดที่อยู่ในลอตนี้
        $wasteLogs = FoodWasteLog::where('batch_id', $batchId)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('foodwaste.admin.members_waste.waste_logs', compact('batch', 'wasteLogs'));
    }

    // 4. ฟังก์ชันสำหรับอัปเดตและยืนยันการตรวจสอบขยะ
    public function verifyWasteLog(Request $request, $id)
    {
        $request->validate([
            'verified_dry_weight' => 'nullable|numeric|min:0',
            'actual_moisture_avg' => 'nullable|numeric|min:0|max:100',
        ]);

        $log = FoodWasteLog::findOrFail($id);
        $log->verified_dry_weight = $request->verified_dry_weight;
        $log->actual_moisture_avg = $request->actual_moisture_avg;
        $log->is_verified = true; // เปลี่ยนสถานะเป็นตรวจสอบแล้ว

        // 💡 ออปชันเสริม: หากคุณมีสูตรคำนวณคาร์บอนเครดิตใหม่จากน้ำหนักแห้ง (verified_dry_weight) สามารถเขียนทับ $log->carbon_saved_kg ตรงนี้ได้เลยครับ

        $log->save();

        return back()->with('success', 'ตรวจสอบและยืนยันข้อมูลขยะเรียบร้อยแล้ว!');
    }
}
