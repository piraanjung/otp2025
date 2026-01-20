<?php

namespace App\Http\Controllers\Tabwater;

use App\Http\Controllers\Controller;
use App\Exports\ReportOweUserExport;
use App\Exports\DailyReportExport;
use App\Exports\meterRecordHistoryExport;
use App\Http\Controllers\Api\FunctionsController;
use App\Models\Tabwater\TwInvoice;
use App\Models\Tabwater\TwInvoicePeriod;
use App\Models\Tabwater\TwMeterInfos;
use App\Models\Tabwater\TwCutmeter;
use App\Models\Admin\Subzone;
use App\Models\User;
use App\Models\Admin\Zone;
use App\Models\Admin\BudgetYear;
use App\Models\Admin\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class ReportsController extends Controller
{
    // =================================================================================
    // 1. REPORT OWE (รายงานหนี้ค้างชำระ - หน้าแรก)
    // =================================================================================

    public function owe(Request $request)
{
    $orgId = Auth::user()->org_id_fk;

    // 1. Prepare Filter Data
    $budgetyears = BudgetYear::with(['invoice_period:id,inv_p_name,budgetyear_id,status'])
        ->get(['id', 'budgetyear_name', 'status']);
    
    $zones = Zone::where('status', 'active')->get(['id', 'zone_name']);
    $subzones = Subzone::where('status', 'active')->get(['id', 'subzone_name']);

    // 2. Query Data (Simple Default View)
    $invoice_owe_status = TwInvoice::where("status", "owe")
        ->where('org_id_fk', $orgId) 
        ->with(['tw_meter_infos.user.user_zone', 'tw_meter_infos.user.user_subzone']) // Eager Load เพิ่มเพื่อลด Query ใน View
        ->get();

    // Default Variables
    $defaultData = [
        'budgetyears' => $budgetyears,
        'budgetyears_selected' => [], 
        'inv_periods' => [], 
        'selectedInvPeriodID' => [0],
        'zone_selected' => ['all'], 
        'subzone_selected' => ['all'], 
        'selected_inv_periods' => ['all'],
        'owe_zones' => [], 
        'zones' => $zones, 
        'subzones' => $subzones,
        'owe_inv_periods' => [], 
        'orgInfos' => Organization::getOrgName($orgId)
    ];

    if ($invoice_owe_status->isEmpty()) {
        return view("reports.owe", array_merge($defaultData, [
            'owes' => [],
            'reservemeter_sum' => 0, 
            'crudetotal_sum' => 0,
        ]));
    }

    // Map Data & Calculate
    $owesGrouped = $invoice_owe_status->groupBy('meter_id_fk');
    
    // *** ต้องมั่นใจว่า function mapOweData มีอยู่จริงใน Controller นี้นะครับ ***
    $owes = $this->mapOweData($owesGrouped); 
    
    $reservemeter_sum = $invoice_owe_status->where('inv_type', 'r')->sum('paid');
    $crudetotal_sum   = $invoice_owe_status->where('inv_type', 'u')->sum('paid');

    // Prepare View Variables (Get Active Year Safely)
    $activeBudgetYear = $budgetyears->where('status', 'active')->first();
    $budgetyears_selected = $activeBudgetYear ? [$activeBudgetYear->id] : [];
    
    $inv_periods = TwInvoicePeriod::whereIn('budgetyear_id', $budgetyears_selected)->get(['id', 'inv_p_name']);
    
    $owe_inv_periods = $invoice_owe_status->groupBy('inv_period_id_fk')->map(function ($group, $key) {
        return TwInvoicePeriod::select('id', 'inv_p_name')->find($key);
    })->sortByDesc('id');

    return view("reports.owe", array_merge($defaultData, compact(
        'owes', 'budgetyears_selected', 'inv_periods',
        'reservemeter_sum', 'crudetotal_sum', 'owe_inv_periods'
    )));
}

    // =================================================================================
    // 1.1 REPORT OWE SEARCH (ค้นหาหนี้ค้าง - จุดแก้ Performance หลัก)
    // =================================================================================

    public function owe_search(Request $request)
    {
        $orgId = Auth::user()->org_id_fk;

        // Get Filters
        $budget_ids  = $this->getFilterArray($request->get('budgetyear'));
        $subzone_ids = $this->getFilterArray($request->get('subzone'));
        $zone_ids    = $this->getFilterArray($request->get('zone'));
        $inv_p_ids   = $this->getFilterArray($request->get('inv_period'));

        // Query Builder
        $query = TwInvoice::query()
            ->where('org_id_fk', $orgId) // [SECURITY] Filter Org
            ->whereIn("status", ["owe", 'tw_invoices'])
            ->with(['usermeterinfos:meter_id,user_id,undertake_zone_id,undertake_subzone_id']);

        // [PERFORMANCE] Filter Invoice Period / Budget Year ใน SQL
        if (!empty($inv_p_ids)) {
            $query->whereIn("inv_period_id_fk", $inv_p_ids);
        } elseif (!empty($budget_ids)) {
            $query->whereHas('period', function($q) use ($budget_ids) {
                $q->whereIn('budgetyear_id', $budget_ids);
            });
        }

        // [PERFORMANCE] Filter Zone/Subzone ผ่าน Relation ใน SQL (แทนการ Filter PHP)
        if (!empty($zone_ids)) {
            $query->whereHas('usermeterinfos', function($q) use ($zone_ids) {
                $q->whereIn('undertake_zone_id', $zone_ids);
            });
        }
        if (!empty($subzone_ids)) {
            $query->whereHas('usermeterinfos', function($q) use ($subzone_ids) {
                $q->whereIn('undertake_subzone_id', $subzone_ids);
            });
        }

        // Execute Query
        $rawInvoices = $query->get();

        // Group & Map
        $grouped = $rawInvoices->groupBy('meter_id_fk');
        $owes = $this->mapOweData($grouped, true);

        // Sum
        $reservemeter_sum = $rawInvoices->sum('reserve_meter');
        $crudetotal_sum   = $rawInvoices->sum('paid');

        // Dropdowns & View Data
        $zones       = Zone::where('status', 'active')->get(['id', 'zone_name']);
        $subzones    = Subzone::where('status', 'active')->get(['id', 'subzone_name']);
        $budgetyears = BudgetYear::get(['id', 'budgetyear_name', 'status']);
        
        $periodQuery = TwInvoicePeriod::query();
        if(!empty($budget_ids)) {
            $periodQuery->whereIn('budgetyear_id', $budget_ids);
        }
        $inv_periods = $periodQuery->get(['id', 'inv_p_name']);

        $selected_inv_periods = TwInvoicePeriod::whereIn('id', $inv_p_ids)->get();
        if ($selected_inv_periods->isEmpty()) $selected_inv_periods = ['all'];

        $owe_inv_periods = $rawInvoices->groupBy('inv_period_id_fk')
            ->map(fn($g, $k) => TwInvoicePeriod::select('id', 'inv_p_name')->find($k))
            ->sortByDesc('id');

        // Excel Export
        if ($request->has('excelBtn')) {
            return Excel::download(new ReportOweUserExport([
                'owes' => $owes,
                'budgetyears_selected' => $budget_ids,
                'selected_inv_periods' => $selected_inv_periods,
                'reservemeter_sum' => $reservemeter_sum,
                'crudetotal_sum' => $crudetotal_sum,
                'owe_zones' => $zone_ids,
                'owe_inv_periods' => $owe_inv_periods,
                'zone_selected' => $zone_ids,
            ], $request->get('excelBtn') != 'overview'), 'รายงานการผู้ค้างชำระค่าน้ำประปา.xlsx');
        }

        return view("reports.owe", [
            'owes' => $owes,
            'budgetyears' => $budgetyears,
            'budgetyears_selected' => $budget_ids ?: [],
            'zone_selected' => $zone_ids ?: ['all'],
            'subzone_selected' => $subzone_ids ?: ['all'],
            'selectedInvPeriodID' => collect($selected_inv_periods)->pluck('id'),
            'inv_periods' => $inv_periods,
            'selected_inv_periods' => $selected_inv_periods,
            'reservemeter_sum' => $reservemeter_sum,
            'crudetotal_sum' => $crudetotal_sum,
            'owe_zones' => $request->get('zone') != 'all' ? $request->get('zone') : 'ทุกหมู่',
            'zones' => $zones,
            'subzones' => $subzones,
            'owe_inv_periods' => $owe_inv_periods,
            'orgInfos' => Organization::getOrgName($orgId)
        ]);
    }

    // Helper: Map Owe Data
    private function mapOweData($groupedCollection, $isSearch = false)
    {
        return $groupedCollection->map(function ($items) use ($isSearch) {
            $first = $items->first();
            return [
                'meter_id_fk'   => $first->meter_id_fk,
                'user_id'       => $first->user_id,
                'paid'          => $items->sum('paid'),
                'printed_time'  => $isSearch ? ($first->printed_time ?? 0) : TwCutmeter::where('meter_id_fk', $first->meter_id_fk)->where('status', '<>', 'deleted')->where('warning_print', 1)->count(),
                'vat'           => number_format($items->sum('vat'), 2),
                'totalpaid'     => number_format($items->sum('totalpaid'), 2),
                'reserve_meter' => number_format($items->sum('reserve_meter'), 2),
                'owe_count'     => $items->count(),
                'status'        => $first->status,
                'owe_infos'     => $items,
                'inv_period_id_fk' => $first->inv_period_id_fk, 
            ];
        });
    }

    private function getFilterArray($input)
    {
        if (empty($input) || in_array('all', (array)$input)) {
            return [];
        }
        return (array)$input;
    }

    // =================================================================================
    // 2. REPORT DAILY PAYMENT (รายงานรับชำระ - แก้ Connection Leak & N+1)
    // =================================================================================

    public function dailypayment(Request $request)
    {
        // [FIX] เอา ManagesTenantConnection ออก
        $orgId = Auth::user()->org_id_fk;
        
        if ($request->has('excel')) {
            return $this->export($request);
        }

        $fnCtrl = new FunctionsController();
        
        // Defaults
        if (!$request->has('nav') || $request->get('nav') == 'nav') {
            $activePeriod = TwInvoicePeriod::where('status', 'active')->first(['id', 'budgetyear_id']);
            $request->merge([
                'zone_id' => 'all', 'subzone_id' => 'all', 'acc_trans_id_fk' => 'all',
                'cashier_selected' => 0, 'cashier_id' => 'all', 'inv_period_id' => 'all',
                'budgetyear_id' => $activePeriod->budgetyear_id ?? null,
                'fromdate' => date('Y-m-d'), 'todate' => date('Y-m-d')
            ]);
            $fromdateTh = $fnCtrl->engDateToThaiDateFormat(date('Y-m-d'));
            $todateTh   = $fnCtrl->engDateToThaiDateFormat(date('Y-m-d'));
        } else {
            $fromdateTh = $request->get('fromdate');
            $todateTh   = $request->get('todate');
            $request->merge([
                'fromdate' => $fnCtrl->thaiDateToEngDateFormat($request->get('fromdate')),
                'todate'   => $fnCtrl->thaiDateToEngDateFormat($request->get('todate')),
            ]);
        }

        // Fetch Data (Refactored)
        $paidInfos = $this->getDailyPaymentData($request, $orgId);

        // Prepare View
        $zones       = Zone::all();
        $subzones    = ($request->zone_id != 'all') ? Subzone::all() : 'all';
        $budgetyears = BudgetYear::all();
        $inv_periods = TwInvoicePeriod::where('budgetyear_id', $request->budgetyear_id)->orderByDesc('id')->get(['id', 'inv_p_name']);
        
        // [Security] ดึงเฉพาะ User ใน Org เดียวกัน
$receiptions = User::where('org_id_fk', $orgId)
    ->whereHas('roles', function ($query) {
        // เลือกเอาว่าจะเช็คจาก ID หรือ ชื่อ (แนะนำชื่อจะอ่านรู้เรื่องกว่า)
        
        // กรณีเช็คจากชื่อ Role
        $query->whereIn('name', ['Admin', 'Finance Staff']); 
        
        // หรือ กรณีเช็คจาก ID (ถ้าคุณมั่นใจว่า 1=Admin, 2=Finance)
        // $query->whereIn('id', [1, 2]); 
    })
    ->get(['id', 'lastname', 'firstname']);        
        $cashierName = 'ทั้งหมด';
        if ($request->cashier_id != 'all') {
             $c = User::find($request->cashier_id);
             $cashierName = $c ? $c->firstname . ' ' . $c->lastname : '-';
        }

        $request_selected = [
            'budgeryear' => BudgetYear::where('id', $request->budgetyear_id)->pluck('budgetyear_name'),
            'inv_period' => $request->inv_period_id == 'all' ? ['ทั้งหมด'] : TwInvoicePeriod::where('id', $request->inv_period_id)->pluck('inv_p_name'),
            'zone'       => $request->zone_id == 'all' ? ['ทั้งหมด'] : Zone::where('id', $request->zone_id)->pluck('zone_name'),
            'subzone'    => $request->subzone_id == 'all' ? ['ทั้งหมด'] : Subzone::where('id', $request->subzone_id)->pluck('subzone_name'),
            'cashier'    => [['id' => $request->cashier_id, 'firstname' => $cashierName, 'lastname' => '']]
        ];

        return view('reports.dailypayment', compact(
            'zones', 'subzones', 'paidInfos', 'receiptions', 
            'fromdateTh', 'todateTh', 'budgetyears', 'inv_periods', 
            'request_selected'
        ) + [
            'subzone_id' => $request->subzone_id,
            'zone_id'    => $request->zone_id,
            'todate'     => date('d/m/Y', strtotime($request->todate)),
            'fromdate'   => date('d/m/Y', strtotime($request->fromdate)),
            'inv_period_id' => $request->inv_period_id,
            'cashier' => null,
            'orgInfos' => Organization::getOrgName($orgId)
        ]);
    }

    private function getDailyPaymentData($request, $orgId)
    {
        // [PERFORMANCE] Query Optimization
        // ใช้ whereHas Invoice แทนการ Loop Filter
        // ใช้ Filter Org ผ่าน User

        $query = TwMeterInfos::query()
            ->whereHas('user', function($q) use ($orgId) {
                $q->where('org_id_fk', $orgId); // [Security] Check Org
            })
            ->with([
                'tw_invoices' => function ($q) use ($request) {
                    $q->select('id', 'meter_id_fk', 'inv_no', 'acc_trans_id_fk', 'updated_at', 
                               'inv_period_id_fk', 'lastmeter', 'currentmeter', 'water_used', 
                               'paid', 'vat', 'reserve_meter', 'totalpaid', 'status')
                      ->whereBetween("updated_at", [$request->fromdate . " 00:00:00", $request->todate . " 23:59:59"])
                      ->where('status', 'paid');
    
                    if ($request->inv_period_id != 'all') {
                        $q->where('inv_period_id_fk', $request->inv_period_id);
                    }
                    
                    // Filter Cashier ที่ Invoice Level (ถ้าจำเป็นต้องเช็คที่นี่)
                    if ($request->cashier_id != 'all') {
                        $q->whereHas('tw_acc_transactions', function($transQ) use ($request) {
                            $transQ->where('cashier', $request->cashier_id);
                        });
                    }
                },
                'tw_invoices.tw_acc_transactions:id,cashier',
                'tw_invoices.tw_acc_transactions.cashier_info:id,firstname,lastname'
            ]);

        // Filter Zone/Subzone
        $query->when($request->zone_id != 'all', fn($q) => $q->where('undertake_zone_id', $request->zone_id));
        $query->when($request->subzone_id != 'all', fn($q) => $q->where('undertake_subzone_id', $request->subzone_id));
        
        $query->where('status', 'active');
        
        // [CRITICAL FIX] ใช้ whereHas เพื่อดึงเฉพาะ Meter ที่มีบิลจ่ายเงินในช่วงเวลานั้นจริงๆ
        // ไม่เช่นนั้น มันจะดึง Meter ทั้งหมดออกมา แล้วค่อยมา Filter ว่างทีหลัง ซึ่งช้ามาก
        $query->whereHas('tw_invoices', function ($q) use ($request) {
            $q->whereBetween("updated_at", [$request->fromdate . " 00:00:00", $request->todate . " 23:59:59"])
              ->where('status', 'paid');
            
            if ($request->inv_period_id != 'all') {
                $q->where('inv_period_id_fk', $request->inv_period_id);
            }
            if ($request->cashier_id != 'all') {
                $q->whereHas('tw_acc_transactions', fn($tq) => $tq->where('cashier', $request->cashier_id));
            }
        });

        return $query->get(['meter_id', 'user_id', 'meter_address', 'submeter_name', 'undertake_zone_id', 'status', 'updated_at']);
    }

    public function export(Request $request)
    {
        $dateStr = str_replace("/", "-", $request->get('fromdate')) . ' ถึง ' . str_replace("/", "-", $request->get('todate'));
        return Excel::download(new DailyReportExport($request), "รายงานการรับชำระค่าน้ำประจำวันที่ {$dateStr}.xlsx");
    }

    // =================================================================================
    // 3. REPORT METER HISTORY
    // =================================================================================
    
    public function meter_record_history(Request $request)
{
    // 1. Timezone ควรตั้งใน config/app.php แต่ถ้าจำเป็นจริงๆ ให้ใช้ Carbon จะดีกว่า
    // date_default_timezone_set('Asia/Bangkok'); 
    
    $orgId = Auth::user()->org_id_fk;

    // 2. Default Values (ใช้ when หรือ check ดีกว่าการ query ใน default parameter)
    $activeBudgetIds = BudgetYear::where('status', 'active')->pluck('id')->toArray();
    $budget_ids = $request->get('budgetyear', $activeBudgetIds);
    
    // ตรวจสอบว่าเป็น array หรือไม่ ป้องกัน error
    if(!is_array($budget_ids)) $budget_ids = [$budget_ids];

    $zone_ids = $request->get('zone', ['all']);
    if(!is_array($zone_ids)) $zone_ids = [$zone_ids];

    // Master Data
    $zones = Zone::all();
    $budgetyears = BudgetYear::select('id', 'budgetyear_name', 'status')->get();
    $inv_periods = TwInvoicePeriod::whereIn('budgetyear_id', $budget_ids)
                    ->orderBy('id', 'asc')
                    ->get();

    // 3. Main Query
    $query = TwMeterInfos::query()
        ->whereHas('user', function($q) use ($orgId) {
            $q->where('users.org_id_fk', $orgId); 
        })
        // แก้ไข N+1: Load user_zone และ user_subzone มาด้วยเลย
        ->with([
            'tw_invoices' => fn($q) => $this->historySubQuery($q, $budget_ids, 'tw_invoice'),
            'tw_invoice_history' => fn($q) => $this->historySubQuery($q, $budget_ids, 'tw_invoice_history'),
            'user' => function($q) {
                $q->select('id', 'prefix', 'firstname', 'lastname', 'zone_id', 'subzone_id', 'address')
                  ->with(['user_zone:id,zone_name', 'user_subzone:id,subzone_name']); 
            }
        ]);

    if (!in_array("all", $zone_ids)) {
        $query->whereIn('undertake_zone_id', $zone_ids);
    }

    // Load เฉพาะ Active
    $results = $query->where('status', '<>', 'deleted')->get();

    // 4. Transform Data
    // การใช้ map จะ clean กว่า foreach และไม่กระทบ object เดิมโดยตรง
    $usermeterinfos = $results->map(function ($meter) use ($inv_periods) {
        $allInvoices = collect($meter->tw_invoices)->merge($meter->invoice_history);
        $mappedPeriods = [];
        $sumCurrentMeter = 0;

        foreach ($inv_periods as $period) {
            $match = $allInvoices->firstWhere('inv_period_id_fk', $period->id);
            $current = $match->currentmeter ?? 0;
            $sumCurrentMeter += $current;

            $mappedPeriods[] = [
                'id' => $period->id,
                'inv_p_name' => $period->inv_p_name,
                'lastmeter' => $match->lastmeter ?? 0,
                'currentmeter' => $current,
                'water_used' => $match->water_used ?? 0
            ];
        }

        // Attach ข้อมูลเพิ่มเข้าไปใน Object ชั่วคราว (หรือจะสร้าง Array ใหม่ก็ได้)
        $meter->infos = $mappedPeriods;
        $meter->bringForward = $mappedPeriods[0]['lastmeter'] ?? 0; // ยอดยกมา
        $meter->sumCurrentMeter = $sumCurrentMeter;
        
        return $meter;
    })->filter(function ($meter) {
        // Filter ตรงนี้
        return $meter->sumCurrentMeter > 0 && $meter->user;
    });

    // 5. Export Logic
    if ($request->get('submitBtn') == 'export_excel') {
        return Excel::download(new meterRecordHistoryExport([
            'usermeterinfos' => $usermeterinfos,
            'inv_period_list' => $inv_periods,
            // ... parameters อื่นๆ
        ]), 'รายงานสมุดจดเลขอ่านมาตรวัดน้ำ(ป.31).xlsx');
    }

    return view('reports.meter_record_history', [
        'usermeterinfos' => $usermeterinfos,
        'inv_period_list' => $inv_periods,
        'zones' => $zones,
        'budgetyears' => $budgetyears,
        'budgetyear_selected_array' => $budget_ids,
        'zone_id_array' => $zone_ids
    ]);
}

    private function historySubQuery($q, $budget_ids, $table)
    {
        // 1. [Fix Ambiguous] ระบุชื่อตารางนำหน้า org_id_fk
        // เพื่อไม่ให้ชนกับ org_id_fk ของตาราง users หรือ meter_infos
        $q->where($table . '.org_id_fk', Auth::user()->org_id_fk);

        // 2. [Restore Logic] กรองเฉพาะ Invoice ที่อยู่ในปีงบประมาณที่เลือก
        // โดยการเช็คว่า inv_period_id_fk (รหัสงวด) ต้องอยู่ในลิสต์ของปีงบประมาณนั้นๆ
        if (!empty($budget_ids)) {
            $q->whereIn('inv_period_id_fk', function($sq) use ($budget_ids) {
                $sq->select('id')
                   ->from('invoice_period') // ชื่อตารางงวดเดือน
                   ->whereIn('budgetyear_id', $budget_ids);
            });
        }
        
        // (Optional) เลือกเฉพาะฟิลด์ที่จำเป็นเพื่อความเร็ว
        // $q->select($table.'.*'); 

        return $q;
    }

    // =================================================================================
    // 4. REPORT WATER USED
    // =================================================================================

 public function water_used(Request $request, $from = "")
{
    $orgId = Auth::user()->org_id_fk;

    // 1. จัดการ Default Parameters ให้กระชับและปลอดภัยขึ้น
    if (!$request->filled('budgetyear_id')) {
        // ใช้ value() หรือ active() scope ถ้ามี
        $activeId = BudgetYear::where('status', 'active')->value('id'); 
        
        // ถ้าไม่มีปีงบประมาณ Active เลย ให้หยุดทำงานหรือ Handle error
        if (!$activeId && $from != 'dashboard') {
             return redirect()->back()->with('error', 'ไม่พบปีงบประมาณที่เปิดใช้งาน');
        }

        $request->merge([
            'budgetyear_id' => $activeId, 
            'zone_id' => $request->get('zone_id', 'all'), // ใช้ค่าเดิมถ้ามี หรือ default 'all'
            'subzone_id' => $request->get('subzone_id', 'all')
        ]);
    }
    
    // 2. Load BudgetYear
    $selected_budgetYear = BudgetYear::with('invoice_period:budgetyear_id,id')
        ->find($request->budgetyear_id);
    
    // Handle กรณีไม่เจอข้อมูล
    if (!$selected_budgetYear) {
         return $from == 'dashboard' 
            ? [] 
            : view('reports.water_used', ['data' => [], 'waterUsedDataTables' => []]);
    }

    $invPeriodIds = $selected_budgetYear->invoice_period->pluck('id')->toArray(); // แปลงเป็น Array ให้ชัวร์

    // 3. Query ข้อมูล (Database Logic)
    // หมายเหตุ: ถ้าข้อมูลเยอะ แนะนำให้ทำ Union ใน SQL แทนการ Merge Collection
    $invData = $this->buildWaterUsedQuery('tw_invoice', $invPeriodIds, $request, $orgId)->get();
    $histData = $this->buildWaterUsedQuery('tw_invoice_history', $invPeriodIds, $request, $orgId)->get();

    // รวมข้อมูลและเรียงลำดับ
    $waterUsed = $invData->merge($histData)->sortBy('undertake_zone_id');

    // 4. Process Data (Business Logic)
    $data = $this->processWaterUsedData($waterUsed, $request->budgetyear_id);

    // 5. Response
    if ($from == 'dashboard') {
        return $data['chart'];
    }

    // Load list ปีงบประมาณมาแสดงใน Dropdown (เฉพาะที่ไม่ถูกลบ)
    $budgetyears = BudgetYear::where('status', '<>', 'deleted')
                    ->orderBy('id', 'desc') // เรียงปีล่าสุดขึ้นก่อน
                    ->select('id', 'budgetyear_name')
                    ->get();

    return view('reports.water_used', [
        'data' => $data['chart'],
        'waterUsedDataTables' => $data['table'],
        'zone_and_subzone_selected_text' => $this->getZoneText($request),
        'selected_budgetYear' => $selected_budgetYear,
        'budgetyears' => $budgetyears
    ]);
}

    // Helper: Build SQL for Water Used (DB facade doesn't use Trait, so we need explicit check)
    private function buildWaterUsedQuery($table, $periodIds, $request, $orgId)
    {
        $query = DB::table('tw_meter_infos as umf')
            ->join("$table as inv", 'inv.meter_id_fk', '=', 'umf.meter_id')
            ->join('invoice_period as ivp', 'ivp.id', '=', 'inv.inv_period_id_fk')
            ->join('budget_year as bgy', 'bgy.id', '=', 'ivp.budgetyear_id')
            ->join('zones as z', 'z.id', '=', 'umf.undertake_zone_id')
            ->join('users as u', 'u.id', '=', 'umf.user_id')
            ->select(
                'inv.inv_period_id_fk', 'ivp.inv_p_name', 'umf.undertake_zone_id',
                'inv.water_used', 'z.zone_name', 'z.id as zone_id',
                'bgy.id as budgetyear_id', 'u.org_id_fk'
            )
            ->whereIn('inv.inv_period_id_fk', $periodIds)
            ->where('u.org_id_fk', $orgId); // [Security] Explicit Org Check

        if ($request->zone_id != 'all') {
            if ($request->subzone_id != 'all') {
                $query->where('umf.undertake_subzone_id', $request->subzone_id);
            } else {
                $query->where('umf.undertake_zone_id', $request->zone_id);
            }
        }

        return $query;
    }

    private function processWaterUsedData($collection, $budgetYearId)
    {
        $grouped = $collection->groupBy('undertake_zone_id');
        $labels = [];
        $values = [];
        $tables = [];

        $allPeriodNames = TwInvoicePeriod::where('budgetyear_id', $budgetYearId)->pluck('inv_p_name');

        foreach ($grouped as $zoneId => $zoneData) {
            $first = $zoneData->first();
            $labels[] = $first->zone_name;
            $values[] = $zoneData->sum('water_used');

            $classified = [];
            foreach($allPeriodNames as $index => $pName) {
                $realData = $zoneData->filter(fn($d) => $d->inv_p_name == $pName);
                $classified[] = [
                    'id' => $realData->first()->inv_period_id_fk ?? ($index + 1),
                    'inv_p_name' => $pName,
                    'water_used' => $realData->sum('water_used')
                ];
            }

            $tables[] = [
                'zone_id' => $zoneId,
                'zone_name' => $first->zone_name,
                'water_used' => $zoneData->sum('water_used'),
                'classify_by_inv_period' => collect($classified)
            ];
        }

        return ['chart' => ['labels' => $labels, 'data' => $values], 'table' => $tables];
    }

    private function getZoneText($request)
    {
        $text = 'ทั้งหมด';
        if ($request->zone_id != 'all') {
            $z = Zone::find($request->zone_id);
            $text .= ' ' . ($z->zone_name ?? '');
            if ($request->subzone_id != 'all') {
                $s = Subzone::find($request->subzone_id);
                $text .= ' เส้นทางจัดเก็บ ' . ($s->subzone_name ?? '');
            }
        }
        return $text;
    }
}