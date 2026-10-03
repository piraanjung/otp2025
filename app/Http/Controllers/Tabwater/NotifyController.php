<?php

namespace App\Http\Controllers\Tabwater;

use App\Http\Controllers\Controller;
use App\Models\Admin\Organization;
use App\Models\Admin\Staff;
use App\Models\IssueType;
use App\Models\SystemModule;
use App\Models\Tabwater\TwNotifies;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NotifyController extends Controller
{
    public function index(Request $request)
    {
        // 1. รับค่า org_id จาก QR Code (ถ้าไม่มี ให้ดึงจาก Session หรือใช้ Org แรกเป็น Default)
        $selectedOrgId = $request->get('org_id', session('selected_org_id', 1));

        // 2. บันทึก org_id ไว้ใน Session
        session(['selected_org_id' => $selectedOrgId]);

        // 3. ดึงรายการองค์กรทั้งหมดมาแสดงใน Dropdown
        $organizations = Organization::select(
            'id',
            'org_type_id',
            'org_name',
            'org_zone_id_fk',
            'org_tambon_id_fk',
            'org_district_id_fk',
            'org_province_id_fk',
            'org_logo_img'
        )
            ->with('tambons', 'districts', 'provinces', 'orgType')
            ->get();


        $currentOrg =  collect($organizations)->filter(function ($v) use ($selectedOrgId) {
            if ($v->id == $selectedOrgId) {
                return $v;
            }
        })->first();
        // 4. ดึงข้อมูลองค์กรที่กำลังเลือกอยู่ปัจจุบัน
        // ดึงหมวดหมู่ทั้งหมดที่เปิดใช้งานเรียงตามลำดับ sort_order
        $categories = SystemModule::where('is_active', true)
            ->orderBy('sort_order', 'asc')
            ->get();

        return view('notify.index', compact('categories', 'organizations', 'currentOrg', 'selectedOrgId'));
    }

    public function create(Request $request)
    {

        // รับค่า system_type จาก parameter เช่น ?system_type=water (ถ้าไม่มีให้ default เป็น water)
        $systemType = $request->query('system_type', 'water');

        // ดึงรายการประเภทปัญหาที่เปิดใช้งานอยู่ตามหมวดหมู่
        $issueTypes = IssueType::where('system_type', $systemType)
            ->where('is_active', true)
            ->get();
        $systemTypeModule = SystemModule::where('system_type', $systemType)->get('title');

        return view('notify.create', compact('issueTypes', 'systemType', 'systemTypeModule'));
    }

    /**
     * บันทึกข้อมูลการแจ้งเหตุ
     */
   public function store(Request $request) 
{

    // 1. Validate ข้อมูลให้ตรงกับฟิลด์ที่ส่งมาจากฟอร์ม
    $request->validate([
        'reporter_name' => 'required|string|max:255',
        'reporter_phone' => 'required|string|max:20',
        'issue_type' => 'required|string',
        'issue_type' => 'required',
        'other_issue' => 'nullable|required_if:issue_type,0|string|max:255', // ปรับจาก custom_issue_type เป็น other_issue ตามฟอร์ม
        'latitude' => 'required',
        'longitude' => 'required',
        'photos' => 'nullable|array',
        'photos.*' => 'image|mimes:jpeg,png,jpg|max:5120',
    ]);
    DB::beginTransaction();
    try {
        // 2. จัดการอัปโหลดรูปภาพ (ถ้ามี)
        $photoPaths = [];
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $photoPaths[] = $photo->store('notify', 'direct_public');
            }
        }

        // 3. กำหนดค่า issue_type และจัดการกรณี "อื่นๆ" (ถ้า issue_type เป็น 0 หรืออื่นๆ)
        $issueTypeVal = $request->issue_type;
        // $customIssueText = null;

        if ($request->issue_type == '0' || $request->issue_type === 'other') {
            // บันทึกลงตาราง IssueType กลางถ้าจำเป็น
            $customIssueType = IssueType::firstOrCreate(
                [
                    'system_type' => $request->system_type,
                    'name' => $request->other_issue,
                ],
                [
                    'is_active' => 1,
                    'is_suggested' => 1,
                ]
            );

            $issueTypeVal = $customIssueType->id;

        }

        $orgId = session()->pull('selected_org_id');
        $moduleType = $request->system_type; 
        $userId = $request->user_id;


        // 4. บันทึกข้อมูลลงตาราง tw_notifies
        $notify = TwNotifies::create([
            'user_id' => $memberId ?? ($userId != 0 ? $userId : null),
            'org_id_fk' => $orgId ?? Auth::user()->org_id_fk,
            'reporter_name' => $request->reporter_name,
            'reporter_phone' => $request->reporter_phone,
            'issue_type_id' => $issueTypeVal,
            'description' => $request->description,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'photo_path' => json_encode($photoPaths), 
            'status' => 'pending',
        ]);

        DB::commit();
        // ส่งแจ้งเตือน (ถ้ามีเมธอดนี้)
        if (method_exists($this, 'sendHeadNotificationText')) {
            $this->sendHeadNotificationText($notify);
        }

        return response()->json([
            'success' => true,
            'message' => 'บันทึกข้อมูลการแจ้งเหตุเรียบร้อยแล้ว',
            'id' => $notify->id
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'success' => false,
            'message' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $e->getMessage()
        ], 500);
    }
}

