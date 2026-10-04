<?php
use App\Http\Controllers\Api\FunctionsController;
$fnc = new FunctionsController();
//dd($invoicesPaidForPrint);

$exp = explode(' ', $invoicesPaidForPrint[0]->tw_acc_transactions->updated_at);
$receipt_th_date = $fnc->engDateToThaiDateFormat($exp[0]);
?>


<table  width="100%" style="{{ $page == 1 ? "margin-top:13px !important;"  : "margin-top:10px !important;" }}">
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
            <div class="tax_number pt-1">
                {{-- เลขที่ --}}
                &nbsp;
            </div>
            <div class="header-bg" style="font-size: 1.4rem; font-weight:bolder">
                &nbsp;
                 {{-- 00000001 --}}
            </div>
        </td>
    </tr>
    <tr class="ref">
        <td width="10%" class=" border-0"></td>
        <td width="10%" class=" border-0"></td>
        <td width="10%" class=" border-0"></td>
        <td width="10%" class=" border-0"></td>
        <td width="10%" class=" border-0"></td>
        <td width="10%" class=" border-0"></td>
        <td width="10%" class=" border-0"></td>
        <td width="10%" class=" border-0"></td>
        <td width="10%" class=" border-0"></td>
        <td width="10%" class=" border-0"></td>

    </tr>
    <tr>
        <td colspan="7" class="text-left text-primary row2">
            {{-- เทศบาลตำบลห้องแซง --}}
            &nbsp;
            <div class="address2">
                {{-- 222 หมู่ 17 ตำบลห้องแซง อำเภอเลิงนกทา จังหวัดยโสธร 35120 --}}
                &nbsp;
            </div>
        </td>
        <td colspan="3" class="text-center pt-4 pb-0 row2">
                {{ $receipt_th_date }}

            @if ($receipt_th_date < $fnc->engDateToThaiDateFormat(date('Y-m-d')))
                <div style="font-size: 0.7rem;"> ( ปริ้น: {{ $fnc->engDateToThaiDateFormat(date('Y-m-d')) }} ) </div>
            @endif
              &nbsp;
        </td>
    </tr>
    {{-- <tr>
        <td colspan="10" style=" padding:0px !important">  &nbsp;</td>
    </tr> --}}
    <tr>
        <td colspan="2" class="waterUsedHisHead pl-2 header-bg">
            {{-- ชื่อผู้ใช้น้ำ --}}
            &nbsp;
        </td>
        <td colspan="5" class="{{ $page ==1 ? 'pl-5' : 'pl-4' }}"
            style="height: 3rem !important;">
            {{-- &nbsp; --}}
          <span class="{{ $page ==2 ? 'pl-1' : '' }}">
             {{ $invoicesPaidForPrint[0]->tw_meter_infos->user->prefix . '' . $invoicesPaidForPrint[0]->tw_meter_infos->user->firstname . ' ' . $invoicesPaidForPrint[0]->tw_meter_infos->user->lastname }}
        </span>
            </td>

        <td colspan="3" rowspan="2" class="text-center border-0" >
            &nbsp;
            {{-- <img src="{{ asset('/logo/hs_logo.jpg') }}" width="100"> --}}
        </td>
    </tr>

    <tr>
        <td colspan="2" class="waterUsedHisHead pl-3 header-bg">
            {{-- ที่อยู่ --}}
            &nbsp;
        </td>
        <td colspan="5" class="address pt-2 {{ $page ==1 ? 'pl-5' : 'pl-4' }}"
         style="height: 3rem !important; padding-top: 15px !important;">
          {{-- &nbsp;
        <br> --}}
        {{-- {{ dd($invoicesPaidForPrint[0]) }} --}}
        <span class="{{ $page ==2 ? 'pl-1' : '' }}">
            {{ $invoicesPaidForPrint[0]->tw_meter_infos->meter_address }}
            {{ $invoicesPaidForPrint[0]->tw_meter_infos->undertake_zone->zone_name }}
            ต.{{ $invoicesPaidForPrint[0]->tw_meter_infos->user->user_tambon->tambon_name }}
            อ.{{ $invoicesPaidForPrint[0]->tw_meter_infos->user->user_district->district_name }}
            จ.{{ $invoicesPaidForPrint[0]->tw_meter_infos->user->user_province->province_name }}
            {{ $invoicesPaidForPrint[0]->tw_meter_infos->user->user_tambon->zipcode }}
        </span>
        </td>

    </tr>

</table>

