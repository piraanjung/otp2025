@extends('layouts.keptkaya')

@section('nav-current', 'ถอนเงินสด')

@section('style')
<style>
    .select2-container--default .select2-selection--single {
        height: 48px !important;
        border-radius: 50rem !important;
        border: 1px solid #d2d6da !important;
        padding: 8px 16px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 30px !important;
        color: #495057 !important;
        font-weight: 600 !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 46px !important;
        right: 12px !important;
    }
    .select2-dropdown {
        border-radius: 1rem !important;
        border: 1px solid #d2d6da !important;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
        z-index: 9999 !important;
    }
</style>
@endsection

@section('content')
<div class="container py-3">
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <h4 class="fw-bold mb-3">
                <i class="fas fa-hand-holding-usd text-success me-2"></i>ยื่นคำขอถอนเงินสด
            </h4>

            <!-- ส่วนค้นหารายชื่อสมาชิก (แสดงเฉพาะเมื่อเป็น เจ้าหน้าที่/Staff) -->
            @if($isStaff)
                <div class="card bg-gray-100 border-0 p-3 rounded-4 mb-4">
                    <label class="form-label font-weight-bolder text-primary mb-2">
                        <i class="fas fa-search me-1"></i> พิมพ์ค้นหารายชื่อสมาชิก / เลขบัตรประชาชน (สำหรับเจ้าหน้าที่บริการ On-site)
                    </label>
                    <div class="row g-2">
                        <div class="col-12">
                            <select id="memberSelect" class="form-select form-select-lg rounded-pill text-sm select2-member" style="width: 100%;">
                                <option value="">🔍 พิมพ์ชื่อ-นามสกุล หรือ เลขบัตรประชาชน เพื่อค้นหา...</option>
                                @foreach($members as $m)
                                    <option value="{{ $m->id }}" 
                                            data-card="{{ $m->id_card ?? '' }}" 
                                            {{ $targetUserId == $m->id ? 'selected' : '' }}>
                                        {{ $m->firstname }} {{ $m->lastname }} {{ $m->id_card ? '(บัตร: '.$m->id_card.')' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            @endif

            <!-- แจ้งเตือนกรณีเป็น Staff แต่ยังไม่ได้เลือกสมาชิก หรือสมาชิกยังไม่มีบัญชี -->
            @if(!$account)
                <div class="alert alert-warning text-dark border-0 rounded-4 text-center py-4 mb-0">
                    <i class="fas fa-exclamation-circle fa-2x mb-2 d-block text-warning"></i>
                    @if($isStaff && !$targetUserId)
                        <strong>กรุณาพิมพ์ค้นหาและเลือกรายชื่อสมาชิกด้านบน</strong> เพื่อทำรายการถอนเงินแทนสมาชิก
                    @else
                        <strong>ไม่พบข้อมูลบัญชีธนาคารขยะของสมาชิกรายนี้</strong> กรุณาตรวจสอบการเปิดบัญชีก่อนทำรายการ
                    @endif
                </div>
            @else
                <!-- แสดงยอดเงินคงเหลือและยอดถอนได้ตาม Setting -->
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <div class="bg-light p-3 rounded-3 h-100">
                            <small class="text-muted d-block font-weight-bold">ยอดเงินในสมุดทั้งหมด</small>
                            <h5 class="fw-bold text-secondary mb-0">{{ number_format($account->balance ?? 0, 2) }} บาท</h5>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="bg-success bg-opacity-10 p-3 rounded-3 h-100 border border-success border-opacity-25">
                            <small class="text-success fw-bold d-block">ถอนได้สูงสุด (สำรอง {{ number_format($minReserve) }} บ.)</small>
                            <h4 class="fw-bold text-success mb-0">{{ number_format($withdrawableAmount ?? 0, 2) }} บาท</h4>
                        </div>
                    </div>
                </div>

                <!-- แจ้งเตือนกรณีติดสิทธิ์ขายขยะย้อนหลัง -->
                @if (!$hasRecentWasteSale)
                    <div class="alert alert-danger text-white border-0 rounded-4 mb-4" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>ไม่สามารถทำรายการถอนเงินได้:</strong> บัญชีนี้ไม่มีการนำขยะมาขายติดต่อกันเกิน {{ $maxInactiveCycles }} รอบบิล กรุณานำขยะมาขายเพื่อปลดล็อกสิทธิ์
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
                    <input type="hidden" name="target_user_id" value="{{ $targetUserId }}">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">ระบุจำนวนเงินที่ต้องการถอน (บาท)</label>
                        <input type="number" name="amount" class="form-control form-control-lg rounded-pill @error('amount') is-invalid @enderror" placeholder="0.00" min="{{ $minWithdraw }}" max="{{ $withdrawableAmount }}" step="0.01" {{ (!$hasRecentWasteSale || $withdrawableAmount < $minWithdraw) ? 'disabled' : '' }} required>
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
                                <strong class="d-block text-sm">รอบการรับเงินสดของเทศบาล/อบต.:</strong> คำขอนี้จะเข้าสู่รอบเสนออนุมัติ และนัดรับเงินสดใน <strong>วัน {{ $payoutDateFormatted }}</strong>
                            </div>
                        </div>
                    </div>

                    <!-- ตัวเลือกการรับเงินแทน -->
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="receiveInstead" name="is_proxy" {{ (!$hasRecentWasteSale || $withdrawableAmount < $minWithdraw) ? 'disabled' : '' }}>
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

                    <button type="submit" class="btn bg-gradient-success w-100 py-3 rounded-pill fw-bold fs-5 shadow-sm" {{ (!$hasRecentWasteSale || $withdrawableAmount < $minWithdraw) ? 'disabled' : '' }}>
                        <i class="fas fa-paper-plane me-2"></i>ยืนยันส่งคำขอถอนเงิน
                    </button>
                </form>
            @endif

        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    $(document).ready(function() {
        // เช็กและสั่งเปิดใช้งาน Select2
        if ($('.select2-member').length > 0) {
            $('.select2-member').select2({
                placeholder: "🔍 พิมพ์ชื่อ-นามสกุล หรือ เลขบัตรประชาชน เพื่อค้นหา...",
                allowClear: true,
                language: {
                    noResults: function() {
                        return "ไม่พบข้อมูลสมาชิกที่ค้นหา";
                    }
                }
            });

            // เมื่อเลือกสมาชิก ให้ Redirect ไปดึงข้อมูลสมุดบัญชี
            $('.select2-member').on('select2:select', function (e) {
                var userId = e.params.data.id;
                if (userId) {
                    window.location.href = "{{ url('keptkayas/withdraw/create') }}/" + userId;
                }
            });

            // เมื่อกดลบตัวเลือก
            $('.select2-member').on('select2:clear', function (e) {
                window.location.href = "{{ route('keptkayas.withdraw.create') }}";
            });
        }

        // Toggle สวิตช์ผู้รับแทน
        $('#receiveInstead').on('change', function() {
            $('#proxyInputs').slideToggle(this.checked);
        });
    });
</script>
@endsection