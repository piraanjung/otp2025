@extends('layouts.keptkaya')

@section('title_page', 'รับชำระค่าจัดเก็บถังขยะรายปี')

@section('page-topic', 'รายละเอียดค่าถังขยะรายปี')
@section('nav-current', 'รับชำระค่าจัดเก็บถังขยะรายปี')
@section('nav-keptkayas.annual_payments.index', 'active')
@section('route-header')
 {{ route('keptkayas.annual_payments.index') }}
@endsection
@section('nav-main', 'รับชำระค่าจัดเก็บถังขยะรายปี')

@section('content')
    {{-- Section 1: Dashboard Cards (Summary) --}}
    <div class="row mb-4">
        <div class="col-xl-4 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">ถังขยะรายปีทั้งหมด</p>
                                <h5 class="font-weight-bolder mb-0">
                                    {{ number_format($totalBins) }}
                                    <span class="text-secondary text-sm font-weight-bolder">ถัง</span>
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                                <i class="fas fa-dumpster text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">ชำระครบแล้ว</p>
                                <h5 class="font-weight-bolder mb-0 text-success">
                                    {{ number_format($paidBins) }}
                                    <span class="text-success text-sm font-weight-bolder">ถัง</span>
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-success shadow text-center border-radius-md">
                                <i class="fas fa-check-circle text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-sm-6">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">คงเหลือ / ค้างชำระ</p>
                                <h5 class="font-weight-bolder mb-0 text-danger">
                                    {{ number_format($pendingBins) }}
                                    <span class="text-danger text-sm font-weight-bolder">ถัง</span>
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-danger shadow text-center border-radius-md">
                                <i class="fas fa-exclamation-circle text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Section 2: Filter & Table --}}
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                
                {{-- Header + Search Form --}}
                <div class="card-header pb-0">
                    <form action="{{ route('keptkayas.annual_payments.index') }}" method="GET">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                            <div class="mb-3 mb-md-0">
                                <h6 class="mb-0">รายการค่าถังขยะรายปี</h6>
                                <p class="text-xs text-secondary mb-0">ประจำปีงบประมาณ {{ $fiscalYear }}</p>
                            </div>
                            
                            <div class="d-flex align-items-center gap-2">
                                {{-- Search Input --}}
                                <div class="input-group input-group-outline input-group-sm" style="width: 350px;">
                                    <label class="form-label">ค้นหา ชื่อ / รหัสถัง</label>
                                    <input type="text" name="search" class="form-control" value="{{ $search }}" onfocus="focused(this)" onfocusout="defocused(this)">
                                </div>

                                {{-- Status Filter --}}
                                <div class="input-group input-group-static input-group-sm" style="width: 140px;">
                                    <select name="status" class="form-control" onchange="this.form.submit()">
                                        <option value="all" {{ $statusFilter == 'all' ? 'selected' : '' }}>ทุกสถานะ</option>
                                        <option value="pending" {{ $statusFilter == 'pending' ? 'selected' : '' }}>ค้างชำระ</option>
                                        <option value="paid" {{ $statusFilter == 'paid' ? 'selected' : '' }}>ชำระแล้ว</option>
                                    </select>
                                </div>

                                {{-- Year Filter --}}
                                <div class="input-group input-group-static input-group-sm" style="width: 100px;">
                                    <select name="fy" class="form-control font-weight-bold text-primary" onchange="this.form.submit()">
                                        @foreach($availableFiscalYears as $year)
                                            <option value="{{ $year }}" {{ $year == $fiscalYear ? 'selected' : '' }}>{{ $year }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <button type="submit" class="btn btn-icon btn-sm btn-primary mb-0">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="card-body px-0 pt-0 pb-2 mt-3">
                    {{-- Alerts --}}
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show mx-3" role="alert">
                            <span class="alert-text text-white"><strong>สำเร็จ!</strong> {{ session('success') }}</span>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        </div>
                    @endif

                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ผู้ใช้งาน</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">รายการถังขยะ / ยอดชำระ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($preferences as $pref)
                                    <tr>
                                        <td class="align-top" style="width: 30%;">
                                            <div class="d-flex px-2 py-1">
                                                <div class="avatar avatar-sm me-3 bg-gradient-secondary rounded-circle">
                                                    <span class="text-white text-xs">{{ substr($pref->user->firstname ?? 'U', 0, 1) }}</span>
                                                </div>
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">{{ $pref->user->firstname ?? '-' }} {{ $pref->user->lastname ?? '' }}</h6>
                                                    <p class="text-xs text-secondary mb-0">{{ $pref->user->email ?? 'ไม่มีอีเมล' }}</p>
                                                    <p class="text-xs text-secondary mb-0"><i class="fas fa-map-marker-alt"></i> {{ Str::limit($pref->user->address ?? '-', 30) }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="align-top">
                                            @foreach($pref->wasteBins as $bin)
                                                {{-- Filter at View Level: แสดงเฉพาะถังที่มี Sub ของปีนี้ --}}
                                                @php
                                                    $sub = $bin->subscriptions->where('fiscal_year', $fiscalYear)->first();
                                                @endphp

                                                @if($sub)
                                                    {{-- Filter Logic: ถ้าเลือกดู "จ่ายแล้ว" แต่ถังนี้ "ยังไม่จ่าย" ก็ข้ามไป (กรณี User มีหลายถัง) --}}
                                                    @if($statusFilter == 'paid' && $sub->status != 'paid') @continue @endif
                                                    @if($statusFilter == 'pending' && $sub->status == 'paid') @continue @endif

                                                    <div class="card card-body border p-2 mb-2 shadow-none bg-gray-100">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div>
                                                                <h6 class="text-xs font-weight-bold mb-0 text-primary">
                                                                    <i class="fas fa-trash me-1"></i> {{ $bin->bin_code }}
                                                                </h6>
                                                                <span class="text-xxs text-secondary">{{ $bin->kpUserGroup->usergroup_name ?? 'ทั่วไป' }}</span>
                                                            </div>
                                                            <div class="text-end">
                                                                <span class="text-xs font-weight-bold d-block">ยอดรวม: {{ number_format($sub->annual_fee, 2) }} ฿</span>
                                                                <span class="text-xxs text-secondary">ค้างชำระ: {{ number_format($sub->annual_fee - $sub->total_paid_amt, 2) }} ฿</span>
                                                            </div>
                                                            <div class="d-flex align-items-center gap-2">
                                                                @if($sub->status == 'paid')
                                                                    <span class="badge badge-sm bg-gradient-success">จ่ายครบแล้ว</span>
                                                                    <button class="btn btn-link text-secondary mb-0 p-0 disabled"><i class="fas fa-check-double"></i></button>
                                                                @else
                                                                    @if($sub->total_paid_amt > 0)
                                                                        <span class="badge badge-sm bg-gradient-warning">ค้างบางส่วน</span>
                                                                    @else
                                                                        <span class="badge badge-sm bg-gradient-danger">ค้างชำระ</span>
                                                                    @endif
                                                                    
                                                                    <a href="{{ route('keptkayas.annual_payments.show', $sub->id) }}" class="btn btn-sm bg-gradient-primary mb-0 ms-2 px-3">
                                                                        จ่ายเงิน
                                                                    </a>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center p-5">
                                            <div class="text-secondary mb-3">
                                                <i class="fas fa-search fa-3x opacity-5"></i>
                                            </div>
                                            <h6 class="text-secondary font-weight-normal">ไม่พบข้อมูลตามเงื่อนไขที่ค้นหา</h6>
                                            @if($search || $statusFilter != 'all')
                                                <a href="{{ route('keptkayas.annual_payments.index', ['fy' => $fiscalYear]) }}" class="text-xs text-primary text-gradient font-weight-bold">
                                                    ล้างค่าการค้นหา
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    {{-- Pagination --}}
                    <div class="d-flex justify-content-center mt-3">
                        {{ $preferences->appends(['fy' => $fiscalYear, 'search' => $search, 'status' => $statusFilter])->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection