@extends('layouts.keptkaya')
@section('nav-header', 'ขยะรีไซเคิล')
@section('nav-current', 'สร้างข้อมูลขยะรีไซเคิล')
@section('page-topic', 'สร้างข้อมูลขยะรีไซเคิล')

@section('content')
  {{-- ... ส่วน Import/Export เดิม ... --}}
  <div class="card card-info">
    <form action="{{ route('keptkayas.tbank.items.store') }}" class="form-horizontal" method="post"
      enctype="multipart/form-data">
      @csrf
      <div class="card-body" id="item-fields-container">

        {{-- ======================================================= --}}
        {{-- ส่วนนี้คือชุดฟอร์มที่เราจะทำซ้ำ (ITEM TEMPLATE) --}}
        {{-- **สำคัญ:** ใช้ name ในรูปแบบ Array เช่น 'items[0][kp_itemsname]' --}}
        {{-- ======================================================= --}}
        <div class="item-form-group border p-3 mb-1 bg-light rounded" data-index="0">

          <h5 class="mb-1">รายการที่ 1</h5>
          <div class="row">
            <div class="form-group  col-3">
              <label class="col-form-label">ชื่อขยะรีไซเคิล</label>
              <div class="">
                <input type="text" class="form-control" name="items[0][kp_itemsname]" required>
              </div>
            </div>

            <div class="form-group  col-2">
              <label class="col-form-label">ประเภทขยะรีไซเคิล</label>
              <div class="">
                <select name="items[0][kp_items_group_idfk]" class="form-control" required>
                  <option value="">เลือก..</option>
                  @foreach ($kp_items_groups as $kp_items_group)
                    <option value="{{ $kp_items_group->id }}">{{ $kp_items_group->kp_items_groupname }}</option>
                  @endforeach
                </select>
              </div>
            </div>

            <div class="form-group  col-2">
              <label class="col-form-label">รหัส</label>
              <div class="">
                <input type="text" class="form-control" name="items[0][kp_itemscode]">
              </div>
            </div>

            <div class="form-group  col-3">
              <label class="col-form-label">รูปภาพ</label>
              {{-- **หมายเหตุ:** การจัดการหลายไฟล์ในฟอร์มเดียวที่ใช้ Array Notation สำหรับ 'name' ต้องระวังในการประมวลผล
              --}}
              <div class="">
                <input type="file" class="form-control" name="images[0]">
              </div>
            </div>
            <div class="form-group  col-2">
              <label class="col-form-label">&nbsp;</label>
              <div class="">
                <button type="button" class="btn btn-danger btn-sm  remove-item-btn"
                  style="display: none;">ลบรายการนี้</button>
              </div>
            </div>
          </div>
        </div>
        {{-- END ITEM TEMPLATE --}}

      </div>
      <div class="card-footer d-flex justify-content-between">
        <button type="button" id="add-item-btn" class="btn btn-primary">➕ เพิ่มรายการขยะ</button>
        <button type="submit" class="btn btn-info">บันทึกทั้งหมด</button>
      </div>
    </form>
  </div>


