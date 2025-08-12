<?php

namespace App\Http\Controllers\Tabwater;

use App\Http\Controllers\Controller;

use App\Models\UserMerterInfo;
use Illuminate\Http\Request;
use App\UserMeterInfos;
use App\Models\Subzone;

class UserMeterInfosController extends Controller
{
    public function find_subzone($zone_id, $subzone_id = 0){
        $resVal =0;
        if($zone_id > 0){
            $subzones = Subzone::where('zone_id', $zone_id)->get('id');
            if(collect($subzones)->isNotEmpty()){
                foreach($subzones as $subzone){
                    $undertake_subzone_id = UserMerterInfo::where('undertake_subzone_id', $subzone->id)
                                            ->get()->first();

                    if(collect($undertake_subzone_id)->isNotEmpty()){
                        $resVal = 1;
                        break;
                    }

                }
            }
        }else{
            $undertake_subzone_id = UserMerterInfo::where('undertake_subzone_id', $subzone_id)->get()->first();
            if(collect($undertake_subzone_id)->isNotEmpty()){
                $resVal = 1;
            }
        }

        return $resVal;
    }
}
