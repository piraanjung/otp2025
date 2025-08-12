<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class OrgSettings extends Model
{
    protected $table  = 'org_settings';


    public function provinces(){
        return $this->belongsTo(Province::class, 'org_province_id', 'id');
    }

    public function districts(){
        return $this->belongsTo(District::class, 'org_district_id', 'id');
    }

    public function tambons(){
        return $this->belongsTo(Tambon::class, 'org_tambon_id', 'id');
    }

    public static function getOrgInfos($connection){
        return DB::connection($connection)->table('org_settings as st')
        ->join('provinces as pv', 'pv.id', '=', 'st.org_province_id')
        ->join('tambons as tb', 'tb.id', '=', 'st.org_tambon_id')
        ->join('districts as dt', 'dt.id', '=', 'st.org_district_id')
        ->get(); 
    }
}
