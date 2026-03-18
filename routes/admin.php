<?php

use App\Http\Controllers\Admin\SuperUserController;
use Illuminate\Support\Facades\Route;



Route::middleware(['auth', 'role:Admin|finance|Super Admin'])->group(function () {
    Route::prefix('admin/')->name('admin.')->group(function () {
        Route::prefix('super_users')->name('super_users.')->group(function () {
            Route::resource('/', SuperUserController::class);
        });
    });
});
