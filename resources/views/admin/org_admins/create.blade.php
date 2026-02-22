@extends('layouts.super-admin')

@section('title', 'เพิ่มผู้ดูแลหน่วยงานใหม่')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0"><i class="fas fa-user-plus"></i> เพิ่มผู้ดูแลประจำหน่วยงาน</h4>
                    </div>
                    <div class="card-body">

                        {{-- แสดง Error รวม (ถ้ามี) --}}
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('org-admins.store') }}" method="POST">
                            @csrf

                            {{-- ส่วนที่ 1: ข้อมูลสังกัด --}}
                            <h6 class="heading-small text-muted mb-4">ข้อมูลหน่วยงานสังกัด</h6>
                            <div class="pl-lg-4">
                                <div class="form-group">
                                    <label class="form-control-label">เลือกหน่วยงาน / เทศบาล <span
                                            class="text-danger">*</span></label>
                                    <select name="org_id_fk" class="form-control @error('org_id_fk') is-invalid @enderror"
                                        required>
                                        <option value="">-- กรุณาเลือกหน่วยงาน --</option>
                                        @foreach($organizations as $org)
                                            <option value="{{ $org->id }}" {{ old('org_id_fk') == $org->id ? 'selected' : '' }}>
                                                {{ $org->org_type_name }} {{ $org->org_name }}
                                                ({{ $org->provinces->province_name ?? 'ไม่ระบุจังหวัด' }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('org_id_fk')
                                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label>ค้นหา User ที่มีอยู่แล้ว (ชื่อ, Username, Email) <span
                                            class="text-danger">*</span></label>
                                    <select class="form-control select2-user-ajax" name="user_id" required>
                                        <option value="">-- พิมพ์เพื่อค้นหา --</option>
                                    </select>
                                </div>

                                <div id="selected-user-info" class="alert alert-secondary mt-3" style="display:none;">
                                    <strong>User ที่เลือก:</strong> <span id="display-name"></span>
                                </div>
                            </div>

                            <hr class="my-4" />

                            {{-- ส่วนที่ 2: ข้อมูลส่วนตัว --}}
                            <h6 class="heading-small text-muted mb-4">ข้อมูลผู้ใช้งาน (Admin Info)</h6>
                            <div class="pl-lg-4">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label class="form-control-label">ชื่อ<span class="text-danger">*</span></label>
                                            <input type="text" name="firstname" class="form-control"
                                                value="{{ old('firstname') }}" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label class="form-control-label">นามสกุล <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" name="lastname" class="form-control"
                                                value="{{ old('lastname') }}" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label class="form-control-label">เบอร์โทรศัพท์</label>
                                            <input type="text" name="phone" class="form-control" placeholder="08x-xxxxxxx"
                                                value="{{ old('phone') }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label class="form-control-label">Username (สำหรับเข้าระบบ) <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" name="username"
                                                class="form-control @error('username') is-invalid @enderror"
                                                value="{{ old('username') }}" required>
                                            @error('username') <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label class="form-control-label">Email <span
                                                    class="text-danger">*</span></label>
                                            <input type="email" name="email"
                                                class="form-control @error('email') is-invalid @enderror"
                                                value="{{ old('email') }}" required>
                                            @error('email') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label class="form-control-label">Password <span
                                                    class="text-danger">*</span></label>
                                            <input type="password" name="password"
                                                class="form-control @error('password') is-invalid @enderror" required
                                                minlength="8">
                                            @error('password') <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label class="form-control-label">Confirm Password <span
                                                    class="text-danger">*</span></label>
                                            <input type="password" name="password_confirmation" class="form-control"
                                                required>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer text-right">
                                <a href="{{ route('org-admins.index') }}" class="btn btn-secondary">ยกเลิก</a>
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save"></i> บันทึกข้อมูล
                                </button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function () {
            $('.select2-user-ajax').select2({
                theme: 'bootstrap4', // หรือ theme ที่คุณใช้
                placeholder: 'พิมพ์ชื่อ หรือ Username เพื่อค้นหา...',
                minimumInputLength: 2, // พิมพ์ 2 ตัวค่อยค้นหา
                ajax: {
                    url: '{{ route("ajax.users.search") }}',
                    dataType: 'json',
                    delay: 250, // หน่วงเวลาพิมพ์นิดนึง
                    data: function (params) {
                        return {
                            q: params.term // ส่งคำค้นหาไปที่ Controller
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data // data ต้องเป็น array ของ object {id, text}
                        };
                    },
                    cache: true
                }
            });

            // (ลูกเล่นเสริม) เมื่อเลือกแล้ว ให้โชว์ชื่อยืนยันอีกที
            $('.select2-user-ajax').on('select2:select', function (e) {
                var data = e.params.data;
                $('#display-name').text(data.text);
                $('#selected-user-info').show();
            });
        });
    </script>
@endsection
