<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Web Bluetooth Thermal Printer</title>
    <!-- Tailwind CSS CDN for styling -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f0f4f8;
            /* display: flex; */
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 1rem;
        }

        .container {
            background-color: #ffffff;
            padding: 2rem;
            border-radius: 1rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            /* Increased max-width for better layout */
            width: 100%;
            text-align: center;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 0.75rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease-in-out;
            margin: 0.5rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background-color: #4f46e5;
            color: #ffffff;
            border: none;
        }

        .btn-primary:hover {
            background-color: #4338ca;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(79, 70, 229, 0.3);
        }

        .btn-disabled {
            background-color: #cbd5e1;
            color: #64748b;
            cursor: not-allowed;
        }

        .status-message {
            margin-top: 1.5rem;
            padding: 1rem;
            border-radius: 0.5rem;
            font-size: 0.9rem;
            color: #334155;
            background-color: #e2e8f0;
            text-align: left;
            word-wrap: break-word;
            /* Ensure long messages wrap */
        }

        .status-success {
            background-color: #d1fae5;
            color: #065f46;
        }

        .status-error {
            background-color: #fee2e2;
            color: #991b1b;
        }

        textarea {
            width: 100%;
            padding: 0.75rem;
            border-radius: 0.5rem;
            border: 1px solid #cbd5e1;
            margin-bottom: 1rem;
            font-family: 'Inter', sans-serif;
            font-size: 1rem;
            resize: vertical;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">เครื่องพิมพ์ความร้อน Web Bluetooth</h1>
        <p class="text-gray-600 mb-6">
            เชื่อมต่อกับเครื่องพิมพ์ความร้อน Bluetooth ของคุณและส่งข้อความเพื่อทดสอบ
        </p>

         <div class="flex flex-col sm:flex-row justify-center gap-4 mb-6">
            <button id="connectButton" class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z"
                        clip-rule="evenodd" />
                </svg>
                เชื่อมต่อเครื่องพิมพ์
            </button>
            <button id="printImageButton" class="btn btn-primary btn-disabled" disabled>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-4 3 3 5-5V5h-2v4.586l-3 3L9 9.414l-4 4V5h11v10z"
                        clip-rule="evenodd" />
                </svg>
                พิมพ์ข้อความ (เป็นรูปภาพ)
            </button>

        </div>
       <div id="status" class="status-message">
            สถานะ: ยังไม่ได้เชื่อมต่อ
        </div>

        <div class="card shadow-lg" style="width: 250px; font-size:0.8rem !important">
            <div class="card-header bg-success text-white text-center">
                <img src="{{asset('logo/hs_logo.jpg')}}" alt="">
                <h3 class="mb-0">ใบเสร็จรับเงิน</h3>
                <p class="mb-0">ธนาคารขยะ</p>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>เลขที่ธุรกรรม:</strong> {{ $transaction->kp_u_trans_no }}
                    </div>
                    <div class="col-md-6 text-md-end">
                        <strong>วันที่ทำรายการ:</strong> {{ $transaction->transaction_date->format('Y-m-d') }}
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <strong>ผู้ขาย:</strong> {{ $transaction->user_waste_pref->user->firstname }}
                        {{ $transaction->user_waste_pref->user->lastname }}
                    </div>
                    <div class="col-md-6 text-md-end">
                        <strong>ผู้บันทึก:</strong> {{ $transaction->recorder->firstname ?? 'N/A' }}
                        {{ $transaction->recorder->lastname ?? 'N/A' }}
                    </div>
                </div>

                <div class="table-responsive mb-4">
                    <table class="table table-bordered table-striped">
                        <thead class="table-light">
                            <tr>
                                <th>รายการขยะ</th>
                                <th>ปริมาณ</th>
                                <th>ราคา/หน่วย</th>
                                <th>จำนวนเงิน</th>
                                <th>คะแนน</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($transaction->details as $detail)
                                <tr>
                                    <td>{{ $detail->item->kp_itemsname ?? 'N/A' }}</td>
                                    <td>{{ number_format($detail->amount_in_units, 2) }}
                                        {{ $detail->pricePoint->kp_units_info->unitname ?? 'N/A' }}
                                    </td>
                                    <td>{{ number_format($detail->price_per_unit, 2) }} บาท</td>
                                    <td>{{ number_format($detail->amount, 2) }} บาท</td>
                                    <td>{{ number_format($detail->points) }} คะแนน</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="row">
                    <div class="col-md-6 offset-md-6">
                        <div class="card p-3">
                            <div class="d-flex justify-content-between">
                                <strong>น้ำหนัก/ปริมาณรวม:</strong>
                                <span>{{ number_format($transaction->total_weight, 2) }} kg</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <strong>คะแนนรวม:</strong>
                                <span>{{ number_format($transaction->total_points) }} คะแนน</span>
                            </div>
                            <hr class="my-2">
                            <div class="d-flex justify-content-between fs-4 text-success">
                                <strong>ยอดรวมทั้งหมด:</strong>
                                <span>{{ number_format($transaction->total_amount, 2) }} บาท</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer text-end">
                <button class="btn btn-primary" onclick="window.print()">
                    <i class="bi bi-printer-fill me-1"></i> พิมพ์ใบเสร็จ
                </button>
                <button class="btn btn-primary" onclick="aaa()">
                    <i class="bi bi-printer-fill me-1"></i> aaa
                </button>
                <button class="btn btn-success" id="downloadReceipt">
                    <i class="bi bi-download me-1"></i> บันทึกเป็นรูปภาพ
                </button>

                <a href="{{ route('keptkayas.purchase.select_user') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-1"></i> กลับหน้าเลือกผู้ใช้งาน
                </a>
            </div>
        </div>
    </div>
    <br>
    {{-- <img src="{{ asset('/logo/hs_logo.jpg') }}" id="zh_logo" style="opacity: 1"> --}}
    <canvas id="canvas" width="100" height="100" style="opacity: 0"></canvas>

    <script>


        let ctx2
        document.addEventListener('DOMContentLoaded', function () {
            const receiptCard = document.querySelector('.card.shadow-lg');
            const resultCanvas = document.getElementById('canvas');
            ctx2 = resultCanvas.getContext('2d');
            function renderToCanvas() {
                // คืนค่า Promise ของ html2canvas
                return html2canvas(receiptCard, {
                    scale: 2,
                    useCORS: true
                });
            }

            renderToCanvas().then(canvas => {
                // ปรับขนาด Canvas ผลลัพธ์ให้เท่ากับ Canvas ที่สร้างโดย html2canvas
                resultCanvas.width = 400;//canvas.width;
                resultCanvas.height = 1000;//canvas.height;

                // วาดรูปภาพจาก Canvas ที่สร้างใหม่ ลงบน Canvas ผลลัพธ์
                ctx2.drawImage(canvas, 0, 0);
            });
        })
        // window.onload = (event) => {
        //     const myImage = new Image();
        //     let imgSrc = document.getElementById('zh_logo').src
        //     myImage.src = imgSrc
        //     const convas = document.getElementById('canvas');
        //     const ctx = canvas.getContext('2d');
        //     ctx.drawImage(myImage, 0, 0, canvas.width, canvas.height);

        // };

        // Get references to HTML elements
        const connectButton = document.getElementById('connectButton');
        const printTextButton = document.getElementById('printTextButton'); // Renamed from printButton
        const printImageButton = document.getElementById('printImageButton');
        const printContentTextArea = document.getElementById('printContent');
        const statusDiv = document.getElementById('status');

        let bluetoothDevice; // Variable to store the connected Bluetooth device
        let printCharacteristic; // Variable to store the Characteristic for sending data

        // Define UUIDs for Service and Characteristic
        // **IMPORTANT**: These UUIDs may vary depending on your printer model.
        // You might need to use tools like Chrome's chrome://bluetooth-internals
        // to find the correct UUIDs for your printer.
        // Common UUID examples for thermal printers:
        // Service UUID: '000018f0-0000-1000-8000-00805f9b34fb' (or 'f000')
        // Characteristic UUID: '00002af1-0000-1000-8000-00805f9b34fb' (or '2af1')
        const PRINTER_SERVICE_UUID = '000018f0-0000-1000-8000-00805f9b34fb'; // Example
        const PRINTER_CHARACTERISTIC_UUID = '00002af1-0000-1000-8000-00805f9b34fb'; // Example

        // Printer specific settings for image printing
        // **IMPORTANT**: Adjust PRINTER_WIDTH_DOTS to match your printer's physical resolution.
        // Common values: 384 (for 58mm printers), 576 (for 80mm printers)
        const PRINTER_WIDTH_DOTS = 384; // Example: 58mm printer width in dots
        const FONT_SIZE = 24; // Font size for text on canvas
        const FONT_FAMILY = 'Inter, sans-serif'; // Font family for text on canvas
        const LINE_HEIGHT = FONT_SIZE * 1.5; // Line height for text on canvas
        const MARGIN_X = 10; // Horizontal margin for text on canvas

        const LAST_USED_DEVICE_ID_KEY = 'lastUsedBluetoothDeviceId';
        /**
         * Function to update the status message on the screen.
         * @param {string} message The message to display.
         * @param {string} type The type of message (info, success, error) for styling.
         */
        function updateStatus(message, type = 'info') {
            statusDiv.textContent = `สถานะ: ${message}`;
            statusDiv.className = 'status-message'; // Reset class
            if (type === 'success') {
                statusDiv.classList.add('status-success');
            } else if (type === 'error') {
                statusDiv.classList.add('status-error');
            }
        }

        /**
         * Function to connect to the Bluetooth printer.
         */

        async function connectToPrinter() {
    updateStatus('กำลังค้นหาเครื่องพิมพ์...');

    // 1. ตรวจสอบว่า Web Bluetooth API พร้อมใช้งาน
    if (!navigator.bluetooth) {
        updateStatus('เบราว์เซอร์ของคุณไม่รองรับ Web Bluetooth API โปรดใช้ Chrome หรือ Edge.', 'error');
        return;
    }

    try {
        let selectedDevice = null;
        const lastDeviceId = localStorage.getItem(LAST_USED_DEVICE_ID_KEY);

        // 2. พยายามดึงอุปกรณ์ที่เคยเชื่อมต่อล่าสุด
        if (lastDeviceId) {
            updateStatus('กำลังตรวจสอบอุปกรณ์ที่เคยเชื่อมต่อล่าสุด...');
            
            // ใช้ getDevices() เพื่อดึงรายการอุปกรณ์ที่แอปพลิเคชันเคยมีสิทธิ์เข้าถึง
            const previouslyConnectedDevices = await navigator.bluetooth.getDevices();
            
            // ค้นหาอุปกรณ์ที่มี ID ตรงกับที่บันทึกไว้
            selectedDevice = previouslyConnectedDevices.find(device => device.id === lastDeviceId);
        }

        // 3. ถ้าไม่พบอุปกรณ์ที่เคยเชื่อมต่อ หรือไม่เคยเชื่อมต่อมาก่อน ให้ร้องขออุปกรณ์ใหม่
        if (!selectedDevice) {
            updateStatus('ไม่พบอุปกรณ์ที่บันทึกไว้ หรือการเชื่อมต่อครั้งแรก กำลังร้องขอให้เลือกอุปกรณ์...');
            
            // Request the user to select a Bluetooth device
            selectedDevice = await navigator.bluetooth.requestDevice({
                filters: [{ services: [PRINTER_SERVICE_UUID] }],
                optionalServices: []
            });

            // 💡 4. เก็บ ID ของอุปกรณ์ที่เลือก/เชื่อมต่อสำเร็จลงใน Local Storage
            localStorage.setItem(LAST_USED_DEVICE_ID_KEY, selectedDevice.id);
        } else {
            updateStatus(`กำลังเชื่อมต่ออัตโนมัติกับอุปกรณ์ ${selectedDevice.name || 'ที่ไม่รู้จัก'}...`);
        }
        
        // กำหนดให้อุปกรณ์ที่เลือก/พบ เป็น bluetoothDevice สำหรับฟังก์ชันอื่นๆ
        bluetoothDevice = selectedDevice;

        // 5. ดำเนินการเชื่อมต่อ GATT Server
        const server = await bluetoothDevice.gatt.connect();

        // Get the primary service related to printing
        const service = await server.getPrimaryService(PRINTER_SERVICE_UUID);

        // Get the characteristic used for writing data (printing)
        printCharacteristic = await service.getCharacteristic(PRINTER_CHARACTERISTIC_UUID);

        updateStatus(`เชื่อมต่อสำเร็จกับ ${bluetoothDevice.name || 'อุปกรณ์ที่ไม่รู้จัก'}!`, 'success');
        printImageButton.disabled = false;
        printImageButton.classList.remove('btn-disabled');

        // Add Event Listener for disconnection
        bluetoothDevice.addEventListener('gattserverdisconnected', onDisconnected);

    } catch (error) {
        updateStatus(`การเชื่อมต่อล้มเหลว: ${error.message}`, 'error');
        console.error('Connection error:', error);
        
        // ถ้าเกิด Error ในระหว่างเชื่อมต่ออัตโนมัติ ให้ลบ ID ออก
        if (lastDeviceId && bluetoothDevice && bluetoothDevice.id === lastDeviceId) {
             localStorage.removeItem(LAST_USED_DEVICE_ID_KEY);
             updateStatus('ลบอุปกรณ์ที่บันทึกไว้ออกจากหน่วยความจำแล้ว', 'info');
        }

        printImageButton.disabled = true;
        printImageButton.classList.add('btn-disabled');
    }
}
        // async function connectToPrinter() {
        //     updateStatus('กำลังค้นหาเครื่องพิมพ์...');
        //     try {
        //         // Check if Web Bluetooth API is available
        //         if (!navigator.bluetooth) {
        //             updateStatus('เบราว์เซอร์ของคุณไม่รองรับ Web Bluetooth API โปรดใช้ Chrome หรือ Edge.', 'error');
        //             return;
        //         }

        //         // Request the user to select a Bluetooth device
        //         bluetoothDevice = await navigator.bluetooth.requestDevice({
        //             filters: [{ services: [PRINTER_SERVICE_UUID] }], // Filter devices with the specified Service UUID
        //             optionalServices: [] // No optional services needed for now
        //         });

        //         updateStatus(`กำลังเชื่อมต่อกับ ${bluetoothDevice.name || 'อุปกรณ์ที่ไม่รู้จัก'}...`);

        //         // Connect to the device's GATT Server
        //         const server = await bluetoothDevice.gatt.connect();

        //         // Get the primary service related to printing
        //         const service = await server.getPrimaryService(PRINTER_SERVICE_UUID);

        //         // Get the characteristic used for writing data (printing)
        //         printCharacteristic = await service.getCharacteristic(PRINTER_CHARACTERISTIC_UUID);

        //         updateStatus(`เชื่อมต่อสำเร็จกับ ${bluetoothDevice.name || 'อุปกรณ์ที่ไม่รู้จัก'}!`, 'success');
        //         // printTextButton.disabled = false; // Enable direct text print button
        //         // printTextButton.classList.remove('btn-disabled');
        //         printImageButton.disabled = false; // Enable image print button
        //         printImageButton.classList.remove('btn-disabled');

        //         // Add Event Listener for disconnection
        //         bluetoothDevice.addEventListener('gattserverdisconnected', onDisconnected);

        //     } catch (error) {
        //         updateStatus(`การเชื่อมต่อล้มเหลว: ${error.message}`, 'error');
        //         console.error('Connection error:', error);
        //         // printTextButton.disabled = true; // Disable buttons
        //         // printTextButton.classList.add('btn-disabled');
        //         printImageButton.disabled = true;
        //         printImageButton.classList.add('btn-disabled');
        //     }
        // }

        /**
         * Function to be called when the printer disconnects.
         */
        function onDisconnected() {
            updateStatus('เครื่องพิมพ์ตัดการเชื่อมต่อแล้ว', 'info');
            // printTextButton.disabled = true;
            // printTextButton.classList.add('btn-disabled');
            printImageButton.disabled = true;
            printImageButton.classList.add('btn-disabled');
            bluetoothDevice = null;
            printCharacteristic = null;
        }

        /**
         * Function to send raw text to the printer.
         * This might result in garbled text if the printer doesn't support the encoding or font.
         */
        async function printRawText() {
            if (!printCharacteristic) {
                updateStatus('ยังไม่ได้เชื่อมต่อกับเครื่องพิมพ์', 'error');
                return;
            }

            const textToPrint = printContentTextArea.value || "สวัสดีครับ! นี่คือข้อความทดสอบจาก Web Bluetooth.\n" +
                "-----------------------------------\n" +
                "วันที่: " + new Date().toLocaleString('th-TH') + "\n" +
                "-----------------------------------\n" +
                "ขอบคุณที่ใช้บริการ!\n\n\n";

            updateStatus('กำลังส่งข้อมูลข้อความ (ตรง) ไปยังเครื่องพิมพ์...');
            try {
                const encoder = new TextEncoder(); // Use TextEncoder to convert text to Byte Array
                const data = encoder.encode(textToPrint);

                // Send data to the Characteristic
                // Due to BLE MTU limitations, large data might need to be chunked.
                const CHUNK_SIZE = 20; // Common BLE chunk size (can vary)
                for (let i = 0; i < data.length; i += CHUNK_SIZE) {
                    const chunk = data.slice(i, i + CHUNK_SIZE);
                    await printCharacteristic.writeValueWithoutResponse(chunk);
                    // Add a small delay here if needed, e.g., await new Promise(r => setTimeout(r, 10));
                    // to prevent buffer overflow on some printers.
                }

                updateStatus('ส่งข้อความ (ตรง) สำเร็จ!', 'success');
            } catch (error) {
                updateStatus(`การพิมพ์ข้อความ (ตรง) ล้มเหลว: ${error.message}`, 'error');
                console.error('Raw text print error:', error);
            }
        }

        /**
         * Helper function to get monochrome bitmap data from a canvas context.
         * This converts the canvas image data into a 1-bit bitmap suitable for ESC/POS GS v 0 command.
         * @param {CanvasRenderingContext2D} ctx The 2D rendering context of the canvas.
         * @param {number} width The width of the canvas.
         * @param {number} height The height of the canvas.
         * @returns {Uint8Array} The 1-bit monochrome bitmap data.
         */
        function getMonochromeBitmapData(ctx, width, height) {
            const imageData = ctx.getImageData(0, 0, width, height);
            const data = imageData.data; // RGBA pixel data
            const bitmap = new Uint8Array(Math.ceil(width / 8) * height); // 1 byte per 8 pixels

            for (let y = 0; y < height; y++) {
                for (let x = 0; x < width; x++) {
                    const i = (y * width + x) * 4; // Index for RGBA data
                    const r = data[i];
                    const g = data[i + 1];
                    const b = data[i + 2];
                    const avg = (r + g + b) / 3; // Simple grayscale conversion

                    // Thresholding: If average color is dark enough, consider it black (1), otherwise white (0)
                    // Thermal printers print when a dot is '1' (black), '0' (white)
                    if (avg < 128) { // Adjust threshold as needed
                        const byteIndex = y * Math.ceil(width / 8) + Math.floor(x / 8);
                        const bitIndex = 7 - (x % 8); // Bits are packed from MSB to LSB
                        bitmap[byteIndex] |= (1 << bitIndex);
                    }
                }
            }
            return bitmap;
        }

        /**
         * Function to print text as an image via Canvas.
         * This is recommended for non-English characters or custom fonts.
         */
        async function printTextAsImage() {

            // if (!printCharacteristic) {
            //     updateStatus('ยังไม่ได้เชื่อมต่อกับเครื่องพิมพ์', 'error');
            //     return;
            // }

            updateStatus('กำลังสร้างรูปภาพจากข้อความ...');
            let textToPrint = "";
            const aaa = @json($transaction);
            aaa.details.forEach(item => {
                textToPrint += `รายการขยะ: ${item.item.kp_itemsname}\n
                จำนวนเงิน: ${item.amount}\n`;
            });
            textToPrint += "สวัสดีครับ! นี่คือข้อความทดสอบจาก Web Bluetooth.\n" +
                "-----------------------------------\n" +
                "วันที่: " + new Date().toLocaleString('th-TH') + "\n" +
                "-----------------------------------\n" +
                "ขอบคุณที่ใช้บริการ!\n";
            // Create an offscreen canvas
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');

            // Set canvas width to printer's dot width
            canvas.width = PRINTER_WIDTH_DOTS;

            // Calculate height based on text content and line breaks
            const lines = textToPrint.split('\n');
            let currentY = MARGIN_X; // Start Y position with margin

            // Set font for measurement and drawing
            ctx.font = `${FONT_SIZE}px ${FONT_FAMILY}`;
            ctx.fillStyle = 'black'; // Text color

            // Calculate total height needed for text wrapping
            let totalHeight = 0;
            for (const line of lines) {
                let currentLine = '';
                const words = line.split(' ');
                for (let i = 0; i < words.length; i++) {
                    const testLine = currentLine + (currentLine === '' ? '' : ' ') + words[i];
                    const metrics = ctx.measureText(testLine);
                    const testWidth = metrics.width;

                    if (testWidth > (canvas.width - 2 * MARGIN_X) && i > 0) {
                        totalHeight += LINE_HEIGHT;
                        currentLine = words[i];
                    } else {
                        currentLine = testLine;
                    }
                }
                totalHeight += LINE_HEIGHT;
            }
            // Add some padding at the bottom
            totalHeight += LINE_HEIGHT;

            canvas.height = totalHeight;

            // Clear canvas and set background to white
            ctx.fillStyle = 'white';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            ctx.fillStyle = 'black'; // Set fill style back to black for text

            // Draw text onto the canvas with wrapping
            ctx.font = `${FONT_SIZE}px ${FONT_FAMILY}`;
            for (const line of lines) {
                let currentLine = '';
                const words = line.split(' ');
                for (let i = 0; i < words.length; i++) {
                    const testLine = currentLine + (currentLine === '' ? '' : ' ') + words[i];
                    const metrics = ctx.measureText(testLine);
                    const testWidth = metrics.width;

                    if (testWidth > (canvas.width - 2 * MARGIN_X) && i > 0) {
                        ctx.fillText(currentLine, MARGIN_X, currentY);
                        currentY += LINE_HEIGHT;
                        currentLine = words[i];
                    } else {
                        currentLine = testLine;
                    }
                }
                ctx.fillText(currentLine, MARGIN_X, currentY);
                currentY += LINE_HEIGHT;
            }

            document.body.appendChild(canvas);

            // Get the 1-bit monochrome bitmap data
            const bitmapData = getMonochromeBitmapData(ctx2, canvas.width, canvas.height);

            updateStatus('กำลังส่งรูปภาพไปยังเครื่องพิมพ์...');
            try {
                // ESC/POS command for printing raster bit image (GS v 0)
                // GS v 0 m xL xH yL yH d1...dk
                // m = 0 (normal mode)
                // xL, xH = width in bytes (xL + xH * 256)
                // yL, yH = height in dots (yL + yH * 256)
                const bytesPerRow = Math.ceil(canvas.width / 8);
                const command = new Uint8Array([
                    0x1D, 0x76, 0x30, // GS v 0
                    0x00,             // m (mode 0)
                    bytesPerRow & 0xFF, (bytesPerRow >> 8) & 0xFF, // xL, xH (width in bytes)
                    canvas.height & 0xFF, (canvas.height >> 8) & 0xFF // yL, yH (height in dots)
                ]);

                // Combine command with bitmap data
                const dataToSend = new Uint8Array(command.length + bitmapData.length);
                dataToSend.set(command, 0);
                dataToSend.set(bitmapData, command.length);

                // Send data in chunks
                // **ปรับค่า CHUNK_SIZE และ delay ให้ conservative มากขึ้นสำหรับมือถือ Android**
                const CHUNK_SIZE = 256; // ลดขนาด chunk ลงจาก 512
                for (let i = 0; i < dataToSend.length; i += CHUNK_SIZE) {
                    const chunk = dataToSend.slice(i, i + CHUNK_SIZE);
                    await printCharacteristic.writeValueWithoutResponse(chunk);
                    // เพิ่ม delay เพื่อให้เครื่องพิมพ์มีเวลาประมวลผลแต่ละ chunk
                    await new Promise(r => setTimeout(r, 50)); // เพิ่ม delay จาก 20ms เป็น 50ms
                }

                // Add a few line feeds to push the paper up after printing
                await printCharacteristic.writeValueWithoutResponse(new Uint8Array([0x0A, 0x0A, 0x0A, 0x0A]));

                updateStatus('ส่งรูปภาพสำเร็จ!', 'success');
            } catch (error) {
                updateStatus(`การพิมพ์รูปภาพล้มเหลว: ${error.message}`, 'error');
                console.error('Image print error:', error);
            }
        }


        // Add Event Listeners to buttons
        connectButton.addEventListener('click', connectToPrinter);
        // printTextButton.addEventListener('click', printRawText);
        printImageButton.addEventListener('click', printTextAsImage);

        // Check Web Bluetooth status on page load
        if (!navigator.bluetooth) {
            updateStatus('เบราว์เซอร์ของคุณไม่รองรับ Web Bluetooth API โปรดใช้ Chrome หรือ Edge.', 'error');
            connectButton.disabled = true;
            connectButton.classList.add('btn-disabled');
        }
    </script>
</body>

</html>