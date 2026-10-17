<div class="row">
    <div class="col-md-6 mb-3">
        <label for="shop_name" class="form-label">ชื่อร้านรับซื้อ:</label>
        <input type="text" id="shop_name" name="shop_name" class="form-control @error('shop_name') is-invalid @enderror" value="{{ old('shop_name', $shop->shop_name ?? '') }}" required>
        @error('shop_name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="contact_person" class="form-label">ชื่อผู้ติดต่อ:</label>
        <input type="text" id="contact_person" name="contact_person" class="form-control @error('contact_person') is-invalid @enderror" value="{{ old('contact_person', $shop->contact_person ?? '') }}">
        @error('contact_person')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="phone" class="form-label">เบอร์โทรศัพท์:</label>
        <input type="text" id="phone" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $shop->phone ?? '') }}">
        @error('phone')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="status" class="form-label">สถานะ:</label>
        <select id="status" name="status" class="form-select @error('status') is-invalid @enderror" required>
            <option value="active" {{ old('status', $shop->status ?? '') == 'active' ? 'selected' : '' }}>ใช้งานอยู่</option>
            <option value="inactive" {{ old('status', $shop->status ?? '') == 'inactive' ? 'selected' : '' }}>ไม่ใช้งาน</option>
        </select>
        @error('status')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="form-group mb-3">
    <label for="address" class="form-label">ที่อยู่:</label>
    <textarea id="address" name="address" class="form-control @error('address') is-invalid @enderror">{{ old('address', $shop->address ?? '') }}</textarea>
    @error('address')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="form-group mb-3">
    <label for="comment" class="form-label">หมายเหตุ:</label>
    <textarea id="comment" name="comment" class="form-control @error('comment') is-invalid @enderror">{{ old('comment', $shop->comment ?? '') }}</textarea>
    @error('comment')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
