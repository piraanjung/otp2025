<style>
    .tier-row {
        margin-bottom: 10px;
        padding: 15px;
        border: 1px solid #e3e6f0;
        background-color: #f8f9fc;
        border-radius: 5px;
        position: relative;
    }
    .btn-remove-tier {
        position: absolute;
        top: 10px;
        right: 10px;
    }
</style>

{{-- ส่วนจัดการ Meter Type และ Pricing Type --}}
<div class="row">
    <div class="col-12 col-md-6 mb-3">
        <label for="meter_type_id_fk" class="font-weight-bold">ประเภทมิเตอร์ <span class="text-danger">*</span></label>
        <select id="meter_type_id_fk" name="meter_type_id_fk" class="form-control" required>
            <option value="">-- เลือกประเภทมิเตอร์ --</option>
            @foreach ($meterTypes as $type)
                <option value="{{ $type->id }}" 
                    {{ old('meter_type_id_fk', $meterRateConfig->meter_type_id_fk ?? '') == $type->id ? 'selected' : '' }}>
                    {{ $type->meter_type_name }} ({{ $type->metersize }}")
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-12 col-md-6 mb-3">
        <label for="pricing_type_id" class="font-weight-bold">ประเภทการคำนวณราคา <span class="text-danger">*</span></label>
        <select id="pricing_type_id" name="pricing_type_id" class="form-control" required>
            <option value="">-- เลือกประเภท --</option>
            @foreach ($pricingTypes as $type)
                <option value="{{ $type->id }}" 
                    {{ old('pricing_type_id', $meterRateConfig->pricing_type_id ?? '') == $type->id ? 'selected' : '' }}>
                    {{ ucfirst($type->name) }}
                </option>
            @endforeach
        </select>
    </div>
</div>

{{-- ส่วนค่าบริการขั้นต่ำ --}}
<div class="row">
    <div class="col-12 col-md-6 mb-3">
        <label for="min_usage_charge" class="font-weight-bold">ค่าบริการขั้นต่ำ / ค่ารักษามิเตอร์ (บาท) <span class="text-danger">*</span></label>
        <input type="number" step="0.01" class="form-control" id="min_usage_charge" name="min_usage_charge"
            value="{{ old('min_usage_charge', $meterRateConfig->min_usage_charge ?? 0) }}" required>
    </div>
    <div class="col-12 col-md-6 mb-3">
        <label for="vat" class="font-weight-bold">VAT (%)</label>
        <input type="number" step="0.01" class="form-control" id="vat" name="vat"
            value="{{ old('vat', $meterRateConfig->vat ?? 7) }}">
    </div>
</div>

<hr>

{{-- Section: Fixed Rate --}}
<div id="fixed_rate_section" style="display: none;">
    <h5 class="text-primary">กำหนดราคาแบบคงที่ (Fixed Rate)</h5>
    <div class="form-group">
        <label for="fixed_rate_per_unit">อัตราค่าน้ำต่อหน่วย (บาท)</label>
        <input type="number" step="0.01" class="form-control" id="fixed_rate_per_unit" name="fixed_rate_per_unit"
            value="{{ old('fixed_rate_per_unit', $meterRateConfig->fixed_rate_per_unit ?? '') }}">
        <small class="text-muted">คิดราคาเดียวทุกหน่วยการใช้น้ำ</small>
    </div>
</div>

{{-- Section: Progressive Rate (Tiers) --}}
<div id="progressive_tiers_section" style="display: none;">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="text-primary m-0">กำหนดราคาแบบขั้นบันได (Progressive Rate)</h5>
        <button type="button" class="btn btn-info btn-sm" onclick="addTier()">
            <i class="fas fa-plus"></i> เพิ่มขั้นบันได (Add Tier)
        </button>
    </div>

    <div id="tiers_container">
        @php
            // รวม Logic ดึงข้อมูล: ถ้ามี Old Input (Submit ไม่ผ่าน) ให้ใช้ Old, ถ้าไม่มีให้ใช้ DB, ถ้าไม่มีเลยให้เป็น Array ว่าง
            $tiersData = [];
            if(old('tiers')) {
                $tiersData = old('tiers');
            } elseif(isset($meterRateConfig) && $meterRateConfig->Ratetiers && $meterRateConfig->Ratetiers->count() > 0) {
                $tiersData = $meterRateConfig->Ratetiers;
            }
        @endphp

        @foreach ($tiersData as $index => $tier)
            {{-- แปลงข้อมูลให้เป็น Array เสมอ เพื่อให้เรียกใช้ง่าย --}}
            @php
                $min = is_array($tier) ? ($tier['min_units'] ?? '') : $tier->min_units;
                $max = is_array($tier) ? ($tier['max_units'] ?? '') : $tier->max_units;
                $rate = is_array($tier) ? ($tier['rate_per_unit'] ?? '') : $tier->rate_per_unit;
                $comment = is_array($tier) ? ($tier['comment'] ?? '') : $tier->comment;
            @endphp

            <div class="tier-row">
                <button type="button" class="btn btn-danger btn-sm btn-remove-tier" onclick="removeTier(this)">
                    <i class="fas fa-times"></i>
                </button>
                <h6 class="text-secondary font-weight-bold">Tier <span class="tier-number">{{ $index + 1 }}</span></h6>
                
                <div class="row">
                    <div class="col-12 col-md-3">
                        <label>หน่วยเริ่มต้น (Min)</label>
                        <input type="number" class="form-control" name="tiers[{{ $index }}][min_units]" 
                               value="{{ $min }}" required min="0" placeholder="0">
                    </div>
                    <div class="col-12 col-md-3">
                        <label>ถึงหน่วยที่ (Max)</label>
                        <input type="number" class="form-control" name="tiers[{{ $index }}][max_units]" 
                               value="{{ $max }}" placeholder="ว่าง = ไม่จำกัด">
                    </div>
                    <div class="col-12 col-md-3">
                        <label>ราคา/หน่วย (บาท)</label>
                        <input type="number" step="0.01" class="form-control" name="tiers[{{ $index }}][rate_per_unit]" 
                               value="{{ $rate }}" required min="0">
                    </div>
                    <div class="col-12 col-md-3">
                        <label>หมายเหตุ</label>
                        <input type="text" class="form-control" name="tiers[{{ $index }}][comment]" 
                               value="{{ $comment }}">
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    
    @if(empty($tiersData))
        <div class="alert alert-light text-center border mt-2" id="no-tier-alert">
            <small class="text-muted">ยังไม่มีข้อมูลขั้นบันได กรุณากดปุ่ม "เพิ่มขั้นบันได"</small>
        </div>
    @endif
</div>

<hr>

{{-- วันที่และสถานะ --}}
<div class="row">
    <div class="col-12 col-md-4 mb-3">
        <label for="effective_date">วันที่เริ่มใช้งาน (Effective Date)</label>
        <input type="date" id="effective_date" name="effective_date" class="form-control"
            value="{{ old('effective_date', isset($meterRateConfig->effective_date) ? \Carbon\Carbon::parse($meterRateConfig->effective_date)->format('Y-m-d') : date('Y-m-d')) }}"
            required>
    </div>
    <div class="col-12 col-md-4 mb-3">
        <label for="end_date">วันที่สิ้นสุด (End Date)</label>
        <input type="date" id="end_date" name="end_date" class="form-control"
            value="{{ old('end_date', isset($meterRateConfig->end_date) ? \Carbon\Carbon::parse($meterRateConfig->end_date)->format('Y-m-d') : '') }}">
        <small class="text-muted">ปล่อยว่างหากไม่มีกำหนด</small>
    </div>
    <div class="col-12 col-md-4 mb-3">
        <label for="is_active">สถานะ (Status)</label>
        <select id="is_active" name="is_active" class="form-control">
            {{-- ✅ แก้ Error จุดนี้: ใช้ isset เช็คก่อน --}}
            <option value="1" {{ old('is_active', $meterRateConfig->is_active ?? 1) == 1 ? 'selected' : '' }}>Active (ใช้งาน)</option>
            <option value="0" {{ old('is_active', $meterRateConfig->is_active ?? 1) == 0 ? 'selected' : '' }}>Inactive (ไม่ใช้งาน)</option>
        </select>
    </div>
</div>

<div class="form-group">
    <label for="comment">หมายเหตุเพิ่มเติม</label>
    <textarea id="comment" name="comment" class="form-control" rows="3">{{ old('comment', $meterRateConfig->comment ?? '') }}</textarea>
</div>

{{-- Javascript --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const pricingTypeSelect = document.getElementById('pricing_type_id');
        const fixedRateSection = document.getElementById('fixed_rate_section');
        const progressiveTiersSection = document.getElementById('progressive_tiers_section');
        const fixedRateInput = document.getElementById('fixed_rate_per_unit');
        const tiersContainer = document.getElementById('tiers_container');
        const noTierAlert = document.getElementById('no-tier-alert');

        // Logic check ID ตาม PricingType ใน Database (1=Fixed, 2=Progressive)
        // ถ้า Database คุณ ID ต่างจากนี้ ให้แก้เลขตรงนี้นะครับ
        const TYPE_FIXED = '1'; 
        const TYPE_PROGRESSIVE = '2'; 

        function togglePricingFields() {
            const selectedType = pricingTypeSelect.value;
            
            // Reset required attributes first
            fixedRateInput.removeAttribute('required');
            const tierInputs = tiersContainer.querySelectorAll('input[required]');
            // Tier inputs required status is handled by their existence, mostly.

            if (selectedType == TYPE_FIXED) {
                fixedRateSection.style.display = 'block';
                progressiveTiersSection.style.display = 'none';
                fixedRateInput.setAttribute('required', 'required');
            } 
            else if (selectedType == TYPE_PROGRESSIVE) {
                fixedRateSection.style.display = 'none';
                progressiveTiersSection.style.display = 'block';
                
                // ถ้าไม่มี tier เลย ให้เพิ่มอัตโนมัติ 1 อัน
                if (tiersContainer.children.length === 0) {
                    addTier();
                }
            } 
            else {
                fixedRateSection.style.display = 'none';
                progressiveTiersSection.style.display = 'none';
            }
        }

        // ทำให้ function เป็น Global เพื่อเรียกผ่าน onclick ได้
        window.addTier = function() {
            if(noTierAlert) noTierAlert.style.display = 'none';
            
            // ใช้ Timestamp เป็น Index ชั่วคราว เพื่อไม่ให้ Key ซ้ำเวลาลบแล้วเพิ่มใหม่
            const index = Date.now(); 
            const tierCount = tiersContainer.children.length + 1;

            const html = `
            <div class="tier-row" id="tier-row-${index}">
                <button type="button" class="btn btn-danger btn-sm btn-remove-tier" onclick="removeTier(this)">
                    <i class="fas fa-times"></i>
                </button>
                <h6 class="text-secondary font-weight-bold">Tier <span class="tier-number">${tierCount}</span></h6>
                <div class="row">
                    <div class="col-12 col-md-3">
                        <label>หน่วยเริ่มต้น (Min)</label>
                        <input type="number" class="form-control" name="tiers[${index}][min_units]" required min="0">
                    </div>
                    <div class="col-12 col-md-3">
                        <label>ถึงหน่วยที่ (Max)</label>
                        <input type="number" class="form-control" name="tiers[${index}][max_units]">
                    </div>
                    <div class="col-12 col-md-3">
                        <label>ราคา/หน่วย</label>
                        <input type="number" step="0.01" class="form-control" name="tiers[${index}][rate_per_unit]" required min="0">
                    </div>
                    <div class="col-12 col-md-3">
                        <label>หมายเหตุ</label>
                        <input type="text" class="form-control" name="tiers[${index}][comment]">
                    </div>
                </div>
            </div>`;
            
            tiersContainer.insertAdjacentHTML('beforeend', html);
            updateTierNumbers();
        };

        window.removeTier = function(btn) {
            btn.closest('.tier-row').remove();
            updateTierNumbers();
        };

        function updateTierNumbers() {
            const rows = tiersContainer.querySelectorAll('.tier-row');
            rows.forEach((row, index) => {
                row.querySelector('.tier-number').textContent = index + 1;
            });
            
            if (rows.length === 0 && noTierAlert) {
                noTierAlert.style.display = 'block';
            }
        }

        // Init
        togglePricingFields();
        pricingTypeSelect.addEventListener('change', togglePricingFields);
    });
</script>