<table border="0" width="100%" style="margin-top:0px !important">
    <tr>

        <td width="30%" class="waterUsedHisHead pl-2 header-bg">
            {{-- เลขที่ผู้เสียภาษีผู้ใช้น้ำ --}}
            &nbsp;
        </td>
        <td width="25%" class="text-left pl-2 pt-3">
            {{-- {{ $invoicesPaidForPrint[0]->tw_meter_infos->user->id_card }} --}}
            &nbsp;

        </td>
        <td width="14%" class="border-0">&nbsp;</td>
        <td width="8%" class="waterUsedHisHead pl-2 header-bg">
            {{-- เลขมิเตอร์ --}}
            &nbsp;
        </td>
        <td width="20%" class="text-left pt-3 {{ $page == 1 ? 'pl-3' : 'pl-3' }}">
            {{-- &nbsp; --}}

            {{ $fnc::createInvoiceNumberString($invoicesPaidForPrint[0]->tw_meter_infos->meter_id) }}
        </td>
    </tr>
</table>

<table border="0" width="{{ $page == 1  ? '97%' : '99%'}}" id="tabwater_info"
style="margin-top:10px !important;{{ $page == 1  ? 'margin-left:0.4rem !important' : 'margin-left:-0.5rem !important'}}"
>

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
            {{-- <div> --}}
                &nbsp;
                {{-- จดมาตร --}}
            </div>
        </td>
        <td class="waterUsedHisHead2 header-bg text-center" width="9%">
            <div>
                {{-- มิเตอร์ --}}
                &nbsp;
            </div>
            {{-- <div> --}}
                &nbsp;
                {{-- ปัจจุบัน --}}
            </div>
            <div><sup>
                    &nbsp;
                    {{-- (หน่วย) --}}
                </sup></div>
        </td>
        <td class="waterUsedHisHead2 header-bg text-center" width="10%">
            <div>
                &nbsp;
                 {{-- มิเตอร์ --}}
            </div>
            <div>
                &nbsp;
                 {{-- ครั้งก่อน --}}
            </div>
            <div><sup>
                    &nbsp;
                     {{-- (หน่วย) --}}
                </sup></div>
        </td>
        <td class="waterUsedHisHead2 header-bg text-center" width="8%">
            <div>
                &nbsp;
                {{-- จำนวน --}}
            </div>
            <div>
                &nbsp;
                 {{-- น้ำที่ใช้ --}}
            </div>
            <div><sup>
                    &nbsp;
                     {{-- (หน่วย) --}}
                </sup></div>
        </td>
        <td class="waterUsedHisHead2 header-bg text-center" width="10%">
            <div>
                &nbsp;
                {{-- ค่าน้ำ --}}
            </div>
            <div>
                &nbsp;
                {{-- ประปา --}}
            </div>
            <div><sup>
                    &nbsp;
                    {{-- (บาท) --}}
                </sup></div>
        </td>
        <td class="waterUsedHisHead2 header-bg text-center" width="10%">
            <div>
                &nbsp;
                {{-- ค่ารักษา --}}
            </div>
            <div>
                &nbsp;
                {{-- มิเตอร์ --}}
            </div>
            <div><sup>
                    &nbsp;
                    {{-- (บาท) --}}
                </sup></div>
        </td>
        <td class="waterUsedHisHead2 header-bg text-center" width="9%">
            <div>
                &nbsp;
                 {{-- Vat 7% --}}
            </div>
            <div><sup>
                    &nbsp;
                     {{-- (บาท) --}}
                </sup></div>
        </td>
        <td class="waterUsedHisHead2 header-bg text-center" width="13%">
            <div>
                &nbsp;
                 {{-- จำนวนเงิน --}}
            </div>
            <div><sup>
                    &nbsp;
                     {{-- (บาท) --}}
                </sup></div>
        </td>
    </tr>
    <?php $total = 0;
    $reserveMeter = 0;
    $totalVat7 = 0;
    ?>

    @if (strlen($invoicesPaidForPrint[0]['currentmeter']) >= 4)
        <style>
            .inv_p {
                font-size: 12px !important
            }

            .inf {
                font-size: 12px !important
            }
        </style>
    @endif
    <tr>
        <td colspan="9" style="padding:2px !important;"></td>
    </tr>
    @foreach ($invoicesPaidForPrint as $key => $item)
        <tr id="{{ collect($invoicesPaidForPrint)->count() > 6 ? 'info_over6' : 'info' }}">
            <td class="text-right inv_p">
                <?php
                $exp = explode(' ', $item->invoice_period->inv_p_name);
                // dd($exp);
                $year = substr($exp[1],2) ;
                echo $fnc->fullThaiMonth(trim($exp[0]))."/".$year;
                ?>
                {{-- &nbsp; --}}
            </td>
            <td class="text-center inf">
                <?php
                $date = Str::substr($item->created_at, 0, 10);
                $dateArray = explode("/", $fnc->engDateToThaiDateFormat($date));
                // {{ dd($dateArray); }}
                echo $dateArray[0]."/".$dateArray[1]."/".Str::substr($dateArray[2],2,2);
                ?>
                {{-- &nbsp; --}}
            </td>

            <td class="text-right inf">
                <?php echo number_format($item['currentmeter']); ?>
                {{-- &nbsp; --}}
            </td>
            <td class="text-right inf">
                <?php echo number_format($item['lastmeter']); ?>
                {{-- &nbsp; --}}
            </td>
            <td class="text-right number inf">
                <?php
                $waterUsedNet = intval($item['currentmeter']) - intval($item['lastmeter']);
                $reserveMeter = $waterUsedNet == 0 ? 10 : 0;
                $used_price = $waterUsedNet * 8;
                $paid = $used_price + $reserveMeter;
                $vat7 = $paid * 0.07;
                $total += $paid;
                $totalVat7 += $vat7;
                ?>
                <span id="unit_used">
                    {{ number_format($waterUsedNet) }}
                    {{-- &nbsp; --}}
                </span>
            </td>
            <td class="text-right number inf">
                {{ number_format($used_price, 2) }}
            </td>
            <td class="text-right number inf">
                <?php echo number_format($reserveMeter, 2); ?>
            &nbsp;
            </td>
            <td class="text-right number inf">{{ $vat7 }}</td>
            <td class="text-right number t2-pr-3 inf">
                {{ number_format($paid + $vat7, 2) }}
                {{-- &nbsp; --}}
            </td>
        </tr>
    @endforeach
    {{-- @endfor --}}

    @for ($i = collect($invoicesPaidForPrint)->count(); $i < 5; $i++)
        <tr id="{{ $i == 5 ? '' : 'info' }}">
            @for ($j = 0; $j < 9; $j++)
                <td class="border-0">&nbsp;</td>
            @endfor
        </tr>
    @endfor

 <tr>
        <td colspan="9" style="padding:4px !important;"></td>
    </tr>
 <tr>
        <td colspan="4" class="text-center border-0" rowspan="3">
            &nbsp;

            {{-- <div >งานประปา  โทร. 0841683269</div>
            <div > งานจัดเก็บ โทร. 0834525757</div> --}}

        </td>
        <td class="pl-2 summary_text" colspan="4">
            &nbsp;
            {{-- รวมเป็นเงิน <span class="baht"> (บาท)</span> --}}
        </td>
        <td class="text-right t2-pr-3 ">
            {{-- &nbsp; --}}
            {{ number_format($total, 2) }}
        </td>
    </tr>

    <tr>
        <td class="pl-3 summary_text" colspan="4">
            &nbsp;
            {{-- ภาษีมูลค่าเพิ่ม 7% <span class="baht"> (บาท)</span> --}}
        </td>
        <td class="text-right t2-pr-3 pt-2">
            {{-- &nbsp; --}}
            {{ number_format($totalVat7, 2) }}
        </td>
    </tr>
    <tr>
        <td class="pl-2 pt-0 summary_text" colspan="5">
            <div class="row">
                <div class="col-8 pt-1">
                    <span style="font-size: 0.95rem">
                        &nbsp;
                        {{-- รวมที่ต้องชำระทั้งสิ้น</span> --}}
                     <span class="baht">
                        {{-- (บาท) --}}

                     </span>
                </div>
                <div class="col-4 text-right t2-pr-3 pt-2 header-bg">
                    <h5>
                        {{ number_format($total + $totalVat7, 2) }}
                        {{-- &nbsp; --}}
                    </h5>
                </div>
            </div>
            <div class="text-right t2-pr-3" style="font-size: 0.8rem">
                ({{ App\Http\Controllers\Api\FunctionsController::convertAmountToLetter(number_format($total + $totalVat7, 2)) }})
            </div>

        </td>
    </tr>
