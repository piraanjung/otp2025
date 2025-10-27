<?php
use App\Http\Controllers\FunctionsController;
$fnc = new FunctionsController();
// dd($invoicesPaidForPrint[0]);

$exp = explode(' ', $invoicesPaidForPrint[0]->acc_transactions->updated_at);
$receipt_th_date = $fnc->engDateToThaiDateFormat($exp[0]);
?>
<div class="row  pr-2">
    <div class="col-9" style="border: 1px solid black">
        <div class="text-center head pt-2 pb-2 header-bg">
         ต้นขั้วใบเสร็จรับเงิน/ใบกำกับภาษี
            {{-- &nbsp; --}}
            <div class="tax_number header-bg">
                เลขที่ผู้เสียภาษี 0994000352620
                {{-- &nbsp; --}}
            </div>
        </div>
    </div>
    <div class="col-3" style="border: 1px solid black">
        <div colspan="2" class="text-center head2 pt-2 pb-2">
            <div class="tax_number header-bg pt-1">
                 เลขที่ 
                {{-- &nbsp; --}}
            </div>
            <div class="text-danger" style="font-size: 1.4rem; font-weight:bolder">
                {{-- &nbsp; --}}
                00000001
            </div>
        </div>
    </div>
</div>
<div  class="row pr-2">
        <div class="col-9 test text-left text-primary row2">
            เทศบาลตำบลขามป้อม
            {{-- &nbsp; --}}
            <div class="address2">
                222 หมู่ 17 ตำบลขามป้อม อำเภอพระยืน จังหวัดขอนแก่น 40003
                {{-- &nbsp; --}}
            </div>
        </div>
        <div class="col-3 test text-center pt-0 pb-0 row2">
            <div>{{ $receipt_th_date }}</div>
            @if ($receipt_th_date < $fnc->engDateToThaiDateFormat(date('Y-m-d')))
                <div style="font-size: 0.8rem;"> ( ปริ้น: {{ $fnc->engDateToThaiDateFormat(date('Y-m-d')) }} ) </div>
            @endif
        </div>
</div>
 <div class="row pr-2">
    <div class="col-9">
        <div class="row">
            <div class="col-3 waterUsedHisHead  test header-bg">
            ชื่อผู้ใช้น้ำ
            {{-- &nbsp; --}}
            </div>
            <div class="col-9 test">
                {{ $invoicesPaidForPrint[0]->tw_meter_infos->user->prefix . '' . $invoicesPaidForPrint[0]->tw_meter_infos->user->firstname . ' ' . $invoicesPaidForPrint[0]->tw_meter_infos->user->lastname }}
            </div>
         <div class="col-3 waterUsedHisHead  test header-bg">
            ที่อยู่
            &nbsp;
         </div>
            <div class="col-9 test">
            {{ $invoicesPaidForPrint[0]->tw_meter_infos->meter_address }}
            {{ $invoicesPaidForPrint[0]->tw_meter_infos->undertake_subzone->subzone_name }}
            ต.{{ $invoicesPaidForPrint[0]->tw_meter_infos->user->user_tambon->tambon_name }}
            อ.{{ $invoicesPaidForPrint[0]->tw_meter_infos->user->user_district->district_name }}
            จ.{{ $invoicesPaidForPrint[0]->tw_meter_infos->user->user_province->province_name }}
            {{ $invoicesPaidForPrint[0]->tw_meter_infos->user->user_tambon->zipcode }}
            </div>

        </div>
    </div>
       

        <div  class=" col-3 text-center test border-right-none border-bottom-none">
            {{-- &nbsp; --}} 
            <img src="{{ asset('/logo/khampom.png') }}" width="100">
        </div>
    </div>
 <div class="row pr-2">

        <div  class="col-2 waterUsedHisHead test pl-2 header-bg">
            เลขผู้ใช้มิเตอร์
            &nbsp;
        </div>
        
        <?php $sunmeter_name  = $invoicesPaidForPrint[0]->tw_meter_infos->submeter_name == "" ? "" : " (".$invoicesPaidForPrint[0]->tw_meter_infos->submeter_name.")"; ?>
        <div  class="col-4 text-center test pl-1"> {{ $invoicesPaidForPrint[0]->tw_meter_infos->user_id."".$sunmeter_name }}</div>
        <div  class="col-2 waterUsedHisHead test pl-2 header-bg">
            เลขมิเตอร์
            &nbsp;
        </div>
        <div  class="col-4 test text-center">
            {{ $fnc->createInvoiceNumberString($invoicesPaidForPrint[0]->tw_meter_infos->meter_id) }} /
            <span
                style="font-size: 0.8rem">{{ $fnc->createNumberString($invoicesPaidForPrint[0]->id, 'B') }}</span>
        </div>
