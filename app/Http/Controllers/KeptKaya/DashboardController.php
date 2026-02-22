<?php

namespace App\Http\Controllers\keptkaya;

use App\Http\Controllers\Controller;
use App\Models\KeptKaya\KpPurchaseTransactionDetail;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function getCarbonSummary($userId = null)
    {
        // เริ่ม Query จากตาราง Detail
        $query = KpPurchaseTransactionDetail::query()
            // Join กับตารางสินค้าเพื่อเอาชื่อมาแสดง (สมมติชื่อตาราง kp_tbank_items)
            ->join('kp_tbank_items', 'kp_purchase_transactions_details.kp_recycle_item_id', '=', 'kp_tbank_items.id')
            ->select(
                'kp_tbank_items.kp_itemsname as material_name', // ชื่อวัสดุภาษาไทย
                DB::raw('SUM(kp_purchase_transactions_details.carbon_saved) as total_carbon'), // ผลรวมคาร์บอน
                DB::raw('SUM(kp_purchase_transactions_details.amount_in_units) as total_weight') // ผลรวมน้ำหนัก
            );

        // ถ้ามีการระบุ User ID (เช่น ดูหน้า Profile นักเรียน) ให้กรองข้อมูลเฉพาะคนนั้น
        if ($userId) {
            // ต้อง Join กลับไปหา Header เพื่อเช็ค user_id
            $query->join('kp_purchase_transactions', 'kp_purchase_transactions_details.kp_purchase_trans_id', '=', 'kp_purchase_transactions.id')
                ->where('kp_purchase_transactions.kp_user_w_pref_id_fk', $userId);
        }

        // จัดกลุ่มและเรียงลำดับ
        $summary = $query->groupBy('kp_tbank_items.kp_itemsname')
            ->orderByDesc('total_carbon') // เรียงจากมากไปน้อย
            ->get();

        return $summary;
    }

    /**
     * หน้าแสดงผล Dashboard
     */
    public function index()
    {
        // 1. ดึงข้อมูลภาพรวมทั้งโรงเรียน
        $schoolStats = $this->getCarbonSummary();

        // 2. ดึงข้อมูลเฉพาะ User ที่ Login อยู่ (ถ้านักเรียน Login)
        $myStats = null;
        if (Auth::check()) {
            // สมมติว่า user_id ใน Auth ตรงกับ kp_user_w_pref_id_fk หรือมีการ map ไว้
            // $userWasteId = ...;
            // $myStats = $this->getCarbonSummary($userWasteId);
        }

        // 1. ข้อมูลเดิม (School Stats & Chart Data)
        $schoolStats = $this->getCarbonSummary();
        $chartLabels = $schoolStats->pluck('material_name');
        $chartData   = $schoolStats->pluck('total_carbon');

        // 2. 🏆 Top 5 Hall of Fame (ลดคาร์บอนสูงสุด)
        $topStudents = User::join('kp_purchase_transactions', 'users.id', '=', 'kp_purchase_transactions.kp_user_w_pref_id_fk')
            ->select('users.firstname', 'users.lastname', DB::raw('SUM(kp_purchase_transactions.total_carbon_saved) as total_carbon'))
            ->groupBy('users.id', 'users.firstname', 'users.lastname')
            ->orderByDesc('total_carbon')
            ->take(5)
            ->get();

        // 3. 📈 Trend Analysis (ย้อนหลัง 6 เดือน)
        $monthlyTrend = \App\Models\KeptKaya\KpPurchaseTransaction::select(
            DB::raw("DATE_FORMAT(transaction_date, '%Y-%m') as month"),
            DB::raw('SUM(total_carbon_saved) as total_carbon')
        )
            ->where('transaction_date', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // 4. 💰 Economic Value (มูลค่าเศรษฐกิจหมุนเวียน)
        $economicStats = [
            'total_money' => \App\Models\KeptKaya\KpPurchaseTransaction::sum('total_amount'),
            'total_points' => \App\Models\KeptKaya\KpPurchaseTransaction::sum('total_points')
        ];

        // 5. 🕒 Recent Activity (รายการล่าสุด)
        $recentActivities = \App\Models\KeptKaya\KpPurchaseTransaction::with('userWastePreference.user') // ตรวจสอบ Relation ใน Model ให้ถูกต้อง
            ->latest('created_at')
            ->take(5)
            ->get();

        // เตรียมข้อมูลสำหรับกราฟ (Chart.js)
        $chartLabels = $schoolStats->pluck('material_name');
        $chartData   = $schoolStats->pluck('total_carbon');
        $totalMembers = User::count(); // หรือกรองตาม Role เช่น ->where('role', 'student')->count();
        return view('keptkayas.dashboard_recycle', compact(
            'schoolStats', 'chartLabels', 'chartData',
            'topStudents', 'monthlyTrend', 'economicStats', 'recentActivities','totalMembers'
            ));
    }
}
