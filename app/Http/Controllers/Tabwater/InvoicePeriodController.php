<?php

namespace App\Http\Controllers\Tabwater;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\FunctionsController;
use App\Models\Tabwater\AccTransactions;
use App\Models\Admin\BudgetYear;
use App\Models\Tabwater\TwInvoice;
use App\Models\Tabwater\TwInvoiceHistoty;
use App\Models\Tabwater\TwInvoicePeriod;
use App\Models\Tabwater\TwInvoiceTemp;
use App\Models\Tabwater\TwUsersInfo;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvoicePeriodController extends Controller
{
    public function index()
    {

        $funcCtrl = new FunctionsController();

        //1.check ว่ามีปีงบประมาณที่ active ไหม ถ้าไม่มีให้ทำการสร้างปีงบประมาณก่อน
        $budgetyearModel = BudgetYear::where('status', 'active')->get();

        $invoice_periods = TwInvoicePeriod::with('budgetyear')->orderBy('id', 'desc')
            ->where('budgetyear_id', $budgetyearModel[0]->id)
            ->get();

        foreach ($invoice_periods as $invoice_period) {
            $invoice_period->startdate = $funcCtrl->engDateToThaiDateFormat($invoice_period->startdate);
            $invoice_period->enddate = $funcCtrl->engDateToThaiDateFormat($invoice_period->enddate);
        }

        return view('admin.invoice_period.index', compact('invoice_periods'));
    }

    public function create()
    {
        $budgetyear = BudgetYear::where('status', 'active')->first();
        return view('admin.invoice_period.create', compact('budgetyear'));
    }

    public function store(Request $request, TwInvoicePeriod $invoice_period)
    {
        $request->validate([
            'startdate'         => 'required',
            'enddate'           => 'required',
            'inv_period_name'   => 'required',
        ],[
            'required'          =>'ใส่ข้อมูล',
        ]);



        //เปลี่ยน last inv period เป็น inactive
       $last_inv_prd = TwInvoicePeriod::orderBy('id', 'desc')->first();
        
        $check_inv_init_status = TwInvoice::where([
            'inv_period_id_fk' => $last_inv_prd->id,
            'status' => 'init'
        ])->count();
        if($check_inv_init_status > 0){
            return redirect()->route('admin.invoice_period.create')->with(['color' => 'warning', 'message'=> 'มีข้อมูลยังไม่ถูกบันทึก']);
        }

        $last_inv_prd->update([
            'status'    => 'inactive',
            'updated_at'=> date('Y-m-d H:i:s'),
        ]);


        $req = $request->all();
        $funcCtrl = new FunctionsController();
        //เปลี่ยนวันที่ไทยเป็นอังกฤษ
        $req['startdate']   = $funcCtrl->thaiDateToEngDateFormat($request->get('startdate'));
        $req['enddate']     = $funcCtrl->thaiDateToEngDateFormat($request->get('enddate'));
        $req['inv_p_name']  = $request->get('inv_period_name')."-".$request->get("inv_period_name_year");
        $req["status"]      = 'active';

        //สร้าง new inv period
        $current_inv_prd    = $invoice_period->create($req);

        //สร้าง invoice status init ของรอบบิลใหม่
        $idLastInvPeriod = $last_inv_prd->id;

        $user_meter_infos = TwUsersInfo::where('status', 'active')
            ->with([
                // 'invoice' => function ($q) use ($idLastInvPeriod) {
                //     return $q->select('meter_id_fk', 'inv_period_id_fk', 'currentmeter', 'acc_trans_id_fk')
                //         ->where('inv_period_id_fk', $idLastInvPeriod);
                // },
                'invoice_not_paid' => function ($q) {
                    return $q->select('inv_id', 'meter_id_fk', 'inv_period_id_fk', 'status', 'acc_trans_id_fk')
                        ->whereIn('status', ['owe', 'invoice']);
                }
            ])
            ->get(['meter_id', 'user_id','last_meter_recording','inv_no_index']);

        $newInvoiceArray = [];
        $invModel = new TwInvoiceTemp();

        foreach ($user_meter_infos as $user_meter_info) {
            $newInvoiceArray[] = [
                'meter_id_fk'       => $user_meter_info->meter_id,
                'inv_no'            => $user_meter_info->inv_no_index,
                'inv_period_id_fk'  => $current_inv_prd->id,
                'lastmeter'         => $user_meter_info->last_meter_recording,
                'currentmeter'      => 0,
                'status'            => 'init',
                'recorder_id'       => Auth::user()->id,
                'created_at'        => date('Y-m-d H:i:s'),
                'updated_at'        => date('Y-m-d H:i:s'),

            ];
        
										
            if(collect($user_meter_info->invoice_not_paid)->isNotEmpty() ){
                //มียอดค้างชำระเกินรอบบิลปัจจุบัน
                $accTrans = AccTransactions::create([
                    'user_id_fk'    => $user_meter_info->meter_id,
                    'inv_no_fk'     => 0,
                    'paidsum'       => 0,
                    'vatsum'        => 0,
                    'totalpaidsum'  => 0,
                    'net'           => 0,
                    'status'        => 2,//hold
                    'cashier'       =>Auth::user()->id
                ]);
                foreach( $user_meter_info->invoice_not_paid as $owe ){
                    TwInvoiceTemp::where('inv_id', $owe->inv_id)->update([
                        'acc_trans_id_fk'   => $accTrans->id,
                        'updated_at'        => date('Y-m-d H:i:s'),
                    ]);
                }

            }

        }

       return TwInvoiceTemp::insert($newInvoiceArray);

        //เปลี่ยน invoice ที่ inv_period_id_fk รอบบิลที่แล้วให้ สถานะ เป็น Owe
        TwInvoice::where('inv_period_id_fk', $idLastInvPeriod)
            ->where('status', 'invoice')
            ->update(['status'=> 'owe', 'updated_at' => date('Y-m-d H:i:s')]);


        // เอา invoice ที่ status == paid ไปที่ invoice_history table
        $copyInvPaidStatusArr = [];
        // $invoicePaid = TwInvoice::where('inv_period_id_fk', $idLastInvPeriod)->where('status', 'paid')->get();
        // foreach (collect($invoicePaid)->chunk(500) as $k => $vv) {
        //     foreach($vv as $v){
        //         $copyInvPaidStatusArr[] = [
        //             'inv_id'           => $v->inv_id,
        //             'user_id'          => $v->user_id,
        //             'meter_id_fk'      => $v->meter_id_fk,
        //             'inv_period_id_fk' => $v->inv_period_id_fk,
        //             'lastmeter'        => $v->lastmeter,
        //             'currentmeter'     => $v->currentmeter,
        //             'water_used'       => $v->water_used,
        //             'inv_type'         => $v->inv_type,
        //             'paid'             => $v->paid,
        //             'vat'              => $v->vat,
        //             'totalpaid'        => $v->totalpaid,
        //             'status'           => $v->status,
        //             'acc_trans_id_fk'  => $v->acc_trans_id_fk,
        //             'comment'          => $v->comment,
        //             'recorder_id'      => $v->recorder_id,
        //             'created_at'       => $v->created_at,
        //             'updated_at'       => $v->updated_at
        //         ];
        //     }
        // }
        // InvoiceHistoty::insert($copyInvPaidStatusArr);
        // //ทำการลบ invoice status paid ของรอบบิลก่อน ที่ copy ไปที่ invoice_history แล้ว
        // TwInvoice::where('inv_period_id_fk', $idLastInvPeriod)->where('status', 'paid')->delete(); //where('inv_period_id_fk', $idLastInvPeriod)

        // $request->session()->flash('subzone_id', $request->get('subzone_id'));
        return redirect()->route('admin.invoice_period.index')->with( ['message'=>'ทำการบันทึกข้อมูลแล้ว', 'color'=>'success']);
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
        ],[
            'required' =>'ใส่ข้อมูล',
        ]);

        $req = $request->all();
        $funcCtrl = new FunctionsController();
        $req['startdate'] = $funcCtrl->thaiDateToEngDateFormat($request->get('startdate'));
        $req['enddate'] = $funcCtrl->thaiDateToEngDateFormat($request->get('enddate'));
        //สร้าง new inv period
        $invoice_period->update($req);
        return redirect()->route('admin.invoice_period.index')->with('message','ทำการอัพเดทข้อมูลเรียบร้อยแล้ว');

    }

    public function destroy(TwInvoicePeriod $invoice_period)
    {
        if(collect($invoice_period)->isNotEmpty()){
            $check_inv_prd_count = TwInvoicePeriod::all()->count();
            if ($check_inv_prd_count == 1) {
                return redirect()->route('admin.invoice_period.index')->with(['message' => 'ไม่สามารถทำการลบข้อมูลได้ เนื่องจากระบบตั้งค่าให้ต้องมีรอบบิลอย่างน้อย 1 รอบบิล']);
            }
            //check ว่ารอบบิลนี้มีการชำระเงินเกิดขึ้นหรือยัง
            $count_paid_status = TwInvoice::where(['inv_period_id_fk' => $invoice_period->id, 'status'=>'paid'])->count();
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
