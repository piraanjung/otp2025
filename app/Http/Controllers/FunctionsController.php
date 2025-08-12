<?php

namespace App\Http\Controllers;


use App\Models\District;
use App\Models\KeptKaya\WasteBin;
use App\Models\Tambon;
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
        return  DB::connection('mysql')->table('org_settings as st')
            ->where('st.id', '=', Auth::user()->settings_id_fk)
            ->join('provinces as pv', 'pv.id', '=', 'st.org_province_id')
            ->join('tambons as tb', 'tb.id', '=', 'st.org_tambon_id')
            ->join('districts as dt', 'dt.id', '=', 'st.org_district_id')
            ->get();
    }

    public  function createInvoiceNumberString($id)
    {
        $meternumber_code = DB::connection('mysql')->table('settings')
            ->where('id', Auth::user()->settings_id_fk)->get('meter_code_format');

        return $meternumber_code[0]->meter_code_format . "TM" . $this->createNumberString($id);
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
