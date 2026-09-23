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

    // 🔒 ฟังก์ชัน Login
    // if (loginForm) {
    //     console.log('xxxsss')
    //     loginForm.addEventListener('submit', async (e) => {
    //         e.preventDefault();
    //         console.log('xxx')
    //         const username = document.getElementById('username').value;
    //         const password = document.getElementById('password').value;
    //         const btnSubmit = document.getElementById('btnLogin');

    //         if (loginError) loginError.style.display = "none";
    //         btnSubmit.disabled = true;
    //         btnSubmit.innerText = "กำลังตรวจสอบข้อมูล...";

    //         try {
    //             console.log('sss')
    //             // เคลียร์ค่าค้างเก่า
    //             localStorage.clear();

    //             // 🟢 1. เปลี่ยน Endpoint ไปใช้ API ตัวใหม่ (/v1/staff/authen)
    //             const response = await fetch(`${API_BASE_URL}/users/staff_login_core`, {
    //                 method: 'POST',
    //                 headers: {
    //                     'Content-Type': 'application/json',
    //                     'Accept': 'application/json',
    //                     'ngrok-skip-browser-warning': 'true'
    //                 },
    //                 body: JSON.stringify({
    //                     username: username,
    //                     passwords: password,
    //                 })
    //             });

    //             const resData = await response.json();
    //                 console.log('login cor resData',resData)
    //             if (resData.code === 200 && resData.data && resData.data.logged === true) {
    //                 $('.app-header').removeClass('hidden');
    //                 const staffUser = resData.data;
    //                 const fullName = `${staffUser.prefix || ''}${staffUser.firstname} ${staffUser.lastname}`;

    //                 // บันทึกข้อมูลส่วนตัวของ User
    //                 localStorage.setItem("staff_token", staffUser.remember_token);
    //                 localStorage.setItem("staff_name", fullName);
    //                 localStorage.setItem("staff_user_id", staffUser.id);

    //                 // 🔴 แก้ไขจุดที่ 1: ต้อง stringify Object ก่อนบันทึกลง localStorage
    //                 localStorage.setItem("twman", JSON.stringify(staffUser));

    //                 const profiles = staffUser.staff_profiles || [];

    //                 console.log('profiles', profiles);

    //                 // 🟢 2. เช็คจำนวนองค์กร (Multi-Org Logic)
    //                 if (profiles.length === 1) {
    //                     // มี 1 องค์กร: เซ็ตค่าแล้วเข้าหน้า Main ทันที
    //                     const singleOrg = profiles[0];
    //                     setStaffActiveOrg(singleOrg.org_id_fk, singleOrg.id);

    //                     // 🔴 แก้ไขจุดที่ 2: สั่งซ่อนหน้า Login / หน้าเลือก Org แล้วแสดงหน้า Main
    //                     if (loginScreen) loginScreen.classList.add('is-hidden');
    //                     if (selectOrgScreen) selectOrgScreen.classList.add('is-hidden');
    //                     if (mainScreen) mainScreen.classList.remove('is-hidden'); // <-- เพิ่มสั่งแสดงหน้าหลัก (ถ้ามีตัวแปร mainScreen)

    //                     showMainScreen(fullName);
    //                 } else if (profiles.length > 1) {
    //                     // มีหลายองค์กร: Render ให้เลือกลงในหน้า selectOrgScreen
    //                     renderOrgSelectionList(profiles, fullName);
    //                     if (loginScreen) loginScreen.classList.add('is-hidden');
    //                     if (selectOrgScreen) selectOrgScreen.classList.remove('is-hidden');
    //                 } else {
    //                     throw new Error("ไม่พบข้อมูลสิทธิ์เจ้าหน้าที่ในระบบ");
    //                 }

    //             } else {
    //                 if (loginError) {
    //                     loginError.innerText = resData.message || "ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง";
    //                     loginError.style.display = "block";
    //                 }
    //             }
    //         } catch (error) {
    //             if (loginError) {
    //                 loginError.innerText = error.message || "เกิดข้อผิดพลาด: ไม่สามารถเชื่อมต่อกับฐานข้อมูลระบบได้";
    //                 loginError.style.display = "block";
    //             }
    //             console.error("API Connection Error:", error);
    //         } finally {
    //             btnSubmit.disabled = false;
    //             btnSubmit.innerText = "🔓 เข้าสู่ระบบ";
    //         }
    //     });
    // }

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
    document.getElementById('selectOrgScreen').classList.add('is-hidden');
    document.getElementById('mainScreen').classList.remove('is-hidden');
    if (document.getElementById('staffName')) document.getElementById('staffName').innerText = fullName;
}

