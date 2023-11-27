<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\FunctionsController;
use App\Models\Invoice;
use App\Models\InvoicePeriod;
use App\Models\MeterType;
use App\Models\Subzone;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Api\ReportsController as apiReportCtrl;
class ReportsController extends Controller
{
    public function owe()
    {
        $invoice_owe_status = Invoice::where("status", "owe")->get()->values();


        $owe_users = collect($invoice_owe_status)->groupBy(['usermeterinfos.user.id', 'meter_id_fk'])->values()->map(function ($arr, $key) {
            $infos = collect($arr)->values();

            $rowspan_sum = 0;
            foreach ($infos as $info) {
                $info[0]->rowspan = collect($info)->count();
                $rowspan_sum += $info[0]->rowspan;

                foreach ($info as $key2 => $inv) {

                    $inv->water_used = $inv->currentmeter - $inv->lastmeter;
                    $total = $inv->water_used * $inv->usermeterinfos->meter_type->price_per_unit;
                    $inv->total = $total == 0 ? 10.00 :$total;
                    $inv->reservemeter = $total == 0 ? 10.00 : 0;
                    $inv->clude_total = $total == 0 ? 0: $total;
                    $inv->vat7= $inv->total *0.07;
                    $inv->total_net = $inv->total + $inv->vat7;
                }




            }
            $infos[0][0]->rowspan_sum = $rowspan_sum;
            return $infos;
        });

        $reservemeter_sum = collect($owe_users)->sum(function ($arr,) {
            return collect($arr)->sum(function ($ar) {
                return collect($ar)->sum(function ($b) {
                    return $b->reservemeter;
                });
            });
        });

        $crudetotal_sum = collect($owe_users)->sum(function ($arr,) {
            return collect($arr)->sum(function ($ar) {
                return collect($ar)->sum(function ($b) {
                    return $b->clude_total;
                });
            });
        });

        $owe_zones =  collect($invoice_owe_status)->groupBy(['usermeterinfos.undertake_zone_id'])->map(function ($arr, $key) {
            return  Zone::where('id', $key)->first(['id', 'zone_name']);
        })->sortByDesc('id')->reverse();

        $owe_inv_periods =  collect($invoice_owe_status)->groupBy(['inv_period_id_fk'])->map(function ($arr, $key) {
            return  InvoicePeriod::where('id', $key)->first(['id', 'inv_p_name']);
        })->sortByDesc('id')->reverse();

        return view("reports.owe", compact("owe_users", 'reservemeter_sum', 'crudetotal_sum', 'owe_zones', 'owe_inv_periods'));
    }
    public function owe_search(Request $request)
    {
        $subzone        = collect($request->get('subzone'))->isEmpty()    || $request->get('subzone')   =="all"     ? [] : $request->get('subzone');
        $zone           = collect($request->get('zone1'))->isEmpty()      || $request->get('zone1')     =="all"     ? [] : $request->get('zone1');
        $inv_period     = collect($request->get('inv_period'))->isEmpty() || $request->get('inv_period')=="all"     ? [] : $request->get('inv_period');
        $invoice_owe_status = Invoice::where("status", "owe");
        if(collect($inv_period)->isNotEmpty()) {
            $invoice_owe_status = $invoice_owe_status->whereIn("inv_period_id_fk", $inv_period);
        }
        $invoice_owe_status = $invoice_owe_status->get()->values();
        if(collect($zone)->isNotEmpty()) {
            $invoice_owe_status = collect($invoice_owe_status)->filter(function ($item) use ($zone) {
                return in_array($item->usermeterinfos->undertake_zone_id, $zone);
            });
        }

        if(collect($subzone)->isNotEmpty()) {
            $invoice_owe_status = collect($invoice_owe_status)->filter(function ($item) use ($subzone) {
                return in_array($item->usermeterinfos->undertake_subzone_id, $subzone);
            });
        }

        $owe_users = collect($invoice_owe_status)->groupBy(['usermeterinfos.user.id', 'meter_id_fk'])->values()->map(function ($arr, $key) {
            $infos = collect($arr)->values();

            $rowspan_sum = 0;
            foreach ($infos as $info) {
                $info[0]->rowspan = collect($info)->count();
                $rowspan_sum += $info[0]->rowspan;

                foreach ($info as $key2 => $inv) {
                    $inv->water_used = $inv->currentmeter - $inv->lastmeter;
                    $total = $inv->water_used * $inv->usermeterinfos->meter_type->price_per_unit;
                    $inv->total = $total == 0 ? 10.00 :$total;
                    $inv->reservemeter = $total == 0 ? 10.00 : 0;
                    $inv->clude_total = $total == 0 ? 0: $total;
                    $inv->vat7= $inv->total *0.07;
                    $inv->total_net = $inv->total + $inv->vat7;
                }
            }
            $infos[0][0]->rowspan_sum = $rowspan_sum;
            return $infos;
        });

        $reservemeter_sum = collect($owe_users)->sum(function ($arr,) {
            return collect($arr)->sum(function ($ar) {
                return collect($ar)->sum(function ($b) {
                    return $b->reservemeter;
                });
            });
        });

        $crudetotal_sum = collect($owe_users)->sum(function ($arr,) {
            return collect($arr)->sum(function ($ar) {
                return collect($ar)->sum(function ($b) {
                    return $b->clude_total;
                });
            });
        });

        $owe_zones =  collect($invoice_owe_status)->groupBy(['usermeterinfos.undertake_zone_id'])->map(function ($arr, $key) {
            return  Zone::where('id', $key)->first(['id', 'zone_name']);
        })->sortByDesc('id')->reverse();

        $owe_inv_periods =  collect($invoice_owe_status)->groupBy(['inv_period_id_fk'])->map(function ($arr, $key) {
            return  InvoicePeriod::where('id', $key)->first(['id', 'inv_p_name']);
        })->sortByDesc('id')->reverse();


        return view("reports.owe", compact("owe_users", 'reservemeter_sum', 'crudetotal_sum', 'owe_zones', 'owe_inv_periods'));
    }

