@extends('layouts.super-admin')

@section('nav-main', 'จัดการก๊าซเรือนกระจก')
@section('nav-current', 'Emission Factors')
@section('nav-current-title', 'ฐานข้อมูล Emission Factor (ค่าสัมประสิทธิ์คาร์บอน)')

@section('content')
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show text-white" role="alert">
            <span class="alert-text"><strong>เกิดข้อผิดพลาด!</strong> {{ $errors->first() }}</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- สรุปตัวเลข --}}
    <div class="row">
        <div class="col-xl-3 col-sm-6 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 font-weight-bold">รายการวัสดุในระบบ</p>
                                <h5 class="font-weight-bolder mb-0">{{ number_format($totalCount) }}</h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-success shadow text-center border-radius-md">
                                <i class="fas fa-leaf text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 font-weight-bold">ค่า EF เฉลี่ย (kgCO2e/kg)</p>
                                <h5 class="font-weight-bolder mb-0">{{ number_format($avgEF, 2) }}</h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                <i class="fas fa-chart-line text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- นำเข้า/ส่งออก --}}
    <div class="card mb-4">
        <div class="card-header pb-0">
            <h6 class="mb-0"><i class="fas fa-file-excel me-2 text-success"></i> เครื่องมือนำเข้า/ส่งออกข้อมูล</h6>
        </div>
        <div class="card-body">
            <div class="row g-4">
                <div class="col-md-6">
                    <p class="text-sm font-weight-bold mb-2">ขั้นตอนที่ 1: เตรียมไฟล์</p>
                    <a href="{{ route('keptkayas.emission.export') }}" class="btn btn-outline-primary btn-sm mb-2">
                        <i class="fas fa-download me-1"></i> ดาวน์โหลด Template Excel
                    </a>
                    <p class="text-xs text-secondary mb-0">* กรุณากรอกข้อมูลตามรูปแบบตัวอย่างในไฟล์เพื่อป้องกันข้อผิดพลาด</p>
                </div>
                <div class="col-md-6">
                    <p class="text-sm font-weight-bold mb-2">ขั้นตอนที่ 2: อัปโหลดข้อมูล</p>
                    <form action="{{ route('keptkayas.emission.import') }}" method="POST" enctype="multipart/form-data"
                        class="d-flex gap-2 align-items-start">
                        @csrf
                        <input type="file" name="file" id="efFile" class="form-control form-control-sm"
                            accept=".xlsx, .xls" required>
                        <button type="submit" class="btn bg-gradient-success btn-sm mb-0 text-nowrap">
                            <i class="fas fa-upload me-1"></i> นำเข้าข้อมูล
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- รายการ --}}
    <div class="card mb-4">
        <div class="card-header pb-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h6 class="mb-0">รายการ Emission Factor ทั้งหมด</h6>
            <div>
                <a href="{{ route('keptkayas.emission.create') }}" class="btn bg-gradient-primary btn-sm mb-0">
                    <i class="fas fa-plus me-1"></i> เพิ่มรายการใหม่
                </a>
            </div>
        </div>
        <div class="card-body px-0 pt-0 pb-2">
            <div class="table-responsive p-0">
                <table class="table align-items-center mb-0">
                    <thead>
                        <tr>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="width: 50px">#</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ชื่อวัสดุ (Material Name)</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">หน่วย (Unit)</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ค่า EF (kgCO2e)</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ตัวอย่าง/หมายเหตุ</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">แหล่งที่มา</th>
                            <th class="text-secondary opacity-7"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($emissionFactors as $ef)
                            <tr>
                                <td class="ps-4 text-sm">{{ $loop->iteration }}</td>
                                <td><h6 class="mb-0 text-sm">{{ $ef->material_name }}</h6></td>
                                <td class="text-center">
                                    <span class="badge badge-sm bg-gradient-info">{{ $ef->unit }}</span>
                                </td>
                                <td class="text-center text-sm font-weight-bold text-success">{{ number_format($ef->ef_value, 4) }}</td>
                                <td><p class="text-xs text-secondary mb-0">{{ $ef->example ?: '-' }}</p></td>
                                <td><p class="text-xs text-secondary mb-0">{{ $ef->source ?: '-' }}</p></td>
                                <td class="align-middle text-end pe-4 text-nowrap">
                                    <a href="{{ route('keptkayas.emission.edit', $ef->id) }}"
                                        class="text-secondary font-weight-bold text-xs me-3" title="แก้ไขข้อมูล">
                                        <i class="fas fa-edit me-1"></i> แก้ไข
                                    </a>
                                    <form action="{{ route('keptkayas.emission.destroy', $ef->id) }}" method="POST" class="d-inline"
                                        onsubmit="return confirm('ยืนยันการลบรายการนี้? ข้อมูลที่ถูกลบจะไม่สามารถกู้คืนได้');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-link text-danger font-weight-bold text-xs p-0 mb-0"
                                            title="ลบข้อมูล">
                                            <i class="fas fa-trash me-1"></i> ลบ
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-secondary py-5">
                                    <i class="fas fa-leaf fa-2x mb-2 opacity-5"></i><br>
                                    ยังไม่มีข้อมูลในระบบ กรุณานำเข้าข้อมูลด้วยไฟล์ Excel
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
