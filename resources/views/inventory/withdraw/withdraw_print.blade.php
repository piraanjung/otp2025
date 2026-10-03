@extends('inventory.inv_master')

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

        /* ตารางข้อมูล */
        .table-custom th {
            background-color: #f8f9fa !important;
            font-weight: 600;
            text-align: center;
        }

        /* Stepper Timeline แนวตั้ง */
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

        /* ตั้งค่าสำหรับการปริ้นเอกสาร */
        @media print {
        .no-print { 
            display: none !important; 
        } 
            /* body { 
                background-color: #fff !important; 
                margin: 0 !important;
                padding: 0 !important;
            } */
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
  }
}
        .container-fluid {
            max-width: 100% !important;
            padding: 0 !important;
            margin: 0 !important;
        }
        
        /* ปรับแต่ง A4 ตอนพิมพ์ให้พอดีเป๊ะ ไม่ล้นหน้า */
        .a4-document { 
            box-shadow: none !important; 
            padding: 15mm !important; /* ลด padding ตอนพิมพ์เล็กน้อยเพื่อกันล้น */
            margin: 0 !important;
            width: 100% !important;
            min-height: auto !important; /* ปลดล็อก min-height เพื่อไม่ให้บังคับยืดเป็นหน้าเปล่า */
            border: none !important;
        }

        /* 💡 แก้ไขจุดสำคัญ: สั่งขึ้นหน้าใหม่เฉพาะตัวที่ "ไม่ใช่ตัวสุดท้าย" */
        /* .a4-document:not(:last-child) {
            page-break-after: always;
            break-after: page;
        } */

        /* ตัด Margin ส่วนเกินออกทั้งหมดตอนปริ้น */
        .mb-5, .my-4, .mt-5 {
            margin-bottom: 1rem !important;
            margin-top: 1rem !important;
        }
    }
    .sidebar-scroll {
            max-height: 88vh; /* กำหนดความสูงสูงสุดตามหน้าจอ */
            overflow-y: auto;  /* ให้แสดง Scrollbar เฉพาะเมื่อเนื้อหายาวเกิน */
            padding-right: 5px;
        }
        
        /* ตกแต่งหน้าตา Scrollbar ให้ดูสวยงาม (สำหรับ Chrome, Edge, Safari) */
        .sidebar-scroll::-webkit-scrollbar {
            width: 6px;
        }
        .sidebar-scroll::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }
        .sidebar-scroll::-webkit-scrollbar-thumb {
            background: #ccc;
            border-radius: 4px;
        }
        .sidebar-scroll::-webkit-scrollbar-thumb:hover {
            background: #999;
        }

        /* 🖱️ เพิ่ม Scrollbar ให้ฝั่งคอลัมน์ขวา (กรณีอยากให้หน้าจอเว็บแสดง A4 แบบมีกรอบสكرولดูทีละหน้า) 
           * หากต้องการให้หน้าจอฝั่งขวาสกรอลได้ ให้เปิดใช้งาน .preview-scroll นี้ครอบ .a4-document
        */
        .preview-scroll {
            max-height: 88vh;
            overflow-y: auto;
            padding: 10px;
        }
        .preview-scroll::-webkit-scrollbar {
            width: 8px;
        }
        .preview-scroll::-webkit-scrollbar-track {
            background: #e9ecef;
            border-radius: 4px;
        }
        .preview-scroll::-webkit-scrollbar-thumb {
            background: #adb5bd;
            border-radius: 4px;
        }

        .hidden{
            display: none;
        }
    
    </style>
@endsection

