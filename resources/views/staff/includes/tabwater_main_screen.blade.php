    <div id="tabwaterScreen" class="main-screen-container">
    <div class="container py-3">
        <!-- ส่วนหัว: ปุ่มเมนู วันที่ และโปรไฟล์ -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="menu-toggle-btn shadow-sm" onclick="toggleFullSidebar()">
                <i class="fa-solid fa-bars"></i>
            </div>
            <div class="text-muted fw-semibold" style="font-size: 0.9rem;" id="currentDateText">Aug 26, 2020</div>
            <div class="profile-avatar shadow-sm">
                <img src="https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?w=150" alt="Avatar">
            </div>
        </div>

        <!-- หัวข้อ My Task -->
        <h4 class="fw-bold text-dark mb-3">My Task</h4>

        <!-- ส่วนกริดการ์ดเมนู 4 ช่อง (สไตล์ตามรูป) -->
        <div class="row g-3 mb-4">
            <!-- การ์ดที่ 1: สีชมพู/แดง (Mobile App Design) -->
            <div class="col-6">
                <div class="task-card card-pink" onclick="navigateTo('water-tabwater-record')">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="card-icon-box">
                            <i class="fa-solid fa-palette"></i>
                        </div>
                        <i class="fa-solid fa-arrow-right card-arrow"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-white mb-1">จดมิเตอร์ประปา</h6>
                        <small class="text-white-50">10 Task</small>
                    </div>
                </div>
            </div>

            <!-- การ์ดที่ 2: สีส้ม/เหลือง (Pending) -->
            <div class="col-6">
                <div class="task-card card-orange" onclick="navigateTo('water-cutmeter')">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="card-icon-box">
                            <i class="fa-solid fa-desktop"></i>
                        </div>
                        <i class="fa-solid fa-arrow-right card-arrow"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-white mb-1">Pending</h6>
                        <small class="text-white-50">26 tasks</small>
                    </div>
                </div>
            </div>

            <!-- การ์ดที่ 3: สีม่วง (Illustration) -->
            <div class="col-6">
                <div class="task-card card-purple" onclick="navigateTo('water-complain')">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="card-icon-box">
                            <i class="fa-solid fa-pen-nib"></i>
                        </div>
                        <i class="fa-solid fa-arrow-right card-arrow"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-white mb-1">Illustration</h6>
                        <small class="text-white-50">6 tasks</small>
                    </div>
                </div>
            </div>

            <!-- การ์ดที่ 4: สีฟ้า (Website Design) -->
            <div class="col-6">
                <div class="task-card card-blue" onclick="navigateTo('water-equipment-control')">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="card-icon-box">
                            <i class="fa-solid fa-lightbulb"></i>
                        </div>
                        <i class="fa-solid fa-arrow-right card-arrow"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-white mb-1">Website<br>Design</h6>
                        <small class="text-white-50">10 task</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- หัวข้อ On Going และปุ่ม See all -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-bold text-dark mb-0">On Going</h4>
            <a href="#" class="text-danger text-decoration-none fw-semibold" style="font-size: 0.85rem;">See all</a>
        </div>

        <!-- การ์ดแสดงรายการ On Going ด้านล่าง -->
        <div class="ongoing-card p-3 shadow-sm d-flex justify-content-between align-items-center">
            <div>
                <h6 class="fw-bold text-dark mb-2" style="font-size: 0.95rem;">Startup Website Design<br>with responsive</h6>
                <div class="d-flex align-items-center text-muted mb-2" style="font-size: 0.75rem;">
                    <i class="fa-regular fa-clock me-1 text-primary"></i> 10:00 AM - 12:30 PM
                </div>
                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-1 rounded-pill" style="font-size: 0.7rem;">Complete: 80%</span>
            </div>
            <div>
                <!-- ไอคอนจรวด -->
                <div class="ongoing-rocket-icon">
                    🚀
                </div>
            </div>
        </div>
    </div>
</div>