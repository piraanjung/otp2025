@extends('layouts.foodwaste')

@section('content')
    <div class="container">
        <h2>ลงทะเบียนถังขยะเศษอาหาร</h2>
        <hr>
        <div class="col-12">
            <div id="previewBox" class="alert alert-info mt-2" style="display: none;">
                <i class="fas fa-info-circle"></i> <span id="previewText">กำลังโหลดข้อมูล...</span>
            </div>
        </div>
        <form action="{{ route('foodwaste.bins.store') }}" method="POST">
            @csrf

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="prefix" class="form-label">รหัสขึ้นต้น (Prefix)</label>
                    <input type="text" class="form-control @error('prefix') is-invalid @enderror" id="prefix" name="prefix"
                        value="{{ old('prefix', 'TWS-H') }}" required placeholder="เช่น TWS-H">
                    @error('prefix')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="quantity" class="form-label">จำนวนที่ต้องการสร้าง (ถัง)</label>
                    <input type="number" class="form-control @error('quantity') is-invalid @enderror" id="quantity"
                        name="quantity" value="{{ old('quantity', 1) }}" min="1" max="100" required>
                    @error('quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="status" class="form-label">สถานะ</label>
                    <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                        <option value="">เลือก..</option>
                        <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="damaged" {{ old('status') == 'damaged' ? 'selected' : '' }}>Damaged</option>
                        <option value="removed" {{ old('status') == 'removed' ? 'selected' : '' }}>Removed</option>
                    </select>
                    @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="bin_type" class="form-label">ประเภทถัง (Bin Type)</label>
                    <select class="form-select @error('bin_type') is-invalid @enderror" id="bin_type" name="bin_type"
                        required>
                        <option value="">เลือกประเภทถัง..</option>
                        <option value="recycle" {{ old('bin_type') == 'annual' ? 'selected' : '' }}>ถังขยะทั่วไป
                        </option>
                        <option value="compost" {{ old('bin_type') == 'compost' ? 'selected' : '' }}>ถังหมักเศษอาหาร</option>
                    </select>
                    @error('bin_type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">รายละเอียด</label>
                <textarea class="form-control @error('description') is-invalid @enderror" id="description"
                    name="description" rows="3">{{ old('description') }}</textarea>
                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <button type="submit" class="btn btn-primary">ยืนยันการสร้างข้อมูล</button>
        </form>
    </div>
@endsection
@section('script')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const prefixInput = document.getElementById('prefix');
            const quantityInput = document.getElementById('quantity');
            const previewBox = document.getElementById('previewBox');
            const previewText = document.getElementById('previewText');

            // สร้างฟังก์ชันสำหรับดึงข้อมูลพรีวิว
            function fetchPreview() {
                const prefix = prefixInput.value.trim();
                const quantity = quantityInput.value;

                // ถ้าช่อง prefix ว่าง ให้ซ่อนกล่องพรีวิว
                if (prefix === '' || quantity < 1) {
                    previewBox.style.display = 'none';
                    return;
                }

                // แสดงคำว่ากำลังโหลดรอไว้ก่อน
                previewBox.style.display = 'block';
                previewText.innerHTML = 'กำลังคำนวณรหัส...';

                // ยิง AJAX ไปถาม Backend
                const url = `{{ route('foodwaste.bins.preview') }}?prefix=${encodeURIComponent(prefix)}&quantity=${quantity}`;

                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        if (data.message) {
                            // แสดงข้อความที่ได้จาก Backend
                            previewText.innerHTML = data.message;
                            // เปลี่ยนสีกล่องเป็นสีเขียวเพื่อบอกว่าพร้อม
                            previewBox.classList.remove('alert-info', 'alert-danger');
                            previewBox.classList.add('alert-success');
                        } else {
                            throw new Error('เกิดข้อผิดพลาด');
                        }
                    })
                    .catch(error => {
                        previewText.innerHTML = '<span class="text-danger">ไม่สามารถโหลดข้อมูลพรีวิวได้</span>';
                        previewBox.classList.remove('alert-info', 'alert-success');
                        previewBox.classList.add('alert-danger');
                    });
            }

            // ดักจับเหตุการณ์เมื่อผู้ใช้พิมพ์หรือเปลี่ยนค่า
            prefixInput.addEventListener('input', fetchPreview);
            quantityInput.addEventListener('input', fetchPreview);

            // โหลดพรีวิวครั้งแรกเมื่อเปิดหน้าเว็บ (ถ้ามีค่า default อยู่แล้ว)
            if (prefixInput.value) {
                fetchPreview();
            }
        });
    </script>
@endsection
