@extends('layouts.adminlte')

@section('mainheader')
 งานประปา  {{isset($invoice_period->inv_period_name) ? 'รอบบิล '.$invoice_period->inv_period_name : ""}}
@endsection
@section('invoice')
    active
@endsection
@section('nav')
<a href="{{url('/invoice/index')}}">ออกใบแจ้งหนี้</a>
@endsection

@section('style')
<style>
  .row {
    display: -ms-flexbox;
    display: flex;
    -ms-flex-wrap: wrap;
    flex-wrap: wrap;
    margin-right: -1.2px;
    margin-left: -1.2px;
}
  .subzone_info{
    border-bottom: 1px solid rgba(0,0,0,.125);
    margin: 0;

  }
  .card-footer{
    background:  #ffffff
  }
  .disabled {
  pointer-events: none;
  cursor: default;
}
</style>
@endsection

@section('content')
@if ($message = Session::get('massage'))
<div class="alert alert-info alert-block">
	<button type="button" class="close" data-dismiss="alert">×</button>	
	<strong>{{ $message }}</strong>
</div>
@endif
{{-- <div>
  ปีงบประมาณ
  <input type="text" class="">
  รอบบิลที่
  <input type="text">
</div> --}}

@if (collect($invoice_period)->count() == 0)
{{-- ยังไม่มีการสร้างรอบบิล --}}
<div class="col-lg-6 col-6">
  <div class="small-box bg-warning">
    <div class="inner">
      <h3>ยังไม่ได้สร้างรอบบิลปัจจุบัน</h3>
      <p>&nbsp;</p>
    </div>
    <div class="icon">
      <i class="fas fa-exclamation-circle"></i>
    </div>
    <a href="{{url('invoice_period')}}" class="small-box-footer h5">สร้างรอบบิลปัจจุบัน <i class="fas fa-arrow-circle-right"></i></a>
  </div>
</div>

