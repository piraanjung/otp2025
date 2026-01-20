@extends('layouts.keptkaya')

{{-- ปรับข้อความ Header ให้ตรงกับเนื้อหา --}}
@section('mainheader', 'จัดการกลุ่มผู้ใช้งาน')
@section('nav-header', 'ตั้งค่าระบบ')
@section('nav-current', 'กลุ่มผู้ใช้งาน')
@section('page-topic', 'ตารางกลุ่มผู้ใช้งาน')
@section('nav-usergroup', 'active')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                {{-- Card Header: จัดปุ่มเพิ่มให้อยู่ขวาด้วย Flexbox --}}
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h6>รายการกลุ่มผู้ใช้งาน</h6>
                    <a href="{{ route('keptkayas.kp_usergroup.create') }}" class="btn bg-gradient-primary btn-sm mb-0">
                        <i class="fas fa-plus me-2"></i> เพิ่มข้อมูล
                    </a>
                </div>

                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ID</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">ชื่อกลุ่มผู้ใช้งาน</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">สถานะ</th>
                                    <th class="text-secondary opacity-7">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($usergroups as $usergroup)
                                    <tr>
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm text-secondary">{{ $usergroup->id }}</h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <p class="text-sm font-weight-bold mb-0">{{ $usergroup->usergroup_name }}</p>
                                        </td>
                                        <td class="align-middle text-center text-sm">
                                            @if ($usergroup->deleted == '0' && $usergroup->status == 'active')
                                                <span class="badge badge-sm bg-gradient-success">ใช้งานอยู่</span>
                                            @else
                                                <span class="badge badge-sm bg-gradient-secondary">ไม่ใช้งาน</span>
                                            @endif
                                        </td>
                                        <td class="align-middle">
                                            <a href="{{ route('keptkayas.kp_usergroup.edit', $usergroup) }}" 
                                               class="text-secondary font-weight-bold text-xs me-3" 
                                               data-toggle="tooltip" 
                                               data-original-title="Edit user">
                                                <i class="fas fa-edit"></i> แก้ไข
                                            </a>

                                            @if ($usergroup->deleted == 0)
                                                <form action="{{ route('keptkayas.kp_usergroup.destroy', $usergroup) }}" 
                                                      method="POST" 
                                                      class="d-inline delete-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <a href="javascript:void(0);" 
                                                       class="text-danger font-weight-bold text-xs btn-delete"
                                                       data-toggle="tooltip" 
                                                       data-original-title="Delete user">
                                                        <i class="fas fa-trash"></i> ลบ
                                                    </a>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center p-4">
                                            <p class="text-secondary mb-0">ไม่พบข้อมูลกลุ่มผู้ใช้งาน</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('script')
    <script>
        $('a.test').on('click', function (e) {
            e.preventDefault();
            let res = window.confirm("ต้องการลบ ?")
            if (res === true) {
                $(this).closest('form').submit();
            }
            return false;
        });

        function deleteData(del_id) {
            var r = confirm("ต้องการลบข้อมูลใช่หรือไม่!");
            if (r == true) {
                //หาว่ามีผูกใน user_meter_infos table ไหม
                $.get('/api/tabwatermeter/checkTabwatermeterMatchedUserMeterInfos/' + del_id)
                    .done(function (data) {
                        if (data == 0) {
                            $.get('/tabwatermeter/' + del_id + '/delete').then((res) => {
                                alert('ทำการลบข้อมูลเรียบร้อย')
                                setTimeout(() => {
                                    window.location.reload()
                                }, 500)

                            })
                        } else {
                            alert(`ไม่สามารถลบได้เนื่องจากมีการใช้ข้อมูลขนาดมิเตอร์นี้`)
                        }
                    })
            }
        }
    </script>
@endsection