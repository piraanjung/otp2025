{{-- <div id="recycleBankScreen" class="screen">
    <div class="sub-header">
        <button onclick="backToMenu()" class="btn-back">⬅️ กลับเมนูหลัก</button>
        <h3 style="margin: 0;">ธนาคารขยะรีไซเคิล</h3>
    </div>

    <div class="container" style="padding-bottom: 80px;"> <!-- เผื่อพื้นที่ด้านล่าง -->
        <!-- Search & Scan Bar -->
        <div class="card" style="padding: 12px; margin-bottom: 12px;">
            <div class="search-wrapper" style="display: flex; gap: 8px;">
                <input type="text" id="searchMemberRecycleInput" placeholder="🔍 ค้นหาชื่อ หรือเบอร์โทร..."
                    oninput="filterMembers()"
                    style="flex: 1; padding: 8px; border: 1px solid #ddd; border-radius: 6px;">
                <button id="btnScanQR" class="btn-scan" onclick="openScanModal()"
                    style="background: #007bff; color: white; border: none; padding: 8px 12px; border-radius: 6px; cursor: pointer;">📷
                    สแกน</button>
            </div>
        </div>

        <h4 class="section-title" style="font-size: 14px; color: #666; margin-bottom: 8px;">รายชื่อสมาชิกในระบบ (<span
                id="memberCount">2</span> คน)</h4>

        <!-- Tabs -->
        <div class="tab-container"
            style="display: flex; margin-bottom: 15px; background: #eee; padding: 4px; border-radius: 8px;">
            <button id="btnTabPending" onclick="switchTab('pending')"
                style="flex: 1; padding: 8px; border: none; border-radius: 6px; font-weight: bold; background: #007bff; color: white; cursor: pointer; font-size: 13px;">
                ⏳ รอรับซื้อ (<span id="countPending">2</span>)
            </button>
            <button id="btnTabCompleted" onclick="switchTab('completed')"
                style="flex: 1; padding: 8px; border: none; border-radius: 6px; font-weight: bold; background: transparent; color: #333; cursor: pointer; font-size: 13px;">
                ✓ รับซื้อแล้ว (<span id="countCompleted">0</span>)
            </button>
        </div>

        <!-- Member List Container -->
        <div id="memberListContainer">
            <div class="member-card"
                style="background: #fff; padding: 12px; border-radius: 8px; margin-bottom: 10px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); display: flex; justify-content: space-between; align-items: center;">
                <div class="member-info">
                    <p style="font-weight: bold; font-size: 15px; margin: 0 0 4px 0;">ทองม้วน แสงวงศ์</p>
                    <p style="margin: 0 0 4px 0; font-size: 13px; color: #555;">📞 89700000</p>
                    <span class="badge-account"
                        style="background: #e6f0fa; color: #007bff; padding: 2px 6px; border-radius: 4px; font-size: 11px;">RC-002-37540001</span>
                </div>
                <div>
                    <button class="btn-deposit" onclick="selectMemberToDeposit(1)"
                        style="background: #28a745; color: white; border: none; padding: 8px 12px; border-radius: 6px; cursor: pointer; font-weight: bold; font-size: 13px;">
                        💰 รับซื้อ
                    </button>
                </div>
            </div>

            <div class="member-card"
                style="background: #fff; padding: 12px; border-radius: 8px; margin-bottom: 10px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); display: flex; justify-content: space-between; align-items: center;">
                <div class="member-info">
                    <p style="font-weight: bold; font-size: 15px; margin: 0 0 4px 0;">ศักดิ์สิทธิ์ สวนดี</p>
                    <p style="margin: 0 0 4px 0; font-size: 13px; color: #555;">📞 ไม่มีข้อมูล</p>
                    <span class="badge-account"
                        style="background: #e6f0fa; color: #007bff; padding: 2px 6px; border-radius: 4px; font-size: 11px;">RC-002-37550002</span>
                </div>
                <div>
                    <button class="btn-deposit" onclick="selectMemberToDeposit(3408)"
                        style="background: #28a745; color: white; border: none; padding: 8px 12px; border-radius: 6px; cursor: pointer; font-weight: bold; font-size: 13px;">
                        💰 รับซื้อ
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ตัวอย่างโครงสร้าง Modal / Bottom Sheet (ซ่อนไว้ก่อน เปิดด้วย JS) -->
    <div id="depositModal"
        style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: flex-end;">
        <div
            style="background: white; width: 100%; max-height: 85vh; border-top-left-radius: 16px; border-top-right-radius: 16px; padding: 20px; overflow-y: auto; box-sizing: border-box;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <h3 style="margin: 0;">บันทึกรับซื้อขยะรีไซเคิล</h3>
                <button onclick="closeDepositModal()"
                    style="background: none; border: none; font-size: 18px; cursor: pointer;">✕</button>
            </div>
            <!-- เนื้อหาฟอร์มรับซื้อขยะจะแสดงที่นี่ -->
            <div id="depositModalContent">
                <p>เลือกรายการขยะและน้ำหนัก...</p>
            </div>
        </div>
    </div>
</div> --}}