</div><!--row-->
 <div class="d-flex align-content-center ">
        <div style="padding: 0.15rem;width:10%" class="test waterUsedHisHead2 header-bg text-center">
                ประจำเดือน
        </div>
        <div style="padding: 0.15rem;width:15%" class="test waterUsedHisHead2 header-bg text-center">
                วันที่จดมาตร
        </div>
        <div style="padding: 0.15rem;width:10%" class="test waterUsedHisHead2 header-bg text-center">
           
                มิเตอร์ปัจจุบัน
           <div> <sup>
                    {{-- &nbsp;  --}}
                    (หน่วย)
                </sup>
           </div>
        </div>
        <div style="padding: 0.15rem;width:10%" class="test waterUsedHisHead2 header-bg text-center">
           
                <div> มิเตอร์</div>
                <div>ครั้งก่อน</div>
            <div><sup>
                    {{-- &nbsp;  --}}
                    (หน่วย)
                </sup></div>
        </div>
        <div style="padding: 0.15rem;width:9%" class="test waterUsedHisHead2 header-bg text-center">
           
            <div>จำนวน</div>
            <div>น้ำที่ใช้</div>
            <div><sup>
                    {{-- &nbsp;  --}}
                    (หน่วย)
                </sup></div>
        </div>
        <div style="padding: 0.15rem;width:10%" class="test waterUsedHisHead2 header-bg text-center">
                {{-- &nbsp;  --}}
                ค่าน้ำประปา
            <div><sup>
                    {{-- &nbsp;  --}}
                    (บาท)
                </sup></div>
        </div>
        <div style="padding: 0.15rem;width:10%" class="test waterUsedHisHead2 header-bg text-center">
                {{-- &nbsp;  --}}
                ค่ารักษามิเตอร์
            <div><sup>
                    {{-- &nbsp;  --}}
                    (บาท)
                </sup></div>
        </div>
        <div style="padding: 0.15rem;width:9%" class="test waterUsedHisHead2 header-bg text-center">
           
                {{-- &nbsp;  --}}
                <div>
                Vat&nbsp;7%
                </div>
                <div>
                <sup>
                    {{-- &nbsp;  --}}
                    (บาท)
                </sup></div>
        </div>
        <div style="padding: 0.15rem;width:17%" class="test waterUsedHisHead2 header-bg text-center">
            
                จำนวนเงิน
            <div><sup>
                    {{-- &nbsp;  --}}
                    (บาท)
                </sup></div>
        </div>
    </div>
    @for ($i = 0; $i < 6; $i++)
    <div class="d-flex align-content-center ">
        <div style="padding: 0.25rem 0.15rem 0.25rem 0.15rem;width:10%" class="test waterUsedHisHead2 header-bg text-center">
                07/68
        </div>
        <div style="padding: 0.25rem 0.15rem 0.25rem 0.15rem;width:15%" class="test waterUsedHisHead2 header-bg text-center">
                17/07/67
        </div>
        <div style="padding: 0.25rem 0.15rem 0.25rem 0.15rem;width:10%" class="test waterUsedHisHead2 header-bg text-right">
           7,777
        </div>
        <div style="padding: 0.25rem 0.15rem 0.25rem 0.15rem;width:10%" class="test waterUsedHisHead2 header-bg text-right">
           
            7,778
        </div>
        <div style="padding: 0.25rem 0.15rem 0.25rem 0.15rem;width:9%" class="test waterUsedHisHead2 header-bg text-right">
           7,777
        </div>
        <div style="padding: 0.25rem 0.15rem 0.25rem 0.15rem;width:10%" class="test waterUsedHisHead2 header-bg text-right">
             7,777.00
        </div>
        <div style="padding: 0.25rem 0.15rem 0.25rem 0.15rem;width:10%" class="test waterUsedHisHead2 header-bg text-right">
          7,777.00
        </div>
        <div style="padding: 0.25rem 0.15rem 0.25rem 0.15rem;width:9%" class="test waterUsedHisHead2 header-bg text-right">
           777.77
        </div>
        <div style="padding: 0.25rem 0.15rem 0.25rem 0.15rem;width:17%" class="test waterUsedHisHead2 header-bg text-right">
            77,777.77
        </div>
    </div>
    @endfor

     <div class="d-flex">
        <div style="width: 50%" class="test text-center border-left-none border-bottom-none" rowspan="3">
            &nbsp; งานกิจการประปา
            <div >โทร. 08-810-0543-5</div>
            <div style="padding-left: .9rem"> 045-777116</div>

        </div>
        <div style="width: 50%">
            <div class="d-flex">
                <div  style="width:66%"  class="test summary_text">
                    {{-- &nbsp;  --}}
                    รวมเป็นเงิน <span class="baht"> (บาท)</span>
                </div>
                <div  style="width: 34%"  class="test text-right t2-pr-3 number">
                    77,777.77
                </div>
            </div>
            <div class="d-flex">
                <div  style="width:66%"  class="test summary_text">
                    {{-- &nbsp;  --}}
                    ภาษีมูลค่าเพิ่ม 7%  <span class="baht"> (บาท)</span>
                </div>
                <div  style="width: 34%"  class="test text-right t2-pr-3 number">
                    77,777.77
                </div>
            </div>
            <div class="d-flex">
                <div style="width:66%" class=" summary_text">
                    {{-- &nbsp;  --}}
                    รวมที่ต้องชำระทั้งสิ้น <span class="baht"> (บาท)</span>
                </div>
                <div style="width:34%"  class="h5 text-right t2-pr-3 number">
                    77,777.77

                </div>
                
            </div>
            <div class="text-right" style="font-size: 0.85rem;">
                ({{ App\Http\Controllers\Api\FunctionsController::convertAmountToLetter(77777.77) }})

            </div>
        </div>
    </div>
{{--    
     <div class="text-right" style="font-size: 0.85rem;>
                ({{ App\Http\Controllers\Api\FunctionsController::convertAmountToLetter(77777.77) }})
            </div> --}}

 <div class="d-flex test">
        {{-- <div class="test text-center border-left-none border-right-none pt-1"> --}}
            {{-- (ลงชื่อ)  --}}
            {{-- ddf --}}
            {{-- <div class="d-flex justify-content-center row_sign"> --}}
                <div style="width:50%" >
                    <div  style="font-size: 0.90rem">
                        <img src="{{ asset('/sign/sign2.png') }}" width="130" height="20" class="imgtes1t">
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        {{-- {{ $invoicesPaidForPrint[0]->acc_transactions->cashier_info->prefix . '' . $invoicesPaidForPrint[0]->acc_transactions->cashier_info->firstname . ' ' . $invoicesPaidForPrint[0]->acc_transactions->cashier_info->lastname }} --}}
                        <div>(นางสาวสมจริง ไทยทองหลาง)
                        {{-- &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; --}}
                        </div>ผู้รับเงิน
                        <br>&nbsp;
                    </div>
                </div>
                {{-- <div class="width:50%" > &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div> --}}
                <div style="width:50%" >
                    <div  style="font-size: 0.88rem" class="text-left">
                        &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <img src="{{ asset('/sign/sign.png') }}" width="130" height="20" class="img1test">

                        &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <div>(นางสาวฐานันพัชร ยศตีนเทียน)</div>
                        {{-- ({{ $invoicesPaidForPrint[0]->acc_transactions->cashier_info->prefix . '' . $invoicesPaidForPrint[0]->acc_transactions->cashier_info->firstname . ' ' . $invoicesPaidForPrint[0]->acc_transactions->cashier_info->lastname }}) --}}
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        </span>ผู้อำนวยการกองคลัง
                        <br>&nbsp;
                    </div>
                </div>
            {{-- </div> --}}

        {{-- </div> --}}
    </div>