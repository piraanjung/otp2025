<?php

use App\Http\Controllers\Admin\AdminWithdrawController;
use App\Http\Controllers\Admin\OrganizationTypeController;
use App\Http\Controllers\Admin\OrgSelectorController;
use App\Http\Controllers\Admin\SuperUserController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\WasteFinancialReportController;
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

Route::prefix('admin')->name('admin.')->middleware(['auth','role:Admin|Super Admin' , 'can:admin-access'])->group(function () {
        Route::get('/withdraws', [AdminWithdrawController::class, 'index'])->name('withdraws.index');
        Route::post('/withdraws/verify/{id}', [AdminWithdrawController::class, 'verifyCode'])->name('withdraws.verify');
        Route::get('/withdraws/summary', [AdminWithdrawController::class, 'payoutSummary'])->name('.withdraws.summary');

    });


