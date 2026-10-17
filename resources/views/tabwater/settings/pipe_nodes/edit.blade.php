@extends('layouts.admin1') 

@section('content') 
<!-- Leaflet CSS & JS CDN -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<div class="container-fluid py-4" style="max-width: 900px;"> 
    <div class="card shadow-sm border-0 rounded-3"> 
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center"> 
            <h5 class="fw-bold text-dark mb-0">✏️ แก้ไขจุดโหนด: {{ $node->name }}</h5> 
            <span class="badge bg-secondary">รหัส: {{ $node->node_code }}</span> 
        </div> 
        <div class="card-body p-4"> 
            <form action="{{ route('tabwater.settings.pipe-nodes.update', $node->id) }}" method="POST"> 
                @csrf 
                @method('PUT') 

                <div class="row g-3 mb-3"> 
                    <div class="col-md-6"> 
                        <label class="form-label fw-bold text-secondary">สถานีผลิตน้ำต้นทาง *</label> 
                        <select name="station_id" required class="form-select fw-bold"> 
                            <option value="">-- เลือกสถานี --</option> 
                            @foreach($stations as $s) 
                                <option value="{{ $s->id }}" {{ old('station_id', $node->station_id) == $s->id ? 'selected' : '' }}>{{ $s->name }}</option> 
                            @endforeach 
                        </select> 
                    </div> 
                    <div class="col-md-6"> 
                        <label class="form-label fw-bold text-secondary">รหัสโหนด *</label> 
                        <input type="text" name="node_code" value="{{ old('node_code', $node->node_code) }}" required class="form-control"> 
                    </div> 
                </div> 

                <div class="row g-3 mb-3"> 
                    <div class="col-md-6"> 
                        <label class="form-label fw-bold text-secondary">ชื่อจุดโหนด *</label> 
                        <input type="text" id="nodeNameInput" name="name" value="{{ old('name', $node->name) }}" required class="form-control"> 
                    </div> 
                    <div class="col-md-6"> 
                        <label class="form-label fw-bold text-secondary">ประเภทโหนด *</label> 
                        @php $type = old('type',$node->type); @endphp 
                        <select name="type" id="nodeTypeSelect" required class="form-select fw-bold"> 
                            <option value="main_junction" {{ $type === 'main_junction' ? 'selected' : '' }}>จุดแยกท่อเมนหลัก</option> 
                            <option value="sub_junction" {{ $type === 'sub_junction' ? 'selected' : '' }}>จุดแยกท่อรอง/ท่อซอย</option> 
                            <option value="valve_point" {{ $type === 'valve_point' ? 'selected' : '' }}>จุดติดตั้งวาล์ว</option> 
                            <option value="tank" {{ $type === 'tank' ? 'selected' : '' }}>ถังพักน้ำ/หอถังสูง</option> 
                            <option value="end_point" {{ $type === 'end_point' ? 'selected' : '' }}>จุดปลายท่อ/บ้านผู้ใช้</option> 
                            <option value="station" {{ $type === 'station' ? 'selected' : '' }}>โรงผลิตน้ำต้นทาง</option> 
                        </select> 
                    </div> 
                </div> 

                <!-- Leaflet.js ปักหมุดย้ายพิกัดโหนด --> 
                <div class="mb-3"> 
                    <label class="form-label fw-bold text-primary mb-1">🗺️ ปรับย้ายตำแหน่งพิกัดจุดโหนดบนแผนที่:</label> 
                    <div id="nodeMap" style="height: 320px; width: 100%; border-radius: 8px; border: 1px solid #dee2e6;" class="mb-2"></div> 
                    
                    <div class="row g-3"> 
                        <div class="col-md-6"> 
                            <div class="input-group input-group-sm"> 
                                <span class="input-group-text fw-bold bg-light">Lat</span> 
                                <input type="text" id="latInput" name="lat" value="{{ old('lat', $node->lat) }}" readonly class="form-control fw-bold text-primary"> 
                            </div> 
                        </div> 
                        <div class="col-md-6"> 
                            <div class="input-group input-group-sm"> 
                                <span class="input-group-text fw-bold bg-light">Lng</span> 
                                <input type="text" id="lngInput" name="lng" value="{{ old('lng', $node->lng ?? $node->long) }}" readonly class="form-control fw-bold text-primary"> 
                            </div> 
                        </div> 
                    </div> 
                </div> 

                <div class="mb-4"> 
                    <label class="form-label fw-bold text-secondary">หมายเหตุเพิ่มเติม</label> 
                    <textarea name="remark" rows="2" class="form-control">{{ old('remark', $node->remark) }}</textarea> 
                </div> 

                <div class="d-flex justify-content-end gap-2"> 
                    <a href="{{ route('tabwater.settings.pipe-nodes.index') }}" class="btn btn-light fw-bold">ยกเลิก</a> 
                    <button type="submit" class="btn btn-primary fw-bold">บันทึกการปรับปรุง</button> 
                </div> 
            </form> 
        </div> 
    </div> 
