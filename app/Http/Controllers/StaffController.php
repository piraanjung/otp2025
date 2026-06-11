<?php

namespace App\Http\Controllers;

use App\Models\Admin\Staff;
use App\Models\Tabwater\TwNotifies;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;

class StaffController extends Controller
{
    protected $staffRolesArray;
    function __construct()
    {
        $this->staffRolesArray = ['Tabwater Staff', 'Tabwater Header', 'Admin', 'Recycle Bank Staff', 'Annual Fee Staff'];
    }
    public function index(Request $request)
    {
        // รายการ Role ที่ถือว่าเป็น Staff
        $staffRoles = $this->staffRolesArray;

        // Get search and filter parameters
        $searchName = $request->input('search_name');
        $searchStatus = $request->input('search_status');
        $perPage = $request->input('per_page', 10);
        $searchCanAccessWasteBank = $request->input('search_can_access_waste_bank');
        $searchCanAccessAnnualCollection = $request->input('search_can_access_annual_collection');
        $isAjax = $request->input('ajax');

        $query = User::role($staffRoles)->with(['roles', 'permissions', 'staff.user'])
            ->where('org_id_fk', Auth::user()->org_id_fk);

        // Apply filters
        if ($searchName) {
            $query->where(function ($q) use ($searchName) {
                $q->where('firstname', 'like', "%{$searchName}%")
                    ->orWhere('lastname', 'like', "%{$searchName}%")
                    ->orWhere('email', 'like', "%{$searchName}%");
            });
        }

        if ($searchStatus && $searchStatus !== 'any') {
            $query->where('status', $searchStatus);
        }

        // Filter by permissions (This part is complex and assumes a specific permission structure)
        if ($searchCanAccessWasteBank === 'true') {
            $query->permission('access waste bank');
        } elseif ($searchCanAccessWasteBank === 'false') {
            $query->whereDoesntHave('permissions', function ($q) {
                $q->where('name', 'access waste bank');
            });
        }

        if ($searchCanAccessAnnualCollection === 'true') {
            $query->permission('access annual collection');
        } elseif ($searchCanAccessAnnualCollection === 'false') {
            $query->whereDoesntHave('permissions', function ($q) {
                $q->where('name', 'access annual collection');
            });
        }

        if ($perPage === 'all') {
            $staffs = $query->orderBy('firstname')->get();
        } else {
            $staffs = $query->orderBy('firstname')->paginate($perPage);
        }
        if ($isAjax) {
            return view('keptkayas.staffs._table_body', compact('staffs'))->render();
        }
        return view('keptkayas.staffs.index', compact('staffs', 'perPage'));
    }

