@extends('layouts.keptkaya')

@section('content')
<div class="container-fluid py-4">
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white py-3">
            <h4 class="fw-bold mb-0">บันทึกการขายขยะให้โรงหลอม/ผู้รับซื้อ</h4>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('admin.bulk_sales.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">วันที่ขาย</label>
                        <input type="date" name="sale_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">ชื่อร้านผู้รับซื้อ / โรงหลอม</label>
                        <input type="text" name="buyer_name" class="form-control" placeholder="เช่น ร้านเฮียสมศักดิ์ รีไซเคิล" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">รูปใบชั่งน้ำหนัก / ใบเสร็จ</label>
                        <input type="file" name="receipt_image" class="form-control" accept="image/*">
                    </div>
                </div>

                <h5 class="fw-bold mb-3 border-bottom pb-2">รายการขยะที่ขาย</h5>
                <table class="table table-bordered align-middle" id="itemsTable">
                    <thead class="bg-light text-center">
                        <tr>
                            <th style="width: 40%;">ประเภทขยะ</th>
                            <th>น้ำหนัก (กก.)</th>
                            <th>ราคาขาย/หน่วย (บาท)</th>
                            <th style="width: 50px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="item-row">
                            <td>
                                <select name="items[0][kp_tbank_item_id]" class="form-select select2" required>
                                    <option value="">-- เลือกประเภทขยะ --</option>
                                    @foreach($items as $type)
                                        <option value="{{ $type->id }}">{{ $type->item_name }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td><input type="number" name="items[0][weight_kg]" class="form-control text-center weight-input" step="0.01" required></td>
                            <td><input type="number" name="items[0][price_per_unit]" class="form-control text-center price-input" step="0.1" required></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
                <button type="button" class="btn btn-outline-primary btn-sm mb-4" id="addRow">
                    <i class="bi bi-plus-circle"></i> เพิ่มประเภทขยะ
                </button>

                <div class="text-end border-top pt-4">
                    <button type="submit" class="btn btn-success px-5 py-2 rounded-pill fw-bold">
                        <i class="bi bi-save"></i> บันทึกรายการขายและกำไร
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let rowIdx = 1;
    document.getElementById('addRow').addEventListener('click', function() {
        const table = document.querySelector('#itemsTable tbody');
        const newRow = document.querySelector('.item-row').cloneNode(true);

        // ล้างข้อมูลในแถวใหม่
        newRow.querySelectorAll('input').forEach(input => input.value = '');
        newRow.querySelectorAll('select').forEach(select => {
            select.name = `items[${rowIdx}][kp_tbank_item_id]`;
            select.value = '';
        });
        newRow.querySelector('.weight-input').name = `items[${rowIdx}][weight_kg]`;
        newRow.querySelector('.price-input').name = `items[${rowIdx}][price_per_unit]`;

        // เพิ่มปุ่มลบ
        const delBtn = document.createElement('td');
        delBtn.innerHTML = '<button type="button" class="btn btn-outline-danger btn-sm remove-row"><i class="bi bi-trash"></i></button>';
        newRow.replaceChild(delBtn, newRow.lastElementChild);

        table.appendChild(newRow);
        rowIdx++;
    });

    // ลบแถว
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-row')) {
            e.target.closest('tr').remove();
        }
    });
</script>
@endsection
