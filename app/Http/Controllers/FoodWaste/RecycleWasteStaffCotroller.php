<?php

namespace App\Http\Controllers\KeptKaya;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RecycleWasteStaffCotroller extends Controller
{
    public function index()
    {
        return view('foodwaste.staffs.mobile.recycle.index');
    }
}
