<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\BudgetYear;
use App\Models\Invoice;
use App\Models\InvoicePeriod;
use App\Models\Subzone;
use App\Models\UserMerterInfo;
use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Api\InvoiceController;

class ReportsController extends Controller
{
    private $functionsCtlr;

    public function invoicereport(Request $request)
    {
        // dd($request->get('invperiodstart'));
        $startDate = $this->functionsCtlr->thaiDateToEngDateFormat("01/08/2561"); //$request->get('invperiodstart')
        $endDate = $this->functionsCtlr->thaiDateToEngDateFormat("9/08/2564"); //$request->get('invperiodend')

        if ($request->get('invoicetype') != 'all') {
            $invoice = Invoice::whereBetween('created_at', [$startDate . '%', $endDate . '%'])
                ->with('invoice_period', 'users', 'users.user_profile', 'recorder')
                ->where('status', $request->get('invoicetype'))
                ->get();
        } else {
            $invoice = Invoice::whereBetween('created_at', [$startDate . '%', $endDate . '%'])
                ->with('invoice_period', 'users', 'users.user_profile', 'recorder')
                ->get();
        }
        return response()->json($invoice);
    }

    public function get_used_water()
    {
        $year = date('Y') - 1;
        $invoice = Invoice::where('created_at', "like", $year . "%")->get();

        $used_water = DB::table('invoice')
            ->select(DB::raw('sum(currentmeter) as meter_sum, inv_period_id'))
            ->where('status', '<>', 'www')
            ->groupBy('inv_period_id')
            ->get();
        $labels = [];
        $values = [];
        $datas = [];
        foreach ($used_water as $uw) {
            $inv_periods[$uw->inv_period_id] = [];
            $inv_name = InvoicePeriod::where('id', $uw->inv_period_id)->first();
            $uw->inv_period_name = $inv_name->invfunction_period_name;

            $paid = $invoice->filter(function ($val) {
                return $val->status = 'paid';
            });
            $owe = $invoice->filter(function ($val) {
                return $val->status = 'owe';
            });

            array_push($labels, $inv_name->inv_period_name);
            array_push($values, $uw->meter_sum);
        }
        $used_water['labels'] = $labels;
        $used_water['values'] = $values;

        return response()->json($used_water);
    }

    public function users($zone_id = 'all', $subzone_id = 'all')
    {
        $fnCtrl = new FunctionsController();
        $active_users = UserMerterInfo::where('status', 'active');
        if ($zone_id != 'all') {
            $active_users = $active_users->where('undertake_zone_id', $zone_id);
            if ($subzone_id != 'all') {
                $active_users = $active_users->where('undertake_subzone_id', $subzone_id);
            }
        }

        return $active_users->get();

        $array = [];
        $c = 1;
        foreach ($active_users as $user) {
            array_push($array,
                [
                    $fnCtrl->createInvoiceNumberString($user->id),
                    $user->userMeterInfos->meternumber,
                    $user->name,
                    $user->address,
                    $user->zone->zone_name,
                    $user->subzone->subzone_name,
                    $user->phone,
                    '',

                ]);
        }

        return response()->json($array);
    }

