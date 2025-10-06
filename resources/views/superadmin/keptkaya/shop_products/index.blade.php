@extends('layouts.keptkaya')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h6>ข้อมูลสินค้า</h6>
                    <a href="{{ route('keptkayas.shop-products.create') }}" class="btn bg-gradient-primary btn-sm mb-0">เพิ่มสินค้าใหม่</a>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show mx-3" role="alert">
                            <span class="alert-text text-white"><strong>สำเร็จ!</strong> {{ session('success') }}</span>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">สินค้า</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">หมวดหมู่</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ราคาแต้ม</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ราคาสินค้า</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">สต็อก</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">สถานะ</th>
                                    <th class="text-secondary opacity-7"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($products as $product)
                                    <tr>
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                <div>
                                                    <img src="{{ asset('storage/' . $product->image_path) }}" class="avatar avatar-sm me-3" alt="{{ $product->name }}">
                                                </div>
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">{{ $product->name }}</h6>
                                                    <p class="text-xs text-secondary mb-0">{{ $product->sku }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">{{ $product->category->name ?? 'N/A' }}</p>
                                        </td>
                                        <td class="align-middle text-center text-sm">
                                            <p class="text-xs font-weight-bold mb-0">{{ number_format($product->point_price) }} แต้ม</p>
                                        </td>
                                        <td class="align-middle text-center text-sm">
                                            <p class="text-xs font-weight-bold mb-0">{{ number_format($product->cash_price, 2) }} บาท</p>
                                        </td>
                                        <td class="align-middle text-center text-sm">
                                            <p class="text-xs font-weight-bold mb-0">{{ $product->stock }}</p>
                                        </td>
                                        <td class="align-middle text-center text-sm">
                                            <span class="badge badge-sm bg-gradient-{{ $product->status == 'active' ? 'success' : 'secondary' }}">{{ ucfirst($product->status) }}</span>
                                        </td>
                                        <td class="align-middle">
                                            <a href="{{ route('keptkayas.shop-products.edit', $product->id) }}" class="text-secondary font-weight-bold text-xs me-2" data-toggle="tooltip" data-original-title="Edit user">
                                                แก้ไข
                                            </a>
                                            <form action="{{ route('keptkayas.shop-products.destroy', $product->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-link text-danger text-gradient px-0 mb-0" onclick="return confirm('คุณแน่ใจหรือไม่ที่จะลบสินค้านี้?')">ลบ</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">ไม่มีข้อมูลสินค้าในระบบ</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="d-flex justify-content-center mt-3">
                            {{-- Assuming $products is a paginator instance --}}
                            {{-- {{ $products->links('pagination::bootstrap-5') }} --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
