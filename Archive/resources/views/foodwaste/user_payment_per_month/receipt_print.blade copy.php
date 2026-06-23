@extends('layouts.print')

<head>

</head>
<?php $index = 0; ?>

@section('style')
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
    <div class="row" style="">

        <div class="col-6" style="border: 1px solid red">
            <div class="row">
                <div class="col-2 text-end">
                    <img src="{{ asset('logo/hz_logo.png') }}" width="150">
                </div>
                <div class="col-7 text-start mt-3">
                        <h3>เทศบาลตำบลห้องแซง  </h3>
                        222 หมู่ 17 ตำบลห้องแซง อำเภอเลิงนกทา จังหวัดยโสธร 35120<br>โทรศัพท์ 045777116 โทรสาร 045777116 ต่อ 16
                </div>
                <div class="col-3 text-center  bg-secondary">
                    <h1 class="mt-4">ค่าเก็บขยะ</h1>
                </div>
            </div>
        </div>
        <div class="col-6" style="margin-right: 0px !important">

        </div>
        <p style=" page-break-after: avoid;"></p>

    </div>
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
            // if ($('#type').val() == 'paid_receipt') {
            //     setTimeout(function() {
            //         window.location.href = '/payment/';
            //     }, 200);
            // } else {
            //     //type == history_recipt
            //     setTimeout(function() {
            //         window.location.href = '../search';
            //     }, 200);

            // }
        });
    </script>
@endsection
