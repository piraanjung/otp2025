@extends('layouts.admin1') 

@section('content') 
<!-- Leaflet CSS & JS CDN -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<div class="container-fluid py-4" style="max-width: 1000px;"> 
    <div class="card shadow-sm border-0 rounded-3"> 
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center"> 
            <h5 class="fw-bold text-dark mb-0">✏️ แก้ไขแนวท่อประปา: {{ $pipeline->name }}</h5> 
            <span class="badge bg-secondary">รหัส: {{ $pipeline->pipe_code }}</span> 
        </div> 
        <div class="card-body p-4"> 
            <form action="{{ route('tabwater.settings.pipelines.update', $pipeline->id) }}" method="POST"> 
                @csrf 
                @method('PUT') 

                <div class="row g-3 mb-3"> 
                    <div class="col-md-4"> 
                        <label class="form-label fw-bold text-secondary">เลือกสถานีผลิตน้ำ *</label> 
                        <select name="station_id" id="stationSelect" required class="form-select fw-bold"> 
                            <option value="">-- เลือกสถานี --</option> 
                            @foreach($stations as $s) 
                                <option value="{{ $s->id }}" 
                                        data-lat="{{ $s->lat }}" 
                                        data-lng="{{ $s->lng }}" 
                                        {{ old('station_id', $pipeline->station_id) ==$s->id ? 'selected' : '' }}>
                                    {{ $s->name }}
                                </option> 
                            @endforeach 
                        </select> 
                    </div> 
                    <div class="col-md-4"> 
                        <label class="form-label fw-bold text-secondary">รหัสแนวท่อ *</label> 
                        <input type="text" name="pipe_code" value="{{ old('pipe_code', $pipeline->pipe_code) }}" required class="form-control"> 
                    </div> 
                    <div class="col-md-4"> 
                        <label class="form-label fw-bold text-secondary">ชื่อแนวท่อ *</label> 
                        <input type="text" name="name" value="{{ old('name', $pipeline->name) }}" required class="form-control"> 
                    </div> 
                </div> 

                <div class="row g-3 mb-3"> 
                    <div class="col-md-4"> 
                        <label class="form-label fw-bold text-secondary">โหนดต้นทาง (From Node) *</label> 
                        <select name="from_node_id" id="fromNodeSelect" required class="form-select fw-bold"> 
                            <option value="">-- เลือกจุดต้นทาง --</option> 
                            
                            <optgroup label="🏭 โรงผลิตน้ำประปา">
                                @foreach($stations as $s)
                                    <option value="station_{{ $s->id }}" 
                                            data-station-id="{{ $s->id }}" 
                                            data-lat="{{ $s->lat }}" 
                                            data-lng="{{ $s->lng }}" 
                                            data-name="{{ $s->name }}"
                                            {{ old('from_node_id', $pipeline->from_node_id) == "station_".$s->id ? 'selected' : '' }}> 
                                        🏭 {{ $s->name }}
                                    </option>
                                @endforeach
                            </optgroup>

                            <optgroup label="📍 จุดโหนดและจุดแยก">
                                @foreach($nodes as $n) 
                                    <option value="{{ $n->id }}" 
                                            data-station-id="{{ $n->station_id }}" 
                                            data-lat="{{ $n->lat }}" 
                                            data-lng="{{ $n->lng }}" 
                                            data-name="{{ $n->name }}"
                                            {{ old('from_node_id', $pipeline->from_node_id) ==$n->id ? 'selected' : '' }}> 
                                        📍 {{ $n->name }} ({{$n->node_code }}) 
                                    </option> 
                                @endforeach 
                            </optgroup>
                        </select> 
                    </div> 
                    <div class="col-md-4"> 
                        <label class="form-label fw-bold text-secondary">โหนดปลายทาง (To Node) *</label> 
                        <select name="to_node_id" id="toNodeSelect" required class="form-select fw-bold"> 
                            <option value="">-- เลือกจุดปลายทาง --</option> 
                            
                            <optgroup label="🏭 โรงผลิตน้ำประปา">
                                @foreach($stations as $s)
                                    <option value="station_{{ $s->id }}" 
                                            data-station-id="{{ $s->id }}" 
                                            data-lat="{{ $s->lat }}" 
                                            data-lng="{{ $s->lng }}" 
                                            data-name="{{ $s->name }}"
                                            {{ old('to_node_id', $pipeline->to_node_id) == "station_".$s->id ? 'selected' : '' }}> 
                                        🏭 {{ $s->name }}
                                    </option>
                                @endforeach
                            </optgroup>

                            <optgroup label="📍 จุดโหนดและจุดแยก">
                                @foreach($nodes as $n) 
                                    <option value="{{ $n->id }}" 
                                            data-station-id="{{ $n->station_id }}" 
                                            data-lat="{{ $n->lat }}" 
                                            data-lng="{{ $n->lng }}" 
                                            data-name="{{ $n->name }}"
                                            {{ old('to_node_id', $pipeline->to_node_id) ==$n->id ? 'selected' : '' }}> 
                                        📍 {{ $n->name }} ({{$n->node_code }}) 
                                    </option> 
                                @endforeach 
                            </optgroup>
                        </select> 
                    </div> 
                    <div class="col-md-4"> 
                        <label class="form-label fw-bold text-secondary">ระดับชั้นท่อ *</label> 
                        @php $currentLvl = old('level',$pipeline->level); @endphp 
                        <select name="level" id="levelSelect" required class="form-select fw-bold"> 
                            <option value="main" {{ $currentLvl === 'main' ? 'selected' : '' }}>🔵 ท่อเมนหลัก (Main)</option> 
                            <option value="sub_main" {{ $currentLvl === 'sub_main' ? 'selected' : '' }}>🟢 ท่อประธาน (Sub Main)</option> 
                            <option value="distribution" {{ $currentLvl === 'distribution' ? 'selected' : '' }}>🟡 ท่อบริการ/ท่อซอย (Distribution)</option> 
                        </select> 
                    </div> 
                </div> 

                <div class="row g-3 mb-3"> 
                    <div class="col-md-4"> 
                        <label class="form-label fw-bold text-secondary">ขนาดท่อ (มม.) *</label> 
                        <input type="number" name="size_mm" value="{{ old('size_mm', $pipeline->size_mm) }}" required class="form-control"> 
                    </div> 
                    <div class="col-md-4"> 
                        <label class="form-label fw-bold text-secondary">วัสดุท่อ *</label> 
                        <input type="text" name="material" value="{{ old('material', $pipeline->material) }}" required class="form-control"> 
                    </div> 
                    <div class="col-md-4"> 
                        <label class="form-label fw-bold text-secondary">ความยาวท่อโดยประมาณ (เมตร)</label> 
                        <input type="number" step="0.1" id="lengthInput" name="length_m" value="{{ old('length_m', $pipeline->length_m) }}" class="form-control"> 
                    </div> 
                </div> 

                <!-- Leaflet Interactive Map --> 
                <div class="mb-3"> 
                    <div class="d-flex justify-content-between align-items-center mb-1"> 
                        <label class="form-label fw-bold text-primary mb-0"> 
                            🗺️ คลิกหมุดหรือคลิกบนถนนเพื่อปรับแต่งเส้นทางแนวท่อ: 
                        </label> 
                        <button type="button" id="btnClearLine" class="btn btn-outline-danger btn-sm py-0 font-bold" style="font-size: 11px;"> 
                            🗑️ ลบเส้นแล้ววาดใหม่ 
                        </button> 
                    </div> 
                    <div id="pipeMap" style="height: 420px; width: 100%; border-radius: 8px; border: 1px solid #dee2e6;" class="mb-2"></div> 
                    
                    <!-- Alert แจ้งเตือนสถานะการลากเส้น --> 
                    <div id="statusAlert" class="alert alert-info py-2 px-3 mb-0 font-bold small d-none"></div> 

                    <!-- Hidden JSON Field --> 
                    <input type="hidden" id="pathCoordsInput" name="path_coordinates" value="{{ old('path_coordinates', json_encode($pipeline->path_coordinates)) }}"> 
                </div> 

                <div class="d-flex justify-content-end gap-2"> 
                    <a href="{{ route('tabwater.settings.pipelines.index') }}" class="btn btn-light fw-bold">ยกเลิก</a> 
                    <button type="submit" class="btn btn-primary fw-bold">บันทึกการปรับปรุง</button> 
                </div> 
            </form> 
        </div> 
    </div> 
