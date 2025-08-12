@extends('layouts.admin1')

@section('report-meter_record_history')
    active
@endsection

@section('nav-header')
    รายงาน
@endsection
@section('nav-main')
    <a href="{{ route('reports.meter_record_history') }}"> สมุดจดเลขอ่านมาตรวัดน้ำ(ป.31)</a>
@endsection

@section('nav-topic')
    ตารางสมุดจดเลขอ่านมาตรวัดน้ำ(ป.31)
@endsection
@section('style')
    <style>
        .hidden {
            display: none !important
        }

        .dtable-container {
            max-width: 100% !important;
        }

        table {
            white-space: nowrap !important;
            width: 100% !important;
            border-collapse: collapse !important;
        }
    </style>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

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

    <div class="card col-8">
        <form action="{{ route('reports.meter_record_history') }}" method="get">
            @csrf
            <div class="card-body row">
                <div class="col-5">
                    <div class="form-group">
                        <label for="exampleFormControlInput1">ปีงบประมาณ</label>
                        <select class="form-control js-example-tokenizer" name="budgetyear[]" id="budgetyear"
                            data-placeholder="เลือก.." multiple>
                            @foreach ($budgetyears as $budgetyear)
                                <option value="{{ $budgetyear->id }}"
                                    {{ in_array($budgetyear->id, collect($budgetyear_selected_array)->toArray()) ? 'selected' : '' }}>
                                    {{ $budgetyear->budgetyear_name }}</option>
                            @endforeach

                        </select>
                    </div>
                </div>
                <div class="col-5">
                    <div class="form-group">
                        <label for="exampleFormControlInput1">หมู่ที่</label>
                        <select class="form-control js-example-tokenizer" id="zone" name="zone[]"
                            data-placeholder="เลือก.." multiple>
                            <option value="all" {{ in_array('all', $zone_id_array) ? 'selected' : '' }}>ทั้งหมด</option>

                            @foreach ($zones as $zone)
                                <option value="{{ $zone->id }}"
                                    {{ in_array($zone->id, $zone_id_array) ? 'selected' : '' }}>{{ $zone->zone_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-2">
                    <div class="form-group">
                        <label for="exampleFormControlInput1">&nbsp;</label>

                        <button type="submit" name="submitBtn" value="search" class=" form-control  btn btn-success"> ค้นหา
                        </button>
                    </div>
                </div>

                <div class="col-12 pt-2 row" style="border-top: 1px solid gray">
                    <button type="submit" name="submitBtn" value="export_excel" class="btn btn-info col-3"> ดาวน์โหลดไฟล์
                        Excel </button>
                </div>



            </div>
        </form>
    </div>

    <div class="card mt-4">
        <div class="card-header">
            <div class="card-title">
                <h5>ตารางสมุดจดเลขอ่านมาตรวัดน้ำ(ป.31)</h5>
                <div id="budgetyear_selected"></div>
                <div id="zone_selected"></div>
            </div>

        </div>
        <div class="card-body ">
            <div id="DivIdToExport" class="table-responsive">
                <table id="oweTable" class="table  table-bordered table-striped">
                    <thead>
                        <tr>
                            <th style="background-color: #FFD3B6">#</th>
                            <th style="background-color: #FFD3B6">รหัสผู้ใช้น้ำ</th>
                            <th style="background-color: #FFD3B6">ชื่อผู้ใช้น้ำ</th>
                            <th style="background-color: #FFD3B6">
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ที่อยู่&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                            <th style="background-color: #FFD3B6">
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;หมู่ที่&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                            <th style="background-color: #FFD3B6">เส้นทางจดมิเตอร์</th>
                            <th style="background-color: #FFD3B6">ยอดยกมา</th>
                            @foreach ($inv_period_list as $inv_period)
                                <th colspan="3" style="text-align: center; background-color: #FFD3B6">รอบบิล
                                    {{ $inv_period->inv_p_name }}</th>
                            @endforeach

                        </tr>
                        <tr>
                            <th style="background-color: #FFD3B6"></th>
                            <th style="background-color: #FFD3B6"></th>
                            <th style="background-color: #FFD3B6"></th>
                            <th style="background-color: #FFD3B6"></th>
                            <th style="background-color: #FFD3B6"></th>
                            <th style="background-color: #FFD3B6"></th>
                            <th style="background-color: #FFD3B6"></th>
                            @foreach ($inv_period_list as $inv_period)
                                <th style="background-color: #FFD3B6">วันที่อ่านมิเตอร์</th>
                                <th style="background-color: #FFD3B6">เลขอ่านมิเตอร์</th>
                                <th style="background-color: #FFD3B6">จำนวนที่ใช้</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1; ?>
                        @foreach ($usermeterinfos as $user)
                            @php
                                $aa = 0;
                                $subzone = 0;

                                if (isset($user->user->user_subzone->subzone_name)) {
                                    $subzone = $user->user->user_subzone->subzone_name;
                                }
                            @endphp

                            <tr>
                                <td>{{ $i++ }}</td>
                                <th>{{ $user['meter_id'] }}</th>
                                <th>

                                    {{ $user->user->prefix . '' . $user->user->firstname . ' ' . $user->user->lastname }}
                                </th>
                                <th style="text-align:right">{{ $user->user->address }}</th>
                                <th style="text-align:center">{{ $user->user->user_zone->zone_name }}</th>
                                <th style="text-align:center">{{ $subzone }}</th>

                                <th style="background-color:#F7E7DC; text-align:right">
                                    {{ number_format($user['bringForward']) }}</th>
                                @foreach ($user['infos'] as $inv_period)
                                    <th style="text-align:right">
                                        {{ number_format($inv_period['lastmeter']) }}
                                    </th>
                                    <th style="text-align:right">
                                        {{ number_format($inv_period['currentmeter']) }}
                                    </th>
                                    <th style="background-color:#DEF9C4; text-align:right">
                                        {{ number_format($inv_period['water_used']) }}
                                    </th>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endsection
            @section('script')
                <script src="https://cdn.datatables.net/buttons/2.3.2/js/dataTables.buttons.min.js"></script>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
                <script src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.html5.min.js"></script>
                <script src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.print.min.js"></script>
                <script
                    src="https://www.jqueryscript.net/demo/Export-Html-Table-To-Excel-Spreadsheet-using-jQuery-table2excel/src/jquery.table2excel.js">
                </script>
                <script>
                    let table;
                    let preloaderwrapper = document.querySelector('.preloader-wrapper')
                    let col_index = -1
                    var a = true;

                    $(document).ready(() => {
                        getData()
                        preloaderwrapper.classList.add('fade-out-animation')



                    });

                    $(".js-example-tokenizer").select2({
                        tags: true,
                        tokenSeparators: [',', ' ']
                    });


                    function getData(budgetyear = 'now', zone_id = 'all') {
                        console.log('b' + budgetyear + "  z" + zone_id)

                        table = $('#oweTable').removeAttr('width').DataTable({
                            "pagingType": "listbox",

                            exportOptions: {
                                rows: ':visible'
                            },
                            responsive: true,
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

                        }); //oweTable

                        if (a) {
                            a = false
                        }

                        $('#oweTable thead tr:eq(1) th').each(function(index) {
                            var title = $(this).text();
                            $(this).removeClass('sorting')
                            $(this).removeClass('sorting_asc')
                            if (index > 0 && index < 6) {
                                $(this).html(
                                    `<input type="text" data-id="${index}" class="form-control" id="search_col_${index}" placeholder="ค้นหา" />`
                                );
                            } else {
                                //$(this).html('')
                            }
                        });

                        $('#oweTable_filter').remove()

                        // //custom การค้นหา


                        $('#oweTable thead input[type="text"]').focus(function() {
                            var col = parseInt($(this).data('id'))
                            console.log('col= ' + col + "  colindex=" + col_index)
                            if (col !== col_index && col_index >= 0) {
                                $('input[type="text"]').val('')

                            }
                            col_index = col
                        })
                        setTimeout(() => {
                            $('.overlay').remove()
                        }, 2000)

                        console.log('ss', $('#budgetyear').val())
                        $('#budgetyear_selected').html($('#budgetyear').val())
                    }
                    $(document).on('keyup', 'input[type="text"]', function() {
                        let that = $(this)
                        var col = parseInt(that.data('id'))

                        var _val = that.val()
                        // if(col === 1){
                        //     var val = $.fn.dataTable.util.escapeRegex(
                        //         _val
                        //     );
                        //     table.column(col)
                        //     .search( val ? '^'+val+'.*$' : '', true, false )
                        //     .draw();
                        // }else{
                        table.column(col)
                            .search(_val)
                            .draw();
                        // }
                        col_index = col
                    })

                    $('#printBtn').click(function() {
                        $('#oweTable thead tr:eq(1)').addClass('hidden')

                        var tagid = 'oweTable'
                        var hashid = "#" + tagid;
                        var tagname = $(hashid).prop("tagName").toLowerCase();
                        var attributes = "";
                        var attrs = document.getElementById(tagid).attributes;
                        $.each(attrs, function(i, elem) {
                            attributes += " " + elem.name + " ='" + elem.value + "' ";
                        })
                        var divToPrint = $(hashid).html();
                        var head = '<html><head>' +
                            $("head").html() +
                            ' <style>body{background-color:white !important;}' +
                            '@page { size: landscape;}@media print {td,th {font-size:15px}}' +
                            '</style></head>';
                        var allcontent = head + "<body  onload='window.print()' >" + "<" + tagname + attributes + ">" +
                            divToPrint + "</" + tagname + ">" + "</body></html>";
                        var newWin = window.open('', 'Print-Window');
                        newWin.document.open();
                        newWin.document.write(allcontent);
                        newWin.document.close();
                        setTimeout(function() {
                            newWin.close();
                            // $('#texttitle').remove()

                            $('#oweTable thead tr:eq(1)').removeClass('hidden')

                        }, 220);
                    })
                </script>
            @endsection
