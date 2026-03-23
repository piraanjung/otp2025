@extends('layouts.annual_trash')
@section('content')
    <div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 fw-bold"><i class="bi bi-calendar-check text-primary"></i> จัดการค่าขยะรายปี</h5>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th>ชื่อ-นามสกุล</th>
                    <th>เบอร์โทร</th>
                    <th>สถานะปัจจุบัน</th>
                    <th>หนี้ค้างชำระ</th>
                    <th>เหตุผลล่าสุด</th>
                    <th>จัดการ</th>
                </tr>
            </thead>
            <tbody>
                @foreach($subscriptions as $sub)
                <tr>
                    <td>{{ $sub->user->firstname }} {{ $sub->user->lastname }}</td>
                    <td>{{ $sub->user->phone }}</td>
                    <td>
                        @if($sub->billing_status == 'waived')
                            <span class="badge bg-success-light text-success">ฟรี (Waived)</span>
                        @elseif($sub->billing_status == 'pending')
                            <span class="badge bg-warning-light text-warning">ค้างชำระ</span>
                        @else
                            <span class="badge bg-primary-light text-primary">จ่ายแล้ว</span>
                        @endif
                    </td>
                    <td class="fw-bold">฿{{ number_format($sub->current_debt, 2) }}</td>
                    <td class="small text-muted">{{ $sub->waive_reason }}</td>
                    <td>
                        <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editModal{{$sub->id}}">
                            <i class="bi bi-pencil-square"></i> แก้ไขสิทธิ์
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
