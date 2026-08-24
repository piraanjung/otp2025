@extends('layouts.keptkaya') {{-- ใช้ Layout เดิมที่คุณมี --}}

@section('content')
<div class="container py-4">
    <div class="d-flex align-items-center mb-4">
        <a href="javascript:history.back()" class="text-dark me-3"><i class="bi bi-chevron-left fs-4"></i></a>
        <h3 class="mb-0 fw-bold">ประวัติการขายขยะ</h3>
    </div>

    @if($histories->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-clock-history text-muted" style="font-size: 3rem;"></i>
            <p class="mt-3 text-muted">ยังไม่มีประวัติการขายขยะ</p>
        </div>
    @else
        @foreach($histories as $item)
            <div class="card border-0 shadow-sm rounded-4 mb-3">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="fw-bold text-dark">{{ $item->kp_u_trans_no }}</div>
                            <small class="text-muted"><i class="bi bi-calendar3"></i> {{ \Carbon\Carbon::parse($item->transaction_date)->format('d/m/Y') }}</small>
                        </div>
                        <div class="text-end">
                            <span class="badge {{ $item->status == 1 ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning' }} rounded-pill mb-1">
                                {{ $item->status == 1 ? 'สำเร็จ' : 'รอดำเนินการ' }}
                            </span>
                            <div class="fw-bold text-primary">+{{ number_format($item->total_amount, 2) }} บาท</div>
                        </div>
                    </div>

                    <hr class="my-2 opacity-50">

                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            <i class="bi bi-box-seam"></i> {{ number_format($item->total_weight, 2) }} kg
                            <span class="mx-1">|</span>
                            <i class="bi bi-star-fill text-warning"></i> {{ $item->total_points }} แต้ม
                        </small>
                        {{-- ปุ่มดูใบเสร็จย้อนหลัง --}}
                        <a href="{{ route('keptkayas.purchase.receipt', $item->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                            ดูใบเสร็จ
                        </a>
                    </div>
                </div>
            </div>
        @endforeach

        <div class="d-flex justify-content-center mt-4">
            {{ $histories->links() }}
        </div>
    @endif
</div>
@endsection
