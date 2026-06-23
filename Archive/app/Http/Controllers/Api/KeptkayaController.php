<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Models\KeptKaya\KpPurchaseTransaction;
use App\Models\KeptKaya\KpPurchaseTransactionDetail;
use App\Models\KeptKaya\KpTbankItems;
use App\Models\RecycleBankAccount;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class KeptkayaController extends Controller
{
    public function kp_items_recycle_info(Request $request)
    {
        try {
            // วันที่ปัจจุบัน เอาไว้เช็กช่วงเวลาของราคาที่มีผล (Effective Date)
            $today = Carbon::today()->toDateString();

            // 🟢 เริ่มต้นคิวรีจากตารางหลัก kp_tbank_items
            // (Note: เนื่องจากมี Trait BelongsToOrganization ระบบจะกรอง org_id_fk ให้พี่อัตโนมัติอยู่แล้วครับ สบายใจได้)
            $items = KpTbankItems::join('kp_tbank_items_groups as grp', 'kp_tbank_items.kp_items_group_idfk', '=', 'grp.id')
                // พี่เช็กดูตามความเหมาะสมนะครับว่าใช้ unit_kiosk_idfk หรือ unit_bank_idfk
                ->join('kp_tbank_items_units as unt', 'kp_tbank_items.unit_bank_idfk', '=', 'unt.id')
                ->join('kp_tbank_items_pricepoint as prc', 'kp_tbank_items.id', '=', 'prc.kp_items_idfk')

                // 🟢 กรองสถานะที่เปิดใช้งานอยู่ (ใน Log ฟ้องว่าหลังบ้านของพี่ใช้คำว่า 'active' แทนตัวเลข 1 ครับ)
                ->where('kp_tbank_items.status', 'active')
                ->where('grp.status', 'active')
                ->where('prc.status', 'active')

                // กรองช่วงเวลาวันที่ของราคารีไซเคิลปัจจุบันให้ถูกต้อง
                ->where('prc.effective_date', '<=', $today)
                ->where(function ($query) use ($today) {
                    $query->whereNull('prc.end_date')->orWhere('prc.end_date', '>=', $today);
                })

                // 🟢 ดึงชื่อฟิลด์ไปแมปปิ้งกับหน้า JavaScript (app.js) ของพี่
                ->select([
                    'kp_tbank_items.id as id',
                    'kp_tbank_items.kp_itemscode as kp_itemscode',
                    'kp_tbank_items.kp_itemsname as kp_itemsname',
                    'kp_tbank_items.kp_items_group_idfk as group_id',
                    'grp.kp_items_groupname as group_name',
                    'unt.unitname as unitname',
                    'prc.price_for_member as price',
                    'prc.price_from_dealer as price_dealer',
                    'prc.point as point'
                ])
                ->get();

            // 🟢 ส่งข้อมูลกลับไปหาฝั่ง JavaScript ในรูปแบบ JSON พร้อม Code 200
            return response()->json($items, 200);
        } catch (\Exception $e) {
            // หากระบบฐานข้อมูลหลังบ้านพัง ให้พ่น Log บอกและส่งสถานะ 500 กลับไป
            Log::error('Error in kp_items_recycle_info: ' . $e->getMessage());

            return response()->json([
                'code' => 500,
                'message' => 'เกิดข้อผิดพลาดภายในเซิร์ฟเวอร์: ' . $e->getMessage()
            ], 500);
        }
    }

    public function members(Request $request)
    {
        try {
            $today = Carbon::today()->toDateString(); // วันที่ปัจจุบัน (YYYY-MM-DD)

            // ดึงรายชื่อ User ทั้งหมด (ระบบจะกรอง org_id_fk อัตโนมัติด้วย Trait BelongsToOrganization)
            $members = User::with('wastePreference') // ดึงข้อมูล preference พ่วงไปด้วยเพื่อเอา ID คีย์นอก
                ->whereHas('wastePreference')
                ->get()
                ->map(function ($member) use ($today) {

                    $hasTransactionToday = false;

                    // 🟢 เช็กว่าสมาชิกคนนี้มีข้อมูล Preference (สิทธิ์จัดการขยะ) ในตารางย่อยไหม
                    if ($member->wastePreference) {
                        // ดึง ID ของฝั่ง Preference เพื่อเอาไปใช้ค้นหาในตารางบิลธุรกรรม
                        $prefId = $member->wastePreference->id;

                        // 🟢 ชี้เป้า: ตรวจสอบในตารางธุรกรรมว่า วันนี้ มีคีย์ของสมาชิกคนนี้ทำรายการไปแล้วหรือยัง
                        $hasTransactionToday = DB::table('kp_purchase_transactions')
                            ->where('kp_user_w_pref_id_fk', $prefId)
                            ->whereDate('transaction_date', $today)
                            ->exists();
                    }

                    // 🟢 กำหนดสถานะส่งกลับไปที่แอป (pending = รอคิว / completed = บันทึกฝากขยะแล้ว)
                    $member->status = $hasTransactionToday ? 'completed' : 'pending';

                    return $member;
                });

            // ส่งข้อมูลกลับไปรูปแบบที่ตกลงกัน { code: 200, data: [...] }
            return response()->json([
                'code' => 200,
                'data' => $members
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => 'เกิดข้อผิดพลาดในการโหลดข้อมูลสมาชิก: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store_purchase(Request $request)
    {
        // 🟢 เปิด Transaction ป้องกันข้อมูลบันทึกครึ่งๆ กลางๆ
        DB::beginTransaction();
        try {
            // 1. เจนเลขที่เอกสารอัตโนมัติ (ตัวอย่าง: REC-ปีเดือนวัน-รันนิ่ง)
            $dateSlug = Carbon::now()->format('Ymd');
            $lastTrans = KpPurchaseTransaction::whereDate('transaction_date', Carbon::today())->count();
            $runningNum = str_pad($lastTrans + 1, 4, '0', STR_PAD_LEFT);
            $transNo = "REC-" . $dateSlug . "-" . $runningNum;

            // 2. 🟢 บันทึกตารางหลัก (Master: kp_purchase_transactions)
            $transaction = new KpPurchaseTransaction();
            $transaction->org_id_fk             = $request->org_id_fk;
            $transaction->kp_u_trans_no         = $transNo;
            $transaction->kiosk_id_fk           = $request->kiosk_id_fk ?? null;
            $transaction->kp_user_w_pref_id_fk  = $request->kp_user_w_pref_id_fk; // ไอดี Preference ของสมาชิก
            $transaction->transaction_date      = Carbon::now();
            $transaction->total_weight          = collect($request->cart_items)->sum('amount_in_units');
            $transaction->total_amount          = collect($request->cart_items)->sum('amount');
            $transaction->total_points          = collect($request->cart_items)->sum('points');
            $transaction->status                = 'completed';
            $transaction->recorder_id           = $request->recorder_id; // ไอดีสตาฟฟ์ผู้บันทึก
            $transaction->cash_back             = 1;//$request->cashback == true ? 1 : 0; // ยอดเงินสดที่จ่ายคืน
            $transaction->total_carbon_saved    = $request->total_carbon_saved ?? 0.0000;
            $transaction->save();

            $carbonSavedTotal = 0;
            // 3. 🟢 วนลูปบันทึกตารางรายการย่อย (Detail: kp_purchase_transactions_details)
            if ($request->has('cart_items') && is_array($request->cart_items)) {
                foreach ($request->cart_items as $item) {
                    $itemModel = KpTbankItems::with('emissionFactor')->find($item['kp_tbank_item_id']);

                    // เช็คชื่อคอลัมน์ EF ให้ชัวร์ (ef_value หรือ carbon_value)
                    $ef                 = $itemModel->emissionFactor->ef_value ?? 0;
                    $carbonSaved        = $item['amount_in_units'] * $ef;
                    $carbonSavedTotal   += $carbonSaved;

                    $detail = new KpPurchaseTransactionDetail();
                    $detail->org_id_fk                      = $request->org_id_fk;
                    $detail->kp_purchase_trans_id           = $transaction->id; // 👈 ผูกกับ ID บิลหลักที่เพิ่งได้มา
                    $detail->kp_recycle_item_id             = $item['kp_recycle_item_id'];
                    $detail->kp_tbank_items_pricepoint_id   = $item['kp_tbank_items_pricepoint_id'];
                    $detail->recorder_id                    = $request->recorder_id;
                    $detail->amount_in_units                = $item['amount_in_units']; // น้ำหนักที่ชั่งได้
                    $detail->kp_units_idfk                  = $item['kp_units_idfk'];
                    $detail->price_per_unit                 = $item['price_per_unit'];
                    $detail->amount                         = $item['amount'];
                    $detail->points                         = $item['points'];
                    $detail->carbon_saved                   = $item['carbon_saved'] ?? 0.0000;
                    $detail->save();
                }
            }

            // 5. อัปเดต Carbon รวม
            $transaction->update(['total_carbon_saved' => $carbonSavedTotal]);

            // 6. อัปเดตสมุดบัญชีธนาคารขยะ (RecycleBankAccount)
            $recycleAcc = RecycleBankAccount::firstOrCreate(
                ['user_pref_id', $request->kp_user_w_pref_id_fk],
                [
                    'account_no' => 'ACC-' . str_pad($request->kp_user_w_pref_id_fk, 6, '0', STR_PAD_LEFT),
                    'balance'    => 0,
                    'points'     => 0,
                    'status'     => 'active',
                    'updated_at' => date('Y-m-d H:i:s')
                ]
            );

            $recycleAcc->increment('points', collect($request->cart_items)->sum('points'));
            if ($request->cashback) {
                $recycleAcc->increment('balance', collect($request->cart_items)->sum('amount'));
            }

            Session::forget(['purchase_cart', 'purchase_user_id']);


            // ถ้าทุกอย่างผ่านฉลุยให้เซฟลงฐานข้อมูลจริง
            DB::commit();

            // 🟢 ส่งข้อมูลบิลที่สมบูรณ์กลับไปให้ฝั่ง JavaScript นำไปปริ้นออกเครื่องบลูทูธ
            return response()->json([
                'code' => 200,
                'message' => 'บันทึกข้อมูลสำเร็จ',
                'data'    => [
                    'transaction_id'    => $transaction->id,
                    'receipt_no'        => $transaction->kp_u_trans_no,
                    'transaction_date'  => $transaction->transaction_date->format('Y-m-d H:i:s'),
                    'total_amount'      => $transaction->total_amount,
                    'total_points'      => $transaction->total_points
                ]
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack(); // พังตรงไหนให้ยกเลิกทั้งหมดทันที
            return response()->json([
                'code' => 500,
                'message' => 'เกิดข้อผิดพลาดหลังบ้าน: ' . $e->getMessage()
            ], 500);
        }
    }
}