<div class="sub-header">
    {{-- <button onclick="backToMenu()" class="btn-back">⬅️ กลับเมนูหลัก</button> --}}
    <h3 style="margin: 0; font-size: 18px;">ธนาคารขยะรีไซเคิล</h3>
    <div style="width: 50px;"></div>
</div>



<!-- ส่วนจำลอง Podiums (Top 3 ผู้นำขยะสะสม) เพิ่ม onclick และ active class -->
<div class="podium-container">
    <!-- อันดับ 2 -->
    <div class="podium-item" onclick="togglePodium(this)">
        <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=150" class="podium-avatar">
        <div class="podium-box second">
            <p
                style="font-weight: bold; font-size: 13px; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                Teresa</p>
            <span
                style="font-size: 10px; color: #ff6600; background: #fff3e0; padding: 1px 4px; border-radius: 4px;">VIP</span>
            <p style="font-size: 10px; color: #777; margin: 3px 0 0 0;">82130 vals</p>
        </div>
    </div>
    <!-- อันดับ 1 (ตั้งค่า active เริ่มต้นไว้ให้เด่นขึ้น) -->
    <div class="podium-item active" onclick="togglePodium(this)">
        <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=150" class="podium-avatar"
            style="width: 65px; height: 65px;">
        <div class="podium-box first">
            <p
                style="font-weight: bold; font-size: 14px; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                Amanda</p>
            <span
                style="font-size: 10px; color: #ff6600; background: #fff3e0; padding: 1px 4px; border-radius: 4px;">SVIP</span>
            <p style="font-size: 10px; color: #ff6600; font-weight: bold; margin: 3px 0 0 0;">83947 vals</p>
        </div>
    </div>
    <!-- อันดับ 3 -->
    <div class="podium-item" onclick="togglePodium(this)">
        <img src="https://images.unsplash.com/photo-1517841905240-472988babdf9?w=150" class="podium-avatar">
        <div class="podium-box third">
            <p
                style="font-weight: bold; font-size: 13px; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                Rita</p>
            <span
                style="font-size: 10px; color: #ff6600; background: #fff3e0; padding: 1px 4px; border-radius: 4px;">VIP</span>
            <p style="font-size: 10px; color: #777; margin: 3px 0 0 0;">75047 vals</p>
        </div>
    </div>
</div>

