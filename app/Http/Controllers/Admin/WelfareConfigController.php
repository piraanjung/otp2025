<?php 

namespace App\Http\Controllers\Admin; 

use App\Http\Controllers\Controller; 
use App\Models\KeptKaya\KpSetting;
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\DB;

class WelfareConfigController extends Controller 
{ 
    public function editConfig() 
    { 
        $orgId = Auth::user()->org_id_fk ?? 1;

        // ดึงค่าคอนฟิกทั้งหมดขององค์กรนี้จาก kp_settings
        $config = (object) [
            'min_months_active'         => KpSetting::getValue($orgId, 'welfare_min_months', 6),
            'min_total_weight'          => KpSetting::getValue($orgId, 'welfare_min_weight', 50),
            'default_payout_amount'     => KpSetting::getValue($orgId, 'welfare_default_payout', 5000),
            'minimum_fund_reserve'      => KpSetting::getValue($orgId, 'welfare_min_fund_reserve', 5000),
            'min_wallet_balance'        => KpSetting::getValue($orgId, 'welfare_min_wallet_balance', 300),
            'workflow_id'               => KpSetting::getValue($orgId, 'welfare_workflow_id', 4),
            'criteria_description'      => KpSetting::getValue($orgId, 'welfare_criteria_desc', ''),
            
            // นโยบาย 3 ส่วน
            'use_central_fund_subsidy'  => KpSetting::getValue($orgId, 'welfare_use_fund_subsidy', 1),
            'recycle_deduction_model'   => KpSetting::getValue($orgId, 'welfare_recycle_model', 'proportional_deficit'),
            'recycle_fixed_fee'         => KpSetting::getValue($orgId, 'welfare_recycle_fixed_fee', 30),
            'non_recycle_model'         => KpSetting::getValue($orgId, 'welfare_non_recycle_model', 'fixed'),
            'non_recycle_member_fee'    => KpSetting::getValue($orgId, 'welfare_non_recycle_fee', 90),
        ]; 

        $workflows = DB::table('approval_workflows')->where('org_id_fk', $orgId)->get();

        return view('admin.welfare.config', compact('config', 'workflows')); 
    } 

    public function updateConfig(Request $request) 
    { 
        $orgId = Auth::user()->org_id_fk ?? 1;

        $settings = [
            'welfare_min_months'        => $request->min_months_active,
            'welfare_min_weight'        => $request->min_total_weight,
            'welfare_default_payout'    => $request->default_payout_amount,
            'welfare_min_fund_reserve'  => $request->minimum_fund_reserve,
            'welfare_min_wallet_balance'=> $request->min_wallet_balance,
            'welfare_workflow_id'       => $request->workflow_id,
            'welfare_criteria_desc'     => $request->criteria_description,
            
            // นโยบาย 3 ส่วน
            'welfare_use_fund_subsidy'  => $request->use_central_fund_subsidy ?? 0,
            'welfare_recycle_model'     => $request->recycle_deduction_model,
            'welfare_recycle_fixed_fee' => $request->recycle_fixed_fee ?? 0,
            'welfare_non_recycle_model' => $request->non_recycle_model,
            'welfare_non_recycle_fee'   => $request->non_recycle_member_fee,
        ];

        foreach ($settings as $key => $value) {
            KpSetting::updateOrCreate(
                ['org_id_fk' => $orgId, 'key_name' => $key],
                ['key_value' => $value]
            );
        }

        return back()->with('success', 'บันทึกการตั้งค่าเกณฑ์สวัสดิการเรียบร้อยแล้ว'); 
    } 
}