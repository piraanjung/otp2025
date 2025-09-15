<?php

namespace App\Http\Controllers;

use App\Models\KeptKaya\UserWastePreference;
use App\Models\Tabwater\SequenceNumber;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class LineLiffController extends Controller
{
    public function index(){
         $qrCode = QrCode::size(300)->generate('https://example.com');

        return view('lineliff.index', compact('qrCode'));
        
    }

    public function dashboard($user_waste_pref_id){
        $userWastePref = UserWastePreference::with('user', 'user.purchaseTransactions')->where('id', $user_waste_pref_id)->get()->first();
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

    public function fine_line_id($lineUserId, $displayName, $imagUrl){
        $user = User::with('wastePreference')->where('line_id', $lineUserId)
                ->whereHas('wastePreference')
                ->get()->first();
        $res = 0;
        if(collect($user)->isEmpty()){
            $user_no = SequenceNumber::where('id', 1)->get('user')->first();
            User::create([
                'id' => $user_no->user,
                'firstname' => $displayName,
                'line_id' => $lineUserId,
                'image' => $imagUrl,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

           $userWastPref = UserWastePreference::create([
                'user_id' => $user_no->user,
                'is_annual_collection' => 0,
                'is_waste_bank' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            SequenceNumber::where('id', 1)->update([
                'user' => $user_no->user + 1
            ]);
            $res = 1;
            $user_waste_pref_id = $userWastPref->id;
        }else{
             $res = 1;
             $user_waste_pref_id = $user->wastePreference->id;
        }
    $qrCodeString = QrCode::size(300)->generate('https://example.com');

        return response()->json(['res' => $res,
                'user_waste_pref_id' => $user_waste_pref_id, 'qrcode' =>$qrCodeString]);
    }
}
