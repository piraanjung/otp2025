<style>
    tr,
    td {
        background-color: white !important
    }

    .table-striped-columns>:not(caption)>tr>:nth-child(2n),
    .table-striped>tbody>tr:nth-of-type(odd)>* {
        --bs-table-accent-bg: white;
        color: white;
    }

    input[type="search"] {
        padding: 0.5rem;
        font-size: 1rem;
        display: inline;
    }
</style>
<div class="card">
    <div class="card-body table-responsive">
        <form action="{{ route('invoice.store') }}" method="POST">
            @csrf
            <input type="submit" class="btn btn-primary col-4" id="print_multi_inv" value="บันทึก">
            <br><br>
            <table class="table  table-striped datatable" id="oweTable_mobile">
                <thead class="bg-light">
                    <tr>
                        <th class="text-center"></th>
                    </tr>
                </thead>

                <tbody id="app">
                    <?php $i = 1; ?>
                    @foreach ($inv_in_seleted_subzone as $invoice)
                        <tr data-id="{{ $i }}" class="data">
                            <td>
                                <div class="card bg-info">
                                    <div class="card-header bg-info">
                                        <div class="row h-3 text-white">
                                            <div class="border-0 text-center col-10">
                                                <div class="border-0 text-left">
                                                    <span class="username "
                                                        data-user_id="{{ $invoice->usermeterinfos->user_id }}">
                                                        {{ $invoice->usermeterinfos->user->prefix . '' . $invoice->usermeterinfos->user->firstname . ' ' . $invoice->usermeterinfos->user->lastname }}</span>
                                                    <input type="hidden" name="data[{{ $i }}][user_id]"
                                                        value="{{ $invoice->usermeterinfos->user_id }}">

                                                </div>
                                                {{ $invoice->usermeterinfos->meternumber }}
                                                <input type="hidden"
                                                    value="{{ $invoice->usermeterinfos->meternumber }}"
                                                    name="data[{{ $i }}][meternumber]"
                                                    data-id="{{ $i }}" id="meternumber{{ $i }}"
                                                    class="form-control text-right meternumber border-primary text-sm text-bold"
                                                    readonly>
                                                <input type="hidden" value="{{ $invoice->meter_id_fk }}"
                                                    name="data[{{ $i }}][meter_id]">
                                            </div>
                                            <div class="text-center col-2">
                                                <a href="javascript:void(0)" class="btn btn-danger delbtn2"
                                                    onclick="del('{{ $invoice->meter_id_fk }}')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                            <div class="">
                                                บ้านเลขที่ {{ $invoice->usermeterinfos->user->address }} หมู่
                                                {{ $invoice->usermeterinfos->user->zone_id }}
                                                <input type="hidden" readonly class="form-control "
                                                    value="{{ $invoice->usermeterinfos->user->address }}"
                                                    name="data[{{ $i }}][address]">
                                            </div>
                                        </div>

                                    </div>
                                    <div class="card-boby">
                                        <div class="row p-2">
                                            <div class="col-6">
                                                <div class="border-0">
                                                    <label>ยอดยกมา</label>
                                                    <input type="text" value="{{ $invoice->lastmeter }}"
                                                        name="data[{{ $i }}][lastmeter]"
                                                        data-id="{{ $i }}"
                                                        id="lastmeter{{ $i }}"
                                                        data-price_per_unit="{{ $invoice->usermeterinfos->meter_type->price_per_unit }}"
                                                        class="form-control text-right lastmeter">
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <label>มิเตอร์ปัจจุบัน</label>
                                                <div class="border-0 text-right">
                                                    <input type="text" value="{{ $invoice->currentmeter }}"
                                                        name="data[{{ $i }}][currentmeter]"
                                                        data-id="{{ $i }}"
                                                        id="currentmeter{{ $i }}"
                                                        data-price_per_unit="{{ $invoice->usermeterinfos->meter_type->price_per_unit }}"
                                                        class="form-control text-right currentmeter border-success">

                                                </div>
                                            </div>
                                        </div>


                                        <?php
                                        $used_net = $invoice->currentmeter - $invoice->lastmeter;
                                        $total = $used_net * $invoice->usermeterinfos->meter_type->price_per_unit;
                                        ?>
                                        <div class="row p-2">
                                            <div class="border-0 text-right col-4">
                                                <!-- จำนวนสุทธิ -->
                                                <label>ใช้น้ำสุทธิ</label>

                                                <input type="text" readonly
                                                    class="form-control text-right water_used_net"
                                                    id="water_used_net{{ $i }}"
                                                    value="{{ $used_net }}">
                                            </div>
                                            <div class="border-0 text-right col-4">
                                                <label>ค่ารักษามาตร</label>
                                                <input type="text" readonly
                                                    class="form-control text-right meter_reserve_price"
                                                    id="meter_reserve_price{{ $i }}"
                                                    value="{{ $used_net == 0 ? 10 : 0 }}">
                                            </div>

                                            <div class="border-0 text-right col-4">
                                                <label>รวมเป็นเงิน</label>
                                                <input type="text" readonly class="form-control text-right total"
                                                    id="total{{ $i }}"
                                                    value="{{ $used_net == 0 ? 10 : $total }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>

                        </tr>
                        <?php $i++; ?>
                    @endforeach
                </tbody>
            </table>
        </form>
    </div>
