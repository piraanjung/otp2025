@extends('layouts.admin1') 

@section('content') 
<!-- Leaflet CSS & JS CDN -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

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

                <!-- ส่วน Leaflet.js ปักหมุด GIS --> 
                <div class="mb-3"> 
                    <div class="d-flex justify-content-between align-items-center mb-1"> 
                        <label class="form-label fw-bold text-secondary mb-0"> 
                            🗺️ พิกัดสถานีผลิตน้ำ (คลิกบนแผนที่เพื่อปักหมุด): 
                        </label> 
                        <button type="button" id="btnCurrentLoc" class="btn btn-outline-primary btn-sm py-0 font-bold" style="font-size: 11px;"> 
                            🎯 ใช้ตำแหน่งปัจจุบันของฉัน 
                        </button> 
                    </div> 
                    
                    <!-- พื้นที่ Leaflet Map --> 
                    <div id="leafletMap" style="height: 320px; width: 100%; border-radius: 8px; border: 1px solid #dee2e6;" class="mb-2"></div> 
                    
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
                        $assignedStaffIds = old('staff_ids',$station->staffs->pluck('id')->toArray()); 
                    @endphp 
                    <div class="border rounded p-3 bg-light" style="max-height: 150px; overflow-y: auto;"> 
                        <div class="row g-2"> 
                            @foreach($staffs as $s) 
                                <div class="col-md-4"> 
                                    <div class="form-check"> 
                                        <input class="form-check-input" type="checkbox" name="staff_ids[]" value="{{ $s->id }}" id="staff_{{ $s->id }}" {{ in_array($s->id,$assignedStaffIds) ? 'checked' : '' }}> 
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

<script> 
    let map, marker; 

    function createStationIcon() {
        return L.icon({
            iconUrl: '{{ asset("imgs/tower.png") }}',
            iconSize: [32, 32],
            iconAnchor: [16, 32],
            popupAnchor: [0, -32]
        });
    }

    document.addEventListener("DOMContentLoaded", function() {
        initLeafletMap();
    });

    function initLeafletMap() { 
        const latInput = document.getElementById('latInput'); 
        const lngInput = document.getElementById('lngInput'); 
        const nameInput = document.querySelector('input[name="name"]'); 

        const defaultLat = parseFloat(latInput.value) || 17.154100; 
        const defaultLng = parseFloat(lngInput.value) || 104.148600; 

        // 1. สร้าง Leaflet Map
        map = L.map('leafletMap').setView([defaultLat, defaultLng], 15); 

        // 2. Tile Layer OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(map);

        // 3. สร้าง Draggable Marker
        marker = L.marker([defaultLat, defaultLng], { 
            icon: createStationIcon(), 
            draggable: true 
        }).addTo(map);

        // ฟังก์ชันสร้างเนื้อหา HTML ป๊อปอัป
        function getPopupContent(lat, lng) { 
            const stationName = (nameInput && nameInput.value.trim() !== '') ? nameInput.value : 'สถานีผลิตน้ำประปา'; 
            return ` 
                <div style="padding: 4px; font-family: sans-serif; font-size: 12px; line-height: 1.4;"> 
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

        // อัปเดตตำแหน่งลงช่อง Input และป๊อปอัป
        function updateLocation(lat, lng) { 
            latInput.value = lat.toFixed(6); 
            lngInput.value = lng.toFixed(6); 
            marker.bindPopup(getPopupContent(lat, lng)); 
        } 

        // ตั้งค่า Popup เริ่มต้น
        updateLocation(defaultLat, defaultLng);

        // เมื่อลากหมุด
        marker.on('dragend', function(e) { 
            const position = marker.getLatLng(); 
            updateLocation(position.lat, position.lng); 
            marker.openPopup();
        }); 

        // เมื่อคลิกบนแผนที่
        map.on('click', function(e) { 
            marker.setLatLng(e.latlng); 
            updateLocation(e.latlng.lat, e.latlng.lng); 
        }); 

        // ปุ่มดึง GPS ตำแหน่งปัจจุบัน
        document.getElementById('btnCurrentLoc').addEventListener('click', function() { 
            if (navigator.geolocation) { 
                navigator.geolocation.getCurrentPosition(function(position) { 
                    const lat = position.coords.latitude; 
                    const lng = position.coords.longitude; 
                    
                    map.setView([lat, lng], 16); 
                    marker.setLatLng([lat, lng]); 
                    updateLocation(lat, lng); 
                    marker.openPopup();
                }, function(err) {
                    alert('ไม่สามารถดึงพิกัด GPS ปัจจุบันได้: ' + err.message);
                }); 
            } else {
                alert('เบราว์เซอร์นี้ไม่รองรับการดึงพิกัด GPS');
            }
        }); 

        // เมื่อมีการพิมพ์เปลี่ยนชื่อสถานี
        if (nameInput) { 
            nameInput.addEventListener('input', function() { 
                const pos = marker.getLatLng(); 
                marker.bindPopup(getPopupContent(pos.lat, pos.lng)); 
            }); 
        } 
    } 
</script> 
@endsection