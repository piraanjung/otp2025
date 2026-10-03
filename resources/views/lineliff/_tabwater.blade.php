<h2>งานประปา</h2>
<style>
    .navigation ul li.active a .icon {
        transform: translateY(-35px) !important;
        color: #fff;
        font-weight: bold;
        top: 45%;
        left: 45%;
    }
    .icon {
        position: absolute;
        top: 40%;
        left: 52%;
        transform: translate(-50%, -50%);
        width: 40%;
        height: 40%;
    }
</style>

<div class="main__stat-blocks">
    <div class="main__stat-block main__stat-block--lg">
        <div class="main__stat-graph main__stat-graph--filled">
            <svg class="ring" viewBox="0 0 180 180" height="180" width="180" xmlns="http://www.w3.org/2000/svg">
                <circle class="ring-track" cx="90" cy="90" r="82" fill="none" stroke="#156066" stroke-width="16" />
                <circle class="ring-stroke ring-stroke--steps" cx="90" cy="90" r="82" fill="none" stroke="#22a6b3" stroke-linecap="round" stroke-width="16" stroke-dasharray="515.22 515.22" stroke-dashoffset="0" transform="rotate(-90,90,90)" />
                <circle class="ring-fill" cx="90" cy="90" r="0" fill="none" transform="rotate(-90,90,90)" />
            </svg>
            <div class="main__stat-detail">
                <strong class="main__stat-value">{{ $userWastePref->purchase_transactions[0]->total_amounts ?? '0.00' }}</strong>
                <span class="main__stat-unit">ปริมาณน้ำประปาที่ท่านใช้<div>2568</div></span>
            </div>
        </div>
    </div>
</div>

<div class="main__stat-blocks">
    <div class="main__stat-block" data-bs-toggle="modal" data-bs-target="#reportModal" style="cursor: pointer;">
        <div class="main__stat-graph">
            <svg class="ring" viewBox="0 0 60 60" height="60" width="60" xmlns="http://www.w3.org/2000/svg">
                <circle class="ring-track" cx="30" cy="30" r="26" fill="none" stroke="#156066" stroke-width="8" />
                <circle class="ring-stroke ring-stroke--cals" cx="30" cy="30" r="26" fill="none" stroke="#22a6b3" stroke-linecap="round" stroke-width="8" stroke-dasharray="163.36 163.36" stroke-dashoffset="12.25" transform="rotate(-90,30,30)" />
            </svg>
            <svg role="img" aria-label="Flame" class="icon" viewBox="0 0 24 24" height="24" width="24" xmlns="http://www.w3.org/2000/svg">
                <path class="no-fill" fill="none" stroke="#156066" stroke-width="2" d="M 14.505 1 C 11.546 1.356 10.354 12.419 10.272 12.478 C 10.189 12.538 6.773 6.184 6.773 6.184 C 6.773 6.184 3.855 8.381 4 14 C 4.2 18 5.868 23.067 12.177 22.999 C 18.488 22.932 20.1 18 20 14 C 19.9 10 17.533 10.05 15.964 6.738 C 14.638 3.939 14.505 1 14.505 1 Z" />
            </svg>
        </div>
        <div class="main__stat-detail" style="margin-left: 0.5rem">
            <strong class="main__stat-value">แจ้งปัญหาน้ำประปา</strong>
        </div>
    </div>
    <div class="main__stat-block">
        <a href="#">
            <div class="main__stat-graph">
                <svg class="ring" viewBox="0 0 60 60" height="60" width="60" xmlns="http://www.w3.org/2000/svg">
                    <circle class="ring-track" cx="30" cy="30" r="26" fill="none" stroke="#156066" stroke-width="8" />
                    <circle class="ring-stroke ring-stroke--miles" cx="30" cy="30" r="26" fill="none" stroke="#22a6b3" stroke-linecap="round" stroke-width="8" stroke-dasharray="163.36 163.36" stroke-dashoffset="80" transform="rotate(180,30,30)" />
                </svg>
                <svg role="img" aria-label="Location marker" class="icon" viewBox="0 0 24 24" height="24" width="24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 2C7.6 2 4 5.6 4 10C4 15.4 11 21.5 11.3 21.8C11.5 21.9 11.8 22 12 22C12.2 22 12.5 21.9 12.7 21.8C13 21.5 20 15.4 20 10C20 5.6 16.4 2 12 2ZM12 19.7C9.9 17.7 6 13.4 6 10C6 6.7 8.7 4 12 4C15.3 4 18 6.7 18 10C18 13.3 14.1 17.7 12 19.7ZM12 6C9.8 6 8 7.8 8 10C8 6.7 8.7 4 12 4C15.3 4 18 6.7 18 10C18 13.3 14.1 17.7 12 19.7ZM12 6C9.8 6 8 7.8 8 10C8 12.2 9.8 14 12 14C14.2 14 16 12.2 16 10C16 7.8 14.2 6 12 6ZM12 12C10.9 12 10 11.1 10 10C10 8.9 10.9 8 12 8C13.1 8 14 8.9 14 10C14 11.1 13.1 12 12 12Z" />
                </svg>
            </div>
            <div class="main__stat-detail">
                <strong class="main__stat-value">ใบแจ้งหนี้</strong>
            </div>
        </a>
    </div>
</div>

<div class="main__stat-blocks">
    <div class="main__stat-block" id="qrcosde">
        <div class="main__stat-graph">
            <svg class="ring" viewBox="0 0 60 60" height="60" width="60" xmlns="http://www.w3.org/2000/svg">
                <circle class="ring-track" cx="30" cy="30" r="26" fill="none" stroke="#156066" stroke-width="8" />
                <circle class="ring-stroke ring-stroke--cals" cx="30" cy="30" r="26" fill="none" stroke="#22a6b3" stroke-linecap="round" stroke-width="8" stroke-dasharray="163.36 163.36" stroke-dashoffset="12.25" transform="rotate(-90,30,30)" />
            </svg>
            <svg role="img" aria-label="Flame" class="icon" viewBox="0 0 24 24" height="24" width="24" xmlns="http://www.w3.org/2000/svg">
                <path class="no-fill" fill="none" stroke="#22a6b3" stroke-width="2" d="M 14.505 1 C 11.546 1.356 10.354 12.419 10.272 12.478 C 10.189 12.538 6.773 6.184 6.773 6.184 C 6.773 6.184 3.855 8.381 4 14 C 4.2 18 5.868 23.067 12.177 22.999 C 18.488 22.932 20.1 18 20 14 C 19.9 10 17.533 10.05 15.964 6.738 C 14.638 3.939 14.505 1 14.505 1 Z" />
            </svg>
        </div>
        <a href="{{ route('keptkayas.recycle_classify') }}">
            <div class="main__stat-detail">
                <strong class="main__stat-value">วิธีแก้ปัญหาถังหมัก</strong>
            </div>
        </a>
    </div>
    <div class="main__stat-block">
        <div class="main__stat-graph">
            <svg class="ring" viewBox="0 0 60 60" height="60" width="60" xmlns="http://www.w3.org/2000/svg">
                <circle class="ring-track" cx="30" cy="30" r="26" fill="none" stroke="#156066" stroke-width="8" />
                <circle class="ring-stroke ring-stroke--miles" cx="30" cy="30" r="26" fill="none" stroke="#22a6b3" stroke-linecap="round" stroke-width="8" stroke-dasharray="163.36 163.36" stroke-dashoffset="35.39" transform="rotate(-90,30,30)" />
            </svg>
            <svg role="img" aria-label="Location marker" class="icon" viewBox="0 0 24 24" height="24" width="24" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 2C7.6 2 4 5.6 4 10C4 15.4 11 21.5 11.3 21.8C11.5 21.9 11.8 22 12 22C12.2 22 12.5 21.9 12.7 21.8C13 21.5 20 15.4 20 10C20 5.6 16.4 2 12 2ZM12 19.7C9.9 17.7 6 13.4 6 10C6 6.7 8.7 4 12 4C15.3 4 18 6.7 18 10C18 13.3 14.1 17.7 12 19.7ZM12 6C9.8 6 8 7.8 8 10C8 6.7 8.7 4 12 4C15.3 4 18 6.7 18 10C18 13.3 14.1 17.7 12 19.7ZM12 6C9.8 6 8 7.8 8 10C8 12.2 9.8 14 12 14C14.2 14 16 12.2 16 10C16 7.8 14.2 6 12 6ZM12 12C10.9 12 10 11.1 10 10C10 8.9 10.9 8 12 8C13.1 8 14 8.9 14 10C14 11.1 13.1 12 12 12Z" />
            </svg>
        </div>
        <div class="main__stat-detail">
            <strong class="main__stat-value">ประวัติชำระค่าประปา</strong>
        </div>
    </div>
