<?php

namespace App\Http\Controllers\keptkaya;

use App\Http\Controllers\Controller;
use App\Models\Admin\BudgetYear;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request, $recycle_type){
        $request->session()->put('keptkaya_type', $recycle_type);
        if (collect(BudgetYear::where('status', 'active')->first())->isEmpty()) {
            session(['hiddenMenu' => true]);
        }
        return view('keptkaya.dashboard');
    }
}
