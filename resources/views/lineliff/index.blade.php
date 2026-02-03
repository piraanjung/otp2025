@extends('layouts.print')
@section('style')
  <style>
    @import url("https://fonts.googleapis.com/css?family=Fredoka+One");

    .store-container {
      line-height: 0;
      margin: 50px auto;
      width: 90%;
    }

    .stroke {
      stroke: #0170bb;
      stroke-width: 5;
      stroke-linejoin: round;
      stroke-miterlimit: 10;
    }

    .round-end {
      stroke-linecap: round;
    }

    #store {
      animation: fadeIn 0.8s ease-in;
    }

    .border-animation {
      background-color: white;
      border-radius: 10px;
      position: relative;
    }

    .border-animation:after {
      content: "";
      background: linear-gradient(45deg, #ccc 48.9%, #0170bb 49%);
      background-size: 300% 300%;
      border-radius: 10px;
      position: absolute;
      top: -5px;
      left: -5px;
      height: calc(100% + 10px);
      width: calc(100% + 10px);
      z-index: -1;
      animation: borderGradient 8s linear both infinite;
    }

    @keyframes borderGradient {

      0%,
      100% {
        background-position: 0% 100%;
      }

      50% {
        background-position: 100% 0%;
      }
    }

    @keyframes fadeIn {
      to {
        opacity: 1;
      }
    }

    #browser {
      transform: translateY(-100%);
      -webkit-animation: moveDown 1.5s cubic-bezier(0.77, -0.5, 0.3, 1.5) forwards;
      animation: moveDown 1.5s cubic-bezier(0.77, -0.5, 0.3, 1.5) forwards;
    }

    @keyframes moveDown {
      from {
        transform: translate(0, -100%);
      }

      to {
        transform: translate(0, 0);
      }
    }

    #toldo {
      animation: fadeIn 1s 1.4s ease-in forwards;
    }

    .grass {
      animation: fadeIn 0.5s 1.6s ease-in forwards;
    }

    #window {
      animation: fadeIn 0.5s 1.8s ease-in forwards;
    }

    #door {
      animation: fadeIn 0.5s 2s ease-in forwards;
    }

    #sign {
      transform-origin: 837px 597px;
      animation: pendulum 1.5s 2s ease-in-out alternate;
    }

    .trees {
      animation: fadeIn 0.5s 2.2s ease-in forwards;
    }

    #toldo,
    .grass,
    #window,
    #door,
    .trees,
    .cat,
    .cat-shadow,
    .box,
    .parachute,
    .tshirt,
    .cap,
    .ball,
    #text,
    #button,
    .sky-circle,
    .sky-circle2,
    .sky-circle3 {
      opacity: 0;
    }

    @keyframes pendulum {
      20% {
        transform: rotate(60deg);
      }

      40% {
        transform: rotate(-40deg);
      }

      60% {
        transform: rotate(20deg);
      }

      80% {
        transform: rotate(-5deg);
      }
    }

    .cat {
      transform-origin: 1145px 620px;
    }

    .cat-shadow {
      transform-origin: 1115px 625px;
    }

    #store:hover .cat {
      animation: catHi 3s 3s cubic-bezier(0.7, -0.5, 0.3, 1.4);
    }

    #store:hover .cat-shadow {
      animation: catShadow 4s 2s cubic-bezier(0.7, -0.5, 0.3, 1.4) alternate;
    }

    @keyframes catHi {

      0%,
      100% {
        opacity: 0;
        transform: scale(0.8);
      }

      10%,
      60% {
        transform: scale(1);
        opacity: 1;
      }
    }

    @keyframes catShadow {

      0%,
      100% {
        transform: translate(40px, -35px) scale(0.3);
      }

      10%,
      60% {
        opacity: 1;
        transform: translate(-5px, 10px) scale(0.5);
      }

      60% {
        opacity: 0;
      }
    }

    .box,
    .parachute {
      transform-origin: 430px 100px;
      animation: moveBox 14s 4s linear forwards infinite;
    }

    .parachute {
      animation: parachute 14s 4s linear forwards infinite;
    }

    @keyframes moveBox {
      0% {
        opacity: 0;
        transform: translate(0, -150px) rotate(20deg);
      }

      15% {
        opacity: 1;
        transform: translate(0, 100px) rotate(-15deg);
      }

      25% {
        transform: translate(0, 250px) rotate(10deg);
      }

      30% {
        transform: translate(0, 350px) rotate(-5deg);
      }

      35% {
        opacity: 1;
        transform: translate(0, 570px) rotate(0deg);
      }

      45%,
      100% {
        opacity: 0;
        transform: translate(0, 570px);
      }
    }

    @keyframes parachute {
      0% {
        transform: translate(0, -150px) rotate(20deg) scale(0.8);
        opacity: 0;
      }

      15% {
        transform: translate(0, 100px) rotate(-15deg) scale(1);
        opacity: 1;
      }

      25% {
        transform: translate(0, 250px) rotate(10deg);
      }

      30% {
        transform: translate(0, 350px) rotate(-5deg);
      }

      33% {
        transform: translate(0, 460px) rotate(0deg) scale(0.9);
        opacity: 1;
      }

      45%,
      100% {
        transform: translate(0, 480px);
        opacity: 0;
      }
    }

    .tshirt {
      animation: fadeInOut 42s 10s ease-in forwards infinite;
    }

    .cap {
      animation: fadeInOut 42s 24s ease-in forwards infinite;
    }

    .ball {
      animation: fadeInOut 42s 38s ease-in forwards infinite;
    }

    #text,
    #button {
      animation: fadeIn 1s 5s ease-in forwards;
    }

    @keyframes fadeInOut {

      5%,
      12% {
        opacity: 1;
      }

      20% {
        opacity: 0;
      }
    }

    .cloud {
      animation: clouds 50s linear backwards infinite;
    }

    .cloud2 {
      animation: clouds 40s 40s linear backwards infinite;
    }

    .plane {
      animation: clouds 30s linear backwards infinite;
      will-change: transform;
    }

    @keyframes clouds {
      from {
        transform: translate(-150%, 0);
      }

      to {
        transform: translate(150%, 0);
      }
    }

    .sky-circle {
      animation: fadeInOut 10s 5s ease-in infinite;
    }

    .sky-circle2 {
      animation: fadeInOut 12s 30s ease-in infinite;
    }

    .sky-circle3 {
      animation: fadeInOut 8s 40s ease-in infinite;
    }

    .hidden {
      display: none !important
    }

    .select2 {
      width: 100% !important
    }


  </style>
  <style>
    /* ปรับให้ Modal เต็มจอบนมือถือ */
    @media (max-width: 576px) {
        .modal-dialog.modal-fullscreen-sm-down {
            max-width: none;
            height: 100%;
            margin: 0;
        }
        .modal-content {
            height: 100%;
            border: 0;
            border-radius: 0;
        }
    }

    /* Input ที่กดแล้วเด้ง Modal */
    .clickable-input {
        background-color: #fff !important; /* ให้ดูเหมือน Input ปกติ */
        cursor: pointer;
        caret-color: transparent; /* ไม่ให้มี cursor กระพริบ */
    }

    /* รายการใน Modal */
    .list-group-item-action {
        cursor: pointer;
    }
    .list-group-item-action:active {
        background-color: #e9ecef;
    }

    /* Import ฟอนต์ไทยสวยๆ */
@import url('https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600&display=swap');

body {
    font-family: 'Prompt', sans-serif;
    background-color: #f0f2f5; /* สีพื้นหลังให้อ่อนสบายตา */
}

/* ปรับแต่ง Card หลัก */
.material-card {
    border: none;
    border-radius: 20px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    background: #fff;
    overflow: hidden;
}

/* ส่วนหัว Profile */
.profile-header {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    color: white;
    padding: 30px 20px 20px;
    text-align: center;
    border-radius: 0 0 50% 50% / 20px; /* ทำโค้งด้านล่างเล็กน้อย */
    margin-bottom: 25px;
}

.profile-avatar {
    width: 90px;
    height: 90px;
    border-radius: 50%;
    border: 4px solid rgba(255,255,255,0.8);
    box-shadow: 0 4px 10px rgba(0,0,0,0.2);
    object-fit: cover;
    background-color: #ddd;
}

/* Selection Cards (เลือกประเภทหน่วยงาน) */
.org-selector-wrapper {
    display: flex;
    gap: 15px;
    justify-content: center;
}

.org-radio-input {
    display: none;
}

.org-card {
    flex: 1;
    border: 2px solid #e9ecef;
    border-radius: 15px;
    padding: 15px 10px;
    text-align: center;
    cursor: pointer;
    transition: all 0.2s ease;
    background: #fff;
    color: #6c757d;
}

.org-card i {
    font-size: 1.5rem;
    margin-bottom: 5px;
    display: block;
}

/* เมื่อถูกเลือก (Checked State) */
.org-radio-input:checked + .org-card {
    border-color: #007bff;
    background-color: #f0f7ff;
    color: #007bff;
    font-weight: 600;
    box-shadow: 0 4px 12px rgba(0,123,255,0.15);
}

