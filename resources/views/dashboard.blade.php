@extends('layouts.admin1')
@section('nav-dashboard')
    active
@endsection

@section('nav-header')
    Dashboard
@endsection
@section('nav-main')
    <a href="{{ route('dashboard') }}">หน้าหลัก</a>
@endsection

@section('nav-topic')
    <h3>งานประปา องค์การบริหารส่วนตำบลขามป้อม</h3>
@endsection
@section('style')
    <script src="{{ asset('js/chartjs/chart.js_2.7.1.js') }}"></script>
@endsection
@section('content')
    <div class="container-fluid py-4">
        <div class="row mt-4">
            <div class="col-12">
                <div class="card bg-gradient-secondary">
                    <img src="{{ asset('soft-ui/assets/img/shapes/waves-white.svg') }}" alt="pattern-lines"
                        class="position-absolute opacity-4 start-0 top-0 w-100">
                    <div class="card-body px-5 z-index-1 bg-cover">
                        <div class="row">
                            <div class="col-lg-4 col-12 text-center">
                                <img class="w-75 w-lg-auto mt-n7 mt-lg-n9 d-none d-md-block"
                                    src="{{ asset('soft-ui/assets/img/water.png') }}" alt="car image">
                                <div class="d-flex align-items-center">
                                    <h4 class="text-white opacity-7 ms-0 ms-md-auto">ปีงบประมาณ</h4>
                                    <h2 class="text-white ms-2 me-auto">{{$current_budgetyear->budgetyear_name}}</h2>
                                </div>
                            </div>
                            <div class="col-lg-3 col-12 my-auto">
                                <h4 class="text-white opacity-9">ข้อมูลทั่วไป</h4>
                                <hr class="horizontal light mt-1 mb-3">
                                <div class="d-flex">
                                    <div>
                                        <h6 class="mb-0 text-white opacity-7">จำนวนหมู่บ้าน</h6>
                                        <h3 class="text-white">{{ $subzone_count }} <small
                                                class="text-sm align-top">หมู่บ้าน</small></h3>
                                    </div>
                                    <div class="ms-lg-6 ms-4">
                                        <h6 class="mb-0 text-white opacity-7">จำนวนสมาชิก</h6>
                                        <h3 class="text-white">{{ number_format($user_count_sum) }} <small
                                                class="text-sm align-top">ราย</small></h3>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-5 col-12 my-auto">
                                <h4 class="text-white opacity-9">&nbsp;</h4>
                                <hr class="horizontal light mt-1 mb-3">
                                <div class="d-flex">
                                    <div>
                                        <h6 class="mb-0 text-white opacity-7">ปริมาณการใช้น้ำ</h6>
                                        <h3 class="text-white">{{ number_format($water_used_total) }} <small
                                                class="text-sm align-top">หน่วย</small></h3>
                                    </div>
                                    <div class="ms-lg-6 ms-4">
                                        <h6 class="mb-0 text-white opacity-7">จำนวนเงิน</h6>
                                        <h3 class="text-white">{{ number_format($paid_total, 2) }} <small
                                                class="text-sm align-top">บาท</small>
                                        </h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-lg-3 col-md-6 col-12">
                <div class="card bg-gradient-secondary">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-white text-sm mb-0 text-capitalize font-weight-bold opacity-7">
                                        จัดเก็บเงินได้</p>
                                    <h5 class="text-white font-weight-bolder mb-0">
                                        145 Km
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-white shadow text-center border-radius-md">
                                    <i class="ni ni-money-coins text-dark text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-12 mt-4 mt-md-0">
                <div class="card bg-gradient-secondary">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-white text-sm mb-0 text-capitalize font-weight-bold opacity-7">ค้างชำระ
                                    </p>
                                    <h5 class="text-white font-weight-bolder mb-0">
                                        99 %
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-white shadow text-center border-radius-md">
                                    <i class="ni ni-controller text-dark text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-12 mt-4 mt-lg-0">
                <div class="card bg-gradient-secondary">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-white text-sm mb-0 text-capitalize font-weight-bold opacity-7">Average
                                        Speed</p>
                                    <h5 class="text-white font-weight-bolder mb-0">
                                        56 Km/h
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-white shadow text-center border-radius-md">
                                    <i class="ni ni-delivery-fast text-dark text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-12 mt-4 mt-lg-0">
                <div class="card bg-gradient-secondary">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-white text-sm mb-0 text-capitalize font-weight-bold opacity-7">Music
                                        Volume</p>
                                    <h5 class="text-white font-weight-bolder mb-0">
                                        15/100
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-white shadow text-center border-radius-md">
                                    <i class="ni ni-note-03 text-dark text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-6">
                <div class="card">
                    <div class="card-header">
                        <h4>ปริมาณการใช้น้ำแยกตามหมู่บ้าน</h4>
                    </div>
                    <div class="card-body">
                        <canvas id="barChart"></canvas>
                        <script>
                            var ctx = document.getElementById('barChart').getContext('2d');
                            var myChart = new Chart(ctx, {
                                type: 'bar',
                                data: {
                                    labels: @json($data['labels']),
                                    datasets: [{
                                        label: 'ปริมาณการใช้น้ำแยกตามหมู่บ้าน',
                                        data: @json($data['data']),
                                        borderColor: '#acc23',
                                        backgroundColor: '#4dc9f6',
                                        borderWidth: 2,
                                        borderRadius: 10,
                                        borderSkipped: false,
                                    }]
                                },
                                options: {
                                    scales: {
                                        y: {
                                            beginAtZero: true
                                        }
                                    }
                                }
                            });
                        </script>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="card">
                    <div class="card-header">
                        <h4>สมาชิกผู้ใช้น้ำประปาแยกตามพื้นที่จดมิเตอร์</h4>
                    </div>
                    <div class="card-body">
                        <canvas id="user_in_subzone_data_barChart"></canvas>
                        <script>
                            var ctx2 = document.getElementById('user_in_subzone_data_barChart').getContext('2d');
                            var myChart = new Chart(ctx2, {
                                type: 'bar',
                                data: {
                                    labels: @json($user_in_subzone_data['labels']),
                                    datasets: [{
                                        label: 'สมาชิกผู้ใช้น้ำประปาแยกตามพื้นที่จดมิเตอร์',
                                        data: @json($user_in_subzone_data['data']),
                                        borderColor: '#acc23',
                                        backgroundColor: 'pink',
                                        borderWidth: 1,
                                        borderRadius: 10,
                                        borderSkipped: false,
                                    }]
                                },
                                options: {
                                    scales: {
                                        y: {
                                            beginAtZero: true
                                        }
                                    }
                                }
                            });
                        </script>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card bg-gradient-dark mt-4">
                    <div class="card-header bg-transparent">
                        <h4 style="color: white">ตำแหน่งมิเตอร์หลัก</h4>
                        {{-- <div class="row">
                            <div class="col-lg-4 col-md-6 col-12">
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text text-white bg-transparent border-0">
                                        <i class="ni ni-zoom-split-in text-lg" aria-hidden="true"></i>
                                    </span>
                                    <input type="text" class="form-control bg-transparent border-0"
                                        placeholder="Search anything..." onfocus="focused(this)"
                                        onfocusout="defocused(this)">
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-12 my-auto ms-auto">
                                <div class="d-flex align-items-center">
                                    <i class="ni ni-headphones text-lg text-white ms-auto" data-bs-toggle="tooltip"
                                        data-bs-placement="top" aria-label="Headphones connected"
                                        data-bs-original-title="Headphones connected"></i>
                                    <i class="ni ni-button-play text-lg text-white ms-3" data-bs-toggle="tooltip"
                                        data-bs-placement="top" aria-label="Music is playing"
                                        data-bs-original-title="Music is playing"></i>
                                    <i class="ni ni-button-power text-lg text-white ms-3" data-bs-toggle="tooltip"
                                        data-bs-placement="top" aria-label="Start radio"
                                        data-bs-original-title="Start radio"></i>
                                    <i class="ni ni-watch-time text-lg text-white ms-3" data-bs-toggle="tooltip"
                                        data-bs-placement="top" aria-label="Time tracker"
                                        data-bs-original-title="Time tracker"></i>
                                    <h4 class="text-white mb-1 ms-4">10:45</h4>
                                </div>
                            </div>
                        </div> --}}
                        <hr class="horizontal light">
                        {{-- <div class="row">
                            <div class="col-lg-4 col-md-6 col-12">
                                <div class="d-flex align-items-center position-relative">
                                    <h3 class="text-white mb-1">11:13</h3>
                                    <p class="text-white opacity-8 mb-1 ms-3">Estimated arrival time</p>
                                    <hr class="vertical light mt-0">
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6 col-12">
                                <div class="d-flex align-items-center position-relative">
                                    <h3 class="text-white mb-1 ms-auto">2.4<small class="align-top text-sm">Km</small>
                                    </h3>
                                    <p class="text-white opacity-8 mb-1 ms-3 me-auto">Turn right in 2.4 miles</p>
                                    <hr class="vertical light mt-0">
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6 col-12 ms-lg-auto">
                                <div class="d-flex align-items-center">
                                    <h3 class="text-white mb-1 ms-lg-auto">6.3<small class="align-top text-sm">Km</small>
                                    </h3>
                                    <p class="text-white opacity-8 mb-1 ms-3">Distance to Creative Tim</p>
                                </div>
                            </div>
                        </div> --}}
                    </div>
                    <div class="card-body px-0 py-0">
                        <iframe width="100%" height="450" style="border:1px" loading="lazy" allowfullscreen
                            referrerpolicy="no-referrer-when-downgrade"
                        src="https://www.google.com/maps/embed/v1/place?key= AIzaSyA-5AlIGzLhFXErl2STRT6GacX0616iW2o&q=16.2966141,102.66817&language=th-TH&zoom=16">
                        </iframe>
                    </div>
                    <div class="card-footer">
                        {{-- <div class="row">
                            <div class="col-lg-4 col-md-6 col-12">
                                <div class="d-flex align-items-center">
                                    <div class="position-relative">
                                        <div class="avatar avatar-lg">
                                            <img src="{{ asset('soft-ui/assets/img/curved-images/curved10.jpg') }}"
                                                alt="kal" class="border-radius-xl rounded-circle shadow">
                                        </div>
                                        <img class="position-absolute w-60 end-0 bottom-0 me-n3 mb-0"
                                            src="{{ asset('soft-ui/assets/img/small-logos/logo-spotify.svg') }}"
                                            alt="spotify logo">
                                    </div>
                                    <div class="px-3">
                                        <p class="text-white text-sm font-weight-bold mb-0">
                                            You're Mines Still (feat Drake)
                                        </p>
                                        <p class="text-white text-xs mb-2 opacity-8">
                                            Yung Bleu - Hip-Hop
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6 col-12 my-auto text-center mt-3 mt-lg-0">
                                <div class="d-flex align-items-center">
                                    <button class="btn btn-lg btn-icon-only btn-rounded btn-outline-white mb-0 ms-auto">
                                        <i class="ni ni-button-play top-0 rotate-180" aria-hidden="true"></i>
                                    </button>
                                    <button class="btn btn-lg btn-icon-only btn-rounded btn-outline-white mb-0 ms-4">
                                        <i class="ni ni-button-pause top-0" aria-hidden="true"></i>
                                    </button>
                                    <button
                                        class="btn btn-lg btn-icon-only btn-rounded btn-outline-white mb-0 ms-4 me-auto">
                                        <i class="ni ni-button-play top-0" aria-hidden="true"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-6 col-8 my-auto">
                                <p class="text-white mb-2">Volume</p>
                                <div id="sliderRegular" class="noUi-target noUi-ltr noUi-horizontal">
                                    <div class="noUi-base">
                                        <div class="noUi-connects">
                                            <div class="noUi-connect" style="transform: translate(0%, 0px) scale(0.4, 1);">
                                            </div>
                                        </div>
                                        <div class="noUi-origin" style="transform: translate(-600%, 0px); z-index: 4;">
                                            <div class="noUi-handle noUi-handle-lower" data-handle="0" tabindex="0"
                                                role="slider" aria-orientation="horizontal" aria-valuemin="0.0"
                                                aria-valuemax="100.0" aria-valuenow="40.0" aria-valuetext="40.00">
                                                <div class="noUi-touch-area"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-1 col-md-6 col-4 my-auto ms-auto">
                                <i class="ni ni-bullet-list-67 text-white mt-3 ms-auto" data-bs-toggle="tooltip"
                                    data-bs-placement="top" aria-label="Hide menu" data-bs-original-title="Hide menu"></i>
                                <i class="ni ni-chat-round text-white ms-3 mt-3" data-bs-toggle="tooltip"
                                    data-bs-placement="top" aria-label="Track messages"
                                    data-bs-original-title="Track messages"></i>
                            </div>
                        </div> --}}
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
                            </script>2023,
                            made with <i class="fa fa-heart" aria-hidden="true"></i> by
                            <a href="https://www.creative-tim.com" class="font-weight-bold" target="_blank">Creative
                                Tim</a>
                            for a better web.
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <ul class="nav nav-footer justify-content-center justify-content-lg-end">
                            <li class="nav-item">
                                <a href="https://www.creative-tim.com" class="nav-link text-muted" target="_blank">Creative
                                    Tim</a>
                            </li>
                            <li class="nav-item">
                                <a href="https://www.creative-tim.com/presentation" class="nav-link text-muted"
                                    target="_blank">About Us</a>
                            </li>
                            <li class="nav-item">
                                <a href="https://www.creative-tim.com/blog" class="nav-link text-muted"
                                    target="_blank">Blog</a>
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
@endsection

@section('script')
@endsection