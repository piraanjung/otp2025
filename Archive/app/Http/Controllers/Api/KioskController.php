<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use App\Events\KioskImageCaptured;
use App\Events\KioskCommandSent;
use App\Models\KeptKaya\KpKioskUnknownItem;
use App\Models\KeptKaya\KpPurchaseTransaction;
use App\Models\KeptKaya\KpPurchaseTransactionDetail;
use App\Models\KeptKaya\KpTbankItems;
use App\Models\KeptKaya\KpTbankItemsPriceAndPoint;
use App\Models\KeptKaya\KpTbankUnits;
use App\Models\KeptKaya\KpUserWastePreference;
use App\Models\RecycleBankAccount;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class KioskController extends Controller
{

    // 1. เมื่อเซนเซอร์เจอวัตถุ

    public function index()
    {
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

    public function getRates()
    {
        // ดึงข้อมูลขยะหลัก พร้อมราคาเฉพาะของตู้คีออส (type = 'tbox')
        $items = KpTbankItems::where('status', 'active')
            ->with(['prices' => function ($q) {
                $q->where('type', 'tbox')
                    ->whereDate('effective_date', '<=', now());
            }])
            ->get();

        $rates = [];

        foreach ($items as $item) {
            foreach ($item->prices as $price) {
                // ประกอบร่างชื่อ เช่น: no_screen_has_cover_1500ml
                $sizeSuffix = $price->size_code ? '_' . $price->size_code : '';
                $mapKey = $item->kp_itemscode . $sizeSuffix;

                // 🔥 เปลี่ยนมาเก็บเป็น Array ที่มีทั้ง point และ price
                $rates[$mapKey] = [
                    'id' => $price->id,
                    'point' => (float) $price->point,
                    'price' => (float) $price->price_for_member
                ];
            }
        }

        // กันเหนียวกรณีหาขวดไม่เจอ ให้ค่าเริ่มต้นเป็น 1 แต้ม / 0 บาท
        $rates['default'] = [
            'id' => 0,
            'point' => 1,
            'price' => 0
        ];

        return response()->json($rates);
    }
    // =========================================================================
    // 🌿 [Branch: backend/feature-sync-text-handler]
    // ปรับปรุงการรับค่าข้อมูลขยะในโครงสร้าง Multipart FormData (Text Payload)
    // =========================================================================
    public function submitTransaction(Request $request)
    {
        // 1. รับข้อมูลจาก Mobile App (สอดรับกับ FormData ที่ส่งมาจากตัวตู้ AIroBacT)
        $userId      = $request->input('userId');
        $totalPoints = $request->input('totalPoints');
        $totalAmount = $request->input('totalPrice'); // หน้าบ้านใช้คีย์ 'totalPrice'

        // 🚨 [จุดแก้ไขสำคัญ] เนื่องจากหน้าบ้านทำ FormData แนบ JSON String ของตะกร้าขยะมาในฟิลด์ 'items'
        // เราต้องใช้ json_decode เพื่อแปลงข้อความ String กลับไปเป็นโครงสร้าง Array ใน PHP
        $itemsRaw    = $request->input('items');
        $items       = is_string($itemsRaw) ? json_decode($itemsRaw, true) : $itemsRaw;

        // ดักจับตรวจสอบความถูกต้องเบื้องต้น ป้องกันการส่งคิวขยะว่างเปล่ามาถล่มระบบ
        if (empty($items) || !is_array($items)) {
            return response()->json([
                'success' => false,
                'message' => 'ไม่พบข้อมูลรายการขยะในตระกร้า หรือโครงสร้างข้อมูลผิดพลาด'
            ], 400);
        }

        $resultData = DB::transaction(function () use ($userId, $items, $totalPoints, $totalAmount) {

            $userWastePref = KpUserWastePreference::where('user_id', $userId)->first();
            $user = User::find($userId);

            // ในโหมด Kiosk ให้ staff_id เป็น null
            $recorderId = null;
            $orgId      = $user->org_id_fk; // กำหนดตาม Organization ของตู้

            // 2. เจนเลขที่เอกสาร
            $transNo = 'Kiosk-' . now()->format('ymdHis') . strtoupper(Str::random(3));

            // 3. บันทึก Header (KpPurchaseTransaction)
            $transaction = KpPurchaseTransaction::create([
                'kp_u_trans_no'         => $transNo,
                'org_id_fk'             => $orgId,
                'kiosk_id_fk'           => 1, // ระบุว่าเป็นตู้ที่เท่าไหร่
                'kp_user_w_pref_id_fk'  => $userWastePref->id,
                'transaction_date'      => now(),
                'total_weight'          => 0, // Kiosk ไม่ได้ชั่งน้ำหนัก แต่ใช้จำนวนชิ้น
                'total_amount'          => $totalAmount,
                'total_points'          => $totalPoints,
                'recorder_id'           => $recorderId,
                'status'                => 'complete',
                'cash_back'             => 0, // Kiosk มักบันทึกเข้าสะสมแต้ม/เงิน ไม่ได้ทอนเงินสดทันที
                'deleted'               => 0
            ]);

            $carbonTotal = 0;
            // 4. บันทึก Details พร้อมจัดการรหัสขยะย่อย
            foreach ($items as $cartItem) {

                // 🎯 1. ดักจับกรณีหน้าบ้านส่งรหัส 0 หรือระบุว่าเป็นสิ่งแปลกปลอม
                if (empty($cartItem['rateId']) || $cartItem['rateId'] == 0) {
                    
                    // 🚀 สั่งแยกบันทึกข้อมูลเข้าตารางสิ่งแปลกปลอมทันทีเพื่อทำ Ref รอตรวจสอบ
                    KpKioskUnknownItem::create([
                        'kp_purchase_trans_id' => $transaction->id,
                        'org_id_fk'            => $orgId,
                        'kiosk_id_fk'          => 1, // ไอดีตู้
                        'user_id_fk'           => $userId,
                        'detected_label'       => $cartItem['sLabel'] ?? 'UNKNOWN_OBJECT',
                        'confidence_score'     => $cartItem['confidence'] ?? 0,
                        'image_path'           => null, // รอ Background Async Queue ส่งรูปมาอัปเดตพาร์ทภายหลัง
                        'status'               => 'pending_review'
                    ]);

                    // บันทึก Log แจ้งเตือนระบบหลังบ้าน
                    Log::warning("⚠️ [AIroBacT Kiosk] ตรวจพบสิ่งแปลกปลอมรหัส 0 จาก User: {$userId}, Class: " . ($cartItem['sLabel'] ?? 'Unknown'));
                    
                    continue; // ⚡ ข้ามลูปนี้ไปรายการถัดไปทันที ไม่ให้ไปลงตารางรายละเอียดหลัก
                }

                // 🚀 ระบบทำงานโหมดปกติ (กรณีมี rateId ที่มากกว่า 0)
                $dbItem = KpTbankItems::find($cartItem['rateId']);

                // 🚨 กรณีหา ID ไม่เจอในฐานข้อมูล (ข้อมูลไม่ตรงกัน)
                if (!$dbItem) {
                    $dbItem = KpTbankItems::where('kp_itemscode', 'default')->first();
                }

                // ถ้ายังไม่เจออีก ให้ข้ามรายการนี้ไป
                if (!$dbItem) continue;

                // --- [เริ่มต้นโค้ดบันทึกตารางรายละเอียดหลักตามโครงสร้างเดิมของพี่] ---

                // Logic การคำนวณ Carbon (ดึงจากความสัมพันธ์ emissionFactor)
                $ef = $dbItem->emissionFactor->ef_value ?? 0.5;
                $carbonSaved = 1 * $ef;
                $carbonTotal += $carbonSaved;

                $recycleId = KpTbankItemsPriceAndPoint::where('id', $dbItem->id)->get(['kp_items_idfk'])->first();
                $kp_units_idfk = KpTbankUnits::where('org_id_fk', $user->org_id_fk)
                    ->where('unitname', 'ขวด')->where('status', 'active')
                    ->get(['id'])->first();

               

                KpPurchaseTransactionDetail::create([
                    'org_id_fk'                    => $orgId,
                    'kp_purchase_trans_id'         => $transaction->id,
                    'kp_recycle_item_id'           => $recycleId->kp_items_idfk,
                    'kp_tbank_items_pricepoint_id' => $dbItem->id,
                    'amount_in_units'              => 1,
                    'kp_units_idfk'                => $kp_units_idfk->id,
                    'price_per_unit'               => $cartItem['price'] ?? 0,
                    'amount'                       => 1,
                    // ดักจับตรวจสอบคะแนน หากความแม่นยำต่ำกว่า Threshold (80%) แต้มจะเป็น 0 ทันทีตามเงื่อนไขพี่
                    'points'                       => ($cartItem['confidence'] ?? 100) < 80 ? 0 : ($cartItem['point'] ?? 0),
                    'carbon_saved'                 => $carbonSaved,
                    'recorder_id'                  => $recorderId,
                    // 💡 [คำแนะนำชิ้นส่วนย่อย] หากพี่ต้องการบันทึกสถานะการซิงค์รูปภาพลงในตาราง Detail 
                    // สามารถระบุฟิลด์เช่น 'image_sync_status' => 'pending' เพื่อรอรับรูปภาพจากคิวเบื้องหลังได้ครับ
                ]);
            }

            // 5. อัปเดตยอด Carbon รวม
            $transaction->update(['total_carbon_saved' => $carbonTotal]);

            // 6. อัปเดตแต้ม/เงินในบัญชีผู้ใช้ (RecycleBankAccount)
            $recycleAcc = RecycleBankAccount::firstOrCreate(
                ['user_id' => $userId],
                [
                    'account_no' => 'ACC-' . str_pad($userId, 6, '0', STR_PAD_LEFT),
                    'balance'    => 0,
                    'points'     => 0,
                    'status'     => 'active'
                ]
            );

            $recycleAcc->increment('points', $totalPoints);

            return [
                'success' => true,
                'trans_no' => $transNo
            ];
        });

        return response()->json($resultData);
    }

    // =========================================================================
    // 🌿 [Branch: backend/bugfix-public-move-handler]
    // แก้ไขฟังก์ชันรับรูปภาพเบื้องหลัง เพื่อให้อัปเดต image_path เข้าตารางสิ่งแปลกปลอมออโต้
    // =========================================================================
    public function uploadItemImageChunk(Request $request)
    {
        $transNo = $request->input('trans_no');
        $itemIndex = $request->input('item_index');
        
        $fileName = 'trans_' . $transNo . '_item_' . $itemIndex . '_' . time() . '.jpg';
        $subFolder = 'waste_items/' . $transNo;
        $destinationPath = public_path($subFolder);

        if (!File::exists($destinationPath)) {
            File::makeDirectory($destinationPath, 0775, true, true);
        }

        $isSaved = false;

        // 🎯 โหมดที่ 1: รับไฟล์แบบปกติ
        if ($request->hasFile('waste_image')) {
            $file = $request->file('waste_image');
            $file->move($destinationPath, $fileName);
            $isSaved = true;
        } 
        // 🎯 โหมดที่ 2: รับข้อมูลแบบ Base64 (Hybrid)
        else {
            $rawImageContent = $request->input('waste_image') ?? $request->getContent();
            if (!empty($rawImageContent) && (strpos($rawImageContent, 'data:image') !== false || strlen($rawImageContent) > 1000)) {
                $imageData = preg_replace('#^data:image/\w+;base64,#', '', $rawImageContent);
                $imageData = str_replace(' ', '+', $imageData);
                File::put($destinationPath . '/' . $fileName, base64_decode($imageData));
                $isSaved = true;
            }
        }

        // =========================================================================
        // 🔥 [จุดที่ต้องเพิ่มใหม่] อัปเดตพาร์ทรูปภาพเข้าสู่ตารางสิ่งแปลกปลอมอัตโนมัติ
        // =========================================================================
        if ($isSaved) {
            $relativeImagePath = $subFolder . '/' . $fileName; // พาร์ทที่จะเก็บลงตาราง (เช่น waste_items/Kiosk-xxxx/item_0.jpg)

            // 1. ค้นหาธุรกรรมหลักก่อนเพื่อเอา ID (เพราะหน้าบ้านส่งเป็นเลข String เช่น Kiosk-260611xxxx)
            $mainTrans = KpPurchaseTransaction::where('kp_u_trans_no', $transNo)->first();

            if ($mainTrans) {
                // 2. ค้นหาตารางสิ่งแปลกปลอมที่ผูกกับบิลนี้ และส่องหาดัชนีรูปที่ส่งมา (ป้องกันกรณีหย่อนสิ่งแปลกปลอมหลายชิ้น)
                // โดยใช้เทคนิค skip($itemIndex) เพื่อให้อัปเดตพาร์ทรูปภาพตรงตามลำดับชิ้นในตะกร้า
                $unknownItem = KpKioskUnknownItem::where('kp_purchase_trans_id', $mainTrans->id)
                    ->whereNull('image_path') // เลือกตัวที่ยังไม่มีรูปภาพ
                    ->orderBy('id', 'asc')
                    ->first();

                // 3. ถ้าเจอรายการแปลกปลอมค้างอยู่ ให้สั่งสวมรอยพ่นอัปเดตพาร์ทรูปภาพลงไปทันที
                if ($unknownItem) {
                    $unknownItem->update([
                        'image_path' => $relativeImagePath
                    ]);
                    Log::info("📸 [AIroBacT Sync] แนบรูปภาพสิ่งแปลกปลอมสำเร็จ ID: {$unknownItem->id} -> Path: {$relativeImagePath}");
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'บันทึกรูปภาพและเชื่อมโยงระบบฐานข้อมูลสำเร็จแล้วครับพี่',
                'image_url' => url($relativeImagePath)
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'เซิร์ฟเวอร์ไม่ได้รับไฟล์ภาพถ่ายขยะ หรือโครงสร้างข้อมูลสูญหายระหว่างทาง'
        ], 400);
    }

    public function uploadOfflineImages(Request $request)
    {
        $transNo = $request->transNo;
        $images  = $request->images;
        $userId  = $request->userId ?? 1;

        // หา User และ Preference ID
        $user = User::find($userId);
        $prefId = ($user && $user->wastePreference) ? $user->wastePreference->id : 1;
        $orgId = $user ? $user->org_id_fk : 1; // กันเหนียวถ้าไม่เจอ User

        // 1. ตรวจสอบหรือสร้าง Transaction หลัก
        $transaction = KpPurchaseTransaction::firstOrCreate(
            ['kp_u_trans_no' => $transNo],
            [
                'org_id_fk'            => $orgId,
                'kiosk_id_fk'          => 1,
                'kp_user_w_pref_id_fk' => $prefId,
                'transaction_date'     => now(),
                'total_weight'         => 0,
                'total_amount'         => 0,
                'total_points'         => 0,
                'status'               => 2, // 'PENDING'
                'total_carbon_saved'   => 0,
                'cash_back'            => 0,
                'recorder_id'          => 1
            ]
        );

        $savedCount = 0;
        $subFolder = 'kiosk_captures/' . now()->format('Y-m-d');
        $directory = public_path($subFolder);

        // ใช้ \File เพื่อเรียก Facade ของ Laravel
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        foreach ($images as $imgData) {
            $uniqueId = substr(md5(microtime()), 0, 5);
            $fileName = $transNo . '_' . ($imgData['type'] ?? 'item') . '_' . $uniqueId . '.jpg';
            $fullPath = $directory . '/' . $fileName;

            if ($this->saveBase64ToPublic($imgData['imageBase64'], $fullPath)) {
                $dbPath = $subFolder . '/' . $fileName;

                $rawRateId = $imgData['rateId']; // เช่น 'bg'
                $finalPricePointId = 0;

                // 🎯 แก้ไขชื่อคอลัมน์ให้ตรงกับ DB ของพี่ (kp_itemscode)
                if (!is_numeric($rawRateId)) {
                    $item = KpTbankItems::where('kp_itemscode', $rawRateId)->first();
                    if ($item) {
                        // ใช้ Relation currentPriceAndPoint ที่พี่เขียนไว้ใน Model
                        $pp = $item->currentPriceAndPoint;
                        $finalPricePointId = $pp ? $pp->id : 0;
                    }
                } else {
                    $finalPricePointId = (int)$rawRateId;
                }

                $pricePoint = KpTbankItemsPriceAndPoint::find($finalPricePointId);

                // 🎯 บันทึกลง Detail
                $detail = KpPurchaseTransactionDetail::updateOrCreate(
                    [
                        'kp_purchase_trans_id'         => $transaction->id,
                        'kp_tbank_items_pricepoint_id' => $finalPricePointId,
                        'image_path'                   => null
                    ],
                    [
                        'org_id_fk'          => $orgId,
                        'kp_recycle_item_id' => $pricePoint ? $pricePoint->kp_items_idfk : 0,
                        'kp_units_idfk'      => $pricePoint ? $pricePoint->kp_units_idfk : 1,
                        'recorder_id'        => $transaction->recorder_id,
                        'image_path'         => $dbPath,
                        'amount_in_units'    => 1,
                        'price_per_unit'     => $pricePoint ? $pricePoint->price_for_member : 0,
                        'amount'             => $pricePoint ? $pricePoint->price_for_member : 0,
                        'points'             => $pricePoint ? $pricePoint->point : 0,
                        'carbon_saved'       => 0
                    ]
                );

                if ($detail) $savedCount++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Sync สำเร็จ $savedCount รายการ",
            'debug'   => $transaction->wasRecentlyCreated ? 'New Trans' : 'Exist Trans'
        ]);
    }

    /**
     * ฟังก์ชันช่วยบันทึก Base64 ลง Public Path ตรงๆ (ตัดปัญหา Permission ยุ่งยาก)
     */
    private function saveBase64ToPublic($base64String, $fullPath)
    {
        try {
            if (preg_match('/^data:image\/(\w+);base64,/', $base64String, $type)) {
                $base64String = substr($base64String, strpos($base64String, ',') + 1);
            }

            $imageBinary = base64_decode($base64String);
            return file_put_contents($fullPath, $imageBinary) !== false;
        } catch (\Exception $e) {
            return false;
        }
    }
}
