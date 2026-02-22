<?php

namespace App\Http\Controllers\KeptKaya;

use App\Http\Controllers\Controller;
use App\Models\Admin\Zone;
use App\Models\KeptKaya\WasteBinSubscription;
use App\Models\KeptKaya\WasteBinPayment;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


class AnnualReportController extends Controller
{
    // หน้าเลือกรายงาน
    public function index()
    {
        $zones = Zone::all();
        $fiscalYear = WasteBinSubscription::calculateFiscalYear();
        return view('keptkayas.reports.index', compact('zones', 'fiscalYear'));
    }

    // ฟังก์ชันประมวลผลรายงาน
   public function generate(Request $request)
{
    $type = $request->report_type;
    $data = [];
    $title = "";
    $summary = []; // ประกาศตัวแปรเปล่าไว้ก่อน กัน Error case อื่น
    $view = 'keptkayas.reports.preview_table';

    switch ($type) {
        // ... (Case 1-4 เดิมของคุณถูกต้องแล้ว ใส่ไว้เหมือนเดิม) ...
        case 'daily_collection':
            $date = $request->input('date', date('Y-m-d'));
            $title = "รายงานการรับเงินประจำวันที่ " . \Carbon\Carbon::parse($date)->locale('th')->isoFormat('D MMMM YYYY');
            $data = \App\Models\KeptKaya\WasteBinPayment::with(['subscription.wasteBin.user', 'subscription.wasteBin.kpUserGroup'])
                ->whereDate('created_at', $date)
                ->get();
            break;

        case 'arrears':
            $fy = $request->input('fiscal_year');
            $zone = $request->input('zone_id');
            $title = "รายชื่อลูกหนี้ค้างชำระ ประจำปีงบประมาณ $fy";
            $query = \App\Models\KeptKaya\WasteBinSubscription::with(['wasteBin.user', 'wasteBin.user.user_zone'])
                ->where('fiscal_year', $fy)
                ->where('status', '!=', 'paid');
            if ($zone) {
                $query->whereHas('wasteBin.user', function ($q) use ($zone) {
                    $q->where('zone_id', $zone);
                });
            }
            $data = $query->get();
            break;

        case 'zone_summary':
            $fy = $request->input('fiscal_year');
            $title = "สรุปผลการจัดเก็บรายโซน ปีงบประมาณ $fy";
            $data = \App\Models\KeptKaya\WasteBinSubscription::join('kp_waste_bins', 'kp_waste_bin_subscriptions.waste_bin_id', '=', 'kp_waste_bins.id')
                ->join('users', 'kp_waste_bins.user_id', '=', 'users.id')
                ->join('user_zones', 'users.zone_id', '=', 'user_zones.id')
                ->where('kp_waste_bin_subscriptions.fiscal_year', $fy)
                ->select(
                    'user_zones.zone_name',
                    DB::raw('COUNT(*) as total_bins'),
                    DB::raw('SUM(annual_fee) as total_revenue'),
                    DB::raw('SUM(total_paid_amt) as total_collected'),
                    DB::raw('SUM(annual_fee - total_paid_amt) as total_outstanding')
                )
                ->groupBy('user_zones.zone_name')
                ->get();
            break;

        case 'receipt_control':
            $start = $request->input('start_date');
            $end = $request->input('end_date');
            $title = "ทะเบียนคุมใบเสร็จรับเงิน (" . \Carbon\Carbon::parse($start)->format('d/m/Y') . " - " . \Carbon\Carbon::parse($end)->format('d/m/Y') . ")";
            $data = \App\Models\KeptKaya\WasteBinPayment::with(['subscription.wasteBin.user'])
                ->whereDate('created_at', '>=', $start)
                ->whereDate('created_at', '<=', $end)
                ->orderBy('id', 'asc')
                ->get();
            break;

        case 'remittance':
            // 5. รายงานนำส่งเงิน
            $date = $request->input('date', date('Y-m-d'));
            $title = "ใบนำส่งเงินประจำวันที่ " . \Carbon\Carbon::parse($date)->locale('th')->isoFormat('D MMMM YYYY');
            $data = \App\Models\KeptKaya\WasteBinPayment::with(['subscription.wasteBin.user'])
                ->whereDate('created_at', $date)
                ->get();

            $summary = [
                'cash' => $data->where('payment_method', 'cash')->sum('amount_paid'),
                'transfer' => $data->where('payment_method', 'transfer')->sum('amount_paid'),
                'total' => $data->sum('amount_paid'),
                'count' => $data->count(),
                'first_no' => $data->min('id'),
                'last_no' => $data->max('id'),
            ];
            break;

        // --- เพิ่มใหม่ 2 รายงาน ---

        case 'service_points':
            // 6. รายชื่อจุดเก็บขยะ (Service Point List)
            $zone = $request->input('zone_id');
            $title = "รายชื่อจุดเก็บขยะ (Service Point List)";
            
            // ดึงข้อมูลถังขยะ Active
            $query = \App\Models\KeptKaya\WasteBin::with(['user.user_zone', 'kpUserGroup'])
                ->where('status', 'active'); // เฉพาะที่ Active
            
            if ($zone) {
                $query->whereHas('user', function($q) use ($zone) {
                    $q->where('zone_id', $zone);
                });
            }
            // เรียงตามโซน เพื่อให้รถเก็บวิ่งง่าย
            $data = $query->get()->sortBy(function($bin) {
                return $bin->user->user_zone->id ?? 0;
            });
            break;

        case 'damaged_bins':
            // 7. รายงานถังขยะชำรุด
            $title = "รายงานถังขยะชำรุด/แจ้งซ่อม (Damaged Bins)";
            
            $data = \App\Models\KeptKaya\WasteBin::with(['user.user_zone'])
                ->where('status', 'damaged') // สถานะ damaged
                ->orderBy('updated_at', 'desc') // เรียงตามวันที่แจ้งล่าสุด
                ->get();
            break;
    }

    // *** สำคัญ: ต้องส่ง $summary ไปด้วย ***
    return view($view, compact('data', 'type', 'title', 'request', 'summary'));
}




}