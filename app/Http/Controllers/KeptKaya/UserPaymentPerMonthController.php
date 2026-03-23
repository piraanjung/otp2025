<?php

namespace App\Http\Controllers;

use App\Models\KPBudgetYear;
use App\Models\Admin\Subzone;
use App\Models\Admin\Zone;
use App\Models\User;
use App\Models\UserMeterInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Api\FunctionsController;
use App\Models\Admin\BudgetYear;
use App\Models\UserKayaInfos;
use App\Models\UserPaymentPerMonth;
use App\Models\UserPaymentPeryear;
use DataTables;

class UserPaymentPerMonthController extends Controller
{
    public function index()
    {
        $subzones  = collect(Subzone::all())->sortBy('zone_id');
        $page = 'index';
        $paid_per_year_members = kpuser::where([
            'status' => 'active',
            'trashbankmember' => "0"
        ])->get();
        return view('keptkaya.user_payment_per_month.index', compact('subzones', 'page', 'paid_per_year_members'));
    }

    public function index2(Request $request)
    {
        $current_budgetyear = BudgetYear::where('status', 'active')->first();
        $budgetyear_id      = $current_budgetyear->id;
        $owe_users = DB::table('user_kaya_infos as umf')
            ->join('users as u', 'umf.user_id', '=', 'u.id')
            ->join('usergroup as ugrp',  'umf.usergroup_id', '=', 'ugrp.id')
            ->join('zones as z',  'u.zone_id', '=', 'z.id')
            // ->join('user_payment_per_year as ppy', function($join) use ($budgetyear_id){
            //     $join->on('umf.user_id', '=', 'ppy.user_id_fk')
            //         ->where('budgetyear_id_fk', '=', $budgetyear_id);
            // })
            ->where('umf.owe_count', '>', 0)
            ->select(
                'umf.id',
                DB::raw('CONCAT(u.prefix,u.firstname," ",u.lastname) as name'),
                'umf.user_id as trash_code',
                // 'ppy.bin_quantity',
                'usergroup_name',
                'u.address',
                'z.zone_name as zonename',
                'umf.owe_count',
                // 'umf.comment'
            )
            ->get();


        if ($request->ajax()) {
            // $data = $owe_users;
            return Datatables::of($owe_users)
                ->addIndexColumn()
                ->make(true);
        }
    }

