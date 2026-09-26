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
        'receive_amount'   => 'required|numeric|min:0.01',     
        'conversion_rate'  => 'required|numeric|min:0.01',     
        'location_id_fk'   => 'required|exists:inv_locations,id', 
        'supplier_id_fk'   => 'required|exists:suppliers,id',
        'lot_number'       => 'nullable|string',
        'reference_doc'    => 'nullable|string',
        'expire_date'      => 'nullable|date',
    ]);

    $user = Auth::user();
    $item = InvItem::where('org_id_fk', $user->org_id_fk)->findOrFail($request->inv_item_id_fk);

    $receiveAmount = $request->receive_amount;     // เช่น รับมา 10 กล่อง
    $conversionRate = $request->conversion_rate;   // กล่องละ 1,000 ชิ้น
    
    // คำนวณจำนวนชิ้นสุทธิทั้งหมด (10 * 1000 = 10,000 ชิ้น)
    $totalQty = $receiveAmount * $conversionRate;

    // บันทึกแค่แถวเดียวจบ! ไม่ต้องวนลูป
    InvItemDetail::create([
        'inv_item_id_fk'   => $item->id,
        'org_id_fk'        => $user->org_id_fk,
        'lot_number'       => $request->lot_number,
        'reference_doc'    => $request->reference_doc,
        'supplier_id_fk'   => $request->supplier_id_fk,
        // เก็บยอดรวมทั้งหมดไว้ในแถวนี้
        'initial_qty'      => $totalQty,
        'current_qty'      => $totalQty, // จะค่อยๆ ถูกตัดลดลงเวลาเบิก (เช่น เบิกทีละ 5 ชิ้น)

        'conversion_rate'  => $conversionRate,
        'location_id_fk'   => $request->location_id_fk,
        'expire_date'      => $request->expire_date,
        'received_date'    => now(),
        'received_by'    => Auth::id(),
        'status'           => 'ACTIVE'
    ]);

    return redirect()->route('inventory.items.index')
        ->with('success', "เพิ่มสต็อก {$item->name} จำนวนรวม {$totalQty} {$item->unit} เรียบร้อยแล้ว!");
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
