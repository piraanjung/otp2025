@extends('layouts.foodwaste')

@section('content')
    <div class="container py-4">
        <!-- ปุ่มย้อนกลับ & หัวข้อ -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <a href="{{ route('foodwaste.admin.members_waste.index') }}"
                    class="btn btn-outline-secondary btn-sm rounded-pill mb-2">
                    <i class="bi bi-arrow-left"></i> กลับหน้ารวมสมาชิก
                </a>
                <h4 class="fw-bold text-primary">
                    <i class="bi bi-box-seam me-2"></i> รายละเอียดลอตขยะ (Batches)
                </h4>
                <div class="text-muted small">
                    ของสมาชิก: <span class="fw-bold text-dark">{{ $member->firstname ?? $member->name }}</span>
                    ({{ $member->email }})
                </div>
            </div>
        </div>

        <!-- ตารางแสดงรายการลอต -->
        <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4 py-3">รหัสลอต (Batch Code)</th>
                                <th class="py-3 text-center">วันที่เริ่ม - สิ้นสุด</th>
                                <th class="py-3 text-center">สถานะ</th>
                                <th class="py-3 text-center">จำนวนครั้งที่ทิ้ง</th>
                                <th class="py-3 text-center">น้ำหนักรวม (กก.)</th>
                                <th class="py-3 text-center">ลดคาร์บอน (kgCO₂e)</th>
                                <!-- <th class="pe-4 py-3 text-end">จัดการ</th> -->
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($batches as $batch)
                                <tr>
                                    <td class="ps-4 fw-bold text-dark">
                                        {{ $batch->batch_code ?? 'ไม่มีรหัส' }}
                                    </td>
                                    <td class="text-center small">
                                        <div>เริ่ม: <span
                                                class="text-success">{{ $batch->start_date ? $batch->start_date->format('d/m/Y') : '-' }}</span>
                                        </div>
                                        <div>ปิด: <span
                                                class="text-danger">{{ $batch->closed_date ? $batch->closed_date->format('d/m/Y') : '-' }}</span>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        @if($batch->status == 'active' || $batch->status == 'open')
                                            <span class="badge bg-success rounded-pill px-3">กำลังหมัก</span>
                                        @else
                                            <span class="badge bg-secondary rounded-pill px-3">ปิดลอตแล้ว</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary-subtle text-secondary px-3 py-2 rounded-pill">
                                            {{ $batch->waste_logs_count ?? 0 }} ครั้ง
                                        </span>
                                    </td>
                                    <td class="text-center fw-bold text-success">
                                        {{ number_format($batch->waste_logs_sum_weight_kg ?? 0, 2) }}
                                    </td>
                                    <td class="text-center fw-bold text-primary">
                                        {{ number_format($batch->waste_logs_sum_carbon_saved_kg ?? 0, 2) }}
                                    </td>
                                    <td class="pe-4 text-end">
                                        <a href="{{ route('foodwaste.admin.members_waste.waste_logs', $batch->id) }}"
                                            class="btn btn-sm btn-outline-primary rounded-pill fw-bold">
                                            ดูขยะในลอตนี้ <i class="bi bi-search ms-1"></i>
                                        </a>
                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <i class="bi bi-box text-secondary opacity-50 mb-3 d-block"
                                            style="font-size: 3rem;"></i>
                                        <h5 class="fw-bold text-muted">ยังไม่มีข้อมูลลอตขยะ</h5>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
