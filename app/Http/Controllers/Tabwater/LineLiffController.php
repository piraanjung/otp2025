<?php

namespace App\Http\Controllers\Tabwater;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LineLiffController extends Controller
{
    public function index(){
        return view('lineliff.index');
    }
}
