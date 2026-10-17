@extends('layouts.super-admin')

@section('nav-main', 'จัดการก๊าซเรือนกระจก')
@section('nav-main-url', route('keptkayas.emission.index'))
@section('nav-current', 'แก้ไข Emission Factor')
@section('nav-current-title', 'แก้ไขข้อมูล Emission Factor')

@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card mb-4">
            <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="fas fa-edit me-2 text-primary"></i> แก้ไข: {{ $factor->material_name }}</h6>
                <a href="{{ route('keptkayas.emission.index') }}" class="btn btn-outline-secondary btn-sm mb-0">
                    <i class="fas fa-arrow-left me-1"></i> กลับไปหน้ารายการ
                </a>
            </div>

            <form action="{{ route('keptkayas.emission.update', $factor->id) }}" method="POST">
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

                    @include('admin.ef._form')
                </div>
                <div class="card-footer text-end pt-0">
                    <button type="reset" class="btn btn-outline-secondary mb-0">
                        <i class="fas fa-undo me-1"></i> ล้างค่า
                    </button>
                    <button type="submit" class="btn bg-gradient-primary mb-0">
                        <i class="fas fa-save me-1"></i> บันทึกการเปลี่ยนแปลง
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
