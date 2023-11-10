<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subzone;
use App\Models\UserMerterInfo;
use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Api\ReportsController as apiReportCtrl;
class OwepaperController extends Controller
{
    public function index(REQUEST $request)
    {
        date_default_timezone_set('Asia/Bangkok');
        $fnCtrl = new FunctionsController();
        $apiReportCtrl = new apiReportCtrl();
        if (collect($request)->isEmpty()) {
            $a = [
                'invperiodstart' => 'all',
                'invperiodend' => 'all',
                'zone_id' => 'all',
                'subzone_id' => 'all',
                'type' => 'payment',
            ];
            $request->merge($a);
        }
        //หา user  ที่ invoice.status  เป็นowe หรือ invoice, init
        $oweInfosArr = DB::table('user_meter_infos as umf')
            ->join('invoice as iv', 'iv.user_id', '=', 'umf.user_id')
            ->join('user_profile as upf', 'upf.user_id', '=', 'umf.user_id')
            ->join('zone as z', 'z.id', '=', 'upf.zone_id')
            ->join('subzone as udt_sz', 'udt_sz.id', '=', 'umf.undertake_subzone_id')
            ->join('zone as udt_z', 'udt_z.id', '=', 'umf.undertake_zone_id')
            ->whereIn('umf.status', ['active', 'cutmeter'])
            ->where('umf.deleted', 0)
            ->where('iv.deleted', '=', 0);
        if ($request->get('type') == 'payment-search') {
            $oweInfosArr = $oweInfosArr->where('iv.receipt_id', '>', 0);
        } else {
            //type เป็น payment
            $oweInfosArr = $oweInfosArr->whereIn('iv.status', ['owe', 'invoice', 'init']);
        }
        if ($request->get('zone_id') != 'all' && $request->get('subzone_id') != 'all') {
            $oweInfosArr = $oweInfosArr->where('umf.undertake_subzone_id', '=', $request->get('subzone_id'));
            $oweInfosArr = $oweInfosArr->where('umf.undertake_zone_id', '=', $request->get('zone_id'));

        }

        $oweInfosArr = $oweInfosArr->select(
            'umf.owe_count',
            'upf.name', 'upf.address',
            'z.zone_name',
            'udt_sz.subzone_name',
            'umf.meternumber', 'umf.user_id',
            'umf.comment'
        )
            ->groupBy('umf.user_id')
            ->get();

        return $oweInfosArr;

    }

