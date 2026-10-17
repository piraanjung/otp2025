@extends('layouts.keptkaya_mobile')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <h2 class="text-center mb-4">Barcode Scanner & Search (AJAX)</h2>

            <div class="card shadow">
                <div class="card-body">
                    
                    <div id="reader" style="width: 100%; height: 300px;"></div>
                    
                    <p class="text-center mt-3 h5" id="scan-status">
                        Initializing camera...
                    </p>

                    <hr>
                    
                    <form id="manual-search-form" class="mb-3">
                        @csrf
                        <label for="barcodeInput" class="form-label">Barcode Number (Manual Entry)</label>
                        <div class="input-group">
                            <input type="text" id="barcodeInput" name="barcode_number" class="form-control" placeholder="Enter Barcode or Scan">
                            <button class="btn btn-primary" type="submit">Search</button>
                        </div>
                    </form>

                </div>
            </div>

            <div id="product-details" class="card mt-4" style="display: none;">
                <div class="card-header bg-info text-white">
                    Product Details
                </div>
                <div class="card-body">
                    </div>
            </div>
            
        </div>
    </div>
</div>
@endsection

@section('script') 
<script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js" integrity="sha512-r6rDA7W6ZeQhvl8S7yRVQUKVHdexq+GAlNkNNqVC7YyIV+NwqCTJe2hDWCiffTyRNOeGEzRRJ9ifvRm/HCzGYg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script>
    // ดึง CSRF Token จาก meta tag 
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    // ใช้ route() helper เพื่อให้แน่ใจว่า URL ถูกต้อง
    const SEARCH_URL = '{{ route("keptkayas.barcode.search") }}';

    document.addEventListener('DOMContentLoaded', function () {
        const html5Qrcode = new Html5Qrcode("reader");
        const scanStatus = document.getElementById('scan-status');
        const barcodeInput = document.getElementById('barcodeInput');
        const productDetails = document.getElementById('product-details');
        const productDetailsBody = productDetails.querySelector('.card-body');
        
        let lastScannedBarcode = null;

        const config = { 
            fps: 10, 
            qrbox: { width: 250, height: 150 },
            // กำหนดให้รองรับ Barcode หลายชนิด
            supportedScanTypes: [Html5QrcodeScanType.SCAN_TYPE_CAMERA],
            formatsToSupport: [Html5QrcodeSupportedFormats.CODE_128, Html5QrcodeSupportedFormats.EAN_13, Html5QrcodeSupportedFormats.QR_CODE]
        };
        
        // --- 1. AJAX Search Function (ใช้ Fetch API) ---
        const searchProduct = async (barcode) => {
            scanStatus.textContent = `Searching for: ${barcode}...`;
            productDetails.style.display = 'none';

            try {
                const response = await fetch(SEARCH_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN
                    },
                    body: JSON.stringify({ barcode_number: barcode })
                });

                const data = await response.json();

                if (response.ok) { 
                    const product = data.product;
                    scanStatus.className = 'text-center mt-3 h5 text-success';
                    scanStatus.textContent = `✅ Found product: ${product.product_name}`;
                    
                    productDetailsBody.innerHTML = `
                        <p><strong>Barcode:</strong> ${product.barcode_number}</p>
                        <p><strong>Name:</strong> ${product.product_name}</p>
                        <p><strong>Price:</strong> ${new Intl.NumberFormat('th-TH').format(product.price)} บาท</p>
                    `;
                    productDetails.className = 'card mt-4 border-success';
                    productDetails.style.display = 'block';

                } else { 
                    scanStatus.className = 'text-center mt-3 h5 text-danger';
                    scanStatus.textContent = `❌ ${data.message}`;
                    productDetails.style.display = 'none';
                }

            } catch (error) {
                console.error('Fetch error:', error);
                scanStatus.className = 'text-center mt-3 h5 text-danger';
                scanStatus.textContent = '❌ An error occurred during search.';
            }
        };

        // --- 2. Barcode Scan Callback ---
        const qrCodeSuccessCallback = (decodedText, decodedResult) => {
            
            // 2.1 ป้องกันการสแกนซ้ำซ้อน
            if (decodedText === lastScannedBarcode) return;
            lastScannedBarcode = decodedText;

            html5Qrcode.pause(true); // หยุดสแกนชั่วคราว
            
            barcodeInput.value = decodedText; 
            searchProduct(decodedText);       

            // 2.2 กลับมาสแกนต่อหลังจากหน่วงเวลา
            setTimeout(() => {
                lastScannedBarcode = null;
                html5Qrcode.resume(); 
                scanStatus.textContent = 'Ready to scan...';
            }, 3000); // 3 วินาที
        };

        // --- 3. Start Scanner (ปรับปรุง: ค้นหากล้องก่อนเริ่ม) ---
        Html5Qrcode.getCameras().then(devices => {
            if (devices && devices.length) {
                // เลือกกล้องที่เหมาะสม (ใช้กล้องหลังถ้ามี ไม่งั้นใช้กล้องแรก)
                // นี่เป็น ID ของกล้องที่พบ
                const cameraId = devices[0].id; 

                html5Qrcode.start(
                    cameraId, // ส่ง ID กล้องเข้าไป
                    config,
                    qrCodeSuccessCallback,
                    (errorMessage) => {
                        // console.log("Scan attempt error: ", errorMessage); // สามารถเปิดใช้งานเพื่อ Debug
                    }
                ).catch((err) => {
                     // Error ในการเริ่มกล้อง (เช่น สิทธิ์ถูกปฏิเสธ)
                     scanStatus.className = 'text-center mt-3 h5 text-danger';
                     scanStatus.textContent = '❌ Failed to start camera. Check permissions/HTTPS.';
                     console.error("Camera start failed:", err);
                });
            } else {
                scanStatus.className = 'text-center mt-3 h5 text-danger';
                scanStatus.textContent = '❌ No cameras found on this device.';
            }
        }).catch(err => {
            // Error ในการเข้าถึงอุปกรณ์กล้อง
            scanStatus.className = 'text-center mt-3 h5 text-danger';
            scanStatus.textContent = '❌ General error accessing cameras. Check console.';
            console.error("Get cameras error:", err);
        });

        // --- 4. Manual Search Listener ---
        document.getElementById('manual-search-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const manualBarcode = barcodeInput.value.trim();
            if (manualBarcode) {
                searchProduct(manualBarcode);
            }
        });
    });
</script>
@endsection