<?php

namespace App\Http\Controllers;

use App\Exports\EmissionFactorExport;
use App\Imports\EFImport;
use App\Imports\EmissionFactorImport;
use App\Models\EmissionFactor;
use App\Models\KeptKaya\KpTbankItems;
use EmptyIterator;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class EmissionFactorController extends Controller
{
    public function index()
    {
        // 1. ดึงข้อมูล EF ทั้งหมด เรียงตามลำดับล่าสุด
        $emissionFactors = EmissionFactor::latest()->get();

        // 2. คำนวณค่าที่จะนำไปโชว์ใน Dashboard Cards (เพื่อแก้ปัญหา Undefined variable)
        $totalCount = $emissionFactors->count();
        $avgEF = $emissionFactors->avg('ef_value') ?? 0;

        // 3. ส่งตัวแปรไปยังหน้า admin.ef.index
        return view('admin.ef.index', compact('emissionFactors', 'totalCount', 'avgEF'));
    }


    public function create()
    {
        return view('admin.ef.create');
    }

    // บันทึกข้อมูลที่กรอกมาจากฟอร์ม
    public function store(Request $request)
    {
        $request->validate([
            'material_name' => 'required|string|max:255|unique:emission_factors,material_name',
            'unit'          => 'nullable|string|max:50',
            'ef_value'      => 'required|numeric|min:0',
            'source'        => 'nullable|string|max:255',
            'example'       => 'nullable|string',
        ], [
            'material_name.required' => 'กรุณากรอกชื่อวัสดุ',
            'material_name.unique'   => 'มีชื่อวัสดุนี้ในระบบแล้ว',
            'ef_value.required'      => 'กรุณากรอกค่า EF',
        ]);

        EmissionFactor::create($request->all());

        return redirect()->route('keptkayas.emission.index')->with('success', 'เพิ่มข้อมูล Emission Factor สำเร็จแล้ว');
    }

    public function update(Request $request, $id)
    {
        // ตรวจสอบข้อมูล
        $request->validate([
            'material_name' => 'required|string|max:255|unique:emission_factors,material_name,' . $id,
            'unit'          => 'nullable|string|max:50',
            'ef_value'      => 'required|numeric|min:0|max:99.9999',
            'source'        => 'nullable|string|max:255',
            'example'       => 'nullable|string',
        ], [
            // 2. กำหนดข้อความแจ้งเตือนภาษาไทย (Optional)
            'material_name.required' => 'กรุณากรอกชื่อวัสดุ',
            'material_name.unique'   => 'มีชื่อวัสดุนี้อยู่ในระบบแล้ว',
            'ef_value.required'      => 'กรุณากรอกค่า EF',
            'ef_value.numeric'       => 'ค่า EF ต้องเป็นตัวเลขเท่านั้น',
        ]);

        // 3. ค้นหาและอัปเดต
        $factor = EmissionFactor::findOrFail($id);
        $factor->update($request->all());

        return redirect()->route('keptkayas.emission.index')->with('success', 'อัปเดตข้อมูลสำเร็จแล้ว');
    }

    // ดาวน์โหลดเทมเพลต
    public function export()
    {
        return Excel::download(new EmissionFactorExport, 'template_emission_factors.xlsx');
    }

    // นำเข้าข้อมูล
    public function import(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,xls']);
        Excel::import(new EmissionFactorImport, $request->file('file'));
        return back()->with('success', 'นำเข้าข้อมูล Emission Factor เรียบร้อยแล้ว');
    }

    // หน้าฟอร์มแก้ไข
    public function edit($id)
    {
        $factor = EmissionFactor::findOrFail($id);
        return view('admin.ef.edit', compact('factor'));
    }

    // ฟังก์ชันลบข้อมูล
    public function destroy($id)
    {
        $factor = EmissionFactor::findOrFail($id);
        $factor->delete();

        return back()->with('success', 'ลบข้อมูล Emission Factor เรียบร้อยแล้ว');
    }
    public function syncItemsWithEF()
    {
        $items = KpTbankItems::whereNull('ef_id_fk')->get();
        $count = 0;

        foreach ($items as $item) {
            // ค้นหา EF ที่ชื่อตรงกัน (Case Insensitive)
            $ef = EmissionFactor::where('material_name', 'LIKE', '%' . $item->kp_itemsname . '%')->first();

            if ($ef) {
                $item->update(['ef_id_fk' => $ef->id]);
                $count++;
            }
        }

        return back()->with('success', "เชื่อมโยงข้อมูลสำเร็จแล้ว $count รายการ");
    }
}
