<?php

namespace App\Http\Controllers\Inventory;
use App\Http\Controllers\Controller;

use App\Models\Admin\Organization;
use App\Models\Admin\Staff;
use Illuminate\Http\Request;
use App\Models\InvItem;
use App\Models\InvItemDetail;
use App\Models\InvTransaction;
use App\Models\User;
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
            $requesters = User::Role(['Tabwater Staff', 'Tabwater Header'])
                ->select('id','firstname', 'lastname', 'email') // ดึง email หรือตำแหน่งมาช่วยระบุตัวตน
                ->orderBy('firstname')
                ->get();
        } else {
            // กรณีมหาลัย: ดึง User ที่อยู่ zone_id เดียวกัน
            $requesters = User::Role(['Tabwater Staff', 'Tabwater Header'])
                ->select('id','firstname', 'lastname', 'email') // ดึง email หรือตำแหน่งมาช่วยระบุตัวตน
                ->orderBy('firstname')
                ->get();
        }



        return view('inventory.inv_withdraw_form', compact('item', 'active_bottles', 'requesters'));
    }


public function createMultipleWithdraw()
{
    // ดึงพัสดุทั้งหมดที่มี total_stock มากกว่า 0
    $items = InvItem::with('details') // โหลดความสัมพันธ์เผื่อใช้แสดงผล
                    ->get()
                    ->filter(function ($item) {
                        return $item->total_stock > 0; // กรองเอาเฉพาะตัวที่มีของเหลือ
                    });

    // ดึงรายชื่อบุคลากรที่มีสิทธิ์เบิก
    $requesters = User::role(['Tabwater Staff', 'Tabwater Header'])
                    ->select('id', 'firstname', 'lastname', 'email')
                    ->orderBy('firstname')
                    ->get();

    return view('inventory.withdraw.withdraw_cart', compact('items', 'requesters'));
}
public function storeMultipleWithdraw(Request $request)
{
    $request->validate([
        'items' => 'required|array|min:1',
        'items.*.item_id' => 'required|exists:inv_items,id',
        'items.*.qty' => 'required|numeric|min:1',
        'purpose' => 'required|string',
        'requester_name' => 'required',
    ]);

    DB::beginTransaction();
    try {
        // สร้างเลขที่ใบเบิกกลาง (Ref No) สำหรับการเบิกชุดนี้
        $date = now()->format('Ymd');
        $count = InvTransaction::whereDate('created_at', today())->count() + 1;
        $refNo = 'WD-MULTI-' . $date . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
        
        $firstTransactionId = null;

        // 2. วนลูปทีละชนิดพัสดุที่ผู้ใช้เลือกมาในตะกร้า
        foreach ($request->items as $cartItem) {
            $itemId = $cartItem['item_id'];
            $requestedQty = $cartItem['qty'];

            // ดึงล็อตสินค้าของ Item นี้ (เรียงตาม FIFO หรือวันหมดอายุตามที่คุณต้องการ)
            $availableBottles = InvItemDetail::where('inv_item_id_fk', $itemId)
                ->where('current_qty', '>', 0)
                ->orderBy('expire_date', 'asc') // เรียงจากล็อตหมดอายุก่อน
                ->get();

            $totalStock = $availableBottles->sum('current_qty');
            if ($requestedQty > $totalStock) {
                throw new \Exception("พัสดุบางรายการมีจำนวนไม่พอจ่าย (เกินสต็อกคงเหลือ)");
            }

            $remainingQtyToSubtract = $requestedQty;

            // ตัดสต็อกตามล็อตย่อยของสินค้านั้นๆ
            foreach ($availableBottles as $bottle) {
                if ($remainingQtyToSubtract <= 0) break;

                $qtyToDeduct = min($remainingQtyToSubtract, $bottle->current_qty);

                // บันทึก Log Transaction
                $transaction = InvTransaction::create([
                    'org_id_fk' => Auth::user()->org_id_fk ?? 1,
                    'user_id_fk' => Auth::id(),
                    'inv_item_id_fk' => $itemId,
                    'inv_item_detail_id_fk' => $bottle->id,
                    'quantity' => $qtyToDeduct,
                    'purpose' => $request->purpose,
                    'ref_no' => $refNo,
                    'status' => 'PENDING',
                    'requester_name' => $request->requester_name,
                    'transaction_date' => now()
                ]);

                if (!$firstTransactionId) {
                    $firstTransactionId = $transaction->id;
                }

                // ตัดสต็อกจริง
                $bottle->current_qty -= $qtyToDeduct;
                if ($bottle->current_qty <= 0) {
                    $bottle->current_qty = 0;
                    $bottle->status = 'EMPTY';
                }
                $bottle->save();

                $remainingQtyToSubtract -= $qtyToDeduct;
            }
        }

        DB::commit();

        // ✅ เปลี่ยน Redirect ไปหน้าแสดงใบเบิก (Print View) โดยอ้างอิงจาก ref_no หรือ id แรก
        return redirect()->route('inventory.withdraw.show_ref', $refNo)
            ->with('success', 'บันทึกการเบิกพัสดุหลายรายการสำเร็จ');
        // return redirect()->route('inventory.withdraw.index')
        //     ->with('success', 'บันทึกการเบิกพัสดุหลายรายการสำเร็จ (Ref: ' . $refNo . ')');

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->withErrors(['error' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()])->withInput();
    }
}
public function showByRef($refNo)
{
    // ดึงรายการเบิกทั้งหมดที่มี ref_no เดียวกัน
    $transactions = InvTransaction::with(['item', 'detail', 'user'])->where('ref_no', $refNo)->get();
    $approver = User::Role('Tabwater Header')->get(['id', 'firstname', 'lastname'])->first();
    if ($transactions->isEmpty()) {
        abort(404);
    }

    // ใช้ตัวแปรแรกสำหรับข้อมูลหัวกระดาษ (วันที่, ผู้เบิก, สถานะ)
    $transaction = $transactions->first();

    return view('inventory.withdraw.withdraw_print', compact('transactions', 'transaction', 'approver'));
}

    // 2. บันทึกการเบิก และ ตัดสต็อก
    public function storeWithdraw(Request $request)
{
    // 1. Validation ข้อมูลขาเข้า (เปลี่ยนจาก detail_id เป็น detail_ids และต้องเป็น Array)
    $request->validate([
        'detail_ids' => 'required|array|min:1',
        'detail_ids.*' => 'exists:inv_item_details,id',
        'withdraw_qty' => 'required|numeric|min:1',
        'purpose' => 'required|string',
        'requester_name' => 'required',
        // 'approver_name' => 'required|string',
    ]);

    // 2. คำนวณผลรวมสต็อกทั้งหมดจากขวดที่เลือก เพื่อเช็คว่ายอดเบิกเกินจริงไหม
    $selectedDetails = InvItemDetail::whereIn('id', $request->detail_ids)->get();
    $totalAvailableQty = $selectedDetails->sum('current_qty');

    if ($request->withdraw_qty > $totalAvailableQty) {
        return back()->withErrors(['withdraw_qty' => 'ยอดที่เบิกเกินกว่าจำนวนคงเหลือรวมของรายการที่เลือก!'])->withInput();
    }

    // ใช้ Database Transaction เพื่อความปลอดภัย (ถ้าพลาดต้อง rollback ทั้งหมด)
    DB::beginTransaction();
    try {
        // สร้างเลขที่ใบเบิก (Ex: WD-20260925-0001)
        $date = now()->format('Ymd');
        $count = InvTransaction::whereDate('created_at', today())->count() + 1;
        $refNo = 'WD-' . $date . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);

        $remainingWithdrawQty = $request->withdraw_qty;
        $mainTransactionId = null;

        // 3. วนลูปตัดสต็อกทีละขวดตามลำดับที่เลือก (FIFO หรือตามที่ติ๊ก)
        foreach ($selectedDetails as $detail) {
            if ($remainingWithdrawQty <= 0) {
                break; // ถ้าตัดครบตามจำนวนที่ขอเบิกแล้ว หยุดวนลูป
            }

            // คำนวณจำนวนที่จะตัดออกจากขวดนี้
            $qtyToSubtract = min($remainingWithdrawQty, $detail->current_qty);

            // บันทึก Log การเบิกของแต่ละขวด
            $transaction = InvTransaction::create([
                'org_id_fk' => Auth::user()->org_id_fk,
                'user_id_fk' => Auth::id(),
                'inv_item_id_fk' => $detail->inv_item_id_fk,
                'inv_item_detail_id_fk' => $detail->id, // บันทึก ID ของขวดนั้นๆ
                'quantity' => $qtyToSubtract,         // จำนวนที่ตัดจากขวดนี้
                'purpose' => $request->purpose,
                'ref_no' => $refNo,
                'status' => 'PENDING',
                'requester_name' => $request->requester_name,
                'approver_name' => $request->approver_name,
                'transaction_date' => now()
            ]);

            // เก็บ ID แรกไว้สำหรับ Redirect ไปหน้า Show
            if (!$mainTransactionId) {
                $mainTransactionId = $transaction->id;
            }

            // ตัดสต็อกจริงของขวดนั้น
            $detail->current_qty -= $qtyToSubtract;
            if ($detail->current_qty <= 0) {
                $detail->current_qty = 0;
                $detail->status = 'EMPTY';
            }
            $detail->save();

            // หักลบจำนวนที่เหลือที่ต้องตัดต่อ
            $remainingWithdrawQty -= $qtyToSubtract;
        }

        DB::commit();

        // ส่งไปหน้า Preview ใบเบิก
        return redirect()->route('inventory.withdraw.show', $mainTransactionId)
            ->with('success', 'บันทึกคำขอเบิกเรียบร้อย รอการอนุมัติ');

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->withErrors(['error' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $e->getMessage()])->withInput();
    }
}

    public function show($id)
    {
        $transaction = InvTransaction::with(['item', 'user', 'detail'])->findOrFail($id);
        return view('inventory.withdraw.inv_withdraw_slip', compact('transaction'));
    }

