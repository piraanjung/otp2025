<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\FunctionsController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\OwepaperController;
use App\Models\Account;
use App\Models\Invoice;
use App\Models\InvoicePeriod;
use App\Models\Subzone;
use App\Models\User;
use App\Models\UserMerterInfo;
use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Api\PaymentController as ApiPaymentController;
use App\Models\AccTransactions;
use App\Models\Setting;
use App\Models\InvoiceHistoty;
use Termwind\Components\Raw;

class PaymentController extends Controller
{
    public function index(REQUEST $request)
    {
    
        $invoice_period = InvoicePeriod::where('status', 'active')->get()->first();

        $invoices_sql= Invoice::where('inv_period_id_fk', $invoice_period->id)
        ->whereIn('status', ['owe', 'invoice'])
        ->with(['usermeterinfos'=> function ($query) {
            $query->select('meter_id', 'undertake_subzone_id','undertake_subzone_id', 'undertake_zone_id', 'user_id', 'metertype_id','meternumber', 'owe_count');
        },'usermeterinfos.meter_type'=> function ($query){
            $query->select('id', 'price_per_unit');
        }
        ])->get();
        $invoices = collect($invoices_sql)->sortBy('usermeterinfos.user_id');
        foreach($invoices as $invoice){
            $invoice->meternumberStr = FunctionsController::createInvoiceNumberString($invoice->meter_id_fk);
        }
        // return $invoices;
        $subzones = collect(Subzone::all())->sortBy('zone_id');
        $page = 'index';

        return view('payment.index', compact('invoices', 'subzones','page'));
    }
    public function index_search_by_suzone(Request $request){
        $invoice_period = InvoicePeriod::where('status', 'active')->get()->first();
        $subzones = Subzone::all();
        $subzone_search_lists = $request->get('subzone_id_lists');
        if(collect($request->get('subzone_id_lists'))->isEmpty()){
            $subzone_search_lists = collect($subzones)->pluck('id')->toArray();
        }
        $invoices_sql= Invoice::where('inv_period_id_fk', $invoice_period->id)
        ->where('status', 'invoice')
        ->with(['usermeterinfos'=> function ($query) use ($subzone_search_lists) {
            $query->select('meter_id', 'undertake_subzone_id','undertake_zone_id', 'user_id', 'metertype_id','meternumber', 'owe_count')
            ->whereIn('undertake_subzone_id', $subzone_search_lists);
        },'usermeterinfos.meter_type'=> function ($query){
            $query->select('id', 'price_per_unit');
        }
        ])->get();
        $invoices = collect($invoices_sql)->filter(function ($invoice) {
            return collect($invoice->usermeterinfos)->count() > 0;
        })->sortBy('usermeterinfos.user_id');
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
        $accTrans = AccTransactions::create([
            'user_id_fk'    => $request->get('user_id'),
            'paidsum'       => $request->get('paidsum'),
            'vatsum'        => $request->get('vat7'),
            'totalpaidsum'  => $request->get('mustpaid'),
            'status'        => 1,
            'cashier'       => Auth::id()
        ]);

        $accounts = Account::where('payee', $request->get('user_id'))->first();
        if(collect($accounts)->isEmpty()){
            Account::create([
                'deposit'    =>$request->get('mustpaid'),
                'payee'      => $request->get('user_id'),
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s"),
            ]);
        }else{
            Account::where('payee', $request->get('user_id'))->update([
                'deposit' => $accounts->deposit + $request->get('mustpaid'),
                'payee'   => $request->get('user_id'),
                'updated_at' => date("Y-m-d H:i:s"),
            ]);
        }

        //ทำการ update user_meter_infos table โดยการลบ owe_count
        $query = UserMerterInfo::where('meter_id', $request->get('meter_id'));
        $user_meter_owe_count = $query->get('owe_count')->first();
        if ($user_meter_owe_count->owe_count >  0) {
            $payments_owe_status_count = collect($payments)->filter(function ($v) {
                return $v['status'] == 'owe';
            })->count();
            $remain_owe_count = $user_meter_owe_count->owe_count - $payments_owe_status_count;
            $query->update([
                'owe_count' => $remain_owe_count,
                'updated_at' => date("Y-m-d H:i:s"),
            ]);

            //เหลือการจัดกการตาราง cutmeter
        }

        foreach ($payments as $payment) {
            Invoice::where('inv_id', $payment['iv_id'])->update([
                'status'          => 'paid',
                'accounts_id_fk'  => $accTrans->id,
                'updated_at' => date("Y-m-d H:i:s"),
            ]);
        }

        return redirect()->route('payment.receipt_print')->with(['receipt_id' => $accTrans->id]);
    }