    public function store(Request $request)
    {
        // filter row ที่ selected แล้วทำการ group แยกเป็นปีงบประมาณ
        $u_payment_per_month_grouped = collect($request->get('payments'))->filter(function ($v) {
            return isset($v['on']) || isset($v['past']);
        })->groupBy('id');

        $past_status_count = collect($request->get('payments'))->filter(function ($v) {
            return isset($v['past']);
        })->count();
        $usermeterinfos  = (new UserKayaInfos())->on(session('auth_infos')['databasename'])
            ->where('user_id', $request->get('user_id'))->first();

        //เตรียม array data เพื่อส่งไป ปริ้นใบเสร็จรับเงิน
        $datas = [
            'usermeterinfos'    => $usermeterinfos,
            'update_date'       => date('Y-m-d'),
            'user_id'           => FunctionsController::createInvoiceNumberString($request->get('user_id')),
            'paid_budgetyear'   => []
        ];
        // วนลูปแยก
        $paid_sum       = 0;
        $paid_count_sum = 0;
        foreach ($u_payment_per_month_grouped as $paymentPerMonthIDkey => $u_payment_per_month) {

            $userPaymentPerMonthQuery = UserPaymentPerMonth::where('id', $paymentPerMonthIDkey)->get(['json', 'init_status_count', 'bin_no'])[0];
            $userPaymentPerMonthJson = json_decode($userPaymentPerMonthQuery->json, true);

            //1. update json ,paid_status_count, init_status_count
            foreach ($u_payment_per_month as $month) {
                $monthName = $month['month'];
                $mothSearch = collect($userPaymentPerMonthJson)->search(function ($val) use ($monthName) {
                    return $val['month'] == $monthName;
                });
                $userPaymentPerMonthJson[$mothSearch] = [
                    'month'         => $monthName,
                    'status'        => isset($month['past']) ? "past" : 'paid',
                    'updated_at'    => date('Y-m-d H:i:s')
                ];
            }
            UserPaymentPerMonth::where('id', $paymentPerMonthIDkey)->update([
                'json'              => json_encode($userPaymentPerMonthJson),
                'paid_status_count' => collect($u_payment_per_month)->count(),
                'init_status_count' => DB::raw('init_status_count - ' . collect($u_payment_per_month)->count()),
                'status'            => $userPaymentPerMonthQuery->init_status_count - collect($u_payment_per_month)->count() <= 0 ? 'paid' : 'init',
                'updated_at'        => date('Y-m-d H:i:s')

            ]);

            //2. sum จำนวนเงินที่ชำระ แล้ว update user_payment_per_year
            $paid_sum  = $u_payment_per_month[0]['rate_payment_per_month'] * collect($u_payment_per_month)->count();

            UserPaymentPeryear::where('id', $u_payment_per_month[0]['user_payment_per_year_id_fk'])->update([
                'paid_total_payment_per_year'   => DB::raw('paid_total_payment_per_year + ' . $paid_sum),
                'paid_in_full'                  => DB::raw('total_payment_per_year	 == paid_total_payment_per_year + ' . $paid_sum  ? '1' : '0'),
                'updated_at'                    => date('Y-m-d H:i:s')
            ]);

            //3. sumจำนวน paid แล้ว update user_meter_info ->owe_count
            $paid_count_sum += collect($u_payment_per_month)->count();

            //4. add to array for print ส่งค่าไป print ใบเสร็จรับเงิน
            $budgetyear_id_key = UserPaymentPeryear::where('id', $u_payment_per_month[0]['user_payment_per_year_id_fk'])
                ->get('budgetyear_id_fk')[0];
            $u_payment_per_month_last_index_array = collect($u_payment_per_month)->count() - 1;
            array_push($datas['paid_budgetyear'], [
                'budgetyear'                => KpBudgetYear::where('id', $budgetyear_id_key->budgetyear_id_fk)->get('budgetyear_name'),
                'month_paid_count'          => collect($u_payment_per_month)->count(),
                'bin_no'                    => $userPaymentPerMonthQuery->bin_no,
                'start_month_paid'          => $u_payment_per_month[0]['month'],
                'end_month_paid'            => $u_payment_per_month[$u_payment_per_month_last_index_array]['month'],
                'billcycleOwe'              => $userPaymentPerMonthQuery->init_status_count - collect($u_payment_per_month)->count(),
                'sum_rate_payment_per_month' => $u_payment_per_month[0]['rate_payment_per_month'] * collect($u_payment_per_month)->count(),
                'recorder'                  => User::where('id', Auth::id())->get(['firstname', 'lastname']),
                'rate_payment_per_month'    => $u_payment_per_month[0]['rate_payment_per_month'],

            ]);
        } //foreach

        $paid_count_total = $paid_count_sum;
        (new UserKayaInfos())->on(session('auth_infos')['databasename'])
            ->where('user_id', $request->get('user_id'))->update([
                'owe_count'     => DB::raw('owe_count -' . $paid_count_total),
                'updated_at'    => date('Y-m-d H:i:s')
            ]);

        $from = 'userPaymentPerMonth.store';
        return view('keptkaya.user_payment_per_month.receipt_print', compact('datas', 'from'));
    }

