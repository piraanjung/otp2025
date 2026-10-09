<?php

namespace App\Http\Controllers\KeptKaya;

use App\Http\Controllers\Controller;
use App\Models\InvTransactionApprovals;
use App\Models\KeptKaya\KpMoneyRequest;
use App\Models\KeptKaya\KpSetting;
use App\Models\KeptKaya\KpWithdrawBatch;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KpWithdrawBatchController extends Controller
{
    /**
     * แสดงรายการ Batch ทั้งหมด และคำขอที่รอการตัดรอบ
     */
    public function index()
    {
        $orgId = Auth::user()->org_id_fk ?? 1;

        // 1. ดึงคำขอถอนเงินที่ค้างอยู่ (ยังไม่ได้ผูกกับ Batch ใดๆ)
        $pendingRequests = KpMoneyRequest::where('status', 'pending')
            ->whereNull('batch_id_fk')
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get();

        $pendingTotalAmount = $pendingRequests->sum('amount');
        $pendingCount = $pendingRequests->count();

        // 2. ดึงรายการ Batch ย้อนหลัง
        $batches = KpWithdrawBatch::where('org_id_fk', $orgId)
            ->with('creator')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // 3. อ่านค่าวันตัดรอบและวันจ่ายเงินสดจาก Setting
        $cutoffDay = KpSetting::getValue($orgId, 'cutoff_day', 'FRIDAY');
        $payoutDay = KpSetting::getValue($orgId, 'payout_day', 'TUESDAY');

        return view('keptkayas.batches.index', compact(
            'pendingRequests',
            'pendingTotalAmount',
            'pendingCount',
            'batches',
            'cutoffDay',
            'payoutDay'
        ));
    }

    /**
     * ประมวลผลรวบรวมคำขอถอนเงินเป็น Batch ใหม่ (ตัดรอบวันศุกร์)
     */
    public function createBatch(Request $request)
{
    $orgId = Auth::user()->org_id_fk ?? 1;
    $userId = Auth::id();

    // 1. ดึงคำขอถอนเงินที่ค้างอยู่
    $pendingRequests = KpMoneyRequest::where('status', 'pending')
        ->whereNull('batch_id_fk')
        ->get();

    if ($pendingRequests->isEmpty()) {
        return back()->with('error', 'ไม่มีรายการถอนเงินค้างชำระสำหรับรวบรวมเป็น Batch');
    }

    // 2. ดึง Approval Workflow ที่เปิดใช้งานอยู่สำหรับองค์กรนี้
    $workflow = \App\Models\ApprovalWorkflow::with(['steps' => function ($q) {
        $q->orderBy('step_order', 'asc');
    }])->where('is_active', 1)->first();

    if (!$workflow || $workflow->steps->isEmpty()) {
        return back()->with('error', 'ไม่พบการตั้งค่าขั้นตอนการอนุมัติ (Approval Workflow) กรุณาตั้งค่า Workflow ก่อนตัดรอบ');
    }

    return DB::transaction(function () use ($orgId, $userId, $pendingRequests, $workflow) {
        $payoutDaySetting = KpSetting::getValue($orgId, 'payout_day', 'TUESDAY');
        $dayConstant = constant("\Carbon\Carbon::" . strtoupper($payoutDaySetting)) ?? Carbon::TUESDAY;
        $payoutDate = now()->next($dayConstant)->toDateString();

        $batchNo = 'BATCH-' . date('Ymd') . '-' . rand(10, 99);
        $totalAmount = $pendingRequests->sum('amount');
        $totalCount = $pendingRequests->count();

        // สร้าง Batch ใหม่
        $batch = KpWithdrawBatch::create([
            'org_id_fk' => $orgId,
            'batch_no' => $batchNo,
            'cutoff_date' => now()->toDateString(),
            'payout_date' => $payoutDate,
            'total_requests' => $totalCount,
            'total_amount' => $totalAmount,
            'status' => 'in_review',
            'current_step_order' => $workflow->steps->min('step_order') ?? 1,
            'created_by' => $userId,
        ]);

        // อัปเดตผูก batch_id_fk ให้รายการคำขอถอนเงินรายคน
        KpMoneyRequest::whereIn('id', $pendingRequests->pluck('id'))->update([
            'batch_id_fk' => $batch->id,
            'status' => 'in_review',
            'payout_date' => $payoutDate,
        ]);

        // 3. สร้าง Record ล่วงหน้าลงตารางกลาง InvTransactionApprovals (สถานะ PENDING ทุก Step)
        foreach ($workflow->steps as $step) {
            \App\Models\InvTransactionApprovals::create([
                'ref_no' => $batchNo,
                'module_name' => 'kept_kaya_withdraw',
                'approval_workflow_id' => $workflow->id,
                'step_order' => $step->step_order,
                'approver_id' => $step->specific_user_id ?? $userId, // หากระบุตัวบุคคลไว้ให้ใช้ specific_user_id
                'status' => 'PENDING',
                'comment' => null,
                'action_at' => null,
            ]);
        }

        return redirect()->route('keptkayas.batches.show', $batch->id)
            ->with('success', "รวบรวมคำขอถอนเงินจำนวน {$totalCount} รายการ ยอดรวม " . number_format($totalAmount, 2) . " บาท และสร้างรายการเสนออนุมัติเรียบร้อยแล้ว");
    });
}

    /**
     * แสดงรายละเอียดของ Batch นั้นๆ และพิมพ์เอกสารเสนออนุมัติ
     */
    public function show($id)
    {
        $batch = KpWithdrawBatch::with(['requests.user', 'creator'])->findOrFail($id);

        return view('keptkayas.batches.show', compact('batch'));
    }

    /**
     * พิมพ์บันทึกข้อความและใบสรุปยอดขอเบิกจ่ายเงินสดประจำรอบ (PDF/Print View)
     */
    public function printReport($id)
    {
        $batch = KpWithdrawBatch::with(['requests.user', 'creator'])->findOrFail($id);
        $orgId = Auth::user()->org_id_fk ?? 1;

        return view('keptkayas.batches.print_report', compact('batch'));
    }

    /**
     * อัปเดตสถานะ Batch (เช่น เมื่อผู้บริหารเซ็นอนุมัติแล้ว)
     */
    public function updateStatus(Request $request, $id)
    {
        $batch = KpWithdrawBatch::findOrFail($id);

        $request->validate([
            'status' => 'required|in:approved,rejected,completed',
        ]);

        DB::transaction(function () use ($batch, $request) {
            $batch->status = $request->status;
            $batch->save();

            // อัปเดตสถานะย่อยของ Money Requests ใน Batch ด้วย
            $requestStatus = $request->status === 'approved' ? 'ready' : $request->status;
            
            KpMoneyRequest::where('batch_id_fk', $batch->id)->update([
                'status' => $requestStatus,
            ]);
        });

        return back()->with('success', 'อัปเดตสถานะชุดเบิกถอนเงินเรียบร้อยแล้ว');
    }

    /**
     * อนุมัติ/ตีกลับ แต่ละสเต็ป ในตารางกลาง InvTransactionApprovals
     */
    public function approveStep(Request $request, $batchId)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'comment' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();
        $batch = KpWithdrawBatch::with('requests')->findOrFail($batchId);

        // 1. หาคิว/สเต็ปปัจจุบันของ Batch นี้ที่เป็น PENDING
        $currentApproval = InvTransactionApprovals::where('ref_no', $batch->batch_no)
            ->where('module_name', 'kept_kaya_withdraw')
            ->where('status', 'PENDING')
            ->orderBy('step_order', 'asc')
            ->first();

        // กรณีที่ยังไม่มี Record PENDING สร้างไว้ล่วงหน้า (เช่น เพิ่งย้ายมาใช้ตารางกลาง)
        // สามารถดึงสเต็ปจาก workflow มาบันทึกสร้างได้
        if (!$currentApproval) {
            return back()->with('error', 'ไม่พบรายการที่ต้องอนุมัติ หรือรายการนี้ถูกดำเนินการไปแล้ว');
        }

        // 2. ตรวจสอบสิทธิ์ว่าใช่ผู้อนุมัติในสเต็ปนี้จริงไหม (หรือ Super Admin)
        if ($currentApproval->approver_id != $user->id && !$user->hasRole('Super Admin')) {
            return back()->with('error', 'คุณไม่มีสิทธิ์อนุมัติในขั้นตอนการอนุมัตินี้');
        }

        return DB::transaction(function () use ($batch, $currentApproval, $request, $user) {
            $isApproved = ($request->status === 'approved');
            $statusText = $isApproved ? 'APPROVED' : 'REJECTED';

            // 3. อัปเดตสถานะสเต็ปปัจจุบันในตารางกลาง
            $currentApproval->update([
                'status' => $statusText,
                'comment' => $request->comment,
                'action_at' => now(),
            ]);

            // 4. กรณี "ตีกลับ / ไม่อนุมัติ (REJECTED)"
            if (!$isApproved) {
                $batch->update([
                    'status' => 'rejected',
                ]);

                // ปรับสถานะคำขอถอนเงินรายคนเป็น rejected และปลด Hold เงินคืนเข้าบัญชีสมาชิก
                KpMoneyRequest::where('batch_id_fk', $batch->id)->update([
                    'status' => 'rejected',
                    'hold_status' => 'released', // ปลดการอายัดเงิน
                ]);

                return back()->with('success', "ไม่อนุมัติชุดเบิกถอนเงิน {$batch->batch_no} เรียบร้อยแล้ว (ปลดอายัดเงินคืนสมุดบัญชีสมาชิกแล้ว)");
            }

            // 5. กรณี "อนุมัติ (APPROVED)" -> เช็กว่ายังมีสเต็ปถัดไปอีกไหม
            $nextApproval = InvTransactionApprovals::where('ref_no', $batch->batch_no)
                ->where('module_name', 'kept_kaya_withdraw')
                ->where('step_order', '>', $currentApproval->step_order)
                ->where('status', 'PENDING')
                ->orderBy('step_order', 'asc')
                ->first();

            if ($nextApproval) {
                // 👉 ถ้ายังมีสเต็ปถัดไป: อัปเดต current_step_order ของ Batch
                $batch->update([
                    'current_step_order' => $nextApproval->step_order,
                    'status' => 'in_review',
                ]);

                $message = "บันทึกการอนุมัติขั้นตอนที่ {$currentApproval->step_order} เรียบร้อยแล้ว (ส่งต่อขั้นตอนที่ {$nextApproval->step_order})";
            } else {
                // 👉 ถ้าไม่มีสเต็ปถัดไปแล้ว (อนุมัติครบถ้วน): อัปเดตสถานะ Batch เป็น approved และคำขอเป็น ready
                $batch->update([
                    'status' => 'approved',
                ]);

                KpMoneyRequest::where('batch_id_fk', $batch->id)->update([
                    'status' => 'ready', // พร้อมจ่ายเงินสดวันอังคาร
                ]);

                $message = "อนุมัติครบทุกขั้นตอนเรียบร้อยแล้ว ชุดเบิกถอน {$batch->batch_no} พร้อมสำหรับจ่ายเงินสดในวันนัดหมาย";
            }

            return back()->with('success', $message);
        });
    }
}