@section('content')

    <div class="container-fluid py-4 px-lg-5">
        <div class="row g-4">
            
            <!-- ================= คอลัมน์ซ้าย: เมนูควบคุม + Timeline แยกตาม Workflow (ซ่อนตอน Print) ================= -->
            <div class="col-lg-4 no-print">
                <div class="sticky-top" style="top: 20px;">
                                                     {{-- <a href="{{ route('inventory.history') }}" class="btn btn-sm btn-outline-secondary py-2">
                                    &larr; กลับหน้าประวัติ
                                </a> --}}
                    <!-- เมนูจัดการทั่วไป -->
                    <div class="card border-0 shadow-sm rounded-3 mb-3">
                        <div class="card-body">
                            <h5 class="fw-bold text-dark mb-3">⚙️ เมนูจัดการเอกสาร</h5>
                            <div class="d-column gap-2">

                                <button onclick="print_all()" class="btn btn-sm btn-primary py-2 fw-bold">
                                    🖨️ พิมพ์เอกสารทั้งหมด
                                </button>
                                 <button onclick="view_peper(0)" class="btn btn-sm btn-info  py-2 fw-bold">
                                    ดูเอกสารทั้งหมด
                                </button>
                               
                            </div>
                        </div>
                    </div>

                    <!-- 💡 วนลูปแยกส่วนการอนุมัติและ Timeline ตามแต่ละ Workflow ที่แตกต่างกัน -->
                    <div class="preview-scroll">
                        @php $i =1; @endphp
                    @foreach($workflowGroups as $group)
                        <div class="card border-0 shadow-sm rounded-3 mb-3 border-start border-4 border-primary">
                            <div class="card-body">
                                <a href="#" onclick="print_only({{ $i }})" class="btn btn-primary btn-sm text-end">ปริ้น</a>
                                <a href="#" onclick="view_paper({{ $i++ }})" class="btn btn-info btn-sm text-end cap-2">ดูรายละเอียด</a>
                
                                <h6 class="fw-bold text-primary mb-3 mt-2">
                                    📋 สายงาน: {{ $group['workflow_name'] }}
                                </h6>
                                <!-- ส่วนอนุมัติเฉพาะสายงานนี้ (แสดงเฉพาะตอน PENDING) -->
                                <div class="row">
                                    <div class="col-6 bottom-0">
                                        @php $currentAppr = $group['currentApproval']; @endphp
                                    

                                        @if($transaction->status == 'PENDING' ||  collect($currentAppr)->isNotEmpty())
                                            <div class="bg-light shadow-lg p-3 rounded-3 mb-3 text-center">
                                                {{-- @dd($currentAppr) --}}
                                                @if(isset($currentAppr) && $currentAppr->approver_id == Auth::id())
                                                    <div class="d-grid gap-2">
                                                        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#approveModal-{{ $group['workflow_id'] }}">
                                                            ✅ อนุมัติขั้นที่ {{ $currentAppr->step_order }}
                                                        </button>
                                                        <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#rejectModal-{{ $group['workflow_id'] }}">
                                                            ❌ ตีกลับ / ไม่อนุมัติ
                                                        </button>
                                                    </div>
                                                @elseif($transaction->user_id == Auth::id())
                                                    <div class="alert alert-warning py-2 mb-0 small" role="alert">
                                                        ⏳ รอการตรวจสอบและอนุมัติสายงานนี้
                                                    </div>
                                                @elseif(isset($currentAppr))
                                                    <div class="text-muted small">
                                                        🔒 รอการดำเนินการจาก:<br>
                                                        <strong class="text-dark">{{ optional($currentAppr->approver)->firstname." ".optional($currentAppr->approver)->lastname ?? 'ผู้อนุมัติขั้นที่ ' . $currentAppr->step_order }}</strong>
                                                    </div>
                                                @else
                                                    <div class="text-success small fw-bold">
                                                        ✨ สายงานนี้อนุมัติครบถ้วนแล้ว
                                                    </div>
                                                @endif
                                            </div>
                                       @else
                                       {{-- @dd($group['currentApproval']) --}}
                                            {{-- ถ้า $group['currentApproval'] == null  ให้แสดงกำลังทำการเบิก
                                            else $ttranction->status == 'complete' และ  inv_approval_workflow == 'Approved' 
                                                    ให้แสดงว่าทำกาเบิกจ่ายแล้ว
                                            xxx --}}
                                            <div class="card  shadow-lg">
                                                <div class="card-body text-center">
                                                    @if ($group['currentApproval'] == null)
                                                        @if($group['approvals'][0]->status == 'APPROVED')
                                                        <p>อนุมัติครบแล้ว</p> 
                                                        <p>กำลังทำการเบิก</p> 
                                                        @else
                                                        <p>อนุมัติครบแล้ว</p> 
                                                        <p>ทำการเบิกจ่ายเรียบร้อย</p> 
                                                        @endif

                                                    @endif

                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-6">
                                        <!-- Timeline ขั้นตอนการอนุมัติเฉพาะสายงานนี้ -->
                                        <div class="timeline-steps">
                                            <div class="timeline-step completed">
                                                <div class="timeline-step-icon">✓</div>
                                                <div class="small fw-bold">ผู้ขอเบิก</div>
                                                <div class="text-muted" style="font-size: 13px;">{{ optional($transaction->requester)->firstname." ".optional($transaction->requester)->lastname ?? '-' }}</div>
                                            </div>

                                            @foreach($group['approvals'] as $approval)
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
                                                    
                                                    <div class="small fw-bold">{{ $approval->step_name ?? 'ลำดับที่ ' . $approval->step_order }}</div>
                                                    <div class="text-muted" style="font-size: 13px;">{{ optional($approval->approver)->firstname." ".optional($approval->approver)->lastname ?? 'รอผู้รับผิดชอบ' }}</div>
                                                    <div style="font-size: 12px; text-align:center" class="mt-1">
                                                        @if($approval->status == 'APPROVED') 
                                                            <div class="text-success fw-bold py-1 px-2 border border-success rounded bg-white shadow-sm" style="font-size: 12px;">
                                                                ✅ APPROVED<br>
                                                                <span class="text-muted" style="font-size: 10px;">{{ $approval->action_at }}</span>
                                                            </div>
                                                        @elseif($approval->status == 'PENDING') 
                                                            <span class="text-warning fw-bold">กำลังรออนุมัติ</span>
                                                        @elseif($approval->status == 'REJECTED') 
                                                            <span class="text-danger fw-bold">ตีกลับแล้ว</span>
                                                        @else 
                                                            <span class="text-muted">รอคิว</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    </div>
                </div>
            </div>

            <!-- ================= คอลัมน์ขวา: วนลูปสร้างหน้ากระดาษ A4 ตาม Workflow ที่แตกต่างกัน ================= -->
            <div class="col-lg-8">
                @php
                    $i= 1;
                @endphp
                <div id="print-area">
                    @foreach($workflowGroups as $index => $group)
                        <div class="a4-document @if(!$loop->last) mb-5  @endif" id="a4_{{ $i++ }}">
                            
                            <!-- หัวข้อเอกสาร -->
                            <div class="text-center my-4">
                                <h4 class="fw-bold text-dark mb-1">ใบเบิกพัสดุ</h4>
                                {{-- <p class="text-muted small mb-0">
                                    สายการอนุมัติ: <span class="text-primary fw-bold">{{ $group['workflow_name'] }}</span>
                                </p> --}}
                            </div>

                            <!-- ข้อมูลเอกสารและผู้เบิก -->
                            <div class="row mb-4 bg-light p-3 border rounded-3 g-2">
                                <div class="col-sm-7">
                                    <p class="mb-1"><strong>หน่วยงานผู้เบิก:</strong> {{ optional(optional($transaction->requester)->organization)->name ?? '-' }}</p>
                                    <p class="mb-1"><strong>ชื่อผู้เบิก:</strong> {{ optional($transaction->requester)->firstname . ' ' . optional($transaction->requester)->lastname ?? '-' }}</p>
                                    <p class="mb-0"><strong>วันที่ขอเบิก:</strong> {{ \Carbon\Carbon::parse($transaction->created_at)->format('d/m/Y H:i') }}</p>
                                </div>
                                <div class="col-sm-5 text-sm-end d-flex flex-column justify-content-center align-items-sm-end">
                                    <p class="mb-1 text-muted small">เลขที่เอกสาร: <strong class="text-dark">{{ $transaction->ref_no }}</strong></p>
                                    {{-- <div>
                                        @if($transaction->status == 'APPROVED')
                                            <span class="badge bg-success px-3 py-2">APPROVED / อนุมัติแล้ว</span>
                                        @else
                                            <span class="badge bg-warning text-dark px-3 py-2">PENDING / รออนุมัติ</span>
                                        @endif
                                    </div> --}}
                                </div>
                            </div>

                            <!-- ตารางรายการพัสดุ (เฉพาะไอเทมใน Workflow นี้) -->
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
                                        @foreach($group['transactions'] as $txIndex => $tx)
                                        <tr>
                                            <td class="text-center">{{ $txIndex + 1 }}</td>
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
                                        
                                        @if($group['transactions']->count() < 3)
                                            <tr style="height: 50px;"><td colspan="5"></td></tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>

                            <!-- ส่วนลงลายมือชื่อ (แสดงผู้อนุมัติตามสาย Workflow นั้นๆ จริงๆ) -->
                            <div class="row mt-5 pt-4 text-center g-4">
                                <!-- ผู้เบิก -->
                                <div class="col-4">
                                    <div class="p-3 border rounded-3  h-100 d-flex flex-column justify-content-between">
                                        <div>
                                            <span class="fw-bold text-secondary d-block mb-1">ผู้ขอเบิก</span>
                                            <div class="my-3 text-muted">.......................................</div>
                                        </div>
                                        <div>
                                            <span class="small d-block text-dark fw-bold">( {{ optional($transaction->requester)->firstname . ' ' . optional($transaction->requester)->lastname ?? '-' }} )</span>
                                            <small class="text-muted" style="font-size: 11px;">วันที่: {{ \Carbon\Carbon::parse($transaction->created_at)->format('d/m/Y') }}</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- ผู้อนุมัติเฉพาะของ Workflow นี้ -->
                                @foreach($group['approvals'] as $approval)
                                    <div class="col-4">
                                        <div class="p-3 border rounded-3 h-100 d-flex flex-column justify-content-between">
                                            <div>
                                                <span class="fw-bold text-secondary d-block mb-1">{{ $approval->step_name ?? 'ผู้อนุมัติขั้นที่ ' . $approval->step_order }}</span>
                                                <div class="my-3 text-muted">.......................................</div>
                                            </div>
                                            <div>
                                                <span class="small d-block text-dark fw-bold">( {{ optional($approval->approver)->firstname ? optional($approval->approver)->firstname . ' ' . optional($approval->approver)->lastname : 'รออนุมัติ' }} )</span>
                                                <small class="text-muted" style="font-size: 11px;">
                                                    @if($approval->status == 'APPROVED')
                                                        อนุมัติเมื่อ: {{ \Carbon\Carbon::parse($approval->action_at)->format('d/m/Y') }}
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
                    <div style="break-after: page; page-break-after: always;"></div>
        
                    @endforeach
                </div>
            </div>

        </div>
    </div>

    <!-- ================= MODALS สำหรับอนุมัติ / ตีกลับ (แยกตาม Workflow ID) ================= -->
    @foreach($workflowGroups as $group)
        <div class="modal fade no-print" id="approveModal-{{ $group['workflow_id'] }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <form action="{{ route('inventory.withdraw.step.approve', $transaction->ref_no) }}" method="POST" class="w-100">
                    @csrf
                    <input type="hidden" name="workflow_id" value="{{ $group['workflow_id'] }}">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header bg-success text-white">
                            <h5 class="modal-title">✅ ยืนยันการอนุมัติ (สายงาน: {{ $group['workflow_name'] }})</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-start p-4">
                            <p class="mb-2">คุณกำลังจะอนุมัติใบเบิกเลขที่: <strong class="text-success">{{ $transaction->ref_no }}</strong></p>
                            <div class="mb-3 mt-3">
                                <label class="form-label small fw-bold text-secondary">ความเห็นเพิ่มเติม (ถ้ามี):</label>
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

        <div class="modal fade no-print" id="rejectModal-{{ $group['workflow_id'] }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <form action="{{ route('inventory.withdraw.step.reject', $transaction->ref_no) }}" method="POST" class="w-100">
                    @csrf
                    <input type="hidden" name="workflow_id" value="{{ $group['workflow_id'] }}">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title">❌ ยืนยันการตีกลับ (สายงาน: {{ $group['workflow_name'] }})</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-start p-4">
                            <p class="text-danger small fw-bold mb-2">⚠️ คำเตือน: การตีกลับจะทำให้สายงานนี้ถูกระงับทันที</p>
                            <div class="mb-3 mt-3">
                                <label class="form-label small fw-bold text-danger">ระบุเหตุผลที่ไม่อนุมัติ (จำเป็น):</label>
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
    @endforeach
@endsection
<!-- Bootstrap 5 JS Bundle -->
@section('scripts')
    <script>
    function print_only(i){
        $('.a4-document').each(function(){
            if(!$(this).hasClass('hidden')){
                $(this).css('display','none')
            }
        }) 
        setTimeout(() => {
            $(`#a4_${i}`).css('display','block')
             window.print()
        }, 100);
        
       
    }

    function print_all(){
        $('.a4-document').each(function(){
                $(this).css('display','block')
        }) 
         setTimeout(() => {
             window.print()
        }, 100);
        
    }
    function view_paper(i){
            if(i === 0){
                $('.a4-document').each(function(){
                    $(this).css('display','block')
                }) 
            }else{
                 $('.a4-document').each(function(){
                    $(this).css('display','none')
                }) 

                setTimeout(() => {
                    $(`#a4_${i}`).css('display','block')
                 }, 200);

            }
          
    }
</script>
@endsection
