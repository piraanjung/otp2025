<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Envsogo Waste Bank</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>

    <style>
        /* --- GLOBAL RESET & VARIABLES --- */
        :root {
            --hue: 184;
            --bg: hsl(var(--hue), 10%, 90%);
            --fg: hsl(var(--hue), 66%, 24%);
            --primary: hsl(var(--hue), 66%, 44%);
            --gradient: linear-gradient(145deg, hsl(var(--hue), 10%, 85%), hsl(var(--hue), 10%, 100%));
            font-size: 16px;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            /* ลบ border: 0 ออกเพื่อให้ input/modal ทำงานปกติ */
        }

        body {
            background: var(--bg);
            color: var(--fg);
            font-family: "Nunito", sans-serif;
            min-height: 100vh;
            /* แก้จาก height: 100vh เพื่อให้ scroll ได้ */
            display: block;
            /* เอา grid/place-items ออกเพื่อให้ layout มือถือปกติ */
            padding-bottom: 2em;
            /* เผื่อที่ด้านล่าง */
            overflow-x: hidden;
            -webkit-tap-highlight-color: transparent;
        }

        a {
            text-decoration: none;
            color: var(--fg);
        }

        button {
            font-family: inherit;
            cursor: pointer;
        }

        .hidden {
            display: none !important;
        }

        /* --- APP CONTAINER --- */
        .app {
            background: hsl(var(--hue), 10%, 85%);
            border-radius: 0 0 2em 2em;
            /* โค้งแค่ด้านล่าง */
            padding: 1.5em;
            margin-bottom: 1.5em;
            min-height: 100vh;
        }

        /* --- SIDEBAR & NAVIGATION --- */
        /* ปุ่มเปิดเมนู */
        .menu-trigger-btn {
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1030;
            /* อยู่เหนือ Content แต่อยู่ใต้ Modal */
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: #fff;
            border: none;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: var(--primary);
            transition: transform 0.2s;
        }

        .menu-trigger-btn:active {
            transform: scale(0.9);
        }

        /* ฉากหลัง Sidebar */
        .menu-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1040;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            backdrop-filter: blur(2px);
        }

        .menu-backdrop.active {
            opacity: 1;
            visibility: visible;
        }

        /* ตัว Sidebar */
        .modern-sidebar {
            position: fixed;
            top: 0;
            left: -280px;
            width: 280px;
            max-width: 85vw;
            /* กันไม่ให้เกินจอมือถือเล็ก */
            height: 100%;
            background: #ffffff;
            z-index: 1045;
            /* อยู่เหนือ Backdrop แต่อยู่ใต้ Modal (Bootstrap Modal คือ 1055) */
            transition: left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 4px 0 15px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
        }

        .modern-sidebar.active {
            left: 0;
        }

        .sidebar-header {
            padding: 30px 20px;
            background: linear-gradient(135deg, var(--primary), #96c93d);
            color: white;
            position: relative;
        }

        .sidebar-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            border: 3px solid rgba(255, 255, 255, 0.8);
            margin-bottom: 10px;
            object-fit: cover;
        }

        .close-sidebar-btn {
            position: absolute;
            top: 10px;
            right: 15px;
            background: none;
            border: none;
            color: white;
            font-size: 2rem;
            line-height: 1;
            opacity: 0.8;
        }

        .sidebar-content {
            flex: 1;
            overflow-y: auto;
            padding: 20px 0;
        }

        .sidebar-divider {
            padding: 15px 20px 5px;
            font-size: 0.8rem;
            color: #888;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: bold;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: #444;
            font-size: 1rem;
            transition: background 0.2s;
            border-left: 4px solid transparent;
        }

        .sidebar-link i {
            font-size: 1.2rem;
            margin-right: 15px;
            color: var(--primary);
            width: 25px;
            text-align: center;
        }

        .sidebar-link:hover,
        .sidebar-link.active {
            background-color: #f0fdfc;
            color: var(--primary);
            font-weight: bold;
            border-left-color: var(--primary);
        }

        .sidebar-footer {
            padding: 20px;
            border-top: 1px solid #eee;
        }

        .logout-btn {
            display: block;
            text-align: center;
            padding: 10px;
            border-radius: 8px;
            background-color: #ffebee;
            color: #d32f2f;
            font-weight: 600;
        }

        /* --- DASHBOARD ELEMENTS --- */
        .header {
            display: flex;
            justify-content: flex-end;
            /* ชิดขวา */
            margin-bottom: 1.5em;
            margin-top: 1em;
            /* หลบปุ่มเมนู */
        }

        .header__profile-btn {
            background: transparent;
            border: none;
            padding: 0;
            margin-right: 10px;
        }

        .header__profile-icon {
            border-radius: 12px;
            object-fit: cover;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .header__info {
            display: flex;
            flex-direction: column;
            justify-content: center;
            text-align: right;
        }

        .main__date-nav {
            margin-bottom: 2em;
        }

        .main__stat-blocks {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            grid-gap: 1.5em;
            margin-bottom: 1.5em;
        }

        .main__stat-block {
            background: var(--gradient);
            border-radius: 1.5em;
            box-shadow: -0.5em -0.5em 1.5em hsl(0, 0%, 100%), 0.5em 0.5em 1.5em hsl(var(--hue), 5%, 80%);
            padding: 1em;
            text-align: center;
            width: 100%;
            transition: transform 0.2s;
            cursor: pointer;
            position: relative;
            /* สำหรับจัด Layout ภายใน */
        }

        .main__stat-block:active {
            transform: scale(0.98);
        }

        .main__stat-block--lg {
            grid-column: 1 / -1;
            /* เต็มความกว้าง */
            padding: 1.5em;
        }

        .main__stat-graph {
            position: relative;
            width: 100%;
            height: auto;
            aspect-ratio: 1/1;
            /* ให้เป็นสี่เหลี่ยมจัตุรัส */
            max-width: 120px;
            margin: 0 auto 0.5em;
        }

        .main__stat-block--lg .main__stat-graph {
            max-width: 180px;
        }

        .main__stat-detail {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            position: absolute;
            inset: 0;
            pointer-events: none;
        }

        .main__stat-value {
            font-size: 1.25em;
            line-height: 1.2;
            font-weight: 700;
        }

        .main__stat-block--lg .main__stat-value {
            font-size: 2em;
        }

        .main__stat-unit {
            font-weight: 300;
            font-size: 0.8em;
            color: hsl(var(--hue), 10%, 40%);
        }

        /* SVG Rings */
        .ring {
            width: 100%;
            height: 100%;
        }

        .icon {
            position: absolute;
            top: 30%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 30%;
            height: 30%;
        }

        /* --- DARK MODE SUPPORT --- */
        @media (prefers-color-scheme: dark) {
            :root {
                --bg: hsl(var(--hue), 10%, 10%);
                --fg: hsl(var(--hue), 66%, 94%);
                --gradient: linear-gradient(145deg, hsl(var(--hue), 10%, 15%), hsl(var(--hue), 10%, 30%));
            }

            .app {
                background: hsl(var(--hue), 10%, 20%);
            }

            .modern-sidebar {
                background: #2d2d2d;
                color: #fff;
            }

            .sidebar-link {
                color: #ccc;
            }

            .sidebar-link:hover,
            .sidebar-link.active {
                background-color: #3d3d3d;
                color: var(--primary);
            }

            .menu-trigger-btn {
                background: #333;
                color: #fff;
            }

            .main__stat-block {
                box-shadow: -0.5em -0.5em 1.5em hsl(var(--hue), 10%, 30%), 0.5em 0.5em 1.5em hsl(var(--hue), 5%, 5%);
            }
        }

        /* --- ANIMATION KEYFRAMES (เพิ่มส่วนนี้เพื่อให้วงกลมวิ่ง) --- */

        /* 1. สร้างการเคลื่อนไหว (จากว่างเปล่า -> ไปยังค่าที่กำหนด) */
        @keyframes fill-ring-lg {
            from {
                stroke-dashoffset: 515;
            }

            /* 515 คือเส้นรอบวงของวงใหญ่ */
        }

        @keyframes fill-ring-sm {
            from {
                stroke-dashoffset: 163;
            }

            /* 163 คือเส้นรอบวงของวงเล็ก */
        }

        /* 2. สั่งให้วงกลมเริ่มวิ่งเมื่อโหลดหน้า */
        .main__stat-graph--filled .ring-stroke {
            /* วงใหญ่: วิ่ง 1.5 วินาที */
            animation: fill-ring-lg 1.5s ease-out forwards;
        }

        .main__stat-graph:not(.main__stat-graph--filled) .ring-stroke {
            /* วงเล็ก (ถ้ามี): วิ่ง 1 วินาที */
            animation: fill-ring-sm 1s ease-out forwards;
        }

        /* 3. เอฟเฟกต์ Hover ให้เด้งนิดหน่อย */
        .main__stat-block:hover .icon {
            transform: translate(-50%, -50%) scale(1.1);
            transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        /* ทำให้เส้นกราฟมีความโค้งมนที่ปลายเส้น */
        .ring-stroke {
            stroke-linecap: round;
            /* เพิ่มเงาเรืองแสงให้กราฟ */
            filter: drop-shadow(0px 0px 4px rgba(55, 226, 213, 0.5));
            transition: all 1s ease-out;
        }

        /* พื้นหลังรางวงกลม (สีเทาจางๆ) */
        .ring-track {
            stroke: #f0f0f0;
            /* ปรับสีเทาให้อ่อนลงจะได้ดูสะอาดขึ้น */
        }

        /* Animation การวิ่งของเส้น (ที่ให้ไปรอบที่แล้ว) */
        @keyframes fill-ring-lg {
            from {
                stroke-dashoffset: 515;
            }
        }

        @keyframes fill-ring-sm {
            from {
                stroke-dashoffset: 163;
            }
        }

        .main__stat-graph--filled .ring-stroke {
            animation: fill-ring-lg 1.5s ease-out forwards;
        }
    </style>
</head>

<body id="body">
    <svg style="position: absolute; width: 0; height: 0; overflow: hidden;" aria-hidden="true">
        <defs>
            <linearGradient id="ring" x1="0%" y1="0%" x2="100%" y2="0%">
                <stop offset="0%" stop-color="#37e2d5" />
                <stop offset="100%" stop-color="#22a6b3" />
            </linearGradient>

            <linearGradient id="green-ring" x1="0%" y1="0%" x2="100%" y2="0%">
                <stop offset="0%" stop-color="#a8ff78" />
                <stop offset="100%" stop-color="#78ffd6" />
            </linearGradient>
        </defs>
    </svg>
    <button class="menu-trigger-btn" id="openMenuBtn">
        <i class="bi bi-list"></i>
    </button>

    <div class="menu-backdrop" id="menuBackdrop"></div>

    <div class="modern-sidebar" id="mainSidebar">
        <div class="sidebar-header">
            <img src="https://profile.line-scdn.net/{{$userWastePref->user->image ?? ''}}"
                onerror="this.src='https://via.placeholder.com/60'" alt="Profile" class="sidebar-avatar">
            <div class="sidebar-user-info">
                <h5 class="mb-0">{{$userWastePref->user->firstname ?? 'Guest'}}</h5>
                <small>ยินดีต้อนรับ</small>
            </div>
            <button class="close-sidebar-btn" id="closeMenuBtn">&times;</button>
        </div>

        <div class="sidebar-content">
            <a href="#" class="sidebar-link active main_bottom_nav" data-id="recycle">
                <i class="bi bi-house-door-fill"></i> หน้าหลัก (รีไซเคิล)
            </a>

            <div class="sidebar-divider">บริการหลัก</div>

            <a href="#" class="sidebar-link main_bottom_nav" data-id="recycle">
                <i class="bi bi-recycle"></i> ขยะรีไซเคิล
            </a>
            <a href="#" class="sidebar-link main_bottom_nav" data-id="wet">
                <i class="bi bi-trash-fill"></i> ขยะเปียก
            </a>
            <a href="#" class="sidebar-link main_bottom_nav" data-id="tabwater">
                <i class="bi bi-droplet-fill"></i> งานประปา
            </a>

            <div class="sidebar-divider">อื่นๆ</div>
            <a href="#" class="sidebar-link">
                <i class="bi bi-shop"></i> ตลาดชุมชน
            </a>
        </div>

        <div class="sidebar-footer">
            <a href="#" class="logout-btn">
                <i class="bi bi-box-arrow-right"></i> ออกจากระบบ
            </a>
        </div>
    </div>

    <div class="app">
        <svg class="app__gradients" style="position: absolute; width: 0; height: 0;">
            <defs>
                <linearGradient id="ring" x1="1" y1="0" x2="0" y2="1">
                    <stop offset="0%" stop-color="hsl(184,66%,54%)" />
                    <stop offset="100%" stop-color="hsl(184,66%,34%)" />
                </linearGradient>
            </defs>
        </svg>

        <header class="header">
            <button class="header__profile-btn" type="button">
                <img class="header__profile-icon"
                    src="https://profile.line-scdn.net/{{$userWastePref->user->image ?? ''}}"
                    onerror="this.src='https://via.placeholder.com/78'" width="60" height="60">
            </button>
            <div class="header__info">
                <div style="font-size: 1.4em; font-weight: bold;">{{$userWastePref->user->firstname ?? 'User'}}</div>
                <div style="font-size: 1.2em">{{$userWastePref->user->lastname ?? ''}}</div>
            </div>
        </header>

        <main>
            <div class="main__date-nav">
                <div class="main__date d-flex align-items-center justify-content-center">
                    <img src="{{asset('logo/ko_envsogo.png')}}" alt="Logo" style="width: 80px; height: auto;">
                    <strong class="ms-3">
                        <span style="font-size:1.5rem;">Envsogo</span>
                    </strong>
                </div>
            </div>

            <div class="kp div_recycle">
                <h3 class="mb-3 text-center"><i class="bi bi-bank"></i> ธนาคารขยะรีไซเคิล</h3>

                <div class="main__stat-blocks">
                    <div class="main__stat-block main__stat-block--lg">
                        <div class="main__stat-graph main__stat-graph--filled">
                            <svg class="ring" viewBox="0 0 180 180">
                                <circle class="ring-track" cx="90" cy="90" r="82" fill="none" stroke-width="12" />

                                <circle class="ring-stroke" cx="90" cy="90" r="82" fill="none" stroke="url(#ring)"
                                    stroke-width="12" stroke-dasharray="515" stroke-dashoffset="100"
                                    transform="rotate(-90,90,90)" />
                            </svg>
                            <div class="main__stat-detail">
                                <strong
                                    class="main__stat-value">{{ $userWastePref->purchaseTransactions[0]->total_amount ?? '0.00' }}</strong>
                                <span class="main__stat-unit">บาท (คงเหลือ)</span>
                                <div class="my-1"></div>
                                <strong
                                    class="main__stat-value">{{ $userWastePref->purchaseTransactions[0]->total_points ?? '0.00' }}</strong>
                                <span class="main__stat-unit">แต้มสะสม</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="main__stat-blocks">
                    <div class="main__stat-block" data-bs-toggle="modal" data-bs-target="#qrcodeModal">
                        <div class="main__stat-graph">
                            <svg class="ring" viewBox="0 0 60 60">
                                <circle class="ring-track" cx="30" cy="30" r="26" fill="none" stroke="#e0e0e0"
                                    stroke-width="6" />
                            </svg>
                            <i class="bi bi-qr-code icon" style="font-size: 1.5rem;"></i>
                        </div>
                        <div class="main__stat-detail">
                            <strong class="main__stat-value" style="font-size: 1em;">QR Code</strong>
                        </div>
                    </div>

                    <a href="{{url('keptkayas/kiosk/noscreen/login')}}" class="main__stat-block">
                        <div class="main__stat-graph">
                            <svg class="ring" viewBox="0 0 60 60">
                                <circle class="ring-track" cx="30" cy="30" r="26" fill="none" stroke="#e0e0e0"
                                    stroke-width="6" />
                            </svg>
                            <i class="bi bi-camera icon" style="font-size: 1.5rem;"></i>
                        </div>
                        <div class="main__stat-detail">
                            <strong class="main__stat-value" style="font-size: 1em;">ขายด้วยกล้อง</strong>
                        </div>
                    </a>

                    <a href="{{route('keptkayas.shop.index')}}" class="main__stat-block">
                        <div class="main__stat-graph">
                            <svg class="ring" viewBox="0 0 60 60">
                                <circle class="ring-track" cx="30" cy="30" r="26" fill="none" stroke="#e0e0e0"
                                    stroke-width="6" />
                            </svg>
                            <i class="bi bi-cart icon" style="font-size: 1.5rem;"></i>
                        </div>
                        <div class="main__stat-detail">
                            <strong class="main__stat-value" style="font-size: 1em;">ร้านค้า</strong>
                        </div>
                    </a>

                    <a href="{{ route('keptkayas.recycle_classify') }}" class="main__stat-block">
                        <div class="main__stat-graph">
                            <svg class="ring" viewBox="0 0 60 60">
                                <circle class="ring-track" cx="30" cy="30" r="26" fill="none" stroke="#e0e0e0"
                                    stroke-width="6" />
                            </svg>
                            <i class="bi bi-tags icon" style="font-size: 1.5rem;"></i>
                        </div>
                        <div class="main__stat-detail">
                            <strong class="main__stat-value" style="font-size: 1em;">ราคา/คัดแยก</strong>
                        </div>
                    </a>
                </div>
            </div>

            <div class="kp div_wet hidden">
                <h3 class="mb-3 text-center"><i class="bi bi-trash"></i> ธนาคารขยะเปียก</h3>

                <div class="main__stat-blocks">
                    <div class="main__stat-block main__stat-block--lg">
                        <div class="main__stat-graph">
                            <svg class="ring" viewBox="0 0 180 180">
                                <circle class="ring-track" cx="90" cy="90" r="82" fill="none" stroke="#e0e0e0"
                                    stroke-width="12" />
                                <circle class="ring-stroke" cx="90" cy="90" r="82" fill="none" stroke="hsl(3, 90%, 55%)"
                                    stroke-width="12" stroke-dasharray="515" stroke-dashoffset="200"
                                    transform="rotate(-90,90,90)" />
                            </svg>
                            <div class="main__stat-detail">
                                <strong
                                    class="main__stat-value">{{ $userWastePref->purchase_transactions[0]->total_amounts ?? '0.00' }}</strong>
                                <span class="main__stat-unit">Kg (ปี 2568)</span>
                                <div class="my-1"></div>
                                <strong
                                    class="main__stat-value">{{ $userWastePref->purchase_transactions[0]->total_points ?? '0.00' }}</strong>
                                <span class="main__stat-unit">แต้มสะสม</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="main__stat-blocks">
                    <div class="main__stat-block" data-bs-toggle="modal" data-bs-target="#qrcodeModal">
                        <div class="main__stat-graph">
                            <svg class="ring" viewBox="0 0 60 60">
                                <circle class="ring-track" cx="30" cy="30" r="26" fill="none" stroke="#e0e0e0"
                                    stroke-width="6" />
                            </svg>
                            <i class="bi bi-exclamation-triangle icon" style="font-size: 1.5rem; color: orange;"></i>
                        </div>
                        <div class="main__stat-detail">
                            <strong class="main__stat-value" style="font-size: 0.9em;">แจ้งปัญหาถังหมัก</strong>
                        </div>
                    </div>

                    <a href="{{route('keptkayas.shop.index')}}" class="main__stat-block">
                        <div class="main__stat-graph">
                            <svg class="ring" viewBox="0 0 60 60">
                                <circle class="ring-track" cx="30" cy="30" r="26" fill="none" stroke="#e0e0e0"
                                    stroke-width="6" />
                            </svg>
                            <i class="bi bi-book icon" style="font-size: 1.5rem;"></i>
                        </div>
                        <div class="main__stat-detail">
                            <strong class="main__stat-value" style="font-size: 0.9em;">วิธีจัดการ</strong>
                        </div>
                    </a>
                </div>
            </div>

            <div class="kp div_tabwater hidden">
                @if(View::exists('lineliff._tabwater'))
                    @include('lineliff/_tabwater')
                @else
                    <div class="alert alert-warning m-3 text-center">กำลังปรับปรุงระบบประปา</div>
                @endif
            </div>

        </main>
    </div>

    <div class="modal fade" id="qrcodeModal" tabindex="-1" aria-labelledby="qrcodeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="qrcodeModalLabel">QR Code สมาชิก</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <div class="mb-3">
                        {!! $qrcode ?? 'QR Code Error' !!}
                    </div>
                    <p class="text-muted">
                        ID: {{ $userWastePref->id ?? '-' }} - {{ $userWastePref->user_id ?? '-' }}
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary w-100" data-bs-dismiss="modal">ปิด</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>

    <script>
        $(document).ready(function () {
            // --- 1. Sidebar Logic ---
            const $sidebar = $('#mainSidebar');
            const $backdrop = $('#menuBackdrop');
            const $body = $('body');

            function openMenu() {
                $sidebar.addClass('active');
                $backdrop.addClass('active');
                $body.css('overflow', 'hidden'); // ล็อค Scroll
            }

            function closeMenu() {
                $sidebar.removeClass('active');
                $backdrop.removeClass('active');
                $body.css('overflow', ''); // ปลดล็อค Scroll
            }

            $('#openMenuBtn').click(openMenu);
            $('#closeMenuBtn, #menuBackdrop').click(closeMenu);

            // --- 2. Navigation Logic ---
            $('.main_bottom_nav').click(function (e) {
                e.preventDefault();

                // 2.1 Active State
                $('.sidebar-link').removeClass('active');
                $(this).addClass('active');

                // 2.2 Get ID Target
                let div_id = $(this).data('id');

                // 2.3 Close Menu first
                closeMenu();

                // 2.4 Change Content (Wait 300ms for sidebar animation)
                setTimeout(() => {
                    // Hide all sections
                    $('.kp').addClass('hidden');

                    // Show target section with simple fade
                    $('.div_' + div_id).removeClass('hidden').hide().fadeIn(300);

                    // Scroll to top
                    window.scrollTo(0, 0);
                }, 300);
            });
        });
    </script>
</body>

</html>
