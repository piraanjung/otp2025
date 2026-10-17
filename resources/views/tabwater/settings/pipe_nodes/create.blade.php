@extends('layouts.admin1') 

@section('content') 
<!-- Leaflet CSS & JS CDN -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<div class="container-fluid py-4"> 
    <div class="d-flex justify-content-between align-items-center mb-3"> 
        <h4 class="fw-bold text-dark mb-0">📍 จัดการจุดโหนดและวาล์วประปา (Interactive Node Manager - Leaflet.js)</h4> 
        <div class="d-flex gap-2"> 
            <a href="{{ route('tabwater.settings.pipe-nodes.index') }}" class="btn btn-outline-secondary btn-sm fw-bold"> 
                📋 รายการตารางโหนด (Index) 
            </a> 
            <a href="{{ route('tabwater.settings.pipelines.index') }}" class="btn btn-outline-primary btn-sm fw-bold"> 
                🚰 จัดการแนวท่อ (Pipelines) 
            </a> 
        </div> 
    </div> 

    @if(session('success')) 
        <div class="alert alert-success alert-dismissible fade show fw-bold text-sm" role="alert"> 
            {{ session('success') }} 
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button> 
        </div> 
    @endif 

    <!-- 1. การ์ดแผนที่ Interactive และกรองสถานี --> 
    <div class="card shadow-sm border-0 rounded-3 mb-4"> 
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2"> 
            <div class="d-flex align-items-center gap-3"> 
                <h6 class="fw-bold text-primary mb-0">🗺️ แผนที่ปักหมุดจุดโหนด (Leaflet.js)</h6> 
                <div class="d-none d-md-flex align-items-center gap-2 font-bold" style="font-size: 11px;"> 
                    <span class="badge bg-primary">🏭 โรงผลิตน้ำ</span> 
                    <span class="badge bg-info text-dark">🔵 ท่อเมน</span> 
                    <span class="badge bg-success">🟢 ท่อซอย</span> 
                    <span class="badge bg-danger">🛑 วาล์ว</span> 
                    <span class="badge bg-purple text-white" style="background-color: #8b5cf6;">🛢️ หอถังสูง</span> 
                    <span class="badge bg-warning text-dark">🏠 ปลายท่อ</span> 
                </div> 
            </div> 
            <div class="d-flex align-items-center gap-2"> 
                <select id="stationSelect" class="form-select form-select-sm fw-bold" style="min-width: 200px;"> 
                    <option value="">-- เลือกซูมไปที่สถานีผลิตน้ำ --</option> 
                    @foreach($stations as $s) 
                        <option value="{{ $s->id }}" data-lat="{{ $s->lat }}" data-lng="{{ $s->lng }}"> 
                            {{ $s->name }} ({{$s->code ?? '-' }}) 
                        </option> 
                    @endforeach 
                </select> 
                <button type="button" id="btnClearNewNodes" class="btn btn-outline-danger btn-sm text-nowrap fw-bold" style="font-size: 12px;"> 
                    🗑️ ล้างหมุดใหม่ที่ยังไม่บันทึก 
                </button> 
            </div> 
        </div> 
        <div class="card-body p-2"> 
            <div id="interactiveNodeMap" style="height: 450px; width: 100%; border-radius: 8px;"></div> 
        </div> 
    </div> 

    <!-- 2. ฟอร์มตารางจัดการจุดโหนด (รวมทั้งหมุดใหม่ที่เพิ่งคลิก + หมุดเดิมในระบบ) --> 
    <form action="{{ route('tabwater.settings.pipe-nodes.store') }}" method="POST"> 
        @csrf 
        <div class="card shadow-sm border-0 rounded-3 mb-4"> 
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center"> 
                <div class="d-flex align-items-center gap-2"> 
                    <h6 class="fw-bold text-dark mb-0">📋 รายการจุดโหนดที่ต้องการบันทึกใหม่ (<span id="newNodesCount" class="text-primary">0</span> จุด)</h6> 
                    <span class="badge bg-info-subtle text-info border border-info-subtle font-normal" style="font-size: 11px;"> 
                        💡 คลิกบนแผนที่เพื่อปักหมุดเพิ่ม 
                    </span> 
                </div> 
                <button type="submit" id="btnSubmitNewNodes" class="btn btn-primary btn-sm fw-bold px-3" disabled> 
                    💾 บันทึกจุดโหนดใหม่ทั้งหมด 
                </button> 
            </div> 
            <div class="card-body p-0"> 
                <div class="table-responsive"> 
                    <table class="table table-hover align-middle mb-0 text-sm"> 
                        <thead class="table-light border-bottom text-nowrap"> 
                            <tr> 
                                <th style="width: 50px;">#</th> 
                                <th style="width: 150px;">สถานีผลิตน้ำ *</th> 
                                <th style="width: 130px;">รหัสโหนด *</th> 
                                <th>ชื่อจุดโหนด *</th> 
                                <th style="width: 160px;">ประเภทโหนด *</th> 
                                <th style="width: 170px;">พิกัด (Lat, Long)</th> 
                                <th style="width: 60px;" class="text-center">จัดการ</th> 
                            </tr> 
                        </thead> 
                        <tbody id="newNodesTableBody"> 
                            <tr id="emptyNewRow"> 
                                <td colspan="7" class="text-center text-muted py-4"> 
                                    ยังไม่ได้คลิกปักหมุดใหม่บนแผนที่ (คลิกบนแผนที่ด้านบนเพื่อเพิ่มจุดโหนด) 
                                </td> 
                            </tr> 
                        </tbody> 
                    </table> 
                </div> 
            </div> 
        </div> 
    </form> 

    <!-- 3. ตารางแสดงรายการจุดโหนดเดิมที่มีอยู่แล้วในระบบ (สามารถกดแก้ไข/ลบได้) --> 
    <div class="card shadow-sm border-0 rounded-3"> 
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center"> 
            <h6 class="fw-bold text-secondary mb-0">📌 จุดโหนดที่มีอยู่ในระบบแล้ว (Existing Nodes)</h6> 
            <span class="badge bg-secondary-subtle text-secondary border fw-bold"> 
                ทั้งหมด {{ count($allNodesMap) }} จุด 
            </span> 
        </div> 
        <div class="card-body p-0"> 
            <div class="table-responsive"> 
                <table class="table table-hover align-middle mb-0 text-sm"> 
                    <thead class="table-light border-bottom text-nowrap"> 
                        <tr> 
                            <th>รหัสโหนด</th> <th>ชื่อจุดโหนด</th> <th>สถานีต้นทาง</th> <th>ประเภทโหนด</th> <th>พิกัด (Lat, Long)</th> <th class="text-end">จัดการ</th> 
                        </tr> 
                    </thead> 
                    <tbody> 
                        @forelse($allNodesMap as $existingNode) 
                            <tr> 
                                <td class="fw-bold text-secondary font-monospace">{{ $existingNode->node_code }}</td> 
                                <td class="fw-bold text-primary">{{ $existingNode->name }}</td> 
                                <td class="fw-bold text-dark">{{ $existingNode->station->name ?? '-' }}</td> 
                                <td> 
                                    @switch($existingNode->type) 
                                        @case('main_junction') 
                                            <span class="badge bg-primary fw-bold">🔵 จุดแยกท่อเมน</span> 
                                            @break 
                                        @case('sub_junction') 
                                            <span class="badge bg-info text-dark fw-bold">🟢 จุดแยกท่อซอย</span> 
                                            @break 
                                        @case('valve_point') 
                                            <span class="badge bg-danger fw-bold">🛑 จุดวาล์ว</span> 
                                            @break 
                                        @case('tank') 
                                            <span class="badge text-white fw-bold" style="background-color: #8b5cf6;">🛢️ ถังพักน้ำ/หอถัง</span> 
                                            @break 
                                        @case('end_point') 
                                            <span class="badge bg-warning text-dark fw-bold">🏠 จุดปลายท่อ</span> 
                                            @break 
                                        @default 
                                            <span class="badge bg-dark fw-bold">🏭 โรงผลิตน้ำ</span> 
                                    @endswitch 
                                </td> 
                                <td class="font-monospace text-muted small"> 
                                    {{ number_format($existingNode->lat, 6) }}, {{ number_format($existingNode->lng, 6) }} 
                                </td> 
                                <td class="text-end text-nowrap"> 
                                    <a href="{{ route('tabwater.settings.pipe-nodes.edit', $existingNode->id) }}" class="btn btn-sm btn-outline-primary fw-bold me-1"> 
                                        แก้ไข 
                                    </a> 
                                    <form action="{{ route('tabwater.settings.pipe-nodes.destroy', $existingNode->id) }}" method="POST" class="d-inline" onsubmit="return confirm('ยืนยันการลบจุดโหนดนี้?')"> 
                                        @csrf 
                                        @method('DELETE') 
                                        <button type="submit" class="btn btn-sm btn-outline-danger fw-bold">ลบ</button> 
                                    </form> 
                                </td> 
                            </tr> 
                        @empty 
                            <tr> 
                                <td colspan="6" class="text-center text-muted py-4">ยังไม่มีข้อมูลจุดโหนดเดิมในระบบ</td> 
                            </tr> 
                        @endforelse 
                    </tbody> 
                </table> 
            </div> 
        </div> 
    </div> 
