<ul class="navbar-nav">
    <li class="nav-item">
        <a class="nav-link  @yield('nav-dashboard')" href="{{route('dashboard')}}">
            <div
                class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                <svg width="12px" height="12px" viewBox="0 0 45 40" version="1.1"
                    xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                    <title>shop </title>
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <g transform="translate(-1716.000000, -439.000000)" fill="#FFFFFF"
                            fill-rule="nonzero">
                            <g transform="translate(1716.000000, 291.000000)">
                                <g transform="translate(0.000000, 148.000000)">
                                    <path class="color-background opacity-6"
                                        d="M46.7199583,10.7414583 L40.8449583,0.949791667 C40.4909749,0.360605034 39.8540131,0 39.1666667,0 L7.83333333,0 C7.1459869,0 6.50902508,0.360605034 6.15504167,0.949791667 L0.280041667,10.7414583 C0.0969176761,11.0460037 -1.23209662e-05,11.3946378 -1.23209662e-05,11.75 C-0.00758042603,16.0663731 3.48367543,19.5725301 7.80004167,19.5833333 L7.81570833,19.5833333 C9.75003686,19.5882688 11.6168794,18.8726691 13.0522917,17.5760417 C16.0171492,20.2556967 20.5292675,20.2556967 23.494125,17.5760417 C26.4604562,20.2616016 30.9794188,20.2616016 33.94575,17.5760417 C36.2421905,19.6477597 39.5441143,20.1708521 42.3684437,18.9103691 C45.1927731,17.649886 47.0084685,14.8428276 47.0000295,11.75 C47.0000295,11.3946378 46.9030823,11.0460037 46.7199583,10.7414583 Z">
                                    </path>
                                    <path class="color-background"
                                        d="M39.198,22.4912623 C37.3776246,22.4928106 35.5817531,22.0149171 33.951625,21.0951667 L33.92225,21.1107282 C31.1430221,22.6838032 27.9255001,22.9318916 24.9844167,21.7998837 C24.4750389,21.605469 23.9777983,21.3722567 23.4960833,21.1018359 L23.4745417,21.1129513 C20.6961809,22.6871153 17.4786145,22.9344611 14.5386667,21.7998837 C14.029926,21.6054643 13.533337,21.3722507 13.0522917,21.1018359 C11.4250962,22.0190609 9.63246555,22.4947009 7.81570833,22.4912623 C7.16510551,22.4842162 6.51607673,22.4173045 5.875,22.2911849 L5.875,44.7220845 C5.875,45.9498589 6.7517757,46.9451667 7.83333333,46.9451667 L19.5833333,46.9451667 L19.5833333,33.6066734 L27.4166667,33.6066734 L27.4166667,46.9451667 L39.1666667,46.9451667 C40.2482243,46.9451667 41.125,45.9498589 41.125,44.7220845 L41.125,22.2822926 C40.4887822,22.4116582 39.8442868,22.4815492 39.198,22.4912623 Z">
                                    </path>
                                </g>
                            </g>
                        </g>
                    </g>
                </svg>
            </div>
            <span class="nav-link-text ms-1">Dashboard</span>
        </a>
    </li>
    <li class="nav-item">
        <a data-bs-toggle="collapse" href="#invoices" class="nav-link active" aria-controls="invoices" role="button" aria-expanded="true">
            <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center d-flex align-items-center justify-content-center  me-2">
                <svg width="12px" height="12px" viewBox="0 0 42 42" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                    <title>office</title>
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <g transform="translate(-1869.000000, -293.000000)" fill="#FFFFFF" fill-rule="nonzero">
                            <g transform="translate(1716.000000, 291.000000)">
                                <g id="office" transform="translate(153.000000, 2.000000)">
                                    <path class="color-background" d="M12.25,17.5 L8.75,17.5 L8.75,1.75 C8.75,0.78225 9.53225,0 10.5,0 L31.5,0 C32.46775,0 33.25,0.78225 33.25,1.75 L33.25,12.25 L29.75,12.25 L29.75,3.5 L12.25,3.5 L12.25,17.5 Z" opacity="0.6"></path>
                                    <path class="color-background" d="M40.25,14 L24.5,14 C23.53225,14 22.75,14.78225 22.75,15.75 L22.75,38.5 L19.25,38.5 L19.25,22.75 C19.25,21.78225 18.46775,21 17.5,21 L1.75,21 C0.78225,21 0,21.78225 0,22.75 L0,40.25 C0,41.21775 0.78225,42 1.75,42 L40.25,42 C41.21775,42 42,41.21775 42,40.25 L42,15.75 C42,14.78225 41.21775,14 40.25,14 Z M12.25,36.75 L7,36.75 L7,33.25 L12.25,33.25 L12.25,36.75 Z M12.25,29.75 L7,29.75 L7,26.25 L12.25,26.25 L12.25,29.75 Z M35,36.75 L29.75,36.75 L29.75,33.25 L35,33.25 L35,36.75 Z M35,29.75 L29.75,29.75 L29.75,26.25 L35,26.25 L35,29.75 Z M35,22.75 L29.75,22.75 L29.75,19.25 L35,19.25 L35,22.75 Z">
                                    </path>
                                </g>
                            </g>
                        </g>
                    </g>
                </svg>
            </div>
            <span class="nav-link-text ms-1">จัดการใบแจ้งหนี้</span>
        </a>
        <div class="collapse show" id="invoices" style="">
            <ul class="nav ms-4 ps-3">

                <li class="nav-item  ">
                    <a class="nav-link @yield('nav-invoice')" href="{{ route('invoice.index') }}">
                        <span class="sidenav-mini-icon"> P </span>
                        <span class="sidenav-normal">ออกใบแจ้งหนี้ </span>
                    </a>
                </li>
                <li class="nav-item ">
                    <a class="nav-link " href="http://localhost:8000/admin/settings">
                        <span class="sidenav-mini-icon"> P </span>
                        <span class="sidenav-normal">ออกใบแจ้งเตือนค้างชำระหนี้ </span>
                    </a>
                </li>
                <li class="nav-item ">
                    <a class="nav-link " href="http://localhost:8000/admin/settings">
                        <span class="sidenav-mini-icon"> P </span>
                        <span class="sidenav-normal">ตัดมิเตอร์น้ำประปา </span>
                    </a>
                </li>



            </ul>
        </div>
    </li>
    <li class="nav-item">
        <a data-bs-toggle="collapse" href="#receipt" class="nav-link active" aria-controls="receipt" role="button" aria-expanded="true">
            <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center d-flex align-items-center justify-content-center  me-2">
                <svg width="12px" height="12px" viewBox="0 0 42 42" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                    <title>office</title>
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <g transform="translate(-1869.000000, -293.000000)" fill="#FFFFFF" fill-rule="nonzero">
                            <g transform="translate(1716.000000, 291.000000)">
                                <g id="office" transform="translate(153.000000, 2.000000)">
                                    <path class="color-background" d="M12.25,17.5 L8.75,17.5 L8.75,1.75 C8.75,0.78225 9.53225,0 10.5,0 L31.5,0 C32.46775,0 33.25,0.78225 33.25,1.75 L33.25,12.25 L29.75,12.25 L29.75,3.5 L12.25,3.5 L12.25,17.5 Z" opacity="0.6"></path>
                                    <path class="color-background" d="M40.25,14 L24.5,14 C23.53225,14 22.75,14.78225 22.75,15.75 L22.75,38.5 L19.25,38.5 L19.25,22.75 C19.25,21.78225 18.46775,21 17.5,21 L1.75,21 C0.78225,21 0,21.78225 0,22.75 L0,40.25 C0,41.21775 0.78225,42 1.75,42 L40.25,42 C41.21775,42 42,41.21775 42,40.25 L42,15.75 C42,14.78225 41.21775,14 40.25,14 Z M12.25,36.75 L7,36.75 L7,33.25 L12.25,33.25 L12.25,36.75 Z M12.25,29.75 L7,29.75 L7,26.25 L12.25,26.25 L12.25,29.75 Z M35,36.75 L29.75,36.75 L29.75,33.25 L35,33.25 L35,36.75 Z M35,29.75 L29.75,29.75 L29.75,26.25 L35,26.25 L35,29.75 Z M35,22.75 L29.75,22.75 L29.75,19.25 L35,19.25 L35,22.75 Z">
                                    </path>
                                </g>
                            </g>
                        </g>
                    </g>
                </svg>
            </div>
            <span class="nav-link-text ms-1">จัดการใบเสร็จรับเงิน</span>
        </a>
        <div class="collapse show" id="receipt" style="">
            <ul class="nav ms-4 ps-3">

                <li class="nav-item  ">
                    <a class="nav-link @yield('nav-payment')" href="{{ route('payment.index') }}">
                        <span class="sidenav-mini-icon"> P </span>
                        <span class="sidenav-normal">รับชำระค่าน้ำประปา </span>
                    </a>
                </li>
                <li class="nav-item ">
                        <form action="{{ route('payment.search') }}" method="post" class="mb-0">
                            @csrf
                            <button type="submit" class="nav-link @yield('nav-payment-search') border-0">
                                <input type="hidden" value="nav" name="nav">
                            <span class="sidenav-mini-icon"> P </span>
                            <span class="sidenav-normal">ค้นหาใบเสร็จรับเงิน </span>
                            </button>
                        </form>
                </li>
                <li class="nav-item ">
                    <a class="nav-link " href="http://localhost:8000/admin/settings">
                        <span class="sidenav-mini-icon"> P </span>
                        <span class="sidenav-normal">รับชำระค่าน้ำประปาผ่านธนาคาร </span>
                    </a>
                </li>



            </ul>
        </div>
    </li>
    <li class="nav-item">
        <a data-bs-toggle="collapse" href="#reports" class="nav-link active" aria-controls="reports" role="button" aria-expanded="true">
            <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center d-flex align-items-center justify-content-center  me-2">
                <svg width="12px" height="12px" viewBox="0 0 42 42" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                    <title>office</title>
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <g transform="translate(-1869.000000, -293.000000)" fill="#FFFFFF" fill-rule="nonzero">
                            <g transform="translate(1716.000000, 291.000000)">
                                <g id="office" transform="translate(153.000000, 2.000000)">
                                    <path class="color-background" d="M12.25,17.5 L8.75,17.5 L8.75,1.75 C8.75,0.78225 9.53225,0 10.5,0 L31.5,0 C32.46775,0 33.25,0.78225 33.25,1.75 L33.25,12.25 L29.75,12.25 L29.75,3.5 L12.25,3.5 L12.25,17.5 Z" opacity="0.6"></path>
                                    <path class="color-background" d="M40.25,14 L24.5,14 C23.53225,14 22.75,14.78225 22.75,15.75 L22.75,38.5 L19.25,38.5 L19.25,22.75 C19.25,21.78225 18.46775,21 17.5,21 L1.75,21 C0.78225,21 0,21.78225 0,22.75 L0,40.25 C0,41.21775 0.78225,42 1.75,42 L40.25,42 C41.21775,42 42,41.21775 42,40.25 L42,15.75 C42,14.78225 41.21775,14 40.25,14 Z M12.25,36.75 L7,36.75 L7,33.25 L12.25,33.25 L12.25,36.75 Z M12.25,29.75 L7,29.75 L7,26.25 L12.25,26.25 L12.25,29.75 Z M35,36.75 L29.75,36.75 L29.75,33.25 L35,33.25 L35,36.75 Z M35,29.75 L29.75,29.75 L29.75,26.25 L35,26.25 L35,29.75 Z M35,22.75 L29.75,22.75 L29.75,19.25 L35,19.25 L35,22.75 Z">
                                    </path>
                                </g>
                            </g>
                        </g>
                    </g>
                </svg>
            </div>
            <span class="nav-link-text ms-1">รายงาน</span>
        </a>
        <div class="collapse show" id="reports" style="">
            <ul class="nav ms-4 ps-3">

                <li class="nav-item  ">
                    <a class="nav-link @yield('nav-reports-owe')" href="{{ route('reports.owe') }}">
                        <span class="sidenav-mini-icon"> P </span>
                        <span class="sidenav-normal">ผู้ค้างชำระค่าน้ำประปา </span>
                    </a>
                </li>
                <li class="nav-item ">
                        <form action="{{ route('reports.dailypayment') }}" method="post" class="mb-0">
                            @csrf
                            <button type="submit" class="nav-link @yield('nav-reports-dailypayment') border-0">
                                <input type="hidden" value="nav" name="nav">
                            <span class="sidenav-mini-icon"> P </span>
                            <span class="sidenav-normal">การชำระค่าน้ำประปาประจำวัน </span>
                            </button>
                        </form>
                </li>
                <li class="nav-item ">
                    <a class="nav-link " href="http://localhost:8000/admin/settings">
                        <span class="sidenav-mini-icon"> P </span>
                        <span class="sidenav-normal">รับชำระค่าน้ำประปาผ่านธนาคาร </span>
                    </a>
                </li>



            </ul>
        </div>
    </li>
    <li class="nav-item">
        <a data-bs-toggle="collapse" href="#pagesExamples" class="nav-link active"
            aria-controls="pagesExamples" role="button" aria-expanded="true">
            <div
                class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center d-flex align-items-center justify-content-center  me-2">
                <svg width="12px" height="12px" viewBox="0 0 42 42" version="1.1"
                    xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                    <title>office</title>
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <g transform="translate(-1869.000000, -293.000000)" fill="#FFFFFF"
                            fill-rule="nonzero">
                            <g transform="translate(1716.000000, 291.000000)">
                                <g id="office" transform="translate(153.000000, 2.000000)">
                                    <path class="color-background"
                                        d="M12.25,17.5 L8.75,17.5 L8.75,1.75 C8.75,0.78225 9.53225,0 10.5,0 L31.5,0 C32.46775,0 33.25,0.78225 33.25,1.75 L33.25,12.25 L29.75,12.25 L29.75,3.5 L12.25,3.5 L12.25,17.5 Z"
                                        opacity="0.6"></path>
                                    <path class="color-background"
                                        d="M40.25,14 L24.5,14 C23.53225,14 22.75,14.78225 22.75,15.75 L22.75,38.5 L19.25,38.5 L19.25,22.75 C19.25,21.78225 18.46775,21 17.5,21 L1.75,21 C0.78225,21 0,21.78225 0,22.75 L0,40.25 C0,41.21775 0.78225,42 1.75,42 L40.25,42 C41.21775,42 42,41.21775 42,40.25 L42,15.75 C42,14.78225 41.21775,14 40.25,14 Z M12.25,36.75 L7,36.75 L7,33.25 L12.25,33.25 L12.25,36.75 Z M12.25,29.75 L7,29.75 L7,26.25 L12.25,26.25 L12.25,29.75 Z M35,36.75 L29.75,36.75 L29.75,33.25 L35,33.25 L35,36.75 Z M35,29.75 L29.75,29.75 L29.75,26.25 L35,26.25 L35,29.75 Z M35,22.75 L29.75,22.75 L29.75,19.25 L35,19.25 L35,22.75 Z">
                                    </path>
                                </g>
                            </g>
                        </g>
                    </g>
                </svg>
            </div>
            <span class="nav-link-text ms-1">ผู้ดูแลระบบ</span>
        </a>
        <div class="collapse show" id="pagesExamples" style="">
            <ul class="nav ms-4 ps-3">
                <li class="nav-item ">
                    <a class="nav-link collapsed @yield('user-show')" data-bs-toggle="collapse" aria-expanded="false"
                        href="#profileExample">
                        <span class="sidenav-mini-icon"> P </span>
                        <span class="sidenav-normal"> ผู้ใช้งานระบบ <b class="caret"></b></span>
                    </a>
                    <div class="collapse @yield('user-show')" id="profileExample" style="">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item @yield('nav-user')">
                                <a class="nav-link nav-user" href="{{ route('admin.users.index') }}">
                                    <span class="sidenav-mini-icon text-xs"> P </span>
                                    <span class="sidenav-normal"> ผู้ใช้น้ำประปา </span>
                                </a>
                            </li>
                            <li class="nav-item @yield('nav-staff')">
                                <a class="nav-link " href="{{ route('admin.users.staff') }}">
                                    <span class="sidenav-mini-icon text-xs"> T </span>
                                    <span class="sidenav-normal"> เจ้าหน้าที่ </span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item @yield('nav-metertype')">
                    <a class="nav-link " href="{{ route('admin.metertype.index') }}">
                        <span class="sidenav-mini-icon"> P </span>
                        <span class="sidenav-normal">ประเภทมิเตอร์ </span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link collapsed  @yield('budgetyear-show') @yield('inv_prd-show') @yield('nav-inv_prd')" data-bs-toggle="collapse" aria-expanded="false"
                        href="#budgetyear">
                        <span class="sidenav-mini-icon"> P </span>
                        <span class="sidenav-normal">ปีงบประมาณ/รอบบิล <b class="caret"></b></span>
                    </a>
                    <div class="collapse @yield('budgetyear-show') @yield('inv_prd-show')" id="budgetyear" style="">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item @yield('nav-budgetyear')">
                                <a class="nav-link " href="{{ route('admin.budgetyear.index') }}">
                                    <span class="sidenav-mini-icon text-xs"> P </span>
                                    <span class="sidenav-normal"> ปีงบประมาณ </span>
                                </a>
                            </li>
                            <li class="nav-item @yield('nav-inv_prd')">
                                <a class="nav-link " href="{{ route('admin.invoice_period.index') }}">
                                    <span class="sidenav-mini-icon text-xs"> T </span>
                                    <span class="sidenav-normal"> รอบบิล </span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item ">
                    <a class="nav-link collapsed" data-bs-toggle="collapse" aria-expanded="false"
                        href="#zone">
                        <span class="sidenav-mini-icon"> P </span>
                        <span class="sidenav-normal">พื้นที่จัดเก็บค่าน้ำประปา<b class="caret"></b></span>
                    </a>
                    <div class="collapse" id="zone" style="">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a class="nav-link " href="{{ route('admin.zone.index') }}">
                                    <span class="sidenav-mini-icon text-xs"> P </span>
                                    <span class="sidenav-normal"> พื้นที่-เส้นทางจัดเก็บค่าน้ำประปา </span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link " href="{{ route('admin.invoice_period.index') }}">
                                    <span class="sidenav-mini-icon text-xs"> T </span>
                                    <span class="sidenav-normal"> ผู้รับผิดชอบพื้นที่จดมิเตอร์ </span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="nav-item ">
                    <a class="nav-link " href="{{ route('admin.settings.index') }}">
                        <span class="sidenav-mini-icon"> P </span>
                        <span class="sidenav-normal">ตั้งค่าข้อมูลองค์กร </span>
                    </a>
                </li>
                <li class="nav-item @yield('nav-excel')">
                    <a class="nav-link " href="{{ route('admin.excel.index') }}">
                        <span class="sidenav-mini-icon"> P </span>
                        <span class="sidenav-normal">Import excel </span>
                    </a>
                </li>
                <li class="nav-item ">
                    <a class="nav-link collapsed" data-bs-toggle="collapse" aria-expanded="false"
                        href="#roles">
                        <span class="sidenav-mini-icon"> P </span>
                        <span class="sidenav-normal">สิทธิ์การใช้งานระบบ<b class="caret"></b></span>
                    </a>
                    <div class="collapse" id="roles" style="">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a class="nav-link " href="{{ route('admin.roles.index') }}">
                                    <span class="sidenav-mini-icon text-xs"> P </span>
                                    <span class="sidenav-normal"> Roles </span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link " href="{{ route('admin.permissions.index') }}">
                                    <span class="sidenav-mini-icon text-xs"> T </span>
                                    <span class="sidenav-normal"> Permission </span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

            </ul>
        </div>
    </li>
</ul>



