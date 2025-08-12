@extends('layouts.keptkaya')

@section('title_page', 'เพิ่มถังขยะใหม่')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <h6>เพิ่มถังขยะใหม่สำหรับ: {{ $w_user->firstname }} {{ $w_user->lastname }}</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('keptkaya.waste_bins.store', $w_user->id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="bin_code" class="form-label">รหัสถังขยะ </label>
                            <input type="text" class="form-control @error('bin_code') is-invalid @enderror" id="bin_code" readonly name="bin_code" value="{{ $bin_code }}">
                            @error('bin_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="user_group" class="form-label">ประเภทถังขยะ</label>
                            <select name="user_group" id="user_group" class="form-control">
                                <option>เลือก...</option>
                                @foreach ($user_groups as $group)
                                    <option value="{{ $group->id }}" data-usergroupname="{{$group->usergroup_name}}">{{ $group->usergroup_name }}</option>
                                @endforeach
                            </select>
                            {{-- <input type="text" class="form-control @error('bin_type') is-invalid @enderror" id="bin_type" name="bin_type" value="{{ old('bin_type') }}" required> --}}
                            @error('bin_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="bin_type" class="form-label">&nbsp;</label>
                            <input type="text" class="form-control @error('bin_type') is-invalid @enderror" id="bin_type" name="bin_type" value="{{ old('bin_type') }}" readonly required>
                            @error('bin_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="location_description" class="form-label">รายละเอียดตำแหน่งที่ตั้ง</label>
                            <textarea class="form-control @error('location_description') is-invalid @enderror" id="location_description" name="location_description" rows="3">{{ $w_user->address }}</textarea>
                            @error('location_description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="latitude" class="form-label">ละติจูด</label>
                                    <input type="number" step="any" class="form-control @error('latitude') is-invalid @enderror" id="latitude" name="latitude" value="{{ old('latitude') }}" readonly>
                                    @error('latitude')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="longitude" class="form-label">ลองจิจูด</label>
                                    <input type="number" step="any" class="form-control @error('longitude') is-invalid @enderror" id="longitude" name="longitude" value="{{ old('longitude') }}" readonly>
                                    @error('longitude')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>

                        {{-- Map Section --}}
                        <div class="mb-3">
                            <label class="form-label">ปักหมุดตำแหน่งถังขยะ</label>
                            <div id="map" style="height: 400px; width: 100%; border-radius: 8px;"></div>
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">สถานะของถัง</label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="damaged" {{ old('status') == 'damaged' ? 'selected' : '' }}>Damaged</option>
                                <option value="removed" {{ old('status') == 'removed' ? 'selected' : '' }}>Removed</option>
                            </select>
                            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <div class="form-check form-switch ps-0">
                                <input class="form-check-input ms-auto" type="checkbox" id="is_active_for_annual_collection" name="is_active_for_annual_collection" value="1" {{ old('is_active_for_annual_collection', true) ? 'checked' : '' }}>
                                <label class="form-check-label text-body ms-3 text-truncate w-80 mb-0" for="is_active_for_annual_collection">
                                    ถังนี้ใช้งานสำหรับบริการเก็บขยะรายปี
                                </label>
                            </div>
                        </div>
                        <button type="submit" class="btn bg-gradient-primary">บันทึกถังขยะ</button>
                        <a href="{{ route('keptkaya.waste_bins.index', $w_user->id) }}" class="btn btn-secondary">ยกเลิก</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
    <script>
        $(document).on('change', '#user_group', function(){
            var selectedOption = $(this).find('option:selected');

        // 2. ใช้ .data('ชื่อแอตทริบิวต์') เพื่อดึงค่าจาก data-usergroupname
        var userGroupName = selectedOption.data('usergroupname');

        // 3. แสดงค่าที่ได้ (คุณสามารถนำค่านี้ไปใช้งานต่อได้)
        if (userGroupName) {
            $('#bin_type').val(userGroupName)
           
        } else {
            console.log('ไม่ได้เลือกกลุ่มผู้ใช้งาน หรือ option ที่เลือกไม่มี data-usergroupname');
        }
        });

        // Initialize Google Maps
        function initMap() {
            // Check for existing values from old() or fall back to a specific village
            // หาค่าละติจูดและลองจิจูดของหมู่บ้านที่ต้องการจาก Google Maps
            const defaultLat = 17.3333436; // ตัวอย่าง: ละติจูดของหมู่บ้านสมมติในประเทศไทย
            const defaultLng = 103.6683659; // ตัวอย่าง: ลองจิจูดของหมู่บ้านสมมติในประเทศไทย
            
            const initialLat = parseFloat("{{ old('latitude', '') }}") || defaultLat;
            const initialLng = parseFloat("{{ old('longitude', '') }}") || defaultLng;

            const initialLocation = { lat: initialLat, lng: initialLng };

            const map = new google.maps.Map(document.getElementById("map"), {
                zoom: 15, // ซูมเข้ามามากขึ้นเพื่อให้เห็นรายละเอียดของหมู่บ้าน
                center: initialLocation,
                mapTypeId: "roadmap",
            });

            // Create a marker that can be moved
            const marker = new google.maps.Marker({
                position: initialLocation,
                map: map,
                draggable: true,
            });
            
            // Update form fields when the map is clicked
            map.addListener("click", (event) => {
                const latLng = event.latLng;
                marker.setPosition(latLng);
                document.getElementById('latitude').value = latLng.lat().toFixed(6);
                document.getElementById('longitude').value = latLng.lng().toFixed(6);
            });

            // Update form fields when the marker is dragged
            marker.addListener('dragend', (event) => {
                const latLng = marker.getPosition();
                document.getElementById('latitude').value = latLng.lat().toFixed(6);
                document.getElementById('longitude').value = latLng.lng().toFixed(6);
            });

            // Set initial values if they exist
            document.getElementById('latitude').value = initialLat.toFixed(6);
            document.getElementById('longitude').value = initialLng.toFixed(6);
        }
    </script>
    <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA-5AlIGzLhFXErl2STRT6GacX0616iW2o&callback=initMap"></script>
@endsection
