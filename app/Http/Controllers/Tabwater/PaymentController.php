<?php

namespace App\Http\Controllers\Tabwater;

use App\Http\Controllers\Controller;

use App\Http\Controllers\FunctionsController;
use App\Http\Controllers\Api\InvoiceController;
use App\Models\Admin\BudgetYear;
use App\Models\Tabwater\Account;
use App\Models\Tabwater\Invoice;
use App\Models\Tabwater\InvoicePeriod;
use App\Models\Admin\Subzone;
use App\Models\User;
use App\Models\Tabwater\UserMerterInfo;
use App\Models\Admin\Zone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Api\PaymentController as ApiPaymentController;
use App\Models\Admin\Organization;
use App\Models\Tabwater\AccTransactions;
use App\Models\Tabwater\Cutmeter;
use App\Models\Tabwater\InvoiceHistoty;
use App\Models\Tabwater\Setting;
use PDO;
use PhpParser\Node\Expr\FuncCall;

class PaymentController extends Controller
{
    public function index(REQUEST $request)
    {


        // return $this->test($request);

        if ($request->session()->has('payment_subzone_selected')) {

            if (collect($request->get('subzone_id_lists'))->isNotEmpty()) {
                $subzone_selected = $request->get('subzone_id_lists');
                $request->session()->forget('payment_subzone_selected');
                $request->session()->put('payment_subzone_selected', collect($subzone_selected)->toJson());
            } else {
                $subzone_selected = json_decode($request->session()->get('payment_subzone_selected'));
            }
        } else {
            $subzone_id = collect(Subzone::where('status', 'active')->get(['id']))->pluck('id')->toArray();
            $subzone_selected = $subzone_id;
            $request->session()->put('payment_subzone_selected', collect($subzone_selected)->toJson());
        }

        $inv_period_id = 0;
        if( $request->has('inv_period_id') ){
            $inv_period_id = $request->get('inv_period_id');
        }

        $usermeterinfosQuery = UserMerterInfo::whereIn('status', ['active', 'inactive', 'deleted'])
            ->with([
                'invoice' => function ($query) use ($inv_period_id){
                    $query->select('meter_id_fk', 'inv_id', 'inv_period_id_fk', 'status', 'water_used', 'totalpaid', 'inv_no', 'acc_trans_id_fk')
                        ->whereIn('status', ['owe', 'invoice']);
                    if($inv_period_id > 0){
                        $query->where('inv_period_id_fk', $inv_period_id);
                    }
                    return  $query;
                      
                },
                'meter_type' => function ($query) {
                    $query->select('id', 'price_per_unit');
                }
            ]);
        if (collect($subzone_selected)->isNotEmpty()) {
            $usermeterinfosQuery = $usermeterinfosQuery->whereIn('undertake_subzone_id', $subzone_selected);
        }

        $usermeterinfos = $usermeterinfosQuery->get([
            'meter_id',
            'undertake_subzone_id',
            'meter_address',
            'undertake_zone_id',
            'user_id',
            'metertype_id',
            'meternumber',
            'owe_count',
            'cutmeter',
            'inv_no_index'
        ]);

        $invoices = collect($usermeterinfos)->filter(function ($v) {
            if (collect($v->invoice)->isNotEmpty()) {
                return $v;
            }
        });

        foreach($usermeterinfos as $umf){
             $umf['same'] = false;
            if ( collect($umf['invoice'])->isNotEmpty() ) {
                $firstInvNo = $umf['invoice'][0]->inv_no;
                $allInvNoAreSame = true;
                foreach ($umf['invoice'] as $inv){
                    if ($inv->inv_no !== $firstInvNo) {
                        $allInvNoAreSame = false;
                        break;
                    }
                }
               
                if ($allInvNoAreSame) {
                    $umf['same'] = true;
                    // ทำสิ่งที่คุณต้องการเมื่อ inv_no ทั้ง 6 ตัวเหมือนกัน
                    // เช่น dd('Inv_no ทั้ง 6 ตัวเหมือนกัน: ' . $firstInvNo);
                    // หรือเก็บค่าในตัวแปรเพื่อนำไปใช้ต่อไป
                } 
            } 
        }


        $subzones = collect(Subzone::all())->sortBy('zone_id');
        $page = 'index';
        $selected_subzone_name_array = [];
        if ($request->has('check-input-select-all')) {
            array_push($selected_subzone_name_array, 'ทุกเส้นทางจดมิเตอร์');
        } else {
            $i = 0;
            foreach ($subzone_selected as $subzone_id) {
                $subzone_name = Subzone::where('id', $subzone_id)->get('subzone_name');
                array_push($selected_subzone_name_array, $i == 0 ? "   " . $subzone_name[0]->subzone_name : ", " . $subzone_name[0]->subzone_name);
                $i++;
            }
        }

        $total_water_used = collect($invoices)->sum(function ($v) {
            return $v->invoice[0]->water_used;
        });
          $current_budgetyear = BudgetYear::where('status', 'active')->with([
            'invoicePeriod' => function($q){
                return $q->select('id', 'budgetyear_id','inv_p_name')->where('deleted', 0);
            }
        ])->get(['id', 'budgetyear_name']);

        $select_all = collect($subzones)->count() == collect($subzone_selected)->count() ? true : false;
        return view('payment.index', compact(
            'invoices',
            'subzones',
            'page',
            'subzone_selected',
            'select_all',
            'selected_subzone_name_array',
            'total_water_used',
            'current_budgetyear'
        ));
    }

    private function test(Request $request)
    {
        if ($request->session()->has('payment_subzone_selected')) {
            if (collect($request->get('subzone_id_lists'))->isNotEmpty()) {
                $subzone_selected = $request->get('subzone_id_lists');
                $request->session()->forget('payment_subzone_selected');
                $request->session()->put('payment_subzone_selected', collect($subzone_selected)->toJson());
            } else {
                $subzone_selected = json_decode($request->session()->get('payment_subzone_selected'));
            }
        } else {
            $subzone_id = collect(Subzone::where('status', 'active')->get(['id']))->pluck('id')->toArray();
            $subzone_selected = $subzone_id;
            $request->session()->put('payment_subzone_selected', collect($subzone_selected)->toJson());
        }

        $usermeterinfosQuery = UserMerterInfo::whereIn('status', ['active', 'inactive', 'deleted'])
            ->with([
                'invoice' => function ($query) {
                    return $query->select('meter_id_fk', 'inv_id', 'inv_period_id_fk', 'status', 'water_used', 'totalpaid', 'inv_no', 'acc_trans_id_fk')
                        ->whereIn('status', ['owe', 'invoice']);
                },
                'meter_type' => function ($query) {
                    $query->select('id', 'price_per_unit');
                }
            ]);
        if (collect($subzone_selected)->isNotEmpty()) {
            $usermeterinfosQuery = $usermeterinfosQuery->whereIn('undertake_subzone_id', $subzone_selected);
        }

        $usermeterinfos = $usermeterinfosQuery->get([
            'meter_id',
            'undertake_subzone_id',
            'meter_address',
            'undertake_zone_id',
            'user_id',
            'metertype_id',
            'meternumber',
            'owe_count',
            'cutmeter'
        ]);

        $invoices = collect($usermeterinfos)->filter(function ($v) {
            if (collect($v->invoice)->isNotEmpty()) {
                return $v;
            }
        });

        // Check if all inv_no values are the same
        $inv_no_not_all = false;
        if ($invoices->isNotEmpty()) {
            $first_inv_no = $invoices->first()->invoice[0]->inv_no ?? null;
            $inv_no_not_all = $invoices->contains(function ($v) use ($first_inv_no) {
                return ($v->invoice[0]->inv_no ?? null) !== $first_inv_no;
            });
        }

        $subzones = collect(Subzone::all())->sortBy('zone_id');
        $page = 'index';
        $selected_subzone_name_array = [];
        if ($request->has('check-input-select-all')) {
            array_push($selected_subzone_name_array, 'ทุกเส้นทางจดมิเตอร์');
        } else {
            $i = 0;
            foreach ($subzone_selected as $subzone_id) {
                $subzone_name = Subzone::where('id', $subzone_id)->get('subzone_name');
                array_push($selected_subzone_name_array, $i == 0 ? "   " . $subzone_name[0]->subzone_name : ", " . $subzone_name[0]->subzone_name);
                $i++;
            }
        }

        $total_water_used = collect($invoices)->sum(function ($v) {
            return $v->invoice[0]->water_used;
        });

        $select_all = collect($subzones)->count() == collect($subzone_selected)->count() ? true : false;
        return $invoices;
    }

    private function insertInv_no_status_paid()
    {
        ini_set('memory_limit', '512M');

        $invHGrouped = Invoice::with(['invoice_period' => function ($q) {
            return $q->select('budgetyear_id', "id");
        }])
            ->where('status', 'paid')
            ->get(['meter_id_fk', 'acc_trans_id_fk', 'inv_period_id_fk', 'status']);
        $invUserGroup =  collect($invHGrouped)->groupBy('meter_id_fk');
        foreach ($invUserGroup->chunk(500) as $chunk) {
            foreach ($chunk as $key => $userG) {
                $accTransGroup = collect($userG)->groupBy('acc_trans_id_fk');
                $i = 0;
                $bg = '';
                foreach ($accTransGroup as $key => $group) {
                    $i++;
                    if ($bg == '' || $bg == $group[0]->invoice_period->budgetyear_id) {
                        $bg = $group[0]->invoice_period->budgetyear_id;
                    } else {
                        $i = 1;
                        $bg = $group[0]->invoice_period->budgetyear_id;
                    }
                    $ii = $i < 10 ? "0" . $i : $i;
                    $budgetyear_id = $group[0]->invoice_period->budgetyear_id < 10 ? "0" . $group[0]->invoice_period->budgetyear_id : $group[0]->invoice_period->budgetyear_id;

                    Invoice::where('meter_id_fk', $group[0]->meter_id_fk)
                        ->where('acc_trans_id_fk', $key)->update([
                            'inv_no' => $budgetyear_id . "" . $ii
                        ]);
                }
            }
        }
    }

