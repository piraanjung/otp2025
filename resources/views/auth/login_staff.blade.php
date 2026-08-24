<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        body {
            font-family: sans-serif;
            padding: 20px;
            background: #f0f2f5;
            margin: 0;
        }

        .card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 15px;
        }

        /* สไตล์ฟอร์ม Login */
        .login-container {
            max-width: 400px;
            margin: 50px auto;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 16px;
        }

        button {
            background: #007bff;
            color: rgb(0, 0, 0);
            border: none;
            padding: 12px 20px;
            border-radius: 5px;
            font-size: 16px;
            width: 100%;
            cursor: pointer;
        }

        button:disabled {
            background: #cccccc;
        }

        /* คลาสสำหรับซ่อนหน้าจอ */
        .is-hidden {
            display: none !important;
        }

        /* เพิ่มต่อท้าย CSS เดิมใน <style> */
:root {
    --primary: #007bff;
    --recycle-color: #28a745;
    --organic-color: #fd7e14;
    --water-color: #17a2b8;
    --dark: #343a40;
}

body {
    margin: 0;
    padding: 0;
    background-color: #f4f6f9;
    color: var(--dark);
}

.container {
    padding: 15px;
}

/* Header สไตล์แอปมือถือ */
.app-header {
    background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
    color: white;
    padding: 20px 15px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom-left-radius: 15px;
    border-bottom-right-radius: 15px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.user-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.avatar {
    font-size: 28px;
    background: rgba(255,255,255,0.2);
    padding: 5px;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.welcome-text {
    font-size: 12px;
    opacity: 0.8;
}

.staff-name {
    font-size: 18px;
    font-weight: bold;
}

.btn-logout {
    background: rgba(255, 255, 255, 0.15);
    border: 1px solid rgba(255,255,255,0.3);
    color: white;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 13px;
    width: auto;
    margin-top: 0;
}

.section-title {
    margin: 20px 0 10px 5px;
    font-size: 16px;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Layout เมนู Grid */
.menu-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr); /* แบ่งเป็น 2 คอลัมน์เท่าๆ กัน */
    gap: 15px;
}

/* สไตล์พื้นฐานของปุ่มเมนู */
.menu-item {
    background: white;
    padding: 20px 15px;
    border-radius: 12px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.05);
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    cursor: pointer;
    transition: transform 0.1s ease, box-shadow 0.1s ease;
    border-top: 5px solid #ccc;
}

/* เอฟเฟกต์ตอนใช้นิ้วกด (Touch Feedback) */
.menu-item:active {
    transform: scale(0.95);
    box-shadow: 0 1px 4px rgba(0,0,0,0.1);
}

.menu-icon {
    font-size: 40px;
    margin-bottom: 10px;
}

.menu-title {
    font-size: 15px;
    font-weight: bold;
    margin-bottom: 6px;
    color: #212529;
}

.menu-desc {
    font-size: 11px;
    color: #6c757d;
    line-height: 1.3;
}

