<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Api\FunctionsController;
use App\Http\Controllers\Controller;
use App\Models\Tabwater\TwMeterType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MetertypeController extends Controller
{
    public function index()
    {
        $metertypes = TwMeterType::where('org_id_fk', Auth::user()->org_id_fk)->get();
        return view("admin.metertype.index", compact("metertypes"));
    }
    public function create()
    {
        return view("admin.metertype.create");
    }


    public function store(Request $request)
    {
        $request->merge(['org_id_fk' => Auth::user()->org_id_fk]);
        $validated = $request->validate([
            "meter_type_name"=> "required",
            "metersize"=> "required|numeric",
            "org_id_fk" => 'required'
        ],
        [
            "required" => "ใส่ข้อมูล",
            "numeric" => "ใส่ตัวเลข",
        ],);

        TwMeterType::create($validated);
        return redirect()->route("admin.metertype.index")->with("message","บันทึกข้อมูลเรียบร้อยแล้ว");
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
    public function edit(TwMeterType $metertype)
    {
        
        return view("admin.metertype.edit", compact("metertype"));
    }

    public function update(Request $request, TwMeterType $metertype)
    {
        $validated = $request->validate([
            "meter_type_name"=> "required",
            "metersize"=> "required|numeric",
            "price_per_unit"=> "required|numeric",
        ],
        [
            "required" => "ใส่ข้อมูล",
            "numeric" => "ใส่ตัวเลข",
        ]);

        $metertype->update($validated);
        return redirect()->route("admin.metertype.index")->with("message","บันทึกการแก้ไขแล้ว");
    }
    public function destroy(TwMeterType $metertype)
    {
        TwMeterType::destroy($metertype);
        return redirect()->route("admin.metertype.index")->with("message","บันทึกการแก้ไขแล้ว");
    }
    public function infos($id){
        return TwMeterType::find($id);
    }
}