    private function insertInv_no_status_owe()
    {
        ini_set('memory_limit', '512M');

        $invHGrouped = Invoice::with(['invoice_period' => function ($q) {
            return $q->select('budgetyear_id', "id");
        }])
            ->whereIn('status', ['owe', 'invoice'])
            // ->where('meter_id_fk', 1879)
            ->get(['meter_id_fk', 'acc_trans_id_fk', 'inv_period_id_fk', 'status']);
        $invUserGroup =  collect($invHGrouped)->groupBy('meter_id_fk');
        foreach ($invUserGroup->chunk(500) as $chunk) {
            $prvInP = 0;
            foreach ($chunk as $key => $userG) {
                $prvInP =  $userG[0]->inv_period_id_fk - 1;
                $inv = Invoice::where('inv_period_id_fk', $prvInP)
                    ->where('meter_id_fk', $userG[0]->meter_id_fk)
                    ->get('inv_no');
                $bg = '';
                $i = 0;
                if (collect($inv)->isNotEmpty()) {
                    $i = intval(substr($inv[0]->inv_no, 2)) + 1;
                    $bg = intval(substr($inv[0]->inv_no, 0, 2));
                }
                $accTransGroup = collect($userG)->groupBy('acc_trans_id_fk');


                foreach ($accTransGroup as $key => $group) {
                    $i++;
                    if ($bg == '' || $bg == $group[0]->invoice_period->budgetyear_id) {
                        $bg = $group[0]->invoice_period->budgetyear_id;
                    } else {
                        $i = 1;
                        $bg = $group[0]->invoice_period->budgetyear_id;
                    }
                    $ii = $i < 10 ? "0" . $i : $i;
                    $budgetyear_id = $group[0]->invoice_period->budgetyear_id < 10 ? "0" . $group[0]->invoice_period->budgetyear_id : $group[0]->invoice_period->budgetyear_id;

                    Invoice::where('meter_id_fk', $group[0]->meter_id_fk)
                        ->where('acc_trans_id_fk', $key)->update([
                            'inv_no' => $budgetyear_id . "" . $ii
                        ]);
                }
            }
        }
    }

    private function insertInv_no_status_init()
    {
        ini_set('memory_limit', '512M');

        $invHGrouped = Invoice::with(['invoice_period' => function ($q) {
            return $q->select('budgetyear_id', "id");
        }])
            ->where('status', 'init')
            // ->where('meter_id_fk', 1879)
            ->get(['meter_id_fk', 'acc_trans_id_fk', 'inv_period_id_fk', 'status']);
        $invUserGroup =  collect($invHGrouped)->groupBy('meter_id_fk');
        foreach ($invUserGroup->chunk(500) as $chunk) {
            $prvInP = 0;
            foreach ($chunk as $key => $userG) {
                $prvInP =  $userG[0]->inv_period_id_fk - 1;
                $inv = Invoice::where('inv_period_id_fk', $prvInP)
                    ->where('meter_id_fk', $userG[0]->meter_id_fk)
                    ->get('inv_no');
                $bg = '';
                $i = 0;
                if (collect($inv)->isNotEmpty()) {
                    $i = intval(substr($inv[0]->inv_no, 2)) + 1;
                    $bg = intval(substr($inv[0]->inv_no, 0, 2));
                }
                $accTransGroup = collect($userG)->groupBy('acc_trans_id_fk');


                foreach ($accTransGroup as $key => $group) {
                    $i++;
                    if ($bg == '' || $bg == $group[0]->invoice_period->budgetyear_id) {
                        $bg = $group[0]->invoice_period->budgetyear_id;
                    } else {
                        $i = 1;
                        $bg = $group[0]->invoice_period->budgetyear_id;
                    }
                    $ii = $i < 10 ? "0" . $i : $i;
                    $budgetyear_id = $group[0]->invoice_period->budgetyear_id < 10 ? "0" . $group[0]->invoice_period->budgetyear_id : $group[0]->invoice_period->budgetyear_id;

                    Invoice::where('meter_id_fk', $group[0]->meter_id_fk)
                        ->where('acc_trans_id_fk', $key)->update([
                            'inv_no' => $budgetyear_id . "" . $ii
                        ]);
                }
            }
        }
    }

