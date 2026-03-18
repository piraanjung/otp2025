<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Api\FunctionsController;
use App\Http\Controllers\SettingsController;
use App\Models\Admin\ManagesTenantConnection;
use App\Models\Subzone;
use App\Models\Admin\Zone;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Admin\Organization;
use Illuminate\Support\Facades\Auth;

class ZoneController extends Controller
{
    public function index()
    {
        $zones = Zone::all();
        $orgInfos = Organization::getOrgName(Auth::user()->org_id_fk);

        return view('admin.zone.index', compact('zones','orgInfos'));
    }

    public function create()
    {
        // $settingsCtrl = new SettingsController();
        // $tambonInfos = $settingsCtrl->getTambonInfos();
        return view('admin.zone.create',);
    }


    public function store(Request $request)
    {

        foreach($request->zone as $zone){
            Zone::create([
                "zone_name" => $zone['zonename'],
                "org_id_fk" => Auth::user()->org_id_fk,
                "tambon_id" => Auth::user()->tambon_code,
                "location" => $zone['zoneAddress'],
                "status" => 'active',
                "lat" => 0,
                'long' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }

        return redirect('admin/zone');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Zone $zone)
    {
         $zonename = $zone->zone_name;
        try{
            $zone->delete();
            $message = 'ลบ '.$zonename.' แล้ว'; $color = "success";
        }catch(\Exception $e){
            $message = 'ลบ '.$zonename.' ไม่ได้ ใช้งานอยู่'; $color = "danger";

        }
        return redirect()->back()->with(['message'=>$message, "color" => $color]);
    }

    public function getZones($tambon_id){
        $zones = Zone::where('tambon_id', $tambon_id)
                ->with('subzone')
                ->get(['id', 'tambon_id', 'zone_name','location']);
        return response()->json(['zones' => $zones]);
    }
}