// 1. ฟังก์ชันกดอนุมัติ (E-Approval) -> เปลี่ยนสถานะเป็น APPROVED แต่ยังไม่ตัดสต็อก
public function approve($refNo)
{
    $transactions = InvTransaction::where('ref_no', $refNo)->get();

    if ($transactions->isEmpty()) {
        return back()->with('error', 'ไม่พบรายการเบิกพัสดุที่ต้องการอนุมัติ');
    }

    foreach ($transactions as $trans) {
        if ($trans->status == 'APPROVED' || $trans->status == 'DISPENSED') {
            return back()->with('error', 'ใบเบิกชุดนี้ได้รับการอนุมัติหรือเบิกจ่ายไปแล้ว');
        }
    }

    // อัปเดตสถานะทุกรายการภายใต้ ref_no นี้ให้เป็น APPROVED
    InvTransaction::where('ref_no', $refNo)->update([
        'status' => 'APPROVED',
        'approved_by' => Auth::id(), // ID ผู้อนุมัติ
        'approved_at' => now(),
    ]);

    return back()->with('success', 'อนุมัติใบเบิกเรียบร้อย (รอเจ้าหน้าที่จ่ายพัสดุ)');
}

// 1. แสดงหน้าฟอร์มให้เจ้าหน้าที่ตรวจสอบ/แก้ไขจำนวนก่อนจ่ายจริง
public function dispenseForm($refNo)
{
    $transactions = InvTransaction::where('ref_no', $refNo)
        ->with(['item', 'detail'])
        ->get();

    if ($transactions->isEmpty()) {
        return redirect()->route('inventory.history')->with('error', 'ไม่พบข้อมูลใบเบิกนี้');
    }

    // เช็คว่าต้องสถานะ APPROVED เท่านั้นถึงจะมาหน้านี้ได้
    foreach ($transactions as $trans) {
        if ($trans->status != 'APPROVED') {
            return redirect()->route('inventory.history')->with('error', 'ใบเบิกนี้ยังไม่ได้รับการอนุมัติ หรือถูกจ่ายไปแล้ว');
        }
    }

    return view('inventory.withdraw.withdraw_dispense', compact('transactions', 'refNo'));
}

