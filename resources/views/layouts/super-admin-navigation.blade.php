<div class="sidenav-header">
    <i class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none"
        aria-hidden="true" id="iconSidenav"></i>
    <a class="navbar-brand m-0" href=" https://demos.creative-tim.com/soft-ui-dashboard/pages/dashboard.html "
        target="_blank">
        <img src="{{ asset('logo/ko_envsogo.png')}}" class="navbar-brand-img h-100" alt="main_logo">
        <span class="ms-1 font-weight-bold">Envsogo::Admin</span>
    </a>
</div>
<hr class="horizontal dark mt-0">
<div class="collapse navbar-collapse  w-auto " id="sidenav-collapse-main">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link  active" href="{{ route('admin.dashboard') }}">
                <i class="fa-solid fa-house-laptop text-primary h3"></i>
                <span class="nav-link-text ms-1">Dashboard ผู้บริหาร</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link  active" href="{{ route('accessmenu') }}">
                <i class="fa-solid fa-house-laptop text-info h3"></i>
                <span class="nav-link-text ms-1">accessmenu</span>
            </a>
        </li>
        <li class="nav-item">
            {{-- <a class="nav-link  " href="{{ route('admin.settings.settings_form') }}">
                <div
                    class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                    <i class="ni ni-circle-08 text-dark text-gradient text-lg opacity-10"></i></i>
                </div>
                <span class="nav-link-text ms-1">Settings & Import</span>
            </a> --}}
        </li>
        @if (auth()->user()->can('access tabwater') || auth()->user()->hasRole('Super Admin'))
            <li class="nav-item">
                <a data-bs-toggle="collapse" href="#meterTypes" class="nav-link" aria-controls="meterTypes" role="button"
                    aria-expanded="true">
                    <div
                        class="icon icon-sm shadow-sm border-radius-md bg-white text-center d-flex align-items-center justify-content-center  me-2">
                        <i class="fa-solid fa-traffic-light" aria-hidden="true"></i>
                    </div>
                    <span class="nav-link-text ms-1">ประเภทมิเตอร์</span>
                </a>
                <div class="collapse show" id="meterTypes" style="">
                    <ul class="nav ms-4 ps-3">
                        <li class="nav-item ">
                            <a class="nav-link " href="{{ route('admin.metertype.index') }}">
                                <span class="sidenav-mini-icon"> MT </span>
                                <span class="sidenav-normal"> จัดการข้อมูลประเภทมิเตอร์ </span>
                            </a>
                        </li>
                        <li class="nav-item ">
                            <a class="nav-link " href="{{ route('admin.meter_rates.index') }}">
                                <span class="sidenav-mini-icon"> MR </span>
                                <span class="sidenav-normal"> อัตราชำระตามประเภทมิเตอร์ </span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            <li class="nav-item">
                <a class="nav-link  " href="{{ route('admin.pricing_types.index') }}">
                    <div
                        class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-circle-08 text-dark text-gradient text-lg opacity-10"></i></i>
                    </div>
                    <span class="nav-link-text ms-1">ประเภทการชำระเงิน</span>
                </a>
            </li>

            
        @endif

        <li class="nav-item">
                <a class="nav-link  " href="{{ route('keptkayas.emission.index') }}">
                    <div
                        class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-circle-08 text-dark text-gradient text-lg opacity-10"></i></i>
                    </div>
                    <span class="nav-link-text ms-1">Emission Factor</span>
                </a>
            </li>

              <li class="nav-item">
                <a class="nav-link  " href="{{ route('admin.financial.index') }}">
                    <div
                        class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-circle-08 text-dark text-gradient text-lg opacity-10"></i></i>
                    </div>
                    <span class="nav-link-text ms-1">รายงาน สรุป ทางบัญชี</span>
                </a>
            </li>

        

        <li class="nav-item">
            @if (auth()->user()->hasRole('Super Admin'))
                <a class="nav-link  " href="{{ route('org-admins.index') }}">
                    <div
                        class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-circle-08 text-dark text-gradient text-lg opacity-10"></i></i>
                    </div>
                    <span class="nav-link-text ms-1">Org SuperAdmins </span>
                </a>
            @endif
            {{-- <a class="nav-link  " href="{{ route('admin.super_users.index') }}"> --}}
            <a class="nav-link  " href="{{ route('admin.users.index') }}">
                <div
                    class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                    <i class="ni ni-circle-08 text-dark text-gradient text-lg opacity-10"></i></i>
                </div>
                <span class="nav-link-text ms-1">สมาชิก </span>
            </a>
            <a class="nav-link  " href="{{ route('superadmin.staff.index') }}">
                <div
                    class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                    <i class="ni ni-circle-08 text-dark text-gradient text-lg opacity-10"></i></i>
                </div>
                <span class="nav-link-text ms-1">Staffs </span>
            </a>

            <a class="nav-link  " href="{{ route('admin.zone.index') }}">
                <div
                    class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                    <i class="ni ni-circle-08 text-dark text-gradient text-lg opacity-10"></i></i>
                </div>
                <span class="nav-link-text ms-1">ตั้งค่าหมู่บ้าน </span>
            </a>
        </li>


        @if (auth()->user()->hasRole('Super Admin|Admin'))

            <li class="nav-item">
                <a data-bs-toggle="collapse" href="#roles" class="nav-link" aria-controls="roles" role="button"
                    aria-expanded="true">
                    <div
                        class="icon icon-sm shadow-sm border-radius-md bg-white text-center d-flex align-items-center justify-content-center  me-2">
                        <i class="ni ni-circle-08 text-dark text-gradient text-lg opacity-10" aria-hidden="true"></i>
                    </div>
                    <span class="nav-link-text ms-1">สิทธิ์การใช้งานระบบ</span>
                </a>

                <div class="collapse show" id="roles" style="">
                    <ul class="nav ms-4 ps-3">
                        <li class="nav-item">
                            <a class="nav-link " href="{{ route('admin.roles.index') }}">
                                <span class="sidenav-mini-icon text-xs"> P </span>
                                <span class="sidenav-normal"> Roles </span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link " href="{{ route('admin.permissions.index') }}">
                                <span class="sidenav-mini-icon text-xs"> T </span>
                                <span class="sidenav-normal"> Permission </span>
                            </a>
                        </li>

                    </ul>
            </li>
        @endif
        {{--
        <li class="nav-item ">
            <a class="nav-link collapsed" data-bs-toggle="collapse" aria-expanded="false" href="#roles">
                <span class="sidenav-mini-icon"> P </span>
                <span class="sidenav-normal"><b class="caret"></b></span>
            </a>
            <div class="collapse" id="roles" style="">
                <ul class="nav nav-sm flex-column">
                    <li class="nav-item">
                        <a class="nav-link " href="{{ route('admin.roles.index') }}">
                            <span class="sidenav-mini-icon text-xs"> P </span>
                            <span class="sidenav-normal"> Roles </span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link " href="{{ route('admin.permissions.index') }}">
                            <span class="sidenav-mini-icon text-xs"> T </span>
                            <span class="sidenav-normal"> Permission </span>
                        </a>
                    </li>
                </ul>
            </div>
        </li> --}}
        @if (auth()->user()->can('access tabwater') || auth()->user()->hasRole('Super Admin'))

            <li class="nav-item">
                <a data-bs-toggle="collapse" href="#settings" class="nav-link" aria-controls="settings" role="button"
                    aria-expanded="true">
                    <div
                        class="icon icon-sm shadow-sm border-radius-md bg-white text-center d-flex align-items-center justify-content-center  me-2">
                        <i class="fa-solid fa-traffic-light" aria-hidden="true"></i>
                    </div>
                    <span class="nav-link-text ms-1">Settings</span>
                </a>
                <div class="collapse show" id="settings" style="">
                    <ul class="nav ms-4 ps-3">
                        {{-- <li class="nav-item ">
                            <a class="nav-link " href="{{ route('admin.settings.budget_years.index') }}">
                                <span class="sidenav-mini-icon"> BY </span>
                                <span class="sidenav-normal"> Manage Budget Years </span>
                            </a>
                        </li>
                        <li class="nav-item ">
                            <a class="nav-link " href="{{ route('admin.settings.tw_periods.index') }}">
                                <span class="sidenav-mini-icon"> BY </span>
                                <span class="sidenav-normal"> Manage Periods </span>
                            </a>
                        </li> --}}

                        {{-- <li class="nav-item ">
                            <a class="nav-link " href="{{ route('admin.settings.tw_meter_readings.index') }}">
                                <span class="sidenav-mini-icon"> BY </span>
                                <span class="sidenav-normal"> Meter Readings </span>
                            </a>
                        </li>
                        <li class="nav-item ">
                            <a class="nav-link " href="{{ route('admin.settings.payments.index') }}">
                                <span class="sidenav-mini-icon"> BY </span>
                                <span class="sidenav-normal">Manage Payments </span>
                            </a>
                        </li>

                        <li class="nav-item ">
                            <a class="nav-link " href="{{ route('admin.settings.user_to_tabwater') }}">
                                <span class="sidenav-mini-icon"> BY </span>
                                <span class="sidenav-normal">เพิ่ม user to Tabwater </span>
                            </a>
                        </li> --}}

                    </ul>
                </div>
            </li>
        @endif

        <li class="nav-item mt-3">
    <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">ระบบธนาคารขยะ</h6>
