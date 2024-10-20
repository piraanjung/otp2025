<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\FunctionsController;
use App\Http\Controllers\Api\ReportsController as apiReportCtrl;
use App\Http\Controllers\Controller;
use App\Models\UserMerterInfo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CutmeterController extends Controller
{
    public function index(Request $request, $zone_id = "all", $subzone="all")
    {
        // date_default_timezone_set('Asia/Bangkok');
        // $fnCtrl = new FunctionsController();
        // $apiReportCtrl = new apiReportCtrl();
        // if (collect($request)->isEmpty()) {
        //     $a = [
        //         'invperiodstart' => 'all',
        //         'invperiodend' => 'all',
        //         'zone_id' => 'all',
        //         'subzone_id' => 'all',
        //         'type' => 'payment',
        //     ];
        //     $request->merge($a);
        // }
        // //หา user  ที่ status  เป็น cutmeter
        // $oweInfosArr = DB::table('usermeterinfos as umf')
        //     ->join('invoice as iv', 'iv.user_id', '=', 'umf.user_id')
        //     ->join('user as u', 'u.id', '=', 'umf.user_id')
        //     ->join('zone as z', 'z.id', '=', 'u.zone_id')
        //     ->join('subzone as udt_sz', 'udt_sz.id', '=', 'umf.undertake_subzone_id')
        //     ->join('zone as udt_z', 'udt_z.id', '=', 'umf.undertake_zone_id')
        //     ->whereIn('umf.cutmeter', [1, 2, 3])
        //     ->WhereIn('umf.status', ['active', 'cutmeter'])
        //     ->where('iv.deleted', '=', 0);

        // if ($request->get('zone_id') != 'all') {
        //     if ($request->get('subzone_id') != 'all') {
        //         $oweInfosArr = $oweInfosArr->where('umf.undertake_subzone_id', '=', $request->get('subzone_id'));
        //     } else if ($request->get('subzone_id') == 'all') {
        //         $oweInfosArr = $oweInfosArr->where('umf.undertake_zone_id', '=', $request->get('zone_id'));
        //     }
        // }

        // $oweInfosArr = $oweInfosArr->select(
        //     'u.name', 'upf.address',
        //     'z.zone_name',
        //     'udt_sz.subzone_name',
        //     'umf.meternumber', 'umf.user_id', 'umf.status as umf_status', 'umf.owe_count',
        //     'iv.status',
        // )
        //     ->groupBy('umf.user_id')
        //     ->get();

        // foreach ($oweInfosArr as $arr) {
        //     $cth = CutmeterHistory::where('user_id', $arr->user_id)->get('status');
        //     $arr->cutmeter_status = isset($cth[0]->status) ? $this->cutmeter_status_Th($cth[0]->status, $arr->user_id) : '<button class="btn btn-block btn-sm btn-outline-warning disabled">รอดำเนินการถอดมิเตอร์</button><input type="hidden" id="cutmeter_id'.$arr->user_id.'" value="0">';
        // }

        // return $oweInfosArr;

    }

    public function index2(REQUEST $request)
    {
        // date_default_timezone_set('Asia/Bangkok');
        // $fnCtrl = new FunctionsController();
        // $apiReportCtrl = new apiReportCtrl();
        // if (collect($request)->isEmpty()) {
        //     $a = [
        //         'invperiodstart' => 'all',
        //         'invperiodend' => 'all',
        //         'zone_id' => 'all',
        //         'subzone_id' => 'all',
        //         'type' => 'payment',
        //     ];
        //     $request->merge($a);
        // }
        // //หา user  ที่ status  เป็น cutmeter
        // $oweInfosArr = DB::table('user_meter_infos as umf')
        //     ->join('user_profile as upf', 'upf.user_id', '=', 'umf.user_id')
        //     ->whereIn('umf.cutmeter', [1, 2, 3])
        // ->get(['upf.user_id']);
        // if ($request->get('zone_id') != 'all') {
        //     if ($request->get('subzone_id') != 'all') {
        //         $oweInfosArr = $oweInfosArr->where('umf.undertake_subzone_id', '=', $request->get('subzone_id'));
        //     } else if ($request->get('subzone_id') == 'all') {
        //         $oweInfosArr = $oweInfosArr->where('umf.undertake_zone_id', '=', $request->get('zone_id'));
        //     }
        // }

        // $oweInfosArr = $oweInfosArr->select(
        //     'upf.name', 'upf.address',
        //     'z.zone_name',
        //     'udt_sz.subzone_name',
        //     'umf.meternumber', 'umf.user_id', 'umf.status as umf_status', 'umf.owe_count',
        //     'iv.status',
        // )
        //     ->groupBy('umf.user_id')
        //     ->get();

        // foreach ($oweInfosArr as $arr) {
        //     $cth = CutmeterHistory::where('user_id', $arr->user_id)->get('status');
        //     $arr->cutmeter_status = isset($cth[0]->status) ? $this->cutmeter_status_Th($cth[0]->status,$arr->user_id) : '<button class="btn btn-block btn-sm btn-outline-warning disabled">รอดำเนินการถอดมิเตอร์</button><input type="hidden" id="cutmeter_id'.$arr->user_id.'" value="0">';
        // }

        // return $oweInfosArr;

    }

    public function get_cutmeter_history($user_id)
    {
        // $historys = CutmeterHistory::where('user_id', $user_id)
        //     ->whereIn('status',[1,2] )
        //     ->where('deleted', 0)
        //     ->get();
        // if(collect($historys)->count() == 0){
        //     return [
        //         'status'
        //     ];
        // }
        // $res = collect([]);
        // foreach($historys as $history){
        //     $operate_infos = json_decode($history->operate_infos, true);
        //     $twmans = collect([]);

        //     foreach ($operate_infos['twman'] as $twman) {
        //             if ($twman["user_id"] != null) {
        //                 $rers = User::where('id', $twman['user_id'])
        //                     ->get(['prefix','firstname', 'lastname'])->first();
        //                 $twmans->push($rers);
        //             }


        //     }
        //     $res->push([
        //         'status_id' => $history->status,
        //         'twmans' => $twmans,
        //         'operate_date' => $operate_infos['operate_date'],
        //         'operate_time' => $operate_infos['operate_time'],
        //         'status' => $this->cutmeter_status_Th($history->status, 0),
        //     ]);
        // }

        // return $res;

    }

    public function get_process_history($user_id, $inv_period_id)
    {
    //     $historys = CutmeterHistory::where('user_id', $user_id)
    //         ->where('inv_period_id', $inv_period_id)
    //         ->where('deleted', 0)
    //         ->get();

    //     $processHistory = json_decode($historys[0]->process_history, true);
    //     $processHistory['status'] = $this->cutmeter_status_Th($processHistory['cutmeter_status'], 0);
    //     foreach($processHistory['twman'] as $key => $twman){
    //         if ($twman["user_id"] != null) {
    //             $rers = UserProfile::where('user_id', $twman['user_id'])
    //                 ->get(['name'])->first();
    //             $processHistory['twman'][$key]['name'] = $rers->name;
    //         }
    //     }
    //     return $processHistory;


    }

    private function cutmeter_status_Th($status, $user_id)
    {
        $str = '';
        if ($status == '1' || $status =='cutmeter') {
            $str = '<button class="btn btn-block btn-outline-danger btn-sm disabled">ถอดมิเตอร์แล้ว</button>';
        } else if ($status == '2') {
            $str = '<button class="btn btn-block btn-outline-info btn-sm disabled">ชำระเงินแล้ว รอติดตั้งมิเตอร์</button>';
        } else if ($status == '3') {
            $str = '<button class="btn btn-block btn-outline-success btn-sm disabled">ติดตั้งมิเตอร์สำเร็จ</button>';
        } else if ($status == '0') {
            $str = '<button class="btn btn-block btn-outline-secondary btn-sm disabled">ยกเลิก</button>';
        } else if ($status == 'hibernate') {
            $str = '<button class="btn btn-block btn-outline-warning disabled">รอดำเนินการถอดมิเตอร์</button>';
        }

        return $str.'<input type="hidden" id="cutmeter_id'.$user_id.'" value="'.$status.'">';
    }
    public function count(REQUEST $request)
    {
        return $count = UserMerterInfo::WhereIn('cutmeter', [1, 2, 3])
            ->where('deleted', 0)
            ->count();
    }

    public function test(REQUEST $request)
    {
        return $this->getOweOver3CountDivideBySubzone($request);
    }

    public function getOweOver3CountDivideBySubzone()
    {

        $findUserMeterInfosTableWhereOweCountOver3 = UserMerterInfo::whereIn('cutmeter', [1, 2, 3])
            ->where('deleted', 0)->whereIn('status', ['active', 'cutmeter'])
            ->with([
                'zone' => function ($query) {
                    $query = $query->select('id', 'zone_name');
                    return $query;
                },
                'subzone' => function ($query) {
                    $query = $query->select('id', 'subzone_name');
                    return $query;
                },
            ])
            ->get([
                'user_id', 'undertake_subzone_id', 'undertake_zone_id', 'owe_count',
            ]);

        $oweOver3BySubzone = [];
        array_push($oweOver3BySubzone, [
            'zone_id' => 'all',
            'subzone_id' => 'all',
            'zone_name' => 'ทั้งหมด',
            'subzone_name' => 'ทั้งหมด',
            'zone_index' => '0',
            'oweOver3Count' => collect($findUserMeterInfosTableWhereOweCountOver3)->count(),
        ]);
        $vals = collect($findUserMeterInfosTableWhereOweCountOver3)->groupBy('undertake_subzone_id');
        foreach ($vals as $val) {
            array_push($oweOver3BySubzone, [
                'zone_id' => $val[0]->zone->id,
                'subzone_id' => $val[0]->subzone->id,
                'zone_name' => $val[0]->zone->zone_name,
                'subzone_name' => $val[0]->subzone->subzone_name,
                'zone_index' => $val[0]->zone->id,
                'oweOver3Count' => collect($val)->count(),
            ]);
        }
        return collect($oweOver3BySubzone)->sortBy('zone_index')->values();
    }
}
