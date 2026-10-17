@extends('layouts.keptkaya')
@section('page-topic', 'หน่วยนับสินค้า')
@section('nav-header', 'ตั้งค่าข้อมูล')
@section('nav-current', 'ข้อมูลหน่วยนับสินค้า')

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show text-white" role="alert">
            <span class="alert-text">{{ session('success') }}</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-header pb-0 d-flex justify-content-between align-items-center">
            <h6 class="mb-0">รายการหน่วยนับ</h6>
            <a href="{{ route('keptkayas.tbank.units.create') }}" class="btn bg-gradient-primary btn-sm mb-0">
                <i class="fas fa-plus me-1"></i> เพิ่มหน่วยนับสินค้า
            </a>
        </div>
        <div class="card-body px-0 pt-3 pb-2">
            <div class="table-responsive p-0">
                <table class="table align-items-center mb-0">
                    <thead>
                        <tr>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-4">รหัส</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">ชื่อหน่วยนับ</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">ชื่อย่อ</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">สถานะ</th>
                            <th class="text-secondary opacity-7"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($units as $unit)
                            <tr>
                                <td class="ps-4"><p class="text-xs font-weight-bold mb-0">{{ $unit->id }}</p></td>
                                <td><h6 class="mb-0 text-sm">{{ $unit->unitname }}</h6></td>
                                <td><p class="text-xs font-weight-bold mb-0">{{ $unit->unit_short_name }}</p></td>
                                <td class="align-middle text-center">
                                    <span class="badge badge-sm bg-gradient-{{ $unit->status == 'active' ? 'success' : 'secondary' }}">
                                        {{ $unit->status == 'active' ? 'ใช้งาน' : 'ปิดใช้งาน' }}
                                    </span>
                                </td>
                                <td class="align-middle text-end pe-4">
                                    <a href="{{ route('keptkayas.tbank.units.show', $unit) }}"
                                        class="btn btn-link text-info font-weight-bold text-xs p-0 mb-0 me-3">
                                        <i class="fas fa-eye me-1"></i> ดู
                                    </a>
                                    <a href="{{ route('keptkayas.tbank.units.edit', $unit) }}"
                                        class="btn btn-link text-secondary font-weight-bold text-xs p-0 mb-0 me-3">
                                        <i class="fas fa-edit me-1"></i> แก้ไข
                                    </a>
                                    <form action="{{ route('keptkayas.tbank.units.destroy', $unit) }}" method="POST"
                                        class="d-inline" onsubmit="return confirm('ยืนยันการลบหน่วยนับนี้?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-link text-danger font-weight-bold text-xs p-0 mb-0">
                                            <i class="fas fa-trash me-1"></i> ลบ
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-secondary py-4">ยังไม่มีหน่วยนับ</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
