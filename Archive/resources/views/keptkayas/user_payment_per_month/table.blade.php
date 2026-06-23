@extends('layouts.admin1')

@section('nav-paid_per_billingcycle-table')
    active
@endsection
@section('nav-header')
    จัดการใบเสร็จรับเงิน
@endsection
@section('nav-main')
    <a href="{{ route('invoice.index') }}"> รับชำระค่าเก็บขยะ</a>
@endsection
@section('nav-current')
    {{-- รายชื่อผู้ใช้น้ำประปาที่ยังไม่ได้ชำระค่าเก็บขยะ --}}
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

@section('page-topic')
    <div class="h-100">
        <h5 class="mb-1">ค้นหา : ปีงบประมาณ</h5>
        <form action="{{ route('user_payment_per_month.table_search') }}" method="POST"
            class="d-flex justify-content-between">
            @csrf

            <select class="js-example-basic-single form-control" name="user_info">
                <option>เลือก...</option>
                @foreach ($budgetyears as $budgetyear)
                    <option value="{{ $budgetyear->id }}">
                        {{ $budgetyear->budgetyear_name }}

                    </option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-secondary ms-3"><i class="fa fa-search">ค้นหา</i></button>
        </form>

    </div>
@endsection
<div class="row mt-4">
    <div class="col-12">
        <div class="card" style="border: 1px solid blue">
            <div class="card-header"><button class="btn btn-success" id="excelBtn">Excel</button></div>
            <div class="card-body">
                <div class="table-responsive" id="DivIdToExport">
                    <table class="table" id="invoiceTable" border="1">
                        <thead>
                            <tr>
                                <th colspan="28" class="text-center">
                                    เทศบาลตำบลห้องแซง
                                    <div>รายละเอียดผู้ชำระรายได้อื่นๆ(กค.3) ปี พศ. {{ $budgetyear->budgetyear_name }}
                                    </div>
                                    <div>ประเภทรายได้ <span
                                            style="text-decoration:underline dotted"">ค่าเก็บขยะรายปี</span>
                                </th>
                            </tr>
                            <tr>
                                <th rowspan="2">#</th>
                                <th rowspan="2">ชื่อ</th>
                                @foreach ($paids_per_years[0]['user_payment_per_year'][0]['user_payment_per_month'] as $item)
                                    <th colspan="2">{{ $item['month'] }}</th>
                                @endforeach
                                <th rowspan="2">รวม</th>
                                <th rowspan="2">หมายเหตุ</th>
                            </tr>
                            <tr>

                                @foreach ($paids_per_years[0]['user_payment_per_year'][0]['user_payment_per_month'] as $item)
                                    <th class="bg-light">ยอดจ่าย</th>
                                    <th class="bg-light">ชำระแล้ว</th>
                                @endforeach


                            </tr>
                        </thead>
                        <tbody>

                            @foreach ($paids_per_years as $key => $member)
                                <tr>
                                    <td>{{ ++$key }}</td>
                                    <th class="text-left">{{ $member->user->firstname . ' ' . $member->user->lastname }}
                                    </th>
                                    <?php $user_sum_paid = 0;
                                    if(!isset($member['user_payment_per_year'][0]['user_payment_per_month']))
                                        dd($member['user_payment_per_year'][0]['user_payment_per_month']);
                                    ?>
                                    @foreach ($member['user_payment_per_year'][0]['user_payment_per_month'] as $item)
                                        <th class="text-end">{{ $item['rate_payment_per_month'] }}</th>

                                        <th class="text-end">
                                            @if ($item->trashbank_member == 1)
                                                <span class="text-info"> tbm</span>
                                            @else
                                                @if ($item['status'] == 'paid')
                                                    {{ $item['rate_payment_per_month'] }}
                                                    <?php $user_sum_paid += $item['rate_payment_per_month']; ?>
                                                @else
                                                    0
                                                @endif
                                            @endif
                                        </th>
                                    @endforeach
                                    <td>{{ $member['user_payment_per_year'][0]['paid_sum_payment_per_year'] }}</td>
                                    <td></td>
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
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/plug-ins/1.13.6/pagination/select.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script
    src="https://www.jqueryscript.net/demo/Export-Html-Table-To-Excel-Spreadsheet-using-jQuery-table2excel/src/jquery.table2excel.js">
</script>
<script>
    let a = true
    table = $('#invoiceTable').DataTable({
        responsive: true,

        "pagingType": "listbox",
        "lengthMenu": [
            [10, 25, 50, 150, -1],
            [10, 25, 50, 150, "ทั้งหมด"]
        ],

    }) //table

    // $('#invoiceTable_filter').remove()
    if (a) {
        //$('#invoiceTable thead tr').clone().appendTo('#invoiceTable thead');
        // $('#searchname').html('<input type="text" data-id="1" class="col-md-12 input-search-by-title" id="search_col_1" placeholder="ค้นหา" />')
        a = false
    }

    //custom การค้นหา
    // let col_index = -1
    // $('#invoiceTable thead input[type="text"]').keyup(function() {
    //     let that = $(this)
    //     var col = parseInt(that.data('id'))

    //     if (col !== col_index && col_index !== -1) {
    //         $('#search_col_' + col_index).val('')
    //         table.column(col_index)
    //             .search('')
    //             .draw();
    //     }
    //     setTimeout(function() {

    //         let _val = that.val()
    //         if (col === 0 || col === 4) {
    //             var val = $.fn.dataTable.util.escapeRegex(
    //                 _val
    //             );
    //             table.column(col)
    //                 .search(val ? '^' + val + '.*$' : '', true, false)
    //                 .draw();
    //         } else {
    //             table.column(col)
    //                 .search(_val)
    //                 .draw();
    //         }
    //     }, 300);

    //     col_index = col

    // });

    $('#excelBtn').click(function() {
        alert()
        $("#DivIdToExport").table2excel({
            // exclude CSS class
            exclude: ".noExl",
            name: "Worksheet Name",
            filename: 'aa', //do not include extension
            fileext: ".xls" // file extension
        })
    });






    $(document).on('click', '.aa', function() {
        if ($(this).hasClass('fa-arrow-alt-circle-down')) {
            $(this).removeClass('fa-arrow-alt-circle-down')
            $(this).addClass('fa-arrow-alt-circle-up')
        } else {
            $(this).removeClass('fa-arrow-alt-circle-up')
            $(this).addClass('fa-arrow-alt-circle-down')
        }
    })
</script>
@endsection
