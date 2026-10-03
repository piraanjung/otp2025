// -------------------------------------------------------------------------
//  โมดูลระบบเชื่อมต่อเครื่องพิมพ์บลูทูธ (Bluetooth LE เท่านั้น ไม่ใช้ระบบเก่า)
// -------------------------------------------------------------------------
function checkBluetoothStatus() {
    const statusText = document.getElementById('status');
    if (!statusText) return;

    const BleClient = window.Capacitor?.Plugins?.BluetoothLe;
    if (BleClient) {
        statusText.innerText = "💻 โหมดจำลองบน Browser (คอมพิวเตอร์)";
        statusText.style.color = "#858796";
    } else {
        const isConnected = localStorage.getItem('is_printer_connected') === 'true';
        const printerName = localStorage.getItem('connected_printer_name') || 'เครื่องพิมพ์';
        if (isConnected) {
            statusText.innerText = `🟢 เชื่อมต่ออยู่กับ: ${printerName}`;
            statusText.style.color = "green";
        } else {
            // statusText.innerText = "🔴 พร้อมเชื่อมต่อ (เปิดบลูทูธเครื่องพิมพ์ไว้เลย)";
            statusText.style.color = "#dc3545";
        }
    }
}

// 🔄 ระบบสั่งค้นหา & เชื่อมต่ออุปกรณ์ด้วย Capacitor BLE
const btnConnect = document.getElementById('btnConnect');
if (btnConnect) {
    btnConnect.addEventListener('click', async () => {
        const statusText = document.getElementById('status');
        const BleClient = window.Capacitor?.Plugins?.BluetoothLe;

        if (!BleClient) {
            alert("ระบบตรวจไม่พบปลั๊กอิน Capacitor Bluetooth LE (เปิดบนคอมพิวเตอร์จะทดสอบปุ่มนี้ไม่ได้)");
            return;
        }

        try {
            if (statusText) statusText.innerText = "กำลังตรวจสอบสิทธิ์บลูทูธ...";

            // 🟢 เรียกขอเปิดใช้งานบลูทูธและสิทธิ์ค้นหาอุปกรณ์ (แมทช์กับสิทธิ์ Android 12)
            await BleClient.initialize();

            if (statusText) statusText.innerText = "กำลังสแกนหาอุปกรณ์บลูทูธรอบตัว...";

            // 🟢 เปิดหน้าป๊อปอัปให้เจ้าหน้าที่ทำการเลือกจับคู่เครื่องพิมพ์ใบเสร็จ
            const device = await BleClient.requestDevice();

            if (!device) {
                if (statusText) statusText.innerText = "ยกเลิกการเลือกอุปกรณ์";
                return;
            }

            if (statusText) statusText.innerText = `กำลังเชื่อมต่อเข้ากับ: ${device.name || 'Thermal Printer'}...`;

            // 🟢 เชื่อมต่อสัญญาณโดยตรง
            await BleClient.connect({ deviceId: device.deviceId });

            localStorage.setItem('printer_id', device.deviceId);
            localStorage.setItem('connected_printer_name', device.name || 'Thermal Printer');
            localStorage.setItem('is_printer_connected', 'true');

            if (statusText) {
                statusText.innerText = `🟢 เชื่อมต่อสำเร็จกับ: ${device.name || 'Thermal Printer'}`;
                statusText.style.color = "green";
            }
            updateGlobalPrinterStatus();
            alert(`✅ เชื่อมต่อกับเครื่องพิมพ์สำเร็จ!`);

            // วาดใบเสร็จตัวอย่างอัปเดตลง Canvas ทันที
            drawReceipt();

        } catch (error) {
            if (statusText) {
                statusText.innerText = "❌ การเชื่อมต่อล้มเหลว";
                statusText.style.color = "red";
            }
            console.error(error);
            alert("เกิดข้อผิดพลาดในการเชื่อมต่อ: " + error.message);
        }
    });
}

