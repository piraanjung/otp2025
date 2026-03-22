@extends('layouts.keptkaya')
@section('nav-header', 'ขยะรีไซเคิล')
@section('nav-current', 'สร้างข้อมูลขยะรีไซเคิล')
@section('page-topic', 'สร้างข้อมูลขยะรีไซเคิล')

@section('content')

    {{-- 1. ส่วนสำหรับ Import Excel (คงเดิมไว้) --}}
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card card-outline card-success shadow-sm">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-file-import"></i> นำเข้าข้อมูลด้วย Excel (Bulk Import)</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('keptkayas.tbank.items.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <div class="custom-file">
                                        <input type="file" name="file" class="custom-file-input" id="excelFile"
                                            accept=".xlsx, .xls, .csv" required>
                                        <label class="custom-file-label" for="excelFile">เลือกไฟล์ Excel...</label>
                                    </div>
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-success"><i class="fas fa-upload"></i>
                                            Import</button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <a href="{{ route('keptkayas.tbank.items.export') }}" class="btn btn-outline-primary">
                                    <i class="fas fa-download"></i> ดาวน์โหลดเทมเพลต EXCEL
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <hr>

    {{-- 2. ส่วนสำหรับกรอกฟอร์มแบบ Dynamic --}}
    <div class="card card-info shadow">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-plus-circle"></i> เพิ่มรายการขยะใหม่ (กรอกข้อมูลเอง)</h3>
        </div>
        <form action="{{ route('keptkayas.tbank.items.store') }}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="card-body" id="item-fields-container">

                {{-- ITEM TEMPLATE --}}
                <div class="item-form-group border p-4 mb-4 bg-white rounded shadow-sm" data-index="0">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0 text-info font-weight-bold"><i class="fas fa-box"></i> รายการที่ 1</h5>
                        <button type="button" class="btn btn-outline-danger btn-sm remove-item-btn" style="display: none;">
                            <i class="fas fa-trash-alt"></i> ลบรายการนี้
                        </button>
                    </div>

                    <div class="row">
                        <!-- ชื่อขยะ -->
                        <div class="form-group col-md-4">
                            <label>ชื่อขยะรีไซเคิล <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="items[0][kp_itemsname]"
                                placeholder="เช่น ขวดน้ำ PET" required>
                        </div>

                        <!-- รหัสสินค้า -->
                        <div class="form-group col-md-2">
                            <label>รหัสสินค้า</label>
                            <input type="text" class="form-control" name="items[0][kp_itemscode]" placeholder="PLA-001">
                        </div>

                        <!-- ประเภทขยะ -->
                        <div class="form-group col-md-3">
                            <label>ประเภทกลุ่มขยะ <span class="text-danger">*</span></label>
                            <select name="items[0][kp_items_group_idfk]" class="form-control" required>
                                <option value="">เลือกประเภท..</option>
                                @foreach ($kp_items_groups as $group)
                                    <option value="{{ $group->id }}">{{ $group->kp_items_groupname }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- รูปภาพ -->
                        <div class="form-group col-md-3">
                            <label>รูปภาพประกอบ</label>
                            <input type="file" class="form-control-file border p-1 bg-light rounded" name="images[0]">
                        </div>

                        <!-- หน่วยนับธนาคาร -->
                        <div class="form-group col-md-3">
                            <label>หน่วย (ธนาคาร/รับซื้อ) <span class="text-danger">*</span></label>
                            <select name="items[0][unit_bank_idfk]" class="form-control" required>
                                <option value="">เลือกหน่วย..</option>
                                @if(isset($units))
                                    @foreach ($units as $unit)
                                        <option value="{{ $unit->id }}">{{ $unit->unitname }} ({{ $unit->unit_short_name }})</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <!-- หน่วยนับตู้ -->
                        <div class="form-group col-md-3">
                            <label>หน่วย (ตู้ Kiosk)</label>
                            <select name="items[0][unit_kiosk_idfk]" class="form-control">
                                <option value="">ไม่ใช้งานที่ตู้</option>
                                  @if(isset($units))
                                    @foreach ($units as $unit)
                                        <option value="{{ $unit->id }}">{{ $unit->unitname }} ({{ $unit->unit_short_name }})
                                        </option>
                                    @endforeach
                                 @endif
                            </select>
                        </div>

                        <!-- ค่า EF (Emission Factor) -->
                        <div class="form-group col-md-6">
                            <label class="text-success font-weight-bold"><i class="fas fa-leaf"></i> จับคู่ค่าคาร์บอน
                                (EF)</label>
                            <select name="items[0][ef_id_fk]" class="form-control select2-dynamic">
                                <option value="">-- ยังไม่ระบุค่า EF --</option>
                                @foreach ($emissionFactors as $ef)
                                    <option value="{{ $ef->id }}">{{ $ef->material_name }}
                                        [{{ number_format($ef->ef_value, 4) }}]</option>
                                @endforeach
                            </select>
                            <small class="text-muted">ใช้สำหรับคำนวณการลดก๊าซเรือนกระจก</small>
                        </div>
                    </div>
                </div>
                {{-- END ITEM TEMPLATE --}}

            </div>
            <div class="card-footer d-flex justify-content-between bg-light">
                <button type="button" id="add-item-btn" class="btn btn-primary shadow-sm">
                    <i class="fas fa-plus-circle"></i> เพิ่มรายการขยะอีกรายการ
                </button>
                <button type="submit" class="btn btn-info px-5 shadow">
                    <i class="fas fa-save"></i> บันทึกข้อมูลทั้งหมดเข้าสู่ระบบ
                </button>
            </div>
        </form>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function () {
            // ฟังก์ชันตั้งค่า Select2 สำหรับตัวที่เพิ่มใหม่
            function initSelect2(element) {
                // ถ้าคุณใช้ Select2 library ให้ปลดคอมเมนต์บรรทัดข้างล่าง
                // $(element).find('.select2-dynamic').select2({ theme: 'bootstrap4', width: '100%' });
            }

            const container = document.getElementById('item-fields-container');
            const addItemBtn = document.getElementById('add-item-btn');
            let itemIndex = 1;

            // Clone Template เก็บไว้
            const template = container.querySelector('.item-form-group').cloneNode(true);

            addItemBtn.addEventListener('click', function () {
                const newNode = template.cloneNode(true);

                newNode.setAttribute('data-index', itemIndex);
                newNode.querySelector('h5').innerHTML = `<i class="fas fa-box"></i> รายการที่ ${itemIndex + 1}`;
                newNode.querySelector('.remove-item-btn').style.display = 'block';

                // ล้างค่าและเปลี่ยนชื่อ Name Array
                newNode.querySelectorAll('input, select, textarea').forEach(field => {
                    let name = field.getAttribute('name');
                    if (name) {
                        // เปลี่ยนชื่อจาก [0] เป็น [index ปัจจุบัน]
                        field.setAttribute('name', name.replace(/\[\d+\]/, '[' + itemIndex + ']'));
                    }
                    if (field.tagName === 'SELECT') {
                        field.selectedIndex = 0; // รีเซ็ต dropdown
                    } else {
                        field.value = ''; // ล้างค่า input
                    }
                });

                container.appendChild(newNode);
                initSelect2(newNode); // เรียกใช้ถ้ามี select2
                itemIndex++;
            });

            // ลบรายการ
            container.addEventListener('click', function (e) {
                if (e.target.closest('.remove-item-btn')) {
                    const group = e.target.closest('.item-form-group');
                    group.remove();
                    reIndexItems();
                }
            });

            function reIndexItems() {
                const groups = container.querySelectorAll('.item-form-group');
                itemIndex = groups.length;
                groups.forEach((group, idx) => {
                    group.setAttribute('data-index', idx);
                    group.querySelector('h5').innerHTML = `<i class="fas fa-box"></i> รายการที่ ${idx + 1}`;
                    group.querySelectorAll('input, select').forEach(field => {
                        let name = field.getAttribute('name');
                        if (name) {
                            field.setAttribute('name', name.replace(/\[\d+\]/g, '[' + idx + ']'));
                        }
                    });
                });
            }
        });
    </script>
@endsection
