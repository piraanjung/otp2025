// ////////////////////////////////////////////////////////
//                    Global                             //
///////////////////////////////////////////////////////////
let currentScreenGlobal = "";
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
// ////////////////////////////////////////////////////////
//                    Screen Login                       //
///////////////////////////////////////////////////////////
document.addEventListener("DOMContentLoaded", () => {

    const loginForm = document.getElementById('loginForm');

    if (document.getElementById('connectionStatusBadge')) {
        document.getElementById('connectionStatusBadge').innerHTML = 'x';
    }



    // 🔒 Logout
    const btnLogout = document.getElementById('btnLogout');
    if (btnLogout) {
        btnLogout.addEventListener('click', () => {
            sessionStorage.clear();
            navigateTo('login');

            if (loginForm) loginForm.reset();
            window.location.reload();
        });
    }
});



// 🟢 Helper Functions สำหรับจัดการ Org
function setStaffActiveOrg(orgId, staffId) {
    localStorage.setItem("staff_org_id", orgId);
    localStorage.setItem("staff_id", staffId);
}

function renderOrgSelectionList(profiles, fullName) {
    console.log('renderOrgSelectionList', profiles)
    let container = document.getElementById('orgListContainer');
    if (!container) return;
    container.innerHTML = '';

    profiles.forEach(staff => {
        let org = staff.organization || {};
        console.log('org', org)
        let orgName = org.org_name || `องค์กรรหัส #${staff.org_id_fk}`;


        // 🟢 แก้ไขบรรทัดนี้: ลบ @ ออก ให้เหลือแค่ ${...} สำหรับ JS
        let logoHtml = org.org_logo_img

            ? `<img src="/logo/${org.org_logo_img}" class="rounded-circle" style="width: 45px; height: 45px; object-fit: cover;">`
            : `<div class="bg-primary-subtle text-primary rounded-circle p-2 fw-bold text-center" style="width: 45px; height: 45px; line-height: 28px;">🏢</div>`;

        let cardHtml = `
            <div class="card border-0 shadow-sm rounded-3 cursor-pointer mb-2" 
                 onclick="selectOrgAndContinue(${staff.org_id_fk}, ${staff.id}, '${fullName}')">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div>${logoHtml}</div>
                    <div class="flex-grow-1">
                        <h6 class="fw-bold mb-0 text-dark">${orgName}</h6>
                        <small class="text-muted">รหัสเจ้าหน้าที่: ${staff.id}</small>
                    </div>
                    <span class="btn btn-sm btn-outline-primary rounded-pill px-3">เลือก ➔</span>
                </div>
            </div>
        `;
        container.innerHTML += cardHtml;
    });
}



function selectOrgAndContinue(orgId, staffId, fullName) {
    setStaffActiveOrg(orgId, staffId);
    // ซ่อนหน้าเลือก Org แล้วเปิดหน้า Main
    console.log('xxx')
    document.getElementById('selectOrgScreen').classList.add('is-hidden');
    document.getElementById('mainScreen').classList.remove('is-hidden');
    if (document.getElementById('staffName')) document.getElementById('staffName').innerText = fullName;
}

$(document).ready(function () {

    // sessionStorage.clear()
    // ==========================================
    // 1. ตรวจสอบ Session ทันทีเมื่อเปิดหน้าเว็บหรือ Refresh
    // ==========================================
    checkExistingSession();

    // ==========================================
    // 2. ดักจับ Event Login ผ่านฟอร์มด้วย jQuery
    // ==========================================
    $('#loginForm').on('submit', function (e) {
        e.preventDefault(); // ป้องกันไม่ให้ฟอร์ม Reload หน้าแบบปกติ
        handleStaffLogin();
    });
});

