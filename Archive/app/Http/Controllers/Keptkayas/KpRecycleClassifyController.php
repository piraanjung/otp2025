<?php

namespace App\Http\Controllers\Keptkayas;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KpRecycleClassifyController extends Controller
{
    public function index(){
        $user = User::find(Auth::id());
        return view('keptkayas.recycle_classify.index', compact('user'));
    }
}
