<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Api\FunctionsController;
use App\Http\Controllers\Controller;
use App\Models\Admin\UserProfile;
use App\Models\MeterType;
use App\Models\NumberSequence;
use App\Models\User;
use App\Models\UserMerterInfo;
use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        $users = User::role("user")
                ->get();
                $usertype = "user";

        return view('admin.users.index', compact('users', 'usertype'));
    }
    public function staff()
    {
        $users = User::with('roles')
                ->get()->filter(
                fn ($user) => $user->roles->whereIn('name', ["admin", "tabwater man", "finance"])->toArray()
            );
        $usertype = "staff";
        return view('admin.users.index', compact('users', 'usertype'));
    }
    public function create(){
        $meter_sq_number = NumberSequence::get('meternumber');
        $zones = Zone::all();
        $meter_types = MeterType::all();
        $meternumber = $this->createInvoiceNumberString($meter_sq_number[0]->meternumber);
        $username = "user".$meter_sq_number[0]->meternumber;
        $password = "user".substr($meternumber,3);

        return view('admin.users.create', compact('meternumber', 'zones', 'meter_types', 'username', 'password'));
    }
    public function store(Request $request){
        $request->validate(
            [
                "name" => 'required',
                "gender" => 'required|in:w,m',
                "id_card" => 'required',
                "phone" => 'required',
                "address" => 'required',
                "province_code" => 'required|integer',
                "metertype_id" => 'required|integer',
                "zone_id" => 'required',
                "undertake_zone_id" => 'required|integer',
                "username" => 'required',
                "password" => 'required',

            ],
            [
                "required" => "ใส่ข้อมูล",
                "in" => "เลือกข้อมูล",
                "integer" => "เลือกข้อมูล",
            ],

        );

        //user table
        $user = User::create([
            "username"=> $request->username,
            "password"=> Hash::make($request->password),
            "email"=> $request->username."gmail.com",
            "status" => 'active'
        ]);
        //user_profile table
        $user_profile = UserProfile::create([
            "user_id" => $user->id,
            "name" => $request->get("name"),
            "id_card" => $request->get("id_card"),
            "phone" => $request->get("phone"),
            "gender" => $request->get("gender"),
            "address" => $request->get("address"),
            "zone_id" => $request->get("zone_id"),
            "subzone_id" => $request->get("zone_id"),
            "tambon_code" => $request->get("tambon_code"),
            "district_code" => $request->get("district_code"),
            "province_code" => $request->get("province_code"),
            "status" => $request->get("status")
        ]);
        //usermeterinfo table
        UserMerterInfo::create([
        "user_id" => $user->id,
        "meternumber" => $request->get('meternumber'),
        "metertype_id" => $request->get('metertype_id'),
        "undertake_zone_id" => $request->get('undertake_zone_id'),
        "undertake_subzone_id" => $request->get('undertake_subzone_id'),
        "acceptace_date" => date('Y-m-d'),
        "payment_id" => $request->get('payment_id'),
        "discounttype" => $request->get('discounttype'),
        "recorder_id" => Auth::id()
        ]);
        //model_has_role table
        $user->assignRole($request->get("role"));
        //sequnce number +
        NumberSequence::where('nsq_id', 1)->update([
            'meternumber' =>$user->id +1
        ]);
        return redirect()->route('admin.users.index')->with(['message','บันทึกแล้ว', 'color' => 'success']);
    }

    public function edit(User $user){
        $zones = Zone::all();
        $meter_types = MeterType::all();
        $subzones = $user->usermeter_info->zone->subzone;
        return view('admin.users.edit', compact('user', 'zones', 'meter_types', 'subzones'));
    }

    public function update(Request $request, User $user){
        $request->validate(
            [
                "name" => 'required',
                "gender" => 'required|in:w,m',
                "id_card" => 'required',
                "phone" => 'required',
                "address" => 'required',
                "province_code" => 'required|integer',
                "metertype_id" => 'required|integer',
                "zone_id" => 'required',
                "undertake_zone_id" => 'required|integer',
                "username" => 'required',
                "password" => 'required',

            ],
            [
                "required" => "ใส่ข้อมูล",
                "in" => "เลือกข้อมูล",
                "integer" => "เลือกข้อมูล",
            ],

        );

        //user table
        User::where('id', $user->id)->update([
            "username"=> $request->username,
            "password"=> Hash::make($request->password),
            "email"=> $request->username."gmail.com",
            "status" => 'active'
        ]);
        //user_profile table
        UserProfile::where('user_id', $user->id)->update([
            "user_id" => $user->id,
            "name" => $request->get("name"),
            "id_card" => $request->get("id_card"),
            "phone" => $request->get("phone"),
            "gender" => $request->get("gender"),
            "address" => $request->get("address"),
            "zone_id" => $request->get("zone_id"),
            "subzone_id" => $request->get("zone_id"),
            "tambon_code" => $request->get("tambon_code"),
            "district_code" => $request->get("district_code"),
            "province_code" => $request->get("province_code"),
            "status" => $request->get("status")
        ]);
        //usermeterinfo table
        UserMerterInfo::where('user_id', $user->id)->update([
        "user_id" => $user->id,
        "meternumber" => $request->get('meternumber'),
        "metertype_id" => $request->get('metertype_id'),
        "undertake_zone_id" => $request->get('undertake_zone_id'),
        "undertake_subzone_id" => $request->get('undertake_subzone_id'),
        "acceptace_date" => date('Y-m-d'),
        "payment_id" => $request->get('payment_id'),
        "discounttype" => $request->get('discounttype'),
        "recorder_id" => Auth::id()
        ]);

        return redirect()->route('admin.users.index')->with(['message','บันทึกแล้ว', 'color' => 'success']);
    }
    public function show(User $user)
    {
        $roles = Role::all();
        $permissions = Permission::all();

        return view('admin.users.role', compact('user', 'roles', 'permissions'));
    }
    public function assignRole(Request $request, User $user)
    {
        if ($user->hasRole($request->role)) {
            return back()->with('message', 'Role exists.');
        }

        $user->assignRole($request->role);
        return back()->with('message', 'Role assigned.');
    }

    public function removeRole(User $user, Role $role)
    {
        if ($user->hasRole($role)) {
            $user->removeRole($role);
            return back()->with('message', 'Role removed.');
        }

        return back()->with('message', 'Role not exists.');
    }
    public function givePermission(Request $request, User $user)
    {
        if ($user->hasPermissionTo($request->permission)) {
            return back()->with('message', 'Permission exists.');
        }
        $user->givePermissionTo($request->permission);
        return back()->with('message', 'Permission added.');
    }

    public function revokePermission(User $user, Permission $permission)
    {
        if ($user->hasPermissionTo($permission)) {
            $user->revokePermissionTo($permission);
            return back()->with('message', 'Permission revoked.');
        }
        return back()->with('message', 'Permission does not exists.');
    }
    public function destroy(User $user)
    {
        if ($user->hasRole('admin')) {
            return back()->with('message', 'you are admin.');
        }
        $user->delete();

        return back()->with('message', 'User deleted.');
    }
    public static function createInvoiceNumberString($id)
    {
        $invString = '';
        if ($id < 10) {
            $invString = '0000' . $id;
        } else if ($id >= 10 && $id < 100) {
            $invString = '000' . $id;
        } else if ($id >= 100 && $id < 1000) {
            $invString = '00' . $id;
        } elseif ($id >= 1000 && $id < 9999) {
            $invString = '0' . $id;
        } else {
            $invString = $id;
        }
        return "HS-".$invString;

    }
}
