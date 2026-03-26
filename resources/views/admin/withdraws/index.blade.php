@extends('layouts.super-admin')

@section('content')
<div class="container-fluid py-4">
    <h2 class="mb-4">รายการอนุมัติถอนเงินสด (วันนัดรับเงิน)</h2>

    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>วันที่นัดรับ</th>
                        <th>ผู้ขอถอน / ผู้รับแทน</th>
                        <th>จำนวนเงิน</th>
                        <th>สถานะ</th>
                        <th class="text-center" style="width: 250px;">ยืนยันการจ่ายเงิน</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($requests as $item)
                    <tr>
                        <td><strong>{{ \Carbon\Carbon::parse($item->payout_date)->format('d/m/Y') }}</strong></td>
                        <td>
                            <div>{{ $item->user->name }}</div>
                            @if($item->is_proxy)
                                <small class="text-danger">รับแทนโดย: {{ $item->proxy_name }}</small>
                            @endif
                        </td>
                        <td class="fw-bold text-primary">{{ number_format($item->amount, 2) }}</td>
                        <td><span class="badge bg-warning text-dark">รอรับเงิน</span></td>
                        <td>
                            <form action="{{ route('admin.withdraws.verify', $item->id) }}" method="POST" class="d-flex gap-2">
                                @csrf
                                <input type="text" name="input_code" class="form-control form-control-sm text-center fw-bold"
                                       placeholder="รหัส 6 หลัก" maxlength="6" required>
                                <button type="submit" class="btn btn-sm btn-success text-nowrap">ยืนยัน</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