</li>

<li class="nav-item">
    <a data-bs-toggle="collapse" href="#bulkSalesMenu" class="nav-link" aria-controls="bulkSalesMenu" role="button" aria-expanded="false">
        <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
            <i class="fa-solid fa-truck-ramp-box text-dark"></i>
        </div>
        <span class="nav-link-text ms-1">การขายรวม (Bulk)</span>
    </a>
    <div class="collapse" id="bulkSalesMenu">
        <ul class="nav ms-4 ps-3">
            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.bulk_sales.index') }}">
                    <span class="sidenav-mini-icon"> BS </span>
                    <span class="sidenav-normal"> ประวัติการขายใหญ่ </span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.bulk_sales.create') }}">
                    <span class="sidenav-mini-icon"> NB </span>
                    <span class="sidenav-normal"> บันทึกขายขยะใหม่ </span>
                </a>
            </li>
        </ul>
    </div>
</li>

<li class="nav-item">
    <a class="nav-link" href="{{ route('admin.welfare.dashboard') }}">
        <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
            <i class="fa-solid fa-hand-holding-heart text-danger"></i>
        </div>
        <span class="nav-link-text ms-1">กองทุนสวัสดิการ</span>
    </a>
</li>
<li class="nav-item">
        <a class="nav-link" href="{{ route('admin.welfare.config') }}">

 <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
            <i class="fa-solid fa-hand-holding-heart text-danger"></i>
        </div>
        <span class="sidenav-normal"> ตั้งค่าเกณฑ์สวัสดิการ </span>
    </a>
</li>

<li class="nav-item">
    <a data-bs-toggle="collapse" href="#withdrawMenu" class="nav-link" aria-controls="withdrawMenu" role="button" aria-expanded="false">
        <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
            <i class="fa-solid fa-money-bill-transfer text-success"></i>
        </div>
        <span class="nav-link-text ms-1">จัดการถอนเงิน</span>
    </a>
    <div class="collapse" id="withdrawMenu">
        <ul class="nav ms-4 ps-3">
            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.withdraws.index') }}">
                    <span class="sidenav-mini-icon"> WL </span>
                    <span class="sidenav-normal"> รายการรอจ่ายเงิน </span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.withdraws.summary') }}">
                    <span class="sidenav-mini-icon"> WS </span>
                    <span class="sidenav-normal"> สรุปยอดเบิกรายรอบ </span>
                </a>
            </li>
        </ul>
    </div>
</li>

    </ul>
</div>
