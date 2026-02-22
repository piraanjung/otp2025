<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>พิมพ์ใบเสร็จธนาคารขยะ</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">

    <script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>

    <style>
        :root {
            --primary-color: #4f46e5; /* Indigo */
            --bg-color: #f3f4f6;
        }

        body {
            font-family: 'Sarabun', sans-serif;
            background-color: var(--bg-color);
            padding-bottom: 100px; /* เว้นที่ให้ Footer ด้านล่าง */
        }

        /* App Bar ด้านบน */
        .app-bar {
            background: white;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        /* จำลองกระดาษใบเสร็จ */
        .receipt-container {
            max-width: 380px; /* ความกว้างมาตรฐานสำหรับดูตัวอย่าง */
            margin: 0 auto;
            background: white;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            border-radius: 8px;
            overflow: hidden;
            position: relative;
        }

        /* รอยปะกระดาษ */
        .receipt-dashed-line {
            border-bottom: 2px dashed #e5e7eb;
            margin: 15px 0;
        }

        /* Action Bar ด้านล่าง (Sticky Footer) */
        .bottom-action-bar {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background: white;
            padding: 15px;
            box-shadow: 0 -4px 10px rgba(0,0,0,0.05);
            z-index: 1050;
            display: flex;
            gap: 10px;
        }

        /* ปรับแต่งปุ่มให้ดูทันสมัยขึ้น */
        .btn-custom-primary {
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            padding: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.2s;
        }
        .btn-custom-primary:hover {
            background-color: #4338ca;
            color: white;
            transform: translateY(-2px);
        }
        .btn-custom-primary:disabled {
            background-color: #c7c7c7;
            transform: none;
        }

        .btn-custom-secondary {
            background-color: #f3f4f6;
            color: #4b5563;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            font-size: 0.8rem;
        }
        .btn-custom-secondary .material-icons-round {
            font-size: 24px;
            margin-bottom: 2px;
        }

        /* สถานะ */
        .status-badge {
            font-size: 0.85rem;
            border-radius: 6px;
            padding: 8px 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
    </style>
</head>

<body>

    <nav class="app-bar py-3 px-3 mb-4">
        <div class="container d-flex align-items-center">
            <a href="{{ route('keptkayas.purchase.select_user') }}" class="btn btn-light btn-sm rounded-circle me-3" style="width: 40px; height: 40px; display:flex; align-items:center; justify-content:center;">
                <span class="material-icons-round">arrow_back</span>
            </a>
            <h5 class="mb-0 fw-bold text-dark">สรุปรายการและพิมพ์</h5>
        </div>
    </nav>

    <div class="container">
        
        <div class="row justify-content-center mb-4">
            <div class="col-12 col-md-6">
                <div id="status-card" class="status-badge bg-light text-secondary border">
                    <span class="material-icons-round text-primary">info</span>
                    <div>
                        <small class="d-block text-uppercase fw-bold" style="font-size: 0.7rem;">สถานะการเชื่อมต่อ</small>
                        <span id="status-text">ยังไม่ได้เชื่อมต่อ</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-auto">
                <p class="text-center text-muted small mb-2">ตัวอย่างใบเสร็จ</p>
                
                <div id="receipt-card" class="receipt-container p-4">
                    
                    <div class="text-center mb-3">
                        @if(isset($orgInfos['org_logo_img']))
                            <img src="{{asset('logo/'.$orgInfos['org_logo_img'])}}" style="width: 60px; margin-bottom: 10px;">
                        @endif
                        <h6 class="fw-bold mb-1">{{$orgInfos['org_type_name']}}</h6>
                        <div class="text-muted small">{{$orgInfos['org_name']}}</div>
                    </div>

                    <div class="receipt-dashed-line"></div>

                    <div class="row g-1 small mb-3 text-secondary">
                        <div class="col-6">เลขที่: <span class="text-dark fw-bold">{{ $transaction->kp_u_trans_no }}</span></div>
                        <div class="col-6 text-end">วันที่: {{ $transaction->transaction_date->format('d/m/Y') }}</div>
                        <div class="col-12">จนท.: {{ $transaction->recorder->firstname ?? '-' }}</div>
                    </div>

                    <div class="bg-light p-2 rounded mb-3 border border-light">
                        <div class="small fw-bold text-dark">สมาชิก: {{ $transaction->userWastePreference->user->firstname }} {{ $transaction->userWastePreference->user->lastname }}</div>
                        <div class="small text-muted" style="font-size: 0.75rem;">
                             {{ $transaction->userWastePreference->user->address }} 
                             {{ $transaction->userWastePreference->user->user_zone->zone_name ?? '' }}
                             ต.{{ $transaction->userWastePreference->user->user_tambon->tambon_name }}
                        </div>
                    </div>

                    <table class="table table-borderless table-sm small mb-2">
                        <thead class="text-secondary border-bottom">
                            <tr>
                                <th class="ps-0">รายการ</th>
                                <th class="text-end pe-0">รวม (บาท)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($transaction->details as $detail)
                            <tr>
                                <td class="ps-0 py-2">
                                    <div class="fw-bold text-dark">{{ $detail->item->kp_itemsname ?? 'ขยะรีไซเคิล' }}</div>
                                    <div class="text-muted" style="font-size: 0.75rem;">
                                        {{ number_format($detail->amount_in_units, 2) }} {{ $detail->pricePoint->kp_units_info->unit_short_name ?? 'หน่วย' }} 
                                        x {{ number_format($detail->price_per_unit, 2) }}
                                    </div>
                                </td>
                                <td class="text-end pe-0 py-2 align-top">
                                    <div class="fw-bold">{{ number_format($detail->amount, 2) }}</div>
                                    <div class="text-warning small">+{{ number_format($detail->points) }} แต้ม</div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="receipt-dashed-line"></div>

                    <div class="d-flex justify-content-between align-items-end mb-2">
                        <span class="fw-bold">ยอดเงินสุทธิ</span>
                        <span class="h4 mb-0 fw-bold text-success">{{ number_format($transaction->total_amount, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center small text-warning bg-warning bg-opacity-10 p-2 rounded">
                        <span>คะแนนสะสมเพิ่ม</span>
                        <span class="fw-bold">+{{ number_format($transaction->total_points) }} คะแนน</span>
                    </div>

                    <div class="text-center mt-4 text-muted" style="font-size: 0.7rem;">
                        ประเภท: {{ $transaction->cash_back == 1 ? 'เงินสด' : 'เข้าบัญชีสะสม' }}
                        <br>ขอบคุณที่ร่วมรักษ์โลก
                    </div>
                </div>
                </div>
        </div>
    </div>

    <div class="bottom-action-bar">
        <button id="connectButton" class="btn btn-custom-secondary col-4">
            <span class="material-icons-round">bluetooth</span>
            <span>เชื่อมต่อ</span>
        </button>
        <button id="printImageButton" disabled onclick="printReceipt()" class="btn btn-custom-primary col-8 shadow-sm">
            <span class="material-icons-round">print</span>
            <span id="printBtnText">พิมพ์ใบเสร็จ</span>
        </button>
    </div>

    <canvas id="canvas" style="display: none;"></canvas>

    <script>
        // --- Configuration Constants ---
        const PRINTER_SERVICE_UUID = '000018f0-0000-1000-8000-00805f9b34fb';
        const PRINTER_CHARACTERISTIC_UUID = '00002af1-0000-1000-8000-00805f9b34fb';
        const LAST_USED_DEVICE_ID_KEY = 'lastUsedBluetoothDeviceId';
        
        // --- UI Elements ---
        const statusText = document.getElementById('status-text');
        const statusCard = document.getElementById('status-card');
        const connectButton = document.getElementById('connectButton');
        const printButton = document.getElementById('printImageButton');
        const printBtnText = document.getElementById('printBtnText');
        
        let bluetoothDevice;
        let printCharacteristic;

        // --- Helper: UI Updates ---
        function updateStatus(message, type = 'info') {
            statusText.textContent = message;
            
            // Reset Classes
            statusCard.className = 'status-badge border';
            const icon = statusCard.querySelector('.material-icons-round');
            
            if (type === 'success') {
                statusCard.classList.add('bg-success', 'bg-opacity-10', 'text-success', 'border-success');
                icon.textContent = 'check_circle';
                icon.classList.replace('text-primary', 'text-success');
                
                // Update Buttons
                connectButton.classList.add('text-success', 'border-success');
                connectButton.innerHTML = '<span class="material-icons-round">bluetooth_connected</span><span>เชื่อมต่อแล้ว</span>';
                
                printButton.disabled = false;
                
            } else if (type === 'error') {
                statusCard.classList.add('bg-danger', 'bg-opacity-10', 'text-danger', 'border-danger');
                icon.textContent = 'error';
                icon.classList.replace('text-primary', 'text-danger');
            } else {
                // Info / Loading
                statusCard.classList.add('bg-light', 'text-secondary');
                icon.textContent = 'info';
                icon.classList.replace('text-danger', 'text-primary');
                icon.classList.replace('text-success', 'text-primary');
            }
        }

        // --- Bluetooth Logic ---
        async function connectToPrinter() {
            updateStatus('กำลังค้นหาเครื่องพิมพ์...', 'info');

            if (!navigator.bluetooth) {
                updateStatus('Browser ไม่รองรับ Bluetooth (ใช้ Chrome Android)', 'error');
                return;
            }

            try {
                let selectedDevice = null;
                const lastDeviceId = localStorage.getItem(LAST_USED_DEVICE_ID_KEY);

                if (lastDeviceId) {
                    try {
                        const devices = await navigator.bluetooth.getDevices();
                        selectedDevice = devices.find(d => d.id === lastDeviceId);
                    } catch(e) {}
                }

                if (!selectedDevice) {
                    selectedDevice = await navigator.bluetooth.requestDevice({
                        filters: [{ services: [PRINTER_SERVICE_UUID] }],
                        optionalServices: []
                    });
                    localStorage.setItem(LAST_USED_DEVICE_ID_KEY, selectedDevice.id);
                }

                bluetoothDevice = selectedDevice;
                bluetoothDevice.addEventListener('gattserverdisconnected', onDisconnected);

                const server = await bluetoothDevice.gatt.connect();
                const service = await server.getPrimaryService(PRINTER_SERVICE_UUID);
                printCharacteristic = await service.getCharacteristic(PRINTER_CHARACTERISTIC_UUID);

                updateStatus(`เชื่อมต่อ ${bluetoothDevice.name} สำเร็จ`, 'success');

            } catch (error) {
                updateStatus(`เชื่อมต่อไม่สำเร็จ: ${error.message}`, 'error');
            }
        }

        function onDisconnected() {
            updateStatus('เครื่องพิมพ์หลุดการเชื่อมต่อ', 'error');
            printButton.disabled = true;
            connectButton.innerHTML = '<span class="material-icons-round">bluetooth</span><span>เชื่อมต่อ</span>';
            connectButton.classList.remove('text-success', 'border-success');
        }

        // --- Printing Logic ---
        function getMonochromeBitmapData(ctx, width, height) {
            const imageData = ctx.getImageData(0, 0, width, height);
            const data = imageData.data;
            const bitmap = new Uint8Array(Math.ceil(width / 8) * height);

            for (let y = 0; y < height; y++) {
                for (let x = 0; x < width; x++) {
                    const i = (y * width + x) * 4;
                    const avg = (data[i] + data[i+1] + data[i+2]) / 3;
                    if (avg < 128) { 
                        const byteIndex = y * Math.ceil(width / 8) + Math.floor(x / 8);
                        const bitIndex = 7 - (x % 8);
                        bitmap[byteIndex] |= (1 << bitIndex);
                    }
                }
            }
            return bitmap;
        }

        async function printReceipt() {
            if (!printCharacteristic) {
                updateStatus('กรุณาเชื่อมต่อก่อนพิมพ์', 'error');
                return;
            }

            const originalText = printBtnText.textContent;
            printButton.disabled = true;
            printBtnText.textContent = 'กำลังส่งข้อมูล...';

            try {
                // 1. HTML to Canvas
                const receiptElement = document.getElementById('receipt-card');
                const canvas = await html2canvas(receiptElement, {
                    scale: 2, 
                    useCORS: true,
                    backgroundColor: '#ffffff'
                });

                // 2. Resize
                const printerWidth = 384; 
                const scaleFactor = printerWidth / canvas.width;
                const printerHeight = canvas.height * scaleFactor;

                const printCanvas = document.createElement('canvas');
                printCanvas.width = printerWidth;
                printCanvas.height = printerHeight;
                const ctx = printCanvas.getContext('2d');
                ctx.drawImage(canvas, 0, 0, printerWidth, printerHeight);

                // 3. Bitmap & Command
                const bitmapData = getMonochromeBitmapData(ctx, printerWidth, printerHeight);
                const bytesPerRow = Math.ceil(printerWidth / 8);
                const command = new Uint8Array([
                    0x1D, 0x76, 0x30, 0x00,
                    bytesPerRow & 0xFF, (bytesPerRow >> 8) & 0xFF,
                    printerHeight & 0xFF, (printerHeight >> 8) & 0xFF
                ]);

                const dataToSend = new Uint8Array(command.length + bitmapData.length);
                dataToSend.set(command, 0);
                dataToSend.set(bitmapData, command.length);

                // 4. Send Chunks
                const CHUNK_SIZE = 256; 
                for (let i = 0; i < dataToSend.length; i += CHUNK_SIZE) {
                    const chunk = dataToSend.slice(i, i + CHUNK_SIZE);
                    await printCharacteristic.writeValueWithoutResponse(chunk);
                    await new Promise(r => setTimeout(r, 40));
                }

                // Feed Lines
                await printCharacteristic.writeValueWithoutResponse(new Uint8Array([0x0A, 0x0A, 0x0A]));
                updateStatus('พิมพ์เสร็จสิ้น', 'success');

            } catch (error) {
                updateStatus(`Error: ${error.message}`, 'error');
            } finally {
                printButton.disabled = false;
                printBtnText.textContent = originalText;
            }
        }

        // Initialize
        connectButton.addEventListener('click', connectToPrinter);
    </script>
</body>
</html>