<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LineLiffController extends Controller
{
    public function index(){
        return view('lineliff.index');
    }
}
