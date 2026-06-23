@extends('layouts.super-admin')

@section('title', 'แก้ไขข้อมูลผู้ดูแล')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-warning">
                    <h4 class="mb-0 text-white"><i class="fas fa-user-edit"></i> แก้ไขข้อมูลผู้ดูแล: {{ $user->name }}</h4>
                </div>
                <div class="card-body">

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('org-admins.update', $user->id) }}" method="POST">
                        @csrf
                        @method('PUT') {{-- สำคัญมากสำหรับการ Update --}}

                        {{-- ส่วนที่ 1: ข้อมูลสังกัด --}}
                        <h6 class="heading-small text-muted mb-4">ข้อมูลหน่วยงานสังกัด</h6>
                        <div class="pl-lg-4">
                            <div class="form-group">
                                <label class="form-control-label">เลือกหน่วยงาน / เทศบาล <span class="text-danger">*</span></label>
                                <select name="org_id_fk" class="form-control @error('org_id_fk') is-invalid @enderror" required>
                                    <option value="">-- กรุณาเลือกหน่วยงาน --</option>
                                    @foreach($organizations as $org)
                                        <option value="{{ $org->id }}"
                                            {{ (old('org_id_fk', $user->org_id_fk) == $org->id) ? 'selected' : '' }}>
                                            {{ $org->org_type_name }} {{ $org->org_name }} {{ $org->provinces->province_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <hr class="my-4" />

                        {{-- ส่วนที่ 2: ข้อมูลส่วนตัว --}}
                        <h6 class="heading-small text-muted mb-4">ข้อมูลผู้ใช้งาน</h6>
                        <div class="pl-lg-4">
                            <div class="row">
                                  <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="form-control-label">ชื่อ<span class="text-danger">*</span></label>
                                        <input type="text" name="firstname" class="form-control"  value="{{ old('firstname',  $user->firstname) }}" required>
                                    </div>
                                </div>
                                  <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="form-control-label">นามสกุล <span class="text-danger">*</span></label>
                                        <input type="text" name="lastname" class="form-control" value="{{ old('lastname',  $user->lastname) }}" required>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="form-control-label">เบอร์โทรศัพท์</label>
                                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="form-control-label">Username</label>
                                        <input type="text" name="username" class="form-control @error('username') is-invalid @enderror" value="{{ old('username', $user->username) }}" required>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="form-control-label">Email</label>
                                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                                    </div>
                                </div>
                            </div>

                            {{-- ส่วนรหัสผ่าน (เว้นว่างได้) --}}
                            <div class="row mt-3 p-3 bg-light rounded">
                                <div class="col-12">
                                    <small class="text-danger font-weight-bold">** เปลี่ยนรหัสผ่าน (ปล่อยว่างไว้ถ้าไม่ต้องการเปลี่ยน)</small>
                                </div>
                                <div class="col-lg-6 mt-2">
                                    <div class="form-group">
                                        <label class="form-control-label">New Password</label>
                                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" autocomplete="new-password">
                                        @error('password') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6 mt-2">
                                    <div class="form-group">
                                        <label class="form-control-label">Confirm New Password</label>
                                        <input type="password" name="password_confirmation" class="form-control">
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="card-footer text-right mt-3">
                            <a href="{{ route('org-admins.index') }}" class="btn btn-secondary">ยกเลิก</a>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save"></i> อัพเดทข้อมูล
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
