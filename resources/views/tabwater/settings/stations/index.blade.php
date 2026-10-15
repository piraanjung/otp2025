@extends('layouts.admin1')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold text-dark mb-0">🏭 ตั้งค่าและแผนที่สถานีผลิตน้ำประปา</h4>
        <a href="{{ route('tabwater.settings.stations.create') }}" class="btn btn-primary btn-sm fw-bold">
            ➕ เพิ่มสถานีใหม่
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show fw-bold text-sm" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- การ์ดแสดงแผนที่รวมสถานีทั้งหมด -->
    <div class="card shadow-sm border-0 rounded-3 mb-4">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <h6 class="fw-bold text-primary mb-0">🗺️ แผนที่ตำแหน่งสถานีผลิตน้ำทั้งหมด</h6>
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle fw-bold">
                ทั้งหมด {{ $allStationsMap->count() }} สถานี
            </span>
        </div>
        <div class="card-body p-2">
            <!-- กรอบ Google Maps -->
            <div id="allStationsMap" style="height: 400px; width: 100%; border-radius: 8px;"></div>
        </div>
    </div>

    <!-- ตารางรายการสถานี -->
    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-sm">
                    <thead class="table-light border-bottom text-nowrap">
                        <tr>
                            <th>รหัส</th>
                            <th>ชื่อสถานี</th>
                            <th>ช่างผู้รับผิดชอบ</th>
                            <th>อุปกรณ์</th>
                            <th>เกณฑ์เตือน NTU</th>
                            <th>เกณฑ์เตือน pH</th>
                            <th>เกณฑ์ตัดปั๊ม %</th>
                            <th>K-Factor</th>
                            <th class="text-end">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stations as $st)
                            <tr>
                                <td class="fw-bold text-muted">{{ $st->code ?? '-' }}</td>
                                <td class="fw-bold text-primary">{{ $st->name }}</td>
                                <td>
                                    @forelse($st->staffs as $staff)
                                        <span class="badge bg-secondary-subtle text-secondary me-1">
                                            {{ $staff->user->firstname ?? $staff->name }}
                                        </span>
                                    @empty
                                        <span class="text-muted small">-</span>
                                    @endforelse
                                </td>
                                <td><span class="badge bg-info text-dark fw-bold">{{ $st->devices->count() }} ชิ้น</span></td>
                                <td class="fw-bold text-danger">> {{ number_format($st->max_turbidity_ntu, 1) }} NTU</td>
                                <td class="fw-bold text-warning">{{ $st->min_ph }} - {{$st->max_ph }}</td>
                                <td class="fw-bold text-primary">{{ $st->max_water_level_percent }}%</td>
                                <td class="fw-bold text-dark">{{ $st->secchi_k_factor }}</td>
                                <td class="text-end text-nowrap">
                                    <a href="{{ route('tabwater.settings.stations.edit', $st->id) }}" class="btn btn-sm btn-outline-primary fw-bold me-1">
                                        แก้ไข
                                    </a>
                                    <form action="{{ route('tabwater.settings.stations.destroy', $st->id) }}" method="POST" class="d-inline" onsubmit="return confirm('ยืนยันการลบสถานีนี้?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger fw-bold">ลบ</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">ยังไม่มีข้อมูลสถานีผลิตน้ำ</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-3">
        {{ $stations->links() }}
    </div>
</div>

<!-- Script โหลด Google Maps API -->
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA-5AlIGzLhFXErl2STRT6GacX0616iW2o&callback=initOverviewMap"></script>
<script>
    // แปลงข้อมูล PHP $allStationsMap เป็น JSON อาร์เรย์สำหรับ JavaScript
    const stationsData = @json($allStationsMap);

    function initOverviewMap() {
        // ค่าพิกัดเริ่มต้นกรณีไม่มีข้อมูลสถานี (Default: สกลนคร)
        let defaultLat = 17.154100;
        let defaultLng = 104.148600;

        if (stationsData.length > 0) {
            defaultLat = parseFloat(stationsData[0].lat);
            defaultLng = parseFloat(stationsData[0].lng);
        }

        // 1. สร้าง Google Map
        const map = new google.maps.Map(document.getElementById('allStationsMap'), {
            center: { lat: defaultLat, lng: defaultLng },
            zoom: 12,
            mapTypeControl: true,
            streetViewControl: false,
        });

        const infoWindow = new google.maps.InfoWindow();
        const bounds = new google.maps.LatLngBounds();

        // 2. วนลูปปักหมุดตามสถานีทั้งหมด
        stationsData.forEach(function(station) {
            const lat = parseFloat(station.lat);
            const lng = parseFloat(station.lng);

            if (!isNaN(lat) && !isNaN(lng)) {
                const pos = { lat: lat, lng: lng };

                // สร้าง Marker สำหรับแต่ละสถานี
                const marker = new google.maps.Marker({
                    position: pos,
                    map: map,
                    title: station.name,
                    animation: google.maps.Animation.DROP
                });

                // ขยาย Bounds ของแผนที่ให้ครอบคลุมทุกหมุด
                bounds.extend(pos);

                // เนื้อหา Pop-up
                const contentString = `
                    <div style="padding: 6px; font-family: sans-serif; font-size: 12px; line-height: 1.4;">
                        <div style="font-weight: bold; color: #0d6efd; font-size: 14px; margin-bottom: 4px;">
                            🏭 ${station.name} (${station.code || '-'})
                        </div>
                        <div style="color: #495057; margin-bottom: 6px;">
                            <b>พิกัด:</b> ${lat.toFixed(6)}, ${lng.toFixed(6)}<br>
                            <b>เกณฑ์ NTU:</b> <span style="color: red;">> ${station.max_turbidity_ntu}</span> | 
                            <b>เกณฑ์ pH:</b> ${station.min_ph} - ${station.max_ph}
                        </div>
                        <div style="text-align: right;">
                            <a href="/tabwater/settings/stations/${station.id}/edit" 
                               style="display: inline-block; padding: 2px 8px; background: #0d6efd; color: white; text-decoration: none; border-radius: 4px; font-size: 11px; font-weight: bold;">
                                ✏️ แก้ไขสถานี
                            </a>
                        </div>
                    </div>
                `;

                // Hover (mouseover) -> แสดง Pop-up
                marker.addListener('mouseover', function() {
                    infoWindow.setContent(contentString);
                    infoWindow.open(map, marker);
                });

                // Click -> ซูมเข้าไปที่สถานีนั้น
                marker.addListener('click', function() {
                    map.setZoom(15);
                    map.setCenter(marker.getPosition());
                    infoWindow.setContent(contentString);
                    infoWindow.open(map, marker);
                });
            }
        });

        // 3. ปรับแผนที่อัตโนมัติให้เห็นหมุดสถานีครบทุกจุด
        if (stationsData.length > 1) {
            map.fitBounds(bounds);
        }
    }
</script>
@endsection