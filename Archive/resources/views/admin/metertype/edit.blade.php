@extends('layouts.admin1')
@section('mainheader')
    แก้ไขขนาดมิเตอร์
@endsection
@section('nav')
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
                    <form action="{{ route('admin.metertype.update', $metertype->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="col-sm-12 col-md-10">
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <strong class="text-muted d-block mb-2">ชื่อประเภทมิเตอร์
                                        @error('name')
                                            <span class="text-danger h-8">({{ $meter_type_name }})</span>
                                        @enderror
                                    </strong>
                                    <input type="text" class="form-control" id="meter_type_name"
                                        value="{{ $metertype->meter_type_name }}" name="meter_type_name">
                                </div>
                                <div class="form-group col-md-3">
                                    <strong class="text-muted d-block mb-2">ขนาดมิเตอร์(หน่วย:นิ้ว)
                                        @error('name')
                                            <span class="text-danger h-8">({{ $metersize }})</span>
                                        @enderror
                                    </strong>
                                    <input type="text" class="form-control" id="metersize"
                                        value="{{ $metertype->metersize }}" name="metersize">
                                </div>
                                <div class="form-group col-md-3">
                                    <strong class="text-muted d-block mb-2">ราคาต่อหน่วย
                                        @error('name')
                                            <span class="text-danger h-8">({{ $price_per_unit }})</span>
                                        @enderror
                                    </strong>
                                    <input type="text" class="form-control" id="price_per_unit"
                                        value="{{ $metertype->price_per_unit }}" name="price_per_unit">
                                </div>
                                <div class="form-group col-md-2">
                                    <strong class="text-muted d-block mb-2">&nbsp;</strong>
                                    <button type="submit" class="btn btn-warning">Update</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
