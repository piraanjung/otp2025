<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\CutmeterController as ApiCutmeterCtrl;
use App\Http\Controllers\Api\FunctionsController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\UsersController as ApiUsersCtrl;
use App\Models\Cutmeter;
use App\Models\Invoice;
use App\Models\InvoicePeriod;
use App\Models\Setting;
use App\Models\User;
use App\Models\UserMerterInfo;
use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\Support\facades\DB;

class CutmeterController extends Controller
{
    public function index($subzone_id = "")
    {
        $zones = Zone::all();
        $zone_id_selected = $subzone_id == "" ? 'all' : $subzone_id;
        $subzone_id_selected = 'all';

        $cutmeters = UserMerterInfo::where('owe_count', '>=',2)
                ->orWhere('cutmeter',1)
                ->with(['cutmeter' => function($q){
                    return $q->select('id','meter_id_fk', 'owe_count', 'progress', 'warning_print', 'status')->whereIn('status', ['init', 'cutmeter', 'install']);
                }])
                ->get(['meter_id', 'user_id', 'owe_count', 'status']);

        return view('cutmeter.index', compact('cutmeters'));
    }

    public function cutmeterProgress($id)
    {
        $cutmeter  = Cutmeter::where('id', $id)->get()->first();
        $lastmeter = Invoice::where('meter_id_fk', $cutmeter->meter_id_fk)->whereIn('status', ['owe', 'invoice','paid'])->get(['inv_id','lastmeter'])->last();
        $twmans    = User::where('role_id', 5)->get(['prefix', 'firstname', 'lastname', 'name','id']);
        return view('cutmeter.progress', compact('cutmeter', 'lastmeter','twmans'));
    }

    public function installMeterProgress($id){
        $cutmeter                = Cutmeter::where('id', $id)->get()->first();
        $cutmeter->twmanArray    = json_decode($cutmeter->progress, true)[1]['undertaker'];
        $lastmeter               = Invoice::where('meter_id_fk', $cutmeter->meter_id_fk)->whereIn('status', ['owe', 'invoice','paid'])->get(['inv_id','lastmeter'])->last();
        $twmans                  = User::where('role_id', 5)->get(['prefix', 'firstname', 'lastname', 'name','id']);
        return view('cutmeter.install_meter', compact('cutmeter','twmans', 'lastmeter'));
    }

    public function create($user_id)
    {
        $tambon_infos_db = DB::table('settings')
            ->where('name', 'organization')
            ->select('values')
            ->get();
        $tambon_infos = collect(json_decode($tambon_infos_db[0]->values, true))->toArray();
        $user = UserMerterInfo::where('user_id', $user_id)
            ->where('deleted', 0)
            ->with([
                'user_profile' => function ($query) {
                    $query->select('name', 'address', 'phone', 'user_id', 'zone_id');
                },
                'zone:zone_name,id',
                'subzone:subzone_name,id',
            ])
            ->get(['user_id', 'meternumber', 'undertake_zone_id', 'undertake_subzone_id']);

        $cutmeter_status = [['id' => '1', 'value' => 'ล็อคมิเตอร์']];

        $tabwatermans = User::where('user_cat_id', 4)
            ->with(['user_profile:name,user_id'])
            ->where('status', 'active')
            ->get(['id']);

        return view('cutmeter.create', compact('user', 'cutmeter_status', 'tabwatermans', 'tambon_infos',));
    }

    public function store(REQUEST $request)
    {

    }

    function print(REQUEST $request)
    {
        date_default_timezone_set('Asia/Bangkok');
        $from_view = $request->get('from_view');
        $funcCtrl = new FunctionsController();
        $oweArray = [];
        $apiInvoiceCtrl = new InvoiceController ();
        foreach ($request->get('user_id') as $key => $on) {
            if ($on == 'on') {
                //หาการใช้น้ำ 5 เดือนล่าสุด
                $last5InvoiceByInvoicePeriod = Invoice::where('user_id', $key)
                    ->whereIn('status', ['owe', 'invoice'])
                    ->with(
                        'invoice_period',
                        'usermeterinfos.user',
                        'usermeterinfos'
                    )
                    ->orderBy('inv_period_id', 'desc')
                    ->get();
                if (collect($last5InvoiceByInvoicePeriod)->count() > 0) {
                    // หาผลรวมการให้น้ำของ status ที่เป็น owe และ invoice ปัจจุบัน
                    $sumUsedWaterByOweAndInvoiceStatus = collect($last5InvoiceByInvoicePeriod)->sum(function ($inv) use ($funcCtrl) {
                        $inv['invoice_period']['startdate'] = $funcCtrl->engDateToThaiDateFormat($inv['invoice_period']['startdate']);
                        $inv['invoice_period']['enddate'] = $funcCtrl->engDateToThaiDateFormat($inv['invoice_period']['enddate']);

                        if ($inv['status'] == 'invoice' || $inv['status'] == 'owe') {
                            $a = $inv['currentmeter'] - $inv['lastmeter'];
                            return $a;
                        }
                    });
                    $inv['water_used'] = $sumUsedWaterByOweAndInvoiceStatus;
                    $inv['paid'] = $sumUsedWaterByOweAndInvoiceStatus * 8;

                    array_push($oweArray, [
                        'res' => collect($last5InvoiceByInvoicePeriod)->reverse()->flatten(),
                    ]);
                }
            }
        }

        return view('owepaper.print', compact('oweArray', 'from_view'));
    }


