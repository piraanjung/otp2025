@extends('layouts.print')



@section('content')
<?php
use App\Http\Controllers\Api\FunctionsController;

$fnc = new FunctionsController();
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

        td div {
            padding-top: 2px !important;
            padding-bottom: 2px !important
        }

        .indent {
            text-indent: 3rem;
        }
    </style>
@endsection
@section('content')
    <?php $a = 0;
    $i = 0; ?>
    @foreach ($oweArray as $item)
        <div class="row">

            <div class="col-12">


                <style>
                    .textbetweenKrut {
                        padding-top: 60pt !important
                    }

                    .tesabanAddr {
                        padding-left: 60pt
                    }

                    .date {
                        padding-left: 45pt
                    }

                    @media print {
                        .table {
                            opacity: 1 !important;
                        }
                    }
                </style>
                <table class="table2" style="">
                    {{-- width:220mm; margin-left:5rem; opacity:1 --}}
                    <tr>
                        <td style="padding-left: 4rem;padding-right: 1rem;">
                            <div class="row">
                                <div class="col-12">
                                    <div style="margin-top: 2rem"></div>
                                    <div>กิจการประปาองค์การบริหารส่วนตำบลขามป้อม</div>
                                    <div>หมู่1 ต.ขามป้อม</div>
                                    <div>อ.พระยืน จ.ขอนแก่น 40320</div>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-4 text-center"></div>
                                <div class="col-6 text-left pt-3 ml-5">
                                    <div>เรียน &nbsp;
                                        {{ $item['res'][0]->tw_meter_infos->user->prefix . '' . $item['res'][0]->tw_meter_infos->user->firstname . ' ' . $item['res'][0]->tw_meter_infos->user->lastname }}
                                    </div>

                                    <div class="pl-5">
                                        {{ $item['res'][0]->tw_meter_infos->user->address . ' ' . $item['res'][0]->tw_meter_infos->user->user_zone->zone_name }}
                                        ต.ขามป้อม
                                    </div>
                                    <div class="pl-5">อ.พระยืน จ.ขอนแก่น 40320</div>
                                </div>
                            </div>
                            <hr>

                            <div style="margin-top: -1rem"></div>

                            <div class="row">
                                <div class="col-3 textbetweenKrut">ที่ ขก 78002/</div>
                                <div class="col-4 text-right">
                                    <img src="{{ asset('/logo/krut.png') }}" style="width: 3cm; height:3cm">
                                </div>
                                <div class="col-5 textbetweenKrut tesabanAddr">
                                    <div>สำนักงานองค์การบริหารส่วนตำบลขามป้อม</div>
                                    <div>หมู่1 ต.ขามป้อม อ.พระยืน จ.ขอนแก่น 40320</div>
                                </div>
                            </div>
                            <div class="row mt-4">
                                <div class="col-5"></div>
                                <div class="col-4 date">
                                    <?php
                                            $currentYear = date('Y') + 543;
                                            $monthThai = $fnc->fullThaiMonth(date('m'));
                                    ?>
                                    วันที่ {{ date('d') . ' ' . $monthThai . ' ' . $currentYear }}
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-12">เรื่อง ขอให้ชำระหนี้ค้างค่าน้ำประปา</div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-12">เรียน &nbsp;
                                    <b
                                        id="user_name">{{ $item['res'][0]->tw_meter_infos->user->prefix . '' . $item['res'][0]->tw_meter_infos->user->firstname . ' ' . $item['res'][0]->tw_meter_infos->user->lastname }}</b>
                                </div>
                            </div>
                            <div class="row mt-1">
                                <div class="col-12 indent">
                                    <div style="letter-spacing: 1.2px;">ตามที่ท่านเป็นผู้ใช้น้ำของ
                                        กิจการประปาองค์การบริหารส่วนตำบลขามป้อม
                                        เลขที่ผู้ใช้น้ำ
                                        <b>{{ $item['res'][0]->tw_meter_infos->meternumber }}</b>
                                        <input type="hidden" id="meter_id"
                                            value="{{ $item['res'][0]->tw_meter_infos->meter_id }}">
                                        <input type="hidden" id="acc_trans_id" value="{{ $item['res'][0]->acc_trans_id_fk }}">
                                        ในนามของ
                                        <b>{{ $item['res'][0]->tw_meter_infos->user->prefix . '' . $item['res'][0]->tw_meter_infos->user->firstname . ' ' . $item['res'][0]->tw_meter_infos->user->lastname }}</b>
                                        ซึ่งมีหนี้ค้างชำระค่าน้ำประปา ดังนี้

                                    </div>
                                    <div class="col-12">
                                        <table class="table mt-4 text-center table-bordered">
                                            <tr>
                                                <th class="waterUsedHisHead td_history text-center">ประจำเดือน</th>
                                                <?php    $count_history = 0; ?>
                                                @foreach ($item['res'] as $history)
                                                    <th class="text-center td_history">
                                                        {{ str_replace($currentYear, date('y') + 43, $history->invoice_period->inv_p_name) }}
                                                    </th>
                                                @endforeach
                                                <th class="text-center td_history">รวม (บาท)</th>
                                            </tr>
                                            <tr>
                                                <th class="waterUsedHisHead td_history text-center">จำนวนเงิน(บาท)</th>
                                                <?php    $oweSum = 0; ?>
                                                <?php    $count_history = 0; ?>
                                                @foreach ($item['res'] as $history)
                                                    <?php        $oweSum += $history->totalpaid; ?>
                                                    <td class="text-center td_history">
                                                        {{ $history->totalpaid }}
                                                    </td>
                                                @endforeach
                                                <td class="text-center td_history" id="oweSum">
                                                    <?php
                                                        echo number_format($oweSum, 2);
                                                    ?>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12 indent" style="">
                                        ค้างชำระ <b>{{ collect($item['res'])->count() }}</b> เดือน
                                        รวมเป็นเงินที่ต้องชำระทั้งสิ้น <b
                                            id="oweSum{{ $item['res'][0]->tw_meter_infos->meter_id }}">{{ number_format($oweSum, 2) }}
                                        </b>บาท
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12 indent" style="line-height: 1.8rem">
                                        <?php
            $currentMonthExpTemp = date('m');
            $splitArr = str_split($currentMonthExpTemp);
            $currentYear = date('Y') + 543;
            $currentMonthExp = '';
            if ($splitArr[0] == '0') {
                $plus = $splitArr[1] + 1;
                $currentMonthExp = $plus < 10 ? '0' . $plus : $plus;
            } else {
                //check เดือน 12 ?
                if ($currentMonthExpTemp == 12) {
                    $currentMonthExp = '01';
                    $currentYear = $currentYear + 1;
                } else {
                    $currentMonthExp = $currentMonthExpTemp + 1;
                }
            }
            $monthThai = $fnc->fullThaiMonth($currentMonthExp);
            $paidBeforeDate = 30;
                                                        ?>
                                        <div style="letter-spacing: -0.35px;">
                                            ในการนี้ จึงขอให้ท่านโปรดติดต่อชำระค่าน้ำประปา ได้ที่
                                            กองคลังองค์การบริหารส่วนตำบลขามป้อม
                                            ให้แล้วเสร็จ
                                            ภายในวันที่_________________________
                                            {{-- <b>{{ $paidBeforeDate.' '.$monthThai. ' ' .$currentYear}}</b> --}}
                                            หากเกินกำหนด กิจการประปามีความจำเป็นต้องระงับการใช้น้ำในวันถัดไป
                                            และจะจ่ายน้ำใหม่หลังจากได้รับการชำระหนี้ค้างทั้งหมดพร้อมทั้งค่าธรรมเนียมการใช้น้ำแล้ว
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 indent" style=" line-height: 1.8rem">
                                    จึงเรียนมาเพื่อโปรดชำระค่าน้ำประปาภายในกำหนด
                                    หวังเป็นอย่างยิ่งคงได้รับความร่วมมือด้วยดีเช่นเคย
                                    ขอขอบพระคุณมา ณ โอกาสนี้
                                    หรือหากท่านชำระค่าน้ำประปาแล้วต้องขออภัยไว้ ณ ที่นี้ด้วย
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-3">
                                    <div class="card" style="margin-top: -2.5rem">
                                        <div class="card-body text-center" style="color:black;">
                                            <textarea id="qrcode_text" class="qrcode_text"
                                                data-id="{{ $item['res'][0]->tw_meter_infos->meter_id }}" style="opacity: 0"
                                                cols="1" rows="1">{{ $item['qrcode'] }}</textarea>
                                            <div style="font-size:0.9rem; text-align:center;">สแกน QR CODE</div>
                                            <div style="font-size:0.9rem; text-align:center; border-bottom:1px solid black">
                                                ชำระเงินค่าน้ำประปา</div>
                                            <div id="qrcode{{ $item['res'][0]->tw_meter_infos->meter_id }}"></div>
                                        </div>
                                    </div>

                                </div>
                                <div class="col-8">
                                    <div class="col-12" style="margin-left: 43%">ขอแสดงความนับถือ </div>
                                    <div class="row mt-5">
                                        <div class="col-5"></div>
                                        <div class="col-6 text-center pt-3" style="line-height: 1.8rem">
                                            <div>(ร้อยตำรวจตรีชวรินทร์ อบสุกลิ่น)</div>
                                            <div style="font-size: 0.93rem; border-top:1px dotted black">
                                                นายกองค์การบริหารส่วนตำบลขามป้อม</div>
                                        </div>
                                    </div>
                                    <div class="row mt-4">
                                        {{-- <div class="col-12" style="font-size:0.9rem">
                                            <div>กิจการประปา องค์การบริหารส่วนตำบลขามป้อม</div>
                                            <div style="margin-top: -5px">โทร 043-002389 </div>
                                        </div> --}}
                                    </div>
                                </div>

                            </div>
                            <div class="col-12" style="font-size:0.9rem">
                                <div>กิจการประปา องค์การบริหารส่วนตำบลขามป้อม</div>
                                <div style="margin-top: -5px">โทร 043-002389 </div>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="page-break"></div>
    @endforeach
@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/jquery.qrcode@1.0.3/jquery.qrcode.min.js"></script>

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

            // window.print();
            // setTimeout(function() {
            //     if ('<?= $from_view ?>' === 'owepaper') {
            //         window.location.href = '/reports/owe';
            //     } else {
            //         //cutmeter
            //         window.location.href = '/cutmeter';
            //     }

            // }, 200);

            $('.qrcode_text').each(function () {
                let id = $(this).data('id')
                console.log(id)

                $('#qrcode' + id).html("")
                $('#qrcode' + id).append($('#user_name').val() + '\n');
                $('#qrcode' + id).append(
                    `<div style="font-size:0.9rem">จำนวนที่ต้องชำระ ${$('#oweSum' + id).text()} บาท</div>`
                );
                $('#qrcode' + id).append().qrcode({
                    text: $(this).val(),
                    width: 120,
                    height: 120
                });
            }); //.qrcode_text

        });
    </script>

@endsection