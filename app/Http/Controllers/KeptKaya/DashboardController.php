<?php namespace App\Http\Controllers\KeptKaya; 

use App\Http\Controllers\Controller; 
use App\Models\KeptKaya\KpPurchaseTransactionDetail; 
use App\Models\KeptKaya\KpPurchaseTransaction;
use App\Models\KPBankAccount; 
use App\Models\User; 
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\DB; 
use Illuminate\Http\Request; 

class DashboardController extends Controller 
{ 
    public function index(Request $request, $keptkayatype = '') 
    { 
        $request->session()->forget('keptkayatype'); 
        $orgId = Auth::user()->org_id_fk; 

        $schoolStats = $this->getCarbonSummary($orgId); 

        // แก้ไขจุด Join ใช้อ้างอิง user_id (หรือ kp_user_id_fk) ตรงกับ users.id
        $topStudents = User::join('kp_purchase_transactions', 'users.id', '=', 'kp_purchase_transactions.user_id') 
            ->select('users.firstname', 'users.lastname', DB::raw('SUM(kp_purchase_transactions.total_carbon_saved) as total_carbon')) 
            ->where('kp_purchase_transactions.org_id_fk', $orgId)
            ->where('kp_purchase_transactions.deleted', 0)
            ->whereHas('kpUserPreference', function($q) use ($orgId) {
                $q->where('org_id_fk', $orgId);
            })
            ->groupBy('users.id', 'users.firstname', 'users.lastname') 
            ->orderByDesc('total_carbon') 
            ->take(5) 
            ->get(); 

        $monthlyTrend = KpPurchaseTransaction::select( 
                DB::raw("DATE_FORMAT(transaction_date, '%Y-%m') as month"), 
                DB::raw('SUM(total_carbon_saved) as total_carbon') 
            ) 
            ->where('org_id_fk', $orgId)
            ->where('deleted', 0)
            ->where('transaction_date', '>=', now()->subMonths(6)) 
            ->groupBy('month') 
            ->orderBy('month') 
            ->get(); 

        $economicStats = [ 
            'total_money' => KpPurchaseTransaction::where('org_id_fk', $orgId)->where('deleted', 0)->sum('total_amount'), 
            'total_points' => KpPurchaseTransaction::where('org_id_fk', $orgId)->where('deleted', 0)->sum('total_points') 
        ]; 

        if ($economicStats['total_money'] == 0 && $economicStats['total_points'] == 0) { 
            $economicStats = [ 
                'total_money' => KpBankAccount::where('org_id_fk', $orgId)->sum('balance'), 
                'total_points' => KpBankAccount::where('org_id_fk', $orgId)->sum('points'), 
            ]; 
        } 

        $recentActivities = KpPurchaseTransaction::where('org_id_fk', $orgId) 
            ->where('deleted', 0)
            ->with('userWastePreference.user') 
            ->latest('created_at') 
            ->take(5) 
            ->get(); 

        $chartLabels = $schoolStats->pluck('material_name'); 
        $chartData = $schoolStats->pluck('total_carbon'); 

        $totalMembers = User::where('org_id_fk', $orgId) 
            ->whereHas('kpUserPreference', function($q) use ($orgId){ 
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
        $orgId = $orgId ?? Auth::user()->org_id_fk; 

        $query = KpPurchaseTransactionDetail::query() 
            ->join('kp_purchase_transactions', 'kp_purchase_transactions_details.kp_purchase_trans_id', '=', 'kp_purchase_transactions.id') 
            ->join('kp_tbank_items', 'kp_purchase_transactions_details.kp_recycle_item_id', '=', 'kp_tbank_items.id') 
            ->where('kp_purchase_transactions.org_id_fk', $orgId) 
            ->where('kp_purchase_transactions.deleted', 0) 
            ->select( 
                'kp_tbank_items.id',
                'kp_tbank_items.kp_itemsname as material_name', 
                DB::raw('SUM(kp_purchase_transactions_details.carbon_saved) as total_carbon'), 
                DB::raw('SUM(kp_purchase_transactions_details.amount_in_units) as total_weight') 
            ); 

        if ($userId) { 
            // เปลี่ยนคอลัมน์เงื่อนไข userId เป็น user_id (หรือ kp_user_id_fk)
            $query->where('kp_purchase_transactions.user_id', $userId); 
        } 

        return $query->groupBy('kp_tbank_items.id', 'kp_tbank_items.kp_itemsname') 
            ->orderByDesc('total_carbon') 
            ->get(); 
    } 
}