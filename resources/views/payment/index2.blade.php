@extends('layouts.admin1')

@section('nav-payment')
    active
@endsection

@section('nav-header')
    จัดการใบเสร็จรับเงิน
@endsection
@section('nav-main')
    <a href="{{ route('invoice.index') }}">รับชำระค่าน้ำประปา</a>
@endsection

@section('page-topic')
    รายชื่อผู้ใช้น้ำประปาที่ยังไม่ได้ชำระค่าน้ำประปา
    @foreach ($selected_subzone_name_array as $item)
        {{ $item }}
    @endforeach
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
            width: 90rem;
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

        .cashback {
            color: white
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
                max-width: 90rem;
                margin-right: auto;
                margin-left: auto
            }
        }
    </style>
@endsection
@section('content')
    <div class="row">
        <div class="col-3">
            <form action="{{ route('payment.index') }}" method="get">
                @csrf
                <div class="card">
                    <div class="card-body row">
                        <h6>ค้นหาจากเส้นทางจดมิเตอร์</h6>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="check-input-select-all"
                                id="check-input-select-all" {{ $select_all == true ? 'checked' : '' }}>
                            <label class="custom-control-label" for="customRadio1">เลือกทั้งหมด</label>
                        </div>

                        @foreach ($subzones as $key => $subzone)
                            <div class="col-lg-6 col-md-3 col-sm-3 mt-025">
                                <div class="row">
                                    <div class="col-1">
                                        @if (isset($subzone_selected))
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="subzone_id_lists[]"
                                                    value="{{ $subzone->id }}"
                                                    {{ in_array($subzone->id, $subzone_selected) == true ? 'checked' : '' }}>
                                            </div>
                                        @else
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="subzone_id_lists[]"
                                                    value="{{ $subzone->id }}">
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-9">
                                        <div class="text-start text-primary text-bold" for="customCheck1">
                                            {{ $subzone->zone->zone_name }}
                                        </div>

                                    </div>
                                </div>

                            </div>
                        @endforeach
                        <a href="javascript:;" class="w-auto mt-3">
                            <button type="submit" class="avatar avatar-md border-1 rounded-circle">
                                <i class="fas fa-search text-secondary" aria-hidden="true"></i>
                            </button>
                            ค้นหา
                        </a>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-9">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table" id="invoiceTable">
                                <thead>
                                    <th>เลขใบแจ้งหนี้</th>
                                    <th>ชื่อ</th>
                                    <th>เลขมิเตอร์</th>
                                    <th>บ้านเลขที่</th>
                                    <th>หมู่</th>
                                    <th>เส้นทางจดมิเตอร์</th>
                                    {{-- <th>ค้างชำระ(เดือน)</th> --}}
                                    {{-- <th>หมายเหตุ</th> --}}
                                </thead>
                                <tbody>

                                    @foreach ($invoices as $invoice)
                                        <tr>
                                            <td class="text-end">{{ "Iv".$invoice->meter_id."".$invoice->invoice[0]->inv_no }}</td>
                                            <td>{{ $invoice->user->prefix . '' . $invoice->user->firstname . ' ' . $invoice->user->lastname }}
                                            </td>
                                            <td class="meternumber text-center" data-meter_id={{ $invoice->meter_id }}>
                                                {{ $invoice->meternumber }}
                                            </td>
                                            <td class="text-center">{{ $invoice->user->address }}</td>
                                            <td class="text-center">
                                                {{ $invoice->undertake_zone->zone_name }}
                                            </td>
                                            <td class="text-center">
                                                @if (!isset($invoice->undertake_subzone->subzone_name))
                                                    {{ dd($invoice) }}
                                                @endif
                                                {{ $invoice->undertake_subzone->subzone_name }}
                                            </td>
                                        </tr>
                                    @endforeach


                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div><!--row-->


    <!-- Modal -->
    <form action="{{ route('payment.store') }}" method="post" onsubmit="return check()">
        @csrf
        <div class="modal fade" id="modal-success">
            <div class="modal-dialog modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="modal-title" id="exampleModalLabel">
                            <h5 class="font-weight-bolder" id="feFirstName">
                            </h5>
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
                                    <input type="hidden" name="inv_no" id="inv_no">
                                    <input type="hidden" name="user_id" id="user_id" value="">
                                    <input type="hidden" name="meter_id" id="meter_id" value="">
                                    <input type="hidden" class="form-control text-bold text-center  paidform" readonly
                                        name="paidsum" id="paidsum">
                                    <input type="hidden" class="form-control text-bold text-center  paidform" readonly
                                        name="vat7" id="vat7">
                                    <input type="hidden" class="form-control text-bold text-center  paidform" readonly
                                        name="mustpaid" id="mustpaid">

                                    <div id="payment_res"> </div>
                                </div>
                            </div>
                            <div class="row mt-4">
                                <div class="col-12 col-md-3 text-center">
                                    <div class="card">
                                        <div class="card-body" style="color:black; ">
                                            <h6>สแกน QR CODE ชำระเงินค่าน้ำประปา</h6>
                                            <div id="qrcode"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-9 row">
                                    <div class="col-12 col-md-5 offset-md-1">
                                        <div class="card">
                                            <div class="card-body">
                                                <h6 class="mb-3">สรุปยอดที่ต้องชำระ</h6>
                                                <div class="d-flex justify-content-between">
                                                    <span class="mb-2 text-sm">
                                                        รวมค่าใช้น้ำ
                                                    </span>
                                                    <span class="text-dark font-weight-bold ms-2">
                                                        <span class="paidsum"></span><sup class="sup">บาท</sup>
                                                    </span>
                                                </div>
                                                <div class="d-flex justify-content-between">
                                                    <span class="mb-2 text-sm">
                                                        Vat 7 %:
                                                    </span>
                                                    <span class="text-dark font-weight-bold ms-2">
                                                        <span class="vat7"></span><sup class="sup">บาท</sup>
                                                    </span>
                                                </div>
                                                <hr class="horizontal" style="background-color: black; margin:0.3rem 0">
                                                <div class="d-flex justify-content-between">
                                                    <span class="mb-2 text-lg">
                                                        ยอดที่ต้องชำระ:
                                                    </span>
                                                    <span class="text-dark font-weight-bold ms-2">
                                                        <span class="mustpaid"></span><sup class="sup">บาท</sup>
                                                    </span>
                                                </div>
                                                <hr class="horizontal" style="background-color: black; margin:0.3rem 0">
                                                <textarea id="qrcode_text" style="opacity: 0" cols="1" rows="1"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="card col-md-8 col-8">
                                            <div class="card-body p-3">
                                                <div class="d-flex">
                                                    <div>
                                                        <div
                                                            class="icon icon-shape bg-gradient-dark text-center border-radius-md">
                                                            <i class="ni ni-money-coins text-lg opacity-10"
                                                                aria-hidden="true"></i>
                                                        </div>
                                                    </div>
                                                    <div class="ms-3">
                                                        <div class="numbers">
                                                            <p class="text-sm mb-0 text-capitalize font-weight-bold">
                                                                รับเงินมา</p>
                                                            <h5 class="font-weight-bolder mb-0">
                                                                <input type="text"
                                                                    class="form-control text-bold text-center fs-5 cash_from_user paidform"
                                                                    name="cash_from_user" value="">
                                                            </h5>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex flex-wrap">
                                            <div class="card mt-3 col-12 col-md-8">
                                                <div class="card-body p-3">
                                                    <div class="d-flex ">
                                                        <div
                                                            class="icon icon-shape bg-gradient-dark text-center border-radius-md">
                                                            <i class="ni ni-money-coins text-lg opacity-10"
                                                                aria-hidden="true"></i>
                                                        </div>
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
                                            <span>&nbsp;&nbsp;&nbsp;&nbsp;</span>
                                            <button type="submit" style="height:120px; width:150px"
                                                class="btn btn-success  btn-block submitbtn hidden mt-4  m-2">
                                                <h6><i class="fa fa-solid fa-money text-secondary mb-1 text-white"
                                                        aria-hidden="true"></i></h6>
                                                <h5 class="text-white"> ชำระเงิน</h5>
                                            </button>
                                        </div><!--d-flex-->
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
    <script src="https://cdn.jsdelivr.net/npm/jquery.qrcode@1.0.3/jquery.qrcode.min.js"></script>
    <script>
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
                    // "info": "แสดง _MENU_ แถว",
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
            if (index < 4) {
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
                if (col === 0 || col === 3) {
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
            let meternumber = $(this).find('td.meternumber').data('meter_id')
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

        function findReciept(meter_id) {
            let txt = '';
            let totalpaidsum = parseFloat(0);
            let paidsum = parseFloat(0);
            let vatsum = parseFloat(0);
            let vat = 0.07;
            let i = 0;

            $('.cashback').val(0)
            $('#paidvalues').val(0)
            $('#vat7').val(0)
            $('#mustpaid').val(0)
            $('#user_id').val(meter_id);





            $.get(`/api/invoice/get_user_invoice/${meter_id}/inv_and_owe`).done(function(invoices) {

                let i = 0;
                if (Object.keys(invoices).length > 0) {
                    console.log(invoices)
                    txt += `<div class="card card-success border border-success rounded">
                                <div class="card-header p-1">
                                    <h6 class="card-title bg-gray-100">รายการค้างชำระ [ ${Object.keys(invoices).length} <sup class="sup">รอบบิล</sup> ] <span style="padding-left:30px">เลขใบเสร็จรับเงิน  ${invoices[0].acc_trans_id_fk}</span> </h6>
                                </div>
                                <div class="card-body p-0 " style="display: block;height:250px; overflow-y: scroll;">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                            <th style="width: 10px">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="checkAll" checked>
                                                </div>
                                            </th>
                                            <th class="text-center">เลขใบแจ้งหนี้</th>
                                            <th class="text-center">ลำดับที่</th>
                                            <th class="text-center">เลขมิเตอร์</th>
                                            <th class="text-center">รอบบิล</th>
                                            <th class="text-end">ยอดครั้งก่อน<div class="fs-7 sup">(หน่วย)</div></th>
                                            <th class="text-end">ยอดปัจจุบัน<div class="fs-7 sup">(หน่วย)</div></th>
                                            <th class="text-end">จำนวนที่ใช้<div class="fs-7 sup">(หน่วย)</div></th>
                                            <th class="text-end">ค่าใช้น้ำ<div class="fs-7 sup">(บาท)</div></th>
                                            <th class="text-end">ค่ารักษามิเตอร์<div class="fs-7 sup">(หน่วย)</div></th>
                                            <th class="text-end">Vat 7%<div class="fs-7 sup">(บาท)</div></th>
                                            <th class="text-end">เป็นเงิน<div class="fs-7 sup">(บาท)</div></th>
                                            <th class="text-center">สถานะ</th>
                                            </tr>
                                        </thead>
                                        <tbody>`;
                    invoices.forEach(element => {
                        totalpaidsum += parseFloat(element.totalpaid)
                        vatsum += parseFloat(element.vat);
                        paidsum += parseFloat(element.paid)

                        let _vat = parseFloat(element.paid) == 0 ? parseFloat(vat) : parseFloat(vat) *
                            parseFloat(element.paid)
                        let totalpaid = parseFloat(_vat) + parseFloat(element.paid)

                        let status = element.status == 'owe' ? 'ค้างชำระ' : 'ออกใบแจ้งหนี้';
                        txt += ` <tr>
                                <td class="text-center">
                                    <div class="form-check">
                                        <input type="checkbox"  checked  class="form-check-input invoice_id checkbox"
                                            data-inv_id="${element.inv_id}" name="payments[${i}][on]">
                                    </div>
                                </td>
                                <td class="text-center">${"Iv"+element.meter_id_fk+""+element.inv_no}</td>
                                <td class="text-center">${element.inv_id}</td>
                                <td class="text-center">${element.usermeterinfos.meternumber}</td>
                                <td class="text-center">${element.invoice_period.inv_p_name}</td>
                                <td class="text-end">   ${element.lastmeter}</td>
                                <td class="text-end">   ${element.currentmeter}</td>
                                <td class="text-end">   ${element.water_used}</td>
                                <td class="text-end" id="paid${element.inv_id}" data-paid="${element.inv_id}">   ${ element.paid }
                                    <input type="hidden" name="payments[${i}][total]" value="${ element.paid }">
                                    <input type="hidden" name="payments[${i}][iv_id]" value="${ element.inv_id }">
                                    <input type="hidden" name="payments[${i}][status]" value="${ element.status }">
                                </td>
                                <td class="text-end">${ element.inv_type === 'r' ? element.paid : 0}</td>
                                <td class="text-end" id="vat${element.inv_id}" data-vat="${element.inv_id}">${parseFloat(_vat).toFixed(2)}</td>
                                <td class="total text-end" id="total${element.inv_id}" data-total="${element.inv_id}">${parseFloat(totalpaid).toFixed(2)}</td>
                                <td class="text-center">${status}</td>

                            </tr>
                        `;
                        i++;
                    }) //foreach

                    txt += `             </tbody>
                                    </table>
                                </div>
                            </div>`;


                    let address = `${invoices[0].usermeterinfos.user.address}
                                 ${invoices[0].usermeterinfos.user.user_zone.user_zone_name} \n
                                ตำบล ${invoices[0].usermeterinfos.user.user_tambon.tambon_name}\n
                                อำเภอ ${invoices[0].usermeterinfos.user.user_district.district_name}\n
                                จังหวัด ${invoices[0].usermeterinfos.user.user_province.province_name}`;

                    $('#feFirstName').html(invoices[0].usermeterinfos.user.prefix + "" + invoices[0].usermeterinfos
                        .user.firstname + " " + invoices[0]
                        .usermeterinfos.user.lastname);
                    $('#meternumber2').html(invoices[0].usermeterinfos.meternumber);
                    $('#feInputAddress').html(address);
                    $('#phone').html(invoices[0].usermeterinfos.user.phone);

                    $('#payment_res').html(txt);
                    $('.modal').modal('show')

                    $('#paidsum').val(parseFloat(paidsum).toFixed(2))
                    $('.paidsum').html(parseFloat(paidsum).toFixed(2))
                    $('#vat7').val(parseFloat(vatsum).toFixed(2))
                    $('.vat7').html(parseFloat(vatsum).toFixed(2))
                    $('#mustpaid').val(parseFloat(totalpaidsum).toFixed(2))
                    $('.mustpaid').html(parseFloat(totalpaidsum).toFixed(2))
                    $('#meter_id').val(invoices[0].usermeterinfos.meter_id);
                    $('#inv_no').val(invoices[0].usermeterinfos.meter_id+""+invoices[0].inv_no)
                    let init = "000000000000000000"
                    let meter_id_length = invoices[0].usermeterinfos.meter_id.toString().length
                    let meter_id_str = init.substring(meter_id_length) + "" + invoices[0].usermeterinfos.meter_id
                        .toString()
                    let inv_no_length = $('#inv_no').val().toString().length
                    let inv_no_str = init.substring(inv_no_length) + "" + $('#inv_no').val()
                        .toString()
                    let paidVal = parseFloat(totalpaidsum).toFixed(2).toString().replace(".", "")
                    let res = `|099400035262000\n${meter_id_str}\n${inv_no_str}\n${paidVal}`
                    $('#qrcode_text').val(res)
                    $('#qrcode').html("")
                    $('#qrcode').append(invoices[0].usermeterinfos.user.prefix + "" + invoices[0].usermeterinfos
                        .user.firstname + " " + invoices[0].usermeterinfos.user.lastname + "\n");
                    $('#qrcode').append(
                        `<div font-size:1.1rem">จำนวนที่ต้องชำระ ${parseFloat(totalpaidsum).toFixed(2)} บาท</div>`
                        );
                    $('#qrcode').append().qrcode({
                        text: $('#qrcode_text').val(),
                        width: 135,
                        height: 135
                    });

                } else {
                    $('#empty-invoice').removeClass('hidden')
                }


            });
        } //text


        $('.cash_from_user').keyup(function() {
            let mustpaid = $('#mustpaid').val()
            let cash_from_user = $(this).val()
            let cashback = cash_from_user - mustpaid

            $('.cashback').val(cashback.toFixed(2))
            if (cash_from_user === "") {
                $('.cashback').val("")
            }
            if (cashback >= 0) {
                mustpaid == 0 ? $('.submitbtn').attr('style', 'display:none') : $('.submitbtn').attr('style',
                    'display:block')
            } else {
                $('.submitbtn').attr('style', 'display:none')
            }

        }); //$('.cash_from_user')


        $(document).on('click', '.checkbox', function() {
            checkboxclicked()
        }); //$(document).on('click','.checkbox',

        function checkboxclicked() {
            let totalsum = 0;
            let vatsum = 0;
            let paidsum = 0;
            $('.checkbox').each(function(index, element) {
                if ($(this).is(":checked")) {
                    let id = $(this).data('inv_id')
                    totalsum = parseFloat(totalsum) + parseFloat($(`#total${id}`).text())
                    paidsum = parseFloat(paidsum) + parseFloat($(`#paid${id}`).text())
                    vatsum = parseFloat(vatsum) + parseFloat($(`#vat${id}`).text())

                } else {
                    $('#check-input-select-all').prop('checked', false)
                }
            });
            console.log('totalsum', totalsum)
            if (totalsum == 0) {
                $('.cash_form_user').attr('readonly')
                $('.submitbtn').addClass('hidden')
                $('.submitbtn').removeAttr('style')

            } else if (totalsum > 0) {
                $('.cash_form_user').removeAttr('readonly')
                $('.submitbtn').removeClass('hidden')
            }

            let cash_from_user = parseFloat($('.cash_from_user').val()).toFixed(2)

            if (cash_from_user > 0) {
                let remain = cash_from_user - totalsum
                $('.cashback').val(remain.toFixed(2))
            }


            $('.vat7').html(vatsum)
            $('#vat7').val(vatsum)
            $('.paidsum').html(paidsum)
            $('#paidsum').val(paidsum)
            $('#mustpaid').val(totalsum)
            $('.mustpaid').text(totalsum)
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

        $('#check-input-select-all').on('click', function() {
            if (!$(this).is(':checked')) {
                $('.form-check-input').prop('checked', false)
            } else {
                $('.form-check-input').prop('checked', true)
            }
        });

        $('.close').click(() => {
            $('.modal').modal('close')
        })
    </script>
@endsection
