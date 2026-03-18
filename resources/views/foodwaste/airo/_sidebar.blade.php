<div class="menu-backdrop" id="menuBackdrop"></div>

<div class="modern-sidebar" id="mainSidebar">
    <div class="sidebar-header">

        <div class="sidebar-user-info">
            <h5 class="mb-0">{{$userWastePref->wastePreference->user->firstname ?? 'Guest'}}</h5>
            <small>ยินดีต้อนรับ</small>
        </div>

        <button class="close-sidebar-btn" id="closeMenuBtn">&times;</button>
    </div>

    <div class="sidebar-content">
    <a href="{{ url('line/dashboard/'.$userWastePref->wastePreference->id.'/'.Auth::user()->org_id_fk) }}" class="sidebar-link">

        {{-- <a href="{{ route('dashboard') }}" class="sidebar-link"> --}}
            <i class="bi bi-house-door-fill"></i> หน้าหลัก (รีไซเคิล)
        </a>


    </div>

    <div class="sidebar-footer mt-10">
        <a href="{{ route('logout') }}" class="logout-btn sidebar-link">
            <i class="bi bi-box-arrow-right"></i> ออกจากระบบ
        </a>
    </div>
</div>

<script>
    $(document).ready(function() {
        const $sidebar = $('#mainSidebar');
        const $backdrop = $('#menuBackdrop');

        // ฟังก์ชันเปิด
        $('#openMenuBtn').on('click', function() {
            $sidebar.addClass('active');
            $backdrop.addClass('active');
            $('body').css('overflow', 'hidden'); // ล็อค scroll พื้นหลัง
        });

        // ฟังก์ชันปิด
        function closeSidebar() {
            $sidebar.removeClass('active');
            $backdrop.removeClass('active');
            $('body').css('overflow', '');
        }

        $('#closeMenuBtn, #menuBackdrop').on('click', closeSidebar);
    });
</script>
