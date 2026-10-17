
    <div class="iphone-assistive-btn" id="assistiveBtn" onclick="toggleFullSidebar()" title="ซ่อน/แสดง เมนู">
    <i class="fa-solid fa-bars-staggered" id="assistiveIcon"></i>
</div>
<!-- Floating Sidebar -->
<div id="sidebarMenu" class="sidebar-menu">
    
    <div class="sidebar-header text-center py-3">
    
        <!-- กดที่โลโก้บ้านเพื่อ Toggle ย่อเมนูลอยเหลือแค่ Icon -->
        <div class="sidebar-logo-icon" onclick="toggleIconOnly()" title="ย่อ/ขยาย เมนู">
            <i class="fa-solid fa-house"></i>
        </div>
    </div>

    <div class="sidebar-nav" id="sidebarNavList">
        <div class="active-indicator" id="activeIndicator"></div>

        <div class="sidebar-item active" onclick="switchSidebarMenu(this, 'dashboard')">
            <div class="menu-link">
                <i class="fa-solid fa-gauge-high"></i>
                <div>Dashboard</div>
            </div>
        </div>
        <div class="sidebar-item" onclick="switchSidebarMenu(this, 'tabwater')">
            <div class="menu-link">
                <i class="fa-solid fa-user-group"></i>
                <div>งานประปา</div>
            </div>
        </div>
        <div class="sidebar-item" onclick="switchSidebarMenu(this, 'recycle_bank')">
            <div class="menu-link">
                <i class="fa-solid fa-comments"></i>
                <div>ธนาคาร</div>
                <div>ขยะรีไซเคิล</div>
            </div>
        </div>
        <div class="sidebar-item" onclick="switchSidebarMenu(this, 'inventory')">
            <div class="menu-link">
                <i class="fa-solid fa-chart-line"></i>
                <div>คลังพัสดุ</div>
            </div>
        </div>
        <div class="sidebar-item" onclick="switchSidebarMenu(this, 'statistics')">
            <div class="menu-link">
                <i class="fa-regular fa-star"></i>
                <div>จัดเก็บค่า</div>
                <div>ถังขยะรายปี</div>
            </div>
        </div>
        <div class="sidebar-item" style="background: white;">
            <div class="menu-link">

                <button class="btn btn-info mb-1" onclick="navigateTo('settings')">
                    <i class="fa fa-gears"></i>
                   ตั้งค่า
                </button>
                <button type="button" id="btnLogout" class="btn btn-danger">
                    🚪 ออกจากระบบ
                </button>
                
            </div>
        </div>
    </div>


</div>