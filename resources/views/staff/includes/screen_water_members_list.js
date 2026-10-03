

let html5QrCodeForTabwater = null;

  async function loadWaterMembersList() {
    console.log('loadWaterMembersList()')
    const container = document.getElementById('membersListContainer');
    const subzoneSelectedRaw = localStorage.getItem('subzone_selected');
    if (!subzoneSelectedRaw){
        return;
    } ;
    const subzoneSelected = JSON.parse(subzoneSelectedRaw);

    if (document.getElementById("currentSubzoneTitle")) {
        document.getElementById("currentSubzoneTitle").innerText = `สาย: ${subzoneSelected.subzone.subzone_name || ''}`;
        document.getElementById("progressBadge").innerText =  `${subzoneSelected.members_status_invoice} / ${subzoneSelected.members}`;
    }

    if (container) {
        container.innerHTML = `<div class="text-center py-5"><div class="spinner-border text-primary"></div></div>`;
    }


    try {
        const response = await fetch(`${API_BASE_URL}/staff/tabwater/members`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'ngrok-skip-browser-warning': 'true'
            },
            body: JSON.stringify({ subzone_id: subzoneSelected.subzone_id })
        });

        const resData = await response.json();
        console.log('tabwater subzone members', resData)
        if (resData.code === 200) {
            renderMembersCardList(resData.data);
        } else {
            if (container) container.innerHTML = `<div class="text-center text-danger py-4">${resData.message}</div>`;
        }
    } catch (error) {
        console.error("Error loading members:", error);
        if (container) container.innerHTML = `<div class="text-center text-danger py-4">ไม่สามารถดึงข้อมูลสมาชิกได้</div>`;
    }
}

// 🟢 เปิด Modal สแกน QR Code
function openQrScannerTabwaterModal() {
    const modalEl = document.getElementById('qrScannerTabwaterModal');
    const qrModal = new bootstrap.Modal(modalEl);
    qrModal.show();

    // เริ่มทำงานกล้องเมื่อ Modal แสดงผล
    modalEl.addEventListener('shown.bs.modal', function () {
        if (!html5QrCodeForTabwater) {
            html5QrCodeForTabwater = new Html5Qrcode("qr-reader");
        }

        html5QrCodeForTabwater.start(
            { facingMode: "environment" }, // ใช้กล้องหลังมือถือ
            {
                fps: 10,
                qrbox: { width: 250, height: 250 }
            },
            (decodedText, decodedResult) => {
                console.log("Scanned QR Code:", decodedText);

                const searchInput = document.getElementById('searchMemberInput');
                if (searchInput) {
                    searchInput.value = decodedText;
                    searchInput.dispatchEvent(new Event('input'));
                }

                closeQrScannerTabwaterModal();
            },
            (errorMessage) => {
                // ข้าม Error ระหว่างที่ยังสแกนไม่ติด
            }
        ).catch(err => {
            console.error("ไม่สามารถเปิดกล้องได้:", err);
            alert("ไม่สามารถเปิดใช้งานกล้องได้ กรุณาตรวจสอบสิทธิ์การอนุญาตกล้อง");
        });
    }, { once: true });
}

// 🔴 ปิดกล้องและปิด Modal
function closeQrScannerTabwaterModal() {
    if (html5QrCodeForTabwater && html5QrCodeForTabwater.isScanning) {
        html5QrCodeForTabwater.stop().then(() => {
            const modalEl = document.getElementById('qrScannerTabwaterModal');
            const modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) modal.hide();
        }).catch(err => console.error("Error stopping scanner:", err));
    } else {
        const modalEl = document.getElementById('qrScannerTabwaterModal');
        const modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) modal.hide();
    }
}

// 🔍 Event Listener สำหรับกรองข้อมูลในหน้ารายชื่อ
document.getElementById('searchMemberInput')?.addEventListener('input', function (e) {
    console.log('xxxx');
    const keyword = e.target.value.toLowerCase().trim();
    const cardItems = document.querySelectorAll('.member-card-item');

    cardItems.forEach(card => {
        const textContent = card.innerText.toLowerCase();
        if (textContent.includes(keyword)) {
            card.classList.remove('d-none');
        } else {
            card.classList.add('d-none');
        }
    });
});

