@extends('layouts.adminlte')
@section('mainheader')
    แก้ไขขนาดมิเตอร์
@endsection
@section('nav-header')
    <a href="{{ url('tabwatermeter') }}"> ขนาดมิเตอร์</a>
@endsection
@section('tabwatermeter')
    active
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-small mb-4">
                <div class="card-body">
                    <form action="{{ route('admin.kp_usergroup.update', $kp_usergroup) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="col-sm-12 col-md-10">
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <strong class="text-muted d-block mb-2">ชื่อประเภทผู้ใช้งาน
                                        @error('usergroup_name')
                                            <span class="text-danger h-8">({{ $message }})</span>
                                        @enderror
                                    </strong>
                                    <input type="text" class="form-control" id="usergroup_name"
                                        value="{{ $kp_usergroup->usergroup_name }}" name="usergroup_name">
                                </div>

                              
                                <div class="form-group col-md-2">
                                    <strong class="text-muted d-block mb-2">&nbsp;</strong>
                                    <input type="hidden" name="id" value="{{$kp_usergroup->id}}">
                                    <button type="submit" class="btn btn-warning">บันทึกการแก้ไข</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
