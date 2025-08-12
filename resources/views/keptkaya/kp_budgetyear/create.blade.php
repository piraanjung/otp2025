@extends('layouts.adminlte')

@section('mainheader')
    สร้างปีงบประมาณ
@endsection
@section('budgetyear-show')
    show
@endsection
@section('nav-budgetyear-header')
    active
@endsection
@section('nav-budgetyear')
    active
@endsection

@section('nav-header')
    ปีงบประมาณ
@endsection
@section('route')
    {{route('admin.kp_budgetyear.index')}}
@endsection
@section('style')
    <script src="https://code.jquery.com/jquery-1.12.4.js" integrity="sha256-Qw82+bXyGq6MydymqBxNPYTaUXXq7c8v3CwiYwLLNXU="
        crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"
        integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <script type="text/javascript" src="{{ asset('js/bootstrap-datepicker/js/bootstrap-datepicker-thai.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/bootstrap-datepicker/js/locales/bootstrap-datepicker.th.js') }}">
    </script>
    <style>
        .datepicker .active {
            color: red !important
        }



        .cv-spinner {
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .spinner {
            width: 40px;
            height: 40px;
            border: 4px #ddd solid;
            border-top: 4px #2e93e6 solid;
            border-radius: 50%;
            animation: sp-anime 0.8s infinite linear;
        }

        @keyframes sp-anime {
            100% {
                transform: rotate(360deg);
            }
        }

        .is-hide {
            display: none;
        }

        #checkboxPrimary2 {
            min-height: 30px;
            margin-top: 0px !important;
            margin-bottom: 0px !important;
            width: 20px !important;
            padding-left: 0;
        }

        th {
            text-align: center
        }
    </style>
@endsection
@section('content')

    <form action="{{ route('admin.kp_budgetyear.store') }}" id="form" method="POST" onsubmit="openLoader()">
        @csrf
        <input type="hidden" name="kp_budgetyear_id" value="{{$budgetYear['budgetyear_id']}}">
        <div class="row">
            <div class="col-6">
                <div class="card card-info card-outline direct-chat direct-chat-primary shadow-none">
                    <div class="card-header">
                        <h3 class="card-title">ข้อมูลปีงบประมาณใหม่</h3>

                        <div class="card-tools">

                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-2">

                        <div class="form-group row">
                            <label for="inputEmail3" class="col-sm-4 col-form-label">ปีงบประมาณ
                                @error('budgetyear')
                                    <span class="text-sm text-alert">({{ $message }}) </span>
                                @enderror
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control text-center" readonly id="budgetyear"
                                    name="budgetyear" value="{{ $budgetYear['budgetYear'] }}">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="inputEmail3" class="col-sm-4 col-form-label">วันที่เริ่มปีงบประมาณ
                                @error('start')
                                    <span class="text-sm text-alert">({{ $message }}) </span>
                                @enderror
                            </label>
                            <div class="col-sm-8">
                                <input class="form-control text-center datepicker" readonly type="text" name="start"
                                    id="start" value="{{ $budgetYear['startDate'] }}">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="inputEmail3" class="col-sm-4 col-form-label">วันที่สิ้นสุดปีงบประมาณ
                                @error('end')
                                    <span class="text-sm text-alert">({{ $message }}) </span>
                                @enderror
                            </label>
                            <div class="col-sm-8">
                                <input class="form-control text-center datepicker" readonly type="text" name="end" id="end"
                                    value="{{ $budgetYear['endDate'] }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="card card-primary card-outline direct-chat direct-chat-primary shadow-none">
                    <div class="card-header">
                        <h3 class="card-title">อัตราจ่ายต่อเดือน</h3>

                        <div class="card-tools">

                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>


                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <!-- Conversations are loaded here -->
                        <div class="direct-chat-messages" style="height: auto !important">
                            @php
                                $i = 0;
                            @endphp
                            @foreach ($usergroups as $usergroup)
                            @php
                                if(collect($usergroup->kp_usergroup_payrate_permonth)->isEmpty()){
                                    $payrate_permonth = 0;
                                    $vat = 0;
                                }else {
                                    $payrate_permonth = $usergroup->kp_usergroup_payrate_permonth[0]->payrate_permonth;
                                    $vat = $usergroup->kp_usergroup_payrate_permonth[0]->vat;
                                }
                            @endphp  
                            <div class="row">
                                <div class="col-8">
                                    <div class="input-group mb-3">
                                        <span class="input-group-prepend">
                                            <button type="input" class="btn btn-primary" style="width: 150px">
                                                {{$usergroup->usergroup_name}}
                                            </button>
                                        </span>
                                        <input type="text" name="payrate[{{$i}}][ratepermonth]"
                                            value="{{$payrate_permonth}}"
                                            class="form-control text-center">
                                        <input type="hidden" name="payrate[{{$i}}][usergroup]"
                                            value="{{$usergroup->id}}">

                                        <span class="input-group-append">
                                            <button type="input" class="btn btn-warning">บาท/เดือน</button>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="input-group mb-3">
                                        <span class="input-group-prepend">
                                            <button type="input" class="btn btn-primary" style="width: 50px">
                                            Vat
                                            </button>
                                        </span>
                                        <input type="text" name="payrate[{{$i}}][vat]"
                                            value="{{$vat}}"
                                            class="form-control text-center">
                                        <input type="hidden" name="payrate[{{$i++}}][usergroup]"
                                            value="{{$usergroup->id}}">

                                        <span class="input-group-append">
                                            <button type="input" class="btn btn-warning">%</button>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    <!-- /.card-body -->

                </div>

            </div>
            <div class="col-12 text-right">
                <button type="submit" class="btn btn-success mb-2 col-2">บันทึก</button>
            </div>

        </div>
    </form>
@endsection

@section('script')
    <script>
        // $(document).ready(function () {
        //     $('.datepicker').datepicker({
        //         format: 'dd/m/yyyy',
        //         todayBtn: true,
        //         language: 'th', //เปลี่ยน label ต่างของ ปฏิทิน ให้เป็น ภาษาไทย   (ต้องใช้ไฟล์ bootstrap-datepicker.th.min.js นี้ด้วย)
        //         // thaiyear: true //Set เป็นปี พ.ศ.
        //     }).datepicker(); //กำหนดเป็นวันปัจุบัน



        // });

        function openLoader() {
            $("#overlay").fadeIn(300);
        }
    </script>
@endsection