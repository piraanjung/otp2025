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
        //  User::where('id',Auth::user()->id)->with('roles.permissions')->get();
        // return Auth::user()->getRoleNames(); // ควรคืนค่าเป็น true
        $request->session()->put('keptkaya_type', $recycle_type);
        if (collect(BudgetYear::where('status', 'active')->first())->isEmpty()) {
            session(['hiddenMenu' => true]);
        }

        return view('keptkayas.dashboard');
    }
}
