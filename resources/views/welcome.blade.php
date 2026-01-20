<!DOCTYPE html>
<html>

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>EnvSoGo</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/bxslider/4.2.12/jquery.bxslider.css">
  <link rel="stylesheet" href="{{ asset('Applight/css/animate.css')}}">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css"
    integrity="sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB" crossorigin="anonymous">
  <link rel="stylesheet" type="text/css" href="{{ asset('Applight/style.css')}}" />
    <link rel="icon" type="image/png" href="{{ asset('logo/ko_envsogo.png') }}">

<link href="https://fonts.googleapis.com/css2?family=Bruno+Ace+SC&family=Sarabun:ital,wght@0,500;0,700;1,400;1,500&display=swap" rel="stylesheet">

<style>
  html, body {
    width: 100%;
    overflow-x: hidden; /* ซ่อนส่วนเกินแนวนอนทั้งหมด */
    margin: 0;
    padding: 0;
}
  .aa{
    background-image: url("{{asset('imgs/iotrash1.png')}}");
    background-repeat: no-repeat;
    background-size: 100% 100%;
  }
  .disabled-section{
    display: none
  }
  .navbar-toggler {
        position: absolute !important; /* ลอยอิสระ */
        top: 15px;    /* ระยะห่างจากขอบบน */
        right: 15px;  /* ระยะห่างจากขอบขวา */
        z-index: 1050; /* ให้แน่ใจว่าอยู่เหนือเลเยอร์อื่นๆ */
        border: 1px solid rgba(255,255,255,0.5); /* (ออพชั่น) ใส่กรอบให้เห็นชัดขึ้น */
    }

    /* ปรับสีไอคอนขีดๆ (Hamburger) ให้ตัดกับพื้นหลัง */
    .navbar-toggler .fa-bars {
        color: white !important; /* เปลี่ยนเป็น black ถ้าพื้นหลังคุณเป็นสีขาว */
        font-size: 1.5rem; /* ขยายขนาดให้กดง่ายขึ้น */
    }

    .navbar {
    position: fixed;
    right: 0;
    left: 0;
    width: 95% !important;
    padding-left: 0;
    padding-right: 0;
    min-height: 50px;
    line-height: 50px;
    background: transparent;
    z-index: 1030;
        min-height: 70px; 
        background: transparent; /* หรือสีที่คุณต้องการ */
    }
  /* กรอบหลักสำหรับคลุมรูปและกล่องข้อความ */
    .img-wrapper-relative {
        position: relative;
        overflow: hidden; /* ซ่อนส่วนที่เลื่อนออกไปนอกกรอบ */
        border-radius: 8px;
        border: 1px solid #ddd;
    }

    /* กล่องข้อความที่ลอยทับ (Sidebar) */
    .floating-sidebar {
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0; /* ให้ความสูงยืดตามรูปภาพ */
        width: 450px; /* กำหนดความกว้างของกล่องข้อความ */
        max-width: 90%; /* กันไม่ให้เกินจอเวลารูปเล็ก */
        background-color: rgba(255, 255, 255, 0.96); /* สีพื้นหลังขาวเกือบทึบ */
        border-left: 2px solid #3498db; /* เส้นขอบซ้ายสีฟ้า */
        padding: 25px;
        overflow-y: auto; /* ถ้าข้อความยาวกว่ารูป ให้เลื่อนขึ้นลงได้ */
        transition: transform 0.4s cubic-bezier(0.77, 0, 0.175, 1);
        z-index: 10;
        box-shadow: -5px 0 15px rgba(0,0,0,0.05);
    }

    /* คลาสสำหรับซ่อนกล่อง (เลื่อนไปทางขวา) */
    .sidebar-hidden {
        transform: translateX(100%);
    }

    /* ปุ่มกดเพื่อแสดง (จะโผล่มาเมื่อซ่อนกล่องไปแล้ว) */
    .btn-show-sidebar {
        position: absolute;
        top: 20px;
        right: 20px;
        z-index: 5;
        display: none; /* ซ่อนไว้ก่อน */
        background: #3498db;
        color: white;
        border: none;
        padding: 8px 15px;
        border-radius: 4px;
        cursor: pointer;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }

    .btn-show-sidebar:hover { background: #2980b9; }

    /* ปุ่มซ่อน (กากบาท หรือ ข้อความ) */
    .btn-hide {
        background: transparent;
        border: 1px solid #ddd;
        color: #777;
        padding: 5px 10px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 0.85rem;
    }
    .btn-hide:hover { background: #f1f1f1; color: #333; }

    /* Flat Design Elements */
    .flat-item {
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        padding: 10px;
        margin-bottom: 8px;
        border-radius: 6px;
    }
    .flat-badge {
        display: inline-block;
        padding: 4px 8px;
        background: #e8f5e9;
        color: #27ae60;
        border-radius: 4px;
        font-size: 12px;
        margin-right: 5px;
        font-weight: 600;
    }

    /* Responsive: บนมือถือ ให้เลิกทำ Overlay แล้วเรียงตามปกติ */
   @media (max-width: 991px) {
    #otp-connect {
    z-index: 999;
    position: absolute;
    margin-top: 0;
    left: 5rem;
    font-size: 4rem;
    font-weight: bolder;
    color: white;
    text-shadow: 10px 5px 2px #000;
    font-family: "Bruno Ace SC", sans-serif;
    font-weight: 600;
    font-style: normal;
}
    #org {
    z-index: 998;
    position: absolute;
    margin-top: 43rem;
    left: 5rem;
    font-size: 3.5rem;
    font-weight: bolder;
    text-shadow: 2px 2px 2px #ffffff;
}
    .main-container {
        /* เปลี่ยน margin-left เป็น 0 ตามที่คุณต้องการ */
        /* และผมแนะนำให้ลด margin-top ลงด้วยเพราะ 16rem (256px) สูงเกินไปสำหรับมือถือ */
        
        margin: 16rem 0 0 0;  /* <-- โค้ดที่คุณต้องการ */
        
        /* หรือถ้าอยากให้สวยบนมือถือ แนะนำให้ใช้แบบบรรทัดล่างนี้แทนครับ */
        /* margin: 4rem auto 0 auto;  */
    }

        .floating-sidebar {
            position: relative; /* ไม่ลอยทับแล้ว */
            width: 100%;
            max-width: 100%;
            height: auto;
            border-left: none;
            border-top: 2px solid #3498db;
            box-shadow: none;
        }
        .btn-hide, .btn-show-sidebar {
            display: none !important; /* ซ่อนปุ่ม Toggle บนมือถือ */
        }
    

  /* CSS เพิ่มเติมสำหรับ Flat Design */
    .flat-section {
        background-color: #f8f9fa; /* สีพื้นหลังเทาอ่อนแบบเรียบ */
    }
    
    .sectioner-header p {
        color: #6c757d;
    }
    .line {
        height: 3px;
        width: 60px;
        background: #3498db; /* สีฟ้าแบบ Flat */
        display: inline-block;
        margin-bottom: 15px;
    }

    /* การ์ดฟีเจอร์แบบเรียบ */
    .flat-feature-box {
        background-color: #ffffff;
        border: 2px solid #e9ecef; /* เส้นขอบสีเทาอ่อนมากๆ แทนเงา */
        border-radius: 8px; /* มุมมนเล็กน้อย (Modern Flat) */
        padding: 15px;
        margin-bottom: 15px;
        transition: all 0.3s ease;
    }

    .flat-feature-box:hover {
        border-color: #3498db; /* เปลี่ยนสีขอบเมื่อ Hover */
        background-color: #f1faff;
    }

    .flat-icon-wrapper {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 50px;
        height: 50px;
        border-radius: 10px;
        margin-right: 15px;
    }
    
    /* สีไอคอนพื้นหลังแบบ Flat */
    .bg-flat-success { background-color: #2ecc71; color: white; }
    .bg-flat-info { background-color: #3498db; color: white; }
    .bg-flat-warning { background-color: #f1c40f; color: white; }
    .bg-flat-secondary { background-color: #95a5a6; color: white; }
    .bg-flat-dark { background-color: #34495e; color: white; }

    .flat-feature-content h6 {
        margin-bottom: 5px;
        font-weight: 700;
        color: #2c3e50;
    }
    .flat-feature-content p {
        margin-bottom: 0;
        font-size: 0.9rem;
        color: #7f8c8d;
    }

    /* ป้ายเทคนิคแบบเรียบ */
    .flat-tech-badge {
        display: inline-block;
        padding: 8px 12px;
        margin-right: 8px;
        margin-bottom: 8px;
        background-color: #ffffff;
        border: 2px solid #2ecc71;
        color: #27ae60;
        border-radius: 25px;
        font-weight: 600;
        font-size: 0.9rem;
    }
  
</style>
</head>

<body>

    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="fas fa-bars"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item"> <a class="nav-link active" href="" data-scroll-nav="0">Home</a> </li>
                    <li class="nav-item"> <a class="nav-link" href="#" data-scroll-nav="1">งานประปา</a> </li>
                    <li class="nav-item"> <a class="nav-link" href="#" data-scroll-nav="2">ธนาคารขยะรีไซเคิล</a> </li>
                    <li class="nav-item"> <a class="nav-link" href="#" data-scroll-nav="3">ธนาคารขยะเปียก</a> </li>
                    <li class="nav-item"> <a class="nav-link" href="#" data-scroll-nav="4">ธนาคารชุมชนออมทรัพย์</a> </li>
                    <li class="nav-item"> <a class="nav-link" href="#" data-scroll-nav="5">จัดเก็บค่าถังขยะรายปี</a> </li>
                    <li class="nav-item"> <a class="nav-link" href="#" data-scroll-nav="7">ติดต่อเรา</a> </li>
                    <li class="nav-item"> <a class="nav-link" href="https://qa.envsogo.site/login">Login</a> </li>
                </ul>
            </div>
        </div>
    </nav>
    <div id="otp-connect">
        <div class="icon-box wow fadeInUp" data-wow-delay="0.2s">
            ENVSOGO
            <hr style="margin-bottom: 3px;margin-top: 3px;">
            <div id="org_addr">พัฒนาชุมชน เชื่อมใจ ให้ใกล้กัน</div>
        </div>
    </div>

    <div id="org">
        <div class="icon-box wow fadeInUp" data-wow-delay="0.6s">
            <div>ระบบบริหารจัดการ</div>
            <div>องค์การบริหารส่วนท้องถิ่น</div>
        </div>
    </div>

    <section class="banner" data-scroll-index="0">
        <div class="banner-overlay">
            <div class="container">
                <div class="main-container centralized">
                    
                    <div class="main-circle">
                        <div class="inner centralized" style="background-image: url('https://qa.envsogo.site/logo/ko_envsogo.png'); background-repeat: no-repeat; background-position: center;">
                        </div>
                    </div>

                    <div class="bubble-container centralized blue-dark">
                        <a href="#">
                            <div class="bubble centralized">
                                <div class="inner centralized">งานประปา</div>
                            </div>
                        </a>
                    </div>

                    <div class="bubble-container centralized blue-light">
                        <div class="bubble centralized">
                            <div class="inner centralized">ตู้รับซื้อ<br>ขยะรีไซเคิล</div>
                        </div>
                    </div>

                    <div class="bubble-container centralized green">
                        <a href="">
                            <div class="bubble centralized">
                                <div class="inner centralized">ธนาคาร<br>ขยะรีไซเคิล</div>
                            </div>
                        </a>
                    </div>

                    <div class="bubble-container centralized orange">
                        <a href="">
                            <div class="bubble centralized">
                                <div class="inner centralized">จัดเก็บ<br>ถังขยะรายปี</div>
                            </div>
                        </a>
                    </div>

                    <div class="bubble-container centralized red">
                        <div class="bubble centralized">
                            <div class="inner centralized">ถังหมัก<br>เศษอาหาร</div>
                        </div>
                    </div>

                    <div class="bubble-container centralized black">
                        <div class="bubble centralized">
                            <div class="inner centralized">ธนาคาร<br>ชุมชน<br>ออมทรัพย์</div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>
    <section class="about section-padding prelative" data-scroll-index="1">
    <div class="container"> <div class="row">
            <div class="col-md-12">
                <div class="sectioner-header text-center mb-4">
                    <h3>ระบบบริหารจัดการงานประปา</h3>
                    <span class="line"></span>
                    <p>Web Application บริหารงานครบวงจร (กดซ่อนเมนูขวาเพื่อดูแผนภาพเต็ม)</p>
                </div>

                <div class="img-wrapper-relative">
                    
                    <img src="https://qa.envsogo.site/imgs/tabwater.png" class="img-fluid w-100 d-block"  style="height: 700px !important" alt="Water System Diagram">

                    <button class="btn-show-sidebar" onclick="toggleSidebar()">
                        <i class="fa fa-info-circle"></i> ดูรายละเอียด
                    </button>

                    <div class="floating-sidebar" id="infoSidebar">
                        
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="m-0 text-dark font-weight-bold">ข้อมูลระบบ</h5>
                            <button class="btn-hide" onclick="toggleSidebar()">
                                ซ่อน <i class="fa fa-chevron-right"></i>
                            </button>
                        </div>

                        <p class="small text-muted mb-3">ระบบบริหารจัดการที่ช่วยลดขั้นตอนการทำงานและเพิ่มประสิทธิภาพการจัดเก็บรายได้</p>

                        <div class="flat-item">
                            <h6 class="text-primary mb-1"><i class="fa fa-chart-line mr-2"></i>Dashboard</h6>
                            <p class="small mb-0 text-secondary">แสดงสถิติงบประมาณ, ปริมาณน้ำ และยอดเงินแบบ Real-time</p>
                        </div>

                        <div class="flat-item">
                            <h6 class="text-info mb-1"><i class="fa fa-users mr-2"></i>ทะเบียนผู้ใช้น้ำ</h6>
                            <p class="small mb-0 text-secondary">จัดการสมาชิก, ข้อมูลมิเตอร์ และกำหนดสิทธิ์เจ้าหน้าที่</p>
                        </div>

                        <div class="flat-item">
                            <h6 class="text-warning mb-1"><i class="fa fa-file-invoice mr-2"></i>การเงิน & ใบแจ้งหนี้</h6>
                            <p class="small mb-0 text-secondary">ออกบิล, รับชำระ, ออกใบเสร็จ และตัดรอบบิลอัตโนมัติ</p>
                        </div>
                        
                        <div class="mt-3">
                            <span class="flat-badge">Web-based</span>
                            <span class="flat-badge">Mobile Support</span>
                            <span class="flat-badge">Secure Auth</span>
                        </div>

                    </div>
                    </div>
                </div>
        </div>
    </div>
</section>
    <section class="feature section-padding" data-scroll-index="2">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="sectioner-header text-center">
                        <h3>ธนาคารขยะรีไซเคิล</h3>
                        <span class="line"></span>
                        <p>Sed quis nisi nisi. Proin consectetur porttitor dui sit amet viverra.</p>
                    </div>
                    <div class="section-content text-center">
                        <div class="row">
                            <div class="col-md-12">
                                <img src="https://qa.envsogo.site/imgs/recycle.png" class="img-fluid w-100" alt="Recycle">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="team section-padding" data-scroll-index="3">
        <div class="container">
            <div class="row aa">
                <div class="col-md-12">
                    <div class="sectioner-header text-center">
                        <h3>ธนาคารขยะเปียก</h3>
                        <span class="line"></span>
                        <p>
                            <span style="font-size: 2rem; font-weight:bold; color: black;">SmartWaste</span> 
                            ถังหมัก AIroTrash และระบบธนาคารขยะเปียกครบวงจร
                        </p>
                    </div>
                    <div class="section-content text-center">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="icon-box wow fadeInUp" data-wow-delay="0.2s">
                                    <img src="https://qa.envsogo.site/imgs/iotrash.png" class="img-fluid" alt="AIroTrash">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="icon-box wow fadeInUp" data-wow-delay="0.4s">
                                    <img src="https://qa.envsogo.site/imgs/iot.png" class="img-fluid" alt="IoT">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="icon-box wow fadeInUp" data-wow-delay="0.6s">
                                    <img src="https://qa.envsogo.site/imgs/iot_web.png" class="img-fluid" alt="Web System">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="testimonial section-padding" data-scroll-index="4">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="sectioner-header text-center">
                        <h3>ธนาคารชุมชนออมทรัพย์</h3>
                        <span class="line"></span>
                        <p><span style="font-size: 1.5rem; font-weight:bold; color: black;">สร้างรายได้ ใช้จ่ายภายในชุมชน</span></p>
                    </div>
                    <div class="section-content text-center">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="icon-box wow fadeInUp" data-wow-delay="0.2s">
                                    <img src="https://qa.envsogo.site/imgs/bookbank.png" class="img-fluid w-100" alt="Bookbank">
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="icon-box wow fadeInUp" data-wow-delay="0.4s">
                                    <img src="https://qa.envsogo.site/imgs/buystore.png" class="img-fluid w-100" alt="Store">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="faq section-padding prelative" data-scroll-index="5">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="sectioner-header text-center">
                        <h3>ค่าจัดการถังขยะรายปี</h3>
                        <span class="line"></span>
                        <p>Sed quis nisi nisi. Proin consectetur porttitor dui sit amet viverra.</p>
                    </div>
                    <div class="section-content">
                        <img src="https://qa.envsogo.site/imgs/map.png" class="img-fluid w-100" alt="Map">
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="contact section-padding" data-scroll-index="7">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="sectioner-header text-center">
                        <h3>ติดต่อเรา</h3>
                        <span class="line"></span>
                        <p>Sed quis nisi nisi. Proin consectetur porttitor dui sit amet viverra.</p>
                    </div>
                    <div class="section-content">
                        <div class="row">
                            <div class="col-sm-12 col-md-12 col-lg-8">
                                <form id="contact_form" name="aa" action="https://qa.envsogo.site/login" method="POST">
                                    <input type="hidden" name="_token" value="GFH9pFIiiChM3gI3WyAQdYHyEktOPlpU2YlCMBet">
                                    <div class="row">
                                        <div class="col">
                                            <input type="text" id="your_name" class="form-input w-100" name="username" placeholder="Username" required="">
                                        </div>
                                        <div class="col">
                                            <input type="password" id="password" class="form-input w-100" name="password" placeholder="Password" required="">
                                        </div>
                                    </div>
                                    <button class="btn-grad w-100 text-uppercase" type="submit" name="buttond" style="margin-top:15px;">submit</button>
                                </form>
                            </div>
                            <div class="col-sm-12 col-md-12 col-lg-4">
                                <div class="contact-info white">
                                    <div class="contact-item media"> <i class="fa fa-map-marker-alt media-left media-right-margin"></i>
                                        <div class="media-body">
                                            <p class="text-uppercase">Address</p>
                                            <p class="text-uppercase">New Delhi, India</p>
                                        </div>
                                    </div>
                                    <div class="contact-item media"> <i class="fa fa-mobile media-left media-right-margin"></i>
                                        <div class="media-body">
                                            <p class="text-uppercase">Phone</p>
                                            <p class="text-uppercase"><a class="text-white" href="tel:+15173977100">009900990099</a> </p>
                                        </div>
                                    </div>
                                    <div class="contact-item media"> <i class="fa fa-envelope media-left media-right-margin"></i>
                                        <div class="media-body">
                                            <p class="text-uppercase">E-mail</p>
                                            <p class="text-uppercase"><a class="text-white" href="mailto:yogeshsingh.now@gmail.com">yogeshsingh.now@gmail.com</a> </p>
                                        </div>
                                    </div>
                                    <div class="contact-item media"> <i class="fa fa-clock media-left media-right-margin"></i>
                                        <div class="media-body">
                                            <p class="text-uppercase">Working Hours</p>
                                            <p class="text-uppercase">Mon-Fri 9.00 AM to 5.00PM.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <footer class="footer-copy">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <p>2018 © Applight. Website Designed by <a href="http://w3Template.com" target="_blank" rel="dofollow">W3 Template</a></p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js" integrity="sha384-smHYKdLADwkXOn1EmN1qk/HfnUcbVRZyYmZ4qpPea6sjB/pTJ0euyQp0Mk8ck+5T" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/bxslider/4.2.12/jquery.bxslider.min.js"></script>
    <script src="https://qa.envsogo.site/Applight/js/scrollIt.min.js"></script>
    <script src="https://qa.envsogo.site/Applight/js/wow.min.js"></script>
    
    <script>
        wow = new WOW();
        wow.init();
        $(document).ready(function(e) {

            $('#video-icon').on('click', function(e) {
                e.preventDefault();
                $('.video-popup').css('display', 'flex');
                $('.iframe-src').slideDown();
            });
            $('.video-popup').on('click', function(e) {
                var $target = e.target.nodeName;
                var video_src = $(this).find('iframe').attr('src');
                if ($target != 'IFRAME') {
                    $('.video-popup').fadeOut();
                    $('.iframe-src').slideUp();
                    $('.video-popup iframe').attr('src', " ");
                    $('.video-popup iframe').attr('src', video_src);
                }
            });

            $('.slider').bxSlider({
                pager: false
            });
        });

        $(window).on("scroll", function() {
            var bodyScroll = $(window).scrollTop(),
                navbar = $(".navbar");

            if (bodyScroll > 50) {
                $('.navbar-logo img').attr('src', 'images/logo-black.png');
                navbar.addClass("nav-scroll");
            } else {
                $('.navbar-logo img').attr('src', 'images/logo.png');
                navbar.removeClass("nav-scroll");
            }
        });

        $(window).on("load", function() {
            var bodyScroll = $(window).scrollTop(),
                navbar = $(".navbar");

            if (bodyScroll > 50) {
                $('.navbar-logo img').attr('src', 'images/logo-black.png');
                navbar.addClass("nav-scroll");
            } else {
                $('.navbar-logo img').attr('src', 'images/logo-white.png');
                navbar.removeClass("nav-scroll");
            }

            $.scrollIt({
                easing: 'swing', // the easing function for animation
                scrollTime: 900, // how long (in ms) the animation takes
                activeClass: 'active', // class given to the active nav element
                onPageChange: null, // function(pageIndex) that is called when page is changed
                topOffset: -63
            });
        });
    </script>
    
    <script>
        $(document).ready(function() {
            var bubbleList = $('.bubble-container');
            const bubbleCount = bubbleList.length;
            const degStep = 180 / (bubbleCount - 1);

            $('.bubble-container').each((index) => {
                const deg = index * degStep;
                const invertDeg = deg * -1;

                $(bubbleList[index]).css('transform', `rotate(${deg}deg)`);
                $(bubbleList[index]).css('opacity', `1`);
                $(bubbleList[index]).find('.bubble').css('transform', `rotate(${invertDeg}deg)`);
            })
        })
        function toggleSidebar() {
        var sidebar = document.getElementById('infoSidebar');
        var showBtn = document.querySelector('.btn-show-sidebar');
        
        // สลับ Class เพื่อเลื่อนกล่อง
        sidebar.classList.toggle('sidebar-hidden');
        
        // จัดการการแสดงปุ่ม "ดูรายละเอียด"
        if (sidebar.classList.contains('sidebar-hidden')) {
            showBtn.style.display = 'block';
        } else {
            showBtn.style.display = 'none';
        }
    }
    </script>
    
    {{-- <script defer="" src="https://static.cloudflareinsights.com/beacon.min.js/vcd15cbe7772f49c399c6a5babf22c1241717689176015" integrity="sha512-ZpsOmlRQV6y907TI0dKBHq9Md29nnaEIPlkf84rnaERnq6zvWvPUqr2ft8M1aS28oN72PdrCzSjY4U6VaAw1EQ==" data-cf-beacon="{&quot;version&quot;:&quot;2024.11.0&quot;,&quot;token&quot;:&quot;38d411edd0bb489997cfe0a5405644f9&quot;,&quot;r&quot;:1,&quot;server_timing&quot;:{&quot;name&quot;:{&quot;cfCacheStatus&quot;:true,&quot;cfEdge&quot;:true,&quot;cfExtPri&quot;:true,&quot;cfL4&quot;:true,&quot;cfOrigin&quot;:true,&quot;cfSpeedBrain&quot;:true},&quot;location_startswith&quot;:null}}" crossorigin="anonymous"></script> --}}

</body>

</html>