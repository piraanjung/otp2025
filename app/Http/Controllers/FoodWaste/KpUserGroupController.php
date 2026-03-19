<?php

namespace App\Http\Controllers\KeptKaya;

use App\Http\Controllers\Controller;
use App\Models\Admin\BudgetYear;
use App\Models\Keptkaya\WasteBin;
use App\Models\Keptkaya\KpUserGroup;
use App\Models\Keptkaya\KpUserKeptkayaInfos;
use Illuminate\Http\Request;

class KpUserGroupController extends Controller
{
    public function index()
    {
        $usergroups = kpUserGroup::where([
            'status' => 'active',
            'deleted' => '0'
        ])->get();

        return view("keptkayas.kp_usergroup.index", compact("usergroups"));
    }
    public function create()
    {
        $budgetyear = BudgetYear::where('status', 'active')->get(['id', 'budgetyear_name']);
        return view("keptkayas.kp_usergroup.create", compact('budgetyear'));
    }


    public function store(Request $request)
    {
        $validated = $request->validate(
            [
                "usergroup_name" => "required",
            ],
            [
                "required" => "ใส่ข้อมูล",
            ],
        );
        KpUserGroup::create([
            'usergroup_name' => $request->get('usergroup_name'),
            'status' => 'active',
            'deleted' => '0',
            'created_at'  => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        return redirect()->route("keptkayas.kp_usergroup.index")->with(["message" => "บันทึกข้อมูลเรียบร้อยแล้ว", 'color' => 'success']);
    }


    public function edit(KpUserGroup $kp_usergroup)
    {
        return view("keptkayas.kp_usergroup.edit", compact("kp_usergroup"));
    }

    public function update(Request $request, KpUserGroup $kp_usergroup)
    {
        $validated = $request->validate(
            [
                "usergroup_name" => "required",
                "id" => "required"
            ],
            [
                "required" => "ใส่ข้อมูล",
            ]
        );

        KpUserGroup::where('id', $kp_usergroup->id)->update([
            'usergroup_name' => $request->get('usergroup_name'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        return redirect()->route("keptkayas.kp_usergroup.index")->with(["message" => "บันทึกการแก้ไขแล้ว", 'color' => 'success']);
    }
    public function destroy(KpUserGroup $kp_usergroup)
    {
        //check ว่ามีการใช้งานออยู่ไหม ถ้ามีห้ามรบ
        // $checkUsing = KpUserKeptkayaInfos::where('kp_usergroup_idfk', $kp_usergroup->id)->count();
        // if($checkUsing > 0){
        //     return redirect()->route("keptkayas.kp_usergroup.index")->with(["message" => "มีการใข้งานอยู่ ไม่สามารถทำการลบข้อมูลได้", 'color' => 'warning']);
        // }
        // $usergroup = KpUserGroup::find($kp_usergroup->id);
        // $usergroup->delete();
        // return redirect()->route("keptkayas.kp_usergroup.index")->with(["message" => "ทำการลบข้อมูลเรียบร้อย", 'color' => 'success']);
    }
    public function infos($id)
    {
        return KpUserGroup::find($id);
    }
}