function renderMembersCardList(members) {
    console.log('xx');
    const container = document.getElementById('membersListContainer');
    if (!container) return;

    container.innerHTML = '';

    if (!members || members.length === 0) {
        container.innerHTML = `<div class="text-center text-muted py-4">ไม่พบรายชื่อสมาชิกในสายนี้</div>`;
        return;
    }

    members.forEach((item) => {
        const user = item.user || {};
        const userName = `${user.firstname || ''} ${user.lastname || ''}`.trim() || 'ไม่ระบุชื่อ';
        const address = item.meter_address || 'ไม่ระบุบ้านเลขที่';
        const lastMeter = item.last_meter_recording || 0;

        const currentInvoice = (item.invoice_currrent_inv_period && item.invoice_currrent_inv_period.length > 0)
            ? item.invoice_currrent_inv_period[0]
            : null;

        const invoiceId = currentInvoice ? currentInvoice.id : null;
        const isRecorded = currentInvoice && currentInvoice.status !== 'init';

        const statusBadge = isRecorded
            ? `<span class="badge bg-success rounded-pill">จดแล้ว (${currentInvoice.currentmeter})</span>`
            : `<span class="badge bg-warning text-dark rounded-pill">ยังไม่จด</span>`;

        const cardHtml = `
            <div class="col-12 member-card-item mb-2">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <span class="fw-bold fs-5 text-primary">🏠 ${address}</span>
                                <h6 class="mb-0 text-dark fw-bold mt-1">${userName}</h6>
                            </div>
                            <div>${statusBadge}</div>
                        </div>

                        <div class="bg-light rounded-3 p-2 mb-3 d-flex justify-content-between align-items-center">
                            <small class="text-muted">เลขมิเตอร์เดิม: <strong class="text-dark">${lastMeter}</strong></small>
                            <small class="text-muted">รหัสมิเตอร์: <strong class="text-dark">${item.meternumber || '-'}</strong></small>
                        </div>

                        <button onclick="openRecordMeterModal(${item.id}, '${userName}', ${lastMeter}, ${invoiceId})" 
                                class="btn ${isRecorded ? 'btn-outline-secondary' : 'btn-primary'} w-100 rounded-pill fw-bold">
                            ${isRecorded ? '✏️ แก้ไขเลขมิเตอร์ป' : '📝 บันทึกเลขมิเตอร์'}
                        </button>
                    </div>
                </div>
            </div>
        `;
        container.innerHTML += cardHtml;
    });
}

// 🟢 ฟังก์ชันคำนวณ หน่วยน้ำประปาที่ใช้
function calculateWaterUsage() {
    console.log('calculateWaterUsage', $('#modalLastMeterInput').val());

    const lastMeterVal = parseFloat(document.getElementById('modalLastMeterInput')?.value) || 0;
    const currentMeterVal = parseFloat(document.getElementById('modalCurrentMeterInput')?.value) || 0;
    console.log('lastMeterVal', lastMeterVal);
    console.log('currentMeterVal', currentMeterVal);

    let usage = currentMeterVal - lastMeterVal;
    console.log('usage', usage);
    if (usage < 0) {
        usage = 0;
    }

    const usageEl = document.getElementById('modalWaterUsageText');
    if (usageEl) {
        console.log('[]usage', usage);
        usageEl.innerText = usage.toFixed(2);
    }

    return usage;
}

let cropper = null;

// 🟢 ฟังก์ชันเปิดกล้อง/เลือกรูป
function triggerMeterCamera() {
    const input = document.getElementById('meterCameraInput');
    if (input) input.click();
}

