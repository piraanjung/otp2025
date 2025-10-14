<?php

namespace App\Http\Controllers\Tabwater;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\FunctionsController;
use App\Models\Admin\ManagesTenantConnection;
use App\Models\Tabwater\AccTransactions;
use App\Models\Admin\BudgetYear;
use App\Models\Admin\Organization;
use App\Models\Tabwater\TwAccTransactions;
use App\Models\Tabwater\TwInvoice;
use App\Models\Tabwater\TwInvoicePeriod;
use App\Models\Tabwater\TwInvoiceTemp;
use App\Models\Tabwater\TwUsersInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvoicePeriodController extends Controller
{
    public function index()
    {

        $funcCtrl = new FunctionsController();

        //1.check ว่ามีปีงบประมาณที่ active ไหม ถ้าไม่มีให้ทำการสร้างปีงบประมาณก่อน
        $budgetyearModel = (new BudgetYear())->setConnection(session('db_conn'))->where('status', 'active')->get();


        ManagesTenantConnection::configConnection(session('db_conn'));
        $invoice_periods = (new TwInvoicePeriod())::with('budgetyear')->orderBy('id', 'desc')
            ->where('budgetyear_id', $budgetyearModel[0]->id)
            ->get();

        foreach ($invoice_periods as $invoice_period) {
            $invoice_period->startdate = $funcCtrl->engDateToThaiDateFormat($invoice_period->startdate);
            $invoice_period->enddate = $funcCtrl->engDateToThaiDateFormat($invoice_period->enddate);
        }
        $orgInfos = Organization::getOrgName(Auth::user()->org_id_fk);

        return view('admin.invoice_period.index', compact('invoice_periods', 'orgInfos'));
    }

    public function create()
    {
        $orgInfos = Organization::getOrgName(Auth::user()->org_id_fk);

        $budgetyear = (new BudgetYear())->setConnection(session('db_conn'))->where('status', 'active')->first();
        return view('admin.invoice_period.create', compact('budgetyear', 'orgInfos'));
    }

    public function store(Request $request, TwInvoicePeriod $invoice_period)
    {
        $request->validate([
            'startdate'         => 'required',
            'enddate'           => 'required',
            'inv_period_name'   => 'required',
        ], [
            'required'          => 'ใส่ข้อมูล',
        ]);



        //เปลี่ยน last inv period เป็น inactive
        $last_inv_prd = (new TwInvoicePeriod())->setConnection(session('db_conn'))->orderBy('id', 'desc')->first();

        $check_inv_init_status = (new TwInvoice())->setConnection(session('db_conn'))->where([
            'inv_period_id_fk' => collect($last_inv_prd)->isNotEmpty() ? $last_inv_prd->id : 0,
            'status' => 'init'
        ])->count();
        if ($check_inv_init_status > 0) {
            return redirect()->route('admin.invoice_period.create')->with(['color' => 'warning', 'message' => 'มีข้อมูลยังไม่ถูกบันทึก']);
        }

        if (collect($last_inv_prd)->isNotEmpty()) {
            $last_inv_prd->update([
                'status'    => 'inactive',
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            //เปลี่ยน invoice ที่ inv_period_id_fk รอบบิลที่แล้วให้ สถานะ เป็น Owe
            (new TwInvoice())->setConnection(session('db_conn'))->where('inv_period_id_fk', $last_inv_prd->id)
                ->where('status', 'invoice')
                ->update(['status' => 'owe', 'updated_at' => date('Y-m-d H:i:s')]);
        }


        $req = $request->all();
        $funcCtrl = new FunctionsController();
        //เปลี่ยนวันที่ไทยเป็นอังกฤษ
        $req['startdate']   = $funcCtrl->thaiDateToEngDateFormat($request->get('startdate'));
        $req['enddate']     = $funcCtrl->thaiDateToEngDateFormat($request->get('enddate'));
        $req['inv_p_name']  = $request->get('inv_period_name') . "-" . $request->get("inv_period_name_year");
        $req["status"]      = 'active';
        $req['deleted']     = '0';

        //สร้าง new inv period
        $current_inv_prd    = (new TwInvoicePeriod())->setConnection(session('db_conn'))->create($req);

        ManagesTenantConnection::configConnection(session('db_conn'));
        $user_meter_infos = (new TwUsersInfo())->setConnection(session('db_conn'))->where('status', 'active')
            ->with([
                'invoice_not_paid' => function ($q) {
                    return $q->select('id', 'meter_id_fk', 'inv_period_id_fk', 'status', 'acc_trans_id_fk')
                        ->whereIn('status', ['owe', 'invoice']);
                }
            ])->limit(10)
            ->get(['id', 'user_id', 'last_meter_recording', 'inv_no_index']);

        $newInvoiceArray = [];

        foreach ($user_meter_infos as $user_meter_info) {
            $newInvoiceArray[] = [
                'meter_id_fk'       => $user_meter_info->id,
                'inv_no'            => $user_meter_info->inv_no_index,
                'inv_period_id_fk'  => $current_inv_prd->id,
                'lastmeter'         => $user_meter_info->last_meter_recording,
                'currentmeter'      => 0,
                'status'            => 'init',
                'recorder_id'       => Auth::user()->id,
                'created_at'        => date('Y-m-d H:i:s'),
                'updated_at'        => date('Y-m-d H:i:s'),

            ];


            if (collect($user_meter_info->invoice_not_paid)->isNotEmpty()) {
                //มียอดค้างชำระเกินรอบบิลปัจจุบัน
                $accTrans = (new TwAccTransactions())->setConnection(session('db_conn'))->create([
                    'user_id_fk'    => $user_meter_info->meter_id,
                    'inv_no_fk'     => 0,
                    'paidsum'       => 0,
                    'vatsum'        => 0,
                    'totalpaidsum'  => 0,
                    'net'           => 0,
                    'cashier'       => Auth::user()->id
                ]);
                foreach ($user_meter_info->invoice_not_paid as $owe) {
                    (new TwInvoiceTemp())->setConnection(session('db_conn'))->where('inv_id', $owe->inv_id)->update([
                        'acc_trans_id_fk'   => $accTrans->id,
                        'updated_at'        => date('Y-m-d H:i:s'),
                    ]);
                }
            }
        }

        (new TwInvoiceTemp())->setConnection(session('db_conn'))->insert($newInvoiceArray);


        return redirect()->route('admin.invoice_period.index')->with(['message' => 'ทำการบันทึกข้อมูลแล้ว', 'color' => 'success']);
    }
    public function edit(TwInvoicePeriod $invoice_period)
    {
        $funcCtrl = new FunctionsController();

        $invoice_period['startdate'] = $funcCtrl->engDateToThaiDateFormat($invoice_period->startdate);
        $invoice_period['enddate'] = $funcCtrl->engDateToThaiDateFormat($invoice_period->enddate);

        return view('admin.invoice_period.edit', compact('invoice_period'));
    }

    public function update(Request $request, TwInvoicePeriod $invoice_period)
    {
        date_default_timezone_set('Asia/Bangkok');

        $request->validate([
            'startdate' => 'required',
            'enddate' => 'required',
            'inv_p_name' => 'required',
        ], [
            'required' => 'ใส่ข้อมูล',
        ]);

        $req = $request->all();
        $funcCtrl = new FunctionsController();
        $req['startdate'] = $funcCtrl->thaiDateToEngDateFormat($request->get('startdate'));
        $req['enddate'] = $funcCtrl->thaiDateToEngDateFormat($request->get('enddate'));
        //สร้าง new inv period
        $invoice_period->update($req);
        return redirect()->route('admin.invoice_period.index')->with('message', 'ทำการอัพเดทข้อมูลเรียบร้อยแล้ว');
    }

    public function destroy(TwInvoicePeriod $invoice_period)
    {
        if (collect($invoice_period)->isNotEmpty()) {
            $check_inv_prd_count = (new TwInvoicePeriod())->setConnection(session('db_conn'))->all()->count();
            if ($check_inv_prd_count == 1) {
                return redirect()->route('admin.invoice_period.index')->with(['message' => 'ไม่สามารถทำการลบข้อมูลได้ เนื่องจากระบบตั้งค่าให้ต้องมีรอบบิลอย่างน้อย 1 รอบบิล']);
            }
            //check ว่ารอบบิลนี้มีการชำระเงินเกิดขึ้นหรือยัง
            $count_paid_status = (new TwInvoice())->setConnection(session('db_conn'))->where(['inv_period_id_fk' => $invoice_period->id, 'status' => 'paid'])->count();
            if ($count_paid_status > 0) {
                return redirect()->route('admin.invoice_period.index')->with([
                    'message' => 'ไม่สามารถทำการลบข้อมูลได้ เนื่องจากมีการชำระเงินในรอบบิลนี้แล้ว โปรดติดต่อ Super Addin'
                ]);
            }
        }

        $invoice_period->delete();

        // FunctionsController::reset_auto_increment_when_deleted('invoice_period');
        return redirect()->route('admin.invoice_period.index')->with(['message' => 'ทำการลบข้อมูลเรียบร้อยแล้ว']);
    }
}