    public function owe2(REQUEST $request)
    {
        date_default_timezone_set('Asia/Bangkok');
        $fnCtrl = new FunctionsController();
        $apiReportCtrl = new apiReportCtrl();
        if (collect($request)->isEmpty()) {
            $a = [
                'invperiodstart' => 'all',
                'invperiodend' => 'all',
                'zone_id' => 'all',
                'subzone_id' => 'all',
                'type' => 'payment',
            ];
            $request->merge($a);
        }

        $apiReportsCtrl = new ReportsController();
        // return $water_used_history = (json_decode($apiReportsCtrl->meter_record_history($currentBudgetYear->id, 'all')->content(), true));

        //หา user  ที่ invoice status owe และ invoice
        $oweInfosArr = DB::table('user_meter_infos as umf')
            ->join('invoice as iv', 'iv.user_id', '=', 'umf.user_id')
            ->join('invoice_period as ivp', 'ivp.id', '=', 'iv.inv_period_id')
            ->join('user_profile as upf', 'upf.user_id', '=', 'umf.user_id')
            ->join('zone as z', 'z.id', '=', 'upf.zone_id')
        // ->join('subzone as sz', 'sz.id', '=', 'upf.subzone_id')
            ->join('subzone as udt_sz', 'udt_sz.id', '=', 'umf.undertake_subzone_id')
            ->join('zone as udt_z', 'udt_z.id', '=', 'umf.undertake_zone_id')
            ->whereIn('umf.status', ['active', 'cutmeter'])
            ->where('umf.deleted', '=', 0)
            ->where('iv.deleted', '=', 0);

        if ($request->get('type') == 'payment-search') {
            $oweInfosArr = $oweInfosArr->where('iv.receipt_id', '>', 0);
        } else {
            //type เป็น payment
            $oweInfosArr = $oweInfosArr->whereIn('iv.status', ['owe', 'invoice']);
        }

        if ($request->get('zone_id') != 'all') {
            if ($request->get('subzone_id') != 'all') {
                $oweInfosArr = $oweInfosArr->where('umf.undertake_subzone_id', '=', $request->get('subzone_id'));
            } else if ($request->get('subzone_id') == 'all') {
                $oweInfosArr = $oweInfosArr->where('umf.undertake_zone_id', '=', $request->get('zone_id'));
            }
        }

        return $oweInfosArr = $oweInfosArr->select(
            'upf.name', 'upf.address',
            'z.zone_name',
            'udt_sz.subzone_name',
            'umf.meternumber', 'umf.user_id',
            'upf.address', 'z.zone_name',
            DB::raw('CONCAT(udt_z.zone_name," - ",udt_sz.subzone_name) as undertakezoneAndSubzone'),
            'iv.status', 'iv.inv_period_id', 'iv.id as iv_id', 'iv.lastmeter', 'iv.currentmeter',
            'ivp.inv_period_name'
        )
            ->orderBy('umf.user_id', 'asc')
        // ->groupBy('umf.user_id')
            ->get();

        $owes_grouped = collect($oweInfosArr)->groupBy('user_id')->values();
        $arr = collect([]);
        foreach ($owes_grouped as $key => $owes) {
            $total = 0;
            $res = collect([]);
            $sum_reserve_paid = 0;
            $sum_paid = 0;
            foreach ($owes as $owe) {
                $water_used = $owe->currentmeter - $owe->lastmeter;
                $reserve_paid = $water_used > 0 ? 0 : 10;
                $paid = $water_used > 0 ? $water_used * 8 : 0;
                $sum_paid += $paid;
                $sum_reserve_paid += $reserve_paid;
                $total += $paid + $reserve_paid;
                $results = [
                    'invoice_id' => $owe->iv_id,
                    'inv_period_name' => $owe->inv_period_name,
                    'lastmeter' => $owe->lastmeter,
                    'currentmeter' => $owe->currentmeter,
                    'water_used' => $water_used,
                    'reserve_paid' => $reserve_paid,
                    'paid' => $paid,
                    'paid_net' => $paid + $reserve_paid,

                ];
                $res->push($results);
            }
            $arr->push([
                'name' => $owes[0]->name,
                'user_id' => $owes[0]->user_id,
                'meternumber' => $owes[0]->meternumber,
                'address' => $owes[0]->address,
                'zone_name' => $owes[0]->zone_name,
                'subzone_name' => $owes[0]->subzone_name,
                'address' => $owes[0]->address,
                // 'userAddress' => $owes[0]->userAddress,
                // 'undertakezoneAndSubzone' => $owes[0]->undertakezoneAndSubzone,
                'oweInvCount' => collect($owes)->count(),
                'sum_paid' => $sum_paid,
                'sum_reserve_paid' => $sum_reserve_paid,
                'total' => $total,
                'empty' => '',
                'results' => $res,
            ]);
        }

        return $arr;

    }

