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
    {{-- <div class="col-md-4">
        <a href="{{ route('inventory.items.create') }}" class="btn btn-primary btn-material">
            <i class="material-icons-round align-middle">add</i> เพิ่มพัสดุใหม่
        </a>
    </div> --}}

    <div class="col-md-8">
        <form action="{{ route('inventory.items.iframe') }}" method="GET">
            <div class="input-group shadow-sm">
                <input type="text" name="search" class="form-control border-0" 
                       placeholder="ค้นหาชื่อพัสดุ, รหัส, หรือ CAS No..." 
                       value="{{ request('search') }}">
                
                <button class="btn btn-white border-0 bg-white" type="submit">
                    <i class="material-icons-round text-primary">search</i>
                </button>
                
                @if(request('search'))
                    <a href="{{ route('inventory.items.iframe') }}" class="btn btn-light border-start text-danger" title="ล้างค่าค้นหา">
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
        </div>

        <div class="table-responsive">
    <table class="table table-hover align-middle">
        {{-- เอา thead ออกแล้ว (ลบทิ้งทั้งหมด) --}}
        <tbody>
            @forelse($items as $item)
                <tr>
                    {{-- ========================================================= --}}
                    {{-- สำหรับ Mobile: Card Layout พร้อมใส่ label กำกับหัวข้อ --}}
                    {{-- ========================================================= --}}
                    <td colspan="6" class="d-md-none p-2 bg-light-subtle">
                        <div class="card border shadow-sm p-3 bg-white">
                            {{-- แถวบน: รูปภาพ และ ชื่อ/รหัส --}}
                            <div class="d-flex align-items-start">
                                <div class="flex-shrink-0 me-3">
                                    @if($item->image_path)
                                        <img src="{{ asset('storage/' . $item->image_path) }}" class="rounded shadow-sm" width="55" height="55" style="object-fit: cover;">
                                    @else
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center text-muted" style="width: 55px; height: 55px;">
                                            <i class="material-icons-round">image</i>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-grow-1">
                                    <span class="badge bg-light text-dark border mb-1">
                                        {{ $item->category->name ?? 'ไม่ระบุ' }}
                                    </span>
                                    <div class="fw-bold text-dark">{{ $item->name }}</div>
                                    <small class="text-muted d-block">รหัส: {{ $item->code ?? '-' }}</small>
                                </div>
                            </div>

                            {{-- แถวกลาง: คุณสมบัติ และ จำนวนคงเหลือ (มี Label กำกับ) --}}
                            <div class="row align-items-center border-top pt-2 mt-2 g-2">
                                <div class="col-7">
                                    <div class="text-muted" style="font-size: 0.75rem;">คุณสมบัติ:</div>
                                    <div>
                                        @if($item->is_chemical)
                                            <span class="badge bg-warning text-dark me-1" title="สารเคมี">Chem</span>
                                        @endif
                                        @if($item->return_required)
                                            <span class="badge bg-info text-dark" title="ต้องคืนของ">ยืม-คืน</span>
                                        @endif
                                        @if(!$item->is_chemical && !$item->return_required)
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </div>
                                    @if($item->pending_qty > 0)
                                        <div class="small text-warning mt-1" style="font-size: 0.75rem;">
                                            <i class="material-icons-round fs-6 align-text-bottom">hourglass_empty</i> รออนุมัติ: {{ number_format($item->pending_qty) }}
                                        </div>
                                    @endif
                                </div>
                                <div class="col-5 text-end">
                                    <div class="text-muted" style="font-size: 0.75rem;">คงเหลือ:</div>
                                    <span class="fw-bold text-success fs-5">{{ $item->details->sum('quantity') }}</span>
                                    <small class="text-muted">{{ $item->unit }}</small>
                                </div>
                            </div>

                            {{-- แถวล่าง: ปุ่มจัดการ --}}
                            <div class="border-top pt-2 mt-2">
                                <a href="{{ route('inventory.withdraw.form', $item->id) }}" class="btn btn-sm btn-primary w-100">
                                    <i class="material-icons-round fs-6 align-middle me-1">shopping_cart</i> เบิกพัสดุ
                                </a>
                            </div>
                        </div>
                    </td>

                    {{-- ========================================================= --}}
                    {{-- สำหรับ Desktop: ตารางปกติ 6 คอลัมน์ (ไม่มี thead ด้านบนแล้ว) --}}
                    {{-- ========================================================= --}}
                    <td class="d-none d-md-table-cell">
                        @if($item->image_path)
                            <img src="{{ asset('storage/' . $item->image_path) }}" class="rounded shadow-sm" width="50" height="50" style="object-fit: cover;">
                        @else
                            <div class="bg-light rounded d-flex align-items-center justify-content-center text-muted" style="width: 50px; height: 50px;">
                                <i class="material-icons-round">image</i>
                            </div>
                        @endif
                    </td>
                    <td class="d-none d-md-table-cell">
                        <div class="fw-bold text-dark">{{ $item->name }}</div>
                        <small class="text-muted">Code: {{ $item->code ?? '-' }}</small>
                    </td>
                    <td class="d-none d-md-table-cell">
                        <span class="badge bg-light text-dark border">
                            {{ $item->category->name ?? 'ไม่ระบุ' }}
                        </span>
                    </td>
                    <td class="text-center d-none d-md-table-cell">
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
                    <td class="d-none d-md-table-cell">
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
                    <td class="text-end d-none d-md-table-cell">
                        <a href="{{ route('inventory.withdraw.form', $item->id) }}"
                            class="btn btn-sm btn-outline-primary rounded-circle me-1" title="เบิกพัสดุ">
                            <i class="material-icons-round fs-6">shopping_cart</i>
                        </a>
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
@endsection <tr>
                            <td>