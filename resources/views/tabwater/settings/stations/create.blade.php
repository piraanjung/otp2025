@extends('layouts.admin1') 

@section('content') 
<!-- Leaflet CSS & JS CDN -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<div class="container-fluid py-4" style="max-width: 900px;"> 
    <div class="card shadow-sm border-0 rounded-3"> 
        <div class="card-header bg-white py-3 border-bottom"> 
            <h5 class="fw-bold text-dark mb-0">➕ เพิ่มสถานีผลิตน้ำประปา (Leaflet.js)</h5> 
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
                    
                    <div id="leafletMap" style="height: 320px; width: 100%; border-radius: 8px; border: 1px solid #dee2e6;" class="mb-2"></div> 
                    
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

        const defaultLat = parseFloat(latInput.value) || 17.154100; 
        const defaultLng = parseFloat(lngInput.value) || 104.148600; 

        // 1. สร้าง Leaflet Map
        map = L.map('leafletMap').setView([defaultLat, defaultLng], 14); 

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

        function updateLocation(lat, lng) { 
            latInput.value = lat.toFixed(6); 
            lngInput.value = lng.toFixed(6); 
        } 

        // คลิกแผนที่เพื่อย้ายหมุด
        map.on('click', function(e) { 
            marker.setLatLng(e.latlng); 
            updateLocation(e.latlng.lat, e.latlng.lng); 
        }); 

        // ลากหมุดเพื่ออัปเดต Lat/Lng
        marker.on('dragend', function(e) { 
            const position = marker.getLatLng(); 
            updateLocation(position.lat, position.lng); 
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
                }, function(err) {
                    alert('ไม่สามารถดึงตำแหน่งพิกัด GPS ปัจจุบันได้: ' + err.message);
                }); 
            } else {
                alert('เบราว์เซอร์นี้ไม่รองรับการดึงพิกัด GPS');
            }
        }); 
    } 
</script> 
@endsection