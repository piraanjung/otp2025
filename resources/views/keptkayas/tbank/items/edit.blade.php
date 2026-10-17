@extends('layouts.keptkaya')

@section('page-topic', 'แก้ไขข้อมูลขยะรีไซเคิล')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-10 mx-auto">
            <div class="card card-outline card-info">
                <div class="card-header">
                    <h3 class="card-title text-bold">
                        <i class="fas fa-edit text-info"></i> แก้ไขรายการ: {{ $item->kp_itemsname }}
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('keptkayas.tbank.items.index') }}" class="btn btn-default btn-sm">
                            <i class="fas fa-arrow-left"></i> กลับหน้ารายการ
                        </a>
                    </div>
                </div>

                <!-- อย่าลืม enctype="multipart/form-data" สำหรับการอัปโหลดรูปภาพ -->
                <form action="{{ route('keptkayas.tbank.items.update', $item->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <h5><i class="icon fas fa-ban"></i> ข้อผิดพลาด!</h5>
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="row">
                            <!-- ส่วนรูปภาพ -->
                            <div class="col-md-4 text-center border-right">
                                <label>รูปภาพประกอบ</label>
                                <div class="mb-3">
                                    @if($item->image)
                                        <img src="{{ asset('keptkaya/items/' . $item->image) }}" id="img-preview" class="img-thumbnail" style="max-height: 200px; width: 100%; object-fit: cover;">
                                    @else
                                        <img src="{{ asset('images/no-image.png') }}" id="img-preview" class="img-thumbnail" style="max-height: 200px; width: 100%; object-fit: cover;">
                                    @endif
                                </div>
                                <div class="custom-file text-left">
                                    <input type="file" name="image" class="custom-file-input" id="imageInput" accept="image/*">
                                    <label class="custom-file-label" for="imageInput">เปลี่ยนรูปภาพ...</label>
                                </div>
                                <small class="text-muted mt-2 d-block text-left">* รองรับไฟล์ JPG, PNG (ไม่เกิน 2MB)</small>
                            </div>

                            <!-- ส่วนข้อมูลข้อความ -->
                            <div class="col-md-8">
                                <div class="row">
                                    <div class="form-group col-md-4">
                                        <label>รหัสสินค้า (Code) <span class="text-danger">*</span></label>
                                        <input type="text" name="kp_itemscode" class="form-control" value="{{ old('kp_itemscode', $item->kp_itemscode) }}" required>
                                    </div>
                                    <div class="form-group col-md-8">
                                        <label>ชื่อรายการขยะ <span class="text-danger">*</span></label>
                                        <input type="text" name="kp_itemsname" class="form-control" value="{{ old('kp_itemsname', $item->kp_itemsname) }}" required>
                                    </div>

                                    <!-- Dropdown หน่วยนับ -->
                                    <div class="form-group col-md-6">
                                        <label>หน่วยนับ (ธนาคาร/รับซื้อ) <span class="text-danger">*</span></label>
                                        <select name="unit_bank_idfk" class="form-control select2" required>
                                            <option value="">-- เลือกหน่วยนับ --</option>
                                            @foreach($units as $unit)
                                                <option value="{{ $unit->id }}" {{ $item->unit_bank_idfk == $unit->id ? 'selected' : '' }}>
                                                    {{ $unit->unit_name }} ({{ $unit->unit_short_name }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>หน่วยนับ (ตู้ Kiosk)</label>
                                        <select name="unit_kiosk_idfk" class="form-control select2">
                                            <option value="">-- ไม่แสดงที่ตู้ --</option>
                                            @foreach($units as $unit)
                                                <option value="{{ $unit->id }}" {{ $item->unit_kiosk_idfk == $unit->id ? 'selected' : '' }}>
                                                    {{ $unit->unit_name }} ({{ $unit->unit_short_name }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Dropdown ค่า Emission Factor -->
                                    <div class="form-group col-md-12">
                                        <label class="text-success"><i class="fas fa-leaf"></i> จับคู่ค่าคาร์บอน (Emission Factor)</label>
                                        <select name="ef_id_fk" class="form-control select2">
                                            <option value="">-- ยังไม่ระบุค่า EF (ไม่คำนวณคาร์บอน) --</option>
                                            @foreach($emissionFactors as $ef)
                                                <option value="{{ $ef->id }}" {{ $item->ef_id_fk == $ef->id ? 'selected' : '' }}>
                                                    {{ $ef->material_name }} [{{ number_format($ef->ef_value, 4) }} {{ $ef->unit }}]
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>สถานะ</label>
                                        <select name="status" class="form-control">
                                            <option value="active" {{ $item->status == 'active' ? 'selected' : '' }}>เปิดใช้งาน</option>
                                            <option value="inactive" {{ $item->status == 'inactive' ? 'selected' : '' }}>ปิดใช้งาน</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer text-right">
                        <button type="submit" class="btn btn-info px-5 text-bold">
                            <i class="fas fa-save"></i> บันทึกการแก้ไขข้อมูล
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    $(function () {
        // เริ่มต้นใช้งาน Select2 (ถ้ามี Library)
        $('.select2').select2({ theme: 'bootstrap4' });

        // Preview รูปภาพทันทีที่เลือก
        $("#imageInput").change(function() {
            readURL(this);
            // แสดงชื่อไฟล์ที่เลือกใน Label
            let fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').addClass("selected").html(fileName);
        });
    });

    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#img-preview').attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection
