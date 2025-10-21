@extends('layouts.admin1')

@section('nav-invoice')
    active
@endsection

@section('nav-header')
            ออกใบแจ้งหนี้
@endsection
ิ
@section('nav-current')
    เพิ่มผู้ใช้น้ำระหว่างรอบบิล
@endsection

@section('nav-topic')
เพิ่มผู้ใช้น้ำระหว่างรอบบิล
@endsection

@section('style')
    <style>
        .hidden {
            display: none
        }
    </style>

    <script src="{{asset('/adminlte/plugins/jquery/jquery.min.js')}}"></script>
    <script src="{{asset('/adminlte/plugins/datatables/jquery.dataTables.js')}}"></script>
    <script src="{{asset('/adminlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
    <script src="https://cdn.datatables.net/plug-ins/1.13.6/pagination/select.js"></script>

@endsection

@section('content')

        <div id="web_app" class="">
            <div class="card">
                <div class="card-body table-responsive">
                    <form action="{{ route('invoice.store') }}" method="POST">
                        @csrf
                        <input type="submit" class="btn btn-primary col-2" id="print_multi_inv" value="บันทึก">
                        <input type="hidden" value="inv_create" name="inv_from_page">
                        <input type="hidden" value="{{$subzone->zone_id}}" name="zone_id">
                        <input type="hidden" value="{{$subzone->id}}" name="subzone_id">
                        <br><br>
                        <table class="table  table-striped datatable" id="DivIdToPrint">
                            <thead class="bg-light">
                                <tr>
                                    <th></th>
                                    <th class="text-center">เลขมิเตอร์</th>
                                    <th class="text-center">Factory No.</th>
                                    <th class="text-center">ชื่อ-สกุล</th>
                                    <th class="text-center">บ้านเลขที่</th>
                                    <th class="text-center">ยกยอดมา</th>
                                    <th class="text-center">มิเตอร์<div>ปัจจุบัน</div></th>
                                    <th class="text-center">ใช้น้ำ<div> (หน่วย)</div></th>
                                    <th class="text-center">เป็นเงิน <div>(บาท)</div></th>
                                    <th class="text-center">ค่ารักษา<div>มาตร (บาท)</div></th>
                                    <th class="text-center">ภาษีมูลค่า<div>เพิ่ม 7% (บาท)</div></th>
                                    <th class="text-center">รวมทั้งสิ้น <div>(บาท)</div></th>
                                </tr>
                            </thead>

                            <tbody id="app">
                                <?php $i = 1; ?>
                                @if (collect($member_not_yet_recorded_present_inv_period)->count() > 0)
                                    @foreach ($member_not_yet_recorded_present_inv_period[0] as $key => $invoice)
                                        <tr data-id="{{ $i }}" class="data">
                                        <td>{{ $i }}</td>
                                        <td class="border-0 text-center">
                                            {{ $invoice->meternumber }}
                                            <input type="hidden" value="{{ $invoice->meternumber }}"
                                                name="data[{{ $i }}][meternumber]"
                                                data-id="{{ $i }}" id="meternumber{{ $i }}"
                                                class="form-control text-right meternumber border-primary text-sm text-bold"
                                                readonly>
                                            <input type="hidden" value="new_inv" name="data[{{ $i }}][inv_id]">
                                            <input type="hidden" value="{{ $invoice->meter_id }}" name="data[{{ $i }}][meter_id]">

                                        </td>
                                        <td>{{ $invoice->factory_no }}</td>

                                        <td class="border-0 text-left">
                                            <span class="username"
                                                data-user_id="{{ $invoice->user_id }}"><i
                                                    class="fas fa-search-plus"></i>
                                                {{ $invoice->user->firstname . ' ' . $invoice->user->lastname }}</span>
                                            <input type="hidden" name="data[{{ $i }}][user_id]"
                                                value="{{ $invoice->user_id }}">

                                        </td>
                                        <td class="text-center">
                                            {{ $invoice->user->address }}
                                            <input type="hidden" readonly class="form-control "
                                                value="{{ $invoice->user->address }}"
                                                name="data[{{ $i }}][address]">
                                        </td>

                                        <td class="border-0">
                                            <input type="text" value="0"
                                                name="data[{{ $i }}][lastmeter]" data-id="{{ $i }}"
                                                id="lastmeter{{ $i }}"
                                                data-price_per_unit="{{ $invoice->meter_type->price_per_unit }}"
                                                class="form-control text-end lastmeter">
                                        </td>
                                        <td class="border-0 text-right">
                                            <input type="text" value="0"
                                                name="data[{{ $i }}][currentmeter]"
                                                data-id="{{ $i }}" id="currentmeter{{ $i }}"
                                                data-price_per_unit="{{ $invoice->meter_type->price_per_unit }}"
                                                class="form-control text-end currentmeter border-success">

                                        </td>
                                        <td class="border-0 text-right">
                                            <input type="text" readonly class="form-control text-end water_used_net"
                                                id="water_used_net{{ $i }}" value="">
                                        </td>
                                        <td class="border-0 text-right">
                                            <input type="text" readonly class="form-control text-end paid"
                                                id="paid{{ $i }}" value="">
                                        </td>
                                        <td class="border-0 text-right">
                                            <input type="text" readonly
                                                class="form-control text-end meter_reserve_price"
                                                id="meter_reserve_price{{ $i }}" value="">
                                        </td>
                                        <td class="border-0 text-right">
                                            <input type="text" readonly class="form-control text-end vat"
                                                id="vat{{ $i }}" value="">
                                        </td>

                                        <td class="border-0 text-right">
                                            <input type="text" readonly class="form-control text-end total"
                                                id="total{{ $i }}" value="">
                                        </td>
                                        </tr>
                                        <?php $i++; ?>
                                    @endforeach
                                @else
                                    @foreach ($invoices as $invoice)
                                        <tr data-id="{{ $i }}" class="data">
                                            <th class="text-center" width="2%">
                                                <a href="javascript:void(0)" class="btn btn-outline-warning delbtn2"
                                                    onclick="del('{{ $invoice->meter_id_fk }}')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </th>
                                            <td class="border-0 text-center">
                                                {{ $invoice->usermeterinfos->meternumber }}
                                                <input type="hidden" value="{{ $invoice->usermeterinfos->meternumber }}"
                                                    name="data[{{ $i }}][meternumber]"
                                                    data-id="{{ $i }}" id="meternumber{{ $i }}"
                                                    class="form-control text-right meternumber border-primary text-sm text-bold"
                                                    readonly>
                                                <input type="hidden" value="{{ $invoice->meter_id_fk }}"
                                                    name="data[{{ $i }}][meter_id]">
                                                <input type="hidden" value="{{ $invoice->id }}" name="data[{{ $i }}][inv_id]">
                                                <input type="hidden" value="{{ $invoice->usermeterinfos->meter_type->rateConfigs[0]->fixed_rate_per_unit }}" name="data[{{ $i }}][fixed_rate_per_unit]">


                                            </td>
                                            <td class="text-center">{{ $invoice->usermeterinfos->factory_no }}</td>

                                            <td class="border-0 text-left">
                                                <span class="username"
                                                    data-user_id="{{ $invoice->usermeterinfos->user_id }}"><i
                                                        class="fas fa-search-plus"></i>
                                                    {{ $invoice->usermeterinfos->user->firstname . ' ' . $invoice->usermeterinfos->user->lastname." ".  $invoice->usermeterinfos->submeter_name}}</span>
                                                <input type="hidden" name="data[{{ $i }}][user_id]"
                                                    value="{{ $invoice->usermeterinfos->user_id }}">

                                            </td>
                                            <td class="text-center">
                                                {{ $invoice->usermeterinfos->user->address }}
                                                <input type="hidden" readonly class="form-control "
                                                    value="{{ $invoice->usermeterinfos->user->address }}"
                                                    name="data[{{ $i }}][address]">
                                            </td>

                                            <td class="border-0">
                                                <input type="text" value="{{ $invoice->lastmeter }}"
                                                    name="data[{{ $i }}][lastmeter]" data-id="{{ $i }}"
                                                    id="lastmeter{{ $i }}"
                                                    data-price_per_unit="{{ $invoice->usermeterinfos->meter_type->rateConfigs[0]->fixed_rate_per_unit }}"
                                                    class="form-control text-end lastmeter">
                                            </td>
                                            <td class="border-0 text-right">
                                                <input type="text" value="{{ !isset($invoice->currentmeter) ? 0 : $invoice->currentmeter  }}"
                                                    name="data[{{ $i }}][currentmeter]"
                                                    data-id="{{ $i }}" id="currentmeter{{ $i }}"
                                                    data-price_per_unit="{{ $invoice->usermeterinfos->meter_type->rateConfigs[0]->fixed_rate_per_unit }}"
                                                    class="form-control text-end currentmeter border-success">

                                            </td>
                                            <td class="border-0 text-right">
                                                <input type="text" readonly class="form-control text-end water_used_net" 
                                                    id="water_used_net{{ $i }}" value="{{ !isset($invoice->water_used) ? 0 : $invoice->water_used  }}">
                                            </td>
                                            <td class="border-0 text-right">
                                                <input type="text" readonly class="form-control text-end paid" name="data[{{ $i }}][paid]"
                                                    id="paid{{ $i }}" value="{{ !isset($invoice->paid) ? 0 : $invoice->paid  }}">
                                            </td>
                                            <td class="border-0 text-right">
                                                <input type="text" readonly name="data[{{ $i }}][meter_reserve_price]"
                                                    class="form-control text-end meter_reserve_price"
                                                    id="meter_reserve_price{{ $i }}" value="{{ !isset($invoice->usermeterinfos->meter_type->rateConfigs[0]) ? 0 : $invoice->usermeterinfos->meter_type->rateConfigs[0]->min_usage_charge  }}">
                                            </td>
                                            
                                            <td class="border-0 text-right">
                                                <input type="text" readonly name="data[{{ $i }}][vat]"
                                                    class="form-control text-end vat" data-id="{{ $i }}"
                                                    id="vat{{ $i }}" value="{{ !isset($invoice->usermeterinfos->meter_type->rateConfigs[0]->vat) ? 0 : $invoice->usermeterinfos->meter_type->rateConfigs[0]->vat  }}">
                                            </td>


                                            <td class="border-0 text-right">
                                                <input type="text" readonly class="form-control text-end total" name="data[{{ $i }}][totalpaid]"
                                                    id="total{{ $i }}" value="{{ !isset($invoice->totalpaid) ? 0 : $invoice->totalpaid  }}">
                                            </td>
                                        </tr>
                                        <?php $i++; ?>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
        </div>
        {{-- <div id="mobile">
            @include('invoice.zone_create_mobile')
    </div> --}}
        <canvas id="myCanvas" width="100" height="100"></canvas>
    @endsection


    @section('script')


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
            let i = 0;

            function del(id) {
                let res = window.confirm('ต้องการลบข้อมูลใช่หรือไม่ !!!')
                if (res) {
                    window.location.href = `/invoice/delete/${id}/ลบ`
                } else {
                    return false
                }
            }
           function cal(water_used){
            let reserve_meter_ref   = parseFloat($('#reserve_meter_ref').val())
            let price_per_unit_ref  = parseFloat($('#price_per_unit_ref').val())
            let vat_ref             = parseFloat($(`#vat_ref`).val())

            let paid                = parseFloat(water_used) * price_per_unit_ref
            let vat                 = paid *vat_ref
            let total               = paid + vat + reserve_meter_ref
            return [parseFloat(paid).toFixed(2), parseFloat(vat).toFixed(2), parseFloat(total).toFixed(2)]
        }

        //คำนวนเงินค่าใช้น้ำ
        $(document).on('keyup', '.currentmeter', function() {
            let id = $(this).data('id')
            let currentmeter = $(this).val()
            let lastmeter = $(`#lastmeter${id}`).val()
            let water_used = parseFloat(currentmeter) - parseFloat(lastmeter)
            if(parseFloat(water_used) < 0){
                alert('จำนวนการใช้น้ำติดลบไม่ได้')
                currentmeter = $(this).data('val_ref')
                $(this).val(currentmeter)
                water_used = parseFloat(currentmeter) - parseFloat(lastmeter)
            }
            const [paid,vat, total] = cal(water_used)

            $('#water_used_net' + id).val(water_used)
            $('#paid' + id).val(paid)
            $('#vat' + id).val(vat)
            $('#total' + id).val(total);
            $('#changevalue' + id).val(1)
        

        });

        $(document).on('keyup', '.lastmeter', function() {
           let id = $(this).data('id')
            let lastmeter = $(this).val()
            let currentmeter = $(`#currentmeter${id}`).val()
            let water_used = parseFloat(currentmeter) - parseFloat(lastmeter)
                if(parseFloat(water_used) < 0){
                alert('จำนวนการใช้น้ำติดลบไม่ได้')
                lastmeter = $(this).data('val_ref')
                $(this).val(lastmeter)
                water_used = parseFloat(currentmeter) - parseFloat(lastmeter)
            }
            const [paid,vat, total] = cal(water_used)
            $('#water_used_net' + id).val(water_used)
            $('#paid' + id).val(paid)
            $('#vat' + id).val(vat)
            $('#total' + id).val(total);
            $('#changevalue' + id).val(1)
            
           

        });

            var table = $('.datatable').DataTable({
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
            })

            $(document).ready(function() {
                $('.paginate_page').text('หน้า')
                let val = $('.paginate_of').text()
                $('.paginate_of').text(val.replace('of', 'จาก'));
                $('.datatable thead tr').clone().appendTo('.datatable thead');

                $('.datatable thead tr:eq(1) th').each(function(index) {
                    var title = $(this).text();
                    $(this).removeClass('sorting')
                    $(this).removeClass('sorting_asc')
                    if (index >0 && index < 5) {
                        $(this).html(
                            `<input type="text" data-id="${index}" class="col-md-12" id="search_col_${index}" placeholder="ค้นหา" />`
                        );
                    } else {
                        $(this).html('')
                    }
                });

                $('.datatable .dataTables_filter').remove();

                let col_index = -1
                $('.datatable thead input[type="text"]').keyup(function() {
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
                        if (col === 4) {
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
                })
            })

            //เพิ่มข้อมูลลงตาราง lists
            $(document).on('click', '.username', function() {
                let user_id = $(this).data('user_id')
                var tr = $(this).closest('tr');
                var row = table.row(tr);
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
    @endsection
