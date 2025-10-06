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
    <h3>งานประปา  {{$orgInfos['org_type_name'].$orgInfos['org_name']}}</h3>
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
                            <div class="col-lg-4 col-12 my-auto">
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

                            <div class="col-lg-4 col-12 my-auto">
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

         
        </div>
        {{-- <footer class="footer pt-3  ">
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
        </footer> --}}
    </div>
@endsection

@section('script')
@endsection