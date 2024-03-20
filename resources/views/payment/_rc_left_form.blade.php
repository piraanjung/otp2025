<?php
use App\Http\Controllers\Api\FunctionsController;
$fnc = new FunctionsController();
// dd($invoicesPaidForPrint);
$exp = explode(' ', $invoicesPaidForPrint[0]->acc_transactions->updated_at);
$receipt_th_date = $fnc->engDateToThaiDateFormat($exp[0]);
?>


<table border="0" width="95%" style="margin-top:8px !important;" class="t2">
    <tr>
        <td colspan="7" class="text-center head pt-2 pb-2 header-bg">
            {{-- ต้นขั้วใบเสร็จรับเงิน/ใบกำกับภาษี --}}
            &nbsp;
            <div class="tax_number header-bg">
                {{-- เลขที่ผู้เสียภาษี 0994000352620 --}}
                &nbsp;
            </div>
        </td>
        {{-- <td colspan="1" class="text-center header-bg head2 pt-2 pb-2 border-right-none inv_number_text">เลขที่</td> --}}
        <td colspan="3" class="text-center head2 pt-2 pb-2">
            <div class="tax_number header-bg pt-1">
                {{-- เลขที่ --}}
                &nbsp;
            </div>
            <div class="text-danger" style="font-size: 1.4rem; font-weight:bolder">
                &nbsp;
                {{-- 00000001 --}}
            </div>
        </td>
    </tr>
    <tr class="ref">
        <td width="10%"></td>
        <td width="10%"></td>
        <td width="10%"></td>
        <td width="10%"></td>
        <td width="10%"></td>
        <td width="10%"></td>
        <td width="10%"></td>
        <td width="10%"></td>
        <td width="10%"></td>
        <td width="10%"></td>

    </tr>
    <tr>
        <td colspan="7"  class="text-left text-primary row2">
            {{-- เทศบาลตำบลห้องแซง --}}
            &nbsp;
            <div class="address2">
                {{-- 222 หมู่ 17 ตำบลห้องแซง อำเภอเลิงนกทา จังหวัดยโสธร 35120 --}}
                &nbsp;
            </div>
        </td>
        <td colspan="3" class="text-center pt-0 pb-0 row2">

            <div>{{ $receipt_th_date }}</div>
            @if ($receipt_th_date < $fnc->engDateToThaiDateFormat(date('Y-m-d')))
                <div style="font-size: 0.8rem;"> ( ปริ้น: {{ $fnc->engDateToThaiDateFormat(date('Y-m-d')) }} ) </div>
            @endif
        </td>
    </tr>
    <tr>
        <td colspan="2" class="waterUsedHisHead pl-2 header-bg">
            {{-- ชื่อผู้ใช้น้ำ --}}
            {{-- &nbsp; --}}
        </td>
        <td colspan="5">
            {{-- {{ dd($invoicesPaidForPrint[0]->usermeterinfos->user_profile->name) }} --}}
            {{ $invoicesPaidForPrint[0]->usermeterinfos->user->prefix."".$invoicesPaidForPrint[0]->usermeterinfos->user->name }}
        </td>

        <td colspan="3" rowspan="2" class="text-center border-right-none border-bottom-none">
            &nbsp;
            {{-- <img src="{{ asset('/logo/logo.png') }}" width="100"> --}}
        </td>
    </tr>

    <tr>
        <td colspan="2" class="waterUsedHisHead pl-2 header-bg">
            {{-- ที่อยู่ --}}
            &nbsp;
        </td>
        <td colspan="5" class="address pt-1" style="height: 3rem !important">
            {{ $invoicesPaidForPrint[0]->usermeterinfos->user->address }}

            {{ $invoicesPaidForPrint[0]->usermeterinfos->user->user_zone->user_zone_name }}
            ต.{{ $invoicesPaidForPrint[0]->usermeterinfos->user->user_tambon->tambon_name }}
            อ.{{ $invoicesPaidForPrint[0]->usermeterinfos->user->user_district->district_name }}
            จ.{{ $invoicesPaidForPrint[0]->usermeterinfos->user->user_province->province_name }}
            {{ $invoicesPaidForPrint[0]->usermeterinfos->user->user_tambon->zipcode }}
        </td>

    </tr>

