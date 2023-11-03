<?php

use App\Http\Controllers\Api\SubzoneController;
use App\Http\Controllers\Api\ZoneController;
use App\Http\Controllers\Api\UsersController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::middleware(['throttle:api'])->name('api.')->group(function () {
    Route::prefix('subzone')->group(function () {
        Route::get('/{zone_id}', [SubzoneController::class, 'subzone'])->name('subzone');
        Route::get('/delete/{id}', [SubzoneController::class, 'delete'])->name('subzone.delete');
        Route::get('/get_members_subzone_infos/{zone_id}', 'Api\SubzoneController@get_members_subzone_infos');
        Route::get('/get_members_last_inactive_invperiod/{zone_id}', 'Api\SubzoneController@get_members_last_inactive_invperiod');
    });

    Route::prefix('zone')->group(function () {
        Route::get('/', [ZoneController::class,'index']);
        Route::delete('/delete/{id}', [ZoneController::class,'delete'])->name('zone.delete');
        Route::get('/getzone_and_subzone', [ZoneController::class,'getZoneAndSubzone']);
        Route::get('/users_by_zone/{zone_id}', [ZoneController::class,'users_by_zone']);
        Route::get('/undertakenZoneAndSubzone/{id}', [ZoneController::class,'undertakenZoneAndSubzone']);
    });

    Route::prefix('users')->group(function () {
        Route::get('/', [UsersController::class,'index']);
        Route::get('/users', [UsersController::class, 'users']);
        Route::get('/user/{user_id}', [UsersController::class, 'user']);
        Route::get('/check_line_id/{id}', [UsersController::class, 'check_line_id']);
        Route::get('/update_line_id/{user_id}/{line_id}', [UsersController::class, 'update_line_id']);
        Route::get('/search/{val}/{type?}', [UsersController::class, 'search']);
        Route::post('/search2', [UsersController::class, 'search2']);
        Route::get('/by_zone/{zone_id}', [UsersController::class, 'by_zone']);
        Route::get('/report_by_subzone/{zone_id}', [UsersController::class, 'report_by_subzone']);
        Route::get('/findsearchselected/{val}', [UsersController::class, 'findsearchselected']);
        Route::get('/{user_cat_id}/{twman_id}', [UsersController::class, 'users_by_subzone']);
        Route::get('/usersbycategory/{cate_id}', [UsersController::class, 'usersbycategory']);
        Route::post('/store', [UsersController::class, 'store']);
        Route::get('/set_session_id/{user_id}/{session_id}', [UsersController::class, 'set_session_id']);
        Route::get('/init_settings', [UsersController::class, 'init_settings']);

    });
});

