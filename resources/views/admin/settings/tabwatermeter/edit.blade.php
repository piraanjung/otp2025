@extends('layouts.adminlte')
@section('mainheader')
แก้ไขขนาดมิเตอร์
@endsection
@section('nav')
<a href="{{url('tabwatermeter')}}"> ขนาดมิเตอร์</a>
@endsection
@section('tabwatermeter')
    active
@endsection
@section('content')

<div class="card card-small mb-4">
    <form action="{{url('tabwatermeter/'.$tabwatermeter->id.'/update')}}" method="POST">
        @csrf
        @method("PUT")
        @include('settings.tabwatermeter.form', ['mode'=> 'edit'])
    </form>
</div>

@endsection