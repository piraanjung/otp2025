@extends('layouts.keptkaya') 

@section('content') 
<div class="container py-4"> 
    <div class="card border-0 shadow-sm rounded-4 mb-4"> 
        <div class="card-body p-4 text-center"> 
            <div class="mb-3 text-success"> 
                <i class="fas fa-check-circle text-success" style="font-size: 3rem;"></i> 
            </div> 
            <h4 class="fw-bold mb-1">ส่งคำขอถอนเงินเรียบร้อยแล้ว</h4> 
            <p class="text-muted mb-4 text-sm">กรุณาเก็บบันทึก/พิมพ์ใบร้องขอนี้ เพื่อนำมาแสดงแก่เจ้าหน้าที่การเงินในวันนัดรับเงินสด</p> 

            <div class="bg-light rounded-4 p-4 mb-4 border"> 
                <small class="text-muted d-block font-weight-bold mb-1">รหัสยืนยันการถอนเงิน (Verification Code)</small> 
                <h2 class="fw-bold text-primary mb-0 tracking-wider">{{ $withdraw->verification_code ?? '—' }}</h2> 
            </div> 

            <dl class="row text-start small mb-4 bg-gray-100 p-3 rounded-3"> 
                <dt class="col-6 text-muted">ชื่อสมาชิกเจ้าของบัญชี:</dt> 
                <dd class="col-6 fw-bold text-dark">{{ $withdraw->user->firstname ?? '' }} {{ $withdraw->user->lastname ?? '' }}</dd> 

                <dt class="col-6 text-muted">จำนวนเงินที่ขอถอน:</dt> 
                <dd class="col-6 fw-bold text-success">{{ number_format((float) ($withdraw->amount ?? 0), 2) }} บาท</dd> 

                <dt class="col-6 text-muted">กำหนดวันนัดรับเงินสด:</dt> 
                <dd class="col-6 fw-bold text-info">{{ $withdraw->payout_date ? \Carbon\Carbon::parse($withdraw->payout_date)->format('d/m/Y') : '—' }}</dd> 

                @if($withdraw->is_proxy)
                    <dt class="col-6 text-muted">ผู้ได้รับมอบอำนาจรับแทน:</dt> 
                    <dd class="col-6 fw-bold text-warning">{{ $withdraw->proxy_name }} (บัตร: {{ $withdraw->proxy_id_card ?? '—' }})</dd> 
                @endif
            </dl> 

            <div class="d-flex justify-content-center gap-2 flex-wrap">
                <!-- ปุ่มพิมพ์ใบยืนยันการถอนเงิน -->
                <a href="{{ route('keptkayas.withdraw.print_slip', $withdraw->id) }}" target="_blank" class="btn bg-gradient-info rounded-pill px-4">
                    <i class="fas fa-print me-1"></i> พิมพ์ใบยืนยันการถอนเงิน
                </a>

                <a href="{{ route('keptkayas.withdraw.create', auth()->id()) }}" class="btn btn-outline-secondary rounded-pill"> 
                    ยื่นถอนเงินอีกครั้ง 
                </a> 
                <a href="{{ route('admin.withdraws.index') }}" class="btn btn-primary rounded-pill"> 
                    กลับหน้าหลัก 
                </a> 
            </div>
        </div> 
    </div> 
</div> 
@endsection