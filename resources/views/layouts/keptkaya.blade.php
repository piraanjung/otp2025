@php
    // view ของ keptkaya ตั้งหัวข้อหน้าด้วย section ต่างกัน (page-topic / nav-current / nav-header / ...)
    // รวมเป็นลำดับ fallback เดียวที่นี่ เพื่อให้ทุกหน้ามีหัวข้อแสดงและมี <title>
    $clean = fn (string $v) => trim(preg_replace('/\s+/', ' ', strip_tags($v)));
    $pageTitle = '';
    foreach (['page-topic', 'nav-topic', 'mainheader', 'topic', 'title_page', 'nav-current', 'nav-main', 'nav-header'] as $s) {
        $pageTitle = $clean($__env->yieldContent($s));
        if ($pageTitle !== '') {
            break;
        }
    }
    $menuTitle = \App\Support\PageTitle::for();
    if ($pageTitle === '') {
        $pageTitle = (string) $menuTitle;
    }
    $crumbParent = $clean($__env->yieldContent('nav-header')) ?: 'ธนาคารขยะ';
    $crumbCurrent = $clean($__env->yieldContent('nav-current')) ?: $clean($__env->yieldContent('nav-main')) ?: $pageTitle;
    $tabTitle = $menuTitle ?: $pageTitle;

    $me = Auth::user();
    $orgInfos = $me ? App\Models\Admin\Organization::getOrgName($me->org_id_fk) : [];
    $orgLogo = $orgInfos['org_logo_img'] ?? 'ko_envsogo.png';
    $meName = trim(($me->firstname ?? '') . ' ' . ($me->lastname ?? '')) ?: ($me->username ?? '');
@endphp
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="utf-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('logo/ko_envsogo.png') }}">
    <link rel="icon" type="image/png" href="{{ asset('logo/ko_envsogo.png') }}">
    <title>{{ $tabTitle !== '' ? $tabTitle . ' | ' : '' }}EnvSoGo::Kept Kaya</title>

    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;600;700&display=swap" rel="stylesheet" />
    <link href="{{ asset('soft-ui/assets/css/nucleo-icons.css') }}" rel="stylesheet" />
    <link href="{{ asset('soft-ui/assets/css/nucleo-svg.css') }}" rel="stylesheet" />
    <!-- Font Awesome 5 (soft-ui-dashboard.css อ้าง 'Font Awesome 5 Free'; ใช้ class fas/far/fab) -->
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/fontawesome-free/css/all.min.css') }}" />
    <link id="pagestyle" href="{{ asset('soft-ui/assets/css/soft-ui-dashboard.css?v=1.0.7') }}" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <style>
        /* Open Sans ไม่มีอักษรไทย ให้ใช้ Sarabun เป็นตัวสำรอง */
        body, .btn, .form-control, .form-select, .dropdown-menu, .table {
            font-family: "Open Sans", "Sarabun", sans-serif;
        }
        /* sidebar ยาว: ให้เลื่อนในตัวเอง */
        .navbar-vertical.navbar-expand-xs .navbar-collapse { max-height: calc(100vh - 6rem); overflow-y: auto; }
        .navbar-vertical .navbar-nav .nav-item .collapse .nav .nav-item .nav-link,
        .navbar-vertical .navbar-nav .nav-item .collapsing .nav .nav-item .nav-link { color: #344767; font-size: .875rem; }
        /* DataTables */
        .dataTables_length, .dataTables_filter { display: inline-flex; }
        .dataTables_filter { margin-left: 1rem; }
        /* select2 ให้สูงเท่า .form-control ของ Soft UI */
        .select2-container .select2-selection--single { height: calc(1.4rem + 1rem + 2px); border: 1px solid #d2d6da; border-radius: .5rem; }
        .select2-container--default .select2-selection--single .select2-selection__rendered { line-height: calc(1.4rem + 1rem); padding-left: .75rem; }
        .select2-container--default .select2-selection--single .select2-selection__arrow { height: 100%; }
    </style>

    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/plug-ins/1.13.6/pagination/select.js"></script>
    @yield('style')
</head>

<body class="g-sidenav-show bg-gray-100">
    <aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3"
        id="sidenav-main">
        <div class="sidenav-header">
            <i class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none"
                aria-hidden="true" id="iconSidenav"></i>
            <a class="navbar-brand m-0" href="{{ route('keptkayas.dashboard') }}">
                <img src="{{ asset('logo/ko_envsogo.png') }}" class="navbar-brand-img h-100" alt="main_logo">
                <span class="ms-1 font-weight-bold">EnvSoGo::Kept Kaya</span>
            </a>
        </div>
        <hr class="horizontal dark mt-0">
        <div class="collapse navbar-collapse w-auto" id="sidenav-collapse-main">
            @include('layouts.keptkaya_navigation')
        </div>
    </aside>

    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <!-- Navbar -->
        <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur"
            navbar-scroll="true">
            <div class="container-fluid py-1 px-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
                        <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark"
                                href="{{ $__env->yieldContent('route-header') ?: 'javascript:;' }}">{{ $crumbParent }}</a></li>
                        <li class="breadcrumb-item text-sm text-dark active" aria-current="page">{{ $crumbCurrent }}</li>
                    </ol>
                    <h6 class="font-weight-bolder mb-0">{{ $pageTitle }}</h6>
                </nav>

                <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
                    <ul class="navbar-nav ms-auto justify-content-end">
                        <li class="nav-item dropdown d-flex align-items-center">
                            <a href="javascript:;" class="nav-link text-body font-weight-bold px-0" id="userMenuButton"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <img src="{{ asset('logo/' . $orgLogo) }}" class="avatar avatar-xs me-2" alt="org_logo">
                                <span class="d-sm-inline d-none">{{ $meName }}</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end px-2 py-3" aria-labelledby="userMenuButton">
                                <li>
                                    <a class="dropdown-item border-radius-md" href="{{ route('accessmenu') }}">
                                        <i class="fas fa-th-large me-2"></i> กลับไปหน้าเมนูหลัก
                                    </a>
                                </li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item border-radius-md">
                                            <i class="fas fa-sign-out-alt me-2"></i> ออกจากระบบ
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
            {{-- success แต่ละหน้าแสดงเอง (layout เดิมแสดงเฉพาะ error) --}}
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show text-white" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <span class="alert-text">{{ session('error') }}</span>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <!-- soft-ui-dashboard.js อ้าง [data-class] ของ configurator ตอน resize; ใส่ตัวแทนไว้เพื่อไม่ให้เกิด TypeError -->
    <span data-class="bg-white" class="d-none" aria-hidden="true"></span>

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ asset('soft-ui/assets/js/core/popper.min.js') }}"></script>
    <script src="{{ asset('soft-ui/assets/js/core/bootstrap.min.js') }}"></script>
    <script src="{{ asset('soft-ui/assets/js/plugins/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('soft-ui/assets/js/plugins/smooth-scrollbar.min.js') }}"></script>
    <script src="{{ asset('soft-ui/assets/js/soft-ui-dashboard.min.js?v=1.0.7') }}"></script>

    @include('layouts.partials.sidenav-active')
    @yield('script')

    <script>
        $(function () {
            $('.js-example-basic-single').select2();
        });
    </script>

</body>

</html>
