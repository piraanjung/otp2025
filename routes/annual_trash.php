<?php

use App\Http\Controllers\Admin\AnnualTrashAdminController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth', 'role:Admin|Super Admin']], function () {
    Route::resource('annual_trash', AnnualTrashAdminController::class);
});
