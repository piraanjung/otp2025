<?php

namespace App\Http\Controllers\Tabwater;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class TransferOldDataToNewDBController extends Controller
{
    public function index(){

       $datas =  DB::table('user_meter_infos_old as umf')
       ->join('invoice_old as iv','iv.user_id','=', 'umf.user_id' )
        // ->where('umf.status', '=', 'permanent deleted')
        // ->where('umf.deleted', '=', 1)
        ->select('umf.user_id', 'iv.id', 'iv.receipt_id', 'iv.status')
        ->get();

        return $datas->filter(function($d){
            return $d->receipt_id == 0 && $d->status == 'paid';
        })->groupBy('user_id');
    }
}
