<?php

use App\Http\Controllers\AccessMenusController;
use App\Http\Controllers\Admin\ExcelController;
use App\Http\Controllers\Admin\IndexController;
use App\Http\Controllers\Admin\MetertypeController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SuperAdminAuthController;
use App\Http\Controllers\Admin\SuperAdminSettingsController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ZoneController;
use App\Http\Controllers\IoTCompost\CP_DashboardController;
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
use Illuminate\Http\Request;
use App\Http\Controllers\Api\UsersController as apiUserCtrl;
use App\Http\Controllers\KeptKaya\KeptKayaPurchaseController;
use App\Http\Controllers\SqlToJsonController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\Tabwater\TwManMobileController;
use App\Http\Controllers\Tabwater\TwPricingTypeController;
use App\Http\Controllers\Tabwater\UndertakerSubzoneController;
use App\Models\Admin\BudgetYear;
use App\Models\Admin\Organization;
use App\Models\Admin\OrgSettings;
use App\Models\Admin\Subzone;
use App\Models\Tabwater\Invoice;
use App\Models\User;
use PhpParser\Node\Stmt\Else_;

Route::get('/', function () {
    // return view('welcome');
    return redirect()->route('login');
});

Route::get('/liff', function () {
    return view('liff');
});
Route::get('/logout', function () {
    Auth::logout();
    Session()->invalidate();
    Session()->regenerateToken();
    Session()->flush();

    return redirect('/');
});

Route::get('/upload-form', function () {
    return view('upload');
});

Route::post('/upload-and-convert', [SqlToJsonController::class, 'uploadAndProcess']);


Route::resource('/test', TestController::class);


Auth::routes();

Route::get('/accessmenu', [AccessMenusController::class, 'accessmenu'])->middleware(['auth'])->name('accessmenu');


Route::get('/dashboard', [AccessMenusController::class, 'dashboard'])->middleware(['auth'])->name('dashboard');

Route::resource('/lineliff', LineLiffController::class);
Route::get('/line/dashboard/{user_waste_pref_id}', [LineLiffController::class , 'dashboard']);
Route::get('/line/fine_line_id/{lineUserId}/{displayName}/{imagUrl}', [LineLiffController::class , 'fine_line_id']);
Route::post('/line/login', [LineLiffController::class , 'handleLineLogin']);


Route::get('/staff_accessmenu', [AccessMenusController::class, 'staff_accessmenu'])->middleware(['auth', 'permission:access waste bank mobile'])->name('staff_accessmenu');
Route::prefix('staffs')->name('keptkayas.staffs.')->group(function () {
    Route::get('/', [StaffController::class, 'index'])->name('index');
    Route::get('/create', [StaffController::class, 'create'])->name('create');
    Route::post('/', [StaffController::class, 'store'])->name('store');
    Route::get('/{staff}', [StaffController::class, 'show'])->name('show');
    Route::get('/{staff}/edit', [StaffController::class, 'edit'])->name('edit');
    Route::put('/{staff}', [StaffController::class, 'update'])->name('update');
    Route::delete('/{staff}', [StaffController::class, 'destroy'])->name('destroy');
});

Route::prefix('tabwater/staff/mobile/')->name('tabwater.staff.mobile.')->group(function () {
    Route::get('{subzone_id}/{status}/members',[ StaffMobileController::class, 'members'])->name('members');
    Route::get('{subzone_id}/membersJson',[ StaffMobileController::class, 'membersJson'])->name('membersJson');
    Route::get('{meter_id}/meter_reading',[ StaffMobileController::class, 'meter_reading'])->name('meter_reading');
    Route::post('process-meter-image', [StaffMobileController::class, 'process_meter_image'])->name('process_meter_image');
    Route::resource('/',StaffMobileController::class);
   
   
});

Route::get('twmanmobile', [TwManMobileController::class, 'index'])->name('twmanmobile');
Route::get('twmanmobile/main', [TwManMobileController::class, 'main'])->name('twmanmobile.main');
Route::get('twmanmobile/edit_members_subzone_selected', [TwManMobileController::class, 'edit_members_subzone_selected'])->name('twmanmobile.edit_members_subzone_selected');


 
Route::middleware(['auth', 'role:Admin|Super Admin'])->name('admin.')->prefix('admin')->group(function () {
    Route::get('/transfer_old_data', [TransferOldDataToNewDBController::class, 'index'])->name('transfer_old_data');
    Route::get('/', [IndexController::class, 'index'])->name('index');
    Route::resource('/roles', RoleController::class);
    Route::post('/roles/{role}/permissions', [RoleController::class, 'givePermission'])->name('roles.permissions');
    Route::delete('/roles/{role}/permissions/{permission}', [RoleController::class, 'revokePermission'])->name('roles.permissions.revoke');
    Route::post('/permissions/{permission}/roles', [PermissionController::class, 'assignRole'])->name('permissions.roles');
    Route::delete('/permissions/{permission}/roles/{role}', [PermissionController::class, 'removeRole'])->name('permissions.roles.remove');
    Route::resource('/permissions', PermissionController::class);

    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/staff', [UserController::class, 'staff'])->name('users.staff');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::get('/users/{user_id}/edit/{addmeter?}', [UserController::class, 'edit'])->name('users.edit');
    Route::post('/users/store', [UserController::class, 'store'])->name('users.store');
    Route::post('/users/users_search', [UserController::class, 'users_search'])->name('users.users_search');
    Route::put('/users/{user_id}/update', [UserController::class, 'update'])->name('users.update');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::get('/users/{user_id}/cancel', [UserController::class, 'cancel'])->name('users.cancel');
    // Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::delete('/users/{meter_id}/destroy', [UserController::class, 'destroy'])->name('users.destroy');
    Route::get('/users/{user}/history', [UserController::class, 'history'])->name('users.history');
    Route::post('/users/{user}/roles', [UserController::class, 'assignRole'])->name('users.roles');
    Route::delete('/users/{user}/roles/{role}', [UserController::class, 'removeRole'])->name('users.roles.remove');
    Route::post('/users/{user}/permissions', [UserController::class, 'givePermission'])->name('users.permissions');
    Route::delete('/users/{user}/permissions/{permission}', [UserController::class, 'revokePermission'])->name('users.permissions.revoke');

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

    // Route::prefix('settings')->name('settings.')->group(function () {
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
    // });


    Route::get('undertaker_subzone', [UndertakerSubzoneController::class, 'index'])->name('undertaker_subzone');
    // Route::get('undertaker_subzone/create', 'UndertakerSubzoneController@create');
    // Route::post('undertaker_subzone/store', 'UndertakerSubzoneController@store');
    // Route::get('undertaker_subzone/update/{id}', 'UndertakerSubzoneController@update');
    // Route::get('undertaker_subzone/edit/{id}', [UndertakerSubzoneController::class, 'edit']);
    // Route::get('undertaker_subzone/delete/{id}', 'UndertakerSubzoneController@delete');
});


