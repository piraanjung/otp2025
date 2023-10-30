@extends('layouts.adminlte')

@section('mainheader')
    จ่ายค่าน้ำประปา
@endsection
@section('presentheader')
    เพิ่มข้อมูลการใช้น้ำประปา
@endsection

@section('content')
<?php $path = '';?>
  <div id="app">
    <invoice-paid :id="{!!$invoice_id!!}" :mode="'create'" :path="'{{$path}}'"></invoice-paid>
  </div>
@endsection