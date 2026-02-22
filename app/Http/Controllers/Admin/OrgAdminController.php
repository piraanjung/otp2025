<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Admin\Staff;
use App\Models\Admin\Organization; // สมมติว่ามี Model นี้
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class OrgAdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // ดึงเฉพาะ User ที่เป็น OrgAdmin พร้อมข้อมูล Organization
        // สมมติว่า Role ชื่อ 'OrgAdmin'
        $admins = User::role('OrgAdmin')
            ->with('org') // Eager load organization
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.org_admins.index', compact('admins'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $organizations = Organization::all(); // เพื่อเอาไปทำ Dropdown
        return view('admin.org_admins.create', compact('organizations'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'org_id_fk' => 'required|exists:organizations,id',
            'username'  => 'required|string|max:255|unique:users',
            'email'     => 'required|email|max:255|unique:users',
            'password'  => 'required|string|min:8|confirmed', // ต้องมี field password_confirmation ใน form
            'firstname' => 'required|string|max:255',
            'lastname'  => 'required|string|max:255',
            'phone'     => 'nullable|string|max:20',
            'user_id'   => 'required|exists:users,id', // ต้องมี User นี้ในระบบจริง

        ]);

        try {
        DB::transaction(function () use ($request) {
            // 1. ดึง User เดิมมา
            $user = User::findOrFail($request->user_id);

            // 2. อัปเดต Org ID ให้เป็นของใหม่ (ย้ายสังกัดมาเป็น Admin ที่นี่)
            $user->update([
                'org_id_fk' => $request->org_id_fk,
                // 'status' => 'active' // ถ้า User เดิม inactive อยู่ อาจต้องเปิดให้ active
            ]);

            // 3. Assign Role "OrgAdmin"
            // เช็คก่อนว่ามี Role นี้หรือยัง จะได้ไม่ซ้ำ
            if (!$user->hasRole('OrgAdmin')) {
                $user->assignRole('OrgAdmin');
            }

            // 4. Update หรือ Create Staff Record
            // ใช้ updateOrCreate เพื่อความชัวร์ (ถ้าเคยเป็น Staff แล้วก็ update org, ถ้ายังไม่เคยก็ create ใหม่)
            Staff::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'org_id_fk' => $request->org_id_fk,
                    'status'    => 'active',
                    'deleted'   => 0
                ]
            );
        });

        return redirect()->route('org-admins.index')
            ->with('success', 'แต่งตั้ง User เป็นผู้ดูแลเรียบร้อยแล้ว');

    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
    }

        // try {
        //     DB::transaction(function () use ($request) {
        //         // 1. Create User
        //         $user = User::create([
        //             'org_id_fk' => $request->org_id_fk,
        //             'username'  => $request->username,
        //             'email'     => $request->email,
        //             'password'  => Hash::make($request->password),
        //             'firstname' => $request->firstname,
        //             'lastname'  => $request->lastname,
        //             'phone'     => $request->phone,
        //             'status'    => 'active', // หรือ 1 ตาม Database คุณ
        //             // field อื่นๆ เช่น prefix, lastname ใส่เพิ่มตาม form
        //         ]);

        //         // 2. Assign Role Spatie
        //         $user->assignRole('OrgAdmin');

        //         // 3. Create Staff (Link User & Org)
        //         Staff::create([
        //             'user_id'   => $user->id, // PK is user_id
        //             'org_id_fk' => $request->org_id_fk,
        //             'status'    => 'active',
        //             'deleted'   => '0', // ตาม field ใน model staff
        //         ]);
        //     });

        //     return redirect()->route('org-admins.index')
        //         ->with('success', 'สร้างผู้ดูแลหน่วยงานเรียบร้อยแล้ว');
        // } catch (\Exception $e) {
        //     return redirect()->back()
        //         ->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage())
        //         ->withInput();
        // }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $user = User::with('staff')->findOrFail($id);
        $organizations = Organization::all();

        return view('admin.org_admins.edit', compact('user', 'organizations'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'org_id_fk' => 'required|exists:organizations,id',
            'username'  => ['required', Rule::unique('users')->ignore($user->id)],
            'email'     => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'name'      => 'required|string|max:255',
            'password'  => 'nullable|string|min:8|confirmed', // ใส่เมื่อต้องการเปลี่ยนรหัสเท่านั้น
        ]);

        try {
            DB::transaction(function () use ($request, $user) {

                // 1. Update User Data
                $userData = [
                    'org_id_fk' => $request->org_id_fk,
                    'username'  => $request->username,
                    'email'     => $request->email,
                    'name'      => $request->name,
                    'phone'     => $request->phone,
                ];

                // ถ้ามีการกรอก Password ใหม่ ให้ Update ด้วย
                if ($request->filled('password')) {
                    $userData['password'] = Hash::make($request->password);
                }

                $user->update($userData);

                // 2. Update Staff Data (กรณีมีการย้าย Org หรือเปลี่ยนสถานะ)
                // ตรวจสอบว่ามี Staff record หรือไม่ ถ้าไม่มีให้ create ใหม่ (กันเหนียว)
                $staff = Staff::firstOrCreate(
                    ['user_id' => $user->id],
                    ['org_id_fk' => $request->org_id_fk]
                );

                $staff->update([
                    'org_id_fk' => $request->org_id_fk,
                    // 'status' => $request->status // ถ้ามี field status ใน form
                ]);
            });

            return redirect()->route('org-admins.index')
                ->with('success', 'แก้ไขข้อมูลเรียบร้อยแล้ว');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Update failed: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Option A: Hard Delete (ลบถาวร)
        // $user->staff()->delete(); // ลบ staff ก่อน (ถ้าไม่มี cascade)
        // $user->delete();

        // Option B: Soft Delete / Flagging (แนะนำแบบนี้สำหรับระบบราชการ)
        // ปรับ Status เป็น Inactive หรือ set deleted flag
        DB::transaction(function () use ($user) {
            $user->update(['status' => 'inactive']); // หรือ 'banned'

            if ($user->staff) {
                $user->staff()->update([
                    'status' => 'inactive',
                    'deleted' => 1
                ]);
            }
        });

        return redirect()->route('org-admins.index')
            ->with('success', 'ลบ (ระงับการใช้งาน) ผู้ดูแลเรียบร้อยแล้ว');
    }

    public function searchUsers(Request $request)
    {
        $term = $request->get('q'); // คำที่พิมพ์ใน Select2

        if (empty($term)) {
            return response()->json([]);
        }

        $users = User::query()
            ->where(function ($query) use ($term) {
                $query->where('name', 'LIKE', "%{$term}%")
                    ->orWhere('username', 'LIKE', "%{$term}%")
                    ->orWhere('email', 'LIKE', "%{$term}%");
            })
            // (Optional) อาจจะกรองไม่เอาคนที่เป็น OrgAdmin อยู่แล้ว
            // ->whereDoesntHave('roles', function($q) { $q->where('name', 'OrgAdmin'); })
            ->limit(20) // ดึงมาแค่ 20 คนพอ กันโหลดหนัก
            ->get();

        // จัด Format ให้ตรงกับที่ Select2 ต้องการ (id, text)
        $results = $users->map(function ($user) {
            return [
                'id' => $user->id,
                'text' => $user->name . ' (' . $user->username . ') - ' . ($user->org ? $user->org->name : 'ไม่มีสังกัด'),
                'email' => $user->email // ส่งเผื่อไปโชว์ใน JS
            ];
        });

        return response()->json($results);
    }
}
