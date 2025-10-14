<?php

namespace App\Http\Controllers\KeptKaya;

use App\Http\Controllers\Controller;
use App\Models\KeptKaya\Machine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KpMobileController extends Controller
{
    public function create(Request $request ){
        $machine= Machine::where('current_user_active_id', Auth::id())->get()->first();
        return view('keptkayas.kp_mobile.create', compact('machine'));
    }

    
}
