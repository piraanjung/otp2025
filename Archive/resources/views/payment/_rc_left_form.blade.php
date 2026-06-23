@php
    // 1. ดึงข้อมูลส่วนหัว (ใช้ข้อมูลจาก Invoice ใบแรก และ Transaction แม่)
    $head = $invoicesPaidForPrint->first();
    $transaction = $head->tw_acc_transactions; // เรียกผ่าน relation ที่แก้แล้ว
    $meterInfo = $head->tw_meter_infos;
    
    // ข้อมูลแคชเชียร์
    $cashierName = $transaction->cashier_info 
        ? $transaction->cashier_info->prefix . $transaction->cashier_info->firstname . ' ' . $transaction->cashier_info->lastname 
        : '-';

    // ฟังก์ชันแปลงตัวเลขเป็นคำอ่านภาษาไทย (ใส่ไว้ตรงนี้เพื่อให้เรียกใช้ได้เลย)
    if (!function_exists('baht_text')) {
        function baht_text($number) {
            $txtnum1 = array('ศูนย์','หนึ่ง','สอง','สาม','สี่','ห้า','หก','เจ็ด','แปด','เก้า','สิบ');
            $txtnum2 = array('','สิบ','ร้อย','พัน','หมื่น','แสน','ล้าน');
            $number = str_replace(",","",$number);
            $number = str_replace(" ","",$number);
            $number = str_replace("บาท","",$number);
            $number = explode(".",$number);
            if(sizeof($number)>2){ return 'ตัวเลขมีรูปแบบผิดพลาด'; }
            $baht = $number[0];
            $satang = count($number) > 1 ? $number[1] : 0;
            if($baht == '') $baht = 0;
            if($satang == '') $satang = 0;
            $str = "";
            $len = strlen($baht);
            for($i=0;$i<$len;$i++){
                $n = substr($baht, $i, 1);
                if($n!=0){
                    if($i==($len-1) AND $n==1){ $str .= 'เอ็ด'; }
                    elseif($i==($len-2) AND $n==2){ $str .= 'ยี่'; }
                    elseif($i==($len-2) AND $n==1){ $str .= ''; }
                    else{ $str .= $txtnum1[$n]; }
                    $str .= $txtnum2[$len-$i-1];
                }
            }
            $str .= 'บาท';
            if($satang == 0){
                $str .= 'ถ้วน';
            }else{
                $len = strlen($satang);
                for($i=0;$i<$len;$i++){
                    $n = substr($satang, $i, 1);
                    if($n!=0){
                        if($i==($len-1) AND $n==1){ $str .= 'เอ็ด'; }
                        elseif($i==($len-2) AND $n==2){ $str .= 'ยี่'; }
                        elseif($i==($len-2) AND $n==1){ $str .= ''; }
                        else{ $str .= $txtnum1[$n]; }
                        $str .= $txtnum2[$len-$i-1];
                    }
                }
                $str .= 'สตางค์';
            }
            return $str;
        }
    }
@endphp

