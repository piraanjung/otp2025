@extends('layouts.foodwaste')

@section('style')
    <style>
        .icon-shape {
            width: 48px;
            height: 48px;
            background-position: center;
            border-radius: 0.75rem;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid py-4">
        <h5 class="fw-bold mb-4"><i class="fas fa-chart-line text-primary me-2"></i> ภาพรวมผลการดำเนินงาน (Executive
            Dashboard)</h5>

        <div class="row mb-4">
            <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-capitalize font-weight-bold">ผู้เข้าร่วมโครงการ</p>
                                    <h4 class="font-weight-bolder mb-0 text-primary">
                                        {{ number_format($totalUsers) }} <span
                                            class="text-sm font-weight-normal text-secondary">คน</span>
                                    </h4>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div
                                    class="icon-shape bg-gradient-primary shadow text-center border-radius-md d-flex align-items-center justify-content-center">
                                    <i class="fas fa-users text-lg text-white opacity-10"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-capitalize font-weight-bold">ขยะที่ลดได้สะสม</p>
                                    <h4 class="font-weight-bolder mb-0 text-success">
                                        {{ number_format($totalWasteSaved, 1) }} <span
                                            class="text-sm font-weight-normal text-secondary">กก.</span>
                                    </h4>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div
                                    class="icon-shape bg-gradient-success shadow text-center border-radius-md d-flex align-items-center justify-content-center">
                                    <i class="fas fa-leaf text-lg text-white opacity-10"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-capitalize font-weight-bold">ลดก๊าซเรือนกระจก</p>
                                    <h4 class="font-weight-bolder mb-0 text-info">
                                        {{ number_format($totalCarbonSaved, 1) }} <span
                                            class="text-sm font-weight-normal text-secondary">kgCO₂e</span>
                                    </h4>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div
                                    class="icon-shape bg-gradient-info shadow text-center border-radius-md d-flex align-items-center justify-content-center">
                                    <i class="fas fa-globe-asia text-lg text-white opacity-10"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-sm-6">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-capitalize font-weight-bold">มูลค่าหมุนเวียน</p>
                                    <h4 class="font-weight-bolder mb-0 text-warning">
                                        ฿{{ number_format($totalRevenue, 2) }}
                                    </h4>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div
                                    class="icon-shape bg-gradient-warning shadow text-center border-radius-md d-flex align-items-center justify-content-center">
                                    <i class="fas fa-coins text-lg text-white opacity-10"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-lg-8 mb-lg-0 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header pb-0 p-3 bg-white">
                        <h6 class="mb-0">สถิติการจัดเก็บขยะเศษอาหาร (6 เดือนล่าสุด)</h6>
                    </div>
                    <div class="card-body p-3">
                        <div class="chart">
                            <canvas id="bar-chart" class="chart-canvas" height="300"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header pb-0 p-3 bg-white">
                        <h6 class="mb-0">สัดส่วนกิจกรรมของสมาชิก</h6>
                    </div>
                    <div class="card-body p-3 d-flex align-items-center justify-content-center">
                        @if(empty($pieData))
                            <div class="text-center text-muted py-5">
                                <i class="fas fa-chart-pie fa-3x mb-3 opacity-50"></i>
                                <p class="mb-0">ยังไม่มีข้อมูลกิจกรรม</p>
                            </div>
                        @else
                            <div class="chart w-100">
                                <canvas id="doughnut-chart" class="chart-canvas" height="250"></canvas>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-12 mt-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header pb-0 p-3 bg-white">
                    <h6 class="mb-0"><i class="fas fa-chart-bar text-info me-2"></i> ความต่อเนื่องในการทิ้งขยะของสมาชิก
                        (นับจากวันเริ่มหมัก)</h6>
                    <p class="text-xs text-secondary mb-0">แสดงจำนวนสมาชิก (คน) ที่นำขยะมาทิ้งในแต่ละวันของรอบการหมัก</p>
                </div>
                <div class="card-body p-3">
                    <div class="chart">
                        <canvas id="engagement-chart" class="chart-canvas" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header pb-0 bg-white">
                        <h6 class="mb-0"><i class="fas fa-trophy text-warning me-2"></i> 5 อันดับสมาชิกดีเด่น (ลดขยะสูงสุด)
                        </h6>
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            อันดับ</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            ชื่อสมาชิก</th>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            ปริมาณขยะสะสม (กก.)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($topPerformers as $index => $performer)
                                        <tr>
                                            <td>
                                                <div class="d-flex px-3 py-1">
                                                    <h6 class="mb-0 text-sm">#{{ $index + 1 }}</h6>
                                                </div>
                                            </td>
                                            <td>
                                                <p class="text-sm font-weight-bold mb-0">{{ $performer->user_name }}</p>
                                            </td>
                                            <td class="align-middle text-center text-sm">
                                                <span
                                                    class="badge bg-gradient-success">{{ number_format($performer->total_waste, 2) }}
                                                    kg</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // --- 1. กราฟแท่ง (Bar Chart) ---
            var ctxBar = document.getElementById("bar-chart").getContext("2d");
            new Chart(ctxBar, {
                type: "bar",
                data: {
                    labels: {!! json_encode($months) !!},
                    datasets: [{
                        label: "ปริมาณขยะ (กก.)",
                        data: {!! json_encode($wasteData) !!},
                        backgroundColor: "#2dce89", // สีเขียว Success
                        borderRadius: 4,
                        maxBarThickness: 30
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { drawBorder: false, display: true, drawOnChartArea: true, drawTicks: false, borderDash: [5, 5] },
                            ticks: { padding: 10 }
                        },
                        x: {
                            grid: { drawBorder: false, display: false, drawOnChartArea: false, drawTicks: false },
                            ticks: { padding: 10 }
                        }
                    }
                }
            });

            // --- 2. กราฟโดนัท (Doughnut Chart) ---
            var ctxDoughnut = document.getElementById("doughnut-chart").getContext("2d");
            new Chart(ctxDoughnut, {
                type: "doughnut",
                data: {
                    labels: {!! json_encode($pieLabels) !!},
                    datasets: [{
                        data: {!! json_encode($pieData) !!},
                        backgroundColor: ["#11cdef", "#f5365c", "#fb6340"], // สีฟ้า, แดง, ส้ม
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom' }
                    }
                }
            });
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // --- กราฟ Engagement วันที่ 1-7 ---
            var ctxEngage = document.getElementById("engagement-chart").getContext("2d");
            new Chart(ctxEngage, {
                type: "bar",
                data: {
                    labels: {!! json_encode($engagementLabels ?? []) !!},
                    datasets: [{
                        label: "จำนวนผู้เข้าร่วม (คน)",
                        data: {!! json_encode($engagementValues ?? []) !!},
                        backgroundColor: [
                            "#5e72e4", "#5e72e4", "#5e72e4", "#5e72e4",
                            "#5e72e4", "#5e72e4", "#5e72e4", "#f5365c" // ให้แท่ง "เกิน 7 วัน" เป็นสีแดงเพื่อความโดดเด่น
                        ],
                        borderRadius: 4,
                        maxBarThickness: 40
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: { display: true, text: 'จำนวนสมาชิก (คน)' },
                            ticks: { stepSize: 1 } // ให้แกน Y นับทีละ 1 คน
                        },
                        x: {
                            grid: { display: false }
                        }
                    }
                }
            });
        });
    </script>
@endsection
