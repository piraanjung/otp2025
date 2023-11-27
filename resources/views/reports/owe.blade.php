@extends('layouts.admin1')

@section('nav-reports-owe')
    active
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
        <div class="col-7">
            <div class="card mt-4">
                <form action="{{ route('reports.owe_search') }}" method="post">
                    @csrf
                    <div class="card-body row">
                        <div class="col-3">
                            <div class="form-group">
                                <label for="exampleFormControlInput1">ค้นหาจากหมู่ที่</label>
                                <select class="form-select" id="zone1" name="zone1[]" data-placeholder="เลือก.." multiple>
                                    <option value="">เลือก..</option>
                                    @foreach ($owe_zones as $zone)
                                        <option value="{{$zone->id}}">{{ $zone->zone_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="form-group">
                                <label for="exampleFormControlInput1">ค้นหาจากเส้นทาง</label>
                                <select class="form-select" id="subzone" name="subzone[]" data-placeholder="เลือก.." multiple>
                                </select>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="form-group">
                                <label for="exampleFormControlInput1">ค้นหาจากรอบบิล</label>
                                <select class="form-select" name="inv_period[]" id="inv_period" data-placeholder="เลือก.." multiple>
                                    <option value="">เลือก</option>
                                    @foreach ($owe_inv_periods as $inv_period)
                                        <option value="{{$inv_period->id}}">{{ $inv_period->inv_p_name }}</option>
                                    @endforeach

                                </select>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="form-group">
                                <label for="exampleFormControlInput1">&nbsp;</label>
                                <button type="submit" class="btn btn-info btn-sm form-control"> ค้นหา </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-5 ">
            <?php
            $clude_totalxVat7 = number_format($crudetotal_sum * 0.07, 2);
            $crudetotal_total = $crudetotal_sum + $crudetotal_sum * 0.07;
            ?>
            <div class="card  mb-2">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-6">
                            <div class="numbers">
                                <h5 class="font-weight-bolder mb-0">
                                    {{ number_format($crudetotal_total, 2) }}
                                    <span class="text-success text-sm font-weight-bolder">ค้างค่าใช้น้ำ<sup>(บาท)</sup>
                                        <div class=""> </div>
                                    </span>
                                </h5>
                            </div>
                        </div>
                        <div class="col-6 text-end">
                            <div class="text-success text-sm font-weight-bolder">
                                [ {{ number_format($crudetotal_sum, 2) }} + {{ $clude_totalxVat7 }}<sup>(vat 7%)</sup> ]
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card  mb-2">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-7">
                            <?php
                            $_reservemeter_sum = number_format($reservemeter_sum, 2);
                            $retservemeterxVat7 = number_format($reservemeter_sum * 0.07, 2);
                            $retservemeter_total = $reservemeter_sum + $reservemeter_sum * 0.07;
                            ?>
                            <div class="numbers">
                                <h5 class="font-weight-bolder mb-0">
                                    {{ number_format($retservemeter_total, 2) }}
                                    <span
                                        class="text-success text-sm font-weight-bolder">ค้างค่ารักษามิเตอร์<sup>(บาท)</sup>
                                        <div class=""> </div>
                                    </span>
                                </h5>
                            </div>
                        </div>
                        <div class="col-5 text-end">
                            <div class="text-success text-sm font-weight-bolder">
                                [ {{ $_reservemeter_sum . ' + ' . $retservemeterxVat7 }}<sup>(vat 7%)</sup> ]
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card  mb-2">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-7">
                            <?php
                            $total = $crudetotal_total + $retservemeter_total;
                            ?>
                            <div class="numbers">
                                <h5 class="font-weight-bolder mb-0">
                                    {{ number_format($total, 2) }}
                                    <span class="text-success text-sm font-weight-bolder">รวมทั้งสิ้น<sup>(บาท)</sup>
                                        <div class=""> </div>
                                    </span>
                                </h5>
                            </div>
                        </div>
                        <div class="col-5 text-end">
                            <div class="text-success text-sm font-weight-bolder">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body table-responsive">
            <table class="table" id="example">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>ชื่อ-สกุล</th>
                        <th>เลขมิเตอร์</th>
                        <th>บ้านเลขที่</th>
                        <th>เส้นทางจดมิเตอร์</th>
                        <th>รอบบิล</th>
                        <th>ก่อนจด<div><sup>หน่วย</sup></div>
                        </th>
                        <th>หลังจด <div><sup>หน่วย</sup></div>
                        </th>
                        <th>ใช้น้ำ <div><sup>หน่วย</sup></div>
                        </th>
                        <th>เป็นเงิน <div><sup>บาท</sup></div>
                        </th>
                        <th>Vat 7% <div><sup>บาท</sup></div>
                        </th>
                        <th>รวมเป็นเงิน <div><sup>บาท</sup></div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $name_first_row = true;

                    $index_row = 0;
                    ?>
                    @foreach ($owe_users as $k => $meters)
                        <?php $mod = fmod($index_row++, 2); ?>
                        <?php $name_first_row = true;
                         ?>
                        @foreach ($meters as $key => $meter)
                        <?php $check_same_meternumber = 0; ?>
                            @foreach ($meter as $invoice)
                                <tr class="{{ $mod == 0 ? 'group tr' . $index_row : 'tr_even' }}">
                                    <td style="{{ $name_first_row == true ? '' : 'opacity:0.0' }}">{{ $index_row }}
                                    </td>
                                    <td style="{{ $name_first_row == true ? '' : 'opacity:0' }}"
                                        class="align-middle text-center h5 text-info">
                                        {{ $invoice->usermeterinfos->user->firstname . ' ' . $invoice->usermeterinfos->user->lastname }}
                                        <?php $name_first_row = false; ?>
                                    </td>
                                    <td style="{{ $check_same_meternumber == 0 ? '' : 'opacity:0' }}"
                                        class="align-middle text-center">
                                        <?php
                                        echo '<span class="text-primary h6">' . $invoice->usermeterinfos->meternumber . '</span>';

                                        ?>
                                    </td>
                                    <td style="{{ $check_same_meternumber == 0 ? '' : 'opacity:0' }}"
                                        class="align-middle text-center">
                                        <?php
                                        echo '<div class="text-primary h6">' . $invoice->usermeterinfos->user->address . ' ' . $invoice->usermeterinfos->user->user_zone->zone_name . '</div>';
                                        ?>
                                    </td>
                                    <td style="{{ $check_same_meternumber == 0 ? '' : 'opacity:0' }}"
                                        class="align-middle text-center">
                                        <?php
                                        echo '<div class="text-primary h6">' . $invoice->usermeterinfos->undertake_subzone->subzone_name . '</div>';

                                        $check_same_meternumber = $invoice->usermeterinfos->meternumber;
                                        ?>
                                    </td>
                                    <td class="text-center">{{ $invoice->invoice_period->inv_p_name }}</td>
                                    <td class="text-end">{{ $invoice->lastmeter }}</td>
                                    <td class="text-end">{{ $invoice->currentmeter }}</td>
                                    <td class="text-end">{{ $invoice->water_used }}</td>
                                    <td class="text-end">{{ $invoice->total }}</td>
                                    <td class="text-end">{{ number_format($invoice->vat7, 2) }}</td>
                                    <td class="text-end">{{ number_format($invoice->total_net, 2) }}</td>
                                </tr>
                            @endforeach
                        @endforeach
                    @endforeach

                </tbody>
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
    <script>
        $('#zone1').select2({
            theme: "bootstrap-5",
            width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
            placeholder: $(this).data('placeholder'),
            closeOnSelect: false,
        });
        $('#subzone').select2({
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

        $(document).on('change', "#zone1", function(e){
            //get ค่าsubzone
            let zone_id = $(this).val()

            $.post(`../api/subzone`,{zone_id : zone_id})
                .done(function (data) {
                    console.log('data',data)
                    let text = zone_id !== 'all' ? '<option value="">เลือก</option>' : '<option value="all">ทั้งหมด</option>';
                    if(data.length > 1){
                        text += `<option value="all">ทั้งหมด</option>`;
                    }
                    data.forEach(element => {
                        text += `<option value="${element.id}">${element.zone.zone_name} - ${element.subzone_name}</option>`
                    });
                    $('#subzone').html(text)
                });
        });
    </script>
@endsection
