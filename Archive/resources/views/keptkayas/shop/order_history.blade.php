@extends('layouts.keptkaya')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">ประวัติคำสั่งซื้อของฉัน</h1>
        <a href="{{ route('keptkayas.shop.index') }}" class="btn btn-outline-primary">
            <i class="bi bi-arrow-left me-1"></i> กลับไปที่ร้านค้า
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

    @if($orders->isEmpty())
        <div class="alert alert-info text-center">คุณยังไม่มีประวัติคำสั่งซื้อ</div>
    @else
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0">รายการคำสั่งซื้อ</h5>
            </div>
            <div class="card-body">
                @foreach ($orders as $order)
                    <div class="card mb-3 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h5 class="card-title mb-1">คำสั่งซื้อ #{{ $order->id }}</h5>
                                    <p class="card-text mb-1">
                                        <small class="text-muted">วันที่สั่งซื้อ: {{ $order->created_at->format('d/m/Y H:i') }}</small><br>
                                        <small class="text-muted">สถานะ:
                                            <span class="badge bg-{{ $order->order_status == 'pending' ? 'warning' : 'success' }}">
                                                {{ ucfirst($order->order_status) }}
                                            </span>
                                        </small>
                                    </p>
                                </div>
                                <div>
                                    <p class="h6 mb-1 text-end">ยอดเงิน: <span class="text-info">{{ number_format($order->total_cash, 2) }} บาท</span></p>
                                    <p class="h6 mb-0 text-end">แต้ม: <span class="text-success">{{ number_format($order->total_points) }} แต้ม</span></p>
                                </div>
                            </div>
                            <hr>
                            <h6>รายละเอียดสินค้า:</h6>
                            <ul class="list-group list-group-flush">
                                @foreach ($order->details as $detail)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>
                                            {{ $detail->product->name ?? 'N/A' }}
                                            <small class="text-muted">x {{ $detail->quantity }}</small>
                                        </span>
                                        <small>
                                            {{ number_format($detail->points_used) }} แต้ม / {{ number_format($detail->cash_used, 2) }} บาท
                                        </small>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
