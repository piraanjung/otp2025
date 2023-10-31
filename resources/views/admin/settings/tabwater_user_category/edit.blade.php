@extends('layouts.admin')

@section('content')
<div class="page-header row no-gutters py-4">
    <div class="col-12 col-sm-5 text-center text-sm-left mb-0">
        <span class="text-uppercase page-subtitle">Settings</span>
        <h3 class="page-title"><span style="font-size:1rem">ประเภทผู้ใช้น้ำ -</span> แก้ไขข้อมูลประเภทผู้ใช้น้ำ</h3>
    </div>
</div>
<div class="card card-small mb-4">
    <div class="card-header border-bottom">
        <h6 class="m-0">Form Inputs</h6>
    </div>
    <div class="card-body pt-0">
        <div id="app">
            <tabwater-user-category-edit id="{{$id}}"></tabwater-user-category-edit>
        </div>
    </div>
</div>

@endsection