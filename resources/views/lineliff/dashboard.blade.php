<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
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
            width: 24.375em;
            height: 52.75em;
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
            margin-bottom: 1.5em;
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
            margin-bottom: 1.5em;
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
            position: fixed; /* กำหนดให้องค์ประกอบมีตำแหน่งคงที่ */
            bottom: 0;      /* ให้อยู่ที่ขอบด้านล่างสุด */
            left: 0;        /* ให้อยู่ที่ขอบด้านซ้ายสุด */
            width: 100%;    /* ให้มีความกว้างเต็มหน้าจอ */
            z-index: 1000;  /* (optional) กำหนดลำดับการแสดงผลให้อยู่ด้านบนองค์ประกอบอื่น */
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
        a{
            text-decoration: none;
             color: var(--fg);
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>

</head>

<body id="body">
    <div class="app">
        <svg class="app__gradients" hidden>
            <defs>
                <linearGradient id="ring" x1="1" y1="0" x2="0" y2="1">
                    <stop offset="0%" stop-color="hsl(184,66%,54%)" />
                    <stop offset="100%" stop-color="hsl(184,66%,34%)" />
                </linearGradient>
            </defs>
        </svg>
        <header class="header">
            <button class="header__profile-btn" type="button">
                <img class="header__profile-icon" id="header__profile_img" alt="Profile (Mr. Trololo)"
                    src="https://profile.line-scdn.net/{{$userWastePref->user->image}}" width="78" height="78">
            </button>
            <button class="header__notes-btn" type="button" title="Notifications">
                <span style="font-size: 4em">{{$userWastePref->user->firstname}}</span>
            </button>
        </header>
        <main>
            <div class="main__date-nav">
                <button class="main__date-arrow-btn" type="button">
                    {{-- <svg class="icon" viewBox="0 0 24 24" height="24" width="24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M14.7071 5.29289C15.0976 5.68342 15.0976 6.31658 14.7071 6.70711L9.41421 12L14.7071 17.2929C15.0976 17.6834 15.0976 18.3166 14.7071 18.7071C14.3166 19.0976 13.6834 19.0976 13.2929 18.7071L7.29289 12.7071C6.90237 12.3166 6.90237 11.6834 7.29289 11.2929L13.2929 5.29289C13.6834 4.90237 14.3166 4.90237 14.7071 5.29289Z" />
                    </svg>
                    <span class="sr-only"></span> --}}
                </button>
                <div class="main__date">
                    <img src="{{asset('logo/ko_envsogo.png')}}" alt="" style="width: 35%; margin-top: -3.6rem; position: absolute;">
                    <strong><span style="">&nbsp;</span><div style="font-size:1.5rem;margin-top: 2rem;
    border-top: 2px solid white;">Envsogo</div></strong>
                </div>
                <button class="main__date-edit-btn" type="button"></button>
            </div>
            <div class="main__stat-blocks">
                <!--
                    To get the stroke-dashoffset for the rings:

                    2πr * (1 - value/max)
                    -->
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
                            <strong class="main__stat-value">{{ $userWastePref->user->purchase_transactions[0]->total_amounts ?? '0.00' }}</strong>
                            <span class="main__stat-unit">ยอดเงินคงเหลือ</span>
                            <strong class="main__stat-value">{{ $userWastePref->user->purchase_transactions[0]->total_points ?? '0.00' }}</strong>
                            <span class="main__stat-unit">แต้มสะสม</span>
                        </div>
                    </div>
                </div>
                {{-- <div class="main__stat-block" style="margin-left: 1rem" id="qrcosde" data-bs-toggle="modal"
                    data-bs-target="#exampleModal">
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
                        {{-- <span class="main__stat-unit">Cals</span> --}
                    </div>
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
                        {{-- <span class="main__stat-unit">Miles</span> --}
                    </div>
                </div> --}
                {{-- <div class="main__stat-block">
                    <div class="main__stat-graph">
                        <svg class="ring" viewBox="0 0 60 60" height="60" width="60" xmlns="http://www.w3.org/2000/svg">
                            <circle class="ring-track" cx="30" cy="30" r="26" fill="none" stroke="#7f7f7f"
                                stroke-width="8" />
                            <circle class="ring-stroke ring-stroke--mins" cx="30" cy="30" r="26" fill="none"
                                stroke="#000" stroke-linecap="round" stroke-width="8" stroke-dasharray="163.36 163.36"
                                stroke-dashoffset="65.34" transform="rotate(-90,30,30)" />
                        </svg>
                        <svg role="img" aria-label="Lightning bolt" class="icon" viewBox="0 0 24 24" height="24"
                            width="24" xmlns="http://www.w3.org/2000/svg">
                            <polygon points="14,1 13,9 22,9 11,23 12,15 2,15" fill="none" stroke="#000"
                                stroke-linejoin="round" stroke-width="2" />
                        </svg>
                    </div>
                    <div class="main__stat-detail">
                        <strong class="main__stat-value">72</strong>
                        <span class="main__stat-unit">Mins</span>
                    </div>
                </div> --}}
            </div>

             <div class="main__stat-blocks">
                
                <div class="main__stat-block"  data-bs-toggle="modal"
                    data-bs-target="#exampleModal">
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
                    <div class="main__stat-detail" style="">
                        <strong class="main__stat-value">ราคา/วิธีคัดแยกขยะ</strong>
                        {{-- <span class="main__stat-unit">Cals</span> --}}
                    </div>
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
            {{-- <div class="main__stat-rows">
                <div class="main__stat-row">
                    <div class="main__stat-graph">
                        <svg role="img" aria-label="Beating heart" class="icon icon--red icon--pulse"
                            viewBox="0 0 24 24" height="24" width="24" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M12 4.52765C9.64418 2.41689 6.02125 2.49347 3.75736 4.75736C1.41421 7.10051 1.41421 10.8995 3.75736 13.2426L10.5858 20.0711C11.3668 20.8521 12.6332 20.8521 13.4142 20.0711L20.2426 13.2426C22.5858 10.8995 22.5858 7.10051 20.2426 4.75736C17.9787 2.49347 14.3558 2.41689 12 4.52765ZM10.8284 6.17157L11.2929 6.63604C11.6834 7.02656 12.3166 7.02656 12.7071 6.63604L13.1716 6.17157C14.7337 4.60948 17.2663 4.60948 18.8284 6.17157C20.3905 7.73367 20.3905 10.2663 18.8284 11.8284L12 18.6569L5.17157 11.8284C3.60948 10.2663 3.60948 7.73367 5.17157 6.17157C6.73367 4.60948 9.26633 4.60948 10.8284 6.17157Z" />
                        </svg>
                    </div>
                    <div class="main__stat-detail">
                        <span class="main__stat-value"><strong>90</strong> <small>bpm</small></span>
                        <span class="main__stat-subtext">76 bpm resting heart rate</span>
                    </div>
                </div>
                <div class="main__stat-row">
                    <div class="main__stat-graph">
                        <svg class="ring" viewBox="0 0 60 60" height="60" width="60" xmlns="http://www.w3.org/2000/svg">
                            <circle class="ring-track" cx="30" cy="30" r="21" fill="none" stroke="#7f7f7f"
                                stroke-width="4" />
                            <circle class="ring-stroke ring-stroke--stepHrs" cx="30" cy="30" r="21" fill="none"
                                stroke="#000" stroke-linecap="round" stroke-width="4" stroke-dasharray="131.95 131.95"
                                stroke-dashoffset="52.78" transform="rotate(-90,30,30)" />
                        </svg>
                        <svg role="img" aria-label="Person" class="icon" viewBox="0 0 24 24" height="24" width="24"
                            xmlns="http://www.w3.org/2000/svg">
                            <ellipse fill="none" stroke="#000" stroke-width="2" cx="12" cy="4" rx="3" ry="3" />
                            <path class="no-fill" stroke-linejoin="round" stroke-width="2"
                                d="M 10.216 9.108 C 10.216 9.108 3.146 3.809 1.477 6.133 C -0.46 8.829 9.295 12.25 9.295 12.25 C 9.295 12.25 6.011 22.985 8.381 23.076 C 10.953 23.175 11.855 16.128 11.873 16.128 C 11.891 16.128 12.21 23.037 14.991 22.973 C 17.772 22.909 14.555 12.188 14.555 12.188 C 14.555 12.188 22.747 13.423 22.754 10.901 C 22.762 8.142 13.734 9.126 13.734 9.126 C 13.734 9.126 10.318 9.033 10.216 9.108 Z" />
                        </svg>
                    </div>
                    <div class="main__stat-detail">
                        <span class="main__stat-value">
                            <strong>3</strong> <small>of</small> <strong>5</strong> <small>hr</small>
                        </span>
                        <span class="main__stat-subtext">250+ steps per hour</span>
                    </div>
                </div>
            </div> --}}
        </main>
        <footer class="footer">
            {{-- <button class="footer__nav-btn" type="button" title="Today">
                <svg class="icon" viewBox="0 0 24 24" height="24" width="24" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="2" cy="12" r="1" />
                    <circle cx="7" cy="7" r="1.25" />
                    <circle cx="7" cy="12" r="1.25" />
                    <circle cx="7" cy="17" r="1.25" />
                    <circle cx="12" cy="2" r="1.5" />
                    <circle cx="12" cy="7" r="1.5" />
                    <circle cx="12" cy="12" r="1.5" />
                    <circle cx="12" cy="17" r="1.5" />
                    <circle cx="12" cy="22" r="1.5" />
                    <circle cx="17" cy="7" r="1.75" />
                    <circle cx="17" cy="12" r="1.75" />
                    <circle cx="17" cy="17" r="1.75" />
                    <circle cx="22" cy="12" r="2" />
                </svg>
            </button>
            <button class="footer__nav-btn" style="text-align: center" type="button" title="Discover">
                 <svg class="icon" viewBox="0 0 24 24" height="24" width="24" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M10 4C7.79086 4 6 5.79086 6 8C6 10.2091 7.79086 12 10 12C12.2091 12 14 10.2091 14 8C14 5.79086 12.2091 4 10 4ZM4 8C4 4.68629 6.68629 2 10 2C13.3137 2 16 4.68629 16 8C16 11.3137 13.3137 14 10 14C6.68629 14 4 11.3137 4 8ZM16.8284 3.75736C17.219 3.36683 17.8521 3.36683 18.2426 3.75736C20.5858 6.10051 20.5858 9.8995 18.2426 12.2426C17.8521 12.6332 17.219 12.6332 16.8284 12.2426C16.4379 11.8521 16.4379 11.219 16.8284 10.8284C18.3905 9.26633 18.3905 6.73367 16.8284 5.17157C16.4379 4.78105 16.4379 4.14788 16.8284 3.75736ZM17.5299 16.7575C17.6638 16.2217 18.2067 15.8959 18.7425 16.0299C20.0705 16.3618 20.911 17.2109 21.3944 18.1778C21.8622 19.1133 22 20.1571 22 21C22 21.5523 21.5523 22 21 22C20.4477 22 20 21.5523 20 21C20 20.3429 19.8878 19.6367 19.6056 19.0722C19.339 18.5391 18.9295 18.1382 18.2575 17.9701C17.7217 17.8362 17.3959 17.2933 17.5299 16.7575ZM6.5 18C5.24054 18 4 19.2135 4 21C4 21.5523 3.55228 22 3 22C2.44772 22 2 21.5523 2 21C2 18.3682 3.89347 16 6.5 16H13.5C16.1065 16 18 18.3682 18 21C18 21.5523 17.5523 22 17 22C16.4477 22 16 21.5523 16 21C16 19.2135 14.7595 18 13.5 18H6.5Z" />
                </svg>งานประปา
            </button>
            <button class="footer__nav-btn" type="button" title="Community">
                <svg class="icon" viewBox="0 0 24 24" height="24" width="24" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M10 4C7.79086 4 6 5.79086 6 8C6 10.2091 7.79086 12 10 12C12.2091 12 14 10.2091 14 8C14 5.79086 12.2091 4 10 4ZM4 8C4 4.68629 6.68629 2 10 2C13.3137 2 16 4.68629 16 8C16 11.3137 13.3137 14 10 14C6.68629 14 4 11.3137 4 8ZM16.8284 3.75736C17.219 3.36683 17.8521 3.36683 18.2426 3.75736C20.5858 6.10051 20.5858 9.8995 18.2426 12.2426C17.8521 12.6332 17.219 12.6332 16.8284 12.2426C16.4379 11.8521 16.4379 11.219 16.8284 10.8284C18.3905 9.26633 18.3905 6.73367 16.8284 5.17157C16.4379 4.78105 16.4379 4.14788 16.8284 3.75736ZM17.5299 16.7575C17.6638 16.2217 18.2067 15.8959 18.7425 16.0299C20.0705 16.3618 20.911 17.2109 21.3944 18.1778C21.8622 19.1133 22 20.1571 22 21C22 21.5523 21.5523 22 21 22C20.4477 22 20 21.5523 20 21C20 20.3429 19.8878 19.6367 19.6056 19.0722C19.339 18.5391 18.9295 18.1382 18.2575 17.9701C17.7217 17.8362 17.3959 17.2933 17.5299 16.7575ZM6.5 18C5.24054 18 4 19.2135 4 21C4 21.5523 3.55228 22 3 22C2.44772 22 2 21.5523 2 21C2 18.3682 3.89347 16 6.5 16H13.5C16.1065 16 18 18.3682 18 21C18 21.5523 17.5523 22 17 22C16.4477 22 16 21.5523 16 21C16 19.2135 14.7595 18 13.5 18H6.5Z" />
                </svg>
            </button> --}}
            {{-- <button class="footer__nav-btn" type="button" title="Premium">
                <svg class="icon" viewBox="0 0 24 24" height="24" width="24" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="2" cy="12" r="1" />
                    <circle cx="7" cy="12" r="1.25" />
                    <circle cx="12" cy="2" r="1.5" />
                    <circle cx="12" cy="12" r="1.5" />
                    <circle cx="12" cy="22" r="1.5" />
                    <circle cx="17" cy="7" r="1.75" />
                    <circle cx="17" cy="12" r="1.75" />
                    <circle cx="17" cy="17" r="1.75" />
                    <circle cx="22" cy="12" r="2" />
                </svg>
            </button> --}}
        </footer>
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
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</body>

</html>