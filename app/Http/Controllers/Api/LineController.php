<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admin\ManagesTenantConnection;
use App\Models\Admin\Organization;
use App\Models\Admin\Province;
use App\Models\KeptKaya\KPAccounts;
use App\Models\KeptKaya\KpUserWastePreference;
use App\Models\SuperUser;
use App\Models\Tabwater\SequenceNumber;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class LineController extends Controller
{

    public function index(Request $request)
    {
       return $provinces = Province::get(['id', 'province_name']);
        return view('lineliff.index', compact('provinces'));
    }
    public function fine_line_id(Request $request)
    {
        $res = 0;
        $waste_pref_id = 0;
        //check
        $user = User::where('line_id', $request->userId)
                ->with('wastePreference')
                ->first();
        if(collect($user)->isEmpty() || collect($user->wastePreference)->isEmpty()){
            $org_id     = 0; 
            $user_id    = 0;
        }else{
            $org_id         = $user->org_id_fk;
            $res            = 1;
            $user_id        = $user->id;
            $waste_pref_id  = $user->wastePreference->id;
        }
        
       
        return response()->json([
            'res'           => $res,
            'user_id'       => $user_id,
            'org_id'        => $org_id,
            'waste_pref_id' => $waste_pref_id

        ]);
    }

    public function user_line_register(Request $request)
    {

        $user =User::create([
            'firstname'     => $request->firstname,
            'lastname'      => $request->lastname,
            'line_id'       => $request->line_user_id,
            'image'         => $request->line_user_image,
            'phone'         => $request->phoneNum,
            'tambon_code'   => $request->tambon_id,
            'district_code' => $request->district_id,
            'province_code' => $request->province_id,
            'org_id_fk'     => $request->org_id,
            'zone_id'       => $request->zone_id,
            'address'       => $request->address,
            'subzone_id' => $request->subzone_id,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $userWastPref = KpUserWastePreference::create([
            'user_id' => $user->id,
            'is_annual_collection' => 0,
            'is_waste_bank' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        (new KPAccounts())->registerAccount($userWastPref->id) ;

        return response()->json([
            'res' => 1,
            'waste_pref_id' => $userWastPref->id

        ]);
    }

    public function user_qrcode()
    {
        return  view('lineliff.user_qrcode');
    }

    public function dashboard($user_waste_pref_id, $db_conn = "envsogo_main")
    {
        $userWastePref = KpUserWastePreference::with('user', 'purchaseTransactions')->where('id', $user_waste_pref_id)->get()->first();
        $qrcode = QrCode::size(300)->generate($user_waste_pref_id);
        return view('lineliff.dashboard', compact('userWastePref', 'qrcode'));
    }


    public function update_user_by_phone(Request $request)
    {
        
        // return $request;
        $user_org = (new Organization())->setConnection('envsogo_main')::find($request->org_id);

       
        $_user = (new User())->setConnection($user_org->org_database)->where('phone', $request->phoneNum)->get()->first();
        $res            = 0;
        $user_id        = 0;
        $waste_pref_id  = 0;

        $user_org   = Organization::find($request->org_id);

        $local_user = (new User())->setConnection($user_org->org_database)
            ->where('phone', $request->phoneNum)
            ->where('line_id', $request->line_user_id)->get()->first();

        if ($_user) {
            $userUpdate = SuperUser::find($_user->id);
            $userUpdate->province_code  = $request->province_id;
            $userUpdate->district_code  = $request->district_id;
            $userUpdate->tambon_code    =  $request->tambon_id;
            $userUpdate->org_id_fk      = $request->org_id;
            $userUpdate->line_id        = $request->line_user_id;
            $userUpdate->image          = $request->line_user_image;
            $userUpdate->save();

            $user_org = Organization::find($request->org_id);
            $local_user = (new User())->setConnection($user_org->org_database)->where('phone', $request->phoneNum)
            ->where('line_id', $request->line_user_id)->get()->first();
            
            $local_user_wastePreference = (new KpUserWastePreference())->setConnection($user_org->org_database)->where('user_id', $local_user->id)->get();
            if (collect($local_user_wastePreference)->isEmpty()) {
                $newUWastePref = (new KpUserWastePreference())->setConnection($user_org->org_database)->create([
                    'user_id' => $_user->id,
                    'is_annual_collection' => 0,
                    'is_waste_bank' => 1,
                ]);

                $waste_pref_id  = $newUWastePref->id;
            }


            $user_id = $_user->id;
            $res = 1;
        } else {
            //ถ้ายังไม่มีข้อมูลให้ ทำการ create

            //บันทึก new user ที่ envsogo_main
            $new_user               =  new SuperUser();
            $new_user->firstname    = $request->displayName;
            $new_user->line_id      = $request->line_user_id;
            $new_user->phone        = $request->phoneNum;
            $new_user->org_id_fk    = $request->org_id;
            $new_user->image        = $request->line_user_image;
            $new_user->created_at   = date("Y-m-d H:i:s");
            $new_user->updated_at   = date("Y-m-d H:i:s");
            $new_user->save();
           
            //บันทึก new user ที่ envsogo_ ตาม org_id ของ user
            $userCount = (new SequenceNumber())->setConnection($user_org->org_database)->where('id', 1)->get('user')->first();
            
            $local_user                 = (new User())->setConnection($user_org->org_database);
            $local_user->id             =  $userCount->user; 
            $local_user->firstname      = $request->displayName;
            $local_user->line_id        = $request->line_user_id;
            $local_user->phone          = $request->phoneNum;
            $local_user->image          = $request->line_user_image;
            $local_user->org_id_fk      = $request->org_id;
            $local_user->tambon_code    = $request->tambon_id;
            $local_user->district_code  = $request->district_id;
            $local_user->province_code  = $request->province_id;
            $local_user->created_at     = date("Y-m-d H:i:s");
            $local_user->updated_at     = date("Y-m-d H:i:s");
            $local_user->save();
            
            $newUWastePref = (new KpUserWastePreference())->setConnection($user_org->org_database)->create([
                'user_id'               => $local_user->id,
                'is_annual_collection'  => 0,
                'is_waste_bank'         => 1,
                'created_at'            => date('Y-m-d H:i:s'),
                'updated_at'            => date('Y-m-d H:i:s'),
            ]);


            (new KPAccounts())->setConnection($user_org->org_database)->registerAccount($newUWastePref->id, $user_org->org_database);
           
            (new SequenceNumber())->setConnection($user_org->org_database)->where('id', 1)->update([
                'user' => $userCount->user + 1
            ]);
            $user_id        = $local_user->id;
            $res            = 1;
            $waste_pref_id  = $newUWastePref->id;
        }
        return response()->json([
            'res'           => $res,
            'user_id'       => $user_id,
            'org_id'        => $request->org_id,
            'waste_pref_id' => $waste_pref_id
        ]);
    }
}
