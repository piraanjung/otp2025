<?php
use App\Http\Controllers\Api\FunctionsController;
$fnc = new FunctionsController();
?>
<style>
    .ivtable{
        width: 94% !important;
        margin-left: 2rem
    }
</style>
   @if ($a == 1)
    <table class="ivtable"  style="margin-top: 1.8rem !important;">

   {{-- <div class="row mb-1" style="margin-top: 1.8rem !important; margin-left: 5rem !important"> --}}
@elseif ($a == 2) 
    <table class="ivtable"  style="margin-top: 4rem !important;">
   {{-- <div class="row mt-0 mb-3 " style="margin-top: 4rem !important; margin-left: 5rem !important"> --}}
@else
    <table class="ivtable"  style="margin-top: 3.85rem !important">
   {{-- <div class="row  mb-0 " style="margin-top: 3.85rem !important; margin-left: 5rem !important"> --}}
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
             {{ $item['user_profile']['name'] }}
        </td>
    </tr>
    <tr>
        <td rowspan="2">&nbsp;</td>
        <td colspan="3"  class="border-bottom-none address "> 
            &nbsp;
            {{ $item['user_profile']['address'] }} หมู่ {{ $item['user_profile']['zone_id'] }} ต.ห้องแซง 
        </td>
    </tr>
    <tr>
        {{-- <td colspan="1">&nbsp;</td> --}}
        <td colspan="3" class="border-top-none address ">
            &nbsp;
            อ.เลิงนกทา จ.ยโสธร 35120
        </td>
    </tr>
    <tr>
        <td colspan="4" class="border-left-none  border-right-none" ></td>
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
            {{ $item['invoice_period']['inv_period_name'] }}<!--dd-->
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
        <td class="" style="width: 100px !important" >&nbsp;</td>
        {{-- <td class="waterUsedHisHead text-left pl-3">&nbsp;</td>
        <td class="waterUsedHisHead text-left pl-3">&nbsp;</td>
        <td class="waterUsedHisHead text-left pl-3">&nbsp;</td>
        <td class="waterUsedHisHead text-left pl-3">&nbsp;</td>  --}}
        

        <td class="">&nbsp;<br>&nbsp;</td>
        <td class="">&nbsp;<br>&nbsp;</td>
        <td class="">&nbsp;<br>&nbsp;</td>
        <td class="">&nbsp;<br>&nbsp;</td>
    </tr>

    <tr>
        <?php  
            $diff = $item['currentmeter'] - $item['lastmeter'];  
            $diffPlus8 =  $diff == 0 ? 0 : $diff * 8;
            $reserveMeter = $diffPlus8 == 0 ? 10 : 0;

            $oweSum = 0; 
            if(collect($item['owe'])->count() > 0){
                $currentSum = collect($item['owe'])->sum('currentmeter');
                $lastSum    = collect($item['owe'])->sum('lastmeter');
                $oweSum     = ($currentSum - $lastSum) * 8;
            }
            
            $owePaid   =  collect($item['owe'])->count() == 0 ? 0 : $oweSum;

            
            $total = $diffPlus8  + $reserveMeter;
        ?>
        <td class="td_col1 pt-1">
            {{ $fnc->engDateToThaiDateFormat(Str::substr($item['created_at'], 0, 10) ) }}<!--dd-->
        </td>
        <td class="text-center pt-1">
            {{number_format( $item['currentmeter'] )}}<!--dd-->
        </td>
        <td class="text-center pt-1">
            {{number_format( $item['lastmeter'] )}}<!--dd-->
        </td>
        <td class="text-center pt-1">
            <span id="unit_used">
                {{number_format($diff)}}<!--dd-->
            </span> 
            <span class="unit_usedtext"> 
                (x 8 บาท)
            </span>
        </td>
        <td class="td_money_col pt-1">
            {{number_format($diffPlus8)}}<!--dd-->
        </td>
    </tr>
    <tr>
        <td colspan="2" rowspan="3" class="border-bottom-none border-left-none text-center"> 
            {{-- {{ QrCode::size(40)->generate($item['id']) }} --}}
            <div class='mt-0'>เลขใบแจ้งหนี้:  {{ $item['id'] }} </div>
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
            {{number_format($total)}}<!--dd-->
        </td>
    </tr>


