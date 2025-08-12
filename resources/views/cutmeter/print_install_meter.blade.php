@extends('layouts.print')



@section('content')
<?php
use App\Http\Controllers\Api\FunctionsController;
use App\Http\Controllers\Api\ZoneController;
use App\Http\Controllers\Api\SubzoneController;
$fnc = new FunctionsController();
$zoneApi = new ZoneController();
$subzoneApi = new SubzoneController();
    ?>

<head>

    <link rel="stylesheet" href="{{ asset('adminlte/dist/css/adminlte.min.css') }}">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</head>

<?php $index = 0; ?>

@section('style')
    <style>
        table {
            font-size: 1.08rem
        }


        @media print {

            table td {
                /* border-top: 1px none !important; */

            }

        }
    </style>
@endsection
@section('content')


    <?php

    $headText = 'ดำเนินการติดตั้งมิเตอร์น้ำประปา';

        ?>
    <table class="table" id="logo" style="width:220mm; margin-left:5rem; margin-top:1rem">
        <tr>
            <td class="text-center">
                <img src="{{ asset('logo/hs_logo.jpg') }}" style="width: 3cm; height:3cm">
            </td>
        </tr>
        <tr>
            <td>

                <div>กิจการประปาเทศบาลตำบลขามป้อม</div>
                <div>222 หมู่ 17 ต.ขามป้อม</div>
                <div>อ.พระยืน จ.ขอนแก่น 40003</div>
            </td>

        </tr>
    </table>
    <table class="table mt-4" style="width:220mm; margin-left:5rem">
        <tr>
            <td style="padding-left: 4rem;padding-right: 1rem;" colspan="4">
                <div class="row">
                    <div class="col-12 h4 text-center">ใบงาน<?php echo $headText; ?></div>
                </div>

                <div class="row mt-2">
                    <div class="col-5"></div>
                    <div class="col-4 date mb-4">
                        <?php
    $currentYear = date('Y') + 543;
    $monthThai = $fnc->fullThaiMonth(date('m'));
                            ?>
                        วันที่ {{ date('d') . ' ' . $monthThai . ' ' . $currentYear }}
                    </div>
                </div>
                <div class="row mt-1">
                    <div class="col-12">
                        <div style="letter-spacing: 1.2px;">
                            เลขที่ผู้ใช้น้ำ
                            <b>{{ $cutmeterArr[0]['usermeterinfos'][0]->meternumber }}</b>
                            <br>

                        </div>

                        <br>ในนามของ

                        <b>
                            {{ $cutmeterArr[0]['usermeterinfos'][0]->user->prefix . '' . $cutmeterArr[0]['usermeterinfos'][0]->user->firstname . ' ' . $cutmeterArr[0]['usermeterinfos'][0]->user->lastname }}
                        </b>
                        บ้านเลขที่
                        {{ $cutmeterArr[0]['usermeterinfos'][0]->user->address }}
                        <?php
    echo ' ' . $cutmeterArr[0]['usermeterinfos'][0]->user->user_zone->zone_name;
                            ?>
                        ต.ขามป้อม อ.พระยืน จ.ขอนแก่น
                        <br>
                        ได้ทำการชำระค่าน้ำประปาที่ค้างชำระเรียบร้อยแล้ว
                        <br>จึงขอให้เจ้าหน้าที่ไปทำการติดตั้งมิเตอร์น้ำ
                        ให้กับผู้ใช้น้ำเพื่อให้สามารถใช้น้ำประปาได้อีกครั้ง
                    </div>
                </div>
            </td>
        </tr>
        <tr>
            <td class="text-center" colspan="4">
                {{-- ณ วันที่ {{ date('d') . '/' . date('m') . '/' . date('Y') + 543 }} --}}
            </td>


        </tr>

        <tr>
            <th colspan="3" style="border-top:none">ผู้รับผิดชอบ {{ $headText }}</th>

        </tr>
        <tr>
            <td style="border-top:none" class="text-center">
                <div>______________________</div>
                <div>
                    <h6 class="mt-2">
                        {{ Auth::user()->prefix . '' . Auth::user()->firstname . ' ' . Auth::user()->lastname }}

                    </h6>
                </div>
                <div>ผู้รับเงิน</div>
            </td>
            <td style="border-top:none" class="text-center">
                <div>______________________</div>
                <div>
                    <h6 class="mt-2">
                        {{ $cutmeterArr[0]['head_twman']->prefix . '' . $cutmeterArr[0]['head_twman']->firstname . ' ' . $cutmeterArr[0]['head_twman']->lastname . ' ' . $cutmeterArr[0]['head_twman']->name }}

                    </h6>
                </div>
                <div>หัวหน้างานประปา </div>

            </td>
            @if (isset($cutmeterArr[0]['twman']))
                @foreach ($cutmeterArr[0]['twman'] as $twman)
                    <td style="border-top:none" class="text-center">
                        <div>______________________</div>
                        <div>
                            <h6 class="mt-2">
                                {{ $twman->prefix . '' . $twman->firstname . ' ' . $twman->lastname . ' ' . $twman->name }}

                            </h6>
                        </div>
                        <div>เจ้าหน้าที่{{ $headText }}</div>
                    </td>
                @endforeach

            @endif


        </tr>
        <tr>
            <td class="text-center mt-3" colspan="4" style="width: 100%; border:1px solid black">
                <h3>ชำระเงินแล้ว</h3>
                <h3>{{ $headText }}</h3>
            </td>
        </tr>
    </table>



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

            window.print();
            setTimeout(function () {

                window.location.href = '/cutmeter';

            }, 200);

            // window.onafterprint = function(){
            //     window.history.back();
            //     window.location.href = `../../invoice/zone_info/${$('#subzone_id').val()}`;
            // }
        });
    </script>

@endsection