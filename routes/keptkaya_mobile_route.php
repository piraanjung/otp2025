<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\KeptKaya\KpMobileController;
use App\Http\Controllers\KeptKaya\MachineController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;


// 1. Route Login หลัก: ใช้ URL ที่คุณต้องการ แต่ Controller จะจัดการ Logic
Route::get('/kp_mobile/login', [LoginController::class, 'showMobileLoginForm'])
    ->name('keptkayas.kp_mobile.login');

// 2. API สำหรับบันทึกสถานะ Pending (ใช้เมื่อสแกน QR Code สำเร็จ)
Route::post('/api/machine/bind-pending', [MachineController::class, 'bindMachineToPendingSession'])
    ->name('api.machine.bind');
    
// 3. Route POST Login ยังคงใช้เดิม
Route::post('/kpmobile_login', [LoginController::class, 'login'])->name('kpmobile_login');

$guard = 'web_hs1,web_kp1';
// Route::middleware(['auth:web_hs1', 'role:Super Admin|Admin|Recycle Bank Staff|Tabwater Staff|User|Annual Staff'])->prefix('kp_mobile/')->name('kp_mobile.')->group(function () {
Route::middleware(['auth:'.$guard])->prefix('kp_mobile/')->name('kp_mobile.')->group(function () {
    Route::get('create',[KpMobileController::class, 'create'])->name('create');
    Route::get('/device/check-object-status2', [MachineController::class, 'getSensorStatus']);

});