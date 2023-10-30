<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Api\FunctionsController;


class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $funcCtrl = new FunctionsController();
        $currentInvPeriod = 1;//InvoicePeriod::where('status', 'active')->get('id')->first();

        //หาสมาชิกผู้ใช้น้ำที่ใช้งานอยู่
        $userMeterInfos = UserMeterInfos::orderBy('undertake_zone_id')
            ->whereIn('status', ['active', 'cutmeter'])
            ->where('deleted', 0)
            ->orderBy('id', 'asc')
            ->get(['id', 'user_id', 'undertake_zone_id', 'undertake_subzone_id']);

        $userMeterInfosGroup = collect($userMeterInfos)->groupBy(['undertake_zone_id', 'undertake_subzone_id']);

        $zones = [];
        $newUserCount = 0;
        foreach ($userMeterInfosGroup as $users_in_zone) {
            //จัดการข้อมูลแยกตามหมู่ที่ สังกัด
            foreach ($users_in_zone as $key => $user_in_zone) {
                $invoice_sum = 0;
                if ($findNewInvPeriod == 0) {
                    // table =0; ยังไม่มีการบันทึกข้อมูล ของรอบบิลใหม่
                } else {
                    $invoicesTemp = UserMeterInfos::where('undertake_subzone_id', $key)
                        ->where('deleted', 0)
                        ->whereIn('status', ['active', 'cutmeter'])
                        ->with(['invoice' => function ($query) use ($currentInvPeriod) {
                            return $query->where('inv_period_id', $currentInvPeriod->id)
                                ->select('id as iv_id', 'user_id', 'inv_period_id', 'lastmeter', 'currentmeter', 'status', 'deleted', 'comment');
                        }])
                        ->get();
                    $invoic_status_count_array = collect($invoicesTemp)->countBy(function ($v) {
                        $status = '';
                        if (collect($v->invoice)->isNotEmpty()) {
                            $duplicateInv = collect($v->invoice)->sortByDesc('iv_id')->take(1)->flatten();
                            $status = $duplicateInv[0]->status;
                        } else {
                            // dd($v);
                            $status = 'new_user';
                        }
                        return $status;
                        // if (!isset($v->invoice[0]->status)) {
                        //     return 'new_user';
                        // } else {
                        //     return $v->invoice[0]->status;
                        // }

                    });

                    $invoice_sum = collect($invoicesTemp)->count(function ($inv) {
                        if ($inv->invoice[0]->status == 'invoice') {
                            $sum = ($inv->invoice[0]['currentmeter'] - $inv->invoice[0]['lastmeter']) * 8;
                            return $sumtemp = $sum == 0 ? 10 : $sum;
                            // $invoice_sum += $sumtemp == 0 ? 10 : $sumtemp;
                        }
                    });

                    // if (collect($invoices)->isNotEmpty()) {
                    //     foreach ($invoices as $inv) {
                    //         $sum = ($inv->invoice[0]['currentmeter'] - $inv->invoice[0]['lastmeter']) * 8;
                    //         $sumtemp = $sum == 0 ? 10 : $sum;
                    //         $invoice_sum += $sumtemp == 0 ? 10 : $sumtemp;

                    //     }
                    // }
                    // $newUserCount = collect($invoicesTemp)->filter(function);
                } //else

                $subzone = Subzone::where('id', $user_in_zone[0]->undertake_subzone_id)->get(['id', 'zone_id', 'subzone_name'])->first();
                //หา จำนวนที่โยนยกหม้อมิเตอร์ใน subzone นี้
                $invoice_current_inv_period_deleted_status = 0;
                $invoice_current_inv_period_new_user_status = 0;
                if (isset($invoic_status_count_array['deleted'])) {
                    $invoice_current_inv_period_deleted_status = $invoic_status_count_array['deleted'];
                }
                if (isset($invoic_status_count_array['new_user'])) {
                    $invoice_current_inv_period_new_user_status = $invoic_status_count_array['new_user'];

                }
                $new_user = $invoice_current_inv_period_deleted_status + $invoice_current_inv_period_new_user_status;
                $cutmeter_count = UserMeterInfos::whereIn('status', ['active', 'cutmeter'])
                    ->where('deleted', 0)
                    ->where('owe_count', '>=', 3)
                    ->where('undertake_subzone_id', $user_in_zone[0]->undertake_subzone_id)->count();
                array_push($zones, [
                    'zone_id' => $user_in_zone[0]->undertake_zone_id,
                    'zone_name' => $subzone->zone->zone_name,
                    'subzone_id' => collect($subzone)->isEmpty() ? 999 : $subzone->id,
                    'subzone_name' => collect($subzone)->isEmpty() ? 999 : $subzone->subzone_name,
                    'total' => collect($user_in_zone)->count(),
                    //function invStatusCountBySubzone() นับจำนวนตามสถานะ
                    'invoice' => isset($invoic_status_count_array['invoice']) ? $invoic_status_count_array['invoice'] : 0, // $this->invStatusCountBySubzone('invoice', $key, $currentInvPeriod->id),
                    'owes' => isset($invoic_status_count_array['owe']) ? $invoic_status_count_array['owe'] : 0, // $this->invStatusCountBySubzone('owe', $key, $currentInvPeriod->id),
                    'paid' => isset($invoic_status_count_array['paid']) ? $invoic_status_count_array['paid'] : 0, //$this->invStatusCountBySubzone('paid', $key, $currentInvPeriod->id),
                    'new_user' => $new_user,
                    'init' => isset($invoic_status_count_array['init']) ? $invoic_status_count_array['init'] : 0,
                    'invoice_sum' => $invoice_sum,
                    'cutmeter_count' => $cutmeter_count,
                ]);
            }
        } //foreach
        // return $zones;
        $invoice_period = InvoicePeriod::where('status', 'active')->get()->first();
        return view('invoice.index', compact('zones', 'invoice_period'));
    }
}