    public function create()
    {
        // ดึงผู้ใช้งานที่ไม่มี role ที่เกี่ยวข้องกับ staff/super_admin
        $usersToAssign = User::where('org_id_fk', Auth::user()->org_id_fk) // เงื่อนไขบังคับ: ต้องอยู่ Org เดียวกัน
            ->whereDoesntHave('staff') // เงื่อนไข: ต้องยังไม่ถูกบันทึกอยู่ในตาราง staff (ใช้ความสัมพันธ์ 'staff')
            ->where(function ($query) {
                // เงื่อนไขกลุ่ม Role: ไม่มี Role เลย หรือ มีเฉพาะ Role 'User'
                $query->doesntHave('roles')
                    ->orWhereHas('roles', function ($q) {
                        $q->where('name', 'User');
                    });
            })
            ->get();

        // ดึง roles ที่สามารถ assign ได้
        $assignableRoles = Role::whereNotIn('name', ['Super Admin'])->get();

        $permissions = Permission::all();
        $staffRoles = ['Tabwater Staff', 'Tabwater Header', 'Admin', 'Recycle Bank Staff', 'Annual Fee Staff'];
        $roles = Role::whereIn('name', $staffRoles)->get();
        return view('keptkayas.staffs.create', compact('usersToAssign', 'assignableRoles', 'permissions', 'roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate(
            [
                'user_id' => 'required|exists:users,id',
                'roles' => [
                    'required',
                    // Rule::in(['tabwater staff', 'tabwater header', 'finance staff', 'finance header']),
                    Rule::unique('model_has_roles', 'model_id')->where(function ($query) use ($request) {
                        $roleId = Role::where('name', $request->roles)->first()->id;
                        return $query->where('role_id', $roleId)
                            ->where('model_type', 'App\\Models\\User');
                    })
                ],
            ],
            [
                'role_name.unique' => 'ผู้ใช้งานนี้มีบทบาทที่เลือกอยู่แล้ว'
            ]
        );
        $user = User::find($request->user_id);

        foreach ($request->roles as $role) {
            $user->assignRole($role);
        }
        if (collect($request->get('permissions'))->isNotEmpty()) {
            foreach ($request->get('permissions') as $permission) {
                $user->givePermissionTo($permission);
            }
        }

        $staff = Staff::find($user->id);
        if (collect($staff)->isEmpty()) {
            $staff = new Staff();
            $staff->id = $user->id;
            $staff->user_id = $user->id;
            $staff->status  = 'active';
            $staff->deleted    = '0';
            $staff->save();
        }



        return redirect()->route('keptkayas.staffs.index')->with('success', 'เพิ่มเจ้าหน้าที่ใหม่เรียบร้อยแล้ว');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $staff)
    {
        // โหลด permissions และ roles สำหรับการแสดงผล
        $staff->load('permissions', 'roles');
        return view('keptkayas.staffs.show', compact('staff'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $staff)
    {
        // ดึง Roles ทั้งหมด (หรือเฉพาะกลุ่มที่อนุญาต)
        $allRoles = Role::whereIn('name', [
            'staff',
            'Admin',
            'tabwater staff',
            'tabwater header',
            'finance staff',
            'finance header'
        ])->get();

        // ดึง Permissions ทั้งหมด
        $allPermissions = Permission::all();

        // Load ข้อมูลความสัมพันธ์
        $staff->load('roles', 'permissions');
        return view('keptkayas.staffs.edit', compact('staff', 'allRoles', 'allPermissions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $staff)
    {
        // 1. ปรับ Validation ให้ตรงกับชื่อ input ในหน้า Blade
        $request->validate([
            'roles' => ['required', 'array'], // รับเป็น array ตามหน้า Blade
            'roles.*' => [Rule::in(['staff', 'tabwater staff', 'tabwater header', 'finance staff', 'Admin'])],
            'permissions' => ['nullable', 'array'], // เพิ่มการตรวจสอบ permissions
            'status' => ['required', Rule::in(['active', 'inactive', 'suspended'])],
        ]);

        // 2. อัปเดต Roles (ใช้ syncRoles จะจัดการลบอันเก่าและเพิ่มอันใหม่ให้เอง)
        $staff->syncRoles($request->roles);

        // 3. อัปเดต Permissions (เพิ่มส่วนนี้เพื่อให้สิทธิ์ที่ติ๊กไว้ถูกบันทึก)
        if ($request->has('permissions')) {
            $staff->syncPermissions($request->permissions);
        } else {
            // ถ้าไม่ได้ติ๊กอะไรเลย ให้ล้าง permissions เดิม (Direct Permissions)
            $staff->syncPermissions([]);
        }

        // 4. อัปเดตข้อมูลอื่นๆ
        // $staff->status = $request->status;
        // $staff->deleted = $request->has('deleted') ? '1' : '0'; // รองรับ checkbox 'deleted'
        // $staff->save();

        return redirect()->route('keptkayas.staffs.index')->with('success', 'อัปเดตข้อมูลเจ้าหน้าที่เรียบร้อยแล้ว');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $staff)
    {
        // ดึง roles ทั้งหมดที่เกี่ยวข้องกับ staff
        $staffRoles = ['staff', 'tabwater staff', 'tabwater header', 'finance staff', 'finance header'];

        // ลบ roles ทั้งหมดที่อยู่ในรายการนี้ออกจากผู้ใช้งาน
        foreach ($staffRoles as $roleName) {
            $staff->removeRole($roleName);
        }

        return redirect()->route('keptkayas.staffs.index')->with('success', 'ลบบทบาทเจ้าหน้าที่ออกจากผู้ใช้งานเรียบร้อยแล้ว');
    }

    public function acceptJob(TwNotifies $notify)
    {
        $staffUser = User::find(Auth::id());

        // 1. ตรวจสอบสิทธิ์และสถานะงานโดยรวม (เช่น ไม่ควรรับงานที่ถูกยกเลิกแล้ว)
        if ($notify->status === 'cancel') {
            return back()->with('error', 'งานนี้ถูกยกเลิกแล้ว');
        }

        // 2. รับงาน: เพิ่มรายการในตาราง Pivot (notify_staff)
        try {
            // ใช้เมธอด attach() เพื่อสร้างความสัมพันธ์ Many-to-Many
            $staffUser->acceptedNotifies()->attach($notify->id, [
                'staff_status' => 'working' // ตั้งสถานะเฉพาะของ Staff คนนี้
            ]);

            // 3. **อัปเดตสถานะหลักของงาน:** //    ถ้าสถานะหลักยังเป็น 'pending' ให้เปลี่ยนเป็น 'processing'
            if ($notify->status === 'pending') {
                $notify->update(['status' => 'processing']);
            }

            return redirect()->route('staff.dashboard')->with('success', "คุณได้รับงาน #{$notify->id} เพื่อดำเนินการแล้ว");
        } catch (\Illuminate\Database\QueryException $e) {
            // ตรวจจับ Primary Key Conflict (กรณี Staff คนนี้เคยรับงานนี้ไปแล้ว)
            if ($e->getCode() == 23000) {
                return back()->with('warning', 'คุณเคยรับงานนี้ไปแล้ว!');
            }
            return back()->with('error', 'เกิดข้อผิดพลาดในการรับงาน');
        }
    }
}
