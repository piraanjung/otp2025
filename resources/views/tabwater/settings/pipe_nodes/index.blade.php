@extends('layouts.admin1')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold text-dark mb-0">📍 รายการจุดโหนดและวาล์วในระบบ (Pipe Nodes)</h4>
        <div class="d-flex gap-2">
            <a href="{{ route('tabwater.settings.pipelines.index') }}" class="btn btn-outline-secondary btn-sm fw-bold">
                🚰 จัดการแนวท่อ (Pipelines)
            </a>
            <a href="{{ route('tabwater.settings.pipe-nodes.create') }}" class="btn btn-primary btn-sm fw-bold">
                ➕ เพิ่มจุดโหนดใหม่
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show fw-bold text-sm" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show fw-bold text-sm" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- การ์ดแสดงแผนที่ตำแหน่ง Node และ สถานีทั้งหมด -->
    <div class="card shadow-sm border-0 rounded-3 mb-4">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-3">
                <h6 class="fw-bold text-primary mb-0">🗺️ แผนที่ตำแหน่งโรงผลิตน้ำประปาและจุดโหนด</h6>
                <!-- สัญลักษณ์สีหมุด (Legend) -->
                <div class="d-none d-md-flex align-items-center gap-2 font-bold" style="font-size: 11px;">
                    <span class="badge bg-primary">🏭 โรงผลิตน้ำ</span>
                    <span class="badge bg-info text-dark">🔵 ท่อเมนหลัก</span>
                    <span class="badge bg-success">🟢 ท่อซอย</span>
                    <span class="badge bg-danger">🛑 วาล์ว</span>
                    <span class="badge bg-purple text-white" style="background-color: #8b5cf6;">🛢️ หอถังสูง</span>
                    <span class="badge bg-warning text-dark">🏠 ปลายท่อ</span>
                </div>
            </div>
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle fw-bold">
                โหนด {{ $nodes->total() }} จุด / โรงผลิต {{ $stationsMap->count() }} แห่ง
            </span>
        </div>
        <div class="card-body p-2">
            <div id="allNodesMap" style="height: 420px; width: 100%; border-radius: 8px;"></div>
        </div>
    </div>

    <!-- Filter กรองข้อมูล -->
    <div class="card shadow-sm border-0 mb-3">
        <div class="card-body p-2">
            <form method="GET" action="{{ route('tabwater.settings.pipe-nodes.index') }}" class="row g-2 align-items-center">
                <div class="col-auto">
                    <select name="station_id" onchange="this.form.submit()" class="form-select form-select-sm fw-bold">
                        <option value="">-- แสดงทุกสถานีผลิตน้ำ --</option>
                        @foreach($stations as $s)
                            <option value="{{ $s->id }}" {{ request('station_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <select name="type" onchange="this.form.submit()" class="form-select form-select-sm fw-bold">
                        <option value="">-- ทุกประเภทโหนด --</option>
                        <option value="main_junction" {{ request('type') === 'main_junction' ? 'selected' : '' }}>จุดแยกท่อเมนหลัก</option>
                        <option value="sub_junction" {{ request('type') === 'sub_junction' ? 'selected' : '' }}>จุดแยกท่อรอง/ท่อซอย</option>
                        <option value="valve_point" {{ request('type') === 'valve_point' ? 'selected' : '' }}>จุดติดตั้งวาล์ว</option>
                        <option value="tank" {{ request('type') === 'tank' ? 'selected' : '' }}>ถังพักน้ำ/หอถังสูง</option>
                        <option value="end_point" {{ request('type') === 'end_point' ? 'selected' : '' }}>จุดปลายท่อ/บ้านผู้ใช้</option>
                        <option value="station" {{ request('type') === 'station' ? 'selected' : '' }}>โรงผลิตน้ำต้นทาง</option>
                    </select>
                </div>
            </form>
        </div>
    </div>

    <!-- ตารางแสดงรายการจุดโหนด -->
    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-sm">
                    <thead class="table-light border-bottom text-nowrap">
                        <tr>
                            <th>รหัสโหนด</th>
                            <th>ชื่อจุดโหนด</th>
                            <th>สถานีต้นทาง</th>
                            <th>ประเภทโหนด</th>
                            <th>พิกัด (Lat, Long)</th>
                            <th>ท่อเชื่อมต่อ (ออก / เข้า)</th>
                            <th>หมายเหตุ</th>
                            <th class="text-end">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($nodes as $node)
                            <tr>
                                <td class="fw-bold text-secondary font-monospace">{{ $node->node_code }}</td>
                                <td class="fw-bold text-primary">{{ $node->name }}</td>
                                <td class="fw-bold text-dark">{{ $node->station->name ?? '-' }}</td>
                                <td>
                                    @switch($node->type)
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
                                    {{ number_format($node->lat, 6) }}, {{ number_format($node->lng, 6) }}
                                </td>
                                <td>
                                    <span class="badge bg-primary-subtle text-primary border me-1">
                                        ออก: {{ $node->outgoingPipes->count() }} สาย
                                    </span>
                                    <span class="badge bg-secondary-subtle text-secondary border">
                                        เข้า: {{ $node->incomingPipes->count() }} สาย
                                    </span>
                                </td>
                                <td class="text-muted small">{{ $node->remark ?? '-' }}</td>
                                <td class="text-end text-nowrap">
                                    <a href="{{ route('tabwater.settings.pipe-nodes.edit', $node->id) }}" class="btn btn-sm btn-outline-primary fw-bold me-1">
                                        แก้ไข
                                    </a>
                                    <form action="{{ route('tabwater.settings.pipe-nodes.destroy', $node->id) }}" method="POST" class="d-inline" onsubmit="return confirm('ยืนยันการลบจุดโหนดนี้?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger fw-bold">ลบ</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">ยังไม่มีข้อมูลจุดโหนดในระบบ</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-3">
        {{ $nodes->links() }}
    </div>
</div>

<!-- Google Maps Script -->
<!-- Google Maps Script -->
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA-5AlIGzLhFXErl2STRT6GacX0616iW2o&callback=initNodesMap"></script>
<script>
    // ดึงข้อมูลสถานีทั้งหมด และ Pipe Nodes ทั้งหมดที่ไม่ได้ติด Pagination
    const stationsData = @json($stationsMap ?? []);
    const nodesData = @json($allNodesMap ?? []);

    const MARKER_ICONS = {
        station: '{{ asset("imgs/tower.png") }}',       // สีน้ำเงินเข้ม (โรงผลิตน้ำ)
        main_junction: '{{ asset("imgs/fire_hydrant.png") }}', // สีฟ้า (จุดแยกท่อเมน)
        sub_junction: '{{ asset("imgs/pump.png") }}',  // สีเขียว (จุดแยกท่อซอย)
        valve_point: 'http://maps.google.com/mapfiles/ms/icons/red-dot.png',     // สีแดง (จุดวาล์ว)
        tank: 'http://maps.google.com/mapfiles/ms/icons/purple-dot.png',        // สีม่วง (หอถังสูง)
        end_point: 'http://maps.google.com/mapfiles/ms/icons/yellow-dot.png',   // สีเหลือง (ปลายท่อ)
    };

    function initNodesMap() {
        let defaultLat = 17.154100;
        let defaultLng = 104.148600;

        if (stationsData.length > 0 && stationsData[0].lat && stationsData[0].lng) {
            defaultLat = parseFloat(stationsData[0].lat);
            defaultLng = parseFloat(stationsData[0].lng);
        } else if (nodesData.length > 0 && nodesData[0].lat && nodesData[0].lng) {

            defaultLat = parseFloat(nodesData[0].lat);
            defaultLng = parseFloat(nodesData[0].lng);
        }

        const map = new google.maps.Map(document.getElementById('allNodesMap'), {
            center: { lat: defaultLat, lng: defaultLng },
            zoom: 13,
            mapTypeControl: true,
            streetViewControl: false,
        });

        const infoWindow = new google.maps.InfoWindow();
        const bounds = new google.maps.LatLngBounds();
        let hasPoints = false;

        // 1. ปักหมุดโรงผลิตน้ำประปา (Stations)
        stationsData.forEach(function(station) {
            const lat = parseFloat(station.lat);
            const lng = parseFloat(station.lng || station.lng);

            if (!isNaN(lat) && !isNaN(lng)) {
                const pos = { lat: lat, lng: lng };
                hasPoints = true;

                const marker = new google.maps.Marker({
                    position: pos,
                    map: map,
                    title: station.name,
                    icon: MARKER_ICONS.station,
                    zIndex: 1000
                });

                bounds.extend(pos);

                const contentString = `
                    <div style="padding: 6px; font-family: sans-serif; font-size: 12px; line-height: 1.4;">
                        <div style="font-weight: bold; color: #0d6efd; font-size: 14px; margin-bottom: 4px;">
                            🏭 ${station.name} (${station.code || '-'})
                        </div>
                        <div style="color: #495057;">
                            <b>ประเภท:</b> <span style="color: #0d6efd; font-weight: bold;">โรงผลิตน้ำประปาหลัก</span><br>
                            <b>พิกัด:</b> ${lat.toFixed(6)}, ${lng.toFixed(6)}
                        </div>
                    </div>
                `;

                marker.addListener('mouseover', function() {
                    infoWindow.setContent(contentString);
                    infoWindow.open(map, marker);
                });
            }
        });

        // 2. ปักหมุดจุดโหนด (Pipe Nodes) ทั้งหมดที่บันทึกไว้
        nodesData.forEach(function(node) {
            const lat = parseFloat(node.lat);
            const lng = parseFloat(node.lng);

            if (!isNaN(lat) && !isNaN(lng)) {

                const pos = { lat: lat, lng: lng };
                hasPoints = true;
                
                const iconUrl = MARKER_ICONS[node.type];
                const marker = new google.maps.Marker({
                    position: pos,
                    map: map,
                    title: node.name,
                    icon: iconUrl
                });
                console.log('marker',marker)

                bounds.extend(pos);

                const contentString = `
                    <div style="padding: 6px; font-family: sans-serif; font-size: 12px; line-height: 1.4;">
                        <div style="font-weight: bold; color: #212529; font-size: 13px; margin-bottom: 4px;">
                            📍 ${node.name} (${node.node_code})
                        </div>
                        <div style="color: #495057; margin-bottom: 6px;">
                            <b>ประเภท:</b> ${node.type}<br>
                            <b>พิกัด:</b> ${lat.toFixed(6)}, ${lng.toFixed(6)}
                        </div>
                        <div style="text-align: right;">
                            <a href="/tabwater/settings/pipe-nodes/${node.id}/edit" 
                               style="display: inline-block; padding: 2px 8px; background: #0d6efd; color: white; text-decoration: none; border-radius: 4px; font-size: 11px; font-weight: bold;">
                                ✏️ แก้ไขโหนด
                            </a>
                        </div>
                    </div>
                `;

                marker.addListener('mouseover', function() {
                    infoWindow.setContent(contentString);
                    infoWindow.open(map, marker);
                });

                marker.addListener('click', function() {
                    map.setZoom(16);
                    map.setCenter(marker.getPosition());
                    infoWindow.setContent(contentString);
                    infoWindow.open(map, marker);
                });
            }
        });

        // ปรับซูมแผนที่อัตโนมัติให้เห็นหมุดทั้งหมด
        if (hasPoints) {
            map.fitBounds(bounds);
        }
    }
</script>
@endsection