// ฟังก์ชันตรวจสอบ Session เดิมตอนโหลดหน้าเว็บ
async function checkExistingSession() {
    const staffToken = sessionStorage.getItem("staff_token");
    const staffUserId = sessionStorage.getItem("staff_user_id");
    const savedName = sessionStorage.getItem("staff_name");
    currentScreenGlobal = sessionStorage.getItem("current_screen");

    if (!staffToken || !staffUserId) {
        console.log("🔴 ไม่พบ Token ในระบบ แสดงหน้า Login");
        sessionStorage.setItem('current_screen', 'loginScreen');
        currentScreenGlobal = 'loginScreen';

        navigateTo('loginScreen')
        return;
    }

    console.log("🟡 กำลังตรวจสอบความถูกต้องของ Token กับ Server...");

    try {
        const response = await fetch(`${API_BASE_URL}/users/staff/verify_token`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'ngrok-skip-browser-warning': 'true'
            },
            body: JSON.stringify({
                user_id: staffUserId,
                remember_token: staffToken
            })
        });

        const resData = await response.json();

        if (resData.code === 200 && resData.data && resData.data.valid === true) {
            console.log("🟢 Token ถูกต้อง ข้ามหน้า Login ไปยังหน้าหลัก");
            $('.app-header').removeClass('hidden');

            const staffNameEl = document.getElementById('staffName');
            if (staffNameEl && savedName) {
                staffNameEl.innerText = savedName;
            }

            if (!currentScreenGlobal) {
                console.log("loginScreenxx");

                navigateTo('loginScreen')
            } else {
                console.log("currentScreenGlobalxxx", currentScreenGlobal);

                navigateTo(currentScreenGlobal)
            }

            console.log('checkExistingSession currentScreenGlobal', currentScreenGlobal)

            loadInventoryIframeOnce();
            if (typeof showMainScreen === "function" && savedName) {
                showMainScreen(savedName);
            }
        } else {
            console.warn("⚠️ Token ไม่ถูกต้องหรือถูกใช้งานจากที่อื่น บังคับ Logout");
            handleForceLogout("เซสชันหมดอายุ หรือมีการเข้าสู่ระบบจากอุปกรณ์อื่น กรุณาเข้าสู่ระบบใหม่");
        }

    } catch (error) {
        console.error("API Verification Error:", error);
        handleForceLogout("ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์เพื่อตรวจสอบสิทธิ์ได้");
    }
}

// ฟังก์ชันหลักสำหรับการ Login
async function handleStaffLogin() {
    const username = $('#username').val().trim();
    const password = $('#password').val().trim();
    const $loginError = $('#loginError');
    const $btnSubmit = $('#btnLogin');

    $loginError.text("");

    if (!username || !password) {
        $loginError.text("กรุณากรอกชื่อผู้ใช้และรหัสผ่านให้ครบถ้วน").show();
        return;
    }

    $btnSubmit.prop('disabled', true).text("กำลังตรวจสอบข้อมูล...");

    try {
        const response = await fetch(`${API_BASE_URL}/users/staff_login_core`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'ngrok-skip-browser-warning': 'true'
            },
            body: JSON.stringify({
                username: username,
                passwords: password
            })
        });

        const resData = await response.json();
        // console.log('login core resData:', resData);

        if (resData.code === 200 && resData.data && resData.data.logged === true) {
            const staffUser = resData.data;
            const fullName = `${staffUser.prefix || ''}${staffUser.firstname || ''} ${staffUser.lastname || ''}`.trim();

            // บันทึกข้อมูลลงใน sessionStorage (ข้อมูลจะหายไปเมื่อปิดแท็บเบราว์เซอร์)
            sessionStorage.setItem("staff_token", staffUser.token || '');
            sessionStorage.setItem("staff_name", fullName);
            sessionStorage.setItem("staff_user_id", staffUser.id);
            sessionStorage.setItem("twman", JSON.stringify(staffUser));

            const profiles = staffUser.staff_profiles || [];
            console.log('staff profiles:', profiles);
            console.log('staff_token =>', sessionStorage.getItem("staff_token"));

            const $loginScreen = $('#loginScreen');
            const $selectOrgScreen = $('#selectOrgScreen');
            const $mainScreen = $('#mainScreen, #mainAppScreen');

            if (profiles.length === 1) {
                const singleOrg = profiles[0];
                if (typeof setStaffActiveOrg === "function") {
                    setStaffActiveOrg(singleOrg.org_id_fk, singleOrg.id);
                }
                // $loginScreen.addClass('is-hidden');
                // $selectOrgScreen.addClass('is-hidden'); 
                // $mainScreen.removeClass('is-hidden');
                navigateTo('mainScreen')
                if (typeof showMainScreen === "function") {
                    showMainScreen(fullName);
                } else {
                    console.warn("⚠️ ไม่พบฟังก์ชัน showMainScreen แต่สลับหน้าจอให้เรียบร้อยแล้ว");
                }

            } else if (profiles.length > 1) {
                if (typeof renderOrgSelectionList === "function") {
                    renderOrgSelectionList(profiles, fullName);
                }

                // $loginScreen.addClass('is-hidden'); 
                // $selectOrgScreen.removeClass('is-hidden');
                navigateTo('selectOrgScreen')

            } else {
                throw new Error("ไม่พบข้อมูลสิทธิ์เจ้าหน้าที่ในระบบ (Staff Profiles)");
            }

        } else {
            $loginError.text(resData.message || "ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง").show();
        }

    } catch (error) {
        $loginError.text(error.message || "เกิดข้อผิดพลาด: ไม่สามารถเชื่อมต่อกับฐานข้อมูลระบบได้").show();
        console.error("API Connection Error:", error);
    } finally {
        $btnSubmit.prop('disabled', false).text("🔓 เข้าสู่ระบบ");
    }
}

