@extends('layouts.keptkaya')
@section('nav-header', 'ขายให้ร้านรับซื้อขยะ')
@section('page-topic', 'ประวัติการขายขยะรีไซเคิล')

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show text-white" role="alert">
            <span class="alert-text">{{ session('success') }}</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-header pb-0 d-flex justify-content-between align-items-center">
            <h6 class="mb-0">รายการธุรกรรมการขายทั้งหมด</h6>
            <a href="{{ route('keptkayas.sell.form') }}" class="btn bg-gradient-primary btn-sm mb-0">
                <i class="fas fa-plus me-1"></i> บันทึกการขายใหม่
            </a>
        </div>
        <div class="card-body px-0 pt-3 pb-2">
            <div class="table-responsive p-0">
                <table class="table align-items-center mb-0">
                    <thead>
                        <tr>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-4">#</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">เลขที่ธุรกรรม</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">ร้านรับซื้อ</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">วันที่ขาย</th>
                            <th class="text-end text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">น้ำหนักรวม (kg)</th>
                            <th class="text-end text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ยอดรวม (บาท)</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-4">ผู้บันทึก</th>
                            <th class="text-secondary opacity-7"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($transactions as $transaction)
                            <tr>
                                <td class="ps-4"><p class="text-xs font-weight-bold mb-0">{{ $transactions->firstItem() + $loop->index }}</p></td>
                                <td><h6 class="mb-0 text-sm">{{ $transaction->kp_u_trans_no }}</h6></td>
                                <td><p class="text-xs font-weight-bold mb-0">{{ $transaction->shop_name }}</p></td>
                                <td class="text-center text-xs">{{ $transaction->transaction_date->format('Y-m-d') }}</td>
                                <td class="text-end text-sm">{{ number_format($transaction->total_weight, 2) }}</td>
                                <td class="text-end text-sm font-weight-bold">{{ number_format($transaction->total_amount, 2) }}</td>
                                <td class="ps-4"><p class="text-xs mb-0">{{ $transaction->recorder->firstname ?? 'N/A' }}</p></td>
                                <td class="align-middle text-end pe-4 text-nowrap">
                                    <a href="{{ route('keptkayas.sell.receipt', $transaction->id) }}"
                                        class="btn btn-link text-info font-weight-bold text-xs p-0 mb-0 me-3">
                                        <i class="fas fa-receipt me-1"></i> ใบเสร็จ
                                    </a>
                                    <form action="{{ route('keptkayas.sell.destroy', $transaction->id) }}" method="POST"
                                        class="d-inline" onsubmit="return confirm('คุณแน่ใจหรือไม่ที่จะลบธุรกรรมนี้?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-link text-danger font-weight-bold text-xs p-0 mb-0">
                                            <i class="fas fa-trash me-1"></i> ลบ
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center text-secondary py-4">ไม่พบประวัติการขาย</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-4 pt-3">
                {{ $transactions->links() }}
            </div>
        </div>
    </div>
@endsection