// 🟢 ฟังก์ชันจัดการเมื่อเลือกรูปแล้วโหลดใส่ Cropper.js
function handleImageCapture(event) {
    const file = event.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = function (e) {
        const image = document.getElementById('imageToCrop');
        if (!image) return;

        image.src = e.target.result;

        const cropContainer = document.getElementById('cropContainer');
        const btnProcessOcr = document.getElementById('btnProcessOcr');
        if (cropContainer) cropContainer.classList.remove('d-none');
        if (btnProcessOcr) btnProcessOcr.classList.remove('d-none');

        if (cropper) {
            cropper.destroy();
        }

        cropper = new Cropper(image, {
            viewMode: 1,
            autoCropArea: 0.6,
            movable: true,
            zoomable: true,
            rotatable: false,
            scalable: false
        });
    };
    reader.readAsDataURL(file);
}

// 🟢 ฟังก์ชันปรับภาพเป็น ขาว-ดำ (Black & White Thresholding) ก่อนเข้า OCR
function preprocessCanvasForOcr(sourceCanvas) {
    const canvas = document.createElement('canvas');
    const ctx = canvas.getContext('2d');

    canvas.width = sourceCanvas.width * 2;
    canvas.height = sourceCanvas.height * 2;

    ctx.drawImage(sourceCanvas, 0, 0, canvas.width, canvas.height);

    const imgData = ctx.getImageData(0, 0, canvas.width, canvas.height);
    const data = imgData.data;

    for (let i = 0; i < data.length; i += 4) {
        const avg = (data[i] + data[i + 1] + data[i + 2]) / 3;
        const threshold = 130;
        const color = avg > threshold ? 255 : 0;

        data[i] = color;
        data[i + 1] = color;
        data[i + 2] = color;
    }

    ctx.putImageData(imgData, 0, 0);
    return canvas;
}

// 🟢 ปรับปรุงฟังก์ชันอ่าน OCR
async function processCroppedOcrWithClientSide() {
    if (!cropper) {
        alert('กรุณาเลือกพื้นที่ตัวเลขบนรูปภาพก่อนครับ');
        return;
    }

    const btn = document.getElementById('btnProcessOcr');
    const originalBtnText = btn ? btn.innerText : '🔍 อ่านตัวเลขจากพื้นที่ที่เลือก';

    const rawCanvas = cropper.getCroppedCanvas({ width: 400 });
    const processedCanvas = preprocessCanvasForOcr(rawCanvas);

    if (btn) {
        btn.innerText = '⏳ กำลังประมวลผลตัวเลข...';
        btn.disabled = true;
    }

    try {
        const worker = await Tesseract.createWorker('eng');

        await worker.setParameters({
            tessedit_char_whitelist: '0123456789',
            tessedit_pageseg_mode: Tesseract.PSM.SINGLE_LINE,
        });

        const { data: { text } } = await worker.recognize(processedCanvas);
        await worker.terminate();

        const cleanedNumber = text.replace(/[^0-9]/g, '');
        console.log("OCR Result:", cleanedNumber);

        if (cleanedNumber) {
            const input = document.getElementById('modalCurrentMeterInput');
            if (input) {
                input.value = cleanedNumber;
                if (typeof calculateWaterUsage === 'function') calculateWaterUsage();
            }
        } else {
            alert('อ่านตัวเลขไม่ชัดเจน กรุณากรอกตัวเลขด้วยตนเอง');
        }
    } catch (err) {
        console.error('Client-side OCR Error:', err);
        alert('เกิดข้อผิดพลาดในการประมวลผลรูปภาพ');
    } finally {
        if (btn) {
            btn.innerText = originalBtnText;
            btn.disabled = false;
        }
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const digits = document.querySelectorAll('.meter-digit-input');

    digits.forEach((digit, index) => {
        digit.addEventListener('focus', function () {
            this.select();
        });

        digit.addEventListener('input', function (e) {
            this.value = this.value.replace(/[^0-9]/g, '');

            if (this.value.length >= 1) {
                this.value = this.value.slice(-1);

                if (index < digits.length - 1) {
                    setTimeout(() => {
                        digits[index + 1].focus();
                        digits[index + 1].select();
                    }, 10);
                }
            }

            updateMeterDigitsAndCalculate();
        });

        digit.addEventListener('keydown', function (e) {
            if (e.key === 'Backspace') {
                if (!this.value && index > 0) {
                    digits[index - 1].focus();
                    digits[index - 1].select();
                } else {
                    this.value = '';
                    updateMeterDigitsAndCalculate();
                }
            }
        });
    });
});

