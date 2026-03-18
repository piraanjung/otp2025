@extends('layouts.foodwaste')
 @section('content')
    <div class="container">
        <h2>แก้ไขข้อมูลถังขยะ: {{ $bin->bin_code }}</h2>

        <form action="{{ route('foodwaste.bins.update', $bin->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="bin_code" class="form-label">รหัสถังขยะ</label>
                    <input type="text" class="form-control @error('bin_code') is-invalid @enderror" id="bin_code"
                        name="bin_code" value="{{ old('bin_code', $bin->bin_code) }}" required>
                    @error('bin_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="status" class="form-label">สถานะ</label>
                    <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                        <option value="active" {{ old('status', $bin->status) == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $bin->status) == 'inactive' ? 'selected' : '' }}>Inactive
                        </option>
                        <option value="damaged" {{ old('status', $bin->status) == 'damaged' ? 'selected' : '' }}>Damaged
                        </option>
                        <option value="removed" {{ old('status', $bin->status) == 'removed' ? 'selected' : '' }}>Removed
                        </option>
                    </select>
                    @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">รายละเอียด / หมายเหตุ</label>
                <textarea class="form-control @error('description') is-invalid @enderror" id="description"
                    name="description" rows="3">{{ old('description', $bin->description) }}</textarea>
                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <button type="submit" class="btn btn-primary">บันทึกการเปลี่ยนแปลง</button>
            <a href="{{ route('foodwaste.bins.index') }}" class="btn btn-secondary">ยกเลิก</a>
        </form>
    </div>
@endsection