// 2. ประมวลผลการตัดสต็อกจริง (รองรับการแก้ไขจำนวน และยกเลิกบางรายการ)
public function dispenseProcess(Request $request, $refNo)
{
    $itemsData = $request->input('items', []);

    DB::beginTransaction();
    try {
        foreach ($itemsData as $data) {
            $trans = InvTransaction::with(['item', 'detail'])->find($data['id']);
            if (!$trans) continue;

            // กรณีเจ้าหน้าที่ติ๊กยกเลิกรายการนี้
            if (isset($data['cancel']) && $data['cancel'] == '1') {
                $trans->update([
                    'status' => 'CANCELLED', // หรือสถานะยกเลิกตามระบบของคุณ
                    'quantity' => 0,
                ]);
                continue;
            }

            $dispensedQty = intval($data['dispensed_qty']);
            $requestedQty = intval($data['requested_qty']);

            // ตรวจสอบว่าจำนวนจ่ายจริงต้องไม่มากกว่าจำนวนที่ขอเบิก
            if ($dispensedQty > $requestedQty) {
                DB::rollBack();
                return back()->with('error', 'จำนวนจ่ายจริงของพัสดุ "' . $trans->item->name . '" ต้องไม่มากกว่าจำนวนที่ขอเบิก');
            }

            // ถ้าจำนวนจ่ายจริงเป็น 0 ให้ถือว่ายกเลิกรายการ
            if ($dispensedQty <= 0) {
                $trans->update([
                    'status' => 'CANCELLED',
                    'quantity' => 0,
                ]);
                continue;
            }

            // ตรวจสอบสต็อกคงเหลือในคลัง (current_qty)
            if ($trans->inv_item_detail_id_fk) {
                $detail = InvItemDetail::find($trans->inv_item_detail_id_fk);
                
                if (!$detail || $detail->current_qty < $dispensedQty) {
                    DB::rollBack();
                    $itemName = $trans->item->name ?? 'ไม่ระบุ';
                    $remain = $detail->current_qty ?? 0;
                    return back()->with('error', "สต็อกของพัสดุ \"{$itemName}\" ไม่เพียงพอ (ต้องการจ่าย: {$dispensedQty}, คงเหลือในคลัง: {$remain})");
                }

                // ตัดสต็อกจริงตามจำนวนที่เจ้าหน้าที่ระบุ
                $detail->current_qty = $detail->current_qty - $dispensedQty;
                $detail->save();
            }

            // อัปเดตข้อมูล Transaction เป็นจ่ายแล้ว และบันทึกจำนวนที่จ่ายจริง
            $trans->update([
                'quantity' => $dispensedQty, // อัปเดตจำนวนสุทธิที่จ่ายจริง
                'status' => 'DISPENSED',
                'dispensed_by' => Auth::id(),
                'dispensed_at' => now(),
            ]);
        }

        DB::commit();
        return redirect()->route('inventory.history')->with('success', 'บันทึกการจ่ายพัสดุและตัดสต็อกเรียบร้อยแล้ว');

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
    }
}

