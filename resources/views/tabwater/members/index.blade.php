@extends('layouts.admin1')

@section('title_page', 'รายชื่อผู้ใช้น้ำประปา')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1">💧 รายชื่อผู้ใช้น้ำประปา</h2>
            <p class="text-muted mb-0">จัดการข้อมูลผู้ใช้น้ำประปาและทะเบียนมิเตอร์ในระบบ</p>
        </div>
        <a href="{{ route('tabwater.members.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
            <i class="fas fa-user-plus me-1"></i> เพิ่มผู้ใช้น้ำใหม่ (ดึงจาก DB กลาง)
        </a>
    </div>

    <!-- Alert แจ้งเตือนข้อความสำเร็จ/ข้อผิดพลาด -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- กล่องกรองข้อมูล -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body">
            <form action="{{ route('tabwater.members.index') }}" method="GET" class="row g-3">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-0 bg-light px-3" placeholder="ค้นหาด้วยชื่อ, เลขมิเตอร์ หรือเบอร์โทร..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <select name="zone_id" class="form-select border-0 bg-light" onchange="this.form.submit()">
                        <option value="">--- แสดงทุกโซน/เขต ---</option>
                        @foreach($zones as $zone)
                            <option value="{{ $zone->id }}" {{ request('zone_id') == $zone->id ? 'selected' : '' }}>
                                {{ $zone->zone_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary rounded-pill px-4 w-100">ค้นหา</button>
                    @if(request()->hasAny(['search', 'zone_id']))
                        <a href="{{ route('tabwater.members.index') }}" class="btn btn-outline-secondary rounded-pill px-3">ล้าง</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- ตารางแสดงรายชื่อ -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="table-responsive">
          <table class="table align-middle mb-0">
    <thead class="bg-light text-muted">
        <tr>
            <th class="ps-4">ชื่อ-นามสกุล / ที่อยู่หลัก</th>
            <th>เขตจัดเก็บ (Zone)</th>
            <th class="text-center">จำนวนมิเตอร์</th>
            <th>รายการมิเตอร์ที่ถือครอง</th>
            <th class="text-end pe-4">จัดการ</th>
        </tr>
    </thead>
    <tbody>
        @forelse($members as $user)
            <tr>
                <!-- ข้อมูลสมาชิก -->
                <td class="ps-4">
                    <div class="fw-bold text-dark fs-6">{{ $user->prefix }}{{ $user->firstname }} {{ $user->lastname }}</div>
                    <small class="text-muted">
                        <i class="fas fa-map-marker-alt me-1 text-danger"></i> {{ $user->address ?? '-' }}
                        @if($user->phone) | <i class="fas fa-phone me-1"></i> {{ $user->phone }} @endif
                    </small>
                </td>

                <!-- เขตจัดเก็บหลัก -->
                <td>{{ $user->user_zone->zone_name ?? '-' }}</td>

                <!-- จำนวนมิเตอร์ทั้งหมด -->
                <td class="text-center">
                    <span class="badge rounded-pill bg-primary-subtle text-primary fw-bold px-3 py-2">
                        {{ $user->usermeterinfos->count() }} เครื่อง
                    </span>
                </td>

                <!-- แสดงรายการมิเตอร์พร้อมปุ่มแก้ไข/ลบรายเครื่อง -->
              <!-- ในส่วน <tbody> ของ index.blade.php -->
<td>
    <div class="d-flex flex-wrap gap-2">
        @foreach($user->usermeterinfos as $meter)
            <div class="border rounded-3 p-2 bg-light d-flex align-items-center justify-content-between gap-2 shadow-sm" style="min-width: 180px;">
                <div>
                    <div class="fw-bold text-primary small">
                        <i class="fas fa-tachometer-alt me-1"></i>
                        {{ $meter->meternumber ? 'TW' . sprintf('%05d', $meter->meternumber) : '-' }}
                    </div>
                    <div class="text-muted" style="font-size: 0.75rem;">
                        {{ $meter->meter_type->name ?? $meter->meter_type->meter_type_name ?? 'ทั่วไป' }} 
                        (ล่าสุด: {{ number_format($meter->last_meter_recording) }})
                    </div>
                </div>
                <!-- ปุ่มกดเข้าไปแก้ไข/จัดการมิเตอร์เครื่องนี้ -->
                <a href="{{ route('tabwater.members.edit', $meter->id) }}" 
                   class="btn btn-sm btn-outline-secondary rounded-circle p-1 ms-1" 
                   title="แก้ไข/จัดการมิเตอร์นี้">
                    <i class="fas fa-cog px-1"></i>
                </a>
            </div>
        @endforeach
    </div>
</td>

                <!-- ปุ่มเพิ่มมิเตอร์เครื่องใหม่ให้ User คนนี้ -->
                <td class="text-end pe-4">
                    <a href="{{ route('tabwater.members.add_meter', $user->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 shadow-sm">
                        <i class="fas fa-plus-circle me-1"></i> เพิ่มมิเตอร์
                    </a>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="text-center py-5 text-muted">
                    <i class="fas fa-info-circle fs-4 mb-2 d-block text-secondary"></i>
                    ยังไม่มีรายการผู้ใช้น้ำประปาในระบบ
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
        </div>
        <div class="card-footer bg-white py-3 border-0">
            {{ $members->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection