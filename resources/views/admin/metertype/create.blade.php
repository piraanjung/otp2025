@extends('layouts.admin1')
@section('mainheader')
    สร้างขนาดมิเตอร์
@endsection
@section('nav')
    <a href="{{ url('tabwatermeter') }}"> ขนาดมิเตอร์</a>
@endsection
@section('tabwatermeter')
    active
@endsection
@section('content')
    <div class="card card-small mb-4">
        <div class="card-header border-bottom">
        </div>
        <div class="card-body">
            <form action="{{ route('admin.metertype.store') }}" method="POST">
                @csrf
                <div class="col-sm-12 col-md-10">
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <strong class="text-muted d-block mb-2">ชื่อประเภทมิเตอร์
                                @error('meter_type_name')
                                    <span class="text-danger h-8">({{ $message }})</span>
                                @enderror
                            </strong>
                            <input type="text" class="form-control" id="meter_type_name"
                                placeholder="ตัวอย่าง : ประปาหมู่บ้าน" name="meter_type_name">
                        </div>
                        <div class="form-group col-md-3">
                            <strong class="text-muted d-block mb-2">ขนาดมิเตอร์(หน่วย:นิ้ว)
                                @error('metersize')
                                    <span class="text-danger h-8">({{ $message }})</span>
                                @enderror
                            </strong>
                            <input type="text" class="form-control" id="metersize" name="metersize">
                        </div>
                        <div class="form-group col-md-3">
                            <strong class="text-muted d-block mb-2">ราคาต่อหน่วย
                                @error('price_per_unit')
                                    <span class="text-danger h-8">({{ $message }})</span>
                                @enderror
                            </strong>
                            <input type="text" class="form-control" id="price_per_unit" placeholder="ตัวอย่าง : 8.25"
                                name="price_per_unit">
                        </div>
                        <div class="form-group col-md-2">
                            <strong class="text-muted d-block mb-2">&nbsp;</strong>
                            <button type="submit" class="btn btn-primary">บันทึก</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
