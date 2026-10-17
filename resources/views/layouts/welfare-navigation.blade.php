@php
    // เมนูที่ตรงกับ route ปัจจุบันจะได้ class `active` (แทนการใส่ active ตายตัว)
    $is = fn (string ...$routes) => request()->routeIs(...$routes);$isTabwaterOrSuper = auth()->user()->can('access tabwater') || auth()->user()->hasRole('Super Admin');
@endphp

<div class="sidenav-header">
    <i class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
    <a class="navbar-brand m-0" href="{{ route('admin.dashboard') }}">
        <img src="{{ asset('logo/ko_envsogo.png')}}" class="navbar-brand-img h-100" alt="main_logo">
        <span class="ms-1 font-weight-bold">Envsogo::กองทุนฌาปณกิจสงเคราะห์</span>
    </a>
</div>

<hr class="horizontal dark mt-0">

<div class="collapse navbar-collapse w-auto" id="sidenav-collapse-main">
    <ul class="navbar-nav">

        {{-- ================================================= --}}
        {{-- 1. เมนูหลักและแดชบอร์ด (General)                     --}}
        {{-- ================================================= --}}
        <li class="nav-item">
            <a class="nav-link {{ $is('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                    <i class="fas fa-tachometer-alt text-dark text-sm"></i>
                </div>
                <span class="nav-link-text ms-1">Dashboard ผู้บริหาร</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $is('accessmenu') ? 'active' : '' }}" href="{{ route('accessmenu') }}">
                <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                    <i class="fas fa-th-large text-dark text-sm"></i>
                </div>
                <span class="nav-link-text ms-1">หน้าเมนูหลัก</span>
            </a>
        </li>

        {{-- ================================================= --}}
        {{-- 2. การบริหารจัดการสมาชิกและระบบกลาง (Central Administration) --}}
        {{-- ================================================= --}}
        

            <li class="nav-item">
                <a class="nav-link {{ $is('admin.welfare.dashboard') ? 'active' : '' }}" href="{{ route('admin.welfare.dashboard') }}">
                    <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-hand-holding-heart text-danger text-sm"></i>
                    </div>
                    <span class="nav-link-text ms-1">กองทุนสวัสดิการ</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $is('admin.welfare.member.create') ? 'active' : '' }}" href="{{ route('admin.welfare.member.create') }}">
                    <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-sliders-h text-danger text-sm"></i>
                    </div>
                    <span class="nav-link-text ms-1">สำหรับสมาชิก(create)</span>
                </a>
            </li>
             <li class="nav-item">
                <a class="nav-link {{ $is('admin.welfare.member.tracking') ? 'active' : '' }}" href="{{ route('admin.welfare.member.tracking') }}">
                    <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-sliders-h text-danger text-sm"></i>
                    </div>
                    <span class="nav-link-text ms-1">สำหรับสมาชิก(tracking)</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ $is('admin.welfare.collections.index') ? 'active' : '' }}" href="{{ route('admin.welfare.collections.index') }}">
                    <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-sliders-h text-danger text-sm"></i>
                    </div>
                    <span class="nav-link-text ms-1">จัดเก็บค่าฌาปณกิจ</span>
                </a>
            </li>
            
            

            <li class="nav-item">
                <a class="nav-link {{ $is('admin.welfare.config') ? 'active' : '' }}" href="{{ route('admin.welfare.config') }}">
                    <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-sliders-h text-danger text-sm"></i>
                    </div>
                    <span class="nav-link-text ms-1">ตั้งค่าเกณฑ์สวัสดิการ</span>
                </a>
            </li>


             <li class="nav-item">
                <a class="nav-link {{ $is('admin.welfare.members.index') ? 'active' : '' }}" href="{{ route('admin.welfare.members.index') }}">
                    <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-sliders-h text-danger text-sm"></i>
                    </div>
                    <span class="nav-link-text ms-1">สมาชิกกองทุน</span>
                </a>
            </li>


            @php
                $withdrawOpen =$is('admin.withdraws.*');
            @endphp
            <li class="nav-item">
                <a data-bs-toggle="collapse" href="#withdrawMenu" class="nav-link {{ $withdrawOpen ? 'active' : '' }}" aria-controls="withdrawMenu" role="button" aria-expanded="{{ $withdrawOpen ? 'true' : 'false' }}">
                    <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-exchange-alt text-success text-sm"></i>
                    </div>
                    <span class="nav-link-text ms-1">จัดการถอนเงิน</span>
                </a>
                <div class="collapse {{ $withdrawOpen ? 'show' : '' }}" id="withdrawMenu">
                    <ul class="nav ms-4 ps-3">
                        <li class="nav-item">
                            <a class="nav-link {{ $is('admin.withdraws.index') ? 'active' : '' }}" href="{{ route('admin.withdraws.index') }}">
                                <span class="sidenav-mini-icon"> WL </span>
                                <span class="sidenav-normal"> รายการรอจ่ายเงิน </span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ $is('admin.withdraws.summary') ? 'active' : '' }}" href="{{ route('admin.withdraws.summary') }}">
                                <span class="sidenav-mini-icon"> WS </span>
                                <span class="sidenav-normal"> สรุปยอดเบิกรายรอบ </span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>


        
      
    </ul>
</div>