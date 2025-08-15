@extends('layouts.keptkaya')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">ราคารับซื้อขยะรีไซเคิล</h1>
        <a href="{{ route('keptkaya.tbank.prices.create') }}" class="btn btn-primary">
            <i class="fa fa-plus-circle me-1"></i> เพิ่มราคาใหม่
        </a>
    </div>

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
        <div class="card-header bg-light">
            <h5 class="mb-0">รายการราคารับซื้อ</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>รายการขยะ</th>
                            <th>หน่วยนับ</th>
                            <th>ราคาจ่ายให้สมาชิก</th>
                            <th>ราคาจากร้านรับซื้อ</th>
                            <th>คะแนน</th>
                            <th>เริ่มมีผล</th>
                            <th>สิ้นสุด</th>
                            <th>สถานะ</th>
                            <th style="width: 180px;">การจัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($prices as $price)
                            <tr>
                                <td>{{ $prices->firstItem() + $loop->index }}</td>
                                <td>{{ $price->item->item_name ?? 'N/A' }}</td>
                                <td>{{ $price->kp_units_info->unitname ?? 'N/A' }}</td>
                                <td>{{ number_format($price->price_for_member, 2) }}</td>
                                <td>{{ number_format($price->price_from_dealer, 2) }}</td>
                                <td>{{ number_format($price->point) }}</td>
                                <td>{{ $price->effective_date }}</td>
                                <td>{{ $price->end_date ? $price->end_date : 'ปัจจุบัน' }}</td>
                                <td>
                                    <span class="badge {{ $price->is_active ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $price->status == 'active' ? 'ใช้งาน' : 'ไม่ใช้งาน' }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('keptkaya.tbank.prices.show', $price->id) }}" class="btn btn-info btn-sm" title="ดูรายละเอียด"><i class="fa fa-eye"></i></a>
                                    <a href="{{ route('keptkaya.tbank.prices.edit', $price->id) }}" class="btn btn-warning btn-sm" title="แก้ไข"><i class="fa fa-edit"></i></a>
                                    <form action="{{ route('keptkaya.tbank.prices.destroy', $price->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('คุณแน่ใจหรือไม่ที่จะลบราคานี้?')"><i class="fa fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center">ไม่พบข้อมูลราคารับซื้อ</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $prices->links() }}
            </div>
        </div>
    </div>

@endsection
