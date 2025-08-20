<style>
    .tier-row {
        margin-bottom: 10px;
        padding: 10px;
        border: 1px solid #eee;
        background-color: #f9f9f9;
    }

    .tier-row button {
        margin-left: 10px;
    }
</style>

<div class="row">
    <div class="col-12 col-md-6">
        <label for="meter_type_id">ประเภทมิเตอร์:</label><br>
        <select id="meter_type_id" name="meter_type_id" class="form-control" required>
            <option value="">เลือก..</option>
            @foreach ($meterTypes as $type)
                <option value="{{ $type->id }}" {{ old('meter_type_id', $meterRateConfig->meter_type_id ?? '') == $type->id ? 'selected' : '' }}>
                    {{ $type->meter_type_name }}
                </option>
            @endforeach
        </select>
    </div>


    <div class="col-12 col-md-6">
        <label for="pricing_type_id">ประเภทการชำระ:</label><br>
        <select id="pricing_type_id" name="pricing_type_id" class="form-control" required>
            <option value="">เลือก..</option>
            @foreach ($pricingTypes as $type)
                <option value="{{ $type->id }}" {{ old('pricing_type_id', $meterRateConfig->pricing_type_id ?? '') == $type->id ? 'selected' : '' }}>
                    {{ $type->name }}
                </option>
            @endforeach
        </select>
    </div>
</div>

<div>
    <label for="min_usage_charge">ค่ารักษามิเตอร์:</label><br>
    <input type="number" step="0.01" class="form-control" id="min_usage_charge" name="min_usage_charge"
        value="{{ old('min_usage_charge', $meterRateConfig->min_usage_charge ?? 0) }}" required>
</div>
<br>

<div id="fixed_rate_section" style="display:none;">
    <label for="fixed_rate_per_unit">อัตราการชำระค่าน้ำต่อหน่วย:</label><br>
    <input type="number" step="0.5" class="form-control" id="fixed_rate_per_unit" name="fixed_rate_per_unit"
        value="{{ old('fixed_rate_per_unit', $meterRateConfig->fixed_rate_per_unit ?? '') }}">
</div>
<br>
{{-- @dd($meterRateConfig->tiers->count()) --}}

<div id="progressive_tiers_section" style="display:none;">
    <h3>อัตราการชำระค่าน้ำแบบก้าวหน้า</h3>
    <div id="tiers_container">
        {{-- ตรวจสอบว่า $meterRateConfig ถูกส่งมาและเป็น Record จาก DB และมี tiers --}}

        @if (isset($meterRateConfig) && $meterRateConfig->Ratetiers->count() == 0)
        @php
            $index = 0;
        @endphp
            @foreach ($meterRateConfig->Ratetiers as $tier)
                <div class="tier-row">
                    <label>Tier {{ $index + 1 }}</label><br>
                    <div class="row">
                        <div class="col-12 col-md-2">
                            <label>Min Units:</label>
                            <input type="number" class="form-control" name="tiers[{{ $index }}][min_units]"
                                value="{{ old('tiers.' . $index . '.min_units', $tier->min_units) }}" required>
                        </div>
                        <div class="col-12 col-md-2">
                            <label>Max Units:</label>
                            <input type="number" class="form-control" name="tiers[{{ $index }}][max_units]"
                                value="{{ old('tiers.' . $index . '.max_units', $tier->max_units) }}">

                        </div>
                        <div class="col-12 col-md-2">
                            <label>Rate/Unit:</label>
                            <input type="number" class="form-control" step="0.5" name="tiers[{{ $index }}][rate_per_unit]"
                                value="{{ old('tiers.' . $index . '.rate_per_unit', $tier->rate_per_unit) }}" required>
                        </div>
                        <div class="col-12 col-md-4">
                            <label>Comment:</label>
                            <input type="text" name="tiers[{{ $index }}][comment]"
                                value="{{ old('tiers.' . $index . '.comment', $tier->comment) }}">

                        </div>
                        <div class="col-12 col-md-2">
                            <label>&nbsp;</label>
                            <button type="button" class="btn btn-danger" onclick="removeTier(this)"><i
                                    class="fa fa-trash"></i></button>
                        </div>
                    </div>

                </div>
            @endforeach
            {{-- ส่วนนี้สำหรับ old('tiers') เมื่อเกิด Validation Error --}}
            {{-- @elseif (old('tiers')) --}}
        @else
            @foreach ($meterRateConfig->Ratetiers as $index => $tier)
                <div class="tier-row">
                    <label>Tier {{ $index + 1 }}</label><br>
                    <div class="row">
                        <div class="col-12 col-md-2">
                            <label>Min Units:</label>
                            <input type="number" class="form-control" name="tiers[{{ $index }}][min_units]"
                                value="{{ $tier['min_units'] ?? '' }}" required>
                        </div>
                        <div class="col-12 col-md-2">
                            <label>Max Units:</label>
                            <input type="number" class="form-control" name="tiers[{{ $index }}][max_units]"
                                value="{{ $tier['max_units'] ?? '' }}">
                        </div>
                        <div class="col-12 col-md-2">
                            <label>Rate/Unit:</label>
                            <input type="number" class="form-control" step="0.0001" name="tiers[{{ $index }}][rate_per_unit]"
                                value="{{ $tier['rate_per_unit'] ?? '' }}" required>
                        </div>
                        <div class="col-12 col-md-4">
                            <label>Comment:</label>
                            <input type="text" class="form-control" name="tiers[{{ $index }}][comment]" value="{{ $tier['comment'] ?? '' }}">
                        </div>
                        <div class="col-12 col-md-2">
                            <label>&nbsp;</label><br>
                            <button type="button" class="btn btn-danger" onclick="removeTier(this)"><i
                                    class="fa fa-trash"></i></button>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
    <button type="button" class="btn btn-info" onclick="addTier()">Add Tier</button>
