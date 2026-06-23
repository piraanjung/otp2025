@extends('layouts.keptkaya')
@section('nav-header', 'สมาชิก ธนาคารขยะ, จัดการถังขยะรายปี')
@section('nav-current', 'เพิ่มสมาชิก ธนาคารขยะ, จัดการถังขยะรายปี')
@section('page-topic', 'เพิ่มสมาชิก ธนาคารขยะ, จัดการถังขยะรายปี')


@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-10 mx-auto">
            <div class="card mb-4">
                <div class="card-header pb-0 p-3">
                    <h6 class="mb-0">เพิ่มสมาชิกธนาคารขยะ</h6>
                </div>
                <div class="card-body p-3">

                    <ul class="nav nav-pills nav-fill p-1" id="pills-tab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="pills-manual-tab" data-bs-toggle="pill" data-bs-target="#pills-manual" type="button" role="tab" aria-controls="pills-manual" aria-selected="true">
                                📝 เพิ่มสมาชิกใหม่ (กรอกข้อมูล)
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="pills-table-tab" data-bs-toggle="pill" data-bs-target="#pills-table" type="button" role="tab" aria-controls="pills-table" aria-selected="false">
                                📊 ดึงจากผู้ใช้งานที่มีอยู่ (เลือกจากตาราง)
                            </button>
                        </li>
                    </ul>
                    <hr class="mt-2 mb-4">

                    <div class="tab-content" id="pills-tabContent">

                        <div class="tab-pane fade show active" id="pills-manual" role="tabpanel" aria-labelledby="pills-manual-tab">
                            <form action="{{ route('keptkayas.users.store') }}" method="POST">
                                @csrf
                                {{-- Hidden field เพื่อระบุโหมดการบันทึก --}}
                                <input type="hidden" name="mode" value="manual">

                                {{-- โค้ดฟอร์มเดิมของคุณทั้งหมด --}}
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="username" class="form-label">ชื่อผู้ใช้งาน</label>
                                            <input type="text" class="form-control @error('username') is-invalid @enderror" id="username" name="username" value="{{ old('username') }}" required>
                                            @error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="email" class="form-label">อีเมล</label>
                                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="prefix" class="form-label">คำนำหน้า</label>
                                            <input type="text" class="form-control @error('prefix') is-invalid @enderror" id="prefix" name="prefix" value="{{ old('prefix') }}">
                                            @error('prefix')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="firstname" class="form-label">ชื่อจริง</label>
                                            <input type="text" class="form-control @error('firstname') is-invalid @enderror" id="firstname" name="firstname" value="{{ old('firstname') }}" required>
                                            @error('firstname')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="lastname" class="form-label">นามสกุล</label>
                                            <input type="text" class="form-control @error('lastname') is-invalid @enderror" id="lastname" name="lastname" value="{{ old('lastname') }}" required>
                                            @error('lastname')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="password" class="form-label">รหัสผ่าน</label>
                                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="password_confirmation" class="form-label">ยืนยันรหัสผ่าน</label>
                                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="id_card" class="form-label">เลขบัตรประชาชน</label>
                                            <input type="text" class="form-control @error('id_card') is-invalid @enderror" id="id_card" name="id_card" value="{{ old('id_card') }}">
                                            @error('id_card')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="phone" class="form-label">เบอร์โทรศัพท์</label>
                                            <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}">
                                            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="gender" class="form-label">เพศ</label>
                                            <select class="form-select @error('gender') is-invalid @enderror" id="gender" name="gender">
                                                <option value="">เลือกเพศ</option>
                                                <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>ชาย</option>
                                                <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>หญิง</option>
                                                <option value="Other" {{ old('gender') == 'Other' ? 'selected' : '' }}>อื่นๆ</option>
                                            </select>
                                            @error('gender')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="status" class="form-label">สถานะผู้ใช้</label>
                                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                                <option value="banned" {{ old('status') == 'banned' ? 'selected' : '' }}>Banned</option>
                                            </select>
                                            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="address" class="form-label">ที่อยู่</label>
                                    <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="3">{{ old('address') }}</textarea>
                                    @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                {{-- สิ้นสุดโค้ดฟอร์มเดิม --}}

                                <button type="submit" class="btn bg-gradient-primary mt-3">บันทึกผู้ใช้งานใหม่</button>
                                <a href="{{ route('keptkayas.users.index') }}" class="btn btn-secondary mt-3">ยกเลิก</a>
                            </form>
                        </div>

                        <div class="tab-pane fade" id="pills-table" role="tabpanel" aria-labelledby="pills-table-tab">
                            <form action="{{ route('keptkayas.users.store') }}" method="POST">
                                @csrf
                                {{-- Hidden field เพื่อระบุโหมดการบันทึก --}}
                                <input type="hidden" name="mode" value="batch_select">

                                <p class="text-sm text-muted">เลือกผู้ใช้งานที่มี Role เป็น User และยังไม่ได้เป็นสมาชิกธนาคารขยะ เพื่อเพิ่มเป็นสมาชิก</p>
                                <button type="submit" class="btn bg-gradient-success mb-4" id="batch-add-btn" disabled>
                                    เพิ่มสมาชิกที่เลือก
                                </button>
                                <div class="table-responsive">
                                    <table class="table align-items-center mb-0">
                                        <thead>
                                            <tr>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                    เลือก
                                                </th>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                    ชื่อผู้ใช้งาน
                                                </th>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                    อีเมล
                                                </th>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                    ชื่อ-นามสกุล
                                                </th>
                                                <th class="text-secondary opacity-7"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($nonMemberUsers as $user)
                                                <tr>
                                                    <td>
                                                        <div class="form-check text-center">
                                                            <input class="form-check-input user-select-checkbox" type="checkbox" name="selected_user_ids[]" value="{{ $user->id }}">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <p class="text-xs font-weight-bold mb-0">{{ $user->username }}</p>
                                                    </td>
                                                    <td>
                                                        <p class="text-xs text-secondary mb-0">{{ $user->email }}</p>
                                                    </td>
                                                    <td>
                                                        <span class="text-secondary text-xs font-weight-bold">{{ $user->firstname }} {{ $user->lastname }}</span>
                                                    </td>
                                                    <td class="align-middle">
                                                        <a href="#" class="text-secondary font-weight-bold text-xs" data-toggle="tooltip" data-original-title="Edit user">
                                                            ...
                                                        </a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center text-muted">
                                                        ไม่พบผู้ใช้งานที่มี Role เป็น User และยังไม่ได้เป็นสมาชิกธนาคารขยะ
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkboxes = document.querySelectorAll('.user-select-checkbox');
        const batchAddBtn = document.getElementById('batch-add-btn');

        // Function to update the button's disabled state
        function updateBatchButtonState() {
            let checkedCount = 0;
            checkboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    checkedCount++;
                }
            });
            // Enable button only if at least one checkbox is checked
            batchAddBtn.disabled = checkedCount === 0;
        }

        // Add event listeners to all checkboxes
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateBatchButtonState);
        });

        // Initial check
        updateBatchButtonState();
    });
</script>
@endsection
