@extends('layouts.foodwaste')
@section('nav-dashboard')
  active
@endsection
@section('nav-header')
  Dashboard
@endsection
@section('style')
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
  <script
    src="https://cdn.jsdelivr.net/npm/chartjs-plugin-annotation@3.0.1/dist/chartjs-plugin-annotation.min.js"></script>

@endsection
@section('content')
  <div class="container mt-4">
    <h2 class="mb-0">Food Waste Bins Dashboard</h2>
    {{-- <p class="mb-4 ms-1">This is a simple dashboard with some statistics and charts.</p> --}}

    @php
      $refs = $globalTemperatureData['references'] ?? [];
    @endphp
    {{-- <div class="alert alert-info">
      <strong>อุณหภูมิ:</strong> {{ $refs['temp_min'] }}°C - {{ $refs['temp_max'] }}°C |
      <strong>ความชื้น:</strong> {{ $refs['hum_min'] }}% - {{ $refs['hum_max'] }}% |
      <strong>มีเทน:</strong> {{ $refs['methane_min'] }} PPM - {{ $refs['methane_max'] }} PPM |
      <strong>น้ำหนัก:</strong> {{ $refs['weight_min'] }} Kg - {{ $refs['weight_max'] }} Kg
    </div> --}}

    <div class="row mb-3">
            <div class="col-lg-3 col-sm-6">
              <div class="card  mb-4">
                <div class="card-body p-3">
                  <div class="row">
                    <div class="col-8">
                      <div class="numbers">
                        <p class="text-sm mb-0 text-capitalize font-weight-bold">สมาชิก</p>
                        <h5 class="font-weight-bolder mb-0">
                          530
                          <span class="text-success text-sm font-weight-bolder">ราย</span>
                        </h5>
                      </div>
                    </div>
                    <div class="col-4 text-end">
                      <div class="icon icon-shape bg-primary shadow text-center border-radius-md">
                        <i class="ni ni-money-coins text-lg opacity-10" aria-hidden="true"></i>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-3 col-sm-6">
              <div class="card ">
                <div class="card-body p-3">
                  <div class="row">
                    <div class="col-8">
                      <div class="numbers">
                        <p class="text-sm mb-0 text-capitalize font-weight-bold">จำนวนถัง</p>
                        <h5 class="font-weight-bolder mb-0">
                          600
                          <span class="text-success text-sm font-weight-bolder">ถัง</span>
                        </h5>
                      </div>
                    </div>
                    <div class="col-4 text-end">
                      <div class="icon icon-shape bg-primary shadow text-center border-radius-md">
                        <i class="ni ni-world text-lg opacity-10" aria-hidden="true"></i>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="col-lg-3 col-sm-6">
              <div class="card  mb-4">
                <div class="card-body p-3">
                  <div class="row">
                    <div class="col-8">
                      <div class="numbers">
                        <p class="text-sm mb-0 text-capitalize font-weight-bold">ก๊าซมีเทนรวม</p>
                        <h5 class="font-weight-bolder mb-0">
                          10
                          <span class="text-danger text-sm font-weight-bolder">หน่วย</span>
                        </h5>
                      </div>
                    </div>
                    <div class="col-4 text-end">
                      <div class="icon icon-shape bg-primary shadow text-center border-radius-md">
                        <i class="ni ni-paper-diploma text-lg opacity-10" aria-hidden="true"></i>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-3 col-sm-6">
              <div class="card ">
                <div class="card-body p-3">
                  <div class="row">
                    <div class="col-8">
                      <div class="numbers">
                        <p class="text-sm mb-0 text-capitalize font-weight-bold">น้ำหนักรวม</p>
                        <h5 class="font-weight-bolder mb-0">
                          3,430
                          <span class="text-success text-sm font-weight-bolder">กิโลกรัม</span>
                        </h5>
                      </div>
                    </div>
                    <div class="col-4 text-end">
                      <div class="icon icon-shape bg-primary shadow text-center border-radius-md">
                        <i class="ni ni-cart text-lg opacity-10" aria-hidden="true"></i>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

    <div class="row">

      {{-- ****************** 1. กราฟอุณหภูมิ (Temperature Chart) ****************** --}}
      <div class="col-md-6 mb-5">
        <div class="card shadow h-100 border-danger">
          <div class="card-header bg-danger text-white">
            <h4 class="card-title mb-0">🌡️ อุณหภูมิรวม (Temperature)</h4>
          </div>
          <div class="card-body">
            <div class="chart-container" style="height:350px;">
              <canvas id="chart-global-temperature"></canvas>
            </div>
          </div>
        </div>
      </div>

      {{-- ****************** 2. กราฟความชื้น (Humidity Chart) ****************** --}}
      <div class="col-md-6 mb-5">
        <div class="card shadow h-100 border-primary">
          <div class="card-header bg-primary text-white">
            <h4 class="card-title mb-0">💧 ความชื้นรวม (Humidity)</h4>
          </div>
          <div class="card-body">
            <div class="chart-container" style="height:350px;">
              <canvas id="chart-global-humidity"></canvas>
            </div>
          </div>
        </div>
      </div>

      {{-- ****************** 3. กราฟมีเทน (Methane Gas Chart) ****************** --}}
      <div class="col-md-6 mb-5">
        <div class="card shadow h-100 border-warning">
          <div class="card-header bg-warning text-dark">
            <h4 class="card-title mb-0">💨 ก๊าซมีเทนรวม (Methane Gas)</h4>
          </div>
          <div class="card-body">
            <div class="chart-container" style="height:350px;">
              <canvas id="chart-global-methane"></canvas>
            </div>
          </div>
        </div>
      </div>

      {{-- ****************** 4. กราฟน้ำหนัก (Weight Chart) ****************** --}}
      <div class="col-md-6 mb-5">
        <div class="card shadow h-100 border-success">
          <div class="card-header bg-success text-white">
            <h4 class="card-title mb-0">⚖️ น้ำหนักรวม (Weight)</h4>
          </div>
          <div class="card-body">
            <div class="chart-container" style="height:350px;">
              <canvas id="chart-global-weight"></canvas>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>




  <div class="app-content"> <!--begin::Container-->
    <div class="container-fluid py-4 m">
      <div class="row">

      
        <div class="col-lg-7 mt-4">
          
          <div class="mt-4">
            <div class="card ">
              <div class="card-header pb-0 p-3">
                <div class="d-flex justify-content-between">
                  <h6 class="mb-2">Sales by Country</h6>
                </div>
              </div>
              <div class="table-responsive">
                <table class="table align-items-center mb-0">
                  <thead>
                    <tr>
                      <th class="text-left text-sm p-0 pb-2 ps-3">Country</th>
                      <th class="text-left text-sm p-0 pb-2 ps-1">Sales</th>
                      <th class="text-left text-sm p-0 pb-2 ps-1">Value</th>
                      <th class="text-left text-sm p-0 pb-2 ps-1">Bounce</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td class="w-30">
                        <div class="d-flex px-2 py-1 align-items-center">
                          <div>
                            <img src="../../assets/img/icons/flags/US.png" alt="Country flag">
                          </div>
                          <div class="ms-2">
                            <h6 class="text-sm mb-0">United States</h6>
                          </div>
                        </div>
                      </td>
                      <td>
                        <div class="text-left">
                          <h6 class="text-sm mb-0">2500</h6>
                        </div>
                      </td>
                      <td>
                        <div class="text-left">
                          <h6 class="text-sm mb-0">$230,900</h6>
                        </div>
                      </td>
                      <td class="align-middle text-sm">
                        <div class="col text-left">
                          <h6 class="text-sm mb-0">29.9%</h6>
                        </div>
                      </td>
                    </tr>
                    <tr>
                      <td class="w-30">
                        <div class="d-flex px-2 py-1 align-items-center">
                          <div>
                            <img src="../../assets/img/icons/flags/DE.png" alt="Country flag">
                          </div>
                          <div class="ms-2">
                            <h6 class="text-sm mb-0">Germany</h6>
                          </div>
                        </div>
                      </td>
                      <td>
                        <div class="text-left">
                          <h6 class="text-sm mb-0">3.900</h6>
                        </div>
                      </td>
                      <td>
                        <div class="text-left">
                          <h6 class="text-sm mb-0">$440,000</h6>
                        </div>
                      </td>
                      <td class="align-middle text-sm">
                        <div class="col text-left">
                          <h6 class="text-sm mb-0">40.22%</h6>
                        </div>
                      </td>
                    </tr>
                    <tr>
                      <td class="w-30">
                        <div class="d-flex px-2 py-1 align-items-center">
                          <div>
                            <img src="../../assets/img/icons/flags/GB.png" alt="Country flag">
                          </div>
                          <div class="ms-2">
                            <h6 class="text-sm mb-0">Great Britain</h6>
                          </div>
                        </div>
                      </td>
                      <td>
                        <div class="text-left">
                          <h6 class="text-sm mb-0">1.400</h6>
                        </div>
                      </td>
                      <td>
                        <div class="text-left">
                          <h6 class="text-sm mb-0">$190,700</h6>
                        </div>
                      </td>
                      <td class="align-middle text-sm">
                        <div class="col text-left">
                          <h6 class="text-sm mb-0">23.44%</h6>
                        </div>
                      </td>
                    </tr>
                    <tr>
                      <td class="w-30">
                        <div class="d-flex px-2 py-1 align-items-center">
                          <div>
                            <img src="../../assets/img/icons/flags/BR.png" alt="Country flag">
                          </div>
                          <div class="ms-2">
                            <h6 class="text-sm mb-0">Brasil</h6>
                          </div>
                        </div>
                      </td>
                      <td>
                        <div class="text-left">
                          <h6 class="text-sm mb-0">562</h6>
                        </div>
                      </td>
                      <td>
                        <div class="text-left">
                          <h6 class="text-sm mb-0">$143,960</h6>
                        </div>
                      </td>
                      <td class="align-middle text-sm">
                        <div class="col text-left">
                          <h6 class="text-sm mb-0">32.14%</h6>
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-5 col-sm-6 mt-sm-0 mt-4">
          <div class="card">
            <div class="card-body p-3 position-relative overflow-hidden min-height-500">
              <div class="row">
                <div class="col-10">
                  <div class="numbers">
                    <h3 class="text-dark font-weight-bold mb-0">Global Sales</h3>
                    <p class="mb-0 mb-4">Check the global stats of the company</p>
                    <h5 class="font-weight-bolder mb-0">
                      $103,430
                    </h5>
                    <p class="mb-2">Generated sales</p>
                    <h5 class="font-weight-bolder mb-0">
                      24,500
                    </h5>
                    <p class="mb-0">Reached Users</p>
                  </div>
                </div>
                <div class="col-2 text-end">
                  <div class="icon icon-shape bg-primary shadow text-center border-radius-md">
                    <i class="ni ni-square-pin text-lg opacity-10" aria-hidden="true"></i>
                  </div>
                </div>
              </div>
              <div id="globe" class="position-absolute end-0 bottom-n2 mt-sm-3 peekaboo">
                <canvas width="451" height="506" class="w-lg-100 h-lg-100 w-75 h-75 me-lg-0 me-n8 mt-lg-5"
                  style="width: 451.828px; height: 506.922px;"></canvas>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row mt-4">
        <div class="col-lg-5 mb-lg-0 mb-4">
          <div class="card z-index-2">
            <div class="card-body p-2">
              <div class="bg-dark border-radius-md py-3 pe-1 mb-3">
                <div class="chart">
                  <canvas id="chart-bars" class="chart-canvas" height="340" width="1310"
                    style="display: block; box-sizing: border-box; height: 170px; width: 655px;"></canvas>
                </div>
              </div>
              <h6 class="ms-2 mt-4 mb-0"> Active Users </h6>
              <p class="text-sm ms-2"> (<span class="font-weight-bolder">+23%</span>) than last week </p>
              <div class="container border-radius-lg">
                <div class="row">
                  <div class="col-3 py-3 ps-0">
                    <div class="d-flex mb-2">
                      <div
                        class="icon icon-shape icon-xxs shadow border-radius-sm bg-primary text-center me-2 d-flex align-items-center justify-content-center">
                        <svg width="10px" height="10px" viewBox="0 0 40 44" version="1.1"
                          xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                          <title>document</title>
                          <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                            <g transform="translate(-1870.000000, -591.000000)" fill="#FFFFFF" fill-rule="nonzero">
                              <g transform="translate(1716.000000, 291.000000)">
                                <g transform="translate(154.000000, 300.000000)">
                                  <path class="color-background"
                                    d="M40,40 L36.3636364,40 L36.3636364,3.63636364 L5.45454545,3.63636364 L5.45454545,0 L38.1818182,0 C39.1854545,0 40,0.814545455 40,1.81818182 L40,40 Z"
                                    opacity="0.603585379"></path>
                                  <path class="color-background"
                                    d="M30.9090909,7.27272727 L1.81818182,7.27272727 C0.814545455,7.27272727 0,8.08727273 0,9.09090909 L0,41.8181818 C0,42.8218182 0.814545455,43.6363636 1.81818182,43.6363636 L30.9090909,43.6363636 C31.9127273,43.6363636 32.7272727,42.8218182 32.7272727,41.8181818 L32.7272727,9.09090909 C32.7272727,8.08727273 31.9127273,7.27272727 30.9090909,7.27272727 Z M18.1818182,34.5454545 L7.27272727,34.5454545 L7.27272727,30.9090909 L18.1818182,30.9090909 L18.1818182,34.5454545 Z M25.4545455,27.2727273 L7.27272727,27.2727273 L7.27272727,23.6363636 L25.4545455,23.6363636 L25.4545455,27.2727273 Z M25.4545455,20 L7.27272727,20 L7.27272727,16.3636364 L25.4545455,16.3636364 L25.4545455,20 Z">
                                  </path>
                                </g>
                              </g>
                            </g>
                          </g>
                        </svg>
                      </div>
                      <p class="text-xs mt-1 mb-0 font-weight-bold">Users</p>
                    </div>
                    <h4 class="font-weight-bolder">36K</h4>
                    <div class="progress w-75">
                      <div class="progress-bar bg-dark w-60" role="progressbar" aria-valuenow="60" aria-valuemin="0"
                        aria-valuemax="100"></div>
                    </div>
                  </div>
                  <div class="col-3 py-3 ps-0">
                    <div class="d-flex mb-2">
                      <div
                        class="icon icon-shape icon-xxs shadow border-radius-sm bg-gradient-info text-center me-2 d-flex align-items-center justify-content-center">
                        <svg width="10px" height="10px" viewBox="0 0 40 40" version="1.1"
                          xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                          <title>spaceship</title>
                          <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                            <g transform="translate(-1720.000000, -592.000000)" fill="#FFFFFF" fill-rule="nonzero">
                              <g transform="translate(1716.000000, 291.000000)">
                                <g transform="translate(4.000000, 301.000000)">
                                  <path class="color-background"
                                    d="M39.3,0.706666667 C38.9660984,0.370464027 38.5048767,0.192278529 38.0316667,0.216666667 C14.6516667,1.43666667 6.015,22.2633333 5.93166667,22.4733333 C5.68236407,23.0926189 5.82664679,23.8009159 6.29833333,24.2733333 L15.7266667,33.7016667 C16.2013871,34.1756798 16.9140329,34.3188658 17.535,34.065 C17.7433333,33.98 38.4583333,25.2466667 39.7816667,1.97666667 C39.8087196,1.50414529 39.6335979,1.04240574 39.3,0.706666667 Z M25.69,19.0233333 C24.7367525,19.9768687 23.3029475,20.2622391 22.0572426,19.7463614 C20.8115377,19.2304837 19.9992882,18.0149658 19.9992882,16.6666667 C19.9992882,15.3183676 20.8115377,14.1028496 22.0572426,13.5869719 C23.3029475,13.0710943 24.7367525,13.3564646 25.69,14.31 C26.9912731,15.6116662 26.9912731,17.7216672 25.69,19.0233333 L25.69,19.0233333 Z">
                                  </path>
                                  <path class="color-background"
                                    d="M1.855,31.4066667 C3.05106558,30.2024182 4.79973884,29.7296005 6.43969145,30.1670277 C8.07964407,30.6044549 9.36054508,31.8853559 9.7979723,33.5253085 C10.2353995,35.1652612 9.76258177,36.9139344 8.55833333,38.11 C6.70666667,39.9616667 0,40 0,40 C0,40 0,33.2566667 1.855,31.4066667 Z">
                                  </path>
                                  <path class="color-background"
                                    d="M17.2616667,3.90166667 C12.4943643,3.07192755 7.62174065,4.61673894 4.20333333,8.04166667 C3.31200265,8.94126033 2.53706177,9.94913142 1.89666667,11.0416667 C1.5109569,11.6966059 1.61721591,12.5295394 2.155,13.0666667 L5.47,16.3833333 C8.55036617,11.4946947 12.5559074,7.25476565 17.2616667,3.90166667 L17.2616667,3.90166667 Z"
                                    opacity="0.598539807"></path>
                                  <path class="color-background"
                                    d="M36.0983333,22.7383333 C36.9280725,27.5056357 35.3832611,32.3782594 31.9583333,35.7966667 C31.0587397,36.6879974 30.0508686,37.4629382 28.9583333,38.1033333 C28.3033941,38.4890431 27.4704606,38.3827841 26.9333333,37.845 L23.6166667,34.53 C28.5053053,31.4496338 32.7452344,27.4440926 36.0983333,22.7383333 L36.0983333,22.7383333 Z"
                                    opacity="0.598539807"></path>
                                </g>
                              </g>
                            </g>
                          </g>
                        </svg>
                      </div>
                      <p class="text-xs mt-1 mb-0 font-weight-bold">Clicks</p>
                    </div>
                    <h4 class="font-weight-bolder">2m</h4>
                    <div class="progress w-75">
                      <div class="progress-bar bg-dark w-90" role="progressbar" aria-valuenow="90" aria-valuemin="0"
                        aria-valuemax="100"></div>
                    </div>
                  </div>
                  <div class="col-3 py-3 ps-0">
                    <div class="d-flex mb-2">
                      <div
                        class="icon icon-shape icon-xxs shadow border-radius-sm bg-gradient-warning text-center me-2 d-flex align-items-center justify-content-center">
                        <svg width="10px" height="10px" viewBox="0 0 43 36" version="1.1"
                          xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                          <title>credit-card</title>
                          <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                            <g transform="translate(-2169.000000, -745.000000)" fill="#FFFFFF" fill-rule="nonzero">
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
                      <p class="text-xs mt-1 mb-0 font-weight-bold">Sales</p>
                    </div>
                    <h4 class="font-weight-bolder">435$</h4>
                    <div class="progress w-75">
                      <div class="progress-bar bg-dark w-30" role="progressbar" aria-valuenow="30" aria-valuemin="0"
                        aria-valuemax="100"></div>
                    </div>
                  </div>
                  <div class="col-3 py-3 ps-0">
                    <div class="d-flex mb-2">
                      <div
                        class="icon icon-shape icon-xxs shadow border-radius-sm bg-gradient-danger text-center me-2 d-flex align-items-center justify-content-center">
                        <svg width="10px" height="10px" viewBox="0 0 40 40" version="1.1"
                          xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                          <title>settings</title>
                          <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                            <g transform="translate(-2020.000000, -442.000000)" fill="#FFFFFF" fill-rule="nonzero">
                              <g transform="translate(1716.000000, 291.000000)">
                                <g transform="translate(304.000000, 151.000000)">
                                  <polygon class="color-background" opacity="0.596981957"
                                    points="18.0883333 15.7316667 11.1783333 8.82166667 13.3333333 6.66666667 6.66666667 0 0 6.66666667 6.66666667 13.3333333 8.82166667 11.1783333 15.315 17.6716667">
                                  </polygon>
                                  <path class="color-background"
                                    d="M31.5666667,23.2333333 C31.0516667,23.2933333 30.53,23.3333333 30,23.3333333 C29.4916667,23.3333333 28.9866667,23.3033333 28.48,23.245 L22.4116667,30.7433333 L29.9416667,38.2733333 C32.2433333,40.575 35.9733333,40.575 38.275,38.2733333 L38.275,38.2733333 C40.5766667,35.9716667 40.5766667,32.2416667 38.275,29.94 L31.5666667,23.2333333 Z"
                                    opacity="0.596981957"></path>
                                  <path class="color-background"
                                    d="M33.785,11.285 L28.715,6.215 L34.0616667,0.868333333 C32.82,0.315 31.4483333,0 30,0 C24.4766667,0 20,4.47666667 20,10 C20,10.99 20.1483333,11.9433333 20.4166667,12.8466667 L2.435,27.3966667 C0.95,28.7083333 0.0633333333,30.595 0.00333333333,32.5733333 C-0.0583333333,34.5533333 0.71,36.4916667 2.11,37.89 C3.47,39.2516667 5.27833333,40 7.20166667,40 C9.26666667,40 11.2366667,39.1133333 12.6033333,37.565 L27.1533333,19.5833333 C28.0566667,19.8516667 29.01,20 30,20 C35.5233333,20 40,15.5233333 40,10 C40,8.55166667 39.685,7.18 39.1316667,5.93666667 L33.785,11.285 Z">
                                  </path>
                                </g>
                              </g>
                            </g>
                          </g>
                        </svg>
                      </div>
                      <p class="text-xs mt-1 mb-0 font-weight-bold">Items</p>
                    </div>
                    <h4 class="font-weight-bolder">43</h4>
                    <div class="progress w-75">
                      <div class="progress-bar bg-dark w-50" role="progressbar" aria-valuenow="50" aria-valuemin="0"
                        aria-valuemax="100"></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-7">
          <div class="card z-index-2">
            <div class="card-header pb-0">
              <h6>Sales overview</h6>
              <p class="text-sm">
                <i class="fa fa-arrow-up text-success"></i>
                <span class="font-weight-bold">4% more</span> in 2021
              </p>
            </div>
            <div class="card-body p-3">
              <div class="chart">
                <canvas id="chart-line" class="chart-canvas" height="600" width="1286"
                  style="display: block; box-sizing: border-box; height: 300px; width: 643px;"></canvas>
              </div>
            </div>
          </div>
        </div>
      </div>
      <footer class="footer pt-3  ">
        <div class="container-fluid">
          <div class="row align-items-center justify-content-lg-between">
            <div class="col-lg-6 mb-lg-0 mb-4">
              <div class="copyright text-center text-sm text-muted text-lg-start">
                ©
                <script>
                  document.write(new Date().getFullYear())
                </script>2025,
                made with <i class="fa fa-heart"></i> by
                <a href="https://www.creative-tim.com" class="font-weight-bold" target="_blank">Creative Tim</a>
                for a better web.
              </div>
            </div>
            <div class="col-lg-6">
              <ul class="nav nav-footer justify-content-center justify-content-lg-end">
                <li class="nav-item">
                  <a href="https://www.creative-tim.com" class="nav-link text-muted" target="_blank">Creative Tim</a>
                </li>
                <li class="nav-item">
                  <a href="https://www.creative-tim.com/presentation" class="nav-link text-muted" target="_blank">About
                    Us</a>
                </li>
                <li class="nav-item">
                  <a href="https://www.creative-tim.com/blog" class="nav-link text-muted" target="_blank">Blog</a>
                </li>
                <li class="nav-item">
                  <a href="https://www.creative-tim.com/license" class="nav-link pe-0 text-muted"
                    target="_blank">License</a>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </footer>
    </div>
  </div> <!--end::App Content-->
