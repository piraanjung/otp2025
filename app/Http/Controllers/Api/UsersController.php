<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\FunctionsController;
use App\Http\Controllers\Api\ZoneController;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoicePeriod;
use App\Models\UndertakerSubzone;
use App\Models\User;
use App\Models\UserMerterInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UsersController extends Controller
{
    public function __construct()
    {
    }
    public function index()
    {
        $active_users = $this->usersInfos('all');
        return response()->json($active_users);
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
        foreach ($user[0]->usermeterinfos->invoice as $u) {
            $date = explode(" ", $u->updated_at);
            $u->updated_at_th = $fn->engDateToThaiDateFormat($date[0]);
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
        $code = 200;
        $username = ($request->has('username') ? $request->username : 0);
        $passwords = ($request->has('passwords') ? $request->passwords : 0);
        $user_cate_id = ($request->has('user_cate_id') ? $request->user_cate_id : 0);

        if ($username == '' || $username == '0' || $passwords == '' || $passwords == '0') {
            $result = ['message' => 'ไม่พบผู้ใช้งาน'];
            $code = 204;
        } else {
            if ($user_cate_id == 4) {
                //เจ้าหน้าที่บันทึกมิเตอร์
                $result = User::where('username', $username)
                    ->with(
                        'user_profile',
                        'undertaker_subzone',
                        'undertaker_subzone.subzone',
                        'undertaker_subzone.subzone.zone'
                    )
                    ->first();

                $currentInvoicePeriod = InvoicePeriod::where('status', 'active')->first();
                $result->inv_period = $currentInvoicePeriod;
            } else {
                $result = User::where('username', $username)
                    ->with(
                        'user_profile',
                        'undertaker_subzone',
                        'undertaker_subzone.subzone',
                    )
                    ->first();
            }

            if (collect($result)->isNotEmpty()) {
                $result = $this->verifyhasPassword($passwords, $result);
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

    private function usersInfos($subzone_id)
    {
        $fnCtrl = new FunctionsController();
        $active_users = DB::table('user_meter_infos as umf')
            ->join('zone', 'zone.id', '=', 'umf.undertake_zone_id')
            ->join('subzone', 'subzone.id', '=', 'umf.undertake_subzone_id')
            ->join('user_profile as uf', 'uf.user_id', '=', 'umf.user_id')
            ->where('umf.status', '=', 'active')
            ->select(
                'umf.meternumber',
                'umf.user_id',
                'umf.id as meterId',
                'zone.zone_name',
                'subzone.subzone_name',
                'uf.name',
                'uf.address',
            );
        if ($subzone_id != 'all') {
            $active_users = $active_users->where('umf.undertake_subzone_id', '=', $subzone_id);
        }
        $active_users = $active_users->orderBy('umf.user_id', 'asc')
            ->get();

        foreach ($active_users as $key => $user) {
            //$meternumber = substr($user->meternumber, 2);

            $active_users[$key]->user_id_str = $user->user_id; //$fnCtrl->createInvoiceNumberString($user->user_id);
            $active_users[$key]->showLink = '<a href="users/show/' . $user->meterId . '" class="btn btn-block btn-info">ดู</a>';
            $active_users[$key]->editLink = '<a href="users/edit/3/' . $user->meterId . '" class="btn btn-block btn-warning">แก้ไข</a>';
        }
        return $active_users;
    }
    public function set_session_id($user_id, $session_id)
    {
        User::where('id', $user_id)->update([
            'remember_token' => $session_id,
        ]);

    }

    public function users_count()
    {
        return UserMerterInfo::where('deleted', 0)->count();
    }

}
