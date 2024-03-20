@extends('layouts.admin1')


@section('nav-payment-search')
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
<div class="col-5 my-auto">
    <div class="h-100">
        <h5 class="mb-1">ค้นหา : ชื่อ,ที่อยู่ ,เลขมิเตอร์</h5>
        <form action="{{ route('payment.search') }}" method="POST" class="d-flex justify-content-between">
            @csrf

            <select class="js-example-basic-single form-control" name="user_info">
                <option>เลือก...</option>
                @foreach ($users as $user)

                    <option value="{{ $user->usermeterinfos[0]->meter_id }}" >
                        {{ $user->firstname . ' ' . $user->lastname.'     [บ้านเลขที่ ' . $user->address . ' ' . $user->user_zone->zone_name }}]

                        - [{{ $user->usermeterinfos[0]->meternumber }}]

                    </option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-secondary ms-3"><i class="fa fa-search">ค้นหา</i></button>
        </form>

    </div>
</div>
    @if (collect($inv_by_budgetyear)->isNotEmpty())

        <div class="container-fluid my-3 py-3">
            <div class="row mb-5">
                <div class="col-lg-3">
                    <div class="card position-sticky top-1">
                        <!-- sidebar budgetyear -->
                        @foreach ($inv_by_budgetyear as $budgetyear)
                            <div class="col-12 mt-2 budgetyear-div ">
                                <div class="card">
                                    <span
                                        class="mask {{ $budgetyear[0]['invoice_period']['budgetyear']['status'] == 'active' ? 'bg-gradient-info' : 'bg-gradient-dark' }} opacity-9 border-radius-xl">
                                    </span>
                                    <div class="card-body p-3 position-relative">
                                        <a href="#by{{ $budgetyear[0]['invoice_period']['budgetyear_id'] }}" data-scroll="">
                                            <div class="row">
                                                <div class="col-12  d-flex justify-content-between">
                                                    <div class="icon icon-shape bg-white shadow text-center border-radius-md">
                                                        <i class="ni ni-circle-08 text-dark text-gradient text-lg opacity-10"
                                                            aria-hidden="true"></i>
                                                    </div>
                                                    <div class="">
                                                        <h5 class="text-white font-weight-bolder mb-0 text-end">
                                                            {{ $budgetyear[0]['invoice_period']['budgetyear']['budgetyear_name'] }}
                                                        </h5>
                                                        <span class="text-white text-sm">ปีงบประมาณ</span>
                                                    </div>
                                                </div>
                                                <div class="col-12 text-start mt-1 pt-1" style="border-top: 1px solid;">
                                                    <?php
                                                    $lastmeter_sum = collect($budgetyear)->sum('lastmeter');
                                                    $currentmeter_sum = collect($budgetyear)->sum('currentmeter');
                                                    $net = $currentmeter_sum - $lastmeter_sum;
                                                    $inv_period_count = collect($budgetyear)->count();
                                                    ?>
                                                    <p class="text-white text-sm  font-weight-bolder mt-auto mb-0">
                                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $inv_period_count }}
                                                        <sup>รอบบิล</sup>
                                                    </p>
                                                    <p class="text-white text-sm  font-weight-bolder mt-auto mb-0"> <span
                                                            class="text-sm"> ใช้น้ำ</span>
                                                        {{ $net }}<sup>ลิตร </sup></p>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="col-lg-9 mt-lg-0 mt-4">
                    <!-- ข้อมูล user -->
                    <div class="card card-body" id="profile">
                        <div class="row justify-content-left align-items-left">
                            <div class="col-sm-auto col-4">
                                <div class="avatar avatar-xl position-relative">
                                    <img src="{{ asset('soft-ui/assets/img/bruce-mars.jpg') }}" alt="bruce"
                                        class="w-100 border-radius-lg shadow-sm">
                                </div>
                            </div>
                            <div class="col-sm-auto col-8 my-auto">
                                <div class="h-100">
                                    <h5 class="mb-1 font-weight-bolder d-flex justify-content-between">
                                        <span>
                                            {{ $inv_by_budgetyear[0][0]['usermeterinfos']['user']['firstname'] . ' ' . $inv_by_budgetyear[0][0]['usermeterinfos']['user']['lastname'] }}
                                        </span>
                                        <span>
                                            {{ $inv_by_budgetyear[0][0]['usermeterinfos']['meternumber'] }}
                                        </span>
                                    </h5>
                                    <p class="mb-0 font-weight-bold text-sm">
                                        เส้นทาง
                                        {{ $inv_by_budgetyear[0][0]['usermeterinfos']['undertake_subzone']['undertake_subzone_name'] }}
                                        ::
                                        {{ $inv_by_budgetyear[0][0]['usermeterinfos']['user']['address'] }}
                                        {{ $inv_by_budgetyear[0][0]['usermeterinfos']['undertake_zone']['undertake_zone_name'] }}
                                        ต.
                                        {{ $inv_by_budgetyear[0][0]['usermeterinfos']['user']['user_tambon']['tambon_name'] }}
                                        อ.
                                        {{ $inv_by_budgetyear[0][0]['usermeterinfos']['user']['user_district']['district_name'] }}
                                        จ.
                                        {{ $inv_by_budgetyear[0][0]['usermeterinfos']['user']['user_province']['province_name'] }}
                                        {{-- &nbsp;{{ $inv_by_budgetyear[1][0]['usermeterinfos']['user']['user_tambon']['zipcode'] }} --}}

                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ตาราง ขวา -->
                    @foreach ($inv_by_budgetyear as $budgetyear)
                        <?php
                        $grouped = collect($budgetyear)->groupBy('accounts_id_fk');
                        ?>
                        <div class="row my-4" id="by{{ $budgetyear[0]['invoice_period']['budgetyear_id'] }}">
                            <div class="col-12">
                                <div
                                    class="card {{ $budgetyear[0]['invoice_period']['budgetyear']['status'] == 'active' ? 'bg-gradient-info' : 'bg-gradient-dark' }}">
                                    <div
                                        class="card-header {{ $budgetyear[0]['invoice_period']['budgetyear']['status'] == 'active' ? 'bg-gradient-info' : 'bg-gradient-dark' }}">

                                        <div class="card-title text-white fs-4 fw-bold">
                                            ปีงบประมาณ
                                            {{ $budgetyear[0]['invoice_period']['budgetyear']['budgetyear_name'] }}
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        @foreach ($grouped as $group)
                                            <div class="card mt-4">
                                                <div class="card-header">
                                                    <div class="card-title d-flex justify-content-between">
                                                        <span>เลขใบเสร็จรับเงิน{{ $group[0]['accounts_id_fk']}}</span>
                                                        <span class="text-end">{{ date_format(new DateTime($group[0]['updated_at']),'d-m-Y') }}</span>

                                                        <div class="text-right d-flex">
                                                            <a href="{{ route('payment.receipt_print_history',['account_id_fk' =>$group[0]['accounts_id_fk']]) }}" class="btn btn-primary btn-sm">ปริ้นใบเสร็จ</a>
                                                            <form action="{{ route('payment.destroy',$group[0]['accounts_id_fk']) }}" method="post">
                                                                @csrf
                                                                @method("DELETE")
                                                                &nbsp;<button type="button" class="btn btn-warning cancel_receipt_paper btn-sm">
                                                                    ยกเลิกใบเสร็จรับเงิน
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card-body">
                                                    <div class="table-responsive">
                                                        <table class="table align-items-center mb-0 ">
                                                            <thead>
                                                                <tr>
                                                                    <th
                                                                        class="text-uppercase text-secondary text-xxs font-weight-bolder text-center">
                                                                        เลขใบแจ้งหนี้
                                                                    </th>
                                                                    <th
                                                                        class="text-uppercase text-secondary text-xxs font-weight-bolder text-center ps-2">
                                                                        รอบบิล</th>
                                                                    <th
                                                                        class="text-uppercase text-secondary text-xxs font-weight-bolder text-center ps-2">
                                                                        ก่อนจดมิเตอร์<sup>หน่วย</sup></th>
                                                                    <th
                                                                        class="text-center text-uppercase text-secondary text-xxs font-weight-bolder text-center">
                                                                        หลังจดมิเตอร์<sup>หน่วย</sup></th>
                                                                    <th
                                                                        class="text-uppercase text-secondary text-xxs font-weight-bolder text-center ps-2">
                                                                        ค่าน้ำประปา<sup>บาท</sup></th>
                                                                    <th
                                                                        class="text-center text-uppercase text-secondary text-xxs font-weight-bolder text-center">
                                                                        รักษามิเตอร์<sup>บาท</sup></th>
                                                                    <th
                                                                        class="text-center text-uppercase text-secondary text-xxs font-weight-bolder text-center">
                                                                        รวมเป็นเงิน<sup>บาท</sup></th>
                                                                </tr>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php $sum = 0; ?>
                                                                @foreach ($group as $invoice)
                                                                    <?php
                                                                    $lastmeter = $invoice['lastmeter'];
                                                                    $currentmeter = $invoice['currentmeter'];
                                                                    $diff = $currentmeter - $lastmeter;
                                                                    $sum += $diff == 0 ? 10 : $diff * 8;

                                                                    ?>
                                                                    <tr>
                                                                        <td>

                                                                            <div class="text-center">
                                                                                <h6 class="mb-0 text-sm">
                                                                                    {{ $invoice['id'] }}</h6>
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            <span class="badge badge-dot me-4">
                                                                                <i class="bg-info"></i>
                                                                                <span
                                                                                    class="text-dark text-xs">{{ $invoice['invoice_period']['inv_p_name'] }}</span>
                                                                            </span>
                                                                        </td>
                                                                        <td class="align-middle text-center text-sm">
                                                                            <p class="text-secondary mb-0 text-sm">
                                                                                {{ $invoice['lastmeter'] }}</p>
                                                                        </td>
                                                                        <td class="align-middle text-center">
                                                                            <span
                                                                                class="text-secondary text-xs font-weight-bold">{{ $invoice['currentmeter'] }}</span>
                                                                        </td>
                                                                        <td class="align-middle text-center text-sm">
                                                                            <p class="text-secondary mb-0 text-sm">
                                                                                {{ $diff == 0 ? 0 : $diff * 8 }}</p>
                                                                        </td>
                                                                        <td class="align-middle text-center">
                                                                            <span
                                                                                class="text-secondary text-xs font-weight-bold">{{ $diff == 0 ? 10 : 0 }}</span>
                                                                        </td>
                                                                        <td class="align-middle text-center">
                                                                            <span
                                                                                class="text-secondary text-xs font-weight-bold">{{ $diff == 0 ? 10 : $diff * 8 }}</span>
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                                <tr>
                                                                    <td colspan="6" class="text-end"><b>รวมเป็นเงิน</b>
                                                                    </td>
                                                                    <td class="text-center">{{ number_format($sum, 2) }}
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

@endsection


@section('script')
    <script
        src="https://www.jqueryscript.net/demo/Export-Html-Table-To-Excel-Spreadsheet-using-jQuery-table2excel/src/jquery.table2excel.js">
    </script>
    <script src="https://code.jquery.com/ui/1.13.1/jquery-ui.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.1/bootstrap3-typeahead.min.js"></script>

    <script>
        $(document).ready(function() {
            $('.js-example-basic-single').select2();
        });
    </script>
@endsection
