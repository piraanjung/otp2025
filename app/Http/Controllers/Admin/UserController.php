<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Api\FunctionsController;
use App\Http\Controllers\Controller;
use App\Models\Admin\Organization;
use App\Models\Admin\Subzone;
use App\Models\AnnualTrash\AnnualTrashSubscription;
use App\Models\Tabwater\TwInvoice;
use App\Models\Tabwater\TwInvoiceHistory;
use App\Models\User;
use App\Models\Admin\Zone;
use App\Models\FoodWaste\FoodWasteAccount;
use App\Models\AnnualTrash\AnnualTrashPayratePerMonth;
use App\Models\FoodWaste\FoodWasteUserPreference;
use App\Models\RecycleBankAccount;
use App\Models\Tabwater\TwUsersInfos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 20);

        // ใช้ with() เพื่อป้องกัน N+1 Query (โหลดข้อมูลบัญชีมาพร้อมกันเลย)
        $query = User::with(['recycleAccount', 'foodWasteAccount', 'annualTrashSubscription'])
            ->where('org_id_fk', Auth::user()->org_id_fk);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('firstname', 'like', "%$search%")
                    ->orWhere('lastname', 'like', "%$search%")
                    ->orWhere('id', $search)
                    ->orWhere('address', 'like', "%$search%");
            });
        }

        // --- 🌟 Filter ตามโซน (Zone / Subzone) ---
        if ($request->filled('zone_id')) {
            $query->where('zone_id', $request->zone_id);
        }
        if ($request->filled('subzone_id')) {
            $query->where('subzone_id', $request->subzone_id);
        }

        // --- 🌟 Filter ตามสถานะบริการ (Service Status) ---
        if ($request->filled('service_filter')) {
            $filter = $request->service_filter;
            if ($filter == 'recycle') $query->has('recycleAccount');
            if ($filter == 'food_waste') $query->has('foodWasteAccount');
            if ($filter == 'annual_trash') $query->has('annualTrashSubscription');
        }

        $users = ($perPage == 'all') ? $query->get() : $query->paginate($perPage);

        // ดึงข้อมูล Zone สำหรับตัวเลือก Filter
        $zones = Zone::all();

        return view('admin.users.index', compact('users', 'perPage', 'zones'));
    }
    // public function index()
    // {
    //     // 1. สร้าง Base Query ไว้ก่อน (ยังไม่ get)
    //     $query = TwMeterInfos::with([
    //         'tw_invoices' => function ($q) {
    //             return $q->select('meter_id_fk', 'status');
    //         },
    //         'user' => function ($q) {
    //             return $q->select('id', 'prefix', 'firstname', 'lastname', 'status');
    //         }
    //     ])
    //         ->whereHas('user', function ($q) {
    //             return $q->where('org_id_fk', Auth::user()->org_id_fk);
    //         });

    //     // 2. ดึงเฉพาะ Active โดยสั่ง SQL (เร็วกว่า filter ใน PHP)
    //     // ใช้ clone $query เพื่อไม่ให้กระทบ query หลัก
    //     $user_active = (clone $query)
    //         ->where('status', 'active')
    //         // ->where('deleted', '!=', '1') // (Option) กันเหนียวถ้า active แต่ deleted=1
    //         ->get()
    //         ->groupBy('user_id');

    //     // 3. ดึงเฉพาะ Deleted โดยสั่ง SQL
    //     $user_deleted = (clone $query)
    //         ->where('status', 'deleted')
    //         ->get()
    //         ->groupBy('user_id');

    //     // Query Zone (เหมือนเดิม)
    //     $zones = Zone::all();
    //     $orgInfos = Organization::getOrgName(Auth::user()->org_id_fk);

    //     $usertype = "user";

    //     // ไม่ต้องส่ง $users ก้อนใหญ่ไป ส่งแค่ที่แยกแล้วไป
    //     return view('admin.users.index', compact('orgInfos', 'usertype', 'zones', 'user_deleted', 'user_active'));
    // }

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

        // ดึง Org ของผู้ใช้งานที่ Logged in อยู่
        $org = Organization::find(Auth::user()->org_id_fk);

        // ดึง Zone ที่สังกัด Org เดียวกันเท่านั้น
        $zones = Zone::all();

        $defaultAddress = [
            'province' => $org->provinces->province_name ?? '-',
            'district'  => $org->districts->district_name ?? '-',
            'tambon' => $org->tambons->tambon_name ?? '-'
        ];

        return view('admin.users.create', compact('zones', 'defaultAddress'));
    }


    // 1. Validation ข้อมูลพื้นฐาน
    public function store(Request $request)
    {
        // กำหนดข้อความ Error เป็นภาษาไทย (Custom Messages)
        $messages = [
            'required' => 'กรุณากรอกข้อมูลในช่อง :attribute',
            'unique'   => ':attribute นี้มีอยู่ในระบบแล้ว',
            'min'      => ':attribute ต้องมีความยาวอย่างน้อย :min ตัวอักษร',
            'confirmed' => 'การยืนยันรหัสผ่านไม่ตรงกัน',
        ];

        // กำหนดชื่อเรียกฟิลด์เป็นภาษาไทย
        $attributes = [
            'username' => 'ชื่อผู้ใช้งาน',
            'password' => 'รหัสผ่าน',
            'firstname' => 'ชื่อจริง',
            'lastname' => 'นามสกุล',
            'phone' => 'เบอร์โทรศัพท์',
            'zone_id' => 'โซน',
            'subzone_id' => 'ซอย/ชุมชนย่อย',
        ];

        $request->validate([
            'username'   => 'required|string|max:255', //unique:users,username
            'password'   => 'required|string|min:6',
            'firstname'  => 'required|string|max:255',
            'lastname'   => 'required|string|max:255',
            'phone'      => 'required', //|unique:users,phone
            'zone_id'    => 'required|exists:zones,id', // ตรวจสอบชื่อ table ให้ตรง
            'subzone_id' => 'required|exists:subzones,id',
        ], $messages, $attributes);
        DB::beginTransaction();
        try {
            $org = Organization::find(Auth::user()->org_id_fk);
            // 2. สร้าง User หลัก
            $user = User::create([
                'username'      => $request->username,
                'password'      => Hash::make($request->password),
                'firstname'     => $request->firstname,
                'lastname'      => $request->lastname,
                'phone'         => $request->phone,
                'address'       => $request->address,
                'zone_id'       => $request->zone_id,
                'subzone_id'    => $request->subzone_id,
                'tambon_code'   => $org->org_tambon_id_fk,
                'district_code' => $org->org_district_id_fk,
                'province_code' => $org->org_province_id_fk,
                'org_id_fk'     => $org->id,
                'status'        => 'active',
            ]);

            $user->assignRole('User');

            // 3. เช็คและเปิดบริการตามที่ติ๊กมา
            // ธนาคารขยะรีไซเคิล
            if ($request->has('svc_recycle')) {
                RecycleBankAccount::create([
                    'user_id'    => $user->id,
                    'account_no' => 'RC-' . strtoupper(uniqid()),
                    'balance'    => 0,
                    'status'     => 'active'
                ]);
            }

            // ธนาคารขยะเปียก
            if ($request->has('svc_food_waste')) {
                FoodWasteAccount::create([
                    'user_id'               => $user->id,
                    'total_weight_kg'       => 0,
                    'last_contributed_at'   => now(),
                ]);
            }

            if ($request->has('svc_annual_trash')) {
                // 1. หาอัตราค่าบริการปัจจุบัน
                $payRate = AnnualTrashPayratePerMonth::where('status', 1)->latest()->first();
                $monthFee = $payRate ? $payRate->payrate_permonth : 20;

                // 2. คำนวณปีงบประมาณอัตโนมัติ (ใช้ฟังก์ชันใน Model ที่คุณมี)
                $fiscalYear = AnnualTrashSubscription::calculateFiscalYear();

                // 3. สร้าง Subscription
                // หมายเหตุ: ปกติ Subscription ต้องผูกกับถังขยะ (waste_bin_id)
                // หากตอนสมัครยังไม่มีถัง ให้สร้างถังขยะ "ใบแรก" ให้เขาก่อน หรืออนุญาตให้ waste_bin_id เป็น null ได้
                $subscription = AnnualTrashSubscription::create([
                    'waste_bin_id'           => $newAnnualTrash->id ?? null, // ผูกกับถังขยะ
                    'fiscal_year'            => $fiscalYear,
                    'payrate_permonth_id_fk' => $payRate->id ?? null,
                    'month_fee'              => $monthFee,
                    'annual_fee'             => $monthFee * 12,
                    'total_paid_amt'         => 0,
                    'status'                 => 'active',
                ]);
            }

            DB::commit();
            return redirect()->route('admin.users.index')->with('success', 'เพิ่มผู้ใช้งานและเปิดบริการเรียบร้อยแล้ว');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }
    // public function create()
    // {
    //     $meter_sq_number    = SequenceNumber::get();
    //     $zones              = Zone::all();
    //     $meter_types        = TwMeterType::all();
    //     $usergroups         = Role::get(['id', 'name']);
    //     $usernumber         = ''; //FunctionsController::createInvoiceNumberString($meter_sq_number[0]->user);
    //     $username           = "user" . $meter_sq_number[0]->user;
    //     $meternumber        = FunctionsController::createInvoiceNumberString($meter_sq_number[0]->tabmeter);
    //     $password           = "user" . substr($usernumber, 3);
    //     $factory_no         = "";
    //     $orgInfos = Organization::getOrgName(Auth::user()->org_id_fk);

    //     $as_tw_members = (new User())->setConnection('envsogo_super_admin')->where('as_tw_member', 0)
    //         ->where('role_id', 3)
    //         ->get();

    //     return view('admin.users.create', compact('as_tw_members', 'orgInfos', 'usernumber', 'meternumber', 'factory_no', 'zones', 'usergroups', 'meter_types', 'username', 'password'));
    // }
    // public function store(Request $request)
    // {

    //     date_default_timezone_set('Asia/Bangkok');

    //     // รับค่า string จาก textarea
    //     $userIdsString = $request->input('user_id_lists');

    //     // แปลง string ที่คั่นด้วย comma ให้เป็น array ของ User ID (ที่เป็น string)
    //     $selectedUserIds = array_map('trim', explode(',', $userIdsString));

    //     // ถ้าต้องการให้แน่ใจว่าเป็นตัวเลข
    //     $selectedUserIds = array_filter($selectedUserIds, 'is_numeric');

    //     if (!empty($selectedUserIds)) {
    //         // ตอนนี้ $selectedUserIds เป็น Array ที่มี User ID ที่ถูกเลือก เช่น ['1', '5', '10']
    //         // คุณสามารถนำไปประมวลผลต่อได้ เช่น
    //         // User::whereIn('id', $selectedUserIds)->update(['status' => 'processed']);
    //         $this->addUserAsTWmember($selectedUserIds);
    //         return redirect()->route('admin.users.index')->with(['message' => 'บันทึกแล้ว', 'color' => 'success']);
    //     }


    //     $request->validate(
    //         [
    //             "prefix_select"     => 'required',
    //             "firstname"         => 'required',
    //             // "lastname"          => 'required',
    //             "factory_no"        => 'required',
    //             "gender"            => 'required|in:w,m',
    //             "id_card"           => 'required',
    //             "phone"             => 'required',
    //             "address"           => 'required',
    //             "metertype_id"      => 'required|integer',
    //             "zone_id"           => 'required',
    //             "undertake_zone_id" => 'required|integer',
    //             "province_code"     => 'required|integer',
    //             "username"          => 'required',
    //             "password"          => 'required',
    //         ],
    //         [
    //             "required"      => "ใส่ข้อมูล",
    //             "in"            => "เลือกข้อมูล",
    //             "integer"       => "เลือกข้อมูล",
    //         ],

    //     );
    //     DB::beginTransaction();

    //     try {
    //         // ล็อค row นี้ไว้ ห้ามคนอื่นแย่ง update จนกว่าจะจบ transaction
    //         $sequence = SequenceNumber::where('id', 1)->lockForUpdate()->first();

    //         $newUserId = $sequence->user;
    //         $newMeterId = $sequence->tabmeter;

    //         // 1. Create User
    //         $user = User::create([
    //             "id"            => $newUserId,
    //             "username"      => $request->username,
    //             "password"      => Hash::make($request->password),
    //             // ... field อื่นๆ
    //             "status"        => 1,
    //             "created_at"    => now(), // ใช้ now()
    //             "updated_at"    => now(),
    //         ]);

    //         $user->assignRole("user");

    //         // 2. Create User Meter Info
    //         TwUsersInfos::create([
    //             "meter_id"              => $newMeterId,
    //             "user_id"               => $newUserId,
    //             "meternumber"           => FunctionsController::createMeterNumberString($newMeterId),
    //             // ... field อื่นๆ
    //             "created_at"            => now(),
    //             "updated_at"            => now(),
    //         ]);

    //         // 3. Update Sequence
    //         $sequence->update([
    //             'tabmeter' => $newMeterId + 1,
    //             'user'     => $newUserId + 1
    //         ]);

    //         DB::commit(); // ยืนยันการบันทึกทั้งหมด

    //         return redirect()->route('admin.users.index')
    //             ->with(['message' => 'บันทึกแล้ว', 'color' => 'success']);
    //     } catch (\Throwable $th) {
    //         DB::rollBack(); // ยกเลิกทั้งหมดถ้ามี error จุดใดจุดหนึ่ง
    //         Log::error($th->getMessage()); // เก็บ Log ไว้ดู

    //         // ส่งกลับไปหน้าเดิมพร้อม error
    //         return back()->withInput()->with(['message' => 'เกิดข้อผิดพลาด: ' . $th->getMessage(), 'color' => 'danger']);
    //     }
    // }


    public function edit($user_id, $addmeter = "")
    {
        // 1. ดึงข้อมูล User พร้อมความสัมพันธ์ที่จำเป็น
        // เปลี่ยนจาก .get() เป็น .firstOrFail() เพื่อให้ได้ Object ตัวเดียว
        $user = User::where('id', $user_id)
            ->with([
                'user_zone',
                'user_subzone',
                'recycleAccount',
                'foodWasteAccount',
                'annualTrashSubscription'
            ])
            ->firstOrFail();

        // 2. ดึงข้อมูล Organization ของแอดมินเพื่อใช้เป็นที่อยู่ฐาน (ตำบล/อำเภอ/จังหวัด)
        $org = Organization::find(Auth::user()->org_id_fk);
        $defaultAddress = [
            'province' => $org->province ?? '-',
            'amphure'  => $org->amphure ?? '-',
            'district' => $org->district ?? '-'
        ];

        // 3. ข้อมูลสำหรับ Dropdown
        $zones = Zone::where('org_id_fk', Auth::user()->org_id_fk)->get();

        // ดึง Subzone ของโซนที่ User คนนี้สังกัดอยู่มาโชว์รอไว้เลย
        $subzones = Subzone::where('zone_id', $user->zone_id)->get();

        return view('admin.users.edit', compact(
            'user',
            'zones',
            'subzones',
            'defaultAddress',
            'addmeter'
        ));
    }

    // public function edit($user_id, $addmeter = "")
    // {
    //     $meter_id = $user_id;
    //     $user = TwUsersInfos::where('meter_id', $meter_id)
    //         ->with('user', 'undertake_subzone')
    //         ->get();
    //     $zones = Zone::all();
    //     $meter_types = TwMeterType::all();
    //     return view('admin.users.edit', compact('user', 'zones', 'meter_types', 'addmeter'));
    // }

    // public function update(Request $request,  $meter_id)
    // {

    //     $checkDuplicateFactNo = TwUsersInfos::where('factory_no', $request->get('factory_no'))->count();
    //     if ($checkDuplicateFactNo > 1) {
    //         return redirect()->route('admin.users.index')->with(['message' => 'ไม่สามารถบันทึกข้อมูลได้ \nกรุณาตรวจสอบ รหัสมิเตอร์จากโรงงานเป็นค่าว่าง หรือ ถูกใช้งานแล้ว', 'color' => 'warning']);
    //     }
    //     $temp_password = User::where('id', $request->get('user_id'))->get('password')->first();
    //     $request->merge([
    //         'password' => collect($request->password)->isEmpty() ? $temp_password->password : Hash::make($request->password)
    //     ]);
    //     $request->validate(
    //         [
    //             "username"          => 'required',
    //             "password"          => 'required',
    //             "prefix_select"     => 'required',
    //             "firstname"         => 'required',
    //             "gender"            => 'required|in:w,m',
    //             "id_card"           => 'required',
    //             "phone"             => 'required',
    //             "address"           => 'required',
    //             "province_code"     => 'required|integer',
    //             "metertype_id"      => 'required|integer',
    //             "zone_id"           => 'required',
    //             "factory_no"        => 'required',
    //             "undertake_zone_id" => 'required|integer',
    //         ],
    //         [
    //             "required"  => "ใส่ข้อมูล",
    //             "in"        => "เลือกข้อมูล",
    //             "integer"   => "เลือกข้อมูล",
    //         ],

    //     );

    //     //user table
    //     User::where('id', $request->get('user_id'))->update([
    //         "username"      => $request->username,
    //         "password"      => $request->password,
    //         "email"         => $request->email,
    //         "prefix"        => $request->get('prefix_select') == "other" ? $request->get('prefix_text') : $request->get('prefix_select'),
    //         "firstname"     => $request->get('firstname'),
    //         "lastname"      => $request->get('lastname'),
    //         "id_card"       => $request->get('id_card'),
    //         "phone"         => $request->get('phone'),
    //         "gender"        => $request->get("gender"),
    //         "address"       => $request->get("address"),
    //         "zone_id"       => $request->get("zone_id"),
    //         "subzone_id"    => $request->get("zone_id"),
    //         "tambon_code"   => $request->get("tambon_code"),
    //         "district_code" => $request->get("district_code"),
    //         "province_code" => $request->get("province_code"),
    //         "status"        => 1,
    //         "updated_at"    => date("Y-m-d H:i:s"),
    //     ]);
    //     //usermeterinfo table
    //     if (collect($request->get('addmeter'))->isNotEmpty()) {
    //         $number_sequence = SequenceNumber::where('id', 1)->get();

    //         TwUsersInfos::create([
    //             "meter_id"              => $number_sequence[0]->tabmeter,
    //             "user_id"               => $request->get('user_id'),
    //             "meternumber"           => FunctionsController::createMeterNumberString($number_sequence[0]->tabmeter),
    //             "submeter_name"         => $request->get('submeter_name'),
    //             "undertake_zone_id"     => $request->get('undertake_zone_id'),
    //             "undertake_subzone_id"  => $request->get('undertake_subzone_id'),
    //             "factory_no"            => $request->get('factory_no'),
    //             "metertype_id"          => $request->get('metertype_id'),
    //             "meter_address"         => $request->get('address'),
    //             "acceptance_date"       => date('Y-m-d'),
    //             "payment_id"            => 1,
    //             "owe_count"             => 0,
    //             "status"                => "active",
    //             "recorder_id"           => Auth::id(),
    //             "created_at"            => date("Y-m-d H:i:s"),
    //             "updated_at"            => date("Y-m-d H:i:s"),
    //         ]);
    //         SequenceNumber::where('id', 1)->update([
    //             'tabmeter' => $number_sequence[0]->tabmeter + 1,
    //         ]);
    //     } else {
    //         TwUsersInfos::where('meter_id', $meter_id)->update([
    //             "metertype_id"          => $request->get('metertype_id'),
    //             "submeter_name"         => $request->get('submeter_name'),
    //             "undertake_zone_id"     => $request->get('undertake_zone_id'),
    //             "undertake_subzone_id"  => $request->get('undertake_subzone_id'),
    //             "factory_no"            => $request->get('factory_no'),
    //             "recorder_id"           => Auth::id(),
    //             "updated_at"            => date("Y-m-d H:i:s"),
    //         ]);
    //     }


    //     return redirect()->route('admin.users.index')->with(['messege', 'บันทึกแล้ว', 'color' => 'success']);
    // }

    public function update(Request $request, $id)
{
    // 1. Validation (ยกเว้น unique ของตัวมันเอง)
    $messages = [
        'required' => 'กรุณากรอกข้อมูลในช่อง :attribute',
        'unique'   => ':attribute นี้ถูกใช้งานแล้ว',
        'confirmed' => 'การยืนยันรหัสผ่านไม่ตรงกัน',
    ];

    $request->validate([
        'username'   => 'required|string|max:255|unique:users,username,' . $id,
        'firstname'  => 'required|string|max:255',
        'lastname'   => 'required|string|max:255',
        'phone'      => 'required|unique:users,phone,' . $id,
        'password'   => 'nullable|min:6|confirmed', // เปลี่ยนรหัสผ่านเฉพาะเมื่อมีการกรอกเท่านั้น
        'zone_id'    => 'required|exists:kp_zones,id',
        'subzone_id' => 'required|exists:kp_subzones,id',
    ], $messages);

    DB::beginTransaction();
    try {
        $user = User::findOrFail($id);

        // 2. อัปเดตข้อมูลพื้นฐาน
        $user->username = $request->username;
        $user->firstname = $request->firstname;
        $user->lastname = $request->lastname;
        $user->phone = $request->phone;
        $user->address = $request->address;
        $user->zone_id = $request->zone_id;
        $user->subzone_id = $request->subzone_id;

        // ถ้ามีการกรอกรหัสผ่านใหม่เข้ามา
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        // 3. จัดการสิทธิ์บริการ (Service Logic)

        // --- ธนาคารขยะรีไซเคิล ---
        if ($request->has('svc_recycle')) {
            // ใช้ firstOrCreate เพื่อไม่ให้สร้างซ้ำถ้ามีอยู่แล้ว
            RecycleBankAccount::firstOrCreate(
                ['user_id' => $user->id],
                ['status' => 'active', 'balance' => 0]
            );
        }

        // 4. บริการธนาคารขยะเปียก (AiroBact)
        if ($request->has('svc_food_waste')) {
            // 🌟 สำคัญ: ต้องสร้าง Preference ก่อน เพื่อป้องกัน Error ใน Dashboard
            $preference = FoodWasteUserPreference::create([
                'user_id' => $user->id,
                'setup_status' => 'completed',
                'compost_bin_type' => 'AiroBact_Bin',
            ]);

            // สร้าง Account โดยผูกกับ User หรือ Preference (ตามโครงสร้าง DB ล่าสุดของคุณ)
            FoodWasteAccount::create([
                'user_id' => $user->id,
                // 'fw_pref_id_fk' => $preference->id, // ถ้า DB ใช้ตัวนี้ให้เปิดบรรทัดนี้แทน
                'points_balance' => 0,
                'total_weight_kg' => 0,
            ]);
        }

        // 5. บริการขยะรายปี
        if ($request->has('svc_annual_trash')) {
            $payRate = AnnualTrashPayratePerMonth::where('status', 1)->latest()->first();
            $monthFee = $payRate ? $payRate->payrate_permonth : 20.00;

            AnnualTrashSubscription::create([
                'user_id' => $user->id,
                'fiscal_year' => AnnualTrashSubscription::calculateFiscalYear(),
                'payrate_permonth_id_fk' => $payRate->id ?? null,
                'month_fee' => $monthFee,
                'annual_fee' => $monthFee * 12,
                'status' => 'active',
                'billing_status' => 'waived', // ให้สิทธิ์ฟรีเริ่มต้น
            ]);
        }


        DB::commit();
        return redirect()->route('admin.users.index')->with('success', 'อัปเดตข้อมูลและสิทธิ์บริการเรียบร้อยแล้ว');

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->withInput()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
    }
}    public function show($function, $action)
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
                        ->whereIn('status', ['init', 'tw_invoices', 'owe']);
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
    public function destroy($meter_id)
    {
        $usermeterinfos = TwUsersInfos::where('meter_id', $meter_id)->get(['user_id', 'meter_id'])->first();

        $user = User::find($usermeterinfos->user_id);
        if ($user->hasRole('admin')) {
            return back()->with('message', 'you are admin.');
        }

        $invoices = TwInvoice::where('meter_id_fk', $usermeterinfos->meter_id)->get();
        $invoicesHistory = TwInvoiceHistory::where('meter_id_fk', $usermeterinfos->meter_id)->get();

        foreach ($invoices as $invoice) {
            if ($invoice->status == 'init') {
                TwInvoice::where('inv_id', $invoice->inv_id)->delete();
            } else if ($invoice->status == 'tw_invoices') {
                TwInvoice::where('inv_id', $invoice->inv_id)->update([
                    'status'        => 'owe',
                    'updated_at'    => date('Y-m-d H:i:s')

                ]);
            }
        }
        TwInvoice::where('meter_id_fk', $usermeterinfos->meter_id)->update([
            'deleted' => '1',
        ]);
        TwInvoiceHistory::where('meter_id_fk', $usermeterinfos->meter_id)->update([
            'deleted' => '1',
        ]);
        $checkInvoiceHasHistoryInfos = collect($invoices)->filter(function ($v) {
            return $v->status == 'paid' || $v->status == 'owe';
        })->count();
        $checkInvoiceHistoryHasHistoryInfos = collect($invoicesHistory)->filter(function ($v) {
            return $v->status == 'paid';
        })->count();
        TwUsersInfos::where('meter_id', $meter_id)->update([
            'status'        => $checkInvoiceHasHistoryInfos > 0 && $checkInvoiceHistoryHasHistoryInfos > 0  ? 'deleted' : 'inactive',
            'deleted'       => '1',
            'comment'       => $checkInvoiceHasHistoryInfos > 0 && $checkInvoiceHistoryHasHistoryInfos > 0 ? 'ยกเลิกการใช้งาน' :  'ยกเลิกการใช้งานแต่มีข้อมูลเก่า',
            'updated_at'    => date('Y-m-d H:i:s')
        ]);

        $checkHaveMeternumber = $usermeterinfos::where([
            'status' => 'active',
            'user_id' => $usermeterinfos->user_id
        ])->count();

        if ($checkHaveMeternumber == 0) {
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

    public function users_by_subzone($subzone_id)
    {
        $users = User::where('subzone_id', $subzone_id)
            ->get(['id', 'firstname', 'lastname', 'subzone_id']);
        return response()->json($users);
    }

    public function updateMetrics(Request $request)
    {
        $request->validate([
            'age'    => 'required|integer|min:1|max:120',
            'weight' => 'required|numeric|min:10|max:300',
            'height' => 'required|integer|min:50|max:250',
            'gender' => 'required|in:male,female',
        ]);

        $user = User::find(Auth::id());
        $user->update([
            'age'    => $request->age,
            'weight' => $request->weight,
            'height' => $request->height,
            'gender' => $request->gender == 'male' ? 'm' : 'f',
        ]);

        return back()->with('success', 'บันทึกข้อมูลร่างกายเรียบร้อยแล้ว');
    }


    public function updateService(Request $request)
    {
        $services = $request->input('services', []);

        DB::beginTransaction();
        try {
            foreach ($services as $userId => $data) {
                $user = User::find($userId);
                if (!$user) continue;

                // 1. จัดการธนาคารขยะรีไซเคิล (recycle_)
                if (isset($data['recycle']) && $data['recycle'] == "1") {
                    RecycleBankAccount::firstOrCreate(
                        ['user_id' => $user->id],
                        ['account_no' => 'RC-' . strtoupper(uniqid()), 'status' => 'active']
                    );
                } else {
                    // หากยกเลิกติ๊ก อาจจะเลือกปิดสถานะ แทนการลบข้อมูล
                    $user->recycleAccount()->update(['status' => 'inactive']);
                }

                // 2. จัดการธนาคารขยะเปียก (food_waste_)
                if (isset($data['food_waste']) && $data['food_waste'] == "1") {
                    FoodWasteAccount::firstOrCreate(['user_id' => $user->id]);
                } else {
                    $user->foodWasteAccount()->delete();
                }

                // 3. จัดการขยะรายปี (annual_trash_)
                if (isset($data['annual_trash']) && $data['annual_trash'] == "1") {
                    AnnualTrashSubscription::firstOrCreate(
                        ['user_id' => $user->id],
                        ['billing_status' => 'waived', 'monthly_fee' => 20.00]
                    );
                } else {
                    $user->annualTrashSubscription()->delete();
                }
            }

            DB::commit();
            return back()->with('success', 'บันทึกการตั้งค่าสิทธิ์ผู้ใช้งานเรียบร้อยแล้ว');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }
}
