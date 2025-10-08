<?php

namespace App\Http\Controllers\FoodWaste;

use App\Http\Controllers\Controller;
use App\Models\Admin\BudgetYear;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request, ){
        if (collect(BudgetYear::where('status', 'active')->first())->isEmpty()) {
            session(['hiddenMenu' => true]);
        }

        return view('foodwaste.dashboard');
    }
}
