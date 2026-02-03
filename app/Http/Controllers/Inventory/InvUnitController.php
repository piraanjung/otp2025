<?php

namespace App\Http\Controllers\Inventory;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\InvUnit;
use Illuminate\Support\Facades\Auth;

class InvUnitController extends Controller
{
    // แสดงรายการ + ฟอร์มเพิ่ม
    public function index()
    {
        $user = Auth::user();
        $units = InvUnit::where('org_id_fk', $user->org_id_fk)
                        ->orderBy('name', 'asc')
                        ->get();

        return view('inventory.settings.inv_unit_index', compact('units'));
    }

    // บันทึก
    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:50']);
        $user = Auth::user();

        // เช็คซ้ำ
        $exists = InvUnit::where('org_id_fk', $user->org_id_fk)
                         ->where('name', $request->name)->exists();

        if ($exists) {
            return back()->with('error', 'หน่วยนับนี้มีอยู่แล้ว');
        }

        InvUnit::create([
            'org_id_fk' => $user->org_id_fk,
            'name' => $request->name
        ]);

        return back()->with('success', 'เพิ่มหน่วยนับเรียบร้อย');
    }

    // ลบ
    public function destroy($id)
    {
        $unit = InvUnit::findOrFail($id);
        
        // ควรเช็คก่อนว่ามี Item ไหนใช้หน่วยนี้อยู่ไหม (ถ้าซีเรียส)
        // if(...) { return back()->with('error', 'ลบไม่ได้ มีพัสดุใช้งานอยู่'); }

        $unit->delete();
        return back()->with('success', 'ลบข้อมูลเรียบร้อย');
    }
}