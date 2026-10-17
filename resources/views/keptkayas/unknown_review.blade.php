@extends('layouts.keptkaya')
@section('nav-header', 'ตั้งค่าข้อมูล')
@section('page-topic', 'ตรวจสอบวัตถุ Unknown')
@section('style')
    <style>
        .table-img { width: 56px; height: 56px; object-fit: cover; cursor: pointer; transition: transform 0.2s; }
        .table-img:hover { transform: scale(1.1); }
    </style>
@endsection

@section('content')
<div class="card mb-4">
    <div class="card-header pb-0">
        <div class="d-lg-flex justify-content-between align-items-center">
            <div class="mb-lg-0 mb-3">
                <h6 class="mb-1"><i class="fas fa-robot text-primary me-2"></i>Workspace ตรวจสอบสิ่งแปลกปลอม</h6>
                <p class="text-sm text-secondary mb-0">จัดการรายการขยะแปลกปลอม (รหัส 0) จากตู้คีออส AIroBacT เพื่อตักเตือน หรือปรับปรุงยอดแต้มบิลหลัก</p>
            </div>
            <button type="button" class="btn btn-outline-secondary btn-sm mb-0" onclick="fetchPendingItems()">
                <i class="fas fa-sync-alt me-1"></i> รีเฟรชข้อมูล
            </button>
        </div>
    </div>
    <div class="card-body px-0 pt-3 pb-2">
        <div class="table-responsive p-0">
            <table class="table align-items-center mb-0" id="unknown-table">
                <thead>
                    <tr>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-4">รูปภาพ</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">เลขที่บิลหลัก</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">รหัสผู้ใช้</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">AI Detect Label</th>
                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ความมั่นใจ (AI)</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">วันที่-เวลา</th>
                        <th class="text-secondary opacity-7"></th>
                    </tr>
                </thead>
                <tbody id="items-list">
                    <tr>
                        <td colspan="7" class="text-center py-5 text-secondary text-sm">
                            <div class="spinner-border text-success mb-2" role="status"></div>
                            <div>กำลังโหลดรายการสิ่งแปลกปลอม...</div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="verifyModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title"><i class="fas fa-edit text-success me-1"></i> แก้ไขประเภทขยะให้ถูกต้อง</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <img id="modal-preview-img" src="" alt="ขยะหน้าตู้" class="img-fluid border-radius-lg" style="max-height: 200px; object-fit: contain;">
                    <div class="mt-2 text-secondary text-sm" id="modal-detected-hint"></div>
                </div>
                
                <input type="hidden" id="target-item-id">
                
                <div class="mb-3">
                    <label class="form-label">เลือกประเภทกลุ่มขวดที่ถูกต้อง (เรทราคาในระบบ)</label>
                    <select class="form-select" id="correct-rate-select">
                        <option value="">-- กรุณาเลือกกลุ่มขวดขยะที่ถูกต้อง --</option>
                        <option value="1">ขวดน้ำดื่ม PET ขนาด 600 ml</option>
                        <option value="2">ขวดน้ำดื่ม PET ขนาด 1500 ml</option>
                        <option value="3">ขวดน้ำดื่ม PET ขนาด 250 ml</option>
                    </select>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">จำนวนชิ้น (Units)</label>
                    <input type="number" class="form-control" id="amount-units" value="1" min="1">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary mb-0" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="button" class="btn bg-gradient-success mb-0" onclick="submitVerifyCorrection()">
                    <i class="fas fa-check"></i> บันทึกข้อมูลเข้าบิลหลัก
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
    
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
                tbody.innerHTML = `<tr><td colspan="7" class="text-center py-5 text-secondary text-sm">🎉 ไม่มีรายการสิ่งแปลกปลอมค้างตรวจสอบในระบบ</td></tr>`;
                return;
            }
            
            let html = '';
            res.data.forEach(item => {
                // อ้างอิงรูปภาพผ่านระบบ asset ของ Laravel public
                const imgSrc = item.image_path ? window.location.origin + '/' + item.image_path : 'https://placehold.co/150x150/e2e8f0/64748b?text=AIroBacT';
                const confClass = item.confidence_score >= 80 ? 'bg-gradient-success' : 'bg-gradient-warning';
                const createdDate = new Date(item.created_at).toLocaleString('th-TH');

                html += `
                    <tr>
                        <td class="ps-4">
                            <img src="${imgSrc}" class="table-img border-radius-lg" onclick="previewImage('${imgSrc}')" title="ส่องดูขยะใบเต็ม">
                        </td>
                        <td><h6 class="mb-0 text-sm">#${item.kp_purchase_trans_id}</h6></td>
                        <td><span class="badge badge-sm bg-gradient-secondary">${item.user_id_fk}</span></td>
                        <td><p class="text-xs font-weight-bold text-primary mb-0">${item.detected_label}</p></td>
                        <td class="text-center">
                            <span class="badge badge-sm ${confClass}">
                                <i class="fas fa-robot"></i> ${item.confidence_score}%
                            </span>
                        </td>
                        <td><p class="text-xs text-secondary mb-0">${createdDate}</p></td>
                        <td class="text-end pe-4 text-nowrap">
                            <button type="button" class="btn btn-link text-danger font-weight-bold text-xs p-0 mb-0 me-3" onclick="triggerWarning(${item.id})">
                                <i class="fas fa-bell me-1"></i> เตือนผู้ใช้
                            </button>
                            <button type="button" class="btn btn-link text-success font-weight-bold text-xs p-0 mb-0" onclick="openVerifyModal(${JSON.stringify(item).replace(/"/g, '&quot;')})">
                                <i class="fas fa-magic me-1"></i> แก้ข้อมูลขยะ
                            </button>
                        </td>
                    </tr>
                `;
            });
            tbody.innerHTML = html;
        } catch (error) {
            tbody.innerHTML = `<tr><td colspan="7" class="text-center py-5 text-danger text-sm">❌ ไม่สามารถดึงข้อมูลได้ กรุณาเช็คพาร์ท API</td></tr>`;
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
