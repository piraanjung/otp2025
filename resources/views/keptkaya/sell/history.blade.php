    @extends('layouts.keptkaya')

    @section('content')
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="mb-0">ประวัติการขายขยะ</h1>
            <a href="{{ route('keptkaya.sell.form') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-1"></i> บันทึกการขายใหม่
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">รายการธุรกรรมการขายทั้งหมด</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>เลขที่ธุรกรรม</th>
                                <th>ร้านรับซื้อ</th>
                                <th>วันที่ขาย</th>
                                <th>น้ำหนักรวม (kg)</th>
                                <th>ยอดรวมทั้งหมด (บาท)</th>
                                <th>ผู้บันทึก</th>
                                <th style="width: 150px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($transactions as $transaction)
                                <tr>
                                    <td>{{ $transactions->firstItem() + $loop->index }}</td>
                                    <td>{{ $transaction->kp_u_trans_no }}</td>
                                    <td>{{ $transaction->shop_name }}</td>
                                    <td>{{ $transaction->transaction_date->format('Y-m-d') }}</td>
                                    <td>{{ number_format($transaction->total_weight, 2) }}</td>
                                    <td>{{ number_format($transaction->total_amount, 2) }}</td>
                                    <td>{{ $transaction->recorder->firstname ?? 'N/A' }}</td>
                                    <td>
                                        <a href="{{ route('keptkaya.sell.receipt', $transaction->id) }}" class="btn btn-info btn-sm">
                                            <i class="fa fa-receipt me-1"></i> ใบเสร็จ
                                        </a>
                                        <form action="{{ route('keptkaya.sell.destroy', $transaction->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('คุณแน่ใจหรือไม่ที่จะลบธุรกรรมนี้?')"><i class="fa fa-trash"></i> ลบ</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">ไม่พบประวัติการขาย</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $transactions->links() }}
                </div>
            </div>
        </div>
    @endsection
    