<!-- แผ่นกระดาษสีขาวโค้งด้านล่าง บรรจุเนื้อหาเดิมของคุณ -->
<div class="white-sheet-container">
    <div class="container" style="padding: 0;">
        <div class="card" style="padding: 12px; margin-bottom: 15px;">
            <div class="search-wrapper" style="display: flex; gap: 8px;">
                <input type="text" id="searchMemberRecycleInput" placeholder="🔍 ค้นหาชื่อ, นามสกุล หรือเบอร์โทร..."
                    oninput="filterMembers()"
                    style="flex: 1; padding: 8px 12px; border: 1px solid #ddd; border-radius: 8px; outline: none; font-size: 14px;">
                <button id="btnScanQR" class="btn-scan"
                    style="background: #ff6600; color: white; border: none; padding: 8px 14px; border-radius: 8px; cursor: pointer; font-weight: bold;">📷
                    สแกน QR</button>
            </div>
        </div>

        <h4 class="section-title" style="font-size: 15px; color: #444; margin-bottom: 12px;">รายชื่อสมาชิกในระบบ (<span
                id="memberCount">2</span> คน)</h4>

        <div class="tab-container"
            style="display: flex; margin-bottom: 15px; background: #f1f3f5; padding: 4px; border-radius: 8px;">
            <button id="btnTabPending" onclick="switchTab('pending')"
                style="flex: 1; padding: 8px; border: none; border-radius: 6px; font-weight: bold; background: #ff6600; color: white; cursor: pointer; font-size: 13px;">
                ⏳ รอรับซื้อ (<span id="countPending">2</span>)
            </button>
            <button id="btnTabCompleted" onclick="switchTab('completed')"
                style="flex: 1; padding: 8px; border: none; border-radius: 6px; font-weight: bold; background: transparent; color: #555; cursor: pointer; font-size: 13px;">
                ✓ รับซื้อแล้ววันนี้ (<span id="countCompleted">0</span>)
            </button>
        </div>

        <div id="memberListContainer"></div>
    </div>
</div>

<script>
    // ตัวแปร Global สำหรับเก็บข้อมูลสมาชิกธนาคารขยะทั้งหมดในเซสชันนี้
let cachedRecycleMembers = [];
let currentRecycleTab = 'pending'; // 'pending' หรือ 'completed'

/**
 * 🟢 1. โหลดข้อมูลสมาชิกธนาคารขยะมาเก็บไว้ครั้งเดียว (Session Storage / Global Variable)
 */
async function loadRecycleBankMembers() {
    const staffId = localStorage.getItem('staff_id');
    const orgId = localStorage.getItem('staff_org_id') || 1;

    if (!staffId) {
        console.warn('ไม่พบข้อมูลเจ้าหน้าที่');
        return;
    }

    // แสดงสถานะกำลังโหลดในรายการสมาชิก
    $('#memberListContainer').html(`
        <div class="text-center py-5">
            <div class="spinner-border text-warning" role="status"></div>
            <p class="mt-2 text-muted">กำลังโหลดรายชื่อสมาชิกธนาคารขยะ...</p>
        </div>
    `);

    try {
        const response = await fetch(`${API_BASE_URL}/staff/recycle/members`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'ngrok-skip-browser-warning': 'true'
            },
            body: JSON.stringify({
                staff_id: staffId,
                org_id: orgId
            })
        });

        const resData = await response.json();

        if (resData.code !== 200 || !resData.data) {
            $('#memberListContainer').html(`<div class="text-center text-danger py-4">${resData.message || 'ไม่สามารถโหลดข้อมูลสมาชิกได้'}</div>`);
            return;
        }

        // เก็บข้อมูลลงในตัวแปร Global และเก็บสำรองใน sessionStorage
        cachedRecycleMembers = resData.data; 
        sessionStorage.setItem('cached_recycle_members', JSON.stringify(cachedRecycleMembers));

        // เรนเดอร์ข้อมูลแสดงผล
        renderRecycleMemberList();

    } catch (error) {
        console.error("API Error:", error);
        
        // กรณีออฟไลน์หรือเน็ตหลุด ลองดึงจาก sessionStorage เผื่อมีข้อมูลเก่าเก็บไว้
        const localCached = sessionStorage.getItem('cached_recycle_members');
        if (localCached) {
            cachedRecycleMembers = JSON.parse(localCached);
            renderRecycleMemberList();
            console.warn('ใช้ข้อมูลสำรองจาก Session Storage');
        } else {
            $('#memberListContainer').html(`<div class="text-center text-danger py-4">เกิดข้อผิดพลาดในการเชื่อมต่อเซิร์ฟเวอร์</div>`);
        }
    }
}

/**
 * 🟢 2. ฟังก์ชันแสดงผลรายชื่อสมาชิก (กรองตามแท็บและคำค้นหา)
 */
