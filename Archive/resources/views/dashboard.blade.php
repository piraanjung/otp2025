@extends('layouts.admin1')

{{-- ส่วนหัวและ Navigation --}}
@section('nav-dashboard', 'active')
@section('nav-header', 'Dashboard')

@section('nav-main')
    <a href="{{ route('dashboard') }}" class="text-white">หน้าหลัก</a>
@endsection

@section('nav-topic')
    {{-- ตรวจสอบว่ามีข้อมูล orgInfos หรือไม่ กัน Error --}}
    <h3>งานประปา {{ isset($orgInfos['org_type_name']) ? $orgInfos['org_type_name'] : '' }}{{ isset($orgInfos['org_name']) ? $orgInfos['org_name'] : '' }}</h3>
@endsection

@section('style')
    {{-- CSS เพิ่มเติมถ้าจำเป็น --}}
@endsection

@section('content')
    <div class="container-fluid py-4">

        {{-- ========================================================= --}}
        {{-- ส่วนที่ 1: การ์ดสรุปข้อมูลรวม (Summary Cards) --}}
        {{-- ========================================================= --}}
        <div class="row mt-4">
            <div class="col-12">
                <div class="card bg-gradient-secondary" style="overflow: visible">
                    <img src="{{ asset('soft-ui/assets/img/shapes/waves-white.svg') }}" alt="pattern-lines"
                        class="position-absolute opacity-4 start-0 top-0 w-100">
                    <div class="card-body px-5 z-index-1 bg-cover">
                        <div class="row">
                            <div class="col-lg-3 col-12 text-center">
                                <img class="w-75 w-lg-auto mt-n7 mt-lg-n9 d-none d-md-block"
                                    src="{{ asset('soft-ui/assets/img/water.png') }}" alt="water image"
                                    style="max-height: 200px; object-fit: contain; position: relative; z-index: 100;">
                                <div class="d-flex align-items-center justify-content-center mt-2">
                                    <h4 class="text-white opacity-7 ms-0 ms-md-auto">ปีงบประมาณ</h4>
                                    <h2 class="text-white ms-2 me-auto">
                                        {{ isset($current_budgetyear->budgetyear_name) ? $current_budgetyear->budgetyear_name : '-' }}
                                    </h2>
                                </div>
                            </div>
                            <div class="col-lg-4 col-12 my-auto">
                                <h4 class="text-white opacity-9">ข้อมูลทั่วไป</h4>
                                <hr class="horizontal light mt-1 mb-3">
                                <div class="d-flex">
                                    <div>
                                        <h6 class="mb-0 text-white opacity-7">จำนวนหมู่บ้าน</h6>
                                        <h3 class="text-white">{{ $subzone_count }} <small class="text-sm align-top">หมู่บ้าน</small></h3>
                                    </div>
                                    <div class="ms-lg-6 ms-4">
                                        <h6 class="mb-0 text-white opacity-7">จำนวนสมาชิก</h6>
                                        <h3 class="text-white">{{ number_format($user_count_sum) }} <small class="text-sm align-top">ราย</small></h3>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-5 col-12 my-auto">
                                <h4 class="text-white opacity-9">&nbsp;</h4>
                                <hr class="horizontal light mt-1 mb-3">
                                <div class="d-flex">
                                    <div>
                                        <h6 class="mb-0 text-white opacity-7">ปริมาณการใช้น้ำ</h6>
                                        <h3 class="text-white">{{ number_format($water_used_total) }} <small class="text-sm align-top">หน่วย</small></h3>
                                    </div>
                                    <div class="ms-lg-6 ms-4">
                                        <h6 class="mb-0 text-white opacity-7">จำนวนเงินชำระแล้ว</h6>
                                        <h3 class="text-white">{{ number_format($paid_total, 2) }} <small class="text-sm align-top">บาท</small></h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ========================================================= --}}
        {{-- ส่วนที่ 2: กราฟแถวบน (ใช้น้ำ & สมาชิก) --}}
        {{-- ========================================================= --}}
        <div class="row mt-4">
            <div class="col-lg-6 col-12 mb-4">
                <div class="card z-index-2 h-100">
                    <div class="card-header pb-0">
                        <h6>ปริมาณการใช้น้ำแยกตามหมู่บ้าน</h6>
                    </div>
                    <div class="card-body p-3">
                        <div class="chart">
                            <canvas id="barChart" class="chart-canvas" height="300"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 col-12 mb-4">
                <div class="card z-index-2 h-100">
                    <div class="card-header pb-0">
                        <h6>สมาชิกผู้ใช้น้ำประปาแยกตามพื้นที่จดมิเตอร์</h6>
                    </div>
                    <div class="card-body p-3">
                        <div class="chart">
                            <canvas id="user_in_subzone_data_barChart" class="chart-canvas" height="300"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ========================================================= --}}
        {{-- ส่วนที่ 3: กราฟแถวล่าง (Stacked Bar Chart: สถานะการชำระเงินแยกโซน) --}}
        {{-- ========================================================= --}}
        <div class="row mt-4">
            <div class="col-12 mb-4">
                <div class="card z-index-2">
                    <div class="card-header pb-0">
                        <h6>สถานะการชำระเงินแยกตามโซน (เดือนปัจจุบัน)</h6>
                    </div>
                    <div class="card-body p-3">
                        <div class="chart">
                            <canvas id="zoneBarChart" class="chart-canvas" height="400"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- @include('layouts.footers.auth.footer') --}}

    </div>
