@extends('layouts.admin')

@section('title')
    ตั้งค่า / พื้นที่จดมิเตอร์น้ำ / แก้ไขข้อมูลพื้นที่จดมิเตอร์น้ำ
@endsection

@section('content')
    <div class="row">
        <div class="col">
          <div class="card shadow">
            <div class="card-header border-0">
            </div>
            <div class="card-body">
                <form action="/zone/edit/{{$zone->id}}" method="post">
                    @csrf
                    @method('PUT')
                    @include('zone.form', ['formMode' => 'update'])
                </form>
            </div>
          </div>
        </div>
    </div>
    
@endsection