    private function updateInvoice()
    {
        // $invoices = Invoice::whereIn('status', ['invoice', 'owe'])->get();
        $invoices = Invoice::all();
        foreach ($invoices as $invoice) {
            $water_used = $invoice->currentmeter - $invoice->lastmeter;
            $inv_type   = $water_used == 0 ? 'r' : 'u';
            $paid       = $water_used * 6;
            // $vat        = $paid * 0;
            $totalpaid  = $paid + 10;
            Invoice::where('inv_id', $invoice->inv_id)->update([
                'water_used' => $water_used,
                'inv_type'  => $inv_type,
                'paid'      => $paid,
                'vat'       => 0,
                'reserve_meter' => 10,
                'totalpaid' => $totalpaid,
            ]);
        }
        return 1;
    }
    public function index_search_by_suzone(Request $request)
    {
        $subzone_search_lists = $request->get('subzone_id_lists');

        if ($request->session()->has('payment_subzone_selected')) {
            $request->session()->forget('payment_subzone_selected');
        }
        $request->session()->put('payment_subzone_selected', collect($subzone_search_lists)->toJson());

        $usermeterinfos = UserMerterInfo::whereIn('status', ['active', 'inactive'])
            ->with([
                'invoice' => function ($query) {
                    return $query->select('meter_id_fk', 'inv_id', 'inv_period_id_fk', 'status')
                        ->whereIn('status', ['owe', 'invoice']);
                },
                'meter_type' => function ($query) {
                    $query->select('id', 'price_per_unit');
                }
            ])->whereIn('undertake_subzone_id', $subzone_search_lists)
            ->get(['meter_id', 'undertake_subzone_id', 'undertake_subzone_id', 'undertake_zone_id', 'user_id', 'metertype_id', 'meternumber', 'owe_count']);

        $invoices = collect($usermeterinfos)->filter(function ($v) {
            return collect($v->invoice)->isNotEmpty();
        });
        $subzones = collect(Subzone::all())->sortBy('zone_id');
        $page = 'index_search_by_suzone';

        return view('payment.index', compact('invoices', 'subzones', 'subzone_search_lists', 'page'));
    }

    public function create(Request $request)
    {
        $paymentsArr = [];
        $total = 0;
        foreach ($request->get('data') as $inv) {
            $v = Invoice::where('id', $inv['inv_id'])
                ->with('usermeterinfos', 'usermeterinfos.user_profile', 'invoice_period')
                ->get();
            array_push($paymentsArr, $v);

            $total += ($v[0]->currentmeter - $v[0]->lastmeter) * 8;
        }
        $payments = collect($paymentsArr)->flatten();
        return view('payment.invoice_sum', compact('payments', 'total'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'payments' => 'required|array|min:1',
        ]);

        date_default_timezone_set('Asia/Bangkok');


        $payments = collect($request->get('payments'))->filter(function ($v) {
            return isset($v['on']);
        });

        
        $accT = AccTransactions::create([
            'user_id_fk'    => $request->get('user_id'),
            'paidsum'       => $request->get('paidsum'),
            'inv_no_fk'     => $request->get('inv_no'),
            'vatsum'        => $request->get('vat7'),
            'totalpaidsum'  => $request->get('mustpaid'), //จ่ายทั้งหมด
            'net'           =>  $request->get('mustpaid'), //จำนวนจ่ายจริงแล้ว
            'status'        => 1,
            'cashier'       => Auth::id(),
            "created_at"    => date("Y-m-d H:i:s"),
            "updated_at"    => date("Y-m-d H:i:s"),

        ]);

        $accounts = Account::where('payee', $request->get('user_id'))->first();
        if (collect($accounts)->isEmpty()) {
            Account::create([
                'deposit'    => collect($payments)->sum('total'),
                'payee'      => $request->get('user_id'),
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s"),
            ]);
        } else {
            Account::where('payee', $request->get('user_id'))->update([
                'deposit' => $accounts->deposit + collect($payments)->sum('total'),
                'payee'   => $request->get('user_id'),
                'updated_at' => date("Y-m-d H:i:s"),
            ]);
        }

        //ทำการ update user_meter_infos table โดยการลบ owe_count
        $checkCutmeterInfos = Cutmeter::where('meter_id_fk', $request->get('meter_id'))->whereIn('status', ['init', 'cutmeter'])->get();
        $userMeterInfosQuery = UserMerterInfo::where('meter_id', $request->get('meter_id'));
        $user_meter_owe_count = $userMeterInfosQuery->get(['owe_count', 'cutmeter'])->first();
        $remain_owe_count  = 0;
        if ($user_meter_owe_count->owe_count >  0) {
            $payments_owe_status_count = collect($payments)->filter(function ($v) {
                return $v['status'] == 'owe' ||  $v['status'] == 'invoice';
            })->count();
            $remain_owe_count = $user_meter_owe_count->owe_count - $payments_owe_status_count;
        }

        if (collect($checkCutmeterInfos)->isNotEmpty()) {
            if ($remain_owe_count == 0 && $user_meter_owe_count->cutmeter == 1) {
                //update cutmeter table status = 'install'
                $cutmeterQuery = Cutmeter::where('meter_id_fk', $request->get('meter_id'))->whereIn('status', ['init', 'cutmeter']);
                $cutmeter      = $cutmeterQuery->get()->first();
                if (collect($cutmeter)->isNotEmpty()) {
                    if ($cutmeter->status == 'cutmeter') {
                        $installMeter               = json_decode($cutmeter->progress)[1];
                        $installMeter->topic        = 'install';
                        $installMeter->created_at   = strtotime(date("Y-m-d H:i:s"));
                        $progressInstall            =  json_decode($cutmeter->progress);
                        array_push($progressInstall, $installMeter);
                    };
                    $cutmeterQuery->update([
                        'status'        => $cutmeter->status == "init" ? "complete" : "install",
                        'owe_count'     => $remain_owe_count,
                        "progress"      => $cutmeter->status == "init" ? json_encode([]) : json_encode($progressInstall),
                        "updated_at"    => date("Y-m-d H:i:s"),
                    ]);
                }
                $userMeterInfosQuery->update([
                    'owe_count'     => $remain_owe_count,
                    'cutmeter'      => $cutmeter->status == 'init' || $cutmeter->status == 'complete' ? 0 : 1,
                    'status'        => $cutmeter->status == 'init' || $cutmeter->status == 'complete' ? 'active' : 'inactive',
                    'updated_at'    => date("Y-m-d H:i:s"),
                ]);
            } else {
                $userMeterInfosQuery->update([
                    'owe_count'     => $remain_owe_count,
                    'updated_at'    => date("Y-m-d H:i:s"),
                ]);
            }
        } else {
            UserMerterInfo::where('meter_id', $request->get('meter_id'))->update([
                'owe_count' => $remain_owe_count,
                'cutmeter'  => $remain_owe_count < 2 ? 0 : 1,
                'status'    => $remain_owe_count == 0 ? "active" : 'inactive',
            ]);
        }

        foreach ($payments as $payment) {
            Invoice::where('inv_id', $payment['iv_id'])->update([
                'acc_trans_id_fk' => $accT->id,
                'status'          => 'paid',
                'updated_at'      => date("Y-m-d H:i:s"),
            ]);
        }


        return $this->receipt_print2($request, $accT->id, 'payment.index');
        // return redirect()->route('payment.receipt_print')->with(['account_id_fk' => $accT->id]);
    }

