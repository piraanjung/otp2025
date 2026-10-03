<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;

use App\Models\Admin\Organization;
use App\Models\Admin\Staff;
use App\Models\ApprovalWorkflowStep;
use App\Models\InvCategory;
use Illuminate\Http\Request;
use App\Models\InvItem;
use App\Models\InvItemDetail;
use App\Models\InvTransaction;
use App\Models\InvTransactionApprovals;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\ApprovalNotificationMail;
use App\Mail\RequestApprovedCompleteMail;

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
                ->select('id', 'firstname', 'lastname', 'email') // ดึง email หรือตำแหน่งมาช่วยระบุตัวตน
                ->orderBy('firstname')
                ->get();
        } else {
            // กรณีมหาลัย: ดึง User ที่อยู่ zone_id เดียวกัน
            $requesters = User::Role(['Tabwater Staff', 'Tabwater Header'])
                ->select('id', 'firstname', 'lastname', 'email') // ดึง email หรือตำแหน่งมาช่วยระบุตัวตน
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
        'requester_id' => 'required',
    ]);

    DB::beginTransaction();
    try {
        // สร้างเลขที่ใบเบิกกลาง (Ref No) สำหรับการเบิกชุดนี้
        $date = now()->format('Ymd');
        $count = InvTransaction::whereDate('created_at', today())->count() + 1;
        $refNo = 'WD-MULTI-' . $date . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);

        $firstApprover = null;
        $approveStep = null;
        
        // 1. สร้าง Workflow การอนุมัติ (ใช้วิธีดึงจาก Item แรกหรือเช็คตามหมวดหมู่)
        foreach ($request->items as $cartItem) {
            $item = InvItem::find($cartItem['item_id']);
            $approval_workflow = InvCategory::where('id', $item->inv_category_id_fk)
                ->with('workflow', 'workflow.steps')
                ->get()->first();

            if ($approval_workflow && $approval_workflow->workflow) {
                foreach ($approval_workflow->workflow->steps as $step) {

                $approverId = $step->specific_user_id != "" ? $step->specific_user_id : User::Role($step->role_name)->get('id')->pluck('id')[0];
                    if(!$firstApprover){
                        $firstApprover = User::find($approverId);
                        $approveStep = $step;
                    }
                    InvTransactionApprovals::create([
                        'ref_no' => $refNo,
                        'step_order' => $step->step_order,
                        'approval_workflow_id' =>$step->workflow_id,
                        'approver_id' => $approverId,
                        'status' =>  'PENDING',
                        'action_at' =>  now(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
            break; // ดึง workflow แค่รอบเดียวเป็นตัวแทนใบเบิกชุดนี้
        }

        $firstTransactionId = null;

        // 2. บันทึกรายการขอเบิก (บันทึกตามยอดที่ขอโดยยังไม่ตัดสต็อกจริง)
        foreach ($request->items as $cartItem) {
            $itemId = $cartItem['item_id'];
            $requestedQty = $cartItem['qty'];

            // (ทางเลือก) ตรวจสอบแค่ว่าสต็อกรวมพอให้เบิกไหม เพื่อเตือนตั้งแต่ตอนกดขอเบิก
            $availableItems = InvItemDetail::where('inv_item_id_fk', $itemId)
                ->where('current_qty', '>', 0)
                ->get();
            $totalStock = $availableItems->sum('current_qty');
            
            if ($requestedQty > $totalStock) {
                throw new \Exception("พัสดุบางรายการมีจำนวนในคลังไม่พอให้เบิก (สต็อกรวมไม่เพียงพอ)");
            }

            // บันทึก Transaction แบบยังไม่ตัดสต็อก (เก็บแค่จำนวนที่ขอ)
            $transaction = InvTransaction::create([
                'org_id_fk' => Auth::user()->org_id_fk ?? 1,
                'requester_id_fk' => Auth::id(),
                'inv_item_id_fk' => $itemId,
                'quantity' => $requestedQty, // ใช้จำนวนที่ขอเบิกตรงๆ
                'purpose' => $request->purpose,
                'ref_no' => $refNo,
                'status' => 'PENDING',
                'approve_workflow_id_fk' => $approval_workflow->id,
                'transaction_date' => now(),
                'current_step' => $approveStep->id ?? null
            ]);

            if (!$firstTransactionId) {
                $firstTransactionId = $transaction->id;
            }
        }

        // ส่งเมลหาผู้อนุมัติ (ถ้ามี)
        if ($firstApprover) {
            $transactions = InvTransaction::where('ref_no', $refNo)->get();
            Mail::to($firstApprover->email)->send(new ApprovalNotificationMail($transactions, $approveStep, $firstApprover));
            Mail::to("piraanj@gmail.com")->send(new ApprovalNotificationMail($transactions, $approveStep, $firstApprover));
        }
        DB::commit();

      //  เมื่อบันทึกสำเร็จ redirect ไปยังหน้าแสดงใบเบิกหรือประวัติ
        return redirect()->route('inventory.withdraw.show_ref', $refNo)
            ->with('success', 'บันทึกการเบิกพัสดุหลายรายการสำเร็จ (Ref: ' . $refNo . ')');

    } catch (\Exception $e) {
        DB::rollBack();
        // ส่งข้อความ Error กลับไปแสดงที่หน้าจอ Blade ทันที
        return redirect()->back()
            ->withInput()
            ->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
    }
}
public function showByRef($refNo)
{
    // โหลด transactions พร้อมความสัมพันธ์ที่จำเป็น รวมถึง workflow ของพัสดุแต่ละตัว
    $transactions = InvTransaction::with(['item', 'detail', 'requester', 'approve_workflow'])
        ->where('ref_no', $refNo)
        ->get();

    if ($transactions->isEmpty()) {
        abort(404);
    }

    $transaction = $transactions->first();

    // 💡 หาว่าใน ref_no นี้มี Workflow ID อะไรบ้าง (ไม่ซ้ำกัน)
    $workflowIds = $transactions->pluck('approve_workflow_id_fk')->unique();

    // ตัวแปรสำหรับส่งไป View
    $workflowGroups = [];

    if ($workflowIds->count() > 1) {
        // --- กรณีมีหลาย Workflow ปะปนกันในใบเดียว ---
        foreach ($workflowIds as $wfId) {
            // 1. กรองเฉพาะรายการพัสดุที่เป็นของ Workflow นี้
             $groupTransactions = $transactions->where('approve_workflow_id_fk', 3);

            // 2. ดึงสเต็ปการอนุมัติของ Workflow นี้ (สมมติว่าเก็บบันทึก approvals ผูกกับ ref_no และ workflow หรือดึงจากตาราง workflow steps)
            // *หมายเหตุ: ถ้าตาราง InvTransactionApprovals ของคุณเก็บ workflow_id ไว้ด้วย ให้ where('approve_workflow_id_fk', $wfId) ด้วยครับ*
            $approvals = InvTransactionApprovals::with('approver')
                ->where('ref_no', $refNo)
                ->where('approval_workflow_id', $wfId)
                ->orderBy('step_order', 'asc')
                ->get();

            $currentApproval = $approvals->where('status', 'PENDING')->first();

            $workflowGroups[] = [
                'workflow_id' => $wfId,
                'workflow_name' => $groupTransactions->first()->approve_workflow->name ?? 'ไม่ระบุสายอนุมัติ',
                'transactions' => $groupTransactions,
                'approvals' => $approvals,
                'currentApproval' => $currentApproval,
            ];
        }
    } else {
        // --- กรณี Workflow เดียวกันปกติ (แบบเดิม) ---
        $wfId = $workflowIds->first();
        $approvals = InvTransactionApprovals::with('approver')
            ->where('ref_no', $refNo)
            ->where('approval_workflow_id', $wfId)
            ->orderBy('step_order', 'asc')
            ->get();

        $currentApproval = $approvals->where('status', 'PENDING')->first();

        $workflowGroups[] = [
            'workflow_id' => $wfId,
            'workflow_name' => $transaction->workflow->name ?? 'ทั่วไป',
            'transactions' => $transactions,
            'approvals' => $approvals,
            'currentApproval' => $currentApproval,
        ];
    }
// return   $workflowGroups[1];
    // ส่ง $workflowGroups ไปแสดงผลที่หน้า Blade แทนการส่งแบบเดี่ยวๆ
    return view('inventory.withdraw.withdraw_print', compact('transaction', 'workflowGroups', 'workflowIds'));
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
    // 1. ดึงข้อมูลรายการเบิก พร้อมความสัมพันธ์ item และ workflow
    $transactions = InvTransaction::where('ref_no', $refNo)
        ->with(['item'])
        ->get();

    if ($transactions->isEmpty()) {
        return redirect()->route('inventory.history')->with('error', 'ไม่พบข้อมูลใบเบิกนี้');
    }

    foreach ($transactions as $trans) {
        if ($trans->status != 'APPROVED') {
            return redirect()->route('inventory.history')->with('error', 'ใบเบิกนี้ยังไม่ได้รับการอนุมัติ หรือถูกจ่ายไปแล้ว');
        }
    }

    // ==========================================
    // 💡 2. คำนวณแผนแนะนำการหยิบ FIFO (รองรับหลายล็อต)
    // ==========================================
    $fifoPlans = [];
    foreach ($transactions as $tx) {
        $requiredQty = $tx->quantity;
        
        $lots = InvItemDetail::where('inv_item_id_fk', $tx->inv_item_id_fk)
            ->where('current_qty', '>', 0)
            ->with('location')
            ->orderBy('expire_date', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $itemPlan = [];
        foreach ($lots as $lot) {
            if ($requiredQty <= 0) break;

            $takeQty = min($lot->current_qty, $requiredQty);
            
            $itemPlan[] = [
                'lot_number' => $lot->lot_number,
                'expire_date' => $lot->expire_date,
                'location_name' => $lot->location->name ?? 'ไม่ระบุโซน', 
                'take_qty' => $takeQty,
                'remaining_after' => $lot->current_qty - $takeQty
            ];
            
            $requiredQty -= $takeQty;
        }

        $fifoPlans[$tx->id] = [
            'item_name' => $tx->item->name ?? '-',
            'plans' => $itemPlan,
            'is_stock_enough' => ($requiredQty <= 0)
        ];
    }

    // ==========================================
    // 💡 3. จัดกลุ่มรายการตาม Workflow ID (approve_workflow_id_fk)
    // ==========================================
    $workflowIds = $transactions->pluck('approve_workflow_id_fk')->unique();
    $workflowGroups = [];

    foreach ($workflowIds as $wfId) {
        $groupTransactions = $transactions->where('approve_workflow_id_fk', $wfId);
        
        // ดึงชื่อ Workflow (ปรับโมเดลตามโครงสร้างของคุณ เช่น InvWorkflow หรือ InvCategory)
        $workflowName = optional($groupTransactions->first()->item->category->workflow)->name ?? 'สายการอนุมัติทั่วไป (Workflow #' . $wfId . ')';

        $workflowGroups[] = [
            'workflow_id' => $wfId,
            'workflow_name' => $workflowName,
            'transactions' => $groupTransactions,
        ];
    }

    $transaction = $transactions->first();

    return view('inventory.withdraw.withdraw_dispense', compact('transactions', 'refNo', 'fifoPlans', 'workflowGroups', 'transaction'));
}

    // 2. ประมวลผลการตัดสต็อกจริง (รองรับการแก้ไขจำนวน และยกเลิกบางรายการ)
    public function dispenseProcess(Request $request, $refNo)
{
    // DB::beginTransaction();
    // try {
    return $request;
        // วนลูปรับข้อมูลรายการที่ส่งมาจากฟอร์ม
        if ($request->has('items')) {
            foreach ($request->items as $itemData) {
                // ถ้ามีการกดcancel รายการนี้ ข้ามไป
                if (isset($itemData['cancel']) && $itemData['cancel'] == 1) {
                    continue;
                }

                $transactionId = $itemData['transaction_id'];
                $transaction = InvTransaction::findOrFail($transactionId);

                $totalDispensedQty = 0;

                // วนลูปเช็คว่าแต่ละล็อต เจ้าหน้าที่หยิบไปเท่าไหร่
                if (isset($itemData['lots'])) {
                    foreach ($itemData['lots'] as $lotInput) {
                        $takeQty = floatval($lotInput['qty']);
                        if ($takeQty <= 0) continue;

                        $lotId = $lotInput['lot_id'];
                        $itemDetail = InvItemDetail::findOrFail($lotId);

                        // เช็คว่าสต็อกในล็อตนั้นพอให้ตัดไหม
                        if ($itemDetail->current_qty < $takeQty) {
                            throw new \Exception("พัสดุรหัส {$transaction->item->code} ล็อต {$itemDetail->lot_number} มีสต็อกไม่พอจ่ายจริง (เหลือ {$itemDetail->current_qty})");
                        }

                        // 💡 ตัดสต็อกจริงออกจากตาราง inv_item_details
                        $itemDetail->current_qty -= $takeQty;
                        $itemDetail->save();

                        // (ถ้ามีตารางเก็บบันทึกประวัติการจ่ายแยกแต่ละล็อต เช่น inv_transaction_details สามารถบันทึกตรงนี้ได้)
                        
                        $totalDispensedQty += $takeQty;
                    }
                }

                if ($totalDispensedQty <= 0) {
                    throw new \Exception("กรุณาระบุจำนวนที่จ่ายจริงอย่างน้อย 1 ล็อต สำหรับรายการพัสดุ {$transaction->item->name}");
                }

                // อัปเดตสถานะ Transaction เป็น APPROVED หรือ DISPENSED (จ่ายแล้ว)
                $transaction->update([
                    'status' => 'COMPLETED', // หรือ APPROVED ตาม flow ของระบบคุณ
                    'quantity' => $totalDispensedQty, // อัปเดตยอดจ่ายจริง
                    'comment' => $itemData['comment'] ?? null
                ]);
            }
        }

    //     DB::commit();

    //     return redirect()->route('inventory.history')
    //         ->with('success', 'บันทึกการจ่ายพัสดุและตัดสต็อกตามล็อตหน้างานสำเร็จเรียบร้อย');

    // } catch (\Exception $e) {
    //     DB::rollBack();
    //     return redirect()->back()
    //         ->withInput()
    //         ->with('error', 'เกิดข้อผิดพลาดในการตัดสต็อก: ' . $e->getMessage());
    // }
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
            $subQuery->where(function ($q) use ($searchTerm) {
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
            ->with(['item', 'requester', 'detail', 'approver_user'])
            ->orderBy('transaction_date', 'desc')
            ->paginate(20)
            ->withQueryString(); // 👈 สำคัญมาก! เพื่อคงค่า Query ทั้ง status, search และ date ไว้ตอนเปลี่ยนหน้า

        return view('inventory.inv_history', compact('transactions'));
    }

    public function approveStep(Request $request, $refNo)
    {
        $user = Auth::user();

        // 1. หาคิว/สเต็ปปัจจุบันที่เป็น PENDING
        $currentApproval = InvTransactionApprovals::where('ref_no', $refNo)
            ->where('status', 'PENDING')
            ->where('approval_workflow_id', $request->workflow_id)
            ->orderBy('step_order', 'asc')
            ->first();

        if (!$currentApproval) {
            return back()->with('error', 'ไม่พบรายการที่ต้องอนุมัติ หรือรายการนี้ถูกดำเนินการไปแล้ว');
        }

        // 2. ตรวจสอบสิทธิ์ว่าใช่ผู้อนุมัติในสเต็ปนี้จริงไหม (หรือเป็น Admin)
        if ($currentApproval->approver_id != $user->id) {
            return back()->with('error', 'คุณไม่มีสิทธิ์อนุมัติในขั้นตอนี้');
        }

        // 3. อัปเดตสถานะสเต็ปนี้เป็น APPROVED
        $currentApproval->update([
            'status' => 'APPROVED',
            'comment' => $request->input('comment'),
            'action_at' => now(),
        ]);

        // ดึงข้อมูลรายการเบิกทั้งหมดเพื่อใช้แนบไปกับเมล
        $transactions = InvTransaction::where('ref_no', $refNo)
            ->with(['item', 'detail', 'requester'])
            ->get();

        // 4. เช็คว่ายังมีสเต็ปถัดไปอีกไหม
        $nextApproval = InvTransactionApprovals::where('ref_no', $refNo)
            ->where('step_order', '>', $currentApproval->step_order)
            ->where('status', 'PENDING')
            ->where('approval_workflow_id', $request->workflow_id)
            ->orderBy('step_order', 'asc')
            ->with('approver')
            ->first();

        if ($nextApproval) {
            // 👉 ถ้ายังมีสเต็ปถัดไป: ส่งอีเมลหาผู้อนุมัติสเต็ปถัดไป
            if ($nextApproval->approver && $nextApproval->approver->email) {
                   
                Mail::to($nextApproval->approver->email)
                    ->send(new ApprovalNotificationMail($transactions, $nextApproval, $nextApproval->approver));
            }
        } else {
            // 👉 ถ้าไม่มีสเต็ปถัดไปแล้ว (หมดแล้ว): อัปเดตสถานะภาพรวมของ Transaction เป็น APPROVED
            InvTransaction::where('ref_no', $refNo)->update(['status' => 'APPROVED']);

            // (แนะนำ) ส่งเมลแจ้งผู้ขอเบิก (Requester) ว่าใบเบิกได้รับการอนุมัติเสร็จสิ้นสมบูรณ์
            $requesterEmail = $transactions->first()->user->email ?? null;
            if ($requesterEmail) {
                Mail::to($requesterEmail)->send(new RequestApprovedCompleteMail($transactions));
            }
        }

        return back()->with('success', 'อนุมัติรายการสำเร็จ');
    }

    /**
     * ตีกลับ (Reject) ใบเบิก
     */
    public function rejectStep(Request $request, $refNo)
    {
        $user = Auth::user();

        $currentApproval = InvTransactionApprovals::where('ref_no', $refNo)
            ->where('status', 'PENDING')
            ->orderBy('step_order', 'asc')
            ->first();

        if (!$currentApproval) {
            return back()->with('error', 'ไม่พบรายการที่ต้องดำเนินการ');
        }

        if ($currentApproval->approver_id != $user->id) {
            return back()->with('error', 'คุณไม่มีสิทธิ์ตีกลับเอกสารนี้');
        }

        // อัปเดตสถานะสเต็ปนี้เป็น REJECTED
        $currentApproval->update([
            'status' => 'REJECTED',
            'comment' => $request->input('comment'),
            'action_at' => now(),
        ]);

        // จัดการเปลี่ยนสถานะสเต็ปที่เหลือทั้งหมดที่ยัง PENDING อยู่ ให้เป็น CANCEL (หรือข้ามไป) 
        // เพื่อไม่ให้ผู้อนุมัติสเต็ปถัดไปค้างสถานะรออนุมัติ
        InvTransactionApprovals::where('ref_no', $refNo)
            ->where('status', 'PENDING')
            ->update([
                'status' => 'CANCELLED', // หรือใช้ 'SKIPPED' ตามโครงสร้างฐานข้อมูลของคุณ
                'action_at' => now(),
            ]);

        // อัปเดตสถานะภาพรวมของ Transaction เป็น REJECTED
        InvTransaction::where('ref_no', $refNo)->update(['status' => 'REJECTED']);

        // ดึงข้อมูลรายการและผู้ขอเบิก เพื่อส่งอีเมลแจ้งว่าเอกสารถูก Reject
        $transactions = InvTransaction::where('ref_no', $refNo)
            ->with(['item', 'detail', 'user'])
            ->get();

        $requesterEmail = $transactions->first()->user->email ?? null;
        if ($requesterEmail) {
            // ส่งเมลแจ้งผู้ขอเบิกว่าเอกสารถูกตีกลับ (คุณสามารถเปลี่ยนชื่อ Mailable ตามระบบของคุณได้)
            Mail::to($requesterEmail)->send(new ApprovalNotificationMail($transactions, $currentApproval, $transactions->first()->user));
        }

        return back()->with('success', 'ตีกลับเอกสารเรียบร้อยแล้ว');
    }

    public function issueItems(Request $request, $refNo)
{
    $transactions = InvTransaction::where('ref_no', $refNo)
        ->where('status', 'APPROVED')
        ->get();

    if ($transactions->isEmpty()) {
        return back()->with('error', 'ไม่พบรายการที่พร้อมจ่าย หรือรายการนี้ถูกจ่ายไปแล้ว');
    }

    DB::beginTransaction();
    try {
        foreach ($transactions as $tx) {
            $requiredQty = $tx->quantity; // จำนวนที่ User ขอเบิก (เช่น 3,000)

            // 1. ดึงล็อตจากตาราง inv_item_details โดยใช้ inv_item_id_fk 
            // เรียงตามวันหมดอายุ (expire_date) หรือวันที่รับเข้า (received_date) จากเก่าไปใหม่ (FIFO)
            $lots = InvItemDetail::where('inv_item_id_fk', $tx->item_id) // ปรับชื่อฟิลด์ item_id ตามความสัมพันธ์จริงของคุณ
                ->where('current_qty', '>', 0)
                ->where('status', 'ACTIVE') // หรือเงื่อนไขสถานะล็อตที่พร้อมใช้งาน
                ->orderBy('expire_date', 'asc') // เอาล็อตที่หมดอายุก่อนขึ้นมาตัดก่อน
                ->get();

            // 2. เช็คสต็อกรวมทุก็ล็อตว่าพอไหม
            $totalStockAvailable = $lots->sum('current_qty');
            if ($totalStockAvailable < $requiredQty) {
                DB::rollBack();
                return back()->with('error', 'สินค้าในสต็อกรวมไม่พอจ่าย (ต้องการ ' . $requiredQty . ' แต่มีคงเหลือรวม ' . $totalStockAvailable . ' ชิ้น)');
            }

            // 3. วนลูปตัดจำนวนใน current_qty ทีละล็อต
            foreach ($lots as $lot) {
                if ($requiredQty <= 0) {
                    break; // ถ้าตัดครบจำนวนที่ต้องการแล้ว หยุดวนลูป
                }

                if ($lot->current_qty >= $requiredQty) {
                    // เคสที่ 1: ล็อตนี้มีของพอ (เช่น ล็อตแรกมี 20,000 ต้องการ 3,000 ล็อตนี้พอดี)
                    $lot->decrement('current_qty', $requiredQty);
                    $requiredQty = 0;
                } else {
                    // เคสที่ 2: ล็อตนี้มีของน้อยกว่าที่ต้องการ (เช่น ล็อตแรกมี 2,000 แต่ต้องการ 3,000)
                    $deductQty = $lot->current_qty; // ดึงออกจนหมดล็อตนี้ (เหลือ 0)
                    $requiredQty -= $deductQty;  // หักลบยอดที่ยังขาด (เหลือต้องหาเพิ่ม 1,000)
                    
                    $lot->update([
                        'current_qty' => 0,
                        'status' => 'OUT_OF_STOCK' // หรือสถานะตามระบบของคุณเมื่อของหมดล็อต
                    ]); 
                }

                // (แนะนำ) หากต้องการเก็บบันทึกประวัติว่าใบเบิกนี้ ตัดของมาจาก lot_number ไหนบ้าง 
                // สามารถบันทึกลงตาราง Pivot หรือตารางประวัติการจ่ายแยกได้ตรงนี้ครับ
            }
        }

        // 4. เปลี่ยนสถานะภาพรวมของใบเบิกเป็น ISSUED (จ่ายพัสดุแล้ว)
        InvTransaction::where('ref_no', $refNo)->update([
            'status' => 'ISSUED',
            'issued_at' => now(),
            'issued_by' => Auth::id(),
        ]);

        DB::commit();
        return back()->with('success', 'ตัดสต็อกข้ามล็อตและบันทึกการจ่ายพัสดุสำเร็จ');

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
    }
}
}
