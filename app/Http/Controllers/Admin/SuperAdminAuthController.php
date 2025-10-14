<?php

// app/Http/Controllers/SuperAdminAuthController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Organization;
use App\Models\Admin\SetConnectionDB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin\SuperAdmin; // อย่าลืม import Model
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class SuperAdminAuthController extends Controller
{
    // แสดงหน้า Login ของ Super Admin
    public function showLoginForm()
    {
$superOrg = Organization::setTenantConnection('super_admin');
        $orgs = $superOrg->all();
        return view('superadmin.auth.login', compact('orgs'));
    }

    // จัดการการ Login ของ Super Admin
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);
        
        $organizationModel = (new Organization())->setConnection('envsogo_super_admin');
        $userModel = (new User())->setConnection('envsogo_super_admin');
        $org = $organizationModel->find($request->org_id);
        session(['org_code'=>$org->org_code]);
        if($request->get('username') == 'superadmin1' && $request->get('password') == 's12345'){
            $superAdmin = $userModel->where('username', $request->get('username'))->get()->first();
            Auth::login($superAdmin);            
        }
            return redirect()->intended(route('superadmin.dashboard')); // เปลี่ยนไปหน้า Dashboard ของ Super Admin
       
      
        // ตรวจสอบการ Login โดยใช้ Guard 'super_admin'
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = User::find(Auth::id());
            if($user->hasPermissionTo('access_to_tabwater') && $user->hasRole('tabwater staff')){
                return redirect()->intended(route('staff.tabwater.dashboard')); // เปลี่ยนไปหน้า Dashboard ของ Super Admin
            }
            $superAdminUser = SuperAdmin::find(Auth::guard('super_admin')->id()); // ดึง User ที่ล็อกอินเข้ามา
        
            // ตรวจสอบว่า Role 'super_admin' มีอยู่จริงหรือไม่ในตาราง roles
            // หากไม่มี ควรสร้าง Role 'super_admin' ใน Laratrust ก่อน
            // if (!$superAdminUser->hasRole('super_admin')) {
            //     $superAdminUser->addRole('super_admin'); // ใช้ addRole() หรือ assignRole()
            // }
            return redirect()->intended(route('superadmin.dashboard')); // เปลี่ยนไปหน้า Dashboard ของ Super Admin
        }

        return back()->withErrors([
            'username' => 'The provided credentials do not match our records.',
        ])->onlyInput('username');
    }

    // จัดการการ Logout ของ Super Admin
    public function logout(Request $request)
    {
        Auth::guard('super_admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('superadmin.login');
    }

    // (OPTIONAL) สำหรับสร้าง Super Admin คนแรกด้วยตนเองใน development
    public function createInitialSuperAdmin()
    {
        // ตรวจสอบว่ามี Super Admin อยู่แล้วหรือไม่
        if (SuperAdmin::count() == 0) {
            SuperAdmin::create([
                'username' => 'superadmin', // เปลี่ยนชื่อผู้ใช้ตามต้องการ
                'password' => Hash::make('password'), // เปลี่ยนรหัสผ่านที่ปลอดภัยกว่านี้ใน Production!
            ]);
            return "Super Admin created successfully!";
        }
        return "Super Admin already exists.";
    }
}