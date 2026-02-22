<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tabwater\TwMeterType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MetertypeController extends Controller
{
    // Check Permission Helper
    private function checkOwnership($item)
    {
        if ($item->org_id_fk !== Auth::user()->org_id_fk) {
            abort(403, 'Unauthorized action.');
        }
    }

    public function index()
    {
        // ใช้ Global Scope หรือ Trait ที่ทำไว้ก่อนหน้าจะดีมาก แต่ถ้าไม่มี ใช้แบบนี้ครับ
        $metertypes = TwMeterType::where('org_id_fk', Auth::user()->org_id_fk)->get();
        return view("admin.metertype.index", compact("metertypes"));
    }

    public function create()
    {
        return view("admin.metertype.create");
    }

    public function store(Request $request)
    {
        // Merge Org ID จาก User ที่ Login
        $request->merge(['org_id_fk' => Auth::user()->org_id_fk]);

        $validated = $request->validate([
            "meter_type_name" => "required|string|max:255",
            "metersize"       => "required|numeric",
            "description"     => "nullable|string", // เพิ่ม Description
            "org_id_fk"       => "required"
        ], [
            "required" => "กรุณากรอกข้อมูล",
            "numeric"  => "กรุณากรอกเป็นตัวเลข",
        ]);

        TwMeterType::create($validated);

        return redirect()->route("admin.metertype.index")
            ->with("success", "บันทึกข้อมูลเรียบร้อยแล้ว"); // เปลี่ยน key เป็น success เพื่อใช้กับ SweetAlert หรือ Alert สีเขียว
    }

    public function edit(TwMeterType $metertype)
    {
        $this->checkOwnership($metertype); // เช็คความเป็นเจ้าของ
        return view("admin.metertype.edit", compact("metertype"));
    }

    public function update(Request $request, TwMeterType $metertype)
    {
        $this->checkOwnership($metertype);

        $validated = $request->validate([
            "meter_type_name" => "required|string|max:255",
            "metersize"       => "required|numeric",
            "description"     => "nullable|string",
            // ตัด price_per_unit ออก เพราะย้ายไป RateConfig แล้ว
        ], [
            "required" => "กรุณากรอกข้อมูล",
            "numeric"  => "กรุณากรอกเป็นตัวเลข",
        ]);

        $metertype->update($validated);

        return redirect()->route("admin.metertype.index")
            ->with("success", "บันทึกการแก้ไขเรียบร้อยแล้ว");
    }

    public function destroy(TwMeterType $metertype)
    {
        $this->checkOwnership($metertype);
        
        // ควรเช็ค Dependency (การใช้งาน) ที่นี่อีกครั้งเพื่อความชัวร์ (Backend Validation)
        // แต่ถ้าจะเช็คหน้าบ้านด้วย JS แล้ว ก็สั่งลบได้เลย
        $metertype->delete(); // ใช้ delete() แทน destroy() กับ Model Instance

        return redirect()->route("admin.metertype.index")
            ->with("success", "ลบข้อมูลเรียบร้อยแล้ว");
    }
}