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
        ManagesTenantConnection::configConnection(session('db_conn'));

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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
            FunctionsController::reset_auto_increment_when_deleted('zones');
            $message = 'ลบ '.$zonename.' แล้ว'; $color = "success";
        }catch(\Exception $e){
            $message = 'ลบ '.$zonename.' ไม่ได้ ใช้งานอยู่'; $color = "danger";

        }
        return redirect()->back()->with(['message'=>$message, "color" => $color]);
    }
}