</div>
<br>

<div class="row">
    <div class="col-12 col-md-6">
        <label for="effective_date">Effective Date:</label><br>
        <input type="date" id="effective_date" name="effective_date" class="form-control"
            value="{{ old('effective_date', (isset($meterRateConfig) && $meterRateConfig->effective_date) ? $meterRateConfig->effective_date->format('Y-m-d') : date('Y-m-d')) }}"
            required>
    </div>
    <div class="col-12 col-md-6">
        <label for="end_date">End Date:</label><br>
        <input type="date" id="end_date" name="end_date" class="form-control"
            value="{{ old('end_date', (isset($meterRateConfig) && $meterRateConfig->end_date) ? $meterRateConfig->end_date->format('Y-m-d') : '') }}">
    </div>

</div>


<div class="row">
    <div class="col-12 col-md-6">
        <label for="is_active">Is Active</label>
        <select id="is_active" name="is_active" class="form-control">
            <option value="1" {{ $meterRateConfig->is_active == 1 || collect($meterRateConfig->is_active)->isEmpty() ? 'selected' : '' }}>Active</option>
            <option value="0" {{ $meterRateConfig->is_active == 0 && collect($meterRateConfig->is_active)->isNotEmpty() ? 'selected' : '' }}>Inactive</option>
        </select>
    </div>
    <div class="col-12 col-md-6">
        <label for="comment">Comment:</label><br>
        <textarea id="comment" name="comment"
            class="form-control">{{ old('comment', $meterRateConfig->comment ?? '') }}</textarea>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const pricingTypeSelect = document.getElementById('pricing_type_id');
        const fixedRateSection = document.getElementById('fixed_rate_section');
        const progressiveTiersSection = document.getElementById('progressive_tiers_section');
        const fixedRateInput = document.getElementById('fixed_rate_per_unit');
        const tiersContainer = document.getElementById('tiers_container');

        let tierCounter = tiersContainer.children.length; // สำหรับนับจำนวน Tier ที่มีอยู่แล้ว

        function togglePricingFields() {
            const selectedPricingTypeName = pricingTypeSelect.options[pricingTypeSelect.selectedIndex].text;
            console.log('selectedPricingTypeName', selectedPricingTypeName)

            if (selectedPricingTypeName === ('Fixed').toLowerCase()) {
                fixedRateSection.style.display = 'block';
                fixedRateInput.setAttribute('required', 'required'); // บังคับกรอก
                progressiveTiersSection.style.display = 'none';
            } else if (selectedPricingTypeName === ('Progressive').toLowerCase()) {
                fixedRateSection.style.display = 'none';
                fixedRateInput.removeAttribute('required'); // ไม่บังคับกรอก
                fixedRateInput.value = ''; // เคลียร์ค่า
                progressiveTiersSection.style.display = 'block';
                console.log('selectedPricingTypeName', selectedPricingTypeName)

                // ตรวจสอบว่ามี Tier อย่างน้อย 1 อัน ถ้าเป็น Progressive
                if (tiersContainer.children.length === 0) {
                    // addTier(); // อาจจะเพิ่ม Tier แรกอัตโนมัติ
                }
            } else { // ไม่มีประเภทราคาที่เลือก
                fixedRateSection.style.display = 'none';
                fixedRateInput.removeAttribute('required');
                fixedRateInput.value = '';
                progressiveTiersSection.style.display = 'none';
            }
        }

        function addTier() {
            const newTierDiv = document.createElement('div');
            newTierDiv.classList.add('tier-row');
            newTierDiv.innerHTML = `
                <label>Tier ${tierCounter + 1}</label><br>
                <div class="row">
                    <div class="col-12 col-md-2">
                        <label class=""> Min Units: </label>
                       <input type="number" class="form-control" name="tiers[${tierCounter}][min_units]" required min="0">
                    </div>
                    <div class="col-12 col-md-2">
                        <label class="">Max Units:</label>
                         <input type="number" class="form-control" name="tiers[${tierCounter}][max_units]" min="0">
                    </div>
                    <div class="col-12 col-md-2">
                        <label class="">Rate/Unit:</label>
                         <input type="number" class="form-control" step="0.0001" name="tiers[${tierCounter}][rate_per_unit]" required min="0">
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="">Comment: </label>
                        <input type="text" class="form-control"  name="tiers[${tierCounter}][comment]">
                    </div>
                    <div class="col-12 col-md-1">
                         <label class="">&nbsp;</label>
                        <button type="button" class="btn btn-warning btn-sm" onclick="removeTier(this)"><i class="fa fa-trash"></i></button>
                    </div>
                </div>
                
            `;
            tiersContainer.appendChild(newTierDiv);
            tierCounter++;
        }

        function removeTier(button) {
            button.closest('.tier-row').remove();
            // ปรับปรุง tierCounter หากจำเป็น (ถ้ามีการลบจากตรงกลาง)
            // หรืออาจจะแค่ปล่อยให้ index ใน form array มีช่องว่างก็ได้
        }

        // กำหนดฟังก์ชันให้เป็น Global เพื่อให้ HTML สามารถเรียกได้
        window.addTier = addTier;
        window.removeTier = removeTier;

        // เรียกใช้ครั้งแรกเมื่อโหลดหน้า
        togglePricingFields();

        // เพิ่ม Event Listener เมื่อมีการเปลี่ยนแปลง Pricing Type
        pricingTypeSelect.addEventListener('change', togglePricingFields);
    });
</script>