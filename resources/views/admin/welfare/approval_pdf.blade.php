<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        
        @font-face {
            font-family: 'THSarabunNew';
            src: url({{ public_path('fonts/THSarabunNew.ttf') }});
        }

        body {
            font-family: 'THSarabunNew';
            font-size: 16pt;
            padding: 40px;
        }

        .header {
            font-size: 20pt;
            font-weight: bold;
            text-align: center;
        }

        .section {
            margin-top: 20px;
        }

        .line {
            border-bottom: 1px dotted #000;
            display: inline-block;
            min-width: 150px;
            text-align: center;
        }

        .footer-sign {
            width: 100%;
            margin-top: 50px;
        }
    </style>
</head>

<body>
    <div class="" style="font-weight: normal; font-size: 40px; text-align: center;">บันทึกข้อความ</div>
    <div style="font-weight: normal; font-size: 20px; ">ส่วนราชการ: <span style="font-weight: normal;">กองสาธารณสุขและสิ่งแวดล้อม
            {{ $org->org_name }}</span></div>
    <div sstyle="font-weight: normal;">ที่: <span style="font-weight: normal;">สธ.
            {{ date('Y') }}/{{ str_pad($payout->id, 4, '0', STR_PAD_LEFT) }}</span> <span
            style="margin-left: 50px;">วันที่: {{ $date }}</span></div>
    <div style="font-weight: normal;">เรื่อง: <span
            style="font-weight: normal;">ขออนุมัติเบิกจ่ายเงินกองทุนสวัสดิการฌาปนกิจขยะคืนสุข</span></div>
    <hr>

    <div class="section">
        เรียน นายกเทศมนตรี/ผู้บริหาร<br>
        <p style="text-indent: 15px;">ด้วย สมาชิกชื่อ <span class="line">{{ $payout->user->firstname." ".$payout->user->lastname }}</span>
            <div></div>
            บ้านเลขที่<span class="line">{{ $payout->user->adress." ".$payout->user->user_zone->zone_name }}</span>
            ตำบล<span class="line">{{ $payout->user->user_tambon->tambon_name }}</span>
            อำเภอ<span class="line">{{ $payout->user->user_district->district_name }}</span>
            จังหวัด<span class="line">{{ $payout->user->user_province->province_name }}</span>
            ซึ่งเป็นสมาชิกกองทุนฌาปณากิจสงเคราะห์ที่มีคุณสมบัติครบถ้วนตามระเบียบกองทุนฯ ได้มีสมาชิกในครอบครัวถึงแก่กรรมคือ 
            <div>
                <span class="line">{{ $payout->deceased_name }}</span>
            เมื่อวันที่ <span class="line">{{ $date  }}</span>

            </div>
            </p>

        <p style="text-indent: 50px;">
            ในการนี้ เห็นควรพิจารณาอนุมัติเบิกจ่ายเงินช่วยเหลือฌาปนกิจให้แก่
            <span class="line">{{ $payout->beneficiary_name }}</span> ผู้รับสิทธิ์
            เป็นจำนวนเงินทั้งสิ้น <span class="line">{{ number_format($payout->amount, 2) }}</span> บาท
            โดยหักจากบัญชีกองทุนสวัสดิการขยะคืนสุข
        </p>
        <p style="text-indent: 50px;">จึงเรียนมาเพื่อโปรดพิจารณาอนุมัติ</p>
    </div>

    <table class="footer-sign">
        <tr>
            <td style="width: 50%; text-align: center;">
                (ลงชื่อ)............................................<br>
                ( {{ $payout->requester->firstname." ".$payout->requester->lastname }} )<br>
                ตำแหน่ง เจ้าหน้าที่ผู้เสนอ
            </td>
            <td style="width: 50%; text-align: center;">
                (ลงชื่อ)............................................<br>
                (....................................................)<br>
                ตำแหน่ง นายกเทศมนตรี/ผู้บริหาร<br>
                <div>[ ] อนุมัติ [ ] ไม่อนุมัติ</div>
            </td>
        </tr>
    </table>
</body>

</html>