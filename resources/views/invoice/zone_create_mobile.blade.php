@section('nav-invoice')
    active
@endsection
@section('nav-header')
    จัดการใบแจ้งหนี้
@endsection
@section('nav-main')
    <a href="{{ route('invoice.index') }}"> ออกใบแจ้งหนี้</a>
@endsection
@section('nav-current')
    บันทึกข้อมูลใบแจ้งหนี้
@endsection
@section('page-topic')
    {{ $subzone->zone->zone_name }}
    <div class="text-sm">เส้นทาง::
        {{ $subzone->subzone_name }}</div>
@endsection

<style>
    li {
        display: block
    }

    ul li input[type=text] {
        display: block
    }

    .card.card-pricing .card-body {
        padding: 1rem;
    }

    .card .card-body {
        font-family: Open Sans;
        padding: 0.5rem;
    }

    ol,
    ul {
        padding-left: 0.2rem;
    }

    td {
        background-color: white
    }

    .form-control {
        display: block;
        width: 16rem;
        padding: .5rem .75rem;
        font-size: .875rem;
        font-weight: 400;
        line-height: 1.4rem;
        color: #495057;
        background-color: #fff;
        background-clip: padding-box;
        border: 1px solid #d2d6da;
        appearance: none;
        transition: box-shadow .15s ease, border-color .15s ease;
    }

    .table-striped>tbody>tr:nth-of-type(odd)>* {
        --bs-table-accent-bg: white;
        color: rgb(0, 0, 0);
    }

    .table td,
    .table th {
        white-space: normal;
    }

    .table>:not(caption)>*>* {
        padding: .1rem;
        background-color: var(--bs-table-bg);
        border-bottom-width: 1px;
        box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg);
    }
</style>

