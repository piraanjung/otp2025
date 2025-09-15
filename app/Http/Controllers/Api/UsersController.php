<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\FunctionsController;
use App\Http\Controllers\Api\ZoneController;
use App\Http\Controllers\Controller;
use App\Models\Tabwater\Invoice;
use App\Models\Tabwater\InvoicePeriod;
use App\Models\Tabwater\UndertakerSubzone;
use App\Models\User;
use App\Models\Tabwater\UserMerterInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use PhpParser\Node\Expr\AssignOp\Concat;

class UsersController extends Controller
{
    public function __construct()
    {
    }
    public function index()
    {
      return  $active_users = $this->usersInfos('all');

    }
       public function users()
    {
        $users = DB::table('user')->get(['firstname', 'lastname']);
        $userArray = [];
        foreach ($users as $user) {
            array_push($userArray, $user->firstname . ' ' . $user->lastname);
        }
        return response()->json($userArray);
    }

    public function user($user_id)
    {
        $user = User::where('id', $user_id)
            ->with('usermeterinfos', 'usermeterinfos.invoice', 'usermeterinfos.invoice.invoice_period')
            ->get();
        $session_id = User::where('id', $user_id)->get('remember_token');
        $user['session_id'] = $session_id[0]->remember_token;
        $fn = new FunctionsController;
        foreach ($user[0]->usermeterinfos[0]->invoice as $u) {
            $date = explode(" ", $u->updated_at);
            $u->updated_at_th = date_format(date_create($date[0]),'d-m-Y');//$fn->engDateToThaiDateFormat($date[0]);
        }
        return response()->json($user);
    }

    public function by_zone($subzone_id)
    {
        $users = $this->usersInfos($subzone_id);
        return response()->json($users);
    }
    public function report_by_subzone($subzone_id)
    {
        $users = $this->usersInfos($subzone_id);
        return response()->json($users);
    }
    public function findsearchselected($val)
    {
        // return $val;
        $userSql = DB::table('user_profile as uf')
            ->join('user_meter_infos as umf', 'umf.user_id', '=', 'uf.user_id')
            ->join('zone', 'zone.id', '=', 'uf.zone_id');

        if (preg_match('/(หมู่)/', $val)) {
            // ถ้าค้นหาจากบ้านเลขที่
            $addressArray = \explode('หมู่', $val);
            $userSql = $userSql->where('uf.zone_id', '=', trim($addressArray['1']))
                ->where('uf.address', '=', trim($addressArray['0']));
        } elseif (preg_match('/[HhSs]+[0-9]/', $val)) {
            $userSql = $userSql->where('meternumber', '=', trim($val));
        } else if (preg_match('/^[ก-ฮ]/', $val)) {
            $userSql = $userSql->where('name', '=', $val);
        }
        $user = $userSql->get(['uf.user_id']);

        return response()->json($user);
    }

    public function check_line_id($line_id)
    {
        $user = User::where('line_id', $line_id)
            ->with([
                'userMeterInfos' => function ($query) {
                    return $query->get(['meternumber', 'user_id']);
                },
                'zone',
                'subzone'
            ])
            ->get(['user_id', 'name', 'zone_id', 'subzone_id', 'address']);

        if (collect($user)->isEmpty()) {
            return 0;
        }
        return \response()->json($user);
    }

    public function update_line_id($user_id, $line_id)
    {
        $userSql = User::where('id', $user_id);
        $userSql->update([
            'line_id' => $line_id,
        ]);
        return \response()->json($userSql->get());
    }

