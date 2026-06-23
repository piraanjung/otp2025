@extends('layouts.keptkaya')

@section('title_page', 'ทะเบียนคุมผู้ชำระค่าธรรมเนียม (กค.3)')
@section('nav-current', 'ทะเบียนคุม กค.3')

@section('content')
    <div class="container-fluid py-4">
        
        {{-- SECTION 1: Dashboard สรุปยอดเงิน --}}
        <div class="row mb-4">
            {{-- Card 1: จำนวนราย --}}
            <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                <div class="card">
                    <div class="card-header p-3 pt-2">
                        <div class="icon icon-lg icon-shape bg-gradient-dark shadow-dark text-center border-radius-xl mt-n4 position-absolute">
                            <i class="fas fa-users opacity-10"></i>
                        </div>
                        <div class="text-end pt-1">
                            <p class="text-sm mb-0 text-capitalize">ลูกหนี้ทั้งหมด</p>
                            <h4 class="mb-0">{{ number_format($totalItems) }} ราย</h4>
                        </div>
                    </div>
                    <hr class="dark horizontal my-0">
                    <div class="card-footer p-3">
                        <p class="mb-0"><span class="text-success text-sm font-weight-bolder">ปีงบ {{ $fiscalYear }}</span></p>
                    </div>
                </div>
            </div>

            {{-- Card 2: ยอดประเมินรวม --}}
            <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                <div class="card">
                    <div class="card-header p-3 pt-2">
                        <div class="icon icon-lg icon-shape bg-gradient-primary shadow-primary text-center border-radius-xl mt-n4 position-absolute">
                            <i class="fas fa-money-bill-wave opacity-10"></i>
                        </div>
                        <div class="text-end pt-1">
                            <p class="text-sm mb-0 text-capitalize">ยอดประเมินรวม</p>
                            <h4 class="mb-0">{{ number_format($totalRevenue, 2) }} ฿</h4>
                        </div>
                    </div>
                    <hr class="dark horizontal my-0">
                    <div class="card-footer p-3">
                        <p class="mb-0 text-sm">เป้าหมายการจัดเก็บ</p>
                    </div>
                </div>
            </div>

            {{-- Card 3: เก็บได้แล้ว --}}
            <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                <div class="card">
                    <div class="card-header p-3 pt-2">
                        <div class="icon icon-lg icon-shape bg-gradient-success shadow-success text-center border-radius-xl mt-n4 position-absolute">
                            <i class="fas fa-hand-holding-usd opacity-10"></i>
                        </div>
                        <div class="text-end pt-1">
                            <p class="text-sm mb-0 text-capitalize">เก็บได้แล้ว</p>
                            <h4 class="mb-0 text-success">{{ number_format($totalCollected, 2) }} ฿</h4>
                        </div>
                    </div>
                    <hr class="dark horizontal my-0">
                    <div class="card-footer p-3">
                        <div class="progress mt-1" style="height: 6px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $collectionProgress }}%"></div>
                        </div>
                        <p class="mb-0 text-xs mt-1">ความคืบหน้า {{ number_format($collectionProgress, 1) }}%</p>
                    </div>
                </div>
            </div>

            {{-- Card 4: ค้างชำระ --}}
            <div class="col-xl-3 col-sm-6">
                <div class="card">
                    <div class="card-header p-3 pt-2">
                        <div class="icon icon-lg icon-shape bg-gradient-danger shadow-danger text-center border-radius-xl mt-n4 position-absolute">
                            <i class="fas fa-exclamation-circle opacity-10"></i>
                        </div>
                        <div class="text-end pt-1">
                            <p class="text-sm mb-0 text-capitalize">ค้างชำระ</p>
                            <h4 class="mb-0 text-danger">{{ number_format($totalOutstanding, 2) }} ฿</h4>
                        </div>
                    </div>
                    <hr class="dark horizontal my-0">
                    <div class="card-footer p-3">
                        <p class="mb-0 text-sm">ที่ต้องติดตาม</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- SECTION 2: Filter & Table --}}
        <div class="card">
            <div class="card-header p-3 pb-0">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0 text-primary font-weight-bold">
                        <i class="fas fa-book me-2"></i>ทะเบียนคุมผู้ชำระค่าธรรมเนียม (กค.3)
                    </h6>
                    <div>
                        {{-- ปุ่ม Export (Placeholder) --}}
                       <a href="{{ route('keptkayas.korkor3.export', request()->query()) }}" target="_blank" class="btn btn-outline-success btn-sm mb-0 me-1">
                            <i class="fas fa-file-excel me-1"></i> Export Excel
                        </a>
                    </div>
                </div>
                
                {{-- Form ค้นหา --}}
                <form action="{{ route('keptkayas.korkor3.index') }}" method="GET">
                    <div class="row bg-gray-100 border-radius-lg p-3 mx-0">
                        <div class="col-md-3">
                            <label class="form-label text-xs font-weight-bold">ค้นหา (ชื่อ / รหัสถัง)</label>
                            <div class="input-group input-group-outline bg-white">
                                <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="พิมพ์คำค้นหา...">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label text-xs font-weight-bold">ปีงบประมาณ</label>
                            <div class="input-group input-group-outline bg-white">
                                <select name="fy" class="form-control" onchange="this.form.submit()">
                                    @foreach($availableFiscalYears as $year)
                                        <option value="{{ $year }}" {{ $year == $fiscalYear ? 'selected' : '' }}>{{ $year }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label text-xs font-weight-bold">สถานะการชำระ</label>
                            <div class="input-group input-group-outline bg-white">
                                <select name="status" class="form-control" onchange="this.form.submit()">
                                    <option value="">ทั้งหมด</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>ค้างชำระ</option>
                                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>ชำระครบแล้ว</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary btn-sm mb-0 w-100">
                                <i class="fas fa-search"></i> ค้นหา
                            </button>
                        </div>
                        <div class="col-md-3 d-flex align-items-end justify-content-end">
                             @if(request('search') || request('status'))
                                <a href="{{ route('keptkayas.korkor3.index') }}" class="text-xs text-secondary font-weight-bold cursor-pointer">
                                    <i class="fas fa-times me-1"></i> ล้างการค้นหา
                                </a>
                             @endif
                        </div>
                    </div>
                </form>
            </div>

            <div class="card-body px-0 pb-2">
                <div class="table-responsive p-0">
                    <table class="table align-items-center mb-0 table-hover">
                        <thead class="bg-light">
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center" style="width: 5%">ลำดับ</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">ผู้ชำระภาษี / ที่อยู่</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">รหัสทรัพย์สิน (ถัง)</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">ประเภท</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-end">ยอดประเมิน</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-end">ค้างชำระ</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">สถานะ</th>
                                <th class="text-secondary opacity-7"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($registries as $index => $item)
                                <tr>
                                    <td class="align-middle text-center">
                                        <span class="text-secondary text-xs font-weight-bold">
                                            {{ $registries->firstItem() + $index }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex px-2 py-1">
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-sm">{{ $item->wasteBin->user->firstname ?? '-' }} {{ $item->wasteBin->user->lastname ?? '' }}</h6>
                                                <p class="text-xs text-secondary mb-0">
                                                    <i class="fas fa-map-marker-alt text-xxs me-1"></i>
                                                    {{ Str::limit($item->wasteBin->user->address ?? '-', 30) }}
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0 text-primary">{{ $item->wasteBin->bin_code }}</p>
                                    </td>
                                    <td class="align-middle text-center">
                                        <span class="badge badge-sm bg-gradient-light text-dark">
                                            {{ $item->wasteBin->kpUserGroup->usergroup_name ?? 'ทั่วไป' }}
                                        </span>
                                    </td>
                                    <td class="align-middle text-end">
                                        <span class="text-secondary text-xs font-weight-bold">
                                            {{ number_format($item->annual_fee, 2) }}
                                        </span>
                                    </td>
                                    <td class="align-middle text-end">
                                        @php $debt = $item->annual_fee - $item->total_paid_amt; @endphp
                                        <span class="text-xs font-weight-bold {{ $debt > 0 ? 'text-danger' : 'text-success' }}">
                                            {{ number_format($debt, 2) }}
                                        </span>
                                    </td>
                                    <td class="align-middle text-center text-sm">
                                        @if($item->status == 'paid')
                                            <span class="badge badge-sm bg-gradient-success">ครบถ้วน</span>
                                        @elseif($item->total_paid_amt > 0)
                                            <span class="badge badge-sm bg-gradient-warning">บางส่วน</span>
                                        @else
                                            <span class="badge badge-sm bg-gradient-danger">ค้างชำระ</span>
                                        @endif
                                    </td>
                                    <td class="align-middle">
                                        {{-- CRUD Action Buttons --}}
                                        <a href="#" class="btn btn-link text-dark px-2 mb-0" data-bs-toggle="tooltip" title="แก้ไขข้อมูล">
                                            <i class="fas fa-pencil-alt text-xs"></i>
                                        </a>
                                        <a href="{{ route('keptkayas.annual_payments.index', ['search' => $item->wasteBin->bin_code]) }}" class="btn btn-link text-primary px-2 mb-0" data-bs-toggle="tooltip" title="ไปหน้าชำระเงิน">
                                            <i class="fas fa-coins text-xs"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center p-4">
                                        <div class="text-secondary opacity-5">
                                            <i class="fas fa-folder-open fa-3x mb-3"></i>
                                            <p>ไม่พบข้อมูลในทะเบียนคุม ปี {{ $fiscalYear }}</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="d-flex justify-content-center mt-3">
                    {{ $registries->appends(request()->input())->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
@endsection