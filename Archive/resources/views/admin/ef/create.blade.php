@extends('layouts.super-admin')

@section('page-topic', 'เพิ่มข้อมูล Emission Factor ใหม่')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card card-outline card-success"> {{-- ใช้สีเขียวสำหรับ Create --}}
                <div class="card-header">
                    <h3 class="card-title text-bold">
                        <i class="fas fa-plus-circle text-success"></i> เพิ่มรายการใหม่
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('keptkayas.emission.index') }}" class="btn btn-default btn-sm">
                            <i class="fas fa-arrow-left"></i> กลับไปหน้ารายการ
                        </a>
                    </div>
                </div>

                <form action="{{ route('keptkayas.emission.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label>ชื่อวัสดุ (Material Name) <span class="text-danger">*</span></label>
                                <input type="text" name="material_name"
                                       class="form-control @error('material_name') is-invalid @enderror"
                                       value="{{ old('material_name') }}" placeholder="เช่น พลาสติก PET" required>
                                @error('material_name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group col-md-6">
                                <label>หน่วย (Unit)</label>
                                <input type="text" name="unit" class="form-control"
                                       value="{{ old('unit', 'kgCO2e/kg') }}">
                            </div>

                            <div class="form-group col-md-6">
                                <label>ค่า EF (ef_value) <span class="text-danger">*</span></label>
                                <input type="number" step="0.0001" name="ef_value"
                                       class="form-control text-bold text-success @error('ef_value') is-invalid @enderror"
                                       value="{{ old('ef_value') }}" placeholder="0.0000" required>
                                @error('ef_value') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group col-md-12">
                                <label>แหล่งที่มา (Source)</label>
                                <input type="text" name="source" class="form-control"
                                       value="{{ old('source') }}" placeholder="เช่น TGO 2025">
                            </div>

                            <div class="form-group col-md-12">
                                <label>ตัวอย่างวัสดุ / หมายเหตุ</label>
                                <textarea name="example" class="form-control" rows="3" placeholder="ระบุตัวอย่างขยะ...">{{ old('example') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer text-right">
                        <button type="submit" class="btn btn-success px-4">
                            <i class="fas fa-save"></i> บันทึกข้อมูล
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
