<?php

use App\Http\Controllers\Admin\AdminWithdrawController;
use App\Http\Controllers\Admin\OrganizationTypeController;
use App\Http\Controllers\Admin\OrgSelectorController;
use App\Http\Controllers\Admin\SuperUserController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\WasteFinancialReportController;
use App\Http\Controllers\Admin\WelfareController;
use App\Http\Controllers\Admin\BulkSaleController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\WelfareConfigController;
use Illuminate\Support\Facades\Route;



Route::middleware(['auth', 'role:Admin|finance|Super Admin'])->group(function () {
    Route::prefix('admin/')->name('admin.')->group(function () {
        Route::prefix('super_users')->name('super_users.')->group(function () {
            Route::resource('/', SuperUserController::class);
        });

        Route::resource('org-types', OrganizationTypeController::class);

        Route::resource('/financial', WasteFinancialReportController::class);
        Route::post('users/update-service', [UserController::class, 'updateService'])->name('users.update_service');
    });
});

Route::group(['middleware' => ['auth', 'role:Super Admin']], function () {
    Route::get('/admin/select-organization', [OrgSelectorController::class, 'index'])->name('admin.org_selector');
    Route::post('/admin/set-org-context', [OrgSelectorController::class, 'setContext'])->name('admin.set_org_context');
});

Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:Admin|Super Admin', 'can:admin-access'])->group(function () {
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

    // --- ส่วนกองทุนสวัสดิการ (Welfare Fund) ---
    // หน้า Dashboard สรุปยอดเงินกองกลาง
    Route::get('/welfare/dashboard', [WelfareController::class, 'getWelfareSummary'])->name('welfare.dashboard');
    // หน้าบันทึกจ่ายเงินฌาปนกิจ
    Route::get('/welfare/config', [WelfareConfigController::class, 'editConfig'])->name('welfare.config');
    Route::post('/welfare/config/update', [WelfareConfigController::class, 'updateConfig'])->name('welfare.config.update');
    Route::post('/welfare/payout', [WelfareController::class, 'storePayout'])->name('welfare.payout.store');
    Route::get('/welfare/payout/{id}/voucher', [WelfareController::class, 'downloadVoucher'])->name('admin.welfare.payout.voucher');
});
