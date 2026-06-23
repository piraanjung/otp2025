@extends('layouts.admin1')
@section('nav-payment')
    active
@endsection
@section('nav-header')
จัดการใบแจ้งหนี้
@endsection
@section('nav-main')
<a href="{{route('invoice.index')}}"> ออกใบแจ้งหนี้</a>
@endsection
@section('nav-current')
ชำระเงินแล้ว
@endsection
@section('page-topic')
ชำระเงินแล้ว
@endsection

@section('content')

<div class="card  table-responsive">
    <div class="card-body ">
        <div  id="has_invoice">

                    <table class="table mb-0 mt-3  table-striped dataTable text-nowrap">
                        <thead class="bg-light">
                            <tr>
                                <th class="text-center">#</th>
                                <th class="text-center">เลขใบแจ้งหนี้</th>
                                <th class="text-center">เลขมิเตอร์</th>
                                <th class="text-center">ชื่อ-สกุล</th>
                                <th class="text-center">บ้านเลขที่</th>
                                <th class="text-center">ยกยอดมา</th>
                                <th class="text-center">มิเตอร์ปัจจุบัน</th>
                                <th class="text-center">จำนวนสุทธิ</th>
                                <th class="text-center">เป็นเงิน</th>
                                <th class="text-center">หมายเหตุ</th>
                            </tr>
                        </thead>
                        <tbody id="app">
                            <?php $i =1 ; ?>
                            @foreach ($invoices_paid as $invoice)
                                <tr data-id="{{$invoice->id}}" class="data">
                                    <td class="border-0 text-right">
                                        {{$i++}}
                                    </td>
                                    <td class="border-0 text-right">
                                        {{$invoice->id}}
                                    </td>
                                    <td class="border-0 text-right">
                                        {{$invoice->usermeterinfos->meternumber}}
                                    </td>
                                    <td class="border-0 text-left">
                                        {{$invoice->usermeterinfos->user->firstname." ".$invoice->usermeterinfos->user->lastname}}

                                    </td>
                                    <td class="border-0 text-right">
                                       {{$invoice->usermeterinfos->user->address}}
                                    </td>
                                    <td class="border-0 text-right">
                                        {{number_format($invoice->lastmeter)}}
                                    </td>
                                    <td class="border-0 text-right">
                                        {{number_format($invoice->currentmeter)}}
                                    </td>
                                    <?php
                                        $meter_net = $invoice->currentmeter - $invoice->lastmeter;
                                        $total     = $meter_net * $invoice->usermeterinfos->meter_type->price_per_unit
                                    ?>

                                    <td class="border-0 text-right">
                                        {{number_format($meter_net,2)}}
                                    </td>
                                    <td class="border-0 text-right">
                                       {{number_format($total,2)}}
                                    </td>

                                    <td class="border-0">
                                       {{$invoice->comment}}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>

                                <th colspan="7" class="text-right">รวม:</th>
                                <th  class="text-right pr-0"></th>
                                <th  class="text-right pr-0"></th>
                                <th  class="text-right pr-0"></th>

                            </tr>
                        </tfoot>
                    </table>
        </div>
    </div>
</div>

@endsection


@section('script')
<script>
    $('.dataTable').DataTable({
        "pagingType": "listbox",
        "lengthMenu": [[10, 25, 50, 150, -1], [10, 25, 50, 150, "ทั้งหมด"]],
        "language": {
            "search": "ค้นหา:",
            "lengthMenu": "แสดง _MENU_ แถว",
            "info":       "แสดง _START_ ถึง _END_ จาก _TOTAL_ แถว",
            "infoEmpty":  "แสดง 0 ถึง 0 จาก 0 แถว",
            "paginate": {
                "info": "แสดง _MENU_ แถว",
            },
        },
        "footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(), data;
            // Remove the formatting to get integer data for summation
            var intVal = function ( i ) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
            };
            var nf = new Intl.NumberFormat();

            for(let i = 7; i <= 8; i++){
                total_water_price = api
                    .column( i )
                    .data()
                    .reduce( function (a, b) {

                        return intVal(a) + intVal(b);
                    }, 0 );

                // Total over this page
                pageTotal_water_price = api
                    .column( i, { page: 'current'} )
                    .data()
                    .reduce( function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0 );

                // Update footer
                $( api.column( i ).footer() ).html(
                    nf.format(pageTotal_water_price) +' ( ทั้งหมด: '+nf.format(total_water_price) +' )'
                );
            }//for

        }
    })
    $(document).ready(function(){
        $('.paginate_page').text('หน้า')
        let val = $('.paginate_of').text()
        $('.paginate_of').text(val.replace('of', 'จาก'));
    })

</script>
@endsection


