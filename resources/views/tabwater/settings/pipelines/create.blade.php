@extends('layouts.admin1') 

@section('content') 
<!-- Leaflet CSS & JS CDN -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<div class="container-fluid py-4" style="max-width: 1000px;"> 
    <div class="card shadow-sm border-0 rounded-3"> 
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center"> 
            <h5 class="fw-bold text-dark mb-0">➕ ลากวาดแนวท่อประปาใหม่ (Connected to Pipe Nodes - Leaflet.js)</h5> 
            <a href="{{ route('tabwater.settings.pipelines.index') }}" class="btn btn-light btn-sm fw-bold">⬅️ ย้อนกลับ</a> 
        </div> 
        <div class="card-body p-4"> 
            <form action="{{ route('tabwater.settings.pipelines.store') }}" method="POST"> 
                @csrf 
                <div class="row g-3 mb-3"> 
                    <div class="col-md-4"> 
                        <label class="form-label fw-bold text-secondary">เลือกสถานีผลิตน้ำ *</label> 
                        <select name="station_id" id="stationSelect" required class="form-select fw-bold"> 
                            <option value="">-- เลือกสถานี --</option> 
                            @foreach($stations as $s) 
                                <option value="{{ $s->id }}" data-lat="{{ $s->lat }}" data-lng="{{ $s->lng }}">{{ $s->name }}</option> 
                            @endforeach 
                        </select> 
                    </div> 
                    <div class="col-md-4"> 
                        <label class="form-label fw-bold text-secondary">รหัสแนวท่อ *</label> 
                        <input type="text" name="pipe_code" value="{{ old('pipe_code') }}" required placeholder="เช่น PIPE-M01" class="form-control"> 
                    </div> 
                    <div class="col-md-4"> 
                        <label class="form-label fw-bold text-secondary">ชื่อแนวท่อ *</label> 
                        <input type="text" name="name" value="{{ old('name') }}" required placeholder="เช่น ท่อเมนส่งน้ำดิบหลัก" class="form-control"> 
                    </div> 
                </div> 

                <div class="row g-3 mb-3"> 
                    <div class="col-md-4"> 
                        <label class="form-label fw-bold text-secondary">โหนดต้นทาง (From Node) *</label> 
                        <select name="from_node_id" id="fromNodeSelect" required class="form-select fw-bold"> 
                            <option value="">-- เลือกจุดต้นทาง --</option> 
                            
                            <!-- กลุ่มโรงผลิตน้ำประปา -->
                            <optgroup label="🏭 โรงผลิตน้ำประปา">
                                @foreach($stations as $s)
                                    <option value="station_{{ $s->id }}" data-station-id="{{ $s->id }}" data-lat="{{ $s->lat }}" data-lng="{{ $s->lng }}" data-name="{{ $s->name }}"> 
                                        🏭 {{ $s->name }}
                                    </option>
                                @endforeach
                            </optgroup>

                            <!-- กลุ่ม Pipe Nodes -->
                            <optgroup label="📍 จุดโหนดและจุดแยก">
                                @foreach($nodes as $n) 
                                    <option value="{{ $n->id }}" data-station-id="{{ $n->station_id }}" data-lat="{{ $n->lat }}" data-lng="{{ $n->lng }}" data-name="{{ $n->name }}"> 
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
                                    <option value="station_{{ $s->id }}" data-station-id="{{ $s->id }}" data-lat="{{ $s->lat }}" data-lng="{{ $s->lng }}" data-name="{{ $s->name }}"> 
                                        🏭 {{ $s->name }}
                                    </option>
                                @endforeach
                            </optgroup>

                            <optgroup label="📍 จุดโหนดและจุดแยก">
                                @foreach($nodes as $n) 
                                    <option value="{{ $n->id }}" data-station-id="{{ $n->station_id }}" data-lat="{{ $n->lat }}" data-lng="{{ $n->lng }}" data-name="{{ $n->name }}"> 
                                        📍 {{ $n->name }} ({{$n->node_code }}) 
                                    </option> 
                                @endforeach 
                            </optgroup>
                        </select> 
                    </div> 
                    <div class="col-md-4"> 
                        <label class="form-label fw-bold text-secondary">ระดับชั้นท่อ *</label> 
                        <select name="level" id="levelSelect" required class="form-select fw-bold"> 
                            <option value="main">🔵 ท่อเมนหลัก (Main)</option> 
                            <option value="sub_main">🟢 ท่อประธาน (Sub Main)</option> 
                            <option value="distribution">🟡 ท่อบริการ/ท่อซอย (Distribution)</option> 
                        </select> 
                    </div> 
                </div> 

                <div class="row g-3 mb-3"> 
                    <div class="col-md-4"> 
                        <label class="form-label fw-bold text-secondary">ขนาดท่อ (มม.) *</label> 
                        <input type="number" name="size_mm" value="{{ old('size_mm', 110) }}" required class="form-control"> 
                    </div> 
                    <div class="col-md-4"> 
                        <label class="form-label fw-bold text-secondary">วัสดุท่อ *</label> 
                        <input type="text" name="material" value="{{ old('material', 'PE') }}" required placeholder="เช่น PE, PVC" class="form-control"> 
                    </div> 
                    <div class="col-md-4"> 
                        <label class="form-label fw-bold text-secondary">ความยาวท่อโดยประมาณ (เมตร)</label> 
                        <input type="number" step="0.1" id="lengthInput" name="length_m" value="{{ old('length_m') }}" placeholder="คำนวณให้อัตโนมัติ" class="form-control"> 
                    </div> 
                </div> 

                <!-- Leaflet Interactive Map --> 
                <div class="mb-3"> 
                    <div class="d-flex justify-content-between align-items-center mb-1"> 
                        <label class="form-label fw-bold text-primary mb-0"> 
                            🗺️ คลิกหมุดเริ่มต้น ➔ คลิกบนถนนลากแนวท่อ ➔ คลิกหมุดปลายทางเพื่อจบเส้น: 
                        </label> 
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-light text-secondary border font-normal" style="font-size: 10px;">➖ เส้นจาง: แนวท่อเดิมในระบบ</span>
                            <button type="button" id="btnClearLine" class="btn btn-outline-danger btn-sm py-0 font-bold" style="font-size: 11px;"> 
                                🗑️ ล้างเส้นที่กำลังลากใหม่ 
                            </button> 
                        </div>
                    </div> 
                    <div id="pipeMap" style="height: 420px; width: 100%; border-radius: 8px; border: 1px solid #dee2e6;" class="mb-2"></div> 
                    
                    <!-- Alert แจ้งเตือนสถานะการลากเส้น --> 
                    <div id="statusAlert" class="alert alert-info py-2 px-3 mb-0 font-bold small d-none"></div> 

                    <!-- Hidden JSON Field เก็บพิกัดเส้น --> 
                    <input type="hidden" id="pathCoordsInput" name="path_coordinates" value="{{ old('path_coordinates', '[]') }}"> 
                </div> 

                <div class="d-flex justify-content-end gap-2"> 
                    <a href="{{ route('tabwater.settings.pipelines.index') }}" class="btn btn-light fw-bold">ยกเลิก</a> 
                    <button type="submit" class="btn btn-primary fw-bold">บันทึกแนวท่อ</button> 
                </div> 
            </form> 
        </div> 
    </div> 
</div> 

<script> 
    let map, activePolyline; 
    const stationsData = @json($stationsMap ?? []); 
    const nodesData = @json($allNodesMap ?? []); 
    const existingPipelinesData = @json($allPipelinesMap ?? []); // ข้อมูลแนวท่อเดิมทั้งหมดที่มีในระบบ

    let pathCoords = [];          // อาร์เรย์เก็บพิกัดเส้นที่กำลังลากใหม่ [{lat, lng}, ...] 
    let isStartLocked = false;    // สถานะว่าเลือก Node 1 แล้วหรือยัง 
    let isEndLocked = false;      // สถานะว่าเลือก Node 2 จบเส้นแล้วหรือยัง 
    let nodeMarkers = [];         // อาร์เรย์เก็บหมุด Pipe Nodes สำหรับกรอง
    let existingPipeLayers = [];  // อาร์เรย์เก็บ Layer เส้นแนวท่อเดิมในระบบ

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

        if (stationsData.length > 0 && stationsData[0].lat) { 
            defaultLat = Number(stationsData[0].lat); 
            defaultLng = Number(stationsData[0].lng); 
        } 

        // 1. สร้าง Map ด้วย Leaflet
        map = L.map('pipeMap').setView([defaultLat, defaultLng], 14); 

        // 2. ใช้ OpenStreetMap Tile
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(map);

        // 3. วาดเส้นแนวท่อเดิมในระบบ (Existing Pipelines - Read-only Lines)
        renderExistingPipelines();

        // 4. วาดหมุดโรงผลิตน้ำ (Stations)
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

        // 5. วาดหมุดจุดโหนด (Pipe Nodes)
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

        // 6. สร้าง Active Polyline สำหรับเส้นที่กำลังลากวาดใหม่ (สีเข้มชัดเจน)
        activePolyline = L.polyline([], { color: '#0284c7', weight: 5, opacity: 0.95 }).addTo(map);

        // 7. คลิกบนถนน/แผนที่อิสระ (Waypoints) 
        map.on('click', function(e) { 
            if (!isStartLocked) { 
                alert('กรุณาคลิกเลือก "หมุดจุดโหนดเริ่มต้น (From Node)" บนแผนที่ก่อนเริ่มลากเส้นครับ'); 
                return; 
            } 

            if (isEndLocked) { 
                alert('เส้นท่อนี้เชื่อมต่อไปยังจุดโหนดปลายทางสมบูรณ์แล้ว หากต้องการลากใหม่กรุณากด "ล้างเส้นที่กำลังลากใหม่"'); 
                return; 
            } 

            pathCoords.push({ lat: e.latlng.lat, lng: e.latlng.lng }); 
            updatePolylineAndJSON(); 
        }); 

        // เปลี่ยนสีเส้นใหม่ตามระดับชั้นท่อ 
        document.getElementById('levelSelect').addEventListener('change', function() { 
            const lvl = this.value; 
            const color = LINE_COLORS[lvl] || '#0284c7'; 
            activePolyline.setStyle({ color: color }); 
        }); 

        // เมื่อเลือก Dropdown From Node 
        document.getElementById('fromNodeSelect').addEventListener('change', function() { 
            const opt = this.options[this.selectedIndex]; 
            const lat = Number(opt.getAttribute('data-lat')); 
            const lng = Number(opt.getAttribute('data-lng')); 
            const name = opt.getAttribute('data-name'); 

            if (!isNaN(lat) && !isNaN(lng) && !isStartLocked) { 
                map.setView([lat, lng], 16); 
                handleNodeClick({ id: this.value, name: name, lat: lat, lng: lng }, false); 
            } 
        }); 

        // ปุ่มล้างเส้น 
        document.getElementById('btnClearLine').addEventListener('click', function() { 
            clearLine(); 
        }); 

        // เมื่อเปลี่ยนสถานีใน Dropdown หลัก -> กรองแนวท่อเดิมและโหนดเฉพาะสถานีนั้น
        document.getElementById('stationSelect').addEventListener('change', function() { 
            const stationId = this.value; 
            const selectedOption = this.options[this.selectedIndex]; 
            const sLat = Number(selectedOption.getAttribute('data-lat')); 
            const sLng = Number(selectedOption.getAttribute('data-lng')); 

            if (!isNaN(sLat) && !isNaN(sLng)) { 
                map.setView([sLat, sLng], 16); 
            } 

            clearLine(); 
            filterNodesAndPipelinesByStation(stationId); 
        }); 
    } 

    // ------------------------------------------------------------------- 
    // วาดแนวท่อเดิมที่มีอยู่ในระบบขึ้นแผนที่ (Read-only Reference Lines)
    // ------------------------------------------------------------------- 
    function renderExistingPipelines() {
        existingPipelinesData.forEach(function(pipe) {
            if (pipe.path_coordinates && pipe.path_coordinates.length > 0) {
                const pathLatLngs = pipe.path_coordinates.map(p => [
                    Number(p.lat || p[0]), 
                    Number(p.lng || p.long || p[1])
                ]);

                // สร้างเส้นแนวท่อเดิมด้วยสไตล์โปร่งแสง + เส้นประ เพื่อไม่ให้สับสนกับเส้นใหม่ที่กำลังลาก
                const line = L.polyline(pathLatLngs, {
                    color: LINE_COLORS[pipe.level] || '#6c757d',
                    weight: 3,
                    opacity: 0.45,
                    dashArray: '6, 6' // เส้นประอ้างอิง
                }).addTo(map);

                line.bindPopup(`
                    <div style="font-size: 12px; font-family: sans-serif;"> 
                        <span class="badge bg-secondary mb-1">🚰 แนวท่อที่มีอยู่เดิม</span><br>
                        <b style="color: #0284c7;">${pipe.name} (${pipe.pipe_code})</b><br> 
                        ขนาด: ${pipe.size_mm} มม. | ความยาว: ${pipe.length_m || '-'} ม. 
                    </div> 
                `);

                existingPipeLayers.push({
                    stationId: pipe.station_id,
                    layer: line
                });
            }
        });
    }

    // ------------------------------------------------------------------- 
    // ฟังก์ชันจัดการเมื่อคลิกที่หมุด Node บนแผนที่ 
    // ------------------------------------------------------------------- 
    function handleNodeClick(nodeData, syncDropdown = true) { 
        const exactLat = Number(nodeData.lat); 
        const exactLng = Number(nodeData.lng); 

        // สเต็ปที่ 1: กำหนดจุดเริ่มต้น (Start Node) 
        if (!isStartLocked) { 
            pathCoords = [{ lat: exactLat, lng: exactLng }]; 
            isStartLocked = true; 

            if (syncDropdown) { 
                selectDropdownOption('fromNodeSelect', nodeData.id); 
            } 
            showStatusAlert(`📍 <b>จุดเริ่มต้น:</b> ${nodeData.name} (คลิกบนแผนที่เพื่อลากเส้นต่อ)`); 
        } 
        // สเต็ปที่ 2: กำหนดจุดสิ้นสุด (End Node) 
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
            showStatusAlert(`🏁 <b>จุดสิ้นสุด:</b> ${nodeData.name} (เชื่อมต่อแนวท่อใหม่สมบูรณ์แล้ว)`, 'success'); 
        } 

        updatePolylineAndJSON(); 
    } 

    // ------------------------------------------------------------------- 
    // อัปเดต Polyline, JSON และคำนวณความยาว 
    // ------------------------------------------------------------------- 
    function updatePolylineAndJSON() { 
        const latLngs = pathCoords.map(p => [p.lat, p.lng]);
        activePolyline.setLatLngs(latLngs); 

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
        activePolyline.setLatLngs([]); 
        document.getElementById('pathCoordsInput').value = '[]'; 
        document.getElementById('lengthInput').value = ''; 
        document.getElementById('fromNodeSelect').value = ''; 
        document.getElementById('toNodeSelect').value = ''; 
        document.getElementById('statusAlert').classList.add('d-none'); 
    } 

    // กรองหมุด Pipe Nodes และแนวท่อเดิมบนแผนที่เมื่อเลือกสถานี
    function filterNodesAndPipelinesByStation(stationId) { 
        const fromSelect = document.getElementById('fromNodeSelect'); 
        const toSelect = document.getElementById('toNodeSelect'); 

        // 1. กรอง Dropdown
        [fromSelect, toSelect].forEach(select => { 
            select.value = ''; 
            Array.from(select.options).forEach(opt => { 
                if (opt.value === '') return; 
                const nodeStationId = opt.getAttribute('data-station-id'); 
                if (!stationId || nodeStationId == stationId) { 
                    opt.style.display = ''; 
                } else { 
                    opt.style.display = 'none'; 
                } 
            }); 
        }); 

        // 2. กรองหมุดโหนด
        nodeMarkers.forEach(item => { 
            if (!stationId || item.stationId == stationId) { 
                if (!map.hasLayer(item.marker)) map.addLayer(item.marker); 
            } else { 
                if (map.hasLayer(item.marker)) map.removeLayer(item.marker); 
            } 
        }); 

        // 3. กรองเส้นแนวท่อเดิมในระบบ (แสดงเฉพาะของสถานีที่เลือก)
        existingPipeLayers.forEach(item => {
            if (!stationId || item.stationId == stationId) {
                if (!map.hasLayer(item.layer)) map.addLayer(item.layer);
            } else {
                if (map.hasLayer(item.layer)) map.removeLayer(item.layer);
            }
        });
    } 
</script> 
@endsection