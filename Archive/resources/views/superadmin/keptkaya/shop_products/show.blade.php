@extends('layouts.keptkaya')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h6>รายละเอียดสินค้า</h6>
                    <a href="{{ route('superadmin.keptkaya.shop-products.index') }}" class="btn bg-gradient-secondary btn-sm mb-0">กลับสู่รายการสินค้า</a>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="p-4">
                        <div class="row">
                            <div class="col-md-4 text-center">
                                @if($product->image_path)
                                    <img src="{{ asset('storage/' . $product->image_path) }}" class="img-fluid rounded" alt="{{ $product->name }}" style="max-width: 300px;">
                                @else
                                    <img src="{{ asset('storage/placeholder.jpg') }}" class="img-fluid rounded" alt="ไม่มีรูปภาพ" style="max-width: 300px;">
                                @endif
                            </div>
                            <div class="col-md-8">
                                <h4 class="mb-3">{{ $product->name }}</h4>
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item"><strong>SKU:</strong> {{ $product->sku }}</li>
                                    <li class="list-group-item"><strong>หมวดหมู่:</strong> {{ $product->category->name ?? 'N/A' }}</li>
                                    <li class="list-group-item"><strong>รายละเอียด:</strong> {{ $product->description }}</li>
                                    <li class="list-group-item"><strong>ราคาแลกเปลี่ยน (คะแนน):</strong> {{ number_format($product->point_price) }} แต้ม</li>
                                    <li class="list-group-item"><strong>ราคาแลกเปลี่ยน (เงินสด):</strong> {{ number_format($product->cash_price, 2) }} บาท</li>
                                    <li class="list-group-item"><strong>สต็อก:</strong> {{ $product->stock }}</li>
                                    <li class="list-group-item"><strong>สถานะ:</strong>
                                        <span class="badge badge-sm bg-gradient-{{ $product->status == 'active' ? 'success' : 'secondary' }}">{{ ucfirst($product->status) }}</span>
                                    </li>
                                </ul>
                                <div class="mt-4">
                                    <a href="{{ route('superadmin.keptkaya.shop-products.edit', $product->id) }}" class="btn bg-gradient-warning">แก้ไข</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection