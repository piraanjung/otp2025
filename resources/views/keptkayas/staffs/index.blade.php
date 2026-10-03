@extends('layouts.super-admin')

@section('title_page', 'จัดการเจ้าหน้าที่')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h6 class="mb-0">ข้อมูลเจ้าหน้าที่</h6>
                        <p class="text-sm text-secondary mb-0">
                            ทั้งหมด {{ number_format($staffs instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator ? $staffs->total() : $staffs->count()) }} คน
                        </p>
                    </div>
                    <a href="{{ route('keptkayas.staffs.create') }}" class="btn bg-gradient-primary btn-sm mb-0">
                        <i class="fas fa-plus me-1"></i> เพิ่มเจ้าหน้าที่ใหม่
                    </a>
                </div>

                <div class="card-body px-0 pt-0 pb-2">
                    {{-- ฟอร์มกรองเป็น GET จริง: กด Enter / เปลี่ยนตัวเลือกแล้วค้นหาได้ และ pagination คงค่าที่กรองไว้ --}}
                    <form id="filterForm" action="{{ url()->current() }}" method="GET"
                        class="row g-2 align-items-end px-4 py-3">
                        <div class="col-12 col-md-5">
                            <label for="search_name" class="form-label text-xs mb-1">ค้นหา</label>
                            <input type="text" name="search_name" id="search_name" class="form-control form-control-sm"
                                placeholder="ชื่อ, username หรืออีเมล" value="{{ request('search_name') }}">
                        </div>
                        <div class="col-6 col-md-3">
                            <label for="search_status" class="form-label text-xs mb-1">สถานะ</label>
                            <select name="search_status" id="search_status" class="form-select form-select-sm">
                                <option value="any">ทั้งหมด</option>
                                <option value="active" @selected(request('search_status') == 'active')>ใช้งาน (Active)</option>
                                <option value="inactive" @selected(request('search_status') == 'inactive')>ไม่ใช้งาน (Inactive)</option>
                            </select>
                        </div>
                        <div class="col-6 col-md-2">
                            <label for="per_page" class="form-label text-xs mb-1">แสดง</label>
                            <select name="per_page" id="per_page" class="form-select form-select-sm">
                                @foreach ([10, 20, 50, 100] as $option)
                                    <option value="{{ $option }}" @selected($perPage == $option)>{{ $option }}</option>
                                @endforeach
                                <option value="all" @selected($perPage == 'all')>ทั้งหมด</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-2 d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-sm mb-0 flex-fill">
                                <i class="fas fa-search me-1"></i> ค้นหา
                            </button>
                            <a href="{{ url()->current() }}" class="btn btn-outline-secondary btn-sm mb-0"
                                title="ล้างตัวกรอง"><i class="fas fa-undo"></i></a>
                        </div>
                    </form>

                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ชื่อเจ้าหน้าที่</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">อีเมล</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">หน้าที่ (Roles)</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">สิทธิ์เข้าถึงโมดูล</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">สถานะ</th>
                                    <th class="text-secondary opacity-7"></th>
                                </tr>
                            </thead>
                            <tbody id="staffTableBody">
                                @include('keptkayas.staffs._table_body')
                            </tbody>
                        </table>

                        @if ($staffs instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator)
                            <div class="d-flex justify-content-center mt-3">
                                {{ $staffs->appends(request()->query())->links('pagination::bootstrap-5') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('filterForm');
            let timer;

            // พิมพ์ค้นหาแล้วส่งฟอร์มอัตโนมัติหลังหยุดพิมพ์ 500 ms
            document.getElementById('search_name').addEventListener('input', function () {
                clearTimeout(timer);
                timer = setTimeout(() => form.submit(), 500);
            });

            ['search_status', 'per_page'].forEach(id =>
                document.getElementById(id).addEventListener('change', () => form.submit())
            );
        });
    </script>
@endsection
