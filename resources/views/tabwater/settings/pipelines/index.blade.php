@extends('layouts.admin1') 

@section('content') 
<!-- Leaflet CSS & JS CDN -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<div class="container-fluid py-4"> 
    <div class="d-flex justify-content-between align-items-center mb-3"> 
        <h4 class="fw-bold text-dark mb-0">🚰 บริหารจัดการโครงข่ายแนวท่อน้ำประปา</h4> 
        <div class="d-flex gap-2"> 
            <a href="{{ route('tabwater.settings.pipe-nodes.create') }}" class="btn btn-outline-primary btn-sm fw-bold"> 
                📍 จัดการจุดโหนด (Pipe Nodes) 
            </a> 
            <a href="{{ route('tabwater.settings.pipelines.create') }}" class="btn btn-primary btn-sm fw-bold"> 
                ➕ ลากวาดแนวท่อใหม่ 
            </a> 
        </div> 
    </div> 

    @if(session('success')) 
        <div class="alert alert-success alert-dismissible fade show fw-bold text-sm" role="alert"> 
            {{ session('success') }} 
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button> 
        </div> 
    @endif 

    <!-- แผนที่แสดงโครงข่ายท่อประปาทั้งหมด (Leaflet.js) --> 
    <div class="card shadow-sm border-0 rounded-3 mb-4"> 
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2"> 
            <div class="d-flex align-items-center gap-3"> 
                <h6 class="fw-bold text-primary mb-0">🗺️ แผนที่โครงข่ายท่อน้ำประปาและจุดโหนดทั้งหมด (Leaflet.js)</h6> 
                <div class="d-none d-md-flex align-items-center gap-2 font-bold" style="font-size: 11px;"> 
                    <span class="badge bg-primary">🔵 ท่อเมนหลัก</span> 
                    <span class="badge bg-success">🟢 ท่อประธาน</span> 
                    <span class="badge bg-warning text-dark">🟡 ท่อซอย</span> 
                </div> 
            </div> 
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle fw-bold"> 
                แนวท่อ {{ count($allPipelinesMap) }} สาย / จุดโหนด {{ count($allNodesMap) }} จุด 
            </span> 
        </div> 
        <div class="card-body p-2"> 
            <div id="pipelineMap" style="height: 420px; width: 100%; border-radius: 8px;"></div> 
        </div> 
    </div> 

    <!-- Filter ตามสถานี --> 
    <div class="card shadow-sm border-0 mb-3"> 
        <div class="card-body p-2"> 
            <form method="GET" action="{{ route('tabwater.settings.pipelines.index') }}" class="row g-2 align-items-center"> 
                <div class="col-auto"> 
                    <select name="station_id" onchange="this.form.submit()" class="form-select form-select-sm fw-bold"> 
                        <option value="">-- แสดงทุกสถานีผลิตน้ำ --</option> 
                        @foreach($stations as $s) 
                            <option value="{{ $s->id }}" {{ request('station_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option> 
                        @endforeach 
                    </select> 
                </div> 
            </form> 
        </div> 
    </div> 

    <div class="card shadow-sm border-0 rounded-3"> 
        <div class="card-body p-0"> 
            <div class="table-responsive"> 
                <table class="table table-hover align-middle mb-0 text-sm"> 
                    <thead class="table-light border-bottom text-nowrap"> 
                        <tr> 
                            <th>รหัสท่อ</th> <th>ชื่อสายท่อ</th> <th>สถานีต้นทาง</th> <th>ระดับท่อ</th> <th>ขนาด / วัสดุ</th> <th>จุดโหนด (ต้นทาง ➔ ปลายทาง)</th> <th>ความยาว</th> <th class="text-end">จัดการ</th> 
                        </tr> 
                    </thead> 
                    <tbody> 
                        @forelse($pipelines as $p) 
                            <tr> 
                                <td class="fw-bold text-secondary">{{ $p->pipe_code }}</td> 
                                <td class="fw-bold text-primary">{{ $p->name }}</td> 
                                <td class="fw-bold text-dark">{{ $p->station->name ?? '-' }}</td> 
                                <td> 
                                    @if($p->level === 'main') 
                                        <span class="badge bg-primary fw-bold">🔵 ท่อเมนหลัก</span> 
                                    @elseif($p->level === 'sub_main') 
                                        <span class="badge bg-success fw-bold">🟢 ท่อประธาน</span> 
                                    @else 
                                        <span class="badge bg-warning text-dark fw-bold">🟡 ท่อซอย/บริการ</span> 
                                    @endif 
                                </td> 
                                <td class="fw-bold text-dark">{{ $p->size_mm }} มม. ({{ $p->material }})</td> 
                                <td> 
                                    <span class="badge bg-light text-dark border me-1">{{ $p->fromNode->name ?? '-' }}</span> ➔ 
                                    <span class="badge bg-light text-dark border ms-1">{{ $p->toNode->name ?? '-' }}</span> 
                                </td> 
                                <td class="fw-bold text-secondary">{{ $p->length_m ? number_format($p->length_m, 2).' ม.' : '-' }}</td> 
                                <td class="text-end text-nowrap"> 
                                    <a href="{{ route('tabwater.settings.pipelines.edit', $p->id) }}" class="btn btn-sm btn-outline-primary fw-bold me-1">แก้ไข</a> 
                                    <form action="{{ route('tabwater.settings.pipelines.destroy', $p->id) }}" method="POST" class="d-inline" onsubmit="return confirm('ยืนยันการลบสายท่อนี้?')"> 
                                        @csrf 
                                        @method('DELETE') 
                                        <button type="submit" class="btn btn-sm btn-outline-danger fw-bold">ลบ</button> 
                                    </form> 
                                </td> 
                            </tr> 
                        @empty 
                            <tr> 
                                <td colspan="8" class="text-center text-muted py-4">ยังไม่มีข้อมูลแนวท่อน้ำประปาในระบบ</td> 
                            </tr> 
                        @endforelse 
                    </tbody> 
                </table> 
            </div> 
        </div> 
    </div> 

    <div class="mt-3"> 
        {{ $pipelines->links() }} 
    </div> 
</div> 

<script> 
    const stationsData = @json($stationsMap ?? []); 
    const nodesData = @json($allNodesMap ?? []); 
    const pipelinesData = @json($allPipelinesMap ?? []); 

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
        main: '#0284c7',        // สีฟ้าท่อเมนหลัก 
        sub_main: '#16a34a',    // สีเขียวท่อประธาน 
        distribution: '#eab308' // สีเหลืองท่อซอย 
    }; 

    document.addEventListener("DOMContentLoaded", function() {
        initPipelineMap();
    });

    function initPipelineMap() { 
        let defaultLat = 17.154100; 
        let defaultLng = 104.148600; 

        if (stationsData.length > 0 && stationsData[0].lat) { 
            defaultLat = Number(stationsData[0].lat); 
            defaultLng = Number(stationsData[0].lng); 
        } 

        // 1. สร้าง Map ด้วย Leaflet
        const map = L.map('pipelineMap').setView([defaultLat, defaultLng], 14); 

        // 2. Tile Layer OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(map);

        const bounds = [];

        // 3. วาดเส้นแนวท่อประปา (Polylines)
        pipelinesData.forEach(function(pipe) { 
            if (pipe.path_coordinates && pipe.path_coordinates.length > 0) { 
                const pathLatLngs = pipe.path_coordinates.map(p => [
                    Number(p.lat || p[0]), 
                    Number(p.lng || p.long || p[1])
                ]); 

                const polyline = L.polyline(pathLatLngs, {
                    color: LINE_COLORS[pipe.level] || '#0284c7',
                    weight: pipe.level === 'main' ? 6 : (pipe.level === 'sub_main' ? 4 : 3),
                    opacity: 0.85
                }).addTo(map);

                pathLatLngs.forEach(pt => bounds.push(pt));

                polyline.bindPopup(`
                    <div style="font-size: 12px; font-family: sans-serif;"> 
                        <b style="color: #0284c7;">🚰 ${pipe.name} (${pipe.pipe_code})</b><br> 
                        ขนาด: ${pipe.size_mm} มม. | ระดับ: ${pipe.level} 
                    </div> 
                `);
            } 
        }); 

        // 4. ปักหมุด Pipe Nodes
        nodesData.forEach(function(node) { 
            const lat = Number(node.lat); 
            const lng = Number(node.lng); 

            if (!isNaN(lat) && !isNaN(lng)) { 
                bounds.push([lat, lng]);
                const icon = MARKER_ICONS[node.type] || MARKER_ICONS.sub_junction;

                const marker = L.marker([lat, lng], { icon: icon }).addTo(map);
                marker.bindPopup(`<b>📍 ${node.name} (${node.node_code})</b><br>ประเภท: ${node.type}`);
            } 
        }); 

        // 5. ปักหมุดโรงผลิตน้ำ (Stations)
        stationsData.forEach(function(station) { 
            const lat = Number(station.lat); 
            const lng = Number(station.lng); 

            if (!isNaN(lat) && !isNaN(lng)) { 
                bounds.push([lat, lng]);

                const marker = L.marker([lat, lng], { icon: MARKER_ICONS.station }).addTo(map);
                marker.bindPopup(`<b>🏭 ${station.name} (${station.code || '-'})</b>`);
            } 
        }); 

        // 6. ปรับขอบเขตแผนที่ให้ครอบคลุมพิกัดทั้งหมด
        if (bounds.length > 0) { 
            map.fitBounds(bounds, { padding: [30, 30] }); 
        } 
    } 
</script> 
@endsection