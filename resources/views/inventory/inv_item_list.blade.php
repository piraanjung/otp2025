@extends('inventory.inv_master')

@section('title', 'รายการพัสดุ')
@section('header_title', 'คลังพัสดุทั้งหมด')

@section('content')

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="material-icons-round align-middle me-2">check_circle</i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
<div class="row mb-4 align-items-center">
    <div class="col-md-4">
        <a href="{{ route('inventory.items.create') }}" class="btn btn-primary btn-material">
            <i class="material-icons-round align-middle">add</i> เพิ่มพัสดุใหม่
        </a>
    </div>

    <div class="col-md-8">
        <form action="{{ route('inventory.items.index') }}" method="GET">
            <div class="input-group shadow-sm">
                <input type="text" name="search" class="form-control border-0" 
                       placeholder="ค้นหาชื่อพัสดุ, รหัส, หรือ CAS No..." 
                       value="{{ request('search') }}">
                
                <button class="btn btn-white border-0 bg-white" type="submit">
                    <i class="material-icons-round text-primary">search</i>
                </button>
                
                @if(request('search'))
                    <a href="{{ route('inventory.items.index') }}" class="btn btn-light border-start text-danger" title="ล้างค่าค้นหา">
                        <i class="material-icons-round">close</i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>
    <div class="card p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="fw-bold text-secondary m-0">รายการสินค้าในสต็อก</h5>
            <a href="{{ route('inventory.items.create') }}" class="btn btn-primary btn-material">
                <i class="material-icons-round align-middle">add</i> เพิ่มพัสดุใหม่
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="bg-light text-secondary">
                    <tr>
                        <th width="80">รูปภาพ</th>
                        <th>รหัส/ชื่อพัสดุ</th>
                        <th>หมวดหมู่</th>
                        <th class="text-center">คุณสมบัติ</th>
                        <th class="text-center">คงเหลือ</th>
                        <th class="text-end">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                        <tr>
                            <td>
                                @if($item->image_path)
                                    <img src="{{ asset('storage/' . $item->image_path) }}" class="rounded shadow-sm" width="50"
                                        height="50" style="object-fit: cover;">
                                @else
                                    <div class="bg-light rounded d-flex align-items-center justify-content-center text-muted"
                                        style="width: 50px; height: 50px;">
                                        <i class="material-icons-round">image</i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $item->name }}</div>
                                <small class="text-muted">Code: {{ $item->code ?? '-' }}</small>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    {{ $item->category->name ?? 'ไม่ระบุ' }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if($item->is_chemical)
                                    <span class="badge bg-warning text-dark me-1" title="สารเคมี">
                                        <i class="material-icons-round fs-6 align-middle">science</i> Chem
                                    </span>
                                @endif
                                @if($item->return_required)
                                    <span class="badge bg-info text-dark" title="ต้องคืนของ">
                                        <i class="material-icons-round fs-6 align-middle">assignment_return</i> ยืม-คืน
                                    </span>
                                @endif
                            </td>
                            <td>
                                <span class="fw-bold text-success fs-5">
                                    {{ $item->details->sum('quantity') }}
                                </span>
                                {{ $item->unit }}

                                @if($item->pending_qty > 0)
                                    <div class="small text-warning mt-1" data-bs-toggle="tooltip" title="มีการขอเบิก รออนุมัติ">
                                        <i class="material-icons-round fs-6 align-text-bottom">hourglass_empty</i>
                                        รออนุมัติ: {{ number_format($item->pending_qty) }}
                                    </div>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('inventory.stock.receive', $item->id) }}"
                                    class="btn btn-sm btn-outline-success rounded-circle me-1" title="เติมสต็อก/เพิ่มขวด">
                                    <i class="material-icons-round fs-6">add_box</i>
                                </a>
                                <a href="{{ route('inventory.withdraw.form', $item->id) }}"
                                    class="btn btn-sm btn-outline-primary rounded-circle me-1" title="เบิกพัสดุ">
                                    <i class="material-icons-round fs-6">shopping_cart</i>
                                </a>

                                <button class="btn btn-sm btn-outline-secondary rounded-circle" title="แก้ไข">
                                    <i class="material-icons-round fs-6">edit</i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="material-icons-round display-4 opacity-25">inventory_2</i>
                                <p>ยังไม่มีรายการพัสดุ</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $items->links() }}
        </div>
    </div>
@endsection