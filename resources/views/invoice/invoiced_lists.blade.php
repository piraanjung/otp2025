@extends('layouts.admin1')

@section('nav-invoice')
    active
@endsection
@section('nav-header')
    จัดการใบแจ้งหนี้
@endsection
@section('nav-main')
    <a href="{{ route('invoice.index') }}"> ออกใบแจ้งหนี้</a>
@endsection
@section('nav-current')
    ปริ้นใบแจ้งหนี้
@endsection
@section('nav-topic')
    เส้นทาง:: {{ $subzone_id }}
    รอบบิลที่ <span id="invPeriod"></span> ปีงบประมาณ <span id="_budgetyear"></span>
@endsection
@section('style')
    <style>
        .selected {
            background: lightblue
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

        #print_multi_inv {
            margin-left: 50%
        }

        .table {
            border-collapse: collapse
        }

        .search_col {
            border-radius: 10px 10px;
            border: 1px solid lightskyblue;
            min-width: 90px;
        }
    </style>
@endsection
@section('content')
    <div class="card">
        <form action="{{ url('invoice/print_multi_invoice') }}" id="form" method="POST">
            @csrf
            <div class="card-body table-responsive">
                <input type="hidden" id="zone_id" name="zone_id" value="">
                <input type="hidden" id="subzone_id" name="subzone_id" value="">
                <input type="hidden" name="mode" id="mode" value="zone_info">
                @error('inv_id')
                    <div class="alert alert-warning alert-dismissible text-white" role="alert">
                        <h5>{{ $message }}</h5>
                        <button type="button" class="btn-close text-lg py-3 opacity-10" data-bs-dismiss="alert"
                            aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                @enderror
                <table id="oweTable" class="table " width="100%">
                    <tfoot>
                        <tr>
                            <td colspan="4" class="text-right h4">รวม</td>
                            <td style="border-bottom: 1px solid  #000000"></td>
                            <td style="border-bottom: 1px solid  #000000"></td>
                            <td style="border-bottom: 1px solid  #000000"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </form>

        <div class="overlay"><i class="fas fa-2x fa-sync-alt fa-spin"></i></div>

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
        let i = 0;
        //เพิ่มข้อมูลลงตาราง lists
        let count = 1;

        let table
        let cloneThead = true

        //getข้อมูลจาก api มาแสดงใน datatable
        $(document).ready(function() {
            getOweInfos()

        })

        function getOweInfos() {

            $.get(`/api/invoice/invoiced_lists/<?php echo $subzone_id; ?>`).done(function(data) {
                console.log('data', data.zoneInfo.undertake_subzone_id)
                $('#invPeriod').html(data.presentInvoicePeriod.inv_period_name)
                $('#_budgetyear').html(data.presentInvoicePeriod.budgetyear)
                $('#undertake_zone').html(data.zoneInfo.undertake_zone)
                $('#undertake_subzone').html(data.zoneInfo.undertake_subzone)
                $('#zone_id').val(data.zoneInfo.undertake_zone_id)
                $('#subzone_id').val(data.zoneInfo.undertake_subzone_id)
                if (data.length === 0) {

                    $('.res').html('<div class="card-body h3 text-center">ไม่พบข้อมูล</div>')
                } else {
                    table = $('#oweTable').DataTable({
                        responsive: true,
                        "pagingType": "listbox",
                        "lengthMenu": [
                            [10, 25, 50, 150, -1],
                            [10, 25, 50, 150, "ทั้งหมด"]
                        ],
                        dom: 'lBfrtip',
                        buttons: [{
                            extend: 'excelHtml5',
                            'text': 'Excel',
                            exportOptions: {
                                rows: ['.selected']
                            }
                        }],
                        "language": {
                            "search": "ค้นหา:",
                            "lengthMenu": "แสดง _MENU_ แถว",
                            "info": "แสดง _START_ ถึง _END_ จาก _TOTAL_ แถว",
                            "infoEmpty": "แสดง 0 ถึง 0 จาก 0 แถว",
                            "paginate": {
                                "info": "แสดง _MENU_ แถว",
                            },

                        },
                        data: data.invoicedlists,
                        columns: [{
                                'title': 'เลขใบแจ้งหนี้',
                                data: function(data) {
                                    return `${data.id}<input type="hidden" value="${data.id}" name="zone[${data.id}][iv_id]" data-id="${data.id}"
                                    id="iv_id${data.id}" class="form-control text-right iv_id">
                                    <input type="checkbox" class="control-input invoice_id" name="inv_id[${data.id}]" id="inv_id[${data.id}]"
                                            data-id="${data.id}" style="opacity: 10">
                                    `
                                },
                                'className': 'text-center'

                            },
                            {
                                'title': 'เลขที่',
                                data: function(data) {
                                    return `<center>${data.meternumber}&nbsp;&nbsp;</center><input type="hidden" value="${data.meternumber}" name="zone[${data.id}][meter_id]">`
                                },
                            },
                            {
                                'title': 'ชื่อ-สกุล',
                                data: function(data) {
                                    return `${data.firstname} ${data.lastname}`
                                }
                            },
                            {
                                'title': 'บ้านเลขที่',
                                data: 'address',
                                'className': 'text-center'
                            },
                            {
                                'title': 'ยกยอดมา',
                                data: function(data) {
                                    return `${data.lastmeter.toLocaleString()}`
                                },
                                'className': 'text-right'
                            },
                            {
                                'title': 'มิเตอร์ปัจจุบัน',
                                data: function(data) {
                                    return `${data.currentmeter.toLocaleString()}`
                                },
                                'className': 'text-right'
                            },
                            {
                                'title': 'ใช้น้ำ(หน่วย)',
                                data: function(data) {
                                    return `${data.meter_net.toLocaleString()}`
                                },
                                'className': 'text-right'
                            },
                            {
                                'title': 'เป็นเงิน(บาท)',
                                data: function(data) {
                                    let amount = parseInt(data.total) == 0 ? 10 : data.total;
                                    return `${amount.toLocaleString()}`
                                },
                                'className': 'text-right'
                            },



                        ],
                        "footerCallback": function(row, data, start, end, display) {
                            var api = this.api(),
                                data;
                            // Remove the formatting to get integer data for summation
                            var intVal = function(i) {
                                return typeof i === 'string' ?
                                    i.replace(/[\$,]/g, '') * 1 :
                                    typeof i === 'number' ?
                                    i : 0;
                            };

                            // var nf = new Intl.NumberFormat();
                            for (let i = 4; i <= 7; i++) {

                                // แถวค่าน้ำ
                                total_water_price = api
                                    .column(i)
                                    .data()
                                    .reduce(function(a, b) {
                                        return intVal(a) + intVal(b);
                                    }, 0);

                                // Total over this page
                                pageTotal_water_price = api
                                    .column(i, {
                                        page: 'current'
                                    })
                                    .data()
                                    .reduce(function(a, b) {
                                        return intVal(a) + intVal(b);
                                    }, 0);

                                // Update footer
                                $(api.column(i).footer()).html(
                                    pageTotal_water_price.toLocaleString() + ' ( ทั้งหมด: ' +
                                    total_water_price.toLocaleString() + ' )'
                                );
                            }
                        },

                    }) //table
                    // ทำการ clone thead แล้วสร้าง input text
                    if (cloneThead) {
                        $('#oweTable thead tr').clone().appendTo('#oweTable thead');
                        cloneThead = false
                    }
                    $('#oweTable thead tr:eq(1) th').each(function(index) {
                        var title = $(this).text();
                        $(this).removeClass('sorting')
                        $(this).removeClass('sorting_asc')
                        if (index < 4) {
                            $(this).html(
                                `<input type="text" data-id="${index}" class="col-md-12 search_col" id="search_col_${index}" placeholder="ค้นหา" />`
                            );
                        } else {
                            $(this).html('')
                        }
                    });
                } //else
                $('.overlay').remove()
                $('#oweTable_filter').remove();

                //custom การค้นหา
                let col_index = -1

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
                        if (col === 0 || col === 3) {
                            var val = $.fn.dataTable.util.escapeRegex(
                                _val
                            );
                            table.column(col)
                                .search(val ? '^' + val + '.*' : '', true, false)
                                .draw();
                        } else {
                            table.column(col)
                                .search(_val)
                                .draw();
                        }
                    }, 300);

                    col_index = col

                })

                $('.dt-button').addClass('btn btn-sm btn-info')
                $('.dt-buttons').prepend('<label class="m-0">ดาวน์โหลด:</label>')

                $(`<div class="deselect_row_all">
                    <label class="m-0">ยกเลิกเลือกทั้งหมด:</label>
                    <button type="button" class="btn btn-secondary btn-sm" id="deselect-all">ตกลง</button>
                </div>
                <div class=" select_row_all">
                    <label class="m-0">เลือกทั้งหมด:</label>
                    <button type="button" class="btn btn-success btn-sm" id="deselect-all">ตกลง</button>
                </div>`).insertAfter('.dataTables_length')

                $(`<input type="submit" class="btn btn-primary mt-6"  id="print_multi_inv" value="ปริ้นใบแจ้งหนี้ผู้ใช้ที่เลือก">`)
                    .insertAfter('.dt-buttons')

            }) //.get

        } //function getOweInfos

        $(document).on('click', 'tbody tr', function(e) {
            $(this).hasClass('selected') ? $(this).removeClass('selected') : $(this).addClass('selected');
        });
        $(document).on('click', '#deselect-all', function(e) {
            $("tbody tr.selected").removeClass('selected')
            $('.invoice_id').prop('checked', false)

        });
        $(document).on('click', '.select_row_all', function(e) {
            $("tbody tr").addClass('selected')
            $('.invoice_id').prop('checked', true)

        });
    </script>
@endsection