</div>
<script>
    let i = 0;
    let table;
    let cloneThead = true
    let col_index = -1

    //getข้อมูลจาก api มาแสดงใน datatable
    $(document).ready(function() {
        // getOweInfos()
        table = $('#oweTable_mobile').DataTable({
            responsive: true,
            // order: false,
            searching: true,
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
            select: false,


        }) //table
        // ทำการ clone thead แล้วสร้าง input text
        // if (cloneThead) {
        //     $('#oweTable_mobile thead tr').clone().appendTo('#oweTable_mobile thead');
        //     cloneThead = false
        // }
        // $('#oweTable_mobile thead tr:eq(1) th').each(function(index) {
        //     var title = $(this).text();
        //     $(this).removeClass('sorting')
        //     $(this).removeClass('sorting_asc')
        //     if (index < 4) {
        //         $(this).html(
        //             `<input type="text" data-id="${index}" class="col-md-12" style="font-size:14px" id="search_col_${index}" placeholder="ค้นหา" />`
        //         );
        //     } else {
        //         $(this).html('')
        //     }
        // });

        $('.overlay').remove()
        $('#oweTable_mobile_info').remove()
        $('#oweTable_mobile_paginate').remove()
        $('#oweTable_mobile_length').remove()

        // $('#oweTable_mobile_filter').remove()
    })

    //คำนวนเงินค่าใช้น้ำ
    $(document).on('keyup', '.currentmeter', function() {
        let inv_id = $(this).data('id')
        let price_per_unit = $(this).data('price_per_unit')
        let currentmeter = $(this).val()
        let lastmeter = $(`#lastmeter${inv_id}`).val()
        let net = currentmeter - lastmeter
        let total = (net * price_per_unit) + check_meter_reserve_price(inv_id);
        $('#water_used_net' + inv_id).val(net)
        $('#total' + inv_id).val(total);
        $('#changevalue' + inv_id).val(1)
    });

    $(document).on('keyup', '.lastmeter', function() {
        let inv_id = $(this).data('id')
        let price_per_unit = $(this).data('price_per_unit')
        let lastmeter = $(this).val()
        let currentmeter = $(`#currentmeter${inv_id}`).val()
        let net = currentmeter - lastmeter
        let total = (net * price_per_unit) + check_meter_reserve_price(inv_id);
        $('#water_used_net' + inv_id).val(net)
        $('#total' + inv_id).val(total);
        $('#changevalue' + inv_id).val(1)
    });

    function check_meter_reserve_price(inv_id) {
        let lastmeter = $(`#lastmeter${inv_id}`).val()
        let currentmeter = $(`#currentmeter${inv_id}`).val();

        let diff = currentmeter - lastmeter;

        let res = diff == 0 ? 10 : 0;
        $('#meter_reserve_price' + inv_id).val(res)
        return res;
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
                "info": "แสดง _MENU_ แถว",
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