</table> 
<table class="ivtable mt-1">
    <tr>
        <td class="waterUsedHisHead td_history text-center" width="13.4%" rowspan="2">&nbsp;<br>&nbsp;</td>
        <td class="waterUsedHisHead td_history text-center" width="13.4%">&nbsp;</td>
        <?php $count_history = 0; ?>
        @foreach ($item['inv_history'] as $history)
            
            <?php $count_history++; ?>
            <td class="text-center td_history" width="12.1%">
                {{ $history['invoice_period']['inv_period_name'] != "" ? $history['invoice_period']['inv_period_name'] : '&nbsp;'   }}
            </td>
           
        @endforeach
        @for ($i = collect($item['inv_history'])->count(); $i < 5; $i++)
            <td class="text-center td_history" width="12.1%">&nbsp;&nbsp;&nbsp;&nbsp;</td>
        @endfor
        <td class="text-center td_history" width="12.1%">ค้าง<span style="font-size: 0.7rem">(บาท)</span></td>
    </tr>
    <tr>

        <td class="waterUsedHisHead td_history text-right" width="26.8%">&nbsp;</td>
        <?php $oweSum = 0;  ?>
        @foreach ($item['inv_history'] as $history)
            <td class="text-center td_history " width="12.1%">
                <?php 
                    $count_history++;
                    $history_diff = $history['currentmeter'] - $history['lastmeter'];
                    $history_status = $history['status'] =='owe'   ? '(ค้าง)' : '';
                    if($history['status'] == 'owe'){
                        $oweSum += $history_diff * 8;
                        if($history_diff == 0){
                            $oweSum += 10;
                            $history_diff = 10;
                        }
                    }
                    echo $history_diff;
                ?>
                <span style="color: red; font-size:0.70rem;"> {{ $history_status }}</span>
            </td>
           
        @endforeach
        @for ($i = collect($item['inv_history'])->count(); $i < 5; $i++)
            <td class="text-center td_history" width="12.1%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
        @endfor
        <td class="text-center td_history" width="12.1%"> 
            {{$oweSum}}</td>
        <?php $count_history  = 0; $oweSum = 0; ?>
    </tr>
    
</table>
<table width="100%">
    <tr>
        <td  class="text-center border-left-none border-right-none 
            {{$a== 3 ? 'pt-0' :'pt-2'}}">
            <?php
                $currentMonthExp = explode( '-', $item['invoice_period']['inv_period_name'])[0];
                $currentYear = date('Y') + 543;
                $monthThai =  $fnc->fullThaiMonth($currentMonthExp);
                $paidBeforeDate = 30;
            ?>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;   
            &nbsp;&nbsp; &nbsp; &nbsp; &nbsp;&nbsp;&nbsp; &nbsp; &nbsp; &nbsp;&nbsp;&nbsp; &nbsp;

            <span id="expiredDate" class="h4" style="opacity: 0">
                {{ $paidBeforeDate.' '.$monthThai. ' ' .$currentYear}}
            </span>
        </td>
    </tr>
    <tr>
        <td  class="text-center m_detail border-left-none border-right-none border-bottom-none text-danger">
            &nbsp;
        </td>
    </tr>
</table>





