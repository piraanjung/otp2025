@extends('layouts.super-admin')

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-dark mb-1">🏢 กองทุนสวัสดิการฌาปนกิจ</h2>
                <p class="text-muted">บริหารจัดการเงินกำไรจากขยะเพื่อสวัสดิการชุมชน</p>
            </div>
            <button class="btn btn-primary rounded-pill px-4 shadow-sm" data-bs-toggle="modal"
                data-bs-target="#addPayoutModal">
                <i class="bi bi-plus-lg me-1"></i> บันทึกจ่ายสวัสดิการ
            </button>
        </div>

        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            </div>
        @endif

        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 bg-success text-white h-100">
                    <div class="card-body p-4 text-center">
                        <h6 class="opacity-75 mb-2 text-uppercase fw-bold" style="font-size: 0.8rem;">ยอดเงินกองทุนคงเหลือ
                        </h6>
                        <h2 class="display-6 fw-bold mb-0">฿ {{ number_format($fundBalance, 2) }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 bg-white h-100">
                    <div class="card-body p-4 text-center border-start border-primary border-5 rounded-4">
                        <h6 class="text-muted mb-2 text-uppercase fw-bold" style="font-size: 0.8rem;">กำไรสะสมจากการขายขยะ
                        </h6>
                        <h2 class="fw-bold text-primary mb-0">฿ {{ number_format($totalProfit, 2) }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 bg-white h-100">
                    <div class="card-body p-4 text-center border-start border-danger border-5 rounded-4 text-danger">
                        <h6 class="text-muted mb-2 text-uppercase fw-bold" style="font-size: 0.8rem;">จ่ายสวัสดิการไปแล้ว
                        </h6>
                        <h2 class="fw-bold mb-0">- ฿ {{ number_format($totalPaid, 2) }}</h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold"><i class="bi bi-clock-history me-2"></i>ประวัติการจ่ายสวัสดิการล่าสุด</h5>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="bg-light text-muted">
                        <tr>
                            <th class="ps-4">วันที่</th>
                            <th>สมาชิกที่เสียชีวิต</th>
                            <th>ทายาทผู้รับเงิน</th>
                            <th class="text-end">จำนวนเงิน</th>
                            <th class="text-center">หลักฐาน</th>
                            <th class="text-center">สถานะ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentPayouts as $payout)
                            <tr>
                                <td class="ps-4 text-muted">{{ $payout->created_at->format('d/m/Y') }}</td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $payout->deceased_name }}</div>
                                    <small class="text-muted">ID: #{{ str_pad($payout->id, 5, '0', STR_PAD_LEFT) }}</small>
                                </td>
                                <td>{{ $payout->beneficiary_name }}</td>
                                <td class="text-end fw-bold text-danger">฿ {{ number_format($payout->amount, 2) }}</td>
                                <td class="text-center">
                                    {{-- ปุ่มสำหรับเสนอผู้บริหาร --}}
                                    <a href="{{ route('admin.welfare.approval.pdf', $payout->id) }}" target="_blank"
                                        class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-file-earmark-text"></i> ใบเสนออนุมัติ
                                    </a>
                                    <a href="{{ route('admin.admin.welfare.payout.voucher', $payout->id) }}" target="_blank"
                                        class="btn btn-sm btn-outline-dark rounded-pill">
                                        <i class="bi bi-printer"></i> พิมพ์ใบสำคัญ
                                    </a>
                                    @if($payout->death_certificate_img)
                                        {{-- ปรับ Path ให้ดึงจาก folder public/storage --}}
                                        <a href="{{ asset('storage/' . $payout->death_certificate_img) }}" target="_blank"
                                            class="btn btn-sm btn-outline-info rounded-pill px-3">
                                            <i class="bi bi-image"></i> ใบมรณบัตร
                                        </a>
                                    @else
                                        <span class="text-muted small">ไม่มีหลักฐาน</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-success-subtle text-success rounded-pill px-3">จ่ายเรียบร้อย</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">ยังไม่มีประวัติการเบิกจ่ายสวัสดิการ</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white border-0 py-3">
                {{ $recentPayouts->links() }}
            </div>
        </div>
    </div>

    <div class="modal fade" id="addPayoutModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-4">
                <div class="modal-header border-0">
                    <h5 class="fw-bold mb-0">บันทึกจ่ายเงินสวัสดิการ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.welfare.payout.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">ชื่อสมาชิกที่เสียชีวิต</label>
                            <input type="text" name="deceased_name" class="form-control rounded-3 bg-light border-0"
                                required placeholder="ระบุชื่อ-นามสกุล">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">ชื่อทายาทผู้รับเงิน</label>
                            <input type="text" name="beneficiary_name" class="form-control rounded-3 bg-light border-0"
                                required placeholder="ระบุชื่อผู้รับเงินแทน">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">จำนวนเงิน (บาท)</label>
                            <input type="number" name="amount"
                                class="form-control rounded-3 bg-light border-0 fw-bold text-primary" required
                                placeholder="0.00">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">หลักฐานใบมรณบัตร (แนบรูปถ่าย)</label>
                            <input type="file" name="death_certificate_img" class="form-control rounded-3 bg-light border-0"
                                accept="image/*">
                        </div>
                        <div class="mb-0">
                            <label class="form-label small fw-bold text-muted">หมายเหตุ / ข้อมูลเพิ่มเติม</label>
                            <textarea name="note" class="form-control rounded-3 bg-light border-0" rows="2"
                                placeholder="เช่น เลขที่เอกสารเทศบาล..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-light rounded-pill px-4"
                            data-bs-dismiss="modal">ยกเลิก</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm">ยืนยันการจ่ายเงิน</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
