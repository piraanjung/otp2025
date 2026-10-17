@extends('layouts.keptkaya')
@section('nav-header', 'ตั้งค่าข้อมูล')
@section('nav-current', 'รายการร้านรับซื้อขยะ')
@section('page-topic', 'รายการร้านรับซื้อขยะ')

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show text-white" role="alert">
            <span class="alert-text">{{ session('success') }}</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-header pb-0 d-flex justify-content-between align-items-center">
            <h6 class="mb-0">รายการร้านรับซื้อขยะ</h6>
            <a href="{{ route('keptkayas.purchase-shops.create') }}" class="btn bg-gradient-primary btn-sm mb-0">
                <i class="fas fa-plus me-1"></i> เพิ่มร้านรับซื้อใหม่
            </a>
        </div>
        <div class="card-body px-0 pt-3 pb-2">
            <div class="table-responsive p-0">
                <table class="table align-items-center mb-0">
                    <thead>
                        <tr>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-4">#</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">ชื่อร้าน</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">ผู้ติดต่อ</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">เบอร์โทรศัพท์</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">สถานะ</th>
                            <th class="text-secondary opacity-7"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($shops as $shop)
                            <tr>
                                <td class="ps-4"><p class="text-xs font-weight-bold mb-0">{{ $shops->firstItem() + $loop->index }}</p></td>
                                <td><h6 class="mb-0 text-sm">{{ $shop->shop_name }}</h6></td>
                                <td><p class="text-xs font-weight-bold mb-0">{{ $shop->contact_person ?? '-' }}</p></td>
                                <td><p class="text-xs font-weight-bold mb-0">{{ $shop->phone ?? '-' }}</p></td>
                                <td class="align-middle text-center">
                                    <span class="badge badge-sm bg-gradient-{{ $shop->status == 'active' ? 'success' : 'secondary' }}">
                                        {{ $shop->status == 'active' ? 'ใช้งาน' : 'ปิดใช้งาน' }}
                                    </span>
                                </td>
                                <td class="align-middle text-end pe-4 text-nowrap">
                                    <a href="{{ route('keptkayas.purchase-shops.edit', $shop->id) }}"
                                        class="btn btn-link text-secondary font-weight-bold text-xs p-0 mb-0 me-3">
                                        <i class="fas fa-edit me-1"></i> แก้ไข
                                    </a>
                                    <form action="{{ route('keptkayas.purchase-shops.destroy', $shop->id) }}" method="POST"
                                        class="d-inline" onsubmit="return confirm('คุณแน่ใจหรือไม่ที่จะลบร้านรับซื้อนี้?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-link text-danger font-weight-bold text-xs p-0 mb-0">
                                            <i class="fas fa-trash me-1"></i> ลบ
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-secondary py-4">ไม่พบร้านรับซื้อ</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-4 pt-3">
                {{ $shops->links() }}
            </div>
        </div>
    </div>
@endsection
