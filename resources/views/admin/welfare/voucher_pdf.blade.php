<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        @font-face {
            font-family: 'THSarabunNew';
            src: url({{ public_path('fonts/THSarabunNew.ttf') }}) format('truetype');
        }
        body { font-family: 'THSarabunNew'; font-size: 16pt; line-height: 1.2; }
        .text-center { text-center: center; }
        .text-right { text-align: right; }
        .header { font-size: 20pt; font-weight: bold; margin-bottom: 10px; }
        .content-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .footer-table { width: 100%; margin-top: 50px; }
        .sign-line { border-bottom: 1px dotted #000; width: 200px; display: inline-block; }
    </style>
</head>
<body>
    <div class="text-center header">ใบสำคัญรับเงินสวัสดิการฌาปนกิจ</div>
    <div class="text-center">{{ $org->org_name }}</div>

    <div class="text-right">
        วันที่: {{ $date }}<br>
        เลขที่ใบสำคัญ: {{ str_pad($payout->id, 6, '0', STR_PAD_LEFT) }}
    </div>

    <div style="margin-top: 20px;">
        ข้าพเจ้า <span class="sign-line text-center">{{ $payout->beneficiary_name }}</span> (ทายาทผู้รับสวัสดิการ)<br>
        ได้รับเงินช่วยเหลือกรณีการเสียชีวิตของ <span class="sign-line text-center">{{ $payout->deceased_name }}</span><br>
        เป็นจำนวนเงิน <span class="sign-line text-center">{{ number_format($payout->amount, 2) }}</span> บาท
        (............................................................)
    </div>

    <table class="footer-table">
        <tr>
            <td class="text-center">
                ลงชื่อ............................................................ผู้รับเงิน<br>
                ( {{ $payout->beneficiary_name }} )
            </td>
            <td class="text-center">
                ลงชื่อ............................................................ผู้จ่ายเงิน<br>
                ( {{ $payout->requester->name }} )
            </td>
        </tr>
    </table>

    <div style="margin-top: 30px; font-size: 12pt; color: #666;">
        * เงินจำนวนนี้มาจากกำไรส่วนต่างจากการบริหารจัดการขยะในโครงการ AiroBact Bin
    </div>
</body>
</html>
