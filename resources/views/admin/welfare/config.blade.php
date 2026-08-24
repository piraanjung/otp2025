@extends('layouts.keptkaya')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 pb-0">
                    <h5 class="fw-bold"><i class="bi bi-gear-fill me-2 text-primary"></i>ตั้งค่าเกณฑ์การรับสวัสดิการ</h5>
                    <p class="text-sm text-muted">กำหนดเงื่อนไขที่สมาชิกจะได้รับเงินสวัสดิการฌาปนกิจ</p>
                </div>
                <hr class="horizontal dark">
                <div class="card-body pt-0">
                    <form action="{{ route('admin.welfare.config.update') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small">ระยะเวลาการเป็นสมาชิกขั้นต่ำ (เดือน)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i class="bi bi-calendar-check"></i></span>
                                    <input type="number" name="min_months_active" class="form-control border-0 bg-light" value="{{ $config->min_months_active }}">
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small">น้ำหนักขยะสะสมขั้นต่ำ (กก.)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i class="bi bi-box-seam"></i></span>
                                    <input type="number" name="min_total_weight" class="form-control border-0 bg-light" value="{{ $config->min_total_weight }}">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small">จำนวนเงินจ่ายสวัสดิการมาตรฐาน (บาท)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0 text-success fw-bold">฿</span>
                                <input type="number" name="default_payout_amount" class="form-control border-0 bg-light fs-4 fw-bold" value="{{ $config->default_payout_amount }}">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold small">รายละเอียด/หมายเหตุเกณฑ์การจ่าย</label>
                            <textarea name="criteria_description" class="form-control border-0 bg-light" rows="3">{{ $config->criteria_description }}</textarea>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary rounded-pill px-5 shadow-sm">
                                <i class="bi bi-save me-1"></i> บันทึกการตั้งค่า
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card bg-gradient-info border-0 shadow-sm rounded-4 mt-4">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <h6 class="text-white mb-0">ระบบตรวจสอบอัตโนมัติ</h6>
                            <p class="text-white text-xs opacity-8">เมื่อตั้งค่าแล้ว ระบบจะใช้เกณฑ์นี้ตรวจสอบประวัติจากตู้บักแอโร่อัตโนมัติในหน้าเบิกจ่าย</p>
                        </div>
                        <div class="col-4 text-end">
                            <i class="bi bi-shield-check text-white display-6"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
