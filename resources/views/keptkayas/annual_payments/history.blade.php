@extends('layouts.keptkaya')
@section('nav-header', 'ประวัติใบเสร็จรับเงิน')
@section('nav-current', 'ค้นหาใบเสร็จรับเงิน')
@section('nav-user_payment_per_month-history', 'active')

@section('style')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    
    <style>
        /* จัด Style ของ Select2 ให้ดูดีขึ้น */
        .select2-container--bootstrap-5 .select2-selection {
            border-color: #dee2e6;
            padding: 0.5rem 1rem;
            height: auto;
        }
        
        /* สไตล์ของใบเสร็จ (Receipt Paper Look) */
        .receipt-paper {
            background: #fff;
            padding: 40px;
            margin-bottom: 30px;
            border-radius: 4px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            border: 1px solid #e0e0e0;
            position: relative;
        }

        .receipt-header {
            border-bottom: 2px solid #344767;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }

        .receipt-title {
            font-size: 1.5rem;
            font-weight: bold;
            color: #344767;
        }

        .org-logo {
            width: 80px;
            height: 80px;
            object-fit: contain;
            border: 1px solid #eee;
            padding: 5px;
            border-radius: 8px;
        }

        .info-label {
            font-weight: 600;
            color: #6c757d;
            width: 120px;
        }

        .payment-grid {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            overflow: hidden;
            margin-top: 20px;
        }

        .payment-grid-header {
            background-color: #f8f9fa;
            font-weight: bold;
            padding: 10px;
            text-align: center;
            border-bottom: 1px solid #dee2e6;
        }

        .payment-grid-item {
            border-right: 1px solid #dee2e6;
            border-bottom: 1px solid #dee2e6;
            padding: 10px;
        }
        
        .month-name {
            font-weight: bold;
            color: #344767;
            margin-bottom: 4px;
        }

        .amount-text {
            color: #198754;
            font-family: 'Courier New', monospace;
            font-weight: bold;
        }

        @media print {
            .no-print { display: none !important; }
            .receipt-paper { box-shadow: none; border: none; }
        }
    </style>
@endsection

@section('content')
<div class="container-fluid py-4">
    {{-- 1. Search Section --}}
    <div class="row justify-content-center mb-5 no-print">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h5 class="mb-3 text-center text-primary">
                        <i class="fas fa-search me-2"></i>ค้นหาใบเสร็จรับเงิน
                    </h5>
                    <form action="{{ route('keptkayas.annual_payments.history') }}" method="post"> 
                        @csrf
                        {{-- ใช้ GET ดีกว่าสำหรับการค้นหา เพื่อให้กด Back ได้ --}}
                        <div class="input-group">
                            <select class="form-select select2-search" name="bin_code" required>
                                <option value="">-- พิมพ์ชื่อ, ที่อยู่ หรือ รหัสถังขยะ --</option>
                                @foreach ($searchOptions as $opt)
                                    <option value="{{ $opt->wasteBin->bin_code }}" {{ request('bin_code') == $opt->wasteBin->bin_code ? 'selected' : '' }}>
                                        {{ $opt->wasteBin->bin_code }} : {{ $opt->wasteBin->user->firstname }} {{ $opt->wasteBin->user->lastname }} 
                                        (บ้านเลขที่ {{ $opt->wasteBin->user->address }})
                                    </option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn bg-gradient-primary mb-0 px-5">ค้นหา</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- 2. Result Section (Receipts) --}}
    @if ($selectedSubscriptions->isNotEmpty())
        @foreach ($selectedSubscriptions as $sub)
            <div class="row justify-content-center">
                <div class="col-md-10 col-lg-9">
                    <div class="receipt-paper">
                        {{-- Header --}}
                        <div class="receipt-header d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center gap-3">
                                @if(!empty($orgInfos['org_logo_img']))
                                    <img src="{{ asset('logo/'.$orgInfos['org_logo_img']) }}" class="org-logo">
                                @endif
                                <div>
                                    <div class="text-xs text-secondary mb-1">ใบเสร็จรับเงิน (ต้นขั้ว)</div>
                                    <h4 class="mb-0 font-weight-bolder">{{ $orgInfos['org_name'] }}</h4>
                                    <p class="text-xs text-secondary mb-0">
                                        {{ $orgInfos['org_address'] }} หมู่ {{ $orgInfos['org_zone'] }} 
                                        ต.{{ $orgInfos['org_tambon'] }} อ.{{ $orgInfos['org_district'] }} 
                                        จ.{{ $orgInfos['org_province'] }} {{ $orgInfos['org_zipcode'] }}
                                    </p>
                                </div>
                            </div>
                            <div class="text-end">
                                <div class="badge bg-gradient-info mb-2 fs-6">ปีงบประมาณ {{ $sub->fiscal_year }}</div>
                                <div class="text-sm"><b>เลขที่:</b> {{ str_pad($sub->id, 6, '0', STR_PAD_LEFT) }}</div>
                                <div class="text-sm"><b>วันที่:</b> {{ $sub->updated_at->locale('th')->isoFormat('D MMMM YYYY') }}</div>
                            </div>
                        </div>

                        {{-- Body: Customer Info --}}
                        <div class="row mb-4">
                            <div class="col-md-7">
                                <table class="table table-borderless table-sm">
                                    <tr>
                                        <td class="info-label">ผู้ชำระเงิน:</td>
                                        <td>{{ $sub->wasteBin->user->firstname }} {{ $sub->wasteBin->user->lastname }}</td>
                                    </tr>
                                    <tr>
                                        <td class="info-label">ที่อยู่:</td>
                                        <td>{{ $sub->wasteBin->user->address }} ({{ $sub->wasteBin->user->user_zone->zone_name ?? '-' }})</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-5">
                                <table class="table table-borderless table-sm">
                                    <tr>
                                        <td class="info-label">รหัสถังขยะ:</td>
                                        <td class="font-weight-bold text-primary">{{ $sub->wasteBin->bin_code }}</td>
                                    </tr>
                                    <tr>
                                        <td class="info-label">ประเภท:</td>
                                        <td>ขยะรายปี ({{ number_format($sub->annual_fee, 2) }} บาท/ปี)</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        {{-- Payment Details Table --}}
                        <div class="table-responsive mb-4">
                            <table class="table align-items-center mb-0 border">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-4">รายการ</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-end pe-4">จำนวนเงิน (บาท)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="ps-4 py-3">1. ค่าธรรมเนียมเก็บและขนขยะมูลฝอย ({{ number_format($sub->annual_fee, 2) }})</td>
                                        <td class="text-end pe-4 py-3 font-weight-bold">{{ number_format($sub->total_paid_amt, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="ps-4 text-secondary text-xs">ภาษีมูลค่าเพิ่ม 7%</td>
                                        <td class="text-end pe-4 text-secondary text-xs">0.00</td>
                                    </tr>
                                    <tr class="border-top">
                                        <td class="ps-4 font-weight-bold text-end">รวมทั้งสิ้น</td>
                                        <td class="text-end pe-4 font-weight-bold text-success fs-5">{{ number_format($sub->total_paid_amt, 2) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        {{-- Payment History Grid (Waterfall) --}}
                        <h6 class="text-sm font-weight-bold mb-2 text-secondary">
                            <i class="fas fa-history me-1"></i> รายละเอียดการชำระรายเดือน (ปีงบประมาณ {{ $sub->fiscal_year }})
                        </h6>
                        <div class="payment-grid">
                            <div class="row g-0">
                                @php
                                    // Logic เดือนไทย และดึงยอดเงิน
                                    $thaiMonths = [10,11,12,1,2,3,4,5,6,7,8,9];
                                    $payments = $sub->payments; // ดึงจาก Relation ที่ Eager Load มา
                                    
                                    // สร้าง Map: เดือน -> จำนวนเงินที่จ่าย เพื่อให้เรียกใช้ง่าย
                                    $paidMap = [];
                                    foreach($payments as $p) {
                                        $paidMap[$p->pay_mon] = $p->amount_paid;
                                    }
                                    
                                    // Helper สำหรับชื่อเดือน
                                    $fn = new \App\Http\Controllers\Api\FunctionsController();
                                @endphp

                                @foreach ($thaiMonths as $month)
                                    <div class="col-3 payment-grid-item text-center">
                                        <div class="month-name text-xs">{{ $fn->shortThaiMonth($month) }}</div>
                                        @if (isset($paidMap[$month]))
                                            <div class="amount-text text-sm">
                                                <i class="fas fa-check-circle text-xxs me-1"></i>
                                                {{ number_format($paidMap[$month], 2) }}
                                            </div>
                                        @else
                                            <div class="text-secondary text-xxs mt-1">-</div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Footer Signature --}}
                        <div class="row mt-5 pt-4">
                            <div class="col-6 offset-6 text-center">
                                <div style="border-bottom: 1px dashed #ccc; width: 80%; margin: 0 auto 10px auto;"></div>
                                <p class="text-sm mb-0">ลงชื่อเจ้าหน้าที่ผู้รับเงิน</p>
                                <p class="text-xs text-secondary">({{ Auth::user()->firstname }} {{ Auth::user()->lastname }})</p>
                            </div>
                        </div>
                        
                        {{-- Print Button (Visible only on screen) --}}
                        <div class="text-center mt-4 no-print">
                            <a href="{{ route('keptkayas.annual_payments.printReceipt', $sub ) }}" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-print me-1"></i> พิมพ์ใบเสร็จ
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        @endforeach
    @elseif(request()->has('bin_code'))
        <div class="alert alert-warning text-center mx-auto" style="max-width: 600px;">
            <i class="fas fa-exclamation-triangle me-2"></i> ไม่พบข้อมูลใบเสร็จ หรือ ข้อมูลการชำระเงินยังไม่สมบูรณ์
        </div>
    @endif
</div>
@endsection

@section('script')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2-search').select2({
                theme: 'bootstrap-5',
                placeholder: "ค้นหาข้อมูล...",
                allowClear: true,
                width: '100%'
            });
        });
    </script>
@endsection