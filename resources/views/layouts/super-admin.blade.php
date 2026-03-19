<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('soft-ui/assets/img/apple-icon.png')}}">
  <link rel="icon" type="image/png" href="{{ asset('soft-ui/assets/img/favicon.png')}}">
  <title>@yield('title', 'Flat Dashboard')</title>

  <!-- Fonts and icons -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
  <!-- Nucleo Icons -->
  <link href="{{ asset('soft-ui/assets/css/nucleo-icons.css')}}" rel="stylesheet" />
  <link href="{{ asset('soft-ui/assets/css/nucleo-svg.css')}}" rel="stylesheet" />
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" integrity="sha384-DyZ88mC6Up2uqS4h/KRgHuoeGwBcD4Ng9SiP4dIRy0EXTlnuz47vAwmeGwVChigm" crossorigin="anonymous" />
  <!-- Main CSS -->
  <link id="pagestyle" href="{{ asset('soft-ui/assets/css/soft-ui-dashboard.css?v=1.0.7')}}" rel="stylesheet" />

  <style>
    /* --- Flat UI Overrides --- */

    /* 1. ปรับพื้นหลังรวมให้ดูสะอาด (Minimal Gray) */
    body.bg-gray-100 {
      background-color: #f8f9fa !important;
    }

    /* 2. ลบ Shadow และใช้ Border แทนใน Card และ Sidebar */
    .card, .sidenav, .navbar-main {
      box-shadow: none !important;
      border: 1px solid #e9ecef !important;
      border-radius: 8px !important;
    }

    /* 3. ปรับ Navbar ให้แบนและติดขอบบนชัดเจน */
    #navbarBlur {
      background-color: #ffffff !important;
      border-bottom: 1px solid #e9ecef !important;
      backdrop-filter: none !important;
      box-shadow: none !important;
    }

    /* 4. ปรับสีพื้นหลัง Sidenav ให้ดู Flat */
    .sidenav {
      background-color: #ffffff !important;
    }

    /* 5. ปรับแต่ง Input ให้เป็นสไตล์ Flat Gray */
    .input-group {
      background-color: #f1f3f5 !important;
      border: 1px solid #e9ecef !important;
      border-radius: 6px !important;
      box-shadow: none !important;
    }
    .input-group .form-control {
      background-color: transparent !important;
      border: none !important;
    }
    .input-group-text {
      background-color: transparent !important;
      border: none !important;
    }

    /* 6. ยกเลิก Gradient (การไล่สี) ให้ใช้สีพื้น (Solid Color) */
    .bg-gradient-primary, .btn-primary, .btn-outline-primary:hover {
      background-image: none !important;
      background-color: #5e72e4 !important;
      border: none !important;
    }
    .bg-gradient-dark, .btn-dark {
      background-image: none !important;
      background-color: #344767 !important;
    }
    .bg-gradient-success { background-image: none !important; background-color: #2dce89 !important; }
    .bg-gradient-info { background-image: none !important; background-color: #11cdef !important; }
    .bg-gradient-warning { background-image: none !important; background-color: #fb6340 !important; }
    .bg-gradient-danger { background-image: none !important; background-color: #f5365c !important; }

    /* 7. ปรับ Breadcrumb ให้คมชัด */
    .breadcrumb-item a, .breadcrumb-item.active {
      color: #525f7f !important;
      font-weight: 600;
    }

    /* 8. ปรับระยะ Icon ในปุ่ม */
    .btn.btn-sm i {
      font-size: 0.85rem;
      margin-right: 4px;
    }

    /* 9. ลบความนูนออกจาก Badge */
    .badge {
      text-transform: none;
      font-weight: 600;
      box-shadow: none !important;
    }
  </style>
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
              <a class="opacity-5 text-dark" href="@yield('nav-main-url')">@yield('nav-main')</a>
            </li>
            <li class="breadcrumb-item text-sm text-dark active" aria-current="page">@yield('nav-current')</li>
          </ol>
          <h6 class="font-weight-bolder mb-0">@yield('nav-current-title')</h6>
        </nav>

        <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
          <div class="ms-md-auto pe-md-3 d-flex align-items-center">
            <div class="input-group">
              <span class="input-group-text text-body"><i class="fas fa-search" aria-hidden="true"></i></span>
              <input type="text" class="form-control" placeholder="Search...">
            </div>
          </div>

          <ul class="navbar-nav justify-content-end">
            <li class="nav-item d-flex align-items-center">
              <a href="javascript:;" class="nav-link text-body font-weight-bold px-0">
                <i class="fa fa-user me-sm-1"></i>
                <span class="d-sm-inline d-none">Sign In</span>
              </a>
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
            <li class="nav-item px-3 d-flex align-items-center">
              <a href="javascript:;" class="nav-link text-body p-0">
                <i class="fa fa-cog fixed-plugin-button-nav cursor-pointer"></i>
              </a>
            </li>
            <!-- Notifications Dropdown -->
            <li class="nav-item dropdown pe-2 d-flex align-items-center">
              <a href="javascript:;" class="nav-link text-body p-0" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fa fa-bell cursor-pointer"></i>
              </a>
              <ul class="dropdown-menu dropdown-menu-end px-2 py-3 me-sm-n4" aria-labelledby="dropdownMenuButton">
                <!-- Dropdown items... -->
                <li class="mb-2">
                  <a class="dropdown-item border-radius-md" href="javascript:;">
                    <div class="d-flex py-1">
                      <div class="my-auto">
                        <img src="{{ asset('soft-ui/assets/img/team-2.jpg')}}" class="avatar avatar-sm me-3 ">
                      </div>
                      <div class="d-flex flex-column justify-content-center">
                        <h6 class="text-sm font-weight-normal mb-1">
                          <span class="font-weight-bold">New message</span> from Laur
                        </h6>
                        <p class="text-xs text-secondary mb-0">
                          <i class="fa fa-clock me-1"></i> 13 mins ago
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
    <!-- End Navbar -->

    <div class="container-fluid py-4">
      <!-- Alert Message Section -->
      @if (session('success'))
      <div class="alert alert-success alert-dismissible fade show text-white" role="alert">
        <span class="alert-text">{{ session('success') }}</span>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
          <span aria-hidden="true" class="text-white">&times;</span>
        </button>
      </div>
      @endif

      @yield('content')
    </div>
  </main>

  <!-- Configuration Plugin (Optional) -->
  <div class="fixed-plugin">
    <a class="fixed-plugin-button text-dark position-fixed px-3 py-2">
      <i class="fa fa-cog py-2"> </i>
    </a>
    <div class="card shadow-lg">
      <!-- Plugin content... -->
      <div class="card-header pb-0 pt-3">
        <div class="float-start">
          <h5 class="mt-3 mb-0">UI Configurator</h5>
          <p>Flat design active.</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Core JS Files -->
  <script src="{{ asset('soft-ui/assets/js/core/popper.min.js')}}"></script>
  <script src="{{ asset('soft-ui/assets/js/core/bootstrap.min.js')}}"></script>
  <script src="{{ asset('soft-ui/assets/js/plugins/perfect-scrollbar.min.js')}}"></script>
  <script src="{{ asset('soft-ui/assets/js/plugins/smooth-scrollbar.min.js')}}"></script>
  <script src="{{ asset('soft-ui/assets/js/soft-ui-dashboard.min.js?v=1.0.7')}}"></script>
  <script src="{{ asset('js/jquery-3.7.1.slim.js')}}"></script>

  <script>
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
      var options = { damping: '0.5' }
      Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }
  </script>
  @yield('script')
</body>
</html>
