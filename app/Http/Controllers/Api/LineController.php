<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admin\ManagesTenantConnection;
use App\Models\Admin\Organization;
use App\Models\Admin\Province;
use App\Models\Admin\Zone;
use App\Models\KeptKaya\KPAccounts;
use App\Models\KeptKaya\KpPurchaseTransaction;
use App\Models\KeptKaya\KpUserWastePreference;
use App\Models\SuperUser;
use App\Models\Tabwater\SequenceNumber;
use App\Models\Tabwater\TwNotifies;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Testing\Fluent\Concerns\Has;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class LineController extends Controller
{

    public function index(Request $request)
    {
        $provinces = Province::get(['id', 'province_name']);
        return view('lineliff.index', compact('provinces'));
    }
    public function find_line_id(Request $request)
    {
        $res = 0;
        $waste_pref_id = 0;
        //check
        $user = User::where('line_id', $request->userId)
            ->with('wastePreference')
            ->first();
        if (collect($user)->isEmpty() || collect($user->wastePreference)->isEmpty()) {
            $org_id     = 0;
            $user_id    = 0;
        } else {
            $org_id         = $user->org_id_fk;
            $res            = 1;
            $user_id        = $user->id;
            $waste_pref_id  = $user->wastePreference->id;
        }


        return response()->json([
            'res'           => $res,
            'user_id'       => $user_id,
            'org_id'        => $org_id,
            'waste_pref_id' => $waste_pref_id

        ]);
    }

    public function checkLineUser(Request $request)
    {

        $lineUserId = $request->input('line_user_id');

        // ค้นหาผู้ใช้งานในตาราง users ด้วย line_id

        $user = User::where('line_id', $lineUserId)->with('wastePreference')->get()->first();
        if(!$user){
            return response()->json(['status' => 'not_foundxx']);
        }
        
        if(collect($user->wastePreference)->isEmpty()){
            KpUserWastePreference::create([
                'user_id' => $user->id,
                'org_id_fk' => $user->org_id_fk,
                'is_waste_bank' => 1,
                'is_annual_collection' => 0,
                "address" => $user->address,
                "zone_id" => $user->zone_id,
                "subzone_id" => $user->subzone_id,
                "tambon_code" => $user->tambon_code,
                "district_code" => $user->district_code,
                "province_code" => $user->province_code,
            ]);
        }
        if (collect($user)->isNotEmpty()) {
            // ถ้าเจอ -> ดึงรายการองค์กรที่สังกัดทั้งหมดส่งกลับไป

            $organizationList = $this->getOrganizations($user->id);
            return response()->json([
                'status' => collect($organizationList)->isEmpty() ? 'not found' : 'found',
                'organization_list' => $organizationList
            ]);
            
        }

        // ถ้าไม่เจอ -> แจ้งให้หน้าบ้านเริ่มกรอกเบอร์โทรศัพท์ (Step 1)
        return response()->json(['status' => 'not_found']);
    }

    public function verifyUser(Request $request)
    {
        $phone = $request->input('phone');
        $imagePath = $request->input('line_user_image');
        $idCard = $request->input('id_card');
        $lineUserId = $request->input('line_user_id');

        // เคสที่ 1: หน้าบ้านส่งเฉพาะเบอร์โทรมาอย่างเดียว (Step 1)
        if ($phone && !$idCard) {
            $user = User::where('phone', $phone)->first();
            if ($user) {
                // เจอข้อมูลด้วยเบอร์โทร -> อัปเดตผูก LINE ID และส่งรายชื่อองค์กรกลับไป
                $user->update([
                    'line_id' => $lineUserId,
                    'image'   => $imagePath
                    ]);
                return response()->json([
                    'status' => 'found',
                    'organization_list' => $this->getOrganizations($user->id)
                ]);
            }
            // ไม่เจอด้วยเบอร์โทร -> สั่งให้หน้าบ้านแสดงกล่องกรอกเลขบัตรประชาชน (Step 2)
            return response()->json(['status' => 'ask_id_card']);
        }

        // เคสที่ 2: เบอร์โทรแรกไม่เจอ หน้าบ้านจึงส่งเลขบัตรประชาชนพ่วงกลับมาตรวจสอบซ้ำ (Step 2)
        if ($idCard) {
            // ค้นหาจากสิ่งที่เปลี่ยนแปลงไม่ได้คือเลขบัตรประชาชน
            $user = User::where('id_card', $idCard)->first();
            if ($user) {
                // เจอด้วยเลขบัตร -> แสดงว่าผู้ใช้เปลี่ยนเบอร์โทร!
                // ทำการอัปเดตทั้ง LINE ID และเบอร์โทรศัพท์ใหม่ที่เขาพิมพ์ในขั้นแรกเข้าไปแทนที่ข้อมูลเก่า
                $user->update([
                    'line_id' => $lineUserId,
                    'phone'   => $phone,
                    'image'   => $imagePath
                ]);
                return response()->json([
                    'status' => 'found_and_updated',
                    'organization_list' => $this->getOrganizations($user->id)
                ]);
            }
            // ไม่เจออะไรเลยทั้งเบอร์และบัตรประชาชน -> แจ้งให้หน้าบ้านพาไปลงทะเบียนใหม่ (Step 4)
            return response()->json(['status' => 'go_to_register']);
        }
    }

    public function setSessionOrg(Request $request)
    {
        $orgId = $request->input('org_id');

        // บันทึกองค์กรที่เลือกไว้ลงใน Session ของผู้ใช้งานเพื่อให้ระบบจำได้ใน Request ถัดไป
        session(['active_org_id' => $orgId]);

        return response()->json(['status' => 'success']);
    }

    // ฟังก์ชันภายในสำหรับดึงข้อมูลองค์กรผ่านตาราง Preferences ร่วมกับตาราง Organizations
    private function getOrganizations($userId)
{
    return KpUserWastePreference::where('user_id', $userId)
        ->join('organizations', 'kp_user_waste_preferences.org_id_fk', '=', 'organizations.id')
        ->join('organization_types',  'organization_types.id','=','organizations.org_type_id')
        ->select([

            'kp_user_waste_preferences.id as pref_id', // 👈 ดึง ID ของตาราง Preferences และตั้งชื่อ Alias ว่า pref_id
            'organizations.id',   
            'organization_types.name as org_type',   
            'kp_user_waste_preferences.user_id',
            'organizations.org_name'                  // ชื่อองค์กร
        ])
        ->get();
}

public function getOrgLists($org_type){
    $orgs = Organization::where('org_type_id', $org_type)
            ->with('provinces', 'districts', 'tambons', 'orgType')
            ->get(['id', 'org_type_id', 'org_name', 'org_tambon_id_fk', 'org_district_id_fk', 'org_province_id_fk']);

    return response()->json(['orgs' => $orgs]);
}

public function getZones($tambon_id)
    {
        $zones = Zone::where('tambon_id', $tambon_id)
            ->with('subzone')
            ->get(['id', 'tambon_id', 'zone_name', 'location']);
        return response()->json(['zones' => $zones]);
    }



    public function user_line_register(Request $request)
    {

        $user = User::create([
            'username'      => $request->org_id . $request->phoneNum,
            'password'      => Hash::make($request->phoneNum),
            'firstname'     => $request->firstname,
            'lastname'      => $request->lastname,
            'line_id'       => $request->line_user_id,
            'image'         => $request->line_user_image,
            'phone'         => $request->phoneNum,
            'age'           => 0,
            'height'        => 0,
            'weight'        => 0,
            'tambon_code'   => $request->tambon_id,
            'district_code' => $request->district_id,
            'province_code' => $request->province_id,
            'org_id_fk'     => $request->org_id,
            'zone_id'       => $request->zone_id,
            'address'       => $request->address,
            'subzone_id' => $request->subzone_id,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $user->assignRole('User');

        $userWastPref = KpUserWastePreference::create([
            'user_id' => $user->id,
            'is_annual_collection' => 0,
            'is_waste_bank' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        (new KPAccounts())->registerAccount($userWastPref->id);

        return response()->json([
            'res' => 1,
            'waste_pref_id' => $userWastPref->id

        ]);
    }

    public function user_qrcode()
    {
        return  view('lineliff.user_qrcode');
    }

    public function dashboard($user_waste_pref_id, $db_conn = "envsogo_main")
    {
        $userWastePref = KpUserWastePreference::with('user', 'purchaseTransactions')->where('id', $user_waste_pref_id)->get()->first();
        $qrcode = QrCode::size(300)->generate($user_waste_pref_id);
        return view('lineliff.dashboard', compact('userWastePref', 'qrcode'));
    }

    public function findUserByPhone(Request $request)
    {
        // ทำความสะอาดเบอร์ (ลบขีดออกถ้ามี)
        $user = User::where('phone', $request->phone)->get();

        if (!$user) {
            return response()->json(['user_info' => []], 404);
        }
        $user->line_user_id = $request->line_id;
        $user->line_id = $request->line_id;
        $user->save();
        $user_info_lists = [];
        foreach ($user as $u) {
            array_push($user_info_lists, [
                'user_id' => $u->id,
                'org_id' => $u->org_id_fk,
                'firstname' => $u->firstname,
                'lastname' => $u->lastname,
                'phone' => $u->phone,
            ]);
        }
        return response()->json(
            [
                'user_info' => $user_info_lists,
                'haved_line_id' =>  collect($user[0]->line_user_id)->isEmpty() ? 0 : 1
            ],
            200
        );
    }


    public function update_user_by_phone(Request $request)
    {

        // return $request;
        $user_org = (new Organization())->setConnection('envsogo_main')::find($request->org_id);


        $_user = (new User())->setConnection($user_org->org_database)->where('phone', $request->phoneNum)->get()->first();
        $res            = 0;
        $user_id        = 0;
        $waste_pref_id  = 0;

        $user_org   = Organization::find($request->org_id);

        $local_user = (new User())->setConnection($user_org->org_database)
            ->where('phone', $request->phoneNum)
            ->where('line_id', $request->line_user_id)->get()->first();

        if ($_user) {
            $userUpdate = SuperUser::find($_user->id);
            $userUpdate->province_code  = $request->province_id;
            $userUpdate->district_code  = $request->district_id;
            $userUpdate->tambon_code    =  $request->tambon_id;
            $userUpdate->org_id_fk      = $request->org_id;
            $userUpdate->line_id        = $request->line_user_id;
            $userUpdate->image          = $request->line_user_image;
            $userUpdate->save();

            $user_org = Organization::find($request->org_id);
            $local_user = (new User())->setConnection($user_org->org_database)->where('phone', $request->phoneNum)
                ->where('line_id', $request->line_user_id)->get()->first();

            $local_user_wastePreference = (new KpUserWastePreference())->setConnection($user_org->org_database)->where('user_id', $local_user->id)->get();
            if (collect($local_user_wastePreference)->isEmpty()) {
                $newUWastePref = (new KpUserWastePreference())->setConnection($user_org->org_database)->create([
                    'user_id' => $_user->id,
                    'is_annual_collection' => 0,
                    'is_waste_bank' => 1,
                ]);

                $waste_pref_id  = $newUWastePref->id;
            }


            $user_id = $_user->id;
            $res = 1;
        } else {
            //ถ้ายังไม่มีข้อมูลให้ ทำการ create

            //บันทึก new user ที่ envsogo_main
            $new_user               =  new SuperUser();
            $new_user->firstname    = $request->displayName;
            $new_user->line_id      = $request->line_user_id;
            $new_user->phone        = $request->phoneNum;
            $new_user->org_id_fk    = $request->org_id;
            $new_user->image        = $request->line_user_image;
            $new_user->created_at   = date("Y-m-d H:i:s");
            $new_user->updated_at   = date("Y-m-d H:i:s");
            $new_user->save();

            //บันทึก new user ที่ envsogo_ ตาม org_id ของ user
            $userCount = (new SequenceNumber())->setConnection($user_org->org_database)->where('id', 1)->get('user')->first();

            $local_user                 = (new User())->setConnection($user_org->org_database);
            $local_user->id             =  $userCount->user;
            $local_user->firstname      = $request->displayName;
            $local_user->line_id        = $request->line_user_id;
            $local_user->phone          = $request->phoneNum;
            $local_user->image          = $request->line_user_image;
            $local_user->org_id_fk      = $request->org_id;
            $local_user->tambon_code    = $request->tambon_id;
            $local_user->district_code  = $request->district_id;
            $local_user->province_code  = $request->province_id;
            $local_user->created_at     = date("Y-m-d H:i:s");
            $local_user->updated_at     = date("Y-m-d H:i:s");
            $local_user->save();

            $newUWastePref = (new KpUserWastePreference())->setConnection($user_org->org_database)->create([
                'user_id'               => $local_user->id,
                'is_annual_collection'  => 0,
                'is_waste_bank'         => 1,
                'created_at'            => date('Y-m-d H:i:s'),
                'updated_at'            => date('Y-m-d H:i:s'),
            ]);


            (new KPAccounts())->setConnection($user_org->org_database)->registerAccount($newUWastePref->id, $user_org->org_database);

            (new SequenceNumber())->setConnection($user_org->org_database)->where('id', 1)->update([
                'user' => $userCount->user + 1
            ]);
            $user_id        = $local_user->id;
            $res            = 1;
            $waste_pref_id  = $newUWastePref->id;
        }
        return response()->json([
            'res'           => $res,
            'user_id'       => $user_id,
            'org_id'        => $request->org_id,
            'waste_pref_id' => $waste_pref_id
        ]);
    }


 

    /**
     * Summary of replyWithUserQrCode
     * @param mixed $lineId
     * @param mixed $replyToken
     */
    private function replyWithUserQrCode($lineId, $replyToken)
    {
        // ค้นหา User จาก lineId ใน Database ของคุณ
        // สมมติว่าตาราง users มีคอลัมน์ line_user_id
        $user = User::where('line_id', $lineId)->first();

        if (!$user) {
            // ถ้าไม่พบ User ให้แจ้งเตือนให้เขาลงทะเบียนก่อน
            return $this->replyTextMessage($replyToken, "ขออภัยครับ ไม่พบข้อมูลสมาชิกของคุณในระบบ กรุณาลงทะเบียนก่อนใช้งานครับ");
        }

        $qrContent = "USER-" . $user->phone;
        // สร้าง URL QR Code โดยใช้ Google Chart API
        $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=500x500&data=" . urlencode($qrContent);

        $flexData = [
            "type" => "bubble",
            "size" => "mega",
            "header" => [
                "type" => "box",
                "layout" => "vertical",
                "contents" => [
                    ["type" => "text", "text" => "QR Code ของคุณ", "weight" => "bold", "color" => "#ee2385", "size" => "sm"],
                    ["type" => "text", "text" => "สมาชิกธนาคารขยะ", "weight" => "bold", "size" => "xl", "margin" => "md", "color" => "#ffffff"]
                ]
            ],
            "body" => [
                "type" => "box",
                "layout" => "vertical",
                "contents" => [
                    [
                        "type" => "text",
                        "text" => "แสดง QR Code นี้ให้เจ้าหน้าที่สแกนเพื่อสะสมแต้มและขายขยะ",
                        "size" => "xs",
                        "color" => "#aaaaaa",
                        "wrap" => true,
                        "align" => "center"
                    ],
                    [
                        "type" => "image",
                        "url" => $qrUrl,
                        "size" => "xl",
                        "margin" => "xl",
                        "aspectRatio" => "1:1"
                    ],
                    [
                        "type" => "text",
                        "text" => $qrContent, // โชว์ USER-ID
                        "weight" => "bold",
                        "size" => "lg",
                        "align" => "center",
                        "margin" => "xl",
                        "color" => "#111111"
                    ]
                ]
            ],
            "styles" => [
                "header" => ["backgroundColor" => "#111111"]
            ]
        ];

        $this->sendFlexMessage($replyToken, "QR Code สมาชิกของคุณ", $flexData);
    }

    /**
     * Summary of sendFlexMessage
     * @param mixed $replyToken
     * @param mixed $altText
     * @param mixed $flexData
     * @return void
     */
    private function sendFlexMessage($replyToken, $altText, $flexData)
    {
        $httpClient = new \GuzzleHttp\Client();
        $httpClient->post('https://api.line.me/v2/bot/message/reply', [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . config('services.line.channel_token'),
            ],
            'json' => [
                'replyToken' => $replyToken,
                'messages' => [
                    [
                        'type' => 'flex',
                        'altText' => $altText,
                        'contents' => $flexData
                    ]
                ]
            ]
        ]);
    }

    /**
     * Summary of replyWithLastReceipt
     * @param mixed $lineId
     * @param mixed $replyToken
     * @return \Illuminate\Http\Client\Response
     */
    public function replyWithLastReceipt($lineId, $replyToken)
    {

        $transaction = KpPurchaseTransaction::whereHas('userWastePreference.user', function ($q) use ($lineId) {
            $q->where('line_id', $lineId);
        })
            // ตรวจสอบชื่อ Relation 'details' ใน KpPurchaseTransaction ให้ดีว่าเชื่อมไปที่ Detail หรือยัง
            ->with(['details.item', 'userWastePreference.user'])
            ->latest()
            ->first();
        if (!$transaction) {
            return $this->replyText($replyToken, "บักแอโร่ยังไม่พบประวัติการขายขยะของคุณครับ" . $lineId);
        }

        $flexData = $this->buildFlexReceipt($transaction);

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('LINE_CHANNEL_ACCESS_TOKEN'),
        ])->post('https://api.line.me/v2/bot/message/reply', [
            'replyToken' => $replyToken,
            'messages' => [
                [
                    'type' => 'flex',
                    'altText' => 'ใบเสร็จล่าสุดจากธนาคารขยะรีไซเคิล',
                    'contents' => $flexData
                ]
            ]
        ]);

        if ($response->failed()) {
            Log::error("LINE Reply Error: " . $response->body());
        }
    }

    public function handle(Request $request) 
    { 
        $events = $request->input('events', []);
        
        foreach ($events as $event) { 
            // return $event['message'];
            // 1. ตรวจสอบว่าเป็นข้อความตัวอักษรที่ส่งเข้ามาในแชท (ไม่ว่าจะพิมพ์เองหรือกดปุ่ม)
            if ($event['type'] === 'message' && $event['message']['type'] === 'text') { 

                $userMessage = trim($event['message']['text']); 
                $replyToken = $event['replyToken']; // โทเค็นชั่วคราวสำหรับกดส่งตอบกลับทันที

                // 2. เช็กว่าข้อความขึ้นต้นด้วยคำว่า "งานเข้า" หรือไม่
                if (preg_match('/^งานเข้า\s+(\d+)$/u', $userMessage, $matches)) {
                   $notifyId = $matches[1]; // ดึงตัวเลขหลังคำว่า "งานเข้า" (เช่น เลข 3)
                    
                    // 3. ไปค้นหาข้อมูลจากฐานข้อมูล
                    $notify = TwNotifies::find($notifyId); 
                    
                    if ($notify) { 
                        // สร้างโครงสร้าง Flex Message จากข้อมูลจริงใน Database
                        $flexPayload = $this->buildStaffFlexMessage($notify); 
                        
                        // ส่ง Flex Message ตอบกลับไปหาห้องแชทนั้นทันที (ใช้ replyToken)
                        $this->sendReplyMessage($replyToken, $flexPayload); 
                    } else {
                        $this->sendReplyText($replyToken, "ไม่พบข้อมูลแจ้งเหตุรหัส #{$notifyId}");
                    }
                } 
            } 
        } 
        
        return response()->json(['status' => 'ok'], 200); 
    }

    /**
     * Summary of buildFlexReceipt
     * @param mixed $transaction
     * @return array{body: array, header: array, styles: array, type: string}
     */
    private function buildStaffFlexMessage(TwNotifies $notify)
    {
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
   return  [
        'type' => 'bubble',
        'header' => [
            'type' => 'box',
            'layout' => 'vertical',
            'contents' => [
                [
                    'type' => 'text',
                    'text' => 'แจ้งเตือนงานใหม่ (เจ้าหน้าที่งานประปา)',
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
                        'type' => 'uri',
                        'label' => 'กดเพื่อดูข้อมูลและรับงาน',
                        'uri' => 'https://yourdomain.com/notify/detail/' . $notify->id // ใส่ URL หน้าเว็บของคุณที่ต้องการให้กดแล้วเด้งไปหา
                    ],
                    'style' => 'primary',
                    'color' => '#0275d8' // สามารถปรับสีปุ่มได้ตามต้องการ (เช่น #0275d8 สีฟ้า, #d9534f สีแดง)
                ]
            ]
        ]
    ];
    }

    // ฟังก์ชันยิง Reply API กลับไปหา LINE
    private function sendReplyMessage($replyToken, $flexPayload)
    {
        $channelToken = config('services.line_staff.channel_token'); // หรือใส่ Token ตรงๆ

        Http::withHeaders([
            'Authorization' => 'Bearer ' . $channelToken,
            'Content-Type'  => 'application/json',
        ])->post('https://api.line.me/v2/bot/message/reply', [
            'replyToken' => $replyToken,
            'messages'   => [
                [
                    'type'     => 'flex',
                    'altText'  => 'รายละเอียดแจ้งเหตุ',
                    'contents' => $flexPayload
                ]
            ]
        ]);
    }

    private function sendReplyText($replyToken, $text)
    {
        $channelToken = config('services.line_staff.channel_token');

        Http::withHeaders([
            'Authorization' => 'Bearer ' . $channelToken,
            'Content-Type'  => 'application/json',
        ])->post('https://api.line.me/v2/bot/message/reply', [
            'replyToken' => $replyToken,
            'messages'   => [
                ['type' => 'text', 'text' => $text]
            ]
        ]);
    }

 



}

