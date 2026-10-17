@extends('layouts.foodwaste')
@section('content')


<div class="container">
    <h4 class="mb-4">รายการเมนูรอการอนุมัติ ({{ $pendingItems->count() }})</h4>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row">
        @foreach($pendingItems as $item)
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="row g-0 h-100">
                    <div class="col-4">
                        <img src="{{ asset($item->mealLog->photo_path) }}"
                             class="img-fluid rounded-start h-100" style="object-fit: cover;">
                    </div>
                    <div class="col-8">
                        <div class="card-body">
                            <form action="{{ route('foodwaste.meal_logs_approve_item', $item->id) }}" method="POST">
                                @csrf
                                <div class="mb-2">
                                    <label class="small fw-bold">หมวดหมู่: {{ $item->category }}</label>
                                    <input type="text" name="menu_name" class="form-control form-control-sm" value="{{ $item->menu_name }}">
                                </div>
                                <div class="mb-3">
                                    <label class="small fw-bold">แคลอรี่ที่เหมาะสม</label>
                                    <input type="number" name="calories" class="form-control form-control-sm" value="{{ $item->calories }}">
                                </div>
                                <div class="d-flex justify-content-between">
                                    <button type="submit" class="btn btn-success btn-sm w-100 me-2">✅ อนุมัติ & บันทึก</button>
                                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="confirmDelete({{ $item->id }})">🗑️ ลบ</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection

