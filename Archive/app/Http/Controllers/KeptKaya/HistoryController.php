<?php

namespace App\Http\Controllers\KeptKaya;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\KeptKaya\KpPurchaseTransaction;
use App\Models\KeptKaya\KpUserWastePreference;

class HistoryController extends Controller
{
    public function index($userId)
    {
        // 1. หา UserWastePreference ID ก่อน
        $userPref = KpUserWastePreference::where('user_id', $userId)->firstOrFail();

        // 2. ดึงประวัติการขายขยะ (Recycle Bank)
        $histories = KpPurchaseTransaction::where('kp_user_w_pref_id_fk', $userPref->id)
            ->orderBy('transaction_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10); // แบ่งหน้าละ 10 รายการ

        return view('keptkayas.history', compact('histories', 'userId'));
    }
}
