<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    {{--
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Tangerine&effect=shadow-multiple|3d-float">
    --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:ital,wght@0,500;0,700;1,400;1,500&display=swap"
        rel="stylesheet">
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
        .centralized a{
            color: #000
        }

        .main-container {
            /* border: solid 1px #000; */
            margin: 16rem 0 0 11rem;
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
            position: absolute;
            margin-top: 180px;
            left: -8rem;
            font-size: 5rem;
            font-weight: bolder;
            text-shadow: 2px 2px 2px #ffffff;

        }

        #org_addr {
            font-size: 1.5rem;
            text-align: center;
            text-shadow: 1px 1px 1px #ffffff;
        }
    </style>

</head>

<body>
    <div class="main-container centralized ">
        <div id="org">
            เทศบาล
            <div>ตำบลห้องแซง</div>
            <hr style="    margin-bottom: 3px;margin-top: 3px;">
            <div id="org_addr">ตำบลห้องแซง อำเภอเลิงนกทา จังหวัดยโสธร</div>
        </div>
        <div class="main-circle">
            <div class="inner centralized">
                ระบบบริหารจัดการ
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
        <div class="bubble-container centralized green">
            <a href="{{Auth::user()->can('access waste bank') ? route('keptkaya.dashboard') : '#'}}">
                <div class="bubble centralized">
                    <div class="inner centralized">
                        ธนาคาร<br>ขยะรีไซเคิล
                    </div>
                </div>
            </a>
        </div>
        <div class="bubble-container centralized orange">
            <a href="{{Auth::user()->can('access waste bank') ? route('keptkaya.dashboard') : '#'}}">
                <div class="bubble centralized">
                    <div class="inner centralized">
                        จัดเก็บ<br>ถังขยะรายปี
                    </div>
                </div>
            </a>
        </div>
        <div class="bubble-container centralized red">

            <div class="bubble centralized">
                <div class="inner centralized">
                    ถังหมัก<br>เศษอาหาร
                </div>
            </div>
        </div>
        <div class="bubble-container centralized black">

            <div class="bubble centralized">
                <div class="inner centralized">
                    ธนาคาร<br>ออมทรัพย์
                </div>
            </div>
        </div>
        <div class="bubble-container centralized blue-light">

            <div class="bubble centralized">
                <div class="inner centralized">
                    ผู้ดูแลระบบ
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
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