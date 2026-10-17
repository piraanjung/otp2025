<?php

namespace App\Http\Requests\Auth;

use App\Models\User; // ตรวจสอบให้แน่ใจว่าใช้ Model User ที่ถูกต้อง
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Request;

class LoginRequest1 extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials and log the user in.
     *
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited(); // ตรวจสอบ Rate Limiting ก่อน

        $username = $this->input('username');
        $password = $this->input('password');

        // 1. ดึง Prefix 3 ตัวอักษรแรก และกำหนดชื่อ Connection
        $prefix = substr($username, 0, 3);
        // **ปรับแก้:** ใช้ชื่อ Connection ที่สั้นกว่าในการจับคู่ Prefix
        // เช่น 'hs1', 'kp1' ในการดึงชื่อ Connection เต็ม
        $connectionPrefix = match ($prefix) {
            'hs1' => 'envsogo_hs1',
            'kp1' => 'envsogo_kp1',
            default => null, // หรือ 'envsogo_super_admin' หากไม่มี Prefix
        };

        // หากไม่มี Prefix หรือใช้ Prefix ที่ไม่มีในรายการ
        if ($connectionPrefix === null && $prefix !== 'sup') { 
             throw ValidationException::withMessages([
                'username' => __('auth.failed'),
            ]);
        }

        // กำหนด Connection Name
        $connectionName = ($prefix === 'sup') 
            ? 'envsogo_super_admin' 
            : $connectionPrefix;
        // 2. ค้นหาผู้ใช้ในฐานข้อมูลที่ถูกต้อง
        
        $userFromDb = DB::connection($connectionName)
                        ->table('users')
                        ->where('username', $username)
                        ->first();

        
        // 3. ตรวจสอบรหัสผ่าน

        if (!$userFromDb || !Hash::check($password, $userFromDb->password)) {
             // 4. หากไม่ผ่าน: เพิ่มการนับความพยายามและโยน Exception
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'username' => __('auth.failed'),
            ]);
        }

        // 5. หากตรวจสอบผ่าน: ล้าง Rate Limit
        RateLimiter::clear($this->throttleKey());
        // 6. สร้าง Model Instance และกำหนด Connection ให้มัน 'จดจำ'
        
        session(['db_conn'=> $connectionName]);

        $userModel = (new User())->setConnection($connectionName)->find($userFromDb->id);
        $userModel->db_conn = $connectionName;

        // 7. **ล็อกอินผู้ใช้**:
        // เนื่องจากเราล็อกอินสำเร็จแล้ว ให้ใช้ Auth::attemptUsing()
        // เพื่อล็อกอินด้วย Model Instance ที่เราสร้างไว้
        Auth::login($userModel, $this->boolean('remember'));
        // **หมายเหตุ: ไม่ต้องโยน Exception ถ้าล็อกอินสำเร็จ!**
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'username' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     *
     * @return string
     */
    public function throttleKey(): string
    {
        return Str::lower($this->input('username')).'|'.$this->ip();
    }
}