    public function receipt_print(REQUEST $request, $receiptId = 0, $from_blade = 'payment.index')
    {
        $receipt_id = $receiptId;
        if($request->session()->has('receipt_id')){
            $receipt_id = $request->session()->get('receipt_id');
        }
        $invoicesPaidForPrint = Invoice::where('accounts_id_fk', $receipt_id)
            ->with([
                'invoice_period' => function ($query) {
                    return $query->select('id', 'inv_p_name');
                },
                'usermeterinfos' => function ($query) {
                    return $query->select('meter_id', 'user_id', 'meternumber', 'undertake_subzone_id');
                },
                'acc_transactions' => function ($query) {
                    return $query->select('id','user_id_fk', 'paidsum','vatsum', 'totalpaidsum', 'cashier', 'updated_at')
                            ->where('status', 1);
                },
            ])
            ->get(['inv_period_id_fk', 'meter_id_fk', 'lastmeter', 'currentmeter', 'status', 'accounts_id_fk', 'recorder_id','updated_at', 'created_at']);

        $newId = FunctionsController::createInvoiceNumberString($receipt_id);
        $type = 'paid_receipt';
        return view('payment.receipt_print', compact('invoicesPaidForPrint', 'newId', 'type', 'from_blade'));
    }
    public function receipt_print_history($id){
        $receipt_id = $id;

        $invoicesPaidForPrint = Invoice::where('accounts_id_fk', $receipt_id)
            ->with([
                'invoice_period' => function ($query) {
                    return $query->select('id', 'inv_p_name');
                },
                'usermeterinfos' => function ($query) {
                    return $query->select('meter_id', 'user_id', 'meternumber', 'undertake_subzone_id');
                },
                'accounting' => function ($query) {
                    return $query->select('id', 'deposit', 'payee', 'updated_at');
                },
            ])
            ->get(['inv_period_id_fk', 'meter_id_fk', 'lastmeter', 'currentmeter', 'status', 'accounts_id_fk', 'recorder_id','updated_at', 'created_at']);

        $newId = FunctionsController::createInvoiceNumberString($receipt_id);
        $type = 'payment_search
        ';
        return view('payment.receipt_print', compact('invoicesPaidForPrint', 'newId', 'type'));
    }
    public function search(Request $request)
    {
        $inv_by_budgetyear = [];
        if($request->has('user_info')){
            $invoiceApi = new InvoiceController();
            $invoice_infos = json_decode($invoiceApi->get_user_invoice($request->get('user_info'))->content(), true);

            $inv_by_budgetyear = collect($invoice_infos)->groupBy(function ($invoice_info) {
                return $invoice_info['invoice_period']['budgetyear_id'];
            })->values();
        }
        $users = User::
        with('usermeterinfos')->where('role_id', 3)->get(['firstname', 'lastname', 'address', 'id', 'zone_id']);

        $zones = Zone::all();
        $invoice_period = InvoicePeriod::where('status', 'active')->get()->first();

        return view('payment.search', compact('zones', 'invoice_period', 'users', 'inv_by_budgetyear'));
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
                        ->with(['usermeterinfos' => function($query){
                            return $query->select('meter_id','undertake_subzone_id', 'meternumber', 'metertype_id', 'user_id');
                        }])
                        ->where('status', 'paid')->get();

        $invoices_paid = collect($invoices)->filter(function($v)use ($subzone_id){
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

    public function destroy(Invoice $invoice){
        return $invoice;
    }


}
