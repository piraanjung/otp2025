@extends('layouts.keptkaya_mobile2')

@section('style')
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
            #card-reciept .amount{
                text-align: right;
                font-size: 1.3rem;
                vertical-align: top
            }
        </style>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
@endsection

@section('content')
    <div id="card-reciept" style="width: 384px; 
            background: #ffffff; 
            color: #000000;
            font-size:1.4rem !important
            ">
        
        

    </div>
    {{-- <canvas id="receiptCanvas" width="384" height="400" style="display:block;border:1px solid"></canvas> --}}
    {{-- <button type="button" id="btnPrintTest"
        style="background: #17a2b8; font-weight: bold; padding: 14px; border-radius: 6px;">
        ⚡ ทดสอบการพิมพ์ (Test Print)
    </button> --}}
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
                        {{-- <img src="{{ " https://profile.line-scdn.net/" . Auth::user()->image }}" class="img-bordered
                        md"
                        alt=""> --}}
                    </center>
                </div>
                <div class="sidebar-user-details">
                    <small>เจ้าหน้าที่ผู้ปฏิบัติงาน</small>
                    <span id="staffName"></span>
                </div>
            </div>

            <div class="sidebar-menu-items">
                <a href="#" class="menu-item active" onclick="toggleSidebar(false,'main')"> หน้าหลักบันทึกขยะ</a>
                <a href="#" class="menu-item" onclick="alert('ระบบตั้งค่าเครื่องชั่งและเครื่องปรินท์บลูทูธ')">⚙️
                    ตั้งค่าอุปกรณ์</a>
                <a href="#" class="menu-item" onclick="alert('เวอร์ชันแอปพลิเคชัน: v1.2.0-Recycle')">ℹ️
                    เกี่ยวกับระบบ</a>
            </div>

            <div class="sidebar-footer">
                <button type="button" id="btnLogout" class="btn-sidebar-logout">
                    🚪 ออกจากระบบ
                </button>
            </div>
        </div>
    </div>

    <div id="loginScreen" class="mobile-login-wrapper">
        <div class="login-brand-area">
            <div class="brand-logo">♻️</div>
            <h1>ธนาคารขยะรีไซเคิล</h1>
            <p>KeptKaya Staff Application</p>
        </div>

        <div class="mobile-login-card">
            <h2>ยินดีต้อนรับ</h2>
            <p class="subtitle">กรุณาเข้าสู่ระบบเพื่อปฏิบัติงาน</p>

            <div id="loginError" class="error-banner" style="display: none;"></div>

            <form id="loginForm" autocomplete="off">
                <div class="floating-group">
                    <input type="text" id="username" placeholder=" " value="katsukipai16@gmail.com" required>
                    <label for="username">👤 ชื่อผู้ใช้งาน / รหัสเจ้าหน้าที่</label>
                    <span class="input-highlight"></span>
                </div>

                <div class="floating-group">
                    <input type="password" id="password" placeholder=" " value="0910642922" required>
                    <label for="password">🔒 รหัสผ่านความปลอดภัย</label>
                    <span class="input-highlight"></span>
                </div>

                <button type="submit" id="btnLogin" class="btn-mobile-login">
                    🔓 เข้าสู่ระบบ
                </button>
            </form>
        </div>


    </div>

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
                    <input type="text" id="searchMemberInput" placeholder="🔍 ค้นหาชื่อ, นามสกุล หรือเบอร์โทร..."
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
                        style="display: none; background: #fff3cd; color: #856404; padding: 15px; border-radius: 8px; justify-content: space-between; align-items: center;">
                        <div>
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
                                    <div
                                        style="display: grid-column; grid-template-columns: 7fr 5fr; gap: 15px; margin-bottom: 15px; align-items: end;">
                                        <div>
                                            <label class="small text-muted mb-1"
                                                style="font-size: 13px; color: #6c757d; display: block; margin-bottom: 5px;">จำนวน</label>
                                            <input type="number" step="0.01" id="amount_in_units" class="form-control"
                                                placeholder="0.00"
                                                style="width: 100%; padding: 12px 5px; box-sizing: border-box; font-size: 28px; font-weight: bold; text-align: center; background: #f8f9fa; border: 1px solid #ced4da; border-radius: 8px; height: 58px;">
                                        </div>

                                        <div>
                                            <label class="small text-muted mb-1"
                                                style="font-size: 15px; color: #6c757d; display: block; margin-bottom: 5px;">หน่วย</label>
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

                {{-- <div style="display: flex; flex-direction: column; gap: 12px;">
                    <button type="button" id="btnConnect"
                        style="background: #4e73df; font-weight: bold; padding: 14px; border-radius: 6px;">
                        🔄 ค้นหา & เชื่อมต่ออุปกรณ์
                    </button>

                    <button type="button" id="btnPrintTest"
                        style="background: #17a2b8; font-weight: bold; padding: 14px; border-radius: 6px;">
                        ⚡ ทดสอบการพิมพ์ (Test Print)
                    </button>
                </div> --}}
                <div class="bottom-action-bar">
                    <button id="connectButton" class="btn btn-custom-secondary col-4">
                        <span>🔄 ค้นหา & เชื่อมต่ออุปกรณ์</span>
                    </button>
                    <button id="printImageButton" onclick="printReceipt()" class="btn btn-custom-primary col-8 shadow-sm">
                        <span class="material-icons-round">print2</span>
                        <span id="printBtnText">พิมพ์ใบเสร็จ2</span>
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

            <div style="text-align: center; margin-top: 15px;">
                <p style="font-size: 13px; color: #6c757d; margin-bottom: 8px;">🖼️
                    ตัวอย่างหน้าตาใบเสร็จพิมพ์สแกนกราฟิก:</p>
                {{-- <canvas id="receiptCanvas" width="384" height="600" style="display:block;border:1px solid"></canvas>
                --}}
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
                    <div class="menu-title">จุดมิเตอร์ประปา</div>
                    <div class="menu-desc">จุดมิเตอร์ประปา</div>
                </div>

                <div class="menu-item card-water" onclick="navigateTo('water-equipment-control')">
                    <div class="menu-icon">💧</div>
                    <div class="menu-title">ควบคุมงานผลิตน้ำ</div>
                    <div class="menu-desc">ควบคุมงานผลิตน้ำ</div>
                </div>


                <div class="menu-item card-settings" onclick="navigateTo('settings-store')">
                    <div class="menu-icon">🖨️</div>
                    <div class="menu-title">ยืม/คืน พัสดุงานประปา</div>
                    <div class="menu-desc">ยืม/คืน พัสดุงานประปา</div>
                </div>
            </div>
        </div>
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

    {{--
    <script src="capacitor.js"></script>
    <script src="cordova.js"></script>
    <script src="js/receipt-printer-encoder.umd.js"></script>
    <script src="app.js"></script> --}}

@endsection

