<?php

namespace App\Http\Controllers;


use App\Models\Admin\District;
use App\Models\KeptKaya\WasteBin;
use App\Models\Admin\Tambon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class FunctionsController extends Controller
{
    public function getDistricts($province_id)
    {
        return District::where('province_id', $province_id)->get(['id', 'district_name', 'province_id']);
    }

    public function getTambons($district_id)
    {
        return Tambon::where('district_id', $district_id)->get(['id', 'tambon_name']);
    }

    public function getOrgInfos()
    {
        return  DB::connection('mysql')->table('organizations as st')
            ->where('st.id', '=', Auth::user()->org_id_fk)
            ->join('provinces as pv', 'pv.id', '=', 'st.org_province_id_fk')
            ->join('tambons as tb', 'tb.id', '=', 'st.org_tambon_id_fk')
            ->join('districts as dt', 'dt.id', '=', 'st.org_district_id_fk')
            ->get();
    }

    public  function createInvoiceNumberString($id)
    {
        $meternumber_code = DB::connection('mysql')->table('organizations')
            ->where('id', Auth::user()->org_id_fk)->get('org_code');

        return $meternumber_code[0]->org_code. $this->createNumberString($id);
    }
    public  function createNumberString($id)
    {
        $invString = '';
        if ($id < 10) {
            $invString = '000' . $id;
        } else if ($id >= 10 && $id < 100) {
            $invString = '00' . $id;
        } else if ($id >= 100 && $id < 1000) {
            $invString = '0' . $id;
        } else {
            $invString = $id;
        }
        return $invString;
    }

    public function engDateToThaiDateFormat($date)
    {
        $dateExp = explode("-", $date);
        $cEyear = $dateExp[0] + 543;
        return $dateExp[2] . "/" . $dateExp[1] . "/" . $cEyear;
    }

    public function wastBinCode(){
      
        $wasteBin = WasteBin::get('bin_code')->last();
        $int = (int)explode('KP-B',$wasteBin->bin_code)[1]+1;
        return "KP-B" .$this->createNumberString($int);
    }
}
