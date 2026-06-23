<?php

namespace App\Http\Controllers\FoodWaste;
use App\Http\Controllers\Controller;

use App\Models\FoodWaste\FoodwastIotbox;
use Illuminate\Http\Request;

class FoodwastIotboxController extends Controller
{
    // READ: แสดงรายการทั้งหมด
    public function index()
    {
        $iotboxes = FoodwastIotbox::all();
        return view('foodwaste.iotboxes.index', compact('iotboxes')); // ต้องสร้าง view ไฟล์ 'resources/views/iotboxes/index.blade.php'
    }

    // CREATE: แสดงฟอร์มสำหรับสร้างข้อมูลใหม่
    public function create()
    {
        return view('foodwaste.iotboxes.create'); // ต้องสร้าง view ไฟล์ 'resources/views/iotboxes/create.blade.php'
    }

    // CREATE: บันทึกข้อมูลใหม่
    public function store(Request $request)
    {
        // 1. ตรวจสอบข้อมูล (Validation)
        $request->validate([
            'iotbox_code' => 'required|string|max:100|unique:foodwast_iotboxes,iotbox_code',
            'temp_humid_sensor' => 'required|in:0,1',
            'gas_sensor' => 'required|in:0,1',
            'weight_sensor' => 'required|in:0,1',
        ]);

        // 2. สร้างข้อมูล
        FoodwastIotbox::create($request->all());

        // 3. เปลี่ยนเส้นทาง
        return redirect()->route('foodwaste.iotboxes.index')->with('success', 'สร้างข้อมูลสำเร็จ');
    }

    // READ: แสดงข้อมูลรายการเดียว
    public function show(FoodwastIotbox $iotbox)
    {
        return view('foodwaste.iotboxes.show', compact('iotbox')); // ต้องสร้าง view ไฟล์ 'resources/views/iotboxes/show.blade.php'
    }

    // UPDATE: แสดงฟอร์มสำหรับแก้ไขข้อมูล
    public function edit(FoodwastIotbox $iotbox)
    {
        return view('foodwaste.iotboxes.edit', compact('iotbox')); // ต้องสร้าง view ไฟล์ 'resources/views/iotboxes/edit.blade.php'
    }

    // UPDATE: อัปเดตข้อมูล
    public function update(Request $request, FoodwastIotbox $iotbox)
    {
        // 1. ตรวจสอบข้อมูล (Validation) - ไม่ต้องตรวจสอบ unique สำหรับรายการตัวเอง
        $request->validate([
            'iotbox_code' => 'required|string|max:50|unique:foodwast_iotboxes,iotbox_code,' . $iotbox->id,
            'temp_humid_sensor' => 'required|in:0,1',
            'gas_sensor' => 'required|in:0,1',
            'weight_sensor' => 'required|in:0,1',
        ]);

        // 2. อัปเดตข้อมูล
        $iotbox->update($request->all());

        // 3. เปลี่ยนเส้นทาง
        return redirect()->route('foodwaste.iotboxes.index')->with('success', 'อัปเดตข้อมูลสำเร็จ');
    }

    // DELETE: ลบข้อมูล
    public function destroy(FoodwastIotbox $iotbox)
    {
        $iotbox->delete();
        return redirect()->route('foodwaste.iotboxes.index')->with('success', 'ลบข้อมูลสำเร็จ');
    }
}

