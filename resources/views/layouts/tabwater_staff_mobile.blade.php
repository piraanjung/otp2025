<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />

    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('soft-ui/assets/img/apple-icon.png') }}">
    <link rel="icon" type="image/png" href="{{ asset('soft-ui/assets/img/favicon.png') }}">
    <title>
        Tabwater
    </title>



    {{-- <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" /> --}}
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:ital,wght@0,500;0,700;1,400;1,500&display=swap"
        rel="stylesheet">
    <link href="{{ asset('soft-ui/assets/css/nucleo-icons.css') }}" rel="stylesheet" />
    <link href="{{ asset('soft-ui/assets/css/nucleo-svg.css') }}" rel="stylesheet" />

    <link href="{{ asset('soft-ui/assets/css/nucleo-svg.css') }}" rel="stylesheet" />

    <link id="pagestyle" href="{{ asset('soft-ui/assets/css/soft-ui-dashboard.min.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css"
        integrity="sha384-DyZ88mC6Up2uqS4h/KRgHuoeGwBcD4Ng9SiP4dIRy0EXTlnuz47vAwmeGwVChigm" crossorigin="anonymous" />
    <script src="{{ asset('adminlte/plugins/jquery/jquery.js') }}"></script>
    <style>
        body{
             font-family: "Sarabun", sans-serif;
        }
        .navbar-vertical.navbar-expand-xs .navbar-collapse {
            display: block;
            overflow: auto;
            height: calc(100vh) !important;
        }

        .navbar-vertical .navbar-nav .nav-item .collapse .nav .nav-item .nav-link,
        .navbar-vertical .navbar-nav .nav-item .collapsing .nav .nav-item .nav-link {
            position: relative;
            background-color: transparent;
            box-shadow: none;
            color: black;
            margin-left: 1.35rem;
        }

        .btn-link {
            border: none;
            outline: none;
            background: none;
            cursor: pointer;
            padding: 0;
            font-family: inherit;
            font-size: inherit;
        }

        .btn-link:active {
            color: #FF0000;
        }

        .navbar-vertical .navbar-nav>.nav-item .nav-link.active {
            color: #344767;
            background-color: #fff;
            font-size: 1.15rem;
        }

        .form-label,
        label {
            font-size: 1rem;
            font-weight: 700;
            margin-bottom: .5rem;
            color: #344767;
            margin-left: .25rem;
        }

        .navbar-vertical .navbar-nav .nav-item .collapse .nav .nav-item .nav-link,
        .navbar-vertical .navbar-nav .nav-item .collapsing .nav .nav-item .nav-link {
            position: relative;
            background-color: transparent;
            box-shadow: none;
            color: black;
            margin-left: 1.35rem;
            font-size: 1rem;
        }

        input:read-only {
            background-color: rgb(170 175 188);
            color: black !important
        }

        .selected {
            background: lightblue
        }

        .dataTables_length,
        .dt-buttons,
        .dataTables_filter,
        .select_row_all,
        .deselect_row_all,
        .create_user {
            display: inline-flex;
        }

        .dt-buttons,
        .select_row_all,
        .deselect_row_all,
        .create_user {
            flex-direction: column
        }

        .dt-buttons {
            margin-left: 3%
        }

        .dataTables_filter {
            margin-left: 2%
        }

        .preloader-wrapper {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: #111;
            opacity: 0.8;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1200;
            transition: all .4s ease;
        }

        .fade-out-animation {
            opacity: 0;
            visibility: hidden;
        }

        @media (max-width: 1199.98px) {
            .g-sidenav-show.rtl .sidenav {
                transform: translateX(19.125rem)
            }

            .g-sidenav-show:not(.rtl) .sidenav {
                transform: translateX(-19.125rem);
            }

            .g-sidenav-show .sidenav.fixed-start~.main-content {
                margin-left: 0 !important
            }

            .g-sidenav-show.g-sidenav-pinned .sidenav {
                transform: translateX(0)
            }

        }

        .hidden {
            display: none
        }

        .card {
            border: 1px solid #ccc;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);

        }
        .padding4px{
            padding: 4px !important
        }
        .min-height-200 {
            min-height: 50px !important;
        }
        .container, .container-fluid, .container-lg, .container-md, .container-sm, .container-xl, .container-xxl
 {
    padding-right: calc(var(--bs-gutter-x) * 1);
    padding-left: calc(var(--bs-gutter-x) * 1);
}
    </style>
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    {{-- <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script> --}}

    {{-- <script src="https://cdn.datatables.net/plug-ins/1.13.6/pagination/select.js"></script> --}}
   <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&icon_names=arrow_circle_right" />
    @yield('style')
</head>

<body class="g-sidenav-show  bg-gray-100">

    <aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3 pr-5"
        id="sidenav-main">
        <div class="sidenav-header">
            <i class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none"
                aria-hidden="true" id="iconSidenav"></i>
            <a class="navbar-brand m-0" href=" https://demos.creative-tim.com/soft-ui-dashboard/pages/dashboard.html "
                target="_blank">
                <img src="{{ asset('logo/khampom.png') }}" class="navbar-brand-img h-100" alt="main_logo">
                <span class="ms-1 font-weight-bold">งานประปา อบต.ขามป้อม</span>
            </a>
        </div>
        <hr class="horizontal dark mt-0">
        <div class="collapse navbar-collapse  w-auto  p-3" id="sidenav-collapse-main">
              <div class="row">
        <div class="col-12">
            <div class="card  mb-4">
                <div class="card-body p-3">
                  <div class="row">
                    <div class="col-8">
                      <div class="numbers">
                        <p class="text-sm mb-0 text-capitalize font-weight-bold">OPT-CONNECT</p>
                        <h5 class="font-weight-bolder mb-0">
                          {{-- {{$staff->firstname." ".$staff->lastname}} --}}
                          
                        </h5>
                        <p class="text-danger text-sm font-weight-bolder">Tabwater</ย>
                      </div>
                    </div>
                    <div class="col-4 text-end">
                      <div class="icon icon-shape bg-primary shadow text-center border-radius-md">
                        <i class="ni ni-paper-diploma text-lg opacity-10" aria-hidden="true"></i>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
        </div>
    </div>
            <ul class="navbar-nav">

    <li class="nav-item">
        <a class="nav-link active mb-2 bg-warning @yield('nav-accessmenu')" href="{{ route('accessmenu') }}">
            <div class="icon icon-shape icon-sm bg-white shadow text-center border-radius-2xl padding4px" >
                <i class="ni ni-shop  text-dark text-white text-lg opacity-10  justify-content-center" aria-hidden="true"></i>
            </div>
            <span class="nav-link-text ms-1">หน้าเมนูหลัก</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link  @yield('nav-dashboard')" href="{{ route('dashboard') }}">
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
            </ul>
        </div>

    </aside>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
        <nav
            class="navbar navbar-main navbar-expand-lg bg-transparent shadow-none position-absolute px-4 w-100 z-index-2">
            <div class="container-fluid py-1">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 ps-2 me-sm-6 me-5">
                        <li class="breadcrumb-item text-sm"><a class="text-white"
                                href="javascript:;">@yield('nav-header')</a></li>
                        <li class="breadcrumb-item text-sm text-white active" aria-current="page">@yield('nav-main')</li>
                    </ol>
                    <h6 class="text-white font-weight-bolder ms-2">@yield('nav-current')</h6>
                </nav>
                <div class="sidenav-toggler sidenav-toggler-inner d-xl-block d-none">
                    <a href="javascript:;" class="nav-link text-white p-0">
                        <div class="sidenav-toggler-inner">
                            <i class="sidenav-toggler-line bg-white"></i>
                            <i class="sidenav-toggler-line bg-white"></i>
                            <i class="sidenav-toggler-line bg-white"></i>

                        </div>
                    </a>
                </div>
                <div class="collapse navbar-collapse me-md-0 me-sm-4 mt-sm-0 mt-2" id="navbar">
                    <div class="ms-md-auto pe-md-3 d-flex align-items-center">
                        <div class="input-group"></div>
                    </div>
                    <ul class="navbar-nav justify-content-end">

                        <li class="nav-item d-xl-none ps-3 pe-0 d-flex align-items-center">
                            <a href="javascript:;" class="nav-link text-body p-0" id="iconNavbarSidenav">
                                <div class="sidenav-toggler-inner">
                                    <i class="sidenav-toggler-line bg-white"></i>
                                    <i class="sidenav-toggler-line bg-white"></i>
                                    <i class="sidenav-toggler-line bg-white"></i>
                                </div>
                            </a>
                        </li>

                        <li class="nav-item px-3 d-flex align-items-center">
                            <a href="javascript:;" class="nav-link text-white p-0">
                                {{-- <i class="fa fa-cog fixed-plugin-button-nav cursor-pointer" aria-hidden="true"></i>
                                --}}
                            </a>
                        </li>
                        
                    </ul>

                </div>
            </div>
        </nav>

        <div class="container-fluid">
            <div class="page-header min-height-200 border-radius-xl mt-4" style=" background-position-y: 50%;">
                <span class="mask bg-gradient-primary opacity-6"></span>
            </div>
        
        </div>
        <div class="container-fluid py-4">
        
            @yield('content')
        </div>
    </main>

    <script src="{{ asset('soft-ui/assets/js/core/popper.min.js') }}"></script>
    <script src="{{ asset('soft-ui/assets/js/core/bootstrap.min.js') }}"></script>
    <script src="{{ asset('soft-ui/assets/js/plugins/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('soft-ui/assets/js/plugins/smooth-scrollbar.min.js') }}"></script>
    <script src="{{ asset('soft-ui/assets/js/soft-ui-dashboard.min.js') }}"></script>


    @yield('script')

    <script>
        $(document).ready(function () {
            setTimeout(function () {
                $('.alert').toggle();
            }, 1000);
            $('.alert').hide()
        })
    </script>

</body>

</html>