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
    <!-- คอลัมน์ วันที่/เลขที่ -->
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

    <!-- 🔍 คอลัมน์ พัสดุ (แก้ไขตรงนี้ให้ดึงทุกรายการที่มี ref_no เดียวกันมาแสดง) -->
    <td>
        @php
            // ดึงรายการพัสดุทั้งหมดที่อยู่ในใบเบิกเลขที่ (ref_no) นี้
            $groupItems = $trans->ref_no 
                ? \App\Models\InvTransaction::where('ref_no', $trans->ref_no)->with(['item', 'detail'])->get() 
                : collect([$trans]);
        @endphp

        @foreach($groupItems as $idx => $gItem)
            <div class="{{ $idx > 0 ? 'mt-2 pt-2 border-top' : '' }}">
                <span class="fw-bold text-primary">{{ $gItem->item->name ?? '-' }}</span>
                @if($gItem->item->code ?? false)
                    <small class="text-muted">({{ $gItem->item->code }})</small>
                @endif
                @if($gItem->detail ?? false)
                    <br><span class="badge bg-light text-dark border" style="font-size: 11px;">Lot: {{ $gItem->detail->lot_number }}</span>
                @endif
            </div>
        @endforeach
    </td>

    <!-- คอลัมน์ ผู้เบิก/ผู้ทำรายการ -->
    <td>
        <div class="mb-1">
            <span class="text-dark fw-bold"><i class="material-icons-round fs-6 align-middle text-muted">person</i> {{ $trans->requester_name }}</span>
        </div>
        <small class="text-muted fst-italic d-block text-truncate" style="max-width: 150px;">
            "{{ $trans->purpose }}"
        </small>
    </td>

    <!-- คอลัมน์ สถานะ -->
    <td class="text-center">
        @if($trans->status == 'PENDING')
            <span class="badge rounded-pill bg-warning text-dark">
                <i class="material-icons-round align-middle" style="font-size:12px;">hourglass_empty</i> รออนุมัติ
            </span>
        @elseif($trans->status == 'APPROVED')
            <span class="badge rounded-pill bg-success">
                <i class="material-icons-round align-middle" style="font-size:12px;">check_circle</i> อนุมัติแล้ว
            </span>
        @else
            <span class="badge bg-secondary">{{ $trans->status }}</span>
        @endif
    </td>

    <!-- 📊 คอลัมน์ จำนวน (แสดงผลรวมหรือแสดงแยกตามรายการ) -->
    <td class="text-end">
        @foreach($groupItems as $idx => $gItem)
            <div class="{{ $idx > 0 ? 'mt-2 pt-2 border-top' : '' }}">
                <h6 class="m-0 fw-bold {{ $gItem->status == 'APPROVED' ? 'text-danger' : 'text-secondary' }}">
                    {{ number_format($gItem->quantity) }}
                </h6>
                <small class="text-muted">{{ $gItem->item->unit ?? 'หน่วย' }}</small>
            </div>
        @endforeach
    </td>

    <!-- คอลัมน์ จัดการ (กดแล้ววิ่งไปหน้า Print/Show แบบกลุ่ม ref_no) -->
<td class="text-center">
    @if($trans->ref_no)
        <a href="{{ route('inventory.withdraw.show_ref', $trans->ref_no) }}" 
           class="btn btn-sm btn-outline-primary shadow-sm"
           data-bs-toggle="tooltip" title="ดูรายละเอียดใบเบิกทั้งหมด">
            <i class="material-icons-round fs-6">visibility</i>
        </a>

        {{-- เพิ่มปุ่มให้เจ้าหน้าที่พัสดุกด "จ่ายพัสดุ" เมื่อสถานะเป็น APPROVED แล้ว --}}
        @if($trans->status == 'APPROVED')
            <a href="{{ route('inventory.withdraw.dispense_form', $trans->ref_no) }}" 
               class="btn btn-sm btn-success shadow-sm ms-1"
               data-bs-toggle="tooltip" title="บันทึกจ่ายพัสดุและตัดสต็อก">
                <i class="material-icons-round fs-6">local_shipping</i>
            </a>
        @endif
    @else
        <a href="{{ route('inventory.withdraw.show', $trans->id) }}" 
           class="btn btn-sm btn-outline-primary shadow-sm"
           data-bs-toggle="tooltip" title="ดูรายละเอียด">
            <i class="material-icons-round fs-6">visibility</i>
        </a>
    @endif
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