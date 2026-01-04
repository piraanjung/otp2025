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
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // รายการ Role ที่ถือว่าเป็น Staff
        $staffRoles = ['Tabwater Staff', 'Tabwater Header', 'Finance Staff', 'finance header'];

        // Get search and filter parameters
        $searchName = $request->input('search_name');
        $searchStatus = $request->input('search_status');
        $perPage = $request->input('per_page', 10);
        $searchCanAccessWasteBank = $request->input('search_can_access_waste_bank');
        $searchCanAccessAnnualCollection = $request->input('search_can_access_annual_collection');
        $isAjax = $request->input('ajax');

        $query = User::role($staffRoles)->with(['roles', 'permissions', 'staff.user']);

        // Apply filters
        if ($searchName) {
            $query->where(function($q) use ($searchName) {
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
        $usersToAssign = User::doesntHave('roles')
            ->orWhereHas('roles', function ($query) {
                $query->whereNotIn('name', ['Tabwater Staff', 'Tabwater Header', 'Admin', 'finance header', 'Super Admin']);
            })
            ->where('org_id_fk', Auth::user()->org_id_fk)
            ->get();

        // ดึง roles ที่สามารถ assign ได้
        $assignableRoles = Role::whereIn('name', ['Tabwater Staff', 'Tabwater Header', 'tabwater header', 'finance staff', 'finance header'])->get();
        
        $permissions = Permission::all();
        $staffRoles = ['Tabwater Staff', 'Tabwater Header', 'Admin', 'finance staff', 'finance header'];
        $roles = Role::whereIn('name', $staffRoles)->get();
        return view('keptkayas.staffs.create', compact('usersToAssign', 'assignableRoles', 'permissions', 'roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
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
        ]);
        $user = User::find($request->user_id);
        
        foreach($request->roles as $role){
            $user->assignRole($role);
        }
        if(collect($request->get('permissions'))->isNotEmpty()){
            foreach($request->get('permissions') as $permission){
                $user->givePermissionTo($permission);
            }
        }

        $staff = Staff::find($user->id);
        if(collect($staff)->isEmpty()){
            $staff = new Staff();
            $staff->user_id = $user->id;
            $staff->status  = 'active';
            $staff->deleted	= '0';
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
        // ดึง roles ที่สามารถ assign ได้
        $assignableRoles = Role::whereIn('name', ['staff', 'tabwater staff', 'tabwater header', 'finance staff', 'finance header'])->get();
        $staff->load('roles');
        return view('keptkayas.staffs.edit', compact('staff', 'assignableRoles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $staff)
    {
        $request->validate([
            'role_name' => [
                'required',
                Rule::in(['staff', 'tabwater staff', 'tabwater header', 'finance staff', 'finance header']),
                Rule::unique('model_has_roles', 'model_id')->where(function ($query) use ($request, $staff) {
                    $roleId = Role::where('name', $request->role_name)->first()->id;
                    return $query->where('role_id', $roleId)
                                 ->where('model_type', 'App\\Models\\User')
                                 ->where('model_id', '!=', $staff->id);
                })
            ],
            'status' => ['required', Rule::in(['active', 'inactive', 'suspended'])],
        ],
        [
            'role_name.unique' => 'ผู้ใช้งานนี้มีบทบาทที่เลือกอยู่แล้ว'
        ]);

        $staff->syncRoles([$request->role_name]);
        $staff->status = $request->status;
        $staff->save();

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
        $staffUser = Auth::user();

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
