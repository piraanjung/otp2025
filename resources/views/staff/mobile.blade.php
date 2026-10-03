@extends('layouts.keptkaya_mobile2')

@section('style')
      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
     
<style>
        :root {
            --primary-purple: #5835df;
            --bg-light: #f4f6f9;
            --sidebar-width: 110px;
            --sidebar-collapsed-width: 70px;
        }

        body {
            background-color: #e4e7eb;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
        }

        .app-container {
            display: flex;
            width: 100%;
            max-width: 340px;
            height: 100vh;
            background: var(--bg-light);
            margin: 15px 10px;
            border-radius: 35px;
            box-shadow: 0 15px 35px rgba(228, 98, 98, 0.2);
            position: relative;
            overflow: hidden;
        }

        /* เมนูลอย (Floating Sidebar) */
        .sidebar-menu {
            width: var(--sidebar-width);
            background-color: var(--primary-purple);
            height: 94%; /* ให้ลอยอยู่ภายในกรอบโดยมีระยะขอบบนล่างเล็กน้อย */
            top: 3%;
            left: 10px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: all 0.4s cubic-bezier(0.25, 1, 0.5, 1);
            position: absolute;
            z-index: 100; /* ให้อยู่ทับเนื้อหา */
            border-radius: 28px;
            box-shadow: 10px 0 30px rgba(88, 53, 223, 0.3); /* เพิ่มเงาให้ดูลอยเด่น */
        }
        /* สถานะพับเก็บซ่อนออกไปซ้ายสุด */
        .sidebar-menu.hidden-sidebar {
            transform: translateX(-130%);
            box-shadow: none;
        }

        /* สถานะย่อเหลือแค่ Icon แบบลอย */
        .sidebar-menu.icon-only {
            width: var(--sidebar-collapsed-width);
        }

        .sidebar-menu.icon-only .menu-link span,
        .sidebar-menu.icon-only .sidebar-logo-text {
            display: none;
        }

        .sidebar-menu.icon-only .sidebar-item {
            padding: 12px 10px;
            text-align: center;
        }

        .sidebar-menu.icon-only .menu-link {
            justify-content: center;
            gap: 0;
        }
        

        .sidebar-logo-icon {
            width: 45px;
            height: 45px;
            background: #ffffff;
            color: var(--primary-purple);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            font-size: 1.2rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.15);
            cursor: pointer;
            transition: transform 0.3s ease;
        }

        .sidebar-logo-icon:hover {
            transform: scale(1.05);
        }

        .sidebar-nav {
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 4px;
            position: relative;
        }

