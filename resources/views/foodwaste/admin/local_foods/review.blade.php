@extends('layouts.foodwaste')
@section('pendingFoods')
 {{ collect($pendingFoods)->count() }}
@endsection

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-primary">
            <i class="bi bi-list-check me-2"></i> รายการเมนูรอผู้เชี่ยวชาญกำหนดแคลอรี่
        </h4>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4 py-3">ชื่อเมนูอาหารที่ User พิมพ์มา</th>
                        <th class="py-3 text-center">จำนวนที่รออนุมัติ</th>
                        <th class="py-3">กำหนดหมวดหมู่</th>
                        <th class="py-3">กำหนดแคลอรี่ (kcal)</th>
                        <th class="pe-4 py-3 text-end">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendingFoods as $food)
                    <tr>
                        <form action="{{ route('foodwaste.admin.local_foods.approve') }}" method="POST">
                            @csrf
                            <td class="ps-4 fw-bold text-dark">
                                {{ $food->menu_name }}
                                <input type="hidden" name="menu_name" value="{{ $food->menu_name }}">
                            </td>
                            <td class="text-center">
                                <span class="badge bg-danger rounded-pill px-3">{{ $food->request_count }} คิว</span>
                            </td>
                            <td>
                                <input type="text" name="category" class="form-control form-control-sm" value="{{ $food->category }}" placeholder="เช่น กับข้าว, จานเดียว">
                            </td>
                            <td style="width: 200px;">
                                <div class="input-group input-group-sm">
                                    <input type="number" name="calories" class="form-control" required placeholder="ระบุตัวเลข">
                                    <span class="input-group-text">kcal</span>
                                </div>
                            </td>
                            <td class="pe-4 text-end">
                                <button type="submit" class="btn btn-sm btn-success rounded-pill px-3 shadow-sm">
                                    <i class="bi bi-check-lg"></i> บันทึก & อนุมัติ
                                </button>
                            </td>
                        </form>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <i class="bi bi-emoji-smile text-success opacity-50 mb-3 d-block" style="font-size: 3rem;"></i>
                            <h5 class="fw-bold text-muted">ยอดเยี่ยม! ไม่มีคิวงานค้าง</h5>
                            <p class="small text-muted">รายการอาหารทั้งหมดได้รับการกำหนดแคลอรี่แล้ว</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
