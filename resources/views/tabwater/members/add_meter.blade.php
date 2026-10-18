@extends('layouts.admin1')

@section('title_page', 'เพิ่มมิเตอร์ประปาใหม่')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1">💧 เพิ่มมิเตอร์ประปาใหม่ให้สมาชิก</h2>
            <p class="text-muted mb-0">ลงทะเบียนเพิ่มมิเตอร์อาคาร/บ้านหลังใหม่ให้กับสมาชิกที่มีอยู่ในระบบแล้ว</p>
        </div>
        <a href="{{ route('tabwater.members.index') }}" class="btn btn-light rounded-pill px-4 shadow-sm">
            <i class="fas fa-arrow-left me-1"></i> กลับหน้ารายชื่อผู้ใช้น้ำ
        </a>
    </div>

    <!-- กล่องแสดงข้อมูลผู้ใช้หลัก -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-primary text-white">
        <div class="card-body p-4">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-white text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 54px; height: 54px;">
                    <i class="fas fa-user fs-4"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-1">{{ $user->prefix }}{{ $user->firstname }} {{ $user->lastname }}</h4>
                    <p class="mb-0 opacity-75">
                        <i class="fas fa-id-card me-1"></i> เลขบัตรประชาชน: {{ $user->id_card ?? '-' }} | 
                        <i class="fas fa-phone me-1"></i> เบอร์โทรศัพท์: {{ $user->phone ?? '-' }} | 
                        <i class="fas fa-home me-1"></i> ที่อยู่หลัก: {{ $user->address ?? '-' }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- ฟอร์มเพิ่มข้อมูลมิเตอร์ -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <form action="{{ route('tabwater.members.store_meter', $user->id) }}" method="POST">
                @csrf
                <div class="row g-4">
                    <!-- แสดงสถานะเลขทะเบียนมิเตอร์ (Auto Run) -->
                    <div class="col-md-4">
                        <label class="form-label text-muted small fw-bold">เลขทะเบียนมิเตอร์ (Meter No.)</label>
                        <input type="text" class="form-control bg-light border-0 py-2" value="TW-HS-000{{ $meterNo }}" readonly>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted small fw-bold">ชื่อมิเตอร์</label>
                        <input type="text" name="submeter_name" class="form-control bg-light border-0 py-2" placeholder="ระบุเลขซีเรียลมิเตอร์ (ถ้ามี)" value="{{ old('factory_no') }}">
                    </div>

                    <!-- เลขซีเรียลมิเตอร์โรงงาน -->
                    <div class="col-md-4">
                        <label class="form-label text-muted small fw-bold">เลขซีเรียลมิเตอร์โรงงาน (Factory No. / Serial)</label>
                        <input type="text" name="factory_no" class="form-control bg-light border-0 py-2" placeholder="ระบุเลขซีเรียลมิเตอร์ (ถ้ามี)" value="{{ old('factory_no') }}">
                    </div>

                    <!-- ประเภทมิเตอร์ -->
                    <div class="col-md-6">
                        <label class="form-label text-muted small fw-bold">ประเภทมิเตอร์ประปา <span class="text-danger">*</span></label>
                        <select name="metertype_id" class="form-select bg-light border-0 py-2" required>
                            <option value="">-- เลือกประเภทมิเตอร์ --</option>
                            @foreach($meterTypes as $type)
                                <option value="{{ $type->id }}" {{ old('metertype_id') == $type->id ? 'selected' : '' }}>
                                    {{ $type->name ?? $type->meter_type_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- เขตจัดเก็บ (Zone) -->
                    <div class="col-md-6">
                        <label class="form-label text-muted small fw-bold">เขตจัดเก็บ (Zone) <span class="text-danger">*</span></label>
                        <select name="undertake_zone_id" class="form-select bg-light border-0 py-2" required>
                            <option value="">-- เลือกเขตจัดเก็บ --</option>
                            @foreach($zones as $zone)
                                <option value="{{ $zone->id }}" {{ (old('undertake_zone_id') ?? $user->zone_id) == $zone->id ? 'selected' : '' }}>
                                    {{ $zone->zone_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- เลขอ่านเริ่มต้น -->
                    <div class="col-md-6">
                        <label class="form-label text-muted small fw-bold">เลขอ่านเริ่มต้นมิเตอร์ (Initial Recording) <span class="text-danger">*</span></label>
                        <input type="number" step="1" min="0" name="last_meter_recording" class="form-control bg-light border-0 py-2" value="{{ old('last_meter_recording', 0) }}" required>
                    </div>

                    <!-- ที่ติดตั้งมิเตอร์ (ถ้าต่างจากที่อยู่หลัก) -->
                    <div class="col-md-6">
                        <label class="form-label text-muted small fw-bold">สถานที่ติดตั้งมิเตอร์ / ที่อยู่อุปกรณ์</label>
                        <input type="text" name="meter_address" class="form-control bg-light border-0 py-2" placeholder="ระบุสถานที่ติดตั้งมิเตอร์ (เว้นว่างไว้จะใช้ที่อยู่หลัก)" value="{{ old('meter_address', $user->address) }}">
                    </div>
                </div>

                <hr class="my-4 text-muted opacity-25">

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('tabwater.members.index') }}" class="btn btn-light rounded-pill px-4">ยกเลิก</a>
                    <button type="submit" class="btn btn-success rounded-pill px-5 shadow-sm">
                        <i class="fas fa-check-circle me-1"></i> บันทึกเพิ่มมิเตอร์
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection