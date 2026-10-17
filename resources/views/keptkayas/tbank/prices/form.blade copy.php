@php
    // Prepare initial data for the form.
    // This is for handling old input and existing data in edit mode.
    $formTiers = old('units_data', $price->unitsData ?? [
        [
            'kp_units_idfk' => $price->kp_units_idfk ?? '',
            'price_from_dealer' => $price->price_from_dealer ?? 0,
            'price_for_member' => $price->price_for_member ?? 0,
            'point' => $price->point ?? 0
        ]
    ]);
    
    // Calculate end date as 1 year from now
    $oneYearFromNow = \Carbon\Carbon::now()->addYear()->format('Y-m-d');
@endphp

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="kp_items_idfk" class="form-label">รายการขยะ:</label>
        <select id="kp_items_idfk" name="kp_items_idfk" class="form-select @error('kp_items_idfk') is-invalid @enderror" required>
            <option value="">เลือกรายการขยะ</option>
            @foreach ($items as $item)
                <option value="{{ $item->id }}" {{ old('kp_items_idfk', $price->kp_items_idfk ?? '') == $item->id ? 'selected' : '' }}>
                    {{ $item->kp_itemsname }}
                </option>
            @endforeach
        </select>
        @error('kp_items_idfk')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="effective_date" class="form-label">วันที่เริ่มมีผล:</label>
        <input type="date" id="effective_date" name="effective_date" class="form-control @error('effective_date') is-invalid @enderror" value="{{ old('effective_date', $price->effective_date ? $price->effective_date->format('Y-m-d') : date('Y-m-d')) }}" required>
        @error('effective_date')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="end_date" class="form-label">วันที่สิ้นสุด:</label>
        <input type="date" id="end_date" name="end_date" class="form-control @error('end_date') is-invalid @enderror" value="{{ old('end_date', $price->end_date ? $price->end_date->format('Y-m-d') : $oneYearFromNow) }}">
        @error('end_date')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        {{-- @dd($recorders) --}}
        <label for="recorder_id" class="form-label">ผู้บันทึก:</label>
        <select id="recorder_id" name="recorder_id" class="form-select @error('recorder_id') is-invalid @enderror">
            <option value="">เลือกผู้บันทึก</option>
            @foreach ($recorders as $recorder)
                <option value="{{ $recorder->user_id }}" {{ old('recorder_id', $price->recorder_id ?? Auth::id()) == $recorder->user_id ? 'selected' : '' }}>
                    {{ $recorder->user->firstname }} {{ $recorder->user->lastname }}
                </option>
                {{-- <option value="{{ $recorder->user_id }}" {{ old('recorder_id', $price->recorder_id ?? '') == $recorder->user_id ? 'selected' : '' }}>
                    {{ $recorder->user->firstname }} {{ $recorder->user->lastname }}
                </option> --}}
            @endforeach
        </select>
        @error('recorder_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<hr>
<h5 class="mb-3">กำหนดราคาและคะแนน</h5>
<div id="units-container">
    @forelse(old('units_data', $formTiers) as $index => $unitData)
        <div class="row g-3 align-items-end unit-item mb-3">
            <div class="col-md-3">
                <label class="form-label">หน่วยนับ:</label>
                <select name="units_data[{{$index}}][kp_units_idfk]" class="form-select @error('units_data.'.$index.'.kp_units_idfk') is-invalid @enderror" required>
                    <option value="">เลือกหน่วยนับ</option>
                    @foreach ($units as $unit)
                        <option value="{{ $unit->id }}" {{ old("units_data.{$index}.kp_units_idfk", $unitData['kp_units_idfk'] ?? '') == $unit->id ? 'selected' : '' }}>
                            {{ $unit->unitname }}
                        </option>
                    @endforeach
                </select>
                @error('units_data.'.$index.'.kp_units_idfk')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-3">
                <label class="form-label">ราคาจ่ายให้สมาชิก:</label>
                <input type="number" step="0.01" name="units_data[{{$index}}][price_for_member]" class="form-control @error('units_data.'.$index.'.price_for_member') is-invalid @enderror" value="{{ old("units_data.{$index}.price_for_member", $unitData['price_for_member'] ?? 0) }}" required min="0">
                @error('units_data.'.$index.'.price_for_member')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-3">
                <label class="form-label">ราคาจากร้านรับซื้อ:</label>
                <input type="number" step="0.01" name="units_data[{{$index}}][price_from_dealer]" class="form-control @error('units_data.'.$index.'.price_from_dealer') is-invalid @enderror" value="{{ old("units_data.{$index}.price_from_dealer", $unitData['price_from_dealer'] ?? 0) }}" required min="0">
                @error('units_data.'.$index.'.price_from_dealer')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-2">
                <label class="form-label">คะแนน:</label>
                <input type="number" name="units_data[{{$index}}][point]" class="form-control @error('units_data.'.$index.'.point') is-invalid @enderror" value="{{ old("units_data.{$index}.point", $unitData['point'] ?? 0) }}" min="0">
                @error('units_data.'.$index.'.point')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-1">

                <button type="button" class="btn btn-danger btn-sm remove-unit-btn"><i class="fa fa-trash"></i></button>
            </div>
        </div>
    @empty
        <div class="row g-3 align-items-end unit-item mb-3">
            <div class="col-md-3">
                <label class="form-label">หน่วยนับ:</label>
                <select name="units_data[0][kp_units_idfk]" class="form-select" required>
                    <option value="">เลือกหน่วยนับ</option>
                    @foreach ($units as $unit)
                        <option value="{{ $unit->id }}">{{ $unit->unitname }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">ราคาจ่ายให้สมาชิก:</label>
                <input type="number" step="0.01" name="units_data[0][price_for_member]" class="form-control" value="0" required min="0">
            </div>
            <div class="col-md-3">
                <label class="form-label">ราคาจากร้านรับซื้อ:</label>
                <input type="number" step="0.01" name="units_data[0][price_from_dealer]" class="form-control" value="0" required min="0">
            </div>
            <div class="col-md-2">
                <label class="form-label">คะแนน:</label>
                <input type="number" name="units_data[0][point]" class="form-control" value="0" min="0">
            </div>
            <div class="col-md-1 d-flex align-items-end">
                <button type="button" class="btn btn-danger btn-sm remove-unit-btn" disabled><i class="fa fa-trash"></i></button>
            </div>
        </div>
    @endforelse
</div>
<button type="button" class="btn btn-outline-primary btn-sm mt-2" id="add-unit-btn"><i class="fa fa-plus-circle me-1"></i> เพิ่มหน่วยนับ</button>

<div class="form-group mb-3 mt-4">
    <label for="comment" class="form-label">หมายเหตุ:</label>
    <textarea id="comment" name="comment" class="form-control @error('comment') is-invalid @enderror">{{ old('comment', $price->comment ?? '') }}</textarea>
    @error('comment')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="form-check mb-3">
    <input type="checkbox" id="is_active" name="is_active" class="form-check-input" {{ old('is_active', $price->is_active ?? true) ? 'checked' : '' }}>
    <label for="is_active" class="form-check-label">ใช้งานอยู่</label>
</div>

{{-- JavaScript for dynamic form fields --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const addUnitBtn = document.getElementById('add-unit-btn');
        const unitsContainer = document.getElementById('units-container');
        let unitIndex = unitsContainer.querySelectorAll('.unit-item').length;

        function addUnitRow() {
            const newRow = document.createElement('div');
            newRow.classList.add('row', 'g-3', 'align-items-end', 'unit-item', 'mb-3');
            newRow.innerHTML = `
                <div class="col-md-3">
                    <label class="form-label">หน่วยนับ:</label>
                    <select name="units_data[${unitIndex}][kp_units_idfk]" class="form-select" required>
                        <option value="">เลือกหน่วยนับ</option>
                        @foreach ($units as $unit)
                            <option value="{{ $unit->id }}">{{ $unit->unitname }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">ราคาจ่ายให้สมาชิก:</label>
                    <input type="number" step="0.01" name="units_data[${unitIndex}][price_for_member]" class="form-control" value="0" required min="0">
                </div>
                <div class="col-md-3">
                    <label class="form-label">ราคาจากร้านรับซื้อ:</label>
                    <input type="number" step="0.01" name="units_data[${unitIndex}][price_from_dealer]" class="form-control" value="0" required min="0">
                </div>
                <div class="col-md-2">
                    <label class="form-label">คะแนน:</label>
                    <input type="number" name="units_data[${unitIndex}][point]" class="form-control" value="0" min="0">
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="button" class="btn btn-danger btn-sm remove-unit-btn"><i class="fa fa-trash"></i></button>
                </div>
            `;
            unitsContainer.appendChild(newRow);
            unitIndex++;
        }

        function handleRemoveBtn() {
            unitsContainer.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-unit-btn') || e.target.closest('.remove-unit-btn')) {
                    const rowToRemove = e.target.closest('.unit-item');
                    if (unitsContainer.querySelectorAll('.unit-item').length > 1) {
                        rowToRemove.remove();
                    } else {
                        alert('ต้องมีหน่วยนับอย่างน้อยหนึ่งรายการ');
                    }
                }
            });
        }

        addUnitBtn.addEventListener('click', addUnitRow);
        handleRemoveBtn();

        // Update the form fields when kp_items_idfk changes
        // This is a placeholder for a more complex feature.
        const kpItemsIdfkSelect = document.getElementById('kp_items_idfk');
        kpItemsIdfkSelect.addEventListener('change', function() {
            // Here you would fetch existing prices and units for the selected item
            // and populate the form.
            // This is a complex feature that would require an AJAX endpoint.
        });

    });
</script>
