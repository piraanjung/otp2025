@extends('layouts.admin1') 

@section('content') 
<div class="container mt-4 mb-5"> 
    <div class="card shadow-sm border-0"> 
        <div class="card-header bg-white py-3"> 
            <h4 class="mb-0">➕ ลงทะเบียนสร้างตู้ Kiosk ใหม่เข้าหน่วยงาน</h4> 
        </div> 
        <div class="card-body"> 
            <form action="{{ route('keptkayas.kiosks.store') }}" method="POST" id="kioskForm"> 
                @csrf 

                <!-- 1. เลือกหน่วยงาน/องค์กร -->
                <div class="mb-4">
                    <label for="org_id_fk" class="form-label fw-bold">เลือกหน่วยงาน / องค์กร <span class="text-danger">*</span></label>
                    <select name="org_id_fk" id="org_id_fk" class="form-select @error('org_id_fk') is-invalid @enderror" onchange="onOrgChange(this.value)" required>
                        <option value="">-- เลือกหน่วยงาน --</option>
                        @foreach($orgs as $orgItem)
                            <option value="{{ $orgItem->id }}" 
                                data-lat="{{ $orgItem->lat ?? $orgItem->latitude ?? '' }}" 
                                data-lng="{{ $orgItem->long ?? $orgItem->lng ?? $orgItem->longitude ?? '' }}"
                                {{ old('org_id_fk', $org->id ?? '') == $orgItem->id ? 'selected' : '' }}>
                                {{ $orgItem->orgType->name. $orgItem->org_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('org_id_fk') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- 2. ส่วนฟอร์มปักหมุดระบุตู้ใหม่ -->
                <div class="card border mb-4 bg-light">
                    <div class="card-body">
                        <h5 class="fw-bold text-primary mb-3"><i class="bi bi-geo-alt-fill me-1"></i> ระบุข้อมูลตู้และปักหมุดตำแหน่งบนแผนที่</h5>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="temp_id" class="form-label fw-semibold">รหัสตู้ Kiosk (ID) <span class="text-danger">*</span></label>
                                <input type="text" id="temp_id" class="form-control" placeholder="เช่น KSK-001">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="temp_name" class="form-label fw-semibold">ชื่อจุดติดตั้ง <span class="text-danger">*</span></label>
                                <input type="text" id="temp_name" class="form-control" placeholder="เช่น ตู้หน้าอาคารเรียน 1">
                            </div>
                        </div>

                        <!-- แผนที่ปักหมุด Leaflet -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">📍 คลิกแผนที่เพื่อปักหมุด หรือลากหมุดไปยังพิกัดจริง</label>
                            <div id="map" style="height: 320px; width: 100%; class="rounded border shadow-sm mb-2""></div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="temp_lat" class="form-label">ละติจูด (Latitude)</label>
                                <input type="text" id="temp_lat" class="form-control" value="{{ $org->lat ?? '13.7563' }}" onchange="updateMapFromInput()">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="temp_lng" class="form-label">ลองจิจูด (Longitude)</label>
                                <input type="text" id="temp_lng" class="form-control" value="{{ $org->long ?? '100.5018' }}" onchange="updateMapFromInput()">
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="button" class="btn btn-success px-3" onclick="addKioskToList()">
                                ➕ เพิ่มตู้ลงในรายการลงทะเบียน
                            </button>
                        </div>
                    </div>
                </div>

                <!-- 3. ตารางตู้ที่จะทำการบันทึกลงฐานข้อมูล (Kiosk Array Table) -->
                <div class="mb-4">
                    <h5 class="fw-bold mb-3">📋 รายการตู้คีออสก์ที่จะบันทึก (<span id="kioskCount">0</span> ตู้)</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle bg-white" id="kioskTable">
                            <thead class="table-dark">
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th>รหัสตู้ (ID)</th>
                                    <th>ชื่อจุดติดตั้ง</th>
                                    <th>ละติจูด (Lat)</th>
                                    <th>ลองจิจูด (Lng)</th>
                                    <th style="width: 80px;" class="text-center">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody id="kioskTableBody">
                                <tr id="emptyRow">
                                    <td colspan="6" class="text-center text-muted py-4">ยังไม่มีรายการตู้คีออสก์ถูกเพิ่ม กรุณากรอกข้อมูลและปักหมุดด้านบน แล้วกด "เพิ่มตู้ลงในรายการ"</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    @error('kiosks') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>

                <!-- Container เก็บ Hidden Inputs สำหรับส่งค่า Form Post -->
                <div id="hiddenInputsContainer"></div>

                <div class="d-flex justify-content-between pt-3 border-top"> 
                    <a href="{{ route('keptkayas.kiosks.index') }}" class="btn btn-secondary">ยกเลิก</a> 
                    <button type="submit" class="btn btn-primary px-4" id="submitBtn" disabled>บันทึกการลงทะเบียนตู้ทั้งหมด</button> 
                </div> 
            </form> 
        </div> 
    </div> 
</div> 

<!-- Leaflet CSS & JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    let map, mainMarker;
    let mapMarkers = [];
    let kioskList = [];

    document.addEventListener("DOMContentLoaded", function () {
        let defaultLat = parseFloat(document.getElementById('temp_lat').value) || 13.7563;
        let defaultLng = parseFloat(document.getElementById('temp_lng').value) || 100.5018;

        // สร้่างแผนที่ Leaflet
        map = L.map('map').setView([defaultLat, defaultLng], 14);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // หมุดหลักสำหรับเลือกพิกัด
        mainMarker = L.marker([defaultLat, defaultLng], { draggable: true }).addTo(map);

        mainMarker.on('dragend', function () {
            const pos = mainMarker.getLatLng();
            setTempInputs(pos.lat, pos.lng);
        });

        map.on('click', function (e) {
            mainMarker.setLatLng(e.latlng);
            setTempInputs(e.latlng.lat, e.latlng.lng);
        });
    });

    // เปลี่ยนหน่วยงานแล้วอัปเดตตำแหน่งจุดเริ่มต้นแผนที่
    function onOrgChange(orgId) {
        const select = document.getElementById('org_id_fk');
        const selectedOption = select.options[select.selectedIndex];
        
        const orgLat = parseFloat(selectedOption.getAttribute('data-lat'));
        const orgLng = parseFloat(selectedOption.getAttribute('data-lng'));

        if (!isNaN(orgLat) && !isNaN(orgLng)) {
            setTempInputs(orgLat, orgLng);
            if (map && mainMarker) {
                mainMarker.setLatLng([orgLat, orgLng]);
                map.setView([orgLat, orgLng], 15);
            }
        }
    }

    function setTempInputs(lat, lng) {
        document.getElementById('temp_lat').value = parseFloat(lat).toFixed(6);
        document.getElementById('temp_lng').value = parseFloat(lng).toFixed(6);
    }

    function updateMapFromInput() {
        const lat = parseFloat(document.getElementById('temp_lat').value);
        const lng = parseFloat(document.getElementById('temp_lng').value);

        if (!isNaN(lat) && !isNaN(lng) && map && mainMarker) {
            mainMarker.setLatLng([lat, lng]);
            map.panTo([lat, lng]);
        }
    }

    // เพิ่มตู้ลงในรายการ Array
    function addKioskToList() {
        const id = document.getElementById('temp_id').value.trim();
        const name = document.getElementById('temp_name').value.trim();
        const lat = document.getElementById('temp_lat').value.trim();
        const lng = document.getElementById('temp_lng').value.trim();

        if (!id || !name || !lat || !lng) {
            alert('กรุณากรอกรหัสตู้ ชื่อจุดติดตั้ง และปักหมุดพิกัดให้ครบถ้วน');
            return;
        }

        // ตรวจสอบรหัสตู้ซ้ำในรายการ
        if (kioskList.some(k => k.id === id)) {
            alert('รหัสตู้นี้ถูกเพิ่มไว้ในรายการแล้ว');
            return;
        }

        const kioskItem = { id, name, lat, lng };
        kioskList.push(kioskItem);

        // ปักหมุดถาวรบนแผนที่สำหรับตู้ที่เพิ่มแล้ว
        const addedMarker = L.marker([parseFloat(lat), parseFloat(lng)])
            .addTo(map)
            .bindPopup(`<b>${name}</b><br>ID: ${id}`);
        mapMarkers.push({ id: id, marker: addedMarker });

        // ล้างช่องกรอกเพื่อเตรียมกรอกตู้ถัดไป
        document.getElementById('temp_id').value = '';
        document.getElementById('temp_name').value = '';

        renderKioskTable();
    }

    // ลบตู้ดูออกจากรายการ Array
    function removeKiosk(index) {
        const removedItem = kioskList[index];
        
        // ลบหมุดออกจากแผนที่
        const markerObj = mapMarkers.find(m => m.id === removedItem.id);
        if (markerObj) {
            map.removeLayer(markerObj.marker);
            mapMarkers = mapMarkers.filter(m => m.id !== removedItem.id);
        }

        kioskList.splice(index, 1);
        renderKioskTable();
    }

    // เรนเดอร์สร้างตารางและ Hidden Inputs
    function renderKioskTable() {
        const tbody = document.getElementById('kioskTableBody');
        const container = document.getElementById('hiddenInputsContainer');
        const submitBtn = document.getElementById('submitBtn');
        const countSpan = document.getElementById('kioskCount');

        tbody.innerHTML = '';
        container.innerHTML = '';
        countSpan.innerText = kioskList.length;

        if (kioskList.length === 0) {
            tbody.innerHTML = `
                <tr id="emptyRow">
                    <td colspan="6" class="text-center text-muted py-4">ยังไม่มีรายการตู้คีออสก์ถูกเพิ่ม กรุณากรอกข้อมูลและปักหมุดด้านบน แล้วกด "เพิ่มตู้ลงในรายการ"</td>
                </tr>`;
            submitBtn.disabled = true;
            return;
        }

        submitBtn.disabled = false;

        kioskList.forEach((kiosk, idx) => {
            // สร้าง Row ใน Table
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${idx + 1}</td>
                <td><strong>${kiosk.id}</strong></td>
                <td>${kiosk.name}</td>
                <td>${kiosk.lat}</td>
                <td>${kiosk.lng}</td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-danger" onclick="removeKiosk(${idx})">
                        <i class="bi bi-trash"></i> ลบ
                    </button>
                </td>
            `;
            tbody.appendChild(tr);

            // สร้าง Hidden Input ส่งค่าเข้า Controller แบบ Array kiosks[0][id], kiosks[0][name], ...
            container.innerHTML += `
                <input type="hidden" name="kiosks[${idx}][id]" value="${kiosk.id}">
                <input type="hidden" name="kiosks[${idx}][name]" value="${kiosk.name}">
                <input type="hidden" name="kiosks[${idx}][lat]" value="${kiosk.lat}">
                <input type="hidden" name="kiosks[${idx}][lng]" value="${kiosk.lng}">
            `;
        });
    }
</script>
@endsection