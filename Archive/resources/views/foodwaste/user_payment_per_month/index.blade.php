@extends('layouts.adminlte')

@section('nav-payment')
    active
@endsection
@section('nav-header')
    จัดการใบเสร็จรับเงิน
@endsection
@section('nav-main')
    {{-- <a href="{{ route('invoice.index') }}"> รับชำระค่าเก็บขยะ</a> --}}
@endsection
@section('nav-current')
    {{-- รายชื่อผู้ใช้น้ำประปาที่ยังไม่ได้ชำระค่าเก็บขยะ --}}
@endsection
@section('page-topic')
    รายชื่อสมาชิกที่ยังไม่ได้ชำระค่าเก็บขยะ
@endsection
@section('style')
<link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.dataTables.min.css">
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
                    <i class="bi bi-caret-down-fill aa"></i>
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
                            <i class="bi bi-search text-white" aria-hidden="true"></i>
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
            <div class="card" style="border: 1px solid blue">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="invoiceTable">
                            <thead>
                                <th>เลข invoice</th>
                                <th>ชื่อ</th>
                                <th>รหัสสมาชิก</th>
                                <th>ประเภท</th>
                                <th>บ้านเลขที่</th>
                                <th>หมู่</th>
                                <th>จำนวนถังขยะ(ถัง)</th>
                                <th>ค้างชำระ(เดือน)</th>
                                <th>หมายเหตุ</th>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal -->
    <form action="{{ route('user_payment_per_month.store') }}" method="post" onsubmit="return check()">
        @csrf
        <div class="modal fade hidden " id="modal-success">
            <div class="modal-dialog modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="modal-title" id="exampleModalLabel">
                            <span class="font-weight-bolder h5" id="feFirstName">
                            </span>
                            <span class="text-sm" id="feInputAddress"></span>
                        </div>
                        <button type="button" class="btn-close text-dark close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">

                        <div id="activity">
                            <div class="row">
                                <div class="col-12">
                                    {{-- ข้อมูลใบแจ้งหนี้และการชำระ --}}
                                    <input type="hidden" name="mode" id="mode" value="payment">
                                    <input type="hidden" name="user_id" id="user_id" value="">
                                    <input type="hidden" name="meter_id" id="meter_id" value="">
                                    <input type="hidden" class="form-control text-bold text-center  paidform" readonly
                                        name="paidsum" id="paidsum">
                                    <input type="hidden" class="form-control text-bold text-center  paidform" readonly
                                        name="vat7" id="vat7">
                                    <input type="hidden" class="form-control text-bold text-center  paidform" readonly
                                        name="mustpaid" id="mustpaid">

                                    <div id="payment_res" class="row">
                                        <div class="col-4">
                                            <div class="card position-sticky top-1">
                                                <ul class="nav flex-column bg-warning border-radius-lg p-3" id="menu_res">
                                                </ul>
                                                <div class="mt-2 row">
                                                    <div class="col-12 col-md-6">
                                                        <div class="card">
                                                            <div class="card-body" style="padding: 1rem">
                                                                <h6 class="mb-3">สรุปยอดที่ต้องชำระ</h6>
                                                                <hr class="horizontal" style="background-color: black; margin:0.3rem 0">
                                                                <div class="d-flex justify-content-between">
                                                                    <span class="mb-2 text-lg">
                                                                        {{-- ยอดที่ต้องชำระ: --}}
                                                                    </span>
                                                                    <span class="text-dark font-weight-bold ms-2">
                                                                        <span class="mustpaid h3"></span><sup class="sup">บาท</sup>
                                                                    </span>
                                                                </div>
                                                                <hr class="horizontal" style="background-color: black; margin:0.3rem 0">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-md-6">
                                                        <button type="submit" style="
                                                            position: absolute;
                                                            right: 7rem;
                                                            top: 20rem;
                                                            z-index: 150;"
                                                            class="btn btn-success hidden  btn-lg submitbtn  mt-4  m-2">
                                                            <h6><i class="fa fa-solid fa-money text-secondary mb-1 text-white"
                                                                    aria-hidden="true"></i></h6>
                                                            <h5 class="text-white"> ชำระเงิน</h5>
                                                        </button>
                                                        <div class="card">
                                                            <div class="card-body p-3">
                                                                <div class="d-flex">
                                                                    <div class="ms-3">
                                                                        <div class="numbers">
                                                                            <p class="text-sm mb-0 text-capitalize font-weight-bold">
                                                                                รับเงินมา</p>
                                                                            <h5 class="font-weight-bolder mb-0">
                                                                                <input type="text"
                                                                                    class="form-control text-bold text-center fs-5 cash_from_user paidform"
                                                                                    name="cash_from_user">
                                                                            </h5>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="card mt-2">
                                                            <div class="card-body p-3">
                                                                <div class="d-flex ">
                                                                    <div class="ms-3">
                                                                        <div class="numbers">
                                                                            <p class="text-sm mb-0 text-capitalize font-weight-bold">
                                                                                เงินทอน</p>
                                                                            <h5 class="font-weight-bolder mb-0">
                                                                                <input type="text"
                                                                                    class="form-control text-bold fs-4 text-center border-success cashback  paidform"
                                                                                    disabled value="" name="cashback">
                                                                            </h5>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>


                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-8" id="infos_res"></div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
