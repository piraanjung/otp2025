@extends('layouts.super-admin')

@section('content')
<div class="container-fluid py-4">
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white py-3">
            <h4 class="fw-bold mb-0">แก้ไขรายการขายขยะล๊อตใหญ่ (#{{ $sale->id }})</h4>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('admin.bulk_sales.update', $sale->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">วันที่ขาย</label>
                        <input type="date" name="sale_date" class="form-control" value="{{ $sale->sale_date }}" required>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label fw-bold">ชื่อร้านผู้รับซื้อ / โรงหลอม</label>
                        <input type="text" name="buyer_name" class="form-control" value="{{ $sale->buyer_name }}" required>
                    </div>
                </div>

                <table class="table table-bordered align-middle" id="itemsTable">
                    <thead class="bg-light">
                        <tr>
                            <th>ประเภทขยะ</th>
                            <th style="width: 20%;">น้ำหนัก (กก.)</th>
                            <th style="width: 20%;">ราคาขาย/หน่วย</th>
                            <th style="width: 50px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sale->details as $index => $detail)
                        <tr class="item-row">
                            <td>
                                <select name="items[{{ $index }}][kp_tbank_item_id]" class="form-select" required>
                                    @foreach($items as $type)
                                        <option value="{{ $type->id }}" {{ $detail->kp_tbank_item_id == $type->id ? 'selected' : '' }}>
                                            {{ $type->item_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td><input type="number" name="items[{{ $index }}][weight_kg]" class="form-control text-center" value="{{ $detail->weight_kg }}" step="0.01" required></td>
                            <td><input type="number" name="items[{{ $index }}][price_per_unit]" class="form-control text-center" value="{{ $detail->price_per_unit }}" step="0.1" required></td>
                            <td>
                                @if($index > 0)
                                    <button type="button" class="btn btn-outline-danger btn-sm remove-row"><i class="bi bi-trash"></i></button>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <button type="button" class="btn btn-outline-primary btn-sm mb-4" id="addRow"><i class="bi bi-plus-circle"></i> เพิ่มประเภทขยะ</button>

                <div class="text-end border-top pt-4">
                    <button type="submit" class="btn btn-primary px-5 py-2 rounded-pill fw-bold">บันทึกการแก้ไข</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