<div class="card" id="m">
    <div class="card-header">
        <div class="card-title">
            <div class="row">
                <div class="ps-0">
                    <div class="d-flex">
                        <div
                            class="icon icon-shape icon-xxs shadow border-radius-sm bg-gradient-primary text-center me-2 d-flex align-items-center justify-content-center">
                            <svg width="10px" height="10px" viewBox="0 0 40 44" version="1.1"
                                xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                                <title>document</title>
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <g transform="translate(-1870.000000, -591.000000)" fill="#FFFFFF"
                                        fill-rule="nonzero">
                                        <g transform="translate(1716.000000, 291.000000)">
                                            <g transform="translate(154.000000, 300.000000)">
                                                <path class="color-background"
                                                    d="M40,40 L36.3636364,40 L36.3636364,3.63636364 L5.45454545,3.63636364 L5.45454545,0 L38.1818182,0 C39.1854545,0 40,0.814545455 40,1.81818182 L40,40 Z"
                                                    opacity="0.603585379"></path>
                                                <path class="color-background"
                                                    d="M30.9090909,7.27272727 L1.81818182,7.27272727 C0.814545455,7.27272727 0,8.08727273 0,9.09090909 L0,41.8181818 C0,42.8218182 0.814545455,43.6363636 1.81818182,43.6363636 L30.9090909,43.6363636 C31.9127273,43.6363636 32.7272727,42.8218182 32.7272727,41.8181818 L32.7272727,9.09090909 C32.7272727,8.08727273 31.9127273,7.27272727 30.9090909,7.27272727 Z M18.1818182,34.5454545 L7.27272727,34.5454545 L7.27272727,30.9090909 L18.1818182,30.9090909 L18.1818182,34.5454545 Z M25.4545455,27.2727273 L7.27272727,27.2727273 L7.27272727,23.6363636 L25.4545455,23.6363636 L25.4545455,27.2727273 Z M25.4545455,20 L7.27272727,20 L7.27272727,16.3636364 L25.4545455,16.3636364 L25.4545455,20 Z">
                                                </path>
                                            </g>
                                        </g>
                                    </g>
                                </g>
                            </svg>
                        </div>
                        <p class="mt-1 mb-0 font-weight-bold">ยังไม่บันทึกข้อมูล</p>
                    </div>

                    <h4 class="font-weight-bolder">{{ $invoice_remain }} คน</h4>
                    <div class="progress w-{{ floor($invoice_remain / 10) * 10 }}">

                        <div class="progress-bar bg-drk w-{{ floor($invoice_remain / 10) * 10 }}" role="progressbar"
                            aria-valuenow="{{ floor($invoice_remain / 10) * 10 }}" aria-valuemin="0"
                            aria-valuemax="100">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">

        <form action="{{ route('invoice.store') }}" method="POST">
            @csrf
            <table class="table  table-striped datatable2" id="DivIdToPrint2">
                <thead class="bg-light">
                    <tr>
                        <th class="text-center bg-white"></th>
                    </tr>
                </thead>

                <tbody id="app">
                    <?php $i = 1; ?>
                    @foreach ($invoices as $invoice)
                    <tr>
                        <td>{{ {{ $invoice->usermeterinfos->user->prefix . '' . $invoice->usermeterinfos->user->firstname . ' ' . $invoice->usermeterinfos->user->lastname }} }}</td>
                    </tr>
                        {{-- <tr data-id="{{ $i }}" class="data">
                            <td>
                                <div class="card card-pricing">
                                    <div class="card-header bg-gradient-dark text-center pt-4 pb-5 position-relative">
                                        <div class="z-index-1 position-relative">
                                            <h5 class="text-white"> {{ $invoice->usermeterinfos->meternumber }}</h5>
                                            <h1 class="text-white mt-2 mb-0">
                                                <small>{{ $invoice->usermeterinfos->user->prefix . '' . $invoice->usermeterinfos->user->firstname . ' ' . $invoice->usermeterinfos->user->lastname }}</span></small>
                                            </h1>
                                            <h6 class="text-white">บ้านเลขที่
                                                {{ $invoice->usermeterinfos->user->address }} หมู่
                                                {{ $invoice->usermeterinfos->user->zone_id }}</h6>
                                        </div>
                                    </div>
                                     <div class="position-relative mt-n5" style="height: 50px;">
                                        <div class="position-absolute w-100">
                                            <svg class="waves waves-sm" xmlns="http://www.w3.org/2000/svg"
                                                xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 24 150 40"
                                                preserveAspectRatio="none" shape-rendering="auto">
                                                <defs>
                                                    <path id="card-wave"
                                                        d="M-160 44c30 0 58-18 88-18s 58 18 88 18 58-18 88-18 58 18 88 18 v44h-352z">
                                                    </path>
                                                </defs>
                                                <g class="moving-waves">
                                                    <use xlink:href="#card-wave" x="48" y="-1"
                                                        fill="rgba(255,255,255,0.30"></use>
                                                    <use xlink:href="#card-wave" x="48" y="3"
                                                        fill="rgba(255,255,255,0.35)"></use>
                                                    <use xlink:href="#card-wave" x="48" y="5"
                                                        fill="rgba(255,255,255,0.25)"></use>
                                                    <use xlink:href="#card-wave" x="48" y="8"
                                                        fill="rgba(255,255,255,0.20)"></use>
                                                    <use xlink:href="#card-wave" x="48" y="13"
                                                        fill="rgba(255,255,255,0.15)"></use>
                                                    <use xlink:href="#card-wave" x="48" y="16"
                                                        fill="rgba(255,255,255,0.99)"></use>
                                                </g>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="card-body ">
                                        <ul class="">
                                            <li class="mb-2">
                                                <div class="row">
                                                    <span class="col-7">ยกยอดมา</span>
                                                    <input type="text" value="{{ $invoice->lastmeter }}"
                                                        name="data[{{ $i }}][lastmeter]"
                                                        data-id="{{ $i }}"
                                                        id="lastmeter2{{ $i }}"
                                                        data-price_per_unit="{{ $invoice->usermeterinfos->meter_type->price_per_unit }}"
                                                        class="form-control text-right lastmeter w-40 col-5" readonly>
                                                </div>

                                            </li>
                                            <li class="mb-2">
                                                <div class="row">
                                                    <span class="col-7">มิเตอร์ปัจจุบัน</span>
                                                    <input
                                                        type="text" value=""
                                                        name="data[{{ $i }}][currentmeter]"
                                                        data-id="{{ $i }}"
                                                        id="currentmeter{{ $i }}"
                                                        data-price_per_unit2="{{ $invoice->usermeterinfos->meter_type->price_per_unit }}"
                                                        class="form-control text-right currentmeter2 border-success  w-40 col-5">
                                                </div>

                                            </li>
                                            <li>
                                                <div class="row">
                                                    <span class="col-7">จำนวนสุทธิ</span>
                                                    <input type="text" value="{{ $invoice->currentmeter }}"
                                                        type="text" readonly
                                                        class="form-control text-right water_used_net w-40 col-5"
                                                        id="water_used_net2{{ $i }}" value="">
                                                </div>

                                                <hr class="horizontal dark">
                                            </li>
                                        </ul>

                                        <input type="submit" class="btn bg-gradient-dark w-100 mt-2 mb-0"
                                            id="print_multi_inv" value="บันทึก">
                                        <input type="hidden" value="{{ $invoice->inv_id }}"
                                            name="data[{{ $i }}][inv_id]">
                                        <input type="hidden" value="inv_create_mobile" name="inv_from_page">
                                        <input type="hidden"
                                            value="{{ $invoice->usermeterinfos->undertake_subzone_id }}"
                                            name="subzone_id">
                                    </div>
                                </div>

                            </td>
                        </tr>--}}
                        <?php $i++; ?>
                    @endforeach
                </tbody>
            </table>
        </form>

    </div>

    {{-- <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script> --}}
    <script src="{{asset('/adminlte/plugins/jquery/jquery.min.js')}}"></script>
    <script src="{{asset('/adminlte/plugins/datatables/jquery.dataTables.js')}}"></script>
    <script src="{{asset('/adminlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
    {{-- <script src="https://cdn.datatables.net/plug-ins/1.13.6/pagination/select.js"></script> --}}


    <script>



        setTimeout(() => {
            $('.alert-success').toggle('slow')
        }, 1000)
        let i2 = 0;

        function del(id) {
            let res = window.confirm('ต้องการลบข้อมูลใช่หรือไม่ !!!')
            if (res) {
                window.location.href = `/invoice/delete/${id}/ลบ`
            } else {
                return false
            }
        }
        //คำนวนเงินค่าใช้น้ำ
        $('.currentmeter2').keyup(function() {
            let inv_id = $(this).data('id')
            let price_per_unit = $(this).data('price_per_unit2')
            console.log(price_per_unit)
            let currentmeter = $(this).val()
            let lastmeter = $(`#lastmeter2${inv_id}`).val()
            let net = currentmeter == '' ? 0 : currentmeter - lastmeter;
            let total = (net * price_per_unit) + check_meter_reserve_price(inv_id);
            $('#water_used_net2' + inv_id).val(net)
            $('#total2' + inv_id).val(total);
        });

        function check_meter_reserve_price(inv_id) {
            let lastmeter = $(`#lastmeter${inv_id}`).val()
            let currentmeter = $(`#currentmeter${inv_id}`).val();

            let diff = currentmeter - lastmeter;

            let res = diff == 0 ? 10 : 0;
            $('#meter_reserve_price' + inv_id).val(res)
            return res;
        }

        var table2 = $('#DivIdToPrint2').DataTable({
            responsive: true,
            searching: true,
        })

        $(document).ready(function() {



            $('#DivIdToPrint2_info').remove()
            $('#DivIdToPrint2_paginate').remove()
            $('#DivIdToPrint2_length').remove()
            $('.currentmeter').val('')
        })

        //เพิ่มข้อมูลลงตาราง lists
        $(document).on('click', '.username', function() {
            let user_id = $(this).data('user_id')
            var tr = $(this).closest('tr');
            var row = table2.row(tr);
            if (row.child.isShown()) {
                // This row is already open - close it
                row.child.hide();
                tr.removeClass('shown');
            } else {
                // Open this row
                //หาข้อมูลการชำระค่าน้ำประปาของ  user
                $.get(`/api/users/user/${user_id}`).done(function(data) {
                    row.child(format(data)).show();
                    tr.addClass('shown');
                });
            }
        })

        function format(d) {
            let text = `<table class="table table-striped">
                    <thead>
                        <tr>
                        <th>เลขมิเตอร์</th>
                        <th>วันที่</th>
                        <th>รอบบิล</th>
                        <th>ยอดครั้งก่อน</th>
                        <th>ยอดปัจจุบัน</th>
                        <th>จำนวนที่ใช้</th>
                        <th>คิดเป็นเงิน(บาท)</th>
                        <th>สถานะ</th>
                        </tr>
                    </thead>
                    <tbody>`;
            d[0].usermeterinfos.invoice.forEach(element => {
                console.log(element)

                let _status = 'รอบันทึกข้อมูล'
                if (element.status === "invoice") {
                    _status = '<button class="btn btn-sm btn-outline-info">ออกใบแจ้งหนี้แล้ว</button>'
                } else if (element.status === "paid") {
                    _status = '<button class="btn btn-sm btn-outline-success">ชำระเงินแล้ว</button>'
                } else if (element.status === "owe") {
                    _status = '<button class="btn btn-sm btn-outline-warning">ค้างชำระ</button>'
                }
                if (element.status !== 'init') {
                    text += `
                            <tr>
                            <td>${d[0].usermeterinfos.meternumber}</td>
                            <td>${element.updated_at_th}</td>
                            <td>${element.invoice_period.inv_p_name}</td>
                            <td>${element.lastmeter}</td>
                            <td>${element.currentmeter}</td>
                            <td>${element.currentmeter - element.lastmeter }</td>
                            <td>${(element.currentmeter - element.lastmeter)*8 }</td>
                            <td>${_status}</td>
                            </tr>
                    `;
                } //if
            });
            text += `</tbody>
                </table>`;
            return text;
        }

        function printtag(tagid) {
            var hashid = "#" + tagid;
            var tagname = $(hashid).prop("tagName").toLowerCase();
            var attributes = "";
            var attrs = document.getElementById(tagid).attributes;
            $.each(attrs, function(i, elem) {
                attributes += " " + elem.name + " ='" + elem.value + "' ";
            })
            var divToPrint = $(hashid).html();
            var head = "<html><head>" + $("head").html() + "</head>";
            var allcontent = head + "<body  onload='window.print()' >" + "<" + tagname + attributes + ">" + divToPrint +
                "</" + tagname + ">" + "</body></html>";
            var newWin = window.open('', 'Print-Window');
            newWin.document.open();
            newWin.document.write(allcontent);
            newWin.document.close();
            setTimeout(function() {
                newWin.close();
            }, 10);
        }
        $(".exportToExcel").click(function() {
            $("#DivIdToPrint").table2excel({
                // exclude CSS class
                exclude: ".noExl",
                name: "Worksheet Name",
                filename: "SomeFile", //do not include extension
                fileext: ".xls" // file extension
            });
        });
    </script>

    {{-- <div class="text-center" width="2%">
                                    <a href="javascript:void(0)" class="btn btn-outline-warning delbtn2"
                                        onclick="del('{{ $invoice->meter_id_fk }}')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                                <div class="border-0 text-center">
                                    {{ $invoice->usermeterinfos->meternumber }}
                                    <input type="hidden" value="{{ $invoice->usermeterinfos->meternumber }}"
                                        name="data[{{ $i }}][meternumber]" data-id="{{ $i }}"
                                        id="meternumber{{ $i }}"
                                        class="form-control text-right meternumber border-primary text-sm text-bold"
                                        readonly>
                                    <input type="hidden" value="{{ $invoice->meter_id_fk }}"
                                        name="data[{{ $i }}][meter_id]">
                                </div>
                                <div class="border-0 text-left">
                                    <span class="username" data-user_id="{{ $invoice->usermeterinfos->user_id }}"><i
                                            class="fas fa-search-plus"></i>
                                        {{ $invoice->usermeterinfos->user->firstname . '' . $invoice->usermeterinfos->user->firstname . ' ' . $invoice->usermeterinfos->user->lastname }}</span>
                                    <input type="hidden" name="data[{{ $i }}][user_id]"
                                        value="{{ $invoice->usermeterinfos->user_id }}">

                                </div>
                                <div class="text-center">
                                    {{ $invoice->usermeterinfos->user->address }}
                                    <input type="hidden" readonly class="form-control "
                                        value="{{ $invoice->usermeterinfos->user->address }}"
                                        name="data[{{ $i }}][address]">
                                </div>

                                <div class="border-0">
                                    <input type="text" value="{{ $invoice->currentmeter }}"
                                        name="data[{{ $i }}][lastmeter]" data-id="{{ $i }}"
                                        id="lastmeter{{ $i }}"
                                        data-price_per_unit="{{ $invoice->usermeterinfos->meter_type->price_per_unit }}"
                                        class="form-control text-right lastmeter">
                                </div>
                                <div class="border-0 text-right">
                                    <input type="text" value="0" name="data[{{ $i }}][currentmeter]"
                                        data-id="{{ $i }}" id="currentmeter{{ $i }}"
                                        data-price_per_unit="{{ $invoice->usermeterinfos->meter_type->price_per_unit }}"
                                        class="form-control text-right currentmeter border-success">

                                </div>
                                <div class="border-0 text-right">
                                    <input type="text" readonly class="form-control text-right water_used_net"
                                        id="water_used_net{{ $i }}" value="">
                                </div>
                                <div class="border-0 text-right">
                                    <input type="text" readonly class="form-control text-right meter_reserve_price"
                                        id="meter_reserve_price{{ $i }}" value="">
                                </div>

                                <div class="border-0 text-right">
                                    <input type="text" readonly class="form-control text-right total"
                                        id="total{{ $i }}" value="">
                                </div> --}}