Route::middleware(['auth', 'role:Admin|finance|Super Admin'])->group(function () {
    Route::get('/payment/paymenthistory/{inv_period}/{subzone_id}', [PaymentController::class, 'paymenthistory'])->name('payment.paymenthistory');
    Route::match(['get', 'post'], '/payment/search', [PaymentController::class, 'search'])->name('payment.search');
    Route::delete('/payment/acc_trans_id_fk/destroy', [PaymentController::class, 'destroy'])->name('payment.destroy');
    Route::post('/payment/index_search_by_suzone', [PaymentController::class, 'index_search_by_suzone'])->name('payment.index_search_by_suzone');
    Route::get('/payment/receipt_print/{account_id_fk?}/{payments?}', [PaymentController::class, 'receipt_print'])->name('payment.receipt_print');
    Route::get('/payment/receipt_print_history/{account_id_fk?}', [PaymentController::class, 'receipt_print_history'])->name('payment.receipt_print_history');
    Route::post('/payment/store_by_inv_no', [PaymentController::class, 'store_by_inv_no'])->name('payment.store_by_inv_no');

    Route::resource('/payment', PaymentController::class);
    Route::resource('/invoice', InvoiceController::class);

    Route::get('/invoice/{subzone_id}/zone_edit/{curr_inv_prd}', [InvoiceController::class, 'zone_edit'])->name('invoice.zone_edit');
    Route::get('/invoice/{subzone_id}/zone_update', [InvoiceController::class, 'zone_update'])->name('invoice.zone_update');
    Route::post('/invoice/zone_update', [InvoiceController::class, 'zone_update'])->name('invoice.zone_update');
    Route::get('/invoice/{subzone_id}/invoiced_lists', [InvoiceController::class, 'invoiced_lists'])->name('invoice.invoiced_lists');
    Route::get('/invoice/reset_invioce_bill/{inv_id}', [InvoiceController::class, 'reset_invioce_bill'])->name('invoice.reset_invioce_bill');
    Route::post('invoice/print_multi_invoice', [InvoiceController::class, 'print_multi_invoice'])->name('invoice.print_multi_invoice');
    Route::post('invoice/delete_duplicate_inv', [InvoiceController::class, 'delete_duplicate_inv'])->name('invoice.delete_duplicate_inv');
    Route::get('invoice/print_invoice/{zone_id}/{curr_inv_prd}', [InvoiceController::class, 'print_invoice'])->name('invoice.print_invoice');
    Route::post('invoice/invoice_bill_print', [InvoiceController::class, 'invoice_bill_print'])->name('invoice.invoice_bill_print');


    Route::post('reports/export', [ReportsController::class, 'export'])->name('reports.export');

    Route::get('reports/owe', [ReportsController::class, 'owe'])->name('reports.owe');
    Route::get('reports/ledger', [ReportsController::class, 'ledger'])->name('reports.ledger');
    Route::get('reports/water_used/{from?}', [ReportsController::class, 'water_used'])->name('reports.water_used');
    Route::post('reports/dailypayment', [ReportsController::class, 'dailypayment'])->name('reports.dailypayment');
    Route::get('reports/dailypayment2', [ReportsController::class, 'dailypayment2'])->name('reports.dailypayment2');
    Route::post('reports/owe_search', [ReportsController::class, 'owe_search'])->name('reports.owe_search');
    Route::get('/meter_record_history/{budgetyear?}/{zone_id?}', [ReportsController::class, 'meter_record_history'])->name('reports.meter_record_history');
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
});

Route::middleware(['auth'])->group(function () {
    Route::get('superadmin/dashboard', function () {
        return view('superadmin.dashboard'); // หน้า Dashboard หลัง Login

    })->name('superadmin.dashboard');
});

require __DIR__ . '/auth.php';
require __DIR__ . '/keptkaya_route.php';
// require __DIR__ . '/tabwater.php';
