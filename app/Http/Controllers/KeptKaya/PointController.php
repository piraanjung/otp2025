<?php

namespace App\Http\Controllers\KeptKaya;

use App\Http\Controllers\Controller;
use App\Models\KeptKaya\KpMoneyRequest;
use App\Models\RecycleBankAccount;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PointController extends Controller
{

    public function create()
    {
        $userId = Auth::id();

        // ดึงยอดแต้มปัจจุบันมาโชว์ในหน้าโอน
        $account = RecycleBankAccount::where('user_id', $userId)->first();
        $recycleTotalPoints = $account ? $account->points : 0;

        return view('keptkayas.transfer_points', compact('recycleTotalPoints'));
    }
    public function transfer(Request $request)
    {
        $request->validate([
            'receiver_phone' => 'required',
            'amount' => 'required|integer|min:1',
        ]);

        $senderId = Auth::id();
        $amount = $request->amount;

        // 1. ค้นหาผู้รับจากเบอร์โทรศัพท์ (หรือ ID)
        $receiver = User::where('phone', $request->receiver_phone)->first();
        if (!$receiver) return back()->with('error', 'ไม่พบผู้รับในระบบ');
        if ($receiver->id === $senderId) return back()->with('error', 'ไม่สามารถโอนให้ตัวเองได้');

        return DB::transaction(function () use ($senderId, $receiver, $amount, $request) {
            // 2. เช็คแต้มผู้โอน (จากตาราง RecycleBankAccount ของคุณ)
            $senderAcc = RecycleBankAccount::where('user_id', $senderId)->first();
            if ($senderAcc->points < $amount) return back()->with('error', 'แต้มของคุณไม่เพียงพอ');

            // 3. หักแต้มผู้โอน - เพิ่มแต้มผู้รับ
            $senderAcc->decrement('points', $amount);

            $receiverAcc = RecycleBankAccount::firstOrCreate(['user_id' => $receiver->id]);
            $receiverAcc->increment('points', $amount);

            // 4. บันทึกประวัติ
            KpMoneyRequest::create([
                'sender_id' => $senderId,
                'receiver_id' => $receiver->id,
                'amount' => $amount,
                'note' => $request->note
            ]);

            return redirect()->route('line.dashboard')->with('success', "โอนแต้มให้ {$receiver->name} สำเร็จ!");
        });
    }
}
