<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Subzone; // ตรวจสอบชื่อ Model ของคุณ
use App\Models\Admin\Zone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubzoneController extends Controller
{
    // ฟังก์ชันสำหรับบันทึกเส้นทางใหม่ (จากฟอร์มในหน้าจัดการ)
    public function store(Request $request)
    {
        $request->validate([
            'subzone_name' => 'required|string|max:255',
            'zone_id_fk' => 'required|exists:zones,id', // ตรวจสอบชื่อตาราง zone ของคุณ
        ]);

        Subzone::create([
            'subzone_name' => $request->subzone_name,
            'zone_id_fk' => $request->zone_id_fk,
            'org_id_fk' => Auth::user()->org_id_fk ?? 1, // ปรับตามระบบของคุณ
        ]);

        return back()->with('success', 'เพิ่มเส้นทางใหม่เรียบร้อยแล้ว');
    }

    // ฟังก์ชันสำหรับแสดงหน้าแก้ไข (ที่คุณเรียกใช้แล้วติด 404 หรือ Error)
    public function edit($id)
    {
        // ในที่นี้ $id คือ ID ของ Zone หลักเพื่อไปจัดการ Subzones ข้างใน
        $zone = Zone::withoutGlobalScope('org')->with('subzone')->findOrFail($id);
        return view('admin.subzone.edit', compact('zone'));
    }

    // ฟังก์ชันเจ้าปัญหาที่ฟ้องว่า Does not exist (ต้องเพิ่มตัวนี้ครับ)
    public function update(Request $request, $id)
    {
        $request->validate([
            'subzone' => 'required|array',
            'subzone.*.new.subzone_name' => 'required|string|max:255',
        ]);

        try {
            // $id ในที่นี้คือ Zone ID
            $zone = Zone::withoutGlobalScope('org')->findOrFail($id);

            // 2. วน Loop จัดการข้อมูลที่ส่งมา
            foreach ($request->subzone as $item) {
                if (isset($item['new']['subzone_name'])) {
                    // สร้าง Subzone ใหม่ภายใต้ Zone นี้
                    $zone->subzone()->create([
                        'subzone_name' => $item['new']['subzone_name'],
                        'org_id_fk' => Auth::user()->org_id_fk ?? 1,
                        // ใส่ field อื่นๆ ตาม model ของคุณ
                    ]);
                }
            }

            return redirect()
                ->route('admin.zone.edit', $id)
                ->with('success', 'บันทึกข้อมูลเส้นทางย่อยเรียบร้อยแล้ว');
        } catch (\Exception $e) {
            return back()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }

    // ฟังก์ชันลบ
    public function destroy($id)
    {
        $subzone = Subzone::findOrFail($id);
        $subzone->delete();

        return back()->with('success', 'ลบเส้นทางเรียบร้อยแล้ว');
    }
}
