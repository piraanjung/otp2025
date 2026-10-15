@extends('layouts.welfare') 
@section('title_page', 'ติดตามสถานะคำร้อง')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">📋 ประวัติและสถานะคำร้องของฉัน</h4>
            <p class="text-muted small">ติดตามความคืบหน้าการอนุมัติเงินสวัสดิการฌาปนกิจ</p>
        </div>
        <a href="{{ route('admin.welfare.member.eligibility') }}" class="btn btn-outline-primary rounded-pill px-4 btn-sm">
            <i class="fas fa-shield-alt me-1"></i> หน้าตรวจสอบสิทธิ์
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success rounded-4 border-0 mb-4">{{ session('success') }}</div>
    @endif

    <div class="card border-0 shadow-sm rounded-4">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="bg-light text-muted">
                    <tr>
                        <th class="ps-4">วันที่ยื่นเรื่อง</th>
                        <th>ผู้เสียชีวิต</th>
                        <th>ทายาทผู้รับเงิน</th>
                        <th class="text-end">จำนวนเงิน</th>
                        <th class="text-center">สถานะปัจจุบัน</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payouts as $payout)
                    <tr>
                        <td class="ps-4 text-muted">{{ $payout->created_at->format('d/m/Y H:i') }}</td>
                        <td><strong class="text-dark">{{ $payout->deceased_name }}</strong></td>
                        <td>{{ $payout->beneficiary_name }}</td>
                        <td class="text-end fw-bold text-danger">฿ {{ number_format($payout->amount, 2) }}</td>
                        <td class="text-center">
                            @if($payout->status === 'paid')
                                <span class="badge bg-success-subtle text-success rounded-pill px-3 py-2">อนุมัติและจ่ายเงินแล้ว</span>
                            @elseif($payout->status === 'rejected')
                                <span class="badge bg-danger-subtle text-danger rounded-pill px-3 py-2">ไม่อนุมัติ</span>
                            @else
                                <span class="badge bg-warning-subtle text-warning rounded-pill px-3 py-2">
                                    <i class="fas fa-clock me-1"></i> กำลังตรวจสอบ ({{ $payout->currentStep->role_name ?? 'รออนุมัติ' }})
                                </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="fas fa-folder-open fa-2x mb-3 text-black-50"></i><br>
                            คุณยังไม่เคยยื่นคำร้องขอรับเงินสวัสดิการผ่านระบบออนไลน์
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white border-0 py-3">
            {{ $payouts->links() }}
        </div>
    </div>
</div>
@endsection