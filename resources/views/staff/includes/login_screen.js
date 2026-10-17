document.addEventListener("DOMContentLoaded", () => {


    const loginScreen = document.getElementById('loginScreen');
    const selectOrgScreen = document.getElementById('selectOrgScreen'); // 🟢 เพิ่ม Screen เลือก Org
    const mainScreen = document.getElementById('mainScreen');
    const loginForm = document.getElementById('loginForm');
    const loginError = document.getElementById('loginError');
    const staffNameSpan = document.getElementById('staffName');

    if (document.getElementById('connectionStatusBadge')) {
        document.getElementById('connectionStatusBadge').innerHTML = 'x';
    }

    // 🟢 ฟังก์ชันสลับหน้าไป Main Screen
    function showMainScreen(name) {
        if (loginScreen) loginScreen.classList.add('is-hidden');
        if (selectOrgScreen) selectOrgScreen.classList.add('is-hidden');
        if (mainScreen) mainScreen.classList.remove('is-hidden');
        if (staffNameSpan) staffNameSpan.innerText = name;
        if (typeof updateGlobalPrinterStatus === 'function') updateGlobalPrinterStatus();
    }


    // 🔒 Logout
    const btnLogout = document.getElementById('btnLogout');
    if (btnLogout) {
        btnLogout.addEventListener('click', () => {
            localStorage.clear();
            if (mainScreen) mainScreen.classList.add('is-hidden');
            if (selectOrgScreen) selectOrgScreen.classList.add('is-hidden');
            if (loginScreen) loginScreen.classList.remove('is-hidden');
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

$(document).ready(function() {

    let currentScreen = sessionStorage.getItem('current_screen')
    navigateTo(currentScreen)
    // ==========================================
    // 1. ตรวจสอบ Session ทันทีเมื่อเปิดหน้าเว็บหรือ Refresh
    // ==========================================
    checkExistingSession();

    // ==========================================
    // 2. ดักจับ Event Login ผ่านฟอร์มด้วย jQuery
    // ==========================================
    $('#loginForm').on('submit', function(e) {
        e.preventDefault(); // ป้องกันไม่ให้ฟอร์ม Reload หน้าแบบปกติ
        handleStaffLogin();
    });
});

// ฟังก์ชันตรวจสอบ Session เดิมตอนโหลดหน้าเว็บ
async function checkExistingSession() {
    const staffToken = sessionStorage.getItem("staff_token");
    const staffUserId = sessionStorage.getItem("staff_user_id");
    const savedName = sessionStorage.getItem("staff_name");
    let currentScreen = sessionStorage.getItem("current_screen");
    console.log('staffToken',staffToken)

    if (!staffToken || !staffUserId) {
        console.log("🔴 ไม่พบ Token ในระบบ แสดงหน้า Login");
            $('#loginScreen').removeClass('is-hidden');

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
        console.log('resData',resData)
        if (resData.code === 200 && resData.data && resData.data.valid === true) {
            console.log("🟢 Token ถูกต้อง ข้ามหน้า Login ไปยังหน้าหลัก");
            $('.app-header').removeClass('hidden');

            const staffNameEl = document.getElementById('staffName');
            if (staffNameEl && savedName) {
                staffNameEl.innerText = savedName;
            }

            $('#loginScreen').addClass('is-hidden');
            $('#selectOrgScreen').addClass('is-hidden');
            // $('#mainScreen, #mainAppScreen').removeClass('is-hidden');
             $('.app-header').removeClass('is-hidden');
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
    const $loginError =$('#loginError');
    const $btnSubmit =$('#btnLogin');

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
        console.log('login core resData:', resData);

        if (resData.code === 200 && resData.data && resData.data.logged === true) {
            $('.app-header').removeClass('hidden');
            const staffUser = resData.data;
            const fullName = `${staffUser.prefix || ''}${staffUser.firstname || ''} ${staffUser.lastname || ''}`.trim();

            // บันทึกข้อมูลลงใน sessionStorage (ข้อมูลจะหายไปเมื่อปิดแท็บเบราว์เซอร์)
            sessionStorage.setItem("staff_token", staffUser.token || '');
            sessionStorage.setItem("staff_name", fullName);
            sessionStorage.setItem("staff_user_id", staffUser.id);
            sessionStorage.setItem("twman", JSON.stringify(staffUser));

            const profiles = staffUser.staff_profiles || [];
            console.log('staff profiles:', profiles);
     console.log('staff_token =>',sessionStorage.getItem("staff_token"));

            const $loginScreen =$('#loginScreen');
            const $selectOrgScreen =$('#selectOrgScreen');
            const $mainScreen =$('#mainScreen, #mainAppScreen');

            if (profiles.length === 1) {
                const singleOrg = profiles[0];
                if (typeof setStaffActiveOrg === "function") {
                    setStaffActiveOrg(singleOrg.org_id_fk, singleOrg.id);
                }
                $loginScreen.addClass('is-hidden');
                $selectOrgScreen.addClass('is-hidden');$mainScreen.removeClass('is-hidden');

                if (typeof showMainScreen === "function") {
                    showMainScreen(fullName);
                } else {
                    console.warn("⚠️ ไม่พบฟังก์ชัน showMainScreen แต่สลับหน้าจอให้เรียบร้อยแล้ว");
                }

            } else if (profiles.length > 1) {
                if (typeof renderOrgSelectionList === "function") {
                    renderOrgSelectionList(profiles, fullName);
                }

                $loginScreen.addClass('is-hidden');$selectOrgScreen.removeClass('is-hidden');
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

$('#btnLogout').on('click', function(){
    sessionStorage.clear();
    navigateTo('login');
})

// $('#btnLogin').on('click', function () {
//     test()
// })
// async function test() {
//     // 1. ดึงค่าจากฟอร์ม Login หน้าจอ
//     const usernameInput = document.getElementById('username');
//     const passwordInput = document.getElementById('password');
//     const loginError = document.getElementById('loginError');
//     const btnSubmit = document.getElementById('btnLogin');

//     if (!usernameInput || !passwordInput) {
//         console.error("ไม่พบฟิลด์กรอกข้อมูลชื่อผู้ใช้งานหรือรหัสผ่าน");
//         return;
//     }

//     const username = usernameInput.value.trim();
//     const password = passwordInput.value.trim();

//     // เคลียร์และซ่อนข้อความแจ้งเตือนเดิม
//     if (loginError) {
//         loginError.style.display = "none";
//         loginError.innerText = "";
//     }

//     if (!username || !password) {
//         if (loginError) {
//             loginError.innerText = "กรุณากรอกชื่อผู้ใช้และรหัสผ่านให้ครบถ้วน";
//             loginError.style.display = "block";
//         }
//         return;
//     }

//     // ล็อคปุ่มระหว่างรอ API ตอบกลับ
//     if (btnSubmit) {
//         btnSubmit.disabled = true;
//         btnSubmit.innerText = "กำลังตรวจสอบข้อมูล...";
//     }

//     try {
//         // 2. เรียกใช้งาน API ล็อกอินผ่านระบบพนักงาน
//         const response = await fetch(`${API_BASE_URL}/users/staff_login_core`, {
//             method: 'POST',
//             headers: {
//                 'Content-Type': 'application/json',
//                 'Accept': 'application/json',
//                 'ngrok-skip-browser-warning': 'true'
//             },
//             body: JSON.stringify({
//                 username: username,
//                 passwords: password // หรือใช้ key 'password' ตามที่ API ฝั่ง Backend กำหนด
//             })
//         });

//         const resData = await response.json();
//         console.log('login core resData:', resData);

//         // 3. ตรวจสอบผลลัพธ์จาก API
//         if (resData.code === 200 && resData.data && resData.data.logged === true) {
//             // แสดงส่วนหัวแอปพลิเคชัน
//             $('.app-header').removeClass('hidden');

//             const staffUser = resData.data;
//             const fullName = `${staffUser.prefix || ''}${staffUser.firstname || ''} ${staffUser.lastname || ''}`.trim();

//             // บันทึกข้อมูลลงใน LocalStorage
//             localStorage.setItem("staff_token", staffUser.remember_token || '');
//             localStorage.setItem("staff_name", fullName);
//             localStorage.setItem("staff_user_id", staffUser.id);
//             localStorage.setItem("twman", JSON.stringify(staffUser));

//             // ตรวจสอบสิทธิ์องค์กร/สาขา (staff_profiles)
//             const profiles = staffUser.staff_profiles || [];
//             console.log('staff profiles:', profiles);

//             const loginScreen = document.getElementById('loginScreen');
//             const selectOrgScreen = document.getElementById('selectOrgScreen');
//             const mainScreen = document.getElementById('mainScreen') || document.getElementById('mainAppScreen');

//             if (profiles.length === 1) {
//                 // กรณีมีสาขาเดียว ตั้งค่า Active Org ทันทีและเข้าหน้าหลัก
//                 const singleOrg = profiles[0];
//                 if (typeof setStaffActiveOrg === "function") {
//                     setStaffActiveOrg(singleOrg.org_id_fk, singleOrg.id);
//                 }

//                 if (loginScreen) {
//                     loginScreen.style.display = 'none';
//                     loginScreen.classList.add('is-hidden');
//                 }
//                 if (selectOrgScreen) {
//                     selectOrgScreen.style.display = 'none';
//                     selectOrgScreen.classList.add('is-hidden');
//                 }
//                 if (mainScreen) {
//                     mainScreen.style.display = 'block';
//                     mainScreen.classList.remove('is-hidden');
//                 }

//                 if (typeof showMainScreen === "function") {
//                     showMainScreen(fullName);
//                 } else {
//                     console.warn("⚠️ ไม่พบฟังก์ชัน showMainScreen แต่สลับหน้าจอให้เรียบร้อยแล้ว");
//                 }

//             } else if (profiles.length > 1) {
//                 // กรณีมีหลายสาขา ให้แสดงหน้าเลือกองค์กร (Select Org)
//                 if (typeof renderOrgSelectionList === "function") {
//                     renderOrgSelectionList(profiles, fullName);
//                 }

//                 if (loginScreen) {
//                     loginScreen.style.display = 'none';
//                     loginScreen.classList.add('is-hidden');
//                 }
//                 if (selectOrgScreen) {
//                     selectOrgScreen.style.display = 'block';
//                     selectOrgScreen.classList.remove('is-hidden');
//                 }
//             } else {
//                 throw new Error("ไม่พบข้อมูลสิทธิ์เจ้าหน้าที่ในระบบ (Staff Profiles)");
//             }

//         } else {
//             // กรณีล็อกอินไม่ผ่าน (รหัสผิด หรือ API ตอบกลับโค้ดอื่น)
//             if (loginError) {
//                 loginError.innerText = resData.message || "ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง";
//                 loginError.style.display = "block";
//             }
//         }

//     } catch (error) {
//         if (loginError) {
//             loginError.innerText = error.message || "เกิดข้อผิดพลาด: ไม่สามารถเชื่อมต่อกับฐานข้อมูลระบบได้";
//             loginError.style.display = "block";
//         }
//         console.error("API Connection Error:", error);
//     } finally {
//         // คืนค่าปุ่มให้กลับมาใช้งานได้ปกติ
//         if (btnSubmit) {
//             btnSubmit.disabled = false;
//             btnSubmit.innerText = "🔓 เข้าสู่ระบบ";
//         }
//     }
// }
// test()