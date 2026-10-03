  {{-- <div id="mainScreen" class="iss-hidden">
        <div class="container">
            <h3 class="section-title">เมนูบริการระบบสนาม</h3>

            <div class="menu-grid">
                <div class="menu-item card-water" onclick="navigateTo('water')">
                    <div class="menu-icon">💧</div>
                    <div class="menu-title">งานประปา</div>
                    <div class="menu-desc">จดมาตรวัดน้ำ, แจ้งท่อแตก/ซ่อมแซม</div>
                </div>

                <div class="menu-item card-recycle" onclick="navigateTo('recycle')">
                    <div class="menu-icon">♻️</div>
                    <div class="menu-title">ธนาคารขยะรีไซเคิล</div>
                    <div class="menu-desc">บันทึกรับขยะ, เช็คยอดเงิน, สมัครสมาชิก</div>
                </div>

                <div class="menu-item card-organic" onclick="navigateTo('organic')">
                    <div class="menu-icon">🍂</div>
                    <div class="menu-title">ธนาคารขยะเปียก</div>
                    <div class="menu-desc">ตรวจประเมินถังขยะ, บันทึกพิกัดคาร์บอน</div>
                </div>

                 <div class="menu-item card-inventory" onclick="navigateTo('inventory')">
                    <div class="menu-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" class="bi bi-house-gear" viewBox="0 0 16 16">
  <path d="M7.293 1.5a1 1 0 0 1 1.414 0L11 3.793V2.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v3.293l2.354 2.353a.5.5 0 0 1-.708.708L8 2.207l-5 5V13.5a.5.5 0 0 0 .5.5h4a.5.5 0 0 1 0 1h-4A1.5 1.5 0 0 1 2 13.5V8.207l-.646.647a.5.5 0 1 1-.708-.708z"/>
  <path d="M11.886 9.46c.18-.613 1.048-.613 1.229 0l.043.148a.64.64 0 0 0 .921.382l.136-.074c.561-.306 1.175.308.87.869l-.075.136a.64.64 0 0 0 .382.92l.149.045c.612.18.612 1.048 0 1.229l-.15.043a.64.64 0 0 0-.38.921l.074.136c.305.561-.309 1.175-.87.87l-.136-.075a.64.64 0 0 0-.92.382l-.045.149c-.18.612-1.048.612-1.229 0l-.043-.15a.64.64 0 0 0-.921-.38l-.136.074c-.561.305-1.175-.309-.87-.87l.075-.136a.64.64 0 0 0-.382-.92l-.148-.044c-.613-.181-.613-1.049 0-1.23l.148-.043a.64.64 0 0 0 .382-.921l-.074-.136c-.306-.561.308-1.175.869-.87l.136.075a.64.64 0 0 0 .92-.382zM14 12.5a1.5 1.5 0 1 0-3 0 1.5 1.5 0 0 0 3 0"/>
</svg>
                    </div>
                    
                    <div class="menu-title">ยืม/คืน พัสดุ</div>
                    <div class="menu-desc">ยืม/คืน พัสดุ</div>
                </div>

                <div class="menu-item card-settings" onclick="navigateTo('settings')">
                    <div class="menu-icon">🖨️</div>
                    <div class="menu-title">ตั้งค่าเครื่องพิมพ์</div>
                    <div class="menu-desc">เชื่อมต่อ Bluetooth Thermal Printer</div>
                </div>

               
            </div>
        </div>
    </div> --}}
    <div id="mainScreen" class="iss-hidden">
    <div class="container-fluid px-0">
        <h3 class="section-title px-3 pt-3 mb-3">เมนูบริการระบบสนาม</h3>

        <!-- Layout แบบแบ่ง 2 คอลัมน์: ซ้ายเป็นแถบเมนูแนวตั้ง, ขวาเป็นพื้นที่แสดงผลหลัก -->
        <div class="row g-0">
            
            <!-- แถบเมนูด้านข้าง (Vertical Sidebar Menu) -->
            <div class="col-4 menu-sidebar d-flex flex-column align-items-center py-4">
                <div class="menu-grid-vertical w-100 px-2">

                    <!-- เมนูที่ 1: งานประปา -->
                    <div class="menu-item-side active" onclick="navigateTo('water', this)">
                        <div class="menu-icon">💧</div>
                    </div>

                    <!-- เมนูที่ 2: ธนาคารขยะรีไซเคิล -->
                    <div class="menu-item-side" onclick="navigateTo('recycle', this)">
                        <div class="menu-icon">♻️</div>
                    </div>

                    <!-- เมนูที่ 3: ธนาคารขยะเปียก -->
                    <div class="menu-item-side" onclick="navigateTo('organic', this)">
                        <div class="menu-icon">🍂</div>
                    </div>

                    <!-- เมนูที่ 4: ยืม/คืน พัสดุ -->
                    <div class="menu-item-side" onclick="navigateTo('inventory', this)">
                        <div class="menu-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" class="bi bi-house-gear" viewBox="0 0 16 16">
                                <path d="M7.293 1.5a1 1 0 0 1 1.414 0L11 3.793V2.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v3.293l2.354 2.353a.5.5 0 0 1-.708.708L8 2.207l-5 5V13.5a.5.5 0 0 0 .5.5h4a.5.5 0 0 1 0 1h-4A1.5 1.5 0 0 1 2 13.5V8.207l-.646.647a.5.5 0 1 1-.708-.708z"/>
                                <path d="M11.886 9.46c.18-.613 1.048-.613 1.229 0l.043.148a.64.64 0 0 0 .921.382l.136-.074c.561-.306 1.175.308.87.869l-.075.136a.64.64 0 0 0 .382.92l.149.045c.612.18.612 1.048 0 1.229l-.15.043a.64.64 0 0 0-.38.921l.074.136c.305.561-.309 1.175-.87.87l-.136-.075a.64.64 0 0 0-.92.382l-.045.149c-.18.612-1.048.612-1.229 0l-.043-.15a.64.64 0 0 0-.921-.38l-.136.074c-.561.305-1.175-.309-.87-.87l.075-.136a.64.64 0 0 0-.382-.92l-.148-.044c-.613-.181-.613-1.049 0-1.23l.148-.043a.64.64 0 0 0 .382-.921l-.074-.136c-.306-.561.308-1.175.869-.87l.136.075a.64.64 0 0 0 .92-.382zM14 12.5a1.5 1.5 0 1 0-3 0 1.5 1.5 0 0 0 3 0"/>
                            </svg>
                        </div>
                    </div>

                    <!-- เมนูที่ 5: ตั้งค่าเครื่องพิมพ์ -->
                    <div class="menu-item-side" onclick="navigateTo('settings', this)">
                        <div class="menu-icon">🖨️</div>
                    </div>

                </div>
            </div>

            <!-- พื้นที่แสดงรายละเอียดเมนูที่ถูกเลือก (Main Content Area) -->
            <div class="col-8 menu-content-area p-4 d-flex flex-column justify-content-center">
                <div id="contentDisplay">
                    <h4 id="menuTitle" class="fw-bold text-white mb-2">งานประปา</h4>
                    <p id="menuDesc" class="text-light small">จดมาตรวัดน้ำ, แจ้งท่อแตก/ซ่อมแซม</p>
                </div>
            </div>

        </div>
    </div>
</div>