    public function dailypayment(Request $request){
        date_default_timezone_set('Asia/Bangkok');
        $fnCtrl = new FunctionsController();
        $apiReportCtrl = new apiReportCtrl();
        if (collect($request)->isEmpty() || $request->get('nav') == 'nav') {
            $a = [
                'zone_id' => 'all',
                'subzone_id' => 'all',
                'fromdate' => date('Y-m-d'),
                'todate' => date('Y-m-d'),
                'accounts_id_fk' => 'all',
                'cashier_selected' => 0,
                'inv_period_id_fk' => InvoicePeriod::where('status', 'active')->first('id')->id,
            ];
            $request->merge($a);
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
        $inv_period_id  = $request->get('inv_period_id_fk');
        $zone_id        = $request->get('zone_id');
        $subzone_id     = $request->get('subzone_id');
        $fromdate       = '2023-11-09';//$request->get('fromdate');
        $todate         = '2023-11-09';//$request->get('todate');
        $accounts_id_fk     = $request->get('accounts_id_fk');

        $paids = Invoice::where('inv_period_id_fk', $inv_period_id)
                    ->where(DB::raw("date(updated_at)"), '>=', $fromdate)
                    ->where(DB::raw("date(updated_at)"), '<=', $todate);
        if ($accounts_id_fk != 'all' ) {
            $paids = $paids->where('accounts_id_fk', '=', $accounts_id_fk);
        }

        $paidInfos = $paids->with([
            'usermeterinfos' => function ($query) {
                $query->select('meter_id', 'user_id');
            }
        ])

        ->get()->groupBy('usermeterinfos.user_id');


        $zones          = Zone::all();
        $subzones       = $zone_id != 'all' && $subzone_id != 'all' ? Subzone::all() : 'all';
        $inv_periods    = InvoicePeriod::orderBy('id', 'desc')->get(['id', 'inv_p_name']);
        $receiptions    = User::whereIn('role_id',[1, 2])->get(['id','lastname', 'firstname']);
        $cashier_name = User::where('id', $request->get('cashier_selected'))->first(['firstname', 'lastname']);
        return view('reports.dailypayment', compact('zones', 'subzones', 'paidInfos',
            'subzone_id', 'zone_id',
            'receiptions','cashier_name',
            'fromdateTh', 'todateTh'
        ));
    }
}
