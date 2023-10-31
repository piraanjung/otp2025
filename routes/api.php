<?php

use App\Http\Controllers\Api\SubzoneController;
use App\Http\Controllers\Api\ZoneController;
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
});

