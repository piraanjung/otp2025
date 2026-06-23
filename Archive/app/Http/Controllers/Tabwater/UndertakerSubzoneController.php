<?php

namespace App\Http\Controllers\Tabwater;

use App\Http\Controllers\Controller;
use App\Models\UndertakerSubzone;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UndertakerSubzoneController extends Controller
{
    public function index()
    {
        $undertakerSubzone = UndertakerSubzone::with('twman_info', 'subzone', 'subzone.zone')
            ->get();
        $undertakerSubzones = collect($undertakerSubzone)->groupBy('twman_id')->values();
        return view('undertaker_subzone.index', compact('undertakerSubzones'));
    }

    public function create()
    {
        $undertakerSubzone = DB::table('undertaker_subzone as us')
            ->select('us.subzone_id')
            ->orderBy('us.subzone_id')
            ->get();
        $undertakerSubzoneArray = collect([]);
        foreach ($undertakerSubzone as $uz) {
            $undertakerSubzoneArray->push($uz->subzone_id);
        }
        $subzone = DB::table('subzone as sz')
            ->select('sz.id as subzone_id', )
            ->orderBy('sz.zone_id')
            ->get();

        $subzoneArr = collect([]);
        foreach ($subzone as $uz) {
            $subzoneArr->push($uz->subzone_id);
        }
        $remain_subzone = collect($subzoneArr)->diff($undertakerSubzoneArray)->values();

        $subzoneCollection = collect([]);
        foreach ($remain_subzone as $remain) {
            $sz = DB::table('subzone as sz')
                ->join('zone as z', 'z.id', 'sz.zone_id')
                ->where('sz.id', '=', $remain)
                ->select('sz.id as subzone_id', 'sz.subzone_name', 'z.zone_name', 'z.id as zone_id')
                ->orderBy('z.id')
                ->get();
            $subzoneCollection->push($sz);
        }
        $subzone = collect($subzoneCollection)->flatten()->sortBy('zone_id');

        $tw_mans = User::where('user_cat_id', 4)
            ->where('status', '=', 'active')
            ->with('user_profile', 'undertaker_subzone',
                'undertaker_subzone.subzone', 'undertaker_subzone.subzone.zone')
            ->get();

        return view('undertaker_subzone.create', compact('subzone', 'tw_mans'));
    }

    public function store(REQUEST $request)
    {
        date_default_timezone_set('Asia/Bangkok');

        foreach ($request->get('on') as $key => $val) {
            $subzone = explode('-', $key)[1];
            $undertakerSubzone = new UndertakerSubzone();
            $undertakerSubzone->twman_id = $request->get('twman_id');
            $undertakerSubzone->subzone_id = $subzone;
            $undertakerSubzone->created_at = date('Y-m-d H:i:s');
            $undertakerSubzone->updated_at = date('Y-m-d H:i:s');
            $undertakerSubzone->save();
        }

        return redirect('undertaker_subzone')->with(['success' => 'ทำการบันทึกข้อมูลเรียบร้อยแล้ว']);
    }

    public function edit($id)
    {
        $undertakerSubzone = DB::table('undertaker_subzone as us')
            ->join('subzones as sz', 'sz.id', 'us.subzone_id')
            ->join('zones as z', 'z.id', 'sz.zone_id')
            ->select('us.subzone_id', 'sz.subzone_name', 'z.zone_name', 'z.id as zone_id')
            ->orderBy('z.id')
            ->where('twman_id', '=', $id)
            ->get();
        $undertakerSubzone_subzone_id = collect($undertakerSubzone)->pluck('subzone_id')->toArray();
        $subzones = DB::table('subzones as sz')
            ->join('zones as z', 'z.id', 'sz.zone_id')
            ->select('sz.id as subzone_id', 'sz.subzone_name', 'z.zone_name', 'z.id as zone_id')
            ->orderBy('z.id')
            ->get();

            $zone = [];
            foreach($subzones as $subzone){
                if(!in_array( $subzone->subzone_id, $undertakerSubzone_subzone_id)){
                    array_push($zone, $subzone);
                }
            }

        $tw_mans = User::where('role_id', 5)
            ->where('id', $id)
            ->with( 'undertaker_subzone',
                'undertaker_subzone.subzone', 'undertaker_subzone.subzone.zone')
            ->get();

        return view('undertaker_subzone.edit', compact('zone', 'tw_mans'));
    }

    public function update(REQUEST $request, $id)
    {
        // date_default_timezone_set('Asia/Bangkok');

        // $tabwaterman_per_areas = TabWaterManPerArea::find($id);
        // $tabwaterman_per_areas->zone_name = $request->get('zone_name');
        // $tabwaterman_per_areas->location = $request->get('location');
        // $tabwaterman_per_areas->updated_at = date('Y-m-d H:i:s');
        // $tabwaterman_per_areas->save();
        // return redirect('tabwaterman_per_areas')->with(['massage' => 'ทำการบันทึกข้อมูลเรียบร้อยแล้ว']);
    }

    public function delete($id)
    {
        $undertakerSubzone = UndertakerSubzone::where('id', $id);
        $undertakerSubzone->delete();
        return redirect('undertaker_subzone')->with(['success' => 'ทำการลบข้อมูลเรียบร้อยแล้ว']);

    }
    // public function index(){
    //     $tabwaterman_per_areas = UndertakerSubzone::all();

    //     return view('undertaker_subzone.index', compact('tabwaterman_per_areas'));
    // }

    // public function create(){
    //     $tabwaterman_per_areas = new UndertakerSubzone;

    //     return view('undertaker_subzone.create', compact('tabwaterman_per_areas'));
    // }

    // public function store(REQUEST $request){
    //     $storeTabwaterman_per_areas = new TabwaterManPerArea;
    //     $storeTabwaterman_per_areas->zone_name= $request->get('zone_name');
    //     $storeTabwaterman_per_areas->location = $request->get('location');
    //     $storeTabwaterman_per_areas->created_at = date('Y-m-d H:i:s');
    //     $storeTabwaterman_per_areas->updated_at = date('Y-m-d H:i:s');
    //     $storeTabwaterman_per_areas->save();

    //     return redirect('tabwaterman_per_areas')->with(['massage' => 'ทำการบันทึกข้อมูลเรียบร้อยแล้ว']);
    // }

    // public function update($id){
    //     $tabwaterman_per_areas = TabWaterManPerArea::where("tabwaterman_id", $id)->first();
    //     // dd($tabwaterman_per_areas);
    //     return view('tabwaterman_per_areas.update', compact('tabwaterman_per_areas'));
    // }

    // public function edit(REQUEST $request , $id){
    //     $tabwaterman_per_areas = TabWaterManPerArea::find($id);
    //     $tabwaterman_per_areas->zone_name = $request->get('zone_name');
    //     $tabwaterman_per_areas->location = $request->get('location');
    //     $tabwaterman_per_areas->updated_at = date('Y-m-d H:i:s');
    //     $tabwaterman_per_areas->save();
    //     return redirect('tabwaterman_per_areas')->with(['massage' => 'ทำการบันทึกข้อมูลเรียบร้อยแล้ว']);
    // }

    // public function delete($id){
    //     $tabwaterman_per_areas = TabWaterManPerArea::find($id);
    //     $tabwaterman_per_areas->delete();
    //     return redirect('tabwaterman_per_areas')->with(['massage' => 'ทำการลบข้อมูลเรียบร้อยแล้ว']);

    // }
}
