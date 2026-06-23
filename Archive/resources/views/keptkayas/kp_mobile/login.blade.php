@extends('layouts.keptkaya_mobile') 

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-12 col-md-6">
            <div class="card shadow-lg mt-5">
                <div class="card-header text-center h4 bg-primary text-white">{{ __('เข้าสู่ระบบทำรายการ') }}</div>

                <div class="card-body">
                    
                    {{-- 🎯 โหมด A: ยังไม่มี Machine ID (ให้สแกนก่อน) --}}
                    @if (!isset($machineId))
                        <div id="scan-mode" class="p-3">
                            <h5 class="text-secondary text-center mb-4">ขั้นตอนที่ 1: สแกน QR Code ตู้ (ESP ID)</h5>
                            <div class="text-center">
                                <button id="start-scan-button" class="btn btn-success mb-3" onclick="startScanner()">
                                    <i class="bi bi-qr-code me-2"></i> คลิกเพื่อเริ่มสแกน
                                </button>
                                
                                {{-- Div สำหรับแสดงผลกล้อง --}}
                                <div id="scanner-reader" style="width: 100%; max-width: 300px; margin: 0 auto; display: none;"></div>
                                
                                <p class="mt-3 fw-bold" id="scanner-status" style="color: #dc3545;">กล้องปิดอยู่</p>
                            </div>
                        </div>
                    @endif

                    {{-- 🎯 โหมด B: มี Machine ID แล้ว (ให้ล็อกอิน) --}}
                    <div id="login-mode" style="display: {{ isset($machineId) ? 'block' : 'none' }};">
                        
                        <h5 class="text-secondary text-center">ขั้นตอนที่ 2: เลือกวิธีการเข้าสู่ระบบ</h5>

                        {{-- ข้อความสถานะ ESP ID --}}
                        <div class="text-center fw-bold mb-4">
                            กำลังทำรายการที่เครื่อง: <span class="text-primary" id="current-esp-id">{{ $machineId ?? 'N/A' }}</span>
                        </div>

                        {{-- Tab สำหรับเลือกวิธีการ Login --}}
                        <ul class="nav nav-tabs nav-justified" id="loginTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="phone-tab" data-bs-toggle="tab" data-bs-target="#phone-login" type="button" role="tab" aria-controls="phone-login" aria-selected="true">
                                    <i class="bi bi-phone me-2"></i> {{ __('เบอร์โทรศัพท์') }}
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="qrcode-tab" data-bs-toggle="tab" data-bs-target="#qrcode-login" type="button" role="tab" aria-controls="qrcode-login" aria-selected="false">
                                    <i class="bi bi-qr-code-scan me-2"></i> {{ __('สแกน User ID') }}
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content" id="loginTabContent">
                            
                            {{-- Phone Login Form --}}
                            <div class="tab-pane fade show active p-4" id="phone-login" role="tabpanel" aria-labelledby="phone-tab">
                                <form method="POST" action="{{ route('kpmobile_login') }}">
                                    @csrf
                                    {{-- 🎯 ช่อง Hidden: ส่ง Machine ID ไปกับฟอร์ม --}}
                                    <input type="hidden" name="machine_id" id="phone-machine-id" value="{{ $machineId ?? '' }}">
                                    
                                    <div class="mb-3">
                                        <label for="identifier" class="form-label">{{ __('เบอร์โทรศัพท์') }}</label>
                                        <input id="identifier" type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" value="0900000000" required autofocus placeholder="เช่น 08xxxxxxxx">
                                        {{-- ... (Hidden fields สำหรับการ Login ด้วยเบอร์โทรศัพท์) ... --}}
                                    </div>
                                    <input type="hidden" name="kp_mobile_login" value="1">
                                    <div class="d-grid gap-2 mt-4">
                                        <button type="submit" class="btn btn-primary btn-lg">{{ __('เข้าสู่ระบบ') }}</button>
                                    </div>
                                </form>
                            </div>

                            {{-- QR Code User ID Login Form --}}
                            <div class="tab-pane fade p-4 text-center" id="qrcode-login" role="tabpanel" aria-labelledby="qrcode-tab">
                                <form method="POST" action="{{ route('login') }}" class="mt-4">
                                    @csrf
                                    {{-- 🎯 ช่อง Hidden: ส่ง Machine ID ไปกับฟอร์ม --}}
                                    <input type="hidden" name="machine_id" id="qr-machine-id" value="{{ $machineId ?? '' }}">
                                    
                                    <p class="text-muted">ใช้กล้องสแกน QR Code User ID ของท่านเพื่อเข้าสู่ระบบ</p>
                                    {{-- ... (เพิ่ม div สำหรับ QR Code User Scanner ที่นี่) ... --}}
                                    <div class="d-grid gap-2 mt-4">
                                        <button type="submit" class="btn btn-success btn-lg">ยืนยันการเข้าสู่ระบบ</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
{{-- ต้องมีไลบรารี html5-qrcode --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js"></script>


<script>
    const machineScanner = new Html5Qrcode("scanner-reader");
    const scannerStatus = document.getElementById('scanner-status');
    const scannerDiv = document.getElementById('scanner-reader');
    const startButton = document.getElementById('start-scan-button');

    function startScanner() {
        // ซ่อนปุ่มและแสดงช่องสำหรับกล้อง
        startButton.style.display = 'none';
        scannerDiv.style.display = 'block';

        const config = { 
            fps: 10, 
            qrbox: { width: 250, height: 250 } 
        };

        scannerStatus.textContent = 'กำลังเปิดกล้องและรอสแกน...';
        scannerStatus.style.color = '#ffc107'; // สีเหลือง

        machineScanner.start(
            // ใช้ facingMode: "environment" เพื่อเลือกกล้องหลัง (Back Camera)
            { facingMode: "environment" }, 
            config,
            (decodedText, decodedResult) => {
                // *** เมื่อสแกน Machine ID สำเร็จ ***
                const machineId = decodedText;
                scannerStatus.textContent = '✅ สแกนสำเร็จ! กำลังบันทึกสถานะ...';
                
                // 1. หยุดกล้อง
                if (machineScanner.isScanning) {
                    machineScanner.stop().catch(console.error);
                }

                // 2. ส่ง Machine ID ไปยัง API เพื่อบันทึกสถานะเป็น pending
                fetch('{{ route('api.machine.bind') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ machine_id: machineId })
                })
                .then(response => response.json())
                .then(data => {
                    if(data.machine_id) {
                        // 3. 🎯 Redirect ตัวเองไปที่ URL เดิมพร้อมเพิ่ม Query String
                        // เพื่อให้ Controller โหลดหน้าใหม่ในโหมด Login
                        window.location.href = `{{ route('keptkayas.kp_mobile.login') }}?machine_id=${data.machine_id}`;
                        
                    } else {
                        scannerStatus.textContent = '❌ ข้อผิดพลาดในการบันทึกสถานะเครื่อง';
                        scannerStatus.style.color = '#dc3545';
                    }
                })
                .catch(error => {
                    scannerStatus.textContent = '❌ ข้อผิดพลาดในการติดต่อ Server';
                    scannerStatus.style.color = '#dc3545';
                    console.error('Error:', error);
                    // เปิดปุ่มสแกนใหม่อีกครั้งถ้าเกิด Error
                    startButton.style.display = 'block';
                    scannerDiv.style.display = 'none';
                });
            },
            (errorMessage) => {
                // Error parsing (มักจะเกิดขึ้นบ่อย ๆ ไม่ต้องแสดง)
            }
        ).catch((err) => {
             scannerStatus.textContent = '❌ ไม่สามารถเข้าถึงกล้องได้';
             scannerStatus.style.color = '#dc3545';
             startButton.style.display = 'block';
             scannerDiv.style.display = 'none';
             console.error('Camera access error:', err);
        });
    }

    // หยุดกล้องเมื่อผู้ใช้ออกจากหน้า
    window.addEventListener('beforeunload', function() {
        if (machineScanner && machineScanner.isScanning) {
            machineScanner.stop().catch(console.error);
        }
    });

    // ถ้ามี machineId อยู่แล้ว เราไม่ทำอะไร เพราะโหมด Login จะถูกแสดงอยู่แล้ว
    // แต่ถ้าคุณมี User ID Scanner ใน Tab 'qrcode-login' ก็สามารถเรียกใช้ที่นี่ได้
</script>
@endsection