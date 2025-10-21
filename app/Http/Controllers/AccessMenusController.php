<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\UsersController;
use App\Http\Controllers\Tabwater\ReportsController;
use App\Models\Admin\BudgetYear;
use App\Models\Admin\ManagesTenantConnection;
use App\Models\Admin\Organization;
use App\Models\Admin\Subzone;
use App\Models\Tabwater\TwInvoice;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

class AccessMenusController extends Controller
{
    public function dashboard(Request $request)
    {
        return Auth::user();
        Config::set('database.default', session('db_conn'));
        $apiUserCtrl = new  UsersController();
        $reportCtrl = new ReportsController();

        $subzones  = (new Subzone())->setConnection(session('db_conn'))
            ->where('status', 'active')->get(['id', 'subzone_name', 'zone_id'])
            ->sortBy('zone_id');
        $user_in_subzone = [];
        $user_in_subzone_label = collect($subzones)->pluck('subzone_name');
        $user_count = [];
        foreach ($subzones as $subzone) {
            $user_count[] = $apiUserCtrl->users_subzone_count($subzone->id);
        }
        $user_in_subzone_data = [
            'labels' => $user_in_subzone_label,
            'data' => $user_count,
        ];
        $data = $reportCtrl->water_used($request, 'dashboard');
        $water_used_total = collect($data['data'])->sum();

        $invoice_paid = (new TwInvoice())->setConnection(session('db_conn'))->where('status', 'paid')->get(['vat', 'totalpaid']);
        
        $paid_total = collect($invoice_paid)->sum('totalpaid');
        $vat =  collect($invoice_paid)->sum('vat');;
        $user_count_sum = collect($user_count)->sum();
        $subzone_count = collect($subzones)->count();
        
        $current_budgetyear = (new BudgetYear())->setConnection(session('db_conn'))
            ->where('status', 'active')->get('budgetyear_name')[0];
        
        
        $orgInfos = (new Organization())->setConnection(session('db_conn'))->getOrgName(Auth::user()->org_id_fk);

        return view('dashboard', compact(
            'data',
            'user_in_subzone_data',
            'water_used_total',
            'paid_total',
            'vat',
            'user_count_sum',
            'subzone_count',
            'current_budgetyear',
            'orgInfos'
        ));
    }

    public function accessmenu(Request $request)
    {
        $user = (new User())->setConnection(session('db_conn'))->find(Auth::id());
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';

        // ตรวจสอบคำที่บ่งชี้ถึงอุปกรณ์มือถือ
        $ismobile = preg_match(
            "/(android|avantgo|blackberry|bolt|boost|cello|hiptop|irengin|mobi|mini|mo(bil|si)|ntellect|palm|pda|phone|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|wap|windows ce|xda|xiino)/i",
            $userAgent
        );
        if ($ismobile) {
            return redirect()->route('staff_accessmenu');
        }
        $org = (new Organization())->setConnection('envsogo_main')->find(Auth::user()->org_id_fk);
        //session(['db_conn' => strtolower($org->org_database)]);
        $orgInfos = Organization::getOrgName(Auth::user()->org_id_fk);
        return view('accessmenu', compact('orgInfos', 'user'));
    }


    public function staff_accessmenu()
    {
       // return session('db_conn');
     $connectionName = session('db_conn'); 

    // 1. สลับ Connection หลัก (ต้องทำซ้ำในทุก Controller method ที่ใช้ Tenant DB)
    if (Config::has("database.connections.{$connectionName}")) {
        Config::set('database.default', $connectionName);
    } else {
        abort(500, "Database connection for tenant is missing.");
    }

    // 2. ตรวจสอบสิทธิ์ด้วย Auth::user()->can() หรือ Policy (ใช้ Spatie)
    // Note: ต้องมั่นใจว่า Auth::user() ถูกโหลดใหม่จาก Tenant DB
    $userId = Auth::id();
    $newUserInstance = User::find($userId);
    Auth::setUser($newUserInstance);
    $user = Auth::user(); 
    
    // 3. ทำการตรวจสอบสิทธิ์ (Auth::user() ตอนนี้ใช้ Tenant DB แล้ว)
    if (!$user->hasRole(['Super Admin', 'Tabwater Staff'])) {
        // หากผู้ใช้ไม่มีบทบาทที่กำหนดใน Tenant DB
        abort(403, 'Unauthorized action.');
    }

    // 3. Logic การทำงาน (โค้ดเดิมของคุณ)
    $orgInfos = Organization::where('id', $user->org_id_fk)->get();

        return view('staff_accessmenu', compact('orgInfos', 'user'));
    }
}
