<?php
$updated_date = date_format($invoicesPaidForPrint[0]->updated_at, 'Y-m-d');
$receipt_th_date = App\Http\Controllers\Api\FunctionsController::engDateToThaiDateFormat($updated_date);
$today = App\Http\Controllers\Api\FunctionsController::engDateToThaiDateFormat(date('Y-m-d'));
?>

<style>
    .t {
        margin-left: 0px !important;
        /* border:  1px solid plum */
    }
</style>
<table border="0" width="94.8%" style="margin-top:8px !important;" class="t">
    <tr>
        <td colspan="5" class="text-left head pt-2 pb-2">&nbsp;</td>
        <td colspan="2" class="text-center head2 pt-2 pb-2">&nbsp;</td>
    </tr>
    <tr>
        <td colspan="5" class="text-left text-primary address">
            &nbsp;
            <div class="address">&nbsp;</div>
        </td>
        <td colspan="2" class="text-center pt-0 pb-0">
            <div>{{ $receipt_th_date }}</div>
            @if ($receipt_th_date < $today)
                <div style="font-size: 0.8rem;"> ( ปริ้น: {{ $today }} ) </div>
            @endif
        </td>
    </tr>
    <tr>
        <td class="waterUsedHisHead" style="width:80px;">&nbsp;</td>
        <td colspan="4">
            {{ $invoicesPaidForPrint[0]->usermeterinfos->user->firstname . ' ' . $invoicesPaidForPrint[0]->usermeterinfos->user->lastname }}
        </td>

        <td colspan="2" rowspan="2" class="text-center border-right-none border-bottom-none">
            &nbsp;{{-- <img src="{{asset('/img/hslogo.jpg')}}" width="70"> --}}
        </td>
    </tr>
    <tr>
        <td class="waterUsedHisHead">&nbsp;</td>
        <td colspan="4" class="address pt-3">
            {{ $invoicesPaidForPrint[0]->usermeterinfos->user->address }}

            {{ $invoicesPaidForPrint[0]->usermeterinfos->user->user_zone->zone_name }}
            ต.{{ $invoicesPaidForPrint[0]->usermeterinfos->user->user_tambon->tambon_name }}
            อ.{{ $invoicesPaidForPrint[0]->usermeterinfos->user->user_district->district_name }}
            จ.{{ $invoicesPaidForPrint[0]->usermeterinfos->user->user_province->province_name }}
            {{ $invoicesPaidForPrint[0]->usermeterinfos->user->user_tambon->zipcode }}
        </td>
    </tr>
</table>
<table border="0" width="94.8%" class="t" style="margin-top:1.4rem !important">
    <tr>

        <td width="20%" class="waterUsedHisHead"> &nbsp;</td>
        <td width="30%" class="text-center pl-1"> {{ $invoicesPaidForPrint[0]->id }}</td>
        <td width="20%" class="waterUsedHisHead"> &nbsp;</td>
        <td width="30%" class="text-center"> {{ $invoicesPaidForPrint[0]->usermeterinfos->meternumber }}</td>
    </tr>
