<?php

namespace App\Http\Controllers;

use App\Models\Admin\Staff;
use App\Models\Tabwater\TwNotifies;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;

class StaffController extends Controller
{
    protected $staffRolesArray;

    /** Role ที่หน้าจัดการเจ้าหน้าที่อนุญาตให้ติ๊กเพิ่ม/ถอดได้ (role อื่นของผู้ใช้จะไม่ถูกแตะ) */
    protected const MANAGEABLE_ROLES = [
        'Tabwater Staff', 'Tabwater Header', 'Admin', 'Recycle Bank Staff',
        'Annual Fee Staff', 'Food Waste Staff', 'Staff',
    ];

    /** ค่าที่ enum ของ staffs.status รองรับ (ไม่มี suspended) */
    protected const STAFF_STATUSES = ['active', 'inactive'];

    function __construct()
    {
        $this->staffRolesArray = ['Tabwater Staff', 'Tabwater Header', 'Admin', 'Recycle Bank Staff', 
        'Annual Fee Staff', 'Food Waste Staff'];
    }

    /** ผู้ใช้ต้องอยู่ org เดียวกับผู้ที่ล็อกอิน (Super Admin ข้าม org ได้) */
    private function authorizeOrg(User $staff): void
    {
        $me = Auth::user();
        abort_unless(
            $me->hasRole('Super Admin') || $staff->org_id_fk === $me->org_id_fk,
            403
        );
    }

    /**
     * แถว staffs ของผู้ใช้ใน org ของผู้ใช้เอง
     * ไม่ใช้ global scope ของ BelongsToOrganization เพราะ Super Admin อาจแก้ผู้ใช้ต่าง org
     */
    private function staffRecord(User $user): ?Staff
    {
        return Staff::withoutGlobalScopes()
            ->where('user_id', $user->id)
            ->where('org_id_fk', $user->org_id_fk)
            ->first();
    }

    /** บันทึก "สถานะเจ้าหน้าที่" ลง staffs.status (สร้างแถวให้ถ้ายังไม่มี) */
    private function saveStaffStatus(User $user, string $status): void
    {
        $record = $this->staffRecord($user);
        if ($record) {
            $record->update(['status' => $status]);
            return;
        }

        $attributes = [
            'user_id' => $user->id,
            'org_id_fk' => $user->org_id_fk,
            'status' => $status,
            'deleted' => '0',
        ];
        // โค้ดเดิมใช้ staffs.id = users.id ให้คงไว้ถ้า id นั้นยังว่าง
        if (!Staff::withoutGlobalScopes()->whereKey($user->id)->exists()) {
            $attributes['id'] = $user->id;
        }
        Staff::withoutGlobalScopes()->create($attributes);
    }

