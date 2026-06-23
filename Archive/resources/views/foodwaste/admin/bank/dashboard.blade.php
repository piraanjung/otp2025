@extends('layouts.foodwaste')

@section('content')
<div class="container py-4">
    <h4 class="fw-bold mb-4 text-primary"><i class="bi bi-graph-up-arrow me-2"></i> สรุปภาพรวมธนาคารเศษอาหาร</h4>

    <!-- ส่วนที่ 1: Card สรุปตัวเลข (Dashboard Stats) -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-success text-white">
                <div class="card-body">
                    <h6 class="small fw-bold opacity-75">ขยะที่เปลี่ยนเป็นปุ๋ยแล้ว</h6>
                    <h2 class="fw-bold mb-0">{{ number_format($totalWeight, 1) }} <span class="fs-6">กก.</span></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-primary text-white">
                <div class="card-body">
                    <h6 class="small fw-bold opacity-75">รายได้หมุนเวียน (ยอดขายปุ๋ย)</h6>
                    <h2 class="fw-bold mb-0">฿{{ number_format($totalRevenue, 2) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-warning text-dark">
                <div class="card-body">
                    <h6 class="small fw-bold opacity-75">พอยต์ที่หมุนเวียนในระบบ</h6>
                    <h2 class="fw-bold mb-0">{{ number_format($totalPointsIssued) }} <span class="fs-6">KP</span></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-danger text-white">
                <div class="card-body">
                    <h6 class="small fw-bold opacity-75">จำนวนการแลกของรางวัล</h6>
                    <h2 class="fw-bold mb-0">{{ number_format($totalRedeems) }} <span class="fs-6">ครั้ง</span></h2>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- ส่วนที่ 2: ตารางธุรกรรมล่าสุด -->
        <div class="col-md-8">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="fw-bold mb-0"><i class="bi bi-clock-history me-2"></i> รายการล่าสุด (Transactions)</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr class="small">
                                    <th class="ps-4">สมาชิก</th>
                                    <th>รายการ</th>
                                    <th class="text-end pe-4">พอยต์/เงิน</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentTransactions as $txn)
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold text-dark">{{ $txn->preference->user->firstname ?? 'User' }}</div>
                                        <div class="small text-muted">{{ $txn->created_at->diffForHumans() }}</div>
                                    </td>
                                    <td>
                                        <span class="small">{{ $txn->note }}</span>
                                    </td>
                                    <td class="text-end pe-4 fw-bold">
                                        @if($txn->points != 0)
                                            <span class="{{ $txn->points > 0 ? 'text-success' : 'text-danger' }}">
                                                {{ $txn->points > 0 ? '+' : '' }}{{ number_format($txn->points) }} KP
                                            </span>
                                        @else
                                            <span class="text-primary">฿{{ number_format($txn->amount, 2) }}</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- ส่วนที่ 3: เมนูทางลัด (Quick Links) -->
        <div class="col-md-4">
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">จัดการด่วน</h6>
                    <a href="{{ route('foodwaste.admin.fw_bank.index') }}" class="btn btn-outline-primary w-100 rounded-pill mb-2">
                        <i class="bi bi-people me-2"></i> จัดการแต้มสมาชิก
                    </a>
                    <a href="{{ route('foodwaste.admin.members_waste.index') }}" class="btn btn-outline-success w-100 rounded-pill mb-2">
                        <i class="bi bi-check-circle me-2"></i> ไปหน้า Verify ขยะ
                    </a>
                </div>
            </div>

            <div class="alert alert-info border-0 rounded-4">
                <h6 class="fw-bold"><i class="bi bi-info-circle me-2"></i> สัดส่วนการแบ่งปัน</h6>
                <p class="small mb-0">ในอนาคตคุณสามารถใช้ยอด <strong>รายได้หมุนเวียน (฿)</strong> ด้านบน มากดคำนวณปันผลคืนสมาชิกตามน้ำหนักขยะที่ส่งมาได้ทันที</p>
            </div>
        </div>
    </div>
</div>
@endsection
