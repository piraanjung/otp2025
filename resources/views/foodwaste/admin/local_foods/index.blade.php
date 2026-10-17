@extends('layouts.foodwaste')

@section('content')
<div class="container py-4">

    <!-- Header & Add Button -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-success">
            <i class="bi bi-journal-text me-2"></i> ฐานข้อมูลอาหารพื้นถิ่น (Local Foods)
        </h4>
        <button class="btn btn-success rounded-pill px-4 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#addModal">
            <i class="bi bi-plus-lg me-1"></i> เพิ่มเมนูใหม่
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm rounded-3" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Table -->
    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4 py-3 text-muted">ID</th>
                            <th class="py-3">ชื่อเมนูอาหาร</th>
                            <th class="py-3 text-center">หมวดหมู่</th>
                            <th class="py-3 text-center">พลังงาน (kcal)</th>
                            <th class="pe-4 py-3 text-end">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($localFoods as $food)
                        <tr>
                            <td class="ps-4 text-muted">#{{ $food->id }}</td>
                            <td class="fw-bold text-dark">{{ $food->menu_name }}</td>
                            <td class="text-center">
                                <span class="badge bg-secondary-subtle text-secondary px-3 py-2 rounded-pill">
                                    {{ $food->category ?? 'ทั่วไป' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="fw-bold text-success">{{ $food->calories }}</span> <span class="text-muted small">kcal</span>
                            </td>
                            <td class="pe-4 text-end">
                                <!-- ปุ่มแก้ไข -->
                                <button class="btn btn-sm btn-outline-primary rounded-pill px-3 me-1" data-bs-toggle="modal" data-bs-target="#editModal{{ $food->id }}">
                                    <i class="bi bi-pencil-square"></i> แก้ไข
                                </button>

                                <!-- ปุ่มลบ -->
                                <form action="{{ route('foodwaste.admin.local_foods.destroy', $food->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3" onclick="return confirm('คุณแน่ใจหรือไม่ที่จะลบเมนู {{ $food->name }} ออกจากฐานข้อมูล?');">
                                        <i class="bi bi-trash-fill"></i> ลบ
                                    </button>
                                </form>
                            </td>
                        </tr>

                        <!-- Modal แก้ไข (Edit) -->
                        <div class="modal fade" id="editModal{{ $food->id }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 rounded-4 shadow">
                                    <form action="{{ route('foodwaste.admin.local_foods.update', $food->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-header bg-light border-bottom-0 rounded-top-4">
                                            <h6 class="modal-title fw-bold text-primary">✏️ แก้ไขเมนูอาหาร</h6>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body p-4 text-start">
                                            <div class="mb-3">
                                                <label class="form-label small fw-bold text-muted">ชื่อเมนูอาหาร <span class="text-danger">*</span></label>
                                                <input type="text" name="name" class="form-control" value="{{ $food->name }}" required>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label small fw-bold text-muted">หมวดหมู่</label>
                                                    <input type="text" name="category" class="form-control" value="{{ $food->category }}" placeholder="เช่น อาหารจานเดียว, ต้ม">
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label small fw-bold text-muted">พลังงาน (kcal) <span class="text-danger">*</span></label>
                                                    <input type="number" name="calories" class="form-control" value="{{ $food->calories }}" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer bg-white border-top-0 rounded-bottom-4">
                                            <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">ยกเลิก</button>
                                            <button type="submit" class="btn btn-primary rounded-pill px-4">บันทึกการแก้ไข</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-journal-x text-secondary opacity-50 mb-3 d-block" style="font-size: 3rem;"></i>
                                    <h5 class="fw-bold">ยังไม่มีข้อมูลอาหารพื้นถิ่น</h5>
                                    <p class="small">คลิกที่ปุ่ม "เพิ่มเมนูใหม่" ด้านบนเพื่อเริ่มสร้างฐานข้อมูลของคุณ</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal เพิ่มข้อมูลใหม่ (Create) -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <form action="{{ route('foodwaste.admin.local_foods.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-light border-bottom-0 rounded-top-4">
                    <h6 class="modal-title fw-bold text-success"><i class="bi bi-plus-circle-fill me-2"></i>เพิ่มเมนูอาหารพื้นถิ่น</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 text-start">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">ชื่อเมนูอาหาร <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="เช่น ไส้อั่ว, น้ำพริกหนุ่ม" required autofocus>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold text-muted">หมวดหมู่</label>
                            <input type="text" name="category" class="form-control" placeholder="เช่น กับข้าว, ปิ้งย่าง">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold text-muted">พลังงาน (kcal) <span class="text-danger">*</span></label>
                            <input type="number" name="calories" class="form-control" placeholder="เช่น 250" required>
                        </div>
                    </div>
                    <div class="form-text mt-2 text-primary">
                        <i class="bi bi-info-circle"></i> ข้อมูลนี้จะถูกเพิ่มเข้าไปในตัวเลือกเพื่อให้ User กดเลือกได้ทันที
                    </div>
                </div>
                <div class="modal-footer bg-white border-top-0 rounded-bottom-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4">เพิ่มข้อมูล</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
