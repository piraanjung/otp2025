@extends('layouts.keptkaya')

@section('nav-cart')
    active
@endsection
@section('nav-header')
    รายการรับซื้อ
@endsection
@section('nav-main')
    <a href="{{ route('keptkaya.tbank.cart.index') }}"> รับซื้อขยะ</a>
@endsection
@section('nav-current')
    รับซื้อขยะ
@endsection
@section('page-topic')
    รับซื้อขยะ
@endsection

@section('content')
    <div class="row">
        <div class="col-md-3">

            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                    <div class="text-center">
                        <img class="profile-user-img img-fluid img-circle" src="{{asset('adminlte/dist/img/user4-128x128.jpg')}}"
                            alt="User profile picture">
                    </div>
                    <h3 class="profile-username text-center">Nina Mcintire</h3>
                    <p class="text-muted text-center">Software Engineer</p>
                    <ul class="list-group list-group-unbordered mb-3">
                        <li class="list-group-item">
                            <b>Followers</b> <a class="float-right">1,322</a>
                        </li>
                        <li class="list-group-item">
                            <b>Following</b> <a class="float-right">543</a>
                        </li>
                        <li class="list-group-item">
                            <b>Friends</b> <a class="float-right">13,287</a>
                        </li>
                    </ul>
                    <a href="#" class="btn btn-primary btn-block"><b>Follow</b></a>
                </div>

            </div>




        </div>

        <div class="col-md-9">
            <div class="card">
                <div class="card-header p-2">
                    รายการขยะ
                </div>
                <div class="card-body">
                    @foreach (session('cart') as $id => $details)
                        <div class="info-box bg-gradient-warning">
                            <span class="info-box-icon"><i class="far fa-calendar-alt"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text text-bold">
                                    {{ $details['price'] }}<sup>บาท</sup> /
                                    {{-- {{ $item->items_price_and_point_infos[0]->units_info->unit_name }} --}}
                                    ({{ $details['reward_point'] }}<sup>points</sup>)
                                </span>
                                <span class="info-box-number productname h4">
                                    {{ $details['items_name']}}
                                </span>
                                <div class="progress">
                                    <div class="progress-bar" style="width: 60%"></div>
                                </div>
                                <div class="row">
                                <span class="progress-description col-5">
                                    <div class="input-group">
                                        <div class="input-group-prepend minus">
                                            <span class="input-group-text"><i class="fas fa-minus-circle"></i></span>
                                        </div>
                                        <input type="text" class="form-control col-md-4 text-center fw-bold" value="{{$details['quantity']}}"
                                            id="amount{{ $id }}">
                                            {{-- $item->id --}}
                                        <div class="input-group-append plus">
                                            <span class="input-group-text"><i class="fa fa-plus-circle"></i></span>
                                        </div>
                                        <div class="input-group-append ">
                                            <span
                                                class="input-group-text">
                                                {{-- &nbsp;&nbsp;{{ $item->items_price_and_point_infos[0]->units_info->unit_name }} --}}
                                            </span>
                                        </div>
                                    </div>
                                </span>
                                <span class="progress-description col-5">
                                    <div class="row">
                                        <div class="col-5">
                                            <span class="price">
                                                <i class="fa fa-bitcoin"></i> </span>
                                                จำนวน <b class="qty"> {{ $details['quantity'] }}</b>
                                                <span class="count">
                                                X  {{ $details['price'] == "" ? 0 : $details['price'] }} บาท</span>
                                        </div>
                                        <div class="col-2"> = </div>
                                        <div class="col-3">
                                            {{ $details['quantity'] * $details['price'] }} <i class="fa fa-bitcoin"></i>
                                        </div>
                                    </div>

                                </span>
                            </div>

                            </div>
                            <div class="icon icon-plus" style=" font-size: 50px"
                                onclick="delItem()">
                                <i class="fas fa-trash text-danger"  style="position: absolute; right: 15px; top: 15px;"></i>
                            </div>
                        </div>

                    @endforeach
                </div>
            </div>

        </div>

    </div>

@endsection

@section('script')
<script>
      $(document).ready(function() {
            $(".minus").click(function() {
                var $input = $(this).parent().find("input");
                var count = parseInt($input.val()) - 1;
                count = count < 1 ? 1 : count;
                $input.val(count);
                $input.change();
                changeQtyText(count)
                return false;
            });
            $(".plus").click(function() {
                var $input = $(this).parent().find("input");
                var count =parseInt($input.val()) + 1;
                $input.val(count);
                $input.change();
                changeQtyText(count)
                return false;
            });
        });

        function changeQtyText(count){
            $('.qty').html(count)
        }
</script>
@endsection
