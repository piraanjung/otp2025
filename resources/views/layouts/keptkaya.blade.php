<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}"> <!-- เพิ่มบรรทัดนี้ -->
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('soft-ui/assets/img/apple-icon.png') }}">
    <link rel="icon" type="image/png" href="{{ asset('logo/ko_envsogo.png') }}">
    <title>
        EnvSoGo::Kept Kaya
    </title>



    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"
        integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer" />

    {{-- DataTables CSS --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.0/css/dataTables.dataTables.css">


    <link href="{{ asset('soft-ui/assets/css/nucleo-icons.css') }}" rel="stylesheet" />
    <link href="{{ asset('soft-ui/assets/css/nucleo-svg.css') }}" rel="stylesheet" />

    <link href="{{ asset('soft-ui/assets/css/nucleo-svg.css') }}" rel="stylesheet" />

    <link id="pagestyle" href="{{ asset('soft-ui/assets/css/soft-ui-dashboard.min.css') }}" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <style>
        @media (max-width: 767px) {
            .navbar-vertical.navbar-expand-xs {
                max-width: 14.625rem !important;
            }

            .container,
            .container-fluid,
            .container-lg,
            .container-md,
            .container-sm,
            .container-xl,
            .container-xxl {
                padding-left: 5px;
                padding-right: 5px;
            }
        }
        .navbar-vertical.navbar-expand-xs .navbar-collapse {
            display: block;
            overflow: auto;
            height: calc(100vh) !important;
        }

        #overlay {
            position: fixed;
            top: 0;
            z-index: 1050;
            width: 100%;
            height: 100%;
            display: none;
            background: rgba(0, 0, 0, 0.6);
        }

        input:read-only {
            background-color: #e9ecef !important;
        }

        .table thead th {
            padding: 0.2rem 0.2rem !important;
        }

        .form-control {
            display: block;
            width: 100%;
            padding: 0.2rem 0.2rem !important;
            font-size: .875rem;
            font-weight: 400;
            line-height: 1.4rem;
            color: #495057;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid #d2d6da;
            appearance: none;
            transition: box-shadow .15s ease, border-color .15s ease;
        }

        .navbar-vertical.navbar-expand-xs .navbar-nav .nav .nav-link {
            padding-top: .417rem;
            padding-bottom: .417rem;
            padding-left: 0;
        }
        .ps-3 {
            padding-left: 0rem !important;
        }
        .btn-group-sm>.btn i, .btn.btn-sm i {
            font-size: 1rem
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <script src="https://cdn.datatables.net/plug-ins/1.13.6/pagination/select.js"></script>
    @yield('style')
</head>

<body class="g-sidenav-show  bg-gray-100" id="">
    <div id="overlay">
        <div class="cv-spinner">
            <button class="btn btn-primary btn-sm ml-2 mb-2" type="button" disabled>
                <span class="spinner-border spinner-border" role="status" aria-hidden="true"></span>
                <span class="h3 text-white">โปรดรอสักครู่! มีการสร้างข้อมูลจำนวนมาก...</span>
            </button>
        </div>
    </div>
    <aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3 "
        id="sidenav-main">
        <div class="sidenav-header">
            <i class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none"
                aria-hidden="true" id="iconSidenav"></i>
            <a class="navbar-brand m-0" href=" https://demos.creative-tim.com/soft-ui-dashboard/pages/dashboard.html "
                target="_blank">
                <img src="{{ asset('soft-ui/assets/img/logo-ct-dark.png') }}" class="navbar-brand-img h-100"
                    alt="main_logo">
                <span class="ms-1 font-weight-bold">Kept Kaya</span>
            </a>
        </div>
        <hr class="horizontal dark mt-0">
        <div class="collapse navbar-collapse  w-auto " id="sidenav-collapse-main">
            @include('layouts.keptkaya_navigation')
        </div>

    </aside>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
        <nav
            class="navbar navbar-main navbar-expand-lg bg-transparent shadow-none position-absolute px-4 w-100 z-index-2">
            <div class="container-fluid py-1">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 ps-2 me-sm-6 me-5">
                        <li class="breadcrumb-item text-sm"><a class="text-white opacity-8"
                                href="@yield('route-header')">@yield('nav-header')</a></li>
                        <li class="breadcrumb-item text-sm text-white active" aria-current="page">@yield('nav-main')
                        </li>
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
                        <li class="nav-item d-flex align-items-center">
                            <a href="{{ route('logout') }}" class="nav-link text-white font-weight-bold px-0">
                                <i class="fa fa-user me-sm-1" aria-hidden="true"></i>
                                <span class="d-sm-inline d-none">Log Out</span>
                            </a>
                        </li>
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
                                <i class="fa fa-cog fixed-plugin-button-nav cursor-pointer" aria-hidden="true"></i>
                            </a>
                        </li>
                        <li class="nav-item dropdown pe-2 d-flex align-items-center">
                            <a href="javascript:;" class="nav-link text-white p-0" id="dropdownMenuButton"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa fa-bell cursor-pointer" aria-hidden="true"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end px-2 py-3 ms-n4"
                                aria-labelledby="dropdownMenuButton">
                                <li class="mb-2">
                                    <a class="dropdown-item border-radius-md" href="javascript:;">
                                        <div class="d-flex py-1">
                                            <div class="my-auto">
                                                <img src="{{ asset('soft-ui/assets/img/team-2.jpg') }}"
                                                    class="avatar avatar-sm me-3" alt="user image">
                                            </div>
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="text-sm font-weight-normal mb-1">
                                                    <span class="font-weight-bold">New message</span> from Laur
                                                </h6>
                                                <p class="text-xs text-secondary mb-0">
                                                    <i class="fa fa-clock me-1" aria-hidden="true"></i>
                                                    13 minutes ago
                                                </p>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                                <li class="mb-2">
                                    <a class="dropdown-item border-radius-md" href="javascript:;">
                                        <div class="d-flex py-1">
                                            <div class="my-auto">
                                                <img src=" {{ asset('soft-ui/assets/img/small-logos/logo-spotify.svg') }}"
                                                    class="avatar avatar-sm bg-gradient-dark me-3" alt="logo spotify">
                                            </div>
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="text-sm font-weight-normal mb-1">
                                                    <span class="font-weight-bold">New album</span> by Travis Scott
                                                </h6>
                                                <p class="text-xs text-secondary mb-0">
                                                    <i class="fa fa-clock me-1" aria-hidden="true"></i>
                                                    1 day
                                                </p>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item border-radius-md" href="javascript:;">
                                        <div class="d-flex py-1">
                                            <div class="avatar avatar-sm bg-gradient-secondary me-3 my-auto">
                                                <svg width="12px" height="12px" viewBox="0 0 43 36" version="1.1"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    xmlns:xlink="http://www.w3.org/1999/xlink">
                                                    <title>credit-card</title>
                                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                        <g id="Rounded-Icons"
                                                            transform="translate(-2169.000000, -745.000000)"
                                                            fill="#FFFFFF" fill-rule="nonzero">
                                                            <g transform="translate(1716.000000, 291.000000)">
                                                                <g transform="translate(453.000000, 454.000000)">
                                                                    <path class="color-background"
                                                                        d="M43,10.7482083 L43,3.58333333 C43,1.60354167 41.3964583,0 39.4166667,0 L3.58333333,0 C1.60354167,0 0,1.60354167 0,3.58333333 L0,10.7482083 L43,10.7482083 Z"
                                                                        opacity="0.593633743"></path>
                                                                    <path class="color-background"
                                                                        d="M0,16.125 L0,32.25 C0,34.2297917 1.60354167,35.8333333 3.58333333,35.8333333 L39.4166667,35.8333333 C41.3964583,35.8333333 43,34.2297917 43,32.25 L43,16.125 L0,16.125 Z M19.7083333,26.875 L7.16666667,26.875 L7.16666667,23.2916667 L19.7083333,23.2916667 L19.7083333,26.875 Z M35.8333333,26.875 L28.6666667,26.875 L28.6666667,23.2916667 L35.8333333,23.2916667 L35.8333333,26.875 Z">
                                                                    </path>
                                                                </g>
                                                            </g>
                                                        </g>
                                                    </g>
                                                </svg>
                                            </div>
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="text-sm font-weight-normal mb-1">
                                                    Payment successfully completed
                                                </h6>
                                                <p class="text-xs text-secondary mb-0">
                                                    <i class="fa fa-clock me-1" aria-hidden="true"></i>
                                                    2 days
                                                </p>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="container-fluid">
            <div class="page-header min-height-200 border-radius-xl mt-4"
                style="background-image: url('{{ asset('soft-ui/assets/img/curved-images/curved0.jpg') }}'); background-position-y: 50%;">
                <span class="mask bg-gradient-primary opacity-6"></span>
            </div>
            <div class="card card-body blur shadow-blur mx-1 mt-n7 overflow-hidden">
                <div class="row gx-4">
                    <div class="col-auto">
                        <div class="avatar avatar-xl position-relative">
                            <img src="{{asset('soft-ui/assets/img/bruce-mars.jpg')}}" alt="profile_image"
                                class="w-100 border-radius-lg shadow-sm">
                        </div>
                    </div>
                    <div class="col-auto my-auto">
                        <div class="h-100">
                            <h5 class="mb-1">
                                @yield('page-topic')
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
        <div class="container-fluid py-4 ">
            @if ($message = Session::get('message'))
                <div class="alert alert-{{ Session::get('color') }} alert-block">
                    {{-- <button type="button" class="close" data-dismiss="alert">×</button> --}}
                    <strong>{{ $message }}</strong>
                </div>
            @endif

            @yield('content')
        </div>
    </main>
    <script src="https://code.jquery.com/jquery-1.12.4.js"
        integrity="sha256-Qw82+bXyGq6MydymqBxNPYTaUXXq7c8v3CwiYwLLNXU=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script src="{{ asset('soft-ui/assets/js/core/popper.min.js') }}"></script>
    <script src="{{ asset('soft-ui/assets/js/core/bootstrap.min.js') }}"></script>
    <script src="{{ asset('soft-ui/assets/js/plugins/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('soft-ui/assets/js/plugins/smooth-scrollbar.min.js') }}"></script>
    <script src="{{ asset('soft-ui/assets/js/soft-ui-dashboard.min.js') }}"></script>

    @yield('script')

    <script>
        $(document).ready(function () {
            setTimeout(() => {
                $('.alert').toggle('slow')
            }, 3000);

        })

        $(document).ready(function () {
            $('.js-example-basic-single').select2();
        });
    </script>

</body>

</html>