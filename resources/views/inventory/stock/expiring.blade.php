@extends('inventory.inv_master')

@section('title', 'รายการใกล้หมดอายุ')
@section('header_title', 'รายการพัสดุ/ขวดที่ใกล้หมดอายุ (ภายใน 30 วัน)')

@section('content')
<div class="card shadow-sm border-0 p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold text-warning m-0">
            <i class="material-icons-round align-middle">warning</i> รายการที่ต้องตรวจสอบและวางแผนสั่งซื้อ
        </h5>
        <a href="{{ route('inventory.dashboard') }}" class="btn btn-outline-secondary btn-sm">
            <i class="material-icons-round align-middle fs-6">arrow_back</i> กลับหน้าแดชบอร์ด
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="bg-light">
                <tr>
                    <th>รหัส / ชื่อพัสดุ</th>
                    <th>หมวดหมู่</th>
                    <th>ล็อต / รายละเอียดขวด</th>
                    <th>วันหมดอายุ</th>
                    <th class="text-center">สถานะ</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $detail)
                <tr>
                    <td>
                        <div class="fw-bold text-dark">{{ optional($detail->item)->name }}</div>
                        <small class="text-muted">Code: {{ optional($detail->item)->code }}</small>
                    </td>
                    <td>
                        <span class="badge bg-light text-dark border">
                            {{ optional(optional($detail->item)->category)->name ?? '-' }}
                        </span>
                    </td>
                    <td>
                        <div>รหัสขวด/ล็อต: <span class="fw-semibold text-primary">#{{ $detail->id }}</span></div>
                        <small class="text-muted">หน่วย: {{ optional($detail->item)->unit }}</small>
                    </td>
                    <td>
                        @php
                            $expireDate = \Carbon\Carbon::parse($detail->expire_date);
                            $isExpired = $expireDate->isPast();
                        @endphp
                        <span class="fw-bold {{ $isExpired ? 'text-danger' : 'text-warning' }}">
                            {{ $expireDate->format('d/m/Y') }}
                        </span>
                        <br>
                        <small class="text-muted">({{ $expireDate->diffForHumans() }})</small>
                    </td>
                    <td class="text-center">
                        @if($isExpired)
                            <span class="badge bg-danger">หมดอายุแล้ว</span>
                        @else
                            <span class="badge bg-warning text-dark">ใกล้หมดอายุ</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-5 text-muted">
                        <i class="material-icons-round fs-1 text-success mb-2">task_alt</i>
                        <p class="mb-0">ยอดเยี่ยม! ไม่มีพัสดุหรือขวดที่ใกล้หมดอายุในขณะนี้</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-3">
        {{ $items->links() }}
    </div>
</div>
@endsection