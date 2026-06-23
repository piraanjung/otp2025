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
    <link rel="stylesheet" href="{{ asset('adminlte/dist/css/adminlte.min.css') }}">
    <script src="{{ asset('/js/jquery-1.11.3.min.js') }}"></script>
    <script src="{{ asset('/js/ajax/libs/popper.js/1.14.3/umd/popper.min.js') }}"></script>
    <script src="{{ asset('/js/bootstrap/4.5.2/js/bootstrap.min.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/fontawesome-free/css/all.css') }}">
    <style>
        * {
            font: 10.3pt "Sarabun";
            font-weight: bold;
            /* color:red !important */
        }

        td {
            padding: 4.7pt !important;
        }

        .address {
            font-size: 10pt;
        }

        .address2 {
            font-size: 10pt;
        }

        .number {
            padding-top: 2px !important;
            padding-bottom: 2px !important
        }

        .t2-pr-3 {
            padding-right: 0.8rem !important;
        }

        td.waterUsedHisHead {
            /* font-size: 10pt; */
            padding: 2pt;
        }

        .head {
            font-size: 16pt;
            font-weight: bold
        }

        .head2 {
            font-size: 14pt;
        }

        body {
            -webkit-print-color-adjust: exact !important;
        }

        /* td{
                                padding: 4.7pt;
                            } */
        .head {
            height: 3pc;
        }

        .noneborder_top_left {
            border-top: 1px solid white;
            border-right: 1px solid white;
        }

        .barcode {
            height: 50pt;
            width: 160pt
        }

        .border-bottom-none {
            border-bottom: 1px solid white;
        }

        .border-right-none {
            border-right: 1px solid white;
        }

        .border-top-none {
            border-top: 1px solid white;
            padding-left: 10px
        }

        .border-left-none {
            border-left: 1px solid white;
        }

        .border-bottom-fill {
            border-bottom: 1px solid black;
        }

        .border-right-fill {
            border-right: 1px solid black;
        }

        .border-top-fill {
            border-top: 1px solid black;
        }

        .border-left-fill {
            border-left: 1px solid black;
        }

        .unit_usedtext {
            font-size: 8pt;
        }

        .inv_number_text {
            font-size: 1.3rem
        }

        .print {
            page-break-after: always;
        }

        .t2 {
            margin-left: 15px !important;
        }

        .waterUsedHisHead2,
        .waterUsedHisHead2 div {
            font-size: .80rem;
            font-weight: bold;
            padding-top: 1px !important;
            padding-bottom: 1px !important
        }

        .waterUsedHisHead2,
        .waterUsedHisHead2_r {
            /* border-bottom: 1px solid #000; */
        }

        .waterUsedHisHead2,
        .waterUsedHisHead2 div {
            font-weight: bold;
        }

        .baht {
            font-size: 0.8rem;
        }

        .bookbank {
            border-top: 0.1rem solid black;
            border-bottom: 0.1rem solid black;
        }

        .bookbank div {
            text-align: center;
            font-size: 0.9rem;
        }

        td {
            border: 1px solid white
        }

        .qrcode_description {
            padding-left: 0.6rem;
            font-size: 1rem;

        }

        .header-bg {
            /* background-color: #1955b0; */
            /* color: white */
            color: #000;
        }

        .ref td {
            padding: 0px !important;
            border-top: 1px solid white
        }

        .summary_text {
            font-size: 0.96rem;
            padding-top: 1.5px !important;
            padding-bottom: 1.5px !important
        }

        .tax_number {
            font-size: 1.1rem;
            font-weight: bold
        }

        #info td {
            width: 0.9rem !important;
            padding-top: 4px !important;
            padding-bottom: 4px !important
        }

        .t {
            margin-left: 2px !important
        }

        @media print {

            .waterUsedHisHead2,
            .waterUsedHisHead2 div {
                font-size: .80rem;
                font-weight: bold;
                padding-top: 1px !important;
                padding-bottom: 1px !important
            }

            .waterUsedHisHead2_r div {
                font-size: .8rem !important;
                font-weight: bold;

            }

            .ref td {
                padding: 0px !important;
                border-top: 1px solid white
            }

            #info td {
                width: 0.9rem !important;
                padding-top: 4px !important;
                padding-bottom: 4px !important
            }

            .t {
                margin-left: 2px !important
            }

            .summary_text {
                font-size: 0.96rem;
                padding-top: 4px !important;
                padding-bottom: 4px !important
            }

            .number {
                padding-top: 4px !important;
                padding-bottom: 4px !important
            }
        }
    </style>
@endsection
@section('content')
    {{-- <input type="hidden" id="type" value="{{ $type }}"> --}}

    <br>

    <div class="row">
        <div class="col-6 a ">
            <?php
            $iso88591 = "|099400035262000\r\n000000000000001700\r\n000000000000115925\r\n5992";
            $utf8 = iconv('ISO-8859-1', 'UTF-8', $iso88591);

            ?>
            <textarea cols="30" rows="5" id="qrcode_text"><?= $iso88591 ?></textarea>
            <br>
            <div id="qrcode"></div>

            <br>
            {{-- {{ QrCode::size(115)->generate($utf8) }} --}}
            <div class="row">
                <div class='mt-3 col-md-6 text-left' style="font-size: 1rem">
                    <div class="qrcode_description">
                        <i class="far fa-arrow-alt-circle-down"></i> 1.สแกนจ่าย
                    </div>

                    <div class="qrcode_description">
                        <i class="far fa-arrow-alt-circle-down"></i> 2.แชร์ สลิปไป Line
                    </div>
                    <div class="qrcode_description">
                        <i class="far fa-arrow-alt-circle-down"></i> 3.จ่ายจบ
                    </div>

                    <div class="bookbank">
                        <div>ธนาคารกรุงไทย</div>
                        <div> 325-0-320-28-5</div>
                    </div>

                </div>
                <div class="col-md-3 text-center">
                    {{ QrCode::size(115)->generate($utf8) }}
                </div>
            </div>
            {{-- @include('payment._rc_left_form') --}}
        </div>
        <div class="col-6 a">
            {{-- @include('payment._rc_right_form') --}}
        </div>
        <p style=" page-break-after: avoid;"></p>

    </div>
    <br class="print">

    <form action="{{ route('payment.search') }}" method = "post" style="opacity: 0">
        @csrf
        <input type ="submit">
        <input type = "hidden" value = "nav" name = "nav">

    </form>
@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/jquery.qrcode@1.0.3/jquery.qrcode.min.js"></script>
    <script>
        $(document).ready(function() {
            console.log($('#qrcode_text').val())
            $('#qrcode').qrcode($('#qrcode_text').val());


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
            //     setTimeout(function() {
            //         window.location.href = '/payment';
            //     }, 200);
            // } else {
            //     //type == history_recipt
            //     setTimeout(function() {

            //         // document.querySelector("form").addEventListener("submit", function(evt) {
            //         //     evt.preventDefault();
            //         // });

            //         // Just call the .click method of the button
            //         $("input[type='submit']").click();

            //     }, 200);



            // }
        });
    </script>
@endsection