@section('script')
<script src="https://cdn.datatables.net/2.2.2/js/dataTables.min.js"></script>
    <script>       
        $(document).ready(function() {
            if ('<?= $page == 'index' ?>') {
                $('#check-input-select-all').prop('checked', true)
                $('.form-check-input').prop('checked', true)
            }
            // findReciept(1)
        })
        let a = true
        $(function() {
            table = $('#invoiceTable').     ({
                "pagingType": "listbox",
                ordering: false,
                searching: false,
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
                processing: true,
                serverSide: true,
                ajax: "{{ route('user_payment_per_month.index2') }}",
                orderable: false,
                columns: [{
                        data: 'id',
                        name: 'id',
                        render: function(data, row) {
                            if (data > 0 && data < 10) {
                                return `VIT000${data}`
                            } else if (data >= 10 && data < 100) {
                                return `VIT00${data}`
                            } else if (data >= 100 && data < 1000) {
                                return `VIT0${data}`
                            } else if (data >= 1000) {
                                return `VIT${data}`
                            }

                        }
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'trash_code',
                        name: 'trash_code',
                        className: 'meternumber',
                        render: function(data, row) {
                            if (data > 0 && data < 10) {
                                return `HST000${data}`
                            } else if (data >= 10 && data < 100) {
                                return `HST00${data}`
                            } else if (data >= 100 && data < 1000) {
                                return `HST0${data}`
                            } else if (data >= 1000) {
                                return `HST${data}`
                            }

                        }
                    },
                    {
                        data: 'usergroup_name',
                        name: 'usergroup_name'
                    },

                    {
                        data: 'address',
                        name: 'address',
                        className: "text-center"
                    },
                    {
                        data: 'zonename',
                        name: 'zonename',
                        className: "text-center"
                    },
                    {
                        data: 'bin_quantity',
                        name: 'bin_quantity',
                        className: "text-center"
                    },
                    {
                        data: 'owe_count',
                        name: 'owe_count',
                        className: "text-center"
                    },
                    {
                        data: 'comment',
                        name: 'comment'
                    },
                ],
                "createdRow": function(row, data, index) {
                    $('td', row).eq(2).attr('data-user_id', data.trash_code);
                },
                select: true,
            }) //table
        });
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

        $('body').on('click', '#invoiceTable tbody tr', function() {
            let meternumber = $(this).find('td.meternumber').data('user_id')
            if ($(this).hasClass('selected')) {
                $(this).removeClass('selected')
            } else {

                $.each($('#invoiceTable tbody tr'), function(key, value) {

                    $(this).removeClass('selected')
                });
                $(this).addClass('selected')
            }
            findReciept(meternumber)
        });

        function findReciept(user_id) {

            let txt = '';
            let total = 0;
            let i = 0;
            $('.cash_from_user').val(0)
            $('.cashback').val(0)
            $('#paidvalues').val(0)
            $('#mustpaid').val(0)

            $('#user_id').val(user_id)
            $.get(`/api/user_payment_per_month/get_user_payment_per_month_infos/${user_id}`).done(function(datas) {
                let i = 0;
                let menuTxt = '';
                if (Object.keys(datas).length > 0) {
                    for(let i = 0; i < Object.keys(datas).length; i++){
                        menuTxt+=`
                        <li class="nav-item pt-2">
                            <a class="nav-link text-body" data-scroll="" href="#bin${datas[i]['bin_no']}${datas[i]['user_payment_per_year']['budgetyear']['budgetyear_name']}">
                                <div class="icon me-2">
                                    <svg class="text-dark mb-1" width="16px" height="16px"
                                        viewBox="0 0 40 44" version="1.1"
                                        xmlns="http://www.w3.org/2000/svg"
                                        xmlns:xlink="http://www.w3.org/1999/xlink">
                                        <title>switches</title>
                                        <g stroke="none" stroke-width="1" fill="none"
                                            fill-rule="evenodd">
                                            <g transform="translate(-1870.000000, -440.000000)"
                                                fill="#FFFFFF" fill-rule="nonzero">
                                                <g transform="translate(1716.000000, 291.000000)">
                                                    <g transform="translate(154.000000, 149.000000)">
                                                        <path class="color-background"
                                                            d="M10,20 L30,20 C35.4545455,20 40,15.4545455 40,10 C40,4.54545455 35.4545455,0 30,0 L10,0 C4.54545455,0 0,4.54545455 0,10 C0,15.4545455 4.54545455,20 10,20 Z M10,3.63636364 C13.4545455,3.63636364 16.3636364,6.54545455 16.3636364,10 C16.3636364,13.4545455 13.4545455,16.3636364 10,16.3636364 C6.54545455,16.3636364 3.63636364,13.4545455 3.63636364,10 C3.63636364,6.54545455 6.54545455,3.63636364 10,3.63636364 Z"
                                                            opacity="0.6"></path>
                                                        <path class="color-background"
                                                            d="M30,23.6363636 L10,23.6363636 C4.54545455,23.6363636 0,28.1818182 0,33.6363636 C0,39.0909091 4.54545455,43.6363636 10,43.6363636 L30,43.6363636 C35.4545455,43.6363636 40,39.0909091 40,33.6363636 C40,28.1818182 35.4545455,23.6363636 30,23.6363636 Z M30,40 C26.5454545,40 23.6363636,37.0909091 23.6363636,33.6363636 C23.6363636,30.1818182 26.5454545,27.2727273 30,27.2727273 C33.4545455,27.2727273 36.3636364,30.1818182 36.3636364,33.6363636 C36.3636364,37.0909091 33.4545455,40 30,40 Z">
                                                        </path>
                                                    </g>
                                                </g>
                                            </g>
                                        </g>
                                    </svg>
                                </div>
                                <span class="h5">ถังที่ ${datas[i]['bin_no']} ปี${datas[i]['user_payment_per_year']['budgetyear']['budgetyear_name']}</span>
                                <span class="">[ค้างชำระ ถัง]</span>
                            </a>
                        </li>`;
                    }

                    $('#menu_res').html(menuTxt);


                    datas.forEach(element =>{
                    total +=  element.rate_payment_per_month * element.init_status_count;
                    txt +=
                    `<div class="card card-success border border-success rounded mt-2" id="bin${element['bin_no']}${element['user_payment_per_year']['budgetyear']['budgetyear_name']}">
                        <div class="card-header p-1">
                            <h6 class="card-title bg-gray-100 " >
                                <div class="row">
                                    <div class="col-4 col-md-4">
                                        ปีงบประมาณ ${element['user_payment_per_year']['budgetyear']['budgetyear_name']}
                                        ถังที่ ${element['bin_no']}
                                    </div>
                                    <div class="col-4 col-md-4">
                                        เลขใบแจ้งหนี้ ${element.id}
                                    </div>
                                    <div class="col-4 col-md-4">
                                        รายการค้างชำระ [ ${element.init_status_count} <sup class="sup">รอบบิล</sup> ]
                                    </div>
                                </div>
                            </h6>
                        </div>
                        <div class="card-body p-0 " style="display: block;overflow-y: scroll;">
                            <table class="table" id="table${element['bin_no']}${element['user_payment_per_year']['budgetyear']['budgetyear_name']}">
                                <thead>
                                    <tr>
                                        <th style="width: 10px">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="checkAll" data-id="${element['bin_no']}${element['user_payment_per_year']['budgetyear']['budgetyear_name']}" checked>
                                            </div>
                                        </th>
                                        <th class="text-center">เดือน</th>
                                        <th class="text-end">เป็นเงิน<div class="fs-7 sup">(บาท)</div></th>
                                        <th class="text-center">สถานะ</th>
                                    </tr>
                                </thead>
                                <tbody>`;
                                    JSON.parse(element.json).forEach(ele => {
                                        txt +=`
                                        <tr class="${ele.status ==="past" ? 'bg-info disabled' : ''}">
                                            <th style="width: 10px">
                                                <div class="form-check form-check-checkbox">`;
                                                    if(ele.status ==="init"){
                                                        txt +=`

                                                                <input type="hidden"  name="payments[${i}][id]" value="${element.id}" >
                                                                <input type="hidden"  name="payments[${i}][user_payment_per_year_id_fk]" value="${element.user_payment_per_year_id_fk}" >
                                                                <input type="hidden"  name="payments[${i}][rate_payment_per_month]" value="${element.rate_payment_per_month}">
                                                                <input type="hidden"  name="payments[${i}][month]" value="${ele.month}">
                                                            <div class="clone">
                                                            <input class="form-check-input checkbox" type="checkbox" checked name="payments[${i}][on]"> ${++i}</div>`;

                                                    }
                                                    txt +=`
                                                </div>
                                            </th>
                                            <th class="text-center">${ele.month}</th>
                                            <th class="text-end rate_payment_per_month">${ele.status === "past" ? 0 : element.rate_payment_per_month}</th>
                                            <th class="text-center">`;
                                                if(ele.status === "paid"){
                                                    txt += "ชำระแล้ว";
                                                }else{
                                                    txt +=`
                                                    <select class="form-controle json_status" data-i="${i}" data-id="${element.id}${ele.month}" data-payment_per_month="${element.rate_payment_per_month}">
                                                        <option value="init" selected>ออกใบแจ้งนี้</option>
                                                        <option value="past">เดือนก่อนเริ่มใช้งาน</option>
                                                    </select>
                                                    `;
                                                }
                                                txt +=`
                                            </th>
                                        </tr>
                                        `;
                                    });
                                    txt +=`
                                </tbody>
                            </table>
                        </div>
                    </div>`;


                }); //datas.forEach.element
                $('#infos_res').html(txt);

                    let address = `${datas[0].usermeterinfos.user.address}
                             ${datas[0].usermeterinfos.user.user_zone.user_zone_name} \n
                            ตำบล ${datas[0].usermeterinfos.user.user_tambon.tambon_name}\n
                            อำเภอ ${datas[0].usermeterinfos.user.user_district.district_name}\n
                            จังหวัด ${datas[0].usermeterinfos.user.user_province.province_name}`;

                    $('#feFirstName').html(datas[0].usermeterinfos.user.firstname + " " + datas[0].usermeterinfos
                        .user.lastname);
                    $('#meternumber2').html(datas[0].usermeterinfos.meternumber);
                    $('#feInputAddress').html(address);
                    $('#phone').html(datas[0].usermeterinfos.user.phone);

                    // $('#payment_res').html(txt);

                    $('.modal').modal('show')

                    $('#mustpaid').val(total)
                    $('.mustpaid').html(total)
                    $('#meter_id').val(datas[0].usermeterinfos.user_id);
                } else {
                    $('#empty-invoice').removeClass('hidden')
                }


            });//if
        } //.get

        $(document).on('change', '.json_status', function(e){
            e.preventDefault();
            let val = $(this).val()
            let id  = $(this).data('id');
            let i   = $(this).data('i')
            let index = i - 1;
            console.log('id', id)
            let form_check_checkbox    = $(this).parent().parent().find('.form-check-checkbox')
            let rate_payment_per_month = $(this).data('payment_per_month')
            if(val === "init"){
                $(this).parent().parent().find('.form-check-checkbox .clone').html(`<input class="form-check-input checkbox" type="checkbox" checked name="payments[${index}][on]"> ${i}`)
                $(this).parent().parent().removeClass('bg-info disabled')
                $(this).parent().siblings('.rate_payment_per_month').text(rate_payment_per_month)

            }else{
                $(this).parent().siblings('.rate_payment_per_month').text(0)
                $(this).parent().parent().addClass('bg-info disabled')
                $(this).parent().parent().find('.form-check-checkbox .clone').html(`<input class="form-check-input checkbox opacity-0" type="checkbox"  checked name="payments[${index}][past]">`)


            }
            checkboxclicked()
        })

        $('.cash_from_user').keyup(function() {
            let mustpaid = $('#mustpaid').val()
            let cash_from_user = $(this).val()
            let cashback = cash_from_user - mustpaid

            $('.cashback').val(cashback.toFixed(2))
            if (cash_from_user === "") {
                $('.cashback').val("")
            }
            if (cashback >= 0) {
                if(mustpaid == 0) {
                    $('.submitbtn').hasClass('hidden') ? '' : $('.submitbtn').addClass('hidden')
                }else{
                    $('.submitbtn').removeClass('hidden')
                }
            } else {
                $('.submitbtn').addClass('hidden');

            }

        }); //$('.cash_from_user')


        $(document).on('click', '.checkbox', function() {
            checkboxclicked()
        }); //$(document).on('click','.checkbox',

        function checkboxclicked() {
            let _total = 0;
            $('.checkbox').each(function(index, element) {
                console.log('sum', $(this).parent().parent().parent().siblings('.rate_payment_per_month').text())
                if ($(this).is(":checked")) {
                    let sum = $(this).parent().parent().parent().siblings('.rate_payment_per_month').text();
                    _total += parseFloat(sum, 2);
                }
            });

            if (_total == 0) {
                $('.cash_form_user').attr('readonly')
                $('.submitbtn').removeAttr('style')

            } else if (_total > 0) {
                $('.cash_form_user').removeAttr('readonly')
            }

            if ($('.cash_from_user').val() > 0) {
                let remain = $('.cash_from_user').val() - (_total)
                remain >= 0 ? $('.submitbtn').removeClass('hidden') : $('.submitbtn').addClass('hidden')
                $('.cashback').val(remain)
            }

            $('.paidsum').html(_total)
            $('#paidsum').val(_total)
            $('#mustpaid').val(_total)
            $('.mustpaid').text(_total)
        }

        function check() {
            $('.submitbtn').prop('disabled', true);
            let checkboxChecked = false;
            let cashbackRes = false;
            let errText = '';
            $('.checkbox:checked').each(function(index, element) {
                checkboxChecked = true;
            });
            if (checkboxChecked == false) {
                errText += '- ยังไม่ได้เลือกรายการค้างชำระ\n';
            }
            let mustpaid = $('.mustpaid').val()
            let cash_from_user = $('.cash_from_user').val()
            let cashback = cash_from_user - mustpaid
            if (!Number.isNaN(cashback) && cashback >= 0) {
                cashbackRes = true;
            } else {
                errText += '- ใส่จำนวนเงินไม่ถูกต้อง'
            }
            if (checkboxChecked == false || cashbackRes == false) {
                $('.submitbtn').prop('disabled', false);
                alert(errText)
                return false;
            } else {
                return true;

            }
        }
        $(document).on('click', '#checkAll', function() {
            let id      = $(this).data('id');
            let table   = `table${id}`;
            if (!$(this).is(':checked')) {
                $(`#${table} .checkbox`).prop('checked', false)
            } else {
                $(`#${table} .checkbox`).prop('checked', true)
            }
            checkboxclicked()
        });
        //subzone
        $('#check-input-select-all').on('click', function() {
            console.log('ss')
            if (!$(this).is(':checked')) {
                $('.form-check-input').prop('checked', false)
            } else {
                $('.form-check-input').prop('checked', true)
            }
        });

        $('.close').click(() => {
            $('.modal').modal('close')
        })

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
