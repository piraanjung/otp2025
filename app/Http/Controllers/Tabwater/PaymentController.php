<?php

namespace App\Http\Controllers\Tabwater;

use App\Http\Controllers\Controller;

use App\Http\Controllers\FunctionsController;
use App\Http\Controllers\Tabwater\InvoiceController;
use App\Models\Admin\BudgetYear;
use App\Models\Tabwater\Account;
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
use App\Models\Tabwater\TwInvoice;

class PaymentController extends Controller
{
  public function index(Request $request)
    {
        // 1. จัดการ Session ของ Subzone (เหมือนเดิม)
        if ($request->session()->has('payment_subzone_selected')) {
            if (collect($request->get('subzone_id_lists'))->isNotEmpty()) {
                $subzone_selected = $request->get('subzone_id_lists');
                $request->session()->forget('payment_subzone_selected');
                $request->session()->put('payment_subzone_selected', collect($subzone_selected)->toJson());
            } else {
                $subzone_selected = json_decode($request->session()->get('payment_subzone_selected'));
            }
        } else {
            $subzone_id_array = collect(Zone::getOrgSubzone('id'))->pluck('id');
            $subzone_selected = $subzone_id_array;
            $request->session()->put('payment_subzone_selected', collect($subzone_selected)->toJson());
        }

        $inv_period_id = $request->get('inv_period_id', 0);

        // 2. Query ข้อมูล (ปรับใหม่ ใช้ tw_invoices และ whereHas)
        $query = TwMeterInfos::query()
            ->whereIn('status', ['active', 'inactive', 'deleted']);

        // กรอง Subzone
        if (collect($subzone_selected)->isNotEmpty()) {
            $query->whereIn('undertake_subzone_id', $subzone_selected);
        }

        // --- จุดเปลี่ยนสำคัญ: ใช้ whereHas เช็คจากตาราง tw_invoice โดยตรง ---
        // (Database จะกรองเฉพาะคนที่มีหนี้มาให้เลย ไม่ต้องทำ Loop Filter เอง)
        $query->whereHas('tw_invoices', function ($q) use ($inv_period_id) {
            $q->whereIn('status', ['owe', 'invoice']); // สถานะที่ค้างชำระ
            if ($inv_period_id != 0) {
                $q->where('inv_period_id_fk', $inv_period_id);
            }
        });

        // Eager Load (ดึงความสัมพันธ์มาเตรียมไว้)
        $invoices = $query->with([
            'user',                // ดึง User เลยป้องกัน N+1
            'undertake_zone',
            'undertake_subzone',
            'tw_invoices' => function ($query) use ($inv_period_id) {
                // เลือกเฉพาะบิลที่ค้างจ่ายมาแสดง
                $query->whereIn('status', ['owe', 'invoice'])
                      ->orderBy('id', 'asc'); // เรียงตามลำดับบิลเก่าไปใหม่

                if ($inv_period_id != 0) {
                    $query->where('inv_period_id_fk', $inv_period_id);
                }
            }
        ])->get();

        // 3. ข้อมูลประกอบอื่นๆ (เหมือนเดิม)
        $subzones = Zone::getOrgSubzone('array');
        $selected_subzone_name_array = [];
        if ($request->has('check-input-select-all')) {
            array_push($selected_subzone_name_array, 'ทุกเส้นทางจดมิเตอร์');
        } else {
            foreach ($subzone_selected as $subzone_id) {
                $subzone = Subzone::find($subzone_id);
                if ($subzone) {
                    $selected_subzone_name_array[] = $subzone->subzone_name;
                }
            }
        }

        // คำนวณยอดรวม (ปรับมาใช้ tw_invoices)
        $total_water_used = $invoices->sum(function ($meter) {
            return $meter->tw_invoices->sum('water_used');
        });

        $current_budgetyear = BudgetYear::where('status', 'active')->with([
            'invoice_period' => function ($q) {
                $q->select('id', 'budgetyear_id', 'inv_p_name')->where('status', '<>', 'deleted');
            }
        ])->get();

        $select_all = count($subzones) == count($subzone_selected);
        $orgInfos = Organization::getOrgName(Auth::user()->org_id_fk);

        return view('payment.index', compact(
            'orgInfos',
            'invoices',
            'subzones',
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
            $v = TwInvoice::where('id', $inv['inv_id'])
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
        'meter_id' => 'required', // ตรวจสอบ meter_id ด้วย
    ]);

    // 1. ดึง ID ของ Invoice ที่เลือกมาทั้งหมด
    $selected_payments = collect($request->get('payments'))->filter(function ($v) {
        return isset($v['on']);
    });

    if ($selected_payments->isEmpty()) {
        return back()->with('error', 'กรุณาเลือกรายการชำระเงิน');
    }

    $invoice_ids = $selected_payments->pluck('iv_id')->toArray();
    $meter_id = $request->meter_id;

    // 2. ดึงข้อมูล Invoice ทั้งหมดในครั้งเดียว (ลด Query)
    $invoices = TwInvoice::whereIn('id', $invoice_ids)
                 ->where('status', '!=', 'paid') // กันพลาดจ่ายซ้ำ
                 ->get();

    if ($invoices->isEmpty()) {
        return back()->with('error', 'ไม่พบรายการที่เลือก หรือถูกชำระไปแล้ว');
    }

    // 3. คำนวณยอดรวมสำหรับ Transaction เดียว
    $sum_vat = $invoices->sum('vat');
    $sum_reserve = $invoices->sum('reserve_meter');
    $sum_paid = $invoices->sum('paid'); // ค่าน้ำ
    $sum_total = $invoices->sum('totalpaid'); // ยอดสุทธิ

    // 4. สร้าง Transaction (ใบเสร็จรับเงิน Header) เพียง 1 รายการ
    $accTrans = new TwAccTransactions();
    // $accTrans->inv_id_fk = ...; // **ไม่ต้องใส่** เพราะ 1 Transaction มีหลาย Inv ให้ไปดูที่ลูกแทน
    $accTrans->vatsum            = $sum_vat;
    $accTrans->reserve_meter_sum = $sum_reserve;
    $accTrans->paidsum           = $sum_paid;
    $accTrans->totalpaidsum      = $sum_total;
    $accTrans->status            = '1';
    $accTrans->cashier           = Auth::id();
    $accTrans->created_at        = now(); // ใช้ now() ของ Laravel
    $accTrans->updated_at        = now();
    $accTrans->save();

    // 5. เตรียมข้อมูล User และ Running Number ใบเสร็จ
    $twUserInfo = TwMeterInfos::find($meter_id);
    $receipt_running_no = $twUserInfo->inv_no_index; // เลขที่ใบเสร็จรับเงิน
    $nextLastmeter = $twUserInfo->last_meter_recording;

    // 6. Loop Update Invoice (Link ไปหา Transaction)
    foreach ($invoices as $inv) {
        $inv->status          = 'paid';
        $inv->acc_trans_id_fk = $accTrans->id; // **Key สำคัญ: ผูกบิลกับ Transaction**
        $inv->inv_no          = $receipt_running_no; // เลขที่ใบเสร็จเดียวกันทั้งชุด
        $inv->updated_at      = now();
        $inv->save();

        // เช็คเลขมิเตอร์ล่าสุด
        if ($nextLastmeter <= $inv->currentmeter) {
            $nextLastmeter = $inv->currentmeter;
        }
    }

    // 7. จัดการ Cutmeter และสถานะ User (Logic เดิมของคุณ แต่ยุบรวม Query)
    $remaining_owe_count = TwInvoice::where('meter_id_fk', $meter_id)
                            ->whereIn('status', ['owe', 'invoice'])
                            ->count();

    $cutmeter = TwCutmeter::where('meter_id_fk', $meter_id)
                ->whereIn('status', ['pending', 'cutmeter'])
                ->latest() // เอาตัวล่าสุด
                ->first();

    if ($cutmeter) {
        // กรณีปลดหนี้หมด และโดนตัดมิเตอร์อยู่ -> เปลี่ยนสถานะเป็นรอติดตั้ง/ผ่าน
        if ($remaining_owe_count == 0 && $twUserInfo->cutmeter == 1) {

            if ($cutmeter->status == 'cutmeter') {
                // Logic เดิม: cutmeter -> passed
                $cutmeter->status = 'passed';
                $cutmeter->save(); // Save สถานะ passed ก่อน (ตาม code เดิม)
            }

            // Update เป็น install/complete
            $cutmeter->update([
                'status'        => ($cutmeter->status == "pending") ? "complete" : "install",
                'owe_count'     => $remaining_owe_count,
                "warning_print" => 0,
                'updated_at'    => now(),
            ]);

            // Update User Info ตามสถานะ Cutmeter
            $is_active = ($cutmeter->status == 'init' || $cutmeter->status == 'complete');
            $twUserInfo->cutmeter = $is_active ? 0 : 1;
            $twUserInfo->status   = $is_active ? 'active' : 'inactive';

        } else {
            // ยังเหลือหนี้ หรือไม่ได้โดนตัด -> อัปเดตแค่ยอดค้าง
            $cutmeter->update(['owe_count' => $remaining_owe_count]);
        }
    }

    // 8. Update User Info สุดท้าย
    $twUserInfo->owe_count            = $remaining_owe_count;
    // Update สถานะตัดมิเตอร์ (ถ้าไม่ได้เข้าเงื่อนไขข้างบน ก็เช็คตามจำนวนบิล)
    // หมายเหตุ: ตรงนี้ต้องระวัง Logic ตีกันกับข้างบน ถ้าข้างบน set active แล้ว ตรงนี้อาจจะทับ
    // แต่ตาม Code เดิมคุณทำแบบนี้ ผมคงไว้ก่อน
    if(!$cutmeter) {
         $twUserInfo->cutmeter = $remaining_owe_count < 2 ? '0' : '1';
    }

    $twUserInfo->last_meter_recording = $nextLastmeter;
    $twUserInfo->inv_no_index         = $receipt_running_no + 1; // รันเลขใบเสร็จถัดไป
    $twUserInfo->updated_at           = now();
    $twUserInfo->save();

    // 9. ส่งไปพิมพ์ใบเสร็จ (ส่ง Transaction ID ไปเลย แม่นยำกว่า)
    return $this->receipt_print_by_trans($accTrans->id);
}

// สร้าง function ใหม่ หรือปรับแก้ receipt_print เดิมให้รับ trans_id
private function receipt_print_by_trans($acc_trans_id)
{
    // ดึง Invoices โดยอ้างอิงจาก Transaction ID เดียว (จะได้บิลทั้งหมดที่เพิ่งจ่าย)
    $invoicesPaidForPrint = TwInvoice::where('acc_trans_id_fk', $acc_trans_id)
        ->with([
            'invoice_period:id,inv_p_name',
            'tw_meter_infos:meter_id,user_id,meternumber,undertake_subzone_id',
            'tw_acc_transactions' => function ($query) {
                // ดึงข้อมูล Transaction (แคชเชียร์, วันที่, ยอดรวม)
                $query->select('id', 'paidsum', 'vatsum', 'totalpaidsum', 'cashier', 'updated_at')
                      ->with('cashier_info:id,prefix,firstname,lastname');
            }
        ])
        ->get();

    $type = 'paid_receipt';
    $from_blade = 'payment.index';

    return view('payment.receipt_print', compact('invoicesPaidForPrint', 'type', 'from_blade'));
}
    public function store_by_inv_no(Request $request)
    {
        $arr = [];

        foreach ($request->get('datas') as $dataArray) {
            list($req_meter_id, $req_inv_no) = explode("|", $dataArray);

            $inv = new TwInvoice();
            //sum paid vat และ totalpaid แล้ว create tw_acc_transactions table
            $inv_infos = TwInvoice::where('meter_id_fk', $req_meter_id)
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
                TwInvoice::where('inv_id', $inv->inv_id)->update([
                    'status' => 'paid',
                    'recorder_id' => Auth::user()->id,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'acc_trans_id_fk' => $acc_trans->id,
                    'inv_no' => $req_inv_no
                ]);
            }


            //update usermeter_info
            // $invStatusOwesCount = TwInvoice::where('meter_id_fk', $req_meter_id)->whereIn('status', ['owe', 'invoice'])->count();

            $usermeter_infos = TwMeterInfos::where('id', $req_meter_id)
                ->get(['owe_count', 'inv_no_index']);

            // $res = $usermeter_infos[0]->owe_count - $invStatusOwesCount < 0 ? 0 : $usermeter_infos[0]->owe_count - $invStatusOwesCount;
            TwMeterInfos::where('id', $req_meter_id)
                ->update([
                    'owe_count' => $usermeter_infos[0]->owe_count - collect($inv_infos)->count(),
                    'inv_no_index' =>  $usermeter_infos[0]->inv_no_index + 1
                ]);



            $invoicesPaidForPrint = TwInvoice::where('acc_trans_id_fk', $acc_trans->id)
                ->with([
                    'invoice_period' => function ($query) {
                        return $query->select('id', 'inv_p_name');
                    },
                    'usermeterinfos' => function ($query) {
                        return $query->select('id', 'user_id', 'meternumber', 'undertake_zone_id', 'undertake_subzone_id', 'meter_address');
                    },
                    'tw_acc_transactions' => function ($query) {
                        return $query->select('id', 'reserve_meter_sum', 'user_id_fk', 'paidsum', 'vatsum', 'totalpaidsum', 'cashier', 'updated_at')
                            ->where('status', 1);
                    },
                    'tw_acc_transactions.cashier_info' => function ($query) {
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
                'tw_acc_transactions' => function ($query) {
                    return $query->select('id', 'user_id_fk', 'paidsum', 'vatsum', 'totalpaidsum', 'cashier', 'updated_at')
                        ->where('status', 1);
                },
                'tw_acc_transactions.cashier_info' => function ($query) {
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
            $invTemp = TwInvoice::where('acc_trans_id_fk', $receipt_id)->get('inv_no')->first();
            $inv_no = $invTemp->inv_no;
        }

        $invoicesPaidForPrint = TwInvoice::where('meter_id_fk', $request->meter_id)
            ->where('inv_no', $inv_no)
            ->with([
                'invoice_period' => function ($query) {
                    return $query->select('id', 'inv_p_name');
                },
                'tw_meter_infos' => function ($query) {
                    return $query->select('meter_id', 'user_id', 'meternumber', 'undertake_subzone_id');
                },
                'tw_acc_transactions' => function ($query) {
                    return $query->select('id', 'inv_id_fk', 'paidsum',  'vatsum', 'totalpaidsum', 'cashier', 'updated_at')
                        ->where('status', '1');
                },
                'tw_acc_transactions.cashier_info' => function ($query) {
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

        $invoiceTable = TwInvoice::where('acc_trans_id_fk', $receipt_id)
            ->with([
                'invoice_period' => function ($query) {
                    return $query->select('id', 'inv_p_name');
                },
                'usermeterinfos' => function ($query) {
                    return $query->select('id', 'user_id', 'meternumber', 'undertake_subzone_id', 'submeter_name');
                },
                'tw_acc_transactions' => function ($query) {
                    return $query->select('id', 'user_id_fk', 'paidsum', 'vatsum', 'totalpaidsum', 'cashier', 'updated_at')
                        ->where('status', 1);
                },
                'tw_acc_transactions.cashier_info' => function ($query) {
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
        // 1. ดึงชื่อองค์กร (อันนี้ OK)
        $orgInfos = Organization::getOrgName(Auth::user()->org_id_fk);

        // 2. ส่วนของ Invoice
        $inv_by_budgetyear = [];
        if ($request->has('user_info')) {
            // หมายเหตุ: การ new Controller แบบนี้ไม่แนะนำ (ควรย้าย Logic ไป Service)
            // แต่ถ้า code เดิมเป็นแบบนี้ ก็ใช้ไปก่อนได้ครับ
            $invoiceCtrl = new InvoiceController();

            // *** ข้อควรระวัง: พารามิเตอร์ session('db_conn') อาจจะไม่จำเป็นแล้ว
            // ถ้าระบบเปลี่ยนมาใช้ org_id_fk แทนการสลับ DB ตรวจสอบ function นี้ด้วยนะครับ
            $invoice_infos = json_decode($invoiceCtrl->get_invoice_and_invoice_history(
                $request->get('user_info'),
                'paid'
            )->content(), true);

            $inv_by_budgetyear = collect($invoice_infos)->groupBy(function ($invoice_info) {
                return $invoice_info['invoice_period']['budgetyear_id'];
            })->values();
        }

        // --- ลบ ManagesTenantConnection ออกได้เลย ---
        // ManagesTenantConnection::configConnection(session('db_conn'));

        // 3. Query User
        // เนื่องจาก User Model ใช้ Trait BelongsToOrganization แล้ว
        // มันจะเติม where('org_id_fk', ...) ให้อัตโนมัติ
        $users = User::with('usermeterinfos')
            ->role('User')
            ->whereHas('usermeterinfos', function ($q) {
                $q->select('user_id');
            })
            ->get(['prefix', 'firstname', 'lastname', 'address', 'id', 'zone_id']);
            // Note: ตรวจสอบว่าตาราง users มี org_id_fk ใช่ไหม ถ้าใช่ก็ผ่านครับ

        // 4. Query Zone
        // ใช้ Trait แล้ว จะได้เฉพาะ Zone ของ Org นี้
        $zones = Zone::where('status', 'active')->get();

        // 5. Query Invoice Period
        // ใช้ Trait แล้ว จะได้เฉพาะ Period ของ Org นี้
        $invoice_period = TwInvoicePeriod::where('status', 'active')->first(); // ใช้ first() แทน get()->first() ประหยัด query

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
        $invoicesTemp = TwInvoice::where('receipt_id', $receiptId);
        $invoices = $invoicesTemp->get(['inv_period_id', 'id']);
        foreach ($invoices as $invoice) {
            $status = 'invoice';

            if ($invoice->inv_period_id != $currentInvoicePeriod->id) {
                $status = 'owe';
            }
            TwInvoice::where('id', $invoice->id)->update([
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
        // 1. เปลี่ยนจาก TwInvoice เป็น TwInvoice
        // 2. เปลี่ยนชื่อคอลัมน์ inv_period_id_fk เป็น inv_period_id (ปกติ TwInvoice ใช้ชื่อนี้)
        // 3. ใช้ whereHas เพื่อกรอง Subzone ตั้งแต่ใน Database (เร็วกว่าดึงมาทั้งหมดแล้ววน Loop filter)
        $invoices_paid = TwInvoice::where('inv_period_id_fk', $inv_period)
            ->where('status', 'paid')
            ->whereHas('tw_meter_infos', function ($q) use ($subzone_id) {
                // กรองเฉพาะ Subzone ที่ต้องการ
                $q->where('undertake_subzone_id', $subzone_id);
            })
            ->with(['tw_meter_infos' => function ($query) {
                // เลือก meter_id มาด้วยเสมอ เพื่อให้ Relation จับคู่กันติด
                $query->select('meter_id', 'undertake_subzone_id', 'meter_address', 'meternumber', 'metertype_id', 'user_id');
            }])
            ->get(); // ดึงข้อมูล

        // จัดเรียงตาม user_id (ทำใน PHP collection)
        $invoices_paid = $invoices_paid->sortBy(function ($invoice) {
            return $invoice->tw_meter_infos->user_id ?? 0;
        });

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
        TwInvoice::where('acc_trans_id_fk', $acc_trans_id_fk)->update([
            'status'            => 'owe',
            'recorder_id'       => Auth::id(),
            'updated_at'        => date('Y-m-d H:i:s'),
        ]);

        $invCount   = TwInvoice::where('acc_trans_id_fk', $acc_trans_id_fk)->count();
        $invGet     = TwInvoice::where('acc_trans_id_fk', $acc_trans_id_fk)->get(['meter_id_fk'])->first();
        $uMeterInfo = TwMeterInfos::where('id', $invGet->meter_id_fk)->get(['owe_count']);
        TwMeterInfos::where('id', $invGet->meter_id_fk)->update([
            'owe_count' => $uMeterInfo[0]->owe_count - $invCount,
            'updated_at'    => date('Y-m-d H:i:s'),
        ]);
        return redirect()->route('payment.search')->with(['color' => 'success', 'message' => 'ทำการยกเลิกใบแจ้งหนี้เลขที่ ' . $acc_trans_id_fk . ' เรียบร้อยแล้ว']);
    }
}
