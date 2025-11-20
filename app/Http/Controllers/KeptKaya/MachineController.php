<?php

namespace App\Http\Controllers\KeptKaya;

use App\Http\Controllers\Controller;
use App\Models\KeptKaya\Machine;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class MachineController extends Controller
{

   public function index()
    {
        $machines = Machine::orderBy('last_heartbeat_at', 'desc')->get();
        return view('superadmin.machines.index', compact('machines'));
    }

    /**
     * 2. Show the form for creating a new resource (Create).
     */
    public function create()
    {
        // อาจจะต้องส่งข้อมูลอื่นๆ ที่จำเป็นไปให้ฟอร์ม เช่น รายชื่อองค์กร (Organization)
        return view('superadmin.machines.create');
    }

    /**
     * 3. Store a newly created resource in storage (Store).
     */
    public function store(Request $request)
    {
        // กำหนดกฎการตรวจสอบข้อมูล
        $validatedData = $request->validate([
            'machine_id'    => 'required|string|max:100|unique:machines',
            'org_id_fk'     => 'required|integer|exists:organizations,id', // ตรวจสอบว่ามี org นี้จริงในตาราง organizations
            'status'        => 'required',
            'machine_ready' => 'boolean',
            'pending_command' => 'nullable|string|max:255',
            // current_user_active_id, has_new_object, last_heartbeat_at มักจะถูกกำหนดโดยระบบ/เครื่องจักรเอง
        ]);

        // กำหนดค่าเริ่มต้นสำหรับ Boolean fields ที่อาจไม่ได้ส่งมาในฟอร์ม
        $validatedData['machine_ready'] = $request->has('machine_ready');
        $validatedData['has_new_object'] = $request->has('has_new_object');
        
        // สร้าง Machine ใหม่
        $machine = Machine::create($validatedData);

        return redirect()->route('superadmin.machines.index')
                         ->with('success', 'Machine ' . $machine->machine_id . ' created successfully.');
    }

    /**
     * 4. Display the specified resource (Show).
     */
    public function show(Machine $machine)
    {
        return view('superadmin.machines.show', compact('machine'));
    }

    /**
     * 5. Show the form for editing the specified resource (Edit).
     */
    public function edit(Machine $machine)
    {
        return view('superadmin.machines.edit', compact('machine'));
    }

    /**
     * 6. Update the specified resource in storage (Update).
     */
    public function update(Request $request, Machine $machine)
    {
        // กำหนดกฎการตรวจสอบข้อมูล
        $validatedData = $request->validate([
            // unique:machines,machine_id,'.$machine->id หมายถึงตรวจสอบความไม่ซ้ำกัน ยกเว้นตัวมันเอง
            'machine_id' => 'required|string|max:100|unique:machines,machine_id,' . $machine->id,
            'org_id_fk' => 'required|integer|exists:organizations,id',
            'status' => 'required',
            'machine_ready' => 'integer',
            'has_new_object' => 'integer',
            'pending_command' => 'nullable|string|max:255',
        ]);

        // กำหนดค่าสำหรับ Boolean fields ที่อาจไม่ได้ส่งมาในฟอร์ม (ถ้าไม่ส่งมาแปลว่า unchecked)
        $validatedData['machine_ready'] = $request->has('machine_ready');
        $validatedData['has_new_object'] = $request->has('has_new_object');

        // อัปเดต Machine
        $machine->update($validatedData);

        return redirect()->route('superadmin.machines.show', $machine)
                         ->with('success', 'Machine ' . $machine->machine_id . ' updated successfully.');
    }

    /**
     * 7. Remove the specified resource from storage (Destroy).
     */
    public function destroy(Machine $machine)
    {
        $machine_id = $machine->machine_id;
        $machine->delete();

        return redirect()->route('superadmin.machines.index')
                         ->with('success', 'Machine ' . $machine_id . ' deleted successfully.');
    }
    
    // -----------------------------------------------------------------------------------
    // ฟังก์ชันเพิ่มเติมสำหรับการใช้งาน API (เช่น Heartbeat)
    // -----------------------------------------------------------------------------------
    
    /**
     * ฟังก์ชันรับ Heartbeat จากเครื่องจักร
     */
    public function heartbeat(Request $request, $machine_id)
    {
        $machine = Machine::where('machine_id', $machine_id)->first();

        if (!$machine) {
            return response()->json(['message' => 'Machine not found'], 404);
        }

        // อัปเดตสถานะและเวลา Heartbeat
        $machine->update([
            'last_heartbeat_at' => Carbon::now(),
            'status' => 'online', // หรือตรรกะอื่น ๆ ตามข้อมูลที่ส่งมา
            'machine_ready' => $request->get('is_ready', true), // สมมติว่าเครื่องส่งสถานะความพร้อมมาด้วย
            // อัปเดต fields อื่น ๆ ตามข้อมูล Heartbeat
        ]);

        return response()->json(['message' => 'Heartbeat received', 'status' => $machine->status]);
    }
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
        //1.esp กด set แล้วส่ง message มาเพื่อ update ว่า machine ready
        Machine::where('machine_id', $request->machine_id)->update([
            'machine_ready' => $request->machine_ready
        ]);
        return response()->json(['status' => 1]);

        // $data = $request->json()->all();
        // // 1. ตรวจสอบข้อมูลที่รับเข้ามา
        // if(!isset($data['machine_id']) || !isset($data['machine_ready'])){
        //     return response()->json([
        //             'status' => 'empty_data',
        //             'start_buy' => 0,
        //             'received_data' => $data 
        //         ], 404);
        // }

        // // 2. ค้นหาเครื่อง
        // $machine = Machine::where('machine_id', $data['machine_id'])->first();

        // if ($machine) {
        //     // 3. อัปเดตคอลัมน์ machine_ready
        //     $machine->machine_ready = (int) $data['machine_ready'];
        //     // อาจจะอัปเดต timestamp ล่าสุดด้วย
        //     $machine->last_heartbeat_at = now();
        //     $machine->status = 'ready';
        //     $machine->save();

        //     return response()->json([
        //             'status' => 'ready',
        //             'start_buy' =>  1,
        //             'received_data' => $data 
        //     ], 200);
        // }

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

        return response()->json(['status' => 'error', 'message' => 'Machine not found'], 4014);
    }
}