    public function users_by_subzone($user_cat_id, $twman_id)
    {
        //หา subzone ที่รับผิดชอบโดย twman_id
        $twman = User::where('id', $twman_id)->with(['undertaker_subzone'])->first();
        $users = UndertakerSubzone::where('subzone_id', $twman->undertaker_subzone[0]->subzone_id)
            ->with([
                'user_meter_infos',
                'user_meter_infos.user.user_profile',
                'user_meter_infos.tabwatermeter',
                'user.user_profile'
            ])->first();

        foreach ($users->user_meter_infos as $invoice) {
            $invoice->invoice = Invoice::where('user_id', $invoice->user_id)
                ->where('status', 'init')
                ->with(['invoice_period'])
                ->first();
        }
        //เพิ่มรายการโซนกับsubzone ไปด้วย
        $zoneApi = new ZoneController();
        $users['zone_and_subzone'] = json_decode($zoneApi->getZoneAndSubzone()->getContent());
        return \response()->json($users);
    }
    public function usersbycategory($userCategory)
    {
        $users = User::where('users.user_cat_id', $userCategory)
            ->leftJoin('user_profile', 'users.id', '=', 'user_profile.user_id')
            ->get();
        $aa = "<option>เลือก...</option>";
        foreach ($users as $user) {
            $aa .= "<option value='" . $user->id . "'>" . $user->name . " " . $user->lastname . "</option>";
        }
        return response()->json($aa);
    }

    public function store(Request $request)
    {

        return response()->json($request);
    }

    public function staff_authen(Request $request)
    {
        return response()->json($request);
    }

    public function authen(Request $request)
    {
        // $jsonString = $request->getContent();
        // $data = json_decode($jsonString, true); // true เพื่อให้ได้เป็น Associative Array

        // เข้าถึงข้อมูลได้เหมือน PHP Array
        // $username = $data['username'];
        // $passwords = $data['password'];
        // $user_cate_id = $data['user_cate_id'];
        $code = 200;
        $username = ($request->has('username') ? $request->username : 0);
        $passwords = ($request->has('passwords') ? $request->passwords : 0);
        $user_cate_id = ($request->has('user_cate_id') ? $request->user_cate_id : 0);

        if ($username == '' || $username == '0' || $passwords == '' || $passwords == '0') {
            $result = ['message' => 'ไม่พบผู้ใช้งาน'];
            $code = 204;
        } else {
            if ($user_cate_id == 5) {
                //เจ้าหน้าที่บันทึกมิเตอร์
                $result = User::where('username', $username)->where('role_id', $user_cate_id)
                    ->with([
                        'undertaker_subzone.subzone' => function ($q) {
                            return $q->select('id', 'subzone_name', 'zone_id');
                        },
                        'undertaker_subzone.subzone.zone' => function ($q) {
                            return $q->select('id', 'zone_name');
                        }
                    ])
                    ->get(['id', 'username', 'prefix', 'firstname', 'lastname', 'subzone_id', 'zone_id', 'password']);

                foreach ($result[0]->undertaker_subzone as $key => $subzone) {
                    $subzone->members                = $this->users_subzone_count($subzone->subzone_id);
                    $subzone->members_status_init    = $this->usermeter_info_get_invoice_status_count($subzone->subzone_id, 'init');
                    $subzone->members_status_invoice = $this->usermeter_info_get_invoice_status_count($subzone->subzone_id, 'invoice');
                    $subzone->members_status_paid    = $this->usermeter_info_get_invoice_status_count($subzone->subzone_id, 'paid');
                }

                $result[0]->inv_period = InvoicePeriod::where('status', 'active')->get(['id', 'inv_p_name']);
            } else {
                $result = User::where('username', $username)
                    ->with(
                        'undertaker_subzone',
                        'undertaker_subzone.subzone',
                    )
                    ->first();
            }

            if (collect($result)->isNotEmpty()) {
                $result = $this->verifyhasPassword($passwords, $result[0]);
            } else {
                $code = 204;
            }
        } //else
        return response()->json(['data' => $result, 'code' => $code]);
    }

