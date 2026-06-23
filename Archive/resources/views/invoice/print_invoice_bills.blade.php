@extends('layouts.print')

@section('style')
    <style>
        /* body {
                    font-family: 'Sarabun', sans-serif;
                    margin: 0;
                    padding: 0;
                } */
        .invoice-container {
            width: 14.85cm;
            height: 10.05cm;
            max-width: 14.85cm;
            max-height: 10.05cm;
            padding: 0.5cm;
            border: 1px solid #ccc;
            box-sizing: border-box;
            font-size: 0.8em;
            display: flex;
            flex-direction: column;
        }

        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .invoice-header .logo {
            width: 70px;
        }

        .invoice-title {
            text-align: center;
            font-size: 1.2em;
            font-weight: bold;
            flex-grow: 1;
        }

        .invoice-details,
        .customer-details,
        .invoice-items {
            margin-bottom: 10px;
        }

        .invoice-items table {
            width: 100%;
        }

        .summary-box {
            border: 1px solid #050505;
            padding: 5px;
            text-align: right;
            margin-top: auto;
        }

        .owe-info {
            font-size: 0.7em;
            color: #d9534f;
        }

        .current-invoice-info {
            border: 1px solid black
        }
    </style>
@endsection

@section('content')
    <input type="hidden" id="type" value="invoice_owe">

    <?php $count =   1;?>
    @foreach ($print_infos as $item)
        <div class="invoice-container">
            <div class="invoice-header">
                <img src="{{asset('/logo/khampom.png')}}" alt="Logo" class="logo">
                <div class="text-center">
                    <div class="fw-bold text-bold">{{$org->org_type_name . $org->org_name}}</div>
                    <div>กิจการประปา</div>
                    <div>{{$org->org_address}}</div>
                    <div>
                        ต.{{$org->tambons->tambon_name}}
                        อ.{{$org->districts->district_name}}
                        จ.{{$org->provinces->province_name}}
                        {{$org->org_zipcode}}
                    </div>
                </div>
                <div class="text-end">

                    <div class="fw-bold">ใบแจ้งหนี้</div>
                    <div>เลขที่: {{$item['meter_id']}}</div>
                    <div>วันที่: {{$item['created_at']}}</div>
                </div>
            </div>

            <div class="row">
                <div class="col-6">
                    <div class="fw-bold">ชื่อผู้ใช้น้ำ: {{$item['name']}} {{$item['submeter_name']}}</div>
                    <div class="fw-bold">บ้านเลขที่: {{$item['user_address']}}</div>
                </div>
                <div class="col-3">
                     <div>เลขที่มาตร: {{$item['meternumber']}}</div>
                </div>
                <div class="col-3">
                    <div class="fw-bold">รอบบิล: <div>{{$item['period']}}</div></div>
                </div>
            </div>
            <div class="row mt-1 current-invoice-info">
                <div class="col-3" style="border-right: 1px solid black">
                    <div class="fw-bold text-right">เลขมาตรครั้งก่อน:
                        <div>{{$item['lastmeter']}}</div>
                    </div>
                </div>
                <div class="col-3" style="border-right: 1px solid black">
                    <div class="fw-bold text-right">เลขมาตรครั้งนี้:
                        <div>{{$item['currentmeter']}}
                        </div>
                    </div>
                </div>
                <div class="col-3" style="border-right: 1px solid black">
                    <div class="fw-bold text-right">ปริมาณการใช้น้ำ:
                        <div>{{$item['water_used']}} หน่วย
                        </div>
                    </div>
                </div>
                <div class="col-3">
                    <div class="fw-bold text-right">เป็นเงิน:
                        <div>{{$item['paid']}}.00 บาท
                        </div>
                    </div>
                </div>
            </div>

            <div class="row current-invoice-info">
                <div class="col-9 text-right" style="border-right: 1px solid black">ค่ารักษามิเตอร์</div>
                <div class="col-3">
                    <div class="fw-bold text-right">
                        <div>10.00 บาท
                        </div>
                    </div>
                </div>
                <div class="col-9 text-right" style="border-right: 1px solid black">ภาษีมูลค่าเพิ่ม 7%</div>
                <div class="col-3">
                    <div class="fw-bold text-right">
                        <div>{{$item['vat']}}.00 บาท
                        </div>
                    </div>
                </div>
            </div>

            @php
                $sum_owe_totalpaid = 0;
            @endphp
            <div class="row current-invoice-info">
                <div class="col-9" style="border-right: 1px solid black">
                    <div class="fw-bold">ยอดค้างชำระ:</div>

                    <div class="row">
                        @foreach ($item['owe_infos'] as $owe)
                            <div class="col-6">
                                {{$owe['inv_period']}} ( {{$owe['totalpaid']}}.00 บาท)
                            </div>
                            @php
                                $sum_owe_totalpaid += $owe['totalpaid'];
                            @endphp
                        @endforeach
                    </div>
                </div>
                <div class="col-3 text-end d-flex align-items-center justify-content-end">
                    {{$sum_owe_totalpaid}}.00 บาท
                </div>
            </div>

            {{--
            <hr class="my-2"> --}}

            <div class="summary-box">
                {{-- <div class="fw-bold">ค่าบริการงวดนี้: 148.00 บาท</div>
                <div class="fw-bold">ยอดค้างชำระทั้งหมด: 248.00 บาท</div> --}}
                {{--
                <hr class="my-1"> --}}
                @php
                    $total_net = $sum_owe_totalpaid + $item['vat'] + $item['paid'] + 10;
                @endphp
                <div class="fw-bold fs-5">รวมยอดที่ต้องชำระ: {{$total_net}}.00 บาท</div>
            </div>

    
                <small class="text-center">
                    <div>โปรดชำระเงินภายในวันที่  {{$item['expired_date']}}</div>
                    <div> หากเกินกำหนดจะถูกระงับการใช้น้ำและ
              จะจ่ายน้ำใหม่หลังจากได้รับการชำระหนี้ค้างทั้งหมดแล้ว</div>
                </small>
            </div>
        </div>
        <p style='overflow:hidden;page-break-after:always;'></p>

        {{-- @if (collect($print_infos)->count() > $count++) --}}
            {{-- <span style='overflow:hidden;page-break-after:always;'></span> --}}
        {{-- @endif --}}
    @endforeach

@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/jquery.qrcode@1.0.3/jquery.qrcode.min.js"></script>
    <script>
        $(document).ready(function() {
           // console.log($('#qrcode_text').val())
           // $('#qrcode').qrcode($('#qrcode_text').val());


            var os = navigator.platform;

            console.log('os', os)

            // $('.btnprint').click(function(){
            $('.btnprint').hide();
            var css = '@page {  }',
                head = document.head || document.getElementsByTagName('head')[0],
                style = document.createElement('style');
            style.type = 'text/css';
            style.media = 'print';
            if (style.styleSheet) {
                style.styleSheet.cssText = css;
            } else {
                style.appendChild(document.createTextNode(css));
            }
            head.appendChild(style);

            style.type = 'text/css';
            style.media = 'print';

            if (style.styleSheet) {
                style.styleSheet.cssText = css;
            } else {
                style.appendChild(document.createTextNode(css));
            }

            head.appendChild(style);

             window.print();
             if ($('#type').val() == 'invoice_owe') {
                setTimeout(function() {
                     window.location.href = '/invoice';
                 }, 200);
             } else {
                 //type == history_recipt
                 setTimeout(function() {

                    document.querySelector("form").addEventListener("submit", function(evt) {
                         evt.preventDefault();
                     });

                     // Just call the .click method of the button
                     $("input[type='submit']").click();

                 }, 200);

             }
        });
    </script>
@endsection