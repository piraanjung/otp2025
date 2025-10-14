<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ใบเสร็จรับเงิน</title>
    <style>
        @page {
            /* size: A5 portrait; */
            /* Changed to A5 landscape */
            /* margin: 0;
            padding: 0; */
        }

        body {
            font-family: 'THSarabunNew', sans-serif;
            font-size: 12px !important; 
            margin: 0;
            padding: 0;
            height: ;
            : 210mm;
            /* A5 landscape width */
            width: ;
            : 148mm;
            /* A5 landscape height */
            box-sizing: border-box;
            /* Include padding and border in the element's total width and height */
            border:1px solid red
        }

        @media print {
            body {
                font-family: 'THSarabunNew', sans-serif;
                font-size: 14px;
                margin: 0;
                padding: 0;
                width: 210mm;
                height: 148mm;
                box-sizing: border-box;
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

        .container2 {
            width: 100% !important;
            height: 100%;
            padding-right: 0 !important;
            padding-left: 0 !important;
            margin-right: 0px !important;
            margin-left: 0 !important;
            display: flex;
        }

        .receipt-column {
            width: 50%;
            padding: 10px;
            /* Adjusted padding to fit A5 */
            box-sizing: border-box;
            border-right: 1px dashed #ccc;
        }

        .receipt-column:last-child {
            border-right: none;
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
            /* Adjusted margin */
        }

        .header h1 {
            /* font-size: 11px; */
            /* Adjusted font size */
            margin: 0;
        }

        .header p {
            font-size: 11px;
            /* Adjusted font size */
            margin: 3px 0 0;
        }

        .info-table {
            width: 100%;
            margin-bottom: 10px;
            /* Adjusted margin */
        }

        .info-table td {
            /* padding: 3px 0; */
            /* Adjusted padding */
            vertical-align: top;
        }

        .info-table .label {
            font-weight: bold;
            /* width: 70px; */
            /* Adjusted width */
        }

        .items-table {
            /* width: 100%; */
            border-collapse: collapse;
        }

        .items-table th,
        .items-table td {
            border: 1px solid #ddd;
            /* padding: 5px; */
            /* Adjusted padding */
            text-align: left;
        }

        .items-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .total-section {
            margin-top: 10px;
            /* Adjusted margin */
            text-align: right;
            font-size: 14px;
        }

        .footer {
            margin-top: 15px;
            /* Adjusted margin */
            font-size: 10px;
            /* Adjusted font size */
            text-align: center;
        }

        .receipt-type {
            font-size: 10px;
            /* Adjusted font size */
            text-align: right;
            margin-bottom: 5px;
            color: #777;
        }
        td, th{
            border: 1px solid black !important
        }
        .te{
            /* width: 70px !important; */
            /* height: 40px; */
            border: 1px solid black
        }
        .table>:not(caption)>*>* {
    padding: .1rem .1rem !important;
    background-color: var(--bs-table-bg);
    border-bottom-width: 1px;
    box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg);
}
    </style>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>

<body>
    {{-- @dd($data) --}}

    <div class="container2">
        <!-- Left Column: ต้นขั้ว (Stub) -->
        <div class="receipt-column">
            <div class="header d-flex">
                <div class="p-0">
                    <div style="font-size: 0.9rem; font-weight: bold;">ใบเสร็จรับเงิน </div>
                    <div style="font-size: 0.6rem"> (ต้นขั้ว)</div>
                    <div style="font-size: 0.6rem">เลขที่: {{ $data['receiptCode'] }}</div>

                </div>
                <div class="ms-auto pl-2">

                    <div style="font-size: 0.9rem; font-weight: bold;">องค์การบริหารส่วนตำบลห้องแซง</div>
                    <div style="font-size: 0.6rem">22 หมู่ 12 ต.ห้องแซง</div>
                    <div style="font-size: 0.6rem">อ.เลิงนกทา จ.ยโสธร 35120</div>
                </div>   
         
            </div>
            <div class="d-flex flex-row">
                <table class="info-table">
                    <tr>
                        <td class="label">ผู้ชำระ:</td>
                        <td>{{ $data['subscription']->wasteBin->user->firstname ?? 'N/A' }}
                            {{ $data['subscription']->wasteBin->user->lastname ?? '' }}</td>
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
                <img src="{{asset('logo/hs_logo.jpg')}}" style="width: 65px; height:65px;margin-left:0.2rem; border:1px solid black"/>
            </div>
            

            @php
            $arr =[
                ['ค่าเก็บและขนขยะมูลฝอย', 240], ['ภาษีมูลค่าเพิ่ม  7%', '0.00']
            ]
            @endphp
            <div style="display: flex; flex-direction: column">
                <div>
                    <table class="table">
                        <thead>
                            <tr>
                                <td style="width: 70%">รายการ</td>
                                <td style="width: 30%">จำนวนเงิน(บาท)</td>
                            </tr>
                        </thead>
                            <tbody>
                            <tr>
                                <td style="height: 50px; vertical-align: top;">
                                    @php
                                    foreach($arr as $ar){
                                       echo "1. ". $ar[0]."<br>";
                                    }
                                    @endphp
                                
                                </td>
                                <td style="vertical-align: top; text-align: right;">
                                   @php
                                    foreach($arr as $ar){
                                       echo $ar[1]."<br>";
                                    }
                                    @endphp
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align:right">
                                   รวมทั้งสิ้น
                                
                                </td>
                                <td style="text-align: right;">
                                   240.00
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" style="text-align: right; font-size: 10px;">
                                    ({{ App\Http\Controllers\Api\FunctionsController::convertAmountToLetter(number_format(240, 2))}})
                                </td>
                            </tr>
                            
                        </tbody>
                    </table>
                </div>
                ประวัติการชำระค่าธรรมเนียมประจำปีงบประมาณ {{2568}}

                <div class="row" style="padding: 5px 11px">
                     @for($i=0; $i<3; $i++)
                        <div class="col-4 te">
                            <div class="row">
                                <div class="col-4 text-center text-bolder" style="border-right: 1px solid black">
                                    เดือน
                                </div>
                                <div class="col-8 text-center text-bolder">
                                    จำนวนเงิน(บาท)
                                </div>
                            </div>
                            
                        </div>
                    @endfor
                    @php
                    $arr= [10,11,12,1,2,3,4,5,6,7,8,9];
                    $i=0;
                    // dd($data['payments']);
                    @endphp
                    @foreach ($arr as $ar)
                         <div class="col-4 te">
                            <div class="row">
                                 <div class="col-4 text-center" style="border-right: 1px solid black">
                                        {{(new \App\Http\Controllers\Api\FunctionsController())->shortThaiMonth($ar)}}
                                </div>
                               

                               
                                <div class="col-8 text-center">
                                     @if (in_array($ar,collect($paidMonthArr)->toArray()))
                                        {{ number_format($data['payments'][$i++]['amount_paid'], 2) }}  
                                      @else
                                        -
                                    @endif
                                </div>
                               
                               
                            </div>
                            
                        </div>
                    @endforeach
                    {{-- @foreach ($data['payments'] as $payment)
                        <div class="col-4 te">
                            <div class="row">
                                @if (in_array($arr[$i],collect($paidMonthArr)->toArray()))

                                <div class="col-4" style="border-right: 1px solid red">
                                        {{(new \App\Http\Controllers\Api\FunctionsController())->shortThaiMonth($payment->pay_mon)}}
                                </div>
                                <div class="col-8">
                                        {{ number_format($payment->amount_paid, 2) }}  
                                </div>
                                  @else
                                    <div class="col-4" style="border-right: 1px solid red">
                                        {{(new \App\Http\Controllers\Api\FunctionsController())->shortThaiMonth($arr[$i])}}
                                </div>
                                <div class="col-8">
                                        xx 
                                </div>
                                @endif
                            </div>
                            
                        </div>
                        @php
                        $i++;
                        @endphp
                    @endforeach --}}
                </div>
              
            </div>


            <div class="total-section">
                <strong>ยอดรวม:</strong> 
                {{-- {{ number_format($total_paid, 2) }} --}}
                 บาท
            </div>

            <div class="footer">
                <p>ลงชื่อเจ้าหน้าที่: _________________________</p>
                <p>({{ $data['staff']->firstname ?? 'N/A' }} {{ $data['staff']->lastname ?? '' }})</p>
            </div>
        </div>

        <!-- Right Column: ส่วน Copy -->
         <div class="receipt-column">
            <div class="header d-flex">
                <div class="p-0">
                    <div style="font-size: 0.9rem; font-weight: bold;">ใบเสร็จรับเงิน </div>
                    <div style="font-size: 0.6rem"> (สำเนา)</div>
                    <div style="font-size: 0.6rem">เลขที่: {{ $data['receiptCode'] }}</div>

                </div>
                <div class="ms-auto pl-2">

                    <div style="font-size: 0.9rem; font-weight: bold;">องค์การบริหารส่วนตำบลห้องแซง</div>
                    <div style="font-size: 0.6rem">22 หมู่ 12 ต.ห้องแซง</div>
                    <div style="font-size: 0.6rem">อ.เลิงนกทา จ.ยโสธร 35120</div>
                </div>   
         
            </div>
            <div class="d-flex flex-row">
                <table class="info-table">
                    <tr>
                        <td class="label">ผู้ชำระ:</td>
                        <td>{{ $data['subscription']->wasteBin->user->firstname ?? 'N/A' }}
                            {{ $data['subscription']->wasteBin->user->lastname ?? '' }}</td>
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
                <img src="{{asset('logo/hs_logo.jpg')}}" style="width: 65px; height:65px;margin-left:0.2rem; border:1px solid black"/>
            </div>
            

            @php
            $arr =[
                ['ค่าเก็บและขนขยะมูลฝอย', 240], ['ภาษีมูลค่าเพิ่ม  7%', '0.00']
            ]
            @endphp
            <div style="display: flex; flex-direction: column">
                <div>
                    <table class="table">
                        <thead>
                            <tr>
                                <td style="width: 70%">รายการ</td>
                                <td style="width: 30%">จำนวนเงิน(บาท)</td>
                            </tr>
                        </thead>
                            <tbody>
                            <tr>
                                <td style="height: 50px; vertical-align: top;">
                                    @php
                                    foreach($arr as $ar){
                                       echo "1. ". $ar[0]."<br>";
                                    }
                                    @endphp
                                
                                </td>
                                <td style="vertical-align: top; text-align: right;">
                                   @php
                                    foreach($arr as $ar){
                                       echo $ar[1]."<br>";
                                    }
                                    @endphp
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align:right">
                                   รวมทั้งสิ้น
                                
                                </td>
                                <td style="text-align: right;">
                                   240.00
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" style="text-align: right; font-size: 10px;">
                                    ({{ App\Http\Controllers\Api\FunctionsController::convertAmountToLetter(number_format(240, 2))}})
                                </td>
                            </tr>
                            
                        </tbody>
                    </table>
                </div>
                ประวัติการชำระค่าธรรมเนียมประจำปีงบประมาณ {{2568}}

                <div class="row" style="padding: 5px 11px">
                     @for($i=0; $i<3; $i++)
                        <div class="col-4 te">
                            <div class="row">
                                <div class="col-4 text-center text-bolder" style="border-right: 1px solid black">
                                    เดือน
                                </div>
                                <div class="col-8 text-center text-bolder">
                                    จำนวนเงิน(บาท)
                                </div>
                            </div>
                            
                        </div>
                    @endfor
                    @php
                    $arr= [10,11,12,1,2,3,4,5,6,7,8,9];
                    $i=0;
                    // dd($data['payments']);
                    @endphp
                    @foreach ($arr as $ar)
                         <div class="col-4 te">
                            <div class="row">
                                 <div class="col-4 text-center" style="border-right: 1px solid black">
                                        {{(new \App\Http\Controllers\Api\FunctionsController())->shortThaiMonth($ar)}}
                                </div>
                               

                               
                                <div class="col-8 text-center">
                                     @if (in_array($ar,collect($paidMonthArr)->toArray()))
                                        {{ number_format($data['payments'][$i++]['amount_paid'], 2) }}  
                                      @else
                                        -
                                    @endif
                                </div>
                               
                               
                            </div>
                            
                        </div>
                    @endforeach
                    {{-- @foreach ($data['payments'] as $payment)
                        <div class="col-4 te">
                            <div class="row">
                                @if (in_array($arr[$i],collect($paidMonthArr)->toArray()))

                                <div class="col-4" style="border-right: 1px solid red">
                                        {{(new \App\Http\Controllers\Api\FunctionsController())->shortThaiMonth($payment->pay_mon)}}
                                </div>
                                <div class="col-8">
                                        {{ number_format($payment->amount_paid, 2) }}  
                                </div>
                                  @else
                                    <div class="col-4" style="border-right: 1px solid red">
                                        {{(new \App\Http\Controllers\Api\FunctionsController())->shortThaiMonth($arr[$i])}}
                                </div>
                                <div class="col-8">
                                        xx 
                                </div>
                                @endif
                            </div>
                            
                        </div>
                        @php
                        $i++;
                        @endphp
                    @endforeach --}}
                </div>
              
            </div>


            <div class="total-section">
                <strong>ยอดรวม:</strong> 
                {{-- {{ number_format($total_paid, 2) }} --}}
                 บาท
            </div>

            <div class="footer">
                <p>ลงชื่อเจ้าหน้าที่: _________________________</p>
                <p>({{ $data['staff']->firstname ?? 'N/A' }} {{ $data['staff']->lastname ?? '' }})</p>
            </div>
        </div>
    </div>


     <script>
        $(document).ready(function () {
            // console.log($('#qrcode_text').val())
            // $('#qrcode').qrcode($('#qrcode_text').val());


            var os = navigator.platform;

            console.log('os', os)

            // $('.btnprint').click(function(){
            $('.btnprint').hide();
            var css = '@page {  }',
                head = document.head || document.getElementsByTagName('head')[0],
                style = document.createElement('style');
            style.type = 'text/css';
            style.media = 'print';
            if (style.styleSheet) {
                style.styleSheet.cssText = css;
            } else {
                style.appendChild(document.createTextNode(css));
            }
            head.appendChild(style);

            style.type = 'text/css';
            style.media = 'print';

            if (style.styleSheet) {
                style.styleSheet.cssText = css;
            } else {
                style.appendChild(document.createTextNode(css));
            }

            head.appendChild(style);

            // window.print();
            // if ($('#type').val() == 'paid_receipt') {
            //     setTimeout(function () {
            //         window.location.href = '/payment';
            //     }, 200);
            // } else {
            //     //type == history_recipt
            //     setTimeout(function () {
            //         $("input[type='submit']").click();

            //     }, 200);

            // }
        });
    </script>
</body>

</html>