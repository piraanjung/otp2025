<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\FunctionsController;
use App\Models\BudgetYear;
use Illuminate\Http\Request;

class BudgetYearController extends Controller
{
    public function index()
    {
        $funcCtrl = new FunctionsController();

        $budgetyears = BudgetYear::orderBy('budgetyear', 'desc')->get();
        foreach ($budgetyears as $budgetyear) {
            $budgetyear->startdate = $funcCtrl->engDateToThaiDateFormat($budgetyear->startdate);
            $budgetyear->enddate = $funcCtrl->engDateToThaiDateFormat($budgetyear->enddate);
        }

        return view('admin.budgetyear.index', \compact('budgetyears'));
    }

    public function create()
    {
        return view('admin.budgetyear.create');
    }

    public function store(Request $request, BudgetYear $budgetYear)
    {
        $request->validate([
            'budgetyear' => 'required|integer|in:2567,2568,2569,2570,2571,2572',
            'start'  => 'required',
            'end'  => 'required',
        ],[
            'required' => 'ใส่ข้อมูล',
            'integer' => 'ต้องเป็นตัวเลขปีปฏิทิน 4 ตัว',
            'in'=> 'ต้องมากกว่าปี 2566'
        ]);

        date_default_timezone_set('Asia/Bangkok');

        //inactive last budgetyear
        BudgetYear::where('status', 'active')->update([
            'status'=> 'inactive'
        ]);
        //รอสร้าง update invoice period table status active ของ  last budgetyear ให้เป็น  invactive
        // create new budgetyear
        $funcCtrl = new FunctionsController();
        BudgetYear::create([
                "budgetyear" => $request->get('budgetyear'),
                "startdate" => $funcCtrl->thaiDateToEngDateFormat($request->get('start')),
                "enddate" => $funcCtrl->thaiDateToEngDateFormat($request->get('end')),
                "status" => 'active',
        ]);

        return redirect()->route('admin.budgetyear.index')->with('success','บันทึกข้อมูลเรียบร้อย');

    }

    public function edit($id)
    {
        $budgetyear = BudgetYear::find($id);
        $funcCtrl = new FunctionsController();

        $budgetyear->startdate = $funcCtrl->engDateToThaiDateFormat($budgetyear->startdate);
        $budgetyear->enddate = $funcCtrl->engDateToThaiDateFormat($budgetyear->enddate);

        return view('admin.budgetyear.edit', compact('budgetyear'));
    }

    public function delete($id)
    {
        $budgetyear = BudgetYear::find($id);

        $budgetyear->delete();

        return view('budgetyear.edit', compact('budgetyear'));
    }

    public function update(Request $request, $id)
    {
        $funcCtrl = new FunctionsController();
        $budgetyear = BudgetYear::find($id);
        $budgetyear->startdate = $funcCtrl->thaiDateToEngDateFormat($request->get('startdate'));
        $budgetyear->enddate = $funcCtrl->thaiDateToEngDateFormat($request->get('enddate'));
        $budgetyear->save();

        return redirect()->route('admin.budgetyear.index')->with('success','บันทึกการแก้ไขแล้ว');
    }
}
