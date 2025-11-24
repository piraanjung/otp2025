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
            font: 9pt "Sarabun";
            /* font-weight: bold; */
            /* color:aquamarine !important */
        }
        .org_logo{
            width: 80px;
            justify-content: flex-end
        }
        .row, .d-flex{
            margin: 0px 0px;
            border: 1px solid red
        }

        #table_title div ,.d-flex div{
            font-size: 0.9rem;
            font-weight: bold;
            border-right: 1px solid red;
            width: 200px !important;
            text-align: center
        }
        .d-flex div{
             font-size: 1rem !important;
        }
        #table_title #vat{
             width: 100px !important;
        }

        #table_title td div{
             font-size: 0.8rem;
        }




        
    </style>
@endsection
@section('content')
    <input type="hidden" id="type" value="{{ $type }}">

    <br>

    <div class="row row_info" style="opacity: 1; height: 148mm; border: red 1px dotted;">
        <div class="col-6" style="border: 1px solid #000">
            @include('payment._rc_left_form')
        </div>
        <div class="col-6" style="border: 1px solid #000">
            @include('payment._rc_right_form')
        </div>
        {{-- <p style=" page-break-after: avoid;"></p> --}}

    </div>
    <br class="print">

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