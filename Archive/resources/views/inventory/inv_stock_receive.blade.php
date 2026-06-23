@extends('inventory.inv_master')

@section('title', 'รับของเข้าสต็อก')
@section('header_title', 'รับพัสดุ/สารเคมีเข้าคลัง')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        
        <div class="card mb-3 border-0 shadow-sm bg-primary text-white">
            <div class="card-body d-flex align-items-center">
                <div class="bg-white text-primary p-3 rounded-circle me-3">
                    <i class="material-icons-round fs-3">inventory_2</i>
                </div>
                <div>
                    <small class="text-white-50">กำลังทำรายการเพิ่มสต็อกให้กับ:</small>
                    <h4 class="mb-0 fw-bold">{{ $item->name }}</h4>
                    <span class="badge bg-white text-primary mt-1">{{ $item->code ?? 'No Code' }}</span>
                    <span class="badge bg-info text-dark mt-1">{{ $item->category->name ?? '-' }}</span>
                </div>
            </div>
        </div>

        <form action="{{ route('inventory.stock.store_receive') }}" method="POST">
            @csrf
            <input type="hidden" name="inv_item_id_fk" value="{{ $item->id }}">

            <div class="card p-4">
                <h5 class="text-primary fw-bold mb-4">
                    <i class="material-icons-round align-middle">add_shopping_cart</i> ข้อมูลล็อตการรับ
                </h5>

                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" class="form-control" name="lot_number" placeholder="Lot No.">
                            <label>Lot Number / Batch No.</label>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="date" class="form-control" name="expire_date">
                            <label>วันหมดอายุ (ถ้ามี)</label>
                        </div>
                    </div>

                    <div class="col-12"><hr></div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-secondary">ขนาดบรรจุ (ต่อขวด/กล่อง)</label>
                        <div class="input-group">
                            <input type="number" step="0.01" class="form-control" id="qty_per_unit" name="quantity_per_unit" 
                                   placeholder="เช่น 1000" required>
                            <span class="input-group-text bg-light">{{ $item->unit }}</span>
                        </div>
                        <div class="form-text">ปริมาณที่มีใน 1 ขวด</div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-secondary">จำนวนที่รับ (ขวด/กล่อง)</label>
                        <input type="number" class="form-control" id="amount" name="amount" 
                               placeholder="เช่น 5" required min="1">
                        <div class="form-text">จำนวนขวดที่ได้รับมาจริง</div>
                    </div>

                    <div class="col-12 mt-3">
                        <div class="alert alert-info d-flex align-items-center">
                            <i class="material-icons-round me-2">functions</i>
                            <div>
                                สรุปยอดรับเข้า: 
                                <span id="total-calc" class="fw-bold fs-5">0</span> 
                                {{ $item->unit }} 
                                (แยกเป็น <span id="bottle-calc">0</span> รายการ)
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-end mt-4">
                    <a href="{{ route('inventory.items.index') }}" class="btn btn-light text-muted me-2">ยกเลิก</a>
                    <button type="submit" class="btn btn-success btn-material btn-lg px-4">
                        <i class="material-icons-round align-middle">save_alt</i> ยืนยันรับเข้าสต็อก
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // ฟังก์ชันคำนวณยอดรวม Realtime
        function calculateTotal() {
            let perUnit = parseFloat($('#qty_per_unit').val()) || 0;
            let amount = parseInt($('#amount').val()) || 0;
            
            let total = perUnit * amount;
            
            // ใส่ลูกน้ำ (Comma) ให้ตัวเลขสวยงาม
            $('#total-calc').text(total.toLocaleString());
            $('#bottle-calc').text(amount);
        }

        // ดักจับ Event เวลา User พิมพ์ตัวเลข
        $('#qty_per_unit, #amount').on('input', function() {
            calculateTotal();
        });
    });
</script>
@endsection