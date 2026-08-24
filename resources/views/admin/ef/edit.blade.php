@extends('layouts.super-admin')

@section('page-topic', 'แก้ไขข้อมูล Emission Factor')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title text-bold">
                            <i class="fas fa-edit text-primary"></i> แก้ไข: {{ $factor->material_name }}
                        </h3>
                        <div class="card-tools">
                            <a href="{{ route('keptkayas.emission.index') }}" class="btn btn-default btn-sm">
                                <i class="fas fa-arrow-left"></i> กลับไปหน้ารายการ
                            </a>
                        </div>
                    </div>

                    <form action="{{ route('keptkayas.emission.update', $factor->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="card-body">
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div class="row">
                                <!-- ชื่อวัสดุ -->
                                <!-- ตัวอย่างช่องชื่อวัสดุ -->
                                <div class="form-group col-md-12">
                                    <label for="material_name">ชื่อวัสดุ <span class="text-danger">*</span></label>
                                    <input type="text" name="material_name"
                                        class="form-control @error('material_name') is-invalid @enderror"
                                        value="{{ old('material_name', $factor->material_name) }}">

                                    @error('material_name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <!-- หน่วย -->
                                <div class="form-group col-md-6">
                                    <label for="unit">หน่วย (Unit)</label>
                                    <input type="text" name="unit" id="unit" class="form-control"
                                        value="{{ old('unit', $factor->unit) }}" placeholder="เช่น kgCO2e/kg">
                                </div>

                                <!-- ค่า EF -->
                                <div class="form-group col-md-6">
                                    <label for="ef_value">ค่า EF (ef_value) <span class="text-danger">*</span></label>
                                    <input type="number" step="0.0001" name="ef_value" id="ef_value"
                                        class="form-control text-bold text-success"
                                        value="{{ old('ef_value', $factor->ef_value) }}" required>
                                </div>

                                <!-- แหล่งที่มา -->
                                <div class="form-group col-md-12">
                                    <label for="source">แหล่งที่มาของข้อมูล (Source)</label>
                                    <input type="text" name="source" id="source" class="form-control"
                                        value="{{ old('source', $factor->source) }}">
                                </div>

                                <!-- ตัวอย่างขยะ -->
                                <div class="form-group col-md-12">
                                    <label for="example">ตัวอย่างวัสดุ / หมายเหตุ (Example)</label>
                                    <textarea name="example" id="example" class="form-control"
                                        rows="3">{{ old('example', $factor->example) }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer text-right">
                            <button type="reset" class="btn btn-default">
                                <i class="fas fa-undo"></i> ล้างค่า
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> บันทึกการเปลี่ยนแปลง
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
