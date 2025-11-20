<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"
        integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        .hidden {
            display: none
        }

        * {
            border: 0;
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --hue: 184;
            --bg: hsl(var(--hue), 10%, 90%);
            --fg: hsl(var(--hue), 66%, 24%);
            --primary: hsl(var(--hue), 66%, 44%);
            --gradient: linear-gradient(145deg,
                    hsl(var(--hue), 10%, 85%),
                    hsl(var(--hue), 10%, 100%));
            font-size: 16px;
        }

        body,
        button {
            color: var(--fg);
            font: 1em/1.5 "Nunito", sans-serif;
        }

        body {
            background: var(--bg);
            height: 100vh;
            display: grid;
            place-items: center;
            padding: 1.5em 0 0 0;
        }

        body:after {
            content: "";
            display: block;
            height: 1.5em;
            width: 100%;
        }

        /* All */
        .app,
        .header,
        .main__date-nav,
        .main__stat-row,
        .main__stat-graph,
        .footer {
            display: flex;
        }

        .header,
        .main__date-nav,
        .footer {
            justify-content: space-between;
        }

        .header__profile-btn,
        .header__notes-btn,
        .main__date-arrow-btn,
        .main__date-edit-btn,
        .footer__nav-btn {
            background: transparent;
            display: flex;
            outline: transparent;
            transition: all 0.15s linear;
            -webkit-appearance: none;
            appearance: none;
            -webkit-tap-highlight-color: transparent;
        }

        .app {
            background: hsl(var(--hue), 10%, 85%);
            border-radius: 3em;
            flex-direction: column;
            padding: 2.25em;
            /* width: 24.375em;
            height: 52.75em; */
        }

        .app__gradients {
            position: absolute;
            width: 1px;
            height: 1px;
        }

        .icon {
            display: block;
            margin: auto;
            width: 1.5em;
            height: 1.5em;
        }

        .icon circle,
        .icon path {
            fill: currentColor;
            transition: fill 0.15s linear;
        }

        .icon ellipse,
        .icon polygon {
            stroke: currentColor;
            transition: stroke 0.15s linear;
        }

        .icon .no-fill {
            fill: none;
            stroke: currentColor;
        }

        .icon--red path {
            fill: hsl(3, 90%, 55%);
        }

        .icon--pulse {
            animation: bpm 1s linear, pulse 0.75s 1s linear infinite;
        }

        .ring,
        .sr-only {
            position: absolute;
        }

        .ring {
            display: block;
            inset: 0;
            width: 100%;
            height: auto;
        }

        .ring-fill,
        .ring-stroke {
            stroke: url("#ring");
        }

        .ring-stroke {
            animation-duration: 1s;
            animation-timing-function: ease-in-out;
        }

        .ring-stroke--steps {
            animation-name: stepCount;
        }

        .ring-stroke--cals {
            animation-name: cals;
        }

        .ring-stroke--miles {
            animation-name: miles;
        }

        .ring-stroke--mins {
            animation-name: mins;
        }

        .ring-stroke--stepHrs {
            animation-name: stepHrs;
        }

        .ring-track {
            stroke: hsl(var(--hue), 10%, 80%);
        }

        .sr-only {
            clip: rect(1px, 1px, 1px, 1px);
            overflow: hidden;
            width: 1px;
            height: 1px;
        }

        /* Header */
        .header {
            display: flex;
            justify-content: space-between;
            /* margin-bottom: 1.5em; */
        }

        .header__profile-btn,
        .header__notes-btn {
            width: 6em;
            height: 6em;
        }

        .header__profile-btn {
            border-radius: 1em;
            box-shadow: 0 0 0 0.125em inset;
        }

        .header__notes-btn {
            margin-inline-end: -1em;
        }

        .header__profile-btn:active,
        .header__notes-btn:active {
            transform: scale(0.9);
        }

        .header__profile-btn:focus {
            box-shadow: 0 0 0 0.125em var(--primary) inset;
        }

        .header__profile-icon {
            border-radius: 0.5em;
            margin: auto;
            width: 5em;
            height: 5em;
        }

        .header__notes-btn:focus .icon path {
            fill: var(--primary);
        }

        /* Main */
        .main__date-nav {
            /* margin-bottom: 1em; */
        }

        .main__date-arrow-btn,
        .main__date-edit-btn {
            height: 1.5em;
        }

        .main__date-arrow-btn {
            width: 1.5em;
        }

        .main__date-arrow-btn:active .icon path,
        .main__date-arrow-btn:focus .icon path {
            fill: var(--primary);
        }

        .main__date {
            text-transform: uppercase;
            display: flex;
            flex-direction: row
        }

        .main__date-edit-btn {
            min-width: 1.5em;
        }

        .main__date-edit-btn:active,
        .main__date-edit-btn:focus {
            color: var(--primary);
        }

        .main__stat-blocks {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            grid-gap: 1.5em;
            margin-bottom: 1.5em;
        }

        .main__stat-block:active {
            box-shadow: 0.75em 0.75em 1.5em hsl(var(--hue), 5%, 65%),
                -0.75em -0.75em 1.5em hsl(0, 0%, 100%);
            transform: scale(1.1);
        }

        .main__stat-block {
            background: var(--gradient);
            border-radius: 1.5em;
            box-shadow: -0.75em -0.75em 2.25em hsl(0, 0%, 100%),
                0.75em 0.75em 2.25em hsl(var(--hue), 5%, 65%);
            padding: 0.75em;
            text-align: center;
            width: 100%;
        }

        .main__stat-block--lg {
            grid-column: 1 / 4;
            padding: 1.5em;
        }

        .main__stat-rows,
        .main__stat-row {
            margin-bottom: 1.5em;
        }

        .main__stat-row {
            align-items: center;
        }

        .main__stat-graph {
            margin: 0 auto 0.75em auto;
            position: relative;
            width: 3.75em;
            height: 3.75em;
        }

        .main__stat-graph .main__stat-detail {
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: absolute;
            inset: 0;
        }

        .main__stat-block--lg .main__stat-graph {
            margin: auto;
            width: 11.25em;
            height: 11.25em;
        }

        .main__stat-block--lg .icon {
            margin: 0 auto;
            width: 2.25em;
            height: 2.25em;
        }

        .main__stat-row .main__stat-graph {
            background: var(--gradient);
            border-radius: 1em;
            box-shadow: -0.75em -0.75em 2.25em hsl(0, 0%, 100%),
                0.75em 0.75em 2.25em hsl(var(--hue), 5%, 65%);
            margin: 0;
            margin-inline-end: 1.5em;
        }

        .main__stat-value,
        .main__stat-unit {
            display: block;
        }

        .main__stat-value {
            font-size: 1.25em;
            line-height: 1.2;
        }

        .main__stat-block--lg .main__stat-value {
            font-size: 2em;
            line-height: 1.5;
        }

        .main__stat-unit,
        .main__stat-subtext {
            font-weight: 300;
        }

        .main__stat-subtext {
            color: hsl(var(--hue), 10%, 30%);
        }

        .main__stat-graph--filled,
        .main__stat-graph--filled .ring-fill {
            animation-duration: 0.3s;
            animation-delay: 1s;
            animation-fill-mode: forwards;
        }

        .main__stat-graph--filled {
            animation-name: statFill;
            animation-timing-function: linear;
        }

        .main__stat-graph--filled .ring-fill {
            animation-name: ringFill;
            animation-timing-function: ease-in;
        }

        /* Footer */
        .footer {
            /* margin-top: auto; */
            position: fixed;
            /* กำหนดให้องค์ประกอบมีตำแหน่งคงที่ */
            bottom: 0;
            /* ให้อยู่ที่ขอบด้านล่างสุด */
            left: 0;
            /* ให้อยู่ที่ขอบด้านซ้ายสุด */
            width: 100%;
            /* ให้มีความกว้างเต็มหน้าจอ */
            z-index: 1000;
            /* (optional) กำหนดลำดับการแสดงผลให้อยู่ด้านบนองค์ประกอบอื่น */
            /* คุณอาจต้องเพิ่ม background-color เพื่อให้ footer ไม่โปร่งใสทับเนื้อหาด้านล่าง */
            /* background-color: #ffffff; */
            /* หรือสีพื้นหลังตามดีไซน์เดิมของคุณ */
        }

        .footer__nav-btn {
            background: var(--primary);
            border-radius: 10%;
            /* box-shadow: 1em 1em 2em hsl(var(--hue), 5%, 65%),
                -1em -1em 2em hsl(0, 0%, 100%); */
            width: 7.8em;
            height: 4em;
            text-align: center
        }

        .footer__nav-btn:active {
            box-shadow: 0.75em 0.75em 1.5em hsl(var(--hue), 5%, 65%),
                -0.75em -0.75em 1.5em hsl(0, 0%, 100%);
            transform: scale(0.9);
        }

        .footer__nav-btn:focus .icon circle,
        .footer__nav-btn:focus .icon path {
            fill: var(--primary);
        }

        /* Dark theme */
        @media (prefers-color-scheme: dark) {
            :root {
                --bg: hsl(var(--hue), 10%, 10%);
                --fg: hsl(var(--hue), 66%, 94%);
                --primary: hsl(var(--hue), 66%, 44%);
                --gradient: linear-gradient(145deg,
                        hsl(var(--hue), 10%, 15%),
                        hsl(var(--hue), 10%, 30%));
            }

            .app {
                background: hsl(var(--hue), 10%, 20%);
            }

            .icon--red path {
                fill: hsl(3, 90%, 65%);
            }

            .ring-track {
                stroke: hsl(var(--hue), 10%, 30%);
            }

            .main__stat-block,
            .main__stat-row .main__stat-graph {
                box-shadow: -0.75em -0.75em 2.25em hsl(var(--hue), 10%, 30%),
                    0.75em 0.75em 2.25em hsl(var(--hue), 5%, 5%);
            }

            .main__stat-subtext {
                color: hsl(var(--hue), 10%, 70%);
            }

            .footer__nav-btn {
                box-shadow: -1em -1em 2em hsl(var(--hue), 10%, 30%),
                    1em 1em 2em hsl(var(--hue), 5%, 5%);
            }

            .footer__nav-btn:active {
                box-shadow: -0.75em -0.75em 1.5em hsl(var(--hue), 10%, 30%),
                    0.75em 0.75em 1.5em hsl(var(--hue), 5%, 5%);
            }
        }

        /* Animations */
        @keyframes statFill {
            from {
                color: var(--fg);
            }

            to {
                color: hsl(var(--hue), 66%, 94%);
            }
        }

        @keyframes ringFill {
            from {
                r: 82px;
                stroke-width: 16;
            }

            to {
                r: 45px;
                stroke-width: 90;
            }
        }

        @keyframes stepCount {
            from {
                stroke-dashoffset: 515.22;
            }

            to {
                stroke-dashoffset: 0;
            }
        }

        @keyframes cals {
            from {
                stroke-dashoffset: 163.36;
            }

            to {
                stroke-dashoffset: 12.25;
            }
        }

        @keyframes miles {
            from {
                stroke-dashoffset: 163.36;
            }

            to {
                stroke-dashoffset: 35.39;
            }
        }

        @keyframes mins {
            from {
                stroke-dashoffset: 163.36;
            }

            to {
                stroke-dashoffset: 65.34;
            }
        }

        @keyframes bpm {
            from {
                transform: scale(0);
            }

            37.5% {
                transform: scale(1.2);
            }

            75%,
            to {
                transform: scale(1);
            }
        }

        @keyframes stepHrs {
            from {
                stroke-dashoffset: 131.95;
            }

            to {
                stroke-dashoffset: 52.78;
            }
        }

        @keyframes pulse {

            from,
            75%,
            to {
                transform: scale(1);
            }

            25% {
                transform: scale(0.9);
            }

            50% {
                transform: scale(1.2);
            }
        }


        .modal-open {
            overflow: hidden;
        }

        .modal {
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1055;
            display: none;
            width: 100%;
            height: 100%;
            overflow-x: hidden;
            overflow-y: auto;
            outline: 0;
        }

        .modal-dialog {
            position: relative;
            width: auto;
            margin: 0.5rem;
            pointer-events: none;
        }

        .modal.fade .modal-dialog {
            transition: transform .3s ease-out;
            transform: translate(0, -50px);
        }

        @media (prefers-reduced-motion: reduce) {
            .modal.fade .modal-dialog {
                transition: none;
            }
        }

        .modal.show .modal-dialog {
            transform: none;
        }

        .modal.modal-static .modal-dialog {
            transform: scale(1.02);
        }

        .modal-dialog-scrollable {
            height: calc(100% - 1rem);
        }

        .modal-dialog-scrollable .modal-content {
            max-height: 100%;
            overflow: hidden;
        }

        .modal-dialog-scrollable .modal-body {
            overflow-y: auto;
        }

        .modal-dialog-centered {
            display: flex;
            align-items: center;
            min-height: calc(100% - 1rem);
        }

        .modal-content {
            position: relative;
            display: flex;
            flex-direction: column;
            width: 100%;
            pointer-events: auto;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid rgba(0, 0, 0, .2);
            border-radius: 0.3rem;
            outline: 0;
        }

        .modal-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1050;
            width: 100vw;
            height: 100vh;
            background-color: #000;
        }

        .modal-backdrop.fade {
            opacity: 0;
        }

        .modal-backdrop.show {
            opacity: 0.5;
        }

        .modal-header {
            display: flex;
            flex-shrink: 0;
            align-items: center;
            justify-content: space-between;
            padding: 1rem 1rem;
            border-bottom: 1px solid #dee2e6;
            border-top-left-radius: calc(0.3rem - 1px);
            border-top-right-radius: calc(0.3rem - 1px);
        }

        .modal-header .btn-close {
            padding: 0.5rem 0.5rem;
            margin: -0.5rem -0.5rem -0.5rem auto;
        }

        .modal-title {
            margin-bottom: 0;
            line-height: 1.5;
        }

        .modal-body {
            position: relative;
            flex: 1 1 auto;
            padding: 1rem;
        }

        .modal-footer {
            display: flex;
            flex-wrap: wrap;
            flex-shrink: 0;
            align-items: center;
            justify-content: flex-end;
            padding: 0.75rem;
            border-top: 1px solid #dee2e6;
            border-bottom-right-radius: calc(0.3rem - 1px);
            border-bottom-left-radius: calc(0.3rem - 1px);
        }

        .modal-footer>* {
            margin: 0.25rem;
        }

        a {
            text-decoration: none;
            color: var(--fg);
        }



        :root {
            /* COLORS */
            --tab-color: #191919;
            --white-color: #fff;
            --home-icon-color: #00f7ff;
            --heart-icon-color: #ff0000;
            --plus-icon-color: #adff2f;
            --user-icon-color: #ee82ee;
            --bell-icon-color: #ffff00;
        }

        /* ------------ BASE ------------ */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            list-style: none;
        }

        li {
            display: inline-block;
        }

        /* ------------ MENU ------------ */
        .nav {

            background-color: var(--tab-color);
            width: 100%;
            height: 4em;
            border-radius: 2em;
            padding: 0 2em;
            box-shadow: 0 1em 1em rgba(0, 0, 0, 0.2);

            display: flex;
            align-items: center;
            bottom: 0;
            position: fixed;
            overflow: hidden;
            z-index: 1020;
        }

        .nav__links {
            width: 100%;
            display: flex;
            justify-content: space-between;
            text-align: center
        }

        .nav__link a {
            color: var(--white-color);
            font-size: 1.5rem;
            opacity: 0.5;
        }

        .nav__link .text {
            color: #ffffff;

        }

        .nav__light {
            position: absolute;
            top: 0;
            left: 2em;
            background-color: var(--white-color);
            width: 5em;
            height: 0.4em;
            border-radius: 2px;

            display: flex;
            justify-content: center;

            transition: 0.3s ease;
        }

        .nav__light::before {
            content: "";
            width: 5em;
            height: 7em;
            position: absolute;
            top: 0.4em;
            background: linear-gradient(to bottom,
                    rgba(255, 255, 255, 0.3) -50%,
                    rgba(255, 255, 255, 0) 90%);
            clip-path: polygon(30% 0, 70% 0, 100% 100%, 0% 100%);
        }

        .nav__link.active a {
            opacity: 1;

        }

        .nav__link.active a .fa-home-alt-2 {
            color: var(--home-icon-color);
            text-shadow: 0 0 15px var(--home-icon-color), 0 0 30px var(--home-icon-color),
                0 0 45px var(--home-icon-color), 0 0 60px var(--home-icon-color);
        }

        .nav__link:nth-child(1).active~.nav__light {
            background-color: var(--home-icon-color);
        }

        .nav__link.active a .fa-heart {
            color: var(--heart-icon-color);
            text-shadow: 0 0 15px var(--heart-icon-color),
                0 0 30px var(--heart-icon-color), 0 0 45px var(--heart-icon-color),
                0 0 60px var(--heart-icon-color);
        }

        .nav__link:nth-child(2).active~.nav__light {
            background-color: var(--heart-icon-color);
        }

        .nav__link.active a .fa-plus-circle {
            color: var(--plus-icon-color);
            text-shadow: 0 0 15px var(--plus-icon-color), 0 0 30px var(--plus-icon-color),
                0 0 45px var(--plus-icon-color), 0 0 60px var(--plus-icon-color);
        }

        .nav__link:nth-child(3).active~.nav__light {
            background-color: var(--plus-icon-color);
        }

        .nav__link.active a .fa-user {
            color: var(--user-icon-color);
            text-shadow: 0 0 15px var(--user-icon-color), 0 0 30px var(--user-icon-color),
                0 0 45px var(--user-icon-color), 0 0 60px var(--user-icon-color);
        }

        .nav__link:nth-child(4).active~.nav__light {
            background-color: var(--user-icon-color);
        }

        .nav__link.active a .fa-bell {
            color: var(--bell-icon-color);
            text-shadow: 0 0 15px var(--bell-icon-color), 0 0 30px var(--bell-icon-color),
                0 0 45px var(--bell-icon-color), 0 0 60px var(--bell-icon-color);
        }

        .nav__link:nth-child(5).active~.nav__light {
            background-color: var(--bell-icon-color);
        }
        .hidden{
            display: none
        }

        #button {
    position: absolute;
    height: 40px;
    width: 40px;
    border: 4px solid #B20000;

    background-color: #FF0000;
    color: #FFFFFF;
    box-shadow: 5px 5px 5px #888;
    text-align: center;
    border-radius: 20px;
    font-weight: bold;
    /* line-height: 35px;     */
    /* margin: 0 auto; */
}

 #button a {
    text-decoration: none;
    color: #FFFFFF;
    font-size: 135%;
    position: relative;
}