@endsection

@section('script')
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const viewModel = @json($viewModel);

      viewModel.forEach(userView => {
        const userId = userView.user_id;

        userView.bin_charts.forEach(binChart => {
          const binId = binChart.bin_id;

          if (binChart.labels.length === 0) return;

          const ctx = document.getElementById(`chart-combined-${userId}-${binId}`);
          if (!ctx) return;

          // สร้างกราฟเส้นรวม (Line Chart)
          new Chart(ctx, {
            type: 'line', // เปลี่ยนเป็น 'line'
            data: {
              labels: binChart.labels,
              datasets: [
                {
                  label: 'Temp (°C)',
                  data: binChart.temperatures,
                  borderColor: 'rgb(255, 99, 132)', // สีแดงสำหรับอุณหภูมิ
                  backgroundColor: 'rgba(255, 99, 132, 0.2)',
                  fill: false, // ไม่เติมสีใต้เส้น
                  tension: 0.6 // ความโค้งของเส้น
                },
                {
                  label: 'Humid (%)',
                  data: binChart.humidities,
                  borderColor: 'rgb(54, 162, 235)', // สีน้ำเงินสำหรับความชื้น
                  backgroundColor: 'rgba(54, 162, 235, 0.2)',
                  fill: false, // ไม่เติมสีใต้เส้น
                  tension: 0.8
                }
              ]
            },
            options: {
              responsive: true,
              maintainAspectRatio: false,
              plugins: {
                legend: {
                  position: 'top',
                },
                title: {
                  display: false,
                }
              },
              scales: {
                y: {
                  beginAtZero: false,
                  title: { display: true, text: 'Value (°C / %)' }
                },
                x: {
                  title: { display: true, text: 'Time (H:i)' }
                }
              }
            }
          });
        });
      });
    });
  </script>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const globalTemperatureData = @json($globalTemperatureData);
      const globalHumidityData = @json($globalHumidityData);
      const globalMethaneData = @json($globalMethaneData);
      const globalWeightData = @json($globalWeightData);

      // ใช้ค่าอ้างอิงจากชุดข้อมูลใดชุดข้อมูลหนึ่ง (เพราะถูกส่งมาเหมือนกันหมด)
      const refs = globalTemperatureData.references;

      // ฟังก์ชันช่วยสร้างสีสุ่มสำหรับแต่ละเส้น
      const getRandomColor = () => {
        const r = Math.floor(Math.random() * 200);
        const g = Math.floor(Math.random() * 200);
        const b = Math.floor(Math.random() * 200);
        return `rgb(${r}, ${g}, ${b})`;
      };

      // ฟังก์ชันสำหรับสร้าง Reference Annotation (เส้น Min/Max)
      const createReferenceAnnotations = (minVal, maxVal, unit, minColor, maxColor) => {
        // เช็คว่ามีค่า Min/Max ที่ถูกต้องหรือไม่
        if (minVal === undefined || maxVal === undefined) return {};

        return {
          minLine: {
            type: 'line', yMin: minVal, yMax: minVal,
            borderColor: minColor, borderWidth: 2, borderDash: [6, 6],
            label: { content: `Min Ref: ${minVal}${unit}`, enabled: true, position: 'start', backgroundColor: minColor.replace('1)', '0.8)') }
          },
          maxLine: {
            type: 'line', yMin: maxVal, yMax: maxVal,
            borderColor: maxColor, borderWidth: 2, borderDash: [6, 6],
            label: { content: `Max Ref: ${maxVal}${unit}`, enabled: true, position: 'end', backgroundColor: maxColor.replace('1)', '0.8)') }
          }
        };
      };

      // ฟังก์ชันสร้างกราฟรวม (รองรับ 4 พารามิเตอร์)
      const createGlobalChart = (elementId, chartData, unit, title, refKey) => {
        const ctx = document.getElementById(elementId);
        if (!ctx || chartData.labels.length === 0) return;

        let annotations = {};
        let minColor = 'rgba(75, 192, 192, 1)'; // สีเริ่มต้น Min
        let maxColor = 'rgba(255, 159, 64, 1)'; // สีเริ่มต้น Max

        // ดึงค่า Min/Max ตามประเภทกราฟ
        const minRef = refs[`${refKey}_min`];
        const maxRef = refs[`${refKey}_max`];

        // กำหนดสีเส้น Reference ตามประเภทกราฟ (เพื่อให้แยกแยะง่าย)
        if (refKey === 'temp') {
          minColor = 'rgba(75, 192, 192, 1)'; maxColor = 'rgba(255, 159, 64, 1)'; // สีส้ม/ฟ้า
        } else if (refKey === 'hum') {
          minColor = 'rgba(153, 102, 255, 1)'; maxColor = 'rgba(255, 205, 86, 1)'; // สีม่วง/เหลือง
        } else if (refKey === 'methane') {
          minColor = 'rgba(54, 162, 235, 1)'; maxColor = 'rgba(255, 99, 132, 1)'; // สีน้ำเงิน/แดง
        } else if (refKey === 'weight') {
          minColor = 'rgba(255, 99, 132, 1)'; maxColor = 'rgba(75, 192, 192, 1)'; // สีแดง/ฟ้า
        }

        annotations = createReferenceAnnotations(minRef, maxRef, unit, minColor, maxColor);

        const datasets = chartData.datasets.map(dataset => {
          const randomColor = getRandomColor();
          return {
            ...dataset,
            borderColor: randomColor,
            backgroundColor: randomColor.replace('rgb', 'rgba').replace(')', ', 0.1)'),
            fill: false,
            tension: 0.2,
            pointRadius: 4,
            spanGaps: true
          };
        });

        new Chart(ctx, {
          type: 'line',
          data: {
            labels: chartData.labels,
            datasets: datasets
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
              y: {
                beginAtZero: false,
                title: { display: true, text: title + ' (' + unit + ')' }
              },
              x: {
                title: { display: true, text: 'Time (H:i:s)' },
                ticks: { autoSkip: true, maxRotation: 45 }
              }
            },
            plugins: {
              title: { display: false },
              legend: { display: true },
              annotation: { annotations: annotations },
              // ** Tooltip แสดง Bin/User ID **
              tooltip: {
                mode: 'index',
                intersect: false,
                callbacks: {
                  label: function (context) {
                    let label = context.dataset.label || '';
                    if (label) { label += ': '; }
                    if (context.parsed.y !== null) {
                      // แสดงค่าทศนิยม 2 ตำแหน่ง
                      label += context.parsed.y.toFixed(2) + unit;
                    }
                    return label;
                  },
                  title: function (context) {
                    return 'Time: ' + context[0].label;
                  }
                }
              }
            }
          }
        });
      };

      // 1. สร้างกราฟอุณหภูมิ
      createGlobalChart('chart-global-temperature', globalTemperatureData, '°C', 'Temperature', 'temp');

      // 2. สร้างกราฟความชื้น
      createGlobalChart('chart-global-humidity', globalHumidityData, '%', 'Humidity', 'hum');

      // 3. สร้างกราฟมีเทน
      createGlobalChart('chart-global-methane', globalMethaneData, ' PPM', 'Methane Gas', 'methane');

      // 4. สร้างกราฟน้ำหนัก
      createGlobalChart('chart-global-weight', globalWeightData, ' Kg', 'Weight', 'weight');
    });
  </script>
@endsection
  {{-- @foreach ($viewModel as $userView)
          <div class="col-lg-6 mb-lg-0 mb-4">
            <div class="card z-index-2">
              <div class="card-body p-2">
                <div class="bg-dark border-radius-md py-3 pe-1 mb-3">
                  <div class="chart" style="max-height: 150px; min-height: 150px">
                    <div class="row">
                      @forelse ($userView['bin_charts'] as $binChart)
                        {{-- ใช้ col-md-6 เพื่อให้แสดง 2 ถังต่อแถว --}
                        <div class="col-md-6 mb-4">
                          <div class="card  border-success">

                            <div class="card-body p-1">

                              @if (count($binChart['labels']) > 0)

                                <div class="chart-container" style="height:150px;">
                                  <canvas id="chart-combined-{{ $userView['user_id'] }}-{{ $binChart['bin_id'] }}"></canvas>
                                </div>

                              @else
                                <div class="alert alert-warning">
                                  No IoT data available for this bin.
                                </div>
                              @endif
                            </div>
                          </div>
                        </div>
                      @empty
                        <div class="col-12">
                          <div class="alert alert-info">
                            No foodwaste bins found for this user.
                          </div>
                        </div>
                      @endforelse
                    </div>
                  </div>
                </div>
                <h6 class="ms-2 mt-4 mb-0"> Active Users </h6>
                <p class="text-sm ms-2"> (<span class="font-weight-bolder">User ID: {{ $userView['user_id'] }}</span>) than
                  last
                  week </p>
                <div class="border-radius-lg">
                  <div class="row">
                    <div class="col-3 py-3 ps-0">
                      <div class="d-flex mb-2">
                        <div
                          class="icon icon-shape icon-xxs shadow border-radius-sm bg-primary text-center me-2 d-flex align-items-center justify-content-center">
                          <svg width="10px" height="10px" viewBox="0 0 40 44" version="1.1"
                            xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                            <title>document</title>
                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                              <g transform="translate(-1870.000000, -591.000000)" fill="#FFFFFF" fill-rule="nonzero">
                                <g transform="translate(1716.000000, 291.000000)">
                                  <g transform="translate(154.000000, 300.000000)">
                                    <path class="color-background"
                                      d="M40,40 L36.3636364,40 L36.3636364,3.63636364 L5.45454545,3.63636364 L5.45454545,0 L38.1818182,0 C39.1854545,0 40,0.814545455 40,1.81818182 L40,40 Z"
                                      opacity="0.603585379"></path>
                                    <path class="color-background"
                                      d="M30.9090909,7.27272727 L1.81818182,7.27272727 C0.814545455,7.27272727 0,8.08727273 0,9.09090909 L0,41.8181818 C0,42.8218182 0.814545455,43.6363636 1.81818182,43.6363636 L30.9090909,43.6363636 C31.9127273,43.6363636 32.7272727,42.8218182 32.7272727,41.8181818 L32.7272727,9.09090909 C32.7272727,8.08727273 31.9127273,7.27272727 30.9090909,7.27272727 Z M18.1818182,34.5454545 L7.27272727,34.5454545 L7.27272727,30.9090909 L18.1818182,30.9090909 L18.1818182,34.5454545 Z M25.4545455,27.2727273 L7.27272727,27.2727273 L7.27272727,23.6363636 L25.4545455,23.6363636 L25.4545455,27.2727273 Z M25.4545455,20 L7.27272727,20 L7.27272727,16.3636364 L25.4545455,16.3636364 L25.4545455,20 Z">
                                    </path>
                                  </g>
                                </g>
                              </g>
                            </g>
                          </svg>
                        </div>
                        <p class="text-xs mt-1 mb-0 font-weight-bold">ก๊าซมีเทน</p>
                      </div>
                      <h4 class="font-weight-bolder">0</h4>
                      <div class="progress w-75">
                        <div class="progress-bar bg-dark w-60" role="progressbar" aria-valuenow="60" aria-valuemin="0"
                          aria-valuemax="100"></div>
                      </div>
                    </div>
                    <div class="col-3 py-3 ps-0">
                      <div class="d-flex mb-2">
                        <div
                          class="icon icon-shape icon-xxs shadow border-radius-sm bg-gradient-info text-center me-2 d-flex align-items-center justify-content-center">
                          <svg width="10px" height="10px" viewBox="0 0 40 40" version="1.1"
                            xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                            <title>spaceship</title>
                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                              <g transform="translate(-1720.000000, -592.000000)" fill="#FFFFFF" fill-rule="nonzero">
                                <g transform="translate(1716.000000, 291.000000)">
                                  <g transform="translate(4.000000, 301.000000)">
                                    <path class="color-background"
                                      d="M39.3,0.706666667 C38.9660984,0.370464027 38.5048767,0.192278529 38.0316667,0.216666667 C14.6516667,1.43666667 6.015,22.2633333 5.93166667,22.4733333 C5.68236407,23.0926189 5.82664679,23.8009159 6.29833333,24.2733333 L15.7266667,33.7016667 C16.2013871,34.1756798 16.9140329,34.3188658 17.535,34.065 C17.7433333,33.98 38.4583333,25.2466667 39.7816667,1.97666667 C39.8087196,1.50414529 39.6335979,1.04240574 39.3,0.706666667 Z M25.69,19.0233333 C24.7367525,19.9768687 23.3029475,20.2622391 22.0572426,19.7463614 C20.8115377,19.2304837 19.9992882,18.0149658 19.9992882,16.6666667 C19.9992882,15.3183676 20.8115377,14.1028496 22.0572426,13.5869719 C23.3029475,13.0710943 24.7367525,13.3564646 25.69,14.31 C26.9912731,15.6116662 26.9912731,17.7216672 25.69,19.0233333 L25.69,19.0233333 Z">
                                    </path>
                                    <path class="color-background"
                                      d="M1.855,31.4066667 C3.05106558,30.2024182 4.79973884,29.7296005 6.43969145,30.1670277 C8.07964407,30.6044549 9.36054508,31.8853559 9.7979723,33.5253085 C10.2353995,35.1652612 9.76258177,36.9139344 8.55833333,38.11 C6.70666667,39.9616667 0,40 0,40 C0,40 0,33.2566667 1.855,31.4066667 Z">
                                    </path>
                                    <path class="color-background"
                                      d="M17.2616667,3.90166667 C12.4943643,3.07192755 7.62174065,4.61673894 4.20333333,8.04166667 C3.31200265,8.94126033 2.53706177,9.94913142 1.89666667,11.0416667 C1.5109569,11.6966059 1.61721591,12.5295394 2.155,13.0666667 L5.47,16.3833333 C8.55036617,11.4946947 12.5559074,7.25476565 17.2616667,3.90166667 L17.2616667,3.90166667 Z"
                                      opacity="0.598539807"></path>
                                    <path class="color-background"
                                      d="M36.0983333,22.7383333 C36.9280725,27.5056357 35.3832611,32.3782594 31.9583333,35.7966667 C31.0587397,36.6879974 30.0508686,37.4629382 28.9583333,38.1033333 C28.3033941,38.4890431 27.4704606,38.3827841 26.9333333,37.845 L23.6166667,34.53 C28.5053053,31.4496338 32.7452344,27.4440926 36.0983333,22.7383333 L36.0983333,22.7383333 Z"
                                      opacity="0.598539807"></path>
                                  </g>
                                </g>
                              </g>
                            </g>
                          </svg>
                        </div>
                        <p class="text-xs mt-1 mb-0 font-weight-bold">ความชื้น</p>
                      </div>
                      <h4 class="font-weight-bolder">70%</h4>
                      <div class="progress w-75">
                        <div class="progress-bar bg-dark w-90" role="progressbar" aria-valuenow="90" aria-valuemin="0"
                          aria-valuemax="100"></div>
                      </div>
                    </div>
                    <div class="col-3 py-3 ps-0">
                      <div class="d-flex mb-2">
                        <div
                          class="icon icon-shape icon-xxs shadow border-radius-sm bg-gradient-warning text-center me-2 d-flex align-items-center justify-content-center">
                          <svg width="10px" height="10px" viewBox="0 0 43 36" version="1.1"
                            xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                            <title>credit-card</title>
                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                              <g transform="translate(-2169.000000, -745.000000)" fill="#FFFFFF" fill-rule="nonzero">
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
                        <p class="text-xs mt-1 mb-0 font-weight-bold">อุณหภูมิ</p>
                      </div>
                      <h4 class="font-weight-bolder">50 ํC</h4>
                      <div class="progress w-75">
                        <div class="progress-bar bg-dark w-30" role="progressbar" aria-valuenow="30" aria-valuemin="0"
                          aria-valuemax="100"></div>
                      </div>
                    </div>
                    <div class="col-3 py-3 ps-0">
                      <div class="d-flex mb-2">
                        <div
                          class="icon icon-shape icon-xxs shadow border-radius-sm bg-gradient-danger text-center me-2 d-flex align-items-center justify-content-center">
                          <svg width="10px" height="10px" viewBox="0 0 40 40" version="1.1"
                            xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                            <title>settings</title>
                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                              <g transform="translate(-2020.000000, -442.000000)" fill="#FFFFFF" fill-rule="nonzero">
                                <g transform="translate(1716.000000, 291.000000)">
                                  <g transform="translate(304.000000, 151.000000)">
                                    <polygon class="color-background" opacity="0.596981957"
                                      points="18.0883333 15.7316667 11.1783333 8.82166667 13.3333333 6.66666667 6.66666667 0 0 6.66666667 6.66666667 13.3333333 8.82166667 11.1783333 15.315 17.6716667">
                                    </polygon>
                                    <path class="color-background"
                                      d="M31.5666667,23.2333333 C31.0516667,23.2933333 30.53,23.3333333 30,23.3333333 C29.4916667,23.3333333 28.9866667,23.3033333 28.48,23.245 L22.4116667,30.7433333 L29.9416667,38.2733333 C32.2433333,40.575 35.9733333,40.575 38.275,38.2733333 L38.275,38.2733333 C40.5766667,35.9716667 40.5766667,32.2416667 38.275,29.94 L31.5666667,23.2333333 Z"
                                      opacity="0.596981957"></path>
                                    <path class="color-background"
                                      d="M33.785,11.285 L28.715,6.215 L34.0616667,0.868333333 C32.82,0.315 31.4483333,0 30,0 C24.4766667,0 20,4.47666667 20,10 C20,10.99 20.1483333,11.9433333 20.4166667,12.8466667 L2.435,27.3966667 C0.95,28.7083333 0.0633333333,30.595 0.00333333333,32.5733333 C-0.0583333333,34.5533333 0.71,36.4916667 2.11,37.89 C3.47,39.2516667 5.27833333,40 7.20166667,40 C9.26666667,40 11.2366667,39.1133333 12.6033333,37.565 L27.1533333,19.5833333 C28.0566667,19.8516667 29.01,20 30,20 C35.5233333,20 40,15.5233333 40,10 C40,8.55166667 39.685,7.18 39.1316667,5.93666667 L33.785,11.285 Z">
                                    </path>
                                  </g>
                                </g>
                              </g>
                            </g>
                          </svg>
                        </div>
                        <p class="text-xs mt-1 mb-0 font-weight-bold">น้ำหนัก</p>
                      </div>
                      <h4 class="font-weight-bolder">8กก.</h4>
                      <div class="progress w-75">
                        <div class="progress-bar bg-dark w-50" role="progressbar" aria-valuenow="50" aria-valuemin="0"
                          aria-valuemax="100"></div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

          </div>
        @endforeach --}}