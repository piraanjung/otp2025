<?php

namespace App\Http\Controllers;

use App\Models\Admin\Organization;
use App\Models\KeptKaya\KPAccounts;
use App\Models\KeptKaya\UserWastePreference;
use App\Models\Tabwater\SequenceNumber;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class LineLiffController extends Controller
{
    public function index(Request $request){
       
        return view('lineliff.index');
        
    }

    public function dashboard($user_waste_pref_id, $reg =0){
        $uWastePref = UserWastePreference::find($user_waste_pref_id);
        $user = User::find($uWastePref->user_id);
        Auth::login($user);
        if($reg == 1){
            $user->assignRole('User');
            $user->givePermissionTo('access waste bank mobile');
        }

        $userWastePref = UserWastePreference::with('user', 'purchaseTransactions')->where('id', $user_waste_pref_id)->get()->first();
        $qrcode = QrCode::size(300)->generate($user_waste_pref_id);
        return view('lineliff.dashboard', compact('userWastePref', 'qrcode'));
    }

     public function handleLineLogin(Request $request)
    {
        $validatedData = $request->validate([
            'userId' => 'required|string',
            'displayName' => 'required|string',
            'pictureUrl' => 'nullable|string',
        ]);
        
        $lineUserId = $validatedData['userId'];

        // ค้นหา User จาก line_user_id
        $user = User::where('line_user_id', $lineUserId)->first();

        if ($user) {
            // ถ้าพบ: User ได้ลงทะเบียนไว้แล้ว
            Log::info("User with LINE ID {$lineUserId} logged in.");
            return response()->json([
                'status' => 'success',
                'message' => 'User logged in successfully.',
                'action' => 'login'
            ]);
        } else {
            // ถ้าไม่พบ: ลงทะเบียน User ใหม่
            $newUser = User::create([
                'name' => $validatedData['displayName'],
                'line_user_id' => $lineUserId,
                'picture_url' => $validatedData['pictureUrl'],
                // สามารถเพิ่มข้อมูลอื่นๆ ได้ตามต้องการ
            ]);

            Log::info("New user with LINE ID {$lineUserId} registered.");
            return response()->json([
                'status' => 'success',
                'message' => 'New user registered successfully.',
                'action' => 'register'
            ], 201);
        }
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
