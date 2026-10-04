<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    public function index()
    {
        // เดิมเป็นหน้า placeholder ("Admin Content") ให้ไปที่ Dashboard แทน
        return redirect()->route('admin.dashboard');
    }
}
