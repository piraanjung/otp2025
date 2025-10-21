<?php

use App\Http\Controllers\FoodWaste\BinsController;
use App\Http\Controllers\FoodWaste\DashboardController;
use App\Http\Controllers\FoodWaste\FoodWasteBinController;
use App\Http\Controllers\FoodWaste\FoodwastIotboxController;
use App\Http\Controllers\FoodWaste\UserFoodWasteController;


use App\Http\Controllers\Keptkaya\Admin\ExcelController;
use App\Http\Controllers\Keptkaya\Admin\IndexController;
use App\Http\Controllers\Keptkaya\Admin\KpUserController;
use App\Http\Controllers\KeptKaya\KpPurchaseShopController;
use App\Http\Controllers\KeptKaya\KpTbankPriceController;
use App\Http\Controllers\Keptkaya\KpUserGroupController;
use App\Http\Controllers\Keptkaya\CartController;
use App\Http\Controllers\Keptkaya\InvoicePeriodController;
use App\Http\Controllers\KeptKaya\KeptKayaPurchaseController;
use App\Http\Controllers\Keptkaya\KpBudgetYearController;
use App\Http\Controllers\Keptkaya\KpPaymentController;
use App\Http\Controllers\KeptKaya\KpSellController;
use App\Http\Controllers\Keptkaya\KpUsergroupPayratePerMonthController;
use App\Http\Controllers\Keptkaya\KpUserMonthlyStatusController;
use App\Http\Controllers\KeptKaya\RecycleWasteStaffCotroller;
use App\Http\Controllers\Keptkaya\SettingsController;
use App\Http\Controllers\Keptkaya\KpSubzoneController;
use App\Http\Controllers\Keptkaya\KpTbankItemsController;
use App\Http\Controllers\Keptkaya\KpTbankItemsGroupsController;
use App\Http\Controllers\Keptkaya\KpTbankUnitsController;
use App\Http\Controllers\KeptKaya\WasteBinPayratePerMonthController;
use App\Http\Controllers\KeptKaya\WasteBinSubscriptionController;
use App\Http\Controllers\KpMemberShopController;
use App\Http\Controllers\KpShopProductController;
use App\Models\Admin\BudgetYear;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Firebase\JWT\Key;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

Route::middleware(['auth', 'role:Super Admin|Admin|FoodWaste Staff'])->prefix('foodwaste')->name('foodwaste.')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('waste-bins/viewmap', [FoodWasteBinController::class, 'viewmap'])->name('foodwaste_bins.viewmap'); // NEW ROUTE
    
    Route::get('/users/foodwaste_bin_users', [UserFoodWasteController::class, 'foodwaste_bin_users'])->name('users.foodwaste_bin_users');
    Route::get('/users/search/{query}', [UserFoodWasteController::class, 'search'])->name('users.search');
    Route::post('users/waste-service-preferences', [UserFoodWasteController::class, 'updateWasteServicePreferences'])->name('users.updateWasteServicePreferences');
    Route::post('users/batch-update-service-preferences', [UserFoodWasteController::class, 'batchUpdateWasteServicePreferences'])->name('users.batchUpdateWasteServicePreferences');
    Route::resource('users', UserFoodWasteController::class);

    Route::prefix('/{w_user}/waste-bins')->name('waste_bins.')->group(function () {
        Route::get('/', [FoodWasteBinController::class, 'index'])->name('index');
        Route::get('/create', [FoodWasteBinController::class, 'create'])->name('create');
        Route::post('/', [FoodWasteBinController::class, 'store'])->name('store');
        Route::get('/edit', [FoodWasteBinController::class, 'edit'])->name('edit');
    });
    Route::put('waste-bins/{waste_bin}', [FoodWasteBinController::class, 'update'])->name('waste_bins.update');
    Route::get('waste-bins/map', [FoodWasteBinController::class, 'map'])->name('waste_bins.map'); // NEW ROUTE
