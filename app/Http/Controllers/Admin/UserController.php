<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Api\FunctionsController;
use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Accounting;
use App\Models\Invoice;
use App\Models\InvoiceHistoty;
use App\Models\MeterType;
use App\Models\SequenceNumber;
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
        $users = UserMerterInfo::with([
            'invoice' => function($q){
                return $q->select('meter_id_fk','status');
            },
            'user' => function($q){
                return $q->select('id','prefix','firstname', 'lastname', 'status');
            },
            ])->get();
        $user_deleted =  collect($users)->filter(function($v){
            return $v->deleted == '1';
        })->groupBy('user_id');
        $user_active =  collect($users)->filter(function($v){
            return $v->status == 'active';
        })->groupBy('user_id');

       
        
        $usertype = "user";
        $zones = Zone::all();
        return view('admin.users.index', compact( 'users', 'usertype', 'zones', 'user_deleted', 'user_active'));
    }

    public function users_search(Request $request)
    {
        $users = User::role("user")->whereIn("zone_id", $request->input("zone"))->get();
        $usertype = "user";
        $zones = Zone::all();
        return view('admin.users.index', compact('users', 'usertype', 'zones'));
    }


      public function staff()
    {
        $users = User::with('roles')
            ->get()->filter(
                fn($user) => $user->roles->whereIn('name', ["admin", "tabwater man", "finance"])->toArray()
            );
        $usertype = "staff";
        return view('admin.users.index', compact('users', 'usertype'));
    }
    public function create()
    {
        $meter_sq_number    = SequenceNumber::get();
        $zones              = Zone::all();
        $meter_types = MeterType::all();
        $usergroups         = Role::get(['id', 'name']);
        $usernumber         = FunctionsController::createInvoiceNumberString($meter_sq_number[0]->user);
        $username           = "user" . $meter_sq_number[0]->user;
        $meternumber        = FunctionsController::createInvoiceNumberString($meter_sq_number[0]->tabmeter);
        $password           = "user" . substr($usernumber, 3);
        $factory_no         = "";

        return view('admin.users.create', compact('usernumber', 'meternumber', 'factory_no', 'zones', 'usergroups', 'meter_types', 'username', 'password'));
    }
    public function store(Request $request)
    {
        date_default_timezone_set('Asia/Bangkok');
        $request->validate(
            [
                "prefix_select"     => 'required',
                "firstname"         => 'required',
                // "lastname"          => 'required',
                "factory_no"        => 'required',
                "gender"            => 'required|in:w,m',
                "id_card"           => 'required',
                "phone"             => 'required',
                "address"           => 'required',
                "metertype_id"      => 'required|integer',
                "zone_id"           => 'required',
                "undertake_zone_id" => 'required|integer',
                "province_code"     => 'required|integer',
                "username"          => 'required',
                "password"          => 'required',
            ],
            [
                "required"      => "ใส่ข้อมูล",
                "in"            => "เลือกข้อมูล",
                "integer"       => "เลือกข้อมูล",
            ],

        );
        $number_sequence = SequenceNumber::where('id', 1)->get();
        try {

            $user = User::create([
                "id"            => $number_sequence[0]->user,
                "username"      => $request->username,
                "password"      => Hash::make($request->password),
                "email"         => $request->email,
                "prefix"        => $request->get('prefix_select') == "other" ? $request->get('prefix_text') : $request->get('prefix_select'),
                "firstname"     => $request->get('firstname'),
                 "lastname"      => $request->get('lastname'),
                'settings_id_fk' => 2,
                'name'          => $request->get('firstname') . " " . $request->get('lastname'),
                "id_card"       => $request->get('id_card'),
                "phone"         => $request->get('phone'),
                "gender"        => $request->get('gender'),
                "address"       => $request->get('address'),
                "zone_id"       => $request->get('zone_id'),
                "subzone_id"    => $request->get('undertake_subzone_id'),
                "tambon_code"   => $request->get('tambon_code'),
                "district_code" => $request->get('district_code'),
                "province_code" => $request->get('province_code'),
                "role_id"       => 3,
                "status"        => 1,
                "created_at"    => date("Y-m-d H:i:s"),
                "updated_at"    => date("Y-m-d H:i:s"),
            ]);
        } catch (\Throwable $th) {
            return $this->show('store', $th->getMessage());
        }
        //model_has_role table
        $user->assignRole("user");

        //usermeterinfo table
        try {       
        UserMerterInfo::create([
            "meter_id"              => $number_sequence[0]->tabmeter,
            "user_id"               => $number_sequence[0]->user,
            "submeter_name" => $request->get('submeter_name'),
            "meternumber"           => FunctionsController::createMeterNumberString($number_sequence[0]->tabmeter),
            "undertake_zone_id"     => $request->get('undertake_zone_id'),
            "undertake_subzone_id"  => $request->get('undertake_subzone_id'),
            "factory_no"            => $request->get('factory_no'),
            "metertype_id"          => $request->get('metertype_id'),
            "meter_address"         => $request->get('address'),
            "acceptance_date"       => date('Y-m-d'),
            "payment_id"            => 1,
            "owe_count"             => 0,
            "status"                => "active",
            "recorder_id"           => Auth::id(),
            "created_at"            => date("Y-m-d H:i:s"),
            "updated_at"            => date("Y-m-d H:i:s"),
        ]);
    } catch (\Throwable $th) {
        //throw $th;
    }
        //sequnce number +
        SequenceNumber::where('id', 1)->update([
            'tabmeter' => $number_sequence[0]->tabmeter + 1,
            'user'     => $number_sequence[0]->user + 1
        ]);


        return redirect()->route('admin.users.index')->with(['message' => 'บันทึกแล้ว', 'color' => 'success']);
    }

    

    public function edit($user_id, $addmeter = "")
    {
        $meter_id = $user_id;
        $user = UserMerterInfo::where('meter_id', $meter_id)
        ->with('user', 'undertake_subzone')
        ->get();
        $zones = Zone::all();
        $meter_types = MeterType::all();
        return view('admin.users.edit', compact('user', 'zones', 'meter_types', 'addmeter'));
    }

    public function update(Request $request,  $meter_id)
    {
         
        $checkDuplicateFactNo = UserMerterInfo::where('factory_no', $request->get('factory_no'))->count();
        if($checkDuplicateFactNo > 1){
            return redirect()->route('admin.users.index')->with(['message'=> 'ไม่สามารถบันทึกข้อมูลได้ \nกรุณาตรวจสอบ รหัสมิเตอร์จากโรงงานเป็นค่าว่าง หรือ ถูกใช้งานแล้ว', 'color' => 'warning']);
        }
        $temp_password = User::where('id', $request->get('user_id'))->get('password')->first();
        $request->merge([
            'password' => collect($request->password)->isEmpty() ? $temp_password->password : Hash::make($request->password)
        ]);
        $request->validate(
            [
                "username"          => 'required',
                "password"          => 'required',
                "prefix_select"     => 'required',
                "firstname"         => 'required',
                "gender"            => 'required|in:w,m',
                "id_card"           => 'required',
                "phone"             => 'required',
                "address"           => 'required',
                "province_code"     => 'required|integer',
                "metertype_id"      => 'required|integer',
                "zone_id"           => 'required',
                "factory_no"        => 'required',
                "undertake_zone_id" => 'required|integer',
            ],
            [
                "required"  => "ใส่ข้อมูล",
                "in"        => "เลือกข้อมูล",
                "integer"   => "เลือกข้อมูล",
            ],

        );

        //user table
        User::where('id', $request->get('user_id'))->update([
            "username"      => $request->username,
            "password"      => $request->password,
            "email"         => $request->email,
            "prefix"        => $request->get('prefix_select') == "other" ? $request->get('prefix_text') : $request->get('prefix_select'),
            "firstname"     => $request->get('firstname'),
            "lastname"      => $request->get('lastname'),
            "id_card"       => $request->get('id_card'),
            "phone"         => $request->get('phone'),
            "gender"        => $request->get("gender"),
            "address"       => $request->get("address"),
            "zone_id"       => $request->get("zone_id"),
            "subzone_id"    => $request->get("zone_id"),
            "tambon_code"   => $request->get("tambon_code"),
            "district_code" => $request->get("district_code"),
            "province_code" => $request->get("province_code"),
            "status"        => 1,
            "updated_at"    => date("Y-m-d H:i:s"),
        ]);
        //usermeterinfo table
        if(collect($request->get('addmeter'))->isNotEmpty()){
            $number_sequence = SequenceNumber::where('id', 1)->get();
            
            UserMerterInfo::create([
                "meter_id"              => $number_sequence[0]->tabmeter,
                "user_id"               => $request->get('user_id'),
                "meternumber"           => FunctionsController::createMeterNumberString($number_sequence[0]->tabmeter),
                "submeter_name"         => $request->get('submeter_name'),
                "undertake_zone_id"     => $request->get('undertake_zone_id'),
                "undertake_subzone_id"  => $request->get('undertake_subzone_id'),
                "factory_no"            => $request->get('factory_no'),
                "metertype_id"          => $request->get('metertype_id'),
                "meter_address"         => $request->get('address'),
                "acceptance_date"       => date('Y-m-d'),
                "payment_id"            => 1,
                "owe_count"             => 0,
                "status"                => "active",
                "recorder_id"           => Auth::id(),
                "created_at"            => date("Y-m-d H:i:s"),
                "updated_at"            => date("Y-m-d H:i:s"),
            ]);
                SequenceNumber::where('id', 1)->update([
                    'tabmeter' => $number_sequence[0]->tabmeter + 1,
                ]);
        
        
        }else{
            UserMerterInfo::where('meter_id', $meter_id)->update([
                "metertype_id"          => $request->get('metertype_id'),
                "submeter_name"         => $request->get('submeter_name'),
                "undertake_zone_id"     => $request->get('undertake_zone_id'),
                "undertake_subzone_id"  => $request->get('undertake_subzone_id'),
                "factory_no"            => $request->get('factory_no'),
                "recorder_id"           => Auth::id(),
                "updated_at"            => date("Y-m-d H:i:s"),
            ]);
        }
       

        return redirect()->route('admin.users.index')->with(['messege', 'บันทึกแล้ว', 'color' => 'success']);
    }
    // public function show(User $user)
    // {
    //     $roles = Role::all();
    //     $permissions = Permission::all();

    //     return view('admin.users.role', compact('user', 'roles', 'permissions'));
    // }

    public function show($function, $action)
    {
        if ($function == 'store') {
            return $action . "Error";
        }
        // return view('admin.users.role', compact('user', 'roles', 'permissions'));
    }

    public function history(User $user)
    {
        $user = User::with('usermeterinfos', 'usermeterinfos.invoice')->where('id', $user->id)->get();
        return view('admin.users.history', compact('user'));
    }
    public function assignRole(Request $request, User $user)
    {
        if ($user->hasRole($request->role)) {
            return back()->with('message', 'Role exists.');
        }

        $user->assignRole($request->role);
        return back()->with('message', 'Role assigned.');
    }

    public function cancel($user_id)
    {
        //check ว่ามีค้างจ่ายไหม
        $user = User::where('id', $user_id)
            ->with([
                'usermeterinfos',
                'usermeterinfos.invoice' => function ($query) {
                    return $query->select('meter_id_fk', 'inv_id', 'status')
                        ->whereIn('status', ['init', 'invoice', 'owe']);
                }
            ])
            ->get();
        return view('admin.users.cancel', compact('user'));
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
    public function destroy( $meter_id)
    {
        $usermeterinfos = UserMerterInfo::where('meter_id', $meter_id)->get(['user_id', 'meter_id'])->first();

        $user = User::find($usermeterinfos->user_id);
        if ($user->hasRole('admin')) {
            return back()->with('message', 'you are admin.');
        }
       
        $invoices = Invoice::where('meter_id_fk', $usermeterinfos->meter_id)->get();
        $invoicesHistory = InvoiceHistoty::where('meter_id_fk', $usermeterinfos->meter_id)->get();

        foreach ($invoices as $invoice) {
            if ($invoice->status == 'init') {
                Invoice::where('inv_id', $invoice->inv_id)->delete();
            }else if ($invoice->status == 'invoice') {
                Invoice::where('inv_id', $invoice->inv_id)->update([
                    'status'        => 'owe',
                    'updated_at'    => date('Y-m-d H:i:s')

                ]);
            }
        }
        Invoice::where('meter_id_fk', $usermeterinfos->meter_id)->update([
            'deleted' => '1',
        ]);
        InvoiceHistoty::where('meter_id_fk', $usermeterinfos->meter_id)->update([
            'deleted' => '1',
        ]);
        $checkInvoiceHasHistoryInfos = collect($invoices)->filter(function($v){
            return $v->status == 'paid' || $v->status == 'owe';
        })->count();
        $checkInvoiceHistoryHasHistoryInfos = collect($invoicesHistory)->filter(function($v){
            return $v->status == 'paid';
        })->count();
        UserMerterInfo::where('meter_id', $meter_id)->update([
            'status'        => $checkInvoiceHasHistoryInfos >0 && $checkInvoiceHistoryHasHistoryInfos > 0  ? 'deleted' : 'inactive',
            'deleted'       => '1',
            'comment'       => $checkInvoiceHasHistoryInfos >0 && $checkInvoiceHistoryHasHistoryInfos > 0 ? 'ยกเลิกการใช้งาน' :  'ยกเลิกการใช้งานแต่มีข้อมูลเก่า',
            'updated_at'    => date('Y-m-d H:i:s')
        ]);

        $checkHaveMeternumber = $usermeterinfos::where([
            'status' => 'active',
            'user_id' => $usermeterinfos->user_id
        ])->count();
        
        if($checkHaveMeternumber == 0){
            $user->update([
                'status'        => 'deleted',
                'comment'       => 'ยกเลิกการใช้งาน',
                'updated_at'    => date('Y-m-d H:i:s')
            ]);
        }
       


        // $user->delete();
        // FunctionsController::reset_auto_increment_when_deleted('users');
        return redirect()->route('admin.users.index')->with(['message' => 'ทำการลบข้อมูลผู้ใช้งานระบบเรียบร้อยแล้ว', 'color' => 'success']);
    }
}
