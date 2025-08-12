    @extends('layouts.keptkaya')

    @section('content')
    <div class="container my-5">
        <div class="card shadow-lg">
            <div class="card-header bg-success text-white text-center">
                <h3 class="mb-0">ใบเสร็จรับเงินการขายขยะ</h3>
                <p class="mb-0">ธนาคารขยะ</p>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>เลขที่ธุรกรรม:</strong> {{ $transaction->kp_u_trans_no }}
                    </div>
                    <div class="col-md-6 text-md-end">
                        <strong>วันที่ขาย:</strong> {{ $transaction->transaction_date->format('Y-m-d') }}
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <strong>ร้านรับซื้อ:</strong> {{ $transaction->shop_name }}
                    </div>
                    <div class="col-md-6 text-md-end">
                        <strong>ผู้บันทึก:</strong> {{ $transaction->recorder->firstname ?? 'N/A' }} {{ $transaction->recorder->lastname ?? 'N/A' }}
                    </div>
                </div>

                <div class="table-responsive mb-4">
                    <table class="table table-bordered table-striped">
                        <thead class="table-light">
                            <tr>
                                <th>รายการขยะ</th>
                                <th>น้ำหนัก/ปริมาณ (kg)</th>
                                <th>ราคา/หน่วย (บาท)</th>
                                <th>เป็นเงิน (บาท)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($transaction->details as $detail)
                            <tr>
                                <td>{{ $detail->item->kp_itemsname ?? 'N/A' }}</td>
                                <td>{{ number_format($detail->weight, 2) }}</td>
                                <td>{{ number_format($detail->price_per_unit, 2) }}</td>
                                <td>{{ number_format($detail->amount, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="row">
                    <div class="col-md-6 offset-md-6">
                        <div class="card p-3">
                            <div class="d-flex justify-content-between">
                                <strong>น้ำหนัก/ปริมาณรวม:</strong>
                                <span>{{ number_format($transaction->total_weight, 2) }} kg</span>
                            </div>
                            <hr class="my-2">
                            <div class="d-flex justify-content-between fs-4 text-success">
                                <strong>ยอดรวมทั้งหมด:</strong>
                                <span>{{ number_format($transaction->total_amount, 2) }} บาท</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer text-end">
                <button class="btn btn-primary" onclick="window.print()">
                    <i class="bi bi-printer-fill me-1"></i> พิมพ์ใบเสร็จ
                </button>
                <a href="{{ route('keptkaya.sell.history') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-1"></i> กลับหน้าประวัติการขาย
                </a>
            </div>
        </div>
    </div>

    <style>
        /* CSS for printing */
        @media print {
            body {
                visibility: hidden;
            }
            .container {
                visibility: visible;
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
            .card-footer, .btn {
                display: none;
            }
        }
    </style>
    @endsection
    