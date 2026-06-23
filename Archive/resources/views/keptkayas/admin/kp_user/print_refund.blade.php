@extends('layouts.print')



@section('content')
    <?php
    use App\Http\Controllers\Api\FunctionsController;

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
        .inputname{
            border-top: none;
            border-left: none;
            border-right: none;
            border-bottom: 1px dotted black;
            margin-bottom: 5px;
        }

        .indent {
            text-indent: 5rem;
        }
        .header1{
            padding-top: 5rem
        }
        .address{
            padding-top: 7rem
        }
        .top-row{
            padding-left: 8rem;
            padding-right: 8rem;
            /* opacity: 0; */
        }
        .date, .topic{
            margin-top:2rem
        }
        .xx, .xx2{
            border-top: 1px solid black;
            border-bottom: 1px solid black;
        }
        .xx{
            border-left: 1px solid black;
            border-right: 1px dotted black;
        }
        .xx2{
            border-left: 1px dotted black;
            border-right: 1px solid black;
        }


        .member_name, .sign{
            display: inline-block;
            text-decoration: dotted underline black;
        }
        .office_adress{
            margin-top: 14rem
        }
        th{
            font-size: 1rem
        }
        @media print {
            .page-break {page-break-after: always;}
            .top-row{ opacity: 1;}
        }


    </style>
