<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ใบเสร็จรับเงิน</title>
  <style>
        @page {
            size: A5 landscape; /* Changed to A5 landscape */
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'THSarabunNew', sans-serif;
            font-size: 14px;
            margin: 0;
            padding: 0;
            height: ;: 210mm; /* A5 landscape width */
            width: ;: 148mm; /* A5 landscape height */
            box-sizing: border-box; /* Include padding and border in the element's total width and height */
        }
        @media print{
             body {
            font-family: 'THSarabunNew', sans-serif;
            font-size: 14px;
            margin: 0;
            padding: 0;
            height: ;: 210mm; /* A5 landscape width */
            width: ;: 148mm; /* A5 landscape height */
            box-sizing: border-box; /* Include padding and border in the element's total width and height */
        }
        }
        @font-face {
            font-family: 'THSarabunNew';
            font-style: normal;
            font-weight: normal;
            src: url("{{ public_path('fonts/THSarabunNew.ttf') }}") format('truetype');
        }
        @font-face {
            font-family: 'THSarabunNew';
            font-style: normal;
            font-weight: bold;
            src: url("{{ public_path('fonts/THSarabunNew Bold.ttf') }}") format('truetype');
        }
        .container {
            width: 100%;
            height: 100%;
            display: flex;
        }
        .receipt-column {
            width: 50%;
            padding: 10px; /* Adjusted padding to fit A5 */
            box-sizing: border-box;
            border-right: 1px dashed #ccc;
        }
        .receipt-column:last-child {
            border-right: none;
        }
        .header {
            text-align: center;
            margin-bottom: 10px; /* Adjusted margin */
        }
        .header h1 {
            font-size: 18px; /* Adjusted font size */
            margin: 0;
        }
        .header p {
            font-size: 12px; /* Adjusted font size */
            margin: 3px 0 0;
        }
        .info-table {
            width: 100%;
            margin-bottom: 10px; /* Adjusted margin */
        }
        .info-table td {
            padding: 3px 0; /* Adjusted padding */
            vertical-align: top;
        }
        .info-table .label {
            font-weight: bold;
            width: 70px; /* Adjusted width */
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
        }
        .items-table th, .items-table td {
            border: 1px solid #ddd;
            padding: 5px; /* Adjusted padding */
            text-align: left;
        }
        .items-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .total-section {
            margin-top: 10px; /* Adjusted margin */
            text-align: right;
            font-size: 14px;
        }
        .footer {
            margin-top: 15px; /* Adjusted margin */
            font-size: 10px; /* Adjusted font size */
            text-align: center;
        }
        .receipt-type {
            font-size: 10px; /* Adjusted font size */
            text-align: right;
            margin-bottom: 5px;
            color: #777;
        }
    </style>
</head>
<body>
    {{-- @dd($data) --}}
    <div class="container">
        <!-- Left Column: ต้นขั้ว (Stub) -->
        <div class="receipt-column">
            <div class="receipt-type">ต้นขั้ว</div>
            <div class="header">
                <h1>ใบเสร็จรับเงิน</h1>
                <p>โครงการรับซื้อขยะ</p>
                <p>เลขที่: {{ $data['receiptCode'] }}</p>
            </div>
            <table class="info-table">
                <tr>
                    <td class="label">ผู้ชำระ:</td>
                    <td>{{ $data['subscription']->wasteBin->user->firstname ?? 'N/A' }} {{ $data['subscription']->wasteBin->user->lastname ?? '' }}</td>
                </tr>
                <tr>
                    <td class="label">รหัสถัง:</td>
                    <td>{{ $data['subscription']->wasteBin->bin_code ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="label">วันที่ชำระ:</td>
                    <td>{{ \Carbon\Carbon::parse($data['paymentDate'])->locale('th')->isoFormat('Do MMMM YYYY') }}</td>
                </tr>
            </table>

            <table class="items-table">
                <thead>
                    <tr>
                        <th>รายการ</th>
                        <th>จำนวนเงิน (฿)</th>
                    </tr>
                </thead>
                <tbody>
                    @php $total_paid = 0; @endphp
                    @foreach ($data['payments'] as $payment)
                        <tr>
                            <td>ค่าธรรมเนียมรายเดือน {{ \Carbon\Carbon::createFromDate($payment->pay_yr, $payment->pay_mon)->locale('th')->monthName }} {{ $payment->pay_yr }}</td>
                            <td>{{ number_format($payment->amount_paid, 2) }}</td>
                        </tr>
                        @php $total_paid += $payment->amount_paid; @endphp
                    @endforeach
                </tbody>
            </table>
            
            <div class="total-section">
                <strong>ยอดรวม:</strong> {{ number_format($total_paid, 2) }} ฿
            </div>

            <div class="footer">
                <p>ลงชื่อเจ้าหน้าที่: _________________________</p>
                <p>({{ $data['staff']->firstname ?? 'N/A' }} {{ $data['staff']->lastname ?? '' }})</p>
            </div>
        </div>

        <!-- Right Column: ส่วน Copy -->
        <div class="receipt-column">
            <div class="receipt-type">ส่วน Copy</div>
            <div class="header">
                <h1>ใบเสร็จรับเงิน</h1>
                <p>โครงการรับซื้อขยะ</p>
                <p>เลขที่: {{ $data['receiptCode'] }}</p>
            </div>
            <table class="info-table">
                <tr>
                    <td class="label">ผู้ชำระ:</td>
                    <td>{{ $data['subscription']->wasteBin->user->firstname ?? 'N/A' }} {{ $data['subscription']->wasteBin->user->lastname ?? '' }}</td>
                </tr>
                <tr>
                    <td class="label">รหัสถัง:</td>
                    <td>{{ $data['subscription']->wasteBin->bin_code ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="label">วันที่ชำระ:</td>
                    <td>{{ \Carbon\Carbon::parse($data['paymentDate'])->locale('th')->isoFormat('Do MMMM YYYY') }}</td>
                </tr>
            </table>

            <table class="items-table">
                <thead>
                    <tr>
                        <th>รายการ</th>
                        <th>จำนวนเงิน (฿)</th>
                    </tr>
                </thead>
                <tbody>
                    @php $total_paid = 0; @endphp
                    @foreach ($data['payments'] as $payment)
                        <tr>
                            <td>ค่าธรรมเนียมรายเดือน {{ \Carbon\Carbon::createFromDate($payment->pay_yr, $payment->pay_mon)->locale('th')->monthName }} {{ $payment->pay_yr }}</td>
                            <td>{{ number_format($payment->amount_paid, 2) }}</td>
                        </tr>
                        @php $total_paid += $payment->amount_paid; @endphp
                    @endforeach
                </tbody>
            </table>
            
            <div class="total-section">
                <strong>ยอดรวม:</strong> {{ number_format($total_paid, 2) }} ฿
            </div>

            <div class="footer">
                <p>ลงชื่อเจ้าหน้าที่: _________________________</p>
                <p>({{ $data['staff']->firstname ?? 'N/A' }} {{ $data['staff']->lastname ?? '' }})</p>
            </div>
        </div>
    </div>
</body>
</html>
