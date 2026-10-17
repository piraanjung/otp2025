<?php
$receipt_th_date = App\Http\Controllers\Api\FunctionsController::engDateToThaiDateFormat($datas['update_date']);
$today = App\Http\Controllers\Api\FunctionsController::engDateToThaiDateFormat(date('Y-m-d'));
?>

<table width="94.8%" border="1" style="margin-top:8px !important;" class="t2 border-0">
    <tr>
        <td colspan="5" class="text-center head pt-2 pb-2">ใบเสร็จรับเงินค่าจัดเก็บขยะ</td>
        <td colspan="3" class="text-left head2 pt-2 pb-2">
        เลขที่ &nbsp;
        </td>
    </tr>
    <tr>
        <td colspan="5" class="text-left text-primary address">
            <div class="address">เทศบาลตำบลห้องแซง โทร. 08-81005436
                <div class="address">222 หมู่ 17 ตำบลห้องแซง อำเภอเลิงนกทา จังหวัดยโสธร 35120</div>
            </div>
        </td>
        {{-- <td colspan="2" rowspan="3" class="text-center border-right-none border-bottom-none">
            <img src="{{asset('/logo/hz_logo.png')}}" width="110">
        </td> --}}
        <td colspan="3" class="text-center pt-0 pb-0">
            <div>{{ $receipt_th_date }}</div>
            @if ($receipt_th_date < $today)
                <div style="font-size: 0.8rem;"> ( ปริ้น: {{ $today }} ) </div>
            @endif
        </td>
    </tr>
    <tr>
        <td class="waterUsedHisHead" style="width:80px;">ชื่อ-สกุล</td>
        <td colspan="4">
            {{ $datas['usermeterinfos']['user']->firstname." ".$datas['usermeterinfos']['user']->lastname }}
        </td>

        <td colspan="3" rowspan="2" class="text-center border-right-none border-bottom-none">
            <img src="{{asset('/logo/hz_logo.png')}}" width="110">
        </td>
    </tr>
    <tr>
        <td class="waterUsedHisHead">ที่อยู่</td>
        <td colspan="4" class="address pt-3">
              {{ $datas['usermeterinfos']['user']->address }}
              {{ $datas['usermeterinfos']['user']->user_zone->zone_name }}
            ต.{{ $datas['usermeterinfos']['user']->user_tambon->tambon_name }}
            อ.{{ $datas['usermeterinfos']['user']->user_district->district_name }}
            จ.{{ $datas['usermeterinfos']['user']->user_province->province_name }}
              {{ $datas['usermeterinfos']['user']->user_tambon->zipcode }}
        </td>

    </tr>
</table>
<table border="1" width="94.8%" class="t2" style="margin-top:1.4rem !important">
    <tr>

        <td width="20%" class="waterUsedHisHead">รหัสผู้ใช้</td>
        <td width="30%" class="text-center pl-1"> HSB-{{ $datas['user_id'] }}</td>
        <td width="20%" class="waterUsedHisHead border-right-none border-bottom-none border-top-none"> &nbsp;</td>
        <td width="30%" class="text-center border-right-none border-bottom-none border-top-none"> &nbsp;</td>
    </tr>
</table>

<table border="1" width="94.8%" class="t2" style="margin-top:1.1rem !important">

    <tr>
        <th class="waterUsedHisHead text-center">ปีงบประมาณ</th>
        <th class="waterUsedHisHead text-center">เลขที่ถังขยะ</th>
        <th class="waterUsedHisHead text-center">ช่วงเดือนที่ชำระ</th>
        <th class="waterUsedHisHead text-center">จำนวน(เดือน)</th>
        <th class="waterUsedHisHead text-center">เดือนละ(บาท)</th>
        <th class="waterUsedHisHead text-center">เป็นเงินที่ชำระ (บาท)</th>
    </tr>
    <?php
        $total = 0;
    ?>
    @foreach ($datas['paid_budgetyear'] as $key => $item)
    {{-- {{ dd($item) }} --}}
        <tr>
            <td class="text-right">{{$item['budgetyear'][0]['budgetyear_name']}}</td>
            <td class="text-right">{{$item['bin_no']}}</td>
            <td class="text-right">{{$item['start_month_paid']." ถึง " .$item['end_month_paid']}}</td>
            <td class="text-right">{{ $item['month_paid_count'] }}</td><!-- ชำระ -->
            <td class="text-right">{{ number_format($item['rate_payment_per_month'],2) }}</td> <!-- ค้างชำระ -->
            <td class="text-right number t2-pr-3">{{ number_format($item['sum_rate_payment_per_month'],2) }}</td><!-- เป็นเงินที่ชำระ-->
        </tr>
        <?php $total += $item['sum_rate_payment_per_month']; ?>
    @endforeach

    @for ($i = 6; $i > collect($datas['paid_budgetyear'])->count(); $i--)
        <tr>
            @if ($i == 6)
                <td class="" colspan="6">&nbsp;</td>
            @else
                <td class="border-top-none" colspan="6">&nbsp;</td>
            @endif
        </tr>
    @endfor
    <tr>
        <td colspan="3" rowspan="3" class=" text-center ">

                @if ($from == 'userPaymentPerMonth.printReceiptHistory')
                    <h5 class="text-danger"> ***สำเนาเอกสาร***</h5>
                @endif
        </td>
        <td class="text-right" colspan="2">รวม (บาท)</td>
        <td class="text-right number t2-pr-3">{{ number_format($total,2) }}</td>
    </tr>
    <tr>
        {{-- <td colspan="2" class="border-top-none"></td> --}}
        <td class="text-right" colspan="2">ภาษีมูลค่าเพิ่ม 7% (บาท)</td>
        <td class="text-right number t2-pr-3">{{ number_format(0,2) }}</td>
    </tr>
    <tr>
        {{-- <td colspan="2" class="border-top-none"></td> --}}
        <td class="text-right" colspan="2">รวมที่ต้องชำระ (บาท)</td>
        <td class="text-right t2-pr-3 head2 text-white bg-secondary">{{ number_format($total,2) }}</td>
    </tr>
</table>
<table border="0" width="94.8%" class="mt-4">
    <tr>
        <td colspan="7" class="text-center border-left-none border-right-none pt-4">
            ลงชื่อ<span style="text-decoration: underline; text-underline-position: under; text-decoration-style: dotted">
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                {{ $datas['paid_budgetyear'][0]['recorder'][0]->firstname." ".$datas['paid_budgetyear'][0]['recorder'][0]->lastname }}
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            </span>ผู้รับเงิน<br>&nbsp;
        </td>
    </tr>
</table>