    private function modify_inv_no()
    {
        $invs = Invoice::whereBetween('meter_id_fk', [1, 4000])
            ->where('deleted', 0)
            ->get(['inv_id', 'inv_no', 'inv_period_id_fk', 'acc_trans_id_fk', 'meter_id_fk']);

        $groupeds = collect($invs)->groupBy('meter_id_fk');
        foreach ($groupeds as $g) {
            $acc_trans_id_fk_groups = collect($g)->groupBy('acc_trans_id_fk');
            $c = 1;
            $cfore6 = 0;
            foreach ($acc_trans_id_fk_groups as $acc_g) {
                $cfore6 = $c;
                foreach ($acc_g as $a) {

                    $meter_id_str = substr("0000", strlen($a->meter_id_fk)) . "" . $a->meter_id_fk;
                    Invoice::where('inv_id', $a->inv_id)->update([
                        'inv_no' => $a->inv_period_id_fk == 6 ?  "010" . $cfore6 . "" . $meter_id_str : "010" . $c . "" . $meter_id_str
                    ]);
                }

                $c++;
            }
        }

        return   $invs = Invoice::whereBetween('meter_id_fk', [1, 2])
            ->where('deleted', 0)
            ->orderBy('meter_id_fk')
            ->get(['inv_id', 'inv_no', 'inv_period_id_fk', 'acc_trans_id_fk', 'meter_id_fk']);
    }

