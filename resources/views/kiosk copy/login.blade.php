<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Kiosk Login</title>
    <script src="https://unpkg.com/html5-qrcode"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #f68d9d; /* สีชมพูพีชจากรูปที่ 3 */
            --bg-color: #fdf2f4;
            --neu-shadow: 8px 8px 16px #e8dbde, -8px -8px 16px #ffffff;
            --neu-inset: inset 5px 5px 10px #e8dbde, inset -5px -5px 10px #ffffff;
        }

        body {
            background-color: var(--bg-color);
            font-family: 'Kanit', sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            overflow: hidden;
        }

        /* พื้นหลังลายเส้นโค้งตาม Reference 3 */
        .bg-topography {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 40%;
            background: var(--primary-color);
            border-bottom-left-radius: 100% 20%;
            border-bottom-right-radius: 100% 20%;
            z-index: -1;
            opacity: 0.9;
        }

        .login-card {
            width: 90%;
            max-width: 380px;
            background: var(--bg-color);
            border-radius: 40px;
            padding: 30px;
            box-shadow: var(--neu-shadow);
            text-align: center;
        }

        .welcome-text {
            color: #444;
            font-weight: 600;
            margin-bottom: 5px;
        }

        /* กรอบ QR Reader ตาม Reference 1 */
        .qr-window-wrapper {
            width: 220px;
            height: 220px;
            margin: 30px auto;
            background: white;
            border-radius: 35px;
            padding: 15px;
            box-shadow: var(--neu-inset);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        #reader {
            width: 100% !important;
            height: 100% !important;
            border: none !important;
            border-radius: 25px;
            overflow: hidden;
        }

        /* ปุ่มสไตล์ Neumorphism ตาม Reference 2 */
        .btn-neu {
            background: var(--bg-color);
            border: none;
            border-radius: 20px;
            padding: 15px 30px;
            color: var(--primary-color);
            font-weight: bold;
            box-shadow: var(--neu-shadow);
            transition: all 0.2s;
            width: 100%;
            margin-top: 20px;
        }

        .btn-neu:active {
            box-shadow: var(--neu-inset);
            transform: scale(0.98);
        }

        .input-neu {
            background: var(--bg-color);
            border: none;
            border-radius: 50px;
            padding: 12px 20px;
            box-shadow: var(--neu-inset);
            width: 100%;
            text-align: center;
            color: #666;
            margin-bottom: 15px;
        }

        .status-msg {
            font-size: 0.85rem;
            color: #888;
            margin-top: 10px;
        }
    </style>
</head>
<body>

<div class="bg-topography"></div>

<div class="login-card">
    <div class="mb-2">
        <i class="bi bi-qr-code-scan" style="font-size: 2.5rem; color: white;"></i>
    </div>
    <h3 class="welcome-text">ยินดีต้อนรับ</h3>
    <p style="color: #888; font-size: 0.9rem;">สแกน QR Code ที่หน้าตู้ Kiosk เพื่อเข้าใช้งาน</p>

    <div class="qr-window-wrapper">
        <div id="reader"></div>
        <div id="placeholder-icon" style="position: absolute; pointer-events: none;">
            <i class="bi bi-camera" style="font-size: 3rem; color: #eee;"></i>
        </div>
    </div>

    <input type="text" id="kiosk-id-display" class="input-neu" placeholder="รหัสตู้ Kiosk" readonly>

    <button class="btn-neu" onclick="startScanner()">
        <i class="bi bi-upc-scan me-2"></i> เปิดกล้องสแกน
    </button>

    <div class="status-msg" id="status-text">รอการสแกน...</div>
</div>

<script>
    let html5QrCode;

    function startScanner() {
        document.getElementById('placeholder-icon').style.opacity = '0';
        document.getElementById('status-text').innerText = "กำลังเข้าถึงกล้อง...";

        html5QrCode = new Html5Qrcode("reader");
        const config = { fps: 10, qrbox: { width: 180, height: 180 } };

        html5QrCode.start(
            { facingMode: "environment" },
            config,
            (decodedText) => {
                // ✅ สแกนสำเร็จ
                handleSuccess(decodedText);
            },
            (errorMessage) => {
                // กำลังค้นหา QR...
            }
        ).catch((err) => {
            document.getElementById('status-text').innerText = "กล้องมีปัญหา: " + err;
        });
    }

    function handleSuccess(kioskId) {
        // หยุดสแกน
        html5QrCode.stop();

        // ใส่ค่าใน Input
        document.getElementById('kiosk-id-display').value = kioskId;
        document.getElementById('status-text').innerText = "เชื่อมต่อตู้สำเร็จ!";
        document.getElementById('status-text').style.color = "#2ecc71";

        // จำลองการเปลี่ยนหน้า (ไปยังหน้าหลักของระบบ)
        setTimeout(() => {
            alert("เข้าสู่ระบบตู้: " + kioskId + "\nUser ID: US-7890");
            // window.location.href = "/main-system"; // ไปหน้าถัดไป
        }, 1000);
    }
</script>

</body>
</html>
