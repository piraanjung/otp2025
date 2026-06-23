<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Organization;
use App\Models\KeptKaya\KpPurchaseTransaction;
use App\Models\kp_welfare_configs;
use App\Models\KpWelfarePayout;
use App\Models\KpBulkSale;
use App\Models\KpWelfareConfigs;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class WelfareController extends Controller
{
    // 1. หน้า Dashboard สรุปยอดเงินกองกลาง
    public function getWelfareSummary()
    {
        $orgId = Auth::user()->org_id_fk;

        // คำนวณกำไรสะสมทั้งหมดจากการขายขยะล๊อตใหญ่
        $totalProfit = KpBulkSale::where('org_id_fk', $orgId)->sum('profit_amount');

        // คำนวณยอดเงินที่จ่ายสวัสดิการไปแล้ว (เฉพาะรายการที่จ่ายสำเร็จ)
        $totalPaid = KpWelfarePayout::where('org_id_fk', $orgId)
                    ->where('status', 'paid')
                    ->sum('amount');

        $fundBalance = $totalProfit - $totalPaid;

        // ดึงรายการจ่ายล่าสุด
        $recentPayouts = KpWelfarePayout::where('org_id_fk', $orgId)
                        ->orderBy('created_at', 'desc')
                        ->paginate(10);

        return view('admin.welfare.dashboard', compact('fundBalance', 'totalProfit', 'totalPaid', 'recentPayouts'));
    }

    // 2. บันทึกการจ่ายเงินสวัสดิการ (ฌาปนกิจ)
    public function storePayout(Request $request)
    {
        $request->validate([
            'deceased_name' => 'required|string',
            'beneficiary_name' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'death_certificate_img' => 'nullable|image|max:2048', // จำกัดขนาด 2MB
        ]);

        try {
            $orgId = Auth::user()->org_id_fk;

            // ตรวจสอบยอดเงินในกองทุนก่อนจ่าย
            $totalProfit = KpBulkSale::where('org_id_fk', $orgId)->sum('profit_amount');
            $totalPaid = KpWelfarePayout::where('org_id_fk', $orgId)->where('status', 'paid')->sum('amount');
            $currentBalance = $totalProfit - $totalPaid;

            if ($request->amount > $currentBalance) {
                return back()->with('error', 'ยอดเงินในกองทุนไม่เพียงพอสำหรับการเบิกจ่าย');
            }

            $payout = new KpWelfarePayout();
            $payout->org_id_fk = $orgId;
            $payout->deceased_name = $request->deceased_name;
            $payout->beneficiary_name = $request->beneficiary_name;
            $payout->amount = $request->amount;
            $payout->requester_id = Auth::id();
            $payout->status = 'paid'; // ในที่นี้สมมติว่าจ่ายทันที หรือจะตั้งเป็น pending ก่อนก็ได้
            $payout->paid_at = now();
            $payout->note = $request->note;

            // จัดการอัปโหลดรูปใบมรณบัตร
            if ($request->hasFile('death_certificate_img')) {
                $path = $request->file('death_certificate_img')->store('welfare/payouts', 'public');
                $payout->death_certificate_img = $path;
            }

            $payout->save();

            return back()->with('success', 'บันทึกการจ่ายเงินสวัสดิการเรียบร้อยแล้ว');

        } catch (\Exception $e) {
            return back()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }


public function downloadVoucher($id)
{
    $payout = KpWelfarePayout::with(['requester', 'approver'])->findOrFail($id);
    $org = Organization::find($payout->org_id_fk);

    // ข้อมูลที่จะส่งไปใน PDF
    $data = [
        'payout' => $payout,
        'org'    => $org,
        'date'   => $payout->created_at->format('d/m/Y'),
    ];

    // โหลดวิวและตั้งค่ากระดาษ A5 (แนวนอน) เพื่อประหยัดกระดาษตามสไตล์เทศบาล
    $pdf = Pdf::loadView('admin.welfare.voucher_pdf', $data)
              ->setPaper('a5', 'landscape');

    return $pdf->stream('voucher-' . $payout->id . '.pdf');
}

public function downloadApprovalNote($id)
{
    $payout = KpWelfarePayout::with(['user', 'requester'])->findOrFail($id);
    $org = Organization::find($payout->org_id_fk);

    $data = [
        'payout' => $payout,
        'org'    => $org,
        'date'   => now()->format('d/m/Y'),
    ];

    $pdf = Pdf::loadView('admin.welfare.approval_pdf', $data)
              ->setPaper('a4', 'portrait'); // ใบขออนุมัติมักใช้ A4 แนวตั้ง

    return $pdf->stream('approval-' . $payout->id . '.pdf');
}

public function checkEligibility($userId)
{
    $config = KpWelfareConfigs::where('org_id_fk', Auth::user()->org_id_fk)->first();
    $user = User::findOrFail($userId);

    // 1. ตรวจสอบระยะเวลาการเป็นสมาชิก
    $monthsActive = $user->created_at->diffInMonths(now());

    // 2. ตรวจสอบน้ำหนักขยะสะสม (จากตารางธุรกรรมขยะที่คุณมี)
    $totalWeight = KpPurchaseTransaction::where('user_id', $userId)
                    ->where('status', 'completed')
                    ->sum('weight');

    $isEligible = ($monthsActive >= $config->min_months_active) && ($totalWeight >= $config->min_total_weight);

    return response()->json([
        'is_eligible' => $isEligible,
        'details' => [
            'months_active' => $monthsActive,
            'total_weight' => $totalWeight,
            'required_months' => $config->min_months_active,
            'required_weight' => $config->min_total_weight,
            'suggested_payout' => $config->payout_amount
        ]
    ]);
}
}
