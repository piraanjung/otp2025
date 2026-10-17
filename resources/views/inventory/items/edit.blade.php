@extends('inventory.inv_master')

@section('title', 'แก้ไขทะเบียนพัสดุ')
@section('header_title', 'แก้ไขทะเบียนพัสดุ (Master Data)')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">

            <form action="{{ route('inventory.items.update', $item->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="card p-4 mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold text-primary mb-0">ข้อมูลทั่วไป (General Info)</h5>
                        <span class="text-muted small">รหัสระบบ ID: {{ $item->id }}</span>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-12 text-center mb-3">
                            <div class="position-relative d-inline-block">
                                {{-- ตรวจสอบว่ามีรูปภาพเดิมไหม ถ้ามีแสดงรูปเดิม ถ้าไม่มีแสดงรูป Placeholder --}}
                                <img id="preview-image"
                                    src="{{ $item->image_path ? asset('storage/' . $item->image_path) : 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxNTAiIGhlaWdodD0iMTUwIiB2aWV3Qm94PSIwIDAgMTUwIDE1MCI+PHJlY3Qgd2lkdGg9IjE1MCIgaGVpZ2h0PSIxNTAiIGZpbGw9IiNlZWVlZWUiLz48dGV4dCB4PSI1MCUiIHk9IjUwJSIgZG9taW5hbnQtYmFzZWxpbmU9Im1pZGRsZSIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZm9udC1mYW1pbHk9InNhbnMtc2VyaWYiIGZvbnQtc2l6ZT0iMjAiIGZpbGw9IiM5OTk5OTkiPlVwbG9hZDwvdGV4dD48L3N2Zz4=' }}"
                                    class="rounded-circle shadow-sm border" width="120" height="120"
                                    style="object-fit: cover;">
                                <label for="image"
                                    class="position-absolute bottom-0 end-0 bg-primary text-white rounded-circle p-2 shadow"
                                    style="cursor: pointer;">
                                    <i class="material-icons-round fs-6">camera_alt</i>
                                </label>
                                <input type="file" name="image" id="image" class="d-none" accept="image/*"
                                    onchange="previewFile()">
                            </div>
                        </div>

                        <div class="col-md-8">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="name" name="name" placeholder="ชื่อพัสดุ"
                                    value="{{ old('name', $item->name) }}" required>
                                <label for="name">ชื่อพัสดุ (Item Name) *</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="code" name="code" placeholder="รหัส/Barcode"
                                    value="{{ old('code', $item->code) }}">
                                <label for="code">รหัส (SKU/Code)</label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-floating">
                                <select class="form-select" id="category" name="inv_category_id_fk">
                                    <option value="">-- เลือกหมวดหมู่ --</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ old('inv_category_id_fk', $item->inv_category_id_fk) == $cat->id ? 'selected' : '' }}>
                                            {{ $cat->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <label for="category">หมวดหมู่</label>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-floating">
                                <select class="form-select" id="unit" name="unit" required>
                                    <option value="">-- เลือก --</option>
                                    @foreach($units as $u)
                                        <option value="{{ $u->name }}" {{ old('unit', $item->unit) == $u->name ? 'selected' : '' }}>
                                            {{ $u->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <label for="unit">หน่วยนับหลัก *</label>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-floating">
                                <input type="number" class="form-control" id="min_stock" name="min_stock" placeholder="0"
                                    min="0" value="{{ old('min_stock', $item->min_stock) }}">
                                <label for="min_stock">แจ้งเตือนเมื่อต่ำกว่า</label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-floating">
                                <textarea name="inv_description" class="form-control" id="inv_description" style="height: 100px;">{{ old('inv_description', $item->inv_description) }}</textarea>
                                <label for="inv_description">คำอธิบายเพิ่มเติม</label>
                            </div>
                        </div>
                        <div class="col-md-12 mt-3">
                            <div class="d-flex gap-4 p-3 bg-light rounded border">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="return_required"
                                        name="return_required" {{ old('return_required', $item->return_required) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="return_required">ต้องส่งคืน (ยืม-คืน)</label>
                                </div>

                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="is_chemical" name="is_chemical"
                                        {{ old('is_chemical', $item->is_chemical) ? 'checked' : '' }}>
                                    <label class="form-check-label text-danger fw-bold"
                                        for="is_chemical">เป็นสารเคมีอันตราย</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ส่วนข้อมูลสารเคมี (ซ่อน/แสดง ตาม Checkbox) --}}
                <div class="card p-4 mb-4 border-warning" id="chemical-section" style="display: none;">
                    <h5 class="fw-bold text-warning mb-3">
                        <i class="material-icons-round align-middle">science</i> ข้อมูลเฉพาะสารเคมี
                    </h5>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="form-floating">
                                <input type="text" class="form-control" name="cas_number" placeholder="CAS No."
                                    value="{{ old('cas_number', $item->cas_number) }}">
                                <label>CAS Number</label>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="form-floating">
                                <input type="url" class="form-control" name="msds_link" placeholder="https://..."
                                    value="{{ old('msds_link', $item->msds_link) }}">
                                <label>ลิงก์เอกสาร MSDS</label>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <hr>
                        <label class="fw-bold mb-2">ระบุความเป็นอันตราย</label>
                        <div class="row g-2">
                            @php
                                // ดึง ID ของ Hazard ที่เคยเลือกไว้แล้ว (รองรับทั้ง Relation แบบ Eloquent หรือ Array)
                                $selectedHazards = old('hazards', isset($item->hazards) ? $item->hazards->pluck('id')->toArray() : []);
                            @endphp
                            @foreach($hazards as $h)
                                <div class="col-md-3">
                                    <div class="form-check border rounded p-2 d-flex align-items-center bg-white">
                                        <input class="form-check-input mt-0 me-2" type="checkbox" name="hazards[]"
                                            value="{{ $h->id }}" id="haz_{{ $h->id }}"
                                            {{ in_array($h->id, $selectedHazards) ? 'checked' : '' }}>
                                        <label class="form-check-label d-flex align-items-center w-100" for="haz_{{ $h->id }}"
                                            style="cursor: pointer;">
                                            @if($h->image_path)
                                                <img src="{{ asset('storage/' . $h->image_path) }}" width="25" class="me-2">
                                            @endif
                                            <small>{{ $h->name }}</small>
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="text-end mb-5">
                    <a href="{{ route('inventory.items.index') }}" class="btn btn-light text-muted me-2">ยกเลิก</a>
                    <button type="submit" class="btn btn-primary btn-material btn-lg px-5">
                        <i class="material-icons-round align-middle">save</i> บันทึกการแก้ไข
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            // --- ส่วนที่ 1: จัดการการแสดงผลส่วนสารเคมี ---
            const $chemicalCheckbox = $('#is_chemical');
            const $chemicalSection = $('#chemical-section');

            function toggleChemicalSection() {
                if ($chemicalCheckbox.is(':checked')) {
                    $chemicalSection.slideDown(); 
                } else {
                    $chemicalSection.slideUp();   
                }
            }

            // เรียกทำงานทันทีตอนโหลดหน้า (เช็คค่าเดิมว่าติ๊กสารเคมีไว้ไหม)
            toggleChemicalSection();

            // เรียกทำงานเมื่อมีการเปลี่ยนค่า Checkbox
            $chemicalCheckbox.on('change', function () {
                toggleChemicalSection();
            });

            // --- ส่วนที่ 2: ฟังก์ชัน Preview รูปภาพ ---
            window.previewFile = function () {
                const preview = document.querySelector('#preview-image');
                const file = document.querySelector('#image').files[0];
                const reader = new FileReader();

                reader.addEventListener("load", function () {
                    preview.src = reader.result;
                }, false);

                if (file) {
                    reader.readAsDataURL(file);
                }
            }
        });
    </script>
@endsection