// ฟังก์ชันเคลียร์ค่าเมื่อออกจากระบบหรือ Session หลุด
function handleForceLogout(message) {
    sessionStorage.removeItem("staff_token");
    sessionStorage.removeItem("staff_name");
    sessionStorage.removeItem("staff_user_id");
    sessionStorage.removeItem("twman");

    $('#loginScreen').removeClass('is-hidden');
    $('#selectOrgScreen').addClass('is-hidden');
    $('#mainScreen, #mainAppScreen').addClass('is-hidden');
    $('.app-header').addClass('hidden');

    if (message) {
        $('#loginError').text(message).show();
    }
}

function loadInventoryIframeOnce() {
    const $iframe = $('#inventoryIframe');
    const realSrc = $iframe.attr('data-src');

    // ถ้ามี data-src และตัว iframe ยังไม่มี src (หรือยังไม่ได้โหลด) ให้ใส่ค่าลงไป
    if (realSrc && (!$iframe.attr('src') || $iframe.attr('src') === '')) {
        console.log("🟢 เริ่มโหลด Inventory Iframe หลังจาก Login สำเร็จ");
        $iframe.attr('src', realSrc);
    }
}

///////////////////////////////////////////////////////////
//                    NavigateTo                        //
///////////////////////////////////////////////////////////

