@extends('layouts.super-admin')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-dark"><i class="bi bi-journal-text"></i> ประวัติการขายขยะล๊อตใหญ่</h2>
        <a href="{{ route('admin.bulk_sales.create') }}" class="btn btn-primary rounded-pill px-4">
            <i class="bi bi-plus-lg"></i> บันทึกรายการขายใหม่
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>วันที่ขาย</th>
                        <th>ผู้ซื้อ / โรงหลอม</th>
                        <th class="text-end">ยอดขายรวม</th>
                        <th class="text-end">ต้นทุนรวม</th>
                        <th class="text-end text-success">กำไรเข้ากองทุน</th>
                        <th class="text-center">หลักฐาน</th>
                        <th>ผู้บันทึก</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bulkSales as $sale)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($sale->sale_date)->format('d/m/Y') }}</td>
                        <td><span class="fw-bold">{{ $sale->buyer_name }}</span></td>
                        <td class="text-end fw-bold">฿ {{ number_format($sale->total_revenue, 2) }}</td>
                        <td class="text-end text-muted">฿ {{ number_format($sale->total_cost, 2) }}</td>
                        <td class="text-end fw-bold text-success">+ ฿ {{ number_format($sale->profit_amount, 2) }}</td>
                        <td class="text-center">
                            @if($sale->receipt_image)
                                <a href="{{ asset('storage/' . $sale->receipt_image) }}" target="_blank" class="btn btn-sm btn-outline-info">
                                    <i class="bi bi-image"></i> ดูใบเสร็จ
                                </a>
                            @else
                                <span class="text-muted small">ไม่มีรูป</span>
                            @endif
                        </td>
                        <td><small>{{ $sale->recorder->name ?? 'N/A' }}</small></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">ยังไม่มีประวัติการขายขยะ</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white border-0 py-3">
            {{ $bulkSales->links() }}
        </div>
    </div>
</div>
@endsection
