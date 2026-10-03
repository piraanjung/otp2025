<!-- 🟢 หน้ารายชื่อสมาชิก (ย้าย Search Box + ปุ่ม QR เข้ามาข้างในนี้) -->
<!-- Header Summary -->
<div id="globalPrinterStatus"
    style="background: #fff; padding: 10px 15px; margin-bottom: 15px; border-radius: 8px; border-left: 5px solid #dc3545; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
    <div style="font-size: 14px; font-weight: bold; color: #495057;">
        🖨️ สถานะเครื่องพิมพ์: <span id="lblGlobalPrinterName" style="color: #dc3545;">🔴
            ยังไม่ได้เชื่อมต่อ</span>
    </div>
    <span id="lblGlobalPrinterIndicator"
        style="width: 12px; height: 12px; background: #dc3545; border-radius: 50%;"></span>
</div>
<div class="card border-0 shadow-sm rounded-3 mb-3 bg-primary text-white">
    <div class="card-body p-3">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h6 class="mb-0 text-white-50" id="currentSubzoneTitle"></h6>
                <h4 class="fw-bold mb-0">รายการจดมิเตอร์น้ำ</h4>
            </div>
            <span class="badge bg-white text-primary px-3 py-2 fs-6 rounded-pill" id="progressBadge">0 / 2</span>
        </div>
    </div>
</div>
<div class="mb-3 text-center">
    <button type="button" class="btn btn-outline-primary w-100 mb-2" onclick="triggerMeterCamera()">
        📷 ถ่ายรูปมิเตอร์เพื่ออ่านตัวเลข
    </button>
    <input type="file" id="meterCameraInput" accept="image/*" capture="environment" class="d-none"
        onchange="handleImageCapture(event)">

    <!-- พื้นที่แสดงรูปภาพสำหรับลาก Crop -->
    <div id="cropContainer" class="d-none mt-2" style="max-height: 300px; overflow: hidden;">
        <img id="imageToCrop" src="" style="max-width: 100%;">
    </div>

    <button type="button" id="btnProcessOcr" class="btn btn-sm btn-success w-100 mt-2 d-none"
        onclick="processCroppedOcrWithClientSide()">
        🔍 อ่านตัวเลขจากพื้นที่ที่เลือก
    </button>
</div>

<!-- Search Box + ปุ่ม QR Code (รวมเป็นชุดเดียว) -->
<div class="row g-2 mb-3">
    <div class="col">
        <input type="text" id="searchMemberInput" class="form-control form-control-lg rounded-3 border-0 shadow-sm"
            placeholder="🔍 ค้นหาบ้านเลขที่, ชื่อ หรือเลขมิเตอร์...">
    </div>
    <div class="col-auto">
        <button type="button" class="btn btn-primary btn-lg rounded-3 shadow-sm px-3"
            onclick="openQrScannerTabwaterModal()">
            📷 สแกน QR
        </button>
    </div>
</div>

<!-- Container สำหรับ Render รายการสมาชิก -->
<div id="membersListContainer" class="row g-2">
    <!-- JS จะ Render การ์ดสมาชิกใส่ตรงนี้ -->
</div>


<!-- 🟢 Modal หน้าจอกล้องสแกน QR Code (วางไว้นอก Screen หลักได้) -->
<div class="modal fade" id="qrScannerTabwaterModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">📷 สแกน QR Code มิเตอร์/สมาชิก</h5>
                <button type="button" class="btn-close" onclick="closeQrScannerTabwaterModal()"></button>
            </div>
            <div class="modal-body p-3 text-center">
                <!-- พื้นที่แสดงกล้องสแกน -->
                <div id="qr-reader" style="width: 100%; max-width: 400px; margin: 0 auto;"></div>
                <small class="text-muted d-block mt-2">ส่องกล้องไปที่ QR Code บนป้ายมิเตอร์ หรือการ์ดสมาชิก</small>
            </div>
        </div>
    </div>
</div>

<!-- Modal สำหรับกรอก/บันทึกเลขมิเตอร์ -->
<div class="modal fade" id="recordMeterModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0">
            <div class="modal-header bg-primary text-white border-0">
                <h5 class="modal-title fw-bold">📝 บันทึกเลขมิเตอร์น้ำ</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label text-muted mb-0">ชื่อสมาชิก</label>
                    <h5 class="fw-bold text-dark" id="modalMeterUserName">-</h5>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label text-muted">เลขจดครั้งก่อน</label>
                        <input type="text" id="modalLastMeterInput" class="form-control bg-light">
                    </div>

                </div>


                <div class="mb-3 text-center">
                    <label class="form-label fw-bold text-primary mb-2">เลขจดครั้งนี้ (แตะช่องที่ต้องการเริ่มพิมพ์)
                        *</label>

                    <div class="d-flex justify-content-center gap-2" id="meterDigitContainer">
                        <input type="number" maxlength="1"
                            class="form-control form-control-lg text-center fw-bold fs-3 meter-digit-input" id="digit1"
                            data-index="0" autocomplete="off">
                        <input type="number" pattern="[0-9]*"  maxlength="1"
                            class="form-control form-control-lg text-center fw-bold fs-3 meter-digit-input" id="digit2"
                            data-index="1" autocomplete="off">
                        <input type="number" pattern="[0-9]*"  maxlength="1"
                            class="form-control form-control-lg text-center fw-bold fs-3 meter-digit-input" id="digit3"
                            data-index="2" autocomplete="off">
                        <input type="number" pattern="[0-9]*"  maxlength="1"
                            class="form-control form-control-lg text-center fw-bold fs-3 meter-digit-input" id="digit4"
                            data-index="3" autocomplete="off">
                    </div>

                    <!-- Hidden Input สำหรับรวมค่าส่งประมวลผล -->
                    <input type="hidden" id="modalCurrentMeterInput" value="0">

                    <!-- Hidden Input สำหรับรับ Focus คีย์บอร์ดมือถือ และเป็นตัวรับการพิมพ์ -->
                    <input type="number" pattern="[0-9]*"  id="hiddenMeterInput"
                        style="opacity: 0; position: absolute; left: -9999px;" autocomplete="off">

                    <input type="hidden" id="modalCurrentMeterInput" value="0">
                    <small class="text-muted">แตะที่กล่องตัวเลข แล้วพิมพ์เลขมิเตอร์ได้เลย</small>
                </div>

                <!-- สรุปจำนวนหน่วยที่ใช้ -->
                <div
                    class="alert alert-info d-flex justify-content-between align-items-center py-2 px-3 rounded-3 mb-3">
                    <span class="mb-0">ปริมาณน้ำที่ใช้:</span>
                    <h5 class="fw-bold mb-0 text-primary"><span id="modalWaterUsageText">0</span> ลบ.ม. (หน่วย)</h5>
                </div>

                <!-- ปุ่มบันทึกเฉยๆ -->
                <button type="button" id="btn-save-only" class="btn btn-primary">
                    <i class="fas fa-save"></i> บันทึกข้อมูล
                </button>

                <!-- ปุ่มบันทึกพร้อมพิมพ์ -->
                <button type="button" id="btn-save-and-print" class="btn btn-success">
                    <i class="fas fa-print"></i> บันทึกและพิมพ์ใบแจ้งหนี้
                </button>
            </div>
        </div>
    </div>
</div>