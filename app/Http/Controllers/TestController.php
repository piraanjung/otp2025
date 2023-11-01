<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Api\FunctionsController;
use App\Models\Admin\UserProfile;
use App\Models\SequenceNumber;
use App\Models\User;
use App\Models\UserMerterInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class TestController extends Controller
{

    public function index(){
        // add user
        $userArr = [
            [
                "username"=> 'finance','email'=> 'fn1@gmail.com','password'=> bcrypt('1234'),
                'status' => 'active', 'role'=>'finance',
                "name" => 'fn1', "id_card" => '11', "phone" => '11', "gender" => 'm', "address" => '1',
                "zone_id" => '1', "subzone_id" => '1', "tambon_code" => '1', "district_code" => '1',
                "province_code" => '1',
                "meter_address" => "1/1","undertake_zone_id" => '1', "undertake_subzone_id" => "1",
                "acceptace_date" =>date(Now()) ,"comment" => "xx", "metertype_id" => "1",
                "owe_count" => "0","payment_id" => "1","discounttype" => "1","recorder_id" => 1
            ],
            [
                "username"=> 'user1','email'=> 'user1@gmail.com','password'=> bcrypt('1234'),
                'status' => 'active', 'role'=>'user',
                "name" => 'user1', "id_card" => '11', "phone" => '11', "gender" => 'm', "address" => '1',
                "zone_id" => '1', "subzone_id" => '1', "tambon_code" => '1', "district_code" => '1',
                "province_code" => '1',
                "meter_address" => "1/13","undertake_zone_id" => '1', "undertake_subzone_id" => "1",
                "acceptace_date" =>date(Now()) ,"comment" => "xx", "metertype_id" => "1",
                "owe_count" => "0","payment_id" => "1","discounttype" => "1","recorder_id" => 1
            ],
            [
                "username"=> 'user2','email'=> 'user2@gmail.com','password'=> bcrypt('1234'),
                'status' => 'active', 'role'=>'user',
                "name" => 'user2', "id_card" => '11', "phone" => '11', "gender" => 'm', "address" => '1',
                "zone_id" => '1', "subzone_id" => '1', "tambon_code" => '1', "district_code" => '1',
                "province_code" => '1',
                "meter_address" => "1/11","undertake_zone_id" => '1', "undertake_subzone_id" => "1",
                "acceptace_date" =>date(Now()) ,"comment" => "xx", "metertype_id" => "1",
                "owe_count" => "0","payment_id" => "1","discounttype" => "1","recorder_id" => 1
            ]
        ];
        //ลบ user
        $users = User::all();
        foreach ($users as $user){
            $this->detroyUser($user);
        }

        foreach ($userArr as $user){
            $this->CreateUser($user);
        }
    }

    private function detroyUser($user){
        //ลบผู้ใช้งาน และทำการ set auto increment number ด้วย


        $userCrtl  = new UserController();
        $userCrtl->destroy($user);

        FunctionsController::reset_auto_increment_when_deleted("user_profile") ;
        FunctionsController::reset_auto_increment_when_deleted("user_meter_infos") ;
    }
    private function CreateUser($user){
        $user_created = User::create( $user );
         $user['user_id'] = $user_created->id;
        UserProfile::create($user );




        //update user and tabmeter if user role = user
        if($user['role'] == "user"){
            $user['meter_id'] = SequenceNumber::where('id' , 1)->first()->tabmeter;

            $user['meternumber'] = FunctionsController::createInvoiceNumberString($user_created->id);
            UserMerterInfo::create($user);
            SequenceNumber::where("id",1 )->update([
                "tabmeter" => $user['meter_id']+ 1
            ]);
        }
    }
}
