@extends('layouts.admin1')

@section('nav-daily')
    active
@endsection
@section('nav-header')
    จัดการใบเสร็จรับเงินประจำวัน
@endsection
@section('nav-main')
    <a href="{{ route('report.index') }}"> รับชำระค่าเก็บขยะประจำวัน</a>
@endsection
@section('nav-current')
    {{-- รายชื่อผู้ใช้น้ำประปาที่ยังไม่ได้ชำระค่าเก็บขยะ --}}
@endsection
@section('page-topic')
    รับชำระค่าเก็บขยะประจำวัน
@endsection
@section('style')
    <style>
        .selected {
            background-color: lightblue;
        }

        .displayblock {
            display: block
        }

        .displaynone,
        .hidden {
            display: none
        }

        .modal-dialog {
            width: 75rem;
            margin: 30px auto;
        }

        .sup {
            color: blue
        }

        .fs-7 {
            font-size: 0.7rem
        }

        .table {
            border-collapse: collapse
        }

        .table thead th {
            padding: 0.55rem 0.5rem;
            text-transform: capitalize;
            letter-spacing: 0;
            border-bottom: 1px solid #e9ecef;
            color: black;
            text-align: center
        }

        .mt-025 {
            margin-top: 0.15rem
        }

        .input-search-by-title {
            border-radius: 10px 10px;
            height: 1.65rem;
            border: 1px solid #2077cd
        }

        @media (min-width:568px) {
            .modal {
                --bs-modal-margin: 1.75rem;
                --bs-modal-box-shadow: 0 0.3125rem 0.625rem 0 rgba(0, 0, 0, .12)
            }

            .modal-dialog {
                max-width: 75rem;
                margin-right: auto;
                margin-left: auto
            }
        }
    </style>
