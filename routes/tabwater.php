<?php

use App\Http\Controllers\Admin\DistrictsController;
use App\Http\Controllers\Admin\TambonsController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\Tabwater\TwDashboardController;
use App\Http\Controllers\Tabwater\TwInvoiceController;
use App\Http\Controllers\Tabwater\TwMeterReadingController;
use App\Http\Controllers\Tabwater\TwMetersController;
use App\Http\Controllers\Tabwater\TwPaymentController;
use App\Http\Controllers\Tabwater\TwPeriodController;
use App\Http\Controllers\Tabwater\TwPricingTypeController;
use App\Http\Controllers\Tabwater\TwReportsController;
use App\Http\Controllers\Tabwater\TwUserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:super admin|admin'])->prefix('tw')->name('tw.')->group(function () {
    
    Route::resource('tw_dashboard', TwDashboardController::class);
    Route::resource('tw_meters', TwMetersController::class);

    Route::get('tw_user/get-user-data/{userId}', [TwUserController::class, 'getUserData'])->name('get_user_data');
    Route::resource('tw_user', TwUserController::class);

    Route::name('admin.')->prefix('admin')->group(function(){
        Route::get('/get-districts-by-province-code/{province_id}', [DistrictsController::class, 'getDistrictsByProvinceCode'])->name('get_districts_by_province_code');
        Route::get('/get-district/{district_code}', [DistrictsController::class, 'getDistrict'])->name('get_district');
        Route::get('/get-tambons-by-district-code/{province_id}', [TambonsController::class, 'getTambonsByDistrictCode'])->name('get_tambons_by_district_code');
        Route::get('/get-tambon/{tambon_code}', [TambonsController::class, 'gettambon'])->name('get_tambon');
    });
    

    
});


Route::middleware(['auth', 'role:super admin|admin|finance staff|tabwater staff'])->prefix('tw')->name('tw.')->group(function () {
    Route::name('payment.')->prefix('payment')->group(function(){
        Route::get('/paymenthistory/{inv_period}/{subzone_id}', [TwPaymentController::class, 'paymenthistory'])->name('paymenthistory');
        Route::match(['get', 'post'], '/search', [TwPaymentController::class, 'search'])->name('search');
        Route::delete('/acc_trans_id_fk/destroy', [TwPaymentController::class, 'destroy'])->name('destroy');
        Route::post('/index_search_by_suzone', [TwPaymentController::class, 'index_search_by_suzone'])->name('index_search_by_suzone');
        Route::get('/receipt_print/{account_id_fk?}/{payments?}', [TwPaymentController::class, 'receipt_print'])->name('receipt_print');
        Route::get('/receipt_print_history/{account_id_fk?}', [TwPaymentController::class, 'receipt_print_history'])->name('receipt_print_history');
        Route::post('/store_by_inv_no', [TwPaymentController::class, 'store_by_inv_no'])->name('store_by_inv_no');

        Route::resource('/', TwPaymentController::class);
    });
    
    Route::prefix('invoice')->name('invoice.')->group(function () {
        Route::get('/{subzone_id}/zone_edit/{curr_inv_prd}', [TwInvoiceController::class, 'zone_edit'])->name('zone_edit');
        Route::post('/zone_update', [TwInvoiceController::class, 'zone_update'])->name('zone_update');
        Route::get('/zone_create/{zone_id}/{curr_inv_prd}/{new_user?}', [InvoiceController::class, 'zone_create'])->name('zone_create');
        Route::get('/get_user_invoice/{user_id}/{status?}', [TwInvoiceController::class, 'get_user_invoice'])->name('get_user_invoice');
        Route::get('/export_excel/{zone_id}/{curr_inv_prd}', [TwInvoiceController::class, 'export_excel'])->name('export_excel');

        Route::resource('/',TwInvoiceController::class);
    });
    
    Route::get('tw_meters/unrecorded-by-subzone', [TwMetersController::class, 'unrecordedMeters'])->name('tw_meters.unrecorded_by_subzone');
    Route::post('tw_periods/{period}/generate-invoices', [TWPeriodController::class, 'generateInvoices'])->name('tw_periods.generate_invoices');
    
    Route::prefix('tw_meter_readings')->name('tw_meter_readings.')->group(function () {
        Route::get('/get-previous-reading', [TwMeterReadingController::class, 'getPreviousReading'])->name('get_previous_reading');
        Route::get('/unrecorded-form', [TwMeterReadingController::class, 'showUnrecordedReadingForm'])->name('unrecorded_form');
        Route::post('/store-unrecorded', [TwMeterReadingController::class, 'storeUnrecordedReadings'])->name('store_unrecorded');
        Route::resource('/', TwMeterReadingController::class);
        // --- Routes สำหรับ Batch Edit Recorded Meter Readings ---
        Route::get('/batch-edit-recorded/{undertake_zone_block_id}/{period_id}', [TwMeterReadingController::class, 'showBatchEditRecordedReadingsForm'])->name('batch_edit_recorded_form');
        Route::post('/batch-update-recorded', [TwMeterReadingController::class, 'storeBatchEditRecordedReadings'])->name('store_batch_edit_recorded');
    });

    Route::name('reports.')->prefix('reports')->group(function(){
        Route::get('/owe', [TwReportsController::class, 'owe'])->name('owe');
        Route::get('/ledger', [TwReportsController::class, 'ledger'])->name('ledger');
        Route::get('/water_used/{from?}', [TwReportsController::class, 'water_used'])->name('water_used');
        Route::post('/dailypayment', [TwReportsController::class, 'dailypayment'])->name('dailypayment');
        Route::post('/owe_search', [TwReportsController::class, 'owe_search'])->name('owe_search');
    });

      

});