    private function verifyhasPassword($plainPassword, $result)
    {
        $hasPassword = (isset($result->password) ? $result->password : 0);
        if (collect($result)->isNotEmpty() && Hash::check($plainPassword, $hasPassword)) {

            $result['logged'] = true;
            $result['rows'] = 1;

            $result['remember_token'] = base64_encode(Str::random(40));
            $this->updateApiToken($result->id, $result->remember_token);
        } else {
            $result = array();
            $result['rows'] = 0;
            $result['logged'] = false;
        }

        return $result;
    }

    public function search($val, $type = '')
    {
        $userArray = [];
        if (intval($val) > 0) {
            // if (preg_match('/^[0-9][\D]/', $val)) {
            //ค้นหาโดยบ้านเลขที่
            $userfilter = $this->searchQueryForAddresAndName($val, 'address', $type, 'aa');
            foreach ($userfilter as $user) {
                array_push($userArray, ' เลขที่ ' . $user->address . ' ' . $user->zone_name . ' - ' . $user->name . ' - ' . $user->meternumber);
            }
        } elseif (preg_match('/[HhSs]+[--0-9]/', $val) || preg_match('/[HhSs]+[0-9]/', $val)) {
            //หาว่ามี "-" หรือไม่ถ้ามีให้ replace ด้วย ""
            if (strpos($val, "-") >= 0) {
                $val = str_replace("-", "", $val);
            }

            $userfilter = DB::table('user_meter_infos as umf')
                ->join('invoice as iv', 'umf.user_id', '=', 'iv.user_id')
                ->join('zone as z', 'umf.undertake_zone_id', '=', 'z.id')
                ->join('user_profile as uf', 'uf.user_id', '=', 'umf.user_id')
                ->where('umf.meternumber', 'like', $val . '%');
            if ($type == '') {
                //ถ้าเป็นการค้นหาจาก การจ่ายบิล payment->index
                $userfilter = $userfilter->whereIn('iv.status', ['invoice', 'owe']);
            } elseif ($type == 'search_history') {
                $userfilter = $userfilter->where('iv.receipt_id', "<>", 0);
            }
            $userfilter = $userfilter->select('uf.name', 'umf.meternumber', 'uf.address', 'z.zone_name')
                ->groupBy('umf.meternumber')
                ->get();
            foreach ($userfilter as $user) {
                // array_push($userArray, $user->meternumber);
                array_push($userArray, $user->name . ' - ' . ' เลขที่ ' . $user->address . ' ' . $user->zone_name . ' - ' . $user->meternumber);
            }
        } else if (preg_match('/^[ก-ฮ]/', $val)) {
            //ค้นหาจากรายชื่อ
            $userfilter = $this->searchQueryForAddresAndName($val, 'name', $type, 'aa');

            foreach ($userfilter as $user) {
                array_push($userArray, $user->name . ' - ' . ' เลขที่ ' . $user->address . ' ' . $user->zone_name . ' - ' . $user->meternumber);
            }
        } else if ($val == "all") {
            $userfilter = $this->searchQueryForAddresAndName($val, 'all', $type, 'aa');
            foreach ($userfilter as $user) {
                array_push($userArray, $user->name . ' - ' . ' เลขที่ ' . $user->address . ' ' . $user->zone_name . ' - ' . $user->meternumber);
            }
        }
        //หา ข้อมูล user

        return response()->json($userArray);
    }
    public function search2(REQUEST $request)
    {

        $val = $request->get('name');
        $type = $request->get('type');
        $val_lenght = $request->get('name_length');
        $userArray = [];
        if (intval($request->get('name')) > 0 && strpos($val, 'HS') == 0) {
            //ค้นหาโดยบ้านเลขที่
            $userfilter = $this->searchQueryForAddresAndName($val, 'address', $type, $val_lenght);
            $userArray = collect($userfilter)->pluck('aa');
        } elseif (preg_match('/[HhSs]+[0-9]/', $val)) {
            $userfilter = DB::table('user_meter_infos as umf')
                ->join('zone as z', 'umf.undertake_zone_id', '=', 'z.id')
                ->join('user_profile as uf', 'uf.user_id', '=', 'umf.user_id')
                ->join('invoice as inv', 'inv.user_id', '=', 'umf.user_id')
                ->where('umf.meternumber', 'like', $val . '%');
            $userfilter = $userfilter->select(
                DB::RAW('CONCAT(uf.address," ", z.zone_name, " - ",uf.name," - ",umf.meternumber) as aa')
            )
                ->orwhere('status', '=', 'invoice')
                ->orwhere('status', '=', 'owe')
                ->groupBy('umf.meternumber')
                ->get();
            $userArray = collect($userfilter)->pluck('aa');
        } else if (preg_match('/^[ก-ฮ]/', $val)) {
            //ค้นหาจากรายชื่อ
            $userArray = collect($this->searchQueryForAddresAndName($val, 'name', $type, $val_lenght))->pluck('aa');
        } else if ($val == "all") {
            $userfilter = $this->searchQueryForAddresAndName($val, 'all', $type, $val_lenght);
            $userArray = collect($userfilter)->pluck('aa');
        }
        return response()->json($userArray);
    }

