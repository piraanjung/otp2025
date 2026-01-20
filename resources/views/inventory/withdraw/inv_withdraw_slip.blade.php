<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ใบเบิกพัสดุ {{ $transaction->ref_no }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        body { font-family: 'Sarabun', sans-serif; } /* ควรใช้ Font ไทย */
        .signature-box { border-top: 1px solid #000; width: 80%; margin: 0 auto; margin-top: 50px; }
        
        /* ตั้งค่าสำหรับการปริ้น */
        @media print {
            .no-print { display: none !important; } /* ซ่อนปุ่มต่างๆ */
            .card { border: none !important; }
            body { -webkit-print-color-adjust: exact; }
        }
    </style>
</head>
<body class="bg-light">

<div class="container mt-4">
    <div class="mb-4 text-center no-print">
        <a href="{{ route('inventory.history') }}" class="btn btn-secondary">
             &larr; กลับ
        </a>
        <button onclick="window.print()" class="btn btn-primary btn-lg ms-2">
             พิมพ์ใบเบิก (Print)
        </button>
        
        @if($transaction->status == 'PENDING')
        <form action="{{ route('inventory.withdraw.approve', $transaction->id) }}" method="POST" class="d-inline" onsubmit="return confirm('ยืนยันการอนุมัติและตัดสต็อก?');">
            @csrf
            <button class="btn btn-success btn-lg ms-2">
                 ✅ อนุมัติ (E-Approval)
            </button>
        </form>
        @endif
    </div>

    <div class="card p-5 shadow-sm mx-auto bg-white" style="max-width: 210mm; min-height: 297mm;">
        
        <div class="text-center mb-5">
            <h3 class="fw-bold">ใบเบิกพัสดุ (Material Requisition Form)</h3>
            <p>เลขที่เอกสาร: <strong>{{ $transaction->ref_no }}</strong></p>
        </div>

        <div class="row mb-4">
            <div class="col-6">
                <strong>หน่วยงาน:</strong> {{ Auth::user()->organization->name ?? '-' }}<br>
                <strong>วันที่ขอเบิก:</strong> {{ \Carbon\Carbon::parse($transaction->created_at)->format('d/m/Y') }}
            </div>
            <div class="col-6 text-end">
                <strong>สถานะ:</strong> 
                @if($transaction->status == 'APPROVED')
                    <span class="text-success border border-success px-2 py-1 rounded">ANPROVED / อนุมัติแล้ว</span>
                @else
                    <span class="text-warning border border-warning px-2 py-1 rounded">PENDING / รออนุมัติ</span>
                @endif
            </div>
        </div>

        <table class="table table-bordered border-dark">
            <thead class="bg-light">
                <tr class="text-center">
                    <th width="10%">ลำดับ</th>
                    <th>รายการพัสดุ (Description)</th>
                    <th width="15%">จำนวน</th>
                    <th width="15%">หน่วยนับ</th>
                    <th width="20%">หมายเหตุ</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-center">1</td>
                    <td>
                        {{ $transaction->item->name }} <br>
                        <small>รหัส: {{ $transaction->item->code ?? '-' }}</small>
                        @if($transaction->detail)
                            <br><small>Lot: {{ $transaction->detail->lot_number }}</small>
                        @endif
                    </td>
                    <td class="text-center fw-bold">{{ $transaction->quantity }}</td>
                    <td class="text-center">{{ $transaction->item->unit }}</td>
                    <td>{{ $transaction->purpose }}</td>
                </tr>
                <tr style="height: 100px;"><td colspan="5"></td></tr>
            </tbody>
        </table>

        <div class="row mt-5 pt-5 text-center">
            <div class="col-4">
                <div class="mb-5">ผู้เบิก (Requester)</div>
                <div class="signature-box"></div>
                <div class="mt-2">({{ $transaction->requester_name }})</div>
                <div>วันที่ ............/............/............</div>
            </div>
            <div class="col-4">
                <div class="mb-5">เจ้าหน้าที่พัสดุ (Stock Keeper)</div>
                <div class="signature-box"></div>
                <div class="mt-2">({{ Auth::user()->name }})</div>
                <div>วันที่ ............/............/............</div>
            </div>
            <div class="col-4">
                <div class="mb-5">ผู้อนุมัติ (Approver)</div>
                
                @if($transaction->status == 'APPROVED')
                   <div class="text-success fw-bold py-2 border border-success rounded bg-light">
                       E-SIGNED by {{ $transaction->approver_user->name ?? 'System' }}<br>
                       <small>{{ $transaction->approved_at }}</small>
                   </div>
                @else
                   <div class="signature-box"></div>
                   <div class="mt-2">({{ $transaction->approver_name }})</div>
                   <div>วันที่ ............/............/............</div>
                @endif
            </div>
        </div>

    </div>
</div>

</body>
</html>