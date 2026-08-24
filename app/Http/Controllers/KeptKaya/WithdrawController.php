<?php

namespace App\Http\Controllers\KeptKaya;

use App\Http\Controllers\Controller;
use App\Models\KeptKaya\KpMoneyRequest;
use App\Models\KpBankAccount;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use Illuminate\Support\Facades\DB;

class WithdrawController extends Controller
{
    public function create($pref_id)
    {
        $account = KpBankAccount::where('user_pref_id', $pref_id)->first();

        // คำนวณวันอังคารหน้า
        // ถ้าวันนี้เป็นวันจันทร์/อาทิตย์ ก็นัดอังคารนี้เลย
        // แต่ถ้าเลยวันจันทร์ไปแล้ว (เช่น วันพุธ) ก็นัดอังคารหน้า
        $payoutDate = now()->next(Carbon::TUESDAY)->format('d/m/Y');

        return view('keptkayas.withdraw_form', compact('account', 'payoutDate'));
    }

    public function storeRequest(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:20',
            'is_proxy' => 'nullable',
            'proxy_name' => 'required_if:is_proxy,on'
        ]);

        $userId = FacadesAuth::id();
        $amount = $request->amount;

        return DB::transaction(function () use ($request, $userId, $amount) {
            // 1. เช็คยอดเงินจริงในบัญชี
            $account = KpBankAccount::where('user_id', $userId)->firstOrFail();

            if ($account->balance < $amount) {
                return back()->with('error', 'ยอดเงินคงเหลือไม่เพียงพอ');
            }

            // 2. สร้างรหัสลับ 6 หลัก
            $verificationCode = rand(100000, 999999);

            // 3. บันทึกคำขอถอนเงิน
            // สมมติใช้ตารางชื่อ kp_money_requests
            $withdraw = KpMoneyRequest::create([
                'user_id'           => $userId,
                'amount'            => $amount,
                'verification_code' => $verificationCode,
                'status'            => 'pending',
                'is_proxy'          => $request->has('is_proxy') ? 1 : 0,
                'proxy_name'        => $request->proxy_name,
                'payout_date'       => now()->next(Carbon::TUESDAY)->toDateString(),
            ]);

            // 4. ตัดยอดเงินทันที (เพื่อกันวงเงินไว้)
            $account->decrement('balance', $amount);

            return redirect()->route('keptkayas.withdraw.success', $withdraw->id);
        });
    }

    // หน้าแสดงรหัสลับให้ User ดู
    public function showSuccess($id)
    {
        $withdraw = KpMoneyRequest::findOrFail($id);
        return view('keptkayas.withdraw_success', compact('withdraw'));
    }
}
