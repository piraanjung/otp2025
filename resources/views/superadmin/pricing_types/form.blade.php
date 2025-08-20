<div>
    <label for="name">Name:</label><br>
    <input type="text" id="name" class="form-control" name="name" value="{{ old('name', $pricingType->name ?? '') }}" required>
</div>
<br>

<div>
    <label for="description">Description:</label><br>
    <textarea id="description" class="form-control" name="description">{{ old('description', $pricingType->description ?? '') }}</textarea>
</div>
<br>
