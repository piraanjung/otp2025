{{-- partials/price_item_template.blade.php --}}
@php
    // คำนวณสถานะตลอดไปสำหรับ Item Block นี้
    $currentIsForeverActive = $itemData['is_forever_active'] ?? (($itemData['end_date'] ?? null) === null);
    $currentUnitIndex = 0; // ตัวนับ Index หน่วยนับเริ่มต้นสำหรับ Item Block นี้
@endphp

<div class="card card-body mb-4 item-block" data-index="{{ $itemIndex }}">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="mb-0">รายการขยะที่ <span class="item-index-label">{{ $itemIndex + 1 }}</span></h6>
        <button type="button" class="btn btn-danger btn-sm remove-item-btn"><i class="fa fa-times"></i> ลบรายการ</button>
    </div>
    
    <div class="row">
        {{-- รายการขยะ --}}
        <div class="col-md-4 mb-3">
            <label for="items_{{ $itemIndex }}_kp_items_idfk" class="form-label">รายการขยะ:</label>
            <select id="items_{{ $itemIndex }}_kp_items_idfk" name="items_data[{{ $itemIndex }}][kp_items_idfk]" class="form-select" required>
                <option value="">เลือกรายการขยะ</option>
                @foreach ($items as $item)
                    <option value="{{ $item->id }}" {{ ($itemData['kp_items_idfk'] ?? '') == $item->id ? 'selected' : '' }}>
                        {{ $item->kp_itemsname }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- วันที่เริ่มมีผล --}}
        <div class="col-md-4 mb-3">
            <label for="items_{{ $itemIndex }}_effective_date" class="form-label">วันที่เริ่มมีผล:</label>
            <input type="date" id="items_{{ $itemIndex }}_effective_date" name="items_data[{{ $itemIndex }}][effective_date]" class="form-control" value="{{ $itemData['effective_date'] ?? date('Y-m-d') }}" required>
        </div>

        {{-- วันที่สิ้นสุด + ตลอดไป Checkbox --}}
        <div class="col-md-4 mb-3">
            <label for="items_{{ $itemIndex }}_end_date" class="form-label">วันที่สิ้นสุด:</label>
            <div class="input-group">
                <input 
                    type="date" 
                    id="items_{{ $itemIndex }}_end_date" 
                    name="items_data[{{ $itemIndex }}][end_date]" 
                    class="form-control item-end-date" 
                    value="{{ $itemData['end_date'] ?? $oneYearFromNow }}"
                    {{ $currentIsForeverActive ? 'disabled' : '' }}
                >
                <div class="input-group-text p-0">
                    <div class="form-check form-check-inline m-2">
                        <input 
                            type="checkbox" 
                            id="items_{{ $itemIndex }}_is_forever_active" 
                            name="items_data[{{ $itemIndex }}][is_forever_active]" 
                            class="form-check-input is-forever-active-checkbox" 
                            value="1" 
                            {{ $currentIsForeverActive ? 'checked' : '' }}
                        >
                        <label class="form-check-label" for="items_{{ $itemIndex }}_is_forever_active">ตลอดไป</label>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <hr class="mt-0">
    <h6 class="mb-3">หน่วยนับและราคา</h6>
    <div class="units-container">
        {{-- หน่วยนับ (Price Tiers) --}}
        @foreach($itemData['units_data'] as $unitIndex => $unitData)
             <div class="row g-3 align-items-end unit-item mb-3" data-unit-index="{{ $unitIndex }}">
                <div class="col-md-3">
                    <label class="form-label">หน่วยนับ:</label>
                    <select name="items_data[{{ $itemIndex }}][units_data][{{ $unitIndex }}][kp_units_idfk]" class="form-select" required>
                        <option value="">เลือกหน่วยนับ</option>
                        @foreach ($units as $unit)
                            <option value="{{ $unit->id }}" {{ ($unitData['kp_units_idfk'] ?? '') == $unit->id ? 'selected' : '' }}>
                                {{ $unit->unitname }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">ราคาจ่ายให้สมาชิก:</label>
                    <input type="number" step="0.01" name="items_data[{{ $itemIndex }}][units_data][{{ $unitIndex }}][price_for_member]" class="form-control" value="{{ $unitData['price_for_member'] ?? 0 }}" required min="0">
                </div>
                <div class="col-md-3">
                    <label class="form-label">ราคาจากร้านรับซื้อ:</label>
                    <input type="number" step="0.01" name="items_data[{{ $itemIndex }}][units_data][{{ $unitIndex }}][price_from_dealer]" class="form-control" value="{{ $unitData['price_from_dealer'] ?? 0 }}" required min="0">
                </div>
                <div class="col-md-2">
                    <label class="form-label">คะแนน:</label>
                    <input type="number" name="items_data[{{ $itemIndex }}][units_data][{{ $unitIndex }}][point]" class="form-control" value="{{ $unitData['point'] ?? 0 }}" min="0">
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-danger btn-sm remove-unit-btn"><i class="fa fa-trash"></i></button>
                </div>
            </div>
            @php $currentUnitIndex++; @endphp
        @endforeach

        {{-- ปุ่มเพิ่มหน่วยนับสำหรับ Item Block นี้ --}}
        <button type="button" class="btn btn-outline-primary btn-sm mt-2 add-unit-btn-per-item"><i class="fa fa-plus-circle me-1"></i> เพิ่มหน่วยนับ</button>
    </div>
</div>