</div> 

<script> 
    let map, polyline; 
    const stationsData = @json($stationsMap ?? []); 
    const nodesData = @json($allNodesMap ?? []); 
    let rawPathCoords = @json($pipeline->path_coordinates ?? []); 

    // แปลงรูปแบบพิกัดเดิมให้อยู่ในโครงสร้าง {lat, lng}
    let pathCoords = rawPathCoords.map(p => ({
        lat: Number(p.lat || p[0]),
        lng: Number(p.lng || p.long || p[1])
    }));

    let isStartLocked = pathCoords.length > 0;
    let isEndLocked = pathCoords.length > 1;
    let nodeMarkers = [];

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

    const LINE_COLORS = { 
        main: '#0284c7', 
        sub_main: '#16a34a', 
        distribution: '#eab308' 
    };

    document.addEventListener("DOMContentLoaded", function() {
        initLeafletMap();
    });

    function initLeafletMap() { 
        let defaultLat = 17.154100; 
        let defaultLng = 104.148600; 

        if (pathCoords.length > 0) {
            defaultLat = pathCoords[0].lat;
            defaultLng = pathCoords[0].lng;
        } else if (stationsData.length > 0 && stationsData[0].lat) { 
            defaultLat = Number(stationsData[0].lat); 
            defaultLng = Number(stationsData[0].lng); 
        } 

        // 1. สร้าง Map ด้วย Leaflet
        map = L.map('pipeMap').setView([defaultLat, defaultLng], 15); 

        // 2. Tile Layer OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(map);

        // 3. วาดหมุดโรงผลิตน้ำ (Stations)
        stationsData.forEach(function(s) { 
            const lat = Number(s.lat); 
            const lng = Number(s.lng); 

            if (!isNaN(lat) && !isNaN(lng)) { 
                const marker = L.marker([lat, lng], { icon: MARKER_ICONS.station }).addTo(map);
                marker.bindTooltip(`<b>🏭 ${s.name}</b><br><span class="text-muted small">คลิกเพื่อเลือกเป็นจุดโหนด</span>`);

                marker.on('click', function() { 
                    handleNodeClick({ id: `station_${s.id}`, name: s.name, lat: lat, lng: lng, isStation: true }); 
                }); 
            } 
        }); 

        // 4. วาดหมุดจุดโหนด (Pipe Nodes)
        nodesData.forEach(function(n) { 
            const lat = Number(n.lat); 
            const lng = Number(n.lng); 

            if (!isNaN(lat) && !isNaN(lng)) { 
                const icon = MARKER_ICONS[n.type] || MARKER_ICONS.sub_junction;
                const marker = L.marker([lat, lng], { icon: icon }).addTo(map);
                marker.bindTooltip(`<b>📍 ${n.name} (${n.node_code})</b><br><span class="text-muted small">คลิกเพื่อเลือกเป็นจุดโหนด</span>`);

                nodeMarkers.push({
                    stationId: n.station_id,
                    marker: marker
                });

                marker.on('click', function() { 
                    handleNodeClick({ id: n.id, name: n.name, lat: lat, lng: lng, isStation: false }); 
                }); 
            } 
        }); 

        // 5. วาดเส้น Polyline เดิม
        const initialLevel = document.getElementById('levelSelect').value;
        const initialLatLngs = pathCoords.map(p => [p.lat, p.lng]);
        polyline = L.polyline(initialLatLngs, { 
            color: LINE_COLORS[initialLevel] || '#0284c7', 
            weight: 5, 
            opacity: 0.85 
        }).addTo(map);

        if (initialLatLngs.length > 0) {
            map.fitBounds(polyline.getBounds(), { padding: [30, 30] });
        }

        // 6. คลิกบนถนน/แผนที่อิสระ (Waypoints)
        map.on('click', function(e) { 
            if (!isStartLocked) { 
                alert('กรุณาคลิกเลือก "หมุดจุดโหนดเริ่มต้น (From Node)" บนแผนที่ก่อนเริ่มลากเส้นครับ'); 
                return; 
            } 

            if (isEndLocked) { 
                alert('เส้นท่อนี้เชื่อมต่อไปยังจุดโหนดปลายทางสมบูรณ์แล้ว หากต้องการปรับเปลี่ยนให้กดปุ่ม "ลบเส้นแล้ววาดใหม่"'); 
                return; 
            } 

            pathCoords.push({ lat: e.latlng.lat, lng: e.latlng.lng }); 
            updatePolylineAndJSON(); 
        }); 

        // เปลี่ยนสีเส้นตามระดับชั้นท่อ 
        document.getElementById('levelSelect').addEventListener('change', function() { 
            const lvl = this.value; 
            polyline.setStyle({ color: LINE_COLORS[lvl] || '#0284c7' }); 
        }); 

        // ปุ่มล้างเส้น 
        document.getElementById('btnClearLine').addEventListener('click', function() { 
            clearLine(); 
        }); 
    } 

    function handleNodeClick(nodeData, syncDropdown = true) { 
        const exactLat = Number(nodeData.lat); 
        const exactLng = Number(nodeData.lng); 

        if (!isStartLocked) { 
            pathCoords = [{ lat: exactLat, lng: exactLng }]; 
            isStartLocked = true; 

            if (syncDropdown) { 
                selectDropdownOption('fromNodeSelect', nodeData.id); 
            } 
            showStatusAlert(`📍 <b>จุดเริ่มต้น:</b> ${nodeData.name} (คลิกบนแผนที่เพื่อลากเส้นต่อ)`); 
        } 
        else if (!isEndLocked) { 
            const fromId = document.getElementById('fromNodeSelect').value; 
            if (fromId == nodeData.id) { 
                alert('จุดปลายทางต้องไม่ซ้ำกับจุดเริ่มต้นครับ'); 
                return; 
            } 

            pathCoords.push({ lat: exactLat, lng: exactLng }); 
            isEndLocked = true; 

            if (syncDropdown) { 
                selectDropdownOption('toNodeSelect', nodeData.id); 
            } 
            showStatusAlert(`🏁 <b>จุดสิ้นสุด:</b> ${nodeData.name} (เชื่อมต่อแนวท่อสมบูรณ์แล้ว)`, 'success'); 
        } 

        updatePolylineAndJSON(); 
    } 

    function updatePolylineAndJSON() { 
        const latLngs = pathCoords.map(p => [p.lat, p.lng]);
        polyline.setLatLngs(latLngs); 

        document.getElementById('pathCoordsInput').value = JSON.stringify(pathCoords); 

        if (pathCoords.length > 1) { 
            let totalDistMeters = 0; 
            for (let i = 0; i < pathCoords.length - 1; i++) { 
                const p1 = L.latLng(pathCoords[i].lat, pathCoords[i].lng); 
                const p2 = L.latLng(pathCoords[i+1].lat, pathCoords[i+1].lng); 
                totalDistMeters += p1.distanceTo(p2); 
            } 
            document.getElementById('lengthInput').value = totalDistMeters.toFixed(2); 
        } else { 
            document.getElementById('lengthInput').value = ''; 
        } 
    } 

    function selectDropdownOption(elementId, value) { 
        const select = document.getElementById(elementId); 
        if (select) { 
            select.value = value; 
        } 
    } 

    function showStatusAlert(msg, type = 'info') { 
        const alertBox = document.getElementById('statusAlert'); 
        alertBox.className = `alert alert-${type} py-2 px-3 mb-2 font-bold small`; 
        alertBox.innerHTML = msg; 
        alertBox.classList.remove('d-none'); 
    } 

    function clearLine() { 
        pathCoords = []; 
        isStartLocked = false; 
        isEndLocked = false; 
        polyline.setLatLngs([]); 
        document.getElementById('pathCoordsInput').value = '[]'; 
        document.getElementById('lengthInput').value = ''; 
        document.getElementById('fromNodeSelect').value = ''; 
        document.getElementById('toNodeSelect').value = ''; 
        document.getElementById('statusAlert').classList.add('d-none'); 
    } 
</script> 
@endsection