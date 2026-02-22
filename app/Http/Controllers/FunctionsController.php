<?php

namespace App\Http\Controllers;


use App\Models\Admin\District;
use App\Models\Admin\ManagesTenantConnection;
use App\Models\Admin\Organization;
use App\Models\Admin\OrgSettings;
use App\Models\KeptKaya\KpPurchaseShop;
use App\Models\KeptKaya\KpUserWastePreference;
use App\Models\KeptKaya\WasteBin;
use App\Models\Admin\Tambon;
use App\Models\FoodWaste\FoodWasteBin;
use App\Models\KeptKaya\KpTbankItems;
use App\Models\KeptKaya\KpTbankItemsGroups;
use App\Models\KeptKaya\KpTbankItemsPriceAndPoint;
use App\Models\KeptKaya\KpTbankUnits;
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

    public function getOrgName($tambon_id){
        return (new Organization())->setConnection('envsogo_main')
                ->with(['user' =>function($q){
                    $q->select('fistname', 'lastname');
                }])
                ->where('org_tambon_id_fk', $tambon_id)->get(['id','org_type_name', 'org_name']);
       
    }

    

    public function getOrgInfos()
    {
        return  DB::connection('mysql')->table('organizations as st')
            ->where('st.id', '=', Auth::user()->settings_id_fk)
            ->join('provinces as pv', 'pv.id', '=', 'st.org_province_id_fk')
            ->join('tambons as tb', 'tb.id', '=', 'st.org_tambon_id_fk')
            ->join('districts as dt', 'dt.id', '=', 'st.org_district_id_fk')
            ->get();
    }
    public  function createInvoiceNumberString($id)
    {
        $meternumber_code = DB::connection(session('db_conn'))->table('organizations')
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
        $org  = Organization::getOrgName(Auth::user()->org_id_fk);

        $wasteBin = WasteBin::get('bin_code')->last();
        $bin_code = collect($wasteBin)->isEmpty() ? 0 : $wasteBin->bin_code;

        if($bin_code != 0){
            $bCode = explode('-', $bin_code)[1];
            $bin_code = (int)explode('B', $bCode)[1]+1;
        }else{
            $bin_code = 1;
        }
    
        return $org['org_code']."-B" .$this->createNumberString($bin_code);
    }

    public function foodwastBinCode(){
        $org = Organization::getOrgInfos(Auth::user()->org_id_fk);
        $wasteBin = FoodWasteBin::get('bin_code')->last();
        $bin_code = collect($wasteBin)->isEmpty() ? 0 : $wasteBin->bin_code;

        if($bin_code != 0){
            $bCode = explode('-', $bin_code)[1];
            $bin_code = (int)explode('FW', $bCode)[1]+1;
        }else{
            $bin_code = 1;
        }
    
        return $org['org_code']."-FW" .$this->createNumberString($bin_code);
    }

     public static function fullThaiMonth($m)
    {
        $month = [
            'มกราคม'  => '01', 'กุมภาพันธ์'  => '02', 'มีนาคม'   => '03',
            'เมษายน'  => '04', 'พฤษภาคม'  => '05', 'มิถุนายน'  => '06',
            'กรกฎาคม' => '07', 'สิงหาคม'   => '08', 'กันยายน'  => '09',
            'ตุลาคม'   => '10', 'พฤศจิกายน' => '11', 'ธันวาคม'  => '12',
        ];
        return array_search($m, $month);
    }

    public static function keptkaya_nav_infos(){
        $orgId = Auth::user()->org_id_fk;
        return  [
            'items_group_count'         => KpTbankItemsGroups::where('status', 'active')
                                            ->where('org_id_fk', $orgId)->count(),
            'units_count'               => KpTbankUnits::where('status', 'active')
                                            ->where('org_id_fk', $orgId)->count(),
            'items_count'               => KpTbankItems::where('status', 'active')
                                            ->where('org_id_fk', $orgId)->count(),
            'items_prices_count'        => KpTbankItemsPriceAndPoint::where('status', 'active')
                                            ->where('org_id_fk', $orgId)->count(),
            'shop_count'                => KpPurchaseShop::where('status', 'active')
                                            ->where('org_id_fk', $orgId)->count(),
            'kp_user_waste_preferences' => KpUserWastePreference::whereHas('user', function($q)use ($orgId){
                                                $q->where('org_id_fk', $orgId);
                                            })->count(),
        ];
    }
}
