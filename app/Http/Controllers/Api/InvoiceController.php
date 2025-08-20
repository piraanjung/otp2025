<?php

namespace App\Http\Controllers\Api;

use App\Models\Admin\BudgetYear;
use App\Http\Controllers\Api\FunctionsController;
use App\Http\Controllers\Controller;
use App\Models\Tabwater\AccTransactions;
use App\Models\Tabwater\Invoice;
use App\Models\Tabwater\InvoiceHistoty;
use App\Models\Tabwater\InvoicePeriod;
use App\Models\Admin\Subzone;
use App\Models\Tabwater\UserMerterInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $start = microtime(true);

        //หา invoice_period ปัจจุบัน (active)
        if ($request->input('status') == 'active') {
            $status = $request->input('status');
            $presentInvoicePeriod = InvoicePeriod::where('status', '=', "active")->first();
            $invoices = Invoice::where('inv_period_id', $presentInvoicePeriod->id)
                ->with([
                    'invoice_period',
                    'users.user_profile',
                    'users.usermeter_info',
                    'users.usermeter_info.zone',
                    'users.usermeter_info.zone.subzone',
                    'recorder.user_profile'
                ])->get();

            foreach ($invoices as $invoice) {
                //หา owe
                $invoice->owe = Invoice::where('status', 'owe')
                    ->where('user_id', $invoice->user_id)
                    ->with(['invoice_period', 'recorder.user_profile'])->get();

                if ($invoice->owe !== null) {
                    foreach ($invoice->owe as $owe) {
                        $owe->used_water_net = $owe->currentmeter - $owe->lastmeter;
                        $owe->must_paid = $owe->used_water_net * $invoice->users->usermeter_info->counter_unit;
                    }
                }
                //คิดเงินค่าใช้น้ำปัจจุบัน
                if ($invoice->status != "init") {
                    $invoice->used_water_net = $invoice->currentmeter - $invoice->lastmeter;
                    $invoice->must_paid = $invoice->used_water_net * $invoice->users->usermeter_info->counter_unit;
                } else {
                    $invoice->used_water_net = "";
                    $invoice->must_paid = "";
                    $invoice->currentmeter = "";
                }
            }
            //หาจำนวนผู้ใช้งานที่แจ้งใบแจ้งนี้แล้ว

            return response()->json($invoices);
        }

        $columns = ['name', 'remainmeter', 'presentmeter'];
        $length = $request->input('length');
        $column = $request->input('column'); //Index
        $dir = $request->input('dir');
        $searchValue = $request->input('search');
        $query = Invoice::select('id');
        if ($searchValue) {
            $query->where(function ($query) use ($searchValue) {
                $query->where('id', 'like', '%' . $searchValue . '%')
                    ->orWhere('status', 'like', '%' . $searchValue . '%');
            });
        }
        $projects = $query->paginate($length);
        return ['data' => $projects, 'draw' => $request->input('draw')];
    }

    public function get_user_invoice($meter_id, $status = '')
    {
        $invoices = Invoice::where('meter_id_fk', $meter_id)
            ->with([
                'usermeterinfos' => function ($query) {
                    $query->select('meter_id', 'user_id', 'meternumber', 'undertake_zone_id', 'undertake_subzone_id', 'metertype_id');
                },
                'usermeterinfos.user' => function ($query) {
                    return $query->select('id', 'prefix', 'firstname', 'lastname', 'address', 'zone_id', 'phone', 'subzone_id', 'tambon_code', 'district_code', 'province_code');
                },
                'usermeterinfos.user.user_tambon' => function ($query) {
                    return $query->select('id', 'tambon_name');
                },
                'usermeterinfos.user.user_district' => function ($query) {
                    return $query->select('id', 'district_name');
                },
                'usermeterinfos.user.user_province' => function ($query) {
                    return $query->select('id', 'province_name');
                },
                'invoice_period' => function ($query) {
                    return $query->select('id', 'inv_p_name', 'budgetyear_id');
                },
                'invoice_period.budgetyear' => function ($query) {
                    return $query->select('id', 'budgetyear_name', 'status');
                },
                'usermeterinfos.undertake_zone' => function ($query) {
                    return $query->select('id', 'zone_name as undertake_zone_name');
                },
                'usermeterinfos.undertake_subzone' => function ($query) {
                    return $query->select('id', 'subzone_name as undertake_subzone_name');
                },
                'usermeterinfos.user.user_zone' => function ($query) {
                    return $query->select('id', 'zone_name as user_zone_name');
                },
                'usermeterinfos.meter_type' => function ($query) {
                    return $query->select('id', 'price_per_unit');
                },
            ])
            ->orderBy('inv_period_id_fk', 'desc');
        if ($status != '') {
            if ($status == 'inv_and_owe') {
                $invoices = $invoices->whereIn('status', ['invoice', 'owe']);
            }
        }

        $invoices = $invoices->get([
            'inv_id',
            'meter_id_fk',
            'inv_period_id_fk',
            'lastmeter',
            'currentmeter',
            'water_used',
            'inv_type',
            'paid',
            'vat',
            'totalpaid',
            'acc_trans_id_fk',
            'updated_at',
            'status'
        ]);

        return response()->json(collect($invoices)->flatten());
    }

    public function get_invoice_and_invoice_history($meter_id, $status = "")
    {
        $invoice = Invoice::where('meter_id_fk', $meter_id)
            ->with([
                'usermeterinfos' => function ($query) {
                    $query->select('meter_id', 'user_id', 'meternumber', 'undertake_zone_id', 'undertake_subzone_id', 'metertype_id', 'submeter_name');
                },
                'usermeterinfos.user' => function ($query) {
                    return $query->select('id', 'prefix', 'firstname', 'lastname', 'address', 'zone_id', 'phone', 'subzone_id', 'tambon_code', 'district_code', 'province_code');
                },
                'usermeterinfos.user.user_tambon' => function ($query) {
                    return $query->select('id', 'tambon_name');
                },
                'usermeterinfos.user.user_district' => function ($query) {
                    return $query->select('id', 'district_name');
                },
                'usermeterinfos.user.user_province' => function ($query) {
                    return $query->select('id', 'province_name');
                },
                'invoice_period' => function ($query) {
                    return $query->select('id', 'inv_p_name', 'budgetyear_id');
                },
                'invoice_period.budgetyear' => function ($query) {
                    return $query->select('id', 'budgetyear_name', 'status');
                },
                'usermeterinfos.undertake_zone' => function ($query) {
                    return $query->select('id', 'zone_name as undertake_zone_name');
                },
                'usermeterinfos.undertake_subzone' => function ($query) {
                    return $query->select('id', 'subzone_name as undertake_subzone_name');
                },
                'usermeterinfos.user.user_zone' => function ($query) {
                    return $query->select('id', 'zone_name as user_zone_name');
                },
                'usermeterinfos.meter_type' => function ($query) {
                    return $query->select('id', 'price_per_unit');
                },
            ]);
        if ($status != '') {

            $invoice_history =  $status == 'inv_and_owe' ? $invoice->whereIn('status', ['owe', 'invioce']) : $invoice->where('status', $status);
        }

        $invoice = $invoice->orderBy('inv_period_id_fk', 'desc')->get();

        $invoice_history = InvoiceHistoty::where('meter_id_fk', $meter_id)
            ->with([
                'usermeterinfos' => function ($query) {
                    $query->select('meter_id', 'user_id', 'meternumber', 'undertake_zone_id', 'undertake_subzone_id', 'metertype_id');
                },
                'usermeterinfos.user' => function ($query) {
                    return $query->select('id', 'prefix', 'firstname', 'lastname', 'address', 'zone_id', 'phone', 'subzone_id', 'tambon_code', 'district_code', 'province_code');
                },
                'usermeterinfos.user.user_tambon' => function ($query) {
                    return $query->select('id', 'tambon_name');
                },
                'usermeterinfos.user.user_district' => function ($query) {
                    return $query->select('id', 'district_name');
                },
                'usermeterinfos.user.user_province' => function ($query) {
                    return $query->select('id', 'province_name');
                },
                'invoice_period' => function ($query) {
                    return $query->select('id', 'inv_p_name', 'budgetyear_id');
                },
                'invoice_period.budgetyear' => function ($query) {
                    return $query->select('id', 'budgetyear_name', 'status');
                },
                'usermeterinfos.undertake_zone' => function ($query) {
                    return $query->select('id', 'zone_name as undertake_zone_name');
                },
                'usermeterinfos.undertake_subzone' => function ($query) {
                    return $query->select('id', 'subzone_name as undertake_subzone_name');
                },
                'usermeterinfos.user.user_zone' => function ($query) {
                    return $query->select('id', 'zone_name as user_zone_name');
                },
                'usermeterinfos.meter_type' => function ($query) {
                    return $query->select('id', 'price_per_unit');
                },
            ]);
        if ($status != '') {

            $invoice_history =  $status == 'inv_and_owe' ? $invoice_history->whereIn('status', ['owe', 'invioce']) : $invoice_history->where('status', $status);
        }

        $invoice_history =  $invoice_history->orderBy('inv_period_id_fk', 'desc')->get();


        $invoiceMerge = collect($invoice)->merge($invoice_history);
        return  response()->json(collect($invoiceMerge)->flatten());
    }

    public function getLastInvoice($user_id)
    {
        $invoice = Invoice::where('user_id', $user_id)
            ->where('status', '!=', 'deleted')
            ->with('usermeterinfos.user_profile', 'invoice_period')
            ->orderBy('inv_period_id', 'desc')
            ->limit(1)
            ->get();
        return response()->json($invoice);
    }

    public function getInvoiceByInvoiceId($inv_id)
    {
        $invoice = Invoice::where('id', $inv_id)
            ->with(
                'user_profile',
                'invoice_period',
                'usermeterinfos',
                'usermeterinfos.zone',
                'user_profile.province',
                'user_profile.district',
            )
            ->orderBy('inv_period_id', 'desc')
            ->get();
        return response()->json(collect($invoice)->flatten());
    }

    public function getInvoiceByInvoiceUserId($user_id)
    {
        $invoice = Invoice::where('user_id', $user_id)
            ->with(
                'user_profile',
                'invoice_period',
                'usermeterinfos',
                'usermeterinfos.zone',
                'user_profile.province',
                'user_profile.district',
            )
            ->orderBy('inv_period_id', 'desc')
            ->get();
        return response()->json(collect($invoice));
    }

    public function invoice_history_current_budget_year($user_id)
    {
        $apiInvoiceCtrl = new InvoiceController();
        $user = UserMerterInfo::where('user_id', $user_id)
            ->with('user_profile', 'invoice_by_user_id', 'invoice.invoice_period')
            ->get();
        $fn = new FunctionsController;
        foreach ($user[0]->invoice as $u) {
            $date = explode(" ", $u->updated_at);
            $u->updated_at_th = $fn->engDateToThaiDateFormat($date[0]);
        }
        $invApi = new InvoiceController();
        // $inv_infos = \json_decode($invApi->getInvoiceByInvoiceUserId($user_id)->content(), true);
        $current_budget_year = BudgetYear::where('status', 'active')
            ->with('invoicePeriod')
            ->get();

        $invoicePeriodArray = collect($current_budget_year[0]->invoicePeriod)->pluck('id');

        $infos = collect($user[0]->invoice_by_user_id)->filter(function ($val) use ($invoicePeriodArray) {
            return in_array($val['inv_period_id'], collect($invoicePeriodArray)->toArray());
        });
        $user[0]['invoice_by_user_id_curr_bugget_year'] = $infos;
        return $user;
    }

    public function get_user_invoice_by_invId_and_mode($inv_id, $mode)
    {
        //ใบแจ้งหนี้ปััจจุบัน
        $invoice = Invoice::where('id', $inv_id)
            ->where('status', 'invoice')
            ->with([
                'invoice_period',
                'usermeterinfos.user',
                'usermeterinfos',
                'usermeterinfos.subzone',
                'usermeterinfos.subzone.undertaker_subzone.twman_info',
                'usermeterinfos.zone',
                'recorder'
            ])->get()->first();
        if (collect($invoice)->isEmpty()) {
            dd($inv_id);
        }
        //หา owe
        $invoice->owe = Invoice::where('status', 'owe')
            ->where('meter_id_fk', $invoice->meter_id_fk)
            ->with(['invoice_period', 'recorder'])->get();

        if ($invoice->owe !== null) {
            foreach ($invoice->owe as $owe) {
                $owe->used_water_net = $owe->currentmeter - $owe->lastmeter;
                $owe->must_paid = $owe->used_water_net * $invoice->usermeterinfos->meter_type->price_per_unit;
            }
        }
        //คิดเงินค่าใช้น้ำปัจจุบัน
        if ($invoice->status != "init") {
            $invoice->used_water_net = $invoice->currentmeter - $invoice->lastmeter;
            $invoice->must_paid = $invoice->used_water_net * $invoice->usermeterinfos->meter_type->price_per_unit;
        } else {
            $invoice->used_water_net = "";
            $invoice->must_paid = "";
            $invoice->currentmeter = "";
        }

        $funcCtrl = new FunctionsController();

        $invoice->invoice_period->th_startdate = $funcCtrl->engDateToThaiDateFormat($invoice->invoice_period->startdate);
        $invoice->invoice_period->th_enddate = $funcCtrl->engDateToThaiDateFormat($invoice->invoice_period->enddate);

        //หาการใช้น้ำ 5 เดือนล่าสุด
        $inv_history = Invoice::where('meter_id_fk', $invoice->meter_id_fk)
            ->with('invoice_period')
            ->where('inv_period_id_fk', '<', $invoice->inv_period_id_fk)
            ->orderBy('inv_period_id_fk', 'desc')
            ->take(5)->get();
        $invoice->inv_history = collect($inv_history)->reverse()->flatten();

        return response()->json($invoice);
    }
    public function update(Request $request, $invoice_id)
    {
        date_default_timezone_set('Asia/Bangkok');

        if ($invoice_id == -1) {
            //update จาก mobile แอพ
            $invoice = Invoice::where('inv_period_id', $request->get('inv_period_id'))
                ->where('meter_id', $request->get('meter_id'))
                ->update([
                    'currentmeter' => $request->get('currentmeter'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            $status = $invoice == 1 ? 200 : 204;
        } else {
            $invoice = Invoice::find($invoice_id);
            $invoice->currentmeter = $request->get('currmeter_value');
            $invoice->status = 'invoice';
            $invoice->updated_at = date('Y-m-d H:i:s');
            $invoice->update();
            $status = $invoice == 1 ? 200 : 204;
        }

        return response()->json(['res' => $invoice, 'status' => $status]);
    }

    public function update2(Request $request)
    {
        date_default_timezone_set('Asia/Bangkok');
        //update จาก mobile แอพ
        $invoiceSql = Invoice::where('inv_period_id', $request->get('inv_period_id'))
            ->where('meter_id', $request->get('meter_id'));

        $update = $invoiceSql->update([
            'currentmeter' => $request->get('currentmeter'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $invoice = $invoiceSql->get();
        $code = 200;
        $invoice[0]->owe_count = 0;
        $invoice[0]->owe_sum = 0;
        if ($update == 1) {
            $oweRes = Invoice::where('meter_id', $request->get('meter_id'))
                ->where('status', 'owe')
                ->get(['id', 'lastmeter', 'currentmeter']);
            if (collect($oweRes)->count() > 0) {
                $invoice[0]->owe_count = collect($oweRes)->count();
                $sum = 0;
                foreach ($oweRes as $oweR) {
                    $sum += ($oweR->currentmeter - $oweR->lastmeter) * 8;
                }
                $invoice[0]->owe_sum = $sum;
            }
        } else {
            $code = 204;
        }

        return response()->json(['res' => $invoice, 'status' => $code]);
    }

    public function create(Request $request)
    {
        date_default_timezone_set('Asia/Bangkok');
        $invPeriod = InvoicePeriod::where('status', 'active')->get('id');
        $findRowDuplicate = Invoice::where('inv_period_id', $request->get('inv_period_id'))
            ->where('meter_id', $request->get('meter_id'))->get();

        if (collect($findRowDuplicate)->count() > 1) {
            $invoice = [
                'owe_count' => 0,
                'owe_sum' => 0,
            ];
            $code = 204;
            return response()->json(['res' => $invoice, 'status' => $code]);
        }
        $invoice = new Invoice();
        $invoice->inv_period_id = $invPeriod[0]->id;
        $invoice->user_id = $request->get('user_id');
        $invoice->meter_id = $request->get('meter_id');
        $invoice->lastmeter = $request->get('lastmeter');
        $invoice->currentmeter = $request->get('currentmeter');
        $invoice->recorder_id = $request->get('recorder_id');
        $invoice->status = 'invoice';
        $invoice->created_at = date('Y-m-d H:i:s');
        $invoice->updated_at = date('Y-m-d H:i:s');
        if ($invoice->save()) {
            $invoice->owe_count = 0;
            $invoice->owe_sum = 0;
            $code = 200;
            $oweRes = Invoice::where('meter_id', $request->get('meter_id'))
                ->where('status', 'owe')
                ->get(['id', 'lastmeter', 'currentmeter']);
            if (collect($oweRes)->count() > 0) {
                $invoice->owe_count = collect($oweRes)->count();
                $sum = 0;
                foreach ($oweRes as $oweR) {
                    $sum += $oweR->currentmeter - $oweR->lastmeter == 0 ? 10 : ($oweR->currentmeter - $oweR->lastmeter) * 8;
                }
                $invoice->owe_sum = $sum;
            }
        } else {
            $invoice->owe_count = 0;
            $invoice->owe_sum = 0;
            $code = 204;
        }
        return response()->json(['res' => $invoice, 'status' => $code]);
    }
    public function create_for_mobile_app(Request $request)
    {
        
        date_default_timezone_set('Asia/Bangkok');
        //create acc_trans table
        $invSql = Invoice::where('inv_id', $request->get('inv_id'));
        $getInvMeterIdFK = $invSql->get('meter_id_fk');


        $invOweAndInvoiceStatusSql = Invoice::where('meter_id_fk', $getInvMeterIdFK[0]->meter_id_fk)
            ->with(['invoice_period' => function ($q) {
                return $q->select('id', 'inv_p_name');
            }])
            ->whereIn('status', ['invoice', 'owe']);
        $invOweAndInvoiceStatus    = $invOweAndInvoiceStatusSql->get(['inv_period_id_fk', 'paid', 'vat', 'acc_trans_id_fk', 'totalpaid', 'status']);
        
        $accTransIdFK = 0;
        if (collect($invOweAndInvoiceStatus)->isNotEmpty()) {
            $accTransIdFK = $invOweAndInvoiceStatus[0]->acc_trans_id_fk;
        } else {
            $newAccTrans = AccTransactions::create([
                'user_id_fk'    => $getInvMeterIdFK[0]->meter_id_fk,
                'paidsum'       => 0,
                'vatsum'        => 0,
                'totalpaidsum'  => 0,
                'net'           => 0,
                'cashier'       => 0,
                'status'        => 2, //hold
                'inv_no_fk'     => 0,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ]);
            $accTransIdFK = $newAccTrans->id;
        }
        $water_used = $request->get('water_used');
        $paid = $water_used * 6;
        $vat = $water_used == 0 ? 0 : $paid * 0;
        $reserve_meter = 10;
        $updateInv = $invSql->update([
            'currentmeter'      => $request->get('currentmeter'),
            'water_used'        => $request->get('water_used'),
            'inv_type'          => $water_used == 0 ? 'r' : 'u',
            'paid'              => $paid,
            'vat'               => $vat,
            'totalpaid'         => $paid + $reserve_meter,
            'recorder_id'       => $request->get('recorder_id'),
            'acc_trans_id_fk'   => $accTransIdFK,
            'status'            => 'invoice',
            'created_at'        => date('Y-m-d H:i:s'),
            'updated_at'        => date('Y-m-d H:i:s'),
        ]);

        // $updateInvAccTransIdFK = $invOweAndInvoiceStatusSql->update([
        //     'acc_trans_id_fk' => $newAccTrans->id,
        //     'updated_at'      => date('Y-m-d H:i:s'),
        // ]);

        $date = date_create(date('Y-m-d'));
        date_add($date, date_interval_create_from_date_string("15 days"));
        $owe_sum = collect($invOweAndInvoiceStatus)->filter(function ($v) {
            return $v->status == 'owe';
        })->sum('totalpaid');
        $vat_sum = collect($invOweAndInvoiceStatus)->sum('vat');
        $totalpaidsum = collect($invOweAndInvoiceStatus)->isEmpty() ?  $request->get('totalpaid') :  collect($invOweAndInvoiceStatus)->sum('totalpaid');
          $net_paid = Invoice::where('meter_id_fk', $getInvMeterIdFK[0]->meter_id_fk)
            ->whereIn('status', ['invoice', 'owe'])->get(['totalpaid']);
        $datas = [
            'acc_trans_id_fk'   => $accTransIdFK,
            'user_id_fk'        => $getInvMeterIdFK[0]->meter_id_fk,
            'vatsum'            => floatval($vat_sum),
            'invoic_status'     => collect($invOweAndInvoiceStatus)->filter(function ($v) {
                return $v->status == 'invoice';
            })->values(),
            'owe_count'         => collect($invOweAndInvoiceStatus)->filter(function ($v) {
                return $v->status == 'owe';
            })->count(),
            'owe_sum'           => floatval($owe_sum),
            'net_paid'          => floatval(collect($net_paid)->sum('totalpaid')),
            'expire_date'       => date_format($date, "Y-m-d"),
            'water_used'        => $water_used,
            'paid'              => $paid,
            'vat'               => $vat,
            'submeter_name'     => $getInvMeterIdFK[0]->usermeterinfos->submeter_name
        ];

        $res = $updateInv > 0 ? ['code' => 200, 'datas' => $datas] : ['code' => 204];


        return response()->json(["res" => $res]);
    }

    public function create_for_mobile_app2(Request $request)
    {
        
        date_default_timezone_set('Asia/Bangkok');
        //create acc_trans table
        $invSql = Invoice::where('inv_id', $request->get('inv_id'));
        $getInvMeterIdFK = $invSql
        ->with(['invoice_period' => function ($q) {
            return $q->select('id', 'inv_p_name');
        }])
        ->get(['meter_id_fk', 'inv_no', 'inv_period_id_fk', 'updated_at']);


        $invOweAndInvoiceStatusSql = Invoice::where('meter_id_fk', $getInvMeterIdFK[0]->meter_id_fk)
            ->with(['invoice_period' => function ($q) {
                return $q->select('id', 'inv_p_name');
            }])
            ->whereIn('status', ['invoice', 'owe']);
        $invOweAndInvoiceStatus    = $invOweAndInvoiceStatusSql->get(['inv_period_id_fk', 'paid', 'inv_no', 'vat', 'acc_trans_id_fk', 
        'totalpaid', 'status']);
        
        $accTransIdFK = 0;
        if (collect($invOweAndInvoiceStatus)->isNotEmpty()) {
            $accTransIdFK = $invOweAndInvoiceStatus[0]->acc_trans_id_fk;
        } else {
            $newAccTrans = AccTransactions::create([
                'user_id_fk'    => $getInvMeterIdFK[0]->meter_id_fk,
                'paidsum'       => 0,
                'vatsum'        => 0,
                'totalpaidsum'  => 0,
                'net'           => 0,
                'cashier'       => 0,
                'status'        => 2, //hold
                'inv_no_fk'     => 0,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ]);
            $accTransIdFK = $newAccTrans->id;
        }
        $water_used = $request->get('water_used');
        $paid = $water_used * 6;
        $vat = $water_used == 0 ? 0 : $paid * 0;
        $reserve_meter = 10;
        $updateInv = $invSql->update([
            'currentmeter'      => $request->get('currentmeter'),
            'water_used'        => $request->get('water_used'),
            'inv_type'          => $water_used == 0 ? 'r' : 'u',
            'paid'              => $paid,
            'vat'               => $vat,
            'reserve_meter'     => $reserve_meter,
            'totalpaid'         => $paid + $reserve_meter,
            'recorder_id'       => $request->get('recorder_id'),
            'acc_trans_id_fk'   => $accTransIdFK,
            'status'            => 'invoice',
            'created_at'        => date('Y-m-d H:i:s'),
            'updated_at'        => date('Y-m-d H:i:s'),
        ]);

        // $updateInvAccTransIdFK = $invOweAndInvoiceStatusSql->update([
        //     'acc_trans_id_fk' => $newAccTrans->id,
        //     'updated_at'      => date('Y-m-d H:i:s'),
        // ]);

        $date = date_create(date('Y-m-d'));
        date_add($date, date_interval_create_from_date_string("15 days"));
        $owe_sum = collect($invOweAndInvoiceStatus)->filter(function ($v) {
            return $v->status == 'owe';
        })->sum('totalpaid');
        $vat_sum = collect($invOweAndInvoiceStatus)->sum('vat');
        $totalpaidsum = collect($invOweAndInvoiceStatus)->isEmpty() ?  $request->get('totalpaid') :  collect($invOweAndInvoiceStatus)->sum('totalpaid');
          $net_paid = Invoice::where('meter_id_fk', $getInvMeterIdFK[0]->meter_id_fk)
            ->whereIn('status', ['invoice', 'owe'])->get(['totalpaid']);
        $datas = [
            'acc_trans_id_fk'   => $accTransIdFK,
            // 'inv_no'            => $getInvMeterIdFK[0]->inv_no,
            'user_id_fk'        => $getInvMeterIdFK[0]->meter_id_fk,
            'vatsum'            => floatval($vat_sum),
            'invoic_status'     => collect($invOweAndInvoiceStatus)->filter(function ($v) {
                return $v->status == 'invoice';
            })->values(),
            'owe_status'     => collect($invOweAndInvoiceStatus)->filter(function ($v) {
                return $v->status == 'owe';
            })->values(),
            'owe_count'         => collect($invOweAndInvoiceStatus)->filter(function ($v) {
                return $v->status == 'owe';
            })->count(),
            'owe_sum'           => floatval($owe_sum),
            'net_paid'          => floatval(collect($net_paid)->sum('totalpaid')),
            'expire_date'       => date_format($date, "Y-m-d"),
            'water_used'        => $water_used,
            'paid'              => $paid,
            'vat'               => $vat,
            'current_period'    => $getInvMeterIdFK[0]->invoice_period,
            'submeter_name'     => $getInvMeterIdFK[0]->usermeterinfos->submeter_name,
            'updated_at'        => '2024-12-26 14:30:00'//$getInvMeterIdFK[0]->updated_at,

        ];

        $res = $updateInv > 0 ? ['code' => 200, 'datas' => $datas] : ['code' => 204];


        return response()->json(["res" => $res]);
    }

    public function paid_invoice($invoice_id)
    {
        date_default_timezone_set('Asia/Bangkok');

        $invoice = Invoice::find($invoice_id);
        $invoice->status = 'paid';
        $invoice->update();
        //เพิ่มจำ
        return response()->json($invoice);
    }

    public function totalWaterUsed()
    {
        $presentInvoicePeriod = InvoicePeriod::where('status', '=', "active")->first();
        $query = Invoice::where('inv_period_id', $presentInvoicePeriod->id)
            ->get(['meter_id', 'currentmeter']);
        return collect($query)->sum('currentmeter');
    }

    public function totalWaterByInvPeriod($inv_id)
    {
        $query = Invoice::where('inv_period_id', $inv_id)
            ->get();

        if (collect($query)->isEmpty()) {
            return [];
        }

        $q = collect($query);
        $sum = collect($query)->pipe(function ($q) {
            $sumCurrentmer = $q->sum('currentmeter');
            $sumLastmeter  = $q->sum('lastmeter');
            return $sumCurrentmer - $sumLastmeter;
        });
        $invPeriod = InvoicePeriod::where('id', $query[0]->inv_period_id)->get('inv_period_name');

        return [$sum, $invPeriod[0]->inv_period_name];
    }

    public function receipt_bill($user_id)
    {
        return $user_id;
    }

    public function zone_edit($subzone_id)
    {
        $presentInvoicePeriod = InvoicePeriod::where('status', 'active')->get()->first();
        $zoneInfo = Subzone::where('id', $subzone_id)->with([
            'zone',
        ])->get(['zone_id', 'subzone_name']);

        $sql = DB::table('user_meter_infos as umf')
            ->join('invoice as iv', 'iv.meter_id_fk', '=', 'umf.meter_id')
            ->join('users as u', 'u.id', '=', 'umf.user_id')
            ->join('zones as z', 'z.id', '=', 'umf.undertake_zone_id')
            ->join('subzones as sz', 'sz.id', '=', 'umf.undertake_subzone_id')
            ->where('iv.inv_period_id_fk', '=', $presentInvoicePeriod->id)
            ->where('iv.status', '=', 'invoice')
            ->where('umf.undertake_subzone_id', '=', $subzone_id)
            ->where('umf.status', '=', 'active');

        $invoice = $sql->select(
            'umf.user_id',
            'umf.meternumber',
            'umf.undertake_subzone_id',
            'umf.undertake_zone_id',
            'u.firstname',
            'u.lastname',
            'u.address',
            'iv.lastmeter',
            'iv.currentmeter',
            'iv.meter_id_fk',
            'u.zone_id as user_zone_id',
            'iv.comment',
            'iv.status',
            DB::raw('iv.currentmeter - iv.lastmeter as meter_net'),
            DB::raw('(iv.currentmeter - iv.lastmeter)*8 as total'),
        )->get();

        //ถ้ายังไม่มีข้อมูล invoice ในรอบบิลปัจจุบัน ของ subzone ที่เลือกให้ยย้อนกลับ
        if (collect($invoice)->isEmpty()) {
            return response()->json([
                'memberHasInvoice' => [],
                'presentInvoicePeriod' => $presentInvoicePeriod->inv_period_name,
                'zoneInfo' => $zoneInfo,

            ]);
        }

        $zoneInfoSql = $sql
            ->select(
                'umf.user_id',
                'umf.meternumber',
                'z.zone_name as undertake_zone',
                'z.id as undertake_zone_id',
                'sz.subzone_name as undertake_subzone',
                'sz.id as undertake_subzone_id',
            )
            ->get();

        foreach ($invoice as $iv) {
            $funcCtrl = new FunctionsController();
            $iv->user_id_string = $funcCtrl->createInvoiceNumberString($iv->user_id);
        }

        $memberHasInvoice = collect($invoice)->sortBy('user_id')->toArray();

        return response()->json([
            'memberHasInvoice' => $invoice,
            'presentInvoicePeriod' => $presentInvoicePeriod->inv_period_name,
            'zoneInfo' => $zoneInfo[0]->subzone_name,

        ]);
    }

    public function invoiced_lists($subzone_id)
    {
        $presentInvoicePeriod = InvoicePeriod::where('status', 'active')->get()->first();
        $sql = DB::table('user_meter_infos as umf')
            ->join('invoice as iv', 'iv.meter_id_fk', '=', 'umf.meter_id')
            ->join('users as u', 'u.id', '=', 'umf.user_id')
            ->join('zones as z', 'z.id', '=', 'umf.undertake_zone_id')
            ->join('subzones as sz', 'sz.id', '=', 'umf.undertake_subzone_id')
            ->where('iv.inv_period_id_fk', '=', $presentInvoicePeriod->id)
            ->where('iv.status', '=', 'invoice')
            ->where('umf.undertake_subzone_id', '=', $subzone_id);

        $invoice = $sql->get([
            'umf.user_id',
            'umf.meternumber',
            'umf.undertake_subzone_id',
            'umf.undertake_zone_id',
            'u.firstname',
            'u.lastname',
            'u.address',
            'iv.lastmeter',
            'iv.currentmeter',
            'iv.meter_id_fk',
            'u.zone_id as user_zone_id',
            'iv.id',
            DB::raw('iv.currentmeter - iv.lastmeter as meter_net'),
            DB::raw('(iv.currentmeter - iv.lastmeter)*8 as total'),
        ]);
        $zoneInfo = $sql->select([
            'umf.user_id',
            'umf.meternumber',
            'z.zone_name as undertake_zone',
            'z.id as undertake_zone_id',
            'sz.subzone_name as undertake_subzone',
            'sz.id as undertake_subzone_id',
        ])->limit(1)->get();

        foreach ($invoice as $iv) {
            $funcCtrl = new FunctionsController();
            $iv->user_id_string = intval($funcCtrl->createInvoiceNumberString($iv->user_id));
        }

        return response()->json([
            'presentInvoicePeriod'  => ['inv_period_name' => $presentInvoicePeriod->inv_p_name, 'budgetyear' => $presentInvoicePeriod->budgetyear->budgetyear],
            'zoneInfo' => [
                'undertake_zone'    => $zoneInfo[0]->undertake_zone,
                'undertake_subzone' => $zoneInfo[0]->undertake_subzone,
                'undertake_zone_id' => $zoneInfo[0]->undertake_zone_id,
                'undertake_subzone_id' => $zoneInfo[0]->undertake_subzone_id,
            ],
            'invoicedlists' => $invoice,
            'subzone_id'    => $subzone_id,
        ]);
    }
}
