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
                        <!-- เลขที่ล็อต -->
                        <div class="col-md-4">
                            <div class="form-floating">
                                <input type="text" class="form-control" name="lot_number" placeholder="Lot No.">
                                <label>Lot Number / Batch No. (ถ้ามี)</label>
                            </div>
                        </div>

                        <!-- วันหมดอายุ -->
                        <div class="col-md-4">
                            <div class="form-floating">
                                <input type="date" class="form-control" name="expire_date">
                                <label>วันหมดอายุ (ถ้ามี)</label>
                            </div>
                        </div>

                        <!-- เพิ่มใหม่: เลขที่ใบส่งของ / ใบกำกับภาษี -->
                        <div class="col-md-4">
                            <div class="form-floating">
                                <input type="text" class="form-control" name="reference_doc"
                                    placeholder="เช่น INV-2026-001">
                                <label>เลขที่ใบส่งของ / บิล / PO (ถ้ามี)</label>
                            </div>
                        </div>

                        <!-- เพิ่มใหม่: ร้านค้า / ผู้จำหน่าย (Supplier) -->
                        <div class="col-md-6">
                            <div class="form-floating input-group">
                                <select name="supplier_id_fk" class="form-select" required>
                                    <option value="">-- เลือกผู้จำหน่าย / ร้านค้า --</option>
                                    @foreach($suppliers as $sup)
                                        <option value="{{ $sup->id }}">{{ $sup->name }}</option>
                                    @endforeach
                                </select>
                                <a href="{{ route('admin.suppliers.index') }}" target="_blank"
                                    class="btn btn-outline-secondary" title="เพิ่มร้านค้าใหม่">
                                    <i class="material-icons-round align-middle">add</i>
                                </a>
                                <label class="form-label text-secondary">ร้านค้า / ผู้จำหน่าย (Supplier)</label>

                            </div>
                        </div>

                        <!-- ผู้รับของ (ดึงชื่อคน Login อัตโนมัติ หรือให้พิมพ์แก้ได้) -->
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" name="receiver_name"
                                    value="{{ Auth::user()->firstname." ".Auth::user()->lastname }}" readonly>
                                <label>ผู้รับพัสดุ / ผู้ตรวจรับ</label>
                            </div>
                        </div>

                        <div class="col-12">
                            <hr>
                        </div>


                        <!-- จำนวนที่รับมา -->
                        <div class="col-md-6">
                            

                            <label class="form-label fw-bold text-secondary">จำนวนบรรจุภัณฑ์ที่รับเข้ามา</label>
                            <div class="input-group">
                                <input type="number" step="0.01" class="form-control" id="receive_amount"
                                    name="receive_amount" placeholder="เช่น 10" value="1" required min="0.01">

                                <!-- ใส่ id="package_unit_select" เพื่อให้ JS มาดึงชื่อหน่วยไปใช้ -->
                                <select name="package_unit_id" id="package_unit_select" class="form-select"
                                    style="max-width: 140px;" required>
                                    <option value="">-- เลือกหน่วย --</option>
                                    @foreach($units as $unit)
                                        <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- จำนวนย่อยต่อ 1 บรรจุภัณฑ์ -->
                        <div class="col-md-2">
                            <label class="form-label fw-bold text-secondary">&nbsp;</label>
                            <div class="form-label fw-bold fs-4 text-secondary text-center unit_per_pcs_text">มี</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-secondary">&nbsp;</label>
                            <div class="input-group">
                                <input type="number" step="0.01" class="form-control" id="conversion_rate"
                                    name="conversion_rate" placeholder="เช่น 12" value="1" required min="0.01">
                                <span class="input-group-text bg-light fw-bold text-primary">{{ $item->unit }}</span>
                            </div>
                        </div>

                        <!-- ราคาต่อ 1 บรรจุภัณฑ์ใหญ่ -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-secondary">ราคาซื้อต่อ 1 บรรจุภัณฑ์</label>
                            <div class="input-group">
                                <input type="number" step="0.01" class="form-control" id="unit_price" name="unit_price"
                                    placeholder="เช่น 1200" value="0" required min="0">
                                <!-- เปลี่ยนข้อความตรงนี้ให้สะท้อนตามหน่วยที่เลือก (ผ่าน JS) -->
                                <span class="input-group-text bg-light" id="unit_label_text">บาท / หน่วย</span>
                            </div>
                            <div class="form-text">ราคาตามบิลต่อหน่วยใหญ่</div>
                        </div>

                        <!-- แสดงราคารวมสุทธิ (คำนวณอัตโนมัติ) -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-secondary">ราคารวมสุทธิทั้งหมด</label>
                            <div class="input-group">
                                <input type="text" class="form-control bg-light fw-bold text-success"
                                    id="total_price_display" value="0.00" readonly>
                                <span class="input-group-text bg-light">บาท</span>
                            </div>
                            <div class="form-text">คำนวณจาก (จำนวนบรรจุภัณฑ์ × ราคาต่อหน่วย)</div>
                        </div>

                    </div>

                    <div class="col-12 mt-3">
                        <label class="form-label fw-bold">พื้นที่จัดเก็บ (Location)</label>
                        <select name="location_id_fk" class="form-select" required>
                            <option value="">-- เลือกพื้นที่จัดเก็บของหน่วยงาน --</option>
                            @foreach($locations as $loc)
                                <option value="{{ $loc->id }}">{{ $loc->name }} @if($loc->description) ({{ $loc->description }})
                                @endif</option>
                            @endforeach
                        </select>
                        <div class="form-text">หากยังไม่มีสถานที่ สามารถไปเพิ่มได้ที่เมนูจัดการพื้นที่จัดเก็บ</div>
                    </div>

                    <!-- สรุปผลลัพธ์คำนวณ Realtime -->
                    <div class="col-12 mt-3">
                        <div class="alert alert-success d-flex align-items-center">
                            <i class="material-icons-round me-2">check_circle</i>
                            <div>
                                สรุปยอดเข้าคลังสุทธิ:
                                <span id="total-result" class="fw-bold fs-4">1</span>
                                {{ $item->unit }}
                                <span class="text-muted small">(คำนวณจาก จำนวนรับ × อัตราส่วน)</span>
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
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            // ฟังก์ชันอัปเดตข้อความหน่วยที่ป้ายราคา
            function updateUnitLabel() {
                let selectedOption = $('#package_unit_select option:selected');
                let unitName = selectedOption.val() ? selectedOption.text() : 'หน่วย';
                $('#unit_label_text').text('บาท / ' + unitName);
                $('.unit_per_pcs_text').text('1 ' + unitName +' มี');
                
            }

            // ฟังก์ชันคำนวณสต็อกและราคา
            function calculateStock() {
                let receive = parseFloat($('#receive_amount').val()) || 0;
                let rate = parseFloat($('#conversion_rate').val()) || 0;
                let unitPrice = parseFloat($('#unit_price').val()) || 0;

                // คำนวณยอดหน่วยย่อยสุทธิ
                let totalQty = receive * rate;
                $('#total-result').text(totalQty.toLocaleString());

                // คำนวณราคารวมสุทธิ
                let totalPrice = receive * unitPrice;
                $('#total_price_display').val(totalPrice.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
            }

            // เมื่อเปลี่ยน Dropdown หน่วยใหญ่ ให้เปลี่ยนป้ายข้อความทันที
            $('#package_unit_select').on('change', function () {
                updateUnitLabel();
            });

            // เมื่อมีการกรอกข้อมูลตัวเลข ให้คำนวณใหม่
            $('#receive_amount, #conversion_rate, #unit_price').on('input', function () {
                calculateStock();
            });

            // เรียกทำงานครั้งแรกเผื่อกรณีโหลดหน้าแล้วมีค่าค้างอยู่
            updateUnitLabel();
        });
    </script>
@endsection