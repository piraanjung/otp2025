<div class="row">
    <div class="col-md-6 mb-3">
        <label for="category_id" class="form-label">หมวดหมู่สินค้า:</label>
        <select id="category_id" name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
            <option value="">เลือกหมวดหมู่</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" {{ old('category_id', $shop_product->kp_shop_category_id ?? '') == $category->id ? 'selected' : '' }}>
                    {{ $category->category_name }}
                </option>
            @endforeach
        </select>
        @error('category_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="name" class="form-label">ชื่อสินค้า:</label>
        <input type="text" id="name" name="product_name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $shop_product->product_name ?? '') }}" required>
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row">
    <div class="col-md-4 mb-3">
        <label for="stock" class="form-label">จำนวนสินค้าในคลัง:</label>
        <input type="number" id="stock" name="stock" class="form-control @error('stock') is-invalid @enderror" value="{{ old('stock', $shop_product->stock ?? 0) }}" min="0" required>
        @error('stock')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-4 mb-3">
        <label for="point_price" class="form-label">ราคาแลกเปลี่ยน (คะแนน):</label>
        <input type="number" id="point_price" name="point_price" class="form-control @error('point_price') is-invalid @enderror" value="{{ old('point_price', $shop_product->point_price ?? 0) }}" min="0" required>
        @error('point_price')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-4 mb-3">
        <label for="cash_price" class="form-label">ราคาแลกเปลี่ยน (เงินสด):</label>
        <input type="number" step="0.01" id="cash_price" name="cash_price" class="form-control @error('cash_price') is-invalid @enderror" value="{{ old('cash_price', $shop_product->cash_price ?? 0) }}" min="0" required>
        @error('cash_price')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="mb-3">
    <label for="description" class="form-label">รายละเอียดสินค้า:</label>
    <textarea id="description" name="product_description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description', $shop_product->description ?? '') }}</textarea>
    @error('description')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="image" class="form-label">รูปภาพสินค้า:</label>
    <input type="file" id="image" name="image" class="form-control @error('image') is-invalid @enderror">
    @error('image')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
    @if(isset($shop_product) && $shop_product->image_path)
        <div class="mt-2">
            <p>รูปภาพปัจจุบัน:</p>
            <img src="{{ asset('storage/' . $shop_product->image_path) }}" alt="{{ $shop_product->product_name }}" class="img-thumbnail" style="max-width: 200px;">
        </div>
    @endif
</div>