</div>

<!-- Modal แจ้งปัญหาน้ำประปา -->
<div class="modal fade" id="reportModal" tabindex="-1" aria-labelledby="reportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header bg-light" style="border-top-left-radius: 20px; border-top-right-radius: 20px;">
                <h5 class="modal-title fw-bold" id="reportModalLabel"><i class="bi bi-exclamation-triangle-fill text-warning me-2"></i>แจ้งปัญหาน้ำประปา</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form id="reportForm">
                @csrf
                <!-- Hidden Input สำหรับระบุประเภทระบบงาน -->
                <input type="hidden" name="system_type" value="water">
                
                <div class="modal-body p-4">
                    <!-- Hidden User ID -->
                    <input type="hidden" name="user_id" value="{{ collect($member)->isNotEmpty() ?$member->id : 0 }}">

                    <!-- ข้อมูลผู้แจ้ง -->
                    <div class="row mb-3">
                        <div class="col-md-6 mb-2 mb-md-0">
                            <label for="reporter_name" class="form-label fw-bold">ชื่อผู้แจ้ง:</label>
                            <input type="text" class="form-control" name="reporter_name" id="reporter_name" value="{{ collect($member)->isNotEmpty() ? $member->firstname . " " . $member->lastname : '' }}" placeholder="กรอกชื่อ-นามสกุล..." required>
                        </div>
                        <div class="col-md-6">
                            <label for="reporter_phone" class="form-label fw-bold">เบอร์โทรศัพท์ติดต่อ:</label>
                            <input type="text" class="form-control" name="reporter_phone" id="reporter_phone" value="{{ collect($member)->isNotEmpty() ?$member->phone : '' }}" placeholder="กรอกเบอร์โทรศัพท์..." required>
                        </div>
                    </div>

                    <!-- 1. เลือกประเภทปัญหา -->
                    <label class="form-label fw-bold">เลือกประเภทปัญหา:</label>
                    <input type="hidden" name="issue_type" id="issue_type" required>
                    <div class="row g-2 mb-3" id="lists">
                        <div class="col-6"><button type="button" class="w-100 btn btn-outline-info tabwater_problem" data-value="0">อื่นๆ</button></div>
                    </div>

                    <!-- ช่อง Text Input สำหรับระบุหัวข้ออื่นๆ -->
                    <div class="mb-3" id="otherInputContainer" style="display: none;">
                        <label for="other_issue" class="form-label fw-bold text-primary">ระบุหัวข้อปัญหา (อื่นๆ):</label>
                        <input type="text" class="form-control" name="other_issue" id="other_issue" placeholder="กรุณาระบุชื่อปัญหา...">
                    </div>

                    <!-- คำอธิบายเพิ่มเติม -->
                    <div class="mb-3">
                        <label for="description" class="form-label fw-bold">คำอธิบายเพิ่มเติม (ไม่บังคับ):</label>
                        <textarea class="form-control" name="description" id="description" rows="3" placeholder="ระบุรายละเอียดเพิ่มเติมเกี่ยวกับปัญหาที่พบ..."></textarea>
                    </div>

                    <!-- Hidden Input สำหรับ Lat, Lng -->
                    <input type="hidden" name="latitude" id="lat">
                    <input type="hidden" name="longitude" id="lng">

                    <!-- 2. แผนที่ระบุพิกัด -->
                    <label class="form-label fw-bold">ระบุตำแหน่งที่เกิดเหตุ (ลากหมุดเพื่อปรับพิกัดได้):</label>
                    <div id="statusBox" class="alert alert-warning py-2 style-sm mb-2" style="font-size: 13px;">⏳ กำลังระบุตำแหน่ง GPS...</div>
                    <div id="map" style="height: 220px; width: 100%; border-radius: 12px;" class="mb-3"></div>

                    <!-- 3. ส่วนกล้องและรายการรูปภาพที่ถ่าย -->
                    <label class="form-label fw-bold">ถ่ายภาพสถานที่เกิดเหตุ (ถ่ายได้หลายรูป):</label>
                    <div class="text-center mb-3">
                        <video id="webcam" autoplay playsinline style="width: 100%; max-height: 230px; object-fit: cover; border-radius: 12px; display: none; background: #000;"></video>
                    </div>
                    <div class="d-grid gap-2 mb-3">
                        <button type="button" class="btn btn-secondary rounded-pill py-2" id="btnStartCamera"><i class="bi bi-camera me-1"></i> เปิดกล้องถ่ายรูป</button>
                        <button type="button" class="btn btn-warning rounded-pill py-2 fw-bold" id="btnCapture" style="display: none;"><i class="bi bi-camera-fill me-1"></i> แชะ! ถ่ายภาพนี้</button>
                    </div>
                    <div class="mb-2">
                        <span class="text-muted small">รูปภาพที่ถ่ายแล้ว (<span id="photoCount">0</span> รูป):</span>
                        <div id="photoGallery" class="row g-2 mt-1" style="max-height: 250px; overflow-y: auto;"></div>
                    </div>
                </div>

                <div class="modal-footer bg-light" style="border-bottom-left-radius: 20px; border-bottom-right-radius: 20px;">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4" id="btnSubmit" disabled>ส่งแจ้งเหตุ</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal เตือนให้เปิด GPS -->
