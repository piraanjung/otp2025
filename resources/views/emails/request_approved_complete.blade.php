<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>แจ้งผลการอนุมัติใบเบิกพัสดุ</title>
    <style>
        body { font-family: 'Sarabun', sans-serif; color: #333; line-height: 1.6; }
        .container { width: 100%; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px; background-color: #f9f9f9; }
        .header { background-color: #28a745; color: #fff; padding: 15px; text-align: center; border-radius: 6px 6px 0 0; }
        .content { padding: 20px; background-color: #fff; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; font-size: 14px; }
        th { background-color: #f2f2f2; }
        .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #777; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>ใบเบิกพัสดุได้รับการอนุมัติแล้ว</h2>
        </div>
        <div class="content">
            <p>เรียน คุณ <strong>{{ $transactions->first()->user->firstname ?? 'ผู้ขอเบิก' }}</strong>,</p>
            <p>ใบเบิกพัสดุหมายเลข: <strong style="color: #28a745;">{{ $transactions->first()->ref_no }}</strong> ได้ผ่านกระบวนการตรวจสอบและอนุมัติครบทุกขั้นตอนเรียบร้อยแล้วครับ</p>
            
            <p><strong>วัตถุประสงค์ในการเบิก:</strong> {{ $transactions->first()->purpose }}</p>

            <h4>รายการพัสดุที่ขอเบิก:</h4>
            <table>
                <thead>
                    <tr>
                        <th>ลำดับ</th>
                        <th>รายการพัสดุ</th>
                        <th>จำนวนที่ขอ</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transactions as $index => $tx)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $tx->item->name ?? '-' }}</td>
                        <td>{{ $tx->quantity }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <p style="margin-top: 20px;">ท่านสามารถติดต่อขอรับพัสดุได้จากเจ้าหน้าที่คลังพัสดุ</p>
        </div>
        <div class="footer">
            <p>ระบบคลังพัสดุห้องปฏิบัติการ (Automatic Notification)</p>
        </div>
    </div>
</body>
</html>