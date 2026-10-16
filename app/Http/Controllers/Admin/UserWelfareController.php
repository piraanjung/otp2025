<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WelfareConfig;
use App\Models\WelfareMember;
use App\Models\WelfarePayout;
use App\Models\WelfarePayoutLog;
use App\Models\ApprovalWorkflowStep;
use App\Models\KeptKaya\KpPurchaseTransaction;
use App\Models\WelfareConfigs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserWelfareController extends Controller
{
    /**
     * 1. หน้าตรวจสอบสิทธิ์กองทุนสวัสดิการของสมาชิกเอง
     */
    public function checkEligibility()
    {
        $user = Auth::user();
        $orgId = $user->org_id_fk;

        // ดึงการตั้งค่าเกณฑ์สวัสดิการขององค์กร
        $config = WelfareConfigs::where('org_id_fk', $orgId)->first();

        // ตรวจสอบว่าผู้ใช้คนนี้เป็นสมาชิกกองทุนฌาปนกิจหรือไม่
        $welfareMember = WelfareMember::where('user_id', $user->id)->first();

        if (!$welfareMember || $welfareMember->status !== 'active') {
            return view('admin.welfare.member.eligibility', [
                'isMember' => false,
                'message' => 'คุณยังไม่ได้ลงทะเบียนเป็นสมาชิกกองทุนสวัสดิการฌาปนกิจ หรือสถานะถูกระงับ/พ้นสภาพ'
            ]);
        }

        // คำนวณอายุสมาชิก (นับจากวันที่สมัคร User หรือวันที่ลงทะเบียนกองทุน)
        $monthsActive = $user->created_at->diffInMonths(now());

        // คำนวณน้ำหนักขยะสะสม (กก.) จากตารางธุรกรรมธนาคารขยะ
        $totalWeight = KpPurchaseTransaction::where('user_id', $user->id)
            ->where('status', 'completed')
            ->sum('weight');

        // ตรวจสอบเกณฑ์ (กรณีเป็นสมาชิกสมทบอย่างเดียว ไม่ต้องเช็คขยะ)
        $minMonths = $config ? $config->min_months_active : 6;
        $minWeight = $config ? $config->min_total_weight : 50;

        if ($welfareMember->membership_type === 'contribution_only') {
            $isEligible = ($monthsActive >= $minMonths);
        } else {
            $isEligible = ($monthsActive >= $minMonths) && ($totalWeight >= $minWeight);
        }

        return view('admin.welfare.member.eligibility', compact(
            'welfareMember',
            'config',
            'monthsActive',
            'totalWeight',
            'isEligible',
            'minMonths',
            'minWeight'
        ))->with('isMember', true);
    }

    /**
     * 2. เปิดหน้าฟอร์มยื่นคำร้องขอรับสวัสดิการ
     */
    public function create()
    {
        $user = Auth::user();
        
        // เช็คก่อนว่าผ่านเกณฑ์ไหม ถึงยอมให้เข้าหน้ากรอกฟอร์ม
        $config = WelfareConfigs::where('org_id_fk', $user->org_id_fk)->first();
        $welfareMember = WelfareMember::where('user_id', $user->id)->where('status', 'active')->first();

        if (!$welfareMember) {
            return redirect()->route('admin.welfare.member.eligibility')
                ->with('error', 'คุณยังไม่เป็นสมาชิกกองทุน active ไม่สามารถยื่นคำร้องได้');
        }

        return view('admin.welfare.member.create', compact('user'));
    }

    /**
     * 3. บันทึกคำร้องขอรับสวัสดิการ (ส่งเข้า Workflow อัตโนมัติ)
     */
    public function store(Request $request)
    {
        $request->validate([
            'deceased_name' => 'required|string|max:255',
            'beneficiary_name' => 'required|string|max:255',
            'death_certificate_img' => 'required|image|mimes:jpeg,png,jpg,pdf|max:2048',
            'note' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $user = Auth::user();
            $orgId = $user->org_id_fk;

            $config = WelfareConfigs::where('org_id_fk', $orgId)->first();
            if (!$config || !$config->workflow_id) {
                return back()->with('error', 'องค์กรยังไม่ได้ตั้งค่าระบบ Workflow การอนุมัติสวัสดิการ');
            }

            // ดึง Step แรกของ Workflow ที่องค์กรนี้ตั้งค่าไว้
            $firstStep = ApprovalWorkflowStep::where('workflow_id', $config->workflow_id)
                ->orderBy('step_order', 'asc')
                ->first();

            if (!$firstStep) {
                return back()->with('error', 'ไม่พบขั้นตอนการอนุมัติในระบบ กรุณาติดต่อเจ้าหน้าที่');
            }

            // อัปโหลดรูปใบมรณบัตร
            $path = null;
            if ($request->hasFile('death_certificate_img')) {
                $path = $request->file('death_certificate_img')->store('welfare/payouts', 'public');
            }

            // สร้างรายการขอเบิกสวัสดิการ
            $payout = WelfarePayout::create([
                'org_id_fk' => $orgId,
                'user_id' => $user->id,
                'deceased_name' => $request->deceased_name,
                'beneficiary_name' => $request->beneficiary_name,
                'amount' => $config->default_payout_amount ?? 5000.00,
                'requester_id' => $user->id,
                'current_step_id' => $firstStep->id,
                'status' => 'pending_approval',
                'death_certificate_img' => $path,
                'note' => $request->note,
            ]);

            // บันทึก Log เริ่มต้นยื่นคำร้อง
            WelfarePayoutLog::create([
                'payout_id' => $payout->id,
                'step_id' => $firstStep->id,
                'action_by_user_id' => $user->id,
                'status' => 'submitted',
                'note' => 'สมาชิกยื่นคำร้องขอรับเงินสวัสดิการผ่านระบบออนไลน์'
            ]);

            DB::commit();

            return redirect()->route('admin.welfare.member.tracking')
                ->with('success', 'ส่งคำร้องขอรับเงินสวัสดิการสำเร็จ เจ้าหน้าที่กำลังตรวจสอบเอกสารของคุณ');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }

    /**
     * 4. หน้าติดตามสถานะคำร้องของฉัน (Tracking)
     */
    public function tracking()
    {
        $user = Auth::user();

        // ดึงประวัติคำร้องที่ User คนนี้เคยยื่นไว้
        $payouts = WelfarePayout::where('requester_id', $user->id)
            ->with(['currentStep', 'logs.actionBy', 'logs.step'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.welfare.member.tracking', compact('payouts'));
    }
}