/* แยกสีขอบบนของแต่ละกองงานให้ชัดเจน */
.card-recycle { border-top-color: var(--recycle-color); }
.card-organic { border-top-color: var(--organic-color); }
.card-water { border-top-color: var(--water-color); }
.card-settings { border-top-color: #6c757d; }


.sub-header {
    background: #28a745;
    color: white;
    padding: 15px;
    display: flex;
    align-items: center;
    gap: 15px;
}
.btn-back {
    background: rgba(255,255,255,0.2);
    border: none;
    color: white;
    padding: 6px 12px;
    border-radius: 5px;
    width: auto;
}
.search-wrapper {
    display: flex;
    gap: 8px;
}
#searchMemberInput {
    flex: 1;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 16px;
}
.btn-scan {
    width: auto;
    background: #17a2b8;
    padding: 0 15px;
    margin: 0;
}
.member-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
}
.member-card {
    background: white;
    padding: 15px;
    border-radius: 10px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.member-info p { margin: 4px 0; color: #495057; font-size: 14px; }
.btn-deposit {
    background: #28a745;
    color: white;
    width: auto;
    padding: 8px 15px;
    font-size: 14px;
    margin: 0;
}
.badge-account {
    background: #e2f0d9;
    color: #385723;
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: bold;
}
.badge-no-account {
    background: #fce4d6;
    color: #c65911;
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: bold;
}


/* 🟢 เพิ่มต่อท้ายใน style.css */
.trash-grid-card {
    background: white;
    border: 1px solid #e0e0e0;
    border-radius: 12px;
    padding: 15px 10px;
    text-align: center;
    cursor: pointer;
    box-shadow: 0 2px 4px rgba(0,0,0,0.03);
    transition: all 0.2s ease;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    align-items: center;
    min-height: 100px;
}

/* เอฟเฟกต์ตอนเอานิ้วกดจิ้มปุ่มขยะ */
.trash-grid-card:active {
    transform: scale(0.95);
    background-color: #f1f3f5;
}

.trash-card-code {
    font-size: 11px;
    color: #888;
    background: #f0f2f5;
    padding: 2px 6px;
    border-radius: 4px;
    margin-bottom: 5px;
}

.trash-card-name {
    font-weight: bold;
    font-size: 14px;
    color: #333;
    margin: 5px 0;
    line-height: 1.3;
}

.trash-card-price {
    font-size: 13px;
    color: #28a745;
    font-weight: bold;
    margin-top: 5px;
}

/* สไตล์ปุ่มแท็บหมวดหมู่ด้านบน */
.category-tab-btn {
    padding: 8px 16px;
    border: 1px solid #ced4da;
    background: white;
    color: #495057;
    border-radius: 20px;
    font-weight: bold;
    font-size: 14px;
    cursor: pointer;
    white-space: nowrap;
    transition: all 0.2s;
}
.category-tab-btn.is-active {
    background: #28a745; /* สีเขียวงานรีไซเคิล */
    color: white;
    border-color: #28a745;
}



/* =========================================================================
   🟢 CSS สำหรับ Branch: feature/mobile-animated-login
   ========================================================================= */

/* พื้นหลังไล่เฉดสีนุ่มนวลแบบแอปมือถือสมัยใหม่ */
.mobile-login-wrapper {
    min-height: 100vh;
    background: linear-gradient(180deg, #1e3c72 0%, #2a5298 60%, #f4f6f9 100%);
    display: flex;
    flex-direction: column;
    justify-content: center;
    padding: 20px;
    box-sizing: border-box;
    font-family: sans-serif;
}

/* แอนิเมชันหัวข้อแอป (ค่อยๆ ชัดขึ้น) */
.login-brand-area {
    text-align: center;
    color: white;
    margin-bottom: 25px;
    animation: fadeInDown 0.8s ease-out forwards;
}
.brand-logo {
    font-size: 48px;
    margin-bottom: 10px;
    filter: drop-shadow(0 4px 8px rgba(0,0,0,0.15));
}
.login-brand-area h1 {
    font-size: 24px;
    margin: 0 0 5px 0;
    font-weight: bold;
    letter-spacing: 0.5px;
}
.login-brand-area p {
    font-size: 14px;
    margin: 0;
    opacity: 0.75;
}

/* ⚡ การ์ด Login ลอยขึ้นมาแบบนุ่มนวล (Fade In Up) */
.mobile-login-card {
    background: rgba(255, 255, 255, 0.95);
    padding: 30px 24px;
    border-radius: 20px;
    box-shadow: 0 10px 25px rgba(30, 60, 114, 0.15);
    animation: fadeInUpMobile 0.8s cubic-bezier(0.4, 0, 0.2, 1) forwards;
}
.mobile-login-card h2 {
    font-size: 22px;
    color: #1e3c72;
    margin: 0 0 4px 0;
    font-weight: bold;
}
.subtitle {
    font-size: 13px;
    color: #6c757d;
    margin: 0 25px 25px 0;
}

/* 🔄 ระบบพิมพ์แบบอักษรลอยตัว (Floating Labels) */
.floating-group {
    position: relative;
    margin-bottom: 20px;
}
.floating-group input {
    width: 100%;
    padding: 14px 12px 10px 12px;
    font-size: 16px;
    border: 1.5px solid #ced4da;
    border-radius: 10px;
    outline: none;
    background: transparent;
    box-sizing: border-box;
    transition: all 0.2s ease;
    color: #333;
}
.floating-group label {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    background: transparent;
    color: #6c757d;
    font-size: 14px;
    pointer-events: none;
    transition: all 0.2s ease;
}
/* เมื่อคลิก หรือเมื่อมีข้อความในช่อง ให้ฉลากลอยขึ้นข้างบนอัตโนมัติ */
.floating-group input:focus ~ label,
.floating-group input:not(:placeholder-shown) ~ label {
    top: 0;
    font-size: 12px;
    padding: 0 6px;
    background: white;
    color: #1e3c72;
    font-weight: bold;
}
.floating-group input:focus {
    border-color: #1e3c72;
    box-shadow: 0 0 0 3px rgba(30, 60, 114, 0.1);
}

/* ป้ายแสดงข้อความเตือนเมื่อกรอกผิด */
.error-banner {
    background-color: #f8d7da;
    color: #721c24;
    padding: 12px;
    border-radius: 8px;
    font-size: 13px;
    margin-bottom: 15px;
    border-left: 4px solid #dc3545;
}

/* ปุ่มล็อกอินสำหรับใช้นิ้วโป้งกดง่ายๆ */
.btn-mobile-login {
    width: 100%;
    background: linear-gradient(90deg, #1e3c72 0%, #2a5298 100%);
    color: white;
    border: none;
    padding: 14px;
    font-size: 16px;
    font-weight: bold;
    border-radius: 10px;
    cursor: pointer;
    margin-top: 10px;
    box-shadow: 0 4px 12px rgba(30, 60, 114, 0.2);
    transition: all 0.2s;
}
.btn-mobile-login:active {
    transform: scale(0.98);
    opacity: 0.9;
}
.btn-mobile-login:disabled {
    background: #6c757d;
    cursor: not-allowed;
}

.login-footer {
    text-align: center;
    margin-top: 30px;
    font-size: 11px;
    color: #6c757d;
}

/* 🎬 แอนิเมชันสไลด์ขึ้นตอนโหลดแอป */
@keyframes fadeInUpMobile {
    from {
        opacity: 0;
        transform: translateY(40px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
@keyframes fadeInDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* =========================================================================
   🟢 CSS สำหรับ Branch: feature/sidebar-toggle-menu
   ========================================================================= */

.mobile-sidebar-wrapper {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 10000;
    visibility: hidden;
    transition: visibility 0.3s;
}

/* ปลดล็อกคลาสเมื่อสั่งเปิดเมนู */
.mobile-sidebar-wrapper.is-open {
    visibility: visible;
}

/* พื้นหลังมืดโปร่งแสงด้านหลัง */
.sidebar-overlay {
    position: absolute;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.4);
    opacity: 0;
    transition: opacity 0.3s ease;
}
.mobile-sidebar-wrapper.is-open .sidebar-overlay {
    opacity: 1;
}

/* กล่องเนื้อหาเมนูที่เด้งสไลด์มาจากทางซ้าย */
.sidebar-content {
    position: absolute;
    top: 0;
    left: 0;
    width: 280px; /* ความกว้างเมนู */
    height: 100%;
    background: #ffffff;
    box-shadow: 4px 0 15px rgba(0, 0, 0, 0.1);
    display: flex;
    flex-direction: column;
    transform: translateX(-100%); /* ซ่อนไว้ฝั่งซ้ายสุดจอก่อนเปิด */
    transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.mobile-sidebar-wrapper.is-open .sidebar-content {
    transform: translateX(0); /* สไลด์กลับเข้ามาโชว์ปกติ */
}

/* พื้นหลังข้อมูล Staff ใช้โทนน้ำเงินไล่เฉดให้เข้าเซ็ตกับแอป */
.sidebar-user-profile {
    background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
    color: white;
    padding: 35px 20px 25px 20px;
    display: flex;
    align-items: center;
    gap: 15px;
}
.sidebar-avatar {
    font-size: 32px;
    background: rgba(255, 255, 255, 0.2);
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}
.sidebar-user-details small {
    display: block;
    font-size: 11px;
    opacity: 0.75;
    margin-bottom: 2px;
}
.sidebar-user-details span {
    font-size: 16px;
    font-weight: bold;
}

/* รายการปุ่มเมนูสไลด์ */
.sidebar-menu-items {
    padding: 15px 10px;
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 5px;
}
.menu-item {
    display: block;
    padding: 12px 15px;
    color: #495057;
    text-decoration: none;
    font-size: 15px;
    border-radius: 8px;
    font-weight: 500;
    transition: background 0.2s;
}
.menu-item:active, .menu-item.active {
    background: #f0f4fd;
    color: #1e3c72;
    font-weight: bold;
}

/* พื้นที่ปุ่มออกจากระบบด้านล่างสุด */
.sidebar-footer {
    padding: 15px;
    border-top: 1px solid #dee2e6;
}
.btn-sidebar-logout {
    width: 100%;
    background: #fff0f0;
    color: #dc3545;
    border: 1px solid #fecaca;
    padding: 12px;
    font-size: 14px;
    font-weight: bold;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s;
}
.btn-sidebar-logout:active {
    background: #fcdede;
}

/* 🟢 ล็อกขนาดพื้นที่กล่องรายการขยะย่อยให้สไลด์ข้างในแทน */
#item-buttons-container {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 10px;
    
    /* 📌 จุดสำคัญ: จำกัดความสูงไว้ที่ 160px - 180px (แสดงได้ประมาณ 2 แถวพอดีสวย) */
    max-height: 170px; 
    overflow-y: auto; /* ถ้าของเกิน ให้เกิด Scrollbar สไลด์ในกล่องนี้แทน */
    padding-right: 4px; /* เผื่อพื้นที่ให้ Scrollbar ฝั่งขวาไม่บังปุ่ม */
}

/* 🟢 ปรับสไตล์การ์ดขยะย่อยให้กระชับ เต็มตา ไม่กินพื้นที่แนวตั้ง */
.trash-item-card-compact {
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 8px 10px; /* ลด Padding ลงเพื่อให้การ์ดผอมลง */
    text-align: center;
    min-height: 65px; /* ลดความสูงขั้นต่ำลงจากเดิม */
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    box-shadow: 0 1px 3px rgba(0,0,0,0.02);
}

/* ปรับฟอนต์ชื่อขยะและราคาให้สมดุล */
.trash-item-card-compact .item-title {
    font-size: 14px;
    font-weight: bold;
    color: #2d3748;
    margin-bottom: 2px;
}
.trash-item-card-compact .item-price {
    font-size: 12px;
    color: #38a169;
    font-weight: bold;
}

/* =========================================================================
   🟢 CSS สำหรับหน้าต่างคีย์น้ำหนักเด้งจากด้านล่าง (Bottom Sheet Modal)
   ========================================================================= */
.weight-modal-container {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 1050;
    display: flex;
    flex-direction: column;
    justify-content: flex-end; /* ดึงของลงขอบล่างจอ */
    visibility: hidden;
    transition: visibility 0.3s ease;
}


/* คลาสเปิดใช้งานผ่าน JavaScript */
.weight-modal-container.is-active {
    visibility: visible;
}

/* ฉากหลังสีเทาดำโปร่งแสง */
.weight-modal-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.4);
    opacity: 0;
    transition: opacity 0.3s ease;
}
.weight-modal-container.is-active .weight-modal-overlay {
    opacity: 1;
}

/* ตัวกล่องเนื้อหาที่จะดีดขึ้นมา */
.weight-modal-content {
    position: relative;
    background: #ffffff;
    width: 100%;
    border-top-left-radius: 24px;
    border-top-right-radius: 24px;
    padding: 20px 20px 30px 20px;
    box-sizing: border-box;
    transform: translateY(100%); /* เริ่มต้นซ่อนไว้ใต้จอ */
    transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 -5px 20px rgba(0,0,0,0.1);
}
.weight-modal-container.is-active .weight-modal-content {
    transform: translateY(0); /* สไลด์ลอยขึ้นมาพิกัดปกติ */
}

/* ขีดจับดีไซน์มือถือ */
.weight-modal-handle {
    width: 44px;
    height: 5px;
    background: #e2e8f0;
    border-radius: 3px;
    margin: -10px auto 15px auto;
}

.weight-modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #f1f3f5;
    padding-bottom: 12px;
    margin-bottom: 15px;
}

.btn-weight-modal-close {
    background: #f1f3f5;
    border: none;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    color: #6c757d;
    font-size: 14px;
    font-weight: bold;
    cursor: pointer;
}

/* เอฟเฟกต์ตอนเอานิ้วกดปุ่มลงตะกร้า */
#btnAddToCart:active {
    transform: scale(0.98);
    opacity: 0.95;
}


/* =========================================================================
   🟢 CSS สำหรับกล่องแจ้งเตือนความสำเร็จแบบหายวับอัตโนมัติ (Success Toast)
   ========================================================================= */
.success-toast-overlay {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%) scale(0.8);
    background: rgba(0, 0, 0, 0.85);
    color: white;
    padding: 20px 30px;
    border-radius: 16px;
    text-align: center;
    z-index: 9999; /* อยู่บนสุดของทุกเลเยอร์ */
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 10px;
    pointer-events: none; /* เพื่อไม่ให้บล็อกการกดหน้าจอหลัก */
    opacity: 0;
    transition: all 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

/* คลาสเปิดใช้งานให้แสดงผล */
.success-toast-overlay.show {
    opacity: 1;
    transform: translate(-50%, -50%) scale(1);
}

.toast-icon {
    font-size: 40px;
    animation: pulseIcon 0.4s ease-in-out;
}

.toast-text {
    font-size: 15px;
    font-weight: bold;
    white-space: nowrap;
}

/* แอนิเมชันให้ไอคอนเด้งดึ๋งสะใจ */
@keyframes pulseIcon {
    0% { transform: scale(0.5); }
    50% { transform: scale(1.2); }
    100% { transform: scale(1); }
}
    </style>
    <link rel="manifest" href="{{ asset('manifest.json') }}">

<script>
  if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register("{{ asset('service-worker.js') }}")
      .then(reg => console.log('Service Worker Registered'))
      .catch(err => console.log('Service Worker Failed', err));
  }
</script>
</head>
<body>
     <div id="loginScreen" class="mobile-login-wrapper">
        <div class="login-brand-area">
            <div class="brand-logo">♻️</div>
            <h1>PI-OS</h1>
            <p>KeptKaya Staff Application</p>
        </div>

        <div class="mobile-login-card">
            <h2>ยินดีต้อนรับ</h2>
            <p class="subtitle">กรุณาเข้าสู่ระบบเพื่อปฏิบัติงาน</p>


            <form  method="POST" action="{{ route('login') }}">
            @csrf
                <div class="floating-group">
                    <input type="text" id="username" name="username"  placeholder=" " value="katsukipai16@gmail.com" autofocus required>
                    <label for="username">👤 ชื่อผู้ใช้งาน / รหัสเจ้าหน้าที่</label>
                    <span class="input-highlight"></span>
                </div>

                <div class="floating-group">
                    <input type="password" id="password" name="password" " placeholder=" " value="0910642922" autocomplete="current-password" required>
                    <label for="password">🔒 รหัสผ่านความปลอดภัย</label>
                    <span class="input-highlight"></span>
                </div>
                <input type="hidden" name="login_staff" value="1">
                <button type="submit" id="btnLogin" class="btn-mobile-login">
                    🔓 เข้าสู่ระบบ
                </button>
            </form>
        </div>

      
    </div>
</body>
</html>