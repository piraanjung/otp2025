<?php

namespace App\Http\Controllers\Tabwater;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\FunctionsController;
use App\Models\Admin\BudgetYear;
use App\Models\Admin\Organization;
use App\Models\Tabwater\InvoicePeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BudgetYearController extends Controller
{
    public function index()
    {
        $funcCtrl = new FunctionsController();
        $budgetyears = BudgetYear::on(session('db_conn'))
            ->orderBy('budgetyear_name', 'desc')
            ->get();
        $orgInfos = Organization::getOrgName(Auth::user()->org_id_fk);

        foreach ($budgetyears as $budgetyear) {
            $budgetyear->startdate = $funcCtrl->engDateToThaiDateFormat($budgetyear->startdate);
            $budgetyear->enddate = $funcCtrl->engDateToThaiDateFormat($budgetyear->enddate);
            $budgetyear->have_inv_peroid = InvoicePeriod::on(session('db_conn'))
                ->where('budgetyear_id', $budgetyear->id)
                ->exists();
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
        $request->validate([
            'budgetyear' => 'required|integer|digits:4|min:2566',
            'start' => 'required',
            'end' => 'required',
        ], [
            'required' => 'กรุณากรอกข้อมูล',
            'integer' => 'ต้องเป็นตัวเลข',
            'digits' => 'ปีต้องมี 4 หลัก (พ.ศ.)',
            'min' => 'ปีงบประมาณต้องมากกว่า 2566',
        ]);

        // 1. ปิดสถานะปีงบประมาณเก่าให้เป็น inactive
        BudgetYear::on(session('db_conn'))
            ->where('status', 'active')
            ->update(['status' => 'inactive']);

        $funcCtrl = new FunctionsController();
        $org_id = Auth::user()->org_id_fk;

        $startDateEng = $funcCtrl->thaiDateToEngDateFormat($request->start);
        $endDateEng = $funcCtrl->thaiDateToEngDateFormat($request->end);

        // 2. สร้างปีงบประมาณใหม่
        $newBudgetYear = BudgetYear::create([
            'org_id_fk' => $org_id,
            "budgetyear_name" => $request->budgetyear,
            "startdate" => $startDateEng,
            "enddate" => $endDateEng,
            "status" => 'active',
        ]);

        // =====================================================================
        // 3. สร้างรอบบิลกลาง 12 เดือน (รอดลعبไว้ใช้งานร่วมกันทุกโมดูล)
        // =====================================================================
        // ตามปีงบประมาณไทย จะเริ่มตั้งแต่เดือน ตุลาคม ของปีก่อนหน้า จนถึง กันยายน ของปี พ.ศ. นั้น
        // เช่น ปีงบประมาณ 2569 จะเริ่ม 1 ต.ค. 2568 ถึง 30 ก.ย. 2569
        $thaiMonths = [
            1 => 'มกราคม',
            2 => 'กุมภาพันธ์',
            3 => 'มีนาคม',
            4 => 'เมษายน',
            5 => 'พฤษภาคม',
            6 => 'มิถุนายน',
            7 => 'กรกฎาคม',
            8 => 'สิงหาคม',
            9 => 'กันยายน',
            10 => 'ตุลาคม',
            11 => 'พฤศจิกายน',
            12 => 'ธันวาคม'
        ];

        // แปลงปี พ.ศ. เป็น ค.ศ. สำหรับคำนวณ Carbon (พ.ศ. - 543 = ค.ศ.)
        $bhYearAD = intval($request->budgetyear) - 543;
        $startLoopDate = Carbon::create($bhYearAD - 1, 10, 1); // เริ่ม 1 ตุลาคม ปีก่อนหน้า

        for ($i = 0; $i < 12; $i++) {
            $currentMonthDate = (clone $startLoopDate)->addMonths($i);
            $monthNum = $currentMonthDate->month;
            $yearTh = $currentMonthDate->year + 543; // แปลง ค.ศ. กลับเป็น พ.ศ. สำหรับแสดงชื่อ

            $periodName = $thaiMonths[$monthNum] . ' ' . $yearTh;

            // กำหนดวันเริ่มต้นและวันสิ้นสุดของแต่ละเดือน
            $pStart = (clone $currentMonthDate)->startOfMonth()->toDateString();
            $pEnd = (clone $currentMonthDate)->endOfMonth()->toDateString();

            // เซ็ตสถานะ active เฉพาะเดือนแรก หรือจะให้ inactive ทั้งหมดแล้วให้ผู้ใช้กดเลือกเปิดรอบเองก็ได้
            // ในที่นี้กำหนดให้เดือนแรกเป็น active และเดือนที่เหลือเป็น inactive (หรือจะปรับตามต้องการ)
            $pStatus = ($i === 0) ? 'active' : 'inactive';

            InvoicePeriod::on(session('db_conn'))->create([
                'org_id_fk'         => $org_id,
                'budgetyear_id'     => $newBudgetYear->id,
                'inv_p_name'        => $periodName,
                'inv_p_name_int'    => substr('00', strlen($monthNum)).$monthNum."-".substr($yearTh,2),
                'startdate'         => $pStart,
                'enddate'           => $pEnd,
                'status'            => $pStatus
            ]);
        }
        // =====================================================================

        return redirect()->route('admin.budgetyear.index')->with('success', 'บันทึกปีงบประมาณและสร้างรอบบิล 12 เดือนเรียบร้อย');
    }

    public function edit($id)
    {
        $budgetyear = BudgetYear::on(session('db_conn'))->findOrFail($id);
        $funcCtrl = new FunctionsController();
        $budgetyear->startdate = $funcCtrl->engDateToThaiDateFormat($budgetyear->startdate);
        $budgetyear->enddate = $funcCtrl->engDateToThaiDateFormat($budgetyear->enddate);
        return view('admin.budgetyear.edit', compact('budgetyear'));
    }

    public function update(Request $request, $id)
    {
        $funcCtrl = new FunctionsController();
        $budgetyear = BudgetYear::on(session('db_conn'))->findOrFail($id);

        if ($request->has('budgetyear')) {
            $budgetyear->budgetyear_name = $request->budgetyear;
        }

        $budgetyear->startdate = $funcCtrl->thaiDateToEngDateFormat($request->startdate);
        $budgetyear->enddate = $funcCtrl->thaiDateToEngDateFormat($request->enddate);
        $budgetyear->save();

        return redirect()->route('admin.budgetyear.index')->with('success', 'บันทึกการแก้ไขแล้ว');
    }

    public function delete($id)
    {
        // เช็คว่ามีข้อมูลบิลถูกใช้งานไปแล้วหรือยัง (ถ้ามีรอบบิลผูกอยู่ และมีประวัติบิล อาจจะต้องลบรอบบิลย่อยด้วย หรือป้องกันการลบ)
        $hasInvoice = InvoicePeriod::on(session('db_conn'))
            ->where('budgetyear_id', $id)
            ->exists();

        if ($hasInvoice) {
            // หมายเหตุ: ถ้าต้องการให้ลบปีงบประมาณแล้วลบตาราง invoice_period ย่อยทิ้งทั้งหมดแบบอัตโนมัติ (Cascade) สามารถเขียนคำสั่งลบพ่วงตรงนี้ได้ครับ
            return redirect()->back()->with('error', 'ไม่สามารถลบได้ เนื่องจากมีรายการรอบบิลใช้งานอยู่');
        }

        $budgetyear = BudgetYear::on(session('db_conn'))->findOrFail($id);
        $budgetyear->delete();

        return redirect()->route('admin.budgetyear.index')->with('success', 'ลบข้อมูลเรียบร้อยแล้ว');
    }

    public function invoice_period_list($budgetyear_id)
    {
        return InvoicePeriod::on(session('db_conn'))
            ->where('budgetyear_id', $budgetyear_id)
            ->orderBy('id', 'asc') // เรียงลำดับจากเดือนแรกไปเดือนสุดท้าย
            ->get(['id', 'inv_p_name']);
    }
}
