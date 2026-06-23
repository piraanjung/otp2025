@extends('layouts.admin1')

@section('mainheader')
    รายงานการชำระค่าน้ำประปาประจำวัน
@endsection
@section('nav')
    <a href="{{ 'reports' }}">รายงาน</a>
@endsection
@section('nav-report-dailypayment')
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
    <div class="preloader-wrapper">
        <button class="btn btn-primary btn-sm mb-2" type="button" disabled>
            <span class="spinner-border spinner-border-sm" role="status"></span>
            Loading...
        </button>
    </div>
    <form action="{{ url('reports/payment') }}" method="get" onsubmit="return checkValues();">
        @csrf
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="search" class="col-form-label">หมู่ที่:</label>
                            <select class="form-control" name="zone_id" id="zone_id">
                                <option value="all" {{ $zone_id == 'all' ? 'selected' : '' }}>ทั้งหมด</option>
                                @foreach ($zones as $zone)
                                    <option value="{{ $zone->id }}" {{ $zone_id == $zone->id ? 'selected' : '' }}>
                                        {{ $zone->zone_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="search" class="col-form-label">เส้นทาง:</label>
                            <select class="form-control" name="subzone_id" id="subzone_id">
                                @if ($subzones == 'all' || $subzones == '')
                                    <option value="all" selected>ทั้งหมด</option>
                                @else
                                    @foreach ($subzones as $subzone)
                                        <option value="{{ $subzone->id }}"
                                            {{ $subzone_id == $subzone->id ? 'selected' : '' }}>
                                            {{ $subzone->subzone_name }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="search" class="col-form-label">วันที่:</label>
                            <input class="form-control datepicker" type="text" name="fromdate" id="fromdate">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="search" class="col-form-label">วันที่:</label>
                            <input class="form-control datepicker" type="text" name="todate" id="todate">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="search" class="col-form-label">ผู้รับเงิน:</label>
                            <select class="form-control" name="cashier_id" id="cashier_id">
                                <option value="all" selected>ทั้งหมด</option>
                                @foreach ($receiptions as $receiption)
                                    <option value="{{ $receiption->id }}">
                                        {{ $receiption->firstname . ' ' . $receiption->lastname }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="form-group">
                            <label for="search" class="col-form-label">&nbsp;</label>
                            <button type="submit" id="searchBtn" class="form-control btn btn-primary">ค้นหา</button>
                        </div>
                    </div>
                </div>
            </div><!--card-body-->
        </div><!--card-->
    </form>
    @if (collect($paidInfos)->isEmpty())
        <div class="card">
            <div class="card-body">
                <h4 class="text-center">ไม่พบข้อมูล</h4>
            </div>
        </div>
    @else
        <div class="card mt-4">
            <div class="card-body table-responsive">
                <div id="DivIdToExport">
                    <table id="example" class="table text-nowrap" width="100%">
                        <thead>
                            <tr>
                                <td colspan="16" class="h4">รายงานสรุปการชำระค่าน้ำประปา</td>
                            </tr>
                            {{-- <tr>
                        <td colspan="16">
                            <table width="60%">
                                <tr>
                                    <td colspan="4">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-info"><i class="far fa-envelope"></i></span>

                                            <div class="info-box-content">
                                                <span class="info-box-text">จำนวนหน่วยที่ใช้</span>
                                                <span class="info-box-number diff"></span>
                                            </div>
                                        </div>
                                    </td>
                                    <td colspan="4">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-info"><i class="far fa-envelope"></i></span>

                                            <div class="info-box-content">
                                                <span class="info-box-text">ค่าน้ำประปา</span>
                                                <span class="info-box-number _total"></span>
                                            </div>
                                        </div>
                                    </td>
                                    <td colspan="4">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-info"><i class="far fa-envelope"></i></span>

                                            <div class="info-box-content">
                                                <span class="info-box-text">ค่ารักษามิเตอร์</span>
                                                <span class="info-box-number meter_reserve_price"></span>
                                            </div>
                                        </div>
                                    </td>
                                    <td colspan="4">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-info"><i class="far fa-envelope"></i></span>

                                            <div class="info-box-content">
                                                <span class="info-box-text">รวมเป็นเงิน</span>
                                                <span class="info-box-number total"></span>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr> --}}
                            <tr>
                                <td colspan="3" class="text-center">วันที่: {{ $fromdateTh }} - {{ $todateTh }}
                                </td>
                                <td colspan="3" class="text-center">ผู้รับเงิน:
                                    {{ collect($cashier_name)->isEmpty() ? '-' : $cashier_name->firstname . ' ' . $cashier_name->lastname }}
                                </td>
                                <td colspan="3" class="text-center">
                                    {{ $subzone_id == 'all' ? 'หมู่ที่ 1-19' : $paidInfos[0][0]['zone_name'] . ' เส้นทาง ' . $paidInfos[0][0]['subzone_name'] }}
                                </td>
                                <td colspan="3" class="text-center">
                                    {{-- รอบบิลที่
                            {{ $currentInvPeriodName['inv_period_name'] ." ( ".$currentInvPeriodNameThai." )"}} --}}
                                </td>
                                <td colspan="4"></td>
                            </tr>
                            <tr>
                                <th>เลขใบเสร็จ</th>
                                <th>รหัสผู้ใช้</th>
                                <th>มิเตอร์</th>
                                <th>ชื่อ-สกุล</th>
                                <th>บ้านเลขที่</th>
                                <th>หมู่ที่</th>
                                <th>เส้นทาง</th>
                                <th>รอบบิลที่</th>
                                <th>ยกยอดมา</th>
                                <th>ปัจจุบัน</th>
                                <th>จำนวนหน่วยที่ใช้</th>
                                <th>ค่าน้ำประปา</th>
                                <th>ค่ารักษามิเตอร์</th>
                                <th>เป็นเงิน</th>
                                <th>ผู้รับเงิน</th>
                                <th>วันที่รับเงิน</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sum_diff = 0;
                            $sum_meter_reserve_price = 0;
                            $sum__total = 0;
                            $sum_total = 0;
                            ?>
                            @foreach ($paidInfos as $key => $infos)
                                <?php $i = 1; ?>
                                @foreach ($infos as $owe)
                                    <?php
                                    // dd($infos);
                                    $diff = $owe->currentmeter - $owe->lastmeter; //$owe->mustpaid;
                                    $meter_reserve_price = $diff == 0 ? 10 : 0;
                                    $_total = $diff * 8;
                                    $total = $_total + $meter_reserve_price;

                                    $sum_diff += $diff;
                                    $sum_meter_reserve_price += $meter_reserve_price;
                                    $sum__total += $_total;
                                    $sum_total += $total;
                                    ?>
                                    <tr>
                                        @if ($i++ == 1)
                                            <td class="text-right">{{ $owe->inv_id }}</td>
                                            <td class="text-right">{{ $owe->usermeterinfos->user_id }}</td>
                                            <td class="text-right">{{ $owe->usermeterinfos->meternumber }}</td>
                                            <td>{{ $owe->usermeterinfos->user->prefix . '' . $owe->usermeterinfos->user->firstname . ' ' . $owe->usermeterinfos->user->lastname }}
                                            </td>
                                            <td class="text-right">{{ $owe->usermeterinfos->user->address }}</td>
                                            <td class="text-right">
                                                {{ $owe->usermeterinfos->user->user_zone->zone_name }}</td>
                                            <td class="text-right">
                                                {{ collect($owe->usermeterinfos->user->user_subzone)->isEmpty() ? '-' : '1' }}
                                            </td>
                                        @else
                                            <td class="text-right" style="opacity: 0.2">{{ $owe->inv_id }}</td>
                                            <td class="text-right" style="opacity: 0.2">
                                                {{ $owe->usermeterinfos->user_id }}</td>
                                            <td class="text-right" style="opacity: 0.2">
                                                {{ $owe->usermeterinfos->meternumber }}</td>
                                            <td style="opacity: 0.2">
                                                {{ $owe->usermeterinfos->user->prefix . '' . $owe->usermeterinfos->user->firstname . ' ' . $owe->usermeterinfos->user->lastname }}
                                            </td>
                                            <td class="text-right" style="opacity: 0.2">
                                                {{ $owe->usermeterinfos->user->address }}</td>
                                            <td class="text-right" style="opacity: 0.2">
                                                {{ $owe->usermeterinfos->user->user_zone->zone_name }}</td>
                                            <td class="text-right" style="opacity: 0.2">
                                                {{ collect($owe->usermeterinfos->user->user_subzone)->isEmpty() ? '-' : '1' }}
                                            </td>
                                        @endif

                                        <td class="text-right">{{ $owe->invoice_period->inv_p_name }}</td>
                                        <td class="text-right">{{ number_format($owe->lastmeter) }}</td>
                                        <td class="text-right">{{ number_format($owe->currentmeter) }}</td>
                                        <td class="text-right">{{ number_format($diff) }}</td>
                                        <td class="text-right">{{ number_format($_total) }}</td>
                                        <td class="text-right">{{ number_format($meter_reserve_price) }}</td>
                                        <td class="text-right">{{ number_format($total) }}</td>
                                        <td class="text-right">
                                            @php
                                                if (
                                                    !isset(
                                                        $infos[0]->usermeterinfos->invoice[0]->acc_transactions
                                                            ->cashier,
                                                    )
                                                ) {
                                                    dd($infos[0]->usermeterinfos->invoice[0]);
                                                }
                                            @endphp
                                            {{ $owe->usermeterinfos->invoice[0]->acc_transactions->cashier_info->firstname }}
                                            {{ ' ' . $owe->usermeterinfos->invoice[0]->acc_transactions->cashier_info->lastname }}
                                        </td>
                                        <td class="text-right">{{ $owe->updated_at }}</td>

                                    </tr>
                                @endforeach
                            @endforeach
                            <input type="text" style="opacity: 0" value="{{ number_format($sum_diff) }}"
                                id="diff">
                            <input type="text" style="opacity: 0"
                                value="{{ number_format($sum_meter_reserve_price) }}" id="meter_reserve_price">
                            <input type="text" style="opacity: 0" value="{{ number_format($sum__total) }}"
                                id="_total">
                            <input type="text" style="opacity: 0" value="{{ number_format($sum_total) }}"
                                id="total">
                        </tbody>
                        <tr>
                            <td></td>
                            <td></td>
                            <td>c</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <!--card-body-->
        </div>
    @endif
    </div>




@endsection


@section('script')

    <script
        src="https://www.jqueryscript.net/demo/Export-Html-Table-To-Excel-Spreadsheet-using-jQuery-table2excel/src/jquery.table2excel.js">
    </script>
    <script src="https://cdn.datatables.net/plug-ins/1.10.20/api/sum().js"></script>

    <script>
        $(document).ready(function() {
            $('.datepicker').datepicker({
                format: 'dd/mm/yyyy',
                todayBtn: true,
                language: 'th', //เปลี่ยน label ต่างของ ปฏิทิน ให้เป็น ภาษาไทย   (ต้องใช้ไฟล์ bootstrap-datepicker.th.min.js นี้ด้วย)
                thaiyear: true,
            }).datepicker("setDate", new Date());; //กำหนดเป็นวันปัจุบัน


            $('.diff').html($('#diff').val())
            $('.meter_reserve_price').html($('#meter_reserve_price').val())
            $('._total').html($('#_total').val())
            $('.total').html($('#total').val())

        })
        $('#oweTable').DataTable({
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

            drawCallback: function() {
                var api = this.api();
                $(api.column(6).footer()).html(
                    api.column(6, {
                        page: 'current'
                    }).data().sum()
                );
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

@section('script')
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/select/1.7.0/js/dataTables.select.min.js"></script>
    <script>
        let table
        let preloaderwrapper = document.querySelector('.preloader-wrapper')
        $(document).ready(function() {

            table = $('#example').DataTable({
                "lengthMenu": [
                    [10, 25, 50, 150, -1],
                    [10, 25, 50, 150, "All"]
                ],
                "sPaginationType": "listbox",
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

            $('.dt-buttons').prepend('<label class="m-0">ดาวน์โหลด:</label>')

            $(`<div class="deselect_row_all">
                    <label class="m-0">ยกเลิกเลือกทั้งหมด:</label>
                    <button class="btn btn-secondary btn-sm" id="deselect-all">ตกลง</button>
                </div>
                <div class=" select_row_all">
                    <label class="m-0">เลือกทั้งหมด:</label>
                    <button class="btn btn-success btn-sm" id="deselect-all">ตกลง</button>
                </div>`).insertAfter('.dataTables_length')


            $('.dt-button').addClass('btn btn-sm btn-info')

            preloaderwrapper.classList.add('fade-out-animation')

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

        $(document).on('change', "#zone1", function(e) {
            //get ค่าsubzone
            let zone_id = $(this).val()

            $.post(`../api/subzone`, {
                    zone_id: zone_id
                })
                .done(function(data) {
                    console.log('data', data)
                    let text = zone_id !== 'all' ? '<option value="">เลือก</option>' :
                        '<option value="all">ทั้งหมด</option>';
                    if (data.length > 1) {
                        text += `<option value="all">ทั้งหมด</option>`;
                    }
                    data.forEach(element => {
                        text +=
                            `<option value="${element.id}">${element.zone.zone_name} - ${element.subzone_name}</option>`
                    });
                    $('#subzone').html(text)
                });
        });
    </script>
@endsection
@endsection
