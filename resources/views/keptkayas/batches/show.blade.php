@extends('layouts.keptkaya')

@section('nav-current', 'รายละเอียดชุดขอเบิกถอนเงิน')

@section('style')
<style>
    /* สไตล์จำลองกระดาษ A4 สำหรับพรีวิวบนหน้าเว็บ */
    .a4-preview {
        background: #ffffff;
        border: 1px solid #d2d6da;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.08);
        border-radius: 1rem;
        padding: 15mm 12mm;
        color: #000;
        font-family: 'Sarabun', sans-serif;
    }

    .a4-table th {
        background-color: #f8f9fa !important;
        color: #333 !important;
        font-weight: bold;
        text-align: center;
        border: 1px solid #dee2e6 !important;
    }
    
    .a4-table td {
        border: 1px solid #dee2e6 !important;
        vertical-align: middle;
    }

    /* สไตล์สำหรับการสั่งพิมพ์ (Print CSS) */
    @media print {
        /* ซ่อนส่วนประกอบอื่นทั้งหมดของระบบ */
        body * {
            visibility: hidden;
        }
        
        /* แสดงเฉพาะโซนกระดาษ A4 */
        #a4-print-area, #a4-print-area * {
            visibility: visible;
        }

        #a4-print-area {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            padding: 40px 40px 20px 80px !important;
            margin: 0 !important;
            box-shadow: none !important;
            border: none !important;
        }

        .no-print {
            display: none !important;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-2">

    @if(session('success'))
        <div class="alert alert-success text-white font-weight-bold text-sm mb-3 no-print">
            <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger text-white font-weight-bold text-sm mb-3 no-print">
            <i class="fas fa-exclamation-circle me-1"></i> {{ session('error') }}
        </div>
    @endif

    <div class="row">
        
        <!-- ================= ฝั่งซ้าย: สรุปข้อมูล + พรีวิวเอกสาร A4 ด้านล่าง ================= -->
        <div class="col-lg-8">

            <div class="">
            <!-- 1. การ์ดสรุปรายการเบิกถอน (ส่วนบนฝั่งซ้าย) -->
            <div class="card shadow-sm border-0 rounded-4 mb-4 no-print">
                <div class="card-header bg-white pb-0  align-items-center">
                    <div>
                        <h5 class="font-weight-bolder text-dark mb-0">
                            <i class="fas fa-file-invoice-dollar text-primary me-2"></i>ชุดเบิกถอน: {{ $batch->batch_no }}
                        </h5>
                        <p class="text-xs text-secondary mb-0">
                            วันที่ตัดรอบ: {{ \Carbon\Carbon::parse($batch->cutoff_date)->format('d/m/Y') }} | 
                            กำหนดวันจ่ายเงินสด: {{ \Carbon\Carbon::parse($batch->payout_date)->format('d/m/Y') }}
                        </p>
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
                                <small class="text-xs text-black font-weight-bold d-block">เงินที่ต้องขอเบิก</small>
                                <h4 class="font-weight-bolder text-black mb-0">{{ number_format($batch->total_amount, 2) }} <sup>บาท</sup></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
                    <div>
                        <button onclick="window.print()" class="btn bg-gradient-primary btn-sm mb-2 rounded-pill px-3">
                            <i class="fas fa-print me-1"></i> พิมพ์เอกสารเสนออนุมัติ
                        </button>
                    </div>
            <!-- 2. พรีวิวเอกสาร A4 สำหรับเสนออนุมัติ (แสดงด้านล่างซ้าย) -->
            <div id="a4-print-area" class="a4-preview mb-4 bg-white">
                <div class="text-center mb-3">
                    <h5 class="fw-bold text-dark mb-1">บันทึกข้อความ</h5>
                    <p class="text-xs text-muted mb-0">ขออนุมัติเบิกจ่ายเงินสดธนาคารขยะประจำสัปดาห์</p>
                </div>

                <!-- ข้อมูลส่วนหัวบันทึกข้อความ -->
                <div class="row text-xs mb-3 p-2 bg-light rounded-3 g-1">
                    <div class="col-7">
                        <div><strong>หน่วยงาน:</strong> กองสาธารณสุขและสิ่งแวดล้อม / เทศบาล</div>
                        <div><strong>ชุดเบิกถอนเลขที่:</strong> {{ $batch->batch_no }}</div>
                        <div><strong>วันที่ตัดรอบ:</strong> {{ \Carbon\Carbon::parse($batch->cutoff_date)->format('d/m/Y') }}</div>
                    </div>
                    <div class="col-5 text-end">
                        <div><strong>กำหนดวันรับเงินสด:</strong> {{ \Carbon\Carbon::parse($batch->payout_date)->format('d/m/Y') }}</div>
                        <div><strong>รวมรายการ:</strong> {{ number_format($batch->total_requests) }} รายการ</div>
                    </div>
                </div>

                <p class="text-xs mb-3">
                    เรียน นายกเทศมนตรี / นายก อบต.<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ด้วย เจ้าหน้าที่ได้ทำการตรวจสอบและสรุปยอดขอถอนเงินสดจากบัญชีธนาคารขยะของสมาชิกประจำรอบสัปดาห์ ปรากฏรายละเอียดการขอเบิกถอนเงินสด ดังรายการต่อไปนี้:
                </p>

                <!-- ตารางสรุปรายการขอถอนเงิน -->
                <div class="table-responsive mb-3">
                    <table class="table a4-table table-bordered text-xs mb-0">
                        <thead>
                            <tr>
                                <th width="8%">ลำดับ</th>
                                <th>ชื่อ-นามสกุล สมาชิก</th>
                                <th width="30%">ผู้มีสิทธิ์รับเงินสด</th>
                                <th width="20%">จำนวนเงิน (บาท)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($batch->requests as $index => $req)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>{{ $req->user->firstname ?? '' }} {{ $req->user->lastname ?? '' }}</td>
                                    <td>
                                        @if($req->is_proxy)
                                            รับแทน: {{ $req->proxy_name }}
                                        @else
                                            รับด้วยตนเอง
                                        @endif
                                    </td>
                                    <td class="text-end font-weight-bold">{{ number_format($req->amount, 2) }}</td>
                                </tr>
                            @endforeach
                            <tr class="fw-bold bg-light">
                                <td colspan="3" class="text-end">ยอดเงินรวมทั้งสิ้น</td>
                                <td class="text-end text-primary">{{ number_format($batch->total_amount, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- ส่วนลงลายมือชื่อ ยึดตาม Workflow Step -->
                @php
                    $workflow = \App\Models\ApprovalWorkflow::with(['steps' => fn($q) => $q->orderBy('step_order', 'asc')])->where('is_active', 1)->first();
                    $approvalLogs = \App\Models\InvTransactionApprovals::where('ref_no', $batch->batch_no)
                        ->where('module_name', 'kept_kaya_withdraw')
                        ->get()
                        ->keyBy('step_order');
                @endphp

                <div class="row mt-4 text-center text-xs g-3">
                    <!-- เจ้าหน้าที่ผู้จัดทำ -->
                    <div class="col-6 mb-3">
                        <div class="p-2 border rounded-3 h-100 d-flex flex-column justify-content-between">
                            <div>
                                <span class="fw-bold text-secondary d-block mb-1">ผู้จัดทำรายการ</span>
                                <div class="my-2 text-muted">.......................................</div>
                            </div>
                            <div>
                                <span class="d-block fw-bold text-dark">( {{ $batch->creator->firstname ?? '' }} {{ $batch->creator->lastname ?? '' }} )</span>
                                <small class="text-muted text-xxs">วันที่ {{ \Carbon\Carbon::parse($batch->created_at)->format('d/m/Y') }}</small>
                            </div>
                        </div>
                    </div>

                    <!-- วนลูปแสดงผู้เซ็นอนุมัติตาม Workflow Steps -->
                    @if($workflow)
                        @foreach($workflow->steps->sortBy('step_order') as $step)
                            @php
                                $log = $approvalLogs->get($step->step_order);
                                $isApproved = ($log && strtoupper($log->status) == 'APPROVED');
                            @endphp
                            <div class="col-6 mb-3">
                                <div class="p-2 border rounded-3 h-100 d-flex flex-column justify-content-between">
                                    <div>
                                        <span class="fw-bold text-secondary d-block mb-1">{{ $step->role_name }}</span>
                                        <div class="my-2 text-muted">.......................................</div>
                                    </div>
                                    <div>
                                        <span class="d-block fw-bold text-dark">
                                            ( {{ $log && $log->approver ? ($log->approver->firstname.' '.$log->approver->lastname) : ($step->specificUser ? ($step->specificUser->firstname.' '.$step->specificUser->lastname) : '.......................................') }} )
                                        </span>
                                        <small class="text-muted text-xxs">
                                            @if($isApproved)
                                                อนุมัติแล้วเมื่อ {{ \Carbon\Carbon::parse($log->action_at ?? $log->created_at)->format('d/m/Y') }}
                                            @else
                                                วันที่ ....../....../......
                                            @endif
                                        </small>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>

        </div>

        <!-- ================= ฝั่งขวา: ลำดับขั้นตอนการอนุมัติ (Approval Timeline & Actions) ================= -->
        <div class="col-lg-4 no-print">
        
            <div class="card shadow-sm border-0 rounded-4 mb-4 sticky-top" style="top: 20px;">
                <div class="card-header bg-white pb-0">
                    <h6 class="font-weight-bolder text-dark mb-0">
                        <i class="fas fa-tasks text-primary me-2"></i>ลำดับการอนุมัติ (Approval Workflow)
                    </h6>
                </div>
                <div class="card-body">
                    @php
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
                                                ผู้มีอำนาจ: {{ $step->specificUser->firstname }} {{ $step->specificUser->lastname }}
                                            @endif
                                        </p>
                                        @if($log && $log->status != 'PENDING')
                                            <span class="badge bg-gradient-{{ $isApproved ? 'success' : 'danger' }} text-xxs my-1">
                                                {{ $isApproved ? 'อนุมัติแล้ว' : 'ไม่อนุมัติ' }}
                                            </span>
                                            <small class="text-muted d-block text-xxs">โดย: {{ $log->approver->firstname ?? '' }} | {{ \Carbon\Carbon::parse($log->action_at ?? $log->created_at)->format('d/m/Y H:i') }}</small>
                                            @if($log->comment)
                                                <small class="text-xs text-dark d-block bg-gray-100 p-2 rounded-3 mt-1">"{{ $log->comment }}"</small>
                                            @endif
                                        @elseif($isCurrent)
                                            <span class="badge bg-gradient-warning text-xxs my-1">รอการอนุมัติ</span>
                                        @else
                                            <span class="badge bg-gradient-light text-secondary text-xxs my-1">รอคิว</span>
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
                                    <button type="submit" name="status" value="approved" class="btn bg-gradient-success btn-sm w-100 mb-0 py-2">
                                        <i class="fas fa-check me-1"></i> อนุมัติ
                                    </button>
                                    <button type="submit" name="status" value="rejected" class="btn bg-gradient-danger btn-sm w-100 mb-0 py-2" onclick="return confirm('ยืนยันไม่อนุมัติรายการนี้หรือไม่?');">
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