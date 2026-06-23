<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รับซื้อขวดอัตโนมัติ (Frontend Cart)</title>
    
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" xintegrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    
   
    <!-- Teachable Machine Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@latest/dist/tf.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@teachablemachine/image@latest/dist/teachablemachine-image.min.js"></script>
    
    <style>
        /* จัดตำแหน่งวิดีโอ/รูปภาพให้อยู่ตรงกลางของพื้นที่ */
        .video-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 250px;
        }
        #webcam-preview, #captured-image {
            width: 224px; 
            height: 224px;
            object-fit: cover;
            border: 3px solid #0d6efd;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <div class="container my-5">
        
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card shadow-lg">
                    <div class="card-header bg-primary text-white text-center">
                        <h1 class="h3 mb-0">♻️ ตู้หยอดขวดอัตโนมัติ (Frontend Cart) ♻️</h1>
                    </div>
                    <div class="card-body">
                        
                        <div class="alert alert-info text-center" role="alert">
                            <span id="status-display">...กำลังโหลดโมเดล AI...</span>
                        </div>

                       <!-- ปุ่มเริ่มต้นและปุ่มจบการขาย -->
                       <div class="d-grid gap-2 mb-4">
                            <!-- ปุ่มนี้ถูกปิดใช้งาน (disabled) เพราะระบบจะเริ่มทำงานด้วย Polling อัตโนมัติ -->
                            <button id="start-sale-button" class="btn btn-success btn-lg" disabled>
                                ระบบทำงานอัตโนมัติด้วย Polling
                            </button>
                            
                            <button id="finish-sale-button" class="btn btn-info btn-lg" disabled onclick="finishSale()">
                                ขายเสร็จสิ้น (ส่งข้อมูล <span id="finish-count-display">0</span> รายการ)
                            </button>
                        </div>
                        
                        <h4 class="card-title text-center mb-3">ภาพถ่ายขวด</h4>
                        <div class="video-container bg-light p-3 mb-4 rounded">
                            <video id="webcam-preview" style="display:none;" autoplay></video>
                            <canvas id="photo-canvas" style="display:none;"></canvas>
                            <img id="captured-image" style="display:none;" alt="ขวดที่ถูกถ่าย" class="img-fluid" />
                            <p id="placeholder-text" class="text-muted">กล้องจะแสดงที่นี่เมื่อเริ่มกระบวนการ</p>
                        </div>

                        <h4 class="card-title text-center mt-4">ผลการจำแนกประเภทล่าสุด:</h4>
                        <div id="label-container" class="text-center fw-bold fs-5 mb-4"></div>

                        <h4 class="card-title text-center mt-4">รายการขวดที่ยอมรับ (<span id="bottle-count-display">0</span> ชิ้น)</h4>
                        <div class="table-responsive">
                            <table class="table table-striped table-sm">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>ภาพ</th>
                                        <th>Label</th>
                                        <th>ปริมาตร</th>
                                        <th>ความมั่นใจ</th>
                                    </tr>
                                </thead>
                                <tbody id="sale-list-body">
                                    <tr>
                                        <td colspan="5">ยังไม่มีขวดที่ยอมรับ</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" xintegrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <script>
    // **ตัวแปร Global สำหรับ TM**
    const URL = "https://teachablemachine.withgoogle.com/models/rBH9iJI78/"; 
    const CONFIDENCE_THRESHOLD = 0.60; // เกณฑ์ความมั่นใจ 60%
    const POLLING_RATE = 2000; // ตรวจสอบสถานะทุก 2 วินาที


    let model, maxPredictions;
    let isModelLoaded = false;
    let acceptedBottles = []; 
    let bottleCount = 0;
    let pollingIntervalId = null; 
    let mockPollingCount = 0; // ตัวแปรสำหรับจำลองการตรวจจับ
    
    // --- ฟังก์ชันช่วยเหลือ ---
    function updateStatus(message, alertClass = 'alert-info') {
        const statusElement = $('#status-display');
        const alertDiv = statusElement.closest('.alert');
        statusElement.text(message);
        alertDiv.attr('class', `alert ${alertClass} text-center`);
    }
    
    function toggleDisplay(showElementId) {
        $('#placeholder-text').hide();
        $('#webcam-preview, #captured-image').hide();
        $(showElementId).show();
    }

    function updateFinishButton() {
        if (acceptedBottles.length > 0) {
            $('#finish-sale-button')
                .prop('disabled', false)
                .html(`ขายเสร็จสิ้น (ส่งข้อมูล <span id="finish-count-display">${acceptedBottles.length}</span> รายการ)`);
        } else {
            $('#finish-sale-button')
                .prop('disabled', true)
                .html(`ขายเสร็จสิ้น (ส่งข้อมูล 0 รายการ)`);
        }
    }
    
    // --- 1. โหลดโมเดล Teachable Machine ---
    async function initTeachableMachine() {
        const modelURL = URL + "model.json";
        const metadataURL = URL + "metadata.json";

        try {
            updateStatus('⏳ กำลังโหลดโมเดล AI...', 'alert-primary');
            model = await tmImage.load(modelURL, metadataURL);
            maxPredictions = model.getTotalClasses();
            isModelLoaded = true;
            
            updateStatus('✅ โมเดล AI พร้อมใช้งาน. กำลังรอการตรวจพบวัตถุ...', 'alert-success');
            // **เรียก Polling ทันทีที่โมเดลพร้อม**
            startObjectPolling(); 
        } catch (error) {
            updateStatus('❌ โหลดโมเดล AI ล้มเหลว. โปรดตรวจสอบ URL.', 'alert-danger');
            console.error("Teachable Machine Load Error:", error);
        }
    }
    
    // --- 2. ฟังก์ชัน Polling เพื่อรอสัญญาณเซนเซอร์ (Simulated) ---
    function startObjectPolling() {
        if (pollingIntervalId) {
            clearInterval(pollingIntervalId); 
        }
        
        pollingIntervalId = setInterval(() => {
            updateStatus('⏳ กำลังรอสัญญาณตรวจจับวัตถุ (has_new_object=1) Polling...', 'alert-info');

            // *** MOCK LOGIC: จำลองการตรวจพบวัตถุทุก 4 รอบ (8 วินาที) ***
            // ในระบบจริง: ให้ใช้ $.get('/api/device/status', ...) เพื่อดึงค่า has_new_object
            mockPollingCount++;
            
            if (mockPollingCount % 4 === 0) { 
                // ตรวจพบวัตถุใหม่!
                clearInterval(pollingIntervalId); // หยุด Polling
                pollingIntervalId = null;
                updateStatus('✅ ตรวจพบวัตถุใหม่แล้ว! กำลังเปิดกล้อง...', 'alert-success');
                
                // เริ่มกระบวนการ Capture
                startWebcam();
            }
            
        }, POLLING_RATE); 
    }

    /** เปิดกล้อง (Webcam) และเตรียมถ่ายภาพ */
     function startWebcam() {
        // กำหนด constraints ให้ตรงกับขนาด 224x224 ที่ใช้ใน Teachable Machine
        navigator.mediaDevices.getUserMedia({ 
            video: { 
                 width: 224, 
                 height: 224,
                 facingMode: "environment" // ใช้กล้องหลังสำหรับ RVM
            } 
        })
            .then(function(stream) {
                const video = document.getElementById('webcam-preview');
                video.srcObject = stream;
                toggleDisplay('#webcam-preview'); 

                video.onloadedmetadata = function() {
                    updateStatus('📸 กล้องเปิดแล้ว... เตรียมถ่ายภาพใน 3 วินาที', 'alert-success');
                    
                    setTimeout(() => {
                        captureAndClassify(stream);
                    }, 3000); 
                };
            })
            .catch(function(err) {
                // หากเข้าถึงกล้องไม่ได้ ให้กลับไป Polling
                updateStatus(`❌ ไม่สามารถเข้าถึงกล้องได้: ${err.name}. กลับไปรอสัญญาณเซนเซอร์`, 'alert-danger');
                console.error("Error accessing webcam: ", err);
                // กลับไป Polling ใหม่
                startObjectPolling(); 
            });
    }
    
    // --- 3. ถ่ายภาพและจำแนกประเภท (Classification Logic) ---
    async function captureAndClassify(stream) {
        const video = document.getElementById('webcam-preview');
        const canvas = document.getElementById('photo-canvas');
        const context = canvas.getContext('2d');
        const WIDTH = 224;
        const HEIGHT = 224;

        canvas.width = WIDTH;
        canvas.height = HEIGHT;
        context.drawImage(video, 0, 0, WIDTH, HEIGHT);

        // ปิดกล้องทันทีหลังถ่ายภาพเสร็จ
        stream.getTracks().forEach(track => track.stop());
        
        const base64Image = canvas.toDataURL('image/jpeg', 0.7); 
        toggleDisplay('#captured-image');
        $('#captured-image').attr('src', base64Image);
        
        updateStatus('🔬 กำลังจำแนกประเภทด้วย AI...', 'alert-warning');
        
        const prediction = await model.predict(canvas); 

        let highestProbability = 0;
        let predictedLabel = 'Unknown';

        for (let i = 0; i < maxPredictions; i++) {
            const probability = prediction[i].probability.toFixed(2);
            if (probability > highestProbability) {
                highestProbability = probability;
                predictedLabel = prediction[i].className; 
            }
        }
        
        // --- Logic การตัดสินใจ (Accept/Reject) ---
        $('#label-container').text(`${predictedLabel} (${(highestProbability * 100).toFixed(0)}%)`);

        const labelLower = predictedLabel.toLowerCase();
        
        if (labelLower.includes('background') || labelLower.includes('notbottle') || highestProbability < CONFIDENCE_THRESHOLD) {
            
            // **สถานะ: ถูกปฏิเสธ (Reject)**
            updateStatus(`⚠️ ถูกปฏิเสธ: ${predictedLabel}. กรุณาดึงขวดออก (ความมั่นใจ ${(highestProbability * 100).toFixed(0)}%)`, 'alert-danger');
            
            // 1. ส่งค่า Reject/Error ไปยัง ESP8266 (commandCode=1)
            sendControlSignal(1); 
            
        } else {
            
            // **สถานะ: ยอมรับ (Accept)**
            updateStatus(`✅ ขวดถูกยอมรับ: ${predictedLabel}. พร้อมรับขวดถัดไป`, 'alert-success');
            
            const bottleVolumeMatch = predictedLabel.match(/(\d+)ml/);
            const volume = bottleVolumeMatch ? parseInt(bottleVolumeMatch[1]) : 0;
            
            // 1. เก็บข้อมูลไว้ใน Array/Object
            const newBottle = {
                image: base64Image,
                label: predictedLabel,
                confidence: highestProbability,
                volume: volume,
            };
            acceptedBottles.push(newBottle);
            
            // 2. อัปเดตตารางและปุ่ม Finish
            updateBottleList(newBottle);
            updateFinishButton();
            
            // 3. ส่งสัญญาณ Accept/Ready to receive next (commandCode=0)
            sendControlSignal(0);
        }
        
        // **หลังเสร็จสิ้นกระบวนการ (Accept หรือ Reject) ให้กลับไปรอสัญญาณเซนเซอร์**
        startObjectPolling();
    }
    
    // --- 4. ฟังก์ชันส่งสัญญาณควบคุม (0=Accept/Finished, 1=Reject/Error) ---
    function sendControlSignal(commandCode) {
        // *** MOCK: ในระบบจริงให้ใช้ $.post() หรือ fetch() เพื่อส่งข้อมูลไปยัง Server ***
        // เช่น: /api/device/control, { command_code: commandCode, machine_id: 'RVM101' }
        if (commandCode === 1) {
             console.log("Control signal sent: REJECT (1).");
        } else {
             console.log("Control signal sent: ACCEPT/FINISHED (0).");
        }
    }

    // --- 5. ฟังก์ชันอัปเดตตารางแสดงผล (Frontend List) ---
    function updateBottleList(bottle) {
        const listBody = $('#sale-list-body');
        
        if (bottleCount === 0) {
            listBody.empty(); // ล้างข้อความ "ไม่มีรายการ"
        }
        
        bottleCount++;
        
        const newRow = `
            <tr>
                <td>${bottleCount}</td>
                <td><img src="${bottle.image}" alt="${bottle.label}" style="width: 50px; height: 50px; object-fit: cover;"></td>
                <td>${bottle.label}</td>
                <td>${bottle.volume} ml</td>
                <td>${(bottle.confidence * 100).toFixed(0)}%</td>
            </tr>
        `;
        
        listBody.append(newRow);
        $('#bottle-count-display').text(bottleCount);
    }
    
    // --- 6. ฟังก์ชันจบการขายและส่งข้อมูลทั้งหมดไป Server ---
    function finishSale() {
        if (acceptedBottles.length === 0) {
            updateStatus('⚠️ ไม่มีขวดที่ถูกยอมรับในการขายนี้.', 'alert-warning');
            updateFinishButton();
            return;
        }

        // ก่อนส่งข้อมูล ให้หยุด Polling ชั่วคราว (ถ้ากำลังทำงานอยู่)
        if (pollingIntervalId) {
            clearInterval(pollingIntervalId);
            pollingIntervalId = null;
        }
        
        // *** MOCK: ในระบบจริง ให้ส่ง Array นี้ไปยัง Server ***
        $.ajax({
            url: '/api/bottle/finish-sale', 
            method: 'POST',
            data: JSON.stringify({
                // _token: '{{ csrf_token() }}', // ต้องมีใน Laravel
                bottles: acceptedBottles
            }),
            contentType: 'application/json',
            success: function(response) {
                updateStatus(`✅ การขายเสร็จสมบูรณ์! ${acceptedBottles.length} ขวด`, 'alert-success');
                
                // ล้างตะกร้าและ UI
                acceptedBottles = [];
                bottleCount = 0;
                $('#sale-list-body').empty().append('<tr><td colspan="5">ยังไม่มีขวดที่ยอมรับ</td></tr>');
            },
            error: function(xhr) {
                updateStatus('❌ เกิดข้อผิดพลาดในการส่งข้อมูลการขาย (Mock Fail).', 'alert-danger');
                console.error("Finish Sale Error:", xhr.responseText);
            },
            complete: function() {
                // กลับไปรอสัญญาณเซนเซอร์อีกครั้ง
                startObjectPolling();
                updateFinishButton();
                $('#bottle-count-display').text(0);
            }
        });
    }

    // --- 7. เริ่มต้น ---
    $(document).ready(function() {
        initTeachableMachine();
    });

    </script>
</body>
</html>
