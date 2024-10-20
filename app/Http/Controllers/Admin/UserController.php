<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Api\FunctionsController;
use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Accounting;
use App\Models\Admin\UserProfile;
use App\Models\BudgetYear;
use App\Models\Invoice;
use App\Models\InvoiceOld;
use App\Models\InvoicePeriod;
use App\Models\MeterType;
use App\Models\SequenceNumber;
use App\Models\User;
use App\Models\UserMerterInfo;
use App\Models\UserMeterInfoOld;
use App\Models\UserOld;
use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index()
    {
        // return $this->new_invs();
        // ßreturn $this->insertUserMeterInfos();
        // return $this->insertStaffInfos();
        // return $this->insertInvoice();
        // return $this->insertAccount();
        $users = User::where('role_id', 3)
            ->with([
                'usermeterinfos' => function ($q) {
                    return $q->select('meter_id', 'user_id', 'undertake_zone_id', 'undertake_subzone_id');
                },
                'usermeterinfos.invoice' => function ($q) {
                    return $q->select('inv_id', 'status', 'meter_id_fk');
                },
                'usermeterinfos.invoice_history' => function ($q) {
                    return $q->select('inv_id', 'status', 'meter_id_fk');
                }
            ])
            ->where('status', '0')
            ->get(['id', 'status', 'firstname', 'lastname', 'zone_id', 'subzone_id']);

        $usertype = "user";
        $zones = Zone::all();
        return view('admin.users.index', compact('users', 'usertype', 'zones'));
    }

    public function users_search(Request $request)
    {
        $users = User::role("user")->whereIn("zone_id", $request->input("zone"))->get();
        $usertype = "user";
        $zones = Zone::all();
        return view('admin.users.index', compact('users', 'usertype', 'zones'));
    }

    private function insertAccount()
    {
        $invs = Invoice::where('acc_trans_id_fk', '>', 70000)
            ->where('acc_trans_id_fk', '<=', 90000)
            ->get('acc_trans_id_fk');
        $cols = collect($invs)->groupBy('acc_trans_id_fk')->sortBy('acc_trans_id_fk');
        $arr = collect([]);
        foreach ($cols as $key => $col) {
            $acc = Accounting::where('id', $key)->first();
            if (collect($acc)->isEmpty()) {
                $arr->push($key);
            } else {
                Account::create([
                    "id" => $key,
                    "deposit" => $acc->total,
                    "payee" => $acc->cashier,
                    "comment" => "",
                    "created_at" => $acc->created_at,
                    "updated_at" => $acc->updated_at
                ]);
            }
        }
        return $arr;
    }

    private function insertUserMeterInfos()
    {
        // $usermeterinfos = UserMeterInfoOld::
        // with([
        //     'invoiceold' => function($query){
        //         return $query->select('id', 'user_id', 'inv_period_id', 'status', 'deleted', 'receipt_id');

        //     },
        //     // 'invoiceold.accounting' => function($query){
        //     //     return $query->select('id');
        //     // }
        // ])
        // ->get([
        //     'user_id', 'meternumber', 'status', 'deleted'
        // ]);

        // $users =  $usermeterinfos->filter(function($v){
        //     return collect($v->invoiceold)->isEmpty();
        // });

        // foreach($users as $user){
        //     UserMeterInfoOld::where('user_id', $user->user_id)->delete();
        // }

        $usermeterinfosOld = UserMeterInfoOld::with([
            'userprofile'
        ])->get();

        $users = $usermeterinfosOld->filter(function ($v) {
            return collect($v->userprofile)->isNotEmpty();
        });

        foreach ($users as $user) {
            //     User::create([
            //         "id"=> $old->user_id,
            //         "username"=> "user".$old->user_id,
            //         "password"=> encrypt('1234'),
            //         "prefix"=> $this->findPrefix($old->userprofile->name),
            //         "firstname"=> $old->userprofile->name,
            //         "lastname"=> $old->userprofile->name,
            //         "email"=> $old->user_id."@gmail.com",
            //         "line_id"=>"",
            //         "id_card"=> $old->userprofile->id_card,
            //         "phone"=> $old->userprofile->phone,
            //         "gender"=> $old->userprofile->gender,
            //         "address"=>$old->userprofile->address,
            //         "zone_id"=>$old->userprofile->zone_id,
            //         "subzone_id"=>$old->userprofile->subzone_id,
            //         "tambon_code"=>$old->userprofile->tambon_code,
            //         "district_code"=>$old->userprofile->district_code,
            //         "province_code"=>$old->userprofile->province_code,
            //         "email_verified_at"=>date('Y-m-d H:i:s'),
            //         "remember_token"=>"",
            //         "role_id"=>3,
            //         "status"=> $old->userprofile->deleted = 0 ? 'active' : 'inactive',
            //         "created_at"=> $old->userprofile->created_at,
            //         "updated_at"=>$old->userprofile->updated_at,
            //    ]);
            // UserMerterInfo::create([
            //     "meter_id" => $old->user_id ,
            //     "meter_address" => $old->userprofile->address,
            //     "user_id" => $old->user_id,
            //     "meternumber" => "HS-".FunctionsController::createInvoiceNumberString($old->user_id) ,
            //     "metertype_id" => 1,
            //     "undertake_zone_id" => $old->undertake_zone_id	,
            //     "undertake_subzone_id" => $old->undertake_subzone_id	,
            //     "acceptace_date" => $old->acceptace_date,
            //     "status" => $old->status == 'active' ? 'active' : 'inactive',
            //     "payment_id" => $old->payment_id	,
            //     "discounttype" => $old->discounttype,
            //     "owe_count" => $old->owe_count,
            //     "comment" => $old->comment,
            //     "recorder_id" => $old->recorder_id,
            //     "created_at" => $old->created_at,
            //     "updated_at" => $old->updated_at,
            // ]);
            $invs = InvoiceOld::where('user_id', $user->user_id)->where('status', '<>', 'pemanent deleted')->get();
            foreach ($invs as $inv) {
                Invoice::create([
                    "inv_id" => $inv->id,
                    "meter_id_fk" => $user->user_id,
                    "inv_period_id_fk" => $inv->inv_period_id,
                    "lastmeter" => $inv->lastmeter,
                    "currentmeter" => $inv->currentmeter,
                    "status" => $inv->status,
                    "acc_trans_id_fk" => $inv->receipt_id,
                    "comment" => $inv->comment,
                    "recorder_id" => $inv->recorder_id,
                    "created_at" => $inv->created_at,
                    "updated_at" =>    $inv->updated_at

                ]);
            }
        }
    }

    private function insertInvoice()
    {
        $users = UserMerterInfo::where('user_id', '>', 3000)
            // ->where('user_id', '<=', 3000)
            ->get();


        foreach ($users as $user) {
            $invs = InvoiceOld::where('user_id', $user->user_id)->where('status', '<>', 'pemanent deleted')->get();
            foreach ($invs as $inv) {
                Invoice::create([
                    "inv_id" => $inv->id,
                    "meter_id_fk" => $user->user_id,
                    "inv_period_id_fk" => $inv->inv_period_id,
                    "lastmeter" => $inv->lastmeter,
                    "currentmeter" => $inv->currentmeter,
                    "status" => $inv->status,
                    "acc_trans_id_fk" => $inv->receipt_id,
                    "comment" => $inv->comment,
                    "recorder_id" => $inv->recorder_id,
                    "created_at" => $inv->created_at,
                    "updated_at" =>    $inv->updated_at

                ]);
            }
        }
    }

    private function insertStaffInfos()
    {
        $staffs = UserOld::with('userprofile')->where('user_cat_id', '<>', 3)->get();
        foreach ($staffs as $staff) {
            if ($staff->user_cat_id == 1) {
                $role_id = 2;
            } else if ($staff->user_cat_id == 2) {
                $role_id = 4;
            } else if ($staff->user_cat_id == 4) {
                $role_id = 5;
            }
            User::create([
                "id" => $staff->id,
                "username" => $staff->username,
                "password" => $staff->password,
                "prefix" => "",
                "firstname" => $staff->userprofile->name,
                "lastname" => $staff->userprofile->name,
                "email" => $staff->email,
                "line_id" => "",
                "id_card" => $staff->userprofile->id_card,
                "phone" => $staff->userprofile->phone,
                "gender" => $staff->userprofile->gender,
                "address" => $staff->userprofile->address,
                "zone_id" => $staff->userprofile->zone_id,
                "subzone_id" => $staff->userprofile->subzone_id,
                "tambon_code" => $staff->userprofile->tambon_code,
                "district_code" => $staff->userprofile->district_code,
                "province_code" => $staff->userprofile->province_code,
                "email_verified_at" => date('Y-m-d H:i:s'),
                "remember_token" => "",
                "role_id" => $role_id,
                "status" => $staff->userprofile->deleted = 0 ? 'active' : 'inactive',
                "created_at" => $staff->userprofile->created_at,
                "updated_at" => $staff->userprofile->updated_at,
            ]);
        }
    }


    private function new_invs()
    {
        // Invoice::where('status', 'paid')->where('acc_trans_id_fk',0)->delete();
        // $accounts_id = Invoice::whereIn('status', ['paid'])->get('acc_trans_id_fk');
        // $accounts = collect($accounts_id)->unique('acc_trans_id_fk');
        // foreach ($accounts as $account) {
        //      $acc = DB::table("accounting")->where('id', $account->acc_trans_id_fk)->first();

        //     Account::create([
        //         'id'=> $account->acc_trans_id_fk,
        //         'deposit'=> $acc->net,
        //         'payee'=> $acc->cashier,
        //         'created_at'=> $acc->created_at,
        //         'updated_at'=> $acc->updated_at,
        //     ]);
        // }
        $usermeterinfos = UserMerterInfo::where('meter_id', 3)->get('meter_id');
        foreach ($usermeterinfos as $user) {
            $invs = DB::table('invoice_old')->where('user_id', $user->meter_id)->get();
            foreach ($invs as $inv) {
                if (!in_array($inv->status, ['permanent deleted', ''])) {
                    Invoice::create([
                        'inv_id' => $inv->id,
                        'meter_id_fk' => $inv->user_id,
                        'inv_period_id_fk' => $inv->inv_period_id,
                        'lastmeter' => $inv->lastmeter,
                        'currentmeter' => $inv->currentmeter,
                        'status' => $inv->status,
                        'acc_trans_id_fk' => $inv->receipt_id,
                        'comment' => $inv->comment,
                        'recorder_id' => 1, //$inv->recorder_id,
                        'created_at' => $inv->created_at,
                        'updated_at' => $inv->updated_at,
                    ]);
                }
            }
        }
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
        $meter_sq_number    = SequenceNumber::get('user');
        $zones              = Zone::all();
        $meter_types = MeterType::all();
        $usergroups         = Role::get(['id', 'name']);
        $usernumber         = FunctionsController::createInvoiceNumberString($meter_sq_number[0]->user);
        $username           = "user" . $meter_sq_number[0]->user;
        $meternumber        = FunctionsController::createInvoiceNumberString($meter_sq_number[0]->tabmeter);
        $password           = "user" . substr($usernumber, 3);

        return view('admin.users.create', compact('usernumber', 'meternumber', 'zones', 'usergroups', 'meter_types', 'username', 'password'));
    }
    public function store(Request $request)
    {
        date_default_timezone_set('Asia/Bangkok');
        $request->validate(
            [
                "prefix_select"     => 'required',
                "firstname"         => 'required',
                "lastname"          => 'required',
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
        $user = User::create([
            "id"            => $number_sequence[0]->user,
            "username"      => $request->username,
            "password"      => Hash::make($request->password),
            "email"         => $request->email,
            "prefix"        => $request->get('prefix_select') == "other" ? $request->get('prefix_text') : $request->get('prefix_select'),
            "firstname"     => $request->get('firstname'),
            "lastname"      => $request->get('lastname'),
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

        //model_has_role table
        $user->assignRole("user");

        //usermeterinfo table

        UserMerterInfo::create([
            "meter_id"              => $number_sequence[0]->tabmeter,
            "user_id"               => $number_sequence[0]->user,
            "meternumber"           => FunctionsController::createMeterNumberString($number_sequence[0]->tabmeter),
            "undertake_zone_id"     => $request->get('undertake_zone_id'),
            "undertake_subzone_id"  => $request->get('undertake_subzone_id'),
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
        //sequnce number +
        SequenceNumber::where('id', 1)->update([
            'tabmeter' => $number_sequence[0]->tabmeter + 1,
            'user'     => $number_sequence[0]->user + 1
        ]);


        return redirect()->route('admin.users.index')->with(['message', 'บันทึกแล้ว', 'color' => 'success']);
    }

    public function edit($user_id)
    {
        $user = User::where('id', $user_id)
            ->with('usermeterinfos', 'usermeterinfos.undertake_subzone')->get();
        $zones = Zone::all();
        $meter_types = MeterType::all();
        return view('admin.users.edit', compact('user', 'zones', 'meter_types'));
    }

    public function update(Request $request,  $user_id)
    {
        $temp_password = User::where('id', $user_id)->get('password')->first();
        $request->merge([
            'password' => collect($request->password)->isEmpty() ? $temp_password->password : Hash::make($request->password)
        ]);
        $request->validate(
            [
                "username"          => 'required',
                "password"          => 'required',
                "prefix_select"     => 'required',
                "firstname"         => 'required',
                "lastname"          => 'required',
                "gender"            => 'required|in:w,m',
                "id_card"           => 'required',
                "phone"             => 'required',
                "address"           => 'required',
                "province_code"     => 'required|integer',
                "metertype_id"      => 'required|integer',
                "zone_id"           => 'required',
                "undertake_zone_id" => 'required|integer',
            ],
            [
                "required"  => "ใส่ข้อมูล",
                "in"        => "เลือกข้อมูล",
                "integer"   => "เลือกข้อมูล",
            ],

        );

        //user table
        User::where('id', $user_id)->update([
            "username"      => $request->username,
            "password"      => collect($request->password)->isEmpty() ? $temp_password : Hash::make($request->password),
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
        UserMerterInfo::where('user_id', $user_id)->update([
            "metertype_id"          => $request->get('metertype_id'),
            "undertake_zone_id"     => $request->get('undertake_zone_id'),
            "undertake_subzone_id"  => $request->get('undertake_subzone_id'),
            "recorder_id"           => Auth::id(),
            "updated_at"            => date("Y-m-d H:i:s"),
        ]);

        return redirect()->route('admin.users.index')->with(['message', 'บันทึกแล้ว', 'color' => 'success']);
    }
    public function show(User $user)
    {
        $roles = Role::all();
        $permissions = Permission::all();

        return view('admin.users.role', compact('user', 'roles', 'permissions'));
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
    public function destroy(Request $request, User $user)
    {
        if ($user->hasRole('admin')) {
            return back()->with('message', 'you are admin.');
        }
        $user->update([
            'status'        => 0,
            'comment'       => 'ยกเลิกการใช้งาน',
            'updated_at'    => date('Y-m-d H:i:s')
        ]);

        UserMerterInfo::where('user_id', $user->id)->update([
            'status'        => 'deleted',
            'comment'       => 'ยกเลิกการใช้งาน',
            'updated_at'    => date('Y-m-d H:i:s')
        ]);

        $usermeterinfos = UserMerterInfo::where('user_id', $user->id)->get(['user_id', 'meter_id'])->first();
        $invoices       = Invoice::where('meter_id_fk', $usermeterinfos->meter_id)
            ->whereIn('status', ['init', 'invoice', 'inv_id'])->get();
        foreach ($invoices as $invoice) {
            if ($invoice->status == 'init') {
                Invoice::where('inv_id', $invoice->inv_id)->delete();
            } else if ($invoice->status == 'invoice') {
                Invoice::where('inv_id', $invoice->inv_id)->update([
                    'status'        => 'owe',
                    'updated_at'    => date('Y-m-d H:i:s')

                ]);
            }
        }

        // $user->delete();
        // FunctionsController::reset_auto_increment_when_deleted('users');
        return back()->with(['message' => 'ทำการลบข้อมูลผู้ใช้งานระบบเรียบร้อยแล้ว', 'color' => 'success']);
    }
}