    public function owe(REQUEST $request)
    {
        date_default_timezone_set('Asia/Bangkok');
        $fnCtrl = new FunctionsController();
        $apiReportCtrl = new apiReportCtrl();
        if (collect($request)->isEmpty()) {
            $a = [
                'invperiodstart' => 'all',
                'invperiodend' => 'all',
                'zone_id' => 'all',
                'subzone_id' => 'all',
                'type' => 'payment',
            ];
            $request->merge($a);
        }
        //หา user  ที่ invoice status owe และ invoice
        $oweInfosArr = DB::table('user_meter_infos as umf')
            ->join('invoice as iv', 'iv.user_id', '=', 'umf.user_id')
            ->join('invoice_period as ivp', 'ivp.id', '=', 'iv.inv_period_id')
            ->join('user_profile as upf', 'upf.user_id', '=', 'umf.user_id')
            ->join('zone as z', 'z.id', '=', 'upf.zone_id')
        // ->join('subzone as sz', 'sz.id', '=', 'upf.subzone_id')
            ->join('subzone as udt_sz', 'udt_sz.id', '=', 'umf.undertake_subzone_id')
            ->join('zone as udt_z', 'udt_z.id', '=', 'umf.undertake_zone_id')
            ->whereIn('umf.status', ['active', 'cutmeter'])
            ->where('iv.deleted', '=', 0);

        if ($request->get('type') == 'payment-search') {
            $oweInfosArr = $oweInfosArr->where('iv.receipt_id', '>', 0);
        } else {
            //type เป็น payment
            $oweInfosArr = $oweInfosArr->whereIn('iv.status', ['owe', 'invoice']);
        }

        if ($request->get('zone_id') != 'all') {
            if ($request->get('subzone_id') != 'all') {
                $oweInfosArr = $oweInfosArr->where('umf.undertake_subzone_id', '=', $request->get('subzone_id'));
            } else if ($request->get('subzone_id') == 'all') {
                $oweInfosArr = $oweInfosArr->where('umf.undertake_zone_id', '=', $request->get('zone_id'));
            }
        }

        $oweInfosArr = $oweInfosArr->select(
            'upf.name', 'upf.address',
            'z.zone_name',
            'udt_sz.subzone_name',
            'umf.meternumber', 'umf.user_id',
            'upf.address', 'z.zone_name',
            DB::raw('CONCAT(udt_z.zone_name," - ",udt_sz.subzone_name) as undertakezoneAndSubzone'),
            'iv.status', 'iv.inv_period_id', 'iv.id as iv_id', 'iv.lastmeter', 'iv.currentmeter',
            'ivp.inv_period_name'
        )
            ->orderBy('umf.user_id', 'asc')
        // ->groupBy('umf.user_id')
            ->get();

        $owes_grouped = collect($oweInfosArr)->groupBy('user_id')->values();
        $arr = collect([]);
        foreach ($owes_grouped as $key => $owes) {
            $total = 0;
            $res = collect([]);
            $sum_reserve_paid = 0;
            $sum_paid = 0;
            foreach ($owes as $owe) {
                $water_used = $owe->currentmeter - $owe->lastmeter;
                $reserve_paid = $water_used > 0 ? 0 : 10;
                $paid = $water_used > 0 ? $water_used * 8 : 0;
                $sum_paid += $paid;
                $sum_reserve_paid += $reserve_paid;
                $total += $paid + $reserve_paid;
                $results = [
                    'invoice_id' => $owe->iv_id,
                    'inv_period_name' => $owe->inv_period_name,
                    'lastmeter' => $owe->lastmeter,
                    'currentmeter' => $owe->currentmeter,
                    'water_used' => $water_used,
                    'reserve_paid' => $reserve_paid,
                    'paid' => $paid,
                    'paid_net' => $paid + $reserve_paid,

                ];
                $res->push($results);
            }
            $arr->push([
                'name' => $owes[0]->name,
                'user_id' => $owes[0]->user_id,
                'meternumber' => $owes[0]->meternumber,
                'address' => $owes[0]->address,
                'zone_name' => $owes[0]->zone_name,
                'subzone_name' => $owes[0]->subzone_name,
                'address' => $owes[0]->address,
                // 'userAddress' => $owes[0]->userAddress,
                // 'undertakezoneAndSubzone' => $owes[0]->undertakezoneAndSubzone,
                'oweInvCount' => collect($owes)->count(),
                'sum_paid' => $sum_paid,
                'sum_reserve_paid' => $sum_reserve_paid,
                'total' => $total,
                'empty' => '',
                'results' => $res,
            ]);
        }

        return $arr;

    }

