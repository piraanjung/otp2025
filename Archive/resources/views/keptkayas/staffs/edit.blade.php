@extends('layouts.super-admin')

@section('title_page', 'แก้ไขเจ้าหน้าที่')
ิ@section('style')
    <style>
    /* 1. สไตล์พื้นฐานของกล่อง (Unchecked) */
    .flat-check {
        transition: all 0.2s ease;
        border: 1px solid #ebedf0 !important;
        background-color: #ffffff !important;
        cursor: pointer;
        position: relative;
    }
    /* สไตล์ที่คุณเขียนมานั้นถูกต้องและดีมากแล้วครับ */
    .flat-check:has(input:checked) {
        background-color: #f0f5ff !important;
        border-color: #5e72e4 !important;
    }
    .flat-check:has(input:checked) .form-check-label {
        color: #5e72e4 !important;
        font-weight: 700 !important;
    }

    /* 2. เมื่อกล่องถูก "Checked" (เปลี่ยนสีทั้งกล่องให้เด่น) */
    .flat-check:has(input:checked) {
        background-color: #f0f5ff !important; /* สีฟ้าอ่อนๆ ให้รู้ว่าเลือกแล้ว */
        border-color: #5e72e4 !important; /* เส้นขอบสีหลัก */
    }

    /* 3. ปรับตัวอักษรเมื่อ Checked */
    .flat-check:has(input:checked) .form-check-label {
        color: #5e72e4 !important;
        font-weight: 700 !important;
    }

    /* 4. แก้ไขตัว Switch ให้แสดงสีชัดเจนและไม่ซ้อน */
    .flat-check .form-check-input {
        cursor: pointer;
        background-color: #dee2e6 !important;
        border: none !important;
        height: 1.25rem !important;
        width: 2.5rem !important;
    }

    .flat-check .form-check-input:checked {
        background-color: #5e72e4 !important; /* สีตอนเปิด */
        background-image: none !important; /* ปิด SVG ที่ทำให้ซ้อน */
    }

    /* 5. เอฟเฟกต์ตอน Hover */
    .flat-check:hover {
        border-color: #5e72e4 !important;
        background-color: #f8f9fa !important;
    }

    /* สไตล์เดิมของคุณที่ควรคงไว้ */
    .bg-light-gray {
        background-color: #f8f9fa !important;
    }
    .flat-check {
        transition: all 0.2s;
        border: 1px solid #ebedf0 !important;
        cursor: pointer;
    }
    .card-header {
        border-bottom: 1px solid #f1f3f5 !important;
    }
    .card {
        background-color: #ffffff;
    }

    .form-control, .form-select {
        border: 1px solid #e9ecef !important;
        box-shadow: none !important;
        background-color: #ffffff !important;
        transition: border-color 0.2s;
    }

    .form-control:focus, .form-select:focus {
        border-color: #5e72e4 !important;
    }

    .form-control[readonly] {
        background-color: #f8f9fa !important;
        opacity: 1;
    }

    /* ปรับแต่งหัวข้อ Card */
    .card-header h6 {
        color: #344767;
        letter-spacing: -0.025rem;
    }

    /* สไตล์ปุ่มกดส่ง */
    .btn-primary {
        background-color: #5e72e4 !important;
        border: none;
    }
    </style>
