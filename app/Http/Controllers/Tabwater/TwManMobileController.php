<?php

namespace App\Http\Controllers\Tabwater;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TwManMobileController extends Controller
{
    public function index(){
        return view('twmanmobile.index');
    }

    public function main(){
        return view('twmanmobile.main');
    }

    public function edit_members_subzone_selected(){
         return view('twmanmobile.edit_members_subzone_selected');
    }
}
