<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admin\ManagesTenantConnection;
use App\Models\Admin\Organization;
use App\Models\Admin\Province;
use App\Models\KeptKaya\KPAccounts;
use App\Models\KeptKaya\KpPurchaseTransaction;
use App\Models\KeptKaya\KpUserWastePreference;
use App\Models\SuperUser;
use App\Models\Tabwater\SequenceNumber;
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

    public function handleWebhook(Request $request)
    {
        Log::info('LINE Webhook Data: ', $request->all());
        $events = $request->input('events', []);

        foreach ($events as $event) {
            if ($event['type'] === 'message' && $event['message']['type'] === 'text') {
                $replyToken = $event['replyToken'];
                $userText = trim($event['message']['text']);
                $lineId = $event['source']['userId'];

                // เงื่อนไข: ถ้าพิมพ์ว่า "ใบเสร็จ"
                if (str_contains($userText, 'ใบเสร็จ')) {
                    $this->replyWithLastReceipt($lineId, $replyToken);
                }

                // เงื่อนไข: ถ้าพิมพ์ว่า "แต้ม" (แถมให้)
                if (str_contains($userText, 'แต้ม')) {
                    $this->replyWithPoints($lineId, $replyToken);
                }

                if (str_contains($userText, 'ขายขยะด้วย QR Code')) {
                    $this->replyWithUserQrCode($lineId, $replyToken);
                }
            }
        }
        return response()->json(['status' => 'ok']);
    }

    private function replyWithUserQrCode($lineId, $replyToken)
{
    // ค้นหา User จาก lineId ใน Database ของคุณ
    // สมมติว่าตาราง users มีคอลัมน์ line_user_id
    $user = User::where('line_id', $lineId)->first();

    if (!$user) {
        // ถ้าไม่พบ User ให้แจ้งเตือนให้เขาลงทะเบียนก่อน
        return $this->replyTextMessage($replyToken, "ขออภัยครับ ไม่พบข้อมูลสมาชิกของคุณในระบบ กรุณาลงทะเบียนก่อนใช้งานครับ");
    }

    $qrContent = "USER-" . $user->id;
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
     * ค้นหาใบเสร็จล่าสุดและตอบกลับด้วย Flex Message (ฟรี)
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
     * Helper สำหรับส่งข้อความตัวอักษรธรรมดา
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
}
