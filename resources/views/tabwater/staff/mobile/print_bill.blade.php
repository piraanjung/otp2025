@extends('layouts.tabwater_staff_mobile')
@section('style')

    {{-- <script src="https://cdn.tailwindcss.com"></script> --}}
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:ital,wght@0,500;0,700;1,400;1,500&display=swap"
        rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/qrcode/build/qrcode.min.js"></script>
    <style>
        body {
            font-family: "Sarabun", sans-serif;
            background-color: #f0f4f8;
            display: flex;
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

        .hidden {
            display: none;
        }
    </style>

@endsection
@section('content')

    <div class="container">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">ddเครื่องพิมพ์ความร้อน Web Bluetooth</h1>
        <p class="text-gray-600 mb-2">
            เชื่อมต่อกับเครื่องพิมพ์ความร้อน Bluetooth ของคุณและส่งข้อความเพื่อทดสอบ
        </p>
        <div class="flex flex-col sm:flex-row justify-center gap-4 mb-1">
            <button id="connectButton" class="btn btn-primary">
              
                เชื่อมต่อเครื่องพิมพ์
            </button>
            <button id="printInvoiceButton" class="btn btn-primary w-full">
                พิมพ์ใบแจ้งหนี้
            </button>
        </div>
        
        <div id="status" class="status-message mb-2">
            สถานะ: ยังไม่ได้เชื่อมต่อ
        </div>
        {{-- @dd($data) --}}

        <canvas id="printCanvas" class="mx-auto rounded-lg shadow-md  border border-gray-300"></canvas>
        <img id="logoPreview" src="" alt="Invoice Logo Preview" width="300" style="width: 300px; display: none;">

        <canvas id="qrCodeCanvas" style="opacity: 0"></canvas>

    </div>
@endsection
@section('script')

    <script>
    // Get references to HTML elements
    let data = JSON.parse('{!! addslashes(json_encode($data)) !!}');
    console.log('data', data);
    const connectButton = document.getElementById('connectButton');
    // const printImageButton = document.getElementById('printImageButton');
    const statusDiv = document.getElementById('status');
    const printCanvas = document.getElementById('printCanvas');
    const printInvoiceButton = document.getElementById('printInvoiceButton');
    // เพิ่มตัวแปรสำหรับ preview รูปภาพ (ถ้ามี)
    const canvasImagePreview = document.getElementById('canvasImagePreview');
    // เพิ่มตัวแปรสำหรับ QR Code
    const qrCodeCanvas = document.createElement('canvas');

    // **ส่วนที่เพิ่ม: เตรียมวัตถุ Image สำหรับโลโก้**
    const logoImage = new Image();
    logoImage.src = '{{ asset("logo/" . $data["org_logo"]) }}';

    let bluetoothDevice;
    let printCharacteristic;

    // Define UUIDs
    const PRINTER_SERVICE_UUID = '000018f0-0000-1000-8000-00805f9b34fb';
    const PRINTER_CHARACTERISTIC_UUID = '00002af1-0000-1000-8000-00805f9b34fb';

    // Printer specific settings
    const PRINTER_WIDTH_DOTS = 384;
    const FONT_SIZE = 24;
    const FONT_FAMILY = 'Sarabun, sans-serif';
    const LINE_HEIGHT = FONT_SIZE * 1.5;
    const MARGIN_X = 10;
    const qrCodeSize = 250;

    // Function to update the status
    function updateStatus(message, type = 'info') {
        statusDiv.textContent = `สถานะ: ${message}`;
        statusDiv.className = 'status-message';
        if (type === 'success') {
            statusDiv.classList.add('status-success');
        } else if (type === 'error') {
            statusDiv.classList.add('status-error');
        }
    }

    // **ส่วนที่เพิ่ม: ฟังก์ชันสำหรับบันทึกและดึงค่า Printer ID**
    function savePrinterId(device) {
        if (device && device.id) {
            alert(device.id)
            localStorage.setItem('printerDeviceId', device.id);
        }
    }

    function getSavedPrinterId() {
        return localStorage.getItem('printerDeviceId');
    }
    
    // **ส่วนที่แก้ไข: Function เชื่อมต่อเครื่องพิมพ์แบบปรับปรุงใหม่**
async function connectToPrinter() {
    updateStatus('กำลังค้นหาเครื่องพิมพ์...');
    try {
        if (!navigator.bluetooth) {
            updateStatus('เบราว์เซอร์ของคุณไม่รองรับ Web Bluetooth API', 'error');
            return;
        }

        let bluetoothDevice;
        const savedDeviceId = getSavedPrinterId();
        
        // ตรวจสอบว่า navigator.bluetooth.getDevices มีอยู่หรือไม่
        if (savedDeviceId ) {//&& navigator.bluetooth.getDevices
            alert('aaaaa='+savedDeviceId)

            // **ส่วนที่ปรับปรุง: ใช้ try-catch เพื่อจัดการ error จาก getDevices()**
            try {
                const devices = await navigator.bluetooth.getDevices();
                bluetoothDevice = devices.find(device => device.id === savedDeviceId);
            } catch (err) {
                console.warn('getDevices() failed, falling back to requestDevice()', err);
                bluetoothDevice = null; // ตั้งค่าเป็น null เพื่อให้เข้าเงื่อนไข else
            }
        }
        
        if (bluetoothDevice) {
            updateStatus('พบอุปกรณ์ที่เคยเชื่อมต่อ, กำลังเชื่อมต่ออัตโนมัติ...');
        } else {
            // **ใช้ requestDevice() เป็น fallback เสมอ**
            updateStatus('ไม่พบอุปกรณ์ที่บันทึกไว้ หรือฟังก์ชันไม่รองรับ กำลังค้นหาอุปกรณ์ใหม่...');
            bluetoothDevice = await navigator.bluetooth.requestDevice({
                filters: [{ services: [PRINTER_SERVICE_UUID] }],
                optionalServices: []
            });
        }
        
        // **ดำเนินการเชื่อมต่อต่อจากตรงนี้**
        updateStatus(`กำลังเชื่อมต่อกับ ${bluetoothDevice.name || 'อุปกรณ์ที่ไม่รู้จัก'}...`);
        const server = await bluetoothDevice.gatt.connect();
        const service = await server.getPrimaryService(PRINTER_SERVICE_UUID);
        printCharacteristic = await service.getCharacteristic(PRINTER_CHARACTERISTIC_UUID);

        updateStatus(`เชื่อมต่อสำเร็จกับ ${bluetoothDevice.name || 'อุปกรณ์ที่ไม่รู้จัก'}!`, 'success');
        
        // บันทึก ID ของอุปกรณ์ที่เชื่อมต่อสำเร็จ
        savePrinterId(bluetoothDevice); 

    } catch (error) {
        updateStatus(`การเชื่อมต่อล้มเหลว: ${error.message}`, 'error');
        console.error('Connection error:', error);
    }
}
    // **ส่วนที่แก้ไข: Function to be called when the printer disconnects**
    function onDisconnected() {
        updateStatus('เครื่องพิมพ์ตัดการเชื่อมต่อแล้ว', 'info');
        // printImageButton.disabled = true;
        // printImageButton.classList.add('btn-disabled');
        printInvoiceButton.disabled = true;
        printInvoiceButton.classList.add('btn-disabled');
        bluetoothDevice = null;
        printCharacteristic = null;
    }

    window.addEventListener('load', async function () {
        await generateInvoiceCanvas();
    });

    // Helper function to get monochrome bitmap data
    function getMonochromeBitmapData(ctx, width, height) {
        const imageData = ctx.getImageData(0, 0, width, height);
        const data = imageData.data;
        const bitmap = new Uint8Array(Math.ceil(width / 8) * height);
        for (let y = 0; y < height; y++) {
            for (let x = 0; x < width; x++) {
                const i = (y * width + x) * 4;
                const r = data[i];
                const g = data[i + 1];
                const b = data[i + 2];
                const avg = (r + g + b) / 3;
                if (avg < 128) {
                    const byteIndex = y * Math.ceil(width / 8) + Math.floor(x / 8);
                    const bitIndex = 7 - (x % 8);
                    bitmap[byteIndex] |= (1 << bitIndex);
                }
            }
        }
        return bitmap;
    }

    // Function to print content of a canvas
    async function printCanvasContent(canvas) {
        if (!printCharacteristic) {
            updateStatus('ยังไม่ได้เชื่อมต่อกับเครื่องพิมพ์', 'error');
            return;
        }

        const ctx = canvas.getContext('2d');
        const bitmapData = getMonochromeBitmapData(ctx, canvas.width, canvas.height);

        updateStatus('กำลังส่งรูปภาพไปยังเครื่องพิมพ์...');
        try {
            const bytesPerRow = Math.ceil(canvas.width / 8);
            const command = new Uint8Array([
                0x1D, 0x76, 0x30, 0x00,
                bytesPerRow & 0xFF, (bytesPerRow >> 8) & 0xFF,
                canvas.height & 0xFF, (canvas.height >> 8) & 0xFF
            ]);

            const dataToSend = new Uint8Array(command.length + bitmapData.length);
            dataToSend.set(command, 0);
            dataToSend.set(bitmapData, command.length);

            const CHUNK_SIZE = 220;
            for (let i = 0; i < dataToSend.length; i += CHUNK_SIZE) {
                const chunk = dataToSend.slice(i, i + CHUNK_SIZE);
                await printCharacteristic.writeValueWithoutResponse(chunk);
                await new Promise(r => setTimeout(r, 50));
            }
            await printCharacteristic.writeValueWithoutResponse(new Uint8Array([0x0A, 0x0A, 0x0A, 0x0A]));
            updateStatus('ส่งรูปภาพสำเร็จ!', 'success');
        } catch (error) {
            updateStatus(`การพิมพ์รูปภาพล้มเหลว: ${error.message}`, 'error');
            console.error('Image print error:', error);
        }
    }

    // Function to generate the invoice on the canvas
    async function generateInvoiceCanvas() {
        const canvas = printCanvas;
        const ctx = canvas.getContext('2d');

        const dotWidth = 384;
        const lineHeight = 18;
        const smallFontSize = '14px Sarabun, sans-serif';
        const mediumFontSize = '16px Sarabun, sans-serif';
        const largeFontSize = '20px Sarabun, sans-serif';
        const largestFontSize = '24px Sarabun, sans-serif';
        const pad = 5;

        let totalHeight = 10;

        // รอให้รูปภาพโหลดเสร็จ
        await new Promise(resolve => {
            if (logoImage.complete) {
                resolve();
            } else {
                logoImage.onload = resolve;
                logoImage.onerror = resolve;
            }
        });

        let zero18 = "000000000000000000";
        // $("#qrcode #qrcode_div").html("")
        let inv_id =
            zero18.slice(0, 18 - (data.inv_id).toString().length) +
            "" +
            data.inv_id;
        let meter_id =
            zero18.slice(0, 18 - (data.meter_id).toString().length) +
            "" +
            data.meter_id;

        let payment_str = `|099400035262000\n${meter_id}\n${inv_id}\n${(data.netpaid).toString().replace(".", "")}`;
        // $("#qrcode_textarea").val(payment_str);
        console.log('payment_str', payment_str)

        const qrCodeText = payment_str; // ข้อมูลสำหรับ QR Code

        // วาด QR code ลงบน Canvas ใบเสร็จหลัก

        // **ส่วนที่ปรับปรุง: คำนวณความสูงทั้งหมดของ Canvas**
        let currentY = 10;
        
        // ถ้ามีโลโก้ ให้เพิ่มความสูงสำหรับโลโก้
        if (logoImage.complete && logoImage.naturalWidth > 0) {
            const logoHeight = (logoImage.naturalHeight / logoImage.naturalWidth) * 120;
            currentY += logoHeight + pad;
        }

        currentY += lineHeight + pad; // Company Name
        currentY += lineHeight + pad; // Address
        currentY += lineHeight + pad; // ใบแจ้งหนี้ค่าน้ำประปา text
        currentY += lineHeight + pad + 10; // (org_dept_name)
        currentY += lineHeight + pad + 10; // ชื่อ-สกุล
        currentY += lineHeight + pad + 10; // ที่อยู่
        currentY += lineHeight; // เลขใบแจ้งหนี้, รหัสสมาชิก
        currentY += lineHeight; // เลขมิเตอร์, inv_id
        currentY += pad + 10;
        currentY += lineHeight + pad + 10; // รอบบิล, วันที่
        currentY += lineHeight; // หัวข้อตาราง
        currentY += lineHeight + 10; // ข้อมูลมิเตอร์
        currentY += lineHeight + 5; // ข้อมูลค่ารักษามิเตอร์
        currentY += lineHeight + 10; // ข้อมูลภาษี 7%
        
        if(Object.keys(data.owe_infos).length > 0){
            currentY += pad + 5;
            currentY += lineHeight;
            currentY += Object.keys(data.owe_infos).length * lineHeight;
        }
        
        currentY += pad + 10;
        currentY += lineHeight + pad + 10; // ยอดรวม
        currentY += lineHeight + 5; // โปรดชำระเงิน
        currentY += lineHeight + qrCodeSize;
        currentY += lineHeight + pad; // โปรดชำระเงินภายในวันที่
        currentY += lineHeight + pad;
        currentY += lineHeight + pad;
        currentY += lineHeight + pad;

        const totalCanvasHeight = currentY + 10;

        // **ขั้นตอนที่ 2: วาดเนื้อหาทั้งหมดลงบน Canvas เพียงครั้งเดียว**
        canvas.width = dotWidth;
        canvas.height = totalCanvasHeight;

        let drawY = 10;
        ctx.fillStyle = 'white';
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        ctx.fillStyle = 'black';
        ctx.textAlign = 'center';

        if (logoImage.complete && logoImage.naturalWidth > 0) {
            const logoWidth = 120;
            const logoHeight = (logoImage.naturalHeight / logoImage.naturalWidth) * logoWidth;
            const logoX = (canvas.width / 2) - (logoWidth / 2);
            ctx.drawImage(logoImage, logoX, drawY, logoWidth, logoHeight);
            drawY += logoHeight + pad;
        }
        
        // Company Name
        ctx.font = `bold ${largestFontSize}`;
        ctx.fillText(data.org_name, canvas.width / 2, drawY + lineHeight);
        drawY += lineHeight;

        // Company Address
        ctx.font = smallFontSize;
        ctx.textAlign = 'center';
        ctx.fillText(data.org_address, canvas.width / 2, drawY + lineHeight);
        drawY += lineHeight;
        drawY += pad + 10;

        ctx.textAlign = 'center';
        ctx.font = `bold ${mediumFontSize}`;
        ctx.fillText(`ใบแจ้งหนี้ค่าน้ำประปา`, canvas.width / 2, drawY + lineHeight);
        drawY += lineHeight + pad;

        ctx.textAlign = 'center';
        ctx.font = smallFontSize;
        ctx.fillText(`( ${data.org_dept_name} :โทร.${data.org_dept_phone} )`, canvas.width / 2, drawY + lineHeight);
        drawY += lineHeight + pad;
        drawY += pad+10;

        ctx.textAlign = 'left';
        ctx.font = smallFontSize;
        ctx.fillText(`ชื่อ-สกุล:`, 10, drawY + lineHeight);
        ctx.font = `bold ${mediumFontSize}`;
        ctx.fillText(data.name, 70, drawY + lineHeight);
        drawY += lineHeight;

        ctx.font = smallFontSize;
        ctx.textAlign = 'left';
        ctx.fillText(`ที่อยู่:`, 10, drawY + lineHeight);
        ctx.fillText(data.user_address, 70, drawY + lineHeight);
        drawY += lineHeight + 10;
        
        ctx.textAlign = 'left';
        ctx.font = `${smallFontSize}`;
        ctx.fillText('เลขใบแจ้งหนี้', 10, drawY + lineHeight);
        ctx.textAlign = 'right';
        ctx.fillText('รหัสสมาชิก', canvas.width * 0.55, drawY + lineHeight);
        ctx.fillText('เลขเครื่องมิเตอร์', canvas.width - 10, drawY + lineHeight);
        drawY += lineHeight

        ctx.textAlign = 'left';
        ctx.font = `bold ${mediumFontSize}`;
        ctx.fillText(`INV-${data.inv_id}`, 10, drawY + lineHeight);
        ctx.textAlign = 'right';
        ctx.fillText(data.meternumber, canvas.width * 0.55, drawY + lineHeight);
        ctx.fillText(data.factory_no, canvas.width - 10, drawY + lineHeight);
        drawY += lineHeight

        ctx.fillRect(0, drawY + pad, canvas.width, 1);
        drawY += pad + 10;

        ctx.textAlign = 'left';
        ctx.font = `bold ${mediumFontSize}`;
        ctx.fillText(`รอบบิล: ${data.period}`, 10, drawY + lineHeight);
        ctx.textAlign = 'right';
        ctx.fillText(`วันที่: ${data.created_at}`, canvas.width - 10, drawY + lineHeight);
        drawY += lineHeight + pad + 10;

        ctx.textAlign = 'left';
        ctx.font = `${smallFontSize}`;
        ctx.fillText('ก่อนจด', 10, drawY + lineHeight);
        ctx.textAlign = 'right';
        ctx.fillText('มิเตอร์ปัจุบัน', canvas.width * 0.45, drawY + lineHeight);
        ctx.fillText('ใช้น้ำ(หน่วย)', canvas.width * 0.7, drawY + lineHeight);
        ctx.fillText('เป็นเงิน(บาท)', canvas.width - 10, drawY + lineHeight);
        drawY += lineHeight;

        ctx.font = `bold ${mediumFontSize}`;
        ctx.fillText(data.lastmeter, 50, drawY + lineHeight);
        ctx.textAlign = 'right';
        ctx.fillText(data.currentmeter, canvas.width * 0.45, drawY + lineHeight);
        ctx.fillText(data.water_used, canvas.width * 0.7, drawY + lineHeight);
        ctx.fillText(parseFloat(data.paid).toFixed(2), canvas.width - 10, drawY + lineHeight);
        drawY += lineHeight+10;

        ctx.fillRect(0, drawY + pad, canvas.width, 1);
        drawY += pad + 10;


        ctx.textAlign = 'left';
        ctx.font = `${mediumFontSize}`;
        ctx.fillText('ค่ารักษามิเตอร์', 10, drawY + lineHeight);
        ctx.textAlign = 'right';
        ctx.fillText((data.reserve_meter).toFixed(2), canvas.width - 10, drawY + lineHeight);
        drawY += lineHeight + 5;

        ctx.textAlign = 'left';
        ctx.font = `${mediumFontSize}`;
        ctx.fillText('ภาษีมูลค่าเพิ่ม 7%', 10, drawY + lineHeight);
        ctx.textAlign = 'right';
        ctx.fillText((data.vat).toFixed(2), canvas.width - 10, drawY + lineHeight);
        drawY += lineHeight+10;
        
        if(Object.keys(data.owe_infos).length > 0){
            ctx.fillRect(0, drawY + pad, canvas.width, 1);
            drawY += pad + 5;
            ctx.textAlign = 'left';
            ctx.font = mediumFontSize;
            ctx.fillText(`ค้างชำระ`, 10, drawY + lineHeight);
            drawY += lineHeight;
            data.owe_infos.forEach(item => {
                ctx.font = `${smallFontSize}`;
                ctx.textAlign = 'right';
                ctx.fillText(item.inv_period, canvas.width * 0.45, drawY + lineHeight);
                ctx.textAlign = 'right';
                ctx.font = `bold ${smallFontSize}`;
                ctx.fillText((item.totalpaid).toFixed(2), canvas.width - 10, drawY + lineHeight);
                drawY += lineHeight;
            });
        }

        ctx.fillRect(0, drawY + pad, canvas.width, 1);
        drawY += pad + 10;

        // Total
        ctx.font = `bold ${largeFontSize}`;
        ctx.textAlign = 'left';
        ctx.fillText('ยอดชำระรวม', 10, drawY + lineHeight);
        ctx.textAlign = 'right';
        ctx.fillText(data.netpaid +' บาท', canvas.width - 10, drawY + lineHeight);
        drawY += lineHeight + pad+10;

        ctx.font = `${mediumFontSize}`;
        ctx.textAlign = 'center';
        ctx.fillText(`( สแกน QR Code เพื่อชำระเงิน ${data.netpaid} บาท)`, canvas.width/2, drawY + lineHeight);
        drawY +=5;
        await QRCode.toCanvas(qrCodeCanvas, qrCodeText, {
            width: qrCodeSize
        });
        const qrCodeX = (canvas.width / 2) - (qrCodeSize / 2);
        const qrCodeY = drawY + lineHeight;
        ctx.drawImage(qrCodeCanvas, qrCodeX, qrCodeY);
        drawY += lineHeight + qrCodeSize;
        
        // Footer
        ctx.font = smallFontSize;
        ctx.textAlign = 'center';
        ctx.fillText(`โปรดชำระเงินภายในวันที่ ${data.expired_date}`, canvas.width / 2, drawY + lineHeight);
        drawY += lineHeight + pad;
        ctx.textAlign = 'center';
        ctx.font = smallFontSize;
        ctx.fillText(`หากเกินกำหนดจะถูกระงับการใช้น้ำ`, canvas.width / 2, drawY + lineHeight);
        drawY += lineHeight + pad;
        ctx.textAlign = 'center';
        ctx.fillText(`หลังจากได้รับการชำระหนี้ค้างทั้งหมดแล้ว`, canvas.width / 2, drawY + lineHeight);
        drawY += lineHeight + pad;

        // แสดงพรีวิวของ Canvas
        if (canvasImagePreview) {
            canvasImagePreview.src = canvas.toDataURL('image/png');
            canvasImagePreview.classList.remove('hidden');
        }
    }
    // Add Event Listeners to buttons
    connectButton.addEventListener('click', connectToPrinter);
    // printImageButton.addEventListener('click', async () => await printCanvasContent(printCanvas));
    printInvoiceButton.addEventListener('click', async () => {
        await generateInvoiceCanvas();
        await printCanvasContent(printCanvas);
    });


    // Check Web Bluetooth status on page load
    if (!navigator.bluetooth) {
        updateStatus('เบราว์เซอร์ของคุณไม่รองรับ Web Bluetooth API โปรดใช้ Chrome หรือ Edge.', 'error');
        connectButton.disabled = true;
        connectButton.classList.add('btn-disabled');
    }
</script>
@endsection