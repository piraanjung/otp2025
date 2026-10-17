@extends('layouts.welfare') 

@section('style') 
<style> 
    body { 
        font-family: 'Sarabun', sans-serif; 
        background-color: #f4f6f9; 
        color: #333; 
    } 

    /* สไตล์หน้ากระดาษ A4 (ฝั่งขวา) */ 
    .a4-document { 
        width: 100%; 
        max-width: 210mm; 
        min-height: 297mm; 
        margin: 0 auto; 
        background: #ffffff; 
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.08); 
        border-radius: 8px; 
        padding: 40px; 
    } 

    /* =========================================================
       Stepper Timeline แนวตั้ง (ปรับปรุงโครงสร้างแก้ปัญหาตัวหนังสือทับไอคอน)
       ========================================================= */
    .timeline-steps { 
        position: relative; 
        padding-left: 10px;
        margin-top: 10px; 
    } 

    /* เส้นเชื่อมแนวตั้งอยู่เบื้องหลัง */
    .timeline-steps::before { 
        content: ''; 
        position: absolute; 
        top: 15px; 
        bottom: 25px; 
        left: 24px; 
        width: 2px; 
        background: #dee2e6; 
        z-index: 1;
    } 

    /* กล่องสำหรับแต่ละ Step */
    .timeline-step { 
        position: relative; 
        display: flex;
        align-items: flex-start;
        margin-bottom: 24px; 
        z-index: 2;
    } 

    /* วงกลมไอคอนแสดงสถานะ */
    .timeline-step-icon { 
        width: 30px; 
        height: 30px; 
        min-width: 30px;
        border-radius: 50%; 
        background: #e9ecef; 
        color: #6c757d; 
        display: flex; 
        align-items: center; 
        justify-content: center; 
        font-size: 13px; 
        font-weight: bold; 
        margin-right: 15px;
        border: 2px solid #fff;
        box-shadow: 0 2px 4px rgba(0,0,0,0.08);
        z-index: 3;
    } 

    .timeline-step.completed .timeline-step-icon { 
        background: #198754; 
        color: #fff; 
    } 

    .timeline-step.active .timeline-step-icon { 
        background: #ffc107; 
        color: #000; 
        box-shadow: 0 0 0 3px rgba(255, 193, 7, 0.3); 
    } 

    .timeline-step.rejected .timeline-step-icon { 
        background: #dc3545; 
        color: #fff; 
    } 

    /* เนื้อหาข้อความฝั่งขวาของไอคอน */
    .timeline-step-content {
        flex-grow: 1;
        padding-top: 2px;
    }

    .preview-scroll { 
        max-height: 88vh; 
        overflow-y: auto; 
        padding: 10px; 
    } 

    /* CSS สำหรับการสั่งพิมพ์ */ 
    @media print { 
        .no-print { 
            display: none !important; 
        } 
        body * { 
            visibility: hidden; 
        } 
        #print-area, #print-area * { 
            visibility: visible; 
        } 
        #print-area { 
            position: absolute; 
            left: 0; 
            top: 0; 
            width: 100%; 
        } 
        .a4-document { 
            box-shadow: none !important; 
            padding: 15mm !important; 
            border: none !important; 
        } 
    } 
</style> 
@endsection 

