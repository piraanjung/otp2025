@extends('layouts.admin1')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1">🚰 แผงควบคุมอุปกรณ์และปั๊มน้ำ (Pump Control Panel)</h4>
            <p class="text-muted small mb-0">สถานี: <b class="text-primary">{{ $station->name ?? 'โรงผลิตน้ำหมู่ 19' }}</b></p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-success-subtle text-success border border-success-subtle fw-bold px-3 py-2">
                🟢 สถานะบอร์ด IoT: Online
            </span>
        </div>
    </div>

    <!-- Grid แสดงการ์ดปั๊มและอุปกรณ์ทั้งหมด -->
    <div class="row g-4">
        @foreach($devices as $device)
            <div class="col-md-6 col-lg-4">
                <div class="card shadow-sm border-0 rounded-3 h-100 device-card" id="device-card-{{ $device->id }}">
                    <div class="card-body p-4 d-flex flex-column justify-content-between">
                        
                        <!-- Header การ์ด: ชื่อ และ Badge สถานะ -->
                        <div>
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle fw-bold mb-1">
                                        {{ strtoupper($device->type) }}
                                    </span>
                                    <h5 class="fw-bold text-dark mb-0">{{ $device->name }}</h5>
                                    <small class="text-muted font-monospace">{{ $device->device_code }}</small>
                                </div>
                                
                                <!-- Indicator ไฟแสดงสถานะการทำงาน (ติด/ดับ) -->
                                <span id="status-badge-{{ $device->id }}" class="badge {{ $device->status ? 'bg-success' : 'bg-secondary' }} p-2 rounded-circle shadow-sm" style="width: 14px; height: 14px;" title="{{ $device->status ? 'กำลังทำงาน (ON)' : 'ปิดการทำงาน (OFF)' }}"></span>
                            </div>

                            <hr class="text-muted my-3">

                            <!-- ข้อมูลโหมดและ MQTT Topic -->
                            <div class="space-y-1 text-xs text-secondary mb-3">
                                <div><b>📡 Topic:</b> <span class="font-monospace text-dark">{{ $device->mqtt_topic ?? '-' }}</span></div>
                                <div><b>⚡ Relay:</b> <span class="fw-bold text-dark">Ch. {{ $device->relay_channel ?? '1' }}</span></div>
                            </div>
                        </div>

                        <!-- Footer การ์ด: โหมดควบคุม และ ปุ่ม Toggle ON/OFF -->
                        <div class="pt-3 border-top">
                            <!-- เลือกโหมด Auto / Manual -->
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="fw-bold text-secondary text-xs">โหมดการทำงาน:</span>
                                <div class="btn-group btn-group-sm" role="group">
                                    <button type="button" 
                                            class="btn {{ $device->control_mode === 'auto' ? 'btn-primary fw-bold' : 'btn-outline-secondary' }}"
                                            onclick="changeMode({{ $device->id }}, 'auto')">
                                        🤖 Auto
                                    </button>
                                    <button type="button" 
                                            class="btn {{ $device->control_mode === 'manual' ? 'btn-warning fw-bold' : 'btn-outline-secondary' }}"
                                            onclick="changeMode({{ $device->id }}, 'manual')">
                                        🖐️ Manual
                                    </button>
                                </div>
                            </div>

                            <!-- ปุ่มสวิตช์เปิด-ปิด (Toggle Switch) -->
                            <div class="d-grid">
                                <button type="button" 
                                        id="btn-toggle-{{ $device->id }}"
                                        class="btn {{ $device->status ? 'btn-danger' : 'btn-success' }} btn-lg fw-bold shadow-sm py-2"
                                        {{ $device->control_mode === 'auto' ? 'disabled' : '' }}
                                        onclick="toggleDevice({{ $device->id }}, {{$device->status ? 0 : 1 }})">
                                    <span id="btn-text-{{ $device->id }}">
                                        {{ $device->status ? '⏹️ สั่งปิดปั๊ม (OFF)' : '▶️ สั่งเปิดปั๊ม (ON)' }}
                                    </span>
                                </button>
                            </div>
                            
                            <small id="auto-warning-{{ $device->id }}" class="text-muted text-center d-block mt-2 font-bold {{ $device->control_mode === 'auto' ? '' : 'd-none' }}" style="font-size: 11px;">
                                ⚠️ อยู่ในโหมด Auto (ระบบอัตโนมัติจะควบคุมเอง)
                            </small>
                        </div>

                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<script>
    // ฟังก์ชันสั่ง เปิด-ปิด ปั๊ม (Toggle Switch)
    function toggleDevice(deviceId, targetStatus) {
        const btn = document.getElementById(`btn-toggle-${deviceId}`);
        const btnText = document.getElementById(`btn-text-${deviceId}`);
        
        btn.disabled = true;
        btnText.innerText = '⏳ กำลังส่งคำสั่งไปยังบอร์ด...';

        fetch(`/tabwater/control/devices/${deviceId}/toggle`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ status: targetStatus })
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            if (data.success) {
                const statusBadge = document.getElementById(`status-badge-${deviceId}`);
                
                if (data.status == 1) {
                    btn.className = 'btn btn-danger btn-lg fw-bold shadow-sm py-2';
                    btnText.innerText = '⏹️ สั่งปิดปั๊ม (OFF)';
                    btn.setAttribute('onclick', `toggleDevice(${deviceId}, 0)`);
                    statusBadge.className = 'badge bg-success p-2 rounded-circle shadow-sm';
                } else {
                    btn.className = 'btn btn-success btn-lg fw-bold shadow-sm py-2';
                    btnText.innerText = '▶️ สั่งเปิดปั๊ม (ON)';
                    btn.setAttribute('onclick', `toggleDevice(${deviceId}, 1)`);
                    statusBadge.className = 'badge bg-secondary p-2 rounded-circle shadow-sm';
                }
            } else {
                alert('เกิดข้อผิดพลาดในการส่งคำสั่ง: ' + data.message);
            }
        })
        .catch(err => {
            btn.disabled = false;
            btnText.innerText = 'เกิดข้อผิดพลาด';
            alert('ไม่สามารถติดต่อเซิร์ฟเวอร์ได้');
        });
    }

    // ฟังก์ชันเปลี่ยนโหมด Auto / Manual
    function changeMode(deviceId, mode) {
        fetch(`/tabwater/control/devices/${deviceId}/mode`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ control_mode: mode })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                location.reload(); // รีโหลดเพื่ออัปเดตสถานะปุ่ม
            }
        });
    }
</script>
@endsection