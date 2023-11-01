<?php

use App\Http\Controllers\Admin\IndexController;
use App\Http\Controllers\Admin\MetertypeController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ZoneController;
use App\Http\Controllers\BudgetYearController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\InvoicePeriodController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SubzoneController;
use App\Http\Controllers\TestController;
use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\MockObject\Invocation;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::middleware(['auth', 'role:admin'])->name('admin.')->prefix('admin')->group(function () {
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
    Route::put('/users/{user}/update', [UserController::class, 'update'])->name('users.update');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
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
    Route::post('/settings/create_and_update', [SettingsController::class, 'create_and_update'])->name('settings.create_and_update');
    Route::resource('/settings',SettingsController::class);

});
Route::resource('/test',TestController::class);


Route::middleware(['auth', 'role:admin|finance'])->group(function () {
    Route::resource('/invoice',InvoiceController::class);
});

require __DIR__ . '/auth.php';
