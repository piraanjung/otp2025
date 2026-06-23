<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KeptKaya\KpMoneyRequest;
use App\Models\KpBulkSaleDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
{
    $orgId = Auth::user()->org_id_fk;
    $today = now()->toDateString();
    $nextTuesday = now()->next(\Carbon\Carbon::TUESDAY)->toDateString();

    // 1. สรุปยอดกองทุน (ดึงจากที่เราทำไว้ใน Welfare)
    $totalProfit = \App\Models\KpBulkSale::where('org_id_fk', $orgId)->sum('profit_amount');
    $totalPaid = \App\Models\KpWelfarePayout::where('org_id_fk', $orgId)->where('status', 'paid')->sum('amount');
    $fundBalance = $totalProfit - $totalPaid;

    // 2. สถิติขยะแยกตามประเภท (เพื่อทำกราฟ Donut)
    // ดึงยอดรวมน้ำหนักสะสมจากการขาย Bulk แยกตามชื่อประเภทขยะ

    // 2. ข้อมูลแจ้งเตือน (Alerts)
    // รายการถอนเงินที่นัดรับ "วันนี้"
    $todayWithdraws = KpMoneyRequest::where('org_id_fk', $orgId)
        ->where('payout_date', $today)
        ->where('status', 'pending')
        ->count();

    // รายการถอนเงินทั้งหมดที่ยังค้างจ่าย
    $pendingWithdraws = KpMoneyRequest::where('org_id_fk', $orgId)
        ->where('status', 'pending')
        ->orderBy('payout_date', 'asc')
        ->take(5)
        ->get();
    $wasteStats = KpBulkSaleDetail::join('kp_tbank_items', 'kp_bulk_sale_details.kp_tbank_item_id', '=', 'kp_tbank_items.id')
        ->join('kp_bulk_sales', 'kp_bulk_sale_details.bulk_sale_id', '=', 'kp_bulk_sales.id')
        ->where('kp_bulk_sales.org_id_fk', $orgId)
        ->select('kp_tbank_items.kp_itemsname', DB::raw('SUM(weight_kg) as total_weight'))
        ->groupBy('kp_tbank_items.kp_itemsname')
        ->get();

        // ตัวอย่างการคำนวณหาค่า Carbon Saved รวมใน Dashboard
$carbonData = KpBulkSaleDetail::join('kp_tbank_items', 'kp_bulk_sale_details.kp_tbank_item_id', '=', 'kp_tbank_items.id')
    ->join('emission_factors', 'kp_tbank_items.ef_id_fk', '=', 'emission_factors.id') // เชื่อมตาม ef_id_fk ใน Model ของคุณ
    ->join('kp_bulk_sales', 'kp_bulk_sale_details.bulk_sale_id', '=', 'kp_bulk_sales.id')
    ->where('kp_bulk_sales.org_id_fk', Auth::user()->org_id_fk)
    ->select(DB::raw('SUM(kp_bulk_sale_details.weight_kg * emission_factors.ef_value) as total_co2_saved')) // สมมติคอลัมน์ใน EF ชื่อ value
    ->first();

$totalCarbonSaved = $carbonData->total_co2_saved ?? 0;
    return view('admin.dashboard', compact(
        'fundBalance', 'totalProfit', 'totalPaid', 'wasteStats',
        'todayWithdraws', 'pendingWithdraws', 'nextTuesday', 'totalCarbonSaved'
    ));
}
}
