@php
    // เมนูที่ตรงกับ route ปัจจุบันจะได้ class `active` (แทนการใส่ active ตายตัว)
    $is = fn (string ...$routes) => request()->routeIs(...$routes);
    $isTabwaterOrSuper = auth()->user()->can('access tabwater') || auth()->user()->hasRole('Super Admin');
@endphp

<div class="sidenav-header">
    <i class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none"
        aria-hidden="true" id="iconSidenav"></i>
    <a class="navbar-brand m-0" href="{{ route('admin.dashboard') }}">
        <img src="{{ asset('logo/ko_envsogo.png')}}" class="navbar-brand-img h-100" alt="main_logo">
        <span class="ms-1 font-weight-bold">Envsogo::Admin</span>
    </a>
</div>
<hr class="horizontal dark mt-0">
<div class="collapse navbar-collapse w-auto" id="sidenav-collapse-main">
    <ul class="navbar-nav">
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
                <span class="nav-link-text ms-1">accessmenu</span>
            </a>
        </li>

        @if ($isTabwaterOrSuper)
            @php $meterOpen = $is('admin.metertype.*', 'admin.meter_rates.*'); @endphp
            <li class="nav-item">
                <a data-bs-toggle="collapse" href="#meterTypes" class="nav-link {{ $meterOpen ? 'active' : '' }}"
                    aria-controls="meterTypes" role="button" aria-expanded="{{ $meterOpen ? 'true' : 'false' }}">
                    <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-traffic-light text-dark text-sm"></i>
                    </div>
                    <span class="nav-link-text ms-1">ประเภทมิเตอร์</span>
                </a>
                <div class="collapse {{ $meterOpen ? 'show' : '' }}" id="meterTypes">
                    <ul class="nav ms-4 ps-3">
                        <li class="nav-item">
                            <a class="nav-link {{ $is('admin.metertype.*') ? 'active' : '' }}" href="{{ route('admin.metertype.index') }}">
                                <span class="sidenav-mini-icon"> MT </span>
                                <span class="sidenav-normal"> จัดการข้อมูลประเภทมิเตอร์ </span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ $is('admin.meter_rates.*') ? 'active' : '' }}" href="{{ route('admin.meter_rates.index') }}">
                                <span class="sidenav-mini-icon"> MR </span>
                                <span class="sidenav-normal"> อัตราชำระตามประเภทมิเตอร์ </span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $is('admin.pricing_types.*') ? 'active' : '' }}" href="{{ route('admin.pricing_types.index') }}">
                    <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-credit-card text-dark text-sm"></i>
                    </div>
                    <span class="nav-link-text ms-1">ประเภทการชำระเงิน</span>
                </a>
            </li>
        @endif

        <li class="nav-item">
            <a class="nav-link {{ $is('keptkayas.emission.*') ? 'active' : '' }}" href="{{ route('keptkayas.emission.index') }}">
                <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                    <i class="fas fa-leaf text-dark text-sm"></i>
                </div>
                <span class="nav-link-text ms-1">Emission Factor</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $is('admin.financial.*') ? 'active' : '' }}" href="{{ route('admin.financial.index') }}">
                <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                    <i class="fas fa-file-invoice-dollar text-dark text-sm"></i>
                </div>
                <span class="nav-link-text ms-1">รายงาน สรุป ทางบัญชี</span>
            </a>
        </li>

        @if (auth()->user()->hasRole('Super Admin'))
            <li class="nav-item">
                <a class="nav-link {{ $is('org-admins.*') ? 'active' : '' }}" href="{{ route('org-admins.index') }}">
                    <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-user-shield text-dark text-sm"></i>
                    </div>
                    <span class="nav-link-text ms-1">Org SuperAdmins</span>
                </a>
            </li>
        @endif
        <li class="nav-item">
            <a class="nav-link {{ $is('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                    <i class="fas fa-users text-dark text-sm"></i>
                </div>
                <span class="nav-link-text ms-1">สมาชิก</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $is('superadmin.staff.*', 'keptkayas.staffs.*') ? 'active' : '' }}" href="{{ route('superadmin.staff.index') }}">
                <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                    <i class="fas fa-user-tie text-dark text-sm"></i>
                </div>
                <span class="nav-link-text ms-1">Staffs</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $is('admin.zone.*') ? 'active' : '' }}" href="{{ route('admin.zone.index') }}">
                <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                    <i class="fas fa-map-marked-alt text-dark text-sm"></i>
                </div>
                <span class="nav-link-text ms-1">ตั้งค่าหมู่บ้าน</span>
            </a>
        </li>

        @if (auth()->user()->hasRole('Super Admin|Admin'))
            @php $rolesOpen = $is('admin.roles.*', 'admin.permissions.*'); @endphp
            <li class="nav-item">
                <a data-bs-toggle="collapse" href="#roles" class="nav-link {{ $rolesOpen ? 'active' : '' }}"
                    aria-controls="roles" role="button" aria-expanded="{{ $rolesOpen ? 'true' : 'false' }}">
                    <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-key text-dark text-sm"></i>
                    </div>
                    <span class="nav-link-text ms-1">สิทธิ์การใช้งานระบบ</span>
                </a>
                <div class="collapse {{ $rolesOpen ? 'show' : '' }}" id="roles">
                    <ul class="nav ms-4 ps-3">
                        <li class="nav-item">
                            <a class="nav-link {{ $is('admin.roles.*') ? 'active' : '' }}" href="{{ route('admin.roles.index') }}">
                                <span class="sidenav-mini-icon text-xs"> R </span>
                                <span class="sidenav-normal"> Roles </span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ $is('admin.permissions.*') ? 'active' : '' }}" href="{{ route('admin.permissions.index') }}">
                                <span class="sidenav-mini-icon text-xs"> P </span>
                                <span class="sidenav-normal"> Permission </span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
        @endif

        <li class="nav-item mt-3">
            <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">ระบบธนาคารขยะ</h6>
        </li>

        @php $bulkOpen = $is('admin.bulk_sales.*'); @endphp
        <li class="nav-item">
            <a data-bs-toggle="collapse" href="#bulkSalesMenu" class="nav-link {{ $bulkOpen ? 'active' : '' }}"
                aria-controls="bulkSalesMenu" role="button" aria-expanded="{{ $bulkOpen ? 'true' : 'false' }}">
                <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                    <i class="fas fa-truck-loading text-dark text-sm"></i>
                </div>
                <span class="nav-link-text ms-1">การขายรวม (Bulk)</span>
            </a>
            <div class="collapse {{ $bulkOpen ? 'show' : '' }}" id="bulkSalesMenu">
                <ul class="nav ms-4 ps-3">
                    <li class="nav-item">
                        <a class="nav-link {{ $is('admin.bulk_sales.index') ? 'active' : '' }}" href="{{ route('admin.bulk_sales.index') }}">
                            <span class="sidenav-mini-icon"> BS </span>
                            <span class="sidenav-normal"> ประวัติการขายใหญ่ </span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $is('admin.bulk_sales.create') ? 'active' : '' }}" href="{{ route('admin.bulk_sales.create') }}">
                            <span class="sidenav-mini-icon"> NB </span>
                            <span class="sidenav-normal"> บันทึกขายขยะใหม่ </span>
                        </a>
                    </li>
                </ul>
            </div>
        </li>

        <li class="nav-item">
            <a class="nav-link {{ $is('admin.welfare.dashboard') ? 'active' : '' }}" href="{{ route('admin.welfare.dashboard') }}">
                <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                    <i class="fas fa-hand-holding-heart text-danger text-sm"></i>
                </div>
                <span class="nav-link-text ms-1">กองทุนสวัสดิการ</span>
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

        @php $withdrawOpen = $is('admin.withdraws.*'); @endphp
        <li class="nav-item">
            <a data-bs-toggle="collapse" href="#withdrawMenu" class="nav-link {{ $withdrawOpen ? 'active' : '' }}"
                aria-controls="withdrawMenu" role="button" aria-expanded="{{ $withdrawOpen ? 'true' : 'false' }}">
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
