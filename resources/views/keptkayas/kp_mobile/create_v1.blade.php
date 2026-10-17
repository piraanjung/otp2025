<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>♻️ ตู้หยอดขวดอัตโนมัติ (Sensor Auto-Trigger) ♻️</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@latest/dist/tf.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@teachablemachine/image@latest/dist/teachablemachine-image.min.js"></script>
    
    <style>
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
                        <h1 class="h3 mb-0">♻️ ตู้หยอดขวดอัตโนมัติ (Sensor Auto-Trigger) ♻️</h1>
                    </div>
                    <div class="card-body">
                        
                        <div class="alert alert-info text-center" role="alert">
                            <span id="status-display">...กำลังโหลดโมเดล AI...</span>
                        </div>

                        {{-- ปุ่มจบการขาย --}}
                        <div class="d-grid gap-2 mb-4">
                            <button id="finish-sale-button" class="btn btn-info btn-lg" disabled onclick="finishSale()">
                                ขายเสร็จสิ้น (ส่งข้อมูล <span id="finish-count-display">0</span> รายการ)
                            </button>
                        </div>
                        
                        <h4 class="card-title text-center mb-3">ภาพถ่ายขวด</h4>
                        <div class="video-container bg-light p-3 mb-4 rounded">
                            <video id="webcam-preview" style="display:none; width: 224px; height: 224px;" autoplay></video>
                            <canvas id="photo-canvas" style="display:none;"></canvas>
                            <img id="captured-image" style="display:none; width: 224px; height: 224px;" alt="ขวดที่ถูกถ่าย" class="img-fluid" />
                            <p id="placeholder-text" class="text-muted">กำลังรอสัญญาณตรวจจับวัตถุ</p>
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
                                        <th>จำนวน</th>
                                        <th>เป็นเงิน(บาท)</th>
                                        <th>คะแนน(แต้ม)</th>
                                    </tr>
                                </thead>
                                <tbody id="sale-list-body">
                                    <tr>
                                        <td colspan="6">ยังไม่มีขวดที่ยอมรับ</td>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <script>
    // **ตัวแปร Global**
    const URL = "https://teachablemachine.withgoogle.com/models/n_4Cu6X1N/"; 
    const CONFIDENCE_THRESHOLD = 0.60; 

    let model, maxPredictions;
    let isModelLoaded = false;
    let acceptedBottles = []; 
    let bottleCount = 0;
    let pollingIntervalId = null;
    let PRICE_CONFIG = []; // << **ตัวแปรใหม่** สำหรับเก็บราคา/คะแนน

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
        const count = acceptedBottles.length;
        $('#finish-count-display').text(count); 
        
        if (count > 0) {
            $('#finish-sale-button').prop('disabled', false);
        } else {
            $('#finish-sale-button').prop('disabled', true);
        }
    }
    
    // --- 1. โหลด Configuration และ Model ---
    
    /** * ดึงข้อมูลราคาและคะแนนจาก Server ก่อนเริ่ม Polling
     * **สำคัญ:** คุณต้องสร้าง API Endpoint นี้ใน Laravel ก่อน
     */
    async function loadPriceConfiguration() {
        updateStatus('⏳ กำลังดึงข้อมูลราคา/คะแนน...', 'alert-primary');
        try {
            // **เปลี่ยน URL นี้ให้เป็น Endpoint ที่ส่ง JSON config กลับมา**
            const response = await $.get('/api/device/config-price-points'); 
            PRICE_CONFIG = response;
            console.log("Price Configuration Loaded:", PRICE_CONFIG);
            initTeachableMachine();
        } catch (error) {
            updateStatus('❌ โหลด Config ราคา/คะแนนล้มเหลว. ตรวจสอบ Server.', 'alert-danger');
            console.error("Price Config Error:", error);
        }
    }
    
    async function initTeachableMachine() {
        const modelURL = URL + "model.json";
        const metadataURL = URL + "metadata.json";

        try {
            updateStatus('⏳ กำลังโหลดโมเดล AI...', 'alert-primary');
            model = await tmImage.load(modelURL, metadataURL);
            maxPredictions = model.getTotalClasses();
            isModelLoaded = true;
            
            updateStatus('✅ โมเดล AI พร้อมใช้งาน. กำลังรอการตรวจพบวัตถุ...', 'alert-success');
            startObjectPolling(); 
        } catch (error) {
            updateStatus('❌ โหลดโมเดล TM ล้มเหลว! ตรวจสอบ URL.', 'alert-danger');
            console.error("TM Load Error:", error);
        }
    }
    
    // --- 2. ฟังก์ชัน Polling และ Webcam ---
    function startObjectPolling() {
        if (pollingIntervalId) {
            clearInterval(pollingIntervalId); 
            pollingIntervalId = null; 
        }
        
        pollingIntervalId = setInterval(() => {
            updateStatus('⏳ กำลังรอสัญญาณตรวจจับวัตถุ (has_new_object=1)...', 'alert-info');

            $.get('/api/device/check-object-status', function(data) {
                if (data.has_new_object == 1) {
                    clearInterval(pollingIntervalId); 
                    pollingIntervalId = null;
                    updateStatus('✅ ตรวจพบวัตถุใหม่แล้ว! กำลังเปิดกล้อง...', 'alert-success');
                    startWebcam();
                }
            }).fail(function() {
                 // Ignore failure for continuous Polling
            });
        }, 2000); 
    }

    function startWebcam() {
        navigator.mediaDevices.getUserMedia({ 
            video: { 
                width: { ideal: 640 }, 
                height: { ideal: 480 }, 
                resizeMode: 'none' 
            } 
        })
        .then(function(stream) {
            const video = document.getElementById('webcam-preview');
            video.srcObject = stream;
            toggleDisplay('#webcam-preview'); 

            video.onloadedmetadata = function(e) {
                updateStatus('📸 กล้องเปิดแล้ว... เตรียมถ่ายภาพใน 3 วินาที', 'alert-success');
                
                setTimeout(() => {
                    captureAndClassify(stream);
                }, 3000); 
            };
        })
        .catch(function(err) {
            updateStatus(`❌ ไม่สามารถเข้าถึงกล้องได้: ${err.name}. กลับไปรอสัญญาณเซนเซอร์`, 'alert-danger');
            startObjectPolling(); 
        });
    }

    // --- 3. ถ่ายภาพและจำแนกประเภท (พร้อมคำนวณราคา) ---
    async function captureAndClassify(stream) {
        const video = document.getElementById('webcam-preview');
        const canvas = document.getElementById('photo-canvas');
        const context = canvas.getContext('2d');
        const WIDTH = 224;
        const HEIGHT = 224;
        // ... (โค้ด Capture และ Stop Stream เดิม) ...
        canvas.width = WIDTH;
        canvas.height = HEIGHT;
        context.drawImage(video, 0, 0, WIDTH, HEIGHT);

        if (stream && stream.getTracks) {
            stream.getTracks().forEach(track => { if (track.kind === 'video') { track.stop(); } });
        }
        video.srcObject = null; 

        const base64Image = canvas.toDataURL('image/jpeg', 0.7); 
        toggleDisplay('#captured-image');
        $('#captured-image').attr('src', base64Image);
        
        if (!isModelLoaded || !model) {
            updateStatus('❌ โมเดล AI ยังไม่พร้อม! ไม่สามารถจำแนกประเภทได้.', 'alert-danger');
            sendRejectSignal(1); resetObjectStatus(); startObjectPolling(); return; 
        }
        
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
        
        $('#label-container').html(`**${predictedLabel}** (${(highestProbability * 100).toFixed(0)}%)`);

        const labelLower = predictedLabel.toLowerCase();
        
        // **Logic การ Reject/Accept**
        if (labelLower.includes('background') || labelLower.includes('notbottle') || highestProbability < CONFIDENCE_THRESHOLD) {
            
            // **REJECT**
            updateStatus(`⚠️ ถูกปฏิเสธ: ${predictedLabel}. กรุณาดึงขวดออก`, 'alert-danger');
            sendRejectSignal(1); 
            
        } else {
            
            // **ACCEPT**
            
            // 1. หา Configuration ที่ตรงกัน
            const labelKey = predictedLabel.toLowerCase()//.replace(/[\s\d_.]/g, ''); // สร้างคีย์แบบไม่มีช่องว่าง/ตัวเลข
           console.log('labelKey',labelKey)
            const config = PRICE_CONFIG.find(c => c.kp_itemscode.toLowerCase() === labelKey);
            
            if (!config) {
                updateStatus(`⚠️ ถูกปฏิเสธ: ไม่พบข้อมูลราคาสำหรับ ${predictedLabel}.`, 'alert-danger');
                sendRejectSignal(1);
                resetObjectStatus(); 
                startObjectPolling();
                return;
            }

            // 2. คำนวณราคารวมและคะแนน (สมมติ 1 ชิ้น = 1 หน่วย)
            const amountInUnits = 1; 
            // const priceConfig = config.price_config;

            const totalAmount = amountInUnits * config.price_per_unit;
            const totalPoints = amountInUnits * config.point_per_unit;

            updateStatus(`✅ ขวดถูกยอมรับ: ${predictedLabel}. ได้รับ ${totalAmount.toFixed(2)} บาท`, 'alert-success');
            
            // 3. สร้าง Object ที่มีข้อมูลครบถ้วนสำหรับ Transaction
            const newBottle = {
                user_id: '{{Illuminate\Support\Facades\Auth::id()}}',
                image: base64Image,
                label: predictedLabel,
                confidence: highestProbability,
                recycle_machine:1,
                // **ข้อมูลสำหรับ Transaction Detail (ตามที่ Controller ต้องการ)**
                kp_tbank_item_id: config.kp_tbank_item_id,
                unit_name: config.unit_name, 
                kp_tbank_items_pricepoint_id: config.kp_tbank_items_pricepoint_id,
                amount_in_units: amountInUnits, 
                price_per_unit: config.price_per_unit,
                amount: totalAmount.toFixed(2), 
                points: totalPoints.toFixed(2), 
            };
            acceptedBottles.push(newBottle);
            
            updateBottleList(newBottle);
            updateFinishButton();
        }
        
        // **จุดสำคัญ:** รีเซ็ตสถานะเซนเซอร์ให้เป็น 0 เสมอ หลังจบกระบวนการ
        resetObjectStatus();
        
        // กลับไปรอสัญญาณเซนเซอร์
        startObjectPolling();
    }
    
    // --- 4. ฟังก์ชัน API Control ---
    function sendRejectSignal(rejectValue) {
        $.post('/api/device/control', { 
            _token: '{{ csrf_token() }}',
            reject: rejectValue
        }, function(response) {
            console.log("Reject signal sent:", response);
        }).fail(function() {
            console.error("Failed to send reject signal.");
        });
    }

    function resetObjectStatus() {
        $.post('/api/device/status-simulator', { 
            _token: '{{ csrf_token() }}',
            has_new_object: 0
        }).fail(function() {
            console.error("Failed to send object status reset signal.");
        });
    }

    // --- 5. ฟังก์ชันอัปเดตตารางแสดงผล (Frontend List) ---
    function updateBottleList(bottle) {
        const listBody = $('#sale-list-body');
        
        if (bottleCount === 0) {
            listBody.empty(); 
        }
        
        bottleCount++;
        
        const newRow = `
            <tr>
                <td>${bottleCount}</td>
                <td><img src="${bottle.image}" alt="${bottle.label}" style="width: 50px; height: 50px; object-fit: cover;"></td>
                <td>${bottle.label}</td>
                <td>1</td>
                <td>${bottle.amount}</td>
                <td>${bottle.points}</td>
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

        if (pollingIntervalId) {
            clearInterval(pollingIntervalId);
            pollingIntervalId = null;
        }

        $('#finish-sale-button').prop('disabled', true).text('🚀 กำลังบันทึกธุรกรรม...');
        updateStatus('🚀 กำลังบันทึกธุรกรรมการขาย...', 'alert-info');
        
        // Controller ของคุณดึงข้อมูลจาก Session ดังนั้น Frontend แค่ส่ง Token
        $.ajax({
            url: '{{ route('keptkayas.purchase.save_transaction_machine') }}', 
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                acceptedBottles:acceptedBottles
            },
            success: function(response) {
                updateStatus('✅ การขายเสร็จสมบูรณ์! ระบบได้บันทึกข้อมูลแล้ว', 'alert-success');
                
                acceptedBottles = [];
                bottleCount = 0;
                $('#sale-list-body').empty().append('<tr><td colspan="5">ยังไม่มีขวดที่ยอมรับ</td></tr>');
                
                if (response.redirect_url) {
                     window.location.href = response.redirect_url;
                }
            },
            error: function(xhr) {
                console.log('response', response)
                updateStatus('❌ เกิดข้อผิดพลาดในการบันทึกการขาย.', 'alert-danger');
                console.error("Finish Sale Error:", xhr.responseText);
            },
            complete: function(response) {
                console.log('response', response)

                startObjectPolling(); 
                updateFinishButton();
                $('#finish-sale-button').text('ขายเสร็จสิ้น (ส่งข้อมูล 0 รายการ)');
                $('#bottle-count-display').text(0);
            }
        });
    }

    // --- 7. เริ่มต้น ---
    $(document).ready(function() {
        // เริ่มต้นด้วยการโหลด Config ก่อน
        loadPriceConfiguration();
    });

    </script>
</body>
</html>