// 2. ฟังก์ชันเจ้าหน้าที่พัสดุกดจ่ายพัสดุจริง -> ทำการตัดสต็อก (current_qty) ตรงนี้
public function dispense($refNo)
{
    $transactions = InvTransaction::where('ref_no', $refNo)
        ->with(['item', 'detail'])
        ->get();

    if ($transactions->isEmpty()) {
        return back()->with('error', 'ไม่พบรายการเบิกพัสดุ');
    }

    // ตรวจสอบสถานะว่าต้องผ่านการ Approve มาก่อนแล้วเท่านั้น
    foreach ($transactions as $trans) {
        if ($trans->status != 'APPROVED') {
            return back()->with('error', 'ใบเบิกนี้ยังไม่ได้รับการอนุมัติ ไม่สามารถจ่ายพัสดุได้');
        }
    }

    DB::beginTransaction();
    try {
        foreach ($transactions as $trans) {
            // ตรวจสอบและตัดสต็อกจริงจาก current_qty
            if ($trans->inv_item_detail_id_fk) {
                $detail = InvItemDetail::find($trans->inv_item_detail_id_fk);
                
                if (!$detail || $detail->current_qty < $trans->quantity) {
                    DB::rollBack();
                    $itemName = $trans->item->name ?? 'ไม่ระบุ';
                    $remain = $detail->current_qty ?? 0;
                    return back()->with('error', "สต็อกของพัสดุ \"{$itemName}\" ไม่เพียงพอสำหรับการจ่าย (คงเหลือ: {$remain})");
                }

                // ตัดสต็อกจริง
                $detail->current_qty = $detail->current_qty - $trans->quantity;
                $detail->save();
            }

            // เปลี่ยนสถานะเป็นจ่ายพัสดุแล้ว (DISPENSED หรือ COMPLETED)
            $trans->update([
                'status' => 'DISPENSED', 
                'dispensed_by' => Auth::id(), // (ถ้าต้องการเก็บบันทึกว่าใครเป็นคนจ่ายของ)
                'dispensed_at' => now(),
            ]);
        }

        DB::commit();
        return back()->with('success', 'จ่ายพัสดุและตัดสต็อกสำเร็จเรียบร้อยแล้ว');

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'เกิดข้อผิดพลาดในการตัดสต็อก: ' . $e->getMessage());
    }
}

public function cancel($refNo)
{
    $transactions = InvTransaction::where('ref_no', $refNo)->get();

    if ($transactions->isEmpty()) {
        return back()->with('error', 'ไม่พบรายการใบเบิกนี้');
    }

    foreach ($transactions as $trans) {
        // ล็อกให้ยกเลิกได้เฉพาะตอน PENDING เท่านั้น
        if ($trans->status != 'PENDING') {
            return back()->with('error', 'ไม่สามารถยกเลิกได้ เนื่องจากใบเบิกผ่านการอนุมัติแล้ว กรุณาติดต่อเจ้าหน้าที่เพื่อดำเนินการ');
        }
    }

    InvTransaction::where('ref_no', $refNo)->update([
        'status' => 'CANCELED',
    ]);

    return back()->with('success', 'ยกเลิกใบเบิกเรียบร้อยแล้ว');
}

// ให้ Admin หรือ Approver กดยกเลิกใบเบิกที่อยู่ในสถานะ APPROVED ได้
public function adminCancel($refNo)
{
    $transactions = InvTransaction::where('ref_no', $refNo)->get();

    if ($transactions->isEmpty()) {
        return back()->with('error', 'ไม่พบรายการใบเบิกนี้');
    }

    foreach ($transactions as $trans) {
        if (!in_array($trans->status, ['PENDING', 'APPROVED'])) {
            return back()->with('error', 'ไม่สามารถยกเลิกรายการนี้ได้ เนื่องจากพัสดุถูกจ่ายออกไปแล้ว');
        }
    }

    InvTransaction::where('ref_no', $refNo)->update([
        'status' => 'CANCELED',
        // 'cancelled_by' => Auth::id(), // (ถ้าต้องการเก็บว่าใครเป็นคนกดยกเลิกให้)
    ]);

    return back()->with('success', 'ยกเลิกใบเบิก (โดยผู้ดูแลระบบ/ผู้ออนุมัติ) เรียบร้อยแล้ว');
}

    public function history(Request $request)
{
    $user = Auth::user();

    // 1. ดึงเฉพาะ id แรกของแต่ละ ref_no ที่ไม่ซ้ำกัน เพื่อใช้ทำ Pagination
    $subQuery = InvTransaction::where('org_id_fk', $user->org_id_fk);

    // ➕ กรองตามสถานะ (รับค่ามาจาก Dashboard เช่น PENDING, APPROVED, REJECTED)
    if ($request->has('status') && $request->status != '') {
        $subQuery->where('status', $request->status);
    }

    // Filter ค้นหา (ใส่เงื่อนไขใน Subquery ด้วยเพื่อให้ Pagination นับจำนวนถูกต้องตามการค้นหา)
    if ($request->has('search') && $request->search != '') {
        $searchTerm = $request->search;
        $subQuery->where(function($q) use ($searchTerm) {
            $q->where('ref_no', 'like', '%' . $searchTerm . '%')
              ->orWhere('requester_name', 'like', '%' . $searchTerm . '%')
              ->orWhereHas('item', function ($itemQuery) use ($searchTerm) {
                  $itemQuery->where('name', 'like', '%' . $searchTerm . '%')
                            ->orWhere('code', 'like', '%' . $searchTerm . '%');
              });
        });
    }

    if ($request->start_date) {
        $subQuery->whereDate('transaction_date', '>=', $request->start_date);
    }
    if ($request->end_date) {
        $subQuery->whereDate('transaction_date', '<=', $request->end_date);
    }

    // จัดกลุ่มตาม ref_no (หรือ id กรณีไม่มี ref_no) แล้วเลือก id แรกสุดมาทำ Pagination
    $subQuery->selectRaw('MIN(id) as id')
             ->groupBy(DB::raw('COALESCE(ref_no, CONCAT("single-", id))'));

    // 2. นำ id ที่ได้มา Query ข้อมูลจริงพร้อม Pagination
    $transactions = InvTransaction::whereIn('id', $subQuery)
        ->with(['item', 'user', 'detail', 'approver_user'])
        ->orderBy('transaction_date', 'desc')
        ->paginate(20)
        ->withQueryString(); // 👈 สำคัญมาก! เพื่อคงค่า Query ทั้ง status, search และ date ไว้ตอนเปลี่ยนหน้า

    return view('inventory.inv_history', compact('transactions'));
}
}
