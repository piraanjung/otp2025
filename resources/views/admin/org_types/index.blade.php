@extends('layouts.super-admin')

@section('title_page', 'ประเภทหน่วยงาน')

@section('content')
<div class="row">
    <div class="col-lg-8 mb-4">
        <div class="card">
            <div class="card-header pb-0">
                <h6>ประเภทหน่วยงานทั้งหมด</h6>
            </div>
            <div class="card-body px-0 pt-0 pb-2">
                <div class="table-responsive p-0">
                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ชื่อประเภท</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">รหัส</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">สถานะ</th>
                                <th class="text-secondary opacity-7"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($types as $type)
                                <tr>
                                    <td><div class="d-flex px-3"><h6 class="mb-0 text-sm">{{ $type->name }}</h6></div></td>
                                    <td><p class="text-xs font-weight-bold mb-0">{{ $type->code ?: '-' }}</p></td>
                                    <td class="align-middle text-center">
                                        <span class="badge badge-sm bg-gradient-{{ $type->status ? 'success' : 'secondary' }}">
                                            {{ $type->status ? 'ใช้งาน' : 'ปิด' }}
                                        </span>
                                    </td>
                                    <td class="align-middle text-end pe-4">
                                        <button type="button" class="btn btn-link text-secondary font-weight-bold text-xs p-0 mb-0 me-3"
                                            data-bs-toggle="modal" data-bs-target="#editModal{{ $type->id }}">
                                            <i class="fas fa-edit me-1"></i> แก้ไข
                                        </button>
                                        <form action="{{ route('admin.org-types.destroy', $type->id) }}" method="POST" class="d-inline"
                                            onsubmit="return confirm('ยืนยันการลบประเภท &quot;{{ $type->name }}&quot; ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-link text-danger font-weight-bold text-xs p-0 mb-0">
                                                <i class="fas fa-trash me-1"></i> ลบ
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-secondary py-4">ยังไม่มีประเภทหน่วยงาน</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-header pb-0"><h6>เพิ่มประเภทใหม่</h6></div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger text-white text-sm py-2">{{ $errors->first() }}</div>
                @endif
                <form action="{{ route('admin.org-types.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">ชื่อประเภท (เช่น อบต.)</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">รหัสย่อ</label>
                        <input type="text" name="code" class="form-control" value="{{ old('code') }}">
                    </div>
                    <button type="submit" class="btn bg-gradient-primary w-100">บันทึกข้อมูล</button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- modal แก้ไข (เดิมมีปุ่มแก้ไขแต่ไม่มี modal และไม่ได้โหลด bootstrap js จึงกดแล้วไม่เกิดอะไร) --}}
@foreach ($types as $type)
    <div class="modal fade" id="editModal{{ $type->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form class="modal-content" action="{{ route('admin.org-types.update', $type->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h6 class="modal-title">แก้ไขประเภทหน่วยงาน</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">ชื่อประเภท</label>
                        <input type="text" name="name" class="form-control" value="{{ $type->name }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">รหัสย่อ</label>
                        <input type="text" name="code" class="form-control" value="{{ $type->code }}">
                    </div>
                    <div class="mb-0">
                        <label class="form-label">สถานะ</label>
                        <select name="status" class="form-select">
                            <option value="1" @selected($type->status)>ใช้งาน</option>
                            <option value="0" @selected(! $type->status)>ปิด</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary mb-0" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn bg-gradient-primary mb-0">บันทึก</button>
                </div>
            </form>
        </div>
    </div>
@endforeach
@endsection
