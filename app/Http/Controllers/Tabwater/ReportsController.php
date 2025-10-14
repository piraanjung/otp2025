<?php

namespace App\Http\Controllers\Tabwater;

use App\Http\Controllers\Controller;
use App\Exports\ReportOweUserExport;
use App\Http\Controllers\Api\FunctionsController;
use App\Models\Tabwater\TwInvoiceTemp;
use App\Models\Tabwater\TwInvoicePeriod;
use App\Models\Admin\Subzone;
use App\Models\User;
use App\Models\Admin\Zone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Admin\BudgetYear;
use App\Models\Tabwater\TwUsersInfo;
use App\Exports\DailyReportExport;
use App\Exports\meterRecordHistoryExport;
use App\Exports\ReportOweUser;
use Exception;
use Maatwebsite\Excel\Exporter;
use Maatwebsite\Excel\Facades\Excel;
use PhpParser\Node\Expr\FuncCall;

class ReportsController extends Controller
{
    public function owe(Request $request)
    {
    
        $invoice_owe_status = TwInvoiceTemp::where("status", "owe")->get([
            'id',
            'meter_id_fk',
            'currentmeter',
            'lastmeter',
            'inv_no',
            'water_used',
            'inv_period_id_fk',
            'paid',
            'inv_type',
            'vat',
            'totalpaid',
            'updated_at',
            'status'
        ])->groupBy(['meter_id_fk'])->values();


        $budgetyears = (new BudgetYear())->setConnection('envsogo_'.strtolower(session('org_code')))->with([
            'invoicePeriod' => function ($q) {
                return $q->select('id', 'inv_p_name', 'budgetyear_id', 'status');
            }
        ])->get(['id', 'budgetyear_name', 'status']);

        if (collect($invoice_owe_status)->isEmpty()) {
            $owes = [];
            return view("reports.owe", compact('owes'));
        }

        //ทำการ map เพื่อการ set ค่าไปแสดงในหน้า view

        $owes = collect($invoice_owe_status)->map(function ($ar, $k) {
            if (!isset($ar[0]->usermeterinfos->user->user_subzone)) {
                dd($ar[0]->usermeterinfos->user);
            }

            return [
                'meter_id_fk' => $ar[0]->meter_id_fk,
                'user_id' => $ar[0]->user_id,
                'paid' => collect($ar)->sum('paid'),
                'printed_time' => $ar[0]->printed_time,
                'vat' => number_format(collect($ar)->sum('vat'), 2),
                'totalpaid' => number_format(collect($ar)->sum('totalpaid'), 2),
                'owe_count' => collect($ar)->count(),
                'status' => $ar[0]->status,
                'owe_infos' => $ar,
            ];
        });

        $reservemeter_sum = collect($invoice_owe_status)->sum(function ($arr) {
            return collect($arr)->sum(function ($ar) {
                return $ar['inv_type'] == "r" ? $ar['paid'] : 0;
            });
        });

        $crudetotal_sum = collect($invoice_owe_status)->sum(function ($arr) {
            return collect($arr)->sum(function ($ar) {
                return $ar['inv_type'] == "u" ? $ar['paid'] : 0;
            });
        });
        $zones = Zone::where('status', 'active')->get(['id', 'zone_name']);
        $subzones = Subzone::where('status', 'active')->get(['id', 'subzone_name']);
        $owe_zones =  [];

        $budgetyears_selected = [];
        if (collect($budgetyears_selected)->isEmpty()) {
            $budgetyear = collect($budgetyears)->filter(function ($b) {
                return $b->status == 'active';
            })->values();

            $budgetyears_selected[] = $budgetyear[0]->id;
            $inv_periods = TwInvoicePeriod::whereIn('budgetyear_id', $budgetyears_selected)->get(['id', 'inv_p_name']);
        }

        $owe_inv_periods =  collect($invoice_owe_status)->groupBy(['inv_period_id_fk'])->map(function ($arr, $key) {
            return  TwInvoicePeriod::where('id', $key)->first(['id', 'inv_p_name']);
        })->sortByDesc('id');
        $selected_inv_periods = ['all'];
        $selectedInvPeriodID = [0];
        $zone_selected =['all'];
        $subzone_selected =['all'];
        return view("reports.owe", compact('owes', 'budgetyears', 'budgetyears_selected', 'inv_periods', 
        'selectedInvPeriodID', 'zone_selected', 'subzone_selected',
        'selected_inv_periods', 'reservemeter_sum', 'crudetotal_sum', 'owe_zones', 'zones', 'subzones', 'owe_inv_periods'));
    }
    public function owe_search(Request $request)
    {
        $budgetyears_selected = collect($request->get('budgetyear'))->isEmpty() || in_array("all", $request->get('budgetyear')) ? []
            : $request->get('budgetyear');
        $subzone_selected     = collect($request->get('subzone'))->isEmpty() || in_array("all", $request->get('subzone')) ? ['all']
            : $request->get('subzone');
        $zone_selected        = collect($request->get('zone'))->isEmpty() || in_array("all", $request->get('zone')) ? ['all']
            : $request->get('zone');
        $inv_period_selected  = collect($request->get('inv_period'))->isEmpty() || in_array("all", $request->get('inv_period')) ? ['all']
            : $request->get('inv_period');

        $invoice_owe_status = TwInvoiceTemp::whereIn("status", ["owe", 'invoice'])
        ->with([
            'usermeterinfos' => function($q){
                return $q->select('meter_id', 'user_id', 'undertake_zone_id', 'undertake_subzone_id');
            }
        ]);
        if ($inv_period_selected[0] != 'all') {
            //เลือกค้นหาโดยเลือกรอบบิล
            $invoice_owe_status = $invoice_owe_status->whereIn("inv_period_id_fk", $inv_period_selected);
        } else {
            //เลือกค้นหาเฉพาะปีงบประมาณ
            $inv_period_arr = TwInvoicePeriod::whereIn('budgetyear_id', $budgetyears_selected)->get(['id']);
            $inv_period_selected_pluck = collect($inv_period_arr)->pluck('id');
            $invoice_owe_status = $invoice_owe_status->whereIn("inv_period_id_fk", $inv_period_selected_pluck);
        }
        $invoice_owe_status = $invoice_owe_status->get([
            'id',
            'meter_id_fk',
            'currentmeter',
            'lastmeter',
            'water_used',
            'reserve_meter',
            'inv_period_id_fk',
            'paid',
            'inv_type',
            'vat',
            'status',
            'totalpaid',
            'updated_at'
        ])->values()->groupBy('meter_id_fk');

       
        if ($zone_selected[0] != 'all') {
                $invoice_owe_status = collect($invoice_owe_status)->filter(function ($c) use ($zone_selected) {
                return in_array($c[0]->usermeterinfos->undertake_zone_id, $zone_selected);
            });
        }
        
        if ($subzone_selected[0] != 'all') {
            $invoice_owe_status = collect($invoice_owe_status)->filter(function ($item) use ($subzone_selected) {
                return in_array($item[0]->usermeterinfos->undertake_subzone_id, $subzone_selected);
            });
        }
        if ($inv_period_selected[0] != 'all') {
            $invoice_owe_status = collect($invoice_owe_status)->filter(function ($item) use ($inv_period_selected) {
                return in_array($item[0]->inv_period_id_fk, collect($inv_period_selected)->toArray());
            });
        }
      
        $showDetails = $request->get('excelBtn')=='overview' ? false : true;

      
        $owes = collect($invoice_owe_status)->map(function ($ar, $k){
            
                return [
                    'owe_infos' => $ar,
                    'meter_id_fk' => $ar[0]->meter_id_fk,
                    'user_id' => $ar[0]->user_id,
                    'printed_time' => $ar[0]->printed_time,
                    'status' => $ar[0]->status,
                    'inv_period_id_fk' => $ar[0]->inv_period_id_fk,
                    'paid' => collect($ar)->sum('paid'),
                    'vat' => number_format(collect($ar)->sum('vat'), 2),
                    'reserve_meter' => number_format(collect($ar)->sum('reserve_meter'), 2),
                    'totalpaid' => number_format(collect($ar)->sum('totalpaid'), 2),
                    'owe_count' => collect($ar)->count(),    
                ];
        });

        $reservemeter_sum = collect($invoice_owe_status)->sum(function ($arr) {
            return collect($arr)->sum('reserve_meter');

        });

        $crudetotal_sum = collect($invoice_owe_status)->sum(function ($arr) {
            return collect($arr)->sum('paid');
           
        });
        $zones = Zone::where('status', 'active')->get(['id', 'zone_name']);
        $subzones = Subzone::where('status', 'active')->get(['id', 'subzone_name']);
        $owe_zones =  [];
        $budgetyears = (new BudgetYear())->setConnection('envsogo_'.strtolower(session('org_code')))->get(['id', 'budgetyear_name', 'status']);
        $inv_periods = TwInvoicePeriod::whereIn('budgetyear_id', $budgetyears_selected)->get(['id', 'inv_p_name']);

        $selected_inv_periods = TwInvoicePeriod::whereIn('id', $request->get('inv_period'))->get();
        $selected_inv_periods = collect($selected_inv_periods)->isEmpty() ? ['all'] : $selected_inv_periods;
        $selectedInvPeriodID = collect($selected_inv_periods)->pluck('id');
        $owe_inv_periods =  collect($invoice_owe_status)->groupBy(['inv_period_id_fk'])->map(function ($arr, $key) {
            return  TwInvoicePeriod::where('id', $key)->first(['id', 'inv_p_name']);
        })->sortByDesc('id')->reverse();

        
        if ($request->has('excelBtn')) {

            $excelname = 'รายงานการผู้ค้างชำระค่าน้ำประปา.xlsx';
            
            $arr = [
                'owes' => $owes,
                // 'budgetyears' => $budgetyears,
                'budgetyears_selected' => $budgetyears_selected,
                'selected_inv_periods' => $selected_inv_periods,
                'reservemeter_sum' => $reservemeter_sum,
                'crudetotal_sum' => $crudetotal_sum,
                'owe_zones' => $owe_zones,
                'owe_inv_periods' => $owe_inv_periods,
                'zone_selected' => $zone_selected,
            ];
            $showDetails = $request->get('excelBtn') =='overview' ? false : true;
           
            return Excel::download(new ReportOweUserExport($arr,$showDetails), $excelname);
        }
        $owe_zones = $request->get('zone') != 'all' ? $request->get('zone') : 'ทุกหมู่';
        return view("reports.owe", compact('owes', 'budgetyears', 'budgetyears_selected', 'zone_selected',
            'subzone_selected', 'selectedInvPeriodID', 'inv_periods',
             'selected_inv_periods', 'reservemeter_sum', 'crudetotal_sum', 'owe_zones', 'zones', 'subzones', 'owe_inv_periods'));
    }

