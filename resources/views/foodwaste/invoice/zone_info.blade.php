@extends('layouts.adminlte')

@section('mainheader')
{{-- {{dd($zoneInfo)}} --}}
ดูข้อมูลสมาชิก {{$zoneInfo->zone->zone_name}}  เส้นทาง  {{$zoneInfo->subzone->subzone_name}}
<input type="hidden" id="zone_id" name="zone_id" value="{{$zoneInfo->zone->id}}">
<input type="hidden" id="subzone_id" name="subzone_id" value="{{$zoneInfo->subzone->id}}">
@endsection
@section('invoice')
    active
@endsection
@section('nav')
<a href="{{url('/invoice')}}"> ออกใบแจ้งหนี้</a>
@endsection


@section('content')
<div class="card">

      <div class="card-body">
          <div class="tab-content">
                <table id="example" class="display" width="100%">
                    <div class="overlay"><i class="fas fa-2x fa-sync-alt fa-spin"></i></div>
                </table>
            </div>
      </div>
</div>


@endsection

@section('script')


    <script>
        $(document).ready(function(){
            $.get(`../../api/users/by_zone/${$('#subzone_id').val()}`).done(function(data){
              $('#example').DataTable( {
                "pagingType": "listbox",
                "lengthMenu": [[10, 25, 50, 150, -1], [10, 25, 50, 150, "All"]],
                  data: data,
                  columns: [
                {title: 'เลขผู้ใช้น้ำ', data: 'user_id_str', 'width':'15%'},
                { title: "เลขมิเตอร์" , data: 'meternumber', 'width':'10%'},
                { title: "ชื่อ-สกุล" , data: 'name', 'width':'27%'},
                { title: "ที่อยู่" , data: 'address', 'width':'13%'},
                { title: "หมู่" , data: 'zone_name', 'width':'10%'},
                { title: "เส้นทาง" , data: 'subzone_name', 'width':'10%'},
                { title: "" , data: 'showLink', 'width':'3%'},
                { title: "" , data: 'editLink', 'width':'3%'},
                // { title: "" , data: 'deleteLink', 'width':'3%'},
                  
              ]
              } );

            $('.overlay').html('')
        });

            $('.datatable').DataTable( {
                "pagingType": "listbox",
                "lengthMenu": [[10, 25, 50, 150, -1], [10, 25, 50, 150, "ทั้งหมด"]]
            });
          })//documnet

</script>
@endsection

