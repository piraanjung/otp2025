@extends('layouts.adminlte')
@section('nav-header')
    รับซื้อขยะ
@endsection
@section('style')
<style>
    .hidden {
        display: none;
    }

    .show {
        display: block;
    }
    .icon-plus:hover{
        color:#007bff;
        font-size: 55px !important
    }

</style>
@endsection
@section('content')
    <script src="https://reeteshghimire.com.np/wp-content/uploads/2021/05/html5-qrcode.min_.js"></script>
    <!-- Header -->
    <div class="container-fluid header_se">
        {{ $member->prefix . ' ' . $member->firstname . '  ' . $member->lastname }}
        <div>
            @php
                echo $member->address;
                echo $member->kp_zone->zone_name;
            @endphp
        </div>
        <div class="col-md-8">
            <div class="row">
                <div class="col">
                    <div id="reader" class="hidden"></div>
                </div>
            </div>
            <script type="text/javascript">
                // after success to play camera Webcam Ajax paly to send data to Controller

                // function onScanSuccess(text) {
                //     let pattern = /[aA-zZ0-9]/g
                //     if (text.match(pattern).length > 6) {
                //         $.get(`/items/search_item/${text}`, function(data) {
                //             let results = JSON.parse(data)
                //             console.log('res', results)
                //             if (results.res === 1) {

                //                 var input, filter, ul, card, a, i, txtValue;
                //                 input = document.getElementById('myInput');
                //                 filter = results.product.productname.toUpperCase();
                //                 ul = document.getElementById("myUL");
                //                 card = ul.getElementsByClassName('card');

                //                 for (i = 0; i < card.length; i++) {
                //                     a = card[i].getElementsByClassName("productname")[0];
                //                     txtValue = a.textContent || a.innerText;
                //                     if (txtValue.toUpperCase().indexOf(filter) > -1) {
                //                         card[i].style.display = "";
                //                     } else {
                //                         card[i].style.display = "none";
                //                     }
                //                 }
                //             }
                //         })
                //     }
                // }
                // var html5QrcodeScanner = new Html5QrcodeScanner(
                //     "reader", {
                //         fps: 10,
                //         qrbox: 250
                //     });
                // html5QrcodeScanner.render(onScanSuccess);
            </script>
        </div>
    </div>



    <!-- Button trigger modal -->
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
        <i class="fa fa-shopping-cart" aria-hidden="true"></i> Cart <span class="badge badge-pill badge-danger">
            {{ Session::has('cart') ? count((array) session('cart')) : 0 }}



        </span>
    </button>

    <!-- Modal -->
    @if (Session::has('cart'))
        <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">
                            @php $total = 0 @endphp
                            @foreach ((array) session('cart') as $id => $details)
                                @php $total += $details['price'] * $details['quantity'] @endphp
                            @endforeach
                            <div class="col-lg-12 col-sm-12 col-12 total-section text-right">
                                <p>Total: <span class="text-info"> {{ $total }} บาท</span></p>
                            </div>
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        @foreach (session('cart') as $id => $details)
                            <div class="row cart-detail">
                                <div class="col-lg-3 col-sm-3 col-3 cart-detail-img">
                                    <img src="{{ asset('/adminlte/dist/img/AdminLTELogo.png') }}" width="60" height="60"
                                        class="img-responsive" style="border-radius: 50%" />
                                </div>
                                <div class="col-lg-9 col-sm-9 col-9 cart-detail-product">
                                    <b>{{ $details['items_name'] }}</b>
                                    <div class="row">
                                        <div class="col-2"></div>
                                        <div class="col-5">
                                            <span class="price text-info">
                                                <i class="fa fa-bitcoin"></i> {{ $details['price'] }}</span> <span
                                                class="count text-info">
                                                X {{ $details['quantity'] }}</span>
                                        </div>
                                        <div class="col-1"> = </div>
                                        <div class="col-3 text-info">
                                            {{ $details['quantity'] * $details['price'] }} <i class="fa fa-bitcoin"></i>
                                        </div>
                                    </div>

                                    <hr />
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="modal-footer">
                        <div class="row">
                            <div class="col-lg-12 col-sm-12 col-12 text-center checkout">
                                <a href="{{ route('cart.cart_lists', $member->id) }}" class="btn btn-primary btn-block">View all</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <input type="text" id="myInput" onkeyup="myFunction()" class="form-control mb-2 col-4"
        placeholder="Search for names..">
    <button class="btn btn-outline-primary h4 qrcode_btn"><i class="fa fa-qrcode"></i></button>

    <div id="myUL">
        <div class="row">

            @foreach ($favorite_items as $key => $item)
                <div class="col-md-6 col-sm-6 col-12 xx">
                    <div class="info-box bg-gradient-warning">
                        <span class="info-box-icon"><i class="far fa-calendar-alt"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text text-bold">
                                {{ $item->items_price_and_point_infos[0]['price_for_member'] }}<sup>บาท</sup> /
                                {{ $item->items_price_and_point_infos[0]->units_info->unit_name }}
                                ({{ $item->items_price_and_point_infos[0]['reward_point'] }}<sup>points</sup>)
                            </span>
                            <span class="info-box-number productname h4">{{ $item->itemsname }}
                                ({{ $item->itemscode }})</span>
                            <div class="progress">
                                <div class="progress-bar" style="width: 60%"></div>
                            </div>
                            <span class="progress-description">
                                <div class="input-group">
                                    <div class="input-group-prepend minus">
                                        <span class="input-group-text"><i class="fas fa-minus-circle"></i></span>
                                    </div>
                                    <input type="text" class="form-control col-md-4 text-center fw-bold" value="1"
                                        id="amount{{ $item->id }}">
                                    <div class="input-group-append plus">
                                        <span class="input-group-text"><i class="fa fa-plus-circle"></i></span>
                                    </div>
                                    <div class="input-group-append ">
                                        <span
                                            class="input-group-text">&nbsp;&nbsp;{{ $item->items_price_and_point_infos[0]->units_info->unit_name }}</span>
                                    </div>
                                </div>
                            </span>

                        </div>
                        <div class="icon icon-plus" style=" font-size: 50px"
                            onclick="addItem({{ $item->id }}, {{ $member->id }})"">
                            <i class="fas fa-plus-circle" style="position: absolute; right: 15px; top: 15px;"></i>
                        </div>
                    </div>

                </div>
            @endforeach
        </div><!--row-->
    </div><!-- myUl -->
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
                return false;
            });
            $(".plus").click(function() {
                var $input = $(this).parent().find("input");
                $input.val(parseInt($input.val()) + 1);
                $input.change();
                return false;
            });
        });

        function addItem(item_id, user_id) {
            let amount = $(`#amount${item_id}`).val();
            $.get(`/cart/add_to_cart/${item_id}/${amount}`).done((v) => {
                window.location.href = "/items/buyItems/" + user_id
            });
        }

        function myFunction() {
            var input, filter, ul, card, a, i, txtValue;
            input = document.getElementById('myInput');
            filter = input.value.toUpperCase();

            $( ".xx" ).each(function( index ) {
               if( $(this).find('.productname').text().toUpperCase().indexOf(filter) > -1) {
                 $(this).removeClass('hidden')
               }else{
                $(this).addClass('hidden')
               }
            });

        }

        $('.qrcode_btn').click(function() {
            if ($('#reader').hasClass('hidden')) {
                $('#reader').removeClass('hidden');
                $('#reader').addClass('show')
            } else {
                $('#reader').removeClass('show');

                $('#reader').addClass('hidden');
            }

        })
    </script>
@endsection
