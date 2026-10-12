@php
    $infos_count = App\Http\Controllers\FunctionsController::keptkaya_nav_infos();
@endphp
<style>
    /* ไอคอน Font Awesome ในกล่องไอคอนของเมนูหลัก: เมื่อ active กล่องเป็น gradient ให้ไอคอนเป็นสีขาวแบบเดียวกับ svg ของ Soft UI */
    .navbar-vertical .navbar-nav>.nav-item>.nav-link .icon i { color: #344767; font-size: .8rem; }
    .navbar-vertical .navbar-nav>.nav-item>.nav-link.active .icon i { color: #fff; }
    .navbar-vertical .navbar-nav .nav .nav-link .badge { margin-left: auto; }
</style>
<ul class="navbar-nav">
    <li class="nav-item">
        <a class="nav-link active mb-2 bg-warning" href="{{ route('accessmenu') }}">
            <div class="icon icon-shape icon-sm bg-white shadow text-center border-radius-md me-2 d-flex align-items-center justify-content-center">
                <i class="fas fa-th-large"></i>
            </div>
            <span class="nav-link-text ms-1">กลับไปหน้าเมนูหลัก</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link @yield('nav-dashboard')" href="{{ route('keptkayas.dashboard') }}">
            <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                <i class="fas fa-chart-pie"></i>
            </div>
            <span class="nav-link-text ms-1">Dashboard</span>
        </a>
    </li>

    <li class="nav-item mt-3">
        <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">ธนาคารขยะ</h6>
    </li>

    <li class="nav-item">
        <a data-bs-toggle="collapse" href="#kpBankMenu" class="nav-link collapsed" aria-controls="kpBankMenu"
            role="button" aria-expanded="false">
            <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                <i class="fas fa-recycle"></i>
            </div>
            <span class="nav-link-text ms-1">ธนาคารขยะรีไซเคิล</span>
        </a>
        <div class="collapse" id="kpBankMenu">
            <ul class="nav ms-4 ps-3">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('keptkayas.purchase.select_route') }}">
                        <span class="sidenav-mini-icon"> B </span>
                        <span class="sidenav-normal">รับซื้อขยะรีไซเคิล</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('keptkayas.recycle-bank.members') }}">
                        <span class="sidenav-mini-icon"> M </span>
                        <span class="sidenav-normal">รายชื่อสมาชิกธนาคารขยะ</span>
                    </a>
                </li>
            </ul>
        </div>
    </li>

    <li class="nav-item">
        <a data-bs-toggle="collapse" href="#kpSellMenu" class="nav-link collapsed" aria-controls="kpSellMenu"
            role="button" aria-expanded="false">
            <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                <i class="fas fa-truck"></i>
            </div>
            <span class="nav-link-text ms-1">ขายให้ร้านรับซื้อขยะ</span>
        </a>
        <div class="collapse" id="kpSellMenu">
            <ul class="nav ms-4 ps-3">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('keptkayas.sell.form') }}">
                        <span class="sidenav-mini-icon"> S </span>
                        <span class="sidenav-normal">บันทึกการขายขยะรีไซเคิล</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('keptkayas.sell.history') }}">
                        <span class="sidenav-mini-icon"> H </span>
                        <span class="sidenav-normal">ประวัติการขายขยะรีไซเคิล</span>
                    </a>
                </li>
            </ul>
        </div>
    </li>

    <li class="nav-item">
        <a data-bs-toggle="collapse" href="#kpWithdrawMenu" class="nav-link collapsed" aria-controls="kpWithdrawMenu"
            role="button" aria-expanded="false">
            <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                <i class="fas fa-wallet"></i>
            </div>
            <span class="nav-link-text ms-1">ถอนเงินธนาคารขยะ</span>
        </a>
        <div class="collapse" id="kpWithdrawMenu">
            <ul class="nav ms-4 ps-3">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('keptkayas.withdraw.create') }}">
                        <span class="sidenav-mini-icon"> W </span>
                        <span class="sidenav-normal">ถอนเงิน</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('keptkayas.batches.index', 'current_batches') }}">
                        <span class="sidenav-mini-icon"> P </span>
                        <span class="sidenav-normal">รายการรอจ่ายเงิน</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('keptkayas.batches.index', 'histoty_batches') }}">
                        <span class="sidenav-mini-icon"> H </span>
                        <span class="sidenav-normal">ประวัติการเบิกถอนเงิน</span>
                    </a>
                </li>
            </ul>
        </div>
    </li>

    <li class="nav-item">
        <a data-bs-toggle="collapse" href="#kpWelfareMenu" class="nav-link collapsed" aria-controls="kpWelfareMenu"
            role="button" aria-expanded="false">
            <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                <i class="fas fa-hand-holding-heart"></i>
            </div>
            <span class="nav-link-text ms-1">กองทุนสวัสดิการฌาปนกิจ</span>
        </a>
        <div class="collapse" id="kpWelfareMenu">
            <ul class="nav ms-4 ps-3">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.welfare.dashboard') }}">
                        <span class="sidenav-mini-icon"> D </span>
                        <span class="sidenav-normal">ภาพรวมกองทุน</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.welfare.config') }}">
                        <span class="sidenav-mini-icon"> C </span>
                        <span class="sidenav-normal">ตั้งค่ากองทุน</span>
                    </a>
                </li>
            </ul>
        </div>
    </li>

    <li class="nav-item mt-3">
        <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">ตั้งค่า</h6>
    </li>

    <li class="nav-item">
        <a data-bs-toggle="collapse" href="#kpSettingMenu" class="nav-link collapsed" aria-controls="kpSettingMenu"
            role="button" aria-expanded="false">
            <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                <i class="fas fa-cogs"></i>
            </div>
            <span class="nav-link-text ms-1">ตั้งค่าข้อมูล</span>
        </a>
        <div class="collapse" id="kpSettingMenu">
            <ul class="nav ms-4 ps-3">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('keptkayas.kiosk.unknown.review') }}">
                        <span class="sidenav-mini-icon"> U </span>
                        <span class="sidenav-normal">ตรวจสอบวัตถุ Unknown</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('keptkayas.tbank.items_group.index') }}">
                        <span class="sidenav-mini-icon"> G </span>
                        <span class="sidenav-normal">ประเภทขยะรีไซเคิล</span>
                        @if ($infos_count['items_group_count'] > 0)
                            <span class="badge badge-sm bg-gradient-secondary">{{ $infos_count['items_group_count'] }}</span>
                        @endif
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('keptkayas.tbank.units.index') }}">
                        <span class="sidenav-mini-icon"> U </span>
                        <span class="sidenav-normal">หน่วยนับ</span>
                        @if ($infos_count['units_count'] > 0)
                            <span class="badge badge-sm bg-gradient-secondary">{{ $infos_count['units_count'] }}</span>
                        @endif
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('keptkayas.tbank.items.index') }}">
                        <span class="sidenav-mini-icon"> I </span>
                        <span class="sidenav-normal">ขยะรีไซเคิล</span>
                        @if ($infos_count['items_count'] > 0)
                            <span class="badge badge-sm bg-gradient-secondary">{{ $infos_count['items_count'] }}</span>
                        @endif
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('keptkayas.tbank.prices.index') }}">
                        <span class="sidenav-mini-icon"> P </span>
                        <span class="sidenav-normal">ราคารับซื้อขยะรีไซเคิล</span>
                        @if ($infos_count['items_prices_count'] > 0)
                            <span class="badge badge-sm bg-gradient-secondary">{{ $infos_count['items_prices_count'] }}</span>
                        @endif
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('keptkayas.purchase-shops.index') }}">
                        <span class="sidenav-mini-icon"> S </span>
                        <span class="sidenav-normal">ร้านรับซื้อขยะ</span>
                        @if ($infos_count['shop_count'] > 0)
                            <span class="badge badge-sm bg-gradient-secondary">{{ $infos_count['shop_count'] }}</span>
                        @endif
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('keptkayas.purchase.routes.index') }}">
                        <span class="sidenav-mini-icon"> R </span>
                        <span class="sidenav-normal">เขตรับซื้อขยะ</span>
                    </a>
                </li>
            </ul>
        </div>
    </li>

    <li class="nav-item">
        <a class="nav-link @yield('nav-keptkayas.settings')" href="{{ route('keptkayas.settings.index') }}">
            <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                <i class="fas fa-sliders-h"></i>
            </div>
            <span class="nav-link-text ms-1">เงื่อนไขธนาคารขยะ</span>
        </a>
    </li>

    <li class="nav-item mt-4">
        <form method="POST" action="{{ route('logout') }}" class="mb-0">
            @csrf
            <button type="submit" class="nav-link w-100 text-start border border-danger bg-transparent">
                <div class="icon icon-shape icon-sm shadow border-radius-md bg-danger text-center me-2 d-flex align-items-center justify-content-center">
                    <i class="fas fa-sign-out-alt text-white text-sm"></i>
                </div>
                <span class="nav-link-text ms-1 text-danger">ออกจากระบบ</span>
            </button>
        </form>
    </li>
</ul>
