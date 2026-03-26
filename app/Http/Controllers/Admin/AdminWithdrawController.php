<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KeptKaya\KpMoneyRequest;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AdminWithdrawController extends Controller
{
    // แสดงรายการคำขอทั้งหมดที่ยังไม่ได้จ่ายเงิน
    public function index()
    {
        $requests = KpMoneyRequest::whereIn('status', ['pending', 'ready'])
                    ->with('user')
                    ->orderBy('payout_date', 'asc')
                    ->get();

        return view('admin.withdraws.index', compact('requests'));
    }

    // ฟังก์ชันยืนยันการจ่ายเงินด้วยรหัสลับ
    public function verifyCode(Request $request, $id)
    {
        $withdraw = KpMoneyRequest::findOrFail($id);

        // ตรวจสอบรหัสลับที่ Admin กรอกมา
        if ($request->input_code !== $withdraw->verification_code) {
            return back()->with('error', 'รหัสยืนยันไม่ถูกต้อง กรุณาตรวจสอบรหัสจากมือถือผู้รับเงิน');
        }

        // ถ้ารหัสถูกต้อง -> อัปเดตสถานะและบันทึกผู้จ่ายเงิน
        $withdraw->update([
            'status' => 'completed',
            'admin_id' => Auth::id(),
            'completed_at' => now(),
        ]);

        return back()->with('success', 'ยืนยันการจ่ายเงินเรียบร้อยแล้ว จำนวน ' . number_format($withdraw->amount, 2) . ' บาท');
    }

    public function payoutSummary()
{
    // ดึงยอดรวมเงินที่ต้องจ่าย แยกตามวันที่นัดรับ (เฉพาะรายการที่ยังไม่ได้จ่าย)
    $summary = KpMoneyRequest::whereIn('status', ['pending', 'ready'])
        ->selectRaw('payout_date, COUNT(*) as total_requests, SUM(amount) as total_amount')
        ->groupBy('payout_date')
        ->orderBy('payout_date', 'asc')
        ->get();

    // หา "วันอังคารหน้า" เพื่อเน้นย้ำยอดที่ต้องเตรียมเร็วๆ นี้
    $nextTuesday = now()->next(\Carbon\Carbon::TUESDAY)->toDateString();

    $urgentAmount = $summary->where('payout_date', $nextTuesday)->first()->total_amount ?? 0;

    return view('admin.withdraws.summary', compact('summary', 'urgentAmount', 'nextTuesday'));
}
}
