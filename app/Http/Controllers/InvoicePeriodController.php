<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\FunctionsController;
use App\Models\BudgetYear;
use App\Models\InvoicePeriod;
use Illuminate\Http\Request;

class InvoicePeriodController extends Controller
{
    public function index()
    {
        $funcCtrl = new FunctionsController();

        //1.check ว่ามีปีงบประมาณที่ active ไหม ถ้าไม่มีให้ทำการสร้างปีงบประมาณก่อน
        $budgetyearModel = BudgetYear::where('status', 'active')->get();

        $invoice_periods = InvoicePeriod::with('budgetyear')
            ->where('status', 'active')->orderBy('startdate', 'desc')
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

    public function create_invoices($id)
    {
        //สร้างใบแจ้งหนี้เริ่มต้นของแต่ละ รอบบิลใหม่
        return $invoice_period = InvoicePeriod::with('budgetyear')->where('id', $id)->get()->first();

        return view('invoice_period.create_invoices', \compact('invoice_period'));
    }

    public function store(Request $request, InvoicePeriod $invoice_period)
    {
        $request->validate([
            'startdate' => 'required',
            'enddate' => 'required',
            'inv_p_name' => 'required',
        ],[
            'required' =>'ใส่ข้อมูล',
        ]);

        //เปลี่ยน last inv period เป็น inactive
        $invoice_period->where('status', 'active')->update([
            'status' => 'inactive',
            'updated_at'=> date('Y-m-d H:i:s'),
        ]);

        $req = $request->all();
        $funcCtrl = new FunctionsController();
        $req['startdate'] = $funcCtrl->thaiDateToEngDateFormat($request->get('startdate'));
        $req['enddate'] = $funcCtrl->thaiDateToEngDateFormat($request->get('enddate'));
        $req['inv_p_name'] = $request->get('inv_period_name')."-".$request->get("inv_period_name_year");
        $req["status"] = 'active';
        //สร้าง new inv period
        $invoice_period->create($req);
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
        $invoice_period->delete();
        return redirect()->route('admin.invoice_period.index')->with(['message' => 'ทำการลบข้อมูลเรียบร้อยแล้ว']);
    }
    public function delete($id)
    {
        // ถ้ารอบบิลนี้มีการใช้ในการบันทึก invoice แล้วแจ้งเตือนก่อนว่าจะต้องการลบไหม
        $delete = InvoicePeriod::find($id);
        $delete->update([
            'status' => 'deleted',
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        return redirect()->route('/invoice_period')->with(['message' => 'ทำการลบข้อมูลเรียบร้อยแล้ว']);
    }
}
