@extends('layouts.adminlte')

@section('mainheader')
แก้ไขข้อมูลประปา {{$zoneInfo[0]->undertake_zone}} [รอบบิล {{$presentInvoicePeriod->inv_period_name}}]
@endsection
@section('invoice')
    active
@endsection
@section('nav')
<a href="{{url('/invoice')}}"> ออกใบแจ้งหนี้</a>
@endsection


@section('content')
@if ($message = Session::get('massage'))
<div class="alert alert-{{Session::get('color')}} alert-block">
	<button type="button" class="close" data-dismiss="alert">×</button>	
	<strong>{{ $message }}</strong>
</div>
@endif
<div class="card  table-responsive">
    <div class="card-body ">
        <div  id="has_invoice">
            <form action="{{url('invoice/zone_update/'.$zoneInfo[0]->undertake_zone_id)}}" method="POST">
                @csrf
                @method("PUT")
                    <input type="submit" class="btn btn-primary mb-3 col-2" id="print_multi_inv"
                        value="บันทึก">

                    <table class="table mb-0 mt-3  table-striped dataTable text-nowrap">
                        <thead class="bg-light">
                            <tr>
                                <th class="text-center">เลขใบแจ้งหนี้</th>
                                <th class="text-center">เลขมิเตอร์</th>
                                <th class="text-center">ชื่อ-สกุล</th>
                                <th class="text-center">บ้านเลขที่</th>
                                <th class="text-center">ยกยอดมา</th>
                                <th class="text-center">มิเตอร์ปัจจุบัน</th>
                                <th class="text-center">จำนวนสุทธิ</th>
                                <th class="text-center">เป็นเงิน</th>
                                <th class="text-center">หมายเหตุ</th>
                                <th class="text-center"></th>
                            </tr>
                        </thead>
                        <tbody id="app">
                            @foreach ($memberHasInvoice as $invoice)
                            {{-- {{dd($invoice)}} --}}
                                <tr data-id="{{$invoice->id}}" class="data">
                                   
                                    <td class="border-0">
                                        <input type="text" value="{{$invoice->id}}" name="zone[{{$invoice->id}}][iv_id]" data-id="{{$invoice->id}}" 
                                        id="iv_id{{$invoice->id}}" class="form-control text-right iv_id" readonly>
                                    </td>
                                    <td class="border-0">
                                        <input type="text" value="{{$invoice->meternumber}}" name="zone[{{$invoice->id}}][meternumber]" 
                                        data-id="{{$invoice->id}}" id="meternumber{{$invoice->meternumber}}" class="form-control text-right meternumber border-primary" readonly>
                                        <input type="hidden" value="{{$invoice->meternumber}}" name="zone[{{$invoice->id}}][meter_id]">
                                    </td>
                                    <td class="border-0 text-left">
                                        @if ($invoice->name != null)
                                        {{$invoice->name}}
                                        @endif
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" value="{{$invoice->address}}" name="zone[{{$invoice->id}}][address]">
                                    </td>
                                    <td class="border-0">
                                        <input type="text" value="{{$invoice->lastmeter}}" name="zone[{{$invoice->id}}][lastmeter]" data-id="{{$invoice->id}}" 
                                        id="lastmeter{{$invoice->id}}" class="form-control text-right lastmeter" readonly>
                                    </td>
                                    <td class="border-0 text-right">
                                        <input type="text" value="{{$invoice->currentmeter}}" name="zone[{{$invoice->id}}][currentmeter]" data-id="{{$invoice->id}}" id="currentmeter{{$invoice->id}}" class="form-control text-right currentmeter">
                                    </td>
                                    <td class="border-0 text-right">
                                        <input type="text" readonly class="form-control text-right water_used_net"  id="water_used_net{{$invoice->id}}" value="{{$invoice->meter_net}}">      
                                    </td>
                                    <td class="border-0 text-right">
                                        <input type="text" readonly class="form-control text-right total" id="total{{$invoice->id}}" value="{{$invoice->total}}">
                                    </td>
                                    
                                    <td class="border-0">
                                        <input type="text" name="zone[{{$invoice->id}}][comment]"  value="{{$invoice->comment}}"
                                            data-id="{{$invoice->id}}" id="comment{{$invoice->id}}" class="form-control  comment">
                                    </td>
                                    <td class="border-0">
                                        <a href="javascript:void(0)" class="btn btn-danger delBtn" data-del_invoice_id="{{$invoice->id}}">ลบ</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
            </form>
        </div>
    </div>
