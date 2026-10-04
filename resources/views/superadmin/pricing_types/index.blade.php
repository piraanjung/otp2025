@extends('layouts.super-admin')

@section('nav-main', 'ประเภทมิเตอร์')
@section('nav-current', 'ประเภทการชำระเงิน')
@section('nav-current-title', 'ประเภทการชำระเงิน')

@section('content')
<div class="card mb-4">
    <div class="card-header pb-0 d-flex justify-content-between align-items-center">
        <h6 class="mb-0">ประเภทการชำระเงิน</h6>
        <a href="{{ route('admin.pricing_types.create') }}" class="btn bg-gradient-primary btn-sm mb-0">
            <i class="fas fa-plus me-1"></i> เพิ่มประเภทการชำระเงิน
        </a>
    </div>
    <div class="card-body px-0 pt-0 pb-2">
        <div class="table-responsive p-0">
            <table class="table align-items-center mb-0">
                <thead>
                    <tr>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ID</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ชื่อ</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">คำอธิบาย</th>
                        <th class="text-secondary opacity-7"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pricingTypes as $pricingType)
                        <tr>
                            <td class="ps-4 text-sm">{{ $pricingType->id }}</td>
                            <td><h6 class="mb-0 text-sm">{{ $pricingType->name }}</h6></td>
                            <td><p class="text-xs text-secondary mb-0">{{ $pricingType->description ?: '-' }}</p></td>
                            <td class="align-middle text-end pe-4">
                                <a href="{{ route('admin.pricing_types.edit', $pricingType->id) }}"
                                    class="text-secondary font-weight-bold text-xs me-3">
                                    <i class="fas fa-edit me-1"></i> แก้ไข
                                </a>
                                <form action="{{ route('admin.pricing_types.destroy', $pricingType->id) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('ยืนยันการลบ? การลบอาจกระทบอัตราการชำระที่ใช้ประเภทนี้อยู่')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-link text-danger font-weight-bold text-xs p-0 mb-0">
                                        <i class="fas fa-trash me-1"></i> ลบ
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-secondary py-4">ยังไม่มีข้อมูล</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
