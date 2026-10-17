@extends('layouts.keptkaya')

@section('nav-header', 'Dashboard')
@section('nav-current', 'ภาพรวมการจัดเก็บขยะ')
@section('page-topic', 'Dashboard ความยั่งยืน')
@section('nav-dashboard', 'active')

@section('content')
    {{-- คำนวณยอดรวม --}}
    @php
        $sumWeight = $schoolStats->sum('total_weight');
        $sumCarbon = $schoolStats->sum('total_carbon');
        $sumTrees = floor($sumCarbon / 10);
        // ถ้า Controller ไม่ได้ส่งมา ให้ใช้ค่า default หรือ query สดตรงนี้ (แนะนำให้ส่งจาก Controller ดีกว่า)
        $memberCount = $totalMembers ?? \App\Models\User::count();
    @endphp

    <div class="d-flex justify-content-between align-items-center mb-3">
        <p class="text-sm text-secondary mb-0">สรุปผลกระทบเชิงบวกต่อสิ่งแวดล้อมและเศรษฐกิจหมุนเวียน</p>
        <span class="badge bg-white text-dark shadow-sm">
            <i class="fas fa-calendar-check me-1"></i> ข้อมูลล่าสุด: {{ date('d/m/Y') }}
        </span>
    </div>

    {{-- Mini statistics (6 การ์ด) --}}
    <div class="row">
        <div class="col-xl-4 col-sm-6 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 font-weight-bold">น้ำหนักขยะรีไซเคิลรวม</p>
                                <h5 class="font-weight-bolder mb-0">
                                    {{ number_format($sumWeight, 2) }}
                                    <span class="text-primary text-sm font-weight-bolder">kg</span>
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                                <i class="fas fa-recycle text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-sm-6 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 font-weight-bold">ลดก๊าซเรือนกระจกได้</p>
                                <h5 class="font-weight-bolder mb-0">
                                    {{ number_format($sumCarbon, 2) }}
                                    <span class="text-success text-sm font-weight-bolder">kgCO₂e</span>
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-success shadow text-center border-radius-md">
                                <i class="fas fa-cloud text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-sm-6 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 font-weight-bold">เทียบเท่าการปลูกต้นไม้</p>
                                <h5 class="font-weight-bolder mb-0">
                                    {{ number_format($sumTrees) }}
                                    <span class="text-info text-sm font-weight-bolder">ต้น (โดยประมาณ)</span>
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                <i class="fas fa-tree text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-sm-6 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 font-weight-bold">สมาชิกธนาคารขยะ</p>
                                <h5 class="font-weight-bolder mb-0">
                                    {{ number_format($memberCount) }}
                                    <span class="text-danger text-sm font-weight-bolder">คน</span>
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-danger shadow text-center border-radius-md">
                                <i class="fas fa-users text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-sm-6 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 font-weight-bold">เงินหมุนเวียนสู่ชุมชน</p>
                                <h5 class="font-weight-bolder mb-0">
                                    ฿{{ number_format($economicStats['total_money'], 2) }}
                                    <span class="text-warning text-sm font-weight-bolder">บาท</span>
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-warning shadow text-center border-radius-md">
                                <i class="fas fa-coins text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-sm-6 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 font-weight-bold">แต้มความดีสะสมรวม</p>
                                <h5 class="font-weight-bolder mb-0">
                                    {{ number_format($economicStats['total_points']) }}
                                    <span class="text-secondary text-sm font-weight-bolder">Points</span>
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-secondary shadow text-center border-radius-md">
                                <i class="fas fa-star text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts Area (Trend + Material) --}}
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header pb-0">
                    <h6><i class="fas fa-chart-line text-primary me-1"></i> แนวโน้มการลดคาร์บอน (6 เดือนล่าสุด)</h6>
                </div>
                <div class="card-body p-3">
                    <div class="chart" style="height: 300px;">
                        <canvas id="trendChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header pb-0">
                    <h6><i class="fas fa-chart-bar text-success me-1"></i> สัดส่วนแยกตามประเภทวัสดุ (กิโลกรัม)</h6>
                </div>
                <div class="card-body p-3">
                    <div class="chart" style="height: 300px;">
                        <canvas id="carbonBarChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Hall of Fame & Recent Activity --}}
    <div class="row">
        <div class="col-lg-7 mb-4">
            <div class="card h-100">
                <div class="card-header pb-0">
                    <h6><i class="fas fa-trophy text-warning me-1"></i> Top 5 ฮีโร่รักษ์โลก</h6>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-4">อันดับ</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">ชื่อ-นามสกุล</th>
                                    <th class="text-end text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ช่วยโลกไปแล้ว (kgCO2e)</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ต้นไม้</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topStudents as $index => $student)
                                    <tr>
                                        <td class="ps-4">
                                            @if($index == 0) <span class="text-lg">🥇</span>
                                            @elseif($index == 1) <span class="text-lg">🥈</span>
                                            @elseif($index == 2) <span class="text-lg">🥉</span>
                                            @else <span class="badge badge-sm bg-gradient-secondary">{{ $index + 1 }}</span>
                                            @endif
                                        </td>
                                        <td><h6 class="mb-0 text-sm">{{ $student->firstname }} {{ $student->lastname }}</h6></td>
                                        <td class="text-end text-sm font-weight-bold text-success">
                                            +{{ number_format($student->total_carbon, 2) }}
                                        </td>
                                        <td class="text-center text-sm">
                                            🌳 {{ number_format(floor($student->total_carbon / 10)) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-secondary text-sm py-4">ยังไม่มีข้อมูลการจัดอันดับ</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5 mb-4">
            <div class="card h-100">
                <div class="card-header pb-0">
                    <h6><i class="fas fa-history text-secondary me-1"></i> รายการล่าสุด</h6>
                </div>
                <div class="card-body p-3">
                    <ul class="list-group">
                        @forelse($recentActivities as $activity)
                            <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                                <div class="d-flex align-items-center">
                                    <div class="icon icon-shape icon-sm me-3 bg-gradient-success shadow text-center border-radius-md">
                                        <i class="fas fa-leaf text-white opacity-10"></i>
                                    </div>
                                    <div class="d-flex flex-column">
                                        <h6 class="mb-1 text-dark text-sm">
                                            {{ optional(optional($activity->userWastePreference)->user)->firstname ?? 'Guest' }}
                                        </h6>
                                        <span class="text-xs">
                                            <i class="fas fa-box me-1"></i>{{ number_format($activity->total_weight, 2) }} kg
                                            · {{ $activity->created_at->diffForHumans() }}
                                        </span>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center text-success text-gradient text-sm font-weight-bold">
                                    +{{ number_format($activity->total_carbon_saved, 2) }} kgCO2e
                                </div>
                            </li>
                        @empty
                            <li class="list-group-item border-0 text-center text-secondary text-sm py-4">ยังไม่มีรายการเคลื่อนไหว</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        const ctxBar = document.getElementById('carbonBarChart').getContext('2d');
        new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: @json($chartLabels),
                datasets: [{
                    label: 'คาร์บอน (kgCO2e)',
                    data: @json($chartData),
                    backgroundColor: [
                        'rgba(25, 135, 84, 0.7)',
                        'rgba(13, 110, 253, 0.7)',
                        'rgba(255, 193, 7, 0.7)',
                        'rgba(220, 53, 69, 0.7)',
                        'rgba(102, 16, 242, 0.7)',
                        'rgba(253, 126, 20, 0.7)',
                        'rgba(13, 202, 240, 0.7)',
                        'rgba(33, 37, 41, 0.7)'
                    ],
                    borderColor: [
                        '#198754', '#0d6efd', '#ffc107', '#dc3545', '#6610f2', '#fd7e14', '#0dcaf0', '#212529'
                    ],
                    borderWidth: 1,
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });

        const ctxLine = document.getElementById('trendChart').getContext('2d');
        const trendData = @json($monthlyTrend);
        new Chart(ctxLine, {
            type: 'line',
            data: {
                labels: trendData.map(d => d.month),
                datasets: [{
                    label: 'การลดคาร์บอน (kgCO2e)',
                    data: trendData.map(d => d.total_carbon),
                    borderColor: '#0d6efd',
                    backgroundColor: 'rgba(13, 110, 253, 0.1)',
                    borderWidth: 2,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#0d6efd',
                    pointRadius: 4,
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: true, position: 'top' } },
                scales: {
                    y: { beginAtZero: true, grid: { borderDash: [2, 4] } },
                    x: { grid: { display: false } }
                }
            }
        });
    </script>
@endsection
