@extends('layouts.keptkaya')

@section('title_page', 'แก้ไขเจ้าหน้าที่')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <h6>แก้ไขข้อมูลเจ้าหน้าที่: {{ $staff->user->firstname ?? 'N/A' }} {{ $staff->user->lastname ?? '' }}</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('keptkaya.staffs.update', $staff->user_id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
                                <span class="alert-text text-white"><strong>สำเร็จ!</strong> {{ session('success') }}</span>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif
                        @if($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                                <span class="alert-text text-white"><strong>เกิดข้อผิดพลาด!</strong> โปรดตรวจสอบข้อมูลอีกครั้ง</span>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        <div class="mb-3">
                            <label for="user_display" class="form-label">ผู้ใช้งาน (User)</label>
                            <input type="text" class="form-control" id="user_display" value="{{ $staff->user->prefix ?? '' }} {{ $staff->user->firstname ?? 'N/A' }} {{ $staff->user->lastname ?? '' }} ({{ $staff->user->email ?? 'N/A' }})" readonly>
                            <input type="hidden" name="user_id" value="{{ $staff->user_id }}">
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">สถานะเจ้าหน้าที่</label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="active" {{ old('status', $staff->status) == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', $staff->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="suspended" {{ old('status', $staff->status) == 'suspended' ? 'selected' : '' }}>Suspended</option>
                            </select>
                            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">สิทธิ์เข้าถึงโมดูล:</label>
                            <div class="row">
                                @foreach($permissions as $permission)
                                    @php
                                        // Check if the permission should be displayed in this form
                                        $isRelevantPermission = in_array($permission->name, ['access waste bank', 'access annual collection']);
                                        // Check if the permission was old input or if the user currently has it
                                        $isChecked = (old('permissions') && in_array($permission->name, old('permissions'))) ||
                                                     (!$errors->any() && $staff->user->hasPermissionTo($permission->name));
                                    @endphp
                                    @if($isRelevantPermission)
                                        <div class="col-md-6 col-lg-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox"
                                                       id="perm_{{ Str::slug($permission->name) }}"
                                                       name="permissions[]"
                                                       value="{{ $permission->name }}"
                                                       {{ $isChecked ? 'checked' : '' }}>
                                                <label class="form-check-label" for="perm_{{ Str::slug($permission->name) }}">
                                                    {{ $permission->name == 'access waste bank' ? 'เข้าถึงธนาคารขยะ' : '' }}
                                                    {{ $permission->name == 'access annual collection' ? 'เข้าถึงจัดเก็บรายปี' : '' }}
                                                </label>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch ps-0">
                                <input class="form-check-input ms-auto" type="checkbox" id="deleted" name="deleted" value="1" {{ old('deleted', $staff->deleted) ? 'checked' : '' }}>
                                <label class="form-check-label text-body ms-3 text-truncate w-80 mb-0" for="deleted">
                                    ทำเครื่องหมายว่าถูกลบ (Deleted)
                                </label>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn bg-gradient-primary me-2">บันทึกการเปลี่ยนแปลง</button>
                            <a href="{{ route('keptkaya.staffs.index') }}" class="btn bg-gradient-secondary">ยกเลิก</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
