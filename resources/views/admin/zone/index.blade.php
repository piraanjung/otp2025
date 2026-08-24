@extends('layouts.super-admin')

{{-- ... @section('style') เดิมของคุณ ... --}}
@section('style')
<style>
    /* เพิ่มสไตล์สำหรับ Select Filter */
    .filter-card {
        background: #fff;
        border-radius: 12px;
        border: 1px solid #e9ecef;
        padding: 1.5rem;
    }
    .form-select-flat {
        border: 1px solid #d2d6da !important;
        border-radius: 8px !important;
        padding: 0.5rem 1rem !important;
        box-shadow: none !important;
    }
    .org-badge {
        font-size: 0.7rem;
        background-color: #f0f2f5;
        color: #67748e;
        padding: 2px 8px;
        border-radius: 4px;
        margin-top: 5px;
        display: inline-block;
    }
</style>
{{-- ใส่ CSS เดิมที่คุณมีต่อท้ายตรงนี้ --}}
@endsection

@section('content')
<div class="container-fluid py-4">

    <!-- ส่วน Filter เลือกหน่วยงาน (สำหรับ Super Admin) -->
    <div class="filter-card mb-4 shadow-none">
        <form action="{{ route('admin.zone.index') }}" method="GET" id="filterForm">
            <div class="row align-items-end">
                <div class="col-md-5">
                    <label class="form-label font-weight-bolder text-sm">เลือกหน่วยงานที่ต้องการจัดการ</label>
                    <select name="org_id" class="form-select form-select-flat" onchange="document.getElementById('filterForm').submit()">
                        <option value="">--- แสดงหมู่บ้านทั้งหมด ทุกหน่วยงาน ---</option>
                        @foreach($organizations as $org)
                            <option value="{{ $org->id }}" {{ request('org_id') == $org->id ? 'selected' : '' }}>
                                {{ $org->org_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-7 text-end">
                    <a href="{{ route('admin.zone.create', ['org_id' => request('org_id')]) }}" class="btn btn-create-zone text-black mb-0">
                        <i class="fas fa-plus-circle me-2"></i> สร้างหมู่บ้าน/ซอยใหม่
                    </a>
                </div>
            </div>
        </form>
    </div>

    @if(request('org_id'))
        <div class="mb-3">
            <span class="text-secondary">แสดงผลสำหรับ:</span>
            <strong class="text-dark">{{ $organizations->find(request('org_id'))->org_name }}</strong>
        </div>
    @endif

    <div class="row">
        @forelse ($zones as $item)
            <div class="col-12 col-md-6 col-lg-4 col-xl-3 mb-4">
                <div class="card zone-card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="zone-title text-truncate" style="max-width: 150px;">{{ $item->zone_name }}</h6>
                                <!-- แสดงชื่อหน่วยงานกำกับถ้าเลือกแบบดูทั้งหมด -->
                                @if(!request('org_id'))
                                    <span class="org-badge"><i class="fas fa-building me-1"></i> {{ $item->organization->org_name ?? 'N/A' }}</span>
                                @endif
                            </div>

                            <div class="dropdown">
                                <button class="btn btn-link text-secondary mb-0" data-bs-toggle="dropdown">
                                    <i class="fa fa-ellipsis-v text-xs"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                    <li><a class="dropdown-item" href="{{ route('admin.super_admin.subzone.edit', $item->id) }}">จัดการเส้นทาง</a></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.zone.edit', $item->id) }}">แก้ไขชื่อ/พิกัด</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{route('admin.zone.destroy', $item->id)}}" method="post" onsubmit="return confirm('ยืนยันการลบ?')">
                                            @csrf @method("delete")
                                            <button type="submit" class="dropdown-item text-danger">ลบหมู่บ้าน</button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="card-body pt-1">
                        <p class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 mb-2">เส้นทาง ({{ count($item->subzone) }})</p>
                        <ul class="subzone-list">
                            @forelse ($item->subzone as $subzone)
                                <li class="subzone-item">
                                    <i class="fas fa-road text-primary"></i>
                                    {{ $subzone['subzone_name'] }}
                                </li>
                            @empty
                                <li class="subzone-item text-muted opacity-5">ไม่มีข้อมูลเส้นทาง</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <i class="fas fa-map-marked-alt fa-3x text-secondary opacity-3 mb-3"></i>
                <p class="text-secondary">ไม่พบข้อมูลหมู่บ้านในหน่วยงานนี้</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