let prevScreen = "";
async function navigateTo(moduleName) {
    moduleName = 'tabwaterRecordScreen'
    console.log('navigateTo current_screen', moduleName)
    sessionStorage.setItem('current_screen', moduleName)
    $('.screen').each(function () {

        !$(this).hasClass('is-hidden') ? $(this).addClass('is-hidden') : ''
    })
    $moduleNameArray = ['mainScreen', 'tabwaterScreen']
    if ($moduleNameArray.includes(moduleName)) {
        $('#appSidebar').removeClass('is-hidden')
    }
    if (moduleName === 'recycleBankScreen') {
        loadMembersFromServer()
    }else if (moduleName === 'tabwaterRecordScreen') {
        loadWaterRecordDashboard()
    }
    
    $(`#${moduleName}`).removeClass('is-hidden')
    // prevScreen = moduleName;
    // moduleName = 'main';//await checkCurrentScreen(moduleName);
    // console.log('moduleName',moduleName)
    // if (moduleName === 'recycle') {
    //     document.getElementById('mainScreen').classList.add('is-hidden');
    //     document.getElementById('recycleScreen').classList.remove('is-hidden');
    //     document.getElementById('searchMemberInput').value = "";
    //     loadMembersFromServer();
    //     renderMemberList(allMembers);
    // }
    // else if (moduleName === 'settings') {
    //     document.getElementById('mainScreen').classList.add('is-hidden');
    //     document.getElementById('depositScreen').classList.add('is-hidden');
    //     document.getElementById('settingsScreen').classList.remove('is-hidden');

    //     checkBluetoothStatus();
    // }
    // else if (moduleName === 'inventory') {
    //     $('#mainScreen').addClass('is-hidden');
    //     document.getElementById('depositScreen').classList.add('is-hidden');
    //     document.getElementById('settingsScreen').classList.add('is-hidden');
    //     document.getElementById('inventoryScreen').classList.remove('is-hidden');
    //     manageInventoryIframe('open')
    //     return
    // }
    // else if (moduleName === 'water') {
    //     document.getElementById('mainScreen').classList.add('is-hidden');
    //     document.getElementById('waterRecordScreen').classList.add('is-hidden');
    //     document.getElementById('tabwaterScreen').classList.remove('is-hidden');


    // }
    // // --- เพิ่มเงื่อนไขสำหรับจดมิเตอร์ประปาตรงนี้ ---
    // else if (moduleName === 'water-tabwater-record') {

    //     document.getElementById('tabwaterScreen').classList.add('is-hidden');
    //     document.getElementById('waterRecordScreen').classList.remove('is-hidden');

    //     // หากมีฟังก์ชันโหลดข้อมูลมิเตอร์เดิม ให้เรียกตรงนี้ เช่น loadWaterMeters();
    //     loadWaterRecordDashboard();
    // }
    // else if (moduleName === 'water-members-list') {
    //     console.log('water-members-list')
    //     document.getElementById('waterRecordScreen').classList.add('is-hidden');
    //     document.getElementById('waterMembersListScreen').classList.remove('is-hidden');

    //     // เรียกดึงข้อมูลรายชื่อสมาชิกใน Subzone นั้นทันที
    //     if (typeof loadWaterMembersList === 'function') {
    //         loadWaterMembersList();
    //     }
    // }
    // // 🟢 2. เพิ่มหน้าแก้ไขรายชื่อสมาชิก/เลขมิเตอร์
    // else if (moduleName === 'water-members-edit-list') {
    //     document.getElementById('waterRecordScreen').classList.add('is-hidden');
    //     document.getElementById('waterMembersEditListScreen').classList.remove('is-hidden');

    //     if (typeof loadWaterMembersEditList === 'function') {
    //         loadWaterMembersEditList();
    //     }
    // }
    // else if (moduleName === 'water-complain') {
    //     // 1. เปิด Modal
    //     const staffModal = new bootstrap.Modal(document.getElementById('staffDashboardModal'));
    //     staffModal.show();

    //     // 2. ดึงเนื้อหาจาก Route staff/dashboard ผ่าน Fetch API
    //     fetch('staff/dashboard')
    //         .then(response => response.text())
    //         .then(html => {
    //             // นำ HTML ที่ได้มาใส่ใน modal-body โดยไม่ Refresh หน้า
    //             document.getElementById('modal-staff-content').innerHTML = html;
    //         })
    //         .catch(error => {
    //             console.error('Error loading staff dashboard:', error);
    //             document.getElementById('modal-staff-content').innerHTML =
    //                 '<div class="alert alert-danger">ไม่สามารถโหลดข้อมูลได้</div>';
    //         });
    // }
    // else if (moduleName === 'main') {
    //     document.getElementById('mainScreen').classList.remove('is-hidden');
    //     document.getElementById('assistiveBtn').classList.remove('is-hidden');

    //     document.getElementById('tabwaterScreen').classList.add('is-hidden');
    //     document.getElementById('settingsScreen').classList.add('is-hidden');
    //     document.getElementById('recycleScreen').classList.add('is-hidden');
    //     checkBluetoothStatus();
    // }
    //  else if (moduleName === 'login') {
    //     document.getElementById('mainScreen').classList.add('is-hidden');
    //     document.getElementById('tabwaterScreen').classList.add('is-hidden');
    //     document.getElementById('settingsScreen').classList.add('is-hidden');
    //     document.getElementById('recycleScreen').classList.add('is-hidden');
    //     checkBluetoothStatus();
    // }
    manageInventoryIframe('close')

    // updateGlobalPrinterStatus();
}


$('#staffDashboardModal', '#secondModal').on('hidden.bs.modal', function (e) {
    // โค้ดที่จะทำงานหลังจาก Modal ปิดเรียบร้อยแล้ว
    navigateTo('tabwaterScreen');
});

function manageInventoryIframe(status) {
    const $iframe = $('#inventoryIframe');
    let url = '';
    if (status === 'open') {
        url = '/inventory/items/iframe'
    }

    $iframe.attr('src', url);
}

function checkCurrentScreen(moduleName) {
    let _currentScreen = sessionStorage.getItem('current_screen')
    if (_currentScreen === moduleName || !moduleName) {

    } else {
        console.log('moduleName else')

        sessionStorage.removeItem('current_screen')
        sessionStorage.setItem('current_screen', moduleName)
        _currentScreen = moduleName;
        currentScreenGlobal = moduleName;
    }
    return _currentScreen;
}


