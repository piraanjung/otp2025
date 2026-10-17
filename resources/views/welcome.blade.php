<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PI-OS | ระบบบริหารจัดการองค์กรปกครองส่วนท้องถิ่นดิจิทัล</title>
    <!-- Bootstrap 4.6 & FontAwesome -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Bruno+Ace+SC&family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- AOS Animation Library -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-purple: #7C3AED;
            --primary-purple-dark: #5B21B6;
            --secondary-indigo: #6366F1;
            --light-purple-bg: #FAF5FF;
            --lavender-card: #F3E8FF;
            --soft-surface: #F8FAFC;
            --card-white: #FFFFFF;
            --text-dark: #0F172A;
            --text-muted: #475569;
            --border-subtle: #E2E8F0;
        }
        body {
            font-family: 'Sarabun', sans-serif !important;
            background-color: var(--soft-surface) !important;
            color: var(--text-dark) !important;
            font-size: 1.15rem !important;
            line-height: 1.7 !important;
            overflow-x: hidden;
            margin: 0;
            padding: 0;
            scroll-behavior: smooth;
        }
        /* Glassmorphism Header Bar */
        .navbar-custom {
            padding: 18px 0;
            transition: all 0.3s ease;
            z-index: 1050;
            background: rgba(255, 255, 255, 0.92) !important;
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border-subtle);
        }
        .navbar-custom.scrolled {
            box-shadow: 0 10px 30px rgba(124, 58, 237, 0.08);
            padding: 12px 0;
        }
        .brand-logo {
            font-family: 'Bruno Ace SC', sans-serif;
            font-size: 1.75rem !important;
            font-weight: 700;
            color: var(--primary-purple) !important;
        }
        .nav-link {
            font-weight: 600;
            color: var(--text-dark) !important;
            margin: 0 4px;
            font-size: 1.05rem !important;
            transition: all 0.2s ease;
        }
        .nav-link:hover {
            color: var(--primary-purple) !important;
        }
        .btn-purple-action {
            background: linear-gradient(135deg, var(--primary-purple), var(--secondary-indigo)) !important;
            color: #FFFFFF !important;
            border-radius: 12px !important;
            padding: 10px 26px !important;
            font-weight: 700;
            font-size: 1.05rem !important;
            border: none;
            box-shadow: 0 8px 20px rgba(124, 58, 237, 0.3);
            transition: all 0.3s ease;
        }
        .btn-purple-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 25px rgba(124, 58, 237, 0.4);
            color: #FFFFFF !important;
        }
        /* Hero Banner */
        .hero-section {
            padding-top: 140px;
            padding-bottom: 90px;
            background: linear-gradient(180deg, #FFFFFF 0%, var(--light-purple-bg) 100%);
            position: relative;
        }
        .hero-title {
            font-size: 3.2rem !important;
            font-weight: 700;
            line-height: 1.25;
            color: var(--text-dark);
        }
        .text-purple-gradient {
            background: linear-gradient(135deg, #7C3AED, #4F46E5);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        /* Purple Background Container Blocks */
        .purple-block-container {
            background: linear-gradient(135deg, #7C3AED 0%, #6366F1 100%);
            border-radius: 36px;
            padding: 48px;
            color: #FFFFFF;
            position: relative;
            box-shadow: 0 20px 50px rgba(124, 58, 237, 0.22);
            overflow: hidden;
        }
        .purple-block-container.bg-alt-purple {
            background: linear-gradient(135deg, #4F46E5 0%, #7C3AED 100%);
        }
        /* Section Headings */
        .module-section {
            padding: 90px 0;
            border-bottom: 1px solid var(--border-subtle);
            position: relative;
        }
        .bg-white-section { background-color: #FFFFFF !important; }
        .bg-purple-light-section { background-color: #FAF5FF !important; }

        .section-title-block h2 {
            font-weight: 700;
            font-size: 2.6rem !important;
            color: var(--text-dark);
            margin-bottom: 16px;
        }
        .section-title-block p {
            color: var(--text-muted);
            font-size: 1.25rem !important;
        }
        /* Modern SaaS Floating Cards */
        .saas-card {
            background: var(--card-white) !important;
            border-radius: 24px !important;
            border: 1px solid var(--border-subtle) !important;
            padding: 32px !important;
            height: 100%;
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
        }
        .saas-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(124, 58, 237, 0.15) !important;
            border-color: #C084FC !important;
        }
        .saas-card h5, .saas-card h6 {
            font-size: 1.35rem !important;
            font-weight: 700 !important;
        }
        .saas-card p {
            font-size: 1.08rem !important;
            color: var(--text-muted) !important;
        }
        .icon-purple-pill {
            height: 58px;
            border-radius: 18px;
            background: var(--lavender-card);
            color: var(--primary-purple);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.65rem !important;
            margin-bottom: 18px;
        }
        /* Image Frame Display */
        .img-saas-frame {
            border-radius: 28px;
            overflow: hidden;
            border: 1px solid var(--border-subtle);
            background: #FFFFFF;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.08);
        }
        .img-saas-frame img {
            width: 100%;
            height: auto;
            object-fit: cover;
        }
        /* Bottom Call-To-Action Banner */
        .cta-banner-purple {
            background: linear-gradient(135deg, #7C3AED 0%, #4F46E5 100%);
            border-radius: 36px;
            padding: 60px 40px;
            color: #FFFFFF;
            box-shadow: 0 20px 50px rgba(124, 58, 237, 0.3);
        }
        /* Footer */
        .footer-saas {
            background: #0F172A;
            color: #94A3B8;
            padding: 60px 0;
            font-size: 1rem;
        }

        /* =========================================================
           🖥️ DESKTOP DISPLAY (คงสไตล์เดิมที่คุณจัดไว้ 100%)
           ========================================================= */
        @media (min-width: 992px) {
            .hero-title-overlay {
                position: absolute;
                z-index: 1300;
                margin-top: -3rem;
            }
            .kiosk-floating-img {
                position: absolute;
                width: 350px;
                z-index: 300;
                margin-left: -7%;
                margin-top: -25rem;
            }
            .airobact-title-offset {
                margin-left: 14rem;
            }
            .airobact-hero-img {
                position: absolute;
                z-index: 200;
                width: 26%;
                margin-left: -8rem;
                top: 6rem;
            }
            .airobact-app-img {
                position: absolute;
                z-index: 300;
                margin-left: 71%;
                width: 250px;
                margin-top: -1rem;
            }
            .video-desktop-width {
                width: 740px !important;
                top: 0;
            }
        }

        /* =========================================================
           📱 MOBILE RESPONSIVE (ปรับเฉพาะตอนเปิดบนหน้าจอมือถือ)
           ========================================================= */
        @media (max-width: 991.98px) {
            .hero-section { padding-top: 110px; }
            .hero-title-overlay {
                position: relative !important;
                margin-top: 0 !important;
                text-align: center;
            }
            .hero-title { font-size: 2.2rem !important; }
            .section-title-block h2 { font-size: 1.9rem !important; }
            .purple-block-container { padding: 24px; border-radius: 24px; }
            
            /* ซ่อนรูปที่ลอยทับเกินขอบเฉพาะบนมือถือ */
            .kiosk-floating-img {
                position: relative !important;
                margin-left: 0 !important;
                margin-top: 0 !important;
                width: 100% !important;
                max-width: 280px;
                display: block;
                margin: 0 auto 20px auto !important;
            }
            .airobact-title-offset { margin-left: 0 !important; }
            .airobact-hero-img {
                position: relative !important;
                margin-left: 0 !important;
                top: 0 !important;
                width: 100% !important;
                max-width: 260px;
                display: block;
                margin: 0 auto 20px auto !important;
            }
            .airobact-app-img { display: none !important; }
            .video-desktop-width {
                width: 100% !important;
            }
        }
    </style>
</head>
<body>
    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-xl navbar-light fixed-top navbar-custom" id="mainNavbar">
        <div class="container">
            <a class="navbar-brand brand-logo d-flex align-items-center" href="#">
                <i class="fas fa-cubes mr-2"></i>PI-OS 
            </a>
            <button class="navbar-toggler border-0" type="button" data-toggle="collapse" data-target="#navContent">
                <span class="fas fa-bars"></span>
            </button>
            <div class="collapse navbar-collapse" id="navContent">
                <ul class="navbar-nav ml-auto align-items-center">
                    <li class="nav-item"><a class="nav-link" href="#sec-water">ประปา</a></li>
                    <li class="nav-item"><a class="nav-link" href="#sec-kiosk">คืนขวด KIOSK</a></li>
                    <li class="nav-item"><a class="nav-link" href="#sec-inventory">คลังพัสดุ</a></li>
                    <li class="nav-item"><a class="nav-link" href="#sec-recycle">ธนาคารขยะ</a></li>
                    <li class="nav-item"><a class="nav-link" href="#sec-trashfee">ขยะรายปี</a></li>
                    <li class="nav-item"><a class="nav-link" href="#sec-funeral">ฌาปนกิจ</a></li>
                    <li class="nav-item"><a class="nav-link" href="#sec-foodwaste">ขยะเปียก IoT</a></li>
                    <li class="nav-item"><a class="nav-link" href="#sec-saving">ออมทรัพย์</a></li>
                    <li class="nav-item ml-lg-3"><a class="nav-link btn-purple-action" href="{{ route('login') }}">เข้าสู่ระบบ</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- HERO SECTION -->
    <section class="hero-section">
        <div class="container">
            <div class="col-lg-12 mb-5 mb-lg-0 hero-title-overlay" data-aos="fade-right" data-aos-duration="800">
                <h1 class="hero-title mb-4">ขับเคลื่อนองค์กรท้องถิ่น<span class="text-purple-gradient"> ด้วยระบบดิจิทัล PI-OS</span></h1>
            </div>
            <div class="row align-items-center mb-5">
                <div class="col-lg-12 mt-3" data-aos="fade-left" data-aos-duration="1000">
                    <div class="img-saas-frame">
                        <img src="{{ asset('imgs/1.png') }}" alt="PI-OS Platform Dashboard">
                    </div>
                </div>
            </div>
            <!-- Purple Curved Block Banner -->
            <div class="purple-block-container" data-aos="zoom-in-up" data-aos-duration="900">
                <div class="row align-items-center">
                    <div class="col-lg-8 mb-3 mb-lg-0">
                        <h4 class="font-weight-bold text-white mb-2"><i class="fas fa-shield-alt mr-2"></i>ระบบปฏิบัติการมาตรฐานเพื่อองค์กรปกครองส่วนท้องถิ่น</h4>
                        <p class="text-white-50 mb-0">รองรับการปฏิบัติงานของเจ้าหน้าที่ ปลอดภัย ตรวจสอบประวัติบันทึกย้อนหลังได้ 100%</p>
                    </div>
                    <div class="col-lg-4 text-lg-right">
                        <a href="{{ route('login') }}" class="btn btn-light text-purple font-weight-bold rounded-pill px-4 py-2">เริ่มต้นใช้งาน</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION 1: งานประปา -->
    <section id="sec-water" class="module-section bg-white-section">
        <div class="container">
            <div class="section-title-block text-center" data-aos="fade-up" data-aos-duration="800">
                <h2>ระบบบริหารจัดการงานประปา</h2>
                <p>ทะเบียนผู้ใช้น้ำ คำนวณค่าน้ำ จดมิเตอร์และออกบิลค่าน้ำผ่านมือถือ พร้อม Dashboard สรุปการเงิน</p>
            </div>
            <div class="purple-block-container" data-aos="zoom-in-up" data-aos-duration="900">
                <div class="row align-items-center">
                    <div class="col-lg-6 mb-4 mb-lg-0" data-aos="fade-right" data-aos-duration="1000">
                        <div class="img-saas-frame">
                            <img src="https://images.unsplash.com/photo-1581092160607-ee22621dd758?auto=format&fit=crop&w=1000&q=80" alt="งานประปา">
                        </div>
                    </div>
                    <div class="col-lg-6 pl-lg-4" data-aos="fade-left" data-aos-duration="1000">
                        <div class="saas-card mb-3">
                            <div class="icon-purple-pill"><i class="fas fa-mobile-alt"></i></div>
                            <h5 class="text-dark mb-2">จดมิเตอร์ & ออกบิลมือถือหน้าบ้าน</h5>
                            <p class="mb-0">เจ้าหน้าที่พิมพ์ใบแจ้งหนี้พร้อม QR Code สแกนจ่ายเงินได้ทันที เพิ่มความสะดวกและลดข้อผิดพลาดในการบันทึกข้อมูล</p>
                        </div>
                        <div class="saas-card">
                            <div class="icon-purple-pill"><i class="fas fa-chart-pie"></i></div>
                            <h5 class="text-dark mb-2">ทะเบียนค้างชำระ & สรุปค่าน้ำประปา</h5>
                            <p class="mb-0">ระบบติดตามลูกหนี้ค้างชำระอัตโนมัติ สรุปรายงานรายรับ ต้นทุนผลิตค่าน้ำ และพิมพ์ใบเสร็จรับเงินอย่างเป็นระบบ</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION 2: ตู้คืนขวดอัตโนมัติ (KIOSK) -->
    <section id="sec-kiosk" class="module-section bg-purple-light-section">
        <div class="container">
            <div class="section-title-block text-center" data-aos="fade-up" data-aos-duration="800">
                <h2>ตู้คืนขวดอัตโนมัติ (Reverse Vending Kiosk)</h2>
                <p>นวัตกรรมตู้หยอดขวด PET ประมวลผลรูปทรงด้วย Computer Vision สะสมแต้มและโอนเงินเข้าบัญชีสมาชิก</p>
            </div>
            <div class="row align-items-center">
                <div class="col-lg-3">
                    <img src="{{ asset('imgs/kioskbox.png') }}" alt="ตู้คืนขวดอัตโนมัติ" class="kiosk-floating-img">
                </div>
                <div class="col-lg-9 order-lg-2 mb-4 mb-lg-0" data-aos="fade-left" data-aos-duration="900">
                    <div class="img-saas-frame">
                        <div class="row p-3" data-aos="fade-right" data-aos-duration="900">
                            <div class="col-12 col-md-6 mb-3 mb-md-0">
                                <div class="saas-card">
                                    <div class="icon-purple-pill"><i class="fas fa-microchip mr-2"></i></div>
                                    <h5 class="text-dark mb-2">AI Computer Vision</h5>
                                    <p class="mb-0">คัดแยกขนาดและพื้นที่ขวดผ่านกล้องหน้าตู้ ช่วยแยกประเภทขวด PET ได้อย่างแม่นยำ</p>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="saas-card">
                                    <div class="icon-purple-pill"><i class="fas fa-coins mr-2"></i></div>
                                    <h5 class="text-dark mb-2">สะสมแต้ม & โอนเงินฝากอัตโนมัติ</h5>
                                    <p class="mb-0">ประชาชนสแกน QR Code รับเงินสะสมขยะเข้าสมุดบัญชีธนาคารขยะทันทีเมื่อหยอดขวดสำเร็จ</p>
                                </div>
                            </div>
                        </div>
                        <img src="{{ asset('imgs/kiosk_workflow.png') }}" alt="ตู้คืนขวดอัตโนมัติ">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION 3: คลังพัสดุ -->
    <section id="sec-inventory" class="module-section bg-white-section">
        <div class="container">
            <div class="section-title-block text-center" data-aos="fade-up" data-aos-duration="800">
                <h2>ระบบคลังพัสดุและครุภัณฑ์</h2>
                <p>เบิกจ่ายพัสดุ จัดการสต็อก Lot/Serial Number และสายงานอนุมัติดิจิทัล</p>
            </div>
            <div class="purple-block-container bg-alt-purple" data-aos="zoom-in-up" data-aos-duration="900">
                <div class="row align-items-center">
                    <div class="col-lg-6 mb-4 mb-lg-0" data-aos="fade-right" data-aos-duration="1000">
                        <div class="img-saas-frame">
                            <img src="https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?auto=format&fit=crop&w=1000&q=80" alt="คลังพัสดุ">
                        </div>
                    </div>
                    <div class="col-lg-6 pl-lg-4" data-aos="fade-left" data-aos-duration="1000">
                        <div class="saas-card mb-3">
                            <div class="icon-purple-pill"><i class="fas fa-tasks"></i></div>
                            <h5 class="text-dark mb-2">Multi-step Approval Workflow</h5>
                            <p class="mb-0">ส่งใบเบิกอนุมัติตามลำดับชั้น ตรวจสอบประวัติบันทึกย้อนหลังได้ชัดเจน</p>
                        </div>
                        <div class="saas-card">
                            <div class="icon-purple-pill"><i class="fas fa-barcode"></i></div>
                            <h5 class="text-dark mb-2">ตัดสต็อก & ออกการ์ดควบคุมพัสดุ A4</h5>
                            <p class="mb-0">พิมพ์เอกสารเสนออนุมัติการเบิกจ่าย ตัดยอดคงเหลือในคลังพัสดุ และตรวจสอบรายงานประจำปี</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION 4: ธนาคารขยะรีไซเคิล -->
    <section id="sec-recycle" class="module-section bg-purple-light-section">
        <div class="container">
            <div class="section-title-block text-center" data-aos="fade-up" data-aos-duration="800">
                <h2>ธนาคารขยะรีไซเคิล</h2>
                <p>เปลี่ยนขยะเป็นเงินฝาก บริหารจุดรับซื้อ ตัดรอบจ่ายเงินสดประจำสัปดาห์ และออกสมุดบัญชีสมาชิก</p>
            </div>
            <div class="row align-items-center">
                <div class="col-lg-6 order-lg-2 mb-4 mb-lg-0" data-aos="fade-left" data-aos-duration="900">
                    <div class="img-saas-frame">
                        <img src="https://images.unsplash.com/photo-1532996122724-e3c354a0b15b?auto=format&fit=crop&w=1000&q=80" alt="ธนาคารขยะรีไซเคิล">
                    </div>
                </div>
                <div class="col-lg-6 order-lg-1 pr-lg-4" data-aos="fade-right" data-aos-duration="900">
                    <div class="saas-card mb-4">
                        <div class="icon-purple-pill"><i class="fas fa-recycle"></i></div>
                        <h5 class="text-dark mb-2">รับซื้อขยะหน้างาน & สแกนบาร์โค้ด</h5>
                        <p class="mb-0">กำหนดราคาขยะประจำวัน ชั่งน้ำหนักหน้างาน ออกใบเสร็จ และบันทึกยอดเงินเข้าสมุดบัญชีเงินฝากสมาชิก</p>
                    </div>
                    <div class="saas-card">
                        <div class="icon-purple-pill"><i class="fas fa-file-invoice-dollar"></i></div>
                        <h5 class="text-dark mb-2">ระบบรวบรวม Batch ขอถอนเงินสด</h5>
                        <p class="mb-0">ตัดรอบเสนออนุมัติเบิกจ่ายเงินสดประจำสัปดาห์ รองรับการมอบอำนาจรับเงินแทนของผู้สูงอายุ และสแกนจ่ายเงินสดหน้าเคาน์เตอร์</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION 5: ค่าถังขยะรายปี & ผังเมือง GIS -->
    <section id="sec-trashfee" class="module-section bg-white-section">
        <div class="container">
            <div class="section-title-block text-center" data-aos="fade-up" data-aos-duration="800">
                <h2>จัดเก็บค่าธรรมเนียมถังขยะรายปี</h2>
                <p>จัดการตำแหน่งพิกัดบ้านเรือน GIS จุดตั้งถังขยะ ออกบิลค่าธรรมเนียมรายปี และติดตามสถานะจ่ายเงิน</p>
            </div>
            <div class="purple-block-container" data-aos="zoom-in-up" data-aos-duration="900">
                <div class="row mb-4">
                    <div class="col-12 col-md-6 mb-3 mb-md-0" data-aos="fade-left" data-aos-duration="1000">
                        <div class="saas-card">
                            <div class="icon-purple-pill"><i class="fas fa-map-marked-alt"></i></div>
                            <h5 class="text-dark mb-2">แผนที่ปักมุดพิกัด GIS บ้านเรือน</h5>
                            <p class="mb-0">แสดงจุดตั้งถังขยะครัวเรือนและพิกัดบ้านบนแผนที่ดาวเทียม ช่วยให้เจ้าหน้าที่วางแผนจัดเก็บขยะได้อย่างทั่วถึง</p>
                        </div>
                    </div>
                    <div class="col-12 col-md-6" data-aos="fade-left" data-aos-duration="1000">
                        <div class="saas-card">
                            <div class="icon-purple-pill"><i class="fas fa-receipt"></i></div>
                            <h5 class="text-dark mb-2">ออกบิลค่าธรรมเนียม & ติดตามค้างชำระ</h5>
                            <p class="mb-0">ออกใบแจ้งชำระค่าธรรมเนียมขยะรายปี พิมพ์ใบเสร็จ และตรวจสอบรายชื่อบ้านเรือนที่ยังไม่ได้ชำระเงิน</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12 mb-2 mt-2 mb-lg-0" data-aos="fade-right" data-aos-duration="1200">
                    <div class="img-saas-frame">
                        <img src="{{ asset('imgs/annual_map.png') }}" alt="แผนที่ GIS ค่าถังขยะ" style="max-height: 450px; width: 100%; object-fit: cover;">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION 6: กองทุนฌาปนกิจ -->
    <section id="sec-funeral" class="module-section bg-purple-light-section">
        <div class="container">
            <div class="section-title-block text-center" data-aos="fade-up" data-aos-duration="800">
                <h2>กองทุนฌาปนกิจสงเคราะห์</h2>
                <p>สวัสดิการชุมชน หักสงเคราะห์ศพจากเงินฝากขยะอัตโนมัติ ติดตามทะเบียนผู้เสียชีวิต และการจ่ายเงินเยียวยา</p>
            </div>
            <div class="row align-items-center">
                <div class="col-lg-6 order-lg-2 mb-4 mb-lg-0" data-aos="fade-left" data-aos-duration="900">
                    <div class="img-saas-frame">
                        <img src="https://images.unsplash.com/photo-1576765608535-5f04d1e3f289?auto=format&fit=crop&w=1000&q=80" alt="กองทุนฌาปนกิจ">
                    </div>
                </div>
                <div class="col-lg-6 order-lg-1 pr-lg-4" data-aos="fade-right" data-aos-duration="900">
                    <div class="saas-card mb-4">
                        <div class="icon-purple-pill"><i class="fas fa-hand-holding-heart"></i></div>
                        <h5 class="text-dark mb-2">เชื่อมต่อเงินฝากปันผลธนาคารขยะ</h5>
                        <p class="mb-0">ระบบตัดเงินสมทบเข้ากองทุนฌาปนกิจจากปันผลขยะรีไซเคิลของสมาชิกให้อัตโนมัติ สร้างสวัสดิการชุมชนอย่างยั่งยืน</p>
                    </div>
                    <div class="saas-card">
                        <div class="icon-purple-pill"><i class="fas fa-file-medical-alt"></i></div>
                        <h5 class="text-dark mb-2">โปร่งใส ตรวจสอบประวัติเงินสงเคราะห์ได้</h5>
                        <p class="mb-0">บันทึกยอดเงินเยียวยาสงเคราะห์ศพแก่ครอบครัวผู้เสียชีวิต พร้อมรายงานเงินกองทุนคงเหลือแบบ Real-time</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION 7: ถังขยะเปียกครัวเรือน (AiroBact) -->
    <section id="sec-foodwaste" class="module-section bg-white-section">
        <div class="container position-relative">
            <div class="section-title-block text-center airobact-title-offset" data-aos="fade-up" data-aos-duration="800">
                <h2>ถังพักและหมักเศษอาหาร AiroBact (ไอโรแบคท์)</h2>
                <p>คัดแยกเศษอาหารจากต้นทาง ผลิตจากวัสดุ Upcycling ระบายอากาศได้ดี หมักแบบใช้ออกซิเจน ไร้กลิ่นเหม็นเน่า</p>
            </div>
            <div>
                <img src="{{ asset('imgs/airobact.png') }}" alt="ถังหมัก AiroBact" class="airobact-hero-img">
            </div>
            <img src="{{ asset('imgs/airobact_app.png') }}" alt="แอป AiroBact" class="airobact-app-img">
            
            <div class="purple-block-container bg-alt-purple mb-5" data-aos="zoom-in-up" data-aos-duration="900">
                <div class="row align-items-center">
                    <div class="col-lg-2 d-none d-lg-block">&nbsp;</div>
                    <div class="col-lg-8 mb-4 mb-lg-0 text-center" data-aos="zoom-in" data-aos-duration="1000">
                        <div class="img-saas-frame p-2 bg-white d-inline-block">
                            <video class="embed-responsive-item video-desktop-width" controls autoplay loop muted playsinline preload="auto">
                                <source src="{{ asset('videos/airobact.mp4') }}" type="video/mp4">
                            </video>
                        </div>
                    </div>
                    <div class="col-lg-12 pl-lg-4 row" data-aos="fade-left" data-aos-duration="1000">
                        <div class="col-12 col-md-6 mb-3 mb-md-0">
                            <div class="saas-card">
                                <div class="icon-purple-pill"><i class="fas fa-recycle"></i></div>
                                <h5 class="text-dark mb-2">ผลิตจากวัสดุ Upcycling & ถังหมักไม่ชื้นแฉะ</h5>
                                <p class="mb-0">โครงสร้างประยุกต์ใช้เสื้อยืดมือสองและตะกร้าผ้า ระบายอากาศรอบทิศทาง (Aerobic Composting) ช่วยลดกลิ่นเหม็นเน่า ย่อยสลายได้รวดเร็ว</p>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="saas-card">
                                <div class="icon-purple-pill"><i class="fas fa-seedling"></i></div>
                                <h5 class="text-dark mb-2">สร้างมูลค่าให้ชุมชน & คำนวณคาร์บอนเครดิต</h5>
                                <p class="mb-0">ย่อยสลายเศษอาหารเป็นปุ๋ยอินทรีย์คุณภาพสูง หรือนำไปเข้ากระบวนการ Pyrolysis ทำ Biochar charger ลดก๊าซเรือนกระจกตามมาตรฐาน T-VER</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION 8: ธนาคารออมทรัพย์ -->
    <section id="sec-saving" class="module-section bg-purple-light-section">
        <div class="container">
            <div class="section-title-block text-center" data-aos="fade-up" data-aos-duration="800">
                <h2>ธนาคารชุมชนออมทรัพย์ & ร้านค้า</h2>
                <p>บริหารสมุดเงินฝากชุมชน สินเชื่อหมุนเวียน และเชื่อมต่อระบบร้านค้าสวัสดิการรับชำระผ่านแอป</p>
            </div>
            <div class="row align-items-center">
                <div class="col-lg-6 order-lg-2 mb-4 mb-lg-0" data-aos="fade-left" data-aos-duration="900">
                    <div class="img-saas-frame">
                        <img src="https://images.unsplash.com/photo-1556740758-90de374c12ad?auto=format&fit=crop&w=1000&q=80" alt="ร้านค้าสวัสดิการชุมชน">
                    </div>
                </div>
                <div class="col-lg-6 order-lg-1 pr-lg-4" data-aos="fade-right" data-aos-duration="900">
                    <div class="saas-card mb-4">
                        <div class="icon-purple-pill"><i class="fas fa-piggy-bank"></i></div>
                        <h5 class="text-dark mb-2">สมุดเงินฝากออมทรัพย์ชุมชน</h5>
                        <p class="mb-0">บันทึกยอดฝาก-ถอนเงินออมทรัพย์ของกลุ่มสัจจะออมทรัพย์ คํานวณดอกเบี้ยและเงินปันผลประจำปี</p>
                    </div>
                    <div class="saas-card">
                        <div class="icon-purple-pill"><i class="fas fa-store"></i></div>
                        <h5 class="text-dark mb-2">สแกนชำระสินค้า ณ ร้านค้าชุมชน</h5>
                        <p class="mb-0">เชื่อมโยงแต้มและเงินฝากขยะ สแกนซื้อสินค้าอุปโภคบริโภค ณ ร้านค้าสวัสดิการชุมชนได้สะดวกสบาย</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- BOTTOM CTA BANNER -->
    <section class="py-5 bg-white-section">
        <div class="container" data-aos="zoom-in-up" data-aos-duration="900">
            <div class="cta-banner-purple text-center">
                <h3 class="font-weight-bold text-white mb-3">พร้อมยกระดับท้องถิ่นสู่ยุคดิจิทัลแล้วหรือยัง?</h3>
                <p class="text-white-50 lead mb-4">เริ่มต้นใช้งานระบบบริหารจัดการ PI-OS เพื่อเพิ่มประสิทธิภาพองค์กรวันนี้</p>
                <a href="{{ route('login') }}" class="btn btn-light text-purple font-weight-bold py-3 px-5 rounded-pill shadow-lg">เข้าสู่ระบบทำงาน</a>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="footer-saas text-center">
        <div class="container">
            <h5 class="font-weight-bold text-white mb-2"><i class="fas fa-cubes text-purple mr-2"></i>PI-OS Platform</h5>
            <p class="small mb-4 text-slate-400">ระบบบริหารจัดการองค์กรปกครองส่วนท้องถิ่นยุคใหม่</p>
            <p class="mb-0 text-white-50 text-xs">&copy; 2026 PI-OS Platform. Developed for Local Administrative Organizations.</p>
        </div>
    </footer>

    <!-- JS Dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({ duration: 800, once: true });
        $(window).on("scroll", function () {
            if ($(window).scrollTop() > 40) {
                $("#mainNavbar").addClass("scrolled");
            } else {
                $("#mainNavbar").removeClass("scrolled");
            }
        });
    </script>
</body>
</html>