<div class="modal fade" id="gpsAlertModal" tabindex="-1" aria-labelledby="gpsAlertModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-body text-center p-4">
                <div class="mb-3">
                    <span style="font-size: 50px;">📍</span>
                </div>
                <h5 class="fw-bold text-dark mb-2" id="gpsAlertModalLabel">กรุณาเปิด GPS / ตำแหน่งที่ตั้ง</h5>
                <p class="text-muted small mb-4">
                    เพื่อความรวดเร็วในการระบุตำแหน่งจุดเกิดเหตุ กรุณาเปิดใช้งาน GPS (Location) ที่การตั้งค่าหรือแถบทางลัดของมือถือ จากนั้นกดปุ่ม "ลองใหม่อีกครั้ง" ด้านล่างได้เลยครับ
                </p>
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-primary rounded-pill py-2 fw-bold" id="btnRetryGPS">
                        <i class="bi bi-arrow-clockwise me-1"></i> ลองใหม่อีกครั้ง
                    </button>
                    <button type="button" class="btn btn-outline-secondary rounded-pill py-2" data-bs-dismiss="modal">
                        ปิดหน้าต่างนี้ (ใช้พิกัดสำรอง)
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- โหลด Google Maps API -->
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA-5AlIGzLhFXErl2STRT6GacX0616iW2o&loading=async"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        let map, marker, videoStream;
        let capturedPhotos = []; 
        let fileList = []; 
        let gpsModal;

        const reportModal = document.getElementById('reportModal');
        const statusBox = document.getElementById('statusBox');
        const latInput = document.getElementById('lat');
        const lngInput = document.getElementById('lng');
        const problemInput = document.getElementById('issue_type');
        const otherInputContainer = document.getElementById('otherInputContainer');
        const otherIssueInput = document.getElementById('other_issue');
        const reporterNameInput = document.getElementById('reporter_name');
        const reporterPhoneInput = document.getElementById('reporter_phone');
        const webcam = document.getElementById('webcam');
        const btnStartCamera = document.getElementById('btnStartCamera');
        const btnCapture = document.getElementById('btnCapture');
        const btnSubmit = document.getElementById('btnSubmit');
        const photoGallery = document.getElementById('photoGallery');
        const photoCount = document.getElementById('photoCount');

        // ตั้งค่า Modal เตือน GPS
        if (document.getElementById('gpsAlertModal')) {
            gpsModal = new bootstrap.Modal(document.getElementById('gpsAlertModal'));
        }

        // เมื่อผู้ใช้กดปุ่มลองใหม่อีกครั้งหลังจากเปิด GPS แล้ว
        const btnRetryGPS = document.getElementById('btnRetryGPS');
        if (btnRetryGPS) {
            btnRetryGPS.addEventListener('click', function() {
                if (gpsModal) gpsModal.hide();
                getGPSAndInitMap();
            });
        }

        reporterNameInput.addEventListener('input', checkFormReady);
        reporterPhoneInput.addEventListener('input', checkFormReady);

        // ================= 1. เลือกประเภทปัญหา =================
        $(document).on('click', '.tabwater_problem', function () {
            document.querySelectorAll('.tabwater_problem').forEach(btn => {
                btn.classList.remove('btn-info', 'text-white');
                btn.classList.add('btn-outline-info');
            });
            this.classList.remove('btn-outline-info');
            this.classList.add('btn-info', 'text-white');
            const selectedVal = $(this).attr('data-value');
            problemInput.value = selectedVal;
            
            if (selectedVal === '0') {
                otherInputContainer.style.display = 'block';
                otherIssueInput.required = true;
            } else {
                otherInputContainer.style.display = 'none';
                otherIssueInput.required = false;
                otherIssueInput.value = '';
            }
            checkFormReady();
        });
        
        otherIssueInput.addEventListener('input', checkFormReady);

        // โหลดรายการปัญหาผ่าน API (ตัดจุดที่เอา system_type มาทับ issue_type ออกแล้ว)
        $.get('/api/issue_type/lists/water', function (res) {
            if (res && res.length > 0) {
                res.forEach(function (v) {
                    $('#lists').prepend('<div class="col-6"><button type="button" class="w-100 btn btn-outline-info tabwater_problem" data-value="' + v.id + '">' + v.name + '</button></div>');
                });
            }
        });

        // ================= 2. จัดการ Modal & Google Maps =================
        if (reportModal) {
            reportModal.addEventListener('shown.bs.modal', function () {
                setTimeout(() => {
                    if (!map) {
                        getGPSAndInitMap();
                    } else {
                        google.maps.event.trigger(map, "resize");
                        if (marker && marker.getPosition()) {
                            map.setCenter(marker.getPosition());
                        }
                    }
                }, 150);
                checkFormReady();
            });

            reportModal.addEventListener('hidden.bs.modal', function () {
                stopCamera();
            });
        }

        function getGPSAndInitMap() {
            if (!navigator.geolocation) {
                statusBox.className = "alert alert-danger py-2 mb-2";
                statusBox.innerText = "❌ เบราว์เซอร์ไม่รองรับ GPS";
                return;
            }

            const options = {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0
            };

            statusBox.className = "alert alert-warning py-2 mb-2";
            statusBox.innerText = "⏳ กำลังระบุตำแหน่ง GPS...";

            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    updateLatLng(lat, lng);
                    statusBox.className = "alert alert-success py-2 mb-2";
                    statusBox.innerText = "✅ ระบุพิกัดสำเร็จ (สามารถลากหมุดเพื่อปรับตำแหน่งได้)";
                    if (typeof google !== 'undefined' && typeof google.maps !== 'undefined') {
                        initGoogleMap(lat, lng);
                    }
                },
                (error) => {
                    let errMsg = "⚠️ ไม่สามารถระบุตำแหน่งอัตโนมัติได้";

                    if (error.code === error.PERMISSION_DENIED || error.code === error.POSITION_UNAVAILABLE || error.code === 2 || error.code === error.TIMEOUT) {
                        errMsg = "⚠️ กรุณาเปิด GPS / Location บนมือถือของคุณ";
                        if (gpsModal) {
                            gpsModal.show();
                        }
                    }

                    statusBox.className = "alert alert-danger py-2 mb-2";
                    statusBox.innerHTML = errMsg;

                    const defaultLat = 13.7563;
                    const defaultLng = 100.5018;
                    updateLatLng(defaultLat, defaultLng);
                    if (typeof google !== 'undefined' && typeof google.maps !== 'undefined') {
                        initGoogleMap(defaultLat, defaultLng);
                    }
                },
                options
            );
        }

        function initGoogleMap(lat, lng) {
            const myLatLng = { lat: lat, lng: lng };
            map = new google.maps.Map(document.getElementById("map"), {
                zoom: 17,
                center: myLatLng,
            });
            marker = new google.maps.Marker({
                position: myLatLng,
                map: map,
                draggable: true,
                title: "ลากเพื่อย้ายจุดเกิดเหตุ"
            });
            marker.addListener("dragend", (e) => {
                updateLatLng(e.latLng.lat(), e.latLng.lng());
            });
            map.addListener("click", (e) => {
                marker.setPosition(e.latLng);
                updateLatLng(e.latLng.lat(), e.latLng.lng());
            });
        }

        function updateLatLng(lat, lng) {
            latInput.value = lat;
            lngInput.value = lng;
            checkFormReady();
        }

        // ================= 3. ระบบกล้อง & บีบอัดรูป =================
        btnStartCamera.addEventListener('click', async () => {
            try {
                videoStream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: "environment" }, audio: false });
                webcam.srcObject = videoStream;
                webcam.style.display = 'block';
                btnStartCamera.style.display = 'none';
                btnCapture.style.display = 'block';
            } catch (err) {
                alert('ไม่สามารถเปิดกล้องได้: ' + err.message);
            }
        });

        btnCapture.addEventListener('click', () => {
            const canvas = document.createElement('canvas');
            let width = webcam.videoWidth;
            let height = webcam.videoHeight;
            const MAX_DIMENSION = 1280;
            if (width > height && width > MAX_DIMENSION) {
                height = Math.round((height * MAX_DIMENSION) / width);
                width = MAX_DIMENSION;
            } else if (height > width && height > MAX_DIMENSION) {
                width = Math.round((width * MAX_DIMENSION) / height);
                height = MAX_DIMENSION;
            }
            canvas.width = width;
            canvas.height = height;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(webcam, 0, 0, width, height);
            const targetSizeBytes = 300 * 1024; 
            let quality = 0.9;
            canvas.toBlob(function processBlob(blob) {
                if (!blob) return;
                if (blob.size > targetSizeBytes && quality > 0.1) {
                    quality -= 0.1;
                    canvas.toBlob(processBlob, 'image/jpeg', quality);
                } else {
                    const fileName = `photo_${Date.now()}.jpg`;
                    const file = new File([blob], fileName, { type: 'image/jpeg' });
                    fileList.push(file);
                    const previewUrl = URL.createObjectURL(blob);
                    capturedPhotos.push(previewUrl);
                    updatePhotoGallery();
                    checkFormReady();
                }
            }, 'image/jpeg', quality);
        });

        function stopCamera() {
            if (videoStream) {
                videoStream.getTracks().forEach(track => track.stop());
                videoStream = null;
            }
            webcam.style.display = 'none';
            btnStartCamera.style.display = 'block';
            btnCapture.style.display = 'none';
        }

        function updatePhotoGallery() {
            photoGallery.innerHTML = '';
            photoCount.innerText = capturedPhotos.length;
            capturedPhotos.forEach((photo, index) => {
                const col = document.createElement('div');
                col.className = 'col-4 position-relative';
                col.innerHTML = `
                    <div class="border rounded overflow-hidden shadow-sm bg-white" style="height: 90px;">
                        <img src="${photo}" style="width: 100%; height: 100%; object-fit: cover;">
                        <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1 rounded-circle p-0 d-flex align-items-center justify-content-center" style="width: 24px; height: 24px;" onclick="window.removePhoto(${index})" title="ลบรูปนี้">
                            <i class="bi bi-x fs-6"></i>
                        </button>
                    </div>
                `;
                photoGallery.appendChild(col);
            });
        }

        window.removePhoto = function (index) {
            capturedPhotos.splice(index, 1);
            fileList.splice(index, 1);
            updatePhotoGallery();
            checkFormReady();
        }

        function checkFormReady() {
            let isProblemValid = false;
            if (problemInput.value) {
                if (problemInput.value === '0') {
                    isProblemValid = otherIssueInput.value.trim().length > 0;
                } else {
                    isProblemValid = true;
                }
            }
            const isNameValid = reporterNameInput.value.trim().length > 0;
            const isPhoneValid = reporterPhoneInput.value.trim().length > 0;

            if (isNameValid && isPhoneValid && isProblemValid && latInput.value && fileList.length > 0) {
                btnSubmit.disabled = false;
            } else {
                btnSubmit.disabled = true;
            }
        }

        // ================= 4. ส่งข้อมูลฟอร์ม =================
        document.getElementById('reportForm').addEventListener('submit', function (e) {
            e.preventDefault();
            btnSubmit.disabled = true;
            btnSubmit.innerText = 'กำลังส่งข้อมูล...';
            submitReport();
        });

        async function submitReport() {
            const formElement = document.getElementById('reportForm');
            const formData = new FormData(formElement);
            formData.delete('photos[]');
            fileList.forEach((file) => {
                formData.append('photos[]', file);
            });

            try {
                const response = await fetch("{{ route('tabwater.notify.store') }}", {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: formData
                });
                const result = await response.json();
                
                if (response.ok && result.success) {
                    alert('✅ ส่งแจ้งเหตุเรียบร้อยแล้ว');
                    location.reload();
                } else {
                    alert('❌ เกิดข้อผิดพลาด: ' + (result.message || JSON.stringify(result.errors)));
                    btnSubmit.disabled = false;
                    btnSubmit.innerText = 'ส่งแจ้งเหตุ';
                }
            } catch (error) {
                console.error('Fetch Error:', error);
                alert('❌ ไม่สามารถเชื่อมต่อกับ Server ได้');
                btnSubmit.disabled = false;
                btnSubmit.innerText = 'ส่งแจ้งเหตุ';
            }
        }
    });
</script>