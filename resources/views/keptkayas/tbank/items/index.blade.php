@extends('layouts.keptkaya')
@section('nav-header', 'ตั้งค่าข้อมูล')
@section('nav-current', 'ข้อมูลขยะรีไซเคิล')
@section('page-topic', 'ข้อมูลขยะรีไซเคิล')

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
                <h6 class="mb-lg-0 mb-3">รายการขยะรีไซเคิลในระบบ</h6>
                <div class="d-flex flex-wrap gap-2">
                    <a class="btn bg-gradient-primary btn-sm mb-0" href="{{ route('keptkayas.tbank.items.create') }}">
                        <i class="fas fa-plus me-1"></i> สร้างข้อมูล
                    </a>
                    <a class="btn btn-outline-warning btn-sm mb-0" href="{{ route('keptkayas.tbank.items.pendingEf') }}">
                        <i class="fas fa-leaf me-1"></i> รายการที่ยังไม่มี EF
                    </a>
                    <a class="btn btn-outline-danger btn-sm mb-0" href="{{ route('keptkayas.tbank.items.trash') }}">
                        <i class="fas fa-trash me-1"></i> ถังขยะ
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body px-0 pt-3 pb-2">
            <div class="table-responsive p-0">
                <table class="table align-items-center mb-0">
                    <thead>
                        <tr>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-4">รายการ</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">รหัส</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">หน่วย (Bank / Kiosk)</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ค่าคาร์บอน (EF)</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">สถานะ</th>
                            <th class="text-secondary opacity-7"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($kp_tbank_items as $item)
                            <tr>
                                <td>
                                    <div class="d-flex px-3 py-1">
                                        <div>
                                            @if ($item->image)
                                                <img src="{{ asset('keptkaya/items/' . $item->image) }}"
                                                    class="avatar avatar-sm me-3 border-radius-lg" style="object-fit: cover;" alt="">
                                            @else
                                                <div class="avatar avatar-sm me-3 border-radius-lg bg-gray-200">
                                                    <i class="fas fa-image text-secondary"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="d-flex flex-column justify-content-center">
                                            <h6 class="mb-0 text-sm">{{ $item->kp_itemsname }}</h6>
                                        </div>
                                    </div>
                                </td>
                                <td><p class="text-xs font-weight-bold mb-0">{{ $item->kp_itemscode }}</p></td>
                                <td class="align-middle text-center text-sm">
                                    <span class="badge badge-sm bg-gradient-info" title="หน่วยธนาคาร">{{ $item->unitBank->unit_short_name ?? 'N/A' }}</span>
                                    <i class="fas fa-arrow-right mx-1 text-secondary text-xs"></i>
                                    <span class="badge badge-sm bg-gradient-warning" title="หน่วยตู้ Kiosk">{{ $item->unitKiosk->unit_short_name ?? '-' }}</span>
                                </td>
                                <td class="align-middle text-center">
                                    @if ($item->emissionFactor)
                                        <span class="text-success text-sm font-weight-bold">{{ number_format($item->emissionFactor->ef_value, 4) }}</span>
                                        <p class="text-xxs text-secondary mb-0">kgCO2e/kg</p>
                                    @else
                                        <span class="badge badge-sm bg-gradient-secondary">ยังไม่ได้ตั้งค่า</span>
                                    @endif
                                </td>
                                <td class="align-middle text-center">
                                    <span class="badge badge-sm bg-gradient-{{ $item->status == 'active' ? 'success' : 'danger' }}">
                                        {{ $item->status == 'active' ? 'เปิดใช้งาน' : 'ปิดใช้งาน' }}
                                    </span>
                                </td>
                                <td class="align-middle text-end pe-4">
                                    <a class="btn btn-link text-secondary font-weight-bold text-xs p-0 mb-0 me-3"
                                        href="{{ route('keptkayas.tbank.items.edit', $item->id) }}">
                                        <i class="fas fa-edit me-1"></i> แก้ไข
                                    </a>
                                    <form action="{{ route('keptkayas.tbank.items.destroy', $item->id) }}" method="POST"
                                        class="d-inline" onsubmit="return confirm('คุณมั่นใจหรือไม่ที่จะลบรายการนี้?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-link text-danger font-weight-bold text-xs p-0 mb-0">
                                            <i class="fas fa-trash me-1"></i> ลบ
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-secondary py-4">ยังไม่มีรายการขยะรีไซเคิล</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
