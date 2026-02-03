<?php

namespace App\Http\Controllers\Inventory;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InvItem;
use App\Models\InvItemDetail;
use Illuminate\Support\Facades\Auth;

class InvStockController extends Controller
{
    // 1. เปิดหน้าฟอร์มรับของ
    public function receiveForm($id)
    {
        // ดึงข้อมูลสินค้าแม่ (Catalog) มาแสดง
        $item = InvItem::findOrFail($id);
        
        // ตรวจสอบสิทธิ์ (คนละ Org ห้ามยุ่ง)
        if($item->org_id_fk != Auth::user()->org_id_fk){
            abort(403); 
        }

        return view('inventory.inv_stock_receive', compact('item'));
    }

    // 2. บันทึกข้อมูล (หัวใจสำคัญ ❤️)
    public function storeReceive(Request $request)
    {
        $request->validate([
            'inv_item_id_fk' => 'required',
            'quantity_per_unit' => 'required|numeric|min:0', // ขนาดบรรจุต่อขวด
            'amount' => 'required|integer|min:1',           // จำนวนขวดที่รับ
            'lot_number' => 'nullable|string',
            'expire_date' => 'nullable|date',
        ]);

        $item = InvItem::findOrFail($request->inv_item_id_fk);
        $user = Auth::user();

        // --- LOOP สร้างทีละขวด ---
        // ถ้าUser กรอกว่ารับมา 5 ขวด ระบบจะวนลูปสร้าง 5 record
        for ($i = 0; $i < $request->amount; $i++) {
            
            InvItemDetail::create([
                'inv_item_id_fk' => $item->id,
                'lot_number' => $request->lot_number,
                
                // ปริมาณตั้งต้น และ ปริมาณคงเหลือ (ตอนรับมามันต้องเท่ากัน)
                'initial_qty' => $request->quantity_per_unit, 
                'current_qty' => $request->quantity_per_unit,
                
                'expire_date' => $request->expire_date,
                'received_date' => now(),
                'status' => 'ACTIVE'
            ]);
        }

        // อัปเดตยอดรวมที่ตารางแม่ (Optional: เพื่อความเร็วในการ Query หน้า List)
        // $item->qty += ($request->amount * $request->quantity_per_unit);
        // $item->save();

        return redirect()->route('inventory.items.index')
            ->with('success', "เพิ่มสต็อก {$item->name} จำนวน {$request->amount} ขวด เรียบร้อยแล้ว!");
    }
}