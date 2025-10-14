<?php

namespace App\Http\Controllers\Tabwater;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\FunctionsController;
use App\Models\Admin\BudgetYear;
use App\Models\Admin\Organization;
use App\Models\Tabwater\InvoicePeriod;
use App\Models\Tabwater\TwInvoicePeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BudgetYearController extends Controller
{
    public function index()
    {
        $funcCtrl = new FunctionsController();

        $budgetyears = (new BudgetYear())->setConnection(session('db_conn'))->orderBy('budgetyear_name', 'desc')->get();
        foreach ($budgetyears as $budgetyear) {
            $budgetyear->startdate = $funcCtrl->engDateToThaiDateFormat($budgetyear->startdate);
            $budgetyear->enddate = $funcCtrl->engDateToThaiDateFormat($budgetyear->enddate);
            $budgetyear->have_inv_peroid = (new TwInvoicePeriod())->setConnection(session('db_conn'))->where('budgetyear_id', $budgetyear->id)->count();
        }
            $orgInfos = Organization::getOrgName(Auth::user()->org_id_fk);

        return view('admin.budgetyear.index', \compact('budgetyears', 'orgInfos'));
    }

    public function create()
    {
        $orgInfos = Organization::getOrgName(Auth::user()->org_id_fk);
        return view('admin.budgetyear.create', compact('orgInfos'));
    }

    public function store(Request $request, BudgetYear $budgetYear)
    {
        $request->validate([
            'budgetyear' => 'required|integer|in:2568,2569,2570,2571,2572,2573',
            'start'  => 'required',
            'end'  => 'required',
        ],[
            'required' => 'ใส่ข้อมูล',
            'integer' => 'ต้องเป็นตัวเลขปีปฏิทิน 4 ตัว',
            'in'=> 'ต้องมากกว่าปี 2566'
        ]);

        date_default_timezone_set('Asia/Bangkok');

        //inactive last budgetyear
        (new BudgetYear())->setConnection(session('db_conn'))->where('status', 'active')->update([
            'status'=> 'inactive'
        ]);
        //รอสร้าง update invoice period table status active ของ  last budgetyear ให้เป็น  invactive
        // create new budgetyear
        $funcCtrl = new FunctionsController();
        (new BudgetYear())->setConnection(session('db_conn'))->create([
                "budgetyear_name" => $request->get('budgetyear'),
                "startdate" => $funcCtrl->thaiDateToEngDateFormat($request->get('start')),
                "enddate" => $funcCtrl->thaiDateToEngDateFormat($request->get('end')),
                "status" => 'active',
        ]);

        return redirect()->route('admin.budgetyear.index')->with('success','บันทึกข้อมูลเรียบร้อย');

    }

    public function edit($id)
    {
        $budgetyear = (new BudgetYear())->setConnection(session('db_conn'))->find($id);
        $funcCtrl = new FunctionsController();

        $budgetyear->startdate = $funcCtrl->engDateToThaiDateFormat($budgetyear->startdate);
        $budgetyear->enddate = $funcCtrl->engDateToThaiDateFormat($budgetyear->enddate);

        return view('admin.budgetyear.edit', compact('budgetyear'));
    }

    public function delete($id)
    {
        $budgetyear = (new BudgetYear())->setConnection(session('db_conn'))->find($id);

        $budgetyear->delete();
        // FunctionsController::reset_auto_increment_when_deleted('invoice_period');

        return view('budgetyear.edit', compact('budgetyear'));
    }

    public function update(Request $request, $id)
    {
        $funcCtrl = new FunctionsController();
        $budgetyear = (new BudgetYear())->setConnection(session('db_conn'))->find($id);
        $budgetyear->startdate = $funcCtrl->thaiDateToEngDateFormat($request->get('startdate'));
        $budgetyear->enddate = $funcCtrl->thaiDateToEngDateFormat($request->get('enddate'));
        $budgetyear->save();

        return redirect()->route('admin.budgetyear.index')->with('success','บันทึกการแก้ไขแล้ว');
    }

    public function invoice_period_list($budgetyear_id){
        return (new TwInvoicePeriod())->setConnection(session('db_conn'))->where('budgetyear_id', $budgetyear_id)->orderBy('id', 'desc')->get(['id', 'inv_p_name']);
    }
}
