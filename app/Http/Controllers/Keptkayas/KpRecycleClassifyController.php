<?php

namespace App\Http\Controllers\Keptkayas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KpRecycleClassifyController extends Controller
{
    public function index(){
        $user = Auth::guard('web_hs1')->user();
        return view('keptkayas.recycle_classify.index', compact('user'));
    }
}