</table>

<table border="0" width="96%" class="mt-2">
    <tr>
        <td colspan="3" class="text-center border-right-none">
            <div style="padding-bottom:20px !important">
                &nbsp;
            {{-- (ลงชื่อ) --}}
            <img src="{{ asset('sign/s1.png') }}"  width="150" height="40" style="position: relative; margin-top:-20px; margin-left: 0px; opacity: 0;">
            </div>

            (&nbsp;&nbsp;{{ $invoicesPaidForPrint[0]->tw_acc_transactions->cashier_info->prefix . '' .
                $invoicesPaidForPrint[0]->tw_acc_transactions->cashier_info->firstname . ' ' .
                $invoicesPaidForPrint[0]->tw_acc_transactions->cashier_info->lastname }}&nbsp;&nbsp;)

            </div>
            <div>&nbsp;
                {{-- ผู้รับเงิน --}}
            </div>

        </td>
        <td colspan="3" class="text-center border-left-none">
            <div style="padding-bottom:20px !important">
                &nbsp;
            {{-- (ลงชื่อ) --}}
            <img src="{{ asset('sign/s1.png') }}"  width="150" height="40" style="position: absolute; margin-top:-5px; margin-left: -80px; opacity: 0;">

            </div>
                ( &nbsp;&nbsp;น.ส.พัชรี ทองคุณ&nbsp;&nbsp;  )
            </div>
            <div>
                &nbsp;
                {{-- หัวหน้ากองคลัง --}}
            </div>

        </td>
    </tr>
</table>
