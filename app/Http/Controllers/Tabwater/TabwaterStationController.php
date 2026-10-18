<?php

namespace App\Http\Controllers\Tabwater;

use App\Http\Controllers\Controller;
use App\Models\Admin\Organization;
use App\Models\Tabwater\TabwaterStation;
use App\Models\Admin\Staff;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TabwaterStationController extends Controller
{
    public function index()
{
    $stations = TabwaterStation::with(['staffs', 'devices'])
        ->where('deleted', false)
        ->latest()
        ->paginate(10);

    // ดึงข้อมูลสถานีทั้งหมดที่มีพิกัด lat/long สำหรับแสดงบนแผนที่
    $allStationsMap = TabwaterStation::where('deleted', false)
        ->whereNotNull('lat')
        ->whereNotNull('lng')
        ->get(['id', 'code', 'name', 'lat', 'lng', 'max_turbidity_ntu', 'min_ph', 'max_ph']);

    return view('tabwater.settings.stations.index', compact('stations', 'allStationsMap'));
}

    public function create()
    {
        $staffs = Staff::where('status', 'active')->get();
        $org = Organization::find(Auth::user()->org_id_fk)->get(['lat', 'lng'])->first();
        return view('tabwater.settings.stations.create', compact('staffs', 'org'));
    }

    public function store(Request $request)
    {

       $request->validate([
            'org_id_fk' => 'required|integer',
            'code' => 'nullable|string|max:50',
            'name' => 'required|string|max:255',
            'lat' => 'nullable|numeric',
            'lng' => 'nullable|numeric',
            'max_turbidity_ntu' => 'required|numeric|min:0',
            'min_ph' => 'required|numeric|min:0|max:14',
            'max_ph' => 'required|numeric|min:0|max:14',
            'max_tds_ppm' => 'required|numeric|min:0',
            'min_water_level_percent' => 'required|numeric|min:0|max:100',
            'max_water_level_percent' => 'required|numeric|min:0|max:100',
            'secchi_k_factor' => 'required|numeric|min:1',
            'staff_ids' => 'nullable|array',
            'staff_ids.*' => 'exists:staffs,id',
        ]);



        DB::beginTransaction();
        try {
            $station = TabwaterStation::create($request->only([
                'org_id_fk', 'code', 'name', 'lat', 'lng',
                'max_turbidity_ntu', 'min_ph', 'max_ph', 'max_tds_ppm',
                'min_water_level_percent', 'max_water_level_percent',
                'secchi_k_factor'
            ]));
            if ($request->has('staff_ids')) {
                $station->staffs()->sync($request->staff_ids);
            }

            DB::commit();
            return redirect()->route('tabwater.settings.stations.index')
                ->with('success', 'เพิ่มสถานีผลิตน้ำเรียบร้อยแล้ว');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $station = TabwaterStation::with('staffs')->findOrFail($id);
        $staffs = Staff::where('status', 'active')->get();
        $org = Organization::find(Auth::user()->org_id_fk)->get(['lat', 'lng'])->first();

        return view('tabwater.settings.stations.edit', compact('station', 'staffs','org'));
    }

    public function update(Request $request, $id)
    {
        $station = TabwaterStation::findOrFail($id);

        $request->validate([
            'code' => 'nullable|string|max:50',
            'name' => 'required|string|max:255',
            'lat' => 'nullable|numeric',
            'lng' => 'nullable|numeric',
            'max_turbidity_ntu' => 'required|numeric|min:0',
            'min_ph' => 'required|numeric|min:0|max:14',
            'max_ph' => 'required|numeric|min:0|max:14',
            'max_tds_ppm' => 'required|numeric|min:0',
            'min_water_level_percent' => 'required|numeric|min:0|max:100',
            'max_water_level_percent' => 'required|numeric|min:0|max:100',
            'secchi_k_factor' => 'required|numeric|min:1',
            'staff_ids' => 'nullable|array',
            'staff_ids.*' => 'exists:staffs,id',
        ]);

        DB::beginTransaction();
        try {
            $station->update($request->only([
                'code', 'name', 'lat', 'lng',
                'max_turbidity_ntu', 'min_ph', 'max_ph', 'max_tds_ppm',
                'min_water_level_percent', 'max_water_level_percent',
                'secchi_k_factor'
            ]));

            $station->staffs()->sync($request->staff_ids ?? []);

            DB::commit();
            return redirect()->route('tabwater.settings.stations.index')
                ->with('success', 'อัปเดตข้อมูลสถานีเรียบร้อยแล้ว');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $station = TabwaterStation::findOrFail($id);
        $station->update(['deleted' => true]);

        return redirect()->route('tabwater.settings.stations.index')
            ->with('success', 'ลบสถานีผลิตน้ำเรียบร้อยแล้ว');
    }
}