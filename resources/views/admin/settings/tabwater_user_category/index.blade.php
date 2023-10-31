@extends('layouts.admin')

@section('content')
<div class="page-header row no-gutters py-4">
        <div class="col-12 col-sm-4 text-center text-sm-left mb-0">
          <span class="text-uppercase page-subtitle">Settings</span>
          <h3 class="page-title">ประเภทผู้ใช้น้ำ</h3>
        </div>
      </div>
      <div>
        <div  class="alert alert-success alert-dismissible fade show_x mb-4 mt-4 hidden" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">×</span>
              </button>
              <i class="fa fa-check mx-2"></i>
              <strong id="resText"></strong>
          </div>
          <div class="row">
            
                <div class="col">
                  <div class="card card-small mb-4">
                    <div class="card-header border-bottom flex">
                      <!-- <h6 class="m-0">Active Users</h6> -->
                      <a :href="'/tabwater_user_category/create'" class="btn btn-primary fa-pull-right">เพิ่มข้อมูล</a>
                    </div>
                    <div class="card-body p-0 pb-3 text-center">
                      <table class="table mb-0">
                        <thead class="bg-light">
                          <tr>
                            <th scope="col" class="border-0">#</th>
                            <th scope="col" class="border-0">ประเภทผู้ใช้น้ำ</th>
                            <th scope="col" class="border-0">ราคาต่อหน่วย </th>
                            <th scope="col" class="border-0">จำนวนหน่วย</th>
                            <th scope="col" class="border-0">ชื่อหน่วย</th>
                            <th scope="col" class="border-0">วันที่บันทึก</th>
                            <th scope="col" class="border-0"></th>
                          </tr>
                        </thead>
                        <tbody>
                          @foreach ($tab_user_cate as $item)
                          <tr >
                            <td>{{$item->id}}</td>
                            <td class="text-left">{{$item->name}}</td>
                            <td>{{$item->price_per_unit}}</td>
                            <td>{{$item->unit_num}}</td>
                            <td>{{$item->unitname}}</td>
                            <td>{{$item->created_at}}</td>
                            <td>
                              <a href="{{url('tabwater_user_category/'.$item->id.'/edit')}}" class="btn btn-warning">แก้ไข</a>
                              <button onclick="deleteData('{{$item->id}}')"   class="btn btn-danger">ลบ</button>
                            </td>
                          </tr> 
                          @endforeach                          
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
      </div>
    
@endsection



@section('script')
    <script>
        function deleteData(del_id){
          var r = confirm("คุณต้องการล!");
          if (r == true) {
            $.get('/api/tabwater_user_category/'+del_id+'/delete',function() {
            
            $('#resText').html(res.data)
            if($('.show_x').hasClass('hidden')){
              $(this).removeClass('hidden');
              $(this).addClass('show');
            }
        });
          }
        }
    </script>
@endsection