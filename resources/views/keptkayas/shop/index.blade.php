@extends('layouts.user_mobile')
@section('style')
    <style>
        #prices {
            .area {
                position: fixed;
                top: 1%;
                left: 10%;
                z-index: 9999;
                /* margin-top: -15px;
                margin-left: -15px; */
            }

            .toggle {
                display: block;
                position: relative;
                background: #1063e1;
                border: none;
                width: 44px;
                height: 44px;
                line-height: 44px;
                text-align: center;
                border-radius: 100%;
                cursor: pointer;
                border: 4px solid rgba(255, 255, 255, 0.3);
                transition: all 0.2s ease;
                z-index: 4;
                outline: none;

                &:hover {
                    transform: scale(0.8);
                }

                &.active {
                    transform: none;
                    border: 4px solid #0ADACD;
                }

                &.active .icon {
                    transform: rotate(315deg);
                }

                &.active~.card {
                    opacity: 1;
                    transform: rotate(0);
                }

                &.active~.card h1,
                &.active~.card p {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .bubble {
                position: absolute;
                border: none;
                width: 40px;
                height: 40px;
                border-radius: 100%;
                cursor: pointer;
                transition: all 0.3s ease;
                z-index: 1;
                top: 2px;
                left: 2px;

                &:before {
                    content: "";
                    position: absolute;
                    width: 120px;
                    height: 120px;
                    opacity: 0;
                    border-radius: 100%;
                    top: -40px;
                    left: -40px;
                    background: #8ED6D1;
                    animation: bubble 3s ease-out infinite;
                }

                &:after {
                    content: "";
                    position: absolute;
                    width: 120px;
                    height: 120px;
                    opacity: 0;
                    border-radius: 100%;
                    top: -40px;
                    left: -40px;
                    z-index: 1;
                    background: #8ED6D1;
                    animation: bubble 3s 0.8s ease-out infinite;
                }
            }

            .icon {
                display: inline-block;
                position: relative;
                width: 16px;
                height: 16px;
                z-index: 5;
                transition: all 0.3s ease-out;

                .horizontal {
                    position: absolute;
                    width: 100%;
                    height: 4px;
                    background: linear-gradient(-134deg, #6DCD42 0%, #0ADACD 100%);
                    top: 50%;
                    margin-top: -2px;
                }

                .vertical {
                    position: absolute;
                    width: 4px;
                    height: 100%;
                    left: 50%;
                    margin-left: -2px;
                    background: linear-gradient(-134deg, #6DCD42 0%, #0ADACD 100%);
                }
            }

            @keyframes bubble {
                0% {
                    opacity: 0;
                    transform: scale(0);
                }

                5% {
                    opacity: 1;
                }

                100% {
                    opacity: 0;
                }
            }

            .card {
                width: 300px;
                padding: 10px overflow: hidden;
                position: relative;
                background: #FCFCFC;
                border-radius: 10px;
                margin-top: -25px;
                margin-left: 25px;
                opacity: 0;
                transform: rotate(5deg);
                transform-origin: top left;
                transition: all 0.2s ease-out;

                &-content {
                    display: inline-block;
                    vertical-align: middle;
                    width: 350px;
                    padding: 30px;
                    border-right: 1px solid #f0f0f0;
                }

                &-more {
                    display: inline-block;
                    vertical-align: middle;
                    width: 40px;
                    height: auto;
                    text-align: center;

                    p {
                        color: #0ADACD;
                        font-size: 2em;
                        font-weight: 700;
                        line-height: 1;
                        cursor: pointer;
                    }
                }
            }

            h1 {
                font-size: 1.2em;
                font-weight: 700;
                line-height: 1;
                margin-bottom: 10px;
                opacity: 0;
                color: #222222;
                transform: translateY(60%);
                transition: all 0.3s 0.2s ease-out;
            }

            p {
                font-size: 1em;
                line-height: 1.2;
                opacity: 0;
                color: #434343;
                transform: translateY(60%);
                transition: all 0.3s 0.2s ease-out;
            }
        }
    </style>
@endsection
@section('content')
    @php
        $memberCashs = $member->balance ?? 0;
        $memberPoints = $member->points ?? 0;
    @endphp
    <div>
        @if(Session::has('shop_cart'))
            @php
                $carts = Session::get('shop_cart');
                // dd($carts);
                $cashTotal = 0;
                $pointsTotal = 0;
                foreach ($carts as $cart) {
                    $cashTotal += $cart['cash']['total_cash'];
                    $pointsTotal += $cart['points']['total_points'];
                }
                $remainingCash = $memberCashs - $cashTotal;
                $remainingPoints = $memberPoints - $pointsTotal;
            @endphp
        @else
            @php
                $remainingCash = $memberCashs;
                $remainingPoints = $memberPoints;
            @endphp
        @endif

        <div id="prices">
            <div class="area">
                <div class="toggle">
                    <div class="icon">
                        <div class="horizontal"></div>
                        <div class="vertical"></div>
                    </div>
                </div>
                <div class="bubble"></div>
                <div class="card hidden">
                    <div class="card-content">

                        <div class="row">
                            <div class="col-lg-3 col-6 text-center">
                                <div class="border-dashed border-1 border-secondary border-radius-md py-3">
                                    <h6 class="text-primary text-gradient mb-0">เงินจริง</h6>
                                    <h4 class="font-weight-bolder"><span class="small">$ </span><span id="member_balance"
                                            countto="23980">{{ number_format($memberCashs, 2) }}</span></h4>
                                
                                    <h6 class="text-primary text-gradient mb-0">เงินคงเหลือ</h6>
                                    <h4 class="font-weight-bolder"><span class="small">$ </span><span id="member_remaining_balance"
                                            countto="23980">{{ number_format($remainingCash, 2) }}</span></h4>
                                </div>
                            </div>


                            <div class="col-lg-3 col-6 text-center">
                                <div class="border-dashed border-1 border-secondary border-radius-md py-3">
                                    <h6 class="text-primary text-gradient mb-0">แต้มจริง</h6>
                                    <h4 class="font-weight-bolder"><span id="member_point" countto="4">
                                        {{ number_format($memberPoints) }}</span></h4>
                                     <h6 class="text-primary text-gradient mb-0">แต้มคงเหลือ</h6>
                                    <h4 class="font-weight-bolder"><span id="member_remaining_point" countto="4">
                                        {{ number_format($remainingPoints) }}</span></h4>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>

    </div>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="mb-0">KU Shop</h1>
            <a href="{{ route('keptkayas.shop.cart') }}" class="btn btn-primary position-relative">
                <i class="bi bi-cart-fill me-1"></i> ตะกร้าของฉัน
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                    {{ Session::has('shop_cart') ? count(Session::get('shop_cart')) : 0 }}
                </span>
            </a>
        </div>
        <a href="javascript:void(0)"  class="btn btn-success cate_btn" data-cate_id="0" id="cate0">All</a>
        @foreach ($product_categorys as $product_category)
            <a href="javascript:void(0)" id="cate{{$product_category->id}}" data-cate_id="{{$product_category->id}}" class="btn btn-info cate_btn">{{$product_category->category_name}}</a> 
        @endforeach
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <h6>โปรดแก้ไขข้อผิดพลาดดังต่อไปนี้:</h6>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
            @forelse($products as $product)
                <div class="col">
                    <div class="card h-100 shadow-sm product product_cate{{$product->kp_shop_category_id}}">
                        @php
                            $imagePath = $product->image_path ? $product->image_path : 'placeholder.jpg';
                        @endphp
                        <img src="{{ asset( $imagePath) }}" class="card-img-top" alt="{{ $product->product_name }}">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">{{ $product->product_name }}</h5>
                            <p class="card-text text-muted">{{ Str::limit($product->description, 70) }}</p>
                            <div class="mt-auto">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-success h5 mb-0"
                                        data-point-price="{{ $product->point_price }}">{{ number_format($product->point_price) }}
                                        แต้ม</span>
                                    <span class="text-info h6 mb-0"
                                        data-cash-price="{{ $product->cash_price }}">{{ number_format($product->cash_price, 2) }}
                                        บาท</span>
                                </div>
                                @php
                                    $canPayWithPoints = $remainingPoints >= $product->point_price;
                                    $canPayWithCash = $remainingCash >= $product->cash_price;
                                    $canAfford = $canPayWithPoints || $canPayWithCash;
                                @endphp

                                @if ($canAfford)
                                    <form action="{{ route('keptkayas.shop.add_to_cart') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                                        <div class="mb-2">
                                            <label for="payment_method_{{ $product->id }}"
                                                class="form-label visually-hidden">วิธีการชำระ</label>
                                            <select name="payment_method" id="payment_method_{{ $product->id }}"
                                                class="form-select payment-method-select" required
                                                data-product-id="{{ $product->id }}">
                                                <option value="">เลือกวิธีชำระ</option>
                                                @if ($canPayWithPoints)
                                                    <option value="points">แต้ม ({{ number_format($product->point_price) }})</option>
                                                @endif
                                                @if ($canPayWithCash)
                                                    <option value="cash">เงินสด ({{ number_format($product->cash_price, 2) }})</option>
                                                @endif
                                            </select>
                                        </div>
                                        <div class="input-group mb-2">
                                            <span class="input-group-text">จำนวน</span>
                                            <input type="number" name="quantity" class="form-control quantity-input" value="1"
                                                min="1" required data-product-id="{{ $product->id }}">
                                        </div>
                                        <button type="submit" class="btn btn-success w-100">
                                            <i class="bi bi-bag-plus-fill me-1"></i> เพิ่มในตะกร้า
                                        </button>
                                    </form>
                                @else
                                    <div class="alert alert-warning text-white text-center p-2 mb-0">เงิน/แต้มไม่พอ</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-warning text-center">
                        ไม่พบสินค้าในระบบ
                    </div>
                </div>
            @endforelse
        </div>
@endsection

@section('script')
    <script>
        $(document).on('click', '.cate_btn', function(){
           let cate_id = $(this).data('cate_id')
            console.log('cate_id', cate_id)
            $('.product').addClass('hidden')
           if(cate_id == 0){
            $('.product').removeClass('hidden')
           }else{
            $('.product_cate'+cate_id).removeClass('hidden')
           }

        })

        $('.area').click(function(){
            if($('#prices .card').hasClass('hidden')){
                $('#prices .card').removeClass('hidden')
            }else{
                $('#prices .card').addClass('hidden')
            }
        })
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const memberBalance = parseFloat(document.getElementById('member_balance').textContent.replace(/,/g, ''));
            const memberPoints = parseInt(document.getElementById('member_point').textContent.replace(/,/g, ''));

            const remainingCashElement = document.getElementById('member_remaining_balance');
            const remainingPointsElement = document.getElementById('member_remaining_point');

            const allForms = document.querySelectorAll('.card-body form');

            const initialCartCash = parseFloat('{{ $cashTotal ?? 0 }}');
            const initialCartPoints = parseInt('{{ $pointsTotal ?? 0 }}');

            function formatNumber(num) {
                return num.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
            }

            function number_format(num) {
                return new Intl.NumberFormat('th-TH').format(num);
            }

            function updateRemainingBalance() {
                let totalCash = initialCartCash;
                let totalPoints = initialCartPoints;

                allForms.forEach(form => {
                    const quantityInput = form.querySelector('input[name="quantity"]');
                    const paymentMethodInput = form.querySelector('select[name="payment_method"]');

                    const quantity = parseInt(quantityInput.value) || 0;
                    const paymentMethod = paymentMethodInput.value;

                    const productCard = form.closest('.card');
                    const pointPrice = parseFloat(productCard.querySelector('[data-point-price]').dataset.pointPrice);
                    const cashPrice = parseFloat(productCard.querySelector('[data-cash-price]').dataset.cashPrice);

                    if (paymentMethod === 'cash') {
                        totalCash += cashPrice * quantity;
                    } else if (paymentMethod === 'points') {
                        totalPoints += pointPrice * quantity;
                    }
                });

                const newRemainingCash = memberBalance - totalCash;
                const newRemainingPoints = memberPoints - totalPoints;

                remainingCashElement.textContent = formatNumber(newRemainingCash);
                remainingPointsElement.textContent = number_format(newRemainingPoints);
            }

            allForms.forEach(form => {
                const quantityInput = form.querySelector('input[name="quantity"]');
                const paymentMethodSelect = form.querySelector('select[name="payment_method"]');

                if (quantityInput) {
                    quantityInput.addEventListener('input', updateRemainingBalance);
                }
                if (paymentMethodSelect) {
                    paymentMethodSelect.addEventListener('change', updateRemainingBalance);
                }
            });

            // Initial update
            updateRemainingBalance();
        });


        var selector = document.querySelector(".toggle");

        selector.addEventListener("click", function () {
            if (this.classList.contains("active")) {
                this.className = "toggle";
            } else {
                this.className += " active";
            }
        });
    </script>
@endsection