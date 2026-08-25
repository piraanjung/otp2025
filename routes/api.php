<?php

use App\Http\Controllers\Admin\OrgSelectorController;
use App\Http\Controllers\Api\DeviceController;
use App\Http\Controllers\Api\LineController;
use App\Http\Controllers\Api\OwepaperController;
use App\Http\Controllers\Api\SubzoneController;
use App\Http\Controllers\Api\ZoneController;
use App\Http\Controllers\Api\UsersController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\KeptkayaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\IoTBoxDataController;

use App\Http\Controllers\Api\OcrController;
use App\Http\Controllers\FunctionsController;
use App\Http\Controllers\Api\FunctionsController as apiFunctionsController;
use App\Http\Controllers\Api\KioskController;
use App\Http\Controllers\Api\UnknownItemReviewController;
use App\Http\Controllers\Kiosk\KioskController as WebKioskController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\KeptKaya\MachineController;
use App\Http\Controllers\Kiosk\KioskApiController;
use Illuminate\Support\Facades\Log;


Route::get('/testget', function () {
    return response()->json([
        'testget' => 2222
    ]);
});
Route::post('/testpost', function (Request $request) {
    $data = $request->json()->all();
    return response()->json([
        'sensor' => $request->sensor
    ]);
});

Route::get('/health-check', function () {
    return response()->json([
        'status' => 'online',
        'message' => 'AiroBact Server is ready',
        'timestamp' => now()
    ], 200);
});

Route::get('/keptkaya/kp_items_recycle_info', [KeptkayaController::class, 'kp_items_recycle_info']);
Route::get('/keptkaya/members/{org_id}', [KeptkayaController::class, 'members']);
Route::post('/keptkaya/store_purchase', [KeptkayaController::class, 'store_purchase']);

// =====================================================x====================
// 🌿 [Branch: backend/feature-chunk-image-receiver]
// เพิ่ม Route รองรับการทยอยอัปโหลดรูปภาพขยะทีละใบจากคิวเบื้องหลัง
// =========================================================================
Route::post('/kiosk/upload-item-image-chunk', [KioskController::class, 'uploadItemImageChunk']);

Route::get('/kiosk/index', [KioskController::class, 'index']);
Route::post('/kiosk/upload', [KioskController::class, 'upload']);
Route::get('/kiosk/activate', [KioskController::class, 'activateKiosk']);
Route::get('/kiosk/ready', [KioskController::class, 'setReady']);
Route::get('/kiosk/object-detected', [KioskController::class, 'objectDetected']);
Route::get('/kiosk/check-command', [KioskController::class, 'checkCommand']);
Route::get('/kiosk/drop-object', [KioskController::class, 'dropObject']);
Route::get('/kiosk/sleep', [KioskController::class, 'sleepMode']);
Route::post('/kiosk/submit-transaction', [KioskController::class, 'submitTransaction']);
Route::get('/kiosk/get-rates', [KioskController::class, 'getRates']);
Route::post('/kiosk/upload-offline-images', [KioskController::class, 'uploadOfflineImages']);
Route::get('/kiosk/check-transaction/{kiosk_id}', [KioskController::class, 'checkTransactionStatus']);

Route::post('/kiosk/wake-up', [WebKioskController::class, 'wakeUp']);
Route::post('/kiosk/match', [WebKioskController::class, 'matchKiosk']);

Route::prefix('kiosk/unknown-items')->group(function () {
    Route::get('/pending', [UnknownItemReviewController::class, 'getPendingItems']);
    Route::post('/{id}/warn', [UnknownItemReviewController::class, 'warnUser']);
    Route::post('/{id}/verify', [UnknownItemReviewController::class, 'verifyAndMoveToDetail']);
});

// Endpoint สำหรับ Frontend (Browser) เพื่อส่งคำสั่ง "Start" ไปยัง ESP8266
Route::post('/device/start-sale', [DeviceController::class, 'startSale']);
Route::get('/device/check-bottle-status', [DeviceController::class, 'checkBottleStatus']);
Route::post('/bottle/upload-photo', [ImageController::class, 'uploadPhoto']);
Route::post('/upload-unknown', [ImageController::class, 'uploadUnknownPhoto']);
// 1. Endpoint ที่ Web ใช้ Polling (GET) - อ่านค่า status จาก Server
Route::get('/device/check-object-status', [DeviceController::class, 'getSensorStatus']);
Route::get('/device/config-price-points', [DeviceController::class, 'configPricePoints']);
Route::get('/device/status', [MachineController::class, 'getMachineStatus']);
// 2. Endpoint ที่ Web ใช้ส่งคำสั่ง (POST) - ส่งค่า reject/accept ไปยัง Server
// Route::post('/device/control', [DeviceController::class, 'receiveControlSignal']);
Route::get('/device/get_command', [MachineController::class, 'getEspCommand']);
Route::post('/device/notify', [MachineController::class, 'handleEspWakeup']);
Route::post('/device/command', [MachineController::class, 'saveAiCommand']);
Route::post('/machine/update-status', [MachineController::class, 'updateMachineStatus'])->name('api.machine.update.status');
Route::post('/device/control', [MachineController::class, 'controlDevice'])->name('api.device.control');
Route::get('/device/get-control/{machine_id}', [MachineController::class, 'getControlCommand']);

// 3. ⭐️ NEW: Endpoint สำหรับจำลองการอัปเดตสถานะเซนเซอร์ (ใช้ Postman/Browser)
Route::post('/device/status-simulator', [DeviceController::class, 'updateSensorStatus']);

// Endpoint สำหรับ ESP8266 เพื่อส่งค่า hasBottle=1 กลับมายัง Server
Route::post('/device/update-status', [DeviceController::class, 'updateStatus']);

Route::post('/ocr', [OcrController::class, 'readMeter']);
// -------------------------------------------------------------
// LINE LIFF - Authentications & Multi-Organization Mapping Routes
// -------------------------------------------------------------
Route::prefix('line')->group(function () {
    // 1. เช็คสถานะเริ่มต้นว่า Line ID นี้เคยผูกกับระบบแล้วหรือยัง
    Route::post('/check-user', [LineController::class, 'checkLineUser']);

    // 2. ตรวจสอบยืนยันตัวตนหลัก (Step 1: เช็คเบอร์โทร / Step 2: เช็คเลขบัตรประชาชน + อัปเดตข้อมูล)
    Route::post('/verify-user', [LineController::class, 'verifyUser']);

    // 3. บันทึกองค์กรที่ผู้ใช้เลือกเข้าสู่ระบบใน Session หรือเก็บประวัติชั่วคราว
    Route::post('/set-session-org', [LineController::class, 'setSessionOrg']);

    Route::get('/', [LineController::class, 'index'])->name('line.index');
    Route::post('/find_line_id', [LineController::class, 'find_line_id']);
    Route::post('/update_user_line_id', [LineController::class, 'find_line_id']);
    Route::get('/user_qrcode', [LineController::class, 'user_qrcode']);
    Route::post('/update_user_by_phone', [LineController::class, 'update_user_by_phone']);
    Route::post('/user_line_register', [LineController::class, 'user_line_register']);
    Route::post('/webhook', [LineController::class, 'handleWebhook']);
    Route::get('/reply/{lineUserId}/{replyToken}', [LineController::class, 'replyWithLastReceipt']);
    Route::get('/buildFlexReceipt/{transaction}', [LineController::class, 'buildFlexReceipt']);
    Route::post('/findUserByPhone', [LineController::class, 'findUserByPhone']);

    Route::get('/get_org_lists/{org_type}', [LineController::class,'getOrgLists']);
    Route::get('/getzones/{tambon_id}', [LineController::class, 'getZones'])->name('getzones');

});