</div> 

<script> 
    let map; 
    let newPlacedMarkers = []; // อาร์เรย์หมุดใหม่ที่เพิ่งคลิกปัก 
    const stationsData = @json($stationsMap ?? []); 
    const existingNodesData = @json($allNodesMap ?? []); 

    function createCustomIcon(iconUrl, opacity = 1.0) {
        return L.icon({
            iconUrl: iconUrl,
            iconSize: [28, 28],
            iconAnchor: [14, 28],
            popupAnchor: [0, -28]
        });
    }

    // ไอคอนหมุดประเภทต่างๆ
    const MARKER_ICONS = { 
        station: createCustomIcon('{{ asset("imgs/tower.png") }}'), 
        main_junction: createCustomIcon('{{ asset("imgs/fire_hydrant.png") }}'), 
        sub_junction: createCustomIcon('{{ asset("imgs/pump.png") }}'), 
        valve_point: createCustomIcon('https://maps.google.com/mapfiles/ms/icons/red-dot.png'), 
        tank: createCustomIcon('https://maps.google.com/mapfiles/ms/icons/purple-dot.png'), 
        end_point: createCustomIcon('https://maps.google.com/mapfiles/ms/icons/yellow-dot.png'), 
        new_node: createCustomIcon('https://maps.google.com/mapfiles/ms/icons/blue-dot.png')
    }; 

    document.addEventListener("DOMContentLoaded", function() {
        initInteractiveMap();
    });

    function initInteractiveMap() { 
        const stationSelect = document.getElementById('stationSelect'); 
        let defaultLat = 17.154100; 
        let defaultLng = 104.148600; 

        if (stationsData.length > 0 && stationsData[0].lat) { 
            defaultLat = Number(stationsData[0].lat); 
            defaultLng = Number(stationsData[0].lng); 
        } else if (existingNodesData.length > 0 && existingNodesData[0].lat) { 
            defaultLat = Number(existingNodesData[0].lat); 
            defaultLng = Number(existingNodesData[0].lng); 
        } 

        // 1. สร้าง Map ด้วย Leaflet
        map = L.map('interactiveNodeMap').setView([defaultLat, defaultLng], 14); 

        // 2. Tile Layer OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(map);

        const bounds = [];

        // 3. ปักหมุดโรงผลิตน้ำประปา (Stations) 
        stationsData.forEach(function(station) { 
            const lat = Number(station.lat); 
            const lng = Number(station.lng); 

            if (!isNaN(lat) && !isNaN(lng)) { 
                bounds.push([lat, lng]);
                const marker = L.marker([lat, lng], { icon: MARKER_ICONS.station }).addTo(map);
                marker.bindPopup(`<b>🏭 ${station.name} (${station.code || '-'})</b>`);
            } 
        }); 

        // 4. ปักหมุดจุดโหนดเดิมที่มีอยู่ในระบบ (Existing Nodes) 
        existingNodesData.forEach(function(node) { 
            const lat = Number(node.lat); 
            const lng = Number(node.lng); 

            if (!isNaN(lat) && !isNaN(lng)) { 
                bounds.push([lat, lng]);
                const icon = MARKER_ICONS[node.type] || MARKER_ICONS.sub_junction;

                const marker = L.marker([lat, lng], { icon: icon, opacity: 0.75 }).addTo(map);
                marker.bindPopup(`
                    <div style="font-size: 12px;"> 
                        <b>📍 ${node.name} (${node.node_code})</b><br> 
                        <span style="color: #6c757d;">ประเภท: ${node.type} (โหนดเดิมในระบบ)</span> 
                    </div> 
                `);
            } 
        }); 

        // 5. คลิกบนแผนที่เพื่อสร้างหมุดโหนดใหม่ 
        map.on('click', function(e) { 
            addNewNodeMarker(e.latlng.lat, e.latlng.lng); 
        }); 

        // ปรับซูมแผนที่ให้อยู่ในมุมมองที่เห็นหมุดทั้งหมด 
        if (bounds.length > 0) { 
            map.fitBounds(bounds, { padding: [30, 30] }); 
        } 

        // ซูมไปยังสถานีเมื่อเลือก Dropdown 
        if (stationSelect) { 
            stationSelect.addEventListener('change', function() { 
                const selectedOption = this.options[this.selectedIndex]; 
                const sLat = Number(selectedOption.getAttribute('data-lat')); 
                const sLng = Number(selectedOption.getAttribute('data-lng')); 

                if (!isNaN(sLat) && !isNaN(sLng)) { 
                    map.setView([sLat, sLng], 16); 
                } 
            }); 
        } 

        // ปุ่มล้างหมุดใหม่ 
        document.getElementById('btnClearNewNodes').addEventListener('click', function() { 
            clearAllNewNodes(); 
        }); 
    } 

    // ฟังก์ชันแทรกหมุดใหม่ลงแผนที่และเพิ่มแถวในตาราง 
    function addNewNodeMarker(lat, lng) { 
        const idx = newPlacedMarkers.length; 

        // สร้างหมุดใหม่ที่ลากย้ายได้ (Draggable Marker)
        const marker = L.marker([lat, lng], {
            icon: MARKER_ICONS.new_node,
            draggable: true
        }).addTo(map);

        marker.bindTooltip(`จุดโหนดใหม่ที่ ${idx + 1}`, { permanent: true, direction: 'top' });

        // ลากย้ายหมุดเพื่ออัปเดต Lat/Lng
        marker.on('dragend', function(e) {
            const position = marker.getLatLng();
            const newLat = position.lat.toFixed(6);
            const newLng = position.lng.toFixed(6);

            document.getElementById(`lat_${idx}`).value = newLat;
            document.getElementById(`lng_${idx}`).value = newLng;
            document.getElementById(`coordText_${idx}`).innerText = `${newLat}, ${newLng}`;
        });

        newPlacedMarkers.push(marker); 
        renderNewNodesTable(); 
    } 

    // ฟังก์ชันเรนเดอร์แถวตารางหมุดใหม่ 
    function renderNewNodesTable() { 
        const tbody = document.getElementById('newNodesTableBody'); 
        const countSpan = document.getElementById('newNodesCount'); 
        const btnSubmit = document.getElementById('btnSubmitNewNodes'); 

        if (newPlacedMarkers.length === 0) { 
            tbody.innerHTML = ` 
                <tr id="emptyNewRow"> 
                    <td colspan="7" class="text-center text-muted py-4"> 
                        ยังไม่ได้คลิกปักหมุดใหม่บนแผนที่ (คลิกบนแผนที่ด้านบนเพื่อเพิ่มจุดโหนด) 
                    </td> 
                </tr>`; 
            countSpan.innerText = '0'; 
            btnSubmit.disabled = true; 
            return; 
        } 

        btnSubmit.disabled = false; 
        countSpan.innerText = newPlacedMarkers.length; 

        let html = ''; 
        newPlacedMarkers.forEach((m, idx) => { 
            const pos = m.getLatLng();
            const lat = pos.lat.toFixed(6); 
            const lng = pos.lng.toFixed(6); 

            let stationOptions = ''; 
            stationsData.forEach(s => { 
                stationOptions += `<option value="${s.id}">${s.name}</option>`; 
            }); 

            html += ` 
                <tr> 
                    <td class="fw-bold text-primary">${idx + 1}</td> 
                    <td> 
                        <select name="nodes[${idx}][station_id]" required class="form-select form-select-sm fw-bold"> 
                            ${stationOptions} 
                        </select> 
                    </td> 
                    <td> 
                        <input type="text" name="nodes[${idx}][node_code]" value="JNC-${String(existingNodesData.length + idx + 1).padStart(2, '0')}" required class="form-control form-control-sm font-monospace"> 
                    </td> 
                    <td> 
                        <input type="text" name="nodes[${idx}][name]" placeholder="เช่น จุดแยกซอย ${idx + 1}" required class="form-control form-control-sm"> 
                    </td> 
                    <td> 
                        <select name="nodes[${idx}][type]" required class="form-select form-select-sm fw-bold"> 
                            <option value="main_junction">🔵 จุดแยกท่อเมน</option> 
                            <option value="sub_junction" selected>🟢 จุดแยกท่อซอย</option> 
                            <option value="valve_point">🛑 จุดวาล์ว</option> 
                            <option value="tank">🛢️ ถังพักน้ำ</option> 
                            <option value="end_point">🏠 ปลายท่อ</option> 
                        </select> 
                    </td> 
                    <td class="font-monospace text-muted small"> 
                        <span id="coordText_${idx}">${lat}, ${lng}</span> 
                        <input type="hidden" id="lat_${idx}" name="nodes[${idx}][lat]" value="${lat}"> 
                        <input type="hidden" id="lng_${idx}" name="nodes[${idx}][lng]" value="${lng}"> 
                    </td> 
                    <td class="text-center"> 
                        <button type="button" onclick="removeNewNode(${idx})" class="btn btn-outline-danger btn-sm py-0 px-2">✕</button> 
                    </td> 
                </tr> 
            `; 
        }); 

        tbody.innerHTML = html; 
    } 

    function removeNewNode(index) { 
        if (newPlacedMarkers[index]) {
            map.removeLayer(newPlacedMarkers[index]); 
            newPlacedMarkers.splice(index, 1); 
        }

        newPlacedMarkers.forEach((m, idx) => { 
            m.setTooltipContent(`จุดโหนดใหม่ที่ ${idx + 1}`); 
        }); 

        renderNewNodesTable(); 
    } 

    function clearAllNewNodes() { 
        newPlacedMarkers.forEach(m => map.removeLayer(m)); 
        newPlacedMarkers = []; 
        renderNewNodesTable(); 
    } 
</script> 
@endsection