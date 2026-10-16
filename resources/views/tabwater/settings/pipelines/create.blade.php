@extends('layouts.admin1') 

@section('content') 
<!-- Leaflet CSS & JS CDN -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<div class="container-fluid py-4" style="max-width: 1000px;"> 
    <div class="card shadow-sm border-0 rounded-3"> 
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center"> 
            <h5 class="fw-bold text-dark mb-0">➕ ลากวาดแนวท่อประปาใหม่ (Manual GPS Waypoint Survey)</h5> 
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
                        <label class="form-label fw-bold text-secondary">โหนดปลายทาง (To Node) *</label> 
                        <select name="to_node_id" id="toNodeSelect" required class="form-select fw-bold border-primary"> 
                            <option value="">-- เลือกจุดปลายทาง --</option> 
                            <option value="NEW_NODE_ON_THE_FLY" class="text-primary fw-bold">➕ [สร้างจุดโหนดใหม่ ณ ตำแหน่งปัจจุบัน/จุดสิ้นสุด]</option>
                            
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

                <!-- Leaflet Interactive Map & Manual GPS Controls --> 
                <div class="mb-3"> 
                    <!-- แถบปุ่มกดดึงพิกัด GPS หน้างาน -->
                    <div class="card bg-light border-0 mb-2 p-2">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                            <div class="d-flex align-items-center gap-2">
                                <span class="fw-bold text-dark text-xs">🚶‍♂️ สำรวจหน้างาน:</span>
                                <button type="button" id="btnAddGpsPoint" class="btn btn-success btn-sm fw-bold shadow-sm">
                                    📍 ปักจุดพิกัดปัจจุบัน (GPS)
                                </button>
                                <button type="button" id="btnUndoGpsPoint" class="btn btn-outline-warning btn-sm font-bold" style="font-size: 11px;">
                                    ↩️ ย้อนกลับ 1 จุด
                                </button>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span id="gpsAccuracyBadge" class="badge bg-secondary d-none">GPS Accuracy: -</span>
                                <button type="button" id="btnClearLine" class="btn btn-outline-danger btn-sm py-0 font-bold" style="font-size: 11px;"> 
                                    🗑️ ล้างเส้นทั้งหมด
                                </button> 
                            </div>
                        </div>
                    </div>

                    <div id="pipeMap" style="height: 420px; width: 100%; border-radius: 8px; border: 1px solid #dee2e6;" class="mb-2"></div> 
                    
                    <!-- Alert แจ้งเตือนสถานะการลากเส้น --> 
                    <div id="statusAlert" class="alert alert-info py-2 px-3 mb-2 font-bold small d-none"></div> 

                    <!-- 📋 ตารางแสดงพิกัดที่กดบันทึกไว้อย่างละเอียด -->
                    <div class="card border-0 shadow-sm mb-2">
                        <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
                            <span class="fw-bold text-secondary text-xs">📋 รายการพิกัดแนวท่อที่บันทึกไว้ (<span id="pointCountText">0</span> จุด)</span>
                            <span class="text-muted text-xs">คลิกที่แถวเพื่อเลื่อนซูมไปจุดนั้นบนแผนที่</span>
                        </div>
                        <div class="card-body p-0" style="max-height: 200px; overflow-y: auto;">
                            <table class="table table-sm table-hover align-middle mb-0 text-xs">
                                <thead class="table-light sticky-top">
                                    <tr>
                                        <th style="width: 60px;" class="text-center">ลำดับ</th>
                                        <th>ประเภท / รายละเอียดจุด</th>
                                        <th>ละติจูด (Latitude)</th>
                                        <th>ลองจิจูด (Longitude)</th>
                                        <th style="width: 80px;" class="text-end">จัดการ</th>
                                    </tr>
                                </thead>
                                <tbody id="coordsTableBody">
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-3">ยังไม่มีพิกัดแนวท่อ กรุณาเลือกจุดโหนดเริ่มต้นหรือปักพิกัด GPS</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

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

