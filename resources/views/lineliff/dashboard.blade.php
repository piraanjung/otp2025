<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PI-OS</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

        /* เพิ่มใน <style> */
        .text-danger.main__stat-value {
            animation: shake 0.5s cubic-bezier(.36, .07, .19, .97) both;
            color: #d32f2f !important;
            text-shadow: 0 0 10px rgba(211, 47, 47, 0.2);
        }

        @keyframes shake {

            10%,
            90% {
                transform: translate3d(-1px, 0, 0);
            }

            20%,
            80% {
                transform: translate3d(2px, 0, 0);
            }

            30%,
            50%,
            70% {
                transform: translate3d(-4px, 0, 0);
            }

            40%,
            60% {
                transform: translate3d(4px, 0, 0);
            }
        }
    </style>
</head>

<body id="body">
    {{-- {{ dd( $userWastePref->kp_account) }} --}}
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
            <img src="https://profile.line-scdn.net/{{$user->image ?? ''}}" ...>
            <h5 class="mb-0">{{$user->firstname ?? 'Guest'}}</h5>
            <small>ยินดีต้อนรับ</small>
        </div>
        <button class="close-sidebar-btn" id="closeMenuBtn">&times;</button>
    </div>

    <div class="sidebar-content">
        <a href="#" class="sidebar-link  main_bottom_nav" data-id="recycle">
            <i class="bi bi-house-door-fill"></i> หน้าหลัก
        </a>

        <div class="sidebar-divider">บริการของฉัน</div>
        <a href="#" class="sidebar-link active main_bottom_nav" data-id="recycle">
            <i class="bi bi-recycle"></i> ธนาคารขยะรีไซเคิล
        </a>
        <a href="#" class="sidebar-link main_bottom_nav" data-id="wet">
            <i class="bi bi-trash-fill"></i> ธนาคารขยะเปียก
        </a>
        <a href="#" class="sidebar-link main_bottom_nav" data-id="annual">
            <i class="bi bi-calendar-check"></i> ค่าขยะรายปี
        </a>

        {{-- <div class="sidebar-divider">อื่นๆ</div>
        <a href="#" class="sidebar-link">
            <i class="bi bi-shop"></i> ตลาดชุมชน
        </a> --}}
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
            {{-- <div class="main__date-nav">
                <div class="main__date d-flex align-items-center justify-content-center">
                    <img src="{{asset('logo/ko_envsogo.png')}}" alt="Logo" style="width: 80px; height: auto;">
                    <strong class="ms-3">
                        <span style="font-size:1.5rem;">PI-OS</span>
                    </strong>
                </div>
            </div> --}}

            <div class="kp div_annual hidden">
                @php
                    // เปลี่ยนมาดึงจาก user_id ตรงๆ
                    $transactions = App\Models\RecycleTransaction::where('user_id', $user->id)
                        ->latest()->take(10)->get();
                @endphp
                <h3 class="mb-3 text-center"><i class="bi bi-calendar-check"></i> ค่าธรรมเนียมขยะรายปี</h3>

                <div class="card shadow-sm border-0 mb-4" style="border-radius: 1.5rem;">
                    <div class="card-body text-center p-4">
                        <div class="mb-3">
                            @if($annualTrash && $annualTrash->billing_status == 'waived')
                                <div class="display-1 text-success"><i class="bi bi-check-circle-fill"></i></div>
                                <h4 class="fw-bold text-success">ท่านได้รับสิทธิ์ "ยกเว้น" ค่าธรรมเนียม</h4>
                                <p class="text-muted">ขอบคุณที่ช่วยคัดแยกขยะรีไซเคิลอย่างต่อเนื่อง</p>
                                <span class="badge bg-success rounded-pill">สถานะ: ฟรี (สวัสดิการชุมชน)</span>
                            @else
                                <div class="display-1 text-warning"><i class="bi bi-exclamation-circle-fill"></i></div>
                                <h4 class="fw-bold">สถานะ: รอการชำระ</h4>
                                <p class="text-muted">ยอดค้างชำระปัจจุบัน:
                                    ฿{{ number_format($annualTrash->current_debt ?? 0, 2) }}</p>
                                <button class="btn btn-primary rounded-pill w-100">ชำระเงินออนไลน์</button>
                            @endif
                        </div>
                        <hr>
                        <small class="text-muted">อัปเดตล่าสุดเมื่อ:
                            {{ optional($annualTrash->last_checked_at)->format('d/m/Y') ?? 'กำลังตรวจสอบ' }}</small>
                    </div>
                </div>
            </div>

            <div class="kp div_recycle hidden">
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
                                <strong class="main__stat-value">
                                    {{ number_format($recycleAcc->balance ?? 0, 2) }}
                                </strong>
                                <span class="main__stat-unit">บาท (คงเหลือ)</span>

                                <div class="my-1"></div>

                                <strong class="main__stat-value">
                                    {{ number_format($recycleAcc->points ?? 0) }}
                                </strong>
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

                    {{-- <a href="{{url('keptkayas/kiosk/noscreen/login')}}" class="main__stat-block">
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
                    </a> --}}
                    <div class="main__stat-block" onclick="startScanKiosk()">
                        <div class="main__stat-graph">
                            <svg class="ring" viewBox="0 0 60 60">
                                <circle class="ring-track" cx="30" cy="30" r="26" fill="none" stroke="#e0e0e0"
                                    stroke-width="6" />
                            </svg>
                            <i class="bi bi-camera icon" style="font-size: 1.5rem;"></i>
                        </div>
                        <div class="main__stat-detail">
                            <strong class="main__stat-value" style="font-size: 1em;">สแกนตู้ Kiosk</strong>
                        </div>
                    </div>

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

            <div class="kp div_wet">
                <h3 class="mb-3 text-center"><i class="bi bi-trash"></i> ธนาคารขยะเปียก</h3>

                @if(isset($activeBatch) && $activeBatch)
                    <div class="card shadow-sm mb-4 border-0"
                        style="background: linear-gradient(135deg, #11998e, #38ef7d); color: white; border-radius: 1.5em;">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge bg-white text-success rounded-pill px-3 small">ล็อต:
                                    {{ $activeBatch->batch_code }}</span>
                                <small style="font-size: 0.7rem;"><i class="bi bi-calendar3"></i> เริ่ม:
                                    {{ $activeBatch->start_date->format('d M y') }}</small>
                            </div>
                            <div class="row text-center mt-2">
                                <div class="col-4 border-end border-white-50">
                                    <h4 class="fw-bold mb-0">{{ $activeBatch->days_passed }}</h4>
                                    <small style="font-size: 0.6rem;">วัน</small>
                                </div>
                                <div class="col-4 border-end border-white-50">
                                    <h4 class="fw-bold mb-0">{{ number_format($activeBatch->total_weight ?? 0, 1) }}</h4>
                                    <small style="font-size: 0.6rem;">กก. รวม</small>
                                </div>
                                <div class="col-4">
                                    <h4 class="fw-bold mb-0" style="font-size: 0.9rem;">
                                        {{ $activeBatch->temp_status ?? '-' }}
                                    </h4>
                                    <small style="font-size: 0.6rem;">ความร้อน</small>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="main__stat-blocks">
                    <div class="main__stat-block main__stat-block--lg">
                        <div class="main__stat-graph main__stat-graph--filled">
                            <svg class="ring" viewBox="0 0 180 180">
                                <circle class="ring-track" cx="90" cy="90" r="82" fill="none"
                                    stroke="rgba(0,255,255,0.2)" stroke-width="12" />
                                <circle class="ring-stroke" cx="90" cy="90" r="82" fill="none" stroke="#fff"
                                    stroke-width="12" stroke-dasharray="515" stroke-dashoffset="150"
                                    transform="rotate(-90,90,90)" />
                            </svg>
                            <div class="main__stat-detail">
                                <strong class="main__stat-value">{{ number_format($totalCarbonSaved, 2) }}</strong>
                                <span class="main__stat-unit">kgCO2e (คาร์บอน)</span>
                                <div class="my-1"></div>
                                <strong class="main__stat-value">{{ number_format($totalWasteWeight, 2) }}</strong>
                                <span class="main__stat-unit">กก. (ขยะสะสม)</span>
                            </div>
                        </div>
                        <div class="main__stat-blocks mb-4">
                            <div class="main__stat-block" data-bs-toggle="modal" data-bs-target="#pointsInfoModal"
                                style="cursor: pointer;">
                                <div class="main__stat-graph" style="max-width: 60px;">
                                    <svg class="ring" viewBox="0 0 60 60">
                                        <circle class="ring-track" cx="30" cy="30" r="26" fill="none" stroke="#e0e0e0"
                                            stroke-width="6" />
                                        <circle class="ring-stroke" cx="30" cy="30" r="26" fill="none" stroke="#ffc107"
                                            stroke-width="6" stroke-dasharray="163" stroke-dashoffset="40"
                                            transform="rotate(-90,30,30)" />
                                    </svg>
                                    <i class="bi bi-star-fill icon"
                                        style="font-size: 1.2rem; color: #ffc107; top: 50%;"></i>
                                </div>
                                <div class="main__stat-detail"
                                    style="position: relative; inset: auto; margin-top: 10px;">
                                    <strong class="main__stat-value text-warning" style="font-size: 1.4em;">
                                        {{ number_format($totalPoints ?? 0) }}
                                    </strong>
                                    <span class="main__stat-unit">แต้มสะสม</span>
                                </div>
                            </div>

                            <div class="main__stat-block">
                                <div class="main__stat-graph" style="max-width: 60px;">
                                    <svg class="ring" viewBox="0 0 60 60">
                                        <circle class="ring-track" cx="30" cy="30" r="26" fill="none" stroke="#e0e0e0"
                                            stroke-width="6" />
                                        <circle class="ring-stroke" cx="30" cy="30" r="26" fill="none" stroke="#28a745"
                                            stroke-width="6" stroke-dasharray="163" stroke-dashoffset="80"
                                            transform="rotate(-90,30,30)" />
                                    </svg>
                                    <i class="bi bi-wallet2 icon"
                                        style="font-size: 1.2rem; color: #28a745; top: 50%;"></i>
                                </div>
                                <div class="main__stat-detail"
                                    style="position: relative; inset: auto; margin-top: 10px;">
                                    <strong class="main__stat-value text-success" style="font-size: 1.4em;">
                                        ฿{{ number_format($totalBalance ?? 0, 2) }}
                                    </strong>
                                    <span class="main__stat-unit">เงินในกระเป๋า</span>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>

                <div class="main__stat-block main__stat-block--lg mb-4">
                    <button type="button" class="btn btn-sm shadow-none position-absolute"
                        style="top: 10px; right: 10px; z-index: 10; border-radius: 50%; width: 32px; height: 32px; background: rgba(255,255,255,0.8); display: flex; align-items: center; justify-content: center;"
                        data-bs-toggle="modal" data-bs-target="#userMetricsModal">
                        <i class="bi bi-gear-fill text-success"></i>
                    </button>
                    <h6 class="fw-bold mb-3"><i class="bi bi-activity text-success"></i> พลังงานที่ได้รับ 7 วันล่าสุด
                    </h6>
                    <div class="chart-container" style="position: relative; height:180px; width:100%;">
                        <canvas id="calorieDashboardChart"></canvas>

                    </div>
                    @if(!Auth::user()->weight)
                        <small class="text-muted"
                            style="font-size: 0.7rem;">*ตั้งค่าข้อมูลส่วนตัวเพื่อคำนวณความต้องการพลังงาน</small>
                    @endif


                </div>


                <div class="main__stat-blocks">

                    <div class="main__stat-block position-relative" data-bs-toggle="modal"
                        data-bs-target="#issueListModal">
                        <div class="main__stat-graph">
                            <svg class="ring" viewBox="0 0 60 60">
                                <circle class="ring-track" cx="30" cy="30" r="26" fill="none" stroke="#e0e0e0"
                                    stroke-width="6" />
                            </svg>
                            <i class="bi bi-exclamation-triangle icon" style="font-size: 1.5rem; color: orange;"></i>

                            @if($pendingIssuesCount > 0)
                                <span class="position-absolute translate-middle badge rounded-pill bg-danger"
                                    style="top: 15px; right: 15px; font-size: 0.7rem; border: 2px solid white;">
                                    {{ $pendingIssuesCount }}
                                </span>
                            @endif
                        </div>
                        <div class="main__stat-detail">
                            <strong class="main__stat-value" style="font-size: 0.85em;">ติดตาม / แจ้ง<div>ปัญหาถังหมัก
                                </div></strong>
                        </div>
                    </div>

                    <a href="{{route('foodwaste.airo.dashboard')}}" class="main__stat-block text-center">
                        <div class="main__stat-graph">
                            <svg class="ring" viewBox="0 0 60 60">
                                <circle class="ring-track" cx="30" cy="30" r="26" fill="none" stroke="#e0e0e0"
                                    stroke-width="6" />
                            </svg>
                            <i class="bi bi-camera-fill icon" style="font-size: 1.5rem;"></i>
                        </div>
                        <div class="main__stat-detail">
                            <strong class="main__stat-value" style="font-size: 0.9em;">บันทึก<div>การหมักประจำวัน</div>
                            </strong>
                        </div>
                    </a>

                    <a href="{{ route('foodwaste.airo.batch_history') }}" class="main__stat-block text-center">
                        <div class="main__stat-graph">
                            <svg class="ring" viewBox="0 0 60 60">
                                <circle class="ring-track" cx="30" cy="30" r="26" fill="none" stroke="#e0e0e0"
                                    stroke-width="6" />
                            </svg>
                            <i class="bi bi-journal-text icon" style="font-size: 1.5rem;"></i>
                        </div>
                        <div class="main__stat-detail">
                            <strong class="main__stat-value" style="font-size: 0.9em;">ดูประวัติ<div>ปุ๋ยแต่ละล็อต</div>
                            </strong>
                        </div>
                    </a>

                    <a href="{{route('foodwaste.airo.how_to')}}" class="main__stat-block text-center">
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
            <div class="main__stat-block" data-bs-toggle="modal" data-bs-target="#pointHistoryModal"
                style="cursor: pointer;">
            </div>

            <div class="modal fade" id="pointHistoryModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                    <div class="modal-content" style="border-radius: 1.5rem; border: none;">
                        <div class="modal-header border-0 pb-0">
                            <h5 class="modal-title fw-bold text-success"><i class="bi bi-clock-history"></i>
                                ประวัติแต้มสะสม</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4">
                            @php
                                // เรียกฟังก์ชันดึงประวัติ (หรือส่งมาจาก Controller ก็ได้)
                                $waste_preference_id = $user->foodwastePreference->id;
                                $transactions = App\Models\FoodWaste\FoodWasteTransaction::where('fw_pref_id_fk', $waste_preference_id)
                                    ->latest()->take(10)->get();
                            @endphp

                            @if($transactions->isEmpty())
                                <div class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox fs-1"></i>
                                    <p>ยังไม่มีประวัติการรับแต้ม</p>
                                </div>
                            @else
                                <div class="timeline">
                                    @foreach($transactions as $trx)
                                        <div class="d-flex align-items-center mb-3 p-3 bg-light" style="border-radius: 1rem;">
                                            <div class="flex-shrink-0">
                                                @if($trx->points > 0)
                                                    <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center"
                                                        style="width: 40px; height: 40px;">
                                                        <i class="bi bi-plus-lg"></i>
                                                    </div>
                                                @else
                                                    <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center"
                                                        style="width: 40px; height: 40px;">
                                                        <i class="bi bi-dash-lg"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <div class="fw-bold text-dark" style="font-size: 0.9rem;">{{ $trx->note }}</div>
                                                <div class="small text-muted">{{ $trx->created_at->format('d M Y | H:i') }}
                                                </div>
                                            </div>
                                            <div class="text-end">
                                                <div class="fw-bold {{ $trx->points > 0 ? 'text-success' : 'text-danger' }}">
                                                    {{ ($trx->points > 0 ? '+' : '') . $trx->points }}
                                                </div>
                                                <div class="small text-muted" style="font-size: 0.7rem;">PTS</div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        <div class="modal-footer border-0">
                            <button type="button" class="btn btn-light w-100 rounded-pill fw-bold"
                                data-bs-dismiss="modal">ปิดหน้าต่าง</button>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    @include('foodwaste.airo._report_modal')
    @include('foodwaste.airo._my_issue_modal')
    <script>
        // แก้ไข typo ตรงนี้: เปลี่ยน oquement เป็น document
        document.addEventListener('DOMContentLoaded', function () {
            const issueSelect = document.querySelector('select[name="issue_type"]');
            if (issueSelect) {
                issueSelect.addEventListener('change', function () {
                    const adviceBox = document.getElementById('auto-advice');
                    const advices = {
                        'smell': '💡 วิธีแก้: เติมใบไม้แห้งสับและพรวนกองปุ๋ยเพื่อเติมอากาศ',
                        'maggots': '💡 ไม่ต้องตกใจ: หนอนแมลงวันลายช่วยย่อยขยะได้เร็วขึ้นมากครับ',
                        'wet': '💡 วิธีแก้: เติมวัตถุแห้งเช่น ขากาแฟ หรือเศษใบไม้แห้งเพิ่มครับ',
                        'mold': '💡 ข้อมูล: ราสีขาวคือราดี ช่วยย่อยสลาย แต่ถ้าสีดำให้เติมปูนขาวเล็กน้อย'
                    };
                    adviceBox.innerHTML = advices[this.value] || '';
                    adviceBox.classList.toggle('d-none', !advices[this.value]);
                });
            }
        });
    </script>



    </div>

    {{-- <a href="#" onclick="matchUserWithKiosk('SLAVE_01')" class="btn btn-primary">SLAVE_01</a> --}}

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
                        ID: USER-{{ $user->id }}
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary w-100" data-bs-dismiss="modal">ปิด</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="scannerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">สแกน QR Code ตู้ Kiosk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        onclick="stopBrowserScanner()"></button>
                </div>
                <div class="modal-body">
                    <div id="reader" style="width: 100%; border-radius: 10px; overflow: hidden;"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary w-100" data-bs-dismiss="modal"
                        onclick="stopBrowserScanner()">ยกเลิก</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="userMetricsModal" tabindex="-1" aria-labelledby="userMetricsModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 1.5em; border: none;">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold" id="userMetricsModalLabel">ตั้งค่าข้อมูลร่างกาย</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.users.update_metrics') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-6">
                                <label class="form-label small fw-bold">เพศ</label>
                                <select name="gender" class="form-select rounded-pill">
                                    <option value="male" {{ Auth::user()->gender == 'male' ? 'selected' : '' }}>ชาย
                                    </option>
                                    <option value="female" {{ Auth::user()->gender == 'female' ? 'selected' : '' }}>หญิง
                                    </option>
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-bold">อายุ (ปี)</label>
                                <input type="number" name="age" class="form-control rounded-pill"
                                    value="{{ Auth::user()->age }}" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-bold">น้ำหนัก (kg)</label>
                                <input type="number" step="0.1" name="weight" class="form-control rounded-pill"
                                    value="{{ Auth::user()->weight }}" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-bold">ส่วนสูง (cm)</label>
                                <input type="number" name="height" class="form-control rounded-pill"
                                    value="{{ Auth::user()->height }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="submit"
                            class="btn btn-primary w-100 rounded-pill fw-bold py-2 shadow">บันทึกข้อมูล</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="pointsInfoModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 2em; border: none; background: #f8f9fa;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold text-success"><i class="bi bi-gift-fill"></i> วิธีการรับแต้มสะสม</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="d-flex align-items-center mb-3 p-3 bg-white shadow-sm" style="border-radius: 1.2em;">
                        <div class="flex-shrink-0 bg-warning-light p-2 rounded-circle me-3">
                            <i class="bi bi-calendar-check-fill text-warning fs-4"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold">เทเศษอาหารครั้งแรกของวัน</h6>
                            <small class="text-muted">รับทันที <strong>10 แต้ม</strong></small>
                        </div>
                    </div>

                    <div class="d-flex align-items-center mb-3 p-3 border border-success border-2"
                        style="border-radius: 1.2em; background: #e9f7ef;">
                        <div class="flex-shrink-0 p-2 rounded-circle me-3" style="background: #28a745;">
                            <i class="bi bi-moon-stars-fill text-white fs-4"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold text-success text-uppercase">โบนัสรวบรวมเทตอนเย็น</h6>
                            <small class="text-dark">รวบรวมมาเททีเดียวเวลา 17:00 - 21:00 น.
                                (โดยไม่มีการเทช่วงเช้า/เที่ยง) <strong>รับเพิ่ม +20 แต้ม</strong> (รวมเป็น 30
                                แต้ม)</small>
                        </div>
                    </div>

                    <div class="d-flex align-items-center p-3 bg-white shadow-sm" style="border-radius: 1.2em;">
                        <div class="flex-shrink-0 bg-info-light p-2 rounded-circle me-3">
                            <i class="bi bi-moisture text-info fs-4"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold">แต้มคุณภาพ (วัดโดยเจ้าหน้าที่)</h6>
                            <small class="text-muted">ทุกๆ 1 กก. ของเนื้อขยะแห้ง (หักความชื้น) รับ <strong>100
                                    แต้ม</strong> </small>
                        </div>
                    </div>

                    <div class="mt-4 p-3 bg-light rounded-3">
                        <p class="small text-muted mb-0"><i class="bi bi-lightbulb-fill text-warning"></i>
                            <strong>เคล็ดลับ:</strong> สลัดน้ำออกจากเศษอาหารให้แห้งที่สุดก่อนเท
                            เพื่อให้ได้แต้มสูงสุดตอนจบสัปดาห์!
                        </p>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-success w-100 rounded-pill py-2 fw-bold"
                        data-bs-dismiss="modal">เข้าใจแล้ว!</button>
                </div>
            </div>
        </div>
    </div>

    <style>
        .bg-warning-light {
            background-color: rgba(255, 193, 7, 0.1);
        }

        .bg-info-light {
            background-color: rgba(13, 202, 240, 0.1);
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>
    <script src="https://unpkg.com/html5-qrcode"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script
        src="https://cdn.jsdelivr.net/npm/chartjs-plugin-annotation@3.0.1/dist/chartjs-plugin-annotation.min.js"></script>
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




        async function startScanKiosk() {

            // 2. ถ้าไม่ได้อยู่ใน LINE หรือ LINE Scanner มีปัญหา ให้ใช้ Browser Camera แทน
            // สร้าง Modal หรือ Div สำหรับแสดงหน้ากล้อง
            showBrowserScanner();
        }

        function showBrowserScanner() {
            // สร้างพื้นที่แสดงกล้อง (แนะนำให้สร้างเป็น Modal ของ Bootstrap)
            const html5QrCode = new Html5Qrcode("reader");
            // หมายเหตุ: ต้องมี <div id="reader"></div> ใน HTML ของคุณ

            const config = { fps: 10, qrbox: { width: 250, height: 250 } };

            html5QrCode.start({ facingMode: "environment" }, config, (decodedText) => {
                matchUserWithKiosk(decodedText);
                html5QrCode.stop(); // หยุดกล้องเมื่อเจอ QR
                bootstrap.Modal.getInstance(document.getElementById('scannerModal')).hide();
            });
        }

        let html5QrCode = null; // เก็บ instance ไว้ข้างนอกเพื่อสั่งปิดได้

        async function startScanKiosk() {
            // 1. ตรวจสอบว่าอยู่ใน LINE หรือไม่ และลองใช้ Native Scanner
            if (typeof liff !== 'undefined' && liff.isInClient() && liff.scanCodeV2) {
                try {
                    const result = await liff.scanCodeV2();
                    if (result.value) {
                        matchUserWithKiosk(result.value);
                        return;
                    }
                } catch (error) {
                    console.log("LINE Scan canceled/failed, switching to browser mode.");
                }
            }

            // 2. ถ้าไม่ใช่ LINE หรือ Native พัง ให้เปิด Modal และใช้ Browser Scanner
            const scannerModal = new bootstrap.Modal(document.getElementById('scannerModal'));
            scannerModal.show();

            // รอให้ Modal กางออกเสร็จก่อนเริ่มกล้อง (กัน Error element not found)
            document.getElementById('scannerModal').addEventListener('shown.bs.modal', function () {
                showBrowserScanner();
            }, { once: true });
        }

        function showBrowserScanner() {
            if (html5QrCode === null) {
                html5QrCode = new Html5Qrcode("reader");
            }

            // ปรับ Configuration ให้สแกนไวและแม่นยำขึ้น
            const config = {
                fps: 20, // เพิ่มเฟรมต่อวินาทีเพื่อให้จับภาพได้ต่อเนื่องขึ้น
                qrbox: function (viewfinderWidth, viewfinderHeight) {
                    // ปรับขนาดกล่องสแกนให้สัมพันธ์กับหน้าจอ (ใช้ 70% ของด้านที่สั้นที่สุด)
                    let minEdge = Math.min(viewfinderWidth, viewfinderHeight);
                    let qrboxSize = Math.floor(minEdge * 0.75);
                    return { width: qrboxSize, height: qrboxSize };
                },
                aspectRatio: 1.0 // บังคับสัดส่วนช่องมองภาพเป็นสี่เหลี่ยมจัตุรัส
            };

            html5QrCode.start(
                { facingMode: "environment" }, // บังคับใช้กล้องหลัง
                config,
                (decodedText) => {
                    // เมื่อสแกนสำเร็จ
                    console.log("Found QR Code: ", decodedText);

                    // เพิ่มการสั่นแจ้งเตือน (ถ้าเครื่องรองรับ)
                    if (navigator.vibrate) navigator.vibrate(100);

                    matchUserWithKiosk(decodedText);
                    stopBrowserScanner();

                    // ปิด Modal
                    let modalEl = document.getElementById('scannerModal');
                    let modalInstance = bootstrap.Modal.getInstance(modalEl);
                    if (modalInstance) modalInstance.hide();
                },
                (errorMessage) => {
                    // ปล่อยว่างไว้: ไลบรารีจะพยายามสแกนเฟรมถัดไปเรื่อยๆ
                }
            ).catch((err) => {
                console.error("Camera Start Error: ", err);
                alert("ไม่สามารถเปิดกล้องได้: " + err);
            });
        }
        function stopBrowserScanner() {
            if (html5QrCode) {
                html5QrCode.stop().then(() => {
                    console.log("Camera stopped");
                }).catch((err) => {
                    console.error("Unable to stop camera", err);
                });
            }
        }

        function matchUserWithKiosk(kioskId) {
            $.post("{{ url('api/kiosk/match') }}", {
                _token: "{{ csrf_token() }}",
                kiosk_id: kioskId,
                user_id: "{{ $userWastePref->user_id }}"
            })
                .done(function (response) {
                    if (response.status === 'success') {
                        speak('เชื่อมต่อสำเร็จ!')
                        // ใช้ SweetAlert2 แสดงสถานะรอ
                        Swal.fire({
                            title: 'เชื่อมต่อสำเร็จ!',
                            text: 'กรุณาดำเนินการต่อที่ตู้ Kiosk ' + response.kiosk_name,
                            icon: 'success',
                            allowOutsideClick: false,
                            showConfirmButton: false,
                            html: '<div class="spinner-border text-primary" role="status"></div><p class="mt-3">กำลังรอการทำรายการจากตู้...</p>',
                        });

                        // เริ่มทำการตรวจสอบสถานะจากตู้ (Polling)
                        // เพื่อดูว่าตู้ชั่งน้ำหนักเสร็จหรือยัง
                        checkKioskTransactionStatus(kioskId);
                    }
                })
                .fail(function (xhr) {
                    Swal.fire('ข้อผิดพลาด', xhr.responseJSON.message || 'เชื่อมต่อเซิร์ฟเวอร์ไม่ได้', 'error');
                });
        }

        function checkKioskTransactionStatus(kioskId) {
            let checkInterval = setInterval(function () {
                $.get("{{ url('api/kiosk/check-transaction/') }}/" + kioskId, function (res) {
                    if (res.status === 'completed') {
                        clearInterval(checkInterval); // หยุดถาม

                        // แจ้งเตือนเมื่อทำรายการเสร็จสิ้น
                        Swal.fire({
                            title: 'ขอบคุณที่รักษ์โลก!',
                            text: 'คุณได้รับ ' + res.points + ' แต้ม',
                            icon: 'success',
                            confirmButtonText: 'ตกลง'
                        }).then(() => {
                            location.reload(); // โหลดหน้าใหม่เพื่ออัปเดตยอดคงเหลือใน Dashboard
                        });
                    }
                });
            }, 3000); // เช็คทุกๆ 3 วินาที
        }

        function speak(text) {
            // ตรวจสอบว่า Browser รองรับไหม
            if ('speechSynthesis' in window) {
                const utterance = new SpeechSynthesisUtterance(text);
                utterance.lang = 'th-TH'; // ตั้งค่าเป็นภาษาไทย
                utterance.pitch = 1;      // ระดับเสียง (0-2)
                utterance.rate = 1;       // ความเร็ว (0.1-10)

                window.speechSynthesis.speak(utterance);
            } else {
                console.error("Browser ของคุณไม่รองรับการออกเสียง");
            }
        }
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // 1. ลงทะเบียน Plugin (ทำครั้งเดียวตอนเริ่ม)
            if (typeof chartjsPluginAnnotation !== 'undefined') {
                Chart.register(chartjsPluginAnnotation);
            }

            // 2. เตรียมข้อมูล (รับค่าจาก PHP)
            const ctx = document.getElementById('calorieDashboardChart').getContext('2d');
            const targetCalories = {{ $targetCalories ?? 0 }};
            const chartLabels = {!! json_encode($chartLabels ?? ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']) !!};
            const chartData = {!! json_encode($chartData ?? [0, 0, 0, 0, 0, 0, 0]) !!};

            // 3. สร้างกราฟเพียง "อันเดียว" (เลือกเอาแบบ Bar ที่เราทำสี Warning ไว้จะสวยกว่าครับ)
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: chartLabels,
                    datasets: [{
                        label: 'kcal',
                        data: chartData,
                        // เปลี่ยนสีแท่งกราฟอัตโนมัติถ้ากินเกินเป้าหมาย
                        backgroundColor: chartData.map(value => {
                            return (targetCalories > 0 && value > targetCalories)
                                ? 'rgba(255, 99, 132, 0.8)'
                                : 'rgba(56, 239, 125, 0.6)';
                        }),
                        borderColor: chartData.map(value => {
                            return (targetCalories > 0 && value > targetCalories) ? '#ff6384' : '#11998e';
                        }),
                        borderWidth: 1,
                        borderRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        // วาดเส้นประเป้าหมาย (Goal Line)
                        annotation: {
                            annotations: {
                                line1: {
                                    type: 'line',
                                    yMin: targetCalories,
                                    yMax: targetCalories,
                                    borderColor: 'red',
                                    borderWidth: 2,
                                    borderDash: [6, 6],
                                    label: {
                                        display: targetCalories > 0,
                                        content: 'เป้าหมาย: ' + targetCalories + ' kcal',
                                        position: 'end',
                                        backgroundColor: 'rgba(255, 0, 0, 0.8)',
                                        color: '#fff',
                                        font: { size: 10 }
                                    }
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            suggestedMax: targetCalories > 0 ? targetCalories + 500 : 2000
                        },
                        x: { grid: { display: false } }
                    }
                }
            });

            // --- ส่วนของ Event Listener สำหรับการแจ้งปัญหา (คงไว้เหมือนเดิม) ---
            const issueSelect = document.querySelector('select[name="issue_type"]');
            if (issueSelect) {
                issueSelect.addEventListener('change', function () {
                    const adviceBox = document.getElementById('auto-advice');
                    const advices = {
                        'smell': '💡 วิธีแก้: เติมใบไม้แห้งสับและพรวนกองปุ๋ยเพื่อเติมอากาศ',
                        'maggots': '💡 ไม่ต้องตกใจ: หนอนแมลงวันลายช่วยย่อยขยะได้เร็วขึ้นมากครับ',
                        'wet': '💡 วิธีแก้: เติมวัตถุแห้งเช่น ขากาแฟ หรือเศษใบไม้แห้งเพิ่มครับ',
                        'mold': '💡 ข้อมูล: ราสีขาวคือราดี ช่วยย่อยสลาย แต่ถ้าสีดำให้เติมปูนขาวเล็กน้อย'
                    };
                    adviceBox.innerHTML = advices[this.value] || '';
                    adviceBox.classList.toggle('d-none', !advices[this.value]);
                });
            }
        });
    </script>
</body>

</html>
