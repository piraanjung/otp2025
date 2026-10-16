<?php

namespace App\Http\Controllers\Tabwater;

use App\Http\Controllers\Controller;
use App\Models\Tabwater\TabwaterStation;
use App\Models\Tabwater\TabwaterDevice;
use App\Models\Tabwater\TabwaterQualityLog;
use App\Models\Tabwater\TabwaterDeviceLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TabWaterProductionDashboardController extends Controller
{
    /**
     * Display the 3-Column Multi-Station Dashboard.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // ดึงรายการสถานีผลิตน้ำทั้งหมด พร้อมอุปกรณ์ และค่าน้ำล่าสุด
        $query = TabwaterStation::with([
            'devices',
            'latestQualityLog',
            'staffs'
        ])->where('deleted', false);

        // หากเป็นช่างประจำสถานี (Staff) ให้เห็นเฉพาะสถานีที่ตนเองรับผิดชอบ
        // (หากเป็น Admin/Header จะเห็นทุกสถานี)
        if ($user && method_exists($user, 'hasRole') && $user->hasRole('tabwater_staff')) {
            $query->whereHas('staffs', function ($q) use ($user) {
                $q->where('staffs.id', $user->staff_id ?? $user->id);
            });
        }

        $stations = $query->get()->map(function ($station) {
            // ประมวลผลคำนวณสถานะเตือนภัยประจำสถานี (Evaluation Logic)
            $latestLog = $station->latestQualityLog;
            $alertStatus = 'normal';
            $alertReasons = [];

            if ($latestLog) {
                // เช็กความขุ่น
                if ($latestLog->turbidity_ntu > $station->max_turbidity_ntu) {
                    $alertStatus = 'danger';
                    $alertReasons[] = "ความขุ่นวิกฤต ({$latestLog->turbidity_ntu} NTU)";
                }
                // เช็กค่า pH
                if ($latestLog->ph_value !== null && ($latestLog->ph_value < $station->min_ph || $latestLog->ph_value > $station->max_ph)) {
                    $alertStatus = ($alertStatus === 'danger') ? 'danger' : 'warning';
                    $alertReasons[] = "ค่า pH นอกเกณฑ์ ({$latestLog->ph_value})";
                }
                // เช็กระดับน้ำใสในถังพัก (ตัดปั๊มที่ 95%)
                if ($latestLog->water_level_percent >= $station->max_water_level_percent) {
                    $alertStatus = 'danger';
                    $alertReasons[] = "ระดับน้ำใสเต็มถัง ({$latestLog->water_level_percent}%)";
                } elseif ($latestLog->water_level_percent <= $station->min_water_level_percent) {
                    $alertStatus = ($alertStatus === 'danger') ? 'danger' : 'warning';
                    $alertReasons[] = "ระดับน้ำใสต่ำกว่าเกณฑ์ ({$latestLog->water_level_percent}%)";
                }
            }

            $station->calculated_alert_status = $alertStatus;
            $station->alert_reasons = $alertReasons;

            return $station;
        });

        return view('tabwater.production.dashboard', compact('stations'));
    }

    /**
     * API: Remote Control Switch for Devices (Pumps/Valves)
     */
    public function toggleDevice(Request $request, $deviceId)
    {
        $request->validate([
            'action' => 'required|in:ON,OFF',
            'control_mode' => 'nullable|in:auto,manual',
            'reason' => 'nullable|string|max:255',
        ]);

        $device = TabwaterDevice::with('station.staffs')->findOrFail($deviceId);
        $user = Auth::user();
        $staffId = $user->staff_id ?? $user->id ?? null;

        // RBAC Validation: เช็กสิทธิ์ว่าช่างคนนี้ดูแลสถานีนี้หรือไม่ (ยกเว้น Header/Admin)
        if ($user && method_exists($user, 'hasRole') && $user->hasRole('tabwater_staff')) {
            $isAssigned = $device->station->staffs->contains('id', $staffId);
            if (!$isAssigned) {
                return response()->json([
                    'success' => false,
                    'message' => 'คุณไม่มีสิทธิ์สั่งการอุปกรณ์ในสถานีผลิตน้ำนี้'
                ], 403);
            }
        }

        DB::beginTransaction();
        try {
            $targetStatus = ($request->action === 'ON');
            $newMode = $request->control_mode ?? $device->control_mode;

            // อัปเดตสถานะอุปกรณ์
            $device->update([
                'status' => $targetStatus,
                'control_mode' => $newMode,
            ]);

            // บันทึก Audit Log
            TabwaterDeviceLog::create([
                'device_id' => $device->id,
                'staff_id' => $staffId,
                'action' => $request->action,
                'mode' => $newMode,
                'trigger_reason' => $request->reason ?? ($newMode === 'manual' ? 'ผู้ปฏิบัติงานสั่งการผ่าน Dashboard' : 'ระบบควบคุมอัตโนมัติ'),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "ส่งคำสั่ง {$request->action} ไปยัง {$device->name} เรียบร้อยแล้ว",
                'data' => [
                    'device_id' => $device->id,
                    'status' => $device->status,
                    'control_mode' => $device->control_mode,
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการส่งคำสั่ง: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store Morning Quality Log (Manual Stick / Litmus / IoT)
     */
    public function storeQualityLog(Request $request)
    {
        $request->validate([
            'station_id' => 'required|exists:tabwater_stations,id',
            'secchi_depth_cm' => 'nullable|numeric|min:0',
            'ph_value' => 'nullable|numeric|min:0|max:14',
            'water_temp_c' => 'nullable|numeric',
            'alum_valve_level' => 'nullable|in:low,medium,high',
            'lime_bucket_count' => 'nullable|integer|min:0',
            'ph_litmus_image' => 'nullable|image|max:5120', // 5MB
        ]);

        $station = TabwaterStation::findOrFail($request->station_id);
        $user = Auth::user();
        $staffId = $user->staff_id ?? $user->id ?? null;

        // คำนวณ NTU จากระยะไม้จุ่ม (cm) ถ้าไม่มีค่า NTU จาก IoT ส่งมา
        $secchiDepth = $request->secchi_depth_cm;
        $turbidityNtu = $request->turbidity_ntu;

        if ($secchiDepth && !$turbidityNtu) {
            $turbidityNtu = $station->calculateNTUFromDepth($secchiDepth);
        }

        // จัดการอัปโหลดรูปถ่ายกระดาษลิตมัส
        $imagePath = null;
        if ($request->hasFile('ph_litmus_image')) {
            $imagePath = $request->file('ph_litmus_image')->store('tabwater/litmus', 'public');
        }

        $log = TabwaterQualityLog::create([
            'org_id_fk' => $station->org_id_fk,
            'station_id' => $station->id,
            'data_source' => $request->data_source ?? 'manual',
            'staff_id' => $staffId,
            'secchi_depth_cm' => $secchiDepth,
            'turbidity_ntu' => $turbidityNtu,
            'tds_ppm' => $request->tds_ppm,
            'ph_value' => $request->ph_value,
            'ph_litmus_image' => $imagePath,
            'water_temp_c' => $request->water_temp_c,
            'water_level_percent' => $request->water_level_percent,
            'alum_valve_level' => $request->alum_valve_level,
            'lime_bucket_count' => $request->lime_bucket_count,
            'alum_used_kg' => $request->alum_used_kg ?? 0,
            'lime_used_kg' => $request->lime_used_kg ?? 0,
            'log_date' => now()->toDateString(),
            'log_time' => now()->toTimeString(),
            'remark' => $request->remark,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'บันทึกรายงานคุณภาพน้ำประจำวันเรียบร้อยแล้ว',
            'recommendation' => $log->chemical_recommendation,
        ]);
    }
}