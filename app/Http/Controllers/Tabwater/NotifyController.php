<?php

namespace App\Http\Controllers\Tabwater;

use App\Http\Controllers\Controller;
use App\Models\Admin\Organization;
use App\Models\Tabwater\TwNotifies;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NotifyController extends Controller
{
    public function index()
    {
        $orgInfos = Organization::getOrgName(Auth::user()->org_id_fk);
        return view('tabwater.notify.index', compact('orgInfos'));
    }

    public function store(Request $request)
    {
        // 1. Validate ข้อมูล
        $request->validate([
            'issue_type' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'photo_camera' => 'nullable|image',
            'photo_gallery' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('photo_camera')) {
            $file = $request->file('photo_camera');
        } elseif ($request->hasFile('photo_gallery')) {
            $file = $request->file('photo_gallery');
        }
        $imagePath = null;
        if ($file) {
            $imageName = time() . '.' . $file->extension();
            $file->move(public_path('uploads/notify'), $imageName);
            $imagePath = 'uploads/notify/' . $imageName;
        }

        TwNotifies::create([
            'user_id' => Auth::id(), // **สำคัญ: ผูก user ID ที่นี่**
            'issue_type' => $request->issue_type,
            'description' => $request->description,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'photo_path' => $imagePath,
            'status' => 'pending', // สถานะเริ่มต้น
        ]);

        // $this->sendStaffNotification($request->issue_type, $request->latitude, $request->longitude);

        return back()->with('success', 'แจ้งเหตุเรียบร้อยแล้ว! พิกัด: ' . $request->latitude . ', ' . $request->longitude);
    }

    protected function sendStaffNotification($type, $lat, $long)
    {
        $lineNotifyToken = env('LINE_NOTIFY_STAFF_TOKEN');

        // 2. สร้างข้อความ (ตรวจสอบว่าลิงก์รับงานถูกต้องแล้ว)
        $message = "🚨 งานใหม่เข้า (Pending) 🚨\n";
        $message .= "ประเภท: " . $type . "\n";
        $message .= "พิกัด: Lat {$lat}, Long {$long}\n";
        // ***ควรเปลี่ยน URL เป็นลิงก์สำหรับ Staff รับงานจริง***
        $message .= "ลิงก์รับงาน: " . route('staff.job.accept.page', ['lat' => $lat, 'long' => $long]);

        // 3. ส่ง HTTP POST Request ไปยัง LINE Notify API
        if ($lineNotifyToken) {
            $response = Http::asForm() // กำหนดให้ส่งข้อมูลในรูปแบบ application/x-www-form-urlencoded
                ->withHeaders([
                    'Authorization' => "Bearer {$lineNotifyToken}", // แนบ Token ใน Header
                ])
                ->post('https://notify-api.line.me/api/notify', [
                    'message' => $message, // ข้อความที่จะส่ง
                ]);

            // ตรวจสอบผลลัพธ์ (ไม่บังคับ แต่แนะนำ)
            if ($response->successful()) {
                Log::info('LINE Notify sent successfully.');
            } else {
                Log::error('LINE Notify failed: ' . $response->body());
            }
        } else {
            Log::warning('LINE_NOTIFY_STAFF_TOKEN is not set.');
        }
    }
}