function renderRecycleMemberList() {
    const keyword = $('#searchMemberRecycleInput').val().toLowerCase().trim();
    
    // กรองข้อมูลตาม Tab (pending = ยังไม่รับซื้อวันนี้, completed = รับซื้อแล้ววันนี้)
    let filtered = cachedRecycleMembers.filter(m => {
        const matchTab = (currentRecycleTab === 'pending') ? (m.status_today === 'pending') : (m.status_today === 'completed');
        
        if (!matchTab) return false;

        if (keyword === '') return true;
        
        const fullName = `${m.firstname} ${m.lastname}`.toLowerCase();
        const phone = (m.phone || '').toLowerCase();
        const accountNo = (m.account_no || '').toLowerCase();

        return fullName.includes(keyword) || phone.includes(keyword) || accountNo.includes(keyword);
    });

    // อัปเดตจำนวนตัวเลขบน UI
    const pendingCount = cachedRecycleMembers.filter(m => m.status_today === 'pending').length;
    const completedCount = cachedRecycleMembers.filter(m => m.status_today === 'completed').length;
    
    $('#memberCount').text(cachedRecycleMembers.length);
    $('#countPending').text(pendingCount);
    $('#countCompleted').text(completedCount);

    let html = '';
    if (filtered.length === 0) {
        html = `<div class="text-center text-muted py-4">ไม่พบรายชื่อสมาชิกในรายการนี้</div>`;
    } else {
        filtered.forEach(m => {
            const btnAction = (currentRecycleTab === 'pending')
                ? `<button class="btn-deposit" onclick="selectMemberToDeposit(${m.member_id})" style="background: #ff6600; color: white; border: none; padding: 8px 12px; border-radius: 6px; cursor: pointer; font-weight: bold; font-size: 13px;"> 💰 รับซื้อ </button>`
                : `<span class="badge bg-success p-2" style="font-size: 12px;">✓ รับซื้อแล้ว</span>`;

            html += `
                <div class="member-card" style="background: #fff; padding: 12px; border-radius: 8px; margin-bottom: 10px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); display: flex; justify-content: space-between; align-items: center;">
                    <div class="member-info">
                        <p style="font-weight: bold; font-size: 15px; margin: 0 0 4px 0; color: #333;">${m.firstname} ${m.lastname}</p>
                        <p style="margin: 0 0 4px 0; font-size: 13px; color: #555;">📞 ${m.phone || 'ไม่มีข้อมูล'}</p>
                        <span class="badge-account" style="background: #fff3e0; color: #ff6600; padding: 2px 6px; border-radius: 4px; font-size: 11px; font-weight: bold;">${m.account_no}</span>
                    </div>
                    <div>
                        ${btnAction}
                    </div>
                </div>
            `;
        });
    }

    $('#memberListContainer').html(html);
}

/**
 * 🟢 3. สลับแท็บ (รอรับซื้อ / รับซื้อแล้ว)
 */
function switchTab(tabName) {
    currentRecycleTab = tabName;
    if (tabName === 'pending') {
        $('#btnTabPending').css({ 'background': '#ff6600', 'color': 'white' });
        $('#btnTabCompleted').css({ 'background': 'transparent', 'color': '#555' });
    } else {
        $('#btnTabPending').css({ 'background': 'transparent', 'color': '#555' });
        $('#btnTabCompleted').css({ 'background': '#ff6600', 'color': 'white' });
    }
    renderRecycleMemberList();
}

/**
 * 🟢 4. ฟังก์ชันค้นหาแบบ Real-time เมื่อพิมพ์ในช่อง input
 */
function filterMembers() {
    renderRecycleMemberList();
}

/**
 * 🟢 5. จำลองการบันทึกรับซื้อสำเร็จ และอัปเดตสถานะในตัวแปรทันทีโดยไม่ต้องโหลด API ใหม่ทั้งหมด
 */
function markMemberAsCompleted(memberId) {
    // ค้นหาและเปลี่ยนสถานะในอาเรย์หลัก
    const memberIndex = cachedRecycleMembers.findIndex(m => m.member_id === memberId);
    if (memberIndex !== -1) {
        cachedRecycleMembers[memberIndex].status_today = 'completed';
        // อัปเดต sessionStorage ด้วย
        sessionStorage.setItem('cached_recycle_members', JSON.stringify(cachedRecycleMembers));
    }
    // เรนเดอร์หน้าจอใหม่ทันที
    renderRecycleMemberList();
}
</script>