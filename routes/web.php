<?php

use App\Http\Controllers\AccessMenusController;
use App\Http\Controllers\Admin\ExcelController;
use App\Http\Controllers\Admin\IndexController;
use App\Http\Controllers\Admin\MetertypeController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SuperAdminAuthController;
use App\Http\Controllers\Admin\SuperUserController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ZoneController;
use App\Http\Controllers\KeptKaya\MachineController;
use App\Http\Controllers\Tabwater\CutmeterController;
use App\Http\Controllers\Tabwater\BudgetYearController;
use App\Http\Controllers\Tabwater\InvoiceController;
use App\Http\Controllers\Tabwater\InvoicePeriodController;
use App\Http\Controllers\LineLiffController;
use App\Http\Controllers\Tabwater\MeterRateConfigController;
use App\Http\Controllers\Tabwater\OwePaperController;
use App\Http\Controllers\Tabwater\PaymentController;
use App\Http\Controllers\Tabwater\ReportsController;
use App\Http\Controllers\Tabwater\SettingsController;
use App\Http\Controllers\Tabwater\StaffMobileController;
use App\Http\Controllers\Tabwater\SubzoneController;
use App\Http\Controllers\Tabwater\UserMeterInfosController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\Tabwater\TransferOldDataToNewDBController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\SqlToJsonController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\Tabwater\NotifyController;
use App\Http\Controllers\Tabwater\TwManMobileController;
use App\Http\Controllers\Tabwater\TwPricingTypeController;
use App\Http\Controllers\Tabwater\UndertakerSubzoneController;
use App\Models\User;

Route::get('/', function () {
    return view('welcome');

});

Route::get('/liff', function () {
    return view('liff');
});
Route::get('/logout', function () {
    Auth::logout();
    Session()->invalidate();
    Session()->regenerateToken();
    Session()->flush();

    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';

        // ตรวจสอบคำที่บ่งชี้ถึงอุปกรณ์มือถือ
        $ismobile = preg_match(
            "/(android|avantgo|blackberry|bolt|boost|cello|hiptop|irengin|mobi|mini|mo(bil|si)|ntellect|palm|pda|phone|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|wap|windows ce|xda|xiino)/i",
            $userAgent
        );
        if ($ismobile) {
            return redirect()->route('login');
        }
    return redirect('/');
});

Route::get('/upload-form', function () {
    return view('upload');
});

Route::post('/upload-and-convert', [SqlToJsonController::class, 'uploadAndProcess']);


Route::resource('/test', TestController::class);


Auth::routes();

Route::get('/accessmenu', [AccessMenusController::class, 'accessmenu'])->middleware(['auth'])->name('accessmenu');
Route::get('/staff_accessmenu', [AccessMenusController::class, 'staff_accessmenu'])->middleware(['auth'])->name('staff_accessmenu');


Route::get('/dashboard', [AccessMenusController::class, 'dashboard'])->middleware(['auth'])->name('dashboard');

Route::get('/lineliff', [LineLiffController::class, 'index'])->name('lineliff.index');
Route::get('/line/dashboard/{user_waste_pref_id}/{org_id}/{regis?}', [LineLiffController::class , 'dashboard']);
Route::post('/line/fine_line_id', [LineLiffController::class , 'fine_line_id']);
Route::post('/line/update_user_by_phone', [LineLiffController::class , 'update_user_by_phone']);
Route::post('/line/login', [LineLiffController::class , 'handleLineLogin']);

// $a ='auth:web_hs1,web_kp1';

Route::prefix('staffs')->name('keptkayas.staffs.')->group(function () {
    Route::get('/', [StaffController::class, 'index'])->name('index');
    Route::get('/create', [StaffController::class, 'create'])->name('create');
    Route::post('/', [StaffController::class, 'store'])->name('store');
    Route::get('/{staff}', [StaffController::class, 'show'])->name('show');
    Route::get('/{staff}/edit', [StaffController::class, 'edit'])->name('edit');
    Route::put('/{staff}', [StaffController::class, 'update'])->name('update');
    Route::delete('/{staff}', [StaffController::class, 'destroy'])->name('destroy');
});

