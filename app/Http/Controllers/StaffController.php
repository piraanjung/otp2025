<?php

namespace App\Http\Controllers;

use App\Models\Admin\Staff;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission; // NEW: Import Permission model

class StaffController extends Controller
{
    /**
     * Display a listing of the staff members.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $allowedPerPage = [10, 20, 50, 100];
        if (!in_array($perPage, $allowedPerPage) && $perPage !== 'all') {
            $perPage = 10;
        }

        $searchName = $request->input('search_name');
        $searchStatus = $request->input('search_status');
        $searchCanAccessWasteBank = $request->input('search_can_access_waste_bank');
        $searchCanAccessAnnualCollection = $request->input('search_can_access_annual_collection');

        // Load user and their permissions
        $query = Staff::with(['user', 'user.permissions']); // NEW: Load user permissions

        $query->when($searchName, function ($q, $name) {
            $q->whereHas('user', function ($userQ) use ($name) {
                $userQ->where('firstname', 'like', '%' . $name . '%')
                      ->orWhere('lastname', 'like', '%' . $name . '%')
                      ->orWhere('username', 'like', '%' . $name . '%');
            });
        });

        $query->when($searchStatus && $searchStatus !== 'any', function ($q, $status) {
            $q->where('status', $status);
        });

        // NEW: Filter by Spatie Permissions
        $query->when($searchCanAccessWasteBank && $searchCanAccessWasteBank !== 'any', function ($q) use ($searchCanAccessWasteBank) {
            $q->whereHas('user', function ($userQ) use ($searchCanAccessWasteBank) {
                if ($searchCanAccessWasteBank === 'true') {
                    $userQ->permission('access waste bank module');
                } else { // 'false'
                    $userQ->whereDoesntHave('permissions', function ($permQ) {
                        $permQ->where('name', 'access waste bank module');
                    });
                }
            });
        });

        $query->when($searchCanAccessAnnualCollection && $searchCanAccessAnnualCollection !== 'any', function ($q) use ($searchCanAccessAnnualCollection) {
            $q->whereHas('user', function ($userQ) use ($searchCanAccessAnnualCollection) {
                if ($searchCanAccessAnnualCollection === 'true') {
                    $userQ->permission('access annual collection module');
                } else { // 'false'
                    $userQ->whereDoesntHave('permissions', function ($permQ) {
                        $permQ->where('name', 'access annual collection module');
                    });
                }
            });
        });


        if ($perPage === 'all') {
            $staffs = $query->get();
        } else {
            $staffs = $query->paginate($perPage)->appends($request->query());
        }

        if ($request->ajax()) {
            return view('keptkaya.staffs._table_body', compact('staffs'))->render();
        }

        return view('keptkaya.staffs.index', compact('staffs', 'perPage', 'searchName', 'searchStatus', 'searchCanAccessWasteBank', 'searchCanAccessAnnualCollection'));
    }

    /**
     * Show the form for creating a new staff member.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Get users who are not already staff members
        $eligibleUsers = User::doesntHave('staff')->get();
        // Get all permissions for display in form
        $permissions = Permission::all(); // NEW
        return view('keptkaya.staffs.create', compact('eligibleUsers', 'permissions')); // NEW: Pass permissions
    }

    /**
     * Store a newly created staff member in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id|unique:staffs,user_id',
            'status' => ['required', Rule::in(['active', 'inactive', 'suspended'])],
            'permissions' => 'array', // NEW: Validate permissions array
            'permissions.*' => 'exists:permissions,name', // NEW: Validate each permission name
        ]);

        DB::transaction(function () use ($request) {
            $staff = Staff::create([
                'user_id' => $request->user_id,
                'status' => $request->status,
                'deleted' => '0',
            ]);

            // NEW: Assign permissions to the associated User
            $user = $staff->user;
            if ($request->has('permissions')) {
                $user->syncPermissions($request->input('permissions'));
            } else {
                $user->syncPermissions([]); // Revoke all if none selected
            }
        });

        return redirect()->route('keptkaya.staffs.index')->with('success', 'เพิ่มเจ้าหน้าที่เรียบร้อยแล้ว!');
    }

  
    public function show(Staff $staff)
    {
        return 'ss';
        $staff->load('user');
        return view('keptkaya.staffs.show', compact('staff'));
    }

    public function edit(Staff $staff)
    {
        $staff->load('user.permissions'); // NEW: Load user permissions
        $permissions = Permission::all(); // NEW: Get all permissions for form
        return view('keptkaya.staffs.edit', compact('staff', 'permissions')); // NEW: Pass permissions
    }

    /**
     * Update the specified staff member in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Staff  $staff
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Staff $staff)
    {
        $request->validate([
            'status' => ['required', Rule::in(['active', 'inactive', 'suspended'])],
            'deleted' => 'boolean|nullable',
            'permissions' => 'array', // NEW: Validate permissions array
            'permissions.*' => 'exists:permissions,name', // NEW: Validate each permission name
        ]);
        DB::transaction(function () use ($request, $staff) {
            $staff->update([
                'status' => $request->status,
                'deleted' => $request->has('deleted') ? 1 : 0,
            ]);

            // NEW: Sync permissions to the associated User
            $user = $staff->user;
            if ($request->has('permissions')) {
                $user->syncPermissions($request->input('permissions'));
            } else {
                $user->syncPermissions([]); // Revoke all if none selected
            }
        });

        return redirect()->route('keptkaya.staffs.index')->with('success', 'อัปเดตข้อมูลเจ้าหน้าที่เรียบร้อยแล้ว!');
    }

    public function destroy(Staff $staff)
    {
        DB::transaction(function () use ($staff) {
            // NEW: Revoke all permissions from the associated User before deleting staff record
            $staff->user->syncPermissions([]);
            $staff->delete();
        });

        return redirect()->route('keptkaya.staffs.index')->with('success', 'ลบข้อมูลเจ้าหน้าที่เรียบร้อยแล้ว!');
    }
}
