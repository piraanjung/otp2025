<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\FunctionsController;
use App\Http\Controllers\Api\OwepaperController;
use App\Models\Account;
use App\Models\Invoice;
use App\Models\InvoicePeriod;
use App\Models\Subzone;
use App\Models\UserMerterInfo;
use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Api\PaymentController as ApiPaymentController;

class PaymentController extends Controller
{
    public function autocompleteSearch(Request $request)
    {
        $currentInvoicePeriod = InvoicePeriod::where('status', 'active')->get();
        $query =  $request->get('query');
        $filterResult  = DB::table('user_meter_infos as umf')
        ->join('zone as z', 'umf.undertake_zone_id', '=', 'z.id')
        ->join('user_profile as uf', 'uf.user_id', '=', 'umf.user_id')
        ->join('invoice as inv', 'inv.user_id', '=', 'umf.user_id')
        ->select(DB::RAW('CONCAT(uf.address," ", z.zone_name, " - ",uf.name," - ",umf.meternumber) as aa'))
        ->where('inv.deleted', '=', 0)
        ->get();
          $userArray = collect($filterResult)->filter(function($v) use ($query){
              return str_contains($v->aa, $query);
          })
          ->pluck('aa');
          return response()->json($userArray);
    }
    public function index(REQUEST $request)
    {
        $invoice_period = InvoicePeriod::where('status', 'active')->get()->first();

        $invoices_sql= Invoice::where('inv_period_id_fk', $invoice_period->id)
        ->where('status', 'invoice')
        ->with(['usermeterinfos'=> function ($query) {
            $query->select('meter_id', 'undertake_subzone_id','undertake_subzone_id', 'undertake_zone_id', 'user_id', 'metertype_id','meternumber', 'owe_count');
        },'usermeterinfos.meter_type'=> function ($query){
            $query->select('id', 'price_per_unit');
        }
        ])->get();
        $invoices = collect($invoices_sql)->sortBy('usermeterinfos.user_id');

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

        //ทำการ update user_meter_infos table โดยการลบ owe_count
        $query = UserMerterInfo::where('meter_id', $request->get('meter_id'));
        $user_meter_owe_count = $query->first('owe_count');
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

        $receipt = Account::create([
            'deposit' => $request->get('mustpaid'),
            'payee'   => Auth::id(),
        ]);

        foreach ($payments as $payment) {
            Invoice::where('id', $payment['iv_id'])->update([
                'status'          => 'paid',
                'accounts_id_fk'  => $receipt->id,
                'updated_at' => date("Y-m-d H:i:s"),
            ]);
        }

        return redirect()->route('payment.receipt_print')->with(['receipt_id' => $receipt->id]);
    }

    public function receipt_print(REQUEST $request, $receiptId = 0)
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
                'accounting' => function ($query) {
                    return $query->select('id', 'deposit', 'payee', 'updated_at');
                },
            ])
            ->get(['inv_period_id_fk', 'meter_id_fk', 'lastmeter', 'currentmeter', 'status', 'accounts_id_fk', 'recorder_id','updated_at', 'created_at']);

        $newId = FunctionsController::createInvoiceNumberString($receipt_id);
        $type = 'paid_receipt';
        return view('payment.receipt_print', compact('invoicesPaidForPrint', 'newId', 'type'));
    }

    public function print_payment_history($receipt_id)
    {
        return $this->testPrint($receipt_id);
    }

    public function search()
    {
        $apiOwepaper = new OwepaperController();
        $zones = Zone::all();
        $invoice_period = InvoicePeriod::where('status', 'active')->get()->first();

        return view('payment.search', compact('zones', 'invoice_period'));
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
        $sql = DB::table('user_meter_infos as umf')
            ->join('invoice as iv', 'iv.user_id', '=', 'umf.user_id')
        // ->join('invoice as iv', 'iv.meter_id', '=', 'umf.id')
            ->join('user_profile as upf', 'upf.user_id', '=', 'umf.user_id')
            ->join('zone as z', 'z.id', '=', 'umf.undertake_zone_id')
            ->join('subzone as sz', 'sz.id', '=', 'umf.undertake_subzone_id')
            ->where('iv.inv_period_id', '=', $inv_period)
            ->where('iv.status', '=', 'paid')
            ->where('umf.undertake_zone_id', '=', $subzone_id);

        $paid = $sql->get([
            'umf.user_id', 'umf.meternumber', 'umf.undertake_subzone_id', 'umf.undertake_zone_id',
            'upf.name', 'upf.address', 'iv.lastmeter', 'iv.currentmeter', 'iv.printed_time', 'iv.id',
            'upf.zone_id as user_zone_id', 'iv.comment',
            DB::raw('iv.currentmeter - iv.lastmeter as meter_net'),
            DB::raw('(iv.currentmeter - iv.lastmeter)*8 as total'),
        ]);
        $zoneInfoSql = $sql->get([
            'z.zone_name as undertake_zone', 'z.id as undertake_zone_id',
            'sz.subzone_name as undertake_subzone', 'sz.id as undertake_subzone_id',
        ]);
        $zoneInfo = collect($zoneInfoSql)->take(1);
        foreach ($paid as $iv) {
            $funcCtrl = new FunctionsController();
            $iv->user_id_string = $funcCtrl->createInvoiceNumberString($iv->user_id);
        }

        $presentInvoicePeriod = InvoicePeriod::where('id', $inv_period)->get('inv_period_name')[0];

        $memberHasInvoice = collect($paid)->sortBy('user_id');

        return view('payment.paymenthistory', compact('presentInvoicePeriod', 'zoneInfo', 'memberHasInvoice'));
    }

    public function receipted_list($user_id)
    {
        $apiPaymentCtrl = new ApiPaymentController;
        $receipted_list = $apiPaymentCtrl->history($user_id, 'receipt_history');
        return view('payment.receipted_list', compact('receipted_list'));
    }


    public function testDuplicateInvoice()
    {
        $apiPaymentCtrl = new ApiPaymentController();

        $invoicesPaidForPrint = $apiPaymentCtrl->history(23707, 'receipt');
        $newId = 999;
        $type = 'paid_receipt';
        $cashier = DB::table('user_profile')
            ->where('user_id', '=', Auth::id())
            ->select('name')
            ->get();
        $cashiername = $cashier[0]->name;

        return view('payment.receipt_print', compact('invoicesPaidForPrint', 'newId', 'type', 'cashiername'));

    }

}
