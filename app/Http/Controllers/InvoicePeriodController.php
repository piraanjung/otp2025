<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\FunctionsController;
use App\Models\InvoicePeriod;
use Illuminate\Http\Request;

class InvoicePeriodController extends Controller
{
    public function index()
    {
        $funcCtrl = new FunctionsController();

        //1.check ว่ามีปีงบประมาณที่ active ไหม ถ้าไม่มีให้ทำการสร้างปีงบประมาณก่อน
        $budgetyearModel = BudgetYear::where('status', 'active')->get();
        $budgetyearCount = collect($budgetyearModel)->count();

        $budgetyear = $budgetyearCount == 0 ? $budgetyearCount : $budgetyearModel[0]->id;
        $invoice_periods = InvoicePeriod::with('budgetyear')
            ->where('deleted', '<>', 1)->orderBy('startdate', 'desc')
            ->where('budgetyear_id', $budgetyear)
            ->get();

        foreach ($invoice_periods as $invoice_period) {
            $invoice_period->startdate = $funcCtrl->engDateToThaiDateFormat($invoice_period->startdate);
            $invoice_period->enddate = $funcCtrl->engDateToThaiDateFormat($invoice_period->enddate);
        }

        return view('invoice_period.index', compact('invoice_periods', 'budgetyearCount'));
    }

    public function create()
    {
        $budgetyear = BudgetYear::where('status', 'active')->first();
        if (collect($budgetyear)->isEmpty()) {
            //ยังไม่ได้สร้างปีงบประมาณ ให้ redirect ไปสร้าง
            return redirect()->action('BudgetYearController@index');
        }
        return view('invoice_period.create', compact('budgetyear'));
    }

