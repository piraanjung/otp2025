<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\UsersController;
use App\Http\Controllers\Tabwater\ReportsController;
use App\Models\Admin\BudgetYear;
use App\Models\Admin\Organization;
use App\Models\Admin\Zone;
use App\Models\Tabwater\TwInvoice;
use App\Models\Tabwater\TwNotifies;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccessMenusController extends Controller
{
    public function accessmenu(Request $request)
    {
        
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';

        // ตรวจสอบคำที่บ่งชี้ถึงอุปกรณ์มือถือ
        $ismobile = preg_match(
            "/(android|avantgo|blackberry|bolt|boost|cello|hiptop|irengin|mobi|mini|mo(bil|si)|ntellect|palm|pda|phone|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|wap|windows ce|xda|xiino)/i",
            $userAgent
        );
        if ($ismobile) {
            return redirect()->route('staff_accessmenu');
        }

        $orgInfos = Organization::getOrgName(Auth::user()->org_id_fk);
        return view('accessmenu', compact('orgInfos'));
    }

     public function dashboard(Request $request)
    {
        $apiUserCtrl = new  UsersController();
        $reportCtrl = new ReportsController();

        $subzones  = Zone::getOrgSubzone('array');
        $user_in_subzone = [];
        $user_in_subzone_label = collect($subzones)->pluck('subzone_name');
        $user_count = [];
        foreach ($subzones as $subzone) {
            $user_count[] = $apiUserCtrl->users_subzone_count($subzone['id']);
        }

        $user_in_subzone_data = [
            'labels' => $user_in_subzone_label,
            'data' => $user_count,
        ];
        $data = $reportCtrl->water_used($request, 'dashboard');
        $water_used_total = collect($data['data'])->sum();

        $invoice_paid = TwInvoice::where('status', 'paid')->get(['vat', 'totalpaid']);
        
        $paid_total = collect($invoice_paid)->sum('totalpaid');
        $vat =  collect($invoice_paid)->sum('vat');;
        $user_count_sum = collect($user_count)->sum();
        $subzone_count = collect($subzones)->count();
        
        $current_budgetyear = BudgetYear::where('status', 'active')->get('budgetyear_name')[0];
        
        
        $orgInfos = Organization::getOrgName(Auth::user()->org_id_fk);

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
        ));
    }


    public function staff_accessmenu()
    {
        $orgInfos = Organization::find(Auth::user()->org_id_fk);
        $notifies_pending  = TwNotifies::where('status', 'pending')->get();
        $notifies_pending_count  = TwNotifies::where('status', 'pending')->count();
        return view('staff_accessmenu', compact('orgInfos', 'notifies_pending', 'notifies_pending_count'));
    }
}
