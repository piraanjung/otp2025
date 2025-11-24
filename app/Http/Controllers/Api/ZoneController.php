<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admin\Zone;
use App\Models\User;

class ZoneController extends Controller
{
    public function index()
    {
        $zones = Zone::where('status', "0")->get();
        $zoneArr = [];
        if (count($zones) > 0) {
            foreach ($zones as $zone) {
                $z = ["value" => $zone->id, "name" => $zone->zone_name];
                array_push($zoneArr, $z);
            }
        }
        return response()->json($zoneArr);
    }

    public function getZoneAndSubzone()
    {
        $zones = Zone::with('subzone')->get();
        return response()->json($zones);
    }

    public function undertakenZoneAndSubzone($twman_id)
    {
        $twMan = User::where('id', $twman_id)
            ->with('undertaker_subzone', 'undertaker_subzone.subzone', 'undertaker_subzone.subzone.zone')
            ->get();
        $twMan[0]->undertaker_subzone[0]->subzone[0]->subzone_name = json_decode($twMan[0]->undertaker_subzone[0]->subzone[0]->subzone_name);
        $twMan[0]->undertaker_subzone[1]->subzone[0]->subzone_name = json_decode($twMan[0]->undertaker_subzone[1]->subzone[0]->subzone_name);
        return response()->json($twMan);
    }

    public function users_by_zone($zone_id)
    {
        //active
        $users = User::where('zone_id', $zone_id)->get(['id', 'firstname', 'lastname', 'phone', 'zone_id', 'address']);
        return response()->json($users);

    }

    public function getZone($zone_id)
    {
        $zones = Zone::where('id', $zone_id)
            ->get(['zone_name']);
        return response()->json($zones);
    }
}
