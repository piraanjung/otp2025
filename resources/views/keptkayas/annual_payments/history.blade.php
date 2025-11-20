@extends('layouts.keptkaya')
@section('nav-header', 'ค้นหาใบเสร็จรับเงิน')
@section('nav-current', 'ค้นหาใบเสร็จรับเงิน')
{{-- @section('page-topic', 'ค้นหาใบเสร็จรับเงิน') --}}
@section('nav-user_payment_per_month-history', 'active')


@section('style')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- Latest compiled and minified JavaScript -->

    <style>
        .select2-container .select2-selection--single {
            box-sizing: border-box;
            cursor: pointer;
            display: block;
            height: 50px;
            user-select: none;
            -webkit-user-select: none;
            padding-top: 5px;
            font-size: 1.1rem;
            width: 700px !important
        }
        .select2-container{
            width: 700px !important
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 26px;
            position: absolute;
            top: 11px;
            right: 1px;
            width: 20px;
        }

        .hidden {
            display: none
        }

        .budgetyear-div {
            cursor: pointer;
        }

        .budgetyear-div:hover {
            opacity: 0.8;
            transform: scale(1.05);
            transition: all 1s;
        }
    </style>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.13.1/themes/base/jquery-ui.css">
@endsection


@section('page-topic')
<div class="row">
    <div class="col-5">
        <div class="h-100">
            <h5 class="mb-1">ค้นหา : ชื่อ,ที่อยู่ ,รหัสถังขยะ</h5>
            <form action="{{ route('keptkayas.annual_payments.history') }}" method="POST" class="d-flex justify-content-between">
                @csrf

                <select class="js-example-basic-single form-control" name="bin_code">
                    <option>เลือก...</option>
                    @foreach ($usersArray as $user)

                        <option value="{{ $user['bin_code'] }}" >
                            {{ $user['firstname'] . ' ' . $user['lastname'].'     [บ้านเลขที่ ' . $user['address'] . ' ' . $user['zone_name'] }}]

                            - [{{ $user['bin_code'] }}]

                        </option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-secondary ms-3"><i class="fa fa-search">ค้นหา</i></button>
            </form>

        </div>
    </div>
    <div class="col-5">
        {{-- @if (collect($inv_by_budgetyear)->isNotEmpty())
            {{ $inv_by_budgetyear[0]->user->firstname." ".$inv_by_budgetyear[0]->user->lastname }}
        @endif --}}
    </div>
</div>
@endsection

@section('content')
    @if (collect($usersArray)->isNotEmpty())
        @foreach ($usersArray as $user)
            @if (collect($user['datas'])->isNotEmpty())
      
            <div class="card mb-2">
                <div class="card-header bg-info ">
                    <div class="h5"> ปีงบประมาณ {{ $user['datas'][0]->fiscal_year }}</div>
                        {{-- <a href="{{ route('user_payment_per_month.printReceiptHistory', $budgetyear->id) }}" class="btn btn-primary text-right">ปริ้น</a> --}}
                </div>
                <div class="card-body">
        <div class="receipt-column">
            <div class="header d-flex">
                <div class="p-0">
                    <div style="font-size: 0.9rem; font-weight: bold;">ใบเสร็จรับเงิน </div>
                    <div style="font-size: 0.6rem"> (ต้นขั้ว)</div>
                    <div style="font-size: 0.6rem">เลขที่: {{ $user['datas'][0]->id }}</div>

                </div>
                <div class="ms-auto pl-2">

                    <div style="font-size: 0.9rem; font-weight: bold;">
                        {{ $orgInfos['org_type_name'] }}
                        {{ $orgInfos['org_name'] }}
                    </div>
                    <div style="font-size: 0.6rem">
                        {{ $orgInfos['org_address'] }} 
                        หมู่ {{ $orgInfos['org_zone'] }}  
                        ต.{{ $orgInfos['org_tambon'] }}
                    </div>
                    <div style="font-size: 0.6rem">
                        อ.{{ $orgInfos['org_district'] }}
                         จ.{{ $orgInfos['org_province'] }}
                          {{ $orgInfos['org_zipcode'] }}</div>
                </div>   
         
            </div>
            <div class="d-flex flex-row">
                <table class="info-table">
                    <tr>
                        <td class="label">ผู้ชำระ:</td>
                        <td>{{ $user['datas'][0]->wastBin->user->firstname ?? 'N/A' }}
                            {{ $user['datas'][0]->wastBin->user->lastname ?? '' }}</td>
                    </tr>
                    <tr>
                        <td class="label">รหัสถัง:</td>
                        <td>{{ $user['datas'][0]->wastBin->bin_code ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="label">วันที่ชำระ:</td>
                        <td>{{ \Carbon\Carbon::parse($user['datas'][0]->updated_at)->locale('th')->isoFormat('Do MMMM YYYY') }}</td>
                    </tr>
                </table>
                <img src="{{asset('logo/'.$orgInfos['org_logo_img'] )}}" style="width: 65px; height:65px;margin-left:0.2rem; border:1px solid black"/>
            </div>
            

            @php
            // dd($user['datas'][0]['payments']);
            $arr =[
                ['ค่าเก็บและขนขยะมูลฝอย', $user['datas'][0]->total_paid_amt], ['ภาษีมูลค่าเพิ่ม  7%', '0.00']
            ]
            @endphp
            <div style="display: flex; flex-direction: column">
                <div>
                    <table class="table">
                        <thead>
                            <tr>
                                <td style="width: 70%">รายการ</td>
                                <td style="width: 30%">จำนวนเงิน(บาท)</td>
                            </tr>
                        </thead>
                            <tbody>
                            <tr>
                                <td style="height: 50px; vertical-align: top;">
                                    @php
                                    foreach($arr as $ar){
                                       echo "1. ". $ar[0]."<br>";
                                    }
                                    @endphp
                                
                                </td>
                                <td style="vertical-align: top; text-align: right;">
                                   @php
                                    foreach($arr as $ar){
                                       echo $ar[1]."<br>";
                                    }
                                    @endphp
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align:right">
                                   รวมทั้งสิ้น
                                
                                </td>
                                <td style="text-align: right;">
                                   {{-- {{number_format($data['total_paid_amt'], 2)}} --}}
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" style="text-align: right; font-size: 10px;">
                                    {{-- ({{ App\Http\Controllers\Api\FunctionsController::convertAmountToLetter(number_format($data['totalPaidAmount'], 2))}}) --}}
                                </td>
                            </tr>
                            
                        </tbody>
                    </table>
                </div>
                ประวัติการชำระค่าธรรมเนียมประจำปีงบประมาณ {{2568}}

                <div class="row" style="padding: 5px 11px">
                     @for($i=0; $i<3; $i++)
                        <div class="col-4 te">
                            <div class="row">
                                <div class="col-4 text-center text-bolder" style="border-right: 1px solid black">
                                    เดือน
                                </div>
                                <div class="col-8 text-center text-bolder">
                                    จำนวนเงิน(บาท)
                                </div>
                            </div>
                            
                        </div>
                    @endfor
                    @php
                    $arr= [10,11,12,1,2,3,4,5,6,7,8,9];
                    $i=0;
                    // dd($data['payments']);
                    @endphp
                    @foreach ($arr as $ar)
                         <div class="col-4 te">
                            <div class="row">
                                 <div class="col-4 text-center" style="border-right: 1px solid black">
                                        {{(new \App\Http\Controllers\Api\FunctionsController())->shortThaiMonth($ar)}}
                                </div>
                               

                               {{-- @dd($user['paidMonthArr']) --}}
                                <div class="col-8 text-center">
                                     @if (in_array($ar,collect($user['paidMonthArr'])->toArray()))
                                        {{ number_format($user['datas'][0]['payments'][$i++]['amount_paid'], 2) }}  
                                      @else
                                        -
                                    @endif
                                </div>
                               
                               
                            </div>
                            
                        </div>
                    @endforeach
                    
                </div>
              
            </div>


            <div class="total-section">
                <strong>ยอดรวม:</strong> 
                {{-- {{ number_format($data['totalPaidAmount'], 2) }} --}}
                 บาท
            </div>

            <div class="footer">
                <p>ลงชื่อเจ้าหน้าที่: _________________________</p>
                <p>({{ $data['staff']->firstname ?? 'N/A' }} {{ $data['staff']->lastname ?? '' }})</p>
            </div>
        </div>
                </div>
            </div>
            @endif
        @endforeach

    @endif

@endsection


@section('script')
    <script
        src="https://www.jqueryscript.net/demo/Export-Html-Table-To-Excel-Spreadsheet-using-jQuery-table2excel/src/jquery.table2excel.js">
    </script>
    {{-- <script src="{{ asset('/js/my_script.js') }}"></script> --}}
    <script src="https://code.jquery.com/ui/1.13.1/jquery-ui.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.1/bootstrap3-typeahead.min.js"></script>

    <script>
        $(document).ready(function() {
            $('.js-example-basic-single').select2();
        });
    </script>
@endsection
