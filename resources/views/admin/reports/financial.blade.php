@extends('layouts.super-admin')
@section('content')
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm bg-primary text-white" style="border-radius: 1rem;">
            <div class="card-body p-4">
                <h6 class="text-white-50">ประมาณการรายได้ทั้งหมด</h6>
                <h2 class="fw-bold">฿{{ number_format($totalPotentialIncome, 2) }}</h2>
                <small>คำนวณจากสมาชิกทั้งหมด</small>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm bg-success text-white" style="border-radius: 1rem;">
            <div class="card-body p-4">
                <h6 class="text-white-50">มูลค่าสวัสดิการ (ยกเว้นค่าขยะ)</h6>
                <h2 class="fw-bold">฿{{ number_format($totalWaivedValue, 2) }}</h2>
                <small>คืนกำไรให้ประชาชนที่คัดแยกขยะ</small>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm bg-warning text-dark" style="border-radius: 1rem;">
            <div class="card-body p-4">
                <h6 class="text-dark-50">ยอดรายได้ที่ต้องจัดเก็บจริง</h6>
                <h2 class="fw-bold">฿{{ number_format($expectedCash, 2) }}</h2>
                <small>เป้าหมายการเรียกเก็บเงินเดือนนี้</small>
            </div>
        </div>
    </div>
</div>
@endsection
