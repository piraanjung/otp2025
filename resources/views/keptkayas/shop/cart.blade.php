@extends('layouts.user_mobile')

@section('content')
{{-- <div class="container-fluid"> --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">ตะกร้าสินค้าของฉัน</h1>
        <a href="{{ route('keptkayas.shop.index') }}" class="btn btn-outline-primary">
            <i class="bi bi-arrow-left me-1"></i> เลือกซื้อสินค้าเพิ่ม
        </a>
    </div>

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

    @if(!Session::has('shop_cart') || empty(Session::get('shop_cart')))
        <div class="alert alert-info text-center">ยังไม่มีสินค้าในตะกร้า</div>
    @else
        <div class="card mb-4 shadow-sm  d-none d-md-block">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0">รายการสินค้าในตะกร้า</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive ">
                    <table class="table table-striped table-hover align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>สินค้า</th>
                                <th>แลกโดยใช้</th>
                                <th>จำนวน</th>
                                <th>ราคา/หน่วย</th>
                                <th>ยอดเงิน</th>
                                <th>แต้ม</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $cart = Session::get('shop_cart');
                                $grandTotal = 0;
                                $grandPoints = 0;
                            @endphp
                            @foreach ($cart as $index => $item)
                                @php
                                    $grandTotal += $item['cash']['total_cash'];
                                    $grandPoints += $item['points']['total_points'];
                                @endphp
                                @foreach (['cash', 'points'] as $payType)
                                 @if ($item[$payType]['quantity'] > 0)
                                 
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ asset('storage/' . $item['image_path']) }}" alt="{{ $item['product_name'] }}" style="width: 50px; height: 50px; object-fit: cover;" class="rounded me-2">
                                            <span class="fw-bold">{{ $item['product_name'] }}</span>
                                        </div>
                                    </td>
                                    <td>{{ $payType }}</td>
                                    <td>{{ number_format($item[$payType]['quantity']) }} </td>
                                    {{-- {{ $item['unit_name'] }} --}}
                                    <td>{{ number_format($item['point_price']) }} แต้ม / {{ number_format($item['cash_price'], 2) }} บาท</td>
                                    <td>{{ number_format($item[$payType]['total_cash'], 2) }} บาท</td>
                                    <td>{{ number_format($item[$payType]['total_points']) }} แต้ม</td>
                                    <td>
                                        <form action="{{ route('keptkayas.shop.remove_from_cart', $index) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="pay_type" value="{{$payType}}">
                                            <button type="submit" class="btn btn-danger btn-sm" title="ลบออกจากตะกร้า">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endif   
                               
                                 @endforeach
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="5" class="text-end">ยอดรวมทั้งหมด:</th>
                                <th>{{ number_format($grandTotal, 2) }} บาท</th>
                                <th>{{ number_format($grandPoints) }} แต้ม</th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

            </div>
        </div>
        
        <div class="card d-md-none mt-4">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0">รายการในตะกร้า</h5>
            </div>
            <div class="card-body">
                @php
                    $cart = Session::get('shop_cart');
                    $grandTotal = 0;
                    $grandPoints = 0;
                @endphp
                @foreach ($cart as $index => $item)
                    @php
                        $grandTotal += $item['cash']['total_cash'];
                        $grandPoints += $item['points']['total_points'];
                    @endphp
                     @foreach (['cash', 'points'] as $payType)
                                 @if ($item[$payType]['quantity'] > 0)
                                    <div class="card mb-3 shadow-sm">
                                        <div class="row g-0">
                                            <div class="col-9 p-2">
                                                <img src="{{ asset('storage/' . $item['image_path']) }}" class="img-fluid rounded-start h-100 w-80" style="object-fit: cover;" alt="{{ $item['product_name'] }}">
                                            </div>
                                            <div class="col-3 pt-2">
                                                <form action="{{ route('keptkayas.shop.remove_from_cart', $index) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm" title="ลบออกจากตะกร้า">
                                                        ลบ
                                                        </button>
                                                    </form>
                                            </div>
                                            <div class="col-12">
                                                <div class="card-body">
                                                    <h6 class="card-title">{{ $item['product_name'] }}</h6>
                                                    <p class="card-text mb-1">
                                                        {{-- {{ $item['unit_name'] }} --}}
                                                        <small class="text-muted">จำนวน: {{ number_format($item[$payType]['quantity']) }}</small><br>
                                                        <small class="text-muted">ราคา/หน่วย: {{ number_format($item['point_price']) }} แต้ม / {{ number_format($item['cash_price'], 2) }} บาท</small>
                                                    </p>
                                                    <p class="h6 mb-1">ยอดเงิน: <span class="text-info">{{ number_format($item[$payType]['total_cash'], 2) }} บาท</span></p>
                                                    <p class="h6 mb-2">แต้ม: <span class="text-success">{{ number_format($item[$payType]['total_points']) }} แต้ม</span></p>
                                                    
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                    @endforeach
                    
                @endforeach
                <div class="card mt-3 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-1">
                            <strong>ยอดรวมเงิน:</strong> <span>{{ number_format($grandTotal, 2) }} บาท</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <strong>ยอดรวมแต้ม:</strong> <span>{{ number_format($grandPoints) }} แต้ม</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mt-4">
            <form action="{{ route('keptkayas.shop.place_order') }}" method="POST">
                @csrf
                <input type="submit" class="btn btn-success" value="ยืนยันการแลกเปลี่ยน">
                 
            </form>
        </div>
    @endif
{{-- </div> --}}
@endsection
