@extends('layouts.super-admin') 

@section('style') 
{{-- แนะนำให้ใช้ SweetAlert2 เพื่อ UX ที่ดีกว่า --}} 
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> 
<style>
    /* ปรับแต่งเฉพาะหน้า Metertype ให้เป็น Flat UI สะอาดตา */
    .flat-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        box-shadow: none !important;
    }
    .flat-card-header {
        background-color: #ffffff;
        border-bottom: 1px solid #e2e8f0;
        padding: 1.25rem 1.5rem;
        border-radius: 8px 8px 0 0 !important;
    }
    .flat-table th {
        background-color: #f8fafc !important;
        color: #64748b !important;
        font-weight: 600 !important;
        text-transform: uppercase;
        font-size: 0.7rem;
        letter-spacing: 0.5px;
        border-bottom: 1px solid #e2e8f0 !important;
        border-top: none !important;
    }
    .flat-table td {
        vertical-align: middle;
        color: #334155;
        border-bottom: 1px solid #f1f5f9 !important;
        padding: 1rem 1.5rem !important;
    }
    .flat-btn-primary {
        background-color: #334155 !important;
        border-color: #334155 !important;
        color: #ffffff !important;
        box-shadow: none !important;
        border-radius: 6px;
        font-weight: 500;
        padding: 0.5rem 1rem;
        transition: background 0.2s;
    }
    .flat-btn-primary:hover {
        background-color: #1e293b !important;
    }
    .flat-icon-box {
        width: 36px;
        height: 36px;
        background-color: #f1f5f9;
        color: #475569;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.9rem;
    }
    .flat-badge {
        background-color: #f1f5f9 !important;
        color: #475569 !important;
        font-weight: 600;
        border-radius: 4px;
        padding: 0.35rem 0.65rem;
        font-size: 0.75rem;
    }
    .flat-dropdown-menu {
        border: 1px solid #e2e8f0 !important;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05) !important;
        border-radius: 6px !important;
        padding: 0.5rem !important;
    }
    .flat-dropdown-item {
        border-radius: 4px;
        padding: 0.4rem 0.8rem;
        font-size: 0.85rem;
        transition: background 0.15s;
    }
    .flat-dropdown-item:hover {
        background-color: #f8fafc;
    }
</style>
@endsection 

@section('nav-main') 
<a href="{{ url('tabwatermeter') }}">ขนาดมิเตอร์</a> 
@endsection 

@section('nav-current') 
ประเภทมิเตอร์ 
@endsection 

@section('nav-current-title') 
ตั้งค่าขนาดและประเภทมิเตอร์ 
@endsection 

