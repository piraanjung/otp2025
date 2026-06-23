<?php
use App\Http\Controllers\Api\FunctionsController;
$fnc = new FunctionsController();
?>

@if ($a == 1)
    <table class="acc_mytable table_row1">
@elseif ($a == 2)
    <table class="acc_mytable table_row2">
@else
    <table class="acc_mytable table_row3">
@endif
            <tr>
                <td colspan="3" class="text-center h5 pt-1 pb-2">&nbsp;</td>
                <td colspan="2" class="text-center  h5 pt-1 pb-2">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="4" class="text-center"><b>&nbsp;</b></td>
                <td rowspan="4" class="text-center border-bottom-none border-right-none">
                    {{-- <img src="{{asset('/img/hslogo.jpg')}}" width="60"> --}}&nbsp;
                </td>
            </tr>
            <tr>
                <td class="td_col1">&nbsp;</td>
                <td colspan="3" class="username">&nbsp;
                    {{ $item['usermeterinfos']['user']['firstname'] . ' ' . $item['usermeterinfos']['user']['lastname'] }}
                </td>
            </tr>
            <tr>
                <td rowspan="2">&nbsp;</td>
                <td colspan="3" class="border-bottom-none address ">
                    &nbsp;
                    {{ $item['usermeterinfos']['user']['address'] }} หมู่
                    {{ $item['usermeterinfos']['user']['zone_id'] }}
                    ต.{{ $setting_tambon_infos['tambon'] }}
                </td>
            </tr>
            <tr>
                {{-- <td colspan="1">&nbsp;</td> --}}
                <td colspan="3" class="border-top-none address ">
                    &nbsp;
                    อ.{{ $setting_tambon_infos['district'] }}
                    จ.{{ $setting_tambon_infos['province'] }}
                    {{-- {{$setting_tambon_infos['zipcode']}} --}}
                </td>
            </tr>
            <tr>
                <td colspan="4" class="border-left-none  border-right-none"></td>
                <td class="border-bottom-none border-right-none "></td>
            </tr>
            <tr>
                <td class="">&nbsp;</td>
                <td class="td_col2">&nbsp;</td>
                <td class="td_col3">&nbsp;</td>
                <td class="td_col3">&nbsp;</td>
                <td class="border-right-none td_col2" rowspan="2">&nbsp;</td>
            </tr>
            <tr>
                <td class="td_col1 pt-1">
                    &nbsp;
                    {{ $item['invoice_period']['inv_p_name'] }}<!--dd-->
                </td>
                <td class="text-center pt-1">&nbsp;
                    {{ $item['usermeterinfos']['subzone']['subzone_name'] }}<!--dd-->
                </td>
                <td class="text-center pt-1">&nbsp;
                    {{ $item['id'] }}<!--dd-->
                </td>
                <td class="text-center pt-1">&nbsp;
                    {{ $item['usermeterinfos']['meternumber'] }}<!--dd-->
                </td>
            </tr>

            <tr>
                <td class="" style="width: 100px !important">&nbsp;</td>
                <td class="">&nbsp;<br>&nbsp;</td>
                <td class="">&nbsp;<br>&nbsp;</td>
                <td class="">&nbsp;<br>&nbsp;</td>
                <td class="">&nbsp;<br>&nbsp;</td>
            </tr>

            <tr>
                <?php
$diff = $item['currentmeter'] - $item['lastmeter'];
$diffPlus8 = $diff == 0 ? 0 : $diff * 8;
$reserveMeter = $diffPlus8 == 0 ? 10 : 0;

$oweSum = 0;
if (collect($item['owe'])->count() > 0) {
    $currentSum = collect($item['owe'])->sum('currentmeter');
    $lastSum = collect($item['owe'])->sum('lastmeter');
    $oweSum = ($currentSum - $lastSum) * 8;
}

$owePaid = collect($item['owe'])->count() == 0 ? 0 : $oweSum;

