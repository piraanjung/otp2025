@extends('layouts.admin1')

@section('nav-invoice')
    active
@endsection


@section('style')
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <script src="https://cdn.datatables.net/plug-ins/1.13.6/pagination/select.js"></script>
@endsection

@section('content')
    <div class="card">
        <div class="card-body table-responsive">
            <form action="{{ route('invoice.store') }}" method="POST">
                @csrf
                <input type="submit" class="btn btn-primary col-2" id="print_multi_inv" value="บันทึก">
                <br><br>
                <table class="table  table-striped datatable" id="DivIdToPrint">
                    <thead class="bg-light">
                        <tr>
                            <th></th>
                            <th class="text-center">เลขมิเตอร์</th>
                            <th class="text-center">ชื่อ-สกุล</th>
                            <th class="text-center">บ้านเลขที่</th>
                            <th class="text-center">ยกยอดมา</th>
                            <th class="text-center">มิเตอร์ปัจจุบัน</th>
                            <th class="text-center">จำนวนสุทธิ</th>
                            <th class="text-center">ค่ารักษามาตร</th>
                            <th class="text-center">เป็นเงิน</th>
                        </tr>
                    </thead>

                    <tbody id="app">
                        <?php $i = 1; ?>
                        @foreach ($invoices as $invoice)
                            <tr data-id="{{ $i }}" class="data">
                                <th class="text-center" width="2%">
                                    <a href="javascript:void(0)" class="btn btn-outline-warning delbtn2"
                                        onclick="del('{{ $invoice->meter_id_fk_fk }}')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </th>
                                <td class="border-0 text-center">
                                    {{ $invoice->usermeterinfos->meternumber }}
                                    <input type="hidden" value="{{ $invoice->usermeterinfos->meternumber }}"
                                        name="data[{{ $i }}][meternumber]" data-id="{{ $i }}"
                                        id="meternumber{{ $i }}"
                                        class="form-control text-right meternumber border-primary text-sm text-bold"
                                        readonly>
                                    <input type="hidden" value="{{ $invoice->meter_id_fk_fk }}"
                                        name="data[{{ $i }}][meter_id]">
                                </td>
                                <td class="border-0 text-left">
                                    <span class="username" data-user_id="{{ $invoice->usermeterinfos->user_id }}"><i
                                            class="fas fa-search-plus"></i>
                                        {{ $invoice->usermeterinfos->user->firstname . ' ' . $invoice->usermeterinfos->user->lastname }}</span>
                                    <input type="hidden" name="data[{{ $i }}][user_id]"
                                        value="{{ $invoice->usermeterinfos->user_id }}">

                                </td>
                                <td class="text-center">
                                    {{ $invoice->usermeterinfos->user->address }}
                                    <input type="hidden" readonly class="form-control "
                                        value="{{ $invoice->usermeterinfos->user->address }}"
                                        name="data[{{ $i }}][address]">
                                </td>

                                <td class="border-0">
                                    <input type="text" value="{{ $invoice->currentmeter }}"
                                        name="data[{{ $i }}][lastmeter]" data-id="{{ $i }}"
                                        id="lastmeter{{ $i }}"
                                        data-price_per_unit="{{ $invoice->usermeterinfos->meter_type->price_per_unit }}"
                                        class="form-control text-right lastmeter">
                                </td>
                                <td class="border-0 text-right">
                                    <input type="text" value="0" name="data[{{ $i }}][currentmeter]"
                                        data-id="{{ $i }}" id="currentmeter{{ $i }}"
                                        data-price_per_unit="{{ $invoice->usermeterinfos->meter_type->price_per_unit }}"
                                        class="form-control text-right currentmeter border-success">

                                </td>
                                <td class="border-0 text-right">
                                    <input type="text" readonly class="form-control text-right water_used_net"
                                        id="water_used_net{{ $i }}" value="">
                                </td>
                                <td class="border-0 text-right">
                                    <input type="text" readonly class="form-control text-right meter_reserve_price"
                                        id="meter_reserve_price{{ $i }}" value="">
                                </td>

                                <td class="border-0 text-right">
                                    <input type="text" readonly class="form-control text-right total"
                                        id="total{{ $i }}" value="">
                                </td>
                            </tr>
                            <?php $i++; ?>
                        @endforeach
                    </tbody>
                </table>
            </form>
        </div>
    </div>
@endsection