    public function show() {}

    public function export(Request $request)
    {
        $fromdate = str_replace("/", "-", $request->get('fromdate'));
        $todate = str_replace("/", "-", $request->get('todate'));

        $excelname = 'รายงานการรับชำระค่าน้ำประจำวันที่ ' . $fromdate . ' ถึง ' . $todate . '.xlsx';
        return Excel::download(new DailyReportExport($request), $excelname);
    }

    private function aa(){
      return  $umfs = TwUsersInfo::where('undertake_zone_id', 12)->where('status', 'active')
        ->with([
            'invoice' => function($q){
                return $q->select('id', 'meter_id_fk','totalpaid', 'updated_at', 'status')->where('inv_period_id_fk', 6);
            }
        ])
        ->get(['meter_id']);

        foreach($umfs as $inv){
            TwInvoiceTemp::where('id', $inv->invoice[0]->inv_id)->update([
                'status' => 'paid',
                'updated_at' =>date('Y-m-d H:i:s')
            ]);
        }
        return 'ss';
    }
    public function dailypayment(Request $request)
    {
        // return $this->aa();
        if (collect($request)->has('excel')) {
            return $this->export($request);
        }
        $fnCtrl = new FunctionsController();
        if (collect($request)->isEmpty() || $request->get('nav') == 'nav') {
            $current_inv_period = TwInvoicePeriod::where('status', 'active')->get(['id', 'budgetyear_id']);
            $iniRequest = [
                'zone_id'           => 'all',
                'subzone_id'        => 'all',
                'fromdate'          => date('Y-m-d'),
                'todate'            => date('Y-m-d'),
                'acc_trans_id_fk'   => 'all',
                'cashier_selected'  => 0,
                'cashier_id'        => 'all',
                'inv_period_id'     => 'all',
                'budgetyear_id'     => $current_inv_period[0]->budgetyear_id
            ];
            $request->merge($iniRequest);
            $fromdateTh = $fnCtrl->engDateToThaiDateFormat(date('Y-m-d'));
            $todateTh = $fnCtrl->engDateToThaiDateFormat(date('Y-m-d'));
        } else {
            $fromdateTh = $request->get('fromdate');
            $todateTh = $request->get('todate');
            
            //เปลี่ยนวันไทย ไปเป็นวันอังกฤษ
            $fromdate = $fnCtrl->thaiDateToEngDateFormat($request->get('fromdate'));
            $todate = $fnCtrl->thaiDateToEngDateFormat($request->get('todate'));
            $request->merge([
                'fromdate' => $fromdate,
                'todate' => $todate,
            ]);
        }

        $inv_period_id      = $request->get('inv_period_id');
        $zone_id            = $request->get('zone_id');
        $subzone_id         = $request->get('subzone_id');
       
        $fromdate           = date_format(date_create($request->get('fromdate')), 'd/m/Y');
        $todate             = date_format(date_create($request->get('todate')), 'd/m/Y');
        
        
        $paidInfos = $this->dailypaymentTest($request);
       

        $paidInfos = collect($paidInfos)->filter(function($v){
          return  collect($v->invoice)->isNotEmpty();
        })->values();
        $cashier_id         = $request->get('cashier_id');
        $zones          = Zone::all();
        $subzones       = $zone_id != 'all' && $subzone_id != 'all' ? Subzone::all() : 'all';
        $budgetyears    = (new BudgetYear())->setConnection('envsogo_'.strtolower(session('org_code')))->all();
        $inv_periods    = TwInvoicePeriod::where('budgetyear_id', $request->get('budgetyear_id'))->orderBy('id', 'desc')->get(['id', 'inv_p_name']);
        $receiptions    = User::whereIn('role_id', [1, 2])->get(['id', 'lastname', 'firstname']);
        if ($request->get('cashier_selected') != "all") {
            $cashier    = User::where('id', $request->get('cashier_selected'))->get(['id', 'firstname', 'lastname']);
        }
        // return $inv_period_id;
        $request_selected = [
            'budgeryear' => collect((new BudgetYear())->setConnection('envsogo_'.strtolower(session('org_code')))->where('id', $request->get('budgetyear_id'))->get(['budgetyear_name']))->pluck('budgetyear_name'),
            'inv_period' => $request->get('inv_period_id') == 'all' ? ['ทั้งหมด'] : collect(TwInvoicePeriod::where('id', $request->get('inv_period_id'))->get(['inv_p_name']))->pluck('inv_p_name'),
            'zone' => $request->get('zone_id') == 'all' ? ['ทั้งหมด'] : collect(Zone::where('id', $request->get('zone_id'))->get(['zone_name']))->pluck('zone_name'),
            'subzone' => $request->get('subzone_id') == 'all' ? ['ทั้งหมด'] : collect(Subzone::where('id', $request->get('subzone_id'))->get(['subzone_name']))->pluck('subzone_name'),
            'cashier' => $request->get('cashier_id') == "all" ? [['id' => 'all', 'firstname' => 'ทั้งหมด', 'lastname' => '']] : $cashier    = User::where('id', $request->get('cashier_id'))->get(['id', 'firstname', 'lastname'])
        ];
        if ($cashier_id != 'all') {
            //filter เอาผู้รับเงินที่ต้องการ
            $paidInfos =  collect($paidInfos)->filter(function ($v) use ($cashier_id) {
                if (collect($v->invoice[0]->acc_transactions['cashier_info'])->isNotEmpty()) {
                    return $v->invoice[0]->acc_transactions['cashier_info']['id'] == $cashier_id;
                }
            })->values();
        }
        return view('reports.dailypayment', compact(
        'zones',
        'subzones',
        'paidInfos',
        'subzone_id',
        'zone_id',
        'receiptions',
        'cashier',
        'fromdateTh',
        'request_selected',
        'todateTh',
        'budgetyears',
        'inv_periods',
        'todate',
        'fromdate',
        'inv_period_id'
    ));
        
    }

