@extends('layouts.super-admin') {{-- หรือ layouts.app ตามที่คุณใช้ --}}

@section('title', 'จัดการผู้ดูแลหน่วยงาน (Org Admins)')

@section('content')
<div class="container-fluid">

    <div class="row mb-3">
        <div class="col-md-6">
            <h3 class="m-0 text-dark">
                <i class="fas fa-user-shield"></i> รายชื่อผู้ดูแลประจำหน่วยงาน
            </h3>
        </div>
        <div class="col-md-6 text-right">
            <a href="{{ route('org-admins.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle"></i> เพิ่มผู้ดูแลใหม่
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card shadow">
        <div class="card-header border-0">
            <div class="row align-items-center">
                <div class="col">
                    <h5 class="mb-0">รายการ Admin ทั้งหมด ({{ $admins->total() }} คน)</h5>
                </div>
                {{-- (Optional) ช่องค้นหา --}}
                <div class="col text-right">
                    <form action="{{ route('org-admins.index') }}" method="GET" class="form-inline float-right">
                        <input type="text" name="search" class="form-control form-control-sm mr-2" placeholder="ค้นหาชื่อ หรือ หน่วยงาน..." value="{{ request('search') }}">
                        <button type="submit" class="btn btn-sm btn-outline-primary"><i class="fas fa-search"></i></button>
                    </form>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table align-items-center table-flush table-hover">
                <thead class="thead-light">
                    <tr>
                        <th scope="col" style="width: 5%">#</th>
                        <th scope="col" style="width: 25%">หน่วยงาน (Org)</th>
                        <th scope="col" style="width: 20%">ชื่อ-นามสกุล</th>
                        <th scope="col" style="width: 15%">เบอร์โทร / Email</th>
                        <th scope="col" style="width: 10%" class="text-center">สถานะ</th>
                        <th scope="col" style="width: 15%">สร้างเมื่อ</th>
                        <th scope="col" style="width: 10%" class="text-right">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($admins as $index => $admin)

                    <tr>
                        <td>{{ $admins->firstItem() + $index }}</td>
                        <td>
                            @if($admin->org)
                                {{-- <span class="badge badge-pill badge-info"> --}}
                                    <i class="fas fa-building"></i>
                                    {{ $admin->org->org_type_name }}
                                    {{ $admin->org->org_name }}
                                   <div>
                                   จังหวัด {{ $admin->org->provinces->province_name }}
                                    </div>
                                {{-- </span> --}}
                            @else
                                <span class="text-muted text-sm">ไม่ระบุหน่วยงาน</span>
                            @endif
                        </td>
                        <td>
                            <div class="font-weight-bold">{{ $admin->firstname." ".$admin->lastname }}</div>
                            <small class="text-muted">User: {{ $admin->username }}</small>
                        </td>
                        <td>
                            <div><i class="fas fa-phone-alt text-xs"></i> {{ $admin->phone ?? '-' }}</div>
                            <small class="text-muted"><i class="fas fa-envelope text-xs"></i> {{ $admin->email }}</small>
                        </td>
                        <td class="text-center">
                            @if($admin->status == 'active')
                                <span class="badge badge-success">ใช้งานปกติ</span>
                            @else
                                <span class="badge badge-secondary">ระงับใช้งาน</span>
                            @endif
                        </td>
                        <td>
                            {{ $admin->created_at->format('d/m/Y') }}
                        </td>
                        <td class="text-right">
                            <div class="btn-group">
                                {{-- ปุ่มแก้ไข --}}
                                <a href="{{ route('org-admins.edit', $admin->id) }}" class="btn btn-sm btn-warning" title="แก้ไข">
                                    <i class="fas fa-edit"></i>
                                </a>

                                {{-- ปุ่มลบ (ใช้ Form + SweetAlert) --}}
                                <button type="button" class="btn btn-sm btn-danger delete-confirm"
                                    data-id="{{ $admin->id }}"
                                    data-name="{{ $admin->name }}"
                                    title="ลบข้อมูล">
                                    <i class="fas fa-trash"></i>
                                </button>

                                <form id="delete-form-{{ $admin->id }}" action="{{ route('org-admins.destroy', $admin->id) }}" method="POST" style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="fas fa-folder-open fa-3x mb-3"></i><br>
                            ยังไม่มีข้อมูลผู้ดูแลระบบ
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer py-4">
            <div class="d-flex justify-content-end">
                {{ $admins->appends(request()->query())->links() }}
                {{-- ใช้ links() ธรรมดา หรือ pagination::bootstrap-4 แล้วแต่ Theme --}}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- ตัวอย่าง Script ใช้ SweetAlert2 สำหรับยืนยันการลบ --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        $('.delete-confirm').on('click', function (event) {
            event.preventDefault();
            const id = $(this).data('id');
            const name = $(this).data('name');

            Swal.fire({
                title: 'คุณแน่ใจหรือไม่?',
                text: "ต้องการลบผู้ดูแล: " + name + " ใช่หรือไม่?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'ใช่, ลบเลย!',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            })
        });
    });
</script>
@endpush
