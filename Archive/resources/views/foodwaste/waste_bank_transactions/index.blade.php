@extends('layouts.keptkaya')

@section('title_page', 'ประวัติรายการรับซื้อขยะ')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h6>ประวัติรายการรับซื้อขยะรีไซเคิล</h6>
                    <a href="{{ route('waste_bank_transactions.create') }}" class="btn bg-gradient-primary btn-sm mb-0">
                        <i class="fas fa-plus me-1"></i> บันทึกรายการใหม่
                    </a>
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
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">รหัสรายการ</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">วันที่</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">สมาชิก</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">ยอดจ่ายสมาชิก (฿)</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">ยอดรับจากโรงงาน (฿)</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">สถานะ</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">เจ้าหน้าที่</th>
                                    <th class="text-secondary opacity-7">การจัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transactions as $transaction)
                                <tr>
                                    <td>
                                        <div class="d-flex px-2 py-1">
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-sm">{{ $transaction->receipt_code ?? 'N/A' }}</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">{{ $transaction->transaction_date->format('d/m/Y') }}</p>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">{{ $transaction->user->firstname ?? 'N/A' }} {{ $transaction->user->lastname ?? '' }}</p>
                                    </td>
                                    <td class="align-middle text-center text-sm">
                                        <span class="text-xs font-weight-bold">{{ number_format($transaction->total_member_payout_amount, 2) }}</span>
                                    </td>
                                    <td class="align-middle text-center text-sm">
                                        <span class="text-xs font-weight-bold">{{ number_format($transaction->estimated_factory_revenue, 2) }}</span>
                                    </td>
                                    <td class="align-middle text-center text-sm">
                                        <span class="badge badge-sm bg-gradient-{{ $transaction->status == 'completed' ? 'success' : 'secondary' }}">{{ ucfirst($transaction->status) }}</span>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">{{ $transaction->staff->firstname ?? 'N/A' }}</p>
                                    </td>
                                    <td class="align-middle">
                                        <a href="{{ route('waste_bank_transactions.show', $transaction->id) }}" class="btn btn-link text-secondary font-weight-bold text-xs px-0 mb-0 me-2">
                                            <i class="fas fa-eye me-1"></i> ดูรายละเอียด
                                        </a>
                                        {{-- Add Edit/Delete buttons if you implement them --}}
                                        {{--
                                        <a href="#" class="btn btn-link text-info text-gradient px-0 mb-0 me-2">
                                            <i class="fas fa-edit me-1"></i> แก้ไข
                                        </a>
                                        <form action="#" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-link text-danger text-gradient px-0 mb-0" onclick="return confirm('คุณแน่ใจหรือไม่ที่จะลบรายการนี้?')">
                                                <i class="fas fa-trash-alt me-1"></i> ลบ
                                            </button>
                                        </form>
                                        --}}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center">ยังไม่มีรายการรับซื้อขยะ</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="d-flex justify-content-center mt-3">
                            {{ $transactions->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