// 
    Route::resource('iotboxes', FoodwastIotboxController::class);
    Route::resource('bins', BinsController::class);
    // Route::resource('matchings', UserBinIotboxMatchingController::class);




    Route::resource('shop-products', KpShopProductController::class);

    Route::prefix('shop')->name('shop.')->group(function () {
        Route::get('/', [KpMemberShopController::class, 'index'])->name('index');
        Route::get('cart', [KpMemberShopController::class, 'showCart'])->name('cart');
        Route::get('order-history', [KpMemberShopController::class, 'orderHistory'])->name('order_history');
        Route::get('checkout', [KpMemberShopController::class, 'checkout'])->name('checkout');
        // Actions
        Route::post('add-to-cart', [KpMemberShopController::class, 'addToCart'])->name('add_to_cart');
        Route::post('place-order', [KpMemberShopController::class, 'placeOrder'])->name('place_order');
        Route::delete('remove-from-cart/{productId}', [KpMemberShopController::class, 'removeFromCart'])->name('remove_from_cart');
    });


    Route::prefix('/staffs')->name('staffs.')->group(function () {
        Route::prefix('/mobile')->name('mobile.')->group(function () {
            Route::resource('/recycle', RecycleWasteStaffCotroller::class);
        });
    });
    

    Route::prefix('purchase/')->name('purchase.')->group(function () {
        // Step 1: User Selection
        Route::get('select_user', [KeptKayaPurchaseController::class, 'select_user'])->name('select_user');
        Route::get('start_purchase/{user}', [KeptKayaPurchaseController::class, 'startPurchase'])->name('start_purchase');

        // Step 2: Purchase Form (To be created next)`
        Route::get('form/{user}', [KeptKayaPurchaseController::class, 'showPurchaseForm'])->name('form');
        Route::post('add_to_cart', [KeptKayaPurchaseController::class, 'addToCart'])->name('add_to_cart');

        // Step 3: Cart List (To be created next)
        Route::delete('remove-from-cart/{index}', [KeptKayaPurchaseController::class, 'removeFromCart'])->name('remove_from_cart');
        Route::get('cart', [KeptKayaPurchaseController::class, 'showCart'])->name('cart');
        Route::post('save-transaction', [KeptKayaPurchaseController::class, 'saveTransaction'])->name('save_transaction');
        Route::get('show-receipt/{transaction}', [KeptKayaPurchaseController::class, 'showReceipt'])->name('show_receipt');
        Route::get('history/{user}', [KeptKayaPurchaseController::class, 'showPurchaseHistory'])->name('history');
        Route::get('receipt/{transaction}', [KeptKayaPurchaseController::class, 'showReceipt'])->name('receipt');
    });

    Route::prefix('sell/')->name('sell.')->group(function () {
        // Step 1: Sell Form
        Route::get('form', [KpSellController::class, 'showSellForm'])->name('form');
        Route::post('store', [KpSellController::class, 'storeSellTransaction'])->name('store');

        // Additional routes (to be created later)
        Route::get('history', [KpSellController::class, 'showSellHistory'])->name('history');
        Route::get('receipt/{transaction}', [KpSellController::class, 'showReceipt'])->name('receipt');
        Route::delete('/users/{transaction}', [KpSellController::class, 'destroy'])->name('destroy');
    });
    Route::resource('purchase-shops', KpPurchaseShopController::class);


    

    Route::prefix('annual-payments')->name('annual_payments.')->group(function () {
        Route::get('/', [WasteBinSubscriptionController::class, 'index'])->name('index');
        Route::get('/invoice', [WasteBinSubscriptionController::class, 'invoice'])->name('invoice');
        Route::get('/{wasteBinSubscription}', [WasteBinSubscriptionController::class, 'show'])->name('show');
        Route::get('print/{wasteBinSubscription}', [WasteBinSubscriptionController::class, 'print'])->name('print');
        Route::get('printReceipt/{wasteBinSubscription}', [WasteBinSubscriptionController::class, 'printReceipt'])->name('printReceipt');
        Route::post('/{wasteBinSubscription}/payments', [WasteBinSubscriptionController::class, 'storePayment'])->name('store_payment');
        Route::post('print-selected-invoices', [WasteBinSubscriptionController::class, 'printSelectedInvoices'])->name('print_selected_invoices');
        Route::post('/create-subscription', [WasteBinSubscriptionController::class, 'createSubscription'])->name('create_subscription');
    });

    Route::resource('/kp_budgetyear', KpBudgetYearController::class);
    Route::resource('wbin_payrate_per_months', WasteBinPayratePerMonthController::class);

    Route::prefix('tbank/')->name('tbank.')->group(function () {
        Route::resource('/items_group', KpTbankItemsGroupsController::class);
        Route::resource('/units', KpTbankUnitsController::class);

        Route::get('/cart/cartLists/{user_id}', [CartController::class, 'cartLists'])->name('cart.cart_lists');
        Route::get('/cart/add_to_cart/{id}/{amount}', [CartController::class, 'addToCart'])->name('cart.add_to_cart');
        Route::resource('/cart', CartController::class);
        Route::resource('prices', KpTbankPriceController::class);

        Route::prefix('items/')->name('items.')->group(function () {
            Route::get('buyItems/{user_id?}', [KpTbankItemsController::class, 'buyItems'])->name('buy_items');
            Route::get('search_items/{itemscode}', [KpTbankItemsController::class, 'search_items'])->name('search_items');
            Route::get('set_items_pricepoint', [KpTbankItemsController::class, 'set_items_pricepoint'])->name('set_items_pricepoint');
            Route::get('generate-code/{group_id}', [KpTbankItemsController::class, 'generateCode'])->name('generate_code');

            Route::resource('/', KpTbankItemsController::class);
            Route::get('export', [KpTbankItemsController::class, 'export'])->name('export');
            Route::post('import', [KpTbankItemsController::class, 'import'])->name('import');
        });
    });

    // Route::get('/kp_payment/paymenthistory/{inv_period}/{subzone_id}', [KpPaymentController::class, 'paymenthistory'])->name('kp_payment.paymenthistory');
    // Route::post('/kp_payment/search', [KpPaymentController::class, 'search'])->name('payment.search');
    // Route::post('/kp_payment/index_search_by_suzone', [KpPaymentController::class, 'index_search_by_suzone'])->name('kp_payment.index_search_by_suzone');
    // Route::get('/kp_payment/receipt_print/{acc_trans_id?}', [KpPaymentController::class, 'receipt_print'])->name('kp_payment.receipt_print');
    // Route::get('/kp_payment/get_kp_invoice/{budgetyear_id}/{bincode}', [KpPaymentController::class, 'get_kp_invoice'])->name('kp_payment.get_kp_invoice');
    // Route::resource('/kp_payment', KpPaymentController::class);


    // Route::middleware(['auth', 'role:super admin|admin'])->name('admin.')->prefix('admin')->group(function () {

    // Route::get('/', [IndexController::class, 'index'])->name('index');

    // Route::prefix('kp_user/')->name('kp_user.')->group(function(){
    //     Route::get('/{userId}/info', [KpUserController::class, 'getUserInfo'])->name('get_user_info');
    //     Route::post('/store_multi_users', [KpUserController::class, 'storeMultiUsers'])->name('store_multi_users');

    // });
    // Route::resource('/kp_user',KpUserController::class);

    Route::prefix('kp_usergroup')->name('kp_usergroup.')->group(function () {

        Route::get('/{usergroup_id}/infos', [KpUserGroupController::class, 'infos'])->name('usergroup.infos');

        Route::resource('/', KpUserGroupController::class);
    });

    // Route::resource('/roles', RoleController::class);
    // Route::post('/roles/{role}/permissions', [RoleController::class, 'givePermission'])->name('roles.permissions');
    // Route::delete('/roles/{role}/permissions/{permission}', [RoleController::class, 'revokePermission'])->name('roles.permissions.revoke');
    // Route::resource('/permissions', PermissionController::class);
    // Route::post('/permissions/{permission}/roles', [PermissionController::class, 'assignRole'])->name('permissions.roles');
    // Route::delete('/permissions/{permission}/roles/{role}', [PermissionController::class, 'removeRole'])->name('permissions.roles.remove');
    // Route::get('/users', [KpUserController::class, 'index'])->name('users.index');

    // Route::get('/users/{id}/userslist', [UserController::class, 'userslist'])->name('users.userslist');
    // Route::get('/users/staff', [UserController::class, 'staff'])->name('users.staff');
    // Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    // Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    // Route::post('/users/store', [UserController::class, 'store'])->name('users.store');
    // Route::post('/users/users_search', [UserController::class, 'users_search'])->name('users.users_search');
    // Route::put('/users/{user}/update', [UserController::class, 'update'])->name('users.update');
    // Route::get('/users/{user?}/updateTest', [UserController::class, 'updateTest'])->name('users.updateTest');
    // Route::get('/users/print_refund', [UserController::class, 'print_refund'])->name('users.print_refund');
    // Route::post('/users/update_paid_per_budgetyear', [UserController::class, 'update_paid_per_budgetyear'])->name('users.update_paid_per_budgetyear');

    // Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
    // Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    // Route::post('/users/{user}/roles', [UserController::class, 'assignRole'])->name('users.roles');
    // Route::delete('/users/{user}/roles/{role}', [UserController::class, 'removeRole'])->name('users.roles.remove');
    // Route::post('/users/{user}/permissions', [UserController::class, 'givePermission'])->name('users.permissions');
    // Route::delete('/users/{user}/permissions/{permission}', [UserController::class, 'revokePermission'])->name('users.permissions.revoke');

    // Route::resource('/invoice_period', InvoicePeriodController::class);

    // Route::get('/budgetyear/budgetyearTest/{budgetyear?}', [KpBudgetYearController::class, 'budgetyearTest'])->name('budgetyear.budgetyearTest');

    // Route::resource('/zone', KpZoneController::class);
    // Route::resource('/subzone', KpSubzoneController::class);
    // Route::get('/subzone/{zone_id}/getSubzone', [KpBudgetYearController::class, 'getSubzone'])->name('subzone.getSubzone');
    // Route::post('/settings/create_and_update', [SettingsController::class, 'create_and_update'])->name('settings.create_and_update');
    // Route::resource('/settings', SettingsController::class);
    // Route::resource('/excel', ExcelController::class);


    // });
    // Route::resource('/test', TestController::class);





    // Route::post('/user_payment_per_month/history', [UserPaymentPerMonthController::class, 'history'])->name('user_payment_per_month.history');
    // Route::get('/user_payment_per_month/history/{user_id}', [UserPaymentPerMonthController::class, 'history2'])->name('user_payment_per_month.history2');
    // Route::get('/user_payment_per_month/printReceiptHistory/{userPaymentPerYearId}', [UserPaymentPerMonthController::class, 'printReceiptHistory'])->name('user_payment_per_month.printReceiptHistory');
    // Route::get('/user_payment_per_month/table', [UserPaymentPerMonthController::class, 'table'])->name('user_payment_per_month.table');
    // Route::get('/user_payment_per_month/invoice', [UserPaymentPerMonthController::class, 'invoice'])->name('user_payment_per_month.invoice');
    // Route::post('/user_payment_per_month/print_notice_letters', [UserPaymentPerMonthController::class, 'print_notice_letters'])->name('user_payment_per_month.print_notice_letters');
    // Route::get('/user_payment_per_month/table_search/{budgetyear_id?}', [UserPaymentPerMonthController::class, 'table_search'])->name('user_payment_per_month.table_search');
    // Route::get('/user_payment_per_month/index2', [UserPaymentPerMonthController::class, 'index2'])->name('user_payment_per_month.index2');
    // Route::get('/user_payment_per_month/{payperyear_id}/{bin_no}/get_not_paid', [UserPaymentPerMonthController::class, 'get_not_paid'])->name('user_payment_per_month.get_not_paid');
    // Route::resource('user_payment_per_month', UserPaymentPerMonthController::class);



    // Route::prefix('user-monthly-status/')->name('user-monthly-status.')->group(function () {
    //     // Route สำหรับแสดงรายชื่อผู้ใช้งานเพื่อเลือกจัดการสถานะรายเดือน
    //     Route::get('', [KpUserMonthlyStatusController::class, 'index'])->name('index');

    //     // Route สำหรับแสดงหน้าจัดการสถานะรายเดือนของ User คนหนึ่ง
    //     Route::get('/{user}/manage', [KpUserMonthlyStatusController::class, 'monthlyStatus'])->name('manage');

    //     // Route สำหรับบันทึก/อัปเดตสถานะรายเดือนและ bin exemptions
    //     Route::post('/{user}/save', [KpUserMonthlyStatusController::class, 'saveMonthlyStatus'])->name('save');

    // });


    // Route::get('/report/daily/{month?}/{budgetyear?}', [ReportsController::class, 'daily'])->name('report.daily');
    // Route::post('/report/daily_search', [ReportsController::class, 'daily_search'])->name('report.daily_search');
    // Route::get('/report/get_date/{budgetyear}/{month}', [ReportsController::class, 'get_date'])->name('report.get_date');
    // Route::resource('/report', ReportsController::class);



});
