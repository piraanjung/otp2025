@extends('layouts.foodwaste')

@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card mb-4">
            <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                <h6 class="mb-0">เพิ่มอุปกรณ์ IoT Box</h6>
                <a href="{{ route('foodwaste.iotboxes.index') }}" class="btn btn-outline-secondary btn-sm mb-0">
                    <i class="fas fa-arrow-left me-1"></i> กลับไปหน้ารายการ
                </a>
            </div>
            <form action="{{ route('foodwaste.iotboxes.store') }}" method="POST">
                @csrf
                <div class="card-body">
                    @include('foodwaste.iotboxes._form')
                </div>
                <div class="card-footer text-end pt-0">
                    <button type="submit" class="btn bg-gradient-primary mb-0">
                        <i class="fas fa-save me-1"></i> บันทึก
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
