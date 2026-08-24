<h2>งานประปา</h2>

<div class="main__stat-blocks">
    <div class="main__stat-block main__stat-block--lg">
        <div class="main__stat-graph main__stat-graph--filled">
            <svg class="ring" viewBox="0 0 180 180" height="180" width="180" xmlns="http://www.w3.org/2000/svg">
                <circle class="ring-track" cx="90" cy="90" r="82" fill="none" stroke="#7f7f7f" stroke-width="16" />
                <circle class="ring-stroke ring-stroke--steps" cx="90" cy="90" r="82" fill="none" stroke="#000"
                    stroke-linecap="round" stroke-width="16" stroke-dasharray="515.22 515.22" stroke-dashoffset="0"
                    transform="rotate(-90,90,90)" />
                <circle class="ring-fill" cx="90" cy="90" r="0" fill="none" transform="rotate(-90,90,90)" />
            </svg>
            <div class="main__stat-detail">
                <strong
                    class="main__stat-value">{{ $userWastePref->purchase_transactions[0]->total_amounts ?? '0.00' }}</strong>
                <span class="main__stat-unit">ปริมาณน้ำประปาที่ท่านใช้<div>2568</div></span>
            </div>
        </div>
    </div>
</div>

<div class="main__stat-blocks">
    <div class="main__stat-block" data-bs-toggle="modal" data-bs-target="#reportModal" style="cursor: pointer;">
        <div class="main__stat-graph">
            <svg class="ring" viewBox="0 0 60 60" height="60" width="60" xmlns="http://www.w3.org/2000/svg">
                <circle class="ring-track" cx="30" cy="30" r="26" fill="none" stroke="#7f7f7f" stroke-width="8" />
                <circle class="ring-stroke ring-stroke--cals" cx="30" cy="30" r="26" fill="none" stroke="#000"
                    stroke-linecap="round" stroke-width="8" stroke-dasharray="163.36 163.36" stroke-dashoffset="12.25"
                    transform="rotate(-90,30,30)" />
            </svg>
            <svg role="img" aria-label="Flame" class="icon" viewBox="0 0 24 24" height="24" width="24"
                xmlns="http://www.w3.org/2000/svg">
                <path class="no-fill" fill="none" stroke="#000" stroke-width="2"
                    d="M 14.505 1 C 11.546 1.356 10.354 12.419 10.272 12.478 C 10.189 12.538 6.773 6.184 6.773 6.184 C 6.773 6.184 3.855 8.381 4 14 C 4.2 18 5.868 23.067 12.177 22.999 C 18.488 22.932 20.1 18 20 14 C 19.9 10 17.533 10.05 15.964 6.738 C 14.638 3.939 14.505 1 14.505 1 Z" />
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
                    <circle class="ring-track" cx="30" cy="30" r="26" fill="none" stroke="#7f7f7f" stroke-width="8" />
                    <circle class="ring-stroke ring-stroke--miles" cx="30" cy="30" r="26" fill="none" stroke="#000"
                        stroke-linecap="round" stroke-width="8" stroke-dasharray="163.36 163.36"
                        stroke-dashoffset="35.39" transform="rotate(-90,30,30)" />
                </svg>
                <svg role="img" aria-label="Location marker" class="icon" viewBox="0 0 24 24" height="24" width="24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M12 2C7.6 2 4 5.6 4 10C4 15.4 11 21.5 11.3 21.8C11.5 21.9 11.8 22 12 22C12.2 22 12.5 21.9 12.7 21.8C13 21.5 20 15.4 20 10C20 5.6 16.4 2 12 2ZM12 19.7C9.9 17.7 6 13.4 6 10C6 6.7 8.7 4 12 4C15.3 4 18 6.7 18 10C18 13.3 14.1 17.7 12 19.7ZM12 6C9.8 6 8 7.8 8 10C8 6.7 8.7 4 12 4C15.3 4 18 6.7 18 10C18 13.3 14.1 17.7 12 19.7ZM12 6C9.8 6 8 7.8 8 10C8 12.2 9.8 14 12 14C14.2 14 16 12.2 16 10C16 7.8 14.2 6 12 6ZM12 12C10.9 12 10 11.1 10 10C10 8.9 10.9 8 12 8C13.1 8 14 8.9 14 10C14 11.1 13.1 12 12 12Z" />
                </svg>
            </div>
            <div class="main__stat-detail">
                <strong class="main__stat-value">ใบแจ้งหนี้ (0)</strong>
            </div>
        </a>
    </div>