</table>
<table border="0" width="95%" class="t2" style="margin-top:0.6rem !important">
    <tr>

        <td width="20%" class="waterUsedHisHead pl-2 header-bg">
             {{-- เลขผู้ใช้มิเตอร์ --}}
             &nbsp;
            </td>
        <td width="30%" class="text-center pl-1"> {{ $invoicesPaidForPrint[0]->usermeterinfos->user_id_fk }}</td>
        <td width="20%" class="waterUsedHisHead pl-2 header-bg">
            {{-- เลขมิเตอร์ --}}
            &nbsp;
        </td>
        <td width="30%" class="text-center">
            {{ $fnc::createInvoiceNumberString($invoicesPaidForPrint[0]->usermeterinfos->meternumber) }} /
            <span style="font-size: 0.9rem">{{$fnc::createNumberString( $invoicesPaidForPrint[0]->accounts_id_fk ,"B")}}</span>
        </td>
    </tr>
</table>

<table border="0" width="95%" id="tabwater_info" class="t2" style="margin-top:0.7rem !important">

    <tr>
        <td class="waterUsedHisHead2 header-bg text-center" width="10%">
            <div>
                {{-- ประจำ --}}
                &nbsp;
            </div>
            <div>
                {{-- เดือน --}}
                &nbsp;
            </div>
        </td>
        <td class="waterUsedHisHead2 header-bg text-center" width="10%">
            <div>
                {{-- วันที่ --}}
                &nbsp;
            </div>
            <div>
                &nbsp;
                {{-- จดมาตร --}}
            </div>
        </td>
        <td class="waterUsedHisHead2 header-bg text-center" width="9%">
            <div>
                {{-- มิเตอร์ --}}
                &nbsp;
            </div>
            <div>
                &nbsp;
                {{-- ปัจจุบัน --}}
            </div>
            <div><sup>
                &nbsp; {{-- (หน่วย) --}}
            </sup></div>
        </td>
        <td class="waterUsedHisHead2 header-bg text-center" width="10%">
            <div>
                &nbsp; {{-- มิเตอร์ --}}
            </div>
            <div>
                &nbsp;    {{-- ครั้งก่อน --}}
            </div>
            <div><sup>
                &nbsp; {{-- (หน่วย) --}}
            </sup></div>
        </td>
        <td class="waterUsedHisHead2 header-bg text-center" width="8%">
            <div>
                &nbsp;{{-- จำนวน --}}
            </div>
            <div>
                &nbsp; {{-- น้ำที่ใช้ --}}
            </div>
            <div><sup>
                &nbsp; {{-- (หน่วย) --}}
            </sup></div>
        </td>
        <td class="waterUsedHisHead2 header-bg text-center" width="10%">
            <div>
                &nbsp;    {{-- ค่าน้ำ --}}
            </div>
            <div>
                &nbsp;   {{-- ประปา --}}
            </div>
            <div><sup>
                &nbsp;  {{-- (บาท) --}}
            </sup></div>
        </td>
        <td class="waterUsedHisHead2 header-bg text-center" width="10%">
            <div>
                &nbsp; {{-- ค่ารักษา --}}
            </div>
            <div>
                &nbsp; {{-- มิเตอร์ --}}
            </div>
            <div><sup>
                &nbsp; {{-- (บาท) --}}
            </sup></div>
        </td>
        <td class="waterUsedHisHead2 header-bg text-center" width="9%">
            <div>
                &nbsp;  {{-- Vat 7% --}}
            </div>
            <div><sup>
                &nbsp;  {{-- (บาท) --}}
            </sup></div>
        </td>
        <td class="waterUsedHisHead2 header-bg text-center" width="13%">
            <div>
                &nbsp;  {{-- จำนวนเงิน --}}
            </div>
            <div><sup>
                &nbsp;  {{-- (บาท) --}}
            </sup></div>
        </td>
    </tr>
    <?php $total = 0;
    $reserveMeter = 0;
    $totalVat7 = 0;
    ?>
    {{-- @for ($i = collect($invoicesPaidForPrint)->count(); $i <= 6; $i++) --}}

