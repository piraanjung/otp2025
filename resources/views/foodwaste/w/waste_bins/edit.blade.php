@extends('layouts.app')

@section('title_page', 'แก้ไขถังขยะ')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <h6>แก้ไขข้อมูลถังขยะ: {{ $wasteBin->bin_code ?? 'N/A' }} สำหรับ {{ $wasteBin->user->firstname }}</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('waste_bins.update', $wasteBin->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="bin_code" class="form-label">รหัสถังขยะ (ถ้ามี)</label>
                            <input type="text" class="form-control @error('bin_code') is-invalid @enderror" id="bin_code" name="bin_code" value="{{ old('bin_code', $wasteBin->bin_code) }}">
                            @error('bin_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label for="bin_type" class="form-label">ประเภทถังขยะ</label>
                            <input type="text" class="form-control @error('bin_type') is-invalid @enderror" id="bin_type" name="bin_type" value="{{ old('bin_type', $wasteBin->bin_type) }}" required>
                            @error('bin_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label for="location_description" class="form-label">รายละเอียดตำแหน่งที่ตั้ง</label>
                            <textarea class="form-control @error('location_description') is-invalid @enderror" id="location_description" name="location_description" rows="3">{{ old('location_description', $wasteBin->location_description) }}</textarea>
                            @error('location_description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="latitude" class="form-label">ละติจูด</label>
                                    <input type="number" step="any" class="form-control @error('latitude') is-invalid @enderror" id="latitude" name="latitude" value="{{ old('latitude', $wasteBin->latitude) }}">
                                    @error('latitude')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="longitude" class="form-label">ลองจิจูด</label>
                                    <input type="number" step="any" class="form-control @error('longitude') is-invalid @enderror" id="longitude" name="longitude" value="{{ old('longitude', $wasteBin->longitude) }}">
                                    @error('longitude')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="status" class="form-label">สถานะของถัง</label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="active" {{ old('status', $wasteBin->status) == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', $wasteBin->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="damaged" {{ old('status', $wasteBin->status) == 'damaged' ? 'selected' : '' }}>Damaged</option>
                                <option value="removed" {{ old('status', $wasteBin->status) == 'removed' ? 'selected' : '' }}>Removed</option>
                            </select>
                            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <div class="form-check form-switch ps-0">
                                <input class="form-check-input ms-auto" type="checkbox" id="is_active_for_annual_collection" name="is_active_for_annual_collection" value="1" {{ old('is_active_for_annual_collection', $wasteBin->is_active_for_annual_collection) ? 'checked' : '' }}>
                                <label class="form-check-label text-body ms-3 text-truncate w-80 mb-0" for="is_active_for_annual_collection">
                                    ถังนี้ใช้งานสำหรับบริการเก็บขยะรายปี
                                </label>
                            </div>
                        </div>
                        <button type="submit" class="btn bg-gradient-primary">บันทึกการเปลี่ยนแปลง</button>
                        <a href="{{ route('waste_bins.index', $wasteBin->user->id) }}" class="btn btn-secondary">ยกเลิก</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection