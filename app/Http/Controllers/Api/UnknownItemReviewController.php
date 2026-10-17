<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\KeptKaya\KpKioskUnknownItem;
use App\Models\KeptKaya\KpPurchaseTransaction;
use App\Models\KeptKaya\KpPurchaseTransactionDetail;
use App\Models\KeptKaya\KpTbankItems; // หรือตาราง Rate ราคาขยะของพี่
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UnknownItemReviewController extends Controller
{
    /**
     * 1. ดึงรายการสิ่งแปลกปลอมทั้งหมดที่รอการตรวจสอบ (สมานเข้าหน้า Dashboard)
     */
    public function getPendingItems()
    {
        // ดึงรายการที่ pending พร้อมข้อมูลผู้ใช้มาแสดงในตารางแอดมิน
        $items = KpKioskUnknownItem::where('status', 'pending_review')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $items
        ]);
    }

    /**
     * 2. ฟังก์ชันแอดมินกด "ส่งข้อความตักเตือน User"
     */
    public function warnUser(Request $request, $id)
    {
        $unknownItem = KpKioskUnknownItem::findOrFail($id);
        $userId = $unknownItem->user_id_fk;

        // 📝 ตัวอย่าง: พี่สามารถเขียนยัดข้อความเข้าตาราง Notifications/Messages ในระบบ PI-OS ได้ตรงนี้
        // เช่น KpUserMessage::create([...]);
        
        // อัปเดตสถานะรายการแปลกปลอมเป็นแอดมินตรวจสอบแล้วและปฏิเสธ
        $unknownItem->update(['status' => 'rejected']);

        Log::info("📢 [AIroBacT Admin] ส่งข้อความเตือนผู้ใช้ {$userId} เรื่องหย่อนสิ่งแปลกปลอม: {$unknownItem->detected_label}");

        return response()->json([
            'success' => true,
            'message' => 'ส่งข้อความตักเตือนผู้ใช้งานเรียบร้อยแล้ว'
        ]);
    }

    /**
     * 3. 🔥 [ฟังก์ชันเด็ด] แอดมินแก้ข้อมูลขยะให้ถูกต้อง และย้ายกลับเข้าธุรกรรมหลัก
     */
    public function verifyAndMoveToDetail(Request $request, $id)
    {
        // รับค่ารหัสขยะที่ถูกต้องที่แอดมินตาดีเลือกเปลี่ยน (เช่น ID ของขวด PET 600ml ที่ถูกตัว)
        $correctRateId = $request->input('correctRateId'); 
        $units = $request->input('amount_in_units', 1); // ส่วนใหญ่มาทีละ 1 ชิ้นจากตู้

        $unknownItem = KpKioskUnknownItem::findOrFail($id);
        
        // ดึงเรทราคาของขวดที่ถูกต้องจาก Database
        $dbItem = KpTbankItems::findOrFail($correctRateId);

        // คำนวณแต้มและเงินคืนตามจริงของไอเทมชิ้นนั้น
        $calculatedPoints = ($dbItem->point ?? 0) * $units;
        $calculatedAmount = ($dbItem->price ?? 0) * $units;

        // 🔥 รันระบบธุรกรรมป้องกันข้อมูลหลุดครึ่งๆ กลางๆ
        DB::transaction(function () use ($unknownItem, $correctRateId, $units, $calculatedPoints, $calculatedAmount, $dbItem) {
            
            // 1. ดึง Header ใบเสร็จตัวเดิมที่เคยบันทึกไปก่อนหน้านี้ออกมา
            $transaction = KpPurchaseTransaction::findOrFail($unknownItem->kp_purchase_trans_id);

            // 2. ยัดไอเทมชิ้นนี้เข้าตาราง Details หลักตามโครงสร้างของพี่
            KpPurchaseTransactionDetail::create([
                'org_id_fk'                    => $unknownItem->org_id_fk,
                'kp_purchase_trans_id'         => $transaction->id,
                'kp_recycle_item_id'           => $dbItem->id, // แมตช์เข้า ID กลุ่มขวดที่ถูก
                'kp_tbank_items_pricepoint_id' => $correctRateId,
                'recorder_id'                  => Auth::id() ?? null, // แอดมินผู้แก้ข้อมูล
                'amount_in_units'              => $units,
                'kp_units_idfk'                => $dbItem->kp_units_idfk ?? 1,
                'price_per_unit'               => $dbItem->price ?? 0,
                'amount'                       => $calculatedAmount,
                'points'                       => $calculatedPoints,
                'carbon_saved'                 => ($dbItem->carbon_saved ?? 0) * $units,
                'image_path'                   => $unknownItem->image_path // ส่งพาร์ทรูปภาพขยะตามไปด้วย
            ]);

            // 3. 🎯 อัปเดตบวกแต้มและเงินคืนสะสมเพิ่มเข้าไปในบิลหลัก (Header) ของ User คนนั้น
            $transaction->increment('total_points', $calculatedPoints);
            $transaction->increment('total_amount', $calculatedAmount);

            // 4. ปรับสถานะของ Unknown Item ชิ้นนี้เป็นตรวจสอบและแก้ไขสำเร็จ (verified)
            $unknownItem->update(['status' => 'verified']);
        });

        return response()->json([
            'success' => true,
            'message' => 'แก้ไขข้อมูลขยะเข้าบิลหลัก และคำนวณสะสมแต้มให้ผู้ใช้ใหม่สำเร็จแล้วครับพี่!'
        ]);
    }
}