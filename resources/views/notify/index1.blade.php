@extends('layouts.admin1')
@section('style')
    <style>
        /* กำหนดความสูงของแผนที่ */
        #map {
            height: 400px;
            width: 100%;
            border-radius: 10px;
            margin-bottom: 15px;
        }

        .preview-image {
            max-width: 100%;
            max-height: 300px;
            border-radius: 10px;
            display: none;
            /* ซ่อนไว้ก่อน */
            margin-top: 10px;
            border: 2px solid #ddd;
        }
    </style>
@endsection
@section('content')
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h4>แจ้งเหตุระบบน้ำประปา</h4>
        </div>
        <div class="card-body">

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <form action="{{ route('tabwater.notify.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row mb-3">
                    <div class="col-md-6 mb-2 mb-md-0">
                        <label for="reporter_phone" class="form-label">เบอร์โทรศัพท์ผู้แจ้ง <span
                                class="text-danger">*</span></label>
                        <input type="tel" name="reporter_phone" id="reporter_phone" class="form-control"
                            placeholder="กรอกเบอร์โทร 10 หลัก" maxlength="10" required>
                        <div id="member-status-text" class="form-text"></div>
                    </div>
                    <div class="col-md-6">
                        <label for="reporter_name" class="form-label">ชื่อ-นามสกุล ผู้แจ้ง <span
                                class="text-danger">*</span></label>
                        <input type="text" name="reporter_name" id="reporter_name" class="form-control"
                            placeholder="กรอกชื่อ-นามสกุล" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="issue_type" class="form-label">ประเภทปัญหา</label>
                    <select name="issue_type" id="issue_type" class="form-select" required>
                        <option value="">-- เลือกปัญหา --</option>
                        <option value="pipe_burst">ท่อแตก / ท่อรั่ว</option>
                        <option value="no_water">น้ำไม่ไหล</option>
                        <option value="low_pressure">น้ำไหลอ่อน</option>
                        <option value="dirty_water">น้ำขุ่น / มีกลิ่น</option>
                        <option value="other">อื่นๆ</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">รายละเอียดเพิ่มเติม</label>
                    <textarea name="description" class="form-control" rows="3"
                        placeholder="ระบุจุดสังเกต หรือรายละเอียด..."></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">ระบุตำแหน่ง (ลากหมุดเพื่อปรับตำแหน่ง)</label>
                    <div id="map"></div>
                    <div class="row">
                        <div class="col">
                            <input type="text" name="latitude" id="latitude" class="form-control" placeholder="Latitude"
                                readonly required>
                        </div>
                        <div class="col">
                            <input type="text" name="longitude" id="longitude" class="form-control" placeholder="Longitude"
                                readonly required>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">รูปภาพประกอบ</label>

                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-danger w-50"
                            onclick="document.getElementById('cameraInput').click()">
                            📷 ถ่ายรูป
                        </button>

                        <button type="button" class="btn btn-outline-primary w-50"
                            onclick="document.getElementById('galleryInput').click()">
                            🖼️ เลือกรูป
                        </button>
                    </div>

                    <input type="file" name="photo_camera" id="cameraInput" class="d-none" accept="image/*"
                        capture="environment" onchange="handleFileSelect(this)">

                    <input type="file" name="photo_gallery" id="galleryInput" class="d-none" accept="image/*"
                        onchange="handleFileSelect(this)">

                    <div class="mt-3 text-center">
                        <img id="preview" class="preview-image img-fluid rounded shadow-sm" src="#" alt="ตัวอย่างรูปภาพ"
                            style="display:none; max-height: 300px;" />
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 btn-lg">แจ้งเหตุ</button>
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
                            <button type="button" class="btn btn-primary w-100" onclick="getLocation()">
                                🔄 ลองใหม่ (Retry)
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection
@section('script')
    <script src="https://cdn.jsdelivr.net/npm/browser-image-compression@2.0.2/dist/browser-image-compression.js"></script>
    <script async defer
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA-5AlIGzLhFXErl2STRT6GacX0616iW2o&callback=initMap"></script>
    <script>
        let map;
        let marker;

        // ฟังก์ชันเริ่มทำงานแผนที่
        function initMap() {
            // 1. พิกัดเริ่มต้น (เช่น กรุงเทพฯ) กรณีหาตำแหน่งไม่ได้
            const defaultLocation = { lat: 13.756331, lng: 100.501845 };
            map = new google.maps.Map(document.getElementById("map"), {
                zoom: 15,
                center: defaultLocation,
            });

            marker = new google.maps.Marker({
                position: defaultLocation,
                map: map,
                draggable: true, // อนุญาตให้ลากหมุดได้
                animation: google.maps.Animation.DROP,
            });

            // Event: เมื่อลากหมุดเสร็จ ให้ update ค่า input
            google.maps.event.addListener(marker, 'dragend', function (event) {
                updatePosition(event.latLng.lat(), event.latLng.lng());
            });

            // 2. ขอพิกัดปัจจุบันของผู้ใช้ (Geolocation)
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const pos = {
                            lat: position.coords.latitude,
                            lng: position.coords.longitude,
                        };

                        // ย้ายแผนที่และหมุดไปตำแหน่งผู้ใช้
                        map.setCenter(pos);
                        marker.setPosition(pos);
                        updatePosition(pos.lat, pos.lng);
                    },
                    () => {
                        handleLocationError(true, map.getCenter());
                    }
                );
            } else {
                // Browser ไม่รองรับ
                handleLocationError(false, map.getCenter());
            }
        }

        // ฟังก์ชันอัปเดตค่าลงใน Input
        function updatePosition(lat, lng) {
            $('#latitude').val(lat.toFixed(6));
            $('#longitude').val(lng.toFixed(6));
        }

        function handleLocationError(browserHasGeolocation, pos) {
            alert(browserHasGeolocation
                ? "ไม่สามารถระบุตำแหน่งปัจจุบันได้ (กรุณาเปิด Location Services)"
                : "เบราว์เซอร์ของคุณไม่รองรับ Geolocation");
        }

        // ฟังก์ชันแสดงตัวอย่างรูปภาพ (Image Preview)
        $(document).ready(function () {
            $('#photo').change(function () {
                const file = this.files[0];
                if (file) {
                    let reader = new FileReader();
                    reader.onload = function (event) {
                        $('#preview').attr('src', event.target.result).show();
                    }
                    reader.readAsDataURL(file);
                }
            });
        });

        async function handleFileSelect(input) { // **เพิ่ม async ที่นี่**
            // 1. เคลียร์ค่าของ input อีกตัวที่ไม่ถูกเลือกก่อน 
            if (input.id === 'cameraInput') {
                document.getElementById('galleryInput').value = '';
            } else {
                document.getElementById('cameraInput').value = '';
            }

            if (!input.files || input.files.length === 0) {
                $('#preview').hide().attr('src', '#');
                return;
            }

            const originalFile = input.files[0];

            // **ตัวเลือกการลดขนาดภาพ**
            const options = {
                maxSizeMB: 0.5,
                maxWidthOrHeight: 1024,
                useWebWorker: true,
                onProgress: (progress) => {
                    console.log('Compression progress:', progress);
                },
            };

            try {
                let compressedFile = await imageCompression(originalFile, options);
                console.log('Compressed 1 size:', compressedFile.size / 1024, 'KB');

                // **ถ้าขนาดไฟล์ยังใหญ่เกินเป้าหมายมาก**
                // if (compressedFile.size > (options.maxSizeMB * 1024 ) * 1.5) { // ตรวจสอบว่าใหญ่เกิน 150% ของเป้าหมายหรือไม่
                //     console.warn('Compressing again...');
                //     // 2. บีบอัดครั้งที่ 2 (ใช้ไฟล์ที่บีบอัดแล้วเป็น Input)
                //     compressedFile = await imageCompression(compressedFile, options);
                //     console.log('Compressed 2 size:', compressedFile.size / 1024, 'KB');
                // }
                // const compressedFile = await imageCompression(originalFile, options); // ได้ Blob Object
                // console.log('Compressed file size:', compressedFile.size / 1024, 'KB');

                // **การแก้ไขปัญหา TypeError: แปลง Blob เป็น File Object**
                const compressedImageFile = new File(
                    [compressedFile],
                    originalFile.name,
                    { type: compressedFile.type, lastModified: Date.now() }
                );

                // สร้าง DataTransfer เพื่อนำไฟล์ที่ถูกลดขนาดไปใส่ใน Input
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(compressedImageFile);
                input.files = dataTransfer.files;

                // 2. แสดงรูปตัวอย่างจากไฟล์ที่ลดขนาดแล้ว
                var reader = new FileReader();
                reader.onload = function (e) {
                    $('#preview').attr('src', e.target.result).show();
                }
                reader.readAsDataURL(compressedImageFile); // ใช้ File Object ที่แก้ไขแล้ว

            } catch (error) {
                console.error('Image compression failed:', error);
                alert('เกิดข้อผิดพลาดในการลดขนาดรูปภาพ: ' + error.message);
                $('#preview').hide().attr('src', '#');
                input.value = '';
            }
        }
    </script>
@endsection