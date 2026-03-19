<?php

namespace App\Http\Controllers\Admin;


use App\Models\Admin\Zone;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Admin\Organization;


class ZoneController extends Controller
{
    public function index(Request $request)
    {
        // ดึง Org ทั้งหมดมาทำ Dropdown
        $organizations = Organization::withoutGlobalScope('org')->get();

        // ดึงข้อมูลโซน ถ้ามีการเลือก Org ให้กรองตามนั้น ถ้าไม่เลือกให้แสดงทั้งหมด (หรือว่างไว้ก่อน)
        $zones = Zone::withoutGlobalScope('org')->with('subzone')
            ->when($request->org_id, function ($query) use ($request) {
                return $query->where('org_id_fk', $request->org_id);
            })
            ->get();

        return view('admin.zone.index', compact('zones', 'organizations'));
    }

    public function create(Request $request)
    {
        $org_id_fk = $request->org_id;
        $org = Organization::with('tambons')->where('id',$request->org_id)->get(['id', 'org_tambon_id_fk']);
        return view('admin.zone.create',compact('org_id_fk', 'org'));
    }


    public function store(Request $request)
{
    $request->validate([
        'zone' => 'required|array',
        'org_id_fk' => 'required|exists:organizations,id', // ตรวจสอบว่ามี Org นี้จริง
        //'status'    => 'required|in:active,inactive',
    ]);

    // สร้างข้อมูลโดยใช้ค่า org_id_fk ที่ส่งมาจาก Form

    foreach($request->zone as $data) {
        Zone::create([
            'org_id_fk' => $request->org_id_fk,
            'zone_name' => $data['zonename'],
            'location'  => $data['location'],
            'tambon_id' => $request->tambon_id,
            'lat'       => $data['lat'],
            'long'      => $data['long'],
            'status'    => 'active'
        ]);
    }


    return redirect()
        ->route('admin.zone.index', ['org_id' => $request->org_id_fk])
        ->with('success', 'สร้างหมู่บ้าน/โซนเรียบร้อยแล้ว');
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

    public function edit($id)
    {
        // ดึงข้อมูล Zone พร้อมความสัมพันธ์ subzone
        $zone = Zone::withoutGlobalScope('org')->with('subzone')->findOrFail($id);

        // ส่งไปที่ View (ตรวจสอบ path ไฟล์ view ของคุณด้วยนะครับ)
        return view('admin.zone.edit', compact('zone'));
    }

    public function update(Request $request, $id)
    {
        // เพิ่มส่วน Validation เพื่อความปลอดภัยตามโครงสร้าง Model
        $request->validate([
            'zone_name' => 'required|string|max:255',
            'tambon_id' => 'nullable|integer',
            'status'    => 'required|in:active,inactive',
            'lat'       => 'nullable|numeric',
            'long'      => 'nullable|numeric',
        ]);

        $zone = Zone::findOrFail($id);

        // อัปเดตข้อมูลตาม fillable ที่คุณกำหนดไว้ใน Model
        $zone->update($request->all());

        return redirect()
            ->route('admin.zone.index')
            ->with('success', 'ปรับปรุงข้อมูลหมู่บ้าน/โซน เรียบร้อยแล้ว');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Zone $zone)
    {
        $zonename = $zone->zone_name;
        try {
            $zone->delete();
            $message = 'ลบ ' . $zonename . ' แล้ว';
            $color = "success";
        } catch (\Exception $e) {
            $message = 'ลบ ' . $zonename . ' ไม่ได้ ใช้งานอยู่';
            $color = "danger";
        }
        return redirect()->back()->with(['message' => $message, "color" => $color]);
    }

    public function getZones($tambon_id)
    {
        $zones = Zone::where('tambon_id', $tambon_id)
            ->with('subzone')
            ->get(['id', 'tambon_id', 'zone_name', 'location']);
        return response()->json(['zones' => $zones]);
    }
}
