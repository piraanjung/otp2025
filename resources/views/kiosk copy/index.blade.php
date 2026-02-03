<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Smart Recycle (Snapshot Mode)/</title>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@latest/dist/tf.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@teachablemachine/image@latest/dist/teachablemachine-image.min.js"></script>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;600&family=Varela+Round&display=swap" rel="stylesheet">

    <style>
        :root { --bg-gradient: linear-gradient(135deg, #a18cd1 0%, #fbc2eb 100%); --glass-bg: rgba(255, 255, 255, 0.85); }
        * { box-sizing: border-box; -webkit-tap-highlight-color: transparent; }
        body { font-family: 'Kanit', sans-serif; margin: 0; padding: 0; height: 100dvh; background: var(--bg-gradient); display: flex; flex-direction: column; overflow: hidden; user-select: none; }
        .hidden { display: none !important; }

        /* Screensaver */
        #screensaver { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: #000; z-index: 9999; display: flex; align-items: center; justify-content: center; color: white; }
        .saver-content { text-align: center; animation: breathe 3s infinite; opacity: 0.8; }
        @keyframes breathe { 0%, 100% { transform: scale(1); opacity: 0.6; } 50% { transform: scale(1.05); opacity: 1; } }

        /* Login Page */
        #login-page { flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 20px; }
        .logo-icon { font-size: 3.5rem; color: white; }
        .numpad-display input { width: 100%; border: none; background: rgba(255,255,255,0.9); border-radius: 20px; padding: 15px; font-size: 1.8rem; text-align: center; margin-bottom: 20px; }
        .numpad-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; width: 100%; max-width: 320px; }
        .num-btn { background: rgba(255,255,255,0.4); border-radius: 50%; aspect-ratio: 1; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; color: white; cursor: pointer; }
        .num-btn:active { transform: scale(0.9); background: rgba(255,255,255,0.8); }
        .enter { grid-column: span 3; border-radius: 50px; aspect-ratio: auto; padding: 15px; background: #2ecc71; margin-top: 10px; }

        /* Scan Page */
        #scan-page { flex: 1; display: flex; flex-direction: column; position: relative; }
        .camera-wrapper { flex: 1; margin: 15px; background: #000; border-radius: 20px; overflow: hidden; position: relative; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 15px rgba(0,0,0,0.2); }

        /* Canvas & Video Styling */
        #video { position: absolute; top:0; left:0; width:100%; height:100%; object-fit:cover; opacity: 0; z-index: -1; } /* ซ่อน Video ไว้ข้างหลัง */
        #displayCanvas { width: 100%; height: 100%; object-fit: cover; background-color: #000; } /* โชว์ Canvas แทน */

        .status-pill { position: absolute; bottom: 20px; left: 50%; transform: translateX(-50%); background: rgba(0,0,0,0.6); color: white; padding: 8px 20px; border-radius: 30px; backdrop-filter: blur(5px); transition: 0.3s; z-index: 10; }
        .mode-scanning { border: 2px solid #2ecc71; color: #2ecc71; }
        .mode-processing { border: 2px solid #f1c40f; color: #f1c40f; }
        .mode-error { border: 2px solid #e74c3c; color: #e74c3c; }

        /* Floating Button */
        .fab-btn { position: fixed; bottom: 100px; right: 20px; width: 60px; height: 60px; border-radius: 50%; background: linear-gradient(135deg, #667eea, #764ba2); color: white; border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.3); z-index: 100; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; }
        .fab-btn:active { transform: scale(0.9); }

        .footer-action { background: white; padding: 15px 20px; padding-bottom: calc(15px + env(safe-area-inset-bottom)); }
        .btn-finish { width: 100%; border: none; border-radius: 50px; background: linear-gradient(135deg, #667eea, #764ba2); color: white; padding: 15px; font-weight: bold; font-size: 1.1rem; display: flex; justify-content: space-between; }
    </style>
</head>

<body>
    <div id="screensaver" class="hidden" onclick="wakeUp()">
        <div class="saver-content"><div style="font-size: 5rem;">♻️</div><p>แตะเพื่อเริ่มใช้งาน</p></div>
    </div>
    <button onclick="connectUSB()" class="btn btn-warning w-100 mb-2">🔄 Reconnect USB</button>
<div id="debug-log" style="position:fixed; bottom:0; left:0; width:100%; height:150px; background:black; color:#0f0; overflow:scroll; z-index:9999; font-size:12px; padding:10px;">
    Waiting for loga...
</div>
            <div class="num-btn enter" onclick="doLogin()">🚀 เข้าสู่ระบบ</div>

    <div id="login-page">
        <div class="logo-icon">♻️</div>
        <h2 style="color:white; margin-bottom:20px;">Smart Recycleถ</h2>
        <div class="numpad-display"><input type="text" id="phone-display" readonly placeholder="เบอร์โทรศัพท์" value="0999999999"></div>
        <div class="numpad-grid">
            <div class="num-btn" onclick="addNum('1')">1</div><div class="num-btn" onclick="addNum('2')">2</div><div class="num-btn" onclick="addNum('3')">3</div>
            <div class="num-btn" onclick="addNum('4')">4</div><div class="num-btn" onclick="addNum('5')">5</div><div class="num-btn" onclick="addNum('6')">6</div>
            <div class="num-btn" onclick="addNum('7')">7</div><div class="num-btn" onclick="addNum('8')">8</div><div class="num-btn" onclick="addNum('9')">9</div>
            <div class="num-btn" style="background:rgba(255,100,100,0.5)" onclick="delNum()">⌫</div><div class="num-btn" onclick="addNum('0')">0</div>
            {{-- <div class="num-btn enter" onclick="doLogin()">🚀 เข้าสู่ระบบ</div> --}}
        </div>
    </div>

    <div id="scan-page" class="hidden">
        <div style="padding:10px 15px; color:white; display:flex; justify-content:space-between; align-items:center;">
            <div><span class="result fw-bold">User</span> <small>(<span id="user-score-db">0</span> แต้ม)</small></div>
            <button class="btn btn-sm btn-light rounded-pill" onclick="logout()">Logout</button>
        </div>

        <div class="camera-wrapper">
            <video id="video" autoplay playsinline muted></video>
            <canvas id="displayCanvas"></canvas>
            {{-- <div id="status-pill" class="status-pill mode-scanning">📡 รอขยะ...</div> --}}
        </div>

        <button type="button" class="fab-btn" onclick="showDetailsModal()">
            <i class="bi bi-basket3-fill"></i>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-light">
                <span id="summary-count">0</span>
            </span>
        </button>

        <div class="footer-action">
            <button class="btn-finish" onclick="finishSession()">
                <span>✅ ยืนยัน & บันทึก</span>
                <span class="badge bg-light text-dark rounded-pill">รวม <span id="session-total">0</span> แต้ม</span>
            </button>
        </div>
    </div>

    <canvas id="captureCanvas" width="640" height="480" style="display:none;"></canvas>

    <div class="modal fade" id="detailModal" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">รายการที่หยอด</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body p-0"><table class="table table-striped mb-0"><tbody id="stats-body"></tbody></table><div id="modal-empty-state" class="text-center p-4 text-muted">📭 ยังไม่มีรายการ</div></div><div class="modal-footer"><small class="text-muted me-auto">AI: <span id="ai-debug-modal">Idle</span></small><button class="btn btn-primary" data-bs-dismiss="modal">ตกลง</button></div></div></div></div>

    <script>
        // --- CONFIG ---
        const NODE_MCU_IP = "http://10.52.7.161"; // ⚠️ แก้ IP ให้ตรงกับ Serial Monitor
        const TM_URL = "https://teachablemachine.withgoogle.com/models/n_4Cu6X1N/";
        const IDLE_TIMEOUT_SEC = 600;

        // --- GLOBAL VARS ---
        let model, isModelLoaded = false;
        let isSystemRunning = false, isDeepSleep = false;
        let idleInterval, idleTime = 0;
        let sessionData = {}, sessionTotalScore = 0;
        let currentUserPhone = "", videoStream = null;

        const delay = ms => new Promise(res => setTimeout(res, ms));

        // --- 1. SYSTEM LOOP (ASYNC/AWAIT) ---
        async function startSystemLoop() {
            // if (isSystemRunning) return;
            // isSystemRunning = true;
            // clearDisplayCanvas(); // เริ่มต้นด้วยจอดำ
            // setStatus("scanning", "📡 ระบบพร้อม... กรุณาวางขยะ");
if (isSystemRunning) return;
    isSystemRunning = true;
    clearDisplayCanvas();
    setStatus("scanning", "📡 ระบบพร้อม (USB)... กรุณาวางขยะ");
        //     while (isSystemRunning) {
        //         // ถ้าพักหน้าจอ หรืออยู่หน้า Login ให้ข้าม
        //         if (isDeepSleep || $('#scan-page').hasClass('hidden')) { await delay(1000); continue; }

        //         try {
        //             // 1. เช็ค Sensor (Timeout 5วิ)
        //             const status = await $.ajax({ url: NODE_MCU_IP + "/check", type: "GET", timeout: 5000 });

        //             if (status === "DETECTED") {
        //                 // 2. ถ้าเจอของ -> ถ่ายรูป & วิเคราะห์
        //                 await processDetectionSequence();
        //             }

        //             // พัก 1 วิ ลดภาระ CPU/NodeMCU (ประหยัดไฟ)
        //             await delay(1000);

        //         } catch (err) {
        //             console.log("Waiting for sensor...", err.statusText);
        //             await delay(2000); // ถ้าต่อไม่ได้ ให้รอนานหน่อย
        //         }
        //     }
        // }

        async function processDetectionSequence() {
            // 1. เริ่มถ่ายภาพ (ข้อความจะ Overlay ทับรูปถ่าย)
            setStatus("processing", "กำลังถ่ายภาพ...");

            await captureAndFreeze(); // จังหวะนี้จะได้รูปขยะมาบนจอ

            // 2. AI วิเคราะห์ (ข้อความ Overlay ทับรูปเดิม)
            setStatus("processing", "กำลังวิเคราะห์...");
            const aiResult = await predictSnapshot();

            if (aiResult && aiResult.probability > 0.85) {
                const label = aiResult.className;

                // 3. สั่งเปิดประตู
                setStatus("processing", `✨ เจอ: ${label} | กำลังเปิดประตู...`);

                try {
                    // const gateRes = await $.ajax({ url: NODE_MCU_IP + "/open-gate", type: "GET", timeout: 15000 });
                    sendOpenGateCommand(); // สั่ง NodeMCU ให้หมุน Servo
                    await delay(3000); // รอสัก 3 วินาที (สมมติว่าเปิดปิดเสร็จแล้ว)
                    addScore(label, 10);
                    setStatus("success", "รับคะแนนเรียบร้อย!");
                    // if (gateRes && gateRes.trim() === "DROPPED_OK") {
                    //     addScore(label, 10);
                    //     // 4. สำเร็จ! (Overlay สีเขียวทับรูป)
                    //     setStatus("success", "รับคะแนนเรียบร้อย!");
                    // } else {
                    //     alert("⚠️ ไม่พบการทิ้งขยะ");
                    //     setStatus("error", "❌ ไม่ได้รับคะแนน");
                    // }
                } catch (e) {
                    setStatus("error", "❌ เชื่อมต่อถังขยะไม่ได้");
                }

                await delay(2000); // โชว์ผลลัพธ์ค้างไว้
            } else {
                setStatus("error", "❌ ไม่รู้จักวัตถุนี้");
                await delay(2000);
            }

            // 5. เคลียร์หน้าจอ กลับสู่โหมดรอ (จอดำมีไอคอน)
            setStatus("scanning", "พร้อมทำงาน... วางชิ้นถัดไป");
        }
        // --- 2. CAMERA & SNAPSHOT LOGIC ---
        async function captureAndFreeze() {
            const video = document.getElementById('video');
            const displayCanvas = document.getElementById('displayCanvas');
            const ctx = displayCanvas.getContext('2d');

            // 1. ปลุกกล้อง
            toggleCamera(true);

            // 2. รอแสงเข้า (Warmup) 0.8 วิ
            await delay(800);

            // 3. ปรับขนาด Canvas ให้เท่า video จริง
            displayCanvas.width = video.videoWidth;
            displayCanvas.height = video.videoHeight;

            // 4. แชะ! (วาดภาพปัจจุบันลง Canvas)
            ctx.drawImage(video, 0, 0, displayCanvas.width, displayCanvas.height);

            // 5. ปิดกล้องทันที (ประหยัดไฟ)
            toggleCamera(false);

            // ตอนนี้ภาพจะค้างอยู่ที่ displayCanvas ให้ user เห็น
        }

        function clearDisplayCanvas() {
            const c = document.getElementById('displayCanvas');
            const ctx = c.getContext('2d');
            ctx.fillStyle = "#111"; // สีเทาดำ
            ctx.fillRect(0, 0, c.width, c.height);

            // วาดไอคอนกล้องตรงกลาง
            ctx.fillStyle = "#333";
            ctx.font = "50px Arial";
            ctx.textAlign = "center";
            ctx.fillText("📷", c.width/2, c.height/2);
        }

        async function initCamera() {
            if (!navigator.mediaDevices) return alert("Camera Error");
            try {
                const stream = await navigator.mediaDevices.getUserMedia({
                    video: { facingMode: "environment", width: {ideal: 1280}, height: {ideal: 720} }
                });
                videoStream = stream;
                const video = document.getElementById('video');
                video.srcObject = stream;
                // รอให้ video metadata โหลดเสร็จ
                await new Promise(r => video.onloadedmetadata = r);
                video.play();

                toggleCamera(false); // เริ่มต้นปิดไว้
                clearDisplayCanvas(); // วาดจอดำรอ
            } catch(e) { console.error(e); alert("Camera Init Failed"); }
        }

        function toggleCamera(enable) {
            if (videoStream) videoStream.getVideoTracks().forEach(t => t.enabled = enable);
        }

        // --- 3. AI PREDICTION ---
        async function initModel() {
            if (!isModelLoaded) {
                model = await tmImage.load(TM_URL + "model.json", TM_URL + "metadata.json");
                isModelLoaded = true;
            }
        }

        async function predictSnapshot() {
            if (!model) return null;
            // เอาภาพจาก displayCanvas (ที่แช่แข็งไว้) มาทาย
            const sourceCanvas = document.getElementById('displayCanvas');

            // ย่อลง captureCanvas ให้ AI
            const aiCanvas = document.getElementById('captureCanvas');
            const aiCtx = aiCanvas.getContext('2d');
            aiCanvas.width = 224; aiCanvas.height = 224; // ขนาดมาตรฐาน Teachable Machine
            aiCtx.drawImage(sourceCanvas, 0, 0, aiCanvas.width, aiCanvas.height);

            const prediction = await model.predict(aiCanvas);
            let best = prediction.reduce((p, c) => (p.probability > c.probability) ? p : c);

            $('#ai-debug-modal').text(`${best.className} (${(best.probability*100).toFixed(0)}%)`);

            if (best.className === "Background" || best.className === "Nothing") return null;
            return best;
        }

        // --- 4. UI HELPER ---
        function setStatus(mode, msg) {
            // mode จะมี: 'scanning', 'processing', 'success', 'error'
            drawCanvasUI(mode, msg);
        }

        function addScore(label, points) {
            if (!sessionData[label]) sessionData[label] = { count: 0, score: 0 };
            sessionData[label].count++;
            sessionData[label].score += points;
            sessionTotalScore += points;
            $('#summary-count').text(Object.values(sessionData).reduce((a,b)=>a+b.count,0));
            $('#session-total').text(sessionTotalScore);
            $('#user-score-db').text(sessionTotalScore); // Mock display

            // Add to Modal
            const rowId = 'row-' + label.replace(/\s+/g, '-');
            if ($('#' + rowId).length) {
                $(`#qty-${rowId}`).text(sessionData[label].count);
            } else {
                $('#stats-body').prepend(`<tr id="${rowId}"><td>${label}</td><td class="text-center"><span class="badge bg-light text-dark" id="qty-${rowId}">1</span></td><td class="text-end text-success">+${points}</td></tr>`);
                $('#modal-empty-state').hide();
            }
        }

        function showDetailsModal() { new bootstrap.Modal(document.getElementById('detailModal')).show(); }

        // --- 5. APP FLOW (LOGIN / LOGOUT) ---
        $(document).ready(() => {
            startIdleTimer();
            $(document).on('click touchstart', () => { idleTime = 0; wakeUp(); });
        });

        function startIdleTimer() {
            setInterval(() => {
                if (!isDeepSleep) {
                    idleTime++;
                    if (idleTime >= IDLE_TIMEOUT_SEC) {
                        isDeepSleep = true; $('#screensaver').removeClass('hidden');
                        toggleCamera(false);
                    }
                }
            }, 1000);
        }

        function wakeUp() {
            if(isDeepSleep) { isDeepSleep = false; $('#screensaver').addClass('hidden'); }
        }

        function addNum(n) { $('#phone-display').val($('#phone-display').val() + n); }
        function delNum() { let v = $('#phone-display').val(); $('#phone-display').val(v.slice(0, -1)); }

        async function doLogin() {
            let phone = $('#phone-display').val();
            if(phone.length < 4) return alert("เบอร์ผิด");

            $('.enter').text("⏳ Loading...");
            await initModel();
            await initCamera();

            // Login Success
            $('#login-page').addClass('hidden');
            $('#scan-page').removeClass('hidden');
            $('.result').text("Member " + phone.substring(phone.length-4));
            currentUserPhone = phone;

            // เริ่ม Loop ทำงาน
            startSystemLoop();
        }

        function finishSession() {
            alert("บันทึกข้อมูลเรียบร้อย!");
            location.reload();
        }

        function logout() { location.reload(); }

        // ฟังก์ชันวาด UI บน Canvas ตามสถานะ
function drawCanvasUI(state, text) {
    const canvas = document.getElementById('displayCanvas');
    const ctx = canvas.getContext('2d');
    const w = canvas.width;
    const h = canvas.height;

    // 1. ถ้าไม่ใช่โหมด Processing (คือโหมดรอ หรือ Error) ให้ล้างภาพเก่าแล้วลงพื้นหลัง
    // แต่ถ้าเป็นโหมด Processing เราจะวาดทับรููปถ่ายไปเลย (Overlay)
    if (state !== 'processing' && state !== 'success') {
        // ลงสีพื้นหลัง
        ctx.fillStyle = "#212529"; // สีเทาเข้ม (Dark Theme)
        ctx.fillRect(0, 0, w, h);
    }

    // 2. กำหนดสีและไอคอนตามสถานะ
    let icon = "📡";
    let color = "#ffffff"; // สีข้อความปกติ

    if (state === 'scanning') {
        icon = "♻️";
        color = "#2ecc71"; // เขียว
    } else if (state === 'processing') {
        icon = "⏳";
        color = "#f1c40f"; // เหลือง
        // วาดแถบดำโปร่งแสงรองพื้นข้อความหน่อย จะได้อ่านง่ายบนรูปถ่าย
        ctx.fillStyle = "rgba(0, 0, 0, 0.7)";
        ctx.fillRect(0, h - 100, w, 100);
    } else if (state === 'success') {
        icon = "✅";
        color = "#2ecc71"; // เขียว
        // วาดแถบดำโปร่งแสง
        ctx.fillStyle = "rgba(0, 0, 0, 0.7)";
        ctx.fillRect(0, h - 100, w, 100);
    } else if (state === 'error') {
        icon = "⚠️";
        color = "#e74c3c"; // แดง
        ctx.fillStyle = "#212529"; // เคลียร์เป็นจอดำเลยถ้า Error
        ctx.fillRect(0, 0, w, h);
    }

    // 3. เริ่มวาดข้อความและไอคอน
    ctx.textAlign = "center";
    ctx.textBaseline = "middle";

    if (state === 'processing' || state === 'success') {
        // --- แบบ Overlay (วาดทับรูปถ่ายด้านล่าง) ---
        ctx.font = "bold 24px 'Kanit', sans-serif";
        ctx.fillStyle = color;
        ctx.fillText(icon + " " + text, w / 2, h - 50); // วาดข้อความไว้ด้านล่าง
    } else {
        // --- แบบ Full Screen (โหมดรอ) ---
        // วาดไอคอนใหญ่ๆ ตรงกลาง
        ctx.font = "80px Arial";
        ctx.fillText(icon, w / 2, h / 2 - 30);

        // วาดข้อความ
        ctx.font = "bold 28px 'Kanit', sans-serif";
        ctx.fillStyle = color;
        ctx.fillText(text, w / 2, h / 2 + 50);

        ctx.font = "18px 'Kanit', sans-serif";
        ctx.fillStyle = "#aaaaaa";
        ctx.fillText("(กรุณาวางขยะลงในช่อง)", w / 2, h / 2 + 90);
    }
}

function logToScreen(message) {
    var logBox = document.getElementById("debug-log");
    // เพิ่มข้อความใหม่ไปด้านบนสุด
    logBox.innerHTML = "<div>" + new Date().toLocaleTimeString() + ": " + message + "</div>" + logBox.innerHTML;
}
    // 👇 เพิ่มฟังก์ชันนี้เข้าไปในไฟล์เดิมบน Server ได้เลย
    // --- USB SERIAL CONFIG ---
    let serialBuffer = ""; // ตัวพักข้อมูล USB
    let isConnected = false;

    // ฟังก์ชันเชื่อมต่อ USB (เรียกตอนกดปุ่ม หรือตอนแอปเริ่ม)
    function connectUSB() {
        if (typeof window.serial === 'undefined') {
            logToScreen("⚠️ ใช้งานผ่าน Browser ปกติ (จำลองโหมด)");
            return;
        }

        logToScreen("🔌 กำลังขออนุญาต USB...");
        window.serial.requestPermission(
            // Success Request
            function(success) {
                logToScreen("✅ ได้รับอนุญาต! กำลังเปิดพอร์ต...");
                window.serial.open(
                    { baudRate: 9600 }, // ต้องตรงกับ Serial.begin(9600) ใน NodeMCU
                    function(success) {
                        isConnected = true;
                        logToScreen("🚀 USB Connected พร้อมใช้งาน!");
                        registerReadCallback();
                    },
                    function(error) { logToScreen("❌ เปิดพอร์ตไม่ผ่าน: " + error); }
                );
            },
            // Error Request
            function(error) { logToScreen("❌ Permission Denied (ต้องกด Allow)"); }
        );
    }

    // ฟังก์ชันดักฟังข้อมูลจาก USB (สำคัญมาก!)
    function registerReadCallback() {
        window.serial.registerReadCallback(
            function(data) {
                // 1. แปลง Data เป็นตัวอักษร
                var view = new Uint8Array(data);
                var chunk = "";
                for(var i=0; i<view.length; i++) {
                    chunk += String.fromCharCode(view[i]);
                }

                // 2. เอาใส่ Buffer (ต่อหางไปเรื่อยๆ)
                serialBuffer += chunk;

                // 3. ถ้าเจอ "ขึ้นบรรทัดใหม่" (\n) แสดงว่าจบประโยค
                if (serialBuffer.includes("\n")) {
                    // แยกประโยคออกมา (เผื่อมาหลายบรรทัดติดกัน)
                    let lines = serialBuffer.split("\n");

                    // เก็บเศษที่เหลือไว้ใน Buffer (บรรทัดสุดท้ายที่อาจยังไม่จบ)
                    serialBuffer = lines.pop();

                    // วนลูปเช็คทุกบรรทัดที่สมบูรณ์แล้ว
                    lines.forEach(line => {
                        let cleanLine = line.trim(); // ตัดช่องว่างหัวท้าย
                        if (cleanLine.length > 0) processSerialCommand(cleanLine);
                    });
                }
            },
            function(error) { console.error(error); }
        );
    }

    let isProcessing = false; // 🚩 ตัวแปรเช็คสถานะการทำงาน

function processSerialCommand(command) {
    logToScreen("📩 RX: " + command);

    if (command === "DETECTED") {
        // เช็คเพิ่มว่า: ต้องไม่อยู่ระหว่าง process งานเก่า (isProcessing == false)
        if (!$('#scan-page').hasClass('hidden') && !isDeepSleep && !isProcessing) {

            isProcessing = true; // 🔒 ล็อคระบบ
            logToScreen("⚡ Sensor Triggered! เริ่มถ่ายภาพ...");

            // เรียกทำงาน และรอจนเสร็จค่อยปลดล็อค
            processDetectionSequence().then(() => {
                isProcessing = false; // 🔓 ปลดล็อคเมื่อจบกระบวนการ
                logToScreen("✅ Ready for next item");
            });
        }
    }
}

    // ฟังก์ชันสั่งเปิดประตู (ส่งข้อมูลกลับไปหา NodeMCU)
    function sendOpenGateCommand() {
        if (isConnected) {
            logToScreen("📤 ส่งคำสั่งเปิดประตู...");
            // ส่งคำว่า OPEN ตามด้วย \n เพื่อให้ NodeMCU รู้ว่าจบคำสั่ง
            window.serial.write("OPEN\n",
                function(s) { logToScreen("✅ ส่งคำสั่งสำเร็จ"); },
                function(e) { logToScreen("❌ ส่งไม่ไป: " + e); }
            );
        } else {
            logToScreen("⚠️ USB ไม่ได้ต่อ (Simulation Mode)");
            // จำลองว่าเปิดได้ เพื่อทดสอบ
            return Promise.resolve("DROPPED_OK");
        }
    }

    // --- แก้ไขฟังก์ชันเดิม processDetectionSequence เพื่อใช้ USB แทน HTTP ---
    // (ก๊อปฟังก์ชันนี้ไปทับของเดิมด้านบน หรือแก้ใน logic เดิม)
    /* ⚠️ หมายเหตุ: คุณต้องไปแก้ใน script ด้านบน
       ตรงจุดที่เรียก $.ajax open-gate ให้เปลี่ยนเป็นเรียก sendOpenGateCommand()
       และตรง startSystemLoop ให้เอา $.ajax polling ออก
    */

    // Helper Log
    function logToScreen(message) {
        var logBox = document.getElementById("debug-log");
        var time = new Date().toLocaleTimeString();
        logBox.innerHTML = `<div><span style="color:#aaa">[${time}]</span> ${message}</div>` + logBox.innerHTML;
    }

    // Auto Connect เมื่อเข้าแอป
    document.addEventListener('deviceready', connectUSB, false);

</script>
</body>
</html>
