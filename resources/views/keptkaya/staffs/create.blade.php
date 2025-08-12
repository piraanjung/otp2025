@extends('layouts.keptkaya')

@section('title_page', 'เพิ่มเจ้าหน้าที่ใหม่')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <h6>เพิ่มข้อมูลเจ้าหน้าที่</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('keptkaya.staffs.store') }}" method="POST">
                        @csrf
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
                            <label for="user_id" class="form-label">เลือกผู้ใช้งาน (User)</label>
                            <select class="form-select @error('user_id') is-invalid @enderror" id="user_id" name="user_id" required>
                                <option value="">-- เลือกผู้ใช้งาน --</option>
                                @foreach($eligibleUsers as $user)
                                    <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->prefix ?? '' }} {{ $user->firstname }} {{ $user->lastname }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">สถานะเจ้าหน้าที่</label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="suspended" {{ old('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                            </select>
                            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">สิทธิ์เข้าถึงโมดูล:</label>
                            <div class="row">
                                @foreach($permissions as $permission)
                                    @if(in_array($permission->name, ['access waste bank module', 'access annual collection module'])) {{-- Only show relevant permissions --}}
                                        <div class="col-md-6 col-lg-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox"
                                                       id="perm_{{ Str::slug($permission->name) }}"
                                                       name="permissions[]"
                                                       value="{{ $permission->name }}"
                                                       {{ in_array($permission->name, old('permissions', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="perm_{{ Str::slug($permission->name) }}">
                                                    {{ $permission->name == 'access waste bank module' ? 'เข้าถึงธนาคารขยะ' : '' }}
                                                    {{ $permission->name == 'access annual collection module' ? 'เข้าถึงจัดเก็บรายปี' : '' }}
                                                </label>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn bg-gradient-primary me-2">บันทึกเจ้าหน้าที่</button>
                            <a href="{{ route('keptkaya.staffs.index') }}" class="btn bg-gradient-secondary">ยกเลิก</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
