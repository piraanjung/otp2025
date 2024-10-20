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

    @section('style')
        <style>
            table{
                font-size: 1.08rem
            }
            td div{
                padding-top: 2px !important;
                padding-bottom: 2px !important
            }
            .indent{
                text-indent: 5rem;
            }
        </style>
    @endsection
    @section('content')

                <div class="row">
                    <div class="col-12">
                    @include('cutmeter._iv_form')
                    </div>
                </div>

                <div class="page-break"></div>
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

            //  window.print();
            // setTimeout(function(){

            //         window.location.href = '../../cutmeter/index';

            // }, 200);

            // window.onafterprint = function(){
            //     window.history.back();
            //     window.location.href = `../../invoice/zone_info/${$('#subzone_id').val()}`;
            // }
        });
    </script>

    @endsection
