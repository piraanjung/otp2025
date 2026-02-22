<?php

namespace App\Exports;

use App\Models\Tabwater\Invoice;
use App\Models\Tabwater\UserMerterInfo;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class InvoiceInCurrentInvoicePeriodExport implements FromView
{
    private $invoice_period;
    private $subzone_id;
    public function __construct($arr)
    {
        $this->invoice_period = $arr['curr_inv_prd'];
        $this->subzone_id = $arr['subzone_id'];
    }
    public function view(): View
    {
        $current_inv_period = $this->invoice_period;
        $umfs = UserMerterInfo::where('undertake_subzone_id', $this->subzone_id)
        ->with([
            'invoice_currrent_inv_period' => function($q) use($current_inv_period){
                return $q->select('inv_no', 'meter_id_fk', 'lastmeter', 'currentmeter', 'inv_period_id_fk', 'water_used', 'paid', 'totalpaid')
                ->where('inv_period_id_fk', $current_inv_period);
            },
            'invoice_currrent_inv_period.invoice_period' => function($q){
                return $q->select('id', 'inv_p_name');
            },
            'user' =>  function($q){
                return $q->select('id','prefix', 'firstname', 'lastname');
            },
            'undertake_zone' =>  function($q){
                return $q->select('id','zone_name');
            },
            'undertake_subzone' =>  function($q){
                return $q->select('id','subzone_name');
            },
            'undertake_subzone.undertaker_subzone' =>  function($q){
                return $q->select('subzone_id','twman_id', 'id');
            },
            'undertake_subzone.undertaker_subzone.twman_info' =>  function($q){
                return $q->select('id', 'prefix','firstname', 'lastname');
            },
        ])
        ->get(['meter_id', 'user_id', 'meternumber', 'undertake_zone_id',
        'undertake_subzone_id',
         'submeter_name', 'factory_no', 'meter_address']);

         $umfsFilter = collect($umfs)->filter(function($v){
            return collect($v->invoice_currrent_inv_period)->isNotEmpty();
        })->values();
        return view('exports.invoices_in_curr_inv_period', [
            'umfs' => $umfsFilter
        ]);
    }
}
