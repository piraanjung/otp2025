<?php

namespace App\Http\Controllers\Tabwater;

use App\Http\Controllers\Controller;
use App\Models\Admin\BudgetYear;
use App\Models\Tabwater\UserMerterInfo;
use Illuminate\Http\Request;
use App\UserMeterInfos;
use App\Models\Admin\Subzone;
use App\Models\Tabwater\Invoice;
use App\Models\Tabwater\InvoicePeriod;

class UserMeterInfosController extends Controller
{
    public function find_subzone($zone_id, $subzone_id = 0){
        $resVal =0;
        if($zone_id > 0){
            $subzones = Subzone::where('zone_id', $zone_id)->get('id');
            if(collect($subzones)->isNotEmpty()){
                foreach($subzones as $subzone){
                    $undertake_subzone_id = UserMerterInfo::where('undertake_subzone_id', $subzone->id)
                                            ->get()->first();

                    if(collect($undertake_subzone_id)->isNotEmpty()){
                        $resVal = 1;
                        break;
                    }

                }
            }
        }else{
            $undertake_subzone_id = UserMerterInfo::where('undertake_subzone_id', $subzone_id)->get()->first();
            if(collect($undertake_subzone_id)->isNotEmpty()){
                $resVal = 1;
            }
        }

        return $resVal;
    }

    public function edit_invoices($meter_id){
        $inv_period = InvoicePeriod::where('status', 'active')->get('id')->first();
        $usermeter_infos = UserMerterInfo::with([
            'invoice' => function($q) use ($inv_period){
                $budget_year = BudgetYear::with('invoicePeriod:id,budgetyear_id')->where('status', 'active')
                ->get()->first();
                $inv_period_lists = collect($budget_year->invoicePeriod)->pluck('id');
                return $q->select('*')->whereIn( 'inv_period_id_fk', $inv_period_lists);
            }
        ])->where('meter_id', $meter_id)->get()->first();
        return view('tabwater.usermeter_infos.index' , compact('usermeter_infos'));

    }
}
