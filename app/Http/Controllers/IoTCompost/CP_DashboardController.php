<?php

namespace App\Http\Controllers\IoTCompost;

use App\Http\Controllers\Controller;
use App\Models\IoTCompost\CP_SensorData;
use Illuminate\Http\Request;

class CP_DashboardController extends Controller
{
    public function index()
    {
        // ดึงข้อมูล 100 รายการล่าสุด
        $sensorData = CP_SensorData::latest()->take(100)->get();

        // ส่งข้อมูลไปยังหน้า dashboard
        return view('dashboard', compact('sensorData'));
    }
}