<?php

namespace App\Http\Controllers;

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
        $totalItems = InvItem::where('org_id_fk', $orgId)->count(); // จำนวน SKU ทั้งหมด
        
        $totalBottles = InvItemDetail::whereHas('item', function($q) use ($orgId) {
            $q->where('org_id_fk', $orgId);
        })->where('status', 'ACTIVE')->count(); // จำนวนขวดที่มีของ
        
        // ของใกล้หมดอายุใน 30 วัน
        $expiringSoon = InvItemDetail::whereHas('item', function($q) use ($orgId) {
            $q->where('org_id_fk', $orgId);
        })
        ->where('status', 'ACTIVE')
        ->whereDate('expire_date', '<=', Carbon::now()->addDays(30))
        ->count();

        // 2. รายการเคลื่อนไหวล่าสุด 5 รายการ (Recent Activity)
        $recentTransactions = InvTransaction::where('org_id_fk', $orgId)
                                            ->with(['item', 'user'])
                                            ->latest('transaction_date')
                                            ->take(5)
                                            ->get();

        // 3. ข้อมูลกราฟ (แยกตามหมวดหมู่)
        // ดึงข้อมูลว่าแต่ละหมวดหมู่ มีพัสดุกี่รายการ
        $chartData = InvItem::where('org_id_fk', $orgId)
            ->select('inv_category_id_fk', DB::raw('count(*) as total'))
            ->groupBy('inv_category_id_fk')
            ->with('category')
            ->get();

        // จัด Format ส่งให้ Chart.js (แยกชื่อหมวดหมู่ กับ ตัวเลข)
        $labels = [];
        $data = [];
        foreach ($chartData as $row) {
            $labels[] = $row->category->name ?? 'Uncategorized';
            $data[] = $row->total;
        }

        return view('inventory.inv_dashboard', compact(
            'totalItems', 'totalBottles', 'expiringSoon', 
            'recentTransactions', 'labels', 'data'
        ));
    }
}