#center {
    position: relative;
    left: 0;
    /* max-width: 600px; */
}

.menu {
  height: 100%;
  width: 70%;
  position: fixed;
  z-index: 1;
  top: 0;
  left: 0;
  background-color: #1ce0e7;
  display: none;
  transition: 0.5s;
  padding-top: 60px;
  /* text-align: center; */
}
.menu a {
  text-decoration: dotted;
  /* padding: 8px 8px 8px 20px; */
  font-size: 20px;
  color: #000000;
  display: block;
  transition: 0.3s;
}

.menu a:hover {
  color: #f1f1f1;
}
.menu .closebtn {
  position: absolute;
  top: 0;
  right: 25px;
  font-size: 36px;
  margin-left: 50px;
}
.menu button {
  border: 2px solid black;
  background-color: white;
  color: black;
  padding: 9px 19px;
  font-size: 16px;
  cursor: pointer;
  border-radius:8px;
}

    </style>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui-touch-punch/0.2.3/jquery.ui.touch-punch.min.js"></script>
</head>

<body id="body">
    <div id="center">
  <div id="button">
      <a href='#'>&#9776;</a>
  </div>
</div>
<div class="menu">
  <button class="button closebtn hide">&times;</button>
  <a href="#">Home</a>
  <a href="#" class="main_bottom_nav" data-id="recycle">ขยะรีไซเคิล</a>
  <a href="#" class="main_bottom_nav" data-id="wet">ขยะเปียก</a>
  <a href="#">งานประปา</a>
  <a href="#">ตลาดชุมชน ออนไลน์</a>
  <a href="#">Telegram</a>
  <a href="#">Twitter</a>
