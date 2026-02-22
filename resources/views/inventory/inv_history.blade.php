@extends('inventory.inv_master')

@section('title', 'ประวัติการเบิกจ่าย')
@section('header_title', 'ประวัติการทำรายการ (Transaction Logs)')

@section('content')
<div class="card p-4">
    
    <form action="{{ route('inventory.history') }}" method="GET" class="mb-4">
        <div class="row g-2 align-items-end bg-light p-3 rounded border">
            <div class="col-md-4">
                <label class="form-label small text-muted">ค้นหาพัสดุ</label>
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="material-icons-round fs-6">search</i></span>
                    <input type="text" name="search" class="form-control" placeholder="ชื่อ หรือ รหัสพัสดุ" value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted">ตั้งแต่วันที่</label>
                <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted">ถึงวันที่</label>
                <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100 btn-material">
                    ค้นหา
                </button>
            </div>
        </div>
    </form>

    <div class="table-responsive">
    <table class="table table-hover align-middle">
        <thead class="bg-light">
            <tr>
                <th>วันที่/เลขที่</th> <th>พัสดุ</th>
                <th>ผู้เบิก/ผู้ทำรายการ</th>
                <th class="text-center">สถานะ</th> <th class="text-end">จำนวน</th>
                <th class="text-center" width="10%">จัดการ</th> </tr>
        </thead>
        <tbody>
            @forelse($transactions as $trans)
            <tr>
                <td>
                    <div class="fw-bold text-dark">{{ \Carbon\Carbon::parse($trans->created_at)->format('d/m/Y') }}</div>
                    <small class="text-muted">{{ \Carbon\Carbon::parse($trans->created_at)->format('H:i') }} น.</small>
                    
                    @if($trans->ref_no)
                        <div class="mt-1">
                            <span class="badge bg-light text-secondary border">
                                <i class="material-icons-round fs-6 align-text-bottom" style="font-size: 10px;">receipt</i>
                                {{ $trans->ref_no }}
                            </span>
                        </div>
                    @endif
                </td>

                <td>
                    <span class="fw-bold text-primary">{{ $trans->item->name }}</span>
                    @if($trans->item->code)
                        <br><small class="text-muted">{{ $trans->item->code }}</small>
                    @endif
                    @if($trans->detail)
                        <br><span class="badge bg-light text-dark border">Lot: {{ $trans->detail->lot_number }}</span>
                    @endif
                </td>

                <td>
                    <div class="mb-1">
                        <span class="text-dark fw-bold"><i class="material-icons-round fs-6 align-middle text-muted">person</i> {{ $trans->requester_name }}</span>
                    </div>
                    <small class="text-muted fst-italic d-block text-truncate" style="max-width: 150px;">
                        "{{ $trans->purpose }}"
                    </small>
                </td>

                <td class="text-center">
                    @if($trans->status == 'PENDING')
                        <span class="badge rounded-pill bg-warning text-dark">
                            <i class="material-icons-round align-middle" style="font-size:12px;">hourglass_empty</i> รออนุมัติ
                        </span>
                    @elseif($trans->status == 'APPROVED')
                        <span class="badge rounded-pill bg-success">
                            <i class="material-icons-round align-middle" style="font-size:12px;">check_circle</i> อนุมัติแล้ว
                        </span>
                        <div class="small text-muted mt-1" style="font-size: 10px;">
                            โดย {{ $trans->approver_user->name ?? '-' }}
                        </div>
                    @else
                        <span class="badge bg-secondary">{{ $trans->status }}</span>
                    @endif
                </td>

                <td class="text-end">
                    <h6 class="m-0 fw-bold {{ $trans->status == 'APPROVED' ? 'text-danger' : 'text-secondary' }}">
                        {{ number_format($trans->quantity) }}
                    </h6>
                    <small class="text-muted">{{ $trans->item->unit }}</small>
                </td>

                <td class="text-center">
                    <a href="{{ route('inventory.withdraw.show', $trans->id) }}" 
                       class="btn btn-sm btn-outline-primary shadow-sm"
                       data-bs-toggle="tooltip" title="ดูรายละเอียด/อนุมัติ">
                        <i class="material-icons-round fs-6">visibility</i>
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center py-5 text-muted">
                    <i class="material-icons-round display-4 opacity-25">history</i>
                    <p>ไม่พบประวัติการทำรายการ</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

    <div class="mt-3">
        {{ $transactions->links() }}
    </div>
</div>
@endsection