.sidebar-item {
            position: relative;
            cursor: pointer;
            padding: 12px 14px;
            transition: all 0.3s ease;
            z-index: 2;
            border-radius: 16px; /* กำหนดความโค้งมนให้ทุกเมนู */
            margin: 4px 10px;    /* เว้นระยะขอบด้านข้างไม่ให้ชนขอบนอก */
            height: 6rem;
        }
        .menu-link {
            /* display: flex; */
            align-items: center;
            color: #b0a4e8;
            font-size: 0.85rem;
            text-align: center;
            font-weight: 600;
            gap: 12px;
            transition: color 0.3s ease, transform 0.2s ease;
        }

        .sidebar-item:hover .menu-link {
            color: #ffffff;
            transform: translateX(4px);
        }

        .menu-link i {
            font-size: 1.5rem;
            min-width: 25px;
            text-align: center;
        }

        .menu-link span, .menu-link div  {
            white-space: nowrap;
            font-size: 1rem;
        }

     
        .sidebar-item.active {
            background:white;
            box-shadow: inset 0 2px 4px rgba(193, 59, 59, 0.2), 0 4px 12px rgba(0, 0, 0, 0.1);
            border-radius: 16px; /* ทำมุมโค้งมนรับกับทรงเมนูลอย */
        }
        .sidebar-item.active .menu-link {
            color: #000000;
            font-weight: 700;
            transform: translateX(4px);
        }

        .sidebar-footer {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            z-index: 2;
        }

        /* เนื้อหาหลักขยายเต็มจอ 100% ไม่ถูกเบียดอีกต่อไป */
        .main-content {
            width: 100%;
            height: 100%;
            background-color: var(--bg-light);
            border-radius: 35px;
            overflow-y: auto;
            padding: 20px 20px 20px 25px;
        }

        /* ปุ่มลัดสไตล์ iPhone AssistiveTouch */
        .iphone-assistive-btn {
            position: absolute;
            top: 15px;
            right: 20px;
            z-index: 100;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.4);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.25, 1, 0.5, 1);
        }

        .iphone-assistive-btn:hover {
            transform: scale(1.08);
            background: rgba(255, 255, 255, 1);
        }

        .iphone-assistive-btn i {
            font-size: 1.1rem;
            color: var(--primary-purple);
        }

        /* ดีไซน์การ์ด Soft UI ในหน้า Dashboard */
        .soft-card {
            background: #ffffff;
            border-radius: 20px;
            padding: 16px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.03);
            border: 1px solid rgba(255,255,255,0.8);
            margin-bottom: 15px;
        }

        .profile-card {
            background: linear-gradient(135deg, #f0ebff 0%, #ffffff 100%);
            border: 1px solid #dcd2f8;
        }
    </style>
    <style>
        .is-hidden{
            /* display: none */
        }
    </style>
    <link rel="stylesheet" href="{{ asset('css/staff_accessmenu.css') }}">
        <script src="{{ asset('js/jquery-3.7.1.slim.js') }}"></script>

@endsection

@section('content')

    <!-- ปุ่มลัด iPhone สำหรับซ่อน/แสดงเมนูลอย -->
    <div class="iphone-assistive-btn" id="assistiveBtn" onclick="toggleFullSidebar()" title="ซ่อน/แสดง เมนู">
        <i class="fa-solid fa-bars-staggered" id="assistiveIcon"></i>
    </div>

    <div class="app-container" id="appContainer">
            @include('staff.includes.sidebar')
        
        <!-- หน้าเนื้อหาหลัก (Dashboard เต็มพื้นที่ 100%) -->
        <div class="main-content">
            @include('staff.includes.login_screen')
            @include('staff.includes.main_screen')
            @include('staff.includes.tabwater_screen')
            @include('staff.includes.inventory_screen')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // ฟังก์ชันกดโลโก้บ้านเพื่อย่อเมนูลอยเหลือแค่ Icon
        function toggleIconOnly() {
            const sidebar = document.getElementById('sidebarMenu');
            sidebar.classList.toggle('icon-only');
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
    </script>
    {{-- @include('staff.includes.sidebar') --}}
    {{-- @include('staff.includes.login_screen')
    @include('staff.includes.main_screen')
    @include('staff.includes.tabwater_screen')
    @include('staff.includes.settings_screen')
    @include('staff.includes.recycle_screen')
     --}}
@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        
        let BluetoothSerial = null;
        let allMembers = [];                 // เก็บรายชื่อสมาชิกทั้งหมดที่ดึงมาจาก Laravel
        const API_BASE_URL = "https://6181-1-47-73-222.ngrok-free.app/api";

        let rawItemsData = [];               // เก็บรายการขยะทั้งหมด (kp_tbank_items)
        let currentSelectedTrash = null;     // เก็บขยะชิ้นปัจจุบันที่เจ้าหน้าที่เลือกอยู่
        let purchaseCart = [];               // ตะกร้าเก็บของชั่วคราวบนแอปมือถือ 
        let currentSelectedUnitId = null;
        let currentActiveMember = null;      // เก็บข้อมูลสมาชิกที่กำลังทำรายการฝากขยะอยู่
        const sidebar = document.getElementById('appSidebar');
        let staffInfo;

        //bluethooth.js
        let bluetoothDevice;
        let printCharacteristic;
        let isRecorded = false


        // {!! file_get_contents(resource_path('views/staff/includes/login_screen.js')) !!}
        // {!! file_get_contents(resource_path('views/staff/includes/navigate_to.js')) !!}
        // {!! file_get_contents(resource_path('views/staff/includes/bluethooth.js')) !!}
        // {!! file_get_contents(resource_path('views/staff/includes/settings_screen.js')) !!}

        // ฟังก์ชันกดโลโก้บ้านเพื่อย่อเมนูลอยเหลือแค่ Icon
        function toggleIconOnly() {
            const sidebar = document.getElementById('sidebarMenu');
            sidebar.classList.toggle('icon-only');
        }

        // ฟังก์ชันปุ่ม iPhone สำหรับซ่อน/แสดงเมนูลอยทั้งหมด
        function toggleFullSidebar() {
            $('#appContainer').removeClass('is-hidden')
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
    </script>
@endsection
