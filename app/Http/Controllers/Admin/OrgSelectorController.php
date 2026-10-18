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
        $orgTypes = OrganizationType::all();
        $query = Organization::query();

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

        // อัปเดตสังกัดชั่วคราวเพื่อให้ Super Admin สวมบทบาทเข้าไปจัดการ อปท. นั้นๆ
        User::where('id', $user->id)->update([
            'org_id_fk'     => $org->id,
            'province_code' => $org->org_province_id_fk,
            'district_code' => $org->org_district_id_fk,
            'tambon_code'   => $org->org_tambon_id_fk,
            'zone_id'       => $org->org_zone_id_fk,
            'subzone_id'    => $org->org_zone_id_fk,
        ]);

        session([
            'active_org_id'   => $org->id,
            'active_org_name' => $org->org_name,
            'active_org_code' => $org->org_code,
        ]);

        return redirect()->route('accessmenu')->with('success', "เข้าสู่ระบบในนาม: {$org->org_name}");
    }

    // ฟังก์ชันเสริม: สั่งรีเซ็ตสังกัดกลับเป็น NULL (ไร้สังกัด) เมื่อต้องการออกจาก Context
    public function resetContext()
    {
        $user = Auth::user();

        if ($user->hasRole('Super Admin')) {
            User::where('id', $user->id)->update([
                'org_id_fk'     => null,
                'province_code' => null,
                'district_code' => null,
                'tambon_code'   => null,
                'zone_id'       => null,
                'subzone_id'    => null,
            ]);

            session()->forget(['active_org_id', 'active_org_name', 'active_org_code']);
        }

        return redirect()->route('admin.select_org')->with('info', 'ออกจากสิทธิ์การจัดการองค์กรแล้ว');
    }
}