@extends('layouts.admin1')

@section('title_page', 'เพิ่มผู้ใช้น้ำประปาใหม่')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1">💧 ลงทะเบียนผู้ใช้น้ำประปาใหม่</h2>
            <p class="text-muted mb-0">ดึงรายชื่อจากฐานข้อมูลกลาง (Master Users) ที่ยังไม่มีมิเตอร์ประปาในระบบ</p>
        </div>
        <a href="{{ route('tabwater.members.index') }}" class="btn btn-light rounded-pill px-4 shadow-sm">
            <i class="fas fa-arrow-left me-1"></i> กลับหน้ารายชื่อผู้ใช้น้ำ
        </a>
    </div>

    <!-- กล่องกรองข้อมูล (Search & Filter by Zone) -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body">
            <form action="{{ route('tabwater.members.create') }}" method="GET" class="row g-3">
                <!-- ค้นหาด้วยชื่อ/เบอร์/เลขบัตร -->
                <div class="col-md-5">
                    <label class="form-label text-muted small fw-bold">ค้นหาข้อมูลผู้ใช้</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-0 bg-light px-3" placeholder="ค้นหาด้วยชื่อ, นามสกุล, เบอร์โทร หรือเลขบัตร..." value="{{ request('search') }}">
                    </div>
                </div>

                <!-- Filter Zone (โซน/เขต) -->
                <div class="col-md-4">
                    <label class="form-label text-muted small fw-bold">กรองตามโซน/เขต (Zone)</label>
                    <select name="zone_id" class="form-select border-0 bg-light" onchange="this.form.submit()">
                        <option value="">--- แสดงทุกโซน/เขต ---</option>
                        @foreach($zones as $zone)
                            <option value="{{ $zone->id }}" {{ request('zone_id') == $zone->id ? 'selected' : '' }}>
                                {{ $zone->zone_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- ปุ่มกดกรอง -->
                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary rounded-pill px-4 w-100">
                        <i class="fas fa-filter me-1"></i> กรองข้อมูล
                    </button>
                    @if(request()->hasAny(['search', 'zone_id']))
                        <a href="{{ route('tabwater.members.create') }}" class="btn btn-outline-secondary rounded-pill px-3" title="ล้างการกรอง">
                            <i class="fas fa-undo"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- ตารางรายชื่อให้ติ๊กเลือกเพื่อลงทะเบียน -->
    <div class="card border-0 shadow-sm rounded-4">
        <form action="{{ route('tabwater.members.bulkstore') }}" method="POST">
            @csrf
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="bg-light text-muted">
                        <tr>
                            <th class="ps-4" style="width: 50px;">
                                <input type="checkbox" id="selectAll" class="form-check-input">
                            </th>
                            <th>ชื่อ-นามสกุล / ที่อยู่</th>
                            <th style="min-width: 150px;">เลขซีเรียล (Factory No.)</th>
                            <th style="min-width: 130px;">เลขอ่านเริ่มต้น</th>
                            <th style="min-width: 170px;">ประเภทมิเตอร์</th>
                            <th style="min-width: 160px;">เขตจัดเก็บ (Zone)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($availableUsers as $user)
                            <tr>
                                <td class="ps-4">
                                    <input type="checkbox" name="user_ids[]" value="{{ $user->id }}" class="form-check-input user-checkbox">
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $user->prefix }}{{ $user->firstname }} {{ $user->lastname }}</div>
                                    <small class="text-muted">
                                        <i class="fas fa-map-marker-alt text-danger me-1"></i>
                                        บ้านเลขที่ {{ $user->address }} {{ $user->user_zone ? '('.$user->user_zone->zone_name.')' : '' }}
                                    </small>
                                </td>
              
                                <td>
                                    <input type="text" name="factory_no[{{ $user->id }}]" class="form-control form-control-sm bg-light border-0 rounded-3" placeholder="SN โรงงาน">
                                </td>
                                <td>
                                    <input type="number" step="1" name="last_meter_recording[{{ $user->id }}]" class="form-control form-control-sm bg-light border-0 rounded-3" value="0">
                                </td>
                                <td>
                                    <select name="metertype_id[{{ $user->id }}]" class="form-select form-select-sm bg-light border-0 rounded-3">
                                        @foreach($meterTypes as $type)
                                            <option value="{{ $type->id }}">{{ $type->name ?? $type->meter_type_name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <select name="undertake_zone_id[{{ $user->id }}]" class="form-select form-select-sm bg-light border-0 rounded-3">
                                        @foreach($zones as $zone)
                                            <option value="{{ $zone->id }}" {{ $user->zone_id == $zone->id ? 'selected' : '' }}>
                                                {{ $zone->zone_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="fas fa-info-circle fs-4 mb-2 d-block text-secondary"></i>
                                    ไม่พบรายชื่อสมาชิกกลางที่สามารถเพิ่มได้ (หรือลงทะเบียนมิเตอร์ประปาครบแล้ว)
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                <div class="text-muted small">แสดงผลหน้าละ 20 รายการ</div>
                <div>
                    {{ $availableUsers->withQueryString()->links() }}
                </div>
                @if($availableUsers->isNotEmpty())
                    <button type="submit" class="btn btn-success rounded-pill px-5 shadow-sm">
                        <i class="fas fa-check-circle me-1"></i> ยืนยันเพิ่มผู้ใช้น้ำที่เลือก
                    </button>
                @endif
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('selectAll')?.addEventListener('change', function() {
        let checkboxes = document.querySelectorAll('.user-checkbox');
        checkboxes.forEach(cb => cb.checked = this.checked);
    });
</script>
@endsection