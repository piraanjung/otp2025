@extends('layouts.keptkaya')

@section('nav-current', 'ตั้งค่าเงื่อนไขธนาคารขยะ')

@section('content')
    <div class="container-fluid py-2">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-header bg-white pb-0">
                <div class="d-flex align-items-center">
                    <div>
                        <h5 class="font-weight-bolder text-dark mb-1">
                            <i class="fas fa-cogs text-primary me-2"></i>ตั้งค่าเงื่อนไขและกฎเกณฑ์ธนาคารขยะ
                        </h5>
                        <p class="text-xs text-secondary mb-0">กำหนดเงื่อนไขการถอนเงิน รอบวันตัดอนุมัติ
                            และนโยบายกองทุนสวัสดิการฌาปนกิจ</p>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success text-white font-weight-bold text-sm mb-3">
                        <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('keptkayas.settings.update') }}" method="POST">
                    @csrf

                    <!-- 1. เงื่อนไขทางการเงินและการถอนเงิน -->
                    <h6 class="text-uppercase text-xs font-weight-bolder text-primary mb-3">
                        <i class="fas fa-wallet me-1"></i> 1. เงื่อนไขทางการเงินและการถอนเงิน (Financial Rules)
                    </h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold text-sm">ยอดเงินสำรองขั้นต่ำติดบัญชี (บาท)</label>
                            <input type="number" name="min_reserve" class="form-control"
                                value="{{ $settings['min_reserve'] }}" step="0.01" required>
                            <small class="text-xs text-muted">* เงินส่วนนี้จะถูกสำรองไว้ ไม่สามารถทำรายการถอนออกได้</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold text-sm">ยอดถอนเงินขั้นต่ำต่อครั้ง (บาท)</label>
                            <input type="number" name="min_withdraw" class="form-control"
                                value="{{ $settings['min_withdraw'] }}" step="0.01" required>
                        </div>
                    </div>

                    <hr class="horizontal dark my-4">

                    <!-- 2. รอบเวลาการเสนออนุมัติและการจ่ายเงิน -->
                    <h6 class="text-uppercase text-xs font-weight-bolder text-primary mb-3">
                        <i class="fas fa-calendar-alt me-1"></i> 2. รอบเวลาการเสนออนุมัติและการจ่ายเงิน (Weekly Cycle)
                    </h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <label class="form-label font-weight-bold text-sm">วันตัดรอบคำขอถอนเงิน</label>
                            <select name="cutoff_day" class="form-select">
                                <option value="FRIDAY" {{ $settings['cutoff_day'] == 'FRIDAY' ? 'selected' : '' }}>วันศุกร์
                                </option>
                                <option value="THURSDAY" {{ $settings['cutoff_day'] == 'THURSDAY' ? 'selected' : '' }}>
                                    วันพฤหัสบดี</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label font-weight-bold text-sm">เวลาตัดรอบคำขอ</label>
                            <input type="time" name="cutoff_time" class="form-control"
                                value="{{ $settings['cutoff_time'] }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label font-weight-bold text-sm">วันถอนเงินสดจากธนาคาร</label>
                            <select name="bank_cashout_day" class="form-select">
                                <option value="MONDAY" {{ $settings['bank_cashout_day'] == 'MONDAY' ? 'selected' : '' }}>
                                    วันจันทร์</option>
                                <option value="FRIDAY" {{ $settings['bank_cashout_day'] == 'FRIDAY' ? 'selected' : '' }}>
                                    วันศุกร์</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label font-weight-bold text-sm">วันจ่ายเงินสดให้สมาชิก</label>
                            <select name="payout_day" class="form-select">
                                <option value="TUESDAY" {{ $settings['payout_day'] == 'TUESDAY' ? 'selected' : '' }}>วันอังคาร
                                </option>
                                <option value="WEDNESDAY" {{ $settings['payout_day'] == 'WEDNESDAY' ? 'selected' : '' }}>
                                    วันพุธ</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold text-sm">จำนวนรอบบิลที่ไม่ขายขยะแล้วโดนอายัดถอนเงิน
                                (รอบบิล)</label>
                            <input type="number" name="max_inactive_cycles" class="form-control"
                                value="{{ $settings['max_inactive_cycles'] }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold text-sm">ระยะเวลาผ่อนผันการมารับเงินสด (วัน)</label>
                            <input type="number" name="payout_expiry_days" class="form-control"
                                value="{{ $settings['payout_expiry_days'] }}" required>
                            <small class="text-xs text-muted">* หากไม่มารับภายในกำหนด ระบบจะ Cancel
                                คำขอและคืนสิทธิ์เงินอายัด</small>
                        </div>

                        <div class="col-md-6">
                            <label
                                class="form-label font-weight-bold text-sm">ลำดับการอนุมัติเบิกจ่ายเงินธนาคารขยะรีไซเคิล</label>
                            <select name="payout_approval_workflow" id="payout_approval_workflow" class="form-control"
                                required>
                                <option value="" {{ 0 == $settings['payout_approval_workflow'] ? 'selected' : "" }}>เลือก...
                                </option>
                                @foreach ($approval_workflows as $workflow)
                                    <option value="{{ $workflow->id }}" {{ $workflow->id == $settings['payout_approval_workflow'] ? 'selected' : "" }}>{{ $workflow->name }}</option>
                                @endforeach
                            </select>
                            <small class="text-xs text-muted">* กำหนดลำดับผู้อนุมัติเบิกจ่ายเงินธนาคารขยะรีไซเคิล</small>
                        </div>



                    </div>
                    <hr class="horizontal dark my-4">

                    <!-- 2. รอบเวลาการเสนออนุมัติและการจ่ายเงิน -->
                    <h6 class="text-uppercase text-xs font-weight-bolder text-primary mb-3">
                        <i class="fas fa-calendar-alt me-1"></i> 2. รอบเวลาการเสนออนุมัติและการจ่ายเงิน (Weekly Cycle)
                    </h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <label class="form-label font-weight-bold text-sm">วันตัดรอบคำขอถอนเงิน</label>
                            <select name="cutoff_day" class="form-select">
                                <option value="FRIDAY" {{ $settings['cutoff_day'] == 'FRIDAY' ? 'selected' : '' }}>วันศุกร์
                                </option>
                                <option value="THURSDAY" {{ $settings['cutoff_day'] == 'THURSDAY' ? 'selected' : '' }}>
                                    วันพฤหัสบดี</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label font-weight-bold text-sm">เวลาตัดรอบคำขอ</label>
                            <input type="time" name="cutoff_time" class="form-control"
                                value="{{ $settings['cutoff_time'] }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label font-weight-bold text-sm">วันถอนเงินสดจากธนาคาร</label>
                            <select name="bank_cashout_day" class="form-select">
                                <option value="MONDAY" {{ $settings['bank_cashout_day'] == 'MONDAY' ? 'selected' : '' }}>
                                    วันจันทร์</option>
                                <option value="FRIDAY" {{ $settings['bank_cashout_day'] == 'FRIDAY' ? 'selected' : '' }}>
                                    วันศุกร์</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label font-weight-bold text-sm">วันจ่ายเงินสดให้สมาชิก</label>
                            <select name="payout_day" class="form-select">
                                <option value="TUESDAY" {{ $settings['payout_day'] == 'TUESDAY' ? 'selected' : '' }}>วันอังคาร
                                </option>
                                <option value="WEDNESDAY" {{ $settings['payout_day'] == 'WEDNESDAY' ? 'selected' : '' }}>
                                    วันพุธ</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold text-sm">จำนวนรอบบิลที่ไม่ขายขยะแล้วโดนอายัดถอนเงิน
                                (รอบบิล)</label>
                            <input type="number" name="max_inactive_cycles" class="form-control"
                                value="{{ $settings['max_inactive_cycles'] }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold text-sm">ระยะเวลาผ่อนผันการมารับเงินสด (วัน)</label>
                            <input type="number" name="payout_expiry_days" class="form-control"
                                value="{{ $settings['payout_expiry_days'] }}" required>
                            <small class="text-xs text-muted">* หากไม่มารับภายในกำหนด ระบบจะ Cancel
                                คำขอและคืนสิทธิ์เงินอายัด</small>
                        </div>
                    </div>




                    <hr class="horizontal dark my-4">

                    <!-- 3. ตั้งค่ากองทุนสวัสดิการฌาปนกิจสงเคราะห์ -->
                    <h6 class="text-uppercase text-xs font-weight-bolder text-primary mb-3">
                        <i class="fas fa-ribbon me-1"></i> 4. นโยบายกองทุนสวัสดิการฌาปนกิจสงเคราะห์ (Welfare Policy)
                    </h6>
                    <div class="card bg-gray-100 border-0 p-3 mb-4 rounded-3">
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="enableWelfare" name="enable_welfare"
                                value="1" {{ $settings['enable_welfare'] == '1' ? 'checked' : '' }}>
                            <label class="form-check-label font-weight-bold text-dark" for="enableWelfare">
                                เปิดใช้งานระบบสวัสดิการฌาปนกิจสงเคราะห์
                            </label>
                        </div>

                        <div id="welfareConfigSection"
                            style="display: {{ $settings['enable_welfare'] == '1' ? 'block' : 'none' }};">
                            <div class="mb-3">
                                <label class="form-label font-weight-bold text-sm">รูปแบบการจัดการกองทุน</label>
                                <div class="form-check mb-1">
                                    <input class="form-check-input" type="radio" name="welfare_mode" id="modeMargin"
                                        value="margin_only" {{ $settings['welfare_mode'] == 'margin_only' ? 'checked' : '' }}>
                                    <label class="form-check-label text-sm" for="modeMargin">
                                        ใช้เงินส่วนต่างจากการขายขยะอย่างเดียว (ไม่หักเงินในบัญชีสมาชิก)
                                    </label>
                                </div>
                                <div class="form-check mb-1">
                                    <input class="form-check-input" type="radio" name="welfare_mode" id="modeDeduct"
                                        value="member_deduct" {{ $settings['welfare_mode'] == 'member_deduct' ? 'checked' : '' }}>
                                    <label class="form-check-label text-sm" for="modeDeduct">
                                        หักเงินสมทบจากบัญชีสมาชิกเมื่อมีผู้เสียชีวิต
                                    </label>
                                </div>
                                <div class="form-check mb-1">
                                    <input class="form-check-input" type="radio" name="welfare_mode" id="modeHybrid"
                                        value="hybrid" {{ $settings['welfare_mode'] == 'hybrid' ? 'checked' : '' }}>
                                    <label class="form-check-label text-sm" for="modeHybrid">
                                        แบบผสม (ใช้เงินส่วนต่างก่อน หากไม่พอค่อยหักสมาชิก)
                                    </label>
                                </div>
                            </div>

                            <div class="row g-3 mt-2">
                                <div class="col-md-6">
                                    <label class="form-label font-weight-bold text-sm">จำนวนเงินหักสมทบต่อเคสผู้เสียชีวิต
                                        (บาท/สมาชิก 1 คน)</label>
                                    <input type="number" name="welfare_deduct_amount" class="form-control"
                                        value="{{ $settings['welfare_deduct_amount'] }}" step="0.01">
                                </div>
                                <div class="col-md-6 d-flex align-items-center mt-4">
                                    <div class="form-check me-4">
                                        <input class="form-check-input" type="checkbox" id="allowNegative"
                                            name="allow_negative_balance" value="1" {{ $settings['allow_negative_balance'] == '1' ? 'checked' : '' }}>
                                        <label class="form-check-label text-sm fw-bold" for="allowNegative">
                                            ยินยอมให้ยอดเงินคงเหลือติดลบได้ (กรณีเงินไม่พอหัก)
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="autoDeductSale"
                                            name="auto_deduct_on_sale" value="1" {{ $settings['auto_deduct_on_sale'] == '1' ? 'checked' : '' }}>
                                        <label class="form-check-label text-sm fw-bold" for="autoDeductSale">
                                            หักชำระยอดค้างฌาปนกิจให้อัตโนมัติ เมื่อสมาชิกนำขยะมาขายในครั้งถัดไป
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-end mt-4">
                        <button type="submit" class="btn bg-gradient-primary px-4">
                            <i class="fas fa-save me-1"></i> บันทึกการตั้งค่า
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('enableWelfare').addEventListener('change', function () {
            document.getElementById('welfareConfigSection').style.display = this.checked ? 'block' : 'none';
        });
    </script>
@endsection