    public function owe(REQUEST $request)
    {
        date_default_timezone_set('Asia/Bangkok');

        // $subzon_1 = json_decode($this->meter_record_history(2, 1)->content(), true);
        // return $this->reserve_meter_status($subzon_1, 'invoice');

        $fnCtrl = new FunctionsController();
        //หาจาก  usermeterinfos[undertake_zone_id  undertake_subzone_id] -> invoice table
        $startDate = $request->get('invperiodstart') == 'all' ? 'all' : $fnCtrl->thaiDateToEngDateFormat($request->get('invperiodstart'));
        $endDate = $request->get('invperiodend') == 'all' ? 'all' : $fnCtrl->thaiDateToEngDateFormat($request->get('invperiodend'));
        $zone_id = $request->get('zone_id');
        $subzone_id = $request->get('subzone_id');

        //query string หา owe ทั้งหมด
        $owes = DB::table('invoice as iv')
            ->join('user_profile as uf', 'uf.user_id', '=', 'iv.user_id')
        // ->join('user_meter_infos as umf', 'umf.id', '=', 'iv.meter_id' )
            ->join('user_meter_infos as umf', 'umf.user_id', '=', 'iv.user_id')
            ->join('zone', 'zone.id', '=', 'umf.undertake_zone_id')
            ->join('subzone', 'subzone.id', '=', 'umf.undertake_subzone_id')
            ->join('invoice_period as ivp', 'ivp.id', '=', 'iv.inv_period_id')
            ->select(
                'iv.meter_id', 'iv.user_id', 'iv.currentmeter', 'iv.lastmeter',
                DB::raw('(iv.currentmeter - iv.lastmeter) as water_used'), 'iv.status as iv_status',
                DB::raw('(iv.currentmeter - iv.lastmeter)*8 as mustpaid'),
                'uf.name', 'uf.address',
                'zone.zone_name',
                'subzone.subzone_name', 'umf.undertake_subzone_id',
                'umf.meternumber',
                'ivp.inv_period_name',
            );
        $owes = $owes->where('iv.status', '=', 'owe')
            ->orWhere('iv.status', '=', 'invoice');
        //ถ้า$startDate และ $endDate เป็นช่วงเวลาที่เลือก
        if ($startDate != 'all' && $endDate != 'all') {

            $owes = $owes->where('iv.updated_at', '>=', $startDate . ' 00:00:00')
                ->where('iv.updated_at', '<=', $endDate . ' 23:59:59');
        }
        // ถ้า $zone_id และ $subzone_id ไม่เท่ากับ all
        if ($zone_id != 'all' && $subzone_id != 'all') {
            $owes = $owes->where('umf.undertake_subzone_id', $subzone_id)->get();
            $owes2 = collect($owes)->filter(function ($val) use ($subzone_id) {
                return $subzone_id == $val->undertake_subzone_id;
            });
            $owes = collect($owes2)->values();
        } else {
            $owes = $owes->get();
        }

        // ->get();

        return response()->json($owes);
    }

    public function payment_summary()
    {
        $inv = DB::table('invoice as iv')
            ->select('iv.inv_period_id',
                DB::raw('(iv.currentmeter - iv.lastmeter)*8 as paid'),
                'undertake_subzone_id'
            )
            ->join('invoice_period as ivp', 'ivp.id', '=', 'iv.inv_period_id')
        // ->join('user_meter_infos as umf', 'umf.id', '=', 'iv.meter_id' )
            ->join('user_meter_infos as umf', 'umf.user_id', '=', 'iv.user_id')

            ->where('iv.status', '=', 'paid')
            ->get();
        $invGrouped = collect($inv)->groupBy('inv_period_id');
        $invMap = collect($invGrouped)->map(function ($item, $key) {
            $inv_period = InvoicePeriod::where('id', $item[0]->inv_period_id)->get(['inv_period_name']);
            $inv_period_arr = \explode('-', $inv_period[0]['inv_period_name']);
            $th_year = "25" . $inv_period_arr[1];
            return ['inv_period_id' => $key,
                'inv_period_name' => $inv_period[0]->inv_period_name,
                'inv_period_name_th' => FunctionsController::fullThaiMonth($inv_period_arr[0]) . " " . $th_year,
                'sum' => collect($item)->sum('paid')];
        });
        return response()->json($invMap);
    }

    public function payment(REQUEST $request)
    {
        date_default_timezone_set('Asia/Bangkok');
        $fnCtrl = new FunctionsController();
        //หาจาก  usermeterinfos[undertake_zone_id  undertake_subzone_id] -> invoice table
        $inv_period_id = $request->get('inv_period_id');
        $zone_id = $request->get('zone_id');
        $subzone_id = $request->get('subzone_id');
        $fromdate = $request->get('fromdate');
        $todate = $request->get('todate');
        $cashier_id = $request->get('cashier_id');
        //query string หา owe ทั้งหมด
        $paids = DB::table('invoice as iv')
            ->join('user_profile as uf', 'uf.user_id', '=', 'iv.user_id')
        // ->join('user_meter_infos as umf', 'umf.id', '=', 'iv.meter_id' )
            ->join('user_meter_infos as umf', 'umf.user_id', '=', 'iv.user_id')

            ->join('zone', 'zone.id', '=', 'umf.undertake_zone_id')
            ->join('subzone', 'subzone.id', '=', 'umf.undertake_subzone_id')
            ->join('invoice_period as ivp', 'ivp.id', '=', 'iv.inv_period_id')
            ->join('accounting as acc', 'acc.id', '=', 'iv.receipt_id')
            ->select(
                // 'iv.*',
                DB::raw('(iv.currentmeter - iv.lastmeter) as water_used'), 'iv.status as iv_status',
                DB::raw('(iv.currentmeter - iv.lastmeter)*8 as mustpaid'),
                'uf.name', 'uf.address',
                'zone.zone_name',
                'subzone.subzone_name', 'umf.undertake_subzone_id',
                'umf.meternumber',
                'ivp.inv_period_name',
                'acc.id as acc_id',
                'acc.total as acc_total',
                'acc.cashier', 'acc.updated_at as acc_updated_at'
            )
            ->where('iv.deleted', '<>', 1)
            ->where('iv.receipt_id', '<>', 0);

        // ถ้า $zone_id และ $subzone_id ไม่เท่ากับ all
        if ($zone_id != 'all' && $subzone_id != 'all') {
            $paids = $paids->where('umf.undertake_subzone_id', $subzone_id);
        }

        $paids = $paids->where('iv.status', '=', 'paid')
        // ->where('iv.inv_period_id', '=', $inv_period_id)
            ->where('acc.updated_at', '>=', $fromdate . ' 00:00:00')
            ->where('acc.updated_at', '<=', $todate . ' 23:59:59')
            ->get();

        if ($cashier_id != 'all') {
            //filter เอาผู้รับเงินที่ต้องการ
            $paidFilter = collect($paids)->filter(function ($v) use ($cashier_id) {
                return $v->cashier == $cashier_id;
            });
            return \response()->json($paidFilter);
        }

        return response()->json($paids);
    }

    public function daily_receipt($date, $month, $yearTh)
    {
        $funcCtrl = new FunctionsController();
        $year = $yearTh - 543;
        //หา รายรับของวันทีต้องการ
        $dateSelected = $year . "-" . $month . "-" . $date;

        $receipts = Account::where('updated_at', 'LIKE', $dateSelected . "%")
            ->with(['invoice', 'invoice.invoice_period',
                'invoice.user_profile',
                'invoice.usermeterinfos',
                'invoice.user_profile.zone',
                'invoice.usermeterinfos.subzone',
            ])->get();

        foreach ($receipts as $rc) {
            $date = \explode(" ", $rc->updated_at);
            $rc['dateTh'] = $funcCtrl->engDateToThaiDateFormat($date[0]);
        }

        return response()->json($receipts);
    }

    public function meter_record_history($budgetyear = 'now', $zone_id = 'all')
    {
        $zones = Zone::all();
        $this_budgetyear = $budgetyear;
        if ($this_budgetyear == 'now') {
            $currentBudgetYear = BudgetYear::where('status', 'active')->get('id')->first();
            $this_budgetyear = $currentBudgetYear->id;
        }

        $inv_periods = InvoicePeriod::where('deleted', '<>', 1)
            ->where('budgetyear_id', $this_budgetyear)
            ->orderBy('id', 'asc')->get('id');

        $inv_period_id_first = collect($inv_periods)->first();

        $inv_period_id_last = collect($inv_periods)->last();
        $inv_periodsCount = collect($inv_periods)->count();

        $membersEloquentSql = UserMerterInfo::
            with([
            'invoice' => function ($query) use ($inv_period_id_first, $inv_period_id_last) {
                $query->select('inv_period_id', 'currentmeter', 'lastmeter', 'user_id', 'status', 'updated_at',
                    DB::Raw('currentmeter - lastmeter as water_used'))
                    ->where('inv_period_id', '>=', $inv_period_id_first->id)
                    ->where('inv_period_id', '<=', $inv_period_id_last->id)
                    ->where('deleted', 0);
            },
            'user_profile' => function ($query) {
                $query->select('name', 'zone_id', 'address', 'user_id');
            },
            'invoice.invoice_period' => function ($query) {
                $query->select('inv_period_name', 'id');
            },
            'subzone' => function ($query) {
                $query->select('subzone_name', 'id');
            },
            'zone' => function ($query) {
                $query->select('zone_name', 'id');
            },
        ])
            ->whereIn('status', ['active', 'cutmeter'])
            ->where('deleted', 0);
        if ($zone_id != 'all') {
            $membersEloquentSql = $membersEloquentSql->where('undertake_zone_id', $zone_id);
        }

        $membersEloquent = $membersEloquentSql->get([
            'meternumber', 'undertake_zone_id', 'undertake_subzone_id', 'user_id', 'owe_count',
        ]);
        return response()->json($membersEloquent);

    }

    public function meter_record_history_count($budgetyear = 'now', $zone_id = 'all')
    {

        $this_budgetyear = $budgetyear;
        if ($this_budgetyear == 'now') {
            $currentBudgetYear = BudgetYear::
            where('status', 'active')->get('id')->first();
            $this_budgetyear = $currentBudgetYear->id;
        }

        $inv_periods_eloq = InvoicePeriod::where('deleted', '<>', 1)
            ->where('budgetyear_id', $this_budgetyear)
            ->orderBy('id', 'asc')->get('id');

        $id_inv_periods_array = collect($inv_periods_eloq)->pluck('id');


        $membersEloquentSql = UserMerterInfo::
            with([
            'invoice' => function ($query) use ($id_inv_periods_array) {
                $query->select('inv_period_id', 'currentmeter', 'lastmeter', 'user_id', 'status', 'updated_at',
                    DB::Raw('currentmeter - lastmeter as water_used'))
                    ->whereIn('inv_period_id', $id_inv_periods_array);
                    // ->where('deleted', 0);
            },
            'user_profile' => function ($query) {
                $query->select('name', 'zone_id', 'address', 'user_id');
            },
            'invoice.invoice_period' => function ($query) {
                $query->select('inv_period_name', 'id');
            },
            'subzone' => function ($query) {
                $query->select('subzone_name', 'id');
            },
            'zone' => function ($query) {
                $query->select('zone_name', 'id');
            },
        ]);
            // ->whereIn('status', ['active', 'cutmeter'])
            // ->where('deleted', 0);
        if ($zone_id != 'all') {
            $membersEloquentSql = $membersEloquentSql->where('undertake_zone_id', $zone_id);
        }

        $membersEloquent = $membersEloquentSql->get([
            'meternumber',
            'undertake_zone_id',
            'undertake_subzone_id',
            'user_id', 'owe_count',
        ]);

        return response()->json($membersEloquent);

    }

    public function meter_record_history_count2($budgetyear = 'now', $zone_id = 'all')
    {
        $this_budgetyear = $budgetyear;
        if ($this_budgetyear == 'now') {
            $currentBudgetYear = BudgetYear::
            where('status', 'active')->get('id')->first();
            $this_budgetyear = $currentBudgetYear->id;
        }

        $inv_periods_eloq = InvoicePeriod::where('deleted', '<>', 1)
            ->where('budgetyear_id', $this_budgetyear)
            ->orderBy('id', 'asc')->get('id');

        $id_inv_periods_array = collect($inv_periods_eloq)->pluck('id');

        $membersEloquent = UserMerterInfo::
            with([
            'invoice' => function ($query) use ($id_inv_periods_array) {
                $query->select('inv_period_id', 'user_id', 'status',
                    DB::Raw('currentmeter - lastmeter as water_used'))
                    ->whereIn('inv_period_id', $id_inv_periods_array);
            },
        ])
        // ->limit(1)
        ->get([
            'undertake_subzone_id',
            'user_id',
        ]);

        return response()->json($membersEloquent);

    }

    public function water_used_count($membersEloquent)
    {
        return collect($membersEloquent)->reduce(function ($carry, $item) {
            $result = 0;
            if (collect($item['invoice'])->count() > 0) {
                $result = collect($item['invoice'])->reduce(function ($c, $v) {
                    $res = $v['currentmeter'] - $v['lastmeter'];
                    return $c + $res;
                });
            }
            return $carry + $result;
        });
    }

    public function water_used_count2($membersEloquent)
    {
        return collect($membersEloquent)->reduce(function ($carry, $item) {
            $result = 0;
            if (collect($item['invoice'])->count() > 0) {
                $result = collect($item['invoice'])->reduce(function ($c, $v) {
                    $res = $v['currentmeter'] - $v['lastmeter'];
                    return $c + $res;
                });
            }
            return $carry + $result;
        });
    }

    public function water_used_status($membersEloquent, $status)
    {
        return collect($membersEloquent)->reduce(function ($carry, $item) use ($status) {
            if($status == "all"){
                $item_filter = $item['invoice'];
            }else{
                $item_filter = collect($item['invoice'])->filter(function($v) use ($status){
                    return $v['status'] == $status;
                });
            }

            $result = collect($item_filter)->sum('water_used');
            return $carry + $result;
        });
    }


    public function reserve_meter_status($membersEloquent, $status)
    {
        return collect($membersEloquent)->reduce(function ($carry, $item) use ($status) {
            $item_filter = collect($item['invoice'])->filter(function($v) use ($status){
                return $v['status'] == $status && $v['water_used'] == 0;
            });
            $result = collect($item_filter)->count();

            return $carry + $result;
        });
    }

    public function meter_record_history2($budgetyear = 'now', $zone_id = 'all')
    {
        $zones = Zone::all();
        $this_budgetyear = $budgetyear;
        if ($this_budgetyear == 'now') {
            $currentBudgetYear = BudgetYear::where('status', 'active')->get('id')->first();
            $this_budgetyear = $currentBudgetYear->id;
        }

        $inv_periods = InvoicePeriod::where('deleted', '<>', 1)
            ->where('budgetyear_id', $this_budgetyear)
            ->orderBy('id', 'asc')->get('id');

        $inv_period_id_first = collect($inv_periods)->first();
        $inv_period_id_first = collect($inv_periods)->first();
        $inv_period_id_last = collect($inv_periods)->last();
        $inv_periodsCount = collect($inv_periods)->count();

        $membersEloquentSql = UserMerterInfo::
            with([
            'invoice' => function ($query) use ($inv_period_id_first, $inv_period_id_last) {
                $query->select('inv_period_id', 'currentmeter', 'user_id')
                    ->where('inv_period_id', '>=', $inv_period_id_first->id)
                    ->where('inv_period_id', '<=', $inv_period_id_last->id);
            },
            'user_profile' => function ($query) {
                $query->select('name', 'zone_id', 'address', 'user_id');
            },
            'invoice.invoice_period' => function ($query) {
                $query->select('inv_period_name', 'id');
            },
            'subzone' => function ($query) {
                $query->select('subzone_name', 'id');
            },
            'zone' => function ($query) {
                $query->select('zone_name', 'id');
            },
        ])->where('status', 'active');
        if ($zone_id != 'all') {
            $membersEloquentSql = $membersEloquentSql->where('undertake_zone_id', $zone_id);
        }

        $membersEloquent = $membersEloquentSql->get([
            'meternumber', 'undertake_zone_id', 'undertake_subzone_id', 'user_id',
        ]);

        $text = '
        <div class="table-responsive">
                <table class="table table-striped" id="oweTable">
                <thead>
                    <tr>
                        <th>ชื่อ-สกุล</th>
                        <th>บ้านเลขที่</th>
                        <th style="width:100px !important">หมู่ที่</th>
                        <th>เส้นทาง</th>';
        foreach ($membersEloquent[0]->invoice as $item) {
            $text .= '<th>' . $item->invoice_period->inv_period_name . '</th>';
        }
        $text .= '</tr>
                    </thead>
                    <tbody>';

        foreach ($membersEloquent as $member) {
            $text .= ' <tr>
                    <td class="text-left">' . $member->user_profile->name . '</td>
                    <td class="text-left">' . $member->user_profile->address . '</td>
                    <td class="text-left">' . $member->zone->zone_name . '</td>
                    <td class="text-left">' . $member->subzone->subzone_name . '</td>';
            foreach ($member->invoice as $item) {
                $text .= '<td class="text-right">' . $item->currentmeter . '</td>';
            }

        }
        $text .= '</tr>';
        $text .= '</tbody>
        </table></div>';
        return response()->json($text);

    }