</div>
    <div class="app">
        <svg class="app__gradients" hidden>
            <defs>
                <linearGradient id="ring" x1="1" y1="0" x2="0" y2="1">
                    <stop offset="0%" stop-color="hsl(184,66%,54%)" />
                    <stop offset="100%" stop-color="hsl(184,66%,34%)" />
                </linearGradient>
            </defs>
        </svg>
        <header class="header" style="justify-content: normal !important;">
            <button class="header__profile-btn" type="button">
                <img class="header__profile-icon" id="header__profile_img" alt="Profile (Mr. Trololo)"
                    src="https://profile.line-scdn.net/{{$userWastePref->user->image}}" width="78" height="78">
            </button>
            <button class="header__notes-btn" style="flex-direction:column !important;width: 70%; text-align:right;"
                type="button" title="Notifications">
                <div style="font-size: 1.9em">{{$userWastePref->user->firstname}}</div>
                <div style="font-size: 1.6em">{{$userWastePref->user->lastname}}</div>
            </button>
        </header>
        <main>
            <div class="main__date-nav">
               
                <div class="main__date d-flex flex-row">
                    <img src="{{asset('logo/ko_envsogo.png')}}" alt="" style="width:35%">
                    <strong style="margin-top: 11%;">
                        <span style="font-size:1.5rem; text-align: center; ">Envsogo</span>
                    </strong>
                </div>
            </div>
            <div class="kp div_recycle">
                <div class="main__stat-blocks">
                    <div class="main__stat-block main__stat-block--lg">
                        <div class="main__stat-graph main__stat-graph--filled">
                            <svg class="ring" viewBox="0 0 180 180" height="180" width="180"
                                xmlns="http://www.w3.org/2000/svg">
                                <circle class="ring-track" cx="90" cy="90" r="82" fill="none" stroke="#7f7f7f"
                                    stroke-width="16" />
                                <circle class="ring-stroke ring-stroke--steps" cx="90" cy="90" r="82" fill="none"
                                    stroke="#000" stroke-linecap="round" stroke-width="16" stroke-dasharray="515.22 515.22"
                                    stroke-dashoffset="0" transform="rotate(-90,90,90)" />
                                <circle class="ring-fill" cx="90" cy="90" r="0" fill="none" transform="rotate(-90,90,90)" />
                            </svg>
                            <div class="main__stat-detail">
                                {{-- <svg role="img" aria-label="Footprints" class="icon" viewBox="0 0 36 36" height="36"
                                    width="36" xmlns="http://www.w3.org/2000/svg">
                                    <path class="no-fill" fill="none" stroke="#000" stroke-width="2"
                                        d="M 14.831 17.296 C 13.365 17.803 12 18.046 10.142 18.623 C 10.87 27.73 19.472 24.186 14.831 17.296 Z M 14.236 15.036 C 14.26 13.771 14.191 12.55 14.74 11.349 C 15.362 10.06 15.461 8.925 15.115 7.054 C 14.493 3.647 13.171 1.521 11.389 1.055 C 7.586 0.499 7.113 4.24 7.022 6.974 C 6.812 8.503 8.106 15.054 9.669 16.162 C 11.205 15.77 12.713 15.386 14.236 15.036 Z" />
                                    <path class="no-fill" fill="none" stroke="#000" stroke-width="2"
                                        d="M 21.184 28.252 C 21.184 28.252 24.001 28.918 25.859 29.496 C 25.128 38.603 16.542 35.143 21.184 28.252 Z M 21.764 26.007 C 21.741 24.741 21.807 23.525 21.261 22.32 C 20.64 21.031 20.541 19.9 20.885 18.026 C 21.508 14.618 22.828 12.495 24.61 12.029 C 28.417 11.471 28.888 15.211 28.977 17.945 C 29.187 19.475 27.897 26.027 26.332 27.135 C 24.799 26.743 23.288 26.357 21.764 26.007 Z" />
                                </svg> --}}
                                <strong
                                    class="main__stat-value">{{ $userWastePref->purchase_transactions[0]->total_amounts ?? '0.00' }}</strong>
                                <span class="main__stat-unit">ยอดเงินคงเหลือ</span>
                                <strong
                                    class="main__stat-value">{{ $userWastePref->purchase_transactions[0]->total_points ?? '0.00' }}</strong>
                                <span class="main__stat-unit">แต้มสะสม</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="main__stat-blocks">

                    <div class="main__stat-block" data-bs-toggle="modal" data-bs-target="#exampleModal">
                        <div class="main__stat-graph">
                            <svg class="ring" viewBox="0 0 60 60" height="60" width="60" xmlns="http://www.w3.org/2000/svg">
                                <circle class="ring-track" cx="30" cy="30" r="26" fill="none" stroke="#7f7f7f"
                                    stroke-width="8" />
                                <circle class="ring-stroke ring-stroke--cals" cx="30" cy="30" r="26" fill="none"
                                    stroke="#000" stroke-linecap="round" stroke-width="8" stroke-dasharray="163.36 163.36"
                                    stroke-dashoffset="12.25" transform="rotate(-90,30,30)" />
                            </svg>
                            <svg role="img" aria-label="Flame" class="icon" viewBox="0 0 24 24" height="24" width="24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path class="no-fill" fill="none" stroke="#000" stroke-width="2"
                                    d="M 14.505 1 C 11.546 1.356 10.354 12.419 10.272 12.478 C 10.189 12.538 6.773 6.184 6.773 6.184 C 6.773 6.184 3.855 8.381 4 14 C 4.2 18 5.868 23.067 12.177 22.999 C 18.488 22.932 20.1 18 20 14 C 19.9 10 17.533 10.05 15.964 6.738 C 14.638 3.939 14.505 1 14.505 1 Z" />
                            </svg>
                        </div>
                        <div class="main__stat-detail" style="margin-left: 0.5rem">
                            <strong class="main__stat-value"> QR Code ขายขยะ</strong>
                        </div>
                    </div>
                    <div class="main__stat-block">
                        <a href="{{route('keptkayas.shop.index')}}">
                            <div class="main__stat-graph">
                                <svg class="ring" viewBox="0 0 60 60" height="60" width="60"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <circle class="ring-track" cx="30" cy="30" r="26" fill="none" stroke="#7f7f7f"
                                        stroke-width="8" />
                                    <circle class="ring-stroke ring-stroke--miles" cx="30" cy="30" r="26" fill="none"
                                        stroke="#000" stroke-linecap="round" stroke-width="8"
                                        stroke-dasharray="163.36 163.36" stroke-dashoffset="35.39"
                                        transform="rotate(-90,30,30)" />
                                </svg>
                                <svg role="img" aria-label="Location marker" class="icon" viewBox="0 0 24 24" height="24"
                                    width="24" xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M12 2C7.6 2 4 5.6 4 10C4 15.4 11 21.5 11.3 21.8C11.5 21.9 11.8 22 12 22C12.2 22 12.5 21.9 12.7 21.8C13 21.5 20 15.4 20 10C20 5.6 16.4 2 12 2ZM12 19.7C9.9 17.7 6 13.4 6 10C6 6.7 8.7 4 12 4C15.3 4 18 6.7 18 10C18 13.3 14.1 17.7 12 19.7ZM12 6C9.8 6 8 7.8 8 10C8 12.2 9.8 14 12 14C14.2 14 16 12.2 16 10C16 7.8 14.2 6 12 6ZM12 12C10.9 12 10 11.1 10 10C10 8.9 10.9 8 12 8C13.1 8 14 8.9 14 10C14 11.1 13.1 12 12 12Z" />
                                </svg>
                            </div>
                            <div class="main__stat-detail">
                                <strong class="main__stat-value">Shopping Cart</strong>
                            </div>
                        </a>
                    </div>

                </div>
                <div class="main__stat-blocks">

                    <div class="main__stat-block" id="qrcosde">
                        <div class="main__stat-graph">
                            <svg class="ring" viewBox="0 0 60 60" height="60" width="60" xmlns="http://www.w3.org/2000/svg">
                                <circle class="ring-track" cx="30" cy="30" r="26" fill="none" stroke="#7f7f7f"
                                    stroke-width="8" />
                                <circle class="ring-stroke ring-stroke--cals" cx="30" cy="30" r="26" fill="none"
                                    stroke="#000" stroke-linecap="round" stroke-width="8" stroke-dasharray="163.36 163.36"
                                    stroke-dashoffset="12.25" transform="rotate(-90,30,30)" />
                            </svg>
                            <svg role="img" aria-label="Flame" class="icon" viewBox="0 0 24 24" height="24" width="24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path class="no-fill" fill="none" stroke="#000" stroke-width="2"
                                    d="M 14.505 1 C 11.546 1.356 10.354 12.419 10.272 12.478 C 10.189 12.538 6.773 6.184 6.773 6.184 C 6.773 6.184 3.855 8.381 4 14 C 4.2 18 5.868 23.067 12.177 22.999 C 18.488 22.932 20.1 18 20 14 C 19.9 10 17.533 10.05 15.964 6.738 C 14.638 3.939 14.505 1 14.505 1 Z" />
                            </svg>
                        </div>
                        <a href="{{ route('keptkayas.recycle_classify') }}">
                            <div class="main__stat-detail" style="">
                                <strong class="main__stat-value">ราคา/วิธีคัดแยกขยะ</strong>
                                {{-- <span class="main__stat-unit">Cals</span> --}}
                            </div>
                        </a>
                    </div>
                    <div class="main__stat-block">
                        <div class="main__stat-graph">
                            <svg class="ring" viewBox="0 0 60 60" height="60" width="60" xmlns="http://www.w3.org/2000/svg">
                                <circle class="ring-track" cx="30" cy="30" r="26" fill="none" stroke="#7f7f7f"
                                    stroke-width="8" />
                                <circle class="ring-stroke ring-stroke--miles" cx="30" cy="30" r="26" fill="none"
                                    stroke="#000" stroke-linecap="round" stroke-width="8" stroke-dasharray="163.36 163.36"
                                    stroke-dashoffset="35.39" transform="rotate(-90,30,30)" />
                            </svg>
                            <svg role="img" aria-label="Location marker" class="icon" viewBox="0 0 24 24" height="24"
                                width="24" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M12 2C7.6 2 4 5.6 4 10C4 15.4 11 21.5 11.3 21.8C11.5 21.9 11.8 22 12 22C12.2 22 12.5 21.9 12.7 21.8C13 21.5 20 15.4 20 10C20 5.6 16.4 2 12 2ZM12 19.7C9.9 17.7 6 13.4 6 10C6 6.7 8.7 4 12 4C15.3 4 18 6.7 18 10C18 13.3 14.1 17.7 12 19.7ZM12 6C9.8 6 8 7.8 8 10C8 12.2 9.8 14 12 14C14.2 14 16 12.2 16 10C16 7.8 14.2 6 12 6ZM12 12C10.9 12 10 11.1 10 10C10 8.9 10.9 8 12 8C13.1 8 14 8.9 14 10C14 11.1 13.1 12 12 12Z" />
                            </svg>
                        </div>
                        <div class="main__stat-detail">
                            <strong class="main__stat-value">ประวัติการขายขยะ</strong>
                            {{-- <span class="main__stat-unit">Miles</span> --}}
                        </div>
                    </div>

                </div>
            </div>
            <div class="kp div_wet hidden">
                 <div class="main__stat-blocks">
                    <div class="main__stat-block main__stat-block--lg">
                        <div class="main__stat-graph main__stat-graph--filled">
                            <svg class="ring" viewBox="0 0 180 180" height="180" width="180"
                                xmlns="http://www.w3.org/2000/svg">
                                <circle class="ring-track" cx="90" cy="90" r="82" fill="none" stroke="#7f7f7f"
                                    stroke-width="16" />
                                <circle class="ring-stroke ring-stroke--steps" cx="90" cy="90" r="82" fill="none"
                                    stroke="#000" stroke-linecap="round" stroke-width="16" stroke-dasharray="515.22 515.22"
                                    stroke-dashoffset="0" transform="rotate(-90,90,90)" />
                                <circle class="ring-fill" cx="90" cy="90" r="0" fill="none" transform="rotate(-90,90,90)" />
                            </svg>
                            <div class="main__stat-detail">
                                {{-- <svg role="img" aria-label="Footprints" class="icon" viewBox="0 0 36 36" height="36"
                                    width="36" xmlns="http://www.w3.org/2000/svg">
                                    <path class="no-fill" fill="none" stroke="#000" stroke-width="2"
                                        d="M 14.831 17.296 C 13.365 17.803 12 18.046 10.142 18.623 C 10.87 27.73 19.472 24.186 14.831 17.296 Z M 14.236 15.036 C 14.26 13.771 14.191 12.55 14.74 11.349 C 15.362 10.06 15.461 8.925 15.115 7.054 C 14.493 3.647 13.171 1.521 11.389 1.055 C 7.586 0.499 7.113 4.24 7.022 6.974 C 6.812 8.503 8.106 15.054 9.669 16.162 C 11.205 15.77 12.713 15.386 14.236 15.036 Z" />
                                    <path class="no-fill" fill="none" stroke="#000" stroke-width="2"
                                        d="M 21.184 28.252 C 21.184 28.252 24.001 28.918 25.859 29.496 C 25.128 38.603 16.542 35.143 21.184 28.252 Z M 21.764 26.007 C 21.741 24.741 21.807 23.525 21.261 22.32 C 20.64 21.031 20.541 19.9 20.885 18.026 C 21.508 14.618 22.828 12.495 24.61 12.029 C 28.417 11.471 28.888 15.211 28.977 17.945 C 29.187 19.475 27.897 26.027 26.332 27.135 C 24.799 26.743 23.288 26.357 21.764 26.007 Z" />
                                </svg> --}}
                                <strong
                                    class="main__stat-value">{{ $userWastePref->purchase_transactions[0]->total_amounts ?? '0.00' }}</strong>
                                <span class="main__stat-unit">น้ำหนักขยะเปียกของท่าน<div>2568</div></span>
                                <strong
                                    class="main__stat-value">{{ $userWastePref->purchase_transactions[0]->total_points ?? '0.00' }}</strong>
                                <span class="main__stat-unit">แต้มสะสม</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="main__stat-blocks">

                    <div class="main__stat-block" data-bs-toggle="modal" data-bs-target="#exampleModal">
                        <div class="main__stat-graph">
                            <svg class="ring" viewBox="0 0 60 60" height="60" width="60" xmlns="http://www.w3.org/2000/svg">
                                <circle class="ring-track" cx="30" cy="30" r="26" fill="none" stroke="#7f7f7f"
                                    stroke-width="8" />
                                <circle class="ring-stroke ring-stroke--cals" cx="30" cy="30" r="26" fill="none"
                                    stroke="#000" stroke-linecap="round" stroke-width="8" stroke-dasharray="163.36 163.36"
                                    stroke-dashoffset="12.25" transform="rotate(-90,30,30)" />
                            </svg>
                            <svg role="img" aria-label="Flame" class="icon" viewBox="0 0 24 24" height="24" width="24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path class="no-fill" fill="none" stroke="#000" stroke-width="2"
                                    d="M 14.505 1 C 11.546 1.356 10.354 12.419 10.272 12.478 C 10.189 12.538 6.773 6.184 6.773 6.184 C 6.773 6.184 3.855 8.381 4 14 C 4.2 18 5.868 23.067 12.177 22.999 C 18.488 22.932 20.1 18 20 14 C 19.9 10 17.533 10.05 15.964 6.738 C 14.638 3.939 14.505 1 14.505 1 Z" />
                            </svg>
                        </div>
                        <div class="main__stat-detail" style="margin-left: 0.5rem">
                            <strong class="main__stat-value">แจ้งปัญหาถังหมัก</strong>
                        </div>
                    </div>
                    <div class="main__stat-block">
                        <a href="{{route('keptkayas.shop.index')}}">
                            <div class="main__stat-graph">
                                <svg class="ring" viewBox="0 0 60 60" height="60" width="60"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <circle class="ring-track" cx="30" cy="30" r="26" fill="none" stroke="#7f7f7f"
                                        stroke-width="8" />
                                    <circle class="ring-stroke ring-stroke--miles" cx="30" cy="30" r="26" fill="none"
                                        stroke="#000" stroke-linecap="round" stroke-width="8"
                                        stroke-dasharray="163.36 163.36" stroke-dashoffset="35.39"
                                        transform="rotate(-90,30,30)" />
                                </svg>
                                <svg role="img" aria-label="Location marker" class="icon" viewBox="0 0 24 24" height="24"
                                    width="24" xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M12 2C7.6 2 4 5.6 4 10C4 15.4 11 21.5 11.3 21.8C11.5 21.9 11.8 22 12 22C12.2 22 12.5 21.9 12.7 21.8C13 21.5 20 15.4 20 10C20 5.6 16.4 2 12 2ZM12 19.7C9.9 17.7 6 13.4 6 10C6 6.7 8.7 4 12 4C15.3 4 18 6.7 18 10C18 13.3 14.1 17.7 12 19.7ZM12 6C9.8 6 8 7.8 8 10C8 12.2 9.8 14 12 14C14.2 14 16 12.2 16 10C16 7.8 14.2 6 12 6ZM12 12C10.9 12 10 11.1 10 10C10 8.9 10.9 8 12 8C13.1 8 14 8.9 14 10C14 11.1 13.1 12 12 12Z" />
                                </svg>
                            </div>
                            <div class="main__stat-detail">
                                <strong class="main__stat-value">วิธีจัดการเศษอาหาร</strong>
                            </div>
                        </a>
                    </div>

                </div>
                <div class="main__stat-blocks">

                    <div class="main__stat-block" id="qrcosde">
                        <div class="main__stat-graph">
                            <svg class="ring" viewBox="0 0 60 60" height="60" width="60" xmlns="http://www.w3.org/2000/svg">
                                <circle class="ring-track" cx="30" cy="30" r="26" fill="none" stroke="#7f7f7f"
                                    stroke-width="8" />
                                <circle class="ring-stroke ring-stroke--cals" cx="30" cy="30" r="26" fill="none"
                                    stroke="#000" stroke-linecap="round" stroke-width="8" stroke-dasharray="163.36 163.36"
                                    stroke-dashoffset="12.25" transform="rotate(-90,30,30)" />
                            </svg>
                            <svg role="img" aria-label="Flame" class="icon" viewBox="0 0 24 24" height="24" width="24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path class="no-fill" fill="none" stroke="#000" stroke-width="2"
                                    d="M 14.505 1 C 11.546 1.356 10.354 12.419 10.272 12.478 C 10.189 12.538 6.773 6.184 6.773 6.184 C 6.773 6.184 3.855 8.381 4 14 C 4.2 18 5.868 23.067 12.177 22.999 C 18.488 22.932 20.1 18 20 14 C 19.9 10 17.533 10.05 15.964 6.738 C 14.638 3.939 14.505 1 14.505 1 Z" />
                            </svg>
                        </div>
                        <a href="{{ route('keptkayas.recycle_classify') }}">
                            <div class="main__stat-detail" style="">
                                <strong class="main__stat-value">วิธีแก้ปัญหาถังหมัก</strong>
                            </div>
                        </a>
                    </div>
                    <div class="main__stat-block">
                        <div class="main__stat-graph">
                            <svg class="ring" viewBox="0 0 60 60" height="60" width="60" xmlns="http://www.w3.org/2000/svg">
                                <circle class="ring-track" cx="30" cy="30" r="26" fill="none" stroke="#7f7f7f"
                                    stroke-width="8" />
                                <circle class="ring-stroke ring-stroke--miles" cx="30" cy="30" r="26" fill="none"
                                    stroke="#000" stroke-linecap="round" stroke-width="8" stroke-dasharray="163.36 163.36"
                                    stroke-dashoffset="35.39" transform="rotate(-90,30,30)" />
                            </svg>
                            <svg role="img" aria-label="Location marker" class="icon" viewBox="0 0 24 24" height="24"
                                width="24" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M12 2C7.6 2 4 5.6 4 10C4 15.4 11 21.5 11.3 21.8C11.5 21.9 11.8 22 12 22C12.2 22 12.5 21.9 12.7 21.8C13 21.5 20 15.4 20 10C20 5.6 16.4 2 12 2ZM12 19.7C9.9 17.7 6 13.4 6 10C6 6.7 8.7 4 12 4C15.3 4 18 6.7 18 10C18 13.3 14.1 17.7 12 19.7ZM12 6C9.8 6 8 7.8 8 10C8 12.2 9.8 14 12 14C14.2 14 16 12.2 16 10C16 7.8 14.2 6 12 6ZM12 12C10.9 12 10 11.1 10 10C10 8.9 10.9 8 12 8C13.1 8 14 8.9 14 10C14 11.1 13.1 12 12 12Z" />
                            </svg>
                        </div>
                        <div class="main__stat-detail">
                            <strong class="main__stat-value">ประวัติ</strong>
                            {{-- <span class="main__stat-unit">Miles</span> --}}
                        </div>
                    </div>

                </div>
            </div>
        </main>
        
    </div>

   

    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">สแกน QR Code กับผู้รับซื้อขยะ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{$qrcode}}

                </div>
                <div class="modal-footer">
                    {{ $userWastePref->id."-".$userWastePref->user_id }}
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    {{-- <nav class="nav">
        <ul class="nav__links">
            <li class="nav__link active main_bottom_nav" data-id="recycle">
                <a href="#"><i class='fas fa-recycle'></i></a>
                <div class="nav__link text">ขยะรีไซเคิล</div>
            </li>
            <li class="nav__link main_bottom_nav" data-id="wet">
                <a href="#"><i class='fas fa-trash'></i></a>
                <div class="nav__link text">ขยะเปียก</div>
            </li>



            <div class="nav__light"></div>
        </ul>
    </nav> --}}
<script>
        $(document).ready(function() {
            // This function should now exist and work!
            $("#button").draggable();
            $(".hide").click(function() {
                $(".menu").hide();
            });
            $("#button").click(function() {
                $(".menu").show();
            });
        });
        
    </script>
    <script>
        
        const links = document.querySelectorAll(".nav__link");
        const light = document.querySelector(".nav__light");

        function moveLight({ offsetLeft, offsetWidth }) {
            light.style.left = `${offsetLeft - offsetWidth / 7}px`;
        }

        function activeLink(linkActive) {
            links.forEach((link) => {
                link.classList.remove("active");
                linkActive.classList.add("active");
            });
        }

        links.forEach((link) => {
            link.addEventListener("click", (event) => {
                moveLight(event.target);
                activeLink(link);
            });
        });


        $(document).on('click','.main_bottom_nav', function(){
                $(".menu").toggle('fade out');
                setTimeout(() => {
                    let div_id = $(this).data('id')
                    $('.kp').addClass('hidden')
                    $('.div_'+div_id).removeClass('hidden') 
                }, 1000);
               
            
        })
       
    </script>
    
</body>

</html>