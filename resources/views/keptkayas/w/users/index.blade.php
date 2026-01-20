@extends('layouts.keptkaya')

@section('nav-header', 'สมาชิก ธนาคารขยะรีไซเคิล, จัดการถังขยะรายปี')
@section('nav-current', 'ตารางสมาชิก ธนาคารขยะรีไซเคิล, จัดการถังขยะรายปี')
@section('page-topic', 'ตารางสมาชิก จัดการถังขยะรายปี')
@section('nav-keptkayas.users', 'active')

{{-- เพิ่ม CDN SweetAlert2 สำหรับ Popup ยืนยันสวยๆ --}}
@section('styles')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                {{-- HEADER: ปุ่มควบคุมต่างๆ --}}
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h6>ข้อมูลผู้ใช้และประเภทบริการ</h6>
                    <div class="d-flex align-items-center">
                        {{-- 1. ตัวเลือกจำนวนการแสดงผล --}}
                        <form id="perPageForm" action="{{ route('keptkayas.users.index') }}" method="GET" class="d-flex align-items-center me-3">
                            <label for="per_page" class="form-label mb-0 me-2">แสดง:</label>
                            <select name="per_page" id="per_page" class="form-select form-select-sm" onchange="this.form.submit()">
                                @foreach([10, 20, 50, 100] as $option)
                                    <option value="{{ $option }}" {{ $perPage == $option ? 'selected' : '' }}>{{ $option }}</option>
                                @endforeach
                                <option value="all" {{ $perPage == 'all' ? 'selected' : '' }}>ทั้งหมด</option>
                            </select>
                        </form>
                        
                        {{-- 2. ปุ่มบันทึก (อยู่นอก Form แต่สั่ง Submit Form หลักได้ด้วย attribute form="mainBatchForm") --}}
                        <button type="button" id="btnSaveBatch" class="btn bg-gradient-success btn-sm mb-0 me-2">
                            <i class="fas fa-save me-1"></i> บันทึกข้อมูลที่เลือก
                        </button>

                        {{-- 3. ปุ่มเพิ่มผู้ใช้ --}}
                        <a href="{{ route('keptkayas.users.create') }}" class="btn bg-gradient-primary btn-sm mb-0">เพิ่มผู้ใช้งานใหม่</a>
                    </div>
                </div>

                <div class="card-body px-0 pt-0 pb-2">
                    {{-- Alert Messages --}}
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show mx-3" role="alert">
                            <span class="alert-text text-white"><strong>สำเร็จ!</strong> {{ session('success') }}</span>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    {{-- FORM หลัก: ครอบตารางทั้งหมด --}}
                    <form id="mainBatchForm" action="{{ route('keptkayas.updateWasteServicePreferences') }}" method="POST">
                        @csrf
                        
                        <div class="table-responsive p-2">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ชื่อผู้ใช้</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">อีเมล</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">สถานะ</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ประเภทบริการ</th>
                                        <th class="text-secondary opacity-7">การจัดการ</th>
                                    </tr>
                                    {{-- Search Row --}}
                                    <tr class="bg-gray-100">
                                        <th class="p-1">
                                            <input type="text" name="search_name" id="search_name" class="form-control form-control-sm" placeholder="ค้นหาชื่อ..." value="{{ request('search_name') }}">
                                        </th>
                                        <th class="p-1">
                                            <input type="text" name="search_email" id="search_email" class="form-control form-control-sm" placeholder="ค้นหาอีเมล" value="{{ request('search_email') }}">
                                        </th>
                                        <th class="p-1">
                                            <select name="search_status" id="search_status" class="form-select form-select-sm">
                                                <option value="any" {{ request('search_status') == 'any' ? 'selected' : '' }}>ทั้งหมด</option>
                                                <option value="active" {{ request('search_status') == 'active' ? 'selected' : '' }}>Active</option>
                                                <option value="inactive" {{ request('search_status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                            </select>
                                        </th>
                                        <th class="p-1">
                                            <div class="d-flex flex-column">
                                                <div class="form-check form-check-inline mb-1">
                                                    <input class="form-check-input" type="checkbox" id="selectAllAnnualCollection">
                                                    <label class="form-check-label" for="selectAllAnnualCollection">เก็บรายปี (ทั้งหมด)</label>
                                                </div>
                                                <select name="search_is_annual_collection" id="search_is_annual_collection" class="form-select form-select-sm mb-1">
                                                    <option value="any" {{ request('search_is_annual_collection') == 'any' ? 'selected' : '' }}>เก็บรายปี: ทั้งหมด</option>
                                                    <option value="true" {{ request('search_is_annual_collection') == 'true' ? 'selected' : '' }}>เก็บรายปี: ใช่</option>
                                                    <option value="false" {{ request('search_is_annual_collection') == 'false' ? 'selected' : '' }}>เก็บรายปี: ไม่</option>
                                                </select>
                                                
                                                <div class="form-check form-check-inline mb-1">
                                                    <input class="form-check-input" type="checkbox" id="selectAllWasteBank">
                                                    <label class="form-check-label" for="selectAllWasteBank">ธนาคารขยะ (ทั้งหมด)</label>
                                                </div>
                                                <select name="search_is_waste_bank" id="search_is_waste_bank" class="form-select form-select-sm">
                                                    <option value="any" {{ request('search_is_waste_bank') == 'any' ? 'selected' : '' }}>ธนาคารขยะ: ทั้งหมด</option>
                                                    <option value="true" {{ request('search_is_waste_bank') == 'true' ? 'selected' : '' }}>ธนาคารขยะ: ใช่</option>
                                                    <option value="false" {{ request('search_is_waste_bank') == 'false' ? 'selected' : '' }}>ธนาคารขยะ: ไม่</option>
                                                </select>
                                            </div>
                                        </th>
                                        <th class="p-1 text-center">
                                            <button type="button" id="applySearchBtn" class="btn btn-primary btn-sm mb-0">ค้นหา</button>
                                        </th>
                                    </tr>
                                </thead>
                                
                                <tbody id="userTableBody">
                                    @forelse($users as $user)
                                    <tr>
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">{{ $user->prefix }} {{ $user->firstname }} {{ $user->lastname }}</h6>
                                                    <p class="text-xs text-secondary mb-0">{{ $user->username }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">{{ $user->email }}</p>
                                        </td>
                                        <td class="align-middle text-center text-sm">
                                            <span class="badge badge-sm bg-gradient-{{ $user->status == 'active' ? 'success' : 'secondary' }}">{{ ucfirst($user->status) }}</span>
                                        </td>
                                        <td class="align-middle text-center">
                                            @php
                                                // ตรวจสอบข้อมูล
                                                $hasBins = $user->wasteBins && $user->wasteBins->count() > 0;
                                                $isAnnual = optional($user->wastePreference)->is_annual_collection;
                                                $isBank = optional($user->wastePreference)->is_waste_bank;
                                            @endphp

                                            {{-- ============================ --}}
                                            {{-- CHECKBOX: เก็บขยะรายปี --}}
                                            {{-- ============================ --}}
                                            @if($hasBins)
                                                {{-- CASE: มีถังขยะ -> ล็อคห้ามแก้ไข และส่งค่า 1 เสมอ --}}
                                                <input type="hidden" name="waste[{{$user->id}}][is_annual_collection]" value="1">
                                            @else
                                                {{-- CASE: ไม่มีถังขยะ -> ส่งค่า 0 เป็น Default (ถ้าติ๊ก Checkbox จะเป็น 1) --}}
                                                <input type="hidden" name="waste[{{$user->id}}][is_annual_collection]" value="0">
                                            @endif

                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input annual-coll-checkbox" type="checkbox" 
                                                    id="annual_coll_{{ $user->id }}" 
                                                    name="waste[{{$user->id}}][is_annual_collection]" 
                                                    value="1"
                                                    {{ $isAnnual ? 'checked' : '' }}
                                                    {{ $hasBins ? 'disabled' : '' }} 
                                                >
                                                <label class="form-check-label" for="annual_coll_{{ $user->id }}" 
                                                       @if($hasBins) data-bs-toggle="tooltip" title="ไม่สามารถยกเลิกได้ เนื่องจากมีถังขยะในระบบ" @endif>
                                                    เก็บรายปี
                                                    @if($hasBins) <i class="fas fa-lock text-xs text-warning ms-1"></i> @endif
                                                </label>
                                            </div>

                                            {{-- ============================ --}}
                                            {{-- CHECKBOX: ธนาคารขยะ --}}
                                            {{-- ============================ --}}
                                            <input type="hidden" name="waste[{{$user->id}}][is_waste_bank]" value="0">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input waste-bank-checkbox" type="checkbox" 
                                                    id="waste_bank_{{ $user->id }}" 
                                                    name="waste[{{$user->id}}][is_waste_bank]" 
                                                    value="1"
                                                    {{ $isBank ? 'checked' : '' }}
                                                >
                                                <label class="form-check-label" for="waste_bank_{{ $user->id }}">ธนาคารขยะ</label>
                                            </div>
                                        </td>
                                        <td class="align-middle">
                                            @if($isAnnual)
                                                <a href="{{ route('keptkayas.waste_bins.index', $user->id) }}" class="btn btn-link text-info text-gradient px-0 mb-0 me-2">
                                                    <i class="fas fa-trash-alt me-1"></i> จัดการถังขยะ
                                                    <div>{{ $user->wasteBins ? $user->wasteBins->count() : 0 }} ถัง</div>
                                                </a>
                                            @endif
                                            <a href="{{ route('keptkayas.users.edit', $user->id) }}" class="btn btn-link text-secondary font-weight-bold text-xs px-0 mb-0 me-2">
                                                <i class="fas fa-edit me-1"></i> แก้ไข
                                            </a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4">ไม่พบข้อมูลผู้ใช้งาน</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </form>

                    <div class="d-flex justify-content-center mt-3">
                        @if ($users instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator)
                            {{ $users->appends(request()->query())->links('pagination::bootstrap-5') }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
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
@endsection