</table>
<table border="0" width="94.8%" class="t" style="margin-top:1.1rem !important">

    <tr>
        <td class="waterUsedHisHead text-center" width="12%">&nbsp;<br>&nbsp;</td>
        <td class="waterUsedHisHead text-center" width="20%">&nbsp;<br>&nbsp;</td>
        <td class="waterUsedHisHead text-center" width="10%">&nbsp;<br>&nbsp;</td>
        <td class="waterUsedHisHead text-center" width="10%">&nbsp;<br>&nbsp;</td>
        <td class="waterUsedHisHead text-center" width="11%">&nbsp;<br>&nbsp;</td>
        <td class="waterUsedHisHead text-center" width="14%">&nbsp;<br>&nbsp;</td>
        <td class="waterUsedHisHead text-center" width="10%">&nbsp;<br>&nbsp;</td>
        <td class="waterUsedHisHead text-center" width="13%">&nbsp;<br>&nbsp;</td>
    </tr>
    <?php $total = 0;
    $reserveMeter = 0; ?>
    @foreach ($invoicesPaidForPrint as $key => $item)
        <tr>
            <td class="text-right">
                <?php
                $exp = explode('-', $item->invoice_period->inv_p_name);
                $year = date('y') + 43;
                echo $exp[0] . '-' . $year;
                ?>
                {{-- {{ $item->inv_period_name }} --}}
            </td>
            <td class="text-right">
                <?php
                $date = Str::substr($item->created_at, 0, 10);
                ?>
                {{ App\Http\Controllers\Api\FunctionsController::engDateToThaiDateFormat($date) }}
            </td>
            <td class="text-right">{{ number_format($item->currentmeter) }}</td>
            <td class="text-right">{{ number_format($item->lastmeter) }}</td>
            <td class="text-right number">
                <?php
                $waterUsedNet = $item->currentmeter - $item->lastmeter;
                $reserveMeter = $waterUsedNet == 0 ? 10 : 0;
                $used_price = $waterUsedNet * 8;
                $paid = $used_price + $reserveMeter;
                $total += $paid;
                ?>
                <span id="unit_used">{{ number_format($waterUsedNet) }}</span>
            </td>
            <td class="text-right number">{{ $used_price }}</td>
            <td class="text-right number">{{ $reserveMeter }}</td>
            <td class="text-right t2-pr-3">{{ number_format($paid) }}</td>
        </tr>
    @endforeach

    @if (collect($invoicesPaidForPrint)->count() == 1)
        <tr>
            <td colspan="7">&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td colspan="7" class="border-top-none">&nbsp;</td>
            <td class="border-top-none">&nbsp;</td>
        </tr>
        <tr>
            <td colspan="7" class="border-top-none">&nbsp;</td>
            <td class="border-top-none">&nbsp;</td>
        </tr>
        <tr>
            <td colspan="7" class="border-top-none">&nbsp;</td>
            <td class="border-top-none">&nbsp;</td>
        </tr>
        <tr>
            <td colspan="7" class="border-top-none">&nbsp;</td>
            <td class="border-top-none">&nbsp;</td>
        </tr>
    @elseif(collect($invoicesPaidForPrint)->count() == 2)
        <tr>
            <td colspan="7">&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td colspan="7" class="border-top-none">&nbsp;</td>
            <td class="border-top-none">&nbsp;</td>
        </tr>
        <tr>
            <td colspan="7" class="border-top-none">&nbsp;</td>
            <td class="border-top-none">&nbsp;</td>
        </tr>
        <tr>
            <td colspan="7" class="border-top-none">&nbsp;</td>
            <td class="border-top-none">&nbsp;</td>
        </tr>
    @elseif(collect($invoicesPaidForPrint)->count() == 3)
        <tr>
            <td colspan="7">&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td colspan="7" class="border-top-none">&nbsp;</td>
            <td class="border-top-none">&nbsp;</td>
        </tr>
        <tr>
            <td colspan="7" class="border-top-none">&nbsp;</td>
            <td class="border-top-none">&nbsp;</td>
        </tr>
    @elseif(collect($invoicesPaidForPrint)->count() == 4)
        <tr>
            <td colspan="7">&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td colspan="7" class="border-top-none">&nbsp;</td>
            <td class="border-top-none">&nbsp;</td>
        </tr>
    @elseif(collect($invoicesPaidForPrint)->count() == 5)
        <tr>
            <td colspan="7">&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
    @endif
    <tr>
        <td colspan="4" rowspan="3" class="border-bottom-none border-left-none text-center">
            {{ QrCode::size(70)->generate($newId) }}
            <div class='mt-0'>เลขใบเสร็จรับเงิน: {{ $newId }} </div>
        </td>
        <td class="" colspan="3">&nbsp;</td>
        <td class="text-right t2-pr-3">
            {{ number_format($total,2) }}
        </td>
    </tr>
    <tr>
        <td colspan="3">&nbsp;</td>
        <td class="text-right t2-pr-3">
            <?php
            $vat = number_format(($total * 0.07),2);
            $total = $total + $vat;
            ?>
            {{ $vat }}
        </td>
    </tr>
    <tr>
        <td colspan="3">&nbsp;</td>
        <td class="text-right t2-pr-3 head2">
            {{ number_format($total,2) }}
        </td>
    </tr>


</table>
<table border="0" width="94.8%" class="mt-4 t">
    <tr>
        <td colspan="7" class="text-center border-left-none border-right-none pt-4">
            {{ $invoicesPaidForPrint[0]->accounting->user_payee->firstname . ' ' . $invoicesPaidForPrint[0]->accounting->user_payee->lastname }}
            <br>&nbsp;
        </td>
    </tr>
</table>
