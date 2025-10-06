@extends('layouts.admin1')

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
    แก้ไขข้อมูลใบแจ้งหนี้
@endsection
@section('nav-topic')
    เส้นทาง:: {{ $inv_in_seleted_subzone[0]->undertake_subzone->subzone_name }}
@endsection

@section('content')
    <style>
        .hidden {
            /* display: none */
        }
    </style>
    <link href="https://cdn.datatables.net/2.2.0/css/dataTables.dataTables.css">
    <link href="https://cdn.datatables.net/select/2.1.0/css/select.dataTables.css">
    <link href="https://cdn.datatables.net/buttons/3.2.0/css/buttons.dataTables.css">


    <div id="web_app">
        <div class="card">
            <div class="card-body table-responsive">
                <form action="{{ route('invoice.zone_update') }}" method="POST">
                    @csrf
                    <input type="submit" class="btn btn-primary col-2" id="print_multi_inv" value="บันทึก">
                    <br><br>
                    <table class="table  table-striped datatable" id="oweTable">
                        <thead class="bg-light">
                            <tr>
                                <th class="text-center">เลขใบแจ้งหนี้</th>
                                <th class="text-center">เลขมิเตอร์</th>
                                <th>เลขมิเตอร์จากโรงงาน</th>
                                <th>เลขสมาชิก</th>
                                <th class="text-center">ชื่อ-สกุล</th>
                                <th class="text-center">บ้านเลขที่</th>
                                <th class="text-center">ยกยอดมา<div>(หน่วย)</div>
                                </th>
                                <th class="text-center">มิเตอร์ปัจจุบัน<div>(หน่วย)</div>
                                </th>
                                <th class="text-center">ใช้น้ำสุทธิ<div>(หน่วย)</div>
                                </th>
                                <th class="text-center">เป็นเงิน<div>(บาท)</div>
                                <th class="text-center">ค่ารักษามาตร<div>(บาท)</div>
                                <th class="text-center">vat 7%<div>(บาท)</div>
                                </th>
                                <th class="text-center">รวมเป็นเงิน<div>(บาท)</div>
                                </th>
                                <th>สถานะการชำระเงิน</th>
                                <th>วันที่บันทึก</th>
                                <th>วันที่แก้ไข</th>
                                <th>ผู้บันทึก</th>
                            </tr>
                        </thead>

                        <tbody id="app">
                            <?php $i = 1; ?>
                            @foreach ($inv_in_seleted_subzone as $u_meter_info)
                                <tr data-id="{{ $i }}" class="data">
                                   
                                    <td class="border-0 text-center">
                                        {{ $u_meter_info->invoice_temp[0]->id }}
                                        <input type="hidden" value="{{ $u_meter_info->invoice_temp[0]->id }}"
                                            name="data[{{ $i }}][inv_id]" data-id="{{ $i }}"
                                            id="inv_id{{ $i }}"
                                            class="form-control text-right inv_id border-primary text-sm text-bold"
                                            readonly>
                                        <input type="hidden" value="{{ $u_meter_info->invoice_temp[0]->id }}"
                                            name="data[{{ $i }}][inv_id]">
                                        <input type="hidden" value="0" id="changevalue{{ $i }}"
                                            name="data[{{ $i }}][changevalue]">

                                    </td>
                                    <td class="border-0 text-center">
                                        {{ $u_meter_info->meternumber }}
                                        <input type="hidden" value="{{ $u_meter_info->meternumber }}"
                                            name="data[{{ $i }}][meternumber]" data-id="{{ $i }}"
                                            id="meternumber{{ $i }}"
                                            class="form-control text-right meternumber border-primary text-sm text-bold"
                                            readonly>
                                        <input type="hidden" value="{{ $u_meter_info->meter_id }}"
                                            name="data[{{ $i }}][meter_id]">
                                    </td>
                                    <td>
                                        {{ $u_meter_info->factory_no }}
                                    </td>
                                    <td>
                                        {{ $u_meter_info->user_id }}
                                    </td>
                                    <td class="border-0 text-left">
                                        <span class="username" data-user_id="{{ $u_meter_info->user_id }}">
                                            {{ $u_meter_info->user->firstname . ' ' . $u_meter_info->user->lastname }}</span>
                                        <input type="hidden" name="data[{{ $i }}][user_id]"
                                            value="{{ $u_meter_info->user_id }}">

                                    </td>
                                    <td class="text-center">
                                        {{ $u_meter_info->meter_address }}
                                        <input type="hidden" readonly class="form-control "
                                            value="{{ $u_meter_info->meter_address }}"
                                            name="data[{{ $i }}][address]">
                                    </td>
                                    @if (collect($u_meter_info->invoice_temp)->isEmpty())
                                        {{ dd($u_meter_info) }}
                                    @endif
                                    <td class="border-0">
                                        <input type="text" value="{{ $u_meter_info->invoice_temp[0]->lastmeter }}"
                                            name="data[{{ $i }}][lastmeter]" data-id="{{ $i }}"
                                            id="lastmeter{{ $i }}"
                                            data-price_per_unit="{{ $u_meter_info->meter_type->price_per_unit }}"
                                            class="form-control text-right lastmeter">
                                           <span class="hidden"> {{$u_meter_info->invoice_temp[0]->lastmeter}}</span>

                                    </td>
                                    <td class="border-0 text-right">
                                        <input type="text" value="{{ $u_meter_info->invoice_temp[0]->currentmeter }}"
                                            name="data[{{ $i }}][currentmeter]" data-id="{{ $i }}"
                                            id="currentmeter{{ $i }}"
                                            data-price_per_unit="{{ $u_meter_info->meter_type->price_per_unit }}"
                                            class="form-control text-right currentmeter border-success">
                                           <span class="hidden"> {{$u_meter_info->invoice_temp[0]->currentmeter}}</span>

                                    </td>

                                    <td class="border-0 text-right">
                                        <!-- จำนวนสุทธิ -->
                                        <input type="text" readonly class="form-control text-right water_used_net"
                                            id="water_used_net{{ $i }}"
                                            name="data[{{ $i }}][water_used]"
                                            value="{{ $u_meter_info->invoice_temp[0]->water_used }}">
                                            <span class="hidden"> {{$u_meter_info->invoice_temp[0]->water_used}}</span>

                                    </td>
                                    <td class="border-0 text-right">
                                        <!-- เป็นเงิน -->
                                        <input type="text" readonly class="form-control text-right paid"
                                            name="data[{{ $i }}][paid]" id="paid{{ $i }}"
                                            value="{{ $u_meter_info->invoice_temp[0]->paid }}">
                                            <span class="hidden"> {{$u_meter_info->invoice_temp[0]->paid}}</span>

                                    </td>
                                    <td class="border-0 text-right">
                                        <!-- ค่ารักษามาตร -->
                                        <input type="text" readonly class="form-control text-right meter_reserve_price"
                                            id="meter_reserve_price{{ $i }}" value="10">
                                            <span class="hidden"> 10</span>

                                        {{-- value="{{ $u_meter_info->invoice_temp[0]->inv_type == 'r' ? $u_meter_info->invoice_temp[0]->reserve : 0 }}"> --}}
                                    </td>
                                    <td class="border-0 text-right">
                                        <input type="text" readonly class="form-control text-right vat"
                                            name="data[{{ $i }}][vat]" id="vat{{ $i }}"
                                            value="{{ $u_meter_info->invoice_temp[0]->vat }}">
                                            <span class="hidden"> {{$u_meter_info->invoice_temp[0]->vat}}</span>

                                    </td>

                                    <td class="border-0 text-right">
                                        <input type="text" readonly class="form-control text-right total"
                                            id="total{{ $i }}" name="data[{{ $i }}][totalpaid]"
                                            value="{{ $u_meter_info->invoice_temp[0]->totalpaid }}">
                                            <span class="hidden"> {{$u_meter_info->invoice_temp[0]->totalpaid}}</span>

                                    </td>
                                    <td>
                                        <span class=""> {{$u_meter_info->invoice_temp[0]->status}}</span>

                                    </td>
                                    <td>
                                        <span class=""> {{$u_meter_info->invoice_temp[0]->created_at}}</span>

                                    </td>
                                    <td>
                                        <span class=""> {{$u_meter_info->invoice_temp[0]->updated_at}}</span>

                                    </td>
                                    <td>
                                        <span class=""> {{$u_meter_info->invoice_temp[0]->recorder->firstname." ".$u_meter_info->invoice_temp[0]->recorder->lastname."(".$u_meter_info->invoice_temp[0]->recorder_id.")"}}</span>

                                    </td>
                                </tr>
                                <?php $i++; ?>
                            @endforeach
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
    </div>
    <div id="mobile">
        {{-- @include('invoice.zone_edit_moblie') --}}
    </div>
@endsection


@section('script')
<script src="https://code.jquery.com/jquery-3.7.1.js"></script>

<script src="https://cdn.datatables.net/2.2.0/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/buttons/3.2.0/js/buttons.dataTables.js"></script>
    <script src="https://cdn.datatables.net/select/1.3.3/js/dataTables.select.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.0/js/dataTables.buttons.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/3.2.0/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/3.2.0/js/buttons.print.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script>
        let screenW = window.screen.availWidth
        console.log('screenW', screenW)
        if (screenW < 860) {
            $('#web_app').addClass('hidden')
            $('#mobile').removeClass('hidden')
        } else {
            $('#web_app').removeClass('hidden')
            $('#mobile').addClass('hidden')
        }

        let i2 = 0;
        let table;
        let cloneThead2 = true
        let col_index2 = -1

        //getข้อมูลจาก api มาแสดงใน datatable
        $(document).ready(function() {
            // getOweInfos()
            table = $('#oweTable').DataTable({
                // responsive: true,
                // order: false,
                // searching:false,
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
                select: false,
              
            // buttons: [
              
            //     {
            //         extend: 'collection',
            //         text: 'Export',
            //         buttons: ['copy', 
            //          {
            //             extend: 'excelHtml5',
            //             title: 'ตุลาคม 2567 - <?=$inv_in_seleted_subzone[0]->undertake_subzone->subzone_name;?>'
            //         }, 
            //         'pdf', 'print']
            //     }
            // ]
        
    

            }) //table
            // ทำการ clone thead แล้วสร้าง input text
            if (cloneThead2) {
                $('#oweTable thead tr').clone().appendTo('#oweTable thead');
                cloneThead2 = false
            }
            $('#oweTable thead tr:eq(1) th').each(function(index) {
                var title = $(this).text();
                $(this).removeClass('sorting')
                $(this).removeClass('sorting_asc')
                if (index < 4 && index > 0) {
                    $(this).html(
                        `<input type="text" data-id="${index}" class="col-md-12" style="font-size:14px" id="search_col_${index}" placeholder="ค้นหา" />`
                    );
                } else {
                    $(this).html('')
                }
            });

            $('.overlay').remove()
            $('#oweTable_filter').remove();

            let col_index = -1
            $('#oweTable thead input[type="text"]').keyup(function() {
                console.log('sdf')
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
                    if (col >= 5) {
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

        })


        //คำนวนเงินค่าใช้น้ำ
        $(document).on('keyup', '.currentmeter', function() {
            let id = $(this).data('id')
            let res = check_meter_reserve_price(id)
            let price_per_unit = 6 //$(this).data('price_per_unit')
            let currentmeter = $(this).val()
            let lastmeter = $(`#lastmeter${id}`).val()
            let net = currentmeter - lastmeter
            let paid = net * price_per_unit
            let vat = 0 // net === 0 ? 0.7 : paid * 0.07

            let total = paid + vat + res;
            $('#water_used_net' + id).val(net)
            $('#paid' + id).val(paid)
            $('#vat' + id).val(vat.toFixed(2))
            $('#total' + id).val(total);
            $('#changevalue' + id).val(1)

        });

        $(document).on('keyup', '.lastmeter', function() {
            let id = $(this).data('id')
            let price_per_unit = 6 //$(this).data('price_per_unit')
            let currentmeter = $(this).val()
            let lastmeter = $(`#lastmeter${id}`).val()
            let net = currentmeter - lastmeter
            let paid = net * price_per_unit
            let vat = 0 //net === 0 ? 0.7 : paid * 0.07
            let total = paid + vat + 10 //$(`#meter_reserve_price${id}`).val();
            $('#water_used_net' + id).val(net)
            $('#paid' + id).val(paid)
            $('#vat' + id).val(vat.toFixed(2))
            $('#total' + id).val(total);
            $('#changevalue' + id).val(1)
            check_meter_reserve_price(id)

        });

        function check_meter_reserve_price(inv_id) {
            let lastmeter = $(`#lastmeter${inv_id}`).val()
            let currentmeter = $(`#currentmeter${inv_id}`).val();

            let diff = currentmeter - lastmeter;

            let res = 10 //diff == 0 ? 10 : 0;
            $('#meter_reserve_price' + inv_id).val(res)
            return 10 //res;
        }

        $('.dataTable').DataTable({
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
            }
        })
        $(document).ready(function() {
            $('.paginate_page').text('หน้า')
            let val = $('.paginate_of').text()
            $('.paginate_of').text(val.replace('of', 'จาก'));

            setTimeout(() => {
                $('.alert').toggle('slow')
            }, 2000)

        })

        $(document).on('click', '.delBtn', function() {
            let res = window.confirm('ต้องการลบข้อมูลใช่หรือไม่ !!!')
            if (res === true) {
                let inv_id = $(this).data('del_invoice_id');
                let comment = $(`#comment${inv_id}`).val()
                window.location.href = `/invoice/delete/${inv_id}/${comment}`
            } else {
                return false;
            }
        });
    </script>
@endsection
