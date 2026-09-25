@extends('layouts.keptkaya_mobile2')

@section('style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Google+Sans:ital,opsz,wght@0,17..18,400..700;1,17..18,400..700&family=Kanit:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">    <style>
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
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .avatar {
            font-size: 28px;
            background: rgba(255, 255, 255, 0.2);
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
            border: 1px solid rgba(255, 255, 255, 0.3);
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
            grid-template-columns: repeat(2, 1fr);
            /* แบ่งเป็น 2 คอลัมน์เท่าๆ กัน */
            gap: 15px;
        }

        /* สไตล์พื้นฐานของปุ่มเมนู */
        .menu-item {
            background: white;
            padding: 20px 15px;
            border-radius: 12px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
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
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);
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
        .card-recycle {
            border-top-color: var(--recycle-color);
        }

        .card-organic {
            border-top-color: var(--organic-color);
        }

        .card-water {
            border-top-color: var(--water-color);
        }

        .card-settings {
            border-top-color: #6c757d;
        }


        .sub-header {
            background: #28a745;
            color: white;
            padding: 15px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .btn-back {
            background: rgba(255, 255, 255, 0.2);
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

        #searchMemberRecycleInput {
            flex: 1;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 16px;
        }

        .btn-scan {
            width: auto;
            background: #17a2b8;
            /* padding: 0 15px; */
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
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .member-info p {
            margin: 4px 0;
            color: #495057;
            font-size: 14px;
        }

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
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.03);
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
            background: #28a745;
            /* สีเขียวงานรีไซเคิล */
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
            filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.15));
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
        .floating-group input:focus~label,
        .floating-group input:not(:placeholder-shown)~label {
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
            width: 280px;
            /* ความกว้างเมนู */
            height: 100%;
            background: #ffffff;
            box-shadow: 4px 0 15px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            transform: translateX(-100%);
            /* ซ่อนไว้ฝั่งซ้ายสุดจอก่อนเปิด */
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .mobile-sidebar-wrapper.is-open .sidebar-content {
            transform: translateX(0);
            /* สไลด์กลับเข้ามาโชว์ปกติ */
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

        .menu-item:active,
        .menu-item.active {
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
            overflow-y: auto;
            /* ถ้าของเกิน ให้เกิด Scrollbar สไลด์ในกล่องนี้แทน */
            padding-right: 4px;
            /* เผื่อพื้นที่ให้ Scrollbar ฝั่งขวาไม่บังปุ่ม */
        }

        /* 🟢 ปรับสไตล์การ์ดขยะย่อยให้กระชับ เต็มตา ไม่กินพื้นที่แนวตั้ง */
        .trash-item-card-compact {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 8px 10px;
            /* ลด Padding ลงเพื่อให้การ์ดผอมลง */
            text-align: center;
            min-height: 65px;
            /* ลดความสูงขั้นต่ำลงจากเดิม */
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
        }

        /* ปรับฟอนต์ชื่อขยะและราคาให้สมดุล */
        .trash-item-card-compact .item-title {
            font-size: 18px;
            font-weight: bold;
            color: #2d3748;
            margin-bottom: 2px;
        }

        .trash-item-card-compact .item-price {
            font-size: 14px;
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
            justify-content: flex-end;
            /* ดึงของลงขอบล่างจอ */
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
            transform: translateY(100%);
            /* เริ่มต้นซ่อนไว้ใต้จอ */
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 -5px 20px rgba(0, 0, 0, 0.1);
        }

        .weight-modal-container.is-active .weight-modal-content {
            transform: translateY(0);
            /* สไลด์ลอยขึ้นมาพิกัดปกติ */
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
            z-index: 9999;
            /* อยู่บนสุดของทุกเลเยอร์ */
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 10px;
            pointer-events: none;
            /* เพื่อไม่ให้บล็อกการกดหน้าจอหลัก */
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
            0% {
                transform: scale(0.5);
            }

            50% {
                transform: scale(1.2);
            }

            100% {
                transform: scale(1);
            }
        }

        .profile img {
            display: block;
            width: 70px;
            height: 70px;
            border-radius: 50%;
            margin: 0 auto;
        }
    </style>
    <style>
        #card-reciept {
            font-size: 1.5rem !important
                /* border-bottom: #000000 1px solid */
        }

        #card-reciept #org div {
            text-align: right;
            padding-right: 7px
        }

        #org_address {
            position: absolute;
            margin-top: 4.5rem;
            text-align: right;
            padding-right: 0
        }

        #org_address div {
            font-size: 1.3rem;
            line-height: 22px
        }

        #card-reciept p {
            font-size: 1.6rem;
            font-weight: bold;
            text-align: center;
            margin-top: 1.5rem;
        }

        #member_info,
        #card-reciept p {
            border-bottom: #000000 1px solid;
            margin-bottom: 10px
        }

        #member_info {
            /* margin-bottom: 10px */
        }

        .header {
            text-align: right;
            font-weight: bold;
            font-size: 1.25rem
        }

        .info,
        .info div {
            font-size: 1.3rem
        }

        thead td {
            font-size: 1.3rem;
            text-align: center
        }

        tbody td {
            font-size: 1.3rem;
            /* border: 1px solid; */
            text-align: center;
            padding-top: 5px
        }

        #card-reciept .amount {
            text-align: right;
            font-size: 1.3rem;
            vertical-align: top
        }

        .hidden {
            display: none
        }
    </style>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">

    <script>
        window.ASSET_URL = "{{ asset('') }}";
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" />
@endsection

