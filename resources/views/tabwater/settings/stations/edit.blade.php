@extends('layouts.admin1')

@section('content')
<div class="container-fluid py-4" style="max-width: 900px;">
    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
            <h5 class="fw-bold text-dark mb-0">✏️ แก้ไขสถานีผลิตน้ำประปา: {{ $station->name }}</h5>
            <span class="badge bg-secondary">ID: {{ $station->id }}</span>
        </div>
        <div class="card-body p-4">
            @if(session('error'))
                <div class="alert alert-danger font-bold text-sm mb-3">{{ session('error') }}</div>
            @endif

            <form action="{{ route('tabwater.settings.stations.update', $station->id) }}" method="POST">
                @csrf
                @method('PUT')

                <input type="hidden" name="org_id_fk" value="{{ $station->org_id_fk }}">

                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-secondary">รหัสสถานี</label>
                        <input type="text" name="code" value="{{ old('code', $station->code) }}" placeholder="เช่น ST-01" class="form-control">
                    </div>
                    <div class="col-md-8">
                        <label class="form-label fw-bold text-secondary">ชื่อสถานีผลิตน้ำ <span class="text-danger">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $station->name) }}" required placeholder="เช่น สถานีผลิตน้ำบ้านหนองขาม" class="form-control">
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

                    <!-- พื้นที่ Google Maps -->
                    <div id="googleMap" style="height: 320px; width: 100%; border-radius: 8px; border: 1px solid #dee2e6;" class="mb-2"></div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text fw-bold bg-light">Lat</span>
                                <input type="text" id="latInput" name="lat" value="{{ old('lat', $station->lat ?? $org->lat) }}" readonly class="form-control fw-bold text-primary">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text fw-bold bg-light">Lng</span>
                                <input type="text" id="lngInput" name="long" value="{{ old('long', $station->lng ?? $org->long) }}" readonly class="form-control fw-bold text-primary">
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="my-4 text-muted">
                <h6 class="fw-bold text-primary mb-3">⚙️ การตั้งค่าเกณฑ์วิกฤตเตือนภัย (Dynamic Thresholds)</h6>

                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-danger">ความขุ่นวิกฤต (Max NTU)</label>
                        <input type="number" step="0.1" name="max_turbidity_ntu" value="{{ old('max_turbidity_ntu', $station->max_turbidity_ntu) }}" required class="form-control fw-bold text-danger">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-secondary">pH ต่ำสุด (Min pH)</label>
                        <input type="number" step="0.1" name="min_ph" value="{{ old('min_ph', $station->min_ph) }}" required class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-secondary">pH สูงสุด (Max pH)</label>
                        <input type="number" step="0.1" name="max_ph" value="{{ old('max_ph', $station->max_ph) }}" required class="form-control">
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-primary">ระดับน้ำใสตัดปั๊ม (%)</label>
                        <input type="number" step="1" name="max_water_level_percent" value="{{ old('max_water_level_percent', $station->max_water_level_percent) }}" required class="form-control fw-bold text-primary">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-secondary">ระดับน้ำเตือนเร่งสูบ (%)</label>
                        <input type="number" step="1" name="min_water_level_percent" value="{{ old('min_water_level_percent', $station->min_water_level_percent) }}" required class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-secondary">TDS สูงสุด (PPM)</label>
                        <input type="number" step="1" name="max_tds_ppm" value="{{ old('max_tds_ppm', $station->max_tds_ppm) }}" required class="form-control">
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-success">K-Factor ไม้จุ่ม (NTU = K / cm)</label>
                        <input type="number" step="1" name="secchi_k_factor" value="{{ old('secchi_k_factor', $station->secchi_k_factor) }}" required class="form-control fw-bold text-success">
                    </div>
                </div>

                <hr class="my-4 text-muted">
                <div class="mb-4">
                    <label class="form-label fw-bold text-secondary d-block">เลือกช่างผู้รับผิดชอบประจำสถานี</label>
                    @php
                        $assignedStaffIds = old('staff_ids', $station->staffs->pluck('id')->toArray());
                    @endphp
                    <div class="border rounded p-3 bg-light" style="max-height: 150px; overflow-y: auto;">
                        <div class="row g-2">
                            @foreach($staffs as $s)
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="staff_ids[]" value="{{ $s->id }}" id="staff_{{ $s->id }}" {{ in_array($s->id, $assignedStaffIds) ? 'checked' : '' }}>
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
                    <button type="submit" class="btn btn-primary fw-bold">บันทึกการปรับปรุง</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- โหลด Google Maps API -->
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA-5AlIGzLhFXErl2STRT6GacX0616iW2o&callback=initMap"></script>
<script>
    let map, marker, infoWindow;

    function initMap() {
        const latInput = document.getElementById('latInput');
        const lngInput = document.getElementById('lngInput');
        const nameInput = document.querySelector('input[name="name"]');

        const defaultLat = parseFloat(latInput.value) || 17.154100;
        const defaultLng = parseFloat(lngInput.value) || 104.148600;
        const initialPos = { lat: defaultLat, lng: defaultLng };

        // 1. สร้างแผนที่
        map = new google.maps.Map(document.getElementById('googleMap'), {
            center: initialPos,
            zoom: 15,
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

        // 3. สร้าง InfoWindow สำหรับแสดงป๊อปอัป
        infoWindow = new google.maps.InfoWindow();

        // ฟังก์ชันสร้างเนื้อหา HTML ในป๊อปอัป
        function getInfoWindowContent(lat, lng) {
            const stationName = (nameInput && nameInput.value.trim() !== '') ? nameInput.value : 'สถานีผลิตน้ำประปา';
            return `
                <div style="padding: 5px; font-family: sans-serif; font-size: 12px; line-height: 1.4;">
                    <div style="font-weight: bold; color: #0d6efd; font-size: 13px; margin-bottom: 4px;">
                        🏭 ${stationName}
                    </div>
                    <div style="color: #495057;">
                        <b>Lat:</b> ${lat.toFixed(6)}<br>
                        <b>Lng:</b> ${lng.toFixed(6)}
                    </div>
                    <div style="color: #6c757d; font-size: 11px; margin-top: 4px;">
                        (คลิกหรือลากหมุดเพื่อเปลี่ยนตำแหน่ง)
                    </div>
                </div>
            `;
        }

        // ฟังก์ชันอัปเดตตำแหน่งลงช่อง Input
        function updateLocation(lat, lng) {
            latInput.value = lat.toFixed(6);
            lngInput.value = lng.toFixed(6);
            
            // อัปเดตเนื้อหาป๊อปอัปตามพิกัดใหม่
            infoWindow.setContent(getInfoWindowContent(lat, lng));
        }

        // ตั้งค่าเนื้อหาป๊อปอัปเริ่มต้น
        infoWindow.setContent(getInfoWindowContent(defaultLat, defaultLng));

        // -------------------------------------------------------------
        // Event Listeners สำหรับแสดงป๊อปอัปเมื่อเอาเมาส์ชี้หมุด (Mouseover / Mouseout)
        // -------------------------------------------------------------

        // 1. เอาเมาส์ชี้หมุด -> แสดงป๊อปอัป
        marker.addListener('mouseover', function() {
            infoWindow.open(map, marker);
        });

        // 2. เอาเมาส์ออกจากหมุด -> ซ่อนป๊อปอัป
        marker.addListener('mouseout', function() {
            infoWindow.close();
        });

        // 3. เมื่อลากหมุด -> ปรับพิกัด
        marker.addListener('dragend', function(e) {
            const lat = e.latLng.lat();
            const lng = e.latLng.lng();
            updateLocation(lat, lng);
            infoWindow.open(map, marker); // แสดงป๊อปอัปตำแหน่งใหม่
        });

        // 4. เมื่อคลิกบนแผนที่ -> ย้ายหมุดมาจุดที่คลิก
        map.addListener('click', function(e) {
            const lat = e.latLng.lat();
            const lng = e.latLng.lng();
            marker.setPosition(e.latLng);
            updateLocation(lat, lng);
        });

        // 5. ปุ่มดึง GPS ตำแหน่งปัจจุบัน
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
                    infoWindow.open(map, marker);
                });
            }
        });

        // 6. เมื่อมีการพิมพ์ชื่อสถานี ให้ป๊อปอัปอัปเดตชื่อตามทันที
        if (nameInput) {
            nameInput.addEventListener('input', function() {
                const currentPos = marker.getPosition();
                infoWindow.setContent(getInfoWindowContent(currentPos.lat(), currentPos.lng()));
            });
        }
    }
</script>
@endsection