<div class="document-wrapper">
    {{-- Header: ชื่อองค์กรและเลขที่ใบเสร็จ --}}
    <div class="row m-0 border-start border-top border-dark">
        <div class="col-8 border-end border-bottom border-dark pt-2 pb-2" style="background: rgb(195, 195, 234)">
            <h6 class="text-danger fw-bold mb-0" style="font-size: 12pt;">ต้นขั้วใบเสร็จรับเงิน/ใบกำกับภาษี</h6>
            <div class="small text-dark" style="font-size: 7.5pt;">
                <span class="fw-bold">เลขผู้เสียภาษี 0994000352620</span>
            </div>
        </div>
        <div class="col-4 text-end border-end border-bottom border-dark">
            <p class="mb-0" style="font-size: 7.5pt;">เลขที่ 
                <span style="font-size: 13pt; font-weight: bold; color: red;">{{ $head->inv_no }}</span>
            </p>
            <p class="mb-0 mt-2 pt-1 border-top border-dark" style="font-size: 8pt;">
                วันที่ {{ \Carbon\Carbon::parse($transaction->updated_at)->addYears(543)->format('d/m/Y H:i') }}
            </p>
        </div>
    </div>

    {{-- Info: ที่อยู่เทศบาล --}}
    <div class="row m-0">
        <div class="col-8">
            <p class="fw-bold mb-0" id="org_name">เทศบาลตำบลขอนแก่น</p>
            <p class="mb-0 org_address">222 หมู่ 17 ตำบลนาป่า </p>
            <p class="mb-0 org_address"> อำเภอพระยืน จังหวัดขอนแก่น 40003</p>
        </div>
        <div class="col-4 text-end">
            <img src="{{ asset('/logo/hs_logo.png') }}" width="95" class="img1test">
        </div>
    </div>

    {{-- Customer Info --}}
    <div class="row m-0 header-box">
        <div class="col-12 p-0" id="username">
            <div class="d-flex border-bottom border-dark">
                <div class="p-1 text-start border-end border-dark w-25 title">ชื่อผู้ใช้น้ำ</div>
                <div class="p-1 border-end w-75 detail">
                   {{-- $meterInfo->user->fullname ?? 'ระบุชื่อลูกค้า' --}}
                   คุณลูกค้า ({{ $meterInfo->meternumber }})
                </div>
            </div>
            <div class="d-flex border-bottom border-dark" id="user_address">
                <div class="p-1 text-start border-end border-dark w-25 title">ที่อยู่</div>
                <div class="p-1 flex-grow-1 text-start w-75 detail">
                    222 หมู่ 17 ตำบลนาป่า อำเภอพระยืน จังหวัดขอนแก่น
                </div>
            </div>
        </div>
    </div>

    {{-- Meter Info --}}
    <div class="row m-0 header-box mt-1">
        <div class="col-12 p-0">
            <div class="d-flex w-100" id="meter">
                <div class="p-1 fw-bold text-start border-end border-dark title w-25">รหัสผู้ใช้น้ำ</div>
                <div class="p-1 border-end border-dark detail w-25">{{ $meterInfo->meternumber }}</div>
                <div class="p-1 fw-bold text-start border-end border-dark title w-25">Subzone</div>
                <div class="p-1 border-dark detail w-25">{{ $meterInfo->undertake_subzone_id }}</div>
            </div>
        </div>
    </div>

    {{-- Table Detail --}}
    <div class="flex-grow-1 mt-1" style="overflow: hidden;">
        <div class="etc">***(R) = ค่ารักษามิเตอร์</div>

        <table class="detail-table w-100">
            <thead>
                <tr class="bg-light">
                    <th style="width: 15%;">เดือน</th>
                    <th style="width: 14%;">วันที่<div>จดมาตร</div></th>
                    <th style="width: 12%;">มิเตอร์<div>ก่อน</div> <span class="unit">(หน่วย)</span></th>
                    <th style="width: 12%;">มิเตอร์<div>หลัง</div> <span class="unit">(หน่วย)</span></th>
                    <th style="width: 10%;">ใช้น้ำ <span class="unit">(หน่วย)</span></th>
                    <th style="width: 14%;">ค่าน้ำ <span class="unit">(บาท)</span></th>
                    <th style="width: 10%;">Vat <span class="unit">(บาท)</span></th>
                    <th style="width: 14%;">รวม <span class="unit">(บาท)</span></th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoicesPaidForPrint as $item)
                <tr>
                    {{-- ชื่อรอบบิล --}}
                    <td>{{ $item->invoice_period->inv_p_name ?? '-' }}</td>
                    {{-- วันที่จด (สมมติใช้ created_at ของบิล) --}}
                    <td>{{ \Carbon\Carbon::parse($item->created_at)->addYears(543)->format('d/m/y') }}</td>
                    <td>{{ $item->lastmeter }}</td>
                    <td>{{ $item->currentmeter }}</td>
                    <td>{{ $item->water_used }}</td>
                    
                    {{-- ค่าน้ำ (Paid คือค่าน้ำเปล่าๆ หรือรวม Vat? เช็ค Logic อีกที ปกติ paid = ค่าน้ำ+ค่ารักษา) --}}
                    <td class="text-end">
                        @if($item->inv_type == 'r' || $item->water_used == 0) <sup class="text-bold">(R)</sup> @endif
                        {{ number_format($item->paid, 2) }}
                    </td>
                    <td class="text-end">{{ number_format($item->vat, 2) }}</td>
                    <td class="text-end">{{ number_format($item->totalpaid, 2) }}</td>
                </tr>
                @endforeach

                {{-- เติมแถวว่าง ถ้ารายการน้อยกว่า 5 แถว เพื่อความสวยงาม (Optional) --}}
                @for($i = $invoicesPaidForPrint->count(); $i < 5; $i++)
                <tr style="height: 18px; color: transparent;">
                    <td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td>
                </tr>
                @endfor
            </tbody>
        </table>
    </div>

    {{-- Footer Summary --}}
    <div class="row m-0 pt-1">
        <div class="col-4 ps-0 pe-0 mt-2 me-0">
            <p class="mb-1 fw-bold text-center" style="font-size: 9pt;">งานกิจการประปา</p>
            <p class="mb-0" style="font-size: 8pt;">การเงิน โทร. 088-1005-xxx</p>
            <p class="mb-0" style="font-size: 8pt;">กองช่าง โทร. 084-1683-xxx</p>
        </div>

        <div class="col-8 pe-0">
            <div class="summary-box p-1">
                <div class="d-flex justify-content-between border-bottom border-dark pb-1">
                    <span class="text-sm">รวมค่าน้ำประปา/รักษามิเตอร์ <span class="unit">(บาท)</span></span>
                    <span class="text-end fw-bold text-sm">{{ number_format($transaction->paidsum, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between border-bottom border-dark pb-1">
                    <span class="text-sm">ภาษีมูลค่าเพิ่ม 7% <span class="unit">(บาท)</span></span>
                    <span class="text-end fw-bold text-sm">{{ number_format($transaction->vatsum, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between pb-0">
                    <span class="text-sm">รวมต้องชำระทั้งสิ้น <span class="unit">(บาท)</span></span>
                    <span class="text-end fw-bold text-danger" style="background: rgb(195, 195, 234); padding-bottom: 5px;">
                        {{ number_format($transaction->totalpaidsum, 2) }}
                    </span>
                </div>
            </div>
            <p class="mb-0 text-center" style="font-size: 7.5pt;">
                ({{ baht_text(number_format($transaction->totalpaidsum, 2)) }})
            </p>
        </div>
    </div>

    {{-- Signatures --}}
    <div class="row m-0 mt-2 align-items-end" style="height: 40px;">
        <div class="col-6">
            {{-- ลายเซ็นผู้รับเงิน (แคชเชียร์) --}}
            <img src="{{ asset('/sign/sign.png') }}" width="130" height="20" class="img1test opacity-0">
            <p class="mt-1 mb-0 text-center border-top border-dark p-0" style="font-size: 9pt;">
                ({{ $cashierName }})
            </p>
            <p class="mb-0 fw-bold text-center" style="font-size: 8pt;">ผู้รับเงิน</p>
        </div>
        <div class="col-6">
            {{-- ลายเซ็น ผอ. กองคลัง (Fix หรือ Dynamic แล้วแต่ระบบ) --}}
            <img src="{{ asset('/sign/sign.png') }}" width="130" height="20" class="img1test">
            <p class="mt-1 mb-0 text-center border-top border-dark p-0" style="font-size: 9pt;">
                (นางสาวญานินทร์ ยศอินเทียม)
            </p>
            <p class="mb-0 fw-bold text-center" style="font-size: 8pt;">ผู้อำนวยการกองคลัง</p>
        </div>
    </div>
</div>