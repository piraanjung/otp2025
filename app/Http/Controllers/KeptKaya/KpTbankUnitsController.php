<?php

namespace App\Http\Controllers\KeptKaya;

use App\Http\Controllers\Controller;
use App\Models\Keptkaya\KpTbankUnits;
use Illuminate\Http\Request;

class KpTbankUnitsController extends Controller
{
    public function index()
    {
        // ดึงข้อมูลทั้งหมดจากตาราง kp_tbank_units
        $units = KpTbankUnits::all();
        return view('keptkaya.tbank.units.index', compact('units'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('keptkaya.tbank.units.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // ตรวจสอบความถูกต้องของข้อมูล (Validation) สำหรับแต่ละรายการใน Array
        // $request->validate([
        //     'unitname.*' => 'required|string|max:255|unique:kp_tbank_items_units,unitname',
        //     'unit_short_name.*' => 'nullable|string|max:50', // เพิ่ม validation สำหรับ unit_short_name
        //     'status' => 'required',
        // ]);

        // วนลูปสร้างแต่ละ Unit
        // $unitNames = $request->input('unitname');
        // $unitShortNames = $request->input('unit_short_name');

        foreach ($request->get('unitname') as $index => $unitName) {
            KpTbankUnits::create([
                'unitname' => $unitName['unitname'],
                'unit_short_name' => $unitName['unit_short_name'] ?? null, // ดึงค่า unit_short_name ตาม index
                'status' => 'active',
                'deleted' => '0',
            ]);
        }

        return redirect()->route('keptkaya.tbank.units.index')->with('success', 'Unit(s) created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(KpTbankUnits $unit)
    {
        return view('keptkaya.tbank.units.show', compact('unit'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(KpTbankUnits $unit)
    {
        return view('keptkaya.tbank.units.edit', compact('unit'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, KpTbankUnits $unit)
    {
        // ตรวจสอบความถูกต้องของข้อมูล (Validation)
        $request->validate([
            'unitname' => 'required|string|max:255|unique:kp_tbank_units,unitname,' . $unit->id,
            'unit_short_name' => 'nullable|string|max:50', // เพิ่ม validation สำหรับ unit_short_name
            'status' => 'required|boolean',
            'deleted' => 'required|boolean',
        ]);

        $unit->update($request->all());

        return redirect()->route('keptkaya.tbank.units.index')->with('success', 'Unit updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(KpTbankUnits $unit)
    {
        // ลบข้อมูล
        $unit->delete();

        return redirect()->route('keptkaya.tbank.units.index')->with('success', 'Unit deleted successfully.');
    }
}
