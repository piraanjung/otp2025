
<div id="recycleBankScreen" class="screen">
    <div class="sub-header">
        <button onclick="backToMenu()" class="btn-back">⬅️ กลับเมนูหลัก</button>
        <h3 style="margin: 0;">ธนาคารขยะรีไซเคิล</h3>
    </div>

    <div class="container" style="padding-bottom: 80px;"> <!-- เผื่อพื้นที่ด้านล่าง -->
        <!-- Search & Scan Bar -->
        <div class="card" style="padding: 12px; margin-bottom: 12px;">
            <div class="search-wrapper" style="display: flex; gap: 8px;">
                <input type="text" id="searchMemberRecycleInput" placeholder="🔍 ค้นหาชื่อ หรือเบอร์โทร..." oninput="filterMembers()" style="flex: 1; padding: 8px; border: 1px solid #ddd; border-radius: 6px;">
                <button id="btnScanQR" class="btn-scan" onclick="openScanModal()" style="background: #007bff; color: white; border: none; padding: 8px 12px; border-radius: 6px; cursor: pointer;">📷 สแกน</button>
            </div>
        </div>

        <h4 class="section-title" style="font-size: 14px; color: #666; margin-bottom: 8px;">รายชื่อสมาชิกในระบบ (<span id="memberCount">2</span> คน)</h4>

        <!-- Tabs -->
        <div class="tab-container" style="display: flex; margin-bottom: 15px; background: #eee; padding: 4px; border-radius: 8px;">
            <button id="btnTabPending" onclick="switchTab('pending')" style="flex: 1; padding: 8px; border: none; border-radius: 6px; font-weight: bold; background: #007bff; color: white; cursor: pointer; font-size: 13px;">
                ⏳ รอรับซื้อ (<span id="countPending">2</span>)
            </button>
            <button id="btnTabCompleted" onclick="switchTab('completed')" style="flex: 1; padding: 8px; border: none; border-radius: 6px; font-weight: bold; background: transparent; color: #333; cursor: pointer; font-size: 13px;">
                ✓ รับซื้อแล้ว (<span id="countCompleted">0</span>)
            </button>
        </div>

        <!-- Member List Container -->
        <div id="memberListContainer">
            <div class="member-card" style="background: #fff; padding: 12px; border-radius: 8px; margin-bottom: 10px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); display: flex; justify-content: space-between; align-items: center;">
                <div class="member-info">
                    <p style="font-weight: bold; font-size: 15px; margin: 0 0 4px 0;">ทองม้วน แสงวงศ์</p>
                    <p style="margin: 0 0 4px 0; font-size: 13px; color: #555;">📞 89700000</p>
                    <span class="badge-account" style="background: #e6f0fa; color: #007bff; padding: 2px 6px; border-radius: 4px; font-size: 11px;">RC-002-37540001</span>
                </div>
                <div>
                    <button class="btn-deposit" onclick="selectMemberToDeposit(1)" style="background: #28a745; color: white; border: none; padding: 8px 12px; border-radius: 6px; cursor: pointer; font-weight: bold; font-size: 13px;">
                        💰 รับซื้อ
                    </button>
                </div>
            </div>
            
            <div class="member-card" style="background: #fff; padding: 12px; border-radius: 8px; margin-bottom: 10px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); display: flex; justify-content: space-between; align-items: center;">
                <div class="member-info">
                    <p style="font-weight: bold; font-size: 15px; margin: 0 0 4px 0;">ศักดิ์สิทธิ์ สวนดี</p>
                    <p style="margin: 0 0 4px 0; font-size: 13px; color: #555;">📞 ไม่มีข้อมูล</p>
                    <span class="badge-account" style="background: #e6f0fa; color: #007bff; padding: 2px 6px; border-radius: 4px; font-size: 11px;">RC-002-37550002</span>
                </div>
                <div>
                    <button class="btn-deposit" onclick="selectMemberToDeposit(3408)" style="background: #28a745; color: white; border: none; padding: 8px 12px; border-radius: 6px; cursor: pointer; font-weight: bold; font-size: 13px;">
                        💰 รับซื้อ
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ตัวอย่างโครงสร้าง Modal / Bottom Sheet (ซ่อนไว้ก่อน เปิดด้วย JS) -->
    <div id="depositModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: flex-end;">
        <div style="background: white; width: 100%; max-height: 85vh; border-top-left-radius: 16px; border-top-right-radius: 16px; padding: 20px; overflow-y: auto; box-sizing: border-box;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <h3 style="margin: 0;">บันทึกรับซื้อขยะรีไซเคิล</h3>
                <button onclick="closeDepositModal()" style="background: none; border: none; font-size: 18px; cursor: pointer;">✕</button>
            </div>
            <!-- เนื้อหาฟอร์มรับซื้อขยะจะแสดงที่นี่ -->
            <div id="depositModalContent">
                <p>เลือกรายการขยะและน้ำหนัก...</p>
            </div>
        </div>
    </div>
</div> 
