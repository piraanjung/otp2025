<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Api\FunctionsController;
use App\Http\Controllers\Controller;
use App\Models\Admin\ManagesTenantConnection;
use App\Models\Admin\Organization;
use App\Models\Tabwater\TwInvoiceTemp;
use App\Models\Tabwater\TwInvoiceHistoty;
use App\Models\Tabwater\TwMeterType;
use App\Models\Tabwater\SequenceNumber;
use App\Models\User;
use App\Models\Admin\Zone;
use App\Models\Tabwater\TwUsersInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Config; // อย่าลืม use

class UserController extends Controller
{
    public function index()
    {
    
    // *** ขั้นตอนที่ 2: รัน Query ***
    // ใช้ TwUsersInfo::with() โดยไม่ต้องเรียก setConnection() บนโมเดลหลัก
    $users = TwUsersInfo::with([
        'invoice' => function($q){
            // Eager Loading จะใช้ Connection หลักที่ถูกเปลี่ยนไปแล้วโดยอัตโนมัติ
            return $q->select('meter_id_fk','status');
        },
        'user' => function($q){
            // Eager Loading จะใช้ Connection หลักที่ถูกเปลี่ยนไปแล้วโดยอัตโนมัติ
            return $q->select('id','prefix','firstname', 'lastname', 'status');
        },
    ])->get();

    // Query สำหรับ Zone Model ก็จะใช้ Default Connection ที่ถูกเปลี่ยนเช่นกัน
    $zones =  (new Zone())->setConnection(session('db_conn'))->get();
    
    $orgInfos = Organization::getOrgName(Auth::user()->org_id_fk);
    // *** ขั้นตอนที่ 3: (ไม่บังคับ) คืนค่า Default Connection เดิมกลับไป ***
    // ถ้าคุณต้องการให้ Connection หลักกลับมาเป็นค่าเดิมหลังจากฟังก์ชันนี้ทำงานเสร็จ
    // Config::set('database.default', $originalConnection); 
    
    // ... โค้ดส่วนอื่น ๆ
    $user_deleted =  collect($users)->filter(function($v){
        return $v->deleted == '1';
    })->groupBy('user_id');
    $user_active =  collect($users)->filter(function($v){
        return $v->status == 'active';
    })->groupBy('user_id');

    $usertype = "user";
    return view('admin.users.index', compact( 'orgInfos', 'users', 'usertype', 'zones', 'user_deleted', 'user_active'));
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
        ManagesTenantConnection::configConnection(session('db_conn'));
        $meter_sq_number    =  SequenceNumber::get();
        $zones              = Zone::all();
        $meter_types        = TwMeterType::all();
        $usergroups         = Role::get(['id', 'name']);
        $usernumber         = FunctionsController::createInvoiceNumberString($meter_sq_number[0]->user);
        $username           = "user" . $meter_sq_number[0]->user;
        $meternumber        = FunctionsController::createInvoiceNumberString($meter_sq_number[0]->tabmeter);
        $password           = "user" . substr($usernumber, 3);
        $factory_no         = "";
    $orgInfos = Organization::getOrgName(Auth::user()->org_id_fk);

     $as_tw_members = (new User())->setConnection('envsogo_super_admin')->where('as_tw_member', 0)
        ->where('role_id', 3)
        ->get();

        return view('admin.users.create', compact('as_tw_members', 'orgInfos', 'usernumber', 'meternumber', 'factory_no', 'zones', 'usergroups', 'meter_types', 'username', 'password'));
    }
    public function store(Request $request)
    {

        date_default_timezone_set('Asia/Bangkok');

         // รับค่า string จาก textarea
        $userIdsString = $request->input('user_id_lists');
        
        // แปลง string ที่คั่นด้วย comma ให้เป็น array ของ User ID (ที่เป็น string)
        $selectedUserIds = array_map('trim', explode(',', $userIdsString));

        // ถ้าต้องการให้แน่ใจว่าเป็นตัวเลข
        $selectedUserIds = array_filter($selectedUserIds, 'is_numeric');

        if (!empty($selectedUserIds)) {
            // ตอนนี้ $selectedUserIds เป็น Array ที่มี User ID ที่ถูกเลือก เช่น ['1', '5', '10']
            // คุณสามารถนำไปประมวลผลต่อได้ เช่น
            // User::whereIn('id', $selectedUserIds)->update(['status' => 'processed']);
            $this->addUserAsTWmember($selectedUserIds);
            return redirect()->route('admin.users.index')->with(['message' => 'บันทึกแล้ว', 'color' => 'success']);

        }


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
        TwUsersInfo::create([
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

    private function addUserAsTWmember($ids){
        foreach($ids as $id){
            (new TwUsersInfo())->setConnection(session('db_conn'))->create([
            "user_id"               =>$id,
            "meternumber"           => FunctionsController::createMeterNumberString($id),
            "undertake_zone_id"     => rand(1,2),
            "undertake_subzone_id"  => rand(1,2),
            "metertype_id"          => 1,
            "meter_address"         => 1,
            "acceptance_date"       => date('Y-m-d'),
            "payment_id"            => 1,
            "owe_count"             => 0,
            "status"                => "active",
            "recorder_id"           => Auth::id(),
            "created_at"            => date("Y-m-d H:i:s"),
            "updated_at"            => date("Y-m-d H:i:s"),
        ]);
        }
    }

    public function edit($user_id, $addmeter = "")
    {
        $meter_id = $user_id;
        $user = TwUsersInfo::where('meter_id', $meter_id)
        ->with('user', 'undertake_subzone')
        ->get();
        $zones = Zone::all();
        $meter_types = TwMeterType::all();
        return view('admin.users.edit', compact('user', 'zones', 'meter_types', 'addmeter'));
    }

    public function update(Request $request,  $meter_id)
    {
         
        $checkDuplicateFactNo = TwUsersInfo::where('factory_no', $request->get('factory_no'))->count();
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
            
            TwUsersInfo::create([
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
            TwUsersInfo::where('meter_id', $meter_id)->update([
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
        return $user;
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
        $usermeterinfos = TwUsersInfo::where('meter_id', $meter_id)->get(['user_id', 'meter_id'])->first();

        $user = User::find($usermeterinfos->user_id);
        if ($user->hasRole('admin')) {
            return back()->with('message', 'you are admin.');
        }
       
        $invoices = TwInvoiceTemp::where('meter_id_fk', $usermeterinfos->meter_id)->get();
        $invoicesHistory = TwInvoiceHistoty::where('meter_id_fk', $usermeterinfos->meter_id)->get();

        foreach ($invoices as $invoice) {
            if ($invoice->status == 'init') {
                TwInvoiceTemp::where('inv_id', $invoice->inv_id)->delete();
            }else if ($invoice->status == 'invoice') {
                TwInvoiceTemp::where('inv_id', $invoice->inv_id)->update([
                    'status'        => 'owe',
                    'updated_at'    => date('Y-m-d H:i:s')

                ]);
            }
        }
        TwInvoiceTemp::where('meter_id_fk', $usermeterinfos->meter_id)->update([
            'deleted' => '1',
        ]);
        TwInvoiceHistoty::where('meter_id_fk', $usermeterinfos->meter_id)->update([
            'deleted' => '1',
        ]);
        $checkInvoiceHasHistoryInfos = collect($invoices)->filter(function($v){
            return $v->status == 'paid' || $v->status == 'owe';
        })->count();
        $checkInvoiceHistoryHasHistoryInfos = collect($invoicesHistory)->filter(function($v){
            return $v->status == 'paid';
        })->count();
        TwUsersInfo::where('meter_id', $meter_id)->update([
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

     public function showRegistrationForm()
    {
        $organizations = Organization::all();

        return view('auth.register', compact('organizations'));
    }

    /**
     * Handle user registration.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function register(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255|unique:users,username',
            'prefix' => 'nullable|string|max:255',
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'id_card' => 'nullable|string|max:13|unique:users,id_card',
            'line_id' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'gender' => 'nullable|in:male,female,other',
            'address' => 'nullable|string',
            'organization_id' => 'nullable|exists:organizations,id',
            'zone_id' => 'nullable|exists:zones,id',
            'subzone_id' => 'nullable|exists:subzones,id', // Assuming subzones is the table name
            'tambon_code' => 'nullable|string|max:10',
            'district_code' => 'nullable|string|max:10',
            'province_code' => 'nullable|string|max:10',
            'status' => 'nullable|string|in:active,inactive,pending',
        ]);

        $user = User::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'prefix' => $request->prefix,
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'name' => $request->firstname . ' ' . $request->lastname,
            'email' => $request->email,
            'id_card' => $request->id_card,
            'line_id' => $request->line_id,
            'phone' => $request->phone,
            'gender' => $request->gender,
            'address' => $request->address,
            'organization_id' => $request->organization_id,
            'zone_id' => $request->zone_id,
            'subzone_id' => $request->subzone_id,
            'tambon_code' => $request->tambon_code,
            'district_code' => $request->district_code,
            'province_code' => $request->province_code,
            'status' => $request->status ?? 'pending', // Default to 'pending'
        ]);

        // You might want to log the user in automatically after registration
        // Auth::login($user);

        return redirect()->route('login')->with('success', 'Registration successful! Please log in.');
    }
}
