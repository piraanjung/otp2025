<?php

namespace App\Http\Controllers\Tabwater;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\FunctionsController;
use App\Models\Admin\BudgetYear;
use App\Models\Admin\Organization;
use App\Models\Tabwater\TwInvoicePeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BudgetYearController extends Controller
{
    public function index()
    {
        $funcCtrl = new FunctionsController();

        // ใช้ withCount ถ้ามีการกำหนด Relationship ใน Model BudgetYear ว่า invoicePeriods()
        // แต่ถ้าไม่มี ใช้แบบเดิมได้ครับ แต่ระวัง N+1 ถ้าข้อมูลเยอะ
        $budgetyears = BudgetYear::on(session('db_conn'))
                        ->orderBy('budgetyear_name', 'desc')
                        ->get();

        $orgInfos = Organization::getOrgName(Auth::user()->org_id_fk);

        foreach ($budgetyears as $budgetyear) {
            $budgetyear->startdate = $funcCtrl->engDateToThaiDateFormat($budgetyear->startdate);
            $budgetyear->enddate = $funcCtrl->engDateToThaiDateFormat($budgetyear->enddate);
            
            // ตรวจสอบว่ามี Invoice Period หรือไม่
            $budgetyear->have_inv_peroid = TwInvoicePeriod::on(session('db_conn'))
                                            ->where('budgetyear_id', $budgetyear->id)
                                            ->exists(); // ใช้ exists() เร็วกว่า count() > 0
        }

        return view('admin.budgetyear.index', compact('budgetyears', 'orgInfos'));
    }

    public function create()
    {
        $orgInfos = Organization::getOrgName(Auth::user()->org_id_fk);
        return view('admin.budgetyear.create', compact('orgInfos'));
    }

    public function store(Request $request)
    {
        // ปรับ Validation ให้ยืดหยุ่นขึ้น
        $request->validate([
            'budgetyear' => 'required|integer|digits:4|min:2566', 
            'start'      => 'required',
            'end'        => 'required',
        ], [
            'required'   => 'กรุณากรอกข้อมูล',
            'integer'    => 'ต้องเป็นตัวเลข',
            'digits'     => 'ปีต้องมี 4 หลัก (พ.ศ.)',
            'min'        => 'ปีงบประมาณต้องมากกว่า 2566',
        ]);

        // Inactive ปีงบประมาณเก่าทั้งหมด
        BudgetYear::on(session('db_conn'))
            ->where('status', 'active')
            ->update(['status' => 'inactive']);

        $funcCtrl = new FunctionsController();
        
        // สร้างปีใหม่
        BudgetYear::on(session('db_conn'))->create([
            "budgetyear_name" => $request->budgetyear, // ใช้ property access ได้เลย
            "startdate"       => $funcCtrl->thaiDateToEngDateFormat($request->start),
            "enddate"         => $funcCtrl->thaiDateToEngDateFormat($request->end),
            "status"          => 'active',
        ]);

        return redirect()->route('admin.budgetyear.index')->with('success', 'บันทึกข้อมูลเรียบร้อย');
    }

    public function edit($id)
    {
        $budgetyear = BudgetYear::on(session('db_conn'))->findOrFail($id); // ใช้ findOrFail เพื่อดัก Error 404
        $funcCtrl = new FunctionsController();

        $budgetyear->startdate = $funcCtrl->engDateToThaiDateFormat($budgetyear->startdate);
        $budgetyear->enddate = $funcCtrl->engDateToThaiDateFormat($budgetyear->enddate);

        return view('admin.budgetyear.edit', compact('budgetyear'));
    }

    public function update(Request $request, $id)
    {
        $funcCtrl = new FunctionsController();
        $budgetyear = BudgetYear::on(session('db_conn'))->findOrFail($id);
        
        // ควร Update ปีงบประมาณด้วยไหม? ถ้าใน View เปิดให้แก้ input name="budgetyear" ก็ต้อง update ตรงนี้ด้วย
        if($request->has('budgetyear')){
             $budgetyear->budgetyear_name = $request->budgetyear;
        }

        $budgetyear->startdate = $funcCtrl->thaiDateToEngDateFormat($request->startdate);
        $budgetyear->enddate = $funcCtrl->thaiDateToEngDateFormat($request->enddate);
        $budgetyear->save();

        return redirect()->route('admin.budgetyear.index')->with('success', 'บันทึกการแก้ไขแล้ว');
    }

    // ฟังก์ชัน Delete ที่ถูกต้อง
    public function delete($id)
    {
        // เช็คอีกรอบฝั่ง Server เพื่อความปลอดภัย (เผื่อ User ยิง API ตรงๆ ไม่ผ่านปุ่ม)
        $hasInvoice = TwInvoicePeriod::on(session('db_conn'))
                        ->where('budgetyear_id', $id)
                        ->exists();

        if ($hasInvoice) {
             return redirect()->back()->with('error', 'ไม่สามารถลบได้ เนื่องจากมีรายการรอบบิลใช้งานอยู่');
        }

        $budgetyear = BudgetYear::on(session('db_conn'))->findOrFail($id);
        $budgetyear->delete(); // Hard Delete ตามที่คุยกัน

        return redirect()->route('admin.budgetyear.index')->with('success', 'ลบข้อมูลเรียบร้อยแล้ว');
    }

    public function invoice_period_list($budgetyear_id){
        return TwInvoicePeriod::on(session('db_conn'))
                ->where('budgetyear_id', $budgetyear_id)
                ->orderBy('id', 'desc')
                ->get(['id', 'inv_p_name']);
    }
}