    private function searchQueryForAddresAndName($val, $seachby, $type, $val_lenght)
    {
        $userfilter = DB::table('user_profile as uf')
            ->join('user_meter_infos as umf', 'umf.user_id', '=', 'uf.user_id')
            ->join('zone as z', 'uf.zone_id', '=', 'z.id');

        if ($seachby == 'address') {
            $userfilter = $userfilter->where('uf.address', 'like', $val . '%')
                ->select(
                    DB::RAW('CONCAT(uf.address," ", z.zone_name, " - ",uf.name," - ",umf.meternumber, " ", umf.comment) as aa')
                );
        } else if ($seachby == 'name') {
            //type == name
            $userfilter = $userfilter->where('uf.name', 'like', '%' . $val . '%')
                ->select(
                    DB::RAW('CONCAT(uf.name," ", uf.address, " - ",z.zone_name," - ",umf.meternumber, " ", umf.comment) as aa')

                );
        } else if ($seachby == 'all') {
            //type == name
            $userfilter = $userfilter->select(
                DB::RAW('CONCAT(uf.address," ", z.zone_name, " - ",uf.name," - ",umf.meternumber, " ", umf.comment) as aa')
            );
        }
        //ถ้าเป็นการชำระค่าน้ำ
        if ($type == '') {
            return $userfilter
                // ->whereIn('iv.status', ['invoice', 'owe'])
                ->groupBy('umf.meternumber')->get();
        } else {
            //ถ้าเป็นการค้าหาประวัติการชำระ
            return $userfilter->groupBy('umf.meternumber')
                ->orderBy('uf.address', 'asc')
                ->get();
        }
    }
    private function updateApiToken($id, $token)
    {
        $result = User::find($id);
        $result->remember_token = $token;
        $result->save();
    }

