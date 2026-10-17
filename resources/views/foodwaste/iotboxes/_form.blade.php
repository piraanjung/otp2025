{{-- ฟอร์ม IoT Box ใช้ร่วมกันระหว่าง create / edit ($iotbox เป็น null ตอนสร้างใหม่) --}}
@php $iotbox = $iotbox ?? null; @endphp
<div class="row">
    <div class="col-12 mb-3">
        <label for="iotbox_code" class="form-label">รหัสอุปกรณ์ <span class="text-danger">*</span></label>
        <input type="text" name="iotbox_code" id="iotbox_code" maxlength="100"
            class="form-control @error('iotbox_code') is-invalid @enderror"
            value="{{ old('iotbox_code', $iotbox->iotbox_code ?? '') }}" required>
        @error('iotbox_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    @foreach (['temp_humid_sensor' => 'เซนเซอร์อุณหภูมิ/ความชื้น', 'gas_sensor' => 'เซนเซอร์ก๊าซ', 'weight_sensor' => 'เซนเซอร์น้ำหนัก'] as $field => $label)
        <div class="col-md-4 mb-3">
            <label for="{{ $field }}" class="form-label">{{ $label }} <span class="text-danger">*</span></label>
            <select name="{{ $field }}" id="{{ $field }}" class="form-select @error($field) is-invalid @enderror" required>
                <option value="1" @selected((string) old($field, $iotbox->$field ?? '1') === '1')>มี</option>
                <option value="0" @selected((string) old($field, $iotbox->$field ?? '1') === '0')>ไม่มี</option>
            </select>
            @error($field) <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
    @endforeach
</div>