/* Floating Form Inputs Customization */
.form-floating > .form-control {
    border-radius: 12px;
    border: 1px solid #dee2e6;
    height: 55px;
}
.form-floating > .form-control:focus {
    box-shadow: none;
    border-color: #007bff;
    border-width: 2px;
}
.select2-container .select2-selection--single {
    height: 55px !important;
    border-radius: 12px !important;
    border: 1px solid #dee2e6 !important;
    display: flex;
    align-items: center;
}

/* Clickable Readonly Inputs */
.clickable-input {
    background-color: #fff !important;
    cursor: pointer;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right 0.75rem center;
    background-size: 16px 12px;
}

/* ปุ่ม Submit */
.btn-submit-material {
    border-radius: 50px;
    padding: 12px;
    font-size: 1.1rem;
    font-weight: 500;
    letter-spacing: 0.5px;
    box-shadow: 0 4px 15px rgba(23, 162, 184, 0.4);
    transition: transform 0.2s;
}
.btn-submit-material:active {
    transform: scale(0.98);
}
</style>
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
@endsection

@section('content')
<a href="#" id="to_index_page" class="btn btn-sm btn-outline"><i class="bi bi-skip-backward-circle"></i>
</a>

<div id="index_page">
  <div id="store_container" class="store-container">
    <div class="border-animation">
      <svg role="img" xmlns="http://www.w3.org/2000/svg" id="store" viewBox="130 0 1230 930">
        <title xml:lang="en">Store animation loader</title>
        <defs>
          <filter id="f1">
            <feGaussianBlur in="SourceGraphic" stdDeviation="0,4" />
          </filter>
          <circle id="sky-circle" fill="none" class="stroke" cx="198.7" cy="314" r="5.5" />
          <path id="cloud" fill="#FFF" class="stroke"
            d="M503.6 39.1c-2.9 0.2-5.8 0.7-8.5 1.4 -14.7-24.5-42.3-40-72.8-37.8 -31.2 2.2-56.9 22.4-67.6 49.7 -2.5-0.4-5-0.5-7.6-0.3 -18.5 1.3-32.5 17.4-31.2 35.9s17.4 32.5 35.9 31.2c2.3-0.2 4.6-0.6 6.8-1.2 14.1 26.5 42.9 43.6 74.8 41.3 23.1-1.6 43.2-13.1 56.4-30.1 6.3 2.5 13.2 3.6 20.4 3.1 25.7-1.8 45.1-24.1 43.3-49.9C551.6 56.7 529.3 37.3 503.6 39.1z" />
          <path id="cloud2" fill="#FFF" class="stroke" transform="scale(.8)"
            d="M503.6 39.1c-2.9 0.2-5.8 0.7-8.5 1.4 -14.7-24.5-42.3-40-72.8-37.8 -31.2 2.2-56.9 22.4-67.6 49.7 -2.5-0.4-5-0.5-7.6-0.3 -18.5 1.3-32.5 17.4-31.2 35.9s17.4 32.5 35.9 31.2c2.3-0.2 4.6-0.6 6.8-1.2 14.1 26.5 42.9 43.6 74.8 41.3 23.1-1.6 43.2-13.1 56.4-30.1 6.3 2.5 13.2 3.6 20.4 3.1 25.7-1.8 45.1-24.1 43.3-49.9C551.6 56.7 529.3 37.3 503.6 39.1z" />
          <g id="tree">
            <rect x="1114.2" y="721.5" fill="#FFF" class="stroke" width="22" height="127" />
            <g opacity="0.4">
              <path fill="#0170BB"
                d="M1085.2 552.4c-29.4 14.7-49.5 45-49.5 80.1 0 49.4 40.1 89.5 89.5 89.5 49.4 0 89.5-40.1 89.5-89.5 0-35.2-20.3-65.6-49.8-80.2" />
              <path fill="#0170BB"
                d="M1164.9 552.3c10-10.1 16.1-24 16.1-39.3 0-30.9-25.1-56-56-56s-56 25.1-56 56c0 15.4 6.2 29.3 16.2 39.4" />
              <path fill="#0170BB" d="M1153.9 561c4-2.4 7.7-5.4 11-8.7" />
              <path fill="#0170BB" d="M1104.3 545.5c-6.7 1.6-13.1 3.9-19.1 7" />
            </g>
            <path fill="none" class="stroke round-end"
              d="M1085.2 552.4c-29.4 14.7-49.5 45-49.5 80.1 0 49.4 40.1 89.5 89.5 89.5 49.4 0 89.5-40.1 89.5-89.5 0-35.2-20.3-65.6-49.8-80.2" />
            <path fill="none" class="stroke round-end"
              d="M1164.9 552.3c10-10.1 16.1-24 16.1-39.3 0-30.9-25.1-56-56-56s-56 25.1-56 56c0 15.4 6.2 29.3 16.2 39.4" />
            <path fill="none" class="stroke round-end" d="M1153.9 561c4-2.4 7.7-5.4 11-8.7" />
            <path fill="none" class="stroke round-end" d="M1104.3 545.5c-6.7 1.6-13.1 3.9-19.1 7" />
          </g>
          <g id="cat">
            <circle fill="#0170BB" cx="1115" cy="625" r="25"></circle>
            <path fill="#FFF" stroke="#0170BB" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"
              stroke-miterlimit="10" d="M1097.1 612.3c0 0-4.5-9.3-0.3-17.7 0 0 4.5 5.6 9.3 7" />
            <path fill="#FFF" stroke="#0170BB" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"
              stroke-miterlimit="10" d="M1140.6 612.3c0 0 4.5-9.3 0.3-17.7 0 0-4.5 5.6-9.3 7" />
            <circle fill="#FFF" stroke="#0170BB" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"
              stroke-miterlimit="10" cx="1118.6" cy="621.7" r="23.1" />
            <path fill="#ED4F43" d="M1122.4 625c0 5.3-1.4 6.3-3.8 6.3 -2.4 0-3.8-1-3.8-6.3" />
            <path fill="#FFF" stroke="#0170BB" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"
              stroke-miterlimit="10" d="M1132.8 621.2c0 3.9-3.2 7-7 7s-7-3.2-7-7h-0.2c0 3.9-3.2 7-7 7s-7-3.2-7-7" />
            <path fill="#FFF" stroke="#0170BB" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"
              stroke-miterlimit="10" d="M1104.7 613c0 0 0-3.1 2.8-3.8 2.9-0.8 4.2 1.7 4.2 1.7" />
            <path fill="#FFF" stroke="#0170BB" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"
              stroke-miterlimit="10" d="M1132.6 613c0 0 0-3.1-2.8-3.8 -2.9-0.8-4.2 1.7-4.2 1.7" />
            <path fill="#0170BB"
              d="M1118.6 622c0 0-2.9-0.8-2.9-1.9v0c0-1 0.8-1.9 1.9-1.9h2.2c1 0 1.9 0.8 1.9 1.9v0C1121.6 621.2 1118.6 622 1118.6 622z" />
          </g>
          <g id="parachute">
            <path fill="#a5c7e4"
              d="M429.4 2.5c-36.7 0-66.3 32.4-66.3 72.4 -9.3-6.7-19.4-5.9-30.1 0C333 74.9 355 2.5 429.4 2.5" />
            <path fill="#a5c7e4"
              d="M429.6 2.5c36.7 0 66.3 32.4 66.3 72.4 9.3-6.7 19.4-5.9 30.1 0C526 74.9 504 2.5 429.6 2.5" />
            <path fill="#a5c7e4"
              d="M429.6 2.5c15.3 0 27.6 36.5 27.7 76 -9.3-3.9-18.5-5.9-27.7-6h-0.2c-9.2 0-18.4 2.1-27.7 6 0.1-39.5 12.4-76 27.7-76" />
            <path fill="none" class="stroke" d="M401.8 78.5c0 0-13.4-14.6-38.9-3.6" />
            <path fill="none" class="stroke"
              d="M429.4 2.5c-36.7 0-66.3 32.4-66.3 72.4 -9.3-6.7-19.4-5.9-30.1 0C333 74.9 355 2.5 429.4 2.5" />
            <path fill="none" class="stroke"
              d="M429.6 2.5c36.7 0 66.3 32.4 66.3 72.4 9.3-6.7 19.4-5.9 30.1 0C526 74.9 504 2.5 429.6 2.5" />
            <path fill="none" class="stroke"
              d="M429.6 2.5c15.3 0 27.6 36.5 27.7 76 -9.3-3.9-18.5-5.9-27.7-6h-0.2c-9.2 0-18.4 2.1-27.7 6 0.1-39.5 12.4-76 27.7-76" />
            <path fill="none" class="stroke"
              d="M362.9 75l66.6 104 66-104.1c-25.5-10.9-38.9 3.6-38.9 3.6L429.5 179 401.3 78" />
            <polyline fill="none" class="stroke" points="333.3 75 429.5 179 526.3 75 " />
          </g>
          <g id="box">
            <rect x="356" y="47" fill="#FFF" class="stroke" width="106.2" height="86" />
            <polygon fill="#FFF" class="stroke" points=" 462.2 47 356 47 403.2 31 500.1 31 " />
            <polygon fill="#FFF" class="stroke" points=" 500.1 117 462.2 133 462.2 47 500.1 31 " />
            <polygon opacity="0.4" fill="#0170BB"
              points="394.1 47 394.5 81.5 408.5 70.5 422.5 81.5 422.5 47 463.3 31 431.7 31 " />
            <polygon fill="none" class="stroke"
              points=" 394.1 47 394.5 81.5 408.5 70.5 422.5 81.5 422.5 47 463.3 31 431.7 31 " />
          </g>
          <path id="tshirt" fill="#FFF" class="stroke"
            d="M442 717h35.7c1.7 0 3-1.5 3-3.4v-59.2c0-2.6 2.2-4.4 4.3-3.6l10.4 3.8c3.8 2.2 4.5 0.7 7.1-4.7l7.3-14.5c1.6-2.8 0.7-4.6-1.9-6.9C486 611.1 464.7 608 464.7 608c-1.5 0-2.7 1.2-3 2.9 -0.7 4.8-6.7 14.6-17.4 14.6s-16.7-9.8-17.4-14.6c-0.2-1.7-1.5-2.9-3-2.9 0 0-21.3 3-43.2 20.5 -2.6 2.4-3.5 4.1-1.9 6.9l7.3 14.5c2.7 5.4 3.3 6.8 7.1 4.7l10.4-3.8c2.1-0.8 4.3 1 4.3 3.6v59.2c0 1.9 1.3 3.4 3 3.4h35.7H442z" />
          <g id="cap">
            <path fill="#FFF" class="stroke"
              d="M495.9 829.4c-0.4 33-19.4 8.5-50 8.5 -31.4 0-50.4 24.5-50-8.5 0.3-27.9 0.6-62.5 50-62.5C495.5 766.9 496.2 801.5 495.9 829.4z" />
            <path fill="none" class="stroke" d="M396.4 824.4c0 0 18.9-8 49.5-8s49.5 8 49.5 8" />
            <ellipse fill="#0170BB" cx="445.9" cy="763.4" rx="8.5" ry="3" />
            <path fill="none" class="stroke" d="M406.4 819.4c0-20.7-4.8-52 39.5-52.5 44.7-0.5 39.5 31.8 39.5 52.5" />
            <line fill="none" class="stroke" x1="445.9" y1="766.4" x2="445.9" y2="816.4" />
            <circle fill="#0170BB" cx="429.4" cy="777.4" r="2" />
            <circle fill="#0170BB" cx="462.4" cy="777.4" r="2" />
          </g>
          <g id="ball">
            <circle fill="#FFF" class="stroke" cx="446" cy="803.8" r="47.3" />
            <line fill="none" class="stroke" x1="446" y1="756.8" x2="446" y2="850.8" />
            <line fill="none" class="stroke" x1="493" y1="804.3" x2="399" y2="804.3" />
            <path fill="none" class="stroke" d="M484.2 834c-9.1-6.3-22.8-16.4-38.2-16.4s-29.1 10-38.2 16.4" />
            <path fill="none" class="stroke" d="M407.8 774.6c9.1 6.3 22.8 16.4 38.2 16.4s29.1-10 38.2-16.4" />
          </g>
          <g id="grass">
            <path fill="#a5c7e4"
              d="M1226.5 857.5c4.7-20.9-7-33.3-20.4-41.3 -2-1.2-4-2.2-6.1-3.1 4.6 8.1 4.6 18.2-1 26.5 -1.3 1.9-2.7 3.5-4.4 5 -1.9-5.6-4.8-11.1-8.9-16 -5.7-6.9-12.9-12.1-20.9-15.4 6.6 10.9 4 24.9-6.5 33 -10.1-7.4-13.4-20.4-8.2-31.3 -7.6 4-14.3 9.8-19.3 17.2 -3.2 4.8-5.5 9.8-6.9 15 -2-1.4-3.8-3.1-5.4-5 -6.4-7.8-7.4-17.8-3.6-26.3 -2 1.1-3.6 2.8-5.7 3.6 -7.2 2.9-9.8 11.8-10.5 21 -3.7-12.9-11.1-24.1-11.1-24.1 -2-1.2-4-2.2-6.1-3.1 4.6 8.1 4.6 18.2-1 26.5 -1.3 1.9-2.7 3.5-4.4 5 -1.9-5.6-4.8-11.1-8.9-16 -5.7-6.9-12.9-12.1-20.9-15.4 6.6 10.9 4 24.9-6.5 33 -10.1-7.4-13.4-20.4-8.2-31.3 -7.6 4-14.3 9.8-19.3 17.2 -3.2 4.8-5.5 9.8-6.9 15 -2-1.4-3.8-3.1-5.4-5 -6.4-7.8-7.4-17.8-3.6-26.3 -2 1.1-3.9 2.3-5.7 3.6 -11 8-17.9 19.2-20.2 31.2 -2.6-13.7-11-26.3-24.4-34.3 -2-1.2-4-2.2-6.1-3.1 4.6 8.1 4.6 18.2-1 26.5 -1.3 1.9-2.7 3.5-4.4 5 -1.9-5.6-4.8-11.1-8.9-16 -5.7-6.9-12.9-12.1-20.9-15.4 6.6 10.9 4 24.9-6.5 33 -10.1-7.4-13.4-20.4-8.2-31.3 -7.6 4-14.3 9.8-19.3 17.2 -3.2 4.8-5.5 9.8-6.8 15 -2-1.4-3.8-3.1-5.4-5 -6.4-7.8-7.4-17.8-3.6-26.3 -2 1.1-3.9 2.3-5.7 3.6 -8.1 5.9-14 13.6-17.5 22 -4-10-11.4-19-21.7-25.2 -2-1.2-4-2.2-6.1-3.1 4.6 8.1 4.6 18.2-1 26.5 -1.3 1.9-2.7 3.5-4.4 5 -1.9-5.6-4.8-11.1-8.9-16 -5.7-6.9-12.9-12.1-20.9-15.4 6.6 10.9 4 24.9-6.5 33 -10.1-7.4-13.4-20.4-8.2-31.3 -7.6 4-14.3 9.8-19.3 17.2 -3.2 4.8-5.5 9.8-6.9 15 -2-1.4-3.8-3.1-5.4-5 -6.4-7.8-7.4-17.8-3.6-26.3 -2 1.1-3.9 2.3-5.7 3.6 -11 8-17.9 19.2-20.2 31.2 -2.6-13.7-11-26.3-24.4-34.3 -2-1.2-4-2.2-6.1-3.1 4.6 8.1 4.6 18.2-1 26.5 -1.3 1.9-2.7 3.5-4.4 5 -1.9-5.6-4.8-11.1-8.9-16 -5.7-6.9-12.9-12.1-20.9-15.4 6.6 10.9 4 24.9-6.5 33 -10.1-7.4-13.4-20.4-8.2-31.3 -7.6 4-14.3 9.8-19.3 17.2 -3.2 4.8-5.5 9.8-6.9 15 -2-1.4-3.8-3.1-5.4-5 -6.4-7.8-7.4-17.8-3.6-26.3 -2 1.1-3.9 2.3-5.7 3.6 -8.5 6.2-14.5 14.2-17.9 23 -3.9-10.4-11.4-19.8-22.1-26.2 -2-1.2-4-2.2-6.1-3.1 4.6 8.1 4.6 18.2-1 26.5 -1.3 1.9-2.7 3.5-4.4 5 -1.9-5.6-4.8-11.1-8.9-16 -5.7-6.9-12.9-12.1-20.9-15.4 6.6 10.9 4 24.9-6.5 33 -10.1-7.4-13.4-20.4-8.2-31.3 -7.6 4-14.3 9.8-19.3 17.2 -3.2 4.8-5.5 9.8-6.8 15 -2-1.4-3.8-3.1-5.4-5 -6.4-7.8-7.4-17.8-3.6-26.3 -2 1.1-3.9 2.3-5.7 3.6 -11 8-17.9 19.2-20.2 31.2 -2.6-13.7-11-26.3-24.4-34.3 -2-1.2-4-2.2-6.1-3.1 4.6 8.1 4.6 18.2-1 26.5 -1.3 1.9-2.7 3.5-4.4 5 -1.9-5.6-4.8-11.1-8.9-16 -5.7-6.9-12.9-12.1-20.9-15.4 6.6 10.9 4 24.9-6.5 33 -10.1-7.4-13.4-20.4-8.2-31.3 -7.6 4-14.3 9.8-19.3 17.2 -3.2 4.8-5.5 9.8-6.9 15 -2-1.4-3.8-3.1-5.4-5 -6.4-7.8-7.4-17.8-3.6-26.3 -2 1.1-3.9 2.3-5.7 3.6 -8.1 5.9-14 13.6-17.5 22 -4-10-11.4-19-21.7-25.2 -2-1.2-4-2.2-6.1-3.1 4.6 8.1 4.6 18.2-1 26.5 -1.3 1.9-2.7 3.5-4.4 5 -1.9-5.6-4.8-11.1-8.9-16 -5.7-6.9-12.9-12.1-20.9-15.4 6.6 10.9 4 24.9-6.5 33 -10.1-7.4-13.4-20.4-8.2-31.3 -7.6 4-14.3 9.8-19.3 17.2 -3.2 4.8-5.5 9.8-6.9 15 -2-1.4-3.8-3.1-5.4-5 -6.4-7.8-7.4-17.8-3.6-26.3 -2 1.1-3.9 2.3-5.7 3.6 -11 8-17.9 19.2-20.2 31.2 -2.6-13.7-11-26.3-24.4-34.3 -2-1.2-4-2.2-6.1-3.1 4.6 8.1 4.6 18.2-1 26.5 -1.3 1.9-2.7 3.5-4.4 5 -1.9-5.6-4.8-11.1-8.9-16 -5.7-6.9-12.9-12.1-20.9-15.4 6.6 10.9 4 24.9-6.5 33 -10.1-7.4-13.4-20.4-8.2-31.3 -7.6 4-14.3 9.8-19.3 17.2 -3.2 4.8-5.5 9.8-6.9 15 -2-1.4-3.8-3.1-5.4-5 -6.4-7.8-7.4-17.8-3.6-26.3 -2 1.1-3.9 2.3-5.7 3.6 -27.2 20.2-8.8 45.6-8.8 45.6" />
            <path fill="none" class="stroke round-end"
              d="M1226.5 857.5c4.7-20.9-7-33.3-20.4-41.3 -2-1.2-4-2.2-6.1-3.1 4.6 8.1 4.6 18.2-1 26.5 -1.3 1.9-2.7 3.5-4.4 5 -1.9-5.6-4.8-11.1-8.9-16 -5.7-6.9-12.9-12.1-20.9-15.4 6.6 10.9 4 24.9-6.5 33 -10.1-7.4-13.4-20.4-8.2-31.3 -7.6 4-14.3 9.8-19.3 17.2 -3.2 4.8-5.5 9.8-6.9 15 -2-1.4-3.8-3.1-5.4-5 -6.4-7.8-7.4-17.8-3.6-26.3 -2 1.1-3.6 2.8-5.7 3.6 -7.2 2.9-9.8 11.8-10.5 21 -3.7-12.9-11.1-24.1-11.1-24.1 -2-1.2-4-2.2-6.1-3.1 4.6 8.1 4.6 18.2-1 26.5 -1.3 1.9-2.7 3.5-4.4 5 -1.9-5.6-4.8-11.1-8.9-16 -5.7-6.9-12.9-12.1-20.9-15.4 6.6 10.9 4 24.9-6.5 33 -10.1-7.4-13.4-20.4-8.2-31.3 -7.6 4-14.3 9.8-19.3 17.2 -3.2 4.8-5.5 9.8-6.9 15 -2-1.4-3.8-3.1-5.4-5 -6.4-7.8-7.4-17.8-3.6-26.3 -2 1.1-3.9 2.3-5.7 3.6 -11 8-17.9 19.2-20.2 31.2 -2.6-13.7-11-26.3-24.4-34.3 -2-1.2-4-2.2-6.1-3.1 4.6 8.1 4.6 18.2-1 26.5 -1.3 1.9-2.7 3.5-4.4 5 -1.9-5.6-4.8-11.1-8.9-16 -5.7-6.9-12.9-12.1-20.9-15.4 6.6 10.9 4 24.9-6.5 33 -10.1-7.4-13.4-20.4-8.2-31.3 -7.6 4-14.3 9.8-19.3 17.2 -3.2 4.8-5.5 9.8-6.8 15 -2-1.4-3.8-3.1-5.4-5 -6.4-7.8-7.4-17.8-3.6-26.3 -2 1.1-3.9 2.3-5.7 3.6 -8.1 5.9-14 13.6-17.5 22 -4-10-11.4-19-21.7-25.2 -2-1.2-4-2.2-6.1-3.1 4.6 8.1 4.6 18.2-1 26.5 -1.3 1.9-2.7 3.5-4.4 5 -1.9-5.6-4.8-11.1-8.9-16 -5.7-6.9-12.9-12.1-20.9-15.4 6.6 10.9 4 24.9-6.5 33 -10.1-7.4-13.4-20.4-8.2-31.3 -7.6 4-14.3 9.8-19.3 17.2 -3.2 4.8-5.5 9.8-6.9 15 -2-1.4-3.8-3.1-5.4-5 -6.4-7.8-7.4-17.8-3.6-26.3 -2 1.1-3.9 2.3-5.7 3.6 -11 8-17.9 19.2-20.2 31.2 -2.6-13.7-11-26.3-24.4-34.3 -2-1.2-4-2.2-6.1-3.1 4.6 8.1 4.6 18.2-1 26.5 -1.3 1.9-2.7 3.5-4.4 5 -1.9-5.6-4.8-11.1-8.9-16 -5.7-6.9-12.9-12.1-20.9-15.4 6.6 10.9 4 24.9-6.5 33 -10.1-7.4-13.4-20.4-8.2-31.3 -7.6 4-14.3 9.8-19.3 17.2 -3.2 4.8-5.5 9.8-6.9 15 -2-1.4-3.8-3.1-5.4-5 -6.4-7.8-7.4-17.8-3.6-26.3 -2 1.1-3.9 2.3-5.7 3.6 -8.5 6.2-14.5 14.2-17.9 23 -3.9-10.4-11.4-19.8-22.1-26.2 -2-1.2-4-2.2-6.1-3.1 4.6 8.1 4.6 18.2-1 26.5 -1.3 1.9-2.7 3.5-4.4 5 -1.9-5.6-4.8-11.1-8.9-16 -5.7-6.9-12.9-12.1-20.9-15.4 6.6 10.9 4 24.9-6.5 33 -10.1-7.4-13.4-20.4-8.2-31.3 -7.6 4-14.3 9.8-19.3 17.2 -3.2 4.8-5.5 9.8-6.8 15 -2-1.4-3.8-3.1-5.4-5 -6.4-7.8-7.4-17.8-3.6-26.3 -2 1.1-3.9 2.3-5.7 3.6 -11 8-17.9 19.2-20.2 31.2 -2.6-13.7-11-26.3-24.4-34.3 -2-1.2-4-2.2-6.1-3.1 4.6 8.1 4.6 18.2-1 26.5 -1.3 1.9-2.7 3.5-4.4 5 -1.9-5.6-4.8-11.1-8.9-16 -5.7-6.9-12.9-12.1-20.9-15.4 6.6 10.9 4 24.9-6.5 33 -10.1-7.4-13.4-20.4-8.2-31.3 -7.6 4-14.3 9.8-19.3 17.2 -3.2 4.8-5.5 9.8-6.9 15 -2-1.4-3.8-3.1-5.4-5 -6.4-7.8-7.4-17.8-3.6-26.3 -2 1.1-3.9 2.3-5.7 3.6 -8.1 5.9-14 13.6-17.5 22 -4-10-11.4-19-21.7-25.2 -2-1.2-4-2.2-6.1-3.1 4.6 8.1 4.6 18.2-1 26.5 -1.3 1.9-2.7 3.5-4.4 5 -1.9-5.6-4.8-11.1-8.9-16 -5.7-6.9-12.9-12.1-20.9-15.4 6.6 10.9 4 24.9-6.5 33 -10.1-7.4-13.4-20.4-8.2-31.3 -7.6 4-14.3 9.8-19.3 17.2 -3.2 4.8-5.5 9.8-6.9 15 -2-1.4-3.8-3.1-5.4-5 -6.4-7.8-7.4-17.8-3.6-26.3 -2 1.1-3.9 2.3-5.7 3.6 -11 8-17.9 19.2-20.2 31.2 -2.6-13.7-11-26.3-24.4-34.3 -2-1.2-4-2.2-6.1-3.1 4.6 8.1 4.6 18.2-1 26.5 -1.3 1.9-2.7 3.5-4.4 5 -1.9-5.6-4.8-11.1-8.9-16 -5.7-6.9-12.9-12.1-20.9-15.4 6.6 10.9 4 24.9-6.5 33 -10.1-7.4-13.4-20.4-8.2-31.3 -7.6 4-14.3 9.8-19.3 17.2 -3.2 4.8-5.5 9.8-6.9 15 -2-1.4-3.8-3.1-5.4-5 -6.4-7.8-7.4-17.8-3.6-26.3 -2 1.1-3.9 2.3-5.7 3.6 -27.2 20.2-8.8 45.6-8.8 45.6" />
          </g>
          <g id="plane">
            <path fill="#FFF" class="stroke"
              d="M966.1 203.5c0 0 70.8 0.9 70.8 10.7 0 20.6-23.3 41.3-88.7 43 -34 0.9-98.5 3.6-120-1.8 -30.5-7.6-109.1-44-112-52.8 -13.4-41.2-18.8-49.3 2.7-49.3 12 0 18.6 0 26 0 14.3 0 12.5 2.7 27.8 42.1 0 0 50.2 8.1 66.3-1.8s24.6-23.3 57.6-23.4l21 0.1C938.5 171.3 949.5 176.3 966.1 203.5z" />
            <path fill="#a5c7e4"
              d="M896.5 182.4v18c0 1.1-0.9 2-2 2h-39.6c-1.8 0-2.7-2.1-1.5-3.4 5.7-6 19.6-17.9 41-18.6C895.5 180.3 896.5 181.2 896.5 182.4z" />
            <path fill="#a5c7e4"
              d="M906.5 182.4v18c0 1.1 0.9 2 2 2h39.6c1.8 0 2.4-1.9 1.5-3.4 -6.1-9.6-12.1-18.6-41-18.6C907.4 180.4 906.5 181.2 906.5 182.4z" />
            <path fill="none" class="stroke"
              d="M896.5 182.4v18c0 1.1-0.9 2-2 2h-39.6c-1.8 0-2.7-2.1-1.5-3.4 5.7-6 19.6-17.9 41-18.6C895.5 180.3 896.5 181.2 896.5 182.4z" />
            <path fill="none" class="stroke"
              d="M906.5 182.4v18c0 1.1 0.9 2 2 2h39.6c1.8 0 2.4-1.9 1.5-3.4 -6.1-9.6-12.1-18.6-41-18.6C907.4 180.4 906.5 181.2 906.5 182.4z" />
            <path fill="#a5c7e4"
              d="M745.3 193.7h-58.2c-3.7 0-6.7-3-6.7-6.7v0c0-3.7 3-6.7 6.7-6.7h58.2c3.7 0 6.7 3 6.7 6.7v0C752 190.6 749 193.7 745.3 193.7z" />
            <g id="helix">
              <path fill="#0170BB"
                d="M1037.8 233.5h-1.8c-4.2 0-3.1-12.1-3.1-12.1s-1.1-12.1 3.1-12.1l0 0c5.2 0 9.4 4.2 9.4 9.4v7.2C1045.4 230.1 1041.9 233.5 1037.8 233.5z" />
              <path fill="#a5c7e4"
                d="M1037.2 214.4L1037.2 214.4c-4.6 0-8.3-34-8.3-34 0-4.6 3.8-8.3 8.3-8.3h0c4.6 0 8.3 3.8 8.3 8.3C1045.6 180.3 1041.8 214.4 1037.2 214.4z" />
              <path fill="#a5c7e4"
                d="M1037.2 228.5L1037.2 228.5c4.6 0 8.3 34 8.3 34 0 4.6-3.8 8.3-8.3 8.3h0c-4.6 0-8.3-3.8-8.3-8.3C1028.9 262.5 1032.7 228.5 1037.2 228.5z" />
              <path fill="none" class="stroke"
                d="M1037.2 214.4L1037.2 214.4c-4.6 0-8.3-34-8.3-34 0-4.6 3.8-8.3 8.3-8.3h0c4.6 0 8.3 3.8 8.3 8.3C1045.6 180.3 1041.8 214.4 1037.2 214.4z" />
              <path fill="none" class="stroke"
                d="M1037.2 228.5L1037.2 228.5c4.6 0 8.3 34 8.3 34 0 4.6-3.8 8.3-8.3 8.3h0c-4.6 0-8.3-3.8-8.3-8.3C1028.9 262.5 1032.7 228.5 1037.2 228.5z" />
            </g>
            <use class="helix" xlink:href="#helix" filter="url(#f1)"></use>
            <line fill="none" class="stroke" x1="728" y1="213.3" x2="520" y2="213.2" />
            <polyline fill="none" class="stroke" points="520 182.8 558.5 214.2 520 243.7 " />
            <path fill="#FFF" class="stroke"
              d="M506.9 253.6H21.2c-6.6 0-12-5.4-12-12v-56.7c0-6.6 5.4-12 12-12h485.8c6.6 0 12 5.4 12 12v56.7C518.9 248.2 513.5 253.6 506.9 253.6z" />
            <text transform="matrix(1.0027 0 0 1 44.8218 224.8768)" font-family='Fredoka One' font-size="34"
              fill="#0170BB"> We are building your store </text>
            <path fill="#a5c7e4"
              d="M850.5 216.5h79.7l-4.5 10.7c0 0-2.7 7.2-9.9 7.2h-72.6c0 0-6.3-0.9-1.8-7.2L850.5 216.5z" />
            <path fill="none" class="stroke"
              d="M745.3 193.7h-58.2c-3.7 0-6.7-3-6.7-6.7v0c0-3.7 3-6.7 6.7-6.7h58.2c3.7 0 6.7 3 6.7 6.7v0C752 190.6 749 193.7 745.3 193.7z" />
            <path fill="none" class="stroke"
              d="M850.5 216.5h79.7l-4.5 10.7c0 0-2.7 7.2-9.9 7.2h-72.6c0 0-6.3-0.9-1.8-7.2L850.5 216.5z" />
          </g>
        </defs>

        <g id="window">
          <path opacity="0.4" fill="#0170BB"
            d="M683.6 773H368c-8.1 0-14.7-6.6-14.7-14.7V565.2c0-8.1 6.6-14.7 14.7-14.7h315.6c8.1 0 14.7 6.6 14.7 14.7v193.1C698.3 766.4 691.7 773 683.6 773z" />
          <path fill="none" class="stroke"
            d="M683.6 773H368c-8.1 0-14.7-6.6-14.7-14.7V565.2c0-8.1 6.6-14.7 14.7-14.7h315.6c8.1 0 14.7 6.6 14.7 14.7v193.1C698.3 766.4 691.7 773 683.6 773z" />
        </g>
        <use class="box" xlink:href="#box" x="20" y="30"></use>
        <use class="parachute" xlink:href="#parachute" x="20" y="-112"></use>
        <rect fill="white" x="320" y="310" width="665" height="238"></rect>
        <use class="tshirt" xlink:href="#tshirt"></use>
        <use class="cap" xlink:href="#cap" y="-150"></use>
        <use class="ball" xlink:href="#ball" y="-140"></use>
        <use class="sky-circle" xlink:href="#sky-circle" x="-10px" y="5"></use>
        <use class="sky-circle2" xlink:href="#sky-circle" x="500px" y="-125"></use>
        <use class="sky-circle3" xlink:href="#sky-circle" x="1000px" y="50"></use>
        <use class="cloud" xlink:href="#cloud2" x="0" y="10"></use>
        <use class="plane" xlink:href="#plane" y="-80"></use>

        <use class="cloud2" xlink:href="#cloud" x="0" y="130"></use>
        <use class="trees" xlink:href="#tree" x="40" y="0"></use>
        <circle class="cat-shadow" fill="#0170BB" cx="1160" cy="620" r="23"></circle>
        <use class="cat" xlink:href="#cat" x="15" y="5"></use>
        <g id="browser">
          <path fill="none" class="stroke"
            d="M972.4 847h-640c-8.2 0-15-6.8-15-15V322.5c0-8.2 6.8-15 15-15h640c8.2 0 15 6.8 15 15V832C987.4 840.3 980.7 847 972.4 847z" />
          <circle opacity="0.4" fill="#ED4F43" cx="363.7" cy="349.3" r="12.3" />
          <circle fill="none" class="stroke" cx="402.2" cy="349.3" r="12.3" />
          <path fill="none" stroke="#0170BB" class="stroke"
            d="M943.5 361.5H454.1c-5.5 0-9.9-4.5-9.9-9.9V344c0-5.5 4.5-9.9 9.9-9.9h489.4c5.5 0 9.9 4.5 9.9 9.9v7.6C953.4 357.1 949 361.5 943.5 361.5z" />
          <circle fill="none" class="stroke" cx="363.7" cy="349.3" r="12.3" />
        </g>
        <g id="toldo">
          <polyline fill="#FFF" class="stroke round-end" points=" 277.6 468.6 317.7 391.8 987.6 391.8 1026.9 468.6 " />
          <path fill="#FFF" class="stroke" d="M392.2 391.8l-31.3 79.5c0 22.7 18.4 41 41 41 22.7 0 41-18.4 41-41" />
          <path fill="#FFF" class="stroke" d="M466.6 391.8l-22.3 79.5c0 22.7 18.4 41 41 41s41-18.4 41-41" />
          <path id="tol" fill="#FFF" class="stroke" d="M527.6 471.2c0 22.7 18.4 41 41 41 22.7 0 41-18.4 41-41" />
          <path fill="#FFF" class="stroke" d="M615.5 391.8l-4.5 79.5c0 22.7 18.4 41 41 41 22.7 0 41-18.4 41-41" />
          <path fill="#FFF" class="stroke" d="M689.9 391.8l4.4 79.5c0 22.7 18.4 41 41 41s41-18.4 41-41" />
          <path fill="#FFF" class="stroke" d="M859.7 471.2c0 22.7-18.4 41-41 41 -22.7 0-41-18.4-41-41l-13.3-79.5" />
          <use class="tol" xlink:href="#tol" x="-250"></use>
          <use class="tol" xlink:href="#tol" x="334"></use>
          <use class="tol" xlink:href="#tol" x="417"></use>
          <line class="stroke round-end" x1="277" y1="470.5" x2="1027" y2="470.5" />
          <line class="stroke" x1="541" y1="391.8" x2="526.5" y2="471.2" />
          <line class="stroke" x1="838.8" y1="391.8" x2="860.1" y2="471.2" />
          <path opacity="0.4" fill="#0170BB"
            d="M467.3 392.1h73.4l-14 79.5c0 22.7-18.4 41-41 41 -22.7 0-41-18.4-41-41L467.3 392.1z" />
          <path opacity="0.4" fill="#0170BB"
            d="M615.7 392.1H690l3.5 79.5c0 22.7-18.4 41-41 41 -22.7 0-41-18.4-41-41L615.7 392.1z" />
          <path opacity="0.4" fill="#0170BB"
            d="M765.1 392.1h73.4l21.8 79.5c0 22.7-18.4 41-41 41s-41-18.4-41-41L765.1 392.1z" />
          <path opacity="0.4" fill="#0170BB"
            d="M913.6 392.1h73.4l40.2 79.5c0 22.7-18.4 41-41 41 -22.7 0-41-18.4-41-41L913.6 392.1z" />
          <path opacity="0.4" fill="#0170BB"
            d="M317.9 392.1h73.4l-31.4 79.5c0 22.7-18.4 41-41 41 -22.7 0-41-18.4-41-41L317.9 392.1z" />
          <line fill="none" class="stroke" x1="944.4" y1="471.6" x2="913.2" y2="392.2" />
        </g>
        <g id="door">
          <path fill="none" class="stroke" d="M955.8 846V560.5c0-5.5-4.5-10-10-10H738.6c-5.5 0-10 4.5-10 10V846" />
          <rect fill="#0170BB" x="730" y="700" width="225" height="15"></rect>
          <g id="sign">
            <polyline fill="none" class="stroke" points=" 800.8 672.8 842.5 601 883.6 672.8 " />
            <ellipse fill="#FFF" class="stroke" cx="842.2" cy="601" rx="10" ry="10" />
            <path fill="#a5c7e4"
              d="M909.3 740.7H775.1c-5.5 0-10-4.5-10-10v-47.9c0-5.5 4.5-10 10-10h134.2c5.5 0 10 4.5 10 10v47.9C919.3 736.2 914.8 740.7 909.3 740.7z" />
            <text transform="matrix(1.0027 0 0 1 789.6294 721.7501)" fill="#FFF" font-family='Fredoka One' font-size="38">
              OPEN </text>
            <path fill="none" class="stroke"
              d="M909.3 740.7H775.1c-5.5 0-10-4.5-10-10v-47.9c0-5.5 4.5-10 10-10h134.2c5.5 0 10 4.5 10 10v47.9C919.3 736.2 914.8 740.7 909.3 740.7z" />
          </g>
        </g>
        <g id="button">
          <path opacity="0.4" fill="#0170BB"
            d="M650.5 725.5H547.8c-4.7 0-8.6-3.9-8.6-8.6v-18.1c0-4.7 3.9-8.6 8.6-8.6h102.7c4.7 0 8.6 3.9 8.6 8.6v18.1C659.2 721.7 655.3 725.5 650.5 725.5z" />
          <path fill="none" class="stroke"
            d="M650.5 725.5H547.8c-4.7 0-8.6-3.9-8.6-8.6v-18.1c0-4.7 3.9-8.6 8.6-8.6h102.7c4.7 0 8.6 3.9 8.6 8.6v18.1C659.2 721.7 655.3 725.5 650.5 725.5z" />
        </g>
        <g id="text">
          <line fill="none" class="stroke round-end" x1="539.2" y1="605.5" x2="652.2" y2="605.5" />
          <line fill="none" class="stroke round-end" x1="539.2" y1="630.5" x2="669.2" y2="630.5" />
          <line fill="none" class="stroke round-end" x1="539.2" y1="655.5" x2="619.2" y2="655.5" />
        </g>
        <use class="grass" xlink:href="#grass" x="130" y="0"></use>
        <rect class="grass" x="130" y="850" fill="#a5c7e4" width="100%" height="80"></rect>
      </svg>
    </div>
  </div>

  <div class="form-group p-4">
      <div class="org-selector-wrapper">
          <label class="w-50">
              <input type="radio" class="org-radio-input member_status" name="member_status" id="new_user" value="new_user">
              <div class="org-card">
                  <i class="bi bi-building"></i> {{-- ต้องมี Bootstrap Icons --}}
                  <span>ยังไม่เป็นสมาชิก</span>
              </div>
          </label>

          <label class="w-50">
              <input type="radio" class="org-radio-input member_status" name="member_status" id="member" value="member">
              <div class="org-card">
                  <i class="bi bi-mortarboard-fill"></i>
                  <span>เป็นสมาชิกแล้ว</span>
              </div>
          </label>
      </div>
  </div>
