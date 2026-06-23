<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AnnualTrashSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WasteFinancialReportController extends Controller
{
    public function monthlySummary(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);

        // 1. สรุปจำนวนคนตามสถานะ
        $statusSummary = AnnualTrashSubscription::select('billing_status', DB::raw('count(*) as total'))
            ->groupBy('billing_status')
            ->get();

        // 2. คำนวณยอดเงินรวม (ที่ควรจะเก็บได้)
        $totalPotentialIncome = AnnualTrashSubscription::sum('monthly_fee');

        // 3. คำนวณมูลค่า "สวัสดิการ" ที่เทศบาลลดหย่อนให้ประชาชน (Waived)
        $totalWaivedValue = AnnualTrashSubscription::where('billing_status', 'waived')
            ->sum('monthly_fee');

        // 4. คำนวณยอดเงินที่ต้องเก็บจริง (Pending + Paid)
        $expectedCash = $totalPotentialIncome - $totalWaivedValue;

        return view('admin.reports.financial', compact(
            'statusSummary',
            'totalPotentialIncome',
            'totalWaivedValue',
            'expectedCash',
            'month',
            'year'
        ));
    }
}
