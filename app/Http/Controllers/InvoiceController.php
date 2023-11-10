<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Http\Controllers\Api\FunctionsController;
use App\Http\Controllers\Api\InvoiceController as ApiInvoiceCtrl;
use App\Models\Admin\UserProfile;
use App\Models\Invoice;
use App\Models\InvoicePeriod;
use App\Models\Setting;
use App\Models\Subzone;
use App\Models\User;
use App\Models\UserMerterInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class InvoiceController extends Controller
{
    private $apiInvoiceCtrl;
    public function __construct(ApiInvoiceCtrl $apiInvoiceCtrl)
    {
        $this->apiInvoiceCtrl = $apiInvoiceCtrl;
    }
    public function index($_budgetyearId = '', $_invPeriod = '')
    {
        //ปรับ owe_count ใน user_meter_infos table ใหม่
        $funcCtrl = new FunctionsController();
        $current_inv_period = InvoicePeriod::where('status', 'active')->get(['id', 'inv_p_name'])->first();

        $invActive = Invoice::where('inv_period_id_fk', $current_inv_period->id)
            ->with([
                'usermeterinfos' => function ($query) {
                    $query->select('meter_id', 'undertake_zone_id', 'undertake_subzone_id', 'owe_count');
                },
                'usermeterinfos.undertake_zone'=> function ($query) {
                    $query->select('id', 'zone_name');
                },
                'usermeterinfos.undertake_subzone'=> function ($query) {
                    $query->select('id','zone_id', 'subzone_name');
                }
            ])
            ->get();

        $grouped_inv_by_subzone = collect($invActive)->groupBy(function ($key) {
            return $key->usermeterinfos->undertake_subzone_id;
        });


        $zones = collect([]);
        //ข้อมูลของ แต่ละ subzone
        foreach ($grouped_inv_by_subzone as $key => $zone) {
            $status_grouped = collect($grouped_inv_by_subzone[$key])->groupBy('status');

            $invoiceTotalCount = 0;
            if (isset($status_grouped['invoice'])) {
                $invoiceTotalCount = collect($status_grouped['invoice'])->count();
                $invoice_count = collect($status_grouped['invoice'])->count();
            }
            $paidTotalCount = 0;
            if (isset($status_grouped['paid'])) {
                $paidTotalCount = collect($status_grouped['paid'])->count();
            }

            $initTotalCount = 0;
            if (isset($status_grouped['init'])) {
                $initTotalCount = collect($status_grouped['init'])->count();
            }
            $zones->push([
                    'zone_id' =>$zone[0]->usermeterinfos->undertake_subzone->zone_id,
                    'zone_info' => $zone[0]->usermeterinfos,
                    'members_count' => collect($grouped_inv_by_subzone[$key])->count(),
                    'owe_over3' => collect($grouped_inv_by_subzone[$key])->filter(function ($item) {
                        return $item->usermeterinfos->owe_count >= 3;
                    })->count()
                    ,
                    'initTotalCount' => $initTotalCount,
                    'invoiceTotalCount' => $invoiceTotalCount,
                    'paidTotalCount' => $paidTotalCount
                ]);
        }
        $zones = collect($zones)->sortBy('zone_id');
        return view('invoice.index', compact('zones', 'current_inv_period'));
    }

    public function paid($id)
    {
        $inv = $this->apiInvoiceCtrl->get_user_invoice($id);
        $invoice = json_decode($inv->getContent());
        // dd($invoice);
        return view('invoice.paid', compact('invoice'));
    }

    public function zone_create($subzone_id, $curr_inv_prd, $new_user = 0)
    {
        $curr_inv_init_status = Invoice::where(['inv_period_id_fk' => $curr_inv_prd, 'status' => 'init'])
                                    ->with(['usermeterinfos'=> function ($query) use ($subzone_id) {
                                        $query->select('meter_id', 'undertake_subzone_id', 'user_id', 'metertype_id','meternumber')
                                            ->where('undertake_subzone_id', $subzone_id);
                                    },'usermeterinfos.meter_type'=> function ($query){
                                        $query->select('id', 'price_per_unit');
                                    }
                                    ])->get();
        //filter subzone  ที่ต้องการ
        $invoices = collect($curr_inv_init_status)->filter(function ($item) use ($subzone_id) {
            if(collect($item->usermeterinfos)->isNotEmpty()) {
                return $item->usermeterinfos->undertake_subzone_id == $subzone_id;
            }
        });

        return view('invoice.zone_create', compact('invoices'));
    }
    public function store(REQUEST $request)
    {
        date_default_timezone_set('Asia/Bangkok');
        //filter หาเฉพาะที่มีการกรอกข้อมูลขมิเตอร์ปัจจุบัน
        $filters = collect($request->get('data'))->filter(function ($val) {
            return $val['currentmeter'] != null;
        });

        $presentInvoicePeriod = InvoicePeriod::where("status", "active")->first(); //หารอบบิลล่าสุด
        //เพิ่มข้อมูลลง invoice
        foreach ($filters as $inv) {
            Invoice::where('meter_id_fk', $inv['meter_id'])
                ->where('inv_period_id_fk', $presentInvoicePeriod->id)
                ->update([
                    'lastmeter' => $inv['lastmeter'],
                    'currentmeter' => $inv['currentmeter'],
                    'status' => 'invoice',
                    'recorder_id' => Auth::id(),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
        }
        return redirect()->route('invoice.index')->with([
            'massage' => 'ทำการบันทึกข้อมูลเรียบร้อยแล้ว',
            'color' => 'success',
        ]);
    }
    public function edit($invoice_id)
    {
        $inv = $this->apiInvoiceCtrl->get_user_invoice($invoice_id);
        $invoice = json_decode($inv->getContent());
        return view('invoice.edit', compact('invoice'));
    }
    public function update(REQUEST $request, $id)
    {
        date_default_timezone_set('Asia/Bangkok');
        $invoice = invoice::find($id);
        $invoice->currentmeter = $request->get('currentmeter');
        $invoice->status = $request->get('status');
        $invoice->recorder_id = 5;
        $invoice->updated_at = date('Y-m-d H:i:s');
        $invoice->save();

        return \redirect('invoice/index');
    }

    public function invoiced_lists($subzone_id)
    {
        return view('invoice.invoiced_lists', compact('subzone_id'));
    }

    public function print_multi_invoice(REQUEST $request)
    {
        $validated = $request->validate([
            'inv_id'=> 'required',
        ],[
            'required'=> 'ยังไม่ได้เลือกแถวที่ต้องการปริ้น',
        ]) ;
        date_default_timezone_set('Asia/Bangkok');
        if ($request->get('mode') == 'payment') {
            //การเป็นการจ่ายเงิน ให้ทำการบันทึกยอดเงินใน accounting
            foreach ($request->get('payments') as $key => $val) {
                $acc = new Account();
                $acc->net = $val['total'];
                $acc->recorder_id = Auth::id();
                $acc->printed_time = 1;
                $acc->status = 1;
                $acc->created_at = date('Y-m-d H:i:s');
                $acc->updated_at = date('Y-m-d H:i:s');
                $acc->save();

                //แล้ว update invoice  status = paid
                invoice::where('meter_id_fk', $key)->update([
                    'status' => 'paid',
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }
        //เตรียมการปริ้น
        $setting_tambon_infos_json = Setting::where('name', 'tambon_infos')->get(['values']);
        $setting_tambon_infos = json_decode($setting_tambon_infos_json[0]['values'], true);
        //หาวันสุดท้ายที่จะมาชำระหนี้ได้ ให้เวลา 30 วันนับแต่ออกใบแจ้งหนี้
        $setting_invoice_expired = Setting::where('name', 'invoice_expired')->get(['values']);
        $strStartDate = date('Y-m-d');
         $invoice_expired_next30day = date("Y-m-d", strtotime("+" . $setting_invoice_expired[0]['values'] . " day", strtotime($strStartDate)));
        $invoiceArray = [];
        $apiInvoiceCtrl = new ApiInvoiceCtrl();
        foreach ($request->get('inv_id') as $key => $on) {
            if ($on == 'on') {
                $data = json_decode($apiInvoiceCtrl->get_user_invoice_by_invId_and_mode($key, $request->get('mode'))->getContent(), true);

                array_push($invoiceArray, $data);

            }
        }
        $mode = "multipage";
        $subzone_id = $request->get('subzone_id');
        return view('invoice.print', compact('invoiceArray', 'mode', 'subzone_id', 'setting_tambon_infos', 'invoice_expired_next30day'));

    }

    public function search_from_meternumber($meternumber, $zone_id)
    {
        //หาเลขมิเตอร์
        $usermeterInfos = UserMerterInfo::orWhere('meternumber', 'LIKE', '%' . $meternumber . '%')
            ->where('zone_id', $zone_id)
            ->with('user', 'user.user_profile', 'user.usermeter_info.zone')->get()->first();

        if (collect($usermeterInfos)->count() == 0) {
            return $arr = ['usermeterInfos' => null, 'invoice' => null];
        }
        $invoice = invoice::where('user_id', $usermeterInfos->user_id)
            ->orderBy('id', 'desc')
            ->get()->first();
        return $arr = ['usermeterInfos' => $usermeterInfos, 'invoice' => $invoice];

    }

    public function not_invoiced_lists()
    {
        //แสดงตาราง user ที่ยังไม่ถูกออกใบแจ้งหนี้
        $invoice = invoice::where('inv_period_id', 1)->get('user_id');
        $invoiced_array = collect($invoice)->pluck('user_id');
        return $users = User::whereNotIn('id', $invoiced_array)
            ->where('user_cat_id', 3)
            ->get();

    }

    public function zone_info($subzone_id)
    {
        $funcCtrl = new FunctionsController();
        $presentInvoicePeriod = InvoicePeriod::where('status', 'active')->get()->first();
        $userMeterInfos = UserMerterInfo::where('undertake_subzone_id', $subzone_id)
            ->where('status', 'active')
            ->with('user_profile', 'zone', 'subzone')
            ->orderBy('undertake_zone_id')->get(['user_id', 'undertake_zone_id', 'meternumber', 'undertake_subzone_id']);
        //หาว่า มีการบันทึก invoice ในรอบบิลปัจจุบันหรือยัง
        foreach ($userMeterInfos as $user) {
            $user->invoice = invoice::where('inv_period_id', $presentInvoicePeriod->id)
                ->where('user_id', $user->user_id)->get()->first();
        }

        $totalMemberCount = $userMeterInfos->count();

        $memberNoInvoice = collect($userMeterInfos)->filter(function ($value, $key) {
            return collect($value->invoice)->isEmpty();
        });
        $memberHasInvoiceFilter = collect($userMeterInfos)->filter(function ($value, $key) {
            return !collect($value->invoice)->isEmpty();
        });

        $memberHasInvoice = collect($memberHasInvoiceFilter)->sortBy('user_id');

        $memberHasInvoiceCount = $totalMemberCount - collect($memberNoInvoice)->count();
        $zoneInfo = collect($userMeterInfos)->first();
        return view('invoice.zone_info', compact('zoneInfo', 'memberHasInvoice', 'memberNoInvoice'));

    }

    public function zone_edit($subzone_id, $curr_inv_prd)
    {
        $inv_status_invoice= Invoice::where('inv_period_id_fk', $curr_inv_prd)
        ->where('status', 'invoice')
        ->with(['usermeterinfos'=> function ($query) use ($subzone_id) {
            $query->select('meter_id', 'undertake_subzone_id', 'user_id', 'metertype_id','meternumber')
                ->where('undertake_subzone_id', $subzone_id);
        },'usermeterinfos.meter_type'=> function ($query){
            $query->select('id', 'price_per_unit');
        }
        ])->get();
        $inv_in_seleted_subzone = collect($inv_status_invoice)->filter(function ($value, $key) use ( $subzone_id) {
            if(collect($value->usermeterinfos)->isNotEmpty())
                return $value->usermeterinfos->undertake_subzone_id == $subzone_id;
        });
        return view('invoice.zone_edit', compact('inv_in_seleted_subzone', 'subzone_id'));
    }
    public function zone_update(REQUEST $request,  $subzone_id)
    {

        date_default_timezone_set('Asia/Bangkok');
        $filters_all = collect($request->get('zone'))->filter(function ($val) {
            return $val['changevalue'] == 1 || $val['status'] == 'delete' || $val['status'] == 'init';
        });
        $filters_changeValue = collect($filters_all)->filter(function ($val) {
            return $val['changevalue'] == 1;
        });
        $filters_delete_status = collect($filters_all)->filter(function ($val) {
            return $val['status'] == 'delete';
        });
        $filters_init_status = collect($filters_all)->filter(function ($val) {
            return $val['status'] == 'init';
        });
        if (collect($filters_changeValue)->count() > 0) {
            foreach ($filters_changeValue as $key => $vals) {
                $invoice = invoice::where('id', $key)->update([
                    "currentmeter" => $vals['currentmeter'],
                    "lastmeter" => $vals['lastmeter'],
                    "recorder_id" => Auth::id(),
                    "comment" => $vals['comment'],
                    "updated_at" => date('Y-m-d H:i:s'),
                ]);
            }
        }
        if (collect($filters_delete_status)->count() > 0) {
            foreach ($filters_delete_status as $key => $vals) {
                $invoice = invoice::where('id', $key)->update([
                    "status" => 'deleted',
                    "deleted" => 1,
                    "recorder_id" => Auth::id(),
                    "comment" => $vals['comment'],
                    "updated_at" => date('Y-m-d H:i:s'),
                ]);
            }
        }

        if (collect($filters_init_status)->count() > 0) {
            foreach ($filters_init_status as $key => $vals) {
                $invoice = invoice::where('id', $key)->update([
                    "currentmeter" => 0,
                    "status" => 'init',
                    "recorder_id" => Auth::id(),
                    "comment" => $vals['comment'],
                    "updated_at" => date('Y-m-d H:i:s'),
                ]);
            }
        }
        return \redirect('invoice/index')->with([
            'massage' => 'ทำการบันทึกการแก้ไขข้อมูลเรียบร้อยแล้ว',
            'color' => 'success',
        ]);
    }

    public function zone_create_for_new_users(REQUEST $request)
    {
        $user_id_array = collect($request->get('new_users'))->flatten();
        $presentInvoicePeriod = InvoicePeriod::where("status", "active")->first();
        $lastInvoicePeriod = InvoicePeriod::where("status", "inactive")->orderBy('id', 'desc')->first();
        if (collect($lastInvoicePeriod)->isEmpty()) {
            $lastInvoicePeriod = $presentInvoicePeriod;
        }
        $currentInvPeriod_id = $presentInvoicePeriod->id;
        $prevInvPeriod_id = $lastInvoicePeriod->id;
        $subzone_id = $request->get('undertake_subzone_id');
        $new_users = $request->get('new_users');
        // //ถ้าต้องการสร้างข้อมูลการใช้น้ำของ user ที่เพิ่ง add เข้ามาใหม่
        // //หา user ที่ invoice ที่ invoice_period ปัจจุบัน []
        foreach ($new_users as $user) {
            //สร้าง invoice รอบบิลปัจจุบัน
            $user_query = invoice::where('user_id', $user['user_id'])->where('inv_period_id', $currentInvPeriod_id);
            $userinfo = $user_query->get();
            if (collect($userinfo)->isNotEmpty()) {
                $user_query->update([
                    'status' => 'init',
                    'currentmeter' => 0,
                    'deleted' => 0,
                    'recorder_id' => Auth::id(),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            } else {
                $InvForNewUser = new Invoice();
                $InvForNewUser->inv_period_id = $currentInvPeriod_id;
                $InvForNewUser->lastmeter = 0;
                $InvForNewUser->user_id = $user['user_id'];
                $InvForNewUser->meter_id = $user['user_id'];
                $InvForNewUser->currentmeter = 0;
                $InvForNewUser->status = 'init';
                $InvForNewUser->recorder_id = Auth::id();
                $InvForNewUser->created_at = date('Y-m-d H:i:s');
                $InvForNewUser->updated_at = date('Y-m-d H:i:s');
                $InvForNewUser->save();
            }

        }
        $member_not_yet_recorded_present_inv_period = UserMerterInfo::where('undertake_subzone_id', $subzone_id)
            ->where('status', 'active')
            ->with([
                'user_profile:name,address,user_id',
                'invoice' => function ($query) use ($prevInvPeriod_id, $currentInvPeriod_id) {
                    return $query->select('inv_period_id', 'currentmeter', 'status', 'id as iv_id', 'user_id')
                        // ->where('inv_period_id', '>=', $prevInvPeriod_id)
                        ->where('inv_period_id', '=', $currentInvPeriod_id);
                    // ->where('status', 'init');

                },
                'invoice_last_inctive_inv_period' => function ($query) use ($prevInvPeriod_id, $currentInvPeriod_id) {
                    return $query->select('inv_period_id', 'currentmeter', 'lastmeter', 'id as iv_id', 'user_id')
                        ->where('inv_period_id', '=', $currentInvPeriod_id);
                    // ->where('inv_period_id', '<=', $currentInvPeriod_id);
                    // ->where('status', 'init');

                },
                'zone' => function ($query) {
                    return $query->select('zone_name', 'id');
                },
                'subzone' => function ($query) {
                    return $query->select('subzone_name', 'id');
                },
            ])
            ->orderBy('user_id')
            ->whereIn('user_id', $user_id_array)
            ->get(['meternumber', 'undertake_zone_id', 'undertake_subzone_id', 'id', 'user_id']);

        // $member_not_yet_recorded_present_inv_period = collect($subzone_members)->filter(function ($v) {
        //     return collect($v->invoice)->count() > 0 && $v->invoice[0]->status == 'init';
        // })->flatten();
        return view('invoice.zone_create', compact('member_not_yet_recorded_present_inv_period', 'presentInvoicePeriod'));

    }

    public function delete($invoice_id, $comment)
    {
        $inv = invoice::where('id', $invoice_id)->update([
            'status' => 'deleted',
            'deleted' => 1,
            'recorder_Id' => Auth::id(),
            'updated_at' => date('Y-m-d H:i:s'),
            'comment' => $comment,
        ]);
        return redirect('invoice/index')->with(['message' => 'ทำการลบข้อมูลเรียบร้อยแล้ว']);
    }

    public function invoice_by_user($type = "data", $status = "", $user_id = "")
    {
        if ($type == 'count') {
            $active_users = $this->oweSql($status)->where('umf.user_id', '=', $user_id)->count();
        } else {
            $active_users = $this->oweSql($status)->where('umf.user_id', '=', $user_id)->get();
        }
        return $active_users;
    }

    public function invoice_by_subzone($type = "data", $status = "", $subzone_id = "")
    {
        if ($type == 'count') {
            $owes = $this->oweSql($status)->where('umf.undertake_subzone_id', '=', $subzone_id)->count();
        } else {
            $owes = $this->oweSql($status)->where('umf.undertake_subzone_id', '=', $subzone_id)->get();
        }
        return $owes;
    }

    private function oweSql($status)
    {
        $sql = DB::table('user_meter_infos as umf')
            ->join('user_profile as uf', 'uf.user_id', '=', 'umf.user_id')
            ->leftJoin('invoice as iv', 'iv.user_id', '=', 'umf.user_id')
            // ->leftJoin('invoice as iv', 'iv.meter_id', '=', 'umf.id')
            ->where('umf.status', '=', 'active')
            ->where('iv.status', '=', $status)
            ->select(
                'umf.meternumber',
                'umf.user_id',
                'iv.*',
                'uf.name',
                'uf.address',
            );
        return $sql;
    }
}
