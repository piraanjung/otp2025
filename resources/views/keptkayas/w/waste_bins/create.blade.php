@extends('layouts.keptkaya')

@section('nav-header', 'เพิ่มถังขยะใหม่')
@section('nav-current', 'เพิ่มถังขยะใหม่')

@section('style')
<style>
    .form-control:read-only {
        background-color: #e9ecef;
        opacity: 1;
        cursor: not-allowed;
    }
    .map-container {
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }
</style>
@endsection

@section('content')
    <div class="container-fluid py-4">
        
        {{-- 1. ส่วนแสดงข้อมูลผู้ใช้งาน (User Context) --}}
        <div class="row mb-4">
            <div class="col-12 col-md-10 col-lg-8 mx-auto">
                <div class="card card-body border-0 shadow-sm">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-lg bg-gradient-primary me-3 text-white text-center rounded-circle shadow">
                            <i class="fas fa-user fa-lg mt-2"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 font-weight-bold">{{ $w_user->firstname }} {{ $w_user->lastname }}</h6>
                            <p class="text-xs text-secondary mb-0">
                                <i class="fas fa-map-marker-alt me-1"></i> {{ $w_user->address ?? 'ไม่ระบุที่อยู่' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 col-md-10 col-lg-8 mx-auto">
                <form action="{{ route('keptkayas.waste_bins.store', $w_user->id) }}" method="POST">
                    @csrf
                    
                    {{-- 2. การ์ดฟอร์มหลัก --}}
                    <div class="card mb-4">
                        <div class="card-header pb-0">
                            <h6 class="font-weight-bolder text-primary">
                                <i class="fas fa-trash-alt me-2"></i>ข้อมูลถังขยะ
                            </h6>
                            <hr class="horizontal dark mt-2 mb-0">
                        </div>
                        
                        <div class="card-body">
                            {{-- Section: Bin Info --}}
                            <div class="row g-3">
                                <div class="col-12 col-md-4">
                                    <label for="bin_code" class="form-label font-weight-bold">รหัสถังขยะ</label>
                                    <div class="input-group input-group-outline">
                                        <input type="text" class="form-control fw-bold text-primary @error('bin_code') is-invalid @enderror"
                                            id="bin_code" readonly name="bin_code" value="{{ $bin_code }}">
                                    </div>
                                    @error('bin_code')<div class="text-danger text-xs mt-1">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-12 col-md-4">
                                    <label for="user_group" class="form-label font-weight-bold">ประเภทผู้ใช้งาน <span class="text-danger">*</span></label>
                                    <div class="input-group input-group-outline">
                                        <select name="user_group" id="user_group" class="form-control px-2 border rounded" required>
                                            <option value="">-- เลือกประเภท --</option>
                                            @foreach ($user_groups as $group)
                                                <option value="{{ $group->id }}" data-usergroupname="{{$group->usergroup_name}}">
                                                    {{ $group->usergroup_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('user_group')<div class="text-danger text-xs mt-1">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-12 col-md-4">
                                    <label for="bin_type" class="form-label font-weight-bold">ประเภทถัง (Auto)</label>
                                    <div class="input-group input-group-outline">
                                        <input type="text" class="form-control @error('bin_type') is-invalid @enderror"
                                            id="bin_type" name="bin_type" value="{{ old('bin_type') }}" readonly placeholder="รอการเลือกประเภท...">
                                    </div>
                                    @error('bin_type')<div class="text-danger text-xs mt-1">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            {{-- Section: Location & Map --}}
                            <div class="row mt-4">
                                <h6 class="font-weight-bolder text-primary mb-3">
                                    <i class="fas fa-map-marked-alt me-2"></i>ตำแหน่งที่ตั้ง
                                </h6>
                                
                                <div class="col-12 mb-3">
                                    <label for="location_description" class="form-label font-weight-bold">รายละเอียดจุดตั้งถัง</label>
                                    <div class="input-group input-group-outline">
                                        <textarea class="form-control @error('location_description') is-invalid @enderror"
                                        id="location_description" name="location_description"
                                        rows="2" placeholder="เช่น หน้าบ้าน ติดเสาไฟฟ้า...">{{ $w_user->address }}</textarea>
                                    </div>
                                    @error('location_description')<div class="text-danger text-xs mt-1">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-12">
                                    <div class="map-container mb-3 border">
                                        <div id="map" style="height: 400px; width: 100%;"></div>
                                        <div class="bg-gray-100 p-2 text-center text-xs text-secondary border-top">
                                            <i class="fas fa-hand-pointer me-1"></i> คลิกบนแผนที่ หรือลากหมุดเพื่อระบุตำแหน่งที่แน่นอน
                                        </div>
                                    </div>
                                </div>

                                <div class="col-6 col-md-4">
                                    <label class="form-label text-xs">Lat</label>
                                    <input type="number" step="any" class="form-control form-control-sm border ps-2 bg-light" 
                                           id="latitude" name="latitude" value="{{ old('latitude') }}" readonly>
                                </div>
                                <div class="col-6 col-md-4">
                                    <label class="form-label text-xs">Long</label>
                                    <input type="number" step="any" class="form-control form-control-sm border ps-2 bg-light" 
                                           id="longitude" name="longitude" value="{{ old('longitude') }}" readonly>
                                </div>
                            </div>

                            <hr class="horizontal dark my-4">

                            {{-- Section: Status & Settings --}}
                            <div class="row align-items-center">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <label for="status" class="form-label font-weight-bold">สถานะเริ่มต้น</label>
                                    <select class="form-select px-3 border @error('status') is-invalid @enderror" id="status" name="status" required>
                                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>🟢 Active (ใช้งานปกติ)</option>
                                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>⚪ Inactive (ยังไม่ใช้งาน)</option>
                                        <option value="damaged" {{ old('status') == 'damaged' ? 'selected' : '' }}>🟠 Damaged (ชำรุด)</option>
                                        <option value="removed" {{ old('status') == 'removed' ? 'selected' : '' }}>🔴 Removed (ยกเลิก/ถอนถัง)</option>
                                    </select>
                                    @error('status')<div class="text-danger text-xs mt-1">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-md-6">
                                    <div class="p-3 border rounded bg-gray-100 d-flex align-items-center justify-content-between">
                                        <div>
                                            <h6 class="mb-0 text-sm font-weight-bold">บริการรายปี</h6>
                                            <p class="text-xs text-secondary mb-0">คิดค่าบริการแบบเหมาจ่ายรายปี</p>
                                        </div>
                                        <div class="form-check form-switch ps-0">
                                            <input class="form-check-input ms-auto" type="checkbox"
                                                id="is_active_for_annual_collection" name="is_active_for_annual_collection"
                                                value="1" {{ old('is_active_for_annual_collection', true) ? 'checked' : '' }}>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- Actions --}}
                            <div class="d-flex justify-content-end mt-4 pt-3">
                                <a href="{{ route('keptkayas.waste_bins.index', $w_user->id) }}" class="btn btn-light me-2">
                                    <i class="fas fa-times me-1"></i> ยกเลิก
                                </a>
                                <button type="submit" class="btn bg-gradient-primary">
                                    <i class="fas fa-save me-1"></i> บันทึกข้อมูล
                                </button>
                            </div>

                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            // Logic เดิม: เลือก User Group แล้ว Auto Fill Bin Type
            $('#user_group').on('change', function () {
                var selectedOption = $(this).find('option:selected');
                var userGroupName = selectedOption.data('usergroupname');

                if (userGroupName) {
                    $('#bin_type').val(userGroupName).addClass('is-valid').removeClass('is-invalid');
                } else {
                    $('#bin_type').val('').removeClass('is-valid');
                }
            });
        });

        // Google Maps Logic (เหมือนเดิมแต่ปรับ Default ให้ Robust ขึ้น)
        function initMap() {
            // Fallback location (Bangkok or user zone)
            const zoneLat = parseFloat("{{ $w_user->org->lat ?? 0 }}");
            const zoneLong = parseFloat("{{ $w_user->org->long ?? 0 }}");
            
            // ถ้า User Zone ไม่มีค่า ให้ใช้ Default กลางๆ หรือพิกัดไทย
            const defaultLat = zoneLat || 13.7563; 
            const defaultLng = zoneLong || 100.5018;

            const initialLat = parseFloat("{{ old('latitude') }}") || defaultLat;
            const initialLng = parseFloat("{{ old('longitude') }}") || defaultLng;

            const initialLocation = { lat: initialLat, lng: initialLng };

            const map = new google.maps.Map(document.getElementById("map"), {
                zoom: 15,
                center: initialLocation,
                mapTypeId: "roadmap",
                streetViewControl: false, // ปิด street view ให้ดูสะอาด
                mapTypeControl: false
            });

            const marker = new google.maps.Marker({
                position: initialLocation,
                map: map,
                draggable: true,
                animation: google.maps.Animation.DROP,
                title: "ตำแหน่งถังขยะ"
            });

            function updateInputs(latLng) {
                document.getElementById('latitude').value = latLng.lat().toFixed(6);
                document.getElementById('longitude').value = latLng.lng().toFixed(6);
            }

            map.addListener("click", (event) => {
                const latLng = event.latLng;
                marker.setPosition(latLng);
                updateInputs(latLng);
            });

            marker.addListener('dragend', (event) => {
                updateInputs(marker.getPosition());
            });

            // Set initial values
            if(!document.getElementById('latitude').value) {
                updateInputs({ lat: () => initialLat, lng: () => initialLng });
            }
        }
    </script>
    <script async defer
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA-5AlIGzLhFXErl2STRT6GacX0616iW2o&callback=initMap"></script>
@endsection