<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Admin\ManagesTenantConnection;
use App\Models\Admin\Organization;
use App\Models\Admin\SuperAdmin;
use App\Models\KeptKaya\Machine;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showMobileLoginForm(Request $request)
    {
        // 1. ตรวจสอบ Machine ID จาก Query String
        $machineId = $request->query('machine_id');

        // 2. ตรวจสอบความถูกต้องของ Machine ID
        if ($machineId) {
            $machine = Machine::where('machine_id', $machineId)->first();

            // ถ้าไม่พบเครื่อง หรือเครื่องกำลังถูกใช้งานอยู่แล้ว ให้ถือว่าไม่มี Machine ID
            if (!$machine || $machine->status == 'active_session') {
                $machineId = null;
            }
        }

        // โหลด View ใหม่ของคุณ พร้อมส่ง Machine ID ไป
        return view('keptkayas.kp_mobile.login', ['machineId' => $machineId]);
    }

    public function login_staff(Request $request){
        return $request;
    }

    public function login(Request $request)
    {
        // 1. ดึง Machine ID ที่ส่งมาจากฟอร์ม Hidden Field
        $machineId = $request->input('machine_id');

        // ** (โค้ดการตรวจสอบข้อมูล Login ตามปกติของคุณ...) **
        $request->validate([
            'phone' => 'required|string', // หรือ identifier อื่นๆ
            // ... (กฎอื่น ๆ )
        ]);


        $localUser = User::where('phone', $request->phone)->get()->first();
        if(!$localUser){
            return redirect()->back();
        }

        Auth::login($localUser);


        // ตัวอย่างการ Login สำเร็จ:
        if (Auth::check()) {
            // 2. 🎯 จัดการเมื่อ Login สำเร็จ: ผูก User ID เข้ากับ Machine ID
            if ($machineId && Auth::check()) {
                $machine = Machine::where('machine_id', $machineId)->first();
                if ($machine) {
                    $machine->current_user_active_id = Auth::id();
                    $machine->status = 'active_session'; // เปลี่ยนสถานะเป็นใช้งานจริง

                    $machine->save();

                    // Redirect ไปหน้าธุรกรรมพร้อม Machine ID
                    return redirect()->route('kp_mobile.create', ['machine_id' => $machineId]);
                }
            }
            // ถ้าไม่มี Machine ID หรือ Logic ผูกข้อมูลล้มเหลว
            return redirect('/home'); // หรือหน้า Dashboard ปกติ
        }

        // ... (โค้ดสำหรับ Login ล้มเหลว) ...
        return back()->withErrors(['login_error' => 'Login Failed']);
    }
}
