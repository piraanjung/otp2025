@extends('layouts.adminlte')
@section('mainheader')
 สร้างขนาดมิเตอร์
@endsection
@section('nav')
<a href="{{url('tabwatermeter')}}"> ขนาดมิเตอร์</a>
@endsection
@section('tabwatermeter')
    active
@endsection
@section('content')
<div class="card card-small mb-4">
    <div class="card-header border-bottom">
        {{-- <h6 class="m-0">Form Inputs</h6> --}}
    </div>
    <div class="card-body pt-0">
        <form action="{{url('tabwatermeter/store')}}" method="POST">
            @csrf
            @include('settings.tabwatermeter.form', ['mode'=> 'create'])
        </form>
    </div>
</div>

@endsection