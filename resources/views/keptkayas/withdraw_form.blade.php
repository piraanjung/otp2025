@extends('layouts.keptkaya')

@section('content')
<div class="container py-4">
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <h4 class="fw-bold mb-3"><i class="bi bi-cash-coin text-success"></i> ถอนเงินสด</h4>
            <div class="bg-light p-3 rounded-3 mb-3">
                <small class="text-muted d-block">ยอดเงินที่ถอนได้</small>
                <h3 class="fw-bold text-primary mb-0">{{ number_format($account->balance, 2) }} บาท</h3>
            </div>

            <form action="{{ route('keptkayas.withdraw.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-bold">ระบุจำนวนเงิน (บาท)</label>
                    <input type="number" name="amount" class="form-control form-control-lg rounded-pill"
                           placeholder="0.00" min="20" max="{{ $account->balance }}" required>
                </div>

                <div class="alert alert-info border-0 rounded-4 mb-4">
                    <div class="d-flex">
                        <i class="bi bi-calendar-event-fill me-2 fs-5"></i>
                        <div>
                            <strong>รอบการรับเงินสด:</strong><br>
                            รายการนี้สามารถรับเงินได้ใน <strong>วันอังคารที่ {{ $payoutDate }}</strong>
                        </div>
                    </div>
                </div>

                <div class="form-check form-switch mb-4">
                    <input class="form-check-input" type="checkbox" id="receiveInstead" name="is_proxy">
                    <label class="form-check-label" for="receiveInstead">ให้ผู้อื่นมารับเงินแทน</label>
                </div>

                <div id="proxyNameInput" style="display: none;" class="mb-4">
                    <label class="form-label small text-muted">ชื่อ-นามสกุล ผู้รับแทน</label>
                    <input type="text" name="proxy_name" class="form-control rounded-pill">
                </div>

                <button type="submit" class="btn btn-success w-100 py-3 rounded-pill fw-bold fs-5 shadow-sm">
                    ยืนยันคำขอถอนเงิน
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById('receiveInstead').addEventListener('change', function() {
        document.getElementById('proxyNameInput').style.display = this.checked ? 'block' : 'none';
    });
</script>
@endsection