@section('content')

    <div class="app-header">
        <div style="display: flex; align-items: center; gap: 12px;">
            <button type="button" id="btnOpenSidebar" onclick="toggleSidebar(true)"
                style="background: transparent; border: none; color: white; font-size: 24px; cursor: pointer; padding: 0 5px; width: auto; margin: 0;">
                ☰
            </button>
            <span style="font-weight: bold; font-size: 18px; letter-spacing: 0.5px;">PI-STAFF</span>
        </div>
        <div class="header-status">
            <span id="connectionStatusBadge"
                style="font-size: 12px; background: rgba(255,255,255,0.15); padding: 4px 10px; border-radius: 20px;">📱
                Mobile Mode</span>
        </div>
    </div>


    <div id="appSidebar" class="mobile-sidebar-wrapper">
        <div class="sidebar-overlay" onclick="toggleSidebar(false)"></div>

        <div class="sidebar-content">

            <div class="sidebar-user-profile">
                <div class="sidebar-avatar">
                    <center class="profile">
                        <img src="{{ " https://profile.line-scdn.net/" . Auth::user()->image }}" class="img-bordered-md"
                            alt="">
                    </center>
                </div>
                <div class="sidebar-user-details">
                    <small>เจ้าหน้าที่ผู้ปฏิบัติงาน</small>
                    <span id="staffName"></span>
                </div>
            </div>

            <div class="sidebar-menu-items">
                <a href="#" class="menu-item active" onclick="toggleSidebar(false,'main'), navigateTo('main')">
                    หน้าหลักบันทึกขยะ</a>
                <a href="#" class="menu-item" onclick="toggleSidebar(false,'main'), navigateTo('settings')">⚙️
                    ตั้งค่าอุปกรณ์</a>

            </div>

            <div class="sidebar-footer">
                <button type="button" id="btnLogout" class="btn-sidebar-logout">
                    🚪 ออกจากระบบ
                </button>
            </div>
        </div>
    </div>

    <div id="loginScreen" class="">
        @include('staff.includes.screen_login')
    </div>
    @include('staff.includes.screen_select_org')

    <div id="mainScreen" class="is-hidden">
        <div class="container">
            <h3 class="section-title">เมนูบริการระบบสนาม</h3>

            <div class="menu-grid">
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

                <div class="menu-item card-water" onclick="navigateTo('water')">
                    <div class="menu-icon">💧</div>
                    <div class="menu-title">งานประปาภูมิภาค</div>
                    <div class="menu-desc">จดมาตรวัดน้ำ, แจ้งท่อแตก/ซ่อมแซม</div>
                </div>

                <div class="menu-item card-settings" onclick="navigateTo('settings')">
                    <div class="menu-icon">🖨️</div>
                    <div class="menu-title">ตั้งค่าเครื่องพิมพ์</div>
                    <div class="menu-desc">เชื่อมต่อ Bluetooth Thermal Printer</div>
                </div>

                <div class="menu-item card-inventory" onclick="navigateTo('inventory')">
                    <div class="menu-icon">🖨️</div>
                    <div class="fa fa-bible"></div>
                    <div class="menu-title">ยืม/คืน พัสดุ</div>
                    <div class="menu-desc">ยืม/คืน พัสดุ</div>
                </div>
            </div>
        </div>
    </div>

    <div id="recycleScreen" class="is-hidden">
        <div class="sub-header">
            <button onclick="backToMenu()" class="btn-back">⬅️ กลับเมนูหลัก</button>
            <h3 style="margin: 0;">ธนาคารขยะรีไซเคิล</h3>
        </div>

        <div class="container">
            <div class="card" style="padding: 12px;">
                <div class="search-wrapper">
                    <input type="text" id="searchMemberRecycleInput" placeholder="🔍 ค้นหาชื่อ, นามสกุล หรือเบอร์โทร..."
                        oninput="filterMembers()">
                    <button id="btnScanQR" class="btn-scan">📷 สแกน QR</button>
                </div>
            </div>

            <h4 class="section-title">รายชื่อสมาชิกในระบบ (<span id="memberCount">0</span> คน)</h4>


            <div class="tab-container"
                style="display: flex; margin-bottom: 15px; background: #eee; padding: 5px; border-radius: 8px;">
                <button id="btnTabPending" onclick="switchTab('pending')"
                    style="flex: 1; padding: 10px; border: none; border-radius: 6px; font-weight: bold; background: #007bff; color: white; cursor: pointer;">
                    ⏳ รอรับซื้อ (<span id="countPending">0</span>)
                </button>
                <button id="btnTabCompleted" onclick="switchTab('completed')"
                    style="flex: 1; padding: 10px; border: none; border-radius: 6px; font-weight: bold; background: transparent; color: #333; cursor: pointer;">
                    ✓ รับซื้อแล้ววันนี้ (<span id="countCompleted">0</span>)
                </button>
            </div>

            <div id="memberListContainer"></div>
        </div>
    </div>

    <div id="depositScreen" class="is-hidden">
        <div id="globalPrinterStatus"
            style="background: #fff; padding: 10px 15px; margin-bottom: 15px; border-radius: 8px; border-left: 5px solid #dc3545; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
            <div style="font-size: 14px; font-weight: bold; color: #495057;">
                🖨️ สถานะเครื่องพิมพ์: <span id="lblGlobalPrinterName" style="color: #dc3545;">🔴
                    ยังไม่ได้เชื่อมต่อ</span>
            </div>
            <span id="lblGlobalPrinterIndicator"
                style="width: 12px; height: 12px; background: #dc3545; border-radius: 50%;"></span>
        </div>

        <div class="sub-header"
            style="background: #28a745; color: white; padding: 15px; display: flex; align-items: center; gap: 15px;">
            <button onclick="backToRecycleScreen()" class="btn-back">⬅️ ย้อนกลับ</button>
            <h4 class="fw-bold mb-0 text-white" style="margin:0;">รับซื้อขยะรีไซเคิล</h4>
        </div>

        <div class="container-fluid px-0" style="max-width: 600px; margin: 0 auto; padding: 15px;">
            <div class="d-flex justify-content-between align-items-center mb-3 mt-2 px-2"
                style="display: flex; justify-content: space-between; align-items: center;">
                <h4 class="fw-bold mb-0 text-dark" style="margin:0; font-weight: bold;">รับซื้อขยะ</h4>
                <span class="badge bg-light text-secondary rounded-pill border px-3 py-2"
                    style="background: #f8f9fa; border: 1px solid #ddd; padding: 5px 12px; border-radius: 50px; font-size: 13px;">
                    👤 <span id="lblActiveStaffName"></span>
                </span>
            </div>

            <div class="card border-0 shadow-sm rounded-4 mb-3"
                style="background: white; padding: 15px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border-left: 5px solid #28a745;">
                <small
                    style="color:#6c757d; font-weight: bold; display:block; margin-bottom:2px;">ผู้มาติดต่อ/สมาชิก</small>
                <h5 id="lblDepositMemberName" style="margin:0 0 5px 0; font-weight:bold;">คุณ...</h5>
                <small style="color:#28a745; font-weight:bold;">💳 เลขที่บัญชี: <span
                        id="lblDepositMemberAcc">b111x</span></small>
            </div>

            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5"
                style="background: white; padding: 15px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">

                <div id="purchaseForm" class="p-3 bg-white border rounded-4"
                    style="background: #fff; border: 1px solid #dee2e6; padding: 15px; border-radius: 12px; margin-top: 15px; clear: both;">

                    <div class="d-flex justify-content-between align-items-center mb-2"
                        style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                        <label class="fw-bold text-dark mb-0" style="font-weight: bold;">หมวดหมู่ขยะ</label>
                        <button type="button" id="btnScanTrashQR"
                            class="btn btn-sm btn-light text-primary rounded-pill fw-bold"
                            style="background: #e6f0fa; color: #007bff; border: none; padding: 5px 15px; border-radius: 50px; font-weight: bold; font-size: 13px;">
                            📷 สแกน QR ขยะ
                        </button>
                    </div>

                    {{-- <div class="d-flex overflow-auto pb-3 pt-2 hide-scrollbar ps-2" id="groupFiltersContainer"
                        style="display: flex; overflow-x: auto; gap: 15px; padding-bottom: 10px; margin-bottom: 15px;">
                    </div> --}}
                    <div class="row" id="groupFiltersContainer" style="padding-bottom: 10px; margin-bottom: 15px;">
                    </div>

                    <div id="categoryItemsModal" class="weight-modal-container">
                        <div class="weight-modal-overlay" onclick="closeCategoryModal()"></div>
                        <div class="weight-modal-content" style="max-height: 80vh; overflow-y: auto;">
                            <div class="weight-modal-handle"></div>
                            <div class="weight-modal-header">
                                <div>
                                    <span style="font-size: 18px; font-weight: bold; color: #1e3c72;">เลือกรายการขยะ</span>
                                </div>
                                <button type="button" class="btn-weight-modal-close"
                                    onclick="closeCategoryModal()">✕</button>
                            </div>
                            <div class="weight-modal-body">
                                <!-- แถบเลือกหมวดหมู่ขยะ -->
                                <div class="d-flex overflow-auto pb-3 pt-2 hide-scrollbar ps-2" id="groupFiltersContainer"
                                    style="display: flex; overflow-x: auto; gap: 15px; padding-bottom: 10px; margin-bottom: 15px;">
                                </div>

                                <!-- กล่องแสดงรายการขยะย่อยใน Modal -->
                                <div class="bg-light rounded-4 p-3 mb-3"
                                    style="background: #f8f9fa; padding: 15px; border-radius: 12px; min-height: 150px; margin-bottom: 15px;">
                                    <div id="item-buttons-container"
                                        style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; max-height: 300px; overflow-y: auto;">
                                        <div
                                            style="grid-column: span 2; text-align: center; color: #6c757d; padding: 30px 0;">
                                            👋<br><small>เลือกหมวดหมู่ด้านบนเพื่อเริ่มรายการ</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="selected-item-display"
                        class="alert alert-warning border-0 rounded-3 d-flex justify-content-between align-items-center mb-3 shadow-sm"
                        style="display: none; opacity: 0; background: #fff3cd; color: #856404; padding: 15px; border-radius: 8px; justify-content: space-between; align-items: center;">
                        <div style="opacity: 0">
                            <small class="d-block" style="font-size: 11px; text-transform: uppercase;">กำลังเลือก:</small>
                            <span id="selected-item-name" class="fw-bold fs-3 text-dark"
                                style="font-size: 22px; font-weight: bold; color: #212529;">...</span>
                        </div>
                        <button type="button" class="btn-close-selection"
                            style="background: #fff; border: 1px solid #ffeeba; color: #dc3545; width: 40px; height: 40px; border-radius: 50%; font-size: 20px; font-weight: bold;"
                            onclick="resetSelection()">✕</button>
                    </div>

                    <div id="weightInputModal" class="weight-modal-container">
                        <div class="weight-modal-overlay" onclick="closeWeightModal()"></div>

                        <div class="weight-modal-content">
                            <div class="weight-modal-handle"></div>

                            <div class="weight-modal-header">
                                <div>
                                    <small style="color: #6c757d; font-size: 16px; display: block;">รายการที่เลือก:</small>
                                    <span id="modal-selected-item-name"
                                        style="font-size: 18px; font-weight: bold; color: #1e3c72;">-</span>
                                </div>
                                <button type="button" class="btn-weight-modal-close" onclick="closeWeightModal()">✕</button>
                            </div>

                            <div class="weight-modal-body">

                                <div class="p-3 bg-white border rounded-4"
                                    style="background: #fff; border: 1px solid #dee2e6; padding: 15px; border-radius: 12px; margin-top: 15px; clear: both;">
                                    <div class="row"
                                        style="display: grid-column; grid-template-columns: 7fr 5fr; gap: 15px; margin-bottom: 15px; align-items: end;">
                                        <div class="col-8">
                                            <label class="small text-muted mb-1"
                                                style="font-size: 13px; color: #6c757d; display: block; margin-bottom: 5px;">จำนวน</label>
                                            <input type="number" step="0.01" id="amount_in_units" class="form-control"
                                                placeholder="0.00"
                                                style="width: 100%; padding: 12px 5px; box-sizing: border-box; font-size: 28px; font-weight: bold; text-align: center; background: #f8f9fa; border: 1px solid #ced4da; border-radius: 8px; height: 58px;">
                                        </div>

                                        <div class="col-4">
                                            <div id="unit-buttons-container"
                                                style="display: flex; gap: 8px; height: 58px; align-items: stretch; color:black">
                                            </div>
                                            <input type="hidden" id="kp_units_idfk">
                                        </div>
                                    </div>

                                    <button type="button" id="btnAddToCart"
                                        class="btn btn-success w-100 py-3 rounded-3 shadow fw-bold"
                                        style="background: #28a745; color: #000000; border: none; width: 100%; padding: 15px; border-radius: 8px; font-size: 18px; font-weight: bold; cursor: pointer;">
                                        ➕ เพิ่มรายการลงตะกร้า
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="floatingCartContainer"></div>
            </div>
        </div>
    </div>
    <div id="settingsScreen" class="is-hidden">
        <div class="sub-header">
            <button onclick="backToMenuFromSettings()" class="btn-back">⬅️ กลับเมนูหลัก</button>
            <h3 style="margin: 0;">ตั้งค่าระบบเครื่องพิมพ์</h3>
        </div>

        <div class="container" style="max-width: 500px; margin: 20px auto;">
            <div class="card" style="text-align: center; padding: 25px 20px;">
                <div style="font-size: 50px; margin-bottom: 10px;">🖨️</div>
                <h4 style="margin: 0 0 10px 0; font-weight: bold;">Bluetooth Thermal Printer</h4>
                <p style="color: #6c757d; font-size: 14px; margin-bottom: 20px;">
                    กรุณาเปิดเครื่องพิมพ์ใบเสร็จและจับคู่บลูทูธบนระบบสมาร์ทโฟนก่อนทำการกดเชื่อมต่อ
                </p>

                <div
                    style="margin-bottom: 20px; padding: 15px; background: #f8f9fa; border-radius: 8px; border: 1px solid #e3e6f0;">
                    <span id="status" style="font-weight: bold; font-size: 15px; color: #858796;">
                        🔴 ยังไม่ได้เชื่อมต่ออุปกรณ์
                    </span>
                </div>

                <div class="bottom-action-bar">
                    <button id="connectButton" class="btn btn-info col-4">
                        <span>🔄 ค้นหา & เชื่อมต่ออุปกรณ์</span>
                    </button>
                    <button id="printImageButton" onclick="printReceipt()" class="btn btn-primary col-8 shadow-sm">
                        <span class="material-icons-round">print</span>
                        <span id="printBtnText">พิมพ์ใบเสร็จ</span>
                    </button>
                </div>
                <div class="row justify-content-center mb-4">
                    <div class="col-12 col-md-6">
                        <div id="status-card" class="status-badge bg-light text-secondary border">
                            <span class="material-icons-round text-primary">info</span>
                            <div>
                                <small class="d-block text-uppercase fw-bold"
                                    style="font-size: 0.7rem;">สถานะการเชื่อมต่อ</small>
                                <span id="status-text">ยังไม่ได้เชื่อมต่อ</span>
                            </div>
                        </div>
                    </div>
                </div>

                <canvas id="canvas" style="display: none;"></canvas>
            </div>
        </div>
    </div>

    <div id="waterScreen" class="is-hidden">
        <div class="container">
            <h3 class="section-title">งานประปา</h3>

            <div class="menu-grid">
                <div class="menu-item card-recycle" onclick="navigateTo('water-cutmeter')">
                    <div class="menu-icon">♻️</div>
                    <div class="menu-title">ตัดมิเตอร์ประปา</div>
                    <div class="menu-desc">ตัดมิเตอร์ประปา</div>
                </div>
                <div class="menu-item card-recycle" onclick="navigateTo('water-complain')">
                    <div class="menu-icon">♻️</div>
                    <div class="menu-title">เรื่องร้องเรียน/แจ้งเหตุงานประปา</div>
                    <div class="menu-desc">เรื่องร้องเรียน/แจ้งเหตุงานประปา</div>
                </div>

                <div class="menu-item card-organic" onclick="navigateTo('water-tabwater-record')">
                    <div class="menu-icon">🍂</div>
                    <div class="menu-title">จดมิเตอร์ประปา</div>
                    <div class="menu-desc">จดมิเตอร์ประปา</div>
                </div>

                <div class="menu-item card-water" onclick="navigateTo('water-equipment-control')">
                    <div class="menu-icon">💧</div>
                    <div class="menu-title">ควบคุมงานผลิตน้ำ</div>
                    <div class="menu-desc">ควบคุมงานผลิตน้ำ</div>
                </div>


                
            </div>
        </div>
    </div>

    <div id="waterRecordScreen" class="is-hidden">
        @include('staff.includes.screen_water_record')
    </div>

    <div id="waterMembersListScreen" class="is-hidden">
        @include('staff.includes.screen_water_members_list')
    </div>
    <div id="inventoryScreen" class="is-hidden">
        <iframe src="{{ route('inventory.items.iframe') }}" 
            style="width: 100%; height: 600px; border: none;" 
            id="inventoryIframe">
    </iframe>
    </div>

    <div id="successToast" class="success-toast-overlay">
        <div class="toast-icon">✅</div>
        <div id="successToastText" class="toast-text">เพิ่มลงตะกร้าเรียบร้อย</div>
    </div>

    <div id="qr-reader-container"
        style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 9999; justify-content: center; align-items: center; flex-direction: column;">
        <div id="reader" style="width: 90%; max-width: 400px; background: #fff; padding: 10px; border-radius: 8px;"></div>
        <button id="btnCloseScanner"
            style="margin-top: 15px; padding: 10px 20px; background: #ff4d4d; color: #fff; border: none; border-radius: 5px; cursor: pointer;">ปิดกล้อง</button>
    </div>

    <!-- Modal สำหรับแสดงหน้า Dashboard/Staff -->
    <div class="modal fade" id="staffDashboardModal" tabindex="-1" aria-labelledby="staffDashboardModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staffDashboardModalLabel">ระบบจัดการงาน Staff</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <!-- ส่วนที่จะเอา HTML จาก Server มาใส่ -->
                <div class="modal-body" id="modal-staff-content">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status"></div>
                        <p class="mt-2 text-muted">กำลังโหลดข้อมูล...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="card-reciept" style="width: 384px; 
                                background: #ffffff; 
                                color: #000000;
                                font-size:1.4rem !important;
                                ">
    </div>

