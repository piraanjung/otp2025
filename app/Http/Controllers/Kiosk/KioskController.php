<?php

namespace App\Http\Controllers\Kiosk; // 👈 เปลี่ยน Namespace เป็น Kiosk

use App\Http\Controllers\Controller;
use App\Models\KeptKaya\KpPurchaseTransaction;
use App\Models\KeptKaya\KpTbankItemsPriceAndPoint;
use App\Models\KeptKaya\KpTbankUnits;
use App\Models\Kiosk;
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

    public function userMatchKiosk(Request $request)
    {
        // 1. รับค่า kioskId จาก JS (ต้องตรงกับชื่อตัวแปรใน $.post)
        $kioskId = $request->kioskId;

        // 2. ค้นหาตู้ Kiosk
        $kiosk = Kiosk::where('id', $kioskId)->first();

        // กรณีไม่เจอตู้
        if (!$kiosk) {
            return response()->json(['status' => 'error', 'message' => 'ไม่พบตู้ Kiosk นี้ในระบบ' . $kioskId], 404);
        }

        // 3. (Optional) เช็คว่าตู้ว่างไหม?
        // ถ้าสถานะไม่ใช่ idle และไม่ใช่ user คนเดิมที่เชื่อมต่ออยู่
        if ($kiosk->status != 'idle' && $kiosk->current_user_id != Auth::id()) {
            return response()->json(['status' => 'error', 'message' => 'ตู้นี้กำลังถูกใช้งานโดยผู้อื่น'], 400);
        }


        // 4. ผูก User ปัจจุบันเข้ากับตู้ (auth()->id() คือ user ที่ login ในมือถือ)
        try {
            Kiosk::where('id', $kioskId)->update([
                'status' => 'active',
                'current_user_id' => Auth::id(),
                'last_online_at' => now(),
                'updated_at' => now()
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Connected successfully',
                'kiosk_name' => $kiosk->name
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
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
                $priceConfig = $this->getPrice($itemId,2);

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

    public function getPrice($itemId, $unitId) {
    // $unitId อาจจะเป็น 'kg' หรือ 'piece'
    // $unit = KpTbankUnits::where('id', $unitId)->first();

    return KpTbankItemsPriceAndPoint::where('kp_items_idfk', $itemId)
        ->where('kp_units_idfk', $unitId)
        ->where('status', 'active')
        ->first();
}
}