{{-- {{ dd($invoicesPaidForPrint) }} --}}
    @foreach ($invoicesPaidForPrint as $key => $item)
        <tr id="info">
            <td class="text-center">
                <?php
                $exp = explode('-', $item->invoice_period->inv_period_name);
                $year = date('y') + 43;
                echo $exp[0] . '-' . $year;
                ?>
                {{-- {{ $item->inv_period_name }} --}}
            </td>
            <td class="text-center">
                <?php
                $date = Str::substr($item->created_at, 0, 10);
                ?>
                {{ $fnc->engDateToThaiDateFormat($date) }}
            </td>
            <td class="text-right">{{ number_format($item->currentmeter) }}</td>
            <td class="text-right">{{ number_format($item->lastmeter) }}</td>
            <td class="text-right number">
                <?php
                $waterUsedNet = $item->currentmeter - $item->lastmeter;
                $reserveMeter = $waterUsedNet == 0 ? 10 : 0;
                $used_price = $waterUsedNet * 8;
                $paid = number_format($used_price + $reserveMeter, 2);
                $vat7 = number_format($paid * 0.07, 2);
                $total += $paid;
                $totalVat7 += $vat7;
                ?>
                <span id="unit_used">{{ number_format($waterUsedNet) }}</span>
            </td>
            <td class="text-right number">{{ $used_price }}</td>
            <td class="text-right number">{{ $reserveMeter }}</td>
            <td class="text-right number">{{ $vat7 }}</td>
            <td class="text-right number t2-pr-3">{{ number_format($paid + $vat7, 2) }}</td>
        </tr>
    @endforeach
    {{-- @endfor --}}

    {{-- @for ($i = collect($invoicesPaidForPrint)->count(); $i < 6; $i++) --}}
    @for ($i = collect($invoicesPaidForPrint)->count(); $i < 6; $i++)
        <tr id="{{  $i == 5 ? "" : 'info' }}">
            @for ($j = 0; $j < 8; $j++)
                <td class="">&nbsp;</td>
            @endfor
        </tr>
    @endfor
    <tr>
        <td colspan="4" class="text-center border-left-none border-bottom-none" rowspan="3">
           &nbsp; {{-- งานกิจการประปา --}}
            {{-- <div >โทร. 08-810-0543-5</div> --}}
            {{-- <div style="padding-left: .9rem"> 045-777116</div> --}}
        </td>
        <td class="pl-2 summary_text" colspan="4">
            &nbsp; {{-- รวมเป็นเงิน <span class="baht"> (บาท)</span> --}}
        </td>
        <td class="text-right t2-pr-3 number">
            {{ number_format($total, 2) }}
        </td>
    </tr>
    <tr>
        <td class="pl-2 summary_text" colspan="4">
            &nbsp;   {{-- ภาษีมูลค่าเพิ่ม 7% <span class="baht"> (บาท)</span> --}}
        </td>
        <td class="text-right t2-pr-3 number">
            {{ number_format($totalVat7, 2) }}
        </td>
    </tr>
    <tr>
        <td class="pl-2 pt-0 summary_text" colspan="5">
            <div class="row" >
                <div class="col-8 pt-1">
                    <span style="font-size: 0.95rem">
                        &nbsp;  {{-- รวมที่ต้องชำระทั้งสิ้น</span>
                     <span class="baht"> (บาท)</span> --}}
                </div>
                <div class="col-4 text-right t2-pr-3 header-bg">
                    <h5>{{ number_format($total + $totalVat7, 2) }}</h5>
                </div>
            </div>
            <div class="text-right pt-2" style="font-size: 0.8rem">
            ({{ App\Http\Controllers\Api\FunctionsController::convertAmountToLetter(number_format($total + $totalVat7, 2))}})
            </div>

        </td>
    </tr>
</table>
<table border="0" width="95%" class="mt-1 t">
    <tr>
        <td colspan="7" class="text-center border-left-none border-right-none pt-4">
           {{-- (ลงชื่อ) --}}
           {{-- <span style="text-decoration: underline  dotted black;"> --}}
            &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
           {{ $invoicesPaidForPrint[0]->acc_transactions->cashier_info->firstname." ".$invoicesPaidForPrint[0]->acc_transactions->cashier_info->lastname }}
           &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        {{-- </span>ผู้รับเงิน --}}
            <br>&nbsp;
        </td>
    </tr>
</table>
