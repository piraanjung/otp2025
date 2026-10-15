@extends('layouts.admin1')

@section('content')
<div class="container-fluid py-4" style="max-width: 900px;">
    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-header bg-white py-3 border-bottom">
            <h5 class="fw-bold text-dark mb-0">➕ เพิ่มสถานีผลิตน้ำประปา</h5>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('tabwater.settings.stations.store') }}" method="POST">
                @csrf
                <input type="hidden" name="org_id_fk" value="1">

                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-secondary">รหัสสถานี</label>
                        <input type="text" name="code" value="{{ old('code') }}" placeholder="เช่น ST-01" class="form-control">
                    </div>
                    <div class="col-md-8">
                        <label class="form-label fw-bold text-secondary">ชื่อสถานีผลิตน้ำ <span class="text-danger">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" required placeholder="เช่น สถานีผลิตน้ำบ้านหนองขาม" class="form-control">
                    </div>
                </div>

                <!-- ส่วน Google Maps ปักหมุด GIS -->
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="form-label fw-bold text-secondary mb-0">
                            🗺️ พิกัดสถานีผลิตน้ำ (คลิกบนแผนที่เพื่อปักหมุด):
                        </label>
                        <button type="button" id="btnCurrentLoc" class="btn btn-outline-primary btn-sm py-0 font-bold" style="font-size: 11px;">
                            🎯 ใช้ตำแหน่งปัจจุบันของฉัน
                        </button>
                    </div>

                    <!-- กำหนด height และ style แบบ inline การันตีการแสดงผล -->
                    <div id="googleMap" style="height: 320px; width: 100%; border-radius: 8px; border: 1px solid #dee2e6;" class="mb-2"></div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text fw-bold bg-light">Lat</span>
                                <input type="text" id="latInput" name="lat" value="{{ old('lat', $org->lat) }}" readonly class="form-control fw-bold text-primary">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text fw-bold bg-light">Lng</span>
                                <input type="text" id="lngInput" name="lng" value="{{ old('long', $org->long) }}" readonly class="form-control fw-bold text-primary">
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="my-4 text-muted">
                <h6 class="fw-bold text-primary mb-3">⚙️ การตั้งค่าเกณฑ์วิกฤตเตือนภัย (Dynamic Thresholds)</h6>

                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-danger">ความขุ่นวิกฤต (Max NTU)</label>
                        <input type="number" step="0.1" name="max_turbidity_ntu" value="{{ old('max_turbidity_ntu', 5.0) }}" required class="form-control fw-bold text-danger">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-secondary">pH ต่ำสุด (Min pH)</label>
                        <input type="number" step="0.1" name="min_ph" value="{{ old('min_ph', 6.5) }}" required class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-secondary">pH สูงสุด (Max pH)</label>
                        <input type="number" step="0.1" name="max_ph" value="{{ old('max_ph', 8.5) }}" required class="form-control">
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-primary">ระดับน้ำใสตัดปั๊ม (%)</label>
                        <input type="number" step="1" name="max_water_level_percent" value="{{ old('max_water_level_percent', 95) }}" required class="form-control fw-bold text-primary">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-secondary">ระดับน้ำเตือนเร่งสูบ (%)</label>
                        <input type="number" step="1" name="min_water_level_percent" value="{{ old('min_water_level_percent', 30) }}" required class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-secondary">TDS สูงสุด (PPM)</label>
                        <input type="number" step="1" name="max_tds_ppm" value="{{ old('max_tds_ppm', 300) }}" required class="form-control">
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-success">K-Factor ไม้จุ่ม (NTU = K / cm)</label>
                        <input type="number" step="1" name="secchi_k_factor" value="{{ old('secchi_k_factor', 350) }}" required class="form-control fw-bold text-success">
                    </div>
                </div>

                <hr class="my-4 text-muted">
                <div class="mb-4">
                    <label class="form-label fw-bold text-secondary d-block">เลือกช่างผู้รับผิดชอบประจำสถานี</label>
                    <div class="border rounded p-3 bg-light" style="max-height: 150px; overflow-y: auto;">
                        <div class="row g-2">
                            @foreach($staffs as $s)
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="staff_ids[]" value="{{ $s->id }}" id="staff_{{ $s->id }}">
                                        <label class="form-check-label text-secondary fw-semibold" for="staff_{{ $s->id }}">
                                            {{ $s->user->firstname." ".$s->user->lastname }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('tabwater.settings.stations.index') }}" class="btn btn-light fw-bold">ยกเลิก</a>
                    <button type="submit" class="btn btn-primary fw-bold">บันทึกข้อมูล</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- โหลด Google Maps API ตรงๆ ในหน้า -->
 <script async defer
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA-5AlIGzLhFXErl2STRT6GacX0616iW2o&callback=initMap"></script>

<script>
    let map, marker;

    function initMap() {
        const latInput = document.getElementById('latInput');
        const lngInput = document.getElementById('lngInput');

        const defaultLat = parseFloat(latInput.value) || 17.154100;
        const defaultLng = parseFloat(lngInput.value) || 104.148600;
        const initialPos = { lat: defaultLat, lng: defaultLng };

        // 1. สร้างแผนที่
        map = new google.maps.Map(document.getElementById('googleMap'), {
            center: initialPos,
            zoom: 14,
            mapTypeControl: true,
            streetViewControl: false,
        });

        // 2. สร้างหมุดปัก
        marker = new google.maps.Marker({
            position: initialPos,
            map: map,
            draggable: true,
            title: "ลากเพื่อย้ายตำแหน่ง"
        });

        function updateLocation(lat, lng) {
            latInput.value = lat.toFixed(6);
            lngInput.value = lng.toFixed(6);
        }

        // คลิกแผนที่เพื่อย้ายหมุด
        map.addListener('click', function(e) {
            marker.setPosition(e.latLng);
            updateLocation(e.latLng.lat(), e.latLng.lng());
        });

        // ลากหมุด
        marker.addListener('dragend', function(e) {
            updateLocation(e.latLng.lat(), e.latLng.lng());
        });

        // ปุ่มดึง GPS
        document.getElementById('btnCurrentLoc').addEventListener('click', function() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    const pos = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                    };
                    map.setCenter(pos);
                    map.setZoom(16);
                    marker.setPosition(pos);
                    updateLocation(pos.lat, pos.lng);
                });
            }
        });
    }
</script>
@endsection