@endsection
@section('content')

    <?php $a = 0;
    $i = 0; ?>
    @foreach ($print_refund_infos as $item)
    <div class="row p-3">
        <div class="col-md-6 xx">
            <div class="col-12 p-3 ">
                <div class="row">
                    <div class="col-12 text-center">
                        <h4>ใบเสร็จคืนเงินค่าจัดเก็บขยะ  (ต้นขั้ว)</h4>
                        <hr>
                    </div>
                    <div class="col-12">
                        <div>สำนักงานเทศบาลตำบลห้องแซง</div>
                        <div>222 หมู่ 17 ตำบลห้องแซง อำเภอเลิงนกทา ยส 35120</div>
                    </div>
                </div>
                <div class="row date">
                    <div class="col-4 text-center"></div>
                    <div class="col-4"> วันที่ {{ App\Http\Controllers\Api\FunctionsController::engDateToThaiDateFormat(date('Y-m-d')) }}</div>
                    <div class="col-4"></div>
                </div>
                <div class="row topic">
                    <div class="col-12">
                        <div>เรื่อง คืนเงินค่าจัดเก็บขยะรายปี</div>
                        <div class="mt-3">
                            เรียน<span class="member_name">
                                &nbsp;&nbsp;&nbsp;&nbsp; คุณ {{ $item['oldUserPaymentPerYear']->user->firstname." ".$item['oldUserPaymentPerYear']->user->lastname }} &nbsp;&nbsp;&nbsp;&nbsp;
                            </span>
                            &nbsp;บ้านเลขที่ {{ $item['oldUserPaymentPerYear']->user->address }}

                            <span class="member_address">
                                &nbsp; {{ $item['oldUserPaymentPerYear']->user->user_zone->zone_name }}</span>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ตามที่ เทศบาลตำบลห้องแซงได้ดำเนินการจัดเก็บค่าจัดเก็บขยะรายปี
                            โดยจัดเก็บถังละ {{ number_format($item['oldUserPaymentPerYear']->payment_per_year,2) }} บาท  ต่อปี ท่านได้แจ้งความประสงค์จะใช้ถังขยะจำนวน {{ $item['oldUserPaymentPerYear']->bin_quantity }} ถัง
                            และท่านได้ทำการชำระเงินแล้วเป็นจำนวน {{ number_format($item['oldUserPaymentPerYear']->paid_total_payment_per_year,2) }}  บาท
                        </div>
                        <div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; เนื่องจากมีการแจ้งขอเปลี่ยนแปลงข้อมูลค่าจัดเก็บขยะรายปี หรือ จำนวนถังขยะที่
                            ท่านต้องการใช้ดังข้อมูลดังต่อไปนี้
                        </div>
                        <br>
                        <div>

                            <table border="1">
                                <tr>
                                    <th>ปีงบประมาณ</th>
                                    <th>จัดเก็บรายเดือนต่อถัง (บาท)</th>
                                    <th>ถังขยะที่ใช้ (ถัง)</th>
                                    <th>รวมเป็นเงินค้างชำระ (บาท)</th>
                                </tr>
                                <tr>
                                    <td class="text-center">    {{ $item['oldUserPaymentPerYear']->budgetyear->budgetyear_name }} </td>
                                    <td class="text-right ">    {{ number_format( $item['payment_per_year'],2) }} </td>
                                    <td class="text-right ">    {{ $item['bin_quantity'] }}                        </td>
                                    <td class="text-right ">    {{ number_format( $item['payment_per_year'] * $item['bin_quantity'], 2) }}     </td>
                                </tr>
                            </table>

                        </div>
                        <br>
                        <div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            การนี้เทศบาลตำบลห้องแซงจึงขอคืนเงินที่ท่านได้ทำการชำระเงินแล้วเป็นจำนวน {{ number_format($item['oldUserPaymentPerYear']->paid_total_payment_per_year,2) }}  บาท
                            <div><b class="text-danger">*** และขอให้ท่านได้ดำเนินการชำระเงินค่าจัดเก็บขยะใหม่ เป็นจำนวนเงิน {{ number_format( $item['payment_per_year'] * $item['bin_quantity'], 2) }} บาท ตามข้อมูลที่แสดงในตารางข้างต้น ****</b></div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;จึงเรียนมาเพื่อโปรดทราบและดำเนินการต่อไป
                    </div>
                </div>
                </div>
                <hr>

                <div class="row mt-3 mb-2">

                    <div class="col-6 text-center">

                        <div><input type="text" class="inputname"></div>
                        <div>ผู้รับเงิน</div>

                    </div>
                    <div class="col-6 text-center">
                        <div><input type="text" class="inputname"></div>
                        <div>เจ้าหน้าที่การเงิน</div>

                    </div>
                </div>



            </div>
        </div>
        <div class="col-md-6 xx2" >
            <div class="col-12 p-3 ">
                <div class="row">
                    <div class="col-12 text-center">
                        <h4>ใบเสร็จคืนเงินค่าจัดเก็บขยะ</h4>
                        <hr>
                    </div>
                    <div class="col-12">
                        <div>สำนักงานเทศบาลตำบลห้องแซง</div>
                        <div>222 หมู่ 17 ตำบลห้องแซง อำเภอเลิงนกทา ยส 35120</div>
                    </div>
                </div>
                <div class="row date">
                    <div class="col-4 text-center"></div>
                    <div class="col-4"> วันที่ {{ App\Http\Controllers\Api\FunctionsController::engDateToThaiDateFormat(date('Y-m-d')) }}</div>
                    <div class="col-4"></div>
                </div>
                <div class="row topic">
                    <div class="col-12">
                        <div>เรื่อง คืนเงินค่าจัดเก็บขยะรายปี</div>
                        <div class="mt-3">
                            เรียน<span class="member_name">
                                &nbsp;&nbsp;&nbsp;&nbsp; คุณ {{ $item['oldUserPaymentPerYear']->user->firstname." ".$item['oldUserPaymentPerYear']->user->lastname }} &nbsp;&nbsp;&nbsp;&nbsp;
                            </span>
                            &nbsp;บ้านเลขที่ {{ $item['oldUserPaymentPerYear']->user->address }}

                            <span class="member_address">
                                &nbsp; {{ $item['oldUserPaymentPerYear']->user->user_zone->zone_name }}</span>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ตามที่ เทศบาลตำบลห้องแซงได้ดำเนินการจัดเก็บค่าจัดเก็บขยะรายปี
                            โดยจัดเก็บถังละ {{ number_format($item['oldUserPaymentPerYear']->payment_per_year,2) }} บาท  ต่อปี ท่านได้แจ้งความประสงค์จะใช้ถังขยะจำนวน {{ $item['oldUserPaymentPerYear']->bin_quantity }} ถัง
                            และท่านได้ทำการชำระเงินแล้วเป็นจำนวน {{ number_format($item['oldUserPaymentPerYear']->paid_total_payment_per_year,2) }}  บาท
                        </div>
                        <div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; เนื่องจากมีการแจ้งขอเปลี่ยนแปลงข้อมูลค่าจัดเก็บขยะรายปี หรือ จำนวนถังขยะที่
                            ท่านต้องการใช้ดังข้อมูลดังต่อไปนี้
                        </div>
                        <br>
                        <div>

                            <table border="1">
                                <tr>
                                    <th>ปีงบประมาณ</th>
                                    <th>จัดเก็บรายเดือนต่อถัง (บาท)</th>
                                    <th>ถังขยะที่ใช้ (ถัง)</th>
                                    <th>รวมเป็นเงินค้างชำระ (บาท)</th>
                                </tr>
                                <tr>
                                    <td class="text-center">    {{ $item['oldUserPaymentPerYear']->budgetyear->budgetyear_name }} </td>
                                    <td class="text-right ">    {{ number_format( $item['payment_per_year'],2) }} </td>
                                    <td class="text-right ">    {{ $item['bin_quantity'] }}                        </td>
                                    <td class="text-right ">    {{ number_format( $item['payment_per_year'] * $item['bin_quantity'], 2) }}     </td>
                                </tr>
                            </table>

                        </div>
                        <br>
                        <div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            การนี้เทศบาลตำบลห้องแซงจึงขอคืนเงินที่ท่านได้ทำการชำระเงินแล้วเป็นจำนวน {{ number_format($item['oldUserPaymentPerYear']->paid_total_payment_per_year,2) }}  บาท
                            <div><b class="text-danger">*** และขอให้ท่านได้ดำเนินการชำระเงินค่าจัดเก็บขยะใหม่ เป็นจำนวนเงิน {{ number_format( $item['payment_per_year'] * $item['bin_quantity'], 2) }} บาท ตามข้อมูลที่แสดงในตารางข้างต้น ****</b></div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;จึงเรียนมาเพื่อโปรดทราบและดำเนินการต่อไป
                    </div>
                </div>
                </div>
                <hr>

                <div class="row mt-3 mb-2">

                    <div class="col-6 text-center">

                        <div><input type="text" class="inputname"></div>
                        <div>ผู้รับเงิน</div>

                    </div>
                    <div class="col-6 text-center">
                        <div><input type="text" class="inputname"></div>
                        <div>เจ้าหน้าที่การเงิน</div>

                    </div>
                </div>



            </div>
        </div>
    </div>

    <div class="page-break"></div>
    @endforeach

@endsection

@section('script')

    <script>
        $(document).ready(function() {
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

            window.onafterprint = function(){
                if("<?php echo $from == 'edit' ?>"){
                    window.location.href = `../../users`;
                }else{
                    window.history.back();
                }


            }
        });
    </script>

@endsection
