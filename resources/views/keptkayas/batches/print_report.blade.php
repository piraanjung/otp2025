<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>บันทึกข้อความขออนุมัติเบิกจ่ายเงินถอนธนาคารขยะ - {{ $batch->batch_no }}</title>
    <style>
        body {
            font-family: 'Sarabun', sans-serif;
            font-size: 14px;
            line-height: 1.6;
            color: #000;
            margin: 20px;
        }

        .header {
            text-align: center;
            font-weight: bold;
            font-size: 18px;
            mb-3;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .table th,
        .table td {
            border: 1px solid #000;
            padding: 6px 8px;
            font-size: 13px;
        }

        .text-center {
            text-align: center;
        }

        .text-end {
            text-align: right;
        }

        .sign-section {
            margin-top: 40px;
            width: 100%;
        }

        .sign-box {
            width: 45%;
            display: inline-block;
            text-align: center;
            vertical-align: top;
            margin-bottom: 30px;
        }

        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="no-print" style="margin-bottom: 20px; text-align: right;">
        <button onclick="window.print()" style="padding: 10px 20px; font-size: 16px; cursor: pointer;">🖨️
            พิมพ์เอกสารนี้</button>
    </div>

    <div class="header">
        บันทึกข้อความขออนุมัติเบิกจ่ายเงินสดธนาคารขยะประจำรอบ<br>
        ชุดที่: {{ $batch->batch_no }}
    </div>

    <p>
        <strong>เรียน:</strong> นายกเทศมนตรี / นายก อบต.<br>
        <strong>วันที่ตัดรอบ:</strong> {{ \Carbon\Carbon::parse($batch->cutoff_date)->format('d/m/Y') }}
        &nbsp;&nbsp;&nbsp;&nbsp;
        <strong>กำหนดวันรับเงินสด:</strong> {{ \Carbon\Carbon::parse($batch->payout_date)->format('d/m/Y') }}
    </p>

    <p>
        ตามที่สมาชิกธนาคารขยะได้ยื่นเรื่องขอถอนเงินสดประจำสัปดาห์
        เจ้าหน้าที่ได้ทำการรวบรวมรายการและตรวจสอบสิทธิ์ตามระเบียบเรียบร้อยแล้ว ปรากฏรายละเอียดการขอเบิกจ่ายเงินสด
        ดังนี้:
    </p>

    <table class="table">
        <thead>
            <tr style="background-color: #f2f2f2;">
                <th style="width: 5%;">ลำดับ</th>
                <th>ชื่อ-นามสกุล สมาชิก</th>
                <th style="width: 25%;">ผู้มีสิทธิ์รับเงิน</th>
                <th style="width: 15%;">จำนวนเงิน (บาท)</th>
                <th style="width: 20%;">ลายมือชื่อรับเงิน</th>
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
                    <td class="text-end">{{ number_format($req->amount, 2) }}</td>
                    <td></td>
                </tr>
            @endforeach
            <tr style="font-weight: bold; background-color: #f9f9f9;">
                <td colspan="3" class="text-end">ยอดเงินรวมทั้งหมด</td>
                <td class="text-end">{{ number_format($batch->total_amount, 2) }}</td>
                <td></td>
            </tr>
        </tbody>
    </table>

    <p style="margin-top: 15px;">
        จึงเรียนมาเพื่อโปรดพิจารณาอนุมัติ เบิกจ่ายเงินสดจำนวน <strong>{{ number_format($batch->total_amount, 2) }}
            บาท</strong> จากบัญชีธนาคารขยะ เพื่อจัดเตรียมจ่ายให้แก่สมาชิกต่อไป
    </p>

    <!-- ช่องเซ็นชื่อผู้อนุมัติดึงแบบ Dynamic จาก ApprovalWorkflow -->
    <div class="sign-section">
        @if(isset($workflow) && $workflow->steps->count() > 0)
            @foreach($workflow->steps->sortBy('step_order') as $step)
                <div class="sign-box">
                    ลงชื่อ..........................................................<br>
                    (
                    @if($step->specificUser)
                        {{ $step->specificUser->firstname }} {{ $step->specificUser->lastname }}
                    @else
                        ..........................................................
                    @endif
                    )<br>
                    <strong>{{ $step->role_name }}</strong>
                </div>
            @endforeach
        @else
            <!-- กรณีไม่มีการตั้งค่า Workflow ให้แสดงโครงสร้างตั้งต้น -->
            <div class="sign-box">
                ลงชื่อ..........................................................<br>
                ( .......................................................... )<br>
                <strong>เจ้าหน้าที่ผู้จัดทำ</strong>
            </div>
            <div class="sign-box">
                ลงชื่อ..........................................................<br>
                ( .......................................................... )<br>
                <strong>ผู้อำนวยการกองสาธารณสุข / กองคลัง</strong>
            </div>
            <div class="sign-box">
                ลงชื่อ..........................................................<br>
                ( .......................................................... )<br>
                <strong>ปลัด อบต. / เทศบาล</strong>
            </div>
            <div class="sign-box">
                ลงชื่อ..........................................................<br>
                ( .......................................................... )<br>
                <strong>นายก อบต. / เทศบาล (ผู้อนุมัติ)</strong>
            </div>
        @endif
    </div>
</body>

</html>