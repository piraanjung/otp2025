<?php

namespace App\Http\Controllers\Tabwater;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\FunctionsController;
use App\Models\Admin\ManagesTenantConnection;
use App\Models\Admin\BudgetYear;
use App\Models\Admin\Organization;
use App\Models\Tabwater\TwAccTransactions;
use App\Models\Tabwater\TwInvoice;
use App\Models\Tabwater\InvoicePeriod;
use App\Models\Tabwater\TwMeterInfos;
use App\Models\Tabwater\TwMeterType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvoicePeriodController extends Controller
{
    public function index(Request $request)
    {
        $org_id = Auth::user()->org_id_fk;

        // ดึงปีงบประมาณที่ active อยู่
        $budgetyear = BudgetYear::where('status', 'active')->first();

        $periods = [];
        if ($budgetyear) {
            // ดึงรอบบิลทั้ง 12 เดือนที่ผูกกับปีงบประมาณนี้
            $periods = InvoicePeriod::where('budgetyear_id', $budgetyear->id)
                ->orderBy('id', 'asc')
                ->get();

            // เช็คสถานะการสร้าง Invoice ของแต่ละรอบบิล
            foreach ($periods as $period) {
                $invoiceCount = TwInvoice::where('inv_period_id_fk', $period->id)->count();

                $period->invoice_count = $invoiceCount;
                $period->is_generated = $invoiceCount > 0;
            }
        }
        // เช็คว่ามีประเภทผู้ใช้น้ำและตั้งค่าอัตราค่าน้ำเรียบร้อยหรือยัง
        $hasConfig = TwMeterType::whereHas('rateConfigs')->exists();


        return view('admin.invoice_period.index', compact('budgetyear', 'periods', 'hasConfig'));
    }

    // ฟังก์ชันสร้าง Invoice เฉพาะรอบบิลที่เลือก (กดปุ่มสร้าง)
    public function generateInvoice($period_id)
    {
        $current_inv_prd = InvoicePeriod::findOrFail($period_id);

        // เช็คว่ารอบบิลนี้เคยถูกสร้างไปหรือยัง
        $existingCount = TwInvoice::where('inv_period_id_fk', $current_inv_prd->id)->count();

        if ($existingCount > 0) {
            return redirect()->back()->with(['color' => 'warning', 'message' => 'รอบบิลนี้ถูกสร้างใบแจ้งหนี้ไปแล้ว']);
        }

        // ดึงมิเตอร์ที่ active อยู่
        $user_meter_infos = TwMeterInfos::where('status', 'active')
            ->with(['invoice_not_paid' => function ($q) {
                $q->select('id', 'meter_id_fk', 'inv_period_id_fk', 'status', 'acc_trans_id_fk')
                    ->whereIn('status', ['owe', 'invoice']);
            }])
            ->get(['meter_id', 'user_id', 'last_meter_recording', 'inv_no_index']);

        $newInvoiceArray = [];
        $now = now();
        $orgId = Auth::user()->org_id_fk;

        $invoiceModel = new TwInvoice();

        foreach ($user_meter_infos as $user_meter_info) {
            $newInvNo = $invoiceModel->generateInvNo($user_meter_info->meter_id);

            $newInvoiceArray[] = [
                'meter_id_fk' => $user_meter_info->meter_id,
                'inv_no' => $newInvNo,
                'inv_period_id_fk' => $current_inv_prd->id,
                'lastmeter' => $user_meter_info->last_meter_recording,
                'currentmeter' => 0,
                'water_used' => 0,
                'paid' => 0,
                'reserve_meter' => 0,
                'vat' => 0,
                'totalpaid' => 0,
                'status' => 'init',
                'recorder_id' => Auth::id(),
                'created_at' => $now,
                'updated_at' => $now,
                'org_id_fk' => $orgId,
            ];

            // จัดการยอดค้างชำระเดิม (ถ้ามี)
            if ($user_meter_info->invoice_not_paid->isNotEmpty()) {
                $accTrans = TwAccTransactions::create([
                    'user_id_fk' => $user_meter_info->user_id,
                    'inv_no_fk' => 0,
                    'paidsum' => 0,
                    'vatsum' => 0,
                    'totalpaidsum' => 0,
                    'net' => 0,
                    'cashier' => Auth::id(),
                    'org_id_fk' => $orgId
                ]);

                $oweIds = $user_meter_info->invoice_not_paid->pluck('id');
                TwInvoice::whereIn('id', $oweIds)->update([
                    'acc_trans_id_fk' => $accTrans->id,
                    'updated_at' => $now
                ]);
            }
        }

        // บันทึกข้อมูลแบบ Bulk Insert
        if (!empty($newInvoiceArray)) {
            TwInvoice::insert($newInvoiceArray);
        }

        return redirect()->route('admin.invoice_period.index')
            ->with(['message' => 'สร้างใบแจ้งหนี้ประจำรอบบิล ' . $current_inv_prd->inv_p_name . ' เรียบร้อยแล้ว', 'color' => 'success']);
    }
    public function create()
    {
        $orgInfos = Organization::getOrgName(Auth::user()->org_id_fk);

        $budgetyear = (new BudgetYear())->setConnection(session('db_conn'))->where('status', 'active')->first();
        return view('admin.invoice_period.create', compact('budgetyear', 'orgInfos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'startdate'         => 'required',
            'enddate'           => 'required',
            'inv_period_name'   => 'required',
        ], ['required' => 'กรุณากรอกข้อมูล']);

        // ---------------------------------------------------------
        // 1. ตรวจสอบสถานะ Init (ป้องกันการสร้างซ้อน)
        // ---------------------------------------------------------
        $last_inv_prd = InvoicePeriod::latest('id')->first();

        $check_inv_init_status = 0;
        if ($last_inv_prd) {
            $check_inv_init_status = TwInvoice::where('inv_period_id_fk', $last_inv_prd->id)
                ->where('status', 'init')
                ->count();
        }

        if ($check_inv_init_status > 0) {
            return redirect()->route('admin.invoice_period.create')
                ->with(['color' => 'warning', 'message' => 'มีข้อมูลยังไม่ถูกบันทึก (สถานะ init ค้างอยู่)']);
        }

        // ---------------------------------------------------------
        // 2. Update รอบบิลเก่า (ถ้ามี)
        // ---------------------------------------------------------
        if ($last_inv_prd) {
            // ปิดรอบบิลเก่า
            $last_inv_prd->update(['status' => 'inactive']);

            // เปลี่ยนบิลที่ยังไม่จ่ายของรอบที่แล้ว ให้เป็น 'owe' (ค้างชำระ)
            TwInvoice::where('inv_period_id_fk', $last_inv_prd->id)
                ->where('status', 'invoice')
                ->update(['status' => 'owe']);
        }

        // ---------------------------------------------------------
        // 3. สร้างรอบบิลใหม่
        // ---------------------------------------------------------
        $funcCtrl = new FunctionsController();
        $req = $request->all();

        // แปลงวันที่และเตรียมข้อมูล
        $req['startdate']   = $funcCtrl->thaiDateToEngDateFormat($request->startdate);
        $req['enddate']     = $funcCtrl->thaiDateToEngDateFormat($request->enddate);
        $req['org_id_fk']   = Auth::user()->org_id_fk;
        $req['inv_p_name']  = $request->inv_period_name . "-" . $request->inv_period_name_year;
        $req['status']      = 'active';
        // $req['org_id_fk'] = ... (Trait เติมให้อัตโนมัติถ้าใช้ create)

        $current_inv_prd = InvoicePeriod::create($req);

        // ---------------------------------------------------------
        // 4. ดึงข้อมูลมิเตอร์และยอดค้างชำระ
        // ---------------------------------------------------------
        $user_meter_infos = TwMeterInfos::where('status', 'active')
            ->with(['invoice_not_paid' => function ($q) {
                $q->select('id', 'meter_id_fk', 'inv_period_id_fk', 'status', 'acc_trans_id_fk')
                    ->whereIn('status', ['owe', 'invoice']);
            }])
            ->get(['meter_id', 'user_id', 'last_meter_recording', 'inv_no_index']);

        $newInvoiceArray = [];
        $now = now(); // ใช้เวลาเดียวกันทั้งหมด
        $orgId = Auth::user()->org_id_fk; // ดึง Org ID มารอไว้

        // 4.1 เตรียม Array สำหรับ Invoice ใหม่
        // 1. หาเลขบิล "ล่าสุด" ของ Org นี้มาก่อน (ดึงครั้งเดียวพอ)
        // สมมติ format คือ "YYMMxxxx" (ปีเดือน + เลขรัน 4 หลัก)
        // 1. หาเลขล่าสุด (ใช้ Logic เดิมของคุณ ถูกแล้ว)
        $latestInvoice = TwInvoice::where('org_id_fk', Auth::user()->org_id_fk)
            ->where('inv_no', 'like', date('ym') . '%')
            ->orderBy('inv_no', 'desc')
            ->first();

        $lastRunningNo = 0;
        if ($latestInvoice) {
            // ตัดเอา 4 ตัวท้ายมาแปลงเป็น Int
            $lastRunningNo = intval(substr($latestInvoice->inv_no, -4));
        }

        $currentYearMonth = date('ym');
        // หมายเหตุ: ถ้าอยากได้ พ.ศ. ให้ใช้: (date('y') + 43) . date('m');

        $loopCounter = 1;

        foreach ($user_meter_infos as $user_meter_info) {

            // คำนวณเลขใหม่
            $nextNumber = $lastRunningNo + $loopCounter;
            $runningString = str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
            $newInvNo = $currentYearMonth . $runningString;

            $newInvoiceArray[] = [
                // เช็คตรงนี้ดีๆ ว่าใช้ 'id' หรือ 'meter_id' (ปกติ Eloquent มักคืนค่า id เป็น PK)
                'meter_id_fk'       => $user_meter_info->meter_id,

                'inv_no'            => $newInvNo,
                'inv_period_id_fk'  => $current_inv_prd->id,
                'lastmeter'         => $user_meter_info->last_meter_recording,
                'currentmeter'      => 0,
                'water_used'        => 0,
                'paid'              => 0,
                'reserve_meter'     => 0,
                'vat'               => 0,
                'totalpaid'         => 0,
                'status'            => 'init',
                'recorder_id'       => Auth::id(),
                'created_at'        => $now,
                'updated_at'        => $now,
                'org_id_fk'         => $orgId,
            ];

            // [Logic หนี้ค้างชำระ - ส่วนนี้ถูกต้องแล้ว]
            if ($user_meter_info->invoice_not_paid->isNotEmpty()) {
                $accTrans = TwAccTransactions::create([
                    'user_id_fk'    => $user_meter_info->user_id, // หรือ $user_meter_info->user_id_fk เช็คดีๆ
                    'inv_no_fk'     => 0, // หรือใส่ $newInvNo ถ้าต้องการผูกกับบิลปัจจุบัน (แต่ปกติหนี้เก่าจะไม่ผูกบิลใหม่)
                    'paidsum'       => 0,
                    'vatsum'        => 0,
                    'totalpaidsum'  => 0,
                    'net'           => 0,
                    'cashier'       => Auth::id(),
                    'org_id_fk'     => $orgId
                ]);

                $oweIds = $user_meter_info->invoice_not_paid->pluck('id');
                TwInvoice::whereIn('id', $oweIds)->update([
                    'acc_trans_id_fk' => $accTrans->id,
                    'updated_at'      => $now
                ]);
            }

            // +++++ [สำคัญมาก] ต้องบวกตัวนับเพิ่ม ไม่งั้นเลขซ้ำ +++++
            $loopCounter++;
        }

        // 4. บันทึกข้อมูลทั้งหมด (อย่าลืมบรรทัดนี้)
        if (!empty($newInvoiceArray)) {
            TwInvoice::insert($newInvoiceArray);
        }

        return redirect()->route('admin.invoice_period.index')
            ->with(['message' => 'ทำการบันทึกข้อมูลแล้ว', 'color' => 'success']);
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

    public function destroy(InvoicePeriod $invoice_period)
    {
        if (collect($invoice_period)->isNotEmpty()) {
            $check_inv_prd_count = (new InvoicePeriod())->setConnection(session('db_conn'))->all()->count();
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
