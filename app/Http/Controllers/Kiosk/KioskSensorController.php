<?php

namespace App\Http\Controllers\Kiosk;

use App\Http\Controllers\Controller;
use App\Models\Kiosk;
use App\Models\KioskSensor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KioskSensorController extends Controller
{
    /**
     * แสดงรายการเซ็นเซอร์ทั้งหมดของตู้ Kiosk
     */
    public function index($kiosk_id)
    {
        $kiosk = Kiosk::withoutGlobalScopes()->findOrFail($kiosk_id);
        $sensors = KioskSensor::where('kiosk_id', $kiosk_id)->get();

        return view('kiosk.sensors.index', compact('kiosk', 'sensors'));
    }

    /**
     * บันทึกการเพิ่มเซ็นเซอร์ใหม่ประจำตู้
     */
    public function store(Request $request, $kiosk_id)
    {
        $request->validate([
            'sensor_code' => 'required|string|max:50',
            'sensor_name' => 'required|string|max:100',
            'sensor_type' => 'required|in:mcu_sensor,actuator,camera,system',
            'status' => 'required|in:ok,warning,error,offline',
        ]);

        $kiosk = Kiosk::withoutGlobalScopes()->findOrFail($kiosk_id);

        // ตรวจสอบ sensor_code ซ้ำในตู้เดียวกัน
        $exists = KioskSensor::where('kiosk_id', $kiosk_id)
            ->where('sensor_code', $request->sensor_code)
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'รหัสเซ็นเซอร์นี้มีอยู่แล้วในตู้ใบนี้');
        }

        KioskSensor::create([
            'kiosk_id' => $kiosk->id,
            'sensor_code' => $request->sensor_code,
            'sensor_name' => $request->sensor_name,
            'sensor_type' => $request->sensor_type,
            'status' => $request->status,
            'last_val' => $request->last_val ?? 'NORMAL',
            'last_ping_at' => now(),
        ]);

        return redirect()->back()->with('success', 'เพิ่มเซ็นเซอร์เรียบร้อยแล้ว');
    }

    /**
     * อัปเดตข้อมูลเซ็นเซอร์
     */
    public function update(Request $request, $kiosk_id, $id)
    {
        $request->validate([
            'sensor_name' => 'required|string|max:100',
            'sensor_type' => 'required|in:mcu_sensor,actuator,camera,system',
            'status' => 'required|in:ok,warning,error,offline',
        ]);

        $sensor = KioskSensor::where('kiosk_id', $kiosk_id)->findOrFail($id);

        $sensor->update([
            'sensor_name' => $request->sensor_name,
            'sensor_type' => $request->sensor_type,
            'status' => $request->status,
            'last_val' => $request->last_val,
            'error_message' => $request->error_message,
        ]);

        return redirect()->back()->with('success', 'อัปเดตข้อมูลเซ็นเซอร์เรียบร้อยแล้ว');
    }

    /**
     * ลบเซ็นเซอร์ประจำตู้
     */
    public function destroy($kiosk_id, $id)
    {
        $sensor = KioskSensor::where('kiosk_id', $kiosk_id)->findOrFail($id);
        $sensor->delete();

        return redirect()->back()->with('success', 'ลบเซ็นเซอร์เรียบร้อยแล้ว');
    }

    public function sensorAlerts()
{
    $userOrgId = Auth::user()->org_id_fk;

    // ดึงเฉพาะเซ็นเซอร์ที่มีสถานะ error หรือ warning ของตู้ในหน่วยงานผู้ใช้
    $problematicSensors = KioskSensor::whereHas('kiosk', function ($query) use ($userOrgId) {
            $query->where('org_id_fk', $userOrgId);
        })
        ->whereIn('status', ['error', 'warning'])
        ->with('kiosk')
        ->orderBy('status', 'asc') // error ขึ้นก่อน warning
        ->orderBy('updated_at', 'desc')
        ->get();

    // นับจำนวนแยกระดับความรุนแรง
    $errorCount = $problematicSensors->where('status', 'error')->count();
    $warningCount = $problematicSensors->where('status', 'warning')->count();

    return view('kiosk.sensors.alert', compact('problematicSensors', 'errorCount', 'warningCount'));
}
}