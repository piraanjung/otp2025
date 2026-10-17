@extends('layouts.super-admin')

@section('nav-main', 'จัดการก๊าซเรือนกระจก')
@section('nav-main-url', route('keptkayas.emission.index'))
@section('nav-current', 'เพิ่ม Emission Factor')
@section('nav-current-title', 'เพิ่มข้อมูล Emission Factor ใหม่')

@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card mb-4">
            <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="fas fa-plus-circle me-2 text-success"></i> เพิ่มรายการใหม่</h6>
                <a href="{{ route('keptkayas.emission.index') }}" class="btn btn-outline-secondary btn-sm mb-0">
                    <i class="fas fa-arrow-left me-1"></i> กลับไปหน้ารายการ
                </a>
            </div>

            <form action="{{ route('keptkayas.emission.store') }}" method="POST">
                @csrf
                <div class="card-body">
                    @include('admin.ef._form')
                </div>
                <div class="card-footer text-end pt-0">
                    <button type="submit" class="btn bg-gradient-primary px-4 mb-0">
                        <i class="fas fa-save me-1"></i> บันทึกข้อมูล
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