{{-- <div id="qrcode_info"></div> --}}

@endsection

@section('script')
    <script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
    <script src="https://unpkg.com/html5-qrcode"></script>
    <script src="https://code.jquery.com/jquery-4.0.0.slim.js"
        integrity="sha256-M+GjhMBfXikM1izMplICCTscIj5hzPCp6uDzaypxtgg=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- แนะนำให้วางไว้ในส่วน <head> หรือก่อนปิด </body> -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- โหลด Tesseract.js ผ่าน CDN (หรือโหลดมาเก็บไว้ใน public/js ของ Laravel) -->
<script src="https://cdn.jsdelivr.net/npm/tesseract.js@5/dist/tesseract.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script>

        let BluetoothSerial = null;
        let allMembers = [];                 // เก็บรายชื่อสมาชิกทั้งหมดที่ดึงมาจาก Laravel
        const API_BASE_URL = "https://8d74-1-47-53-190.ngrok-free.app/api";

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

        {!! file_get_contents(resource_path('views/staff/includes/screen_login.js')) !!}
        {!! file_get_contents(resource_path('views/staff/includes/screen_water_record.js')) !!}
        // {!! file_get_contents(resource_path('views/staff/includes/screen_water_members_list.js')) !!}
        {!! file_get_contents(resource_path('views/staff/includes/navigate_to.js')) !!}
        {!! file_get_contents(resource_path('views/staff/includes/bluethooth.js')) !!}
        {!! file_get_contents(resource_path('views/staff/includes/modals.js')) !!}





        viewFullImage = function (url) {
            if (!url) return;
            const modalEl = document.getElementById('imagePreviewModal');
            const targetImg = document.getElementById('previewImageTarget');
            if (modalEl && targetImg) {
                targetImg.src = url;
                modalEl.classList.add('show');
                modalEl.style.display = 'block';
            }
        };

    // ฟังก์ชันช่วยโหลด Script QRCode อัตโนมัติกรณีที่ยังไม่มีในระบบ
function loadQRCodeScript() {
    return new Promise((resolve, reject) => {
        if (typeof QRCode !== 'undefined') {
            resolve();
            return;
        }
        const script = document.createElement('script');
        script.src = "https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js";
        script.onload = () => resolve();
        script.onerror = () => reject(new Error("ไม่สามารถโหลดไลบรารี QRCode ได้"));
        document.head.appendChild(script);
    });
}

// ฟังก์ชันแปลงข้อความชำระเงินเป็น Base64 Image
async function generateQRBase64(text) {
    // 1. รอให้มั่นใจว่าไลบรารี QRCode โหลดเสร็จแล้ว
    await loadQRCodeScript();

    return new Promise((resolve) => {
        let tempDiv = document.createElement("div");

        new QRCode(tempDiv, {
            text: text,
            width: 150,
            height: 150,
            correctLevel: QRCode.CorrectLevel.M
        });

        // หน่วงเวลาเล็กน้อยให้ DOM เรนเดอร์รูปภาพเสร็จ
        setTimeout(() => {
            let img = tempDiv.querySelector("img");
            if (img && img.src) {
                resolve(img.src);
            } else {
                let canvas = tempDiv.querySelector("canvas");
                resolve(canvas ? canvas.toDataURL("image/png") : "");
            }
        }, 50);
    });
}

