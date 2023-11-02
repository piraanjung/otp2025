<?php

namespace App\Http\Controllers\Api;

use App\Models\BudgetYear;
use App\Http\Controllers\Api\FunctionsController;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoicePeriod;
use App\Models\Subzone;
use App\Models\UserMerterInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        //หา invoice_period ปัจจุบัน (active)
        if ($request->input('status') == 'active') {
            $status = $request->input('status');
            $presentInvoicePeriod = InvoicePeriod::where('status', '=', "active")->first();
            $invoices = Invoice::where('inv_period_id', $presentInvoicePeriod->id)
                ->with(['invoice_period',
                    'users.user_profile',
                    'users.usermeter_info',
                    'users.usermeter_info.zone',
                    'users.usermeter_info.zone.subzone',
                    'recorder.user_profile'])->get();

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

    public function get_user_invoice($meter_id)
    {
        //หา invoice และ owe ของ user
        $invoice = Invoice::where('user_id', $meter_id)
            ->with(['usermeterinfos.user_profile' => function ($query) {
                return $query->select('user_id', 'name', 'address', 'zone_id', 'phone', 'subzone_id', 'district_code', 'province_code');
            }, 'invoice_period' => function ($query) {
                return $query->select('id', 'inv_period_name');
            },
                'usermeterinfos' => function ($query) {
                    return $query->select('user_id', 'meternumber', 'undertake_zone_id', 'undertake_subzone_id');
                }, 'usermeterinfos.zone' => function ($query) {
                    return $query->select('id', 'zone_name as undertake_zone_name');
                },
                'usermeterinfos.subzone' => function ($query) {
                    return $query->select('id', 'subzone_name as undertake_subzone_name');
                },
                'usermeterinfos.user_profile.zone' => function ($query) {
                    return $query->select('id', 'zone_name as user_zone_name');
                },
                'usermeterinfos.user_profile.province' => function ($query) {
                    return $query->select('province_code', 'province_name');
                }, 'usermeterinfos.user_profile.district' => function ($query) {
                    return $query->select('district_code', 'district_name');
                },
            ])
            // ->where('deleted', 0)
            ->orderBy('inv_period_id', 'desc')
            ->get(['id', 'user_id', 'inv_period_id', 'lastmeter', 'currentmeter', 'status']);

        $invAndOweInvoice = collect($invoice)->filter(function ($val) {
            // dd($val);
            return $val->status == 'owe' || $val->status == 'invoice';
        });

        return response()->json(collect($invAndOweInvoice)->flatten());
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
            ->with('user_profile', 'invoice_period',
                'usermeterinfos', 'usermeterinfos.zone',
                'user_profile.province', 'user_profile.district',
            )
            ->orderBy('inv_period_id', 'desc')
            ->get();
        return response()->json(collect($invoice)->flatten());
    }

    public function getInvoiceByInvoiceUserId($user_id)
    {
        $invoice = Invoice::where('user_id', $user_id)
            ->with('user_profile', 'invoice_period',
                'usermeterinfos', 'usermeterinfos.zone',
                'user_profile.province', 'user_profile.district',
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
            ->with(['invoice_period',
                'user_profile',
                'usermeterinfos',
                'usermeterinfos.subzone',
                'usermeterinfos.subzone.undertaker_subzone.user_profile',
                'usermeterinfos.zone',
                'recorder.user_profile'])->get()->first();
        if (collect($invoice)->isEmpty()) {
            dd($inv_id);
        }
        //หา owe
        $invoice->owe = Invoice::where('status', 'owe')
            ->where('user_id', $invoice->user_id)
            ->with(['invoice_period', 'recorder.user_profile'])->get();

        if ($invoice->owe !== null) {
            foreach ($invoice->owe as $owe) {
                $owe->used_water_net = $owe->currentmeter - $owe->lastmeter;
                $owe->must_paid = $owe->used_water_net * 8; //$invoice->users->usermeter_info->counter_unit;
            }
        }
        //คิดเงินค่าใช้น้ำปัจจุบัน
        if ($invoice->status != "init") {
            $invoice->used_water_net = $invoice->currentmeter - $invoice->lastmeter;
            $invoice->must_paid = $invoice->used_water_net * 8; //$invoice->users->usermeter_info->counter_unit;
        } else {
            $invoice->used_water_net = "";
            $invoice->must_paid = "";
            $invoice->currentmeter = "";
        }

        $funcCtrl = new FunctionsController();

        $invoice->invoice_period->th_startdate = $funcCtrl->engDateToThaiDateFormat($invoice->invoice_period->startdate);
        $invoice->invoice_period->th_enddate = $funcCtrl->engDateToThaiDateFormat($invoice->invoice_period->enddate);

        //หาการใช้น้ำ 5 เดือนล่าสุด
        $inv_history = Invoice::where('user_id', $invoice->user_id)
            ->with('invoice_period')
            ->where('inv_period_id', '<', $invoice->inv_period_id)
            ->orderBy('inv_period_id', 'desc')
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
        $oweRes = Invoice::where('user_id', $request->get('user_id'))
            ->where('status', 'owe')
            ->get(['id', 'lastmeter', 'currentmeter']);
        date_default_timezone_set('Asia/Bangkok');
        $invPeriod = InvoicePeriod::where('status', 'active')->get('id');

        date_default_timezone_set('Asia/Bangkok');
        $invPeriod = InvoicePeriod::where('status', 'active')->get('id');

        $invoice = Invoice::where('user_id', $request->get('user_id'))
            ->where('inv_period_id', $invPeriod[0]->id)
            ->update([
                'inv_period_id' => $invPeriod[0]->id,
                'user_id' => $request->get('user_id'),
                'meter_id' => $request->get('user_id'),
                'currentmeter' => $request->get('currentmeter'),
                'recorder_id' => $request->get('recorder_id'),
                'status' => 'invoice',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        $invoice_after_update = Invoice::where('user_id', $request->get('user_id')) //
            ->where('inv_period_id', $invPeriod[0]->id)->get([
            'id', 'inv_period_id', 'user_id', 'lastmeter', 'currentmeter', 'status', 'updated_at',
        ]);

        if ($invoice == 1) {
            $fn = new FunctionsController;
            $invoice_after_update[0]->owe_count = 0;
            $invoice_after_update[0]->owe_sum = 0;
            $date = explode(" ", $invoice_after_update[0]->updated_at);
            $invoice_after_update[0]->record_date = $fn->engDateToThaiDateFormat($date[0]);
            $code = "200";
            $oweRes = Invoice::where('user_id', $request->get('user_id'))
                ->where('status', 'owe')
                ->get(['id', 'lastmeter', 'currentmeter']);
            if (collect($oweRes)->count() > 0) {
                $invoice_after_update[0]->owe_count = collect($oweRes)->count();
                $sum = 0;
                foreach ($oweRes as $oweR) {
                    $sum += $oweR->currentmeter - $oweR->lastmeter == 0 ? 10 : ($oweR->currentmeter - $oweR->lastmeter) * 8;
                }
                $invoice_after_update[0]->owe_sum = $sum;
            }
        } else {
            $invoice_after_update[0]->owe_count = 0;
            $invoice_after_update[0]->owe_sum = 0;
            $code = "204";
        }
        return response()->json(["res" => $invoice_after_update, "status" => $code]);
    }

    // public function test2()
    // {
    //     return $oweRes = Invoice::where('user_id', 565)
    //         ->where('status', 'owe')
    //         ->get(['id', 'lastmeter', 'currentmeter']);
    //     // if (collect($oweRes)->count() > 0) {
    //     //     $invoice->owe_count = collect($oweRes)->count();
    //     //     $sum = 0;
    //     //     foreach ($oweRes as $oweR) {
    //     //         $sum += $oweR->currentmeter - $oweR->lastmeter == 0 ? 10 : ($oweR->currentmeter - $oweR->lastmeter) * 8;
    //     //     }
    //     //     $invoice->owe_sum = $sum;
    //     // }

    //     date_default_timezone_set('Asia/Bangkok');
    //     $invPeriod = InvoicePeriod::where('status', 'active')->get('id');
    //     $findRowDuplicate = Invoice::where('inv_period_id', $request->get('inv_period_id'))
    //         ->where('meter_id', $request->get('meter_id'))->get();

    //     if (collect($findRowDuplicate)->count() > 0) {
    //         $invoice->owe_count = 0;
    //         $invoice->owe_sum = 0;
    //         $code = 204;
    //         return response()->json(['res' => $invoice, 'status' => $code]);
    //     }
    //     $invoice = new Invoice();
    //     $invoice->inv_period_id = $invPeriod[0]->id;
    //     $invoice->user_id = $request->get('user_id');
    //     $invoice->meter_id = $request->get('meter_id');
    //     $invoice->lastmeter = $request->get('lastmeter');
    //     $invoice->currentmeter = $request->get('currentmeter');
    //     $invoice->recorder_id = $request->get('recorder_id');
    //     $invoice->status = 'invoice';
    //     $invoice->created_at = date('Y-m-d H:i:s');
    //     $invoice->updated_at = date('Y-m-d H:i:s');
    //     if ($invoice->save()) {
    //         $invoice->owe_count = 0;
    //         $invoice->owe_sum = 0;
    //         $code = 200;
    //         $oweRes = Invoice::where('meter_id', $request->get('meter_id'))
    //             ->where('status', 'owe')
    //             ->get(['id', 'lastmeter', 'currentmeter']);
    //         if (collect($oweRes)->count() > 0) {
    //             $invoice->owe_count = collect($oweRes)->count();
    //             $sum = 0;
    //             foreach ($oweRes as $oweR) {
    //                 $sum += $oweR->currentmeter - $oweR->lastmeter == 0 ? 10 : ($oweR->currentmeter - $oweR->lastmeter) * 8;
    //             }
    //             $invoice->owe_sum = $sum;
    //         }
    //     } else {
    //         $invoice->owe_count = 0;
    //         $invoice->owe_sum = 0;
    //         $code = 204;
    //     }
    //     return response()->json(['res' => $invoice, 'status' => $code]);
    // }

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
            $sumLastmeter = $q->sum('lastmeter');
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
            ->join('invoice as iv', 'iv.user_id', '=', 'umf.user_id')
        // ->join('invoice as iv', 'iv.meter_id', '=', 'umf.id')
            ->join('user_profile as upf', 'upf.user_id', '=', 'umf.user_id')
            ->join('zone as z', 'z.id', '=', 'umf.undertake_zone_id')
            ->join('subzone as sz', 'sz.id', '=', 'umf.undertake_subzone_id')
            ->where('iv.inv_period_id', '=', $presentInvoicePeriod->id)
            ->where('iv.status', '=', 'invoice')
            ->where('umf.undertake_subzone_id', '=', $subzone_id)
            ->where('umf.status', '=', 'active')
            ->where('umf.deleted', '=', 0);

        $invoice = $sql->select(
            'umf.user_id', 'umf.meternumber', 'umf.undertake_subzone_id', 'umf.undertake_zone_id',
            'upf.name', 'upf.address', 'iv.lastmeter', 'iv.currentmeter', 'iv.printed_time', 'iv.id',
            'upf.zone_id as user_zone_id', 'iv.comment', 'iv.status',
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
                'umf.user_id', 'umf.meternumber', 'z.zone_name as undertake_zone', 'z.id as undertake_zone_id',
                'sz.subzone_name as undertake_subzone', 'sz.id as undertake_subzone_id',
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
            ->join('invoice as iv', 'iv.user_id', '=', 'umf.user_id')
            ->join('user_profile as upf', 'upf.user_id', '=', 'umf.user_id')
            ->join('zone as z', 'z.id', '=', 'umf.undertake_zone_id')
            ->join('subzone as sz', 'sz.id', '=', 'umf.undertake_subzone_id')
            ->where('iv.inv_period_id', '=', $presentInvoicePeriod->id)
            ->where('iv.status', '=', 'invoice')
            ->where('umf.undertake_subzone_id', '=', $subzone_id);

        $invoice = $sql->get([
            'umf.user_id', 'umf.meternumber', 'umf.undertake_subzone_id', 'umf.undertake_zone_id',
            'upf.name', 'upf.address', 'iv.lastmeter', 'iv.currentmeter', 'iv.printed_time', 'iv.id',
            'upf.zone_id as user_zone_id',
            DB::raw('iv.currentmeter - iv.lastmeter as meter_net'),
            DB::raw('(iv.currentmeter - iv.lastmeter)*8 as total'),
        ]);
        $zoneInfo = $sql->select([
            'umf.user_id', 'umf.meternumber', 'z.zone_name as undertake_zone', 'z.id as undertake_zone_id',
            'sz.subzone_name as undertake_subzone', 'sz.id as undertake_subzone_id',
        ])->limit(1)->get();

        foreach ($invoice as $iv) {
            $funcCtrl = new FunctionsController();
            $iv->user_id_string = intval($funcCtrl->createInvoiceNumberString($iv->user_id));
        }

        return response()->json([
            'presentInvoicePeriod' => ['inv_period_name' => $presentInvoicePeriod->inv_period_name, 'budgetyear' => $presentInvoicePeriod->budgetyear->budgetyear],
            'zoneInfo' => [
                'undertake_zone' => $zoneInfo[0]->undertake_zone, 'undertake_subzone' => $zoneInfo[0]->undertake_subzone,
                'undertake_zone_id' => $zoneInfo[0]->undertake_zone_id, 'undertake_subzone_id' => $zoneInfo[0]->undertake_subzone_id,
            ],
            'invoicedlists' => $invoice,
            'subzone_id' => $subzone_id,
        ]);

    }

}
