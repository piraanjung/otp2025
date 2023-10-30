<?php
use App\Http\Controllers\Api\FunctionsController;
$fnc = new FunctionsController();
?>


@if ($a == 1)
    <table  class="mytable table_row1">

@elseif ($a == 2) 
    <table  class="mytable table_row2">
@else
    <table  class="mytable table_row3">
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
            @if (collect($item['user_profile'])->isEmpty())
            {{ dd($item) }}
            @endif
             {{ $item['user_profile']['name'] }}
        </td>
    </tr>
    <tr>
        <td rowspan="2">&nbsp;</td>
        <td colspan="3"  class="border-bottom-none address "> 
            &nbsp;
            {{ $item['user_profile']['address'] }} หมู่ {{ $item['user_profile']['zone_id'] }} ต.{{$setting_tambon_infos['organize_tambon']}} 
        </td>
    </tr>
    <tr>
        {{-- <td colspan="1">&nbsp;</td> --}}
        <td colspan="3" class="border-top-none address ">
            &nbsp;
            อ.{{$setting_tambon_infos['organize_district']}} 
            จ.{{$setting_tambon_infos['organize_province']}} 
            {{$setting_tambon_infos['organize_zipcode']}}
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
            {{ QrCode::size(40)->generate($item['id']) }}
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
<table   class="mt-2 mytable2">
    <tr>
        <td class="waterUsedHisHead td_history text-center" width="12%" rowspan="2">&nbsp;<br>&nbsp;</td>
        <td class="waterUsedHisHead td_history text-center" width="12%">&nbsp;</td>
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
                    $history_diff_plus = $history_diff == 0 ? "(+10)" : "(x8)";
                    $history_status = $history['status'] =='owe'   ? '(ค้าง)' : '';
                    if($history['status'] == 'owe'){
                        $oweSum += $history_diff * 8;
                        if($history_diff == 0){
                            $oweSum += 10;
                            $history_diff_plus = "(+10)";
                        }
                    }
                    // echo $history_diff;
                ?>
                @if ($history['status'] =='owe')
                    <span style="color: red"> {{ $history_diff }}</span>
                    <span style='color: red; font-size:0.60rem;'>{{ $history_diff_plus }}</span>
                @else
                    {{ $history_diff }}
                    <span style='font-size:0.60rem;'>{{ $history_diff_plus }}</span>

                @endif

                {{-- <span style="color: red; font-size:0.70rem;"> {{ $history_status }}</span> --}}
            </td>
           
        @endforeach
        @for ($i = collect($item['inv_history'])->count(); $i < 5; $i++)
            <td class="text-center td_history" width="12.1%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
        @endfor
        <td class="text-center td_history" width="12.1%"> 
            <?php 
                echo $oweSum > 0  ? '<span style="color: red">'.$oweSum.'<span>' : $oweSum;
            ?>
            {{-- {{$oweSum}} --}}
        </td>
        <?php $count_history  = 0; $oweSum = 0; ?>
    </tr>
    
</table>
<table class="mytable2 mt-1">
    <tr>
        <td  class="text-center border-left-none border-right-none 
            {{$a== 3 ? 'pt-2' :'pt-2'}}">
            <?php
                $currentMonthExpTemp = explode("-",$invoice_expired_next30day);//explode( '-', $item['invoice_period']['inv_period_name'])[0];
                $currentYear = $currentMonthExpTemp[0]+543;//date('Y') + 543;
                $monthThai =  $fnc->fullThaiMonth($currentMonthExpTemp[1]);
                $date = $currentMonthExpTemp[2][0] == 0 ? str_split($currentMonthExpTemp[2])[1] : $currentMonthExpTemp[2];
            ?>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;   
            &nbsp;&nbsp; &nbsp; &nbsp; &nbsp;&nbsp;&nbsp; &nbsp; &nbsp; &nbsp;&nbsp;&nbsp; &nbsp;

            <span id="expiredDate" class="h4" >
                {{-- {{$setting_invoice_expired_th}} --}}
                {{ $date.' '.$monthThai. ' ' .$currentYear}}
            </span>
        </td>
    </tr>
    <tr>
        <td  class="text-center m_detail border-left-none border-right-none border-bottom-none text-danger">
            &nbsp;
        </td>
    </tr>
</table>





