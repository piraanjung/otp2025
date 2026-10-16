@extends('layouts.admin1')

@section('content')
<div class="container-fluid py-4">
    <!-- Header & Navigation -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0 font-weight-bold">⚙️ จัดการเซ็นเซอร์ประจำตู้: {{ $kiosk->name }}</h4>
            <small class="text-muted">รหัสตู้ (ID): <code>{{ $kiosk->id }}</code> | สถานะตู้: <span class="badge bg-info">{{ strtoupper($kiosk->status) }}</span></small>
        </div>
        <div>
            <a href="{{ route('keptkayas.kiosks.index') }}" class="btn btn-outline-secondary btn-sm me-2">
                <i class="bi bi-arrow-left"></i> ย้อนกลับไปรายการตู้
            </a>
            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addSensorModal">
                ➕ เพิ่มเซ็นเซอร์ใหม่
            </button>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show text-white" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close text-white" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show text-white" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close text-white" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Sensors Table Card -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-items-center mb-0 table-hover">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">รหัสอุปกรณ์ (Code)</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ชื่อเซ็นเซอร์ / อุปกรณ์</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">ประเภท</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">สถานะปัจจุบัน</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ค่าล่าสุด / ข้อความ</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">อัปเดตล่าสุด</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sensors as $sensor)
                            <tr>
                                <td class="ps-3">
                                    <code class="text-dark font-weight-bold">{{ $sensor->sensor_code }}</code>
                                </td>
                                <td>
                                    <span class="text-sm font-weight-bold">{{ $sensor->sensor_name }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-gradient-secondary text-xxs">
                                        {{ strtoupper($sensor->sensor_type) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @php
                                        $statusClass = match($sensor->status) {
                                            'ok' => 'bg-success',
                                            'warning' => 'bg-warning text-dark',
                                            'error' => 'bg-danger',
                                            default => 'bg-secondary'
                                        };
                                    @endphp
                                    <span class="badge {{ $statusClass }} text-xxs">
                                        {{ strtoupper($sensor->status) }}
                                    </span>
                                </td>
                                <td>
                                    <small class="d-block">{{ $sensor->last_val ?? '-' }}</small>
                                    @if($sensor->error_message)
                                        <small class="text-danger fw-bold">{{ $sensor->error_message }}</small>
                                    @endif
                                </td>
                                <td class="text-center text-xs text-muted">
                                    {{ $sensor->last_ping_at ? \Carbon\Carbon::parse($sensor->last_ping_at)->diffForHumans() : '-' }}
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-link text-dark px-2 mb-0" 
                                            onclick="openEditModal({{ json_encode($sensor) }})">
                                        ✏️ แก้ไข
                                    </button>

                                    <form action="{{ route('keptkayas.kiosks.sensors.destroy', [$kiosk->id, $sensor->id]) }}" 
                                          method="POST" class="d-inline" onsubmit="return confirm('ยืนยันการลบเซ็นเซอร์นี้หรือไม่?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-link text-danger px-2 mb-0">
                                            🗑️ ลบ
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    ยังไม่มีการลงทะเบียนเซ็นเซอร์สำหรับตู้ใบนี้
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal 1: เพิ่มเซ็นเซอร์ใหม่ -->
<div class="modal fade" id="addSensorModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('keptkayas.kiosks.sensors.store', $kiosk->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold">➕ เพิ่มเซ็นเซอร์ใหม่</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label font-weight-bold">รหัสเซ็นเซอร์ (Code) <span class="text-danger">*</span></label>
                        <input type="text" name="sensor_code" class="form-control" placeholder="เช่น ULTRASONIC_BIN, SERVO_DOOR" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label font-weight-bold">ชื่อเซ็นเซอร์ / อุปกรณ์ <span class="text-danger">*</span></label>
                        <input type="text" name="sensor_name" class="form-control" placeholder="เช่น เซ็นเซอร์วัดระดับขวดในถัง" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label font-weight-bold">ประเภทอุปกรณ์ <span class="text-danger">*</span></label>
                        <select name="sensor_type" class="form-select" required>
                            <option value="mcu_sensor">MCU Sensor (เซ็นเซอร์อ่านค่า)</option>
                            <option value="actuator">Actuator (มอเตอร์/เซอร์โว)</option>
                            <option value="camera">Camera (กล้อง AI)</option>
                            <option value="system">System (ระบบควบคุม)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label font-weight-bold">สถานะเริ่มต้น <span class="text-danger">*</span></label>
                        <select name="status" class="form-select" required>
                            <option value="ok">OK (ปกติ)</option>
                            <option value="warning">WARNING (เตือน)</option>
                            <option value="error">ERROR (ขัดข้อง)</option>
                            <option value="offline">OFFLINE (ออฟไลน์)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ค่าที่อ่านได้เริ่มต้น</label>
                        <input type="text" name="last_val" class="form-control" placeholder="เช่น NORMAL หรือ 0%">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-primary">บันทึกข้อมูล</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal 2: แก้ไขเซ็นเซอร์ -->
<div class="modal fade" id="editSensorModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editSensorForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold">✏️ แก้ไขข้อมูลเซ็นเซอร์</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label font-weight-bold">รหัสเซ็นเซอร์ (Code)</label>
                        <input type="text" id="edit_sensor_code" class="form-control bg-light" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label font-weight-bold">ชื่อเซ็นเซอร์ / อุปกรณ์ <span class="text-danger">*</span></label>
                        <input type="text" name="sensor_name" id="edit_sensor_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label font-weight-bold">ประเภทอุปกรณ์ <span class="text-danger">*</span></label>
                        <select name="sensor_type" id="edit_sensor_type" class="form-select" required>
                            <option value="mcu_sensor">MCU Sensor</option>
                            <option value="actuator">Actuator</option>
                            <option value="camera">Camera</option>
                            <option value="system">System</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label font-weight-bold">สถานะ <span class="text-danger">*</span></label>
                        <select name="status" id="edit_status" class="form-select" required>
                            <option value="ok">OK (ปกติ)</option>
                            <option value="warning">WARNING (เตือน)</option>
                            <option value="error">ERROR (ขัดข้อง)</option>
                            <option value="offline">OFFLINE (ออฟไลน์)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ค่าล่าสุดที่อ่านได้</label>
                        <input type="text" name="last_val" id="edit_last_val" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ข้อความแจ้งเตือนความผิดปกติ</label>
                        <input type="text" name="error_message" id="edit_error_message" class="form-control" placeholder="เช่น ถังขยะเต็ม หรือ เซอร์โวค้าง">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-primary">อัปเดตข้อมูล</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openEditModal(sensor) {
        document.getElementById('edit_sensor_code').value = sensor.sensor_code;
        document.getElementById('edit_sensor_name').value = sensor.sensor_name;
        document.getElementById('edit_sensor_type').value = sensor.sensor_type;
        document.getElementById('edit_status').value = sensor.status;
        document.getElementById('edit_last_val').value = sensor.last_val || '';
        document.getElementById('edit_error_message').value = sensor.error_message || '';

        // ตั้งค่า Action URL ของ Form
        const url = "{{ url('keptkayas/kiosks') }}/" + sensor.kiosk_id + "/sensors/" + sensor.id;
        document.getElementById('editSensorForm').action = url;

        // แสดง Modal
        const editModal = new bootstrap.Modal(document.getElementById('editSensorModal'));
        editModal.show();
    }
</script>
@endsection