<?php

use App\Http\Controllers\EmissionFactorController;
use App\Http\Controllers\KeptKaya\AnnualBatchController;
use App\Http\Controllers\KeptKaya\BarcodeController;
use App\Http\Controllers\KeptKaya\DashboardController;
use App\Http\Controllers\KeptKaya\KpPurchaseShopController;
use App\Http\Controllers\KeptKaya\KpTbankPriceController;
use App\Http\Controllers\KeptKaya\KpUserGroupController;
use App\Http\Controllers\KeptKaya\CartController;
use App\Http\Controllers\KeptKaya\KorKor3Controller;
use App\Http\Controllers\Keptkayas\KpRecycleClassifyController;
use App\Http\Controllers\KeptKaya\KpPurchaseController;
use App\Http\Controllers\KeptKaya\KpBudgetYearController;
use App\Http\Controllers\KeptKaya\KpSellController;
use App\Http\Controllers\KeptKaya\RecycleWasteStaffCotroller;
use App\Http\Controllers\KeptKaya\KpTbankItemsController;
use App\Http\Controllers\KeptKaya\KpTbankItemsGroupsController;
use App\Http\Controllers\KeptKaya\KpTbankUnitsController;
use App\Http\Controllers\KeptKaya\UserWasteController;
use App\Http\Controllers\KeptKaya\WasteBinController;
use App\Http\Controllers\KeptKaya\WasteBinPayratePerMonthController;
use App\Http\Controllers\KeptKaya\WasteBinSubscriptionController;
use App\Http\Controllers\KpMemberShopController;
use App\Http\Controllers\KpShopProductController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KeptKaya\AnnualReportController;
use App\Http\Controllers\KeptKaya\AnnualTrashPayratePerMonthController;
use App\Http\Controllers\KeptKaya\BinsController;
use App\Http\Controllers\Kiosk\KioskController;
use App\Models\AnnualTrash\AnnualTrash;
use App\Models\AnnualTrash\AnnualTrashSubscription;

Route::middleware(['auth'])->prefix('keptkayas')->name('keptkayas.')->group(function () {
    Route::resource('kiosks', KioskController::class);
    Route::get('/kiosks/noscreen/login', [KioskController::class, 'login'])->name('kiosks.noscreen.login');
    Route::post('/kiosks/noscreen/userMatchKiosk', [KioskController::class, 'userMatchKiosk']);
    // หน้า Monitor ควบคุมตู้
    Route::get('/kiosks/noscreen/monitor', [KioskController::class, 'monitor'])->name('kiosk.monitor');

    // Route สำหรับกด Disconnect
    Route::post('/kiosks/noscreen/disconnect', [KioskController::class, 'disconnect'])->name('kiosk.disconnect');
    Route::post('/kiosks/noscreen/save-transaction', [KioskController::class, 'storeTransaction']);

    Route::get('/korkor3', [KorKor3Controller::class, 'index'])->name('korkor3.index');
    Route::get('/korkor3/export', [KorKor3Controller::class, 'exportKorKor3'])->name('korkor3.export');
    Route::prefix('/reports')->name('reports.')->group(function () {
        Route::get('/', [AnnualReportController::class, 'index'])->name('index');
        Route::get('/generate', [AnnualReportController::class, 'generate'])->name('generate');
    });

    // 1. Dashboard (ย้ายเข้ามาใน Group ตัดคำว่า keptkayas/ ออก เพราะมี prefix แล้ว)
    // URL: /keptkayas/dashboard/{type} -> Name: keptkayas.dashboard
    Route::get('dashboard/{keptkayatype?}', [DashboardController::class, 'index'])->name('dashboard');

    // 2. Main Module Routes
    Route::get('recycle_classify/index', [KpRecycleClassifyController::class, 'index'])->name('recycle_classify');

    Route::get('scanner', function () {
        return view('keptkayas.barcode.scanner');
    });

    Route::resource('/purchase-shops', KpPurchaseShopController::class); // middleware auth ซ้ำซ้อน ลบออกได้เพราะ Group มีแล้ว

    Route::post('barcode/search', [BarcodeController::class, 'search'])->name('barcode.search');
    Route::resource('shop-products', KpShopProductController::class);

    // 3. Shop Routes
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

    // 4. Staff Routes
    Route::prefix('/staffs')->name('staffs.')->group(function () {
        Route::prefix('/mobile')->name('mobile.')->group(function () {
            Route::resource('/recycle', RecycleWasteStaffCotroller::class);
        });
    });

    Route::prefix('/emission-factors')->name('emission.')->group(function () {
    Route::get('/', [EmissionFactorController::class, 'index'])->name('index');
    Route::get('/create', [EmissionFactorController::class, 'create'])->name('create');
    Route::post('/', [EmissionFactorController::class, 'store'])->name('store');
    Route::get('/{id}/edit', [EmissionFactorController::class, 'edit'])->name('edit');
    Route::put('/{id}', [EmissionFactorController::class, 'update'])->name('update');
    Route::delete('/{id}', [EmissionFactorController::class, 'destroy'])->name('destroy');
    Route::get('/export', [EmissionFactorController::class, 'export'])->name('export');
    Route::post('/import', [EmissionFactorController::class, 'import'])->name('import');
});

    Route::resource('annual_batch', AnnualBatchController::class);

    // 5. User & Waste Bin Management
    Route::resource('users', UserWasteController::class);
    Route::get('/waste_bin_users', [UserWasteController::class, 'waste_bin_users'])->name('waste_bin_users');
    Route::get('/users/search/{query}', [UserWasteController::class, 'search'])->name('users.search');
    Route::post('/waste-service-preferences', [UserWasteController::class, 'updateWasteServicePreferences'])->name('updateWasteServicePreferences');
    Route::post('users/batch-update-service-preferences', [UserWasteController::class, 'batchUpdateWasteServicePreferences'])->name('users.batchUpdateWasteServicePreferences');

    // 6. Purchase System
    Route::prefix('purchase/')->name('purchase.')->group(function () {
        Route::get('get-units/{itemId}', [KpPurchaseController::class, 'getUnitsForItem'])->name('get_units');
        Route::get('select_user', [KpPurchaseController::class, 'select_user'])->name('select_user');
        Route::get('start_purchase/{user_waste_pref_id}', [KpPurchaseController::class, 'startPurchase'])->name('start_purchase');
        Route::get('form/{user_id}', [KpPurchaseController::class, 'showPurchaseForm'])->name('form');
        Route::post('add_to_cart', [KpPurchaseController::class, 'addToCart'])->name('add_to_cart');
        Route::delete('remove-from-cart/{index}', [KpPurchaseController::class, 'removeFromCart'])->name('remove_from_cart');
        Route::get('cart', [KpPurchaseController::class, 'showCart'])->name('cart');
        Route::post('save-transaction', [KpPurchaseController::class, 'saveTransaction'])->name('save_transaction');
        Route::post('save_transaction_machine', [KpPurchaseController::class, 'saveTransactionForMachine'])->name('save_transaction_machine');
        Route::get('show-receipt/{transaction}', [KpPurchaseController::class, 'showReceipt'])->name('show_receipt');
        Route::get('history/{kp_waste_pref_id}', [KpPurchaseController::class, 'showPurchaseHistory'])->name('history');
        Route::get('receipt/{transaction_id}', [KpPurchaseController::class, 'showReceipt'])->name('receipt');
    });

    // 7. Sell System
    Route::prefix('sell/')->name('sell.')->group(function () {
        Route::get('form', [KpSellController::class, 'showSellForm'])->name('form');
        Route::post('store', [KpSellController::class, 'storeSellTransaction'])->name('store');
        Route::get('history', [KpSellController::class, 'showSellHistory'])->name('history');
        Route::get('receipt/{transaction}', [KpSellController::class, 'showReceipt'])->name('receipt');
        Route::delete('/users/{transaction}', [KpSellController::class, 'destroy'])->name('destroy');
    });

    Route::resource('bins',BinsController::class);

    // 8. Waste Bins Specifics
    Route::prefix('/{w_user}/waste-bins')->name('waste_bins.')->group(function () {
        Route::get('/', [AnnualTrash::class, 'index'])->name('index');
        Route::get('/create', [AnnualTrash::class, 'create'])->name('create');
        Route::post('/', [AnnualTrash::class, 'store'])->name('store');
        Route::get('/edit', [AnnualTrash::class, 'edit'])->name('edit');
    });
    // หมายเหตุ: Routes ข้างล่างนี้อยู่นอก prefix /{w_user}/waste-bins แต่อยู่ใน keptkayas group
    Route::put('waste-bins/{waste_bin}', [AnnualTrash::class, 'update'])->name('waste_bins.update');
    Route::get('waste-bins/map', [AnnualTrash::class, 'map'])->name('waste_bins.map');
    Route::get('waste-bins/viewmap', [AnnualTrash::class, 'viewmap'])->name('waste_bins.viewmap');

    // 9. Annual Payments
    Route::prefix('annual-payments')->name('annual_payments.')->group(function () {
        Route::get('/', [AnnualTrashSubscription::class, 'index'])->name('index');
        Route::get('/invoice', [AnnualTrashSubscription::class, 'invoice'])->name('invoice');
        Route::get('/{wasteBinSubscription}', [AnnualTrashSubscription::class, 'show'])->name('show');
        Route::get('print/{wasteBinSubscription}', [AnnualTrashSubscription::class, 'print'])->name('print');
        Route::get('printReceipt/{wasteBinSubscription}', [AnnualTrashSubscription::class, 'printReceipt'])->name('printReceipt');
        Route::post('/{wasteBinSubscription}/payments', [AnnualTrashSubscription::class, 'storePayment'])->name('store_payment');
        Route::post('print-selected-invoices', [AnnualTrashSubscription::class, 'printSelectedInvoices'])->name('print_selected_invoices');
        Route::post('/create-subscription', [AnnualTrashSubscription::class, 'createSubscription'])->name('create_subscription');
        Route::post('/history', [AnnualTrashSubscription::class, 'history'])->name('history');
    });

    Route::resource('/kp_budgetyear', KpBudgetYearController::class);
    Route::resource('wbin_payrate_per_months', AnnualTrashPayratePerMonthController::class);

    // 10. Tbank System
    Route::prefix('tbank/')->name('tbank.')->group(function () {
        Route::resource('items_group', KpTbankItemsGroupsController::class);
        Route::resource('units', KpTbankUnitsController::class);

        Route::get('cart/cartLists/{user_id}', [CartController::class, 'cartLists'])->name('cart.cart_lists');
        Route::get('cart/add_to_cart/{id}/{amount}', [CartController::class, 'addToCart'])->name('cart.add_to_cart');
        Route::resource('cart', CartController::class);

        Route::get('prices/export', [KpTbankPriceController::class, 'export'])->name('prices.export');
         Route::post('prices/import', [KpTbankPriceController::class, 'import'])->name('prices.import');
        Route::resource('prices', KpTbankPriceController::class);


        Route::prefix('items/')->name('items.')->group(function () {
            Route::get('buyItems/{user_id?}', [KpTbankItemsController::class, 'buyItems'])->name('buy_items');
            Route::get('search_items/{itemscode}', [KpTbankItemsController::class, 'search_items'])->name('search_items');
            Route::get('set_items_pricepoint', [KpTbankItemsController::class, 'set_items_pricepoint'])->name('set_items_pricepoint');
            Route::get('generate-code/{group_id}', [KpTbankItemsController::class, 'generateCode'])->name('generate_code');

            Route::get('trash', [KpTbankItemsController::class, 'trash'])->name('trash');
            Route::get('{id}/restore', [KpTbankItemsController::class, 'restore'])->name('restore');
            Route::delete('{id}/force-delete', [KpTbankItemsController::class, 'forceDelete'])->name('forceDelete');

            Route::resource('', KpTbankItemsController::class);
            Route::get('export', [KpTbankItemsController::class, 'exportTemplate'])->name('export');
            Route::post('import', [KpTbankItemsController::class, 'import'])->name('import');

            Route::get('pending-ef', [KpTbankItemsController::class, 'pendingEf'])->name('pendingEf');
            Route::post('update-ef-bulk', [KpTbankItemsController::class, 'updateEfBulk'])->name('updateEfBulk');
        });
    });

    // 11. User Groups
    Route::prefix('kp_usergroup')->name('kp_usergroup.')->group(function () {
        Route::get('/{usergroup_id}/infos', [KpUserGroupController::class, 'infos'])->name('usergroup.infos');
        Route::resource('/', KpUserGroupController::class);
    });
}); // End Main Group
