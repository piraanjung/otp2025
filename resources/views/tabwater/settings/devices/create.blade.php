@extends('layouts.admin1')

@section('content')
<div class="container-fluid py-4" style="max-width: 1000px;">
    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <h5 class="fw-bold text-dark mb-0">➕ เพิ่มอุปกรณ์/ปั๊มใหม่ (เพิ่มพร้อมกันหลายรายการ)</h5>
            <a href="{{ route('tabwater.settings.devices.index') }}" class="btn btn-light btn-sm fw-bold">⬅️ ย้อนกลับ</a>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('tabwater.settings.devices.store') }}" method="POST">
                @csrf

                <!-- เลือกสถานีผลิตน้ำหลัก -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-secondary">เลือกสถานีผลิตน้ำ *</label>
                        <select name="station_id" required class="form-select fw-bold border-primary">
                            <option value="">-- เลือกสถานีผลิตน้ำ --</option>
                            @foreach($stations as $s)
                                <option value="{{ $s->id }}" {{ old('station_id') == $s->id ? 'selected' : '' }}>
                                    🏭 {{ $s->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <hr class="text-muted my-3">

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold text-primary mb-0">📦 รายการอุปกรณ์ที่ต้องการเพิ่ม</h6>
                    <button type="button" id="btnAddDevice" class="btn btn-outline-success btn-sm fw-bold">
                        ➕ เพิ่มอุปกรณ์อีกรายการ
                    </button>
                </div>

                <!-- Container สำหรับรายการอุปกรณ์ (Dynamic Rows) -->
                <div id="deviceListContainer">
                    <!-- Row 0 (เริ่มต้น) -->
                    <div class="device-row card bg-light border-0 p-3 mb-3 position-relative rounded-3" data-index="0">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge bg-primary fw-bold row-number">อุปกรณ์ที่ #1</span>
                            <button type="button" class="btn-close btn-remove-row d-none" aria-label="Close"></button>
                        </div>

                        <div class="row g-3 mb-2">
                            <div class="col-md-3">
                                <label class="form-label fw-bold text-secondary text-xs">รหัสอุปกรณ์ *</label>
                                <input type="text" name="devices[0][device_code]" required placeholder="เช่น PUMP-RAW-01" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold text-secondary text-xs">ชื่ออุปกรณ์ *</label>
                                <input type="text" name="devices[0][name]" required placeholder="เช่น ปั๊มน้ำดิบ ตัวที่ 1" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold text-secondary text-xs">ประเภทอุปกรณ์ *</label>
                                <select name="devices[0][type]" required class="form-select form-select-sm">
                                    <option value="pump_raw">ปั๊มน้ำดิบ (Raw Water Pump)</option>
                                    <option value="pump_clear">ปั๊มน้ำใส (Clear Water Pump)</option>
                                    <option value="dosing_pump">ปั๊มเคมี (Dosing Pump)</option>
                                    <option value="electric_valve">วาล์วไฟฟ้า (Electric Valve)</option>
                                    <option value="relay">Relay สวิตช์ทั่วไป</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold text-secondary text-xs">โหมดเริ่มต้น *</label>
                                <select name="devices[0][control_mode]" required class="form-select form-select-sm fw-bold">
                                    <option value="auto">🤖 Auto (ระบบอัตโนมัติ)</option>
                                    <option value="manual">🖐️ Manual (ช่างควบคุมเอง)</option>
                                </select>
                            </div>
                        </div>

                        <!-- ส่วนข้อมูล IoT Connection -->
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-secondary text-xs">📡 MQTT Command Topic</label>
                                <input type="text" name="devices[0][mqtt_topic]" placeholder="เช่น station1/pump1/command" class="form-control form-control-sm font-monospace">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold text-secondary text-xs">Relay Channel (ถ้ามี)</label>
                                <input type="number" name="devices[0][relay_channel]" placeholder="1" class="form-control form-control-sm">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                    <a href="{{ route('tabwater.settings.devices.index') }}" class="btn btn-light fw-bold">ยกเลิก</a>
                    <button type="submit" class="btn btn-primary fw-bold px-4">💾 บันทึกอุปกรณ์ทั้งหมด</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        let deviceIndex = 1;
        const container = document.getElementById('deviceListContainer');
        const btnAdd = document.getElementById('btnAddDevice');

        // ฟังก์ชันเพิ่มรายการอุปกรณ์ใหม่
        btnAdd.addEventListener('click', function () {
            const firstRow = container.querySelector('.device-row');
            const newRow = firstRow.cloneNode(true);

            // อัปเดต Index สำหรับ attribute name
            newRow.setAttribute('data-index', deviceIndex);
            
            // ล้างค่าข้อมูลเดิมใน input
            newRow.querySelectorAll('input').forEach(input => {
                input.value = '';
                const name = input.getAttribute('name');
                if (name) {
                    input.setAttribute('name', name.replace(/\[\d+\]/, `[${deviceIndex}]`));
                }
            });

            // อัปเดต attribute name ใน select
            newRow.querySelectorAll('select').forEach(select => {
                const name = select.getAttribute('name');
                if (name) {
                    select.setAttribute('name', name.replace(/\[\d+\]/, `[${deviceIndex}]`));
                }
            });

            // เปิดให้แสดงปุ่มลบ
            const removeBtn = newRow.querySelector('.btn-remove-row');
            removeBtn.classList.remove('d-none');

            container.appendChild(newRow);
            deviceIndex++;

            updateRowNumbers();
        });

        // ฟังก์ชันลบรายการอุปกรณ์
        container.addEventListener('click', function (e) {
            if (e.target.classList.contains('btn-remove-row')) {
                const rows = container.querySelectorAll('.device-row');
                if (rows.length > 1) {
                    e.target.closest('.device-row').remove();
                    updateRowNumbers();
                }
            }
        });

        // อัปเดตตัวเลขอุปกรณ์ที่ #1, #2, #3
        function updateRowNumbers() {
            const rows = container.querySelectorAll('.device-row');
            rows.forEach((row, idx) => {
                row.querySelector('.row-number').textContent = `อุปกรณ์ที่ #${idx + 1}`;
                
                // ซ่อนปุ่มลบถ้าเหลือแถวเดียว
                const removeBtn = row.querySelector('.btn-remove-row');
                if (rows.length === 1) {
                    removeBtn.classList.add('d-none');
                } else {
                    removeBtn.classList.remove('d-none');
                }
            });
        }
    });
</script>
@endsection