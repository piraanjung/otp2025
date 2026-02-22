@extends('layouts.foodwaste')

@section('title_page', 'ผู้ใช้งาน')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h6>ข้อมูลผู้ใช้และประเภทบริการ</h6>
                    <div class="d-flex align-items-center">
                        <form id="perPageForm" action="{{ route('keptkayas.users.index') }}" method="GET" class="d-flex align-items-center me-3">
                            <label for="per_page" class="form-label mb-0 me-2">แสดง:</label>
                            <select name="per_page" id="per_page" class="form-select form-select-sm" onchange="this.form.submit()">
                                @foreach([10, 20, 50, 100] as $option)
                                    <option value="{{ $option }}" {{ $perPage == $option ? 'selected' : '' }}>{{ $option }}</option>
                                @endforeach
                                <option value="all" {{ $perPage == 'all' ? 'selected' : '' }}>ทั้งหมด</option>
                            </select>
                        </form>
                        <a href="{{ route('keptkayas.users.create') }}" class="btn bg-gradient-primary btn-sm mb-0">เพิ่มผู้ใช้งานใหม่</a>
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
                            <span class="alert-text text-white"><strong>เกิดข้อผิดพลาด!</strong> โปรดตรวจสอบข้อมูลอีกครั้ง</span>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ชื่อผู้ใช้</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">อีเมล</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">สถานะ</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ประเภทบริการ</th>
                                    <th class="text-secondary opacity-7">การจัดการ</th>
                                </tr>
                                {{-- Search row --}}
                                <tr class="bg-gray-100">
                                    <th class="p-1">
                                        <input type="text" name="search_name" id="search_name" class="form-control form-control-sm" placeholder="ค้นหาชื่อผู้ใช้" value="{{ request('search_name') }}">
                                    </th>
                                    <th class="p-1">
                                        <input type="text" name="search_email" id="search_email" class="form-control form-control-sm" placeholder="ค้นหาอีเมล" value="{{ request('search_email') }}">
                                    </th>
                                    <th class="p-1">
                                        <select name="search_status" id="search_status" class="form-select form-select-sm">
                                            <option value="any" {{ request('search_status') == 'any' ? 'selected' : '' }}>ทั้งหมด</option>
                                            <option value="active" {{ request('search_status') == 'active' ? 'selected' : '' }}>Active</option>
                                            <option value="inactive" {{ request('search_status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                            <option value="banned" {{ request('search_status') == 'banned' ? 'selected' : '' }}>Banned</option>
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
                                @php
                                    $i=0;
                                @endphp
                                 <form action="{{ route('keptkayas.users.updateWasteServicePreferences') }}" method="POST">
                                            @csrf

                                    <input type="submit" class="btn btn-primary" value="บันทึกข้อมูล">
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
                                       
                                            {{-- @method('PATCH') --}}
                                            <input type="hidden" name="is_annual_collection" value="0">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input annual-coll-checkbox" type="checkbox" id="annual_coll_{{ $user->id }}" name="waste[{{$user->id}}][is_annual_collection]" value="1"
                                                    {{ optional($user->wastePreference)->is_annual_collection ? 'checked' : '' }}
                                                    {{-- onchange="this.form.submit()" --}}
                                                    >
                                                <label class="form-check-label" for="annual_coll_{{ $user->id }}">เก็บขยะรายปี</label>
                                            </div>
                                            <input type="hidden" name="is_waste_bank" value="0">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input waste-bank-checkbox" type="checkbox" id="waste_bank_{{ $user->id }}" name="waste[{{$user->id}}][is_waste_bank]" value="1"
                                                    {{ optional($user->wastePreference)->is_waste_bank ? 'checked' : '' }}
                                                    {{-- onchange="this.form.submit()"--}}
                                                    
                                                <label class="form-check-label" for="waste_bank_{{ $user->id }}">ธนาคารขยะ</label>
                                            </div>
                                    </td>
                                    <td class="align-middle">
                                        
                                        @if(optional($user->wastePreference)->is_annual_collection)
                                            <a href="{{ route('keptkayas.waste_bins.index', $user->id) }}" class="btn btn-link text-info text-gradient px-0 mb-0 me-2">
                                                <i class="fas fa-trash-alt me-1"></i> จัดการถังขยะ
                                                <div>{{collect($user->wasteBins)->count() }} ถัง</div>

                                            </a>
                                        @endif
                                        <a href="{{ route('keptkayas.users.edit', $user->id) }}" class="btn btn-link text-secondary font-weight-bold text-xs px-0 mb-0 me-2">
                                            <i class="fas fa-edit me-1"></i> แก้ไข
                                            <div>&nbsp;</div>
                                        </a>
                                        {{-- <form action="{{ route('keptkayas.users.destroy', $user->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-link text-danger text-gradient px-0 mb-0" onclick="return confirm('คุณแน่ใจหรือไม่ที่จะลบผู้ใช้งานนี้?')">
                                                <i class="fas fa-trash-alt me-1"></i> ลบ
                                            </button>
                                        </form> --}}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">ไม่มีผู้ใช้งานในระบบ</td>
                                </tr>
                                @endforelse
                                                                        </form>

                            </tbody>
                        </table>
                        <div class="d-flex justify-content-center mt-3">
                            @if ($users instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator)
                                {{ $users->appends(request()->query())->links('pagination::bootstrap-5') }}
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Define elements to find and their corresponding variable names
        const elementsToFind = [
            { id: 'search_name', varName: 'searchNameInput' },
            { id: 'search_email', varName: 'searchEmailInput' },
            { id: 'search_status', varName: 'searchStatusSelect' },
            { id: 'search_is_annual_collection', varName: 'searchAnnualCollectionSelect' },
            { id: 'search_is_waste_bank', varName: 'searchWasteBankSelect' },
            { id: 'applySearchBtn', varName: 'applySearchBtn' },
            { id: 'userTableBody', varName: 'userTableBody' },
            { id: 'selectAllAnnualCollection', varName: 'selectAllAnnualCollectionCheckbox' }, // NEW
            { id: 'selectAllWasteBank', varName: 'selectAllWasteBankCheckbox' },             // NEW
            { id: 'per_page', varName: 'perPageSelect' }                                     // NEW: Added per_page select
        ];

        let allElementsFound = true;
        const foundElements = {};

        // Attempt to find each element and log an error if not found
        elementsToFind.forEach(el => {
            const element = document.getElementById(el.id);
            if (!element) {
                console.error(`Error: Element with ID '${el.id}' (${el.varName}) not found in the DOM. Cannot attach event listener.`);
                allElementsFound = false;
            }
            foundElements[el.varName] = element;
        });

        // If critical elements are missing, log a general error and stop
        if (!allElementsFound) {
            console.error("Live search functionality might be impaired due to missing elements.");
            return;
        }

        // Assign found elements to const variables for clarity and use
        const searchNameInput = foundElements.searchNameInput;
        const searchEmailInput = foundElements.searchEmailInput;
        const searchStatusSelect = foundElements.searchStatusSelect;
        const searchAnnualCollectionSelect = foundElements.searchAnnualCollectionSelect;
        const searchWasteBankSelect = foundElements.searchWasteBankSelect;
        const applySearchBtn = foundElements.applySearchBtn;
        const userTableBody = foundElements.userTableBody;
        const selectAllAnnualCollectionCheckbox = foundElements.selectAllAnnualCollectionCheckbox; // NEW
        const selectAllWasteBankCheckbox = foundElements.selectAllWasteBankCheckbox;             // NEW
        const perPageSelect = foundElements.perPageSelect;                                       // NEW: Added per_page select

        let searchTimeout;
        const debounceDelay = 300; // milliseconds

        function applyLiveSearch() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                const queryParams = new URLSearchParams();
                queryParams.append('search_name', searchNameInput.value);
                queryParams.append('search_email', searchEmailInput.value);
                queryParams.append('search_status', searchStatusSelect.value);
                queryParams.append('search_is_annual_collection', searchAnnualCollectionSelect.value);
                queryParams.append('search_is_waste_bank', searchWasteBankSelect.value);
                queryParams.append('per_page', perPageSelect.value); // Use perPageSelect.value

                queryParams.append('ajax', '1'); // Flag for AJAX request

                fetch(`{{ route('keptkayas.users.index') }}?${queryParams.toString()}`, {
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
                    userTableBody.innerHTML = html;
                })
                .catch(error => console.error('Error during live search:', error));
            }, debounceDelay);
        }

        // Attach event listeners for live search
        searchNameInput.addEventListener('keyup', applyLiveSearch);
        searchEmailInput.addEventListener('keyup', applyLiveSearch);
        searchStatusSelect.addEventListener('change', applyLiveSearch);
        searchAnnualCollectionSelect.addEventListener('change', applyLiveSearch);
        searchWasteBankSelect.addEventListener('change', applyLiveSearch);
        perPageSelect.addEventListener('change', applyLiveSearch); // Attach listener to per_page select
        
        // Event listener for the "ค้นหา" button
        applySearchBtn.addEventListener('click', applyLiveSearch);

        // NEW: Select All Checkboxes Logic
        selectAllAnnualCollectionCheckbox.addEventListener('change', function() {
            document.querySelectorAll('.annual-coll-checkbox').forEach(checkbox => {
                checkbox.checked = this.checked;
                // Trigger form submission for each checkbox if it's not already handled by onchange
                // This will cause multiple submissions, as noted in the explanation above.
                // if (checkbox.checked !== checkbox.defaultChecked) { // Only submit if state actually changed
                //     checkbox.form.submit();
                // }
            });
        });

        selectAllWasteBankCheckbox.addEventListener('change', function() {
            document.querySelectorAll('.waste-bank-checkbox').forEach(checkbox => {
                checkbox.checked = this.checked;
                // Trigger form submission for each checkbox if it's not already handled by onchange
                // This will cause multiple submissions, as noted in the explanation above.
                // if (checkbox.checked !== checkbox.defaultChecked) { // Only submit if state actually changed
                //     checkbox.form.submit();
                // }
            });
        });
    });
</script>
@endsection