    public function water_used(Request $request)
    {
        $zone_and_subzone_selected_text = '';
        if (collect($request)->isEmpty()) {
            // เริ่มต้นหาปีงบประมาณปัจจุบัน และ รอบบิล

            $selected_budgetYear = BudgetYear::where('id', 1)
                ->with('invoicePeriod')
                ->first();
            $a = [
                'zone_id' => 'all',
                'subzone_id' => 'all',
            ];
            $request->merge($a);
        } else {
            $selected_budgetYear = BudgetYear::where('id', $request->get('budgetyear_id'))
                ->get()->first();
        }
        $invP_of_selected_budgetyear = InvoicePeriod::where('budgetyear_id', $selected_budgetYear->id)
            ->where('status', '<>', 'deleted')
            ->get(['id']);
        $invPeriod_selected_buggetYear_array = collect($invP_of_selected_budgetyear)->pluck('id');

        $waterUsedSql = DB::table('user_meter_infos as umf')
            ->join('invoice as iv', 'iv.user_id', '=', 'umf.user_id')
            ->join('invoice_period as ivp', 'ivp.id', '=', 'iv.inv_period_id')
            ->join('subzone as sz', 'sz.id', '=', 'umf.undertake_subzone_id')
            ->join('zone as z', 'z.id', '=', 'umf.undertake_zone_id')
            ->where('umf.status', '<>', 'inactive')
            ->whereIn('iv.inv_period_id', $invPeriod_selected_buggetYear_array)
            ->select(
                DB::raw('(iv.currentmeter - iv.lastmeter) as waterUsed'),
                'iv.inv_period_id',
                'umf.undertake_subzone_id',
                'umf.undertake_zone_id',
            );

        if ($request->get('zone_id') != 'all') {
            $zone = Zone::where('id', $request->get('zone_id'))->get('zone_name');
            $zone_and_subzone_selected_text .= ' ' . $zone[0]->zone_name;
            if ($request->get('subzone_id') != 'all') {
                $waterUsedSql = $waterUsedSql->where('umf.undertake_subzone_id', '=', $request->get('zone_id'));
                $subzone = subZone::where('id', $request->get('subzone_id'))->get('subzone_name');
                $zone_and_subzone_selected_text .= ' เส้นทางจัดเก็บ ' . $subzone[0]->subzone_name;
            } else {
                $waterUsedSql = $waterUsedSql->where('umf.undertake_zone_id', '=', $request->get('zone_id'));
            }
        }
        $waterUsed = $waterUsedSql
            ->orderBy('zone_id', 'asc')
            ->orderBy('umf.undertake_subzone_id', 'asc')
            ->get();
        $waterUsedGroupedBySubzone = collect($waterUsed)->groupBy('undertake_subzone_id')->values();
        $datas = collect([]);

        foreach ($waterUsedGroupedBySubzone as $key => $subzone) {
            //ทำการหาผลรวมการใช้น้ำของแต่ละรอบบิลของแต่ละ subzone
            //แล้วทำการเก็บไว้ใน array datas
            $invP_in_subzone_info_arr = collect([]);
            foreach ($invPeriod_selected_buggetYear_array as $invP_id) {
                //วนลูป รอบบิลของปีงบประมาณที่เลือก เพื่อทำการ filter หา ผลรวม waterUsed
                $filtered = collect($subzone)->filter(function ($item) use ($invP_id) {
                    return $item->inv_period_id == $invP_id;
                })->sum('waterUsed');
                $find_invP_name = InvoicePeriod::where('id', $invP_id)->get(['inv_period_name']);
                $invP_in_subzone_info_arr->push([
                    'inv_period_id' => $invP_id,
                    'inv_period_name' => $find_invP_name[0]->inv_period_name,
                    'water_used_sum' => $filtered,
                ]);

            }
            $find_subzone_name = Subzone::where('id', $subzone[0]->undertake_subzone_id)->get('subzone_name');
            $datas->push([
                'subzone_id' => $subzone[0]->undertake_subzone_id,
                'zone_id' => $subzone[0]->undertake_zone_id,
                'subzone_name' => $find_subzone_name[0]->subzone_name,
                'values' => $invP_in_subzone_info_arr,
                'total' => collect($invP_in_subzone_info_arr)->sum('water_used_sum'),
            ]);

        }

        $budgetyears = BudgetYear::where('status', '<>', 'deleted')->get(['id', 'budgetyear']);
        $zones = Zone::where('deleted', 0)->get(['id', 'zone_name']);

        return \response()->json($datas);
        // return view('reports.water_used',compact( 'datas', 'zones', 'budgetyears', 'zone_and_subzone_selected_text', 'selected_budgetYear'));
    }

    public function store(Request $request)
    {
        date_default_timezone_set('Asia/Bangkok');
        $inv_idArr = $request->get('value')['inv_id'];
        $receipt = new Account();
        $receipt->total = $request->get('value')['mustpaid'];
        $receipt->cashier = Auth::id();
        $receipt->created_at = date('Y-m-d H:i:s');
        $receipt->updated_at = date('Y-m-d H:i:s');
        $receipt->save();

        //ทำการupdate invoice
        $apiInvCtrl = new InvoiceController();
        foreach ($inv_idArr as $key => $inv_id) {
            $update = Invoice::where('id', $inv_id)->update([
                'receipt_id' => $receipt->id,
                'status' => 'paid',
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }
        return $receipt->id;

    }

}
