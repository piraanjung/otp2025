@extends('layouts.super-admin')

@section('nav-main', 'ประเภทมิเตอร์')
@section('nav-current', 'อัตราการชำระตามประเภทมิเตอร์')
@section('nav-current-title', 'อัตราการชำระตามประเภทมิเตอร์')

@section('content')
<div class="card mb-4">
    <div class="card-header pb-0 d-flex justify-content-between align-items-center">
        <h6 class="mb-0">อัตราการชำระตามประเภทมิเตอร์</h6>
        <a href="{{ route('admin.meter_rates.create') }}" class="btn bg-gradient-primary btn-sm mb-0">
            <i class="fas fa-plus me-1"></i> เพิ่มอัตราการชำระ
        </a>
    </div>
    <div class="card-body px-0 pt-0 pb-2">
        <div class="table-responsive p-0">
            <table class="table align-items-center mb-0">
                <thead>
                    <tr>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ID</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ประเภทมิเตอร์</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ประเภทการคิดราคา</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-end">ค่าบริการขั้นต่ำ</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-end">อัตราคงที่/หน่วย</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">เริ่มใช้</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">สิ้นสุด</th>
                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">สถานะ</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">หมายเหตุ</th>
                        <th class="text-secondary opacity-7"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rateConfigs as $config)
                        <tr>
                            <td class="ps-4 text-sm">{{ $config->id }}</td>
                            <td><h6 class="mb-0 text-sm">{{ $config->meterType->meter_type_name ?? '-' }}</h6></td>
                            <td class="text-sm">{{ $config->pricingType->name ?? '-' }}</td>
                            <td class="text-sm text-end">{{ number_format($config->min_usage_charge, 2) }}</td>
                            <td class="text-sm text-end">{{ $config->fixed_rate_per_unit ? number_format($config->fixed_rate_per_unit, 4) : '-' }}</td>
                            <td class="text-sm">{{ $config->effective_date->format('Y-m-d') }}</td>
                            <td class="text-sm">{{ $config->end_date ? $config->end_date->format('Y-m-d') : '-' }}</td>
                            <td class="align-middle text-center">
                                <span class="badge badge-sm bg-gradient-{{ $config->is_active ? 'success' : 'secondary' }}">
                                    {{ $config->is_active ? 'ใช้งาน' : 'ปิด' }}
                                </span>
                            </td>
                            <td class="text-xs text-secondary">{{ $config->comment }}</td>
                            <td class="align-middle text-end pe-4 text-nowrap">
                                <a href="{{ route('admin.meter_rates.show', $config->id) }}"
                                    class="text-secondary font-weight-bold text-xs me-3">
                                    <i class="fas fa-eye me-1"></i> ดูรายละเอียด
                                </a>
                                <a href="{{ route('admin.meter_rates.edit', $config->id) }}"
                                    class="text-secondary font-weight-bold text-xs me-3">
                                    <i class="fas fa-edit me-1"></i> แก้ไข
                                </a>
                                <form action="{{ route('admin.meter_rates.destroy', $config->id) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('ยืนยันการลบอัตราการชำระนี้?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-link text-danger font-weight-bold text-xs p-0 mb-0">
                                        <i class="fas fa-trash me-1"></i> ลบ
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="10" class="text-center text-secondary py-4">ยังไม่มีข้อมูล</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
