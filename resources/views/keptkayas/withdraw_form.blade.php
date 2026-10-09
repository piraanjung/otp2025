@extends('layouts.keptkaya')

@section('nav-current', 'ถอนเงินสด')

@section('content')
<div class="container py-3">
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <h4 class="fw-bold mb-3">
                <i class="fas fa-hand-holding-usd text-success me-2"></i>ยื่นคำขอถอนเงินสด
            </h4>

            <!-- แสดงยอดเงินคงเหลือและยอดถอนได้ตาม Setting -->
            <div class="row g-2 mb-3">
                <div class="col-6">
                    <div class="bg-light p-3 rounded-3 h-100">
                        <small class="text-muted d-block font-weight-bold">ยอดเงินในสมุดทั้งหมด</small>
                        <h5 class="fw-bold text-secondary mb-0">{{ number_format($account->balance, 2) }} บาท</h5>
                    </div>
                </div>
                <div class="col-6">
                    <div class="bg-success bg-opacity-10 p-3 rounded-3 h-100 border border-success border-opacity-25">
                        <small class="text-success fw-bold d-block">ถอนได้สูงสุด (สำรอง {{ number_format($minReserve) }} บ.)</small>
                        <h4 class="fw-bold text-success mb-0">{{ number_format($withdrawableAmount, 2) }} บาท</h4>
                    </div>
                </div>
            </div>

            <!-- แจ้งเตือนกรณีติดสิทธิ์ขายขยะย้อนหลัง -->
            @if (!$hasRecentWasteSale)
                <div class="alert alert-danger text-white border-0 rounded-4 mb-4" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>ไม่สามารถทำรายการถอนเงินได้:</strong> บัญชีของคุณไม่มีการนำขยะมาขายติดต่อกันเกิน {{ $maxInactiveCycles }} รอบบิล กรุณานำขยะมาขายเพื่อปลดล็อกสิทธิ์
                </div>
            @endif

            <!-- แจ้งเตือนสวัสดิการฌาปนกิจ (ถ้าเปิดใช้งาน) -->
            @if ($enableWelfare && $welfareMode !== 'margin_only')
                <div class="alert alert-warning text-dark border-0 rounded-4 mb-4 text-xs">
                    <i class="fas fa-info-circle me-1"></i>
                    <strong>หมายเหตุสวัสดิการ:</strong> บัญชีนี้เข้าร่วมโครงการฌาปนกิจสงเคราะห์ หากมีการจ่ายเงินสงเคราะห์ ระบบอาจหักเงินสมทบจากสมุดบัญชีตามเกณฑ์เทศบาล
                </div>
            @endif

            <form action="{{ route('keptkayas.withdraw.store') }}" method="POST">
                @csrf
                
                <div class="mb-3">
                    <label class="form-label fw-bold">ระบุจำนวนเงินที่ต้องการถอน (บาท)</label>
                    <input type="number" 
                           name="amount" 
                           class="form-control form-control-lg rounded-pill @error('amount') is-invalid @enderror" 
                           placeholder="0.00" 
                           min="{{ $minWithdraw }}" 
                           max="{{ $withdrawableAmount }}" 
                           step="0.01"
                           {{ (!$hasRecentWasteSale || $withdrawableAmount < $minWithdraw) ? 'disabled' : '' }}
                           required>
                    @error('amount')
                        <div class="invalid-feedback ms-3">{{ $message }}</div>
                    @enderror
                    <small class="text-muted ms-2 mt-1 d-block text-xs">
                        * ขั้นต่ำ {{ number_format($minWithdraw, 2) }} บาท และต้องมีเงินสำรองติดบัญชีไม่ต่ำกว่า {{ number_format($minReserve, 2) }} บาท
                    </small>
                </div>

                <!-- รอบรับเงินสด Dynamic ตาม Setting -->
                <div class="alert alert-info border-0 rounded-4 mb-4">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-calendar-check me-3 fs-3 text-info"></i>
                        <div>
                            <strong class="d-block text-sm">รอบการรับเงินสดของเทศบาล/อบต.:</strong>
                            คำขอนี้จะเข้าสู่รอบเสนออนุมัติ และนัดรับเงินสดใน <strong>วัน {{ $payoutDateFormatted }}</strong>
                        </div>
                    </div>
                </div>

                <!-- ตัวเลือกการรับเงินแทน -->
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" 
                           type="checkbox" 
                           id="receiveInstead" 
                           name="is_proxy" 
                           {{ (!$hasRecentWasteSale || $withdrawableAmount < $minWithdraw) ? 'disabled' : '' }}>
                    <label class="form-check-label fw-semibold" for="receiveInstead">ให้ผู้อื่นมารับเงินแทน</label>
                </div>

                <div id="proxyInputs" style="display: none;" class="bg-gray-100 p-3 rounded-4 mb-4">
                    <h6 class="text-xs font-weight-bolder text-primary mb-2">ข้อมูลผู้รับเงินแทน (ระบุวันรับเงินสด)</h6>
                    <div class="mb-2">
                        <label class="form-label small text-muted font-weight-bold mb-1">ชื่อ-นามสกุล ผู้รับแทน *</label>
                        <input type="text" name="proxy_name" class="form-control rounded-pill @error('proxy_name') is-invalid @enderror" placeholder="ระบุชื่อ-นามสกุล">
                        @error('proxy_name')
                            <div class="invalid-feedback ms-3">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-2">
                        <label class="form-label small text-muted font-weight-bold mb-1">เลขบัตรประชาชน ผู้รับแทน *</label>
                        <input type="text" name="proxy_id_card" maxlength="13" class="form-control rounded-pill @error('proxy_id_card') is-invalid @enderror" placeholder="เลขบัตรประจำตัวประชาชน 13 หลัก">
                        @error('proxy_id_card')
                            <div class="invalid-feedback ms-3">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-0">
                        <label class="form-label small text-muted font-weight-bold mb-1">ความสัมพันธ์ (เช่น บุตร, คู่สมรส)</label>
                        <input type="text" name="proxy_relationship" class="form-control rounded-pill" placeholder="เช่น บุตร, ญาติ, ผู้ดูแล">
                    </div>
                </div>

                <button type="submit" 
                        class="btn bg-gradient-success w-100 py-3 rounded-pill fw-bold fs-5 shadow-sm"
                        {{ (!$hasRecentWasteSale || $withdrawableAmount < $minWithdraw) ? 'disabled' : '' }}>
                    <i class="fas fa-paper-plane me-2"></i>ยืนยันส่งคำขอถอนเงิน
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById('receiveInstead').addEventListener('change', function() {
        document.getElementById('proxyInputs').style.display = this.checked ? 'block' : 'none';
    });
</script>
@endsection