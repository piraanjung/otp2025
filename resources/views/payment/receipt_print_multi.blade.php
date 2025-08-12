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
    {{-- <script src="{{ asset('/js/jquery-1.11.3.min.js') }}"></script> --}}
    {{-- <script src="{{ asset('/js/ajax/libs/popper.js/1.14.3/umd/popper.min.js') }}"></script> --}}
    {{-- <script src="{{ asset('/js/bootstrap/4.5.2/js/bootstrap.min.js') }}"></script> --}}
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/fontawesome-free/css/all.css') }}">
    <style>
        * {
            font: 10.3pt "Sarabun";
            font-weight: bold;
            /* color:blue !important */
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
        td.waterUsedHisHead div {
            font-size: 1pt;
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
            /* margin-left: 72px !important; */
            margin-left: 30px !important;
        }
        .t2_for_name_address {
            margin-left: 62px !important;
            /* margin-left: 8px !important; */
        }
        .t2_r {
            /* margin-left: 41px !important; */
            margin-left: 10px !important;
        }
        .waterUsedHisHead2,
        .waterUsedHisHead2 div {
            font-size: 16px;
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
            /* font-weight: bold; */
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
            /* color: black */
            color: #000;
        }

        #ref td {
            padding: 0px !important;
            border-top: 1px solid white
        }

        .ref td {
            /* padding: 0px !important; */
            border-right: 1px solid white
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

            #ref td {
                padding: 0px !important;
                border-top: 1px solid white
            }

            #info td {
                width: 0.9rem !important;
                padding-top: 4px !important;
                padding-bottom: 4px !important
            }
            #info_over6 td{
                padding-top: 4px !important;
                padding-bottom: 0px !important;
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
            .row_info{
                opacity: 1 !important;
            }
        }
        .imgtest{
            position: absolute;
            z-index: 100;
            float: right;
            width: 40%;
            height:40px !important;
            margin-top: -6%;
            margin-left: 10%
        }
        .row_sign{
            margin-top: -0.5% !important
        }
    
    </style>
@endsection
@section('content')
    <input type="hidden" id="type" value="{{ $type }}">

    <br>
    <?php $count =   1;?>
    @foreach ($arr as $invoicesPaidForPrint)
    <div class="row row_info" style="opacity: 1">

        <div class="col-6">
            @include('payment._rc_left_form')
        </div>
        <div class="col-6">
            @include('payment._rc_right_form')
        </div>
        @if (collect($arr)->count() > $count++)
        <p style='overflow:hidden;page-break-after:always;'></p>
        @endif
        

    </div>

    @endforeach

    {{-- <br class="print"> --}}

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

             window.print();
             if ($('#type').val() == 'paid_receipt') {
                setTimeout(function() {
                     window.location.href = '/payment';
                 }, 200);
             } else {
                 //type == history_recipt
                 setTimeout(function() {

                    document.querySelector("form").addEventListener("submit", function(evt) {
                         evt.preventDefault();
                     });

                     // Just call the .click method of the button
                     $("input[type='submit']").click();

                 }, 200);

             }
        });
    </script>
@endsection
