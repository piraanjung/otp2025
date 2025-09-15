@extends('layouts.keptkaya')

@section('title_page', 'จัดการเจ้าหน้าที่')

@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                        <h6>ข้อมูลเจ้าหน้าที่</h6>
                        <div class="d-flex align-items-center">
                            <form id="perPageForm" action="{{ route('keptkayas.staffs.index') }}" method="GET"
                                class="d-flex align-items-center me-3">
                                <label for="per_page" class="form-label mb-0 me-2">แสดง:</label>
                                <select name="per_page" id="per_page" class="form-select form-select-sm"
                                    onchange="this.form.submit()">
                                    @foreach([10, 20, 50, 100] as $option)
                                        <option value="{{ $option }}" {{ $perPage == $option ? 'selected' : '' }}>{{ $option }}
                                        </option>
                                    @endforeach
                                    <option value="all" {{ $perPage == 'all' ? 'selected' : '' }}>ทั้งหมด</option>
                                </select>
                            </form>
                            <a href="{{ route('keptkayas.staffs.create') }}"
                                class="btn bg-gradient-primary btn-sm mb-0">เพิ่มเจ้าหน้าที่ใหม่</a>
                        </div>
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show mx-3" role="alert">
                                <span class="alert-text text-white"><strong>สำเร็จ!</strong> {{ session('success') }}</span>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif
                        @if($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show mx-3" role="alert">
                                <span class="alert-text text-white"><strong>เกิดข้อผิดพลาด!</strong>
                                    โปรดตรวจสอบข้อมูลอีกครั้ง</span>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            ชื่อเจ้าหน้าที่</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            อีเมล</th>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            สถานะ</th>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            สิทธิ์เข้าถึงโมดูล</th>
                                        <th class="text-secondary opacity-7">การจัดการ</th>
                                    </tr>
                                    {{-- Search row --}}
                                    <tr class="bg-gray-100">
                                        <th class="p-1">
                                            <input type="text" name="search_name" id="search_name"
                                                class="form-control form-control-sm" placeholder="ค้นหาชื่อเจ้าหน้าที่"
                                                value="{{ request('search_name') }}">
                                        </th>
                                        <th class="p-1">
                                            {{-- Email search is handled by name search in controller for simplicity --}}
                                        </th>
                                        <th class="p-1">
                                            <select name="search_status" id="search_status"
                                                class="form-select form-select-sm">
                                                <option value="any" {{ request('search_status') == 'any' ? 'selected' : '' }}>
                                                    ทั้งหมด</option>
                                                <option value="active" {{ request('search_status') == 'active' ? 'selected' : '' }}>Active</option>
                                                <option value="inactive" {{ request('search_status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                                <option value="suspended" {{ request('search_status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                                            </select>
                                        </th>
                                        <th class="p-1">
                                            <div class="d-flex flex-column">
                                                <select name="search_can_access_waste_bank"
                                                    id="search_can_access_waste_bank"
                                                    class="form-select form-select-sm mb-1">
                                                    <option value="any" {{ request('search_can_access_waste_bank') == 'any' ? 'selected' : '' }}>ธนาคารขยะ: ทั้งหมด</option>
                                                    <option value="true" {{ request('search_can_access_waste_bank') == 'true' ? 'selected' : '' }}>ธนาคารขยะ: ใช่</option>
                                                    <option value="false" {{ request('search_can_access_waste_bank') == 'false' ? 'selected' : '' }}>ธนาคารขยะ: ไม่</option>
                                                </select>
                                                <select name="search_can_access_annual_collection"
                                                    id="search_can_access_annual_collection"
                                                    class="form-select form-select-sm">
                                                    <option value="any" {{ request('search_can_access_annual_collection') == 'any' ? 'selected' : '' }}>เก็บรายปี: ทั้งหมด</option>
                                                    <option value="true" {{ request('search_can_access_annual_collection') == 'true' ? 'selected' : '' }}>เก็บรายปี: ใช่</option>
                                                    <option value="false" {{ request('search_can_access_annual_collection') == 'false' ? 'selected' : '' }}>เก็บรายปี: ไม่</option>
                                                </select>
                                            </div>
                                        </th>
                                        <th class="p-1 text-center">
                                            <button type="button" id="applySearchBtn"
                                                class="btn btn-primary btn-sm mb-0">ค้นหา</button>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody id="staffTableBody">
                                    @include('keptkayas.staffs._table_body')
                                </tbody>
                            </table>
                            <div class="d-flex justify-content-center mt-3">
                                @if ($staffs instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator)
                                    {{ $staffs->appends(request()->query())->links('pagination::bootstrap-5') }}
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchNameInput = document.getElementById('search_name');
            const searchStatusSelect = document.getElementById('search_status');
            const searchCanAccessWasteBankSelect = document.getElementById('search_can_access_waste_bank');
            const searchCanAccessAnnualCollectionSelect = document.getElementById('search_can_access_annual_collection');
            const applySearchBtn = document.getElementById('applySearchBtn');
            const staffTableBody = document.getElementById('staffTableBody');
            const perPageSelect = document.getElementById('per_page');

            let searchTimeout;
            const debounceDelay = 300; // milliseconds

            function applyLiveSearch() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    const queryParams = new URLSearchParams();
                    queryParams.append('search_name', searchNameInput.value);
                    queryParams.append('search_status', searchStatusSelect.value);
                    queryParams.append('search_can_access_waste_bank', searchCanAccessWasteBankSelect.value);
                    queryParams.append('search_can_access_annual_collection', searchCanAccessAnnualCollectionSelect.value);
                    queryParams.append('per_page', perPageSelect.value);

                    queryParams.append('ajax', '1'); // Flag for AJAX request

                    fetch(`{{ route('keptkayas.staffs.index') }}?${queryParams.toString()}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error(`HTTP error! status: ${response.status}`);
                            }
                            return response.text();
                        })
                        .then(html => {
                            staffTableBody.innerHTML = html;
                        })
                        .catch(error => console.error('Error during live search:', error));
                }, debounceDelay);
            }

            // Attach event listeners for live search
            if (searchNameInput) searchNameInput.addEventListener('keyup', applyLiveSearch);
            if (searchStatusSelect) searchStatusSelect.addEventListener('change', applyLiveSearch);
            if (searchCanAccessWasteBankSelect) searchCanAccessWasteBankSelect.addEventListener('change', applyLiveSearch);
            if (searchCanAccessAnnualCollectionSelect) searchCanAccessAnnualCollectionSelect.addEventListener('change', applyLiveSearch);
            if (perPageSelect) perPageSelect.addEventListener('change', applyLiveSearch);

            // Event listener for the "ค้นหา" button
            if (applySearchBtn) applySearchBtn.addEventListener('click', function () {
                applyLiveSearch();
            });
        });
    </script>
@endsection