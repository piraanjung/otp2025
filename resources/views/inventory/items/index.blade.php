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
        {{-- <div class="col-md-2">
            <a href="{{ route('inventory.items.create') }}" class="btn btn-primary btn-material">
                <i class="material-icons-round align-middle">add</i> เพิ่มพัสดุใหม่
            </a>
        </div> --}}
        <div class="col-md-2">
            <a href="{{ route('inventory.withdraw.create_multiple') }}" class="btn btn-warning btn-material">
                <i class="material-icons-round align-middle">add</i> เบิกพัสดุ
            </a>
        </div>
        <div class="col-md-8">
            <form action="{{ route('inventory.items.index') }}" method="GET">
                <div class="input-group shadow-sm">
                    <input type="text" name="search" class="form-control border-0"
                        placeholder="ค้นหาชื่อพัสดุ, รหัส, หรือ CAS No..." value="{{ request('search') }}">

                    <button class="btn btn-white border-0 bg-white" type="submit">
                        <i class="material-icons-round text-primary">search</i>
                    </button>

                    @if(request('search'))
                        <a href="{{ route('inventory.items.index') }}" class="btn btn-light border-start text-danger"
                            title="ล้างค่าค้นหา">
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
                        <th width="10%" class="text-center">รูปภาพ</th>
                        <th class="text-center">รหัส/ชื่อพัสดุ</th>
                        <th width="15%" class="text-center">หมวดหมู่</th>
                        <th width="15%" class="text-center">คงเหลือ</th>
                        <th width="20%" class="text-center">การรับเข้า (ล็อต)</th>
                        <th width="8%" class="text-center">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                        @php
                            $total_current_qty = $item->details->sum('current_qty');
                            $min_stock = $item->min_stock ?? 0;

                            // คำนวณเปอร์เซ็นต์เทียบกับ min_stock (ป้องกัน Error หารด้วย 0)
                            $stock_percentage = ($min_stock > 0) ? ($total_current_qty / $min_stock) * 100 : 100;
                        @endphp

                        {{-- สามารถปรับเปลี่ยนสีแถวตามเงื่อนไข min_stock ตรงนี้ได้ --}}
                        <tr
                            class="{{ $total_current_qty <= $min_stock &&  $stock_percentage > 0 ? 'table-danger' : ($stock_percentage > 0 && $stock_percentage <= 300   ? 'table-warning' : '') }}">
                            <td class="text-center">
                                @if($item->image_path)
                                    <img src="{{ asset($item->image_path) }}" class="rounded shadow" width="80" height="80"
                                        style="object-fit: cover;">
                                @else
                                    <div class="bg-light rounded d-flex align-items-center justify-content-center text-muted mx-auto"
                                        style="width: 80px; height: 80px;">
                                        <i class="material-icons-round">image</i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $item->name }}</div>
                                <small class="text-muted">Code: {{ $item->code ?? '-' }}</small>
                                <div class="text-start mt-1">
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
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark border">
                                    {{ $item->category->name ?? 'ไม่ระบุ' }}
                                </span>
                            </td>

                            <td class="text-end">
                                <span class="fw-bold text-success fs-5">
                                    {{ number_format($total_current_qty) }}
                                </span>
                                <sup>{{ $item->unit }}</sup>

                                @if(isset($item->pending_qty) && $item->pending_qty > 0)
                                    <div class="small text-warning mt-1" data-bs-toggle="tooltip" title="มีการขอเบิก รออนุมัติ">
                                        <i class="material-icons-round fs-6 align-text-bottom">hourglass_empty</i>
                                        รออนุมัติ: {{ number_format($item->pending_qty) }}
                                    </div>
                                @endif
                            </td>

                            <!-- แสดงรายการล็อต (เรียงจากล่าสุดไปเก่าสุด) -->
                            <td>
                                <div>
                                    @forelse($item->details->groupBy('received_date') as $receiveDate => $detailsInBatch)
                                        <div class="small border-bottom card shadow-sm mb-1 p-2 bg-white">
                                            <div class="card-body p-0">
                                                <div class="fw-bold text-primary">Lot:
                                                    {{ $detailsInBatch->first()->lot_number ?? '-' }}</div>

                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="text-dark">
                                                        <i class="material-icons-round fs-6 align-middle text-success">event</i>
                                                        {{ \Carbon\Carbon::parse($detailsInBatch->first()->received_date)->format('d/m/Y') }}
                                                    </span>
                                                    <span class="badge bg-success rounded-pill">
                                                        เหลือ {{ number_format($detailsInBatch->first()->current_qty) }}
                                                        {{ $item->unit }}
                                                    </span>
                                                </div>

                                                <!-- วันหมดอายุของล็อตนี้ (ถ้ามี) -->
                                                @php
                                                    $expireDate = $detailsInBatch->first()->expire_date;
                                                @endphp
                                                @if($expireDate)
                                                    <div class="text-danger mt-1">
                                                        <small><i class="material-icons-round fs-6 align-middle">alarm</i> หมดอายุ:
                                                            {{ \Carbon\Carbon::parse($expireDate)->format('d/m/Y') }}</small>
                                                    </div>
                                                @else
                                                    <div class="text-muted mt-1">
                                                        <small>- ไม่มีวันหมดอายุ -</small>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @empty
                                        <span class="text-muted small">ไม่มีล็อต Active</span>
                                    @endforelse
                                </div>
                            </td>

                            <td class="text-end">
                                <div class="d-grid gap-1">
                                    <a href="{{ route('inventory.stock.receive', $item->id) }}"
                                        class="btn btn-sm btn-success text-sm" title="เติมสต็อก/เพิ่ม">
                                        ตรวจรับ
                                    </a>

                                    <a href="{{ route('inventory.items.edit', $item->id) }}"
                                        class="btn btn-sm btn-outline-secondary text-sm" title="แก้ไข">
                                        แก้ไข
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="material-icons-round display-4 opacity-25">inventory_2</i>
                                <p class="mt-2">ยังไม่มีรายการพัสดุ</p>
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