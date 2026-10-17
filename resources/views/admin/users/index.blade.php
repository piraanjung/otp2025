@extends('layouts.super-admin')

@section('nav-header', 'สมาชิก')
@section('nav-current', 'ตารางสมาชิก')
@section('page-topic', 'ตารางสมาชิก')
@section('nav-keptkayas.users', 'active')

@section('styles')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .form-check-input {
            width: 1.25rem !important;
            height: 1.25rem !important;
            cursor: pointer;
        }
        .table td { vertical-align: middle !important; }
    </style>
@endsection

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header bg-white">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="fw-bold mb-0"><i class="fas fa-users"></i> จัดการสมาชิกและสิทธิ์บริการ (Master User)</h6>
            <div>
                <button type="button" id="btnSaveBatch" class="btn btn-success btn-sm mb-0 me-2">บันทึกสิทธิ์ที่เลือก</button>
                <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm mb-0">เพิ่มผู้ใช้ใหม่</a>
            </div>
        </div>

        {{-- ฟอร์มค้นหาและกรองข้อมูล --}}
        <form action="{{ route('admin.users.index') }}" method="GET" class="row g-2">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control form-control-sm" 
                       placeholder="ค้นหา ชื่อ, ID, เบอร์โทร..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="zone_id" class="form-select form-select-sm">
                    <option value="">-- ทุกโซน --</option>
                    @foreach($zones as $zone)
                        <option value="{{ $zone->id }}" {{ request('zone_id') == $zone->id ? 'selected' : '' }}>
                            {{ $zone->zone_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="service_filter" class="form-select form-select-sm">
                    <option value="">-- บริการทั้งหมด --</option>
                    <option value="recycle" {{ request('service_filter') == 'recycle' ? 'selected' : '' }}>ธนาคารขยะรีไซเคิล</option>
                    <option value="food_waste" {{ request('service_filter') == 'food_waste' ? 'selected' : '' }}>ธนาคารขยะเปียก</option>
                    <option value="annual_trash" {{ request('service_filter') == 'annual_trash' ? 'selected' : '' }}>จัดการขยะรายปี</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary btn-sm w-100"><i class="fas fa-search me-1"></i> ค้นหา</button>
            </div>
        </form>
    </div>

    {{-- ฟอร์มบันทึกสถานะสิทธิ์บริการ --}}
    <form id="mainBatchForm" action="{{ route('admin.users.update_service') }}" method="POST">
        @csrf
        <div class="table-responsive">
            <table class="table align-items-center mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="text-xs font-weight-bolder ps-4">ID</th>
                        <th class="text-xs font-weight-bolder">ชื่อ-นามสกุล / เบอร์โทร</th>
                        <th class="text-xs font-weight-bolder">โซน / ซอย</th>
                        <th class="text-center text-xs font-weight-bolder">สิทธิ์การเป็นสมาชิก</th>
                        <th class="text-center text-xs font-weight-bolder">จัดการ</th>
                    </tr>
                    <tr class="bg-gray-100">
                        <th colspan="3"></th>
                        <th class="text-center py-2">
                            <div class="d-flex justify-content-center gap-3">
                                <div>
                                    <input class="form-check-input" type="checkbox" id="selectAllRecycle">
                                    <div class="text-xxs fw-bold">รีไซเคิล</div>
                                </div>
                                <div>
                                    <input class="form-check-input" type="checkbox" id="selectAllFood">
                                    <div class="text-xxs fw-bold">ขยะเปียก</div>
                                </div>
                                <div>
                                    <input class="form-check-input" type="checkbox" id="selectAllAnnual">
                                    <div class="text-xxs fw-bold">รายปี</div>
                                </div>
                            </div>
                        </th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td class="ps-4 text-sm font-weight-bold">#{{ sprintf('%05d', $user->id) }}</td>
                            <td>
                                <div class="d-flex flex-column">
                                    <h6 class="mb-0 text-sm">{{ $user->prefix }} {{ $user->firstname }} {{ $user->lastname }}</h6>
                                    <p class="text-xs text-secondary mb-0">{{ $user->phone ?? $user->username }}</p>
                                </div>
                            </td>
                            <td>
                                <div class="text-xs">
                                    <div><strong>โซน:</strong> {{ $user->kpUserPreference->user_pref_zone->zone_name ?? '-' }}</div>
                                    <div class="text-muted"><strong>ซอย:</strong> {{ $user->kpUserPreference->user_pref_subzone->subzone_name ?? '-' }}</div>
                                </div>
                            </td>
                            <td class="align-middle text-center">
    <div class="d-flex justify-content-center gap-4">
        
        {{-- 1. ธนาคารขยะรีไซเคิล: เจ้าหน้าที่รีไซเคิล และ Admin แก้ไขได้ --}}
        <div class="form-check mb-0">
            <input class="form-check-input" type="checkbox" 
                   name="services[{{$user->id}}][recycle]" value="1" 
                   {{ $user->recycleBankAccount ? 'checked' : '' }}
                   {{-- ถ้าไม่ใช่ Admin หรือ Recycle Staff จะกดไม่ได้ --}}
                   {{ auth()->user()->hasAnyRole(['Super Admin', 'Admin', 'Recycle Bank Staff']) ? '' : 'disabled' }}>
        </div>

        {{-- 2. ขยะเปียก: ปิดไม่ให้เจ้าหน้าที่ขยะรีไซเคิลกดแก้ไข --}}
        <div class="form-check mb-0">
            <input class="form-check-input" type="checkbox" 
                   name="services[{{$user->id}}][food_waste]" value="1" 
                   {{ $user->foodWasteBankAccount ? 'checked' : '' }}
                   {{ auth()->user()->hasAnyRole(['Super Admin', 'Admin', 'FoodWaste Staff']) ? '' : 'disabled' }}>
        </div>

        {{-- 3. ขยะรายปี: ปิดไม่ให้เจ้าหน้าที่ขยะรีไซเคิลกดแก้ไข --}}
        <div class="form-check mb-0">
            <input class="form-check-input" type="checkbox" 
                   name="services[{{$user->id}}][annual_trash]" value="1" 
                   {{ $user->annualTrashSubscription ? 'checked' : '' }}
                   {{ auth()->user()->hasAnyRole(['Super Admin', 'Admin', 'Annual Trash Staff']) ? '' : 'disabled' }}>
        </div>

    </div>
</td>
                            <td class="text-center">
                                <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-link p-0 text-primary me-2">
                                    <i class="fas fa-edit"></i> แก้ไข
                                </a>
                                <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-link p-0 text-info">
                                    <i class="fas fa-eye"></i> ดูข้อมูล
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">ไม่พบข้อมูลผู้ใช้งาน</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </form>

    <div class="card-footer bg-white d-flex justify-content-between align-items-center">
        <div class="small text-muted">
            แสดง {{ $users->firstItem() ?? 0 }} ถึง {{ $users->lastItem() ?? 0 }} จากทั้งหมด {{ $users->total() }} รายการ
        </div>
        <div>
            {{ $users->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Select All Checkbox Handler
    function setupSelectAll(triggerId, targetClass) {
        const trigger = document.getElementById(triggerId);
        if (trigger) {
            trigger.addEventListener('change', function() {
                const checkboxes = document.querySelectorAll('tbody .' + targetClass + ':not(:disabled)');
                checkboxes.forEach(chk => chk.checked = this.checked);
            });
        }
    }

    setupSelectAll('selectAllRecycle', 'chk-recycle');
    setupSelectAll('selectAllFood', 'chk-food');
    setupSelectAll('selectAllAnnual', 'chk-annual');

    // SweetAlert2 Confirmation
    const btnSave = document.getElementById('btnSaveBatch');
    const mainForm = document.getElementById('mainBatchForm');

    if (btnSave && mainForm) {
        btnSave.addEventListener('click', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'ยืนยันการปรับเปลี่ยนสิทธิ์บริการ?',
                text: "ระบบจะทำการเพิ่ม/เปิด/ปิด บัญชีบริการตามที่คุณเลือกให้ผู้ใช้แต่ละคน",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#2dce89',
                cancelButtonColor: '#f5365c',
                confirmButtonText: 'ตกลง, บันทึกเลย',
                cancelButtonText: 'ยกเลิก',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    mainForm.submit();
                }
            });
        });
    }
});
</script>
@endsection