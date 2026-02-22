@extends('layouts.keptkaya')

@section('nav-header', 'Dashboard')
@section('nav-current', 'ภาพรวมการจัดเก็บขยะ')
@section('page-topic', 'ภาพรวมการจัดเก็บขยะ')
@section('nav-dashboard', 'active')
@section('style')
<style>
   #stats-cards-grid .icon-shape i {
    color: #fff;
    opacity: 1;
    top: -18px;
    left: -8px;
    position: relative;
}
 #stats-cards-grid .card .card-body{
    padding: 0.5rem
 }
 </style>
@endsection
@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.13.1/font/bootstrap-icons.min.css" integrity="sha512-t7Few9xlddEmgd3oKZQahkNI4dS6l80+eGEzFQiqtyVYdvcSG2D3Iub77R20BdotfRPA9caaRkg1tyaJiPmO0g==" crossorigin="anonymous" referrerpolicy="no-referrer" />    <div class="container py-4">
        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold text-dark">🌿 Dashboard ความยั่งยืน</h3>
                <p class="text-muted mb-0">สรุปผลกระทบเชิงบวกต่อสิ่งแวดล้อมและเศรษฐกิจหมุนเวียน</p>
            </div>
            <div class="text-end">
                <span class="badge bg-light text-dark border">
                    <i class="bi bi-calendar-check"></i> ข้อมูลล่าสุด: {{ date('d/m/Y') }}
                </span>
            </div>
        </div>

        {{-- คำนวณยอดรวม --}}
        @php
            $sumWeight = $schoolStats->sum('total_weight');
            $sumCarbon = $schoolStats->sum('total_carbon');
            $sumTrees = floor($sumCarbon / 10);
            // ถ้า Controller ไม่ได้ส่งมา ให้ใช้ค่า default หรือ query สดตรงนี้ (แนะนำให้ส่งจาก Controller ดีกว่า)
            $memberCount = $totalMembers ?? \App\Models\User::count();
        @endphp

        {{-- Stats Cards Grid (รวมเป็น 6 การ์ดใน Row เดียว) --}}
        {{-- col-md-4 จะทำให้แถวหนึ่งมี 3 การ์ด (12/4 = 3) พอการ์ดที่ 4 จะขึ้นแถวใหม่เอง --}}
        <div class="row g-3 mb-4" id="stats-cards-grid">
            <div class="col-md-4">
                <div class="card shadow-sm border-0 h-100 border-start border-4 border-primary">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h5 class=" mb-1  text-uppercase  fw-bold">น้ำหนักขยะรีไซเคิลรวม</h5>
                                <h2 class="mb-0 fw-bold text-primary">{{ number_format($sumWeight, 2) }}</h2>
                                <small class="text-dark fw-bold">กิโลกรัม (kg)</small>
                            </div>
                            <div class="icon-shape bg-primary bg-opacity-10 text-primary rounded-3 p-3">
                                <i class="bi bi-recycle fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-sm border-0 h-100 border-start border-4 border-success">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h5 class="mb-1 text-uppercase fw-bold">ลดก๊าซเรือนกระจกได้</h5>
                                <h2 class="mb-0 fw-bold text-success">{{ number_format($sumCarbon, 2) }}</h2>
                                <small class="text-dark fw-bold">kgCO₂e</small>
                            </div>
                            <div class="icon-shape bg-success bg-opacity-10 text-success rounded-3 p-3">
                                <i class="bi bi-cloud-check-fill fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-sm border-0 h-100 border-start border-4 border-info">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h5 class="mb-1 text-uppercase fw-bold">เทียบเท่าการปลูกต้นไม้</h5>
                                <h2 class="mb-0 fw-bold text-info">{{ number_format($sumTrees) }}</h2>
                                <small class="text-dark fw-bold">ต้น (โดยประมาณ)</small>
                            </div>
                            <div class="icon-shape bg-info bg-opacity-10 text-info rounded-3 p-3">
                                <i class="bi bi-tree-fill fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-sm border-0 h-100 border-start border-4 border-danger">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h5 class="mb-1 text-uppercase fw-bold">สมาชิกธนาคารขยะ</h5>
                                <h2 class="mb-0 fw-bold text-danger">{{ number_format($memberCount) }}</h2>
                                <small class="text-dark fw-bold">คน (Active Users)</small>
                            </div>
                            <div class="icon-shape bg-danger bg-opacity-10 text-danger rounded-3 p-3">
                                <i class="bi bi-people-fill fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-sm border-0 h-100 border-start border-4 border-warning">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h5 class="mb-1 text-uppercase fw-bold">เงินหมุนเวียนสู่ชุมชน</h5>
                                <h2 class="mb-0 fw-bold text-warning">฿{{ number_format($economicStats['total_money'], 2) }}
                                </h2>
                                <small class="text-dark fw-bold">บาท</small>
                            </div>
                            <div class="icon-shape bg-warning bg-opacity-10 text-warning rounded-3 p-3">
                                <i class="bi bi-cash-coin fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-sm border-0 h-100 border-start border-4 border-secondary">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h5 class="mb-1 text-uppercase fw-bold">แต้มความดีสะสมรวม</h5>
                                <h2 class="mb-0 fw-bold text-secondary">{{ number_format($economicStats['total_points']) }}
                                </h2>
                                <small class="text-dark fw-bold">Points</small>
                            </div>
                            <div class="icon-shape bg-secondary bg-opacity-10 text-secondary rounded-3 p-3">
                                <i class="bi bi-star-fill fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        {{-- จบ Grid 6 การ์ด --}}


        {{-- Row 3: Charts Area (Trend + Material) --}}
        <div class="row mb-4">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="card-title fw-bold text-dark mb-4">
                            <i class="bi bi-graph-up-arrow text-primary"></i> แนวโน้มการลดคาร์บอน (6 เดือนล่าสุด)
                        </h5>
                        <div style="height: 300px;">
                            <canvas id="trendChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="card-title fw-bold text-success mb-4">
                            <i class="bi bi-bar-chart-fill"></i> สัดส่วนแยกตามประเภทวัสดุ (กิโลกรัม)
                        </h5>
                        <div style="height: 300px;">
                            <canvas id="carbonBarChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Row 4: Hall of Fame & Recent Activity --}}
        <div class="row">
            <div class="col-lg-7 mb-4 mb-lg-0">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-white py-3 border-0">
                        <h5 class="card-title fw-bold text-warning mb-0">
                            <i class="bi bi-trophy-fill"></i> Top 5 ฮีโร่รักษ์โลก
                        </h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">อันดับ</th>
                                    <th>ชื่อ-นามสกุล</th>
                                    <th class="text-end pe-4">ช่วยโลกไปแล้ว (kgCO2e)</th>
                                    <th class="text-center">ต้นไม้</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topStudents as $index => $student)
                                    <tr>
                                        <td class="ps-4">
                                            @if($index == 0) <span class="fs-5">🥇</span>
                                            @elseif($index == 1) <span class="fs-5">🥈</span>
                                            @elseif($index == 2) <span class="fs-5">🥉</span>
                                            @else <span class="badge bg-light text-dark border rounded-circle"
                                                style="width:25px; height:25px;">{{ $index + 1 }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $student->firstname }} {{ $student->lastname }}</td>
                                        <td class="text-end pe-4 fw-bold text-success">
                                            +{{ number_format($student->total_carbon, 2) }}
                                        </td>
                                        <td class="text-center small">
                                            🌳 {{ number_format(floor($student->total_carbon / 10)) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">ยังไม่มีข้อมูลการจัดอันดับ</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-white py-3 border-0">
                        <h5 class="card-title fw-bold text-secondary mb-0">
                            <i class="bi bi-clock-history"></i> รายการล่าสุด
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            @forelse($recentActivities as $activity)
                                <li class="list-group-item px-4 py-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="fw-bold d-block text-dark">
                                                {{ optional(optional($activity->userWastePreference)->user)->firstname ?? 'Guest' }}
                                            </span>
                                            <small class="text-muted">
                                                <i class="bi bi-box-seam"></i> {{ number_format($activity->total_weight, 2) }}
                                                kg
                                            </small>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge bg-success bg-opacity-10 text-black mb-1">
                                                +{{ number_format($activity->total_carbon_saved, 2) }} kgCO2e
                                            </span>
                                            <br>
                                            <small class="text-muted" style="font-size: 0.75rem;">
                                                {{ $activity->created_at->diffForHumans() }}
                                            </small>
                                        </div>
                                    </div>
                                </li>
                            @empty
                                <li class="list-group-item text-center text-muted py-4">ยังไม่มีรายการเคลื่อนไหว</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Scripts (ส่วนนี้เหมือนเดิม ใช้ต่อท้ายได้เลย) --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // ... Script กราฟเดิมของคุณ ...
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
