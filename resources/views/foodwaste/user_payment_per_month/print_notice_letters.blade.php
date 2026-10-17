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
            opacity: 0;
        }
        .date, .topic{
            margin-top:2rem
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
    @foreach ($owes as $owe)

    <div class="row top-row">

        <div class="col-12 p-3 ">
            <div class="row header1">
                <div class="col-4">ที่ ยส. ๗๓๖๐๒/ว</div>
                <div class="col-4">
                    <img src="{{ asset('logo/krut.png') }}" width="130" alt="">
                </div>
                <div class="col-4 address">
                    <div>สำนักงานเทศบาลตำบลห้องแซง</div>
                    <div>222 หมู่ 17 ตำบลห้องแซง</div>
                    <div>อำเภอเลิงนกทา ยส 35120</div>
                </div>
            </div>
            <div class="row date">
                <div class="col-4 text-center"></div>
                <div class="col-4"> {{ $print_date }}</div>
                <div class="col-4"></div>
            </div>

            <div class="row topic">
                <div class="col-12">
                    <div>เรื่อง แจ้งเตือนให้มาชำระค่าจัดเก็บขยะรายปี</div>
                    <div class="mt-3">
                        เรียน<span class="member_name">
                            &nbsp;&nbsp;&nbsp;&nbsp; {{ $owe['user_info'][0]['prefix']."".$owe['user_info'][0]['firstname']." ".$owe['user_info'][0]['lastname'] }} &nbsp;&nbsp;&nbsp;&nbsp;
                        </span>
                        &nbsp;บ้านเลขที่ {{ $owe['user_info'][0]['address'] }}
                        <span class="member_address">
                            &nbsp; {{ $owe['zonename'] }}</span>
                    </div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-12">
                    <div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ตามที่ เทศบาลตำบลห้องแซงได้ดำเนินการจัดเก็บค่าจัดเก็บขยะรายปี
                          โดยจัดเก็บถังละ {{ number_format($owe['owe_infos'][0]['rate_per_month']*12, 2) }} บาท  ต่อปี และเทศบาลตำบลห้องแซงได้ตรวจสอบผู้ค้างชำระค่าจัดเก็บขยะรายปี พบว่าท่านมียอดค้างชำระโดยมีรายละเอียดดังนี้</div>
                    <br>
                    <div>
                        @php
                            $c = 1;
                        @endphp
                        @foreach ($owe['owe_infos'] as $item)
                        <table border="1">
                            <tr>
                                <th>ลำดับ</th>
                                <th>ปีงบประมาณ</th>
                                <th>จัดเก็บรายเดือนต่อถัง (บาท)</th>
                                <th>ค้างชำระ (เดือน)</th>
                                <th>ถังขยะที่ใช้ (ถัง)</th>
                                <th>รวมเป็นเงินค้างชำระ (บาท)</th>
                            </tr>
                            <tr>
                                <td class="text-center">    {{ $c++ }}                                      </td>
                                <td class="text-center">    {{ $item['budgetyear_name'] }}                  </td>
                                <td class="text-right ">    {{ number_format($item['rate_per_month'], 2) }} </td>
                                <td class="text-right ">    {{ $item['owe_month_count'] }}                  </td>
                                <td class="text-right ">    {{ $item['bin_count'] }}                        </td>
                                <td class="text-right ">    {{ number_format( $item['owe_total'], 2) }}     </td>
                            </tr>
                        </table>
                        @endforeach

                    </div>
                    <br>
                    <div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        การนี้เทศบาลตำบลห้องแซงจึงขอให้ท่านไปชำระค่าจัดเก็บขยะรายปี  ได้ที่
                        เทศบาลตำบลห้องแซง กองคลัง งานจัดเก็บรายได้ หรือ ชำระโดยวิธีการโอนเงิน (กรุณาติดต่อเจ้าหน้าที่ ที่เบอร์ 045-777116 ต่อ 18 หรือ 088-1005436)  ในวันเวลาราชการ
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;จึงเรียนมาเพื่อโปรดทราบและดำเนินการต่อไป
                </div>
            </div>

            <div class="row" style="margin-top: 5rem">
                <div class="col-2">&nbsp;</div>
                <div class="col-6">ขอแสดงความนับถือ</div>
            </div>

            <div class="row mt-4">
                <div class="col-4"></div>
                <div class="col-4 text-center">
                    <div class="sign">
                        <img src="{{ asset('/sign/sign.png') }}" width="100" alt="">
                    </div>
                    <div>(นางละเอียด     ศรีสุข)</div>
                    <div>นายกเทศมนตรีตำบลห้องแซง</div>

                </div>
                <div class="col-4"></div>
            </div>
            <div class="row office_adress">
                <div class="col-12">
                    <div>กองคลัง</div>
                    <div>งานจัดเก็บรายได้</div>
                    <div>โทรศัพท์ 0-4577-7116 ต่อ 18</div>
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
            // window.print();

            // window.onafterprint = function(){
            //     window.history.back();
            // }
        });
    </script>

@endsection
