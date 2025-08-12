@extends('layouts.adminlte')
@section('mainheader')
แก้ไขข้อมูลการใช้น้ำประปา
@endsection
@section('presentheader')
  แก้ไขข้อมูลการใช้น้ำประปา
@endsection

@section('content')
  @include('invoice/form',['mode'=> 'edit'])
@endsection