    private function usersInfos($subzone_id = "all")
    {
        $active_users = DB::table('user_meter_infos as umf')
            ->join('users as u', 'u.id', '=', 'umf.user_id')
            ->join('zones', 'zones.id', '=', 'u.zone_id')
            ->where('umf.status', '=', 'active')
            ->orWhere('umf.status', '=', 'inactive')
            ->select(
                'umf.meternumber',
                'umf.meter_id',
                'umf.factory_no',
                'umf.submeter_name',
                'umf.meter_address',
                'umf.user_id',
                'u.prefix','u.firstname','u.lastname',
                'umf.acceptance_date',
                'u.address', 'u.created_at',
                'zones.zone_name',
            );
        if ($subzone_id != 'all') {
            $active_users = $active_users->where('u.subzone_id', '=', $subzone_id);
        }
        $active_users = $active_users->orderBy('umf.user_id', 'asc')->get();

        $arr = [];
        foreach ($active_users as $key => $user) {
            $arr[] =[
               'meternumber'       => $user->meternumber,
                'user_id'           => $user->user_id,
                'factory_no'        => $user->factory_no,
                'fullname'          => $user->prefix."".$user->firstname." ".$user->lastname,
                'acceptance_date'   => $user->acceptance_date,
                'submeter_name'     => $user->submeter_name == "" ? "-" : $user->submeter_name,
                'address'           => $user->meter_address,
                'zone_name'         => $user->zone_name,
                'showLink'          => '<div class="dropstart float-lg-end ms-auto pe-0">
                                            <a href="javascript:;" class="cursor-pointer" id="dropdownTable'.$user->meter_id.'" data-bs-toggle="dropdown" aria-expanded="true">
                                            <i class="fa fa-ellipsis-h text-secondary" aria-hidden="true"></i>
                                            </a>
                                            <ul class="dropdown-menu px-2 py-3 ms-sm-n4 ms-n5 " aria-labelledby="dropdownTable'.$user->meter_id.'"  data-popper-placement="left-start">
                                                <li><a class="dropdown-item border-radius-md" href="/admin/users/'.$user->meter_id.'/edit/addmeter">เพิ่มมิเตอร์ใหม่</a></li>
                                                <li><a class="dropdown-item border-radius-md" href="/admin/users/'.$user->meter_id.'/edit">แก้ไขข้อมูล</a></li>
                                                <li>

                                                <a class="dropdown-item border-radius-md destroy" href="/admin/users/'.$user->user_id.'/destroy">ยกเลิกการใช้งาน</a>
                                                </li>
                                            </ul>
                                            </div>
                                            '

            ];
        }
        return $arr;
    }
    public function set_session_id($user_id, $session_id)
    {
        User::where('id', $user_id)->update([
            'remember_token' => $session_id,
        ]);
    }

    public function users_subzone_count($subzone_id = null)
    {
        return UserMerterInfo::where('status', 'active')
            ->where('undertake_subzone_id', $subzone_id)->count();
    }

    public function usermeter_info_get_invoice_status_count($subzone_id, $status)
    {

    //    $users = User::where('zone_id', 19)->where('status', 1)
    //     ->with([
    //         'usermeterinfos' => function($q){
    //             return $q->select('meter_id', 'user_id', 'status', 'undertake_zone_id')
    //             ->whereIn('status', ['active']);
    //         },
    //         'usermeterinfos.invoice' => function($q){
    //             return $q->select('meter_id_fk', 'inv_period_id_fk', 'status')
    //             ->where('inv_period_id_fk',  51);
    //         }
    //     ])
    //     ->where('role_id', 3)
    //     ->get(['id', 'firstname', 'lastname','zone_id']);

    //     $usermeterinfoNotEmpty = collect($users)->filter(function($v){
    //         return collect($v->usermeterinfos)->isNotEmpty();
    //     });
    //     foreach($usermeterinfoNotEmpty as $user){
    //         UserMerterInfo::where('user_id', $user->id)->update([
    //             'undertake_zone_id' => $user->zone_id,
    //             'undertake_subzone_id' => $user->zone_id,
    //         ]);
    //     }
    //     return 'ss';    
        // return  collect($users)->filter(function($v){
        //     return $v->zone_id != $v->usermeterinfos[0]->undertake_zone_id;
        // });


        $curr_inv_period = InvoicePeriod::where('status', 'active')->get('id')->first();
        $curr_inv_period_id = $curr_inv_period->id;
        $res = UserMerterInfo::where('undertake_subzone_id', $subzone_id)
            ->with(['invoice' => function ($query) use ($status,$curr_inv_period_id) {
                return $query->select('meter_id_fk')->where('status', $status)->where('inv_period_id_fk', $curr_inv_period_id);
            }])
            ->where('status', 'active')->get(['meter_id', 'status']);
        return collect($res)->filter(function ($item) {
            return collect($item->invoice)->isNotEmpty();
        })->count();
    }

}
