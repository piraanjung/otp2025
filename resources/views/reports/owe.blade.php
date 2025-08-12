@extends('layouts.admin1')

@section('nav-reports-owe')
    active
@endsection

@section('nav-header')
    รายงาน
@endsection
@section('nav-main')
    <a href="{{ route('reports.owe') }}"> ผู้ค้างชำระค่าน้ำประปา</a>
@endsection

@section('nav-topic')
    ตารางผู้ค้างชำระค่าน้ำประปา
@endsection
@section('style')
    <style>
        .table {
            border-collapse: collapse
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

        .dataTables_length,
        .dt-buttons,
        .dataTables_filter,
        .select_row_all,
        .deselect_row_all,
        .create_user {
            display: inline-flex;
            margin-right: 5px;
        }

        .hidden {
            display: none
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
{{--    
     ปีงบประมาณ {{$budgetyears[0]->budgetyear_name}}
     รอบบิล 

     @php
     
        if($selected_inv_periods[0] == 'all'){
            echo 'ทั้งหมด';
        }else{
            foreach ($selected_inv_periods as $invp){
                echo $invp->inv_p_name;
            }
        }
     @endphp
    
     หมู่ที่  ทั้งหมด เส้นทาง ทั้งหมด --}}
    <div class="row mb-4">
        <div class="col-8">
            <div class="card">
                <form action="{{ route('reports.owe_search') }}" method="post" onsubmit="return submit()">
                    @csrf
                    <div class="card-body row">
                        <div class="col-2">
                            <div class="form-group">
                                <label for="exampleFormControlInput1">ปีงบประมาณ</label>
                                <select class="form-control js-example-tokenizer" name="budgetyear[]" id="budgetyear"
                                    data-placeholder="เลือก.." multiple>
                                    @foreach ($budgetyears as $budgetyear)
                                        <option value="{{ $budgetyear->id }}" {{ in_array($budgetyear->id, $budgetyears_selected) ? 'selected' : '' }}>
                                            {{ $budgetyear->budgetyear_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="form-group">
                                <label for="exampleFormControlInput1">รอบบิล</label>
                                <select class="form-control js-example-tokenizer" name="inv_period[]" id="inv_period"
                                    data-placeholder="เลือก.." multiple>
                                    <option value="all" {{$selected_inv_periods[0] == 'all' ? 'selected' : ''}} >ทั้งหมด</option>
                                    @foreach ($inv_periods as $inv_period)
                                        <option value="{{ $inv_period->id }}"
                                            {{ in_array($inv_period->id, collect($selectedInvPeriodID)->toArray()) ? 'selected' : '' }}>
                                            {{ $inv_period->inv_p_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="form-group">
                                <label for="exampleFormControlInput1">หมู่ที่</label>
                                <select class="form-control js-example-tokenizer" id="zone" name="zone[]"
                                    data-placeholder="เลือก.." multiple>
                                    <option value="all" {{$zone_selected[0] == 'all' ? 'selected' : ''}}>ทั้งหมด</option>

                                    @foreach ($zones as $zone)
                                        <option value="{{ $zone->id }}" {{in_array($zone->id, $zone_selected) ? 'selected' : ''}}>{{ $zone->zone_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="form-group">
                                <label for="exampleFormControlInput1">เส้นทาง</label>
                                <select class="form-control js-example-tokenizer" id="subzone" name="subzone[]"
                                    data-placeholder="เลือก.." multiple>
                                    <option value="all" {{$subzone_selected[0] == 'all' ? 'selected' : ''}}>ทั้งหมด</option>

                                    @foreach ($subzones as $subzone)
                                        <option value="{{ $subzone->id }}" {{in_array($subzone->id, $subzone_selected) ? 'selected' : ''}}>{{ $subzone->subzone_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-1">
                            <div class="form-group">
                                <label for="exampleFormControlInput1">&nbsp;</label>

                                <button type="submit" name="searchBtn" value="true"
                                    class=" form-control  btn btn-success"> 
                                        <i class="fa fa-search text-bolder"></i>
                                </button>
                            </div>
                        </div>

                        <div class="col-12 pt-2 row" style="border-top: 1px solid gray">
                            <button type="submit" name="excelBtn" value="overview" class="btn btn-info col-5"
                                style="margin-right:5px"> ดาวน์โหลดไฟล์ Excel <div>แสดงผลรวมการค้างชำระ</div> </button>
                            <button type="submit" name="excelBtn" value="details" class="btn btn-warning col-5 ml-1">
                                ดาวน์โหลดไฟล์ Excel <div>แสดงรายละเอียดการค้างชำระ</div></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-4">
            <?php
            $clude_totalxVat7 = 0;// number_format($crudetotal_sum * 0, 2);
            $crudetotal_total = $crudetotal_sum + $crudetotal_sum * 0;
            ?>
            <div class="card  mb-2">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-6">ค้างค่าใช้น้ำ</div>
                        <div class="col-6 text-end">{{ number_format($crudetotal_total, 2) }} บาท</div>
                        <div class="col-12">
                            <div class="text-success text-sm font-weight-bolder">
                                [ {{ number_format($crudetotal_sum, 2) }} + {{ $clude_totalxVat7 }}<sup>(vat 7%)</sup>
                                ]
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card  mb-2">
                <div class="card-body p-3">
                    <div class="row">
                        <?php
                        $_reservemeter_sum = number_format($reservemeter_sum, 2);
                        $reservemeterxVat7 = 0;//number_format($reservemeter_sum * 0.07, 2);
                        $reservemeter_total = $reservemeter_sum ;//+ $reservemeter_sum * 0.07;
                        ?>
                        <div class="col-6">ค้างค่ารักษามิเตอร์</div>
                        <div class="col-6 text-end">{{ number_format($reservemeter_total, 2) }} บาท</div>
                        <div class="col-12">
                            <div class="text-success text-sm font-weight-bolder">
                                [ {{ $_reservemeter_sum }} + {{ $reservemeterxVat7 }}<sup>(vat 7%)</sup> ]
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card  mb-2">
                <div class="card-body p-3">
                    <div class="row">
                        <?php
                        $total = $crudetotal_total + $reservemeter_total;
                        ?>
                        <div class="col-6">รวมทั้งสิ้น</div>
                        <div class="col-6 text-end">{{ number_format($total, 2) }} บาท</div>

                    </div>

                </div>
            </div>
        </div>
    </div>
    @if (collect($owes)->isNotEmpty())

        <div class="card">
            <div class="card-body table-responsive">
                <form action="{{ route('admin.owepaper.print') }}" method="POST" onsubmit="return check();">
                    @csrf
                    <input type="hidden" name="from_view" value="owepaper">

                    <input type="submit" class="btn mb-3 text-end hidden"
                        style="background-color:#17c1e8;color:white !important" id="print_multi_inv"
                        value="ปริ้นใบแจ้งเตือนชำระหนี้ที่เลือก">

                    <table class="table" id="example">
                        <thead>
                            <tr>
                                <td>#</td>
                                <td></td>
                                <td>เลขมิเตอร์</td>
                                <td>แจ้งเตือน(ครั้ง)</td>
                                <td>ชื่อ-สกุล</td>
                                <td>บ้านเลขที่</td>
                                <td>หมู่ที่</td>
                                <td>ซอย</td>
                                <td>เส้นทางจดมิเตอร์</td>
                                <td>ค้างชำระ <div><sup>รอบบิล</sup></div>
                                </td>
                                <td>เป็นเงิน <div><sup>บาท</sup></div>
                                </td>
                                <td>Vat 7% <div><sup>บาท</sup></div>
                                </td>
                                <td>ค่ารักษามิเตอร์<div><sup>บาท</sup></div>
                                </td>
                                <td>รวมเป็นเงิน <div><sup>บาท</sup></div>
                                </td>
                            </td>
                            {{-- <td  class="text-center">สถานะ 
                            </td> --}}
                                {{-- <td></td> --}}
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $name_first_row = true;
                            
                            $index_row = 0;
                           
                            ?>
                            @foreach ($owes as $owe)
                            {{-- {{dd($owe['owe_infos'][0]->usermeterinfos->user->address)}} --}}
                                <tr>
                                    <td>{{ ++$index_row }}/ </td>
                                    <td>
                                        <input type="checkbox" class="invoice_id" style="opacity:0"
                                            name="meter_id[{{ $owe['meter_id_fk'] }}]">
                                        <i class="fa fa-plus-circle text-success fa-2x findInfo"
                                            data-user_id="{{ $owe['user_id'] }}"></i>
                                    </td>
                                    <td>{{ $owe['meter_id_fk'] }}</td>
                                    <td class="text-center">{{ $owe['printed_time'] }}</td>
                                    <td>{{ $owe['owe_infos'][0]->usermeterinfos->user->prefix."".$owe['owe_infos'][0]->usermeterinfos->user->firstname." ".$owe['owe_infos'][0]->usermeterinfos->user->lastname }}</td>
                                    {{-- <td></td> --}}
                                    <td>{{$owe['owe_infos'][0]->usermeterinfos->user->address }}</td>
                                    <td>{{ $owe['owe_infos'][0]->usermeterinfos->user->user_zone->zone_name}}</td>
                                    <td>{{ $owe['owe_infos'][0]->usermeterinfos->user->user_subzone->subzone_name}}</td>
                                    <td>{{ 
                                        $owe['owe_infos'][0]->usermeterinfos->user->subzone_id == 13 ? 'เส้นหมู่13' : 
                                            $owe['owe_infos'][0]->usermeterinfos->user->user_subzone->subzone_name
                                    }}</td>
                                    <td class="text-end">
                                        {{-- {{ $owe['owe_count'] }} --}}
                                        @foreach ($owe['owe_infos'] as $item)
                                        <div>{{$item->invoice_period->inv_p_name}}</div>

                                        @endforeach
                                    </td>
                                    <td class="text-end">{{ $owe['paid'] }}</td>
                                    <td class="text-end">{{ $owe['vat'] }}</td>
                                    <td class="text-end">{{$owe['owe_count']*10}}</td>
                                    <td class="text-end">{{ $owe['totalpaid'] }}</td>
                                    {{-- <td class="text-center">{{ $owe['status'] =='owe' ? 'ค้างชำระ' : 'กำลังออกใบแจ้งหนี้' }}</td> --}}

                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
    @else
        <div class="card">
            <div class="card-body text-center">
                <h3>ไม่พบข้อมูลการค้างชำระ 

                @php
                
                if(isset($owe_inv_periods)){
                    // dd($owe_zones[0]);
                    echo "<br>ของ หมู่ ".$owe_zones[0]." ประจำเดือน ". $owe_inv_periods;
                }
                @endphp
                </h3>
            </div>
        </div>
    @endif
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
        $(".js-example-tokenizer").select2({
            tags: true,
            tokenSeparators: [',', ' ']
        });

        let a = true
        let preloaderwrapper = document.querySelector('.preloader-wrapper')
        
        $(document).ready(function() {
            $('.select2-search--inline').addClass('hidden')
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
               

            });


            $(`<div class="deselect_row_all ml-2">
                    <label class="m-0">&nbsp;</label>
                    <button type="button" class="btn btn-secondary btn-sm hidden" id="deselect-all">ยกเลิกเลือกทั้งหมด</button>
                </div>
                <div class=" select_row_all">
                    <label class="m-0">&nbsp;</label>
                    <button type="button" class="btn btn-success btn-sm" id="select_row_all">เลือกทั้งหมด</button>
                </div>`).insertAfter('.dataTables_length')

            preloaderwrapper.classList.add('fade-out-animation')

        });

        function submit(){
            preloaderwrapper.classList.add('fade-out-animation')
            return true
        }

        $(document).on('click', 'tbody tr', function() {
            $(this).hasClass('selected') ? $(this).removeClass('selected') : $(this).addClass('selected');
            if ($('tbody tr').hasClass('selected')) {
                $('#print_multi_inv').removeClass('hidden')
                $(this).first().find('input[type=checkbox]').prop('checked', true)
            } else {
                $('#print_multi_inv').addClass('hidden')

                $(this).first().find('input[type=checkbox]').prop('checked', false)

            }
        });

        $(document).on('click', '#deselect-all', function(e) {
            $("tbody tr.selected").removeClass('selected')
            $('#print_multi_inv').addClass('hidden')
            $('#deselect-all').addClass('hidden')
            $('#select_row_all').removeClass('hidden')
        });

        $(document).on('click', '.select_row_all', function(e) {
            $("tbody tr").addClass('selected');
            $('#print_multi_inv').removeClass('hidden')
            $('#deselect-all').removeClass('hidden')
            $('#select_row_all').addClass('hidden')


            $('tr.selected').find('.invoice_id').prop('checked', true)
        });

        $(".paginate_select").addClass('form-control-sm mb-3 float-right')

        $(document).on('change', "#zone", function(e) {
            //get ค่าsubzone
            let zone_id = $(this).val()
            console.log('zone_id', zone_id)
            $.post(`../api/subzone`, {
                    zone_id: zone_id
                })
                .done(function(data) {
                    text = ``;
                    data.forEach(element => {

                        console.log('unde', element)

                        text +=
                            `<option value="${element.id}" selected>${element.zone.zone_name} - ${element.subzone_name}</option>`
                    });
                    $('#subzone').html(text)
                });
        });

        $(document).on('change', "#budgetyear", function(e) {
            //get ค่าsubzone
            let budgetyear_arr = $(this).val()
            console.log('budgetyear_arr1', budgetyear_arr)
            $.post(`../api/invoice_period/inv_period_lists_post`, {
                    budgetyear_id: budgetyear_arr
                })
                .done(function(data) {
                    let text = ""
                    data.forEach(element => {
                        text += `  <optgroup label="ปีงบ ${element.budgetyear_name}">`
                        element.invoice_period.forEach(ele => {
                            text +=
                                `<option value="${ele.budgetyear_id}">${ele.inv_p_name}</option>`
                        });
                        text += `</optgroup>`
                    });
                    $('#inv_period').html(text)
                });
        });


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
                    console.log('datas', data)
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

        function owe_by_user_id_format(d) {
            console.log('d', d[0].usermeterinfos[0].invoice)
            let a = 0;
            let text = `
            <div class="table table-responsive  border border-success rounded ml-3 mr-3">
            <table class="table table-striped">
                <thead>
                    <tr class="bg-info">
                    <th class="text-center">วันที่</th>
                    <th class="text-center">รอบบิล</th>
                    <th class="text-center">ยอดครั้งก่อน</th>
                    <th class="text-center">ยอดปัจจุบัน</th>
                    <th class="text-center">จำนวนที่ใช้(หน่วย)</th>
                    <th class="text-center">คิดเป็นเงิน(บาท)</th>
                    <th class="text-center">vat 7%(บาท)</th>
                    <th class="text-center">รวมเป็นเงินทั้งสิ้น(บาท)</th>
                    <th class="text-center">สถานะ</th>
                    </tr>
                </thead>
                <tbody>`;
            d[0].usermeterinfos[0].invoice.forEach(element => {
                let status
                console.log('forEach', element.status)
                if (element.status === 'owe' || element.status === 'invoice') {
                    if (element.status == 'owe') {
                        status = `<span class="text-danger">ค้างชำระ</span>`
                    } else if (element.status == 'invoice') {
                        status = `<span class="text-warning">กำลังออกใบแจ้งหนี้</span>`
                    }
                    let diff = element.currentmeter - element.lastmeter
                    let total = diff > 0 ? (diff * 6)+10 : 10
                    text += `
                                    <tr>
                                    <td class="text-center">${element.updated_at_th}</td>
                                    <td class="text-center">${element.invoice_period.inv_p_name}</td>
                                    <td class="text-end">${element.lastmeter}</td>
                                    <td class="text-end">${element.currentmeter}</td>
                                    <td class="text-end">${element.water_used }</td>
                                    <td class="text-end">${ element.paid }</td>
                                    <td class="text-end">${ element.vat }</td>
                                    <td class="text-end">${ element.totalpaid }</td>
                                    <td class="text-center">${status}</td>
                                    </tr>
                            `;
                    a = 1;

                } // if
            });
            if ( Object.keys(d[0].usermeterinfos[0].invoice) === 0) {
                console.log('sss')
                text += `<tr><td colspan="7" class="text-center h4">ไม่พบข้อมูลการค้างชำระ</td></tr>`
            }
            text += `</tbody>
            </table>
            </div>`;
            console.log('text', text)
            return text;
        }
    </script>
@endsection
