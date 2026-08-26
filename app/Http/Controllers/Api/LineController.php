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
            return response()->json(['status' => 'not_found']);
        }
        if(collect($user->wastePreference)->isEmpty()){
            KpUserWastePreference::create([
                'user_id' => $user[0]->id,
                'org_id_fk' => $user[0]->org_id_fk,
                'is_waste_bank' => 1,
                'is_annual_collection' => 0,
                "address" => $user[0]->address,
                "zone_id" => $user[0]->zone_id,
                "subzone_id" => $user[0]->subzone_id,
                "tambon_code" => $user[0]->tambon_code,
                "district_code" => $user[0]->district_code,
                "province_code" => $user[0]->province_code,
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

    // app/Http/Controllers/Api/LineController.php

    public function handle(Request $request)
    {
        $events = $request->input('events', []);

        foreach ($events as $event) {
            // ดักจับเหตุการณ์ที่เป็นการส่งข้อความตัวหนังสือ (Text Message)
            if ($event['type'] === 'message' && $event['message']['type'] === 'text') {
                
                $userMessage = trim($event['message']['text']);
                $replyToken  = $event['replyToken'];

                // ใช้ Regex ดักคำสั่ง เช่น "งานเข้า 1042" หรือ "งานเข้า#1042"
                if (preg_match('/^งานเข้า\s*#?(\d+)$/u', $userMessage, $matches)) {
                    $notifyId = $matches[1];

                    // ค้นหาข้อมูลการแจ้งเหตุใน DB
                    $notify = TwNotifies::find($notifyId);

                    if ($notify) {
                        // 1. สร้างโครงสร้าง Flex Message
                        $flexPayload = $this->buildStaffFlexMessage($notify);

                        // 2. ส่ง Reply Message กลับไปที่กลุ่ม (ใช้ Reply API ฟรี)
                        $this->sendReplyMessage($replyToken, $flexPayload);
                    }
                }
            }
        }

        return response()->json(['status' => 'ok'], 200);
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

    /**
     * Summary of buildFlexReceipt
     * @param mixed $transaction
     * @return array{body: array, header: array, styles: array, type: string}
     */
    public function buildFlexReceipt($transaction)
    {
        $itemContents = [];

        // หมายเหตุ: อย่าลืมเอาบรรทัด find(1) ออกเมื่อใช้งานจริงนะครับ เพื่อให้ใช้ค่า $transaction ที่รับมาจาก Parameter
        // $transaction = KpPurchaseTransaction::find(1);

        foreach ($transaction->details as $detail) {
            $itemContents[] = [
                "type" => "box",
                "layout" => "vertical",
                "margin" => "lg", // เพิ่มระยะห่างระหว่างรายการขยะ
                "spacing" => "sm",
                "contents" => [
                    [
                        "type" => "box",
                        "layout" => "horizontal",
                        "contents" => [
                            [
                                "type" => "text",
                                "text" => (string)($detail->item->kp_itemsname ?? 'ขยะรีไซเคิล'),
                                "size" => "md",
                                "color" => "#555555",
                                "flex" => 0,
                                "weight" => "bold"
                            ],
                            [
                                "type" => "text",
                                "text" => number_format($detail->amount, 2) . " บาท", // เปลี่ยนเป็น $detail->amount
                                "size" => "md",
                                "color" => "#111111",
                                "align" => "end",
                                "weight" => "bold"
                            ]
                        ]
                    ],
                    [
                        "type" => "box",
                        "layout" => "horizontal",
                        "contents" => [
                            [
                                "type" => "text",
                                // เปลี่ยนเป็น amount_in_units และ unit_short_name
                                "text" => "(" . (float)$detail->amount_in_units . " " . ($detail->unit->unit_short_name ?? 'กก.') . " x " . number_format($detail->price_per_unit, 2) . " บาท)",
                                "size" => "xs",
                                "color" => "#555555",
                                "flex" => 0,
                                "style" => "italic",
                                "wrap" => true,
                            ],
                            [
                                "type" => "text",
                                "text" => " ",
                                "flex" => 1
                            ]
                        ]
                    ]
                ]
            ];
        }

        if (empty($itemContents)) {
            $itemContents[] = ["type" => "text", "text" => "ไม่พบรายการสินค้า", "size" => "sm", "color" => "#aaaaaa"];
        }

        return [
            "type" => "bubble",
            "header" => [
                "type" => "box",
                "layout" => "vertical",
                "contents" => [
                    ["type" => "text", "text" => "ใบเสร็จรับเงิน", "weight" => "bold", "color" => "#1DB446", "size" => "xl"],
                    ["type" => "text", "text" => "ธนาคารขยะรีไซเคิล", "weight" => "bold", "size" => "xxl", "margin" => "md", "color" => "#ffffff"],
                    ["type" => "text", "text" => "ขอบคุณที่ร่วมเป็นส่วนหนึ่งในการรักษาสิ่งแวดล้อม", "size" => "xs", "color" => "#aaaaaa", "wrap" => true]
                ]
            ],
            "body" => [
                "type" => "box",
                "layout" => "vertical",
                "contents" => [
                    ["type" => "box", "layout" => "vertical", "contents" => $itemContents],
                    ["type" => "separator", "margin" => "xxl"],

                    // --- ส่วนสรุปยอด (เพิ่ม Margin เพื่อให้ไม่ติดกัน) ---
                    [
                        "type" => "box",
                        "layout" => "horizontal",
                        "margin" => "xl", // ห่างจากเส้นคั่น
                        "contents" => [
                            ["type" => "text", "text" => "รวมเป็นเงิน", "size" => "sm", "color" => "#555555", "weight" => "bold"],
                            ["type" => "text", "text" => number_format($transaction->total_amount, 2) . " บาท", "size" => "sm", "color" => "#111111", "align" => "end", "weight" => "bold"]
                        ]
                    ],
                    [
                        "type" => "box",
                        "layout" => "horizontal",
                        "margin" => "md", // เพิ่มช่องว่างแถวแต้ม
                        "contents" => [
                            ["type" => "text", "text" => "แต้มที่ได้รับ", "size" => "sm", "color" => "#555555", "weight" => "bold"],
                            ["type" => "text", "text" => "+ " . number_format($transaction->total_points) . " แต้ม", "size" => "sm", "color" => "#111111", "align" => "end", "weight" => "bold"]
                        ]
                    ],
                    [
                        "type" => "box",
                        "layout" => "horizontal",
                        "margin" => "md", // เพิ่มช่องว่างแถวคาร์บอน
                        "contents" => [
                            ["type" => "text", "text" => "ลดคาร์บอนได้", "size" => "sm", "color" => "#1DB446", "weight" => "bold"],
                            ["type" => "text", "text" => number_format($transaction->total_carbon_saved ?? 0, 4) . " kgCO2e", "size" => "sm", "color" => "#1DB446", "align" => "end", "weight" => "bold"]
                        ]
                    ],

                    ["type" => "separator", "margin" => "xxl"],

                    [
                        "type" => "box",
                        "layout" => "horizontal",
                        "margin" => "lg", // ดึงเลขที่ใบเสร็จให้ห่างออกมา
                        "contents" => [
                            ["type" => "text", "text" => "เลขที่ใบเสร็จ", "size" => "xs", "color" => "#aaaaaa", "flex" => 0],
                            ["type" => "text", "text" => (string)($transaction->kp_u_trans_no ?? '-'), "color" => "#aaaaaa", "size" => "xs", "align" => "end"]
                        ]
                    ]
                ]
            ],
            "styles" => [
                "header" => [
                    "separator" => true,
                    "backgroundColor" => "#111111",
                    "separatorColor" => "#111111"
                ],
                "body" => [
                    "separator" => true,
                    "separatorColor" => "#ee2385" // 🌟 เปลี่ยนเส้นคั่นระหว่าง Header และ Body เป็นสีชมพู
                ],
                "footer" => [
                    "separator" => true
                ]
            ]
        ];
    }

    /**
     * Summary of replyText
     * @param mixed $replyToken
     * @param mixed $text
     * @return \Illuminate\Http\Client\Response
     */
    private function replyText($replyToken, $text)
    {
        return Http::withHeaders([
            'Authorization' => 'Bearer ' . env('LINE_CHANNEL_ACCESS_TOKEN'),
        ])->post('https://api.line.me/v2/bot/message/reply', [
            'replyToken' => $replyToken,
            'messages' => [['type' => 'text', 'text' => $text]]
        ]);
    }

    /**
     * Summary of buildStaffFlexMessage
     * @param TwNotifies $notify
     * @return array{altText: string, contents: array, type: string}
     */
    private function buildStaffFlexMessage(TwNotifies $notify)
    {
        $notify = TwNotifies::find(1);

        // $notify = TwNotifies::where('id', 1)
        // ->with('issueType')
        // ->get()->first();
        // แกะรูปภาพแรกจาก JSON photo_path
        $imageUrl = 'https://via.placeholder.com/600x400?text=No+Image';
        if (!empty($notify->photo_path)) {
            $photos = is_array($notify->photo_path) 
                ? $notify->photo_path 
                : json_decode($notify->photo_path, true);

            if (!empty($photos[0])) {
                $imageUrl = asset($photos[0]);
            }
        }

        // แปลงประเภทงานเป็นภาษาไทย
       
        $typeName = $notify->issueType->name;
        // $typeName = $typeNames[$notify->issue_type] ?? $notify->issue_type;

        // ลิงก์สำหรับช่างกดรับงาน และ ลิงก์แผนที่นำทาง
        $acceptJobUrl  = route('staff.job.accept', ['notify' => $notify->id]);
        $googleMapsUrl = "https://www.google.com/maps?q={$notify->latitude},{$notify->longitude}";

        return [
            'type' => 'flex',
            'altText' => "🚨 แจ้งเหตุงานประปา (#{$notify->id})",
            'contents' => [
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
                            'text' => "🚨 แจ้งเหตุใหม่ (#{$notify->id})",
                            'weight' => 'bold',
                            'size' => 'lg',
                            'color' => '#1DB446',
                        ],
                        [
                            'type' => 'box',
                            'layout' => 'vertical',
                            'margin' => 'md',
                            'spacing' => 'xs',
                            'contents' => [
                                [
                                    'type' => 'text',
                                    'text' => "ประเภท: {$typeName}",
                                    'size' => 'sm',
                                    'color' => '#555555',
                                    'weight' => 'bold',
                                ],
                                [
                                    'type' => 'text',
                                    'text' => "สถานะ: รอดำเนินการ",
                                    'size' => 'xs',
                                    'color' => '#ff9900',
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
                                'uri' => $acceptJobUrl,
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
            ],
        ];
    }

    /**
     * Summary of sendReplyMessage
     * @param mixed $replyToken
     * @param mixed $flexPayload
     * @return void
     */
    private function sendReplyMessage($replyToken, $flexPayload)
    {
        $channelToken = config('services.line_staff.channel_token');

        Http::withHeaders([
            'Authorization' => "Bearer {$channelToken}",
            'Content-Type'  => 'application/json',
        ])->post('https://api.line.me/v2/bot/message/reply', [
            'replyToken' => $replyToken,
            'messages'   => [$flexPayload],
        ]);
    }
}

