@extends('layouts.adminlte')

@section('mainheader')
แก้ไขรอบบิล
@endsection
@section('invoice_period')
    active
@endsection
@section('nav')
<a href="{{url('/invoice_period')}}"> สร้างรอบบิล</a>
@endsection

@section('content')

<main class="main-content col">
    <div class="main-content-container container-fluid px-4 my-auto h-100">
        <div class="row no-gutters h-100">
            <div class="col-lg-3 col-md-5 auth-form mx-auto my-auto">
                <div class="card">
                    <div class="card-body">
                      
                              <h2 class="text-center">แก้ไขรอบบิล</h2>
                              <form action="{{url('invoice_period/update/'.$invoice_period->id)}}" method="post">
                              @csrf
                              @method('PUT')
                                    <div class="form-group">
                                          <label>ปีงบประมาณ</label>
                                          <input class="form-control text-center" type="text" name="budgetyear_name" value="{{$invoice_period['budgetyear']->budgetyear}}" placeholder="" readonly>
                                          <input class="form-control text-center" type="text" name="budgetyear_id" value="{{$invoice_period->budgetyear_id}}" hidden>
                                    </div>
                                    <div class="form-group">
                                          <label>รอบบิลประจำเดือน</label>
                                          <input class="form-control text-center" type="text" name="inv_period_name" value="{{$invoice_period->inv_period_name}}" placeholder="01-63">
                                    </div>
                                    <div class="form-group">
                                          <label>วันที่เริ่มรอบ</label>
                                          <input class="form-control text-center datepicker" type="text" name="startdate" value="{{$invoice_period->startdate}}" placeholder="Select date">
                                    </div>
                                    <div class="form-group">
                                          <label>วันสิ้นสุดรอบ</label>
                                          <input class="form-control text-center datepicker" type="text" name="enddate" value="{{$invoice_period->enddate}}" placeholder="Select date">
                                    </div>
                                    <div class="form-group">
                                        <label>สถานะ</label>
                                        <select name="status" id="status" class="form-control">
                                            <option value="active" {{$invoice_period->status == 'active' ? 'selected' : ''}}>รอบบิลปัจจุบัน</option>
                                            <option value="inactive" {{$invoice_period->status == 'inactive' ? 'selected' : ''}}>สิ้นสุดรอบบิล</option>
                                        </select>
                                  </div>

                                    <button type="submit" class="btn btn-success">ยืนยัน</button>
                              </form>
                    </div>

                </div>
            </div>
        </div>
</main>
@endsection

@section('script')
    <script>
            $(document).ready(function(){
                $('.datepicker').datepicker({
                    format: 'dd/mm/yyyy',
                    todayBtn: true,
                    language: 'th',             //เปลี่ยน label ต่างของ ปฏิทิน ให้เป็น ภาษาไทย   (ต้องใช้ไฟล์ bootstrap-datepicker.th.min.js นี้ด้วย)
                    // thaiyear: true              //Set เป็นปี พ.ศ.
                }).datepicker();  //กำหนดเป็นวันปัจุบัน
            })    
    </script>
@endsection