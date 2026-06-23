<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kiosk AI Analyzer</title>
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@latest/dist/tf.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@teachablemachine/image@latest/dist/teachablemachine-image.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Kanit', sans-serif; text-align: center; background: #f0f2f5; padding: 10px; margin: 0; }
        .container { max-width: 450px; margin: 0 auto; background: white; padding: 20px; border-radius: 20px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); position: relative; }
        #monitor-image { width: 100%; border-radius: 15px; border: 2px solid #ddd; margin: 10px 0; min-height: 200px; background: #eee; object-fit: cover; }
        .log-box { background: #212529; color: #00ff00; padding: 10px; text-align: left; height: 120px; overflow-y: auto; font-family: monospace; font-size: 0.8em; margin-top: 10px; border-radius: 10px; display: none; }
        .status-badge { padding: 8px; border-radius: 8px; background: #fff3cd; margin-bottom: 10px; font-size: 0.9em; }
        .result-box { background: #e8f5e9; padding: 15px; border-radius: 15px; border-left: 8px solid #2e7d32; display: none; margin-bottom: 10px; }
        .btn-start { background: #28a745; color: white; border: none; padding: 12px 30px; font-size: 1.1em; border-radius: 50px; cursor: pointer; }

        /* --- FAB & Inventory UI --- */
        .fab {
            position: fixed; bottom: 25px; right: 25px;
            background: #007bff; color: white; width: 65px; height: 65px;
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3); cursor: pointer; z-index: 1000;
            font-size: 26px; transition: 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        .fab:active { transform: scale(0.9); }

        #total-badge {
            position: absolute; top: 0; right: 0; background: #ff3b30;
            color: white; border-radius: 50%; width: 22px; height: 22px;
            font-size: 12px; font-weight: bold; display: flex; align-items: center; justify-content: center;
            border: 2px solid white;
        }

        .cart-panel {
            position: fixed; bottom: 100px; right: 25px;
            width: 320px; background: white; border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2); display: none;
            overflow: hidden; z-index: 999; animation: slideIn 0.3s ease-out;
        }
        @keyframes slideIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

        .cart-header { background: #007bff; color: white; padding: 12px; font-weight: 600; text-align: left; }
        .cart-table { width: 100%; border-collapse: collapse; }
        .cart-table th { background: #f8f9fa; padding: 8px; text-align: left; font-size: 12px; color: #666; }
        .cart-table td { padding: 12px 8px; border-bottom: 1px solid #f1f1f1; font-size: 14px; text-align: left; }
    </style>
</head>
<body>

    <div style="text-align: left; max-width: 450px; margin: 0 auto 10px auto;">
        <button onclick="toggleLog()" style="padding: 5px 10px; cursor: pointer; border-radius: 5px; border: 1px solid #ccc;">📋 Log</button>
        <div id="log-box" class="log-box">
            <div id="log-content">--- ระบบพร้อมทำงาน ---</div>
        </div>
    </div>

    <div class="container">
        <div id="start-screen">
            <h2 style="color: #2e7d32;">🤖 AI Recycle Kiosk</h2>
            <p>กรุณากดเริ่มเพื่อเชื่อมต่อกล้องและเซนเซอร์</p>
            <button class="btn-start" onclick="startSession()">🚀 เริ่มใช้งาน</button>
        </div>

        <div id="main-screen" style="display:none;">
            <div id="status-badge" class="status-badge">⏳ กำลังโหลด AI...</div>
            <img id="monitor-image" src="" crossorigin="anonymous" />

            <div id="result-box" class="result-box">
                <div id="label-result" style="font-size:1.5em; font-weight:bold;">-</div>
                <div id="conf-result" style="color: #666;">-</div>
            </div>
        </div>
    </div>

    <div id="cart-panel" class="cart-panel">
        <div class="cart-header">📦 รายการสะสมวันนี้</div>
        <div id="cart-empty" style="padding: 20px; color: #999;">ยังไม่มีการหย่อนวัตถุ...</div>
        <table class="cart-table" id="cart-table" style="display: none;">
            <thead>
                <tr><th>วัตถุ</th><th>จำนวน</th></tr>
            </thead>
            <tbody id="cart-items"></tbody>
        </table>
    </div>

    <div class="fab" onclick="toggleCart()">
        🛒
        <div id="total-badge">0</div>
    </div>

    <script src="{{ mix('js/app.js') }}"></script>

    <script>
        const AI_URL = "https://teachablemachine.withgoogle.com/models/n_4Cu6X1N/";
        const kioskID = new URLSearchParams(window.location.search).get('kiosk') || 'SLAVE_01';
        const API_BASE = "http://10.138.254.80:8081/api/kiosk";

        let model;
        let inventory = {}; // JSON เก็บข้อมูลสะสม

        function addLog(message, type = 'info') {
            const logContent = document.getElementById('log-content');
            if (!logContent) return;
            const now = new Date();
            const timeStr = now.toLocaleTimeString();
            let color = "#00ff00";
            if (type === 'error') color = "#ff4d4d";
            if (type === 'warn') color = "#ffcc00";
            if (type === 'process') color = "#00d4ff";

            const div = document.createElement('div');
            div.style.color = color;
            div.innerHTML = `[${timeStr}] > ${message}`;
            logContent.insertBefore(div, logContent.firstChild);
        }

        function toggleLog() {
            const box = document.getElementById('log-box');
            box.style.display = box.style.display === 'none' ? 'block' : 'none';
        }

        function toggleCart() {
            const panel = document.getElementById('cart-panel');
            panel.style.display = panel.style.display === 'none' ? 'block' : 'none';
        }

        async function startSession() {
            document.getElementById('start-screen').style.display = 'none';
            document.getElementById('main-screen').style.display = 'block';
            addLog("🧠 กำลังโหลด AI Model...", "process");

            try {
                model = await tmImage.load(AI_URL + "model.json", AI_URL + "metadata.json");
                addLog("✅ AI พร้อมใช้งาน!", "info");
                document.getElementById('status-badge').innerText = "✅ พร้อม! กรุณาหย่อนขวด";
                setupEcho();
            } catch (err) {
                addLog("❌ โหลด AI ไม่สำเร็จ: " + err, "error");
            }
        }

        function setupEcho() {
            addLog("📡 เชื่อมต่อ Server...", "info");
            window.Echo.channel('kiosk.' + kioskID)
                .listen('.image.sent', (e) => {
                    addLog("📸 ได้รับรูปภาพใหม่!", "process");
                    const img = document.getElementById('monitor-image');
                    img.src = e.image + "?t=" + new Date().getTime();
                    img.onload = () => predict();
                })
                .listen('.command.received', (e) => {
                    if (e.command === 'SENSOR_TRIGGERED') {
                        addLog("🚨 เซนเซอร์เจอวัตถุ!", "warn");
                    }
                });
        }

        async function predict() {
            addLog("🔍 กำลังวิเคราะห์วัตถุ...", "process");
            const img = document.getElementById('monitor-image');
            const prediction = await model.predict(img);

            let best = { className: "", probability: 0 };
            prediction.forEach(p => {
                if (p.probability > best.probability) best = p;
            });

            // แสดงผล AI
            document.getElementById('result-box').style.display = 'block';
            document.getElementById('label-result').innerText = best.className;
            document.getElementById('conf-result').innerText = `ความมั่นใจ: ${(best.probability * 100).toFixed(2)}%`;
            addLog(`🎯 พบ: ${best.className}`);

            // ✅ บันทึกข้อมูลลง Inventory (Data Management)
            if (best.probability > 0.75) { // มั่นใจ 75% ขึ้นไปค่อยนับ
                updateInventory(best.className);
            }

            // ส่งสัญญาณ Resume ให้เซนเซอร์
            addLog("📤 ส่งสัญญาณพร้อมรับรอบถัดไป", "info");
            await fetch(`${API_BASE}/ready?kiosk=${kioskID}`);
        }

        function updateInventory(label) {
            // อัปเดตข้อมูลใน JSON
            inventory[label] = (inventory[label] || 0) + 1;

            // อัปเดต UI Table
            const itemsBody = document.getElementById('cart-items');
            const emptyMsg = document.getElementById('cart-empty');
            const table = document.getElementById('cart-table');
            const totalBadge = document.getElementById('total-badge');

            itemsBody.innerHTML = "";
            let total = 0;

            for (let key in inventory) {
                total += inventory[key];
                const row = `<tr><td><strong>${key}</strong></td><td>${inventory[key]} ชิ้น</td></tr>`;
                itemsBody.innerHTML += row;
            }

            totalBadge.innerText = total;
            emptyMsg.style.display = "none";
            table.style.display = "table";

            // Effect สั่นที่ปุ่ม FAB ให้รู้ว่ามีของเข้า
            const fab = document.querySelector('.fab');
            fab.style.transform = "scale(1.2)";
            setTimeout(() => fab.style.transform = "scale(1)", 200);
        }
    </script>
</body>
</html>
