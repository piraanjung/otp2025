<div class="sidenav-header">
  <i class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none"
    aria-hidden="true" id="iconSidenav"></i>
  <a class="navbar-brand m-0" href=" https://demos.creative-tim.com/soft-ui-dashboard/pages/dashboard.html "
    target="_blank">
    <img src="{{ asset('soft-ui/assets/img/logo-ct-dark.png')}}" class="navbar-brand-img h-100" alt="main_logo">
    <span class="ms-1 font-weight-bold">AIrOTrash</span>
  </a>
</div>
<hr class="horizontal dark mt-0">
<div class="collapse navbar-collapse  w-auto " id="sidenav-collapse-main">
  <ul class="navbar-nav">
    <li class="nav-item">
      <a class="nav-link  active" href="">
        <i class="fa-solid fa-house-laptop text-primary h3"></i>
        <span class="nav-link-text ms-1">Dashboard</span>
      </a>
    </li>
     <li class="nav-item">
      <a class="nav-link  active" href="{{ route('accessmenu') }}">
        <i class="fa-solid fa-house-laptop text-info h3"></i>
        <span class="nav-link-text ms-1">accessmenu</span>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link  " href="{{ route('admin.settings.settings_form') }}">
        <div
          class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
          <i class="fa-solid fa-file-csv"></i></i>
        </div>
        <span class="nav-link-text ms-1">Settings & Import</span>
      </a>
    </li>
    <li class="nav-item">
      <a data-bs-toggle="collapse" href="#meterTypes" class="nav-link" aria-controls="meterTypes" role="button"
        aria-expanded="true">
        <div
          class="icon icon-sm shadow-sm border-radius-md bg-white text-center d-flex align-items-center justify-content-center  me-2">
          <i class="fa-solid fa-traffic-light" aria-hidden="true"></i>
        </div>
        <span class="nav-link-text ms-1">Meter Types</span>
      </a>
      <div class="collapse show" id="meterTypes" style="">
        <ul class="nav ms-4 ps-3">
          <li class="nav-item ">
            <a class="nav-link " href="{{ route('admin.metertype.index') }}">
              <span class="sidenav-mini-icon"> MT </span>
              <span class="sidenav-normal"> Manage Meter Types </span>
            </a>
          </li>
          <li class="nav-item ">
            <a class="nav-link " href="{{ route('admin.meter_rates.index') }}">
              <span class="sidenav-mini-icon"> MR </span>
              <span class="sidenav-normal"> Manage Meter Rates </span>
            </a>
          </li>
        </ul>
      </div>
    </li>

    <li class="nav-item">
      <a class="nav-link  " href="{{ route('admin.pricing_types.index') }}">
        <div
          class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
          <i class="fa-solid fa-file-csv"></i></i>
        </div>
        <span class="nav-link-text ms-1">Manage Pricing Types </span>
      </a>
    </li>

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
   

  </ul>
</div>