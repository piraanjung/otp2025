@extends('layouts.admin1')

@section('title_page', 'แก้ไขข้อมูลมิเตอร์ประปา')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1">⚙️ แก้ไขข้อมูลมิเตอร์ประปา</h2>
            <p class="text-muted mb-0">
                เลขทะเบียนมิเตอร์: <strong class="text-primary">TW{{ sprintf('%05d', $meter->meternumber) }}</strong>
            </p>
        </div>
        <a href="{{ route('tabwater.members.index') }}" class="btn btn-light rounded-pill px-4 shadow-sm">
            <i class="fas fa-arrow-left me-1"></i> กลับหน้ารายชื่อผู้ใช้น้ำ
        </a>
    </div>

    <!-- กล่องแสดงข้อมูลผู้ใช้เจ้าของมิเตอร์ -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-primary text-white">
        <div class="card-body p-4">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-white text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                    <i class="fas fa-user fs-4"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-1">{{ $meter->user->prefix }}{{ $meter->user->firstname }} {{ $meter->user->lastname }}</h5>
                    <p class="mb-0 small opacity-75">
                        <i class="fas fa-phone me-1"></i> {{ $meter->user->phone ?? '-' }} | 
                        <i class="fas fa-home me-1"></i> ที่อยู่หลัก: {{ $meter->user->address ?? '-' }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- ฟอร์มแก้ไขข้อมูลมิเตอร์ -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <form action="{{ route('tabwater.members.update', $meter->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label text-muted small fw-bold">เลขทะเบียนมิเตอร์ (Meter No.)</label>
                        <input type="text" class="form-control bg-light border-0 py-2 fw-bold text-primary" value="TW{{ sprintf('%05d', $meter->meternumber) }}" readonly>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label text-muted small fw-bold">เลขซีเรียลโรงงาน (Factory No.)</label>
                        <input type="text" name="factory_no" class="form-control bg-light border-0 py-2" value="{{ old('factory_no', $meter->factory_no) }}" placeholder="ระบุเลขซีเรียล">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label text-muted small fw-bold">ประเภทมิเตอร์ <span class="text-danger">*</span></label>
                        <select name="metertype_id" class="form-select bg-light border-0 py-2" required>
                            @foreach($meterTypes as $type)
                                <option value="{{ $type->id }}" {{ old('metertype_id', $meter->metertype_id) == $type->id ? 'selected' : '' }}>
                                    {{ $type->name ?? $type->meter_type_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label text-muted small fw-bold">เขตจัดเก็บ (Zone) <span class="text-danger">*</span></label>
                        <select name="undertake_zone_id" class="form-select bg-light border-0 py-2" required>
                            @foreach($zones as $zone)
                                <option value="{{ $zone->id }}" {{ old('undertake_zone_id', $meter->undertake_zone_id) == $zone->id ? 'selected' : '' }}>
                                    {{ $zone->zone_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label text-muted small fw-bold">เลขจดครั้งล่าสุด <span class="text-danger">*</span></label>
                        <input type="number" step="1" name="last_meter_recording" class="form-control bg-light border-0 py-2" value="{{ old('last_meter_recording', $meter->last_meter_recording) }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label text-muted small fw-bold">สถานะการใช้งาน <span class="text-danger">*</span></label>
                        <select name="status" class="form-select bg-light border-0 py-2" required>
                            <option value="active" {{ old('status', $meter->status) == 'active' ? 'selected' : '' }}>ใช้งานปกติ</option>
                            <option value="inactive" {{ old('status', $meter->status) == 'inactive' ? 'selected' : '' }}>ระงับการใช้งาน</option>
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label text-muted small fw-bold">สถานที่ติดตั้งมิเตอร์</label>
                        <input type="text" name="meter_address" class="form-control bg-light border-0 py-2" value="{{ old('meter_address', $meter->meter_address) }}">
                    </div>
                </div>

                <hr class="my-4 text-muted opacity-25">

                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <button type="submit" class="btn btn-primary rounded-pill px-5 shadow-sm">
                            <i class="fas fa-save me-1"></i> บันทึกการแก้ไข
                        </button>
                        <a href="{{ route('tabwater.members.index') }}" class="btn btn-light rounded-pill px-4 ms-2">ยกเลิก</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- ส่วนโซนอันตราย: สำหรับลบ/ยกเลิกมิเตอร์ -->
    <div class="card border-danger shadow-sm rounded-4 bg-danger-subtle">
        <div class="card-body p-4 d-flex justify-content-between align-items-center">
            <div>
                <h6 class="fw-bold text-danger mb-1"><i class="fas fa-exclamation-triangle me-1"></i> ยกเลิก/ลบมิเตอร์รายการนี้</h6>
                <p class="text-muted small mb-0">หากทำการลบ มิเตอร์เครื่องนี้จะถูกยกเลิกจากระบบทันที (หากผู้ใช้ไม่มีมิเตอร์อื่นเหลืออยู่ ระบบจะถอน Role สมาชิกให้อัตโนมัติ)</p>
            </div>
            <form action="{{ route('tabwater.members.destroy', $meter->id) }}" method="POST" onsubmit="return confirm('คุณแน่ใจหรือไม่ว่าต้องการลบและยกเลิกมิเตอร์เครื่องนี้?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger rounded-pill px-4 shadow-sm">
                    <i class="fas fa-trash-alt me-1"></i> ลบมิเตอร์นี้
                </button>
            </form>
        </div>
    </div>
</div>
@endsection