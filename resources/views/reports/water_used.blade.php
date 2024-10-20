@extends('layouts.admin1')

@section('mainheader')
    ข้อมูลปริมาณการใช้น้ำประปา
@endsection
@section('nav')
    <a href="{{ url('/reports') }}"> รายงาน</a>
@endsection
@section('report-water_used')
    active
@endsection
@section('style')
    <style>
        #stocks-div {
            height: 100% !important;
            width: 100% !important;
        }
    </style>
    <script src="{{asset('js/chartjs/chart.js_2.7.1.js')}}"></script>
@endsection
@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('reports.water_used') }}" method="GET">
                @csrf
                <div class="info-box">
                    <div class="info-box-content">
                        <div class="row">
                            <div class="col-md-2">
                                <label class="control-label">ปีงบประมาณ</label>

                                <select class="form-control" name="budgetyear_id" id="budgetyear_id">
                                    <option value="all"="">ทั้งหมด</option>
                                    <option value="1">2564</option>
                                    <option value="2">2565</option>
                                    <option value="3">2566</option>
                                    <option value="4" selected>2567</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="control-label">หมู่ที่</label>
                                <select class="form-control" name="zone_id" id="zone_id">
                                    <option value="all" selected>ทั้งหมด</option>
                                    <option value="1">หมู่ 1</option>
                                    <option value="2">หมู่ 2</option>
                                    <option value="3">หมู่ 3</option>
                                    <option value="4">หมู่ 4</option>
                                    <option value="5">หมู่ 5</option>
                                    <option value="6">หมู่ 6</option>
                                    <option value="7">หมู่ 7</option>
                                    <option value="8">หมู่ 8</option>
                                    <option value="9">หมู่ 9</option>
                                    <option value="10">หมู่ 10</option>
                                    <option value="11">หมู่ 11</option>
                                    <option value="12">หมู่ 12</option>
                                    <option value="13">หมู่ 13</option>
                                    <option value="14">หมู่ 14</option>
                                    <option value="15">หมู่ 15</option>
                                    <option value="16">หมู่ 16</option>
                                    <option value="17">หมู่ 17</option>
                                    <option value="18">หมู่ 18</option>
                                    <option value="19">หมู่ 19</option>
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
                                <button type="submit" class="form-control btn btn-primary searchBtn">ค้นหา</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <canvas id="barChart"></canvas>
            <script>
                var ctx = document.getElementById('barChart').getContext('2d');
                var myChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: @json($data['labels']),
                        datasets: [{
                            label: 'ปริมาณการใช้น้ำแยกตามหมู่บ้าน',
                            data: @json($data['data']),
                            borderColor: '#acc23',
                            backgroundColor: '#4dc9f6',
                            borderWidth: 2,
                            borderRadius: 10,
                            borderSkipped: false,
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            </script>
        </div>
    </div>


    <div class="card mt-2">
        <div class="card-header">
            <div class="card-title"></div>
            <div class="card-tools">
                {{-- <button class="btn btn-primary" id="printBtn">ปริ้น</button>
                <button class="btn btn-success" id="excelBtn">Excel</button> --}}
            </div>
        </div>
        <div class="card-body" id="DivIdToExport">
            <?php
            $sum_total = 0;
            $colspan = collect($waterUsedDataTables[0]['classify_by_inv_period'])->count();
            $last_colspan = $colspan + 2;
            $head_info_colspan = $colspan + 2;
            ?>

            <div class="card-body table-responsive">
                <table class="table table-striped text-nowrap" id="example">
                    <thead>
                        <tr>
                            <td colspan="{{ $head_info_colspan }}" class="text-center" style="opacity: 1">
                                <h5> ตารางสรุปปริมาณการใช้น้ำ
                                    {{ $zone_and_subzone_selected_text == 'ทั้งหมด' ? 'หมู่ที่ 1 - 19' : $zone_and_subzone_selected_text }}
                                    ปีงบประมาณ {{ $selected_budgetYear->budgetyear_name }}</h5>
                            </td>
                        </tr>
                        <tr class="">
                            <th class="text-center ">หมู่ที่</th>

                            @foreach ($waterUsedDataTables[0]['classify_by_inv_period'] as $item)
                                <th class="text-center">{{ $item['inv_p_name'] }}</th>
                            @endforeach
                            <th class="text-center">รวม(หน่วย)</th>

                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($waterUsedDataTables as $data)
                            {{-- {{dd($data)}} --}}
                            <?php $index = 0; ?>
                            <?php $sum_total += $data['water_used']; ?>
                            <tr>
                                {{-- <td class="bg-dark">
                                    {{ $data['zone_id'] }}
                                </td> --}}
                                <td class="">
                                    {{ $data['zone_name'] }}
                                </td>

                                @foreach ($data['classify_by_inv_period'] as $item)
                                    <td class="text-end  ivpsum{{ $index++ }}">
                                        {{ number_format($item['water_used']) }}
                                    </td>
                                @endforeach

                                @if ($selected_budgetYear->id == 3 && $data['zone_id'] == 7)
                                    @for ($i = 0; $i < 6; $i++)
                                        <td>0</td>
                                    @endfor
                                @endif
                                <td class="text-end h6">{{ number_format($data['water_used']) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                        @for ($i=0 ; $i < $head_info_colspan; $i++)
                        @if ($i==0)
                        <th class="text-end  tfoot_col{{ $i }}">รวม</th>
                        @else
                        <th class="text-end  tfoot_col{{ $i }}"></th>

                        @endif

                        @endfor
                        </tr>
                    </tfoot>
                </table>
            </div>

        </div><!--DivIdToExport-->
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

        $(document).ready(() => {
            let sum = 0;
            let total = 0;
            for (let i = 0; i < 12; i++) {
                sum = 0;
                $(`.ivpsum${i}`).each((index, v) => {
                    sum += parseInt($(v).text().split(',').join(''))
                });
                // if(sum >0){
                $('.xx').append(`<th class="text-right">
                ${new Intl.NumberFormat('en-IN').format(sum)}
                </th>`)
                total += sum;
                // }

            }
            // $('.xx').append(`<th class="bg-info text-right">${new Intl.NumberFormat('th-TH').format(total)}</th>`)

            $('.dt-buttons button').addClass('btn btn-info')
        });


        $('#zone_id').change(function() {
            //get ค่าsubzone
            $.get(`../api/subzone/getSubzone/${$(this).val()}`)
                .done(function(data) {
                    let text = '<option value="all" selected>ทั้งหมด</option>';
                    data.forEach(element => {
                        text += `<option value="${element.id}">${element.subzone_name}</option>`
                    });
                    $('#subzone_id').html(text)
                });
        }); //$#zone_id

        $('#printBtn').click(function() {
            var tagid = 'DivIdToExport'
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
            $("#example").table2excel({
                // exclude CSS class
                // exclude: ".noExl",
                name: "Worksheet Name",
                filename: 'aa', //do not include extension
                fileext: ".xlsx" // file extension
            })
        });
        let table = $('#example').DataTable({
            ordering: false,
            searching: false,
            paging: false,
            "lengthMenu": [
                [-1],
                ["All"]
            ],
            "language": {
                "search": ":",
                "lengthMenu": "",
                "info": "",
                "infoEmpty": "",
                "paginate": {
                    "info": "",
                },
            },
            dom: 'lBfrtip',
            buttons: [{
                extend: 'excelHtml5',
                'text': 'Excel'
            }, {
                extend: 'print',
                'text': 'ปริ้น'
            }],

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

                    for(let i = 1; i<=14; i++){
                      let  total_water_used = api
                        .column(i)
                        .data()
                        .reduce((a, b) => intVal(a) + intVal(b), 0);
                        // pageTotal_water_used = api
                        //     .column(i, {
                        //         page: 'current'
                        //     })
                        //     .data()
                            // .reduce((a, b) => intVal(a) + intVal(b), 0);
                        api.column(i).footer().innerHTML = '<div> ' +parseInt(total_water_used) + ' </div>';
                    }
                    // _water_used


                    // total_paid
                    // total_paid = api
                    //     .column(13)
                    //     .data()
                    //     .reduce((a, b) => intVal(a) + intVal(b), 0);

                    // // Total_paid over this page
                    // pageTotal_paid = api
                    //     .column(13, {
                    //         page: 'current'
                    //     })
                    //     .data()
                    //     .reduce((a, b) => intVal(a) + intVal(b), 0);

                    // // Update footer
                    // api.column(13).footer().innerHTML =
                    //     '<div class="subtotal"> ' + pageTotal_paid +
                    //     '</div> <div class="total" id="paid">  ' +
                    //     total_paid + ' </div>';

                    // // total_reserve
                    // total_reserve = api
                    //     .column(14)
                    //     .data()
                    //     .reduce((a, b) => parseFloat(a) + parseFloat(b), 0);

                    // // Total_reserve over this page
                    // pageTotal_reserve = api
                    //     .column(14, {
                    //         page: 'current'
                    //     })
                    //     .data()
                    //     .reduce((a, b) => parseFloat(a) + parseFloat(b), 0);

                    // // Update footer
                    // api.column(14).footer().innerHTML =
                    //     '<div class="subtotal"> ' + pageTotal_reserve.toFixed(2) +
                    //     '</div> <div class="total" id="reserve">  ' +
                    //     total_reserve.toFixed(2) + ' </div>';


                    // // total_vat
                    // total_vat = api
                    //     .column(15)
                    //     .data()
                    //     .reduce((a, b) => parseFloat(a) + parseFloat(b), 0);

                    // // Total_totalp idover this page
                    // pageTotal_vat = api
                    //     .column(15, {
                    //         page: 'current'
                    //     })
                    //     .data()
                    //     .reduce((a, b) => parseFloat(a) + parseFloat(b), 0);

                    // // Update footer
                    // api.column(15).footer().innerHTML =
                    //     '<div class="subtotal"> ' + pageTotal_vat.toFixed(2) +
                    //     '</div> <div class="total" id="vat">  ' +
                    //     total_vat.toFixed(2) + ' </div>';


                    //    // total_totalpaid
                    //    total_totalpaid = api
                    //     .column(16)
                    //     .data()
                    //     .reduce((a, b) => parseFloat(a) + parseFloat(b), 0);

                    // // Total_totalp idover this page
                    // pageTotal_totalpaid = api
                    //     .column(16, {
                    //         page: 'current'
                    //     })
                    //     .data()
                    //     .reduce((a, b) => parseFloat(a) + parseFloat(b), 0);

                    // // Update footer
                    // api.column(16).footer().innerHTML =
                    //     '<div class="subtotal"> ' + pageTotal_totalpaid.toFixed(2) +
                    //     '</div> <div class="total" id="totalpaid">  ' +
                    //     total_totalpaid.toFixed(2) + ' </div>';

                }
        })

        // fetch(`{{ url('../api/reports/water_used') }}`)
        //     .then(function (response) {
        //         return response.json() // แปลงข้อมูลที่ได้เป็น json
        //     })
        //     .then(function (data) {
        //         console.log(data); // แสดงข้อมูล JSON จาก then ข้างบน
        //         table =   $('#example').DataTable( {
        //         destroy: true,
        //         "pagingType": "listbox",
        //         "lengthMenu": [[10, 25, 50, 150, -1], [10, 25, 50, 150, "ทั้งหมด"]],
        //         "language": {
        //             "search": "ค้นหา:",
        //             "lengthMenu": "แสดง _MENU_ แถว",
        //             "info":       "แสดง _START_ ถึง _END_ จาก _TOTAL_ แถว",
        //             "infoEmpty":  "แสดง 0 ถึง 0 จาก 0 แถว",
        //             "paginate": {
        //                 "info": "แสดง _MENU_ แถว",
        //             },
        //         },
        //             data: data,
        //             columns: [
        //                 {
        //                     'title': '',
        //                     data: 'user_id',
        //                     render: function(data){
        //                         return `<i class="fa fa-plus-circle text-success findInfo"
    //                                     data-user_id="${data}"></i>`
        //                         }
        //                 },
        //                 {'title': 'เลขผู้ใช้งาน', data: 'user_id_str'},
        //                 {'title': 'ชื่อ-สกุล', data: 'name'},
        //                 {'title': 'เลขมิเตอร์', data: 'meternumber'},
        //                 {'title': 'บ้านเลขที่', data: 'address'},
        //                 {'title': 'หมู่ที่', data: 'zone_name'},
        //                 {'title': 'เส้นทาง', data: 'subzone_name'},


        //         ]
        //         });
        //         // $('.overlay').css('display', 'none');
        //         $('#example').find('.overlay_tr').remove()
        //         $('#example thead').find('#title').remove()
        //         let title = 'ตารางข้อมูลผู้ใช้น้ำประปาบ้านห้องแซง';
        //         title += $('#zone_id').val() === 'all' ? 'หมู่ที่ 1 - 19' :data[0].zone_name;
        //         // $('#example thead').first().remove()
        //         $('#example thead').prepend(`<tr id="title"><td colspan="7" class="text-center h4">${title}</td></tr>`)

        //         $('.paginate_page').text('หน้า')
        //         let val = $('.paginate_of').text()
        //         $('.paginate_of').text(val.replace('of', 'จาก'));
        //     });
        //     })


        // $.get('../api/reports/water_used')
        // .done(function(data){
        //     console.log(data)
        // });
        // });s
    </script>
@endsection
