@extends('layouts.super-admin')

@section('style')
    {{-- แนะนำให้ใช้ SweetAlert2 เพื่อ UX ที่ดีกว่า --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection

@section('mainheader')
    ตั้งค่าขนาดและประเภทมิเตอร์
@endsection

@section('nav')
    <a href="{{ url('tabwatermeter') }}"> ขนาดมิเตอร์</a>
@endsection

@section('nav-metertype', 'active')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-small mb-4">
                <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="m-0">รายการประเภทมิเตอร์</h6>
                    <a href="{{ route('admin.metertype.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> เพิ่มข้อมูล
                    </a>
                </div>

                @if (session('success'))
                    <div class="alert alert-success m-3 text-white" role="alert">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="card-body px-0 pt-0 pb-2">
                    @if ($metertypes->isEmpty())
                        <div class="text-center p-5">
                            <h4 class="text-muted">ยังไม่มีข้อมูลขนาดมิเตอร์</h4>
                            <p class="text-sm">กรุณากดปุ่ม "เพิ่มข้อมูล" เพื่อเริ่มใช้งาน</p>
                        </div>
                    @else
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ประเภทมิเตอร์</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ขนาด (นิ้ว)</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">หมายเหตุ</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">วันที่บันทึก</th>
                                        <th class="text-secondary opacity-7"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($metertypes as $item)
                                        <tr>
                                            <td>
                                                <div class="d-flex px-2 py-1">
                                                    <div>
                                                        <div class="avatar avatar-sm rounded-circle bg-gradient-info me-3 d-flex align-items-center justify-content-center">
                                                            <i class="fas fa-tint text-white"></i>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm">{{ $item->meter_type_name }}</h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="align-middle text-center text-sm">
                                                <span class="badge badge-sm bg-gradient-secondary">{{ $item->metersize }}"</span>
                                            </td>
                                            <td class="align-middle text-sm">
                                                <span class="text-secondary text-xs font-weight-bold">{{ $item->description ?? '-' }}</span>
                                            </td>
                                            <td class="align-middle text-center">
                                                <span class="text-secondary text-xs font-weight-bold">
                                                    {{ $item->created_at ? $item->created_at->format('d/m/Y') : '-' }}
                                                </span>
                                            </td>
                                            <td class="align-middle text-end pe-4">
                                                <div class="dropdown">
                                                    <a href="javascript:;" class="text-secondary font-weight-bold text-xs" data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="fas fa-ellipsis-v text-xs"></i>
                                                    </a>
                                                    <ul class="dropdown-menu dropdown-menu-end px-2 py-3" aria-labelledby="dropdownTable2">
                                                        <li>
                                                            <a class="dropdown-item border-radius-md" href="{{ route('admin.metertype.edit', $item->id) }}">
                                                                <i class="fas fa-edit text-warning me-2"></i> แก้ไขข้อมูล
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item border-radius-md text-danger" href="javascript:;" onclick="confirmDelete('{{ $item->id }}')">
                                                                <i class="fas fa-trash text-danger me-2"></i> ลบข้อมูล
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                                
                                                {{-- Hidden Delete Form --}}
                                                <form id="delete-form-{{ $item->id }}" action="{{ route('admin.metertype.destroy', $item->id) }}" method="POST" style="display: none;">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        // ใช้ SweetAlert2 เพื่อ UX ที่ดีกว่า
        function confirmDelete(id) {
            Swal.fire({
                title: 'กำลังตรวจสอบข้อมูล...',
                text: 'กรุณารอสักครู่',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading()
                }
            });

            // 1. ตรวจสอบก่อนว่าลบได้ไหม (Check dependencies via API)
            $.get('/api/tabwatermeter/checkTabwatermeterMatchedUserMeterInfos/' + id)
                .done(function(data) {
                    Swal.close(); // ปิด Loading

                    if (data == 0) {
                        // 2. ถ้าลบได้ ให้ถามยืนยันอีกครั้ง
                        Swal.fire({
                            title: 'ยืนยันการลบ?',
                            text: "ข้อมูลนี้จะถูกลบถาวรและไม่สามารถกู้คืนได้",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#3085d6',
                            confirmButtonText: 'ลบข้อมูล',
                            cancelButtonText: 'ยกเลิก'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // 3. Submit Form (Method DELETE)
                                document.getElementById('delete-form-' + id).submit();
                            }
                        })
                    } else {
                        // ถ้าลบไม่ได้ แจ้งเตือน
                        Swal.fire({
                            icon: 'error',
                            title: 'ไม่สามารถลบได้',
                            text: 'มีการใช้งานประเภทมิเตอร์นี้อยู่ในระบบ (มีผู้ใช้น้ำผูกอยู่)',
                            confirmButtonText: 'เข้าใจแล้ว'
                        })
                    }
                })
                .fail(function() {
                    Swal.fire('Error', 'เกิดข้อผิดพลาดในการตรวจสอบข้อมูล', 'error');
                });
        }
    </script>
@endsection