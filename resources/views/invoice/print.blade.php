@extends('layouts.print')



@section('content')
<?php
use App\Http\Controllers\Api\FunctionsController;

?>

<head>

    <link rel="stylesheet" href="{{asset('adminlte/dist/css/adminlte.min.css')}}">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    
</head>

<?php $index =  0; ?>
<input type="hidden" id="subzone_id" value="{{$subzone_id}}">
<?php 
    $year = date('Y')+543;
    $year2 = date('y')+43;
    $a =1;
    $c2 = 1;
    $invoiceNumber = FunctionsController::invoice_last_record()->id + 1;
?>
    @section('style')
        <style>
           
            body {
                -webkit-print-color-adjust: exact !important;
            }
            td{
                padding: 2.3pt 2pt 1pt 2pt;
            }
            *{
                font: 9.5pt "Sarabun";
                font-weight: bold;
                color:black;
            }
          
            .td_col1{
                width: 87pt !important;
                text-align: center;
            }

            .td_col2{
                width: 65pt !important
            }
            .td_col3{
                width: 75.5pt !important
            }

            .td_money_col{
                text-align: right;
                padding-right: 8pt
            }
            .td_staff_col1{
                /* window */
                /* width: 65pt; */
                /* mac */
                width: 60pt;
            }
            .td_staff_col2{
                text-align: right;
                padding-top: 0px !important;
                vertical-align: top
            }

            .username, .address{
                padding-left: 13pt
            }

            .noneborder_top_left{
                border-top: 1px solid white;
                border-right:1px solid white; 
            }
           
            .border-bottom-none{
                border-bottom: 1px solid white;
            }
            .border-right-none{
                border-right: 1px solid white;
            }
            .border-top-none{
                border-top: 1px solid white;
            }
            .border-left-none{
                border-left: 1px solid white;
            }
            .border-bottom-fill{
                border-bottom: 1px solid lightgrey;
            }
            .border-right-fill{
                border-right: 1px solid lightgrey;
            }
            .border-top-fill{
                border-top: 1px solid lightgrey;
            }
            .border-left-fill{
                border-left: 1px solid lightgrey;
            }
            .unit_usedtext{
                font-size: 8pt;
            }
            .td_history{
                padding: 1.5pt 1pt 1pt 1pt;
                font-size: 0.9rem
            }

            .page-break {
                /* page-break-before: always; */
                page-break-after: always;
    
            }

            /* .page-break:first {
                page-break-after: avoid;
                margin-top: 15px  !important
            } */

            @media print { 
                @page { margin: 0; }
                * { overflow: hidden!important; } 
            }

            .col-5, .col-52, .col-2{
                /* border:1px solid red */
            }
            table, th, td {
                /* border: 1px solid red !important; */
            }
            .table_row11{
                /* windows */
                margin-top: 7mm !important;
                /* margin-top: 5mm !important;mac */

            }
            .table_row2{
                /* windows */
               margin-top: 14mm !important;
               /*mac*/
               /* margin-top: 12.5mm !important;  */
            }
            .table_row3{
                 /* windows */
               margin-top: 13.7mm !important;
               /*mac*/
               /* margin-top: 11mm !important;  */

            }

            .col-5{
                flex: 0 0  420px !important;
                max-width:  420px !important;
                margin-left: 4px !important;

            }
            .col-52{
                flex: 0 0  410px !important;
                max-width:  410px !important;
                margin-left: 4px !important;

            }
            .col-2{
                flex: 0 0  150 !important;
                max-width:  150 !important;
            }

            .mytable{
                width: 380px !important;
                margin-left: 6mm;
            }
            .mytable2{
                width: 375px !important;
                margin-left: 7mm;
            }

            .mytextvalueNameAndAddr{
                padding-left: 10pt !important
            }
            .mytextvalue{
                padding-right: 5pt !important
            }
            .acc_mytable{
                width: 384px !important;
            
            }
            .acc_mytable2{
                width:384px !important;

            }    
            .staff_mytable{
                width: 120px !important;
            }
     
        </style>
    @endsection
    @section('content')
        <?php $a = 0; $i=0; ?>
            @foreach ($invoiceArray as $item)
                <?php ++$a; ++$i; ?>
                @if ($a == 1)
                    <div class="row table_row11">
                @elseif ($a == 2) 
                    <div class="row">
                @else
                    <div class="row">
                @endif
            
                    <div class="col-5">
                    @include('invoice._iv_form')
                    </div>
                    <div class="col-52">
                        @include('invoice._iv_acceptmoney_form')
                    </div>
                    <div class="col-2">
                        @include('invoice._iv_staff_form')
                    </div>
                </div>
                @if ($a == 3)
                    <?php $a = 0; ?>
                    <div class="page-break"></div>
                @endif
            @endforeach
    @endsection

    @section('script')

    <script>
        $(document).ready(function () {
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
            setTimeout(function(){ window.location.href = '../invoice/index'; }, 200);

            window.onafterprint = function(){
                window.history.back();
                window.location.href = `../../invoice/zone_info/${$('#subzone_id').val()}`;
            }
        });
    </script>

    @endsection
