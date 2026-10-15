<?php

namespace App\Http\Controllers\Tabwater;

use App\Http\Controllers\Controller;
use App\Models\Tabwater\TabwaterPipeNode;
use App\Models\Tabwater\TabwaterStation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TabwaterPipeNodeController extends Controller
{
 public function index(Request $request)
{
    $query = TabwaterPipeNode::with(['station', 'outgoingPipes', 'incomingPipes']);

    // กรองตามสถานี (ถ้าผู้ใช้เลือกจาก Dropdown)
    if ($request->filled('station_id')) {
        $query->where('station_id', $request->station_id);
    }

    // กรองตามประเภท (ถ้าผู้ใช้เลือกจาก Dropdown)
    if ($request->filled('type')) {
        $query->where('type', $request->type);
    }

    $nodes = $query->latest()->paginate(10);

    // ดึงข้อมูล Pipe Nodes "ทั้งหมด" สำหรับปักหมุดบนแผนที่ (อิงตาม Filter เดียวกัน)
    $allNodesMapQuery = TabwaterPipeNode::whereNotNull('lat')->whereNotNull('lng');

    if ($request->filled('station_id')) {
        $allNodesMapQuery->where('station_id', $request->station_id);
    }

    if ($request->filled('type')) {
        $allNodesMapQuery->where('type', $request->type);
    }

    $allNodesMap = $allNodesMapQuery->get(['id', 'station_id', 'node_code', 'name', 'type', 'lat', 'lng']);

    // ดึงพิกัดสถานีผลิตน้ำประปาอ้างอิง
    $stationsMap = TabwaterStation::where('deleted', false)
        ->whereNotNull('lat')
        ->whereNotNull('lng') // หรือ 'lng' ตามฟีลด์ของตาราง tabwater_stations
        ->get(['id', 'code', 'name', 'lat', 'lng']);

    $stations = TabwaterStation::where('deleted', false)->get();

    return view('tabwater.settings.pipe_nodes.index', compact('nodes', 'allNodesMap', 'stations', 'stationsMap'));
}
 public function create()
{
    // 1. ดึงสถานีผลิตน้ำทั้งหมดสำหรับเลือกใน Dropdown
    $stations = TabwaterStation::where('deleted', false)->get();

    // 2. ดึงสถานีผลิตน้ำทั้งหมดที่มีพิกัด GIS สำหรับแสดงบนแผนที่อ้างอิง (หมุดสีน้ำเงิน)
    $stationsMap = TabwaterStation::where('deleted', false)
        ->whereNotNull('lat')
        ->whereNotNull('lng')
        ->get(['id', 'code', 'name', 'lat', 'lng']);

    // 3. ดึงจุดโหนดเดิมทั้งหมดที่มีอยู่แล้วในระบบ สำหรับแสดงเป็นหมุดอ้างอิงบนแผนที่
    $allNodesMap = TabwaterPipeNode::whereNotNull('lat')
        ->whereNotNull('lng')
        ->get(['id', 'station_id', 'node_code', 'name', 'type', 'lat', 'lng']);

    return view('tabwater.settings.pipe_nodes.create', compact('stations', 'stationsMap', 'allNodesMap'));
}


public function store(Request $request)
{
    // ตรวจสอบ Validation กรณีรับข้อมูลเป็น Array ของโหนดใหม่
    $request->validate([
        'nodes' => 'required|array|min:1',
        'nodes.*.station_id' => 'required|exists:tabwater_stations,id',
        'nodes.*.node_code' => 'required|string|max:50',
        'nodes.*.name' => 'required|string|max:255',
        'nodes.*.type' => 'required|in:station,main_junction,sub_junction,valve_point,tank,end_point',
        'nodes.*.lat' => 'required|numeric',
        'nodes.*.lng' => 'required|numeric',
    ]);

    // บันทึกข้อมูลโหนดใหม่ทั้งหมดลง Database
    DB::transaction(function () use ($request) {
        foreach ($request->nodes as $nodeData) {
            TabwaterPipeNode::create([
                'station_id' => $nodeData['station_id'],
                'node_code'  => $nodeData['node_code'],
                'name'       => $nodeData['name'],
                'type'       => $nodeData['type'],
                'lat'        => $nodeData['lat'],
                'lng'        => $nodeData['lng'],
                'remark'     => $nodeData['remark'] ?? null,
            ]);
        }
    });

    return redirect()->route('tabwater.settings.pipe-nodes.create')
        ->with('success', 'บันทึกจุดโหนดใหม่ทั้งหมดเรียบร้อยแล้ว');
}
    public function edit($id)
    {
        $node = TabwaterPipeNode::findOrFail($id);
        $stations = TabwaterStation::where('deleted', false)->get();
        return view('tabwater.settings.pipe_nodes.edit', compact('node', 'stations'));
    }

    public function update(Request $request, $id)
    {
        $node = TabwaterPipeNode::findOrFail($id);

        $request->validate([
            'station_id' => 'required|exists:tabwater_stations,id',
            'node_code' => 'required|string|max:50',
            'name' => 'required|string|max:255',
            'type' => 'required|in:station,main_junction,sub_junction,valve_point,tank,end_point',
            'lat' => 'required|numeric',
            'long' => 'required|numeric',
            'remark' => 'nullable|string',
        ]);

        $node->update($request->only([
            'station_id', 'node_code', 'name', 'type', 'lat', 'long', 'remark'
        ]));

        return redirect()->route('tabwater.settings.pipe-nodes.index')
            ->with('success', 'อัปเดตข้อมูลจุดโหนดเรียบร้อยแล้ว');
    }

    public function destroy($id)
    {
        $node = TabwaterPipeNode::findOrFail($id);

        // เช็กก่อนลบว่ามีท่อเชื่อมต่ออยู่หรือไม่
        if ($node->outgoingPipes()->count() > 0 || $node->incomingPipes()->count() > 0) {
            return back()->with('error', 'ไม่สามารถลบจุดโหนดนี้ได้ เนื่องจากมีเส้นทางท่อเชื่อมต่ออยู่');
        }

        $node->delete();

        return redirect()->route('tabwater.settings.pipe-nodes.index')
            ->with('success', 'ลบจุดโหนดเรียบร้อยแล้ว');
    }
}