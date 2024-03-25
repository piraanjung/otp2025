  //เอา user status ทุกคน ทั้งที่ status active และ inactive
    // whereIn('subzone_id', [1,2,3,4,5,6,7,8,9,10,11,12,14,15,16,17,18,19,20,21,35])
    //subzone_id == 13  ไม่มี
//     $userProfile = UserProfile::whereIn('subzone_id', [19,20,21,35])->
//         with([
//             'user' => function($q){
//                 return $q->select('id','user_cat_id')
//                 ;
//             },
//             'invoice_old' => function($q){
//                 return $q->select('id','user_id','status','inv_period_id')
//                 ;
//             },
//             'invoice_history' => function($q){
//                 // return $q->select('*')
//                 return $q->select('id','user_id','status','inv_period_id')

//                 ;
//             },
//             'usermeter_info_old' => function($q){
//                 return $q->select('meternumber','user_id','status','undertake_zone_id', 'undertake_subzone_id')
//                 ;
//             }
//         ])
//         ->get();
//     //user ที่ มีstatus = 3 และมีข้อการจ่ายค่าน้ำ status paid invioce owe init
//    $userCate_3 = collect($userProfile)->filter(function($item){
//     return collect($item->invoice_old)->count() > 0  || collect($item->invoice_history)->count() > 0 ;
//   });

//insert เข้า usermeterInfo table

// $userArr = [];
// $usermeterinfoArr =[];
// $sq_init = SequenceNumber::where('id',1)->first();
// $init = $sq_init->user;
// $pass = Hash::make('hsu1234');
// $meternumber_code = Setting::where('name', 'meternumber_code')->get('values')->first();

//     foreach ($userCate_3 as $uprofile)  {
//         $userArr[] = [
//             'id' => $init,
//             'username' => "hsu01".$init,
//             'password' => $pass,
//             'prefix' => '',
//             'firstname' => $uprofile->name,
//             'lastname' =>  '',
//             'email'    => 'user'.$init.'@hs.lgov',
//             'line_id' => '',
//             'id_card' =>  $uprofile->id_card,
//             'phone' =>  $uprofile->phone,
//             'gender' => $uprofile->gender == "" ? 'w' : $uprofile->gender ,
//             'address' =>  $uprofile->address,
//             'zone_id' =>  $uprofile->zone_id,
//             'subzone_id' =>  $uprofile->usermeter_info_old[0]->undertake_subzone_id,
//             'tambon_code' =>  $uprofile->tambon_code,
//             'district_code' =>  $uprofile->district_code,
//             'province_code' =>  $uprofile->province_code,
//             'email_verified_at' =>  $uprofile->email_verified_at,
//             'remember_token' => $uprofile->remember_token,
//             'role_id' => 3,
//             'status' => $uprofile->deleted = 1 ? 'inactive' : 'active',
//             'created_at'=> $uprofile->created_at,
//             'updated_at'=> $uprofile->updated_at
//         ];

//         $usermeterinfoArr[] = [
//             'meter_id' => $uprofile->user_id,
//             'user_id' => $init,
//             'meternumber' =>  FunctionsController::createNumberString($uprofile->user_id, $meternumber_code->values) ,
//             'meter_address' => $uprofile->address,
//             'undertake_zone_id' => $uprofile->usermeter_info_old[0]->undertake_zone_id,
//             'undertake_subzone_id' => $uprofile->usermeter_info_old[0]->undertake_subzone_id,
//             'acceptace_date' => date('Y-m-d', strtotime($uprofile->created_at)),
//             'status' => 'active',
//             'comment' => '',
//             'metertype_id' => 2,
//             'owe_count' => 0,
//             'payment_id' => 1,
//             'discounttype' => 1,
//             'recorder_id' => 2,
//             'created_at'=> $uprofile->created_at,
//             'updated_at'=> $uprofile->updated_at
//         ];
//         $init++;
//     };
//    User::insert($userArr);
//     UserMerterInfo::insert($usermeterinfoArr);
//     $sq = SequenceNumber::where('id',1)->first();
// return SequenceNumber::where('id', 1)->update([
//     'tabmeter' => $sq->tabmeter + collect($usermeterinfoArr)->count(),
//     'user' => $sq->user + collect($userArr)->count(),
// ]);

// $setPrefix = User::where('role_id', 3)->get(['firstname', 'id']);
// $userPrefixs = collect($setPrefix)->filter(function ($prefix) {
//     return str_contains($prefix->firstname,'ครู');
// });
// foreach ($userPrefixs as $prefix) {
//      $firstname = trim($prefix->firstname);
        // $name =  explode("ร้าน", $firstname)[1];
        // // $nameExp =  explode(" ", $name);
        // // $lastname = empty($nameExp[1]) ? $nameExp[2] : $nameExp[1];
        // User::where('id', $prefix->id)->update([
        //     'prefix'=> "ร้าน",
        //     "firstname" => trim($name),
        //     'lastname' => "",
        //     'remember_token'=> $firstname
        // ]);
        // $name =  explode("ครู", $firstname)[1];
        // $nameExp =  explode(" ", $name);
        // $lasname = empty($nameExp[1]) ? $nameExp[2] : $nameExp[1];
        // User::where('id', $prefix->id)->update([
        //     'prefix'=> "อาจารย์",
        //     "firstname" => trim($nameExp[0]),
        //     'lastname' => trim($lasname),
        //     'remember_token'=> $firstname
        // ]);
