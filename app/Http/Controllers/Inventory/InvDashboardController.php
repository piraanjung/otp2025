<?php

namespace App\Http\Controllers\Inventory;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InvItem;
use App\Models\InvItemDetail;
use App\Models\InvTransaction;
use App\Models\InvCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InvDashboardController extends Controller
{
    public function index()
{
    $user = Auth::user();
    $orgId = $user->org_id_fk;

    // 1. ตัวเลขสรุป (Cards)
    $totalItems = InvItem::where('org_id_fk', $orgId)->count();

    $totalBottles = InvItemDetail::whereHas('item', function($q) use ($orgId) {
        $q->where('org_id_fk', $orgId);
    })->where('status', 'ACTIVE')->count();

    $expiringSoon = InvItemDetail::whereHas('item', function($q) use ($orgId) {
        $q->where('org_id_fk', $orgId);
    })
    ->where('status', 'ACTIVE')
    ->whereDate('expire_date', '<=', Carbon::now()->addDays(30))
    ->count();

    // ➕ นับจำนวนตามสถานะการเบิกจ่าย
    $pendingCount = InvTransaction::where('org_id_fk', $orgId)->where('status', 'PENDING')->count();
    $approvedCount = InvTransaction::where('org_id_fk', $orgId)->where('status', 'APPROVED')->count();
    $rejectedCount = InvTransaction::where('org_id_fk', $orgId)->where('status', 'REJECTED')->count();

    // 2. รายการเคลื่อนไหวล่าสุด 5 รายการ
    $recentTransactions = InvTransaction::where('org_id_fk', $orgId)
                                        ->with(['item', 'user'])
                                        ->latest('transaction_date')
                                        ->take(5)
                                        ->get();

    // 3. ข้อมูลกราฟ (แยกตามหมวดหมู่)
    $chartData = InvItem::where('org_id_fk', $orgId)
        ->select('inv_category_id_fk', DB::raw('count(*) as total'))
        ->groupBy('inv_category_id_fk')
        ->with('category')
        ->get();

    $labels = [];
    $data = [];
    foreach ($chartData as $row) {
        $labels[] = $row->category->name ?? 'Uncategorized';
        $data[] = $row->total;
    }

    // ➕ ส่งตัวแปรทั้งหมดไปที่ View
    return view('inventory.inv_dashboard', compact(
        'totalItems', 'totalBottles', 'expiringSoon',
        'pendingCount', 'approvedCount', 'rejectedCount',
        'recentTransactions', 'labels', 'data'
    ));
}
}
