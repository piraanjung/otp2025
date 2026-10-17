<?php

namespace App\Http\Controllers\FoodWaste;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FoodWaste\FoodWasteUserPreference;
use App\Models\FoodWaste\FoodWasteLog;
use App\Models\FoodWaste\FoodWasteTransaction;
use App\Models\User; // เผื่อดึงชื่อ User
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ExecutiveDashboardController extends Controller
{
    public function index()
    {
        // 1. KPI Cards (ภาพรวม)
        $totalUsers = FoodWasteUserPreference::count();
        $totalWasteSaved = FoodWasteLog::sum('weight_kg');
        $totalCarbonSaved = FoodWasteLog::sum('carbon_saved_kg');
        // หารายได้รวมจากการซื้อปุ๋ย (สมมติว่าใช้ type 'buy_compost')
        $totalRevenue = FoodWasteTransaction::where('transaction_type', 'buy_compost')->sum('amount');

        // 2. ข้อมูลกราฟแท่ง: ปริมาณขยะ 6 เดือนย้อนหลัง
        $months = [];
        $wasteData = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->startOfMonth()->subMonths($i);
            $months[] = $date->format('M Y'); // เช่น Mar 2026

            $sum = FoodWasteLog::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->sum('weight_kg');
            $wasteData[] = round($sum, 2);
        }

        // 3. ข้อมูลกราฟโดนัท: สัดส่วน Transaction
        $transactionStats = FoodWasteTransaction::select('transaction_type', DB::raw('count(*) as total'))
            ->groupBy('transaction_type')
            ->pluck('total', 'transaction_type')
            ->toArray();

        $pieLabels = [];
        $pieData = [];
        foreach ($transactionStats as $type => $count) {
            $pieLabels[] = $type === 'earn_waste' ? 'ได้พอยต์' : ($type === 'redeem' ? 'แลกของ' : 'ซื้อปุ๋ย');
            $pieData[] = $count;
        }

        // 4. ตาราง Top 5 สมาชิกที่ส่งขยะเยอะสุด (Leaderboard)
        $topPerformers = FoodWasteLog::select('user_id', DB::raw('SUM(weight_kg) as total_waste'))
            ->groupBy('user_id')
            ->orderByDesc('total_waste')
            ->limit(5)
            ->get();

        // ดึงชื่อ User เข้ามาแนบ (ใช้วิธีวนลูปเพื่อความง่ายหาก Model ไม่ได้ทำ Relation ไว้)
        foreach ($topPerformers as $performer) {
            $user = User::find($performer->user_id);
            $performer->user_name = $user ? $user->firstname . ' ' . $user->lastname : 'Unknown User';
        }

        // ==========================================
        // ส่วนที่เพิ่มใหม่: สถิติการทิ้งขยะแยกตามวัน (วันที่ 1-7 และเกิน 7 วัน)
        // ==========================================
        $engagementData = DB::table('foodwaste_waste_logs as logs')
            ->join('foodwaste_compost_batches as batches', 'logs.batch_id', '=', 'batches.id')
            ->select(
                // หาความต่างของวัน (วันที่ทิ้ง - วันที่เริ่มหมัก)
                DB::raw('DATEDIFF(DATE(logs.created_at), batches.start_date) as day_diff'),
                // นับจำนวน User แบบไม่ซ้ำคน (คนนึงอาจจะทิ้ง 2 รอบในวันเดียวกัน ให้นับเป็น 1 คน)
                DB::raw('COUNT(DISTINCT logs.user_id) as user_count')
            )
            ->whereNotNull('logs.batch_id')
            ->groupBy('day_diff')
            ->get();

        // เตรียมกล่องใส่ข้อมูล (วันที่ 1, 2, 3, 4, 5, 6, 7, และ >7)
        $engagementLabels = ['วันที่ 1', 'วันที่ 2', 'วันที่ 3', 'วันที่ 4', 'วันที่ 5', 'วันที่ 6', 'วันที่ 7', 'เกิน 7 วัน'];
        $engagementValues = array_fill(0, 8, 0); // สร้าง array มีเลข 0 จำนวน 8 ตัวรอไว้

        foreach ($engagementData as $data) {
            $diff = (int) $data->day_diff;

            // สมมติว่า start_date คือวันที่ 1 (day_diff = 0)
            if ($diff < 0) {
                continue; // ข้ามข้อมูลที่ทิ้งก่อนวันเริ่มหมัก (ถ้ามีหลุดมา)
            } elseif ($diff == 0) {
                $engagementValues[0] += $data->user_count; // วันที่ 1
            } elseif ($diff == 1) {
                $engagementValues[1] += $data->user_count; // วันที่ 2
            } elseif ($diff == 2) {
                $engagementValues[2] += $data->user_count; // วันที่ 3
            } elseif ($diff == 3) {
                $engagementValues[3] += $data->user_count; // วันที่ 4
            } elseif ($diff == 4) {
                $engagementValues[4] += $data->user_count; // วันที่ 5
            } elseif ($diff == 5) {
                $engagementValues[5] += $data->user_count; // วันที่ 6
            } elseif ($diff == 6) {
                $engagementValues[6] += $data->user_count; // วันที่ 7
            } else {
                $engagementValues[7] += $data->user_count; // เกิน 7 วัน (วันที่ 8 เป็นต้นไป)
            }
        }

        return view('foodwaste.executive_dashboard', compact(
            'engagementLabels',
            'engagementValues',
            'totalUsers',
            'totalWasteSaved',
            'totalCarbonSaved',
            'totalRevenue',
            'months',
            'wasteData',
            'pieLabels',
            'pieData',
            'topPerformers'
        ));
    }
}
