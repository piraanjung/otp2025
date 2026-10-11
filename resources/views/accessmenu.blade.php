<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KU:PI-OS</title>


    <link rel="stylesheet" href="https://cdn.jsdelivr.net/bxslider/4.2.12/jquery.bxslider.css">


    <link
        href="https://fonts.googleapis.com/css2?family=Bruno+Ace+SC&family=Sarabun:ital,wght@0,500;0,700;1,400;1,500&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('Applight/css/animate.css')}}">

    <style>
        @keyframes rotateMain {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        @keyframes rotateInner {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(-360deg);
            }
        }

        body {
            font-family: "Sarabun", sans-serif;
            font-weight: 700;
            font-style: normal;
            background: linear-gradient(to right, #8e9eab, #eef2f3);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-wrap: wrap;
            gap: 30px;
        }

        .centralized {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .centralized a {
            color: #000
        }

        .main-container {
            /* border: solid 1px #000; */
            margin: 16rem 0 0 16rem;
            /* padding-top: 300px; */
            /* height: 350px; */
            width: 100%;
            position: relative;
            `
        }

        .main-container .main-circle {
            border: 6px solid #bcbcbc;
            border-radius: 100%;
            box-sizing: border-box;
            padding: 24px;
            height: 500px;
            width: 500px;
            position: relative;
        }

        .main-container .main-circle .inner {
            background: #ededed;
            border: 4px solid #e3e3e3;
            border-radius: 100%;
            box-shadow: 4px 5px 5px 0px rgba(0, 0, 0, 0.2);
            box-sizing: border-box;
            color: #8dc03f;
            font-size: 34px;
            height: 100%;
            line-height: 1.5;
            text-align: center;
            width: 100%;
            text-shadow: 1px 1px 1px #000;

            text-align: center
        }

        .main-container .bubble-container {
            border: 6px;
            box-sizing: border-box;
            height: 320px;
            width: 320px;
            position: absolute;
            opacity: 0;
            transform: rotate(0deg);
            transition: transform ease-in 0.7s, opacity ease 1s;
        }

        .main-container .bubble-container .pointer {
            background: #fff;
            border: 4px solid #bcbcbc;
            border-radius: 100%;
            box-sizing: border-box;
            position: absolute;
            left: calc(50% -117px);
            height: 54px;
            top: calc(50% - 167px);
            width: 54px;
        }

        .main-container .bubble-container .pointer .arrow {
            width: 0;
            height: 0;
            border-style: solid;
            border-width: 7px 14px 7px 0;
            border-color: transparent #bcbcbc transparent transparent;
            position: absolute;
            left: -15px;
            top: 5.52px;
        }

        .main-container .bubble-container .pointer .inner {
            background: #000;
            border-radius: 100%;
            box-sizing: border-box;
            height: 14px;
            width: 14px;
        }

        /* ปรับแต่งขนาดและระยะห่างของฟองวงกลมย่อย */
        .main-container .bubble-container .bubble {
            border-radius: 100%;
            box-sizing: border-box;
            position: absolute;

            /* 1. ย่อขนาดวงกลมย่อยให้พอดี ไม่เบียดกัน */
            height: 172px;
            width: 172px;

            /* 2. ดันวงกลมย่อยให้ออกห่างจากรัศมีวงกลมใหญ่กลาง (ขยับออกไปทางซ้าย/บน) */
            top: -95px;
            left: -300px;

            transition: all ease 0.8s;
            text-align: center;
        }

        /* ปรับขนาดกล่องเนื้อหาภายในวงกลม */
        .main-container .bubble-container .bubble .inner {
            background: #fff;
            border-radius: 100%;
            box-shadow: 4px 5px 5px 0px rgba(0, 0, 0, 0.2);
            box-sizing: border-box;
            height: 135px;
            width: 135px;
            overflow: hidden;
            font-size: 20px;
            /* ปรับขนาดฟอนต์ให้สมดุลกับขนาดวงกลมใหม่ */
            line-height: 1.3;
        }

        .bubble .inner:hover {
            transition: all ease 0.3s;
            transform: scale(1.1) !important;
        }


        /* 1. งานประปา - สีฟ้าคราม (Ocean Blue) */
.main-container .bubble-container.red .bubble,
.main-container .bubble-container.red .pointer .inner {
    background: #4A90E2;
}

/* 2. ตู้คืนขวดอัตโนมัติ - สีฟ้าเทอร์ควอยซ์ (Turquoise) */
.main-container .bubble-container.cyan .bubble,
.main-container .bubble-container.cyan .pointer .inner {
    background: #20B2AA;
}

/* 3. คลังพัสดุ - สีเขียวพก/มะกอกอ่อน (Sage Green) */
.main-container .bubble-container.sage .bubble,
.main-container .bubble-container.sage .pointer .inner {
    background: #5B8C5A;
}

/* 4. ธนาคารขยะรีไซเคิล - สีเขียวใบไม้สดใส (Soft Leaf Green) */
.main-container .bubble-container.green .bubble,
.main-container .bubble-container.green .pointer .inner {
    background: #62B865;
}

/* 5. ค่าจัดการถังขยะรายปี - สีส้มอบอุ่น/พีช (Soft Amber / Peach) */
.main-container .bubble-container.orange .bubble,
.main-container .bubble-container.orange .pointer .inner {
    background: #E88848;
}

/* 6. กองทุนฌาปนกิจ - สีม่วงพาสเทลเทา (Muted Slate Violet) */
.main-container .bubble-container.black .bubble,
.main-container .bubble-container.black .pointer .inner {
    background: #6C5B7B;
}

/* 7. ถังขยะเปียกจากครัวเรือน - สีเขียวไผ่/โอลีฟ (Olive Green) */
.main-container .bubble-container.blue-dark .bubble,
.main-container .bubble-container.blue-dark .pointer .inner {
    background: #739E82;
}

/* 8. ธนาคารออมทรัพย์ - สีทองอมส้มอ่อน (Soft Warm Gold) */
.main-container .bubble-container.gold .bubble,
.main-container .bubble-container.gold .pointer .inner {
    background: #D4A359;
}

/* 9. ผู้ดูแลระบบ - สีน้ำเงินเกรย์/คอร์นฟลาวเวอร์ (Cornflower Steel Blue) */
.main-container .bubble-container.blue-light .bubble,
.main-container .bubble-container.blue-light .pointer .inner {
    background: #5C7AEA;
}


        #org {
            z-index: 998;
            position: absolute;
            margin-top: 30rem;
            left: 5rem;
            font-size: 4.5rem;
            font-weight: bolder;
            text-shadow: 2px 2px 2px #ffffff;

        }

        #org_addr {
            font-size: 2rem;
            text-align: center;
            color: black;

            text-shadow: 2px 2px 2px #ffffff;
        }

        #org_addr2 {
            font-size: 1.8rem;
            text-align: center;
            color: black;

            text-shadow: 2px 2px 2px #ffffff;
        }

        #otp-connect {
            z-index: 999;
            position: absolute;
            top: 0;
            margin-top: 2rem;
            left: 5rem;
            font-size: 4rem;
            /* font-weight: bolder; */
            color: white;
            text-shadow: 10px 5px 2px #000;
            font-family: "Bruno Ace SC", sans-serif;
            font-weight: 800;
            font-style: normal;
        }

        .a-disbled {
            /* display: none */
            opacity: 0.1 !important;
            cursor: not-allowed;
        }
    </style>

</head>

<body>
    <div id="otp-connect">
        <div class="icon-box wow fadeInUp" data-wow-delay="0.2s">
            {{$orgInfos['org_code']}}:PIOS

            <hr style="margin-bottom: 3px;margin-top: 3px;">
            <form action="{{ route('logout') }}">
                @csrf
                <input type="submit" style="border-radius: 10px " value="ออกจากระบบ">

            </form>
            {{-- <div id="org_addr">พัฒนาชุมชน เชื่อมใจ ให้ใกล้กัน</div> --}}
        </div>
    </div>
    <div id="org" class="icon-box wow fadeInUp" data-wow-delay="0.4s">
        <div style="font-size: 3.5rem">{{$orgInfos['org_type_name']}}</div>
        <div>{{$orgInfos['org_name']}}</div>
        <hr style="margin-bottom: 3px;margin-top: 3px;">
        <div id="org_addr2">ตำบล{{$orgInfos['org_tambon']}} อำเภอ{{$orgInfos['org_district']}}
            จังหวัด{{$orgInfos['org_province']}}</div>
    </div>

    <div class="main-container centralized ">

        <div class="main-circle">
            <div class="inner centralized">
                <img src="{{asset('logo/' . $orgInfos['org_logo_img'])}}" width="100%" height="100%">
                {{-- ระบบบริหารจัดการ --}}
            </div>
        </div>
        <div
            class="bubble-container centralized  red {{auth()->user()->can('access tabwater') | auth()->user()->hasRole('Super Admin') ? '' : 'a-disbled'}}">
            <a
                href="{{auth()->user()->can('access tabwater') | auth()->user()->hasRole('Super Admin') ? route('dashboard') : '#'}}">
                <div class="bubble centralized">
                    <div class="inner centralized">
                        งานประปา
                    </div>
                </div>
            </a>
        </div>
        <div
            class="bubble-container centralized  red {{auth()->user()->can('access tabwater') | auth()->user()->hasRole('Super Admin') ? '' : 'a-disbled'}}">
            <a
                href="{{auth()->user()->can('access tabwater') | auth()->user()->hasRole('Super Admin') ? route('keptkayas.kiosks.index') : '#'}}">
                <div class="bubble centralized">
                    <div class="inner centralized">
                        ตู้คืนขวดอัตโนมัติ
                    </div>
                </div>
            </a>
        </div>

        <div
            class="bubble-container centralized  red {{auth()->user()->can('access tabwater') | auth()->user()->hasRole('Super Admin') ? '' : 'a-disbled'}}">
            <a
                href="{{auth()->user()->can('access tabwater') | auth()->user()->hasRole('Super Admin') ? route('inventory.dashboard') : '#'}}">
                <div class="bubble centralized">
                    <div class="inner centralized">
                        คลังพัสดุ
                    </div>
                </div>
            </a>
        </div>


        <div
            class="bubble-container centralized green {{auth()->user()->can('access recycle bank') || auth()->user()->hasRole('Super Admin|Recycle Bank Staff| Admin') ? '' : 'a-disbled'}}">
            <a href="{{route('keptkayas.dashboard', 'recycle')}}">
                <div class="bubble centralized">
                    <div class="inner centralized">
                        ธนาคาร<br>ขยะรีไซเคิล
                    </div>
                </div>
            </a>
        </div>
        <div
            class="bubble-container centralized orange {{auth()->user()->can('access recycle bank') || auth()->user()->hasRole('Super Admin | Admin |Annual Trash Staff') ? '' : 'a-disbled'}}">
            <a
                href="{{auth()->user()->can('access recycle bank') || auth()->user()->hasRole('Super Admin | Admin |Annual Trash Staff') ? route('annual_trash.index') : 'javascript:void(0)'}}">

                <div class="bubble centralized">
                    <div class="inner centralized">
                        ค่าจัดการ<br>ถังขยะรายปี
                    </div>
                </div>
            </a>

        </div>
        <div class="bubble-container centralized black ">
            <a href="#">
                <div class="bubble centralized">
                    <div class="inner centralized">
                        กองทุน<br>ฌาปณกิจ
                    </div>
                </div>
            </a>
        </div>
        <div
            class="bubble-container centralized  blue-dark">
            <a
                href="{{auth()->user()->can('access food waste') || auth()->user()->hasRole('Super Admin') ? route('foodwaste.executive_dashboard') : 'javascript:void(0)'}}">
                <div class="bubble centralized">
                    <div class="inner centralized">
                        ถังขยะเปียกจากครัวเรือน
                    </div>
                </div>
            </a>
        </div>
        <div
            class="bubble-container centralized black ">
            <a href="#">
                <div class="bubble centralized">
                    <div class="inner centralized">
                        ธนาคาร<br>ออมทรัพย์
                    </div>
                </div>
            </a>
        </div>
        {{-- {{dd(auth()->user()->guard_name) }} --}}
        <div
            class="bubble-container centralized blue-light {{auth()->user()->hasRole('Super Admin|Admin') ? '' : 'a-disbled'}}">
            <a
                href="{{auth()->user()->hasRole('Super Admin|Admin') ? route('superadmin.dashboard') : 'javascript:void(0)'}}">
                <div class="bubble centralized">
                    <div class="inner centralized">
                        ผู้ดูแลระบบ
                    </div>
                </div>
            </a>
        </div>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"
        integrity="sha384-smHYKdLADwkXOn1EmN1qk/HfnUcbVRZyYmZ4qpPea6sjB/pTJ0euyQp0Mk8ck+5T"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/bxslider/4.2.12/jquery.bxslider.min.js"></script>
    <!-- scrollIt js -->
    <script src="{{ asset('Applight/js/scrollIt.min.js')}}"></script>
    <script src="{{ asset('Applight/js/wow.min.js')}}"></script>
    <script>
        wow = new WOW();
        wow.init();
        $(document).ready(function (e) {

            $('#video-icon').on('click', function (e) {
                e.preventDefault();
                $('.video-popup').css('display', 'flex');
                $('.iframe-src').slideDown();
            });
            $('.video-popup').on('click', function (e) {
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

        $(window).on("scroll", function () {

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
        $(window).on("load", function () {
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

                easing: 'swing',      // the easing function for animation
                scrollTime: 900,       // how long (in ms) the animation takes
                activeClass: 'active', // class given to the active nav element
                onPageChange: null,    // function(pageIndex) that is called when page is changed
                topOffset: -63
            });
        });

    </script>
    <script>
        $(document).ready(function () {
            var bubbleList = $('.bubble-container');
            const bubbleCount = bubbleList.length;

            // ขยายช่วงการกระจายองศาจาก 180 เป็น 220 องศา เพื่อเพิ่มระยะห่างระหว่างแต่ละวงกลม
            const totalDeg = 200;
            const startDeg = -20; // เริ่มต้นเอียงไปทางซ้ายล่างเล็กน้อย
            const degStep = totalDeg / (bubbleCount - 1);

            $('.bubble-container').each((index) => {
                const deg = startDeg + (index * degStep);
                const invertDeg = deg * -1; // หมุนเนื้อหาในวงกลมกลับ เพื่อให้อ่านตัวหนังสือแนวตั้งตรงเสมอ

                $(bubbleList[index]).css({
                    'transform': `rotate(${deg}deg)`,
                    'opacity': '1'
                });

                $(bubbleList[index]).find('.bubble').css('transform', `rotate(${invertDeg}deg)`);
            });
        });
    </script>
</body>

</html>