@section('script')
    <script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
    <script src="https://unpkg.com/html5-qrcode"></script>
    <script src="https://code.jquery.com/jquery-4.0.0.slim.js"
        integrity="sha256-M+GjhMBfXikM1izMplICCTscIj5hzPCp6uDzaypxtgg=" crossorigin="anonymous"></script>
    <script>
        // =========================================================================
        //  PIOS Staffs Application - Core Logic (app.js)
        // =========================================================================
        let BluetoothSerial = null;
        let allMembers = [];                 // เก็บรายชื่อสมาชิกทั้งหมดที่ดึงมาจาก Laravel
        const API_BASE_URL = " https://b520-1-46-64-210.ngrok-free.app/api";
        // const API_BASE_URL = " https://qa.envsogo.site/api";

        let rawItemsData = [];               // เก็บรายการขยะทั้งหมด (kp_tbank_items)
        let currentSelectedTrash = null;     // เก็บขยะชิ้นปัจจุบันที่เจ้าหน้าที่เลือกอยู่
        let purchaseCart = [];               // ตะกร้าเก็บของชั่วคราวบนแอปมือถือ 
        let currentSelectedUnitId = null;
        let currentActiveMember = null;      // เก็บข้อมูลสมาชิกที่กำลังทำรายการฝากขยะอยู่
        const sidebar = document.getElementById('appSidebar');
        let staffInfo;
        // -------------------------------------------------------------------------
        //  เริ่มต้นระบบเมื่อหน้าจอ (DOM) โหลดพร้อมใช้งาน
        // -------------------------------------------------------------------------
        document.addEventListener("DOMContentLoaded", () => {
            $('#card-reciept').html(
                `<div class="row">
                <div class="col-5">
                    <img src="{{ asset('logo/chiangkreu_sakonnakhon.png') }}" width="100%" alt="">
                </div>
                <div class="col-7" id="org">
                    <div style="font-size: 1.5rem;">เทศบาลตำบล</div>
                    <div style="font-size: 2rem; margin-top: -10px;">เชียงเครือ</div>

                </div>
                <div class="text-right" id="org_address">
                    <div>109 หมู่ 14 ต.เชียงเครือ</div>
                    <div>อ.เมืองสกลนคร จ.สกลนคร</div>
                    <div>โทร.042-4345433</div>
                </div>
            </div>

            <p>ธนาคารขยะรีไซเคิล</p>
            <div id="member_info">
                <div class="row">
                    <div class="col-3 header">ชื่อ-สกุล:</div>
                    <div class="col-9">สมชาย รักสะอาด</div>
                    <div class="col-3 header">ที่อยู่:</div>
                    <div class="col-9 info">
                        <div>109 หมู่ 14 ต.เชียงเครือ</div>
                        <div>อ.เมืองสกลนคร จ.สกลนคร</div>
                    </div>
                    <div class="col-3 header">รหัส:</div>
                    <div class="col-9 info">001122122</div>
                </div>
            </div>

            <div id="reciept_info" style="margin-bottom:10px">
                <div class="row">
                    <div class="col-4 header">วันที่:</div>
                    <div class="col-8">10/07/2569</div>
                    <div class="col-4 header">เลขใบเสร็จ:</div>
                    <div class="col-8 info">Bill12333353</div>
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
                <tbody>
                    @for ($i = 0; $i < 2; $i++)

                        <tr>

                            <td style="text-align: left; font-size: 1.5rem;line-height: 22px;">
                                -ขวดพลาสติกใส 
                                <div style="font-size: 1rem; padding-left: 10px; ">
                                    <span style="font-size: 1.4rem;">3 กก.   </span>
                                    <span style="margin-left:10px">(5 บาท, 10 แต้ม):กก.</span>
                                </div>
                            </td>
                            <td class="amount">4,444</td>
                            <td class="amount">7,777.77</td>
                        </tr>
                    @endfor
                    
                </tbody>
                
            </table>
            <hr style="color: #000000;opacity:1; margin-left: 20px;" width="90%">
            <table width="100%">
                    <tbody>
                         <tr>

                            <td style="text-align: left; font-size: 1.5rem;font-weight:bold" width="60%">
                                รวมรับเงิน/แต้มสะสม
                            </td>
                            <td width="15%" style="line-height: 22px; font-weight:bold" class="amount">4,444 <div style="font-size: 1rem;text-align: right">แต้ม</div></td>
                            <td width="25%" style="line-height: 22px; font-weight:bold" class="amount">4,444.77 <div style="font-size: 1rem;text-align: right">บาท</div></td>
                        </tr>

                    </tbody>
                </table>
            <hr style="opacity:1;">

            <div style="margin-bottom:3rem; margin-top:rem;text-align:center;font-size:1.4rem; font-weight:bold">
                 ขอบคุณที่ร่วมลดโลกร้อน
            </div>`
            )
            // ดึงองค์ประกอบจากหน้า HTML หลังโหลดครบ
            const loginScreen = document.getElementById('loginScreen');
            const mainScreen = document.getElementById('mainScreen');
            const loginForm = document.getElementById('loginForm');
            const loginError = document.getElementById('loginError');
            const staffNameSpan = document.getElementById('staffName');
            document.getElementById('connectionStatusBadge').innerHTML = 'x';

            // ตรวจสอบสถานะการล็อกอินเดิม (Auto Login)
            const savedToken = localStorage.getItem("staff_token");
            const savedName = localStorage.getItem("staff_name");
            const savedStaffID = localStorage.getItem("staff_id");
            const savedMembers = localStorage.getItem("all_members_data");
            const savedCart = localStorage.getItem("current_purchase_cart");


            if (savedToken && savedName) {
                //     if (savedMembers) {
                //         allMembers = JSON.parse(savedMembers); // ดึงข้อมูลสมาชิกเก่าคืนมา
                //     }
                //     if (savedCart) {
                //         purchaseCart = JSON.parse(savedCart); // ดึงข้อมูลตะกร้าเก่าค้างไว้คืนมา
                //     }
                // showMainScreen(savedName);
                // renderFloatingCart(); // วาดตะกร้าลอยที่มีข้อมูลค้างอยู่ทันที
            }

            // ฟังก์ชันสลับหน้าจอไปหน้าหลักของ Staff
            function showMainScreen(name) {
                loginScreen.classList.add('is-hidden');
                mainScreen.classList.remove('is-hidden');
                if (staffNameSpan) staffNameSpan.innerText = name;
                updateGlobalPrinterStatus();
            }

            // 🔒 ฟังก์ชันเมื่อกดส่งฟอร์ม Login เพื่อต่อ API Laravel
            if (loginForm) {
                loginForm.addEventListener('submit', async (e) => {
                    console.log('loginform()')

                    e.preventDefault();

                    const username = document.getElementById('username').value;
                    const password = document.getElementById('password').value;
                    const btnSubmit = document.getElementById('btnLogin');

                    if (loginError) loginError.style.display = "none";
                    btnSubmit.disabled = true;
                    btnSubmit.innerText = "กำลังตรวจสอบข้อมูล...";

                    try {
                        // 🟢 เคลียร์ข้อมูลขยะค้างเก่าในเครื่องของคนก่อนหน้าก่อนเริ่มล็อกอินใหม่
                        localStorage.removeItem("staff_token");
                        localStorage.removeItem("staff_name");
                        localStorage.removeItem("staff_id");

                        const response = await fetch(`${API_BASE_URL}/users/staff_authen`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'ngrok-skip-browser-warning': 'true'
                            },
                            body: JSON.stringify({
                                username: username,
                                passwords: password,
                            })
                        });

                        // เช็คว่า response ตอบกลับมาสำเร็จหรือไม่ (Status 200-299)
                        console.log('HTTP Status:', response.status);

                        const resData = await response.json(); // 🟢 เพิ่ม await ตรงนี้
                        console.log('resData จากเซิร์ฟเวอร์:', resData);





                        // 📦 โครงโค้ดในท่อน Login ของ app.js หลังดึงข้อมูลสำเร็จ
                        if (resData.code === 200 && resData.data && resData.data.logged === true) {
                            staffInfo = resData.data;
                            const fullName = `${staffInfo.prefix || ''}${staffInfo.firstname} ${staffInfo.lastname}`;

                            localStorage.setItem("staff_token", staffInfo.remember_token);
                            localStorage.setItem("staff_name", fullName);
                            localStorage.setItem("staff_id", staffInfo.id)
                            localStorage.setItem("staff_org_id", staffInfo.org_id_fk || '');

                            showMainScreen(fullName);

                            // 🟢 เติมบรรทัดนี้: ให้แอปวิ่งไปดูดรายชื่อสมาชิกจาก API มาเตรียมทันทีที่เข้าหน้าหลัก
                            loadMembersFromServer();


                        } else {
                            if (loginError) {
                                loginError.innerText = resData.message || "ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง หรือไม่มีสิทธิ์เข้าใช้งาน";
                                loginError.style.display = "block";
                            }
                        }
                    } catch (error) {
                        if (loginError) {
                            loginError.innerText = "เกิดข้อผิดพลาด: ไม่สามารถเชื่อมต่อกับฐานข้อมูลระบบ PIOS ได้";
                            loginError.style.display = "block";
                        }
                        console.error("API Connection Error:", error);
                    } finally {
                        btnSubmit.disabled = false;
                        btnSubmit.innerText = "🔓 เข้าสู่ระบบ";
                    }
                });
            }

            // 🔒 ระบบออกจากระบบ (Logout)
            const btnLogout = document.getElementById('btnLogout');
            if (btnLogout) {
                btnLogout.addEventListener('click', () => {
                    localStorage.clear(); // ล้างข้อมูลทั้งหมดในเครื่องออก
                    allMembers = [];
                    purchaseCart = [];
                    currentActiveMember = null;
                    mainScreen.classList.add('is-hidden');
                    loginScreen.classList.remove('is-hidden');
                    sidebar.classList.remove('is-open');

                    if (loginForm) loginForm.reset();
                    renderFloatingCart();

                    document.location.href = 'acc'
                });
            }
        });

        // -------------------------------------------------------------------------
        //  โมดูลระบบเชื่อมต่อเครื่องพิมพ์บลูทูธ (Bluetooth LE เท่านั้น ไม่ใช้ระบบเก่า)
        // -------------------------------------------------------------------------
        function checkBluetoothStatus() {
            const statusText = document.getElementById('status');
            if (!statusText) return;

            const BleClient = window.Capacitor?.Plugins?.BluetoothLe;
            if (BleClient) {
                statusText.innerText = "💻 โหมดจำลองบน Browser (คอมพิวเตอร์)";
                statusText.style.color = "#858796";
            } else {
                const isConnected = localStorage.getItem('is_printer_connected') === 'true';
                const printerName = localStorage.getItem('connected_printer_name') || 'เครื่องพิมพ์';
                if (isConnected) {
                    statusText.innerText = `🟢 เชื่อมต่ออยู่กับ: ${printerName}`;
                    statusText.style.color = "green";
                } else {
                    // statusText.innerText = "🔴 พร้อมเชื่อมต่อ (เปิดบลูทูธเครื่องพิมพ์ไว้เลย)";
                    statusText.style.color = "#dc3545";
                }
            }
        }

        // 🔄 ระบบสั่งค้นหา & เชื่อมต่ออุปกรณ์ด้วย Capacitor BLE
        const btnConnect = document.getElementById('btnConnect');
        if (btnConnect) {
            btnConnect.addEventListener('click', async () => {
                const statusText = document.getElementById('status');
                const BleClient = window.Capacitor?.Plugins?.BluetoothLe;

                if (!BleClient) {
                    alert("ระบบตรวจไม่พบปลั๊กอิน Capacitor Bluetooth LE (เปิดบนคอมพิวเตอร์จะทดสอบปุ่มนี้ไม่ได้)");
                    return;
                }

                try {
                    if (statusText) statusText.innerText = "กำลังตรวจสอบสิทธิ์บลูทูธ...";

                    // 🟢 เรียกขอเปิดใช้งานบลูทูธและสิทธิ์ค้นหาอุปกรณ์ (แมทช์กับสิทธิ์ Android 12)
                    await BleClient.initialize();

                    if (statusText) statusText.innerText = "กำลังสแกนหาอุปกรณ์บลูทูธรอบตัว...";

                    // 🟢 เปิดหน้าป๊อปอัปให้เจ้าหน้าที่ทำการเลือกจับคู่เครื่องพิมพ์ใบเสร็จ
                    const device = await BleClient.requestDevice();

                    if (!device) {
                        if (statusText) statusText.innerText = "ยกเลิกการเลือกอุปกรณ์";
                        return;
                    }

                    if (statusText) statusText.innerText = `กำลังเชื่อมต่อเข้ากับ: ${device.name || 'Thermal Printer'}...`;

                    // 🟢 เชื่อมต่อสัญญาณโดยตรง
                    await BleClient.connect({ deviceId: device.deviceId });

                    localStorage.setItem('printer_id', device.deviceId);
                    localStorage.setItem('connected_printer_name', device.name || 'Thermal Printer');
                    localStorage.setItem('is_printer_connected', 'true');

                    if (statusText) {
                        statusText.innerText = `🟢 เชื่อมต่อสำเร็จกับ: ${device.name || 'Thermal Printer'}`;
                        statusText.style.color = "green";
                    }
                    updateGlobalPrinterStatus();
                    alert(`✅ เชื่อมต่อกับเครื่องพิมพ์สำเร็จ!`);

                    // วาดใบเสร็จตัวอย่างอัปเดตลง Canvas ทันที
                    drawReceipt();

                } catch (error) {
                    if (statusText) {
                        statusText.innerText = "❌ การเชื่อมต่อล้มเหลว";
                        statusText.style.color = "red";
                    }
                    console.error(error);
                    alert("เกิดข้อผิดพลาดในการเชื่อมต่อ: " + error.message);
                }
            });
        }

        // 🖼️ ฟังก์ชันสำหรับวาดรูปหน้าตาใบเสร็จสลิปจำลองลง Canvas
        function drawReceipt() {
            const canvas = document.getElementById('receiptCanvas');
            if (!canvas) return;
            const ctx = canvas.getContext('2d');

            // 1. สร้าง Image Object สำหรับ Logo
            const logo = new Image();
            logo.src = "{{ asset('logo/chiangkreu_sakonnakhon_gray.png') }}"; // 👈 เปลี่ยนเป็น Path หรือ URL ของรูปโลโก้ของคุณ

            // 2. เมื่อรูปภาพโหลดเสร็จแล้วค่อยเริ่มวาด Canvas
            logo.onload = () => {
                // ล้าง Canvas และลงสีพื้นหลัง
                ctx.fillStyle = '#FFFFFF';
                ctx.fillRect(0, 0, canvas.width, canvas.height);
                ctx.fillStyle = '#000000';

                // ตรวจสอบว่ามีของในตะกร้าไหม ถ้าไม่มีให้ใส่ข้อมูลจำลองเพื่อพรีวิว/ทดสอบพิมพ์
                const itemsToPrint = purchaseCart.length > 0 ? purchaseCart : [
                    { item_name: "ขวดพลาสติกใส (PET)", amount_in_units: 5.5, unit_name: "กก.", amount: 24.75, points: 10, pointsum: 55 },
                    { item_name: "กระดาษลัง", amount_in_units: 10.0, unit_name: "กก.", amount: 75.00, points: 20, pointsum: 200 },
                    { item_name: "ขวดเบียร์ลีโอ", amount_in_units: 10.0, unit_name: "ลัง.", amount: 75.00, points: 20, pointsum: 200 },
                    // { item_name: "กระดาษลัง", amount_in_units: 10.0, unit_name: "กก.", amount: 75.00 },
                ];
                let expandHCanvas = 550 + (40 * itemsToPrint.length)
                $('#receiptCanvas').attr('height', expandHCanvas)

                // ----------------------------------------------------
                // 3. วาดโลโก้ลง Canvas (ปรับตำแหน่ง X, Y และ ขนาด W, H ตามต้องการ)
                // ctx.drawImage(image, x, y, width, height)
                const logoWidth = 170;   // กว้าง 60px
                const logoHeight = 150;  // สูง 60px
                const logoX = 0;//(canvas.width - logoWidth) / 2; // จัดให้อยู่ตรงกลาง (162)
                const logoY = 15;       // วาดเริ่มที่ Y = 15

                ctx.drawImage(logo, logoX, logoY, logoWidth, logoHeight);
                // ----------------------------------------------------
                let xExis = (canvas.width + logoWidth - 40) / 2;
                let yExis = 0;
                ctx.font = 'bold 20px Arial';
                ctx.textAlign = 'right';
                ctx.fillText('เทศบาลตำบล', canvas.width - 20, logoHeight / 4);

                yExis += logoHeight / 2;
                ctx.font = 'bold 28px Arial';
                ctx.textAlign = 'right';
                ctx.fillText('เชียงเครือ', canvas.width - 20, yExis);

                yExis += 20;
                let rage = 20
                ctx.font = '16px Arial';
                ctx.textAlign = 'right';
                ctx.lineWidth = 2;
                ctx.beginPath(); ctx.moveTo(logoWidth - 22, yExis); ctx.lineTo(canvas.width - 20, yExis); ctx.stroke();

                ctx.fillText('109 หมู่ 14 ต.เชียงเครือ', canvas.width - 20, yExis + (rage * 1));
                ctx.fillText('อ.เมืองสกลนคร จ.สกลนคร', canvas.width - 20, yExis + (rage * 2));
                ctx.fillText('โทร.042-4345433', canvas.width - 20, yExis + (rage * 3));

                // ปรับตำแหน่ง Y ของข้อความหัวข้อให้ขยับลงมาต่อจากโลโก้
                ctx.font = 'bold 24px Arial';
                ctx.textAlign = 'center';
                ctx.fillText('ธนาคารขยะรีไซเคิล', canvas.width / 2, yExis + (rage * 5));

                ctx.lineWidth = 2;
                ctx.beginPath(); ctx.moveTo(20, yExis + (rage * 5 + 10));
                ctx.lineTo(canvas.width - 20, yExis + (rage * 5 + 10)); ctx.stroke();

                rage += 3;
                ctx.font = 'bold 18px Arial';
                ctx.textAlign = 'left';
                const memberName = currentActiveMember ? `${currentActiveMember.firstname} ${currentActiveMember.lastname}` : "นายสมชาย ใจดี (ทดสอบ)";
                ctx.fillText(`สมาชิก:`, 20, yExis + (rage * 6));

                ctx.font = ' 18px Arial';
                ctx.textAlign = 'left';
                ctx.fillText(`${memberName}`, 90, yExis + (rage * 6));


                ctx.font = 'bold 18px Arial';
                ctx.textAlign = 'left';
                ctx.fillText('ที่อยู่:', 40, yExis + (rage * 7));

                ctx.font = '16px Arial';
                ctx.textAlign = 'left';
                ctx.fillText(`109 หมู่ 14 ต.เชียงเครือ`, 90, yExis + (rage * 7));
                ctx.textAlign = 'left'

                ctx.fillText(`อ.เมืองสกลนคร จ.สกลนคร 47000`, 90, yExis + (rage * 8));

                ctx.lineWidth = 2;
                ctx.beginPath(); ctx.moveTo(20, yExis + (rage * 9 - 10));
                ctx.lineTo(canvas.width - 20, yExis + (rage * 9 - 10)); ctx.stroke();


                const today = new Date().toLocaleDateString('th-TH');
                ctx.font = 'bold 18px Arial';
                ctx.fillText(`วันที่:`, 20, yExis + (rage * 10));

                ctx.font = '16px Arial';
                ctx.fillText(`${today}`, 70, yExis + (rage * 10));
                ctx.font = 'bold 18px Arial';
                ctx.fillText(`ใบเสร็จเลขที่:`, 20, yExis + (rage * 11));
                ctx.font = '16px Arial';
                ctx.fillText(`0333-333-333`, 130, yExis + (rage * 11));

                let startY = yExis + rage * 13;
                let grandTotal = 0;
                let pointTotal = 0;

                ctx.font = '16px Arial';
                ctx.textAlign = 'left';
                ctx.fillText(`แต้ม`, 250, startY - 20);
                ctx.fillText(`บาท`, 333, startY - 20);


                itemsToPrint.forEach(item => {
                    // 1. วาดชื่อสินค้า (ชิดซ้ายที่ X = 20)
                    ctx.font = '16px Arial';
                    ctx.textAlign = 'left';
                    ctx.fillText(`- ${item.item_name}`, 20, startY);

                    // 2. วาดจำนวน + หน่วย (กำหนดพิกัด X คงที่ เช่น X = 170 และ X = 210)
                    ctx.textAlign = 'right';
                    ctx.font = 'bold 17px Arial';
                    ctx.fillText(`${item.amount_in_units}`, 210, startY);
                    ctx.textAlign = 'left';
                    ctx.font = '15px Arial';
                    ctx.fillText(`${item.unit_name}`, 215, startY);

                    // วาดรายละเอียดราคา/แต้มย่อย (บรรทัดล่าง)
                    ctx.font = '14px Arial';
                    ctx.fillText(`( 5 บาท:กก. / ${item.points} แต้ม:กก.)`, 40, startY + 20);

                    // 3. วาดรวมแต้ม และ รวมเงิน (ชิดขวาตามพิกัดเดิม)
                    ctx.textAlign = 'right';
                    ctx.font = '16px Arial';
                    ctx.fillText(`${item.pointsum}`, 280, startY);
                    ctx.fillText(`${item.amount.toFixed(2)}`, 364, startY);

                    grandTotal += item.amount;
                    pointTotal += item.pointsum;
                    startY += 40;
                });

                ctx.beginPath(); ctx.moveTo(20, startY); ctx.lineTo(364, startY); ctx.stroke();
                startY += 40;

                ctx.font = 'bold 18px Arial';
                ctx.textAlign = 'left';
                ctx.fillText('รวมรับเงิน/แต้ม สะสม:', 20, startY);
                ctx.textAlign = 'right';
                ctx.fillText(`${pointTotal} `, 280, startY);
                ctx.fillText(`${grandTotal.toFixed(2)} `, 364, startY);

                ctx.font = 'bold 14px Arial';
                ctx.textAlign = 'left';
                ctx.fillText(`แต้ม`, 250, startY + 20);
                ctx.fillText(`บาท`, 334, startY + 20);


                startY += 50;
                ctx.font = '14px Arial';
                ctx.textAlign = 'center';
                ctx.fillText('ขอบคุณที่ร่วมลดโลกร้อนกับ อบต.', 192, startY);
                ctx.fillText('--- ใบเสร็จระบบพิมพ์กราฟิก ---', 192, startY + 25);
            };

            // กรณีรูปโหลดไม่ผ่าน สามารถวาดส่วนอื่นต่อได้เพื่อไม่ให้ระบบค้าง
            logo.onerror = () => {
                console.error("ไม่สามารถโหลดภาพ Logo ได้");
            };
        }

        // ⚡ ฟังก์ชันสำหรับส่งข้อมูลรูปกราฟิกออกไปยังหัวพิมพ์ความร้อน (ใช้ร่วมกันทั้งปุ่มเทสและบิลจริง)
        async function sendCanvasToPrinter() {
            const BleClient = window.Capacitor?.Plugins?.BluetoothLe;
            const deviceId = localStorage.getItem('printer_id');

            if (!BleClient || !deviceId) {
                throw new Error("ยังไม่ได้เชื่อมต่อเครื่องพิมพ์บลูทูธ กรุณากดปุ่มเชื่อมต่ออุปกรณ์ก่อนครับ");
            }

            // 🟢 ตรวจสอบตัวแปรที่ดึงมาจากสคริปต์ออฟไลน์ใน index.html
            const EncoderClass = window.ReceiptPrinterEncoder;

            if (!EncoderClass) {
                throw new Error("ระบบหาโมดูลแปลงรูปภาพ (ReceiptPrinterEncoder) ไม่เจอ กรุณาตรวจสอบการใส่แท็ก script ใน index.html");
            }

            const canvas = document.getElementById('receiptCanvas');
            if (!canvas) {
                throw new Error("ไม่พบหน้าจอ Canvas (#receiptCanvas)");
            }
            const ctx = canvas.getContext('2d');

            // 🟢 1. คำนวณหาค่าความสูงใหม่ที่ใกล้เคียงที่สุดและหารด้วย 8 ลงตัวพอดี (Multiple of 8)
            const exactHeight = canvas.height;
            const adjustedHeight = Math.ceil(exactHeight / 8) * 8; // ปัดขึ้นให้หาร 8 ลงตัวเสมอล้างพัง

            let imageData;

            // 🟢 2. เช็คว่าถ้าความสูงเดิมหาร 8 ไม่ลงตัว ให้สร้างแผ่นภาพสำรองที่ปัดเศษแล้ว เพื่อไม่ให้ภาพยืดหรือพัง
            if (exactHeight !== adjustedHeight) {
                // สร้าง Canvas ชั่วคราวขนาดที่ถอดรหัสผ่านชัวร์ 100%
                const tempCanvas = document.createElement('canvas');
                tempCanvas.width = canvas.width;
                tempCanvas.height = adjustedHeight;
                const tempCtx = tempCanvas.getContext('2d');

                // เทสีพื้นหลังเป็นสีขาว (เพื่อไม่ให้ส่วนที่ขยายออกไปกลายเป็นสีดำปื้น)
                tempCtx.fillStyle = '#FFFFFF';
                tempCtx.fillRect(0, 0, tempCanvas.width, tempCanvas.height);

                // วาดใบเสร็จตัวจริงของคุณพี่ทับลงไป
                tempCtx.drawImage(canvas, 0, 0);
                imageData = tempCtx.getImageData(0, 0, tempCanvas.width, tempCanvas.height);
            } else {
                // ถ้าหาร 8 ลงตัวอยู่แล้ว ดึงค่าตรงๆ ไปใช้ได้เลยครับ
                imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
            }

            // 🟢 3. เรียกใช้งาน Object ตัวใหม่ตามปกติ (ส่งภาพที่ปรับขนาดความสูงแมทช์ชิ่งเรียบร้อยแล้ว)
            const encoder = new EncoderClass({
                language: 'esc-pos'
            });

            // แยกสั่งงานทีละคำสั่ง ห้ามเขียนต่อท้ายแบบเดิมครับ
            encoder.image(imageData, canvas.width, imageData.height, 'threshold');
            encoder.newline(4);

            // บรรทัดสุดท้ายให้ดึงรหัสฐานสองออกมาเก็บในตัวแปร binaryCommands
            const binaryCommands = encoder.encode();

            // ค้นหา Service และส่งสัญญาณบลูทูธความเร็วต่ำ
            const result = await BleClient.getServices({ deviceId: deviceId });
            const servicesList = result.services || [];
            let targetServiceUuid = null;
            let targetCharacteristicUuid = null;

            // 🟢 ลูปค้นหาพอร์ตออโต้แบบคัดกรอง (ข้ามพอร์ตระบบ 1800 ที่ทำให้แอปเอ๋อ)
            for (const s of servicesList) {
                if (!s.characteristics) continue;

                const sUUID = s.uuid.toLowerCase();
                if (sUUID.includes("1800") || sUUID.includes("1801") || sUUID.includes("180a")) {
                    continue; // ข้ามไปพอร์ตถัดไป
                }

                for (const c of s.characteristics) {
                    if (c.properties.writeWithoutResponse || c.properties.write) {
                        targetServiceUuid = s.uuid;
                        targetCharacteristicUuid = c.uuid;
                        break;
                    }
                }
                if (targetServiceUuid) break;
            }

            // แผนสำรองสุดท้าย
            if (!targetServiceUuid || !targetCharacteristicUuid) {
                targetServiceUuid = "0000ffe0-0000-1000-8000-00805f9b34fb";
                targetCharacteristicUuid = "0000ffe1-0000-1000-8000-00805f9b34fb";
            }

            console.log("พบพอร์ตจริงของเครื่องพิมพ์คือ Service:", targetServiceUuid, "Char:", targetCharacteristicUuid);

            // 6. ส่งข้อมูลภาพที่หั่นเป็น Chunk ละ 512 บายต์ ออกไปทางบลูทูธความเร็วต่ำ (BLE)
            const CHUNK_SIZE = 64;

            for (let i = 0; i < binaryCommands.length; i += CHUNK_SIZE) {
                const chunk = binaryCommands.slice(i, i + CHUNK_SIZE);

                // แปลงอาร์เรย์ตัวเลขก้อนใหญ่ให้กลายเป็น Hex String
                let hexString = '';
                for (let j = 0; j < chunk.length; j++) {
                    const hex = chunk[j].toString(16).padStart(2, '0');
                    hexString += hex;
                }

                try {
                    await BleClient.writeWithoutResponse({
                        deviceId: deviceId,
                        service: targetServiceUuid,
                        characteristic: targetCharacteristicUuid,
                        value: hexString
                    });
                } catch (writeError) {
                    console.error("จุดที่พังตอนยิงข้อมูล:", writeError);
                    throw new Error("ยิงข้อมูลเข้าหัวพิมพ์ไม่สำเร็จ: " + writeError.message);
                }

                // 🟢 2. ลดเวลาหน่วงเหลือแค่ 5ms เพื่อให้เครื่องพิมพ์ทำงานได้ต่อเนื่องแบบรวดเร็ว
                // (ถ้าพิมพ์แล้วตัวหนังสือขาด ให้พี่ลองขยับเพิ่มเป็น 10 หรือ 15 ดูนะครับ แต่ 5 คือเร็วสะใจสุด)
                await new Promise(resolve => setTimeout(resolve, 2));
            }
        }

        // ⚡ ดักจับเหตุการณ์เมื่อจิ้มปุ่ม "ทดสอบการพิมพ์ (Test Print)"
        const btnPrintTest = document.getElementById('btnPrintTest');
        if (btnPrintTest) {
            btnPrintTest.addEventListener('click', async () => {
                try {
                    await sendCanvasToPrinter();
                    alert("✅ ส่งข้อมูลทดสอบระบบหัวพิมพ์เรียบร้อยแล้ว!");
                } catch (error) {
                    console.error("Test print failed:", error);
                    alert("❌ พิมพ์ทดสอบล้มเหลว: " + error.message + "\nกรุณาตรวจสอบว่าเปิดบลูทูธและต่อเครื่องพิมพ์แล้ว");
                }
            });
        }

        // =========================================================================
        //  โมดูลระบบนำทาง และ ค้นหาสมาชิก (Recycle Module)
        // =========================================================================
        function navigateTo(moduleName) {
            if (moduleName === 'recycle') {
                document.getElementById('mainScreen').classList.add('is-hidden');
                document.getElementById('recycleScreen').classList.remove('is-hidden');
                document.getElementById('searchMemberInput').value = "";
                loadMembersFromServer();
                renderMemberList(allMembers);
            }
            else if (moduleName === 'settings') {
                document.getElementById('mainScreen').classList.add('is-hidden');
                document.getElementById('settingsScreen').classList.remove('is-hidden');
                checkBluetoothStatus();
            }
            else if (moduleName === 'water') {
                document.getElementById('mainScreen').classList.add('is-hidden');
                document.getElementById('waterScreen').classList.remove('is-hidden');
                checkBluetoothStatus();
            }
            // else if (moduleName === 'water-cutmeter') {
            //    document.location.href= 'cutmeter'
            // }
            else if (moduleName === 'main') {
                document.getElementById('mainScreen').classList.remove('is-hidden');
                document.getElementById('waterScreen').classList.add('is-hidden');
                document.getElementById('settingsScreen').classList.add('is-hidden');
                document.getElementById('recycleScreen').classList.add('is-hidden');

                checkBluetoothStatus();
            }

            updateGlobalPrinterStatus();
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
                console.log('member', member.waste_preference.kp_bank_account.account_no)
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
            const searchText = document.getElementById('searchMemberInput').value.toLowerCase();
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
            try {
                console.log("กำลังดึงรายชื่อสมาชิกทั้งหมดจากระบบ Keptkaya..." + API_BASE_URL);

                const response = await fetch(`${API_BASE_URL}/keptkaya/members/${staffInfo.org_id_fk}`, {
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
                    console.log('resData.data', resData.data)
                    console.log(`โหลดข้อมูลสำเร็จ! พบสมาชิกทั้งหมด: ${allMembers.length} คน`);

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
                                document.getElementById('searchMemberInput').value = scannedValue;
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
                                document.getElementById('searchMemberInput').value = decodedText;
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

                    // 🟢 2. สั่งยิง Request ไปที่ URL ของ Server
                    const response = await fetch(`${API_BASE_URL}/keptkaya/kp_items_recycle_info`, {
                        method: "GET", // ขอข้อมูลใช้ GET
                        headers: {
                            "Content-Type": "application/json",
                            // 🚨 สำคัญมากสำหรับ ngrok: ใส่ตั๋วใบนี้เพื่อไม่ให้ ngrok แสดงหน้าเว็บ Browser Warning ไม่งั้นข้อมูลจะไม่เข้าแอปครับ
                            "ngrok-skip-browser-warning": "true"
                        }
                    });
                    console.log('res', response)
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
                const unitNameLabel = activeUnitBtn ? activeUnitBtn.innerText : "กก.";

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
                    unit_name: unitNameLabel,
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
            cartBtn.innerHTML = `🛒<span style="position:absolute; top:-15px; right:-10px; background:#dc3545; color:#000000; font-size:25px; font-weight:bold; padding:4px 8px; border-radius:50%; border:2px solid #fff; min-width:20px; text-align:center;">${purchaseCart.length}</span>`;
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
                                                                        <td style="padding:5px;"><b>${item.item_name}</b><br><small style="color:#888;">${item.price_per_unit.toFixed(2)} บ./หน่วย</small></td>
                                                                        <td style="text-align:right; padding:5px;">${item.amount_in_units.toFixed(2)} ${item.unit_name}</td>
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
                                                                        <button onclick="submitFinalPurchase(1)" style="flex:2; padding:12px; border-radius:8px; border:none; background:#28a745; color:#fff; font-weight:bold; font-size:16px; cursor:pointer;">📝 ยืนยันบันทึก & ปริ้นบิล</button>
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
            if (purchaseCart.length === 0) {
                alert("❌ ไม่มีรายการขยะในตะกร้า");
                return;
            }

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

                    alert(`🎉 บันทึกสำเร็จ! เลขที่บิล: ${resData.data.receipt_no}`);
                    $('#card-reciept').html(
                        `<div class="row">
                <div class="col-5">
                    <img src="{{ asset('logo/chiangkreu_sakonnakhon.png') }}" width="100%" alt="">
                </div>
                <div class="col-7" id="org">
                    <div style="font-size: 1.5rem;">เทศบาลตำบล</div>
                    <div style="font-size: 2rem; margin-top: -10px;">เชียงเครือ</div>

                </div>
                <div class="text-right" id="org_address">
                    <div>109 หมู่ 14 ต.เชียงเครือ</div>
                    <div>อ.เมืองสกลนคร จ.สกลนคร</div>
                    <div>โทร.042-4345433</div>
                </div>
            </div>

            <p>ธนาคารขยะรีไซเคิล</p>
            <div id="member_info">
                <div class="row">
                    <div class="col-3 header">ชื่อ-สกุล:</div>
                    <div class="col-9">สมชาย รักสะอาด</div>
                    <div class="col-3 header">ที่อยู่:</div>
                    <div class="col-9 info">
                        <div>109 หมู่ 14 ต.เชียงเครือ</div>
                        <div>อ.เมืองสกลนคร จ.สกลนคร</div>
                    </div>
                    <div class="col-3 header">รหัส:</div>
                    <div class="col-9 info">001122122</div>
                </div>
            </div>

            <div id="reciept_info" style="margin-bottom:10px">
                <div class="row">
                    <div class="col-4 header">วันที่:</div>
                    <div class="col-8">10/07/2569</div>
                    <div class="col-4 header">เลขใบเสร็จ:</div>
                    <div class="col-8 info">Bill12333353</div>
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
                <tbody>
                    @for ($i = 0; $i < 2; $i++)

                        <tr>

                            <td style="text-align: left; font-size: 1.5rem;line-height: 22px;">
                                -ขวดพลาสติกใส 
                                <div style="font-size: 1rem; padding-left: 10px; ">
                                    <span style="font-size: 1.4rem;">3 กก.   </span>
                                    <span style="margin-left:10px">(5 บาท, 10 แต้ม):กก.</span>
                                </div>
                            </td>
                            <td class="amount">4,444</td>
                            <td class="amount">7,777.77</td>
                        </tr>
                    @endfor
                    
                </tbody>
                
            </table>
            <hr style="color: #000000;opacity:1; margin-left: 20px;" width="90%">
            <table width="100%">
                    <tbody>
                         <tr>

                            <td style="text-align: left; font-size: 1.5rem;font-weight:bold" width="60%">
                                รวมรับเงิน/แต้มสะสม
                            </td>
                            <td width="15%" style="line-height: 22px; font-weight:bold" class="amount">4,444 <div style="font-size: 1rem;text-align: right">แต้ม</div></td>
                            <td width="25%" style="line-height: 22px; font-weight:bold" class="amount">4,444.77 <div style="font-size: 1rem;text-align: right">บาท</div></td>
                        </tr>

                    </tbody>
                </table>
            <hr style="opacity:1;">

            <div style="margin-bottom:3rem; margin-top:rem;text-align:center;font-size:1.4rem; font-weight:bold">
                 ขอบคุณที่ร่วมลดโลกร้อน
            </div>`
                    )
                    // 🟢 จุดสำคัญ: เอาข้อมูลตัวจริงจาก Server (resData.data) ส่งไปสั่งพิมพ์ใบเสร็จบลูทูธ
                    if (typeof printReceipt === "function" && print_bill === 1) {
                        printReceipt(resData.data, purchaseCart);
                    }

                    // เคลียร์ค่า และอัปเดตหน้าจอแอปพลิเคชันกลับไปสถานะเริ่มต้น
                    purchaseCart = [];
                    closePurchaseModal();

                    // เปลี่ยนสถานะของสมาชิกคนนี้ในหน้าหลักเป็นเสร็จสิ้นทันที
                    onDepositSuccess(currentActiveMember.id);

                } else {
                    alert("❌ เซิร์ฟเวอร์ปฏิเสธการบันทึก: " + resData.message);
                }

            } catch (error) {
                console.error("Submit Purchase Error:", error);
                alert("❌ ไม่สามารถเชื่อมต่อฐานข้อมูลได้ กรุณาเช็กอินเทอร์เน็ตหน้างาน");
            }
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

            if (isConnected) {
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

            alert("บันทึกข้อมูลและสั่งพิมพ์ใบเสร็จสำเร็จ!");

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
            initializeAppSession();
        });
    </script>



    <script>
        // --- Configuration Constants ---
        const PRINTER_SERVICE_UUID = '000018f0-0000-1000-8000-00805f9b34fb';
        const PRINTER_CHARACTERISTIC_UUID = '00002af1-0000-1000-8000-00805f9b34fb';
        const LAST_USED_DEVICE_ID_KEY = 'lastUsedBluetoothDeviceId';

        // --- UI Elements ---
        const statusText = document.getElementById('status-text');
        const statusCard = document.getElementById('status-card');
        const connectButton = document.getElementById('connectButton');
        const printButton = document.getElementById('printImageButton');
        const printBtnText = document.getElementById('printBtnText');

        let bluetoothDevice;
        let printCharacteristic;

        // --- Helper: UI Updates ---
        function updateStatus(message, type = 'info') {
            console.log('mes', message)
            statusText.textContent = message;

            // Reset Classes
            statusCard.className = 'status-badge border';
            const icon = statusCard.querySelector('.material-icons-round');

            if (type === 'success') {
                statusCard.classList.add('bg-success', 'bg-opacity-10', 'text-success', 'border-success');
                icon.textContent = 'check_circle';
                icon.classList.replace('text-primary', 'text-success');

                // Update Buttons
                connectButton.classList.add('text-success', 'border-success');
                // connectButton.innerHTML = '<span class="material-icons-round">bluetooth_connected</span><span>เชื่อมต่อแล้ว</span>';
                const isConnected = localStorage.getItem('is_printer_connected') === 'true';
                const printerName = localStorage.getItem('connected_printer_name') || 'เครื่องพิมพ์';

                statusText.innerText = `🟢 เชื่อมต่ออยู่กับ: ${printerName}`;
                statusText.style.color = "green";

                printButton.disabled = false;

            } else if (type === 'error') {
                statusCard.classList.add('bg-danger', 'bg-opacity-10', 'text-danger', 'border-danger');
                icon.textContent = 'error';
                icon.classList.replace('text-primary', 'text-danger');
            } else {
                // Info / Loading

                statusCard.classList.add('bg-light', 'text-secondary');
                icon.textContent = 'info';
                icon.classList.replace('text-danger', 'text-primary');
                icon.classList.replace('text-success', 'text-primary');
            }
        }

        // --- Bluetooth Logic ---
        async function connectToPrinter() {
            updateStatus('กำลังค้นหาเครื่องพิมพ์...', 'info');

            if (!navigator.bluetooth) {
                updateStatus('Browser ไม่รองรับ Bluetooth (ใช้ Chrome Android)', 'error');
                return;
            }

            try {
                let selectedDevice = null;
                const lastDeviceId = localStorage.getItem(LAST_USED_DEVICE_ID_KEY);

                if (lastDeviceId) {
                    try {
                        const devices = await navigator.bluetooth.getDevices();
                        selectedDevice = devices.find(d => d.id === lastDeviceId);
                    } catch (e) { }
                }

                if (!selectedDevice) {
                    selectedDevice = await navigator.bluetooth.requestDevice({
                        filters: [{ services: [PRINTER_SERVICE_UUID] }],
                        optionalServices: []
                    });
                    localStorage.setItem(LAST_USED_DEVICE_ID_KEY, selectedDevice.id);
                }

                bluetoothDevice = selectedDevice;
                bluetoothDevice.addEventListener('gattserverdisconnected', onDisconnected);

                const server = await bluetoothDevice.gatt.connect();
                const service = await server.getPrimaryService(PRINTER_SERVICE_UUID);
                printCharacteristic = await service.getCharacteristic(PRINTER_CHARACTERISTIC_UUID);

                updateStatus(`เชื่อมต่อ ${bluetoothDevice.name} สำเร็จ`, 'success');

            } catch (error) {
                updateStatus(`เชื่อมต่อไม่สำเร็จ: ${error.message}`, 'error');
            }
        }

        function onDisconnected() {
            updateStatus('เครื่องพิมพ์หลุดการเชื่อมต่อ', 'error');
            printButton.disabled = true;
            connectButton.innerHTML = '<span class="material-icons-round">bluetooth</span><span>เชื่อมต่อ</span>';
            connectButton.classList.remove('text-success', 'border-success');
        }

        // --- Printing Logic ---
        function getMonochromeBitmapData(ctx, width, height) {
            const imageData = ctx.getImageData(0, 0, width, height);
            const data = imageData.data;
            const bitmap = new Uint8Array(Math.ceil(width / 8) * height);

            for (let y = 0; y < height; y++) {
                for (let x = 0; x < width; x++) {
                    const i = (y * width + x) * 4;
                    const avg = (data[i] + data[i + 1] + data[i + 2]) / 3;
                    if (avg < 128) {
                        const byteIndex = y * Math.ceil(width / 8) + Math.floor(x / 8);
                        const bitIndex = 7 - (x % 8);
                        bitmap[byteIndex] |= (1 << bitIndex);
                    }
                }
            }
            return bitmap;
        }

        async function printReceipt() {
            if (!printCharacteristic) {
                console.log('!printCharacteristic');
                updateStatus('กรุณาเชื่อมต่อก่อนพิมพ์', 'error');
                return;
            }

            // 🟢 ประกาศตัวแปรดึง DOM Elements ให้ครบถ้วนตรงนี้
            const printButton = document.getElementById('printImageButton');
            const printBtnText = document.getElementById('printBtnText');

            if (!printBtnText || !printButton) {
                console.error('ไม่พบปุ่มพิมพ์หรือข้อความปุ่มใน DOM');
                return;
            }

            const originalText = printBtnText.textContent;
            printButton.disabled = true;
            printBtnText.textContent = 'กำลังส่งข้อมูล...';

            try {
                // 1. HTML to Canvas
                const receiptElement = document.getElementById('card-reciept');
                if (!receiptElement) {
                    throw new Error('ไม่พบ Element #receipt-card ในหน้าเว็บ');
                }

                const canvas = await html2canvas(receiptElement, {
                    scale: 2,
                    useCORS: true,
                    backgroundColor: '#ffffff'
                });

                // 2. Resize
                const printerWidth = 384; // 58mm thermal printer (384 dots)
                const scaleFactor = printerWidth / canvas.width;
                const printerHeight = Math.floor(canvas.height * scaleFactor);

                const printCanvas = document.createElement('canvas');
                printCanvas.width = printerWidth;
                printCanvas.height = printerHeight;
                const ctx = printCanvas.getContext('2d');

                // เทสีขาวป้องกันภาพพื้นหลังโปร่งใสกลายเป็นสีดำ
                ctx.fillStyle = '#ffffff';
                ctx.fillRect(0, 0, printerWidth, printerHeight);
                ctx.drawImage(canvas, 0, 0, printerWidth, printerHeight);

                // 3. Bitmap & Command (GS v 0)
                const bitmapData = getMonochromeBitmapData(ctx, printerWidth, printerHeight);
                const bytesPerRow = Math.ceil(printerWidth / 8);

                const command = new Uint8Array([
                    0x1D, 0x76, 0x30, 0x00,
                    bytesPerRow & 0xFF, (bytesPerRow >> 8) & 0xFF,
                    printerHeight & 0xFF, (printerHeight >> 8) & 0xFF
                ]);

                const dataToSend = new Uint8Array(command.length + bitmapData.length);
                dataToSend.set(command, 0);
                dataToSend.set(bitmapData, command.length);

                // 4. Send Chunks
                const CHUNK_SIZE = 194;
                for (let i = 0; i < dataToSend.length; i += CHUNK_SIZE) {
                    const chunk = dataToSend.slice(i, i + CHUNK_SIZE);
                    await printCharacteristic.writeValueWithoutResponse(chunk);
                    await new Promise(r => setTimeout(r, 20));
                }

                // Feed Lines (0x0A = Line Feed)
                await printCharacteristic.writeValueWithoutResponse(new Uint8Array([0x0A, 0x0A, 0x0A]));
                updateStatus('พิมพ์เสร็จสิ้น', 'success');

            } catch (error) {
                // ป้องกันกรณี error.message ไม่มีค่า
                const errorMsg = error.message || error;
                updateStatus(`Error: ${errorMsg}`, 'error');
            } finally {
                printButton.disabled = false;
                printBtnText.textContent = originalText;
            }
        }
        // Initialize
        connectButton.addEventListener('click', connectToPrinter);
    </script>
@endsection