    private function dailypaymentTest($request){

        $inv_period_id      = $request->get('inv_period_id');
        $zone_id            = $request->get('zone_id');
        $subzone_id         = $request->get('subzone_id');
        $fnCtrl = new FunctionsController();
       
        $fromdate = $request->get('fromdate');
        $todate = $request->get('todate');
        $cashier_id         = $request->get('cashier_id');
        $umfs = TwUsersInfo::with([
            'invoice' => function($q) use ($todate, $fromdate, $inv_period_id){
                $query = $q->select('id','meter_id_fk','inv_no', 'acc_trans_id_fk','updated_at',  'inv_period_id_fk', 
                        'lastmeter', 'currentmeter', 'water_used', 'paid', 'vat', 'reserve_meter','totalpaid',
                        'status')
                        ->whereBetween("updated_at",  [$fromdate . " 00:00:00", $todate. " 23:59:59"])
                        ->where('status', 'paid');
                if($inv_period_id != 'all'){
                    $query = $query ->where('inv_period_id_fk',$inv_period_id);
                }
               
                return $query;
            },
            'invoice.acc_transactions' => function($q) {
                return $q->select('id', 'cashier' );
            },
            'invoice.acc_transactions.cashier_info' => function($q){
                return $q->select('id','firstname', 'lastname');
            } 
        ]);
        if($zone_id != 'all'){
            $umfs = $umfs->where('undertake_zone_id', $zone_id);
        }
        if($subzone_id != 'all'){
            $umfs = $umfs->where('undertake_subzone_id', $subzone_id);
        }

        if($cashier_id != 'all'){

        }
        
        $umfs = $umfs->where('status', 'active');
        
       return $umfs = $umfs->get(['meter_id', 'user_id', 'meter_address', 'submeter_name', 'undertake_zone_id', 'status', 'updated_at']);
        
      
    }
    public function meter_record_history(REQUEST $request)
    {
        date_default_timezone_set('Asia/Bangkok');

        $budgetyear_selected_array = ['now'];
        $zone_id_array = ['all'];
        if (collect($request)->isNotEmpty()) {
            $budgetyear_selected_array = $request->get('budgetyear');
            $zone_id_array = $request->get('zone');
        } else {
            $budgetyear_selected_array = (new BudgetYear())->setConnection('envsogo_'.strtolower(session('org_code')))->where('status', 'active')
                ->get('id')->pluck('id');
        }
        $zones = Zone::all();
        $budgetyears = (new BudgetYear())->setConnection('envsogo_'.strtolower(session('org_code')))->get(['id', 'budgetyear_name', 'status']);;

        //หารอบบิลที่เปิดใช้งานของ ปีงบประมาณปัจจุบัน
        $active_inv_periods = TwInvoicePeriod::whereIn('budgetyear_id', $budgetyear_selected_array)
            ->orderBy('id', 'asc')->get('id');

        $inv_periods_list_array = collect($active_inv_periods)->pluck('id');
        $inv_periodsCount = collect($active_inv_periods)->count();

        $usermeterinfosQuery = TwUsersInfo::with([
            'invoice' => function ($q) use ($budgetyear_selected_array) {
                return $q->select(
                    'tw_invoice.lastmeter',
                    'tw_invoice.currentmeter',
                    'tw_invoice.water_used',
                    'tw_invoice.inv_period_id_fk',
                    'tw_invoice.meter_id_fk',
                    'tw_invoice_period.inv_p_name',
                    'tw_invoice_period.budgetyear_id'
                )
                    ->join('tw_invoice_period', 'tw_invoice_period.id', '=', 'tw_invoice.inv_period_id_fk')
                    ->whereIn('tw_invoice_period.budgetyear_id', $budgetyear_selected_array);
            },
            'invoice_history' => function ($q) use ($budgetyear_selected_array) {
                return $q->select(
                    'tw_invoice_history.lastmeter',
                    'tw_invoice_history.currentmeter',
                    'tw_invoice_history.water_used',
                    'tw_invoice_history.inv_period_id_fk',
                    'tw_invoice_history.meter_id_fk',
                    'tw_invoice_period.inv_p_name',
                    'tw_invoice_period.budgetyear_id'
                )
                    ->join('tw_invoice_period', 'tw_invoice_period.id', '=', 'tw_invoice_history.inv_period_id_fk')
                    ->where('tw_invoice_period.budgetyear_id', $budgetyear_selected_array);
            },
            'user' => function ($q) {
                return $q->select('id', 'prefix', 'firstname', 'lastname', 'zone_id', 'subzone_id', 'address');
            }
        ]);
        if (!in_array("all", $zone_id_array)) {
            $usermeterinfosQuery = $usermeterinfosQuery->whereIn('undertake_zone_id', $zone_id_array);
        }
        $usermeterinfosQuery = $usermeterinfosQuery
            ->where('status', '<>', 'deleted')
            ->get(['meter_id', 'user_id', 'undertake_zone_id']);

        $inv_period_list = TwInvoicePeriod::whereIn('budgetyear_id', $budgetyear_selected_array)->get(['id', 'inv_p_name']);
        $inv_period_id_list =  collect($inv_period_list)->pluck('id');

        foreach ($usermeterinfosQuery as $us) {
            $inv_period_list_clone = [];

            $mergeInvoice = collect($us->invoice)->merge($us->invoice_history)->sortBy('inv_period_id_fk')->values();
            foreach ($inv_period_list as $clone) {
                $id = $clone->id;
                $res = collect($mergeInvoice)->filter(function ($v) use ($id) {
                    return $v->inv_period_id_fk == $id;
                })->values();
                if (collect($res)->isEmpty()) {
                    array_push($inv_period_list_clone, [
                        'id' => $clone->id,
                        'inv_p_name' => $clone->inv_p_name,
                        'lastmeter' => 0,
                        "currentmeter" => 0,
                        "water_used" => 0
                    ]);
                } else {
                    array_push($inv_period_list_clone, [
                        'id' => $clone->id,
                        'inv_p_name' => $clone->inv_p_name,
                        'lastmeter' => $res[0]['lastmeter'],
                        "currentmeter" => $res[0]['currentmeter'],
                        "water_used" => $res[0]['water_used']
                    ]);
                }
            }
            $us['infos'] =  $inv_period_list_clone;
            $us['bringForward'] = collect($inv_period_list_clone)->isEmpty() ? 0 : $inv_period_list_clone[0]['lastmeter'];
            unset($us->invoice);
            unset($us->invoice_history);
        }

        $usermeterinfos = collect($usermeterinfosQuery)->filter(function ($v) {
            return collect($v->infos)->sum('currentmeter') > 0 && collect($v->user)->isNotEmpty();
        });

        if ($request->get('submitBtn') == 'export_excel') {
            $excelname = 'รายงานสมุดจดเลขอ่านมาตรวัดน้ำ(ป.31).xlsx';
            return Excel::download(new meterRecordHistoryExport([
                'usermeterinfos' => $usermeterinfos,
                'inv_period_list' => $inv_period_list,
                'zones' => $zones,
                'budgetyears' => $budgetyears,
                'budgetyear_selected_array' => $budgetyear_selected_array,
                'zone_id_array' => $zone_id_array,
            ]), $excelname);
        }
        return view('reports.meter_record_history', compact('usermeterinfos', 'inv_period_list', 'zones', 'budgetyears', 'budgetyear_selected_array', 'zone_id_array'));
    }

