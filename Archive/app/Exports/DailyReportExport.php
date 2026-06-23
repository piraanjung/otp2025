<?php

namespace App\Exports;

use App\Models\Admin\BudgetYear;
use App\Models\Invoice;
// use Maatwebsite\Excel\Concerns\FromCollection;
use App\Models\InvoiceHistoty;
use App\Models\InvoicePeriod;
use App\Models\Admin\Subzone;
use Illuminate\Contracts\View\View;
use App\Http\Controllers\Api\FunctionsController;
use App\Models\Tabwater\TwInvoice;
use App\Models\Tabwater\TwInvoiceHistory;
use App\Models\Tabwater\TwInvoicePeriod;
use App\Models\User;
use App\Models\Admin\Zone;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromView;


class DailyReportExport implements FromView
{
    /**
     * @return \Illuminate\Support\Collection
     */
    // public function collection()
    // {
    //    // return Invoice::where('inv_period_id_fk', 44)->get();
    // }



    private $request;
    public function __construct( $request) {
        $this->request = $request;
}
    public function view(): View
    {

        date_default_timezone_set('Asia/Bangkok');
        $fnCtrl = new FunctionsController();

        $fromdateThTopic =$this->request->get('fromdate');
        $todateThTopic = $this->request->get('todate');
        if (collect($this->request)->isEmpty() || $this->request->get('nav') == 'nav') {
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
            $this->request->merge($iniRequest);

            $fromdateTh = $fnCtrl->engDateToThaiDateFormat(date('Y-m-d'));
            $todateTh = $fnCtrl->engDateToThaiDateFormat(date('Y-m-d'));
        } else {
            //เปลี่ยนวันไทย ไปเป็นวันอังกฤษ
            $fromdate = $fnCtrl->thaiDateToEngDateFormat($this->request->get('fromdate'));
            $todate = $fnCtrl->thaiDateToEngDateFormat($this->request->get('todate'));
            $this->request->merge([
                'fromdate' => $fromdate,
                'todate' => $todate,
            ]);
            $fromdateTh = $this->request->get('fromdate');
            $todateTh = $this->request->get('todate');

        }
        $fnCtrl = new FunctionsController();
        //หาจาก  tw_meter_infos[undertake_zone_id  undertake_subzone_id] -> invoice table
        $inv_period_id      = $this->request->get('inv_period_id');
        $zone_id            = $this->request->get('zone_id');
        $subzone_id         = $this->request->get('subzone_id');
        $fromdate           = $this->request->get('fromdate');
        $todate             = $this->request->get('todate');
        $cashier_id         = $this->request->get('cashier_id');
        
        $paidsQuery = TwInvoice::whereBetween("updated_at",  [$fromdate . " 00:00:00", $todate . " 23:59:59"])
         ->where('status', 'paid') ;

        $paidCurrentInfos = $paidsQuery->with([
            'tw_meter_infos' => function ($query) {
                return $query->select('meter_id', 'user_id', 'meternumber', 'undertake_zone_id', 'undertake_subzone_id', 'status')
                ->where('status', 'active');
            },
            'tw_meter_infos.user',
            'tw_acc_transactions.cashier_info' => function ($query) {
                return $query->select('id', 'prefix', 'firstname', 'lastname');
            }
        ])->get([
            "id",
            'inv_no',
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
        ])
        ->values();
        
        $paidsHistoryQuery = TwInvoiceHistory::whereBetween("updated_at",  [$todate . " 00:00:00", $fromdate . " 23:59:59"])
            ->where('status', 'paid');
        
        $paidHistoryInfos = $paidsHistoryQuery->with([
            'tw_meter_infos' => function ($query) {
                return $query->select('meter_id', 'user_id', 'meternumber', 'undertake_zone_id', 'undertake_subzone_id', 'status')
                ->where('status', 'active');
                
            },
            'tw_meter_infos.user',
            'tw_acc_transactions.cashier_info' => function ($query) {
                return $query->select('id', 'prefix', 'firstname', 'lastname');
            }
        ])->get([
            "id",
            "inv_period_id_fk",
            "inv_no",
            "meter_id_fk",
            "lastmeter",
            "currentmeter",
            "water_used",
            "inv_type",
            "paid",
            "vat",
            "totalpaid",
            "status",
            // "deleted",
            "acc_trans_id_fk",
            "updated_at"
        ])
        // ->groupBy('inv_no')
        ->values();

        $paidInfos = collect($paidCurrentInfos)->merge($paidHistoryInfos);
        $paidInfos = collect($paidInfos)->filter(function($v){
            return collect($v->tw_meter_infos)->isNotEmpty();
        });
       
        if ($cashier_id != 'all') {
            //filter เอาผู้รับเงินที่ต้องการ
            $paidInfos =  collect($paidInfos)->filter(function ($v) use ($cashier_id) {
                if (collect($v->tw_acc_transactions['cashier_info'])->isNotEmpty()) {
                    return $v->tw_acc_transactions['cashier_info']['id'] == $cashier_id;
                }
            });
        }

        if($zone_id !="all"){
            $paidInfos =  collect($paidInfos)->filter(function ($v) use ($zone_id) {
                if (collect($v->tw_meter_infos['undertake_zone_id'])->isNotEmpty()) {
                    return $v->tw_meter_infos['undertake_zone_id'] == $zone_id;
                }
            });
        }

        if($subzone_id !="all"){
            $paidInfos =  collect($paidInfos)->filter(function ($v) use ($subzone_id) {
                if (collect($v->tw_meter_infos['undertake_subzone_id'])->isNotEmpty()) {
                    return $v->tw_meter_infos['undertake_subzone_id'] == $subzone_id;
                }
            });
        }
        
        if($inv_period_id !="all"){
            $paidInfos =  collect($paidInfos)->filter(function ($v) use ($inv_period_id) {
                if (collect($v['inv_period_id_fk'])->isNotEmpty()) {
                    return $v['inv_period_id_fk'] == $inv_period_id;
                }
            });
        }
      
        
        $paidInfos = collect($paidInfos)->groupBy('acc_trans_id_fk');

        $orgId = Auth::user()->org_id_fk;

        $zones          = Zone::all();
        $subzones       = $zone_id != 'all' && $subzone_id != 'all' ? Subzone::all() : 'all';
        $budgetyears    = BudgetYear::all();
        $inv_periods    = TwInvoicePeriod::orderBy('id', 'desc')->get(['id', 'inv_p_name']);
        $receiptions    = User::where('org_id_fk', $orgId)
    ->whereHas('roles', function ($query) {
        // เลือกเอาว่าจะเช็คจาก ID หรือ ชื่อ (แนะนำชื่อจะอ่านรู้เรื่องกว่า)
        
        // กรณีเช็คจากชื่อ Role
        $query->whereIn('name', ['Admin', 'Finance Staff']); 
        
        // หรือ กรณีเช็คจาก ID (ถ้าคุณมั่นใจว่า 1=Admin, 2=Finance)
        // $query->whereIn('id', [1, 2]); 
    })
    ->get(['id', 'lastname', 'firstname']); 
        if ($this->request->get('cashier_selected') != "all") {
            $cashier    = User::where('id', $this->request->get('cashier_selected'))->get(['id', 'firstname', 'lastname']);
        }
        // return $request;
        $request_selected = [
            'budgeryear' => collect(BudgetYear::where('id', $this->request->get('budgetyear_id'))->get(['budgetyear_name']))->pluck('budgetyear_name'),
            'inv_period' => $this->request->get('inv_period_id') == 'all' ? ['ทั้งหมด'] : collect(TwInvoicePeriod::where('id', $this->request->get('inv_period_id'))->get(['inv_p_name']))->pluck('inv_p_name'),
            'zone' => $this->request->get('zone_id') == 'all' ? ['ทั้งหมด'] : collect(Zone::where('id', $this->request->get('zone_id'))->get(['zone_name']))->pluck('zone_name'),
            'subzone' => $this->request->get('subzone_id') == 'all' ? ['ทั้งหมด'] : collect(Subzone::where('id', $this->request->get('subzone_id'))->get(['subzone_name']))->pluck('subzone_name'),
            'cashier' => $this->request->get('cashier_id') == "all" ? [['firstname' => 'ทั้งหมด', 'lastname' => '']] : $cashier    = User::where('id', $this->request->get('cashier_id'))->get(['id', 'firstname', 'lastname'])
        ];


        return view('reports.export_dailypayment', [
            'zones' => $zones,
            'subzones' => $subzones,
            'paidInfos' => $paidInfos,
            'subzone_id' => $subzone_id,
            'zone_id' => $zone_id,
            'receiptions' => $receiptions,
            'cashier' => $cashier,
            'fromdateTh' => $fromdateTh,
            'fromdateThTopic' => $fromdateThTopic,
            'todateThTopic' => $todateThTopic,
            'request_selected' => $request_selected,
            'todateTh' => $todateTh,
            'budgetyears' => $budgetyears,
            'inv_periods' => $inv_periods,
        ]);
    }
}