$total = $diffPlus8 + $reserveMeter;
    ?>
                <td class="td_col1 pt-1">
                    {{ $fnc->engDateToThaiDateFormat(Str::substr($item['created_at'], 0, 10)) }}<!--dd-->
                </td>
                <td class="text-center pt-1">
                    {{ number_format($item['currentmeter']) }}<!--dd-->
                </td>
                <td class="text-center pt-1">
                    {{ number_format($item['lastmeter']) }}<!--dd-->
                </td>
                <td class="text-center pt-1">
                    <span id="unit_used">
                        {{ number_format($diff) }}<!--dd-->
                    </span>
                    <span class="unit_usedtext">
                        (x 8 บาท)
                    </span>
                </td>
                <td class="td_money_col pt-1">
                    {{ number_format($diffPlus8) }}<!--dd-->
                </td>
            </tr>
            <tr>
                <td colspan="2" rowspan="3" class="border-bottom-none border-left-none text-center">
                    {{ QrCode::size(40)->generate($item['id']) }}
                    <div class='mt-0'>เลขใบแจ้งหนี้: {{ $item['id'] }} </div>
                </td>
                <td class="" colspan="2">&nbsp;</td>
                <td class="td_money_col pt-1">
                    {{ $reserveMeter }}<!--dd-->
                </td>
            </tr>
            <tr>
                <td class="" colspan="2">&nbsp;</td>
                <td class="td_money_col pt-1">
                    0<!--dd-->
                </td>
            </tr>
            <tr>
                <td class="" colspan="2">&nbsp;</td>
                <td class="td_money_col pt-2 waterUsedHisHead h4">
                    {{ number_format($total) }}<!--dd-->
                </td>
            </tr>


        </table>
        <table class="acc_mytable2 mt-2">

            <tr>
                <td rowspan="2">&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>

            <tr>
                {{-- {{dd($item['usermeterinfos']['subzone']['undertaker_subzone']['twman_info']['firstname'])}} --}}
                <td colspan="7" class="text-center border-left-none border-right-none pt-2">
                    &nbsp;&nbsp;&nbsp;
                    <input type="text"
                        class="text-center border-left-none border-right-none border-top-none border-bottom-none mb-1"
                        style="width: 200px;"
                        value="{{ $item['usermeterinfos']['subzone']['undertaker_subzone']['twman_info']['firstname'] . ' ' . $item['usermeterinfos']['subzone']['undertaker_subzone']['twman_info']['lastname'] }}">
                    &nbsp;
                    <br>&nbsp;
                </td>
            </tr>



        </table>










        {{-- <php use App\Http\Controllers\Api\FunctionsController; $fnc=new FunctionsController(); ?>
            <table border="1" width="100%">
                <tr>
                    <td colspan="3" class="text-center waterUsedHisHead h5 pt-2 pb-2">ใบรับเงินค่าน้ำประปา</td>
                    <td colspan="2" class="text-center waterUsedHisHead h5 pt-2 pb-2">(ไม่ใช่ใบเสร็จรับเงิน)</td>


                </tr>


                <tr>
                    <td colspan="4" class="text-center"><b>กิจการประปา เทศบาลตำบลขามป้อม โทร. 045777116 ต่อ 18</b>
                    </td>
                    <td rowspan="4" class="text-center border-bottom-none border-right-none">
                        <img src="{{asset('/img/hslogo.jpg')}}" width="60">
                    </td>
                </tr>
                <tr>
                    <td class="waterUsedHisHead">ชื่อผู้ใช้น้ำ</td>
                    <td colspan="3" class="textvalue"> {{ $item['user_profile']['name'] }}</td>

                </tr>
                <tr>
                    <td rowspan="2" class="waterUsedHisHead ">ที่อยู่</td>
                    <td colspan="3" class="border-bottom-none textvalue"> {{ $item['user_profile']['address'] }} หมู่ {{
                        $item['user_profile']['zone_id'] }} ต.ขามป้อม </td>
                </tr>
                <tr>
                    <td colspan="3" class="border-top-none textvalue">อ.พระยืน จ.ขอนแก่น 40003</td>
                </tr>
                <tr>
                    <td colspan="4" class="border-left-none  border-right-none"></td>
                    <td class="border-bottom-none border-right-none"></td>
                </tr>
                <tr>
                    <td class="waterUsedHisHead text-center">ประจำเดือน</td>
                    <td class="waterUsedHisHead text-center">เส้นทาง</td>
                    <td class="waterUsedHisHead text-center">เลขที่ผู้ใช้</td>
                    <td class="waterUsedHisHead text-center">เลขที่มิเตอร์</td>
                    <td class="border-right-none" rowspan="2">&nbsp;</td>
                </tr>
                <tr>
                    <td class="text-center textvalue">{{ $item['invoice_period']['inv_period_name'] }}</td>
                    <td class="text-center textvalue">{{ $item['usermeterinfos']['subzone']['subzone_name'] }}</td>
                    <td class="text-center textvalue">{{ $item['id'] }}</td>
                    <td class="text-center textvalue meternumber">{{ $item['usermeterinfos']['meternumber'] }}</td>
                </tr>
                <tr>
                    <td class="waterUsedHisHead text-center">วันที่จดมาตร</td>
                    <td class="waterUsedHisHead text-center">มิเตอร์<br>ปัจจุบัน</td>
                    <td class="waterUsedHisHead text-center">มิเตอร์<br>ครั้งก่อน</td>
                    <td class="waterUsedHisHead text-center">จำนวน<br>หน่วยที่ใช้</td>
                    <td class="waterUsedHisHead text-center">จำนวนเงิน<br>(บาท)</td>
                </tr>

                <tr>
                    <php $diff=$item['currentmeter'] - $item['lastmeter']; $diffPlus8=$diff==0 ? 0 : $diff * 8;
                        $reserveMeter=$diffPlus8==0 ? 10 : 0; $oweSum=0; if(collect($item['owe'])->count() > 0){
                        $currentSum = collect($item['owe'])->sum('currentmeter');
                        $lastSum = collect($item['owe'])->sum('lastmeter');
                        $oweSum = ($currentSum - $lastSum) * 8;
                        }

                        $owePaid = collect($item['owe'])->count() == 0 ? 0 : $oweSum;


                        $total = $diffPlus8 + $reserveMeter;
                        ?>
                        <td class="text-center textvalue">{{
                            $fnc->engDateToThaiDateFormat(Str::substr($item['created_at'], 0, 10) ) }}</td>
                        <td class="text-right textvalue">{{number_format( $item['currentmeter'] )}}</td>
                        <td class="text-right textvalue">{{number_format( $item['lastmeter'] )}}</td>
                        <td class="text-right textvalue">
                            <span id="unit_used">{{number_format($diff)}}</span>
                            <span class="unit_usedtext"> (x 8 บาท)</span>
                        </td>
                        <td class="text-right textvalue">{{number_format($diffPlus8)}}</td>
                </tr>
                <tr>
                    <td colspan="2" rowspan="3" class="border-bottom-none border-left-none text-center">
                        {{ QrCode::size(60)->generate($item['id']) }}
                        <div class='mt-0'>เลขใบแจ้งหนี้: {{ $item['id'] }} </div>
                    </td>
                    <td class="" colspan="2">ค่ารักษามาตร (บาท)</td>
                    <td class="text-right textvalue">{{ $reserveMeter }}</td>
                </tr>
                <tr>
                    <td class="" colspan="2">ภาษีมูลค่าเพิ่ม 7%</td>
                    <td class="text-right textvalue">0</td>
                </tr>
                <tr>
                    <td class="" colspan="2">รวมที่ต้องชำระ (บาท)</td>
                    <td class="text-right textvalue waterUsedHisHead h4">{{number_format($total)}}</td>
                </tr>


            </table>
            <table border="0" width="100%" class="mt-2">

                <tr>
                    <td rowspan="2">&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="7" class="text-center border-left-none border-right-none pt-2">
                        (ลงชื่อ)
                        <input type="text" class="text-center border-left-none border-right-none border-top-none mb-1"
                            style="width: 200px"
                            value="{{$item['usermeterinfos']['subzone']['undertaker_subzone']['user_profile']['name']}}">
                        ผู้รับเงิน
                        <br>พนักงานเก็บเงิน
                    </td>
                </tr>

            </table> --}}