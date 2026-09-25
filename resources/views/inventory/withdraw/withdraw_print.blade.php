<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ใบเบิกพัสดุหลายรายการ {{ $transaction->ref_no }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        body { font-family: 'Sarabun', sans-serif; background-color: #f8f9fa; }
        .signature-box { width: 85%; margin: 0 auto; margin-top: 30px; }
        
        /* ตั้งค่าสำหรับการปริ้นเอกสารให้ออกมาพอดีหน้า A4 */
        @media print {
            .no-print { display: none !important; } 
            .card { border: none !important; box-shadow: none !important; }
            body { background-color: #fff !important; -webkit-print-color-adjust: exact; }
        }
    </style>
</head>
<body>

<div class="container mt-4 mb-5">
    <!-- แถบปุ่มกดด้านบน (ซ่อนตอน Print) -->
    <div class="mb-4 text-center no-print">
        <a href="{{ route('inventory.history') }}" class="btn btn-secondary">
             &larr; กลับหน้าประวัติ
        </a>
        <button onclick="window.print()" class="btn btn-primary btn-lg ms-2">
             🖨️ พิมพ์ใบเบิก (Print)
        </button>
        
        {{-- ตัวอย่างระบบ E-Approval แยกตามสิทธิ์หรือระดับ ----}}
        @if($transaction->status == 'PENDING')
            {{-- สมมติให้สิทธิ์ Stock Keeper (เจ้าหน้าที่พัสดุ) ตรวจสอบและกดรับทราบ/อนุมัติขั้นที่ 1 --}}
            <form action="{{ route('inventory.withdraw.approve', $transaction->ref_no) }}" method="POST" class="d-inline" onsubmit="return confirm('ยืนยันการอนุมัติใบเบิกชุดนี้?');">
                @csrf
                <button class="btn btn-success btn-lg ms-2">
                     ✅ E-Approval (อนุมัติการเบิก)
                </button>
            </form>
        @endif
    </div>

    <!-- ส่วนกระดาษฟอร์ม A4 -->
    <div class="card p-5 shadow-sm mx-auto bg-white" style="max-width: 210mm; min-height: 297mm;">
        
        <!-- ส่วนหัวกระดาษ -->
        <div class="text-center mb-4">
            <h3 class="fw-bold">ใบเบิกพัสดุหลายรายการ (Material Requisition Form)</h3>
            <p class="text-muted mb-0">เลขที่เอกสาร: <strong>{{ $transaction->ref_no }}</strong></p>
        </div>

        <div class="row mb-3">
            <div class="col-6">
                <strong>หน่วยงานผู้เบิก:</strong> {{ $transaction->user->organization->name ?? Auth::user()->organization->name ?? '-' }}<br>
                <strong>ชื่อผู้เบิก (Requester):</strong> {{ $transaction->requester_name ?? '-' }}<br>
                <strong>วันที่ขอเบิก:</strong> {{ \Carbon\Carbon::parse($transaction->created_at)->format('d/m/Y H:i') }}
            </div>
            <div class="col-6 text-end">
                <strong>สถานะเอกสาร:</strong><br>
                @if($transaction->status == 'APPROVED')
                    <span class="badge bg-success fs-6 px-3 py-2 mt-1">APPROVED / อนุมัติแล้ว</span>
                @else
                    <span class="badge bg-warning text-dark fs-6 px-3 py-2 mt-1">PENDING / รออนุมัติ</span>
                @endif
            </div>
        </div>

        <!-- ตารางแสดงรายการพัสดุที่เบิก (หลายรายการ) -->
        <table class="table table-bordered border-dark align-middle">
            <thead class="table-light text-center">
                <tr>
                    <th width="8%">ลำดับ</th>
                    <th>รายการพัสดุ (Description)</th>
                    <th width="15%">จำนวนที่เบิก</th>
                    <th width="15%">หน่วยนับ</th>
                    <th width="22%">วัตถุประสงค์ / หมายเหตุ</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transactions as $index => $tx)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        <div class="fw-bold">{{ $tx->item->name ?? '-' }}</div>
                        <small class="text-muted">รหัสพัสดุ: {{ $tx->item->code ?? '-' }}</small>
                        @if($tx->detail)
                            <br><small class="text-primary">Lot / Serial: {{ $tx->detail->lot_number ?? $tx->detail->serial_number ?? '-' }}</small>
                        @endif
                    </td>
                    <td class="text-center fw-bold fs-5">{{ $tx->quantity }}</td>
                    <td class="text-center">{{ $tx->item->unit ?? 'หน่วย' }}</td>
                    <td><small>{{ $tx->purpose }}</small></td>
                </tr>
                @endforeach
                
                {{-- เว้นพื้นที่ว่างในตารางกรณีรายการน้อย ให้ฟอร์มดูสมดุล --}}
                @if($transactions->count() < 3)
                    <tr style="height: 60px;"><td colspan="5"></td></tr>
                @endif
            </tbody>
        </table>

        <!-- ส่วนลงลายมือชื่อ 3 ระดับ (ผู้เบิก, เจ้าหน้าที่พัสดุ, ผู้อนุมัติ) -->
        <div class="row mt-5 pt-4 text-center">
            <!-- ช่องที่ 1: ผู้เบิก -->
            <div class="col-4">
                <div class="mb-1"><strong>ผู้เบิก (Requester)</strong></div>
                <div class="signature-box"></div>
                <div class="mt-2">
                       <span>...................................................</span><br>
                    <span>( {{$transaction->user->prefix."". $transaction->user->firstname." ". $transaction->user->lastname}} )</span><br>
                    <small class="text-muted">วันที่ ....../....../......</small>
                </div>
            </div>

            <!-- ช่องที่ 3: ผู้อนุมัติ (Approver) -->
            <div class="col-4">
                <div class="mb-1"><strong>ผู้อนุมัติ (Approver)</strong></div>
                
                @if($transaction->status == 'APPROVED')
                   <div class="text-success fw-bold py-2 border border-success rounded bg-light mt-3">
                       <small>✅ E-APPROVED BY</small><br>
                       <span>{{ $approver->prefix."".$approver->firstname." ".$approver->lastname ?? 'System Admin' }}</span><br>
                       {{-- <span>{{ $transaction->approver_user->firstname ?? 'System Admin' }}</span><br> --}}
                       <small class="text-muted" style="font-size: 11px;">{{ $transaction->approved_at ?? $transaction->updated_at }}</small>
                   </div>
                @else
                   <div class="signature-box"></div>
                   <div class="mt-2">
                       <span>...................................................</span><br>
                        <span>( {{ $approver->prefix."".$approver->firstname." ".$approver->lastname ?? 'System Admin' }} )</span><br>
                       <small class="text-muted">วันที่ ....../....../......</small>
                   </div>
                @endif
            </div>

            <!-- ช่องที่ 2: เจ้าหน้าที่พัสดุ (Stock Keeper) -->
            <div class="col-4">
                <div class="mb-1"><strong>เจ้าหน้าที่เบิกจ่ายพัสดุ </strong></div>
                
                @if($transaction->status == 'APPROVED' && isset($transaction->keeper_user))
                    <!-- แสดงลายเซ็นอิเล็กทรอนิกส์ของเจ้าหน้าที่พัสดุ (ถ้ามีบันทึกแยกไว้) -->
                    <div class="text-success fw-bold py-2 border border-success rounded bg-light mt-3">
                        <small>E-SIGNED (Keeper)</small><br>
                        <span>{{ $transaction->keeper_user->firstname ?? 'Checked' }}</span>
                    </div>
                @else
                    <div class="signature-box"></div>
                    <div class="mt-2">
                        <span>...................................................</span><br>
                       <span>( {{ $approver->prefix."".$approver->firstname." ".$approver->lastname ?? 'System Admin' }} )</span><br>

                        <small class="text-muted">วันที่ ....../....../......</small>
                    </div>
                @endif
            </div>

            
        </div>

    </div>
</div>

</body>
</html>