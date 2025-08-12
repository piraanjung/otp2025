@extends('layouts.admin1')
@section('nav-reports-ledger')
    active
@endsection

@section('nav-header')
    รายงาน
@endsection
@section('nav-main')
    <a href="{{ route('reports.ledger') }}"> เล็ดเยอร์รายตัวลูกหนี้(ป.17)</a>
@endsection

@section('page-topic')
ตารางเล็ดเยอร์รายตัวลูกหนี้(ป.17)
@endsection


@section('style')
    <style>
        .hidden {
            display: none
        }

        td,
        table thead th {
            text-align: left;
            color: black;
            border: 1px solid black
        }
    </style>
    <link href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
    <link href="https://cdn.datatables.net/buttons/1.5.1/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css" />
@endsection

@section('content')
    <div class="preloader-wrapper">
        <button class="btn btn-primary btn-sm mb-2" type="button" disabled>
            <span class="spinner-border spinner-border-sm" role="status"></span>
            Loading...
        </button>
    </div>
    <form action="{{ url('reports/ledger') }}" method="get" onsubmit="return checkValues();">
        @csrf
        <div class="card">
            <div class="card-body">
                <div class="info-box">
                    <div class="info-box-content">
                        <div class="row">

                            <div class="col-md-3">
                                <div class="form-group row">
                                    <label for="search" class="col-sm-6 col-form-label">ปีงบประมาณ:</label>
                                    <div class="col-sm-6">
                                        <select class="form-control" name="budgetyear_id" id="budgetyear_id">
                                            <option value="all">ทั้งหมด</option>
                                            @foreach ($budgetyear_list as $list)
                                                <option value="{{ $list->id }}"
                                                    {{ $list->id == $budgetyear_selected[0]->id ? 'selected' : '' }}>
                                                    {{ $list->budgetyear_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group row">
                                    <label for="search" class="col-sm-5 col-form-label">รอบบิลที่:</label>
                                    <div class="col-sm-7">
                                        <select class="form-control" name="inv_period_id" id="inv_period_id">
                                            <option value="all">ทั้งหมด</option>

                                            @if (collect($budgetyear_selected)->isNotEmpty())
                                                @foreach ($budgetyear_selected[0]->invoicePeriod as $item)
                                                    <option value="{{ $item->id }}"
                                                        {{ $item->id == $current_inv_period[0]->id ? 'selected' : '' }}>
                                                        {{ $item->inv_p_name }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group row">
                                    <label for="search" class="col-sm-5 col-form-label">สถานะ:</label>
                                    <div class="col-sm-7">
                                        <select class="form-control" name="status" id="status">
                                            <option value="all">ทั้งหมด</option>
                                            <option value="init">รอบันทึกข้อมูล</option>
                                            <option value="paid">ชำระเงินแล้ว</option>
                                            <option value="owe">ค้างชำระ</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <label for="search" class="col-sm-5 col-form-label">&nbsp;</label>

                                <button type="submit" id="searchBtn" class="form-control btn btn-primary">ค้นหา</button>
                            </div>
                        </div><!--row-->


                    </div><!-- /.info-box-content -->
                </div><!--info-box-->
            </div><!--card-body-->
        </div><!--card-->
    </form>

    <div class="card mt-2">
        <div class="card-header"></div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="oweTable" class="table" width="100%">
                    <thead>
                        <tr>
                            <th colspan="17">ประจำเดือน {{ $current_inv_period[0]['inv_p_name'] }}</th>

                        </tr>
                        <tr>
                            <th rowspan="3">ผู้ใช้น้ำ<br>ประปาเลขที่</th>
                            <th rowspan="3">ชื่อ-สกุล</th>
                            <th rowspan="3">บิลที่</th>
                            <th colspan="9">หนี้สินที่เกิดขึ้นในเดือนนี้</th>

                            <th rowspan="3">รวม</th>

                            <th colspan="3" rowspan="1">การชำระหนี้เดือนนี้ </th>
                            <th rowspan="3">คงค้างยก<div>ไปเดือนหน้า</div>

                            </th>
                        </tr>
                        {{-- tr 1 --}}
                        <tr>
                            <th colspan="2">เลขอ่านของมาตรวัด</th>
                            <th rowspan="2">จำนวนหน่วย</th>
                            <th rowspan="2">คิดเป็นเงิน</th>
                            <th rowspan="2">เพิ่มให้เต็ม<div>อัตราอย่างต่ำ</div>
                            </th>
                            <th rowspan="2">ค่าบริการ</th>
                            <th rowspan="2">ภาษีมูลค่า<div>เพิ่ม 7%</div>
                            </th>
                            <th rowspan="2">รวมเป็นเงิน</th>

                            <th rowspan="2">คงค้างยกมา<div>แต่เดือนก่อน</div>
                            </th>
                            <th rowspan="2">วันที่</th>
                            <th rowspan="2">หน้าบัญชี<div>เงินสด</div>
                            </th>
                            <th rowspan="2">จำนวนเงินที่ชำระ</th>


                        </tr>
                        {{-- tr 2 --}}
                        <tr>
                            <th>จาก</th>
                            <th>ถึง</th>



                        </tr>
                        {{-- tr 3 --}}
                        {{-- <tr>
                            <th>จาก</th>
                            <th>ถึง</th>
                        </tr> --}}

                    </thead>

                    <tbody>

                        @foreach ($ledgers as $key => $infos)
                            <?php
                            $sum_diff = 0;
                            $sum_meter_reserve_price = 0;
                            $sum__total = 0;
                            $sum_total = 0;
                            $paid_amount = 0;
                            $status = '';
                            $bg ='';
                            $sum = 0;
                            $min_rate = 0;
                            if (collect($infos->invoice)->isNotEmpty()) {
                                $min_rate = $infos->invoice[0]->water_used == 0 ? 10 : 0;
                            }
                            $prev_owe_amount = collect($infos['invoice_by_user_id'])->isEmpty() ? 0 : $infos['invoice_by_user_id'][0]->totalpaid;

                            if (isset($infos->invoice[0]['status'])) {
                                $bg = $prev_owe_amount > 0 ? '#feeaf1' : '#c1e5ff';
                            }
                            ?>
                            <tr class="" style="background-color:{{ $bg }};">

                                <td class="text-right">
                                    {{ $infos['user_id'] }}
                                </td><!-- user_id -->
                                <td>
                                    @php
                                        $prefix = !isset($infos->user->prefix) ? '' : $infos->user->prefix;
                                        $firstname = !isset($infos->user->firstname) ? '' : $infos->user->firstname;
                                        $lastname = !isset($infos->user->lastname) ? '' : $infos->user->lastname;
                                    @endphp
                                    {{ $prefix . '' . $firstname . ' ' . $lastname }}
                                </td>
                                <td>
                                    <?php
                                    if (collect($infos->invoice)->isEmpty()) {
                                        echo '<span class="right badge badge-danger">ไม่มีข้อมูล</span>';
                                    } else {
                                        if ($infos->invoice[0]->status == 'init') {
                                            $status = 'init';
                                            if ($infos->invoice[0]['lastmeter'] == 0) {
                                                // echo '<span class="right badge badge-warning">ล็อคมิเตอร์</span>';
                                            } else {
                                                echo '<span class="right badge badge-primary">รอบันทึกข้อมูล</span>';
                                            }
                                        } else {
                                            $status = 'paid';
                                            echo $infos->invoice[0]->inv_id;
                                        }
                                    }
                                    ?>
                                </td>
                                <td class="text-right">{{ $status == '' ? '-' : $infos->invoice[0]->lastmeter }}</td>
                                <td class="text-right">
                                    {{ $status == 'init' || $status == '' ? '-' : $infos->invoice[0]->currentmeter }}</td>
                                <td class="text-right">
                                    {{ $status == 'init' || $status == '' ? '-' : $infos->invoice[0]->water_used }}</td>
                                <td class="text-right">

                                    @if (collect($infos->invoice)->isNotEmpty())
                                        {{ $status == 'init' || $status == '' ? '-' : $infos->invoice[0]->paid }}
                                        <!-- คิดเป็น -->
                                    @endif
                                </td>
                                <td> {{ $status == 'init' || $status == '' ? '-' : $min_rate }}</td>
                                <td>-</td> <!-- ค่าบริการ -->
                                <td>{{ $status == 'init' || $status == '' ? '-' : $infos->invoice[0]->vat }}</td>
                                <!-- vat 7% -->
                                <td class="text-right">
                                    {{ $status == 'init' || $status == '' ? '-' : $infos->invoice[0]->totalpaid }}
                                    <!-- รวมเป็นเงิน -->
                                </td>
                                <td class="text-right">{{ $status == '' ? '-' : $prev_owe_amount }}</td>
                                <!-- ค่างวดก่อน -->
                                <td class="text-right">
                                    {{ $status == 'init' || $status == '' ? '-' : $prev_owe_amount + $infos->invoice[0]->totalpaid }}
                                </td>
                                <!--รวม-->
                                <td class="text-right">
                                    <!-- วันที่ชำระเดือนนี้ -->
                                    @if (collect($infos->invoice)->isNotEmpty())
                                        <?php
                                        if ($prev_owe_amount > 0) {
                                            echo '';
                                        } else {
                                            if (!isset($infos->invoice[0]->acc_transactions->created_at)) {
                                                echo '-'; //dd($infos->invoice[0]);
                                            } else {
                                                echo date_format($infos->invoice[0]->acc_transactions->created_at, 'd-m-Y');
                                            }
                                        }
                                        ?>
                                    @endif

                                </td>
                                <td>
                                    {{-- หน้าบัญชีเงินสด --}}
                                    @if (collect($infos->invoice)->isNotEmpty())
                                        {{ $status == 'init' || $status == '' ? '-' : $prev_owe_amount + $infos->invoice[0]->totalpaid }}
                                    @endif
                                </td>
                                <td class="text-right">
                                    {{-- จำนวนเงินที่ชำระ --}}
                                    @if (collect($infos->invoice)->isNotEmpty())
                                        <?php
                                        if ($prev_owe_amount == 0) {
                                            echo $prev_owe_amount + $infos->invoice[0]->totalpaid;
                                        } else {
                                            echo '';
                                        }
                                        ?>
                                    @endif
                                </td>
                                <td>
                                    {{-- คงค้างยกไปเดือนหน้า --}}
                                    @if (collect($infos->invoice)->isNotEmpty())
                                        <?php
                                        if ($prev_owe_amount > 0) {
                                            echo $prev_owe_amount + $infos->invoice[0]->totalpaid;
                                        } else {
                                            echo '0';
                                        }
                                        ?>
                                    @endif
                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection


@section('script')
    <script
        src="https://www.jqueryscript.net/demo/Export-Html-Table-To-Excel-Spreadsheet-using-jQuery-table2excel/src/jquery.table2excel.js">
    </script>

    <script src="{{ asset('js/my_script.js') }}"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.print.min.js"></script>
    <script>
        let preloaderwrapper = document.querySelector('.preloader-wrapper')

        $(document).ready(function() {
            // $('.datepicker').datepicker({
            //     format: 'dd/mm/yyyy',
            //     todayBtn: true,
            //     language: 'th', //เปลี่ยน label ต่างของ ปฏิทิน ให้เป็น ภาษาไทย   (ต้องใช้ไฟล์ bootstrap-datepicker.th.min.js นี้ด้วย)
            //     thaiyear: true,
            // }).datepicker("setDate", new Date());; //กำหนดเป็นวันปัจุบัน


            $('.diff').html($('#diff').val())
            $('.meter_reserve_price').html($('#meter_reserve_price').val())
            $('._total').html($('#_total').val())
            $('.total').html($('#total').val())


            $('#example').DataTable();
            preloaderwrapper.classList.add('fade-out-animation')

        })

        $('#budgetyear_id').change(() => {
            let budgetyear_id = $('#budgetyear_id').val()
            $.get('../api/invoice_period/inv_period_lists/' + budgetyear_id).done(function(data) {
                let text = '<option value="all" seleted>ทั้งหมด</option>';
                data.forEach(element => {
                    text += `<option value=${element.id}>${element.inv_period_name}</option>`
                });

                $('#inv_period_id').html(text)
            })
        })

        $('#oweTable').DataTable({
            responsive: true,
            dom: 'lBfrtip',
            buttons: [
                //    'excel', 'pdf', 'print'
            ],
            exportOptions: {
                rows: ':visible'
            },
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
            }
        })
        $(document).ready(function() {
            $('.paginate_page').text('หน้า')
            let val = $('.paginate_of').text()
            $('.paginate_of').text(val.replace('of', 'จาก'));

        })

        $('#zone_id').change(function() {
            //get ค่าsubzone
            $.get(`../api/subzone/${$(this).val()}`)
                .done(function(data) {
                    let text = '<option value="all" selected>ทั้งหมด</option>';
                    data.forEach(element => {
                        text += `<option value="${element.id}">${element.subzone_name}</option>`
                    });
                    $('#subzone_id').html(text)
                });
        });

        $('#printBtn').click(function() {
            var tagid = 'oweTable'
            var hashid = "#" + tagid;
            var tagname = $(hashid).prop("tagName").toLowerCase();
            var attributes = "";
            var attrs = document.getElementById(tagid).attributes;
            $.each(attrs, function(i, elem) {
                attributes += " " + elem.name + " ='" + elem.value + "' ";
            })
            var divToPrint = $(hashid).html();
            var head = "<html><head>" + $("head").html() + "</head>";
            var allcontent = head + "<body  onload='window.print()' >" + "<" + tagname + attributes + ">" +
                divToPrint + "</" + tagname + ">" + "</body></html>";
            var newWin = window.open('', 'Print-Window');
            newWin.document.open();
            newWin.document.write(allcontent);
            newWin.document.close();
            setTimeout(function() {
                newWin.close();
            }, 10);
        })

        $('#excelBtn').click(function() {
            $("#DivIdToExport").table2excel({
                // exclude CSS class
                exclude: ".noExl",
                name: "Worksheet Name",
                filename: 'aa', //do not include extension
                fileext: ".xls" // file extension
            })
        });

        function checkValues() {

        }
    </script>
@endsection