    public function print_install_meter($cutmeter_id){
        $cutmeter        = Cutmeter::where('id', $cutmeter_id)->first();
        $undertaker_array = json_decode($cutmeter->progress, true)[1]['undertaker'];
        $twman = [];
        foreach($undertaker_array as $undertaker_id){
            $twman_info = User::where('id', $undertaker_id)->get(['prefix', 'firstname', 'lastname', 'name'])->first();
            array_push($twman, $twman_info);
        }
        $cutmeterArr[] = [
                "twman"             => $twman,
                "head_twman"        => User::where('id', 88)->get(['prefix', 'id', 'firstname', 'lastname', 'name'])->first(),
                "usermeterinfos"    => UserMerterInfo::where('meter_id', $cutmeter->meter_id_fk)->with([
                                'user' => function ($query) {
                                    return $query->select('id','prefix', 'firstname', 'lastname', 'name', 'address', 'zone_id');
                                }
                            ])->get(['meter_id', 'user_id', 'meternumber']),
                ];

        return view('cutmeter.print_install_meter', compact('cutmeterArr'));

    }

    public function edit($user_id)
    {
        $tambon_infos_db = DB::table('settings')
            ->where('name', 'organization')
            ->select('values')
            ->get();
        $tambon_infos = collect(json_decode($tambon_infos_db[0]->values, true))->toArray();
        $user = UserMerterInfo::where('user_id', $user_id)
            ->where('deleted', 0)
            ->with([
                'user_profile' => function ($query) {
                    $query->select('name', 'address', 'phone', 'user_id', 'zone_id');
                },
                'zone:zone_name,id',
                'subzone:subzone_name,id',
            ])
            ->get(['user_id', 'meternumber', 'undertake_zone_id', 'undertake_subzone_id']);

        $cutmeter_user_current_state = Cutmeter::where('user_id', $user_id)->where('pending', 1)->get(['status', 'twman_id']);
        $cutmeter_user_status = collect($cutmeter_user_current_state)->count() == 0 ? '' : $cutmeter_user_current_state[0]->status;
        $twman_appoint_json = collect($cutmeter_user_current_state)->count() == 0 ? 0 : \json_decode($cutmeter_user_current_state[0]->twman_id);
        $twman_appoint = $twman_appoint_json == 0 ? [(object) ['user_id' => ''], (object) ['user_id' => ''], (object) ['user_id' => '']] : $twman_appoint_json;

        $cutmeter_status = [
            ['id' => '1', 'value' => 'ล็อคมิเตอร์'],
            ['id' => '3', 'value' => 'ติดตั้งมิเตอร์สำเร็จ'],
            ['id' => '4', 'value' => 'ยกเลิก']
        ];

        $tabwatermans = User::where('user_cat_id', 4)
            ->with(['user_profile:name,user_id'])
            ->where('status', 'active')
            ->get(['id']);

        //ถ้า status == disambled สเตป ต่อไป จะ เปลี่ยน status เป็น  complete
        //ให้ check owe_count ใน user_data_infos table ว่า <2 ไหม
        //ถ้าไม่ให้ $show_submit_btn = 'hidden' และ $owe_count_text = 'ยังมีไม่การชำระเงินค่าน้ำประปาที่ค้าง'
        $show_submit_btn = '';
        $owe_count_text = '';

        if ($cutmeter_user_status == '1') {
            $check_owe_count = UserMerterInfo::where('user_id', $user_id)
                ->where('status', 'active')
                ->where('deleted', 0)
                ->where('cutmeter',)
                ->get(['owe_count']);
            if ($check_owe_count[0]->owe_count >= 3) {
                $show_submit_btn = 'hidden';
                $owe_count_text = 'ยังมีไม่การชำระเงินค่าน้ำประปาที่ค้าง';
            }
        }

        return view('cutmeter.edit', compact('user', 'twman_appoint', 'cutmeter_status', 'cutmeter_user_status', 'tabwatermans', 'tambon_infos', 'cutmeter_user_status', 'show_submit_btn', 'owe_count_text'));
    }

