<?php

use App\Http\Controllers\Admin\AdminWithdrawController;
use App\Http\Controllers\Admin\OrganizationTypeController;
use App\Http\Controllers\Admin\SuperUserController;
use App\Http\Controllers\Admin\WasteFinancialReportController;
use App\Http\Controllers\Admin\WelfareController;
use App\Http\Controllers\Admin\BulkSaleController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserWelfareController;
use App\Http\Controllers\Admin\WelfareConfigController;
use App\Http\Controllers\Admin\WelfareMemberController;
use Illuminate\Support\Facades\Route;



Route::middleware(['auth', 'role:Admin|finance|Super Admin'])->group(function () {
    Route::prefix('admin/')->name('admin.')->group(function () {
        Route::prefix('super_users')->name('super_users.')->group(function () {
            Route::resource('/', SuperUserController::class);
        });

        Route::resource('org-types', OrganizationTypeController::class)->only(['index', 'store', 'update', 'destroy']);

        Route::get('/financial', [WasteFinancialReportController::class, 'monthlySummary'])->name('financial.index');
    });
});



Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:Admin|Super Admin'])->group(function () {
    Route::get('/withdraws', [AdminWithdrawController::class, 'index'])->name('withdraws.index');
    Route::post('/withdraws/verify/{id}', [AdminWithdrawController::class, 'verifyCode'])->name('withdraws.verify');
    Route::get('/withdraws/summary', [AdminWithdrawController::class, 'payoutSummary'])->name('withdraws.summary');
});

Route::middleware(['auth', 'role:Admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // --- ส่วนการขายขยะล๊อตใหญ่ (Bulk Sales) ---
    // หน้าฟอร์มบันทึกการขาย
    Route::get('/bulk-sales/create', [BulkSaleController::class, 'create'])->name('bulk_sales.create');
    // จัดการบันทึกข้อมูลเข้า DB
    Route::post('/bulk-sales/store', [BulkSaleController::class, 'store'])->name('bulk_sales.store');
    // (เพิ่มเติม) หน้ารายการประวัติการขายทั้งหมด
    Route::get('/bulk-sales', [BulkSaleController::class, 'index'])->name('bulk_sales.index');
    Route::get('/bulk-sales/{id}/edit', [BulkSaleController::class, 'edit'])->name('bulk_sales.edit');
    Route::put('/bulk-sales/{id}', [BulkSaleController::class, 'update'])->name('bulk_sales.update');

});

Route::middleware(['auth', 'role:Admin|Super Admin'])->prefix('admin')->name('admin.')->group(function () {



    Route::get('/welfare/dashboard', [WelfareController::class, 'getWelfareSummary'])->name('welfare.dashboard');
    //  จัดการรายการรอจัดเก็บเงินสมทบ (Collections)
    Route::get('/welfare/collections', [WelfareController::class, 'collectionIndex'])->name('welfare.collections.index');
    Route::post('/welfare/collections/{id}/store', [WelfareController::class, 'storeCollection'])->name('welfare.collections.store');
    // ตั้งค่าเกณฑ์และนโยบายสวัสดิการ (ใช้ WelfareConfigController)
    Route::get('/welfare/config', [WelfareConfigController::class, 'editConfig'])->name('welfare.config');
    Route::match(['post', 'put'], '/welfare/config/update', [WelfareConfigController::class, 'updateConfig'])->name('welfare.config.update');

    // Payout & Workflow Actions (ใช้ WelfareController)
    Route::post('/welfare/payout', [WelfareController::class, 'storePayout'])->name('welfare.payout.store');
    Route::post('/welfare/payout/{id}/approve', [WelfareController::class, 'approveStep'])->name('welfare.payout.approve');
    Route::get('/welfare/payout/{id}/voucher', [WelfareController::class, 'downloadVoucher'])->name('welfare.payout.voucher');
    Route::get('/welfare/payout/{id}/approval-pdf', [WelfareController::class, 'downloadApprovalNote'])->name('welfare.approval.pdf');
    Route::get('/welfare/check-eligibility/{userId}', [WelfareController::class, 'checkEligibility'])->name('welfare.check_eligibility');

    Route::prefix('/welfare/members')->name('welfare.members.')->group(function () {
        Route::get('/', [WelfareMemberController::class, 'index'])->name('index');
        Route::get('/add', [WelfareMemberController::class, 'create'])->name('create');
        Route::post('/bulk-store', [WelfareMemberController::class, 'bulkStore'])->name('bulkStore');
        Route::delete('/{id}', [WelfareMemberController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('/welfare/member')->name('welfare.member.')->group(function () {
        Route::get('/eligibility', [UserWelfareController::class, 'checkEligibility'])->name('eligibility');
        Route::get('/request', [UserWelfareController::class, 'create'])->name('create');
        Route::post('/store', [UserWelfareController::class, 'store'])->name('store');
        Route::get('/tracking', [UserWelfareController::class, 'tracking'])->name('tracking');
    });
});


