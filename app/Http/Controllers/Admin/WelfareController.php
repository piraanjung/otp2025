<?php 

namespace App\Http\Controllers\Admin; 

use App\Http\Controllers\Controller; 
use App\Models\Admin\Organization; 
use App\Models\KeptKaya\KpPurchaseTransaction; 
use App\Models\WelfarePayout; 
use App\Models\KpBulkSale; 
use App\Models\WelfareConfig; 
use App\Models\WelfareMember;
use App\Models\ApprovalWorkflowStep;
use App\Models\WelfarePayoutLog;
use App\Models\WelfareContribution;
use App\Models\User;
use App\Models\WelfareConfigs;
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Facades\Http;
use Barryvdh\DomPDF\Facade\Pdf; 

class WelfareController extends Controller 
{ 
    public function getWelfareSummary() 
    { 
        $orgId = Auth::user()->org_id_fk; 
        
        $totalProfit = KpBulkSale::where('org_id_fk', $orgId)->sum('profit_amount'); 
        $totalPaid = WelfarePayout::where('org_id_fk', $orgId) 
            ->where('status', 'paid') 
            ->sum('amount'); 
        $fundBalance = $totalProfit - $totalPaid; 
        
        $recentPayouts = WelfarePayout::where('org_id_fk', $orgId) 
            ->with(['currentStep', 'logs.actionBy', 'logs.step']) 
            ->orderBy('created_at', 'desc') 
            ->paginate(10); 

        // ดึงเฉพาะสมาชิกกองทุนฌาปนกิจที่มีสถานะ active
        $welfareMembers = WelfareMember::where('org_id_fk', $orgId)
            ->where('status', 'active')
            ->with('user')
            ->get();
            
        return view('admin.welfare.dashboard', compact(
            'fundBalance', 
            'totalProfit', 
            'totalPaid', 
            'recentPayouts',
            'welfareMembers'
        )); 
    } 