// 🟢 ฟังก์ชันรวมค่าตัวเลข 4 ช่อง
function updateMeterDigitsAndCalculate() {
    const d1 = document.getElementById('digit1')?.value || '0';
    const d2 = document.getElementById('digit2')?.value || '0';
    const d3 = document.getElementById('digit3')?.value || '0';
    const d4 = document.getElementById('digit4')?.value || '0';

    const combinedValue = parseInt(`${d1}${d2}${d3}${d4}`, 10);

    const mainInput = document.getElementById('modalCurrentMeterInput');
    if (mainInput) {
        mainInput.value = combinedValue;
    }

    if (typeof calculateWaterUsage === 'function') {
        calculateWaterUsage();
    }
}

// 🟢 ฟังก์ชันเปิด Modal สำหรับบันทึก/แก้ไขเลขมิเตอร์ (รวมเป็นฟังก์ชันเดียว)
function openRecordMeterModal(meterId, userName, lastMeter, invoiceId) {
    window.selectedMeterId = meterId;
    window.selectedInvoiceId = invoiceId; // 🟢 เก็บ invoiceId ไว้ใช้งานตอนบันทึก

    if (document.getElementById('modalMeterUserName')) {
        document.getElementById('modalMeterUserName').innerText = userName;
    }
    if (document.getElementById('modalLastMeterInput')) {
        document.getElementById('modalLastMeterInput').value = lastMeter;
    }
    ['digit1', 'digit2', 'digit3', 'digit4'].forEach(id => {
        const input = document.getElementById(id);
        if (input) input.value = '0';
    });

    if (document.getElementById('modalCurrentMeterInput')) {
        document.getElementById('modalCurrentMeterInput').value = 0;
    }

    const recordModalEl = document.getElementById('recordMeterModal');
    if (recordModalEl) {
        const modal = bootstrap.Modal.getOrCreateInstance(recordModalEl);
        modal.show();
    }
}

// 🟢 ฟังก์ชันบันทึกข้อมูลเลขมิเตอร์ (ประมวลผลค่าจาก 4 ช่อง)
async function saveMeterRecord(isPrint = false) {
    console.log('window.selectedInvoiceId',)
    const invoiceId = window.selectedInvoiceId;
    const currentMeter = document.getElementById('modalCurrentMeterInput')?.value;
    const lastMeter = document.getElementById('modalLastMeterInput')?.value;
    if (!invoiceId) {
        alert('ไม่พบข้อมูลใบแจ้งหนี้รอบปัจจุบัน');
        return;
    }

    if (currentMeter === '' || currentMeter === null) {
        alert('กรุณากรอกเลขมิเตอร์ครั้งนี้');
        return;
    }

    if (parseInt(currentMeter, 10) < parseInt(lastMeter || 0, 10)) {
        if (!confirm('เลขมิเตอร์ครั้งนี้น้อยกว่าครั้งก่อน คุณแน่ใจหรือไม่ที่จะบันทึก?')) {
            return;
        }
    }

    const btnSave = document.getElementById('btnSaveMeterRecord');
    if (btnSave) {
        btnSave.disabled = true;
        btnSave.innerText = '⏳ กำลังบันทึก...';
    }

    try {


        const response = await fetch(`${API_BASE_URL}/staff/tabwater/meter-records`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            },
            body: JSON.stringify({
                invoice_id: invoiceId, // 🟢 ส่ง invoice_id ไปประมวลผลแทน
                current_reading: parseInt(currentMeter, 10)
            })
        });

        const resData = await response.json();

        if (response.ok && (resData.code === 200 || resData.success)) {

            let recieptText = generateWaterBillHTML(resData.data);

            $('#card-reciept').html(recieptText);

            if (isPrint) {
                if (typeof printReceipt === "function") {
                    await printReceipt();
                }
            }

            const recordModalEl = document.getElementById('recordMeterModal');
            if (recordModalEl) {
                const modal = bootstrap.Modal.getInstance(recordModalEl);
                if (modal) modal.hide();
            }
            console.log('renderMembersListUI()')
            // if (typeof renderMembersListUI === 'function') {
            //     renderMembersListUI();
            // } else {
            //     location.reload();
            // }
        } else {
            alert(resData.message || 'เกิดข้อผิดพลาดในการบันทึกข้อมูล');
        }
    } catch (err) {
        console.error('Save Meter Record Error:', err);
        alert('เกิดข้อผิดพลาดในการเชื่อมต่อเซิร์ฟเวอร์');
    } finally {
        if (btnSave) {
            btnSave.disabled = false;
            btnSave.innerText = '💾 บันทึกข้อมูล';
        }
    }
}