// ////////////////////////////////////////////////////////
//                    SideBar.                           //
///////////////////////////////////////////////////////////

function toggleIconOnly() {
    const sidebar = document.getElementById('sidebarMenu');
    sidebar.classList.toggle('icon-only');
    $('#sidebarMenu').hasClass('icon-only') ? $('.menu-link div').addClass('is-hidden')
        : $('.menu-link div').removeClass('is-hidden')
}

// ฟังก์ชันปุ่ม iPhone สำหรับซ่อน/แสดงเมนูลอยทั้งหมด
function toggleFullSidebar() {
    const sidebar = document.getElementById('sidebarMenu');
    const assistiveIcon = document.getElementById('assistiveIcon');

    sidebar.classList.toggle('hidden-sidebar');

    if (sidebar.classList.contains('hidden-sidebar')) {

        assistiveIcon.className = "fa-solid fa-arrow-right";
    } else {
        assistiveIcon.className = "fa-solid fa-bars-staggered";

    }
}

function switchSidebarMenu(element, menuName) {
    document.querySelectorAll('.sidebar-nav .sidebar-item').forEach(item => {
        item.classList.remove('active');
    });
    element.classList.add('active');
    toggleIconOnly()
    switch (menuName) {
        case 'tabwater':
            navigateTo('tabwaterScreen')
            break;
        case 'recycle_bank':
            navigateTo('recycleBankScreen')
            break;
        case 'inventory':
            navigateTo('inventoryScreen')
            break;
        default:
            navigateTo('mainScreen')
    }


    const indicator = document.getElementById('activeIndicator');
    indicator.style.transform = `translateY(${element.offsetTop}px)`;
}

window.addEventListener('load', () => {
    const firstActive = document.querySelector('.sidebar-nav .sidebar-item.active');
    if (firstActive) {
        const indicator = document.getElementById('activeIndicator');
        indicator.style.transform = `translateY(${firstActive.offsetTop}px)`;
    }
});

///////////////////////////////////////////////////////////
//                    tabwaterScreen.                    //
///////////////////////////////////////////////////////////
document.addEventListener("DOMContentLoaded", () => {
    const taskCards = document.querySelectorAll('.task-card');

    taskCards.forEach(card => {
        card.addEventListener('click', function (e) {
            if (this.classList.contains('is-pressed')) return;
            this.classList.add('is-pressed');

            const onclickAttr = this.getAttribute('onclick');
            this.removeAttribute('onclick');

            setTimeout(() => {
                this.classList.remove('is-pressed');
                if (onclickAttr) {
                    new Function(onclickAttr).call(this);
                }
            }, 200); // หน่วงเวลา 200 มิลลิวินาทีให้อนิเมชันเล่นจบอย่างสมูท
        });
    });
});

//////////////////////////////////////////////////////////
//                    Tabwater.                           //
///////////////////////////////////////////////////////////


let html5QrCodeForTabwater = null;

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
                org_id_fk: 1,//orgId,
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

    // navigateTo('water-members-list');
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

    // navigateTo('water-members-edit-list');
});


async function loadWaterMembersList() {
    console.log('loadWaterMembersList()')
    const container = document.getElementById('membersListContainer');
    const subzoneSelectedRaw = localStorage.getItem('subzone_selected');
    if (!subzoneSelectedRaw) {
        return;
    };
    const subzoneSelected = JSON.parse(subzoneSelectedRaw);

    if (document.getElementById("currentSubzoneTitle")) {
        document.getElementById("currentSubzoneTitle").innerText = `สาย: ${subzoneSelected.subzone.subzone_name || ''}`;
        document.getElementById("progressBadge").innerText = `${subzoneSelected.members_status_invoice} / ${subzoneSelected.members}`;
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
$('#btn-save-only').on('click', function () {
    saveMeterRecord(false); // บันทึกอย่างเดียว
});

$('#btn-save-and-print').on('click', function () {
    BluethoothConnectedModal('tabwater');
    saveMeterRecord(true); // บันทึกพร้อมพิมพ์
});

async function test2() {
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

async function generateWaterBillHTML2(data = '2') {
    console.log('dsssa', data)
    console.log('dsxxxxssa', data)
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

    $('#qrcode_info').html(`
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
    console.log('da', data)
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