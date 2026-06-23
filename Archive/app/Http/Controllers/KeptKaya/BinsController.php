<?php

namespace App\Http\Controllers\KeptKaya;

use App\Http\Controllers\Controller;

use App\Models\FoodWaste\FoodAnnualTrashStocks;
use App\Models\FoodWaste\FoodwastIotbox; // ต้องใช้ Model ของ IoT Box เพื่อดึงข้อมูลมาแสดงในฟอร์ม
use Illuminate\Http\Request;

class BinsController extends Controller
{
    public function index()
    {
        $bins = FoodAnnualTrashStocks::with('foodwaste_bin', 'foodwaste_bin.fw_user_preference', 'foodwaste_bin.fw_user_preference.user')
            ->where('bin_type', 'ab')
            ->get();
        return view('keptkayas.bins.index', compact('bins'));
    }

    public function create()
    {
        $active_bins = FoodAnnualTrashStocks::all();
        return view('keptkayas.bins.create');
    }

    public function store(Request $request)
    {
        // 1. Validation (ตรวจสอบข้อมูล)
        $request->validate([
            'bin_code' => 'required|string|unique:foodwaste_bin_stocks,bin_code|max:191',
            'description' => 'required|string|max:191',
            'status' => 'required|in:active,pending,damaged,removed',
            'bin_type' => 'required',
        ]);

        // 2. สร้างข้อมูล
        FoodAnnualTrashStocks::create($request->all());

        return redirect()->route('keptkayas.bins.index')->with('success', 'สร้างถังขยะสำเร็จ');
    }

    public function show(FoodAnnualTrashStocks $bin)
    {
        $bin->load('iotbox'); // โหลดข้อมูล IoT Box ที่เกี่ยวข้อง
        return view('foodwaste.bins.show', compact('bin'));
    }

    public function edit(FoodAnnualTrashStocks $bin)
    {
        $iotboxes = FoodwastIotbox::all();
        return view('foodwaste.bins.edit', compact('bin', 'iotboxes'));
    }

    public function update(Request $request, FoodAnnualTrashStocks $bin)
    {
        // 1. Validation
        $request->validate([
            'bin_code' => 'required|string|unique:foodwaste_bins,bin_code,' . $bin->id . '|max:191', // ยกเว้นตัวเอง
            // ... (ตรวจสอบคอลัมน์อื่น ๆ เช่นเดียวกับ store)
        ]);

        // 2. อัปเดตข้อมูล
        $bin->update($request->all());

        return redirect()->route('foodwaste.bins.index')->with('success', 'อัปเดตข้อมูลสำเร็จ');
    }

    public function destroy(FoodAnnualTrashStocks $bin)
    {
        $bin->delete();
        return redirect()->route('foodwaste.bins.index')->with('success', 'ลบข้อมูลสำเร็จ');
    }
}
