<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />

    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('soft-ui/assets/img/apple-icon.png') }}">
    <link rel="icon" type="image/png" href="{{ asset('logo/ko_envsogo.png') }}">
    <title>
        EnvSoGo::Tabwater
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
        .card .card-body {
            font-family: "Sarabun", sans-serif;
            padding: 1.5rem;
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
    </style>
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <script src="https://cdn.datatables.net/plug-ins/1.13.6/pagination/select.js"></script>
    @yield('style')
</head>

<body class="g-sidenav-show  bg-gray-100">

    <aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3 "
        id="sidenav-main">
        <div class="sidenav-header">
            <i class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none"
                aria-hidden="true" id="iconSidenav"></i>
            <a class="navbar-brand m-0" href=" https://demos.creative-tim.com/soft-ui-dashboard/pages/dashboard.html "
                target="_blank">
                <img src="{{ asset('logo/'.$orgInfos['org_logo_img']) }}" class="navbar-brand-img h-100" alt="main_logo">
                <span class="ms-1 font-weight-bold">งานประปา {{$orgInfos['org_short_type_name'].$orgInfos['org_name']}}</span>
            </a>
        </div>
        <hr class="horizontal dark mt-0">
        <div class="collapse navbar-collapse  w-auto " id="sidenav-collapse-main">
            @include('layouts.admin1_navigation')
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
                        <li class="nav-item dropdown pe-2 d-flex align-items-center">


                            <a href="javascript:;" class="nav-link text-white" id="dropdownMenuButton"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <div class="dropdown-item border-radius-md" href="javascript:;">
                                    <div class="d-flex py-1">
                                        <div class="my-auto">
                                            <img src="{{ asset('Applight/images/user1.jpg') }}"
                                                class="avatar avatar-sm me-3" alt="user image">
                                        </div>
                                        <div class="d-flex flex-column justify-content-center">
                                            <h6 class="font-weight-normal mb-1">
                                                <span
                                                    class="font-weight-bold pr-3 text-white">{{ Auth::user()->firstname . ' ' . Auth::user()->lastname . ' (' . Auth::user()->id . ')' }}</span>
                                            </h6>

                                        </div>
                                    </div>
                                </div>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end px-2 py-3 ms-n4"
                                aria-labelledby="dropdownMenuButton">

                                <li class="">

                                    <div class="dropdown-item border-radius-md" href="javascript:;">
                                        <div class="d-flex py-1">

                                            <div class="d-flex flex-column justify-content-center">

                                                <form method="POST" action="{{ route('logout') }}">

                                                    @csrf
                                                    <a href="#"
                                                        onclick="event.preventDefault();this.closest('form').submit();"
                                                        class="nav-link  font-weight-bold px-0" target="_blank">
                                                        <i class="fa fa-sign-out me-sm-1" aria-hidden="true"></i>
                                                        <span class="d-sm-inline d-none">Log out</span>
                                                    </a>
                                                </form>

                                            </div>
                                        </div>
                                    </div>

                                </li>
                            </ul>
                        </li>
                    </ul>

                </div>
            </div>
        </nav>

        <div class="container-fluid">
            <div class="page-header min-height-200 border-radius-xl mt-4" style=" background-position-y: 50%;">
                <span class="mask bg-gradient-primary opacity-6"></span>
            </div>
            <div class="card card-body blur shadow-blur mx-4 mt-n6 overflow-hidden">
                <div class="row gx-4">
                    <div class="col-auto">
                        <div class="avatar avatar-xl position-relative">
                            <img src="{{ asset('logo/'.$orgInfos['org_logo_img'] )}}" alt="profile_image"
                                class="w-100 border-radius-lg shadow-sm">
                        </div>
                    </div>
                    <div class="col-auto my-auto">
                        <div class="h-100">
                            <h5 class="mb-1">
                                @yield('nav-topic')
                            </h5>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 my-sm-auto ms-sm-auto me-sm-0 mx-auto mt-3">
                        <div class="nav-wrapper position-relative end-0">

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container-fluid py-4">
            @if (session()->has('message'))
            <div class="alert alert-{{session()->get('color')}} alert-dismissible fade show" role="alert">
                                    <strong>{{ session()->get('message') }}</strong>

                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
            {{-- @if (Session::has('message'))
                <div class="alert alert-{{ Session::get('color') }} alert-block" id="alert_message">
                    <strong>{{ Session::get('message') }}</strong>
                </div>
            @endif --}}
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