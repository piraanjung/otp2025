<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OPT-ConnecT</title>


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
            height: 400px;
            width: 400px;
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
            height: 300px;
            position: absolute;
            width: 300px;
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

        .main-container .bubble-container .bubble {
            border-radius: 100%;
            box-sizing: border-box;
            position: absolute;
            height: 210px;
            top: calc(50% - 215px);
            left: -280px;
            width: 210px;
            transform: rotate(0deg);
            transition: all ease 0.8s;

            text-align: center
        }

        .bubble .inner:hover {
            transform: rotate(0deg);
            transition: all ease 0.8s;
            transform: scale(1.08) !important;
        }

        .main-container .bubble-container .bubble .inner {
            background: #fff;
            border-radius: 100%;
            box-shadow: 4px 5px 5px 0px rgba(0, 0, 0, 0.2);
            box-sizing: border-box;
            height: 184px;
            width: 184px;
            overflow: hidden;
            font-size: 30px;
        }

        .main-container .bubble-container.black .bubble,
        .main-container .bubble-container.black .pointer .inner {
            background: #505269;
        }

        .main-container .bubble-container.blue-dark .bubble,
        .main-container .bubble-container.blue-dark .pointer .inner {
            background: #4c67aa;
        }

        .main-container .bubble-container.blue-light .bubble,
        .main-container .bubble-container.blue-light .pointer .inner {
            background: #25ade1;
        }

        .main-container .bubble-container.green .bubble,
        .main-container .bubble-container.green .pointer .inner {
            background: #8dc03f;
        }

        .main-container .bubble-container.orange .bubble {
            background: #fa9128;
        }

        .main-container .bubble-container.orange .pointer .inner {
            background: #fa9128;
        }

        .main-container .bubble-container.red .bubble,
        .main-container .bubble-container.red .pointer .inner {
            background: #e46020;
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
        .a-disbled{
            opacity: 0.3;
        }
    </style>

</head>

<body>
    <div id="otp-connect">
        <div class="icon-box wow fadeInUp" data-wow-delay="0.2s">
            OPT-CONECT
            {{--
            <hr style="margin-bottom: 3px;margin-top: 3px;">
            <div id="org_addr">พัฒนาชุมชน เชื่อมใจ ให้ใกล้กัน</div> --}}
        </div>
    </div>
    <div id="org" class="icon-box wow fadeInUp" data-wow-delay="0.4s">
        องค์การบริหาร
        <div>ส่วนตำบลขามป้อม</div>
        <hr style="    margin-bottom: 3px;margin-top: 3px;">
        <div id="org_addr2">ตำบลขามป้อม อำเภอพระยืน จังหวัดขอนแก่น</div>
    </div>
    <div class="main-container centralized ">

        <div class="main-circle">
            <div class="inner centralized">
                <img src="{{asset('logo/khampom.png')}}" width="90%">
                {{-- ระบบบริหารจัดการ --}}
            </div>
        </div>
        <div class="bubble-container centralized blue-dark">
            <a href="{{'dashboard'}}">
                <div class="bubble centralized">
                    <div class="inner centralized">
                        งานประปา
                    </div>
                </div>
            </a>
        </div>
        <div class="bubble-container centralized green ">
            <a href="{{route('keptkaya.dashboard', ['keptkayatype' => 'recycle'])}}">
            <div class="bubble centralized">
                <div class="inner centralized">
                    ธนาคาร<br>ขยะรีไซเคิล
                </div>
            </div>
            </a>
        </div>
        <div class="bubble-container centralized orange">
            <a href="{{route('keptkaya.dashboard', ['keptkayatype' => 'annual'])}}">
            <div class="bubble centralized">
                <div class="inner centralized">
                    ค่าจัดการ<br>ถังขยะรายปี
                </div>
            </div>
            </a>

        </div>
        <div class="bubble-container centralized red">
            <a href="{{route('keptkaya.dashboard')}}">

            <div class="bubble centralized">
                <div class="inner centralized">
                    ถังหมัก<br>เศษอาหาร
                </div>
            </div>
            </a>
        </div>
        <div class="bubble-container centralized black">
            <a href="#">
            <div class="bubble centralized">
                <div class="inner centralized">
                    ธนาคาร<br>ออมทรัพย์
                </div>
            </div>
            </a>
        </div>
        <div class="bubble-container centralized blue-light">

            <div class="bubble centralized">
                <div class="inner centralized">
                    ผู้ดูแลระบบ
                </div>
            </div>
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
            const degStep = 180 / (bubbleCount - 1);

            $('.bubble-container').each((index) => {
                const deg = index * degStep;
                const invertDeg = deg * -1;

                $(bubbleList[index]).css('transform', `rotate(${deg}deg)`);
                $(bubbleList[index]).css('opacity', `1`);
                $(bubbleList[index]).find('.bubble').css('transform', `rotate(${invertDeg}deg)`);
            })
        })
    </script>
</body>

</html>