<ul class="navbar-nav">
    <li class="nav-item">
        <a href="{{route('accessmenu')}}" class="nav-link active">
            <div
                class="icon icon-sm shadow-sm border-radius-md bg-yellow text-center d-flex align-items-center justify-content-center  me-2">
                <i class="ni ni-compass-04" aria-hidden="true"></i>
            </div>
            <span class="nav-link-text ms-1">Main Menu</span>
        </a>

    </li>
    <li class="nav-item">
        <a class="nav-link  @yield('nav-dashboard')" href="{{route('keptkaya.dashboard')}}">
            <div
                class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                <svg width="12px" height="12px" viewBox="0 0 45 40" version="1.1" xmlns="http://www.w3.org/2000/svg"
                    xmlns:xlink="http://www.w3.org/1999/xlink">
                    <title>shop </title>
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <g transform="translate(-1716.000000, -439.000000)" fill="#FFFFFF" fill-rule="nonzero">
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

    {{-- @can('access annual collection') --}}
        @if (Session::get('keptkaya_type') == 'annual')
            <li class="nav-item">
                <a data-bs-toggle="collapse" href="#receipt" class="nav-link active" aria-controls="receipt" role="button"
                    aria-expanded="true">
                    <div
                        class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center d-flex align-items-center justify-content-center  me-2">
                        <svg width="12px" height="12px" viewBox="0 0 42 42" version="1.1" xmlns="http://www.w3.org/2000/svg"
                            xmlns:xlink="http://www.w3.org/1999/xlink">
                            <title>office</title>
                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <g transform="translate(-1869.000000, -293.000000)" fill="#FFFFFF" fill-rule="nonzero">
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
                    <span class="nav-link-text ms-1">จัดเก็บขยะรายปี</span>
                </a>
                <div class="collapse show" id="receipt" style="">
                    <ul class="nav ms-4 ps-3">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('keptkaya.waste_bins.viewmap') }}">
                                <div
                                    class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                                    <i class="fa fa-map-marked-alt text-danger text-gradient text-lg"></i>
                                </div>
                                <span class="nav-link-text ms-1">แผนที่ถังขยะ</span>
                            </a>
                        </li>

                        <li class="nav-item  ">
                            <a class="nav-link @yield('nav-payment')" href="{{route('keptkaya.annual_payments.index')}}">
                                <div
                                    class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                                    <i class="fa fa-trash text-danger text-gradient text-lg"></i>
                                </div>
                                <span class="sidenav-normal">รับชำระค่าจัดเก็บถังขยะ </span>
                            </a>
                        </li>
                    </ul>
                    <ul class="nav ms-4 ps-3">
                        <li class="nav-item ">
                            <form action="" name="myForm2" id="myForm2" method="post" class="mb-0">
                                {{-- {{ route('user_payment_per_month.history') }} --}}
                                @csrf
                                <button type="submit" class="nav-link @yield('nav-user_payment_per_month-history') border-0">
                                    <input type="hidden" value="nav" name="nav">
                                    <div
                                        class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                                        <i class="fa fa-search text-danger text-gradient text-lg"></i>
                                    </div>
                                    <span class="sidenav-normal">ค้นหาใบเสร็จรับเงิน </span>
                                </button>
                            </form>
                        </li>
                        <li class="nav-item ">
                            <a class="nav-link @yield('nav-user_payment_per_month-table')" href="">
                                {{-- {{ route('user_payment_per_month.table') }} --}}
                                <div
                                    class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                                    <i class="fa fa-chart-bar text-danger text-gradient text-lg"></i>
                                </div>
                                <span class="sidenav-normal">ตารางการชำระเงิน (กค.3) </span>
                            </a>
                        </li>


                        <li class="nav-item ">
                            <a class="nav-link @yield('nav-daily')" href="">
                                {{-- {{ route('report.daily') }} --}}
                                <div
                                    class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                                    <i class="fa fa-clipboard-list text-danger text-gradient text-lg"></i>
                                </div>
                                <span class="sidenav-normal">ตารางจัดเก็บค่าขยะประจำวัน </span>
                            </a>
                        </li>

                        <li class="nav-item ">
                            <a class="nav-link @yield('nav-invoice')" href="">
                                {{-- {{ route('user_payment_per_month.invoice') }} --}}
                                <div
                                    class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                                    <i class="fa fa-file-invoice text-danger text-gradient text-lg"></i>
                                </div>
                                <span class="sidenav-normal">ออกใบแจ้งหนี้ </span>
                            </a>
                        </li>
                        <li class="nav-item @yield('nav-staff')">
                            <a class="nav-link " href="{{route('keptkaya.kp_usergroup.index')}}">
                                <div
                                    class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                                    <i class="fa fa-users text-danger text-gradient text-lg"></i>
                                </div>
                                <span class="sidenav-normal"> กลุ่มผู้ใข้งาน </span>
                            </a>
                        </li>

                        <li class="nav-item  ">
                            <a class="nav-link @yield('nav-cart')" href="{{ route('keptkaya.payrate_per_months.index') }}">
                                <div
                                    class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                                    <i class="fa fa-users text-danger text-gradient text-lg"></i>
                                </div>
                                <span class="sidenav-normal">กำหนดอัตราชำระต่อกลุ่มผู้ใข้งาน </span>
                            </a>
                        </li>

                    </ul>
                </div>
            </li>
        @endif
    {{-- @endcan --}}

    {{-- @can('access waste bank') --}}
    @if (Session::get('keptkaya_type') == 'recycle')
        <li class="nav-item">
            <a data-bs-toggle="collapse" href="#cart" class="nav-link active" aria-controls="cart" role="button"
                aria-expanded="true">
                <div
                    class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center d-flex align-items-center justify-content-center  me-2">
                    <svg width="12px" height="12px" viewBox="0 0 42 42" version="1.1" xmlns="http://www.w3.org/2000/svg"
                        xmlns:xlink="http://www.w3.org/1999/xlink">
                        <title>office</title>
                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                            <g transform="translate(-1869.000000, -293.000000)" fill="#FFFFFF" fill-rule="nonzero">
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
                <span class="nav-link-text ms-1">ธนาคารขยะรีไซเคิล</span>
            </a>
            <div class="collapse show" id="cart" style="">
                <ul class="nav ms-4 ps-3">
                    <li class="nav-item  ">
                        <a class="nav-link @yield('nav-cart')" href="{{ route('keptkaya.purchase.select_user') }}">
                            <div
                                class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                                <i class="fa fa-shopping-cart text-danger text-gradient text-lg"></i>
                            </div>
                            <span class="sidenav-normal">รับซื้อขยะรีไซเคิล </span>
                        </a>
                    </li>

                    <li class="nav-item  ">
                        <a class="nav-link @yield('nav-cart')" href="{{ route('keptkaya.sell.form') }}">
                            <div
                                class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                                <i class="fa fa-sd-card text-danger text-gradient text-lg"></i>
                            </div>
                            <span class="sidenav-normal">บันทึกข้อมูลการขายขยะรีไซเคิล</span>
                        </a>
                    </li>
                    <li class="nav-item ">
                        <a class="nav-link " href="{{route('keptkaya.sell.history')}}">
                            <div
                                class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                                <i class="fa fa-history text-danger text-gradient text-lg"></i>
                            </div>
                            <span class="sidenav-normal">ประวัติข้อมูลการขายขยะรีไซเคิล</span>
                        </a>
                    </li>
                    <li class="nav-item ">
                        <a class="nav-link " href="{{route('keptkaya.tbank.items_group.index')}}">
                            <div
                                class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                                <i class="fa fa-object-group text-danger text-gradient text-lg"></i>
                            </div>
                            <span class="sidenav-normal">ประเภทขยะรีไซเคิล</span>
                        </a>
                    </li>
                    <li class="nav-item ">
                        <a class="nav-link " href="{{route('keptkaya.tbank.units.index')}}">
                            <div
                                class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                                <i class="fa fa-tablets text-danger text-gradient text-lg"></i>
                            </div>
                            <span class="sidenav-normal">หน่วยนับ</span>
                        </a>
                    </li>
                    <li class="nav-item ">
                        <a class="nav-link " href="{{route('keptkaya.tbank.items.index')}}">
                            <div
                                class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                                <i class="fa fa-wine-bottle text-danger text-gradient text-lg"></i>
                            </div>
                            <span class="sidenav-normal">รายการขยะรีไซเคิล </span>
                        </a>
                    </li>
                    <li class="nav-item  ">
                        <a class="nav-link @yield('nav-cart')" href="{{ route('keptkaya.tbank.prices.index') }}">
                            <div
                                class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                                <i class="fa fa-money-bill-wave text-danger text-gradient text-lg"></i>
                            </div>
                            <span class="sidenav-normal"> ราคาขยะรีไซเคิล</span>
                        </a>
                    </li>
                    <li class="nav-item  ">
                        <a class="nav-link @yield('nav-cart')" href="{{ route('keptkaya.purchase-shops.index') }}">
                            <div
                                class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                                <i class="fa fa-home text-danger text-gradient text-lg"></i>
                            </div>
                            <span class="sidenav-normal">ร้านรับซื้อขยะ </span>
                        </a>
                    </li>

                </ul>
            </div>
        </li>
     @endif
    {{--@endcan --}}
    {{-- @can('manage staff') --}}
    <li class="nav-item">
        <a data-bs-toggle="collapse" href="#pagesExamples" class="nav-link active" aria-controls="pagesExamples"
            role="button" aria-expanded="true">
            <div
                class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center d-flex align-items-center justify-content-center  me-2">
                <i class="fa fa-user"></i>
            </div>
            <span class="nav-link-text ms-1">ผู้ใช้งานระบบ</span>
        </a>
        <div class="collapse show" id="pagesExamples" style="">
            <ul class="nav ms-4 ps-3">
                <li class="nav-item @yield('nav-user')">
                    <a class="nav-link nav-user" href="{{ route('keptkaya.users.index') }}">
                        {{-- keptkaya.admin.kp_user.index --}}
                        <span class="sidenav-mini-icon text-xs"> P </span>
                        <span class="sidenav-normal">สมาชิก </span>
                    </a>
                </li>
                <li class="nav-item @yield('nav-staff')">
                    <a class="nav-link " href="{{route('keptkaya.staffs.index')}}">
                        <span class="sidenav-mini-icon text-xs"> T </span>
                        <span class="sidenav-normal"> เจ้าหน้าที่ </span>
                    </a>
                </li>


                <li class="nav-item  ">
                    <a class="nav-link @yield('nav-cart')" href="{{ route('keptkaya.kp_budgetyear.index') }}">
                        <span class="sidenav-mini-icon"> P </span>
                        <span class="sidenav-normal">ปีงบประมาณ </span>
                    </a>
                </li>

        </div>
    </li>
    {{-- @endcan --}}

</ul>