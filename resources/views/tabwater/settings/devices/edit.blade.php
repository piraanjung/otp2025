@extends('layouts.admin1')

@section('content')
<div class="container py-4" style="max-width: 600px;">
    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
            <h5 class="fw-bold text-dark mb-0">✏️ แก้ไขอุปกรณ์: {{ $device->name }}</h5>
            <span class="badge bg-primary font-monospace">{{ $device->device_code }}</span>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('tabwater.settings.devices.update', $device->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label fw-bold text-secondary">เลือกสถานีผลิตน้ำ <span class="text-danger">*</span></label>
                    <select name="station_id" required class="form-select fw-bold">
                        <option value="">-- เลือกสถานี --</option>
                        @foreach($stations as $s)
                            <option value="{{ $s->id }}" {{ old('station_id', $device->station_id) == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-secondary">รหัสอุปกรณ์ <span class="text-danger">*</span></label>
                        <input type="text" name="device_code" value="{{ old('device_code', $device->device_code) }}" required class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-secondary">ชื่ออุปกรณ์ <span class="text-danger">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $device->name) }}" required class="form-control">
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-secondary">ประเภทอุปกรณ์</label>
                        @php $currentType = old('type', $device->type); @endphp
                        <select name="type" class="form-select">
                            <option value="pump_raw" {{ $currentType === 'pump_raw' ? 'selected' : '' }}>ปั๊มน้ำดิบ (Raw Water Pump)</option>
                            <option value="pump_clear" {{ $currentType === 'pump_clear' ? 'selected' : '' }}>ปั๊มน้ำใส (Clear Water Pump)</option>
                            <option value="dosing_pump" {{ $currentType === 'dosing_pump' ? 'selected' : '' }}>ปั๊มเคมี (Dosing Pump)</option>
                            <option value="electric_valve" {{ $currentType === 'electric_valve' ? 'selected' : '' }}>วาล์วไฟฟ้า (Electric Valve)</option>
                            <option value="relay" {{ $currentType === 'relay' ? 'selected' : '' }}>Relay สวิตช์ทั่วไป</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-secondary">โหมดการทำงาน</label>
                        @php $currentMode = old('control_mode', $device->control_mode); @endphp
                        <select name="control_mode" class="form-select fw-bold">
                            <option value="auto" {{ $currentMode === 'auto' ? 'selected' : '' }}>🤖 Auto (ระบบควบคุมอัตโนมัติ)</option>
                            <option value="manual" {{ $currentMode === 'manual' ? 'selected' : '' }}>🖐️ Manual (ช่างควบคุมเอง)</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="form-check bg-light p-3 border rounded">
                        <input type="checkbox" class="form-check-input" id="auto_control_enabled" name="auto_control_enabled" value="1" {{ old('auto_control_enabled', $device->auto_control_enabled) ? 'checked' : '' }}>
                        <label class="form-check-label fw-bold text-secondary" for="auto_control_enabled">
                            เปิดใช้งานระบบ Auto Cut ตัดการทำงานอัตโนมัติเมื่อระดับน้ำเต็ม
                        </label>
                    </div>
                </div>

                <hr class="my-4 text-muted">
                <h6 class="fw-bold text-primary mb-3">📡 ข้อมูลการเชื่อมต่อ IoT</h6>

                <div class="row g-3 mb-4">
                    <div class="col-md-8">
                        <label class="form-label fw-bold text-secondary">MQTT Command Topic</label>
                        <input type="text" name="mqtt_topic" value="{{ old('mqtt_topic', $device->mqtt_topic) }}" class="form-control font-monospace">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-secondary">Relay Channel</label>
                        <input type="number" name="relay_channel" value="{{ old('relay_channel', $device->relay_channel) }}" class="form-control">
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('tabwater.settings.devices.index') }}" class="btn btn-light fw-bold">ยกเลิก</a>
                    <button type="submit" class="btn btn-primary fw-bold">อัปเดตอุปกรณ์</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection