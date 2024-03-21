<?php

namespace App\Http\Controllers;

use App\Models\Province;
use App\Subzone;
use App\Models\TabwaterMeter;
use App\Models\User;
use App\UserProfile;
use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StaffController extends Controller
{
    public function index()
    {
        $checkZone = Zone::all();

        $staffsQuery = User::with('user_profile', 'user_profile.zone', 'user_profile.subzone')
            ->whereIn('user_cat_id', [1, 2, 4])
            ->where('status', '=', 'active')
            ->get();
        $staffs = collect($staffsQuery)->sort();
        foreach ($staffs as $staff) {
            if ($staff->user_cat_id == 1) {
                $staff->position = 'ผู้ดูแลระบบ';
            } else if ($staff->user_cat_id == 2) {
                $staff->position = 'การเงิน';
            } else if ($staff->user_cat_id == 4) {
                $staff->position = 'พนักงานจดมิเตอร์';
            }

        }
        return view('staff.index', compact('staffs'));
    }

    public function create($renew = 0, $user_id = 0)
    {
        $user = new User();
        $provinces = Province::all();
        // $usercategories = Usercategory::where('id', '<>', 3)->get();

        $zones = Zone::all();

        return view('staff.create', compact('user', 'provinces', 'usercategories', 'zones'));
    }

    public function store(REQUEST $request)
    {
        date_default_timezone_set('Asia/Bangkok');
        $user = new User;
        $user->username = $request->get('username');
        $user->password = Hash::make($request->get('password'));
        $user->email = $user->username . '@gmail.com';
        $user->user_cat_id = $request->get('usercategory_id');
        $user->created_at = date('Y-m-d H:i:s');
        $user->updated_at = date('Y-m-d H:i:s');
        $user->save();

        $user_id = $user->id;
        $user_profile = new UserProfile;
        $user_profile->user_id = $user->id;
        $user_profile->name = $request->get('name');
        $user_profile->gender = $request->get('gender');
        $user_profile->id_card = $request->get('id_card');
        $user_profile->phone = $request->get('phone');
        $user_profile->address = $request->get('address');
        $user_profile->province_code = $request->get('province_code');
        $user_profile->district_code = $request->get('district_code');
        $user_profile->tambon_code = $request->get('tambon_code');
        $user_profile->zone_id = $request->get('zone_id');
        $user_profile->subzone_id = $request->get('zone_id');
        $user_profile->created_at = date('Y-m-d H:i:s');
        $user_profile->updated_at = date('Y-m-d H:i:s');
        $user_profile->save();

        //กำหนด user_role  ให้กับเจ้าหน้าที่
        $user = User::find($user->id);
        if ($user->user_cat_id == 1) {
            $user->attachRole('superadministrator');
        } elseif ($user->user_cat_id == 2) {
            $user->attachRole('accounting');
        } elseif ($user->user_cat_id == 4) {
            $user->attachRole('twman');
        }

        return redirect('staff')->with(['massage' => 'ทำการบันทึกข้อมูลเรียบร้อยแล้ว', 'color' => 'success']);
    }

    public function edit($id)
    {
        $user = User::where('id', $id)
            ->with('user_profile')
            ->get()
            ->first();
        $provinces = Province::all();
        $usercategories = Usercategory::where('id', '<>', 3)->get();
        $zones = Zone::all();
        $tabwatermeters = TabwaterMeter::all(['id', 'typemetername']);
        $user_subzone = Subzone::where('zone_id', $user->undertake_zone_id)->get();
        return view('staff.edit', compact('user', 'zones', 'provinces', 'usercategories',
            'user_subzone'));
    }

    public function update(REQUEST $request, $id)
    {
        date_default_timezone_set('Asia/Bangkok');

        // แก้ไขข้อมูล UserProfile
        $user = User::where('id', $id)->update([
            'username' => $request->get('username'),
            // 'email' => 'username.'gmail.com',
            'user_cat_id' => $request->get('usercategory_id'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        if ($request->get('password') != '') {
            $user = User::where('id', $id)->update([
                'password' => Hash::make($request->get('password')),
            ]);
        }

        $userProfile = UserProfile::where('user_id', $id)->update([
            'name' => $request->get('name'),
            'id_card' => $request->get('id_card'),
            'phone' => $request->get('phone'),
            'gender' => $request->get('gender'),
            'address' => $request->get('address'),
            'zone_id' => $request->get('zone_id'),
            'subzone_id' => $request->get('zone_id'),
            'tambon_code' => $request->get('tambon_code'),
            'district_code' => $request->get('district_code'),
            'province_code' => $request->get('province_code'),
        ]);

        return redirect('staff')->with(['massage' => 'ทำการบันทึกการแก้ไขเรียบร้อยแล้ว', 'color' => 'warning']);

    }

    public function delete($id)
    {
        $user = User::where('id', $id)->update([
            'status' => 'deleted',
        ]);

        $user_profile = UserProfile::where('user_id', $id)->update([
            'status' => 0,
        ]);

        return redirect('staff')->with(['massage' => 'การลบข้อมูลเรียบร้อยแล้ว', 'color' => 'danger']);

    }
}