// Event Listeners
$('#btn-save-only').on('click', function() {
    saveMeterRecord(false); // บันทึกอย่างเดียว
});

$('#btn-save-and-print').on('click', function() {
    BluethoothConnectedModal('tabwater');
    saveMeterRecord(true); // บันทึกพร้อมพิมพ์
});

async function test2(){
    console.log('sssssss')
     try {


        


        //  if (response.ok && (resData.code === 200 || resData.success)) {

            let recieptText = generateWaterBillHTML2('xx');
            console.log('ssss')
            $('#card-reciept').html(recieptText);

            if (isPrint) {
                if (typeof printReceipt === "function") {
                    await printReceipt();
                }
            }

            const recordModalEl = document.getElementById('recordMeterModal');
            if (recordModalEl) {
                const modal = bootstrap.Modal.getInstance(recordModalEl);
                if (modal) modal.hide();
            }
            if (typeof renderMembersListUI === 'function') {
                renderMembersListUI();
            } else {
                location.reload();
            }
        // } else {
        //     alert(resData.message || 'เกิดข้อผิดพลาดในการบันทึกข้อมูล');
        // }
    } catch (err) {
        console.error('Save Meter Record Error:', err);
        alert('เกิดข้อผิดพลาดในการเชื่อมต่อเซิร์ฟเวอร์');
    } finally {
        if (btnSave) {
            btnSave.disabled = false;
            btnSave.innerText = '💾 บันทึกข้อมูล';
        }
    }
}

// test2();

