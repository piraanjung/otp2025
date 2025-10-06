@extends('layouts.super-admin')
@section('nav-main')
    อัตราการชำระค่าน้ำ
@endsection
@section('nav-main-url')
    {{route('meter_types.index')}}
@endsection
@section('nav-current')
    สร้าง อัตราการชำระค่าน้ำ
@endsection
@section('nav-current-title')
    สร้าง อัตราการชำระค่าน้ำ
@endsection
@section('content')
   
    <div class="card card-body p-2">
        <div class="row">
            <div class="col-12 col-lg-8 m-auto">
                <form action="{{ route('admin.meter_rates.store') }}" method="POST">
                    @csrf
                    
        @include('superadmin.meter_rates.form')
                    <button type="submit" class="btn btn-success">บันทึกอัตราการชำระค่าน้ำ</button>
                </form>

            </div>
        </div>
    </div>



@endsection