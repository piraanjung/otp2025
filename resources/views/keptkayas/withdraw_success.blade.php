@extends('layouts.keptkaya')

@section('content')
<div class="container py-4">
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4 text-center">
            <div class="mb-3 text-success">
                <i class="bi bi-check-circle-fill" style="font-size: 3rem;"></i>
            </div>
            <h4 class="fw-bold mb-3">ส่งคำขอถอนเงินแล้ว</h4>
            <p class="text-muted mb-4">กรุณาเก็บรหัสยืนยันนี้ไว้แสดงเจ้าหน้าที่ในวันรับเงิน</p>

            <div class="bg-light rounded-4 p-4 mb-4">
                <small class="text-muted d-block">รหัสยืนยัน</small>
                <h2 class="fw-bold text-primary mb-0 tracking-wider">{{ $withdraw->verification_code ?? '—' }}</h2>
            </div>

            <dl class="row text-start small mb-4">
                <dt class="col-6 text-muted">จำนวนเงิน</dt>
                <dd class="col-6 fw-semibold">{{ number_format((float) ($withdraw->amount ?? 0), 2) }} บาท</dd>
                <dt class="col-6 text-muted">รับเงินโดยประมาณ</dt>
                <dd class="col-6 fw-semibold">{{ $withdraw->payout_date ? \Carbon\Carbon::parse($withdraw->payout_date)->format('d/m/Y') : '—' }}</dd>
            </dl>

            <a href="{{ route('keptkayas.withdraw.create', auth()->id()) }}" class="btn btn-outline-secondary rounded-pill me-2">
                ถอนเงินอีกครั้ง
            </a>
            <a href="{{ url()->previous() }}" class="btn btn-primary rounded-pill">
                กลับ
            </a>
        </div>
    </div>
</div>
@endsection