{{-- <php
use App\Http\Controllers\Api\FunctionsController;
$fnc = new FunctionsController();
?>

<table border="0" width="100%">
    <tr>
        <td colspan="3" class="text-center waterUsedHisHead h5 pt-1 pb-2">ใบแจ้งค่าน้ำประปา</td>
        <td colspan="2" class="text-center waterUsedHisHead h5 pt-1 pb-2">(ไม่ใช่ใบเสร็จรับเงิน)</td>


    </tr>


    <tr>
        <td colspan="4" class="text-center"><b>กิจการประปา เทศบาลตำบลห้องแซง โทร. 045777116 ต่อ 18</b></td>
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
        <td colspan="3"  class="border-bottom-none textvalue"> {{ $item['user_profile']['address'] }} หมู่ {{ $item['user_profile']['zone_id'] }} ต.ห้องแซง </td>
    </tr>
    <tr>
        <td colspan="3" class="border-top-none textvalue">อ.เลิงนกทา จ.ยโสธร 35120</td>
    </tr>
    <tr>
        <td colspan="4" class="border-left-none  border-right-none" ></td>
        <td class="border-bottom-none border-right-none "></td>
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
        <td class="text-center textvalue">{{ $item['usermeterinfos']['meternumber'] }}</td>
    </tr>
    <tr>
        <td class="waterUsedHisHead text-center">วันที่จดมาตร</td>
        <td class="waterUsedHisHead text-center">มิเตอร์<br>ปัจจุบัน</td>
        <td class="waterUsedHisHead text-center">มิเตอร์<br>ครั้งก่อน</td>
        <td class="waterUsedHisHead text-center">จำนวน<br>หน่วยที่ใช้</td>
        <td class="waterUsedHisHead text-center">จำนวนเงิน<br>(บาท)</td>
    </tr>

    <tr>
        <php  
            $diff = $item['currentmeter'] - $item['lastmeter'];  
            $diffPlus8 =  $diff == 0 ? 0 : $diff * 8;
            $reserveMeter = $diffPlus8 == 0 ? 10 : 0;

            $oweSum = 0; 
            if(collect($item['owe'])->count() > 0){
                $currentSum = collect($item['owe'])->sum('currentmeter');
                $lastSum    = collect($item['owe'])->sum('lastmeter');
                $oweSum     = ($currentSum - $lastSum) * 8;
            }
            
            $owePaid   =  collect($item['owe'])->count() == 0 ? 0 : $oweSum;

            
            $total = $diffPlus8  + $reserveMeter;
        ?>
        <td class="textvalue text-center ">{{ $fnc->engDateToThaiDateFormat(Str::substr($item['created_at'], 0, 10) ) }}</td>
        <td class="textvalue text-right">{{number_format( $item['currentmeter'] )}}</td>
        <td class="textvalue text-right">{{number_format( $item['lastmeter'] )}}</td>
        <td class="textvalue text-right">
            <span id="unit_used">{{number_format($diff)}}</span> 
            <span class="unit_usedtext"> (x 8 บาท)</span>
        </td>
        <td class="text-right textvalue">{{number_format($diffPlus8)}}</td>
    </tr>
    <tr>
        <td colspan="2" rowspan="3" class="border-bottom-none border-left-none text-center">
            {{ QrCode::size(60)->generate($item['id']) }}
            <div class='mt-0'>เลขใบแจ้งหนี้:  {{ $item['id'] }} </div>
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
        <td class="waterUsedHisHead td_history text-center" rowspan="2">ประวัติ<br>การใช้น้ำ</td>
        <td class="waterUsedHisHead td_history text-center">รอบบิล</td>
        <php $count_history = 0; ?>
        @foreach ($item['inv_history'] as $history)
            <php $count_history++; ?>
            <td class="text-center td_history">
                {{ $history['invoice_period']['inv_period_name'] }}
            </td>
            @if (collect($item['inv_history'])->count() == $count_history)
                <td class="text-center td_history">ค้าง(บาท)</td>
                <php $count_history  = 0; ?>
            @endif
        @endforeach
    </tr>
    <tr>

        <td class="waterUsedHisHead td_history text-center">จำนวนหน่วย</td>
        <php $oweSum = 0;  ?>
        @foreach ($item['inv_history'] as $history)
            <td class="text-center td_history">
                <php 
                    $count_history++;
                    $history_diff = $history['currentmeter'] - $history['lastmeter'];
                    $history_status = $history['status'] =='owe' ? '(ค้าง)' : '';
                    if($history['status'] == 'owe'){
                        $oweSum += $history_diff * 8;
                        if($history_diff == 0){
                            $oweSum += 10;
                        }
                    }
                    echo $history_diff;
                ?>
                <span style="color: red; font-size:0.70rem;"> {{ $history_status }}</span>
            </td>
            @if (collect($item['inv_history'])->count() == $count_history)
                <td class="text-center td_history">
                    {{$oweSum}}</td>
                <php $count_history  = 0; $oweSum = 0; ?>
            @endif
        @endforeach
    </tr>
    
</table>
<table width="100%">
    <tr>
        <td  class="text-center border-left-none border-right-none 
            {{$a== 3 ? 'pt-0' :'pt-1'}}">
            <php
                $currentMonthExp = explode( '-', $item['invoice_period']['inv_period_name'])[0];
                $currentYear = date('Y') + 543;
                $monthThai =  $fnc->fullThaiMonth($currentMonthExp);
                $paidBeforeDate = 30;
            ?>
            โปรดชำระเงินภายในวันที่  
            <span id="expiredDate" class="h4 text-danger">{{ $paidBeforeDate.' '.$monthThai. ' ' .$currentYear}}</span>
        </td>
    </tr>
    <tr>
        <td  class="text-center m_detail border-left-none border-right-none border-bottom-none text-danger">
            ***หากเกินกำหนดจะถูกระงับการใช้น้ำ และจะจ่ายน้ำใหม่หลังจากได้รับการชำระหนี้ค้างทั้งหมดพร้อมค่าธรรมเนียมการใช้น้ำแล้ว
        </td>
    </tr>
</table> --}}

