@extends('layouts.super-admin')

@section('content')
<div class="card">
    <div class="card-header">
        รายละเอียดอัตราค่าน้ำ: {{ $meterRateConfig->meterType->meter_type_name }}
    </div>
    <div class="card-body">
        <p><strong>ประเภทมิเตอร์:</strong> {{ $meterRateConfig->meterType->meter_type_name }}</p>
        <p><strong>ประเภทราคา:</strong> {{ $meterRateConfig->pricingType->name }}</p>
        <p><strong>ค่าบริการขั้นต่ำ:</strong> {{ number_format($meterRateConfig->min_usage_charge, 2) }} บาท</p>
        
        @if($meterRateConfig->fixed_rate_per_unit)
            <p><strong>ราคาคงที่:</strong> {{ number_format($meterRateConfig->fixed_rate_per_unit, 2) }} บาท/หน่วย</p>
        @endif

       {{-- ใช้ count() เช็คจาก method โดยตรงเพื่อความชัวร์ หรือใช้ property ตัวเล็ก --}}
@if($meterRateConfig->Ratetiers->isNotEmpty()) 
    <h5 class="mt-4 text-primary">อัตราก้าวหน้า (Progressive Tiers)</h5>
    <table class="table table-bordered table-striped w-100">
        <thead class="thead-dark">
            <tr>
                <th style="width: 10%">Tier</th>
                <th style="width: 45%">ช่วงการใช้งาน (หน่วย)</th>
                <th style="width: 45%">ราคาต่อหน่วย (บาท)</th>
            </tr>
        </thead>
        <tbody>
            {{-- วนลูปแสดงข้อมูล --}}
            @foreach($meterRateConfig->Ratetiers as $index => $tier)
            <tr>
                <td class="text-center font-weight-bold">{{ $index + 1 }}</td>
                <td>
                    {{ $tier->min_units }} 
                    - 
                    {{ $tier->max_units ? $tier->max_units : 'ขึ้นไป (ไม่จำกัด)' }}
                </td>
                <td class="text-right">
                    {{ number_format($tier->rate_per_unit, 2) }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
@else
    {{-- กรณีไม่มีข้อมูล หรือเป็น Fixed Rate --}}
    @if($meterRateConfig->pricingType->name == 'Progressive')
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle"></i> พบว่าเป็นประเภท Progressive แต่ไม่พบข้อมูลขั้นบันได (Data Missing)
        </div>
    @endif
@endif

        <a href="{{ route('admin.meter_rates.index') }}" class="btn btn-secondary">ย้อนกลับ</a>
        <a href="{{ route('admin.meter_rates.edit', $meterRateConfig->id) }}" class="btn btn-warning">แก้ไข</a>
    </div>
</div>
@endsection