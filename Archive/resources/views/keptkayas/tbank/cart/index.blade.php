@extends('layouts.keptkaya')

@section('nav-cart')
    active
@endsection
@section('nav-header')
    รับซื้อขยะ
@endsection
@section('nav-main')
    <a href="{{route('keptkayas.tbank.cart.index')}}"> รับซื้อขยะ</a>
@endsection
@section('nav-current')
    รับซื้อขยะ
@endsection
@section('page-topic')
    รับซื้อขยะ
@endsection

@section('content')
    <div class="container-fluid my-3 py-3">
        <div class="row">
            <div class="col-12">
                <div class="card" style="border: 1px solid blue">
                    <div class="card-header">
                        <div class="card-title"></div>
                        <div class="card-tools">
                            <a href="{{route('keptkayas.tbank.cart.create')}}" class="btn btn-info">รับซื้อขยะ</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table" id="invoiceTable">
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
                                    {{-- @foreach ($owe_users as $owe_user)
                                    <tr>
                                        <td class="text-center">{{ $owe_user->meter_id_fk }}</td>
                                        <td>{{ $owe_user->user->firstname . ' ' . $owe_user->user->lastname }}
                                        </td>
                                        <td class="meternumber text-center" data-meter_id={{ $owe_user->meter_id_fk }}>
                                            {{ $owe_user->meternumber }}
                                        </td>
                                        <td class="text-center">{{ $owe_user->user->address }}</td>
                                        <td class="text-center">{{ $owe_user->undertake_zone->zone_name }}</td>
                                        <td class="text-center">{{ $owe_user->undertake_subzone->subzone_name }}</td>
                                        <td class="text-center">{{ $owe_user->owe_count }}</td>
                                        <td>{{ $owe_user->comment }}</td>
                                    </tr>
                                    @endforeach --}}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@section('script')

@endsection