public function sendHeadNotificationText()
{
    $notify = TwNotifies::find(3);
    
    // ดักตรวจสอบกรณีไม่พบข้อมูล
    if (!$notify) {
        return response()->json(['status' => 'error', 'message' => 'ไม่พบข้อมูลแจ้งเหตุ ID: 3'], 404);
    }

    $channelToken = config('services.line_staff.channel_token');

    $headStaff = Staff::where('org_id_fk', 1)
        ->where('status', 'active') // ปรับค่า status ตามระบบของคุณ
        ->whereHas('user', function ($query) {
            $query->role('Tabwater Header') // เช็ก Role ใน Model User
                  ->whereNotNull('line_id');
        })
        ->where('user_id', 1)
        ->with('user') // Eager load Relation user เพื่อลด Query
        ->first();

    // หากไม่พบข้อมูล
    if (!$headStaff || !$headStaff->user || empty($headStaff->user->line_id)) {
        Log::warning("ไม่พบหัวหน้างานประปา (Staff) ที่มี LINE ID สำหรับ Org ID: {1}");
        return;
    }

    // ดึง line_user_id ของหัวหน้าจาก Relationship user
    $headerGroupId = $headStaff->user->line_id;

    // 1. เตรียมเนื้อหาภายใน body (เริ่มจากข้อความรายละเอียดหลัก)
    $bodyContents = [
        [
            'type' => 'text',
            'text' => "รหัสแจ้งเหตุ: {$notify->id}\nเรื่อง: " . ($notify->issueType->name ?? '-'),
            'weight' => 'bold',
            'size' => 'sm',
            'color' => '#333333',
            'wrap' => true 
        ],
        [
            'type' => 'text',
            'text' => "ผู้แจ้ง: {$notify->reporter_name}\nเบอร์โทร: {$notify->reporter_phone}",
            'size' => 'sm',
            'color' => '#666666',
            'wrap' => true
        ]
    ];

    // 2. จัดการดึงรูปภาพจาก Database มาแสดง (สมมติว่าฟิลด์ photos เก็บเป็น JSON array เช่น ['notify/abc.jpg'])
    // ปรับเปลี่ยนวิธี decode ตามโครงสร้างจริงของฐานข้อมูลคุณ เช่น json_decode หรือถ้าเป็น array อยู่แล้วก็ใช้ได้เลย
    $photos = is_string($notify->photo_path) ? json_decode($notify->photo_path, true) : $notify->photo_path;

    if (!empty($photos) && is_array($photos)) {
        // คั่นเส้นแบ่งก่อนแสดงรูป
        $bodyContents[] = [
            'type' => 'separator',
            'margin' => 'md'
        ];

        $bodyContents[] = [
            'type' => 'text',
            'text' => '📸 รูปภาพแนบ:',
            'size' => 'xs',
            'weight' => 'bold',
            'color' => '#aaaaaa',
            'margin' => 'md'
        ];

        // วนลูปรูปภาพแต่ละรูป
        foreach ($photos as $photoPath) {
            // ใช้ asset() เพื่อแปลงเป็น Full URL (เช่น https://yourdomain.com/notify/xxx.jpg)
            // *หมายเหตุ: URL รูปภาพต้องเป็น HTTPS และเปิดให้คนภายนอกเข้าถึงได้
            $imageUrl = asset("uploads/".$photoPath);

            $bodyContents[] = [
                'type' => 'image',
                'url' => $imageUrl,
                'size' => 'full',       // ขนาด: xs, sm, md, lg, full
                'aspectRatio' => '4:3',  // สัดส่วนรูป: '1:1', '4:3', '16:9'
                'aspectMode' => 'cover',  // การแสดงผลภาพ: cover หรือ fit
                'margin' => 'md',
                'action' => [
                    'type' => 'uri',
                    'label' => 'ดูรูปขนาดเต็ม',
                    'uri' => $imageUrl // พอกดที่รูปจะเด้งเปิดดูรูปใหญ่
                ]
            ];
        }
    }

    // 3. ประกอบร่างโครงสร้าง Flex Message ทั้งหมด
    $flexPayload = [
        'type' => 'bubble',
        'header' => [
            'type' => 'box',
            'layout' => 'vertical',
            'contents' => [
                [
                    'type' => 'text',
                    'text' => 'แจ้งเตือนงานใหม่ (หัวหน้างานประปา)',
                    'weight' => 'bold',
                    'color' => '#ffffff',
                    'size' => 'sm'
                ]
            ],
            'backgroundColor' => '#d9534f'
        ],
        'body' => [
            'type' => 'box',
            'layout' => 'vertical',
            'spacing' => 'md',
            'contents' => $bodyContents // นำอาเรย์ข้อมูลทั้งหมดที่เตรียมไว้มายัดใส่ตรงนี้
        ],
        'footer' => [
            'type' => 'box',
            'layout' => 'vertical',
            'contents' => [
                [
                    'type' => 'button',
                    'action' => [
                        'type' => 'message',
                        'label' => 'กดเพื่อแจ้งข้อมูลให้ทุกคน',
                        'text' => 'งานเข้า '.$notify->id
                    ],
                    'style' => 'primary'
                ]
            ]
        ]
    ];

    // 4. ส่ง Request ไปยัง LINE Messaging API (Push Message)
    $response = Http::withHeaders([
        'Authorization' => 'Bearer ' . $channelToken,
        'Content-Type'  => 'application/json',
    ])->post('https://api.line.me/v2/bot/message/push', [
        'to'       => $headerGroupId, 
        'messages' => [
            [
                'type'     => 'flex',
                'altText'  => 'แจ้งเตือนงานใหม่ #3',
                'contents' => $flexPayload
            ]
        ]
    ]);

    return response()->json([
        'status' => $response->successful() ? 'success' : 'error',
        'response' => $response->json()
    ]);
}

