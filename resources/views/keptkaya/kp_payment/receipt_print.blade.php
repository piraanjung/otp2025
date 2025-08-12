@extends('layouts.print')

<head>
    <link rel="stylesheet" href="{{ asset('adminlte/dist/css/adminlte.min.css') }}">
    <script src="{{ asset('/js/jquery-1.11.3.min.js') }}"></script>
    <script src="{{ asset('/js/ajax/libs/popper.js/1.14.3/umd/popper.min.js') }}"></script>
    <script src="{{ asset('public/js/bootstrap/4.5.2/js/bootstrap.min.js') }}"></script>
</head>
<?php $index = 0; ?>
<?php
$year = date('Y') + 543;
$year2 = date('y') + 43;
$a = 1;
$c2 = 1;
// $invoiceNumber = App\Http\Controllers\Api\FunctionsController::invoice_last_record()->id + 1;
?>
@section('style')
    <style>
        * {
            font: 11pt "Sarabun";
            font-weight: bold;
            color: black
        }

        .a5{
            /* width: 210mm; */
            min-width:  210mm !important;
            height : 148.5mm; 
            min-height: 148.5mm;
           /* border: 1px solid red; */
           /* padding: 10px */
        }
        .border-l-t-b-none{
            border-left: none;
            border-top: none;
            border-bottom: none
        }
       </style>
       <style>
        * {
      -webkit-print-color-adjust: exact;
      font-size: 0.85rem
    }
    
    .username {
        font-size: 13px;
        font-weight: 600;
        margin-top: -1px;
        color: blue;
        text-decoration: underline
    }
    
        td{
            border: 1px solid black
        }
        .aa td{
            height:80px
        }
        .a td{
            min-width: 25%;
            height: 50px;
            border: none !important;
            padding: 3px
        }
        .a td.org div{
            color: white;
    
        }
        .owe{
                border: none;
        }
        .box-icon {
        border-radius: .25rem;
        -ms-flex-align: center;
        align-items: center;
        display: -ms-flexbox;
        display: flex;
        font-size: 2.875rem;
        -ms-flex-pack: center;
        justify-content: center;
        text-align: center;
        /* width: 70px; */
      
    }
    .mb-3, .my-3 {
        margin-bottom: 0rem !important;
    }
    
    .reciept_topic{
        color: blue;
        font-weight: bold;
        font-size: 1.2rem
    }
    .list-group-item {
        position: relative;
        font-size: 0.8rem !important;
        display: block;
        padding: .15rem;
        background-color: #fff;
        border: 1px solid rgba(0, 0, 0, .125);
    }
    
    .td-border-none{
        border: none
    }
    
    .row {
        display: -ms-flexbox;
        display: flex;
        -ms-flex-wrap: wrap;
        flex-wrap: wrap;
        margin-right: 1px !important; 
        margin-left: 1px !important;
    }
    
    .list-group li a{
        font-size: 0.8rem 
    }
    
    .list-group li b{
        font-size: 0.9rem !important
    
    }
    @media print {
        * {
      -webkit-print-color-adjust: exact;
      font-size: 0.84rem
    }
    
    .username {
        font-size: 11px;
        font-weight: 600;
        margin-top: -1px;
        color: blue;
        text-decoration: underline
    }
    
        td{
            border: 1px solid black
        }
        .aa td{
            height:80px
        }
        .a td{
            min-width: 25%;
            height: 50px;
            padding: 2px;
        }
        .a td.org{
            border: blue 1px solid
        }
        .a td.org div{
            color: white;
            font-size: 0.8rem
    
        }
        .owe{
                border: none;
        }
        .box-icon {
        border-radius: .25rem;
        -ms-flex-align: center;
        align-items: center;
        display: -ms-flexbox;
        display: flex;
        font-size: 1.875rem;
        -ms-flex-pack: center;
        justify-content: center;
        text-align: center;
        /* width: 70px; */
      
    }
    .mb-3, .my-3 {
        margin-bottom: 0rem !important;
    }
    
    .reciept_topic{
        color: blue;
        font-weight: bold;
        font-size: 0.8rem
    }
    .list-group-item {
        position: relative;
        font-size: 0.7rem !important;
        display: block;
        padding: .15rem;
        background-color: #fff;
        border: 1px solid rgba(0, 0, 0, .125);
    }
    
    .row {
        display: -ms-flexbox;
        display: flex;
        -ms-flex-wrap: wrap;
        flex-wrap: wrap;
        margin-right: 1px !important; 
        margin-left: 1px !important;
    }
    
    .list-group li a{
        font-size: 0.7rem 
    }
    
    .list-group li b{
        font-size: 0.8rem !important
    
    }
    }
       
    
    </style>
@endsection
@section('content')
    <input type="hidden" id="type" value="{{ $type }}">
    <br>
    <div class="row a5" style="">
        <div class="col-6">
            @include('keptkaya.kp_payment._rc_left_form')
        </div>
        <div class="col-6">
            @include('keptkaya.kp_payment._rc_right_form')
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

            //window.print();
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
