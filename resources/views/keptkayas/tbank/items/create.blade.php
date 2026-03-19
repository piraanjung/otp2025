@extends('layouts.keptkaya')
@section('nav-header', 'ขยะรีไซเคิล')
@section('nav-current', 'สร้างข้อมูลขยะรีไซเคิล')
@section('page-topic', 'สร้างข้อมูลขยะรีไซเคิล')

@section('content')

{{-- 1. ส่วนสำหรับ Import Excel --}}
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card card-outline card-success">
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
                                    <input type="file" name="file" class="custom-file-input" id="excelFile" accept=".xlsx, .xls, .csv" required>
                                    <label class="custom-file-label" for="excelFile">เลือกไฟล์ Excel...</label>
                                </div>
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-success"><i class="fas fa-upload"></i> Import</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <a href="{{ route('keptkayas.tbank.items.export') }}" class="btn btn-outline-primary">
                                <i class="fas fa-download"></i> ดาวน์โหลดเทมเพลต Excel
                            </a>
                            <small class="text-muted d-block mt-1">* กรุณาใช้ไฟล์ตามรูปแบบเทมเพลตเพื่อความถูกต้องของข้อมูล</small>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<hr>

{{-- 2. ส่วนสำหรับกรอกฟอร์มทีละหลายรายการ (Dynamic Form) --}}
<div class="card card-info">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-edit"></i> เพิ่มรายการขยะใหม่ (กรอกข้อมูลเอง)</h3>
    </div>
    <form action="{{ route('keptkayas.tbank.items.store') }}" class="form-horizontal" method="post" enctype="multipart/form-data">
      @csrf
      <div class="card-body" id="item-fields-container">

        {{-- ITEM TEMPLATE --}}
        <div class="item-form-group border p-3 mb-3 bg-light rounded" data-index="0">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <h5 class="mb-0 text-info font-weight-bold">รายการที่ 1</h5>
            <button type="button" class="btn btn-danger btn-sm remove-item-btn" style="display: none;">
                <i class="fas fa-trash"></i> ลบรายการนี้
            </button>
          </div>

          <div class="row">
            <div class="form-group col-md-4">
              <label class="col-form-label">ชื่อขยะรีไซเคิล <span class="text-danger">*</span></label>
              <input type="text" class="form-control" name="items[0][kp_itemsname]" placeholder="เช่น ขวดน้ำ PET" required>
            </div>

            <div class="form-group col-md-3">
              <label class="col-form-label">ประเภทขยะรีไซเคิล <span class="text-danger">*</span></label>
              <select name="items[0][kp_items_group_idfk]" class="form-control select2" required>
                <option value="">เลือกประเภท..</option>
                @foreach ($kp_items_groups as $kp_items_group)
                  <option value="{{ $kp_items_group->id }}">{{ $kp_items_group->kp_items_groupname }}</option>
                @endforeach
              </select>
            </div>

            <div class="form-group col-md-2">
              <label class="col-form-label">รหัสสินค้า</label>
              <input type="text" class="form-control" name="items[0][kp_itemscode]" placeholder="PLA-001">
            </div>

            <div class="form-group col-md-3">
              <label class="col-form-label">รูปภาพประกอบ</label>
              <input type="file" class="form-control-file border p-1 bg-white rounded" name="images[0]">
            </div>
          </div>
        </div>
        {{-- END ITEM TEMPLATE --}}

      </div>
      <div class="card-footer d-flex justify-content-between bg-white">
        <button type="button" id="add-item-btn" class="btn btn-outline-primary shadow-sm">
            <i class="fas fa-plus-circle"></i> ➕ เพิ่มรายการขยะอีก
        </button>
        <button type="submit" class="btn btn-info px-5 shadow">
            <i class="fas fa-save"></i> บันทึกข้อมูลทั้งหมด
        </button>
      </div>
    </form>
</div>
@endsection

@section('script')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // จัดการชื่อไฟล์ใน Custom File Input (Bootstrap 4)
        $(document).on('change', '.custom-file-input', function (e) {
            var fileName = e.target.files[0].name;
            $(this).next('.custom-file-label').html(fileName);
        });

        const container = document.getElementById('item-fields-container');
        const addItemBtn = document.getElementById('add-item-btn');
        let itemIndex = 1;

        // ดึงเฉพาะโครงสร้าง HTML ของรายการแรกมาเป็น Template
        const template = container.querySelector('.item-form-group').cloneNode(true);

        addItemBtn.addEventListener('click', function () {
            const newNode = template.cloneNode(true);

            // ปรับปรุง Index และข้อความ
            newNode.setAttribute('data-index', itemIndex);
            newNode.querySelector('h5').textContent = 'รายการที่ ' + (itemIndex + 1);

            // แสดงปุ่มลบ
            newNode.querySelector('.remove-item-btn').style.display = 'block';

            // ล้างค่าข้อมูลเดิม
            newNode.querySelectorAll('input, select').forEach(field => {
                // อัปเดตชื่อ Name Array
                let name = field.getAttribute('name');
                if(name) {
                    field.setAttribute('name', name.replace('[0]', '[' + itemIndex + ']'));
                }
                field.value = '';
            });

            container.appendChild(newNode);
            itemIndex++;
        });

        // ลบรายการ
        container.addEventListener('click', function (e) {
            if (e.target.closest('.remove-item-btn')) {
                const formGroups = container.querySelectorAll('.item-form-group');
                if (formGroups.length > 1) {
                    e.target.closest('.item-form-group').remove();
                    reIndexItems();
                }
            }
        });

        // ฟังก์ชันช่วยเรียงลำดับรายการใหม่เมื่อมีการลบ
        function reIndexItems() {
            const groups = container.querySelectorAll('.item-form-group');
            itemIndex = groups.length;
            groups.forEach((group, idx) => {
                group.setAttribute('data-index', idx);
                group.querySelector('h5').textContent = 'รายการที่ ' + (idx + 1);
                group.querySelectorAll('input, select').forEach(field => {
                    let name = field.getAttribute('name');
                    if(name) {
                        field.setAttribute('name', name.replace(/\[\d+\]/, '[' + idx + ']'));
                    }
                });
            });
        }
    });
</script>
@endsection
