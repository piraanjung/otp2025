<?php

namespace App\Http\Controllers\Tabwater;

use App\Http\Controllers\Controller;
use App\Models\Tabwater\TabwaterDevice;
use Illuminate\Http\Request;
// หากใช้ MQTT Client Package เช่น phpMQTT หรือ Laravel MQTT
// use PhpMqtt\Client\Facades\MQTT;

class TabwaterControlController extends Controller
{
    public function index()
    {
        $devices = TabwaterDevice::where('station_id', 1)->get(); // ดึงอุปกรณ์ตามสถานี
        return view('tabwater.control.index', compact('devices'));
    }

    // ฟังก์ชันสั่งเปิด/ปิด ปั๊มผ่าน AJAX
    public function toggle(Request $request, $id)
    {
        $device = TabwaterDevice::findOrFail($id);
        $newStatus = $request->input('status'); // 1 = ON, 0 = OFF

        // 1. อัปเดตสถานะในฐานข้อมูล
        $device->status = $newStatus;
        $device->save();

        // 2. ส่งคำสั่งไปยัง MQTT Broker
        if ($device->mqtt_topic) {
            $payload = json_encode([
                'device_code' => $device->device_code,
                'relay_channel' => $device->relay_channel,
                'status' => $newStatus, // 1 หรือ 0
                'timestamp' => now()->toIso8601String()
            ]);

            // ส่งข้อมูลไปยัง MQTT Topic ประจำอุปกรณ์ เช่น "TW19/relay1/command"
            // MQTT::publish($device->mqtt_topic, $payload);
        }

        return response()->json([
            'success' => true,
            'status' => $device->status,
            'message' => 'ส่งคำสั่งไปยังปั๊มเรียบร้อยแล้ว'
        ]);
    }

    // ฟังก์ชันเปลี่ยนโหมด Auto / Manual
    public function changeMode(Request $request, $id)
    {
        $device = TabwaterDevice::findOrFail($id);
        $device->control_mode = $request->input('control_mode');
        $device->save();

        return response()->json(['success' => true]);
    }
}