    /** ชื่อ role จริงใน DB ที่จัดการได้ (เทียบแบบไม่สนตัวพิมพ์เล็ก/ใหญ่ ตาม collation ของ MySQL) */
    private function manageableRoles()
    {
        return Role::whereIn('name', self::MANAGEABLE_ROLES)->orderBy('name')->get();
    }
    public function index(Request $request)
    {
        // รายการ Role ที่ถือว่าเป็น Staff
        $staffRoles = $this->staffRolesArray;

        // Get search and filter parameters
        $searchName = $request->input('search_name');
        $searchStatus = $request->input('search_status');
        $perPage = $request->input('per_page', 10);
        if ($perPage !== 'all' && !in_array((int) $perPage, [10, 20, 50, 100], true)) {
            $perPage = 10;
        }
        $searchCanAccessWasteBank = $request->input('search_can_access_waste_bank');
        $searchCanAccessAnnualCollection = $request->input('search_can_access_annual_collection');
        $isAjax = $request->input('ajax');

        $query = User::role($staffRoles)->with(['roles', 'permissions', 'staffs.user'])
            ->where('org_id_fk', Auth::user()->org_id_fk);

        // Apply filters
        if ($searchName) {
            $query->where(function ($q) use ($searchName) {
                $q->where('firstname', 'like', "%{$searchName}%")
                    ->orWhere('lastname', 'like', "%{$searchName}%")
                    ->orWhere('username', 'like', "%{$searchName}%")
                    ->orWhere('email', 'like', "%{$searchName}%");
            });
        }

        if ($searchStatus && $searchStatus !== 'any') {
            $query->whereHas('staffs', fn ($q) => $q->where('status', $searchStatus));
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
            ->whereDoesntHave('staffs') // เงื่อนไข: ต้องยังไม่ถูกบันทึกอยู่ในตาราง staff (ใช้ความสัมพันธ์ 'staffs')
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
        $roleNames = $this->manageableRoles()->pluck('name')->all();

        $request->validate(
            [
                'user_id' => 'required|exists:users,id',
                'roles' => ['required', 'array', 'min:1'],
                'roles.*' => [Rule::in($roleNames)],
                'permissions' => ['nullable', 'array'],
                'permissions.*' => ['exists:permissions,name'],
                'status' => ['nullable', Rule::in(self::STAFF_STATUSES)],
            ],
            [
                'roles.required' => 'กรุณาเลือกบทบาทอย่างน้อย 1 รายการ',
                'roles.*.in' => 'บทบาทที่เลือกไม่ถูกต้อง',
            ]
        );
        $user = User::findOrFail($request->user_id);
        $this->authorizeOrg($user);

        $user->assignRole($request->roles);
        if (collect($request->get('permissions'))->isNotEmpty()) {
            $user->givePermissionTo($request->get('permissions'));
        }

        $this->saveStaffStatus($user, $request->input('status', 'active'));

        return redirect()->route('keptkayas.staffs.index')->with('success', 'เพิ่มเจ้าหน้าที่ใหม่เรียบร้อยแล้ว');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $staff)
    {
        $this->authorizeOrg($staff);

        // ยังไม่มีหน้ารายละเอียดแยก ใช้หน้าแก้ไขซึ่งแสดงข้อมูลบทบาท/สิทธิ์ครบแล้ว
        return redirect()->route('keptkayas.staffs.edit', $staff->id);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $staff)
    {
        $this->authorizeOrg($staff);

        $allRoles = $this->manageableRoles();
        $allPermissions = Permission::all();

        $staff->load('roles', 'permissions');
        $staffStatus = $this->staffRecord($staff)?->status ?? 'active';
        return view('keptkayas.staffs.edit', compact('staff', 'allRoles', 'allPermissions', 'staffStatus'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $staff)
    {
        $this->authorizeOrg($staff);

        $manageable = $this->manageableRoles()->pluck('name');

        $request->validate([
            'roles' => ['nullable', 'array'],
            'roles.*' => [Rule::in($manageable->all())],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,name'],
            'status' => ['required', Rule::in(self::STAFF_STATUSES)],
        ]);

        // เปลี่ยนเฉพาะ role ที่อยู่ในรายการที่จัดการได้ ส่วน role อื่น (User, Tabwater User ฯลฯ) คงไว้
        $kept = $staff->roles->pluck('name')->reject(fn ($name) => $manageable->contains($name));
        $staff->syncRoles($kept->merge($request->input('roles', []))->all());

        $staff->syncPermissions($request->input('permissions', []));

        $this->saveStaffStatus($staff, $request->input('status'));

        return redirect()->route('keptkayas.staffs.index')->with('success', 'อัปเดตข้อมูลเจ้าหน้าที่เรียบร้อยแล้ว');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $staff)
    {
        $this->authorizeOrg($staff);

        // ถอดเฉพาะ role ฝั่งเจ้าหน้าที่ที่ผู้ใช้มีอยู่จริง (removeRole กับ role ที่ไม่มีจะโยน exception)
        $manageable = $this->manageableRoles()->pluck('name');
        foreach ($staff->roles->pluck('name')->intersect($manageable) as $roleName) {
            $staff->removeRole($roleName);
        }

        return redirect()->route('keptkayas.staffs.index')->with('success', 'ลบบทบาทเจ้าหน้าที่ออกจากผู้ใช้งานเรียบร้อยแล้ว');
    }

    public function dashboard()
    {
        $notifies = TwNotifies::with(['user', 'staffs'])
            ->orderBy('created_at', 'desc')
            ->get();

        $pendingCount  = $notifies->where('status', 'pending')->count();
        $workingCount  = $notifies->where('status', 'processing')->count();
        $completeCount = $notifies->where('status', 'complete')->count();
        $totalCount    = $notifies->count();

        return view('staff.dashboard', compact(
            'notifies', 'pendingCount', 'workingCount', 'completeCount', 'totalCount'
        ));
    }

    // --- 2. ฟังก์ชันกดรับงาน ---
    public function acceptJob(TwNotifies $notify)
    {
        $staffUser = User::find(Auth::id());

        // ตรวจสอบว่างานถูกยกเลิกไปแล้วหรือยัง
        if ($notify->status === 'cancel') {
            return redirect()->route('staff.dashboard')->with('error', 'งานนี้ถูกยกเลิกแล้ว');
        }
        // ตรวจสอบว่างานนี้มี Staff ท่านอื่นรับไปทำแล้วหรือยัง (ถ้า status เป็น processing หรือ complete แล้ว)
        if ($notify->status !== 'pending' && !$notify->staffs->contains($staffUser->id)) {
            return redirect()->route('staff.dashboard')->with('warning', 'งานนี้มีเจ้าหน้าที่ท่านอื่นรับดำเนินการไปแล้ว');
        }

        DB::beginTransaction();
        try {
            // ผูก Staff กับ Job ผ่าน Pivot Table (ถ้ายังไม่เคยผูก)
            if (!$notify->staffs->contains($staffUser->id)) {
                $staffUser->acceptedNotifies()->attach($notify->id, [
                    'staff_status' => 'working',
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]);
            }

            // อัปเดตสถานะหลักของงานเป็น 'processing' และใส่ staff_id คนแรกที่รับงาน
            if ($notify->status === 'pending') {
                $notify->update([
                    'status'   => 'processing',
                    'staff_id' => $staffUser->id,
                ]);
            }

            DB::commit();
            return redirect()->route('staff.dashboard')->with('success', "คุณได้รับงาน #{$notify->id} เรียบร้อยแล้ว");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('staff.dashboard')->with('error', 'เกิดข้อผิดพลาดในการรับงาน: ' . $e->getMessage());
        }
    }

    // --- 3. ฟังก์ชันบันทึกปิดงาน (Complete Job) ---
    public function completeJob(Request $request, TwNotifies $notify)
    {
        $request->validate([
            'remark' => 'nullable|string|max:500',
        ]);

        try {
            $notify->update([
                'status' => 'complete',
                'description' => $notify->description . ($request->remark ? "\n[บันทึกการซ่อม]: " . $request->remark : ''),
            ]);

            // อัปเดตสถานะใน Pivot Table
            DB::table('notify_staff')
                ->where('tw_notify_id', $notify->id)
                ->where('user_id', Auth::id())
                ->update(['staff_status' => 'complete', 'updated_at' => now()]);

            return redirect()->route('staff.dashboard')->with('success', "บันทึกปิดงาน #{$notify->id} สำเร็จแล้ว");
        } catch (\Exception $e) {
            return back()->with('error', 'ไม่สามารถบันทึกปิดงานได้');
        }
    }
}
