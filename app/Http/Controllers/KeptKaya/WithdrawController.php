<?php

namespace App\Http\Controllers\KeptKaya;

use App\Http\Controllers\Controller;
use App\Models\KeptKaya\KpMoneyRequest;
use App\Models\KeptKaya\KpSetting;
use App\Models\KeptKaya\KpPurchaseTransaction;
use App\Models\KPBankAccount;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth ;
use Illuminate\Support\Facades\DB;

class WithdrawController extends Controller
{
    /**
     * Helper คำนวณวันจ่ายเงินสด (Payout Date) ถัดไปแบบ Dynamic ตาม Setting
     */
    private function getNextPayoutDate($orgId): Carbon
    {
        $payoutDaySetting = KpSetting::getValue($orgId, 'payout_day', 'TUESDAY'); // TUESDAY, WEDNESDAY, etc.
        $dayConstant = constant("\Carbon\Carbon::" . strtoupper($payoutDaySetting)) ?? Carbon::TUESDAY;

        return now()->next($dayConstant);
    }

    public function create(Request $request, $userIdParam = null)
{
    $currentUser =  User::find(Auth::id());
    $orgId = $currentUser->org_id_fk ?? 1;

    // เช็กสิทธิ์ว่าคนที่กำลังใช้งานเป็นเจ้าหน้าที่หรือไม่
    $isStaff = $currentUser->hasRole(['Admin', 'Super Admin', 'Staff']);

    // กำหนด targetUserId:
    // - ถ้าส่ง userIdParam มา ให้ใช้ userIdParam
    // - ถ้าไม่ส่งมา (เช่น สมาชิกกดผ่าน App หรือ เจ้าหน้าที่เปิดหน้าถอนเงินสดลอยๆ มา) 
    //   -> ถ้าเป็นสมาชิกปกติใช้ Auth::id(), ถ้าเป็น Staff และยังไม่ได้เลือกใคร ให้เป็น null
    if ($userIdParam) {
        $targetUserId = $userIdParam;
    } else {
        $targetUserId = $isStaff ? null : $currentUser->id;
    }

    // ดึงรายชื่อสมาชิกให้เจ้าหน้าที่เลือก (กรณีเป็น Staff)
    $members = [];
    if ($isStaff) {
        $members = \App\Models\User::where('org_id_fk', $orgId)
            ->whereHas('roles', fn($q) => $q->where('name', 'User'))
            ->get();
    }

    // ดึงข้อมูลบัญชีและตรวจสอบเงื่อนไขย้อนหลัง (ถ้ามี targetUserId)
    $account = null;
    $withdrawableAmount = 0;
    $hasRecentWasteSale = false;

    if ($targetUserId) {
        $account = KpBankAccount::where('user_id', $targetUserId)->first();
        if ($account) {
            $minReserve = (float) KpSetting::getValue($orgId, 'min_reserve', 300);
            $maxInactiveCycles = (int) KpSetting::getValue($orgId, 'max_inactive_cycles', 3);

            $withdrawableAmount = max(0, (float) $account->balance - $minReserve);

            $monthsAgo = now()->subMonths($maxInactiveCycles)->startOfMonth();
            $hasRecentWasteSale = KpPurchaseTransaction::where('user_id', $targetUserId)
                ->where('transaction_date', '>=', $monthsAgo)
                ->exists();
        }
    }

    $minReserve = (float) KpSetting::getValue($orgId, 'min_reserve', 300);
    $minWithdraw = (float) KpSetting::getValue($orgId, 'min_withdraw', 20);
    $maxInactiveCycles = (int) KpSetting::getValue($orgId, 'max_inactive_cycles', 3);
    $enableWelfare = KpSetting::getValue($orgId, 'enable_welfare', '0') == '1';
    $welfareMode = KpSetting::getValue($orgId, 'welfare_mode', 'margin_only');

    $payoutCarbon = $this->getNextPayoutDate($orgId);
    $payoutDateFormatted = $payoutCarbon->format('d/m/Y');

    return view('keptkayas.withdraw_form', compact(
        'isStaff',
        'members',
        'targetUserId',
        'account',
        'withdrawableAmount',
        'minReserve',
        'minWithdraw',
        'maxInactiveCycles',
        'hasRecentWasteSale',
        'payoutDateFormatted',
        'enableWelfare',
        'welfareMode'
    ));
}