</div>

<div class="main__stat-blocks">
    <div class="main__stat-block" id="qrcosde">
        <div class="main__stat-graph">
            <svg class="ring" viewBox="0 0 60 60" height="60" width="60" xmlns="http://www.w3.org/2000/svg">
                <circle class="ring-track" cx="30" cy="30" r="26" fill="none" stroke="#7f7f7f" stroke-width="8" />
                <circle class="ring-stroke ring-stroke--cals" cx="30" cy="30" r="26" fill="none" stroke="#000"
                    stroke-linecap="round" stroke-width="8" stroke-dasharray="163.36 163.36" stroke-dashoffset="12.25"
                    transform="rotate(-90,30,30)" />
            </svg>
            <svg role="img" aria-label="Flame" class="icon" viewBox="0 0 24 24" height="24" width="24"
                xmlns="http://www.w3.org/2000/svg">
                <path class="no-fill" fill="none" stroke="#000" stroke-width="2"
                    d="M 14.505 1 C 11.546 1.356 10.354 12.419 10.272 12.478 C 10.189 12.538 6.773 6.184 6.773 6.184 C 6.773 6.184 3.855 8.381 4 14 C 4.2 18 5.868 23.067 12.177 22.999 C 18.488 22.932 20.1 18 20 14 C 19.9 10 17.533 10.05 15.964 6.738 C 14.638 3.939 14.505 1 14.505 1 Z" />
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
                <circle class="ring-track" cx="30" cy="30" r="26" fill="none" stroke="#7f7f7f" stroke-width="8" />
                <circle class="ring-stroke ring-stroke--miles" cx="30" cy="30" r="26" fill="none" stroke="#000"
                    stroke-linecap="round" stroke-width="8" stroke-dasharray="163.36 163.36" stroke-dashoffset="35.39"
                    transform="rotate(-90,30,30)" />
            </svg>
            <svg role="img" aria-label="Location marker" class="icon" viewBox="0 0 24 24" height="24" width="24"
                xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M12 2C7.6 2 4 5.6 4 10C4 15.4 11 21.5 11.3 21.8C11.5 21.9 11.8 22 12 22C12.2 22 12.5 21.9 12.7 21.8C13 21.5 20 15.4 20 10C20 5.6 16.4 2 12 2ZM12 19.7C9.9 17.7 6 13.4 6 10C6 6.7 8.7 4 12 4C15.3 4 18 6.7 18 10C18 13.3 14.1 17.7 12 19.7ZM12 6C9.8 6 8 7.8 8 10C8 6.7 8.7 4 12 4C15.3 4 18 6.7 18 10C18 13.3 14.1 17.7 12 19.7ZM12 6C9.8 6 8 7.8 8 10C8 12.2 9.8 14 12 14C14.2 14 16 12.2 16 10C16 7.8 14.2 6 12 6ZM12 12C10.9 12 10 11.1 10 10C10 8.9 10.9 8 12 8C13.1 8 14 8.9 14 10C14 11.1 13.1 12 12 12Z" />
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
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reportModalLabel">แจ้งปัญหาน้ำประปา</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('tabwater.notify.store') }}" method="POST" enctype="multipart/form-data"
                id="reportForm">
                @csrf
                <div class="modal-body">
                    <!-- 1. เลือกประเภทปัญหา -->
                    <label class="form-label fw-bold">เลือกประเภทปัญหา:</label>
                    <input type="hidden" name="issue_type" id="issue_type" required>
                    <div class="row g-2 mb-3">
                        <div class="col-6"><button type="button" class="w-100 btn btn-outline-info tabwater_problem"
                                data-value="pipe_burst">ท่อแตก / ท่อรั่ว</button></div>
                        <div class="col-6"><button type="button" class="w-100 btn btn-outline-info tabwater_problem"
                                data-value="no_water">น้ำไม่ไหล</button></div>
                        <div class="col-6"><button type="button" class="w-100 btn btn-outline-info tabwater_problem"
                                data-value="low_pressure">น้ำไหลอ่อน</button></div>
                        <div class="col-6"><button type="button" class="w-100 btn btn-outline-info tabwater_problem"
                                data-value="dirty_water">น้ำขุ่น / มีกลิ่น</button></div>
                        <div class="col-6"><button type="button" class="w-100 btn btn-outline-info tabwater_problem"
                                data-value="orther">อื่นๆ</button></div>
                    </div>

                    <!-- Hidden Input สำหรับ Lat, Lng และ รูป Base64 -->
                    <input type="hidden" name="latitude" id="lat">
                    <input type="hidden" name="longitude" id="lng">
                    <input type="hidden" name="photo_base64" id="photo_base64">

                    <!-- 2. แผนที่ระบุพิกัด -->
                    <label class="form-label fw-bold">ระบุตำแหน่งที่เกิดเหตุ (ลากหมุดเพื่อปรับพิกัดได้):</label>
                    <div id="statusBox" class="alert alert-warning py-2 style-sm mb-2" style="font-size: 13px;">⏳
                        กำลังระบุตำแหน่ง GPS...</div>
                    <div id="map" style="height: 220px; width: 100%; border-radius: 8px;" class="mb-3"></div>

                    <!-- 3. ส่วนกล้องและ Canvas -->
                    <label class="form-label fw-bold">ถ่ายภาพสถานที่เกิดเหตุ:</label>
                    <div class="text-center mb-2">
                        <video id="webcam" autoplay playsinline
                            style="width: 100%; max-height: 250px; object-fit: cover; border-radius: 8px; display: none; background: #000;"></video>
                        <canvas id="photoCanvas"
                            style="width: 100%; max-height: 250px; object-fit: cover; border-radius: 8px; display: none; border: 1px solid #ccc;"></canvas>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-secondary" id="btnStartCamera">📷 เปิดกล้องถ่ายรูป</button>
                        <button type="button" class="btn btn-warning" id="btnCapture" style="display: none;">📸
                            ถ่ายภาพ</button>
                        <button type="button" class="btn btn-outline-secondary" id="btnRetake" style="display: none;">🔄
                            ถ่ายใหม่</button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-primary" id="btnSubmit" disabled>ส่งแจ้งเหตุ</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- โหลด Google Maps API -->
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA-5AlIGzLhFXErl2STRT6GacX0616iW2o"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        let map, marker, videoStream;
        const reportModal = document.getElementById('reportModal');
        const statusBox = document.getElementById('statusBox');
        const latInput = document.getElementById('lat');
        const lngInput = document.getElementById('lng');
        const problemInput = document.getElementById('issue_type');
        const photoBase64Input = document.getElementById('photo_base64');

        const webcam = document.getElementById('webcam');
        const canvas = document.getElementById('photoCanvas');
        const btnStartCamera = document.getElementById('btnStartCamera');
        const btnCapture = document.getElementById('btnCapture');
        const btnRetake = document.getElementById('btnRetake');
        const btnSubmit = document.getElementById('btnSubmit');

        // ================= 1. เลือกประเภทปัญหา =================
        document.querySelectorAll('.tabwater_problem').forEach(button => {
            button.addEventListener('click', function () {
                document.querySelectorAll('.tabwater_problem').forEach(btn => {
                    btn.classList.remove('btn-info', 'text-white');
                    btn.classList.add('btn-outline-info');
                });
                this.classList.remove('btn-outline-info');
                this.classList.add('btn-info', 'text-white');

                problemInput.value = this.getAttribute('data-value');
                if (problemInput.value === 'orther') {
                }
                checkFormReady();
            });
        });

        // ================= 2. จัดการ Modal & Google Maps =================
        if (reportModal) {
            reportModal.addEventListener('shown.bs.modal', function () {
                // หน่วงเวลาเล็กน้อยให้ Modal กางพื้นเสร็จก่อนแผนที่วาดตัว
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
            });

            // ปิดกล้องเมื่อปิด Modal
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
                timeout: 7000,
                maximumAge: 0
            };

            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;

                    updateLatLng(lat, lng);
                    statusBox.className = "alert alert-success py-2 mb-2";
                    statusBox.innerText = "✅ ระบุพิกัดสำเร็จ (สามารถลากหมุดเพื่อปรับตำแหน่งได้)";

                    if (typeof google !== 'undefined' && typeof google.maps !== 'undefined') {
                        initGoogleMap(lat, lng);
                    } else {
                        console.warn("Google Maps API ยังโหลดไม่เสร็จ หรือไม่ได้ใส่ API Key");
                    }
                },
                (error) => {
                    console.error("GPS Error:", error);
                    let errMsg = "";
                    if (error.code === error.PERMISSION_DENIED) {
                        errMsg = "❌ คุณได้ปิดกั้นการเข้าถึง GPS กรุณากดอนุญาตสิทธิ์ตำแหน่งที่ไอคอนแม่กุญแจตรง URL ด้านบนแล้วรีเฟรชหน้าเว็บ";
                    } else if (error.code === error.POSITION_UNAVAILABLE) {
                        errMsg = "⚠️ ไม่พบสัญญาณ GPS กรุณาตรวจสอบอินเทอร์เน็ตหรืออุปกรณ์";
                    } else if (error.code === error.TIMEOUT) {
                        errMsg = "⚠️ ท่านยังไม่ได้เปิด GPS การค้นหาตำแหน่งใช้เวลานานเกินไป";
                    } else {
                        errMsg = "⚠️ ไม่สามารถระบุตำแหน่งอัตโนมัติได้";
                    }

                    statusBox.className = "alert alert-danger py-2 mb-2";
                    statusBox.innerHTML = errMsg;

                    // Fallback พิกัดเริ่มต้น (กรุงเทพฯ)
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

        // ================= 3. ระบบกล้อง & Canvas =================
        btnStartCamera.addEventListener('click', async () => {
            try {
                videoStream = await navigator.mediaDevices.getUserMedia({
                    video: { facingMode: "environment" },
                    audio: false
                });
                webcam.srcObject = videoStream;
                webcam.style.display = 'block';
                canvas.style.display = 'none';
                btnStartCamera.style.display = 'none';
                btnCapture.style.display = 'inline-block';
            } catch (err) {
                alert('ไม่สามารถเปิดกล้องได้: ' + err.message);
            }
        });

        btnCapture.addEventListener('click', () => {
            canvas.width = webcam.videoWidth;
            canvas.height = webcam.videoHeight;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(webcam, 0, 0, canvas.width, canvas.height);

            photoBase64Input.value = canvas.toDataURL('image/jpeg', 0.7);

            stopCamera();
            webcam.style.display = 'none';
            canvas.style.display = 'block';
            btnCapture.style.display = 'none';
            btnRetake.style.display = 'inline-block';
            alert('ทำงานถ่ายถาพแล้ว')
            checkFormReady();
        });

        btnRetake.addEventListener('click', () => {
            photoBase64Input.value = '';
            btnRetake.style.display = 'none';
            btnStartCamera.click();
            checkFormReady();
        });

        function stopCamera() {
            if (videoStream) {
                videoStream.getTracks().forEach(track => track.stop());
                videoStream = null;
            }
        }

        function checkFormReady() {
            if (problemInput.value && latInput.value && photoBase64Input.value) {
                btnSubmit.disabled = false;
            } else {
                btnSubmit.disabled = true;
            }
        }

        document.getElementById('reportForm').addEventListener('submit', function (e) {


            // สั่งแสดง Spinner หรือปิดปุ่มกันกดซ้ำ
            document.getElementById('btnSubmit').disabled = true;
            document.getElementById('btnSubmit').innerText = 'กำลังส่งข้อมูล...';
            submitReport();
        });

        async function submitReport() {
            // 1. ดึงค่าจาก Element หน้าเว็บ
            const issueType = document.getElementById('issue_type').value; // หรือ issue_type
            const latitude = document.getElementById('lat').value;
            const longitude = document.getElementById('lng').value;
            const photoBase64 = document.getElementById('photo_base64').value;
            const csrfToken = document.querySelector('input[name="_token"]').value; // CSRF Token สำหรับ Laravel

            // 2. ปรับแต่ง FormData เพื่อเตรียมส่ง
            const formData = new FormData();
            formData.append('_token', csrfToken);
            formData.append('issue_type', issueType);
            formData.append('latitude', latitude);
            formData.append('longitude', longitude);
            formData.append('photo_base64', photoBase64);

            try {
                // 3. ยิง POST Request ไปยัง Route
                const response = await fetch("{{ route('tabwater.notify.store') }}", {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const result = await response.json();

                if (response.ok) {
                    alert('✅ ส่งแจ้งเหตุเรียบร้อยแล้ว');
                    // ปิด Modal หรือสั่ง Reset Form ตามต้องการ
                    location.reload();
                } else {
                    alert('❌ เกิดข้อผิดพลาด: ' + (result.message || 'ไม่สามารถบันทึกข้อมูลได้'));
                }

            } catch (error) {
                console.error('Fetch Error:', error);
                alert('❌ ไม่สามารถเชื่อมต่อกับ Server ได้');
            }
        }
    });

</script>