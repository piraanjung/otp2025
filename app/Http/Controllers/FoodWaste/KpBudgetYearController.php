<?php

namespace App\Http\Controllers\KeptKaya;

use  App\Http\Controllers\Controller;
use App\Http\Controllers\FunctionsController;
use App\Models\KpBinsInvoices;
use App\Models\KeptKaya\KpBudgetYear;
use App\Models\KpInvoicePeriods;
use App\Models\KpUserGroup;
use App\Models\KpUsergroupPayratePerMonth;
use App\Models\KpUserKeptkayaInfos;
use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KpBudgetYearController extends Controller
{
    public function index()
    {
        $funcCtrl = new FunctionsController();

        $budgetyears = KpBudgetYear::orderBy('budgetyearname', 'desc')->get();
        foreach ($budgetyears as $budgetyear) {
            $budgetyear->startdate = $funcCtrl->engDateToThaiDateFormat($budgetyear->startdate);
            $budgetyear->enddate = $funcCtrl->engDateToThaiDateFormat($budgetyear->enddate);
        }

        return view('keptkaya.kp_budgetyear.index', \compact('budgetyears'));
    }

    public function create()
    {
        $userKeptKayaInfos = KpUserKeptkayaInfos::with([
            'kp_bins' => function ($query) {
                return $query->select('id', 'kp_u_infos_idfk', 'bincode', 'status')
                    ->where('status', "<>", 'inactive')->where('deleted', '0');
            }
        ])->where([
            'status' => 'active',
            'as_tbank' => "0"
        ])->get();

        $userKeptKayaInfos = collect($userKeptKayaInfos)->filter(function ($v) {
            return collect($v->kp_bins)->isNotEmpty();
        });
        $users = collect($userKeptKayaInfos)->groupBy('kp_usergroup_idfk');
        $zones = Zone::all();

        $currentBudgetYear = KpBudgetYear::where('status', 'active')->get();

        $budgetyear_id = 1;
        if (collect($currentBudgetYear)->isNotEmpty()) {
            $budgetyear_id = $currentBudgetYear[0]->id;
        }

        $usergroups = KpUserGroup::where('status', 'active')
            ->with([
                'kp_usergroup_payrate_permonth' => function ($q) use ($budgetyear_id) {
                    return $q->select('id', 'kp_usergroup_id_fk', 'vat', 'budgetyear_idfk', 'payrate_permonth')
                        ->where('budgetyear_idfk', $budgetyear_id);
                },
            ])->get();

        if (collect($currentBudgetYear)->isEmpty()) {
            $budgetYear = [
                "budgetyear_id" => 1,
                "budgetYear"  => Date('Y') + 543,
                "startDate"   => Date("30/09/" . (date("Y") + 542)),
                "endDate"     => date("01/10/" . date("Y", strtotime("+12 month")) + 542)
            ];
        } else {
            $budgetYear = [
                "budgetyear_id" => $budgetyear_id,
                "budgetYear"  => $currentBudgetYear[0]->budgetyearname + 1,
                "startDate"   => Date("30/09/" . $currentBudgetYear[0]->budgetyearname),
                "endDate"     => date("01/10/" . $currentBudgetYear[0]->budgetyearname + 1)
            ];
        }
        return view('admin.kp_budgetyear.create', compact('budgetYear', 'usergroups'));
    }

    public function store(Request $request, KpBudgetYear $budgetYear)
    {
        $request->validate([
            'budgetyear' => 'required|integer|between:2567,2599',
            'start'  => 'required',
            'end'  => 'required',
        ], [
            'required' => 'ใส่ข้อมูล',
            'integer' => 'ต้องเป็นตัวเลขปีปฏิทิน 4 ตัว',
            'in' => 'ต้องมากกว่าปี 2566'
        ]);

        date_default_timezone_set('Asia/Bangkok');

        //inactive ปีงบประมาณก่อนหน้านี้
        KpBudgetYear::where('status', 'active')->update([
            'status' => 'inactive'
        ]);

        // จากนั้น create new budgetyear
        $funcCtrl = new FunctionsController();
        $currentBGYear =  KpBudgetYear::create([
            "budgetyearname"   => $request->get('budgetyear'),
            "startdate"         => $funcCtrl->thaiDateToEngDateFormat($request->get('start')),
            "enddate"           => $funcCtrl->thaiDateToEngDateFormat($request->get('end')),
            "deleted"           => '0',
            "status"            => 'active',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')

        ]);

        //inactive kp_usergroup_payrate_peryears ปีงบประมาณก่อนหน้านี้
        KpUsergroupPayratePerMonth::where('budgetyear_idfk', $request->get('kp_budgetyear_id'))->update([
            'status' => 'inactive',
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        //create kp_usergroup_payrate_permonth สำหรับปีงบประมาณใหม่
        foreach ($request->get('payrate') as $payrate) {
            KpUsergroupPayratePerMonth::create([
                'kp_usergroup_id_fk' => $payrate['usergroup'],
                'budgetyear_idfk' => $currentBGYear->id,
                'payrate_permonth' => $payrate['ratepermonth'],
                'vat' => $payrate['vat'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }


        //getข้อมูลสมาชิก keptkaya  ทั้งที่ as_tbank =0 และ 1
        $userKeptKayaInfos = KpUserKeptkayaInfos::with([
            'kp_bins' => function ($query) {
                return $query->select('id', 'kp_u_infos_idfk', 'bincode', 'next_invoice_num', 'kp_payrate_permonth_idfk', 'status')
                    ->where('status', "<>", 'inactive')->where('deleted', '0');
            },
            'kp_bins.kp_bins_payrate_permonth' => function ($q) {
                return $q->select('id', 'kp_usergroup_id_fk', 'payrate_permonth', 'vat', 'budgetyear_idfk');
            }
        ])->where('status', 'active')->get();

        //filter เอาเฉพาะสมาชิกที่มีถังขยะ
        $userKeptKayaInfos = collect($userKeptKayaInfos)->filter(function ($v) {
            return collect($v->kp_bins)->isNotEmpty();
        });

        //inactive เดือนของปีงบประมาณ ก่อนหน้า
        KpInvoicePeriods::where('status', 'active')->update([
            'status' => 'inactive',
        ]);

        //สร้างเดือนของปีงบประมาณปัจจุบัน
        $bGYear  = substr($request->get('budgetyear'), 2);
        $months         = [
            '09-' . ($bGYear - 1),
            '10-' . ($bGYear - 1),
            '11-' . ($bGYear - 1),
            '12-' . ($bGYear - 1),
            '01-' . ($bGYear),
            '02-' . ($bGYear),
            '03-' . ($bGYear),
            '04-' . ($bGYear),
            '05-' . ($bGYear),
            '06-' . ($bGYear),
            '07-' . ($bGYear),
            '08-' . ($bGYear)
        ];

        // $activeKpInvoicePeriodsId = [1,
        // 2,
        // 3,
        // 4,
        // 5,
        // 6,
        // 7,
        // 8,
        // 9,
        // 10,
        // 11,
        // 12
        // ];
        $activeKpInvoicePeriodsId = [];
        foreach ($months as $month) {
            $kpInvoicePeriod = KpInvoicePeriods::create([
                'kp_inv_p_name' => $month,
                'kp_budgetyear_idfk' => $currentBGYear->id,
                'status'  => 'active',
                'deleted'    => '0',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ]);
            $activeKpInvoicePeriodsId[] = $kpInvoicePeriod->id;
        }

        //สร้าง kp_invoice สำหรับแต่ละถังของ user จำนวนถังละ 12 เดือน  
        //เก็บข้อมูลไว้ใน array
        $inv_datas = [];
        foreach ($userKeptKayaInfos as $key => $userKeptKayaInfo) {
            return $userKeptKayaInfo;
            foreach ($userKeptKayaInfo->kp_bins as $bin) {

                $kp_u_infos_str = substr('0000', strlen($bin->kp_u_infos_idfk)) . "" . $bin->kp_u_infos_idfk;
                foreach ($activeKpInvoicePeriodsId as $inv_p_id) {
                    $paid = $bin->kp_bins_payrate_permonth[0]->payrate_permonth;
                    return  $vat =  round($bin->kp_bins_payrate_permonth[0]->payrate_permonth * ($bin->kp_bins_payrate_permonth[0]->vat / 100), 2);
                    $inv_datas[] = [
                        'inv_no' => "06" . $kp_u_infos_str . "0" . $bin->kp_bins_payrate_permonth[0]->id . "0" . $bin->kp_bins_payrate_permonth[0]->next_invoice_num,
                        'kp_bin_idfk' => $bin->kp_bins_payrate_permonth[0]->id,
                        'kp_inv_period_idfk' => $inv_p_id,
                        'kp_acc_trans_idfk' => 0,
                        'paid' => $paid,
                        'vat' => $vat,
                        'totalpaid' => $paid + $vat,
                        'recorder_idfk' => Auth::user()->id,
                        'status'  => 'active',
                        'deleted'    => '0',
                        'created_at'  => date('Y-m-d H:i:s'),
                        'updated_at'  => date('Y-m-d H:i:s'),
                    ];
                }
            }
        }
        //สร้าง kp_invoice สำหรับแต่ละถังของ user จำนวนถังละ 12 เดือน 
        //โดยแบ่งบันทึกข้อมูลเป็นกลุ่มย่อยๆ
        return $inv_datas;
        foreach (collect($inv_datas)->chunk(200) as $chunks) {
            return $chunks;
            foreach ($chunks as $invoice) {

                KpBinsInvoices::insert($invoice);
            }
        }

        return redirect()->route('admin.kp_budgetyear.index')->with(['color' => 'success', 'message' => 'บันทึกข้อมูลเรียบร้อย']);
    }

    public function edit($id)
    {
        $budgetyear = KpBudgetYear::find($id);
        $funcCtrl = new FunctionsController();

        $budgetyear->startdate = $funcCtrl->engDateToThaiDateFormat($budgetyear->startdate);
        $budgetyear->enddate = $funcCtrl->engDateToThaiDateFormat($budgetyear->enddate);

        return view('admin.budgetyear.edit', compact('budgetyear'));
    }

    public function destroy(Request $request, $id)
    {
        $budgetyear = KpBudgetYear::with('user_paid_per_budgetyear')
            ->where('id', $id)
            ->first();
        if ($budgetyear->user_paid_per_budgetyear->count() > 0) {
            return redirect()->route('admin.budgetyear.index')->with(['color' => 'warning', 'message' => '!!ไม่สามารถทำการลบข้อมูลได้!!     มีการใช้งานข้อมูลนี้อยู่']);
        }
        $budgetyear->delete();

        return redirect()->route('admin.budgetyear.index')->with([
            'message' => 'ทำการลบข้อมูลเรียบร้อยแล้ว',
            'color' => 'success'
        ]);
    }

    public function update(Request $request, $id)
    {
        $funcCtrl = new FunctionsController();
        $budgetyear = KpBudgetYear::find($id);
        $budgetyear->startdate = $funcCtrl->thaiDateToEngDateFormat($request->get('startdate'));
        $budgetyear->enddate = $funcCtrl->thaiDateToEngDateFormat($request->get('enddate'));
        $budgetyear->save();

        return redirect()->route('admin.budgetyear.index')->with('success', 'บันทึกการแก้ไขแล้ว');
    }
}