    public function get_reciepting(REQUEST $request)
    {
        date_default_timezone_set('Asia/Bangkok');
        $fnCtrl = new FunctionsController();
        $apiReportCtrl = new apiReportCtrl();
        if (collect($request)->isEmpty()) {
            $a = [
                'invperiodstart' => 'all',
                'invperiodend' => 'all',
                'zone_id' => 'all',
                'subzone_id' => 'all',
            ];
            $request->merge($a);
        }
        //หา user  ที่ invoice status owe และ invoice
        $oweInfosArr = DB::table('user_meter_infos as umf')
            ->join('invoice as iv', 'iv.user_id', '=', 'umf.user_id')
            ->join('user_profile as upf', 'upf.user_id', '=', 'umf.user_id')
            ->join('zone as z', 'z.id', '=', 'upf.zone_id')
            ->join('subzone as sz', 'sz.id', '=', 'upf.subzone_id')
            ->join('subzone as udt_sz', 'udt_sz.id', '=', 'umf.undertake_subzone_id')
            ->join('zone as udt_z', 'udt_z.id', '=', 'umf.undertake_zone_id')
            ->where('umf.status', '=', 'active')
            ->whereIn('iv.status', ['owe', 'invoice']);
        if ($request->get('zone_id') != 'all') {
            if ($request->get('subzone_id') != 'all') {
                $oweInfosArr = $oweInfosArr->where('umf.undertake_subzone_id', '=', $request->get('subzone_id'));
            } else if ($request->get('subzone_id') == 'all') {
                $oweInfosArr = $oweInfosArr->where('umf.undertake_zone_id', '=', $request->get('zone_id'));
            }
        }

        $oweInfosArr = $oweInfosArr->select(
            DB::raw('count(*)  as oweInvCount'),
            'upf.name', 'upf.address',
            'z.zone_name', 'sz.subzone_name',
            'umf.meternumber', 'umf.user_id',
            DB::raw('CONCAT(upf.address,"  ",z.zone_name) as userAddress'),
            DB::raw('CONCAT(udt_z.zone_name," - ",udt_sz.subzone_name) as undertakezoneAndSubzone'),
            'iv.status', 'iv.inv_period_id'

        )
        // ->orderBy('umf.user_id', 'asc')
            ->groupBy('umf.user_id')
            ->get();

        return $oweInfosArr;

    }

    public function oweAndInvoiceCount()
    {
        $owes = DB::table('user_meter_infos as umf')
            ->join('invoice as iv', 'iv.user_id', '=', 'umf.user_id')
            ->join('user_profile as upf', 'upf.user_id', '=', 'umf.user_id')
            ->join('zone as z', 'z.id', '=', 'upf.zone_id')
            ->join('subzone as sz', 'sz.id', '=', 'upf.subzone_id')
            ->whereIn('umf.status', ['active', 'changemeter', 'cutmeter'])
            ->whereIn('iv.status', ['owe', 'invoice', 'init'])
            ->where('iv.deleted', 0)
            ->select(
                // 'iv.user_id', 'iv.status',
                DB::raw('count(*)  as oweInvCount')
            )
            ->groupBy('umf.user_id')
            ->get();

        return collect($owes)->count();
    }

    public function receiptedCount()
    {
        $reciepteds = DB::table('user_meter_infos as umf')
            ->join('invoice as iv', 'iv.user_id', '=', 'umf.user_id')
            ->join('user_profile as upf', 'upf.user_id', '=', 'umf.user_id')
            ->join('zone as z', 'z.id', '=', 'upf.zone_id')
            ->join('subzone as sz', 'sz.id', '=', 'upf.subzone_id')
            ->where('umf.status', '=', 'active')
            ->where('iv.receipt_id', '>', 0)
            ->select(
                DB::raw('count(*)  as oweInvCount')
            )
            ->groupBy('umf.user_id')
            ->get();
        return collect($reciepteds)->count();
    }

