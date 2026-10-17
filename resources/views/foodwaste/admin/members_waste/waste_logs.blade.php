@extends('layouts.foodwaste')

@section('content')
<div class="container py-4">
    <!-- Header & Back Button -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('foodwaste.admin.members_waste.batches', $batch->user_id) }}" class="btn btn-outline-secondary btn-sm rounded-pill mb-2">
                <i class="bi bi-arrow-left"></i> กลับไปหน้ารายการลอต
            </a>
            <h4 class="fw-bold text-success">
                <i class="bi bi-search me-2"></i> ตรวจสอบรายการขยะในลอต
            </h4>
            <div class="text-muted small">
                รหัสลอต: <span class="fw-bold text-dark">{{ $batch->batch_code ?? 'ไม่ระบุ' }}</span> |
                สมาชิก: <span class="fw-bold text-dark">{{ $batch->user->firstname ?? 'ไม่ระบุชื่อ' }}</span>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4 py-3">รูปภาพ</th>
                            <th class="py-3">วันที่บันทึก</th>
                            <th class="py-3 text-center">น้ำหนักที่แจ้ง (กก.)</th>
                            <th class="py-3 text-center">คาร์บอนที่ลดได้</th>
                            <th class="py-3 text-center">สถานะ</th>
                            <th class="pe-4 py-3 text-end">ตรวจสอบ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($wasteLogs as $log)
                        <tr>
                            <td class="ps-4">
                                @if($log->photo_path)
                                    <img src="{{ asset($log->photo_path) }}" class="rounded-3 shadow-sm" style="width: 60px; height: 60px; object-fit: cover; border: 2px solid #e2e8f0;" alt="รูปขยะ">
                                @else
                                    <div class="bg-light rounded-3 d-flex align-items-center justify-content-center text-muted" style="width: 60px; height: 60px; border: 2px dashed #cbd5e1;">
                                        <i class="bi bi-image"></i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $log->created_at->format('d/m/Y') }}</div>
                                <div class="small text-muted">{{ $log->created_at->format('H:i') }} น.</div>
                            </td>
                            <td class="text-center">
                                <div class="fw-bold fs-5 text-dark">{{ number_format($log->weight_kg, 2) }}</div>
                                @if($log->is_mixed)
                                    <span class="badge bg-warning text-dark small rounded-pill">มีขยะปนเปื้อน</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="fw-bold text-primary">{{ number_format($log->carbon_saved_kg, 2) }}</span>
                                <div class="small text-muted">kgCO₂e</div>
                            </td>
                            <td class="text-center">
                                @if($log->is_verified)
                                    <span class="badge bg-success rounded-pill px-3"><i class="bi bi-check-circle-fill me-1"></i> ยืนยันแล้ว</span>
                                @else
                                    <span class="badge bg-danger rounded-pill px-3"><i class="bi bi-hourglass-split me-1"></i> รอตรวจสอบ</span>
                                @endif
                            </td>
                            <td class="pe-4 text-end">
                                <!-- ปุ่มเปิด Modal ตรวจสอบ -->
                                <button class="btn btn-sm {{ $log->is_verified ? 'btn-outline-secondary' : 'btn-primary' }} rounded-pill px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#verifyModal{{ $log->id }}">
                                    {{ $log->is_verified ? 'ดู/แก้ไขข้อมูล' : 'ตรวจสอบ' }}
                                </button>
                            </td>
                        </tr>

                        <!-- Modal ตรวจสอบข้อมูล -->
                        <div class="modal fade" id="verifyModal{{ $log->id }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 rounded-4 shadow">
                                    <form action="{{ route('foodwaste.admin.members_waste.verify_waste_log', $log->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-header bg-light border-bottom-0 rounded-top-4">
                                            <h5 class="modal-title fw-bold text-success">ตรวจสอบรายการขยะ</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>

                                        <div class="modal-body p-4 text-start">
                                            <!-- โชว์รูปใหญ่ๆ ให้แอดมินดูชัดๆ -->
                                            <div class="text-center mb-4">
                                                @if($log->photo_path)
                                                    <img src="{{ asset($log->photo_path) }}" class="img-fluid rounded-4 shadow-sm" style="max-height: 250px;">
                                                @else
                                                    <div class="bg-light p-5 rounded-4 text-muted"><i class="bi bi-camera-fill fs-1"></i><p class="mb-0">ไม่มีรูปภาพประกอบ</p></div>
                                                @endif
                                            </div>

                                            <div class="row bg-light p-3 rounded-3 mb-4 mx-0 border">
                                                <div class="col-6 mb-2">
                                                    <label class="small text-muted fw-bold">น้ำหนักที่แจ้งมา:</label>
                                                    <div class="fw-bold text-dark fs-5">{{ $log->weight_kg }} กก.</div>
                                                </div>
                                                <div class="col-6 mb-2">
                                                    <label class="small text-muted fw-bold">ลักษณะความชื้น:</label>
                                                    <div class="fw-bold text-dark fs-5">{{ $log->temperature_feel ?? 'ไม่ระบุ' }}</div>
                                                </div>
                                                <div class="col-12 mt-2">
                                                    @if($log->is_mixed)
                                                        <div class="alert alert-warning py-2 mb-0 small"><i class="bi bi-exclamation-triangle-fill"></i> ผู้ใช้ระบุว่ามีขยะอื่นปนเปื้อน</div>
                                                    @else
                                                        <div class="alert alert-success py-2 mb-0 small"><i class="bi bi-check-circle-fill"></i> ผู้ใช้ระบุว่าไม่มีขยะปนเปื้อน</div>
                                                    @endif
                                                </div>
                                            </div>

                                            <h6 class="fw-bold text-primary mb-3"><i class="bi bi-tools me-2"></i>สำหรับผู้เชี่ยวชาญ (Verification)</h6>

                                            <div class="mb-3">
                                                <label class="form-label small fw-bold">ความชื้นที่ประเมินได้ (%)</label>
                                                <div class="input-group">
                                                    <input type="number" step="0.01" name="actual_moisture_avg" class="form-control" value="{{ $log->actual_moisture_avg }}" placeholder="เช่น 60.5">
                                                    <span class="input-group-text">%</span>
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label small fw-bold">น้ำหนักแห้งที่ยืนยันแล้ว (กก.)</label>
                                                <div class="input-group">
                                                    <input type="number" step="0.01" name="verified_dry_weight" class="form-control" value="{{ $log->verified_dry_weight }}" placeholder="ระบุน้ำหนักที่หักลบความชื้นแล้ว">
                                                    <span class="input-group-text">กก.</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="modal-footer bg-white border-top-0 rounded-bottom-4">
                                            <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">ยกเลิก</button>
                                            <button type="submit" class="btn btn-success rounded-pill px-4"><i class="bi bi-check-lg"></i> บันทึกการตรวจสอบ</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="bi bi-trash text-secondary opacity-50 mb-3 d-block" style="font-size: 3rem;"></i>
                                <h5 class="fw-bold text-muted">ลอตนี้ยังไม่มีประวัติการทิ้งขยะ</h5>
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
