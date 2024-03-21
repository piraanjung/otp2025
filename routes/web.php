<?php

use App\Http\Controllers\Admin\ExcelController;
use App\Http\Controllers\Admin\IndexController;
use App\Http\Controllers\Admin\MetertypeController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ZoneController;
use App\Http\Controllers\BudgetYearController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\InvoicePeriodController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SubzoneController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\TransferOldDataToNewDBController;
use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\MockObject\Invocation;

Route::get('/', function () {
    return view('welcome');
});



Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::middleware(['auth', 'role:admin'])->name('admin.')->prefix('admin')->group(function () {
    Route::get('/transfer_old_data', [TransferOldDataToNewDBController::class, 'index'])->name('transfer_old_data');
    Route::get('/', [IndexController::class, 'index'])->name('index');
    Route::resource('/roles', RoleController::class);
    Route::post('/roles/{role}/permissions', [RoleController::class, 'givePermission'])->name('roles.permissions');
    Route::delete('/roles/{role}/permissions/{permission}', [RoleController::class, 'revokePermission'])->name('roles.permissions.revoke');
    Route::resource('/permissions', PermissionController::class);
    Route::post('/permissions/{permission}/roles', [PermissionController::class, 'assignRole'])->name('permissions.roles');
    Route::delete('/permissions/{permission}/roles/{role}', [PermissionController::class, 'removeRole'])->name('permissions.roles.remove');

    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/staff', [UserController::class, 'staff'])->name('users.staff');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::post('/users/store', [UserController::class, 'store'])->name('users.store');
    Route::post('/users/users_search', [UserController::class, 'users_search'])->name('users.users_search');
    Route::put('/users/{user}/update', [UserController::class, 'update'])->name('users.update');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::get('/users/{user}/history', [UserController::class, 'history'])->name('users.history');
    Route::post('/users/{user}/roles', [UserController::class, 'assignRole'])->name('users.roles');
    Route::delete('/users/{user}/roles/{role}', [UserController::class, 'removeRole'])->name('users.roles.remove');
    Route::post('/users/{user}/permissions', [UserController::class, 'givePermission'])->name('users.permissions');
    Route::delete('/users/{user}/permissions/{permission}', [UserController::class, 'revokePermission'])->name('users.permissions.revoke');

    Route::resource('/invoice_period',InvoicePeriodController::class);
    Route::get('/metertype/{metertype_id}/infos', [MetertypeController::class, 'infos'])->name('metertype.infos');

    Route::resource('/metertype',MetertypeController::class);
    Route::resource('/budgetyear',BudgetYearController::class);
    Route::resource('/zone',ZoneController::class);
    Route::resource('/subzone',SubzoneController::class);
    Route::get('/subzone/{zone_id}/getSubzone', [SubzoneController::class, 'getSubzone'])->name('subzone.getSubzone');


    Route::get('/settings/invoice', [SettingsController::class, 'invoice'])->name('settings.invoice');
    Route::post('/settings/invoice_and_vat', [SettingsController::class, 'update_invoice_and_vat'])->name('settings.invoice_and_vat');
    Route::post('/settings/create_and_update', [SettingsController::class, 'create_and_update'])->name('settings.create_and_update');
    Route::resource('/settings',SettingsController::class);
    Route::resource('/excel',ExcelController::class);

});
Route::resource('/test',TestController::class);


Route::middleware(['auth', 'role:admin|finance'])->group(function () {
    Route::get('/payment/paymenthistory/{inv_period}/{subzone_id}', [PaymentController::class, 'paymenthistory'])->name('payment.paymenthistory');
    Route::post('/payment/search', [PaymentController::class, 'search'])->name('payment.search');
    Route::post('/payment/index_search_by_suzone', [PaymentController::class, 'index_search_by_suzone'])->name('payment.index_search_by_suzone');
    Route::get('/payment/receipt_print/{account_id_fk?}', [PaymentController::class, 'receipt_print'])->name('payment.receipt_print');
    Route::get('/payment/receipt_print_history/{account_id_fk?}', [PaymentController::class, 'receipt_print_history'])->name('payment.receipt_print_history');
    Route::resource('/payment', PaymentController::class);
    Route::resource('/invoice',InvoiceController::class);
    Route::get('/invoice/{zone_id}/{curr_inv_prd}/zone_create/{new_user?}',[InvoiceController::class,'zone_create' ])->name('invoice.zone_create');
    Route::get('/invoice/{subzone_id}/zone_edit/{curr_inv_prd}',[InvoiceController::class,'zone_edit' ])->name('invoice.zone_edit');
    Route::get('/invoice/{subzone_id}/zone_update',[InvoiceController::class,'zone_update' ])->name('invoice.zone_update');
    Route::get('/invoice/{subzone_id}/invoiced_lists',[InvoiceController::class,'invoiced_lists' ])->name('invoice.invoiced_lists');
    Route::post('invoice/print_multi_invoice', [InvoiceController::class,'print_multi_invoice' ])->name('invoice.print_multi_invoice');


    Route::get('reports/owe',[ReportsController::class,'owe' ])->name('reports.owe');
    Route::post('reports/dailypayment',[ReportsController::class,'dailypayment' ])->name('reports.dailypayment');
    Route::get('reports/dailypayment2',[ReportsController::class,'dailypayment2' ])->name('reports.dailypayment2');
    Route::post('reports/owe_search',[ReportsController::class,'owe_search' ])->name('reports.owe_search');
});

require __DIR__ . '/auth.php';