<!-- Modal สำหรับกรอกข้อมูล Pipe Node ใหม่หน้างาน -->
<div class="modal fade" id="newFlyNodeModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white py-2">
                <h6 class="modal-title fw-bold">📍 บันทึกสร้างจุดโหนดใหม่หน้างาน</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-bold text-secondary">ชื่อจุดโหนดใหม่ *</label>
                    <input type="text" id="flyNodeName" class="form-control" placeholder="เช่น จุดแยกซอย 4 หรือ วาล์วประตูน้ำหลัก">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold text-secondary">ประเภทโหนด *</label>
                    <select id="flyNodeType" class="form-select fw-bold">
                        <option value="sub_junction">🔀 จุดแยก / ข้อต่อ (Junction)</option>
                        <option value="valve_point">🛑 วาล์วเปิด-ปิดน้ำ (Valve)</option>
                        <option value="main_junction">🔥 หัวดับเพลิง (Fire Hydrant)</option>
                        <option value="end_point">📍 จุดสิ้นสุดปลายท่อ (End Point)</option>
                        <option value="tank">🪣 ถังพักน้ำ / หอถังสูง (Tank)</option>
                    </select>
                </div>
                <div class="p-2 bg-light rounded text-xs text-muted mb-2">
                    🌐 <b>พิกัดที่จะบันทึก:</b> <span id="flyNodeCoordsText">กำลังดึงจากจุดพิกัดล่าสุด...</span>
                </div>
            </div>
            <div class="modal-footer py-2">
                <button type="button" class="btn btn-light btn-sm fw-bold" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="button" id="btnSaveFlyNode" class="btn btn-primary btn-sm fw-bold">💾 ยืนยันสร้างโหนดและจบเส้นท่อ</button>
            </div>
        </div>
    </div>
</div>

