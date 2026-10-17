<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ใบยืนยันการขอถอนเงินสด - {{ $withdraw->verification_code }}</title>
    <style>
        @page { size: A5 landscape; margin: 15mm; }
        body { font-family: 'Sarabun', sans-serif; font-size: 14px; line-height: 1.5; color: #000; background: #fff; margin: 0; }
        .receipt-box { border: 2px solid #000; padding: 20px; border-radius: 8px; }
        .header { text-align: center; border-bottom: 2px dashed #000; padding-bottom: 10px; margin-bottom: 15px; }
        .header h3 { margin: 0; font-size: 18px; }
        .header p { margin: 2px 0 0 0; font-size: 12px; }
        .code-box { text-align: center; border: 1px solid #000; background: #f8f9fa; padding: 8px; margin: 10px 0; font-size: 18px; font-weight: bold; }
        .table-info { width: 100%; margin-bottom: 15px; border-collapse: collapse; }
        .table-info td { padding: 4px 0; vertical-align: top; }
        .sign-section { width: 100%; margin-top: 25px; }
        .sign-box { width: 48%; display: inline-block; text-align: center; vertical-align: top; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="no-print" style="text-align: right; margin-bottom: 15px;">
        <button onclick="window.print()" style="padding: 8px 16px; font-size: 14px; cursor: pointer; background: #2dce89; color: #fff; border: none; border-radius: 4px;">
            🖨️ พิมพ์เอกสารใบยืนยัน
        </button>
    </div>

    <div class="receipt-box">
        <div class="header">
            <h3>ใบร้องขอถอนเงินสดธนาคารขยะ</h3>
            <p>องค์กรปกครองส่วนท้องถิ่น (ระบบบริหารจัดการธนาคารขยะ PI-OS)</p>
        </div>

        <div class="code-box">
            รหัสยืนยันการรับเงินสด: {{ $withdraw->verification_code }}
        </div>

        <table class="table-info">
            <tr>
                <td style="width: 35%;"><strong>วันที่ยื่นเรื่อง:</strong></td>
                <td>{{ \Carbon\Carbon::parse($withdraw->created_at)->format('d/m/Y H:i') }} น.</td>
            </tr>
            <tr>
                <td><strong>ชื่อ-นามสกุล สมาชิก:</strong></td>
                <td>{{ $withdraw->user->firstname ?? '' }} {{ $withdraw->user->lastname ?? '' }}</td>
            </tr>
            <tr>
                <td><strong>จำนวนเงินที่ขอถอน:</strong></td>
                <td><strong style="font-size: 16px;">{{ number_format((float)$withdraw->amount, 2) }} บาท</strong></td>
            </tr>
            <tr>
                <td><strong>กำหนดวันรับเงินสด:</strong></td>
                <td><strong style="color: #0056b3;">วัน {{ \Carbon\Carbon::parse($withdraw->payout_date)->format('d/m/Y') }}</strong></td>
            </tr>
            @if($withdraw->is_proxy)
                <tr>
                    <td><strong>ผู้รับเงินแทน (มอบอำนาจ):</strong></td>
                    <td>{{ $withdraw->proxy_name }} (เลขบัตร: {{ $withdraw->proxy_id_card ?? '—' }})</td>
                </tr>
            @endif
        </table>

        <div style="font-size: 11px; color: #555; background: #eee; padding: 6px; border-radius: 4px;">
            * หมายเหตุ: กรุณานำใบยืนยันฉบับนี้พร้อมบัตรประจำตัวประชาชน มาแสดง ณ เทศบาล/อบต. ในวันนัดรับเงินสด
        </div>

        <!-- ช่องลงลายมือชื่อ -->
        <div class="sign-section">
            <div class="sign-box">
                ลงชื่อ..........................................................<br>
                ( {{ $withdraw->is_proxy ? $withdraw->proxy_name : ($withdraw->user->firstname ?? '').' '.($withdraw->user->lastname ?? '') }} )<br>
                <strong>สมาชิก / ผู้รับเงินสด</strong>
            </div>
            <div class="sign-box">
                ลงชื่อ..........................................................<br>
                ( .......................................................... )<br>
                <strong>เจ้าหน้าที่การเงิน / ผู้จ่ายเงิน</strong>
            </div>
        </div>
    </div>
</body>
</html>