@extends('layouts.admin1')

@section('user-show')
    show
@endsection
@section('nav-user')
    active
@endsection
@section('nav-main')
    <a href="{{ route('admin.users.index') }}"> ผู้ใช้น้ำประปา</a>
@endsection

@section('nav-header')
    ผู้ใช้งานระบบ
@endsection
@section('nav-current')
    ข้อมูลผู้ใช้น้ำประปา
@endsection
@section('nav-topic')
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
        a.meternumber{
            color:blue;
            /* text-decoration: underline */
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

   
            <div class="card">
                <div class="card-header">
                    <h4>มิเตอร์ที่ยังใช้งานปัจจุบัน</h4>
                </div>

                <div class="card-body">
                    <table class="table" style="border-collapse: collapse" id="example">
                        <thead>
                            <tr>
                                <th class="font-weight-bolder opacity-7">
                                    เลขผู้ใช้งาน</th>
                                    <th class="font-weight-bolder opacity-7 ps-2">
                                        ชื่อ-สกุล</th>
                                    <th class="font-weight-bolder opacity-7">
                                        factory_no</th>
                                <th class="font-weight-bolder opacity-7 ps-2">
                                    เลขผู้ใช้น้ำ</th>
                               
                                <th class="font-weight-bolder opacity-7 ps-2">
                                        ชื่อมิเตอร์ย่อย</th>
                                {{-- <th class="font-weight-bolder opacity-7 ps-2">
                                    วันที่ลงทะเบียน
                                </th> --}}
                                <th class="font-weight-bolder opacity-7 ps-2">
                                    ที่อยู่</th>
                                <th class="font-weight-bolder opacity-7 ps-2">
                                    หมู่</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                    @foreach ($user_active as $u_active)
                            <tr>
                                <td>{{$u_active[0]->user_id}}</td>
                                <td>
                                    {{$u_active[0]->user->prefix."".$u_active[0]->user->firstname." ".$u_active[0]->user->lastname}}
                                </td>
                                <td class="text-right">
                                    @foreach ($u_active as $item)
                                    <div>{{$item['factory_no']}}</div>
                                    @endforeach
                                </td>
                                <td>
                                    @foreach ($u_active as $item)
                                   <div>  <a class="meternumber" href="{{route('admin.users.edit', ['user_id' => $item->meter_id])}}">
                                            {{$item['meternumber']}} 
                                        </a>
                                    </div>
                                  
                                    @endforeach
                                </td>
                               
                                <td>
                                    {{$u_active[0]->submeter_name}}
                                </td>
                                <td>
                                    {{$u_active[0]->undertake_zone->zone_name}}
                                </td>
                                <td>
                                    {{$u_active[0]->undertake_subzone->subzone_name}}
                                </td>
                                <td>
                                    <div class="dropstart float-lg-end ms-auto pe-0">
                                        <a href="javascript:;" class="cursor-pointer" id="dropdownTable{{$u_active[0]->meter_id}}" data-bs-toggle="dropdown" aria-expanded="true">
                                        <i class="fa fa-ellipsis-h text-secondary" aria-hidden="true"></i>
                                        </a>
                                        <ul class="dropdown-menu px-2 py-3 ms-sm-n4 ms-n5 " aria-labelledby="dropdownTable{{$u_active[0]->meter_id}}"  data-popper-placement="left-start">
                                            <li><a class="dropdown-item border-radius-md" href="{{route('admin.users.edit', ['user_id' => $item->meter_id, 'addmeter' => 'addmeter'])}}">เพิ่มมิเตอร์ใหม่</a></li>
                                             <li>

                                            <a class="dropdown-item border-radius-md" href="{{route('usermeter_infos.edit_invoices', ['meter_id' => $item->meter_id])}}">แก้ไขเลขมิเตอร์</a>
                                            </li>
                                            {{-- <li>

                                            <a class="dropdown-item border-radius-md destroy" href="{{route('usermeter_infos.edit_invoices', ['meter_id' => $item->meter_id])}}">แก้ไขเลขมิเตอร์</a>
                                            </li> --}}
                                        </ul>
                                        </div>
                                </td>
                            </tr>
                       
                    @endforeach
                </tbody>
                </table>

                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h4>มิเตอร์ที่ยกเลิกการใช้งาน</h4>
                </div>
                <div class="card-body">
                    <table class="table" style="border-collapse: collapse" id="example2">
                        <thead>
                            <tr>
                                <th class="font-weight-bolder opacity-7">
                                    เลขผู้ใช้งาน</th>
                                    <th class="font-weight-bolder opacity-7 ps-2">
                                        ชื่อ-สกุล</th>
                                    <th class="font-weight-bolder opacity-7">
                                        factory_no</th>
                                <th class="font-weight-bolder opacity-7 ps-2">
                                    เลขผู้ใช้น้ำ</th>
                               
                                <th class="font-weight-bolder opacity-7 ps-2">
                                        ชื่อมิเตอร์ย่อย</th>
                                {{-- <th class="font-weight-bolder opacity-7 ps-2">
                                    วันที่ลงทะเบียน
                                </th> --}}
                                <th class="font-weight-bolder opacity-7 ps-2">
                                    ที่อยู่</th>
                                <th class="font-weight-bolder opacity-7 ps-2">
                                    หมู่</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                    @foreach ($user_deleted as $user)
                    {{-- @dd($user) --}}
                            <tr>
                                <td>{{$user[0]->user_id}}</td>
                                <td>
                                    {{$user[0]->user->prefix."".$user[0]->user->firstname." ".$user[0]->user->lastname}}
                                </td>
                                <td class="text-right">
                                    @foreach ($user as $item)
                                    <div>{{$item['factory_no']}}</div>
                                    @endforeach
                                </td>
                                <td>
                                    @foreach ($user as $item)
                                   <div>  
                                   @if($item->status == 'active')
                                        <a class="meternumber" href="{{route('admin.users.edit', ['user_id' => $item->meter_id])}}">
                                            {{$item['meternumber']}} 
                                        </a>
                                    @else
                                        {{$item['meternumber']}} (ยกเลิกการใช้งาน)
                                    @endif
                                    
                                    </div>
                                  
                                    @endforeach
                                </td>
                               
                                <td>
                                    {{$user[0]->submeter_name}}
                                </td>
                                <td>
                                    {{$user[0]->undertake_zone->zone_name}}
                                </td>
                                <td>
                                    {{$user[0]->undertake_subzone->subzone_name}}
                                </td>
                                <td>
                                    <div class="dropstart float-lg-end ms-auto pe-0">
                                        <a href="javascript:;" class="cursor-pointer" id="dropdownTable{{$user[0]->meter_id}}" data-bs-toggle="dropdown" aria-expanded="true">
                                        <i class="fa fa-ellipsis-h text-secondary" aria-hidden="true"></i>
                                        </a>
                                        <ul class="dropdown-menu px-2 py-3 ms-sm-n4 ms-n5 " aria-labelledby="dropdownTable{{$user[0]->meter_id}}"  data-popper-placement="left-start">
                                            <li><a class="dropdown-item border-radius-md" href="{{route('admin.users.edit', ['user_id' => $item->meter_id, 'addmeter' => 'addmeter'])}}">เพิ่มมิเตอร์ใหม่</a></li>
                                            
                                            <li>

                                            {{-- <a class="dropdown-item border-radius-md destroy" href="{{route('admin.users.destroy', ['user_id' => $item->meter_id])}}">ยกเลิกการใช้งาน</a> --}}
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
        $(document).ready(function() {

            table = $('#example').DataTable({
                // ajax: {
                //     url: '/api/users',
                //     dataSrc: ''
                // },
                "sPaginationType": "listbox",
               
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
            setTimeout(() => {
                preloaderwrapper.classList.add('fade-out-animation')

            }, 2000)


        });


        $(document).ready(function() {

            table = $('#example2').DataTable({
                // ajax: {
                //     url: '/api/users',
                //     dataSrc: ''
                // },
                "sPaginationType": "listbox",
               
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

            $('select[name="example2_length"]').on('change', function(e) {
                setTimeout(() => {
                    $('#example2 tbody tr.selected').each(function(index) {
                        $(this).removeClass('selected')
                    })
                }, 50);

                setTimeout(() => {
                    $('#example2 tbody tr').each(function(index) {
                        $(this).addClass('selected')
                    })
                }, 100);
            });

            $('#example2_length .dt-buttons').prepend('<label class="m-0">ดาวน์โหลด:</label>')

            $(`<div class="deselect_row_all">
                    <label class="m-0">ยกเลิกเลือกทั้งหมด:</label>
                    <button class="btn btn-secondary btn-sm" id="deselect-all">ตกลง</button>
                </div>
                <div class=" select_row_all">
                    <label class="m-0">เลือกทั้งหมด:</label>
                    <button class="btn btn-success btn-sm" id="deselect-all">ตกลง</button>
                </div>`).insertAfter('#example2_wrapper .dataTables_length')


            $(`<div class="create_user" style="margin-left:15%"><label class="m-0">&nbsp;</label>
            <a href="{{ route('admin.users.create') }}" class="btn bg-gradient-success btn-sm" >เพิ่มผู้ใช้งานระบบ</a></div>`)
                .insertAfter('#example2_wrapper .dataTables_filter')

            // $('#example_filter label').html('ค้นหา:')
            $('#example2_length .dt-button').addClass('btn btn-sm btn-info')
            setTimeout(() => {
                preloaderwrapper.classList.add('fade-out-animation')

            }, 2000)


        });

        $(document).on('click', '.destroy', function(e) {
            return window.confirm('คุณต้องการยกเลิกการใช้งานของผู้งานใช้งานใช่หรือไม่ ???')
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
 
        $(document).on('click', '#example2_length tbody tr', function(e) {
            $(this).hasClass('selected') ? $(this).removeClass('selected') : $(this).addClass('selected');
        });
        $(document).on('click', '#example2_length #deselect-all', function(e) {
            $("#example2_length tbody tr.selected").removeClass('selected')
        });
        $(document).on('click', '#example2_length .select_row_all', function(e) {
            $("#example2_length tbody tr").addClass('selected')
        });

        $(".paginate_select").addClass('form-control-sm mb-3 float-right')
    </script>
@endsection
