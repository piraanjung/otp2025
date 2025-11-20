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
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h6>รายการค่าถังขยะรายปี (ปีงบประมาณ {{ $fiscalYear }})</h6>
                    <form action="{{ route('keptkayas.annual_payments.index') }}" method="GET" class="d-flex align-items-center">
                        <label for="fy" class="form-label mb-0 me-2">ปีงบประมาณ:</label>
                        <select name="fy" id="fy" class="form-select form-select-sm" onchange="this.form.submit()">
                            @foreach($availableFiscalYears as $year)
                                <option value="{{ $year }}" {{ $year == $fiscalYear ? 'selected' : '' }}>{{ $year }}</option>
                            @endforeach
                            @if(!$availableFiscalYears->contains($fiscalYear))
                                <option value="{{ $fiscalYear }}" selected>{{ $fiscalYear }}</option>
                            @endif
                        </select>
                    </form>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show mx-3 mt-3" role="alert">
                            <span class="alert-text text-white"><strong>สำเร็จ!</strong> {{ session('success') }}</span>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show mx-3 mt-3" role="alert">
                            <span class="alert-text text-white"><strong>เกิดข้อผิดพลาด!</strong> โปรดตรวจสอบข้อมูล</span>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                   
                                    <th class="text-uppercase font-weight-bolder">ผู้ใช้งาน</th>
                                    <th class="text-uppercase font-weight-bolder ps-2">รหัสถัง</th>
                                    <th class="text-uppercase font-weight-bolder text-center">ค่าธรรมเนียมรายปี (฿)</th>
                                    <th class="text-uppercase font-weight-bolder text-center">ชำระแล้ว (฿)</th>
                                    <th class="text-uppercase font-weight-bolder text-center">ค้างชำระ (฿)</th>
                                    <th class="text-uppercase font-weight-bolder text-center">สถานะ</th>
                                    <th class="text-secondary">การจัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                    @php
                                        // Filter subscriptions for the current fiscal year
                                        $userSubscriptions = $user->wasteBins
                                            ->flatMap(fn($bin) => $bin->subscriptions)
                                            ->where('fiscal_year', $fiscalYear);
                                    @endphp
                                    @if($userSubscriptions->count() > 0)
                                        @foreach($userSubscriptions as $sub)
                                            <tr class="{{ $loop->first ? 'table-primary' : '' }}">
                                                @if($loop->first)
                                                    
                                                    <td rowspan="{{ $userSubscriptions->count() }}">
                                                        <p class="text-xs font-weight-bold mb-0">{{ $user->firstname ?? 'N/A' }} {{ $user->lastname ?? '' }}</p>
                                                        <p class="text-xs text-muted mb-0">{{ $user->email }}</p>
                                                    </td>
                                                @endif
                                                <td>
                                                    <p class="text-xs font-weight-bold mb-0">{{ $sub->wasteBin->bin_code ?? 'N/A' }}</p>
                                                </td>
                                                <td class="align-middle text-center text-sm">
                                                    <span class="text-xs font-weight-bold">{{ number_format($sub->annual_fee, 2) }}</span>
                                                </td>
                                                <td class="align-middle text-center text-sm">
                                                    <span class="text-xs font-weight-bold">{{ number_format($sub->total_paid_amt, 2) }}</span>
                                                </td>
                                                <td class="align-middle text-center text-sm">
                                                    <span class="text-xs font-weight-bold">{{ number_format($sub->annual_fee - $sub->total_paid_amt, 2) }}</span>
                                                </td>
                                                <td class="align-middle text-center text-sm">
                                                    @php
                                                        $statusClass = '';
                                                        switch($sub->status) {
                                                            case 'paid': $statusClass = 'success'; break;
                                                            case 'partially_paid': $statusClass = 'warning'; break;
                                                            case 'overdue': $statusClass = 'danger'; break;
                                                            default: $statusClass = 'secondary'; break;
                                                        }
                                                    @endphp
                                                    <span class="badge badge-sm bg-gradient-{{ $statusClass }}">{{ ucfirst(str_replace('_', ' ', $sub->status)) }}</span>
                                                </td>
                                                <td class="align-middle">
                                                    <a href="{{ route('keptkayas.annual_payments.show', $sub->id) }}" class="btn btn-link text-secondary font-weight-bold text-xs px-0 mb-0 me-2">
                                                        <i class="fas fa-eye me-1"></i> ดูรายละเอียด/ชำระ
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="7" class="text-center">ไม่มีข้อมูลการสมัครสมาชิกสำหรับ User นี้ในปีงบประมาณนี้</td>
                                        </tr>
                                    @endif
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">ไม่มี User ที่มีรายการค่าถังขยะรายปีสำหรับปีงบประมาณนี้</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="d-flex justify-content-center mt-3">
                            {{ $users->appends(['fy' => $fiscalYear])->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
