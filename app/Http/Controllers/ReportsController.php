<?php

namespace App\Http\Controllers;

use App\Exports\ReportOweUserExport;
use App\Http\Controllers\Api\FunctionsController;
use App\Models\Invoice;
use App\Models\InvoicePeriod;
use App\Models\Subzone;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Api\ReportsController as apiReportCtrl;
use App\Http\Controllers\Api\InvoicePeriodController as apiInvoicePeriodCtrl;
use App\Models\BudgetYear;
use App\Models\InvoiceHistoty;
use App\Models\UserMerterInfo;
use App\Exports\DailyReportExport;
use App\Exports\meterRecordHistoryExport;
use App\Exports\ReportOweUser;
use Maatwebsite\Excel\Exporter;
use Maatwebsite\Excel\Facades\Excel;

class ReportsController extends Controller
{
    public function owe(Request $request)
    {
        //get user ที่มีสถานะติดหนี้ จาก Invoice Table
        //แล้วทำการ group ด้วย  meter_id_fk แล้วทำการ reset index ของ Array
        $invoice_owe_status = Invoice::where("status", "owe")->get([
            'inv_id', 'meter_id_fk', 'currentmeter', 'lastmeter', 'water_used', 'inv_period_id_fk', 'paid', 'inv_type', 'vat', 'totalpaid', 'updated_at'
        ])->groupBy(['meter_id_fk'])->values();
        //ทำการ map เพื่อการ set ค่าไปแสดงในหน้า view
        $owes = collect($invoice_owe_status)->map(function ($ar, $k) {
            return [
                'meter_id_fk' => $ar[0]->meter_id_fk,
                'name' => $ar[0]->usermeterinfos->user->prefix . "" . $ar[0]->usermeterinfos->user->firstname . " " . $ar[0]->usermeterinfos->user->lastname,
                'address' => $ar[0]->usermeterinfos->user->address,
                'zone' => $ar[0]->usermeterinfos->user->user_zone->zone_name,
                'subzone' => $ar[0]->usermeterinfos->user->subzone_id == 13 ? 'เส้นหมู่13' :  $ar[0]->usermeterinfos->user->user_subzone->subzone_name,
                'undertake_subzone' =>  $ar[0]->usermeterinfos->undertake_subzone_id == 13 ? 'เส้นหมู่13' : $ar[0]->usermeterinfos->undertake_subzone->subzone_name,
                'paid' => collect($ar)->sum('paid'),
                'vat' => number_format(collect($ar)->sum('vat'), 2),
                'totalpaid' => number_format(collect($ar)->sum('totalpaid'), 2),
                'owe_count' => collect($ar)->count()
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
        $budgetyears = BudgetYear::with([
            'invoicePeriod' => function($q){
                return $q->select('id', 'inv_p_name', 'budgetyear_id', 'status');
            }
        ])->get(['id', 'budgetyear_name', 'status']);
        $budgetyears_selected = [];
        if (collect($budgetyears_selected)->isEmpty()) {
            $budgetyear = collect($budgetyears)->filter(function ($b) {
                return $b->status == 'active';
            })->values();

            $budgetyears_selected[] = $budgetyear[0]->id;
            $selected_inv_periods = InvoicePeriod::whereIn('budgetyear_id', $budgetyears_selected)->get(['id', 'inv_p_name']);
        }

        $owe_inv_periods =  collect($invoice_owe_status)->groupBy(['inv_period_id_fk'])->map(function ($arr, $key) {
            return  InvoicePeriod::where('id', $key)->first(['id', 'inv_p_name']);
        })->sortByDesc('id');
        return view("reports.owe", compact("owes", 'budgetyears', 'budgetyears_selected', 'selected_inv_periods', 'reservemeter_sum', 'crudetotal_sum', 'owe_zones', 'zones', 'subzones', 'owe_inv_periods'));
    }
    public function owe_search(Request $request)
    {
        $budgetyears_selected = collect($request->get('budgetyear'))->isEmpty() || in_array("all", $request->get('budgetyear')) ? []
                        : $request->get('budgetyear');
        $subzone_selected     = collect($request->get('subzone'))->isEmpty() || in_array("all", $request->get('subzone')) ? []
                        : $request->get('subzone');
        $zone_selected        = collect($request->get('zone'))->isEmpty() || in_array("all", $request->get('zone')) ? []
                        : $request->get('zone');
        $inv_period_selected  = collect($request->get('inv_period'))->isEmpty() || in_array("all", $request->get('inv_period')) ? []
                        : $request->get('inv_period');

        $invoice_owe_status = Invoice::where("status", "owe");
        if (collect($inv_period_selected)->isNotEmpty()) {
            //เลือกค้นหาโดยเลือกรอบบิล
            $invoice_owe_status = $invoice_owe_status->whereIn("inv_period_id_fk", $inv_period_selected);
        }else{
            //เลือกค้นหาเฉพาะปีงบประมาณ
            $inv_period_arr = InvoicePeriod::whereIn('budgetyear_id', $budgetyears_selected)->get(['id']);
            $inv_period_selected = collect($inv_period_arr)->pluck('id');
            $invoice_owe_status = $invoice_owe_status->whereIn("inv_period_id_fk", $inv_period_selected);
        }

        $invoice_owe_status = $invoice_owe_status->get([
            'inv_id', 'meter_id_fk', 'currentmeter', 'lastmeter', 'water_used', 'inv_period_id_fk', 'paid', 'inv_type', 'vat', 'totalpaid', 'updated_at'
        ])->values();

        if (collect($zone_selected)->isNotEmpty()) {
            $invoice_owe_status = collect($invoice_owe_status)->filter(function ($item) use ($zone_selected) {
                return in_array($item->usermeterinfos->undertake_zone_id, $zone_selected);
            });
        }

        if (collect($subzone_selected)->isNotEmpty()) {
            $invoice_owe_status = collect($invoice_owe_status)->filter(function ($item) use ($subzone_selected) {
                return in_array($item->usermeterinfos->undertake_subzone_id, $subzone_selected);
            });
        }

        $owe_users = collect($invoice_owe_status)->groupBy(['meter_id_fk'])->values();
        $owes = collect($owe_users)->map(function ($ar, $k) {
            return [
                'meter_id_fk' => $ar[0]->meter_id_fk,
                'inv_period_id_fk' => $ar[0]->inv_period_id_fk,
                'name' => $ar[0]->usermeterinfos->user->prefix . "" . $ar[0]->usermeterinfos->user->firstname . " " . $ar[0]->usermeterinfos->user->lastname,
                'address' => $ar[0]->usermeterinfos->user->address,
                'zone_id' => $ar[0]->usermeterinfos->undertake_zone_id,
                'zone' => $ar[0]->usermeterinfos->user->user_zone->zone_name,
                'subzone' => $ar[0]->usermeterinfos->user->subzone_id == 13 ? 'เส้นหมู่13' :  $ar[0]->usermeterinfos->user->user_subzone->subzone_name,
                'undertake_subzone' =>  $ar[0]->usermeterinfos->undertake_subzone_id == 13 ? 'เส้นหมู่13' : $ar[0]->usermeterinfos->undertake_subzone->subzone_name,
                'paid' => collect($ar)->sum('paid'),
                'vat' => number_format(collect($ar)->sum('vat'), 2),
                'totalpaid' => number_format(collect($ar)->sum('totalpaid'), 2),
                'owe_count' => collect($ar)->count(),
                'owe_infos' => $ar
            ];
        });

        // return collect($owes)->groupBy('zone_id');


        $reservemeter_sum = collect($owe_users)->sum(function ($arr) {
            return collect($arr)->sum(function ($ar) {
                return $ar['inv_type'] == "r" ? $ar['paid'] : 0;
            });
        });

        $crudetotal_sum = collect($owe_users)->sum(function ($arr) {
            return collect($arr)->sum(function ($ar) {
                return $ar['inv_type'] == "u" ? $ar['paid'] : 0;
            });
        });
        $zones = Zone::where('status', 'active')->get(['id', 'zone_name']);
        $subzones = Subzone::where('status', 'active')->get(['id', 'subzone_name']);
        $owe_zones =  [];
        $budgetyears = BudgetYear::get(['id', 'budgetyear_name', 'status']);

        $selected_inv_periods = InvoicePeriod::whereIn('budgetyear_id', $request->get('budgetyear'))->get();

        $owe_inv_periods =  collect($invoice_owe_status)->groupBy(['inv_period_id_fk'])->map(function ($arr, $key) {
            return  InvoicePeriod::where('id', $key)->first(['id', 'inv_p_name']);
        })->sortByDesc('id')->reverse();
        if($request->has('excelBtn')){

            $excelname = 'รายงานการผู้ค้างชำระค่าน้ำประปา.xlsx';;
            $arr = [
                'owes' => $owes,
                'budgetyears' => $budgetyears,
                'budgetyears_selected' => $budgetyears_selected,
                'selected_inv_periods' => $selected_inv_periods,
                'reservemeter_sum' => $reservemeter_sum,
                'crudetotal_sum' => $crudetotal_sum,
                'owe_zones' => $owe_zones,
                'zones' => $zones,
                'subzones' => $subzones,
                'owe_inv_periods' =>$owe_inv_periods
            ];

            return Excel::download(new ReportOweUserExport($arr, $request->get('excelBtn')), $excelname);
        }

        return view("reports.owe", compact('owes', 'budgetyears', 'budgetyears_selected', 'selected_inv_periods', 'reservemeter_sum', 'crudetotal_sum', 'owe_zones', 'zones', 'subzones', 'owe_inv_periods'));
    }



    public function export(Request $request){
        $fromdate = str_replace("/","-",$request->get('fromdate'));
        $todate = str_replace("/","-",$request->get('todate'));

        $excelname = 'รายงานการรับชำระค่าน้ำประจำวันที่ '.$fromdate.' ถึง '.$todate.'.xlsx';
        
        return Excel::download(new DailyReportExport($request), $excelname);
    }

    public function dailypayment(Request $request)
    {
//         ini_set('memory_limit', '512M');

//            $paidsQuery =  DB::table('invoice_history as iv')->join('acc_transactions as act', 'act.id', '=', 'iv.acc_trans_id_fk')
//          ->where('iv.status', 'paid')
//         ->get([
//             'iv.inv_id', 'iv.updated_at as iv_updated_at', 'act.updated_at as act_updated_at', 'iv.status'
//         ]);

//         foreach($paidsQuery as $p){
//             InvoiceHistoty::where('inv_id', $p->inv_id)->update([
//                 'updated_at' => $p->act_updated_at,
//             ]);
//         }
// return 'ss';
        if(collect($request)->has('excel')){
            return $this->export($request);
        }
        date_default_timezone_set('Asia/Bangkok');
        $fnCtrl = new FunctionsController();
        $apiReportCtrl = new apiReportCtrl();
        if (collect($request)->isEmpty() || $request->get('nav') == 'nav') {
            $current_inv_period = InvoicePeriod::where('status', 'active')->get(['id', 'budgetyear_id']);
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
            //เปลี่ยนวันไทย ไปเป็นวันอังกฤษ
            $fromdate = $fnCtrl->thaiDateToEngDateFormat($request->get('fromdate'));
            $todate = $fnCtrl->thaiDateToEngDateFormat($request->get('todate'));
            $request->merge([
                'fromdate' => $fromdate,
                'todate' => $todate,
            ]);
            $fromdateTh = $request->get('fromdate');
            $todateTh = $request->get('todate');
        }
        $fnCtrl = new FunctionsController();
        //หาจาก  usermeterinfos[undertake_zone_id  undertake_subzone_id] -> invoice table
        $inv_period_id      = $request->get('inv_period_id');
        $zone_id            = $request->get('zone_id');
        $subzone_id         = $request->get('subzone_id');
        $fromdate           = date_format(date_create($request->get('fromdate')), 'd/m/Y');
        $todate             = date_format(date_create($request->get('todate')), 'd/m/Y');
        $cashier_id         = $request->get('cashier_id');
        $paidsQuery = Invoice::whereBetween("updated_at",  [$request->get('fromdate') . " 00:00:00", $request->get('todate'). " 23:59:59"])
            ->where('status', 'paid');

        $paidCurrentInfos = $paidsQuery->with([
            'usermeterinfos' => function ($query) {
                return $query->select('meter_id', 'user_id', 'meternumber', 'undertake_zone_id', 'undertake_subzone_id');
            },
            'acc_transactions.cashier_info' => function ($query) {
                return $query->select('id', 'prefix', 'firstname', 'lastname');
            }
        ])->get([
            "inv_id",
            "inv_period_id_fk",
            "meter_id_fk",
            "lastmeter",
            "currentmeter",
            "water_used",
            "inv_type",
            "paid",
            "vat",
            "totalpaid",
            "status",
            "acc_trans_id_fk",
            "updated_at"
        ])->groupBy('acc_trans_id_fk')->values();


        $paidsHistoryQuery = InvoiceHistoty::whereBetween("updated_at",  [$request->get('fromdate') . " 00:00:00", $request->get('todate') . " 23:59:59"])
            ->where('status', 'paid');

        $paidHistoryInfos = $paidsHistoryQuery->with([
            'usermeterinfos' => function ($query) {
                return $query->select('meter_id', 'user_id', 'meternumber', 'undertake_zone_id', 'undertake_subzone_id');
            },
            'acc_transactions.cashier_info' => function ($query) {
                return $query->select('id', 'prefix', 'firstname', 'lastname');
            }
        ])->get([
            "inv_id",
            "inv_period_id_fk",
            "meter_id_fk",
            "lastmeter",
            "currentmeter",
            "water_used",
            "inv_type",
            "paid",
            "vat",
            "totalpaid",
            "status",
            "acc_trans_id_fk",
            "updated_at"
        ])->groupBy('acc_trans_id_fk')->values();
        
        //gเก็บไว้แก้ไข case   updated_at ใน invoicehistory กับ acctranx ไม่เท่ากัน
        // $date20240715 = collect($paidHistoryInfos)->filter(function($v){
        //     $d = date_format($v->acc_transactions->updated_at, 'Y-m-d');
        //    return $d  != '2024-07-15';
        // });

        // foreach($date20240715 as $d){
        //     $date = date_format($d->acc_transactions->updated_at, 'Y-m-d');
        //     InvoiceHistoty::where('inv_id', $d->inv_id)->update([
        //         'updated_at' => $date
        //     ]);
        // }
//gเก็บไว้แก้ไข case   updated_at ใน invoicehistory กับ acctranx ไม่เท่ากัน

$paidInfos = collect($paidCurrentInfos)->merge($paidHistoryInfos);
        if ($cashier_id != 'all') {
            //filter เอาผู้รับเงินที่ต้องการ
            $paidInfos =  collect($paidInfos)->filter(function ($v) use ($cashier_id) {
                if (collect($v[0]->acc_transactions['cashier_info'])->isNotEmpty()) {
                    return $v[0]->acc_transactions['cashier_info']['id'] == $cashier_id;
                }
            });
        }
        if($zone_id !="all"){
            $paidInfos =  collect($paidInfos)->filter(function ($v) use ($zone_id) {
                if (collect($v[0]->usermeterinfos['undertake_zone_id'])->isNotEmpty()) {
                    return $v[0]->usermeterinfos['undertake_zone_id'] == $zone_id;
                }
            });
        }
        if($subzone_id !="all"){
            $paidInfos =  collect($paidInfos)->filter(function ($v) use ($subzone_id) {
                if (collect($v[0]->usermeterinfos['undertake_subzone_id'])->isNotEmpty()) {
                    return $v[0]->usermeterinfos['undertake_subzone_id'] == $subzone_id;
                }
            });
        }
        if($inv_period_id !="all"){
            $paidInfos =  collect($paidInfos)->filter(function ($v) use ($inv_period_id) {
                if (collect($v[0]['inv_period_id_fk'])->isNotEmpty()) {
                    return $v[0]['inv_period_id_fk'] == $inv_period_id;
                }
            });
        }

        $zones          = Zone::all();
        $subzones       = $zone_id != 'all' && $subzone_id != 'all' ? Subzone::all() : 'all';
        $budgetyears    = BudgetYear::all();
        $inv_periods    = InvoicePeriod::where('budgetyear_id', $request->get('budgetyear_id'))->orderBy('id', 'desc')->get(['id', 'inv_p_name']);
        $receiptions    = User::whereIn('role_id', [1, 2])->get(['id', 'lastname', 'firstname']);
        if ($request->get('cashier_selected') != "all") {
            $cashier    = User::where('id', $request->get('cashier_selected'))->get(['id', 'firstname', 'lastname']);
        }
        // return $inv_period_id;
         $request_selected = [
            'budgeryear' => collect(BudgetYear::where('id', $request->get('budgetyear_id'))->get(['budgetyear_name']))->pluck('budgetyear_name'),
            'inv_period' => $request->get('inv_period_id') == 'all' ? ['ทั้งหมด'] : collect(InvoicePeriod::where('id', $request->get('inv_period_id'))->get(['inv_p_name']))->pluck('inv_p_name'),
            'zone' => $request->get('zone_id') == 'all' ? ['ทั้งหมด'] : collect(Zone::where('id', $request->get('zone_id'))->get(['zone_name']))->pluck('zone_name'),
            'subzone' => $request->get('subzone_id') == 'all' ? ['ทั้งหมด'] : collect(Subzone::where('id', $request->get('subzone_id'))->get(['subzone_name']))->pluck('subzone_name'),
            'cashier' => $request->get('cashier_id') == "all" ? [['id' =>'all','firstname' => 'ทั้งหมด', 'lastname' => '']] : $cashier    = User::where('id', $request->get('cashier_id'))->get(['id', 'firstname', 'lastname'])
        ];

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
    public function meter_record_history(REQUEST $request)
    {
        date_default_timezone_set('Asia/Bangkok');

        $budgetyear_selected_array = ['now'];
        $zone_id_array = ['all'];
        if (collect($request)->isNotEmpty()) {
            $budgetyear_selected_array = $request->get('budgetyear');
            $zone_id_array = $request->get('zone');
        }else{
            $budgetyear_selected_array = BudgetYear::where('status', 'active')
                ->get('id')->pluck('id');
        }
        $zones = Zone::all();
        $budgetyears = BudgetYear::get(['id','budgetyear_name', 'status']);;

        //หารอบบิลที่เปิดใช้งานของ ปีงบประมาณปัจจุบัน
        $active_inv_periods = InvoicePeriod::where('deleted', 0)
            ->whereIn('budgetyear_id', $budgetyear_selected_array)
            ->orderBy('id', 'asc')->get('id');

        $inv_periods_list_array = collect($active_inv_periods)->pluck('id');
        $inv_periodsCount = collect($active_inv_periods)->count();

        $usermeterinfosQuery = UserMerterInfo::with([
            'invoice' => function($q) use($budgetyear_selected_array){
                return $q->select('invoice.lastmeter', 'invoice.currentmeter', 'invoice.water_used', 'invoice.inv_period_id_fk',
                 'invoice.meter_id_fk','invoice_period.inv_p_name', 'invoice_period.budgetyear_id')
                ->join('invoice_period', 'invoice_period.id', '=', 'invoice.inv_period_id_fk')
                ->whereIn('invoice_period.budgetyear_id', $budgetyear_selected_array );
            },
            'invoice_history' => function($q) use($budgetyear_selected_array){
                return $q->select('invoice_history.lastmeter', 'invoice_history.currentmeter',
                'invoice_history.water_used',
                'invoice_history.inv_period_id_fk', 'invoice_history.meter_id_fk','invoice_period.inv_p_name', 'invoice_period.budgetyear_id')
                ->join('invoice_period', 'invoice_period.id', '=', 'invoice_history.inv_period_id_fk')
                ->where('invoice_period.budgetyear_id', $budgetyear_selected_array);
            },
            'user' => function($q){
                return $q->select('id', 'prefix', 'firstname', 'lastname', 'zone_id', 'subzone_id', 'address');
            }
        ]);
        if(!in_array("all", $zone_id_array)){
            $usermeterinfosQuery = $usermeterinfosQuery->whereIn('undertake_zone_id', $zone_id_array);
        }
        $usermeterinfosQuery = $usermeterinfosQuery
        ->where('status', '<>', 'deleted')
        ->get(['meter_id', 'user_id', 'undertake_zone_id']);

        $inv_period_list = InvoicePeriod::whereIn('budgetyear_id', $budgetyear_selected_array)->get(['id', 'inv_p_name']);
        $inv_period_id_list =  collect($inv_period_list)->pluck('id');
        foreach($usermeterinfosQuery as $us){
            $inv_period_list_clone = [];

            $mergeInvoice = collect( $us->invoice)->merge( $us->invoice_history)->sortBy('inv_period_id_fk')->values();
            foreach($inv_period_list as $clone){
                $id = $clone->id;
                $res = collect($mergeInvoice)->filter(function($v) use ($id){
                    return $v->inv_period_id_fk == $id;
                })->values();
                if(collect($res)->isEmpty()){
                    array_push($inv_period_list_clone, ['id'=> $clone->id, 'inv_p_name' => $clone->inv_p_name,
                        'lastmeter' => 0, "currentmeter" => 0 , "water_used" => 0]);
                }else{
                    array_push($inv_period_list_clone, ['id'=> $clone->id, 'inv_p_name' => $clone->inv_p_name,
                        'lastmeter' => $res[0]['lastmeter'], "currentmeter" => $res[0]['currentmeter'], "water_used" => $res[0]['water_used']]);
                }

            }
            $us['infos'] =  $inv_period_list_clone;
            $us['bringForward'] = collect($inv_period_list_clone)->isEmpty() ? 0 : $inv_period_list_clone[0]['lastmeter'];
            unset($us->invoice);
            unset($us->invoice_history);
        }

        $usermeterinfos = collect($usermeterinfosQuery)->filter(function($v){
            return collect($v->infos)->sum('currentmeter') > 0 && collect($v->user)->isNotEmpty();
        });

        if($request->get('submitBtn') == 'export_excel'){
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

    public function water_used(Request $request, $from="")
    {
        // ค่าตั้งต้น
        if (collect($request)->isEmpty()) {
            $selected_budgetYear = BudgetYear::where('status', 'active')
                ->with(['invoicePeriod' => function ($query) {
                    $query->select('budgetyear_id', 'id')->where('deleted', '=', 0);
                }])
                ->get(['id', 'budgetyear_name'])
                ->first();
            $a = [
                'zone_id' => 'all',
                'subzone_id' => 'all',
            ];
            $request->merge($a);
        } else {
            $selected_budgetYear = BudgetYear::where('id', $request->get('budgetyear_id'))
                ->with(['invoicePeriod' => function ($query) {
                    $query->select('budgetyear_id', 'id');
                }])
                ->get(['id', 'budgetyear_name'])->first();
        }
        $invPeriod_selected_buggetYear_array = collect($selected_budgetYear->invoicePeriod)->pluck('id');
        $zone_and_subzone_selected_text = 'ทั้งหมด';


        $budgetyears = BudgetYear::where('status', '<>', 'deleted')->get(['id', 'budgetyear_name']);
        $waterUsedInvoiceTable = Db::table('user_meter_infos as umf')
            ->join('invoice as inv', 'inv.meter_id_fk', '=', 'umf.meter_id')
            ->join('invoice_period as ivp', 'ivp.id', '=', 'inv.inv_period_id_fk')
            ->join('budget_year as bgy', 'bgy.id', '=', 'ivp.budgetyear_id')
            ->join('zones as z', 'z.id', '=', 'umf.undertake_zone_id')
            ->select('inv.inv_period_id_fk', 'ivp.inv_p_name', 'umf.undertake_zone_id', 'inv.water_used', 'z.zone_name', 'z.id as zone_id', 'bgy.id as budgetyear_id')
            ->whereIn('inv.inv_period_id_fk', $invPeriod_selected_buggetYear_array);

        $waterUsedInvoiceHistoryTable = Db::table('user_meter_infos as umf')
            ->join('invoice_history as invh', 'invh.meter_id_fk', '=', 'umf.meter_id')
            ->join('invoice_period as ivp', 'ivp.id', '=', 'invh.inv_period_id_fk')
            ->join('budget_year as bgy', 'bgy.id', '=', 'ivp.budgetyear_id')
            ->join('zones as z', 'z.id', '=', 'umf.undertake_zone_id')
            ->select('invh.inv_period_id_fk', 'ivp.inv_p_name', 'umf.undertake_zone_id', 'invh.water_used', 'z.zone_name', 'z.id as zone_id', 'bgy.id as budgetyear_id')
            ->whereIn('invh.inv_period_id_fk', $invPeriod_selected_buggetYear_array);

        if ($request->get('zone_id') != 'all') {
            $zone = Zone::where('id', $request->get('zone_id'))->get('zone_name');
            $zone_and_subzone_selected_text .= ' ' . $zone[0]->zone_name;
            if ($request->get('subzone_id') != 'all') {
                $waterUsedInvoiceTable = $waterUsedInvoiceTable->where('umf.undertake_subzone_id', '=', $request->get('zone_id'));
                $waterUsedInvoiceHistoryTable = $waterUsedInvoiceHistoryTable->where('umf.undertake_subzone_id', '=', $request->get('zone_id'));
                $subzone = subZone::where('id', $request->get('subzone_id'))->get('subzone_name');
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
        $zones = Zone::where('status', 'active')->get(['id', 'zone_name']);
        $zone_id = $request->get('zone_id');
        $zoneNameLabels = [];
        $zoneWaterUsedData = [];
        $waterUsedDataTables = [];
        foreach ($waterUsedGrouped as $key => $zone) {

            $zoneNameLabels[] = $zone[0]->zone_name;
            $zoneWaterUsedData[] = collect($zone)->sum('water_used');
            $waterUsedByInvPeriod = [];

            $waterUsedByInvPeriodCollection = collect($zone)->groupBy('inv_period_id_fk');
            foreach ($waterUsedByInvPeriodCollection as $inv_p_zone) {
                $w_used = collect($inv_p_zone)->sum('water_used');
                $waterUsedByInvPeriod[] = [
                    'id'         => $inv_p_zone[0]->inv_period_id_fk,
                    'inv_p_name' => $inv_p_zone[0]->inv_p_name,
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
        if($from == 'dashboard'){
            return $data;
        }
        // return $waterUsedDataTables;

        return view('reports.water_used', compact('data', 'waterUsedDataTables', 'zone_and_subzone_selected_text', 'selected_budgetYear'));

    }
}
