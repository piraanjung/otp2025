@extends('layouts.admin1')

@section('content')
<div class="container mx-auto p-4 max-w-xl">
    <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 space-y-4">
        <h2 class="text-lg font-bold text-slate-800 border-b pb-2">➕ เพิ่มอุปกรณ์/ปั๊มใหม่</h2>

        <form action="{{ route('tabwater.settings.devices.store') }}" method="POST" class="space-y-4 text-xs">
            @csrf

            <div>
                <label class="font-bold text-slate-700">เลือกสถานีผลิตน้ำ: *</label>
                <select name="station_id" required class="w-full border rounded p-2 mt-1 font-bold text-slate-800">
                    <option value="">-- เลือกสถานี --</option>
                    @foreach($stations as $s)
                        <option value="{{ $s->id }}">{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="font-bold text-slate-700">รหัสอุปกรณ์: *</label>
                    <input type="text" name="device_code" required placeholder="เช่น PUMP-RAW-01" class="w-full border rounded p-2 mt-1">
                </div>
                <div>
                    <label class="font-bold text-slate-700">ชื่ออุปกรณ์: *</label>
                    <input type="text" name="name" required placeholder="เช่น ปั๊มน้ำดิบตัวที่ 1 (5 กม.)" class="w-full border rounded p-2 mt-1">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="font-bold text-slate-700">ประเภทอุปกรณ์:</label>
                    <select name="type" class="w-full border rounded p-2 mt-1">
                        <option value="pump_raw">ปั๊มน้ำดิบ (Raw Water Pump)</option>
                        <option value="pump_clear">ปั๊มน้ำใส (Clear Water Pump)</option>
                        <option value="dosing_pump">ปั๊มเคมี (Dosing Pump)</option>
                        <option value="electric_valve">วาล์วไฟฟ้า (Electric Valve)</option>
                        <option value="relay">Relay สวิตช์ทั่วไป</option>
                    </select>
                </div>
                <div>
                    <label class="font-bold text-slate-700">โหมดเริ่มต้น:</label>
                    <select name="control_mode" class="w-full border rounded p-2 mt-1 font-bold">
                        <option value="auto">🤖 Auto (ระบบควบคุมอัตโนมัติ)</option>
                        <option value="manual">🖐️ Manual (ช่างควบคุมเอง)</option>
                    </select>
                </div>
            </div>

            <hr class="border-slate-100">
            <h3 class="font-bold text-blue-900">📡 ข้อมูลการเชื่อมต่อ IoT</h3>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="font-bold text-slate-700">MQTT Command Topic:</label>
                    <input type="text" name="mqtt_topic" placeholder="station1/pump1/command" class="w-full border rounded p-2 mt-1 font-mono">
                </div>
                <div>
                    <label class="font-bold text-slate-700">Relay Channel (ถ้ามี):</label>
                    <input type="number" name="relay_channel" placeholder="1" class="w-full border rounded p-2 mt-1">
                </div>
            </div>

            <div class="flex justify-end space-x-2 pt-2">
                <a href="{{ route('tabwater.settings.devices.index') }}" class="bg-slate-200 text-slate-700 px-4 py-2 rounded-lg font-bold">ยกเลิก</a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-bold">บันทึกอุปกรณ์</button>
            </div>
        </form>
    </div>
</div>
@endsection