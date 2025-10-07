@extends('layouts.keptkaya')

@section('mainheader')
    ตารางขนาดมิเตอร์
@endsection
@section('nav-header')
    <a href="{{ url('tabwatermeter') }}"> ขนาดมิเตอร์</a>
@endsection
@section('nav-usergroup')
    active
@endsection
@section('content')

    {{-- @if (collect($usergroups)->isEmpty())
        <h4 class="text-center mt-3">ยังไม่มีข้อมูลประเภทผู้ใช้งาน</h4>
    @else --}}
        <div class="card card-outline card-danger">
            <div class="card-header">
                <h3 class="card-title"></h3>
                <div class="card-tools">
                    <a href="{{ route('keptkayas.kp_usergroup.create') }}" class="btn btn-primary fa-pull-right">เพิ่มข้อมูล</a>

                </div>
            </div>
            <div class="card-body pb-3 text-center">
                <?php    $order = 1; ?>

                <div class="card">
                    <div class="table-responsive">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>ชื่อกลุ่มผู้ใช้งาน</th>
                                    <th>สถานะ</th>
                                    <th class=""></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($usergroups as $usergroup)
                                    <tr>
                                        <td>{{ $usergroup->id }}</td>
                                        <td>
                                            <div class="d-flex px-2">
                                                <div>

                                                </div>
                                                <div class="my-auto">
                                                    <h6 class="mb-0">{{ $usergroup->usergroup_name }}</h6>
                                                </div>
                                            </div>
                                        </td>


                                        <td>
                                            <span class="badge badge-success">
                                                @if ($usergroup->deleted == '0' && $usergroup->status == 'active')
                                                    มีการใช้งานอยู่
                                                @endif
                                            </span>
                                        </td>

                                        <td class="align-middle">
                                            <ul class="navbar-nav">
                                                <li class="nav-item dropdown">
                                                    <a class="nav-link" data-toggle="dropdown" href="#" aria-expanded="false">
                                                        <i class="far fa-comments"></i>
                                                    </a>
                                                    <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right"
                                                        style="left: inherit; right: 0px;">

                                                        <ul class="navbar-nav">
                                                            <li><a class="dropdown-item text-center"
                                                                    href="{{ route('keptkayas.kp_usergroup.edit', $usergroup) }}">แก้ไขข้อมูล</a>
                                                            </li>
                                                            <li>
                                                                @if ($usergroup->deleted == 0)
                                                                    <form
                                                                        action="{{ route('keptkayas.kp_usergroup.destroy', $usergroup) }}"
                                                                        method="Post">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <a class="dropdown-item text-center test"
                                                                            href="javascript:test()">ลบขัอมูล</a>
                                                                    </form>
                                                                @endif

                                                            </li>
                                                        </ul>
                                                    </div>
                                                </li>
                                            </ul>

                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    {{-- @endif --}}

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