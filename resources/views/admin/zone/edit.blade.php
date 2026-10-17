@extends('layouts.super-admin')

@section('title_page', 'แก้ไขหมู่บ้าน/โซน')

@section('style')
<style>
    .card-flat { border: 1px solid #e9ecef !important; border-radius: 12px !important; box-shadow: none !important; background-color: #fff; }
    .form-label { font-weight: 700; color: #344767; font-size: 0.85rem; margin-bottom: 0.5rem; }
    .form-control-flat { border: 1px solid #d2d6da !important; border-radius: 8px !important; background-color: #f8f9fa !important; box-shadow: none !important; transition: all 0.2s ease; }
    .form-control-flat:focus { background-color: #fff !important; border-color: #5e72e4 !important; }
    .btn-flat { border-radius: 8px; font-weight: 600; padding: 0.6rem 1.2rem; box-shadow: none !important; }
    .btn-gps { background-color: #e8eafe; color: #5e72e4; border: none; }
    .btn-gps:hover { background-color: #5e72e4; color: #fff; }
    .subzone-item { border-bottom: 1px dashed #f1f3f5; padding: 10px 0; }
    .subzone-item:last-child { border-bottom: none; }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-lg-9 mx-auto">
            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('admin.zone.index') }}" class="btn btn-link text-dark p-0 me-3"><i class="fas fa-arrow-left"></i></a>
                <div>
                    <h4 class="font-weight-bolder mb-0">แก้ไขหมู่บ้าน / โซน</h4>
                    <p class="text-sm text-secondary mb-0">ID: #{{ $zone->id }} | ปรับปรุงล่าสุด: </p>
                    {{-- {{ $zone->updated_at->format('d/m/Y H:i') }} --}}
                </div>
            </div>

            <form action="{{ route('admin.zone.update', $zone->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-7">
                        <div class="card card-flat mb-4">
                            <div class="card-body p-4">
                                <h6 class="text-uppercase text-primary text-xxs font-weight-bolder mb-3">รายละเอียดหลัก</h6>
                                <div class="mb-3">
                                    <label class="form-label">ชื่อหมู่บ้าน / โซน</label>
                                    <input type="text" name="zone_name" class="form-control form-control-flat" value="{{ old('zone_name', $zone->zone_name) }}" required>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">ตำบล (Tambon ID)</label>
                                        <input type="text" name="tambon_id" class="form-control form-control-flat" value="{{ old('tambon_id', $zone->tambon_id) }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">สถานะการทำงาน</label>
                                        <select name="status" class="form-select form-control-flat">
                                            <option value="active" {{ $zone->status == 'active' ? 'selected' : '' }}>เปิดใช้งาน (Active)</option>
                                            <option value="inactive" {{ $zone->status == 'inactive' ? 'selected' : '' }}>ปิดใช้งาน (Inactive)</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-0">
                                    <label class="form-label">คำอธิบายสถานที่ (Location)</label>
                                    <textarea name="location" class="form-control form-control-flat" rows="3">{{ old('location', $zone->location) }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="card card-flat mb-4">
                            <div class="card-header bg-transparent border-bottom py-3 d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 text-sm font-weight-bold">เส้นทาง/ซอยย่อย ({{ count($zone->subzone) }})</h6>
                                <a href="{{ route('admin.super_admin.subzone.edit', $zone->id) }}" class="text-xs text-primary font-weight-bold">จัดการเส้นทาง</a>
                            </div>
                            <div class="card-body p-4 pt-2">
                                @forelse($zone->subzone as $sub)
                                    <div class="subzone-item d-flex align-items-center">
                                        <i class="fas fa-road text-secondary g me-3 opacity-5"></i>
                                        <span class="text-sm text-dark">{{ $sub->subzone_name }}</span>
                                    </div>
                                @empty
                                    <p class="text-xs text-muted py-3 mb-0 text-center">ไม่มีข้อมูลเส้นทางย่อย</p>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <div class="col-md-5">
                        <div class="card card-flat mb-4 border-primary" style="border-width: 2px !important;">
                            <div class="card-body p-4 text-center">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="text-uppercase text-muted text-xxs font-weight-bolder mb-0">พิกัด GPS</h6>
                                    <button type="button" class="btn btn-xs btn-gps mb-0 shadow-none" onclick="getLocation()">
                                        <i class="fas fa-crosshairs me-1"></i> ดึงค่าปัจจุบัน
                                    </button>
                                </div>
                                <div class="mb-3 text-start">
                                    <label class="form-label text-xs">Latitude</label>
                                    <input type="text" name="lat" id="lat_val" class="form-control form-control-flat" value="{{ old('lat', $zone->lat) }}">
                                </div>
                                <div class="mb-3 text-start">
                                    <label class="form-label text-xs">Longitude</label>
                                    <input type="text" name="long" id="long_val" class="form-control form-control-flat" value="{{ old('long', $zone->long) }}">
                                </div>
                                <div class="bg-gray-100 border-radius-lg p-3" style="border: 1px dashed #dee2e6;">
                                    <i class="fas fa-map-marked-alt text-secondary mb-2"></i>
                                    <p class="text-xxs text-secondary mb-0">พิกัดนี้จะถูกใช้ระบุตำแหน่งบนแอปฯ สำหรับเจ้าหน้าที่เก็บขยะ</p>
                                </div>
                            </div>
                        </div>

                        <div class="card card-flat bg-gray-100">
                            <div class="card-body p-4">
                                <button type="submit" class="btn btn-primary w-100 btn-flat mb-3">
                                    <i class="fas fa-save me-2"></i> บันทึกการเปลี่ยนแปลง
                                </button>
                                <a href="{{ route('admin.zone.index') }}" class="btn btn-light w-100 btn-flat border">ยกเลิก</a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    // ระบบดึงพิกัดจากเครื่องคอมพิวเตอร์หรือมือถือของ Admin
    function getLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                document.getElementById("lat_val").value = position.coords.latitude;
                document.getElementById("long_val").value = position.coords.longitude;
                alert('ดึงพิกัดปัจจุบันสำเร็จ!');
            }, function(error) {
                alert("ไม่สามารถดึงพิกัดได้: " + error.message);
            });
        } else {
            alert("เบราว์เซอร์ของคุณไม่รองรับการดึง GPS");
        }
    }
</script>
@endsection
