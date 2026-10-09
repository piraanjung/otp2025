@extends('layouts.keptkaya')

@section('nav-current', 'รายละเอียดชุดขอเบิกถอนเงิน')

@section('content')
<div class="container-fluid py-2">

    @if(session('success'))
        <div class="alert alert-success text-white font-weight-bold text-sm mb-3">
            <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger text-white font-weight-bold text-sm mb-3">
            <i class="fas fa-exclamation-circle me-1"></i> {{ session('error') }}
        </div>
    @endif

    <div class="row">
        <!-- ฝั่งซ้าย: รายละเอียดคำขอถอนเงินใน Batch -->
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-header bg-white pb-0 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="font-weight-bolder text-dark mb-0">
                            <i class="fas fa-file-invoice-dollar text-primary me-2"></i>ชุดเบิกถอน: {{ $batch->batch_no }}
                        </h5>
                        <p class="text-xs text-secondary mb-0">
                            วันที่ตัดรอบ: {{ \Carbon\Carbon::parse($batch->cutoff_date)->format('d/m/Y') }} | 
                            กำหนดวันจ่ายเงินสด: {{ \Carbon\Carbon::parse($batch->payout_date)->format('d/m/Y') }}
                        </p>
                    </div>
                    <div>
                        <a href="{{ route('keptkayas.batches.print_view', $batch->id) }}" target="_blank" class="btn btn-outline-primary btn-sm mb-0">
                            <i class="fas fa-print me-1"></i> พิมพ์เอกสารเสนออนุมัติ A4
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <div class="bg-gray-100 p-3 rounded-3">
                                <small class="text-xs text-muted font-weight-bold d-block">จำนวนคำขอทั้งหมด</small>
                                <h4 class="font-weight-bolder text-dark mb-0">{{ number_format($batch->total_requests) }} รายการ</h4>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="bg-success bg-opacity-10 p-3 rounded-3 border border-success border-opacity-25">
                                <small class="text-xs text-success font-weight-bold d-block">ยอดเงินสดรวมที่ต้องขอเบิก</small>
                                <h4 class="font-weight-bolder text-success mb-0">{{ number_format($batch->total_amount, 2) }} บาท</h4>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0 text-sm">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ลำดับ</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">สมาชิก</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">ผู้มีสิทธิ์รับเงิน</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">จำนวนเงิน (บาท)</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">รหัสยืนยัน</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($batch->requests as $index => $req)
                                    <tr>
                                        <td class="ps-3"><span class="text-xs font-weight-bold">{{ $index + 1 }}</span></td>
                                        <td>
                                            <span class="text-xs font-weight-bold d-block">{{ $req->user->firstname ?? '' }} {{ $req->user->lastname ?? '' }}</span>
                                            <small class="text-xxs text-muted">ยื่นเมื่อ: {{ $req->created_at->format('d/m/Y H:i') }}</small>
                                        </td>
                                        <td>
                                            @if($req->is_proxy)
                                                <span class="badge bg-gradient-warning text-xxs">
                                                    <i class="fas fa-user-friends me-1"></i>รับแทน: {{ $req->proxy_name }}
                                                </span>
                                                <small class="d-block text-xxs text-muted">บัตร: {{ $req->proxy_id_card ?? '—' }}</small>
                                            @else
                                                <span class="badge bg-gradient-light text-dark text-xxs">รับด้วยตนเอง</span>
                                            @endif
                                        </td>
                                        <td class="text-center"><span class="text-xs font-weight-bolder text-dark">{{ number_format($req->amount, 2) }}</span></td>
                                        <td class="text-center"><span class="badge bg-gradient-secondary text-xxs">{{ $req->verification_code }}</span></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-xs text-muted">ไม่พบข้อมูลคำขอใน Batch นี้</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- ฝั่งขวา: ลำดับขั้นตอนการอนุมัติ (Approval Chain Timeline) -->
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-header bg-white pb-0">
                    <h6 class="font-weight-bolder text-dark mb-0">
                        <i class="fas fa-tasks text-primary me-2"></i>ลำดับการอนุมัติ (Approval Workflow)
                    </h6>
                </div>
                <div class="card-body">
                    @php
                        $workflow = \App\Models\ApprovalWorkflow::with(['steps' => fn($q) => $q->orderBy('step_order', 'asc')])->where('is_active', 1)->first();
                        
                        // ดึงจากตารางกลาง InvTransactionApprovals แทน KpBatchApprovalLog
                        $approvalLogs = \App\Models\InvTransactionApprovals::where('ref_no', $batch->batch_no)
                            ->where('module_name', 'kept_kaya_withdraw')
                            ->get()
                            ->keyBy('step_order');

                        $currentStep = $workflow ? $workflow->steps->where('step_order', $batch->current_step_order)->first() : null;
                    @endphp

                    <div class="timeline timeline-one-side">
                        @if($workflow && $workflow->steps->count() > 0)
                            @foreach($workflow->steps->sortBy('step_order') as $step)
                                @php
                                    $log = $approvalLogs->get($step->step_order);
                                    $isApproved = ($log && strtoupper($log->status) == 'APPROVED');
                                    $isRejected = ($log && strtoupper($log->status) == 'REJECTED');
                                    $isCurrent = ($batch->status == 'in_review' && $batch->current_step_order == $step->step_order);
                                @endphp
                                <div class="timeline-block mb-3">
                                    <span class="timeline-step">
                                        @if($isApproved)
                                            <i class="fas fa-check-circle text-success"></i>
                                        @elseif($isRejected)
                                            <i class="fas fa-times-circle text-danger"></i>
                                        @elseif($isCurrent)
                                            <i class="fas fa-spinner fa-spin text-warning"></i>
                                        @else
                                            <i class="fas fa-circle text-secondary opacity-3"></i>
                                        @endif
                                    </span>
                                    <div class="timeline-content">
                                        <h6 class="text-dark text-sm font-weight-bold mb-0">
                                            Step {{ $step->step_order }}: {{ $step->role_name }}
                                        </h6>
                                        <p class="text-secondary text-xs mt-1 mb-0">
                                            @if($step->specificUser)
                                                ชื่อผู้มีอำนาจ: {{ $step->specificUser->firstname }} {{ $step->specificUser->lastname }}
                                            @endif
                                        </p>
                                        @if($log)
                                            <span class="badge bg-gradient-{{ $isApproved ? 'success' : 'danger' }} text-xxs my-1">
                                                {{ $isApproved ? 'อนุมัติแล้ว' : 'ไม่อนุมัติ' }}
                                            </span>
                                            <small class="text-muted d-block text-xxs">โดย: {{ $log->approver->firstname ?? '' }} | {{ \Carbon\Carbon::parse($log->action_at ?? $log->created_at)->format('d/m/Y H:i') }}</small>
                                            @if($log->comment)
                                                <small class="text-xs text-dark d-block bg-gray-100 p-2 rounded-3 mt-1">"{{ $log->comment }}"</small>
                                            @endif
                                        @elseif($isCurrent)
                                            <span class="badge bg-gradient-warning text-xxs my-1">รอการอนุมัติ</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p class="text-xs text-muted mb-0">ยังไม่ได้เปิดใช้งานหรือตั้งค่า Approval Workflow</p>
                        @endif
                    </div>

                    <!-- ฟอร์มลงบันทึกอนุมัติใน Step ปัจจุบัน -->
                    @if($batch->status == 'in_review' && $currentStep)
                        <hr class="horizontal dark my-3">
                        <div class="bg-gray-100 p-3 rounded-4">
                            <h6 class="text-xs font-weight-bolder text-primary mb-2">อนุมัติขั้นตอน: {{ $currentStep->role_name }}</h6>
                            <form action="{{ route('keptkayas.batches.approve_step', $batch->id) }}" method="POST">
                                @csrf
                                <div class="mb-2">
                                    <textarea name="comment" class="form-control text-xs" rows="2" placeholder="ระบุความคิดเห็น/บันทึกเพิ่มเติม (ถ้ามี)"></textarea>
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="submit" name="status" value="approved" class="btn bg-gradient-success btn-sm w-100 mb-0">
                                        <i class="fas fa-check me-1"></i> อนุมัติ
                                    </button>
                                    <button type="submit" name="status" value="rejected" class="btn bg-gradient-danger btn-sm w-100 mb-0" onclick="return confirm('ยืนยันไม่อนุมัติรายการนี้หรือไม่?');">
                                        <i class="fas fa-times me-1"></i> ไม่อนุมัติ
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection