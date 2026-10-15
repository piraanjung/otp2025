@extends('layouts.admin1')

@section('content')
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
                        <input type="text" name="name" value="{{ old('name', $node->name) }}" required class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-secondary">ประเภทโหนด *</label>
                        @php $type = old('type', $node->type); @endphp
                        <select name="type" required class="form-select fw-bold">
                            <option value="main_junction" {{ $type === 'main_junction' ? 'selected' : '' }}>จุดแยกท่อเมนหลัก</option>
                            <option value="sub_junction" {{ $type === 'sub_junction' ? 'selected' : '' }}>จุดแยกท่อรอง/ท่อซอย</option>
                            <option value="valve_point" {{ $type === 'valve_point' ? 'selected' : '' }}>จุดติดตั้งวาล์ว</option>
                            <option value="tank" {{ $type === 'tank' ? 'selected' : '' }}>ถังพักน้ำ/หอถังสูง</option>
                            <option value="end_point" {{ $type === 'end_point' ? 'selected' : '' }}>จุดปลายท่อ/บ้านผู้ใช้</option>
                            <option value="station" {{ $type === 'station' ? 'selected' : '' }}>โรงผลิตน้ำต้นทาง</option>
                        </select>
                    </div>
                </div>

                <!-- Google Maps ปักหมุดย้ายพิกัดโหนด -->
                <div class="mb-3">
                    <label class="form-label fw-bold text-primary mb-1">🗺️ ปรับย้ายตำแหน่งพิกัดจุดโหนดบนแผนที่:</label>
                    <div id="nodeMap" style="height: 300px; width: 100%; border-radius: 8px; border: 1px solid #dee2e6;" class="mb-2"></div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text fw-bold bg-light">Lat</span>
                                <input type="text" id="latInput" name="lat" value="{{ old('lat', $node->lat) }}" readonly class="form-control fw-bold text-primary">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text fw-bold bg-light">Long</span>
                                <input type="text" id="lngInput" name="long" value="{{ old('long', $node->long) }}" readonly class="form-control fw-bold text-primary">
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

<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA-5AlIGzLhFXErl2STRT6GacX0616iW2o&callback=initMap"></script>
<script>
    let map, marker;

    function initMap() {
        const latInput = document.getElementById('latInput');
        const lngInput = document.getElementById('lngInput');

        const defaultLat = parseFloat(latInput.value) || 17.154100;
        const defaultLng = parseFloat(lngInput.value) || 104.148600;
        const pos = { lat: defaultLat, lng: defaultLng };

        map = new google.maps.Map(document.getElementById('nodeMap'), {
            center: pos,
            zoom: 15,
            mapTypeControl: true,
            streetViewControl: false,
        });

        marker = new google.maps.Marker({
            position: pos,
            map: map,
            draggable: true,
            title: "ลากเพื่อย้ายพิกัดโหนด"
        });

        function updateInputs(lat, lng) {
            latInput.value = lat.toFixed(6);
            lngInput.value = lng.toFixed(6);
        }

        map.addListener('click', function(e) {
            marker.setPosition(e.latLng);
            updateInputs(e.latLng.lat(), e.latLng.lng());
        });

        marker.addListener('dragend', function(e) {
            updateLocation(e.latLng.lat(), e.latLng.lng());
        });
    }
</script>
@endsection