Route::get('/sensor_data', [IoTBoxDataController::class, 'store']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('users')->group(function () {
    Route::get('/findUserByUserId/{userId}', [UsersController::class, 'findByUserId']);
    Route::get('/findUserByPhone/{phone}', [UsersController::class, 'findByPhone']);
});

Route::middleware(['throttle:api'])->name('api.')->group(function () {
    Route::prefix('subzone')->group(function () {
        Route::get('/{zone_id}', [SubzoneController::class, 'subzone'])->name('subzone');
        Route::get('/delete/{id}', [SubzoneController::class, 'delete'])->name('subzone.delete');
        Route::get('/get_members_subzone_infos/{zone_id}', 'Api\SubzoneController@get_members_subzone_infos');
        Route::get('/get_members_last_inactive_invperiod/{zone_id}', 'Api\SubzoneController@get_members_last_inactive_invperiod');
        Route::get('/get_subzones_in_zone/{zone_id}', [SubzoneController::class, 'get_subzones_in_zone']);
    });

    Route::prefix('zone')->group(function () {
        Route::get('/', [ZoneController::class, 'index']);
        Route::delete('/delete/{id}', [ZoneController::class, 'delete'])->name('zone.delete');
        Route::get('/getzoxne_and_subzone', [ZoneController::class, 'getZoneAndSubzone']);
        Route::get('/users_by_zone/{zone_id}', [ZoneController::class, 'users_by_zone']);
        Route::get('/undertakenZoneAndSubzone/{id}', [ZoneController::class, 'undertakenZoneAndSubzone']);
    });

    Route::prefix('users')->group(function () {
        Route::get('/', [UsersController::class, 'index']);
        Route::get('/findUserByUserId/{userID}', [UsersController::class, 'findUserByUserId']);
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
        Route::get('/usermeter_info_get_invoice_status_count/{subzone_id}/{status}', [UsersController::class, 'usermeter_info_get_invoice_status_count']);


        Route::post('/authen', [UsersController::class, 'authen']);
        Route::post('/staff_authen', [UsersController::class, 'staff_authen']);
    });

    Route::prefix('invoice')->group(function () {
        Route::get('/', [InvoiceController::class, 'index']);
        Route::put('/update/{invoice_id}', [InvoiceController::class, 'update']);
        Route::post('/update2', [InvoiceController::class, 'update2']);
        Route::post('/create', [InvoiceController::class, 'create']);
        Route::post('/create_for_mobile_app', [InvoiceController::class, 'create_for_mobile_app']);
        Route::post('/create_for_mobile_app2', [InvoiceController::class, 'create_for_mobile_app2']);
        Route::get('/getLastInvoice/{user_id}', [InvoiceController::class, 'getLastInvoice']);
        Route::get('/get_user_invoice/{user_id}/{status?}', [InvoiceController::class, 'get_user_invoice']);
        Route::get('/invoice_history_current_budget_year/{user_id}', [InvoiceController::class, 'invoice_history_current_budget_year']);
        Route::get('/paid_invoice/{invoice_id}', [InvoiceController::class, 'paid_invoice']);
        Route::get('/print/{id_array}', [InvoiceController::class, 'print']);
        Route::get('/totalWaterByInvPeriod/{inv_id}', [InvoiceController::class, 'totalWaterByInvPeriod']);
        Route::get('/totalWaterByInvPeriodAndSubzone/{inv_id}/{subzone_id}', [InvoiceController::class, 'totalWaterByInvPeriodAndSubzone']);
        Route::get('/get_user_invoice_by_invId_and_mode/{inv_id}/{mode}', [InvoiceController::class, 'get_user_invoice_by_invId_and_mode']);
        Route::get('/checkInvoice/{inv_id}', [InvoiceController::class, 'checkInvoice']);
        Route::get('/zone_edit/{zone}', [InvoiceController::class, 'zone_edit']);
        Route::get('/invoiced_lists/{subzone_id}', [InvoiceController::class, 'invoiced_lists']);
        Route::get('/get_invoice_and_invoice_history/{meter_id}/{status?}', [InvoiceController::class, 'get_invoice_and_invoice_history'])->name('invoice.get_invoice_and_invoice_history');
        Route::get('/paid_by_bank_slip/{inv_id}/{paid}/{meter_id}', [InvoiceController::class, 'paid_by_bank_slip']);
        // Route::post('/paid_by_bank_slip',[InvoiceController::class,'paid_by_bank_slip' ]);
    });

    Route::prefix('subzone')->group(function () {
        Route::post('/', [SubzoneController::class, 'subzone']);
        Route::get('/delete/{id}', [SubzoneController::class, 'delete']);
        Route::get('/getSubzone/{subzone_id}', [SubzoneController::class, 'getSubzone']);
        Route::get('/get_members_subzone_infos/{zone_id}', [SubzoneController::class, 'get_members_subzone_infos']);
        Route::get('/get_members_last_inactive_invperiod/{zone_id}', [SubzoneController::class, 'get_members_last_inactive_invperiod']);
    });
    Route::prefix('owepaper')->group(function () {
        Route::get('/testIndex', [OwepaperController::class, 'testIndex']);
    });

    Route::get('/get_districts/{province_id}', [apiFunctionsController::class, 'getDistricts']);
    Route::get('/get_tambons/{district_id}', [FunctionsController::class, 'getTambons']);
    Route::get('/get_org/{tambon_id}', [FunctionsController::class, 'getOrgName']);

    Route::post('/line/groupid-finder', function (Request $request) {
        $data = $request->json()->all();

        // ตรวจสอบว่า Event มาจาก 'group'
        if (isset($data['events'][0]['source']['type']) && $data['events'][0]['source']['type'] === 'group') {
            $groupId = $data['events'][0]['source']['groupId'];

            // **รหัส Group ID จะถูกบันทึกไว้ใน Log**
            Log::alert("*********** [LINE GROUP ID FOUND] ***********");
            Log::alert("Group ID: " . $groupId);
            Log::alert("*********************************************");

            return response()->json(['status' => 'ID logged']);
        }
        return response()->json(['status' => 'Not a group event']);
    });

    // Route สำหรับรับ Transaction Log (ใช้โดย User Smartphone)
    Route::post('/kiosk/upload-log', [KioskApiController::class, 'uploadTransactionLog'])->name('uploadTransactionLog');

    // Route สำหรับรับไฟล์ภาพ (ใช้โดย ESP32-CAM ช่วงกลางคืน)
    Route::post('/kiosk/upload-image', [KioskApiController::class, 'uploadImage'])->name('uploadImage');

    // Route::prefix('cutmeter')->group(function () {
    //     Route::get('/index/{zone_id?}/{subzone_id?}', [CutmeterController::class,'index']);
    //     Route::get('/owe', 'Api\CutmeterController@owe');
    //     Route::get('/get_reciepting', 'Api\CutmeterController@get_reciepting');
    //     Route::get('/user_owe_infos/{user_id}', 'Api\CutmeterController@user_owe_infos');
    //     Route::get('/get_cutmeter_history/{user_id}', 'Api\CutmeterController@get_cutmeter_history');
    //     Route::get('/get_process_history/{user_id}/{inv_period_id}', 'Api\CutmeterController@get_process_history');
    //     Route::get('/count', 'Api\CutmeterController@count');
    //     Route::get('/test', 'Api\CutmeterController@test');
    //     Route::get('/getOweOver3CountDivideBySubzone', 'Api\CutmeterController@getOweOver3CountDivideBySubzone');

    // });

});
