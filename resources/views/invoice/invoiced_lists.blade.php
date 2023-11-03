@extends('layouts.admin1')

@section('nav-invoice')
    active
@endsection

@section('content')
    <div class="card">
        <form action="{{ url('invoice/print_multi_invoice') }}" method="POST">
            @csrf
            <div class="card-header">
                <div class="card-title">รอบบิลที่ <span id="invPeriod"></span> ปีงบประมาณ <span id="_budgetyear"></span>
                </div>
                <div class="card-tools">
                    <input type="submit" class="btn btn-primary" id="print_multi_inv" value="ปริ้นใบแจ้งหนี้ผู้ใช้ที่เลือก">
                </div>
            </div>
            <div class="card-body table-responsive">

                <input type="hidden" id="zone_id" name="zone_id" value="">
                <input type="hidden" id="subzone_id" name="subzone_id" value="">
                <input type="hidden" name="mode" id="mode" value="zone_info">
                @error('inv_id')
                <div class="alert alert-warning alert-dismissible text-white" role="alert">
                    <h5>{{$message}}</h5>
                    <button type="button" class="btn-close text-lg py-3 opacity-10" data-bs-dismiss="alert" aria-label="Close">
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
    <script src="https://cdn.datatables.net/select/1.3.3/js/dataTables.select.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.0/js/dataTables.buttons.min.js"></script>

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
                        // order: false,
                        // searching:false,
                        "pagingType": "listbox",
                        "lengthMenu": [
                            [10, 25, 50, 150, -1],
                            [10, 25, 50, 150, "ทั้งหมด"]
                        ],
                        dom: 'lBfrtip',
                        buttons: [{
                                text: 'เลือกทั้งหมด',
                                className: 'show_all_btn'

                            },

                        ],
                        select: {
                            style: 'multi'
                        },
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
                        columns: [
                            {
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
                                data: function(data){
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
                                `<input type="text" data-id="${index}" class="col-md-12" id="search_col_${index}" placeholder="ค้นหา" />`
                                );
                        } else {
                            $(this).html('')
                        }
                    });
                } //else
                $('.overlay').remove()
                $('#oweTable_filter').remove();
                $('.dt-buttons').addClass('ml-3')

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
                                .search(val ? '^' + val + '.*' :'', true, false)
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

        } //function getOweInfos


        $(document).ready(function() {

            $('.paginate_page').text('หน้า')
            let val = $('.paginate_of').text()
            $('.paginate_of').text(val.replace('of', 'จาก'));
        })

        $('#checkall').change(function() {
            if ($(this).is(":checked")) {
                $('.invoice_id').each(function(index, ele) {
                    $(ele).prop('checked', true)
                })
            } else {
                $('.invoice_id').each(function(index, ele) {
                    $(ele).prop('checked', false)
                })
            }
        })

        $('body').on('click', 'tbody tr', function() {
            $('.select-item').text('')

            let checked = $(this).children().first().children().last()
            if (checked.prop('checked') === false) {
                checked.prop('checked', true)
                $(this).addClass('selected')
            } else {
                $(this).removeClass('selected')
                checked.prop('checked', false)
            }
        })

        $('body').on('click', '.show_all_btn', function() {
            let _val = $(this).hasClass('all') ? 'all' : '';
            openAllChildTable(_val)
        })

        function openAllChildTable(_val) {
            if (_val === 'all') {
                $("table > tbody > tr[role='row']").each(function(index, val) {

                    var tr = $(this).closest('tr');
                    var row = table.row(tr);
                    let checked = tr.children().first().children().last()
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
                    let checked = tr.children().first().children().last()

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
    </script>
@endsection
