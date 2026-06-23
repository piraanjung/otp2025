@extends('layouts.keptkaya')
@section('nav-header', ' ร้านรับซื้อขยะ')
@section('nav-current', ' รายการร้านรับซื้อขยะ')
@section('page-topic', ' รายการร้านรับซื้อขยะ')

@section('content')

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h5 class="mb-0">รายการร้านรับซื้อขยะ</h5>
            <a href="{{ route('keptkayas.purchase-shops.create') }}" class="btn btn-primary">
                <i class="fa fa-plus-circle me-1"></i> เพิ่มร้านรับซื้อใหม่
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>ชื่อร้าน</th>
                            <th>ผู้ติดต่อ</th>
                            <th>เบอร์โทรศัพท์</th>
                            <th>สถานะ</th>
                            <th style="width: 180px;">การจัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($shops as $shop)
                            <tr>
                                <td>{{ $shops->firstItem() + $loop->index }}</td>
                                <td>{{ $shop->shop_name }}</td>
                                <td>{{ $shop->contact_person ?? 'N/A' }}</td>
                                <td>{{ $shop->phone ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge {{ $shop->status == 'active' ? 'bg-success' : 'bg-secondary' }}">
                                        {{ ucfirst($shop->status) }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('keptkayas.purchase-shops.show', $shop->id) }}"
                                        class="btn btn-info btn-sm" title="ดูรายละเอียด"><i class="fa fa-eye"></i></a>
                                    <a href="{{ route('keptkayas.purchase-shops.edit', $shop->id) }}"
                                        class="btn btn-warning btn-sm" title="แก้ไข"><i class="fa fa-edit"></i></a>
                                    <form action="{{ route('keptkayas.purchase-shops.destroy', $shop->id) }}" method="POST"
                                        class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm"
                                            onclick="return confirm('คุณแน่ใจหรือไม่ที่จะลบร้านรับซื้อนี้?')"><i
                                                class="fa fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">ไม่พบร้านรับซื้อ</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $shops->links() }}
            </div>
        </div>
    </div>


@endsection