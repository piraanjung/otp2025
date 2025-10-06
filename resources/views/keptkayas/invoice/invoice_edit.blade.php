@extends('layouts.adminlte')

@section('mainheader')
  ประวัติ
@endsection
@section('invoice')
    active
@endsection
@section('nav')
<a href="{{url('/invoice')}}"> งานประปา</a>
@endsection


@section('content')

  <div class="row">
    <table class="table">
      <thead>
        <tr>
          <th></th>
          <th></th>
          <th></th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        @foreach ($user[0]->invoice_by_user_id as $item)
        <tr>
          <td>{{$item->user_profile->name}}</td>
      
            <td>{{$item->inv_period_id}}</td>
          </tr>
      @endforeach
      </tbody>
    </table>
      
  </div>


@endsection


@section('script')
<script>
    let i = 0;
    //ค้นหาโดยเลขมิเตอร์
    $('#meternumber').keyup(function(){
        console.log('ss')
        let meternumber = $('#meternumber').val();
        console.log(meternumber)

        $.get("invoice/search_from_meternumber/"+meternumber).done(function(data){
            console.log('data',data)
            if(data.usermeterInfos  === null){
                $('.addBtn').addClass('hidden');
                $('.empty_user').text('ไม่พบผู้ใช้งานเลขมิเตอร์นี้')
            }else{

                let address = `${data.usermeterInfos.user.usermeter_info.zone.zone_name}  ${data.usermeterInfos.user.usermeter_info.zone.location}`;
                $('#feFirstName').val(data.usermeterInfos.user.user_profile.name);
                $('#feInputAddress').val(address);
                $('.empty_user').text('')
                 //ถ้า invoice = 0 
                 if(data.invoice === null){
            
                    $('#lastmeter').val(0);
                    $('#last_invoice').val(-1);
                 }else{
                    $('#lastmeter').val(data.invoice.currentmeter);
                    $('#last_invoice').val(data.invoice.id);
                 }
                
                $('#user_id').val(data.usermeterInfos.user.id);

                if($('.addBtn').hasClass('hidden')){
                    $('.addBtn').removeClass('hidden');
                }
            }
        });
    })

    //คำนวนเงินค่าใช้น้ำ
    $('#currentmeter').keyup(function(){
        let lastmeter = $('#lastmeter').val();
        let currentmeter = $(this).val();
        let total = (currentmeter - lastmeter) * 8;
        $('#cashtotal').val(total);
    });

    //เพิ่มข้อมูลลงตาราง lists
    $('.addBtn').click(function(){
        let newtr = `
        <tr>
            <td width="1%"></td>
            <td width="24%">
                <input type="text" value="${$('#feFirstName').val()}" class="form-control" name="" readonly>
                <input type="hidden" value="${$('#last_invoice').val()}" name="data[${i}][last_invoice]">
                <input type="hidden" value="${$('#user_id').val()}" name="data[${i}][user_id]">
            </td>
            <td width="40%"><input type="text" value="${$('#feInputAddress').val()}" class="form-control" name="" readonly></td>
            <td width="10%"><input type="text" value="${$('#lastmeter').val()}" class="form-control" name="data[${i}][lastmeter]" readonly></td>
            <td width="10%"><input type="text" value="${$('#currentmeter').val()}" class="form-control" name="data[${i}][currentmeter]"></td>
            <td width="10%"><input type="text" value="${$('#cashtotal').val()}" class="form-control" name="data[${i}][cashtotal]" readonly></td>
            <td width="5%"><button type="button" class="btn btn-danger aa">
                              <span><i class="fa fa-trash"></i></span> </button></td>
        </tr>
        `;
        $('#lists').append(newtr);
        i++;
    });

    $( "body" ).on( "click", ".aa", function() {
        $(this).parent().parent().remove();
    });

</script>
@endsection