async function buildReceiptHtml() {
    let user_id = 11;
    let meter_id = String(user_id).padStart(18, '0');
    let net_paid = 263.60;
    let amount_formatted = net_paid.toFixed(2).replace(".", "");

    let payment_str = `|099400035262000\n${meter_id}\n${inv_id}\n${amount_formatted}`;

    // 🟢 เปลี่ยน text (payment_str) ให้กลายเป็นรูป QR Code (Base64)
    let qrImageBase64 = await generateQRBase64(payment_str);


    // ใส่ qrImageBase64 ลงในแท็ก <img> แทนการแสดง text ดิบ
   // let text = generateWaterBillHTML2()
    $('#qrcode').html( `
    <div id="receipt-area" class="receipt-container">
        <!-- รายการค่าน้ำประปา / รายละเอียดต่างๆ -->
        ...
        
        <!-- ส่วนแสดง QR Code -->
        <div class="text-center my-2">
            <img src="${qrImageBase64}" style="width: 180px; height: 180px;" alt="QR Code">
            <div style="font-size: 10px;">Ref1: ${meter_id}</div>
            <div style="font-size: 10px;">Ref2: ${inv_id}</div>
        </div>
    </div>
    `);
}


        function backToMenu() {
            document.getElementById('recycleScreen').classList.add('is-hidden');
            document.getElementById('mainScreen').classList.remove('is-hidden');
        }

        function backToMenuFromSettings() {
            document.getElementById('settingsScreen').classList.add('is-hidden');
            document.getElementById('mainScreen').classList.remove('is-hidden');
        }

        function renderMemberList(members) {
            const container = document.getElementById('memberListContainer');
            const countSpan = document.getElementById('memberCount');
            if (!container || !countSpan) return;

            container.innerHTML = "";
            countSpan.innerText = members.length;

            if (members.length === 0) {
                container.innerHTML = `<p style="text-align: center; color: #dc3545; margin-top:20px;">❌ ไม่พบรายชื่อสมาชิกที่ค้นหา</p>`;
                return;
            }

            members.forEach(member => {
                const account = member.waste_preference.kp_bank_account.account_no;
                const card = document.createElement('div');
                card.className = 'member-card';
                card.innerHTML = `
                                <div class="member-info">
                                    <p style="font-weight: bold; font-size: 16px; margin: 0 0 5px 0;">${member.firstname} ${member.lastname}</p>
                                    <p style="margin: 0 0 5px 0; font-size: 14px; color: #555;">📞 เบอร์โทร: ${member.phone || 'ไม่มีข้อมูล'}</p>
                                    <p style="margin: 0;"><span class="badge-account" style="background: #e6f0fa; color: #007bff; padding: 3px 8px; border-radius: 4px; font-size: 12px;">เลขบัญชี: ${account ? account : 'ไม่มีบัญชี'}</span></p>
                                </div>
                                <div>
                                    <button class="btn-deposit" onclick="selectMemberToDeposit(${member.id})" style="background: #28a745; color: white; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer; font-weight: bold;">
                                        💰 รับซื้อขยะรีไซเคิล
                                    </button>
                                </div>
                            `;
                container.appendChild(card);
            });
        }

        function filterMembers() {
            const searchText = document.getElementById('searchMemberRecycleInput').value.toLowerCase();
            const filtered = allMembers.filter(member => {
                const fullName = `${member.firstname} ${member.lastname}`.toLowerCase();
                const phone = (member.phone || "").toLowerCase();
                return fullName.includes(searchText) || phone.includes(searchText);
            });
            renderMemberList(filtered);
        }

        // 🟢 เพิ่มฟังก์ชันนี้ต่อท้ายตัวแปรโกลบอลด้านบนของ app.js
        async function loadMembersFromServer() {
            console.log('loadMembersFromServer')
            let org_id_fk = localStorage.getItem('staff_org_id')
            try {
                // console.log("กำลังดึงรายชื่อสมาชิกทั้งหมดจากระบบ Keptkaya..." + API_BASE_URL);

                const response = await fetch(`${API_BASE_URL}/keptkaya/members/${org_id_fk}`, {
                    method: "GET",
                    headers: {
                        "Content-Type": "application/json",
                        "Accept": "application/json",
                        "ngrok-skip-browser-warning": "true" // 🛡️ ดักหน้าต่างขาว ngrok
                    }
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const resData = await response.json();

                // ตรวจสอบว่ามีข้อมูลกลับมาตาม format { code: 200, data: [...] } ไหม
                if (resData.code === 200 && Array.isArray(resData.data)) {
                    allMembers = resData.data; // เอาข้อมูลยัดเข้าตัวแปรหลักของ master
                    // console.log('resData.data', resData.data)
                    // console.log(`โหลดข้อมูลสำเร็จ! พบสมาชิกทั้งหมด: ${allMembers.length} คน`);

                    // 💡 เรียกฟังก์ชันอัปเดตหน้าจอ 2 แท็บทำงานต่อ (รอเขียนในด่านถัดไป)
                    if (typeof updateMemberListUI === "function") {
                        updateMemberListUI();
                    }
                } else {
                    console.error("รูปแบบข้อมูลจาก Server ไม่ถูกต้อง:", resData);
                }

            } catch (error) {
                console.error("เกิดข้อผิดพลาดในการโหลดข้อมูลสมาชิก:", error);
                alert("ไม่สามารถดึงข้อมูลสมาชิกจากระบบได้: " + error.message);
            }
        }

        // 📷 ปุ่มสแกน QR Code ค้นหาบัตรสมาชิกชาวบ้าน
        const btnScanQR = document.getElementById('btnScanQR');
        let html5QrCode = null; // ตัวแปรสำหรับเก็บ instance ของตัวสแกน

        if (btnScanQR) {
            btnScanQR.addEventListener('click', async () => {
                if (typeof Capacitor !== 'undefined' && Capacitor.Plugins && Capacitor.Plugins.BarcodeScanning) {
                    const { BarcodeScanning } = Capacitor.Plugins;
                    try {
                        const permission = await BarcodeScanning.requestPermissions();
                        if (permission.camera === 'granted') {
                            const { barcodes } = await BarcodeScanning.scan();
                            if (barcodes.length > 0) {
                                const scannedValue = barcodes[0].rawValue;
                                document.getElementById('searchMemberRecycleInput').value = scannedValue;
                                filterMembers();
                            }
                        } else {
                            alert("แอปไม่ได้รับอนุญาตให้เข้าถึงกล้องถ่ายภาพ");
                        }
                    } catch (e) {
                        console.error("QR Code Scan Error:", e);
                        alert("ระบบกล้องขัดข้อง: " + e);
                    }
                } else {
                    // ส่วนที่แก้ไข: เปิดกล้องหลังบน Browser ของ Smartphone
                    const scannerContainer = document.getElementById('qr-reader-container');
                    if (scannerContainer) {
                        scannerContainer.style.display = 'flex';

                        if (!html5QrCode) {
                            html5QrCode = new Html5Qrcode("reader");
                        }

                        // กำหนด config บังคับเปิดกล้องหลังด้วย facingMode: "environment"
                        const cameraConfig = { facingMode: "environment" };
                        const scanConfig = { fps: 10, qrbox: { width: 250, height: 250 } };

                        html5QrCode.start(
                            cameraConfig,
                            scanConfig,
                            (decodedText, decodedResult) => {
                                // เมื่อสแกนสำเร็จ
                                document.getElementById('searchMemberRecycleInput').value = decodedText;
                                filterMembers();
                                stopWebScanner();
                            },
                            (errorMessage) => {
                                // ระหว่างกำลังสแกนหา QR Code (มองไม่เห็น QR)
                            }
                        ).catch((err) => {
                            console.error("ไม่สามารถเปิดกล้องบน Browser ได้:", err);
                            alert("ไม่สามารถเปิดกล้องได้ กรุณาอนุญาตสิทธิ์เข้าถึงกล้องใน Browser");
                            stopWebScanner();
                        });
                    }
                }
            });
        }

        // ฟังก์ชันสำหรับปิดกล้องและซ่อน Container
        function stopWebScanner() {
            if (html5QrCode && html5QrCode.isScanning) {
                html5QrCode.stop().then(() => {
                    document.getElementById('qr-reader-container').style.display = 'none';
                }).catch((err) => {
                    console.error("Failed to stop scanner", err);
                });
            } else {
                const scannerContainer = document.getElementById('qr-reader-container');
                if (scannerContainer) scannerContainer.style.display = 'none';
            }
        }

        // ผูกอีเวนต์กับปุ่มปิดกล้อง
        const btnCloseScanner = document.getElementById('btnCloseScanner');
        if (btnCloseScanner) {
            btnCloseScanner.addEventListener('click', stopWebScanner);
        }

        // =========================================================================
        //  🛠️ หน้ารับซื้อขยะ และ ระบบตะกร้าสินค้า (Purchase & Cart Module)
        // =========================================================================
        async function selectMemberToDeposit(memberId) {
            const member = allMembers.find(m => m.id === memberId);
            if (member) {
                currentActiveMember = member;
                // console.log('member', member.wastePreference)
                const accountNo = member.waste_preference.kp_bank_account.account_no;
                document.getElementById('lblDepositMemberName').innerText = `คุณ ${member.firstname} ${member.lastname}`;
                document.getElementById('lblDepositMemberAcc').innerText = accountNo;
                document.getElementById('lblActiveStaffName').innerText = localStorage.getItem("staff_name") || "เจ้าหน้าที่";

                resetSelection();

                // ข้อมูลประเภทขยะจำลอง
                try {
                    console.log(`กำลังดึงข้อมูลขยะรีไซเคิลจาก Server สำหรับสมาชิก ID: ${memberId}`);
                    console.log('curmember', currentActiveMember)
                    // 🟢 2. สั่งยิง Request ไปที่ URL ของ Server
                    const response = await fetch(`${API_BASE_URL}/keptkaya/kp_items_recycle_info`, {
                        method: "GET", // ขอข้อมูลใช้ GET
                        headers: {
                            "Content-Type": "application/json",
                            // 🚨 สำคัญมากสำหรับ ngrok: ใส่ตั๋วใบนี้เพื่อไม่ให้ ngrok แสดงหน้าเว็บ Browser Warning ไม่งั้นข้อมูลจะไม่เข้าแอปครับ
                            "ngrok-skip-browser-warning": "true"
                        }
                    });
                    console.log('resxx', response)
                    // 🟢 3. ตรวจสอบว่า Server ตอบกลับมาสำเร็จไหม (Status 200-299)
                    if (!response.ok) {
                        throw new Error(`Server ตอบกลับผิดพลาด: ${response.status}`);
                    }

                    // 🟢 4. แปลงข้อมูลดิบจาก Server ให้กลายเป็น Object/Array รูปแบบ JSON
                    const dataFromServer = await response.json();

                    // 🟢 5. นำข้อมูลจาก Server มาใส่ในตัวแปร rawItemsData แทนอันเดิม (สมมติว่า API ส่งกลับมาเป็นอาเรย์ตรงๆ)
                    // Note: ถ้าใน API ของพี่ส่งมาแบบครอบด้วยวัตถุ เช่น { status: true, data: [...] } ให้พี่แก้เป็น dataFromServer.data นะครับ
                    rawItemsData = dataFromServer;

                    console.log("ดึงข้อมูลประเภทขยะจาก Server สำเร็จแล้วครับพี่!:", rawItemsData);


                    renderGroupFilters();
                    renderFloatingCart();

                    document.getElementById('recycleScreen').classList.add('is-hidden');
                    document.getElementById('depositScreen').classList.remove('is-hidden');

                } catch (error) {
                    console.error("เกิดข้อผิดพลาดตอนดึงข้อมูลขยะ:", error);
                    alert("ไม่สามารถดึงข้อมูลประเภทขยะจากระบบได้: " + error.message);

                    // แผนสำรอง (Fallback): ถ้าเซิร์ฟเวอร์ล่ม ค่อยเอาข้อมูลจำลองเดิมมาเสียบดักพังไว้ก่อนได้ครับพี่
                    rawItemsData = [
                        { id: 10, kp_itemscode: "P001", kp_itemsname: "ขวดพลาสติกใส (PET)", group_id: 3, group_name: "พลาสติก", price: 4.5 },
                        { id: 11, kp_itemscode: "P002", kp_itemsname: "ขวดพลาสติกขุ่น (HDPE)", group_id: 3, group_name: "พลาสติก", price: 2.0 },
                        { id: 20, kp_itemscode: "W001", kp_itemsname: "กระดาษลัง", group_id: 1, group_name: "กระดาษ", price: 7.5 },
                        { id: 30, kp_itemscode: "G001", kp_itemsname: "ขวดแก้วสีชา", group_id: 2, group_name: "แก้ว/ขวด", price: 1.5 },
                        { id: 40, kp_itemscode: "M001", kp_itemsname: "กระป๋องอลูมิเนียม", group_id: 4, group_name: "โลหะ", price: 48.0 }
                    ];
                }


            }
        }

        function renderGroupFilters() {
            console.log('renderGroupFilters()')
            const container = document.getElementById('groupFiltersContainer');
            if (!container) return;
            container.innerHTML = "";

            const groups = [];
            const map = new Map();
            for (const item of rawItemsData) {
                if (!map.has(item.group_id)) {
                    map.set(item.group_id, true);
                    groups.push({ id: item.group_id, name: item.group_name });
                }
            }

            groups.forEach(g => {
                const filterDiv = document.createElement('div');
                filterDiv.className = "col-3";
                // filterDiv.style.cssText = "display:flex; flex-direction:column; align-items:center; cursor:pointer;";
                filterDiv.onclick = () => triggerFilter(g.id, filterDiv);

                let icon = "📁";
                // if (g.name.includes("กระดาษ")) icon = "📰";
                // else if (g.name.includes("แก้ว")) icon = "🍾";
                // else if (g.name.includes("พลาสติก")) icon = "🥤";
                // else if (g.name.includes("โลหะ")) icon = "🛠️";

                filterDiv.innerHTML = `
                                    <button type="button" class="btn btn-outline-dark rounded-circle" style="width: 60px; height: 60px; border-radius:50%; border:1px solid #ccc; background:#fff; font-size:20px;text-align:center">
                                        ${icon}
                                    </button>
                                    <small class="mt-1 text-muted fw-bold " style="font-size: 15px; margin-top:5px; color:green; font-weight:bold;text-align:center">${g.name}</small>
                                `;
                container.appendChild(filterDiv);
            });
        }


        function triggerFilter(groupId, element) {
            document.querySelectorAll('.filter-item button').forEach(b => {
                b.style.background = "#fff"; b.style.color = "#000"; b.style.borderColor = "#ccc";
            });
            document.querySelectorAll('.filter-item small').forEach(s => s.style.color = "#6c757d");

            const targetBtn = element.querySelector('button');
            const targetTxt = element.querySelector('small');
            if (targetBtn && targetTxt) {
                targetBtn.style.background = "#212529"; targetBtn.style.color = "#fff";
                targetTxt.style.color = "#212529";
            }

            const subContainer = document.getElementById('item-buttons-container');
            if (!subContainer) return;
            subContainer.innerHTML = "";


            const filteredItems = rawItemsData.filter(item => item.group_id === groupId);
            filteredItems.forEach(opt => {
                console.log('opt', opt)
                const gridCol = document.createElement('div');
                gridCol.className = 'trash-item-card-compact';
                gridCol.id = `card-${opt.kp_itemscode}`;
                gridCol.onclick = () => selectTrashItem(opt.kp_itemscode);
                gridCol.innerHTML = `
                                                                            <div class="item-title">${opt.kp_itemsname}</div>
                                                                            <div class="item-price">${opt.price} บาท./${opt.unitname}</div>
                                                                        `;
                subContainer.appendChild(gridCol);

            });

            // เพิ่มการสั่งเปิด Modal ให้แสดงผลขึ้นมา
            const modal = document.getElementById('categoryItemsModal');
            if (modal) {
                modal.classList.add('is-active');
            }
        }

        // ฟังก์ชันสำหรับเปิด/ปิด Modal เลือกหมวดหมู่ขยะ
        function openCategoryModal() {
            const modal = document.getElementById('categoryItemsModal');
            if (modal) {
                modal.classList.add('is-active');
            }
        }

        function closeCategoryModal() {
            const modal = document.getElementById('categoryItemsModal');
            if (modal) {
                modal.classList.remove('is-active');
            }
        }

        // แก้ไขฟังก์ชัน selectTrashItem (ปรับปรุงกระบวนการสลับ Modal)
        function selectTrashItem(itemCode) {
            console.log('selectTrashItem(itemCode)', itemCode)
            document.querySelectorAll('#item-buttons-container div').forEach(d => {
                d.style.borderColor = "#e3e6f0";
                d.style.background = "#fff";
            });
            const activeCard = document.getElementById(`card-${itemCode}`);
            if (activeCard) {
                activeCard.style.borderColor = "#28a745";
                activeCard.style.background = "#eafaf1";
            }

            const trash = rawItemsData.find(item => item.kp_itemscode === itemCode);
            if (trash) {
                currentSelectedTrash = trash;
                console.log('currentSelectedTrashccc', trash);

                // ปิด Modal เลือกขยะ แล้วเปิด Modal กรอกน้ำหนัก
                closeCategoryModal();
                openWeightModal(trash.kp_itemsname);

                const mockupUnits = [
                    { id: 1, unitname: trash.unitname },
                    // { id: 2, unitname: "ขวด" }
                ];
                renderUnitButtons(trash.units || mockupUnits);
                const amountInput = document.getElementById('amount_in_units');
                if (amountInput) {
                    amountInput.value = "";
                    amountInput.focus();
                }
            }
        }



        function renderUnitButtons(unitsArray) {
            const container = document.getElementById('unit-buttons-container');
            if (!container) return;
            container.innerHTML = "";

            if (!unitsArray || unitsArray.length === 0) {
                unitsArray = [{ id: 1, unitname: "กิโลกรัม" }];
            }

            unitsArray.forEach((unit, index) => {
                const btn = document.createElement('button');
                btn.type = "button";
                btn.innerText = unit.unitname;
                btn.style.cssText = "flex: 1; height: 58px; border: 1px solid #ced4da; background: #f8f9fa; border-radius: 8px; font-weight: bold; font-size: 16px; cursor: pointer; transition: all 0.2s;";

                if (index === 0) {
                    selectUnitItem(unit.id, btn);
                }

                btn.onclick = () => selectUnitItem(unit.id, btn);
                container.appendChild(btn);
            });
        }

        function selectUnitItem(unitId, element) {
            document.querySelectorAll('#unit-buttons-container button').forEach(b => {
                b.style.background = "#9dc3e8"; b.style.color = "#212529"; b.style.borderColor = "#ced4da";
            });
            element.style.background = "#007bff";
            element.style.color = "#000000";
            element.style.borderColor = "#007bff";

            currentSelectedUnitId = unitId;
            const unitInput = document.getElementById('kp_units_idfk');
            if (unitInput) unitInput.value = unitId;
        }

        function resetSelection() {
            currentSelectedTrash = null;
            currentSelectedUnitId = null;
            document.querySelectorAll('#item-buttons-container div').forEach(d => {
                d.style.borderColor = "#e3e6f0"; d.style.background = "#fff";
            });

            const amountInput = document.getElementById('amount_in_units');
            const kpUnitsInput = document.getElementById('kp_units_idfk');
            if (amountInput) amountInput.value = "";
            if (kpUnitsInput) kpUnitsInput.value = "";

            const unitContainer = document.getElementById('unit-buttons-container');
            if (unitContainer) unitContainer.innerHTML = "";
        }

        // 🛒 เพิ่มรายการลงตะกร้าฝากขยะ
        const btnAddToCart = document.getElementById('btnAddToCart');
        if (btnAddToCart) {
            btnAddToCart.addEventListener('click', () => {
                if (!currentSelectedTrash) {
                    alert("กรุณาจิ้มเลือกประเภทขยะในกล่องสีเทาก่อนครับ!");
                    return;
                }

                const weight = parseFloat(document.getElementById('amount_in_units').value) || 0;
                if (weight <= 0) {
                    alert("กรุณากรอกจำนวนขยะที่รับซื้อ");
                    return;
                }

                const itemTotalPrice = currentSelectedTrash.price * weight;
                const activeUnitBtn = document.querySelector('#unit-buttons-container button[style*="rgb(0, 123, 255)"]');

                const savedItemName = currentSelectedTrash.kp_itemsname;
                console.log('currentSelectedTrash', currentSelectedTrash)
                purchaseCart.push({
                    item_prc_pnt_id: currentSelectedTrash.prc_pnt_id,
                    item_id: currentSelectedTrash.id,
                    item_code: currentSelectedTrash.kp_itemscode,
                    item_name: currentSelectedTrash.kp_itemsname,
                    price_per_unit: currentSelectedTrash.price,
                    point: currentSelectedTrash.point,
                    amount_in_units: weight,
                    unit_name: currentSelectedTrash.unit_name,
                    unit_short_name: currentSelectedTrash.unit_short_name,
                    amount: itemTotalPrice
                });
                console.log('purchaseCart', purchaseCart)
                localStorage.setItem("current_purchase_cart", JSON.stringify(purchaseCart));

                // ❌ ลบคำสั่ง alert(...) ของเก่าที่ต้องคอยกดตกลงออกไปเรียบร้อย

                // 🟢 เปลี่ยนมาใช้ Toast สไตล์แอปโมบายเวอร์ชันโปรสปีดแทน!
                showSuccessToast(`📥 เพิ่ม ${savedItemName} เรียบร้อย!`);

                // ม้วนหน้าต่างกรอกน้ำหนักลงใต้จอทันที ไม่ขัดจังหวะสายตา
                if (typeof closeWeightModal === "function") {
                    closeWeightModal();
                }

                resetSelection();
                renderFloatingCart();
            });
        }

        function renderFloatingCart() {
            const container = document.getElementById('floatingCartContainer');
            if (!container) return;
            container.innerHTML = "";
            if (purchaseCart.length === 0) return;

            let grandTotal = 0;
            let pointTotal = 0;

            purchaseCart.forEach(c => grandTotal += c.amount);

            const cartBtn = document.createElement('button');
            cartBtn.type = "button";
            cartBtn.style.cssText = "position:fixed; bottom:30px; right:30px; width:80px; height:80px; border-radius:50%; background:#28a745; color:#fff; border:3px solid #fff; box-shadow:0 8px 24px rgba(114, 246, 144, 0.4); font-size:35px; cursor:pointer; z-index:999; display:flex; align-items:center; justify-content:center;";
            cartBtn.innerHTML = `🛒<span style="position:absolute; top:-20px; right:-10px; background:#dc3545; font-size:25px; font-weight:bold; padding:4px 14px; border-radius:50%; border:2px solid #fff; min-width:20px; text-align:center;">${purchaseCart.length}</span>`;
            cartBtn.onclick = () => openCartModal(grandTotal);
            container.appendChild(cartBtn);
        }

        function openCartModal(grandTotal) {
            const oldModal = document.getElementById('customCartModal');
            if (oldModal) oldModal.remove();

            const modal = document.createElement('div');
            modal.id = "customCartModal";
            modal.style.cssText = "position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); display:flex; align-items:center; justify-content:center; z-index:10000; padding:15px; box-sizing:border-box;";

            let tableRows = "";
            purchaseCart.forEach((item, index) => {
                tableRows += `
                                <tr style="border-bottom:1px solid #eee; height:50px;">
                                    <td style="padding:5px;"><b>${item.item_name}</b><br><small style="color:#888;">${item.price_per_unit.toFixed(2)} บ./${item.unit_short_name}</small></td>
                                    <td style="text-align:right; padding:5px;">${item.amount_in_units.toFixed(2)} ${item.unit_short_name}</td>
                                    <td style="text-align:right; padding:5px; color:#28a745; font-weight:bold;">${item.amount.toFixed(2)} บ.</td>
                                    <td style="text-align:center; padding:5px;"><button onclick="removeFromCart(${index})" style="background:none; border:none; color:#dc3545; font-size:18px; cursor:pointer;">🗑️</button></td>
                                </tr>
                            `;
            });

            modal.innerHTML = `
                                                                                    <div style="background:#fff; width:100%; max-width:500px; border-radius:15px; padding:20px; box-shadow:0 10px 25px rgba(0,0,0,0.1); position:relative; font-family:sans-serif;">
                                                                                        <h4 style="margin-top:0; font-weight:bold; color:#333;">🛒 รายการในตะกร้า</h4>
                                                                                        <div style="max-height:250px; overflow-y:auto; margin-bottom:15px;">
                                                                                            <table style="width:100%; border-collapse:collapse; font-size:14px;">
                                                                                                <thead>
                                                                                                    <tr style="background:#f8f9fa; height:35px; text-align:left; color:#6c757d;">
                                                                                                        <th>สินค้า</th><th style="text-align:right;">จำนวน</th><th style="text-align:right;">รวม (บาท)</th><th style="text-align:center;">ลบ</th>
                                                                                                    </tr>
                                                                                                </thead>
                                                                                                <tbody>${tableRows}</tbody>
                                                                                            </table>
                                                                                        </div>
                                                                                        <div style="display:flex; justify-content:space-between; align-items:center; background:#f8f9fa; padding:15px; border-radius:10px; margin-bottom:15px;">
                                                                                            <span>ยอดสุทธิรวม:</span>
                                                                                            <span style="font-size:22px; font-weight:bold; color:#28a745;">${grandTotal.toFixed(2)} บาท</span>
                                                                                            <input type="checkbox" id="cashback" name="cashback" style="width:30px; height:30px">จ่ายเงินสด
                                                                                        </div>
                                                                                        <div style="display:flex; gap:10px;">
                                                                                            <button onclick="document.getElementById('customCartModal').remove()" style="flex:1; padding:12px; border-radius:8px; border:1px solid #ccc; background:#ccc; font-weight:bold; cursor:pointer;">ปิดหน้าต่าง</button>
                                                                                            <button onclick="submitFinalPurchase(0)" style="flex:2; padding:12px; border-radius:8px; border:none; background:#28a745; color:#fff; font-weight:bold; font-size:16px; cursor:pointer;">📝 ยืนยันบันทึก</button>
                                                                                            <button onclick="submitFinalPurchase(1)" id="submit_and_print"  
                                                                                                style="flex:2; padding:12px; border-radius:8px; border:none; background:#28a745; color:#fff; font-weight:bold; font-size:16px; cursor:pointer;">📝 ยืนยันบันทึก & ปริ้นบิล</button>
                                                                                        </div>
                                                                                    </div>
                                                                                `;
            document.body.appendChild(modal);
        }

        function removeFromCart(index) {
            purchaseCart.splice(index, 1);
            localStorage.setItem("current_purchase_cart", JSON.stringify(purchaseCart));
            document.getElementById('customCartModal').remove();
            renderFloatingCart();
            if (purchaseCart.length > 0) {
                let grandTotal = 0;
                let pointTotal = 0;

                purchaseCart.forEach(c => grandTotal += c.amount);
                openCartModal(grandTotal);
            }
        }

        // 📝 ส่งบิลสรุปยอดส่งไปหลังบ้าน และสั่งพิมพ์ใบเสร็จอัตโนมัติ
        // 🟢 ปรับปรุงฟังก์ชันบันทึกในฝั่ง app.js เพื่อยิงขึ้น Server จริงก่อนพิมพ์
        async function submitFinalPurchase(print_bill) {
            console.log('submitFinalPurchase()')
            if (purchaseCart.length === 0) {
                alert("❌ ไม่มีรายการขยะในตะกร้า");
                return;
            }

            BluethoothConnectedModal('recycle');
            // คำนวณยอดรวมสุทธิจากตะกร้าแอป
            const totalWeight = purchaseCart.reduce((sum, item) => sum + parseFloat(item.amount_in_units), 0);
            const totalAmount = purchaseCart.reduce((sum, item) => sum + parseFloat(item.amount), 0);
            const totalPoints = purchaseCart.reduce((sum, item) => sum + parseInt(item.point), 0);

            const staff_id = localStorage.getItem('staff_id');
            // เตรียมก้อนข้อมูลโครงสร้างแปลงส่งไปหลังบ้านตามโมเดล Laravel ของพี่
            console.log('currentActiveMember', currentActiveMember)
            const payload = {
                org_id_fk: staffInfo.org_id_fk,
                kp_user_w_pref_id_fk: currentActiveMember.waste_preference.id, // อ้างอิง ID preference ของสมาชิก
                user_id: currentActiveMember.id,
                recorder_id: staffInfo.id, // สามารถผูกกับไอดีของ Staff ที่ล็อกอินค้างไว้ได้ครับ
                total_weight: totalWeight,
                cashback: document.getElementById("cashback").checked,
                total_amount: totalAmount,
                total_points: totalPoints,
                total_carbon_saved: 0.0000, // ค่าเริ่มต้น
                cart_items: purchaseCart.map(item => ({
                    kp_recycle_item_id: item.item_id,
                    kp_tbank_items_pricepoint_id: item.item_prc_pnt_id || 1, // ไอดีตารางราคา
                    amount_in_units: parseFloat(item.amount_in_units),
                    kp_units_idfk: item.unit_id || 1,
                    price_per_unit: parseFloat(item.price_per_unit),
                    amount: parseFloat(item.price_per_unit * item.amount_in_units),
                    points: parseInt(item.point * item.amount_in_units),
                    carbon_saved: 0.0000
                }))
            };
            console.log('payload', payload)

            try {
                // แจ้งเตือนสตาฟฟ์หน้างานระหว่างส่งข้อมูล
                console.log("กำลังส่งข้อมูลบิลไปบันทึกที่เซิร์ฟเวอร์...");

                const response = await fetch(`${API_BASE_URL}/keptkaya/store_purchase`, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "Accept": "application/json",
                        "ngrok-skip-browser-warning": "true"
                    },
                    body: JSON.stringify(payload)
                });

                const resData = await response.json();

                if (resData.code === 200) {

                    let recieptText =
                        `<div class="row">
                            <div class="col-5">
                                <img src="{{ asset('logo/${staffInfo.org.org_logo_img}') }}" width="100%" alt="">
                            </div>
                            <div class="col-7" id="org">
                                <div style="font-size: 1.5rem;">${staffInfo.org.org_type_name}</div>
                                <div style="font-size: 2rem; margin-top: -10px;">${staffInfo.org.org_name}</div>

                            </div>
                            <div class="text-right" id="org_address">
                                <div>${staffInfo.org.org_address} หมู่ ${staffInfo.org.org_zone} ต.${staffInfo.org.org_tambon}</div>
                                <div>อ.${staffInfo.org.org_district} จ.${staffInfo.org.org_province}</div>
                                <div>โทร.${staffInfo.org.org_phone}</div>
                            </div>
                        </div>

                        <p>ธนาคารขยะรีไซเคิล</p>
                        <div id="member_info">
                            <div class="row">
                                <div class="col-3 header">ชื่อ-สกุล:</div>
                                <div class="col-9">${currentActiveMember.firstname} ${currentActiveMember.lastname}</div>
                                <div class="col-3 header">ที่อยู่:</div>
                                <div class="col-9 info">
                                    <div>${currentActiveMember.address} หมู่ ${currentActiveMember.waste_preference.user_pref_zone.zone_name} ต.${staffInfo.org.org_tambon}</div>
                                    <div>อ.${staffInfo.org.org_district} จ.${staffInfo.org.org_province}</div>
                                </div>
                                <div class="col-3 header">รหัส:</div>
                                <div class="col-9 info">${currentActiveMember.waste_preference.id}</div>
                            </div>
                        </div>

                        <div id="reciept_info" style="margin-bottom:10px">
                            <div class="row">
                                <div class="col-4 header">วันที่:</div>
                                <div class="col-8">${resData.data.transaction_date}</div>
                                <div class="col-4 header">เลขใบเสร็จ:</div>
                                <div class="col-8 info">${resData.data.receipt_no}</div>
                            </div>
                        </div>
                        <table border="0" width="100%">
                            <thead>
                                <tr>
                                    <td width="60%"></td>
                                    <td width="15%">แต้ม</td>
                                    <td width="25%">บาท</td>
                                </tr>
                            </thead>
                            <tbody>`;


                    let totalAmount = 0;
                    let totalPoint = 0;

                    purchaseCart.forEach(v => {
                        let _points = v.point * v.amount_in_units;
                        recieptText += `
                                                <tr>
                                                    <td style="text-align: left; font-size: 1.5rem;line-height: 22px;">
                                                        -${v.item_name} 
                                                        <div style="font-size: 1rem; padding-left: 10px; ">
                                                            <span style="font-size: 1.4rem;">${v.amount_in_units} ${v.unit_short_name}   </span>
                                                            <span style="margin-left:10px">(${v.price_per_unit} บาท, ${v.point} แต้ม):${v.unit_short_name}</span>
                                                        </div>
                                                    </td>
                                                    <td class="amount">${_points}</td>
                                                    <td class="amount">${v.amount.toFixed(2)}</td>
                                                </tr>

                                                `;
                        totalAmount += v.amount;
                        totalPoint += _points
                    })

                    recieptText += `    
                                            </tbody>
                                        </table>
                                        <hr style="color: #000000;opacity:1; margin-left: 20px;" width="90%">
                                        <table width="100%">
                                                <tbody>
                                                    <tr>

                                                        <td style="text-align: left; font-size: 1.5rem;font-weight:bold" width="60%">
                                                            รวมรับเงิน/แต้มสะสม
                                                        </td>
                                                        <td width="15%" style="line-height: 22px; font-weight:bold" class="amount">${totalPoint} <div style="font-size: 1rem;text-align: right">แต้ม</div></td>
                                                        <td width="25%" style="line-height: 22px; font-weight:bold" class="amount">${totalAmount.toFixed(2)} <div style="font-size: 1rem;text-align: right">บาท</div></td>
                                                    </tr>

                                                </tbody>
                                            </table>
                                        <hr style="opacity:1;">

                                        <div style="margin-bottom:3rem; margin-top:rem;text-align:center;font-size:1.4rem; font-weight:bold">
                                            ขอบคุณที่ร่วมลดโลกร้อน
                                        </div>`;


                    $('#card-reciept').html(recieptText)
                    // 🟢 จุดสำคัญ: เอาข้อมูลตัวจริงจาก Server (resData.data) ส่งไปสั่งพิมพ์ใบเสร็จบลูทูธ
                    if (typeof printReceipt === "function" && print_bill === 1) {
                        await printReceipt();
                    }

                    // เคลียร์ค่า และอัปเดตหน้าจอแอปพลิเคชันกลับไปสถานะเริ่มต้น
                    purchaseCart = [];
                    closePurchaseModal();
                    $('#card-reciept').html('')
                    // เปลี่ยนสถานะของสมาชิกคนนี้ในหน้าหลักเป็นเสร็จสิ้นทันที
                    onDepositSuccess(currentActiveMember.id);

                } else {
                    alert("❌ เซิร์ฟเวอร์ปฏิเสธการบันทึก: " + resData.message);
                }

            } catch (error) {
                console.error("Submit Purchase Error:", error);
                alert("❌ ไม่สามารถเชื่อมต่อฐานข้อมูลได้ กรุณาเช็กอินเทอร์เน็ตหน้างาน");
            }
            Swal.close();
            await Swal.fire({
                icon: 'success',
                title: 'บันทึกสำเร็จ!',
                timer: 1500,
                showConfirmButton: false
            });

            backToRecycleScreen();
        }

        function backToRecycleScreen() {
            document.getElementById('depositScreen').classList.add('is-hidden');
            document.getElementById('recycleScreen').classList.remove('is-hidden');
            currentActiveMember = null;
        }

        // 📷 ปุ่มสแกน QR Code ขยะเพื่อเลือกอัตโนมัติ
        const btnScanTrashQR = document.getElementById('btnScanTrashQR');
        if (btnScanTrashQR) {
            btnScanTrashQR.addEventListener('click', async () => {
                if (typeof Capacitor !== 'undefined' && Capacitor.Plugins && Capacitor.Plugins.BarcodeScanning) {
                    const { BarcodeScanning } = Capacitor.Plugins;
                    try {
                        const permission = await BarcodeScanning.requestPermissions();
                        if (permission.camera === 'granted') {
                            const { barcodes } = await BarcodeScanning.scan();
                            if (barcodes.length > 0) {
                                const code = barcodes[0].rawValue.toUpperCase();
                                selectTrashItem(code);
                            }
                        } else {
                            alert("แอปไม่ได้รับอนุญาตให้เข้าถึงกล้อง");
                        }
                    } catch (e) {
                        alert("ระบบกล้องขัดข้อง: " + e);
                    }
                } else {
                    alert("💡 จำลองการสแกน QR Code สินค้าขยะพลาสติกใสรหัส 'P001'");
                    selectTrashItem("P001");
                }
            });
        }

        // 🟢 ฟังก์ชันสำหรับอัปเดตสถานะแถบเครื่องพิมพ์ข้ามหน้าจอ (Global Badge Status)
        function updateGlobalPrinterStatus() {
            const isConnected = localStorage.getItem('is_printer_connected') === 'true';
            const printerName = localStorage.getItem('connected_printer_name') || 'เครื่องพิมพ์';

            const lblName = document.getElementById('lblGlobalPrinterName');
            const lblIndicator = document.getElementById('lblGlobalPrinterIndicator');
            const statusBox = document.getElementById('globalPrinterStatus');

            if (!statusBox || !lblName || !lblIndicator) return;

            if (isConnected || printCharacteristic) {
                lblName.innerText = `🟢 เชื่อมต่ออยู่กับ (${printerName})`;
                lblName.style.color = "#28a745";
                lblIndicator.style.background = "#28a745";
                statusBox.style.borderLeft = "5px solid #28a745";
            } else {
                lblName.innerText = "🔴 ยังไม่ได้เชื่อมต่ออุปกรณ์";
                lblName.style.color = "#dc3545";
                lblIndicator.style.background = "#dc3545";
                statusBox.style.borderLeft = "5px solid #dc3545";
            }
        }


        let currentTab = 'pending'; // แท็บปัจจุบัน (เริ่มต้นที่ pending)

        // 🟢 ฟังก์ชันสำหรับกดสลับแท็บ
        function switchTab(tabName) {
            currentTab = tabName;

            const btnPending = document.getElementById('btnTabPending');
            const btnCompleted = document.getElementById('btnTabCompleted');

            // เปลี่ยนสีปุ่มไฮไลท์แท็บให้สตาฟฟ์เห็นชัดๆ
            if (tabName === 'pending') {
                btnPending.style.background = '#007bff'; btnPending.style.color = 'white';
                btnCompleted.style.background = 'transparent'; btnCompleted.style.color = '#333';
            } else {
                btnCompleted.style.background = '#28a745'; btnCompleted.style.color = 'white'; // แท็บเสร็จแล้วใช้สีเขียว
                btnPending.style.background = 'transparent'; btnPending.style.color = '#333';
            }

            // สั่งให้รีเรนเดอร์หน้าจอใหม่ตามแท็บที่เลือก
            updateMemberListUI();
        }

        // 🟢 ฟังก์ชันคัดกรองข้อมูลส่งไปเรนเดอร์ และอัปเดตตัวเลขบนปุ่มแท็บ
        function updateMemberListUI() {
            // นับจำนวนเพื่อเอาไปแปะบนปุ่มแท็บให้รู้ยอดคงเหลือ
            const pendingTotal = allMembers.filter(m => m.status !== 'completed').length;
            const completedTotal = allMembers.filter(m => m.status === 'completed').length;

            document.getElementById('countPending').innerText = pendingTotal;
            document.getElementById('countCompleted').innerText = completedTotal;

            // กรองข้อมูลตามแท็บที่เปิดอยู่ปัจจุบันเพื่อส่งไปวาดการ์ด
            let filteredMembers = [];
            if (currentTab === 'pending') {
                filteredMembers = allMembers.filter(m => m.status !== 'completed');
            } else {
                filteredMembers = allMembers.filter(m => m.status === 'completed');
            }

            // เรียกใช้ฟังก์ชัน renderMemberList ตัวเดิมของคุณพี่ได้เลยครับ!
            renderMemberList(filteredMembers);
        }

        // โค้ดหลังจากเซฟลงฐานข้อมูลและปรินท์ใบเสร็จผ่านบลูทูธสำเร็จแล้ว
        function onDepositSuccess(completedMemberId) {
            // 1. ค้นหาตัวสมาชิกในตัวแปรอาร์เรย์หลัก แล้วเปลี่ยนสถานะเป็นสำเร็จ
            const member = allMembers.find(m => m.id === completedMemberId);
            if (member) {
                member.status = 'completed';
            }

            //alert("บันทึกข้อมูลและสั่งพิมพ์ใบเสร็จสำเร็จ!");

            // 2. สั่งอัปเดตหน้าจอทันที รายชื่อคนนี้จะหายไปจากแท็บปัจจุบันแล้วย้ายไปแท็บประวัติทันทีครับ
            updateMemberListUI();
        }

        // 🟢 ฟังก์ชันสลับการเปิด/ปิดเมนู Sidebar ด้านข้าง
        function toggleSidebar(isOpen, page) {
            console.log('isOpen', isOpen)
            if (!sidebar) return;

            if (isOpen) {
                sidebar.classList.add('is-open');
            } else {
                if (page === "main") {
                    navigateTo("main");
                } else if (page === "recycle") {
                    navigateTo("recycle");
                }
                else if (page === "water") {
                    navigateTo("recycle");
                }
                sidebar.classList.remove('is-open');
            }
        }

        // 🟢 ฟังก์ชันสั่งปลุกเรียก Modal กรอกน้ำหนักให้ดีดขึ้นมา
        function openWeightModal(itemName) {
            const modal = document.getElementById('weightInputModal');
            if (!modal) return;

            // แสดงชื่อขยะชิ้นที่จิ้มเลือกไว้บนหัวข้อ Modal ให้สตาฟฟ์เช็กความมั่นใจ
            document.getElementById('modal-selected-item-name').innerText = itemName;

            // เคลียร์กล่องคีย์จำนวนให้เป็นค่าว่างเพื่อพร้อมกดตัวเลขใหม่ทันที
            document.getElementById('amount_in_units').value = '';

            // สั่งเปิดทำงานคลาสแอนิเมชันสไลด์
            modal.classList.add('is-active');

            // ⚡ UX เทพ: สั่งให้คีย์บอร์ดตัวเลขเด้งเปิดรอบนจอมือถือออโตเมติกโดยที่สตาฟฟ์ไม่ต้องเอานิ้วไปกดจิ้มกล่องซ้ำอีกรอบ
            setTimeout(() => {
                document.getElementById('amount_in_units').focus();
            }, 300);
        }

        // 🟢 ฟังก์ชันสั่งปิดม้วน Modal ลงใต้จอ
        function closeWeightModal() {
            console.log('closeWeightModal')
            const modal = document.getElementById('weightInputModal');
            if (modal) {
                modal.classList.remove('is-active');
            }
        }

        function closePurchaseModal() {
            console.log('closePurchaseModal')
            const modal = document.getElementById('customCartModal');
            if (modal) {
                modal.remove();
            }
        }



        // 🟢 ฟังก์ชันสั่งโชว์ป้าย Correct Icon แล้วหายไปเองภายใน 1 วินาที
        function showSuccessToast(message) {
            const toast = document.getElementById('successToast');
            const toastText = document.getElementById('successToastText');
            if (!toast || !toastText) return;

            toastText.innerText = message; // เปลี่ยนข้อความตามชื่อขยะ
            toast.classList.add('show');  // สั่งให้เด้งโผล่ขึ้นมา

            // ⏳ ตั้งเวลาค้างไว้ 500 มิลลิวินาที (0.5 วินาที) แล้วปิดตัวเองออโต้
            setTimeout(() => {
                toast.classList.remove('show');
            }, 500);
        }

        // 🟢 ปรับปรุงฟังก์ชันเริ่มต้นแอปให้เน้นความปลอดภัยสูงสุด (Security-First Launch)
        function initializeAppSession() {
            console.log("🔒 ระบบความปลอดภัยกำลังรีเซ็ตแอปพลิเคชันเพื่อป้องกันการสวมสิทธิ์...");

            // 1. ทำลายเซสชันความปลอดภัยและข้อมูลค้างทั้งหมดในแรมและ localStorage ทันทีเมื่อเปิดแอปใหม่
            localStorage.removeItem("staff_token");
            localStorage.removeItem("staff_name");
            // localStorage.removeItem("staff_org_id"); // ตัวนี้ถ้าอยากให้จำสาขาไว้ ไม่ต้องลบก็ได้ครับ สตาฟฟ์จะได้ไม่ต้องเลือกสาขาใหม่
            localStorage.removeItem("current_purchase_cart"); // ล้างตะกร้าขยะที่อาจจะค้างอยู่ทิ้งให้เกลี้ยง

            // 2. รีเซ็ตตัวแปรอาเรย์ตะกร้าใน JavaScript ให้เป็นศูนย์
            if (typeof purchaseCart !== 'undefined') {
                purchaseCart = [];
            }

            // 3. ปิดพวกกล่องซ่อน หน้าต่างสไลด์คีย์น้ำหนัก และ Sidebar ที่อาจจะโหลดค้างไว้
            if (typeof closeWeightModal === "function") closeWeightModal();
            if (typeof toggleSidebar === "function") toggleSidebar(false);
            if (typeof resetSelection === "function") resetSelection();

            // 4. บังคับเปลี่ยนหน้าจอให้แสดงผลเฉพาะ "หน้า Login" เท่านั้น
            const loginScreen = document.getElementById('loginScreen');
            const mainAppScreen = document.getElementById('mainAppScreen'); // หรือ ID หน้าจัดการขยะหลักของพี่

            // if (loginScreen) {
            loginScreen.style.display = 'block';
            // }
            // if (mainAppScreen) {
            // mainAppScreen.style.display = 'none';
            // }

            console.log("🚨 รีเซ็ตระบบสำเร็จ! บังคับผู้ใช้งานไปเริ่มต้นที่หน้า Login เพื่อความปลอดภัยของข้อมูล");
        }

        //📌 สั่งให้ระบบล้างไพ่ทันทีที่หน้าจอเว็บแอปถูกโหลดขึ้นมา (DOM Loaded)
        document.addEventListener("DOMContentLoaded", () => {
         generateWaterBillHTML2('s')
           printReceipt();
            // initializeAppSession();
        });
    </script>


@endsection