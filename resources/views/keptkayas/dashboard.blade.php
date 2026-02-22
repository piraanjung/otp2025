@extends('layouts.keptkaya')

@section('nav-dashboard', 'active')
@section('nav-header', 'ภาพรวมการจัดเก็บขยะ')

@section('content')
<div class="app-content">
    <div class="container-fluid py-4">
        
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="mb-0 text-white">ระบบบริหารจัดการค่าขยะมูลฝอย</h2>
                <p class="text-white opacity-8">ปีงบประมาณปัจจุบัน: 2567</p>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-uppercase font-weight-bold">ยอดที่ต้องจัดเก็บทั้งปี</p>
                                    <h5 class="font-weight-bolder">1,250,000 ฿</h5>
                                    <p class="mb-0 text-sm">
                                        เป้าหมายปีนี้
                                    </p>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-primary shadow-primary text-center rounded-circle">
                                    <i class="ni ni-money-coins text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-uppercase font-weight-bold">รับชำระแล้ว</p>
                                    <h5 class="font-weight-bolder text-success">900,000 ฿</h5>
                                    <p class="mb-0 text-sm">
                                        <span class="text-success font-weight-bolder">+72%</span> ของเป้าหมาย
                                    </p>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-success shadow-success text-center rounded-circle">
                                    <i class="ni ni-check-bold text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-uppercase font-weight-bold">ค้างชำระ</p>
                                    <h5 class="font-weight-bolder text-danger">350,000 ฿</h5>
                                    <p class="mb-0 text-sm">
                                        ต้องติดตาม
                                    </p>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-danger shadow-danger text-center rounded-circle">
                                    <i class="ni ni-fat-remove text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-sm-6">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-uppercase font-weight-bold">ครัวเรือนในระบบ</p>
                                    <h5 class="font-weight-bolder">2,500 ราย</h5>
                                    <p class="mb-0 text-sm">
                                        <span class="text-success font-weight-bolder">1,800</span> จ่ายแล้ว
                                    </p>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-warning shadow-warning text-center rounded-circle">
                                    <i class="ni ni-building text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-lg-7 mb-lg-0 mb-4">
                <div class="card z-index-2 h-100">
                    <div class="card-header pb-0 pt-3 bg-transparent">
                        <h6 class="text-capitalize">สถิติการชำระเงินแยกตามหมู่บ้าน</h6>
                        <p class="text-sm mb-0">
                            <i class="fa fa-arrow-up text-success"></i>
                            <span class="font-weight-bold">หมู่ 1</span> ชำระสูงสุดในปีนี้
                        </p>
                    </div>
                    <div class="card-body p-3">
                        <div class="chart">
                            <canvas id="chart-zone-payment" class="chart-canvas" height="300"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card z-index-2 h-100">
                    <div class="card-header pb-0 pt-3 bg-transparent">
                        <h6 class="text-capitalize">สัดส่วนลูกหนี้ vs ยอดจัดเก็บ</h6>
                    </div>
                    <div class="card-body p-3">
                         <div class="chart">
                            <canvas id="chart-status-pie" class="chart-canvas" height="300"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-lg-12 mb-lg-0 mb-4">
                <div class="card">
                    <div class="card-header pb-0 p-3">
                        <div class="d-flex justify-content-between">
                            <h6 class="mb-2">สรุปยอดจัดเก็บแยกตามโซน (Top 5)</h6>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ชื่อหมู่บ้าน/โซน</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">จำนวนลูกหนี้</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ยอดรับชำระ (บาท)</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ความคืบหน้า</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="d-flex px-2 py-1">
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-sm">หมู่ที่ 1 บ้านเหนือ</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-xs font-weight-bold text-danger">15 ราย (ค้าง)</span>
                                    </td>
                                    <td class="align-middle text-center text-sm">
                                        <span class="text-xs font-weight-bold">45,000</span>
                                    </td>
                                    <td class="align-middle">
                                        <div class="progress-wrapper w-75 mx-auto">
                                            <div class="progress-info">
                                                <div class="progress-percentage">
                                                    <span class="text-xs font-weight-bold">80%</span>
                                                </div>
                                            </div>
                                            <div class="progress">
                                                <div class="progress-bar bg-gradient-info w-80" role="progressbar" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="d-flex px-2 py-1">
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-sm">หมู่ที่ 2 บ้านใต้</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-xs font-weight-bold text-danger">50 ราย (ค้าง)</span>
                                    </td>
                                    <td class="align-middle text-center text-sm">
                                        <span class="text-xs font-weight-bold">20,000</span>
                                    </td>
                                    <td class="align-middle">
                                        <div class="progress-wrapper w-75 mx-auto">
                                            <div class="progress-info">
                                                <div class="progress-percentage">
                                                    <span class="text-xs font-weight-bold">30%</span>
                                                </div>
                                            </div>
                                            <div class="progress">
                                                <div class="progress-bar bg-gradient-danger w-30" role="progressbar" aria-valuenow="30" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
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
<script src="{{ asset('assets/js/plugins/chartjs.min.js') }}"></script>
<script>
    // --- Chart 1: Bar Chart (Payment by Zone) ---
    var ctx1 = document.getElementById("chart-zone-payment").getContext("2d");

    var gradientStroke1 = ctx1.createLinearGradient(0, 230, 0, 50);
    gradientStroke1.addColorStop(1, 'rgba(94, 114, 228, 0.2)');
    gradientStroke1.addColorStop(0.2, 'rgba(94, 114, 228, 0.0)');
    gradientStroke1.addColorStop(0, 'rgba(94, 114, 228, 0)');

    new Chart(ctx1, {
        type: "bar",
        data: {
            labels: ["หมู่ 1", "หมู่ 2", "หมู่ 3", "หมู่ 4", "หมู่ 5", "หมู่ 6"],
            datasets: [{
                label: "ยอดชำระแล้ว (บาท)",
                tension: 0.4,
                borderWidth: 0,
                borderRadius: 4,
                borderSkipped: false,
                backgroundColor: "#5e72e4",
                data: [45000, 20000, 15000, 50000, 30000, 40000],
                maxBarThickness: 6
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            interaction: {
                intersect: false,
                mode: 'index',
            },
            scales: {
                y: {
                    grid: {
                        drawBorder: false,
                        display: true,
                        drawOnChartArea: true,
                        drawTicks: false,
                        borderDash: [5, 5]
                    },
                    ticks: {
                        display: true,
                        padding: 10,
                        color: '#fbfbfb',
                        font: { size: 11, family: "Open Sans", style: 'normal', lineHeight: 2 },
                    }
                },
                x: {
                    grid: {
                        drawBorder: false,
                        display: false,
                        drawOnChartArea: false,
                        drawTicks: false,
                        borderDash: [5, 5]
                    },
                    ticks: {
                        display: true,
                        color: '#ccc',
                        padding: 20,
                        font: { size: 11, family: "Open Sans", style: 'normal', lineHeight: 2 },
                    }
                },
            },
        },
    });

    // --- Chart 2: Pie/Doughnut Chart (Status) ---
    var ctx2 = document.getElementById("chart-status-pie").getContext("2d");
    new Chart(ctx2, {
        type: "doughnut",
        data: {
            labels: ["ชำระแล้ว", "ค้างชำระ"],
            datasets: [{
                label: "จำนวน (ราย)",
                weight: 9,
                cutout: 60,
                tension: 0.9,
                pointRadius: 2,
                borderWidth: 2,
                backgroundColor: ['#2dce89', '#f5365c'], // สีเขียว, สีแดง
                data: [1800, 700], // ใส่ข้อมูลจริงตรงนี้
                fill: false
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom',
                }
            },
            interaction: {
                intersect: false,
                mode: 'index',
            },
        },
    });
</script>
@endsection