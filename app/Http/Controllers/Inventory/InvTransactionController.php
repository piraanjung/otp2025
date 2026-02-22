<?php

namespace App\Http\Controllers\Inventory;
use App\Http\Controllers\Controller;

use App\Models\Admin\Organization;
use Illuminate\Http\Request;
use App\Models\InvItem;
use App\Models\InvItemDetail;
use App\Models\InvTransaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InvTransactionController extends Controller
{
    // 1. หน้าฟอร์มเบิก (แสดงรายชื่อขวดที่มีของ)
    public function withdrawForm($item_id)
    {
        $user = Auth::user();
        $item = InvItem::findOrFail($item_id);

        // ดึงเฉพาะขวดที่สถานะ ACTIVE (ยังมีของ) และเรียงตามวันหมดอายุ (FIFO: First Expire First Out)
        // นี่คือ Logic สำคัญของห้องแล็บครับ ของใกล้หมดอายุต้องโชว์ก่อน
        $active_bottles = InvItemDetail::where('inv_item_id_fk', $item_id)
            ->where('status', 'ACTIVE')
            ->where('current_qty', '>', 0)
            ->orderBy('expire_date', 'asc') // เรียงวันหมดอายุมาก่อน
            ->orderBy('received_date', 'asc')
            ->get();

        // --- ส่วนที่เพิ่ม: เตรียมรายชื่อผู้เบิก ---
        $requesters = collect(); // สร้าง Collection ว่างไว้ก่อน
        // คุณอาจจะเช็คจาก $user->organization_type หรือ Config ก็ได้
        $isMunicipality = Organization::find(Auth::user()->org_id_fk)['org_short_type_name'] == 'ม.' ? 1 : 0; // ** ปรับ logic ตรงนี้ตามจริง **
        if ($isMunicipality) {
            // กรณีเทศบาล: ดึงจาก table staffs
            $requesters = DB::table('staffs as s')
                ->join('users as u', 'u.id', '=', 's.user_id')
                ->select('u.firstname', 'u.email', 'u.id', 's.user_id') // ดึงตำแหน่งมาโชว์ด้วยถ้ามี
                ->orderBy('u.firstname')
                ->get();
        } else {
            // กรณีมหาลัย: ดึง User ที่อยู่ zone_id เดียวกัน
            $requesters = \App\Models\User::where('zone_id', $user->zone_id)
                ->select('firstname', 'email') // ดึง email หรือตำแหน่งมาช่วยระบุตัวตน
                ->orderBy('firstname')
                ->get();
        }



        return view('inventory.inv_withdraw_form', compact('item', 'active_bottles', 'requesters'));
    }

    // 2. บันทึกการเบิก และ ตัดสต็อก
    public function storeWithdraw(Request $request)
    {
        $request->validate([
            'detail_id' => 'required|exists:inv_item_details,id',
            'withdraw_qty' => 'required|numeric|min:0.01',
            'purpose' => 'required|string',
            // ✅ เพิ่ม validation
            'requester_name' => 'required|string',
            'approver_name' => 'required|string',
        ]);


        $detail = InvItemDetail::findOrFail($request->detail_id);

        // Validation: ห้ามเบิกเกินที่มีในขวด
        if ($request->withdraw_qty > $detail->current_qty) {
            return back()->withErrors(['withdraw_qty' => 'ยอดที่เบิกเกินกว่าจำนวนคงเหลือในขวดนี้!']);
        }

        // 1. สร้าง Transaction Log
        // สร้างเลขที่ใบเบิก (Ex: WD-20231025-0001)
        $date = now()->format('Ymd');
        $count = InvTransaction::whereDate('created_at', today())->count() + 1;
        $refNo = 'WD-' . $date . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
        $transaction = InvTransaction::create([
            'org_id_fk' => Auth::user()->org_id_fk,
            'user_id_fk' => Auth::id(), // คนคีย์ข้อมูล (Log user system)
            'inv_item_id_fk' => $detail->inv_item_id_fk,
            'inv_item_detail_id_fk' => $detail->id,
            'quantity' => $request->withdraw_qty,
            'purpose' => $request->purpose,

            'ref_no' => $refNo,
            'status' => 'PENDING', // รออนุมัติ
            'requester_name' => $request->requester_name,     // ✅ บันทึกชื่อผู้เบิกและผู้อนุมัติ
            'approver_name' => $request->approver_name,

            'transaction_date' => now()
        ]);

        // 2. ตัดสต็อกจริง (Logic สำคัญ)
        $detail->current_qty = $detail->current_qty - $request->withdraw_qty;

        // ถ้าเหลือ 0 ให้เปลี่ยนสถานะเป็น EMPTY
        if ($detail->current_qty <= 0) {
            $detail->current_qty = 0;
            $detail->status = 'EMPTY';
        }

        $detail->save();

        // ส่งไปหน้า Preview ใบเบิก หรือ กลับไปหน้าประวัติ
        return redirect()->route('inventory.withdraw.show', $transaction->id)
            ->with('success', 'บันทึกคำขอเบิกเรียบร้อย รอการอนุมัติ');
    }

    public function show($id)
    {
        $transaction = InvTransaction::with(['item', 'user', 'detail'])->findOrFail($id);
        return view('inventory.withdraw.inv_withdraw_slip', compact('transaction'));
    }

    // 3. ฟังก์ชันกดอนุมัติ (E-Approval) -> ตัดสต็อกจริงตรงนี้
    public function approve($id)
    {
        $trans = InvTransaction::findOrFail($id);

        if ($trans->status == 'APPROVED') {
            return back()->with('error', 'รายการนี้ถูกอนุมัติไปแล้ว');
        }

        // ตัดสต็อกจริง
        $detail = InvItemDetail::find($trans->inv_item_detail_id_fk);
        if ($detail->quantity < $trans->quantity) {
            return back()->with('error', 'สต็อกไม่พอสำหรับการอนุมัติ');
        }

        $detail->decrement('quantity', $trans->quantity); // ตัดของ

        // อัปเดตสถานะ Transaction
        $trans->update([
            'status' => 'APPROVED',
            'approved_by' => Auth::id(), // เก็บ ID คนกดปุ่ม
            'approved_at' => now(),
        ]);

        return back()->with('success', 'อนุมัติและตัดสต็อกเรียบร้อย');
    }

    public function history(Request $request)
    {
        $user = Auth::user();

        // เริ่มต้น Query
        $query = InvTransaction::where('org_id_fk', $user->org_id_fk)
            ->with(['item', 'user', 'detail']) // ดึงข้อมูลพัสดุ, ผู้เบิก, รายละเอียดขวด
            ->orderBy('transaction_date', 'desc'); // เรียงจากล่าสุดไปเก่าสุด

        // --- Logic การค้นหา (Filter) ---

        // 1. ค้นหาจากชื่อพัสดุ
        if ($request->has('search') && $request->search != '') {
            $query->whereHas('item', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('code', 'like', '%' . $request->search . '%');
            });
        }

        // 2. กรองตามวันที่ (ถ้ามี)
        if ($request->start_date) {
            $query->whereDate('transaction_date', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query->whereDate('transaction_date', '<=', $request->end_date);
        }

        // ดึงข้อมูล (หน้าละ 20 รายการ)
        $transactions = $query->paginate(20)->withQueryString();

        return view('inventory.inv_history', compact('transactions'));
    }
}
