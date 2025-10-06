<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KeptKaya\KPAccounts;
use App\Models\KeptKaya\UserWastePreference;
use App\Models\Tabwater\SequenceNumber;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class LineController extends Controller
{

    public function index(Request $request)
    {

        return view('lineliff.index');
    }
    public function fine_line_id(Request $request)
    {
        $wastePreferenceID = 0;
        $user_id = 0;
        $user = User::with('wastePreference')->where('line_id', $request->userId)
            ->whereHas('wastePreference')
            ->get()->first();
        if (collect($user)->isNotEmpty()) {
            $wastePreferenceID = $user->wastePreference->id;
        }
        $res =  collect($user)->isEmpty() ? 0 : 1;
        return response()->json([
            'res' => $res,
            'waste_pref_id' => $wastePreferenceID

        ]);
    }

    public function user_line_register(Request $request)
    {
        $seqNumber = SequenceNumber::where('id', 1)->get('user')->first();
        User::create([
            'id' => $seqNumber->user,
            'firstname' => $request->displayName,
            'line_id' => $request->userId,
            'image' => $request->imagUrl,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $userWastPref = UserWastePreference::create([
            'user_id' => $seqNumber->user,
            'is_annual_collection' => 0,
            'is_waste_bank' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        (new KPAccounts())->registerAccount($userWastPref->id);

        SequenceNumber::where('id', 1)->update([
            'user' => $seqNumber->user + 1
        ]);
        return response()->json([
            'res' => 1,
            'waste_pref_id' => $userWastPref->id

        ]);
    }

    public function user_qrcode()
    {
        return  view('lineliff.user_qrcode');
    }

    public function dashboard($user_waste_pref_id)
    {
        $userWastePref = UserWastePreference::with('user', 'purchaseTransactions')->where('id', $user_waste_pref_id)->get()->first();
        $qrcode = QrCode::size(300)->generate($user_waste_pref_id);
        return view('lineliff.dashboard', compact('userWastePref', 'qrcode'));
    }


    public function update_user_by_phone(Request $request)
    {
        $_user = User::with('wastePreference')->where('phone', $request->phoneNum)->get()->first();
        $res = 0;
        $user_id = 0;
        $waste_pref_id = 0;
        if ($_user) {
            $userUpdate = User::find($_user->id);
            $userUpdate->line_id = $request->line_user_id;
            $userUpdate->image = $request->line_user_image;
            $userUpdate->save();

            if (collect($_user->wastePreference)->isEmpty()) {
                $newUWastePref = UserWastePreference::create([
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
            $seqNumber = SequenceNumber::where('id', 1)->get('user')->first();

            $user =  new User();
            $user->id = $seqNumber->user;
            $user->firstname = $request->displayName;
            $user->line_id = $request->line_user_id;
            $user->phone = $request->phoneNum;
            $user->image = $request->line_user_image;
            $user->created_at = date("Y-m-d H:i:s");
            $user->updated_at = date("Y-m-d H:i:s");
            $user->save();
           
            
            $newUWastePref = UserWastePreference::create([
                'user_id' => $seqNumber->user,
                'is_annual_collection' => 0,
                'is_waste_bank' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);


            (new KPAccounts())->registerAccount($newUWastePref->id);

            SequenceNumber::where('id', 1)->update([
                'user' => $seqNumber->user + 1
            ]);
            $user_id = $user->id;
            $res = 1;
            $waste_pref_id  = $newUWastePref->id;
        }
        return response()->json([
            'res' => $res,
            'user_id' => $user_id,
            'waste_pref_id' => $waste_pref_id
        ]);
    }
}