    public function user_owe_infos($user_id)
    {
        date_default_timezone_set('Asia/Bangkok');

        $fnCtrl = new FunctionsController();

        //query string หา owe ทั้งหมด
        $owes = DB::table('invoice as iv')
            ->join('user_profile as uf', 'uf.user_id', '=', 'iv.user_id')
            ->join('user_meter_infos as umf', 'umf.user_id', '=', 'iv.user_id')
            ->join('zone', 'zone.id', '=', 'umf.undertake_zone_id')
            ->join('subzone', 'subzone.id', '=', 'umf.undertake_subzone_id')
            ->join('invoice_period as ivp', 'ivp.id', '=', 'iv.inv_period_id')
            ->select(
                'iv.meter_id', 'iv.user_id', 'iv.id', 'iv.currentmeter', 'iv.lastmeter',
                DB::raw('(iv.currentmeter - iv.lastmeter) as water_used'), 'iv.status as iv_status',
                DB::raw('(iv.currentmeter - iv.lastmeter)*8 as mustpaid'),
                'uf.name', 'uf.address',
                'zone.zone_name',
                'subzone.subzone_name', 'umf.undertake_subzone_id',
                'umf.meternumber',
                'ivp.inv_period_name',
            )
            ->where('iv.user_id', '=', $user_id)
            ->whereIn('iv.status', ['owe', 'invoice'])
            ->get();
        $text = '<table class="table table-striped ml-2 mr-2" style="border:1px solid blue" width="80%">';
        $text .= '<thead><tr>';
        $text .= '<th class="bg-blue text-center">รอบบิล</th>';
        $text .= '<th class="bg-blue text-center">สถานะ</th>';
        $text .= '<th class="bg-blue text-center">ยอดปัจจุบัน</th>';
        $text .= '<th class="bg-blue text-center">ยอดครั้งก่อน</th>';
        $text .= '<th class="bg-blue text-center">จำนวนที่ใช้(หน่วย)</th>';
        $text .= '<th class="bg-blue text-center">คิดเป็นเงิน</th>';
        $text .= '</tr></thead>';
        $text .= '<tbody>';
        foreach ($owes as $owe) {
            $status = $owe->iv_status == 'owe' ? 'ค้างชำระ' : 'กำลังออกใบแจ้งหนี้';
            $statusColor = $owe->iv_status == 'owe' ? 'text-danger' : 'text-info';
            $text .= '<tr>';
            $text .= '<td class="text-right">' . $owe->inv_period_name . '</td>';
            $text .= '<td class="text-center ' . $statusColor . '">' . $status . '</td>';
            $text .= '<td class="text-right">' . number_format($owe->currentmeter) . '</td>';
            $text .= '<td class="text-right">' . number_format($owe->lastmeter) . '</td>';
            $text .= '<td class="text-right">' . number_format($owe->water_used) . '</td>';
            $text .= '<td class="text-right">' . number_format($owe->mustpaid) . '</td>';
            $text .= '</tr>';
        }
        $text .= '</tbody>';
        $text .= '</table>';
        return response()->json($text);
    }

    public function test(REQUEST $request)
    {
        // return $this->testOweAndInvoioceDivideBySubzone($request);
        return $this->testIndex($request);

    }