@section('content') 
<div class="container-fluid py-4 px-lg-5"> 
    <div class="row g-4"> 
        <!-- ================= คอลัมน์ซ้าย: เมนูควบคุม + Timeline อนุมัติ (ซ่อนตอนพิมพ์) ================= --> 
        <div class="col-lg-4 no-print"> 
            <div class="sticky-top" style="top: 20px;"> 
                <!-- เมนูควบคุมการพิมพ์ --> 
                <div class="card border-0 shadow-sm rounded-3 mb-3"> 
                    <div class="card-body"> 
                        <h5 class="fw-bold text-dark mb-3"><i class="fas fa-print me-2 text-primary"></i>จัดการเอกสาร</h5> 
                        <div class="d-grid gap-2"> 
                            <button onclick="window.print()" class="btn btn-primary py-2 fw-bold rounded-pill"> 
                                <i class="fas fa-print me-1"></i> พิมพ์ใบขออนุมัติจ่ายสวัสดิการ 
                            </button> 
                            <a href="{{ route('admin.welfare.dashboard') }}" class="btn btn-outline-secondary py-2 rounded-pill"> 
                                &larr; กลับหน้าหลักสวัสดิการ 
                            </a> 
                        </div> 
                    </div> 
                </div> 

                <!-- การอนุมัติและ Timeline ขั้นตอน --> 
                <div class="preview-scroll"> 
                    <div class="card border-0 shadow-sm rounded-3 mb-3 border-start border-4 border-primary"> 
                        <div class="card-body"> 
                            <h6 class="fw-bold text-primary mb-3"> 
                                📋 การพิจารณาอนุมัติคำร้อง 
                            </h6> 

                            <!-- ปุ่มกดอนุมัติ / ตีกลับ (แสดงเฉพาะเมื่อคำร้องอยู่ในสถานะรออนุมัติ) --> 
                            <div class="bg-light p-3 rounded-3 mb-4 text-center shadow-sm"> 
                                @if($payout->status === 'pending_approval') 
                                    <div class="d-grid gap-2"> 
                                        <button type="button" class="btn btn-success fw-bold" data-bs-toggle="modal" data-bs-target="#approveModal"> 
                                            ✅ อนุมัติขั้นตอนนี้ 
                                        </button> 
                                        <button type="button" class="btn btn-danger fw-bold" data-bs-toggle="modal" data-bs-target="#rejectModal"> 
                                            ❌ ปฏิเสธ / ตีกลับคำร้อง 
                                        </button> 
                                    </div> 
                                @elseif($payout->status === 'paid') 
                                    <div class="text-success fw-bold"> 
                                        <i class="fas fa-check-circle me-1"></i> อนุมัติเบิกจ่ายเรียบร้อยแล้ว 
                                    </div> 
                                @elseif($payout->status === 'rejected') 
                                    <div class="text-danger fw-bold"> 
                                        <i class="fas fa-times-circle me-1"></i> คำร้องนี้ถูกปฏิเสธแล้ว 
                                    </div> 
                                @endif 
                            </div> 

                            <!-- Timeline สายการอนุมัติ --> 
                            <h6 class="fw-bold text-dark mb-3">ลำดับขั้นตอนการอนุมัติ</h6> 
                            <div class="timeline-steps"> 
                                
                                <!-- ผู้ยื่นคำร้อง --> 
                                <div class="timeline-step completed"> 
                                    <div class="timeline-step-icon">✓</div> 
                                    <div class="timeline-step-content">
                                        <div class="fw-bold text-dark small">ผู้ยื่นคำร้อง</div> 
                                        <div class="text-muted text-xs mb-1">{{ $payout->requester->firstname." ".$payout->requester->lastname ?? '-' }}</div> 
                                        <div class="text-success fw-bold text-xxs"> 
                                            ยื่นเมื่อ: {{ \Carbon\Carbon::parse($payout->created_at)->format('d/m/Y H:i') }} 
                                        </div> 
                                    </div>
                                </div> 

                                <!-- วนลูปแสดงขั้นตอนตาม Workflow Steps --> 
                                @foreach($payoutLogs as $log) 
                                    @php 
                                        $stClass = ''; 
                                        if ($log->status === 'approved') { 
                                            $stClass = 'completed'; 
                                        } elseif ($log->status === 'submitted') { 
                                            $stClass = 'active'; 
                                        } elseif ($log->status === 'rejected') { 
                                            $stClass = 'rejected'; 
                                        } 
                                    @endphp 
                                    <div class="timeline-step {{ $stClass }}"> 
                                        <div class="timeline-step-icon"> 
                                            @if($log->status === 'approved') ✓  
                                            @elseif($log->status === 'rejected') ✕  
                                            @else i @endif 
                                        </div> 
                                        <div class="timeline-step-content">
                                            <div class="fw-bold text-dark small">{{ $log->step->step_name ?? 'ขั้นตอนการอนุมัติ' }}</div> 
                                            <div class="text-muted text-xs mb-1">{{ $log->actionBy->firstname ?? 'รอผู้รับผิดชอบ' }}</div> 
                                            <div> 
                                                @if($log->status === 'approved') 
                                                    <span class="badge bg-success text-xxs">อนุมัติแล้ว</span> 
                                                    <span class="text-muted text-xxs d-block mt-1">{{ \Carbon\Carbon::parse($log->created_at)->format('d/m/Y H:i') }}</span> 
                                                @elseif($log->status === 'rejected') 
                                                    <span class="badge bg-danger text-xxs">ปฏิเสธ</span> 
                                                @else 
                                                    <span class="badge bg-warning text-dark text-xxs">กำลังรอพิจารณา</span> 
                                                @endif 
                                            </div> 
                                        </div>
                                    </div> 
                                @endforeach 

                            </div> 
                        </div> 
                    </div> 
                </div> 

            </div> 
        </div> 

        <!-- ================= คอลัมน์ขวา: หน้าเอกสาร A4 (Preview & Print) ================= --> 
        <div class="col-lg-8"> 
            <div id="print-area"> 
                <div class="a4-document"> 
                    
                    <!-- หัวเอกสาร --> 
                    <div class="text-center my-4"> 
                        <h4 class="fw-bold text-dark mb-1">ใบขออนุมัติจ่ายเงินสวัสดิการฌาปนกิจ</h4> 
                        <p class="text-muted small">องค์กร/เทศบาล: {{ $org->name ?? 'เทศบาลตำบล/อบต.' }}</p> 
                    </div> 

                    <!-- ข้อมูลผู้ขอสวัสดิการและผู้เสียชีวิต --> 
                    <div class="row mb-4 bg-light p-3 border rounded-3 g-2"> 
                        <div class="col-sm-7"> 
                            <p class="mb-1"><strong>ชื่อผู้เสียชีวิต:</strong> <span class="text-danger fw-bold">{{ $payout->deceased_name }}</span></p> 
                            <p class="mb-1"><strong>ชื่อผู้รับผลประโยชน์/ผู้ยื่นเรื่อง:</strong> {{ $payout->beneficiary_name }}</p> 
                            <p class="mb-0"><strong>วันที่ยื่นคำร้อง:</strong> {{ \Carbon\Carbon::parse($payout->created_at)->format('d/m/Y H:i') }}</p> 
                        </div> 
                        <div class="col-sm-5 text-sm-end d-flex flex-column justify-content-center align-items-sm-end"> 
                            <p class="mb-1 text-muted small">เลขที่คำร้อง: <strong class="text-dark">#PAY-{{ str_pad($payout->id, 5, '0', STR_PAD_LEFT) }}</strong></p> 
                            <div> 
                                @if($payout->status === 'paid') 
                                    <span class="badge bg-success px-3 py-2">APPROVED / จ่ายเงินแล้ว</span> 
                                @elseif($payout->status === 'rejected') 
                                    <span class="badge bg-danger px-3 py-2">REJECTED / ไม่อนุมัติ</span> 
                                @else 
                                    <span class="badge bg-warning text-dark px-3 py-2">PENDING / รอการอนุมัติ</span> 
                                @endif 
                            </div> 
                        </div> 
                    </div> 

                    <!-- ตารางแสดงรายละเอียดจำนวนเงินเบิกจ่าย --> 
                    <div class="table-responsive mb-4"> 
                        <table class="table table-bordered align-middle text-center"> 
                            <thead class="table-light"> 
                                <tr> 
                                    <th width="10%">ลำดับ</th> 
                                    <th>รายการเงินช่วยเหลือสวัสดิการ</th> 
                                    <th width="25%">จำนวนเงิน (บาท)</th> 
                                </tr> 
                            </thead> 
                            <tbody> 
                                <tr> 
                                    <td>1</td> 
                                    <td class="text-start"> 
                                        <div class="fw-bold">เงินสงเคราะห์ฌาปนกิจศพประจำเคส</div> 
                                        <small class="text-muted">ตามเกณฑ์ระเบียบกองทุนสวัสดิการชุมชน</small> 
                                    </td> 
                                    <td class="fw-bold text-success fs-5"> 
                                        ฿ {{ number_format($payout->amount, 2) }} 
                                    </td> 
                                </tr> 
                                <tr> 
                                    <td colspan="2" class="text-end fw-bold">รวมจำนวนเงินทั้งสิ้น</td> 
                                    <td class="fw-bold text-primary fs-5">฿ {{ number_format($payout->amount, 2) }}</td> 
                                </tr> 
                            </tbody> 
                        </table> 
                    </div> 

                    <!-- หมายเหตุ --> 
                    @if($payout->note) 
                        <div class="mb-4 p-3 bg-light rounded-3 border"> 
                            <small class="fw-bold text-muted d-block">หมายเหตุ/รายละเอียดคำร้อง:</small> 
                            <span class="text-dark small">{{ $payout->note }}</span> 
                        </div> 
                    @endif 

                    <!-- ส่วนลงนามลายมือชื่อตามลำดับสายการอนุมัติ --> 
                    <div class="row mt-5 pt-4 text-center g-4"> 
                        <!-- ผู้ยื่นคำร้อง --> 
                        <div class="col-4"> 
                            <div class="p-3 border rounded-3 h-100 d-flex flex-column justify-content-between"> 
                                <div> 
                                    <span class="fw-bold text-secondary d-block mb-1">ผู้ยื่นคำร้อง</span> 
                                    <div class="my-3 text-muted">.......................................</div> 
                                </div> 
                                <div> 
                                    <span class="small d-block text-dark fw-bold">( {{ $payout->requester->firstname ?? '-' }} )</span> 
                                    <small class="text-muted" style="font-size: 11px;">วันที่: {{ \Carbon\Carbon::parse($payout->created_at)->format('d/m/Y') }}</small> 
                                </div> 
                            </div> 
                        </div> 

                        <!-- ลายเซ็นผู้อนุมัติในแต่ละขั้น --> 
                        @foreach($workflowSteps as $step) 
                            @php 
                                $stepLog = $payoutLogs->where('step_id', $step->id)->first(); 
                            @endphp 
                            <div class="col-4"> 
                                <div class="p-3 border rounded-3 h-100 d-flex flex-column justify-content-between"> 
                                    <div> 
                                        <span class="fw-bold text-secondary d-block mb-1">{{ $step->step_name ?? 'ผู้อนุมัติ' }}</span> 
                                        <div class="my-3 text-muted">.......................................</div> 
                                    </div> 
                                    <div> 
                                        <span class="small d-block text-dark fw-bold"> 
                                            ( {{ $stepLog->actionBy->name ?? 'รอการอนุมัติ' }} ) 
                                        </span> 
                                        <small class="text-muted" style="font-size: 11px;"> 
                                            @if($stepLog && $stepLog->status === 'approved') 
                                                อนุมัติเมื่อ: {{ \Carbon\Carbon::parse($stepLog->created_at)->format('d/m/Y') }} 
                                            @else 
                                                วันที่ ....../....../...... 
                                            @endif 
                                        </small> 
                                    </div> 
                                </div> 
                            </div> 
                        @endforeach 
                    </div> 

                </div> 
            </div> 
        </div> 

    </div> 
</div> 

<!-- ================= MODALS สำหรับกดอนุมัติ / ปฏิเสธ (no-print) ================= --> 

<!-- Modal อนุมัติ --> 
<div class="modal fade no-print" id="approveModal" tabindex="-1" aria-hidden="true"> 
    <div class="modal-dialog modal-dialog-centered"> 
        <form action="{{ route('admin.welfare.payout.approve', $payout->id) }}" method="POST" enctype="multipart/form-data" class="w-100"> 
            @csrf 
            <input type="hidden" name="action" value="approve"> 
            <div class="modal-content border-0 shadow-lg rounded-4"> 
                <div class="modal-header bg-success text-white border-0"> 
                    <h5 class="modal-title fw-bold">✅ ยืนยันการอนุมัติเบิกจ่ายสวัสดิการ</h5> 
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button> 
                </div> 
                <div class="modal-body text-start p-4"> 
                    <p class="mb-2">คุณกำลังจะอนุมัติคำร้องของ: <strong class="text-success">{{ $payout->deceased_name }}</strong></p> 
                    <p class="mb-3 text-muted small">ยอดเงินช่วยเหลือ: ฿ {{ number_format($payout->amount, 2) }}</p> 
                    
                    <div class="mb-3"> 
                        <label class="form-label small fw-bold text-secondary">แนบเอกสารเซ็นอนุมัติ / คำสั่ง (ถ้ามี):</label> 
                        <input type="file" name="signed_doc" class="form-control border-0 bg-light rounded-3" accept=".pdf,image/*"> 
                    </div> 

                    <div class="mb-3"> 
                        <label class="form-label small fw-bold text-secondary">ความเห็นเพิ่มเติม / หมายเหตุ:</label> 
                        <textarea class="form-control border-0 bg-light rounded-3" name="note" rows="3" placeholder="ระบุความเห็นการอนุมัติ..."></textarea> 
                    </div> 
                </div> 
                <div class="modal-footer bg-light border-0"> 
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">ยกเลิก</button> 
                    <button type="submit" class="btn btn-success rounded-pill px-4 shadow-sm">ยืนยันอนุมัติ</button> 
                </div> 
            </div> 
        </form> 
    </div> 
</div> 

<!-- Modal ปฏิเสธ --> 
<div class="modal fade no-print" id="rejectModal" tabindex="-1" aria-hidden="true"> 
    <div class="modal-dialog modal-dialog-centered"> 
        <form action="{{ route('admin.welfare.payout.approve', $payout->id) }}" method="POST" class="w-100"> 
            @csrf 
            <input type="hidden" name="action" value="reject"> 
            <div class="modal-content border-0 shadow-lg rounded-4"> 
                <div class="modal-header bg-danger text-white border-0"> 
                    <h5 class="modal-title fw-bold">❌ ยืนยันการปฏิเสธ / ตีกลับคำร้อง</h5> 
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button> 
                </div> 
                <div class="modal-body text-start p-4"> 
                    <p class="text-danger small fw-bold mb-2">⚠️ คำเตือน: การปฏิเสธจะทำให้คำร้องนี้ถูกระงับทันที</p> 
                    <div class="mb-3 mt-3"> 
                        <label class="form-label small fw-bold text-danger">ระบุเหตุผลที่ไม่อนุมัติ (จำเป็น):</label> 
                        <textarea class="form-control border-danger rounded-3" name="note" rows="3" required placeholder="โปรดระบุเหตุผล..."></textarea> 
                    </div> 
                </div> 
                <div class="modal-footer bg-light border-0"> 
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">ยกเลิก</button> 
                    <button type="submit" class="btn btn-danger rounded-pill px-4 shadow-sm">ยืนยันปฏิเสธคำร้อง</button> 
                </div> 
            </div> 
        </form> 
    </div> 
</div> 
@endsection