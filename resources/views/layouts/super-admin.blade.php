<!DOCTYPE html>
<html lang="th">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('soft-ui/assets/img/apple-icon.png')}}">
  <link rel="icon" type="image/png" href="{{ asset('logo/ko_envsogo.png')}}">
  <title>@hasSection('title')@yield('title')@else @yield('title_page', 'Envsogo Admin')@endif | Envsogo</title>

  <!-- Fonts and icons -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;600;700&display=swap" rel="stylesheet" />
  <!-- Nucleo Icons -->
  <link href="{{ asset('soft-ui/assets/css/nucleo-icons.css')}}" rel="stylesheet" />
  <link href="{{ asset('soft-ui/assets/css/nucleo-svg.css')}}" rel="stylesheet" />
  <!-- Font Awesome 5 (ต้องเป็น v5 เพราะ soft-ui-dashboard.css อ้าง 'Font Awesome 5 Free'; ใช้ class `fas`/`far`/`fab` ไม่ใช่ `fa-solid`) -->
  <link rel="stylesheet" href="{{ asset('adminlte/plugins/fontawesome-free/css/all.min.css') }}" />
  <!-- Main CSS -->
  <link id="pagestyle" href="{{ asset('soft-ui/assets/css/soft-ui-dashboard.css?v=1.0.7')}}" rel="stylesheet" />

  <style>
    /* Open Sans ไม่มีอักษรไทย ให้ใช้ Sarabun เป็นตัวสำรอง */
    body, .btn, .form-control, .form-select, .dropdown-menu, .table {
      font-family: "Open Sans", "Sarabun", sans-serif;
    }
    .sidenav .nav-link-text { white-space: normal; }
  </style>
  @yield('style')
</head>

<body class="g-sidenav-show bg-gray-100">
  <aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3" id="sidenav-main">
    @include('layouts.super-admin-navigation')
  </aside>

  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
    <!-- Navbar -->
    <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" navbar-scroll="true">
      <div class="container-fluid py-1 px-3">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
            <li class="breadcrumb-item text-sm">
              <a class="opacity-5 text-dark" href="@yield('nav-main-url', route('admin.dashboard'))">@yield('nav-main', 'หน้าหลัก')</a>
            </li>
            <li class="breadcrumb-item text-sm text-dark active" aria-current="page">
              @hasSection('nav-current')@yield('nav-current')@else @yield('title_page')@endif
            </li>
          </ol>
          <h6 class="font-weight-bolder mb-0">
            @hasSection('nav-current-title')@yield('nav-current-title')@else @yield('title_page')@endif
          </h6>
        </nav>

        <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
          <ul class="navbar-nav ms-auto justify-content-end">
            <li class="nav-item dropdown d-flex align-items-center">
              <a href="javascript:;" class="nav-link text-body font-weight-bold px-0" id="userMenuButton"
                 data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fa fa-user me-sm-1"></i>
                <span class="d-sm-inline d-none">
                  {{ trim((Auth::user()->firstname ?? '') . ' ' . (Auth::user()->lastname ?? '')) ?: Auth::user()->username }}
                </span>
              </a>
              <ul class="dropdown-menu dropdown-menu-end px-2 py-3" aria-labelledby="userMenuButton">
                <li>
                  <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="dropdown-item border-radius-md">
                      <i class="fa fa-sign-out-alt me-2"></i> ออกจากระบบ
                    </button>
                  </form>
                </li>
              </ul>
            </li>
            <li class="nav-item d-xl-none ps-3 d-flex align-items-center">
              <a href="javascript:;" class="nav-link text-body p-0" id="iconNavbarSidenav">
                <div class="sidenav-toggler-inner">
                  <i class="sidenav-toggler-line"></i>
                  <i class="sidenav-toggler-line"></i>
                  <i class="sidenav-toggler-line"></i>
                </div>
              </a>
            </li>
          </ul>
        </div>
      </div>
    </nav>
    <!-- End Navbar -->

    <div class="container-fluid py-4">
      @include('layouts.partials.flash')

      @yield('content')
    </div>
  </main>

  <!-- soft-ui-dashboard.js อ้าง [data-class] ของ configurator ตอน resize; ใส่ตัวแทนไว้เพื่อไม่ให้เกิด TypeError -->
  <span data-class="bg-white" class="d-none" aria-hidden="true"></span>

  <!-- Core JS Files -->
  <script src="{{ asset('soft-ui/assets/js/core/popper.min.js')}}"></script>
  <script src="{{ asset('soft-ui/assets/js/core/bootstrap.min.js')}}"></script>
  <script src="{{ asset('soft-ui/assets/js/plugins/perfect-scrollbar.min.js')}}"></script>
  <script src="{{ asset('soft-ui/assets/js/plugins/smooth-scrollbar.min.js')}}"></script>
  <script src="{{ asset('soft-ui/assets/js/soft-ui-dashboard.min.js?v=1.0.7')}}"></script>
  <script src="{{ asset('js/jquery-3.7.1.slim.js')}}"></script>

  @yield('script')
</body>
</html>