@endsection
@section('script')
  {{-- ======================================================= --}}
  {{-- ต้องเพิ่ม JavaScript ในส่วนนี้หรือในไฟล์ภายนอก --}}
  {{-- ======================================================= --}}
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const container = document.getElementById('item-fields-container');
      const addItemBtn = document.getElementById('add-item-btn');
      // เก็บ HTML ของชุดฟอร์มแรกไว้เป็น template
      const template = container.querySelector('.item-form-group').outerHTML;
      let itemIndex = 1; // เริ่มที่ 1 เพราะ 0 ใช้กับรายการแรกแล้ว

      // แสดงปุ่มลบสำหรับรายการแรกเมื่อโหลดหน้า
      container.querySelector('.item-form-group .remove-item-btn').style.display = 'block';

      // ฟังก์ชันสำหรับปรับชื่อฟิลด์ให้ตรงกับ index
      function updateFieldNames(newElement, index) {
    newElement.setAttribute('data-index', index);
    newElement.querySelector('h5').textContent = 'รายการที่ ' + (index + 1);

    newElement.querySelectorAll('input, select').forEach(field => {
        const oldName = field.getAttribute('name');
        if (oldName) {
            // ใช้ regex แทนที่ [0] ด้วย index ใหม่
            const newName = oldName.replace(/\[0\]/, '[' + index + ']');
            field.setAttribute('name', newName);
            
            // **ส่วนนี้จะครอบคลุมทั้ง items[...] และ images[...]**
            // ถ้าชื่อเดิมคือ images[0] จะเป็น images[index]
            // ถ้าชื่อเดิมคือ items[0][name] จะเป็น items[index][name]
        }
    });
    // ... ส่วนการล้างค่าเดิม
}

      addItemBtn.addEventListener('click', function () {
        // 1. สร้าง element ใหม่จาก template
        const newFormGroup = document.createElement('div');
        newFormGroup.innerHTML = template;

        // 2. ลบ template attributes ออก
        const clonedElement = newFormGroup.firstChild;

        // 3. ปรับชื่อฟิลด์และค่าเริ่มต้น
        updateFieldNames(clonedElement, itemIndex);

        // 4. ล้างค่าในฟิลด์ที่ถูกคัดลอกมา
        clonedElement.querySelectorAll('input, select').forEach(field => {
          if (field.type !== 'file') {
            field.value = '';
          } else {
            field.value = null; // ล้าง input type="file"
          }
        });

        // 5. แสดงปุ่มลบ
        clonedElement.querySelector('.remove-item-btn').style.display = 'block';

        // 6. เพิ่มเข้า container
        container.appendChild(clonedElement);
        itemIndex++;
      });

      // Event listener สำหรับการลบรายการ
      container.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-item-btn')) {
          const formGroup = e.target.closest('.item-form-group');
          // ตรวจสอบว่าไม่ใช่รายการสุดท้าย (เพื่อป้องกันฟอร์มว่าง)
          if (container.children.length > 1) {
            formGroup.remove();
          }
        }
      });
    });
  </script>
@endsection
{{-- @section('content')
<div class="row mb-3">
  <div class="col-md-6">
    <a href="{{ route('keptkayas.tbank.items.export') }}" class="btn btn-success">Export Items to Excel</a>
  </div>
  <div class="col-md-6">
    <form action="{{ route('keptkayas.tbank.items.import') }}" method="POST" enctype="multipart/form-data"
      class="d-flex">
      @csrf
      <input type="file" name="file" class="form-control me-2" required>
      <button type="submit" class="btn btn-primary">Import Items from Excel</button>
    </form>
  </div>
</div>
<div class="card card-info">

  <!-- form start -->
  <form action="{{ route('keptkayas.tbank.items.store') }}" class="form-horizontal" method="post"
    enctype="multipart/form-data">
    @csrf
    <div class="card-body">

      <div class="form-group row">
        <label for="inputEmail3" class="col-sm-2 col-form-label">ชื่อขยะรีไซเคิล</label>
        <div class="col-sm-10">
          <input type="text" class="form-control" name="kp_itemsname">
        </div>
      </div>

      <div class="form-group row">
        <label for="inputEmail3" class="col-sm-2 col-form-label">ประเภทขยะรีไซเคิล</label>
        <div class="col-sm-10">
          <select name="kp_items_group_idfk" id="" class="form-control">
            <option>เลือก..</option>
            @foreach ($kp_items_groups as $kp_items_group)
            <option value="{{ $kp_items_group->id }}">{{ $kp_items_group->kp_items_groupname }}</option>
            @endforeach
          </select>
        </div>
      </div>
      <div class="form-group row">
        <label for="inputEmail3" class="col-sm-2 col-form-label">รหัส</label>
        <div class="col-sm-10">
          <input type="text" class="form-control" name="kp_itemscode">
        </div>
        {{-- <div class="form-group row">
          <label for="inputEmail3" class="col-sm-2 col-form-label">หน่วยนับ</label>
          <div class="col-sm-10">
            <select name="tbank_item_unit_idfk" id="" class="form-control">
              <option>เลือก..</option>
              @foreach ($tbank_item_units as $tbank_item_unit)
              <option value="{{ $tbank_item_unit->id }}">{{ $tbank_item_unit->unitname }}</option>
              @endforeach
            </select>
          </div>
        </div>

        <div class="form-group row">
          <label for="inputEmail3" class="col-sm-2 col-form-label">รหัส</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" name="kp_itemscode">
          </div>
        </div> --}

        <div class="form-group row">
          <label for="inputEmail3" class="col-sm-2 col-form-label">รูปภาพ</label>
          <div class="col-sm-10">
            <input type="file" class="form-control" name="image">
          </div>
        </div>


      </div>
      <!-- /.card-body -->
      <div class="card-footer">
        <button type="submit" class="btn btn-info float-right">บันทึก</button>
      </div>
      <!-- /.card-footer -->
  </form>
</div>
@endsection --}}