    public function testIndex(REQUEST $request)
    {

        $startTime = microtime(true);
        $oweAndInvoiceDivideBySubzoneArray = collect([]);
        $a = $this->indexFilterOweAndInvoiceCount($request->merge(['zone_id' => 'all', 'subzone_id' => 'all']));
        $a11 = collect($a)->groupBy('undertake_subzone_id');
        $oweAndInvoiceDivideBySubzoneArray->push(
            [
                'zone_id' => 'all',
                'subzone_id' => 'all',
                'zoneName' => 'ทั้งหมด',
                'subzoneName' => 'ทั้งหมด',
                'oweCount' => collect($a)->count(),
            ]
        );
        foreach ($a11 as $a1) {
            // $this->testIndex()
            // return $a1[0]['subzone'];
            $oweAndInvoiceDivideBySubzoneArray->push(
                [
                    'zone_id' => $a1[0]['zone']->id,
                    'subzone_id' => $a1[0]['subzone']->id,
                    'zoneName' => $a1[0]['zone']->zone_name,
                    'subzoneName' => $a1[0]['subzone']->subzone_name,
                    'oweCount' => collect($a1)->count(),
                ]
            );
        }
       return "Time:  " . number_format((microtime(true) - $startTime), 4) . " Seconds\n";

        $zone_id = $request->get('zone_id');
        $subzone_id = $request->get('subzone_id');
        $fnCtrl = new FunctionsController();
        $apiReportCtrl = new ReportsController();
        if (collect($request)->isEmpty()) {
            $a = [
                'invperiodstart' => 'all',
                'invperiodend' => 'all',
                'zone_id' => 'all',
                'subzone_id' => 'all',
                'type' => 'payment',
            ];
            $request->merge($a);
        }
        $type = $request->get('type');
        $findOweAndInvoiceStatusInInvioceTable = UserMerterInfo::whereIn('status', ['active', 'cutmeter'])->where('deleted', 0)
            ->with([
                'user_profile' => function ($query) {
                    return $query->select('name', 'user_id', 'address');
                },
                'invoice' => function ($query) use ($type) {
                    $query = $query->select('status', 'user_id');
                    if ($type == 'payment-search') {
                        $query = $query->where('receipt_id', '>', 0);
                    } else {
                        $query = $query->whereIn('status', ['owe', 'invoice', 'init']);
                    }

                    $query = $query->where('deleted', 0);
                    return $query;
                },
                'zone' => function ($query) use ($zone_id) {
                    $query = $query->select('id', 'zone_name');
                    if ($zone_id != 'all') {
                        $query = $query->where('id', $zone_id);
                    }
                    return $query;
                },
                'subzone' => function ($query) use ($subzone_id) {
                    $query = $query->select('id', 'subzone_name');
                    if ($subzone_id != 'all') {
                        $query = $query->where('id', $subzone_id);
                    }
                    return $query;
                },
            ])
            ->get([
                'user_id', 'undertake_subzone_id', 'undertake_zone_id', 'owe_count', 'meternumber', 'comment',
            ]);
        $filterOweAndInvoiceStatusInInvioceNotEmpty = collect($findOweAndInvoiceStatusInInvioceTable)->filter(function ($val) {
            return collect($val->invoice)->isNotEmpty() && collect($val->subzone)->isNotEmpty() && collect($val->user_profile)->isNotEmpty();
        });

        return collect($filterOweAndInvoiceStatusInInvioceNotEmpty)->values();

    }
    public function testIndexFilterOweAndInvoice(REQUEST $request)
    {
        $zone_id = $request->get('zone_id');
        $subzone_id = $request->get('subzone_id');
        $fnCtrl = new FunctionsController();
        $apiReportCtrl = new apiReportCtrl();
        if (collect($request)->isEmpty()) {
            $a = [
                'invperiodstart' => 'all',
                'invperiodend' => 'all',
                'zone_id' => 'all',
                'subzone_id' => 'all',
                'type' => 'payment',
            ];
            $request->merge($a);
        }
        $type = $request->get('type');

        $findOweAndInvoiceStatusInInvioceTable = UserMerterInfo::whereIn('status', ['active', 'cutmeter','deleted', 'inactive']);
        // $findOweAndInvoiceStatusInInvioceTable = UserMerterInfo::whereIn('status', ['active', 'cutmeter'])->where('deleted', 0);
        if ($request->get('subzone_id') != "all") {
            $findOweAndInvoiceStatusInInvioceTable = $findOweAndInvoiceStatusInInvioceTable->where('undertake_subzone_id', $request->get('subzone_id'));
        }
        $findOweAndInvoiceStatusInInvioceTable = $findOweAndInvoiceStatusInInvioceTable->with([
            'user_profile' => function ($query) {
                return $query->select('name', 'user_id', 'address');
            },
            'invoice' => function ($query) use ($type) {
                $query = $query->select('status', 'user_id');
                if ($type == 'payment-search') {
                    $query = $query->where('receipt_id', '>', 0);
                } else {
                    $query = $query->whereIn('status', ['owe', 'invoice']);
                }

                // $query = $query->where('deleted', 0);
                return $query;
            },
            'zone' => function ($query) use ($zone_id) {
                $query = $query->select('id', 'zone_name');
                if ($zone_id != 'all') {
                    $query = $query->where('id', $zone_id);
                }
                return $query;
            },
            'subzone' => function ($query) use ($subzone_id) {
                $query = $query->select('id', 'subzone_name');
                if ($subzone_id != 'all') {
                    $query = $query->where('id', $subzone_id);
                }
                return $query;
            },
        ]);

        $findOweAndInvoiceStatusInInvioceTable = $findOweAndInvoiceStatusInInvioceTable->get([
            'user_id', 'undertake_subzone_id', 'undertake_zone_id', 'owe_count', 'meternumber', 'comment',
        ]);

        $filterOweAndInvoiceStatusInInvioceNotEmpty = collect($findOweAndInvoiceStatusInInvioceTable)->filter(function ($val) {
            return collect($val->user_profile)->isNotEmpty() && collect($val->invoice)->isNotEmpty() && collect($val->subzone)->isNotEmpty();
        });

        return collect($filterOweAndInvoiceStatusInInvioceNotEmpty)->values();

    }



