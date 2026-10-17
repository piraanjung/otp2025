@extends('layouts.foodwaste')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-0">สมาชิกและแต้มคงเหลือ (ธนาคารขยะเปียก)</h6>
                    <p class="text-sm text-secondary mb-0">เรียงตามแต้มสะสมมากไปน้อย</p>
                </div>
                <a href="{{ route('foodwaste.admin.fw_bank.dashboard') }}" class="btn btn-outline-secondary btn-sm mb-0">
                    <i class="fas fa-chart-pie me-1"></i> แดชบอร์ด
                </a>
            </div>
            <div class="card-body px-0 pt-0 pb-2">
                <div class="table-responsive p-0">
                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">สมาชิก</th>
                                <th class="text-end text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">แต้มคงเหลือ</th>
                                <th class="text-end text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">น้ำหนักรวม (กก.)</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ส่งล่าสุด</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($accounts as $account)
                                <tr>
                                    <td class="ps-4">
                                        <h6 class="mb-0 text-sm">
                                            {{ trim(($account->user->firstname ?? '') . ' ' . ($account->user->lastname ?? '')) ?: ($account->user->username ?? '-') }}
                                        </h6>
                                    </td>
                                    <td class="text-end text-sm font-weight-bold">{{ number_format($account->points_balance) }}</td>
                                    <td class="text-end text-sm">{{ number_format($account->total_weight_kg, 2) }}</td>
                                    <td class="text-sm text-secondary">{{ $account->last_contributed_at ?: '-' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-secondary py-4">ยังไม่มีข้อมูลบัญชีแต้ม</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center mt-3">
                    {{ $accounts->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
