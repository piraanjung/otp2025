<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KU:PI-OS - Access Menu</title>

    <!-- Fonts & Icons -->
    <link
        href="https://fonts.googleapis.com/css2?family=Bruno+Ace+SC&family=Sarabun:ital,wght@0,400;0,600;0,700;0,800;1,400&display=swap"
        rel="stylesheet">
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>

    <!-- Soft UI Dashboard CSS -->
    <link id="pagestyle" href="{{ asset('soft-ui/assets/css/soft-ui-dashboard.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('Applight/css/animate.css')}}">

    <style>
        body {
            font-family: "Sarabun", sans-serif;
            background: linear-gradient(135deg, #e2e8f0 0%, #f8fafc 100%);
            min-height: 100vh;
            overflow: hidden;
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            /* ขยับคอนเทนเนอร์หลักชิดขวา */
        }

        .centralized {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Container หลัก จัดวางวงกลมชิดขอบฝั่งขวา */
        .main-container {
            position: relative;
            width: 420px;
            height: 420px;
            margin-right: 16rem;
            /* เว้นระยะห่างจากขอบขวา */
        }

        /* วงกลมหลักตรงกลาง */
        .main-container .main-circle {
            border: 8px solid rgba(255, 255, 255, 0.85);
            border-radius: 50%;
            box-sizing: border-box;
            padding: 20px;
            height: 100%;
            width: 100%;
            position: absolute;
            top: 0;
            left: 0;
            box-shadow: 0 20px 30px rgba(0, 0, 0, 0.06);
            background: rgba(255, 255, 255, 0.45);
            backdrop-filter: blur(12px);
            z-index: 1;
        }

        .main-container .main-circle .inner {
            background: #ffffff;
            border-radius: 50%;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.05), 0 10px 20px rgba(0, 0, 0, 0.08);
            box-sizing: border-box;
            height: 100%;
            width: 100%;
            padding: 40px;
            overflow: hidden;
        }



        /* โครงสร้างก้านหมุนเมนูวงกลมย่อย */
        .main-container .bubble-container {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            transition: transform 0.8s ease-in-out, opacity 1s ease;
            pointer-events: none;
            z-index: 2;
        }



        .main-container .bubble-container a {
            text-decoration: none;
            pointer-events: auto;
        }

        /* เมนูวงกลมย่อย */
        .main-container .bubble-container .bubble {
            border-radius: 50%;
            /* border: 10px solid  #141727; */
            box-sizing: border-box;
            position: absolute;
            height: 160px;
            width: 160px;
            top: -160.5px;
            left: 142.5px;
            /* (420px / 2) - (135px / 2) */
            transition: all 0.4s ease;
            text-align: center;
        }

        .main-container .bubble-container .bubble .inner {
            /* background: #ffffff; */
            border-radius: 50%;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.12);
            box-sizing: border-box;
            height: 100%;
            width: 100%;
            font-size: 20px;
            font-weight: 700;
            line-height: 1.25;
            color: #feffff;
            border: 3.5px solid #ffffff;
            transition: all 0.3s ease;
            flex-direction: column;
            padding: 10px;
        }

        .bubble .inner:hover {
            transform: scale(1.15) !important;
            box-shadow: 0 20px 27px rgba(0, 0, 0, 0.2) !important;
            color: #252f40;
        }

        /* Soft UI Gradients */
        .main-container .bubble-container.blue .bubble {
            background: linear-gradient(310deg, #2152ff 0%, #21d4fd 100%);
        }

        .main-container .bubble-container.cyan .bubble {
            background: linear-gradient(310deg, #17ad37 0%, #98ec2d 100%);
        }

        .main-container .bubble-container.sage .bubble {
            background: linear-gradient(310deg, #3a416f 0%, #141727 100%);
        }

        .main-container .bubble-container.green .bubble {
            background: linear-gradient(310deg, #17ad37 0%, #98ec2d 100%);
        }

        .main-container .bubble-container.orange .bubble {
            background: linear-gradient(310deg, #fbcf33 0%, #f53939 100%);
        }

        .main-container .bubble-container.purple .bubble {
            background: linear-gradient(310deg, #7928ca 0%, #b07219 100%);
        }

        .main-container .bubble-container.dark-green .bubble {
            background: linear-gradient(310deg, #097648 0%, #22c55e 100%);
        }

        .main-container .bubble-container.gold .bubble {
            background: linear-gradient(310deg, #f59e0b 0%, #fbbf24 100%);
        }

        .main-container .bubble-container.admin .bubble {
            background: linear-gradient(310deg, #ea0606 0%, #ff667c 100%);
        }

        /* ปุ่มออกจากระบบ / Brand Top-Left */
        #otp-connect {
            z-index: 999;
            position: fixed;
            top: 2.5rem;
            left: 3rem;
            font-family: "Bruno Ace SC", sans-serif;
        }

        /* ข้อความองค์กรลอยกลางอากาศ (ไร้กรอบพื้นหลัง) */
        #org {
            z-index: 998;
            position: fixed;
            bottom: 3.5rem;
            left: 3rem;
            pointer-events: none;
        }

        /* คืนค่าให้ปุ่มภายใน #org ให้สามารถกดคลิกได้ตามปกติ */
        #org a,
        #org button {
            pointer-events: auto !important;
        }

        .org-type-floating {
            font-size: 2rem;
            font-weight: 700;
            color: #5974a2;
            letter-spacing: 1px;
            text-shadow: 0 2px 10px rgba(255, 255, 255, 0.8);
        }

        .org-name-floating {
            font-size: 6.8rem;
            font-weight: 800;
            color: #1e293b;
            line-height: 1.15;
            margin: 0.2rem 0;
            text-shadow: 0 4px 20px rgba(255, 255, 255, 0.9), 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .org-address-floating {
            font-size: 2rem;
            font-weight: 600;
            color: #64748b;
            text-shadow: 0 2px 10px rgba(255, 255, 255, 0.8);
        }

        .a-disabled {
            opacity: 0.3 !important;
            filter: grayscale(100%);
        }

        .a-disabled a {
            pointer-events: none !important;
        }

        /* =========================================================
           Responsive มือถือ & แท็บเล็ต
           ========================================================= */

        /* หน้าจอขนาดกลาง/โน้ตบุ๊กเล็ก */
        @media (max-width: 1200px) and (min-width: 768px) {
            .main-container {
                margin-right: 4rem;
                width: 360px;
                height: 360px;
            }

            .main-container .bubble-container .bubble {
                height: 115px;
                width: 115px;
                top: -57.5px;
                left: 122.5px;
            }

            .org-name-floating {
                font-size: 2.8rem;
            }
        }

        /* หน้าจอมือถือ (< 768px) */
        @media (max-width: 767px) {
            body {
                overflow-y: auto;
                padding: 1.5rem 1rem 2.5rem 1rem;
                display: block;
            }

            #otp-connect {
                position: static !important;
                margin-bottom: 1.5rem;
            }

            #org {
                position: static !important;
                margin-bottom: 1.5rem;
            }

            .org-type-floating {
                font-size: 1.1rem;
            }

            .org-name-floating {
                font-size: 2.2rem;
            }

            .org-address-floating {
                font-size: 1rem;
            }

            .main-container {
                width: 100% !important;
                height: auto !important;
                margin: 0 !important;
            }

            .main-circle {
                display: none !important;
            }

            .mobile-grid-wrapper {
                display: grid !important;
                grid-template-columns: repeat(2, 1fr);
                gap: 12px;
            }

            .main-container .bubble-container {
                position: static !important;
                height: auto !important;
                width: 100% !important;
                opacity: 1 !important;
                transform: none !important;
            }

            .main-container .bubble-container .bubble {
                position: static !important;
                height: 195px !important;
                width: 100% !important;
                border-radius: 14px !important;
                padding: 3px;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
            }

            .main-container .bubble-container .bubble .inner {
                border-radius: 12px !important;
                font-size: 13px !important;
                transform: none !important;
            }
        }
    </style>
</head>

<body>

    <!-- Header / Brand Section -->
    <div id="otp-connect" class="wow fadeInDown" data-wow-delay="0.1s">
        <div class="card card-body blur shadow-blur p-3 d-flex flex-row align-items-center gap-3">
            <div>
                <h3 class="font-weight-bolder text-gradient text-primary mb-0">{{ $orgInfos['org_code'] }}:PIOS</h3>
            </div>
            <form action="{{ route('logout') }}" method="POST" class="m-0">
                @csrf
                <button type="submit" class="btn btn-sm bg-gradient-danger mb-0 px-3">
                    <i class="fas fa-sign-out-alt me-1"></i> ออกจากระบบ
                </button>
            </form>
        </div>
    </div>

    <!-- ข้อความเทศบาลตัวหนังสือลอยกลางอากาศ (ไร้กรอบพื้นหลัง) -->
    <!-- ข้อความเทศบาลตัวหนังสือลอยกลางอากาศ -->
    <!-- ข้อความเทศบาลตัวหนังสือลอยกลางอากาศ -->
    <div id="org" class="wow fadeInUp" data-wow-delay="0.3s">
        <div class="d-flex align-items-center gap-2">
            <span class="org-type-floating">{{ $orgInfos['org_type_name'] }}</span>

            <!-- ปุ่มสลับ อปท. สำหรับ Super Admin (เพิ่ม style="pointer-events: auto;") -->
            @hasrole('Super Admin')
            <a href="{{ route('admin.org_selector') }}" class="btn btn-xs bg-gradient-info mb-0 rounded-pill px-3"
                style="pointer-events: auto;">
                <i class="fas fa-exchange-alt me-1"></i> สลับ อปท.
            </a>
            <a href="{{ route('admin.reset_org_context') }}"
                class="btn btn-xs bg-gradient-secondary mb-0 rounded-pill px-2" style="pointer-events: auto;"
                title="ออกจากสิทธิ์การจัดการ">
                <i class="fas fa-power-off"></i>
            </a>
            @endhasrole
        </div>

        <div class="org-name-floating">{{ $orgInfos['org_name'] }}</div>
        <div class="org-address-floating">
            <i class="fas fa-map-marker-alt me-1 text-primary"></i> ตำบล{{ $orgInfos['org_tambon'] }}
            อำเภอ{{ $orgInfos['org_district'] }} จังหวัด{{ $orgInfos['org_province'] }}
        </div>
    </div>

    <!-- Central Circular Menu Wheel (ขยับชิดฝั่งขวา) -->
    <div class="main-container centralized">
        <!-- วงกลมหลักตรงกลาง -->
        <div class="main-circle">
            <div class="inner centralized">
                <img src="{{ asset('logo/' . $orgInfos['org_logo_img']) }}" class="img-fluid" alt="Organization Logo">
            </div>
        </div>

        <!-- Wrapper รวมเมนูโมดูล -->
        <div class="mobile-grid-wrapper">

            <!-- 1. งานประปา -->
            {{-- {{ auth()->user()->can('access tabwater') || auth()->user()->hasRole('Super Admin| Admin') ? '' :
            'a-disabled' }} --}}
            <div class="bubble-container blue ">
                <a href="{{  route('dashboard') }}">
                    <div class="bubble centralized">
                        <div class="inner centralized">
                            <i class="fas fa-tint mb-1 text-primary fs-5"></i>
                            <span>งานประปา</span>
                        </div>
                    </div>
                </a>
            </div>

            <!-- 4. ธนาคารขยะรีไซเคิล -->
            <div class="bubble-container green ">
                <a href="{{ route('keptkayas.dashboard', 'recycle') }}">
                    <div class="bubble centralized">
                        <div class="inner centralized">
                            <i class="fas fa-recycle mb-1 text-success fs-5"></i>
                            <span>ธนาคาร<br>ขยะรีไซเคิล</span>
                        </div>
                    </div>
                </a>
            </div>

            <!-- 7. ถังขยะเปียกจากครัวเรือน -->
            <div class="bubble-container green">
                <a
                    href="{{ auth()->user()->can('access food waste') || auth()->user()->hasRole('Super Admin') ? route('foodwaste.executive_dashboard') : 'javascript:void(0)' }}">
                    <div class="bubble centralized">
                        <div class="inner centralized">
                            <i class="fas fa-leaf mb-1 text-success fs-5"></i>
                            <span>ถังขยะเปียก<br>จากครัวเรือน</span>
                        </div>
                    </div>
                </a>
            </div>

            <!-- 2. ตู้คืนขวดอัตโนมัติ -->
            <div class="bubble-container green">
                <a href="{{ route('keptkayas.kiosks.dashboard') }}">
                    <div class="bubble centralized">
                        <div class="inner centralized">
                            <i class="fas fa-robot mb-1 text-success fs-5"></i>
                            <span>ตู้คืนขวด<br>อัตโนมัติ</span>
                        </div>
                    </div>
                </a>
            </div>

            <!-- 5. ค่าจัดการถังขยะรายปี -->
            <div class="bubble-container dark-green ">
                <a href="{{ route('annual_trash.index')  }}">
                    <div class="bubble centralized">
                        <div class="inner centralized">
                            <i class="fas fa-trash-alt mb-1 text-warning fs-5"></i>
                            <span>ค่าจัดการ<br>ถังขยะรายปี</span>
                        </div>
                    </div>
                </a>
            </div>








            <!-- 6. กองทุนฌาปนกิจสงเคราะห์ -->
            <div class="bubble-container purple">
                <a href="{{ route('admin.welfare.dashboard') }}">
                    <div class="bubble centralized">
                        <div class="inner centralized">
                            <i class="fas fa-hands-helping mb-1 fs-5" style="color: #7928ca;"></i>
                            <span>กองทุน<br>ฌาปนกิจสงเคราะห์</span>
                        </div>
                    </div>
                </a>
            </div>





            <!-- 9. ผู้ดูแลระบบ -->
            <div class="bubble-container admin ">
                <a href="{{ route('superadmin.dashboard') }}">
                    <div class="bubble centralized">
                        <div class="inner centralized">
                            <i class="fas fa-user-shield mb-1 text-danger fs-5"></i>
                            <span>ตั้งค่าระบบ</span>
                        </div>
                    </div>
                </a>
            </div>
            {{-- <div class="bubble-container">
                <a href="javascript:void(0)">
                    <div class="bubble centralized" style="opacity: 0">
                        <div class="inner centralized">
                            <i class="fas fa-user-shield mb-1 text-danger fs-5"></i>
                            <span></span>
                        </div>
                    </div>
                </a>
            </div>

            <div class="bubble-container">
                <a href="javascript:void(0)">
                    <div class="bubble centralized" style="opacity: 0">
                        <div class="inner centralized">
                            <i class="fas fa-user-shield mb-1 text-danger fs-5"></i>
                            <span></span>
                        </div>
                    </div>
                </a>
            </div> --}}
            <!-- 3. คลังพัสดุ -->
            <div class="bubble-container sage ">
                <a href="{{ route('inventory.dashboard') }}">
                    <div class="bubble centralized">
                        <div class="inner centralized">
                            <i class="fas fa-boxes mb-1 text-dark fs-5"></i>
                            <span>คลังพัสดุ</span>
                        </div>
                    </div>
                </a>
            </div>


        </div>

    </div>

    <!-- Core JS Files -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="{{ asset('soft-ui/assets/js/core/popper.min.js') }}"></script>
    <script src="{{ asset('soft-ui/assets/js/core/bootstrap.min.js') }}"></script>
    <script src="{{ asset('Applight/js/wow.min.js') }}"></script>

    <!-- Script คำนวณองศากระจายแบบวงกลม 360 องศารอบโลโก้ -->
    <script>
        new WOW().init();

        $(document).ready(function () {
            function renderWheelLayout() {
                if ($(window).width() >= 768) {
                    var bubbleList = $('.bubble-container');
                    const bubbleCount = bubbleList.length;

                    const startDeg = -90; // เริ่มจากด้านบนสุด
                    const degStep = 360 / bubbleCount;

                    $('.bubble-container').each((index) => {
                        const deg = startDeg + (index * degStep);
                        const invertDeg = deg * -1;

                        $(bubbleList[index]).css({
                            'transform': `rotate(${deg}deg)`,
                            'opacity': '1'
                        });
                        $(bubbleList[index]).find('.bubble').css('transform', `rotate(${invertDeg}deg)`);
                    });
                }
            }

            renderWheelLayout();

            $(window).on('resize', function () {
                renderWheelLayout();
            });
        });
    </script>
</body>

</html>