    public function table()
    {
        $budgetyear = KpBudgetYear::where('status', 'active')->get(['id', 'budgetyear_name']);
        $curr_budgetyear_id = $budgetyear[0]->id;
        $paids_per_years = (new UserKayaInfos())->on(session('auth_infos')['databasename'])
            ->where(['status' => 'active'])
            ->with([
                'user_payment_per_year' => function ($query) use ($curr_budgetyear_id) {
                    return $query->select('id', 'user_id_fk', 'budgetyear_id_fk', 'payment_per_year')
                        ->where('budgetyear_id_fk', $curr_budgetyear_id);
                },
                'user_payment_per_year.user_payment_per_month' => function ($query) {
                    return $query->select('id', 'user_payment_per_year_id_fk', 'month', 'rate_payment_per_month', 'status');
                }
            ])
            ->limit(3)
            ->get(['user_id', 'status', 'usergroup_id', 'owe_count', 'trashbank_status']);

        $budgetyears = (new BudgetYear())->on(session('auth_infos')['databasename'])->where('status', 'active')->get(['id', 'budgetyear_name']);
        return view('keptkaya.user_payment_per_month.table', compact('paids_per_years', 'budgetyears', 'budgetyear'));
    }

    public function table_search($budgetyear = 1)
    {
        $budgetyear = (new BudgetYear())->on(session('auth_infos')['databasename'])->where('id', 1)->first();
        $curr_budgetyear_id = $budgetyear->id;
        $paids_per_years = (new UserKayaInfos())->on(session('auth_infos')['databasename'])->where(['status' => 'active'])
            ->with([
                'user_payment_per_year' => function ($query) use ($curr_budgetyear_id) {
                    return $query->select('id', 'user_id_fk', 'budgetyear_id_fk', 'paid_sum_payment_per_year')
                        ->where('budgetyear_id_fk', $curr_budgetyear_id);
                },
                'user_payment_per_year.user_payment_per_month' => function ($query) {
                    return $query->select('id', 'user_payment_per_year_id_fk', 'month', 'rate_payment_per_month', 'status');
                }
            ])
            ->get(['user_id', 'status', 'usergroup_id', 'owe_count', 'trashbank_status']);

        $budgetyears = KpBudgetYear::where('status', 'active')->get(['id', 'budgetyear_name']);
        return view('user_payment_per_month.table', compact('paids_per_years', 'budgetyears', 'budgetyear'));
    }

    public function history(Request $request)
    {
        $inv_by_budgetyear = [];
        $usersQuery = (new UserKayaInfos())->on(session('auth_infos')['databasename'])->where([
            'status'            => 'active',
            'trashbank_status'  => 0,
        ])
            ->with([
                'user_payment_per_year' => function ($query) {
                    return $query->select('id', 'user_id_fk', 'paid_total_payment_per_year')->where('paid_total_payment_per_year', ">", 0);
                }
            ])->get();
        $users = collect($usersQuery)->filter(function ($v) {
            return collect($v->user_payment_per_year)->isNotEmpty();
        });
        if ($request->has('user_info')) {
            $user_id = $request->get('user_info');
            $inv_by_budgetyear = UserPaymentPeryear::where('user_id_fk', $request->get('user_info'))
                ->with(['user_payment_per_month' => function ($query) use ($user_id) {
                    return $query->select('user_payment_per_year_id_fk', 'month', 'user_id_fk', 'rate_payment_per_month', 'status', 'recorder_id_fk', 'created_at', 'updated_at')
                        ->where('user_id_fk', $user_id);
                }])
                ->get();
        }
        return view('user_payment_per_month.history', compact('users', 'inv_by_budgetyear'));
    }

    public function printReceiptHistory($userPaymentPerYearId)
    {
        $userPaymentPerYear = UserPaymentPerYear::where('id', $userPaymentPerYearId)
            ->with(['user_payment_per_month', 'userMeterInfo'])
            ->first();
        $datas = [
            'usermeterinfos'    => $userPaymentPerYear->userMeterInfo,
            'update_date'       => date('Y-m-d'),
            'paid_budgetyear'   => []
        ];

        array_push($datas['paid_budgetyear'], [
            'budgetyear'                => KpBudgetYear::where('id', $userPaymentPerYear->budgetyear_id_fk)->get('budgetyear_name'),
            'month_paid_count'          => collect($userPaymentPerYear->user_payment_per_month)->count(),
            'billcycleOwe'              => $userPaymentPerYear->userMeterInfo->owe_count - collect($userPaymentPerYear->user_payment_per_month)->count(),
            'sum_rate_payment_per_month' => collect($userPaymentPerYear->user_payment_per_month)->sum('rate_payment_per_month'),
            'recorder'                  => User::where('id', $userPaymentPerYear->user_payment_per_month[0]->recorder_id_fk)->get(['firstname', 'lastname']),
            'rate_payment_per_month'    => $userPaymentPerYear->user_payment_per_month[0]->rate_payment_per_month

        ]);
        $from = 'userPaymentPerMonth.printReceiptHistory';

        return view('user_payment_per_month.receipt_print', compact('datas', 'from'));
    }

    public function invoice()
    {
        //ออกใบแจ้งหนี้
        $owe_users  = UserKayaInfos::where('owe_count', '>', 0)->get();
        $subzones   = collect(Subzone::all())->sortBy('zone_id');
        $page       = 'index';

        $datas_array = collect();

        foreach ($owe_users as $owe_user) {
            $datas_array->push([
                'id'               => $owe_user->id,
                'user_id'          => $owe_user->user_id,
                'firstname'        => $owe_user->user->firstname,
                'lastname'         => $owe_user->user->lastname,
                'usergroup_name'   => $owe_user->usergroup->usergroup_name,
                'address'          => $owe_user->user->address,
                'zone_name'        => $owe_user->user->user_zone->zone_name,
                'subzone_name'     => $owe_user->user->user_subzone->subzone_name,
                'owe_count'        => $owe_user->owe_count,
                'comment'          => $owe_user->comment
            ]);
        }
        return view('user_payment_per_month.invoice', compact('owe_users', 'subzones', 'page', 'datas_array'));
    }

    public function print_notice_letters(Request $request)
    {
        $owe_users  = $request->get('user_id_checked');
        $owes       = collect();
        foreach ($owe_users as $user_id) {
            $owe    = UserPaymentPerMonth::where('user_id_fk', $user_id)
                ->where('status', 'init')
                ->get(['user_id_fk', 'user_payment_per_year_id_fk', 'month', 'rate_payment_per_month', 'status']);

            $owe_user_payment_permonth_grouped = collect($owe)->groupBy('user_payment_per_year_id_fk');
            $owe_by_budgetyear  = collect();
            foreach ($owe_user_payment_permonth_grouped as $key => $user_payment_per_month_group) {

                $rate_per_budgetyear_per_year       = $user_payment_per_month_group[0]->user_payment_per_year->id;
                $owe_diff                           = collect($user_payment_per_month_group)->count();
                $owe_by_budgetyear->push([
                    'budgetyear_name' => $user_payment_per_month_group[0]->user_payment_per_year->budgetyear->budgetyear_name,
                    'owe_month_count' => $owe_diff,
                    'rate_per_month'  => $user_payment_per_month_group[0]->rate_payment_per_month,
                    'bin_count'       => $user_payment_per_month_group[0]->user_payment_per_year->bin_quantity,
                    'owe_total'       => $user_payment_per_month_group[0]->rate_payment_per_month * $owe_diff
                ]);
            }
            $user_info = User::where('id', $user_id)->get(['prefix', 'firstname', 'lastname', 'address', 'zone_id']);
            $owes->push([
                'user_info' => $user_info,
                'zonename'  => $user_info[0]->user_zone->zone_name,
                'owe_infos' => $owe_by_budgetyear
            ]);
        }
        $print_date = date('j') . " " . FunctionsController::fullThaiMonth(date('m')) . " " . (date('Y') + 543);
        $current_budgetyear = KpBudgetYear::where('status', 'active')->get(['budgetyear_name']);
        return view('user_payment_per_month.print_notice_letters', compact('owes', 'print_date', 'current_budgetyear'));
    }

    public function get_not_paid($payperyear_id, $bin_no)
    {

        $paypermonths_notpaid = UserPaymentPerMonth::where('user_payment_per_year_id_fk', $payperyear_id)
            ->with('user_payment_per_year')
            ->get();
        return $paypermonths_notpaid;
    }
}
