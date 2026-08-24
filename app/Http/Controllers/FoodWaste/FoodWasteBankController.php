<?php

namespace App\Http\Controllers\FoodWaste;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FoodWaste\FoodWasteUserPreference;
use App\Models\FoodWaste\FoodWasteAccount;
use App\Models\FoodWaste\FoodWasteLog;
use App\Models\FoodWaste\FoodWasteTransaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FoodWasteBankController extends Controller
{
    // 1. หน้ารวมสมาชิกและยอดแต้มคงเหลือ
    public function index()
    {
        $accounts = FoodWasteAccount::with('preference.user')
            ->orderBy('points_balance', 'desc')
            ->paginate(20);

        return view('foodwaste.admin.bank.index_points', compact('accounts'));
    }

    // 2. ฟังก์ชันแลกของรางวัล (หักแต้ม)
    public function redeem(Request $request)
    {
        $request->validate([
            'account_id' => 'required|exists:foodwaste_accounts,id',
            'points_used' => 'required|integer|min:1',
            'item_name' => 'required|string'
        ]);

        $account = FoodWasteAccount::findOrFail($request->account_id);

        if ($account->points_balance < $request->points_used) {
            return back()->with('error', 'แต้มสะสมไม่เพียงพอสำหรับแลกรายการนี้');
        }

        DB::transaction(function () use ($account, $request) {
            // หักแต้มออกจากบัญชี
            $account->decrement('points_balance', $request->points_used);

            // บันทึกธุรกรรม
            FoodWasteTransaction::create([
                'fw_pref_id_fk' => $account->fw_pref_id_fk,
                'type' => 'redeem',
                'points' => -$request->points_used, // ติดลบเพราะเป็นการใช้ไป
                'note' => "แลกของรางวัล: " . $request->item_name,
                'staff_id' => Auth::id()
            ]);
        });

        return back()->with('success', 'บันทึกการแลกของรางวัลเรียบร้อย!');
    }

    // 3. ฟังก์ชันขายปุ๋ยราคาสมาชิก (สร้างรายได้หมุนเวียน)
    public function sellCompost(Request $request)
    {
        $request->validate([
            'account_id' => 'required|exists:foodwaste_accounts,id',
            'amount_paid' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:1'
        ]);

        $account = FoodWasteAccount::findOrFail($request->account_id);

        DB::transaction(function () use ($account, $request) {
            // บันทึกธุรกรรมการขาย (เก็บเป็นยอดเงิน amount)
            FoodWasteTransaction::create([
                'fw_pref_id_fk' => $account->fw_pref_id_fk,
                'type' => 'buy_compost',
                'amount' => $request->amount_paid,
                'note' => "ซื้อปุ๋ยหมักชุมชน จำนวน " . $request->quantity . " ถุง",
                'staff_id' => Auth::id()
            ]);
        });

        return back()->with('success', 'บันทึกการขายปุ๋ยราคาสมาชิกสำเร็จ! เงินเข้าระบบหมุนเวียนแล้ว');
    }

    public function dashboard()
    {
        // 1. สรุปยอดรวมน้ำหนักขยะทั้งหมดที่ผ่านการ Verify แล้ว
        $totalWeight = FoodWasteLog::where('is_verified', true)->sum('verified_dry_weight');

        // 2. สรุปยอดเงินรายได้จากการขายปุ๋ย (Transaction Type: buy_compost)
        $totalRevenue = FoodWasteTransaction::where('transaction_type', 'buy_compost')->sum('amount');

        // 3. สรุปจำนวนพอยต์ที่แจกไปทั้งหมด (Transaction Type: earn_waste)
        $totalPointsIssued = FoodWasteTransaction::where('transaction_type', 'earn_waste')->sum('points');

        // 4. สรุปจำนวนของรางวัลที่แลกไป (Transaction Type: redeem)
        $totalRedeems = FoodWasteTransaction::where('transaction_type', 'redeem')->count();

        // 5. ดึงรายการธุรกรรมล่าสุด 10 รายการมาโชว์
        $recentTransactions = FoodWasteTransaction::with(['preference.user'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('foodwaste.admin.bank.dashboard', compact(
            'totalWeight', 'totalRevenue', 'totalPointsIssued', 'totalRedeems', 'recentTransactions'
        ));
    }
}
