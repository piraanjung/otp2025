@extends('layouts.admin1')

@section('nav-reports-dailypayment')
    active
@endsection

@section('nav-header')
    รายงาน
@endsection
@section('nav-main')
<form action="{{ route('reports.dailypayment') }}" method="post" class="mb-0 mt-0">
    @csrf
    <button type="submit" class="btn-link" style="
    padding-top: -30px;
    float: left;
    margin-top: -20px;
    margin-left: 10px;
">การชำระค่าน้ำประปาประจำวัน</button>

   
    <input type="hidden" value="nav" name="nav">
</form>
@endsection

@section('page-topic')
ตารางรายงานการรับชำระค่าน้ำประจำวัน
@endsection
@section('style')
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
            padding: 10px;
            border: 1px solid black;
            border-radius: 10px
        }
        .selected_header{
            font-weight: bold
        }

        tbody {
            margin: 20px 29px
        }

        tbody td {
            text-align: center
        }

        tbody td.day {
            color: blue
        }

        .total {
            color: blue
        }

        #topic {
            font-size: 1.3rem;
            font-weight: bold;
            color: black;
            margin-bottom: 1remπ
        }

        .subtotal {
            color: black;
            border-bottom: 1px solid black
        }

        tbody td.day.active {
            font-size: 1.1rem;
            font-weight: bold;
            color: red;
        }

        table td.new,
        td.old.day {
            color: gray
        }

        th,
        th sup {
            text-align: center;
            padding: 3px !important;
        }

        tbody tr.group {
            border-left: 2px solid lightskyblue;
            border-right: 2px solid lightskyblue;


        }

        tbody tr.tr_even {
            border-left: 2px solid red;
            border-right: 2px solid red;


        }

        .info_blur {
            opacity: 0.2;
        }
    </style>
    <!-- Styles -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    <!-- Or for RTL support -->

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.full.min.js"></script>
@endsection
@section('content')
    <div class="preloader-wrapper">
        <button class="btn btn-primary btn-sm mb-2" type="button" disabled>
            <span class="spinner-border spinner-border-sm" role="status"></span>
            Loading...
        </button>
    </div>
    <div class="row mb-4">
        <div class="col-6">
            <form action="{{ route('reports.dailypayment') }}" method="POST">
                @csrf
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="search" class="col-form-label">หมู่ที่:</label>
                                    <select class="form-control" name="zone_id" id="zone_id">
                                        <option value="all" {{ $zone_id == 'all' ? 'selected' : '' }}>ทั้งหมด</option>
                                        @foreach ($zones as $zone)
                                            <option value="{{ $zone->id }}"
                                                {{ $zone_id == $zone->id ? 'selected' : '' }}>
                                                {{ $zone->zone_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
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
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="search" class="col-form-label">จากวันที่:</label>
                                    <input class="form-control datepicker" type="text" name="fromdate"  id="fromdate">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="search" class="col-form-label">ถึงวันที่:</label>
                                    <input class="form-control datepicker2" type="text" name="todate" id="todate">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="search" class="col-form-label">ปีงบประมาณ:</label>
                                    <select name="budgetyear_id" id="budgetyear_id" class="form-control">
                                        @foreach ($budgetyears as $budgetyear)

                                            <option value="{{ $budgetyear->id }}"
                                                {{ $budgetyear->status == 'active' ? 'selected' : '' }}>
                                                {{ $budgetyear->budgetyear_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="search" class="col-form-label">รอบบิล:</label>
                                    <select name="inv_period_id" id="inv_period_id" class="form-control">
                                        <option value="all" {{$inv_period_id =="all" ? 'selected' : '' }}>ทั้งหมด</option>
                                        @foreach ($inv_periods as $item)
                                            <option value="{{ $item->id }}" {{$inv_period_id == $item->id ? 'selected' : '' }}>{{ $item->inv_p_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="search" class="col-form-label">ผู้รับเงิน:</label>
                                    <select class="form-control" name="cashier_id" id="cashier_id">
                                        <option value="all" selected>ทั้งหมด</option>
                                        @foreach ($receiptions as $receiption)
                                            <option value="{{ $receiption->id }}" {{$request_selected['cashier'][0]['id'] == $receiption->id ? 'selected' : ''}}>
                                                {{ $receiption->firstname . ' ' . $receiption->lastname }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="search" class="col-form-label">&nbsp;</label>
                                    <button type="submit" id="searchBtn" name="searchBtn" value="true"
                                        class="form-control btn btn-primary">ค้นหา</button>
                                </div>
                            </div>
                        </div>
                        <div class="row pt-2" style="border-top: 1px solid rgb(202, 196, 196)">
                            <div class="col-6">
                                <button type="submit" id="excel" name="excel" value="true"
                                class="form-control btn btn-info">download excel</button>
                            </div>
                            <div class="col-6">
                                <button type="submit" id="print" name="print" value="true"
                                class="form-control btn btn-info">print</button>
                            </div>
                        </div>

                    </div><!--card-body-->
                </div><!--card-->
            </form>
        </div>
        <div class="col-6">
            <div class="row">
                <div class="col-4">
                    <div class="card">
                        <div class="card-body p-3">
                            <div class="row">
                                <div class="col-8">
                                    <div class="numbers">
                                        <p class="text-sm mb-0 text-capitalize font-weight-bold">ใช้น้ำ</p>
                                        <h5 class="font-weight-bolder mb-0">
                                            <div id="total_water_used"></div>
                                            <span class="text-success text-sm font-weight-bolder">หน่วย</span>
                                        </h5>
                                    </div>
                                </div>
                                <div class="col-4 text-end">
                                    <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                                        <i class="ni ni-world text-lg opacity-10" aria-hidden="true"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="card ">
                        <div class="card-body p-3">
                            <div class="row">
                                <div class="col-8">
                                    <div class="numbers">
                                        <p class="text-sm mb-0 text-capitalize font-weight-bold">เป็นเงิน</p>
                                        <h5 class="font-weight-bolder mb-0">
                                            <div id="total_paid"></div>
                                            <span class="text-success text-sm font-weight-bolder">บาท</span>
                                        </h5>
                                    </div>
                                </div>
                                <div class="col-4 text-end">
                                    <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                                        <i class="ni ni-world text-lg opacity-10" aria-hidden="true"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="card ">
                        <div class="card-body p-3">
                            <div class="row">
                                <div class="col-8">
                                    <div class="numbers">
                                        <p class="text-sm mb-0 text-capitalize font-weight-bold">รักษามิเตอร์</p>
                                        <h5 class="font-weight-bolder mb-0">
                                            <div id="total_reserve"></div>
                                            <span class="text-success text-sm font-weight-bolder">บาท</span>
                                        </h5>
                                    </div>
                                </div>
                                <div class="col-4 text-end">
                                    <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                                        <i class="ni ni-world text-lg opacity-10" aria-hidden="true"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 mt-4">
                    <div class="card">
                        <div class="card-body p-3">
                            <div class="row">
                                <div class="col-8">
                                    <div class="numbers">
                                        <p class="text-sm mb-0 text-capitalize font-weight-bold">ภาษีมูลค่าเพิ่ม</p>
                                        <h5 class="font-weight-bolder mb-0">
                                            <div id="total_vat"></div>
                                            <span class="text-success text-sm font-weight-bolder">บาท</span>
                                        </h5>
                                    </div>
                                </div>
                                <div class="col-4 text-end">
                                    <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                                        <i class="ni ni-world text-lg opacity-10" aria-hidden="true"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 mt-4">
                    <div class="card">
                        <div class="card-body p-3">
                            <div class="row">
                                <div class="col-8">
                                    <div class="numbers">
                                        <p class="text-sm mb-0 text-capitalize font-weight-bold">รวมเป็นเงิน</p>
                                        <h5 class="font-weight-bolder mb-0">
                                            <div id="total_totalpaid"></div>
                                            <span class="text-success text-sm font-weight-bolder">บาท</span>
                                        </h5>
                                    </div>
                                </div>
                                <div class="col-4 text-end">
                                    <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                                        <i class="ni ni-world text-lg opacity-10" aria-hidden="true"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                {{-- {{dd($request_selected)}} --}}
                <h5> ตารางรายงานการรับชำระค่าน้ำประจำวันที่ {{$fromdateTh}} - {{$todateTh}}</h5>
                <div class="row">
                    <div class="col-2">
                        <span class="selected_header">  ปีงบประมาณ : </span> <span id="budgetyear_header">{{ $request_selected['budgeryear'][0] }}</span>
                    </div>
                    <div class="col-2">
                        <span class="selected_header"> รอบบิลที่ : </span> <span id="inv_period_header">{{ $request_selected['inv_period'][0] }}</span>
                    </div>
                    <div class="col-2">
                        <span class="selected_header"> หมู่ที่  : </span> <span id="zone_header">{{ $request_selected['zone'][0] }}</span>
                    </div>
                    <div class="col-2">
                        <span class="selected_header">เส้นทางจด : </span> <span id="subzone_header">{{ $request_selected['subzone'][0] }}</span>
                    </div>
                    <div class="col-3">
                        <span class="selected_header"> ผู้รับเงิน : </span> <span id="cashier_header">{{ $request_selected['cashier'][0]['firstname']." ".$request_selected['cashier'][0]['lastname'] }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body table-responsive">
            <table class="table" id="example">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>เลขผู้ใช้น้ำ</th>
                        <th>ชื่อ-สกุล</th>
                        <th>บ้านเลขที่</th>
                        <th>หมู่ที่</th>
                        <th>เส้นทางจดมิเตอร์</th>
                        <th>ผู้รับเงิน</th>
                        <th>วันที่รับเงิน</th>
                        <th>เลขใบแจ้งหนี้</th>
                        <th>รอบบิล</th>
                        <th>ก่อนจด<div><sup>หน่วย</sup></div>
                        </th>
                        <th>หลังจด <div><sup>หน่วย</sup></div>
                        </th>
                        <th>ใช้น้ำ <div><sup>หน่วย</sup></div>
                        </th>
                        <th>เป็นเงิน <div><sup>บาท</sup></div>
                        </th>
                        <th>รักษามิเตอร์ <div><sup>บาท</sup></div>
                        </th>
                        <th>Vat 7% <div><sup>บาท</sup></div>
                        </th>
                        <th>รวมเป็นเงิน <div><sup>บาท</sup></div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1; ?>
                    @foreach ($paidInfos as $key => $infos)
                        <?php $firstRow = 1; ?>
                        @foreach ($infos as $owe)
                            <tr>
                                @if ($firstRow == 1)
                                    <td>{{ $i }}</td>
                                    <td class="text-right">{{ $owe->usermeterinfos->meternumber }}</td>

                                    <td class="text-start">
                                        {{ $owe->usermeterinfos->user->prefix . '' . $owe->usermeterinfos->user->firstname . ' ' . $owe->usermeterinfos->user->lastname }}
                                    </td>
                                    <td class="text-right">{{ $owe->usermeterinfos->user->address }}</td>
                                    <td class="text-right">
                                        {{ $owe->usermeterinfos->user->user_zone->zone_name }}</td>
                                    <td class="text-right">
                                        {{ collect($owe->usermeterinfos->user->user_subzone)->isEmpty() ? '-' : $owe->usermeterinfos->user->user_subzone->subzone_name }}
                                    </td>

                                    <td class="text-end">
                                        {{ $owe->acc_transactions->cashier_info->prefix . '' . $owe->acc_transactions->cashier_info->firstname . ' ' . $owe->acc_transactions->cashier_info->lastname }}
                                    </td>

                                    <td class="text-center">{{ $owe->updated_at }}</td>
                                    <?php $firstRow = 0; ?>
                                @else
                                    <td class="info_blur">{{ $i }}</td>

                                    <td class="info_blur text-right">
                                        {{ $owe->usermeterinfos->meternumber }}</td>

                                    <td class="info_blur text-start">
                                        {{ $owe->usermeterinfos->user->prefix . '' . $owe->usermeterinfos->user->firstname . ' ' . $owe->usermeterinfos->user->lastname }}
                                    </td>
                                    <td class="info_blur text-end">
                                        {{ $owe->usermeterinfos->user->address }}</td>
                                    <td class="info_blur text-center">
                                        {{ $owe->usermeterinfos->user->user_zone->zone_name }}</td>
                                    <td class="info_blur text-center">
                                        {{ collect($owe->usermeterinfos->user->user_subzone)->isEmpty() ? '-' : $owe->usermeterinfos->user->user_subzone->subzone_name }}
                                    </td>
                                    <td class="info_blur text-end">

                                        {{ $owe->acc_transactions->cashier_info->prefix . '' . $owe->acc_transactions->cashier_info->firstname . ' ' . $owe->acc_transactions->cashier_info->lastname }}
                                    </td>
                                    <td class="info_blur text-center">{{ $owe->updated_at }}</td>
                                @endif
                                <td class="text-end">{{ $owe->inv_id }}</td>
                                <td class="text-end">{{ $owe->invoice_period->inv_p_name }}</td>
                                <td class="text-end">{{ $owe->lastmeter }}</td>
                                <td class="text-end">{{ $owe->currentmeter }}</td>
                                <td class="text-end">{{ $owe->water_used }}</td>
                                <td class="text-end">{{ $owe->water_used == 0 ? 0 : $owe->paid }}</td>
                                <td class="text-end">{{ $owe->water_used == 0 ? 10 : 0 }}</td>
                                <td class="text-end">{{ $owe->vat }}</td>
                                <td class="text-end">{{ $owe->totalpaid }}</td>


                            </tr>
                        @endforeach
                        <?php $i++; ?>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th class="text-end">ใช้น้ำ</th>
                        <th class="text-end">เป็นเงิน</th>
                        <th class="text-end">reserve</th>
                        <th class="text-end">Vat 7%</th>
                        <th class="text-end">รวมเป็นเงิน</th>
                    </tr>
                </tfoot>
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
    <script src="https://cdn.datatables.net/select/1.7.0/js/dataTables.select.min.js"></script>
    <script src="{{ asset('js/myscript.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/bootstrap-datepicker/js/bootstrap-datepicker-thai.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/bootstrap-datepicker/js/locales/bootstrap-datepicker.th.js') }}">
    </script>
    <script>
        $('#zone_id').select2({
            theme: "bootstrap-5",
            width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
            placeholder: $(this).data('placeholder'),
            closeOnSelect: false,
        });
        $('#subzone_id').select2({
            theme: "bootstrap-5",
            width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
            placeholder: $(this).data('placeholder'),
            closeOnSelect: false,
        });
        $('#inv_period').select2({
            theme: "bootstrap-5",
            width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
            placeholder: $(this).data('placeholder'),
            closeOnSelect: false,
        });


        let table
        let preloaderwrapper = document.querySelector('.preloader-wrapper')
        $(document).ready(function() {
            console.log('<?=$fromdateTh; ?>')
            $('.datepicker').datepicker({
                format: 'dd/mm/yyyy',
                todayBtn: true,
                language: 'th', //เปลี่ยน label ต่างของ ปฏิทิน ให้เป็น ภาษาไทย   (ต้องใช้ไฟล์ bootstrap-datepicker.th.min.js นี้ด้วย)
                thaiyear: true,

            }).datepicker("setDate", '<?=$fromdate; ?>');//กำหนดเป็นวันปัจุบัน

            $('.datepicker2').datepicker({
                format: 'dd/mm/yyyy',
                todayBtn: true,
                language: 'th', //เปลี่ยน label ต่างของ ปฏิทิน ให้เป็น ภาษาไทย   (ต้องใช้ไฟล์ bootstrap-datepicker.th.min.js นี้ด้วย)
                thaiyear: true,

            }).datepicker("setDate", '<?=$todate; ?>');//กำหนดเป็นวันปัจุบัน

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
                        // "info": "แสดง _MENU_ แถว",
                    },
                },
                // dom: 'lBfrtip',
                // buttons: [{
                //     extend: 'excelHtml5',
                //     'text': 'Excel',
                //     exportOptions: {
                //         rows: ['.selected']
                //     },
                //     filename: function() {
                //         return $('#topic').text()
                //     },
                //     customize: function(xlsx) {

                //         //copy _createNode function from source
                //         function _createNode(doc, nodeName, opts) {
                //             var tempNode = doc.createElement(nodeName);

                //             if (opts) {
                //                 if (opts.attr) {
                //                     $(tempNode).attr(opts.attr);
                //                 }

                //                 if (opts.children) {
                //                     $.each(opts.children, function(key, value) {
                //                         tempNode.appendChild(value);
                //                     });
                //                 }

                //                 if (opts.text !== null && opts.text !== undefined) {
                //                     tempNode.appendChild(doc.createTextNode(opts.text));
                //                 }
                //             }

                //             return tempNode;
                //         }

                //         var sheet = xlsx.xl.worksheets['sheet1.xml'];
                //         var mergeCells = $('mergeCells', sheet);
                //         var mergeCells2 = $('mergeCells', sheet);
                //         mergeCells[0].children[0].remove(); // remove merge cell 1st row
                //         var rows = $('row', sheet);
                //         rows[0].children[0].remove(); // clear header cell
                //         // create new cell
                //         rows[0].appendChild(_createNode(sheet, 'c', {
                //             attr: {
                //                 t: 'inlineStr',
                //                 r: 'A2', //address of new cell
                //                 s: 51 // center style - https://www.datatables.net/reference/button/excelHtml5
                //             },
                //             children: {
                //                 row: _createNode(sheet, 'is', {
                //                     children: {
                //                         row: _createNode(sheet, 't', {
                //                             text: $('.card-title').text()
                //                         }),
                //                     }
                //                 }),

                //             }
                //         }));
                //         rows[0].appendChild(_createNode(sheet, 'c', {
                //             attr: {
                //                 t: 'inlineStr',
                //                 r: 'A1', //address of new cell
                //                 s: 51 // center style - https://www.datatables.net/reference/button/excelHtml5
                //             },
                //             children: {
                //                 row: _createNode(sheet, 'is', {
                //                     children: {
                //                         row: _createNode(sheet, 't', {
                //                             text: $('.card-title').text()
                //                         }),
                //                     }
                //                 }),

                //             }
                //         }));


                //         // set new cell merged
                //         mergeCells[0].appendChild(_createNode(sheet, 'mergeCell', {
                //             attr: {
                //                 ref: 'A1:Q1' // merge address
                //             }
                //         }));
                //         mergeCells[1].appendChild(_createNode(sheet, 'mergeCell', {
                //             attr: {
                //                 ref: 'A2:Q2' // merge address
                //             }
                //         }));

                //         mergeCells.attr('count', mergeCells.attr('count') + 1);
                //         // mergeCells2.attr('count', mergeCells2.attr('count') + 1);

                //         // add another merged cell
                //     }
                // }],
                footerCallback: function(row, data, start, end, display) {
                    let api = this.api();

                    // Remove the formatting to get integer data for summation
                    let intVal = function(i) {
                        return typeof i === 'string' ?
                            i.replace(/[\$,]/g, '') * 1 :
                            typeof i === 'number' ?
                            i :
                            0;
                    };

                    // _water_used
                    total_water_used = api
                        .column(12)
                        .data()
                        .reduce((a, b) => intVal(a) + intVal(b), 0);
                    pageTotal_water_used = api
                        .column(12, {
                            page: 'current'
                        })
                        .data()
                        .reduce((a, b) => intVal(a) + intVal(b), 0);
                    api.column(12).footer().innerHTML =
                        '<div class="subtotal">' + pageTotal_water_used +
                        '</div> <div class="total" id="water_used"> ' +
                        total_water_used + ' </div>';

                    // total_paid
                    total_paid = api
                        .column(13)
                        .data()
                        .reduce((a, b) => intVal(a) + intVal(b), 0);

                    // Total_paid over this page
                    pageTotal_paid = api
                        .column(13, {
                            page: 'current'
                        })
                        .data()
                        .reduce((a, b) => intVal(a) + intVal(b), 0);

                    // Update footer
                    api.column(13).footer().innerHTML =
                        '<div class="subtotal"> ' + pageTotal_paid +
                        '</div> <div class="total" id="paid">  ' +
                        total_paid + ' </div>';

                    // total_reserve
                    total_reserve = api
                        .column(14)
                        .data()
                        .reduce((a, b) => parseFloat(a) + parseFloat(b), 0);

                    // Total_reserve over this page
                    pageTotal_reserve = api
                        .column(14, {
                            page: 'current'
                        })
                        .data()
                        .reduce((a, b) => parseFloat(a) + parseFloat(b), 0);

                    // Update footer
                    api.column(14).footer().innerHTML =
                        '<div class="subtotal"> ' + pageTotal_reserve.toFixed(2) +
                        '</div> <div class="total" id="reserve">  ' +
                        total_reserve.toFixed(2) + ' </div>';


                    // total_vat
                    total_vat = api
                        .column(15)
                        .data()
                        .reduce((a, b) => parseFloat(a) + parseFloat(b), 0);

                    // Total_totalp idover this page
                    pageTotal_vat = api
                        .column(15, {
                            page: 'current'
                        })
                        .data()
                        .reduce((a, b) => parseFloat(a) + parseFloat(b), 0);

                    // Update footer
                    api.column(15).footer().innerHTML =
                        '<div class="subtotal"> ' + pageTotal_vat.toFixed(2) +
                        '</div> <div class="total" id="vat">  ' +
                        total_vat.toFixed(2) + ' </div>';


                    // total_totalpaid
                    total_totalpaid = api
                        .column(16)
                        .data()
                        .reduce((a, b) => parseFloat(a) + parseFloat(b), 0);

                    // Total_totalp idover this page
                    pageTotal_totalpaid = api
                        .column(16, {
                            page: 'current'
                        })
                        .data()
                        .reduce((a, b) => parseFloat(a) + parseFloat(b), 0);

                    // Update footer
                    api.column(16).footer().innerHTML =
                        '<div class="subtotal"> ' + pageTotal_totalpaid.toFixed(2) +
                        '</div> <div class="total" id="totalpaid">  ' +
                        total_totalpaid.toFixed(2) + ' </div>';

                }
            });

            $('#total_water_used').html($('#water_used').text())
            $('#total_paid').html($('#paid').text())
            $('#total_reserve').html($('#reserve').text())
            $('#total_vat').html($('#vat').text())
            $('#total_totalpaid').html($('#totalpaid').text())


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

        $(document).on('change', "#budgetyear_id", function(e) {
            let budgetyear_id = $(this).val();
            $.get(`../admin/budgetyear/invoice_period_list/${budgetyear_id}`).done(function(data){
                let text = '<option value="">เลือก</option>';
                    if (data.length > 1) {
                        text += `<option value="all" selected>ทั้งหมด</option>`;
                    }
                    data.forEach(element => {
                        text +=
                            `<option value="${element.id}"> ${element.inv_p_name}</option>`
                    });
                    $('#inv_period_id').html(text)
            });

        });
        $(document).on('change', "#zone_id", function(e) {
            //get ค่าsubzone
            let zone_id = $(this).val()
            console.log('zone_id', zone_id)
            $.post(`../api/subzone`, {
                    zone_id: [zone_id]
                })
                .done(function(data) {
                    console.log('data', data)
                    let text = zone_id !== 'all' ? '<option value="">เลือก</option>' :
                        '<option value="all" selected>ทั้งหมด</option>';
                    if (data.length > 1) {
                        text += `<option value="all" selected>ทั้งหมด</option>`;
                    }
                    data.forEach(element => {
                        text +=
                            `<option value="${element.id}"> ${element.subzone_name}</option>`
                    });
                    $('#subzone_id').html(text)
                });
        });
    </script>
@endsection
