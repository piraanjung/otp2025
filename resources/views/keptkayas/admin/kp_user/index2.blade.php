@extends('layouts.admin1')

@section('nav-user')
active
@endsection
@section('nav-main')
<a href="{{ route('admin.users.index') }}"> ผู้ใช้เก็บขยะ</a>
@endsection


@section('nav-header')
ผู้ใช้งานระบบ
@endsection
@section('nav-current')
ข้อมูลผู้ใช้น้ำประปา
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
        /* background: lightblue */
    }

    .dataTables_length,
    .dt-buttons,
    .dataTables_filter,
    .select_row_all,
    .deselect_row_all {
        display: inline-flex;
    }

    .dt-buttons,
    .select_row_all,
    .deselect_row_all {
        flex-direction: column
    }

    .dt-buttons {
        margin-left: 3%
    }

    .dataTables_filter {
        margin-left: 40%
    }

    .opacity-25 {
        opacity: 1%;
    }

    .opacity {
        border-bottom-color: white !important
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
        @foreach($zones as $zone)
            <div class="col-12 col-lg-6 col-md-6 col-sm-12">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="{{ $zone->id }}" name="zone[]">
                    <label class="custom-control-label" for="customCheck1">{{ $zone->zone_name }}</label>
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
        <div class="card-header d-flex">
            <div class="w-80">
                <h5 class="mb-0"> </h5>
                <p class="text-sm mb-0">
                    <span class="w-90"></span>
                </p>
            </div>
            <div>
                <a href="{{ route('admin.users.create') }}"
                    class="btn bg-gradient-success">เพิ่มผู้ใช้งานระบบ</a>
            </div>
        </div>
        <div class="card-body">
            <div class="nav-wrapper position-relative end-0">
                @php
                    $i = 1;
                @endphp
                <ul class="nav nav-pills nav-fill p-1" role="tablist">
                    @foreach($users as $key => $group)
                        <li class="nav-item">
                            <a class="nav-link mb-0 px-0 py-1  {{ $i == 1 ? 'active' : '' }} tab"
                                data-bs-toggle="tab" href="#usergroup-tabs-{{ $key }}" role="tab"
                                aria-controls="preview" aria-selected="true">
                                <i class="ni ni-badge text-sm me-2"></i>
                                {{ $group[0]->usergroup->usergroup_name }}
                            </a>
                        </li>
                        @php
                            $i = 2;
                        @endphp
                    @endforeach
                </ul>
            </div>
            <div class="card mt-4 border-primary">
                <div class="card-body">
                    <div class="tab-content">
                        <?php $active = 1; ?>
                        @foreach($users as $key => $group)
                            <div class="table-responsive tab-pane {{ $active++ == 1 ? 'active show' : '' }}"
                                role="tabpanel" id="usergroup-tabs-{{ $key }}">
                                <form action="{{ route('admin.users.update_paid_per_budgetyear') }}"
                                    method="post">
                                    @csrf
                                    <button type="submit" class="btn btn-info">บันทึกการแก้ไข</button>
                                    <table class="table" style="border-collapse: collapse" id="example">
                                        <thead>
                                            <tr>
                                                <th class="">เลขผู้ใช้น้ำ</th>
                                                <th class="ps-2">ชื่อ-สกุล</th>
                                                <th class="ps-2">ที่อยู่</th>
                                                <th class="ps-2">หมู่</th>
                                                <th class="ps-2">ปีงบประมาณ</th>
                                                <th class="ps-2">จ่ายต่อปี<sup>(บาท)</sup></th>
                                                <th class="ps-2">จำนวนถัง<sup>(ถัง)</sup></th>
                                                <th class="ps-2">คิดเป็นเงิน<sup>(บาท/ปี)</sup></th>
                                                <th class="ps-2">ชำระแล้ว<sup>(บาท)</sup></th>
                                                <th class="ps-2">สถานะ</th>
                                                <th class="ps-2">หมายเหตุ</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($group as $item)
                                                @php
                                                    $c = 1;
                                                    $item_count = collect($item->user_payment_per_year)->count();
                                                @endphp
                                                @if(!isset($item->user))
                                                    {{ dd($item) }}
                                                @endif

                                                <tr>
                                                    <td
                                                        class="{{ $c > 1 ? 'opacity-25' : '' }} ">
                                                        {{ $item->user_id }}</td>
                                                    <td
                                                        class="{{ $c > 1 ? 'opacity-25' : '' }} ps-2">
                                                        {{ $item->user->prefix . '' . $item->user->firstname . ' ' . $item->user->lastname }}
                                                    </td>
                                                    <td
                                                        class="{{ $c > 1 ? 'opacity-25' : '' }} ps-2">
                                                        {{ $item->user->address }}</td>
                                                    <td
                                                        class="{{ $c > 1 ? 'opacity-25' : '' }} ps-2">
                                                        {{ $item->user->user_zone->zone_name }}</td>
                                                    <td>
                                                        {{ $item->user_payment_per_year[0]->budgetyear->budgetyear_name }}
                                                    </td>
                                                    <td class="ps-2">
                                                        <input type="text"
                                                            name="p[{{ $item->user_payment_per_year[0]->id }}][val_changed]"
                                                            id="payment_per_year{{ $item->user_id }}"
                                                            class="form-control w-100  text-end payment_per_year_and_qty"
                                                            data-user_id="{{ $item->user_id }}"
                                                            value="{{ $item->user_payment_per_year[0]->payment_per_year }}">

                                                    </td>
                                                    <td class="ps-2">
                                                        <input type="text"
                                                            name="p[{{ $item->user_payment_per_year[0]->id }}][bin_quantity]"
                                                            id="bin_quantity{{ $item->user_id }}"
                                                            data-user_id="{{ $item->user_id }}"
                                                            class="form-control text-end  payment_per_year_and_qty"
                                                            value="{{ $item->user_payment_per_year[0]->bin_quantity }}">
                                                    </td>
                                                    <td class="ps-2">
                                                        <input type="text"
                                                            name="p[{{ $item->user_payment_per_year[0]->id }}][total_payment_per_year]"
                                                            id="total_payment_per_year{{ $item->user_id }}"
                                                            class="form-control w-100 text-end"
                                                            value="{{ $item->user_payment_per_year[0]->payment_per_year * $item->user_payment_per_year[0]->bin_quantity }}"
                                                            readonly>
                                                        <input type="hidden"
                                                            name="p[{{ $item->user_payment_per_year[0]->id }}][changed_ref]"
                                                            value="{{ $item->user_payment_per_year[0]->payment_per_year * $item->user_payment_per_year[0]->bin_quantity }}">

                                                    </td>
                                                    <td class="ps-2">
                                                        <input type="text" name="" id="price_per_budgetyear"
                                                            class="form-control w-100 text-end"
                                                            value="{{ $item->user_payment_per_year[0]->paid_total_payment_per_year }}"
                                                            readonly>

                                                    </td>
                                                    <td class="ps-2">
                                                        {{ $item->user_payment_per_year[0]->status }}</td>
                                                    <td class="ps-2">
                                                        {{ $item->user_payment_per_year[0]->comment }}</td>
                                                    <td class="align-middle">
                                                        <div class="dropstart float-lg-end ms-auto pe-0">
                                                            <a href="javascript:;" class="cursor-pointer"
                                                                id="dropdownTable2" data-bs-toggle="dropdown"
                                                                aria-expanded="false">
                                                                <i class="fa fa-ellipsis-h" aria-hidden="true"></i>
                                                            </a>
                                                            <ul class="dropdown-menu px-2 py-3 ms-sm-n4 ms-n5"
                                                                aria-labelledby="dropdownTable2" style="">
                                                                <li><a class="dropdown-item"
                                                                        href="{{ route('admin.users.edit', $item->user_id) }}">แก้ไขข้อมูล</a>
                                                                </li>
                                                                <li>

                                                                    <a class="dropdown-item test"
                                                                        href="javascript:test()">ยกเลิกการใช้งาน</a>

                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach

                                        </tbody>


                                    </table>
                                </form>

                            </div>
                        @endforeach

                    </div>
                </div>
            </div>
            {{-- <div class="card mt-4 border-primary">
                        <div class="card-body">
                            <div class="tab-content">
                                <?php $active = 1; ?>
@foreach($users as $key => $group)
                                    <div class="table-responsive tab-pane {{ $active++ == 1 ? 'active show' : '' }}"
            role="tabpanel" id="usergroup-tabs-{{ $key }}">
            <form action="{{ route('admin.users.update_paid_per_budgetyear') }}" method="post">
                @csrf
                <button type="submit" class="btn btn-info">บันทึกการแก้ไข</button>
                <table class="table" style="border-collapse: collapse" id="example">
                    <thead>
                        <tr>
                            <th class="text-uppercase text-secondary font-weight-bolder opacity-7">
                                เลขผู้ใช้น้ำ</th>
                            <th class="text-uppercase text-secondary font-weight-bolder opacity-7 ps-2">
                                ชื่อ-สกุล</th>
                            <th class="text-uppercase text-secondary font-weight-bolder opacity-7 ps-2">
                                วันที่ลงทะเบียน
                            </th>
                            <th class="text-uppercase text-secondary font-weight-bolder opacity-7 ps-2">
                                ที่อยู่</th>
                            <th class="text-uppercase text-secondary font-weight-bolder opacity-7 ps-2">
                                หมู่</th>
                            <th class="text-uppercase text-secondary font-weight-bolder opacity-7 ps-2">
                                ปีงบประมาณ</th>
                            <th class="text-uppercase text-secondary font-weight-bolder opacity-7 ps-2">
                                จ่ายต่อปี<sup>(บาท)</sup>
                            </th>
                            <th class="text-uppercase text-secondary font-weight-bolder opacity-7 ps-2">
                                จำนวนถัง<sup>(ถัง)</sup>
                            </th>
                            <th class="text-uppercase text-secondary font-weight-bolder opacity-7 ps-2">
                                คิดเป็นเงิน<sup>(บาท/ปี)</sup>
                            </th>
                            <th class="text-uppercase text-secondary font-weight-bolder opacity-7 ps-2">
                                ชำระแล้ว<sup>(บาท)</sup></th>
                            <th class="text-uppercase text-secondary font-weight-bolder opacity-7 ps-2">
                                สถานะ</th>
                            <th class="text-uppercase text-secondary font-weight-bolder opacity-7 ps-2">
                                หมายเหตุ</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($group->chunk(2000) as $item)

                            @foreach($item->user_payment_per_year as $paid_per_budgetyear)
                                <?php
                                                        $c = 1;
                                                        $item_count = collect($item->user_payment_per_year)->count();
                                                        ?>
                                <tr
                                    class="{{ $item_count - $c > 0 ? 'opacity' : '' }}">
                                    <td
                                        class="{{ $c > 1 ? 'opacity-25' : '' }} ">
                                        {{ $item->user_id }}</td>
                                    <td
                                        class="{{ $c > 1 ? 'opacity-25' : '' }} ps-2">
                                        {{ $item->user->prefix . '' . $item->user->firstname . ' ' . $item->user->lastname }}
                                    </td>
                                    <td
                                        class="{{ $c > 1 ? 'opacity-25' : '' }} ps-2">
                                        {{ date_format(date_create($item->acceptance_date), 'd-m-' . date('Y') + 543) }}
                                    </td>
                                    <td
                                        class="{{ $c > 1 ? 'opacity-25' : '' }} ps-2">
                                        {{ $item->user->address }}</td>
                                    <td
                                        class="{{ $c++ > 1 ? 'opacity-25' : '' }} ps-2">
                                        @php
                                            if (!isset($item->undertake_subzone->subzone_name)) {
                                            dd($item);
                                            }
                                        @endphp
                                        {{ $item->undertake_subzone->subzone_name }}
                                    </td>
                                    <td class="ps-2">
                                        {{ $paid_per_budgetyear->budgetyear->budgetyear_name }}
                                    </td>
                                    <td class="ps-2">
                                        <input type="text" name="p[{{ $paid_per_budgetyear->id }}][val]"
                                            id="payment_per_year{{ $item->user_id }}"
                                            class="form-control w-100  text-end payment_per_year_and_qty"
                                            data-user_id="{{ $item->user_id }}"
                                            value="{{ $paid_per_budgetyear->total_payment_per_year }}">
                                        <input type="hidden" name="p[{{ $paid_per_budgetyear->id }}][changed]"
                                            value="{{ $paid_per_budgetyear->total_payment_per_year }}"
                                            id="payment_per_year{{ $item->user_id }}">
                                    </td>

                                    <td class="ps-2">
                                        <input type="text" name="p[{{ $paid_per_budgetyear->id }}][bin_quantity]"
                                            id="bin_quantity{{ $item->user_id }}"
                                            class="form-control text-end  payment_per_year_and_qty"
                                            value="{{ $paid_per_budgetyear->bin_quantity }}">

                                    </td>
                                    <td class="ps-2">
                                        <input type="text"
                                            name="p[{{ $paid_per_budgetyear->id }}][total_payment_per_year]"
                                            id="total_payment_per_year{{ $item->user_id }}"
                                            class="form-control w-100 text-end"
                                            value="{{ $paid_per_budgetyear->total_payment_per_year * $paid_per_budgetyear->bin_quantity }}"
                                            readonly>

                                    </td>
                                    <td class="ps-2">
                                        <input type="text" name="" id="price_per_budgetyear"
                                            class="form-control w-100 text-end"
                                            value="{{ $paid_per_budgetyear->paid_total_payment_per_year }}" readonly>

                                    </td>
                                    <td class="ps-2">{{ $paid_per_budgetyear->status }}</td>
                                    <td class="ps-2">{{ $paid_per_budgetyear->comment }}</td>
                                    <td class="align-middle">
                                        <div class="dropstart float-lg-end ms-auto pe-0">
                                            <a href="javascript:;" class="cursor-pointer" id="dropdownTable2"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fa fa-ellipsis-h" aria-hidden="true"></i>
                                            </a>
                                            <ul class="dropdown-menu px-2 py-3 ms-sm-n4 ms-n5"
                                                aria-labelledby="dropdownTable2" style="">
                                                <li><a class="dropdown-item"
                                                        href="{{ route('admin.users.edit', $item->user_id) }}">แก้ไขข้อมูล</a>
                                                </li>
                                                <li>

                                                    <a class="dropdown-item test"
                                                        href="javascript:test()">ยกเลิกการใช้งาน</a>

                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach

                    </tbody>


                </table>
            </form>

        </div>
        @endforeach

    </div>
</div>
</div> --}}
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
<script src="https://cdn.datatables.net/plug-ins/1.13.7/pagination/select.js"></script>

<script>
    let table
    $(document).ready(function () {

        table = $('#example').DataTable({
            "lengthMenu": [
                [10, 25, 50, 150, -1],
                [10, 25, 50, 150, "All"]
            ],
            "sPaginationType": "listbox",
            // pagingType: 'listbox',
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
        $('#example tbody tr').addClass('selected')
        $('select[name="example_length"]').on('change', function (e) {
            setTimeout(() => {
                $('#example tbody tr.selected').each(function (index) {
                    $(this).removeClass('selected')
                })
            }, 50);

            setTimeout(() => {
                $('#example tbody tr').each(function (index) {
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



        // $('#example_filter label').html('ค้นหา:')
        $('.dt-button').addClass('btn btn-sm btn-info')

        $('.opacity td').addClass('opacity');
    });


    $(document).on('click', 'tbody tr', function (e) {
        $(this).hasClass('selected') ? $(this).removeClass('selected') : $(this).addClass('selected');
    });
    $(document).on('click', '#deselect-all', function (e) {
        $("tbody tr.selected").removeClass('selected')
    });
    $(document).on('click', '.select_row_all', function (e) {
        $("tbody tr").addClass('selected')
    });

    $(".paginate_select").addClass('form-control-sm mb-3 float-right')

    $('.tab').on("click", function (e) {
        e.preventDefault();
        let id = $(this).attr('href')
        console.log('id', id)
        $(".tab-pane").removeClass('show active')
        $(id).addClass('show active')
    })
    $('.payment_per_year_and_qty').on('keyup', function (e) {
        e.preventDefault();
        let user_id = $(this).data('user_id');

        $(`#price_per_budgetyear${user_id}`).val(1)
        let total = $(`#bin_quantity${user_id}`).val() * $(`#payment_per_year${user_id}`).val();
        $(`#total_payment_per_year${user_id}`).val(total)
    })

</script>
@endsection
