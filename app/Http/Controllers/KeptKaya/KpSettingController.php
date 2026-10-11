<?php

namespace App\Http\Controllers\KeptKaya;

use App\Http\Controllers\Controller;
use App\Models\ApprovalWorkflow;
use App\Models\KeptKaya\KpSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KpSettingController extends Controller
{
    public function index()
    {
        $orgId = Auth::user()->org_id_fk;

        // โหลดค่า Setting ขององค์กร หรือใช้ค่า Default
        $settings = [
            // 1. การเงินและการถอนเงิน
            'min_reserve' => KpSetting::getValue($orgId, 'min_reserve', '300'),
            'min_withdraw' => KpSetting::getValue($orgId, 'min_withdraw', '20'),
            'cutoff_day' => KpSetting::getValue($orgId, 'cutoff_day', 'FRIDAY'),
            'cutoff_time' => KpSetting::getValue($orgId, 'cutoff_time', '12:00'),
            'bank_cashout_day' => KpSetting::getValue($orgId, 'bank_cashout_day', 'MONDAY'),
            'payout_day' => KpSetting::getValue($orgId, 'payout_day', 'TUESDAY'),
            'payout_expiry_days' => KpSetting::getValue($orgId, 'payout_expiry_days', '3'),
            'max_inactive_cycles' => KpSetting::getValue($orgId, 'max_inactive_cycles', '3'),
            'payout_approval_workflow' => KpSetting::getValue($orgId, 'payout_approval_workflow', '0'),
            // 2. สวัสดิการฌาปนกิจสงเคราะห์
            'enable_welfare' => KpSetting::getValue($orgId, 'enable_welfare', '0'),
            'welfare_mode' => KpSetting::getValue($orgId, 'welfare_mode', 'margin_only'), // margin_only | member_deduct | hybrid
            'welfare_deduct_amount' => KpSetting::getValue($orgId, 'welfare_deduct_amount', '20'),
            'allow_negative_balance' => KpSetting::getValue($orgId, 'allow_negative_balance', '1'),
            'auto_deduct_on_sale' => KpSetting::getValue($orgId, 'auto_deduct_on_sale', '1'),
           
        ];

        $approval_workflows = ApprovalWorkflow::where('is_active', 1)->get();


        return view('keptkayas.settings.index', compact('settings', 'approval_workflows'));
    }

    public function update(Request $request)
    {
        $orgId = Auth::user()->org_id_fk;

        $request->validate([
            'min_reserve' => 'required|numeric|min:0',
            'min_withdraw' => 'required|numeric|min:1',
            'cutoff_day' => 'required|string',
            'cutoff_time' => 'required|string',
            'payout_day' => 'required|string',
            'payout_expiry_days' => 'required|integer|min:1',
            'max_inactive_cycles' => 'required|integer|min:1',
            'welfare_deduct_amount' => 'required_if:enable_welfare,1|numeric|min:0',
        ]);

        $keys = [
            'min_reserve',
            'min_withdraw',
            'cutoff_day',
            'cutoff_time',
            'bank_cashout_day',
            'payout_day',
            'payout_expiry_days',
            'max_inactive_cycles',
            'enable_welfare',
            'welfare_mode',
            'welfare_deduct_amount',
            'allow_negative_balance',
            'auto_deduct_on_sale',
            'payout_approval_workflow'
        ];

        foreach ($keys as $key) {
            $value = $request->has($key) ? $request->input($key) : '0';

            // กรณี Checkbox ที่ไม่ได้ถูกเลือก
            if (in_array($key, ['enable_welfare', 'allow_negative_balance', 'auto_deduct_on_sale']) && !$request->has($key)) {
                $value = '0';
            }

            KpSetting::updateOrCreate(
                ['org_id_fk' => $orgId, 'key_name' => $key],
                ['key_value' => $value]
            );
        }

        return back()->with('success', 'บันทึกการตั้งค่าระบบธนาคารขยะเรียบร้อยแล้ว');
    }
}