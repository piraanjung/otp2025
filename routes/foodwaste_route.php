<?php

use App\Http\Controllers\FoodWaste\AdminIssueController;
use App\Http\Controllers\FoodWaste\AdminIssueTypeController;
use App\Http\Controllers\FoodWaste\AdminLocalFoodController;
use App\Http\Controllers\FoodWaste\AdminMemberWasteController;
use App\Http\Controllers\FoodWaste\AdminReviewController;
use App\Http\Controllers\FoodWaste\AiroBactController;
use App\Http\Controllers\FoodWaste\BinsController;
use App\Http\Controllers\FoodWaste\DashboardController;
use App\Http\Controllers\FoodWaste\ExecutiveDashboardController;
use App\Http\Controllers\FoodWaste\FoodWasteBankController;
use App\Http\Controllers\FoodWaste\UserMatchingWasteBinsController;
use App\Http\Controllers\FoodWaste\FoodwastIotboxController;
use App\Http\Controllers\FoodWaste\UserFoodWasteController;
use App\Http\Controllers\KeptKaya\KpPurchaseShopController;
use App\Http\Controllers\KeptKaya\KpTbankPriceController;
use App\Http\Controllers\Keptkaya\KpUserGroupController;
use App\Http\Controllers\Keptkaya\CartController;
use App\Http\Controllers\KeptKaya\KpPurchaseController;
use App\Http\Controllers\Keptkaya\KpBudgetYearController;
use App\Http\Controllers\KeptKaya\KpSellController;
use App\Http\Controllers\KeptKaya\RecycleWasteStaffCotroller;
use App\Http\Controllers\Keptkaya\KpTbankItemsController;
use App\Http\Controllers\Keptkaya\KpTbankItemsGroupsController;
use App\Http\Controllers\Keptkaya\KpTbankUnitsController;
use App\Http\Controllers\KeptKaya\WasteBinPayratePerMonthController;
use App\Http\Controllers\KeptKaya\WasteBinSubscriptionController;
use App\Http\Controllers\KpMemberShopController;
use App\Http\Controllers\KpShopProductController;
use Illuminate\Support\Facades\Route;


