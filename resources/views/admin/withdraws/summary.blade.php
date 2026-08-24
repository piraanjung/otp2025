@extends('layouts.super-admin')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold"><i class="bi bi-wallet2 text-primary"></i> สรุปยอดเบิกเงินสด</h2>
        <a href="{{ route('admin.withdraws.index') }}" class="btn btn-outline-secondary rounded-pill">
            <i class="bi bi-list-check"></i> ไปหน้ารายการอนุมัติ
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4 bg-primary text-white mb-4">
        <div class="card-body p-4 text-center">
            <h5 class="opacity-75 mb-2">ยอดเงินที่ต้องเตรียมสำหรับวันอังคารหน้า ({{ \Carbon\Carbon::parse($nextTuesday)->format('d/m/Y') }})</h5>
            <h1 class="display-3 fw-bold mb-0">฿ {{ number_format($urgentAmount, 2) }}</h1>
            <p class="mb-0 fs-5 mt-2">รวมทั้งหมด {{ $summary->where('payout_date', $nextTuesday)->first()->total_requests ?? 0 }} รายการ</p>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-bold">ตารางสรุปรายสัปดาห์</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>วันที่นัดรับเงิน (วันอังคาร)</th>
                        <th class="text-center">จำนวนสมาชิก</th>
                        <th class="text-end">ยอดเงินรวมที่ต้องเตรียม</th>
                        <th class="text-center">สถานะการเตรียม</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($summary as $row)
                    <tr>
                        <td>
                            <span class="fw-bold">{{ \Carbon\Carbon::parse($row->payout_date)->format('d/m/Y') }}</span>
                            @if($row->payout_date == $nextTuesday)
                                <span class="badge bg-danger ms-2">เร็วๆ นี้</span>
                            @endif
                        </td>
                        <td class="text-center">{{ $row->total_requests }} ราย</td>
                        <td class="text-end fw-bold text-primary">{{ number_format($row->total_amount, 2) }}</td>
                        <td class="text-center">
                            @if($row->payout_date <= now()->toDateString())
                                <span class="text-danger small"><i class="bi bi-exclamation-triangle"></i> ถึงกำหนดแล้ว</span>
                            @else
                                <span class="text-muted small">เตรียมเงินล่วงหน้า</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-5 text-muted">ไม่มีรายการนัดรับเงินที่ค้างอยู่</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