</div>

  <div class="container-fluid p-0" style="max-width: 600px; margin: 0 auto;">
    {{-- ส่วนหัว Profile แบบ Material --}}
    <div class="material-card mb-4 pb-4">
        <div class="profile-header d-none">
            <div class="mb-2">
                {{-- รูป Profile --}}
                <img src="" id="pictureUrl" class="profile-avatar" alt="Profile">
            </div>
            {{-- ชื่อ Display Name --}}
            <h5 id="display_name" class="mb-0 font-weight-bold">Guest User</h5>
            <small class="text-white-50">ลงทะเบียนสมาชิกใหม่</small>
        </div>

        <div id="new_user_form" class="d-none">
          <div class="px-4">
              <form id="registerForm">

                  {{-- 1. ชื่อ - นามสกุล (ย้ายมาไว้บนสุด เพื่อความชัดเจน) --}}
                  <div class="row g-2 mb-4">
                      <div class="col-6">
                          <div class="form-floating">
                              <input type="text" name="firstname" class="form-control" id="firstname" placeholder="ชื่อ" required>
                              <label for="firstname">ชื่อ</label>
                          </div>
                      </div>
                      <div class="col-6">
                          <div class="form-floating">
                              <input type="text" name="lastname" class="form-control" id="lastname" placeholder="นามสกุล" required>
                              <label for="lastname">นามสกุล</label>
                          </div>
                      </div>
                  </div>

                  <hr class="text-muted opacity-25 mb-4">

                  {{-- 2. เลือกประเภทหน่วยงาน (Selection Cards) --}}
                  <div class="form-group mb-4">
                      <label class="d-block text-secondary small mb-2 ps-1">สังกัดหน่วยงาน</label>
                      <div class="org-selector-wrapper">
                          <label class="w-50">
                              <input type="radio" class="org-radio-input" name="org_type_selector" id="type_general" value="general">
                              <div class="org-card">
                                  <i class="bi bi-building"></i> {{-- ต้องมี Bootstrap Icons --}}
                                  <span>เทศบาล/อบต.</span>
                              </div>
                          </label>

                          <label class="w-50">
                              <input type="radio" class="org-radio-input" name="org_type_selector" id="type_uni" value="uni" checked>
                              <div class="org-card">
                                  <i class="bi bi-mortarboard-fill"></i>
                                  <span>มหาวิทยาลัย</span>
                              </div>
                          </label>
                      </div>
                  </div>

                  {{-- 3. Dropdown ชื่อหน่วยงาน --}}
                  <div class="form-floating mb-4">
                     <input type="text" id="org_display" class="form-control clickable-input" placeholder="เลือกหน่วยงาน..." readonly>
                      <label id="org_label">ระบุชื่อมหาวิทยาลัย</label>

                      <input type="hidden" name="org_id" id="org_id">
                      <input type="hidden" name="province_id" id="province_id">
                      <input type="hidden" name="district_id" id="district_id">
                      <input type="hidden" name="tambon_id" id="tambon_id">
                  </div>

                  {{-- 4. ส่วนแสดงที่อยู่ (Readonly) --}}
                  <div id="location_info_display" class="d-none bg-light p-3 rounded-3 mb-3 border border-light">
                      <p class="small text-muted mb-2"><i class="bi bi-geo-alt-fill"></i> ที่ตั้งหน่วยงาน</p>
                      <div class="row g-2">
                          <div class="col-4">
                              <input type="text" id="show_province" class="form-control form-control-sm bg-white border-0" disabled placeholder="จ.">
                          </div>
                          <div class="col-4">
                              <input type="text" id="show_district" class="form-control form-control-sm bg-white border-0" disabled placeholder="อ.">
                          </div>
                          <div class="col-4">
                              <input type="text" id="show_tambon" class="form-control form-control-sm bg-white border-0" disabled placeholder="ต.">
                          </div>
                      </div>
                  </div>

                  {{-- 5. ส่วนรายละเอียดฟอร์ม --}}
                  <div id="form_details" class="d-none animate__animated animate__fadeIn">

                      {{-- Zone / Subzone (Clickable Inputs) --}}
                      <div class="form-floating mb-3">
                          <input type="text" id="zone_display" class="form-control clickable-input" placeholder="เลือก" readonly>
                          <label id="zone_label">หมู่ที่/โซน</label>
                          <input type="hidden" name="zone_id" id="zone_id">
                      </div>

                      <div class="form-floating mb-4">
                          <input type="text" id="subzone_display" class="form-control clickable-input" placeholder="เลือก" readonly disabled>
                          <label id="subzone_label">ซอย/อาคาร</label>
                          <input type="hidden" name="subzone_id" id="subzone_id">
                      </div>

                      {{-- สมาชิกใหม่ (กรอกเพิ่มเติม) --}}
                      <div id="new_member_div">
                          <div class="form-floating mb-3" id="address_div">
                              <input type="text" name="address" id="address" class="form-control" placeholder="บ้านเลขที่">
                              <label>บ้านเลขที่ / ห้องเลขที่</label>
                          </div>

                          <div class="form-floating mb-3">
                              <input type="tel" name="phone" id="phone" class="form-control border-danger" placeholder="เบอร์โทร">
                              <label>หมายเลขโทรศัพท์ผู้สมัคร</label>
                          </div>
                      </div>

                      <button type="button" id="phone_search_btn" class="btn btn-info text-white w-100 btn-submit-material mt-2">
                          ลงทะเบียนเข้าใช้งาน
                      </button>
                  </div>

              </form>
          </div>
        </div>
        <div id="member_form" class="d-none">
            {{-- สมาชิกเก่า (Search) --}}
            <div class="form-group  mb-3" id="old_member_div">
                <label class="text-secondary small ps-1 mb-1">ค้นหาชื่อของคุณ</label>
                <select name="user_id" id="user_id" class="select2 w-100"></select>
            </div>

        </div>
    </div>
