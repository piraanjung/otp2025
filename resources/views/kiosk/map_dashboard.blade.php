@extends('layouts.admin1')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h3 mb-0 text-gray-800">แผนที่พิกัดตู้คืนขวดอัตโนมัติ</h1>
            <p class="text-muted small mb-0">หน่วยงาน: {{Auth::user()->organization->orgType->name. Auth::user()->organization->org_name ?? 'ไม่ระบุหน่วยงาน' }}</p>
        </div>
        <a href="{{ route('keptkayas.kiosks.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-list-ul"></i> ดูรายการแบบตาราง
        </a>
       
    </div>

    <!-- Map & Summary Layout -->
    <div class="row g-3">
        <!-- ฝั่งซ้าย: แผนที่ Leaflet -->
        <div class="col-lg-9">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-2">
                    <div id="kioskMap" style="height: 400px; width: 100%; class="rounded-3""></div>
                </div>
            </div>
        </div>

        <!-- ฝั่งขวา: รายชื่อตู้และไฟสถานะ -->
        <div class="col-lg-3">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white py-3">
                    <h6 class="fw-bold mb-0"><i class="bi bi-geo-fill text-danger me-1"></i>รายการตู้ในแผนที่ ({{ $kiosks->count() }} ตู้)</h6>
                </div>
                <div class="card-body p-0" style="max-height: 540px; overflow-y: auto;">
                    <div class="list-group list-group-flush">
                        @forelse($kiosks as $kiosk)
                            <button type="button" 
                                    class="list-group-item list-group-item-action p-3 d-flex justify-content-between align-items-start"
                                    onclick="focusKiosk({{ $kiosk->lat }}, {{ $kiosk->lng }}, '{{$kiosk->id }}')">
                                <div>
                                    <strong class="d-block text-primary">{{ $kiosk->name }}</strong>
                                    <small class="text-muted d-block">ID: {{ $kiosk->id }}</small>
                                    <small class="text-muted"><i class="bi bi-recycle me-1"></i>ขวดสะสม: {{ number_format($kiosk->total_waste_count) }} ขวด</small>
                                </div>
                                <span class="badge bg-{{ $kiosk->status == 'offline' ? 'secondary' : ($kiosk->status == 'full' ? 'danger' : 'success') }}">
                                    {{ strtoupper($kiosk->status) }}
                                </span>
                            </button>
                        @empty
                            <div class="p-3 text-center text-muted">ไม่พบข้อมูลตู้ในหน่วยงานนี้</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ========================================================= --}}
    {{-- ส่วนที่ 6: การ์ดแสดงสถานะตู้ Kiosk และเซ็นเซอร์ประจำตู้ --}}
    {{-- ========================================================= --}}
    <div class="row mt-4">
        <div class="col-12 mb-4">
            <div class="card z-index-2">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h6><i class="ni ni-tv-2 text-primary me-2"></i>สถานะตู้คืนขวด Kiosk และเซ็นเซอร์ประจำตู้</h6>
                     <a href="{{ route('keptkayas.kiosks.sensors.alerts') }}" class="btn btn-danger">
    🚨 เฝ้าระวังเซ็นเซอร์ขัดข้อง
