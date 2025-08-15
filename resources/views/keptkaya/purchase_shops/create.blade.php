@extends('layouts.keptkaya')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h1 class="card-title mb-0">เพิ่มร้านรับซื้อใหม่</h1>
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form action="{{ route('keptkaya.purchase-shops.store') }}" method="POST">
                @csrf
                @include('keptkaya.purchase_shops.form', ['shop' => new App\Models\KeptKaya\KpPurchaseShop()])
                <div class="d-flex justify-content-between mt-3">
                    <button type="submit" class="btn btn-success">บันทึกร้านรับซื้อ</button>
                    <a href="{{ route('keptkaya.purchase-shops.index') }}" class="btn btn-secondary">ยกเลิก</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
