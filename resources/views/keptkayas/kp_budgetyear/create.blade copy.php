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
th{
    text-align: center
}
    </style>
@endsection
@section('content')

    <form action="{{ route('admin.budgetyear.store') }}" id="form" method="POST" onsubmit="openLoader()">
        @csrf
        <button type="submit" class="btn btn-success mb-2 col-2">สร้างรอบบิลใหม่</button>

        <div class="card">
            <div class="card-header">
                <div class="row">

                <div class="form-group col-3">
                    <label for="budgetyear">ปีงบประมาณ
                        @error('budgetyear')
                            <span class="text-sm text-alert">({{ $message }}) </span>
                        @enderror
                    </label>
                    <input type="text" class="form-control text-center" readonly id="budgetyear" name="budgetyear"
                        value="{{ $budgetYear['budgetYear'] }}">
                </div>
                <div class="form-group col-3">
                    <label for="startdate">วันที่เริ่มปีงบประมาณ
                        @error('start')
                            <span class="text-sm text-alert">({{ $message }}) </span>
                        @enderror
                    </label>
                    <input class="form-control text-center datepicker" readonly type="text" name="start"
                        id="start" value="{{ $budgetYear['startDate'] }}">
                </div>
                <div class="form-group col-3">
                    <label for="exampleInputPassword2">วันที่สิ้นสุดปีงบประมาณ
                        @error('end')
                            <span class="text-sm text-alert">({{ $message }}) </span>
                        @enderror
                    </label>
                    <input class="form-control text-center datepicker" readonly type="text" name="end"
                        id="end" value="{{ $budgetYear['endDate'] }}">
                </div>

                <div class="form-group col-3" readyonly>
                    <label for="status">สถานะ</label>
                    <select name="status" id="status" class="form-control text-center">
                        <option value="active" selected>ปีงบประมาณปัจจุบัน</option>
                    </select>
                </div>

                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-4 col-sm-2">
                        <div class="nav flex-column nav-tabs h-100" id="vert-tabs-tab" role="tablist" aria-orientation="vertical">
                            @php
                                $i = 1;
                            @endphp
                            @foreach ($userGroups as $group)
                                <a class="nav-link {{ $i++ == 1 ? 'active' : '' }}" id="vert-group{{ $group->id }}-tab"
                                    data-toggle="pill" href="#vert-group{{ $group->id }}" role="tab"
                                    aria-controls="vert-group{{ $group->id }}"
                                    aria-selected="false">{{ $group->usergroup_name }}</a>
                            @endforeach

                        </div>
                    </div>
                    <div class="col-8 col-sm-10">
                        @php
                            $i = 1;
                        @endphp
                        <div class="tab-content" id="vert-tabs-tabContent">
                            @foreach ($userGroups as $group)
                                <div class="tab-pane text-left fade {{ $i++ == 1 ? 'active show' : '' }} "
                                    id="vert-group{{ $group->id }}" role="tabpanel"
                                    aria-labelledby="vert-group{{ $group->id }}-tab">
                                    @if (collect($group->kaya_user_infos)->isNotEmpty())
                                        <div class="card" style="border: 1px solid blue">
                                            <div class="card-header">
                                                {{$group->usergroup_name}}

                                            </div>
                                            <div class="card-body table-responsive">
                                                    <table class="table table-hover text-nowrap" id="isnvoiceTable">
                                                        <thead>
                                                            <tr>
                                                                <th>#</th>
                                                                <th>รหัส</th>
                                                                <th>ชื่อ-สกุล</th>
                                                                <th>ที่อยู่</th>
                                                                <th>หมู่</th>
                                                                <th>อัตราจ่ายต่อปี <sup>บาท</sup></th>
                                                                <th>จำนวนถัง <sup>ถัง</sup></th>
                                                                <th>สถานะ</th>
                                                            </tr>

                                                        </thead>
                                                        <tbody>
                                                            @php
                                                                $i = 1;
                                                            @endphp
                                                            @foreach ($group->kaya_user_infos as $item)
                                                            @php
                                                                $bin_qty = collect($item->user_payment_per_year)->isEmpty() ? 1 : collect($item->user_payment_per_year)->count();
                                                            @endphp
                                                            <tr>
                                                                <td>
                                                                    {{$i++}}
                                                                </td>
                                                                <td>{{$item->id}}</td>
                                                                <td>{{$item->user->prefix."".$item->user->firstname." ".$item->user->lastname }}</td>
                                                                <td class="text-end">{{$item->user->address}}</td>
                                                                <td class="text-center">{{$item->user->user_zone->zone_name}}</td>
                                                                <td class="text-center">{{$group->rate_payment_per_year}}</td>
                                                                <td>
                                                                    <input type="text" class="form-control" name="create[{{$item->id}}][bin_qty]" value="{{$bin_qty}}">
                                                                    <input type="hidden" value="{{$item->id}}" name="create[{{$item->id}}][kaya_user_infos_id_fk]">
                                                                    <input type="hidden" value="{{$group->id}}" name="create[{{$item->id}}][group_id]">
                                                                    <input type="hidden" value="{{$group->rate_payment_per_year}}" name="create[{{$item->id}}][rate_payment_per_year]">
                                                                </td>
                                                                <td>
                                                                    <select name="create[{{$item->id}}][status]}}" class="form-control" id="">
                                                                        <option value="active" selected>สมาชิกชำระค่าขยะรายปี</option>
                                                                        <option value="delete" >ยกเลิกงานใช้งาน</option>
                                                                        <option value="trashbank">ขอเป็นสมาชิกธนาคารขยะ</option>
                                                                    </select>
                                                                </td>
                                                            </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                            </div>
                                        </div>
                                    @else
                                        ss
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </form>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            $('.datepicker').datepicker({
                format: 'dd/m/yyyy',
                todayBtn: true,
                language: 'th', //เปลี่ยน label ต่างของ ปฏิทิน ให้เป็น ภาษาไทย   (ต้องใช้ไฟล์ bootstrap-datepicker.th.min.js นี้ด้วย)
                // thaiyear: true //Set เป็นปี พ.ศ.
            }).datepicker(); //กำหนดเป็นวันปัจุบัน



        });

        function openLoader() {
            $("#overlay").fadeIn(300);
        }
    </script>
@endsection