<script> 
    let map, activePolyline; 
    let waypointMarkers = []; 
    
    const stationsData = @json($stationsMap ?? []); 
    const nodesData = @json($allNodesMap ?? []); 
    const existingPipelinesData = @json($allPipelinesMap ?? []); 

    let pathCoords = [];          
    let isStartLocked = false;    
    let isEndLocked = false;      
    let nodeMarkers = [];         
    let existingPipeLayers = [];  

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
        initManualGpsEvents();
        initFlyNodeEvents();
    });

    function initLeafletMap() { 
        let defaultLat = 17.154100; 
        let defaultLng = 104.148600; 

        if (stationsData.length > 0 && stationsData[0].lat) { 
            defaultLat = Number(stationsData[0].lat); 
            defaultLng = Number(stationsData[0].lng); 
        } 

        map = L.map('pipeMap').setView([defaultLat, defaultLng], 14); 

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(map);

        renderExistingPipelines();

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

        activePolyline = L.polyline([], { color: '#0284c7', weight: 5, opacity: 0.95 }).addTo(map);

        map.on('click', function(e) { 
            if (!isStartLocked) { 
                alert('กรุณาเลือก "โหนดต้นทาง" บนแผนที่หรือใน Dropdown ก่อนเริ่มวางเส้นครับ'); 
                return; 
            } 

            if (isEndLocked) { 
                alert('แนวท่อเชื่อมต่อสมบูรณ์แล้ว หากต้องการลากใหม่กรุณากด "ล้างเส้นทั้งหมด"'); 
                return; 
            } 

            addWaypointPoint(e.latlng.lat, e.latlng.lng, 'คลิกบนแผนที่');
        }); 

        document.getElementById('levelSelect').addEventListener('change', function() { 
            const lvl = this.value; 
            activePolyline.setStyle({ color: LINE_COLORS[lvl] || '#0284c7' }); 
        }); 

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

        document.getElementById('toNodeSelect').addEventListener('change', function() {
            if (this.value === 'NEW_NODE_ON_THE_FLY') return;

            const opt = this.options[this.selectedIndex];
            const lat = Number(opt.getAttribute('data-lat'));
            const lng = Number(opt.getAttribute('data-lng'));
            const name = opt.getAttribute('data-name');

            if (!isNaN(lat) && !isNaN(lng) && isStartLocked && !isEndLocked) {
                handleNodeClick({ id: this.value, name: name, lat: lat, lng: lng }, false);
            }
        });

        document.getElementById('btnClearLine').addEventListener('click', function() { 
            clearLine(); 
        }); 

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

    function initManualGpsEvents() {
        const btnAddGps = document.getElementById('btnAddGpsPoint');
        const btnUndoGps = document.getElementById('btnUndoGpsPoint');
        const accuracyBadge = document.getElementById('gpsAccuracyBadge');

        btnAddGps.addEventListener('click', function() {
            const fromSelect = document.getElementById('fromNodeSelect');

            if (!fromSelect.value || !isStartLocked) {
                alert('⚠️ กรุณาเลือก "โหนดต้นทาง" ใน Dropdown ก่อนปักจุด GPS หน้างานครับ!');
                return;
            }

            if (isEndLocked) {
                alert('แนวท่อถูกเชื่อมต่อสมบูรณ์แล้ว หากต้องการลากใหม่กรุณากด "ล้างเส้นทั้งหมด"');
                return;
            }

            if (!navigator.geolocation) {
                alert('อุปกรณ์หรือเบราว์เซอร์นี้ไม่รองรับ GPS Geolocation');
                return;
            }

            btnAddGps.disabled = true;
            btnAddGps.innerHTML = '⏳ กำลังอ่านพิกัด...';

            navigator.geolocation.getCurrentPosition(
                function(pos) {
                    btnAddGps.disabled = false;
                    btnAddGps.innerHTML = '📍 ปักจุดพิกัดปัจจุบัน (GPS)';

                    const lat = pos.coords.latitude;
                    const lng = pos.coords.longitude;
                    const accuracy = pos.coords.accuracy;

                    accuracyBadge.textContent = `GPS Accuracy: ±${accuracy.toFixed(1)}m`;
                    accuracyBadge.className = accuracy <= 10 ? 'badge bg-success' : 'badge bg-warning text-dark';
                    accuracyBadge.classList.remove('d-none');

                    addWaypointPoint(lat, lng, `GPS (±${accuracy.toFixed(1)}m)`);
                    showStatusAlert(`📍 <b>ปักจุดพิกัดที่ ${pathCoords.length} สำเร็จ!</b> (ความแม่นยำ GPS ±${accuracy.toFixed(1)}m)`, 'success');
                },
                function(err) {
                    btnAddGps.disabled = false;
                    btnAddGps.innerHTML = '📍 ปักจุดพิกัดปัจจุบัน (GPS)';
                    alert(`เกิดข้อผิดพลาดในการดึงพิกัด GPS: ${err.message}`);
                },
                { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
            );
        });

        btnUndoGps.addEventListener('click', function() {
            if (pathCoords.length <= 1) {
                alert('ไม่สามารถย้อนกลับจุดเริ่มต้นได้ครับ หากต้องการเริ่มใหม่กด "ล้างเส้นทั้งหมด"');
                return;
            }

            removeWaypointPoint(pathCoords.length - 1);
            isEndLocked = false;
            showStatusAlert(`↩️ ย้อนกลับเรียบร้อยแล้ว (เหลือ ${pathCoords.length} จุด)`, 'warning');
        });
    }

    // เพิ่มพิกัดลงในอาร์เรย์ หมุด และตาราง
    function addWaypointPoint(lat, lng, sourceLabel = 'ทั่วไป') {
        const pointObj = { lat: lat, lng: lng, label: sourceLabel };
        pathCoords.push(pointObj);

        // หมุดบนแผนที่
        const wpMarker = L.circleMarker([lat, lng], {
            radius: 5,
            fillColor: pathCoords.length === 1 ? "#0284c7" : "#16a34a",
            color: "#ffffff",
            weight: 2,
            opacity: 1,
            fillOpacity: 0.9
        }).addTo(map).bindTooltip(`จุดที่ ${pathCoords.length}`);

        waypointMarkers.push(wpMarker);
        updatePolylineAndJSON();
        renderCoordsTable();
    }

    // ลบพิกัดตาม Index
    function removeWaypointPoint(index) {
        if (index < 0 || index >= pathCoords.length) return;

        pathCoords.splice(index, 1);
        
        if (waypointMarkers[index]) {
            map.removeLayer(waypointMarkers[index]);
            waypointMarkers.splice(index, 1);
        }

        // รีรีเฟรช Tooltip ลำดับหมุดที่เหลือ
        waypointMarkers.forEach((m, i) => {
            m.setTooltipContent(`จุดที่ ${i + 1}`);
        });

        if (pathCoords.length === 0) {
            isStartLocked = false;
        }

        updatePolylineAndJSON();
        renderCoordsTable();
    }

    // เรนเดอร์ ตารางพิกัด
    function renderCoordsTable() {
        const tbody = document.getElementById('coordsTableBody');
        const countText = document.getElementById('pointCountText');
        countText.textContent = pathCoords.length;

        if (pathCoords.length === 0) {
            tbody.innerHTML = `<tr><td colspan="5" class="text-center text-muted py-3">ยังไม่มีพิกัดแนวท่อ กรุณาเลือกจุดโหนดเริ่มต้นหรือปักพิกัด GPS</td></tr>`;
            return;
        }

        let html = '';
        pathCoords.forEach((p, idx) => {
            const isStart = idx === 0;
            const isEnd = idx === pathCoords.length - 1 && isEndLocked;
            
            let badgeTag = `<span class="badge bg-success">จุดเลี้ยว #${idx + 1} (${p.label || 'GPS'})</span>`;
            if (isStart) badgeTag = `<span class="badge bg-primary">📍 จุดเริ่มต้น (From Node)</span>`;
            else if (isEnd) badgeTag = `<span class="badge bg-danger">🏁 จุดสิ้นสุด (To Node)</span>`;

            html += `
                <tr style="cursor: pointer;" onclick="focusOnPoint(${p.lat}, ${p.lng})">
                    <td class="text-center fw-bold text-secondary">${idx + 1}</td>
                    <td>${badgeTag}</td>
                    <td class="font-monospace">${Number(p.lat).toFixed(6)}</td>
                    <td class="font-monospace">${Number(p.lng).toFixed(6)}</td>
                    <td class="text-end" onclick="event.stopPropagation();">
                        <button type="button" class="btn btn-outline-danger btn-sm py-0 px-2 font-bold" onclick="removeWaypointPoint(${idx})" style="font-size: 11px;">
                            🗑️ ลบ
                        </button>
                    </td>
                </tr>
            `;
        });

        tbody.innerHTML = html;
    }

    function focusOnPoint(lat, lng) {
        map.setView([lat, lng], 18);
    }

    function initFlyNodeEvents() {
        document.getElementById('toNodeSelect').addEventListener('change', function() {
            if (this.value === 'NEW_NODE_ON_THE_FLY') {
                if (pathCoords.length === 0) {
                    alert('กรุณาเลือกจุดเริ่มต้นหรือปักพิกัดก่อนกำหนดจุดสิ้นสุดครับ');
                    this.value = '';
                    return;
                }

                const lastPt = pathCoords[pathCoords.length - 1];
                document.getElementById('flyNodeCoordsText').textContent = `Lat: ${lastPt.lat.toFixed(6)}, Lng: ${lastPt.lng.toFixed(6)}`;
                
                const modal = new bootstrap.Modal(document.getElementById('newFlyNodeModal'));
                modal.show();
            }
        });

        document.getElementById('btnSaveFlyNode').addEventListener('click', function() {
            const stationId = document.getElementById('stationSelect').value;
            const name = document.getElementById('flyNodeName').value.trim();
            const type = document.getElementById('flyNodeType').value;
            
            if (!stationId) {
                alert('กรุณาเลือกสถานีผลิตน้ำหลักก่อนครับ');
                return;
            }

            if (!name) {
                alert('กรุณากรอกชื่อจุดโหนดใหม่ด้วยครับ');
                return;
            }

            const lastPt = pathCoords[pathCoords.length - 1];

            fetch("{{ route('tabwater.settings.pipe-nodes.store') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    station_id: stationId,
                    name: name,
                    node_code: "NODE-" + Date.now().toString().substr(-5),
                    type: type,
                    lat: lastPt.lat,
                    lng: lastPt.lng
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success || data.id || data.node) {
                    const newNodeId = data.id || (data.node ? data.node.id : null);
                    
                    const toSelect = document.getElementById('toNodeSelect');
                    const newOpt = document.createElement('option');
                    newOpt.value = newNodeId;
                    newOpt.textContent = `📍 ${name} (โหนดใหม่หน้างาน)`;
                    newOpt.setAttribute('data-lat', lastPt.lat);
                    newOpt.setAttribute('data-lng', lastPt.lng);
                    toSelect.appendChild(newOpt);
                    toSelect.value = newNodeId;

                    const newMarker = L.marker([lastPt.lat, lastPt.lng], { 
                        icon: MARKER_ICONS[type] || MARKER_ICONS.sub_junction 
                    }).addTo(map);
                    newMarker.bindTooltip(`<b>📍 ${name} (โหนดใหม่)</b>`).openTooltip();

                    isEndLocked = true;
                    updatePolylineAndJSON();

                    bootstrap.Modal.getInstance(document.getElementById('newFlyNodeModal')).hide();
                    showStatusAlert(`🎉 บันทึกโหนดใหม่ "${name}" และเชื่อมต่อแนวท่อสมบูรณ์แล้ว!`, 'success');
                } else {
                    alert('เกิดข้อผิดพลาดในการบันทึกโหนดใหม่');
                }
            })
            .catch(err => {
                console.error(err);
                alert('ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์เพื่อสร้างโหนดใหม่ได้');
            });
        });
    }

    function renderExistingPipelines() {
        existingPipelinesData.forEach(function(pipe) {
            if (pipe.path_coordinates && pipe.path_coordinates.length > 0) {
                const pathLatLngs = pipe.path_coordinates.map(p => [
                    Number(p.lat || p[0]), 
                    Number(p.lng || p.long || p[1])
                ]);

                const line = L.polyline(pathLatLngs, {
                    color: LINE_COLORS[pipe.level] || '#6c757d',
                    weight: 3,
                    opacity: 0.45,
                    dashArray: '6, 6'
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

    function handleNodeClick(nodeData, syncDropdown = true) { 
        const exactLat = Number(nodeData.lat); 
        const exactLng = Number(nodeData.lng); 

        if (!isStartLocked) { 
            pathCoords = [];
            waypointMarkers.forEach(m => map.removeLayer(m));
            waypointMarkers = [];

            addWaypointPoint(exactLat, exactLng, nodeData.name);
            isStartLocked = true; 

            if (syncDropdown) { 
                selectDropdownOption('fromNodeSelect', nodeData.id); 
            } 
            showStatusAlert(`📍 <b>จุดเริ่มต้น:</b> ${nodeData.name} (เดินไปยังจุดต่างๆ แล้วกดปุ่ม "ปักจุดพิกัดปัจจุบัน" ได้เลย)`); 
        } 
        else if (!isEndLocked) { 
            const fromId = document.getElementById('fromNodeSelect').value; 
            if (fromId == nodeData.id) { 
                alert('จุดปลายทางต้องไม่ซ้ำกับจุดเริ่มต้นครับ'); 
                return; 
            } 

            addWaypointPoint(exactLat, exactLng, nodeData.name);
            isEndLocked = true; 

            if (syncDropdown) { 
                selectDropdownOption('toNodeSelect', nodeData.id); 
            } 
            showStatusAlert(`🏁 <b>จุดสิ้นสุด:</b> ${nodeData.name} (เชื่อมต่อแนวท่อใหม่สมบูรณ์แล้ว)`, 'success'); 
        } 
    } 

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

        renderCoordsTable();
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

        waypointMarkers.forEach(m => map.removeLayer(m));
        waypointMarkers = [];

        document.getElementById('gpsAccuracyBadge').classList.add('d-none');
        document.getElementById('pathCoordsInput').value = '[]'; 
        document.getElementById('lengthInput').value = ''; 
        document.getElementById('fromNodeSelect').value = ''; 
        document.getElementById('toNodeSelect').value = ''; 
        document.getElementById('statusAlert').classList.add('d-none'); 

        renderCoordsTable();
    } 

    function filterNodesAndPipelinesByStation(stationId) { 
        const fromSelect = document.getElementById('fromNodeSelect'); 
        const toSelect = document.getElementById('toNodeSelect'); 

        [fromSelect, toSelect].forEach(select => { 
            select.value = ''; 
            Array.from(select.options).forEach(opt => { 
                if (opt.value === '' || opt.value === 'NEW_NODE_ON_THE_FLY') return; 
                const nodeStationId = opt.getAttribute('data-station-id'); 
                if (!stationId || nodeStationId == stationId) { 
                    opt.style.display = ''; 
                } else { 
                    opt.style.display = 'none'; 
                } 
            }); 
        }); 

        nodeMarkers.forEach(item => { 
            if (!stationId || item.stationId == stationId) { 
                if (!map.hasLayer(item.marker)) map.addLayer(item.marker); 
            } else { 
                if (map.hasLayer(item.marker)) map.removeLayer(item.marker); 
            } 
        }); 

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