<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบ Kiosk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://rawgit.com/schmich/instascan-builds/master/instascan.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card shadow-lg">
                    <div class="card-header bg-primary text-white text-center">
                        <h4>เข้าสู่ระบบเพื่อเริ่มใช้งาน</h4>
                    </div>
                    <div class="card-body">
                        
                        <div class="d-flex justify-content-center mb-4">
                            <button id="toggle-input" class="btn btn-outline-primary active me-2">
                                📞 เบอร์โทรศัพท์
                            </button>
                            <button id="toggle-scan" class="btn btn-outline-primary">
                                📷 สแกน QR Code
                            </button>
                        </div>
                        
                        <div id="phone-input-area">
                            <form id="phone-login-form">
                                <div class="mb-3">
                                    <label for="phone_number" class="form-label">เบอร์โทรศัพท์ (10 หลัก)</label>
                                    <input type="tel" class="form-control form-control-lg" id="phone_number" required maxlength="10" pattern="[0-9]{10}">
                                    <div class="form-text">ใช้เพื่อบันทึกและรวบรวมคะแนน</div>
                                </div>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-success btn-lg">เข้าสู่ระบบ & เริ่มใช้งาน</button>
                                </div>
                            </form>
                        </div>

                        <div id="scanner-area" style="display: none;">
                            <p class="text-center text-muted">กรุณาแสดง QR Code ที่มีข้อมูล User ID ของท่านหน้ากล้อง</p>
                            <video id="preview" class="w-100 rounded border border-secondary" style="height: 200px;"></video>
                            <div id="scan-result" class="mt-2 text-center text-success fw-bold"></div>
                        </div>

                    </div>
                    <div id="status-message" class="card-footer text-center text-muted">
                        กรุณาเลือกวิธีการเข้าสู่ระบบ
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            const KIOSK_APP_URL = "{{ route('kiosk.app') }}"; 
            let scanner = null; 

            // ==================== A. TOGGLE LOGIC ====================
            
            // สลับไปโหมดกรอกเบอร์โทรศัพท์
            $('#toggle-input').on('click', function() {
                $('#phone-input-area').show();
                $('#scanner-area').hide();
                $(this).addClass('active');
                $('#toggle-scan').removeClass('active');
                if (scanner) {
                    scanner.stop(); // หยุดกล้องหากกำลังทำงาน
                }
            });

            // สลับไปโหมดสแกน QR Code
            $('#toggle-scan').on('click', function() {
                $('#phone-input-area').hide();
                $('#scanner-area').show();
                $(this).addClass('active');
                $('#toggle-input').removeClass('active');
                $('#status-message').text('กำลังเปิดกล้อง...');
                
                // เริ่ม Instascan
                if (!scanner) {
                    scanner = new Instascan.Scanner({ video: document.getElementById('preview'), scanPeriod: 5 });
                    scanner.addListener('scan', function (content) {
                        handleScanResult(content);
                    });
                }

                // ค้นหากล้อง (อาจใช้กล้องหลังถ้ามี)
                Instascan.Camera.getCameras().then(function (cameras) {
                    if (cameras.length > 0) {
                        // ใช้กล้องแรกที่พบ หรือเลือกกล้องที่เหมาะกับมือถือ
                        scanner.start(cameras[0]); 
                        $('#status-message').text('กล้องพร้อมใช้งาน สแกน QR Code ของท่าน');
                    } else {
                        $('#status-message').removeClass('text-muted').addClass('text-danger').text('ไม่พบกล้องในอุปกรณ์นี้');
                    }
                }).catch(function (e) {
                    console.error(e);
                    $('#status-message').removeClass('text-muted').addClass('text-danger').text('เกิดข้อผิดพลาดในการเปิดกล้อง');
                });
            });

            // ==================== B. LOGIN LOGIC ====================
            
            // 1. จัดการการ Login ด้วยเบอร์โทรศัพท์
            $('#phone-login-form').on('submit', function(e) {
                e.preventDefault();
                const phoneNumber = $('#phone_number').val();
                
                if (phoneNumber.length === 10) {
                    const userId = 'USER_' + phoneNumber; // กำหนด User ID จากเบอร์โทรศัพท์
                    processLogin(userId);
                } else {
                    alert('กรุณากรอกเบอร์โทรศัพท์ 10 หลักให้ถูกต้อง');
                }
            });

            // 2. จัดการผลลัพธ์การสแกน QR Code
            function handleScanResult(content) {
                // รูปแบบที่คาดหวัง: user_id-08x-xxxxxxx
                const regex = /^user_id-(\d{10})$/;
                const match = content.match(regex);
                
                if (match) {
                    scanner.stop(); // หยุดการสแกนทันที
                    const userId = 'USER_' + match[1]; // ใช้เบอร์โทรศัพท์เป็นส่วนหนึ่งของ ID
                    $('#scan-result').text(`✅ สแกนสำเร็จ: ${userId}`);
                    processLogin(userId);
                } else {
                    $('#scan-result').text('❌ รูปแบบ QR Code ไม่ถูกต้อง: ' + content);
                }
            }

            // 3. ฟังก์ชันหลักในการเข้าสู่ระบบและเปลี่ยนหน้า
            function processLogin(userId) {
                $('#status-message').removeClass('text-muted').addClass('text-success').text(`ยินดีต้อนรับ ${userId} กำลังเปลี่ยนหน้า...`);
                
                // **TODO:** บันทึก User ID นี้ไว้ใน Session ของ Laravel
                // (ในตัวอย่างนี้ เราจะส่งมันไปใน URL เพื่อความง่าย แต่ควรใช้ Session จริง)
                
                setTimeout(() => {
                    window.location.href = KIOSK_APP_URL + '?user_id=' + userId;
                }, 1000);
            }
        });
    </script>
</body>
</html>