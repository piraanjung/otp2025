<?php

namespace App\Http\Controllers\KeptKaya;

use App\Http\Controllers\Controller;
use App\Models\KeptKaya\Machine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MachineController extends Controller
{
    /**
     * [Route 1: POST /api/device/notify]
     * รับการแจ้งเตือนจาก ESP8266 เมื่อตื่นจากการกดปุ่ม (Deep Sleep Wakeup)
     */
    public function handleEspWakeup(Request $request)
    {
        $request->validate([
            'machine_id' => 'required|string',
            'status' => 'required|in:wakeup,object_detected', // ESP ส่งสถานะมา
        ]);

        // อัปเดตสถานะในฐานข้อมูล: ตั้งค่า has_new_object เป็น 1
        // เพื่อให้ Frontend ตรวจพบในการ Polling ถัดไป
        Machine::updateOrCreate(
            ['machine_id' => $request->machine_id,  'has_new_object' => 0],
            [
                'has_new_object' => $request->status == 'wakeup' ? 0 : 1,
                'machine_ready' => $request->status == 'wakeup' ? 1 : 0,
                'current_user_active_id' => $request->current_user_active_id,
                'pending_command' => null
            ]
        );

        return response()->json(['message' => 'Notification received and status updated.'], 200);
    }

    /**
     * [Route 2: GET /api/device/status]
     * Frontend ใช้ Polling เพื่อดึงสถานะของเครื่อง
     */
    public function getMachineStatus(Request $request)
    {
        $request->validate(['machine_id' => 'required|string']);

        // ดึงสถานะปัจจุบันจากฐานข้อมูล
        $machine = Machine::where('machine_id', $request->machine_id)->first();

        if (!$machine) {
            return response()->json(['error' => 'Machine not found'], 404);
        }

        // **Mock Data (แทนการดึงจาก DB):**
        $hasNewObject = (bool)rand(0, 1); // Mock: 10% chance to be 1
        if ($hasNewObject) {
            // ในทางปฏิบัติจะดึงจาก DB
            $status = ['has_new_object' => 1];
        } else {
            $status = ['has_new_object' => 0];
        }


        return response()->json($status);
    }

    /**
     * [Route 3: POST /api/device/command] <--- ตรงกับโค้ดที่เลือกไว้
     * Frontend (AI Logic) ส่งคำสั่ง ACCEPT/REJECT มาเก็บไว้
     */
    public function saveAiCommand(Request $request)
    {
        $request->validate([
            'machine_id' => 'required|string',
            'command' => 'required|in:ACCEPT,REJECT',
            'code' => 'required|in:0,1',
        ]);

        // บันทึกคำสั่งที่ AI ตัดสินใจแล้วลงในฐานข้อมูล
        Machine::where('machine_id', $request->machine_id)->update([
            'pending_command' => $request->code, // 0=ACCEPT, 1=REJECT
            'has_new_object' => 2, // อาจเปลี่ยนสถานะเพื่อบอกว่า 'กำลังรอ ESP มารับคำสั่ง'
        ]);

        return response()->json(['message' => 'Command saved successfully, waiting for ESP pickup.'], 200);
    }

    /**
     * [Route 4: GET /api/device/get_command]
     * ESP8266 ตรวจสอบและดึงคำสั่งสุดท้ายไปทำงาน
     */
    public function getEspCommand(Request $request)
    {
        $request->validate(['machine_id' => 'required|string']);

        // 1. ดึงคำสั่งที่รออยู่
        $machine = Machine::where('machine_id', $request->machine_id)->first();
        $command = $machine->pending_command ?? '9'; // 9 = NO_COMMAND

        // **Mock Data (แทนการดึงจาก DB):**
        // สมมติว่ามีคำสั่ง 0 (ACCEPT) รออยู่
        $command = '0';

        // 2. รีเซ็ตสถานะใน DB
        Machine::where('machine_id', $request->machine_id)->update([
            'pending_command' => null, // ล้างคำสั่ง
            'has_new_object' => 0,    // รีเซ็ตเป็นสถานะเริ่มต้น
        ]);

        // ESP คาดหวังการตอบกลับที่เรียบง่าย
        return response()->json(['command_code' => $command]);
    }

    public function bindMachineToPendingSession(Request $request)
    {
        $request->validate(['machine_id' => 'required|string|max:50']);

        $machineId = $request->machine_id;

        $machine = Machine::where('machine_id', $machineId)->get();
        if (collect($machine)->isEmpty()) {
            return response()->json([
                'message' => 'ไม่พบ Machine Id ' .  $machineId,
                'machine_id' => $machineId
            ], 204);
        }

        // อัปเดตสถานะเป็น PENDING และบันทึก ID ไว้ใน Session (สำคัญ!)
        // esp บังคับว่าต้องมีข้อมูลอยู่แล้วเท่านั้นไม่มีการ create ใหม่
        Machine::where('machine_id', $machineId)->update([
            'status' => 'pending_login', // ค่าเริ่มต้นถ้าสร้างใหม่
            'has_new_object' => 0,
            'current_user_active_id' => null,
           
        ]);



        //  ส่ง Machine ID กลับไปให้ Client Redirect
        return response()->json([
            'message' => 'Machine ID bound to pending session.',
            'machine_id' => $machineId
        ], 200);
    }

    public function updateMachineStatus(Request $request)
    {
        // 1. ตรวจสอบข้อมูลที่รับเข้ามา
        $validated = $request->validate([
            'machine_id' => 'required|string|exists:machines,machine_id', // ตรวจสอบว่ามี ID นี้ในตาราง
            'machine_ready' => 'required|in:1', // ต้องเป็น 1 เท่านั้น
        ]);

        // 2. ค้นหาเครื่อง
        $machine = Machine::where('machine_id', $validated['machine_id'])->first();

        if ($machine) {
            // 3. อัปเดตคอลัมน์ machine_ready
            $machine->machine_ready = (int) $validated['machine_ready'];
            // อาจจะอัปเดต timestamp ล่าสุดด้วย
            $machine->last_heartbeat_at = now();
            $machine->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Machine status updated successfully',
                'machine_id' => $machine->machine_id
            ], 200);
        }

        // กรณีที่ไม่ควรเกิดขึ้นหาก validate ผ่าน
        return response()->json(['status' => 'error', 'message' => 'Machine not found'], 404);
    }

    public function getSensorStatus()
    {
        // อ่านค่า has_new_object จาก Cache
        $machine = Machine::where('current_user_active_id', Auth::id())->get()->first();
        $status = $machine->has_new_object ?? 0;
        return response()->json(['has_new_object' => (int)$status, 'machine_ready' => $machine->machine_ready]);
    }

    public function controlDevice(Request $request)
    {
        // ...
        if ($request->has('start_buy')) {
            $value = $request->start_buy;
            $machineId = $request->esp_id;
            // อัปเดตตารางที่ ESP8266 ใช้ดึงค่า (เช่น machines table)
            Machine::where('machine_id', $machineId)->update([
                'start_buy' => $value,
                'updated_at' => now()
            ]);
            return response()->json([
                'status' => 'success',
                'message' => "start_buy updated to {$value} for {$machineId}",
                'start_buy' => (int) $value
            ]);
        }

        // 3. จัดการคำสั่งอื่น ๆ เช่น 'reject'
        if ($request->has('reject')) {
            // ... (จัดการ reject) ...
        }

        // ...
    }

    public function getControlCommand($machine_id)
    {
        $machine = Machine::where('machine_id', $machine_id)
            ->select('start_buy', 'machine_ready', 'pending_command', 'buycomplete') // ดึงเฉพาะคอลัมน์ที่จำเป็น
            ->first();

        if ($machine) {
            // ส่งเฉพาะค่าควบคุมที่ ESP ต้องการ
            return response()->json([
                'machine_id' => $machine_id,
                'start_buy' => (int) $machine->start_buy,
                'pending_command' => (int) $machine->pending_command,
                'machine_ready' => (int) $machine->machine_ready,
                'buycomplete' => (int)$machine->buycomplete
            ]);
        }

        return response()->json(['status' => 'error', 'message' => 'Machine not found'], 404);
    }
}