// 🖼️ ฟังก์ชันสำหรับวาดรูปหน้าตาใบเสร็จสลิปจำลองลง Canvas
function drawReceipt() {
    const canvas = document.getElementById('receiptCanvas');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');

    // 1. สร้าง Image Object สำหรับ Logo
    const logo = new Image();
    logo.src = "{{ asset('logo/chiangkreu_sakonnakhon_gray.png') }}"; // 👈 เปลี่ยนเป็น Path หรือ URL ของรูปโลโก้ของคุณ

    // 2. เมื่อรูปภาพโหลดเสร็จแล้วค่อยเริ่มวาด Canvas
    logo.onload = () => {
        // ล้าง Canvas และลงสีพื้นหลัง
        ctx.fillStyle = '#FFFFFF';
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        ctx.fillStyle = '#000000';

        // ตรวจสอบว่ามีของในตะกร้าไหม ถ้าไม่มีให้ใส่ข้อมูลจำลองเพื่อพรีวิว/ทดสอบพิมพ์
        const itemsToPrint = purchaseCart.length > 0 ? purchaseCart : [
            { item_name: "ขวดพลาสติกใส (PET)", amount_in_units: 5.5, unit_name: "กก.", amount: 24.75, points: 10, pointsum: 55 },
            { item_name: "กระดาษลัง", amount_in_units: 10.0, unit_name: "กก.", amount: 75.00, points: 20, pointsum: 200 },
            { item_name: "ขวดเบียร์ลีโอ", amount_in_units: 10.0, unit_name: "ลัง.", amount: 75.00, points: 20, pointsum: 200 },
            // { item_name: "กระดาษลัง", amount_in_units: 10.0, unit_name: "กก.", amount: 75.00 },
        ];
        let expandHCanvas = 550 + (40 * itemsToPrint.length)
        $('#receiptCanvas').attr('height', expandHCanvas)

        // ----------------------------------------------------
        // 3. วาดโลโก้ลง Canvas (ปรับตำแหน่ง X, Y และ ขนาด W, H ตามต้องการ)
        // ctx.drawImage(image, x, y, width, height)
        const logoWidth = 170;   // กว้าง 60px
        const logoHeight = 150;  // สูง 60px
        const logoX = 0;//(canvas.width - logoWidth) / 2; // จัดให้อยู่ตรงกลาง (162)
        const logoY = 15;       // วาดเริ่มที่ Y = 15

        ctx.drawImage(logo, logoX, logoY, logoWidth, logoHeight);
        // ----------------------------------------------------
        let xExis = (canvas.width + logoWidth - 40) / 2;
        let yExis = 0;
        ctx.font = 'bold 20px Arial';
        ctx.textAlign = 'right';
        ctx.fillText('เทศบาลตำบล', canvas.width - 20, logoHeight / 4);

        yExis += logoHeight / 2;
        ctx.font = 'bold 28px Arial';
        ctx.textAlign = 'right';
        ctx.fillText('เชียงเครือ', canvas.width - 20, yExis);

        yExis += 20;
        let rage = 20
        ctx.font = '16px Arial';
        ctx.textAlign = 'right';
        ctx.lineWidth = 2;
        ctx.beginPath(); ctx.moveTo(logoWidth - 22, yExis); ctx.lineTo(canvas.width - 20, yExis); ctx.stroke();

        ctx.fillText('109 หมู่ 14 ต.เชียงเครือ', canvas.width - 20, yExis + (rage * 1));
        ctx.fillText('อ.เมืองสกลนคร จ.สกลนคร', canvas.width - 20, yExis + (rage * 2));
        ctx.fillText('โทร.042-4345433', canvas.width - 20, yExis + (rage * 3));

        // ปรับตำแหน่ง Y ของข้อความหัวข้อให้ขยับลงมาต่อจากโลโก้
        ctx.font = 'bold 24px Arial';
        ctx.textAlign = 'center';
        ctx.fillText('ธนาคารขยะรีไซเคิล', canvas.width / 2, yExis + (rage * 5));

        ctx.lineWidth = 2;
        ctx.beginPath(); ctx.moveTo(20, yExis + (rage * 5 + 10));
        ctx.lineTo(canvas.width - 20, yExis + (rage * 5 + 10)); ctx.stroke();

        rage += 3;
        ctx.font = 'bold 18px Arial';
        ctx.textAlign = 'left';
        const memberName = currentActiveMember ? `${currentActiveMember.firstname} ${currentActiveMember.lastname}` : "นายสมชาย ใจดี (ทดสอบ)";
        ctx.fillText(`สมาชิก:`, 20, yExis + (rage * 6));

        ctx.font = ' 18px Arial';
        ctx.textAlign = 'left';
        ctx.fillText(`${memberName}`, 90, yExis + (rage * 6));


        ctx.font = 'bold 18px Arial';
        ctx.textAlign = 'left';
        ctx.fillText('ที่อยู่:', 40, yExis + (rage * 7));

        ctx.font = '16px Arial';
        ctx.textAlign = 'left';
        ctx.fillText(`109 หมู่ 14 ต.เชียงเครือ`, 90, yExis + (rage * 7));
        ctx.textAlign = 'left'

        ctx.fillText(`อ.เมืองสกลนคร จ.สกลนคร 47000`, 90, yExis + (rage * 8));

        ctx.lineWidth = 2;
        ctx.beginPath(); ctx.moveTo(20, yExis + (rage * 9 - 10));
        ctx.lineTo(canvas.width - 20, yExis + (rage * 9 - 10)); ctx.stroke();


        const today = new Date().toLocaleDateString('th-TH');
        ctx.font = 'bold 18px Arial';
        ctx.fillText(`วันที่:`, 20, yExis + (rage * 10));

        ctx.font = '16px Arial';
        ctx.fillText(`${today}`, 70, yExis + (rage * 10));
        ctx.font = 'bold 18px Arial';
        ctx.fillText(`ใบเสร็จเลขที่:`, 20, yExis + (rage * 11));
        ctx.font = '16px Arial';
        ctx.fillText(`0333-333-333`, 130, yExis + (rage * 11));

        let startY = yExis + rage * 13;
        let grandTotal = 0;
        let pointTotal = 0;

        ctx.font = '16px Arial';
        ctx.textAlign = 'left';
        ctx.fillText(`แต้ม`, 250, startY - 20);
        ctx.fillText(`บาท`, 333, startY - 20);


        itemsToPrint.forEach(item => {
            // 1. วาดชื่อสินค้า (ชิดซ้ายที่ X = 20)
            ctx.font = '16px Arial';
            ctx.textAlign = 'left';
            ctx.fillText(`- ${item.item_name}`, 20, startY);

            // 2. วาดจำนวน + หน่วย (กำหนดพิกัด X คงที่ เช่น X = 170 และ X = 210)
            ctx.textAlign = 'right';
            ctx.font = 'bold 17px Arial';
            ctx.fillText(`${item.amount_in_units}`, 210, startY);
            ctx.textAlign = 'left';
            ctx.font = '15px Arial';
            ctx.fillText(`${item.unit_name}`, 215, startY);

            // วาดรายละเอียดราคา/แต้มย่อย (บรรทัดล่าง)
            ctx.font = '14px Arial';
            ctx.fillText(`( 5 บาท:กก. / ${item.points} แต้ม:กก.)`, 40, startY + 20);

            // 3. วาดรวมแต้ม และ รวมเงิน (ชิดขวาตามพิกัดเดิม)
            ctx.textAlign = 'right';
            ctx.font = '16px Arial';
            ctx.fillText(`${item.pointsum}`, 280, startY);
            ctx.fillText(`${item.amount.toFixed(2)}`, 364, startY);

            grandTotal += item.amount;
            pointTotal += item.pointsum;
            startY += 40;
        });

        ctx.beginPath(); ctx.moveTo(20, startY); ctx.lineTo(364, startY); ctx.stroke();
        startY += 40;

        ctx.font = 'bold 18px Arial';
        ctx.textAlign = 'left';
        ctx.fillText('รวมรับเงิน/แต้ม สะสม:', 20, startY);
        ctx.textAlign = 'right';
        ctx.fillText(`${pointTotal} `, 280, startY);
        ctx.fillText(`${grandTotal.toFixed(2)} `, 364, startY);

        ctx.font = 'bold 14px Arial';
        ctx.textAlign = 'left';
        ctx.fillText(`แต้ม`, 250, startY + 20);
        ctx.fillText(`บาท`, 334, startY + 20);


        startY += 50;
        ctx.font = '14px Arial';
        ctx.textAlign = 'center';
        ctx.fillText('ขอบคุณที่ร่วมลดโลกร้อนกับ อบต.', 192, startY);
        ctx.fillText('--- ใบเสร็จระบบพิมพ์กราฟิก ---', 192, startY + 25);
    };

    // กรณีรูปโหลดไม่ผ่าน สามารถวาดส่วนอื่นต่อได้เพื่อไม่ให้ระบบค้าง
    logo.onerror = () => {
        console.error("ไม่สามารถโหลดภาพ Logo ได้");
    };
}

