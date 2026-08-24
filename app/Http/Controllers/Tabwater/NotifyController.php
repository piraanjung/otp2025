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
        // 1. Validate ข้อมูล (ปรับให้รับ photo_base64 หรือ photo_gallery/photo_camera)
        $request->validate([
            'issue_type'   => 'required',
            'latitude'     => 'required',
            'longitude'    => 'required',
            'photo_base64' => 'nullable|string', // สำหรับรูปจาก Canvas/กล้องถ่ายสด
            'photo_camera' => 'nullable|image',  // เผื่อมี input ไฟล์ปกติ
            'photo_gallery' => 'nullable|image|max:5000',
        ]);

        $imagePath = null;

        // 2. จัดการบันทึกรูปภาพ
        // เคส A: ส่งมาจาก Canvas (Base64) - วิธีหลักที่เราทำกันไว้
        if ($request->filled('photo_base64')) {
            $base64Image = $request->photo_base64;

            // ตัด Header เช่น "data:image/jpeg;base64," ออก
            if (preg_match('/^data:image\/(\w+);base64,/', $base64Image, $type)) {
                $base64Image = substr($base64Image, strpos($base64Image, ',') + 1);
                $type = strtolower($type[1]); // jpg, png, etc.
            } else {
                $type = 'jpg';
            }

            $base64Image = str_replace(' ', '+', $base64Image);
            $imageData = base64_decode($base64Image);

            if ($imageData !== false) {
                $imageName = time() . '_' . uniqid() . '.' . $type;

                // สร้างโฟลเดอร์ uploads/notify หากยังไม่มี
                $destinationPath = public_path('notify');
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0755, true);
                }

                // บันทึกไฟล์ลง public/uploads/notify
                file_put_contents($destinationPath . '/' . $imageName, $imageData);
                $imagePath = 'notify/' . $imageName;
            }
        }
        // เคส B: สำรองกรณีส่งไฟล์เป็น File Upload ปกติ
        else {
            $file = $request->file('photo_camera') ?? $request->file('photo_gallery');
            if ($file) {
                $imageName = time() . '_' . uniqid() . '.' . $file->extension();
                $file->move(public_path('notify'), $imageName);
                $imagePath = 'notify/' . $imageName;
            }
        }

        // 3. บันทึกลง Database ผ่าน TwNotifies Model
        $notify = TwNotifies::create([
            'user_id'     => Auth::id(),
            'issue_type'  => $request->issue_type,
            'description' => $request->description,
            'latitude'    => $request->latitude,
            'longitude'   => $request->longitude,
            'photo_path'  => $imagePath,
            'status'      => 'pending',
        ]);

        // ส่ง Flex Message แจ้งเตือนไปยังกลุ่ม Staff
      return  $this->sendStaffNotification($notify);
        return back()->with('success', 'แจ้งเหตุเรียบร้อยแล้ว! พิกัด: ' . $request->latitude . ', ' . $request->longitude);
    }

   protected function sendStaffNotification(TwNotifies $notify)
{
    $channelToken = config('services.line_staff.channel_token');
    $targetId     = config('services.line_staff.staff_group_id'); // Group ID หรือ User ID ของ Staff

    if (!$channelToken || !$targetId) {
        Log::warning('LINE Messaging API Token or Group ID is not configured.');
        return;
    }

    // ดึงข้อมูลจาก Model $notify
    $type      = $notify->issue_type;
    $lat       = $notify->latitude;
    $long      = $notify->longitude;
    $photoPath = $notify->photo_path;

    // แปลงชื่อประเภทปัญหาเป็นภาษาไทย
    $typeNames = [
        'pipe_burst'   => 'ท่อแตก / ท่อรั่ว',
        'no_water'     => 'น้ำไม่ไหล',
        'low_pressure' => 'น้ำไหลอ่อน',
        'dirty_water'  => 'น้ำขุ่น / มีกลิ่น',
        'other'        => 'อื่นๆ',
    ];
    $typeName = $typeNames[$type] ?? $type;

    // URL รูปภาพเต็ม และ URL ลิงก์ต่างๆ
    $imageUrl      = $photoPath ? asset($photoPath) : 'https://via.placeholder.com/600x400?text=No+Image';
    $googleMapsUrl = "https://www.google.com/maps?q={$lat},{$long}";
    
    // ✅ เรียกใช้ Route Name ที่ถูกต้อง และส่ง $notify->id เข้าไป
    $acceptJobUrl  = route('staff.job.accept', ['notify' => $notify->id]);

    // โครงสร้าง Flex Message
    $flexData = [
        'type' => 'bubble',
        'hero' => [
            'type' => 'image',
            'url' => $imageUrl,
            'size' => 'full',
            'aspectRatio' => '20:13',
            'aspectMode' => 'cover',
        ],
        'body' => [
            'type' => 'box',
            'layout' => 'vertical',
            'contents' => [
                [
                    'type' => 'text',
                    'text' => "🚨 แจ้งเหตุงานประปา (#{$notify->id})",
                    'weight' => 'bold',
                    'size' => 'lg',
                    'color' => '#1DB446',
                ],
                [
                    'type' => 'box',
                    'layout' => 'vertical',
                    'margin' => 'lg',
                    'spacing' => 'sm',
                    'contents' => [
                        [
                            'type' => 'box',
                            'layout' => 'baseline',
                            'spacing' => 'sm',
                            'contents' => [
                                ['type' => 'text', 'text' => 'ประเภท:', 'color' => '#aaaaaa', 'size' => 'sm', 'flex' => 2],
                                ['type' => 'text', 'text' => $typeName, 'wrap' => true, 'color' => '#666666', 'size' => 'sm', 'flex' => 5, 'weight' => 'bold'],
                            ],
                        ],
                        [
                            'type' => 'box',
                            'layout' => 'baseline',
                            'spacing' => 'sm',
                            'contents' => [
                                ['type' => 'text', 'text' => 'พิกัด:', 'color' => '#aaaaaa', 'size' => 'sm', 'flex' => 2],
                                ['type' => 'text', 'text' => "{$lat}, {$long}", 'wrap' => true, 'color' => '#666666', 'size' => 'sm', 'flex' => 5],
                            ],
                        ],
                    ],
                ],
            ],
        ],
        'footer' => [
            'type' => 'box',
            'layout' => 'vertical',
            'spacing' => 'sm',
            'contents' => [
                [
                    'type' => 'button',
                    'style' => 'primary',
                    'height' => 'sm',
                    'action' => [
                        'type' => 'uri',
                        'label' => '🛠️ กดรับงานนี้',
                        'uri' => $acceptJobUrl, // ✅ ลิงก์ตรงกับ Route `staff.job.accept`
                    ],
                    'color' => '#0D6EFD',
                ],
                [
                    'type' => 'button',
                    'style' => 'secondary',
                    'height' => 'sm',
                    'action' => [
                        'type' => 'uri',
                        'label' => '🗺️ เปิดแผนที่นำทาง',
                        'uri' => $googleMapsUrl,
                    ],
                ],
            ],
        ],
    ];

    // ส่ง Push Message ไปยัง LINE
  $response=  Http::withHeaders([
        'Authorization' => "Bearer {$channelToken}",
        'Content-Type'  => 'application/json',
    ])->post('https://api.line.me/v2/bot/message/push', [
        'to'       => $targetId,
        'notificationDisabled' => false, // 👈 บังคับให้ส่งเสียงและเด้ง Banner แจ้งเตือนแน่นอน
        'messages' => [
            [
                'type'     => 'flex',
                'altText'  => "🚨 แจ้งเหตุงานประปาใหม่ (#{$notify->id})",
                'contents' => $flexData,
            ]
        ],
    ]);

    if ($response->successful()) {
    // บันทึก Log เมื่อส่งสำเร็จ (จะเห็น message_id ใน log ไว้ตรวจสอบได้)
    $sentData = $response->json();
    Log::info("LINE Push Message sent successfully. Message ID: " . ($sentData['sentMessages'][0]['id'] ?? '-'));
} else {
    // กรณีที่ LINE ตอบกลับมาเป็น Error (เช่น Token หมดอายุ หรือ Group ID ผิด)
    Log::error("LINE Push Message failed status {$response->status()}: " . $response->body());
}

return $response;
}
}
