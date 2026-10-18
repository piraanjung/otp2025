<?php

if (!function_exists('getAuthOrgId')) {
    /**
     * ดึง ID ขององค์กร (อปท.) ปัจจุบันของผู้ใช้งาน
     *
     * @return int|string|null
     */
    function getAuthOrgId()
    {
        $user = Auth::user();

        if (!$user) {
            return null;
        }

        // กรณีเป็น Super Admin และมีการสลับเลือก อปท. ไว้ใน Session
        if ($user->hasRole('Super Admin') && session()->has('active_org_id')) {
            return session('active_org_id');
        }

        // กรณีผู้ใช้งานทั่วไป ดึงจาก org_id_fk ใน DB
        return $user->org_id_fk ?? null;
    }
}