// ⚡ ฟังก์ชันสำหรับส่งข้อมูลรูปกราฟิกออกไปยังหัวพิมพ์ความร้อน (ใช้ร่วมกันทั้งปุ่มเทสและบิลจริง)
async function sendCanvasToPrinter() {
    const BleClient = window.Capacitor?.Plugins?.BluetoothLe;
    const deviceId = localStorage.getItem('printer_id');

    if (!BleClient || !deviceId) {
        throw new Error("ยังไม่ได้เชื่อมต่อเครื่องพิมพ์บลูทูธ กรุณากดปุ่มเชื่อมต่ออุปกรณ์ก่อนครับ");
    }

    // 🟢 ตรวจสอบตัวแปรที่ดึงมาจากสคริปต์ออฟไลน์ใน index.html
    const EncoderClass = window.ReceiptPrinterEncoder;

    if (!EncoderClass) {
        throw new Error("ระบบหาโมดูลแปลงรูปภาพ (ReceiptPrinterEncoder) ไม่เจอ กรุณาตรวจสอบการใส่แท็ก script ใน index.html");
    }

    const canvas = document.getElementById('receiptCanvas');
    if (!canvas) {
        throw new Error("ไม่พบหน้าจอ Canvas (#receiptCanvas)");
    }
    const ctx = canvas.getContext('2d');

    // 🟢 1. คำนวณหาค่าความสูงใหม่ที่ใกล้เคียงที่สุดและหารด้วย 8 ลงตัวพอดี (Multiple of 8)
    const exactHeight = canvas.height;
    const adjustedHeight = Math.ceil(exactHeight / 8) * 8; // ปัดขึ้นให้หาร 8 ลงตัวเสมอล้างพัง

    let imageData;

    // 🟢 2. เช็คว่าถ้าความสูงเดิมหาร 8 ไม่ลงตัว ให้สร้างแผ่นภาพสำรองที่ปัดเศษแล้ว เพื่อไม่ให้ภาพยืดหรือพัง
    if (exactHeight !== adjustedHeight) {
        // สร้าง Canvas ชั่วคราวขนาดที่ถอดรหัสผ่านชัวร์ 100%
        const tempCanvas = document.createElement('canvas');
        tempCanvas.width = canvas.width;
        tempCanvas.height = adjustedHeight;
        const tempCtx = tempCanvas.getContext('2d');

        // เทสีพื้นหลังเป็นสีขาว (เพื่อไม่ให้ส่วนที่ขยายออกไปกลายเป็นสีดำปื้น)
        tempCtx.fillStyle = '#FFFFFF';
        tempCtx.fillRect(0, 0, tempCanvas.width, tempCanvas.height);

        // วาดใบเสร็จตัวจริงของคุณพี่ทับลงไป
        tempCtx.drawImage(canvas, 0, 0);
        imageData = tempCtx.getImageData(0, 0, tempCanvas.width, tempCanvas.height);
    } else {
        // ถ้าหาร 8 ลงตัวอยู่แล้ว ดึงค่าตรงๆ ไปใช้ได้เลยครับ
        imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
    }

    // 🟢 3. เรียกใช้งาน Object ตัวใหม่ตามปกติ (ส่งภาพที่ปรับขนาดความสูงแมทช์ชิ่งเรียบร้อยแล้ว)
    const encoder = new EncoderClass({
        language: 'esc-pos'
    });

    // แยกสั่งงานทีละคำสั่ง ห้ามเขียนต่อท้ายแบบเดิมครับ
    encoder.image(imageData, canvas.width, imageData.height, 'threshold');
    encoder.newline(4);

    // บรรทัดสุดท้ายให้ดึงรหัสฐานสองออกมาเก็บในตัวแปร binaryCommands
    const binaryCommands = encoder.encode();

    // ค้นหา Service และส่งสัญญาณบลูทูธความเร็วต่ำ
    const result = await BleClient.getServices({ deviceId: deviceId });
    const servicesList = result.services || [];
    let targetServiceUuid = null;
    let targetCharacteristicUuid = null;

    // 🟢 ลูปค้นหาพอร์ตออโต้แบบคัดกรอง (ข้ามพอร์ตระบบ 1800 ที่ทำให้แอปเอ๋อ)
    for (const s of servicesList) {
        if (!s.characteristics) continue;

        const sUUID = s.uuid.toLowerCase();
        if (sUUID.includes("1800") || sUUID.includes("1801") || sUUID.includes("180a")) {
            continue; // ข้ามไปพอร์ตถัดไป
        }

        for (const c of s.characteristics) {
            if (c.properties.writeWithoutResponse || c.properties.write) {
                targetServiceUuid = s.uuid;
                targetCharacteristicUuid = c.uuid;
                break;
            }
        }
        if (targetServiceUuid) break;
    }

    // แผนสำรองสุดท้าย
    if (!targetServiceUuid || !targetCharacteristicUuid) {
        targetServiceUuid = "0000ffe0-0000-1000-8000-00805f9b34fb";
        targetCharacteristicUuid = "0000ffe1-0000-1000-8000-00805f9b34fb";
    }

    console.log("พบพอร์ตจริงของเครื่องพิมพ์คือ Service:", targetServiceUuid, "Char:", targetCharacteristicUuid);

    // 6. ส่งข้อมูลภาพที่หั่นเป็น Chunk ละ 512 บายต์ ออกไปทางบลูทูธความเร็วต่ำ (BLE)
    const CHUNK_SIZE = 64;

    for (let i = 0; i < binaryCommands.length; i += CHUNK_SIZE) {
        const chunk = binaryCommands.slice(i, i + CHUNK_SIZE);

        // แปลงอาร์เรย์ตัวเลขก้อนใหญ่ให้กลายเป็น Hex String
        let hexString = '';
        for (let j = 0; j < chunk.length; j++) {
            const hex = chunk[j].toString(16).padStart(2, '0');
            hexString += hex;
        }

        try {
            await BleClient.writeWithoutResponse({
                deviceId: deviceId,
                service: targetServiceUuid,
                characteristic: targetCharacteristicUuid,
                value: hexString
            });
        } catch (writeError) {
            console.error("จุดที่พังตอนยิงข้อมูล:", writeError);
            throw new Error("ยิงข้อมูลเข้าหัวพิมพ์ไม่สำเร็จ: " + writeError.message);
        }

        // 🟢 2. ลดเวลาหน่วงเหลือแค่ 5ms เพื่อให้เครื่องพิมพ์ทำงานได้ต่อเนื่องแบบรวดเร็ว
        // (ถ้าพิมพ์แล้วตัวหนังสือขาด ให้พี่ลองขยับเพิ่มเป็น 10 หรือ 15 ดูนะครับ แต่ 5 คือเร็วสะใจสุด)
        await new Promise(resolve => setTimeout(resolve, 2));
    }
}