@endsection
@section('content')
    <form action="{{ route('report.daily_search') }}" method="POST">
        @csrf
        <div class="card" style="border: 1px solid blue">
            <div class="card-header">
                <span class="h-5 mr-2"> ค้นหาจากเส้นทางจดมิเตอร์ </span>
                <a class="" data-bs-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false"
                    aria-controls="collapseExample">
                    <i class="far fa-arrow-alt-circle-down aa"></i>
                </a>
            </div>
            <div class="card-body row collapse show" id="collapseExample">

                <div class="row">
                    <div class="form-group col-12 col-md-3">
                        <label class="label-control">ปีงบประมาณ</label>
                        <select class="form-control" name="budgetyear1" id="budgetyear1">
                            <option value="all" {{ $budgetyear_id == 'all' ? 'selected' : '' }}>ทั้งหมด</option>
                            @foreach ($budgetyearlists as $item)
                                <option value="{{ $item->id }}" {{ $budgetyear_id == $item->id ? 'selected' : '' }}>{{ $item->budgetyear_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-12 col-md-3">
                        <label class="label-control">เดือน</label>
                        <select class="form-control" name="monthlists" id="monthlists">
                            <option value="all" {{ $month == 'all' ? 'selected' : '' }}>ทั้งหมด</option>
                            @foreach ($monthlists as $key => $item)
                                <option value="{{ $item }}" {{ $month == $item ? 'selected' : '' }}>{{ $key }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-12 col-md-3">
                        <label class="label-control">วันที่</label>
                        <select class="form-control" name="daily_paids" id="daily_paids">
                            <option value="all" {{ isset($date) ? 'selected' : '' }}>ทั้งหมด</option>
                            @foreach ($daily_paids as $item)
                            @php
                                $explode_date = explode(" ",$item->updated_at);
                                $date         = App\Http\Controllers\Api\FunctionsController::engDateToThaiDateFormat($explode_date[0]);
                            @endphp
                                <option value="{{ $explode_date[0] }}">{{ $date }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-12 col-md-2">
                        <label class="label-control">&nbsp;</label>
                        <button type="submit" class="btn btn-info form-control">ค้นหา </button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="example-date-input" class="form-control-label">เริ่ม</label>
                            <input class="form-control" type="date" name="startdate" value="" id="example-date-input">
                        </div>

                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="example-date-input" class="form-control-label">สิ้นสุด</label>
                            <input class="form-control datepicker" placeholder="Please select date" type="text" onfocus="focused(this)" onfocusout="defocused(this)">

                        </div>
                    </div>


                </div><!--row-->
            </div>


        </div>
    </form>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card" style="border: 1px solid blue">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="invoiceTable">
                            <thead>
                                <th>#</th>
                                <th>ชื่อ</th>
                                <th>รหัสสมาชิก</th>
                                <th>บ้านเลขที่</th>
                                <th>หมู่</th>
                                <th>ปีงบประมาณ</th>
                                <th>วันที่ชำระ</th>
                                <th>จำนวนที่ต้องชำระ(บาท/ปี)</th>
                                <th>ชำระแล้ว(บาท)</th>
                                <th>ค้างชำระ(บาท)</th>
                                <th>รวมเป็นเงิน(บาท)</th>
                                <th>หมายเหตุ</th>
                            </thead>
                            <tbody>
                                @php
                                    $count = 1;
                                @endphp
                                @foreach ($daily_paids as $item)
                                @php
                                    $owe            = number_format($item->paid_per_budgetyear - $item->paid,2);
                                    $subtotal       = $owe == 0 ? $item->paid : $owe;
                                    $exploded_date  = explode(" ",$item->updated_at);
                                    $paid_date      =  App\Http\Controllers\Api\FunctionsController::engDateToThaiDateFormat($exploded_date[0]);
                                @endphp
                                    <tr>
                                        <td>{{ $count++ }}</td>
                                        <td>{{ $item->user->prefix."".$item->user->firstname." ".$item->user->lastname }}</td>
                                        <td class="text-center">{{ $item->user->usermeterinfos->meternumber }}</td>
                                        <td class="text-end">{{ $item->user->address }}</td>
                                        <td>{{ $item->user->user_zone->zone_name }}</td>
                                        <td class="text-center">{{ $item->budgetyear->budgetyear_name }}</td>
                                        <td class="text-center">{{ $paid_date }}</td>
                                        <td class="text-end">{{ number_format($item->paid_per_budgetyear,2) }}</td>
                                        <td class="text-end">{{ number_format($item->paid,2) }}</td>
                                        <td class="text-end">{{ $owe }}</td>
                                        <td class="text-end">{{ number_format($subtotal,2) }}</td>
                                        <td>{{ $item->comment }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')

<script type="text/javascript" src="{{asset('js/bootstrap-datepicker/js/bootstrap-datepicker-thai.js')}}"></script>
<script src="https://npmcdn.com/flatpickr/dist/flatpickr.min.js"></script>
<script src="https://npmcdn.com/flatpickr/dist/l10n/th.js"></script>

    <script>
         if (document.querySelector('.datepicker')) {
        flatpickr('.datepicker', {
            enableTime: true,
            altInput: true,
            altFormat: 'd-m',
    dateFormat: "d-m-Y",
    "locale": "th"
        }); // flatpickr
      }
        // $('.datepicker').datepicker({
        //     format: 'dd/mm/yyyy',
        //     todayBtn: true,
        //     language: 'th', //เปลี่ยน label ต่างของ ปฏิทิน ให้เป็น ภาษาไทย   (ต้องใช้ไฟล์ bootstrap-datepicker.th.min.js นี้ด้วย)
        //     thaiyear: true,
        // }).datepicker("setDate", new Date());; //กำหนดเป็นวันปัจุบัน

        $(document).on('change', '#monthlists', function(){
            if($(this).val() === "all"){
                $('#daily_paids').html("<option value='all'>ทั้งหมด</option")
            }else{
                let budgetyear = $('#budgetyear1').val();
                let month      = $(this).val();
                $.get(`/report/get_date/${budgetyear}/${month}`, function(datas){
                    let dates =  JSON.parse(datas);
                    console.log('dates', dates)
                    let text  = "<option value='all'>ทั้งหมด</option>";
                    dates.forEach(element => {
                        text += `<option value='${element.index}'>${element.date}</option>`
                    });
                    $('#daily_paids').html(text)
                })
            }



        });


        let a = true
        table = $('#invoiceTable').DataTable({
            responsive: true,

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
            select: true,
        }) //table
        $('#invoiceTable_filter').remove()
        if (a) {
            $('#invoiceTable thead tr').clone().appendTo('#invoiceTable thead');
            a = false
        }
        $('#invoiceTable thead tr:eq(1) th').each(function(index) {
            var title = $(this).text();
            $(this).removeClass('sorting')
            $(this).removeClass('sorting_asc')
            if (index < 5) {
                $(this).html(
                    `<input type="text" data-id="${index}" class="col-md-12 input-search-by-title" id="search_col_${index}" placeholder="ค้นหา" />`
                );
            } else {
                $(this).html('')
            }
        });
        //custom การค้นหา
        let col_index = -1
        $('#invoiceTable thead input[type="text"]').keyup(function() {
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
                if (col === 0 || col === 4) {
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

        });

    </script>
@endsection
