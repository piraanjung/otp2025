@extends('layouts.admin1')


@section('nav-paid_per_billingcycle-history')
    active
@endsection
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

@section('content')

@section('page-topic')
<div class="row">
    <div class="col-5">
        <div class="h-100">
            <h5 class="mb-1">ค้นหา : ชื่อ,ที่อยู่ ,เลขมิเตอร์</h5>
            <form action="{{ route('user_payment_per_month.history') }}" method="POST" class="d-flex justify-content-between">
                @csrf

                <select class="js-example-basic-single form-control" name="user_info">
                    <option>เลือก...</option>
                    @foreach ($users as $user)

                        <option value="{{ $user->user_id }}" >
                            {{ $user->user->firstname . ' ' . $user->user->lastname.'     [บ้านเลขที่ ' . $user->user->address . ' ' . $user->user->user_zone->zone_name }}]

                            - [{{ $user->meternumber }}]

                        </option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-secondary ms-3"><i class="fa fa-search">ค้นหา</i></button>
            </form>

        </div>
    </div>
    <div class="col-5">
        @if (collect($inv_by_budgetyear)->isNotEmpty())
            {{ $inv_by_budgetyear[0]->user->firstname." ".$inv_by_budgetyear[0]->user->lastname }}
        @endif
    </div>
</div>
@endsection


    @if (collect($inv_by_budgetyear)->isNotEmpty())
        @foreach (collect($inv_by_budgetyear)->reverse() as $budgetyear)
            <div class="card mb-2">
                <div class="card-header bg-info ">
                    <div class="h5"> ปีงบประมาณ {{ $budgetyear->budgetyear->budgetyear_name }}</div>
                        <a href="{{ route('user_payment_per_month.printReceiptHistory', $budgetyear->id) }}" class="btn btn-primary text-right">ปริ้น</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th class="font-weight-bolder text-center">เลขใบแจ้งหนี้</th>
                                    <th class="font-weight-bolder text-center ps-2">รอบบิล</th>
                                    <th class="font-weight-bolder text-center">จำนวนเงิน<sup>บาท</sup></th>
                                    <th class="font-weight-bolder text-center">สถานะการจ่าย</th>
                                    <th class="font-weight-bolder text-center">วันที่สร้างข้อมูล</th>
                                    <th class="font-weight-bolder text-center">วันที่บันทึกการจ่าย</th>
                                </tr>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($budgetyear->user_payment_per_month as $item)
                                <tr>
                                    <td class="text-center">{{ $item->user_payment_per_year_id_fk }}</td>
                                    <td class="text-center">{{ $item->month }}</td>
                                    <td class="text-end mr-3">{{ number_format($item->rate_payment_per_month,2) }}</td>
                                    <td class="text-center">{{ $item->status == 'paid' ? 'ชำระแล้ว' : 'ยังไม่ชำระ' }}</td>
                                    <td class="text-center">{{ App\Http\Controllers\Api\FunctionsController::engDateTimeToThaiDateFormat($item->created_at) }}</td>
                                    <td class="text-center">{{ App\Http\Controllers\Api\FunctionsController::engDateTimeToThaiDateFormat($item->updated_at) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
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
