@extends('inventory.inv_master')

@section('content')
<div class="container">
    <h2>{{ isset($workflow) ? 'แก้ไขสายการอนุมัติ: ' . $workflow->name : 'สร้างสายการอนุมัติใหม่' }}</h2>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- ฟอร์มหลัก (ส่งข้อมูลทั้งชื่อ และ Array ของ Steps ไปพร้อมกัน) -->
    <form action="{{ isset($workflow) ? route('admin.workflows.update', $workflow->id) : route('admin.workflows.store') }}" method="POST">
        @csrf
        @if(isset($workflow))
            @method('PUT')
        @endif

        <!-- ข้อมูลหลัก -->
        <div class="card card-body bg-light mb-4">
            <div class="mb-3">
                <label class="form-label">ชื่อสายการอนุมัติ</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $workflow->name ?? '') }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label">คำอธิบาย</label>
                <textarea name="description" class="form-control">{{ old('description', $workflow->description ?? '') }}</textarea>
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" name="is_active" class="form-check-input" id="is_active" value="1" {{ (isset($workflow) && $workflow->is_active) && !isset($workflow) ? 'checked' : '' }}>
                <label class="form-check-label" for="is_active">เปิดใช้งาน</label>
            </div>
        </div>

        <hr>

        <!-- ส่วนจัดการขั้นตอนการอนุมัติ (Steps) แบบ Array -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4>กำหนดลำดับขั้นตอนการอนุมัติ (Steps)</h4>
            <button type="button" class="btn btn-primary btn-sm" id="addRowBtn">+ เพิ่มขั้นตอน</button>
        </div>

        <table class="table table-bordered table-striped mb-4" id="stepsTable">
            <thead>
                <tr>
                    <th style="width: 15%;">ลำดับขั้นที่</th>
                    <th>ผู้ออนุมัติ (Role / ตำแหน่ง หรือ บุคคล)</th>
                    <th style="width: 10%; text-align: center;">จัดการ</th>
                </tr>
            </thead>
            <tbody>
                <!-- กรณีหน้า Edit: แสดงข้อมูลเดิมที่มีอยู่ -->
                @if(isset($workflow))
                    @foreach($workflow->steps->sortBy('step_order') as $index => $step)
                        <tr>
                            <td>
                                <input type="number" name="steps[{{ $index }}][step_order]" class="form-control" value="{{ $step->step_order }}" required>
                            </td>
                            <td>
                                <div class="row">
                                    <div class="col-md-6">
                                        <select name="steps[{{ $index }}][role_name]" class="form-control">
                                            <option value="">-- เลือก Role --</option>
                                            @foreach($roles as $role)
                                                <option value="{{ $role->name }}" {{ $step->role_name == $role->name ? 'selected' : '' }}>{{ $role->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <select name="steps[{{ $index }}][specific_user_id]" class="form-control">
                                            <option value="">-- หรือเลือก User --</option>
                                            @foreach($users as $user)
                                                <option value="{{ $user->id }}" {{ $step->specific_user_id == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-danger btn-sm removeRowBtn">ลบ</button>
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>

        <div class="d-flex justify-content-between">
            <a href="{{ route('admin.workflows.index') }}" class="btn btn-secondary">ย้อนกลับ</a>
            <button type="submit" class="btn btn-success">บันทึกข้อมูลทั้งหมด</button>
        </div>
    </form>
</div>

<!-- JavaScript สำหรับเพิ่ม/ลบแถวแบบ Dynamic -->
<script>
    let rowIndex = {{ isset($workflow) ? $workflow->steps->count() : 0 }};

    // ส่งตัวแปร Roles และ Users ไปให้ Javascript ใช้สร้าง Dropdown
    const rolesList = @json($roles);
    const usersList = @json($users);

    document.getElementById('addRowBtn').addEventListener('click', function () {
        let tableBody = document.querySelector('#stepsTable tbody');
        
        let roleOptions = '<option value="">-- เลือก Role --</option>';
        rolesList.forEach(role => {
            roleOptions += `<option value="${role.name}">${role.name}</option>`;
        });

        let userOptions = '<option value="">-- หรือเลือก User --</option>';
        usersList.forEach(user => {
            userOptions += `<option value="${user.id}">${user.name}</option>`;
        });

        let newRow = `
            <tr>
                <td>
                    <input type="number" name="steps[${rowIndex}][step_order]" class="form-control" value="${rowIndex + 1}" required>
                </td>
                <td>
                    <div class="row">
                        <div class="col-md-6">
                            <select name="steps[${rowIndex}][role_name]" class="form-control">
                                ${roleOptions}
                            </select>
                        </div>
                        <div class="col-md-6">
                            <select name="steps[${rowIndex}][specific_user_id]" class="form-control">
                                ${userOptions}
                            </select>
                        </div>
                    </div>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-danger btn-sm removeRowBtn">ลบ</button>
                </td>
            </tr>
        `;

        tableBody.insertAdjacentHTML('beforeend', newRow);
        rowIndex++;
    });

    // ปุ่มลบแถว
    document.addEventListener('click', function (e) {
        if (e.target && e.target.classList.contains('removeRowBtn')) {
            e.target.closest('tr').remove();
        }
    });
</script>
@endsection