    public function create_invoices($id)
    {
        //สร้างใบแจ้งหนี้เริ่มต้นของแต่ละ รอบบิลใหม่
        return $invoice_period = InvoicePeriod::with('budgetyear')->where('id', $id)->get()->first();

        return view('invoice_period.create_invoices', \compact('invoice_period'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'startdate' => 'required',
            'enddate' => 'required',
            'inv_period_name' => 'required',
        ]);

        return $this->test($request);

        date_default_timezone_set('Asia/Bangkok');
        $funcCtrl = new FunctionsController();

        //update invoice_period เดิม ล่าสุดให้เป็น status == inactive
        $last_prev_inv_period_sql = InvoicePeriod::where('status', 'active');
        $last_prev_inv_period = $last_prev_inv_period_sql->get();

        if (collect($last_prev_inv_period)->isNotEmpty()) {
            $last_prev_inv_period_update = $last_prev_inv_period_sql->update([
                'status' => 'inactive',
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            return $find_status_eq_invoice_in_inv_table_sql = Invoice::where('inv_period_id', $last_prev_inv_period[0]->id)
                ->where('status', 'invoice');

            $inv_status_eq_invoice = $find_status_eq_invoice_in_inv_table_sql->get(['user_id']);
            //ทำการ เพิ่ม owe_count ใน user_meter_infos  ของแต่ละ user
            foreach ($inv_status_eq_invoice as $item) {
                $user_meter_infos_get_owe_count_sql = UserMeterInfos::where('user_id', $item->user_id)
                    ->where('deleted', 0);
                $user_meter_infos_get_owe_count = $user_meter_infos_get_owe_count_sql->get(['owe_count']);
                $owe_count_increase = $user_meter_infos_get_owe_count[0]->owe_count + 1;

                // update owe_count ถ้า >3 ให้ update status == cutmeter
                if ($owe_count_increase == 4) {
                    $user_meter_infos_get_owe_count_update = $user_meter_infos_get_owe_count_sql->update([
                        'owe_count' => $user_meter_infos_get_owe_count[0]->owe_count + 1,
                        'status' => $owe_count_increase > 3 ? 'cutmeter' : 'active',
                        'comment' => $owe_count_increase > 3 ? 'ค้างชำระเกิน 3 งวด' : '',
                    ]);
                }

            }

            //ทำการ  update  invoice table โดย ให้ status ของ inv_peroid_id
            //ล่าสุดที่เป็น inactive  -> status = 'owe'
            $updatePrevInvPrdInInvStatusToOwe = $find_status_eq_invoice_in_inv_table_sql->update([
                'status' => 'owe',
                'recorder_id' => Auth::id(),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        }

        //บันทึกข้อมูล รอบบิล ใหม่
        $invoice_period = new InvoicePeriod();
        $invoice_period->inv_period_name = $request->get('inv_period_name') . "-" . $request->get('inv_period_name_year');
        $invoice_period->budgetyear_id = $request->get('budgetyear_id');
        $invoice_period->startdate = $funcCtrl->thaiDateToEngDateFormat($request->get('startdate'));
        $invoice_period->enddate = $funcCtrl->thaiDateToEngDateFormat($request->get('enddate'));
        $invoice_period->created_at = date('Y-m-d H:i:s');
        $invoice_period->updated_at = date('Y-m-d H:i:s');
        $invoice_period->save();

        //สร้าง invioce
        $this->createInvoiceWhenCreateNewInvPeroid($invoice_period->id);
        return redirect('/invoice_period')->with(['message' => 'ทำการเพิ่มข้อมูลเรียบร้อยแล้ว']);
    }

    private function createInvoiceWhenCreateNewInvPeroid($inv_peroid)
    {
        $userMeterInfos = UserMeterInfos::where('status', 'active')->get();
        $newInvoice = [];
        foreach ($userMeterInfos as $active_user) {
            array_push($newInvoice, [
                'inv_period_id' => $inv_peroid,
                'user_id' => $active_user->user_id,
                'meter_id' => $active_user->user_id,
                'lastmeter' => 0,
                'currentmeter' => 0,
                'status' => 'init',
                'recorder_id' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),

            ]);
        }

        Invoice::insert($newInvoice);

    }

    public function test($request)
    {
        return $this->testStore($request);
    }

    public function testStore($request)
    {

        date_default_timezone_set('Asia/Bangkok');
        $funcCtrl = new FunctionsController();

        $last_prev_inv_period_sql = InvoicePeriod::where('status', 'active');
        $last_prev_inv_period = $last_prev_inv_period_sql->get(['id']);

        // update invoice_period เดิม ล่าสุดให้เป็น status == inactive
        if (collect($last_prev_inv_period)->isNotEmpty()) {
            $last_prev_inv_period_update = $last_prev_inv_period_sql->update([
                'status' => 'inactive',
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }

        $last_prev_inv_period_id = $last_prev_inv_period[0]->id;
        $find_status_eq_invoice_in_inv_table_sql = Invoice::where('inv_period_id', $last_prev_inv_period[0]->id)
            ->where('deleted', 0)->where('status', 'invoice');

        $inv_status_eq_invoice = $find_status_eq_invoice_in_inv_table_sql->get(['id', 'user_id']);

        // ทำการ  update  invoice table โดย ให้ status ของ inv_peroid_id
        // ล่าสุดที่เป็น inactive  -> status = 'owe'
        $updatePrevInvPrdInInvStatusToOwe = $find_status_eq_invoice_in_inv_table_sql->update([
            'status' => 'owe',
            'recorder_id' => Auth::id(),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $find_status_eq_init_in_inv_table_sql = Invoice::where('inv_period_id', $last_prev_inv_period_id)
            ->where('deleted', 0)->where('status', 'init')->update([
            'status' => 'deleted',
            'deleted' => 1,
            'recorder_id' => Auth::id(),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        //บันทึกข้อมูล รอบบิล ใหม่
        $invoice_period = new InvoicePeriod();
        $invoice_period->inv_period_name = $request->get('inv_period_name') . "-" . $request->get('inv_period_name_year');
        $invoice_period->budgetyear_id = $request->get('budgetyear_id');
        $invoice_period->startdate = $funcCtrl->thaiDateToEngDateFormat($request->get('startdate'));
        $invoice_period->enddate = $funcCtrl->thaiDateToEngDateFormat($request->get('enddate'));
        $invoice_period->status = 'active';
        $invoice_period->created_at = date('Y-m-d H:i:s');
        $invoice_period->updated_at = date('Y-m-d H:i:s');
        $invoice_period->save();

        $userMeterInfos = UserMeterInfos::where('status', 'active')
            ->with([
                'invoice' => function ($query) use ($last_prev_inv_period_id) {
                    return $query->select('user_id', 'currentmeter', 'inv_period_id')->where('inv_period_id', $last_prev_inv_period_id);
                },
            ])
            ->where('deleted', 0)
            ->get(['id', 'user_id', 'owe_count']);
        $not_current_inv_peroid = collect($userMeterInfos)->filter(function ($v) {
            return collect($v->invoice)->isEmpty();
        });

        $newInvoice = [];
        foreach ($userMeterInfos as $active_user) {
            $plus1_owe_count = $active_user->owe_count + 1;
            UserMeterInfos::where('id', $active_user->id)->update([
                'owe_count' => $plus1_owe_count,
                'cutmeter' => $plus1_owe_count >= 3 ? 1 : 0,
                'comment' => $plus1_owe_count >= 3 ? 'ค้างชำระเกิน 3 งวด' : '',
            ]);
            array_push($newInvoice, [
                'inv_period_id' => isset($invoice_period->id) ? $invoice_period->id : 0,
                'user_id' => $active_user->user_id,
                'meter_id' => $active_user->user_id,
                'lastmeter' => collect($active_user->invoice)->isNotEmpty() ? $active_user->invoice[0]->currentmeter : 0,
                'currentmeter' => 0,
                'status' => 'init',
                'recorder_id' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),

            ]);
        }

        //หาเลขมิเตอร์ล่าสุดกรณีที่ไม่มีการสร้าง invoice รอบบิลปัจจุบัน
        if (collect($not_current_inv_peroid)->isNotEmpty()) {
            foreach ($not_current_inv_peroid as $v) {
                $res = Invoice::where('user_id', $v->user_id)
                    ->orderByDesc('inv_period_id')->limit(1)
                    ->get(['inv_period_id', 'currentmeter']);
                if (collect($res)->isNotEmpty()) {

                    $plus1_owe_count = $active_user->owe_count + 1;
                    UserMeterInfos::where('id', $active_user->id)->update([
                        'owe_count' => $plus1_owe_count,
                        'cutmeter' => $plus1_owe_count >= 3 ? 1 : 0,
                        'comment' => $plus1_owe_count >= 3 ? 'ค้างชำระเกิน 3 งวด' : '',
                    ]);
                    array_push($newInvoice, [
                        'inv_period_id' => isset($invoice_period->id) ? $invoice_period->id : 0,
                        'user_id' => $v->user_id,
                        'meter_id' => $v->user_id,
                        'lastmeter' => collect($v->invoice)->isNotEmpty() ? $v->currentmeter : 0,
                        'currentmeter' => 0,
                        'status' => 'init',
                        'recorder_id' => 0,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),

                    ]);
                }
            }
        }

        // return` $newInvoice;
        Invoice::insert($newInvoice);
        return redirect('/invoice_period')->with(['message' => 'ทำการเพิ่มข้อมูลเรียบร้อยแล้ว']);

    }

    public function edit($id)
    {
        $funcCtrl = new FunctionsController();

        $invoice_period = InvoicePeriod::with('budgetyear')->find($id);
        $invoice_period->startdate = $funcCtrl->engDateToThaiDateFormat($invoice_period->startdate);
        $invoice_period->enddate = $funcCtrl->engDateToThaiDateFormat($invoice_period->enddate);
        return view('invoice_period.edit', compact('invoice_period'));
    }

    public function update(Request $request, $id)
    {
        date_default_timezone_set('Asia/Bangkok');
        $funcCtrl = new FunctionsController();
        $invoice_period = InvoicePeriod::find($id);
        $invoice_period->inv_period_name = $request->get('inv_period_name');
        $invoice_period->startdate = $funcCtrl->thaiDateToEngDateFormat($request->get('startdate'));
        $invoice_period->enddate = $funcCtrl->thaiDateToEngDateFormat($request->get('enddate'));
        $invoice_period->status = $request->get('status');
        $invoice_period->save();

        return redirect('/invoice_period')->with(['message' => 'ทำการอัพเดทข้อมูลเรียบร้อยแล้ว']);
    }

    public function delete($id)
    {
        // ถ้ารอบบิลนี้มีการใช้ในการบันทึก invoice แล้วแจ้งเตือนก่อนว่าจะต้องการลบไหม
        $delete = InvoicePeriod::find($id);
        $delete->update([
            'status' => 'deleted',
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        return redirect('/invoice_period')->with(['message' => 'ทำการลบข้อมูลเรียบร้อยแล้ว']);
    }
}
