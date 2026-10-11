@extends('layouts.keptkaya')

@section('nav-current', 'รวบรวมเสนออนุมัติถอนเงิน')

@if($show_div == "histoty_batches")
    @section('nav-keptkayas.batches.history', 'active')
@elseif($show_div == "current_batches")
    @section('nav-keptkayas.batches', 'active')
@endif

@section('content')
<div class="container-fluid py-2">
    {{-- <div class="col-md-6 text-end mt-3 mt-md-0"> --}}
    <!-- ปุ่มคีย์ถอนเงิน On-site สำหรับเจ้าหน้าที่ยื่นแทนสมาชิก -->
    {{-- <a href="{{ route('keptkayas.withdraw.create') }}" class="btn bg-gradient-primary me-2 mb-0">
        <i class="fas fa-plus-circle me-1"></i> ถอนเงิน
    </a> --}}
    
    <!-- ปุ่มสแกน QR Code จ่ายเงินสดวันอังคาร -->
    {{-- <button type="button" class="btn bg-gradient-success mb-0" data-bs-toggle="modal" data-bs-target="#scanVerifyModal">
        <i class="fas fa-qrcode me-1"></i> จ่ายเงินสด (สแกนรหัส)
    </button> --}}
{{-- </div> --}}
    <!-- Card 1: สรุปยอดรอตัดรอบวันศุกร์ -->
    @if($show_div == "current_batches")
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="font-weight-bolder text-dark mb-1">
                        <i class="fas fa-boxes text-primary me-2"></i>คำขอถอนเงินรอรวบรวมประจำรอบ
                    </h5>
                    <p class="text-xs text-secondary mb-0">รายการถอนเงินจากสมาชิก (ทั้ง On-site และ App) ที่รอการตัดรอบวัน{{ $cutoffDay }}</p>
                </div>
                <div>
                    <form action="{{ route('keptkayas.batches.create') }}" method="POST" onsubmit="return confirm('ยืนยันการรวบรวมคำขอเพื่อตัดรอบเสนออนุมัติหรือไม่?');">
                        @csrf
                        <button type="submit" class="btn bg-gradient-primary mb-0" {{ $pendingCount == 0 ? 'disabled' : '' }}>
                            <i class="fas fa-file-invoice-dollar me-1"></i> รวบรวมตัดรอบเสนออนุมัติ ({{ $pendingCount }} รายการ)
                        </button>
                    </form>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <div class="bg-gray-100 p-3 rounded-3">
                        <small class="text-xs text-muted font-weight-bold d-block">จำนวนคำขอรอตัดรอบ</small>
                        <h4 class="font-weight-bolder text-dark mb-0">{{ number_format($pendingCount) }} รายการ</h4>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="bg-primary bg-opacity-10 p-3 rounded-3 border border-primary border-opacity-25">
                        <small class="text-xs text-white font-weight-bold d-block">ยอดเงินสดรวมที่ต้องเสนอขอเบิก</small>
                        <h4 class="font-weight-bolder text-white mb-0">{{ number_format($pendingTotalAmount, 2) }} บาท</h4>
                    </div>
                </div>
            </div>

            <!-- ตารางแสดงรายการรอตัดรอบ -->
            <div class="table-responsive p-0">
                <table class="table align-items-center mb-0 text-sm">
                    <thead>
                        <tr>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">วันที่ยื่นเรื่อง</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">สมาชิก</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">ผู้รับเงิน</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">จำนวนเงิน (บาท)</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">สถานะ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pendingRequests as $req)
                            <tr>
                                <td class="ps-3"><span class="text-xs font-weight-bold">{{ $req->created_at->format('d/m/Y H:i') }}</span></td>
                                <td><span class="text-xs font-weight-bold">{{ $req->user->firstname ?? '' }} {{ $req->user->lastname ?? '' }}</span></td>
                                <td>
                                    @if($req->is_proxy)
                                        <span class="badge bg-gradient-warning text-xxs">รับแทน: {{ $req->proxy_name }}</span>
                                    @else
                                        <span class="text-xs text-secondary">รับด้วยตนเอง</span>
                                    @endif
                                </td>
                                <td class="text-center"><span class="text-xs font-weight-bold text-dark">{{ number_format($req->amount, 2) }}</span></td>
                                <td class="text-center"><span class="badge bg-gradient-secondary text-xxs">รอตัดรอบ</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-xs text-muted">ไม่มีรายการถอนเงินค้างสะสมในขณะนี้</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    <!-- Card 2: ประวัติชุดเบิกถอนเงิน (Batch History) -->
    @if($show_div == "histoty_batches")
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-white pb-0">
            <h6 class="font-weight-bolder text-dark mb-0">ประวัติชุดการเสนออนุมัติเบิกถอนเงิน (Batches)</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive p-0">
                <table class="table align-items-center mb-0 text-sm">
                    <thead>
                        <tr>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">เลขที่ Batch</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">วันที่ตัดรอบ</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">กำหนดวันจ่ายเงิน</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">รายการ</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ยอดรวม (บาท)</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">สถานะ</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($batches as $b)
                            <tr>
                                <td class="ps-3"><span class="text-xs font-weight-bold text-primary">{{ $b->batch_no }}</span></td>
                                <td><span class="text-xs font-weight-bold">{{ \Carbon\Carbon::parse($b->cutoff_date)->format('d/m/Y') }}</span></td>
                                <td><span class="text-xs font-weight-bold text-info">{{ \Carbon\Carbon::parse($b->payout_date)->format('d/m/Y') }}</span></td>
                                <td class="text-center"><span class="text-xs font-weight-bold">{{ $b->total_requests }}</span></td>
                                <td class="text-center"><span class="text-xs font-weight-bold text-dark">{{ number_format($b->total_amount, 2) }}</span></td>
                                <td class="text-center">
                                    @if($b->status == 'in_review')
                                        <span class="badge bg-gradient-info text-xxs">เสนออนุมัติ</span>
                                    @elseif($b->status == 'approved')
                                        <span class="badge bg-gradient-success text-xxs">อนุมัติแล้ว</span>
                                    @elseif($b->status == 'completed')
                                        <span class="badge bg-gradient-secondary text-xxs">จ่ายเงินเรียบร้อย</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('keptkayas.batches.show', $b->id) }}" class="btn btn-link text-dark text-gradient px-2 mb-0">
                                        <i class="fas fa-eye me-1"></i> ดูรายละเอียด
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-xs text-muted">ยังไม่มีประวัติการรวบรวม Batch</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $batches->links() }}
            </div>
        </div>
    </div>
    @endif
</div>
@endsection