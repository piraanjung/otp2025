@extends('layouts.admin1')

@section('nav-invoice')
    active
@endsection
@section('nav-header')
ออกใบแจ้งหนี้
@endsection
@section('nav-main')
    <a href="{{ route('user_payment_per_month.invoice') }}"> ออกใบแจ้งหนี้</a>
@endsection
@section('nav-current')
    {{-- รายชื่อผู้ใช้น้ำประปาที่ยังไม่ได้ชำระค่าเก็บขยะ --}}
@endsection
@section('page-topic')
    รายชื่อผู้ใช้น้ำประปาที่ยังไม่ได้ชำระค่าเก็บขยะ
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
    <form action="{{ route('payment.index_search_by_suzone') }}" method="POST">
        @csrf
        <div class="card" style="border: 1px solid blue">
            <div class="card-header">
                    <span class="h-5 mr-2"> ค้นหาจากหมู่บ้าน </span>
                    <a class="" data-bs-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false"
                        aria-controls="collapseExample">
                        <i class="far fa-arrow-alt-circle-down aa"></i>
                    </a>
            </div>
            <div class="card-body row collapse" id="collapseExample">
                <div class="row">
                    <div class="col-2">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="check-input-select-all"
                                id="check-input-select-all">
                            <label class="custom-control-label" for="customRadio1">เลือกทั้งหมด</label>
                        </div>
                    </div>
                    <div class="col-10 text-end">
                        <button type="submit" class="avatar avatar-lg border-1 rounded-circle mb-2"
                            style="background-color: #2077cd">
                            <i class="fas fa-search text-white" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>


                @foreach ($subzones as $key => $subzone)
                    <div class="col-lg-2 col-md-3 col-sm-3 mt-025">
                        <div class="row border border-1 rounded ">
                            <div class="col-1">
                                @if (isset($subzone_search_lists))
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="subzone_id_lists[]"
                                            value="{{ $subzone->id }}"
                                            {{ in_array($subzone->id, $subzone_search_lists) == true ? 'checked' : '' }}>
                                    </div>
                                @else
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="subzone_id_lists[]"
                                            value="{{ $subzone->id }}">
                                    </div>
                                @endif
                            </div>
                            <div class="col-10">
                                <div class="text-start text-sm" for="customCheck1">{{ $subzone->zone->zone_name }}
                                </div>
                                <div class="label text-start text-sm fw-bolder" for="customCheck1">
                                    {{ $subzone->subzone_name }}</div>
                            </div>
                        </div>

                    </div>
                @endforeach

            </div>
        </div>

    </form>

    <div class="row mt-4">
        <div class="col-12">
            <form action="{{ route('user_payment_per_month.print_notice_letters') }}" method="post">
                @csrf
                <div class="card" style="border: 1px solid blue">
                    <div class="card-header text-end">
                        <input type="submit" class="btn btn-info" value="ปริ้น">
                    </div>
                    <div class="card-body">
                            <div class="table-responsive">
                                <table class="table" id="invoiceTable">
                                    <thead>
                                        <th>#</th>
                                        <th>เลข invoice</th>
                                        <th>ชื่อ</th>
                                        <th>รหัสสมาชิก</th>
                                        <th>ประเภท</th>
                                        <th>บ้านเลขที่</th>
                                        <th>หมู่</th>
                                        <th>เส้นทางจดมิเตอร์</th>
                                        <th>ค้างชำระ(เดือน)</th>
                                        <th>หมายเหตุ</th>
                                    </thead>
                                    <tbody>

                                        @foreach ($datas_array->chunk(1000) as $chunk)

                                        @foreach($chunk as $item)
                                            <tr>
                                                <td>
                                                    <input type="checkbox" name="user_id_checked[]" class="checkbox" id="check{{ $item['user_id'] }}" value="{{ $item['user_id'] }}">
                                                </td>
                                                <td class="text-center">IV-{{ $item['id'] }}</td>
                                                <td>{{ $item['firstname'] . ' ' . $item['lastname'] }}
                                                </td>
                                                <td class="meternumber text-center" data-user_id={{ $item['user_id'] }}>
                                                    {{ $item['user_id'] }}
                                                </td>
                                                <td class="text-center">{{ $item['usergroup_name'] }}</td>
                                                <td class="text-center">{{ $item['address'] }}</td>
                                                <td class="text-center">{{ $item['zone_name'] }}</td>
                                                <td class="text-center">{{ $item['subzone_name'] }}</td>
                                                <td class="text-center">{{ $item['owe_count'] }}</td>
                                                <td>{{ $item['comment'] }}</td>
                                            </tr>
                                        @endforeach
                                        @endforeach

                                    </tbody>
                                </table>
                            </div>

                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection

@section('script')
<script src="https://cdn.datatables.net/select/1.7.0/js/dataTables.select.min.js"></script>
    <script>
        $(document).ready(function() {
            if ('<?= $page == 'index' ?>') {
                $('#check-input-select-all').prop('checked', true)
                $('.form-check-input').prop('checked', true)
            }
        })
        let a = true
        const table = $('#invoiceTable').DataTable({
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
            select: {
                style: 'multi'
            }
        }) //table
        $('#invoiceTable_filter').remove()
        if (a) {
            $('#invoiceTable thead tr').clone().appendTo('#invoiceTable thead');
            a = false
        }


 table.on('click', 'tbody tr', function (e) {
     let res = e.currentTarget.classList.toggle('selected');
     if(res === true){
        e.currentTarget.querySelector('input').checked = true
     }else{
        e.currentTarget.querySelector('input').checked = false
     }
 });


        $('#invoiceTable thead tr:eq(1) th').each(function(index) {
            var title = $(this).text();
            $(this).removeClass('sorting')
            $(this).removeClass('sorting_asc')
            if (index < 6) {
                if(index === 0){
                    $(this).html(
                        `<input type="checkbox" data-id="${index}" class="" id="checkAll"/>`
                    );
                }else{
                    $(this).html(
                        `<input type="text" data-id="${index}" class="col-md-12 input-search-by-title" id="search_col_${index}" placeholder="ค้นหา" />`
                    );
                }
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
                if (col === 3 || col === 5) {
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

        $(document).on('click', '#checkAll', function() {
            if (!$(this).is(':checked')) {
                $('.checkbox').prop('checked', false)
                $('tbody tr').removeClass('selected');
            } else {
                $('.checkbox').prop('checked', true)
                $('tbody tr').addClass('selected');
            }

        });

    </script>
@endsection
