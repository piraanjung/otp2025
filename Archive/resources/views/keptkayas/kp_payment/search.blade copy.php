ƒ@extends('layouts.admin1')


@section('nav-payment-search')
active
@endsection
@section('style')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<!-- Latest compiled and minified JavaScript -->

<style>
    .main {
        width: 80%;
        margin-left: 20%
    }

    .main div {
        width: 100%;
    }

    .sidebar .content-wrapper div {
        /* width:200px; */
    }

    .hidden {
        display: none
    }

    .budgetyear-div {
        cursor: pointer;
    }

    .budgetyear-div:hover {
        opacity: 0.8;
        transform: scale(1.05);
        transition: all 1s;
    }
</style>
<link rel="stylesheet" href="//code.jquery.com/ui/1.13.1/themes/base/jquery-ui.css">
@endsection

@section('content')
<div class="search-form">
    <div class="page-header min-height-300 border-radius-xl"
        style="background-image: url(' ../../../assets/img/curved-images/curved0.jpg'); background-position-y: 50%;">
        <span class="mask bg-gradient-primary opacity-6"></span>
    </div>
    <div class="card card-body blur shadow-blur mx-4 mt-n6 overflow-hidden">
        <div class="row gx-4">
            <div class="col-1">
                <div class="avatar avatar-xl position-relative">
                    <img src="{{ asset('soft-ui/assets/img/bruce-mars.jpg') }}"
                        alt="profile_image" class="w-100 border-radius-lg shadow-sm">
                </div>
            </div>
            <div class="col-5 my-auto">
                <div class="h-100">
                    <h5 class="mb-1">ค้นหา : ชื่อ,ที่อยู่ ,เลขมิเตอร์</h5>
                    <form action="{{ route('payment.search') }}" method="POST" class="d-flex">
                        @csrf
                        <select class="js-example-basic-single form-control" name="user_info">
                            <option>เลือก...</option>
                            @foreach($users as $user)
                            <option value="{{ $user->usermeterinfos->meter_id_fk }}">
                                {{ $user->firstname . ' ' . $user->lastname . ' บ้านเลขที่ ' . $user->address . ' ' . $user->user_zone->zone_name . ' | ' . $user->usermeterinfos->meternumber }}
                            </option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-info btn-sm"><i class="fa fa-search">ค้นหา</i></button>
                    </form>

                </div>
            </div>
            <div class="col-lg-4 col-md-6 my-sm-auto ms-sm-auto me-sm-0 mx-auto mt-3">
                <div class="nav-wrapper position-relative end-0">
                </div>
            </div>
        </div>
    </div>
</div><!-- search-form-->

<div class="container-fluid my-3 py-3">
    <div class="row mb-5">
        <div class="col-lg-3">
            <div class="card position-sticky top-1">
                <ul class="nav flex-column bg-white border-radius-lg p-3">
                    <li class="nav-item">
                        <a class="nav-link text-body" data-scroll="" href="#profile">
                            <div class="icon me-2">
                                <svg class="text-dark mb-1" width="16px" height="16px" viewBox="0 0 40 40" version="1.1"
                                    xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                                    <title>spaceship</title>
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <g transform="translate(-1720.000000, -592.000000)" fill="#FFFFFF"
                                            fill-rule="nonzero">
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
                            <span class="text-sm">Profile</span>
                        </a>
                    </li>
                    <li class="nav-item pt-2">
                        <a class="nav-link text-body" data-scroll="" href="#basic-info">
                            <div class="icon me-2">
                                <svg class="text-dark mb-1" width="16px" height="16px" viewBox="0 0 40 44" version="1.1"
                                    xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                                    <title>document</title>
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <g transform="translate(-1870.000000, -591.000000)" fill="#FFFFFF"
                                            fill-rule="nonzero">
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
                            <span class="text-sm">Basic Info</span>
                        </a>
                    </li>

                </ul>
            </div>
        </div>
        <div class="col-lg-9 mt-lg-0 mt-4">

            <div class="card card-body" id="profile">
                <div class="row justify-content-center align-items-center">
                    <div class="col-sm-auto col-4">
                        <div class="avatar avatar-xl position-relative">
                            <img src="../../../assets/img/bruce-mars.jpg" alt="bruce"
                                class="w-100 border-radius-lg shadow-sm">
                        </div>
                    </div>
                    <div class="col-sm-auto col-8 my-auto">
                        <div class="h-100">
                            <h5 class="mb-1 font-weight-bolder">
                                Alec Thompson
                            </h5>
                            <p class="mb-0 font-weight-bold text-sm">
                                CEO / Co-Founder
                            </p>
                        </div>
                    </div>
                    <div class="col-sm-auto ms-sm-auto mt-sm-0 mt-3 d-flex">
                        <label class="form-check-label mb-0">
                            <small id="profileVisibility">
                                Switch to invisible
                            </small>
                        </label>
                        <div class="form-check form-switch ms-2">
                            <input class="form-check-input" type="checkbox" id="flexSwitchCheckDefault23" checked=""
                                onchange="visible()">
                        </div>
                    </div>
                </div>
            </div>


            <div class="card mt-4" id="basic-info">
                <div class="card-header">
                    <h5>Basic Info</h5>
                </div>
                <div class="card-body pt-0">

                </div>
            </div>
            @endfor
        </div>
    </div>

</div>
@if(collect($inv_by_budgetyear)->isNotEmpty())
<div class="py-4">
    <div class="row">
        <div class="col-lg-12 col-12">
            <div class="row">
                <div class="col-lg-6 col-md-6 col-12 ">
                    <div class="card">
                        <div class="card-body">
                            <div class="avatar avatar-xl d-flex mb-3" style="justify-content:left">
                                <img src="http://localhost:8000/soft-ui/assets/img/bruce-mars.jpg"
                                    alt="profile_image" class="w-100 border-radius-lg shadow-sm">
                                <div class="d-flex">
                                    <div class="ms-2 my-auto text-end justify-content-between">
                                        <p class="text-xs mb-0 text-dark">เลขผู้ใช้น้ำ</p>
                                        <h6 class="mb-0">
                                            &nbsp;&nbsp;{{ $inv_by_budgetyear[1][0]['usermeterinfos']['meternumber'] }}
                                        </h6>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex">
                                <div class="ms-2 my-auto">
                                    <p class="text-xs mb-0">ชื่อผู้ใช้น้ำ</p>
                                    <h6 class="mb-0">
                                        &nbsp;&nbsp;{{ $inv_by_budgetyear[1][0]['usermeterinfos']['user']['firstname'] . ' ' . $inv_by_budgetyear[1][0]['usermeterinfos']['user']['lastname'] }}
                                    </h6>
                                </div>
                            </div>

                            <div class="d-flex">
                                <div class="ms-2 my-auto">
                                    <p class="text-xs mb-0">ที่อยู่</p>
                                    <h6 class="mb-0">&nbsp;&nbsp;
                                        {{ $inv_by_budgetyear[1][0]['usermeterinfos']['user']['address'] }}
                                        {{ $inv_by_budgetyear[1][0]['usermeterinfos']['undertake_zone']['undertake_zone_name'] }}
                                        {{ $inv_by_budgetyear[1][0]['usermeterinfos']['undertake_subzone']['undertake_subzone_name'] }}
                                    </h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div>
    <div class="sidebar">
        <!-- ปุ่มปีงบประมาณด้านซ้าย -->
        <div class="content-wrapper">
            @foreach(collect($inv_by_budgetyear)->reverse() as $budgetyear)
            <div class="col-12 mt-2 budgetyear-div ">
                <div class="card">
                    <span
                        class="mask {{ $budgetyear[0]['invoice_period']['budgetyear']['status'] == 'active' ? 'bg-gradient-info' : 'bg-gradient-dark' }} opacity-9 border-radius-xl"></span>
                    <div class="card-body p-3 position-relative">
                        <a href="#by{{ $budgetyear[0]['invoice_period']['budgetyear_id'] }}"
                            data-scroll="">
                            <div class="row">
                                <div class="col-12  d-flex justify-content-between">
                                    <div class="icon icon-shape bg-white shadow text-center border-radius-md">
                                        <i class="ni ni-circle-08 text-dark text-gradient text-lg opacity-10"
                                            aria-hidden="true"></i>
                                    </div>
                                    <div class="">
                                        <h5 class="text-white font-weight-bolder mb-0 text-end">
                                            {{ $budgetyear[0]['invoice_period']['budgetyear']['budgetyear_name'] }}
                                        </h5>
                                        <span class="text-white text-sm">ปีงบประมาณ</span>
                                    </div>
                                </div>
                                <div class="col-12 text-start mt-1 pt-1" style="border-top: 1px solid;">
                                    <?php
                                    $lastmeter_sum = collect($budgetyear)->sum('lastmeter');
                                    $currentmeter_sum = collect($budgetyear)->sum('currentmeter');
                                    $net = $currentmeter_sum - $lastmeter_sum;
                                    $inv_period_count = collect($budgetyear)->count();
                                    ?>
                                    <p class="text-white text-sm  font-weight-bolder mt-auto mb-0">
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $inv_period_count }}
                                        <sup>รอบบิล</sup>
                                    </p>
                                    <p class="text-white text-sm  font-weight-bolder mt-auto mb-0"> <span
                                            class="text-sm"> ใช้น้ำ</span>
                                        {{ $net }}<sup>ลิตร </sup>
                                    </p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    <div class="main">
        <!-- ตารางด้านขวา -->
        <div class="col-12 col-lg-10 col-md-8">
            @foreach(collect($inv_by_budgetyear)->reverse() as $budgetyear)
            <?php
            $grouped = collect($budgetyear)->groupBy('accounts_id_fk');
            ?>
            <div class="row my-4"
                id="by{{ $budgetyear[0]['invoice_period']['budgetyear_id'] }}">
                <div class="col-12">
                    <div
                        class="card {{ $budgetyear[0]['invoice_period']['budgetyear']['status'] == 'active' ? 'bg-gradient-info' : 'bg-gradient-dark' }}">
                        <div
                            class="card-header {{ $budgetyear[0]['invoice_period']['budgetyear']['status'] == 'active' ? 'bg-gradient-info' : 'bg-gradient-dark' }}">

                            <div class="card-title text-white fs-4 fw-bold">
                                ปีงบประมาณ
                                {{ $budgetyear[0]['invoice_period']['budgetyear']['budgetyear_name'] }}
                            </div>
                        </div>
                        <div class="card-body">
                            @foreach($grouped as $group)
                            <div class="card mt-4">
                                <div class="card-header">
                                    <div class="card-title">
                                        เลขใบเสร็จรับเงิน{{ $group[0]['accounts_id_fk'] . '[' . $group[0]['updated_at'] . ']' }}
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table align-items-center mb-0 ">
                                            <thead>
                                                <tr>
                                                    <th
                                                        class="text-uppercase text-secondary text-xxs font-weight-bolder text-center">
                                                        เลขใบแจ้งหนี้
                                                    </th>
                                                    <th
                                                        class="text-uppercase text-secondary text-xxs font-weight-bolder text-center ps-2">
                                                        รอบบิล</th>
                                                    <th
                                                        class="text-uppercase text-secondary text-xxs font-weight-bolder text-center ps-2">
                                                        ก่อนจดมิเตอร์<sup>หน่วย</sup></th>
                                                    <th
                                                        class="text-center text-uppercase text-secondary text-xxs font-weight-bolder text-center">
                                                        หลังจดมิเตอร์<sup>หน่วย</sup></th>
                                                    <th
                                                        class="text-uppercase text-secondary text-xxs font-weight-bolder text-center ps-2">
                                                        ค่าน้ำประปา<sup>บาท</sup></th>
                                                    <th
                                                        class="text-center text-uppercase text-secondary text-xxs font-weight-bolder text-center">
                                                        รักษามิเตอร์<sup>บาท</sup></th>
                                                    <th
                                                        class="text-center text-uppercase text-secondary text-xxs font-weight-bolder text-center">
                                                        รวมเป็นเงิน<sup>บาท</sup></th>
                                                </tr>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $sum = 0; ?>
                                                @foreach($group as $invoice)
                                                <?php
                                                $lastmeter = $invoice['lastmeter'];
                                                $currentmeter = $invoice['currentmeter'];
                                                $diff = $currentmeter - $lastmeter;
                                                $sum += $diff == 0 ? 10 : $diff * 8;

                                                ?>
                                                <tr>
                                                    <td>

                                                        <div class="text-center">
                                                            <h6 class="mb-0 text-sm">
                                                                {{ $invoice['id'] }}
                                                            </h6>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-dot me-4">
                                                            <i class="bg-info"></i>
                                                            <span
                                                                class="text-dark text-xs">{{ $invoice['invoice_period']['inv_p_name'] }}</span>
                                                        </span>
                                                    </td>
                                                    <td class="align-middle text-center text-sm">
                                                        <p class="text-secondary mb-0 text-sm">
                                                            {{ $invoice['lastmeter'] }}
                                                        </p>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <span
                                                            class="text-secondary text-xs font-weight-bold">{{ $invoice['currentmeter'] }}</span>
                                                    </td>
                                                    <td class="align-middle text-center text-sm">
                                                        <p class="text-secondary mb-0 text-sm">
                                                            {{ $diff == 0 ? 0 : $diff * 8 }}
                                                        </p>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <span
                                                            class="text-secondary text-xs font-weight-bold">{{ $diff == 0 ? 10 : 0 }}</span>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <span
                                                            class="text-secondary text-xs font-weight-bold">{{ $diff == 0 ? 10 : $diff * 8 }}</span>
                                                    </td>
                                                </tr>
                                                @endforeach
                                                <tr>
                                                    <td colspan="6" class="text-end"><b>รวมเป็นเงิน</b>
                                                    </td>
                                                    <td class="text-center">{{ number_format($sum, 2) }}
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            @endforeach

                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

</div>
@endsection


@section('script')
<script
    src="https://www.jqueryscript.net/demo/Export-Html-Table-To-Excel-Spreadsheet-using-jQuery-table2excel/src/jquery.table2excel.js">
</script>
{{-- <script src="{{ asset('/js/my_script.js') }}"></script> --}}
<script src="https://code.jquery.com/ui/1.13.1/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.1/bootstrap3-typeahead.min.js"></script>

<script>
    $(document).ready(function() {
        $('.js-example-basic-single').select2();
    });
</script>
@endsection