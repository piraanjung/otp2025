<?php

namespace App\Http\Controllers\Kiosk; // 👈 เปลี่ยน Namespace เป็น Kiosk

use App\Http\Controllers\Controller;
use App\Models\KeptKaya\KpPurchaseTransaction;
use App\Models\KeptKaya\KpTbankItemsPriceAndPoint;
use App\Models\KeptKaya\KpTbankUnits;
use App\Models\KeptKaya\KpUserWastePreference;
use App\Models\Kiosk;
use App\Models\KioskMatch;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KioskController extends Controller
{
    public function index()
    {
        $kiosks = Kiosk::orderBy('id', 'asc')->get();
        // เปลี่ยน view path เป็น 'kiosk.index' (ถ้าคุณย้าย view ด้วย)
        // หรือใช้ 'admin.keptkayas.kiosks.index' เหมือนเดิมถ้า view ยังอยู่ที่เดิม
        return view('kiosk.index', compact('kiosks'));
    }

    public function login()
    {

        return view('kiosk.login');
    }

    public function create()
    {
        return view('kiosk.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'id' => 'required|unique:kiosks,id|alpha_dash|max:20',
            'name' => 'required|max:100',
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
        ]);

        return   Kiosk::create([
            'id' => $request->id,
            'org_id_fk' => Auth::user()->org_id_fk,
            'name' => $request->name,
            'lat' => $request->lat,
            'lng' => $request->lng,
            'status' => 'offline',
            'total_waste_count' => 0
        ]);

        return redirect()->route('keptkayas.kiosks.index')->with('success', 'เพิ่มตู้สำเร็จ');
    }

    public function edit($id)
    {
        $kiosk = Kiosk::findOrFail($id);
        return view('kiosk.edit', compact('kiosk'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|max:100',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
        ]);

        $kiosk = Kiosk::findOrFail($id);
        $kiosk->update([
            'name' => $request->name,
            'lat' => $request->lat,
            'lng' => $request->lng,
        ]);

        return redirect()->route('keptkayas.kiosks.index')->with('success', 'อัปเดตเรียบร้อย');
    }

    public function destroy($id)
    {
        $kiosk = Kiosk::findOrFail($id);
        $kiosk->delete();
        return redirect()->route('keptkayas.kiosks.index')->with('success', 'ลบตู้เรียบร้อย');
    }

    public function userMatchKiosk(Request $request) {
    $kiosk = Kiosk::find($request->kiosk_id);
    $timeLimit = now()->subSeconds(30); // ยอมรับความล่าช้าได้ 30 วินาที

    // เช็ค NodeMCU
    $mcuReady = ($kiosk->mcu_last_heartbeat > $timeLimit && $kiosk->mcu_status == 'ok');
    // เช็ค ESP32-CAM
    $camReady = ($kiosk->cam_last_heartbeat > $timeLimit && $kiosk->cam_status == 'ok');

    if (!$mcuReady || !$camReady) {
        $errorMsg = !$mcuReady ? "ระบบเซนเซอร์ไม่พร้อม " : "";
        $errorMsg .= !$camReady ? "ระบบกล้องไม่พร้อม" : "";

        return response()->json([
            'status' => 'error',
            'message' => 'ตู้ไม่พร้อมใช้งาน: ' . $errorMsg
        ], 503);
    }

    // ถ้าผ่านทั้งคู่ค่อยอนุญาตให้ Match
    // ... logic matching ...
}

    public function monitor()
    {
        // หาว่า User คนนี้ กำลังเชื่อมต่อกับตู้ไหนอยู่?
        // ใช้ withoutGlobalScopes เพื่อความชัวร์ เผื่อติดเรื่อง Org ID
        $kiosk = Kiosk::withoutGlobalScopes()->where('current_user_id', Auth::id())->first();

        // ถ้าไม่เจอตู้ที่เชื่อมต่อ (เช่น กดเข้ามาเองผ่าน URL) ให้เด้งกลับไปหน้าสแกน
        if (!$kiosk) {
            return redirect()->route('keptkayas.kiosks.noscreen.login')->with('error', 'กรุณาสแกน QR Code ก่อนใช้งาน');
        }

        return view('kiosk.monitor', compact('kiosk'));
    }

    // ฟังก์ชันสำหรับกดยกเลิกการใช้งาน (Disconnect)
    public function disconnect(Request $request)
    {
        $kiosk = Kiosk::withoutGlobalScopes()
            ->where('current_user_id', Auth::id())
            ->first();

        if ($kiosk) {
            // ใช้ Query Builder เพื่อ Update ข้าม Scope เหมือนเดิม
            Kiosk::where('id', $kiosk->id)->update([
                'status' => 'idle',      // หรือ status อื่นที่ต้องการหลังเลิกใช้
                'current_user_id' => null, // เคลียร์คนใช้งาน
                'last_online_at' => now()
            ]);
        }

        return redirect()->route('kiosk.scan');
    }
    public function storeTransaction(Request $request)
    {
        // 1. รับค่าและ Validate
        $request->validate([
            'kioskId' => 'required',
            'inventory' => 'required|array', // { 'Plastic Bottle': 5, 'Can': 2 }
        ]);

        $kioskId = $request->kioskId;
        $inventory = $request->inventory;
        $user = Auth::user();
        // ค้นหาตู้ Kiosk
        $kiosk = Kiosk::withoutGlobalScopes()->find($kioskId);
        if (!$kiosk) return response()->json(['status' => 'error', 'message' => 'Kiosk not found'], 404);

        // 2. เตรียมข้อมูลสำหรับ Mapping (AI Label -> Database ID)
        // คุณต้องแก้ ID ให้ตรงกับในตาราง kp_tbank_items ของคุณ
        $itemMapping = [
            'ขวดพลาสติก/PET0250' => 1, // สมมติ ID 1 คือ PET Clear
            'กระป๋อง/CAN001'    => 2, // สมมติ ID 2 คือ Aluminium Can
            'ขวดแก้ว/OT0001'    => 3, // สมมติ ID 3 คือ Glass
            // 'Plastic Bottle' => 1, // กรณีส่งภาษาอังกฤษมา
        ];

        // 3. เริ่ม Transaction (ถ้า Error จะ Rollback ทั้งหมด)
        return DB::transaction(function () use ($kiosk, $user, $inventory, $itemMapping) {

            // A. คำนวณยอดรวม และ เตรียมข้อมูล Details
            $detailsData = [];
            $grandTotalAmount = 0;
            $grandTotalPoints = 0;
            $grandTotalWeight = 0; // ในที่นี้ AI นับเป็นชิ้น น้ำหนักอาจจะประมาณการหรือเป็น 0

            foreach ($inventory as $label => $qty) {
                if (!isset($itemMapping[$label])) continue; // ถ้าไม่รู้จัก item นี้ให้ข้าม

                $itemId = $itemMapping[$label];

                // ดึงราคา/คะแนน ปัจจุบันจาก DB (Table: kp_tbank_items_price_and_point)
                // เลือก record ล่าสุดที่ Active
                $priceConfig = $this->getPrice($itemId, 2);

                // ค่า Default ถ้าหาราคาไม่เจอ
                $pricePerUnit = $priceConfig ? $priceConfig->price_for_member : 0;
                $pointPerUnit = $priceConfig ? $priceConfig->point : 0;
                $priceConfigId = $priceConfig ? $priceConfig->id : null;
                $unitId = $priceConfig ? $priceConfig->kp_units_idfk : 2; // 1 = ชิ้น/Piece (สมมติ)

                $subTotalAmount = $qty * $pricePerUnit;
                $subTotalPoints = $qty * $pointPerUnit;

                // สะสมยอดรวม Header
                $grandTotalAmount += $subTotalAmount;
                $grandTotalPoints += $subTotalPoints;
                $grandTotalWeight += ($qty * 0.01); // สมมติชิ้นละ 10 กรัม (ถ้าต้องการเก็บนน.)

                // เตรียมข้อมูล Detail
                $detailsData[] = [
                    'kp_recycle_item_id' => $itemId,
                    'kp_units_idfk' => $unitId,
                    'kp_tbank_items_pricepoint_id' => $priceConfigId,
                    'amount_in_units' => $qty, // จำนวนที่นับได้
                    'price_per_unit' => $pricePerUnit,
                    'amount' => $subTotalAmount,
                    'points' => $subTotalPoints,
                    'comment' => 'Auto-detected by AI Kiosk',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // ถ้าไม่มีรายการสินค้าเลย
            if (empty($detailsData)) {
                return response()->json(['status' => 'error', 'message' => 'No valid items found'], 400);
            }

            // B. สร้าง Header (KpPurchaseTransaction)
            // สร้างเลข Transaction (ตัวอย่าง: TRX-YYYYMMDD-Random)
            $transNo = 'TRX-' . Carbon::now()->format('YmdHis') . '-' . rand(100, 999);

            $transaction = KpPurchaseTransaction::create([
                'org_id_fk' => $kiosk->org_id_fk, // ใช้ Org เดียวกับตู้
                'kp_u_trans_no' => $transNo,
                'kp_user_w_pref_id_fk' => 13, // หรือ ID ของ Profile ที่ผูกกับ User
                'machine_id_fk' => null,
                'kiosk_id_fk' => $kiosk->id,
                'transaction_date' => now(),
                'total_weight' => $grandTotalWeight,
                'total_amount' => $grandTotalAmount,
                'total_points' => $grandTotalPoints,
                'status' => '1', // หรือ pending
                'recorder_id' => $user->id, // หรือ ID ของระบบ System
                'cash_back' => 0,
            ]);

            // C. สร้าง Details (KpPurchaseTransactionDetail)
            // ใช้ createMany ผ่าน Relationship ได้เลย
            $transaction->details()->createMany($detailsData);

            // D. (Optional) อัปเดต Kiosk Status กลับเป็น Idle
            $kiosk->update(['status' => 'idle', 'current_user_id' => null]);

            return response()->json([
                'status' => 'success',
                'message' => 'Transaction saved',
                'trans_no' => $transNo,
                'total_amount' => $grandTotalAmount
            ]);
        });
    }

    public function getPrice($itemId, $unitId)
    {
        // $unitId อาจจะเป็น 'kg' หรือ 'piece'
        // $unit = KpTbankUnits::where('id', $unitId)->first();

        return KpTbankItemsPriceAndPoint::where('kp_items_idfk', $itemId)
            ->where('kp_units_idfk', $unitId)
            ->where('status', 'active')
            ->first();
    }
    public function wakeUp(Request $request)
    {
        // $kioskId = $request->kiosk_id;

        // // อัปเดตสถานะใน Database
        // Kiosk::where('kiosk_id', $kioskId)->update(['status' => 'READY']);

        // // ส่งสัญญาณ Real-time ผ่าน Laravel Reverb หรือ Pusher
        // broadcast(new KioskReady($kioskId));

        // return response()->json(['message' => 'Acknowledged']);
    }

    public function checkStatus(Request $request)
    {
        $kioskId = $request->kiosk;

        // ดึงสถานะล่าสุดจาก Database
        $kiosk = Kiosk::where('kiosk_id', $kioskId)->first();

        // Logic การตอบกลับ
        if ($kiosk->status == 'WAITING_SCAN') {
            return "WAIT"; // บอก NodeMCU ว่ายังไม่มีใครสแกน (รอต่อไปจนครบ 15 วิ)
        } elseif ($kiosk->status == 'PAIRED') {
            return "PAIRED"; // มีคนสแกนแล้ว! (NodeMCU จะขยายเวลา)
        } elseif ($kiosk->current_command == 'OPEN') {
            // เมื่อส่งคำสั่ง OPEN แล้ว อย่าลืมเคลียร์คำสั่งทิ้งด้วย เดี๋ยวเปิดรัว
            $kiosk->current_command = null;
            $kiosk->save();
            return "OPEN";
        } elseif ($kiosk->status == 'IDLE') {
            return "FINISHED";
        }

        return "WAIT";
    }

    public function scanQr($kioskId)
    {
        // 1. หาตู้จาก ID
        $kiosk = Kiosk::where('kiosk_id', $kioskId)->firstOrFail();

        // 2. เช็คว่าตู้พร้อมไหม (ต้องอยู่ในสถานะ WAITING_SCAN เท่านั้น)
        if ($kiosk->status !== 'WAITING_SCAN') {
            return redirect()->back()->with('error', 'ตู้นี้ยังไม่พร้อม หรือมีคนใช้งานอยู่');
        }

        // 3. จับคู่ User กับตู้ (Pairing)
        $kiosk->update([
            'status' => 'PAIRED', // 🔥 ค่านี้แหละที่ NodeMCU รออยู่!
            'current_user_id' => Auth::id(),
            'last_active_at' => now(),
        ]);

        // 4. พา User ไปหน้ากล้อง AI ทันที
        return redirect()->route('kiosk.session', ['kioskId' => $kioskId]);
    }

    public function sessionPage($kioskId)
    {
        // โหลดหน้า View ที่มีกล้อง AI (Teachable Machine)
        return view('kiosk.ai-camera', compact('kioskId'));
    }

    public function matchKiosk(Request $request) {
    $kioskId = $request->kiosk_id;
    $userId = $request->user_id;

    // ตรวจสอบว่าเป็นสมาชิกธนาคารขยะหรือไม่
    $isMember = KpUserWastePreference::where('user_id', $userId)
                ->where('is_waste_bank', '1')
                ->exists();

    if (!$isMember) {
        return response()->json([
            'status' => 'error',
            'message' => 'คุณยังไม่ได้ลงทะเบียนสมาชิกธนาคารขยะ'
        ], 403);
    }

    // ถ้าผ่านการเช็คสิทธิ์ ก็ทำการบันทึกลง kiosk_matches ตามปกติ
    KioskMatch::create([
        'kiosk_id' => $kioskId,
        'user_id' => $userId,
        'status' => 'pending',
        'expires_at' => now()->addMinutes(5)
    ]);

    return response()->json(['status' => 'success']);
}

// ใน KioskController.php
public function checkTransactionStatus($kiosk_id)
{
    // ค้นหาตู้หรือรายการ matching ล่าสุด
    $kiosk = Kiosk::where('id', $kiosk_id)->first();

    if (!$kiosk) {
        return response()->json(['status' => 'error', 'message' => 'ไม่พบตู้'], 404);
    }

    // ถ้าตู้กลับไปเป็นสถานะ idle หรือมีรายการ Transaction ใหม่เกิดขึ้น
    // สมมติว่าเมื่อทำงานเสร็จ ESP32 จะส่งค่าน้ำหนักมา และ Server จะเปลี่ยนสถานะตู้เป็น 'idle'
    if ($kiosk->status == 'idle') {
        // ดึงข้อมูลแต้มล่าสุดมาโชว์ (ตัวอย่าง)
        return response()->json([
            'status' => 'completed',
            'points' => 10, // หรือดึงจาก table points
            'message' => 'รายการเสร็จสมบูรณ์'
        ]);
    }

    // ถ้ายังทำงานไม่เสร็จ
    return response()->json([
        'status' => 'processing',
        'message' => 'กำลังรอการชั่งน้ำหนัก...'
    ]);
}

public function checkKioskReady($kiosk_id) {
    $kiosk = Kiosk::find($kiosk_id);

    // ตรวจสอบว่าทั้ง MCU และ CAM ส่ง Heartbeat มาใน 10 วินาทีล่าสุดไหม
    $isMcuReady = $kiosk->mcu_last_active > now()->subSeconds(10);
    $isCamReady = $kiosk->cam_last_active > now()->subSeconds(10);

    return response()->json([
        'ready' => ($isMcuReady && $isCamReady),
        'details' => [
            'mcu' => $isMcuReady,
            'cam' => $isCamReady
        ]
    ]);
}
}
