<?php

namespace App\Http\Controllers\keptkaya;

use App\Http\Controllers\Controller;
use App\Models\Admin\BudgetYear;
use App\Models\Tabwater\TwMeters;
use App\Models\Tabwater\TwUsersInfo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class DashboardController extends Controller
{
    public function index(Request $request, $recycle_type){
    $request->session()->put('keptkaya_type', $recycle_type);
    
    // ... logic เช็ค BudgetYear ...

    // [ตัวอย่างข้อมูลจำลอง] - คุณต้องเขียน Query จริงแทนที่ตรงนี้
    $data = [
        'total_households' => 2500, // ครัวเรือนทั้งหมด
        'paid_households' => 1800,  // จ่ายแล้ว
        'expected_revenue' => 1250000, // ยอดเงินที่ควรได้ (บาท)
        'collected_revenue' => 900000, // เก็บได้จริง (บาท)
        'outstanding_revenue' => 350000, // ค้างชำระ (บาท)
        'progress_percent' => (900000 / 1250000) * 100, // % ความสำเร็จ
    ];

    return view('keptkayas.dashboard', compact('data', 'recycle_type'));
}
}
