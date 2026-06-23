@extends('layouts.admin1')

@section('nav-cutmeter')
    active
@endsection

@section('nav-header')
    จัดการใบแจ้งหนี้
@endsection
@section('nav-main')
    <a href="{{ route('cutmeter.index') }}">ตัดมิเตอร์</a>
@endsection

@section('nav-topic')
    รายชื่อผู้ใช้น้ำประปาค้างชำระเกิน 3 รอบบิล
@endsection


@section('style')
    <style>
        .hidden {
            display: none
        }
    </style>
@endsection

@section('content')
    <div class="card2">
        <div class="card-body2" id="aa">
            <div class="">

                {{-- ส่งข้อมูลที่เลือกไปปริ้น --}}
                <form action="{{ route('admin.owepaper.print') }}" method="POST">
                    @csrf
                    <div class="card res">
                        <div class="card-header header">
                            <div class="card-title row">
                                <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                                    <div class="card">
                                        <div class="card-body p-3">
                                            <div class="row">
                                                <div class="col-8">
                                                    <div class="numbers">
                                                        <p class="text-sm mb-0 text-capitalize font-weight-bold">
                                                            ค้างชำระเงิน 3 รอบบิล
                                                        </p>
                                                        <h5 class="font-weight-bolder mb-0">
                                                            {{ collect($cutmeters)->count() }}
                                                            <span
                                                                class="text-success text-sm font-weight-bolder">(คน)</span>
                                                        </h5>
                                                    </div>
                                                </div>
                                                <div class="col-4 text-end">
                                                    <div
                                                        class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                                                        <i class="ni ni-world text-lg opacity-10" aria-hidden="true"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-5">
                                    @if (collect($cutmeters)->isNotEmpty())
                                        <button type="submit" name="submitBtn" value="print" class="btn btn-primary "
                                            id="print_multi_inv">
                                            ปริ้นใบแจ้งเตือน</button>
                                        <button type="submit" class="btn btn-info" name="submitBtn" value="excel"
                                            id="print_list">
                                            ดาวน์โหลดเป็นไฟล์ Excel</button>
                                    @endif

                                </div>
                            </div>

                        </div>

                        <div class="card-body table-responsive">
                            <div id="DivIdToExport">
                                <table id="oweTable" class="table text-nowrap" width="100%">
                                    <thead class="text-center">
                                        <tr id="headtr">
                                            <th></th>
                                            <th>เลขผู้ใช้น้ำ</th>
                                            <th>ชื่อผู้ค้างชำระ</th>
                                            <th class="text-center">บ้านเลขที่</th>
                                            <th class="text-center">หมู่ที่</th>
                                            <th>ค้างชำระ(เดือน)</th>
                                            {{-- <th>ปริ้นแจ้งเตือน(ครั้ง)</th> --}}
                                            <th>การดำเนินการ</th>
                                            <th>สถานะ</th>
                                            <th></th>
                                        </tr>

                                    </thead>
                                    <tbody>
                                        @foreach ($cutmeters as $item)
                                            <tr>
                                                <td>
                                                    <input type="checkbox" class="invoice_id" style="opacity:0"
                                                        name="meter_id[{{ $item->meter_id }}]">
                                                    <i class="fa fa-plus-circle text-success findInfo"
                                                        data-user_id="{{ $item->meter_id }}}"></i>
                                                </td>
                                                <td class="text-center">{{ $item->meter_id }}</td>
                                                <td>{{ $item->user->prefix . '' . $item->user->firstname . ' ' . $item->user->lastname }}
                                                </td>
                                                <td class="text-end">{{ $item->user->address }}</td>
                                                <td class="text-end">{{ $item->user->user_zone->zone_name }}
                                                </td>
                                                <td class="text-end">{{ $item->owe_count }}</td>
                                                {{-- <td class="text-end">
                                                    {{ collect($item->cutmeter)->isEmpty() ? 0 : $item->cutmeter[0]->warning_print }}
                                                </td> --}}
                                                <td class="text-center">
                                                    @if (collect($item->cutmeter)->isNotEmpty())
                                                        <div class="dropstart ms-auto pe-0">
                                                            <a href="javascript:;" class="cursor-pointer "
                                                                id="dropdown{{ $item->id }}" data-bs-toggle="dropdown"
                                                                aria-expanded="false">
                                                                <i class="fas fa-list-ol" aria-hidden="true"></i>
                                                            </a>
                                                            <div class="row dropdown-menu px-2 py-3 ms-sm-n4 ms-n5"
                                                                aria-labelledby="dropdown{{ $item->id }}">
                                                                <div class="col-12 col-xl-12 mt-xl-0 mt-4">
                                                                    <div class="card">
                                                                        <div class="card-header pb-0 p-3">
                                                                            <h6 class="mb-0">การดำเนินการตัดมิเตอร์ของ
                                                                                <div>
                                                                                    {{ $item->user->prefix . '' . $item->user->firstname . ' ' . $item->user->lastname }}
                                                                                </div>
                                                                            </h6>
                                                                        </div>
                                                                        <div class="card-body p-3">
                                                                            @php
                                                                                $progress = json_decode(
                                                                                    $item->cutmeter[0]->progress,
                                                                                );
                                                                                $i = 1;
                                                                            @endphp
                                                                            @if (collect($progress)->isNotEmpty())
                                                                                <ul class="list-group">
                                                                                    @foreach ($progress as $val)
                                                                                        <li
                                                                                            class="list-group-item border-0 d-flex align-items-center px-0 mb-2">
                                                                                            <h5>{{ $i++ }}.&nbsp;&nbsp;
                                                                                            </h5>
                                                                                            <div class="avatar me-3">
                                                                                                <img src="{{ asset('templatemo/images/avatar/pretty-blonde-woman-wearing-white-t-shirt.jpg') }}"
                                                                                                    alt="kal"
                                                                                                    class="border-radius-lg shadow">
                                                                                            </div>
                                                                                            <div
                                                                                                class="d-flex align-items-start flex-column justify-content-center">
                                                                                                <h6 class="mb-0 text-sm">
                                                                                                    <div>ผู้รับผิดชอบ</div>


                                                                                                    @foreach ($val->undertaker as $taker)
                                                                                                        @php
                                                                                                            $u = App\Models\User::where(
                                                                                                                'id',
                                                                                                                $taker,
                                                                                                            )->get([
                                                                                                                'name',
                                                                                                                'firstname',
                                                                                                                'lastname',
                                                                                                            ]);
                                                                                                            echo '<div>- ' .
                                                                                                                $u[0]
                                                                                                                    ->name .
                                                                                                                ' ' .
                                                                                                                $u[0]
                                                                                                                    ->firstname .
                                                                                                                ' ' .
                                                                                                                $u[0]
                                                                                                                    ->lastname .
                                                                                                                '</div>';
                                                                                                        @endphp
                                                                                                    @endforeach
                                                                                                </h6>
                                                                                                <p class="mb-0 text-xs">
                                                                                                    วันที่
                                                                                                    {{ date('d-m-Y H:i:s', $val->created_at) }}
                                                                                                </p>
                                                                                            </div>
                                                                                            <a class="btn btn-link pe-3 ps-0 mb-0 ms-auto"
                                                                                                href="javascript:;">
                                                                                                <div>สถานะ</div>
                                                                                                {{ $val->topic }}
                                                                                            </a>
                                                                                        </li>
                                                                                    @endforeach
                                                                                </ul>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif

                                                </td>
                                                <td class="text-center">
                                                    @php
                                                        $warning_print = 'รอทำการปริ้นแจ้งเตือนครั้งที่ 1';
                                                        if (collect($item->cutmeter)->isNotEmpty()) {
                                                            $warning_print =
                                                                $item->cutmeter[0]->status == 'init'
                                                                    ? 'ออกใบแจ้งเตือนครั้งที่ ' .
                                                                        $item->cutmeter[0]->warning_print
                                                                    : $item->cutmeter[0]->status;
                                                        }
                                                    @endphp
                                                    {{ $warning_print }}
                                                </td>
                                                <td>
                                                    @if (collect($item->cutmeter)->isNotEmpty())
                                                        <div class="dropstart float-lg-end ms-auto pe-0">
                                                            <a href="javascript:;" class="cursor-pointer"
                                                                id="dropdownTable2" data-bs-toggle="dropdown"
                                                                aria-expanded="false">
                                                                <i class="fa fa-ellipsis-h text-secondary"
                                                                    aria-hidden="true"></i>
                                                            </a>
                                                            <ul class="dropdown-menu px-2 py-3 ms-sm-n4 ms-n5"
                                                                aria-labelledby="dropdownTable2" style="">
                                                                @if ($item->cutmeter[0]->status == 'init')
                                                                    <li>
                                                                        <a class="dropdown-item border-radius-md"
                                                                            href="{{ route('cutmeter.progress', $item->cutmeter[0]->id) }}">ดำเนินการตัดมิเตอร์</a>
                                                                    </li>
                                                                @elseif($item->cutmeter[0]->status == 'cutmeter' || $item->cutmeter[0]->status == 'install')
                                                                    <li>
                                                                        <a class="dropdown-item border-radius-md"
                                                                            href="{{ route('cutmeter.installmeter', $item->cutmeter[0]->id) }}">ติดตั้งมิเตอร์</a>
                                                                    </li>
                                                                    <li>
                                                                        <a class="dropdown-item border-radius-md"
                                                                            href="{{ route('cutmeter.installmeter', $item->cutmeter[0]->id) }}">ยกเลิกการตัดมิเตอร์</a>
                                                                    </li>
                                                                    <li>
                                                                        <a class="dropdown-item border-radius-md"
                                                                            href="{{ route('cutmeter.print_install_meter', $item->cutmeter[0]->id, $item->status) }}">ปริ้นใบขอติดตั้งมิเตอร์ใหม่</a>
                                                                    </li>
                                                                @endif

                                                            </ul>
                                                        </div>
                                                    @endif

                                                </td>
                                            </tr>
                                        @endforeach

                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!--card-body-->
                    </div>
                </form>
            </div>

        </div>
        {{-- @endif --}}
    @endsection


    @section('script')
        <script
            src="{{ asset('/js/demo-Export-Html-Table-To-Excel-Spreadsheet-using-jQuery-table2excel/src/jquery.table2excel.js') }}">
        </script>
        <script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.html5.js"></script>
        <script src="https://cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.colVis.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.0.0/js/buttons.print.min.js"></script>
        <script src="https://cdn.datatables.net/select/1.3.3/js/dataTables.select.min.js"></script>
        <script src="https://cdn.datatables.net/plug-ins/1.10.19/api/sum().js"></script>
        <script type="text/javascript" language="javascript"
            src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>

        <script>
            let table;
            let cloneThead = true
            let col_index = -1


            $(document).ready(function() {
                cutmeterInfos()

                // $('.paginate_page').html('หน้า')
                // let val = $('.paginate_of').text()
                // $('.paginate_of').text(val.replace('of', 'จาก'));

                //เอาค่าผลรวมไปแสดงตารางบนสุด
                $('#meter_unit_used_sum').html($('.meter_unit_used_sum').val())
                $('#owe_total_sum').html($('.owe_total_sum').val())
                $('#reserve_total_sum').html($('.reserve_total_sum').val())
                $('#all_total_sum').html($('.all_total_sum').val())





            }) //document


            function cutmeterInfos() {
                table = $('#oweTable').DataTable({
                    responsive: true,
                    "pagingType": "listbox",
                    "lengthMenu": [
                        [10, 25, 50, 150, -1],
                        [10, 25, 50, 150, "ทั้งหมด"]
                    ],
                    "language": {
                        "search": "ค้นหา:",
                        "lengthMenu": "แสดง _MENU_ แถว",
                        "info": "แสดง _START_ ถึง _END_ จาก _TOTAL_ แถว",
                        "infoEmpty": "แสดง 0 ถึง 0 จาก 0 แถว",
                        "paginate": {
                            // "info": "แสดง _MENU_ แถว",
                        },

                    },
                    // dom: 'lBfrtip',
                    // buttons: [
                    //     'excel', 'print'
                    // ],

                }) //table


                $('.dataTables_filter').remove()
                if (cloneThead) {
                    $('#oweTable thead tr#headtr').clone().appendTo('#oweTable thead');
                    cloneThead = false
                }

                $('#oweTable thead tr:eq(1) th').each(function(index) {
                    var title = $(this).text();
                    $(this).removeClass('sorting')
                    $(this).removeClass('sorting_asc')
                    if (index > 0 && index <= 5) {
                        $(this).html(
                            `<input type="text" data-id="${index}" class="col-md-12 form-control" id="search_col_${index}" placeholder="ค้นหา" />`
                        );
                    } else {
                        $(this).html('')
                    }
                });


                $(`#oweTable thead  input[type="text"]`).keyup(function() {
                    let that = $(this)
                    var col = parseInt(that.data('id'))

                    if (col !== col_index && col_index !== -1) {
                        $('#search_col_' + col_index).val('')
                        table.column(col_index)
                            .search('')
                            .draw();
                    }
                    setTimeout(function() {

                        let _val = that.val()
                        if (col_index > 2 && col_index <= 5) {
                            var val = $.fn.dataTable.util.escapeRegex(
                                _val
                            );
                            table.column(col)
                                .search(val ? '^' + val + '.*$' : '', true, false)
                                .draw();
                        } else {
                            table.column(col)
                                .search(_val)
                                .draw();
                        }
                    }, 300);

                    col_index = col

                })


                $('.dt-buttons').prepend(`
                <input type="button" class="dt-button  buttons-html5 ml-5 show_all_btn all" value="เลือกทั้งหมด">`)

            }

            $('body').on('click', 'tbody tr', function() {
                    // $('.select-item').text('')
                    let checked = $(this).children().first().children().first()
                    if (checked.prop('checked') === false) {
                        checked.prop('checked', true)
                        $(this).addClass('selected')
                        $(this).addClass('shown')

                    } else {
                        $(this).removeClass('selected')
                        $(this).removeClass('shown')
                        checked.prop('checked', false)
                    }
                }),


                $('body').on('click', '.findInfo', function() {
                    let user_id = $(this).data('user_id')
                    var tr = $(this).closest('tr');
                    var row = table.row(tr);
                    console.log('row', row)
                    if (row.child.isShown()) {
                        // This row is already open - close it
                        row.child.hide();
                        tr.removeClass('shown');
                    } else {
                        // Open this row
                        //หาข้อมูลการชำระค่าน้ำประปาของ  user
                        $.get(`../../api/users/user/${user_id}`).done(function(data) {
                            console.log('user_id', data)
                            row.child(owe_by_user_id_format(data)).show();
                            tr.prop('shown');
                        });

                    }
                    if ($(this).hasClass('fa-plus-circle')) {
                        $(this).removeClass('fa-plus-circle')
                        $(this).removeClass('text-success')
                        $(this).addClass('fa-minus-circle')
                        $(this).addClass('text-info')

                        // aa(user_id, tr)

                    } else {
                        $(this).addClass('fa-plus-circle')
                        $(this).addClass('text-success')
                        $(this).removeClass('fa-minus-circle')
                        $(this).removeClass('text-info');
                    }

                });

            $('body').on('click', '.show_all_btn', function() {
                let _val = $(this).hasClass('all') ? 'all' : '';
                openAllChildTable(_val)
            })

            function openAllChildTable(_val) {
                if (_val === 'all') {
                    $("table > tbody > tr").each(function(index, val) {

                        var tr = $(this).closest('tr');
                        var row = table.row(tr);
                        let checked = tr.children().first().children().first()
                        if (checked.prop('checked') === false) {
                            checked.prop('checked', true)

                        }
                        tr.addClass('shown');
                        tr.addClass('selected')
                        $('.show_all_btn').removeClass('all')
                        $('.show_all_btn').text('ยกเลิกเลือกทั้งหมด')

                        $.get(`../../api/users/user/1}`).done(function(data) {
                            console.log('user_id', data)
                            row.child(owe_by_user_id_format(data)).show();
                            tr.prop('shown');
                        });
                    })

                } else {
                    $("table > tbody > tr").each(function() {
                        var tr = $(this).closest('tr');
                        let checked = tr.children().first().children().first()

                        var row = table.row(tr);
                        row.child.hide();
                        tr.removeClass('shown');
                        tr.removeClass('selected');
                        checked.prop('checked', false)

                        $('.show_all_btn').addClass('all')
                        $('.show_all_btn').text('เลือกทั้งหมด')
                        $('.show_detail').prop('checked', false);
                    })

                }

            }

            function owe_by_user_id_format(d) {

                let a = 0;
                let text = `
            <div class="table table-responsive  border border-success rounded ml-3 mr-3">
            <table class="table table-striped">
                <thead>
                    <tr class="bg-info">
                    <th class="text-center">วันที่</th>
                    <th class="text-center">รอบบิล</th>
                    <th class="text-center">ยอดครั้งก่อน</th>
                    <th class="text-center">ยอดปัจจุบัน</th>
                    <th class="text-center">จำนวนที่ใช้(หน่วย)</th>
                    <th class="text-center">คิดเป็นเงิน(บาท)</th>
                    <th class="text-center">vat(บาท)</th>
                    <th class="text-center">รวมทั้งสิ้น(บาท)</th>
                    <th class="text-center">สถานะ</th>
                    </tr>
                </thead>
                <tbody>`;
                d[0].usermeterinfos[0].invoice.forEach(element => {
                    let status
                    if (element.status === 'owe' || element.status === 'invoice') {
                        if (element.status == 'owe') {
                            status = `<span class="text-danger">ค้างชำระ</span>`
                        } else if (element.status == 'invoice') {
                            status = `<span class="text-warning">กำลังออกใบแจ้งหนี้</span>`
                        }

                        text += `
                                    <tr>
                                    <td class="text-center">${element.updated_at_th}</td>
                                    <td class="text-center">${element.invoice_period.inv_p_name}</td>
                                    <td class="text-right">${element.lastmeter}</td>
                                    <td class="text-right">${element.currentmeter}</td>
                                    <td class="text-right">${element.water_used }</td>
                                    <td class="text-right">${ element.paid }</td>
                                    <td class="text-right">${ element.vat }</td>
                                    <td class="text-right">${ element.totalpaid }</td>
                                    <td class="text-center">${status}</td>
                                    </tr>
                            `;
                        a = 1;

                    } // if
                });
                if (a === 0) {
                    console.log('sss')
                    text += `<tr><td colspan="7" class="text-center h4">ไม่พบข้อมูลการค้างชำระ</td></tr>`
                }
                text += `</tbody>
            </table>
            </div>`;
                return text;
            }
        </script>
    @endsection
