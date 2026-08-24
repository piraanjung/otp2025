<?php

namespace App\Http\Controllers\keptkaya;

use App\Http\Controllers\Controller;
use App\Models\KeptKaya\KpPurchaseTransaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class RecycleBankController extends Controller
{
    /**
     * แสดงรายชื่อสมาชิกธนาคารขยะ
     */
    public function index(Request $request)
    {
        $members = User::with([
            'user_zone',
            'user_subzone',
            'recycleBankAccount'
        ])
        ->whereHas('recycleBankAccount') // ดึงเฉพาะผู้ที่มีบัญชีธนาคารขยะ
        ->latest()
        ->paginate(10);

        return view('keptkayas.recycle_bank.index', compact('members'));
    }

    /**
     * แสดงประวัติการขายขยะ แยกตามปีงบประมาณ
     */
    public function history(Request $request, $userId)
    {
        $user = User::with(['user_zone', 'user_subzone', 'recycleBankAccount'])
            ->findOrFail($userId);

        // ปีงบประมาณปัจจุบัน (ถ้าเดือน >= 10 ถือเป็นปีงบประมาณถัดไป)
        $currentMonth = now()->month;
        $currentFiscalYear = $currentMonth >= 10 ? now()->year + 1 : now()->year;

        // รับค่าปีงบประมาณจาก Query String (ถ้าไม่มีให้ใช้ปีปัจจุบัน)
        $selectedFiscalYear = (int) $request->get('fiscal_year', $currentFiscalYear);

        // คำนวณ ช่วงวันที่ของปีงบประมาณที่เลือก (1 ต.ค. ปีก่อน - 30 ก.ย. ปีที่เลือก)
        $startDate = Carbon::create($selectedFiscalYear - 1, 10, 1)->startOfDay();
        $endDate   = Carbon::create($selectedFiscalYear, 9, 30)->endOfDay();

        // ดึงรายการบิลประวัติการขายขยะ
        $transactions = KpPurchaseTransaction::with(['details.item', 'details.unit', 'recorder'])
            ->whereHas('userWastePreference', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->orderBy('transaction_date', 'desc')
            ->get();

        // รายการปีงบประมาณย้อนหลังให้เลือกใน Dropdown (เช่น ย้อนหลัง 5 ปี)
        $fiscalYears = range($currentFiscalYear, $currentFiscalYear - 4);

        return view('keptkayas.recycle_bank.history', compact('user', 'transactions', 'selectedFiscalYear', 'fiscalYears'));
    }
}
