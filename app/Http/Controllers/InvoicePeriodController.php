<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\FunctionsController;
use App\Models\BudgetYear;
use App\Models\Invoice;
use App\Models\InvoicePeriod;
use App\Models\UserMerterInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvoicePeriodController extends Controller
{
    public function index()
    {
        $funcCtrl = new FunctionsController();

        //1.check ว่ามีปีงบประมาณที่ active ไหม ถ้าไม่มีให้ทำการสร้างปีงบประมาณก่อน
        $budgetyearModel = BudgetYear::where('status', 'active')->get();

        $invoice_periods = InvoicePeriod::with('budgetyear')->orderBy('id', 'desc')
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

    public function store(Request $request, InvoicePeriod $invoice_period)
    {
        $request->validate([
            'startdate' => 'required',
            'enddate' => 'required',
            'inv_period_name' => 'required',
        ],[
            'required' =>'ใส่ข้อมูล',
        ]);

        //เปลี่ยน last inv period เป็น inactive
        $last_inv_prd = InvoicePeriod::orderBy('id', 'desc')->first();
        $last_inv_prd->update([
            'status' => 'inactive',
            'updated_at'=> date('Y-m-d H:i:s'),
        ]);


        $req = $request->all();
        $funcCtrl = new FunctionsController();
        //เปลี่ยนวันที่ไทยเป็นอังกฤษ
        $req['startdate'] = $funcCtrl->thaiDateToEngDateFormat($request->get('startdate'));
        $req['enddate'] = $funcCtrl->thaiDateToEngDateFormat($request->get('enddate'));
        $req['inv_p_name'] = $request->get('inv_period_name')."-".$request->get("inv_period_name_year");
        $req["status"] = 'active';
        //สร้าง new inv period
        $current_inv_prd = $invoice_period->create($req);

        //สร้าง invoice status init ของรอบบิลใหม่
        $user_meter_infos = UserMerterInfo::where('status', 'active')->get();
        foreach ($user_meter_infos as $user_meter_info) {
            $invoice_prev_inv_prd = Invoice::where(['meter_id_fk'=> $user_meter_info->meter_id, 'inv_period_id_fk'=> $last_inv_prd->id])->first();
            $invoice = new Invoice;
                $invoice ->meter_id_fk       = $user_meter_info->meter_id;
                $invoice ->inv_period_id_fk  = $current_inv_prd->id;
                $invoice ->lastmeter         = collect($invoice_prev_inv_prd)->isEmpty() ? 0 : $invoice_prev_inv_prd->currentmeter;
                $invoice ->currentmeter      = 0;
                $invoice ->status            = "init";
                $invoice ->recorder_id       = Auth::user()->id;
                $invoice ->save();

        }

        return redirect()->route('admin.invoice_period.index')->with('message','ทำการบันทึกข้อมูลแล้ว');
    }
    public function edit(InvoicePeriod $invoice_period)
    {
        $funcCtrl = new FunctionsController();

        $invoice_period['startdate'] = $funcCtrl->engDateToThaiDateFormat($invoice_period->startdate);
        $invoice_period['enddate'] = $funcCtrl->engDateToThaiDateFormat($invoice_period->enddate);

        return view('admin.invoice_period.edit', compact('invoice_period'));
    }

    public function update(Request $request, InvoicePeriod $invoice_period)
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

    public function destroy(InvoicePeriod $invoice_period)
    {
        if($invoice_period != 0){
            $check_inv_prd_count = InvoicePeriod::all()->count();
            if ($check_inv_prd_count == 1) {
                return redirect()->route('admin.invoice_period.index')->with(['message' => 'ไม่สามารถทำการลบข้อมูลได้ เนื่องจากระบบตั้งค่าให้ต้องมีรอบบิลอย่างน้อย 1 รอบบิล']);
            }
            //check ว่ารอบบิลนี้มีการชำระเงินเกิดขึ้นหรือยัง
            $count_paid_status = Invoice::where(['inv_period_id_fk' => $invoice_period->id, 'status'=>'paid'])->count();
            if ($count_paid_status > 0) {
                return redirect()->route('admin.invoice_period.index')->with([
                    'message' => 'ไม่สามารถทำการลบข้อมูลได้ เนื่องจากมีการชำระเงินในรอบบิลนี้แล้ว โปรดติดต่อ Super Addin'
                ]);
            }
        }

        $invoice_period->delete();

        FunctionsController::reset_auto_increment_when_deleted('invoice_period');
        return redirect()->route('admin.invoice_period.index')->with(['message' => 'ทำการลบข้อมูลเรียบร้อยแล้ว']);
    }

}
