<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Organization;
use App\Models\Admin\OrganizationType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrgSelectorController extends Controller
{
    public function index(Request $request)
{
    // ดึงประเภทหน่วยงานมาทำ Filter (เช่น อบต., เทศบาล)
    $orgTypes = OrganizationType::all();

    $query = Organization::query();//->where('status', 'active');

    // Filter ตามประเภท
    if ($request->filled('org_type')) {
        $query->where('org_type_id', $request->org_type);
    }

    $organizations = $query->orderBy('org_name')->get();

    return view('admin.select_org', compact('organizations', 'orgTypes'));
}

public function setContext(Request $request)
{
    $orgId = $request->org_id;
    $org = Organization::findOrFail($orgId);
    $user = Auth::user();

    // 🌟 อัปเดตข้อมูล Super Admin ให้กลายเป็น Context ของ Org นั้นๆ
    User::where('id', $user->id)->update([
        'org_id_fk'     => $org->id,
        'province_code' => $org->org_province_id_fk,
        'district_code' => $org->org_district_id_fk,
        'tambon_code'   => $org->org_tambon_id_fk,
        'zone_id'       => $org->org_zone_id_fk,
        'subzone_id'    => $org->org_zone_id_fk, // ถ้ามี
    ]);

    // เก็บค่าใน Session เผื่อใช้ตรวจสอบเพิ่มเติม
    session(['active_org_name' => $org->org_name]);

    return redirect()->route('accessmenu')->with('success', "เข้าสู่ระบบในนาม: {$org->org_name}");
}
}
