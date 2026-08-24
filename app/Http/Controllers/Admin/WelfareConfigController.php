<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KpWelfareConfigs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WelfareConfigController extends Controller
{
    public function editConfig()
{
    $config = KpWelfareConfigs::where('org_id_fk', Auth::user()->org_id_fk)->first();

    // ถ้ายังไม่มีค่า ให้สร้างค่าเริ่มต้นไว้ก่อน
    if (!$config) {
        $config = KpWelfareConfigs::create([
            'org_id_fk' => Auth::user()->org_id_fk,
            'min_months_active' => 6,
            'min_total_weight' => 50,
            'default_payout_amount' => 5000
        ]);
    }

    return view('admin.welfare.config', compact('config'));
}

public function updateConfig(Request $request)
{
    $config = KpWelfareConfigs::where('org_id_fk',Auth::user()->org_id_fk)->first();
    $config->update($request->all());

    return back()->with('success', 'บันทึกการตั้งค่าเกณฑ์สวัสดิการเรียบร้อยแล้ว');
}
}
