<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;


use Illuminate\Http\Request;
use App\Models\Supplier;
use Illuminate\Support\Facades\Auth;

class SupplierController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // ดึง Supplier ทั้งหมดขององค์กร
        $suppliers = Supplier::where('org_id_fk', $user->org_id_fk)
            ->orderBy('created_at', 'desc')
            ->get();

        // ดึงรายชื่อแผนกที่ไม่ซ้ำกัน สำหรับทำ Autocomplete ในฟอร์มเพิ่ม/กรอง
        $departments = Supplier::where('org_id_fk', $user->org_id_fk)
            ->whereNotNull('department')
            ->distinct()
            ->pluck('department');

        return view('admin.suppliers.index', compact('suppliers', 'departments'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'department'     => 'nullable|string|max:255', // รองรับแผนก
            'phone'          => 'nullable|string|max:50',
            'contact_person' => 'nullable|string|max:255',
            'address'        => 'nullable|string',
        ]);

        $user = Auth::user();

        Supplier::create([
            'org_id_fk'      => $user->org_id_fk,
            'department'     => $request->department,
            'name'           => $request->name,
            'contact_person' => $request->contact_person,
            'phone'          => $request->phone,
            'address'        => $request->address,
        ]);

        return redirect()->route('admin.suppliers.index')
            ->with('success', 'บันทึกข้อมูลผู้จำหน่ายเรียบร้อยแล้ว');
    }

    public function edit($id)
    {
        $user = Auth::user();
        
        // ดึง Supplier ที่ต้องการแก้ไข (เช็คว่าเป็นขององค์กรตัวเอง)
        $supplier = Supplier::where('org_id_fk', $user->org_id_fk)->findOrFail($id);

        // ดึงข้อมูลทั้งหมดมาแสดงฝั่งตารางด้านขวาเหมือนเดิม
        $suppliers = Supplier::where('org_id_fk', $user->org_id_fk)
                        ->orderBy('created_at', 'desc')
                        ->get();

        // ดึงรายชื่อแผนกสำหรับ Autocomplete
        $departments = Supplier::where('org_id_fk', $user->org_id_fk)
                        ->whereNotNull('department')
                        ->distinct()
                        ->pluck('department');

        // ส่งตัวแปร $supplier ไปด้วย เพื่อให้ฟอร์มรู้ว่ากำลังโหมดแก้ไข
        return view('admin.suppliers.index', compact('suppliers', 'departments', 'supplier'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'department'     => 'nullable|string|max:255',
            'phone'          => 'nullable|string|max:50',
            'contact_person' => 'nullable|string|max:255',
            'address'        => 'nullable|string',
        ]);

        $user = Auth::user();
        
        $supplier = Supplier::where('org_id_fk', $user->org_id_fk)->findOrFail($id);

        $supplier->update([
            'department'     => $request->department,
            'name'           => $request->name,
            'contact_person' => $request->contact_person,
            'phone'          => $request->phone,
            'address'        => $request->address,
        ]);

        return redirect()->route('admin.suppliers.index')
            ->with('success', 'อัปเดตข้อมูลผู้จำหน่ายเรียบร้อยแล้ว');
    }
    public function destroy($id)
    {
        $user = Auth::user();
        $supplier = Supplier::where('org_id_fk', $user->org_id_fk)->findOrFail($id);
        $supplier->delete();

        return redirect()->route('admin.suppliers.index')
            ->with('success', 'ลบข้อมูลผู้จำหน่ายเรียบร้อยแล้ว');
    }
}
