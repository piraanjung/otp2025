<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        @font-face { font-family: 'THSarabunNew'; src: url({{ public_path('fonts/THSarabunNew.ttf') }}) format('truetype'); }
        body { font-family: 'THSarabunNew'; font-size: 16pt; padding: 40px; }
        .header { font-size: 20pt; font-weight: bold; text-align: center; }
        .section { margin-top: 20px; }
        .line { border-bottom: 1px dotted #000; display: inline-block; min-width: 150px; text-align: center; }
        .footer-sign { width: 100%; margin-top: 50px; }
    </style>
</head>
<body>
    <div class="header">บันทึกข้อความ</div>
    <div style="font-weight: bold;">ส่วนราชการ: <span style="font-weight: normal;">กองสาธารณสุขและสิ่งแวดล้อม {{ $org->org_name }}</span></div>
    <div style="font-weight: bold;">ที่: <span style="font-weight: normal;">สธ. {{ date('Y') }}/{{ str_pad($payout->id, 4, '0', STR_PAD_LEFT) }}</span> <span style="margin-left: 50px;">วันที่: {{ $date }}</span></div>
    <div style="font-weight: bold;">เรื่อง: <span style="font-weight: normal;">ขออนุมัติเบิกจ่ายเงินกองทุนสวัสดิการฌาปนกิจขยะคืนสุข</span></div>
    <hr>

    <div class="section">
        เรียน นายกเทศมนตรี/ผู้บริหาร<br>
        <p style="text-indent: 50px;">ด้วย สมาชิกชื่อ <span class="line">{{ $payout->deceased_name }}</span> ได้ถึงแก่กรรมเมื่อวันที่ ............................ ซึ่งเป็นสมาชิกโครงการธนาคารขยะบักแอโร่ที่มีคุณสมบัติครบถ้วนตามระเบียบกองทุนฯ</p>

        <p style="text-indent: 50px;">ในการนี้ เห็นควรพิจารณาอนุมัติเบิกจ่ายเงินช่วยเหลือฌาปนกิจให้แก่ <span class="line">{{ $payout->beneficiary_name }}</span> (ทายาทผู้รับสิทธิ์) เป็นจำนวนเงินทั้งสิ้น <span class="line">{{ number_format($payout->amount, 2) }}</span> บาท โดยหักจากบัญชีกองทุนสวัสดิการขยะคืนสุข (ยอดเงินคงเหลือปัจจุบัน {{ number_format($payout->fund_balance_at_time, 2) }} บาท)</p>

        <p style="text-indent: 50px;">จึงเรียนมาเพื่อโปรดพิจารณาอนุมัติ</p>
    </div>

    <table class="footer-sign">
        <tr>
            <td style="width: 50%; text-align: center;">
                (ลงชื่อ)............................................<br>
                ( {{ $payout->requester->name }} )<br>
                ตำแหน่ง เจ้าหน้าที่ผู้เสนอ
            </td>
            <td style="width: 50%; text-align: center;">
                (ลงชื่อ)............................................<br>
                (....................................................)<br>
                ตำแหน่ง นายกเทศมนตรี/ผู้บริหาร<br>
                <strong>[ ] อนุมัติ  [ ] ไม่อนุมัติ</strong>
            </td>
        </tr>
    </table>
</body>
</html>
