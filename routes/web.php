<?php

use App\Http\Controllers\Admin\ExcelController;
use App\Http\Controllers\Admin\IndexController;
use App\Http\Controllers\Admin\MetertypeController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ZoneController;
use App\Http\Controllers\Api\FunctionsController;
use App\Http\Controllers\BudgetYearController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\InvoicePeriodController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SubzoneController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\TransferOldDataToNewDBController;
use App\Models\Accounting;
use App\Models\AccTransactions;
use App\Models\Invoice;
use App\Models\InvoiceHistoryNew;
use App\Models\InvoiceHistoty;
use App\Models\InvoiceOld;
use App\Models\InvoicePeriod;
use App\Models\SequenceNumber;
use App\Models\Setting;
use App\Models\User;
use App\Models\UserMerterInfo;
use App\Models\UserOld;
use App\Models\UserProfile;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

Route::get('/', function () {
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
// AccTransactions::insert($accTransArr)

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

    return view('welcome');
});
Route::get('/liff', function () {
    return view('liff');
});

Auth::routes();

Route::get('/dashboard', function () {
    //เพิ่ม user active จาก user_profile เข้า user
    $userStatusActive = UserProfile::where('status','=', 1)->get(['user_id', 'name']);
    $userArr = [];
    $i = 3;
    // $userPass
    // foreach ($userStatusActive as $userStatus) {
    //     $userArr[] = [
    //         'username' => "HSU".FunctionsController::createNumberString($i,''),
    //         'password' => ,
    //         'prefix' => ,
    //         'firstname' => ,
    //         'lastname' => ,
    //         'email')->unique( => ,
    //         'line_id')->nullable( => ,
    //         'id_card' => ,
    //         'phone' => ,
    //         ender',['m', 'w'] => ,
    //         'address' => ,
    //         ('zone_id')->comment('หมู่หรือชุมชน แล้วแต่พื้นที่จะแยก' => ,
    //         ('subzone_id')->default(0 => ,
    //         'tambon_code' => ,
    //         'district_code' => ,
    //         'province_code' => ,
    //         mp('email_verified_at')->nullable( => ,
    //         rToken( => ,
    //         'role_id',5)->nullable( => ,
    //         tatus', ['active', 'inactive'])->default('active');
    //     ];
    // };


    // -----------------------------------------------
//     $userStatusDeleted_1 =  UserProfile::where('deleted', 1)
//     ->with([
//         'invoice_old' => function($q){
//             return $q->select('id','user_id','status','inv_period_id')
//             ->whereIn('status', ['owe', 'invoice', 'init']);
//         },
//         'invoice_history' => function($q){
//             return $q->select('id','user_id','status','inv_period_id')
//             ->whereIn('status', ['owe', 'invoice', 'init']);
//         }
//     ])
//     ->get(['user_id', 'name']);

//    $userStatusDeleted_1_filter_have_oweOr`Invoice = collect($userStatusDeleted_1)->filter(function($v) {

//         return collect($v->invoice_history)->isNotEmpty() || collect($v->invoice_old)->isNotEmpty();
//     });

//     $invoice_history_And_invoice_old_dup = collect($userStatusDeleted_1_filter_have_oweOrInvoice)->filter(function($v) {
//         $invP_inv_his = collect($v->invoice_history)->pluck('inv_period_id')->toArray();
//         $invP_inv_old = collect($v->invoice_old)->pluck('inv_period_id')->toArray();
//         if(collect($invP_inv_his)->count()  ==  collect($invP_inv_old)->count()) {
//             // dd(collect( collect($invP_inv_old)->diff($invP_inv_his) )->count());
//             return collect( collect($invP_inv_old)->diff($invP_inv_his) )->count() == 0;
//         }
//     });


//     foreach($invoice_history_And_invoice_old_dup as $dup){
//         foreach($dup->invoice_history as $hisory){
//             InvoicePeriod::where('id', $hisory->id)->delete();
//         }
//     }
//     return 'ss';
//     $invoice_history_And_invoice_old_unique = collect($userStatusDeleted_1_filter_have_oweOrInvoice)->filter(function($v) {
//         $invP_inv_his = collect($v->invoice_history)->pluck('inv_period_id')->toArray();
//         $invP_inv_old = collect($v->invoice_old)->pluck('inv_period_id')->toArray();
//         if(collect($invP_inv_his)->count()  !=  collect($invP_inv_old)->count()) {
//             // dd(collect( collect($invP_inv_old)->diff($invP_inv_his) )->count());
//             return collect( collect($invP_inv_old)->diff($invP_inv_his) )->count() > 0;
//         }
//     });
// return $invoice_history_And_invoice_old_unique;
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::middleware(['auth', 'role:admin'])->name('admin.')->prefix('admin')->group(function () {
    Route::get('/transfer_old_data', [TransferOldDataToNewDBController::class, 'index'])->name('transfer_old_data');
    Route::get('/', [IndexController::class, 'index'])->name('index');
    Route::resource('/roles', RoleController::class);
    Route::post('/roles/{role}/permissions', [RoleController::class, 'givePermission'])->name('roles.permissions');
    Route::delete('/roles/{role}/permissions/{permission}', [RoleController::class, 'revokePermission'])->name('roles.permissions.revoke');
    Route::resource('/permissions', PermissionController::class);
    Route::post('/permissions/{permission}/roles', [PermissionController::class, 'assignRole'])->name('permissions.roles');
    Route::delete('/permissions/{permission}/roles/{role}', [PermissionController::class, 'removeRole'])->name('permissions.roles.remove');

    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/staff', [UserController::class, 'staff'])->name('users.staff');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::post('/users/store', [UserController::class, 'store'])->name('users.store');
    Route::post('/users/users_search', [UserController::class, 'users_search'])->name('users.users_search');
    Route::put('/users/{user}/update', [UserController::class, 'update'])->name('users.update');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::get('/users/{user}/history', [UserController::class, 'history'])->name('users.history');
    Route::post('/users/{user}/roles', [UserController::class, 'assignRole'])->name('users.roles');
    Route::delete('/users/{user}/roles/{role}', [UserController::class, 'removeRole'])->name('users.roles.remove');
    Route::post('/users/{user}/permissions', [UserController::class, 'givePermission'])->name('users.permissions');
    Route::delete('/users/{user}/permissions/{permission}', [UserController::class, 'revokePermission'])->name('users.permissions.revoke');

    Route::resource('/invoice_period',InvoicePeriodController::class);
    Route::get('/metertype/{metertype_id}/infos', [MetertypeController::class, 'infos'])->name('metertype.infos');

    Route::resource('/metertype',MetertypeController::class);
    Route::resource('/budgetyear',BudgetYearController::class);
    Route::resource('/zone',ZoneController::class);
    Route::resource('/subzone',SubzoneController::class);
    Route::get('/subzone/{zone_id}/getSubzone', [SubzoneController::class, 'getSubzone'])->name('subzone.getSubzone');


    Route::get('/settings/invoice', [SettingsController::class, 'invoice'])->name('settings.invoice');
    Route::post('/settings/invoice_and_vat', [SettingsController::class, 'update_invoice_and_vat'])->name('settings.invoice_and_vat');
    Route::post('/settings/create_and_update', [SettingsController::class, 'create_and_update'])->name('settings.create_and_update');
    Route::resource('/settings',SettingsController::class);
    Route::resource('/excel',ExcelController::class);

});
Route::resource('/test',TestController::class);


Route::middleware(['auth', 'role:admin|finance'])->group(function () {
    Route::get('/payment/paymenthistory/{inv_period}/{subzone_id}', [PaymentController::class, 'paymenthistory'])->name('payment.paymenthistory');
    Route::post('/payment/search', [PaymentController::class, 'search'])->name('payment.search');
    Route::post('/payment/index_search_by_suzone', [PaymentController::class, 'index_search_by_suzone'])->name('payment.index_search_by_suzone');
    Route::get('/payment/receipt_print/{account_id_fk?}', [PaymentController::class, 'receipt_print'])->name('payment.receipt_print');
    Route::get('/payment/receipt_print_history/{account_id_fk?}', [PaymentController::class, 'receipt_print_history'])->name('payment.receipt_print_history');
    Route::resource('/payment', PaymentController::class);
    Route::resource('/invoice',InvoiceController::class);
    Route::get('/invoice/{zone_id}/{curr_inv_prd}/zone_create/{new_user?}',[InvoiceController::class,'zone_create' ])->name('invoice.zone_create');
    Route::get('/invoice/{subzone_id}/zone_edit/{curr_inv_prd}',[InvoiceController::class,'zone_edit' ])->name('invoice.zone_edit');
    Route::get('/invoice/{subzone_id}/zone_update',[InvoiceController::class,'zone_update' ])->name('invoice.zone_update');
    Route::get('/invoice/{subzone_id}/invoiced_lists',[InvoiceController::class,'invoiced_lists' ])->name('invoice.invoiced_lists');
    Route::post('invoice/print_multi_invoice', [InvoiceController::class,'print_multi_invoice' ])->name('invoice.print_multi_invoice');


    Route::get('reports/owe',[ReportsController::class,'owe' ])->name('reports.owe');
    Route::post('reports/dailypayment',[ReportsController::class,'dailypayment' ])->name('reports.dailypayment');
    Route::get('reports/dailypayment2',[ReportsController::class,'dailypayment2' ])->name('reports.dailypayment2');
    Route::post('reports/owe_search',[ReportsController::class,'owe_search' ])->name('reports.owe_search');
});

require __DIR__ . '/auth.php';
