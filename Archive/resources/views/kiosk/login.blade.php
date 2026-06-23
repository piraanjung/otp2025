<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Smart Kiosk Login</title>

    <script src="https://unpkg.com/html5-qrcode"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <style>
        /* ... (CSS เดิมของคุณ ใช้ได้แล้วครับ) ... */
        :root {
            --primary-color: #f68d9d;
            --bg-color: #fdf2f4;
            --neu-shadow: 8px 8px 16px #e8dbde, -8px -8px 16px #ffffff;
            --neu-inset: inset 5px 5px 10px #e8dbde, inset -5px -5px 10px #ffffff;
        }
        body { background-color: var(--bg-color); font-family: 'Kanit', sans-serif; height: 100vh; display: flex; align-items: center; justify-content: center; margin: 0; overflow: hidden; }
        .bg-topography { position: absolute; top: 0; left: 0; width: 100%; height: 40%; background: var(--primary-color); border-bottom-left-radius: 100% 20%; border-bottom-right-radius: 100% 20%; z-index: -1; opacity: 0.9; }
        .login-card { width: 90%; max-width: 380px; background: var(--bg-color); border-radius: 40px; padding: 30px; box-shadow: var(--neu-shadow); text-align: center; }
        .welcome-text { color: #444; font-weight: 600; margin-bottom: 5px; }
        .qr-window-wrapper { width: 220px; height: 220px; margin: 30px auto; background: white; border-radius: 35px; padding: 15px; box-shadow: var(--neu-inset); display: flex; align-items: center; justify-content: center; position: relative; }
        #reader { width: 100% !important; height: 100% !important; border: none !important; border-radius: 25px; overflow: hidden; }
        .btn-neu { background: var(--bg-color); border: none; border-radius: 20px; padding: 15px 30px; color: var(--primary-color); font-weight: bold; box-shadow: var(--neu-shadow); transition: all 0.2s; width: 100%; margin-top: 20px; }
        .btn-neu:active { box-shadow: var(--neu-inset); transform: scale(0.98); }
        .input-neu { background: var(--bg-color); border: none; border-radius: 50px; padding: 12px 20px; box-shadow: var(--neu-inset); width: 100%; text-align: center; color: #666; margin-bottom: 15px; }
        .status-msg { font-size: 0.85rem; color: #888; margin-top: 10px; }
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

    // ตั้งค่า CSRF Token สำหรับ jQuery Ajax ทุกครั้ง
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    function startScanner() {
        document.getElementById('placeholder-icon').style.opacity = '0';
        document.getElementById('status-text').innerText = "กำลังเข้าถึงกล้อง...";

        // ⚠️ ลบบรรทัดนี้ทิ้ง เพื่อให้สแกนจริง ไม่ใช่ hardcode
        handleSuccess("SLAVE01");

        html5QrCode = new Html5Qrcode("reader");
        const config = { fps: 10, qrbox: { width: 180, height: 180 } };

        html5QrCode.start(
            { facingMode: "environment" },
            config,
            (decodedText) => {
                handleSuccess(decodedText);
            },
            (errorMessage) => {
                // scanning...
            }
        ).catch((err) => {
            document.getElementById('status-text').innerText = "กล้องมีปัญหา: " + err;
        });
    }

    function handleSuccess(kioskId) {
        // หยุดกล้องเมื่อเจอ QR Code
        // if(html5QrCode) html5QrCode.stop();

        document.getElementById('kiosk-id-display').value = kioskId;
        document.getElementById('status-text').innerText = "กำลังเชื่อมต่อตู้...";
        document.getElementById('status-text').style.color = "#e67e22";

        // ส่งข้อมูลไปหา Server
        $.post('/keptkayas/kiosks/noscreen/userMatchKiosk', { kioskId: kioskId }, function(data) {
            console.log(data);

            if(data.status == 'success'){
                document.getElementById('status-text').innerText = "เชื่อมต่อสำเร็จ! กำลังเข้าสู่ระบบ...";
                document.getElementById('status-text').style.color = "#2ecc71";

                // รอ 1 วินาทีแล้วเปลี่ยนหน้า
                setTimeout(() => {
                    window.location.href = "/keptkayas/kiosks/noscreen/monitor"; // เปลี่ยนไปหน้าควบคุมตู้
                }, 1000);
            } else {
                document.getElementById('status-text').innerText = "ผิดพลาด: " + data.message;
                document.getElementById('status-text').style.color = "red";
                // alert(data.message);
            }
        }).fail(function(xhr) {
            document.getElementById('status-text').innerText = "Error: เชื่อมต่อ Server ไม่ได้";
            console.log(xhr.responseText);
        });
    }
</script>

</body>
</html>