async function generateWaterBillHTML2(data='2') {
    console.log('dsssa',data)
    console.log('dsxxxxssa',data)
    // const recordDate = formatThaiDate(new Date(), 'short');
    // const expireDate = formatThaiDate(data.expire_date || new Date(Date.now() + 15 * 24 * 60 * 60 * 1000));

    let oweHtml = '';
oweHtml = `
    <div class="border-top border-dark border-1 my-1"></div>
    <div class="fw-bold style-subhead">รายการค้างชำระเดิม:</div>
    <ul class="list-unstyled mb-1 ps-1 style-body">
        <li class="d-flex justify-content-between"><span>09/68</span> <span class="">10.70 บาท</span></li>
        <li class="d-flex justify-content-between"><span>10/68</span> <span class="">12.54 บาท</span></li>
        <li class="d-flex justify-content-between"><span>11/68</span> <span class="">23.00 บาท</span></li>
    </ul>
`;

let org_img = window.ASSET_URL ? window.ASSET_URL + "logo/hs_logo.png" : "/logo/hs_logo.png";

$('#qrcode_info').html( `
<style>
    /* CSS สำหรับควบคุมการพิมพ์ลงกระดาษความร้อน 58mm */
    
    #qrcode_info{
        font-family: "Kanit", sans-serif;
    }
    /* สไตล์ขนาดฟอนต์ระดับต่างๆ ที่ขยายให้ใหญ่ขึ้น */
    .style-title { font-size: 2.5rem; font-weight: bold; }
    .style-header { font-size: 2rem; font-weight: bold; }
    .style-subhead { font-size: 1.7rem; font-weight: bold; }
    .style-body { font-size:  1.7rem; }
    .style-small { font-size: 1.5rem }
    .logo{
        width:200px;
        height:200px;
    }

   
</style>

<div class="receipt-container">
    
    <!-- Header/Logo Section -->
    <div class="text-center mb-1">
        <img src="${org_img}" class="img-fluid mb-1 logo" alt="Logo">   
        <div class="style-title">เทศบาลตำบลห้องแซง</div>
        <div class="style-small">124 หมู่ 19 ต.ห้องแซง อ.เลิงนกทา จ.ยโสธร</div>
        <div class="style-small">โทร. 045777123</div>
    </div>

    <!-- Title Section -->
    <div class="text-center my-1">
        <div class="style-header">ใบแจ้งหนี้ / ใบชำระค่าน้ำประปา</div>
        <div class="style-body">รอบบิล: 12/68 | เลขที่: 123456</div>
    </div>

    <div class="border-top border-dark border-1 my-1"></div>

    <!-- User Info Section -->
    <div class="style-body mb-1">
        <div class="d-flex justify-content-between">
            <span class="text-nowrap">ผู้ใช้น้ำ:</span>
            <span class="fw-bold text-end">นายวิเชียร เมืองพิล</span>
        </div>
        <div class="d-flex justify-content-between">
            <span class="text-nowrap">เลขผู้ใช้:</span>
            <span class="text-end">user000101</span>
        </div>
        <div class="d-flex justify-content-between">
            <span class="text-nowrap">เลขมิเตอร์:</span>
            <span class="text-end">01-83230232</span>
        </div>
        <div class="d-flex justify-content-between">
            <span class="text-nowrap">วันที่จด:</span>
            <span class="text-end">12 ธ.ค. 2568</span>
        </div>
    </div>

    <div class="border-top border-dark border-1 my-1"></div>

    <!-- Meter Usage Section -->
    <div class="style-body">
        <div class="d-flex justify-content-between">
            <span>เลขครั้งก่อน:</span>
            <span>100</span>
        </div>
        <div class="d-flex justify-content-between">
            <span>เลขครั้งนี้:</span>
            <span>120</span>
        </div>
        <div class="d-flex justify-content-between fw-bold style-subhead">
            <span>ปริมาณที่ใช้:</span>
            <span>20 หน่วย</span>
        </div>
    </div>

    <div class="border-top border-dark border-1 my-1 col-5" style=" margin-left:63%"></div>

    <!-- Bill Details Section -->
    <div class="style-body">
        <div class="d-flex justify-content-between">
            <span>ค่าน้ำประปา:</span>
            <span>160.00</span>
        </div>
        <div class="d-flex justify-content-between">
            <span>ค่าบริการมิเตอร์:</span>
            <span>0.00</span>
        </div>
        <div class="d-flex justify-content-between">
            <span>ภาษี (VAT):</span>
            <span>3.60</span>
        </div>
        <div class="d-flex justify-content-between fw-bold style-subhead pt-1">
            <span>รวมเป็นเงิน:</span>
            <span>163.60</span>
        </div>
    </div>

    ${oweHtml}

    <div class="border-top border-dark border-2 my-1"></div>

    <!-- Total Section -->
    <div class="d-flex justify-content-between align-items-center fw-bold style-header my-1">
        <span>รวมเงินทั้งสิ้น:</span>
        <span style="font-size: 18px;">263.60 บาท</span>
    </div>

    <!-- QR Code Section -->
    <div class="text-center my-2">
        <div class="fw-bold style-body">สแกนเพื่อชำระเงิน</div>
        <div id="qrcode" class="d-flex justify-content-center my-1"></div>
    </div>

    <div class="text-center p-1 border border-dark rounded my-1 style-body">
        <span class="fw-bold">กำหนดชำระภายใน:</span> 10 ม.ค. 2569
    </div>

    <div class="text-center style-small mt-1">
        *** กรุณาชำระตามกำหนด ***
    </div>
</div>
`);
    buildReceiptHtml()
      if (typeof printReceipt === "function") {
                    await printReceipt();
                }

}
function generateWaterBillHTML(data) {
    console.log('da',data)
    const recordDate = formatThaiDate(new Date(), 'short');
    const expireDate = formatThaiDate(data.expire_date || new Date(Date.now() + 15 * 24 * 60 * 60 * 1000));

    let oweHtml = '';
    if (data.owe_list && data.owe_list.length > 0) {
        oweHtml = `
            <div style="border-top: 1px dotted #000; margin: 4px 0;"></div>
            <div style="font-weight: bold; font-size: 11px;">รายการค้างชำระเดิม:</div>
            <ul style="padding-left:15px; margin:2px 0; font-size:11px;">`;
        data.owe_list.forEach(item => {
            oweHtml += `<li>${item.period_name} <span style="float:right;">${parseFloat(item.totalpaid).toFixed(2)} บาท</span></li>`;
        });
        oweHtml += `</ul>`;
    }

    return `
    <div style="width: 350px; padding: 10px; background: #ffffff; color: #000000; font-family: 'Tahoma', sans-serif; font-size: 12px; line-height: 1.3;">
        <div style="text-align: center; margin-bottom: 6px;">
            <b style="font-size: 14px;">ใบแจ้งหนี้ / ใบชำระค่าน้ำประปา</b><br>
            <span>ประจำงวด: ${data.inv_period_name || '-'}</span><br>
            <span>เลขที่: ${data.invoice_id || data.inv_no || '-'}</span>
        </div>

        <div style="border-top: 1px dashed #000; margin: 4px 0;"></div>

        <div>
            <b>ผู้ใช้น้ำ:</b> ${data.user_name || '-'}<br>
            <b>เลขผู้ใช้:</b> ${data.user_code || data.meter_id || '-'}<br>
            <b>เลขมิเตอร์:</b> ${data.meternumber || '-'}<br>
            <b>วันที่จด:</b> ${recordDate}
        </div>

        <div style="border-top: 1px dashed #000; margin: 4px 0;"></div>

        <table style="width: 100%; font-size: 12px; border-collapse: collapse;">
            <tr><td>เลขครั้งก่อน:</td><td style="text-align: right;">${data.lastmeter}</td></tr>
            <tr><td>เลขครั้งนี้:</td><td style="text-align: right;">${data.currentmeter}</td></tr>
            <tr><td><b>ปริมาณที่ใช้:</b></td><td style="text-align: right;"><b>${data.water_used} หน่วย</b></td></tr>
        </table>

        <div style="border-top: 1px dashed #000; margin: 4px 0;"></div>

        <table style="width: 100%; font-size: 12px; border-collapse: collapse;">
            <tr><td>ค่าน้ำประปา:</td><td style="text-align: right;">${parseFloat(data.water_charge || 0).toFixed(2)}</td></tr>
            <tr><td>ค่าบริการมิเตอร์:</td><td style="text-align: right;">${parseFloat(data.reserve_meter || 0).toFixed(2)}</td></tr>
            <tr><td>ภาษี (VAT):</td><td style="text-align: right;">${parseFloat(data.vat || 0).toFixed(2)}</td></tr>
        </table>

        ${oweHtml}

        <div style="border-top: 2px solid #000; margin: 6px 0;"></div>

        <table style="width: 100%; font-size: 14px; border-collapse: collapse;">
            <tr><td><b>รวมเงินทั้งสิ้น:</b></td><td style="text-align: right;"><b>${parseFloat(data.totalpaid || 0).toFixed(2)} บาท</b></td></tr>
        </table>

        <div style="margin-top: 6px; font-size: 11px;">
            <b>กำหนดชำระภายใน:</b> ${expireDate}
        </div>

        <div style="text-align: center; margin-top: 10px; font-size: 11px;">
            *** กรุณาชำระตามกำหนด ***<br>
            ขอบคุณที่ใช้บริการ
        </div>
    </div>`;
}


function formatThaiDate(dateInput, mode = 'short') {
    if (!dateInput) return '-';

    const date = new Date(dateInput);
    if (isNaN(date.getTime())) return '-';

    const thaiMonthsShort = [
        'ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.',
        'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'
    ];

    const day = date.getDate();
    const month = thaiMonthsShort[date.getMonth()];
    const year = date.getFullYear() + 543; // แปลงเป็น ค.ศ. -> พ.ศ.

    return `${day} ${month} ${year}`;
}