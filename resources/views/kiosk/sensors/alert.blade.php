@extends('layouts.admin1')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0 font-weight-bold text-danger">
                🚨 ศูนย์เฝ้าระวังเซ็นเซอร์และอุปกรณ์ขัดข้อง
            </h4>
            <p class="text-muted small mb-0">
                สรุปรายการเซ็นเซอร์ที่ต้องการการตรวจสอบและแก้ไขด่วน ประจำหน่วยงาน {{ Auth::user()->org->name ?? '' }}
            </p>
        </div>
        <button class="btn btn-sm btn-outline-primary" onclick="window.location.reload();">
            🔄 รีเฟรชสถานะ
        </button>
    </div>

    <!-- Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-gradient-danger text-white">
                <div class="card-body p-3">
                    <div class="text-uppercase text-xs opacity-8 fw-bold">ระดับวิกฤต (Error)</div>
                    <div class="display-6 fw-bold mt-1">{{ $errorCount }} <small class="fs-6">รายการ</small></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-gradient-warning text-dark">
                <div class="card-body p-3">
                    <div class="text-uppercase text-xs opacity-8 fw-bold">ระดับเตือน (Warning)</div>
                    <div class="display-6 fw-bold mt-1">{{ $warningCount }} <small class="fs-6">รายการ</small></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-gradient-dark text-white">
                <div class="card-body p-3">
                    <div class="text-uppercase text-xs opacity-8 fw-bold">รวมอุปกรณ์มีปัญหาทั้งหมด</div>
                    <div class="display-6 fw-bold mt-1">{{ $problematicSensors->count() }} <small class="fs-6">รายการ</small></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert List Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0 text-secondary">
                📋 รายการเซ็นเซอร์ขัดข้องทั้งหมด
            </h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-items-center mb-0 table-hover">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-3 text-uppercase text-secondary text-xxs font-weight-bolder">รหัสตู้ / ชื่อจุดติดตั้ง</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder">อุปกรณ์ / เซ็นเซอร์</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center">สถานะ</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder">ค่าอ่านได้ล่าสุด</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder">รายละเอียดข้อผิดพลาด</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center">เวลาที่พบปัญหา</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($problematicSensors as $sensor)
                            <tr class="{{ $sensor->status == 'error' ? 'table-danger' : 'table-warning' }} bg-opacity-10">
                                <td class="ps-3">
                                    <strong class="d-block text-dark">{{ $sensor->kiosk->name ?? 'ไม่ระบุชื่อ' }}</strong>
                                    <small class="text-muted">ID: <code>{{ $sensor->kiosk_id }}</code></small>
                                </td>
                                <td>
                                    <span class="fw-bold d-block text-sm">{{ $sensor->sensor_name }}</span>
                                    <small class="text-muted">Code: <code>{{ $sensor->sensor_code }}</code></small>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $sensor->status == 'error' ? 'danger' : 'warning text-dark' }} text-xxs">
                                        {{ strtoupper($sensor->status) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="text-sm font-monospace">{{ $sensor->last_val ?? '-' }}</span>
                                </td>
                                <td>
                                    <span class="text-sm text-danger fw-semibold">
                                        {{ $sensor->error_message ?? 'ไม่ระบุข้อความแจ้งเตือน' }}
                                    </span>
                                </td>
                                <td class="text-center text-xs text-muted">
                                    {{ $sensor->updated_at ? \Carbon\Carbon::parse($sensor->updated_at)->diffForHumans() : '-' }}
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('keptkayas.kiosks.sensors.index', $sensor->kiosk_id) }}" 
                                       class="btn btn-sm btn-primary mb-0"
                                       title="ไปหน้าจัดการเซ็นเซอร์ตู้นี้">
                                        🛠️ เข้าแก้ไข
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-success">
                                    <i class="bi bi-check-circle-fill display-6 d-block mb-2"></i>
                                    <strong>ไม่พบปัญหาเซ็นเซอร์!</strong> เซ็นเซอร์ทุกตู้ในหน่วยงานทำงานเป็นปกติ
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection