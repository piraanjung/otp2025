@extends('layouts.print')

<?php $index = 0; ?>
<?php
$year = date('Y') + 543;
$year2 = date('y') + 43;
$a = 1;
$c2 = 1;
?>
@section('style')
    <link rel="stylesheet" href="{{ asset('adminlte/dist/css/adminlte.min.css') }}">
    <script src="{{ asset('adminlte/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/popper/umd/popper.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/bootstrap/js/bootstrap.min.js') }}"></script>
    <style>
        * {
            font: 11pt "Sarabun";
            font-weight: bold;
            color: black
        }

        .address {
            font-size: 11pt;
        }

        .number {
            padding-right: 5px !important
        }

        .t2-pr-3 {
            padding-right: 0.8rem !important
        }

        td.waterUsedHisHead {
            font-size: 10pt;
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

        td {
            padding: 4.7pt;
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

        .t2 {
            margin-left: 15px !important;
        }

        .unit_usedtext {
            font-size: 8pt;
        }

        .print {
            page-break-after: always;
        }

        @media print {
            * {
                font-size: 10.8pt;
            }

            .address {
                font-size: 9pt;
            }

        }
    </style>
@endsection
@section('content')
    <br>

    <div class="row" style="">
        <div class="col-6">
            @include('user_payment_per_month._rc_left_form')
        </div>
        <div class="col-6" style="margin-right: 0px !important">
            @include('user_payment_per_month._rc_right_form')
        </div>
        <p style=" page-break-after: avoid;"></p>

    </div>
    <form action="{{ route('user_payment_per_month.history') }}" name="xx" id="xx" method="post"
    style="position: absolute;
        left: 40px;
        top: 40px;
        z-index: -1; opacity:0">
        @csrf
        <input type="hidden" value="nav" name="nav">

        <input type="submit" >
    </form>
    <br class="print">
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            var os = navigator.platform;

            console.log('os', os)

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
            // setTimeout(function() {
            //     if("<?php echo $from == 'userPaymentPerMonth.store' ?>"){
            //         window.location.href = '/user_payment_per_month';
            //     }else{
            //         window.document.getElementById("xx").submit();
            //     }

            // }, 200);

        });
    </script>
@endsection