    private function modify_inv_no2()
    {
        $invs = Invoice::whereBetween('meter_id_fk', [1, 4000])
            ->where('deleted', 0)
            ->whereIn('inv_period_id_fk', [5, 6])
            ->get(['inv_id', 'inv_no', 'inv_period_id_fk', 'acc_trans_id_fk', 'meter_id_fk', 'status']);

        foreach (collect($invs)->groupBy('meter_id_fk') as $inv) {
            // return $inv;
            if ($inv[0]->status == 'owe') {
                Invoice::where('inv_id', $inv[1]->inv_id)->update([
                    'inv_no' => $inv[0]->inv_no
                ]);
            }
        }
    }
    public function store_by_inv_no(Request $request)
    {
        $arr = [];
        
        foreach ($request->get('datas') as $dataArray) {
             list($req_meter_id, $req_inv_no) = explode("|",$dataArray);
         
             $inv = new Invoice();
            //sum paid vat และ totalpaid แล้ว create acc_transactions table
              $inv_infos = Invoice::where('meter_id_fk', $req_meter_id)
                ->whereIn('status', ['invoice', 'owe'])
                ->get(['inv_id',  'paid', 'vat', 'totalpaid', 'meter_id_fk', 'status']);

            //update Invoice row ที่ตรงกับ inv_no
            
            $paid_sum = 0;
            $vat_sum = 0;
            $reserve_meter_sum = 0;
            $totalpaid_sum = 0;
            foreach($inv_infos as $inv){
                $paid_sum += $inv->paid;
                $vat_sum += $inv->vat;
                $totalpaid_sum += $inv->totalpaid;
                $reserve_meter_sum += $inv->reserve_meter_sum;
            }

            $acc_trans = AccTransactions::create([
                    'user_id_fk' => $inv->meter_id_fk,
                    'paidsum' => $paid_sum,
                    'vatsum' => $vat_sum,
                    'reserve_meter_sum' => $reserve_meter_sum,
                    'totalpaidsum' => $totalpaid_sum,
                    'status' => '1',
                    'cashier' => Auth::user()->id,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
             foreach($inv_infos as $inv){
                Invoice::where('inv_id', $inv->inv_id)->update([
                    'status' => 'paid',
                    'recorder_id' => Auth::user()->id,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'acc_trans_id_fk' => $acc_trans->id,
                    'inv_no' => $req_inv_no
                ]);
               
            }


            //update usermeter_info 
            // $invStatusOwesCount = Invoice::where('meter_id_fk', $req_meter_id)->whereIn('status', ['owe', 'invoice'])->count();
           
            $usermeter_infos = UserMerterInfo::where('meter_id', $req_meter_id)
                ->get(['owe_count','inv_no_index']);

            // $res = $usermeter_infos[0]->owe_count - $invStatusOwesCount < 0 ? 0 : $usermeter_infos[0]->owe_count - $invStatusOwesCount;
            UserMerterInfo::where('meter_id', $req_meter_id)
                ->update([
                    'owe_count' => $usermeter_infos[0]->owe_count - collect($inv_infos)->count(),
                    'inv_no_index' =>  $usermeter_infos[0]->inv_no_index +1
                ]);
            


            $invoicesPaidForPrint = Invoice::where('acc_trans_id_fk', $acc_trans->id)
                ->with([
                    'invoice_period' => function ($query) {
                        return $query->select('id', 'inv_p_name');
                    },
                    'usermeterinfos' => function ($query) {
                        return $query->select('meter_id', 'user_id', 'meternumber', 'undertake_zone_id', 'undertake_subzone_id', 'meter_address');
                    },
                    'acc_transactions' => function ($query) {
                        return $query->select('id', 'reserve_meter_sum', 'user_id_fk', 'paidsum', 'vatsum', 'totalpaidsum', 'cashier', 'updated_at')
                            ->where('status', 1);
                    },
                    'acc_transactions.cashier_info' => function ($query) {
                        return $query->select('id', 'prefix', 'firstname', 'lastname')
                            ->where('status', 1);
                    }
                ])
                ->get(['inv_id', 'inv_period_id_fk', 'meter_id_fk', 'inv_no', 'lastmeter', 'currentmeter', 'status', 'acc_trans_id_fk', 'recorder_id', 'updated_at', 'created_at']);

            $invoicesPaidForPrint[0]['casheir'] = User::where('id', Auth::user()->id)->get(['id', 'prefix', 'firstname', 'lastname']);
            array_push($arr, $invoicesPaidForPrint);
        } //foreach
        $funcCtrl = new \App\Http\Controllers\FunctionsController();
        $settingModel = new Setting();
        // $setting = $settingModel->getSettingInfosByGovId(Auth::user()->settings_id_fk);
        $newId = "-RC-" . $funcCtrl->createInvoiceNumberString($acc_trans->id, 'RC');
        $type = 'paid_receipt';
        $from_blade = 'payment.index';
        return view('payment.receipt_print_multi', compact('arr', 'newId', 'type', 'from_blade'));
    }



    public function receipt_print_multi(REQUEST $request, $account_id_fk = 0, $from_blade = 'payment.index')
    {

        // $funcCtrl = new FunctionsController();
        // $orgInfos = $funcCtrl->getOrgInfos()[0];
        $receipt_id = $account_id_fk;
        if ($request->session()->has('account_id_fk')) {
            $receipt_id = $request->session()->get('account_id_fk');
        }

        //ดึงมาจาก  invoice_history_table  เพราะทำการย้าย data status paid ไปเก็บไว้ตอน  payment.stor
        $invoicesPaidForPrint = InvoiceHistoty::where('acc_trans_id_fk', $receipt_id)
            ->where('status', 'paid')
            ->with([
                'invoice_period' => function ($query) {
                    return $query->select('id', 'inv_p_name');
                },
                'usermeterinfos' => function ($query) {
                    return $query->select('meter_id', 'user_id', 'meternumber', 'undertake_subzone_id');
                },
                'acc_transactions' => function ($query) {
                    return $query->select('id', 'user_id_fk', 'paidsum', 'vatsum', 'totalpaidsum', 'cashier', 'updated_at')
                        ->where('status', 1);
                },
                'acc_transactions.cashier_info' => function ($query) {
                    return $query->select('id', 'prefix', 'firstname', 'lastname')
                        ->where('status', 1);
                }
            ])
            ->get(['inv_id', 'inv_period_id_fk', 'meter_id_fk', 'lastmeter', 'currentmeter', 'status', 'acc_trans_id_fk', 'recorder_id', 'updated_at', 'created_at']);

        $funcCtrl = new FunctionsController();
        $settingModel = new Setting();
        // $setting = $settingModel->getSettingInfosByGovId(Auth::user()->settings_id_fk);
        $newId = "-RC-" . $funcCtrl->createNumberString($receipt_id, 'RC');
        $type = 'paid_receipt';
        return view('payment.receipt_print', compact('invoicesPaidForPrint', 'newId', 'type', 'from_blade'));
    }

    public function receipt_print(REQUEST $request, $account_id_fk = 0, $from_blade = 'payment.index')
    {
        $receipt_id = $account_id_fk;
        if ($request->session()->has('account_id_fk')) {
            $receipt_id = $request->session()->get('account_id_fk');
        }
        $invoicesPaidForPrint = Invoice::where('acc_trans_id_fk', $receipt_id)
            ->with([
                'invoice_period' => function ($query) {
                    return $query->select('id', 'inv_p_name');
                },
                'usermeterinfos' => function ($query) {
                    return $query->select('meter_id', 'user_id', 'meternumber', 'undertake_subzone_id');
                },
                'acc_transactions' => function ($query) {
                    return $query->select('id', 'user_id_fk', 'paidsum', 'vatsum', 'totalpaidsum', 'cashier', 'updated_at')
                        ->where('status', 1);
                },
                'acc_transactions.cashier_info' => function ($query) {
                    return $query->select('id', 'prefix', 'firstname', 'lastname')
                        ->where('status', 1);
                }
            ])
            ->get(['inv_id', 'inv_period_id_fk', 'meter_id_fk', 'lastmeter', 'currentmeter', 'status', 'acc_trans_id_fk', 'recorder_id', 'updated_at', 'created_at']);

        $newId = (new FunctionsController())->createInvoiceNumberString($receipt_id);
        $type = 'paid_receipt';
        return view('payment.receipt_print', compact('invoicesPaidForPrint', 'newId', 'type', 'from_blade'));
    }

    private function receipt_print2(REQUEST $request, $account_id_fk, $from_blade)
    {
        $receipt_id = $account_id_fk;
        if ($request->session()->has('account_id_fk')) {
            $receipt_id = $request->session()->get('account_id_fk');
        }
        $invoicesPaidForPrint = Invoice::where('acc_trans_id_fk', $receipt_id)
            ->with([
                'invoice_period' => function ($query) {
                    return $query->select('id', 'inv_p_name');
                },
                'usermeterinfos' => function ($query) {
                    return $query->select('meter_id', 'user_id', 'meternumber', 'undertake_subzone_id');
                },
                'acc_transactions' => function ($query) {
                    return $query->select('id', 'user_id_fk', 'paidsum',  'vatsum', 'totalpaidsum', 'cashier', 'updated_at')
                        ->where('status', 1);
                },
                'acc_transactions.cashier_info' => function ($query) {
                    return $query->select('id', 'prefix', 'firstname', 'lastname')
                        ->where('status', 1);
                }
            ])
            ->get(['inv_id', 'inv_period_id_fk', 'meter_id_fk', 'inv_no', 'lastmeter', 'currentmeter', 'status', 'acc_trans_id_fk', 'recorder_id', 'updated_at', 'created_at']);

        $newId = (new FunctionsController())->createInvoiceNumberString($receipt_id);
        $type = 'paid_receipt';
        return view('payment.receipt_print', compact('invoicesPaidForPrint', 'newId', 'type', 'from_blade'));
    }
    public function receipt_print_history($id)
    {
        //  $invs = UserMerterInfo::where('undertake_zone_id', 5)
        // ->with([
        //     'invoice' => function($q){
        //         return $q->select('inv_id','meter_id_fk', 'inv_period_id_fk', 'inv_no', 'acc_trans_id_fk','status', 'updated_at')
        //         ->where('inv_period_id_fk',6);
        //     }
        // ])
        // ->where('status', 'active')
        // ->get(['meter_id', 'undertake_zone_id']);

        // foreach($invs as $inv){

        //         Invoice::where('inv_id', $inv->invoice[0]->inv_id)->update([
        //             'status' => 'invoice'
        //         ]);
        // }
        $receipt_id = $id;

        $invoiceTable = Invoice::where('acc_trans_id_fk', $receipt_id)
            ->with([
                'invoice_period' => function ($query) {
                    return $query->select('id', 'inv_p_name');
                },
                'usermeterinfos' => function ($query) {
                    return $query->select('meter_id', 'user_id', 'meternumber', 'undertake_subzone_id', 'submeter_name');
                },
                'acc_transactions' => function ($query) {
                    return $query->select('id', 'user_id_fk', 'paidsum', 'vatsum', 'totalpaidsum', 'cashier', 'updated_at')
                        ->where('status', 1);
                },
                'acc_transactions.cashier_info' => function ($query) {
                    return $query->select('id', 'prefix', 'firstname', 'lastname')
                        ->where('status', 1);
                }
            ])
            ->get(['inv_period_id_fk', 'meter_id_fk', 'inv_no', 'lastmeter', 'currentmeter', 'status', 'acc_trans_id_fk', 'recorder_id', 'updated_at', 'created_at']);

        $invoiceHistoryTable = InvoiceHistoty::where('acc_trans_id_fk', $receipt_id)
            ->with([
                'invoice_period' => function ($query) {
                    return $query->select('id', 'inv_p_name');
                },
                'usermeterinfos' => function ($query) {
                    return $query->select('meter_id', 'user_id', 'meternumber', 'undertake_subzone_id');
                },
            ])
            ->get(['inv_period_id_fk', 'meter_id_fk', 'inv_no', 'lastmeter', 'currentmeter', 'status', 'acc_trans_id_fk', 'recorder_id', 'updated_at', 'created_at']);

        $invoicesPaidForPrint = collect($invoiceTable)->merge($invoiceHistoryTable);

        $newId = (new FunctionsController())->createInvoiceNumberString($receipt_id);
        $type = 'payment_search';
        return view('payment.receipt_print', compact('invoicesPaidForPrint', 'newId', 'type'));
    }
    public function search(Request $request)
    {
        $funcCtrl = new FunctionsController();
        $orgInfos = $funcCtrl->getOrgInfos()[0];

        $inv_by_budgetyear = [];
        if ($request->has('user_info')) {
            $invoiceApi = new InvoiceController();

            $invoice_infos = json_decode($invoiceApi->get_invoice_and_invoice_history($request->get('user_info'), 'paid', $orgInfos->org_database)->content(), true);

            $inv_by_budgetyear = collect($invoice_infos)->groupBy(function ($invoice_info) {
                return $invoice_info['invoice_period']['budgetyear_id'];
            })->values();
        }
        $usersQuery = (new User())->on($orgInfos->org_database)->with('usermeterinfos')->where('role_id', 3)->get(['prefix', 'firstname', 'lastname', 'address', 'id', 'zone_id']);
        $users = collect($usersQuery)->filter(function ($v) {
            return collect($v->usermeterinfos)->isNotEmpty();
        })->values();
        $zones = (new Zone())->on($orgInfos->org_database)->where('status', 'active')->get();
        $invoice_period = (new InvoicePeriod())->on($orgInfos->org_database)->where('status', 'active')->get()->first();

        return view('payment.search', compact('zones', 'invoice_period', 'users', 'inv_by_budgetyear', 'orgInfos'));
    }
    public function remove($receiptId)
    {
        date_default_timezone_set('Asia/Bangkok');

        //เปลี่ยนสถานะของ invoice table
        // receipt_id เป็น 0,
        //ถ้า inv_period_id เท่ากับ invoice period table ที่ status  เท่ากับ active (ปัจจุบัน) ให้
        // - invoice.status เท่ากับ invoice นอกเหนือจากนั้นให้ status เป็น owe
        $currentInvoicePeriod = InvoicePeriod::where('status', 'active')->get('id')->first();
        $invoicesTemp = Invoice::where('receipt_id', $receiptId);
        $invoices = $invoicesTemp->get(['inv_period_id', 'id']);
        foreach ($invoices as $invoice) {
            $status = 'invoice';

            if ($invoice->inv_period_id != $currentInvoicePeriod->id) {
                $status = 'owe';
            }
            Invoice::where('id', $invoice->id)->update([
                'receipt_id' => 0,
                'status' => $status,
                'recorder_id' => Auth::id(),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }

        //เปลี่ยนสถานะของ accouting table เป็น  0
        Account::where('id', $receiptId)->delete();
        return \redirect('payment/search');
    }
    public function paymenthistory($inv_period = '', $subzone_id = '')
    {
        $invoices = Invoice::where('inv_period_id_fk', $inv_period)
            ->with(['usermeterinfos' => function ($query) {
                return $query->select('meter_id', 'undertake_subzone_id', 'meter_address', 'meternumber', 'metertype_id', 'user_id');
            }])
            ->where('status', 'paid')->get();

        $invoices_paid = collect($invoices)->filter(function ($v) use ($subzone_id) {
            return $v->usermeterinfos->undertake_subzone_id == $subzone_id;
        })->sortBy('user_id');

        return view('payment.paymenthistory', compact('invoices_paid'));
    }
    public function receipted_list($user_id)
    {
        $apiPaymentCtrl = new ApiPaymentController;
        $receipted_list = $apiPaymentCtrl->history($user_id, 'receipt_history');
        return view('payment.receipted_list', compact('receipted_list'));
    }

    public function destroy($acc_trans_id_fk)
    {
        AccTransactions::where('id', $acc_trans_id_fk)->update([
            'status'        => 2,
            'paidsum'       => 0,
            'vatsum'        => 0,
            'totalpaidsum'  => 0,
            'net'           => 0,
            'updated_at'    => date('Y-m-d H:i:s'),
        ]);
        Invoice::where('acc_trans_id_fk', $acc_trans_id_fk)->update([
            'status'            => 'owe',
            'recorder_id'       => Auth::id(),
            'updated_at'        => date('Y-m-d H:i:s'),
        ]);

        $invCount   = Invoice::where('acc_trans_id_fk', $acc_trans_id_fk)->count();
        $invGet     = Invoice::where('acc_trans_id_fk', $acc_trans_id_fk)->get(['meter_id_fk'])->first();
        $uMeterInfo = UserMerterInfo::where('meter_id', $invGet->meter_id_fk)->get(['owe_count']);
        UserMerterInfo::where('meter_id', $invGet->meter_id_fk)->update([
            'owe_count' => $uMeterInfo[0]->owe_count - $invCount,
            'updated_at'    => date('Y-m-d H:i:s'),
        ]);
        return redirect()->route('payment.search')->with(['color' => 'success', 'message' => 'ทำการยกเลิกใบแจ้งหนี้เลขที่ ' . $acc_trans_id_fk . ' เรียบร้อยแล้ว']);
    }
}