    public function indexFilterOweAndInvoiceCount(REQUEST $request)
    {
        if (collect($request)->isEmpty()) {
            $a = [
                'invperiodstart' => 'all',
                'invperiodend' => 'all',
                'zone_id' => 'all',
                'subzone_id' => 'all',
                'type' => 'payment',
            ];
            $request->merge($a);
        }
        $zone_id = $request->get('zone_id');
        $subzone_id = $request->get('subzone_id');
        $findOweAndInvoiceStatusInInvioceTable = UserMerterInfo::with([
           'invoice' => function($query){
              return  $query->select('user_id')
                ->whereIn('status', ['owe', 'invoice'])
                ->whereIn('inv_period_id', [14,15,16,17,18,19,20,21]);
        }
       ])
       ->orderBy('undertake_zone_id', 'ASC')->get(['user_id','undertake_zone_id', 'undertake_subzone_id']);

       $filterOweAndInvoiceStatusInInvioceNotEmpty = collect($findOweAndInvoiceStatusInInvioceTable)->filter(function ($val) {
        return collect($val->invoice)->isNotEmpty();
    });
    $a11 = collect($filterOweAndInvoiceStatusInInvioceNotEmpty)->groupBy('undertake_subzone_id');
    $oweAndInvoiceDivideBySubzoneArray = collect([]);

    $oweAndInvoiceDivideBySubzoneArray->push(
        [
            'zone_id' => 'all',
            'subzone_id' => 'all',
            'zoneName' => 'ทั้งหมด',
            'subzoneName' => 'ทั้งหมด',
            'oweCount' => collect($filterOweAndInvoiceStatusInInvioceNotEmpty)->count(),
        ]
    );
    foreach ($a11 as $a1) {
        if(collect($a1[0]['undertake_zone_id'])->isNotEmpty()){
            $oweAndInvoiceDivideBySubzoneArray->push(
                [
                    'zone_id' => $a1[0]['undertake_zone_id'],
                    'subzone_id' => $a1[0]['undertake_subzone_id'],
                    'zoneName' => Zone::where('id', $a1[0]['undertake_zone_id'])->get('zone_name')[0]->zone_name,
                    'subzoneName' => Subzone::where('id', $a1[0]['undertake_subzone_id'])->get('subzone_name')[0]->subzone_name,
                    'oweCount' => collect($a1)->count(),
                ]
            );
        }
    }
    return collect($oweAndInvoiceDivideBySubzoneArray)->values();

        if (collect($request)->isEmpty()) {
            $a = [
                'invperiodstart' => 'all',
                'invperiodend' => 'all',
                'zone_id' => 'all',
                'subzone_id' => 'all',
                'type' => 'payment',
            ];
            $request->merge($a);
        }
        $zone_id = $request->get('zone_id');
        $subzone_id = $request->get('subzone_id');
        $type = $request->get('type');
        $oweAndInvoiceDivideBySubzoneArray = collect([]);
        $findOweAndInvoiceStatusInInvioceTable = UserMerterInfo::whereIn('status', ['active', 'cutmeter']);
        if ($request->get('subzone_id') != "all") {
            $findOweAndInvoiceStatusInInvioceTable = $findOweAndInvoiceStatusInInvioceTable->where('undertake_subzone_id', $request->get('subzone_id'));
        }
        $findOweAndInvoiceStatusInInvioceTable = $findOweAndInvoiceStatusInInvioceTable->with([
            'invoice' => function ($query) use ($type) {
                $query = $query->select('status', 'user_id');
                if ($type == 'payment-search') {
                    $query = $query->where('receipt_id', '>', 0);
                } else {
                    $query = $query->whereIn('status', ['owe', 'invoice']);
                }

                $query = $query->where('deleted', 0);
                return $query;
            },

        ]);

        $findOweAndInvoiceStatusInInvioceTable = $findOweAndInvoiceStatusInInvioceTable->get([
            'user_id', 'undertake_subzone_id', 'undertake_zone_id',
        ]);

        $filterOweAndInvoiceStatusInInvioceNotEmpty = collect($findOweAndInvoiceStatusInInvioceTable)->filter(function ($val) {
            return collect($val->invoice)->isNotEmpty();
        });

        $a11 = collect($filterOweAndInvoiceStatusInInvioceNotEmpty)->groupBy('undertake_subzone_id');

        $oweAndInvoiceDivideBySubzoneArray->push(
            [
                'zone_id' => 'all',
                'subzone_id' => 'all',
                'zoneName' => 'ทั้งหมด',
                'subzoneName' => 'ทั้งหมด',
                'oweCount' => collect($filterOweAndInvoiceStatusInInvioceNotEmpty)->count(),
            ]
        );
        foreach ($a11 as $a1) {

            $oweAndInvoiceDivideBySubzoneArray->push(
                [
                    'zone_id' => $a1[0]['undertake_zone_id'],
                    'subzone_id' => $a1[0]['undertake_subzone_id']->id,
                    'zoneName' => Zone::where('id', $a1[0]['zone']->id)->get('zone_name')[0]['zone_name'],
                    'subzoneName' => Subzone::where('id', $a1[0]['subzone']->id)->get('subzone_name')[0]['subzone_name'],
                    'oweCount' => collect($a1)->count(),
                ]
            );
        }
        // $filterOweAndInvoiceStatusInInvioceNotEmpty = collect($findOweAndInvoiceStatusInInvioceTable)->filter(function ($val) {
        //     return collect($val->user_profile)->isNotEmpty() && collect($val->invoice)->isNotEmpty() && collect($val->subzone)->isNotEmpty();
        // });

        return collect($oweAndInvoiceDivideBySubzoneArray)->values();

    }

