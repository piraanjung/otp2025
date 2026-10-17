@extends('layouts.keptkaya')

@section('content')
<div class="container py-4">
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            @if (session('success'))
                <div class="alert alert-success rounded-4 border-0 mb-4">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger rounded-4 border-0 mb-4">{{ session('error') }}</div>
            @endif
            <h4 class="fw-bold mb-4 text-primary"><i class="bi bi-arrow-left-right"></i> โอนแต้มสะสม</h4>

            <div class="bg-primary-subtle p-3 rounded-3 mb-4 text-center">
                <small class="text-muted d-block">แต้มที่คุณมี</small>
                <h2 class="fw-bold text-primary mb-0">{{ number_format($recycleTotalPoints, 0) }} แต้ม</h2>
            </div>

            <form action="{{ route('keptkayas.transfer_points.store') }}" method="POST">
                @csrf
                <input type="hidden" name="pref_id" value="{{ $pref_id }}">
                <div class="mb-3">
                    <label class="form-label fw-bold">เบอร์โทรศัพท์ผู้รับ</label>
                    <input type="tel" name="receiver_phone" class="form-control form-control-lg rounded-pill" placeholder="08x-xxx-xxxx" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">จำนวนแต้มที่ต้องการโอน</label>
                    <input type="number" name="amount" class="form-control form-control-lg rounded-pill" placeholder="0" min="1" required>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">บันทึกช่วยจำ (ไม่บังคับ)</label>
                    <input type="text" name="note" class="form-control rounded-pill" placeholder="ขอบคุณสำหรับขยะรีไซเคิล">
                </div>

                <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-bold fs-5 shadow-sm">
                    ยืนยันการโอนแต้ม
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
