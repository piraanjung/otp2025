@extends('layouts.print') {{-- หรือ layout หลักของคุณ --}}

@section('style')
    <link rel="stylesheet" href="{{ asset('adminlte/dist/css/adminlte.css') }}">
    <style>
        /* กำหนดความสูงของแผนที่ */
        #map {
            height: 400px;
            width: 100%;
            border-radius: 10px;
            margin-bottom: 15px;
        }

        .preview-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 10px;
        }

        .preview-card {
            position: relative;
            width: 100px;
            height: 100px;
            border-radius: 8px;
            overflow: hidden;
            border: 2px solid #ddd;
        }

        .preview-card img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .preview-card .btn-remove {
            position: absolute;
            top: 2px;
            right: 2px;
            background: rgba(220, 53, 69, 0.85);
            color: #fff;
            border: none;
            border-radius: 50%;
            width: 22px;
            height: 22px;
            font-size: 12px;
            line-height: 1;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .preview-card .btn-remove:hover {
            background: rgba(220, 53, 69, 1);
        }
    </style>
@endsection

@section('content')
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h4>แจ้งเหตุ {{ $systemTypeModule[0]->title ?? 'ระบบประปา' }}</h4>
        </div>
        <div class="card-body">

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <form id="notifyForm" action="{{ route('tabwater.notify.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="system_type" id="system_type" value="{{ $systemType }}">
                <input type="hidden" name="user_id" id="user_id" value="">
                
                <div class="row mb-3">
                    <div class="col-md-6 mb-2 mb-md-0">
                        <label for="reporter_phone" class="form-label">เบอร์โทรศัพท์ผู้แจ้ง <span class="text-danger">*</span></label>
                        <input type="tel" name="reporter_phone" id="reporter_phone" class="form-control @error('reporter_phone') is-invalid @enderror"
                            placeholder="กรอกเบอร์โทร 10 หลัก" maxlength="10" value="{{ old('reporter_phone') }}" required>
                        <div id="member-status-text" class="form-text"></div>
                        @error('reporter_phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="reporter_name" class="form-label">ชื่อ-นามสกุล ผู้แจ้ง <span class="text-danger">*</span></label>
                        <input type="text" name="reporter_name" id="reporter_name" class="form-control @error('reporter_name') is-invalid @enderror"
                            placeholder="กรอกชื่อ-นามสกุล" value="{{ old('reporter_name') }}" required>
                        @error('reporter_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="issue_type" class="form-label">ประเภทปัญหา <span class="text-danger">*</span></label>
                    <select name="issue_type" id="issue_type" class="form-control @error('issue_type') is-invalid @enderror" required>
                        <option value="">-- เลือกปัญหา --</option>
                       @foreach ($issueTypes as $item)
                        <option value="{{ $item->id }}" {{ old('issue_type') == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                       @endforeach
                        <option value="other" {{ old('issue_type') == 'other' ? 'selected' : '' }}>อื่นๆ</option>
                    </select>
                    @error('issue_type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3 d-none" id="custom_issue_wrapper">
                    <label for="custom_issue_type" class="form-label fw-bold">ระบุประเภทปัญหาเพิ่มเติม <span class="text-danger">*</span></label>
                    <input type="text" name="custom_issue_type" id="custom_issue_type" class="form-control @error('custom_issue_type') is-invalid @enderror" placeholder="กรุณาระบุปัญหาที่พบ" value="{{ old('custom_issue_type') }}">
                    @error('custom_issue_type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">รายละเอียดเพิ่มเติม</label>
                    <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="3"
                        placeholder="ระบุจุดสังเกต หรือรายละเอียด...">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">ระบุตำแหน่ง (ลากหมุดเพื่อปรับตำแหน่ง) <span class="text-danger">*</span></label>
                    <div id="map"></div>
                    <div class="row">
                        <div class="col">
                            <input type="text" name="latitude" id="latitude" class="form-control @error('latitude') is-invalid @enderror" placeholder="Latitude"
                                value="{{ old('latitude') }}" readonly required>
                            @error('latitude')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col">
                            <input type="text" name="longitude" id="longitude" class="form-control @error('longitude') is-invalid @enderror" placeholder="Longitude"
                                value="{{ old('longitude') }}" readonly required>
                            @error('longitude')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">แนบรูปภาพประกอบ (ถ่ายภาพหรือเลือกจากคลัง)</label>
                    
                    <!-- ปุ่มกดเลือกถ่ายภาพ / คลังภาพ -->
                    <div class="d-flex gap-2 mb-3">
                        <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('cameraInput').click()">
                            <i class="bi bi-camera-fill me-1"></i> ถ่ายภาพ
                        </button>
                        <button type="button" class="btn btn-outline-secondary" onclick="document.getElementById('galleryInput').click()">
                            <i class="bi bi-images me-1"></i> เลือกจากคลังภาพ
                        </button>
                    </div>

                    <!-- Input สำหรับเลือกไฟล์ -->
                    <input type="file" id="cameraInput" class="d-none" accept="image/*" capture="environment" multiple onchange="handleFileSelect(this)">
                    <input type="file" id="galleryInput" class="d-none" accept="image/*" multiple onchange="handleFileSelect(this)">

                    <!-- แสดงตัวอย่างรูปภาพที่เลือก -->
                    <div id="previewContainer" class="preview-container"></div>
                    @error('photos.*')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" id="btnSubmit" class="btn btn-primary w-100 btn-lg">แจ้งเหตุ</button>
            </form>

            <div class="modal fade" id="gpsModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
                aria-labelledby="gpsModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-warning">
                            <h5 class="modal-title" id="gpsModalLabel">⚠️ ไม่พบตำแหน่งของคุณ</h5>
                        </div>
                        <div class="modal-body text-center">
                            <p class="mb-3" style="font-size: 1.1rem;">
                                ระบบไม่สามารถระบุพิกัดปัจจุบันได้ <br>
                                <strong>กรุณาเปิด GPS (Location Service)</strong> <br>
                                แล้วกดปุ่ม "ลองใหม่"
                            </p>
                            <p class="text-muted small">
                                *หากคุณกด "Block" หรือ "ไม่อนุญาต" ไปก่อนหน้านี้ <br>
                                กรุณาไปที่การตั้งค่า Browser เพื่อ Reset Permission
                            </p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary w-100" onclick="initMap()">
                                🔄 ลองใหม่ (Retry)
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/browser-image-compression@2.0.2/dist/browser-image-compression.js"></script>
    <script async defer
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA-5AlIGzLhFXErl2STRT6GacX0616iW2o&callback=initMap"></script>
    <script>
        let map;
        let marker;
        let selectedFiles = []; // อาร์เรย์สำหรับเก็บไฟล์ภาพทั้งหมดที่ถูกบีบอัดแล้ว

        $(document).ready(function () {
            // เช็คการเลือกประเภทปัญหา
            $('#issue_type').change(function() {
                toggleCustomIssue(this.value);
            });

            // ตรวจสอบสถานะเดิมเมื่อโหลดหน้า (กรณีกลับมาจาก Validation Error)
            if ($('#issue_type').val() === 'other') {
                toggleCustomIssue('other');
            }

            // เช็คเบอร์โทรศัพท์ผู้แจ้ง
            $('#reporter_phone').on('input keyup', function () {
                let phone = $(this).val().replace(/[^0-9]/g, '');
                $(this).val(phone);

                let statusText = $('#member-status-text');
                let nameInput = $('#reporter_name');
                let userIdInput = $('user_id');
                 if (phone.length === 10) {
                    statusText.removeClass('text-danger text-success').addClass('text-muted')
                        .html('⏳ กำลังตรวจสอบข้อมูลสมาชิก...');

                    $.ajax({
                        url: "{{ route('tabwater.notify.check_member') }}",
                        type: "GET",
                        data: { phone: phone },
                        dataType: "json",
                        success: function (response) {
                            if (response.found) {
                                statusText.removeClass('text-muted text-danger').addClass('text-success')
                                    .html('✅ พบข้อมูลสมาชิก: <strong>' + response.name + '</strong>');
                                nameInput.val(response.name);
                                userIdInput.val(response.user_id)
                            } else {
                                statusText.removeClass('text-muted text-success').addClass('text-warning text-dark')
                                    .html('ℹ️ ไม่พบข้อมูลสมาชิก (ท่านสามารถกรอกชื่อเพื่อแจ้งเหตุได้ตามปกติ)');
                            }
                        },
                        error: function () {
                            statusText.removeClass('text-muted text-success').addClass('text-danger')
                                .html('❌ ไม่สามารถเชื่อมต่อระบบตรวจสอบสมาชิกได้');
                        }
                    });
                } else {
                    statusText.html('');
                }
            });

            // ปรับการส่ง Form แบบ AJAX เพื่อแนบไฟล์ใน selectedFiles ส่งเข้า Backend
            $('#notifyForm').on('submit', function (e) {
                e.preventDefault();

                let form = this;
                let formData = new FormData(form);

                // ลบ photos[] เดิมที่อาจค้างใน FormData ออกก่อน
                formData.delete('photos[]');

                // แนบไฟล์ที่สะสมอยู่ใน selectedFiles Array
                selectedFiles.forEach((file) => {
                    formData.append('photos[]', file);
                });

                let btnSubmit = $('#btnSubmit');
                btnSubmit.prop('disabled', true).html('⏳ กำลังบันทึกข้อมูล...');

                $.ajax({
                    url: $(form).attr('action'),
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        alert('บันทึกข้อมูลเรียบร้อยแล้ว');
                        window.location.reload();
                    },
                    error: function (xhr) {
                        btnSubmit.prop('disabled', false).html('แจ้งเหตุ');
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            let errorMsg = 'กรุณาตรวจสอบข้อมูล:\n';
                            $.each(errors, function (key, value) {
                                errorMsg += '- ' + value[0] + '\n';
                            });
                            alert(errorMsg);
                        } else {
                            alert('เกิดข้อผิดพลาดในการบันทึกข้อมูล กรุณาลองใหม่อีกครั้ง');
                        }
                    }
                });
            });
        });

        // สลับแสดง/ซ่อนช่องระบุปัญหาเพิ่มเติม
        function toggleCustomIssue(val) {
            const wrapper = document.getElementById('custom_issue_wrapper');
            const input = document.getElementById('custom_issue_type');
            
            if (val === 'other') {
                wrapper.classList.remove('d-none');
                input.setAttribute('required', 'required');
            } else {
                wrapper.classList.add('d-none');
                input.removeAttribute('required');
                input.value = '';
            }
        }

        // ฟังก์ชันเริ่มทำงานแผนที่
        function initMap() {
            const defaultLocation = { lat: 13.756331, lng: 100.501845 };
            map = new google.maps.Map(document.getElementById("map"), {
                zoom: 15,
                center: defaultLocation,
            });

            marker = new google.maps.Marker({
                position: defaultLocation,
                map: map,
                draggable: true,
                animation: google.maps.Animation.DROP,
            });

            google.maps.event.addListener(marker, 'dragend', function (event) {
                updatePosition(event.latLng.lat(), event.latLng.lng());
            });

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const pos = {
                            lat: position.coords.latitude,
                            lng: position.coords.longitude,
                        };
                        map.setCenter(pos);
                        marker.setPosition(pos);
                        updatePosition(pos.lat, pos.lng);
                    },
                    () => {
                        handleLocationError(true, map.getCenter());
                    }
                );
            } else {
                handleLocationError(false, map.getCenter());
            }
        }

        function updatePosition(lat, lng) {
            $('#latitude').val(lat.toFixed(6));
            $('#longitude').val(lng.toFixed(6));
        }

        function handleLocationError(browserHasGeolocation, pos) {
            alert(browserHasGeolocation
                ? "ไม่สามารถระบุตำแหน่งปัจจุบันได้ (กรุณาเปิด Location Services)"
                : "เบราว์เซอร์ของคุณไม่รองรับ Geolocation");
        }

        // ฟังก์ชันจัดการการเลือกไฟล์หลายรูป + บีบอัดรูปภาพ + รวมเข้า selectedFiles Array
        async function handleFileSelect(input) {
            if (!input.files || input.files.length === 0) return;

            const options = {
                maxSizeMB: 0.5,
                maxWidthOrHeight: 1024,
                useWebWorker: true
            };

            for (let i = 0; i < input.files.length; i++) {
                const originalFile = input.files[i];
                try {
                    const compressedBlob = await imageCompression(originalFile, options);
                    const compressedFile = new File(
                        [compressedBlob],
                        originalFile.name,
                        { type: compressedBlob.type, lastModified: Date.now() }
                    );

                    // เพิ่มไฟล์เข้าไปใน Array หลัก
                    selectedFiles.push(compressedFile);
                } catch (error) {
                    console.error('Image compression failed:', error);
                }
            }

            // เคลียร์ค่า input เพื่อให้สามารถเลือกรูปเดิมซ้ำได้ในอนาคต
            input.value = '';

            // แสดงตัวอย่างรูปทั้งหมดใน selectedFiles
            renderPreviews();
        }

        // แสดงผล Image Previews
        function renderPreviews() {
            const container = document.getElementById('previewContainer');
            container.innerHTML = '';

            selectedFiles.forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = function (e) {
                    const card = document.createElement('div');
                    card.className = 'preview-card';
                    card.innerHTML = `
                        <img src="${e.target.result}" alt="preview">
                        <button type="button" class="btn-remove" onclick="removeFile(${index})">&times;</button>
                    `;
                    container.appendChild(card);
                };
                reader.readAsDataURL(file);
            });
        }

        // ลบรูปภาพออกจาก selectedFiles
        function removeFile(index) {
            selectedFiles.splice(index, 1);
            renderPreviews();
        }
    </script>
@endsection