@endsection
@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-md-12 mx-auto">
                <div class="card mb-4">
                    <div class="card-header pb-0">
                        <h6>แก้ไขข้อมูลเจ้าหน้าที่: {{ $staff->firstname ?? 'N/A' }}
                            {{ $staff->user->lastname ?? '' }}
                        </h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('keptkayas.staffs.update', $staff->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                                @if(session('success'))
                                <div class="alert alert-success border-0 shadow-none text-white mb-3" role="alert" style="background-color: #2dce89 !important;">
                                    <span class="alert-icon"><i class="fas fa-check-circle me-2"></i></span>
                                    <span class="alert-text"><strong>สำเร็จ!</strong> {{ session('success') }}</span>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif
                            @if($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                                    <span class="alert-text text-white"><strong>เกิดข้อผิดพลาด!</strong>
                                        โปรดตรวจสอบข้อมูลอีกครั้ง</span>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            <div class="mb-3">
                                <label for="user_display" class="form-label">ผู้ใช้งาน (User)</label>
                                <input type="text" class="form-control" id="user_display"
                                    value="{{ $staff->prefix ?? '' }} {{ $staff->firstname ?? 'N/A' }} {{ $staff->lastname ?? '' }} ({{ $staff->email ?? 'N/A' }})"
                                    readonly>
                                <input type="hidden" name="user_id" value="{{ $staff->user_id }}">
                            </div>

                            <div class="mb-3">
                                <label for="status" class="form-label">สถานะเจ้าหน้าที่</label>
                                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status"
                                    required>
                                    <option value="active" {{ old('status', $staff->status) == 'active' ? 'selected' : '' }}>
                                        Active</option>
                                    <option value="inactive" {{ old('status', $staff->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    <option value="suspended" {{ old('status', $staff->status) == 'suspended' ? 'selected' : '' }}>Suspended</option>
                                </select>
                                @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="row">
                                <div class="col-12 mb-4">
                                    <div class="card border shadow-none">
                                        <div class="card-header pb-0 bg-transparent border-bottom">
                                            <h6 class="font-weight-bolder text-dark"><i
                                                    class="fas fa-user-tag me-2 text-primary"></i>บทบาทผู้ใช้งาน (Roles)
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                @foreach($allRoles as $role)
                                                    @php
                                                        $isRoleChecked = (old('roles') && in_array($role->name, old('roles'))) ||
                                                            (!$errors->any() && $staff->hasRole($role->name));
                                                    @endphp
                                                    <div class="col-md-4 col-lg-3 mb-3">
                                                        <div
                                                            class="form-check form-switch flat-check p-3 border border-radius-md bg-light-gray d-flex align-items-center">
                                                            <input class="form-check-input ms-0 mt-0" type="checkbox"
                                                                id="role_{{ $role->id }}" name="roles[]"
                                                                value="{{ $role->name }}" {{ $isRoleChecked ? 'checked' : '' }}>
                                                            <label class="form-check-label mb-0 ms-3 font-weight-bold text-dark"
                                                                for="role_{{ $role->id }}">
                                                                {{ $role->name }}
                                                            </label>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="card border shadow-none">
                                        <div
                                            class="card-header pb-0 bg-transparent border-bottom d-flex justify-content-between">
                                            <h6 class="font-weight-bolder text-dark"><i
                                                    class="fas fa-shield-alt me-2 text-info"></i>สิทธิ์การเข้าถึงโมดูล
                                                (Permissions)</h6>
                                            <small
                                                class="text-secondary">ทำเครื่องหมายหน้าสิทธิ์ที่ต้องการมอบให้เพิ่มเติม</small>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                @foreach($allPermissions as $permission)
                                                    @php
                                                        $isPermChecked = (old('permissions') && in_array($permission->name, old('permissions'))) ||
                                                            (!$errors->any() && $staff->hasDirectPermission($permission->name));
                                                    @endphp
                                                    <div class="col-md-6 col-lg-4 mb-3">
                                                        <div
                                                            class="form-check form-switch flat-check p-3 border border-radius-md bg-white d-flex align-items-center">
                                                            <input class="form-check-input ms-0 mt-0" type="checkbox"
                                                                id="perm_{{ $permission->id }}" name="permissions[]"
                                                                value="{{ $permission->name }}" {{ $isPermChecked ? 'checked' : '' }}>
                                                            <label
                                                                class="form-check-label mb-0 ms-3 text-sm font-weight-bold text-secondary"
                                                                for="perm_{{ $permission->id }}">
                                                                {{ $permission->name }}
                                                            </label>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- <div class="mb-3">
                                <div class="form-check form-switch ps-0">
                                    <input class="form-check-input ms-auto" type="checkbox" id="deleted" name="deleted"
                                        value="1" {{ old('deleted', $staff->deleted) ? 'checked' : '' }}>
                                    <label class="form-check-label text-body ms-3 text-truncate w-80 mb-0" for="deleted">
                                        ทำเครื่องหมายว่าถูกลบ (Deleted)
                                    </label>
                                </div>
                            </div> --}}

                            <div class="d-flex justify-content-end mt-4">
                                <a href="{{ route('keptkayas.staffs.index') }}"
                                    class="btn btn-light border me-2 shadow-none">ยกเลิก</a>
                                <button type="submit" class="btn btn-primary shadow-none px-4">บันทึกการเปลี่ยนแปลง</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