    public function storeRequest(Request $request)
    {
        $userId = $request->target_user_id;
        $user = Auth::user();
        $orgId = $user->org_id_fk ?? 1;

        $account = KpBankAccount::where('user_id', $userId)->firstOrFail();

        // ดึงค่า Dynamic Settings สำหรับ Validate
        $minReserve = (float) KpSetting::getValue($orgId, 'min_reserve', 300);
        $minWithdraw = (float) KpSetting::getValue($orgId, 'min_withdraw', 20);
        $maxInactiveCycles = (int) KpSetting::getValue($orgId, 'max_inactive_cycles', 3);

        $maxWithdrawable = max(0, (float) $account->balance - $minReserve);

        $request->validate([
            'amount' => [
                'required',
                'numeric',
                'min:' . $minWithdraw,
                'max:' . $maxWithdrawable
            ],
            'is_proxy' => 'nullable',
            'proxy_name' => 'required_if:is_proxy,on|nullable|string|max:255',
            'proxy_id_card' => 'required_if:is_proxy,on|nullable|string|max:20',
            'proxy_relationship' => 'nullable|string|max:100',
        ], [
            'amount.max' => "ยอดเงินที่ขอถอนเกินจำนวนที่ถอนได้จริง (ต้องเหลือเงินติดบัญชีอย่างน้อย " . number_format($minReserve, 2) . " บาท)",
            'amount.min' => "ยอดถอนขั้นต่ำคือ " . number_format($minWithdraw, 2) . " บาท",
            'proxy_name.required_if' => 'กรุณาระบุชื่อ-นามสกุล ของผู้รับเงินแทน',
            'proxy_id_card.required_if' => 'กรุณาระบุเลขบัตรประชาชนของผู้รับเงินแทน',
        ]);

        // เช็กสิทธิ์ความต่อเนื่องในการขายขยะ
        $monthsAgo = now()->subMonths($maxInactiveCycles)->startOfMonth();
        $hasRecentWasteSale = KpPurchaseTransaction::where('user_id', $userId)
            ->where('transaction_date', '>=', $monthsAgo)
            ->exists();

        if (!$hasRecentWasteSale) {
            return back()->with('error', "ไม่สามารถถอนเงินได้ เนื่องจากไม่มีการนำขยะมาขายเกิน {$maxInactiveCycles} รอบบิล");
        }

        $amount = (float) $request->amount;
        $payoutDate = $this->getNextPayoutDate($orgId)->toDateString();

        return DB::transaction(function () use ($request, $userId, $amount, $payoutDate, $orgId) {
            $verificationCode = rand(100000, 999999);

            $withdraw = KpMoneyRequest::create([
                'org_id_fk'         => $orgId,
                'user_id'           => $userId,
                'amount'            => $amount,
                'verification_code' => $verificationCode,
                'status'            => 'pending',
                'hold_status'       => 'held', // อายัดยอดเงินไว้ชั่วคราว
                'is_proxy'          => $request->has('is_proxy') ? 1 : 0,
                'proxy_name'        => $request->proxy_name,
                'proxy_id_card'     => $request->proxy_id_card,
                'proxy_relationship'=> $request->proxy_relationship,
                'payout_date'       => $payoutDate,
                'admin_id'          => Auth::id()
            ]);
            return redirect()->route('keptkayas.withdraw.success', $withdraw->id);
        });
    }

    public function showSuccess($id)
    {
        $withdraw = KpMoneyRequest::findOrFail($id);
        return view('keptkayas.withdraw_success', compact('withdraw'));
    }

    public function printSlip($id)
{
    $withdraw = KpMoneyRequest::with('user')->findOrFail($id);
    return view('keptkayas.withdraws.print_slip', compact('withdraw'));
}
}