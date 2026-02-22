<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use App\Events\KioskImageCaptured;
use App\Events\KioskCommandSent;

class KioskController extends Controller
{

    // 1. เมื่อเซนเซอร์เจอวัตถุ

    public function index(){
        return "Kiosk Controller";
    }
    public function objectDetected(Request $request)
    {
        $kioskId = $request->query('kiosk', 'SLAVE_01');

        // สั่งถ่ายรูป (เก็บไว้ใน Cache)
        Cache::put("cmd_{$kioskId}", "TAKE_PHOTO", 60);

        // ส่ง Log ไปหน้าเว็บ
        broadcast(new KioskCommandSent($kioskId, 'SENSOR_TRIGGERED'));

        return response("OK");
    }

    // 2. เมื่อกล้องมาถามหาคำสั่ง
    public function checkCommand(Request $request)
    {
        $kioskId = $request->query('kiosk', 'SLAVE_01');

        // ดึงคำสั่งออกมา (ถ้ามีจะเอา TAKE_PHOTO ไป ถ้าไม่มีจะเอา IDLE ไป)
        $command = Cache::get("cmd_{$kioskId}", "IDLE");

        return response($command);
    }

    // 3. รับรูปจากกล้อง
    public function upload(Request $request)
    {
        $kioskId = $request->header('x-kiosk-id', 'SLAVE_01');
        $imgData = $request->getContent();

        if ($imgData) {
            $fileName = "kiosk_captures/{$kioskId}_" . time() . ".jpg";
            Storage::disk('public')->put($fileName, $imgData);
            $imageUrl = asset('storage/' . $fileName);

            // ล้างคำสั่งถ่ายรูปทิ้ง (เพราะถ่ายเสร็จแล้ว)
            Cache::forget("cmd_{$kioskId}");

            broadcast(new KioskImageCaptured($kioskId, $imageUrl));
        }
        return response("SUCCESS");
    }

    // 4. เมื่อ AI วิเคราะห์เสร็จ (Resume ระบบ)
    public function setReady(Request $request)
    {
        $kioskId = $request->query('kiosk', 'SLAVE_01');
        Cache::put("cmd_{$kioskId}", "RESUME_SENSOR", 60);
        return response("OK");
    }

    public function dropObject(Request $request)
    {
        $kioskId = $request->kiosk;
        $label = $request->label;

        // 1. บันทึกคำสั่งเพื่อให้ ESP8266 มาอ่านไปทำงาน
        Cache::put("command_$kioskId", "ROTATE_SERVO", 30);

        // 2. บันทึกจำนวนขวดลงฐานข้อมูลจริง (Optional)
        // $kiosk = Kiosk::find($kioskId);
        // $kiosk->increment('total_bottles');

        return response()->json(['status' => 'success']);
    }

    // สั่งให้ ESP หยุดทำงาน
    public function sleepMode(Request $request)
    {
        $kioskId = $request->query('kiosk');
        Cache::put("kiosk_command_$kioskId", "SLEEP", 3600); // สั่ง Sleep
        return response()->json(['status' => 'kiosk_sleeping']);
    }

    // บันทึกข้อมูลลง Database จริง
    public function saveTransaction(Request $request)
    {
        // สมมติว่าส่ง JSON: { "kiosk_id": "01", "items": {...}, "total_price": 50 }
        $data = $request->all();

        // บันทึกข้อมูลลงตาราง Transactions (คุณต้องสร้าง Model/Migration นี้ไว้)
        // Transaction::create($data);

        // เมื่อบันทึกเสร็จ สั่งให้เครื่องตื่นรอคนใหม่ (Wake up)
        Cache::put("kiosk_command_" . $data['kiosk_id'], "IDLE", 60);

        return response()->json(['status' => 'success', 'message' => 'Data recorded!']);
    }
}