$('#btnLogin').on('click', function () {
    test()
})
async function test() {
    console.log('xxx')
    const username = document.getElementById('username').value;
    console.log('username', username)
    const password = document.getElementById('password').value;
    const btnSubmit = document.getElementById('btnLogin');

    if (loginError) loginError.style.display = "none";
    btnSubmit.disabled = true;
    btnSubmit.innerText = "กำลังตรวจสอบข้อมูล...";

    try {
        console.log('sss')
        // เคลียร์ค่าค้างเก่า
        localStorage.clear();

        // 🟢 1. เปลี่ยน Endpoint ไปใช้ API ตัวใหม่ (/v1/staff/authen)
        const response = await fetch(`${API_BASE_URL}/users/staff_login_core`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'ngrok-skip-browser-warning': 'true'
            },
            body: JSON.stringify({
                username: username,
                passwords: password,
            })
        });

        const resData = await response.json();
        console.log('login cor resData', resData)
        if (resData.code === 200 && resData.data && resData.data.logged === true) {
            $('.app-header').removeClass('hidden');
            const staffUser = resData.data;
            const fullName = `${staffUser.prefix || ''}${staffUser.firstname} ${staffUser.lastname}`;

            // บันทึกข้อมูลส่วนตัวของ User
            localStorage.setItem("staff_token", staffUser.remember_token);
            localStorage.setItem("staff_name", fullName);
            localStorage.setItem("staff_user_id", staffUser.id);

            // 🔴 แก้ไขจุดที่ 1: ต้อง stringify Object ก่อนบันทึกลง localStorage
            localStorage.setItem("twman", JSON.stringify(staffUser));

            const profiles = staffUser.staff_profiles || [];

            console.log('profiles', profiles);

            // 🟢 2. เช็คจำนวนองค์กร (Multi-Org Logic)
            if (profiles.length === 1) {
                // มี 1 องค์กร: เซ็ตค่าแล้วเข้าหน้า Main ทันที
                const singleOrg = profiles[0];

                if (typeof setStaffActiveOrg === "function") {
                    setStaffActiveOrg(singleOrg.org_id_fk, singleOrg.id);
                }

                // 🔴 ซ่อนหน้า Login / หน้าเลือก Org แล้วแสดงหน้า Main
                const loginScreen = document.getElementById('loginScreen');
                const selectOrgScreen = document.getElementById('selectOrgScreen');
                const mainScreen = document.getElementById('mainScreen') || document.getElementById('mainAppScreen');

                if (loginScreen) {
                    loginScreen.style.display = 'none';
                    loginScreen.classList.add('is-hidden');
                }
                if (selectOrgScreen) {
                    selectOrgScreen.style.display = 'none';
                    selectOrgScreen.classList.add('is-hidden');
                }
                if (mainScreen) {
                    mainScreen.style.display = 'block';
                    mainScreen.classList.remove('is-hidden');
                }

                // 🟢 ตรวจสอบว่ามีฟังก์ชัน showMainScreen จริงหรือไม่ก่อนเรียกใช้งาน
                if (typeof showMainScreen === "function") {
                    showMainScreen(fullName);
                } else {
                    console.warn("⚠️ ไม่พบฟังก์ชัน showMainScreen แต่สลับหน้าจอให้เรียบร้อยแล้ว");
                }

            } else if (profiles.length > 1) {
                // มีหลายองค์กร: Render ให้เลือกลงในหน้า selectOrgScreen
                if (typeof renderOrgSelectionList === "function") {
                    renderOrgSelectionList(profiles, fullName);
                }

                const loginScreen = document.getElementById('loginScreen');
                const selectOrgScreen = document.getElementById('selectOrgScreen');

                if (loginScreen) {
                    loginScreen.style.display = 'none';
                    loginScreen.classList.add('is-hidden');
                }
                if (selectOrgScreen) {
                    selectOrgScreen.style.display = 'block';
                    selectOrgScreen.classList.remove('is-hidden');
                }
            } else {
                throw new Error("ไม่พบข้อมูลสิทธิ์เจ้าหน้าที่ในระบบ");
            }

        } else {
            if (loginError) {
                loginError.innerText = resData.message || "ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง";
                loginError.style.display = "block";
            }
        }
    } catch (error) {
        if (loginError) {
            loginError.innerText = error.message || "เกิดข้อผิดพลาด: ไม่สามารถเชื่อมต่อกับฐานข้อมูลระบบได้";
            loginError.style.display = "block";
        }
        console.error("API Connection Error:", error);
    } finally {
        btnSubmit.disabled = false;
        btnSubmit.innerText = "🔓 เข้าสู่ระบบ";
    }
}
test()