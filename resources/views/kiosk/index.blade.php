<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Smart Recycle (Kiosk Mode)</title>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@latest/dist/tf.min.js"></script>
    <script
        src="https://cdn.jsdelivr.net/npm/@teachablemachine/image@latest/dist/teachablemachine-image.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;600&family=Varela+Round&display=swap"
        rel="stylesheet">

    <style>
        /* --- 1. GLOBAL SETTINGS --- */
        :root {
            --bg-gradient: linear-gradient(135deg, #a18cd1 0%, #fbc2eb 100%);
            --glass-bg: rgba(255, 255, 255, 0.85);
            --primary: #667eea;
            --text-main: #4a4a4a;
        }

        * {
            box-sizing: border-box;
            -webkit-tap-highlight-color: transparent;
        }

        body {
            font-family: 'Kanit', sans-serif;
            margin: 0;
            padding: 0;
            height: 100vh;
            background: var(--bg-gradient);
            color: var(--text-main);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            user-select: none;
        }

        .hidden {
            display: none !important;
        }

        /* --- 2. SCREENSAVER (DEEP SLEEP) --- */
        #screensaver {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: #000000;
            /* ดำสนิท */
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            cursor: pointer;
        }

        .saver-content {
            text-align: center;
            animation: breathe 3s infinite ease-in-out;
            opacity: 0.8;
        }

        .saver-content p {
            margin-top: 15px;
            font-size: 1.2rem;
            color: #aaa;
        }

        @keyframes breathe {

            0%,
            100% {
                transform: scale(1);
                opacity: 0.6;
            }

            50% {
                transform: scale(1.05);
                opacity: 1;
            }
        }

        /* --- 3. LOGIN PAGE --- */
        #login-page {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
            animation: fadeIn 0.5s ease;
        }

        .numpad-display {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 20px;
            padding: 15px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 320px;
            text-align: center;
        }

        #phone-display {
            width: 100%;
            border: none;
            background: transparent;
            font-size: 1.8rem;
            font-weight: bold;
            color: #333;
            text-align: center;
            letter-spacing: 2px;
            outline: none;
        }

        .numpad-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            width: 100%;
            max-width: 320px;
        }

        .num-btn {
            background: rgba(255, 255, 255, 0.4);
            border-radius: 50%;
            aspect-ratio: 1/1;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            transition: 0.1s;
        }

        .num-btn:active {
            transform: scale(0.9);
            background: rgba(255, 255, 255, 0.7);
        }

        .num-btn.enter {
            grid-column: span 3;
            border-radius: 50px;
            aspect-ratio: auto;
            padding: 15px;
            background: linear-gradient(90deg, #2ecc71, #27ae60);
            margin-top: 10px;
        }

        /* --- 4. SCAN PAGE --- */
        #scan-page {
            display: flex;
            flex-direction: column;
            height: 100%;
            position: relative;
        }

        .profile-card {
            margin: 10px 15px;
            padding: 10px;
            background: var(--glass-bg);
            border-radius: 20px;
            display: flex;
            align-items: center;
            gap: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        }

        #user-img-large {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            background: #ddd;
        }

        .btn-logout-mini {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: #eee;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-left: auto;
        }

        .camera-wrapper {
            flex-shrink: 0;
            margin: 0 15px;
            height: 32vh;
            background: black;
            border-radius: 20px;
            overflow: hidden;
            position: relative;
        }

        #video {
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: 0.8;
        }

        .status-pill {
            position: absolute;
            bottom: 15px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(0, 0, 0, 0.6);
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
        }

        .st-success {
            background: #2ecc71;
        }

        .st-error {
            background: #e74c3c;
        }

        /* --- 5. DATA TABLE & FOOTER --- */
        .stats-sheet {
            flex-grow: 1;
            background: white;
            margin-top: 10px;
            border-radius: 25px 25px 0 0;
            display: flex;
            flex-direction: column;
            padding-bottom: 90px;
            overflow: hidden;
        }

        .table-scroll {
            overflow-y: auto;
            padding: 0 10px;
            flex-grow: 1;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td,
        th {
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
        }

        .footer-action {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            background: white;
            padding: 15px 20px;
            box-shadow: 0 -5px 20px rgba(0, 0, 0, 0.1);
        }

        .btn-finish {
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 50px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 30px;
        }

        .row-updated {
            animation: highlight 1s ease-out;
        }

        @keyframes highlight {
            0% {
                background: #d4edda;
            }

            100% {
                background: transparent;
            }
        }
    </style>
</head>

<body>

    <div id="screensaver" class="hidden" onclick="wakeUp()">
        <div class="saver-content">
            <div style="font-size: 5rem;">♻️</div>
            <p>แตะหน้าจอเพื่อใช้งาน</p>
        </div>
    </div>

    <div id="login-page">
        <div style="text-align:center; color:white; margin-bottom:20px;">
            <div style="font-size: 3.5rem;">♻️</div>
            <h1 style="margin:0; font-family:'Varela Round'; text-shadow:0 2px 4px rgba(0,0,0,0.2);">Smart Recycle</h1>
        </div>

        <div class="numpad-display">
            <input type="text" id="phone-display" value="0993392334" readonly placeholder="เบอร์โทรศัพท์">
        </div>

        <div class="numpad-grid">
            <div class="num-btn" onclick="addNum('1')">1</div>
            <div class="num-btn" onclick="addNum('2')">2</div>
            <div class="num-btn" onclick="addNum('3')">3</div>
            <div class="num-btn" onclick="addNum('4')">4</div>
            <div class="num-btn" onclick="addNum('5')">5</div>
            <div class="num-btn" onclick="addNum('6')">6</div>
            <div class="num-btn" onclick="addNum('7')">7</div>
            <div class="num-btn" onclick="addNum('8')">8</div>
            <div class="num-btn" onclick="addNum('9')">9</div>
            <div class="num-btn" style="background:rgba(255,100,100,0.5)" onclick="delNum()">⌫</div>
            <div class="num-btn" onclick="addNum('0')">0</div>
            <div class="num-btn enter" onclick="doLogin()">🚀 เข้าสู่ระบบ</div>
        </div>
    </div>

    <div id="scan-page" class="hidden">

        <div class="profile-card">
            <img id="user-img-large" src="" onerror="this.src='https://cdn-icons-png.flaticon.com/512/847/847969.png'">
            <div>
                <h3 class="result" style="margin:0;">Guest</h3>
                <div style="font-size:0.8rem; color:#666;">สะสมเดิม: <span id="user-score-db">0</span></div>
            </div>
            <div class="btn-logout-mini" onclick="logout()">↩</div>
        </div>

        <div class="camera-wrapper">
            <video id="video" autoplay playsinline muted></video>
            <div id="status-pill" class="status-pill">📷 พร้อมสแกน...</div>
        </div>

        <div class="stats-sheet">
            <div style="padding:15px; border-bottom:1px solid #eee; display:flex; justify-content:space-between;">
                <b>📦 รายการครั้งนี้</b>
                <span id="show-ip" style="font-size:0.7rem; color:#aaa;">Connecting...</span>
            </div>
            <div class="table-scroll">
                <table id="stats-table">
                    <tbody id="stats-body"></tbody>
                </table>
                <div id="empty-state" style="text-align:center; padding:30px; color:#ccc;">
                    <span style="font-size:2rem; display:block;">🗑️</span> เริ่มหยอดได้เลย
                </div>
            </div>
        </div>

        <div class="footer-action">
            <button class="btn-finish" onclick="finishSession()">
                <span>✅ ยืนยัน & บันทึก</span>
                <span style="background:rgba(255,255,255,0.2); padding:2px 10px; border-radius:15px; font-size:0.9rem;">
                    รวม <span id="session-total">0</span> แต้ม
                </span>
            </button>
        </div>
    </div>

    <video id="webcamVideo" autoplay playsinline width="640" height="480" style="display:none;"></video>
    <canvas id="captureCanvas" width="640" height="480" style="display:none;"></canvas>

    <script>
        // --- CONFIGURATION ---
        const NODE_MCU_IP = "http://10.255.156.96";
        const TM_URL = "https://teachablemachine.withgoogle.com/models/n_4Cu6X1N/";
        const IDLE_TIMEOUT_SEC = 10; // เวลา Deep Sleep (วินาที)

        // --- VARIABLES ---
        let model, isModelLoaded = false;
        let isProcessing = false;
        let isDeepSleep = false;
        let disConnectCount = 0;

        let sessionData = {};
        let sessionTotalScore = 0;
        let currentUserPhone = "";

        // Timer Variables
        let idleTime = 0;
        let idleInterval;
        // --- KIOSK ID SETUP ---
        let kioskId = localStorage.getItem('KIOSK_ID'); // 1. ลองดึงค่าเดิมมาก่อน
        if (!kioskId) {
            kioskId = "999"; // ค่า Default ถ้ายังไม่ได้ตั้ง
            console.log("⚠️ ยังไม่ได้ตั้งค่า Kiosk ID ใช้ค่า Default: 999");
        }

        // สร้างตัวแปรนับจำนวนการแตะ
        let secretTapCount = 0;

        // สมมติว่าโลโก้มี ID หรือ Class เช่น .logo-area (คุณอาจต้องไปเพิ่ม class นี้ที่ div โลโก้)
        // หรือใช้ event click ที่หัวข้อก็ได้
        $('h1').on('click', function () {
            secretTapCount++;
            console.log('secretTapCount',secretTapCount)
            if (secretTapCount >= 5) {
                // เมื่อแตะครบ 5 ครั้ง ให้เด้งถาม
                let newId = prompt("🔧 [ADMIN MODE]\nกรุณาระบุ ID ของตู้นี้:", kioskId);
                if (newId) {
                    localStorage.setItem('KIOSK_ID', newId); // บันทึกลงเครื่องถาวร
                    kioskId = newId;
                    alert(`✅ บันทึก Kiosk ID: ${kioskId} เรียบร้อยแล้ว`);
                }
                secretTapCount = 0; // รีเซ็ตตัวนับ
            }
        });

        // =========================================
        // 1. INIT & DEEP SLEEP LOGIC
        // =========================================
        $(document).ready(function () {
            startIdleTimer();
            $(document).on('mousemove keypress click touchstart', function () {
                resetIdleTimer();
            });
        });

        function startIdleTimer() {
            if (idleInterval) clearInterval(idleInterval);
            idleInterval = setInterval(timerIncrement, 1000);
        }

        function timerIncrement() {
            // แก้ไข: ให้นับเวลาตลอด ตราบใดที่ยังไม่หลับ (ไม่สนว่าอยู่หน้าไหน)
            if (!isDeepSleep) {
                idleTime++;
                if (idleTime >= IDLE_TIMEOUT_SEC) {
                    goDeepSleep();
                }
            }
        }

        function goDeepSleep() {
            console.log("💤 Entering Deep Sleep Mode...");
            isDeepSleep = true;
            $('#screensaver').removeClass('hidden'); // แสดงจอดำ

            // ถ้าเปิดกล้องอยู่ ให้ปิดด้วย
            stopCamera();

            isProcessing = true; // หยุด AI Loop
        }

        async function wakeUp() {
            if (!isDeepSleep) return;

            // แก้ไข: เช็คก่อนว่า "ตื่นมาที่หน้าไหน?"
            const isScanPage = !$('#scan-page').hasClass('hidden');

            // Update UI
            $('#screensaver .saver-content').html('<div style="font-size:3rem;">⏳</div><p>กำลังเริ่มระบบ...</p>');

            // ถ้าตื่นที่หน้า Scan ให้เปิดกล้อง (ถ้าหน้า Login ไม่ต้องเปิด)
            if (isScanPage) {
                try {
                    await initCamera();
                } catch (e) { console.error("Camera Fail", e); }
            }

            // ซ่อนจอดำ + คืนค่าข้อความเดิม
            $('#screensaver').addClass('hidden');
            setTimeout(() => {
                $('#screensaver .saver-content').html('<div style="font-size: 5rem;">♻️</div><p>แตะหน้าจอเพื่อใช้งาน</p>');
            }, 500);

            // Reset ระบบ
            isDeepSleep = false;
            idleTime = 0;
            isProcessing = false;

            // ถ้าตื่นที่หน้า Scan ให้เริ่มจับ Sensor ต่อ
            if (isScanPage) {
                pollSensor();
            }

            console.log("☀️ System Woke Up!");
        }

        function resetIdleTimer() {
            idleTime = 0;
            if (isDeepSleep) wakeUp();
        }

        // =========================================
        // 2. CAM & AI
        // =========================================
        async function initModel() {
            if (isModelLoaded) return;
            try {
                const modelURL = TM_URL + "model.json";
                const metadataURL = TM_URL + "metadata.json";
                model = await tmImage.load(modelURL, metadataURL);
                isModelLoaded = true;
                console.log("AI Model Loaded");
            } catch (e) { alert("โหลด AI ไม่สำเร็จ"); }
        }

        async function initCamera() {
            const stream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: "environment", width: { ideal: 640 }, height: { ideal: 480 } }
            });
            document.getElementById('webcamVideo').srcObject = stream;
            document.getElementById('video').srcObject = stream;
            await document.getElementById('webcamVideo').play();
        }

        function stopCamera() {
            const video = document.getElementById('webcamVideo');
            if (video.srcObject) {
                const tracks = video.srcObject.getTracks();
                tracks.forEach(track => track.stop());
                video.srcObject = null;
            }
        }

        // =========================================
        // 3. MAIN LOGIC (SENSOR & PROCESS)
        // =========================================
        function pollSensor() {
            if ($('#scan-page').hasClass('hidden') || isProcessing || isDeepSleep) return;

            $.ajax({
                url: NODE_MCU_IP + "/check-sensor",
                type: "GET",
                timeout: 5000,
                success: function (res) {
                    disConnectCount = 0;
                    if (isProcessing) return;

                    if (res && res.includes("YES")) {
                        processObject();
                    } else {
                        setTimeout(pollSensor, 500);
                    }
                },
                error: function () {
                    disConnectCount++;
                    console.log(`Connection Failed (${disConnectCount}/2)`);

                    if (disConnectCount >= 2) {
                        logout("❌ ขาดการเชื่อมต่ออุปกรณ์เกินกำหนด");
                        return;
                    }

                    if (!isProcessing && !isDeepSleep) setTimeout(pollSensor, 2000);
                }
            });
        }

        async function processObject() {
            if (!isModelLoaded || isProcessing) return;
            isProcessing = true;

            updateStatus("⚡ กำลังวิเคราะห์...", "process");

            try {
                await new Promise(r => setTimeout(r, 800));

                const video = document.getElementById('webcamVideo');
                const canvas = document.getElementById('captureCanvas');
                const ctx = canvas.getContext('2d');
                canvas.width = video.videoWidth; canvas.height = video.videoHeight;
                ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

                const prediction = await model.predict(canvas);
                let best = prediction.reduce((p, c) => (p.probability > c.probability) ? p : c);

                if (best.probability > 0.85) {
                    const label = best.className;
                    const points = 10;

                    updateSessionTable(label, points);
                    updateStatus(`✨ พบ: ${label}`, "success");

                    if (label === "btmc_PET600") openGate();
                    else setTimeout(finishProcess, 1500);

                } else {
                    updateStatus("❌ ไม่รู้จักวัตถุ", "error");
                    setTimeout(finishProcess, 1500);
                }

            } catch (err) {
                console.error(err);
                isProcessing = false;
            }
        }

        // =========================================
        // 4. DATA HANDLING & FINISH
        // =========================================
        function updateSessionTable(label, points) {
            if (!sessionData[label]) sessionData[label] = { count: 0, score: 0 };
            sessionData[label].count++;
            sessionData[label].score += points;
            sessionTotalScore += points;

            $('#session-total').text(sessionTotalScore);
            $('#empty-state').hide();

            const rowId = 'row-' + label.replace(/\s+/g, '-');

            if ($('#' + rowId).length) {
                $(`#qty-${rowId}`).text(sessionData[label].count + " ชิ้น");
                $(`#score-${rowId}`).text("+" + sessionData[label].score);

                $('#' + rowId).removeClass('row-updated');
                void document.getElementById(rowId).offsetWidth;
                $('#' + rowId).addClass('row-updated');
            } else {
                const html = `
                    <tr id="${rowId}" class="row-updated">
                        <td>
                            <div style="font-weight:bold; color:#333;">${label}</div>
                            <div style="font-size:0.75rem; color:#888;">Recyclable</div>
                        </td>
                        <td align="center"><span id="qty-${rowId}" style="background:#e3f2fd; color:#1976d2; padding:2px 8px; border-radius:10px;">1 ชิ้น</span></td>
                        <td align="right" style="color:#2ecc71; font-weight:bold;"><span id="score-${rowId}">+${points}</span></td>
                    </tr>`;
                $('#stats-body').prepend(html);
            }
        }

        function finishSession() {
            if (Object.keys(sessionData).length === 0) {
                alert("ยังไม่มีรายการขยะ"); return;
            }
            if (!confirm("ยืนยันและบันทึกข้อมูล?")) return;

            $('.btn-finish').prop('disabled', true).html('⏳ กำลังบันทึก...');
            isProcessing = true;

            $.post("/api/save-session", {
                _token: $('meta[name="csrf-token"]').attr('content'),
                phone: currentUserPhone,
                items: sessionData,
                kiosk_id: kioskId, // <--- ✅ เพิ่มบรรทัดนี้ส่งไปด้วย
                total_score: sessionTotalScore
            }, function () {
                alert(`✅ บันทึกสำเร็จ! รับ ${sessionTotalScore} แต้ม`);
                location.reload();
            }).fail(function () {
                alert("❌ บันทึกไม่สำเร็จ");
                $('.btn-finish').prop('disabled', false).html('✅ ยืนยัน & บันทึก');
                isProcessing = false;
            });
        }

        // =========================================
        // 5. HARDWARE & UTILS
        // =========================================
        function openGate() {
            updateStatus("🚪 กำลังเปิดประตู...", "process");
            $.ajax({
                url: NODE_MCU_IP + "/open-gate",
                success: () => setTimeout(finishProcess, 4000),
                error: () => setTimeout(finishProcess, 1000)
            });
        }

        function finishProcess() {
            isProcessing = false;
            updateStatus("📷 พร้อมสแกน...", "wait");
            pollSensor();
        }

        function updateStatus(msg, type) {
            const el = $('#status-pill');
            el.text(msg).css('background', type === 'success' ? '#2ecc71' : type === 'error' ? '#e74c3c' : 'rgba(0,0,0,0.6)');
        }

        // =========================================
        // 6. LOGIN & LOGOUT
        // =========================================
        function addNum(n) { $('#phone-display').val($('#phone-display').val() + n); resetIdleTimer(); }
        function delNum() { let v = $('#phone-display').val(); $('#phone-display').val(v.slice(0, -1)); resetIdleTimer(); }

        function doLogin() {
            let phone = $('#phone-display').val();
            if (phone.length < 9) return alert('เบอร์ไม่ครบ');
            currentUserPhone = phone;

            $('.enter').text("⏳ Loading...");

            $.post("/api/check-member", { phone: phone, _token: $('meta[name="csrf-token"]').attr('content') },
                function (data) {
                    if (data.status === 'found') setupUI(data.name, data.picture, data.score);
                    else { alert("ไม่พบสมาชิก"); $('.enter').text("🚀 เข้าสู่ระบบ"); }
                }
            ).fail(() => { alert("Connection Error"); $('.enter').text("🚀 เข้าสู่ระบบ"); });
        }

        function setupUI(name, pic, score) {
            $('.result').text(name);
            $('#user-score-db').text(score);
            if (pic) $('#user-img-large').attr('src', pic);

            $('#login-page').addClass('hidden');
            $('#scan-page').removeClass('hidden');
            // $('#show-ip').text(NODE_MCU_IP.replace("http://", ""));

            initModel();
            initCamera();
            setTimeout(pollSensor, 1000);
        }

        function logout(msg = "") {
            stopCamera();
            if (msg) alert(msg);

            sessionData = {}; sessionTotalScore = 0; currentUserPhone = "";
            $('#stats-body').empty(); $('#empty-state').show(); $('#phone-display').val('');

            $('#scan-page').addClass('hidden');
            $('#login-page').removeClass('hidden');

            isProcessing = false; disConnectCount = 0; isDeepSleep = false;
            $('.enter').text("🚀 เข้าสู่ระบบ");
        }
    </script>
</body>

</html>