    public function testOweAndInvoioceDivideBySubzone(REQUEST $request)
    {
        $subzones = Subzone::where('deleted', 0)->get();
        $oweAndInvoiceDivideBySubzoneArray = collect([]);
        $a = $this->testIndexFilterOweAndInvoice($request->merge(['zone_id' => 'all', 'subzone_id' => 'all']));
        $a11 = collect($a)->groupBy('undertake_subzone_id');
        $oweAndInvoiceDivideBySubzoneArray->push(
            [
                'zone_id' => 'all',
                'subzone_id' => 'all',
                'zoneName' => 'ทั้งหมด',
                'subzoneName' => 'ทั้งหมด',
                'oweCount' => collect($a)->count(),
            ]
        );
        foreach ($a11 as $a1) {
            // $this->testIndex()
            // return $a1[0]['subzone'];
            $oweAndInvoiceDivideBySubzoneArray->push(
                [
                    'zone_id' => $a1[0]['zone']->id,
                    'subzone_id' => $a1[0]['subzone']->id,
                    'zoneName' => $a1[0]['zone']->zone_name,
                    'subzoneName' => $a1[0]['subzone']->subzone_name,
                    'oweCount' => collect($a1)->count(),
                ]
            );
        }
        return collect($oweAndInvoiceDivideBySubzoneArray)->values();
    }

}
