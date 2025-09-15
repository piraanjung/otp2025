@extends('layouts.keptkaya')

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h1 class="card-title mb-0">รายละเอียดราคารับซื้อ</h1>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-4">รายการขยะ:</dt>
                    <dd class="col-sm-8">{{ $price->item->item_name ?? 'N/A' }}</dd>

                    <dt class="col-sm-4">หน่วยนับ:</dt>
                    <dd class="col-sm-8">{{ $price->kp_units_info->unitname ?? 'N/A' }}</dd>

                    <dt class="col-sm-4">ราคาจ่ายให้สมาชิก (ต่อหน่วย):</dt>
                    <dd class="col-sm-8">{{ number_format($price->price_for_member, 2) }} บาท</dd>

                    <dt class="col-sm-4">ราคาจากร้านรับซื้อ (ต่อหน่วย):</dt>
                    <dd class="col-sm-8">{{ number_format($price->price_from_dealer, 2) }} บาท</dd>

                    <dt class="col-sm-4">คะแนน:</dt>
                    <dd class="col-sm-8">{{ number_format($price->point) }} คะแนน</dd>

                    <dt class="col-sm-4">วันที่เริ่มมีผล:</dt>
                    <dd class="col-sm-8">{{ $price->effective_date->format('Y-m-d') }}</dd>

                    <dt class="col-sm-4">วันที่สิ้นสุด:</dt>
                    <dd class="col-sm-8">{{ $price->end_date ? $price->end_date->format('Y-m-d') : 'ปัจจุบัน' }}</dd>

                    <dt class="col-sm-4">สถานะ:</dt>
                    <dd class="col-sm-8">
                        <span class="badge {{ $price->is_active ? 'bg-success' : 'bg-secondary' }}">
                            {{ $price->is_active ? 'ใช้งาน' : 'ไม่ใช้งาน' }}
                        </span>
                    </dd>

                    <dt class="col-sm-4">ผู้บันทึก:</dt>
                    <dd class="col-sm-8">{{ $price->recorder->firstname ?? 'N/A' }}
                        {{ $price->recorder->lastname ?? 'N/A' }}</dd>

                    <dt class="col-sm-4">หมายเหตุ:</dt>
                    <dd class="col-sm-8">{{ $price->comment ?? 'N/A' }}</dd>

                    <dt class="col-sm-4">สร้างเมื่อ:</dt>
                    <dd class="col-sm-8">{{ $price->created_at->format('Y-m-d H:i:s') }}</dd>

                    <dt class="col-sm-4">อัปเดตเมื่อ:</dt>
                    <dd class="col-sm-8">{{ $price->updated_at->format('Y-m-d H:i:s') }}</dd>
                </dl>
            </div>
            <div class="card-footer d-flex justify-content-between">
                <a href="{{ route('keptkayas.tbank.prices.index') }}" class="btn btn-secondary">ย้อนกลับ</a>
                <a href="{{ route('keptkayas.tbank.prices.edit', $price->id) }}" class="btn btn-warning">แก้ไข</a>
            </div>
        </div>
    </div>
@endsection