@section('content') 
<div class="row"> 
    <div class="col-12"> 
        <div class="card flat-card mb-4"> 
            <div class="flat-card-header d-flex justify-content-between align-items-center"> 
                <h6 class="m-0 font-weight-bold text-dark" style="font-size: 1rem;">รายการประเภทมิเตอร์</h6> 
                <a href="{{ route('admin.metertype.create') }}" class="btn flat-btn-primary btn-sm mb-0"> 
                    <i class="fas fa-plus me-1"></i> เพิ่มข้อมูล 
                </a> 
            </div> 

         

            <div class="card-body px-0 pt-0 pb-2"> 
                @if ($metertypes->isEmpty()) 
                <div class="text-center p-5"> 
                    <div class="mb-3 text-muted" style="font-size: 2.5rem;">
                        <i class="fas fa-tint"></i>
                    </div>
                    <h5 class="text-dark font-weight-bold">ยังไม่มีข้อมูลขนาดมิเตอร์</h5> 
                    <p class="text-sm text-muted">กรุณากดปุ่ม "เพิ่มข้อมูล" ด้านบนเพื่อเริ่มใช้งาน</p> 
                </div> 
                @else 
                <div class="table-responsive p-0"> 
                    <table class="table flat-table align-items-center mb-0"> 
                        <thead> 
                            <tr> 
                                <th>ประเภทมิเตอร์</th> 
                                <th class="text-center">ขนาด (นิ้ว)</th> 
                                <th>หมายเหตุ</th> 
                                <th class="text-center">วันที่บันทึก</th> 
                                <th class="text-secondary opacity-7"></th> 
                            </tr> 
                        </thead> 
                        <tbody> 
                            @foreach ($metertypes as $item) 
                            <tr> 
                                <td> 
                                    <div class="d-flex align-items-center px-1"> 
                                        <div class="flat-icon-box me-3"> 
                                            <i class="fas fa-tint text-secondary"></i> 
                                        </div> 
                                        <div> 
                                            <h6 class="mb-0 text-sm font-weight-bold text-dark">{{ $item->meter_type_name }}</h6> 
                                        </div> 
                                    </div> 
                                </td> 
                                <td class="align-middle text-center"> 
                                    <span class="badge flat-badge">{{ $item->metersize }}"</span> 
                                </td> 
                                <td class="align-middle"> 
                                    <span class="text-secondary text-xs">{{ $item->description ?? '-' }}</span> 
                                </td> 
                                <td class="align-middle text-center"> 
                                    <span class="text-secondary text-xs font-weight-normal"> 
                                        {{ $item->created_at ? $item->created_at->format('d/m/Y') : '-' }} 
                                    </span> 
                                </td> 
                                <td class="align-middle text-end pe-4"> 
                                    <div class="dropdown"> 
                                        <a href="javascript:;" class="text-secondary px-2 py-1" data-bs-toggle="dropdown" aria-expanded="false"> 
                                            <i class="fas fa-ellipsis-v text-xs"></i> 
                                        </a> 
                                        <ul class="dropdown-menu flat-dropdown-menu dropdown-menu-end" aria-labelledby="dropdownTable2"> 
                                            <li> 
                                                <a class="dropdown-item flat-dropdown-item text-dark" href="{{ route('admin.metertype.edit', $item->id) }}"> 
                                                    <i class="fas fa-edit text-warning me-2"></i> แก้ไขข้อมูล 
                                                </a> 
                                            </li> 
                                            <li> 
                                                <a class="dropdown-item flat-dropdown-item text-danger" href="javascript:;" onclick="confirmDelete('{{ $item->id }}')"> 
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
    // ใช้ SweetAlert2 เช็คก่อนลบ
    function confirmDelete(id) { 
        Swal.fire({ 
            title: 'กำลังตรวจสอบข้อมูล...', 
            text: 'กรุณารอสักครู่', 
            allowOutsideClick: false, 
            didOpen: () => { 
                Swal.showLoading() 
            } 
        }); 

        $.get('/api/tabwatermeter/checkTabwatermeterMatchedUserMeterInfos/' + id) 
        .done(function(data) { 
            Swal.close(); 
            if (data == 0) { 
                Swal.fire({ 
                    title: 'ยืนยันการลบ?', 
                    text: "ข้อมูลนี้จะถูกลบถาวรและไม่สามารถกู้คืนได้", 
                    icon: 'warning', 
                    showCancelButton: true, 
                    confirmButtonColor: '#334155', 
                    cancelButtonColor: '#94a3b8', 
                    confirmButtonText: 'ลบข้อมูล', 
                    cancelButtonText: 'ยกเลิก' 
                }).then((result) => { 
                    if (result.isConfirmed) { 
                        document.getElementById('delete-form-' + id).submit(); 
                    } 
                }) 
            } else { 
                Swal.fire({ 
                    icon: 'error', 
                    title: 'ไม่สามารถลบได้', 
                    text: 'มีการใช้งานประเภทมิเตอร์นี้อยู่ในระบบ (มีผู้ใช้น้ำผูกอยู่)', 
                    confirmButtonText: 'เข้าใจแล้ว',
                    confirmButtonColor: '#334155'
                }) 
            } 
        }) 
        .fail(function() { 
            Swal.fire('Error', 'เกิดข้อผิดพลาดในการตรวจสอบข้อมูล', 'error'); 
        }); 
    } 
</script> 
@endsection