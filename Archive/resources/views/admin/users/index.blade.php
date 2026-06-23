@extends('layouts.super-admin')

@section('nav-header', 'สมาชิก')
@section('nav-current', 'ตารางสมาชิก')
@section('page-topic', 'ตารางสมาชิก')
@section('nav-keptkayas.users', 'active')

{{-- เพิ่ม CDN SweetAlert2 สำหรับ Popup ยืนยันสวยๆ --}}
@section('styles')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .form-check-input {
        width: 1.25rem !important;
        height: 1.25rem !important;
        margin-top: 0.25em;
        vertical-align: top;
        background-color: #fff;
        background-repeat: no-repeat;
        background-position: center;
        background-size: contain;
        border: 1px solid rgba(0,0,0,.25) !important;
        -webkit-appearance: checkbox !important; /* สำหรับบาง Browser ที่ซ่อนไว้ */
        cursor: pointer;
    }

    .form-check-input:checked {
        background-color: #2dce89; /* สีเขียวตามสไตล์ Soft UI */
        border-color: #2dce89;
    }

    /* ป้องกัน Row สลับกันมั่ว */
    .table td {
        vertical-align: middle !important;
    }
    </style>
@endsection


@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header bg-white">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="fw-bold mb-0"><i class="bi bi-people-fill"></i> จัดการสมาชิกและสิทธิ์บริการ</h6>
            <button type="button" id="btnSaveBatch" class="btn btn-success btn-sm mb-0">บันทึกสิทธิ์ที่เลือก</button>
            <a  href="{{ route('admin.users.create') }}"  class="btn btn-success btn-sm mb-0">เพิ่ม user</a>
        </div>

        <form action="{{ route('admin.users.index') }}" method="GET" class="row g-2">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="ค้นหา ชื่อ, ID, ที่อยู่..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <select name="zone_id" class="form-select form-select-sm">
                    <option value="">-- ทุกโซน --</option>
                    @foreach($zones as $zone)
                        <option value="{{ $zone->id }}" {{ request('zone_id') == $zone->id ? 'selected' : '' }}>{{ $zone->zone_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="service_filter" class="form-select form-select-sm">
                    <option value="">-- บริการทั้งหมด --</option>
                    <option value="recycle" {{ request('service_filter') == 'recycle' ? 'selected' : '' }}>ธนาคารขยะ</option>
                    <option value="food_waste" {{ request('service_filter') == 'food_waste' ? 'selected' : '' }}>ขยะเปียก</option>
                    <option value="annual_trash" {{ request('service_filter') == 'annual_trash' ? 'selected' : '' }}>ขยะรายปี</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary btn-sm w-100">ค้นหา</button>
            </div>
        </form>
    </div>

    <form id="mainBatchForm" action="{{ route('admin.users.update_service') }}" method="POST">
        @csrf
        <div class="table-responsive">
            <table class="table align-items-center mb-0">
                <thead class="bg-light">
<tr>
        <th class="text-xs font-weight-bolder ps-4">ID</th>
        <th class="text-xs font-weight-bolder">ชื่อ-ที่อยู่</th>
        <th class="text-xs font-weight-bolder">โซน/ซอย</th>
        <th class="text-center text-xs font-weight-bolder">สถานะบริการ</th>
        <th class="text-center text-xs font-weight-bolder">จัดการ</th>
    </tr>
    <tr class="bg-gray-100" style="border-bottom: 2px solid #dee2e6;">
        <th colspan="3" class="text-end text-xxs font-weight-bolder py-2"></th>
        <th class="text-center py-2">
            <div class="d-flex justify-content-center gap-3">
                <div class="form-check mb-0">
                    <input class="form-check-input" type="checkbox" id="selectAllRecycle" title="เลือกธนาคารขยะทั้งหมด">
                    <span class="text-xxs">รีไซเคิล</span>
                </div>
                <div class="form-check mb-0">
                    <input class="form-check-input" type="checkbox" id="selectAllFood" title="เลือกขยะเปียกทั้งหมด">
                    <span class="text-xxs">ขยะเปียก</span>
                </div>
                <div class="form-check mb-0">
                    <input class="form-check-input" type="checkbox" id="selectAllAnnual" title="เลือกรายปีทั้งหมด">
                    <span class="text-xxs">รายปี</span>
                </div>
            </div>
        </th>
        <th></th>
    </tr>
                </thead>
                <tbody>
    @foreach($users as $user)
    <tr>
        {{-- 1. ID --}}
        <td class="ps-4 text-sm">{{ $user->id }}</td>

        {{-- 2. ชื่อ-ที่อยู่ --}}
        <td>
            <div class="d-flex flex-column">
                <h6 class="mb-0 text-sm">{{ $user->firstname }} {{ $user->lastname }}</h6>
                <p class="text-xs text-secondary mb-0">{{ $user->address ?? '-' }}</p>
            </div>
        </td>

        {{-- 3. โซน/ซอย --}}
        <td>
            <div class="text-xs">
                <div><strong>โซน:</strong> {{ $user->user_zone->zone_name ?? '-' }}</div>
                <div class="text-muted"><strong>ซอย:</strong> {{ $user->user_subzone->subzone_name ?? '-' }}</div>
            </div>
        </td>

        {{-- 4. สถานะบริการ (จุดที่หายไป) --}}
        <td class="align-middle text-center">
            <div class="d-flex justify-content-center gap-3">
                {{-- รีไซเคิล --}}
                <div class="form-check mb-0">
                    <input class="form-check-input chk-recycle" type="checkbox"
                        name="services[{{$user->id}}][recycle]" value="1"
                        {{ $user->recycleAccount ? 'checked' : '' }}>
                </div>
                {{-- ขยะเปียก --}}
                <div class="form-check mb-0">
                    <input class="form-check-input chk-food" type="checkbox"
                        name="services[{{$user->id}}][food_waste]" value="1"
                        {{ $user->foodWasteAccount ? 'checked' : '' }}>
                </div>
                {{-- รายปี --}}
                <div class="form-check mb-0">
                    <input class="form-check-input chk-annual" type="checkbox"
                        name="services[{{$user->id}}][annual_trash]" value="1"
                        {{ $user->annualTrashSubscription ? 'checked' : '' }}>
                </div>
            </div>
        </td>

        {{-- 5. การจัดการ --}}
        <td class="text-center">
            <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-link p-0 text-primary">
                <i class="fas fa-edit"></i> แก้ไข
            </a>
        </td>
    </tr>
    @endforeach
</tbody>
            </table>
        </div>
    </form>

    <div class="card-footer bg-white d-flex justify-content-between">
        <div class="small text-muted">
            แสดง {{ $users->firstItem() }} ถึง {{ $users->lastItem() }} จากทั้งหมด {{ $users->total() }} รายการ
        </div>
        <div>
            {{ $users->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {

        // --- 1. SEARCH VARIABLES & ELEMENTS ---
        const elements = {
            searchName: document.getElementById('search_name'),
            searchEmail: document.getElementById('search_email'),
            searchStatus: document.getElementById('search_status'),
            searchAnnual: document.getElementById('search_is_annual_collection'),
            searchBank: document.getElementById('search_is_waste_bank'),
            btnApply: document.getElementById('applySearchBtn'),
            tableBody: document.getElementById('userTableBody'),
            chkAllAnnual: document.getElementById('selectAllAnnualCollection'),
            chkAllBank: document.getElementById('selectAllWasteBank'),
            perPage: document.getElementById('per_page'),
            btnSave: document.getElementById('btnSaveBatch'),
            mainForm: document.getElementById('mainBatchForm')
        };

        // ตรวจสอบว่ามี Element ครบไหม
        for (const [key, el] of Object.entries(elements)) {
            if (!el && key !== 'tableBody') console.warn(`Element ${key} not found`);
        }

        // --- 2. LIVE SEARCH LOGIC ---
        let searchTimeout;
        const debounceDelay = 300;

        function applyLiveSearch() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                const params = new URLSearchParams({
                    search_name: elements.searchName.value,
                    search_email: elements.searchEmail.value,
                    search_status: elements.searchStatus.value,
                    search_is_annual_collection: elements.searchAnnual.value,
                    search_is_waste_bank: elements.searchBank.value,
                    per_page: elements.perPage.value,
                    ajax: '1'
                });

                fetch(`{{ route('keptkayas.users.index') }}?${params.toString()}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(res => res.text())
                .then(html => {
                    elements.tableBody.innerHTML = html;
                    // Re-initialize tooltips if you use BS tooltips
                    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
                    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                        return new bootstrap.Tooltip(tooltipTriggerEl)
                    })
                })
                .catch(err => console.error('Search error:', err));
            }, debounceDelay);
        }

        // Attach Search Events
        [elements.searchName, elements.searchEmail].forEach(el => el && el.addEventListener('keyup', applyLiveSearch));
        [elements.searchStatus, elements.searchAnnual, elements.searchBank, elements.perPage].forEach(el => el && el.addEventListener('change', applyLiveSearch));
        if(elements.btnApply) elements.btnApply.addEventListener('click', applyLiveSearch);


        // --- 3. SELECT ALL LOGIC (SAFE MODE) ---

        // ฟังก์ชันเลือกทั้งหมด "เก็บรายปี" - จะไม่ยุ่งกับตัวที่ Disabled
        if(elements.chkAllAnnual) {
            elements.chkAllAnnual.addEventListener('change', function() {
                const checkboxes = document.querySelectorAll('.annual-coll-checkbox:not(:disabled)'); // Select เฉพาะตัวที่แก้ได้
                checkboxes.forEach(chk => chk.checked = this.checked);
            });
        }

        // ฟังก์ชันเลือกทั้งหมด "ธนาคารขยะ"
        if(elements.chkAllBank) {
            elements.chkAllBank.addEventListener('change', function() {
                const checkboxes = document.querySelectorAll('.waste-bank-checkbox:not(:disabled)');
                checkboxes.forEach(chk => chk.checked = this.checked);
            });
        }

        // --- 4. SAVE CONFIRMATION (SweetAlert2) ---
        if(elements.btnSave && elements.mainForm) {
            elements.btnSave.addEventListener('click', function(e) {
                e.preventDefault();

                Swal.fire({
                    title: 'ยืนยันการบันทึก?',
                    text: "ตรวจสอบความถูกต้องของบริการที่เลือกก่อนบันทึก",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'ยืนยัน, บันทึกเลย!',
                    cancelButtonText: 'ยกเลิก'
                }).then((result) => {
                    if (result.isConfirmed) {
                        elements.mainForm.submit();
                    }
                });
            });
        }

        // Initialize Tooltips for the "Lock" icons
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    });

</script>
   <script>
    document.addEventListener('DOMContentLoaded', function() {

        // --- 1. จัดการการเลือกทั้งหมด (Select All) ---
        // ฟังก์ชันนี้จะทำงานเมื่อ ID ของ Checkbox ที่หัวตาราง ตรงกับที่ส่งเข้าไป
        function setupSelectAll(triggerId, targetClass) {
            const trigger = document.getElementById(triggerId);
            if (trigger) {
                trigger.addEventListener('change', function() {
                    // เลือก Checkbox ใน tbody ที่มี class ตรงกัน และไม่ถูก disabled
                    const checkboxes = document.querySelectorAll('tbody .' + targetClass + ':not(:disabled)');
                    checkboxes.forEach(chk => {
                        chk.checked = this.checked;
                    });
                });
            }
        }

        // เรียกใช้ฟังก์ชันให้ตรงกับ ID ใน Header และ Class ใน Body
        setupSelectAll('selectAllRecycle', 'chk-recycle');
        setupSelectAll('selectAllFood', 'chk-food');
        setupSelectAll('selectAllAnnual', 'chk-annual');


        // --- 2. ยืนยันการบันทึกด้วย SweetAlert2 ---
        const btnSave = document.getElementById('btnSaveBatch');
        const mainForm = document.getElementById('mainBatchForm');

        if (btnSave && mainForm) {
            btnSave.addEventListener('click', function(e) {
                e.preventDefault();

                // ตรวจสอบว่า Swal โหลดมาหรือยัง
                if (typeof Swal === 'undefined') {
                    mainForm.submit(); // ถ้า Swal ไม่มา ให้ Submit แบบปกติไปเลย
                    return;
                }

                Swal.fire({
                    title: 'ยืนยันการบันทึกสิทธิ์?',
                    text: "ระบบจะเปิด/ปิดบัญชีสมาชิกตามที่คุณเลือกในหน้านี้",
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

        // --- 3. เริ่มทำงาน Tooltips (ถ้ามี) ---
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    });
</script>
@endsection
