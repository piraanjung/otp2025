<?php

namespace App\Http\Controllers\Tabwater;

use App\Http\Controllers\Controller;
use App\Models\Tabwater\TabwaterDevice;
use App\Models\Tabwater\TabwaterStation;
use Illuminate\Http\Request;

class TabwaterDeviceController extends Controller
{
    public function index(Request $request)
    {
        $query = TabwaterDevice::with('station');

        if ($request->has('station_id') && $request->station_id != '') {
            $query->where('station_id', $request->station_id);
        }

        $devices = $query->latest()->paginate(10);
        $stations = TabwaterStation::where('deleted', false)->get();

        return view('tabwater.settings.devices.index', compact('devices', 'stations'));
    }

    public function create()
    {
        $stations = TabwaterStation::where('deleted', false)->get();
        return view('tabwater.settings.devices.create', compact('stations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'station_id' => 'required|exists:tabwater_stations,id',
            'device_code' => 'required|string|max:50',
            'name' => 'required|string|max:255',
            'type' => 'required|in:pump_raw,pump_clear,dosing_pump,electric_valve,relay',
            'control_mode' => 'required|in:auto,manual',
            'auto_control_enabled' => 'boolean',
            'mqtt_topic' => 'nullable|string|max:255',
            'relay_channel' => 'nullable|integer|min:1',
        ]);

        TabwaterDevice::create([
            'station_id' => $request->station_id,
            'device_code' => $request->device_code,
            'name' => $request->name,
            'type' => $request->type,
            'status' => false,
            'control_mode' => $request->control_mode,
            'auto_control_enabled' => $request->has('auto_control_enabled'),
            'mqtt_topic' => $request->mqtt_topic,
            'relay_channel' => $request->relay_channel,
        ]);

        return redirect()->route('tabwater.settings.devices.index')
            ->with('success', 'เพิ่มอุปกรณ์เรียบร้อยแล้ว');
    }

    public function edit($id)
    {
        $device = TabwaterDevice::findOrFail($id);
        $stations = TabwaterStation::where('deleted', false)->get();
        return view('tabwater.settings.devices.edit', compact('device', 'stations'));
    }

    public function update(Request $request, $id)
    {
        $device = TabwaterDevice::findOrFail($id);

        $request->validate([
            'station_id' => 'required|exists:tabwater_stations,id',
            'device_code' => 'required|string|max:50',
            'name' => 'required|string|max:255',
            'type' => 'required|in:pump_raw,pump_clear,dosing_pump,electric_valve,relay',
            'control_mode' => 'required|in:auto,manual',
            'mqtt_topic' => 'nullable|string|max:255',
            'relay_channel' => 'nullable|integer|min:1',
        ]);

        $device->update([
            'station_id' => $request->station_id,
            'device_code' => $request->device_code,
            'name' => $request->name,
            'type' => $request->type,
            'control_mode' => $request->control_mode,
            'auto_control_enabled' => $request->has('auto_control_enabled'),
            'mqtt_topic' => $request->mqtt_topic,
            'relay_channel' => $request->relay_channel,
        ]);

        return redirect()->route('tabwater.settings.devices.index')
            ->with('success', 'อัปเดตข้อมูลอุปกรณ์เรียบร้อยแล้ว');
    }

    public function destroy($id)
    {
        $device = TabwaterDevice::findOrFail($id);
        $device->delete();

        return redirect()->route('tabwater.settings.devices.index')
            ->with('success', 'ลบอุปกรณ์เรียบร้อยแล้ว');
    }
}