Route::prefix('zones')->name('zones.')->group(function () {
    Route::get('/getzones/{tambon_id}', [ZoneController::class, 'getZones'])->name('getzones');

});

Route::prefix('tabwater/staff/mobile/')->name('tabwater.staff.mobile.')->group(function () {
    Route::get('{subzone_id}/{status}/members',[ StaffMobileController::class, 'members'])->name('members');
    Route::get('{subzone_id}/membersJson',[ StaffMobileController::class, 'membersJson'])->name('membersJson');
    Route::get('{meter_id}/meter_reading',[ StaffMobileController::class, 'meter_reading'])->name('meter_reading');
    Route::post('process-meter-image', [StaffMobileController::class, 'process_meter_image'])->name('process_meter_image');
    Route::resource('/',StaffMobileController::class);
   
   
});

Route::prefix('tabwater/notify')->name('tabwater.notify.')->group(function () {
    Route::get('/', [NotifyController::class, 'index'])->name('index');
    Route::post('/', [NotifyController::class, 'store'])->name('store');
});

Route::get('twmanmobile', [TwManMobileController::class, 'index'])->name('twmanmobile');
Route::get('twmanmobile/main', [TwManMobileController::class, 'main'])->name('twmanmobile.main');
Route::get('twmanmobile/edit_members_subzone_selected', [TwManMobileController::class, 'edit_members_subzone_selected'])->name('twmanmobile.edit_members_subzone_selected');


 
Route::middleware(['auth', 'role:Admin|Super Admin'])->name('admin.')->prefix('admin')->group(function () {
    Route::get('/register', [UserController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [UserController::class, 'register']);
    Route::get('/transfer_old_data', [TransferOldDataToNewDBController::class, 'index'])->name('transfer_old_data');
    Route::get('/', [IndexController::class, 'index'])->name('index');
    Route::resource('/roles', RoleController::class);
    Route::post('/roles/{role}/permissions', [RoleController::class, 'givePermission'])->name('roles.permissions');
    Route::delete('/roles/{role}/permissions/{permission}', [RoleController::class, 'revokePermission'])->name('roles.permissions.revoke');
    Route::post('/permissions/{permission}/roles', [PermissionController::class, 'assignRole'])->name('permissions.roles');
    Route::delete('/permissions/{permission}/roles/{role}', [PermissionController::class, 'removeRole'])->name('permissions.roles.remove');
    Route::resource('/permissions', PermissionController::class);

    Route::prefix('super_users')->name('super_users.')->group(function(){
        Route::resource('/', SuperUserController::class);
    });

    //tabwater
    Route::prefix('users/')->name('users.')->group(function(){
        Route::get('', [UserController::class, 'index'])->name('index');
        Route::get('staff', [UserController::class, 'staff'])->name('staff');
        Route::get('create', [UserController::class, 'create'])->name('create');
        Route::get('{user_id}/edit/{addmeter?}', [UserController::class, 'edit'])->name('edit');
        Route::post('store', [UserController::class, 'store'])->name('store');
        Route::post('users_search', [UserController::class, 'users_search'])->name('users_search');
        Route::put('{user_id}/update', [UserController::class, 'update'])->name('update');
        Route::get('{user}', [UserController::class, 'show'])->name('show');
        Route::get('{user_id}/cancel', [UserController::class, 'cancel'])->name('cancel');
        // Route::delete('{user}', [UserController::class, 'destroy'])->name('destroy');
        Route::delete('{meter_id}/destroy', [UserController::class, 'destroy'])->name('destroy');
        Route::get('{user}/history', [UserController::class, 'history'])->name('history');
        Route::post('{user}/roles', [UserController::class, 'assignRole'])->name('roles');
        Route::delete('{user}/roles/{role}', [UserController::class, 'removeRole'])->name('roles.remove');
        Route::get('{user_id}/permissions', [UserController::class, 'givePermission'])->name('permissions');
        Route::delete('{user}/permissions/{permission}', [UserController::class, 'revokePermission'])->name('permissions.revoke');
        

    });
    
    Route::resource('/invoice_period', InvoicePeriodController::class);
    Route::get('/metertype/{metertype_id}/infos', [MetertypeController::class, 'infos'])->name('metertype.infos');

    Route::resource('/metertype', MetertypeController::class);


    Route::get('/budgetyear/invoice_period_list/{budgetyear_id}', [BudgetYearController::class, 'invoice_period_list'])->name('budgetyear.invoice_period_list');
    Route::resource('/budgetyear', BudgetYearController::class);
    Route::resource('/zone', ZoneController::class);
    Route::resource('/subzone', SubzoneController::class);
    Route::get('/subzone/{zone_id}/getSubzone', [SubzoneController::class, 'getSubzone'])->name('subzone.getSubzone');


     Route::get('/settings/invoice', [SettingsController::class, 'invoice'])->name('settings.invoice');
    Route::post('/settings/invoice_and_vat', [SettingsController::class, 'update_invoice_and_vat'])->name('settings.invoice_and_vat');
    Route::post('/settings/create_and_update', [SettingsController::class, 'create_and_update'])->name('settings.create_and_update');
    Route::post('/settings/store_users', [SettingsController::class, 'store_users'])->name('settings.store_users');
    Route::resource('/settings', SettingsController::class);

    Route::post('/excel/store_invoice', [ExcelController::class, 'store_invoice'])->name('excel.store_invoice');
    Route::post('/excel/import_invoice_byzone', [ExcelController::class, 'import_invoice_byzone'])->name('excel.import_invoice_byzone');
    Route::post('/excel/import_invoice_old', [ExcelController::class, 'import_invoice_old'])->name('excel.import_invoice_old');
    Route::resource('/excel', ExcelController::class);


    Route::get('/owepaper/index', [OwePaperController::class, 'index'])->name('owepaper.index');
    Route::post('/owepaper/print', [OwePaperController::class, 'print'])->name('owepaper.print');
    Route::resource('meter_rates', MeterRateConfigController::class);
    Route::resource('pricing_types', TwPricingTypeController::class);

     Route::prefix('settings')->name('settings.')->group(function () {
    //     Route::get('/', [SuperAdminSettingsController::class, 'showSettingsForm'])->name('settings_form');

    //     Route::post('/import-provinces', [SuperAdminSettingsController::class, 'importProvinces'])->name('import.provinces');
    //     Route::get('/export-provinces', [SuperAdminSettingsController::class, 'exportProvinces'])->name('export.provinces');
    //     // Routes สำหรับ Import/Export Districts
    //     Route::post('/import-districts', [SuperAdminSettingsController::class, 'importDistricts'])->name('import.districts');
    //     Route::get('/export-districts', [SuperAdminSettingsController::class, 'exportDistricts'])->name('export.districts');
    //     Route::get('/user-to-tabwater', [SuperAdminSettingsController::class, 'userToTabwater'])->name('user_to_tabwater');

    //     // Routes สำหรับ Import/Export Tambons
    //     Route::post('/import-tambons', [SuperAdminSettingsController::class, 'importTambons'])->name('import.tambons');
    //     Route::get('/export-tambons', [SuperAdminSettingsController::class, 'exportTambons'])->name('export.tambons');

    //     Route::post('/import-tw_zones', [SuperAdminSettingsController::class, 'importTWZones'])->name('import.tw_zones');
    //     Route::get('/export-tw_zones', [SuperAdminSettingsController::class, 'exportTWZones'])->name('export.tw_zones');

    //     // Routes สำหรับ Import/Export TW_ZoneBlocks
    //     Route::post('/import-tw-zoneblocks', [SuperAdminSettingsController::class, 'importTWZoneBlocks'])->name('import.tw_zoneblocks');
    //     Route::get('/export-tw-zoneblocks', [SuperAdminSettingsController::class, 'exportTWZoneBlocks'])->name('export.tw_zoneblocks');

    //     // Routes สำหรับ Import/Export Organizations
    //     Route::post('/import-organizations', [SuperAdminSettingsController::class, 'importOrganizations'])->name('import.organizations');
    //     Route::get('/export-organizations', [SuperAdminSettingsController::class, 'exportOrganizations'])->name('export.organizations');

    //     Route::post('/import-users', [SuperAdminSettingsController::class, 'importUsers'])->name('import.users');
    //     Route::get('/export-users', [SuperAdminSettingsController::class, 'exportUsers'])->name('export.users');

    //     // Routes สำหรับ Import/Export TwMeters
    //     Route::post('/import-twmeters', [SuperAdminSettingsController::class, 'importTwMeters'])->name('import.tw_meters');
    //     Route::get('/export-twmeters', [SuperAdminSettingsController::class, 'exportTwMeters'])->name('export.tw_meters');
     });


    Route::get('undertaker_subzone', [UndertakerSubzoneController::class, 'index'])->name('undertaker_subzone');
    // Route::get('undertaker_subzone/create', 'UndertakerSubzoneController@create');
    // Route::post('undertaker_subzone/store', 'UndertakerSubzoneController@store');
    // Route::get('undertaker_subzone/update/{id}', 'UndertakerSubzoneController@update');
    // Route::get('undertaker_subzone/edit/{id}', [UndertakerSubzoneController::class, 'edit']);
    // Route::get('undertaker_subzone/delete/{id}', 'UndertakerSubzoneController@delete');
});


Route::middleware(['auth', 'role:Admin|finance|Super Admin'])->group(function () {
    Route::prefix('payment/')->name('payment.')->group(function(){
        Route::get('paymenthistory/{inv_period}/{subzone_id}', [PaymentController::class, 'paymenthistory'])->name('paymenthistory');
        Route::match(['get', 'post'], 'search', [PaymentController::class, 'search'])->name('search');
        Route::delete('acc_trans_id_fk/destroy', [PaymentController::class, 'destroy'])->name('destroy');
        Route::post('index_search_by_suzone', [PaymentController::class, 'index_search_by_suzone'])->name('index_search_by_suzone');
        Route::get('receipt_print/{account_id_fk?}/{payments?}', [PaymentController::class, 'receipt_print'])->name('receipt_print');
        Route::get('receipt_print_history/{account_id_fk?}', [PaymentController::class, 'receipt_print_history'])->name('receipt_print_history');
        Route::post('store_by_inv_no', [PaymentController::class, 'store_by_inv_no'])->name('store_by_inv_no');

        Route::resource('', PaymentController::class);
    });
    

    Route::prefix('invoice/')->name('invoice.')->group(function(){
        Route::resource('', InvoiceController::class);
        Route::get('get_user_invoice/{user_id}/{status?}', [InvoiceController::class, 'get_user_invoice'])->name('get_user_invoice');
        Route::get('{subzone_id}/zone_edit/{curr_inv_prd}', [InvoiceController::class, 'zone_edit'])->name('zone_edit');
        Route::get('{subzone_id}/zone_update', [InvoiceController::class, 'zone_update'])->name('zone_update');
        Route::post('zone_update', [InvoiceController::class, 'zone_update'])->name('zone_update');
        Route::get('{subzone_id}/invoiced_lists', [InvoiceController::class, 'invoiced_lists'])->name('invoiced_lists');
        Route::get('reset_invioce_bill/{inv_id}', [InvoiceController::class, 'reset_invioce_bill'])->name('reset_invioce_bill');
        Route::post('print_multi_invoice', [InvoiceController::class, 'print_multi_invoice'])->name('print_multi_invoice');
        Route::post('delete_duplicate_inv', [InvoiceController::class, 'delete_duplicate_inv'])->name('delete_duplicate_inv');
        Route::get('print_invoice/{zone_id}/{curr_inv_prd}', [InvoiceController::class, 'print_invoice'])->name('print_invoice');
        Route::post('invoice_bill_print', [InvoiceController::class, 'invoice_bill_print'])->name('invoice_bill_print');
        Route::get('get_invoice_and_invoice_history/{meter_id}/{status?}', [InvoiceController::class,'get_invoice_and_invoice_history' ])->name('get_invoice_and_invoice_history');
    });

    Route::prefix('reports/')->name('reports.')->group(function(){
        Route::post('export', [ReportsController::class, 'export'])->name('export');
        Route::get('owe', [ReportsController::class, 'owe'])->name('owe');
        Route::get('ledger', [ReportsController::class, 'ledger'])->name('ledger');
        Route::get('water_used/{from?}', [ReportsController::class, 'water_used'])->name('water_used');
        Route::post('dailypayment', [ReportsController::class, 'dailypayment'])->name('dailypayment');
        Route::get('dailypayment2', [ReportsController::class, 'dailypayment2'])->name('dailypayment2');
        Route::post('owe_search', [ReportsController::class, 'owe_search'])->name('owe_search');
        Route::get('meter_record_history/{budgetyear?}/{zone_id?}', [ReportsController::class, 'meter_record_history'])->name('meter_record_history');
    });
});

Route::group(['middleware' => ['role:Admin|tabwater|Super Admin']], function () {

    Route::resource('/invoice', InvoiceController::class);
    Route::get('/invoice/zone_create/{zone_id}/{curr_inv_prd}/{new_user?}', [InvoiceController::class, 'zone_create'])->name('invoice.zone_create');
    Route::get('/invoice/export_excel/{zone_id}/{curr_inv_prd}', [InvoiceController::class, 'export_excel'])->name('invoice.export_excel');
    Route::get('/invoice/{subzone_id}/zone_edit/{curr_inv_prd}', [InvoiceController::class, 'zone_edit'])->name('invoice.zone_edit');
    Route::get('/invoice/{subzone_id}/zone_update', [InvoiceController::class, 'zone_update'])->name('invoice.zone_update');
    Route::get('/invoice/{subzone_id}/invoiced_lists', [InvoiceController::class, 'invoiced_lists'])->name('invoice.invoiced_lists');
    Route::post('invoice/print_multi_invoice', [InvoiceController::class, 'print_multi_invoice'])->name('invoice.print_multi_invoice');

    Route::get('/cutmeter/print_install_meter/{cutmeter_id}', [CutmeterController::class, 'print_install_meter'])->name('cutmeter.print_install_meter');
    Route::get('/cutmeter/cutmeterProgress/{id}', [CutmeterController::class, 'cutmeterProgress'])->name('cutmeter.progress');
    Route::get('/cutmeter/installMeterProgress/{id}', [CutmeterController::class, 'installMeterProgress'])->name('cutmeter.installmeter');
    Route::resource('/cutmeter', CutmeterController::class);
    Route::resource('meter_types', MeterTypeController::class);

    Route::prefix('usermeter_infos')->name('usermeter_infos.')->group(function () {
    Route::resource('/',  UserMeterInfosController::class);
    Route::get('/edit_invoices/{meter_id}',  [UserMeterInfosController::class, 'edit_invoices'])->name('edit_invoices');
    Route::post('/store_edited_invoice',  [UserMeterInfosController::class, 'store_edited_invoice'])->name('store_edited_invoice');
    });
});




Route::prefix('superadmin')->name('superadmin.')->group(function () {
    Route::get('/login', [SuperAdminAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [SuperAdminAuthController::class, 'login'])->name('login.post');
    Route::post('/logout', [SuperAdminAuthController::class, 'logout'])->name('logout');
    Route::resource('staff', StaffController::class);
    Route::resource('/machines',MachineController::class);

});

Route::middleware(['auth'])->group(function () {
    Route::get('superadmin/dashboard', function () {
        return view('superadmin.dashboard'); // หน้า Dashboard หลัง Login

    })->name('superadmin.dashboard');
});

require __DIR__ . '/auth.php';
require __DIR__ . '/foodwaste_route.php';
require __DIR__ . '/keptkaya_route.php';
require __DIR__ . '/keptkaya_mobile_route.php';
// require __DIR__ . '/tabwater.php';
