@extends('layouts.keptkaya')

@section('nav-current', 'รายการถอนเงินสด')

@section('content')
<div class="container-fluid py-2">

    <!-- 1. Top Summary Metric Cards -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-sm-6">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md me-3">
                            <i class="fas fa-hand-holding-usd text-lg opacity-10" aria-hidden="true"></i>
                        </div>
                        <div>
                            <p class="text-xs mb-0 text-capitalize font-weight-bold">ยอดขอถอนรอบนี้</p>
                            <h5 class="font-weight-bolder mb-0">
                                {{ number_format($urgentAmount ?? 0, 2) }} <span class="text-xs text-secondary font-weight-normal">บาท</span>
                            </h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="icon icon-shape bg-gradient-warning shadow text-center border-radius-md me-3">
                            <i class="fas fa-clock text-lg opacity-10" aria-hidden="true"></i>
                        </div>
                        <div>
                            <p class="text-xs mb-0 text-capitalize font-weight-bold">รอตัดรอบอนุมัติ</p>
                            <h5 class="font-weight-bolder mb-0">
                                {{ $pendingCount ?? 0 }} <span class="text-xs text-secondary font-weight-normal">รายการ</span>
                            </h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="icon icon-shape bg-gradient-success shadow text-center border-radius-md me-3">
                            <i class="fas fa-check-circle text-lg opacity-10" aria-hidden="true"></i>
                        </div>
                        <div>
                            <p class="text-xs mb-0 text-capitalize font-weight-bold">พร้อมจ่ายวันอังคาร</p>
                            <h5 class="font-weight-bolder mb-0">
                                {{ $readyCount ?? 0 }} <span class="text-xs text-secondary font-weight-normal">รายการ</span>
                            </h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md me-3">
                            <i class="fas fa-calendar-alt text-lg opacity-10" aria-hidden="true"></i>
                        </div>
                        <div>
                            <p class="text-xs mb-0 text-capitalize font-weight-bold">วันจ่ายเงินถัดไป</p>
                            <h5 class="font-weight-bolder mb-0 text-sm">
                                {{ $nextPayoutDate ?? '—' }}
                            </h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Main Action & Filter Bar -->
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-header bg-white pb-0">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5 class="font-weight-bolder text-dark mb-1">
                        <i class="fas fa-money-bill-wave text-primary me-2"></i>จัดการรายการถอนเงินสด
                    </h5>
                    <p class="text-xs text-secondary mb-0">ค้นหารายการ สแกนรหัสรับเงิน และตรวจสอบคำขอถอนเงิน</p>
                </div>
                <div class="col-md-6 text-end mt-3 mt-md-0">
                    <!-- ปุ่มคีย์ถอนเงิน On-site หน้าเคาน์เตอร์ สำหรับผู้สูงอายุ -->
                    <a href="{{ route('keptkayas.withdraw.create', Auth::id()) }}" class="btn bg-gradient-primary me-2 mb-0">
                        <i class="fas fa-plus me-1"></i> คีย์ถอนเงินหน้าเคาน์เตอร์
                    </a>
                    <!-- ปุ่มสแกน QR Code รับเงินสดวันอังคาร -->
                    <button type="button" class="btn bg-gradient-success mb-0" data-bs-toggle="modal" data-bs-target="#scanVerifyModal">
                        <i class="fas fa-qrcode me-1"></i> จ่ายเงินสด (สแกนรหัส)
                    </button>
                </div>
            </div>

            <!-- Filter Controls -->
            <form action="{{ route('admin.withdraws.index') }}" method="GET" class="row g-2 mt-3 mb-2">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control text-xs" placeholder="ค้นหาชื่อสมาชิก, เลขบัญชี, รหัสยืนยัน..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select text-xs">
                        <option value="">-- สถานะทั้งหมด --</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>รอตัดรอบ (Pending)</option>
                        <option value="in_review" {{ request('status') == 'in_review' ? 'selected' : '' }}>อยู่ระหว่างเสนออนุมัติ (In Review)</option>
                        <option value="ready" {{ request('status') == 'ready' ? 'selected' : '' }}>พร้อมจ่ายเงินสด (Ready)</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>จ่ายเงินสำเร็จ (Completed)</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="date" name="payout_date" class="form-control text-xs" value="{{ request('payout_date') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-outline-secondary w-100 mb-0 text-xs">
                        <i class="fas fa-search me-1"></i> กรองข้อมูล
                    </button>
                </div>
            </form>
        </div>

        <!-- 3. Table List -->
        <div class="card-body px-0 pt-0 pb-2">
            <div class="table-responsive p-0">
                <table class="table align-items-center mb-0 text-sm">
                    <thead>
                        <tr>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ยื่นเรื่องเมื่อ</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">สมาชิกเจ้าของบัญชี</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">ผู้มีสิทธิ์รับเงิน</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">จำนวนเงิน</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">กำหนดรับเงิน</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">สถานะ</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($requests as $req)
                            <tr>
                                <td class="ps-3">
                                    <span class="text-xs font-weight-bold d-block">{{ $req->created_at->format('d/m/Y') }}</span>
                                    <span class="text-xxs text-muted">{{ $req->created_at->format('H:i') }} น.</span>
                                </td>
                                <td>
                                    <div class="d-flex px-2 py-1">
                                        <div class="d-flex flex-column justify-content-center">
                                            <h6 class="mb-0 text-xs">{{ $req->user->firstname ?? '' }} {{ $req->user->lastname ?? '' }}</h6>
                                            <p class="text-xxs text-secondary mb-0">รหัสยืนยัน: <strong class="text-dark">{{ $req->verification_code }}</strong></p>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($req->is_proxy)
                                        <span class="badge bg-gradient-warning text-xxs">
                                            <i class="fas fa-user-friends me-1"></i>รับแทน: {{ $req->proxy_name }}
                                        </span>
                                        <small class="d-block text-xxs text-muted">ID: {{ $req->proxy_id_card }}</small>
                                    @else
                                        <span class="badge bg-gradient-light text-dark text-xxs">รับด้วยตนเอง</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="text-xs font-weight-bolder text-dark">{{ number_format($req->amount, 2) }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="text-xs font-weight-bold text-info">
                                        {{ \Carbon\Carbon::parse($req->payout_date)->format('d/m/Y') }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if($req->status == 'pending')
                                        <span class="badge bg-gradient-secondary text-xxs">รอตัดรอบ</span>
                                    @elseif($req->status == 'in_review')
                                        <span class="badge bg-gradient-info text-xxs">เสนออนุมัติ</span>
                                    @elseif($req->status == 'ready')
                                        <span class="badge bg-gradient-success text-xxs">พร้อมจ่ายเงินสด</span>
                                    @elseif($req->status == 'completed')
                                        <span class="badge bg-gradient-dark text-xxs">จ่ายสำเร็จ</span>
                                    @elseif($req->status == 'rejected')
                                        <span class="badge bg-gradient-danger text-xxs">ไม่อนุมัติ</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($req->status == 'ready')
                                        <button class="btn btn-link text-success p-0 mb-0" onclick="openPayoutModal({{ $req->id }}, '{{ $req->verification_code }}', {{ $req->amount }})">
                                            <i class="fas fa-cash-register me-1"></i> จ่ายเงิน
                                        </button>
                                    @else
                                        <span class="text-xs text-muted">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-xs text-muted">ไม่พบรายการถอนเงินตามเงื่อนไขที่ค้นหา</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-3 mt-3">
                {{ $requests->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Modal สแกนจ่ายเงินสดวันอังคาร -->
<div class="modal fade" id="scanVerifyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title font-weight-bolder"><i class="fas fa-qrcode text-success me-2"></i>ยืนยันจ่ายเงินสดหน้าเคาน์เตอร์</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.withdraws.verify', 0) }}" id="payoutForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label font-weight-bold text-sm">กรอกรหัสยืนยัน (6 หลัก) หรือสแกน QR Code</label>
                        <input type="text" name="input_code" id="input_code" class="form-control form-control-lg text-center font-weight-bolder tracking-wider" placeholder="000000" maxlength="6" required autofocus>
                    </div>
                    <div class="alert alert-warning text-xs text-dark mb-0">
                        <i class="fas fa-exclamation-triangle me-1"></i> กรุณาตรวจสอบบัตรประชาชนตัวจริงของผู้รับเงิน ก่อนกดปุ่มยืนยันจ่ายเงินสด
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn bg-gradient-success">ยืนยันจ่ายเงินสด & ตัด Ledger</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openPayoutModal(id, code, amount) {
        document.getElementById('payoutForm').action = "{{ url('admin/withdraws/verify') }}/" + id;
        document.getElementById('input_code').value = code;
        var myModal = new bootstrap.Modal(document.getElementById('scanVerifyModal'));
        myModal.show();
    }
</script>
@endsection