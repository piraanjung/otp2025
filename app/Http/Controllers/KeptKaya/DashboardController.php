<?php

namespace App\Http\Controllers\KeptKaya;

use App\Http\Controllers\Controller;
use App\Models\KeptKaya\KpPurchaseTransactionDetail;
use App\Models\KpBankAccount;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DashboardController extends Controller
{

    public function index(Request $request, $keptkayatype = '')
    {

        $request->session()->forget('keptkayatype');

        // 0. ดึง Org ID ของผู้ใช้ปัจจุบัน
        $orgId = Auth::user()->org_id_fk;
 
        // 1. ดึงข้อมูลภาพรวม (ต้องส่ง $orgId เข้าไปใน Method นี้ด้วย)
        $schoolStats = $this->getCarbonSummary($orgId);

        // 2. 🏆 Top 5 Hall of Fame (กรองเฉพาะคนใน Org เดียวกัน)
        $topStudents = User::join('kp_purchase_transactions', 'users.id', '=', 'kp_purchase_transactions.kp_user_w_pref_id_fk')
            ->select('users.firstname', 'users.lastname', DB::raw('SUM(kp_purchase_transactions.total_carbon_saved) as total_carbon'))
            ->groupBy('users.id', 'users.firstname', 'users.lastname')
            ->orderByDesc('total_carbon')
            ->whereHas('wastePreference')
            ->take(5)
            ->get();

        // 3. 📈 Trend Analysis (กรองเฉพาะรายการใน Org)
        $monthlyTrend = \App\Models\KeptKaya\KpPurchaseTransaction::select(
                DB::raw("DATE_FORMAT(transaction_date, '%Y-%m') as month"),
                DB::raw('SUM(total_carbon_saved) as total_carbon')
            )
            ->where('transaction_date', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // 4. 💰 Economic Value (กรองเฉพาะยอดเงินของ Org)
        $economicStats = [
            'total_money' => \App\Models\KeptKaya\KpPurchaseTransaction::where('org_id_fk', $orgId)->sum('total_amount'),
            'total_points' => \App\Models\KeptKaya\KpPurchaseTransaction::where('org_id_fk', $orgId)->sum('total_points')
        ];
        if($economicStats['total_money'] == 0 && $economicStats['total_points'] == 0){
            $economicStats = [
                'total_money' => KpBankAccount::where('org_id_fk', $orgId)->sum('balance'),
                'total_points' => KpBankAccount::where('org_id_fk', $orgId)->sum('points'),
            ];
        }

        // 5. 🕒 Recent Activity (รายการล่าสุดใน Org)
        $recentActivities = \App\Models\KeptKaya\KpPurchaseTransaction::where('org_id_fk', $orgId) // <-- เพิ่มบรรทัดนี้
            ->with('userWastePreference.user')
            ->latest('created_at')
            ->take(5)
            ->get();

        $chartLabels = $schoolStats->pluck('material_name');
        $chartData   = $schoolStats->pluck('total_carbon');

        // กรองจำนวนสมาชิกเฉพาะใน Org
        $totalMembers = User::where('org_id_fk', $orgId)
        ->whereHas('wastePreference', function($q) use ($orgId){
            $q->where('org_id_fk', $orgId);
        })
        ->count();

        $request->session()->put('keptkayatype', $keptkayatype);

        return view('keptkayas.dashboard_recycle', compact(
            'schoolStats',
            'chartLabels',
            'chartData',
            'topStudents',
            'monthlyTrend',
            'economicStats',
            'recentActivities',
            'totalMembers'
        ));
    }

    public function getCarbonSummary($orgId = null, $userId = null)
{
    // กำหนด Org ID ถ้าไม่ส่งมาให้ใช้ของคนที่ Login อยู่
    $orgId = $orgId ?? Auth::user()->org_id_fk;

    $query = KpPurchaseTransactionDetail::query()
        // 1. Join กับ Header เสมอเพื่อเช็ค org_id_fk และ user_id
        ->join('kp_purchase_transactions', 'kp_purchase_transactions_details.kp_purchase_trans_id', '=', 'kp_purchase_transactions.id')

        // 2. Join กับตารางสินค้าเพื่อเอาชื่อมาแสดง
        ->join('kp_tbank_items', 'kp_purchase_transactions_details.kp_recycle_item_id', '=', 'kp_tbank_items.id')

        // 3. กรองเฉพาะองค์กรที่ระบุ (Security Check)
        ->where('kp_purchase_transactions.org_id_fk', $orgId)

        // กรองรายการที่ถูกลบ (ถ้ามีฟิลด์ deleted)
        ->where('kp_purchase_transactions.deleted', 0)

        ->select(
            'kp_tbank_items.kp_itemsname as material_name',
            DB::raw('SUM(kp_purchase_transactions_details.carbon_saved) as total_carbon'),
            DB::raw('SUM(kp_purchase_transactions_details.amount_in_units) as total_weight')
        );

    // 4. ถ้ามีการระบุ User ID (เช่น ดูสถิติส่วนตัวนักเรียน) ให้กรองเพิ่ม
    if ($userId) {
        $query->where('kp_purchase_transactions.kp_user_w_pref_id_fk', $userId);
    }

    // จัดกลุ่มและเรียงลำดับ
    return $query->groupBy('kp_tbank_items.id', 'kp_tbank_items.kp_itemsname')
        ->orderByDesc('total_carbon')
        ->get();
}
    // public function getCarbonSummary($userId = null)
    // {
    //     // เริ่ม Query จากตาราง Detail
    //     $query = KpPurchaseTransactionDetail::query()
    //         // Join กับตารางสินค้าเพื่อเอาชื่อมาแสดง (สมมติชื่อตาราง kp_tbank_items)
    //         ->join('kp_tbank_items', 'kp_purchase_transactions_details.kp_recycle_item_id', '=', 'kp_tbank_items.id')
    //         ->select(
    //             'kp_tbank_items.kp_itemsname as material_name', // ชื่อวัสดุภาษาไทย
    //             DB::raw('SUM(kp_purchase_transactions_details.carbon_saved) as total_carbon'), // ผลรวมคาร์บอน
    //             DB::raw('SUM(kp_purchase_transactions_details.amount_in_units) as total_weight') // ผลรวมน้ำหนัก
    //         );

    //     // ถ้ามีการระบุ User ID (เช่น ดูหน้า Profile นักเรียน) ให้กรองข้อมูลเฉพาะคนนั้น
    //     if ($userId) {
    //         // ต้อง Join กลับไปหา Header เพื่อเช็ค user_id
    //         $query->join('kp_purchase_transactions', 'kp_purchase_transactions_details.kp_purchase_trans_id', '=', 'kp_purchase_transactions.id')
    //             ->where('kp_purchase_transactions.kp_user_w_pref_id_fk', $userId);
    //     }

    //     // จัดกลุ่มและเรียงลำดับ
    //     $summary = $query->groupBy('kp_tbank_items.kp_itemsname')
    //         ->orderByDesc('total_carbon') // เรียงจากมากไปน้อย
    //         ->get();

    //     return $summary;
    // }

    /**
     * หน้าแสดงผล Dashboard
     */
    // public function index(Request $request, $keptkayatype = '')
    // {
    //     $request->session()->forget('keptkayatype');

    //     // 1. ดึงข้อมูลภาพรวมทั้งโรงเรียน
    //     $schoolStats = $this->getCarbonSummary();
    //     // 2. ดึงข้อมูลเฉพาะ User ที่ Login อยู่ (ถ้านักเรียน Login)
    //     $myStats = null;
    //     if (Auth::check()) {
    //         // สมมติว่า user_id ใน Auth ตรงกับ kp_user_w_pref_id_fk หรือมีการ map ไว้
    //         // $userWasteId = ...;
    //         // $myStats = $this->getCarbonSummary($userWasteId);
    //     }

    //     // 1. ข้อมูลเดิม (School Stats & Chart Data)
    //     $schoolStats = $this->getCarbonSummary();
    //     $chartLabels = $schoolStats->pluck('material_name');
    //     $chartData   = $schoolStats->pluck('total_carbon');

    //     // 2. 🏆 Top 5 Hall of Fame (ลดคาร์บอนสูงสุด)
    //     $topStudents = User::join('kp_purchase_transactions', 'users.id', '=', 'kp_purchase_transactions.kp_user_w_pref_id_fk')
    //         ->select('users.firstname', 'users.lastname', DB::raw('SUM(kp_purchase_transactions.total_carbon_saved) as total_carbon'))
    //         ->groupBy('users.id', 'users.firstname', 'users.lastname')
    //         ->orderByDesc('total_carbon')
    //         ->take(5)
    //         ->get();

    //     // 3. 📈 Trend Analysis (ย้อนหลัง 6 เดือน)
    //     $monthlyTrend = \App\Models\KeptKaya\KpPurchaseTransaction::select(
    //         DB::raw("DATE_FORMAT(transaction_date, '%Y-%m') as month"),
    //         DB::raw('SUM(total_carbon_saved) as total_carbon')
    //     )
    //         ->where('transaction_date', '>=', now()->subMonths(6))
    //         ->groupBy('month')
    //         ->orderBy('month')
    //         ->get();

    //     // 4. 💰 Economic Value (มูลค่าเศรษฐกิจหมุนเวียน)
    //     $economicStats = [
    //         'total_money' => \App\Models\KeptKaya\KpPurchaseTransaction::sum('total_amount'),
    //         'total_points' => \App\Models\KeptKaya\KpPurchaseTransaction::sum('total_points')
    //     ];

    //     // 5. 🕒 Recent Activity (รายการล่าสุด)
    //     $recentActivities = \App\Models\KeptKaya\KpPurchaseTransaction::with('userWastePreference.user') // ตรวจสอบ Relation ใน Model ให้ถูกต้อง
    //         ->latest('created_at')
    //         ->take(5)
    //         ->get();

    //     // เตรียมข้อมูลสำหรับกราฟ (Chart.js)
    //     $chartLabels = $schoolStats->pluck('material_name');
    //     $chartData   = $schoolStats->pluck('total_carbon');
    //     $totalMembers = User::count(); // หรือกรองตาม Role เช่น ->where('role', 'student')->count();


    //     $request->session()->put('keptkayatype', $keptkayatype);

    //     return view('keptkayas.dashboard_recycle', compact(
    //         'schoolStats', 'chartLabels', 'chartData',
    //         'topStudents', 'monthlyTrend', 'economicStats', 'recentActivities','totalMembers'
    //         ));
    // }
}
