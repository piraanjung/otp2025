<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Http\Controllers\Api\FunctionsController;
use App\Http\Controllers\Api\InvoiceController as ApiInvoiceCtrl;
use App\Models\AccTransactions;
use App\Models\Admin\UserProfile;
use App\Models\Cutmeter;
use App\Models\Invoice;
use App\Models\InvoiceHistoty;
use App\Models\InvoicePeriod;
use App\Models\Setting;
use App\Models\Subzone;
use App\Models\UndertakerSubzone;
use App\Models\User;
use App\Models\UserMerterInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Traits\HasRoles;


class InvoiceController extends Controller
{
    private $apiInvoiceCtrl;
    public function __construct(ApiInvoiceCtrl $apiInvoiceCtrl)
    {
        $this->apiInvoiceCtrl = $apiInvoiceCtrl;
    }
    public function index($_budgetyearId = '', $_invPeriod = '')
    {
    //    $usermeterinfo = UserMerterInfo::whereIn('status',['active', 'inactive'])
    //    ->with([
    //     'invoice' => function($q){
    //         return $q->select('meter_id_fk', 'inv_period_id_fk', 'status', 'lastmeter', 'currentmeter',)
    //         ->where('inv_period_id_fk', ">=", 50)->where('status','<>', 'deleted');
    //     }
    //    ])
    //    ->get(['meter_id', 'owe_count', 'status']);
        // $users = User::where('zone_id', 12)->where('status', 1)
        // ->with([
        //     'usermeterinfos' => function($q){
        //         return $q->select('meter_id', 'user_id', 'status')
        //         ->whereIn('status', ['active', 'inactive']);
        //     },
        //     'usermeterinfos.invoice' => function($q){
        //         return $q->select('meter_id_fk', 'inv_period_id_fk', 'status')
        //         ->where('inv_period_id_fk',  51);
        //     }
        // ])
        // ->where('role_id', 3)
        // ->get(['id', 'firstname', 'lastname']);

        // return collect($users)->filter(function($v){
        //     return collect($v->usermeterinfos[0]->invoice)->isNotEmpty();
        // });
       //owecount ติดลบ แต่ usermeterinfo status  = active,inactive และสร้างรอบเดือน 9 แล้ว 
    //     $oweCount_1StatusActive = collect($usermeterinfo)->filter(function($v){
    //     return $v->owe_count < 0 && collect($v->invoice)->count() > 1 ;
    //    });


    //    foreach($oweCount_1StatusActive as $usermeterinfo){
    //         UserMerterInfo::where('meter_id', $usermeterinfo->meter_id)->update([
    //             'owe_count' => 0,
    //             'status' => 'active'
    //         ]);
    //    }  

    //    return  $oweCount_1StatusActive;
       
        //owecount ติดลบ แต่ usermeterinfo status  = inactive และยังไม่สร้างรอบเดือน 9 แล้ว
        // $oweCount_1StatusInActive = collect($usermeterinfo)->filter(function($v){
        //     return $v->owe_count < 0 && collect($v->invoice)->count() == 1 && $v->invoice[0]->inv_period_id_fk == 50;
        //    });
    
        //    foreach($oweCount_1StatusInActive as $usermeterinfo){
        //         UserMerterInfo::where('meter_id', $usermeterinfo->meter_id)->update([
        //             'owe_count' => 0,
        //             'status' => 'active'
        //         ]);
        //         Invoice::create([
        //             'inv_no' => 0,
        //             'inv_period_id_fk'=> 51,
        //             'user_id' =>$usermeterinfo->meter_id,
        //             'meter_id_fk'=>$usermeterinfo->meter_id,
        //             'lastmeter' => $usermeterinfo->invoice[0]->currentmeter,
        //             'currentmeter' => 0,
        //             'water_used' => 0,
        //             'paid' => 0,
        //             'vat'=> 0,
        //             'totalpaid'=> 0,
        //             'status' => 'init',
        //             'acc_trans_id_fk' => 0,
        //             'recorder_id' => Auth::user()->id,
        //             'created_at' => date('Y-m-d H:i:s'),
        //             'updated_at' => date('Y-m-d H:i:s'),
        //        ]);
        //     } 
            // return  'dsf';
        // return $this->addInvP50();
        // return $this->test();
        // return $this->manageOweCount();

        // return $this->changeVatValues();
        //return $this->aa();//สร้าง acc trans id ผูก invoice ที่ถูกยกเลิกใช้งานแล้วแต่ยังติดหนี้อยู่
        // return $this->removePaidStatusToInvHistoryTable();
        // return $this->editPaidValue0();
        //ปรับ owe_count ใน user_meter_infos table ใหม่
        $current_inv_period = InvoicePeriod::where('status', 'active')->get(['id', 'inv_p_name'])->first();

        $invActive = Invoice::where('inv_period_id_fk', $current_inv_period->id)
            ->with([
                'usermeterinfos' => function ($query) {
                    return $query->select('meter_id', 'undertake_zone_id', 'undertake_subzone_id', 'owe_count');
                },
                'usermeterinfos.undertake_zone' => function ($query) {
                    $query->select('id', 'zone_name');
                },
                'usermeterinfos.undertake_subzone' => function ($query) {
                    if (Auth::user()->hasRole('tabwater')) {
                        $undertake_subzones =  collect(UndertakerSubzone::where('twman_id', Auth::user()->id)->get(['id']))->pluck('id');
                        return $query->select('id', 'zone_id', 'subzone_name')
                            ->whereIn('id', $undertake_subzones);
                    }
                    return $query->select('id', 'zone_id', 'subzone_name');
                }
            ])
            ->get();

        $invActiveFilterSubzoneNotNull = collect($invActive)->filter(function ($item) {
            return collect($item->usermeterinfos->undertake_subzone)->isNotEmpty();
        });
        $grouped_inv_by_subzone = collect($invActiveFilterSubzoneNotNull)->groupBy(function ($key) {
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

            $current_inv_period_id = $current_inv_period->id;
            $user_notyet_inv_info = UserMerterInfo::where('undertake_subzone_id', $zone[0]->usermeterinfos->undertake_subzone_id)
                ->with([
                    'invoice' => function ($q) use ($current_inv_period_id) {
                        return $q->select('meter_id_fk')->where('inv_period_id_fk', $current_inv_period_id);
                    }
                ])->where('status', 'active')->get(['undertake_subzone_id',  'meter_id']);
            $user_notyet_inv_info_count = collect($user_notyet_inv_info)->filter(function ($v) {
                return collect($v->invoice)->isEmpty();
            })->count();
            $user_notyet_inv_lessthan49 = collect($user_notyet_inv_info)->filter(function ($v) {
                return collect($v->invoice)->isEmpty();
            });
            $zones->push([
                'zone_id'       => $zone[0]->usermeterinfos->undertake_subzone->zone_id,
                'zone_info'     => $zone[0]->usermeterinfos,
                'members_count' => collect($grouped_inv_by_subzone[$key])->count(),
                'owe_over3'     => collect($grouped_inv_by_subzone[$key])->filter(function ($item) {
                    return $item->usermeterinfos->owe_count >= 3;
                })->count(),
                'initTotalCount' => $initTotalCount,
                'invoiceTotalCount' => $invoiceTotalCount,
                'paidTotalCount' => $paidTotalCount,
                'user_notyet_inv_info' => $user_notyet_inv_info_count,
                'user_notyet_inv_lessthan49' => $user_notyet_inv_lessthan49
            ]);
        }
        $zones = collect($zones)->sortBy('zone_id');


        return view('invoice.index', compact('zones', 'current_inv_period'));
    }

    private function addInvP50()
    {
        // return Invoice::where('meter_id_fk', 1769)->where('inv_period_id_fk' ,'>', 47)->get();
        $invs = Invoice::where('inv_period_id_fk', 50)
        ->where('status','invoice')
        ->where('lastmeter', '2770.00')
        ->get();

        foreach($invs as $inv){
            
            $invHisInP49 = InvoiceHistoty::where('meter_id_fk', $inv->meter_id_fk)
                            ->where('inv_period_id_fk', 49)->get();
            
            // $invHisInP49[0]->currentmeter;
             Invoice::where('inv_id', $inv->inv_id)->update([
                'lastmeter' => $invHisInP49[0]->currentmeter
             ]);

        }
        return 1;
        // $users = User::where('address', 80)
        //     ->where('zone_id', 12)
        //     ->get();

        // foreach ($users as $user) {
            // $usermeterinfos = UserMerterInfo::where('status' , 'inactive')
            //  ->where('cutmeter' , 0)
            //  ->where('comment', '<>', 'ยกเลิกการใช้งาน')
            //     ->get();
            // foreach($usermeterinfos as $user){
                
            //     $invListInPOver47 = Invoice::WHERE('meter_id_fk', $user->meter_id)
            //     ->where('inv_period_id_fk', '>', 47)
            //     ->where('deleted', 0)
            //     ->get();


            //     if (collect($invListInPOver47)->count() == 1) {
            //         if ($invListInPOver47[0]->inv_period_id_fk == 48) {
            //             $invHisInP49 = InvoiceHistoty::where('meter_id_fk', $usermeterinfos[0]->meter_id)
            //                 ->where('inv_period_id_fk', 49)->get();
            //             $invcreate = Invoice::create([
            //                 'inv_no' => 0,
            //                 'inv_period_id_fk' => 50,
            //                 'user_id' => $user->meter_id,
            //                 'meter_id_fk' => $user->meter_id,
            //                 'lastmeter' => $invHisInP49[0]->lastmeter,
            //                 'currentmeter' => 0,
            //                 'water_used' => 0,
            //                 'paid' => 0,
            //                 'vat' => 0,
            //                 'totalpaid' => 0,
            //                 'status' => 'init',
            //                 'deleted' => 0,
            //                 'printed_time' => 0,
            //                 'acc_trans_id_fk' => 0,
            //                 'recorder_id' => 2860,
            //                 'created_at' => date('Y-m-d H:i:s'),
            //                 'updated_at' => date('Y-m-d H:i:s')
            //             ]);
                        
            //             UserMerterInfo::where('meter_id' , $user->meter_id)->update([
            //                 'status' => 'active'
            //             ]);
            //         }
            //     }else{
            //         return collect($usermeterinfos)->count();
            //     }
            // }
            

            
            
        // }
    }

    private function test()
    {
        $arr = [
            36,
            2949,
            2819,
            209,
            374,
            378,
            293,
            295,
            332,
            571,
            548,
            3469,
            546,
            3452,
            635,
            716,
            3111,
            3481,
            1253,
            3201,
            1162,
            1273,
            1364,
            1372,
            1345,
            1383,
            1347,
            1352,
            1378,
            1491,
            1539,
            1527,
            3454,
            1672,
            1673,
            1776,
            1899,
            1990,
            3180,
            1906,
            1796,
            1872,
            1902,
            2196,
            2197,
            2234,
            3059,
            2287,
            2387,
            2495,
            2587,
            2644,
            2668,
            2679,
            2799,

            775,
            3424,
            3314,
            1221,
            1298,
            1330,
            3489,
            1886,
            3212,
            1792,
            1963,
            1756,
            1911,
            1879,
            1816,
            1763,
            2070,
            3251,
            2121,
            3140,
            2280,
            2788,
            2957,
            3425,
            2773,
            2809
        ];
        $infos = [];
        foreach ($arr as $a) {
            $us = UserMerterInfo::where('user_id', $a)
                ->with([
                    'invoice' => function ($q) {
                        return $q->select('meter_id_fk', 'inv_period_id_fk', 'inv_no', 'acc_trans_id_fk', 'currentmeter', 'status');
                    },
                    'invoice_history' => function ($q) {
                        return $q->select('meter_id_fk', 'inv_period_id_fk', 'inv_no', 'acc_trans_id_fk', 'currentmeter', 'status');
                    },
                    'user' => function ($q) {
                        return $q->select('id', 'firstname', 'lastname', 'address', 'zone_id as หมู่');
                    }
                ])
                ->get(['meter_id', 'user_id', 'status']);
            if (collect($us[0]->invoice)->count() > 2) {
                $invoice = collect($us[0]->invoice)->concat($us[0]->invoice_history);
                $inv_filter = collect($invoice)->filter(function ($v) {
                    return $v->inv_period_id_fk > 46;
                });
                array_push($infos, [
                    'name' => $us[0]->user->firstname . " " . $us[0]->user->lastname,
                    'user_id' => $us[0]->meter_id,
                    'inv' => $inv_filter
                ]);
            }
        }
        // $a1 = collect($infos)->filter(function($v){
        //     return collect($v['inv'])->count() ==1;
        // });
        // $a2 = collect($infos)->filter(function($v){
        //     return collect($v['inv'])->count() ==2;
        // });
        $a3 = collect($infos)->filter(function ($v) {
            return collect($v['inv'])->count() == 3;
        });
        // $a4 = collect($infos)->filter(function($v){
        //     return collect($v['inv'])->count() ==4;
        // });
        // $a5 = collect($infos)->filter(function($v){
        //     return collect($v['inv'])->count() ==5;
        // });
        // $a0 = collect($infos)->filter(function($v){
        //     return collect($v['inv'])->count() ==0;
        // });
        // $aover5 = collect($infos)->filter(function($v){
        //     return collect($v['inv'])->count() > 5;
        // });
        // return $a3;
        foreach ($a3 as $a) {
            $a_inv_values = collect($a['inv'])->values();
            $invp48_inv_no = $a_inv_values[1]->inv_no;
            $invp49 = $a_inv_values[2];
            if ($invp49->inv_period_id_fk == 49) {

                Invoice::create([
                    'inv_no' => 0,
                    'user_id'           => $invp49->meter_id_fk,
                    'inv_period_id_fk' => 50,
                    'lastmeter' => $invp49->currentmeter,
                    'currentmeter'      => 0,
                    'status'            => 'init',
                    'vat' => 0,
                    'acc_trans_id_fk'   => 0,
                    'recorder_id'       => Auth::user()->id,
                    'created_at'        => date('Y-m-d H:i:s'),
                    'updated_at'        => date('Y-m-d H:i:s'),
                ]);
            } else {
                return $a;
            }
        }
        // return [
        //     'all' => collect($infos)->count(),
        //     'a0' => ['count' =>collect($a0)->count(), 'res'=>$a0],
        //    'a1' => ['count' =>collect($a1)->count(), 'res'=>$a1],
        //    'a2' => ['count' =>collect($a2)->count(), 'res'=>$a2],
        //    'a3' => ['count' =>collect($a3)->count(), 'res'=>$a3],
        //    'a4' => ['count' =>collect($a4)->count(), 'res'=>$a4],
        //    'a5' => ['count' =>collect($a5)->count(), 'res'=>$a5],
        //    'aover5' => ['count' =>collect($aover5)->count(), 'res'=>$aover5],
        // ];
    }

    private function editPaidValue0()
    {
        $invoicePaid0 = Invoice::where('paid', 0)
            ->where('status', '<>', 'init')
            ->get(['inv_id', 'meter_id_fk', 'inv_period_id_fk', 'lastmeter', 'currentmeter', 'inv_type', 'water_used', 'paid', 'vat', 'totalpaid', 'status']);
        foreach ($invoicePaid0 as $inv) {
            $water_used = $inv->currentmeter - $inv->lastmeter;
            $paid = $water_used == 0 ? 10 : $water_used * 8;
            $vat = $water_used == 0 ? 0.7 : $paid * 0.07;
            $inv_type = $water_used == 0 ? 'r' : 'u';
            Invoice::where('inv_id', $inv->inv_id)->update([
                'water_used' => $water_used,
                'paid' => $paid,
                'vat' => $vat,
                'inv_type' => $inv_type,
                'totalpaid' => $paid + $vat
            ]);
        }

        $InvoiceHistotyPaid0 = InvoiceHistoty::where('paid', 0)
            ->where('status', '<>', 'init')
            ->get(['inv_id', 'meter_id_fk', 'inv_period_id_fk', 'lastmeter', 'currentmeter', 'inv_type', 'water_used', 'paid', 'vat', 'totalpaid', 'status']);
        foreach ($InvoiceHistotyPaid0 as $inv) {
            $water_used = $inv->currentmeter - $inv->lastmeter;
            $paid = $water_used == 0 ? 10 : $water_used * 8;
            $vat = $water_used == 0 ? 0.7 : $paid * 0.07;
            $inv_type = $water_used == 0 ? 'r' : 'u';
            InvoiceHistoty::where('inv_id', $inv->inv_id)->update([
                'water_used' => $water_used,
                'paid' => $paid,
                'vat' => $vat,
                'inv_type' => $inv_type,
                'totalpaid' => $paid + $vat
            ]);
        }
    }

    private function removePaidStatusToInvHistoryTable()
    {
        $inv_p = 44;
        $inv = Invoice::where('inv_period_id_fk', $inv_p)->where('status', 'paid')->get('inv_id');
        $arrV = [];
        foreach ($inv as $v) {
            $arrV[] = $v->inv_id;
        }
        $invH =  InvoiceHistoty::where('inv_period_id_fk', $inv_p)->where('status', 'paid')->get('inv_id');
        $arr = [];
        foreach ($invH as $h) {
            if (in_array($h->inv_id, $arrV)) {
                $arr[] = $h->inv_id;
            }
        }
        foreach ($arr as $a) {
            InvoiceHistoty::where('inv_id', $a)->delete();
        }
        $copyInvPaidStatusArr = [];
        $invoicePaid = Invoice::where('inv_period_id_fk', $inv_p)->where('status', 'paid')->get();
        foreach (collect($invoicePaid)->chunk(500) as $k => $vv) {
            foreach ($vv as $v) {
                $copyInvPaidStatusArr[] = [
                    'inv_id'           => $v->inv_id,
                    'user_id'          => $v->user_id,
                    'meter_id_fk'      => $v->meter_id_fk,
                    'inv_period_id_fk' => $v->inv_period_id_fk,
                    'lastmeter'        => $v->lastmeter,
                    'currentmeter'     => $v->currentmeter,
                    'water_used'       => $v->water_used,
                    'inv_type'         => $v->inv_type,
                    'paid'             => $v->paid,
                    'vat'              => $v->vat,
                    'totalpaid'        => $v->totalpaid,
                    'status'           => $v->status,
                    'acc_trans_id_fk'  => $v->acc_trans_id_fk,
                    'comment'          => $v->comment,
                    'recorder_id'      => $v->recorder_id,
                    'created_at'       => $v->created_at,
                    'updated_at'       => $v->updated_at
                ];
            }
        }
        InvoiceHistoty::insert($copyInvPaidStatusArr);
        //ทำการลบ invoice status paid ของรอบบิลก่อน ที่ copy ไปที่ invoice_history แล้ว
        Invoice::where('inv_period_id_fk', $inv_p)->where('status', 'paid')->delete();
        return 222;
    }

    private function aa()
    {
        $userM = UserMerterInfo::with(['invoice' => function ($q) {
            return $q->select('inv_id', 'meter_id_fk', 'status', 'acc_trans_id_fk');
        }])
            ->where('status', '<>', 'active')->get(['user_id', 'meter_id']);
        $userMFilter = collect($userM)->filter(function ($v) {
            return collect($v->invoice)->isNotEmpty();
        });
        foreach ($userMFilter as $user) {
            $newAccTrans = AccTransactions::create([
                'user_id_fk' => $user->meter_id,
                'vatsum' => 0,
                'paidsum' => 0,
                'totalpaidsum' => 0,
                'net' => 0,
                'cashier' => Auth::user()->id,
                'status' => 2,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),

            ]);
            foreach ($user->invoice as $inv) {
                if ($inv->status == 'owe' || $inv->status == 'invoice') {
                    Invoice::where('inv_id', $inv->inv_id)->update([
                        'acc_trans_id_fk' => $newAccTrans->id,
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                }
            }
        }
        return $userMFilter;
    }

    public function paid($id)
    {
        $inv = $this->apiInvoiceCtrl->get_user_invoice($id);
        $invoice = json_decode($inv->getContent());
        // dd($invoice);
        return view('invoice.paid', compact('invoice'));
    }

    public function zone_create(Request $request, $subzone_id, $curr_inv_prd, $new_user = 0)
    {
        $member_not_yet_recorded_present_inv_period = [];
        $invoices = [];
        if ($new_user > 0) {
            $subzone_members = UserMerterInfo::where('undertake_subzone_id', $subzone_id)
                ->where('status', 'active')
                ->with([
                    'invoice' => function ($query) use ($curr_inv_prd) {
                        return $query->select('inv_period_id_fk', 'currentmeter', 'inv_id', 'lastmeter', 'status', 'meter_id_fk')
                            //    ->where('inv_period_id_fk', '=', $curr_inv_prd);
                            ->whereIn('inv_period_id_fk', [49, 50]);
                    },
                ])
                ->get(['meter_id', 'undertake_subzone_id', 'meternumber', 'user_id', 'metertype_id']);

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
                        "iv_id" => 0,
                    ]);
                }
            }
            $member_not_yet_recorded_present_inv_period[] = collect($member_inv_isEmpty_filtered)->values();
        } else {
            $curr_inv_init_status = Invoice::where(['inv_period_id_fk' => $curr_inv_prd, 'status' => 'init'])
                ->with([
                    'usermeterinfos' => function ($query) use ($subzone_id) {
                        $query->select('meter_id', 'undertake_subzone_id', 'user_id', 'metertype_id', 'meternumber')
                            ->where('undertake_subzone_id', $subzone_id);
                    },
                    'usermeterinfos.meter_type' => function ($query) {
                        $query->select('id', 'price_per_unit');
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
        $subzone = Subzone::find($subzone_id);
        $invoice_remain = collect($invoices)->count();
        return view('invoice.zone_create', compact('invoices', 'invoice_remain', 'subzone', 'member_not_yet_recorded_present_inv_period'));
    }

    public function store(REQUEST $request)
    {

        date_default_timezone_set('Asia/Bangkok');
        //filter หาเฉพาะที่มีการกรอกข้อมูลขมิเตอร์ปัจจุบัน
        $filters = collect($request->get('data'))->filter(function ($val) {
            return $val['currentmeter'] > 0;
        });
        //เพิ่มข้อมูลลง invoice
        $setting_vat    = Setting::where('name', 'vat')->first();
        $inv_period_table   = InvoicePeriod::where('status', 'active')->get(['id'])->first();
        $price_per_unit = 8;
        foreach ($filters as $inv) {
            $water_used     = $inv['currentmeter'] - $inv['lastmeter'];
            $paid           = $water_used == 0 ? 10 : $water_used * $price_per_unit;
            $vat            = ($paid * $setting_vat->values) / 100;
            $totalPaid      = $paid + $vat;
            $inv_type       =  $water_used == 0 ? "r" : "u";
            $dataArray = [
                'inv_period_id_fk' => $inv_period_table->id,
                'user_id' => $inv['user_id'],
                'meter_id_fk' => $inv['meter_id'],
                'lastmeter'   => $inv['lastmeter'],
                'currentmeter' => $inv['currentmeter'],
                'water_used'  => $water_used,
                'inv_type'    => $inv_type,
                'paid'        => $paid,
                'vat'         => $vat,
                'totalpaid'   => $totalPaid,
                'status'      => 'invoice',
                'recorder_id' => Auth::id(),
                'updated_at'  => date('Y-m-d H:i:s'),
            ];
            if ($inv['inv_id'] == 'new_inv') {
                Invoice::insert($dataArray);
            } else {
                Invoice::where('inv_id', $inv['inv_id'])
                    ->update([
                        'lastmeter'   => $inv['lastmeter'],
                        'currentmeter' => $inv['currentmeter'],
                        'water_used'  => $water_used,
                        'inv_type'    => $inv_type,
                        'paid'        => $paid,
                        'vat'         => $vat,
                        'totalpaid'   => $totalPaid,
                        'status'      => 'invoice',
                        'recorder_id' => Auth::id(),
                        'updated_at'  => date('Y-m-d H:i:s'),
                    ]);
            }
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
            'inv_id' => 'required',
        ], [
            'required' => 'ยังไม่ได้เลือกแถวที่ต้องการปริ้น',
        ]);
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
        $userMeterInfos = UserMerterInfo::where('undertake_subzone_id', $subzone_id)
            ->with([
                'invoice' => function ($query) {
                    return $query->select('meter_id_fk', 'inv_id', 'status', 'lastmeter', 'currentmeter', 'water_used', 'paid', 'vat', 'inv_type', 'totalpaid')
                        ->where('status', 'invoice');
                },
                'meter_type' => function ($query) {
                    return $query->select('id', 'price_per_unit');
                }
            ])
            ->get(['meter_id', 'undertake_subzone_id', 'user_id', 'metertype_id', 'meternumber', 'metertype_id']);

        if (collect($userMeterInfos)->isEmpty()) {
            return redirect('invioce.index');
        }
        $inv_in_seleted_subzone = collect($userMeterInfos)->filter(function ($value, $key) {
            return collect($value->invoice)->isNotEmpty();
        })->values();

        // $inv_in_seleted_subzone = collect($inv_status_invoice)->filter(function ($value, $key) use ($subzone_id) {
        //     if (collect($value->usermeterinfos)->isNotEmpty())
        //         return $value->usermeterinfos->undertake_subzone_id == $subzone_id;
        // })->values();
        return view('invoice.zone_edit', compact('inv_in_seleted_subzone', 'subzone_id'));
    }

    public function reset_invioce_bill($inv_id)
    {
        Invoice::where('inv_id', $inv_id)->update([
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
        // $filters_changeValue = collect($filters_all)->filter(function ($val) {
        //     return $val['changevalue'] == 1;
        // });
        // $filters_delete_status = collect($filters_all)->filter(function ($val) {
        //     return $val['status'] == 'delete';
        // });
        // $filters_init_status = collect($filters_all)->filter(function ($val) {
        //     return $val['status'] == 'init';
        // });
        if (collect($filters_changeValue)->count() > 0) {
            foreach ($filters_changeValue  as $vals) {
                $invoice = invoice::where('inv_id', $vals['inv_id'])->update([
                    "currentmeter"  => $vals['currentmeter'],
                    "lastmeter"     => $vals['lastmeter'],
                    'paid'          => $vals['paid'],
                    'inv_type'      => $vals['water_used'] == 0 ? 'r' : 'u',
                    'water_used'    => $vals['water_used'],
                    'vat'           => $vals['vat'],
                    'totalpaid'     => $vals['totalpaid'],
                    "recorder_id"   => Auth::id(),
                    "comment"       => '',
                    "updated_at"    => date('Y-m-d H:i:s'),
                ]);
            }
        }
        // if (collect($filters_delete_status)->count() > 0) {
        //     foreach ($filters_delete_status as $key => $vals) {
        //         $invoice = invoice::where('id', $key)->update([
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
        //         $invoice = invoice::where('id', $key)->update([
        //             "currentmeter" => 0,
        //             "status" => 'init',
        //             "recorder_id" => Auth::id(),
        //             "comment" => $vals['comment'],
        //             "updated_at" => date('Y-m-d H:i:s'),
        //         ]);
        //     }
        // }
        return \redirect('invoice')->with([
            'message' => 'ทำการบันทึกการแก้ไขข้อมูลเรียบร้อยแล้ว',
            'color' => 'success',
        ]);
    }

    private function setInvoiceVals($lastmeter, $currentmeter)
    {
        $water_used     = $currentmeter - $lastmeter;
        $paid           = $water_used == 0 ? 10 : $water_used * 8;
        $vat            = ($paid * 0.07) / 100;
        $totalPaid      = $paid + $vat;
        $inv_type       =  $water_used == 0 ? "r" : "u";
        return [
            "water_used"    => $water_used,
            "paid"          => $paid,
            "inv_type"      => $inv_type,
            "totalPaid"     => $totalPaid,
            "vat"           => $vat,
        ];
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
                $invoice = invoice::where('id', $key)->update([
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
        //         $invoice = invoice::where('id', $key)->update([
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
        //         $invoice = invoice::where('id', $key)->update([
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

    private function manageOweCount()
    {
        $aa = UserMerterInfo::with([
            'invoice' => function ($query) {
                return $query->select('inv_period_id_fk', 'status', 'meter_id_fk')
                    ->whereIn('status', ['owe', 'invoice']);
            },
        ])->get(['meter_id', 'user_id', 'owe_count', 'status']);

        $arr = collect([]);
        $i = 1;
        foreach ($aa as $a) {
            //นับ invoice status เท่ากับ owe หรือ invioce แล้วทำการ update
            //owe_count ให้  user_meter_infos ใหม่
            $oweInvCount = collect($a->invoice)->count(); // + 1; // บวก row  status == invoice

            UserMerterInfo::where('meter_id', $a->meter_id)->update([
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
                Cutmeter::create([
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

    public function changeVatValues()
    {
        $invoices = Invoice::all();
        foreach ($invoices as $inv) {
            $vat = $inv->paid == 10 ? 0.07 : number_format($inv->paid * 0.07, 2);
            Invoice::where('inv_id', $inv->inv_id)->update([
                'inv_id'    => $inv->inv_id,
                'vat'       => $vat,
                'totalpaid' => $inv->paid + $vat
            ]);
        }
    }
}
