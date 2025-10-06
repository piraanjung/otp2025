<?php
use App\Http\Controllers\Api\FunctionsController;
$fnc = new FunctionsController();
?>

@if ($a == 1)
    <table  class="staff_mytable table_row1">

@elseif ($a == 2)
    <table   class="staff_mytable table_row2">
@else
    <table  class="staff_mytable table_row3">
@endif
    <tr>
        <td colspan="4"  class="text-center waterUsedHisHead h5 pt-2 pb-2">&nbsp;</td>
    </tr>
    <tr>
        <td class="td_staff_col1" colspan="2">&nbsp;</td>
        <td class="td_staff_col2" colspan="2">{{ $item['invoice_period']['inv_p_name'] }}</td>
    </tr>
    <tr>
        <td class="td_staff_col1" colspan="2">&nbsp;</td>
        <td class="td_staff_col2" colspan="2">{{ $item['id'] }}</td>
    </tr>
    <tr>
        <td class="td_staff_col1" colspan="2">&nbsp;</td>
        <td class="td_staff_col2" colspan="2">{{ $item['usermeterinfos']['meternumber'] }}</td>
    </tr>
    <tr>
        <td class="td_staff_col1" colspan="2">&nbsp;</td>
        <td class="td_staff_col2" colspan="2">{{ $item['usermeterinfos']['subzone']['subzone_name'] }}</td>
    </tr>
    <tr>
        <td class="td_staff_col1 text-left" colspan="4">&nbsp;</td>
    </tr>
    <tr>
        <td colspan="4"  class="text-center"> {{ $item['usermeterinfos']['user']['firstname']." ".$item['usermeterinfos']['user']['lastname'] }}</td>
    </tr>
    <tr>
        <td class="waterUsedHisHead text-center" colspan="4">&nbsp;</td>
    </tr>
    <tr>
        <td colspan="4" class="tex-left pl-4 pt-2">
            <div>{{ $item['usermeterinfos']['user']['address'] }} หมู่ {{ $item['usermeterinfos']['user']['zone_id'] }}</div>
            <div style="font-size: 8pt !important">
                ต.{{$setting_tambon_infos['tambon']}}
                อ.{{$setting_tambon_infos['district']}}
            </div>
            <div style="font-size: 8pt !important">
                จ.{{$setting_tambon_infos['province']}}
                {{-- {{$setting_tambon_infos['zipcode']}} --}}
            </div>
        </td>
    </tr>
    <tr>
        <td colspan="4" class="text-center">
            <div class="">
                {{ QrCode::size(40)->generate($item['id']) }}
            </div>
            <div class='mt-0' style="font-size: 0.9rem">เลขใบแจ้งหนี้:  {{ $item['id'] }} </div>
        </td>
    </tr>
    <tr>
        <td colspan="4" class="waterUsedHisHead text-center">
            &nbsp;
        </td>
    </tr>
    <tr>
        <th colspan="4" class="text-center h3 pt-1">
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
            {{ number_format($total)  }}
        </th>
    </tr>
</table>

