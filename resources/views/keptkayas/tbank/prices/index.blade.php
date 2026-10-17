@extends('layouts.keptkaya')
@section('nav-header', 'ตั้งค่าข้อมูล')
@section('nav-current', 'ตารางราคารับซื้อขยะรีไซเคิล')
@section('page-topic', 'ตารางราคารับซื้อขยะรีไซเคิล')
@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show text-white" role="alert">
            <span class="alert-text">{{ session('success') }}</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-header pb-0">
            <div class="d-lg-flex justify-content-between align-items-center">
                <h6 class="mb-lg-0 mb-3">รายการราคารับซื้อ</h6>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('keptkayas.tbank.prices.create') }}" class="btn bg-gradient-primary btn-sm mb-0">
                        <i class="fas fa-plus me-1"></i> เพิ่มราคาใหม่
                    </a>
                    <a href="{{ route('keptkayas.tbank.prices.bulk_edit') }}" class="btn btn-outline-primary btn-sm mb-0">
                        <i class="fas fa-edit me-1"></i> แก้ไขราคาหลายรายการ
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body px-0 pt-3 pb-2">
            <div class="table-responsive p-0">
                <table class="table align-items-center mb-0">
                    <thead>
                        <tr>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-4">#</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">รายการขยะ</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">หน่วยนับ</th>
                            <th class="text-end text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ราคาจ่ายสมาชิก</th>
                            <th class="text-end text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ราคาจากร้านรับซื้อ</th>
                            <th class="text-end text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">คะแนน</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">เริ่มมีผล</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">สิ้นสุด</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">สถานะ</th>
                            <th class="text-secondary opacity-7"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($prices as $price)
                            <tr>
                                <td class="ps-4"><p class="text-xs font-weight-bold mb-0">{{ $prices->firstItem() + $loop->index }}</p></td>
                                <td>
                                    <h6 class="mb-0 text-sm">{{ $price->item->kp_itemsname ?? 'N/A' }}</h6>
                                    <p class="text-xxs text-secondary mb-0">รหัสรายการ {{ $price->item->id ?? '-' }}</p>
                                </td>
                                <td><p class="text-xs font-weight-bold mb-0">{{ $price->kp_units_info->unitname ?? 'N/A' }}</p></td>
                                <td class="text-end text-sm font-weight-bold">{{ number_format($price->price_for_member, 2) }}</td>
                                <td class="text-end text-sm">{{ number_format($price->price_from_dealer, 2) }}</td>
                                <td class="text-end text-sm">{{ number_format($price->point) }}</td>
                                <td class="text-center text-xs">{{ $price->effective_date }}</td>
                                <td class="text-center text-xs">{{ $price->end_date ?: 'ปัจจุบัน' }}</td>
                                <td class="align-middle text-center">
                                    <span class="badge badge-sm bg-gradient-{{ $price->is_active ? 'success' : 'secondary' }}">
                                        {{ $price->status == 'active' ? 'ใช้งาน' : 'ไม่ใช้งาน' }}
                                    </span>
                                </td>
                                <td class="align-middle text-end pe-4 text-nowrap">
                                    <a href="{{ route('keptkayas.tbank.prices.edit', $price->id) }}"
                                        class="btn btn-link text-secondary font-weight-bold text-xs p-0 mb-0 me-3">
                                        <i class="fas fa-edit me-1"></i> แก้ไข
                                    </a>
                                    <form action="{{ route('keptkayas.tbank.prices.destroy', $price->id) }}" method="POST"
                                        class="d-inline" onsubmit="return confirm('คุณแน่ใจหรือไม่ที่จะลบราคานี้?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-link text-danger font-weight-bold text-xs p-0 mb-0">
                                            <i class="fas fa-trash me-1"></i> ลบ
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="10" class="text-center text-secondary py-4">ไม่พบข้อมูลราคารับซื้อ</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-4 pt-3">
                {{ $prices->links() }}
            </div>
        </div>
    </div>
@endsection