@endsection

@section('script')
    {{-- โหลด Chart.js (ป้องกันการโหลดซ้ำ) --}}
    @if (!isset($chartJsLoaded))
        <script src="{{ asset('js/chartjs/chart.js_2.7.1.js') }}"></script>
        @php $chartJsLoaded = true; @endphp
    @endif

    <script>
        document.addEventListener("DOMContentLoaded", function() {

            // --------------------------------------------------------
            // 1. กราฟปริมาณการใช้น้ำ
            // --------------------------------------------------------
            var ctx1 = document.getElementById('barChart').getContext('2d');
            
            // สร้าง Gradient
            var gradientStroke1 = ctx1.createLinearGradient(0, 230, 0, 50);
            gradientStroke1.addColorStop(1, 'rgba(203, 12, 159, 0.2)');
            gradientStroke1.addColorStop(0.2, 'rgba(72, 72, 176, 0.0)');
            gradientStroke1.addColorStop(0, 'rgba(203, 12, 159, 0)');

            new Chart(ctx1, {
                type: 'bar',
                data: {
                    labels: @json($data['labels']),
                    datasets: [{
                        label: 'ปริมาณการใช้น้ำ',
                        data: @json($data['data']),
                        backgroundColor: '#cb0c9f',
                        borderWidth: 0,
                        borderRadius: 4,
                        maxBarThickness: 35
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        yAxes: [{
                            ticks: { beginAtZero: true, fontColor: "#9a9a9a" },
                            gridLines: { borderDash: [2], drawBorder: false }
                        }],
                        xAxes: [{
                            gridLines: { display: false, drawBorder: false },
                            ticks: { fontColor: "#9a9a9a" }
                        }]
                    },
                    tooltips: { mode: 'index', intersect: false }
                }
            });

            // --------------------------------------------------------
            // 2. กราฟสมาชิกแยกตามพื้นที่
            // --------------------------------------------------------
            var ctx2 = document.getElementById('user_in_subzone_data_barChart').getContext('2d');
            
            new Chart(ctx2, {
                type: 'bar',
                data: {
                    labels: @json($user_in_subzone_data['labels']),
                    datasets: [{
                        label: 'จำนวนสมาชิก',
                        data: @json($user_in_subzone_data['data']),
                        backgroundColor: '#17c1e8',
                        borderWidth: 0,
                        borderRadius: 4,
                        maxBarThickness: 35
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        yAxes: [{
                            ticks: { beginAtZero: true, fontColor: "#9a9a9a" },
                            gridLines: { borderDash: [2], drawBorder: false }
                        }],
                        xAxes: [{
                            gridLines: { display: false, drawBorder: false },
                            ticks: { fontColor: "#9a9a9a" }
                        }]
                    }
                }
            });

            // --------------------------------------------------------
            // 3. กราฟสถานะการชำระเงินแยกโซน (Stacked Bar Chart)
            // --------------------------------------------------------
            // รับตัวแปรจาก Controller: $zone_chart_data
            var zoneData = @json(isset($zone_chart_data) ? $zone_chart_data : ['labels'=>[], 'paid'=>[], 'unpaid'=>[]]);

            if(zoneData.labels && zoneData.labels.length > 0) {
                var ctxZone = document.getElementById('zoneBarChart').getContext('2d');
                
                new Chart(ctxZone, {
                    type: 'bar', // ใช้ bar chart ปกติ แต่ config ให้ stack
                    data: {
                        labels: zoneData.labels,
                        datasets: [
                            {
                                label: 'ชำระแล้ว',
                                data: zoneData.paid,
                                backgroundColor: '#82d616', // สีเขียว
                                borderWidth: 0,
                                borderRadius: 4,
                                barPercentage: 0.6,
                            },
                            {
                                label: 'ค้างชำระ',
                                data: zoneData.unpaid,
                                backgroundColor: '#ea0606', // สีแดง
                                borderWidth: 0,
                                borderRadius: 4,
                                barPercentage: 0.6,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        tooltips: {
                            mode: 'index',
                            intersect: false,
                            callbacks: {
                                footer: function(tooltipItems, data) {
                                    var sum = 0;
                                    tooltipItems.forEach(function(tooltipItem) {
                                        sum += data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index];
                                    });
                                    return 'รวมทั้งหมด: ' + sum + ' รายการ';
                                }
                            }
                        },
                        scales: {
                            xAxes: [{
                                stacked: true, // ทำให้กราฟซ้อนกันในแนวตั้ง
                                gridLines: { display: false, drawBorder: false },
                                ticks: { fontColor: "#9a9a9a", autoSkip: false }
                            }],
                            yAxes: [{
                                stacked: true, // ทำให้กราฟซ้อนกัน
                                ticks: {
                                    beginAtZero: true,
                                    fontColor: "#9a9a9a",
                                    precision: 0 // บังคับให้แสดงจำนวนเต็ม
                                },
                                gridLines: {
                                    borderDash: [2],
                                    color: '#dee2e6',
                                    zeroLineColor: '#dee2e6',
                                    drawBorder: false,
                                }
                            }]
                        }
                    }
                });
            }

        });
    </script>
@endsection