</div>

{{-- MODAL สำหรับเลือก คณะ/สาขา (ใช้ตัวเดียววนใช้) --}}
<div class="modal fade" id="selectionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-fullscreen-sm-down">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">เลือกรายการ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="p-2 bg-light sticky-top border-bottom">
                    <input type="text" id="modalSearch" class="form-control" placeholder="ค้นหา...">
                </div>
                <div class="list-group list-group-flush" id="modalListContainer">
                    </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')



  <script src="https://code.jquery.com/jquery-3.7.1.min.js"
    integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
  <script charset="utf-8" src="https://static.line-scdn.net/liff/edge/versions/2.22.3/sdk.js"></script>
  <script src="https://unpkg.com/axios@1.6.7/dist/axios.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

  <script>
    const phone_search_btn = document.getElementById('phone_search_btn')
    const phone_text = document.getElementById('phone')
    const province_id_text = document.getElementById('province_id')
    const district_id_text = document.getElementById('district_id')
    const tambon_id_text = document.getElementById('tambon_id')
    const org_id_text = document.getElementById('org_id')
    let profile;

    const LINE_BOT_API = "https://api.line.me/v2/bot";
    const LINE_CHANNAL_ACCESS_TOKEN = "hKQpGAefzUb3nfDPG+kNim34f3uUhEm0RW8h9E2NtyAYZNtRrDTnP8J6qPyPSPvRNU3XV786SyrBZH649FugjcCrHZ4nOKWLtp/yHTdm/ZXQASL72zVoRIS/UFmTKNddkTrWTIci91qA1JinsUbxMAdB04t89/1O/w1cDnyilFU="

    const main = async () => {
      await liff.init({
        liffId: '1656703539-5eopvjK9',
      });
      if (!liff.isLoggedIn()) {
        liff.login()
        return false
      }

      profile = await liff.getProfile();

      console.log('profile', profile)
      $('#display_name').text(profile.displayName)
      $('#firstname').val(profile.displayName)
      $('#lastname').val(profile.displayName)
      $('#pictureUrl').attr('src', profile.pictureUrl)
      // 1.ส่ง line user_id ไปcheck ก่อนว่าเป็น memberไหม
      $.post(`/api/line/fine_line_id`, {
        userId: profile.userId,
      }).then(function (data) {
        console.log('dta', data)
        if (data.res == 0) {
          //หา user infoไม่เจอ เปิดให้กรอกข้อมูลเบอร์โทร
          phone_div.classList.remove('hidden')
        }
        if (data.res == 1) {
          window.location.href = '/line/dashboard/' + data.waste_pref_id+'/'+data.org_id;
        }
      })

    }

    $('.member_status').click(function(){
      $('#index_page').addClass('d-none')
      $('.profile-header').removeClass('d-none')
      let val = $(this).val()
      if(val === 'new_user'){
        $('#member_form').addClass('d-none')
        $('#new_user_form').removeClass('d-none')
      }else{
        $('#member_form').removeClass('d-none')
        $('#new_user_form').addClass('d-none')
      }
    })

    $('#to_index_page').click(function(){
      $('#index_page').removeClass('d-none')
      $('.profile-header').addClass('d-none')
      $('#member_form').removeClass('d-none').addClass('d-none')
      $('#new_user_form').removeClass('d-none').addClass('d-none')
    })

    const sendMessage = async () => {
      const body = {
        to: profile.userId,
        messages: [
          {
            type: 'text',
            text: "Hello, world1"
          },

        ]
      }
      try {
        const response = await axios.post(
          `${LINE_BOT_API}/message/push`,
          body,
          { headers }
        )
        console.log('response', response.data)
      } catch (error) {
        console.log('err', error)
      }

    }

    const logOut = async () => {
      liff.logout()
      window.location.href = '/line'
    }



    $('#phone_search_btn').on('click', async function () {//async
      let firstname = $('#firstname').val()
      let lastname = $('#lastname').val()
      let phone = phone_text.value;
      let res = true
      if(firstname ===""){
        $('#firstname').addClass('border border-danger')
        res = false
      }
      if(lastname ===""){
        $('#lastname').addClass('border border-danger')
        res = false
      }
      if(phone ===""){
        $('#phone').addClass('border border-danger')
        res = false
      }

      if(res === false){
        return false
      }

      let line_user_image = (profile.pictureUrl).replace("https://profile.line-scdn.net/", "");
      console.log(
        {
          phoneNum    : phone,
          province_id :  province_id_text.value,
          district_id : district_id_text.value,
          tambon_id   : tambon_id_text.value,
          org_id      : org_id_text.value,
          line_user_id: profile.userId,
          displayName : profile.displayName,
          line_user_image: line_user_image,
           firstname : $('#firstname').val(),
          lastname : $('#lastname').val(),
          address : $('#address').val(),
          zone_id:$('#zone_id').val(),
          subzone_id:$('#subzone_id').val(),
        }
      );
      await $.post(`/api/line/user_line_register`,
        {
          phoneNum    : phone,
          province_id :  province_id_text.value,
          district_id : district_id_text.value,
          tambon_id   : tambon_id_text.value,
          org_id      : org_id_text.value,
          line_user_id: profile.userId,
          displayName : profile.displayName,
          line_user_image: line_user_image,
          firstname : $('#firstname').val(),
          lastname : $('#lastname').val(),
          address : $('#address').val(),
          zone_id:$('#zone_id').val(),
          subzone_id:$('#subzone_id').val(),
        }, function (data) {
          console.log('data',data)
          if (data.res == 1) {
            //มีข้อมูล user อยู่แล้วและทำการ update user_line_id แล้ว
            //ให้ไปหา dashboard ของ line  user

            window.location.href = '/line/dashboard/' + data.waste_pref_id +'/'+org_id_text.value+ '/1';
            //  data.waste_pref_id +'/'+org_id_text.value+ '/register==1
          } else {
            // ไม่มีข้อมูล user เป็น new  user ให้ทำการ register
          }
        })

    })


  </script>
  <script>
    // 1. รับข้อมูล Org จาก Blade (PHP)
    const allOrganizations = @json($orgs);

    // Cache Data สำหรับ Modal
    let orgListCache = [];     // เก็บรายชื่อหน่วยงานที่กรองแล้ว
    let zoneListCache = [];
    let subzoneListCache = [];

    // ตัวบอกสถานะ Modal ว่ากำลังเลือกอะไร
    let currentModalType = ''; // 'org', 'zone', 'subzone'

    $(document).ready(function () {
        $('#user_id').select2({ width: '100%' }); // อันนี้ค้นหาชื่อเก่า เก็บ select2 ไว้ หรือจะแก้เป็น modal ก็ได้

        // เริ่มต้น: กรองหน่วยงานแบบ มหาวิทยาลัย รอไว้
        filterOrgList('uni');
    });

    // ==========================================
    // 1. จัดการเลือกประเภทหน่วยงาน (Radio Change)
    // ==========================================
    $('input[name="org_type_selector"]').change(function() {
        let type = $(this).val();
        let org_label = type !== 'uni' ? 'ระบุชื่อเทศบาล/อบต.' : 'ระบุชื่อมหาวิทยาลัย'
        $('#org_label').html(org_label)
        resetForm(); // ล้างค่าเก่าออก
        filterOrgList(type); // กรองข้อมูลใหม่ใส่ Cache
    });

    // ฟังก์ชันกรองข้อมูล (เก็บลง Array แทนการสร้าง Option)
    function filterOrgList(type) {
        orgListCache = []; // Reset Cache
        // Loop ข้อมูลดิบ แล้วเลือกเฉพาะที่ตรงประเภท
        for (const [key, org] of Object.entries(allOrganizations)) {
            let isUni = (org.org_short_type_name === 'ม.');

            if ((type === 'uni' && isUni) || (type === 'general' && !isUni)) {
                // สร้าง Object สำหรับ Modal
                orgListCache.push({
                    id: key, // key คือ ID ใน object json
                    name: `${org.org_type_name}${org.org_name}`, // ชื่อที่จะโชว์ตัวหนา
                    desc: `อ.${org.districts.district_name} จ.${org.provinces.province_name}`, // รายละเอียดตัวเล็ก
                    fullData: org // เก็บ object เต็มไว้ใช้ตอนเลือก
                });
            }
        }

        // เคลียร์ค่าที่แสดงอยู่
        $('#org_display').val('').attr('placeholder', 'แตะเพื่อเลือกหน่วยงาน...');
    }

    // ==========================================
    // 2. จัดการ Modal (รวม Org, Zone, Subzone)
    // ==========================================

    // 2.1 เปิด Modal เลือก "หน่วยงาน"
    $('#org_display').click(function(){
        currentModalType = 'org';
        $('#modalTitle').text('เลือกหน่วยงาน');
        renderModalList(orgListCache); // ส่งข้อมูลที่กรองแล้วไปแสดง
        new bootstrap.Modal(document.getElementById('selectionModal')).show();
    });

    // 2.2 เปิด Modal เลือก "คณะ/โซน"
    $('#zone_display').click(function() {
        if(!$('#org_id').val()) { alert('กรุณาเลือกหน่วยงานก่อน'); return; }

        currentModalType = 'zone';
        $('#modalTitle').text($('#zone_label').text());
        renderModalList(zoneListCache);
        new bootstrap.Modal(document.getElementById('selectionModal')).show();
    });

    // 2.3 เปิด Modal เลือก "สาขา/อาคาร"
    $('#subzone_display').click(function() {
        if($(this).is(':disabled')) return;

        currentModalType = 'subzone';
        $('#modalTitle').text($('#subzone_label').text());
        renderModalList(subzoneListCache);
        new bootstrap.Modal(document.getElementById('selectionModal')).show();
    });

    // ==========================================
    // 3. ฟังก์ชัน Render & Select
    // ==========================================

    // สร้าง List ใน Modal (ใช้ได้กับทุกประเภท)
    function renderModalList(items) {
        let html = '';
        $('#modalSearch').val('');

        if (!items || items.length === 0) {
            html = '<div class="p-4 text-center text-muted">ไม่พบข้อมูล</div>';
        } else {
            items.forEach(item => {
                // เช็คว่า data มาท่าไหน (Org มี field name/desc, Zone อาจมีแค่ zone_name)
                let name = item.name || item.zone_name || item.subzone_name;
                let desc = item.desc || item.location || '';
                let id = item.id;

                html += `
                <a href="#" class="list-group-item list-group-item-action py-3 select-item-btn"
                   data-id="${id}" data-type="${currentModalType}">
                    <div class="d-flex w-100 justify-content-between align-items-center">
                        <div>
                            <div class="fw-bold text-dark">${name}</div>
                            ${desc ? `<small class="text-muted">${desc}</small>` : ''}
                        </div>
                        <i class="bi bi-chevron-right text-muted opacity-50"></i>
                    </div>
                </a>`;
            });
        }
        $('#modalListContainer').html(html);
    }

    // เมื่อกดเลือกรายการใน Modal
    $(document).on('click', '.select-item-btn', function(e) {
        e.preventDefault();
        let id = $(this).data('id');
        let type = $(this).data('type');

        // หา Object เต็มจาก Cache (เพื่อเอาข้อมูลอื่นมาใช้)
        // สำหรับ Org เราเก็บ fullData ไว้, สำหรับ Zone/Subzone อาจจะต้อง find
        let selectedItem = null;

        if (type === 'org') {
            selectedItem = orgListCache.find(x => x.id == id);
            if(selectedItem) handleOrgSelection(selectedItem.fullData, id);

        } else if (type === 'zone') {
            let name = $(this).find('.fw-bold').text(); // ดึงชื่อจาก HTML หรือจะหาจาก cache ก็ได้
            $('#zone_id').val(id);
            $('#zone_display').val(name);

            // Logic โหลด Subzone (สาขา)
            fetchSubzones(id);

        } else if (type === 'subzone') {
            let name = $(this).find('.fw-bold').text();
            $('#subzone_id').val(id);
            $('#subzone_display').val(name);
        }

        // ปิด Modal
        bootstrap.Modal.getInstance(document.getElementById('selectionModal')).hide();
    });

    // ==========================================
    // 4. Logic เฉพาะเมื่อเลือกหน่วยงาน (แยกออกมาให้ชัด)
    // ==========================================
    function handleOrgSelection(orgData, id) {
        // 1. Set ค่าพื้นฐาน
        $('#org_id').val(orgData.id); // ใช้ ID จริงจาก DB
        $('#org_display').val(`${orgData.org_type_name}${orgData.org_name}`);

        // 2. Set Hidden Location
        $('#province_id').val(orgData.org_province_id_fk);
        $('#district_id').val(orgData.org_district_id_fk);
        $('#tambon_id').val(orgData.org_tambon_id_fk);

        // 3. Set Display Location (สำหรับ อบต.)
        $('#show_province').val(orgData.provinces.province_name);
        $('#show_district').val(orgData.districts.district_name);
        $('#show_tambon').val(orgData.tambons.tambon_name);

        // 4. Check Type (ม. หรือ อบต.)
        let isUni = (orgData.org_short_type_name === 'ม.');

        if (isUni) {
            // === มหาวิทยาลัย ===
            $('#location_info_display').addClass('d-none');
            $('#address_div').addClass('d-none');
            $('#address').val('-');

            $('#zone_label').text('คณะ / หน่วยงาน');
            $('#subzone_label').text('สาขาวิชา / ภาควิชา');
            $('#zone_display').attr('placeholder', 'แตะเพื่อเลือกคณะ...');
            $('#subzone_display').attr('placeholder', 'แตะเพื่อเลือกสาขา...');
        } else {
            // === อบต. ===
            $('#location_info_display').removeClass('d-none');
            $('#address_div').removeClass('d-none');
            if($('#address').val() === '-') $('#address').val('');

            $('#zone_label').text('หมู่ที่ / โซน');
            $('#subzone_label').text('ซอย / อาคาร');
            $('#zone_display').attr('placeholder', 'แตะเพื่อเลือกหมู่...');
            $('#subzone_display').attr('placeholder', 'แตะเพื่อเลือกซอย...');
        }

        // 5. Reset & Load Zones
        $('#form_details').removeClass('d-none');
        $('#zone_id').val('');
        $('#zone_display').val('');
        $('#subzone_id').val('');
        $('#subzone_display').val('').prop('disabled', true);

        // AJAX Get Zones
        $.get(`/zones/getzones/${orgData.tambons.id}`).done(function (data) {
            zoneListCache = data.zones || [];
        });
    }

    // ฟังก์ชันย่อย: โหลด Subzone
    function fetchSubzones(zoneId) {
        $('#subzone_id').val('');
        $('#subzone_display').val('กำลังโหลด...').prop('disabled', true);

        // ** อย่าลืมแก้ URL ให้ตรงกับ Route ของคุณ **
        $.get(`/api/subzone/get_subzones_in_zone/${zoneId}`).done(function(data) {
            subzoneListCache = data || [];
            if(subzoneListCache.length > 0) {
                $('#subzone_display').val('').prop('disabled', false).attr('placeholder', 'แตะเพื่อเลือก...');
            } else {
                $('#subzone_display').val('-').prop('disabled', true);
            }
        }).fail(function(){
             $('#subzone_display').val('-').prop('disabled', true);
        });
    }

    // Reset Form Helper
    function resetForm() {
        $('#form_details').addClass('d-none');
        $('#org_id').val('');
        $('#org_display').val('');
        $('#zone_id').val('');
        $('#subzone_id').val('');
        zoneListCache = [];
        subzoneListCache = [];
    }

    // Search ใน Modal
    $('#modalSearch').on('keyup', function() {
        let value = $(this).val().toLowerCase();
        $("#modalListContainer a").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });
    main()
</script>
@endsection
