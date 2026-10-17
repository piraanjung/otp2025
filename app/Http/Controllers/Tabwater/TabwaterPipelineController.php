<?php

namespace App\Http\Controllers\Tabwater;

use App\Http\Controllers\Controller;
use App\Models\Tabwater\TabwaterStation;
use App\Models\Tabwater\TabwaterPipeNode;
use App\Models\Tabwater\TabwaterPipeline;
use Illuminate\Http\Request;

class TabwaterPipelineController extends Controller
{
    public function index(Request $request)
    {
        $query = TabwaterPipeline::with(['station', 'fromNode', 'toNode']);

        if ($request->filled('station_id')) {
            $query->where('station_id', $request->station_id);
        }

        $pipelines = $query->latest()->paginate(10);
        $stations = TabwaterStation::where('deleted', false)->get();

        // 1. ดึงพิกัดสถานีผลิตน้ำประปา
        $stationsMap = TabwaterStation::where('deleted', false)
            ->whereNotNull('lat')->whereNotNull('lng')
            ->get(['id', 'code', 'name', 'lat', 'lng']);

        // 2. ดึงโหนดทั้งหมดในระบบ
        $allNodesMap = TabwaterPipeNode::whereNotNull('lat')->whereNotNull('lng')
            ->get(['id', 'station_id', 'node_code', 'name', 'type', 'lat', 'lng']);

        // 3. ดึงแนวท่อทั้งหมดที่มีพิกัด path_coordinates
        $allPipelinesMap = TabwaterPipeline::whereNotNull('path_coordinates')
            ->get(['id', 'pipe_code', 'name', 'level', 'size_mm', 'from_node_id', 'to_node_id', 'path_coordinates']);

        return view('tabwater.settings.pipelines.index', compact('pipelines', 'stations', 'stationsMap', 'allNodesMap', 'allPipelinesMap'));
    }

    public function create(Request $request)
{
    $stations = TabwaterStation::where('deleted', '0')->get();
    $nodes = TabwaterPipeNode::where('deleted', '0')->get();

    $stationsMap = $stations;
    $allNodesMap = $nodes;
    
    // 📌 ดึงแนวท่อเดิมทั้งหมดเพื่อนำไปโชว์เป็นเส้นอ้างอิงบนแผนที่
    $allPipelinesMap = TabwaterPipeline::where('deleted', '0')
        ->get(['id', 'pipe_code', 'name', 'station_id', 'level', 'size_mm', 'length_m', 'path_coordinates']);

    return view('tabwater.settings.pipelines.create', compact(
        'stations', 'nodes', 'stationsMap', 'allNodesMap', 'allPipelinesMap'
    ));
}

    public function store(Request $request)
    {
        $request->validate([
            'station_id' => 'required|exists:tabwater_stations,id',
            'pipe_code' => 'required|string|max:50',
            'name' => 'required|string|max:255',
            'from_node_id' => 'required|exists:tabwater_pipe_nodes,id',
            'to_node_id' => 'required|exists:tabwater_pipe_nodes,id|different:from_node_id',
            'level' => 'required|in:main,sub_main,distribution',
            'size_mm' => 'required|integer|min:1',
            'material' => 'required|string',
            'path_coordinates' => 'required|json',
        ]);

        $pathCoords = json_decode($request->path_coordinates, true);

        TabwaterPipeline::create([
            'station_id' => $request->station_id,
            'pipe_code' => $request->pipe_code,
            'name' => $request->name,
            'from_node_id' => $request->from_node_id,
            'to_node_id' => $request->to_node_id,
            'level' => $request->level,
            'size_mm' => $request->size_mm,
            'material' => $request->material,
            'length_m' => $request->length_m,
            'path_coordinates' => $pathCoords,
        ]);

        return redirect()->route('tabwater.settings.pipelines.index')
            ->with('success', 'บันทึกแนวท่อน้ำประปาเรียบร้อยแล้ว');
    }

    public function edit($id)
    {
        $pipeline = TabwaterPipeline::findOrFail($id);
        $stations = TabwaterStation::where('deleted', false)->get();
        $nodes = TabwaterPipeNode::whereNotNull('lat')->whereNotNull('lng')->get();

        $stationsMap = TabwaterStation::where('deleted', false)
            ->whereNotNull('lat')->whereNotNull('lng')
            ->get(['id', 'code', 'name', 'lat', 'lng']);

        $allNodesMap = TabwaterPipeNode::whereNotNull('lat')->whereNotNull('lng')
            ->get(['id', 'station_id', 'node_code', 'name', 'type', 'lat', 'lng']);

        return view('tabwater.settings.pipelines.edit', compact('pipeline', 'stations', 'nodes', 'stationsMap', 'allNodesMap'));
    }

    public function update(Request $request, $id)
    {
        $pipeline = TabwaterPipeline::findOrFail($id);

        $request->validate([
            'station_id' => 'required|exists:tabwater_stations,id',
            'pipe_code' => 'required|string|max:50',
            'name' => 'required|string|max:255',
            'from_node_id' => 'required|exists:tabwater_pipe_nodes,id',
            'to_node_id' => 'required|exists:tabwater_pipe_nodes,id|different:from_node_id',
            'level' => 'required|in:main,sub_main,distribution',
            'size_mm' => 'required|integer|min:1',
            'material' => 'required|string',
            'path_coordinates' => 'required|json',
        ]);

        $pathCoords = json_decode($request->path_coordinates, true);

        $pipeline->update([
            'station_id' => $request->station_id,
            'pipe_code' => $request->pipe_code,
            'name' => $request->name,
            'from_node_id' => $request->from_node_id,
            'to_node_id' => $request->to_node_id,
            'level' => $request->level,
            'size_mm' => $request->size_mm,
            'material' => $request->material,
            'length_m' => $request->length_m,
            'path_coordinates' => $pathCoords,
        ]);

        return redirect()->route('tabwater.settings.pipelines.index')
            ->with('success', 'อัปเดตข้อมูลแนวท่อน้ำประปาเรียบร้อยแล้ว');
    }

    public function destroy($id)
    {
        $pipeline = TabwaterPipeline::findOrFail($id);
        $pipeline->delete();

        return redirect()->route('tabwater.settings.pipelines.index')
            ->with('success', 'ลบแนวท่อประปาเรียบร้อยแล้ว');
    }
}