Route::middleware(['auth', 'role:Super Admin|Admin|FoodWaste Staff|User'])->prefix('foodwaste')->name('foodwaste.')->group(function () {

    Route::get('/exec-dashboard',[ExecutiveDashboardController::class, 'index'])->name('executive_dashboard');
    Route::prefix('/admin')->name('admin.')->group(function () {
        Route::get('/reviews', [AdminReviewController::class, 'index'])->name('reviews.index');
        Route::post('/reviews/{id}/update', [AdminReviewController::class, 'update'])->name('reviews.update');
        Route::get('/issues', [AdminIssueController::class, 'index'])->name('issues.index');
        Route::post('/issues/{id}/update', [AdminIssueController::class, 'update'])->name('issues.update');

        // --- จัดการหมวดหมู่ปัญหา (Master Data) ---
        Route::get('/issue-types', [AdminIssueTypeController::class, 'index'])->name('issue_types.index');
        Route::post('/issue-types', [AdminIssueTypeController::class, 'store'])->name('issue_types.store'); // เพิ่ม
        Route::post('/issue-types/{id}', [AdminIssueTypeController::class, 'update'])->name('issue_types.update'); // แก้ไข
        Route::post('/issue-types/{id}/toggle', [AdminIssueTypeController::class, 'toggleActive'])->name('issue_types.toggle'); // เปิด-ปิด


        Route::prefix('/local-foods')->name('local_foods.')->group(function () {
            Route::get('/', [AdminLocalFoodController::class, 'index'])->name('index');
            Route::get('/review', [AdminLocalFoodController::class, 'review'])->name('review');
            Route::post('/', [AdminLocalFoodController::class, 'store'])->name('store');
            Route::put('/{id}', [AdminLocalFoodController::class, 'update'])->name('update');
            Route::delete('/{id}', [AdminLocalFoodController::class, 'destroy'])->name('destroy');
            Route::post('/approve', [AdminLocalFoodController::class, 'approve'])->name('approve');
        });

        Route::prefix('/members-waste')->name('members_waste.')->group(function () {
            // หน้ารวมรายชื่อสมาชิก
            Route::get('/', [AdminMemberWasteController::class, 'index'])->name('index');

            // หน้าดูรายละเอียดลอตขยะของสมาชิกแต่ละคน
            Route::get('/{user_id}/batches', [AdminMemberWasteController::class, 'showBatches'])->name('batches');
            // หน้าดูรายการขยะรายชิ้นในแต่ละลอต
            Route::get('/batches/{batch_id}/waste-logs', [AdminMemberWasteController::class, 'showWasteLogs'])->name('waste_logs');

            // ฟังก์ชันกดยืนยันการตรวจสอบ (Verify)
            Route::put('/waste-logs/{id}/verify', [AdminMemberWasteController::class, 'verifyWasteLog'])->name('verify_waste_log');
        });

        Route::prefix('/food-waste-bank')->name('fw_bank.')->group(function () {
            Route::get('/dashboard', [FoodWasteBankController::class, 'dashboard'])->name('dashboard');
            Route::get('/', [FoodWasteBankController::class, 'index'])->name('index');
            });
    });



    Route::prefix('/airo')->name('airo.')->group(function () {
        Route::get('/batch_history', [AiroBactController::class, 'batchHistory'])->name('batch_history');
        Route::get('/batch-detail/{id}', [AiroBactController::class, 'batchDetail'])->name('batch_detail');
        Route::post('/report-issue', [AiroBactController::class, 'reportIssue'])->name('report_issue');
        Route::get('/how-to', [AiroBactController::class, 'howTo'])->name('how_to');

        Route::get('/dashboard', [AiroBactController::class, 'index'])->name('dashboard');
    });
    Route::post('/store-meal', [AiroBactController::class, 'analyzeMeal'])->name('store_meal');
    Route::post('/store-waste', [AiroBactController::class, 'storeWaste'])->name('store_waste');
    Route::post('/save-meal', [AiroBactController::class, 'saveMeal'])->name('save_meal');
    Route::get('/admin/meal_logs_confirm_dashboard', [AiroBactController::class, 'mealLogsadminDashboard'])->name('meal_logs_confirm_dashboard');
    Route::post('/admin/approve/{id}', [AiroBactController::class, 'approveItem'])->name('meal_logs_approve_item');
});
Route::middleware(['auth', 'role:Super Admin|Admin|FoodWaste Staff'])->prefix('foodwaste')->name('foodwaste.')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    // Admin Routes

    Route::get('waste-bins/viewmap', [UserMatchingWasteBinsController::class, 'viewmap'])->name('foodwaste_bins.viewmap'); // NEW ROUTE

    Route::get('/users/foodwaste_bin_users', [UserFoodWasteController::class, 'foodwaste_bin_users'])->name('users.foodwaste_bin_users');
    Route::get('/users/search/{query}', [UserFoodWasteController::class, 'search'])->name('users.search');
    Route::post('users/waste-service-preferences', [UserFoodWasteController::class, 'updateWasteServicePreferences'])->name('users.updateWasteServicePreferences');
    Route::post('users/batch-update-service-preferences', [UserFoodWasteController::class, 'batchUpdateWasteServicePreferences'])->name('users.batchUpdateWasteServicePreferences');
    Route::resource('users', UserFoodWasteController::class);

    Route::prefix('/{wasteBin}/waste-bins')->name('waste_bins.')->group(function () {
        Route::get('/', [UserMatchingWasteBinsController::class, 'index'])->name('index');
        Route::get('/create', [UserMatchingWasteBinsController::class, 'create'])->name('create');
        Route::post('/', [UserMatchingWasteBinsController::class, 'store'])->name('store');
        Route::get('/edit', [UserMatchingWasteBinsController::class, 'edit'])->name('edit');
    });
    Route::put('waste-bins/{waste_bin}', [UserMatchingWasteBinsController::class, 'update'])->name('waste_bins.update');
    Route::get('waste-bins/map', [UserMatchingWasteBinsController::class, 'map'])->name('waste_bins.map'); // NEW ROUTE
    //
    Route::resource('iotboxes', FoodwastIotboxController::class);
    // Route สำหรับดึงข้อมูลพรีวิวผ่าน AJAX
    Route::get('/bins/preview-codes', [BinsController::class, 'previewCodes'])->name('bins.preview');
    Route::post('/bins/print-selected', [BinsController::class, 'printSelected'])->name('bins.print_selected');

    Route::resource('bins', BinsController::class);



    Route::prefix('/staffs')->name('staffs.')->group(function () {
        Route::prefix('/mobile')->name('mobile.')->group(function () {
            Route::resource('/recycle', RecycleWasteStaffCotroller::class);
        });
    });

    Route::prefix('kp_usergroup')->name('kp_usergroup.')->group(function () {

        Route::get('/{usergroup_id}/infos', [KpUserGroupController::class, 'infos'])->name('usergroup.infos');

        Route::resource('/', KpUserGroupController::class);
    });

});
