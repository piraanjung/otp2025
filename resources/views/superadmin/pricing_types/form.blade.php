<div>
    <label for="name">ชื่อ</label>
    <input type="text" id="name" class="form-control" name="name" value="{{ old('name', $pricingType->name ?? '') }}" required>
</div>
<br>

<div>
    <label for="description">คำอธิบาย</label>
    <textarea id="description" class="form-control" name="description">{{ old('description', $pricingType->description ?? '') }}</textarea>
</div>
<br>
