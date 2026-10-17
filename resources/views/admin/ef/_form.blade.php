{{-- ฟอร์ม Emission Factor ใช้ร่วมกันระหว่าง create / edit ($factor เป็น null ตอนสร้างใหม่) --}}
@php $factor = $factor ?? null; @endphp
<div class="row">
    <div class="col-12 mb-3">
        <label for="material_name" class="form-label">ชื่อวัสดุ (Material Name) <span class="text-danger">*</span></label>
        <input type="text" name="material_name" id="material_name"
            class="form-control @error('material_name') is-invalid @enderror"
            value="{{ old('material_name', $factor->material_name ?? '') }}" placeholder="เช่น พลาสติก PET" required>
        @error('material_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="unit" class="form-label">หน่วย (Unit)</label>
        <input type="text" name="unit" id="unit" class="form-control @error('unit') is-invalid @enderror"
            value="{{ old('unit', $factor->unit ?? 'kgCO2e/kg') }}" placeholder="เช่น kgCO2e/kg">
        @error('unit') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="ef_value" class="form-label">ค่า EF (ef_value) <span class="text-danger">*</span></label>
        <input type="number" step="0.0001" name="ef_value" id="ef_value"
            class="form-control font-weight-bold text-success @error('ef_value') is-invalid @enderror"
            value="{{ old('ef_value', $factor->ef_value ?? '') }}" placeholder="0.0000" required>
        @error('ef_value') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-12 mb-3">
        <label for="source" class="form-label">แหล่งที่มา (Source)</label>
        <input type="text" name="source" id="source" class="form-control"
            value="{{ old('source', $factor->source ?? '') }}" placeholder="เช่น TGO 2025">
    </div>

    <div class="col-12 mb-3">
        <label for="example" class="form-label">ตัวอย่างวัสดุ / หมายเหตุ (Example)</label>
        <textarea name="example" id="example" class="form-control" rows="3"
            placeholder="ระบุตัวอย่างขยะ...">{{ old('example', $factor->example ?? '') }}</textarea>
    </div>
</div>
