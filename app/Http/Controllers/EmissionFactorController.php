<?php

namespace App\Http\Controllers;

use App\Imports\EFImport;
use App\Models\EmissionFactor;
use EmptyIterator;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class EmissionFactorController extends Controller
{
    public function index()
    {
        $factors = EmissionFactor::latest()->get();
        return view('admin.ef.index', compact('factors'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'material_name' => 'required|string',
            'ef_value' => 'required|numeric',
            'source' => 'required',
            'example' => 'nullable'
        ]);
        EmissionFactor::create($data);
        return back()->with('success', 'เพิ่มข้อมูลเรียบร้อย');
    }

    public function update(Request $request, EmissionFactor $emissionFactor)
    {
        $data = $request->validate([
            'material_name' => 'required|string|max:255',
            'ef_value' => 'required|numeric',
            'source' => 'nullable|string|max:255',
            'example' => 'nullable'
        ]);

        $emissionFactor->update($data);

        return back()->with('success', 'อัปเดตข้อมูล EF เรียบร้อยแล้ว');
    }

    public function import(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,xls']);
        Excel::import(new EFImport, $request->file('file'));
        return back()->with('success', 'นำเข้าข้อมูลจาก Excel สำเร็จ!');
    }

    public function destroy(EmissionFactor $emissionFactor)
    {
        $emissionFactor->delete();
        return back()->with('success', 'ลบข้อมูลแล้ว');
    }
}
