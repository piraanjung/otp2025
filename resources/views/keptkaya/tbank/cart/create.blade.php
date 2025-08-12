@extends('layouts.keptkaya')

@section('nav-cart')
    active
@endsection
@section('nav-header')
    รับซื้อขยะ
@endsection
@section('nav-main')
    <a href="{{ route('keptkaya.tbank.cart.index') }}"> รับซื้อขยะ</a>
@endsection
@section('nav-current')
    ค้นหาชื่อสมาชิก
@endsection
@section('page-topic')
    ค้นหาชื่อสมาชิก
@endsection
@section('style')
    <style>
        .for-mobile {
            display: none;
        }

        @media only screen and (max-width: 600px) {
            .forweb {
                display: none;
            }

            .for-mobile {
                display: block;
            }

            .form-control {
                width: 19rem !important
            }
        }
    </style>
@endsection
@section('content')
    {{-- <div class="card forweb" style="border: 1px solid blue">

        <div class="card-body">
            <div class="table-responsive ">
                <table class="table" id="search_member">
                    <thead>
                        <th>#</th>
                        <th>ชื่อ</th>
                        <th>เลขมิเตอร์</th>
                        <th>บ้านเลขที่</th>
                        <th>หมู่</th>
                        <th>เส้นทางจดมิเตอร์</th>
                        <th>จำนวนเงิน(บาท)</th>
                        <th>หมายเหตุ</th>
                    </thead>
                    <tbody>
                        @foreach ($members as $member)
                                        <tr>
                                            <td class="text-center">{{ $member->user->firstname }}</td>
                                        </tr>
                                    @endforeach
                    </tbody>
                </table>
            </div>

        </div>
    </div> --}}
    <table class="table" id="search_member">
        <thead>
            <tr>
                <td></td>
            </tr>
        </thead>
        <tbody>
           
            @foreach ($members as $member)
                <tr>
                    <td>
                        <div class="card ">
                            <div class="card-body p-3">
                                <div class="row">
                                    <div class="col-8">
                                        <div class="numbers">
                                            <p class="text-sm mb-0 text-capitalize font-weight-bold"></p>
                                            <h5 class="font-weight-bolder mb-0">
                                                {{ $member->user->firstname . ' ' . $member->user->lastname }}
                                                <div class="text-success text-sm font-weight-bolder">
                                                    {{$member->user->address }}
                                                    {{$member->user->user_zone->zone_name}}
                                                    {{$member->user->user_subzone->subzone_name}}
                                                    {{$member->user->user_tambon->tambon_name}}
                                                    {{$member->user->user_district->district_name}}
                                                    {{$member->user->user_province->province_name}}
{{-- 
                                                    {{$member->trash_zone->zone_name}}
                                                    {{$member->trash_subzone->subzone_name}} --}}

                                                </div>
                                            </h5>
                                        </div>
                                    </div>
                                    <div class="col-4 text-end">
                                        <a href="{{route('keptkaya.tbank.cart.cart_lists',$member->id)}}" class="btn btn-info">รับซื้อขยะ</a>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>

    </table>
    {{-- <div class="for-mobile">
        @include('keptkaya.tbank.cart.create_for_mobile')
    </div> --}}
@endsection

@section('script')
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $('#search_member').DataTable({
            "bLengthChange": false, //thought this line could hide the LengthMenu
            "bInfo": false,
            "bPaginate": false
        })
    </script>
@endsection
