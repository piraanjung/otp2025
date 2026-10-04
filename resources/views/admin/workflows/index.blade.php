@extends('layouts.super-admin')

@section('title_page', 'จัดการสายการอนุมัติ')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-0">จัดการสายการอนุมัติ (Workflows)</h6>
                    <p class="text-sm text-secondary mb-0">กำหนดลำดับผู้อนุมัติสำหรับการเบิกพัสดุ</p>
                </div>
                <a href="{{ route('admin.workflows.create') }}" class="btn bg-gradient-primary btn-sm mb-0">
                    <i class="fas fa-plus me-1"></i> สร้างสายอนุมัติใหม่
                </a>
            </div>
            <div class="card-body px-0 pt-0 pb-2">
                <div class="table-responsive p-0">
                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ชื่อสายการอนุมัติ</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">คำอธิบาย</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">ขั้นตอน</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">สถานะ</th>
                                <th class="text-secondary opacity-7"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($workflows as $wf)
                                <tr>
                                    <td class="ps-4"><h6 class="mb-0 text-sm">{{ $wf->name }}</h6></td>
                                    <td><p class="text-xs text-secondary mb-0">{{ $wf->description ?: '-' }}</p></td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-1">{{ $wf->steps_count }} ขั้นตอน</p>
                                        @foreach ($wf->steps as $step)
                                            <span class="badge badge-sm bg-gradient-info">{{ $step->step_order . '. ' . $step->role_name }}</span>
                                        @endforeach
                                    </td>
                                    <td class="align-middle text-center text-sm">
                                        <span class="badge badge-sm bg-gradient-{{ $wf->is_active ? 'success' : 'secondary' }}">
                                            {{ $wf->is_active ? 'เปิดใช้งาน' : 'ปิดใช้งาน' }}
                                        </span>
                                    </td>
                                    <td class="align-middle text-end pe-4">
                                        <a href="{{ route('admin.workflows.edit', $wf->id) }}"
                                            class="text-secondary font-weight-bold text-xs me-3">
                                            <i class="fas fa-edit me-1"></i> ตั้งค่าขั้นตอน / แก้ไข
                                        </a>
                                        <form action="{{ route('admin.workflows.destroy', $wf->id) }}" method="POST" class="d-inline"
                                            onsubmit="return confirm('ยืนยันการลบสายการอนุมัติ &quot;{{ $wf->name }}&quot; ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-link text-danger font-weight-bold text-xs p-0 mb-0">
                                                <i class="fas fa-trash me-1"></i> ลบ
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-secondary py-4">ยังไม่มีข้อมูลสายการอนุมัติ</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
