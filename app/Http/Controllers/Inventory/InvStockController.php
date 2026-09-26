<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InvItem;
use App\Models\InvItemDetail;
use App\Models\InvLocation;
use App\Models\InvUnit;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class InvStockController extends Controller
{
    // 1. เปิดหน้าฟอร์มรับของ
    // public function receiveForm($id)
    // {
    //     // ดึงข้อมูลสินค้าแม่ (Catalog) มาแสดง
    //     $item = InvItem::findOrFail($id);

    //     // ตรวจสอบสิทธิ์ (คนละ Org ห้ามยุ่ง)
    //     if($item->org_id_fk != Auth::user()->org_id_fk){
    //         abort(403); 
    //     }

    //     return view('inventory.stock.receive', compact('item'));
    // }
    public function receiveForm($itemId)
{
    $user = Auth::user();
    $item = InvItem::where('org_id_fk', $user->org_id_fk)->findOrFail($itemId);

    // ดึง Location ของหน่วยงาน
    $locations = InvLocation::where('org_id_fk', $user->org_id_fk)
        ->when($user->department_id_fk, function($q) use ($user) {
            $q->where('department_id_fk', $user->department_id_fk);
        })
        ->get();

    
    // ดึงรายการหน่วยนับจาก Table units (กรองตาม org หรือใช้ตารางกลาง)
    $units = InvUnit::where('org_id_fk', $user->org_id_fk)->get(); 
    $suppliers = Supplier::where('org_id_fk', $user->org_id_fk)->get();

    return view('inventory.stock.receive', compact('item', 'locations', 'units', 'suppliers'));
}

    // 2. บันทึกข้อมูล (หัวใจสำคัญ ❤️)
    public function storeReceive(Request $request)
    {
        $request->validate([
            'inv_item_id_fk'   => 'required|exists:inv_items,id',
            'receive_amount'   => 'required|numeric|min:0.01',     // จำนวนที่รับเข้ามา (เช่น 1 กล่อง หรือ 10 เส้น)
            'conversion_rate'  => 'required|numeric|min:0.01',     // อัตราส่วน (เช่น 1 กล่องมี 12 ชิ้น)
            'location_id_fk'   => 'required|exists:inv_locations,id', // พื้นที่จัดเก็บ
            'lot_number'       => 'nullable|string',
            'expire_date'      => 'nullable|date',
        ]);

        $user = Auth::user();
        $item = InvItem::where('org_id_fk', $user->org_id_fk)->findOrFail($request->inv_item_id_fk);

        // คำนวณจำนวนรอบที่จะต้องวนลูปสร้าง Record
        // เช่น รับมา 2 กล่อง กล่องละ 12 ชิ้น -> สร้าง 24 Record ในตาราง detail
        $receiveAmount = $request->receive_amount;
        $conversionRate = $request->conversion_rate;

        // ปริมาณต่อ 1 หน่วยย่อยที่ถูกกระจายลงแต่ละชิ้น (เช่น ถ้าซื้อมาเป็นแพ็ค 12 ชิ้น ตัวย่อยแต่ละชิ้นจะมีค่าเท่ากับ 1 หรือถ้าเป็นขวดใหญ่มีปริมาณ 1000 มล. ค่านี้คือ 1000)
        // ตรงนี้ขึ้นอยู่กับว่าคุณต้องการให้ 1 แถวใน DB แทน 1 หน่วยย่อยหรือไม่
        $qtyPerPiece = $conversionRate;
        $totalRecords = (int) $receiveAmount; // สมมติวนลูปตามจำนวนหน่วยที่รับ (หรือถ้าต้องการสร้างตามจำนวนชิ้นสุทธิ ให้ปรับตรงนี้ได้ครับ)

        // --- LOOP สร้างตามจำนวนที่รับ ---
        for ($i = 0; $i < $totalRecords; $i++) {
            \App\Models\InvItemDetail::create([
                'inv_item_id_fk'   => $item->id,
                'org_id_fk'        => $user->org_id_fk,
                'lot_number'       => $request->lot_number,

                // ปริมาณตั้งต้น และ ปริมาณคงเหลือต่อชิ้น
                'initial_qty'      => $qtyPerPiece,
                'current_qty'      => $qtyPerPiece,

                'conversion_rate'  => $conversionRate,
                'location_id_fk'   => $request->location_id_fk, // บันทึกพิกัดจัดเก็บ
                'expire_date'      => $request->expire_date,
                'received_date'    => now(),
                'status'           => 'ACTIVE'
            ]);
        }

        $totalQtyCalculated = $receiveAmount * $conversionRate;

        return redirect()->route('inventory.items.index')
            ->with('success', "เพิ่มสต็อก {$item->name} จำนวนรวม {$totalQtyCalculated} {$item->unit} เรียบร้อยแล้ว!");
    }

    public function expiringStock()
    {
        $user = Auth::user();
        $orgId = $user->org_id_fk;

        // ดึงข้อมูลจากตารางย่อย (InvItemDetail) เฉพาะที่ Active และใกล้หมดอายุใน 30 วัน
        $items = InvItemDetail::whereHas('item', function ($q) use ($orgId) {
            $q->where('org_id_fk', $orgId);
        })
            ->where('status', 'ACTIVE')
            ->whereDate('expire_date', '<=', Carbon::now()->addDays(30))
            ->orderBy('expire_date', 'asc')
            ->with(['item.category']) // 👈 เอา .unit ออก เหลือไว้แค่ category
            ->paginate(15);

        return view('inventory.stock.expiring', compact('items'));
    }
}
