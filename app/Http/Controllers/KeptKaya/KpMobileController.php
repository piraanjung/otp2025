<?php

namespace App\Http\Controllers\KeptKaya;

use App\Http\Controllers\Controller;
use App\Models\KeptKaya\Machine;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// ระบบตู้รับซื้อขยะ
class KpMobileController extends Controller
{
    public function create(Request $request ){       
         $machine= Machine::where('current_user_active_id', Auth::guard(session('guard'))->id())
            ->get()->first();
        return view('keptkayas.kp_mobile.create', compact('machine'));
    }

    
}
