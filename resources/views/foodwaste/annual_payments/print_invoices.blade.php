@extends('layouts.keptkaya')

@section('content')
    <div class="container my-5">
        <div class="card-footer text-end">
            <button class="btn btn-primary" onclick="window.print()">
                <i class="bi bi-printer-fill me-1"></i> พิมพ์ใบแจ้งหนี้
            </button>
            <a href="{{ route('keptkayas.annual_payments.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-1"></i> กลับหน้าหลัก
            </a>
        </div>
        @foreach($invoicesByUser as $invoice)
            <div class="card shadow-lg mb-5 print-page-break">
                <div class="card-header bg-primary text-white text-center">
                    <h3 class="mb-0">ใบแจ้งหนี้ค่าจัดเก็บขยะรายปี</h3>
                    <p class="mb-0">ค่าจัดเก็บขยะประจำปี (ปีงบประมาณ {{ $invoice[0]->fiscal_year }})</p>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>เลขที่ใบแจ้งหนี้:</strong> {{ $invoice[0]->invoice_number }}
                        </div>
                        <div class="col-md-6 text-md-end">
                            <strong>วันที่ออกใบแจ้งหนี้:</strong> {{ $invoice[0]->created_at->format('Y-m-d') }}
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <strong>ผู้ใช้งาน:</strong> {{ $invoice[0]->wasteBin->user->firstname }}
                            {{ $invoice[0]->wasteBin->user->lastname }}
                        </div>
                        <div class="col-md-6 text-md-end">
                            <strong>วันครบกำหนดชำระ:</strong> {{ $invoice[0]->created_at->format('Y-m-d') }}
                        </div>
                    </div>

                    <div class="table-responsive mb-4">
                        <table class="table table-bordered table-striped">
                            <thead class="table-light">
                                <tr>
                                    <th>หมายเลขถัง</th>
                                    <th>ประเภท</th>
                                    <th>ยอดที่ต้องชำระ</th>
                                    <th>ชำระแล้ว</th>
                                    <th>ค้างชำระ</th>

                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $sum = 0;
                                    $vat = 0;
                                @endphp
                                @foreach ($invoice as $bin)
                                    @php
                                        $diff = $bin->annual_fee - $bin->total_paid_amt;
                                        $sum += $diff;
                                       @endphp
                                    <tr>
                                        <td>{{ $bin->wasteBin->bin_code }}</td>
                                        <td>{{ $bin->wasteBin->bin_type }}</td>
                                        <td class="text-end">{{ number_format($bin->annual_fee, 2) }} บาท</td>
                                        <td class="text-end">{{ number_format($bin->total_paid_amt, 2) }} บาท</td>
                                        <td class="text-end">{{ number_format($diff, 2) }} บาท</td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td colspan="4" class="text-end">ภาษี 7%</td>
                                    <td class="text-end"> {{ number_format($vat, 2) }} บาท </td>
                                    {{-- <td colspan="">s</td> --}}
                                </tr>
                                <tr>
                                    <td colspan="4" class="text-end">รวมค้างชำระ</td>
                                    <td class="text-end"> {{ number_format($sum + $vat, 2) }} บาท </td>
                                    {{-- <td colspan="">s</td> --}}
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    {{-- <div class="row">
                        <div class="col-md-6 offset-md-6">
                            <div class="card p-3">
                                <div class="d-flex justify-content-between">
                                    <strong>ยอดที่ชำระแล้ว:</strong>
                                    <span>{{ number_format($invoice->paid_amount, 2) }} บาท</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <strong>สถานะ:</strong>
                                    <span>{{ ucfirst($invoice->status) }}</span>
                                </div>
                                <hr class="my-2">
                                <div class="d-flex justify-content-between fs-4 text-success">
                                    <strong>ยอดค้างชำระ:</strong>
                                    @php
                                    $outstandingAmount = $invoice->total_amount - $invoice->paid_amount;
                                    @endphp
                                    <span>{{ number_format($outstandingAmount, 2) }} บาท</span>
                                </div>
                            </div>
                        </div>
                    </div> --}}
                </div>
            </div>
        @endforeach


    </div>

    <style>
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

            .card-footer,
            .btn {
                display: none;
            }

            .print-page-break {
                page-break-after: always;
            }
        }
    </style>
@endsection