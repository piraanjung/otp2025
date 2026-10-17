<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KeptKaya\KpTbankItems;
use App\Models\KpBulkSale;
use App\Models\KpBulkSaleDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BulkSaleController extends Controller
{
    public function index()
    {
        $orgId = Auth::user()->org_id_fk;

        // ดึงประวัติการขายล๊อตใหญ่ พร้อมข้อมูล Admin ผู้บันทึก
        $bulkSales = KpBulkSale::where('org_id_fk', $orgId)
            ->with('recorder')
            ->orderBy('sale_date', 'desc')
            ->paginate(15);

        return view('admin.bulk_sales.index', compact('bulkSales'));
    }
    public function create()
    {
        // ดึงประเภทขยะทั้งหมดที่มีในระบบมาให้ Admin เลือก
        $items = KpTbankItems::where('status', 'active')->get();

        return view('admin.bulk_sales.create', compact('items'));
    }
    public function store(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $orgId = Auth::user()->org_id_fk;

            // 1. บันทึกหัวข้อการขาย (Header)
            $bulkSale = KpBulkSale::create([
                'org_id_fk'     => $orgId,
                'sale_date'     => $request->sale_date,
                'buyer_name'    => $request->buyer_name,
                'total_cost'    => 0, // จะอัปเดตหลังจากวนลูป Detail
                'total_revenue' => 0,
                'profit_amount' => 0,
                'recorder_id'   => Auth::id(),
                'note'          => $request->note,
            ]);

            $totalCost = 0;
            $totalRevenue = 0;

            // 2. วนลูปบันทึกรายการขยะแต่ละประเภท (Detail)
            foreach ($request->items as $item) {
                // ดึงราคาต้นทุนเฉลี่ยที่เรารับซื้อจากชาวบ้าน (จากตาราง Price Point หรือ Items)
                $itemModel = KpTbankItems::find($item['kp_tbank_item_id']);
                $costPerUnit = $itemModel->current_buy_price; // ราคารับซื้อปัจจุบัน

                $subTotalCost = $item['weight_kg'] * $costPerUnit;
                $subTotalRevenue = $item['weight_kg'] * $item['price_per_unit'];

                $totalCost += $subTotalCost;
                $totalRevenue += $subTotalRevenue;

                KpBulkSaleDetail::create([
                    'bulk_sale_id'      => $bulkSale->id,
                    'kp_tbank_item_id'  => $item['kp_tbank_item_id'],
                    'weight_kg'         => $item['weight_kg'],
                    'price_per_unit'    => $item['price_per_unit'],
                    'sub_total'         => $subTotalRevenue,
                ]);

                // --- ตรงนี้สามารถเพิ่ม Logic การตัดสต็อกขยะ (Inventory) ได้ในอนาคต ---
            }

            // 3. อัปเดตยอดสรุปและกำไรกลับไปที่ Header
            $profit = $totalRevenue - $totalCost;
            $bulkSale->update([
                'total_cost'    => $totalCost,
                'total_revenue' => $totalRevenue,
                'profit_amount' => $profit,
            ]);

            return redirect()->route('admin.welfare.dashboard')->with('success', 'บันทึกการขายและนำกำไรเข้ากองทุนสำเร็จ!');
        });
    }

    // หน้าฟอร์มแก้ไข
public function edit($id)
{
    $sale = KpBulkSale::with('details')->findOrFail($id);
    $items = KpTbankItems::where('status', 'active')->get();

    return view('admin.bulk_sales.edit', compact('sale', 'items'));
}

// บันทึกการแก้ไข
public function update(Request $request, $id)
{
    return DB::transaction(function () use ($request, $id) {
        $bulkSale = KpBulkSale::findOrFail($id);

        // 1. ลบรายการ Detail เดิมทิ้งก่อนเพื่อบันทึกใหม่ (Re-calculate)
        $bulkSale->details()->delete();

        $totalCost = 0;
        $totalRevenue = 0;

        // 2. วนลูปบันทึกรายการใหม่
        foreach ($request->items as $item) {
            $itemModel = KpTbankItems::find($item['kp_tbank_item_id']);
            $costPerUnit = $itemModel->current_buy_price;

            $subTotalCost = $item['weight_kg'] * $costPerUnit;
            $subTotalRevenue = $item['weight_kg'] * $item['price_per_unit'];

            $totalCost += $subTotalCost;
            $totalRevenue += $subTotalRevenue;

            KpBulkSaleDetail::create([
                'bulk_sale_id'      => $bulkSale->id,
                'kp_tbank_item_id'  => $item['kp_tbank_item_id'],
                'weight_kg'         => $item['weight_kg'],
                'price_per_unit'    => $item['price_per_unit'],
                'sub_total'         => $subTotalRevenue,
            ]);
        }

        // 3. อัปเดต Header (กำไรใหม่จะถูกคำนวณที่นี่)
        $bulkSale->update([
            'sale_date'     => $request->sale_date,
            'buyer_name'    => $request->buyer_name,
            'total_cost'    => $totalCost,
            'total_revenue' => $totalRevenue,
            'profit_amount' => $totalRevenue - $totalCost,
            'note'          => $request->note,
        ]);

        return redirect()->route('admin.bulk_sales.index')->with('success', 'แก้ไขข้อมูลการขายเรียบร้อย ยอดกำไรกองทุนถูกปรับปรุงแล้ว');
    });
}
}