// ⚡ ดักจับเหตุการณ์เมื่อจิ้มปุ่ม "ทดสอบการพิมพ์ (Test Print)"
const btnPrintTest = document.getElementById('btnPrintTest');
if (btnPrintTest) {
    btnPrintTest.addEventListener('click', async () => {
        try {
            await sendCanvasToPrinter();
            alert("✅ ส่งข้อมูลทดสอบระบบหัวพิมพ์เรียบร้อยแล้ว!");
        } catch (error) {
            console.error("Test print failed:", error);
            alert("❌ พิมพ์ทดสอบล้มเหลว: " + error.message + "\nกรุณาตรวจสอบว่าเปิดบลูทูธและต่อเครื่องพิมพ์แล้ว");
        }
    });
}



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



// --- Helper: UI Updates ---
function updateStatus(message, type = 'info') {
    console.log('mes', message)
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
        // connectButton.innerHTML = '<span class="material-icons-round">bluetooth_connected</span><span>เชื่อมต่อแล้ว</span>';
        const isConnected = localStorage.getItem('is_printer_connected') === 'true';
        const printerName = localStorage.getItem('connected_printer_name') || 'เครื่องพิมพ์';

        statusText.innerText = `🟢 เชื่อมต่ออยู่กับ: ${printerName}`;
        statusText.style.color = "green";

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
            } catch (e) { }
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
        updateGlobalPrinterStatus()

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
            const avg = (data[i] + data[i + 1] + data[i + 2]) / 3;
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
    console.log('printReceipt()')
    if (!printCharacteristic) {
        console.log('!printCharacteristic');
        updateStatus('กรุณาเชื่อมต่อก่อนพิมพ์', 'error');
        return;
    }

    // 🟢 ประกาศตัวแปรดึง DOM Elements ให้ครบถ้วนตรงนี้
    const printButton = document.getElementById('printImageButton');
    const printBtnText = document.getElementById('printBtnText');

    if (!printBtnText || !printButton) {
        console.error('ไม่พบปุ่มพิมพ์หรือข้อความปุ่มใน DOM');
        return;
    }

    const originalText = printBtnText.textContent;
    printButton.disabled = true;
    printBtnText.textContent = 'กำลังส่งข้อมูล...';

    try {
        // 1. HTML to Canvas
        const receiptElement = document.getElementById('qrcode_info');
        if (!receiptElement) {
            throw new Error('ไม่พบ Element #receipt-card ในหน้าเว็บ');
        }

        const canvas = await html2canvas(receiptElement, {
            scale: 2,
            useCORS: true,
            backgroundColor: '#ffffff'
        });

        // 2. Resize
        const printerWidth = 384; // 58mm thermal printer (384 dots)
        const scaleFactor = printerWidth / canvas.width;
        const printerHeight = Math.floor(canvas.height * scaleFactor);

        const printCanvas = document.createElement('canvas');
        printCanvas.width = printerWidth;
        printCanvas.height = printerHeight;
        const ctx = printCanvas.getContext('2d');

        // เทสีขาวป้องกันภาพพื้นหลังโปร่งใสกลายเป็นสีดำ
        ctx.fillStyle = '#ffffff';
        ctx.fillRect(0, 0, printerWidth, printerHeight);
        ctx.drawImage(canvas, 0, 0, printerWidth, printerHeight);

        // 3. Bitmap & Command (GS v 0)
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
        const CHUNK_SIZE = 170;
        for (let i = 0; i < dataToSend.length; i += CHUNK_SIZE) {
            const chunk = dataToSend.slice(i, i + CHUNK_SIZE);
            await printCharacteristic.writeValueWithoutResponse(chunk);
            await new Promise(r => setTimeout(r, 20));
        }

        // Feed Lines (0x0A = Line Feed)
        await printCharacteristic.writeValueWithoutResponse(new Uint8Array([0x0A, 0x0A, 0x0A]));
        updateStatus('พิมพ์เสร็จสิ้น', 'success');

    } catch (error) {
        // ป้องกันกรณี error.message ไม่มีค่า
        const errorMsg = error.message || error;
        updateStatus(`Error: ${errorMsg}`, 'error');
    } finally {
        printButton.disabled = false;
        printBtnText.textContent = originalText;
    }
}
// Initialize
connectButton.addEventListener('click', connectToPrinter);