</div> 

<script> 
    let map, marker; 

    function createCustomIcon(iconUrl) {
        return L.icon({
            iconUrl: iconUrl,
            iconSize: [28, 28],
            iconAnchor: [14, 28],
            popupAnchor: [0, -28]
        });
    }

    const MARKER_ICONS = { 
        station: createCustomIcon('{{ asset("imgs/tower.png") }}'), 
        main_junction: createCustomIcon('{{ asset("imgs/fire_hydrant.png") }}'), 
        sub_junction: createCustomIcon('{{ asset("imgs/pump.png") }}'), 
        valve_point: createCustomIcon('https://maps.google.com/mapfiles/ms/icons/red-dot.png'), 
        tank: createCustomIcon('https://maps.google.com/mapfiles/ms/icons/purple-dot.png'), 
        end_point: createCustomIcon('https://maps.google.com/mapfiles/ms/icons/yellow-dot.png'), 
    }; 

    document.addEventListener("DOMContentLoaded", function() {
        initLeafletMap();
    });

    function initLeafletMap() { 
        const latInput = document.getElementById('latInput'); 
        const lngInput = document.getElementById('lngInput'); 
        const typeSelect = document.getElementById('nodeTypeSelect');
        const nameInput = document.getElementById('nodeNameInput');

        const defaultLat = parseFloat(latInput.value) || 17.154100; 
        const defaultLng = parseFloat(lngInput.value) || 104.148600; 

        // 1. สร้าง Leaflet Map
        map = L.map('nodeMap').setView([defaultLat, defaultLng], 16); 

        // 2. Tile Layer OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(map);

        // 3. ดึงไอคอนตามประเภทโหนด
        const currentType = typeSelect.value;
        const initialIcon = MARKER_ICONS[currentType] || MARKER_ICONS.sub_junction;

        // 4. สร้าง Draggable Marker
        marker = L.marker([defaultLat, defaultLng], { 
            icon: initialIcon, 
            draggable: true 
        }).addTo(map);

        function getPopupContent(lat, lng) {
            const nodeName = nameInput.value.trim() !== '' ? nameInput.value : 'จุดโหนด';
            return `
                <div style="font-size: 12px; font-family: sans-serif;">
                    <b>📍 ${nodeName}</b><br>
                    <span style="color: #6c757d;">Lat: ${lat.toFixed(6)}, Lng: ${lng.toFixed(6)}</span>
                </div>
            `;
        }

        function updateInputs(lat, lng) { 
            latInput.value = lat.toFixed(6); 
            lngInput.value = lng.toFixed(6); 
            marker.bindPopup(getPopupContent(lat, lng));
        } 

        updateInputs(defaultLat, defaultLng);

        // เมื่อลากหมุด
        marker.on('dragend', function(e) { 
            const pos = marker.getLatLng();
            updateInputs(pos.lat, pos.lng); 
            marker.openPopup();
        }); 

        // เมื่อคลิกบนแผนที่
        map.on('click', function(e) { 
            marker.setLatLng(e.latlng); 
            updateInputs(e.latlng.lat, e.latlng.lng); 
        }); 

        // เมื่อเปลี่ยนประเภทโหนดใน Dropdown ให้เปลี่ยนไอคอนหมุดตาม
        typeSelect.addEventListener('change', function() {
            const newType = this.value;
            const newIcon = MARKER_ICONS[newType] || MARKER_ICONS.sub_junction;
            marker.setIcon(newIcon);
        });

        // เมื่อพิมพ์เปลี่ยนชื่อโหนด ให้ป๊อปอัปอัปเดตตาม
        nameInput.addEventListener('input', function() {
            const pos = marker.getLatLng();
            marker.bindPopup(getPopupContent(pos.lat, pos.lng));
        });
    } 
</script> 
@endsection