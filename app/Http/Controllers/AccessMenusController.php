<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\UsersController;
use App\Http\Controllers\Tabwater\ReportsController;
use App\Models\Admin\BudgetYear;
use App\Models\Admin\Organization;
use App\Models\Admin\Zone;
use App\Models\Tabwater\TwInvoice;
use App\Models\Tabwater\TwNotifies;
use Carbon\Carbon;
// use App\Models\User; // ไม่ได้ใช้ เอาออกได้
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class AccessMenusController extends Controller
{
    public function accessmenu(Request $request)
    {
        $user = Auth::user();
        // 1. เช็คว่าเป็น Staff หรือไม่? (แก้ 'role' และ 'staff' ให้ตรงกับ DB ของคุณ)
        // เช่น $user->type == 'employee' หรือ $user->is_staff
         
        $isStaff = $user->hasAnyRole(['Recycle Bank Staff', 'Tabwater Staff']);

        // 2. เช็ค Session ก่อนเลย ว่าเคยถูกจำว่าเป็น mobile แล้วหรือยัง?
        if (Session::get('is_mobile') && $isStaff) {
             return redirect()->route('staff_accessmenu');
        }
        return 'xx';

        // 3. ถ้ายังไม่มีใน Session ให้เช็คจาก User Agent
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $isMobileDevice = preg_match(
            "/(android|avantgo|blackberry|bolt|boost|cello|hiptop|irengin|mobi|mini|mo(bil|si)|ntellect|palm|pda|phone|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|wap|windows ce|xda|xiino)/i",
            $userAgent
        );
        if ($isStaff && $isMobileDevice) {
            // *** จำค่าลง Session ไว้เลย ***
            Session::put('is_mobile', true);
            
            return redirect()->route('staff_accessmenu');
        }

        // --- Logic เดิมสำหรับ Desktop ---
        $orgInfos = Organization::getOrgName($user->org_id_fk);
        return view('accessmenu', compact('orgInfos'));
    }
public function dashboard(Request $request)
{
    // --- 1. เตรียม Object และ Controller ที่ต้องใช้ ---
    $apiUserCtrl = new UsersController();
    $reportCtrl = new ReportsController();
    
    // ดึง org_id จาก User ที่ Login
    $org_id = Auth::user()->org_id_fk;

    // --- 2. ข้อมูลกราฟจำนวนสมาชิกแยกตาม Subzone ---
    $subzones = Zone::getOrgSubzone('array');
    $user_in_subzone_label = collect($subzones)->pluck('subzone_name');
    
    $user_count = [];
    foreach ($subzones as $subzone) {
        $user_count[] = $apiUserCtrl->users_subzone_count($subzone['id']);
    }

    $user_in_subzone_data = [
        'labels' => $user_in_subzone_label,
        'data' => $user_count,
    ];
    
    $user_count_sum = collect($user_count)->sum();
    $subzone_count = count($subzones); 

    // --- 3. ข้อมูลกราฟปริมาณการใช้น้ำ ---
    $data = $reportCtrl->water_used($request, 'dashboard');
    $water_used_total = isset($data['data']) ? collect($data['data'])->sum() : 0;

    // --- 4. ข้อมูลยอดเงินรวม ---
    $paid_total = TwInvoice::where('status', 'paid')->sum('totalpaid');
    $vat = TwInvoice::where('status', 'paid')->sum('vat');

    // --- 5. ข้อมูลปีงบประมาณ ---
    $budget_obj = BudgetYear::where('status', 'active')->first();
    $current_budgetyear = $budget_obj ?: (object)['budgetyear_name' => '-'];

    // --- 6. ข้อมูลองค์กร ---
    $orgInfos = Organization::getOrgName($org_id);

    // --- 7. ข้อมูลกราฟสถานะการชำระเงิน แยกตาม Zone (แก้ไขใหม่) ---
    $startOfMonth = Carbon::now()->startOfMonth();
    $endOfMonth   = Carbon::now()->endOfMonth();

    $zones = Zone::where('org_id_fk', $org_id)->get();
    
    // เตรียมตัวแปร Array สำหรับใส่กราฟ
    $zone_labels = [];
    $zone_paid_data = [];
    $zone_unpaid_data = [];

    foreach ($zones as $zone) {
        $queryBase = DB::table('tw_invoice')
            ->join('tw_meter_infos', 'tw_invoice.meter_id_fk', '=', 'tw_meter_infos.meter_id')
            ->where('tw_meter_infos.undertake_zone_id', $zone->id)
            ->whereBetween('tw_invoice.created_at', [$startOfMonth, $endOfMonth]);

        $paid_count = (clone $queryBase)->where('tw_invoice.status', 'paid')->count();
        $unpaid_count = (clone $queryBase)->where('tw_invoice.status', '!=', 'paid')->count();
        $total_zone = $paid_count + $unpaid_count;

        // เก็บข้อมูลทุกโซน (หรือจะกรองเฉพาะที่มีข้อมูลก็ได้ if $total_zone > 0)
        // แต่กราฟแท่งแสดงค่า 0 ได้ ไม่ error ครับ
        if ($total_zone >= 0) { 
            $zone_labels[] = $zone->zone_name;
            $zone_paid_data[] = $paid_count;
            $zone_unpaid_data[] = $unpaid_count;
        }
    }

    $zone_chart_data = [
        'labels' => $zone_labels,
        'paid'   => $zone_paid_data,
        'unpaid' => $zone_unpaid_data
    ];

    // --- 8. ส่งค่าไปยัง View ---
    return view('dashboard', compact(
        'data',
        'user_in_subzone_data',
        'water_used_total',
        'paid_total',
        'vat',
        'user_count_sum',
        'subzone_count',
        'current_budgetyear',
        'orgInfos',
        'zone_chart_data' // <--- เปลี่ยนตัวแปรที่ส่งไป
    ));
}

    public function staff_accessmenu()
    {
        // เพิ่มความปลอดภัย: เช็คอีกทีว่าเป็น Staff จริงไหม ถ้าไม่ใช่ให้ดีดออก
        if (!Auth::user()->hasAnyRole(['Recycle Bank Staff', 'Tabwater Staff'])) { // แก้ตาม DB ของคุณ
            return redirect()->route('accessmenu'); // หรือ route อื่น
        }

        $orgInfos = Organization::find(Auth::user()->org_id_fk);
        
        $notifies_pending = TwNotifies::where('status', 'pending')->get();
        $notifies_pending_count = TwNotifies::where('status', 'pending')->count();
        
        return view('staff_accessmenu', compact('orgInfos', 'notifies_pending', 'notifies_pending_count'));
    }
}