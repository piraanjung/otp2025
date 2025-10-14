@extends('layouts.foodwaste') 

@section('content')
<div class="container">
    <h2>เพิ่มถังขยะ Food Waste ใหม่</h2>
    <hr>

    <form action="{{ route('foodwaste.bins.store') }}" method="POST">
        @csrf

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="bin_code" class="form-label">รหัสถังขยะ (Bin Code)</label>
                <input type="text" class="form-control @error('bin_code') is-invalid @enderror" id="bin_code" name="bin_code" value="{{ old('bin_code') }}" required>
                @error('bin_code')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
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
                @error('status')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">รายละเอียด</label>
            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3" required>{{ old('description') }}</textarea>
            @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        
      
        <button type="submit" class="btn btn-success">บันทึกข้อมูล</button>
        <a href="{{ route('foodwaste.bins.index') }}" class="btn btn-secondary">ยกเลิก</a>
    </form>
</div>
@endsection