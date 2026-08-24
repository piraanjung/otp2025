@extends('layouts.admin1')

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white">
            <h4 class="mb-0">➕ เพิ่มตู้ Kiosk ใหม่</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('keptkayas.kiosks.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="id" class="form-label">รหัสตู้ Kiosk (ID) <span class="text-danger">*</span></label>
                    <input type="text" name="id" id="id" class="form-control @error('id') is-invalid @enderror" value="{{ old('id') }}" placeholder="เช่น KSK-001" required>
                    @error('id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @errorEnd
                </div>

                <div class="mb-3">
                    <label for="name" class="form-label">ชื่อจุดติดตั้ง <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="เช่น ตู้หน้าอาคารเรียน 1" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @errorEnd
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="lat" class="form-label">ละติจูด (Latitude) <span class="text-danger">*</span></label>
                        <!--  กำหนด default จาก $org->lat -->
                        <input type="text" name="lat" id="lat" class="form-control @error('lat') is-invalid @enderror" value="{{ old('lat', $org->lat ?? '') }}" required>
                        @error('lat')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @errorEnd
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="lng" class="form-label">ลองจิจูด (Longitude) <span class="text-danger">*</span></label>
                        <!--  กำหนด default จาก $org->long -->
                        <input type="text" name="lng" id="lng" class="form-control @error('lng') is-invalid @enderror" value="{{ old('lng', $org->long ?? '') }}" required>
                        @error('lng')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @errorEnd
                    </div>
                </div>

                @if($org && $org->lat && $org->long)
                    <div class="mb-3">
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="resetToOrgCoords()">
                            📍 ดึงพิกัดตามตำแหน่งองค์กรอีกครั้ง
                        </button>
                    </div>
                @endif

                <div class="d-flex justify-content-between pt-3">
                    <a href="{{ route('keptkayas.kiosks.index') }}" class="btn btn-secondary">ยกเลิก</a>
                    <button type="submit" class="btn btn-primary">บันทึกข้อมูลตู้</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- @if($org && $org->lat && $org->long)
<script>
    function resetToOrgCoords() {
        document.getElementById('lat').value = "{{ $org->lat }}";
        document.getElementById('long').value = "{{ $org->long }}";
    }
</script>
@endif --}}
@endsection