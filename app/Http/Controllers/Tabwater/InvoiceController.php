<?php

namespace App\Http\Controllers\Tabwater;

use App\Http\Controllers\Controller;
use App\Exports\InvoiceInCurrentInvoicePeriodExport;
use App\Http\Controllers\Api\InvoiceController as ApiInvoiceCtrl;
use App\Http\Controllers\FunctionsController;
use App\Models\Admin\Organization;
use App\Models\Tabwater\TwCutmeter;
use App\Models\Tabwater\TwInvoice;
use App\Models\Tabwater\TwInvoicePeriod;
use App\Models\Tabwater\Setting;
use App\Models\Admin\Subzone;
use App\Models\User;
use App\Models\Tabwater\TwMeterInfos;
use App\Models\Admin\Zone;
use App\Models\Tabwater\TwAccTransactions;
use App\Models\Tabwater\TwInvoiceHistoty;
use App\Models\Tabwater\TwInvoiceTemp;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class InvoiceController extends Controller
{
    
    public function index($_budgetyearId = '', $_invPeriod = '')
    {

        $orgInfos = Organization::getOrgName(Auth::user()->org_id_fk);

        $invPeriodModel = new TwInvoicePeriod();
        $current_inv_period = $invPeriodModel->where('status', 'active')->get(['id', 'inv_p_name'])->first();
        if (collect($current_inv_period)->isEmpty()) {
            return redirect()->route('admin.invoice_period.index')->with(['message' => 'ยังไม่ได้สร้างรอบบิล', 'color' => 'info']);
        }
        $current_inv_periodId = $current_inv_period->id;
        
        $orgId = $orgInfos['id'];
        $invoiceActive = TwInvoiceTemp::where('inv_period_id_fk', $current_inv_periodId)
            ->with([
                'tw_meter_infos' => function ($query) {
                    return $query->select('meter_id', 'undertake_zone_id', 'undertake_subzone_id', 'owe_count')
                    ;
                },
                'tw_meter_infos.undertake_zone' => function ($query) {
                    $query->select('id', 'zone_name',);
                },
                'tw_meter_infos.undertake_subzone' => function ($query) {
                    $query->select('id', 'subzone_name', 'zone_id');
                },
            ])
            ->whereHas('tw_meter_infos.user', function($q) use($orgId){
                $q->select('id')->where('org_id_fk', $orgId);
            })
            ->get(['id', 'inv_period_id_fk', 'meter_id_fk', 'status', 'water_used', 'paid', 'totalpaid', 'vat', 'reserve_meter']);

        $invoiceActiveFilterSubzoneNotNull = collect($invoiceActive)->filter(function ($item) {
            return collect($item->tw_meter_infos->undertake_subzone)->isNotEmpty();
        });
        $grouped_inv_by_subzone = collect($invoiceActiveFilterSubzoneNotNull)->groupBy(function ($key) {
            return $key->tw_meter_infos->undertake_subzone_id;
        })->values()->toArray();


 
        $zones = collect([]);
        //ข้อมูลของ แต่ละ subzone
        foreach ($grouped_inv_by_subzone as $key => $zone) {
            $status_grouped = collect($zone)->groupBy('status');
            $invoiceTotalCount = 0;
            $invoiceTotalAmount = 0;
            if (isset($status_grouped['invoice'])) {
                $invoiceTotalCount = collect($status_grouped['invoice'])->count();
                $invoiceTotalAmount = collect($status_grouped['invoice'])->sum('totalpaid');
            }
            $paidTotalCount = 0;
            $paidTotalAmount = 0;
            if (isset($status_grouped['paid'])) {
                $paidTotalCount = collect($status_grouped['paid'])->count();
                $paidTotalAmount = collect($status_grouped['paid'])->sum('totalpaid');
            }

            $initTotalCount = 0;
            if (isset($status_grouped['init'])) {
                $initTotalCount = collect($status_grouped['init'])->count();
            }
            $user_notyet_inv_info = TwMeterInfos::where('undertake_subzone_id', $zone[0]['tw_meter_infos']['undertake_subzone_id'])
                ->with([
                    'invoice' => function ($q) {
                        return $q->select('meter_id_fk');
                    }
                ])->where('status', 'active')->get(['undertake_subzone_id',  'meter_id']);
            $user_notyet_inv_info_count = collect($user_notyet_inv_info)->filter(function ($v) {
                return collect($v->invoice)->isEmpty();
            })->count();

            $zones->push([
                'zone_id'       => $zone[0]['tw_meter_infos']['undertake_subzone']['zone_id'],
                'zone_info'     => $zone[0]['tw_meter_infos'],
                'members_count' => collect($zone)->count(),
                'owe_over3'     => collect($zone)->filter(function ($item) {
                    return $item['tw_meter_infos']['owe_count'] >= 3;
                })->count(),
                'initTotalCount' => $initTotalCount,
                'invoiceTotalCount' => $invoiceTotalCount,
                'invoiceTotalAmount' => $invoiceTotalAmount,
                'paidTotalCount' => $paidTotalCount,
                'paidTotalAmount' => $paidTotalAmount,
                'user_notyet_inv_info' => $user_notyet_inv_info_count,
                'water_used' => collect($zone)->sum('water_used'),
                'net_paid' => collect($zone)->sum('totalpaid'),
                'reseve_paid' => collect($zone)->sum('reserve_meter'),
                'total_paid' => $invoiceTotalAmount + $paidTotalAmount,
                'total_paid_ref' => collect($zone)->sum('totalpaid'),
            ]);
        }
        $zones = collect($zones)->sortBy('zone_id');

        if ($_budgetyearId == 'from_user_api') {
            $resArray = [];
            $aa =  json_decode($_invPeriod);
            foreach ($aa as $a) {
                array_push($resArray, [
                    'zone_id' => $zones[$a->subzone_id]['zone_id'],
                    'initTotalCount' => $zones[$a->subzone_id]['initTotalCount'],
                    'invoiceTotalCount' => $zones[$a->subzone_id]['invoiceTotalCount'],
                    'members_count' => $zones[$a->subzone_id]['members_count'],
                    'paidTotalCount' => $zones[$a->subzone_id]['paidTotalCount'],
                    'undertake_subzone_id' => $zones[$a->subzone_id]['zone_info']['undertake_subzone_id'],
                    'subzone_name' => $zones[$a->subzone_id]['zone_info']['undertake_subzone']['subzone_name'],
                    'zone_name' => $zones[$a->subzone_id]['zone_info']['undertake_zone']['zone_name'],

                ]);
            }
            return $resArray;
        }

        return view('invoice.index', compact('zones', 'current_inv_period', 'orgInfos'));
    }

    


    public function paid($id)
    {
        $inv = $this->get_user_invoice($id);
        $invoice = json_decode($inv->getContent());

        return view('invoice.paid', compact('invoice'));
    }


    public function zone_create(Request $request, $subzone_id, $curr_inv_prd, $new_user = 0)
    {
        $member_not_yet_recorded_present_inv_period = [];
        $invoices = [];
        if ($new_user > 0) {
            
            $subzone_members = TwMeterInfos::where('undertake_subzone_id', $subzone_id)
                ->where('status', 'active')
                ->with([
                    'invoice' => function ($query) use ($curr_inv_prd) {
                        return $query->select('inv_period_id_fk', 'currentmeter', 'id', 'lastmeter', 'status', 'meter_id_fk')
                            ->where('inv_period_id_fk', '=', $curr_inv_prd);
                    },
                ])
                ->get(['id', 'undertake_subzone_id', 'factory_no', 'submeter_name', 'meternumber', 'user_id', 'metertype_id']);

            $member_inv_isEmpty_filtered = collect($subzone_members)->filter(function ($v) use ($curr_inv_prd) {
                return collect($v->invoice)->isEmpty() || $v->inv_period_id_fk == $curr_inv_prd;
            });
            $aa = collect($member_inv_isEmpty_filtered)->filter(function ($v) {
                return collect($v->invoice_last_inctive_inv_period)->isEmpty();
            });
            foreach ($member_inv_isEmpty_filtered as $key => $a) {
                $member_inv_isEmpty_filtered[$key]->invoice->push($curr_inv_prd);
                if (collect($a->invoice_last_inctive_inv_period)->isEmpty()) {
                    $member_inv_isEmpty_filtered[$key]->invoice_last_inctive_inv_period->push([
                        "inv_period_id" => $curr_inv_prd,
                        "currentmeter" => 0,
                        "id" => 0,
                    ]);
                }
            }
            $member_not_yet_recorded_present_inv_period[] = collect($member_inv_isEmpty_filtered)->values();
        } else {
            
            $curr_inv_init_status = TwInvoiceTemp::where(['inv_period_id_fk' => $curr_inv_prd, 'status' => 'init'])
                ->with([
                    'tw_meter_infos' => function ($query) use ($subzone_id) {
                        $query->select('id', 'undertake_subzone_id', 'user_id', 'factory_no', 'submeter_name', 'metertype_id', 'meternumber')
                            ->where('undertake_subzone_id', $subzone_id);
                    },
                    'tw_meter_infos.meter_type' => function ($query) {
                        $query->select('id');
                    },
                    'tw_meter_infos.meter_type.rateConfigs' => function ($query) {
                        $query->select('*');
                    },
                    'tw_meter_infos.meter_type.rateConfigs.Ratetiers' => function ($query) {
                        $query->select('*');
                    }
                ])->get();
            //filter subzone  ที่ต้องการ
            $invoices = collect($curr_inv_init_status)->filter(function ($item) use ($subzone_id) {
                if (collect($item->usermeterinfos)->isNotEmpty()) {
                    return $item->usermeterinfos->undertake_subzone_id == $subzone_id;
                }
            })->values();
        }
        // $invoices = $invoicesChunk[0];
        $subzone = (new Subzone())->setConnection(session('db_conn'))->find($subzone_id);
        $invoice_remain = collect($invoices)->count();
        $orgInfos = Organization::getOrgName(Auth::user()->org_id_fk);
        return view('invoice.zone_create', compact('orgInfos','invoices', 'invoice_remain', 'subzone', 'member_not_yet_recorded_present_inv_period'));
    }

    public function store(REQUEST $request)
    {
        date_default_timezone_set('Asia/Bangkok');
        //filter หาเฉพาะที่มีการกรอกข้อมูลขมิเตอร์ปัจจุบัน
        $filters = collect($request->get('data'))->filter(function ($val) {
            return $val['currentmeter'] > 0 || $val['inv_id'] == 'new_inv';
        });
        //เพิ่มข้อมูลลง invoice
        $inv_period_table   = (new TwInvoicePeriod())->setConnection(session('db_conn'))->where('status', 'active')->get(['id'])->first();
        foreach ($filters as $inv) {

            $invoiceTemp = (new TwInvoiceTemp())->setConnection(session('db_conn'))->find($inv['inv_id']);
            $twAccTransId = $invoiceTemp->acc_trans_id_fk;
            if($twAccTransId == 0){
                //สร้าง Acctrans
                $newAccTrans = (new TwAccTransactions())->setConnection(session('db_conn'))->create([
                    'meter_id_fk' =>  $inv['meter_id'],
                    'cashier' =>Auth::id()
                ]);
                $invoiceTemp->acc_trans_id_fk = $newAccTrans->id;
            }
            
                $invoiceTemp->inv_period_id_fk = $inv_period_table->id;
                $invoiceTemp->meter_id_fk = $inv['meter_id'];
                $invoiceTemp->lastmeter   = $inv['lastmeter'];
                $invoiceTemp->currentmeter = $inv['currentmeter'];
                $invoiceTemp->water_used  = $inv['currentmeter']-$inv['lastmeter'];
                $invoiceTemp->reserve_meter = $inv['meter_reserve_price'];
                $invoiceTemp->inv_type    = $inv['currentmeter']-$inv['lastmeter'] == 0 ? 'r' : 'u';
                $invoiceTemp->paid        = $inv['paid'];
                $invoiceTemp->vat         = $inv['vat'];
                $invoiceTemp->totalpaid   = $inv['totalpaid'];
                $invoiceTemp->status      = 'invoice';
                $invoiceTemp->recorder_id = Auth::id();
                $invoiceTemp->created_at  = date('Y-m-d H:i:s');
                $invoiceTemp->updated_at  = date('Y-m-d H:i:s');
                $invoiceTemp->save();
        }

        $subzone_id = $request->get('subzone_id');

        return redirect()->action(
            [InvoiceController::class, 'zone_create'],
            ['zone_id' => $subzone_id, 'curr_inv_prd' => $inv_period_table->id]
        )->with([
            'massage' => 'ทำการบันทึกข้อมูลเรียบร้อยแล้ว',
            'color' => 'success',
        ]);
    }
    public function edit($invoice_id)
    {
        $inv = $this->get_user_invoice($invoice_id);
        $invoice = json_decode($inv->getContent());
        return view('invoice.edit', compact('invoice'));
    }
    public function update(REQUEST $request, $id)
    {
        date_default_timezone_set('Asia/Bangkok');
        $invoice = Twinvoice::find($id);
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
            'inv_id' => 'required',
        ], [
            'required' => 'ยังไม่ได้เลือกแถวที่ต้องการปริ้น',
        ]);
        date_default_timezone_set('Asia/Bangkok');
        if ($request->get('mode') == 'payment') {
            //การเป็นการจ่ายเงิน ให้ทำการบันทึกยอดเงินใน accounting
            foreach ($request->get('payments') as $key => $val) {
                // $acc = new Account();
                // $acc->net = $val['total'];
                // $acc->recorder_id = Auth::id();
                // $acc->printed_time = 1;
                // $acc->status = 1;
                // $acc->created_at = date('Y-m-d H:i:s');
                // $acc->updated_at = date('Y-m-d H:i:s');
                // $acc->save();

                //แล้ว update invoice  status = paid
                Twinvoice::where('meter_id_fk', $key)->update([
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
        $usermeterInfos = TwMeterInfos::orWhere('meternumber', 'LIKE', '%' . $meternumber . '%')
            ->where('zone_id', $zone_id)
            ->with('user', 'user.user_profile', 'user.usermeter_info.zone')->get()->first();

        if (collect($usermeterInfos)->count() == 0) {
            return $arr = ['tw_meter_infos' => null, 'invoice' => null];
        }
        $invoice = Twinvoice::where('user_id', $usermeterInfos->user_id)
            ->orderBy('id', 'desc')
            ->get()->first();
        return $arr = ['tw_meter_infos' => $usermeterInfos, 'invoice' => $invoice];
    }

    public function not_invoiced_lists()
    {
        //แสดงตาราง user ที่ยังไม่ถูกออกใบแจ้งหนี้
        $invoice = Twinvoice::where('inv_period_id', 1)->get('user_id');
        $invoiced_array = collect($invoice)->pluck('user_id');
        return $users = User::whereNotIn('id', $invoiced_array)
            ->where('user_cat_id', 3)
            ->get();
    }

    public function zone_info($subzone_id)
    {
        $funcCtrl = new FunctionsController();
        $presentInvoicePeriod = TwInvoicePeriod::where('status', 'active')->get()->first();
        $userMeterInfos = TwMeterInfos::where('undertake_subzone_id', $subzone_id)
            ->where('status', 'active')
            ->with('user_profile', 'zone', 'subzone')
            ->orderBy('undertake_zone_id')->get(['user_id', 'undertake_zone_id', 'meternumber', 'undertake_subzone_id']);
        //หาว่า มีการบันทึก invoice ในรอบบิลปัจจุบันหรือยัง
        foreach ($userMeterInfos as $user) {
            $user->invoice = Twinvoice::where('inv_period_id', $presentInvoicePeriod->id)
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

    public function print_invoice($zone_id, $curr_inv_prd){
         $usermeter_infos = TwMeterInfos::where('undertake_subzone_id', $zone_id)
                    ->with('invoice')
                    ->whereHas('invoice', function($q){
                        return $q->whereIn('status',['invoice', 'owe']);
                    })->get(['id', 'user_id']);
        return view('invoice.print_invoice', compact('usermeter_infos'));
    }

    public function invoice_bill_print(Request $request){
        $print_infos = [];
        foreach($request->get('a') as $meter_id){
            $umf = TwMeterInfos::where('id', $meter_id)
                ->with(['invoice' => function($q){
                    return $q->select('*')->where('inv_period_id_fk', 7);
                }])->get()->first();
            $inv_owes = TwInvoice::where('meter_id_fk', $meter_id)
                        ->with('invoice_period')
                        ->where('status', 'owe')->get();
            $owe_infos = [];
            foreach($inv_owes as $owe){
                $a = explode('-',$owe->invoice_period->inv_p_name);
                $thaiMonthStr = FunctionsController::fullThaiMonth($a[0]);
                array_push($owe_infos,[
                    'inv_id' => $owe->inv_id,
                    'inv_period' => $thaiMonthStr." ".$a[1],
                    'totalpaid' => $owe->totalpaid
                ]);
            } 
            $currentPeriod = TwInvoicePeriod::where('status', 'active')->get()->first();
            $a = explode('-',$currentPeriod->inv_p_name);
            $thaiMonthStr = FunctionsController::fullThaiMonth($a[0]);
            if(!isset($umf->invoice[0]->created_at)){
                return $umf;
            }
            $inv_created_at = explode(' ',$umf->invoice[0]->created_at);
            $date =  Carbon::parse($inv_created_at[0]);
            $expired_date = $date->addDays(15)->format('Y-m-d');

            $thai_created_date = (new FunctionsController())->engDateToThaiDateFormat($inv_created_at[0]);
            $thai_expired_date = (new FunctionsController())->engDateToThaiDateFormat($expired_date);
            

            array_push($print_infos, [
                'id' => $umf->meter_id,
                'inv_id' =>  $umf->invoice[0]->inv_id,
                'meternumber' => $umf->meternumber,
                'submeter_name' => $umf->submeter_name,
                'user_id' => $umf->user_id,
                'name' => $umf->user->prefix.$umf->user->firstname." ".$umf->user->lastname,
                'user_address' => $umf->user->address." ".$umf->user->user_zone->zone_name,
                'lastmeter' => $umf->invoice[0]->lastmeter,
                'currentmeter' => $umf->invoice[0]->currentmeter,
                'water_used' =>  $umf->invoice[0]->water_used,
                'paid' =>  $umf->invoice[0]->paid,
                'vat' =>  $umf->invoice[0]->vat,
                'reservemeter' =>  $umf->invoice[0]->reservemeter,
                'totalpaid' =>  $umf->invoice[0]->totalpaid,
                'period' => $thaiMonthStr." ".$a[1],
                'created_at' => $thai_created_date,
                'expired_date' => $thai_expired_date,
                'owe_infos' => $owe_infos
            ]);
        }
        // return $print_infos;
        $org = Organization::where('id',2)->get()->first();
        return view('invoice.print_invoice_bills',compact('org', 'print_infos'));
        
    }
    public function export_excel(Request $request, $subzone_id, $curr_inv_prd)
    {
        $zone = Zone::where('id', $subzone_id)->get();
        $inv_p = TwInvoicePeriod::where('id', $curr_inv_prd)->get();
        
        $text = 'ฟอร์มกรอกข้อมูลเลขมิเตอร์ ' . $zone[0]->zone_name . ' รอบบิลเดือน ' . $inv_p[0]->inv_p_name . '.xlsx';
        return Excel::download(new InvoiceInCurrentInvoicePeriodExport(
            [
                'subzone_id' => $subzone_id,
                'curr_inv_prd' => $curr_inv_prd
            ]
        ), $text);
    }

    public function zone_edit($subzone_id, $curr_inv_prd)
    { 

        $userMeterInfos = TwMeterInfos::where('undertake_subzone_id', $subzone_id)
            ->with([
                'invoice_temp' => function ($query) {
                     $query->select(
                        'meter_id_fk',
                        'id',
                        'status',
                        'lastmeter',
                        'currentmeter',
                        'water_used',
                        'paid',
                        'reserve_meter',
                        'vat',
                        'totalpaid',
                        'created_at',
                        'updated_at',
                        'recorder_id',
                    )
                        ->whereIn('status', ['invoice']);
                },
                'meter_type' => function ($query) {
                        $query->select('id','org_id_fk');//->where('org_id_fk', Auth::user()->org_id_fk);
                    },
                    'meter_type.rateConfigs' => function ($query) {
                        $query->select('*');
                    },
                    'meter_type.rateConfigs.Ratetiers' => function ($query) {
                        $query->select('*');
                    }
            ])
            ->get(['meter_id', 'undertake_subzone_id', 'user_id', 'meter_address', 'factory_no', 'metertype_id', 'meternumber', 'metertype_id']);

        if (collect($userMeterInfos)->isEmpty()) {
            return redirect('invioce.index');
        }
        $inv_in_seleted_subzone = collect($userMeterInfos)->filter(function ($value, $key) {
            return collect($value->invoice_temp)->isNotEmpty();
        })->values();
        $orgInfos = Organization::getOrgName(Auth::user()->org_id_fk);

        return view('invoice.zone_edit', compact('orgInfos', 'inv_in_seleted_subzone', 'subzone_id'));
    }

    public function reset_invioce_bill($inv_id)
    {
        TwInvoice::where('inv_id', $inv_id)->update([
            'status'        => 'init',
            'currentmeter'  => 0,
            'water_used'    => 0,
            'paid'          => 0,
            'vat'           => 0,
            'totalpaid'     => 0,
        ]);
        return redirect()->back();
    }
    public function zone_update(REQUEST $request)
    {
        date_default_timezone_set('Asia/Bangkok');
        $filters_changeValue = collect($request->get('data'))->filter(function ($val) {
            return $val['changevalue'] == 1;
        });

        if (collect($filters_changeValue)->count() > 0) {
            foreach ($filters_changeValue  as $vals) {
                $invoice = (new TwInvoiceTemp())->setConnection(session('db_conn'))->find($vals['inv_id']);
                
                $invoice->currentmeter = $vals['currentmeter'];
                $invoice->lastmeter    = $vals['lastmeter'];
                $invoice->paid        = $vals['paid'];
                $invoice->inv_type    = $vals['water_used'] == 0 ? 'r' : 'u';
                $invoice->water_used  = $vals['water_used'];
                $invoice->reserve_meter = $vals['reserve_meter'];
                $invoice->vat         = $vals['vat'];
                $invoice->totalpaid   = $vals['totalpaid'];
                $invoice->recorder_id  = Auth::id();
                $invoice->comment      = '';
                $invoice->updated_at   = date('Y-m-d H:i:s');
                $invoice->save();

            }
        }
        
        return \redirect('invoice')->with([
            'message' => 'ทำการบันทึกการแก้ไขข้อมูลเรียบร้อยแล้ว',
            'color' => 'success',
        ]);
    }


    public function zone_update2(REQUEST $request) //route get
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
                $invoice = Twinvoice::where('id', $key)->update([
                    "currentmeter" => $vals['currentmeter'],
                    "lastmeter" => $vals['lastmeter'],
                    "recorder_id" => Auth::id(),
                    "comment" => $vals['comment'],
                    "updated_at" => date('Y-m-d H:i:s'),
                ]);
            }
        }
        // if (collect($filters_delete_status)->count() > 0) {
        //     foreach ($filters_delete_status as $key => $vals) {
        //         $invoice = Twinvoice::where('id', $key)->update([
        //             "status" => 'deleted',
        //             "deleted" => 1,
        //             "recorder_id" => Auth::id(),
        //             "comment" => $vals['comment'],
        //             "updated_at" => date('Y-m-d H:i:s'),
        //         ]);
        //     }
        // }

        // if (collect($filters_init_status)->count() > 0) {
        //     foreach ($filters_init_status as $key => $vals) {
        //         $invoice = Twinvoice::where('id', $key)->update([
        //             "currentmeter" => 0,
        //             "status" => 'init',
        //             "recorder_id" => Auth::id(),
        //             "comment" => $vals['comment'],
        //             "updated_at" => date('Y-m-d H:i:s'),
        //         ]);
        //     }
        // }
        return \redirect('invoice/index')->with([
            'massage' => 'ทำการบันทึกการแก้ไขข้อมูลเรียบร้อยแล้ว',
            'color' => 'success',
        ]);
    }

    public function zone_create_for_new_users(REQUEST $request)
    {
        $user_id_array = collect($request->get('new_users'))->flatten();
        $presentInvoicePeriod = TwInvoicePeriod::where("status", "active")->first();
        $lastInvoicePeriod = TwInvoicePeriod::where("status", "inactive")->orderBy('id', 'desc')->first();
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
            $user_query = Twinvoice::where('user_id', $user['user_id'])->where('inv_period_id', $currentInvPeriod_id);
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
                $InvForNewUser = new TwInvoice();
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
        $member_not_yet_recorded_present_inv_period = TwMeterInfos::where('undertake_subzone_id', $subzone_id)
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
        $inv = Twinvoice::where('id', $invoice_id)->update([
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

    private function manageOweCount()
    {
        $aa = TwMeterInfos::with([
            'invoice' => function ($query) {
                return $query->select('inv_period_id_fk', 'status', 'meter_id_fk')
                    ->whereIn('status', ['owe', 'invoice']);
            },
        ])->get(['id', 'user_id', 'owe_count', 'status']);

        $arr = collect([]);
        $i = 1;
        foreach ($aa as $a) {
            //นับ invoice status เท่ากับ owe หรือ invioce แล้วทำการ update
            //owe_count ให้  user_meter_infos ใหม่
            $oweInvCount = collect($a->invoice)->count(); // + 1; // บวก row  status == invoice

            TwMeterInfos::where('id', $a->meter_id)->update([
                'owe_count'     => $oweInvCount,
                'cutmeter'      => $oweInvCount >= 3 ? '1' : '0',
                'discounttype'  => $oweInvCount >= 3 ? $oweInvCount : 0,
            ]);

            //นำข้อมูล user ที่ค้างเกิน 2  ครั้งไปที่ cutmeter table
            $arr2 = [
                [
                    'status' => 'init',
                    'twman'  => '',
                    'date'   => strtotime(date('Y-m-d H:i:s')),
                    'comment' => ''
                ]
            ];
            if ($oweInvCount >= 3) {
                TwCutmeter::create([
                    'meter_id_fk' => $a->meter_id,
                    'owe_count'   => $oweInvCount,
                    'progress'    => json_encode($arr2),
                    'status'     => 'init',
                    'created_at' => strtotime(date('Y-m-d H:i:s')),
                    'updated_at' => strtotime(date('Y-m-d H:i:s')),
                ]);
            }
        };
        return $arr;
    }


     public function get_user_invoice($meter_id, $status = '')
    {
        
        $invoices = TwInvoiceTemp::where('meter_id_fk', $meter_id)
            ->with([
                'tw_meter_infos' => function ($query) {
                    $query->select('meter_id', 'user_id', 'meternumber', 'undertake_zone_id', 'undertake_subzone_id', 'metertype_id');
                },
                'tw_meter_infos.user' => function ($query) {
                    return $query->select('id', 'prefix', 'firstname', 'lastname', 'address', 'zone_id', 'phone', 'subzone_id', 'tambon_code', 'district_code', 'province_code');
                },
                'tw_meter_infos.user.user_tambon' => function ($query) {
                    return $query->select('id', 'tambon_name');
                },
                'tw_meter_infos.user.user_district' => function ($query) {
                    return $query->select('id', 'district_name');
                },
                'tw_meter_infos.user.user_province' => function ($query) {
                    return $query->select('id', 'province_name');
                },
                'invoice_period' => function ($query) {
                    return $query->select('id', 'inv_p_name', 'budgetyear_id');
                },
                'invoice_period.budgetyear' => function ($query) {
                    return $query->select('id', 'budgetyear_name', 'status');
                },
                'tw_meter_infos.undertake_zone' => function ($query) {
                    return $query->select('id', 'zone_name as undertake_zone_name');
                },
                'tw_meter_infos.undertake_subzone' => function ($query) {
                    return $query->select('id', 'subzone_name as undertake_subzone_name');
                },
                'tw_meter_infos.user.user_zone' => function ($query) {
                    return $query->select('id', 'zone_name as user_zone_name');
                },
                'tw_meter_infos.meter_type' => function ($query) {
                        $query->select('id');
                    },
                    'tw_meter_infos.meter_type.rateConfigs' => function ($query) {
                        $query->select('*');
                    },
                    'tw_meter_infos.meter_type.rateConfigs.Ratetiers' => function ($query) {
                        $query->select('*');
                    }
            ])
            ->orderBy('inv_period_id_fk', 'desc');
        if ($status != '') {
            if ($status == 'inv_and_owe') {
                $invoices = $invoices->whereIn('status', ['invoice', 'owe']);
            }
        }

        $invoices = $invoices->get([
            'id',
            'meter_id_fk',
            'inv_period_id_fk',
            'reserve_meter',
            'lastmeter',
            'currentmeter',
            'water_used',
            'paid',
            'vat',
            'totalpaid',
            'acc_trans_id_fk',
            'updated_at',
            'status'
        ]);

        return response()->json(collect($invoices)->flatten());
    }

    public function get_invoice_and_invoice_history($meter_id, $status = "")
    {
        
        $invoice = TwInvoice::where('meter_id_fk', $meter_id)
            ->with([
                'tw_meter_infos' => function ($query) {
                    $query->select('id', 'user_id', 'meternumber', 'undertake_zone_id', 'undertake_subzone_id', 'metertype_id', 'submeter_name');
                },
                'tw_meter_infos.user' => function ($query) {
                    return $query->select('id', 'prefix', 'firstname', 'lastname', 'address', 'zone_id', 'phone', 'subzone_id', 'tambon_code', 'district_code', 'province_code');
                },
                'tw_meter_infos.user.user_tambon' => function ($query) {
                    return $query->select('id', 'tambon_name');
                },
                'tw_meter_infos.user.user_district' => function ($query) {
                    return $query->select('id', 'district_name');
                },
                'tw_meter_infos.user.user_province' => function ($query) {
                    return $query->select('id', 'province_name');
                },
                'invoice_period' => function ($query) {
                    return $query->select('id', 'inv_p_name', 'budgetyear_id');
                },
                'invoice_period.budgetyear' => function ($query) {
                    return $query->select('id', 'budgetyear_name', 'status');
                },
                'tw_meter_infos.undertake_zone' => function ($query) {
                    return $query->select('id', 'zone_name as undertake_zone_name');
                },
                'tw_meter_infos.undertake_subzone' => function ($query) {
                    return $query->select('id', 'subzone_name as undertake_subzone_name');
                },
                'tw_meter_infos.user.user_zone' => function ($query) {
                    return $query->select('id', 'zone_name as user_zone_name');
                },
                 'tw_meter_infos.meter_type' => function ($query) {
                        $query->select('id');
                    },
                    'tw_meter_infos.meter_type.rateConfigs' => function ($query) {
                        $query->select('*');
                    },
                    'tw_meter_infos.meter_type.rateConfigs.Ratetiers' => function ($query) {
                        $query->select('*');
                    }
            ]);
        if ($status != '') {

            $invoice_history =  $status == 'inv_and_owe' ? $invoice->whereIn('status', ['owe', 'invioce']) : $invoice->where('status', $status);
        }

        $invoice = $invoice->orderBy('inv_period_id_fk', 'desc')->get();

        $invoice_history = TwInvoiceHistoty::where('meter_id_fk', $meter_id)
            ->with([
                'tw_meter_infos' => function ($query) {
                    $query->select('id', 'user_id', 'meternumber', 'undertake_zone_id', 'undertake_subzone_id', 'metertype_id');
                },
                'tw_meter_infos.user' => function ($query) {
                    return $query->select('id', 'prefix', 'firstname', 'lastname', 'address', 'zone_id', 'phone', 'subzone_id', 'tambon_code', 'district_code', 'province_code');
                },
                'tw_meter_infos.user.user_tambon' => function ($query) {
                    return $query->select('id', 'tambon_name');
                },
                'tw_meter_infos.user.user_district' => function ($query) {
                    return $query->select('id', 'district_name');
                },
                'tw_meter_infos.user.user_province' => function ($query) {
                    return $query->select('id', 'province_name');
                },
                'invoice_period' => function ($query) {
                    return $query->select('id', 'inv_p_name', 'budgetyear_id');
                },
                'invoice_period.budgetyear' => function ($query) {
                    return $query->select('id', 'budgetyear_name', 'status');
                },
                'tw_meter_infos.undertake_zone' => function ($query) {
                    return $query->select('id', 'zone_name as undertake_zone_name');
                },
                'tw_meter_infos.undertake_subzone' => function ($query) {
                    return $query->select('id', 'subzone_name as undertake_subzone_name');
                },
                'tw_meter_infos.user.user_zone' => function ($query) {
                    return $query->select('id', 'zone_name as user_zone_name');
                },
                 'tw_meter_infos.meter_type' => function ($query) {
                        $query->select('id');
                    },
                    'tw_meter_infos.meter_type.rateConfigs' => function ($query) {
                        $query->select('*');
                    },
                    'tw_meter_infos.meter_type.rateConfigs.Ratetiers' => function ($query) {
                        $query->select('*');
                    }
            ]);
        if ($status != '') {

            $invoice_history =  $status == 'inv_and_owe' ? $invoice_history->whereIn('status', ['owe', 'invioce']) : $invoice_history->where('status', $status);
        }

        $invoice_history =  $invoice_history->orderBy('inv_period_id_fk', 'desc')->get();


        $invoiceMerge = collect($invoice)->merge($invoice_history);
        return  response()->json(collect($invoiceMerge)->flatten());
    }
}
