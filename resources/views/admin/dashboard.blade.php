@extends('layouts.super-admin')

@section('content')
<div class="container-fluid py-4">
<div class="card border-0 shadow-sm rounded-4 bg-gradient-success text-white">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between">
            <div>
                <h6 class="text-white opacity-8 mb-1">ก๊าซเรือนกระจกที่ลดได้สะสม</h6>
                <h2 class="text-white fw-bold mb-0">{{ number_format($totalCarbonSaved, 2) }} <small class="fs-6">kgCO₂e</small></h2>
            </div>
            <div class="icon icon-shape bg-white shadow text-center border-radius-md">
                <i class="fa-solid fa-leaf text-success opacity-10"></i>
            </div>
        </div>

        <div class="mt-3">
            <div class="progress progress-xs mb-2 bg-white-opacity-2">
                <div class="progress-bar bg-white" role="progressbar" style="width: 70%;"></div>
            </div>
            <p class="text-xs mb-0">เทียบเท่าการปลูกต้นไม้ {{ number_format($totalCarbonSaved / 10, 0) }} ต้น 🌳</p>
        </div>
    </div>
</div>
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header pb-0 p-3 bg-white">
                    <h6 class="mb-0 fw-bold">สถิติการจัดการขยะชุมชน</h6>
                </div>
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-md-7">
                            <div class="chart">
                                <canvas id="waste-donut-chart" class="chart-canvas" height="200"></canvas>
                            </div>
                        </div>
                        <div class="col-md-5 d-flex flex-column justify-content-center">
                            <ul class="list-group list-group-flush">
                                @foreach($wasteStats as $stat)
                                <li class="list-group-item border-0 d-flex justify-content-between ps-0">
                                    <span><i class="fa-solid fa-circle text-xs me-2" style="color: {{ '#' . substr(md5($stat->item_name), 0, 6) }}"></i>{{ $stat->item_name }}</span>
                                    <span class="fw-bold">{{ number_format($stat->total_weight, 1) }} กก.</span>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mt-lg-0 mt-4">
            <div class="card bg-gradient-primary h-100 border-0 shadow-sm rounded-4">
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <div>
                        <h6 class="text-white opacity-8 mb-1">เงินกองทุนสวัสดิการพร้อมจ่าย</h6>
                        <h2 class="text-white fw-bold mb-0">฿ {{ number_format($fundBalance, 2) }}</h2>
                        <hr class="horizontal light my-3">
                        <div class="d-flex justify-content-between text-white text-sm">
                            <span>สะสมทั้งหมด</span>
                            <span>฿ {{ number_format($totalProfit, 0) }}</span>
                        </div>
                        <div class="d-flex justify-content-between text-white text-sm mt-1">
                            <span>ช่วยเหลือไปแล้ว</span>
                            <span>฿ {{ number_format($totalPaid, 0) }}</span>
                        </div>
                    </div>
                    <a href="{{ route('admin.welfare.dashboard') }}" class="btn btn-white btn-sm rounded-pill w-100 mt-4">ดูรายละเอียดกองทุน</a>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-4">
    <div class="col-lg-7">
        <div class="card h-100 border-0 shadow-sm rounded-4">
            <div class="card-header pb-0 p-3 bg-white d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold"><i class="bi bi-bell-fill text-warning me-2"></i>รายการรอดำเนินการ</h6>
                <span class="badge bg-soft-danger text-danger rounded-pill">ต้องจ่ายวันนี้: {{ $todayWithdraws }} รายการ</span>
            </div>
            <div class="card-body p-3">
                <div class="table-responsive">
                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ผู้ขอถอน</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">วันที่นัด</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ยอดเงิน</th>
                                <th class="text-secondary opacity-7"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pendingWithdraws as $draw)
                            <tr>
                                <td>
                                    <div class="d-flex px-2 py-1">
                                        <div class="d-flex flex-column justify-content-center">
                                            <h6 class="mb-0 text-sm">{{ $draw->user->name }}</h6>
                                            @if($draw->is_proxy)
                                                <p class="text-xs text-secondary mb-0">รับแทน: {{ $draw->proxy_name }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <p class="text-xs font-weight-bold mb-0 {{ $draw->payout_date == date('Y-m-d') ? 'text-danger' : '' }}">
                                        {{ \Carbon\Carbon::parse($draw->payout_date)->format('d/m/Y') }}
                                    </p>
                                </td>
                                <td class="align-middle text-center text-sm">
                                    <span class="badge badge-sm bg-gradient-success">฿ {{ number_format($draw->amount, 2) }}</span>
                                </td>
                                <td class="align-middle text-center">
                                    <a href="{{ route('admin.withdraws.index') }}" class="text-secondary font-weight-bold text-xs" data-toggle="tooltip">
                                        ยืนยันรหัส
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">ไม่มีรายการค้างจ่าย</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-5 mt-lg-0 mt-4">
        <div class="card h-100 border-0 shadow-sm rounded-4 bg-white">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3">ทางลัดจัดการระบบ</h6>
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.bulk_sales.create') }}" class="btn btn-outline-primary rounded-4 py-3 text-start">
                        <i class="bi bi-cart-plus-fill fs-4 me-2"></i>
                        <div>
                            <span class="d-block fw-bold">บันทึกการขายขยะ</span>
                            <small class="opacity-75">นำขยะออกจากสต็อกเพื่อเข้ากองทุน</small>
                        </div>
                    </a>
                    <a href="{{ route('admin.withdraws.summary') }}" class="btn btn-outline-dark rounded-4 py-3 text-start">
                        <i class="bi bi-bank fs-4 me-2"></i>
                        <div>
                            <span class="d-block fw-bold">สรุปยอดเบิกเงินสด</span>
                            <small class="opacity-75">ดูยอดที่ต้องเตรียมสำหรับวันอังคารหน้า</small>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<script src="{{ asset('assets/js/plugins/chartjs.min.js') }}"></script>
<script>
    var ctx = document.getElementById("waste-donut-chart").getContext("2d");
    new Chart(ctx, {
        type: "doughnut",
        data: {
            labels: {!! json_encode($wasteStats->pluck('item_name')) !!},
            datasets: [{
                data: {!! json_encode($wasteStats->pluck('total_weight')) !!},
                backgroundColor: {!! json_encode($wasteStats->map(function($s){ return '#' . substr(md5($s->item_name), 0, 6); })) !!},
                borderWidth: 0
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            cutout: '70%',
        }
    });

    // ตัวอย่าง Script เมื่อ Admin เลือกชื่อสมาชิกใน Modal
$('#userSelect').on('change', function() {
    let userId = $(this).val();

    $.get(`/admin/welfare/check-eligibility/${userId}`, function(data) {
        if(data.is_eligible) {
            $('#statusBadge').html('<span class="badge bg-success">ผ่านเกณฑ์สวัสดิการ</span>');
            $('#amountInput').val(data.details.suggested_payout);
        } else {
            $('#statusBadge').html('<span class="badge bg-danger">ไม่ผ่านเกณฑ์ (ส่งขยะไม่ถึงเป้า)</span>');
            $('#amountInput').val(0);
            alert("คำเตือน: สมาชิกรายนี้ยังส่งขยะไม่ครบตามเกณฑ์ที่กำหนด");
        }
    });
});
</script>
@endsection