</div>

@endsection


@section('script')
<script>
    let i = 0;
    

    //คำนวนเงินค่าใช้น้ำ
    $('.currentmeter').keyup(function(){
        let inv_id = $(this).data('id')
        let currentmeter = $(this).val()
        let lastmeter = $(`#lastmeter${inv_id}`).val()
        let net = currentmeter - lastmeter
        let total = net * 8;
        $('#water_used_net'+inv_id).val(net)
        $('#total'+inv_id).val(total);
    });
    $('.lastmeter').keyup(function(){
        let inv_id = $(this).data('id')
        let lastmeter = $(this).val()
        
        let currentmeter = $(`#currentmeter${inv_id}`).val()
        console.log(currentmeter)
        let net = currentmeter - lastmeter
        let total = net * 8;
        $('#water_used_net'+inv_id).val(net)
        $('#total'+inv_id).val(total);
    });

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
        }
    })
    $(document).ready(function(){
        $('.paginate_page').text('หน้า')
        let val = $('.paginate_of').text()
        $('.paginate_of').text(val.replace('of', 'จาก')); 

        setTimeout(()=>{
            $('.alert').toggle('slow')
        },2000)
        
    })

    $('.delBtn').click(function(){
        let res = window.confirm('ต้องการลบข้อมูลใช่หรือไม่ !!!')
          if(res === true){
            let inv_id = $(this).data('del_invoice_id');
            let comment = $(`#comment${inv_id}`).val()
            window.location.href = `/invoice/delete/${inv_id}/${comment}`
          }else{
            return false;
          } 
      });

</script>
@endsection


{{-- //ค้นหาโดยเลขมิเตอร์
    // $('#meternumber').keyup(function(){
    //     $('.init').val('')
    //     let meternumber = $('#meternumber').val();

    //     if(meternumber.length > 4){
    //         let zone_id = "<hp echo $zoneInfo->zone_id; ?>";
    //         console.log(zone_id)
    //         $.get(`../../invoice/search_from_meternumber/${meternumber}/${zone_id}`).done(function(data){
    //             console.log('data',data)
    //             if(data.usermeterInfos  === null){
    //                 $('.addBtn').addClass('hidden');
    //                 $('.empty_user').text('ไม่พบผู้ใช้งานเลขมิเตอร์นี้')
    //             }else{
    //                 $('#currentmeter').focus();
    //                 let address = `${data.usermeterInfos.user.usermeter_info.zone.zone_name}  ${data.usermeterInfos.user.usermeter_info.zone.location}`;
    //                 $('#feFirstName').val(data.usermeterInfos.user.user_profile.name);
    //                 $('#feInputAddress').val(address);
    //                 $('.empty_user').text('')
    //                 //ถ้า invoice = 0 
    //                 if(data.invoice === null){
                
    //                     $('#lastmeter').val(0);
    //                     $('#last_invoice').val(-1);
    //                 }else{
    //                     $('#lastmeter').val(data.invoice.currentmeter);
    //                     $('#last_invoice').val(data.invoice.id);
    //                 }
                    
    //                 $('#user_id').val(data.usermeterInfos.user.id);

    //                 if($('.addBtn').hasClass('hidden')){
    //                     $('.addBtn').removeClass('hidden');
    //                 }
    //             }
    //         });
    //     }
    // }) --}}