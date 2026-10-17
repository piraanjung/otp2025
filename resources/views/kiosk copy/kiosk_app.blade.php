<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kiosk Recycle App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@latest/dist/tf.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@teachablemachine/image@latest/dist/teachablemachine-image.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <meta http-equiv="Cache-Control" content="public, max-age=31536000"> 
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h2 class="text-center text-primary">♻️ ระบบคัดแยกขวด PET</h2>
        <hr>

        <div id="status-alert" class="alert alert-info text-center" role="alert">
            เชื่อมต่อ Wi-Fi Kiosk แล้ว? กด 'เริ่ม' เพื่อใช้งาน
        </div>

        <div class="row">
            <div class="col-md-6 offset-md-3 text-center">
                <div id="webcam-container" class="border border-dark rounded mb-3" style="min-height: 240px; background-color: #333;">
                    <video id="kiosk-stream" style="width: 100%; display: none;"></video>
                    <canvas id="tm-canvas" style="width: 100%; display: none;"></canvas>
                </div>
                
                <div id="label-container" class="mt-2 mb-3"></div>

                <button id="start-scan" class="btn btn-success btn-lg me-2">เริ่มใช้งาน</button>
                <button id="end-session" class="btn btn-danger btn-lg" style="display: none;">สิ้นสุด & อัปโหลด</button>

                <div class="mt-3">
                    <p>Transaction Log: <span id="log-count">0</span> รายการ</p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // กำหนด Local IP และ URL
        const ESP8266_IP = 'http://192.168.1.101';
        const ESP32_IP = 'http://192.168.1.102'; 
        const TM_MODEL_URL = '{{ asset('ml_models/model.json') }}'; // ใช้ Laravel asset helper

        // ตัวแปรสำหรับ Teachable Machine
        let model, labelContainer, maxPredictions;
        let isScanning = false;
        
        // ข้อมูล Session Log ที่ถูกเก็บไว้ใน Browser
        // แม้จะใช้ LocalStorage/SessionStorage เป็นวิธีที่ดีกว่า แต่เราจะใช้ตัวแปร JS เพื่อแสดงแนวคิดการ Batching
        let currentSessionLog = []; 
        const KIOSK_ID = 'Kiosk001';

        // =======================================================
        // A. Teachable Machine & Stream Logic
        // =======================================================

        async function initTM() {
            try {
                // โหลดโมเดล (ใช้ URL ที่กำหนด Header Cache-Control ไว้)
                const modelURL = TM_MODEL_URL + '?' + Date.now(); // อาจต้องเพิ่ม Query String เพื่อบังคับโหลดครั้งแรก
                const metadataURL = modelURL.replace('model.json', '/metadata.json');
                
                $('#status-alert').text('กำลังโหลดโมเดล Machine Learning...');
                model = await tmImage.load(modelURL, metadataURL);
                maxPredictions = model.getTotalClasses();
                
                $('#status-alert').removeClass('alert-info').addClass('alert-success').text('✅ โมเดลโหลดสำเร็จแล้ว');
                
            } catch (error) {
                $('#status-alert').removeClass('alert-success').addClass('alert-danger').text('❌ โหลดโมเดลล้มเหลว: ตรวจสอบไฟล์ TM Model และ Cache Header');
                console.error("TM Load Error:", error);
            }
        }
        
        // ฟังก์ชันหลักในการทำนาย
        async function predict() {
            if (!isScanning || !model) return;

            // ดึงภาพจาก stream หรือ canvas (ถ้าใช้ canvas)
            const webcamElement = document.getElementById('kiosk-stream');
            const prediction = await model.predict(webcamElement);
            
            // หา Class ที่มีความน่าจะเป็นสูงสุด
            const highestPrediction = prediction.reduce((prev, current) => (prev.probability > current.probability) ? prev : current);
            
            const bottleType = highestPrediction.className;
            const probability = highestPrediction.probability;
            
            // แสดงผลลัพธ์
            let action = 'reject';
            if (probability > 0.8 && (bottleType.includes('PET_Bottle'))) {
                action = 'accept';
                $('#label-container').html(`<div class='text-success'>${bottleType} (${(probability * 100).toFixed(1)}%) - ✅ **PASS**</div>`);
            } else {
                action = 'reject';
                $('#label-container').html(`<div class='text-danger'>${bottleType} (${(probability * 100).toFixed(1)}%) - ❌ **REJECT**</div>`);
            }
            
            // สั่งงาน Servo และบันทึก Log/Image
            sendCommandAndLog(bottleType, action);
        }

        // =======================================================
        // B. Kiosk Control & Logging
        // =======================================================

        function sendCommandAndLog(bottleType, action) {
            
            // 1. สั่งงาน Servo (ไปที่ ESP8266)
            const servoUrl = `${ESP8266_IP}/sort?action=${action}`;
            $.get(servoUrl).fail(() => {
                console.error("Failed to send servo command to 8266.");
            });

            // 2. ถ้าเป็น ACCEPT ให้สั่ง ESP32-CAM บันทึกภาพ และบันทึก Log
            if (action === 'accept') {
                const imgFilename = `${bottleType}_${Date.now()}.jpg`;
                
                // สั่ง ESP32-CAM บันทึกภาพและ Log ลง SD Card ทันที
                const logUrl = `${ESP32_IP}/save_image_and_log?type=${bottleType}&filename=${imgFilename}`;
                $.get(logUrl).then(response => {
                    console.log("Image & Log saved to SD Card:", response);
                    
                    // เพิ่มรายการลงใน Local Session Log (สำหรับแสดงผลจำนวนให้ User เห็น)
                    currentSessionLog.push({
                        timestamp: new Date().toISOString(),
                        bottle_type: bottleType,
                        action: action,
                        img_filename: imgFilename,
                        kiosk_id: KIOSK_ID
                    });
                    $('#log-count').text(currentSessionLog.length);
                    
                }).fail(() => {
                    console.error("Failed to save image/log to ESP32-CAM SD.");
                });
            
            } else {
                 // ถ้าเป็น REJECT ให้เพิ่มรายการลงใน Log โดยไม่มีไฟล์ภาพ
                 currentSessionLog.push({
                    timestamp: new Date().toISOString(),
                    bottle_type: bottleType,
                    action: action,
                    img_filename: 'N/A',
                    kiosk_id: KIOSK_ID
                });
                $('#log-count').text(currentSessionLog.length);
            }
        }
        
        // =======================================================
        // C. Event Handlers (ปุ่ม)
        // =======================================================

        // ฟังก์ชันเริ่มการสแกน (หลังจาก User กดปุ่ม)
        $('#start-scan').on('click', async function() {
            $(this).hide();
            $('#end-session').show();
            $('#status-alert').removeClass('alert-success alert-danger').addClass('alert-warning').text('กำลังรอขวดเข้าช่อง...');

            // 1. ปลุก ESP8266 (ปลุกและสั่งให้เริ่มรอเซ็นเซอร์)
            $.get(`${ESP8266_IP}/wake_up`).fail(() => {
                 $('#status-alert').removeClass('alert-warning').addClass('alert-danger').text('❌ เชื่อมต่อ Kiosk ไม่ได้');
            });
            
            // 2. Start Stream (สมมติว่า Stream เริ่มทำงานอยู่แล้ว หรือสั่งเริ่มจาก ESP8266)
            const streamUrl = `${ESP32_IP}/stream`;
            document.getElementById('kiosk-stream').src = streamUrl;
            $('#kiosk-stream').show();
            
            isScanning = true;
            setInterval(predict, 200); // ทำนายทุก 200ms (5 FPS)
        });

        // ฟังก์ชันสิ้นสุดและอัปโหลดข้อมูลทั้งหมด
        $('#end-session').on('click', function() {
            if (currentSessionLog.length === 0) {
                 alert("ไม่มีรายการที่จะอัปโหลด!");
                 window.location.reload();
                 return;
            }

            $('#status-alert').removeClass('alert-warning').addClass('alert-info').text('กำลังอัปโหลดข้อมูล Transaction Log...');
            $(this).prop('disabled', true);
            isScanning = false;
            
            // 1. ส่งข้อมูล Log Batch ไปยัง Laravel API
            $.ajax({
                url: '{{ route("api.uploadTransactionLog") }}', // ใช้ชื่อ route ที่กำหนดใน api.php
                type: 'POST',
                data: JSON.stringify({ transactions: currentSessionLog }),
                contentType: 'application/json',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}' // ต้องมี CSRF token สำหรับการ POST ใน Laravel
                },
                success: function(response) {
                    $('#status-alert').removeClass('alert-info').addClass('alert-success').text('✅ อัปโหลด Log สำเร็จ! กำลังลบข้อมูลค้างที่ Kiosk...');
                    
                    // 2. สั่งลบ Log ใน SD Card (ESP32-CAM)
                    $.get(`${ESP32_IP}/clear_log_complete`).always(() => {
                        alert('สิ้นสุดการใช้งานสำเร็จ! ข้อมูลถูกส่งแล้ว.');
                        currentSessionLog = [];
                        window.location.reload(); // รีเฟรชหน้า
                    });
                },
                error: function(xhr, status, error) {
                    $('#status-alert').removeClass('alert-info').addClass('alert-danger').text('❌ อัปโหลดล้มเหลว! โปรดลองอีกครั้ง');
                    $(this).prop('disabled', false); // อนุญาตให้ลองใหม่
                }
            });
        });

        // 3. เริ่มต้น: โหลด TM Model เมื่อหน้าโหลดเสร็จ
        $(document).ready(function() {
            initTM();
        });

    </script>
</body>
</html>