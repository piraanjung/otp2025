@extends('layouts.keptkaya')
@section('mainheader')
    สร้างประเภทผู้ใช้งาน
@endsection
@section('nav-header')
    <a href="{{ url('tabwatermeter') }}"> ประเภทผู้ใช้งาน</a>
@endsection
@section('tabwatermeter')
    active
@endsection
@section('content')

<div class="card card-outline card-danger">
    <div class="card-header">
      <h3 class="card-title">ประเภทผู้ใช้งาน</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('keptkayas.kp_usergroup.store') }}" method="POST">
            @csrf
            <div class="col-sm-12 col-md-10">
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <strong class="text-muted d-block mb-2">ชื่อประเภทผู้ใช้งาน
                            @error('usergroup_name')
                                <span class="text-danger h-8">({{ $message }})</span>
                            @enderror
                        </strong>
                        <input type="text" class="form-control" id="usergroup_name"
                            placeholder="ตัวอย่าง :ครัวเรือน" name="usergroup_name">
                    </div>

                   
                    <div class="form-group col-md-2">
                        <strong class="text-muted d-block mb-2">&nbsp;</strong>
                        <input type="hidden" name="budgetyear_id" value="{{$budgetyear[0]->id}}">
                        <button type="submit" class="btn btn-primary">บันทึก</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <!-- /.card-body -->
  </div>


@endsection
