<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function history($id, $from = '')
    {
        // return $this->testHistory($id, $from = 'receipt');
        $id = intval($id);
        $user_id = '';
        //หา receipt_id จาก invoice table
        $recieptQuery = DB::table('invoice as iv');

        if ($from == 'receipt') {
            //id == เลขใบเสร็จที่บันทึก
            $recieptQuery = $recieptQuery->where('iv.receipt_id', '=', $id);
        } else if ($from == "receipt_history") {
            //ค้นหาจาก  เลขมิเตอร์ แปลงไปเป็น user_id
            $recieptQuery = $recieptQuery->where('iv.user_id', '=', $id);
        }
        //หา user_id จาก user_meter_infos table
        $recieptQuery = $recieptQuery->join('user_meter_infos as umf', 'umf.user_id', '=', 'iv.user_id')
            ->join('zone as z', 'z.id', '=', 'umf.undertake_zone_id')
            ->join('subzone as sz', 'sz.id', '=', 'umf.undertake_subzone_id')
            ->join('user_profile as upf', 'upf.user_id', '=', 'umf.user_id')
            ->join('invoice_period as ivp', 'ivp.id', '=', 'iv.inv_period_id');

        $user = $recieptQuery
            ->join('province as p', 'p.province_code', '=', 'upf.province_code')
            ->join('district as dt', 'dt.district_code', '=', 'upf.district_code')
            ->join('tambon as tb', 'tb.tambon_code', '=', 'upf.tambon_code')
            ->select(
                'umf.meternumber',
                'dt.district_name', 'umf.user_id',
                'z.zone_name', 'sz.subzone_name',
                'p.province_name',
                'upf.name', 'upf.address',
                'upf.zone_id', 'upf.subzone_id',
                'tb.tambon_name'
            )
            // ->whereIn('umf.status', ['active', 'cutmeter'])
            // ->where('umf.deleted', 0)
            ->get();
        $recieptsTemp = $recieptQuery->join('accounting as acc', 'acc.id', '=', 'iv.receipt_id')
            ->select('iv.id as iv_id', 'iv.receipt_id',
                'iv.currentmeter', 'iv.lastmeter',
                DB::raw('(iv.currentmeter - iv.lastmeter) as water_used'), 'iv.status as iv_status',
                DB::raw('(iv.currentmeter - iv.lastmeter)*8 as mustpaid'),
                'acc.total', 'acc.updated_at', 'acc.cashier',
                'iv.created_at as record_meternumber_date',
                'umf.meternumber',
                'z.zone_name', 'sz.subzone_name',
                'upf.name', 'upf.address',
                'ivp.inv_period_name',
            )
            ->where('iv.receipt_id', '<>', 0)
            ->get();
        foreach ($recieptsTemp as $item) {
            $cashiername = User::where('id', $item->cashier)->get('name');
            $item->cashiername = $cashiername[0]->name;
        }
        $reciepts = collect($recieptsTemp)->groupBy('receipt_id')->values();

        $fncController = new FunctionsController();
        foreach ($reciepts as $reciept) {
            foreach ($reciept as $rec) {
                $exp = explode(' ', $rec->updated_at);
                $rec->receipt_th_date = $fncController->engDateToThaiDateFormat($exp[0]);
            }
        }

        return ['user' => collect($user)->first(), 'reciepts' => $reciepts];
    }

    public function history2($id, $from = '')
    {
        // return $this->testHistory($id, $from = 'receipt');
        $id = intval($id);
        $user_id = '';
        //หา receipt_id จาก invoice table
        $recieptQuery = DB::table('invoice as iv');

        if ($from == 'receipt') {
            //id == เลขใบเสร็จที่บันทึก
            $recieptQuery = $recieptQuery->where('iv.receipt_id', '=', $id);
        } else if ($from == "receipt_history") {
            //ค้นหาจาก  เลขมิเตอร์ แปลงไปเป็น user_id
            $recieptQuery = $recieptQuery->where('iv.user_id', '=', $id);
        }
        //หา user_id จาก user_meter_infos table
        $recieptQuery = $recieptQuery->join('user_meter_infos as umf', 'umf.user_id', '=', 'iv.user_id')
            ->join('zone as z', 'z.id', '=', 'umf.undertake_zone_id')
            ->join('subzone as sz', 'sz.id', '=', 'umf.undertake_subzone_id')
            ->join('user_profile as upf', 'upf.user_id', '=', 'umf.user_id')
            ->join('invoice_period as ivp', 'ivp.id', '=', 'iv.inv_period_id');

        $user = $recieptQuery
            ->join('province as p', 'p.province_code', '=', 'upf.province_code')
            ->join('district as dt', 'dt.district_code', '=', 'upf.district_code')
            ->join('tambon as tb', 'tb.tambon_code', '=', 'upf.tambon_code')
            ->select(
                'umf.meternumber',
                'dt.district_name', 'umf.user_id',
                'z.zone_name', 'sz.subzone_name',
                'p.province_name',
                'upf.name', 'upf.address',
                'upf.zone_id', 'upf.subzone_id',
                'tb.tambon_name'
            )
            // ->whereIn('umf.status', ['active', 'cutmeter'])
            // ->where('umf.deleted', 0)
            ->get();
        $recieptsTemp = $recieptQuery->join('accounting as acc', 'acc.id', '=', 'iv.receipt_id')
            ->select('iv.id as iv_id', 'iv.receipt_id',
                'iv.currentmeter', 'iv.lastmeter',
                DB::raw('(iv.currentmeter - iv.lastmeter) as water_used'), 'iv.status as iv_status',
                DB::raw('(iv.currentmeter - iv.lastmeter)*8 as mustpaid'),
                'acc.total', 'acc.updated_at', 'acc.cashier',
                'iv.created_at as record_meternumber_date',
                'umf.meternumber',
                'z.zone_name', 'sz.subzone_name',
                'upf.name', 'upf.address',
                'ivp.inv_period_name',
            )
            ->where('iv.receipt_id', '<>', 0)
            ->get();
        foreach ($recieptsTemp as $item) {
            $cashiername = User::where('id', $item->cashier)->get('name');
            $item->cashiername = $cashiername[0]->name;
        }
        $reciepts = collect($recieptsTemp)->groupBy('receipt_id')->values();

        $fncController = new FunctionsController();
        foreach ($reciepts as $reciept) {
            foreach ($reciept as $rec) {
                $exp = explode(' ', $rec->updated_at);
                $rec->receipt_th_date = $fncController->engDateToThaiDateFormat($exp[0]);
            }
        }

        return ['user' => collect($user)->first(), 'reciepts' => $reciepts];
    }

    public function users(REQUEST $request)
    {
        date_default_timezone_set('Asia/Bangkok');
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
        //หา user  ที่ invoice.status  เป็นowe หรือ invoice
        $oweInfosArr = DB::table('user_meter_infos as umf')
        // ->join('invoice as iv', 'iv.user_id', '=', 'umf.user_id')
            ->join('user_profile as upf', 'upf.user_id', '=', 'umf.user_id')
            ->join('zone as z', 'z.id', '=', 'upf.zone_id')
            ->join('subzone as udt_sz', 'udt_sz.id', '=', 'umf.undertake_subzone_id')
            ->join('zone as udt_z', 'udt_z.id', '=', 'umf.undertake_zone_id')
            ->whereIn('umf.status', ['active', 'cutmeter']);
        //  ->where('iv.deleted', '=', 0);

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
            'z.zone_name',
            'udt_sz.subzone_name',
            'umf.meternumber', 'umf.user_id',
            // 'iv.status', 'iv.inv_period_id'
        )
            ->groupBy('umf.user_id')
            ->get();
        return $oweInfosArr;

    }

    public function testHistory($id, $from = 'receipt')
    {

        // return $a = $from == 'receipt' ? 'receit_id' : 'user_id';
        $invoice = Invoice::
            with([
            'usermeterinfos' => function ($query) {
                $query->select('user_id', 'meternumber')
                    ->where('status', 'active')
                    ->where('deleted', 0);
            },
            'accounting' => function ($query) {
                $query->select('id', 'total', 'cashier', 'updated_at');
            },
            'accounting.user_profile' => function ($query) {
                $query->select('name as cashier_name', 'user_id');
            },
            'user_profile' => function ($query) {
                $query->select('user_id', 'name', 'address', 'zone_id', 'subzone_id',
                    'tambon_code', 'district_code', 'province_code');
            },
            'user_profile.zone' => function ($query) {
                $query->select('id', 'zone_name');
            },
            'user_profile.subzone' => function ($query) {
                $query->select('id', 'subzone_name');
            },
            'user_profile.tambon' => function ($query) {
                $query->select('tambon_code', 'tambon_name');
            },
            'user_profile.province' => function ($query) {
                $query->select('province_code', 'province_name');
            },
            'user_profile.district' => function ($query) {
                $query->select('district_code', 'district_name');
            },
        ])
            ->where(
                $from == 'receipt' ? 'receipt_id' : 'user_id', $id
            )
            ->get(['inv_period_id', 'lastmeter', 'currentmeter', 'user_id', 'recorder_id', 'receipt_id']);

        return $invoice;
    }

}
