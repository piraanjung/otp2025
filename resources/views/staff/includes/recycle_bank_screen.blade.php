
{{-- <div id="recycleBankScreen" class="screen">
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
</div> --}}
<style>
        #recycleBankScreen {
            background: linear-gradient(135deg, #ff9933 0%, #ff6600 100%);
            min-height: 100vh;
            color: #fff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            box-sizing: border-box;
            position: relative;
            overflow-x: hidden;
        }
        #recycleBankScreen .sub-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 15px 20px;
        }
        #recycleBankScreen .btn-back {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: #fff;
            padding: 6px 12px;
            border-radius: 20px;
            cursor: pointer;
            font-weight: bold;
        }
        /* Top Navigation Bar สำหรับกดสลับหัวข้อ */
        .ranking-navbar {
            display: flex;
            justify-content: space-around;
            background: rgba(0, 0, 0, 0.1);
            margin: 0 20px 15px 20px;
            padding: 4px;
            border-radius: 30px;
        }
        .nav-pill-btn {
            background: transparent;
            border: none;
            color: rgba(255, 255, 255, 0.7);
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 13px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .nav-pill-btn.active {
            background: #ffffff;
            color: #ff6600;
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        }
        /* Podium Section พร้อม Transform Animation เมื่อคลิก */
        .podium-container {
            display: flex;
            justify-content: center;
            align-items: flex-end;
            gap: 12px;
            padding: 10px 20px 0 20px;
        }
        .podium-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            cursor: pointer;
            transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275), filter 0.3s ease;
        }
        .podium-item:hover {
            transform: translateY(-5px);
        }
        .podium-item.active {
            transform: scale(1.12) translateY(-10px);
            z-index: 10;
            filter: drop-shadow(0 15px 25px rgba(0,0,0,0.25));
        }
        .podium-avatar {
            width: 55px;
            height: 55px;
            border-radius: 50%;
            border: 2px solid #fff;
            object-fit: cover;
            margin-bottom: -15px;
            z-index: 2;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            transition: transform 0.3s ease, border-color 0.3s ease;
        }
        .podium-item.active .podium-avatar {
            transform: scale(1.1);
            border-color: #ffcc00;
        }
        .podium-box {
            background: rgba(255, 255, 255, 0.9);
            color: #333;
            width: 90px;
            border-radius: 12px 12px 0 0;
            padding: 18px 8px 10px 8px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            height: 80px;
        }
        .podium-box.active { height: 110px; background: #fff; }
        /* .podium-box.second { height: 90px; }
        .podium-box.third { height: 80px; } */

        /* กล่องสีขาวโค้งด้านล่างสำหรับรายชื่อ */
        .white-sheet-container {
            position: absolute;
            z-index: 100;
            margin-top: -5px;
            background: #ffffff;
            color: #333;
            border-top-left-radius: 30px;
            border-top-right-radius: 30px;
            padding: 20px;
            min-height: 50vh;
            box-shadow: 0 -10px 30px rgba(0,0,0,0.1);
        }
        #recycleBankScreen .card {
            background: #fdfdfd;
            border: 1px solid #eee;
            border-radius: 12px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.03);
        }
        #recycleBankScreen .member-card {
            background: #fff;
            padding: 12px;
            border-radius: 12px;
            margin-bottom: 10px;
            border: 1px solid #f0f0f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.02);
        }
    </style>

