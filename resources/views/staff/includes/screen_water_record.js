// --- ตัวแประดับ Global สำหรับ SPA ---
let currentSubzonesData = []; // เก็บข้อมูล Subzones ที่ดึงมาจาก API
let membersSubzone;
let cur_meter = "";
let water_used = 0.0;
let paid_raw = 0.0;
let vat = 0.0;
let inv_id = 0.0;
let totalpaid = 0.0;
let membersInitStatusCount;
let membersInitStatus;
let inv_period;

// --- ฟังก์ชันสำหรับโหลดข้อมูลเส้นทางจดมิเตอร์ (ปรับปรุงตาม SPA Architecture) ---
async function loadWaterRecordDashboard() {
    // 🟢 1. ดึงข้อมูลยืนยันตัวตนจาก localStorage ชุดใหม่
    const staffId = localStorage.getItem('staff_id');//2;//
    const orgId = localStorage.getItem('staff_org_id')//2//;;
    const userId = localStorage.getItem('staff_user_id');//3857//
    const staffName = localStorage.getItem('staff_name');

    if (!staffId || !orgId) {
        console.warn('ไม่พบข้อมูลเจ้าหน้าที่ หรือยังไม่ได้เลือกองค์กร');
        return;
    }

    // 🟢 2. แสดงชื่อเจ้าหน้าที่บน Header หน้า Dashboard
    if (staffName) {
        $('.twman_name').html(staffName);
    }

    // แสดงสถานะกำลังโหลด
    $("#subzone").html(`
        <div class="col-12 text-center py-5">
            <div class="spinner-border text-info" role="status"></div>
            <p class="mt-2 text-muted">กำลังดึงข้อมูลเส้นทางค่าน้ำประปา...</p>
        </div>
    `);

    try {
        // 🟢 3. เรียก Lazy-loading API ดึงสถิติเฉพาะงานประปา
        const response = await fetch(`${API_BASE_URL}/staff/tabwater/staff/service-data`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'ngrok-skip-browser-warning': 'true'
            },
            body: JSON.stringify({
                user_id: userId,
                staff_id: staffId,
                org_id_fk: orgId,
                service_type: 'water'
            })
        });

        const resData = await response.json();
        console.log('resxxxx', resData)
        if (resData.code !== 200 || !resData.data) {
            $("#subzone").html(`<div class="col-12 text-center text-danger py-4">${resData.message || 'ดึงข้อมูลไม่สำเร็จ'}</div>`);
            return;
        }

        // 🟢 4. เก็บข้อมูล Subzone ลงในตัวแปร Global
        currentSubzonesData = resData.data.undertaker_subzone || [];

        // แสดงรอบบิล (ถ้ามีส่งกลับมาใน API)
        if (resData.data.inv_period && resData.data.inv_period.length > 0) {
            $('.inv_period').html("รอบบิลที่ " + resData.data.inv_period[0].inv_p_name);
        }

        // 🟢 5. Render รายการ Subzone ลงใน HTML
        let text = "";
        if (currentSubzonesData.length === 0) {
            text = `<div class="col-12 text-center text-muted py-4">ไม่พบข้อมูลเขตรับผิดชอบ (Subzone)</div>`;
        } else {
            currentSubzonesData.forEach((element, i) => {
                const zoneName = element.subzone?.zone?.zone_name || 'ไม่ระบุโซน';
                const subzoneName = element.subzone?.subzone_name || 'ไม่ระบุสาย';

                text += `
                    <div class="col-md-6 col-12 p-1">
                        <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
                            <div class="bg-info text-white p-3">
                                <h5 class="mb-1 fw-bold">${zoneName}</h5>
                                <h6 class="mb-0">${subzoneName}</h6>
                            </div>
                            <div class="card-body p-3">
                                <ul class="list-group list-group-flush mb-3">
                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                        <span>จำนวนสมาชิกทั้งหมด</span>
                                        <span class="badge bg-info rounded-pill">${element.members || 0} คน</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                        <span>ชำระค่าน้ำประปาแล้ว</span>
                                        <span class="badge bg-success rounded-pill">${element.members_status_paid || 0} คน</span>
                                    </li>
                                </ul>

                                <div class="row g-2 mb-2">
                                    <div class="col-7">
                                        <small class="d-block text-muted">บันทึกเลขมิเตอร์แล้ว</small>
                                        <span class="badge bg-success">${element.members_status_invoice || 0} คน</span>
                                    </div>
                                    <div class="col-5">
                                        <button data-subzone_id="${element.subzone_id}" data-undertaker_subzone_index="${i}" 
                                            class="btn btn-sm btn-outline-success w-100 goto_edit_subzone_selected_btn ${element.members_status_invoice === 0 ? 'd-none' : ''}">
                                            แก้ไขข้อมูล
                                        </button>
                                    </div>
                                </div>

                                <div class="row g-2">
                                    <div class="col-7">
                                        <small class="d-block text-muted">ยังไม่บันทึกเลขมิเตอร์</small>
                                        <span class="badge bg-info">${element.members_status_init || 0} คน</span>
                                    </div>
                                    <div class="col-5">
                                        <button data-subzone_id="${element.subzone_id}" data-undertaker_subzone_index="${i}" 
                                            class="btn btn-sm btn-info text-white w-100 goto_members_subzone_selected_btn ${element.members_status_init === 0 ? 'd-none' : ''}">
                                            เพิ่มข้อมูล
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
        }
        $("#subzone").html(text);

    } catch (error) {
        console.error("API Error:", error);
        $("#subzone").html(`<div class="col-12 text-center text-danger py-4">เกิดข้อผิดพลาดในการเชื่อมต่อเซิร์ฟเวอร์</div>`);
    }
}

// --- Event Listeners เมื่อกดเลือกเส้นทาง ---
$(document).on('click', '.goto_members_subzone_selected_btn', function () {
    let subzone_id_selected = $(this).data('subzone_id');
    let index = $(this).data('undertaker_subzone_index');

    window.localStorage.setItem('subzone_id_selected', JSON.stringify(subzone_id_selected));
    window.localStorage.setItem('subzone_index_selected', index);
    
    // 🟢 ดึงข้อมูลจาก currentSubzonesData แทน twman.undertaker_subzone
    if (currentSubzonesData[index]) {
        console.log('currentSubzonesData[index]',currentSubzonesData[index])
        window.localStorage.setItem('subzone_selected', JSON.stringify(currentSubzonesData[index]));
    }

    navigateTo('water-members-list');
});

$(document).on('click', '.goto_edit_subzone_selected_btn', function () {
    let subzone_id_selected = $(this).data('subzone_id');
    let index = $(this).data('undertaker_subzone_index');

    window.localStorage.setItem('subzone_id_selected', JSON.stringify(subzone_id_selected));
    window.localStorage.setItem('subzone_index_selected', index);
    
    // 🟢 ดึงข้อมูลจาก currentSubzonesData แทน twman.undertaker_subzone
    if (currentSubzonesData[index]) {
        window.localStorage.setItem('subzone_selected', JSON.stringify(currentSubzonesData[index]));
    }

    navigateTo('water-members-edit-list');
});