    public function storePayout(Request $request) 
    { 
        $request->validate([ 
            'beneficiary_name' => 'required|string', 
            'amount' => 'required|numeric|min:0', 
            'death_certificate_img' => 'nullable|image|max:2048', 
        ]); 

        try { 
            $orgId = Auth::user()->org_id_fk; 
            
            $config = WelfareConfigs::where('org_id_fk', $orgId)->first();
            if (!$config || !$config->workflow_id) {
                return back()->with('error', 'กรุณาตั้งค่า Workflow การอนุมัติในระบบก่อนทำรายการ');
            }

            $firstStep = ApprovalWorkflowStep::where('workflow_id', $config->workflow_id)
                ->orderBy('step_order', 'asc')
                ->first();

            if (!$firstStep) {
                return back()->with('error', 'ไม่พบขั้นตอนการอนุมัติในระบบ');
            }

            // ถ้ามีการเลือกสมาชิกในระบบ ดึงชื่อสมาชิกมาใช้ได้
            $deceasedName = $request->deceased_name;
            if ($request->filled('user_id') && empty($deceasedName)) {
                $user = User::find($request->user_id);
                $deceasedName = $user ? $user->name : '-';
            }

            $payout = new WelfarePayout(); 
            $payout->org_id_fk = $orgId; 
            $payout->user_id = $request->user_id;
            $payout->deceased_name = $deceasedName; 
            $payout->beneficiary_name = $request->beneficiary_name; 
            $payout->amount = $request->amount; 
            $payout->requester_id = Auth::id(); 
            $payout->current_step_id = $firstStep->id; 
            $payout->status = 'pending_approval'; 
            $payout->note = $request->note; 

            if ($request->hasFile('death_certificate_img')) { 
                $path = $request->file('death_certificate_img')->store('welfare/payouts', 'public'); 
                $payout->death_certificate_img = $path; 
            } 

            $payout->save(); 

            WelfarePayoutLog::create([
                'payout_id' => $payout->id,
                'step_id' => $firstStep->id,
                'action_by_user_id' => Auth::id(),
                'status' => 'submitted',
                'note' => 'ยื่นคำร้องขออนุมัติเบิกจ่ายสวัสดิการเข้าสู่ระบบ'
            ]);

            return back()->with('success', 'บันทึกคำร้องขออนุมัติเบิกจ่ายสวัสดิการเรียบร้อยแล้ว'); 
        } catch (\Exception $e) { 

            return back()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage()); 
        } 
    } 

    public function approveStep(Request $request, $id)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'note' => 'nullable|string',
            'signed_doc' => 'nullable|file|mimes:pdf,jpg,png,jpeg|max:4096'
        ]);

        DB::beginTransaction();
        try {
            $payout = WelfarePayout::findOrFail($id);
            $currentStep = ApprovalWorkflowStep::findOrFail($payout->current_step_id);
            $user = Auth::user();

            $canApprove = true;//false
            if ($currentStep->specific_user_id && $currentStep->specific_user_id == $user->id) {
                $canApprove = true;
            } elseif ($currentStep->role_name && $user->hasRole($currentStep->role_name)) {
                $canApprove = true;
            }

            if (!$canApprove) {
                return back()->with('error', 'คุณไม่มีสิทธิ์อนุมัติในขั้นตอนนี้');
            }

            $filePath = null;
            if ($request->hasFile('signed_doc')) {
                $filePath = $request->file('signed_doc')->store('welfare/approvals', 'public');
            }

            if ($request->action === 'reject') {
                $payout->status = 'rejected';
                $payout->save();

                WelfarePayoutLog::create([
                    'payout_id' => $payout->id,
                    'step_id' => $currentStep->id,
                    'action_by_user_id' => $user->id,
                    'status' => 'rejected',
                    'attachment_path' => $filePath,
                    'note' => $request->note
                ]);

                DB::commit();
                return back()->with('success', 'ปฏิเสธรายการขออนุมัติเรียบร้อยแล้ว');
            }

            WelfarePayoutLog::create([
                'payout_id' => $payout->id,
                'step_id' => $currentStep->id,
                'action_by_user_id' => $user->id,
                'status' => 'approved',
                'attachment_path' => $filePath,
                'note' => $request->note
            ]);

            $nextStep = ApprovalWorkflowStep::where('workflow_id', $currentStep->workflow_id)
                ->where('step_order', '>', $currentStep->step_order)
                ->orderBy('step_order', 'asc')
                ->first();

            if ($nextStep) {
                $payout->current_step_id = $nextStep->id;
            } else {
                $payout->status = 'paid';
                $payout->paid_at = now();

                $this->processAutoDeductionAndNotify($payout);
            }

            $payout->save();
            DB::commit();

            return back()->with('success', 'บันทึกการอนุมัติขั้นตอนเรียบร้อยแล้ว');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }

    public function showApprovalPage($id)
{
    $payout = WelfarePayout::with(['requester', 'currentStep'])->findOrFail($id);
    $org = Organization::find($payout->org_id_fk);
    
    // ดึง Log ประวัติการอนุมัติ
    $payoutLogs = WelfarePayoutLog::where('payout_id', $payout->id)->with(['actionBy', 'step'])->get();
    
    // ดึงขั้นตอนการอนุมัติทั้งหมดของ Workflow นี้
    $config = WelfareConfigs::where('org_id_fk', $payout->org_id_fk)->first();
    $workflowSteps = ApprovalWorkflowStep::where('workflow_id', $config->workflow_id ?? 1)
        ->orderBy('step_order', 'asc')
        ->get();

    return view('admin.welfare.payout_approval', compact('payout', 'org', 'payoutLogs', 'workflowSteps'));
}
    private function processAutoDeductionAndNotify(WelfarePayout $payout)
    {
        $config = WelfareConfigs::where('org_id_fk', $payout->org_id_fk)->first();
        $fee = $config->contribution_fee_per_case ?? 60.00;

        // วนลูปเฉพาะสมาชิกกองทุนฌาปนกิจที่มีสถานะ Active
        $activeWelfareMembers = WelfareMember::where('org_id_fk', $payout->org_id_fk)
            ->where('status', 'active')
            ->with('user')
            ->get();

        foreach ($activeWelfareMembers as $welfareMember) {
            $member = $welfareMember->user;
            if (!$member) continue;

            $userWallet = $member->wallet_balance ?? 0;

            if ($userWallet >= $fee) {
                $member->decrement('wallet_balance', $fee);

                WelfareContribution::create([
                    'org_id_fk' => $payout->org_id_fk,
                    'user_id' => $member->id,
                    'payout_id' => $payout->id,
                    'amount' => $fee,
                    'payment_method' => 'auto_wallet',
                    'status' => 'paid',
                    'paid_at' => now(),
                ]);

                if (!empty($member->line_user_id)) {
                    $this->sendLineFlexDeductionNotice($member->line_user_id, $payout->deceased_name, $fee, $member->fresh()->wallet_balance);
                }
            } else {
                WelfareContribution::create([
                    'org_id_fk' => $payout->org_id_fk,
                    'user_id' => $member->id,
                    'payout_id' => $payout->id,
                    'amount' => $fee,
                    'payment_method' => 'pending_deduct',
                    'status' => 'pending',
                ]);
            }
        }
    }

    private function sendLineFlexDeductionNotice($lineUserId, $deceasedName, $amount, $remainingBalance)
    {
        $lineToken = env('LINE_MESSAGING_API_CHANNEL_TOKEN');
        if (!$lineToken) return;

        $flexMessage = [
            'type' => 'flex',
            'altText' => 'แจ้งการหักเงินสมทบกองทุนสวัสดิการ',
            'contents' => [
                'type' => 'bubble',
                'header' => [
                    'type' => 'box',
                    'layout' => 'vertical',
                    'backgroundColor' => '#2b8a3e',
                    'contents' => [
                        ['type' => 'text', 'text' => '🏢 กองทุนสวัสดิการฌาปนกิจ', 'weight' => 'bold', 'color' => '#ffffff', 'size' => 'sm'],
                        ['type' => 'text', 'text' => 'แจ้งหักเงินสมทบประจำเคส', 'weight' => 'bold', 'color' => '#ffffff', 'size' => 'lg', 'margin' => 'xs']
                    ]
                ],
                'body' => [
                    'type' => 'box',
                    'layout' => 'vertical',
                    'contents' => [
                        ['type' => 'text', 'text' => "กรณีเสียชีวิต: {$deceasedName}", 'size' => 'sm', 'color' => '#555555'],
                        ['type' => 'separator', 'margin' => 'md'],
                        [
                            'type' => 'box',
                            'layout' => 'horizontal',
                            'margin' => 'md',
                            'contents' => [
                                ['type' => 'text', 'text' => 'จำนวนเงินหัก:', 'size' => 'sm', 'color' => '#aaaaaa'],
                                ['type' => 'text', 'text' => '-' . number_format($amount, 2) . ' บาท', 'size' => 'sm', 'color' => '#e03131', 'align' => 'end', 'weight' => 'bold']
                            ]
                        ],
                        [
                            'type' => 'box',
                            'layout' => 'horizontal',
                            'margin' => 'xs',
                            'contents' => [
                                ['type' => 'text', 'text' => 'เงินขยะคงเหลือ:', 'size' => 'sm', 'color' => '#aaaaaa'],
                                ['type' => 'text', 'text' => number_format($remainingBalance, 2) . ' บาท', 'size' => 'sm', 'color' => '#2b8a3e', 'align' => 'end', 'weight' => 'bold']
                            ]
                        ]
                    ]
                ]
            ]
        ];

        Http::withHeaders([
            'Authorization' => 'Bearer ' . $lineToken,
            'Content-Type' => 'application/json',
        ])->post('https://api.line.me/v2/bot/message/push', [
            'to' => $lineUserId,
            'messages' => [$flexMessage]
        ]);
    }

    public function downloadVoucher($id) 
    { 
        $payout = WelfarePayout::with(['requester'])->findOrFail($id); 
        $org = Organization::find($payout->org_id_fk); 
        $data = [ 
            'payout' => $payout, 
            'org' => $org, 
            'date' => $payout->created_at->format('d/m/Y'), 
        ]; 
        $pdf = Pdf::loadView('admin.welfare.voucher_pdf', $data)->setPaper('a5', 'landscape'); 
        return $pdf->stream('voucher-' . $payout->id . '.pdf'); 
    } 

    public function downloadApprovalNote($id) 
    { 
        $payout = WelfarePayout::with(['requester'])->findOrFail($id); 
        $org = Organization::find($payout->org_id_fk); 
        $data = [ 
            'payout' => $payout, 
            'org' => $org, 
            'date' => now()->format('d/m/Y'), 
        ]; 
        $pdf = Pdf::loadView('admin.welfare.approval_pdf', $data)->setPaper('a4', 'portrait'); 
        return $pdf->stream('approval-' . $payout->id . '.pdf'); 
    } 

    public function checkEligibility($userId) 
    { 
        $config = WelfareConfigs::where('org_id_fk', Auth::user()->org_id_fk)->first(); 
        $user = User::findOrFail($userId); 
        $monthsActive = $user->created_at->diffInMonths(now()); 
        $totalWeight = KpPurchaseTransaction::where('user_id', $userId) 
            ->where('status', 'completed') 
            ->sum('total_weight'); 

        $isEligible = ($monthsActive >= $config->min_months_active) && ($totalWeight >= $config->min_total_weight); 

        return response()->json([ 
            'is_eligible' => $isEligible, 
            'details' => [ 
                'months_active' => number_format($monthsActive,2), 
                'total_weight' => number_format($totalWeight,2), 
                'required_months' => number_format($config->min_months_active, 2), 
                'required_weight' => number_format($config->min_total_weight,2), 
                'suggested_payout' => $config->default_payout_amount 
            ] 
        ]); 
    } 

    /**
     * แสดงหน้าเพจสำหรับเจ้าหน้าที่จัดการรายชื่อสมาชิกที่มียอดค้างชำระ (Pending Contributions)
     */
    /**
     * แสดงหน้าเพจสำหรับเจ้าหน้าที่จัดการรายชื่อสมาชิกที่มียอดค้างชำระ (Pending Contributions)
     */
    public function collectionIndex(Request $request)
    {
        $orgId = Auth::user()->org_id_fk ?? 1;

        $search = $request->input('search');
        $zoneId = $request->input('zone_id');

        // Query ดึงรายการสมทบที่มีสถานะ pending
        $query = WelfareContribution::where('welfare_contributions.org_id_fk', $orgId)
            ->where('welfare_contributions.status', 'pending')
            ->with(['user.user_zone', 'payout']);

        // กรองตามคำค้นหา (ชื่อ, นามสกุล, เบอร์โทร)
        if (!empty($search)) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('firstname', 'like', "%{$search}%")
                  ->orWhere('lastname', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // กรองตาม Zone ID
        if (!empty($zoneId)) {
            $query->whereHas('user', function ($q) use ($zoneId) {
                $q->where('zone_id', $zoneId);
            });
        }

        $pendingContributions = $query->orderBy('welfare_contributions.created_at', 'desc')
            ->paginate(15)
            ->appends($request->all()); // คงค่า Query String เมื่อเปลี่ยนหน้า Pagination

        // ดึงรายการโซนทั้งหมดขององค์กรสำหรับใส่ตัวเลือกใน Filter
        $zones = DB::table('zones')->where('org_id_fk', $orgId)->get();

        return view('admin.welfare.collections.index', compact('pendingContributions', 'zones', 'search', 'zoneId'));
    }

    /**
     * บันทึกยืนยันการรับเงินสด/โอนเงินจากสมาชิกที่ค้างชำระ
     */
    public function storeCollection(Request $request, $id)
    {
        $request->validate([
            'payment_method' => 'required|in:cash,transfer',
            'slip_img' => 'nullable|image|max:2048',
        ]);

        DB::beginTransaction();
        try {
            $contribution = WelfareContribution::findOrFail($id);
            
            if ($contribution->status === 'paid') {
                return back()->with('error', 'รายการนี้ได้รับการชำระเงินเรียบร้อยแล้ว');
            }

            $user = $contribution->user;
            $amount = $contribution->amount;

            $slipPath = null;
            if ($request->hasFile('slip_img')) {
                $slipPath = $request->file('slip_img')->store('welfare/slips', 'public');
            }

            // 1. เพิ่มยอดเงินเข้า Wallet ของสมาชิกตามยอดที่ต้องชำระจริง
            if ($user) {
                $user->increment('wallet_balance', $amount);
            }

            // 2. หักตัดยอดทันทีเพื่อเคลียร์สถานะค้างชำระ
            if ($user) {
                $user->decrement('wallet_balance', $amount);
            }

            // 3. อัปเดตสถานะ Contribution เป็น paid
            $contribution->update([
                'payment_method' => $request->payment_method,
                'status' => 'paid',
                'paid_at' => now(),
                'slip_img' => $slipPath,
                'note' => $contribution->note . ' (เจ้าหน้าที่บันทึกรับชำระเงินเรียบร้อยแล้ว)',
            ]);

            DB::commit();
            return back()->with('success', 'บันทึกการรับชำระเงินจากสมาชิกเรียบร้อยแล้ว');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }
}