@extends('layouts.admin1')

@section('mainheader')
    ออกใบแจ้งเตือนชำระหนี้
@endsection
@section('nav')
    <a href="{{ 'owepaper/index' }}">ออกใบแจ้งเตือนชำระหนี้</a>
@endsection
@section('owepaper')
    active
@endsection
@section('style')
    <style>
        .hidden {
            display: none
        }
    </style>
@endsection

@section('content')

    <table id="example2" class="display" style="width:100%">

    </table>
    @if (collect($invoice_period)->count() == 0 && $oweInvCountGroupByUserId == 0)
        <div class="col-lg-6 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>ยังไม่ได้สร้างรอบบิลปัจจุบัน</h3>
                    <p>&nbsp;</p>
                </div>
                <div class="icon">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
                <a href="{{ url('invoice_period') }}" class="small-box-footer">สร้างรอบบิลปัจจุบัน
                    <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
    @else
        <div class="card">
            <div class="card-body">
                {{-- ค้นหา --}}
                <div class="info-box">
                    <span class="info-box-icon bg-info"><i class="fa fa-search"></i></span>
                    <div class="info-box-content">

                        <div class="row">
                            <div class="col-md-2">
                                <label class="control-label">หมู่ที่</label>
                                <select class="form-control" name="zone_id" id="zone_id">
                                    <option value="all" selected>ทั้งหมด</option>
                                    @foreach ($zones as $zone)
                                        <option value="{{ $zone->id }}">{{ $zone->zone_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="control-label">เส้นทาง</label>
                                <select class="form-control" name="subzone_id" id="subzone_id">
                                    <option value="all" selected>ทั้งหมด</option>

                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="control-label">&nbsp;</label>
                                <button type="button" class="form-control btn btn-primary searchBtn">ค้นหา</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row" id="main">

            <div class="col-md-12 col-xxl-5">
                <form action="{{ url('owepaper/print') }}" method="POST" onsubmit="return check();">
                    @csrf
                    <input type="hidden" name="from_view" value="owepaper">
                    <div class="card res">
                        <div class="card-header header hidden">
                            <div class="card-title">

                                <div class="row">

                                    <div class="info-box col-12">
                                        <span class="info-box-icon bg-warning"><i class="fas fa-users"></i></span>

                                        <div class="info-box-content">
                                            <span class="info-box-text" id="subzone_span"> </span>
                                            <span class="info-box-number">ค้าง <span
                                                    class="oweCount h5 text-warning"></span> คน</span>
                                        </div>
                                        <!-- /.info-box-content -->
                                    </div>
                                </div>
                            </div>
                            <div class="card-tools">
                                <input type="submit" class="btn btn-primary mb-3 float-right" id="print_multi_inv"
                                    value="ปริ้นใบแจ้งเตือนชำระหนี้ที่เลือก">
                            </div>
                        </div>

                        <div class="card-body table-responsive">
                            <div id="DivIdToExport">
                                <table id="oweTable" class="table text-nowrap" width="100%"></table>
                            </div>
                        </div>
                        <!--card-body-->
                        {{-- <div class="overlay"><i class="fas fa-2x fa-sync-alt fa-spin"></i></div> --}}

                    </div>
                </form>
            </div>

        </div>
    @endif


@endsection


@section('script')
    <script src="{{ asset('/datatables.1.10.20/dataTables.select.min.js') }}"></script>
  oweAndInvoiceCount  {{-- <script src="{{ asset('js/my_script.js') }}"></script> --}}
    <script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.html5.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.colVis.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.0/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/select/1.3.3/js/dataTables.select.min.js"></script>
    <script src="https://cdn.datatables.net/plug-ins/1.10.19/api/sum().js"></script>
    <script type="text/javascript" language="javascript"
        src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>

    {{-- <script
        src="https://www.jqueryscript.net/demo/Export-Html-Table-To-Excel-Spreadsheet-using-jQuery-table2excel/src/jquery.table2excel.js">
    </script> --}}
    <script src="https://cdn.datatables.net/select/1.3.3/js/dataTables.select.min.js"></script>
    {{-- <script src="{{ asset('/js/my_script.js') }}"></script> --}}
    <script>
        let table;
        let cloneThead = true
        let col_index = -1

        $(document).ready(function() {
            getOweInfos('all', 'all')
            let text = ``;


        })
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

        $('.searchBtn').click(function() {
            var zone_id = $('#zone_id').val()
            var subzone_id = $('#subzone_id').val()
            $('#oweTable').DataTable().destroy();

            getOweInfos(zone_id, subzone_id)

        })
        $('body').on('keyup', 'input[type="search"]', function() {
            setTimeout(() => {
                let name = $(this).val();
                findReciept(name)
            }, 200)
        })

        $('body').on('click', '.subzone_click', function() {
            var zone_id = $(this).data('zone_id')
            var subzone_id = $(this).data('subzone_id')
            $('#oweTable').DataTable().destroy();

            getOweInfos(zone_id, subzone_id)
        })

        function findReciept(_name) {
            $('.overlay').html('<div class="overlay"><i class="fas fa-2x fa-sync-alt fa-spin"></i></div>');
            let name = _name
            let txt = '';
            let total = 0;
            let i = 0;
            $('.cash_from_user').val(0)
            $('.cashback').val(0)
            $('.mustpaid').val(0)

            let user_id_string = name.toLowerCase().split('hs')[1];
            let user_id = parseInt(user_id_string)
            $('#user_id').val(user_id)
            $.get(`../api/invoice/${user_id}`).done(function(invoices) {
                $('#invAndOweLists').html(
                    '<div class="overlay"><i class="fas fa-2x fa-sync-alt fa-spin"></i></div>')
                if (invoices.length > 0) {
                    txt += `<div class="card card-success border border-success rounded">
                                <div class="card-header">
                                    <h3 class="card-title">รายการค้างชำระ</h3>
                                </div>
                                <div class="card-body p-0 ">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                            <th style="width: 10px">
                                                <input type="checkbox" id="checkAll" checked>
                                                </th>
                                            <th class="text-center">เลขใบ<br>แจ้งหนี้</th>
                                            <th class="text-center">เลขมิเตอร์</th>
                                            <th class="text-center">รอบบิล</th>
                                            <th class="text-center">ยอดครั้งก่อน</th>
                                            <th class="text-center">ยอดปัจจุบัน</th>
                                            <th class="text-center">จำนวน<br>ที่ใช้</th>
                                            <th class="text-center">ค่าใช้น้ำ<br>(บาท)</th>
                                            <th class="text-center">ค่ารักษามิเตอร์</th>
                                            <th class="text-center">เป็นเงิน<br>(บาท)</th>
                                            </tr>
                                        </thead>
                                        <tbody>`;
                    invoices.forEach(element => {
                        let status = element.status == 'owe' ? 'ค้างชำระ' : 'invoice';
                        let diff = element.currentmeter - element.lastmeter;
                        let _total = diff * 8;
                        let reserve = _total == 0 ? 10 : 0;
                        total += _total + reserve; //

                        txt += ` <tr>
                                                        <td><input type="checkbox"  checked  class="control-input invoice_id checkbox" data-inv_id="${element.id}" name="payments[${i}][on]">
                                                        </td>
                                                        <td>${element.id}</td>
                                                        <td>${element.usermeterinfos.meternumber}</td>
                                                        <td>${element.invoice_period.inv_period_name}</td>
                                                        <td>${element.lastmeter}</td>
                                                        <td>${element.currentmeter}</td>
                                                        <td>${diff}</td>
                                                        <td>${ _total }
                                                            <input type="hidden" name="payments[${i}][total]" value="${ _total }">
                                                            <input type="hidden" name="payments[${i}][iv_id]" value="${ element.id }">
                                                        </td>
                                                        <td>${reserve}</td>
                                                        <td class="total">${_total+ reserve}</td>

                                                    </tr>
                                            `;
                        i++;
                    }) //foreach

                    txt += `             </tbody>
                                    </table>
                                </div>
                            </div>`;


                    let address = `${invoices[0].usermeterinfos.user_profile.address}
                                 ${invoices[0].usermeterinfos.user_profile.zone.zone_name} \n
                                อำเภอ ${invoices[0].usermeterinfos.user_profile.district.district_name}\n
                                จังหวัด ${invoices[0].usermeterinfos.user_profile.province.province_name}`;

                    $('#feFirstName').html(invoices[0].usermeterinfos.user_profile.name);
                    $('#meternumber2').html(invoices[0].usermeterinfos.meternumber);
                    $('#feInputAddress').html(address);
                    $('#phone').html(invoices[0].usermeterinfos.user_profile.phone);

                    $('#payment_res').html(txt);
                    $('.modal').addClass('show')
                    $('.overlay').html('')
                }

                $('.mustpaid').val(total)

            });
        } //text

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
        })

        let init = 1;

        function getOweInfos(zone_id = '<?php echo $zone_id_selected; ?>', subzone_id = '<?php echo $subzone_id_selected; ?>') {
            let params = {
                zone_id: zone_id,
                subzone_id: subzone_id
            }
            // $('#zone_id').change(zone_id)
            $.get(`../../api/owepaper/testIndex`, params).done(function(data) {
                console.log('data', data)
                if (data.length === 0) {
                    $('.res').html('<div class="card-body h3 text-center">ไม่พบข้อมูล</div>')
                } else {

                    $('.oweCount').html(data.length)
                    $('.header').removeClass('hidden')

                    var zone_name = zone_id === 'all' ? 'ทั้งหมด' : $(`#zone_id option:selected`).text()
                    var subzone_name = subzone_id === 'all' ? 'ทั้งหมด' : $(`#subzone_id option:selected`).text()
                    $('#subzone_span').html(`<b>${zone_name}  -  ${subzone_name}</b>`)
                    table = $('#oweTable').DataTable({
                        responsive: true,
                        // order: false,
                        // searching:false,
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
                        data: data,
                        dom: 'lBfrtip',
                        buttons: ['excel', 'print'],
                        select: {
                            style: 'multi',
                        },
                        columns: [{
                                'title': '',
                                data: 'user_id',
                                orderable: false,
                                render: function(data) {
                                    return `
                                    <input type="checkbox" class="invoice_id" style="opacity:0"  name="user_id[${data}]">
                                    <i class="fa fa-plus-circle text-success findInfo" data-user_id="${data}"></i>`
                                }
                            },
                            {
                                'title': 'ชื่อ-สกุล',
                                data: 'user_profile.name'
                            },
                            {
                                'title': 'เลขมิเตอร์',
                                data: 'meternumber',
                                'className': 'meternumber text-center',
                            },
                            {
                                'title': 'บ้านเลขที่',
                                data: 'user_profile.address',
                                'className': 'text-center'
                            },
                            {
                                'title': 'หมู่',
                                data: 'zone.zone_name',
                                'className': 'text-center'
                            },

                            {
                                'title': 'เส้นทางจดมิเตอร์',
                                data: 'subzone.subzone_name',
                                'className': 'text-center'
                            },
                            {
                                'title': 'ค้าง(เดือน)',
                                data: 'owe_count',
                                'className': 'text-center'
                            },
                            {
                                'title': 'หมายเหตุ',
                                data: 'comment',
                                'className': 'text-center'
                            }



                        ],

                    }) //table
                    $('.dt-buttons').prepend(`
                        <button class="dt-button  buttons-html5 ml-5 show_all_btn all" >เลือกทั้งหมด</button>`)
                } //else


                $('.overlay').remove()
                $('#oweTable_filter').remove()

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

        

        $('#zone_id').change(function() {
            //get ค่าsubzone
            if ($(this).val() === "all") {
                $('#subzone_id').html(`<option value="all" selected>ทั้งหมด</option>`)
            } else {
                $.get(`../api/subzone/${$(this).val()}`)
                    .done(function(data) {
                        let text = '<option value="">เลือก</option>';
                        if (data.length > 1) {
                            text += `<option value="all">ทั้งหมด</option>`;
                        }
                        data.forEach(element => {
                            text += `<option value="${element.id}">${element.subzone_name}</option>`
                        });
                        $('#subzone_id').html(text)
                    });
            } //else
        });

        $(document).on('click', '#checkAll', function() {
            let res = $(this).prop('checked')
            $('.checkbox').prop('checked', res)
            checkboxclicked()
        })

        function showEmptyDataBox(val) {
            $('#activity').removeClass('hidden');
            $('.overlay').html('')
            txt = `<div class="text-center h3 mt-2"><b>${val}</b></div>
                    <div class="text-center text-success h4 mb-2">ไม่มีรายการค้างชำระ</div>`;

            $('#activity').html(txt)
        } //showEmptyDataBox

        $('.cash_from_user').keyup(function() {
            let mustpaid = $('.mustpaid').val()
            let cash_from_user = $(this).val()
            let cashback = cash_from_user - mustpaid

            $('.cashback').val(cashback)
            if (cash_from_user === "") {
                $('.cashback').val("")
            }
            if (cashback >= 0) {
                mustpaid == 0 ? $('.submitbtn').attr('style', 'display:none') : $('.submitbtn').attr('style',
                    'display:block')
            } else {
                $('.submitbtn').attr('style', 'display:none')
            }

        }); //$('.cash_from_user')

        $(document).on('click', '.checkbox', function() {
            checkboxclicked()
        }); //$(document).on('click','.checkbox',

        function checkboxclicked() {
            let _total = 0;
            // $('#checkAll').prop('checked', true)
            $('.checkbox').each(function(index, element) {
                if ($(this).is(":checked")) {
                    let sum = $(this).parent().siblings('.total').text()
                    _total += parseInt(sum)
                } else {
                    $('#checkAll').prop('checked', false)

                }
            });
            if ($('.cash_from_user').val() > 0) {
                let remain = $('.cash_from_user').val() - _total
                $('.cashback').val(remain)
            }

            if (_total == 0) {
                $('.cash_form_user').attr('readonly')
            } else if (_total > 0) {
                $('.cash_form_user').removeAttr('readonly')
            }

            $('.mustpaid').val(_total)
        }

        $(document).ready(function() {
            let _total = 0;
            $('.checkbox').each(function(index, element) {
                if ($(this).is(":checked")) {
                    let sum = $(this).parent().siblings('.total').text()
                    _total += parseInt(sum)
                }
            });
            $('.mustpaid').val(_total)
            $('.cash_from_user').val(0)
            $('.cashback').val(0)
        }); //$(document).ready(function()

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
    </script>
@endsection
