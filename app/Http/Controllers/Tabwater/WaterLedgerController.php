<?php

namespace App\Http\Controllers\Tabwater;

use App\Models\User;
use App\Models\Tabwater\TwInvoice;
use App\Models\Tabwater\TwInvoicePeriod;
use App\Exports\P17ReportExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;
use App\Models\Tabwater\TwMeterInfos;

class WaterLedgerController extends Controller
{
    public function p17Report(Request $request)
    {
        $periods = TwInvoicePeriod::orderBy('id', 'desc')->get();
        $periodId = $request->input('inv_period_id_fk');
        $search = $request->input('search');
        $userId = $request->input('user_id');
        $meterNumber = $request->input('meternumber');

        // 1. ค้นหา User ตามเงื่อนไข
        $userQuery = User::query();
        if ($userId) $userQuery->where('id', $userId);
        if ($meterNumber) {
            $userQuery->whereHas('usermeterinfos', fn($q) => $q->where('meternumber', 'LIKE', "%$meterNumber%"));
        }
        if ($search) {
            $userQuery->where(function ($q) use ($search) {
                $q->where(DB::raw("CONCAT(COALESCE(prefix,''), COALESCE(firstname,''), ' ', COALESCE(lastname,''))"), 'LIKE', "%$search%")
                  ->orWhere('firstname', 'LIKE', "%$search%")
                  ->orWhere('lastname', 'LIKE', "%$search%");
            });
        }
        
        $users = $userQuery->paginate(20)->withQueryString();
        $paginateUserIds = $users->pluck('id');

        // 2. ดึง Invoices ของ Users เหล่านี้
        $invoicesQuery = TwInvoice::with(['tw_meter_infos.user', 'tw_acc_transactions', 'invoice_period'])
            ->whereHas('tw_meter_infos', fn($q) => $q->whereIn('user_id', $paginateUserIds));

        // Logic เลือกงวด: Default คือบิลล่าสุดของมิเตอร์นั้นๆ
        if ($periodId) {
            $invoicesQuery->where('inv_period_id_fk', $periodId);
        } else {
            $invoicesQuery->whereIn('id', function ($sub) {
                $sub->select(DB::raw('MAX(id)'))->from('tw_invoice')->groupBy('meter_id_fk');
            });
        }

        $invoices = $invoicesQuery->get();

        // 3. คำนวณตัวเลข
        foreach ($invoices as $invoice) {
            // A. หนี้เก่าสะสม (ที่ไม่ใช่บิลนี้)
            $previousUnpaidSum = TwInvoice::where('meter_id_fk', $invoice->meter_id_fk)
                ->where('id', '<', $invoice->id) // ใช้ ID แทน Period เพื่อความแม่นยำของลำดับ
                ->where(fn($q) => $q->where('status', '!=', 'paid')->orWhereNull('acc_trans_id_fk'))
                ->sum('totalpaid');

            $invoice->computed_brought_forward = $previousUnpaidSum;
            $invoice->computed_total_due = $invoice->totalpaid + $invoice->computed_brought_forward;

            // B. ยอดจ่าย: ถ้ามี acc_trans_id_fk ให้ Sum ทั้งกลุ่ม
            if (!empty($invoice->acc_trans_id_fk)) {
                $invoice->computed_paid_amount = TwInvoice::where('acc_trans_id_fk', $invoice->acc_trans_id_fk)->sum('totalpaid');
                $invoice->computed_pay_date = $invoice->tw_acc_transactions->updated_at ? \Carbon\Carbon::parse($invoice->tw_acc_transactions->updated_at)->format('d/m/Y') : '-';
                $invoice->computed_cashbook_page = $invoice->tw_acc_transactions->cashbook_page ?? '-';
            } else {
                $invoice->computed_paid_amount = ($invoice->status == 'paid') ? $invoice->totalpaid : 0;
                $invoice->computed_pay_date = '-';
                $invoice->computed_cashbook_page = '-';
            }

            // C. คงค้างยกไป = (หนี้รวม - จ่าย)
            $invoice->computed_carried_forward = max(0, $invoice->computed_total_due - $invoice->computed_paid_amount);
        }

        return view('reports.p17', compact('invoices', 'users', 'periods', 'periodId', 'search', 'userId', 'meterNumber'));
    }
}