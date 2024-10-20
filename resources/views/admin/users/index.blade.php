@extends('layouts.admin1')

@if ($usertype == 'user')
    @section('nav-user')
        active
    @endsection
    @section('nav-main')
        <a href="{{ route('admin.users.index') }}"> ผู้ใช้น้ำประปา</a>
    @endsection
@else
    @section('nav-staff')
        active
    @endsection
    @section('nav-main')
        <a href="{{ route('admin.users.index') }}"> เจ้าหน้าที่งานประปา</a>
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

        .preloader-wrapper {
            position: fixed;
            top: 0;
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

        .fade-out-animation {
            opacity: 0;
            visibility: hidden;
        }
    </style>
@endsection
@section('content')

    <div class="row mt-4">
        {{-- <div class="col-12 col-lg-3">
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
        </div> --}}
        <div class="col-12 col-lg-12">
            <div class="card">

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" style="border-collapse: collapse" id="example">
                            <thead>
                                <tr>
                                    <th class="font-weight-bolder opacity-7">
                                        เลขผู้ใช้น้ำ</th>
                                    <th class="font-weight-bolder opacity-7 ps-2">
                                        เลขผู้ใช้งาน</th>
                                    <th class="font-weight-bolder opacity-7 ps-2">
                                        ชื่อ-สกุล</th>
                                    <th class="font-weight-bolder opacity-7 ps-2">
                                        วันที่ลงทะเบียน
                                    </th>
                                    <th class="font-weight-bolder opacity-7 ps-2">
                                        ที่อยู่</th>
                                    <th class="font-weight-bolder opacity-7 ps-2">
                                        หมู่</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <div class="preloader-wrapper">
                                    <button class="btn btn-primary btn-sm mb-2" type="button" disabled>
                                        <span class="spinner-border spinner-border-sm" role="status"></span>
                                        Loading...
                                    </button>
                                </div>
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
    <script src="https://cdn.datatables.net/plug-ins/2.0.5/pagination/select.js"></script>
    <script>
        let table
        let preloaderwrapper = document.querySelector('.preloader-wrapper')
        $(document).ready( function() {

            table = $('#example').DataTable({
                ajax: {
                    url: '/api/users',
                    dataSrc: ''
                },
                "sPaginationType": "listbox",
                columns: [{
                        data: "meternumber"
                    },
                    {
                        data: "user_id"
                    },
                    {
                        data: "fullname"
                    },
                    {
                        data: "acceptance_date"
                    },
                    {
                        data: "address"
                    },
                    {
                        data: "zone_name"
                    },
                    {
                        data: "showLink"
                    },
                ],
                "lengthMenu": [
                    [10, 25, 50, 150, -1],
                    [10, 25, 50, 150, "All"]
                ],
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
                setTimeout(() =>{
                    preloaderwrapper.classList.add('fade-out-animation')

                },2000)


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
