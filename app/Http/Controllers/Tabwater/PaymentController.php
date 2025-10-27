<?php

namespace App\Http\Controllers\Tabwater;

use App\Http\Controllers\Controller;

use App\Http\Controllers\FunctionsController;
use App\Http\Controllers\Tabwater\InvoiceController;
use App\Models\Admin\BudgetYear;
use App\Models\Admin\ManagesTenantConnection;
use App\Models\Tabwater\Account;
use App\Models\Tabwater\TwInvoice;
use App\Models\Tabwater\TwInvoicePeriod;
use App\Models\Admin\Subzone;
use App\Models\User;
use App\Models\Tabwater\TwMeterInfos;
use App\Models\Admin\Zone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Api\PaymentController as ApiPaymentController;
use App\Models\Admin\Organization;
use App\Models\Tabwater\TwAccTransactions;
use App\Models\Tabwater\TwCutmeter;
use App\Models\Tabwater\TwInvoiceHistoty;
use App\Models\Tabwater\TwInvoiceTemp;

class PaymentController extends Controller
{
    public function index(REQUEST $request)
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
            $subzone_id = Zone::getOrgSubzone('id');
            $subzone_selected = $subzone_id;
            $request->session()->put('payment_subzone_selected', collect($subzone_selected)->toJson());
        }
        $inv_period_id = 0;
        if ($request->has('inv_period_id')) {
            $inv_period_id = $request->get('inv_period_id');
        }

        $usermeterinfosQuery = TwMeterInfos::whereIn('status', ['active', 'inactive', 'deleted'])
            ->with([
                'invoice_temp' => function ($query) use ($inv_period_id) {
                    $query->select('id', 'meter_id_fk', 'inv_period_id_fk', 'status', 'water_used', 'totalpaid',  'acc_trans_id_fk')
                        ->whereIn('status', ['owe', 'invoice']);
                    return  $query;
                },
                'meter_type' => function ($query) {
                    $query->select('id');
                },
                'meter_type.rateConfigs' => function ($query) {
                    $query->select('*');
                },
                'meter_type.rateConfigs.Ratetiers' => function ($query) {
                    $query->select('*');
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
            if (collect($v->invoice_temp)->isNotEmpty()) {
                return $v;
            }
        });

        foreach ($usermeterinfos as $umf) {
            $umf['same'] = false;
            if (collect($umf['invoice_temp'])->isNotEmpty()) {
                $firstInvNo = $umf['invoice_temp'][0]->inv_no;
                $allInvNoAreSame = true;
                foreach ($umf['invoice_temp'] as $inv) {
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

        $subzones = Zone::getOrgSubzone('array');
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
            return $v->invoice_temp[0]->water_used;
        });
        $current_budgetyear = BudgetYear::where('status', 'active')->with([
            'invoice_period' => function ($q) {
                return $q->select('id', 'budgetyear_id', 'inv_p_name')->where('status','<>','deleted');
            }
        ])->get(['id', 'budgetyear_name']);

        $select_all = collect($subzones)->count() == collect($subzone_selected)->count() ? true : false;
        $orgInfos = Organization::getOrgName(Auth::user()->org_id_fk);
        return view('payment.index', compact(
            'orgInfos',
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

    public function index_search_by_suzone(Request $request)
    {
        $subzone_search_lists = $request->get('subzone_id_lists');

        if ($request->session()->has('payment_subzone_selected')) {
            $request->session()->forget('payment_subzone_selected');
        }
        $request->session()->put('payment_subzone_selected', collect($subzone_search_lists)->toJson());

        $usermeterinfos = TwMeterInfos::whereIn('status', ['active', 'inactive'])
            ->with([
                'invoice' => function ($query) {
                    return $query->select('meter_id_fk', 'inv_id', 'inv_period_id_fk', 'status')
                        ->whereIn('status', ['owe', 'invoice']);
                },
                'meter_type' => function ($query) {
                    $query->select('id', 'price_per_unit');
                }
            ])->whereIn('undertake_subzone_id', $subzone_search_lists)
            ->get(['id', 'undertake_subzone_id', 'undertake_subzone_id', 'undertake_zone_id', 'user_id', 'metertype_id', 'meternumber', 'owe_count']);

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
            $v = TwInvoiceTemp::where('id', $inv['inv_id'])
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
         $request;
        $this->validate($request, [
            'payments' => 'required|array|min:1',
        ]);

        date_default_timezone_set('Asia/Bangkok');
        $payments = collect($request->get('payments'))->filter(function ($v) {
            return isset($v['on']);
        });

        //ทำการ update user_meter_infos table โดยการลบ owe_count
        $checkCutmeterInfos = TwCutmeter::where('meter_id_fk', $request->get('meter_id'))->whereIn('status', ['pending', 'cutmeter'])->get();
        $userMeterInfosQuery = TwMeterInfos::where('meter_id', $request->get('meter_id'));
        $user_meter_owe_count = $userMeterInfosQuery->get(['owe_count', 'cutmeter'])->first();
        $remain_owe_count  = 0;
        //ทำการลบสถานะการเป็นหนี้
        if ($user_meter_owe_count->owe_count >  0) {
            $payments_owe_status_count = collect($payments)->filter(function ($v) {
                return $v['status'] == 'owe' ||  $v['status'] == 'invoice';
            })->count();
            $remain_owe_count = $user_meter_owe_count->owe_count - $payments_owe_status_count;
        }

        if (collect($checkCutmeterInfos)->isNotEmpty()) {
            if ($remain_owe_count == 0 && $user_meter_owe_count->cutmeter == 1) {
                //update cutmeter table status = 'install'
                $cutmeterQuery = TwCutmeter::where('meter_id_fk', $request->get('meter_id'))->whereIn('status', ['pending', 'cutmeter']);
                $cutmeter      = $cutmeterQuery->get()->first();
                if (collect($cutmeter)->isNotEmpty()) {
                    if ($cutmeter->status == 'cutmeter') {
                        $cutmeter->status           = 'passed';
                        $cutmeter->updated_at       = date("Y-m-d H:i:s");
                        $cutmeter->save();
                    };
                    $cutmeterQuery->update([
                        'status'        => $cutmeter->status == "pending" ? "complete" : "install",
                        'owe_count'     => $remain_owe_count,
                        "warning_print" => 0,
                        'operate_by'    => $cutmeter->operate_by, 
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
        }


        $twUserInfo = TwMeterInfos::find($request->meter_id);

        $nextLastmeter = $twUserInfo->last_meter_recording;
        $inv_no = $twUserInfo->inv_no_index;

        foreach ($payments as $payment) {
            $twInvTemp = TwInvoiceTemp::find($payment['iv_id']);

            $accTrans = new TwAccTransactions();
            $accTrans->inv_id_fk            = $payment['iv_id'];
            $accTrans->vatsum               = $twInvTemp->vat;
            $accTrans->reserve_meter_sum    = $twInvTemp->reserve_meter;
            $accTrans->paidsum              = $twInvTemp->paid;
            $accTrans->totalpaidsum         = $twInvTemp->totalpaid;
            $accTrans->status               = '1';
            $accTrans->cashier              = Auth::id();
            $accTrans->updated_at           = Now();
            $accTrans->created_at           = Now();
            $accTrans->save();

            $twInvTemp->status          = 'paid';
            $twInvTemp->acc_trans_id_fk = $accTrans->id;
            $twInvTemp->created_at      = date("Y-m-d H:i:s");
            $twInvTemp->updated_at      = date("Y-m-d H:i:s");
            $twInvTemp->inv_no          = $inv_no;
            $twInvTemp->save();
            
            if ($nextLastmeter <= $twInvTemp->currentmeter) {
                $nextLastmeter = $twInvTemp->currentmeter;
            }
      
            // TwInvoice::create([
            //     'id'                => $twInvTemp->id,
            //     'inv_period_id_fk'  => $twInvTemp->inv_period_id_fk,
            //     'inv_no'            => $inv_no,
            //     'meter_id_fk'       => $twInvTemp->meter_id_fk,
            //     'lastmeter'         => $twInvTemp->lastmeter,
            //     'water_used'        => $twInvTemp->water_used,
            //     'reserve_meter'     => $twInvTemp->reserve_meter,
            //     'inv_type'          => $twInvTemp->water_used == 0 ? 'u' : 'r',
            //     'paid'              => $twInvTemp->paid,
            //     'vat'               => $twInvTemp->vat,
            //     'totalpaid'         => $twInvTemp->totalpaid,
            //     'acc_trans_id_fk'   => $twInvTemp->acc_trans_id_fk,
            //     'currentmeter'      => $twInvTemp->currentmeter,
            //     'recorder_id'       => $twInvTemp->recorder_id,
            //     'status'            => $twInvTemp->status,
            //     'created_at'        => $twInvTemp->created_at,
            //     'updated_at'        => $twInvTemp->created_at,
            // ]);
        }


       

        $next_inv_no_index  = $twUserInfo->inv_no_index + 1;
        $remain_owe_count   = $twUserInfo->owe_count - collect($payments)->count();
        
        $twUserInfo->owe_count              = $remain_owe_count;
        $twUserInfo->cutmeter               = $remain_owe_count < 2 ? '0' : '1';
        $twUserInfo->last_meter_recording   = $nextLastmeter;
        $twUserInfo->inv_no_index           = $next_inv_no_index;
        $twUserInfo->save();


        return $this->receipt_print($request, $inv_no, 'payment.index');
    }

    public function store_by_inv_no(Request $request)
    {
        $arr = [];

        foreach ($request->get('datas') as $dataArray) {
            list($req_meter_id, $req_inv_no) = explode("|", $dataArray);

            $inv = new TwInvoiceTemp();
            //sum paid vat และ totalpaid แล้ว create acc_transactions table
            $inv_infos = TwInvoiceTemp::where('meter_id_fk', $req_meter_id)
                ->whereIn('status', ['invoice', 'owe'])
                ->get(['inv_id',  'paid', 'vat', 'totalpaid', 'meter_id_fk', 'status']);

            //update Invoice row ที่ตรงกับ inv_no

            $paid_sum = 0;
            $vat_sum = 0;
            $reserve_meter_sum = 0;
            $totalpaid_sum = 0;
            foreach ($inv_infos as $inv) {
                $paid_sum += $inv->paid;
                $vat_sum += $inv->vat;
                $totalpaid_sum += $inv->totalpaid;
                $reserve_meter_sum += $inv->reserve_meter_sum;
            }

            $acc_trans = TwAccTransactions::create([
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
            foreach ($inv_infos as $inv) {
                TwInvoiceTemp::where('inv_id', $inv->inv_id)->update([
                    'status' => 'paid',
                    'recorder_id' => Auth::user()->id,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'acc_trans_id_fk' => $acc_trans->id,
                    'inv_no' => $req_inv_no
                ]);
            }


            //update usermeter_info 
            // $invStatusOwesCount = TwInvoiceTemp::where('meter_id_fk', $req_meter_id)->whereIn('status', ['owe', 'invoice'])->count();

            $usermeter_infos = TwMeterInfos::where('id', $req_meter_id)
                ->get(['owe_count', 'inv_no_index']);

            // $res = $usermeter_infos[0]->owe_count - $invStatusOwesCount < 0 ? 0 : $usermeter_infos[0]->owe_count - $invStatusOwesCount;
            TwMeterInfos::where('id', $req_meter_id)
                ->update([
                    'owe_count' => $usermeter_infos[0]->owe_count - collect($inv_infos)->count(),
                    'inv_no_index' =>  $usermeter_infos[0]->inv_no_index + 1
                ]);



            $invoicesPaidForPrint = TwInvoiceTemp::where('acc_trans_id_fk', $acc_trans->id)
                ->with([
                    'invoice_period' => function ($query) {
                        return $query->select('id', 'inv_p_name');
                    },
                    'usermeterinfos' => function ($query) {
                        return $query->select('id', 'user_id', 'meternumber', 'undertake_zone_id', 'undertake_subzone_id', 'meter_address');
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

        $type = 'paid_receipt';
        $from_blade = 'payment.index';
        return view('payment.receipt_print_multi', compact('arr', 'newId', 'type', 'from_blade'));
    }


    public function receipt_print_multi(REQUEST $request, $inv_id = 0, $from_blade = 'payment.index')
    {

        $receipt_id = $account_id_fk;
        if ($request->session()->has('account_id_fk')) {
            $receipt_id = $request->session()->get('account_id_fk');
        }

        //ดึงมาจาก  invoice_history_table  เพราะทำการย้าย data status paid ไปเก็บไว้ตอน  payment.stor
        $invoicesPaidForPrint = TwInvoiceHistoty::where('acc_trans_id_fk', $receipt_id)
            ->where('status', 'paid')
            ->with([
                'invoice_period' => function ($query) {
                    return $query->select('id', 'inv_p_name');
                },
                'usermeterinfos' => function ($query) {
                    return $query->select('id', 'user_id', 'meternumber', 'undertake_subzone_id');
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

        $type = 'paid_receipt';
        return view('payment.receipt_print', compact('invoicesPaidForPrint', 'type', 'from_blade'));
    }

    private function receipt_print(REQUEST $request, $inv_no, $from_blade)
    {
        if ($request->session()->has('account_id_fk')) {
            $receipt_id = $request->session()->get('account_id_fk');
            $invTemp = TwInvoiceTemp::where('acc_trans_id_fk', $receipt_id)->get('inv_no')->first();
            $inv_no = $invTemp->inv_no;
        }
        
        $invoicesPaidForPrint = TwInvoiceTemp::where('meter_id_fk', $request->meter_id)
            ->where('inv_no', $inv_no)
            ->with([
                'invoice_period' => function ($query) {
                    return $query->select('id', 'inv_p_name');
                },
                'tw_meter_infos' => function ($query) {
                    return $query->select('meter_id', 'user_id', 'meternumber', 'undertake_subzone_id');
                },
                'acc_transactions' => function ($query) {
                    return $query->select('id', 'inv_id_fk', 'paidsum',  'vatsum', 'totalpaidsum', 'cashier', 'updated_at')
                        ->where('status', '1');
                },
                'acc_transactions.cashier_info' => function ($query) {
                    return $query->select('id', 'prefix', 'firstname', 'lastname');
                }
            ])
            ->get(['id', 'inv_period_id_fk', 'meter_id_fk', 'inv_no', 'lastmeter', 'currentmeter', 'status', 'acc_trans_id_fk', 'recorder_id', 'updated_at', 'created_at']);


        $type = 'paid_receipt';
        return view('payment.receipt_print', compact('invoicesPaidForPrint',  'type', 'from_blade'));
    }
    public function receipt_print_history($id)
    {
        $receipt_id = $id;

        $invoiceTable = TwInvoiceTemp::where('acc_trans_id_fk', $receipt_id)
            ->with([
                'invoice_period' => function ($query) {
                    return $query->select('id', 'inv_p_name');
                },
                'usermeterinfos' => function ($query) {
                    return $query->select('id', 'user_id', 'meternumber', 'undertake_subzone_id', 'submeter_name');
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

        $invoiceHistoryTable = TwInvoiceHistoty::where('acc_trans_id_fk', $receipt_id)
            ->with([
                'invoice_period' => function ($query) {
                    return $query->select('id', 'inv_p_name');
                },
                'usermeterinfos' => function ($query) {
                    return $query->select('id', 'user_id', 'meternumber', 'undertake_subzone_id');
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
        $orgInfos = Organization::getOrgName(Auth::user()->org_id_fk);

        $inv_by_budgetyear = [];
        if ($request->has('user_info')) {
            $invoiceCtrl = new InvoiceController();

            $invoice_infos = json_decode($invoiceCtrl->get_invoice_and_invoice_history($request->get('user_info'), 'paid', session('db_conn'))->content(), true);

            $inv_by_budgetyear = collect($invoice_infos)->groupBy(function ($invoice_info) {
                return $invoice_info['invoice_period']['budgetyear_id'];
            })->values();
        }

        ManagesTenantConnection::configConnection(session('db_conn'));
        $users = User::with('usermeterinfos')->role('User')
                ->whereHas('usermeterinfos', function($q){
                    return $q->select('user_id');
                })
                ->get(['prefix', 'firstname', 'lastname', 'address', 'id', 'zone_id']);
        
      
        $zones = Zone::where('status', 'active')->get();
        $invoice_period = TwInvoicePeriod::where('status', 'active')->get()->first();

        return view('payment.search', compact('zones', 'invoice_period', 'users', 'inv_by_budgetyear', 'orgInfos'));
    }
    public function remove($receiptId)
    {
        date_default_timezone_set('Asia/Bangkok');

        //เปลี่ยนสถานะของ invoice table
        // receipt_id เป็น 0,
        //ถ้า inv_period_id เท่ากับ invoice period table ที่ status  เท่ากับ active (ปัจจุบัน) ให้
        // - invoice.status เท่ากับ invoice นอกเหนือจากนั้นให้ status เป็น owe
        $currentInvoicePeriod = TwInvoicePeriod::where('status', 'active')->get('id')->first();
        $invoicesTemp = TwInvoiceTemp::where('receipt_id', $receiptId);
        $invoices = $invoicesTemp->get(['inv_period_id', 'id']);
        foreach ($invoices as $invoice) {
            $status = 'invoice';

            if ($invoice->inv_period_id != $currentInvoicePeriod->id) {
                $status = 'owe';
            }
            TwInvoiceTemp::where('id', $invoice->id)->update([
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
        $invoices = TwInvoiceTemp::where('inv_period_id_fk', $inv_period)
            ->with(['usermeterinfos' => function ($query) {
                return $query->select('id', 'undertake_subzone_id', 'meter_address', 'meternumber', 'metertype_id', 'user_id');
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
        TwAccTransactions::where('id', $acc_trans_id_fk)->update([
            'status'        => 2,
            'paidsum'       => 0,
            'vatsum'        => 0,
            'totalpaidsum'  => 0,
            'net'           => 0,
            'updated_at'    => date('Y-m-d H:i:s'),
        ]);
        TwInvoiceTemp::where('acc_trans_id_fk', $acc_trans_id_fk)->update([
            'status'            => 'owe',
            'recorder_id'       => Auth::id(),
            'updated_at'        => date('Y-m-d H:i:s'),
        ]);

        $invCount   = TwInvoiceTemp::where('acc_trans_id_fk', $acc_trans_id_fk)->count();
        $invGet     = TwInvoiceTemp::where('acc_trans_id_fk', $acc_trans_id_fk)->get(['meter_id_fk'])->first();
        $uMeterInfo = TwMeterInfos::where('id', $invGet->meter_id_fk)->get(['owe_count']);
        TwMeterInfos::where('id', $invGet->meter_id_fk)->update([
            'owe_count' => $uMeterInfo[0]->owe_count - $invCount,
            'updated_at'    => date('Y-m-d H:i:s'),
        ]);
        return redirect()->route('payment.search')->with(['color' => 'success', 'message' => 'ทำการยกเลิกใบแจ้งหนี้เลขที่ ' . $acc_trans_id_fk . ' เรียบร้อยแล้ว']);
    }
}
