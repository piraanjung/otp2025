<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FoodWaste\FoodWastIoTBoxesData;
use App\Models\IotCompost\CP_SensorData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class IoTBoxDataController extends Controller
{
    /**
     * Store a new sensor data entry.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // 1. ตรวจสอบข้อมูลที่ได้รับ (Validation)
        // $request->validate([
        //     'device_id' => 'required|string|max:50',
        //     'temperature' => 'required|numeric',
        //     'humidity' => 'required|numeric',
        // ]);

        // 2. ดึงค่าจาก JSON Payload
        // $device_id = $request->input('device_id');
        // $temperature = $request->input('temperature');
        // $humidity = $request->input('humidity');

        // 3. บันทึกข้อมูล (ตัวอย่าง: บันทึกเข้า Log)
        // Log::info("Data received from device: {$device_id}", [
        //     'temperature' => $temperature,
        //     'humidity' => $humidity,
        // ]);
        
        // 4. ตัวอย่างการบันทึกเข้า Database (หากคุณสร้าง Model แล้ว)
        FoodWastIoTBoxesData::create([
            'esp_device_id' => 'zz',//$device_id,
            'fwbin_id_fk' => 1,
            'temperature' => 10,//$temperature,
            'humidity' => 10,//$humidity,
            'methane_gas' => 0,
            'weight' => 0,
            'timestamp' => now(), // เวลาปัจจุบัน
             
        ]);
      
        // 5. ส่งสถานะตอบกลับไปยัง ESP8266
        return response()->json([
            'status' => 'success',
            'message' => 'Data received and processed successfully.'
        ], 201); // 201 Created/Accepted
    }
}