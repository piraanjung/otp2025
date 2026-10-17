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

        .text-right{
            text-align: right
        }
        th{
            text-align: center;
            background-color: rgb(247, 227, 118) !important
        }
        .table thead th {
            padding: .75rem;
            text-transform: capitalize;
            letter-spacing: 0;
            border-bottom: 1px solid #e9ecef;
        }

        /* td,
        table thead th {
            text-align: left;
            color: black;
            border: 1px solid black
        } */
    </style>
    {{-- <link href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
    <link href="https://cdn.datatables.net/buttons/1.5.1/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css" /> --}}
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css" rel="stylesheet" />
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
                        {{-- Row 1: ปีงบประมาณ, รอบบิล, สถานะ --}}
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group row">
                                    <label for="budgetyear_id" class="col-sm-5 col-form-label">ปีงบประมาณ:</label>
                                    <div class="col-sm-7">
                                        <select class="form-control" name="budgetyear_id" id="budgetyear_id">
                                            <option value="all">ทั้งหมด</option>
                                            @foreach ($budgetyear_list as $list)
                                                <option value="{{ $list->id }}"
                                                    {{ isset($budgetyear_selected[0]) && $list->id == $budgetyear_selected[0]->id ? 'selected' : '' }}>
                                                    {{ $list->budgetyear_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group row">
                                    <label for="inv_period_id" class="col-sm-5 col-form-label">รอบบิลที่:</label>
                                    <div class="col-sm-7">
                                       <select class="form-control" name="inv_period_id" id="inv_period_id">
                                            <option value="all" {{ request('inv_period_id') == 'all' ? 'selected' : '' }}>ทั้งหมด</option>
                                            @if (collect($budgetyear_selected)->isNotEmpty())
                                                @foreach ($budgetyear_selected[0]->invoice_period as $item)
                                                    <option value="{{ $item->id }}"
                                                        {{ (request('inv_period_id') == $item->id) || (!request()->has('inv_period_id') && isset($current_inv_period[0]) && $item->id == $current_inv_period[0]->id) ? 'selected' : '' }}>
                                                        {{ $item->inv_p_name }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group row">
                                    <label for="status" class="col-sm-5 col-form-label">สถานะ:</label>
                                    <div class="col-sm-7">
                                        <select class="form-control" name="status" id="status">
                                            <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>ทั้งหมด</option>
                                            <option value="init" {{ request('status') == 'init' ? 'selected' : '' }}>รอบันทึกข้อมูล</option>
                                            <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>ชำระเงินแล้ว</option>
                                            <option value="owe" {{ request('status') == 'owe' ? 'selected' : '' }}>ค้างชำระ</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div><!--row 1-->

                        {{-- Row 2: ผู้ใช้น้ำ (Select2), โซน, สายการจ่ายน้ำ --}}
                        <div class="row mt-2">
                            <div class="col-md-4">
                                <div class="form-group row">
                                    <label for="user_id" class="col-sm-5 col-form-label">ผู้ใช้น้ำ:</label>
                                    <div class="col-sm-7">
                                        <select class="form-control select2" name="user_id" id="user_id">
                                            <option value="all">ทั้งหมด</option>
                                            @foreach ($users_list as $u)
                                                <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>
                                                    {{ $u->id }} - {{ $u->user ? $u->user->prefix . $u->user->firstname . ' ' . $u->user->lastname : 'ไม่พบข้อมูลผู้ใช้' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group row">
                                    <label for="zone_id" class="col-sm-5 col-form-label">โซน:</label>
                                    <div class="col-sm-7">
                                        <select class="form-control" name="zone_id" id="zone_id">
                                            <option value="all">ทั้งหมด</option>
                                            @foreach ($zone_list as $z)
                                                <option value="{{ $z->id }}" {{ request('zone_id') == $z->id ? 'selected' : '' }}>
                                                    {{ $z->zone_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group row">
                                    <label for="subzone_id" class="col-sm-5 col-form-label">สายน้ำ:</label>
                                    <div class="col-sm-7">
                                        <select class="form-control" name="subzone_id" id="subzone_id">
                                            <option value="all">ทั้งหมด</option>
                                            @foreach ($subzone_list as $sz)
                                                <option value="{{ $sz->id }}" {{ request('subzone_id') == $sz->id ? 'selected' : '' }}>
                                                    {{ $sz->subzone_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" id="searchBtn" class="form-control btn btn-primary">ค้นหา</button>
                            </div>
                        </div><!--row 2-->

                    </div><!-- /.info-box-content -->
                </div><!--info-box-->
            </div><!--card-body-->
        </div><!--card-->
    </form>

    <div class="card mt-2">
        <div class="card-header"></div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="oweTable" class="table table-bordered" width="100%">
                    <thead>
                       <th colspan="18">
                            ประจำเดือน {{ request('inv_period_id') == 'all' ? 'ทั้งหมด' : ($current_inv_period->isNotEmpty() ? $current_inv_period[0]['inv_p_name'] : 'ทั้งหมด') }}
                        </th>
                        <tr>
                            <th rowspan="3">ผู้ใช้น้ำ<br>ประปาเลขที่</th>
                            <th rowspan="3">ชื่อ-สกุล</th>
                            <th rowspan="3">เลขใบเสร็จ</th>
                            <th rowspan="3">รอบบิล</th>
                            <th colspan="9">หนี้สินที่เกิดขึ้นในเดือนนี้</th>

                            <th rowspan="3">รวม</th>

                            <th colspan="3" rowspan="1">การชำระหนี้เดือนนี้ </th>
                            <th rowspan="3">คงค้างยก<div>ไปเดือนหน้า</div></th>
                        </tr>
                        {{-- tr 1 --}}
                        <tr>
                            <th colspan="2">เลขอ่านของมาตรวัด</th>
                            <th rowspan="2">จำนวนหน่วย</th>
                            <th rowspan="2">คิดเป็นเงิน</th>
                            <th rowspan="2">เพิ่มให้เต็ม<div>อัตราอย่างต่ำ</div></th>
                            <th rowspan="2">ค่าบริการ</th>
                            <th rowspan="2">ภาษีมูลค่า<div>เพิ่ม 7%</div></th>
                            <th rowspan="2">รวมเป็นเงิน</th>

                            <th rowspan="2">คงค้างยกมา<div>แต่เดือนก่อน</div></th>
                            <th rowspan="2">วันที่</th>
                            <th rowspan="2">หน้าบัญชี<div>เงินสด</div></th>
                            <th rowspan="2">จำนวน<div>เงินที่ชำระ</div></th>
                        </tr>
                        {{-- tr 2 --}}
                        <tr>
                            <th>จาก</th>
                            <th>ถึง</th>
                        </tr>
                    </thead>

                    <tbody>

                        @foreach ($ledgers as $key => $infos)
                            {{-- กรณีเลือก "ทั้งหมด" ให้แสดงผลแยกทีละใบแจ้งหนี้ (ถ้ามีหลายใบในมิเตอร์นั้น) --}}

                            @php
                                $invoices = collect($infos->invoice);
                            @endphp
                        @php
                            $indexRow= 1;
                            $prevAccTransIDFk = 0;
                            $nextBalance = 0;
                            $totalpaidSum = 0;
                            $paidstatus = "wait";
                            $samAcctransIdBg = '';
                            $invoicesCount = collect($invoices)->count();
                        @endphp
                            @forelse ($invoices as $invIndex => $inv)
                                <?php
                                $nextId= $invIndex+1;
                                if($nextId < $invoicesCount){
                                   if($prevAccTransIDFk != $inv->acc_trans_id_fk){
                                        $prevAccTransIDFk = $inv->acc_trans_id_fk;

                                        if($invoices[$nextId]->acc_trans_id_fk == $prevAccTransIDFk){
                                            // echo 'firstsame ='.$prevAccTransIDFk." ->";
                                            $nextBalance = $inv->totalpaid;
                                            $totalpaidSum = 0;
                                             $paidstatus = 'wait';
                                             $samAcctransIdBg = '#f7e376af';

                                        }else{
                                            $samAcctransIdBg = '';

                                             $nextBalance = 0;
                                             $paidstatus = 'complete';
                                            $totalpaidSum = $inv->totalpaid;
                                            // echo 'new ='.$prevAccTransIDFk." ->".$paidstatus."=="; 
                                        }
                                        //  echo 'ยอดขำระ = '.$totalpaidSum.' , ยกยอดเดือนหน้า '.$nextBalance."<br>";
                                    
                                    }else{
                                        $samAcctransIdBg = '#f7e376af';
                                        $paidstatus = 'wait';
                                        $totalpaidSum = 0;
                                        if($invoices[$nextId]->acc_trans_id_fk != $prevAccTransIDFk){
                                            // echo 'lastsame ='.$prevAccTransIDFk." -> ";
                                            $prevAccTransIDFk = 0;
                                            $totalpaidSum = $nextBalance +$inv->totalpaid;
                                            $nextBalance = 0;
                                             $paidstatus = 'complete';

                                        }else{
                                            $nextBalance += $inv->totalpaid;
                                            // echo 'samexx ='.$invoices[$invIndex+1]->acc_trans_id_fk;
                                        }
                                        //  echo $paidstatus .'  ยอดขำระ = '.$totalpaidSum.' , ยกยอดเดือนหน้า '.$nextBalance."<br>";

                                    }
                                }
                                
                            
                                $status = '';
                                $bg = '';
                                
                                $min_rate = $inv->water_used == 0 ? 10 : 0;
                                $prev_owe_amount = $inv->previous_balance;//collect($infos['invoice_by_user_id'])->isEmpty() ? 0 : ($infos['invoice_by_user_id'][$invIndex]->totalpaid ?? 0);

                                if (isset($inv->status)) {
                                    $bg = $inv->status ==='owe' || $inv->status ==='invoice' > 0 ? '#feeaf1' : '#cff0f0';
                                }

                                if ($inv->status == 'init') {
                                    $status = 'init';
                                } else {
                                    $status = 'paid';
                                }
                                ?>
                                <tr style="background-color:{{ $samAcctransIdBg == '' ?  $bg :$samAcctransIdBg }};">
                                    <td class="text-right">{{ $infos['id'] ?? $infos['user_id'] }}</td>
                                    <td >
                                        @php
                                            $prefix = !isset($infos->user->prefix) ? '' : $infos->user->prefix;
                                            $firstname = !isset($infos->user->firstname) ? '' : $infos->user->firstname;
                                            $lastname = !isset($infos->user->lastname) ? '' : $infos->user->lastname;
                                        @endphp
                                        {{ $prefix . '' . $firstname . ' ' . $lastname }}
                                    </td>
                                    <td style="text-align: center">
                                        <?php
                                        if ($inv->status == 'init') {
                                            if ($inv->lastmeter == 0) {
                                                // echo '<span class="right badge badge-warning">ล็อคมิเตอร์</span>';
                                            } else {
                                                echo '<span class="right badge badge-primary">รอบันทึกข้อมูล</span>';
                                            }
                                        } else {
                                            echo $inv->acc_trans_id_fk;
                                        }
                                        ?>
                                    </td>
                                    <td style="text-align: center">{{   $inv->invoice_period->inv_p_name}}</td>
                                    <td class="text-right">{{ $status == '' ? '-' : $inv->lastmeter }}</td>
                                    <td class="text-right">{{ $status == 'init' || $status == '' ? '-' : $inv->currentmeter }}</td>
                                    <td class="text-right">{{ $status == 'init' || $status == '' ? '-' : $inv->water_used }}</td>
                                    <td class="text-right">{{ $status == 'init' || $status == '' ? '-' : $inv->paid }}</td>
                                    <td>{{ $status == 'init' || $status == '' ? '-' : $min_rate }}</td>
                                    <td>-</td>
                                    <td>{{ $status == 'init' || $status == '' ? '-' : $inv->vat }}</td>
                                    <td class="text-right">{{ $status == 'init' || $status == '' ? '-' : $inv->totalpaid }}</td>
                                    <td class="text-right" style="background:rgb(239, 215, 233)">{{ $prev_owe_amount  }}</td>
                                    <td class="text-right" style="background:rgb(210, 226, 193)">{{ $prev_owe_amount + $inv->totalpaid }}</td>
                                    <td class="text-right">
                                        <?php
                                        
                                            if (!isset($inv->updated_at)) {
                                                echo '-';
                                            } else {
                                                echo  $paidstatus == 'complete' ? \Carbon\Carbon::parse($inv->updated_at)->format('d-m-Y') : '';
                                            }
                                       // }
                                        ?>
                                    </td>
                                    <td class="text-right" style="background:rgb(210, 226, 193)">
                                        @if ($status != 'init' && $status != '')
                                            {{ $prev_owe_amount + $inv->totalpaid }}
                                        @endif
                                    </td>
                                    <td class="text-right" style="background:rgb(143, 230, 50); color: black;">
                                        <?php
                                        // if ($prev_owe_amount == 0) {
                                            echo $paidstatus == 'complete' ? $prev_owe_amount + $inv->totalpaid : 0;
                                        // } else {
                                        //     echo '';
                                        // }
                                        ?>
                                    </td>
                                    <td class="text-right" style="background:rgb(248, 61, 161);color: black;">
                                        <?php
                                        // if ($prev_owe_amount > 0) {
                                        //     echo $prev_owe_amount + $inv->totalpaid;
                                        // } else {
                                            echo   $paidstatus == 'complete' ? '0' : $nextBalance;
                                        // }
                                        ?>
                                    </td>
                                </tr>
                            @empty
                                {{-- กรณีที่มิเตอร์นี้ไม่มีใบแจ้งหนี้เลย --}}
                                <tr>
                                    <td class="text-right">{{ $infos['id'] ?? $infos['user_id'] }}</td>
                                    <td>
                                        @php
                                            $prefix = !isset($infos->user->prefix) ? '' : $infos->user->prefix;
                                            $firstname = !isset($infos->user->firstname) ? '' : $infos->user->firstname;
                                            $lastname = !isset($infos->user->lastname) ? '' : $infos->user->lastname;
                                        @endphp
                                        {{ $prefix . '' . $firstname . ' ' . $lastname }}
                                    </td>
                                    <td><span class="right badge badge-danger">ไม่มีข้อมูล</span></td>
                                    <td class="text-right">-</td>
                                    <td class="text-right">-</td>
                                    <td class="text-right">-</td>
                                    <td class="text-right">-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td class="text-right">-</td>
                                    <td class="text-right">0</td>
                                    <td class="text-right">0</td>
                                    <td class="text-right">-</td>
                                    <td>-</td>
                                    <td class="text-right">-</td>
                                    <td>0</td>
                                </tr>
                            @endforelse
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
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            // เรียกใช้งาน Select2 บน Element ผู้ใช้น้ำ
            $('.select2').select2({
                theme: 'bootstrap4',
                placeholder: "ค้นหาผู้ใช้น้ำ...",
                allowClear: true
            });
        });
    </script>
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
