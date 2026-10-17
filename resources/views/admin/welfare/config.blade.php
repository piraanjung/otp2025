@extends('layouts.welfare')
@section('title_page', 'ตั้งค่าเกณฑ์สวัสดิการ')

@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-lg-10 mx-auto">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white border-0 pt-4 pb-0">
                        <h5 class="fw-bold"><i
                                class="fas fa-cog me-2 text-primary"></i>ตั้งค่าเกณฑ์และนโยบายกองทุนสวัสดิการฌาปนกิจ</h5>
                        <p class="text-sm text-muted">กำหนดเงื่อนไขการรับสิทธิ์, สายการอนุมัติ,
                            และนโยบายการหักเงินแบบแยกส่วน (3 ส่วนหลัก)</p>
                    </div>
                    <hr class="horizontal dark">

                    <div class="card-body pt-0">
                        <form action="{{ route('admin.welfare.config.update') }}" method="POST">
                            @csrf
                            @method('PUT')

                            @if(session('success'))
                                <div class="alert alert-success rounded-3 mb-4">{{ session('success') }}</div>
                            @endif

                            <!-- ================= ส่วนที่ 0: เกณฑ์พื้นฐานและสายการอนุมัติ ================= -->
                            <div class="card border border-light bg-light rounded-4 p-4 mb-4">
                                <h6 class="fw-bold mb-3 text-dark"><i class="fas fa-info-circle text-muted me-2"></i>0.
                                    เกณฑ์คุณสมบัติพื้นฐานและสายการอนุมัติ</h6>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label fw-bold small">ระยะเวลาสมาชิกขั้นต่ำ (เดือน)</label>
                                        <input type="number" name="min_months_active" class="form-control border-0 bg-white"
                                            value="{{ $config->min_months_active }}">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label fw-bold small">น้ำหนักขยะสะสมขั้นต่ำ (กก.)</label>
                                        <input type="number" name="min_total_weight" class="form-control border-0 bg-white"
                                            value="{{ $config->min_total_weight }}">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label fw-bold small">เงินช่วยเหลือมาตรฐาน (บาท)</label>
                                        <input type="number" name="default_payout_amount"
                                            class="form-control border-0 bg-white fw-bold text-success"
                                            value="{{ $config->default_payout_amount }}">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold small">สายการอนุมัติ (Approval Workflow)</label>
                                        <select name="workflow_id" class="form-select border-0 bg-white" required>
                                            <option value="">-- เลือกสายการอนุมัติ --</option>
                                            @foreach($workflows as $workflow)
                                                <option value="{{ $workflow->id }}" {{ $config->workflow_id == $workflow->id ? 'selected' : '' }}>
                                                    {{ $workflow->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold small">เงินคงเหลือขั้นต่ำในบัญชี (บาท)</label>
                                        <input type="number" name="min_wallet_balance"
                                            class="form-control border-0 bg-white fw-bold text-warning"
                                            value="{{ $config->min_wallet_balance ?? 300 }}" step="0.01" required>
                                        <small class="text-muted"
                                            style="font-size: 0.75rem;">ยอดเงินสำรองที่ห้ามหักต่ำกว่านี้
                                            สมาชิกต้องเติมเงินเพิ่มถ้ายอดไม่ถึง</small>
                                    </div>
                                </div>
                            </div>


                            <!-- ================= ส่วนที่ 1: เงินกองทุนธนาคารขยะ (บัญชีกลาง) ================= -->
                            <div class="card border border-primary-subtle rounded-4 p-4 mb-4">
                                <h6 class="fw-bold mb-3 text-primary"><i class="fas fa-university me-2"></i>ส่วนที่ 1:
                                    นโยบายการใช้ "เงินกองทุนธนาคารขยะกลาง"</h6>
                                <p class="text-muted small mb-3">
                                    กำหนดวิธีจัดการเมื่อมีเคสเสียชีวิตและต้องเบิกจ่ายเงินช่วยเหลือ</p>

                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="use_central_fund_subsidy"
                                        id="fundSubsidy1" value="1" {{ ($config->use_central_fund_subsidy ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold text-dark" for="fundSubsidy1">
                                        หักจากเงินกองทุนกลาง (เช็กกำไรสะสม ถ้ามีพอจ่ายตามสิทธิ์ กองทุนกลางจ่ายเต็มจำนวน
                                        สมาชิกไม่ต้องถูกหักเงินเพิ่ม)
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="use_central_fund_subsidy"
                                        id="fundSubsidy0" value="0" {{ !($config->use_central_fund_subsidy ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold text-dark" for="fundSubsidy0">
                                        ไม่ใช้เงินกองทุนกลาง (ข้ามไปเรียกเก็บเงินจากสมาชิกตามเงื่อนไขส่วนที่ 2 และ 3 ทันที)
                                    </label>
                                </div>
                            </div>


                            <!-- ================= ส่วนที่ 2: สมาชิกธนาคารขยะ (Active Recyclers) ================= -->
                            <div class="card border border-success-subtle rounded-4 p-4 mb-4">
                                <h6 class="fw-bold mb-3 text-success"><i class="fas fa-recycle me-2"></i>ส่วนที่ 2:
                                    เงื่อนไขการหักเงิน "สมาชิกธนาคารขยะ"</h6>
                                <p class="text-muted small mb-3">
                                    เลือกวิธีคิดเงินสมทบสำหรับสมาชิกที่ช่วยขายขยะรีไซเคิลสร้างกำไรให้กองทุน</p>

                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="recycle_deduction_model"
                                        id="recycleModelExempt" value="exempt" {{ ($config->recycle_deduction_model ?? 'proportional_deficit') === 'exempt' ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold text-dark" for="recycleModelExempt">
                                        ยกเว้น ไม่หักเงินใดๆ ทั้งสิ้น (ให้สิทธิ์ 0 บาท
                                        เพราะถือเป็นผู้สร้างกำไรให้กองทุนแล้ว)
                                    </label>
                                </div>

                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="recycle_deduction_model"
                                        id="recycleModelFixed" value="fixed" {{ ($config->recycle_deduction_model ?? '') === 'fixed' ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold text-dark" for="recycleModelFixed">
                                        หักเรทฟิกซ์ตายตัวจากบัญชีสมาชิก (กำหนดจำนวนเงินด้านล่าง)
                                    </label>
                                    <div class="ms-4 mt-2">
                                        <input type="number" name="recycle_fixed_fee"
                                            class="form-control form-control-sm w-50 bg-white border"
                                            value="{{ $config->recycle_fixed_fee ?? 30 }}"
                                            placeholder="ระบุจำนวนเงิน (บาท)">
                                    </div>
                                </div>

                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="recycle_deduction_model"
                                        id="recycleModelProp" value="proportional_deficit" {{ ($config->recycle_deduction_model ?? 'proportional_deficit') === 'proportional_deficit' ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold text-dark" for="recycleModelProp">
                                        หักจาก "ส่วนต่างที่เหลือ" (นำยอดที่ต้องจ่าย - เงินกองทุนกลาง - ยอดเก็บจากนอกขยะ
                                        แล้วนำมาหารเฉลี่ยเฉพาะสมาชิกกลุ่มนี้) <em>(แนะนำ)</em>
                                    </label>
                                </div>
                            </div>



                            <!-- ================= ส่วนที่ 3: สมาชิกนอกธนาคารขยะ (Contribution Only) ================= -->
                            <div class="card border border-warning-subtle rounded-4 p-4 mb-4">
                                <h6 class="fw-bold mb-3 text-warning-emphasis"><i class="fas fa-users me-2"></i>ส่วนที่ 3:
                                    เงื่อนไขการเก็บเงิน "สมาชิกนอกธนาคารขยะ (สมทบอย่างเดียว)"</h6>
                                <p class="text-muted small mb-3">กำหนดเรทเงินสมทบสำหรับสมาชิกที่ไม่ได้ขายขยะรีไซเคิล</p>

                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="non_recycle_model"
                                        id="nonRecycleFixed" value="fixed" {{ ($config->non_recycle_model ?? 'fixed') === 'fixed' ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold text-dark" for="nonRecycleFixed">
                                        จ่ายเป็นจำนวนเงินตายตัว (Fix Rate ต่อเคส)
                                    </label>
                                    <div class="ms-4 mt-2">
                                        <input type="number" name="non_recycle_member_fee"
                                            class="form-control form-control-sm w-50 bg-white border fw-bold text-danger"
                                            value="{{ $config->non_recycle_member_fee ?? 90 }}" placeholder="เช่น 90 บาท">
                                    </div>
                                </div>

                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="non_recycle_model"
                                        id="nonRecycleSame" value="same_as_recycle" {{ ($config->non_recycle_model ?? '') === 'same_as_recycle' ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold text-dark" for="nonRecycleSame">
                                        จ่ายเท่ากับเรทของสมาชิกธนาคารขยะ (อัตราเดียวกันทั้งหมด)
                                    </label>
                                </div>
                            </div>


                            <!-- ปุ่มบันทึก -->
                            <div class="d-flex justify-content-end mt-4">
                                <button type="submit" class="btn btn-primary rounded-pill px-5 shadow-sm">
                                    <i class="fas fa-save me-1"></i> บันทึกการตั้งค่าทั้งหมด
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection