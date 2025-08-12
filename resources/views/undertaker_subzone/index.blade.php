@extends('layouts.admin1')
<?php
use App\Http\Controllers\Api\FunctionsController;
$apiFunc = new FunctionsController;
?>
@section('nav-header')
กำหนดผู้รับผิดชอบพื้นที่จดมิเตอร์
@endsection
@section('header_nav_undertaker-subzone')
active
@endsection
@section('nav_undertaker-subzone')
    active
@endsection
@section('nav-current')
<a href="{{url('/undertaker_subzone')}}" style="font-size:0.88rem"> กำหนดผู้รับผิดชอบพื้นที่จดมิเตอร์</a>
@endsection
@section('style')
  <style>
     .child{
      opacity: 0
    }
  </style>

@endsection
@section('content')


  @if ($message = Session::get('success'))
    <div class="alert alert-success alert-block">
      <button type="button" class="close" data-dismiss="alert">×</button>
      <strong>{{ $message }}</strong>
    </div>
  @endif


  <div class="card card-outline card-info">
    <div class="card-header">
      <div class="card-tools">
        <a href="{{url('undertaker_subzone/create')}}" class="btn btn-primary">เพิ่มเจ้าหน้าที่รับผิดชอบเส้นทาง</a>
      </div>
    </div>
    <div class="card-body">
      <table class="table align-items-center table-flush" id="example">
          <thead class="thead-light">
              <tr>
                  <th scope="col">รหัส</th>
                  <th scope="col">ชื่อเจ้าหน้าที่</th>
                  <th scope="col">เบอร์โทรศัพท์</th>
                  <th scope="col">หมู่ที่รับผิดชอบ</th>
                  <th scope="col">เส้นทางที่รับผิดชอบ</th>
                  <th scope="col"></th>
              </tr>
          </thead>
          <tbody>
              @foreach ($undertakerSubzones as $collection)
              {{-- {{dd($collection)}} --}}
                <?php $i = 1 ?>
                @foreach ($collection as $item)

                  @if ($i == 1)
                    <tr>
                      <th class="main">{{$apiFunc->createInvoiceNumberString($item->twman_id)}}</th>
                      <th class="main">
                        {{$item->twman_info->prefix.$item->twman_info->firstname." ".$item->twman_info->lastname}}
                      </th>
                      <th class="main">
                        {{$item->twman_info->phone}}
                      </th>
                      <th class="main">{{$item->subzone->zone->zone_name}}</th>
                      <th class="">{{$item->subzone->subzone_name}}</th>
                      <th class="">
                        <a href="{{url('/admin/undertaker_subzone/edit/'.$item->twman_id)}}" class="btn btn-warning">แก้ไข</a>
                        <a href="{{url('undertaker_subzone/delete/'.$item->twman_id)}}" class="btn btn-danger delbtn">ลบ</a>
                      </th>
                    </tr>
                  @else
                    <tr>
                      <th class="child">{{$apiFunc->createInvoiceNumberString($item->twman_id)}}</th>
                      <th class="child">
                        {{-- {{$item->user_profile->name}} --}}
                      </th>
                      <th class="child">
                        {{-- {{$item->user_profile->phone}} --}}

                      </th>
                      <th class="">
                        @if (!isset($item->subzone->zone->zone_name))
                        ไม่พบข้อมูล2
                        @else
                        {{$item->subzone->zone->zone_name}}
                        @endif
                    </th>
                      <th class="">
                        @if (!isset($item->subzone->subzone_name))
                        ไม่พบข้อมูล
                        @else
                        {{$item->subzone->subzone_name}}
                        @endif

                        </th>
                      <th class="child"></th>
                    </tr>
                  @endif
                  <?php $i++ ?>
                @endforeach
              @endforeach

          </tbody>
      </table>
    </div><!-- card-body -->
  </div><!--card -->
  @if (count($undertakerSubzones) > 0)
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">ยืนยันการลบข้อมูล</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            ต้องการลบข้อมูลหรือไม่ ?
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">ยกเลิก</button>
            <a href="/tabwaterman_per_areas/delete/{{$item['twm_id']}}" class="btn btn-danger"> ต้องการลบข้อมูล</a>
          </div>
        </div>
      </div>
    </div>

  @endif
@endsection

@section('script')
    <script>
      // $(document).ready(function(){
        $('#example').DataTable( {
            "pagingType": "listbox",
            "lengthMenu": [[10, 25, 50, 150, -1], [10, 25, 50, 150, "All"]],
            "language": {
                "search": "ค้นหา:",
                "lengthMenu": "แสดง _MENU_ แถว",
                "info":       "แสดง _START_ ถึง _END_ จาก _TOTAL_ แถว",
                "infoEmpty":  "แสดง 0 ถึง 0 จาก 0 แถว",
                "paginate": {
                    "info": "แสดง _MENU_ แถว",
                },
            },


          } );

          $('.paginate_page').text('หน้า')
          let val = $('.paginate_of').text()
          $('.paginate_of').text(val.replace('of', 'จาก'));
      // })

      $('.delbtn').click(function(){
        let res = window.confirm('ต้องการลบข้อมูลใช่หรือไม่ !!!')
          return  res === true ? true : false;
      });

      setTimeout(()=>{
        $('.alert').toggle('slow')
      },2000)
    </script>
@endsection