@else
{{-- ถ้าสร้างรอบบิลแล้ว --}}
<div class="row">
  {{-- ยังไม่มีการเพิ้มข้อมูลสมาชิกลง --}}
  @if (collect($zones)->isEmpty())
    <div class="col-lg-6 col-6">
      <!-- small box -->
      <div class="small-box bg-warning text-center pb-3">
        <div class="inner">
          <h4>ยังไม่มีข้อมูลสมาชิกผู้ใช้น้ำประปา</h4>
          <p>&nbsp;</p>
        </div>
        <div class="icon">
          <i class="fas fa-exclamation-circle"></i>
        </div>
        <a href="{{url('/users')}}" class="btn btn-primary">เพิ่มข้อมูลสมาชิกผู้ใช้น้ำประปา </a>
      </div>
    </div>
  
  @else
  {{-- มีการเพิ่มข้อมูลสมาชิกแล้ว --}}
      @foreach ($zones as $key =>$zone)
      <div class="col-md-12 col-lg-6">
        <div class="card card-widget widget-user">
          <div class="widget-user-header bg-info">
            <h3 class="widget-user-username">{{ $zone['zone_name'] }}</h3>
            <h5 class="widget-user-desc">เส้นทาง : {{ $zone['subzone_name'] }}</h5>
            <a href="#" class="nav-link  text-info">
                <span class="float-right">
                  <h5>
                    จำนวนสมาชิก {{$zone['total']}}<sup> คน</sup>
                  </h5>
                </span>
            </a>
          </div>
          <div class="widget-user-image">
            <img class="img-circle elevation-2" src="{{asset('adminlte/dist/img/users2.png')}}" alt="User Avatar">
            
          </div>
          <div class="card-footer p-0">
            <div class="row">
          
              <div class="col-md-12 subzone_info pb-2">
                <div class="row pl-3 pr-3 pt-3 {{$zone['new_user'] == 0 ?'pb-2' : 'pb-0'}}">
                  @if ($zone['new_user'] > 0)
                    <div class="col-md-6"><h6>เพิ่มผู้ใช้น้ำระหว่างรอบบิล</h6></div>  
                    <div class="col-md-3 text-right text-primary pr-5">
            
                      <h5>{{$zone['new_user']}} <sup> คน</sup></h5>
                    </div>
                    <div class="col-md-3">
                      <a href="{{url('invoice/test/'.$zone['subzone_id'].'/1')}}" class="btn btn-block btn-primary btn-sm " >เพิ่มข้อมูล </a>
                    </div>
                  @else
                    &nbsp;<sup>&nbsp;</sup>
                  @endif
                   
                </div>
              </div>
              <div class="col-md-12 subzone_info pb-2">
                <div class="row pl-3 pr-3 pt-3 pb-0">
                  <div class="col-md-6"><h5>ยังไม่บันทึกข้อมูลมิเตอร์</h5></div>  
                  <div class="col-md-3 text-right text-primary pr-5">
                    <?php 
                      $not_recorded = $zone['init']; 
                    ?>
                    <h5>{{$not_recorded}} <sup> คน</sup></h5>
                  </div>
                  <div class="col-md-3">
                    <a href="{{url('invoice/zone_create/'.$zone['subzone_id'])}}" class="btn btn-block btn-primary btn-sm  {{ $not_recorded == 0 ? 'disabled' : '' }}" >เพิ่มข้อมูล </a>
                  </div>
                </div>
              </div>
                <div class="col-md-12 subzone_info pb-2">
                  <div class="row pl-3 pr-3 pt-3 pb-0">
                  <div class="col-md-6"><h5>ค้างชำระเกิน 3 รอบบิล</h5></div>  
                  <div class="col-md-3 text-right text-primary pr-5">
                    <h5>{{$zone['cutmeter_count']}} <sup> คน</sup></h5>
                  </div>
                  <div class="col-md-3">
                     <a href="{{url('cutmeter/index/'.$zone['subzone_id'])}}" class="btn btn-block btn-danger btn-sm {{ $zone['cutmeter_count'] == 0 ? 'disabled' : '' }}">ดูข้อมูล </a>
                  </div>
                </div>
              </div>
              
              <div class="col-md-12 subzone_info pb-2">
                <div class="row pl-3 pr-3 pt-3 pb-0">
                  <div class="col-md-6"><h5>บันทึกข้อมูลแล้ว</h5></div>  
                  <div class="col-md-3 text-right text-primary pr-5">
                    <h5>{{$zone['invoice']}} <sup> คน</sup></h5>
                  </div>
                  <div class="col-md-3">
                    {{-- @if ($zone['invoice'] > 0) --}}
                      <a href="{{url('invoice/invoiced_lists/'.$zone['subzone_id'])}}" class="btn btn-info btn-block btn-sm {{ $zone['invoice'] == 0 ? 'disabled' : '' }}">
                        ปริ้นใบแจ้งหนี้
                      </a>
                      <a href="{{url('invoice/zone_edit/'.$zone['subzone_id'])}}" class="btn btn-warning btn-block  btn-sm {{ $zone['invoice'] == 0 ? 'disabled' : '' }}">
                        แก้ไขข้อมูล
                      </a>
                    {{-- @endif --}}
                   
                  </div>
                </div>
              </div>
              <div class="col-md-12 subzone_info pb-2">
                <div class="row pl-3 pr-3 pt-3 pb-0">
                  <div class="col-md-6"><h5>ชำระเงินแล้ว</h5></div>  
                  <div class="col-md-3 text-right text-primary pr-5">
                    <h5>{{$zone['paid']}} <sup> คน</sup></h5>
                  </div>
                  <div class="col-md-3">
                    <a href="{{url('payment/paymenthistory/'.$invoice_period->id.'/'.$zone['subzone_id'])}}" class="btn btn-info btn-block  btn-sm {{ $zone['paid'] == 0 ? 'disabled' : '' }} ">
                      ดูข้อมูล
                    </a>
                  
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      @endforeach
  @endif
</div>

@endif

@endsection


@section('script')
<script>
    let i = 0;
    //ค้นหาโดยเลขมิเตอร์
    $('#meternumber').keyup(function(){
        let meternumber = $('#meternumber').val();

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
    $(document).ready(()=>{
      setTimeout(()=>{
        $('.alert').toggle('slow')
      },2000) 
    })

</script>
@endsection
