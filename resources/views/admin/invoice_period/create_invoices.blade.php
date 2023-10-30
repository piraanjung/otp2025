@extends('layouts.adminlte')

@section('mainheader')
รอบบิล
@endsection
@section('presentheader')
รายการรอบบิล
@endsection

@section('content')
<div class="card card-primary card-outline">
  <div class="card-body box-profile">
    <div class="text-center">
    </div>

    <h3 class="profile-username text-center">สร้างใบแจ้งหนี้เริ่มต้น</h3>

    <p class="text-muted text-center"></p>

    <ul class="list-group list-group-unbordered mb-3">
      <li class="list-group-item">
        <b>ปีงบประมาณ</b> <a class="float-right">{{$invoice_period->budgetyear}}</a>
      </li>
      <li class="list-group-item">
        <b>Following</b> <a class="float-right">543</a>
      </li>
      <li class="list-group-item">
        <b>Friends</b> <a class="float-right">13,287</a>
      </li>
    </ul>

    <a href="#" class="btn btn-primary btn-block"><b>Follow</b></a>
  </div>
  <!-- /.card-body -->
</div>
@endsection