//    protected function sendHeadNotificationText(TwNotifies $notify)
// {
//     $channelToken = config('services.line_staff.channel_token');

//     // ค้นหา Staff ที่:
//     // 1. สังกัด org_id_fk เดียวกัน
//     // 2. สถานะ Staff ปกติ (เช่น status == 'active' หรือ != 'inactive')
//     // 3. มี User relationship ที่มี Role 'head_tap_water' และมี line_user_id
//     $headStaff = Staff::where('org_id_fk', 1)
//         ->where('status', 'active') // ปรับค่า status ตามระบบของคุณ
//         ->whereHas('user', function ($query) {
//             $query->role('Tabwater Header') // เช็ก Role ใน Model User
//                   ->whereNotNull('line_id');
//         })
//         // ->where('user_id', 1)
//         ->with('user') // Eager load Relation user เพื่อลด Query
//         ->first();

//     // หากไม่พบข้อมูล
//     if (!$headStaff || !$headStaff->user || empty($headStaff->user->line_id)) {
//         Log::warning("ไม่พบหัวหน้างานประปา (Staff) ที่มี LINE ID สำหรับ Org ID: {1}");
//         return;
//     }

//     // ดึง line_user_id ของหัวหน้าจาก Relationship user
//     $headUserId = $headStaff->user->line_id;

//     // 2. ข้อความสั้นเพื่อให้หัวหน้าก๊อปปี้ง่ายที่สุด
//     $triggerText = "งานเข้า 3";
    
//     $messageText = "🚨 **มีแจ้งเหตุใหม่ (#{1})**\n"
//                  . "องค์กร: " . ($เทศบาลตำบลห้องแซง ?? '-') . "\n"
//                  . "ประเภท: water\n"
//                  . "---------------------------\n"
//                  . "📌 **กรุณาก๊อปปี้ข้อความด้านล่างนี้ วางลงในกลุ่มงาน:**\n\n"
//                  . $triggerText;

//     // 3. ส่ง 1-on-1 หาหัวหน้า
//     Http::withHeaders([
//         'Authorization' => "Bearer {$channelToken}",
//         'Content-Type'  => 'application/json',
//     ])->post('https://api.line.me/v2/bot/message/push', [
//         'to'       => $headUserId,
//         'messages' => [['type' => 'text', 'text' => $messageText]],
//     ]);
// }

    /**
     * Summary of success
     * @param mixed $id
     * @return \Illuminate\Contracts\View\View
     */
    public function success($id)
{
    $notify = TwNotifies::findOrFail($id);

    return view('notify.success', compact('notify'));
}
    public function checkMember(Request $request)
    {
        $phone = $request->query('phone');

        // สมมติค้นหาจาก Table Member หรือ User
        $member = User::where('phone', $phone)->first();

        if ($member) {
            return response()->json([
                'found' => true,
                'name' => $member->firstname . " " . $member->lastname,
                'user_id' => $member->id
            ]);
        }

        return response()->json([
            'found' => false
        ]);
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
        $response =  Http::withHeaders([
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