// }
// return $setPrefix;
//     // $users = User::where('role_id', 3)->get('id');
//     // foreach ($users as $user) {
//     //     $user->assignRole('user');
//     // }
// $umeterimfosSql = UserMerterInfo::with([
//     'invoice_old' => function($q){
//         $q->select('id','user_id', 'inv_period_id', 'status')
//         ->whereIn('status', ['init', 'owe', 'invoice']);
//     },
//     'invoice_history' => function($q){
//         $q->select('id','user_id', 'inv_period_id', 'status')
//         ->whereIn('status', ['init', 'owe', 'invoice']);
//     }
//     ])->get(['meter_id', 'user_id']);
//     $umeterimfos = collect($umeterimfosSql)->filter(function($v){
//         return collect($v->invoice_history)->count() > 0;
//     });

//     foreach( $umeterimfos as $m ){
//         $InvPeriod = InvoiceOld::where('user_id', $m->meter_id)->get(['inv_period_id']);
//         $InvPeriod_arr = [];
//         foreach($InvPeriod as $inp){
//             $InvPeriod_arr[] = $inp->inv_period_id;
//         }
//         foreach($m->invoice_history as $k => $v){
//             $res = in_array($v->inv_period_id, $InvPeriod_arr);
//             if($res == 1){
//                 dd($v);
//                 //ลบ invoice_history
//                 InvoiceHistoty::where('id', $v->id)->delete();
//             }else{
//                 dd($v);
//             }
//         }
//     }
//     return $umeterimfos;


//  เอา invoice_history data ไปไว้ที่ invoice_history_new
$invHis = InvoiceHistoty::all();
$arr = [];
return User::all();
 return$casheirs = Accounting::where('status', 1)->get('cashier');
dd($casheirs);
return collect($casheirs)->unique('cashier');
$accounting = Accounting::all();
// $accTransArr = [];
// foreach ($accounting as $k => $v) {
//     $accTransArr[] = [
//         'user_id_fk' =>$v->user_id,
//         'paidsum', 6,2 =>$v->total,
//         'vatsum', 6,2 =>0,
//         'totalpaidsum' => $v->total,
//         'status'=>$v->status,
//         'cashier' =>$v->cashier,
//         'created_at' => $v->created_at,
//         'updated_at'=> $v->updated_at
//     ];
// }
// AccTransactionßs::insert($accTransArr)

// foreach ($invHis as $k => $v) {
//     $water_used = $v->currentmeter - $v->lastmeter;
//     $paid = $water_used== 0 ? 10 : $water_used*8;
//     $arr[] = [
//         'inv_id' => $v->id,
//         'meter_id_fk' => $v->user_id,
//         'inv_period_id_fk' => $v->inv_period_id,
//         'lastmeter' => $v->lastmeter,
//         'currentmeter' => $v->currentmeter,
//         'water_used' => $v->water_used,
//         'inv_type' => $water_used == 0 ? "r" : "u",
//         'paid' =>$paid,
//         'vat'  => 0,
//         'totalpaid' => $paid,
//         'status' => $v->status,
//         'acc_trans_id_fk' =>,
//         'comment' => $v->comment,
//         'recorder_id' => $v->recorder_id,
//         'created_at'=> $uprofile->created_at,
//         'updated_at'=> $uprofile->updated_at
//     ];
// }



$invoicePaid = collect(Invoice::
         where('status', 'paid')->
         get())->chunk(2000);

        //  foreach ($invoicePaid as $k => $vv) {
        //     foreach($vv as $v){
        //         $water_used = $v->currentmeter - $v->lastmeter;
        //         $paid = $water_used== 0 ? 10 : $water_used*8;
        //         $invType = $water_used== 0 ? 'r' : 'u';
        //         Invoice::where('inv_id', $v->inv_id)
        //         ->update([
        //             'water_used' => $water_used,
        //             'inv_type' => $invType,
        //             'paid' =>$paid,
        //             'vat'  => 0,
        //             'totalpaid' => $paid,
        //         ]);
        //     }
        // }
        // return 1;
        //  $arr = [];
        // foreach ($invoicePaid as $k => $vv) {
        //     foreach($vv as $v){
        //         $arr[] = [
        //             'inv_id' => $v->inv_id,
        //             'user_id' => $v->user_id,
        //             'meter_id_fk' => $v->meter_id_fk,
        //             'inv_period_id_fk' => $v->inv_period_id_fk,
        //             'lastmeter' => $v->lastmeter,
        //             'currentmeter' => $v->currentmeter,
        //             'water_used' => $v->water_used,
        //             'inv_type' => $v->inv_type,
        //             'paid' =>$v->paid,
        //             'vat'  => $v->vat,
        //             'totalpaid' => $v->totalpaid,
        //             'status' => $v->status,
        //             'acc_trans_id_fk' =>$v->acc_trans_id_fk,
        //             'comment' => $v->comment,
        //             'recorder_id' => $v->recorder_id,
        //             'created_at'=> $v->created_at,
        //             'updated_at'=> $v->updated_at
        //         ];
        //     }
        // }
        // InvoiceHistoty::insert($arr);


        ini_set('memory_limit', '512M');
