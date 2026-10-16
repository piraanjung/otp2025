@extends('layouts.admin1')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold text-dark mb-0">🔌 ตั้งค่าอุปกรณ์/ปั๊มประจำสถานี</h4>
        <a href="{{ route('tabwater.settings.devices.create') }}" class="btn btn-primary btn-sm fw-bold">
            <i class="bi bi-plus-lg me-1"></i> เพิ่มอุปกรณ์ใหม่
        </a>
    </div>

    <!-- Filter ตามสถานี -->
    <div class="card shadow-sm border-0 mb-3">
        <div class="card-body p-2">
            <form method="GET" action="{{ route('tabwater.settings.devices.index') }}" class="row g-2 align-items-center">
                <div class="col-auto">
                    <select name="station_id" onchange="this.form.submit()" class="form-select form-select-sm fw-bold">
                        <option value="">-- แสดงทุกสถานี --</option>
                        @foreach($stations as $s)
                            <option value="{{ $s->id }}" {{ request('station_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-sm">
                    <thead class="table-light border-bottom text-secondary text-nowrap">
                        <tr>
                            <th>รหัสอุปกรณ์</th>
                            <th>ชื่ออุปกรณ์</th>
                            <th>สถานี</th>
                            <th>ประเภท</th>
                            <th>โหมด</th>
                            <th>MQTT Topic / Relay</th>
                            <th class="text-end">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($devices as $dev)
                            <tr>
                                <td class="fw-bold text-secondary">{{ $dev->device_code }}</td>
                                <td class="fw-bold text-primary">{{ $dev->name }}</td>
                                <td class="fw-bold text-dark">{{ $dev->station->name ?? '-' }}</td>
                                <td><span class="badge bg-primary-subtle text-primary border border-primary-subtle fw-bold">{{ $dev->type }}</span></td>
                                <td>
                                    <span class="badge {{ $dev->control_mode === 'auto' ? 'bg-success' : 'bg-warning text-dark' }}">
                                        {{ strtoupper($dev->control_mode) }}
                                    </span>
                                </td>
                                <td class="font-monospace text-muted small">{{ $dev->mqtt_topic ?? "Ch. {$dev->relay_channel}" }}</td>
                                <td class="text-end text-nowrap">
                                    <a href="{{ route('tabwater.settings.devices.edit', $dev->id) }}" class="btn btn-sm btn-outline-primary fw-bold me-1">แก้ไข</a>
                                    <form action="{{ route('tabwater.settings.devices.destroy', $dev->id) }}" method="POST" class="d-inline" onsubmit="return confirm('ยืนยันการลบอุปกรณ์นี้?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger fw-bold">ลบ</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">ยังไม่มีข้อมูลอุปกรณ์ในระบบ</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-3">
        {{ $devices->links() }}
    </div>
</div>
@endsection