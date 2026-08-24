<?php

namespace App\Http\Controllers\KeptKaya;

use App\Http\Controllers\Controller;
use App\Models\KpPointTransfer;
use App\Models\KpBankAccount;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PointController extends Controller
{

    public function create($pref_id)
    {

        // ดึงยอดแต้มปัจจุบันมาโชว์ในหน้าโอน
        $account = KpBankAccount::where('user_pref_id', $pref_id)->first();
        $recycleTotalPoints = $account ? $account->points : 0;

        return view('keptkayas.transfer_points', compact('recycleTotalPoints', 'pref_id'));
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
            $senderAcc = KpBankAccount::where('user_id', $senderId)->first();
            if (! $senderAcc || ($senderAcc->points ?? 0) < $amount) {
                return back()->with('error', 'แต้มของคุณไม่เพียงพอ');
            }

            $senderAcc->decrement('points', $amount);

            $receiverAcc = KpBankAccount::firstOrCreate(['user_id' => $receiver->id]);
            $receiverAcc->increment('points', $amount);

            KpPointTransfer::create([
                'sender_id' => $senderId,
                'receiver_id' => $receiver->id,
                'amount' => $amount,
                'note' => $request->note,
            ]);

            $receiverDisplay = trim(($receiver->name ?? '') ?: (($receiver->firstname ?? '') . ' ' . ($receiver->lastname ?? '')));

            return redirect()->route('keptkayas.transfer_points')->with('success', "โอนแต้มให้ {$receiverDisplay} สำเร็จ!");
        });
    }
}
