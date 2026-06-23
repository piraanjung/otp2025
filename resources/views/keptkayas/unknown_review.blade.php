@extends('layouts.keptkaya')
@section('style')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        body { font-family: 'Prompt', sans-serif; background-color: #f8fafc; color: #334155; }
        .main-card { border: none; border-radius: 16px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05); }
        .table-img { width: 70px; height: 70px; object-fit: cover; border-radius: 10px; cursor: pointer; transition: transform 0.2s; border: 2px solid #e2e8f0; }
        .table-img:hover { transform: scale(1.1); }
        .badge-confidence { font-size: 0.85rem; padding: 5px 10px; border-radius: 20px; }
        .btn-action { border-radius: 10px; padding: 6px 12px; font-weight: 500; }
    </style>
@endsection
    
@section('content')
    

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1">🤖 Workspace ตรวจสอบสิ่งแปลกปลอม</h2>
            <p class="text-muted mb-0">จัดการรายการขยะแปลกปลอม (รหัส 0) จากตู้คีออส AIroBacT เพื่อตักเตือน หรือปรับปรุงยอดแต้มบิลหลัก</p>
        </div>
        <button class="btn btn-outline-secondary btn-action" onclick="fetchPendingItems()">
            <i class="fas fa-sync-alt"></i> รีเฟรชข้อมูล
        </button>
    </div>

    <div class="card main-card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="unknown-table">
                    <thead class="table-light">
                        <tr class="text-secondary" style="font-size: 0.95rem;">
                            <th class="ps-4">รูปภาพ</th>
                            <th>เลขที่บิลหลัก</th>
                            <th>รหัสผู้ใช้</th>
                            <th>AI Detect Label</th>
                            <th>ความมั่นใจ (AI)</th>
                            <th>วันที่-เวลา</th>
                            <th class="text-end pe-4">จัดการกระบวนการ</th>
                        </tr>
                    </thead>
                    <tbody id="items-list">
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <div class="spinner-border text-success mb-2" role="status"></div>
                                <div>กำลังสแกนตลับความจำสิ่งแปลกปลอม...</div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="verifyModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px;">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold"><i class="fas fa-edit text-success"></i> แก้ไขประเภทขยะให้ถูกต้อง</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <img id="modal-preview-img" src="" alt="ขยะหน้าตู้" class="img-fluid rounded-3" style="max-height: 200px; object-fit: contain;">
                    <div class="mt-2 text-muted small" id="modal-detected-hint"></div>
                </div>
                
                <input type="hidden" id="target-item-id">
                
                <div class="mb-3">
                    <label class="form-label fw-medium">เลือกประเภทกลุ่มขวดที่ถูกต้อง (เรทราคาในระบบ)</label>
                    <select class="form-select" id="correct-rate-select" style="border-radius: 10px;">
                        <option value="">-- กรุณาเลือกกลุ่มขวดขยะที่ถูกต้อง --</option>
                        <option value="1">ขวดน้ำดื่ม PET ขนาด 600 ml</option>
                        <option value="2">ขวดน้ำดื่ม PET ขนาด 1500 ml</option>
                        <option value="3">ขวดน้ำดื่ม PET ขนาด 250 ml</option>
                    </select>
                </div>
                
                <div class="mb-3">
                    <label class="form-label fw-medium">จำนวนชิ้น (Units)</label>
                    <input type="number" class="form-control" id="amount-units" value="1" min="1" style="border-radius: 10px;">
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light btn-action" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="button" class="btn btn-success btn-action" onclick="submitVerifyCorrection()">
                    <i class="fas fa-check"></i> บันทึกข้อมูลเข้าบิลหลัก
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
    
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // 🎯 ดึงพาร์ทโดยตรงจากระบบ Laravel (ไม่ต้องพิมพ์ URL เต็ม ป้องกันปัญหา Ngrok เปลี่ยนชื่อ)
    const API_BASE_URL = window.location.origin + '/api'; 
    let verifyModalObj = null;

    // เตรียมดึงค่าหัวสิทธิ์ CSRF Token สำหรับส่งคำสั่ง POST หา Laravel
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    document.addEventListener('DOMContentLoaded', () => {
        verifyModalObj = new bootstrap.Modal(document.getElementById('verifyModal'));
        fetchPendingItems();
    });

    async function fetchPendingItems() {
        const tbody = document.getElementById('items-list');
        try {
            console.log(`${API_BASE_URL}/kiosk/unknown-items/pending`)
            const response = await fetch(`${API_BASE_URL}/kiosk/unknown-items/pending`);

            const res = await response.json();
            
            if (!res.success || res.data.length === 0) {
                tbody.innerHTML = `<tr><td colspan="7" class="text-center py-5 text-muted">🎉 ไม่มีรายการสิ่งแปลกปลอมค้างตรวจสอบในระบบ</td></tr>`;
                return;
            }
            
            let html = '';
            res.data.forEach(item => {
                // อ้างอิงรูปภาพผ่านระบบ asset ของ Laravel public
                const imgSrc = item.image_path ? window.location.origin + '/' + item.image_path : 'https://placehold.co/150x150/e2e8f0/64748b?text=AIroBacT';
                const confClass = item.confidence_score >= 80 ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning';
                const createdDate = new Date(item.created_at).toLocaleString('th-TH');

                html += `
                    <tr>
                        <td class="ps-4">
                            <img src="${imgSrc}" class="table-img" onclick="previewImage('${imgSrc}')" title="ส่องดูขยะใบเต็ม">
                        </td>
                        <td class="fw-medium text-dark">#${item.kp_purchase_trans_id}</td>
                        <td><span class="badge bg-secondary-subtle text-secondary" style="font-size: 0.85rem;">${item.user_id_fk}</span></td>
                        <td class="text-monospace small text-primary fw-bold">${item.detected_label}</td>
                        <td>
                            <span class="badge badge-confidence ${confClass}">
                                <i class="fas fa-robot"></i> ${item.confidence_score}%
                            </span>
                        </td>
                        <td class="text-muted" style="font-size: 0.9rem;">${createdDate}</td>
                        <td class="text-end pe-4">
                            <button class="btn btn-outline-danger btn-sm btn-action me-2" onclick="triggerWarning(${item.id})">
                                <i class="fas fa-bell"></i> เตือนผู้ใช้
                            </button>
                            <button class="btn btn-success btn-sm btn-action" onclick="openVerifyModal(${JSON.stringify(item).replace(/"/g, '&quot;')})">
                                <i class="fas fa-magic"></i> แก้ข้อมูลขยะ
                            </button>
                        </td>
                    </tr>
                `;
            });
            tbody.innerHTML = html;
        } catch (error) {
            tbody.innerHTML = `<tr><td colspan="7" class="text-center py-5 text-danger">❌ ไม่สามารถดึงข้อมูลได้ กรุณาเช็คพาร์ท API</td></tr>`;
        }
    }

    function triggerWarning(id) {
        Swal.fire({
            title: 'ส่งข้อความแจ้งเตือน?',
            text: "ระบบจะแจ้งเตือนไปที่ User ว่าสิ่งที่หย่อนเข้ามาไม่ถูกต้อง ห้ามทำอีก!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            confirmButtonText: 'ใช่, ส่งเตือน',
            cancelButtonText: 'ยกเลิก'
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    const response = await fetch(`${API_BASE_URL}/kiosk/unknown-items/${id}/warn`, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' }
                    });
                    const res = await response.json();
                    if (res.success) {
                        Swal.fire('สำเร็จ!', 'ส่งระบบแจ้งเตือนเรียบร้อย', 'success');
                        fetchPendingItems();
                    }
                } catch (e) {
                    Swal.fire('พัง!', 'การเชื่อมต่อผิดพลาด', 'error');
                }
            }
        });
    }

    function openVerifyModal(item) {
        document.getElementById('target-item-id').value = item.id;
        const imgSrc = item.image_path ? window.location.origin + '/' + item.image_path : 'https://placehold.co/150x150/e2e8f0/64748b?text=AIroBacT';
        document.getElementById('modal-preview-img').src = imgSrc;
        document.getElementById('modal-detected-hint').innerText = `ชื่อคลาสเดิมที่ AI ตรวจจับ: ${item.detected_label}`;
        document.getElementById('correct-rate-select').value = "";
        document.getElementById('amount-units').value = 1;
        verifyModalObj.show();
    }

    async function submitVerifyCorrection() {
        const id = document.getElementById('target-item-id').value;
        const correctRateId = document.getElementById('correct-rate-select').value;
        const units = document.getElementById('amount-units').value;
        
        if (!correctRateId) {
            Swal.fire('คำเตือน', 'กรุณาเลือกประเภทขยะกลุ่มขวดที่ถูกต้องก่อนค่ะ', 'warning');
            return;
        }

        Swal.fire({ title: 'กำลังย้ายข้อมูลเข้าบิลหลัก...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

        try {
            const response = await fetch(`${API_BASE_URL}/kiosk/unknown-items/${id}/verify`, {
                method: 'POST',
                headers: { 
                    'X-CSRF-TOKEN': csrfToken, 
                    'Content-Type': 'application/json' 
                },
                body: JSON.stringify({ correctRateId: correctRateId, amount_in_units: units })
            });
            const res = await response.json();
            if (res.success) {
                verifyModalObj.hide();
                Swal.fire('สำเร็จ!', 'อัปเดตแต้มสะสมและย้ายเข้าตารางบิลหลักเรียบร้อยค่ะ', 'success');
                fetchPendingItems();
            } else {
                throw new Error(res.message);
            }
        } catch (e) {
            Swal.fire('ผิดพลาด', e.message, 'error');
        }
    }

    function previewImage(url) {
        Swal.fire({ imageUrl: url, imageWidth: 450, showConfirmButton: false, background: '#fff' });
    }
</script>
@endsection
