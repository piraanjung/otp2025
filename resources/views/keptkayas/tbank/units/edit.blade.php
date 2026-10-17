@extends('layouts.keptkaya')
@section('page-topic', 'หน่วยนับสินค้า')
@section('nav-header', 'หน่วยนับสินค้า')
@section('nav-current', 'แก้ไขข้อมูลหน่วยนับสินค้า')
@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card mb-4">
            <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                <h6 class="mb-0">แก้ไขหน่วยนับสินค้า: {{ $unit->unitname }}</h6>
                <a href="{{ route('keptkayas.tbank.units.index') }}" class="btn btn-outline-secondary btn-sm mb-0">
                    <i class="fas fa-arrow-left me-1"></i> กลับไปหน้ารายการ
                </a>
            </div>

            <form action="{{ route('keptkayas.tbank.units.update', $unit) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger text-white">
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="unitname" class="form-label">ชื่อหน่วยนับ <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('unitname') is-invalid @enderror" id="unitname"
                                name="unitname" value="{{ old('unitname', $unit->unitname) }}" required>
                            @error('unitname') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="unit_short_name" class="form-label">ชื่อย่อหน่วยนับ</label>
                            <input type="text" class="form-control @error('unit_short_name') is-invalid @enderror"
                                id="unit_short_name" name="unit_short_name"
                                value="{{ old('unit_short_name', $unit->unit_short_name) }}">
                            @error('unit_short_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    {{-- controller validate เป็น required|boolean จึงส่งค่า 0 ไปด้วยเมื่อไม่ได้ติ๊ก --}}
                    <div class="form-check form-switch mb-2">
                        <input type="hidden" name="status" value="0">
                        <input class="form-check-input" type="checkbox" id="status" name="status" value="1"
                            @checked(old('status', $unit->status))>
                        <label class="form-check-label" for="status">สถานะ (Active)</label>
                    </div>
                    <div class="form-check form-switch mb-0">
                        <input type="hidden" name="deleted" value="0">
                        <input class="form-check-input" type="checkbox" id="deleted" name="deleted" value="1"
                            @checked(old('deleted', $unit->deleted))>
                        <label class="form-check-label" for="deleted">ทำเครื่องหมายว่าลบ</label>
                    </div>
                </div>
                <div class="card-footer text-end pt-0">
                    <button type="submit" class="btn bg-gradient-primary mb-0">
                        <i class="fas fa-save me-1"></i> บันทึกการเปลี่ยนแปลง
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