    public function update(REQUEST $request, Cutmeter $cutmeter)
    {
        $twmanArray  = [];
        $progress_array =  json_decode($cutmeter->progress);
        $request->get('twman1') == 0 ? [] : array_push($twmanArray, $request->get('twman1'));
        $request->get('twman2') == 0 ? [] : array_push($twmanArray, $request->get('twman2'));

        $progress_array[] =[
            "topic" =>$request->status,
            "undertaker"  => $twmanArray,
            "created_at"   => strtotime(date('Y-m-d H:i:s')),
            "comment"=>""
        ];
        //update Cutmeter progress , status = cutmeter, updated_at
        if($request->get('status') == 'cutmeter' || $request->get('status') == "install" || $request->get('status') == 'complete' || $request->get('status') == 'cancel') {
            $cutmeter->update([
                "status"=> $request->get('status'),
                'progress'=> $request->get('status') == 'cancel' ?  json_encode([]) : json_encode($progress_array),
                'updated_at'=> date('Y-m-d H:i:s'),
            ]);
        }
        if($request->get('status') == 'cutmeter') {
            //update Usermeterinfo status = inactive
            $vatQuery = Setting::where('name', 'vat')->get('values');
            $vat_rate =  $vatQuery[0]->values/100;
            $metertype = UserMerterInfo::where('meter_id',$cutmeter->meter_id_fk)->get('metertype_id')[0];

            $water_used =  $request->get('currentmeter') - $request->get('lastmeter');
            $paid       = $water_used == 0 ? 10 : $water_used * $metertype->meter_type->price_per_unit;
            $vat        = $water_used == 0 ? $vat_rate*10 : $paid * $vat_rate;
            $inv_type   = $water_used == 0 ? 'r' : 'u';
            Invoice::where('inv_id', $request->get('inv_id'))->update([
                'currentmeter' => $request->get('currentmeter'),
                'water_used'   => $water_used,
                'paid'         => $water_used == 0 ? 10 : $paid,
                'inv_type'     => $inv_type,
                'vat'          => number_format($vat,2),
                'totalpaid'    => $paid + $vat,
                'status'       => 'owe',
                'updated_at'   => date('Y-m-d H:i:s')
            ]);
            UserMerterInfo::where('meter_id', $cutmeter->meter_id_fk)->update(["cutmeter" => 1, "status" => "inactive" ,'updated_at' => date('Y-m-d H:i:s')   ]);
        }else if($request->status == 'complete') {
            //update Usermeterinfo status = active
            $water_used =  $request->get('currentmeter') - $request->get('lastmeter');
            $vat        = $water_used == 0 ? 0.7 : ($water_used * 8) * 0.07;
            Invoice::where('inv_id', $request->get('inv_id'))->update([
                'currentmeter' => $request->get('currentmeter'),
                'water_used'   => $water_used,
                'paid'         => $water_used == 0 ? 10 : $water_used * 8,
                'vat'          => number_format($vat,2),
                'totalpaid'    => ($water_used * 8) + $vat,
                'updated_at'   => date('Y-m-d H:i:s')
            ]);
            UserMerterInfo::where('meter_id', $cutmeter->meter_id_fk)->update([ "status" => "active", 'cutmeter' => '0' ,'updated_at' => date('Y-m-d H:i:s')  ]);
        }
        $this->printDisambledOrCompleteForHead($cutmeter);
        return redirect('cutmeter');

    }


    private function printDisambledOrCompleteForHead($cutmeter)
    {
        $apiCutmeterCtrl = new ApiCutmeterCtrl;
        $apiUsersCtrl = new ApiUsersCtrl;
        $last_inv_period = InvoicePeriod::get()->last();
        $user = \json_decode($apiUsersCtrl->user($cutmeter['meter_id_fk'])->content(), true);
        $headTwman = User::where('id', 2915)->get();
        $recorder = User::where('id', 905)->get(); //วรรณวิภา  ไชยะนนท์

        $invoiceOweAndIvoiceStatus = collect($user[0]['usermeterinfos'][0]['invoice'])->filter(function ($v) {
            return $v['status'] == 'owe' || $v['status'] == 'invoice';
        });
        $status = 1;
        $cutmeteriInfos = Cutmeter::where('id', $cutmeter['id'])->get(); //$apiCutmeterCtrl->get_process_history($meter_id_fk, $last_inv_period->id);

        return view('cutmeter.print', compact('cutmeteriInfos', 'invoiceOweAndIvoiceStatus', 'user', 'status', 'headTwman', 'recorder'));
    }

    public static function cutmeterUserCount(){
        $count = UserMerterInfo::where('status', '<>' ,'deleted')
        ->where('owe_count', '>=',2)->count();
        return $count;
    }

}
