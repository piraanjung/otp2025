<?php

namespace App\Http\Controllers;

use App\Models\KeptKaya\KPAccounts;
use App\Models\KeptKaya\KpPurchaseTransaction;
use App\Models\KeptKaya\KpPurchaseTransactionDetail;
use App\Models\KeptKaya\KpUserWastePreference;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class KioskApiController extends Controller
{
    // 1. Endpoint สำหรับรับ Transaction Log (JSON Batch)
    public function index()
    {
        return view('kiosk.index');
    }
    public function uploadTransactionLog(Request $request)
    {
        // ต้องมั่นใจว่าข้อมูลเป็น JSON Array
        $validatedData = $request->validate([
            'transactions' => 'required|array',
            'transactions.*.timestamp' => 'required|date',
            'transactions.*.bottle_type' => 'required|string',
            'transactions.*.action' => 'required|string',
            'transactions.*.img_filename' => 'nullable|string',
        ]);

        try {
            // โค้ดสำหรับบันทึกลงฐานข้อมูล (Database)
            // (ในตัวอย่างนี้จะ Log ข้อมูลแทนการบันทึกลง DB จริง)
            foreach ($validatedData['transactions'] as $transaction) {
                Log::info('New Kiosk Transaction:', $transaction);
                // **TODO:** เปลี่ยน Log::info เป็น Model::create($transaction) เพื่อบันทึกลง DB
            }

            return response()->json(['message' => 'Transaction logs uploaded and processed successfully.'], 200);
        } catch (\Exception $e) {
            Log::error('Transaction upload failed: ' . $e->getMessage());
            return response()->json(['message' => 'Server error during processing.'], 500);
        }
    }

    // 2. Endpoint สำหรับรับไฟล์ภาพ (JPEG)
    public function uploadImage(Request $request)
    {
        $request->validate([
            'image_file' => 'required|image|mimes:jpeg|max:2048', // 2MB Max
            'filename' => 'required|string'
        ]);

        try {
            // เก็บไฟล์ใน Storage/app/public/kiosk_images
            $path = $request->file('image_file')->storeAs(
                'public/kiosk_images',
                $request->input('filename')
            );

            // **TODO:** อาจบันทึก path ของภาพลงฐานข้อมูลเพื่อเชื่อมโยงกับ Transaction log

            return response()->json(['message' => 'Image uploaded successfully.', 'path' => Storage::url($path)], 200);
        } catch (\Exception $e) {
            Log::error('Image upload failed: ' . $e->getMessage());
            return response()->json(['message' => 'Image upload failed.'], 500);
        }
    }

    public function checkMember(Request $request)
    {
        // เช็คเบอร์โทร
        $user = User::where('phone', $request->phone)->first();

        if ($user) {
            return response()->json([
                'status' => 'found',
                'user_id' => $user->id,
                'picture' => $user->image,
                'name' => $user->firstname." ".$user->firstname,
            ]);
        }
        return response()->json(['status' => 'not_found'], 404);
    }

    public function saveSession(Request $request)
    {
        // 1. รับค่าจาก Frontend
        $phone = $request->input('phone');
        $items = $request->input('items'); // { "btmc_PET600": {count: 1, score: 10}, ... }
        $frontendTotalScore = $request->input('total_score');
        $recorderId = $request->input('kiosk_id', 999);
        
        DB::beginTransaction(); // เริ่ม Transaction Database เพื่อความปลอดภัย
        try {
            // 2. หา User จากเบอร์โทร
            $user = User::where('phone', $phone)->first();
            if (!$user) {
                return response()->json(['status' => 'error', 'message' => 'User not found'], 404);
            }

            // 3. หา User Preference
            $userWastePref = KpUserWastePreference::where('user_id', $user->id)->first();
            if (!$userWastePref) {
                // กรณีไม่เจอ Pref อาจจะต้องสร้างใหม่ หรือ return error (ที่นี่สมมติว่าต้องมี)
                return response()->json(['status' => 'error', 'message' => 'User Preference not found'], 404);
            }

            // 4. แปลงข้อมูล Frontend (Items) ให้เป็น format สำหรับบันทึก ($cart)
            // และคำนวณยอดรวมใหม่ฝั่ง Server เพื่อความถูกต้อง
            $cart = [];
            $totalPoints = 0;
            $totalAmount = 0; // กรณีไม่มีการจ่ายเงินสด ให้เป็น 0
            $totalWeight = 0; // Kiosk ไม่มีตาชั่ง ให้เป็น 0 หรือค่า default

            foreach ($items as $label => $data) {
                // *** สำคัญ: ต้อง Map จาก AI Label ("btmc_PET600") ไปหา Item ID ใน Database ***
                // ตัวอย่าง: ค้นหาจากชื่อ หรือ Hardcode mapping ไว้
                $recycleItem = $this->findItemByLabel($label); 
                
                if ($recycleItem) {
                    $qty = intval($data['count']);
                    $points = intval($data['score']); // หรือจะคำนวณใหม่จาก $recycleItem->point * $qty ก็ได้
                    
                    $totalPoints += $points;
                    
                    // เตรียมข้อมูลสำหรับ loop บันทึก Detail
                    $cart[] = [
                        'kp_tbank_item_id'           => $recycleItem->id,
                        'kp_tbank_items_pricepoint_id' => $recycleItem->current_pricepoint_id ?? 0, // สมมติว่ามี field นี้
                        'amount_in_units'            => $qty,
                        'price_per_unit'             => 0, // ไม่มีการซื้อขายเงินสด
                        'amount'                     => 0,
                        'points'                     => $points,
                        'unit_name'                  => 'ชิ้น' // หรือ $recycleItem->unit_name
                    ];
                }
            }

            // 5. สร้าง Transaction Header (ตาม Logic ที่คุณให้มา)
            $wasteId = substr("0000", strlen($userWastePref->id)) . $userWastePref->id;
            
            // Kiosk ส่วนใหญ่สะสมแต้มอย่างเดียว -> cash_back = 0
            $cashBack = 0; 

            $transaction = KpPurchaseTransaction::create([
                'kp_u_trans_no'        => 'T-' . Carbon::now()->format('ymdH') . $wasteId,
                'kp_user_w_pref_id_fk' => $userWastePref->id,
                'transaction_date'     => Carbon::now()->toDateString(),
                'total_weight'         => $totalWeight,
                'total_amount'         => $totalAmount,
                'total_points'         => $totalPoints,
                'recorder_id'          => $recorderId,
                'status'               => 1,
                'cash_back'            => $cashBack
            ]);

            // 6. จัดการ Account (Register / Update Balance)
            $findKPAccounts = KPAccounts::find($userWastePref->id);
            if (!$findKPAccounts) {
                (new KPAccounts())->registerAccount($userWastePref->id);
            }

            if ($cashBack == 0) {
                // สมาชิกฝากเงินเข้าบัญชี (หรือสะสมแต้ม)
                (new KPAccounts())->updateBalanceAndPoint($userWastePref->id, $totalAmount, $totalPoints);
            }

            // 7. บันทึก Transaction Detail (Loop จาก $cart ที่เตรียมไว้)
            foreach ($cart as $item) {
                KpPurchaseTransactionDetail::create([
                    'kp_purchase_trans_id'         => $transaction->id,
                    'kp_recycle_item_id'           => $item['kp_tbank_item_id'],
                    'kp_tbank_items_pricepoint_id' => $item['kp_tbank_items_pricepoint_id'],
                    'amount_in_units'              => $item['amount_in_units'],
                    'price_per_unit'               => $item['price_per_unit'],
                    'amount'                       => $item['amount'],
                    'points'                       => $item['points'],
                    'unit_type'                    => $item['unit_name'],
                    'recorder_id'                  => $recorderId
                ]);
            }

            DB::commit(); // บันทึกข้อมูลทั้งหมด
            return response()->json(['status' => 'success', 'points' => $totalPoints]);

        } catch (\Exception $e) {
            DB::rollBack(); // ถ้ามี Error ให้ยกเลิกการบันทึกทั้งหมด
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    // Helper Function: แปลง AI Label เป็น Database Object
    private function findItemByLabel($label)
    {
        // วิธีที่ 1: Query หาจากชื่อ (ถ้าใน DB เก็บชื่อตรงกัน)
        // return KpRecycleItem::where('name', $label)->first();

        // วิธีที่ 2: Hardcode Mapping (ถ้าชื่อ AI กับ DB ไม่ตรงกัน)
        // คุณต้องแก้ ID ให้ตรงกับใน Database ของคุณ
        $map = [
            'btmc_PET600' => 1, // สมมติว่า ID 1 คือ ขวด PET
            'can_alum'    => 2, // สมมติว่า ID 2 คือ กระป๋อง
            'glass_bottle'=> 3  // สมมติว่า ID 3 คือ ขวดแก้ว
        ];

        $id = $map[$label] ?? null;
        
        if ($id) {
            // return object KpRecycleItem หรือ object จำลอง
            // ในที่นี้สมมติคืนค่าเป็น Object ที่มี id
            return (object) ['id' => $id, 'current_pricepoint_id' => 1, 'unit_name' => 'ชิ้น'];
        }
        
        return null;
    }
}
