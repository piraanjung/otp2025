@extends('layouts.print')



<?php
use App\Http\Controllers\Api\FunctionsController;

?>


<?php $index = 0; ?>
<?php
$year = date('Y') + 543;
$year2 = date('y') + 43;
$a = 1;
$c2 = 1;
// $invoiceNumber = FunctionsController::invoice_last_record()->id + 1;
?>
@section('style')
    <link rel="stylesheet" href="{{ asset('/templatemo/css/bootstrap.min.css') }}">
    <script src="{{ asset('/js/jquery-1.11.3.min.js') }}"></script>
    <script src="{{ asset('/js/ajax/libs/popper.js/1.14.3/umd/popper.min.js') }}"></script>
    <script src="{{ asset('/js/bootstrap/4.5.2/js/bootstrap.min.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/fontawesome-free/css/all.css') }}">
    <style>
        * {
            font: 10pt "Sarabun";
            /* font-weight: bold; */
            /* color:aquamarine !important */
        }
        /* .org_logo{
            width: 80px;
            justify-content: flex-end
        }
        .row, .d-flex{
            margin: 0px 0px;
            border: 1px solid red
        } */

        /* #table_title div ,.d-flex div{
            font-size: 0.9rem;
            font-weight: bold;
            border-right: 1px solid red;
            width: 200px !important;
            text-align: center
        } */
        /* .d-flex div{
             font-size: 1rem !important;
        }
        #table_title #vat{
             width: 100px !important;
        } */

        /* #table_title td div{
             font-size: 0.8rem;
        }
        .head, .tax_number, #org_address{
            font-weight: bold;
            font-size: 1.5rem
        }
        .tax_number{
            font-size: 1.2rem !important
        }
        .org_address{
            font-size: 1.05rem !important

        } */




        
    </style>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        /* CSS ทั่วไปสำหรับหน้าจอ */
        body {
            font-family: 'Tahoma', 'Sarabun', sans-serif;
            font-size: 8pt;
            /* ปรับลดขนาด Font เพื่อให้ข้อมูลทั้งหมดพอดีในพื้นที่แคบลง */
            /* background-color: #f8f9fa; */
        }

        .a5-container {
            /* กำหนดขนาด A5 แนวนอน (210mm x 148mm) */
            width: 210mm;
            height: 148mm;
            margin: 0 0;
            border: 1px solid #d60707;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            background-color: white;
            padding: 0mm;
            /* ลดขอบกระดาษด้านใน */
            display: flex;
        }

        /* ภาชนะของเอกสารชุดเดียว (ต้นฉบับ/สำเนา) */
        .document-wrapper {
            padding: 0 0px;
            /* เพิ่ม padding เล็กน้อยระหว่างต้นฉบับ/สำเนา */
            height: 100%;
            display: flex;
            flex-direction: column;
            gap: 0.2rem;
            /* ลดระยะห่างระหว่างส่วนหลัก */
        }

        .header-box {
            border: 1px solid #000;
            padding: 0rem 0rem;
            line-height: 1.1;
            font-size: 8pt;
        }

        .detail-table thead th,
        .detail-table thead th div,
        .detail-table tbody td {
            font-size: 7.4pt;
            text-align: center;
            padding: 0.1rem 0.05rem;
            /* ลด Padding ในตารางให้มากที่สุด */
            border: 1px solid #000;
            
        }
        

        .detail-table thead th div {
            border: none;
        }

        .detail-table thead th,
        .detail-table thead th div {
            font-weight: bold;
            background: rgb(195, 195, 234)
        }

        .unit {
            font-weight: bold;
            font-size: 6pt
        }

        .detail-table tbody td {
            font-size: 8.6pt;
            padding-top: 2px;
            padding-bottom: 2px;
        }

        .summary-box {
            border: 1px solid #000;
        }

        #username .title,
        #user_address .title,
        #meter .title {
            font-weight: bold;
            font-size: 9pt;
            background: rgb(195, 195, 234)
        }

        #username .detail,
        #meter .detail,
        #user_address .detail,
        #user_address .detail div {
            font-size: 9pt
        }

        #org_name {
            font-size: 12pt;
            font-weight: bold
        }

        .org_address{
            font-size: 9pt
        }

        p {
            margin-bottom: 0.2rem
        }

        .etc {
            font-size: 6.5pt;
            font-weight: bold;
            text-align: right;
            color: red
        }

        /* CSS สำหรับการพิมพ์เท่านั้น */
        @media print {
            @page {
                size: A5 landscape;
                margin: 0;
            }

            body {
                font-size: 8.5pt;
            }
            .a5-container {
                width: 210mm;
                height: 148mm;
                border: none;
                box-shadow: none;
                margin: 0;
                padding: 0px;
            }
        }
    </style>
@endsection
@section('content')
    <input type="hidden" id="type" value="{{ $type }}">


    <div class="row row_info" style="opacity: 1;">
        {{-- <div class="col-6" style="border: 1px solid #000000; border-bottom: none;">
            @include('payment._rc_left_form')
        </div>
        <div class="col-6" style="border: 1px solid #000; border-bottom: none;">
            @include('payment._rc_left_form')
        </div> --}}
        <div class="col-6 ...">
    @include('payment._rc_left_form', ['is_copy' => false])
</div>
<div class="col-6 ...">
    @include('payment._rc_left_form', ['is_copy' => true])
</div>
        <p style=" page-break-after: avoid;"></p>

    </div>
    {{-- <br class="print"> --}}

    <form action="{{ route('payment.search') }}" method="post" style="opacity: 0">
        @csrf
        <input type="submit">
        <input type="hidden" value="nav" name="nav">

    </form>
@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/jquery.qrcode@1.0.3/jquery.qrcode.min.js"></script>
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
@endsection