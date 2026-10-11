@php
    $infos_count = App\Http\Controllers\FunctionsController::keptkaya_nav_infos();
@endphp
<style>
    ._active{
        background: #f0efef;
        border-radius: 10px
    }
</style>
<ul class="navbar-nav">
           {{-- <li class="nav-item">
        <a href="{{route('lineliff.dashboard', ['pref_id' => session('pref_id_mobile'),'org_id' => 2 ] )}}" class="nav-link active w-50 bg-info mb-2">
            <div
                class="icon icon-sm shadow-sm border-radius-md bg-yellow text-center d-flex align-items-center justify-content-center  me-2">
                <i class="ni ni-compass-04" aria-hidden="true"></i>
            </div>
            <span class="nav-link-text ms-1">dashboard</span>
        </a>

    </li> --}}
   
    <li class="nav-item">
        <a href="{{route('accessmenu')}}" class="nav-link active w-50 bg-info mb-2">
            <div
                class="icon icon-sm shadow-sm border-radius-md bg-yellow text-center d-flex align-items-center justify-content-center  me-2">
                <i class="ni ni-compass-04" aria-hidden="true"></i>
            </div>
            <span class="nav-link-text ms-1">กลับไปหน้าหลัก</span>
        </a>

    </li>
    <li class="nav-item">
        <a class="nav-link  @yield('nav-dashboard')" href="{{route('keptkayas.dashboard')}}">
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
        
     
        <li class="nav-item">
            <a data-bs-toggle="collapse" href="#cart" class="nav-link _active" aria-controls="cart" role="button"
                aria-expanded="true">
                <div
                    class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center d-flex align-items-center justify-content-center  me-2">

                </div>
                <span class="nav-link-text ms-1">ธนาคารขยะรีไซเคิล</span>
            </a>
            <div class="collapse show" id="cart" style="">
                <ul class="nav ms-4 ps-3">
                    <li class="nav-item  ">
                        <a class="nav-link @yield('nav-cart')" href="{{ route('keptkayas.purchase.select_route') }}">
                            <div
                                class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                                <i class="fa fa-shopping-cart text-danger text-gradient text-lg"></i>
                            </div>
                            <span class="sidenav-normal">รับซื้อขยะรีไซเคิล </span>
                        </a>
                    </li>

                    <li class="nav-item  ">
                        <a class="nav-link @yield('nav-cart')" href="{{ route('keptkayas.recycle-bank.members') }}">
                            <div
                                class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                                <i class="fa fa-shopping-cart text-danger text-gradient text-lg"></i>
                            </div>
                            <span class="sidenav-normal">รายชื่อสมาชิกธนาคารขยะ </span>
                        </a>
                    </li>
                    
                </ul>
            </div>
        </li>

        <li class="nav-item">
            <a data-bs-toggle="collapse" href="#savebill" class="nav-link _active" aria-controls="savebill" role="button"
                aria-expanded="false">
                <div
                    class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center d-flex align-items-center justify-content-center  me-2">

                </div>
                <span class="nav-link-text ms-1">ข้อมูลการขายให้ร้านรับซื้อขยะ</span>
            </a>
            <div class="collapse" id="savebill" style="">
                <ul class="nav ms-4 ps-3">
                    <li class="nav-item  ">
                        <a class="nav-link @yield('nav-cart')" href="{{ route('keptkayas.sell.form') }}">
                            <div
                                class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                                <i class="fa fa-sd-card text-danger text-gradient text-lg"></i>
                            </div>
                            <span class="sidenav-normal">บันทึกข้อมูลการขายขยะรีไซเคิล</span>
                        </a>
                    </li>
                    <li class="nav-item ">
                        <a class="nav-link " href="{{route('keptkayas.sell.history')}}">
                            <div
                                class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                                <i class="fa fa-history text-danger text-gradient text-lg"></i>
                            </div>
                            <span class="sidenav-normal">ประวัติข้อมูลการขายขยะรีไซเคิล</span>
                        </a>
                    </li>
                </ul>
            </div>
        </li>



        <li class="nav-item">
            <a data-bs-toggle="collapse" href="#welfare" class="nav-link _active" aria-controls="cart" role="button"
                aria-expanded="true">
                <div
                    class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center d-flex align-items-center justify-content-center  me-2">

                </div>
                <span class="nav-link-text ms-1">กองทุนสวัสดิการฌาปณกิจ</span>
            </a>
            <div class="collapse show" id="welfare" style="">
                <ul class="nav ms-4 ps-3">
                    <li class="nav-item  ">
                        <a class="nav-link @yield('nav-cart')" href="{{ route('admin.welfare.dashboard')  }}">
                            <div
                                class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                                <i class="fa fa-shopping-cart text-danger text-gradient text-lg"></i>
                            </div>
                            <span class="sidenav-normal">กองทุนสวัสดิการฌาปณกิจ </span>
                        </a>
                    </li>

                
                    <li class="nav-item  ">
                        <a class="nav-link @yield('nav-cart')" href="{{ route('admin.welfare.config') }}">
                            <div
                                class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                                <i class="fa fa-sd-card text-danger text-gradient text-lg"></i>
                            </div>
                            <span class="sidenav-normal">ฝากเงินเข้ากองทุน</span>
                        </a>
                    </li>

                    <li class="nav-item  ">
                        <a class="nav-link @yield('nav-cart')" href="{{ route('admin.welfare.config') }}">
                            <div
                                class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                                <i class="fa fa-sd-card text-danger text-gradient text-lg"></i>
                            </div>
                            <span class="sidenav-normal">ตั้งค่ากองทุนสวัสดิการฌาปณกิจ</span>
                        </a>
                    </li>
                </ul>
            </div>
        </li>


        <li class="nav-item">
                <a data-bs-toggle="collapse" href="#withdrawMenu" class="nav-link _active" aria-controls="savebill" role="button"
                    aria-expanded="true">
                    <div
                        class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center d-flex align-items-center justify-content-center  me-2">
                       
                    </div>
                    <span class="nav-link-text ms-1">ถอนเงินธนาคารขยะรีไซเคิล</span>
                </a>
                <div class="collapse show" id="withdrawMenu" style="">
        
                <ul class="nav ms-4 ps-3">
                       <li class="nav-item ">
                            <a class="nav-link @yield('nav-keptkayas.batches')" href="{{ route('keptkayas.withdraw.create') }}">
                            <div
                                class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                                <i class="fa fa-sd-card text-danger text-gradient text-lg"></i>
                            </div>
                            <span class="sidenav-normal">ถอนเงิน</span>
                        </a>
                    </li>

                    <li class="nav-item ">
                            <a class="nav-link @yield('nav-keptkayas.batches')" href="{{ route('keptkayas.batches.index', 'current_batches') }}">
                            <div
                                class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                                <i class="fa fa-sd-card text-danger text-gradient text-lg"></i>
                            </div>
                            <span class="sidenav-normal">รายการรอจ่ายเงิน</span>
                        </a>
                    </li>

                    <li class="nav-item ">
                            <a class="nav-link @yield('nav-keptkayas.batches.history')" href="{{ route('keptkayas.batches.index', 'histoty_batches') }}">
                            <div
                                class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                                <i class="fa fa-sd-card text-danger text-gradient text-lg"></i>
                            </div>
                            <span class="sidenav-normal">ประวัติการเบิกถอนเงิน</span>
                        </a>
                    </li>
                    
        
            </div>
        </li>

        <li class="nav-item">
                <a data-bs-toggle="collapse" href="#reportMenu" class="nav-link _active" aria-controls="savebill" role="button"
                    aria-expanded="true">
                    <div
                        class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center d-flex align-items-center justify-content-center  me-2">
                       
                    </div>
                    <span class="nav-link-text ms-1">รายงาน</span>
                </a>
                <div class="collapse show" id="reportMenu" style="">
        
                    <ul class="nav ms-4 ps-3">
                        <li class="nav-item"></li>
                    </ul>
                </div>
            </li>


        <li class="nav-item">
            <a data-bs-toggle="collapse" href="#pagesExamples" class="nav-link _active" aria-controls="pagesExamples"
                role="button" aria-expanded="true">
                <div
                    class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center d-flex align-items-center justify-content-center  me-2">
                    <i class="fa fa-user"></i>
                </div>
                <span class="nav-link-text ms-1">ตั้งค่าข้อมูล</span>
            </a>
            <div class="collapse show" id="pagesExamples" style="">
                <ul class="nav ms-4 ps-3">

                    <li class="nav-item ">
                        <a class="nav-link " href="{{route('keptkayas.kiosk.unknown.review')}}">
                            <div
                                class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                                <i class="fa fa-object-group text-danger text-gradient text-lg"></i>
                            </div>
                            <div class="d-flex justify-content-between" style="width: 100%">

                                <span class="sidenav-normal">ตรวจสอบวัตถุ Unknow</span>
                                @if ($infos_count['items_group_count'] > 0)
                                    <span
                                        class="badge badge-sm badge-circle badge-floating badge-danger border-white">{{ $infos_count['items_group_count'] }}</span>
                                @endif
                            </div>
                        </a>
                    </li>
                    <li class="nav-item ">
                        <a class="nav-link " href="{{route('keptkayas.tbank.items_group.index')}}">
                            <div
                                class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                                <i class="fa fa-object-group text-danger text-gradient text-lg"></i>
                            </div>
                            <div class="d-flex justify-content-between" style="width: 100%">

                                <span class="sidenav-normal">ประเภทขยะรีไซเคิล</span>
                                @if ($infos_count['items_group_count'] > 0)
                                    <span
                                        class="badge badge-sm badge-circle badge-floating badge-danger border-white">{{ $infos_count['items_group_count'] }}</span>
                                @endif
                            </div>
                        </a>
                    </li>
                    <li class="nav-item ">
                        <a class="nav-link " href="{{route('keptkayas.tbank.units.index')}}">
                            <div
                                class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                                <i class="fa fa-tablets text-danger text-gradient text-lg"></i>
                            </div>
                            <div class="d-flex justify-content-between" style="width: 100%">
                                <span class="sidenav-normal">หน่วยนับ</span>
                                @if ($infos_count['units_count'] > 0)
                                    <span
                                        class="badge badge-sm badge-circle badge-floating badge-danger border-white">{{ $infos_count['units_count'] }}</span>
                                @endif
                            </div>

                        </a>
                    </li>
                    <li class="nav-item ">
                        <a class="nav-link " href="{{route('keptkayas.tbank.items.index')}}">
                            <div
                                class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                                <i class="fa fa-wine-bottle text-danger text-gradient text-lg"></i>
                            </div>
                            <div class="d-flex justify-content-between" style="width: 100%">

                                <span class="sidenav-normal">ขยะรีไซเคิล</span>
                                @if ($infos_count['items_count'] > 0)
                                    <span
                                        class="badge badge-sm badge-circle badge-floating badge-danger border-white">{{ $infos_count['items_count'] }}</span>
                                @endif
                            </div>

                        </a>
                    </li>
                    <li class="nav-item  ">
                        <a class="nav-link @yield('nav-cart')" href="{{ route('keptkayas.tbank.prices.index') }}">
                            <div
                                class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                                <i class="fa fa-money-bill-wave text-danger text-gradient text-lg"></i>
                            </div>
                            <div class="d-flex justify-content-between" style="width: 100%">

                                <span class="sidenav-normal">ตั้งราคารับซื้อขยะรีไซเคิล</span>
                                @if ($infos_count['items_prices_count'] > 0)
                                    <span
                                        class="badge badge-sm badge-circle badge-floating badge-danger border-white">{{ $infos_count['items_prices_count'] }}</span>
                                @endif
                            </div>


                        </a>
                    </li>
                    <li class="nav-item  ">
                        <a class="nav-link @yield('nav-cart')" href="{{ route('keptkayas.purchase-shops.index') }}">
                            <div
                                class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                                <i class="fa fa-home text-danger text-gradient text-lg"></i>
                            </div>
                            <div class="d-flex justify-content-between" style="width: 100%">
                                <span class="sidenav-normal">ร้านรับซื้อขยะ </span>

                                @if ($infos_count['shop_count'] > 0)
                                    <span
                                        class="badge badge-sm badge-circle badge-floating badge-danger border-white">{{ $infos_count['shop_count'] }}</span>
                                @endif
                            </div>

                        </a>
                    </li>

                    <li class="nav-item  ">
                        <a class="nav-link @yield('nav-cart')" href="{{ route('keptkayas.purchase.routes.index') }}">
                            <div
                                class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                                <i class="fa fa-home text-danger text-gradient text-lg"></i>
                            </div>
                            <div class="d-flex justify-content-between" style="width: 100%">
                                <span class="sidenav-normal">สร้างเขตรับซื้อขยะ </span>

                                    <span
                                        class="badge badge-sm badge-circle badge-floating badge-danger border-white">{{ $infos_count['shop_count'] }}</span>
                            </div>

                        </a>
                    </li>

            </div>
        </li>
        <li class="nav-item">
    <a class="nav-link @yield('nav-keptkayas.settings') _active" href="{{ route('keptkayas.settings.index') }}">
        <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
            <i class="fa fa-sliders-h text-secondary text-gradient text-lg"></i>
        </div>
        <span class="sidenav-normal">เงื่อนไขธนาคารขยะ</span>
    </a>
</li>


    {{-- @endif --}}

    {{-- <li class="nav-item">
                        <a class="nav-link @yield('nav-keptkayas.users')" href="{{route('keptkayas.users.index')}}">
                            <div
                                class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                                <i class="fa fa-user-friends text-dark text-gradient text-lg"></i>
                            </div>
                            <span class="sidenav-normal">สมาชิก</span>
                        </a>
                    </li> --}}

    <li class="nav-item">
        <a href="{{ route('logout') }}" class="nav-link active mt-4" style="border: 1px solid red">
            <div
                class="fab fa-ubuntu icon-sm shadow border-radius-md bg-danger text-center d-flex align-items-center justify-content-center  me-2">
            </div>
            <span class="nav-link-text ms-1 text-danger">Log Out</span>
        </a>

    </li>

    {{-- @endcan --}}

</ul>