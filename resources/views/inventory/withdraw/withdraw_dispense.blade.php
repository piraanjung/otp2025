@extends('inventory.inv_master')

@section('title', 'บันทึกจ่ายพัสดุ')
@section('header_title', 'บันทึกจ่ายพัสดุและตัดสต็อก (Ref: ' . $refNo . ')')

@section('content')
<div class="card p-4">
    <div class="alert alert-warning mb-4">
        <i class="material-icons-round align-middle">info</i> 
        เจ้าหน้าที่สามารถปรับลดจำนวนที่จ่ายจริง (ต้องไม่เกินจำนวนที่ขอเบิก) หรือกดยกเลิกรายการพัสดุที่หมดได้ ก่อนยืนยันการตัดสต็อก
    </div>

    <form action="{{ route('inventory.withdraw.dispense_process', $refNo) }}" method="POST">
        @csrf

        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-light text-center">
                    <tr>
                        <th width="5%">#</th>
                        <th>รายการพัสดุ</th>
                        <th width="10%">จำนวนที่ขอเบิก</th>
                        <th width="10%">จำนวนที่จะจ่ายจริง</th>
                        <th width="10%">หน่วยนับ</th>
                        <th width="10%">จัดการ</th>
                        <th width="20%">หมายเหตุ</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transactions as $index => $tx)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>
                            <span class="fw-bold text-primary">{{ $tx->item->name ?? '-' }}</span>
                            <br><small class="text-muted">รหัส: {{ $tx->item->code ?? '-' }}</small>
                            @if($tx->detail)
                                <br><span class="badge bg-light text-dark border">Lot: {{ $tx->detail->lot_number }} (คงเหลือในคลัง: {{ $tx->detail->current_qty }})</span>
                            @endif
                            <!-- เก็บ ID ของแต่ละรายการ -->
                            <input type="hidden" name="items[{{ $index }}][id]" value="{{ $tx->id }}">
                        </td>
                        <td class="text-center fw-bold">
                            {{ $tx->quantity }}
                            <input type="hidden" name="items[{{ $index }}][requested_qty]" value="{{ $tx->quantity }}">
                        </td>
                        <td>
                            <!-- ช่องกรอกจำนวนจ่ายจริง (ห้ามเกินจำนวนที่ขอเบิก) -->
                            <input type="number" name="items[{{ $index }}][dispensed_qty]" 
                                   class="form-control text-center fw-bold" 
                                   value="{{ $tx->quantity }}" 
                                   min="0" max="{{ $tx->quantity }}" step="1" required>
                        </td>
                        <td class="text-center">{{ $tx->item->unit ?? 'หน่วย' }}</td>
                        <td class="text-center">
                            <!-- ปุ่มสำหรับยกเลิก/ลบรายการนี้ออกจากการเบิก -->
                            <div class="form-check form-switch d-flex justify-content-center" title="ติ๊กเพื่อยกเลิกรายการนี้">
                                <input class="form-check-input" type="checkbox" name="items[{{ $index }}][cancel]" value="1" style="width: 40px; height: 20px; cursor: pointer;">
                            </div>
                            <small class="text-danger d-block mt-1">ยกเลิกรายการ</small>
                        </td>
                        <td>
                            <textarea name="items[{{ $index }}][comment]" id="comment" class="form-control"></textarea>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-between mt-4">
            <a href="{{ route('inventory.history') }}" class="btn btn-secondary">
                <i class="material-icons-round align-middle">arrow_back</i> กลับ
            </a>
            <button type="submit" class="btn btn-success btn-lg" onclick="return confirm('ยืนยันการจ่ายพัสดุและตัดสต็อกตามจำนวนนี้?');">
                <i class="material-icons-round align-middle">check_circle</i> ยืนยันการจ่ายพัสดุและตัดสต็อก
            </button>
        </div>
    </form>
</div>
@endsection