</a>
                    <span class="badge bg-gradient-info">จำนวนตู้ทั้งหมด {{ isset($kiosks) ? $kiosks->count() : 0 }} ตู้</span>
                </div>
                <div class="card-body p-3">
                    <div class="row">
                        @forelse($kiosks ?? [] as $kiosk)
                            @php
                                $maxCapacity = $kiosk->max_bottle_capacity ?? 500;
                                $currentCount = $kiosk->current_bottle_count ?? 0;
                                $percent = $maxCapacity > 0 ? min(100, round(($currentCount / $maxCapacity) * 100)) : 0;
                                
                                // กำหนดสี Progress Bar ตามปริมาณขวดในถัง
                                $progressClass = 'bg-gradient-success';
                                if ($percent >= 90) {
                                    $progressClass = 'bg-gradient-danger';
                                } elseif ($percent >= 75) {
                                    $progressClass = 'bg-gradient-warning';
                                }
                            @endphp
                            <div class="col-lg-4 col-md-6 col-12 mb-4">
                                <div class="card border shadow-none h-100">
                                    <div class="card-header pb-2 d-flex justify-content-between align-items-center bg-transparent border-bottom">
                                        <div>
                                            <h6 class="mb-0 font-weight-bold">{{ $kiosk->name }}</h6>
                                            <small class="text-xs text-muted">ID: {{ $kiosk->id }}</small>
                                        </div>
                                        <span class="badge badge-sm bg-{{ $kiosk->status == 'offline' ? 'secondary' : ($percent >= 90 ? 'danger' : 'success') }}">
                                            {{ strtoupper($kiosk->status ?? 'OFFLINE') }}
                                        </span>
                                    </div>
                                    <div class="card-body p-3">
                                        <!-- สถานะความจุขวดในตู้ -->
                                        <div class="mb-3">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <span class="text-xs font-weight-bold">ความจุขวดในถัง</span>
                                                <span class="text-xs font-weight-bold">{{ number_format($currentCount) }} / {{ number_format($maxCapacity) }} ขวด ({{ $percent }}%)</span>
                                            </div>
                                            <div class="progress" style="height: 8px;">
                                                <div class="progress-bar {{ $progressClass }}" role="progressbar" style="width: {{ $percent }}%;" aria-valuenow="{{ $percent }}" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </div>

                                        <!-- สถานะเซ็นเซอร์ย่อยประจำตู้ -->
                                        <div class="border-top pt-2">
                                            <span class="text-xxs font-weight-bold text-uppercase text-secondary d-block mb-2">สถานะเซ็นเซอร์ & อุปกรณ์</span>
                                            <ul class="list-group list-group-flush">
                                                @forelse($kiosk->sensors ?? [] as $sensor)
                                                    <li class="list-group-item px-0 py-1 d-flex justify-content-between align-items-center border-0 text-xs">
                                                        <span class="text-truncate" style="max-width: 180px;">
                                                            <i class="ni ni-settings me-1 text-secondary"></i> {{ $sensor->sensor_name }}
                                                        </span>
                                                        @php
                                                            $sBadge = match($sensor->status ?? 'ok') {
                                                                'ok' => 'bg-success',
                                                                'warning' => 'bg-warning text-dark',
                                                                'error' => 'bg-danger',
                                                                default => 'bg-secondary'
                                                            };
                                                        @endphp
                                                        <span class="badge badge-sm {{ $sBadge }}">{{ strtoupper($sensor->status ?? 'OK') }}</span>
                                                    </li>
                                                @empty
                                                    <li class="list-group-item px-0 py-1 text-xs text-muted border-0 text-center">ยังไม่ได้ลงทะเบียนเซ็นเซอร์</li>
                                                <a href="{{ route('keptkayas.kiosks.sensors.index', $kiosk->id) }}" class="btn btn-info">ลงทะเบียนเซ็นเซอร์</a>
                                                @endforelse
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center py-4 text-muted">
                                ไม่พบข้อมูลตู้ Kiosk ในหน่วยงานนี้
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

<!-- Leaflet CSS & JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        // ดึงข้อมูล kiosks จาก PHP ส่งมายัง JavaScript
        const kiosks = @json($kiosks);

        // ค่าพิกัดเริ่มต้น (กรุงเทพฯ หรือเลือกตู้แรกในรายการ)
        let defaultLat = 13.7563;
        let defaultLng = 100.5018;

        if (kiosks.length > 0 && kiosks[0].lat && kiosks[0].lng) {
            defaultLat = parseFloat(kiosks[0].lat);
            defaultLng = parseFloat(kiosks[0].lng);
        }

        // สร้าง Map Object
        window.map = L.map('kioskMap').setView([defaultLat, defaultLng], 15);

        // ดึง OpenStreetMap Tile Layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap contributors'
        }).addTo(window.map);

        window.markers = {};

        // วนลูปสร้าง Marker ปักหมุดตามพิกัดแต่ละตู้
        kiosks.forEach(kiosk => {
            if (kiosk.lat && kiosk.lng) {
                const lat = parseFloat(kiosk.lat);
                const lng = parseFloat(kiosk.lng);

                // กำหนดสีหมุดตามสถานะ
                let statusBadge = '<span class="badge bg-success">พร้อมใช้งาน</span>';
                if (kiosk.status === 'offline') {
                    statusBadge = '<span class="badge bg-secondary">ออฟไลน์</span>';
                } else if (kiosk.status === 'full') {
                    statusBadge = '<span class="badge bg-danger">ถังเต็ม</span>';
                }

                const popupContent = `
                    <div style="min-width: 180px;">
                        <h6 style="margin: 0 0 5px 0; font-weight: bold;">${kiosk.name}</h6>
                        <p style="margin: 0; font-size: 12px; color: #666;">ID: ${kiosk.id}</p>
                        <hr style="margin: 6px 0;">
                        <p style="margin: 0 0 4px 0; font-size: 13px;">สถานะ: ${statusBadge}</p>
                        <p style="margin: 0; font-size: 13px;">ขวดสะสม: <b>${kiosk.total_waste_count}</b> ขวด</p>
                    </div>
                `;

                const marker = L.marker([lat, lng])
                    .addTo(window.map)
                    .bindPopup(popupContent);

                window.markers[kiosk.id] = marker;
            }
        });
    });

    // ฟังก์ชั่นเมื่อกดเลือกลิสต์รายการตู้ฝั่งขวา ให้แผนที่ซูมไปหาตู้ใบนั้น
    function focusKiosk(lat, lng, kioskId) {
        if (window.map && window.markers[kioskId]) {
            window.map.setView([lat, lng], 16, { animate: true });
            window.markers[kioskId].openPopup();
        }
    }
</script>
@endsection