@section('script')
    {{-- <script src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.print.min.js"></script> --}}
    {{-- <script src="https://cdn.datatables.net/buttons/1.7.0/js/dataTables.buttons.min.js"></script> --}}
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script> --}}
    {{-- <script src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.html5.min.js"></script> --}}
    {{-- <script src="https://www.ninenik.com/js/vfs_fonts.js"></script> --}}
    {{-- <script src="https://www.jqueryscript.net/demo/Export-Html-Table-To-Excel-Spreadsheet-using-jQuery-table2excel/src/jquery.table2excel.js"></script> --}}
    <script>
        let i = 0;

        function del(id) {
            let res = window.confirm('ต้องการลบข้อมูลใช่หรือไม่ !!!')
            if (res) {
                window.location.href = `/invoice/delete/${id}/ลบ`
            } else {
                return false
            }
        }
        //คำนวนเงินค่าใช้น้ำ
        $('.currentmeter').keyup(function() {
            let inv_id = $(this).data('id')
            let price_per_unit = $(this).data('price_per_unit')
            console.log(price_per_unit)
            let currentmeter = $(this).val()
            let lastmeter = $(`#lastmeter${inv_id}`).val()
            let net = currentmeter == '' ? 0 : currentmeter - lastmeter;
            let total = (net * price_per_unit) + check_meter_reserve_price(inv_id);
            $('#water_used_net' + inv_id).val(net)
            $('#total' + inv_id).val(total);
        });
        $('.lastmeter').keyup(function() {
            let inv_id = $(this).data('id')
            let price_per_unit = $(this).data('price_per_unit')
            let lastmeter = $(this).val()

            let currentmeter = $(`#currentmeter${inv_id}`).val()
            let net = currentmeter - lastmeter
            let total = (net * price_per_unit) + check_meter_reserve_price(inv_id);
            $('#water_used_net' + inv_id).val(net)
            $('#total' + inv_id).val(total);
        });

        function check_meter_reserve_price(inv_id) {
            let lastmeter = $(`#lastmeter${inv_id}`).val()
            let currentmeter = $(`#currentmeter${inv_id}`).val();

            let diff = currentmeter - lastmeter;

            let res = diff == 0 ? 10 : 0;
            $('#meter_reserve_price' + inv_id).val(res)
            return res;
        }

        var table = $('.datatable').DataTable({
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
        })

        $(document).ready(function() {
            $('.paginate_page').text('หน้า')
            let val = $('.paginate_of').text()
            $('.paginate_of').text(val.replace('of', 'จาก'));
            $('.datatable thead tr').clone().appendTo('.datatable thead');

            $('.datatable thead tr:eq(1) th').each(function(index) {
                var title = $(this).text();
                $(this).removeClass('sorting')
                $(this).removeClass('sorting_asc')
                if (index > 0 && index < 4) {
                    $(this).html(
                        `<input type="text" data-id="${index}" class="col-md-12" id="search_col_${index}" placeholder="ค้นหา" />`
                        );
                } else {
                    $(this).html('')
                }
            });

            $('.dataTables_filter').remove();

            let col_index = -1
            $('.datatable thead input[type="text"]').keyup(function() {
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
                    if (col === 3) {
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
        })

        //เพิ่มข้อมูลลงตาราง lists
        $(document).on('click', '.username', function() {
            let user_id = $(this).data('user_id')
            var tr = $(this).closest('tr');
            var row = table.row(tr);
            if (row.child.isShown()) {
                // This row is already open - close it
                row.child.hide();
                tr.removeClass('shown');
            } else {
                // Open this row
                //หาข้อมูลการชำระค่าน้ำประปาของ  user
                $.get(`/api/users/user/${user_id}`).done(function(data) {
                    row.child(format(data)).show();
                    tr.addClass('shown');
                });
            }
        })

        function format(d) {
            let text = `<table class="table table-striped">
                    <thead>
                        <tr>
                        <th>เลขมิเตอร์</th>
                        <th>วันที่</th>
                        <th>รอบบิล</th>
                        <th>ยอดครั้งก่อน</th>
                        <th>ยอดปัจจุบัน</th>
                        <th>จำนวนที่ใช้</th>
                        <th>คิดเป็นเงิน(บาท)</th>
                        <th>สถานะ</th>
                        </tr>
                    </thead>
                    <tbody>`;
            d[0].usermeterinfos.invoice.forEach(element => {
                console.log(element)

                let _status = 'รอบันทึกข้อมูล'
                if (element.status === "invoice") {
                    _status = '<button class="btn btn-sm btn-outline-info">ออกใบแจ้งหนี้แล้ว</button>'
                } else if (element.status === "paid") {
                    _status = '<button class="btn btn-sm btn-outline-success">ชำระเงินแล้ว</button>'
                } else if (element.status === "owe") {
                    _status = '<button class="btn btn-sm btn-outline-warning">ค้างชำระ</button>'
                }
                if (element.status !== 'init') {
                    text += `
                            <tr>
                            <td>${d[0].usermeterinfos.meternumber}</td>
                            <td>${element.updated_at_th}</td>
                            <td>${element.invoice_period.inv_p_name}</td>
                            <td>${element.lastmeter}</td>
                            <td>${element.currentmeter}</td>
                            <td>${element.currentmeter - element.lastmeter }</td>
                            <td>${(element.currentmeter - element.lastmeter)*8 }</td>
                            <td>${_status}</td>
                            </tr>
                    `;
                } //if
            });
            text += `</tbody>
                </table>`;
            return text;
        }

        function printtag(tagid) {
            var hashid = "#" + tagid;
            var tagname = $(hashid).prop("tagName").toLowerCase();
            var attributes = "";
            var attrs = document.getElementById(tagid).attributes;
            $.each(attrs, function(i, elem) {
                attributes += " " + elem.name + " ='" + elem.value + "' ";
            })
            var divToPrint = $(hashid).html();
            var head = "<html><head>" + $("head").html() + "</head>";
            var allcontent = head + "<body  onload='window.print()' >" + "<" + tagname + attributes + ">" + divToPrint +
                "</" + tagname + ">" + "</body></html>";
            var newWin = window.open('', 'Print-Window');
            newWin.document.open();
            newWin.document.write(allcontent);
            newWin.document.close();
            setTimeout(function() {
                newWin.close();
            }, 10);
        }
        $(".exportToExcel").click(function() {
            $("#DivIdToPrint").table2excel({
                // exclude CSS class
                exclude: ".noExl",
                name: "Worksheet Name",
                filename: "SomeFile", //do not include extension
                fileext: ".xls" // file extension
            });
        });
    </script>
@endsection
