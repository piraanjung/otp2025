<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Api\FunctionsController;
use App\Models\Admin\BudgetYear;
use App\Models\Admin\Staff;
use App\Models\Admin\UserProfile;
use App\Models\QaAccTrans;
use App\Models\QaInvoice;
use App\Models\QaUser;
use App\Models\QaUsermeterInfos;
use App\Models\SequenceNumber;
use App\Models\Tabwater\TwAccTransactions;
use App\Models\Tabwater\TwInvoice;
use App\Models\Tabwater\TwInvoicePeriod;
use App\Models\Tabwater\TwMeterInfos;
use App\Models\User;
use App\Models\UserMerterInfo;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class TestController extends Controller
{

    public function index()
    {
        set_time_limit(600);

        $org2 = User::where('org_id_fk', 2)->get();
        foreach($org2 as $user){
            if($user->email == 'katsukipai16@gmail.com'){
                $user->assignRole("Admin");
            }
            $user->assignRole("User");
        }
        return;
        $twUers = User::all();
        foreach($twUers as $twU){
            // $twU->remember_token = QaUser::find($twU->age)->role_id;
            // $twU->save();
            $twU->assignRole('User');
            if($twU->remember_token == 2){
                $twU->assignRole('Finance Staff');
            }
             if($twU->remember_token == 5){
                $twU->assignRole('Tabwater Staff');
            }
        }
        return 'ss';
        // $users = DB::connection('qa')->select('select * from users'); 
        // // return collect($users)->count();
        // foreach($users as $user){
        //     try{
        //     $subzone_id = $user->subzone_id;
        //     if($subzone_id == 35){
        //         $subzone_id = 22;
        //     }else if($subzone_id == 13){
        //         $subzone_id =20;
        //     }
        //     DB::connection('envsogo_main')->table('users')->insert([
        //     "org_id_fk" => 1,
        //     "username" => $user->username,
        //     "prefix" => $user->prefix,
        //     "firstname" => $user->firstname,
        //     "lastname" => $user->lastname,
        //     "email" => collect($user->email)->isEmpty() ? $user->email : $user->username.$user->id."@gmail.com",
        //     "password" =>  $user->password,
        //     "id_card" =>  $user->id_card,
        //     "line_id" =>  $user->line_id,
        //     'line_user_id' => $user->line_id,
        //     "image" =>  "",
        //     "phone" =>  $user->phone,
        //     'age' =>  $user->id,
        //     'weight' => 0,
        //     'height' =>  0,
        //     "gender" =>  $user->gender ,
        //     "address" =>  $user->address,
        //     "zone_id" =>  $user->zone_id,
        //     "subzone_id" =>  $subzone_id,
        //     "tambon_code" =>  $user->tambon_code,
        //     "district_code" =>  $user->district_code,
        //     "province_code" =>  $user->province_code,
        //     "status" =>  $user->status == 1 ? 'active' : 'inactive',
        //     ]);

        //     }catch(Error $e){
        //         return $e;
        //     }

        // }
        // User::where('id' ,'>',0)->delete();
        // TwMeterInfos::where('id' ,'>',0)->delete();
        // TwInvoice::where('id' ,'>',0)->delete();
        // TwAccTransactions::where('id' ,'>',0)->delete();


        // $qaUsers = QaUser::with(['user_meter_info.invoices' => function ($q) {

        //     $q->WhereIn('inv_period_id_fk', [62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72]); // อย่าลืม select user_meter_info_id มาด้วยนะครับ

        // }])->get();

        // $userAcctives = collect($qaUsers)->filter(function ($v) {

        //     //มี member role 3 อันนี้มี user_meter_info แต่ เจ้าหน้าที่ role 2,5 ไม่มี

        //     return !collect($v->user_meter_info)->isEmpty() && !collect($v)->contains('role_id', [2, 5]);
        // });
        // $userChunk = collect($userAcctives)->chunk(100);
        // $i = 1;
        // $accsArray = [];
        // $accTArray = [];
        // $invArray = [];
        // foreach ($userChunk as $users) {


        //     foreach ($users as  $user) {
        //         $userNameEmpty = "user" . date("Hms") . $i++;

        //         $newUser = User::create([
        //             "org_id_fk" => 1,
        //             "username" => collect($user->username)->isEmpty() ? $userNameEmpty : $user->username,
        //             "prefix" => $user->prefix,
        //             "firstname" => $user->firstname,
        //             "lastname" => $user->lastname,
        //             "email" => collect($user->email)->isEmpty() || $user->email == 0 ||  $user->email == 1 ? $userNameEmpty . "@hz.lgov" :  $user->email,
        //             "password" => collect($user->password)->isEmpty() ? Hash::make($userNameEmpty) : $user->password,
        //             "id_card" => $user->id_card,
        //             "line_id" => $user->line_id,
        //             'line_user_id' => $user->line_id,
        //             "image" => "",
        //             "phone" => $user->phone,
        //             'age' => $user->id,
        //             'weight' => 0,
        //             'height' => 0,
        //             "gender" => $user->gender,
        //             "address" => $user->address,
        //             "zone_id" => $user->zone_id,
        //             "subzone_id" => $user->subzone_id,
        //             "tambon_code" => $user->tambon_code,
        //             "district_code" => $user->district_code,
        //             "province_code" => $user->province_code,
        //             "status" => $user->status == 1 ? "active" : "inactive",
        //         ]);

        //         $twmeter = TwMeterInfos::create([
        //             "meter_id" => $newUser->id,
        //             "org_id_fk" => 1,
        //             "meter_address" => $user->user_meter_info[0]->meter_address,
        //             'submeter_name' => "",
        //             "user_id" => $newUser->id,
        //             "meternumber" => "HS" . substr("0000", strlen($newUser->id)) . $newUser->id,
        //             "metertype_id" => 1,
        //             "undertake_zone_id" => $user->user_meter_info[0]->undertake_zone_id,
        //             "undertake_subzone_id" => $user->user_meter_info[0]->undertake_subzone_id,
        //             "acceptance_date" => $user->user_meter_info[0]->acceptance_date,
        //             "status" => $user->status == 1 ? "active" : "inactive",
        //             "payment_id" => $user->user_meter_info[0]->payment_id,
        //             // "recorder_id" => $user->user_meter_info[0]-> ,
        //             'cutmeter' => "0",
        //             'factory_no' => "",
        //             'inv_no_index' => $user->user_meter_info[0]->meter_id,
        //             'last_meter_recording' => 0
        //         ]);

        //         $invp = [62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72];
        //         foreach ($user->user_meter_info[0]->invoices as $inv) {
        //             $key = array_search($inv->inv_period_id_fk, $invp);
        //             if ($inv->acc_trans_id_fk > 0) {
        //                 array_push($accsArray, $inv->acc_trans_id_fk);
        //             }

        //             array_push($invArray, [
        //                 'inv_period_id_fk' => $key + 1,
        //                 'meter_id_fk' => $twmeter->id,
        //                 'lastmeter' => $inv->lastmeter,
        //                 'water_used' => $inv->water_used,
        //                 'reserve_meter' => $inv->reserve_meter,
        //                 'inv_type' => $inv->reserve_meter == 0 ? 'r' : 'u',
        //                 'paid' => $inv->paid,
        //                 'vat' => $inv->vat,
        //                 'totalpaid' => $inv->totalpaid,
        //                 'acc_trans_id_fk' => $inv->acc_trans_id_fk,
        //                 'currentmeter' => $inv->currentmeter,
        //                 'recorder_id' => $inv->recorder_id,
        //                 'status' => $inv->status,
        //                 'created_at' => $inv->created_at,
        //                 'updated_at' => $inv->updated_at,
        //                 'org_id_fk' => 1,

        //             ]);
        //         }

                // $accsArrayUniqs = collect($accsArray)->unique();
                // foreach($accsArrayUniqs as $acc){
                //     $accRes = QaAccTrans::where('id', $acc)->get()->first();
                //    // $invs = QaInvoice::where('acc_trans_id_fk', $acc)->get('reserve_meter');

                //    // $reserveSum = collect($invs)->sum('reserve_meter');
                //     array_push($accTArray,[
                //         "org_id_fk" => 1,
                //         "meter_id_fk" =>  $twmeter->id, 
                //         'vatsum'=> $accRes->vatsum,
                //          'reserve_meter_sum'=> $accRes->id,
                //          'paidsum'=> $accRes->paidsum,
                //          'totalpaidsum'=> $accRes->totalpaidsum,
                //          'cashier' => $accRes->cashier,
                //          'status' => '1',
                //          'created_at' => $accRes->created_at,
                //          'updated_at' => $accRes->updated_at,
                //     ]);
                // }

        //     }
        // }


    // foreach(collect($invArray)->chunk(50) as $invC){
    //     foreach($invC as  $inv){
    //         TwInvoice::insert($inv);
    //     }
         
    // }
    $accT = TwInvoice::where('acc_trans_id_fk', '>', 0)->get('acc_trans_id_fk');
    $uniq = collect($accT)->unique("acc_trans_id_fk");
    $accTArray = [];
    foreach(collect($uniq)->chunk(50) as $uniqC){
    foreach($uniqC as $uu){
        $u = QaAccTrans::find($uu->acc_trans_id_fk);
        $inv = TwInvoice::where('acc_trans_id_fk', $uu->acc_trans_id_fk)->get(['meter_id_fk', 'reserve_meter']);
        array_push($accTArray, [
            'org_id_fk' => 1, 
            'meter_id_fk' => $inv[0]->meter_id_fk, 
            'vatsum' => $u->vatsum, 
            'reserve_meter_sum' => $uu->acc_trans_id_fk, 
            'paidsum' => $u->paidsum, 
            'totalpaidsum' => $u->totalpaidsum, 
            'cashier' => $u->cashier, 
            'status' => '1',
                         'created_at' => $u->created_at,
                         'updated_at' => $u->updated_at,
        ]);
    }
    }
    foreach(collect($accTArray)->chunk(50) as $accC){
        foreach($accC as  $acc){
            TwAccTransactions::insert($acc);
        }
         
    }
        return 'xxx';
        return view('test.index');
        //     $invPs = DB::connection('envsogo_kp1')->table('budget_year as b')
        //    ->where('b.id', 2)
        //    ->join('invoice_period as ip', 'ip.budgetyear_id', '=', 'b.id')
        //    ->select(
        //     'b.id',
        //     'ip.id as ipId'
        //    )
        //    ->get()->pluck('ipId');

        //     foreach($invPs as $invP){
        //         DB::connection('envsogo_kp1')->table('invoice_history')
        //         ->where('inv_period_id_fk','=', $invP)
        //         ->delete();
        //     }
        //     return'ss';
        // return DB::connection('envsogo_kp1')->table('invoice')
        //     ->where('status',  'deleted')
        //     // ->where('deleted', '1')
        //     ->delete();
        // return    $this->addNewUsers();


        $users =  DB::connection('envsogo_kp1')->table('users as u')
            ->join('user_meter_infos as umf', 'umf.user_id', '=', 'u.id')
            ->join('invoice as inv', 'inv.meter_id_fk', '=', 'umf.meter_id')
            // ->join('invoice_period as invp', 'invp.id', '=', 'inv.inv_period_id_fk')
            // ->join('budget_year as by', 'by.id', '=', 'invp.budgetyear_id')

            ->select(
                // 'u.id as userId',
                // 'umf.meter_address',
                'umf.meter_id',
                // 'umf.meter_address',
                // 'umf.meternumber',
                // 'umf.metertype_id',
                // 'umf.undertake_zone_id',
                // 'umf.undertake_subzone_id',
                // 'umf.acceptance_date',
                // 'umf.status as umfStatus',
                // 'umf.payment_id',
                // 'umf.discounttype',
                // 'umf.recorder_id',
                // 'inv.acc_trans_id_fk',
                'inv.*',

                // 'inv.status',
                // 'inv.currentmeter'
            )
            // ->where('meter_id_fk', 300)
            ->get();


        foreach ($users as $inv) {

            $accTransIdOld = 0;
            if ($inv->status == 'paid') {
                $acc = DB::connection('envsogo_kp1')->table('acc_transactions')
                    ->where('id',  $inv->acc_trans_id_fk)->get()->first();
                $twAcc = TwAccTransactions::create([
                    'vatsum' => $inv->vat,
                    'reserve_meter_sum' => $inv->reserve_meter,
                    'paidsum' => $inv->paid,
                    'totalpaidsum' => $inv->totalpaid,
                    'cashier' => $acc->cashier,
                    'created_at' => $acc->created_at,
                    'updated_at' => $acc->updated_at
                ]);
                $accTransIdOld = $inv->acc_trans_id_fk;
                $accId = $twAcc->id;
            } else {
                $twAcc = TwAccTransactions::create([
                    'vatsum' => 0,
                    'reserve_meter_sum' => 0,
                    'paidsum' => 0,
                    'totalpaidsum' => 0,
                    'cashier' => 2859,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);

                $accId = $twAcc->id;
            }




            TwInvoice::create([
                'meter_id_fk' => $inv->meter_id_fk,
                'inv_period_id_fk' => $inv->inv_period_id_fk,
                'lastmeter' => $inv->lastmeter,
                'reserve_meter' => $inv->reserve_meter,
                'inv_type' => $inv->inv_type,
                'currentmeter' => $inv->currentmeter,
                'water_used' => $inv->water_used,
                'paid' => $inv->paid,
                'vat' => $inv->vat,
                'totalpaid' => $inv->totalpaid,
                'status' => $inv->status,
                'recorder_id' => $inv->recorder_id,
                'comment' => $accTransIdOld,
                'acc_trans_id_fk' => $accId,
                'printed_time' => 0,
                'created_at' => $inv->created_at,
                'updated_at' => $inv->updated_at
            ]);
        }

        // return $this->addNewTwMeterInfos($users);


    }

    private function addNewUsers()
    {
        $users =  DB::connection('envsogo_kp1')->table('users as u')
            ->get();

        foreach ($users as $user) {
            if ($user->role_id == 3) {
                $email = "user" . $user->id . "@hz.lgov";
            }
            $newuser = new User();
            $newuser->id = $user->id;
            $newuser->username = $user->username;
            $newuser->password = $user->password;
            $newuser->prefix = $user->prefix;
            $newuser->firstname = $user->firstname;
            $newuser->lastname = $user->lastname;
            $newuser->email = $user->role_id == 3 ? $email : $user->email;
            $newuser->line_id = $user->line_id;
            $newuser->id_card = $user->id_card;
            $newuser->phone = $user->phone;
            $newuser->gender = $user->gender == "" ? 'w' : $user->gender;
            $newuser->address = $user->address;
            $newuser->email_verified_at = $user->email_verified_at;
            $newuser->remember_token = $user->remember_token;
            $newuser->status = $user->status == 1 ? 'active' : 'deleted';
            $newuser->created_at = $user->created_at;
            $newuser->updated_at = $user->updated_at;
            $newuser->org_id_fk = 1;;
            $newuser->zone_id = $user->zone_id;
            $newuser->subzone_id = $user->subzone_id == 13 ? 36 : $user->subzone_id;
            $newuser->tambon_code = $user->tambon_code;
            $newuser->district_code = $user->district_code;
            $newuser->province_code = $user->province_code;

            $newuser->save();
            $newuser->assignRole('User');
            if ($user->role_id == 2 || $user->role_id == 5) {
                $staff = Staff::create([
                    'user_id' => $newuser->id,
                    'org_id_fk' => 1,
                    'status' => 'active',
                    'deleted' => '0',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
                $staff = User::find($newuser->id);
                if ($user->role_id = 2) {

                    $staff->assignRole('Admin');
                } else {
                    $staff->assignRole('Tabwater Staff');
                }
            }
        }
        return 'a';
    }
    private function addNewTwMeterInfos($users)
    {
        $userG = collect($users)->groupBy('meter_id');
        foreach ($userG as $user) {
            // return $user;
            $last_reading_meter = collect($user)->last()->currentmeter;
            TwMeterInfos::create([
                "meter_id" => $user[0]->meter_id,
                "org_id_fk" => 1,
                "meter_address" => $user[0]->meter_address,
                'submeter_name' => "",
                "user_id" => $user[0]->userId,
                "meternumber" => $user[0]->meternumber,
                "metertype_id" => $user[0]->metertype_id,
                "undertake_zone_id" => $user[0]->undertake_zone_id,
                "undertake_subzone_id" => $user[0]->undertake_subzone_id,
                "acceptance_date" => $user[0]->acceptance_date,
                "status" => $user[0]->umfStatus,
                "payment_id" => $user[0]->payment_id,
                "discounttype" => 0,
                "recorder_id" => $user[0]->recorder_id,
                'cutmeter' => '0',
                'factory_no' => "",
                'inv_no_index' => 1,
                'last_meter_recording' => $last_reading_meter
            ]);
        }
    }

    private function detroyUser($user)
    {
        //ลบผู้ใช้งาน และทำการ set auto increment number ด้วย


        $userCrtl  = new UserController();
        $userCrtl->destroy($user);

        FunctionsController::reset_auto_increment_when_deleted("user_profile");
        FunctionsController::reset_auto_increment_when_deleted("user_meter_infos");
    }
    private function CreateUser($user)
    {
        $user_created = User::create($user);
        $user['user_id'] = $user_created->id;
        UserProfile::create($user);




        //update user and tabmeter if user role = user
        if ($user['role'] == "user") {
            $user['meter_id'] = SequenceNumber::where('id', 1)->first()->tabmeter;

            $user['meternumber'] = FunctionsController::createInvoiceNumberString($user_created->id);
            UserMerterInfo::create($user);
            SequenceNumber::where("id", 1)->update([
                "tabmeter" => $user['meter_id'] + 1
            ]);
        }
    }
}
