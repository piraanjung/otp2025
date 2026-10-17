@extends('layouts.super-admin') 
@section('title_page', 'ยื่นคำร้องขอรับสวัสดิการ')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-primary text-white py-3 rounded-top-4">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-file-invoice-dollar me-2"></i>ยื่นคำร้องขอรับเงินสวัสดิการฌาปนกิจ</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('member.welfare.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        @if(session('error'))
                            <div class="alert alert-danger rounded-3 mb-3">{{ session('error') }}</div>
                        @endif

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">ชื่อสมาชิกผู้มีสิทธิ์ / ผู้เสียชีวิต</label>
                            <input type="text" name="deceased_name" class="form-control rounded-3 bg-light border-0" required placeholder="ระบุชื่อ-นามสกุล ผู้เสียชีวิต">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">ชื่อทายาทผู้รับเงินแทน</label>
                            <input type="text" name="beneficiary_name" class="form-control rounded-3 bg-light border-0" required placeholder="ระบุชื่อ-นามสกุล ทายาทผู้รับเงิน">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">อัปโหลดภาพใบมรณบัตร</label>
                            <input type="file" name="death_certificate_img" class="form-control rounded-3 bg-light border-0" accept="image/*" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold small text-muted">หมายเหตุเพิ่มเติม (ถ้ามี)</label>
                            <textarea name="note" class="form-control rounded-3 bg-light border-0" rows="3" placeholder="ระบุข้อมูลเพิ่มเติม..."></textarea>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('member.welfare.eligibility') }}" class="text-decoration-none small text-muted">
                                <i class="fas fa-arrow-left me-1"></i> กลับหน้าตรวจสอบสิทธิ์
                            </a>
                            <button type="submit" class="btn btn-primary rounded-pill px-5 shadow-sm">
                                ส่งคำร้องขอรับสวัสดิการ
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection