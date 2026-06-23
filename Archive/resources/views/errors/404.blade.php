@extends('layouts.super-admin')

@section('content')
<div class="container-fluid py-9">
    <div class="row">
        <div class="col-lg-6 mx-auto text-center">
            <h1 class="display-1 text-gradient text-primary">404</h1>
            <h2 class="fw-bold">ขออภัย! ไม่พบหน้าที่คุณต้องการ</h2>
            <p class="lead">ดูเหมือนว่าหน้าที่คุณกำลังเรียกหาจะไม่มีอยู่จริง หรือถูกย้ายไปแล้ว</p>
            <a href="{{ url('/') }}" class="btn bg-gradient-primary mt-4">กลับสู่หน้าหลัก</a>
        </div>
    </div>
</div>
@endsection
