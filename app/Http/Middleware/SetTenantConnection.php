<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Auth;
use App\Models\User; // ต้องใช้โมเดล User เพื่อโหลดใหม่
class SetTenantConnection
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if(!session()->has('db_conn')){
            $prefix = 'kp1'; //substr($username, 0, 3);
        
        // **ปรับแก้:** ใช้ชื่อ Connection ที่สั้นกว่าในการจับคู่ Prefix
        // เช่น 'hs1', 'kp1' ในการดึงชื่อ Connection เต็ม
            $connectionPrefix = match ($prefix) {
                'hs1' => 'envsogo_hs1',
                'kp1' => 'envsogo_kp1',
                default => null, // หรือ 'envsogo_super_admin' หากไม่มี Prefix
            };
            
        }else{
            $connectionPrefix = session('db_conn');
        }
        $connectionName = $connectionPrefix;

        if ($connectionName && Config::has("database.connections.{$connectionName}")) {
            
            // 1. สลับ Connection หลัก (Default)
            Config::set('database.default', $connectionName);
            
            // 2. บังคับโหลด User ใหม่จาก Tenant DB (สำคัญมาก!)
            // ตรวจสอบว่ามีการล็อกอินอยู่
            if (Auth::check()) {
                $userId = Auth::id();
                // User::find() ตอนนี้จะใช้ Tenant DB
                $newUserInstance = User::find($userId); 
                
                // ตั้งค่า User Instance ใหม่
                if ($newUserInstance) {
                    Auth::setUser($newUserInstance);
                }
            }
        } else if (Auth::check()) {
            // ถ้าไม่มี session('db_conn') แต่ล็อกอินอยู่ อาจต้องบังคับ logout 
            // หรือจัดการข้อผิดพลาดตามความเหมาะสมของระบบคุณ
            // Auth::logout();
            // return redirect('/login')->withErrors(['error' => 'Tenant session expired.']);
        }

        return $next($request);
    }
}