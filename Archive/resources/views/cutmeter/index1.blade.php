@extends('layouts.admin1')

@section('mainheader')
    ค้างชำระเกิน 3 รอบบิล
@endsection
@section('nav')
    <a href="{{ 'cutmeter/index' }}">ค้างชำระเกิน 3 รอบบิล</a>
@endsection
@section('cutmeter')
    active
@endsection
@section('style')
    <link href="https://cdn.datatables.net/buttons/1.5.1/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css" />
    <link href="https://nightly.datatables.net/css/jquery.dataTables.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://unpkg.com/bootstrap-table@1.21.4/dist/bootstrap-table.min.css">
    <style>
        .hidden {
            display: none
        }
    </style>
@endsection

@section('content')
    <div class="row" id="main">
        <div class="col-md-12">
            {{-- ส่งข้อมูลที่เลือกไปปริ้น --}}
            <form action="{{ url('owepaper/print') }}" method="POST" onsubmit="return check();">
                @csrf
                <input type="hidden" name="from_view" value="cutmeter">
                <div class="card">
                    <div class="card-header header">
                        <div class="card-title">

                            <div class="row">

                                <div class="info-box col-12">
                                    <span class="info-box-icon bg-warning"><i class="fas fa-users"></i></span>

                                    <div class="info-box-content">
                                        <span class="info-box-text" id="subzone_span"> </span>
                                        <span class="info-box-number">ค้าง <span class="oweCount h5 text-warning"></span>
                                            คน</span>
                                    </div>
                                    <!-- /.info-box-content -->
                                </div>
                            </div>
                        </div>
                        <div class="card-tools">
                            <input type="submit" class="btn btn-primary " id="print_multi_inv" value="ปริ้นใบแจ้งหนี้">

                        </div>
                    </div>

                    <div class="card-body table-responsive">
                        <div>จำนวนคนค้าง {{ collect($UserOweCountOver2Times)->count() }} คน</div>
                        <table id="table" data-toolbar="#toolbar" data-search="true" data-show-refresh="true"
                            data-show-toggle="true" data-show-fullscreen="true" data-show-columns="true"
                            data-show-columns-toggle-all="true" data-detail-view="true" data-show-export="true"
                            data-click-to-select="true" data-detail-formatter="detailFormatter"
                            data-minimum-count-columns="2" data-show-pagination-switch="true" data-pagination="true"
                            data-id-field="id" data-page-list="[10, 25, 50, 100, all]" data-show-footer="true",
                            data-locale="th-TH", data-response-handler="responseHandler">
                            <thead>
                                <tr>
                                    <th>user id</th>
                                    <th>name</th>
                                    <th>address></th>
                                    <th>zone</th>
                                    <th>count</th>
                                    <th>status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($UserOweCountOver2Times as $item)
                                    <tr>
                                        <th>{{ $item->user_id }}</th>
                                        <th>{{ $item->firstname }}</th>
                                        <th>{{ $item->address }}</th>
                                        {{-- <td>{{ $item->user->user_zone->zone_name }}</td> --}}
                                        <th>{{ $item->owe_count }}</th>
                                        <td>
                                            {{-- {{ dd($item->cutmeterHistory[0]->status) }} --}}
                                            @if (collect($item->cutmeterHistory)->isEmpty())
                                            <a href="javascript:void(0)" class="btn btn-info edit_btn"
                                            data-user_id="{{ $item->id }}" data-process="cutmeter">ทำการตัดมิเตอร์</a>
                                            @elseif($item->cutmeterHistory[0]->status == 1)<!-- ตัดมิเตอร์แล้ว -->
                                            <button class="btn btn-warning " readonly
                                            data-user_id="{{ $item->id }}">ตัดมิเตอร์แล้ว รอชำระเงิน</button>
                                            @endif

                                        </td>
                                    </tr>
                                @endforeach

                            </tbody>
                        </table>
                        <div id="DivIdToExport">
                            <table id="oweTable" class="table text-nowrap res" width="100%">
                            </table>
                        </div>
                    </div>


                </div>
            </form>
        </div>
        <!--col-md-12-->
    </div>
    <!--row-->
@endsection


@section('script')
    <script
        src="{{ asset('/js/demo-Export-Html-Table-To-Excel-Spreadsheet-using-jQuery-table2excel/src/jquery.table2excel.js') }}">
    </script>
    <script src="{{ asset('/datatables.1.10.20/dataTables.select.min.js') }}"></script>
    <script src="{{ asset('js/my_script.js') }}"></script>

    <script type="text/javascript" language="javascript"
        src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>


    <script src="https://cdn.jsdelivr.net/npm/tableexport.jquery.plugin@1.10.21/tableExport.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/tableexport.jquery.plugin@1.10.21/libs/jsPDF/jspdf.min.js"></script>
    <script
        src="https://cdn.jsdelivr.net/npm/tableexport.jquery.plugin@1.10.21/libs/jsPDF-AutoTable/jspdf.plugin.autotable.js">
    </script>
    <script src="https://unpkg.com/bootstrap-table@1.21.4/dist/bootstrap-table.min.js"></script>
    <script src="https://unpkg.com/bootstrap-table@1.21.4/dist/extensions/export/bootstrap-table-export.min.js"></script>

    <script>
        let table;
        let cloneThead = true
        let col_index = -1


        $(document).ready(function() {
            $('.paginate_page').html('หน้า')
            let val = $('.paginate_of').text()
            $('.paginate_of').text(val.replace('of', 'จาก'));

            //เอาค่าผลรวมไปแสดงตารางบนสุด
            $('#meter_unit_used_sum').html($('.meter_unit_used_sum').val())
            $('#owe_total_sum').html($('.owe_total_sum').val())
            $('#reserve_total_sum').html($('.reserve_total_sum').val())
            $('#all_total_sum').html($('.all_total_sum').val())
        }) //document



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
        let hasDataTable = 0
        $('.searchBtn').click(function() {
            let zone_id = $('#zone_id').val()
            let subzone_id = $('#subzone_id').val()
            console.log('sss', subzone_id)
            if (zone_id !== 'all' && subzone_id === '') {
                alert('ยังไม่ได้เลือกเส้นทาง')
                $('#subzone_id').addClass('border border-danger rounded')
                return false
            }
            if (hasDataTable === 1) {
                $('#oweTable').DataTable().destroy();

            }

            cutmeterInfos(zone_id, subzone_id)
        });

        function cutmeterInfos(zone_id = '<?php echo $zone_id_selected; ?>', subzone_id = '<?php echo $subzone_id_selected; ?>') {
            let params = {
                zone_id: zone_id,
                subzone_id: subzone_id
            }

            $.get(`../../api/cutmeter/index`, params).done(function(data) {
                if (data.length === 0) {
                    $('.res').html('<div class="card-body h3 text-center">ไม่พบข้อมูล</div>')
                } else {
                    $('.oweCount').html(data.length)
                    $('.header').removeClass('hidden')
                    var zone_name = zone_id === 'all' ? 'ทั้งหมด' : data[0].zone_name
                    var subzone_name = subzone_id === 'all' ? 'ทั้งหมด' : data[0].subzone_name
                    $('#subzone_span').html(`<b>${zone_name}  -  ${subzone_name}</b>`)
                    table = $('#oweTable').DataTable({
                        responsive: true,
                        // order: false,
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
                                "info": "แสดง _MENU_ แถว",
                            },

                        },
                        dom: 'lBfrtip',
                        buttons: ['excel', 'print'],
                        select: {
                            style: 'multi',
                        },
                        data: data,
                        columns: [

                            {
                                'title': '',
                                data: 'user_id',
                                orderable: false,
                                render: function(data) {
                                    return `
                                    <input type="checkbox" class="invoice_id" style="opacity:0"
                                            name="user_id[${data}]">
                                    <i class="fa fa-plus-circle text-success findInfo"
                                            data-user_id="${data}"></i>`
                                }
                            },

                            {
                                'title': 'ชื่อ-สกุล',
                                data: 'name',
                            },
                            {
                                'title': 'เลขมิเตอร์',
                                data: 'meternumber',
                                'className': 'text-center meternumber',
                            },
                            {
                                'title': 'บ้านเลขที่',
                                data: 'address',

                                'className': 'text-right',
                            },
                            {
                                'title': 'หมู่ที่',
                                data: 'zone_name',
                                'className': 'text-center',
                            },
                            {
                                'title': 'เส้นทาง',
                                data: 'subzone_name',
                                'className': 'text-center',
                            },
                            {
                                'title': 'ค้างจ่าย(เดือน)',
                                data: 'owe_count',
                                'className': 'text-center',
                                render: function(data) {
                                    if (data > 0) {
                                        return `${data}`
                                    } else {
                                        return `${data}
                                    ( <a href="javascript:void(0)" class="cutmeter_reset">reset</a> )
                                    `
                                    }

                                }
                            }, {
                                'title': 'สถานะ',
                                data: 'cutmeter_status',
                                'className': 'text-center',
                            },
                            {
                                'title': '',
                                data: 'user_id',
                                orderable: false,
                                render: function(data) {
                                    return `
                                    <a href="javascript:void(0)" class="btn btn-info edit_btn" data-user_id="${data}">จัดการสถานะมิเตอร์</a>
                                    `
                                }
                            },
                            {
                                'title': '',
                                data: 'user_id',
                                orderable: false,
                                render: function(data, type, row) {
                                    if (row.cutmeter_status !==
                                        `<button class="btn btn-block btn-outline-warning disabled">รอดำเนินการถอดมิเตอร์</button>`
                                        ) {
                                        return `
                                    <a href="javascript:void(0)" class="btn btn-warning cutmeter_history" data-user_id="${data}">ผู้รับผิดชอบ</a>`
                                    } else {
                                        return "";
                                    }

                                }
                            }


                        ],


                    }) //table
                    $('.dt-buttons').prepend(`
                        <button class="dt-button  buttons-html5 ml-5 show_all_btn all" >เลือกทั้งหมด</button>`)
                } //else
                $('.overlay').remove()
                $('.dataTables_filter').remove()
                if (cloneThead) {
                    $('#oweTable thead tr').clone().appendTo('#oweTable thead');
                    cloneThead = false
                }
                $('#oweTable thead tr:eq(1) th').each(function(index) {
                    var title = $(this).text();
                    $(this).removeClass('sorting')
                    $(this).removeClass('sorting_asc')
                    if (index > 0 && index <= 6) {
                        $(this).html(
                            `<input type="text" data-id="${index}" class="col-md-12" id="search_col_${index}" placeholder="ค้นหา" />`
                            );
                    } else {
                        $(this).html('')
                    }
                });

                $('#oweTable thead input[type="text"]').keyup(function() {
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
                        if (col > 2 && col <= 5) {
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

            }) //.get
        }

        $('body').on('click', '.subzone_click', function() {
            var zone_id = $(this).data('zone_id')
            var subzone_id = $(this).data('subzone_id')
            $('#oweTable').DataTable().destroy();

            cutmeterInfos(zone_id, subzone_id)
        })

        function check() {
            var res = 0;
            $('.invoice_id').each(function(index, ele) {
                if ($(this).is(":checked")) {
                    res = 1;
                    return false;
                }
            })
            if (res === 0) {
                alert('กรุณาเลือกผู้ใช้น้ำที่ต้องการออกใบแจ้งเตือนชำระหนี้')
                return false
            } else {
                return true
            }
        }

        $('body').on('click', '.show_all_btn', function() {
            let _val = $(this).hasClass('all') ? 'all' : '';
            openAllChildTable(_val)
        })

        $('body').on('click', '.edit_btn', function() {
            let user_id = $(this).data('user_id')
            let process = $(this).data('process')
            console.log(process)
            let cutmeter_id = parseInt($('#cutmeter_id' + user_id).val());
            console.log('cutmeter_id', cutmeter_id)
            if (process != "cutmeter") {
                window.location.href = `/cutmeter/edit/${user_id}`;
            } else {
                window.location.href = `/cutmeter/create/${user_id}`;
            }
        })

        $('body').on('click', '.cutmeter_history', function() {
            let user_id = $(this).data('user_id')
            var tr = $(this).closest('tr');
            var row = table.row(tr);
            if (row.child.isShown()) {
                // This row is already open - close it
                row.child.hide();
                tr.removeClass('shown');
            } else {
                $.get(`../api/cutmeter/get_cutmeter_history/${user_id}`).done(function(data) {
                    console.log('api/cutmeter/get_cutmeter_history', data)
                    row.child(show_cutmeter_history_by_user_format(data)).show();
                    tr.prop('shown');
                })
            }
        })

        function openAllChildTable(_val) {
            if (_val === 'all') {
                $("table > tbody > tr[role='row']").each(function(index, val) {

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
                })

            } else {
                $("table > tbody > tr[role='row']").each(function() {
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


        $('body').on('click', '.cutmeter_reset', function() {
            let user_id = parseInt($(this).parent().siblings('.meternumber').text().substring(2))
            window.location.href = `/cutmeter/reset_cutmeter_status/${user_id}`;
        })
    </script>

    <script>
        var $table = $('#table')
        var $remove = $('#remove')
        var selections = []

        function getIdSelections() {
            return $.map($table.bootstrapTable('getSelections'), function(row) {
                return row.id
            })
        }

        function responseHandler(res) {
            $.each(res.rows, function(i, row) {
                row.state = $.inArray(row.id, selections) !== -1
            })
            return res
        }

        function detailFormatter(index, row) {
            var html = []
            $.get(`../api/cutmeter/get_cutmeter_history/1879`).done(function(data) {
                console.log('datata;', data)
                // html.push('<p><b>22å' + key + ':</b> ' + value + '</p>')

            })
            $.each(row, function(key, value) {

            })
            return html.join('')
        }

        function operateFormatter(value, row, index) {
            return [
                '<a class="like" href="javascript:void(0)" title="Like">',
                '<i class="fa fa-heart"></i>',
                '</a>  ',
                '<a class="remove" href="javascript:void(0)" title="Remove">',
                '<i class="fa fa-trash"></i>',
                '</a>'
            ].join('')
        }

        window.operateEvents = {
            'click .like': function(e, value, row, index) {
                alert('You click like action, row: ' + JSON.stringify(row))
            },
            'click .remove': function(e, value, row, index) {
                $table.bootstrapTable('remove', {
                    field: 'id',
                    values: [row.id]
                })
            }
        }

        function totalTextFormatter(data) {
            return 'Total'
        }

        function totalNameFormatter(data) {
            return data.length
        }

        function totalPriceFormatter(data) {
            var field = this.field
            return '$' + data.map(function(row) {
                return +row[field].substring(1)
            }).reduce(function(sum, i) {
                return sum + i
            }, 0)
        }

        function initTable() {
            $table.bootstrapTable('destroy').bootstrapTable({
                // exportDataType: $(this).val(),
                exportTypes: ['excel', 'pdf'],
                height: 550,
                locale: $('#locale').val(),

            })
            $table.on('check.bs.table uncheck.bs.table ' +
                'check-all.bs.table uncheck-all.bs.table',
                function() {
                    $remove.prop('disabled', !$table.bootstrapTable('getSelections').length)

                    // save your data, here just save the current page
                    selections = getIdSelections()
                    // push or splice the selections if you want to save all data selections
                })
            $table.on('all.bs.table', function(e, name, args) {
                console.log(name, args)
            })
            $remove.click(function() {
                var ids = getIdSelections()
                $table.bootstrapTable('remove', {
                    field: 'id',
                    values: ids
                })
                $remove.prop('disabled', true)
            })
        }

        $(function() {
            initTable()

            $('#locale').change(initTable)
        })
    </script>
@endsection