<div id="recycleBankScreen" class="screen is-hidden">
    <div class="sub-header">
        {{-- <button onclick="backToMenu()" class="btn-back">⬅️ กลับเมนูหลัก</button> --}}
        <h3 style="margin: 0; font-size: 18px;">ธนาคารขยะรีไซเคิล</h3>
        <div style="width: 50px;"></div>
    </div>

    <!-- Navbar ปุ่มกดดูรายการของแต่ละหัวข้อ -->
    {{-- <div class="ranking-navbar">
        <button class="nav-pill-btn active" onclick="switchRankingTab('daily', this)">รายวัน</button>
        <button class="nav-pill-btn" onclick="switchRankingTab('weekly', this)">รายสัปดาห์</button>
        <button class="nav-pill-btn" onclick="switchRankingTab('monthly', this)">รายเดือน</button>
    </div> --}}

    <!-- ส่วนจำลอง Podiums (Top 3 ผู้นำขยะสะสม) เพิ่ม onclick และ active class -->
    <div class="podium-container">
        <!-- อันดับ 2 -->
        <div class="podium-item" onclick="togglePodium(this)">
            <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=150" class="podium-avatar">
            <div class="podium-box second">
                <p style="font-weight: bold; font-size: 13px; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Teresa</p>
                <span style="font-size: 10px; color: #ff6600; background: #fff3e0; padding: 1px 4px; border-radius: 4px;">VIP</span>
                <p style="font-size: 10px; color: #777; margin: 3px 0 0 0;">82130 vals</p>
            </div>
        </div>
        <!-- อันดับ 1 (ตั้งค่า active เริ่มต้นไว้ให้เด่นขึ้น) -->
        <div class="podium-item active" onclick="togglePodium(this)">
            <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=150" class="podium-avatar" style="width: 65px; height: 65px;">
            <div class="podium-box first">
                <p style="font-weight: bold; font-size: 14px; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Amanda</p>
                <span style="font-size: 10px; color: #ff6600; background: #fff3e0; padding: 1px 4px; border-radius: 4px;">SVIP</span>
                <p style="font-size: 10px; color: #ff6600; font-weight: bold; margin: 3px 0 0 0;">83947 vals</p>
            </div>
        </div>
        <!-- อันดับ 3 -->
        <div class="podium-item" onclick="togglePodium(this)">
            <img src="https://images.unsplash.com/photo-1517841905240-472988babdf9?w=150" class="podium-avatar">
            <div class="podium-box third">
                <p style="font-weight: bold; font-size: 13px; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Rita</p>
                <span style="font-size: 10px; color: #ff6600; background: #fff3e0; padding: 1px 4px; border-radius: 4px;">VIP</span>
                <p style="font-size: 10px; color: #777; margin: 3px 0 0 0;">75047 vals</p>
            </div>
        </div>
    </div>

    <!-- แผ่นกระดาษสีขาวโค้งด้านล่าง บรรจุเนื้อหาเดิมของคุณ -->
    <div class="white-sheet-container">
        <div class="container" style="padding: 0;">
            <div class="card" style="padding: 12px; margin-bottom: 15px;">
                <div class="search-wrapper" style="display: flex; gap: 8px;">
                    <input type="text" id="searchMemberRecycleInput" placeholder="🔍 ค้นหาชื่อ, นามสกุล หรือเบอร์โทร..." oninput="filterMembers()" style="flex: 1; padding: 8px 12px; border: 1px solid #ddd; border-radius: 8px; outline: none; font-size: 14px;">
                    <button id="btnScanQR" class="btn-scan" style="background: #ff6600; color: white; border: none; padding: 8px 14px; border-radius: 8px; cursor: pointer; font-weight: bold;">📷 สแกน QR</button>
                </div>
            </div>

            <h4 class="section-title" style="font-size: 15px; color: #444; margin-bottom: 12px;">รายชื่อสมาชิกในระบบ (<span id="memberCount">2</span> คน)</h4>

            <div class="tab-container" style="display: flex; margin-bottom: 15px; background: #f1f3f5; padding: 4px; border-radius: 8px;">
                <button id="btnTabPending" onclick="switchTab('pending')" style="flex: 1; padding: 8px; border: none; border-radius: 6px; font-weight: bold; background: #ff6600; color: white; cursor: pointer; font-size: 13px;">
                    ⏳ รอรับซื้อ (<span id="countPending">2</span>)
                </button>
                <button id="btnTabCompleted" onclick="switchTab('completed')" style="flex: 1; padding: 8px; border: none; border-radius: 6px; font-weight: bold; background: transparent; color: #555; cursor: pointer; font-size: 13px;">
                    ✓ รับซื้อแล้ววันนี้ (<span id="countCompleted">0</span>)
                </button>
            </div>

            <div id="memberListContainer"></div>
        </div>
    </div>

    <!-- JavaScript สำหรับควบคุมแอนิเมชันและ Navbar -->
    <script>
        function switchRankingTab(type, element) {
            const buttons = document.querySelectorAll('.ranking-navbar .nav-pill-btn');
            buttons.forEach(btn => btn.classList.remove('active'));
            element.classList.add('active');
            console.log("Switched ranking view to:", type);
        }

        function togglePodium(element) {
            const items = document.querySelectorAll('.podium-item');
            items.forEach(item => {
                if (item !== element) {
                    item.classList.remove('active');
                }
            });
            switchRankingTab('daily', this)
            element.classList.toggle('active');
        }
    </script>
</div>