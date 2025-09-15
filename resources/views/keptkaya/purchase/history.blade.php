@extends('layouts.keptkaya')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="mb-0">ประวัติการรับซื้อขยะ</h1>
            <a href="{{ route('keptkayas.purchase.select_user') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-1"></i> กลับหน้าเลือกผู้ใช้งาน
            </a>
        </div>

        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">สรุปสำหรับ: {{ $user->firstname }} {{ $user->lastname }}</h5>
            </div>
            <div class="card-body">
                @if ($user->purchaseTransactions->isEmpty())
                    <div class="alert alert-info text-center">
                        ไม่พบประวัติการรับซื้อขยะสำหรับผู้ใช้งานนี้
                    </div>
                @else
                    @foreach ($user->purchaseTransactions as $transaction)
                        <div class="card mb-3 border-secondary">
                            <div class="card-header bg-secondary text-white d-flex justify-content-between">
                                <strong>เลขที่ธุรกรรม: {{ $transaction->kp_u_trans_no }}</strong>
                                <span>วันที่: {{ $transaction->transaction_date->format('Y-m-d') }}</span>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-sm mb-0">
                                        <thead>
                                            <tr>
                                                <th>รายการขยะ</th>
                                                <th>ปริมาณ</th>
                                                <th>เป็นเงิน</th>
                                                <th>คะแนน</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($transaction->details as $detail)
                                                <tr>
                                                    <td>{{ $detail->item->kp_itemsname ?? 'N/A' }}</td>
                                                    <td>{{ number_format($detail->amount_in_units, 2) }}
                                                        {{ $detail->pricePoint->kp_units_info->unitname ?? 'N/A' }}</td>
                                                    <td>{{ number_format($detail->amount, 2) }} บาท</td>
                                                    <td>{{ number_format($detail->points) }} คะแนน</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="card-footer d-flex justify-content-end align-items-center">
                                <h6 class="me-3 mb-0">ยอดรวม: <span
                                        class="text-success">{{ number_format($transaction->total_amount, 2) }} บาท</span></h6>
                                <h6 class="me-3 mb-0">คะแนนรวม: <span
                                        class="text-primary">{{ number_format($transaction->total_points) }} คะแนน</span></h6>
                                <a href="{{ route('keptkayas.purchase.receipt', $transaction->id) }}" class="btn btn-sm btn-info">
                                    <i class="bi bi-receipt me-1"></i> ดูใบเสร็จ
                                </a>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
@endsection