@extends('layouts.foodwaste')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-success">
            <i class="bi bi-people-fill me-2"></i> สรุปปริมาณขยะและคาร์บอนเครดิตของสมาชิก
        </h4>
    </div>

    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4 py-3">ชื่อสมาชิก</th>
                            <th class="py-3 text-center">จำนวนลอต (Batches)</th>
                            <th class="py-3 text-center">ปริมาณขยะรวม (กก.)</th>
                            <th class="py-3 text-center">ลดคาร์บอนได้ (kgCO₂e)</th>
                            <th class="pe-4 py-3 text-end">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($members as $member)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="bg-success text-white rounded-circle d-flex justify-content-center align-items-center me-3" style="width: 40px; height: 40px;">
                                        {{ mb_substr($member->firstname." ".$member->lastname ?? $member->name ?? 'U', 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">{{ $member->firstname." ".$member->lastname ?? $member->name }}</div>
                                        <div class="small text-muted">{{ $member->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-secondary-subtle text-secondary px-3 py-2 rounded-pill">
                                    {{ $member->compost_batches_count ?? 0 }} ลอต
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="fw-bold text-success fs-5">
                                    {{ number_format($member->waste_logs_sum_weight_kg ?? 0, 2) }}
                                </span> กก.
                            </td>
                            <td class="text-center">
                                <!-- 🌟 แสดงผลคาร์บอนเครดิตตรงนี้ -->
                                <span class="fw-bold text-primary fs-5">
                                    {{ number_format($member->waste_logs_sum_carbon_saved_kg ?? 0, 2) }}
                                </span> <span class="text-muted small">kgCO₂e</span>
                            </td>
                            <td class="pe-4 text-end">
                                <!-- ปุ่มไปหน้า Batches -->
                                <a href="{{ route('foodwaste.admin.members_waste.batches', $member->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-4 fw-bold">
                                    ดูรายละเอียดลอต <i class="bi bi-arrow-right ms-1"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <i class="bi bi-inbox text-secondary opacity-50 mb-3 d-block" style="font-size: 3rem;"></i>
                                <h5 class="fw-bold text-muted">ยังไม่มีข้อมูลสมาชิก</h5>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-4">
        {{ $members->links() }}
    </div>
</div>
@endsection
