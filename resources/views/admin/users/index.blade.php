@extends('layouts.admin1')

@if ($usertype == 'user')
    @section('nav-user')
        active
    @endsection
    @section('nav-main')
    <a href="{{route('admin.users.index')}}"> ผู้ใช้น้ำประปา</a>
    @endsection
@else
    @section('nav-staff')
        active
    @endsection
    @section('nav-main')
    <a href="{{route('admin.users.index')}}"> เจ้าหน้าที่งานประปา</a>
    @endsection
@endif

@section('nav-header')
ผู้ใช้งานระบบ
@endsection
@section('nav-current')
@if ($usertype == 'user')
ข้อมูลผู้ใช้น้ำประปา
@else
ข้อมูลเจ้าหน้าที่งานประปา
@endif
@endsection
@section('page-topic')

@endsection

@section('style')
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <script src="https://cdn.datatables.net/plug-ins/1.13.6/pagination/select.js"></script>
    <style>
        .selected {
            background: lightblue
        }

        .dataTables_length,
        .dt-buttons,
        .dataTables_filter,
        .select_row_all,
        .deselect_row_all,
        .create_user {
            display: inline-flex;
        }

        .dt-buttons,
        .select_row_all,
        .deselect_row_all,
        .create_user {
            flex-direction: column
        }

        .dt-buttons {
            margin-left: 3%
        }

        .dataTables_filter {
            margin-left: 2%
        }
        .preloader-wrapper{
            position:fixed;
            top:0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: #111;
            opacity: 0.8;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1200;
            transition: all .4s ease;
        }

        .fade-out-animation{
            opacity: 0;
            visibility: hidden;
        }
    </style>
@endsection
@section('content')
    <div class="preloader-wrapper">
        <button class="btn btn-primary btn-sm mb-2" type="button" disabled>
            <span class="spinner-border spinner-border-sm" role="status"></span>
            Loading...
        </button>
    </div>
    <div class="row mt-4">
        <div class="col-12 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.users.users_search') }}" method="POST">
                        @csrf
                        <div class="row">
                            <h5>ค้นหาตามพื้นที่</h5>
                            @foreach ($zones as $zone)
                                <div class="col-12 col-lg-6 col-md-6 col-sm-12">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="{{ $zone->id }}"
                                            name="zone[]">
                                        <label class="custom-control-label"
                                            for="customCheck1">{{ $zone->zone_name }}</label>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <button type="submit" class="btn btn-info btn-sm text-end">ค้นหา</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-9">
            <div class="card">

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" style="border-collapse: collapse" id="example">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        เลขผู้ใช้น้ำ</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                        user_id</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                        ชื่อ-สกุล</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                        วันที่ลงทะเบียน
                                    </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                        ที่อยู่</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                        หมู่</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                        หมายเหตุ</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $user)
                                    <tr>
                                        <td>
                                            <div class="d-flex px-2">

                                                <div class="my-auto">

                                                    <h6 class="mb-0 text-xs">
                                                        <ul>
                                                            @foreach ($user->usermeterinfos as $item)
                                                                <li>{{ $item->meternumber }}</li>
                                                            @endforeach
                                                        </ul>
                                                    </h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">
                                                {{ $user->id  }}</p>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">
                                                {{ $user->firstname . ' ' . $user->lastname }}</p>
                                        </td>
                                        <td>
                                            <span class="badge badge-dot me-4">
                                                <i class="bg-info"></i>
                                                <span
                                                    class="text-dark text-xs">{{ date_format($user->created_at, 'd-m-') . (date_format($user->created_at, 'Y') + 543) }}</span>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-dot me-4">
                                                <i class="bg-info"></i>
                                                <span class="text-dark text-xs">{{ $user->address }}</span>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-dot me-4">
                                                <i class="bg-info"></i>
                                                <span
                                                    class="text-dark text-xs">{{ str_replace(' ', '', $user->user_zone->zone_name) }}</span>
                                            </span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <div class="d-flex align-items-center">
                                                <span class="me-2 text-xs">60%</span>
                                                <div>
                                                    <div class="progress">
                                                        <div class="progress-bar bg-info" role="progressbar"
                                                            aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"
                                                            style="width: 60%;"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        <td class="align-middle">
                                            <div class="dropstart float-lg-end ms-auto pe-0">
                                                <a href="javascript:;" class="cursor-pointer" id="dropdownTable2"
                                                    data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="fa fa-ellipsis-h text-secondary" aria-hidden="true"></i>
                                                </a>
                                                <ul class="dropdown-menu px-2 py-3 ms-sm-n4 ms-n5"
                                                    aria-labelledby="dropdownTable2" style="">
                                                    @if ($usertype == 'user')
                                                        <li><a class="dropdown-item" href="{{ route('admin.users.history', $user->id) }}">ประวัติการใช้น้ำ</a>
                                                        </li>
                                                    @endif
                                                    <li><a class="dropdown-item"
                                                            href="{{ route('admin.users.edit', $user->id) }}">แก้ไขข้อมูลผู้ใช้งานระบบ</a>
                                                    </li>
                                                    <li>
                                                        <form method="POST"
                                                            action="{{ route('admin.users.destroy', $user->id) }}"
                                                            class="col-12" onsubmit="return confirm('Are you sure?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item">ลบข้อมูล</button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/select/1.7.0/js/dataTables.select.min.js"></script>
    <script>
        let table
        let preloaderwrapper = document.querySelector('.preloader-wrapper')
        $(document).ready(function() {

            table = $('#example').DataTable({
                "lengthMenu": [
                    [10, 25, 50, 150, -1],
                    [10, 25, 50, 150, "All"]
                ],
                "sPaginationType": "listbox",
                "language": {
                    "search": "ค้นหา:",
                    "lengthMenu": "แสดง _MENU_ แถว",
                    "info": "แสดง _START_ ถึง _END_ จาก _TOTAL_ แถว",
                    "infoEmpty": "แสดง 0 ถึง 0 จาก 0 แถว",
                    "paginate": {
                        "info": "แสดง _MENU_ แถว",
                    },
                },
                dom: 'lBfrtip',
                buttons: [{
                    extend: 'excelHtml5',
                    'text': 'Excel',
                    exportOptions: {
                        rows: ['.selected']
                    }
                }],

            });

            $('select[name="example_length"]').on('change', function(e) {
                setTimeout(() => {
                    $('#example tbody tr.selected').each(function(index) {
                        $(this).removeClass('selected')
                    })
                }, 50);

                setTimeout(() => {
                    $('#example tbody tr').each(function(index) {
                        $(this).addClass('selected')
                    })
                }, 100);
            });

            $('.dt-buttons').prepend('<label class="m-0">ดาวน์โหลด:</label>')

            $(`<div class="deselect_row_all">
                    <label class="m-0">ยกเลิกเลือกทั้งหมด:</label>
                    <button class="btn btn-secondary btn-sm" id="deselect-all">ตกลง</button>
                </div>
                <div class=" select_row_all">
                    <label class="m-0">เลือกทั้งหมด:</label>
                    <button class="btn btn-success btn-sm" id="deselect-all">ตกลง</button>
                </div>`).insertAfter('.dataTables_length')


            $(`<div class="create_user" style="margin-left:15%"><label class="m-0">&nbsp;</label>
            <a href="{{ route('admin.users.create') }}" class="btn bg-gradient-success btn-sm" >เพิ่มผู้ใช้งานระบบ</a></div>`)
            .insertAfter('.dataTables_filter')

            // $('#example_filter label').html('ค้นหา:')
            $('.dt-button').addClass('btn btn-sm btn-info')

            preloaderwrapper.classList.add('fade-out-animation')
        });


        $(document).on('click', 'tbody tr', function(e) {
            $(this).hasClass('selected') ? $(this).removeClass('selected') : $(this).addClass('selected');
        });
        $(document).on('click', '#deselect-all', function(e) {
            $("tbody tr.selected").removeClass('selected')
        });
        $(document).on('click', '.select_row_all', function(e) {
            $("tbody tr").addClass('selected')
        });

        $(".paginate_select").addClass('form-control-sm mb-3 float-right')
    </script>
@endsection
