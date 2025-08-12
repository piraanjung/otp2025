@extends('layouts.keptkaya')

@section('nav-payment')
    {{-- เมนู Nav ของคุณ --}}
@endsection
@section('nav-header')
    จัดการอัตราค่าบริการ
@endsection
@section('nav-main')
    <a href="{{ route('keptkaya.payrate_per_months.index') }}">อัตราค่าบริการรายปี</a>
@endsection
@section('nav-current')
    แก้ไขอัตราค่าบริการ
@endsection
@section('page-topic')
    แก้ไขอัตราค่าบริการ
@endsection

@section('content')
<div class="row mt-4">
    <div class="col-lg-8 col-md-10 mx-auto">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title mb-0">แก้ไขอัตราค่าบริการ: <span class="text-primary">{{ $payrate->kpUsergroup->name ?? 'N/A' }} ปี {{ $payrate->budgetYear->year ?? 'N/A' }}</span></h3>
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>เกิดข้อผิดพลาด!</strong> โปรดตรวจสอบข้อมูลที่กรอก:
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form action="{{ route('keptkaya.payrate_per_months.update', $payrate->id) }}" method="POST">
                    @csrf
                    @method('PUT') {{-- สำคัญมากสำหรับการอัปเดต --}}

                    <div class="mb-3">
                        <label for="kp_usergroup_idfk" class="form-label">กลุ่มผู้ใช้ <span class="text-danger">*</span></label>
                        <select class="form-select" id="kp_usergroup_idfk" name="kp_usergroup_idfk" required>
                            <option value="">เลือกกลุ่มผู้ใช้</option>
                            @foreach ($usergroups as $group)
                                <option value="{{ $group->id }}" {{ (old('kp_usergroup_idfk', $payrate->kp_usergroup_idfk) == $group->id) ? 'selected' : '' }}>{{ $group->name ?? $group->id }}</option>
                            @endforeach
                        </select>
                        @error('kp_usergroup_idfk')
                            <div class="text-danger text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="budgetyear_idfk" class="form-label">ปีงบประมาณ <span class="text-danger">*</span></label>
                        <select class="form-select" id="budgetyear_idfk" name="budgetyear_idfk" required>
                            <option value="">เลือกปีงบประมาณ</option>
                            @foreach ($budgetYears as $year)
                                <option value="{{ $year->id }}" {{ (old('budgetyear_idfk', $payrate->budgetyear_idfk) == $year->id) ? 'selected' : '' }}>{{ $year->year }}</option>
                            @endforeach
                        </select>
                        @error('budgetyear_idfk')
                            <div class="text-danger text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="payrate_peryear" class="form-label">อัตราค่าบริการรายปี <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" class="form-control" id="payrate_peryear" name="payrate_peryear" value="{{ old('payrate_peryear', $payrate->payrate_peryear) }}" required placeholder="เช่น 1200.00">
                        @error('payrate_peryear')
                            <div class="text-danger text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">สถานะ <span class="text-danger">*</span></label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="active" {{ (old('status', $payrate->status) == 'active') ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ (old('status', $payrate->status) == 'inactive') ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('status')
                            <div class="text-danger text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary me-2">บันทึกการแก้ไข</button>
                        <a href="{{ route('keptkaya.payrate_per_months.index') }}" class="btn btn-secondary">ยกเลิก</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection