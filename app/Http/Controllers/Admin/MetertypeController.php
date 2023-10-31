<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MeterType;
use Illuminate\Http\Request;

class MetertypeController extends Controller
{
    public function index()
    {
        $metertypes = MeterType::all();
        return view("admin.metertype.index", compact("metertypes"));
    }
    public function create()
    {
        return view("admin.metertype.create");
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            "meter_type_name"=> "required",
            "metersize"=> "required|numeric",
            "price_per_unit"=> "required|numeric",
        ],
        [
            "required" => "ใส่ข้อมูล",
            "numeric" => "ใส่ตัวเลข",
        ],);
        MeterType::create($validated);
        return redirect()->route("admin.metertype.index")->with("success","บันทึกข้อมูลเรียบร้อยแล้ว");
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
    public function edit(MeterType $metertype)
    {
        return view("admin.metertype.edit", compact("metertype"));
    }

    public function update(Request $request, MeterType $metertype)
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
        return redirect()->route("admin.metertype.index")->with("success","บันทึกการแก้ไขแล้ว");
    }
    public function destroy($id)
    {
        //
    }
    public function infos($id){
        return MeterType::find($id);
    }
}