    public function water_used(Request $request, $from = "")
    {
        $conn = session('db_conn');

        // ค่าตั้งต้น
        if (collect($request)->isEmpty()) {
            $selected_budgetYear = (new BudgetYear())->setConnection($conn)->where('status', 'active')
                ->with(['invoicePeriod' => function ($query) {
                    $query->select('budgetyear_id', 'id');
                }])
                ->get(['id', 'budgetyear_name'])
                ->first();
            $a = [
                'zone_id' => 'all',
                'subzone_id' => 'all',
            ];
            $request->merge($a);
        } else {
            $selected_budgetYear = (new BudgetYear())->setConnection($conn)->where('id', $request->get('budgetyear_id'))
                ->with(['invoicePeriod' => function ($query) {
                    $query->select('budgetyear_id', 'id');
                }])
                ->get(['id', 'budgetyear_name'])->first();
        }
        $invPeriod_selected_buggetYear_array = collect($selected_budgetYear->invoicePeriod)->pluck('id');
        $zone_and_subzone_selected_text = 'ทั้งหมด';


        $budgetyears = (new BudgetYear())->setConnection($conn)->where('status', '<>', 'deleted')->get(['id', 'budgetyear_name']);
        $waterUsedInvoiceTable = Db::connection($conn)->table('tw_users_infos as umf')
            ->join('tw_invoice_temp as inv', 'inv.meter_id_fk', '=', 'umf.meter_id')
            ->join('tw_invoice_period as ivp', 'ivp.id', '=', 'inv.inv_period_id_fk')
            ->join('budget_year as bgy', 'bgy.id', '=', 'ivp.budgetyear_id')
            ->join('zones as z', 'z.id', '=', 'umf.undertake_zone_id')
            ->select('inv.inv_period_id_fk', 'ivp.inv_p_name', 'umf.undertake_zone_id', 'inv.water_used', 'z.zone_name', 'z.id as zone_id', 'bgy.id as budgetyear_id')
            ->whereIn('inv.inv_period_id_fk', $invPeriod_selected_buggetYear_array);

        $waterUsedInvoiceHistoryTable = Db::connection($conn)->table('tw_users_infos as umf')
            ->join('tw_invoice_history as invh', 'invh.meter_id_fk', '=', 'umf.meter_id')
            ->join('tw_invoice_period as ivp', 'ivp.id', '=', 'invh.inv_period_id_fk')
            ->join('budget_year as bgy', 'bgy.id', '=', 'ivp.budgetyear_id')
            ->join('zones as z', 'z.id', '=', 'umf.undertake_zone_id')
            ->select('invh.inv_period_id_fk', 'ivp.inv_p_name', 'umf.undertake_zone_id', 'invh.water_used', 'z.zone_name', 'z.id as zone_id', 'bgy.id as budgetyear_id')
            ->whereIn('invh.inv_period_id_fk', $invPeriod_selected_buggetYear_array);

        if ($request->get('zone_id') != 'all') {
            $zone = (new Zone())->setConnection($conn)->where('id', $request->get('zone_id'))->get('zone_name');
            $zone_and_subzone_selected_text .= ' ' . $zone[0]->zone_name;
            if ($request->get('subzone_id') != 'all') {
                $waterUsedInvoiceTable = $waterUsedInvoiceTable->where('umf.undertake_subzone_id', '=', $request->get('zone_id'));
                $waterUsedInvoiceHistoryTable = $waterUsedInvoiceHistoryTable->where('umf.undertake_subzone_id', '=', $request->get('zone_id'));
                
                $subzone = (new subZone())->setConnection($conn)->where('id', $request->get('subzone_id'))->get('subzone_name');
                $zone_and_subzone_selected_text .= ' เส้นทางจัดเก็บ ' . $subzone[0]->subzone_name;
            } else {
                $waterUsedInvoiceTable = $waterUsedInvoiceTable->where('umf.undertake_zone_id', '=', $request->get('zone_id'));
                $waterUsedInvoiceHistoryTable = $waterUsedInvoiceHistoryTable->where('umf.undertake_zone_id', '=', $request->get('zone_id'));
            }
        }
        $waterUsedInvoiceHistoryTable = $waterUsedInvoiceHistoryTable->get();
        $waterUsedInvoiceTable = $waterUsedInvoiceTable->get();


        $waterUsed = collect($waterUsedInvoiceTable)->merge($waterUsedInvoiceHistoryTable)->sortBy('undertake_zone_id');

        $waterUsedGrouped = collect($waterUsed)->groupBy('undertake_zone_id');
        $zones =  (new Zone())->setConnection($conn)->where('status', 'active')->get(['id', 'zone_name']);
        $zone_id = $request->get('zone_id');
        $zoneNameLabels = [];
        $zoneWaterUsedData = [];
        $waterUsedDataTables = [];

        foreach ($waterUsedGrouped as $key => $zone) {
            // แบ่งzone
            $zoneNameLabels[] = $zone[0]->zone_name;
            $zoneWaterUsedData[] = collect($zone)->sum('water_used');
            $waterUsedByInvPeriod = [];
            //แต่งตามรอบบิล ในzone
             $waterUsedByInvPeriodCollection = collect($zone)->groupBy('inv_period_id_fk')->values();
            
            $invpCounts = (new TwInvoicePeriod())->setConnection($conn)->where(['budgetyear_id' => $waterUsedByInvPeriodCollection[0][0]->budgetyear_id])->get(['inv_p_name'])->pluck('inv_p_name');
            
            for($i= 0; $i < collect($invpCounts)->count(); $i++){
                $checkIssetInvPName = isset($waterUsedByInvPeriodCollection[$i][0]->inv_p_name) ? 1 : 0; 
               
                $invPName = $checkIssetInvPName == 1 ? $waterUsedByInvPeriodCollection[$i][0]->inv_p_name : $invpCounts[$i];
                
                $w_used = $checkIssetInvPName == 1 ? collect($waterUsedByInvPeriodCollection[$i])->sum('water_used') : 0;
                $a = $i;
                $waterUsedByInvPeriod[] = [
                    'id'         => $checkIssetInvPName == 1 ? $waterUsedByInvPeriodCollection[$i][0]->inv_period_id_fk : $a+1,
                    'inv_p_name' => $invPName,
                    'water_used' => $w_used
                ];
            }

            $waterUsedDataTables[] = [
                'zone_id'                   => $zone[0]->undertake_zone_id,
                'zone_name'                 => $zone[0]->zone_name,
                'water_used'                => collect($zone)->sum('water_used'),
                'classify_by_inv_period'    => collect($waterUsedByInvPeriod)->sortBy('id')
            ];
        }
        $data = [
            'labels' => $zoneNameLabels,
            'data' => $zoneWaterUsedData,
        ];
        if ($from == 'dashboard') {
            return $data;
        }
        // return $waterUsedDataTables;

        return view('reports.water_used', compact('data', 'waterUsedDataTables', 'zone_and_subzone_selected_text', 'selected_budgetYear'));
    }
}
