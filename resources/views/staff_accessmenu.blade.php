<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
        <link href="https://fonts.googleapis.com/css2?family=Sarabun:ital,wght@0,500;0,700;1,400;1,500&display=swap">

    <style>
        body {
            margin: 0;
            font-family: "Sarabun", sans-serif;

            min-height: 100vh;
            position: relative;
            background: radial-gradient(ellipse at center, rgba(139, 143, 145, 1) 0%, rgba(111, 113, 113, 1) 47%, rgba(63, 63, 65, 1) 100%);
        }

        .legend {
            /* position: absolute; */
            color: white;
            left: 45vh;
            /* top: 3vh; */
            text-align: right;
            width: 40vh;
            font-size: 6vh;
            /* font-family: 'Raleway', cursive; */
        }

        .legend-top {
            font-weight: 800;
            border-bottom: solid 1px white;
            float: right;
        }

        .legend-bottom {
            font-weight: 300;
            clear: both;
        }

        .item {
            border: solid 4vh #d1d81e;
            border-radius: 100%;
            position: absolute;
            box-shadow: #000 0 0 15px;
            background-color: white;
            display: flex;
            flex-direction: column;
            text-align: center;
        }
        .item:hover{
            border: solid 5vh #d1d81e;
            box-shadow: #cc0c0c 0 0 15px;

        }

        .value {
            display: flex;
            flex-grow: 1;
            /* font-family: 'Rubik'; */
            font-weight: bold;
            align-items: flex-end;
            justify-content: center;
        }

        /* .value span {
            vertical-align: baseline;
        }

        .value small {
            font-size: .3em;
            line-height: 1em;
        } */

        .title {
            flex-grow: 1;
            align-items: flex-end;
            justify-content: center;
        }

        .title i {
            display: block;
            font-size: 1.4em;
        }

        #first {
            left: 1vh;
            top: 20vh;
            width: 30vh;
            height: 30%;
            z-index: 1;
        }

        #first .value {
            font-size: 2rem;
        }

        #first .title {
            font-size: 4vh;
        }

        #second {
            left: 15vh;
            top: 53vh;
            width: 25vh;
            height: 25%;
            z-index: 2;
        }

        #second .value {
            font-size: 2rem;
        }

        #second .title {
            font-size: 3vh;
        }
        a{
            color: #000
        }

        #third {
            left: 1vh;
            top: 78vh;
            width: 20vh;
            height: 20%;
            z-index: 3;
        }

        #third .value {
            font-size: 2rem;
        }

        #third .title {
            font-size: 2.5vh;
        }
        /* สำหรับจอใหญ่ */
.my-button:hover {
    background-color: #f0f0f0;
}

/* สำหรับจอมือถือ */
@media (pointer: coarse) {
  .my-button:hover {
    /* ไม่ต้องทำอะไร */
  }
  .my-button:active {
    background-color: #f0f0f0;
  }
}
    </style>
</head>

<body>
    <div id='container'>
        <div class='legend' id="myElement">
            <div class='legend-top my-button'>เจ้าหน้าที่</div>
            <div class='legend-bottom'>ANDROID</div>
        </div>
        <a href="{{ route('keptkaya.purchase.select_user') }}">
        <div id='first' class='item'>
            <div class='value'>
                <div>ธนาคารขยะ<br>รีไซเคิล</div>
            </div>
            <div class='title'> <i class="fa fa-trash"></i> </div>
        </div>
        </a>
        <div id='second' class='item'>
            <div class='value'>
                <div>จดมิเตอร์<br>ประปา</div>
            </div>
            <div class='title'> <i class="fa fa-film" aria-hidden="true"></i> </div>
        </div>
        <div id='third' class='item'>
            <div class='value'>
                <div>ธนาคารขยะ<br>เปียก</div>
            </div>
            <div class='title'> <i class="fa fa-music" aria-hidden="true"></i> </div>
        </div>
</body>

</html>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>

<script>
//     const myElement = document.getElementById('myElement');
// myElement.addEventListener('touchstart', function() {
//     this.classList.add('hover-effect');
// });
// myElement.addEventListener('touchend', function() {
//     this.classList.remove('hover-effect');
// });
</script>