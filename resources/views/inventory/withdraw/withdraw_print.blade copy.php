<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ใบเบิกพัสดุเลขที่ {{ $transaction->ref_no }}</title>
    <!-- Google Fonts: Sarabun -->
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
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

        /* ตารางข้อมูล */
        .table-custom th {
            background-color: #f8f9fa !important;
            font-weight: 600;
            text-align: center;
        }

        /* Stepper Timeline แนวตั้ง (เหมาะกับคอลัมน์ซ้าย) */
        .timeline-steps {
            position: relative;
            padding-left: 25px;
        }
        .timeline-steps::before {
            content: '';
            position: absolute;
            top: 5px;
            bottom: 5px;
            left: 9px;
            width: 3px;
            background: #e9ecef;
        }
        .timeline-step {
            position: relative;
            margin-bottom: 20px;
        }
        .timeline-step-icon {
            position: absolute;
            left: -25px;
            top: 0;
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background: #e9ecef;
            color: #6c757d;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: bold;
        }
        .timeline-step.completed .timeline-step-icon { background: #198754; color: #fff; }
        .timeline-step.active .timeline-step-icon { background: #ffc107; color: #000; box-shadow: 0 0 0 3px rgba(255, 193, 7, 0.25); }
        .timeline-step.rejected .timeline-step-icon { background: #dc3545; color: #fff; }

        /* ตั้งค่าสำหรับการปริ้นเอกสาร (บังคับซ่อนคอลัมน์ซ้ายและส่วนควบคุมทั้งหมด) */
        @media print {
            .no-print { 
                display: none !important; 
            } 
            body { 
                background-color: #fff !important; 
            }
            .container-fluid {
                max-width: 100% !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .a4-document { 
                box-shadow: none !important; 
                padding: 0 !important;
                margin: 0 !important;
                width: 100% !important;
                border: none !important;
            }
        }
    </style>
</head>
<body>

<div class="container-fluid py-4 px-lg-5">
    <div class="row g-4">
        
        <!-- ================= คอลัมน์ซ้าย: ปุ่มควบคุม + Timeline (ซ่อนตอน Print) ================= -->
        <div class="col-lg-4 no-print">
            <div class="sticky-top" style="top: 20px;">
                
                <!-- การจัดการและปุ่มสั่งพิมพ์ -->
                <div class="card border-0 shadow-sm rounded-3 mb-3">
                    <div class="card-body">
                        <h5 class="fw-bold text-dark mb-3">⚙️ เมนูจัดการเอกสาร</h5>
                        <div class="d-grid gap-2">
                            <button onclick="window.print()" class="btn btn-primary py-2 fw-bold">
                                🖨️ พิมพ์เอกสาร (Print A4)
                            </button>
                            <a href="{{ route('inventory.history') }}" class="btn btn-outline-secondary py-2">
                                &larr; กลับหน้าประวัติ
                            </a>
                        </div>
                    </div>
                </div>

                <!-- ส่วนอนุมัติ (แสดงเฉพาะตอน PENDING) -->
               <!-- ส่วนอนุมัติ (แสดงเฉพาะคนที่เกี่ยวข้องและตรงเงื่อนไข) -->
                @if($transaction->status == 'PENDING')
                    <div class="card border-0 shadow-sm rounded-3 mb-3 bg-light">
                        <div class="card-body text-center">
                            <h6 class="text-primary fw-bold mb-3">🛠️ ดำเนินการอนุมัติ</h6>
 <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#approveModal">
                                        ✅ อนุมัติรายการนี้ (ขั้นที่ {{ $currentApproval->step_order }})
                                    </button>
                            {{-- กรณีที่เป็น "ผู้อนุมัติในลำดับปัจจุบัน" --}}
                            @if(isset($currentApproval) && $currentApproval->approver_id == Auth::id())
                                <div class="d-grid gap-2">
                                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#approveModal">
                                        ✅ อนุมัติรายการนี้ (ขั้นที่ {{ $currentApproval->step_order }})
                                    </button>
                                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                                        ❌ ตีกลับ / ไม่อนุมัติ
                                    </button>
                                </div>

                            {{-- กรณีที่เป็น "เจ้าของเรื่อง (Requestor)" แต่เอกสารยังรออนุมัติอยู่ --}}
                            @elseif($transaction->user_id == Auth::id())
                                <div class="alert alert-warning py-2 mb-0 small" role="alert">
                                    ⏳ เอกสารของคุณกำลังอยู่ในระหว่างรอการตรวจสอบและอนุมัติ
                                </div>
                                <!-- ถ้าอยากให้ Requestor ยกเลิกใบเบิกเองได้ ให้ใส่ปุ่มนี้ -->
                                <div class="d-grid mt-2">
                                    <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#cancelModal">
                                        🗑️ ยกเลิกคำอนุมัตินี้
                                    </button>
                                </div>

                            {{-- กรณีที่เป็น "คนอื่น" ที่ไม่ใช่คิวปัจจุบัน และไม่ใช่เจ้าของเรื่อง --}}
                            @else
                                <div class="text-muted small py-2">
                                    🔒 อยู่ระหว่างรอการดำเนินการจาก <br>
                                    <strong class="text-dark">{{ optional($currentApproval->approver)->firstname ?? 'ผู้อนุมัติขั้นที่ ' . optional($currentApproval)->step_order }}</strong>
                                </div>
                            @endif

                        </div>
                    </div>
                @endif

                <!-- แผนผังขั้นตอนการอนุมัติ (แนวตั้งใช้งานสะดวกขึ้นใน Sidebar) -->
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-body">
                        <h6 class="fw-bold text-secondary mb-3">📊 สถานะเส้นทางอนุมัติ</h6>
                        
                        <div class="timeline-steps">
                            <!-- ผู้ขอเบิก -->
                            <div class="timeline-step completed">
                                <div class="timeline-step-icon">✓</div>
                                <div class="small fw-bold">ผู้ขอเบิก</div>
                                            <div class="text-muted" style="font-size: 15px;">{{ optional($transaction->user)->firstname." ".optional($transaction->user)->lastname ?? 'รอผู้รับผิดชอบ' }}</div>

                                            <div class="text-success text-center fw-bold py-2 my-2 border border-success rounded bg-white shadow-sm" style="font-size: 14px;">
                                            ✅ REQUESTED<br>
                                <div class="text-success" style="font-size: 11px;">{{ \Carbon\Carbon::parse($transaction->created_at)->format('d/m/y H:i') }}</div>
                                        </div>
                            </div>

                            <!-- วนลูปผู้อนุมัติ -->
                            @foreach($approvals as $approval)
                                @php
                                    $stClass = '';
                                    if ($approval->status == 'APPROVED') { $stClass = 'completed'; }
                                    elseif ($approval->status == 'PENDING') { $stClass = 'active'; }
                                    elseif ($approval->status == 'REJECTED') { $stClass = 'rejected'; }
                                @endphp
                                <div class="timeline-step {{ $stClass }}">
                                    <div class="timeline-step-icon">
                                        @if($approval->status == 'APPROVED') ✓
                                        @elseif($approval->status == 'REJECTED') ✕
                                        @else {{ $approval->step_order }}
                                        @endif
                                    </div>
                                    <div class="small fw-bold">{{ $approval->step_name ?? 'ลำดับการอนุมัติmuj ' . $approval->step_order }}</div>
                                    <div class="text-muted" style="font-size: 15px;">{{ optional($approval->approver)->firstname." ".optional($approval->approver)->lastname ?? 'รอผู้รับผิดชอบ' }}</div>
                                    <div style="font-size: 13px; text-align:center">
                                        @if($approval->status == 'APPROVED') 
                                         <div class="text-success fw-bold py-2 my-2 border border-success rounded bg-white shadow-sm" style="font-size: 14px;">
                                            ✅ APPROVED<br>
                                            <span class="text-muted" style="font-size: 11px;">{{ $approval->action_at }}</span>
                                        </div>
                                        @elseif($approval->status == 'PENDING') <span class="text-warning fw-bold">กำลังรออนุมัติ</span>
                                        @elseif($approval->status == 'REJECTED') <span class="text-danger fw-bold">ตีกลับแล้ว</span>
                                        @else <span class="text-muted">รอคิว</span>
                                        @endif
                                        
                                    </div>
                                </div>
                            @endforeach
                        </div>

                    </div>
                </div>

            </div>
        </div>

        <!-- ================= คอลัมน์ขวา: หน้ากระดาษเอกสาร A4 (ส่วนที่จะถูกปริ้น) ================= -->
        <div class="col-lg-8">
            <div class="a4-document">
                
                <!-- หัวข้อเอกสาร -->
                <div class="text-center my-4">
                    <h4 class="fw-bold text-dark mb-1">ใบเบิกพัสดุหลายรายการ</h4>
                    <p class="text-muted small mb-0">Material Requisition Form</p>
                </div>

                <!-- ข้อมูลเอกสารและผู้เบิก -->
                <div class="row mb-4 bg-light p-3 border rounded-3 g-2">
                    <div class="col-sm-7">
                        <p class="mb-1"><strong>หน่วยงานผู้เบิก:</strong> {{ optional(optional($transaction->user)->organization)->name ?? '-' }}</p>
                        <p class="mb-1"><strong>ชื่อผู้เบิก:</strong> {{ optional($transaction->user)->firstname . ' ' . optional($transaction->user)->lastname ?? '-' }}</p>
                        <p class="mb-0"><strong>วันที่ขอเบิก:</strong> {{ \Carbon\Carbon::parse($transaction->created_at)->format('d/m/Y H:i') }}</p>
                    </div>
                    <div class="col-sm-5 text-sm-end d-flex flex-column justify-content-center align-items-sm-end">
                        <p class="mb-1 text-muted small">เลขที่เอกสาร: <strong class="text-dark">{{ $transaction->ref_no }}</strong></p>
                        <div>
                            @if($transaction->status == 'APPROVED')
                                <span class="badge bg-success px-3 py-2">APPROVED / อนุมัติแล้ว</span>
                            @else
                                <span class="badge bg-warning text-dark px-3 py-2">PENDING / รออนุมัติ</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- ตารางรายการพัสดุ -->
                <div class="table-responsive mb-4">
                    <table class="table table-bordered table-custom align-middle">
                        <thead>
                            <tr>
                                <th width="7%">ลำดับ</th>
                                <th>รายการพัสดุ (Description)</th>
                                <th width="15%">จำนวน</th>
                                <th width="15%">หน่วยนับ</th>
                                <th width="25%">วัตถุประสงค์ / หมายเหตุ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transactions as $index => $tx)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td>
                                    <div class="fw-bold text-dark">{{ optional($tx->item)->name ?? '-' }}</div>
                                    <small class="text-muted">รหัสพัสดุ: {{ optional($tx->item)->code ?? '-' }}</small>
                                    @if($tx->detail)
                                        <br><small class="text-primary">Lot/Serial: {{ $tx->detail->lot_number ?? $tx->detail->serial_number ?? '-' }}</small>
                                    @endif
                                </td>
                                <td class="text-center fw-bold">{{ $tx->quantity }}</td>
                                <td class="text-center">{{ optional($tx->item)->unit ?? 'หน่วย' }}</td>
                                <td><small class="text-muted">{{ $tx->purpose }}</small></td>
                            </tr>
                            @endforeach
                            
                            @if($transactions->count() < 3)
                                <tr style="height: 50px;"><td colspan="5"></td></tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                <!-- ส่วนลงลายมือชื่อ -->
                <div class="row mt-5 pt-4 text-center g-4">
                    <!-- ผู้เบิก -->
                    <div class="col-4">
                        <div class="p-3 border rounded-3 bg-light h-100 d-flex flex-column justify-content-between">
                            <div>
                                <span class="fw-bold text-secondary d-block mb-1">ผู้ขอเบิก</span>
                                <div class="my-3 text-muted">...................................................</div>
                            </div>
                            <div>
                                <span class="small d-block text-dark fw-bold">( {{ optional($transaction->user)->firstname . ' ' . optional($transaction->user)->lastname ?? '-' }} )</span>
                                <small class="text-muted" style="font-size: 11px;">วันที่: {{ \Carbon\Carbon::parse($transaction->created_at)->format('d/m/Y') }}</small>
                            </div>
                        </div>
                    </div>

                    <!-- ผู้อนุมัติ -->
                    @foreach($approvals as $approval)
                        <div class="col-4">
                            <div class="p-3 border rounded-3 bg-light h-100 d-flex flex-column justify-content-between">
                                <div>
                                    <span class="fw-bold text-secondary d-block mb-1">{{ $approval->approver_role_name ?? 'ผู้อนุมัติขั้นที่ ' . $approval->step_order }}</span>
                                 
                                        <div class="my-3 text-muted">...................................................</div>
                                    
                                </div>
                                
                                    <div>
                                        <span class="small d-block text-dark fw-bold">( {{ optional($approval->approver)->firstname ? optional($approval->approver)->firstname . ' ' . optional($approval->approver)->lastname : 'รออนุมัติ' }} )</span>
                                        <small class="text-muted" style="font-size: 11px;">วันที่ ....../....../......</small>
                                    </div>
                            </div>
                        </div>
                    @endforeach
                </div>

            </div>
        </div>

    </div>
</div>

<!-- ================= MODALS สำหรับอนุมัติ / ตีกลับ ================= -->
<div class="modal fade no-print" id="approveModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('inventory.withdraw.step.approve', $transaction->ref_no) }}" method="POST" class="w-100">
            @csrf
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">✅ ยืนยันการอนุมัติใบเบิก</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-start p-4">
                    <p class="mb-2">คุณกำลังจะอนุมัติใบเบิกเลขที่: <strong class="text-success">{{ $transaction->ref_no }}</strong></p>
                    <div class="mb-3 mt-3">
                        <label for="comment" class="form-label small fw-bold text-secondary">ความเห็นเพิ่มเติม (ถ้ามี):</label>
                        <textarea class="form-control" name="comment" rows="3" placeholder="ระบุหมายเหตุการอนุมัติ..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-success px-4">ยืนยันอนุมัติ</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade no-print" id="rejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('inventory.withdraw.step.reject', $transaction->ref_no) }}" method="POST" class="w-100">
            @csrf
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">❌ ยืนยันการตีกลับ (Reject)</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-start p-4">
                    <p class="text-danger small fw-bold mb-2">⚠️ คำเตือน: การตีกลับจะทำให้กระบวนการเบิกนี้ถูกระงับทันที</p>
                    <div class="mb-3 mt-3">
                        <label for="comment" class="form-label small fw-bold text-danger">ระบุเหตุผลที่ไม่อนุมัติ (จำเป็นต้องกรอก):</label>
                        <textarea class="form-control border-danger" name="comment" rows="3" required placeholder="โปรดระบุเหตุผล..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-danger px-4">ยืนยันตีกลับเอกสาร</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>